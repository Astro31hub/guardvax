<?php
// admin/billing.php — Patient Billing
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('admin', 'nurse');
$errors = [];

// Generate bill number
function generateBillNumber(): string {
    $count = (int)db()->query('SELECT COUNT(*) FROM billing')->fetchColumn();
    return 'BILL-' . date('Y') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create_bill') {
        $patientId   = (int)($_POST['patient_id'] ?? 0);
        $admissionId = (int)($_POST['admission_id'] ?? 0) ?: null;
        $consultFee  = (float)($_POST['consultation_fee'] ?? 0);
        $medFee      = (float)($_POST['medicine_fee'] ?? 0);
        $labFee      = (float)($_POST['lab_fee'] ?? 0);
        $roomFee     = (float)($_POST['room_fee'] ?? 0);
        $otherFee    = (float)($_POST['other_fee'] ?? 0);
        $discount    = (float)($_POST['discount'] ?? 0);
        $amountPaid  = (float)($_POST['amount_paid'] ?? 0);
        $payMethod   = $_POST['payment_method'] ?? null;
        $notes       = sanitize($_POST['notes'] ?? '');
        $total       = $consultFee + $medFee + $labFee + $roomFee + $otherFee - $discount;

        $status = 'Unpaid';
        if ($amountPaid >= $total) $status = 'Paid';
        elseif ($amountPaid > 0)   $status = 'Partial';

        if (!$patientId) $errors[] = 'Patient required.';

        if (empty($errors)) {
            $billNum = generateBillNumber();
            $stmt = db()->prepare(
                'INSERT INTO billing (patient_id, admission_id, bill_number, consultation_fee, medicine_fee,
                 lab_fee, room_fee, other_fee, total_amount, discount, amount_paid, payment_method, status, notes, created_by, paid_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $paidAt = $status === 'Paid' ? date('Y-m-d H:i:s') : null;
            $stmt->execute([$patientId, $admissionId, $billNum, $consultFee, $medFee, $labFee, $roomFee,
                           $otherFee, $total, $discount, $amountPaid, $payMethod ?: null, $status, $notes, $user['id'], $paidAt]);
            $bid = (int)db()->lastInsertId();
            auditLog('BILL_CREATED', 'billing', $bid, "Bill $billNum — Total ₱" . number_format($total, 2));
            setFlash('success', "Bill {$billNum} created. Status: {$status}.");
            header('Location: ' . SITE_URL . '/admin/billing.php');
            exit;
        }
    }

    if ($action === 'update_payment') {
        $bid        = (int)($_POST['bill_id'] ?? 0);
        $amountPaid = (float)($_POST['amount_paid'] ?? 0);
        $payMethod  = $_POST['payment_method'] ?? null;
        $total      = (float)db()->prepare('SELECT total_amount FROM billing WHERE id=?')->execute([$bid]) ? 0 : 0;
        $stmt       = db()->prepare('SELECT total_amount FROM billing WHERE id=?');
        $stmt->execute([$bid]); $total = (float)$stmt->fetchColumn();

        $status = 'Unpaid';
        if ($amountPaid >= $total) $status = 'Paid';
        elseif ($amountPaid > 0)   $status = 'Partial';
        $paidAt = $status === 'Paid' ? date('Y-m-d H:i:s') : null;

        db()->prepare('UPDATE billing SET amount_paid=?, payment_method=?, status=?, paid_at=? WHERE id=?')
            ->execute([$amountPaid, $payMethod, $status, $paidAt, $bid]);
        auditLog('BILL_PAYMENT_UPDATED', 'billing', $bid, "Paid ₱" . number_format($amountPaid, 2) . " — Status: $status");
        setFlash('success', 'Payment updated.');
        header('Location: ' . SITE_URL . '/admin/billing.php');
        exit;
    }
}

$statusFilter = sanitize($_GET['status'] ?? '');
$where  = '1=1';
$params = [];
if ($statusFilter) { $where .= ' AND b.status=?'; $params[] = $statusFilter; }

$bills = db()->prepare(
    "SELECT b.*, p.patient_code, u.name AS patient_name, s.name AS created_by_name
     FROM billing b
     JOIN patients p ON p.id = b.patient_id
     JOIN users u    ON u.id = p.user_id
     JOIN users s    ON s.id = b.created_by
     WHERE {$where}
     ORDER BY b.created_at DESC LIMIT 100"
);
$bills->execute($params);
$bills = $bills->fetchAll();

