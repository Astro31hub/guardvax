<?php
// config/db.php — GUARDVAX — Railway-Optimized

// ── DB Session Handler (Railway-safe, replaces file sessions) ─
class DBSessionHandler implements SessionHandlerInterface {
    private PDO $pdo;

    public function open($path, $name): bool {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('MYSQLHOST') ?: 'hopper.proxy.rlwy.net',
            getenv('MYSQLPORT') ?: '28897',
            getenv('MYSQLDATABASE') ?: 'railway'
        );
        $this->pdo = new PDO($dsn,
            getenv('MYSQLUSER') ?: 'root',
            getenv('MYSQLPASSWORD') ?: '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return true;
    }

    public function close(): bool { return true; }

    public function read($id): string {
        $stmt = $this->pdo->prepare('SELECT data FROM sessions WHERE id = ? AND expires_at > NOW()');
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: '';
    }

    public function write($id, $data): bool {
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $stmt = $this->pdo->prepare('
            INSERT INTO sessions (id, data, expires_at) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE data = VALUES(data), expires_at = VALUES(expires_at)
        ');
        return $stmt->execute([$id, $data, $expires]);
    }

    public function destroy($id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function gc($max_lifetime): int|false {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE expires_at < NOW()');
        $stmt->execute();
        return $stmt->rowCount();
    }
}

// Register DB session handler BEFORE anything else
session_set_save_handler(new DBSessionHandler(), true);

// ── Email Configuration ──────────────────────────────────────
define('MAIL_FROM',     getenv('MAIL_FROM')     ?: 'tisoyangelo31@gmail.com');
define('MAIL_HOST',     getenv('MAIL_HOST')     ?: 'smtp-relay.brevo.com');
define('MAIL_PORT',     (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: 'a88eed001@smtp-brevo.com');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: 'xsmtpsib-9c7c9c71037408e93bf93d93b982c06baf6684775957698dd19012071f424748-jaBuTCY7iKMod8EK');

// ── Database Configuration ───────────────────────────────────
define('DB_HOST',    getenv('MYSQLHOST')     ?: 'hopper.proxy.rlwy.net');
define('DB_PORT',    getenv('MYSQLPORT')     ?: '28897');
define('DB_NAME',    getenv('MYSQLDATABASE') ?: 'railway');
define('DB_USER',    getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS',    getenv('MYSQLPASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── Application Configuration ────────────────────────────────
define('SITE_NAME',  'GuardVAX');
define('SITE_URL',   getenv('SITE_URL') ?: 'https://guardvax-production.up.railway.app');
define('SITE_EMAIL', getenv('MAIL_FROM') ?: 'tisoyangelo31@gmail.com');

// ── Session & Security ───────────────────────────────────────
define('SESSION_LIFETIME', 3600);
define('VERIFY_CODE_TTL',  15);
define('DOMPDF_PATH', __DIR__ . '/../vendor/dompdf/autoload.inc.php');
define('DEBUG_MODE', false);

// ── PDO Singleton ────────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $offset = (new DateTime())->format('P');
            $pdo->exec("SET time_zone='{$offset}'");
        } catch (PDOException $e) {
            error_log("[DATABASE ERROR] " . $e->getMessage());
            if (DEBUG_MODE) {
                die('<b>DB Error:</b> ' . htmlspecialchars($e->getMessage()));
            } else {
                die('<div style="padding:2rem;color:red">Database Connection Error. Check server logs.</div>');
            }
        }
    }
    return $pdo;
}

function testDatabaseConnection(): bool {
    try { db()->query("SELECT 1"); return true; }
    catch (Exception $e) { error_log("[DB TEST FAILED] " . $e->getMessage()); return false; }
}