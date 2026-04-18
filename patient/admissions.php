<?php
// patient/admissions.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
$user = guard('patient');
$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]); $patient = $stmt->fetch();
$admissions = [];
if ($patient) {
    $s = db()->prepare("SELECT a.*, d.name AS dept_name, s.name AS admitted_by_name FROM admissions a JOIN departments d ON d.id=a.department_id JOIN users s ON s.id=a.admitted_by WHERE a.patient_id=? ORDER BY a.admission_date DESC");
    $s->execute([$patient['id']]); $admissions = $s->fetchAll();
}
$statusColors = ['Admitted'=>'primary','Discharged'=>'success','Transferred'=>'info','Critical'=>'danger'];
?>
<?php renderHead('My Admissions'); ?><?php renderNav($user); ?>
<?php renderPageHeader('My Admissions', 'View your hospital admission history', 'hospital-fill'); ?>
<?php if (empty($admissions)): ?>
<div class="text-center py-5 text-muted"><i class="bi bi-hospital" style="font-size:3rem;opacity:.3"></i><p class="mt-3">No admission records found.</p></div>
<?php else: ?>
<div class="card card-gvx"><div class="table-responsive"><table class="table table-gvx mb-0">
  <thead><tr><th>Department</th><th>Room</th><th>Admitted</th><th>Discharged</th><th>Reason</th><th>Status</th></tr></thead>
  <tbody>
    <?php foreach ($admissions as $a): ?>
    <tr>
      <td><?= sanitize($a['dept_name']) ?></td>
      <td class="small"><?= sanitize($a['room_number'] ? "Room {$a['room_number']}" : '—') ?></td>
      <td><?= formatDate($a['admission_date']) ?></td>
      <td><?= $a['discharge_date'] ? formatDate($a['discharge_date']) : '<span class="text-primary">Ongoing</span>' ?></td>
      <td class="small text-muted" style="max-width:180px"><?= sanitize($a['reason']) ?></td>
      <td><span class="badge bg-<?= $statusColors[$a['status']] ?? 'secondary' ?>"><?= $a['status'] ?></span></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table></div></div>
<?php endif; ?>
<?php renderFooter(); ?>
