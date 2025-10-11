<?php
/**
 * Analytics Dashboard
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/IrrigationScheme.php';
require_once 'classes/Assessment.php';

// Require admin authentication
require_auth('admin');

$schemeModel = new IrrigationScheme();
$assessmentModel = new Assessment();

// Get statistics
$totalSchemes = $schemeModel->count();
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
    <title>Analytics Dashboard - Baringo Irrigation Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #1B5E20;
            --accent-color: #4CAF50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">
                <i class="fas fa-seedling me-2"></i>
                Baringo Irrigation Portal
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
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

        <!-- Statistics Overview -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?php echo $totalSchemes; ?></div>
                        <div class="text-white-50">Total Schemes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?php echo count($schemeStats); ?></div>
                        <div class="text-white-50">Scheme Types</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?php echo count($registrationStats); ?></div>
                        <div class="text-white-50">Registration Types</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div class="h2 mb-0"><?php echo count($statusStats); ?></div>
                        <div class="text-white-50">Status Types</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Scheme Types Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Schemes by Type
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="schemeTypesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Status Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Registration Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Status Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Current Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Scheme Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schemeStats as $stat): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($stat['scheme_type'] ?: 'Unknown'); ?></td>
                                            <td><?php echo $stat['total']; ?></td>
                                            <td><?php echo round(($stat['total'] / $totalSchemes) * 100, 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart data
        const schemeTypesData = <?php echo json_encode($schemeStats); ?>;
        const registrationData = <?php echo json_encode($registrationStats); ?>;
        const statusData = <?php echo json_encode($statusStats); ?>;

        // Scheme Types Pie Chart
        const schemeTypesCtx = document.getElementById('schemeTypesChart').getContext('2d');
        new Chart(schemeTypesCtx, {
            type: 'pie',
            data: {
                labels: schemeTypesData.map(item => item.scheme_type || 'Unknown'),
                datasets: [{
                    data: schemeTypesData.map(item => item.total),
                    backgroundColor: [
                        '#2E7D32', '#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FF9800', '#FF5722'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Registration Status Bar Chart
        const registrationCtx = document.getElementById('registrationChart').getContext('2d');
        new Chart(registrationCtx, {
            type: 'bar',
            data: {
                labels: registrationData.map(item => item.registration_status || 'Unknown'),
                datasets: [{
                    label: 'Count',
                    data: registrationData.map(item => item.total),
                    backgroundColor: '#2E7D32'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Current Status Doughnut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.current_status || 'Unknown'),
                datasets: [{
                    data: statusData.map(item => item.total),
                    backgroundColor: [
                        '#4CAF50', '#FF9800', '#2196F3', '#9C27B0', '#F44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
