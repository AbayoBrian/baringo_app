<?php
/**
 * Agent Dashboard
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/IrrigationScheme.php';
require_once 'classes/Assessment.php';

// Require agent authentication
require_auth('agent');

$schemeModel = new IrrigationScheme();
$assessmentModel = new Assessment();

// Get subcounties for dropdown
$subcounties = $schemeModel->query("SELECT * FROM subcounties ORDER BY subcounty_name");

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Agent Dashboard - Baringo Irrigation Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #1B5E20;
            --accent-color: #4CAF50;
            --light-color: #E8F5E8;
            --dark-color: #1B5E20;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }
        
        .file-upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: var(--primary-color);
            background-color: rgba(46, 125, 50, 0.05);
        }
        
        .file-upload-area.dragover {
            border-color: var(--primary-color);
            background-color: rgba(46, 125, 50, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-seedling me-2"></i>
                Baringo Irrigation Portal
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Irrigation Scheme Assessment Form
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="assessmentForm" method="POST" action="api/submit_assessment.php" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Basic Information
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="agentName" class="form-label">Field Agent Name *</label>
                                    <input type="text" class="form-control" id="agentName" name="agentName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="visitDate" class="form-label">Assessment Date *</label>
                                    <input type="date" class="form-control" id="visitDate" name="visitDate" required>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subcounty" class="form-label">Subcounty *</label>
                                    <select class="form-select" id="subcounty" name="subcounty" required>
                                        <option value="">Select Subcounty</option>
                                        <?php foreach ($subcounties as $subcounty): ?>
                                            <option value="<?php echo htmlspecialchars($subcounty['subcounty_name']); ?>">
                                                <?php echo htmlspecialchars($subcounty['subcounty_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="scheme" class="form-label">Irrigation Scheme Name *</label>
                                    <input type="text" class="form-control" id="scheme" name="scheme" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="gpsCoordinates" class="form-label">GPS Coordinates *</label>
                                    <input type="text" class="form-control" id="gpsCoordinates" name="gpsCoordinates" 
                                           placeholder="e.g., 0.6341° N, 35.7364° E" required>
                                    <div class="form-text">Enter coordinates in decimal degrees or degrees format</div>
                                </div>
                            </div>

                            <!-- Scheme Details -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-cogs me-2"></i>Scheme Details
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registrationStatus" class="form-label">Registration Status *</label>
                                    <select class="form-select" id="registrationStatus" name="registrationStatus" required>
                                        <option value="">Select Status</option>
                                        <option value="Self help group">Self Help Group</option>
                                        <option value="CBO">CBO</option>
                                        <option value="Irrigation water user association">Irrigation Water User Association</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="currentStatus" class="form-label">Current Operational Status *</label>
                                    <select class="form-select" id="currentStatus" name="currentStatus" required>
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Dormant">Dormant</option>
                                        <option value="Under Construction">Under Construction</option>
                                        <option value="Proposed">Proposed</option>
                                        <option value="Abandoned">Abandoned</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="infrastructureStatus" class="form-label">Infrastructure Status</label>
                                    <select class="form-select" id="infrastructureStatus" name="infrastructureStatus">
                                        <option value="">Select Status</option>
                                        <option value="Fully functional">Fully Functional</option>
                                        <option value="Partially functional">Partially Functional</option>
                                        <option value="Needs repair">Needs Repair</option>
                                        <option value="Not functional">Not Functional</option>
                                        <option value="Not constructed">Not Constructed</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="waterSource" class="form-label">Water Source</label>
                                    <input type="text" class="form-control" id="waterSource" name="waterSource">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="waterAvailability" class="form-label">Water Availability</label>
                                    <select class="form-select" id="waterAvailability" name="waterAvailability">
                                        <option value="">Select Availability</option>
                                        <option value="Adequate">Adequate</option>
                                        <option value="Inadequate">Inadequate</option>
                                        <option value="Seasonal">Seasonal</option>
                                        <option value="No water">No Water</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="applicationType" class="form-label">Application Type</label>
                                    <select class="form-select" id="applicationType" name="applicationType">
                                        <option value="">Select Type</option>
                                        <option value="Sprinkler">Sprinkler</option>
                                        <option value="Canals">Canals</option>
                                        <option value="Basin">Basin</option>
                                        <option value="Drip">Drip</option>
                                        <option value="Furrow">Furrow</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Area Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-ruler-combined me-2"></i>Area Information (in acres)
                                    </h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="schemeArea" class="form-label">Total Scheme Area</label>
                                    <input type="number" class="form-control" id="schemeArea" name="schemeArea" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="irrigableArea" class="form-label">Irrigable Area</label>
                                    <input type="number" class="form-control" id="irrigableArea" name="irrigableArea" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="croppedArea" class="form-label">Currently Cropped Area</label>
                                    <input type="number" class="form-control" id="croppedArea" name="croppedArea" step="0.01" min="0">
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-plus-circle me-2"></i>Additional Information
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mainCrop" class="form-label">Main Crop</label>
                                    <input type="text" class="form-control" id="mainCrop" name="mainCrop">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="farmersCount" class="form-label">Number of Farmers</label>
                                    <input type="number" class="form-control" id="farmersCount" name="farmersCount" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="intakeWorksType" class="form-label">Intake Works Type</label>
                                    <input type="text" class="form-control" id="intakeWorksType" name="intakeWorksType">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="conveyanceWorksType" class="form-label">Conveyance Works Type</label>
                                    <input type="text" class="form-control" id="conveyanceWorksType" name="conveyanceWorksType">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="implementingAgency" class="form-label">Implementing Agency</label>
                                    <input type="text" class="form-control" id="implementingAgency" name="implementingAgency">
                                </div>
                            </div>

                            <!-- Assessment Details -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-clipboard-check me-2"></i>Assessment Details
                                    </h5>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="futurePlans" class="form-label">Future Plans</label>
                                    <textarea class="form-control" id="futurePlans" name="futurePlans" rows="3"></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="challenges" class="form-label">Challenges</label>
                                    <textarea class="form-control" id="challenges" name="challenges" rows="3"></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="additionalNotes" class="form-label">Additional Notes</label>
                                    <textarea class="form-control" id="additionalNotes" name="additionalNotes" rows="3"></textarea>
                                </div>
                            </div>

                            <!-- File Uploads -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-file-upload me-2"></i>Document Uploads
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="officeBearersPdf" class="form-label">Office Bearers List (PDF)</label>
                                    <input type="file" class="form-control" id="officeBearersPdf" name="officeBearersPdf" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="schemeMembersPdf" class="form-label">Scheme Members List (PDF)</label>
                                    <input type="file" class="form-control" id="schemeMembersPdf" name="schemeMembersPdf" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bylawsPdf" class="form-label">Bylaws (PDF)</label>
                                    <input type="file" class="form-control" id="bylawsPdf" name="bylawsPdf" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="schemeMapPdf" class="form-label">Scheme Map (PDF)</label>
                                    <input type="file" class="form-control" id="schemeMapPdf" name="schemeMapPdf" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="intakeDesignsPdf" class="form-label">Intake Designs (PDF)</label>
                                    <input type="file" class="form-control" id="intakeDesignsPdf" name="intakeDesignsPdf" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="feasibilityReport" class="form-label">Feasibility Report (PDF)</label>
                                    <input type="file" class="form-control" id="feasibilityReport" name="feasibilityReport" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="esiaReport" class="form-label">ESIA Report (PDF)</label>
                                    <input type="file" class="form-control" id="esiaReport" name="esiaReport" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="wraLicensing" class="form-label">WRA Licensing (PDF)</label>
                                    <input type="file" class="form-control" id="wraLicensing" name="wraLicensing" accept=".pdf">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="photos" class="form-label">Photos</label>
                                    <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                                    <div class="form-text">Select multiple photos (JPG, PNG, GIF)</div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Assessment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation and submission
        document.getElementById('assessmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const requiredFields = ['agentName', 'visitDate', 'subcounty', 'scheme', 'gpsCoordinates', 'registrationStatus', 'currentStatus'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (isValid) {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
                submitBtn.disabled = true;
                
                // Submit form
                this.submit();
            } else {
                alert('Please fill in all required fields.');
            }
        });
        
        // Set today's date as default
        document.getElementById('visitDate').valueAsDate = new Date();
    </script>
</body>
</html>
