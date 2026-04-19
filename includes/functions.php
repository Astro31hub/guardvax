<?php
// ============================================================
// includes/functions.php — Core Helper Functions
// GuardVAX Hospital System
// ============================================================
require_once __DIR__ . '/../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config/db.php';

// ── Session ─────────────────────────────────────────────────

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser(): ?array
{
    if (!isLoggedIn()) return null;
    $stmt = db()->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(string $role = ''): void
{
    startSecureSession();
    if (!isLoggedIn()) {
        redirect('/auth/login.php');
    }
    if ($role && ($_SESSION['role'] ?? '') !== $role) {
        redirect('/auth/login.php?error=access_denied');
    }
}

function redirect(string $path): void
{
    header('Location: ' . SITE_URL . $path);
    exit;
}

// ── CSRF ─────────────────────────────────────────────────────

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

// ── Input Sanitization ───────────────────────────────────────

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail(string $email): string
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ── Verification Codes ───────────────────────────────────────

function generateVerificationCode(int $userId, string $purpose = 'email_verify'): string
{
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime('+' . VERIFY_CODE_TTL . ' minutes'));

    $stmt = db()->prepare('UPDATE verification_codes SET used = 1 WHERE user_id = ? AND purpose = ? AND used = 0');
    $stmt->execute([$userId, $purpose]);

    $stmt = db()->prepare('INSERT INTO verification_codes (user_id, code, purpose, expires_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $code, $purpose, $expires]);

    return $code;
}

function verifyCode(int $userId, string $code, string $purpose = 'email_verify'): bool
{
    $stmt = db()->prepare(
        'SELECT id FROM verification_codes
         WHERE user_id = ? AND code = ? AND purpose = ? AND used = 0 AND expires_at > NOW()
         ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([$userId, $code, $purpose]);
    $row = $stmt->fetch();

    if (!$row) return false;

    $stmt = db()->prepare('UPDATE verification_codes SET used = 1 WHERE id = ?');
    $stmt->execute([$row['id']]);
    return true;
}

// ── Audit Logging ────────────────────────────────────────────

function auditLog(string $action, ?string $table = null, ?int $recordId = null, ?string $desc = null): void
{
    $userId = $_SESSION['user_id'] ?? null;
    $ip     = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = db()->prepare(
        'INSERT INTO audit_logs (user_id, action, table_name, record_id, description, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $action, $table, $recordId, $desc, $ip]);
}

// ── Flash Messages ───────────────────────────────────────────

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function renderFlash(): string
{
    $flash = getFlash();
    if (!$flash) return '';
    $type = $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'danger' ? 'danger' : $flash['type']);
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
         . htmlspecialchars($flash['message'])
         . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

// ── Patient Code Generator ───────────────────────────────────

function generatePatientCode(): string
{
    $stmt = db()->query('SELECT COUNT(*) AS cnt FROM patients');
    $count = (int) $stmt->fetchColumn();
    return 'GVX-' . str_pad((string)($count + 1), 6, '0', STR_PAD_LEFT);
}

// ── Pagination ───────────────────────────────────────────────

function paginate(int $total, int $perPage, int $current): array
{
    $pages = (int) ceil($total / $perPage);
    return [
        'total'    => $total,
        'per_page' => $perPage,
        'current'  => $current,
        'pages'    => $pages,
        'offset'   => ($current - 1) * $perPage,
    ];
}

// ── Date Helpers ─────────────────────────────────────────────

function formatDate(string $date, string $format = 'M d, Y'): string
{
    return $date ? date($format, strtotime($date)) : 'N/A';
}

function calculateAge(string $dob): int
{
    return (int) date_diff(date_create($dob), date_create('today'))->y;
}

// ── Email Sender ─────────────────────────────────────────────

function sendEmail(string $to, string $subject, string $body): bool
{
    $apiKey = getenv('BREVO_API_KEY');
    
    $data = [
        'sender'      => ['name' => 'GuardVAX', 'email' => 'magtisoy@tip.edu.ph'],
        'to'          => [['email' => $to]],
        'subject'     => $subject,
        'htmlContent' => $body
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Write debug to file
    $debug = "API Key: " . substr($apiKey, 0, 20) . "...\n";
    $debug .= "HTTP Code: $httpCode\n";
    $debug .= "Curl Error: $curlError\n";
    $debug .= "Response: $response\n";
    $debug .= "To: $to\n";
    file_put_contents('/tmp/brevo_debug.txt', $debug);

    if ($httpCode !== 201) {
        error_log("Brevo FAILED - Code: $httpCode Error: $curlError Response: $response");
        return false;
    }

    return true;
}

function sendVerificationEmail(string $toEmail, string $toName, string $code, string $purpose = 'email_verify'): bool
{
    $subject = match($purpose) {
        '2fa_login'      => SITE_NAME . ' — Login Verification Code',
        'password_reset' => SITE_NAME . ' — Password Reset Code',
        default          => SITE_NAME . ' — Email Verification',
    };

    $body = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:40px">
    <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;box-shadow:0 2px 12px rgba(0,0,0,.08)">
      <div style="text-align:center;margin-bottom:24px">
        <span style="font-size:2rem;color:#0d6efd">🛡️</span>
        <h2 style="color:#0d6efd;margin:8px 0">GuardVAX</h2>
      </div>
      <p>Hello <strong>' . htmlspecialchars($toName) . '</strong>,</p>
      <p>Your verification code is:</p>
      <div style="text-align:center;margin:24px 0">
        <span style="font-size:2.5rem;font-weight:700;letter-spacing:8px;color:#0d6efd;background:#e7f0ff;padding:16px 32px;border-radius:8px">' . htmlspecialchars($code) . '</span>
      </div>
      <p>This code expires in <strong>' . VERIFY_CODE_TTL . ' minutes</strong>. Do not share it with anyone.</p>
      <hr style="margin:24px 0">
      <p style="font-size:.8rem;color:#888">If you did not request this, please ignore this email or contact support.</p>
    </div></body></html>';

    return sendEmail($toEmail, $subject, $body);
}