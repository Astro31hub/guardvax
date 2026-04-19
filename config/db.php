<?php
// config/db.php — GUARDVAX — Railway-Optimized
// Uses your EXACT Railway environment variables

// ── Email Configuration ──────────────────────────────────────
define('MAIL_FROM',     getenv('MAIL_FROM')     ?: 'tisoyangelo31@gmail.com');
define('MAIL_HOST',     getenv('MAIL_HOST')     ?: 'smtp.gmail.com');
define('MAIL_PORT',     (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: 'tisoyangelo31@gmail.com');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: 'sqpb irll urps ogkl');

// ── Database Configuration — RAILWAY (YOUR EXACT VARIABLES) ──
// These match EXACTLY what you showed in the screenshot
define('DB_HOST',    getenv('MYSQLHOST')     ?: 'hopper.proxy.rlwy.net');
define('DB_PORT',    getenv('MYSQLPORT')     ?: '28897');
define('DB_NAME',    getenv('MYSQLDATABASE') ?: 'railway');
define('DB_USER',    getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS',    getenv('MYSQLPASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── Application Configuration ────────────────────────────────
define('SITE_NAME',  'GuardVAX');
define('SITE_URL',   getenv('SITE_URL') ?: 'https://guardvax-production.up.railway.app');
define('SITE_EMAIL', getenv('MAIL_FROM') ?: 'noreply@guardvax.com');

// ── Session & Security ───────────────────────────────────────
define('SESSION_LIFETIME', 3600);  // 1 hour
define('VERIFY_CODE_TTL',  15);    // 15 minutes
define('DOMPDF_PATH', __DIR__ . '/../vendor/dompdf/autoload.inc.php');
define('DEBUG_MODE', false);  // Set to true only for troubleshooting

/**
 * Database Connection using PDO (Singleton Pattern)
 * Handles all database operations for GuardVAX
 */
function db(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // Build connection string
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );
            
            // PDO options for security and error handling
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // Attempt connection
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Sync PHP timezone with MySQL
            $offset = (new DateTime())->format('P');
            $pdo->exec("SET time_zone='{$offset}'");
            
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("[DATABASE ERROR] " . $e->getMessage());
            error_log("[DATABASE CONFIG] Host: " . DB_HOST . ":" . DB_PORT . " | User: " . DB_USER . " | DB: " . DB_NAME);
            
            // Display detailed error in debug mode
            if (DEBUG_MODE === true) {
                die('<div style="padding:2rem;color:#d32f2f;font-family:monospace;background:#ffebee;border:1px solid #d32f2f;border-radius:4px">
                    <h2 style="color:#d32f2f;margin-top:0">🔴 Database Connection Failed</h2>
                    <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                    <hr style="border:none;border-top:1px solid #d32f2f">
                    <p><strong>Configuration:</strong></p>
                    <ul style="font-size:12px;line-height:1.6">
                        <li>Host: ' . DB_HOST . ':' . DB_PORT . '</li>
                        <li>Database: ' . DB_NAME . '</li>
                        <li>User: ' . DB_USER . '</li>
                        <li>Charset: ' . DB_CHARSET . '</li>
                    </ul>
                    <hr style="border:none;border-top:1px solid #d32f2f">
                    <p style="font-size:12px;color:#666">
                        <strong>Tip:</strong> Verify Railway MySQL service is running and variables match:
                        <br>MYSQLHOST, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD, MYSQLPORT
                    </p>
                </div>');
            } else {
                // Silent fail with basic message in production
                die('<div style="padding:2rem;color:red;font-family:monospace">
                    <strong>Database Connection Error:</strong> Check server logs for details.
                </div>');
            }
        }
    }
    
    return $pdo;
}

// Optional: Connection test function (useful for debugging)
function testDatabaseConnection(): bool {
    try {
        db()->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        error_log("[DB TEST FAILED] " . $e->getMessage());
        return false;
    }
}