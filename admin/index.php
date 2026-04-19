<?php
// ============================================================
// admin/index.php — Admin Dashboard
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('admin');

// ── Stats ─────────────────────────────────────────────────────
$stats = [];
foreach ([
    'total_patients'      => 'SELECT COUNT(*) FROM patients',
    'total_nurses'        => 'SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE name="nurse") AND status="active"',
    'total_vaccinations'  => 'SELECT COUNT(*) FROM vaccinations',
    'pending_accounts'    => 'SELECT COUNT(*) FROM users WHERE status="pending"',
] as $key => $sql) {
    $stats[$key] = (int) db()->query($sql)->fetchColumn();
}

// Recent vaccinations
$recentVacc = db()->query(
    'SELECT v.date_given, p.patient_code, u.name AS patient_name, vk.name AS vaccine_name, v.dose_number
     FROM vaccinations v
     JOIN patients p ON p.id = v.patient_id
     JOIN users u ON u.id = p.user_id
     JOIN vaccines vk ON vk.id = v.vaccine_id
     ORDER BY v.date_given DESC, v.id DESC LIMIT 10'
)->fetchAll();

// Monthly vaccinations (last 6 months)
// Monthly vaccinations (last 6 months)
// Monthly vaccinations (last 6 months)
$monthlyData = db()->query(
    "SELECT DATE_FORMAT(date_given,'%b %Y') AS month_label,
            DATE_FORMAT(date_given,'%Y-%m') AS month_key,
            COUNT(*) AS cnt
     FROM vaccinations
     WHERE date_given >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
     GROUP BY month_key, month_label ORDER BY month_key ASC"
)->fetchAll();

// Vaccine distribution
$vaccDist = db()->query(
    'SELECT vk.name AS vaccine_name, COUNT(*) AS cnt
     FROM vaccinations v JOIN vaccines vk ON vk.id = v.vaccine_id
     GROUP BY vk.id ORDER BY cnt DESC LIMIT 8'
)->fetchAll();

// Recent audit logs
$recentLogs = db()->query(
    'SELECT al.*, u.name AS user_name FROM audit_logs al
     LEFT JOIN users u ON u.id = al.user_id
     ORDER BY al.created_at DESC LIMIT 8'
)->fetchAll();
?>
<?php renderHead('Admin Dashboard'); ?>
<?php renderNav($user); ?>

<?php renderPageHeader('Admin Dashboard', 'System overview and activity summary', 'speedometer2'); ?>

<!-- ── Stat Cards ── -->
<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['icon'=>'person-badge-fill','label'=>'Total Patients',  'value'=>$stats['total_patients'],     'color'=>'primary', 'link'=>'/admin/patients.php'],
    ['icon'=>'clipboard2-heart-fill','label'=>'Nurses',      'value'=>$stats['total_nurses'],       'color'=>'success', 'link'=>'/admin/users.php'],
    ['icon'=>'syringe',          'label'=>'Vaccinations',    'value'=>$stats['total_vaccinations'],  'color'=>'info',    'link'=>'/admin/reports.php'],
    ['icon'=>'hourglass-split',  'label'=>'Pending Accounts','value'=>$stats['pending_accounts'],   'color'=>'warning', 'link'=>'/admin/users.php?status=pending'],
  ];
  foreach ($cards as $c): ?>
  <div class="col-6 col-xl-3">
    <a href="<?= SITE_URL . $c['link'] ?>" class="stat-card stat-<?= $c['color'] ?> text-decoration-none">
      <div class="stat-icon"><i class="bi bi-<?= $c['icon'] ?>"></i></div>
      <div class="stat-body">
        <div class="stat-value"><?= number_format($c['value']) ?></div>
        <div class="stat-label"><?= $c['label'] ?></div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<!-- ── Charts Row ── -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Monthly Vaccinations (Last 6 Months)
      </div>
      <div class="card-body">
        <canvas id="monthlyChart" height="90"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-pie-chart-fill me-2 text-success"></i>Vaccine Distribution
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <canvas id="vaccDistChart" style="max-height:220px"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- ── Recent Activity ── -->
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center">
        <span><i class="bi bi-syringe me-2 text-primary"></i>Recent Vaccinations</span>
        <a href="<?= SITE_URL ?>/admin/reports.php" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr>
            <th>Patient</th><th>Vaccine</th><th>Dose</th><th>Date</th>
          </tr></thead>
          <tbody>
            <?php foreach ($recentVacc as $rv): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?= sanitize($rv['patient_name']) ?></div>
                <div class="text-muted small"><?= sanitize($rv['patient_code']) ?></div>
              </td>
              <td><?= sanitize($rv['vaccine_name']) ?></td>
              <td><span class="badge bg-primary">Dose <?= $rv['dose_number'] ?></span></td>
              <td><?= formatDate($rv['date_given']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentVacc)): ?>
            <tr><td colspan="4" class="text-center text-muted py-3">No vaccinations recorded yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text me-2 text-warning"></i>Recent Audit Logs</span>
        <a href="<?= SITE_URL ?>/admin/audit_logs.php" class="btn btn-sm btn-outline-warning">View All</a>
      </div>
      <div class="card-body p-0">
        <?php foreach ($recentLogs as $log): ?>
        <div class="audit-item">
          <div class="audit-action">
            <span class="audit-badge"><?= sanitize($log['action']) ?></span>
          </div>
          <div class="audit-meta">
            <span><?= sanitize($log['user_name'] ?? 'System') ?></span>
            <span class="text-muted small"><?= formatDate($log['created_at'], 'M d, H:i') ?></span>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($recentLogs)): ?>
        <div class="text-center text-muted py-3">No logs yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
<script>
// Monthly Bar Chart
const monthlyData = <?= json_encode(array_column($monthlyData, 'cnt')) ?>;
const monthlyLabels = <?= json_encode(array_column($monthlyData, 'month_label')) ?>;

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Vaccinations',
            data: monthlyData,
            backgroundColor: 'rgba(13,110,253,0.7)',
            borderColor: 'rgba(13,110,253,1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Doughnut Chart
const distLabels = <?= json_encode(array_column($vaccDist, 'vaccine_name')) ?>;
const distData   = <?= json_encode(array_column($vaccDist, 'cnt')) ?>;

new Chart(document.getElementById('vaccDistChart'), {
    type: 'doughnut',
    data: {
        labels: distLabels,
        datasets: [{
            data: distData,
            backgroundColor: ['#0d6efd','#198754','#0dcaf0','#ffc107','#dc3545','#6610f2','#fd7e14','#20c997'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
    }
});
</script>
