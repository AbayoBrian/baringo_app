-- =============================================
-- IMS Baringo CIDU Database Schema
-- PHP Version - Ready for Import
-- =============================================

-- Create database
CREATE DATABASE IF NOT EXISTS ims_baringo 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Use the database
USE ims_baringo;

-- =============================================
-- Table: users
-- Purpose: User authentication and roles
-- =============================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: subcounties
-- Purpose: Subcounty information
-- =============================================
DROP TABLE IF EXISTS subcounties;
CREATE TABLE subcounties (
    subcounty_id INT AUTO_INCREMENT PRIMARY KEY,
    subcounty_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_subcounty_name (subcounty_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: irrigation_schemes
-- Purpose: Main irrigation scheme data
-- =============================================
DROP TABLE IF EXISTS irrigation_schemes;
CREATE TABLE irrigation_schemes (
    scheme_id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_name VARCHAR(100) NOT NULL,
    subcounty_id INT NOT NULL,
    scheme_type VARCHAR(50),
    registration_status ENUM('Self help group', 'CBO', 'Irrigation water user association') NULL,
    current_status ENUM('Active', 'Dormant', 'Under Construction', 'Proposed', 'Abandoned') NULL,
    infrastructure_status ENUM('Fully functional', 'Partially functional', 'Needs repair', 'Not functional', 'Not constructed') NULL,
    water_source VARCHAR(100),
    water_availability ENUM('Adequate', 'Inadequate', 'Seasonal', 'No water') NULL,
    intake_works_type VARCHAR(100),
    conveyance_works_type VARCHAR(100),
    application_type ENUM('Sprinkler', 'Canals', 'Basin', 'Drip', 'Furrow') NULL,
    main_crop VARCHAR(100),
    scheme_area DECIMAL(10,2) NULL,
    irrigable_area DECIMAL(10,2) NULL,
    cropped_area DECIMAL(10,2) NULL,
    implementing_agency VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subcounty_id) REFERENCES subcounties(subcounty_id) ON DELETE CASCADE,
    INDEX idx_scheme_name (scheme_name),
    INDEX idx_subcounty_id (subcounty_id),
    INDEX idx_current_status (current_status),
    INDEX idx_registration_status (registration_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: gps_data
-- Purpose: GPS coordinates for schemes
-- =============================================
DROP TABLE IF EXISTS gps_data;
CREATE TABLE gps_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE,
    INDEX idx_scheme_id (scheme_id),
    INDEX idx_coordinates (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: assessments
-- Purpose: Field assessments and evaluations
-- =============================================
DROP TABLE IF EXISTS assessments;
CREATE TABLE assessments (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    agent_name VARCHAR(100) NOT NULL,
    assessment_date DATE NOT NULL,
    farmers_count INT NULL,
    future_plans TEXT NULL,
    challenges TEXT NULL,
    additional_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE,
    INDEX idx_scheme_id (scheme_id),
    INDEX idx_agent_name (agent_name),
    INDEX idx_assessment_date (assessment_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: documents
-- Purpose: Document file management
-- =============================================
DROP TABLE IF EXISTS documents;
CREATE TABLE documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    assessment_id INT NULL,
    document_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size BIGINT NULL,
    mime_type VARCHAR(100) NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE,
    INDEX idx_scheme_id (scheme_id),
    INDEX idx_assessment_id (assessment_id),
    INDEX idx_document_type (document_type),
    INDEX idx_uploaded_at (uploaded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: photos
-- Purpose: Photo file management
-- =============================================
DROP TABLE IF EXISTS photos;
CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    assessment_id INT NULL,
    filename VARCHAR(200) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size BIGINT NULL,
    mime_type VARCHAR(100) NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE,
    INDEX idx_scheme_id (scheme_id),
    INDEX idx_assessment_id (assessment_id),
    INDEX idx_uploaded_at (uploaded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Table: attendance_record
-- Purpose: Event attendance tracking
-- =============================================
DROP TABLE IF EXISTS attendance_record;
CREATE TABLE attendance_record (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    venue VARCHAR(100) NULL,
    date DATE NULL,
    event VARCHAR(100) NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    page_count INT DEFAULT 0,
    file_size BIGINT NULL,
    mime_type VARCHAR(100) NULL,
    INDEX idx_venue (venue),
    INDEX idx_date (date),
    INDEX idx_event (event),
    INDEX idx_upload_date (upload_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Insert Default Data
-- =============================================

-- Insert default users with hashed passwords
-- Password for both users: 'password123' (hashed with PHP password_hash())
INSERT INTO users (username, password, role) VALUES 
('Agent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent'),
('CiduAdmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    role = VALUES(role);

-- Insert sample subcounties
INSERT INTO subcounties (subcounty_name) VALUES 
('Baringo Central'),
('Baringo North'),
('Baringo South'),
('Eldama Ravine'),
('Mogotio'),
('Tiaty')
ON DUPLICATE KEY UPDATE subcounty_name = VALUES(subcounty_name);

-- Insert sample irrigation schemes
INSERT INTO irrigation_schemes (
    scheme_name, 
    subcounty_id, 
    scheme_type, 
    registration_status, 
    current_status, 
    infrastructure_status,
    water_source,
    water_availability,
    application_type,
    main_crop,
    scheme_area,
    implementing_agency
) VALUES 
('Kampi Ya Moto Irrigation Scheme', 1, 'Community', 'Self help group', 'Active', 'Fully functional', 'River', 'Adequate', 'Drip', 'Maize', 25.5, 'County Government'),
('Perkerra Irrigation Scheme', 2, 'Government', 'Irrigation water user association', 'Active', 'Fully functional', 'Perkerra River', 'Adequate', 'Canals', 'Rice', 1200.0, 'National Irrigation Authority'),
('Marigat Irrigation Scheme', 3, 'Community', 'CBO', 'Under Construction', 'Partially functional', 'Molo River', 'Seasonal', 'Sprinkler', 'Vegetables', 45.2, 'County Government')
ON DUPLICATE KEY UPDATE scheme_name = VALUES(scheme_name);

-- Insert sample GPS data
INSERT INTO gps_data (scheme_id, latitude, longitude) VALUES 
(1, 0.6341, 35.7364),
(2, 0.5234, 35.8123),
(3, 0.4567, 35.6789)
ON DUPLICATE KEY UPDATE 
    latitude = VALUES(latitude),
    longitude = VALUES(longitude);

-- Insert sample assessments
INSERT INTO assessments (
    scheme_id, 
    agent_name, 
    assessment_date, 
    farmers_count, 
    future_plans, 
    challenges, 
    additional_notes
) VALUES 
(1, 'John Mwangi', '2024-01-15', 25, 'Expand to 50 acres', 'Water shortage during dry season', 'Scheme performing well'),
(2, 'Mary Wanjiku', '2024-01-20', 150, 'Modernize irrigation system', 'Aging infrastructure', 'Large scale operation'),
(3, 'Peter Kiprotich', '2024-01-25', 35, 'Complete construction', 'Funding delays', 'Good progress so far')
ON DUPLICATE KEY UPDATE 
    agent_name = VALUES(agent_name),
    assessment_date = VALUES(assessment_date);

-- =============================================
-- Create Views for Common Queries
-- =============================================

-- View: Complete scheme information
CREATE OR REPLACE VIEW v_scheme_details AS
SELECT 
    s.scheme_id,
    s.scheme_name,
    sc.subcounty_name,
    s.scheme_type,
    s.registration_status,
    s.current_status,
    s.infrastructure_status,
    s.water_source,
    s.water_availability,
    s.application_type,
    s.main_crop,
    s.scheme_area,
    s.irrigable_area,
    s.cropped_area,
    s.implementing_agency,
    g.latitude,
    g.longitude,
    s.created_at,
    s.updated_at
FROM irrigation_schemes s
LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id
LEFT JOIN gps_data g ON s.scheme_id = g.scheme_id;

-- View: Assessment summary
CREATE OR REPLACE VIEW v_assessment_summary AS
SELECT 
    a.assessment_id,
    a.scheme_id,
    s.scheme_name,
    sc.subcounty_name,
    a.agent_name,
    a.assessment_date,
    a.farmers_count,
    a.created_at
FROM assessments a
LEFT JOIN irrigation_schemes s ON a.scheme_id = s.scheme_id
LEFT JOIN subcounties sc ON s.subcounty_id = sc.subcounty_id;

-- =============================================
-- Create Stored Procedures
-- =============================================

DELIMITER //

-- Procedure: Get scheme statistics
CREATE PROCEDURE GetSchemeStatistics()
BEGIN
    SELECT 
        COUNT(*) as total_schemes,
        COUNT(CASE WHEN current_status = 'Active' THEN 1 END) as active_schemes,
        COUNT(CASE WHEN current_status = 'Dormant' THEN 1 END) as dormant_schemes,
        COUNT(CASE WHEN current_status = 'Under Construction' THEN 1 END) as under_construction,
        AVG(scheme_area) as avg_scheme_area,
        SUM(scheme_area) as total_area
    FROM irrigation_schemes;
END //

-- Procedure: Get assessment summary by date range
CREATE PROCEDURE GetAssessmentSummary(IN start_date DATE, IN end_date DATE)
BEGIN
    SELECT 
        COUNT(*) as total_assessments,
        COUNT(DISTINCT scheme_id) as schemes_assessed,
        AVG(farmers_count) as avg_farmers,
        MIN(assessment_date) as earliest_assessment,
        MAX(assessment_date) as latest_assessment
    FROM assessments 
    WHERE assessment_date BETWEEN start_date AND end_date;
END //

DELIMITER ;

-- =============================================
-- Create Triggers
-- =============================================

-- Trigger: Update scheme updated_at timestamp
DELIMITER //
CREATE TRIGGER tr_irrigation_schemes_update 
BEFORE UPDATE ON irrigation_schemes
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Trigger: Update assessment updated_at timestamp
DELIMITER //
CREATE TRIGGER tr_assessments_update 
BEFORE UPDATE ON assessments
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- =============================================
-- Grant Permissions (Optional - for production)
-- =============================================

-- Create application user (uncomment and modify for production)
-- CREATE USER 'ims_user'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON ims_baringo.* TO 'ims_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =============================================
-- Database Setup Complete
-- =============================================

SELECT 'Database setup completed successfully!' as status;
SELECT 'Default users created:' as info;
SELECT username, role FROM users;
SELECT 'Sample data inserted:' as info;
SELECT COUNT(*) as total_schemes FROM irrigation_schemes;
SELECT COUNT(*) as total_assessments FROM assessments;
SELECT COUNT(*) as total_subcounties FROM subcounties;
