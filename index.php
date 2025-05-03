<?php
// Include configuration
require_once 'includes/config.php';
require_once 'includes/DatabaseConnection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in - redirect to login page
    header('Location: login.php');
    exit();
}

try {
    // Get user type from session
    $isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';

    // Redirect based on user type
    if ($isAdmin) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: posts.php');
    }
    exit();
} catch (Exception $e) {
    // Log the error
    error_log("Error in index.php: " . $e->getMessage());
    
    // Show user-friendly error
    die("Sorry, something went wrong. Please try again later.");
}