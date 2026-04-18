<?php
// admin/audit_logs.php — View Audit Trail
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('admin');

$search    = sanitize($_GET['q'] ?? '');
$dateFrom  = $_GET['date_from'] ?? '';
$dateTo    = $_GET['date_to'] ?? '';
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 25;

$where  = '1=1';
$params = [];

if ($search) {
    $where .= ' AND (al.action LIKE ? OR al.description LIKE ? OR u.name LIKE ?)';
    $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%"]);
}
if ($dateFrom) { $where .= ' AND DATE(al.created_at) >= ?'; $params[] = $dateFrom; }
if ($dateTo)   { $where .= ' AND DATE(al.created_at) <= ?'; $params[] = $dateTo; }

$total = (int) db()->prepare("SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id WHERE {$where}")
                   ->execute($params) ? db()->prepare("SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id WHERE {$where}")->execute($params) : 0;

// Re-execute for count
$countStmt = db()->prepare("SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id WHERE {$where}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pager = paginate($total, $perPage, $page);

$stmt = db()->prepare(
    "SELECT al.*, u.name AS user_name, r.name AS user_role
     FROM audit_logs al
     LEFT JOIN users u ON u.id = al.user_id
     LEFT JOIN roles r ON r.id = u.role_id
     WHERE {$where}
     ORDER BY al.created_at DESC
     LIMIT {$pager['per_page']} OFFSET {$pager['offset']}"
);
$stmt->execute($params);
$logs = $stmt->fetchAll();

$actionColors = [
    'LOGIN_SUCCESS'    => 'success',
    'LOGIN_FAIL'       => 'danger',
    'LOGOUT'           => 'secondary',
    'REGISTER'         => 'info',
    'USER_CREATED'     => 'primary',
    'USER_UPDATED'     => 'warning',
    'USER_STATUS_CHANGED'=> 'warning',
    'PATIENT_CREATED'  => 'primary',
    'PATIENT_UPDATED'  => 'warning',
    'VACCINATION_ADDED'=> 'success',
    'RECORD_ADDED'     => 'info',
    'PASSWORD_RESET'   => 'danger',
    'EMAIL_VERIFIED'   => 'success',
];
?>
<?php renderHead('Audit Logs'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Audit Logs', 'Complete trail of all system actions', 'journal-text'); ?>

<!-- Filter -->
<div class="card card-gvx mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="GET">
      <div class="col-12 col-md-4">
        <label class="form-label small">Search action / user</label>
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search…" value="<?= sanitize($search) ?>">
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label small">From</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= sanitize($dateFrom) ?>">
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label small">To</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= sanitize($dateTo) ?>">
      </div>
      <div class="col-12 col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="bi bi-search"></i> Filter</button>
        <a href="<?= SITE_URL ?>/admin/audit_logs.php" class="btn btn-sm btn-outline-secondary">✕</a>
      </div>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card card-gvx">
  <div class="card-header-gvx">
    <i class="bi bi-journal-text me-2 text-warning"></i>
    Audit Logs <span class="badge bg-secondary ms-1"><?= $total ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>#</th><th>Action</th><th>User</th><th>Table</th><th>Record ID</th><th>Description</th><th>IP</th><th>Time</th>
      </tr></thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
          <td class="text-muted small"><?= $log['id'] ?></td>
          <td>
            <?php $color = $actionColors[$log['action']] ?? 'secondary'; ?>
            <span class="badge bg-<?= $color ?>"><?= sanitize($log['action']) ?></span>
          </td>
          <td><?= sanitize($log['user_name'] ?? 'System') ?></td>
          <td><code class="small"><?= sanitize($log['table_name'] ?? '—') ?></code></td>
          <td><?= $log['record_id'] ?? '—' ?></td>
          <td class="small text-muted" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= sanitize($log['description'] ?? '') ?>">
            <?= sanitize($log['description'] ?? '—') ?>
          </td>
          <td><code class="small"><?= sanitize($log['ip_address'] ?? '—') ?></code></td>
          <td class="small text-muted"><?= formatDate($log['created_at'], 'M d, Y H:i') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
          <tr><td colspan="8" class="text-center py-4 text-muted">No logs found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pager['pages'] > 1): ?>
  <div class="card-body border-top">
    <nav><ul class="pagination pagination-sm mb-0">
      <?php for ($i = 1; $i <= $pager['pages']; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul></nav>
  </div>
  <?php endif; ?>
</div>

<?php renderFooter(); ?>
