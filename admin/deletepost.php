<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Check if post ID is provided
if (!isset($_POST['post_id'])) {
    header('Location: manageposts.php');
    exit();
}

$post_id = (int)$_POST['post_id'];

try {
    // Delete the post
    $stmt = $pdo->prepare("DELETE FROM post WHERE postid = ?");
    $stmt->execute([$post_id]);
    
    // Set success message
    $_SESSION['success_message'] = "Post deleted successfully.";
} catch (PDOException $e) {
    // Set error message
    $_SESSION['error_message'] = "Error deleting post: " . $e->getMessage();
}

// Redirect back to manageposts.php
header('Location: manageposts.php');
exit(); 