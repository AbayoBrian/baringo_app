<?php
/**
 * Assessment Submission API
 * IMS Baringo CIDU - PHP Version
 */

require_once '../config/config.php';
require_once '../classes/IrrigationScheme.php';
require_once '../classes/Assessment.php';
require_once '../classes/BaseModel.php';

// Require agent authentication
require_auth('agent');

// Set content type for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

try {
    $schemeModel = new IrrigationScheme();
    $assessmentModel = new Assessment();
    $subcountyModel = new BaseModel();
    $subcountyModel->table = 'subcounties';
    $gpsModel = new BaseModel();
    $gpsModel->table = 'gps_data';
    $documentModel = new BaseModel();
    $documentModel->table = 'documents';
    $photoModel = new BaseModel();
    $photoModel->table = 'photos';
    
    // Validate required fields
    $requiredFields = [
        'agentName' => 'Field Agent Name',
        'visitDate' => 'Assessment Date',
        'subcounty' => 'Subcounty',
        'scheme' => 'Irrigation Scheme',
        'gpsCoordinates' => 'GPS Coordinates',
        'currentStatus' => 'Current Operational Status',
        'registrationStatus' => 'Registration Status'
    ];
    
    $missingFields = [];
    foreach ($requiredFields as $field => $label) {
        if (empty($_POST[$field])) {
            $missingFields[] = $label;
        }
    }
    
    if (!empty($missingFields)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required fields: ' . implode(', ', $missingFields)
        ]);
        exit;
    }
    
    // Parse GPS coordinates
    function parse_gps_coordinates($gps_str) {
        if (empty($gps_str)) {
            throw new Exception("Empty GPS coordinates");
        }
        
        $cleaned = str_replace('°', '', $gps_str);
        $cleaned = trim($cleaned);
        $parts = preg_split('/[,\s]+/', $cleaned);
        $parts = array_filter($parts);
        
        if (count($parts) == 2) {
            $lat = floatval($parts[0]);
            $lon = floatval($parts[1]);
            return [$lat, $lon];
        }
        
        if (count($parts) == 4) {
            $lat = floatval($parts[0]) * (in_array(strtoupper($parts[1]), ['N', '']) ? 1 : -1);
            $lon = floatval($parts[2]) * (in_array(strtoupper($parts[3]), ['E', '']) ? 1 : -1);
            return [$lat, $lon];
        }
        
        throw new Exception("Could not parse GPS coordinates. Use format: '0.6341° N, 35.7364° E' or '-0.6341, 35.7364'");
    }
    
    // Save uploaded file
    function save_uploaded_file($file, $subfolder) {
        if (!$file || !$file['name']) {
            return [null, null];
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            throw new Exception("File type not allowed: {$file['name']}");
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception("File size exceeds 10MB limit");
        }
        
        $uploadDir = UPLOAD_FOLDER . '/' . $subfolder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
        $filepath = $uploadDir . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Failed to upload file: {$file['name']}");
        }
        
        return [$filename, $filepath];
    }
    
    // Start transaction
    $schemeModel->beginTransaction();
    
    try {
        // Get or create subcounty
        $subcounty = $subcountyModel->findAll(['subcounty_name' => $_POST['subcounty']]);
        if (empty($subcounty)) {
            $subcountyId = $subcountyModel->create(['subcounty_name' => $_POST['subcounty']]);
        } else {
            $subcountyId = $subcounty[0]['subcounty_id'];
        }
        
        // Parse scheme name and type
        $schemeNameWithType = $_POST['scheme'];
        $schemeName = $schemeNameWithType;
        $schemeType = 'Community';
        
        if (strpos($schemeNameWithType, ' (') !== false && strpos($schemeNameWithType, ')') !== false) {
            $parts = explode(' (', $schemeNameWithType);
            $schemeName = $parts[0];
            $schemeType = rtrim($parts[1], ')');
        }
        
        // Create irrigation scheme
        $schemeData = [
            'scheme_name' => $schemeName,
            'subcounty_id' => $subcountyId,
            'scheme_type' => $schemeType,
            'registration_status' => $_POST['registrationStatus'],
            'current_status' => $_POST['currentStatus'],
            'infrastructure_status' => $_POST['infrastructureStatus'] ?: null,
            'water_source' => $_POST['waterSource'] ?: null,
            'water_availability' => $_POST['waterAvailability'] ?: null,
            'intake_works_type' => $_POST['intakeWorksType'] ?: null,
            'conveyance_works_type' => $_POST['conveyanceWorksType'] ?: null,
            'application_type' => $_POST['applicationType'] ?: null,
            'main_crop' => $_POST['mainCrop'] ?: null,
            'scheme_area' => $_POST['schemeArea'] ? floatval($_POST['schemeArea']) : null,
            'irrigable_area' => $_POST['irrigableArea'] ? floatval($_POST['irrigableArea']) : null,
            'cropped_area' => $_POST['croppedArea'] ? floatval($_POST['croppedArea']) : null,
            'implementing_agency' => $_POST['implementingAgency'] ?: null
        ];
        
        $schemeId = $schemeModel->create($schemeData);
        
        // Save GPS data
        list($lat, $lon) = parse_gps_coordinates($_POST['gpsCoordinates']);
        $gpsData = [
            'scheme_id' => $schemeId,
            'latitude' => $lat,
            'longitude' => $lon
        ];
        $gpsModel->create($gpsData);
        
        // Create assessment
        $assessmentData = [
            'scheme_id' => $schemeId,
            'agent_name' => $_POST['agentName'],
            'assessment_date' => $_POST['visitDate'],
            'farmers_count' => $_POST['farmersCount'] ? intval($_POST['farmersCount']) : null,
            'future_plans' => $_POST['futurePlans'] ?: null,
            'challenges' => $_POST['challenges'] ?: null,
            'additional_notes' => $_POST['additionalNotes'] ?: null
        ];
        
        $assessmentId = $assessmentModel->create($assessmentData);
        
        // Handle document uploads
        $documentTypes = [
            'officeBearersPdf' => 'office_bearers',
            'schemeMembersPdf' => 'members_list',
            'bylawsPdf' => 'bylaws',
            'schemeMapPdf' => 'scheme_map',
            'intakeDesignsPdf' => 'intake_designs',
            'feasibilityReport' => 'feasibility_report',
            'esiaReport' => 'esia_report',
            'wraLicensing' => 'wra_licensing'
        ];
        
        foreach ($documentTypes as $field => $docType) {
            if (isset($_FILES[$field]) && $_FILES[$field]['name']) {
                list($filename, $filepath) = save_uploaded_file($_FILES[$field], 'documents');
                if ($filename) {
                    $documentData = [
                        'scheme_id' => $schemeId,
                        'assessment_id' => $assessmentId,
                        'document_type' => $docType,
                        'file_name' => $filename,
                        'file_path' => $filepath
                    ];
                    $documentModel->create($documentData);
                }
            }
        }
        
        // Handle photo uploads
        if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
            $photoCount = count($_FILES['photos']['name']);
            for ($i = 0; $i < $photoCount; $i++) {
                if ($_FILES['photos']['name'][$i]) {
                    $photoFile = [
                        'name' => $_FILES['photos']['name'][$i],
                        'type' => $_FILES['photos']['type'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                        'error' => $_FILES['photos']['error'][$i],
                        'size' => $_FILES['photos']['size'][$i]
                    ];
                    
                    list($filename, $filepath) = save_uploaded_file($photoFile, 'photos');
                    if ($filename) {
                        $photoData = [
                            'scheme_id' => $schemeId,
                            'assessment_id' => $assessmentId,
                            'filename' => $filename,
                            'file_path' => $filepath
                        ];
                        $photoModel->create($photoData);
                    }
                }
            }
        }
        
        // Commit transaction
        $schemeModel->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Assessment submitted successfully!',
            'scheme_id' => $schemeId,
            'assessment_id' => $assessmentId
        ]);
        
    } catch (Exception $e) {
        $schemeModel->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting assessment: ' . $e->getMessage()
    ]);
}
?>
