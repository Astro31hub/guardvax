<?php
// admin/medical_records.php — Admin view of all medical records
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('admin');

$search    = sanitize($_GET['q'] ?? '');
$typeFilter = sanitize($_GET['type'] ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 20;

$where  = '1=1';
$params = [];

if ($search) {
    $where .= ' AND (u.name LIKE ? OR p.patient_code LIKE ? OR mr.title LIKE ?)';
    $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%"]);
}
if ($typeFilter) {
    $where .= ' AND mr.record_type = ?';
    $params[] = $typeFilter;
}

$countStmt = db()->prepare(
    "SELECT COUNT(*) FROM medical_records mr
     JOIN patients p ON p.id=mr.patient_id
     JOIN users u ON u.id=p.user_id
     WHERE {$where}"
);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pager = paginate($total, $perPage, $page);

$stmt = db()->prepare(
    "SELECT mr.*, p.patient_code, u.name AS patient_name, rb.name AS recorded_by_name
     FROM medical_records mr
     JOIN patients p ON p.id=mr.patient_id
     JOIN users u ON u.id=p.user_id
     JOIN users rb ON rb.id=mr.recorded_by
     WHERE {$where}
     ORDER BY mr.record_date DESC, mr.id DESC
     LIMIT {$pager['per_page']} OFFSET {$pager['offset']}"
);
$stmt->execute($params);
$records = $stmt->fetchAll();

$recordTypes = ['Consultation','Lab Result','Diagnosis','Prescription','Other'];
$typeColors  = ['Consultation'=>'primary','Lab Result'=>'info','Diagnosis'=>'warning','Prescription'=>'success','Other'=>'secondary'];
?>
<?php renderHead('Medical Records'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('All Medical Records', 'View and search patient medical records system-wide', 'file-medical-fill'); ?>

<!-- Toolbar -->
<div class="card card-gvx mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-md-5">
        <label class="form-label small">Search patient / record title</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control" placeholder="Patient name, code, or record title…" value="<?= sanitize($search) ?>">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Record Type</label>
        <select name="type" class="form-select form-select-sm">
          <option value="">All Types</option>
          <?php foreach ($recordTypes as $t): ?>
            <option value="<?= $t ?>" <?= $typeFilter===$t?'selected':'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= SITE_URL ?>/admin/medical_records.php" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Records Table -->
<div class="card card-gvx">
  <div class="card-header-gvx">
    <i class="bi bi-file-medical-fill me-2 text-danger"></i>
    Medical Records <span class="badge bg-secondary ms-1"><?= $total ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>Patient</th><th>Type</th><th>Title</th><th>Description</th><th>Recorded By</th><th>Date</th>
      </tr></thead>
      <tbody>
        <?php foreach ($records as $rec): ?>
        <tr>
          <td>
            <div class="fw-semibold small"><?= sanitize($rec['patient_name']) ?></div>
            <div class="text-muted" style="font-size:.72rem"><?= sanitize($rec['patient_code']) ?></div>
          </td>
          <td>
            <span class="badge bg-<?= $typeColors[$rec['record_type']] ?? 'secondary' ?>">
              <?= sanitize($rec['record_type']) ?>
            </span>
          </td>
          <td class="fw-semibold"><?= sanitize($rec['title']) ?></td>
          <td style="max-width:240px">
            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#6c757d;font-size:.8rem"
                 title="<?= sanitize($rec['description']) ?>">
              <?= sanitize($rec['description']) ?>
            </div>
          </td>
          <td class="small"><?= sanitize($rec['recorded_by_name']) ?></td>
          <td class="small text-muted"><?= formatDate($rec['record_date']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
          <tr><td colspan="6" class="text-center py-4 text-muted">No medical records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pager['pages'] > 1): ?>
  <div class="card-body border-top">
    <nav><ul class="pagination pagination-sm mb-0">
      <?php for ($i=1;$i<=$pager['pages'];$i++): ?>
        <li class="page-item <?= $i===$page?'active':'' ?>">
          <a class="page-link" href="?page=<?=$i?>&q=<?=urlencode($search)?>&type=<?=urlencode($typeFilter)?>"><?=$i?></a>
        </li>
      <?php endfor; ?>
    </ul></nav>
  </div>
  <?php endif; ?>
</div>

<?php renderFooter(); ?>
