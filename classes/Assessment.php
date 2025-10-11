<?php
require_once __DIR__ . '/BaseModel.php';

class Assessment extends BaseModel {
    protected $table = 'assessments';
    protected $primaryKey = 'assessment_id';
    
    public function getAssessmentsWithScheme() {
        $sql = "SELECT a.*, s.scheme_name, sc.subcounty_name, sc.subcounty_id
                FROM {$this->table} a
                LEFT JOIN irrigation_schemes s ON a.scheme_id = s.scheme_id
                LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id
                ORDER BY a.assessment_date DESC";
        return $this->query($sql);
    }
    
    public function getAssessmentDetails($assessmentId) {
        $sql = "SELECT a.*, s.*, sc.subcounty_name
                FROM {$this->table} a
                LEFT JOIN irrigation_schemes s ON a.scheme_id = s.scheme_id
                LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id
                WHERE a.assessment_id = ?";
        return $this->query($sql, [$assessmentId]);
    }
    
    public function getAssessmentsByScheme($schemeId) {
        return $this->findAll(['scheme_id' => $schemeId], 'assessment_date DESC');
    }
    
    public function getAssessmentsByDateRange($startDate, $endDate) {
        $sql = "SELECT a.*, s.scheme_name, sc.subcounty_name
                FROM {$this->table} a
                LEFT JOIN irrigation_schemes s ON a.scheme_id = s.scheme_id
                LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id
                WHERE a.assessment_date BETWEEN ? AND ?
                ORDER BY a.assessment_date DESC";
        return $this->query($sql, [$startDate, $endDate]);
    }
}
?>
