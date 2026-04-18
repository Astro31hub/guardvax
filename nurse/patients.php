<?php
// nurse/patients.php — Register & Manage Patients
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('nurse', 'admin');

$errors = [];

// ── CREATE / UPDATE ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['create_patient', 'update_patient'], true)) {
        $patientId = (int) ($_POST['patient_id'] ?? 0);
        $name      = sanitize($_POST['name'] ?? '');
        $email     = sanitizeEmail($_POST['email'] ?? '');
        $dob       = $_POST['dob'] ?? '';
        $gender    = in_array($_POST['gender'] ?? '', ['Male','Female','Other']) ? $_POST['gender'] : '';
        $blood     = sanitize($_POST['blood_type'] ?? '');
        $address   = sanitize($_POST['address'] ?? '');
        $phone     = sanitize($_POST['phone'] ?? '');
        $ecName    = sanitize($_POST['emergency_contact_name'] ?? '');
        $ecPhone   = sanitize($_POST['emergency_contact_phone'] ?? '');
        $allergies = sanitize($_POST['allergies'] ?? '');
        $notes     = sanitize($_POST['notes'] ?? '');

        // Validation
        if (strlen($name) < 2)      $errors[] = 'Patient name is required.';
        if (!validateEmail($email))  $errors[] = 'Valid email is required.';
        if (!$dob)                   $errors[] = 'Date of birth is required.';
        if (!$gender)                $errors[] = 'Gender is required.';

        if (empty($errors)) {
            try {
                db()->beginTransaction();

                if ($action === 'create_patient') {
                    // Check email
                    if (emailExists($email)) { $errors[] = 'Email already registered.'; db()->rollBack(); goto showPage; }

                    // Create user account
                    // Create user account with default password = Patient@12345
                    $defaultPassword = 'Patient@12345';
                    $hash = password_hash($defaultPassword, PASSWORD_BCRYPT, ['cost' => 12]); // random temp pass
                    $roleId = (int) db()->query('SELECT id FROM roles WHERE name="patient"')->fetchColumn();
                    $stmt   = db()->prepare('INSERT INTO users (name, email, password, role_id, status, email_verified) VALUES (?,?,?,?,"active",1)');
                    $stmt->execute([$name, $email, $hash, $roleId]);
                    $userId = (int) db()->lastInsertId();

                    // Create patient record
                    $code  = generatePatientCode();
                    $stmt  = db()->prepare(
                        'INSERT INTO patients (user_id, patient_code, date_of_birth, gender, blood_type, address, phone,
                         emergency_contact_name, emergency_contact_phone, allergies, notes, registered_by)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
                    );
                    $stmt->execute([$userId, $code, $dob, $gender, $blood, $address, $phone, $ecName, $ecPhone, $allergies, $notes, $user['id']]);
                    $newPid = (int) db()->lastInsertId();

                    db()->commit();
                    auditLog('PATIENT_CREATED', 'patients', $newPid, "Registered: {$code} — {$name}");

                    // Send credentials to patient email
                    $subject = 'Your GuardVAX Patient Account';
                    $body = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:40px">
                    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:32px">
                    <div style="text-align:center;margin-bottom:24px">
                    <span style="font-size:2rem">🛡️</span>
                    <h2 style="color:#0d6efd">GuardVAX Hospital</h2>
                    </div>
                    <p>Hello <strong>' . htmlspecialchars($name) . '</strong>,</p>
                    <p>Your patient account has been created. Here are your login credentials:</p>
                    <div style="background:#f0f4f9;border-radius:8px;padding:20px;margin:20px 0">
                    <p><strong>Patient Code:</strong> ' . $code . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                    <p><strong>Password:</strong> Patient@12345</p>
                    </div>
                    <p style="color:#dc3545"><strong>Please change your password after first login.</strong></p>
                    <div style="text-align:center;margin-top:24px">
                    <a href="' . SITE_URL . '/auth/login.php" 
                    style="background:#0d6efd;color:#fff;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:600">
                    Login to GuardVAX
                    </a>
                    </div>
                    </div></body></html>';

sendEmail($email, $subject, $body);

setFlash('success', "Patient {$name} registered. Code: {$code} | Default password: <strong>Patient@12345</strong> — Please inform the patient.");
                } else {
                    // Update existing
                    $stmt = db()->prepare(
                        'UPDATE patients SET date_of_birth=?, gender=?, blood_type=?, address=?, phone=?,
                         emergency_contact_name=?, emergency_contact_phone=?, allergies=?, notes=?
                         WHERE id=?'
                    );
                    $stmt->execute([$dob, $gender, $blood, $address, $phone, $ecName, $ecPhone, $allergies, $notes, $patientId]);

                    // Update user name/email
                    $stmt = db()->prepare(
                        'UPDATE users u JOIN patients p ON p.user_id = u.id
                         SET u.name = ?, u.email = ? WHERE p.id = ?'
                    );
                    $stmt->execute([$name, $email, $patientId]);

                    db()->commit();
                    auditLog('PATIENT_UPDATED', 'patients', $patientId, "Updated patient ID {$patientId}");
                    setFlash('success', 'Patient information updated.');
                }

                header('Location: ' . SITE_URL . '/nurse/patients.php');
                exit;
            } catch (PDOException $e) {
                db()->rollBack();
                $errors[] = 'Database error. Please try again.';
            }
        }
    }
}

