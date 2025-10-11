<?php
/**
 * Database Configuration for Render
 * IMS Baringo CIDU - PHP Version
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $pdo;
    
    public function __construct() {
        // Load environment variables or use defaults
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'ims_baringo';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->port = $_ENV['DB_PORT'] ?? '5432';
    }
    
    public function getConnection() {
        if ($this->pdo === null) {
            try {
                // Use PostgreSQL for Render
                $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=require";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                // Fallback to MySQL if PostgreSQL fails
                try {
                    $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ];
                    
                    $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
                } catch (PDOException $e2) {
                    throw new PDOException($e2->getMessage(), (int)$e2->getCode());
                }
            }
        }
        
        return $this->pdo;
    }
    
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }
    
    public function commit() {
        return $this->getConnection()->commit();
    }
    
    public function rollback() {
        return $this->getConnection()->rollback();
    }
}
?>
