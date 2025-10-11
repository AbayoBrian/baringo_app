-- =============================================
-- IMS Baringo CIDU Database Schema
-- PostgreSQL Version for Render
-- =============================================

-- Create database (this will be done by Render)
-- CREATE DATABASE ims_baringo;

-- =============================================
-- Table: users
-- Purpose: User authentication and roles
-- =============================================
DROP TABLE IF EXISTS users CASCADE;
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);

-- =============================================
-- Table: subcounties
-- Purpose: Subcounty information
-- =============================================
DROP TABLE IF EXISTS subcounties CASCADE;
CREATE TABLE subcounties (
    subcounty_id SERIAL PRIMARY KEY,
    subcounty_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_subcounties_name ON subcounties(subcounty_name);

-- =============================================
-- Table: irrigation_schemes
-- Purpose: Main irrigation scheme data
-- =============================================
DROP TABLE IF EXISTS irrigation_schemes CASCADE;
CREATE TABLE irrigation_schemes (
    scheme_id SERIAL PRIMARY KEY,
    scheme_name VARCHAR(100) NOT NULL,
    subcounty_id INTEGER NOT NULL,
    scheme_type VARCHAR(50),
    registration_status VARCHAR(50) CHECK (registration_status IN ('Self help group', 'CBO', 'Irrigation water user association')),
    current_status VARCHAR(50) CHECK (current_status IN ('Active', 'Dormant', 'Under Construction', 'Proposed', 'Abandoned')),
    infrastructure_status VARCHAR(50) CHECK (infrastructure_status IN ('Fully functional', 'Partially functional', 'Needs repair', 'Not functional', 'Not constructed')),
    water_source VARCHAR(100),
    water_availability VARCHAR(50) CHECK (water_availability IN ('Adequate', 'Inadequate', 'Seasonal', 'No water')),
    intake_works_type VARCHAR(100),
    conveyance_works_type VARCHAR(100),
    application_type VARCHAR(50) CHECK (application_type IN ('Sprinkler', 'Canals', 'Basin', 'Drip', 'Furrow')),
    main_crop VARCHAR(100),
    scheme_area DECIMAL(10,2),
    irrigable_area DECIMAL(10,2),
    cropped_area DECIMAL(10,2),
    implementing_agency VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE irrigation_schemes 
ADD CONSTRAINT fk_irrigation_schemes_subcounty 
FOREIGN KEY (subcounty_id) REFERENCES subcounties(subcounty_id) ON DELETE CASCADE;

CREATE INDEX idx_irrigation_schemes_name ON irrigation_schemes(scheme_name);
CREATE INDEX idx_irrigation_schemes_subcounty ON irrigation_schemes(subcounty_id);
CREATE INDEX idx_irrigation_schemes_status ON irrigation_schemes(current_status);

-- =============================================
-- Table: gps_data
-- Purpose: GPS coordinates for schemes
-- =============================================
DROP TABLE IF EXISTS gps_data CASCADE;
CREATE TABLE gps_data (
    id SERIAL PRIMARY KEY,
    scheme_id INTEGER NOT NULL,
    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE gps_data 
ADD CONSTRAINT fk_gps_data_scheme 
FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE;

CREATE INDEX idx_gps_data_scheme ON gps_data(scheme_id);
CREATE INDEX idx_gps_data_coordinates ON gps_data(latitude, longitude);

-- =============================================
-- Table: assessments
-- Purpose: Field assessments and evaluations
-- =============================================
DROP TABLE IF EXISTS assessments CASCADE;
CREATE TABLE assessments (
    assessment_id SERIAL PRIMARY KEY,
    scheme_id INTEGER NOT NULL,
    agent_name VARCHAR(100) NOT NULL,
    assessment_date DATE NOT NULL,
    farmers_count INTEGER,
    future_plans TEXT,
    challenges TEXT,
    additional_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE assessments 
ADD CONSTRAINT fk_assessments_scheme 
FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE;

CREATE INDEX idx_assessments_scheme ON assessments(scheme_id);
CREATE INDEX idx_assessments_agent ON assessments(agent_name);
CREATE INDEX idx_assessments_date ON assessments(assessment_date);

-- =============================================
-- Table: documents
-- Purpose: Document file management
-- =============================================
DROP TABLE IF EXISTS documents CASCADE;
CREATE TABLE documents (
    document_id SERIAL PRIMARY KEY,
    scheme_id INTEGER NOT NULL,
    assessment_id INTEGER,
    document_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size BIGINT,
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE documents 
ADD CONSTRAINT fk_documents_scheme 
FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE;

ALTER TABLE documents 
ADD CONSTRAINT fk_documents_assessment 
FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE;

CREATE INDEX idx_documents_scheme ON documents(scheme_id);
CREATE INDEX idx_documents_assessment ON documents(assessment_id);
CREATE INDEX idx_documents_type ON documents(document_type);

-- =============================================
-- Table: photos
-- Purpose: Photo file management
-- =============================================
DROP TABLE IF EXISTS photos CASCADE;
CREATE TABLE photos (
    id SERIAL PRIMARY KEY,
    scheme_id INTEGER NOT NULL,
    assessment_id INTEGER,
    filename VARCHAR(200) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size BIGINT,
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE photos 
ADD CONSTRAINT fk_photos_scheme 
FOREIGN KEY (scheme_id) REFERENCES irrigation_schemes(scheme_id) ON DELETE CASCADE;

ALTER TABLE photos 
ADD CONSTRAINT fk_photos_assessment 
FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE;

CREATE INDEX idx_photos_scheme ON photos(scheme_id);
CREATE INDEX idx_photos_assessment ON photos(assessment_id);

-- =============================================
-- Table: attendance_record
-- Purpose: Event attendance tracking
-- =============================================
DROP TABLE IF EXISTS attendance_record CASCADE;
CREATE TABLE attendance_record (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    venue VARCHAR(100),
    date DATE,
    event VARCHAR(100),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    page_count INTEGER DEFAULT 0,
    file_size BIGINT,
    mime_type VARCHAR(100)
);

CREATE INDEX idx_attendance_venue ON attendance_record(venue);
CREATE INDEX idx_attendance_date ON attendance_record(date);
CREATE INDEX idx_attendance_event ON attendance_record(event);

-- =============================================
-- Insert Default Data
-- =============================================

-- Insert default users with hashed passwords
-- Password for both users: 'password123' (hashed with PHP password_hash())
INSERT INTO users (username, password, role) VALUES 
('Agent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent'),
('CiduAdmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON CONFLICT (username) DO UPDATE SET 
    password = EXCLUDED.password,
    role = EXCLUDED.role;

-- Insert sample subcounties
INSERT INTO subcounties (subcounty_name) VALUES 
('Baringo Central'),
('Baringo North'),
('Baringo South'),
('Eldama Ravine'),
('Mogotio'),
('Tiaty')
ON CONFLICT (subcounty_name) DO NOTHING;

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
ON CONFLICT DO NOTHING;

-- Insert sample GPS data
INSERT INTO gps_data (scheme_id, latitude, longitude) VALUES 
(1, 0.6341, 35.7364),
(2, 0.5234, 35.8123),
(3, 0.4567, 35.6789)
ON CONFLICT DO NOTHING;

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
ON CONFLICT DO NOTHING;

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
-- Create Functions and Triggers
-- =============================================

-- Function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Trigger for irrigation_schemes
CREATE TRIGGER update_irrigation_schemes_updated_at 
    BEFORE UPDATE ON irrigation_schemes 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Trigger for assessments
CREATE TRIGGER update_assessments_updated_at 
    BEFORE UPDATE ON assessments 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

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