$patients   = db()->query('SELECT p.id, p.patient_code, u.name FROM patients p JOIN users u ON u.id=p.user_id ORDER BY u.name')->fetchAll();
$admissions = db()->query("SELECT a.id, p.patient_code, u.name AS patient_name FROM admissions a JOIN patients p ON p.id=a.patient_id JOIN users u ON u.id=p.user_id WHERE a.status='Admitted' ORDER BY u.name")->fetchAll();

$totalRevenue = (float)db()->query("SELECT SUM(amount_paid) FROM billing WHERE status IN ('Paid','Partial')")->fetchColumn();
$unpaidCount  = (int)db()->query("SELECT COUNT(*) FROM billing WHERE status='Unpaid'")->fetchColumn();
$paidToday    = (float)db()->query("SELECT SUM(amount_paid) FROM billing WHERE DATE(paid_at)=CURDATE()")->fetchColumn();

$statusColors = ['Unpaid'=>'danger','Partial'=>'warning','Paid'=>'success','Waived'=>'secondary'];
$payMethods   = ['Cash','PhilHealth','HMO','Credit Card','Other'];
?>
<?php renderHead('Billing'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Billing Management', 'Manage patient billing and payments', 'receipt'); ?>

<?php echo renderFlash(); ?>

<!-- Stats -->
<div class="row g-3 mb-3">
  <div class="col-4"><div class="stat-card stat-success"><div class="stat-icon"><i class="bi bi-cash-stack"></i></div><div><div class="stat-value">₱<?= number_format($totalRevenue) ?></div><div class="stat-label">Total Revenue</div></div></div></div>
  <div class="col-4"><div class="stat-card stat-info"><div class="stat-icon"><i class="bi bi-calendar-check"></i></div><div><div class="stat-value">₱<?= number_format($paidToday) ?></div><div class="stat-label">Collected Today</div></div></div></div>
  <div class="col-4"><div class="stat-card stat-warning"><div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div><div><div class="stat-value"><?= $unpaidCount ?></div><div class="stat-label">Unpaid Bills</div></div></div></div>
</div>

<div class="row g-3">
  <!-- Create Bill Form -->
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-plus-circle-fill me-2 text-success"></i>Create Bill</div>
      <div class="card-body">
        <form method="POST" novalidate id="billForm">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="create_bill">
          <div class="mb-2">
            <label class="form-label small">Patient <span class="text-danger">*</span></label>
            <select name="patient_id" class="form-select form-select-sm" required>
              <option value="">— Select Patient —</option>
              <?php foreach ($patients as $p): ?><option value="<?= $p['id'] ?>"><?= sanitize($p['patient_code']) ?> — <?= sanitize($p['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label small">Linked Admission</label>
            <select name="admission_id" class="form-select form-select-sm">
              <option value="">— None —</option>
              <?php foreach ($admissions as $a): ?><option value="<?= $a['id'] ?>"><?= sanitize($a['patient_code']) ?> — <?= sanitize($a['patient_name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <hr class="my-2">
          <div class="row g-2 mb-2">
            <div class="col-6"><label class="form-label small">Consultation (₱)</label><input type="number" name="consultation_fee" class="form-control form-control-sm" step="0.01" value="0" oninput="calcTotal()"></div>
            <div class="col-6"><label class="form-label small">Medicines (₱)</label><input type="number" name="medicine_fee" class="form-control form-control-sm" step="0.01" value="0" oninput="calcTotal()"></div>
            <div class="col-6"><label class="form-label small">Laboratory (₱)</label><input type="number" name="lab_fee" class="form-control form-control-sm" step="0.01" value="0" oninput="calcTotal()"></div>
            <div class="col-6"><label class="form-label small">Room/Board (₱)</label><input type="number" name="room_fee" class="form-control form-control-sm" step="0.01" value="0" oninput="calcTotal()"></div>
            <div class="col-6"><label class="form-label small">Others (₱)</label><input type="number" name="other_fee" class="form-control form-control-sm" step="0.01" value="0" oninput="calcTotal()"></div>
            <div class="col-6"><label class="form-label small">Discount (₱)</label><input type="number" name="discount" class="form-control form-control-sm" step="0.01" value="0" oninput="calcTotal()"></div>
          </div>
          <div class="alert alert-info py-2 mb-2 text-center">
            <strong>Total: ₱<span id="totalDisplay">0.00</span></strong>
          </div>
          <div class="mb-2"><label class="form-label small">Amount Paid (₱)</label><input type="number" name="amount_paid" class="form-control form-control-sm" step="0.01" value="0"></div>
          <div class="mb-2"><label class="form-label small">Payment Method</label>
            <select name="payment_method" class="form-select form-select-sm"><option value="">— None yet —</option><?php foreach ($payMethods as $pm): ?><option value="<?= $pm ?>"><?= $pm ?></option><?php endforeach; ?></select>
          </div>
          <div class="mb-2"><label class="form-label small">Notes</label><textarea name="notes" class="form-control form-control-sm" rows="2"></textarea></div>
          <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-receipt me-1"></i>Generate Bill</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Bills List -->
  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-receipt me-2 text-primary"></i>Bills</span>
        <div class="d-flex gap-1">
          <?php foreach ([''=>'All','Unpaid'=>'Unpaid','Partial'=>'Partial','Paid'=>'Paid'] as $val => $label): ?>
            <a href="?status=<?= urlencode($val) ?>" class="btn btn-xs <?= $statusFilter===$val?'btn-primary':'btn-outline-secondary' ?>"><?= $label ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Bill #</th><th>Patient</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($bills as $b):
              $balance = $b['total_amount'] - $b['amount_paid'];
            ?>
            <tr>
              <td><code class="small"><?= sanitize($b['bill_number']) ?></code></td>
              <td>
                <div class="fw-semibold small"><?= sanitize($b['patient_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($b['patient_code']) ?></div>
              </td>
              <td class="fw-semibold small">₱<?= number_format($b['total_amount'], 2) ?></td>
              <td class="small text-success">₱<?= number_format($b['amount_paid'], 2) ?></td>
              <td class="small <?= $balance > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">₱<?= number_format($balance, 2) ?></td>
              <td><span class="badge bg-<?= $statusColors[$b['status']] ?? 'secondary' ?>"><?= $b['status'] ?></span></td>
              <td>
                <?php if ($b['status'] !== 'Paid'): ?>
                <button class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#payModal<?= $b['id'] ?>">
                  <i class="bi bi-cash"></i> Pay
                </button>
                <div class="modal fade" id="payModal<?= $b['id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="update_payment">
                        <input type="hidden" name="bill_id" value="<?= $b['id'] ?>">
                        <div class="modal-header"><h6 class="modal-title"><?= sanitize($b['bill_number']) ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                          <p class="small text-muted mb-2">Total: ₱<?= number_format($b['total_amount'], 2) ?> | Balance: ₱<?= number_format($balance, 2) ?></p>
                          <div class="mb-2"><label class="form-label small">Amount Paid (₱)</label><input type="number" name="amount_paid" class="form-control form-control-sm" step="0.01" value="<?= $b['amount_paid'] ?>" required></div>
                          <div class="mb-2"><label class="form-label small">Payment Method</label><select name="payment_method" class="form-select form-select-sm"><?php foreach ($payMethods as $pm): ?><option value="<?= $pm ?>" <?= $b['payment_method']===$pm?'selected':'' ?>><?= $pm ?></option><?php endforeach; ?></select></div>
                        </div>
                        <div class="modal-footer py-2"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-sm btn-success">Update</button></div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php else: ?>
                  <span class="text-muted small"><?= $b['paid_at'] ? formatDate($b['paid_at'], 'M d') : '—' ?></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($bills)): ?>
              <tr><td colspan="7" class="text-center py-4 text-muted">No bills found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
<script>
function calcTotal() {
  const ids = ['consultation_fee','medicine_fee','lab_fee','room_fee','other_fee'];
  let total = ids.reduce((sum, id) => sum + (parseFloat(document.querySelector(`[name="${id}"]`)?.value) || 0), 0);
  total -= parseFloat(document.querySelector('[name="discount"]')?.value) || 0;
  document.getElementById('totalDisplay').textContent = Math.max(0, total).toFixed(2);
}
</script>
