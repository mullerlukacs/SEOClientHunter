<?php
/**
 * SEO Client Hunter - Main Configuration File
 * Production-ready settings for cPanel, Shared Hosting, VPS, and Cloud Run.
 */

// Error reporting settings
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '1');
}

// Session security initialization
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    // In HTTPS environments, enable secure cookies
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// Define root directories
define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('CONFIG_DIR', ROOT_DIR . '/config');
define('SRC_DIR', ROOT_DIR . '/src');
define('VIEWS_DIR', ROOT_DIR . '/views');
define('DATABASE_DIR', ROOT_DIR . '/database');
define('LOGS_DIR', ROOT_DIR . '/logs');
define('UPLOADS_DIR', ROOT_DIR . '/uploads');

// Application Constants
define('APP_NAME', 'SEO Client Hunter');
define('APP_VERSION', '2.4.0');

// Database Configuration
// On cPanel/VPS, fill in your MySQL credentials here or via environment variables
define('DB_TYPE', getenv('DB_TYPE') ?: 'auto'); // 'mysql', 'sqlite', or 'auto'
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'seoclienthunter');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('SQLITE_PATH', DATABASE_DIR . '/seoclienthunter.sqlite');

// App Base URL detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
define('BASE_URL', rtrim(getenv('APP_URL') ?: ($protocol . $host), '/'));

// Security Salt / Encryption Key
define('APP_KEY', getenv('APP_KEY') ?: 'seo_hunter_secure_salt_key_983141592653589');

// CSRF Token Helper
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string {
    return $_SESSION['csrf_token'] ?? '';
}

function verify_csrf(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Helper to escape HTML safely
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
