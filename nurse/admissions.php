<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// nurse/admissions.php — Patient Admission & Discharge
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('nurse', 'admin');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'admit_patient') {
        $patientId  = (int)($_POST['patient_id'] ?? 0);
        $deptId     = (int)($_POST['department_id'] ?? 0);
        $room       = sanitize($_POST['room_number'] ?? '');
        $bed        = sanitize($_POST['bed_number'] ?? '');
        $reason     = sanitize($_POST['reason'] ?? '');
        $notes      = sanitize($_POST['notes'] ?? '');

        if (!$patientId) $errors[] = 'Patient required.';
        if (!$deptId)    $errors[] = 'Department required.';
        if (!$reason)    $errors[] = 'Reason for admission required.';

        if (empty($errors)) {
            $stmt = db()->prepare(
                'INSERT INTO admissions (patient_id, department_id, admitted_by, room_number, bed_number, reason, notes)
                 VALUES (?,?,?,?,?,?,?)'
            );
            $stmt->execute([$patientId, $deptId, $user['id'], $room, $bed, $reason, $notes]);
            $aid = (int)db()->lastInsertId();
            auditLog('PATIENT_ADMITTED', 'admissions', $aid, "Patient ID $patientId — Dept ID $deptId");
            setFlash('success', 'Patient admitted successfully.');
            header('Location: ' . SITE_URL . '/nurse/admissions.php');
            exit;
        }
    }

    if ($action === 'discharge_patient') {
        $aid       = (int)($_POST['admission_id'] ?? 0);
        $diagnosis = sanitize($_POST['diagnosis'] ?? '');
        $notes     = sanitize($_POST['discharge_notes'] ?? '');
        $status    = $_POST['status'] ?? 'Discharged';

        $stmt = db()->prepare(
            'UPDATE admissions SET status=?, discharge_date=NOW(), diagnosis=?, discharge_notes=? WHERE id=?'
        );
        $stmt->execute([$status, $diagnosis, $notes, $aid]);
        auditLog('PATIENT_DISCHARGED', 'admissions', $aid, "Status → $status");
        setFlash('success', 'Patient discharged.');
        header('Location: ' . SITE_URL . '/nurse/admissions.php');
        exit;
    }
}

// ── Current admissions ───────────────────────────────────────
$statusFilter = sanitize($_GET['status'] ?? 'Admitted');
$where  = '1=1';
$params = [];
if ($statusFilter) { $where .= ' AND a.status = ?'; $params[] = $statusFilter; }

$admissions = db()->prepare(
    "SELECT a.*, p.patient_code, u.name AS patient_name,
            d.name AS dept_name, s.name AS admitted_by_name
     FROM admissions a
     JOIN patients p    ON p.id  = a.patient_id
     JOIN users u       ON u.id  = p.user_id
     JOIN departments d ON d.id  = a.department_id
     JOIN users s       ON s.id  = a.admitted_by
     WHERE {$where}
     ORDER BY a.admission_date DESC"
);
$admissions->execute($params);
$admissions = $admissions->fetchAll();

$patients    = db()->query('SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name')->fetchAll();
$departments = db()->query('SELECT * FROM departments WHERE is_active=1 ORDER BY name')->fetchAll();

// Stats
$admitted   = (int)db()->query("SELECT COUNT(*) FROM admissions WHERE status='Admitted'")->fetchColumn();
$discharged = (int)db()->query("SELECT COUNT(*) FROM admissions WHERE status='Discharged' AND DATE(discharge_date)=CURDATE()")->fetchColumn();
$critical   = (int)db()->query("SELECT COUNT(*) FROM admissions WHERE status='Critical'")->fetchColumn();

