<?php
/**
 * Main Entry Point
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/User.php';

// Check if user is authenticated
if (!is_authenticated()) {
    redirect('/login.php');
}

// Redirect based on user role
$userRole = $_SESSION['user_role'];
if ($userRole === 'agent') {
    redirect('/agent.php');
} elseif ($userRole === 'admin') {
    redirect('/home.php');
} else {
    redirect('/login.php');
}
?>
