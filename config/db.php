<?php
// config/db.php — Railway-compatible version
// All sensitive values come from environment variables

// ── Email config ────────────────────────────────────────────
define('MAIL_HOST',     getenv('MAIL_HOST')     ?: 'smtp.gmail.com');
define('MAIL_PORT',     (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('MAIL_FROM',     getenv('MAIL_FROM')     ?: '');

// ── Database config ─────────────────────────────────────────
define('DB_HOST',    getenv('MYSQLHOST')     ?: 'localhost');
define('DB_NAME',    getenv('MYSQLDATABASE') ?: 'guardvax');
define('DB_USER',    getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS',    getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT',    getenv('MYSQLPORT')     ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// ── App config ───────────────────────────────────────────────
define('SITE_NAME',  'GuardVAX');
define('SITE_URL',   getenv('SITE_URL') ?: 'http://localhost/guardvax');
define('SITE_EMAIL', getenv('MAIL_FROM') ?: 'noreply@guardvax.com');

define('SESSION_LIFETIME', 3600);
define('VERIFY_CODE_TTL',  15);
define('DOMPDF_PATH', __DIR__ . '/../vendor/dompdf/autoload.inc.php');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Timezone fix
            $offset = (new DateTime())->format('P');
            $pdo->exec("SET time_zone='{$offset}'");
        } catch (PDOException $e) {
            die('<div style="padding:2rem;color:red;font-family:monospace">
                <strong>DB Error:</strong> ' . htmlspecialchars($e->getMessage()) . '
            </div>');
        }
    }
    return $pdo;
}