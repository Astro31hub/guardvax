<?php
// auth/signup.php
require_once __DIR__ . '/../includes/auth.php';

startSecureSession();
if (isLoggedIn()) redirect(dashboardPath(getCurrentUser()['role_name']));

$errors  = [];
$success = false;
$step    = $_SESSION['signup_step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'signup') {
    verifyCsrf();
    $name     = sanitize($_POST['name'] ?? '');
    $email    = sanitizeEmail($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if (strlen($name) < 3)       $errors[] = 'Full name must be at least 3 characters.';
    if (!validateEmail($email))   $errors[] = 'Please enter a valid email address.';
    if (emailExists($email))      $errors[] = 'This email is already registered.';
    if (strlen($password) < 8)    $errors[] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Password needs at least one uppercase letter.';
    if (!preg_match('/[0-9]/', $password)) $errors[] = 'Password needs at least one number.';
    if ($password !== $confirm)   $errors[] = 'Passwords do not match.';
    if ($role !== 'nurse')        $errors[] = 'Only Nurse accounts can self-register.';

    if (empty($errors)) {
        try {
            $userId = registerUser($name, $email, $password, $role);
            $code   = generateVerificationCode($userId, 'email_verify');
            sendVerificationEmail($email, $name, $code, 'email_verify');
            $_SESSION['signup_user_id']    = $userId;
            $_SESSION['signup_user_email'] = $email;
            $_SESSION['signup_user_name']  = $name;
            $_SESSION['signup_step']       = 2;
            $step = 2;
            auditLog('REGISTER', 'users', $userId, "New nurse registration: {$email}");
        } catch (PDOException $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify_email') {
    verifyCsrf();
    $otp    = trim($_POST['otp'] ?? '');
    $userId = (int)($_SESSION['signup_user_id'] ?? 0);
    if (!$userId) { $errors[] = 'Session expired. Please register again.'; $step = 1; }
    if (empty($otp)) $errors[] = 'Please enter the verification code.';
    if (empty($errors)) {
        if (verifyCode($userId, $otp, 'email_verify')) {
            activateUser($userId);
            unset($_SESSION['signup_user_id'], $_SESSION['signup_user_email'],
                  $_SESSION['signup_user_name'], $_SESSION['signup_step']);
            auditLog('EMAIL_VERIFIED', 'users', $userId);
            $success = true;
        } else {
            $errors[] = 'Invalid or expired code.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'resend_verify') {
    $userId = (int)($_SESSION['signup_user_id'] ?? 0);
    if ($userId) {
        $code = generateVerificationCode($userId, 'email_verify');
        $stmt = db()->prepare('SELECT email, name FROM users WHERE id=?');
        $stmt->execute([$userId]);
        $u = $stmt->fetch();
        sendVerificationEmail($u['email'], $u['name'], $code, 'email_verify');
        setFlash('success', 'A new code has been sent.');
    }
    $step = 2;
}

if (isset($_GET['reset'])) {
    unset($_SESSION['signup_step'], $_SESSION['signup_user_id'],
          $_SESSION['signup_user_email'], $_SESSION['signup_user_name']);
    header('Location: ' . SITE_URL . '/auth/signup.php');
    exit;
}

$maskedEmail = '';
if (!empty($_SESSION['signup_user_email'])) {
    $em = $_SESSION['signup_user_email'];
    $p  = explode('@', $em);
    $maskedEmail = substr($p[0], 0, 2) . str_repeat('*', max(1, strlen($p[0])-2)) . '@' . $p[1];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — GuardVAX</title>
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
      <h1>Guard<strong>VAX</strong></h1>
      <p class="auth-tagline">Join Our Healthcare Platform</p>
      <ul class="auth-features">
        <li><i class="bi bi-check-circle-fill"></i> Manage patient records</li>
        <li><i class="bi bi-check-circle-fill"></i> Record vaccinations</li>
        <li><i class="bi bi-check-circle-fill"></i> Schedule appointments</li>
        <li><i class="bi bi-check-circle-fill"></i> 2FA protected login</li>
      </ul>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="auth-panel-right">
    <div class="auth-card">
      <div class="auth-logo d-lg-none">🛡️ <strong>GuardVAX</strong></div>

      <?php if ($success): ?>
      <!-- Success -->
      <div class="text-center py-4">
        <div style="font-size:4rem">✅</div>
        <h2 class="auth-title mt-3">Account Verified!</h2>
        <p class="text-muted">Your nurse account is now active.</p>
        <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-primary w-100 mt-3 btn-auth">
          <i class="bi bi-box-arrow-in-right me-1"></i> Proceed to Login
        </a>
      </div>

      <?php elseif ($step === 2): ?>
      <!-- Step 2: Verify OTP -->
      <div class="text-center mb-2"><div class="otp-icon">📧</div></div>
      <h2 class="auth-title">Verify your email</h2>
      <p class="auth-subtitle">Code sent to <strong class="text-primary"><?= sanitize($maskedEmail) ?></strong></p>

      <?php echo renderFlash(); ?>
      <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger py-2 small"><?= sanitize($e) ?></div>
      <?php endforeach; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="action" value="verify_email">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <div class="mb-3">
          <label class="form-label">Verification Code</label>
          <input type="text" name="otp"
                 class="form-control form-control-lg text-center otp-input"
                 maxlength="6" placeholder="_ _ _ _ _ _"
                 inputmode="numeric" autofocus required>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-auth">
          <i class="bi bi-envelope-check me-1"></i> Verify Account
        </button>
      </form>
      <div class="text-center mt-3 small">
        <form method="POST" class="d-inline">
          <input type="hidden" name="action" value="resend_verify">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <button type="submit" class="btn btn-link btn-sm text-muted">Resend code</button>
        </form>
        &nbsp;|&nbsp;
        <a href="?reset=1" class="text-muted">← Start over</a>
      </div>

      <?php else: ?>
      <!-- Step 1: Register Form -->
      <h2 class="auth-title">Create Nurse Account</h2>
      <p class="auth-subtitle">Register as a healthcare staff member</p>

      <!-- Patient notice -->
      <div class="alert alert-info small mb-3">
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>Are you a Patient?</strong> Patients must be registered
        by a nurse at the hospital. Please visit the nurse station with
        a valid ID, or
        <a href="<?= SITE_URL ?>/contact.php" class="alert-link fw-semibold">contact us here</a>.
      </div>

      <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger py-2 small">
          <i class="bi bi-exclamation-triangle me-1"></i><?= sanitize($e) ?>
        </div>
      <?php endforeach; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="action" value="signup">
        <!-- Role is always nurse — hidden field -->
        <input type="hidden" name="role" value="nurse">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="name" class="form-control"
                   placeholder="Juan dela Cruz"
                   value="<?= sanitize($_POST['name'] ?? '') ?>" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control"
                   placeholder="nurse@hospital.com"
                   value="<?= sanitize($_POST['email'] ?? '') ?>" required>
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" id="pw1"
                     class="form-control" placeholder="Min. 8 chars" required>
            </div>
          </div>
          <div class="col-6">
            <label class="form-label">Confirm</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <input type="password" name="confirm_password" id="pw2"
                     class="form-control" placeholder="Re-enter" required>
            </div>
          </div>
        </div>

        <!-- Password strength -->
        <div class="mb-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Password strength</span>
            <span id="strengthLabel">—</span>
          </div>
          <div class="progress" style="height:4px">
            <div class="progress-bar" id="strengthBar" style="width:0;transition:.3s"></div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-auth">
          <i class="bi bi-person-plus me-1"></i> Create Nurse Account
        </button>
      </form>

      <p class="text-center mt-3 small text-muted">
        Already have an account?
        <a href="<?= SITE_URL ?>/auth/login.php" class="text-primary fw-semibold">Sign In</a>
      </p>

      <?php endif; ?>

      <div class="auth-footer">
        <span>© <?= date('Y') ?> GuardVAX Hospital System</span>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// OTP format
const otp = document.querySelector('.otp-input');
if (otp) otp.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'').slice(0,6);
});

// Password strength
const pw1 = document.getElementById('pw1');
if (pw1) pw1.addEventListener('input', function() {
    const v = this.value;
    let s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const bar = document.getElementById('strengthBar');
    const lbl = document.getElementById('strengthLabel');
    const colors = ['','#dc3545','#fd7e14','#ffc107','#198754'];
    const labels = ['','Weak','Fair','Good','Strong'];
    bar.style.width = (s * 25) + '%';
    bar.style.backgroundColor = colors[s] || '';
    lbl.textContent = labels[s] || '—';
});
</script>
</body>
</html>