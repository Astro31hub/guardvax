<?php
// nurse/vaccinations.php — Record & View Vaccinations
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('nurse', 'admin');

$errors = [];

// ── Record Vaccination ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'record_vaccination') {
    verifyCsrf();
    $patientId  = (int) ($_POST['patient_id'] ?? 0);
    $vaccineId  = (int) ($_POST['vaccine_id'] ?? 0);
    $doseNum    = (int) ($_POST['dose_number'] ?? 1);
    $dateGiven  = $_POST['date_given'] ?? '';
    $batch      = sanitize($_POST['batch_number'] ?? '');
    $site       = sanitize($_POST['site'] ?? '');
    $notes      = sanitize($_POST['notes'] ?? '');
    $nextDose   = $_POST['next_dose_date'] ?? null;

    if (!$patientId)  $errors[] = 'Please select a patient.';
    if (!$vaccineId)  $errors[] = 'Please select a vaccine.';
    if (!$dateGiven)  $errors[] = 'Date given is required.';
    if ($doseNum < 1) $errors[] = 'Invalid dose number.';

    if (empty($errors)) {
        $stmt = db()->prepare(
            'INSERT INTO vaccinations (patient_id, vaccine_id, dose_number, date_given, administered_by, batch_number, site, notes, next_dose_date)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([$patientId, $vaccineId, $doseNum, $dateGiven, $user['id'], $batch ?: null, $site ?: null, $notes ?: null, ($nextDose ?: null)]);
        $vid = (int) db()->lastInsertId();

        auditLog('VACCINATION_ADDED', 'vaccinations', $vid, "Vaccine ID {$vaccineId} — Patient ID {$patientId} — Dose {$doseNum}");
        setFlash('success', 'Vaccination record saved successfully.');
        header('Location: ' . SITE_URL . '/nurse/vaccinations.php?patient_id=' . $patientId);
        exit;
    }
}

// ── Delete vaccination ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_vaccination') {
    verifyCsrf();
    $vid = (int)($_POST['vacc_id'] ?? 0);
    if ($vid) {
        db()->prepare('DELETE FROM vaccinations WHERE id = ?')->execute([$vid]);
        auditLog('VACCINATION_DELETED', 'vaccinations', $vid);
        setFlash('success', 'Vaccination record removed.');
    }
    header('Location: ' . SITE_URL . '/nurse/vaccinations.php');
    exit;
}

// ── Filter by patient ─────────────────────────────────────────
$patientId = (int) ($_GET['patient_id'] ?? 0);
$search    = sanitize($_GET['q'] ?? '');

// Patients list for dropdown
$patients = db()->query(
    'SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name'
)->fetchAll();

// Vaccines
$vaccines = db()->query('SELECT * FROM vaccines WHERE is_active=1 ORDER BY name')->fetchAll();

// Vaccinations history
$where  = '1=1';
$params = [];
if ($patientId) { $where .= ' AND v.patient_id = ?'; $params[] = $patientId; }
if ($search)    { $where .= ' AND (u.name LIKE ? OR p.patient_code LIKE ? OR vk.name LIKE ?)'; $params = array_merge($params, ["%{$search}%","%{$search}%","%{$search}%"]); }

$vaccinations = db()->prepare(
    "SELECT v.*, p.patient_code, u.name AS patient_name, vk.name AS vaccine_name,
            nu.name AS nurse_name
     FROM vaccinations v
     JOIN patients p ON p.id = v.patient_id
     JOIN users u ON u.id = p.user_id
     JOIN vaccines vk ON vk.id = v.vaccine_id
     JOIN users nu ON nu.id = v.administered_by
     WHERE {$where}
     ORDER BY v.date_given DESC, v.id DESC LIMIT 100"
);
$vaccinations->execute($params);
$vaccinations = $vaccinations->fetchAll();

