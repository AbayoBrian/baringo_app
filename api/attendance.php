<?php
/**
 * Attendance API
 * IMS Baringo CIDU - PHP Version
 */

require_once '../config/config.php';
require_once '../classes/AttendanceRecord.php';

header('Content-Type: application/json');

$attendanceModel = new AttendanceRecord();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = intval($_GET['page'] ?? 1);
    $perPage = intval($_GET['per_page'] ?? 25);
    $filters = [
        'date' => $_GET['date'] ?? null,
        'venue' => $_GET['venue'] ?? null,
        'event' => $_GET['event'] ?? null
    ];
    
    $result = $attendanceModel->getAttendanceWithFilters($filters, $page, $perPage);
    echo json_encode($result);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    if (!isset($_FILES['files']) || empty($_FILES['files']['name'][0])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No files selected']);
        exit;
    }
    
    $venue = sanitize_input($_POST['venue'] ?? '');
    $date = $_POST['date'] ?? '';
    $event = sanitize_input($_POST['event'] ?? '');
    
    if (empty($venue) || empty($date)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Venue and date are required']);
        exit;
    }
    
    $uploadedFiles = [];
    $errors = [];
    
    foreach ($_FILES['files']['name'] as $key => $filename) {
        if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $filename,
                'type' => $_FILES['files']['type'][$key],
                'tmp_name' => $_FILES['files']['tmp_name'][$key],
                'error' => $_FILES['files']['error'][$key],
                'size' => $_FILES['files']['size'][$key]
            ];
            
            if (allowed_file($filename)) {
                $uploadDir = UPLOAD_FOLDER . '/attendance';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $uniqueFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
                $filepath = $uploadDir . '/' . $uniqueFilename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $recordData = [
                        'filename' => $filename,
                        'filepath' => $filepath,
                        'venue' => $venue,
                        'date' => $date,
                        'event' => $event,
                        'page_count' => 0
                    ];
                    
                    $recordId = $attendanceModel->create($recordData);
                    $uploadedFiles[] = $filename;
                } else {
                    $errors[] = "Failed to upload {$filename}";
                }
            } else {
                $errors[] = "File type not allowed: {$filename}";
            }
        }
    }
    
    if (empty($uploadedFiles)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All files failed to upload', 'errors' => $errors]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Successfully uploaded ' . count($uploadedFiles) . ' files',
            'files' => $uploadedFiles,
            'errors' => $errors
        ]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
