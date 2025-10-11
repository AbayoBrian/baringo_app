<?php
/**
 * Vercel Entry Point
 * IMS Baringo CIDU - PHP Version
 */

// Set up the environment
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'on';

// Set the document root
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/..';

// Set up the script name and path info
$_SERVER['SCRIPT_NAME'] = '/api/index.php';

// Get the path from the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove query string and decode
$path = urldecode($path);

// Handle different routes
switch ($path) {
    case '/':
    case '':
        // Main entry point
        require_once __DIR__ . '/../index.php';
        break;
        
    case '/login.php':
    case '/login':
        require_once __DIR__ . '/../login.php';
        break;
        
    case '/agent.php':
    case '/agent':
        require_once __DIR__ . '/../agent.php';
        break;
        
    case '/home.php':
    case '/home':
        require_once __DIR__ . '/../home.php';
        break;
        
    case '/dashboard.php':
    case '/dashboard':
        require_once __DIR__ . '/../dashboard.php';
        break;
        
    case '/attendance.php':
    case '/attendance':
        require_once __DIR__ . '/../attendance.php';
        break;
        
    case '/logout.php':
    case '/logout':
        require_once __DIR__ . '/../logout.php';
        break;
        
    default:
        // Check if it's a file that exists
        $filePath = __DIR__ . '/../' . ltrim($path, '/');
        if (file_exists($filePath) && is_file($filePath)) {
            require_once $filePath;
        } else {
            // 404 - File not found
            http_response_code(404);
            echo '<!DOCTYPE html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>404 - Page Not Found</h1>
    <p>The requested page could not be found.</p>
    <a href="/">Go Home</a>
</body>
</html>';
        }
        break;
}
