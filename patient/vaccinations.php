<?php
// patient/vaccinations.php — Patient Vaccination History
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('patient');

$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]);
$patient = $stmt->fetch();

$vaccinations = [];
if ($patient) {
    $stmt = db()->prepare(
        'SELECT v.*, vk.name AS vaccine_name, vk.manufacturer, vk.doses_required,
                nu.name AS nurse_name
         FROM vaccinations v
         JOIN vaccines vk ON vk.id = v.vaccine_id
         JOIN users nu ON nu.id = v.administered_by
         WHERE v.patient_id = ?
         ORDER BY v.date_given DESC'
    );
    $stmt->execute([$patient['id']]);
    $vaccinations = $stmt->fetchAll();
}

// Group by vaccine for progress display
$byVaccine = [];
foreach ($vaccinations as $v) {
    $byVaccine[$v['vaccine_name']][] = $v;
}
?>
<?php renderHead('My Vaccinations'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Vaccination History', 'Complete record of your vaccinations', 'syringe'); ?>

<?php if (!$patient): ?>
  <div class="alert alert-warning">Patient profile not found. Please contact a nurse.</div>
<?php else: ?>

<?php if (empty($vaccinations)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-syringe" style="font-size:3rem;opacity:.3"></i>
    <p class="mt-3">No vaccination records found.</p>
  </div>
<?php else: ?>

<!-- Vaccine Progress Cards -->
<div class="row g-3 mb-4">
  <?php foreach ($byVaccine as $vaccineName => $doses): ?>
  <?php
    $totalRequired = (int)$doses[0]['doses_required'];
    $given         = count($doses);
    $pct           = min(100, round($given / max($totalRequired, 1) * 100));
    $complete      = $given >= $totalRequired;
  ?>
  <div class="col-md-4">
    <div class="card card-gvx h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h6 class="fw-bold mb-0 small"><?= sanitize($vaccineName) ?></h6>
          <span class="badge <?= $complete ? 'bg-success' : 'bg-warning text-dark' ?>">
            <?= $complete ? 'Complete' : 'In Progress' ?>
          </span>
        </div>
        <div class="text-muted small mb-2"><?= sanitize($doses[0]['manufacturer'] ?? '') ?></div>
        <div class="d-flex justify-content-between small mb-1">
          <span>Doses: <?= $given ?>/<?= $totalRequired ?></span>
          <span><?= $pct ?>%</span>
        </div>
        <div class="progress mb-2" style="height:6px">
          <div class="progress-bar <?= $complete ? 'bg-success' : 'bg-primary' ?>" style="width:<?= $pct ?>%"></div>
        </div>
        <div class="small text-muted">Last: <?= formatDate($doses[0]['date_given']) ?></div>
        <?php if (!$complete && !empty($doses[0]['next_dose_date'])): ?>
          <div class="small text-warning"><i class="bi bi-calendar me-1"></i>Next: <?= formatDate($doses[0]['next_dose_date']) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Detailed Table -->
<div class="card card-gvx">
  <div class="card-header-gvx d-flex justify-content-between align-items-center">
    <span><i class="bi bi-table me-2 text-primary"></i>Detailed Records</span>
    <a href="<?= SITE_URL ?>/reports/patient_report.php?patient_id=<?= $patient['id'] ?>" target="_blank" class="btn btn-sm btn-danger">
      <i class="bi bi-file-pdf me-1"></i>Download PDF
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>#</th><th>Vaccine</th><th>Dose</th><th>Date Given</th><th>Site</th><th>Batch</th><th>Next Dose</th><th>Administered By</th>
      </tr></thead>
      <tbody>
        <?php foreach ($vaccinations as $i => $v): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= sanitize($v['vaccine_name']) ?></td>
          <td><span class="badge bg-primary">Dose <?= $v['dose_number'] ?></span></td>
          <td><?= formatDate($v['date_given']) ?></td>
          <td><?= sanitize($v['site'] ?: '—') ?></td>
          <td><code class="small"><?= sanitize($v['batch_number'] ?: '—') ?></code></td>
          <td>
            <?php if ($v['next_dose_date']): ?>
              <span class="text-warning"><?= formatDate($v['next_dose_date']) ?></span>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <td class="text-muted small"><?= sanitize($v['nurse_name']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>

<?php renderFooter(); ?>
