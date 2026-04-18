<?php
// nurse/medical_records.php — Add & Manage Medical Records
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('nurse', 'admin');
$errors = [];

// ── Add Record ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_record') {
    verifyCsrf();
    $patientId  = (int)($_POST['patient_id'] ?? 0);
    $type       = $_POST['record_type'] ?? '';
    $title      = sanitize($_POST['title'] ?? '');
    $desc       = sanitize($_POST['description'] ?? '');
    $recordDate = $_POST['record_date'] ?? date('Y-m-d');

    $validTypes = ['Consultation','Lab Result','Diagnosis','Prescription','Other'];
    if (!$patientId)          $errors[] = 'Patient required.';
    if (!in_array($type, $validTypes)) $errors[] = 'Invalid record type.';
    if (strlen($title) < 2)   $errors[] = 'Title required.';
    if (strlen($desc) < 5)    $errors[] = 'Description required.';

    if (empty($errors)) {
        $stmt = db()->prepare(
            'INSERT INTO medical_records (patient_id, record_type, title, description, recorded_by, record_date)
             VALUES (?,?,?,?,?,?)'
        );
        $stmt->execute([$patientId, $type, $title, $desc, $user['id'], $recordDate]);
        $rid = (int) db()->lastInsertId();
        auditLog('RECORD_ADDED', 'medical_records', $rid, "Type: {$type} for patient ID {$patientId}");
        setFlash('success', 'Medical record added successfully.');
        header('Location: ' . SITE_URL . '/nurse/medical_records.php?patient_id=' . $patientId);
        exit;
    }
}

// ── Delete Record ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_record') {
    verifyCsrf();
    $rid = (int)($_POST['record_id'] ?? 0);
    if ($rid) {
        db()->prepare('DELETE FROM medical_records WHERE id=?')->execute([$rid]);
        auditLog('RECORD_DELETED', 'medical_records', $rid);
        setFlash('success', 'Record deleted.');
    }
    header('Location: ' . SITE_URL . '/nurse/medical_records.php');
    exit;
}

$patientId = (int)($_GET['patient_id'] ?? 0);
$patients  = db()->query('SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name')->fetchAll();

$records = [];
$selectedPatient = null;
if ($patientId) {
    $s = db()->prepare('SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id=p.user_id WHERE p.id=?');
    $s->execute([$patientId]);
    $selectedPatient = $s->fetch();

    $s = db()->prepare(
        'SELECT mr.*, u.name AS recorded_by_name FROM medical_records mr JOIN users u ON u.id=mr.recorded_by
         WHERE mr.patient_id=? ORDER BY mr.record_date DESC'
    );
    $s->execute([$patientId]);
    $records = $s->fetchAll();
}
$typeColors = ['Consultation'=>'primary','Lab Result'=>'info','Diagnosis'=>'warning','Prescription'=>'success','Other'=>'secondary'];
?>
<?php renderHead('Medical Records'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Medical Records', 'Add and view patient health records', 'file-medical-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<div class="row g-3">
  <!-- Add Record Form -->
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-plus-circle-fill me-2 text-success"></i>Add Medical Record</div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="add_record">

          <div class="mb-3">
            <label class="form-label">Patient <span class="text-danger">*</span></label>
            <select name="patient_id" class="form-select" required>
              <option value="">— Select Patient —</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $patientId==$p['id']?'selected':'' ?>>
                  <?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Record Type <span class="text-danger">*</span></label>
            <select name="record_type" class="form-select" required>
              <?php foreach (['Consultation','Lab Result','Diagnosis','Prescription','Other'] as $t): ?>
                <option value="<?= $t ?>"><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Annual Checkup" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="record_date" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Description / Notes <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="4" placeholder="Enter findings, results, or notes…" required></textarea>
          </div>

          <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-floppy me-1"></i> Save Record
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Records View -->
  <div class="col-lg-8">
    <?php if ($selectedPatient): ?>
    <div class="card card-gvx mb-3">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div class="avatar-sm" style="width:40px;height:40px;font-size:1rem"><?= mb_strtoupper(mb_substr($selectedPatient['name'],0,1)) ?></div>
        <div>
          <div class="fw-bold"><?= sanitize($selectedPatient['name']) ?></div>
          <div class="text-muted small"><?= sanitize($selectedPatient['patient_code']) ?> &bull; <?= sanitize($selectedPatient['email']) ?></div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($patientId && empty($records)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-file-medical" style="font-size:2.5rem;opacity:.3"></i>
        <p class="mt-2">No records for this patient yet.</p>
      </div>
    <?php elseif (!$patientId): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-arrow-left-circle" style="font-size:2.5rem;opacity:.3"></i>
        <p class="mt-2">Select a patient from the form to view their records.</p>
      </div>
    <?php else: ?>
      <?php foreach ($records as $rec): ?>
      <div class="card card-gvx mb-2">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
              <span class="badge bg-<?= $typeColors[$rec['record_type']] ?? 'secondary' ?> me-2"><?= sanitize($rec['record_type']) ?></span>
              <strong><?= sanitize($rec['title']) ?></strong>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted small">
              <span><i class="bi bi-calendar2 me-1"></i><?= formatDate($rec['record_date']) ?></span>
              <span>·</span>
              <span><?= sanitize($rec['recorded_by_name']) ?></span>
              <form method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="delete_record">
                <input type="hidden" name="record_id" value="<?= $rec['id'] ?>">
                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </div>
          <p class="mb-0 mt-2 small text-muted" style="white-space:pre-line"><?= sanitize($rec['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php renderFooter(); ?>
