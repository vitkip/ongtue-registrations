<?php
session_start();
require_once '../config/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Log activity
logActivity('User logout', "Username: " . ($_SESSION['username'] ?? 'unknown'));

// Destroy session
session_destroy();

// Redirect to login page
redirect('login.php');
?>