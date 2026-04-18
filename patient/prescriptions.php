<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
$user = guard('patient');
$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]); $patient = $stmt->fetch();
$prescriptions = [];
if ($patient) {
    $s = db()->prepare("SELECT pr.*, doc.name AS prescribed_by_name FROM prescriptions pr JOIN users doc ON doc.id=pr.prescribed_by WHERE pr.patient_id=? ORDER BY pr.prescribed_at DESC");
    $s->execute([$patient['id']]); $prescriptions = $s->fetchAll();
}
$statusColors = ['Pending'=>'warning','Dispensed'=>'success','Cancelled'=>'danger'];
?>
<?php renderHead('My Prescriptions'); ?><?php renderNav($user); ?>
<?php renderPageHeader('My Prescriptions', 'View your prescribed medicines', 'capsule-pill'); ?>
<?php if (empty($prescriptions)): ?>
<div class="text-center py-5 text-muted"><i class="bi bi-capsule" style="font-size:3rem;opacity:.3"></i><p class="mt-3">No prescriptions found.</p></div>
<?php else: ?>
<div class="card card-gvx"><div class="table-responsive"><table class="table table-gvx mb-0">
  <thead><tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th><th>Status</th><th>Date</th></tr></thead>
  <tbody>
    <?php foreach ($prescriptions as $p): ?>
    <tr>
      <td class="fw-semibold"><?= sanitize($p['medicine_name']) ?></td>
      <td class="small"><?= sanitize($p['dosage']) ?></td>
      <td class="small"><?= sanitize($p['frequency']) ?></td>
      <td class="small"><?= sanitize($p['duration'] ?: '—') ?></td>
      <td class="small text-muted"><?= sanitize($p['instructions'] ?: '—') ?></td>
      <td><span class="badge bg-<?= $statusColors[$p['status']] ?? 'secondary' ?>"><?= $p['status'] ?></span></td>
      <td class="small"><?= formatDate($p['prescribed_at']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table></div></div>
<?php endif; ?>
<?php renderFooter(); ?>
