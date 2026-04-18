
<?php
// ============================================================
// config/db.php — Database Connection (PDO)
// GuardVAX Hospital System
// ============================================================
// Email / SMTP settings
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'tisoyangelo31@gmail.com');
define('MAIL_PASSWORD', 'sqpb irll urps ogkl');  // ← new one after revoking
define('MAIL_FROM',     'tisoyangelo31@gmail.com');

define('DB_HOST', 'localhost');
define('DB_NAME', 'guardvax');
define('DB_USER', 'root');         // Change for production
define('DB_PASS', '');             // Change for production
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_NAME',  'GuardVAX');
define('SITE_URL',   'http://localhost/guardvax');   // Change for production
define('SITE_EMAIL', 'noreply@guardvax.com');        // Change for production

// Session lifetime (seconds)
define('SESSION_LIFETIME', 3600);   // 1 hour

// Verification code TTL (minutes)
define('VERIFY_CODE_TTL', 15);

// dompdf path
define('DOMPDF_PATH', __DIR__ . '/../vendor/dompdf/autoload.inc.php');

/**
 * Returns a singleton PDO connection.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

            // ✅ FIX: Sync MySQL timezone to match PHP
            $offset = (new DateTime())->format('P');  // gets +08:00
            $pdo->exec("SET time_zone='{$offset}'");

        } catch (PDOException $e) {
            die('<div style="font-family:monospace;padding:2rem;color:red;">
                  <strong>Database connection failed.</strong><br>
                  ' . htmlspecialchars($e->getMessage()) . '
                 </div>');
        }
    }

    return $pdo;
}


