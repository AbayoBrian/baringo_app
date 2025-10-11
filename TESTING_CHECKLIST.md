# IMS Baringo CIDU - Testing Checklist

## Pre-Deployment Testing

### Local Environment Setup
- [ ] PHP 8.0+ is installed
- [ ] Composer dependencies installed (`composer install`)
- [ ] Database connection works locally
- [ ] All required PHP extensions are available
- [ ] File upload directories have proper permissions

### Code Quality Checks
- [ ] No syntax errors in PHP files
- [ ] All required files are present
- [ ] Database schema is complete
- [ ] Environment variables are properly configured

## Post-Deployment Testing

### 1. Basic Application Access
- [ ] Application loads without errors
- [ ] Homepage redirects to login page
- [ ] No 500 errors in browser console
- [ ] All static assets (CSS, JS, images) load correctly

### 2. Authentication Testing
- [ ] Login page displays correctly
- [ ] Agent login works (Username: `Agent`, Password: `agent@2025!`)
- [ ] Admin login works (Username: `CiduAdmin`, Password: `admin@2025#`)
- [ ] Invalid credentials show appropriate error
- [ ] Logout functionality works
- [ ] Session persistence works

### 3. Database Functionality
- [ ] Database connection established
- [ ] All tables exist and are accessible
- [ ] Data can be inserted and retrieved
- [ ] Database queries execute without errors
- [ ] Foreign key relationships work correctly

### 4. File Upload Testing
- [ ] File upload forms display correctly
- [ ] Documents can be uploaded successfully
- [ ] Photos can be uploaded successfully
- [ ] Uploaded files are accessible via URL
- [ ] File validation works (size, type restrictions)
- [ ] Upload directories have proper permissions

### 5. Core Features Testing

#### Agent Features
- [ ] Agent dashboard loads correctly
- [ ] Irrigation scheme registration works
- [ ] Assessment submission works
- [ ] GPS data collection works
- [ ] Photo upload for schemes works
- [ ] Document upload works

#### Admin Features
- [ ] Admin dashboard loads correctly
- [ ] User management functions work
- [ ] Scheme approval/rejection works
- [ ] Analytics/reporting features work
- [ ] Data export functionality works

### 6. Navigation and UI Testing
- [ ] All navigation links work
- [ ] Page transitions are smooth
- [ ] Forms submit correctly
- [ ] Error messages display properly
- [ ] Success messages display properly
- [ ] Responsive design works on mobile
- [ ] Responsive design works on tablet
- [ ] Responsive design works on desktop

### 7. Performance Testing
- [ ] Page load times are acceptable (< 3 seconds)
- [ ] Database queries are optimized
- [ ] Images are properly optimized
- [ ] No memory leaks or excessive resource usage
- [ ] Concurrent user access works

### 8. Security Testing
- [ ] SQL injection protection works
- [ ] XSS protection works
- [ ] File upload security measures work
- [ ] Session security is properly implemented
- [ ] Password hashing is secure
- [ ] HTTPS is enforced (automatic with Vercel)

### 9. Cross-Browser Testing
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile browsers (Chrome Mobile, Safari Mobile)

### 10. API Endpoints Testing
- [ ] `/api/attendance.php` works correctly
- [ ] `/api/submit_assessment.php` works correctly
- [ ] API responses are in correct format
- [ ] Error handling works for API endpoints
- [ ] Authentication required for API endpoints

## Automated Testing Script

Create a simple test script to verify basic functionality:

```php
<?php
// test_deployment.php
echo "Testing IMS Baringo CIDU Deployment\n";
echo "==================================\n\n";

// Test 1: Database Connection
try {
    require_once 'config/database_postgresql.php';
    $db = new Database();
    $pdo = $db->getConnection();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 2: Required Tables
$required_tables = ['users', 'subcounties', 'irrigation_schemes', 'assessments'];
foreach ($required_tables as $table) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "✓ Table '$table' exists\n";
    } catch (Exception $e) {
        echo "✗ Table '$table' missing: " . $e->getMessage() . "\n";
    }
}

// Test 3: File Upload Directories
$upload_dirs = ['assets/uploads', 'assets/uploads/documents', 'assets/uploads/photos'];
foreach ($upload_dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "✓ Directory '$dir' is writable\n";
    } else {
        echo "✗ Directory '$dir' is not writable\n";
    }
}

echo "\nTesting completed!\n";
?>
```

## Load Testing

For production deployment, consider:

- [ ] Test with multiple concurrent users
- [ ] Monitor database performance under load
- [ ] Test file upload with large files
- [ ] Monitor memory usage
- [ ] Test database connection pooling

## Monitoring Setup

- [ ] Set up error tracking (Sentry, Bugsnag)
- [ ] Configure uptime monitoring
- [ ] Set up database performance monitoring
- [ ] Configure log aggregation
- [ ] Set up alerts for critical errors

## Rollback Plan

- [ ] Document current working version
- [ ] Keep previous deployment as backup
- [ ] Test rollback procedure
- [ ] Have database backup ready
- [ ] Document rollback steps

## Post-Deployment Verification

After deployment, verify:

1. **User Acceptance Testing**
   - [ ] End users can access the application
   - [ ] All business processes work as expected
   - [ ] Data integrity is maintained
   - [ ] Performance meets requirements

2. **Documentation**
   - [ ] Update deployment documentation
   - [ ] Document any issues encountered
   - [ ] Update user guides if needed
   - [ ] Document configuration changes

3. **Training**
   - [ ] Train users on new features
   - [ ] Update admin documentation
   - [ ] Provide troubleshooting guides

## Success Criteria

The deployment is considered successful when:

- [ ] All critical functionality works
- [ ] Performance meets requirements
- [ ] Security measures are in place
- [ ] Users can complete their tasks
- [ ] System is stable and reliable
- [ ] Monitoring and alerting are active

---

**Remember**: Test thoroughly before going live, and always have a rollback plan ready!