$statusColors = ['Admitted'=>'primary','Discharged'=>'success','Transferred'=>'info','Critical'=>'danger'];
?>
<?php renderHead('Admissions'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Patient Admissions', 'Manage hospital admissions and discharges', 'hospital-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<!-- Stats -->
<div class="row g-3 mb-3">
  <div class="col-4"><div class="stat-card stat-primary"><div class="stat-icon"><i class="bi bi-person-check-fill"></i></div><div><div class="stat-value"><?= $admitted ?></div><div class="stat-label">Currently Admitted</div></div></div></div>
  <div class="col-4"><div class="stat-card stat-success"><div class="stat-icon"><i class="bi bi-door-open-fill"></i></div><div><div class="stat-value"><?= $discharged ?></div><div class="stat-label">Discharged Today</div></div></div></div>
  <div class="col-4"><div class="stat-card stat-warning"><div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div><div><div class="stat-value"><?= $critical ?></div><div class="stat-label">Critical</div></div></div></div>
</div>

<div class="row g-3">
  <!-- Admit Form -->
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-person-plus-fill me-2 text-success"></i>Admit Patient</div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="admit_patient">
          <div class="mb-3">
            <label class="form-label">Patient <span class="text-danger">*</span></label>
            <select name="patient_id" class="form-select" required>
              <option value="">— Select Patient —</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>"><?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Department <span class="text-danger">*</span></label>
            <select name="department_id" class="form-select" required>
              <option value="">— Select Department —</option>
              <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>"><?= sanitize($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label">Room</label><input type="text" name="room_number" class="form-control" placeholder="e.g. 204"></div>
            <div class="col-6"><label class="form-label">Bed</label><input type="text" name="bed_number" class="form-control" placeholder="e.g. A"></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason for Admission <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control" rows="3" required placeholder="Chief complaint / reason..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
          <button type="submit" class="btn btn-success w-100"><i class="bi bi-hospital me-1"></i>Admit Patient</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Admissions List -->
  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-list-ul me-2 text-primary"></i>Admissions</span>
        <div class="d-flex gap-1">
          <?php foreach ([''=>'All','Admitted'=>'Admitted','Critical'=>'Critical','Discharged'=>'Discharged'] as $val => $label): ?>
            <a href="?status=<?= urlencode($val) ?>" class="btn btn-xs <?= $statusFilter===$val ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $label ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Patient</th><th>Department</th><th>Room</th><th>Admitted</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($admissions as $a): ?>
            <tr>
              <td>
                <div class="fw-semibold small"><?= sanitize($a['patient_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($a['patient_code']) ?></div>
              </td>
              <td class="small"><?= sanitize($a['dept_name']) ?></td>
              <td class="small"><?= sanitize($a['room_number'] ? "Room {$a['room_number']}" . ($a['bed_number'] ? " Bed {$a['bed_number']}" : '') : '—') ?></td>
              <td class="small"><?= formatDate($a['admission_date'], 'M d, Y') ?></td>
              <td><span class="badge bg-<?= $statusColors[$a['status']] ?? 'secondary' ?>"><?= $a['status'] ?></span></td>
              <td>
                <?php if ($a['status'] === 'Admitted' || $a['status'] === 'Critical'): ?>
                <button class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#dischargeModal<?= $a['id'] ?>">
                  <i class="bi bi-door-open"></i> Discharge
                </button>
                <!-- Discharge Modal -->
                <div class="modal fade" id="dischargeModal<?= $a['id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="discharge_patient">
                        <input type="hidden" name="admission_id" value="<?= $a['id'] ?>">
                        <div class="modal-header"><h5 class="modal-title">Discharge — <?= sanitize($a['patient_name']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                          <div class="mb-3"><label class="form-label">Final Diagnosis</label><textarea name="diagnosis" class="form-control" rows="3"></textarea></div>
                          <div class="mb-3"><label class="form-label">Discharge Status</label>
                            <select name="status" class="form-select">
                              <option value="Discharged">Discharged</option>
                              <option value="Transferred">Transferred</option>
                            </select>
                          </div>
                          <div class="mb-3"><label class="form-label">Discharge Notes</label><textarea name="discharge_notes" class="form-control" rows="2"></textarea></div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Confirm Discharge</button></div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php else: ?>
                  <span class="text-muted small"><?= $a['discharge_date'] ? formatDate($a['discharge_date'], 'M d') : '—' ?></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($admissions)): ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No admissions found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
