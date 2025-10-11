<?php
/**
 * Database Connection Test
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/BaseModel.php';

echo "=============================================\n";
echo "IMS Baringo CIDU Database Connection Test\n";
echo "=============================================\n\n";

try {
    // Test database connection
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
    
    // Test if tables exist
    $tables = ['users', 'subcounties', 'irrigation_schemes', 'assessments', 'documents', 'photos', 'gps_data', 'attendance_record'];
    $existing_tables = [];
    
    $stmt = $pdo->query("SHOW TABLES");
    $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nTable Status:\n";
    foreach ($tables as $table) {
        if (in_array($table, $existing_tables)) {
            echo "✓ $table - exists\n";
        } else {
            echo "✗ $table - missing\n";
        }
    }
    
    // Test data counts
    echo "\nData Counts:\n";
    $counts = [
        'users' => 'SELECT COUNT(*) as count FROM users',
        'subcounties' => 'SELECT COUNT(*) as count FROM subcounties',
        'irrigation_schemes' => 'SELECT COUNT(*) as count FROM irrigation_schemes',
        'assessments' => 'SELECT COUNT(*) as count FROM assessments'
    ];
    
    foreach ($counts as $table => $query) {
        $stmt = $pdo->query($query);
        $result = $stmt->fetch();
        echo "✓ $table: {$result['count']} records\n";
    }
    
    // Test default users
    echo "\nDefault Users:\n";
    $stmt = $pdo->query("SELECT username, role FROM users");
    $users = $stmt->fetchAll();
    foreach ($users as $user) {
        echo "✓ {$user['username']} ({$user['role']})\n";
    }
    
    echo "\n=============================================\n";
    echo "Database test completed successfully!\n";
    echo "Your application is ready to use.\n";
    echo "=============================================\n";
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "\nPlease check your database configuration in .env file\n";
    echo "and make sure the database has been imported.\n";
    exit(1);
}
?>
