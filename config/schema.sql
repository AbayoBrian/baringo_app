-- IMS Baringo CIDU Database Schema
-- PHP Version

CREATE DATABASE IF NOT EXISTS ims_baringo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ims_baringo;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subcounties table
CREATE TABLE IF NOT EXISTS subcounties (
    subcounty_id INT AUTO_INCREMENT PRIMARY KEY,
    subcounty_name VARCHAR(100) NOT NULL UNIQUE
);

-- Irrigation Schemes table
CREATE TABLE IF NOT EXISTS irrigation_schemes (
    scheme_id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_name VARCHAR(100) NOT NULL,
    subcounty_id INT NOT NULL,
    scheme_type VARCHAR(50),
    registration_status ENUM('Self help group', 'CBO', 'Irrigation water user association'),
    current_status ENUM('Active', 'Dormant', 'Under Construction', 'Proposed', 'Abandoned'),
    infrastructure_status ENUM('Fully functional', 'Partially functional', 'Needs repair', 'Not functional', 'Not constructed'),
    water_source VARCHAR(100),
    water_availability ENUM('Adequate', 'Inadequate', 'Seasonal', 'No water'),
    intake_works_type VARCHAR(100),
    conveyance_works_type VARCHAR(100),
    application_type ENUM('Sprinkler', 'Canals', 'Basin', 'Drip', 'Furrow'),
    main_crop VARCHAR(100),
    scheme_area DECIMAL(10,2),
    irrigable_area DECIMAL(10,2),
    cropped_area DECIMAL(10,2),
    implementing_agency VARCHAR(100),
    FOREIGN KEY (subcounty_id) REFERENCES subcounties(subcounty_id)
);

-- GPS Data table
CREATE TABLE IF NOT EXISTS gps_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id)
);

-- Assessments table
CREATE TABLE IF NOT EXISTS assessments (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    agent_name VARCHAR(100) NOT NULL,
    assessment_date DATE NOT NULL,
    farmers_count INT,
    future_plans TEXT,
    challenges TEXT,
    additional_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id)
);

-- Documents table
CREATE TABLE IF NOT EXISTS documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    assessment_id INT,
    document_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id),
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id)
);

-- Photos table
CREATE TABLE IF NOT EXISTS photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    scheme_id INT NOT NULL,
    assessment_id INT,
    filename VARCHAR(200) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id),
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id)
);

-- Attendance Records table
CREATE TABLE IF NOT EXISTS attendance_record (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    venue VARCHAR(100),
    date DATE,
    event VARCHAR(100),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    page_count INT DEFAULT 0
);

-- Insert default users
INSERT INTO users (username, password, role) VALUES 
('Agent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent'),
('CiduAdmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE username=username;
