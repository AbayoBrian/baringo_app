<?php
/**
 * Setup Script for IMS Baringo CIDU - PHP Version
 * Run this script to set up the application
 */

echo "IMS Baringo CIDU - PHP Setup Script\n";
echo "=====================================\n\n";

// Check PHP version
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die("Error: PHP 8.0 or higher is required. Current version: " . PHP_VERSION . "\n");
}

echo "✓ PHP version check passed\n";

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'gd', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    die("Error: Missing required PHP extensions: " . implode(', ', $missing_extensions) . "\n");
}

echo "✓ Required PHP extensions check passed\n";

// Check if .env file exists
if (!file_exists('.env')) {
    die("Error: .env file not found. Please copy .env.example to .env and configure it.\n");
}

echo "✓ Environment file found\n";

// Load environment variables
$lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Test database connection
try {
    $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
    echo "✓ Database connection successful\n";
} catch (PDOException $e) {
    die("Error: Database connection failed: " . $e->getMessage() . "\n");
}

// Check if tables exist
$tables = ['users', 'subcounties', 'irrigation_schemes', 'assessments', 'documents', 'photos', 'gps_data', 'attendance_record'];
$existing_tables = [];

try {
    $stmt = $pdo->query("SHOW TABLES");
    $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error: Could not check existing tables: " . $e->getMessage() . "\n");
}

$missing_tables = array_diff($tables, $existing_tables);

if (!empty($missing_tables)) {
    echo "⚠ Missing tables: " . implode(', ', $missing_tables) . "\n";
    echo "Please run: mysql -u {$_ENV['DB_USER']} -p {$_ENV['DB_NAME']} < config/schema.sql\n";
} else {
    echo "✓ All required tables exist\n";
}

// Check upload directories
$upload_dirs = ['assets/uploads', 'assets/uploads/documents', 'assets/uploads/photos', 'assets/uploads/attendance'];

foreach ($upload_dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Created directory: $dir\n";
        } else {
            echo "⚠ Could not create directory: $dir\n";
        }
    } else {
        echo "✓ Directory exists: $dir\n";
    }
}

// Check directory permissions
foreach ($upload_dirs as $dir) {
    if (is_writable($dir)) {
        echo "✓ Directory is writable: $dir\n";
    } else {
        echo "⚠ Directory is not writable: $dir\n";
        echo "Please run: chmod 755 $dir\n";
    }
}

echo "\nSetup completed!\n";
echo "You can now access the application at: " . ($_ENV['APP_URL'] ?? 'http://localhost') . "\n";
echo "\nDefault login credentials:\n";
echo "Agent: Agent / agent@2025!\n";
echo "Admin: CiduAdmin / admin@2025#\n";
?>