showPage:

// ── List ──────────────────────────────────────────────────────
$search  = sanitize($_GET['q'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$params  = [];
$where   = '1=1';

if ($search) {
    $where .= ' AND (u.name LIKE ? OR p.patient_code LIKE ? OR u.email LIKE ?)';
    $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
}

$countStmt = db()->prepare("SELECT COUNT(*) FROM patients p JOIN users u ON u.id=p.user_id WHERE {$where}");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pager = paginate($total, $perPage, $page);

$stmt = db()->prepare(
    "SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id=p.user_id
     WHERE {$where} ORDER BY p.created_at DESC
     LIMIT {$pager['per_page']} OFFSET {$pager['offset']}"
);
$stmt->execute($params);
$patients = $stmt->fetchAll();

// Fetch patient for edit modal
$editPatient = null;
if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id=p.user_id WHERE p.id=?');
    $s->execute([(int)$_GET['edit']]);
    $editPatient = $s->fetch();
}

$showAddForm = isset($_GET['action']) && $_GET['action'] === 'add';
?>
<?php renderHead('Patients'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Patient Management', 'Register and manage patient records', 'people-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<!-- Toolbar -->
<div class="card card-gvx mb-3">
  <div class="card-body d-flex flex-wrap gap-2 align-items-center">
    <form class="d-flex gap-2 flex-grow-1" method="GET">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Search by name, ID, email…" value="<?= sanitize($search) ?>" style="max-width:280px">
      <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Search</button>
      <?php if ($search): ?><a href="<?= SITE_URL ?>/nurse/patients.php" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
    </form>
    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPatientModal">
      <i class="bi bi-person-plus-fill me-1"></i> Register Patient
    </button>
  </div>
</div>

<!-- Patient Table -->
<div class="card card-gvx">
  <div class="card-header-gvx">
    <i class="bi bi-people-fill me-2 text-primary"></i>
    Patients <span class="badge bg-secondary ms-1"><?= $total ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>Code</th><th>Name</th><th>DOB / Age</th><th>Gender</th><th>Phone</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach ($patients as $p): ?>
        <tr>
          <td><span class="badge bg-light text-dark border"><?= sanitize($p['patient_code']) ?></span></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm"><?= mb_strtoupper(mb_substr($p['name'], 0, 1)) ?></div>
              <div>
                <div class="fw-semibold"><?= sanitize($p['name']) ?></div>
                <div class="text-muted small"><?= sanitize($p['email']) ?></div>
              </div>
            </div>
          </td>
          <td><?= formatDate($p['date_of_birth']) ?> <span class="text-muted small">(<?= calculateAge($p['date_of_birth']) ?> yrs)</span></td>
          <td><?= sanitize($p['gender']) ?></td>
          <td><?= sanitize($p['phone'] ?: '—') ?></td>
          <td>
            <a href="<?= SITE_URL ?>/nurse/patients.php?edit=<?= $p['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
            <a href="<?= SITE_URL ?>/nurse/vaccinations.php?patient_id=<?= $p['id'] ?>" class="btn btn-xs btn-outline-success" title="Vaccinations"><i class="bi bi-syringe"></i></a>
            <a href="<?= SITE_URL ?>/reports/patient_report.php?patient_id=<?= $p['id'] ?>" target="_blank" class="btn btn-xs btn-outline-danger" title="PDF Report"><i class="bi bi-file-pdf"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($patients)): ?>
          <tr><td colspan="6" class="text-center py-4 text-muted">No patients found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pager['pages'] > 1): ?>
  <div class="card-body border-top">
    <nav><ul class="pagination pagination-sm mb-0">
      <?php for ($i=1;$i<=$pager['pages'];$i++): ?>
        <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?=$i?>&q=<?=urlencode($search)?>"><?=$i?></a></li>
      <?php endfor; ?>
    </ul></nav>
  </div>
  <?php endif; ?>
