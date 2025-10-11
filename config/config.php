<?php
/**
 * Application Configuration
 * IMS Baringo CIDU - PHP Version
 */

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Application Settings
define('APP_NAME', 'IMS Baringo CIDU');
define('APP_VERSION', '2.0.0');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

// Security Settings
define('SECRET_KEY', $_ENV['SECRET_KEY'] ?? 'your-secret-key-here-change-in-production');
define('SESSION_LIFETIME', 3600 * 24);

// File Upload Settings
define('UPLOAD_FOLDER', __DIR__ . '/../assets/uploads');
define('MAX_FILE_SIZE', 10 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['pdf', 'png', 'jpg', 'jpeg', 'gif']);

// Authentication Settings
define('AGENT_USERNAME', $_ENV['AGENT_USERNAME'] ?? 'Agent');
define('AGENT_PASSWORD', $_ENV['AGENT_PASSWORD'] ?? 'agent@2025!');
define('ADMIN_USERNAME', $_ENV['ADMIN_USERNAME'] ?? 'CiduAdmin');
define('ADMIN_PASSWORD', $_ENV['ADMIN_PASSWORD'] ?? 'admin@2025#');

// Error Reporting
if ($_ENV['APP_DEBUG'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Helper Functions
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

function is_authenticated() {
    return isset($_SESSION['user_role']);
}

function require_auth($required_role = null) {
    if (!is_authenticated()) {
        flash_message('You must be logged in to access this page.', 'danger');
        redirect('/login.php');
    }
    
    if ($required_role && $_SESSION['user_role'] !== $required_role) {
        flash_message('You are not authorized to access this page.', 'danger');
        redirect('/login.php');
    }
}

function format_file_size($size) {
    if ($size == 0) return '0 Bytes';
    $units = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($size, 1024));
    return round($size / pow(1024, $i), 2) . ' ' . $units[$i];
}

function allowed_file($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

// Start session
session_start();
?>
