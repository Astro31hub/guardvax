<?php
// nurse/prescriptions.php — Prescription Management
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('nurse', 'admin');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add_prescription') {
        $patientId   = (int)($_POST['patient_id'] ?? 0);
        $medicine    = sanitize($_POST['medicine_name'] ?? '');
        $dosage      = sanitize($_POST['dosage'] ?? '');
        $frequency   = sanitize($_POST['frequency'] ?? '');
        $duration    = sanitize($_POST['duration'] ?? '');
        $qty         = (int)($_POST['quantity'] ?? 0) ?: null;
        $instructions= sanitize($_POST['instructions'] ?? '');

        if (!$patientId)       $errors[] = 'Patient required.';
        if (strlen($medicine) < 2) $errors[] = 'Medicine name required.';
        if (!$dosage)          $errors[] = 'Dosage required.';
        if (!$frequency)       $errors[] = 'Frequency required.';

        if (empty($errors)) {
            $stmt = db()->prepare(
                'INSERT INTO prescriptions (patient_id, prescribed_by, medicine_name, dosage, frequency, duration, quantity, instructions)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([$patientId, $user['id'], $medicine, $dosage, $frequency, $duration, $qty, $instructions]);
            $pid = (int)db()->lastInsertId();
            auditLog('PRESCRIPTION_ADDED', 'prescriptions', $pid, "$medicine for Patient ID $patientId");
            setFlash('success', 'Prescription added.');
            header('Location: ' . SITE_URL . '/nurse/prescriptions.php');
            exit;
        }
    }

    if ($action === 'dispense') {
        $pid = (int)($_POST['presc_id'] ?? 0);
        $stmt = db()->prepare('UPDATE prescriptions SET status="Dispensed", dispensed_at=NOW(), dispensed_by=? WHERE id=?');
        $stmt->execute([$user['id'], $pid]);
        auditLog('PRESCRIPTION_DISPENSED', 'prescriptions', $pid);
        setFlash('success', 'Marked as dispensed.');
        header('Location: ' . SITE_URL . '/nurse/prescriptions.php');
        exit;
    }

    if ($action === 'cancel_presc') {
        $pid = (int)($_POST['presc_id'] ?? 0);
        db()->prepare('UPDATE prescriptions SET status="Cancelled" WHERE id=?')->execute([$pid]);
        auditLog('PRESCRIPTION_CANCELLED', 'prescriptions', $pid);
        setFlash('success', 'Prescription cancelled.');
        header('Location: ' . SITE_URL . '/nurse/prescriptions.php');
        exit;
    }
}

$statusFilter = sanitize($_GET['status'] ?? '');
$where  = '1=1';
$params = [];
if ($statusFilter) { $where .= ' AND pr.status=?'; $params[] = $statusFilter; }

$prescriptions = db()->prepare(
    "SELECT pr.*, p.patient_code, u.name AS patient_name,
            doc.name AS prescribed_by_name, dis.name AS dispensed_by_name
     FROM prescriptions pr
     JOIN patients p   ON p.id  = pr.patient_id
     JOIN users u      ON u.id  = p.user_id
     JOIN users doc    ON doc.id = pr.prescribed_by
     LEFT JOIN users dis ON dis.id = pr.dispensed_by
     WHERE {$where}
     ORDER BY pr.prescribed_at DESC LIMIT 100"
);
$prescriptions->execute($params);
$prescriptions = $prescriptions->fetchAll();

$patients = db()->query('SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name')->fetchAll();
$pending  = (int)db()->query("SELECT COUNT(*) FROM prescriptions WHERE status='Pending'")->fetchColumn();
$statusColors = ['Pending'=>'warning','Dispensed'=>'success','Cancelled'=>'danger'];
?>
<?php renderHead('Prescriptions'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Prescriptions', 'Manage patient prescriptions and dispensing', 'capsule-pill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<?php if ($pending > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
  <i class="bi bi-exclamation-circle-fill"></i>
  <strong><?= $pending ?> prescription<?= $pending > 1 ? 's' : '' ?> pending dispensing.</strong>
</div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-plus-circle-fill me-2 text-success"></i>New Prescription</div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="add_prescription">
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
            <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
            <input type="text" name="medicine_name" class="form-control" required placeholder="e.g. Amoxicillin 500mg">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label">Dosage <span class="text-danger">*</span></label><input type="text" name="dosage" class="form-control" placeholder="e.g. 1 capsule"></div>
            <div class="col-6"><label class="form-label">Frequency <span class="text-danger">*</span></label><input type="text" name="frequency" class="form-control" placeholder="e.g. 3x a day"></div>
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6"><label class="form-label">Duration</label><input type="text" name="duration" class="form-control" placeholder="e.g. 7 days"></div>
            <div class="col-6"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" placeholder="e.g. 21"></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Special Instructions</label>
            <textarea name="instructions" class="form-control" rows="2" placeholder="e.g. Take after meals"></textarea>
          </div>
          <button type="submit" class="btn btn-success w-100"><i class="bi bi-floppy me-1"></i>Save Prescription</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-capsule me-2 text-primary"></i>Prescriptions</span>
        <div class="d-flex gap-1">
          <?php foreach ([''=>'All','Pending'=>'Pending','Dispensed'=>'Dispensed','Cancelled'=>'Cancelled'] as $val => $label): ?>
            <a href="?status=<?= urlencode($val) ?>" class="btn btn-xs <?= $statusFilter===$val ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $label ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Patient</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($prescriptions as $pr): ?>
            <tr>
              <td>
                <div class="fw-semibold small"><?= sanitize($pr['patient_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($pr['patient_code']) ?></div>
              </td>
              <td class="fw-semibold small"><?= sanitize($pr['medicine_name']) ?></td>
              <td class="small"><?= sanitize($pr['dosage']) ?></td>
              <td class="small"><?= sanitize($pr['frequency']) ?></td>
              <td><span class="badge bg-<?= $statusColors[$pr['status']] ?? 'secondary' ?>"><?= $pr['status'] ?></span></td>
              <td>
                <?php if ($pr['status'] === 'Pending'): ?>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="action" value="dispense">
                  <input type="hidden" name="presc_id" value="<?= $pr['id'] ?>">
                  <button type="submit" class="btn btn-xs btn-outline-success" title="Mark Dispensed"><i class="bi bi-check2-circle"></i></button>
                </form>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="action" value="cancel_presc">
                  <input type="hidden" name="presc_id" value="<?= $pr['id'] ?>">
                  <button type="submit" class="btn btn-xs btn-outline-danger" title="Cancel" onclick="return confirm('Cancel this prescription?')"><i class="bi bi-x-circle"></i></button>
                </form>
                <?php else: ?>
                  <span class="text-muted small"><?= formatDate($pr['prescribed_at'], 'M d') ?></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($prescriptions)): ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No prescriptions found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
