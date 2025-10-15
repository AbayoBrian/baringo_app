# IMS Baringo CIDU - PHP Version

This is the PHP conversion of the Baringo Irrigation Management System (IMS) originally built with Python Flask.

## Features

- **Authentication System** - Agent/User and Admin roles
- **Irrigation Scheme Management** - Complete scheme data collection and management
- **Assessment System** - Field agent assessment forms with file uploads
- **Attendance Management** - Event attendance tracking and analytics
- **Dashboard & Analytics** - Interactive charts and data visualization
- **File Management** - Document and photo upload/management
- **Map Integration** - GPS coordinates and location mapping
- **Export Functionality** - CSV export for data analysis

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher (or MariaDB 10.3+)
- Web server (Apache/Nginx)
- PDO MySQL extension
- GD extension (for image handling)

## Installation

1. **Clone/Download the project**
   ```bash
   # Extract to your web server directory
   # e.g., /var/www/html/ims-baringo-php
   ```

2. **Set up the database**
   ```bash
   # Create database and import schema
   mysql -u root -p < config/schema.sql
   ```

3. **Configure environment**
   ```bash
   # Edit .env file with your database credentials
   nano .env
   ```

4. **Set permissions**
   ```bash
   # Make upload directories writable
   chmod 755 assets/uploads
   chmod 755 assets/uploads/documents
   chmod 755 assets/uploads/photos
   ```

5. **Configure web server**
   
   **Apache (.htaccess)**
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php [QSA,L]
   ```
   
   **Nginx**
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

## Default Login Credentials

- **Agent Account**: `Agent` / `agent@2025!`
- **Admin Account**: `CiduAdmin` / `admin@2025#`

## File Structure

```
ims-baringoCidu-php/
├── api/                    # API endpoints
├── assets/                 # Static files (CSS, JS, images, uploads)
├── classes/                # PHP model classes
├── config/                 # Configuration files
├── templates/              # HTML templates (converted from Flask)
├── .env                    # Environment configuration
├── index.php              # Main entry point
├── login.php              # Login page
├── agent.php              # Agent dashboard
├── home.php               # Admin dashboard
└── README.md              # This file
```

## API Endpoints

- `POST /api/submit_assessment.php` - Submit assessment data
- `GET /api/attendance.php` - Get attendance records
- `GET /api/schemes.php` - Get irrigation schemes
- `GET /api/assessments.php` - Get assessments data

## Database Schema

The application uses the following main tables:
- `users` - User authentication
- `subcounties` - Subcounty data
- `irrigation_schemes` - Irrigation scheme information
- `assessments` - Field assessments
- `documents` - Uploaded documents
- `photos` - Uploaded photos
- `gps_data` - GPS coordinates
- `attendance_record` - Attendance records

## Security Features

- CSRF protection
- SQL injection prevention (PDO prepared statements)
- File upload validation
- Session management
- Input sanitization

## Migration from Flask

This PHP version maintains 100% feature parity with the original Flask application:

✅ **Converted Features:**
- All database models and relationships
- Authentication and authorization
- File upload and management
- Assessment form submission
- Dashboard and analytics
- Attendance management
- Map integration
- Export functionality

✅ **Preserved:**
- All original HTML templates and styling
- JavaScript functionality
- Bootstrap UI components
- Chart.js visualizations
- Leaflet map integration

## Troubleshooting

1. **Database Connection Issues**
   - Check database credentials in `.env`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check directory permissions
   - Verify `upload_max_filesize` in php.ini
   - Check `post_max_size` in php.ini

3. **Session Issues**
   - Check session directory permissions
   - Verify session configuration

## Support

For technical support contact the developer owinb239@gmail.com/ +254-793-401-572.

## License

This project maintains the same license as the original Flask application.
