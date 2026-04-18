<?php
// nurse/lab_results.php — Laboratory Results
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('nurse', 'admin');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'request_lab') {
        $patientId = (int)($_POST['patient_id'] ?? 0);
        $testName  = sanitize($_POST['test_name'] ?? '');
        $category  = $_POST['test_category'] ?? 'Other';
        $date      = $_POST['requested_date'] ?? date('Y-m-d');
        $notes     = sanitize($_POST['notes'] ?? '');

        if (!$patientId)          $errors[] = 'Patient required.';
        if (strlen($testName) < 2)$errors[] = 'Test name required.';

        if (empty($errors)) {
            $stmt = db()->prepare(
                'INSERT INTO lab_results (patient_id, requested_by, test_name, test_category, requested_date, notes, status)
                 VALUES (?,?,?,?,?,?,"Requested")'
            );
            $stmt->execute([$patientId, $user['id'], $testName, $category, $date, $notes]);
            $lid = (int)db()->lastInsertId();
            auditLog('LAB_REQUESTED', 'lab_results', $lid, "$testName for Patient ID $patientId");
            setFlash('success', 'Lab test requested.');
            header('Location: ' . SITE_URL . '/nurse/lab_results.php');
            exit;
        }
    }

    if ($action === 'enter_result') {
        $lid        = (int)($_POST['lab_id'] ?? 0);
        $result     = sanitize($_POST['result'] ?? '');
        $normalRange= sanitize($_POST['normal_range'] ?? '');
        $unit       = sanitize($_POST['unit'] ?? '');
        $isAbnormal = isset($_POST['is_abnormal']) ? 1 : 0;
        $resultDate = $_POST['result_date'] ?? date('Y-m-d');

        $stmt = db()->prepare(
            'UPDATE lab_results SET result=?, normal_range=?, unit=?, is_abnormal=?,
             result_date=?, status="Completed", performed_by=? WHERE id=?'
        );
        $stmt->execute([$result, $normalRange, $unit, $isAbnormal, $resultDate, $user['id'], $lid]);
        auditLog('LAB_RESULT_ENTERED', 'lab_results', $lid, "Result entered");
        setFlash('success', 'Lab result saved.');
        header('Location: ' . SITE_URL . '/nurse/lab_results.php');
        exit;
    }
}

$statusFilter = sanitize($_GET['status'] ?? '');
$where  = '1=1';
$params = [];
if ($statusFilter) { $where .= ' AND lr.status=?'; $params[] = $statusFilter; }

$labs = db()->prepare(
    "SELECT lr.*, p.patient_code, u.name AS patient_name,
            doc.name AS requested_by_name
     FROM lab_results lr
     JOIN patients p  ON p.id  = lr.patient_id
     JOIN users u     ON u.id  = p.user_id
     JOIN users doc   ON doc.id = lr.requested_by
     WHERE {$where}
     ORDER BY lr.requested_date DESC, lr.id DESC LIMIT 100"
);
$labs->execute($params);
$labs = $labs->fetchAll();

$patients = db()->query('SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name')->fetchAll();
$categories = ['Blood','Urine','Imaging','Microbiology','Chemistry','Other'];
$pending = (int)db()->query("SELECT COUNT(*) FROM lab_results WHERE status='Requested' OR status='Processing'")->fetchColumn();
$statusColors = ['Requested'=>'warning','Processing'=>'info','Completed'=>'success','Cancelled'=>'danger'];

