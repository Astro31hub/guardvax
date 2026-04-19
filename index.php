<?php
// index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header('Location: ' . SITE_URL . '/home.php');
} else {
    require_once __DIR__ . '/includes/auth.php';
    $user = getCurrentUser();
    header('Location: ' . SITE_URL . '/' . dashboardPath($user['role_name']));
}
exit;