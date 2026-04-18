<?php
// ============================================================
// admin/vaccines.php — Manage Vaccine Catalog
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('admin');
$errors = [];

// ── Create ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create_vaccine') {
        $name         = sanitize($_POST['name'] ?? '');
        $manufacturer = sanitize($_POST['manufacturer'] ?? '');
        $description  = sanitize($_POST['description'] ?? '');
        $dosesReq     = max(1, (int)($_POST['doses_required'] ?? 1));
        $intervalDays = (int)($_POST['interval_days'] ?? 0) ?: null;
        $isActive     = isset($_POST['is_active']) ? 1 : 0;

        if (strlen($name) < 2) $errors[] = 'Vaccine name is required.';

        if (empty($errors)) {
            $stmt = db()->prepare(
                'INSERT INTO vaccines (name, manufacturer, description, doses_required, interval_days, is_active)
                 VALUES (?,?,?,?,?,?)'
            );
            $stmt->execute([$name, $manufacturer, $description, $dosesReq, $intervalDays, $isActive]);
            $vid = (int) db()->lastInsertId();
            auditLog('VACCINE_CREATED', 'vaccines', $vid, "Created: {$name}");
            setFlash('success', "Vaccine \"{$name}\" added successfully.");
            header('Location: ' . SITE_URL . '/admin/vaccines.php');
            exit;
        }
    }

    if ($action === 'update_vaccine') {
        $vid          = (int) ($_POST['vaccine_id'] ?? 0);
        $name         = sanitize($_POST['name'] ?? '');
        $manufacturer = sanitize($_POST['manufacturer'] ?? '');
        $description  = sanitize($_POST['description'] ?? '');
        $dosesReq     = max(1, (int)($_POST['doses_required'] ?? 1));
        $intervalDays = (int)($_POST['interval_days'] ?? 0) ?: null;
        $isActive     = isset($_POST['is_active']) ? 1 : 0;

        if (strlen($name) < 2) $errors[] = 'Vaccine name is required.';

        if (empty($errors)) {
            $stmt = db()->prepare(
                'UPDATE vaccines SET name=?, manufacturer=?, description=?, doses_required=?, interval_days=?, is_active=? WHERE id=?'
            );
            $stmt->execute([$name, $manufacturer, $description, $dosesReq, $intervalDays, $isActive, $vid]);
            auditLog('VACCINE_UPDATED', 'vaccines', $vid, "Updated: {$name}");
            setFlash('success', 'Vaccine updated.');
            header('Location: ' . SITE_URL . '/admin/vaccines.php');
            exit;
        }
    }

    if ($action === 'toggle_vaccine') {
        $vid      = (int) ($_POST['vaccine_id'] ?? 0);
        $newState = (int) ($_POST['current_state'] ?? 1) === 1 ? 0 : 1;
        db()->prepare('UPDATE vaccines SET is_active=? WHERE id=?')->execute([$newState, $vid]);
        auditLog('VACCINE_TOGGLED', 'vaccines', $vid, "is_active → {$newState}");
        setFlash('success', 'Vaccine status updated.');
        header('Location: ' . SITE_URL . '/admin/vaccines.php');
        exit;
    }
}

// ── List ──────────────────────────────────────────────────────
$vaccines = db()->query(
    'SELECT v.*, (SELECT COUNT(*) FROM vaccinations va WHERE va.vaccine_id=v.id) AS usage_count
     FROM vaccines v ORDER BY v.name ASC'
)->fetchAll();

