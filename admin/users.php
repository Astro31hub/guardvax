<?php
// ============================================================
// admin/users.php — User Management (CRUD)
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/layout.php';

$user = guard('admin');

$errors  = [];
$success = '';

// ── Handle POST actions ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    // Create user
    if ($action === 'create_user') {
        $name   = sanitize($_POST['name'] ?? '');
        $email  = sanitizeEmail($_POST['email'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['active','inactive','pending']) ? $_POST['status'] : 'active';
        $pw     = $_POST['password'] ?? '';

        if (strlen($name) < 2)     $errors[] = 'Name too short.';
        if (!validateEmail($email)) $errors[] = 'Invalid email.';
        if (emailExists($email))    $errors[] = 'Email already exists.';
        if (strlen($pw) < 8)        $errors[] = 'Password must be 8+ characters.';

        if (empty($errors)) {
            $hash = password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = db()->prepare('INSERT INTO users (name, email, password, role_id, status, email_verified) VALUES (?,?,?,?,?,1)');
            $stmt->execute([$name, $email, $hash, $roleId, $status]);
            $newId = (int) db()->lastInsertId();
            auditLog('USER_CREATED', 'users', $newId, "Admin created user: {$email}");
            setFlash('success', "User '{$name}' created successfully.");
            header('Location: ' . SITE_URL . '/admin/users.php');
            exit;
        }
    }

    // Update user
    if ($action === 'update_user') {
        $uid    = (int) ($_POST['user_id'] ?? 0);
        $name   = sanitize($_POST['name'] ?? '');
        $email  = sanitizeEmail($_POST['email'] ?? '');
        $roleId = (int) ($_POST['role_id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['active','inactive','pending']) ? $_POST['status'] : 'active';

        if (strlen($name) < 2)     $errors[] = 'Name too short.';
        if (!validateEmail($email)) $errors[] = 'Invalid email.';

        if (empty($errors)) {
            $stmt = db()->prepare('UPDATE users SET name=?, email=?, role_id=?, status=? WHERE id=?');
            $stmt->execute([$name, $email, $roleId, $status, $uid]);

            // Optional password change
            if (!empty($_POST['new_password']) && strlen($_POST['new_password']) >= 8) {
                $hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);
                db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $uid]);
            }

            auditLog('USER_UPDATED', 'users', $uid, "Admin updated user ID {$uid}");
            setFlash('success', 'User updated successfully.');
            header('Location: ' . SITE_URL . '/admin/users.php');
            exit;
        }
    }

    // Toggle status
    if ($action === 'toggle_status') {
        $uid       = (int) ($_POST['user_id'] ?? 0);
        $newStatus = ($_POST['current_status'] ?? '') === 'active' ? 'inactive' : 'active';
        db()->prepare('UPDATE users SET status=? WHERE id=?')->execute([$newStatus, $uid]);
        auditLog('USER_STATUS_CHANGED', 'users', $uid, "Status → {$newStatus}");
        setFlash('success', 'User status updated.');
        header('Location: ' . SITE_URL . '/admin/users.php');
        exit;
    }
}

// ── Filter / Pagination ───────────────────────────────────────
$search     = sanitize($_GET['q'] ?? '');
$roleFilter = (int) ($_GET['role'] ?? 0);
$statusFilter = sanitize($_GET['status'] ?? '');
$page       = max(1, (int) ($_GET['page'] ?? 1));
$perPage    = 15;

$where  = '1=1';
$params = [];

if ($search) {
    $where .= ' AND (u.name LIKE ? OR u.email LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($roleFilter) {
    $where .= ' AND u.role_id = ?';
    $params[] = $roleFilter;
}
if ($statusFilter) {
    $where .= ' AND u.status = ?';
    $params[] = $statusFilter;
}

$countStmt = db()->prepare("SELECT COUNT(*) FROM users u WHERE {$where}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pager = paginate($total, $perPage, $page);

$stmt = db()->prepare(
    "SELECT u.*, r.name AS role_name, r.label AS role_label
     FROM users u JOIN roles r ON r.id = u.role_id
     WHERE {$where}
     ORDER BY u.created_at DESC
     LIMIT {$pager['per_page']} OFFSET {$pager['offset']}"
);
$stmt->execute($params);
$users = $stmt->fetchAll();

$roles = db()->query('SELECT * FROM roles ORDER BY id')->fetchAll();

// Fetch single user for edit modal
$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}
?>
<?php renderHead('User Management'); ?>
<?php renderNav($user); ?>
<?php renderPageHeader('User Management', 'Create, edit and manage system users', 'people-fill'); ?>

