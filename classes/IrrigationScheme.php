<?php
require_once __DIR__ . '/BaseModel.php';

class IrrigationScheme extends BaseModel {
    protected $table = 'irrigation_schemes';
    protected $primaryKey = 'scheme_id';
    
    public function getSchemesWithSubcounty() {
        $sql = "SELECT s.*, sc.subcounty_name 
                FROM {$this->table} s 
                LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id 
                ORDER BY s.scheme_name";
        return $this->query($sql);
    }
    
    public function getSchemesBySubcounty($subcountyId) {
        return $this->findAll(['subcounty_id' => $subcountyId], 'scheme_name');
    }
    
    public function getSchemeStats() {
        $sql = "SELECT 
                    scheme_type,
                    COUNT(*) as total
                FROM {$this->table} 
                WHERE scheme_type IS NOT NULL 
                GROUP BY scheme_type";
        return $this->query($sql);
    }
    
    public function getRegistrationStats() {
        $sql = "SELECT 
                    registration_status,
                    COUNT(*) as total
                FROM {$this->table} 
                WHERE registration_status IS NOT NULL 
                GROUP BY registration_status";
        return $this->query($sql);
    }
    
    public function getSchemesByStatus() {
        $sql = "SELECT 
                    current_status,
                    COUNT(*) as total
                FROM {$this->table} 
                WHERE current_status IS NOT NULL 
                GROUP BY current_status";
        return $this->query($sql);
    }
    
    public function getSchemesWithGPS() {
        $sql = "SELECT s.*, sc.subcounty_name, g.latitude, g.longitude
                FROM {$this->table} s
                LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id
                LEFT JOIN gps_data g ON s.scheme_id = g.scheme_id
                WHERE g.latitude IS NOT NULL AND g.longitude IS NOT NULL";
        return $this->query($sql);
    }
}
?>
