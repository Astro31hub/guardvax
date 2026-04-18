<?php
// patient/profile.php — View & Update Own Profile
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('patient');

$stmt = db()->prepare('SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id=p.user_id WHERE p.user_id=?');
$stmt->execute([$user['id']]);
$patient = $stmt->fetch();

$errors    = [];
$pwErrors  = [];

// ── Update contact info ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    verifyCsrf();
    $phone   = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $ecName  = sanitize($_POST['emergency_contact_name'] ?? '');
    $ecPhone = sanitize($_POST['emergency_contact_phone'] ?? '');

    if ($patient) {
        $stmt = db()->prepare(
            'UPDATE patients SET phone=?, address=?, emergency_contact_name=?, emergency_contact_phone=? WHERE user_id=?'
        );
        $stmt->execute([$phone, $address, $ecName, $ecPhone, $user['id']]);
        auditLog('PROFILE_UPDATED', 'patients', $patient['id'], 'Patient updated own profile');
        setFlash('success', 'Profile updated successfully.');
        header('Location: ' . SITE_URL . '/patient/profile.php');
        exit;
    }
}

// ── Change password ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    verifyCsrf();
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_new'] ?? '';

    // Get current hash
    $stmt2 = db()->prepare('SELECT password FROM users WHERE id=?');
    $stmt2->execute([$user['id']]);
    $row = $stmt2->fetch();

    if (!password_verify($current, $row['password'])) $pwErrors[] = 'Current password is incorrect.';
    if (strlen($new) < 8)                             $pwErrors[] = 'New password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $new))                 $pwErrors[] = 'New password needs at least one uppercase letter.';
    if (!preg_match('/[0-9]/', $new))                 $pwErrors[] = 'New password needs at least one number.';
    if ($new !== $confirm)                            $pwErrors[] = 'New passwords do not match.';

    if (empty($pwErrors)) {
        $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
        db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $user['id']]);
        auditLog('PASSWORD_CHANGED', 'users', $user['id'], 'Patient changed own password');
        setFlash('success', 'Password changed successfully. Please use your new password next time you login.');
        header('Location: ' . SITE_URL . '/patient/profile.php');
        exit;
    }
}
?>
<?php renderHead('My Profile'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('My Profile', 'View and update your personal information', 'person-circle'); ?>

<?php echo renderFlash(); ?>

<?php if (!$patient): ?>
  <div class="alert alert-warning">
    Your patient profile has not been set up yet.
    Please <a href="<?= SITE_URL ?>/contact.php" class="alert-link">contact the nurse station</a> to complete your registration.
  </div>
<?php else: ?>

<div class="row g-4">

  <!-- ── Profile Card ── -->
  <div class="col-md-4">
    <div class="card card-gvx text-center">
      <div class="card-body py-4">
        <div class="profile-avatar mx-auto mb-3">
          <?= mb_strtoupper(mb_substr($patient['name'], 0, 1)) ?>
        </div>
        <h4 class="fw-bold mb-1"><?= sanitize($patient['name']) ?></h4>
        <p class="text-muted mb-2"><?= sanitize($patient['email']) ?></p>
        <span class="badge bg-primary"><?= sanitize($patient['patient_code']) ?></span>
        <hr>
        <div class="row text-start small g-2">
          <div class="col-6 text-muted">Age</div>
          <div class="col-6 fw-semibold"><?= calculateAge($patient['date_of_birth']) ?> years</div>
          <div class="col-6 text-muted">Gender</div>
          <div class="col-6 fw-semibold"><?= sanitize($patient['gender']) ?></div>
          <div class="col-6 text-muted">Blood Type</div>
          <div class="col-6 fw-semibold"><?= sanitize($patient['blood_type'] ?: 'Unknown') ?></div>
          <div class="col-6 text-muted">Date of Birth</div>
          <div class="col-6 fw-semibold"><?= formatDate($patient['date_of_birth']) ?></div>
        </div>
        <hr>
        <a href="<?= SITE_URL ?>/reports/patient_report.php?patient_id=<?= $patient['id'] ?>"
           target="_blank" class="btn btn-danger btn-sm w-100">
          <i class="bi bi-file-pdf me-1"></i> Download PDF Report
        </a>
      </div>
    </div>
  </div>

  <!-- ── Right Column ── -->
  <div class="col-md-8">

    <!-- Contact Info Form -->
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-pencil-fill me-2 text-primary"></i>Update Contact Information
      </div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="update_profile">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phone" class="form-control"
                     value="<?= sanitize($patient['phone']) ?>"
                     placeholder="+63 9XX XXX XXXX">
            </div>
            <div class="col-md-6">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control"
                     value="<?= sanitize($patient['address']) ?>"
                     placeholder="Street, City, Province">
            </div>
            <div class="col-md-6">
              <label class="form-label">Emergency Contact Name</label>
              <input type="text" name="emergency_contact_name" class="form-control"
                     value="<?= sanitize($patient['emergency_contact_name']) ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Emergency Contact Phone</label>
              <input type="tel" name="emergency_contact_phone" class="form-control"
                     value="<?= sanitize($patient['emergency_contact_phone']) ?>">
            </div>
          </div>
          <hr>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle me-1"></i>Save Changes
          </button>
        </form>
      </div>
    </div>

    <!-- Medical Info (read only) -->
    <div class="card card-gvx mt-3">
      <div class="card-header-gvx">
        <i class="bi bi-heart-pulse me-2 text-danger"></i>Medical Information
      </div>
      <div class="card-body">
        <div class="row small g-3">
          <div class="col-md-6">
            <div class="text-muted mb-1">Known Allergies</div>
            <div class="p-2 bg-light rounded">
              <?= sanitize($patient['allergies'] ?: 'None reported') ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="text-muted mb-1">Clinical Notes</div>
            <div class="p-2 bg-light rounded">
              <?= sanitize($patient['notes'] ?: 'No notes on file') ?>
            </div>
          </div>
        </div>
        <p class="text-muted small mt-3 mb-0">
          <i class="bi bi-info-circle me-1"></i>
          Medical information can only be updated by a nurse or administrator.
        </p>
      </div>
    </div>

    <!-- ── Change Password ── -->
    <div class="card card-gvx mt-3">
      <div class="card-header-gvx">
        <i class="bi bi-key-fill me-2 text-warning"></i>Change Password
      </div>
      <div class="card-body">

        <?php if (!empty($pwErrors)): ?>
          <?php foreach ($pwErrors as $e): ?>
            <div class="alert alert-danger py-2 small">
              <i class="bi bi-exclamation-triangle me-1"></i><?= sanitize($e) ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- First login warning -->
        <div class="alert alert-warning small py-2 mb-3">
          <i class="bi bi-shield-exclamation me-1"></i>
          If this is your first login, your default password is
          <strong>Patient@12345</strong> — please change it now.
        </div>

        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="change_password">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Current Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="current_password"
                       id="curPw" class="form-control"
                       placeholder="Your current password" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePw('curPw','eyeCur')">
                  <i class="bi bi-eye" id="eyeCur"></i>
                </button>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="new_password"
                       id="newPw" class="form-control"
                       placeholder="Min. 8 characters" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePw('newPw','eyeNew')">
                  <i class="bi bi-eye" id="eyeNew"></i>
                </button>
              </div>
              <!-- Strength bar -->
              <div class="mt-1">
                <div class="progress" style="height:4px">
                  <div class="progress-bar" id="pwStrBar"
                       style="width:0;transition:.3s"></div>
                </div>
                <div class="text-end small text-muted mt-1"
                     id="pwStrLabel"></div>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="confirm_new"
                       id="conPw" class="form-control"
                       placeholder="Re-enter new password" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePw('conPw','eyeCon')">
                  <i class="bi bi-eye" id="eyeCon"></i>
                </button>
              </div>
              <div class="small mt-1" id="matchLabel"></div>
            </div>
          </div>
          <hr>
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-key me-1"></i> Change Password
          </button>
        </form>
      </div>
    </div>
    <!-- ── End Change Password ── -->

  </div>
</div>

<?php endif; ?>
<?php renderFooter(); ?>

<script>
// Toggle password visibility
function togglePw(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

// Password strength meter
const newPw = document.getElementById('newPw');
const conPw = document.getElementById('conPw');

if (newPw) {
    newPw.addEventListener('input', function () {
        const v = this.value;
        let s = 0;
        if (v.length >= 8)           s++;
        if (/[A-Z]/.test(v))         s++;
        if (/[0-9]/.test(v))         s++;
        if (/[^A-Za-z0-9]/.test(v))  s++;

        const bar = document.getElementById('pwStrBar');
        const lbl = document.getElementById('pwStrLabel');
        const colors = ['', '#dc3545', '#fd7e14', '#ffc107', '#198754'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        bar.style.width = (s * 25) + '%';
        bar.style.backgroundColor = colors[s] || '';
        lbl.textContent = labels[s] || '';
        checkMatch();
    });
}

// Password match checker
if (conPw) {
    conPw.addEventListener('input', checkMatch);
}

function checkMatch() {
    const lbl = document.getElementById('matchLabel');
    if (!newPw || !conPw || !conPw.value) { lbl.textContent = ''; return; }
    if (newPw.value === conPw.value) {
        lbl.innerHTML = '<span style="color:#198754"><i class="bi bi-check-circle me-1"></i>Passwords match</span>';
    } else {
        lbl.innerHTML = '<span style="color:#dc3545"><i class="bi bi-x-circle me-1"></i>Passwords do not match</span>';
    }
}
</script>