<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: /blog-project/login.php');
    exit();
}

// Check if student ID is provided
if (!isset($_POST['user_id'])) {
    $_SESSION['error_message'] = "No student ID provided";
    header('Location: manageusers.php');
    exit();
}

$student_id = $_POST['user_id'];

try {
    // Check if this is the last student
    $stmt = $pdo->query("SELECT COUNT(*) FROM student");
    $student_count = $stmt->fetchColumn();

    if ($student_count <= 1) {
        $_SESSION['error_message'] = "Cannot delete the last student in the system";
        header('Location: manageusers.php');
        exit();
    }

    // Delete the student
    $stmt = $pdo->prepare("DELETE FROM student WHERE studentid = ?");
    $stmt->execute([$student_id]);

    $_SESSION['success_message'] = "Student deleted successfully";
} catch (PDOException $e) {
    error_log("Database error in delete user: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while deleting the student";
}

header('Location: manageusers.php');
exit(); 