<?php
// admin/inventory.php — Medicine & Supply Inventory
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('admin', 'nurse');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['add_item','update_item'])) {
        $name      = sanitize($_POST['item_name'] ?? '');
        $category  = $_POST['category'] ?? 'Medicine';
        $desc      = sanitize($_POST['description'] ?? '');
        $qty       = (int)($_POST['quantity'] ?? 0);
        $unit      = sanitize($_POST['unit'] ?? 'pcs');
        $price     = (float)($_POST['unit_price'] ?? 0) ?: null;
        $reorder   = (int)($_POST['reorder_level'] ?? 10);
        $expiry    = $_POST['expiry_date'] ?? null;
        $supplier  = sanitize($_POST['supplier'] ?? '');
        $location  = sanitize($_POST['location'] ?? '');

        if (strlen($name) < 2) $errors[] = 'Item name required.';

        if (empty($errors)) {
            if ($action === 'add_item') {
                $stmt = db()->prepare(
                    'INSERT INTO inventory (item_name, category, description, quantity, unit, unit_price, reorder_level, expiry_date, supplier, location)
                     VALUES (?,?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([$name, $category, $desc, $qty, $unit, $price, $reorder, $expiry ?: null, $supplier, $location]);
                $iid = (int)db()->lastInsertId();
                auditLog('INVENTORY_ADDED', 'inventory', $iid, "Added: $name (qty: $qty)");
                setFlash('success', "\"$name\" added to inventory.");
            } else {
                $iid = (int)($_POST['item_id'] ?? 0);
                $stmt = db()->prepare(
                    'UPDATE inventory SET item_name=?, category=?, description=?, quantity=?, unit=?, unit_price=?,
                     reorder_level=?, expiry_date=?, supplier=?, location=? WHERE id=?'
                );
                $stmt->execute([$name, $category, $desc, $qty, $unit, $price, $reorder, $expiry ?: null, $supplier, $location, $iid]);
                auditLog('INVENTORY_UPDATED', 'inventory', $iid, "Updated: $name");
                setFlash('success', 'Inventory item updated.');
            }
            header('Location: ' . SITE_URL . '/admin/inventory.php');
            exit;
        }
    }

    if ($action === 'adjust_qty') {
        $iid       = (int)($_POST['item_id'] ?? 0);
        $adjust    = (int)($_POST['adjust_qty'] ?? 0);
        $type      = $_POST['adjust_type'] ?? 'add';
        if ($type === 'add') {
            db()->prepare('UPDATE inventory SET quantity = quantity + ? WHERE id=?')->execute([$adjust, $iid]);
        } else {
            db()->prepare('UPDATE inventory SET quantity = GREATEST(0, quantity - ?) WHERE id=?')->execute([$adjust, $iid]);
        }
        auditLog('INVENTORY_ADJUSTED', 'inventory', $iid, "$type $adjust units");
        setFlash('success', 'Stock adjusted.');
        header('Location: ' . SITE_URL . '/admin/inventory.php');
        exit;
    }
}

$search   = sanitize($_GET['q'] ?? '');
$catFilter= sanitize($_GET['category'] ?? '');
$lowStock = isset($_GET['low']);

$where  = '1=1';
$params = [];
if ($search)    { $where .= ' AND item_name LIKE ?'; $params[] = "%{$search}%"; }
if ($catFilter) { $where .= ' AND category = ?';     $params[] = $catFilter; }
if ($lowStock)  { $where .= ' AND quantity <= reorder_level'; }

$items = db()->prepare("SELECT * FROM inventory WHERE {$where} ORDER BY item_name");
$items->execute($params);
$items = $items->fetchAll();

$lowStockCount = (int)db()->query('SELECT COUNT(*) FROM inventory WHERE quantity <= reorder_level AND is_active=1')->fetchColumn();
$totalItems    = (int)db()->query('SELECT COUNT(*) FROM inventory WHERE is_active=1')->fetchColumn();
$categories    = ['Medicine','Vaccine','Medical Supply','Equipment','Other'];

$editItem = null;
if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT * FROM inventory WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $editItem = $s->fetch();
}
?>
<?php renderHead('Inventory'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Inventory Management', 'Track medicines, vaccines, and medical supplies', 'box-seam-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<?php if ($lowStockCount > 0): ?>
<div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
  <span><i class="bi bi-exclamation-triangle-fill me-2"></i><strong><?= $lowStockCount ?> item<?= $lowStockCount > 1 ? 's' : '' ?> below reorder level!</strong></span>
  <a href="?low=1" class="btn btn-sm btn-warning">View Low Stock</a>
</div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-<?= $editItem ? 'pencil-fill text-primary' : 'plus-circle-fill text-success' ?> me-2"></i><?= $editItem ? 'Edit Item' : 'Add Item' ?></div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="<?= $editItem ? 'update_item' : 'add_item' ?>">
          <?php if ($editItem): ?><input type="hidden" name="item_id" value="<?= $editItem['id'] ?>"><?php endif; ?>
          <div class="mb-2"><label class="form-label small">Item Name <span class="text-danger">*</span></label><input type="text" name="item_name" class="form-control form-control-sm" value="<?= sanitize($editItem['item_name'] ?? '') ?>" required></div>
          <div class="mb-2"><label class="form-label small">Category</label>
            <select name="category" class="form-select form-select-sm">
              <?php foreach ($categories as $c): ?><option value="<?= $c ?>" <?= ($editItem['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6"><label class="form-label small">Quantity</label><input type="number" name="quantity" class="form-control form-control-sm" value="<?= (int)($editItem['quantity'] ?? 0) ?>" min="0"></div>
            <div class="col-6"><label class="form-label small">Unit</label><input type="text" name="unit" class="form-control form-control-sm" value="<?= sanitize($editItem['unit'] ?? 'pcs') ?>"></div>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6"><label class="form-label small">Unit Price (₱)</label><input type="number" name="unit_price" class="form-control form-control-sm" step="0.01" value="<?= $editItem['unit_price'] ?? '' ?>"></div>
            <div class="col-6"><label class="form-label small">Reorder Level</label><input type="number" name="reorder_level" class="form-control form-control-sm" value="<?= (int)($editItem['reorder_level'] ?? 10) ?>"></div>
          </div>
          <div class="mb-2"><label class="form-label small">Expiry Date</label><input type="date" name="expiry_date" class="form-control form-control-sm" value="<?= $editItem['expiry_date'] ?? '' ?>"></div>
          <div class="mb-2"><label class="form-label small">Supplier</label><input type="text" name="supplier" class="form-control form-control-sm" value="<?= sanitize($editItem['supplier'] ?? '') ?>"></div>
          <div class="mb-3"><label class="form-label small">Location</label><input type="text" name="location" class="form-control form-control-sm" value="<?= sanitize($editItem['location'] ?? '') ?>" placeholder="e.g. Pharmacy Cabinet A"></div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-<?= $editItem ? 'primary' : 'success' ?> btn-sm flex-grow-1"><i class="bi bi-check2 me-1"></i><?= $editItem ? 'Update' : 'Add Item' ?></button>
            <?php if ($editItem): ?><a href="<?= SITE_URL ?>/admin/inventory.php" class="btn btn-outline-secondary btn-sm">Cancel</a><?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <form class="d-flex flex-wrap gap-2" method="GET">
          <input type="text" name="q" class="form-control form-control-sm" placeholder="Search item…" value="<?= sanitize($search) ?>" style="max-width:180px">
          <select name="category" class="form-select form-select-sm" style="max-width:150px">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?><option value="<?= $c ?>" <?= $catFilter===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
          <a href="<?= SITE_URL ?>/admin/inventory.php" class="btn btn-sm btn-outline-secondary">Reset</a>
          <a href="?low=1" class="btn btn-sm btn-outline-warning"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock</a>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Item</th><th>Category</th><th>Stock</th><th>Unit Price</th><th>Expiry</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($items as $item): ?>
            <?php $isLow = $item['quantity'] <= $item['reorder_level']; ?>
            <tr class="<?= $isLow ? 'table-warning' : '' ?>">
              <td>
                <div class="fw-semibold small"><?= sanitize($item['item_name']) ?></div>
                <div class="text-muted" style="font-size:.72rem"><?= sanitize($item['location'] ?: '—') ?></div>
              </td>
              <td><span class="badge bg-secondary"><?= sanitize($item['category']) ?></span></td>
              <td>
                <span class="fw-bold <?= $isLow ? 'text-danger' : 'text-success' ?>">
                  <?= number_format($item['quantity']) ?> <?= sanitize($item['unit']) ?>
                </span>
                <?php if ($isLow): ?><div class="text-danger" style="font-size:.7rem">⚠️ Low stock</div><?php endif; ?>
              </td>
              <td class="small"><?= $item['unit_price'] ? '₱' . number_format($item['unit_price'], 2) : '—' ?></td>
              <td class="small <?= $item['expiry_date'] && strtotime($item['expiry_date']) < time() ? 'text-danger fw-bold' : 'text-muted' ?>">
                <?= $item['expiry_date'] ? formatDate($item['expiry_date']) : '—' ?>
              </td>
              <td>
                <a href="?edit=<?= $item['id'] ?>" class="btn btn-xs btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <!-- Quick adjust modal trigger -->
                <button class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#adjustModal<?= $item['id'] ?>">
                  <i class="bi bi-plus-slash-minus"></i>
                </button>
                <!-- Adjust Modal -->
                <div class="modal fade" id="adjustModal<?= $item['id'] ?>" tabindex="-1">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="adjust_qty">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <div class="modal-header"><h6 class="modal-title">Adjust: <?= sanitize(substr($item['item_name'], 0, 25)) ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                          <div class="mb-2"><label class="form-label small">Adjustment Type</label>
                            <select name="adjust_type" class="form-select form-select-sm"><option value="add">Add Stock (+)</option><option value="subtract">Use / Remove (-)</option></select>
                          </div>
                          <div class="mb-2"><label class="form-label small">Quantity</label><input type="number" name="adjust_qty" class="form-control form-control-sm" min="1" value="1" required></div>
                          <div class="text-muted small">Current: <?= number_format($item['quantity']) ?> <?= sanitize($item['unit']) ?></div>
                        </div>
                        <div class="modal-footer py-2"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-sm btn-success">Adjust</button></div>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No inventory items found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
