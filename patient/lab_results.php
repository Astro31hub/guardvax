<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
$user = guard('patient');
$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]); $patient = $stmt->fetch();
$labs = [];
if ($patient) {
    $s = db()->prepare("SELECT lr.*, doc.name AS requested_by_name FROM lab_results lr JOIN users doc ON doc.id=lr.requested_by WHERE lr.patient_id=? ORDER BY lr.requested_date DESC");
    $s->execute([$patient['id']]); $labs = $s->fetchAll();
}
$statusColors = ['Requested'=>'warning','Processing'=>'info','Completed'=>'success','Cancelled'=>'danger'];
?>
<?php renderHead('My Lab Results'); ?><?php renderNav($user); ?>
<?php renderPageHeader('My Lab Results', 'View your laboratory test results', 'eyedropper'); ?>
<?php if (empty($labs)): ?>
<div class="text-center py-5 text-muted"><i class="bi bi-flask" style="font-size:3rem;opacity:.3"></i><p class="mt-3">No lab results found.</p></div>
<?php else: ?>
<div class="card card-gvx"><div class="table-responsive"><table class="table table-gvx mb-0">
  <thead><tr><th>Test</th><th>Category</th><th>Result</th><th>Normal Range</th><th>Date</th><th>Status</th></tr></thead>
  <tbody>
    <?php foreach ($labs as $l): ?>
    <tr>
      <td class="fw-semibold"><?= sanitize($l['test_name']) ?></td>
      <td><span class="badge bg-secondary small"><?= sanitize($l['test_category']) ?></span></td>
      <td class="<?= $l['is_abnormal'] ? 'text-danger fw-bold' : '' ?>"><?= $l['result'] ? sanitize($l['result']) . ($l['is_abnormal'] ? ' ⚠️' : '') : '<span class="text-muted">Pending</span>' ?></td>
      <td class="small text-muted"><?= sanitize($l['normal_range'] ? $l['normal_range'] . ($l['unit'] ? ' ' . $l['unit'] : '') : '—') ?></td>
      <td class="small"><?= formatDate($l['result_date'] ?? $l['requested_date']) ?></td>
      <td><span class="badge bg-<?= $statusColors[$l['status']] ?? 'secondary' ?>"><?= $l['status'] ?></span></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table></div></div>
<?php endif; ?>
<?php renderFooter(); ?>