// Find lab needing result entry
$enterResult = null;
if (isset($_GET['enter'])) {
    $s = db()->prepare('SELECT * FROM lab_results WHERE id=?');
    $s->execute([(int)$_GET['enter']]);
    $enterResult = $s->fetch();
}
?>
<?php renderHead('Lab Results'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Laboratory Results', 'Request and manage patient lab tests', 'eyedropper'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<?php if ($pending > 0): ?>
<div class="alert alert-info d-flex align-items-center gap-2 mb-3">
  <i class="bi bi-flask-fill"></i>
  <strong><?= $pending ?> test<?= $pending > 1 ? 's' : '' ?> awaiting results.</strong>
</div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <?php if ($enterResult): ?>
    <!-- Enter Result Form -->
    <div class="card card-gvx mb-3">
      <div class="card-header-gvx"><i class="bi bi-clipboard2-pulse me-2 text-success"></i>Enter Result — <?= sanitize($enterResult['test_name']) ?></div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="enter_result">
          <input type="hidden" name="lab_id" value="<?= $enterResult['id'] ?>">
          <div class="mb-3"><label class="form-label">Result <span class="text-danger">*</span></label><textarea name="result" class="form-control" rows="3" required placeholder="Enter test result..."></textarea></div>
          <div class="row g-2 mb-3">
            <div class="col-7"><label class="form-label">Normal Range</label><input type="text" name="normal_range" class="form-control" placeholder="e.g. 4.5-11.0"></div>
            <div class="col-5"><label class="form-label">Unit</label><input type="text" name="unit" class="form-control" placeholder="e.g. g/dL"></div>
          </div>
          <div class="mb-3"><label class="form-label">Result Date</label><input type="date" name="result_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
          <div class="mb-3 form-check"><input type="checkbox" class="form-check-input" name="is_abnormal" id="abnormal"><label class="form-check-label text-danger" for="abnormal">Mark as Abnormal</label></div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success flex-grow-1"><i class="bi bi-check2 me-1"></i>Save Result</button>
            <a href="<?= SITE_URL ?>/nurse/lab_results.php" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
    <?php else: ?>
    <!-- Request Lab Form -->
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Request Lab Test</div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="request_lab">
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
            <label class="form-label">Test Name <span class="text-danger">*</span></label>
            <input type="text" name="test_name" class="form-control" required placeholder="e.g. Complete Blood Count">
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="test_category" class="form-select">
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c ?>"><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Requested Date</label><input type="date" name="requested_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
          <div class="mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-flask me-1"></i>Request Test</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-eyedropper me-2 text-primary"></i>Lab Tests</span>
        <div class="d-flex gap-1">
          <?php foreach ([''=>'All','Requested'=>'Pending','Completed'=>'Completed'] as $val => $label): ?>
            <a href="?status=<?= urlencode($val) ?>" class="btn btn-xs <?= $statusFilter===$val?'btn-primary':'btn-outline-secondary' ?>"><?= $label ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Patient</th><th>Test</th><th>Category</th><th>Result</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($labs as $l): ?>
            <tr>
              <td>
                <div class="fw-semibold small"><?= sanitize($l['patient_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($l['patient_code']) ?></div>
              </td>
              <td class="fw-semibold small"><?= sanitize($l['test_name']) ?></td>
              <td><span class="badge bg-secondary small"><?= sanitize($l['test_category']) ?></span></td>
              <td class="small">
                <?php if ($l['result']): ?>
                  <span class="<?= $l['is_abnormal'] ? 'text-danger fw-bold' : 'text-success' ?>">
                    <?= sanitize(substr($l['result'], 0, 30)) . (strlen($l['result']) > 30 ? '…' : '') ?>
                    <?= $l['is_abnormal'] ? ' ⚠️' : '' ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted">Pending</span>
                <?php endif; ?>
              </td>
              <td><span class="badge bg-<?= $statusColors[$l['status']] ?? 'secondary' ?>"><?= $l['status'] ?></span></td>
              <td>
                <?php if ($l['status'] === 'Requested' || $l['status'] === 'Processing'): ?>
                  <a href="?enter=<?= $l['id'] ?>" class="btn btn-xs btn-outline-success"><i class="bi bi-pencil-square"></i> Result</a>
                <?php else: ?>
                  <span class="text-muted small"><?= formatDate($l['result_date'] ?? $l['requested_date']) ?></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($labs)): ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No lab tests found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