<?php echo renderFlash(); ?>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger py-2"><?= sanitize($e) ?></div>
<?php endforeach; ?>

<!-- ── Toolbar ── -->
<div class="card card-gvx mb-3">
  <div class="card-body d-flex flex-wrap gap-2 align-items-center">
    <form class="d-flex gap-2 flex-wrap flex-grow-1" method="GET">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Search name or email…" value="<?= sanitize($search) ?>" style="max-width:220px">
      <select name="role" class="form-select form-select-sm" style="max-width:150px">
        <option value="">All Roles</option>
        <?php foreach ($roles as $r): ?>
          <option value="<?= $r['id'] ?>" <?= $roleFilter === (int)$r['id'] ? 'selected' : '' ?>><?= sanitize($r['label']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="status" class="form-select form-select-sm" style="max-width:140px">
        <option value="">All Status</option>
        <option value="active"   <?= $statusFilter === 'active'   ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        <option value="pending"  <?= $statusFilter === 'pending'  ? 'selected' : '' ?>>Pending</option>
      </select>
      <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
      <a href="<?= SITE_URL ?>/admin/users.php" class="btn btn-sm btn-outline-secondary">Reset</a>
    </form>
    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">
      <i class="bi bi-person-plus-fill me-1"></i> Add User
    </button>
  </div>
</div>

<!-- ── Users Table ── -->
<div class="card card-gvx">
  <div class="card-header-gvx">
    <i class="bi bi-people-fill me-2 text-primary"></i>
    Users <span class="badge bg-secondary ms-1"><?= $total ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-gvx mb-0">
      <thead><tr>
        <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th>
      </tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td class="text-muted small"><?= $u['id'] ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div class="avatar-sm"><?= mb_strtoupper(mb_substr($u['name'], 0, 1)) ?></div>
              <?= sanitize($u['name']) ?>
            </div>
          </td>
          <td><?= sanitize($u['email']) ?></td>
          <td><span class="badge bg-role-<?= $u['role_name'] ?>"><?= sanitize($u['role_label']) ?></span></td>
          <td>
            <span class="badge <?= $u['status'] === 'active' ? 'bg-success' : ($u['status'] === 'pending' ? 'bg-warning text-dark' : 'bg-danger') ?>">
              <?= ucfirst($u['status']) ?>
            </span>
          </td>
          <td><?= formatDate($u['created_at'], 'M d, Y') ?></td>
          <td>
            <a href="?edit=<?= $u['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <?php if ($u['id'] !== $user['id']): ?>
            <form method="POST" class="d-inline">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="action" value="toggle_status">
              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
              <input type="hidden" name="current_status" value="<?= $u['status'] ?>">
              <button type="submit" class="btn btn-xs <?= $u['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                      title="<?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                <i class="bi bi-<?= $u['status'] === 'active' ? 'person-x' : 'person-check' ?>"></i>
              </button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
          <tr><td colspan="7" class="text-center py-4 text-muted">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pager['pages'] > 1): ?>
  <div class="card-body border-top">
    <nav><ul class="pagination pagination-sm mb-0">
      <?php for ($i = 1; $i <= $pager['pages']; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&role=<?= $roleFilter ?>&status=<?= $statusFilter ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul></nav>
  </div>
  <?php endif; ?>
</div>

<!-- ── Create User Modal ── -->
<div class="modal fade" id="createUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <input type="hidden" name="action" value="create_user">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min. 8 characters">
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select name="role_id" class="form-select" required>
                <?php foreach ($roles as $r): ?>
                  <option value="<?= $r['id'] ?>"><?= sanitize($r['label']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1"></i>Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ── Edit User Modal ── -->
<?php if ($editUser): ?>
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <input type="hidden" name="action" value="update_user">
        <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-pencil-fill me-2"></i>Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= sanitize($editUser['name']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= sanitize($editUser['email']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
            <input type="password" name="new_password" class="form-control" minlength="8" placeholder="Min. 8 chars">
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Role</label>
              <select name="role_id" class="form-select">
                <?php foreach ($roles as $r): ?>
                  <option value="<?= $r['id'] ?>" <?= $editUser['role_id'] == $r['id'] ? 'selected' : '' ?>><?= sanitize($r['label']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <?php foreach (['active','inactive','pending'] as $s): ?>
                  <option value="<?= $s ?>" <?= $editUser['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="<?= SITE_URL ?>/admin/users.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
});
</script>
<?php endif; ?>

<?php renderFooter(); ?>
