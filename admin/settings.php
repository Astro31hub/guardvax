<?php
// admin/settings.php — Admin Account Settings
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('admin');
$errors = [];

// Update profile name/email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    verifyCsrf();
    $name  = sanitize($_POST['name'] ?? '');
    $email = sanitizeEmail($_POST['email'] ?? '');
    if (strlen($name) < 2)    $errors[] = 'Name too short.';
    if (!validateEmail($email)) $errors[] = 'Invalid email.';

    if (empty($errors)) {
        // Check email not taken by another user
        $stmt = db()->prepare('SELECT id FROM users WHERE email=? AND id!=?');
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already used by another account.';
        } else {
            db()->prepare('UPDATE users SET name=?, email=? WHERE id=?')->execute([$name, $email, $user['id']]);
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            auditLog('PROFILE_UPDATED', 'users', $user['id'], 'Admin updated own profile');
            setFlash('success', 'Profile updated.');
            header('Location: ' . SITE_URL . '/admin/settings.php');
            exit;
        }
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    verifyCsrf();
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = db()->prepare('SELECT password FROM users WHERE id=?');
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch();

    if (!password_verify($current, $row['password'])) $errors[] = 'Current password is incorrect.';
    if (strlen($new) < 8)   $errors[] = 'New password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $new)) $errors[] = 'Password needs at least one uppercase letter.';
    if (!preg_match('/[0-9]/', $new)) $errors[] = 'Password needs at least one number.';
    if ($new !== $confirm)  $errors[] = 'New passwords do not match.';

    if (empty($errors)) {
        $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
        db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $user['id']]);
        auditLog('PASSWORD_CHANGED', 'users', $user['id'], 'Admin changed own password');
        setFlash('success', 'Password changed successfully.');
        header('Location: ' . SITE_URL . '/admin/settings.php');
        exit;
    }
}

// Refresh user data
$stmt = db()->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?');
$stmt->execute([$user['id']]);
$currentUser = $stmt->fetch();

// System stats for info panel
$stats = [
    'users'        => (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'patients'     => (int)db()->query('SELECT COUNT(*) FROM patients')->fetchColumn(),
    'vaccinations' => (int)db()->query('SELECT COUNT(*) FROM vaccinations')->fetchColumn(),
    'logs'         => (int)db()->query('SELECT COUNT(*) FROM audit_logs')->fetchColumn(),
];
?>
<?php renderHead('Settings'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Account Settings', 'Manage your admin profile and security', 'gear-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<div class="row g-4">
  <!-- Profile Update -->
  <div class="col-md-6">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-person-fill me-2 text-primary"></i>Profile Information</div>
      <div class="card-body">
        <div class="text-center mb-4">
          <div class="profile-avatar mx-auto mb-3">
            <?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?>
          </div>
          <span class="badge bg-role-admin px-3 py-2">System Administrator</span>
        </div>
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="update_profile">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= sanitize($currentUser['name']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?= sanitize($currentUser['email']) ?>" required>
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i>Update Profile</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Change Password -->
  <div class="col-md-6">
    <div class="card card-gvx mb-3">
      <div class="card-header-gvx"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Change Password</div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="change_password">
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" id="newPw" class="form-control" required minlength="8">
            <div class="progress mt-1" style="height:4px">
              <div class="progress-bar" id="pwBar" style="width:0;transition:.3s"></div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-warning w-100"><i class="bi bi-key-fill me-1"></i>Change Password</button>
        </form>
      </div>
    </div>

    <!-- System Info -->
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-info-circle-fill me-2 text-info"></i>System Overview</div>
      <div class="card-body">
        <div class="row g-2 text-center">
          <?php foreach ([
            ['label'=>'Users', 'val'=>$stats['users'], 'icon'=>'people-fill', 'color'=>'primary'],
            ['label'=>'Patients', 'val'=>$stats['patients'], 'icon'=>'person-badge', 'color'=>'success'],
            ['label'=>'Vaccinations', 'val'=>$stats['vaccinations'], 'icon'=>'syringe', 'color'=>'info'],
            ['label'=>'Audit Logs', 'val'=>$stats['logs'], 'icon'=>'journal-text', 'color'=>'warning'],
          ] as $s): ?>
          <div class="col-6">
            <div class="p-2 border rounded">
              <i class="bi bi-<?= $s['icon'] ?> text-<?= $s['color'] ?>"></i>
              <div class="fw-bold"><?= number_format($s['val']) ?></div>
              <div class="text-muted small"><?= $s['label'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <hr>
        <div class="small text-muted">
          <div>PHP: <?= PHP_VERSION ?></div>
          <div>Server: <?= sanitize($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') ?></div>
          <div>Last login: <?= formatDate($currentUser['updated_at'], 'M d, Y H:i') ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
<script>
const pw = document.getElementById('newPw');
if (pw) pw.addEventListener('input', function() {
    let s = 0;
    if (this.value.length >= 8) s++;
    if (/[A-Z]/.test(this.value)) s++;
    if (/[0-9]/.test(this.value)) s++;
    if (/[^A-Za-z0-9]/.test(this.value)) s++;
    const b = document.getElementById('pwBar');
    const c = ['','#dc3545','#fd7e14','#ffc107','#198754'];
    b.style.width = (s*25)+'%';
    b.style.backgroundColor = c[s];
});
</script>
