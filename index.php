<?php
require_once __DIR__ . '/config/db.php';

if (isset($_GET['resetadmin'])) {
    $hash = password_hash('Admin123', PASSWORD_BCRYPT);
    $stmt = db()->prepare("UPDATE users SET password = ? WHERE email = 'admin@guardvax.com'");
    $stmt->execute([$hash]);
    echo "Done! Login with Admin123";
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header('Location: ' . SITE_URL . '/home.php');
} else {
    require_once __DIR__ . '/includes/auth.php';
    $user = getCurrentUser();
    header('Location: ' . SITE_URL . '/' . dashboardPath($user['role_name']));
}
exit;