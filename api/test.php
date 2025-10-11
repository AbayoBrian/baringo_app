<?php
/**
 * Test endpoint for Vercel deployment
 */

header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'message' => 'IMS Baringo CIDU API is working!',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['HTTP_HOST'] ?? 'unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
];

echo json_encode($response, JSON_PRETTY_PRINT);
