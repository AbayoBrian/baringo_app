<?php
require_once __DIR__ . '/BaseModel.php';

class AttendanceRecord extends BaseModel {
    protected $table = 'attendance_record';
    protected $primaryKey = 'id';
    
    public function getAttendanceWithFilters($filters = [], $page = 1, $perPage = 25) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereClause = [];
        
        if (!empty($filters['date'])) {
            $whereClause[] = "date = ?";
            $params[] = $filters['date'];
        }
        
        if (!empty($filters['venue'])) {
            $whereClause[] = "venue = ?";
            $params[] = $filters['venue'];
        }
        
        if (!empty($filters['event'])) {
            $whereClause[] = "event = ?";
            $params[] = $filters['event'];
        }
        
        if (!empty($whereClause)) {
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $sql .= " ORDER BY upload_date DESC LIMIT {$perPage} OFFSET {$offset}";
        
        $records = $this->query($sql, $params);
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($whereClause)) {
            $countSql .= " WHERE " . implode(' AND ', $whereClause);
        }
        $totalResult = $this->query($countSql, $params);
        $totalRecords = $totalResult[0]['total'];
        
        return [
            'records' => $records,
            'total_records' => $totalRecords,
            'total_pages' => ceil($totalRecords / $perPage),
            'current_page' => $page
        ];
    }
    
    public function getVenues() {
        $sql = "SELECT DISTINCT venue FROM {$this->table} WHERE venue IS NOT NULL ORDER BY venue";
        $result = $this->query($sql);
        return array_column($result, 'venue');
    }
    
    public function getEvents() {
        $sql = "SELECT DISTINCT event FROM {$this->table} WHERE event IS NOT NULL ORDER BY event";
        $result = $this->query($sql);
        return array_column($result, 'event');
    }
    
    public function getAttendanceStats($filters = []) {
        $whereClause = [];
        $params = [];
        
        if (!empty($filters['venue'])) {
            $whereClause[] = "venue = ?";
            $params[] = $filters['venue'];
        }
        
        if (!empty($filters['event'])) {
            $whereClause[] = "event = ?";
            $params[] = $filters['event'];
        }
        
        if (!empty($filters['start_date'])) {
            $whereClause[] = "date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $whereClause[] = "date <= ?";
            $params[] = $filters['end_date'];
        }
        
        $whereSql = !empty($whereClause) ? " WHERE " . implode(' AND ', $whereClause) : "";
        
        // Trend data
        $trendSql = "SELECT date, COUNT(*) as count FROM {$this->table}{$whereSql} GROUP BY date ORDER BY date";
        $trendData = $this->query($trendSql, $params);
        
        // Venue data
        $venueSql = "SELECT venue, COUNT(*) as count FROM {$this->table}{$whereSql} GROUP BY venue ORDER BY count DESC";
        $venueData = $this->query($venueSql, $params);
        
        // Event data
        $eventSql = "SELECT event, COUNT(*) as count FROM {$this->table}{$whereSql} GROUP BY event ORDER BY count DESC";
        $eventData = $this->query($eventSql, $params);
        
        return [
            'trend' => $trendData,
            'venues' => $venueData,
            'events' => $eventData
        ];
    }
}
?>
