<?php
// patient/appointments.php — Patient View Appointments
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('patient');
$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]);
$patient = $stmt->fetch();

$appointments = [];
if ($patient) {
    $stmt = db()->prepare(
        "SELECT a.*, d.name AS dept_name, doc.name AS doctor_name
         FROM appointments a
         JOIN departments d ON d.id = a.department_id
         LEFT JOIN users doc ON doc.id = a.doctor_id
         WHERE a.patient_id = ?
         ORDER BY a.appointment_date DESC, a.appointment_time DESC"
    );
    $stmt->execute([$patient['id']]);
    $appointments = $stmt->fetchAll();
}
$statusColors = ['Pending'=>'warning','Confirmed'=>'primary','Completed'=>'success','Cancelled'=>'danger','No-show'=>'secondary'];
?>
<?php renderHead('My Appointments'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('My Appointments', 'View your scheduled appointments', 'calendar2-check-fill'); ?>

<?php if (empty($appointments)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-calendar-x" style="font-size:3rem;opacity:.3"></i>
    <p class="mt-3">No appointments found. Contact the nurse to schedule one.</p>
  </div>
<?php else: ?>
<div class="card card-gvx">
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr><th>Date & Time</th><th>Department</th><th>Assigned To</th><th>Reason</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
          <td><div class="fw-semibold"><?= formatDate($a['appointment_date']) ?></div><div class="text-muted small"><?= date('h:i A', strtotime($a['appointment_time'])) ?></div></td>
          <td><?= sanitize($a['dept_name']) ?></td>
          <td><?= sanitize($a['doctor_name'] ?: '—') ?></td>
          <td class="small text-muted"><?= sanitize($a['reason'] ?: '—') ?></td>
          <td><span class="badge bg-<?= $statusColors[$a['status']] ?? 'secondary' ?>"><?= $a['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php renderFooter(); ?>
