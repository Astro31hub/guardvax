<?php
// ============================================================
// auth/login.php — Login + 2FA (Email Code Verification)
// Admin bypasses 2FA and logs in directly
// ============================================================

require_once __DIR__ . '/../includes/auth.php';

startSecureSession();

// Already logged in? redirect.
if (isLoggedIn()) {
    $user = getCurrentUser();
    redirect(dashboardPath($user['role_name']));
}

$errors  = [];
$step    = $_SESSION['login_step'] ?? 1;
$message = '';

// Flash from redirect
$msg = $_GET['msg'] ?? '';
if ($msg === 'logged_out')  $message = 'You have been logged out.';
if ($msg === 'session_exp') $message = 'Your session has expired. Please login again.';

// ── Step 1: Validate credentials ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    verifyCsrf();
    $email    = sanitizeEmail($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!validateEmail($email)) $errors[] = 'Please enter a valid email address.';
    if (empty($password))       $errors[] = 'Password is required.';

    if (empty($errors)) {
        $user = authenticateUser($email, $password);

        if (!$user) {
            $errors[] = 'Invalid email or password.';
            auditLog('LOGIN_FAIL', null, null, "Failed attempt for: {$email}");

        } elseif ($user['status'] === 'pending') {
            $errors[] = 'Account not verified. Please check your email for the verification code.';

        } else {

            // ── ADMIN — skip 2FA, login directly ────────────
            if ($user['role_id'] == 1) {
    loginUser($user);
    auditLog('LOGIN_SUCCESS', 'users', $user['id'], 'Admin login — 2FA bypassed');
    redirect(dashboardPath('admin'));
}

            // ── NURSE & PATIENT — require 2FA ────────────────
            $code = generateVerificationCode($user['id'], '2fa_login');
            sendVerificationEmail($user['email'], $user['name'], $code, '2fa_login');

            $_SESSION['pending_user_id']    = $user['id'];
            $_SESSION['pending_user_email'] = $user['email'];
            $_SESSION['pending_user_name']  = $user['name'];
            $_SESSION['login_step']         = 2;
            $step = 2;
        }
    }
}

// ── Step 2: Validate OTP ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify_otp') {
    verifyCsrf();
    $otp    = trim($_POST['otp'] ?? '');
    $userId = (int)($_SESSION['pending_user_id'] ?? 0);

    if (!$userId) { $errors[] = 'Session expired. Please login again.'; $step = 1; }
    if (empty($otp)) $errors[] = 'Please enter the verification code.';

    if (empty($errors)) {
        if (verifyCode($userId, $otp, '2fa_login')) {
            $stmt = db()->prepare(
                'SELECT u.*, r.name AS role_name
                 FROM users u JOIN roles r ON r.id = u.role_id
                 WHERE u.id = ?'
            );
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            loginUser($user);
            unset(
                $_SESSION['pending_user_id'],
                $_SESSION['pending_user_email'],
                $_SESSION['pending_user_name'],
                $_SESSION['login_step']
            );

            auditLog('LOGIN_SUCCESS', 'users', $user['id'], "Logged in as {$user['role_name']}");
            redirect(dashboardPath($user['role_name']));
        } else {
            $errors[] = 'Invalid or expired code. Please try again.';
        }
    }
}

// ── Resend OTP ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'resend_otp') {
    $userId = (int)($_SESSION['pending_user_id'] ?? 0);
    if ($userId) {
        $stmt = db()->prepare('SELECT name, email FROM users WHERE id=?');
        $stmt->execute([$userId]);
        $u = $stmt->fetch();
        $code = generateVerificationCode($userId, '2fa_login');
        sendVerificationEmail($u['email'], $u['name'], $code, '2fa_login');
        $message = 'A new code has been sent to your email.';
    }
    $step = 2;
}

