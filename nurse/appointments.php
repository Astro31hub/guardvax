<?php
// nurse/appointments.php — Appointment Scheduling
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('nurse', 'admin');
$errors = [];

// ── Create / Update ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['create_appt', 'update_appt'])) {
        $patientId  = (int)($_POST['patient_id'] ?? 0);
        $deptId     = (int)($_POST['department_id'] ?? 0);
        $doctorId   = (int)($_POST['doctor_id'] ?? 0) ?: null;
        $date       = $_POST['appointment_date'] ?? '';
        $time       = $_POST['appointment_time'] ?? '';
        $reason     = sanitize($_POST['reason'] ?? '');
        $status     = $_POST['status'] ?? 'Pending';
        $notes      = sanitize($_POST['notes'] ?? '');

        if (!$patientId) $errors[] = 'Patient required.';
        if (!$deptId)    $errors[] = 'Department required.';
        if (!$date)      $errors[] = 'Date required.';
        if (!$time)      $errors[] = 'Time required.';

        if (empty($errors)) {
            if ($action === 'create_appt') {
                $stmt = db()->prepare(
                    'INSERT INTO appointments (patient_id, department_id, doctor_id, appointment_date, appointment_time, reason, status, notes, created_by)
                     VALUES (?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([$patientId, $deptId, $doctorId, $date, $time, $reason, $status, $notes, $user['id']]);
                $aid = (int)db()->lastInsertId();
                auditLog('APPOINTMENT_CREATED', 'appointments', $aid, "Patient ID $patientId — $date $time");
                setFlash('success', 'Appointment scheduled successfully.');
            } else {
                $aid = (int)($_POST['appt_id'] ?? 0);
                $stmt = db()->prepare(
                    'UPDATE appointments SET patient_id=?, department_id=?, doctor_id=?, appointment_date=?,
                     appointment_time=?, reason=?, status=?, notes=? WHERE id=?'
                );
                $stmt->execute([$patientId, $deptId, $doctorId, $date, $time, $reason, $status, $notes, $aid]);
                auditLog('APPOINTMENT_UPDATED', 'appointments', $aid, "Status → $status");
                setFlash('success', 'Appointment updated.');
            }
            header('Location: ' . SITE_URL . '/nurse/appointments.php');
            exit;
        }
    }

    if ($action === 'delete_appt') {
        $aid = (int)($_POST['appt_id'] ?? 0);
        db()->prepare('DELETE FROM appointments WHERE id=?')->execute([$aid]);
        auditLog('APPOINTMENT_DELETED', 'appointments', $aid);
        setFlash('success', 'Appointment removed.');
        header('Location: ' . SITE_URL . '/nurse/appointments.php');
        exit;
    }
}

// ── Filters ──────────────────────────────────────────────────
$dateFilter   = $_GET['date'] ?? date('Y-m-d');
$statusFilter = sanitize($_GET['status'] ?? '');
$deptFilter   = (int)($_GET['dept'] ?? 0);

$where  = '1=1';
$params = [];
if ($dateFilter)   { $where .= ' AND a.appointment_date = ?'; $params[] = $dateFilter; }
if ($statusFilter) { $where .= ' AND a.status = ?';            $params[] = $statusFilter; }
if ($deptFilter)   { $where .= ' AND a.department_id = ?';    $params[] = $deptFilter; }

$appointments = db()->prepare(
    "SELECT a.*, p.patient_code, u.name AS patient_name,
            d.name AS dept_name, doc.name AS doctor_name
     FROM appointments a
     JOIN patients p    ON p.id  = a.patient_id
     JOIN users u       ON u.id  = p.user_id
     JOIN departments d ON d.id  = a.department_id
     LEFT JOIN users doc ON doc.id = a.doctor_id
     WHERE {$where}
     ORDER BY a.appointment_date ASC, a.appointment_time ASC"
);
$appointments->execute($params);
$appointments = $appointments->fetchAll();

$patients    = db()->query('SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name')->fetchAll();
$departments = db()->query('SELECT * FROM departments WHERE is_active=1 ORDER BY name')->fetchAll();
$doctors     = db()->query('SELECT id, name FROM users WHERE role_id IN (SELECT id FROM roles WHERE name IN ("nurse","admin")) AND status="active" ORDER BY name')->fetchAll();

$statusColors = ['Pending'=>'warning','Confirmed'=>'primary','Completed'=>'success','Cancelled'=>'danger','No-show'=>'secondary'];

$editAppt = null;
if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT * FROM appointments WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $editAppt = $s->fetch();
}