// Edit target
$editVaccine = null;
if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT * FROM vaccines WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $editVaccine = $s->fetch();
}
?>
<?php renderHead('Vaccine Catalog'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Vaccine Catalog', 'Manage available vaccines and their dosing schedules', 'capsule'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<div class="row g-3">
  <!-- Add / Edit Form -->
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-<?= $editVaccine ? 'pencil-fill' : 'plus-circle-fill' ?> me-2 text-<?= $editVaccine ? 'primary' : 'success' ?>"></i>
        <?= $editVaccine ? 'Edit Vaccine' : 'Add New Vaccine' ?>
      </div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="<?= $editVaccine ? 'update_vaccine' : 'create_vaccine' ?>">
          <?php if ($editVaccine): ?>
            <input type="hidden" name="vaccine_id" value="<?= $editVaccine['id'] ?>">
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label">Vaccine Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required
                   value="<?= sanitize($editVaccine['name'] ?? '') ?>" placeholder="e.g. COVID-19 (Pfizer)">
          </div>

          <div class="mb-3">
            <label class="form-label">Manufacturer</label>
            <input type="text" name="manufacturer" class="form-control"
                   value="<?= sanitize($editVaccine['manufacturer'] ?? '') ?>" placeholder="e.g. Pfizer-BioNTech">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label">Doses Required</label>
              <input type="number" name="doses_required" class="form-control" min="1" max="10"
                     value="<?= (int)($editVaccine['doses_required'] ?? 1) ?>">
            </div>
            <div class="col-6">
              <label class="form-label">Interval (days)</label>
              <input type="number" name="interval_days" class="form-control" min="0"
                     value="<?= (int)($editVaccine['interval_days'] ?? 0) ?>"
                     placeholder="0 = N/A">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"
                      placeholder="Brief description of the vaccine"><?= sanitize($editVaccine['description'] ?? '') ?></textarea>
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="is_active" id="isActive"
                   <?= ($editVaccine['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">Active (available for use)</label>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-<?= $editVaccine ? 'primary' : 'success' ?> flex-grow-1">
              <i class="bi bi-<?= $editVaccine ? 'check2' : 'plus-lg' ?> me-1"></i>
              <?= $editVaccine ? 'Update Vaccine' : 'Add Vaccine' ?>
            </button>
            <?php if ($editVaccine): ?>
              <a href="<?= SITE_URL ?>/admin/vaccines.php" class="btn btn-outline-secondary">Cancel</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Vaccine List -->
  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-capsule me-2 text-primary"></i>
        Vaccines <span class="badge bg-secondary ms-1"><?= count($vaccines) ?></span>
      </div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr>
            <th>Vaccine</th><th>Manufacturer</th><th>Doses</th><th>Interval</th><th>Usage</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
            <?php foreach ($vaccines as $v): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?= sanitize($v['name']) ?></div>
                <div class="text-muted small" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                  <?= sanitize($v['description'] ?: '—') ?>
                </div>
              </td>
              <td><?= sanitize($v['manufacturer'] ?: '—') ?></td>
              <td>
                <span class="badge bg-primary"><?= $v['doses_required'] ?> dose<?= $v['doses_required'] > 1 ? 's' : '' ?></span>
              </td>
              <td class="small text-muted">
                <?= $v['interval_days'] ? $v['interval_days'] . ' days' : '—' ?>
              </td>
              <td>
                <span class="badge bg-<?= $v['usage_count'] > 0 ? 'success' : 'secondary' ?>">
                  <?= $v['usage_count'] ?> given
                </span>
              </td>
              <td>
                <span class="badge <?= $v['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                  <?= $v['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td>
                <a href="?edit=<?= $v['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                  <input type="hidden" name="action" value="toggle_vaccine">
                  <input type="hidden" name="vaccine_id" value="<?= $v['id'] ?>">
                  <input type="hidden" name="current_state" value="<?= $v['is_active'] ?>">
                  <button type="submit" class="btn btn-xs btn-outline-<?= $v['is_active'] ? 'danger' : 'success' ?>"
                          title="<?= $v['is_active'] ? 'Deactivate' : 'Activate' ?>">
                    <i class="bi bi-<?= $v['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($vaccines)): ?>
              <tr><td colspan="7" class="text-center py-4 text-muted">No vaccines in catalog.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php renderFooter(); ?>
<?php if ($editVaccine): ?>
<script>
// Scroll to form on edit
document.querySelector('.card-gvx')?.scrollIntoView({ behavior: 'smooth' });
</script>
<?php endif; ?>
