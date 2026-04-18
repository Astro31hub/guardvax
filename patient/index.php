<?php
// patient/index.php — Patient Dashboard
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('patient');

// Get patient record
$stmt = db()->prepare('SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id=p.user_id WHERE p.user_id=?');
$stmt->execute([$user['id']]);
$patient = $stmt->fetch();

if (!$patient) {
    // Patient account exists but no profile yet
    setFlash('warning', 'Your patient profile is not fully set up yet. <a href="http://localhost/guardvax/contact.php" class="alert-link">Contact the nurse station</a> to complete your registration.');
}

$patientId = $patient['id'] ?? 0;

// Stats
$vaccCount   = $patientId ? (int) db()->prepare('SELECT COUNT(*) FROM vaccinations WHERE patient_id=?')->execute([$patientId]) : 0;
$recordCount = $patientId ? (int) db()->prepare('SELECT COUNT(*) FROM medical_records WHERE patient_id=?')->execute([$patientId]) : 0;

// Fix stats
if ($patientId) {
    $s = db()->prepare('SELECT COUNT(*) FROM vaccinations WHERE patient_id=?'); $s->execute([$patientId]); $vaccCount = (int)$s->fetchColumn();
    $s = db()->prepare('SELECT COUNT(*) FROM medical_records WHERE patient_id=?'); $s->execute([$patientId]); $recordCount = (int)$s->fetchColumn();
}

// Recent vaccinations
$recentVacc = [];
if ($patientId) {
    $stmt = db()->prepare(
        'SELECT v.*, vk.name AS vaccine_name, nu.name AS nurse_name
         FROM vaccinations v
         JOIN vaccines vk ON vk.id=v.vaccine_id
         JOIN users nu ON nu.id=v.administered_by
         WHERE v.patient_id=?
         ORDER BY v.date_given DESC LIMIT 5'
    );
    $stmt->execute([$patientId]);
    $recentVacc = $stmt->fetchAll();
}

// Upcoming doses
$upcomingDoses = [];
if ($patientId) {
    $stmt = db()->prepare(
        'SELECT v.next_dose_date, vk.name AS vaccine_name, v.dose_number
         FROM vaccinations v JOIN vaccines vk ON vk.id=v.vaccine_id
         WHERE v.patient_id=? AND v.next_dose_date >= CURDATE()
         ORDER BY v.next_dose_date ASC LIMIT 5'
    );
    $stmt->execute([$patientId]);
    $upcomingDoses = $stmt->fetchAll();
}

// Next dose alert
$nextDoseAlert = null;
if (!empty($upcomingDoses)) {
    $days = (int) date_diff(date_create('today'), date_create($upcomingDoses[0]['next_dose_date']))->days;
    $nextDoseAlert = ['days' => $days, 'vaccine' => $upcomingDoses[0]['vaccine_name'], 'date' => $upcomingDoses[0]['next_dose_date']];
}
?>
<?php renderHead('My Dashboard'); ?>
<?php renderNav($user); ?>

<?php if ($patient): ?>
  <!-- Welcome Banner -->
  <div class="welcome-banner mb-4">
    <div class="welcome-avatar"><?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?></div>
    <div>
      <h2 class="welcome-name">Hello, <?= sanitize(explode(' ', $user['name'])[0]) ?>! 👋</h2>
      <p class="welcome-sub mb-0">
        Patient Code: <strong><?= sanitize($patient['patient_code']) ?></strong>
        &bull; <?= sanitize($patient['gender']) ?> &bull; Age <?= calculateAge($patient['date_of_birth']) ?>
      </p>
    </div>
    <div class="ms-auto d-none d-md-flex gap-2">
      <a href="<?= SITE_URL ?>/reports/patient_report.php?patient_id=<?= $patient['id'] ?>" target="_blank" class="btn btn-outline-light btn-sm">
        <i class="bi bi-file-pdf me-1"></i> Download Report
      </a>
    </div>
  </div>
<?php endif; ?>

<?php echo renderFlash(); ?>

