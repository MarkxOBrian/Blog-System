<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// Check if post ID is provided
if (!isset($_POST['post_id'])) {
    header('Location: myposts.php');
    exit();
}

$post_id = (int)$_POST['post_id'];

try {
    // Verify that the post belongs to the student
    $stmt = $pdo->prepare("SELECT postid FROM post WHERE postid = ? AND studentid = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    
    if ($stmt->fetch()) {
        // Delete the post
        $stmt = $pdo->prepare("DELETE FROM post WHERE postid = ? AND studentid = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        
        // Set success message
        $_SESSION['success_message'] = "Post deleted successfully.";
    } else {
        // Set error message
        $_SESSION['error_message'] = "Post not found or you don't have permission to delete it.";
    }
} catch (PDOException $e) {
    // Set error message
    $_SESSION['error_message'] = "Error deleting post: " . $e->getMessage();
}

// Redirect back to myposts.php
header('Location: myposts.php');
exit(); 