// Pre-select patient for form
$selectedPatient = null;
if ($patientId) {
    $s = db()->prepare('SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id=p.user_id WHERE p.id=?');
    $s->execute([$patientId]);
    $selectedPatient = $s->fetch();
}
?>
<?php renderHead('Vaccinations'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Vaccination Records', 'Record and view patient vaccinations', 'syringe'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<div class="row g-3">
  <!-- Record Form -->
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-plus-circle-fill me-2 text-success"></i>Record Vaccination</div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="record_vaccination">

          <div class="mb-3">
            <label class="form-label">Patient <span class="text-danger">*</span></label>
            <select name="patient_id" class="form-select" required id="patientSelect">
              <option value="">— Select Patient —</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $patientId == $p['id'] ? 'selected' : '' ?>>
                  <?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Vaccine <span class="text-danger">*</span></label>
            <select name="vaccine_id" class="form-select" required id="vaccineSelect">
              <option value="">— Select Vaccine —</option>
              <?php foreach ($vaccines as $v): ?>
                <option value="<?= $v['id'] ?>" data-doses="<?= $v['doses_required'] ?>" data-interval="<?= $v['interval_days'] ?>">
                  <?= sanitize($v['name']) ?> (<?= $v['doses_required'] ?> dose<?= $v['doses_required'] > 1 ? 's' : '' ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-5">
              <label class="form-label">Dose #</label>
              <input type="number" name="dose_number" id="doseNum" class="form-control" value="1" min="1" max="10" required>
            </div>
            <div class="col-7">
              <label class="form-label">Date Given <span class="text-danger">*</span></label>
              <input type="date" name="date_given" id="dateGiven" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Next Dose Date <small class="text-muted">(auto-calculated)</small></label>
            <input type="date" name="next_dose_date" id="nextDoseDate" class="form-control">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Batch #</label>
              <input type="text" name="batch_number" class="form-control" placeholder="Optional">
            </div>
            <div class="col-6">
              <label class="form-label">Site</label>
              <select name="site" class="form-select">
                <option value="">— Select —</option>
                <option>Left Arm</option>
                <option>Right Arm</option>
                <option>Left Thigh</option>
                <option>Right Thigh</option>
                <option>Gluteal</option>
                <option>Deltoid</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Any observations…"></textarea>
          </div>

          <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-syringe me-1"></i> Save Vaccination
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Records Table -->
  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <span><i class="bi bi-table me-2 text-primary"></i>Vaccination History</span>
          <form class="d-flex gap-2" method="GET">
            <select name="patient_id" class="form-select form-select-sm" style="max-width:220px" onchange="this.form.submit()">
              <option value="">All Patients</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $patientId == $p['id'] ? 'selected' : '' ?>>
                  <?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search…" value="<?= sanitize($search) ?>" style="max-width:140px">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr>
            <th>Patient</th><th>Vaccine</th><th>Dose</th><th>Date</th><th>Site</th><th>Nurse</th><th></th>
          </tr></thead>
          <tbody>
            <?php foreach ($vaccinations as $v): ?>
            <tr>
              <td>
                <div class="fw-semibold small"><?= sanitize($v['patient_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($v['patient_code']) ?></div>
              </td>
              <td><?= sanitize($v['vaccine_name']) ?></td>
              <td><span class="badge bg-primary">Dose <?= $v['dose_number'] ?></span></td>
              <td><?= formatDate($v['date_given']) ?></td>
              <td class="small"><?= sanitize($v['site'] ?: '—') ?></td>
              <td class="small"><?= sanitize($v['nurse_name']) ?></td>
              <td>
                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this vaccination record?')">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="action" value="delete_vaccination">
                  <input type="hidden" name="vacc_id" value="<?= $v['id'] ?>">
                  <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($vaccinations)): ?>
              <tr><td colspan="7" class="text-center py-4 text-muted">No vaccinations found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
<script>
// Auto-calculate next dose date
const vaccineSelect = document.getElementById('vaccineSelect');
const dateGiven     = document.getElementById('dateGiven');
const nextDoseDate  = document.getElementById('nextDoseDate');
const doseNum       = document.getElementById('doseNum');

function calcNextDose() {
    const opt      = vaccineSelect.options[vaccineSelect.selectedIndex];
    const interval = parseInt(opt.getAttribute('data-interval') || 0);
    const doses    = parseInt(opt.getAttribute('data-doses') || 1);
    const curDose  = parseInt(doseNum.value || 1);
    const date     = dateGiven.value;

    if (interval && date && curDose < doses) {
        const d = new Date(date);
        d.setDate(d.getDate() + interval);
        nextDoseDate.value = d.toISOString().split('T')[0];
    } else {
        nextDoseDate.value = '';
    }
}

vaccineSelect.addEventListener('change', calcNextDose);
dateGiven.addEventListener('change', calcNextDose);
doseNum.addEventListener('change', calcNextDose);
</script>
