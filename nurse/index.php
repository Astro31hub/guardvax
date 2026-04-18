<?php
// nurse/index.php — Nurse Dashboard
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('nurse');

$stats = [
    'my_patients'     => (int) db()->prepare('SELECT COUNT(*) FROM patients WHERE registered_by = ?')->execute([$user['id']]) ? 0 : 0,
    'total_patients'  => (int) db()->query('SELECT COUNT(*) FROM patients')->fetchColumn(),
    'my_vaccinations' => (int) db()->prepare('SELECT COUNT(*) FROM vaccinations WHERE administered_by = ?')->execute([$user['id']]) ? 0 : 0,
    'today_vacc'      => (int) db()->query("SELECT COUNT(*) FROM vaccinations WHERE date_given = CURDATE()")->fetchColumn(),
];

// Fix stats with proper queries
$s = db()->prepare('SELECT COUNT(*) FROM patients WHERE registered_by = ?');
$s->execute([$user['id']]); $stats['my_patients'] = (int)$s->fetchColumn();

$s = db()->prepare('SELECT COUNT(*) FROM vaccinations WHERE administered_by = ?');
$s->execute([$user['id']]); $stats['my_vaccinations'] = (int)$s->fetchColumn();

// Today's vaccinations
$todayVacc = db()->query(
    "SELECT v.*, p.patient_code, u.name AS patient_name, vk.name AS vaccine_name
     FROM vaccinations v
     JOIN patients p ON p.id = v.patient_id
     JOIN users u ON u.id = p.user_id
     JOIN vaccines vk ON vk.id = v.vaccine_id
     WHERE v.date_given = CURDATE()
     ORDER BY v.id DESC LIMIT 10"
)->fetchAll();

// Recent patients
$recentPatients = db()->query(
    'SELECT p.*, u.name, u.email FROM patients p JOIN users u ON u.id = p.user_id
     ORDER BY p.created_at DESC LIMIT 5'
)->fetchAll();
?>
<?php renderHead('Nurse Dashboard'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Nurse Dashboard', "Welcome back, {$user['name']}", 'clipboard2-heart-fill'); ?>

<?php echo renderFlash(); ?>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-primary">
      <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      <div class="stat-body"><div class="stat-value"><?= $stats['total_patients'] ?></div><div class="stat-label">Total Patients</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-success">
      <div class="stat-icon"><i class="bi bi-person-plus-fill"></i></div>
      <div class="stat-body"><div class="stat-value"><?= $stats['my_patients'] ?></div><div class="stat-label">My Patients</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-info">
      <div class="stat-icon"><i class="bi bi-syringe"></i></div>
      <div class="stat-body"><div class="stat-value"><?= $stats['my_vaccinations'] ?></div><div class="stat-label">My Vaccinations</div></div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card stat-warning">
      <div class="stat-icon"><i class="bi bi-calendar-day"></i></div>
      <div class="stat-body"><div class="stat-value"><?= $stats['today_vacc'] ?></div><div class="stat-label">Today's Vaccinations</div></div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-lightning-fill me-2 text-warning"></i>Quick Actions</div>
      <div class="card-body d-flex flex-wrap gap-2">
        <a href="<?= SITE_URL ?>/nurse/patients.php?action=add" class="btn btn-primary">
          <i class="bi bi-person-plus-fill me-1"></i> Register Patient
        </a>
        <a href="<?= SITE_URL ?>/nurse/vaccinations.php?action=add" class="btn btn-success">
          <i class="bi bi-syringe me-1"></i> Record Vaccination
        </a>
        <a href="<?= SITE_URL ?>/nurse/search.php" class="btn btn-outline-primary">
          <i class="bi bi-search me-1"></i> Search Patients
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Today's Vaccinations -->
  <div class="col-lg-7">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-calendar-check me-2 text-success"></i>Today's Vaccinations</div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Patient</th><th>Vaccine</th><th>Dose</th></tr></thead>
          <tbody>
            <?php foreach ($todayVacc as $v): ?>
            <tr>
              <td><div class="fw-semibold"><?= sanitize($v['patient_name']) ?></div><div class="text-muted small"><?= sanitize($v['patient_code']) ?></div></td>
              <td><?= sanitize($v['vaccine_name']) ?></td>
              <td><span class="badge bg-success">Dose <?= $v['dose_number'] ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($todayVacc)): ?>
              <tr><td colspan="3" class="text-center py-3 text-muted">No vaccinations today.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Patients -->
  <div class="col-lg-5">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2 text-primary"></i>Recent Patients</span>
        <a href="<?= SITE_URL ?>/nurse/patients.php" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <ul class="list-group list-group-flush">
        <?php foreach ($recentPatients as $p): ?>
        <li class="list-group-item d-flex align-items-center gap-2 py-2">
          <div class="avatar-sm"><?= mb_strtoupper(mb_substr($p['name'], 0, 1)) ?></div>
          <div>
            <div class="fw-semibold small"><?= sanitize($p['name']) ?></div>
            <div class="text-muted" style="font-size:.75rem"><?= sanitize($p['patient_code']) ?> · <?= $p['gender'] ?></div>
          </div>
        </li>
        <?php endforeach; ?>
        <?php if (empty($recentPatients)): ?>
          <li class="list-group-item text-muted text-center">No patients yet.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
