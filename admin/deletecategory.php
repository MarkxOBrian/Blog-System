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

// Check if category ID is provided
if (!isset($_POST['category_id'])) {
    $_SESSION['error_message'] = "No category ID provided";
    header('Location: managecategories.php');
    exit();
}

$category_id = $_POST['category_id'];

try {
    // Check if this is the last category
    $stmt = $pdo->query("SELECT COUNT(*) FROM modulecategory");
    $category_count = $stmt->fetchColumn();

    if ($category_count <= 1) {
        $_SESSION['error_message'] = "Cannot delete the last category in the system";
        header('Location: managecategories.php');
        exit();
    }

    // Check if any posts are using this category
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM post WHERE modulecategoryid = ?");
    $stmt->execute([$category_id]);
    $post_count = $stmt->fetchColumn();

    if ($post_count > 0) {
        $_SESSION['error_message'] = "Cannot delete category as it is being used by posts";
        header('Location: managecategories.php');
        exit();
    }

    // Delete the category
    $stmt = $pdo->prepare("DELETE FROM modulecategory WHERE modulecategoryid = ?");
    $stmt->execute([$category_id]);

    $_SESSION['success_message'] = "Category deleted successfully";
} catch (PDOException $e) {
    error_log("Database error in delete category: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while deleting the category";
}

header('Location: managecategories.php');
exit(); 