// Today's count
$todayCount = (int) db()->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn();
$pendingCount = (int) db()->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn();
?>
<?php renderHead('Appointments'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Appointments', 'Schedule and manage patient appointments', 'calendar2-check-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<!-- Stats -->
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3"><div class="stat-card stat-primary"><div class="stat-icon"><i class="bi bi-calendar-day"></i></div><div><div class="stat-value"><?= $todayCount ?></div><div class="stat-label">Today</div></div></div></div>
  <div class="col-6 col-md-3"><div class="stat-card stat-warning"><div class="stat-icon"><i class="bi bi-hourglass-split"></i></div><div><div class="stat-value"><?= $pendingCount ?></div><div class="stat-label">Pending</div></div></div></div>
</div>

<div class="row g-3">
  <!-- Form -->
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-<?= $editAppt ? 'pencil-fill text-primary' : 'plus-circle-fill text-success' ?> me-2"></i><?= $editAppt ? 'Edit Appointment' : 'New Appointment' ?></div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="<?= $editAppt ? 'update_appt' : 'create_appt' ?>">
          <?php if ($editAppt): ?><input type="hidden" name="appt_id" value="<?= $editAppt['id'] ?>"><?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Patient <span class="text-danger">*</span></label>
            <select name="patient_id" class="form-select" required>
              <option value="">— Select Patient —</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($editAppt['patient_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                  <?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Department <span class="text-danger">*</span></label>
            <select name="department_id" class="form-select" required>
              <option value="">— Select Department —</option>
              <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($editAppt['department_id'] ?? 0) == $d['id'] ? 'selected' : '' ?>><?= sanitize($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Assigned Doctor/Nurse</label>
            <select name="doctor_id" class="form-select">
              <option value="">— Not assigned —</option>
              <?php foreach ($doctors as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($editAppt['doctor_id'] ?? 0) == $d['id'] ? 'selected' : '' ?>><?= sanitize($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-7">
              <label class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" name="appointment_date" class="form-control" value="<?= $editAppt['appointment_date'] ?? date('Y-m-d') ?>" required>
            </div>
            <div class="col-5">
              <label class="form-label">Time <span class="text-danger">*</span></label>
              <input type="time" name="appointment_time" class="form-control" value="<?= $editAppt['appointment_time'] ?? '09:00' ?>" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason / Chief Complaint</label>
            <textarea name="reason" class="form-control" rows="2"><?= sanitize($editAppt['reason'] ?? '') ?></textarea>
          </div>
          <?php if ($editAppt): ?>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <?php foreach (['Pending','Confirmed','Completed','Cancelled','No-show'] as $s): ?>
                <option value="<?= $s ?>" <?= ($editAppt['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= sanitize($editAppt['notes'] ?? '') ?></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-<?= $editAppt ? 'primary' : 'success' ?> flex-grow-1">
              <i class="bi bi-check2 me-1"></i><?= $editAppt ? 'Save Changes' : 'Schedule' ?>
            </button>
            <?php if ($editAppt): ?><a href="<?= SITE_URL ?>/nurse/appointments.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- List -->
  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <form class="d-flex flex-wrap gap-2" method="GET">
          <input type="date" name="date" class="form-control form-control-sm" value="<?= sanitize($dateFilter) ?>" style="max-width:150px">
          <select name="status" class="form-select form-select-sm" style="max-width:130px">
            <option value="">All Status</option>
            <?php foreach (['Pending','Confirmed','Completed','Cancelled','No-show'] as $s): ?>
              <option value="<?= $s ?>" <?= $statusFilter===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <select name="dept" class="form-select form-select-sm" style="max-width:150px">
            <option value="">All Depts</option>
            <?php foreach ($departments as $d): ?>
              <option value="<?= $d['id'] ?>" <?= $deptFilter==$d['id']?'selected':'' ?>><?= sanitize($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
          <a href="<?= SITE_URL ?>/nurse/appointments.php" class="btn btn-sm btn-outline-secondary">All</a>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Patient</th><th>Department</th><th>Date & Time</th><th>Reason</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($appointments as $a): ?>
            <tr>
              <td>
                <div class="fw-semibold small"><?= sanitize($a['patient_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($a['patient_code']) ?></div>
              </td>
              <td class="small"><?= sanitize($a['dept_name']) ?></td>
              <td class="small">
                <div><?= formatDate($a['appointment_date']) ?></div>
                <div class="text-muted"><?= date('h:i A', strtotime($a['appointment_time'])) ?></div>
              </td>
              <td class="small text-muted" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= sanitize($a['reason'] ?: '—') ?></td>
              <td><span class="badge bg-<?= $statusColors[$a['status']] ?? 'secondary' ?>"><?= $a['status'] ?></span></td>
              <td>
                <a href="?edit=<?= $a['id'] ?>" class="btn btn-xs btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this appointment?')">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="action" value="delete_appt">
                  <input type="hidden" name="appt_id" value="<?= $a['id'] ?>">
                  <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No appointments found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