<!-- Next Dose Alert -->
<?php if ($nextDoseAlert): ?>
<div class="alert alert-info d-flex align-items-center gap-3 mb-4">
  <i class="bi bi-calendar-check-fill fs-4"></i>
  <div>
    <strong>Upcoming Dose Reminder:</strong>
    Your next <strong><?= sanitize($nextDoseAlert['vaccine']) ?></strong> dose is due on
    <strong><?= formatDate($nextDoseAlert['date']) ?></strong>
    (<?= $nextDoseAlert['days'] === 0 ? '<span class="text-danger">Today!</span>' : "in {$nextDoseAlert['days']} day(s)" ?>).
  </div>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card stat-primary">
      <div class="stat-icon"><i class="bi bi-syringe"></i></div>
      <div class="stat-body"><div class="stat-value"><?= $vaccCount ?></div><div class="stat-label">Vaccinations</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card stat-success">
      <div class="stat-icon"><i class="bi bi-file-medical"></i></div>
      <div class="stat-body"><div class="stat-value"><?= $recordCount ?></div><div class="stat-label">Medical Records</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card stat-info">
      <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
      <div class="stat-body"><div class="stat-value"><?= count($upcomingDoses) ?></div><div class="stat-label">Upcoming Doses</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card stat-warning">
      <div class="stat-icon"><i class="bi bi-droplet-fill"></i></div>
      <div class="stat-body"><div class="stat-value"><?= sanitize($patient['blood_type'] ?: 'N/A') ?></div><div class="stat-label">Blood Type</div></div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Recent Vaccinations -->
  <div class="col-lg-7">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center">
        <span><i class="bi bi-syringe me-2 text-primary"></i>Recent Vaccinations</span>
        <a href="<?= SITE_URL ?>/patient/vaccinations.php" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Vaccine</th><th>Dose</th><th>Date</th><th>By</th></tr></thead>
          <tbody>
            <?php foreach ($recentVacc as $v): ?>
            <tr>
              <td class="fw-semibold"><?= sanitize($v['vaccine_name']) ?></td>
              <td><span class="badge bg-primary">Dose <?= $v['dose_number'] ?></span></td>
              <td><?= formatDate($v['date_given']) ?></td>
              <td class="text-muted small"><?= sanitize($v['nurse_name']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentVacc)): ?>
              <tr><td colspan="4" class="text-center py-3 text-muted">No vaccination records yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Profile Summary + Upcoming -->
  <div class="col-lg-5">
    <?php if ($patient): ?>
    <div class="card card-gvx mb-3">
      <div class="card-header-gvx d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-circle me-2 text-success"></i>Profile Summary</span>
        <a href="<?= SITE_URL ?>/patient/profile.php" class="btn btn-sm btn-outline-success">Edit</a>
      </div>
      <div class="card-body">
        <dl class="row mb-0 small">
          <dt class="col-5 text-muted">Full Name</dt>
          <dd class="col-7"><?= sanitize($patient['name']) ?></dd>
          <dt class="col-5 text-muted">Date of Birth</dt>
          <dd class="col-7"><?= formatDate($patient['date_of_birth']) ?></dd>
          <dt class="col-5 text-muted">Phone</dt>
          <dd class="col-7"><?= sanitize($patient['phone'] ?: '—') ?></dd>
          <dt class="col-5 text-muted">Allergies</dt>
          <dd class="col-7"><?= sanitize($patient['allergies'] ?: 'None') ?></dd>
          <dt class="col-5 text-muted">Emergency Contact</dt>
          <dd class="col-7"><?= sanitize($patient['emergency_contact_name'] ?: '—') ?></dd>
        </dl>
      </div>
    </div>
    <?php endif; ?>

    <!-- Upcoming Doses -->
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-calendar-plus me-2 text-warning"></i>Upcoming Doses</div>
      <ul class="list-group list-group-flush">
        <?php foreach ($upcomingDoses as $ud): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
          <div>
            <div class="fw-semibold small"><?= sanitize($ud['vaccine_name']) ?></div>
            <div class="text-muted" style="font-size:.72rem">Dose <?= $ud['dose_number'] + 1 ?></div>
          </div>
          <span class="badge bg-warning text-dark"><?= formatDate($ud['next_dose_date']) ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($upcomingDoses)): ?>
          <li class="list-group-item text-muted text-center small py-3">No upcoming doses scheduled.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
