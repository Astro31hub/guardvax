<?php
require_once __DIR__ . '/config/db.php';

$newPassword = 'Admin@1234'; // change this to what you want
$hash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = db()->prepare("UPDATE users SET password = ? WHERE email = 'admin@guardvax.com'");
$stmt->execute([$hash]);

echo "Done! Password is now: " . $newPassword;