</div>

<?php
// ── Patient Form Modal (shared for add/edit) ──────────────────
function patientFormModal(string $id, string $title, string $action, ?array $p = null): void
{
    $name  = $p ? sanitize($p['name'])  : '';
    $email = $p ? sanitize($p['email']) : '';
    $dob   = $p['date_of_birth'] ?? '';
    $pid   = $p['id'] ?? '';
    $genders     = ['Male','Female','Other'];
    $bloodTypes  = ['','A+','A-','B+','B-','AB+','AB-','O+','O-'];
    $genderOpts  = implode('', array_map(fn($g) => "<option value=\"{$g}\" " . (($p['gender']??'')===$g?'selected':'') . ">{$g}</option>", $genders));
    $bloodOpts   = implode('', array_map(fn($b) => "<option value=\"{$b}\" " . (($p['blood_type']??'')===$b?'selected':'') . ">" . ($b?:' — ') . "</option>", $bloodTypes));

    $extraFields = $action === 'update_patient' ? "<input type='hidden' name='patient_id' value='{$pid}'>" : '';

    echo <<<HTML
<div class="modal fade" id="{$id}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="__CSRF__">
        <input type="hidden" name="action" value="{$action}">
        {$extraFields}
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>{$title}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{$name}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" value="{$email}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
              <input type="date" name="dob" class="form-control" value="{$dob}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Gender <span class="text-danger">*</span></label>
              <select name="gender" class="form-select" required>{$genderOpts}</select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Blood Type</label>
              <select name="blood_type" class="form-select">{$bloodOpts}</select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-control" value="{$p['phone']}" placeholder="+63 9XX XXX XXXX">
            </div>
            <div class="col-md-6">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="{$p['address']}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Emergency Contact Name</label>
              <input type="text" name="emergency_contact_name" class="form-control" value="{$p['emergency_contact_name']}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Emergency Contact Phone</label>
              <input type="tel" name="emergency_contact_phone" class="form-control" value="{$p['emergency_contact_phone']}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Allergies</label>
              <textarea name="allergies" class="form-control" rows="2">{$p['allergies']}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="2">{$p['notes']}</textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Patient</button>
        </div>
      </form>
    </div>
  </div>
</div>
HTML;
}

$csrf = csrfToken();
ob_start();
patientFormModal('addPatientModal', 'Register New Patient', 'create_patient');
echo str_replace('__CSRF__', $csrf, ob_get_clean());

if ($editPatient) {
    ob_start();
    patientFormModal('editPatientModal', 'Edit Patient', 'update_patient', $editPatient);
    echo str_replace('__CSRF__', $csrf, ob_get_clean());
}
?>

<?php renderFooter(); ?>
<?php if ($editPatient): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('editPatientModal')).show();
});
</script>
<?php endif; ?>
