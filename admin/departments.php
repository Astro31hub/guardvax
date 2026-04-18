<?php
// admin/departments.php — Department Management
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user   = guard('admin');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['create_dept','update_dept'])) {
        $name     = sanitize($_POST['name'] ?? '');
        $desc     = sanitize($_POST['description'] ?? '');
        $location = sanitize($_POST['location'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (strlen($name) < 2) $errors[] = 'Department name required.';

        if (empty($errors)) {
            if ($action === 'create_dept') {
                $stmt = db()->prepare('INSERT INTO departments (name, description, location, is_active) VALUES (?,?,?,?)');
                $stmt->execute([$name, $desc, $location, $isActive]);
                auditLog('DEPT_CREATED', 'departments', (int)db()->lastInsertId(), "Created: $name");
                setFlash('success', "Department \"$name\" created.");
            } else {
                $did = (int)($_POST['dept_id'] ?? 0);
                $stmt = db()->prepare('UPDATE departments SET name=?, description=?, location=?, is_active=? WHERE id=?');
                $stmt->execute([$name, $desc, $location, $isActive, $did]);
                auditLog('DEPT_UPDATED', 'departments', $did, "Updated: $name");
                setFlash('success', 'Department updated.');
            }
            header('Location: ' . SITE_URL . '/admin/departments.php');
            exit;
        }
    }
}

$departments = db()->query(
    'SELECT d.*, COUNT(u.id) AS staff_count
     FROM departments d
     LEFT JOIN users u ON u.department_id = d.id
     GROUP BY d.id ORDER BY d.name'
)->fetchAll();

$editDept = null;
if (isset($_GET['edit'])) {
    $s = db()->prepare('SELECT * FROM departments WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $editDept = $s->fetch();
}
?>
<?php renderHead('Departments'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('Departments', 'Manage hospital departments', 'building-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= sanitize($e) ?></div><?php endforeach; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="card card-gvx">
      <div class="card-header-gvx">
        <i class="bi bi-<?= $editDept ? 'pencil-fill text-primary' : 'plus-circle-fill text-success' ?> me-2"></i>
        <?= $editDept ? 'Edit Department' : 'Add Department' ?>
      </div>
      <div class="card-body">
        <form method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="<?= $editDept ? 'update_dept' : 'create_dept' ?>">
          <?php if ($editDept): ?><input type="hidden" name="dept_id" value="<?= $editDept['id'] ?>"><?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?= sanitize($editDept['name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2"><?= sanitize($editDept['description'] ?? '') ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="<?= sanitize($editDept['location'] ?? '') ?>" placeholder="e.g. Building A, 2nd Floor">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="is_active" id="isActive" <?= ($editDept['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">Active</label>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-<?= $editDept ? 'primary' : 'success' ?> flex-grow-1">
              <i class="bi bi-check2 me-1"></i><?= $editDept ? 'Update' : 'Add Department' ?>
            </button>
            <?php if ($editDept): ?>
              <a href="<?= SITE_URL ?>/admin/departments.php" class="btn btn-outline-secondary">Cancel</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-gvx">
      <div class="card-header-gvx"><i class="bi bi-building-fill me-2 text-primary"></i>Departments <span class="badge bg-secondary ms-1"><?= count($departments) ?></span></div>
      <div class="table-responsive">
        <table class="table table-gvx mb-0">
          <thead><tr><th>Name</th><th>Location</th><th>Staff</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($departments as $d): ?>
            <tr>
              <td>
                <div class="fw-semibold"><?= sanitize($d['name']) ?></div>
                <div class="text-muted small"><?= sanitize($d['description'] ?: '—') ?></div>
              </td>
              <td class="small"><?= sanitize($d['location'] ?: '—') ?></td>
              <td><span class="badge bg-primary"><?= $d['staff_count'] ?> staff</span></td>
              <td><span class="badge <?= $d['is_active'] ? 'bg-success' : 'bg-danger' ?>"><?= $d['is_active'] ? 'Active' : 'Inactive' ?></span></td>
              <td><a href="?edit=<?= $d['id'] ?>" class="btn btn-xs btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php renderFooter(); ?>
