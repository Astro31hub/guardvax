<?php
// index.php
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