<?php
// admin/patients.php — Admin Patient Overview
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('admin');

$search = sanitize($_GET['q'] ?? '');
$gender = sanitize($_GET['gender'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$where  = '1=1';
$params = [];
if ($search) {
    $where .= ' AND (u.name LIKE ? OR p.patient_code LIKE ? OR u.email LIKE ?)';
    $params = ["%{$search}%","%{$search}%","%{$search}%"];
}
if ($gender) { $where .= ' AND p.gender=?'; $params[] = $gender; }

$countStmt = db()->prepare("SELECT COUNT(*) FROM patients p JOIN users u ON u.id=p.user_id WHERE {$where}");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pager = paginate($total, $perPage, $page);

$stmt = db()->prepare(
    "SELECT p.*, u.name, u.email, u.status,
            (SELECT COUNT(*) FROM vaccinations v WHERE v.patient_id=p.id) AS vacc_count,
            (SELECT MAX(date_given) FROM vaccinations v WHERE v.patient_id=p.id) AS last_vacc,
            rn.name AS registered_by_name
     FROM patients p
     JOIN users u ON u.id=p.user_id
     LEFT JOIN users rn ON rn.id=p.registered_by
     WHERE {$where}
     ORDER BY p.created_at DESC
     LIMIT {$pager['per_page']} OFFSET {$pager['offset']}"
);
$stmt->execute($params);
$patients = $stmt->fetchAll();
?>
<?php renderHead('All Patients'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Patient Registry', 'View all patient records across the system', 'person-badge'); ?>

<!-- Toolbar -->
<div class="card card-gvx mb-3">
  <div class="card-body d-flex flex-wrap gap-2 align-items-center">
    <form class="d-flex gap-2 flex-grow-1 flex-wrap" method="GET">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Search name, ID, email…" value="<?= sanitize($search) ?>" style="max-width:260px">
      <select name="gender" class="form-select form-select-sm" style="max-width:130px">
        <option value="">All Genders</option>
        <option value="Male"   <?= $gender==='Male'  ?'selected':'' ?>>Male</option>
        <option value="Female" <?= $gender==='Female'?'selected':'' ?>>Female</option>
        <option value="Other"  <?= $gender==='Other' ?'selected':'' ?>>Other</option>
      </select>
      <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
      <a href="<?= SITE_URL ?>/admin/patients.php" class="btn btn-sm btn-outline-secondary">Reset</a>
    </form>
    <a href="<?= SITE_URL ?>/reports/patient_list.php" target="_blank" class="btn btn-sm btn-danger">
      <i class="bi bi-file-pdf me-1"></i>Export PDF
    </a>
  </div>
</div>

<div class="card card-gvx">
  <div class="card-header-gvx">
    <i class="bi bi-person-badge me-2 text-primary"></i>
    Patients <span class="badge bg-secondary ms-1"><?= $total ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>Code</th><th>Patient</th><th>Age / Gender</th><th>Blood</th>
        <th>Vaccinations</th><th>Last Vaccinated</th><th>Registered By</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach ($patients as $p): ?>
        <tr>
          <td><span class="badge bg-light text-dark border"><?= sanitize($p['patient_code']) ?></span></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm"><?= mb_strtoupper(mb_substr($p['name'],0,1)) ?></div>
              <div>
                <div class="fw-semibold"><?= sanitize($p['name']) ?></div>
                <div class="text-muted small"><?= sanitize($p['email']) ?></div>
              </div>
            </div>
          </td>
          <td><?= calculateAge($p['date_of_birth']) ?> yrs · <?= sanitize($p['gender']) ?></td>
          <td><?= sanitize($p['blood_type'] ?: '—') ?></td>
          <td>
            <?php if ($p['vacc_count'] > 0): ?>
              <span class="badge bg-success"><?= $p['vacc_count'] ?></span>
            <?php else: ?>
              <span class="badge bg-warning text-dark">None</span>
            <?php endif; ?>
          </td>
          <td class="small"><?= $p['last_vacc'] ? formatDate($p['last_vacc']) : 'Never' ?></td>
          <td class="small text-muted"><?= sanitize($p['registered_by_name'] ?? '—') ?></td>
          <td>
            <span class="badge <?= $p['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
              <?= ucfirst($p['status']) ?>
            </span>
          </td>
          <td>
            <a href="<?= SITE_URL ?>/nurse/vaccinations.php?patient_id=<?= $p['id'] ?>" class="btn btn-xs btn-outline-success" title="Vaccinations"><i class="bi bi-syringe"></i></a>
            <a href="<?= SITE_URL ?>/reports/patient_report.php?patient_id=<?= $p['id'] ?>" target="_blank" class="btn btn-xs btn-outline-danger" title="PDF"><i class="bi bi-file-pdf"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($patients)): ?>
          <tr><td colspan="9" class="text-center py-4 text-muted">No patients found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pager['pages'] > 1): ?>
  <div class="card-body border-top">
    <nav><ul class="pagination pagination-sm mb-0">
      <?php for ($i=1;$i<=$pager['pages'];$i++): ?>
        <li class="page-item <?= $i===$page?'active':'' ?>">
          <a class="page-link" href="?page=<?=$i?>&q=<?=urlencode($search)?>&gender=<?=$gender?>"><?=$i?></a>
        </li>
      <?php endfor; ?>
    </ul></nav>
  </div>
  <?php endif; ?>
</div>

<?php renderFooter(); ?>
