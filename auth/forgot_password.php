<?php
// auth/forgot_password.php — Password Reset via Email Code
require_once __DIR__ . '/../includes/auth.php';
startSecureSession();

$step    = $_SESSION['reset_step'] ?? 1;
$errors  = [];
$success = false;

// Step 1: Request reset code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request_reset') {
    verifyCsrf();
    $email = sanitizeEmail($_POST['email'] ?? '');

    if (!validateEmail($email)) { $errors[] = 'Enter a valid email.'; }
    else {
        $stmt = db()->prepare('SELECT id, name, status FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['status'] === 'active') {
            $code = generateVerificationCode($user['id'], 'password_reset');
            sendVerificationEmail($email, $user['name'], $code, 'password_reset');
        }
        // Always show same message to prevent user enumeration
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_step']  = 2;
        $step = 2;
    }
}

// Step 2: Verify code + new password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'do_reset') {
    verifyCsrf();
    $email    = $_SESSION['reset_email'] ?? '';
    $code     = trim($_POST['code'] ?? '');
    $newPass  = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($newPass) < 8)   $errors[] = 'Password must be at least 8 characters.';
    if ($newPass !== $confirm)  $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verifyCode($user['id'], $code, 'password_reset')) {
            $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = db()->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hash, $user['id']]);
            auditLog('PASSWORD_RESET', 'users', $user['id']);
            unset($_SESSION['reset_email'], $_SESSION['reset_step']);
            $success = true;
        } else {
            $errors[] = 'Invalid or expired code.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Forgot Password — HCARE</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body class="auth-body d-flex align-items-center justify-content-center">
<div class="auth-card" style="max-width:440px;width:100%;margin:2rem auto">
  <div class="text-center mb-3">
    <span style="font-size:2.5rem">🔑</span>
    <h2 class="auth-title mt-2">Reset Password</h2>
  </div>

  <?php if ($success): ?>
    <div class="text-center py-3">
      <div style="font-size:3rem">✅</div>
      <h4 class="mt-2">Password Updated!</h4>
      <p class="text-muted">Your password has been changed successfully.</p>
      <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-primary w-100">Back to Login</a>
    </div>
  <?php elseif ($step === 2): ?>
    <p class="text-muted text-center small mb-3">Enter the 6-digit code sent to your email and your new password.</p>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger py-2 small"><?= sanitize($e) ?></div><?php endforeach; ?>
    <form method="POST">
      <input type="hidden" name="action" value="do_reset">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="mb-3">
        <label class="form-label">Verification Code</label>
        <input type="text" name="code" class="form-control text-center otp-input" maxlength="6" placeholder="______" inputmode="numeric" autofocus required>
      </div>
      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" placeholder="Min 8 chars" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Set New Password</button>
    </form>
  <?php else: ?>
    <p class="text-muted text-center small mb-3">Enter your registered email and we'll send you a reset code.</p>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger py-2 small"><?= sanitize($e) ?></div><?php endforeach; ?>
    <form method="POST">
      <input type="hidden" name="action" value="request_reset">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required autofocus>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Code</button>
    </form>
  <?php endif; ?>

  <div class="text-center mt-3 small">
    <a href="<?= SITE_URL ?>/auth/login.php" class="text-muted">← Back to Login</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const otp = document.querySelector('.otp-input');
if (otp) otp.addEventListener('input', function() { this.value = this.value.replace(/\D/g,'').slice(0,6); });
</script>
</body>
</html>