// Cancel step 2
if (isset($_GET['cancel'])) {
    unset(
        $_SESSION['pending_user_id'],
        $_SESSION['pending_user_email'],
        $_SESSION['pending_user_name'],
        $_SESSION['login_step']
    );
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

$maskedEmail = '';
if ($step === 2 && !empty($_SESSION['pending_user_email'])) {
    $email  = $_SESSION['pending_user_email'];
    $parts  = explode('@', $email);
    $masked = substr($parts[0], 0, 2) . str_repeat('*', max(1, strlen($parts[0]) - 2));
    $maskedEmail = $masked . '@' . $parts[1];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — HCARE</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-split">

  <!-- Left Panel -->
  <div class="auth-panel-left d-none d-lg-flex">
    <div class="auth-hero">
      <div class="auth-hero-icon">🛡️</div>
      <h1>H<strong>CARE</strong></h1>
      <p class="auth-tagline">Hospital Vaccination &amp;<br>Health Data Management</p>
      <ul class="auth-features">
        <li><i class="bi bi-check-circle-fill"></i> Secure vaccination records</li>
        <li><i class="bi bi-check-circle-fill"></i> Role-based access control</li>
        <li><i class="bi bi-check-circle-fill"></i> PDF report generation</li>
        <li><i class="bi bi-check-circle-fill"></i> Real-time audit logging</li>
      </ul>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="auth-panel-right">
    <div class="auth-card">
      <div class="auth-logo d-lg-none">🛡️ <strong>HCARE</strong></div>

      <?php if ($step === 1): ?>
      <!-- ── Step 1: Credentials ── -->
      <h2 class="auth-title">Welcome back</h2>
      <p class="auth-subtitle">Sign in to your account</p>

      <?php if ($message): ?>
        <div class="alert alert-info py-2">
          <i class="bi bi-info-circle me-1"></i><?= sanitize($message) ?>
        </div>
      <?php endif; ?>

      <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger py-2">
          <i class="bi bi-exclamation-triangle me-1"></i><?= sanitize($e) ?>
        </div>
      <?php endforeach; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control"
                   placeholder="you@example.com"
                   value="<?= sanitize($_POST['email'] ?? '') ?>"
                   required autofocus>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label d-flex justify-content-between">
            Password
            <a href="<?= SITE_URL ?>/auth/forgot_password.php"
               class="small text-primary">Forgot password?</a>
          </label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="passwordField"
                   class="form-control" placeholder="••••••••" required>
            <button type="button" class="btn btn-outline-secondary"
                    onclick="togglePassword()">
              <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-auth">
          <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
        </button>
      </form>

      <p class="text-center mt-3 small text-muted">
        Nurse registering?
        <a href="<?= SITE_URL ?>/auth/signup.php"
           class="text-primary fw-semibold">Create account here</a>
      </p>

      <!-- Patient info -->
      <div class="alert alert-light border mt-2 small text-muted py-2 px-3">
        <i class="bi bi-person-heart me-1 text-primary"></i>
        <strong class="text-dark">Patient?</strong>
        Your account is created by the nurse. Check your email for
        login credentials, or
        <a href="<?= SITE_URL ?>/contact.php" class="text-primary">contact us</a>.
      </div>

      <?php else: ?>
      <!-- ── Step 2: OTP ── -->
      <div class="text-center mb-3">
        <div class="otp-icon">📧</div>
      </div>
      <h2 class="auth-title">Check your email</h2>
      <p class="auth-subtitle">
        We sent a 6-digit code to<br>
        <strong class="text-primary"><?= sanitize($maskedEmail) ?></strong>
      </p>

      <?php if ($message): ?>
        <div class="alert alert-success py-2"><?= sanitize($message) ?></div>
      <?php endif; ?>

      <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger py-2">
          <i class="bi bi-exclamation-triangle me-1"></i><?= sanitize($e) ?>
        </div>
      <?php endforeach; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="action" value="verify_otp">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <div class="mb-3">
          <label class="form-label">Verification Code</label>
          <input type="text" name="otp"
                 class="form-control form-control-lg text-center otp-input"
                 maxlength="6" placeholder="_ _ _ _ _ _"
                 autocomplete="one-time-code"
                 inputmode="numeric" pattern="[0-9]{6}"
                 autofocus required>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-auth">
          <i class="bi bi-shield-check me-1"></i> Verify &amp; Login
        </button>
      </form>

      <div class="d-flex justify-content-between mt-3 small">
        <form method="POST" class="d-inline">
          <input type="hidden" name="action" value="resend_otp">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <button type="submit" class="btn btn-link btn-sm p-0 text-muted">
            Resend code
          </button>
        </form>
        <a href="?cancel=1" class="text-muted">← Use different account</a>
      </div>

      <?php endif; ?>

      <div class="auth-footer">
        <span>© <?= date('Y') ?> HCARE Hospital System</span>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const f = document.getElementById('passwordField');
    const i = document.getElementById('eyeIcon');
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

const otpInput = document.querySelector('.otp-input');
if (otpInput) {
    otpInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
}
</script>
</body>
</html>