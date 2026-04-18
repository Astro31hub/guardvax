<?php
// ============================================================
// includes/auth.php — Authentication Guards & Helpers
// GuardVAX Hospital System
// ============================================================

require_once __DIR__ . '/functions.php';

/**
 * Authenticate user credentials.
 * Returns user array on success, or null on failure.
 */
function authenticateUser(string $email, string $password): ?array
{
    $stmt = db()->prepare(
        'SELECT u.*, r.name AS role_name
         FROM users u
         JOIN roles r ON r.id = u.role_id
         WHERE u.email = ? AND u.status != "inactive"
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return null;
    }

    return $user;
}

/**
 * Log in a user — creates session.
 */
function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role']       = $user['role_name'];
}

/**
 * Register a new user (nurse or patient).
 * Returns new user ID or throws PDOException.
 */
function registerUser(string $name, string $email, string $password, string $role): int
{
    $stmt = db()->prepare('SELECT id FROM roles WHERE name = ?');
    $stmt->execute([$role]);
    $roleRow = $stmt->fetch();
    if (!$roleRow) throw new InvalidArgumentException('Invalid role.');

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt = db()->prepare(
        'INSERT INTO users (name, email, password, role_id, status, email_verified)
         VALUES (?, ?, ?, ?, "pending", 0)'
    );
    $stmt->execute([$name, $email, $hash, $roleRow['id']]);
    return (int) db()->lastInsertId();
}

/**
 * Check if email is already registered.
 */
function emailExists(string $email): bool
{
    $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    return (bool) $stmt->fetch();
}

/**
 * Activate account after email verification.
 */
function activateUser(int $userId): void
{
    $stmt = db()->prepare(
        'UPDATE users SET email_verified = 1, status = "active" WHERE id = ?'
    );
    $stmt->execute([$userId]);
}

/**
 * Role-specific dashboard paths.
 */
function dashboardPath(string $role): string
{
    return match($role) {
        'admin'   => '/admin/index.php',
        'nurse'   => '/nurse/index.php',
        'patient' => '/patient/index.php',
        default   => '/auth/login.php',
    };
}

/**
 * Destroy session and redirect to login.
 */
function logout(): void
{
    auditLog('LOGOUT');
    session_destroy();
    header('Location: ' . SITE_URL . '/auth/login.php?msg=logged_out');
    exit;
}

/**
 * Enforce login + optional role guard.
 */
function guard(string ...$allowedRoles): array
{
    startSecureSession();

    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }

    $user = getCurrentUser();
    if (!$user) {
        session_destroy();
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit;
    }

    if (!empty($allowedRoles) && !in_array($user['role_name'], $allowedRoles, true)) {
        http_response_code(403);
        include __DIR__ . '/../includes/403.php';
        exit;
    }

    // Refresh session idle timer
    $_SESSION['last_activity'] = time();

    return $user;
}
