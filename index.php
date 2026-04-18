<?php
// index.php — Root redirect
require_once __DIR__ . '/includes/auth.php';
startSecureSession();

if (isLoggedIn()) {
    $user = getCurrentUser();
    redirect(dashboardPath($user['role_name']));
} else {
    // Show homepage
    include __DIR__ . '/home.php';
    exit;
}
