<?php
/**
 * Attendance Management
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/AttendanceRecord.php';

// Require authentication
require_auth();

$attendanceModel = new AttendanceRecord();

// Get venues and events for filters
$venues = $attendanceModel->getVenues();
$events = $attendanceModel->getEvents();

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Baringo Irrigation Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                            <i class="fas fa-calendar-check me-2"></i>
                            Attendance Management
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Upload Form -->
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="venue" class="form-label">Venue *</label>
                                    <input type="text" class="form-control" id="venue" name="venue" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label">Event Date *</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="event" class="form-label">Event Name</label>
                                    <input type="text" class="form-control" id="event" name="event">
                                </div>
                            </div>
                            
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Drop files here or click to select</h5>
                                <p class="text-muted">PDF files only, max 10MB each</p>
                                <input type="file" id="files" name="files[]" multiple accept=".pdf" style="display: none;">
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-upload me-2"></i>Upload Files
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <!-- Records Table -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="attendanceTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Filename</th>
                                        <th>Venue</th>
                                        <th>Date</th>
                                        <th>Event</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via JavaScript -->
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
        // File upload handling
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('files');
        
        fileUploadArea.addEventListener('click', () => fileInput.click());
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });
        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });
        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
        });
        
        // Form submission
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('venue', document.getElementById('venue').value);
            formData.append('date', document.getElementById('date').value);
            formData.append('event', document.getElementById('event').value);
            
            const files = document.getElementById('files').files;
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            
            fetch('api/attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Files uploaded successfully!');
                    loadAttendanceRecords();
                    this.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading files.');
            });
        });
        
        // Load attendance records
        function loadAttendanceRecords() {
            fetch('api/attendance.php')
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#attendanceTable tbody');
                tbody.innerHTML = '';
                
                data.records.forEach(record => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.filename}</td>
                        <td>${record.venue || 'N/A'}</td>
                        <td>${record.date || 'N/A'}</td>
                        <td>${record.event || 'N/A'}</td>
                        <td>${new Date(record.upload_date).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="downloadFile(${record.id})">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error loading records:', error);
            });
        }
        
        function downloadFile(recordId) {
            window.open(`api/download.php?id=${recordId}`, '_blank');
        }
        
        // Load records on page load
        loadAttendanceRecords();
    </script>
</body>
</html>
