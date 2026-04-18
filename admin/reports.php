<?php
// admin/reports.php — Report Generation Hub
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('admin');

// Stats for summary card
$totalPatients     = (int) db()->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$totalVaccinations = (int) db()->query('SELECT COUNT(*) FROM vaccinations')->fetchColumn();
$totalThisMonth    = (int) db()->query("SELECT COUNT(*) FROM vaccinations WHERE MONTH(date_given)=MONTH(NOW()) AND YEAR(date_given)=YEAR(NOW())")->fetchColumn();

// All patients for individual report
$patients = db()->query(
    'SELECT p.id, p.patient_code, u.name, p.date_of_birth, p.gender
     FROM patients p JOIN users u ON u.id = p.user_id
     ORDER BY u.name ASC'
)->fetchAll();
?>
<?php renderHead('Reports'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Reports', 'Generate and export system reports as PDF', 'file-earmark-bar-graph'); ?>

<!-- ── Summary Cards ── -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card stat-primary">
      <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      <div class="stat-body">
        <div class="stat-value"><?= $totalPatients ?></div>
        <div class="stat-label">Total Patients</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card stat-success">
      <div class="stat-icon"><i class="bi bi-syringe"></i></div>
      <div class="stat-body">
        <div class="stat-value"><?= $totalVaccinations ?></div>
        <div class="stat-label">Total Vaccinations</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card stat-info">
      <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
      <div class="stat-body">
        <div class="stat-value"><?= $totalThisMonth ?></div>
        <div class="stat-label">This Month</div>
      </div>
    </div>
  </div>
</div>

<!-- ── Report Options ── -->
<div class="row g-3">
  <!-- Full Summary Report -->
  <div class="col-md-6">
    <div class="card card-gvx h-100">
      <div class="card-header-gvx">
        <i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i>Vaccination Summary Report
      </div>
      <div class="card-body">
        <p class="text-muted mb-3">Generate a full PDF report of all vaccinations administered, grouped by vaccine type with date filters.</p>
        <form method="GET" action="<?= SITE_URL ?>/reports/vaccination_summary.php" target="_blank">
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small">Date From</label>
              <input type="date" name="date_from" class="form-control form-control-sm">
            </div>
            <div class="col-6">
              <label class="form-label small">Date To</label>
              <input type="date" name="date_to" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-danger w-100">
            <i class="bi bi-file-pdf me-1"></i> Generate Summary PDF
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Individual Patient Report -->
  <div class="col-md-6">
    <div class="card card-gvx h-100">
      <div class="card-header-gvx">
        <i class="bi bi-person-vcard-fill me-2 text-primary"></i>Individual Patient Report
      </div>
      <div class="card-body">
        <p class="text-muted mb-3">Generate a personal vaccination and medical record report for a specific patient.</p>
        <form method="GET" action="<?= SITE_URL ?>/reports/patient_report.php" target="_blank">
          <div class="mb-3">
            <label class="form-label small">Select Patient</label>
            <select name="patient_id" class="form-select form-select-sm" required>
              <option value="">— Choose Patient —</option>
              <?php foreach ($patients as $p): ?>
                <option value="<?= $p['id'] ?>"><?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-file-person me-1"></i> Generate Patient PDF
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Patient List Report -->
  <div class="col-12">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-table me-2 text-success"></i>Patient List Report
      </div>
      <div class="card-body">
        <p class="text-muted mb-0 d-inline">Export a full list of all registered patients with demographic information.</p>
        <a href="<?= SITE_URL ?>/reports/patient_list.php" target="_blank" class="btn btn-success btn-sm ms-3">
          <i class="bi bi-download me-1"></i> Download Patient List PDF
        </a>
      </div>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
