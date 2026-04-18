<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';
$user = guard('patient');
$stmt = db()->prepare('SELECT * FROM patients WHERE user_id=?');
$stmt->execute([$user['id']]); $patient = $stmt->fetch();
$bills = []; $totalOwed = 0;
if ($patient) {
    $s = db()->prepare("SELECT * FROM billing WHERE patient_id=? ORDER BY created_at DESC");
    $s->execute([$patient['id']]); $bills = $s->fetchAll();
    foreach ($bills as $b) { if ($b['status'] !== 'Paid') $totalOwed += ($b['total_amount'] - $b['amount_paid']); }
}
$statusColors = ['Unpaid'=>'danger','Partial'=>'warning','Paid'=>'success','Waived'=>'secondary'];
?>
<?php renderHead('My Bills'); ?><?php renderNav($user); ?>
<?php renderPageHeader('My Bills', 'View your hospital billing statements', 'receipt'); ?>
<?php if ($totalOwed > 0): ?>
<div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Outstanding Balance: ₱<?= number_format($totalOwed, 2) ?></strong> — Please settle at the billing counter.</div>
<?php endif; ?>
<?php if (empty($bills)): ?>
<div class="text-center py-5 text-muted"><i class="bi bi-receipt" style="font-size:3rem;opacity:.3"></i><p class="mt-3">No billing records found.</p></div>
<?php else: ?>
<div class="card card-gvx"><div class="table-responsive"><table class="table table-gvx mb-0">
  <thead><tr><th>Bill #</th><th>Total</th><th>Paid</th><th>Balance</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead>
  <tbody>
    <?php foreach ($bills as $b): $balance = $b['total_amount'] - $b['amount_paid']; ?>
    <tr>
      <td><code class="small"><?= sanitize($b['bill_number']) ?></code></td>
      <td class="fw-semibold">₱<?= number_format($b['total_amount'], 2) ?></td>
      <td class="text-success">₱<?= number_format($b['amount_paid'], 2) ?></td>
      <td class="<?= $balance > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">₱<?= number_format($balance, 2) ?></td>
      <td class="small"><?= sanitize($b['payment_method'] ?: '—') ?></td>
      <td><span class="badge bg-<?= $statusColors[$b['status']] ?? 'secondary' ?>"><?= $b['status'] ?></span></td>
      <td class="small"><?= formatDate($b['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table></div></div>
<?php endif; ?>
<?php renderFooter(); ?>
