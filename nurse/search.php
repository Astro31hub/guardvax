<?php
// nurse/search.php — Advanced Patient Search
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('nurse', 'admin');

$q          = sanitize($_GET['q'] ?? '');
$gender     = sanitize($_GET['gender'] ?? '');
$vaccStatus = sanitize($_GET['vacc_status'] ?? '');
$dateFrom   = $_GET['date_from'] ?? '';
$dateTo     = $_GET['date_to'] ?? '';

$results = [];
$searched = false;

if ($q || $gender || $vaccStatus || $dateFrom || $dateTo) {
    $searched = true;
    $where    = '1=1';
    $params   = [];

    if ($q) {
        $where .= ' AND (u.name LIKE ? OR p.patient_code LIKE ? OR u.email LIKE ? OR p.phone LIKE ?)';
        $params = array_merge($params, ["%{$q}%","%{$q}%","%{$q}%","%{$q}%"]);
    }
    if ($gender) { $where .= ' AND p.gender = ?'; $params[] = $gender; }
    if ($dateFrom) { $where .= ' AND p.created_at >= ?'; $params[] = $dateFrom . ' 00:00:00'; }
    if ($dateTo)   { $where .= ' AND p.created_at <= ?'; $params[] = $dateTo . ' 23:59:59'; }

    $sql = "SELECT p.*, u.name, u.email,
            (SELECT COUNT(*) FROM vaccinations v WHERE v.patient_id = p.id) AS vacc_count,
            (SELECT MAX(date_given) FROM vaccinations v WHERE v.patient_id = p.id) AS last_vacc
            FROM patients p JOIN users u ON u.id = p.user_id
            WHERE {$where}
            ORDER BY u.name ASC LIMIT 50";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Filter by vaccination status (requires sub-query result)
    if ($vaccStatus === 'vaccinated')   $results = array_filter($results, fn($r) => $r['vacc_count'] > 0);
    if ($vaccStatus === 'unvaccinated') $results = array_filter($results, fn($r) => $r['vacc_count'] === '0');
}
?>
<?php renderHead('Patient Search'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Patient Search', 'Find patients by name, ID, or filters', 'search'); ?>

<!-- Search Form -->
<div class="card card-gvx mb-4">
  <div class="card-header-gvx"><i class="bi bi-funnel-fill me-2 text-primary"></i>Search & Filter</div>
  <div class="card-body">
    <form method="GET">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Search by Name, Code, Email, Phone</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Juan dela Cruz / GVX-000001" value="<?= sanitize($q) ?>">
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="">Any</option>
            <option value="Male"   <?= $gender==='Male'   ?'selected':'' ?>>Male</option>
            <option value="Female" <?= $gender==='Female' ?'selected':'' ?>>Female</option>
            <option value="Other"  <?= $gender==='Other'  ?'selected':'' ?>>Other</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Vaccination Status</label>
          <select name="vacc_status" class="form-select">
            <option value="">Any</option>
            <option value="vaccinated"   <?= $vaccStatus==='vaccinated'   ?'selected':'' ?>>Vaccinated</option>
            <option value="unvaccinated" <?= $vaccStatus==='unvaccinated' ?'selected':'' ?>>Unvaccinated</option>
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label">Reg. From</label>
          <input type="date" name="date_from" class="form-control" value="<?= sanitize($dateFrom) ?>">
        </div>
        <div class="col-md-1">
          <label class="form-label">To</label>
          <input type="date" name="date_to" class="form-control" value="<?= sanitize($dateTo) ?>">
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
          <a href="<?= SITE_URL ?>/nurse/search.php" class="btn btn-outline-secondary">Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Results -->
<?php if ($searched): ?>
<div class="card card-gvx">
  <div class="card-header-gvx">
    <i class="bi bi-list-ul me-2 text-primary"></i>
    Results <span class="badge bg-secondary ms-1"><?= count($results) ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>Code</th><th>Name</th><th>Age / Gender</th><th>Vaccinations</th><th>Last Vaccinated</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach ($results as $r): ?>
        <tr>
          <td><span class="badge bg-light text-dark border"><?= sanitize($r['patient_code']) ?></span></td>
          <td>
            <div class="fw-semibold"><?= sanitize($r['name']) ?></div>
            <div class="text-muted small"><?= sanitize($r['email']) ?></div>
          </td>
          <td><?= calculateAge($r['date_of_birth']) ?> yrs · <?= sanitize($r['gender']) ?></td>
          <td>
            <?php if ($r['vacc_count'] > 0): ?>
              <span class="badge bg-success"><?= $r['vacc_count'] ?> vaccine<?= $r['vacc_count'] > 1 ? 's' : '' ?></span>
            <?php else: ?>
              <span class="badge bg-warning text-dark">Unvaccinated</span>
            <?php endif; ?>
          </td>
          <td><?= $r['last_vacc'] ? formatDate($r['last_vacc']) : 'Never' ?></td>
          <td>
            <a href="<?= SITE_URL ?>/nurse/patients.php?edit=<?= $r['id'] ?>" class="btn btn-xs btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="<?= SITE_URL ?>/nurse/vaccinations.php?patient_id=<?= $r['id'] ?>" class="btn btn-xs btn-outline-success"><i class="bi bi-syringe"></i></a>
            <a href="<?= SITE_URL ?>/reports/patient_report.php?patient_id=<?= $r['id'] ?>" target="_blank" class="btn btn-xs btn-outline-danger"><i class="bi bi-file-pdf"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
          <tr><td colspan="6" class="text-center py-4 text-muted">No patients match your search.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted">
  <i class="bi bi-search" style="font-size:3rem;opacity:.3"></i>
  <p class="mt-3">Use the search form above to find patients.</p>
</div>
<?php endif; ?>

<?php renderFooter(); ?>
