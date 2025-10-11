<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function findByUsername($username) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public function createUser($username, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->create([
            'username' => $username,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }
}
?>
