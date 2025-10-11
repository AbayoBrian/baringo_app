<?php
/**
 * Admin Home Dashboard
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/IrrigationScheme.php';
require_once 'classes/Assessment.php';
require_once 'classes/AttendanceRecord.php';

// Require admin authentication
require_auth('admin');

$schemeModel = new IrrigationScheme();
$assessmentModel = new Assessment();
$attendanceModel = new AttendanceRecord();

// Get dashboard statistics
$totalSchemes = $schemeModel->count();
$totalAssessments = $assessmentModel->count();
$totalAttendance = $attendanceModel->count();

// Get recent assessments
$recentAssessments = $assessmentModel->query("
    SELECT a.*, s.scheme_name, sc.subcounty_name 
    FROM assessments a 
    LEFT JOIN irrigation_schemes s ON a.scheme_id = s.scheme_id 
    LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id 
    ORDER BY a.created_at DESC 
    LIMIT 5
");

// Get scheme statistics
$schemeStats = $schemeModel->getSchemeStats();
$registrationStats = $schemeModel->getRegistrationStats();
$statusStats = $schemeModel->getSchemesByStatus();

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Baringo Irrigation Portal</title>
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
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
        }
        
        .stat-card .card-body {
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
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
        
        .nav-link {
            color: white !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--accent-color) !important;
        }
        
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-seedling me-2"></i>
                Baringo Irrigation Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-chart-bar me-1"></i>Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assessments.php">
                            <i class="fas fa-clipboard-list me-1"></i>Assessments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php">
                            <i class="fas fa-calendar-check me-1"></i>Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="file.php">
                            <i class="fas fa-folder me-1"></i>Files
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            <i class="fas fa-user me-1"></i>
                            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number"><?php echo $totalSchemes; ?></div>
                        <div class="stat-label">
                            <i class="fas fa-seedling me-2"></i>Total Schemes
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number"><?php echo $totalAssessments; ?></div>
                        <div class="stat-label">
                            <i class="fas fa-clipboard-check me-2"></i>Assessments
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="stat-number"><?php echo $totalAttendance; ?></div>
                        <div class="stat-label">
                            <i class="fas fa-calendar-check me-2"></i>Attendance Records
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Assessments -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Recent Assessments
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentAssessments)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                <p>No assessments found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Scheme</th>
                                            <th>Subcounty</th>
                                            <th>Agent</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentAssessments as $assessment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($assessment['scheme_name']); ?></td>
                                                <td><?php echo htmlspecialchars($assessment['subcounty_name']); ?></td>
                                                <td><?php echo htmlspecialchars($assessment['agent_name']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($assessment['assessment_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-success">Completed</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Scheme Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3">By Type</h6>
                        <?php foreach ($schemeStats as $stat): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?php echo htmlspecialchars($stat['scheme_type'] ?: 'Unknown'); ?></span>
                                <span class="badge bg-primary"><?php echo $stat['total']; ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        
                        <h6 class="text-muted mb-3">By Status</h6>
                        <?php foreach ($statusStats as $stat): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?php echo htmlspecialchars($stat['current_status'] ?: 'Unknown'); ?></span>
                                <span class="badge bg-success"><?php echo $stat['total']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="dashboard.php" class="btn btn-primary w-100">
                                    <i class="fas fa-chart-bar me-2"></i>View Analytics
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="assessments.php" class="btn btn-primary w-100">
                                    <i class="fas fa-clipboard-list me-2"></i>Manage Assessments
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="attendance.php" class="btn btn-primary w-100">
                                    <i class="fas fa-calendar-check me-2"></i>Attendance Records
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="file.php" class="btn btn-primary w-100">
                                    <i class="fas fa-folder me-2"></i>File Management
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
