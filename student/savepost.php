<?php
require_once '../includes/config.php';
require_once '../includes/DatabaseConnection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// Initialize error array
$errors = [];

// Validate required fields
if (empty($_POST['title'])) {
    $errors[] = "Title is required";
}
if (empty($_POST['category_id'])) {
    $errors[] = "Category is required";
}
if (empty($_POST['content'])) {
    $errors[] = "Content is required";
}

// Handle file upload if present
$photo_path = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($_FILES['photo']['type'], $allowed_types)) {
        $errors[] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
    } elseif ($_FILES['photo']['size'] > $max_size) {
        $errors[] = "File size too large. Maximum size is 2MB.";
    } else {
        $upload_dir = '../uploads/posts/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_path = $upload_dir . uniqid() . '.' . $file_extension;
        
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $errors[] = "Failed to upload photo. Please try again.";
        }
    }
}

// If there are errors, redirect back with form data
if (!empty($errors)) {
    $_SESSION['error_message'] = implode('<br>', $errors);
    $_SESSION['form_data'] = [
        'title' => $_POST['title'],
        'category_id' => $_POST['category_id'],
        'content' => $_POST['content']
    ];
    header('Location: addpost.php');
    exit();
}

try {
    // Insert the new post
    $stmt = $pdo->prepare("
        INSERT INTO post (title, posttext, studentid, modulecategoryid, photo, status, postdate)
        VALUES (?, ?, ?, ?, ?, 'published', NOW())
    ");
    
    $stmt->execute([
        $_POST['title'],
        $_POST['content'],
        $_SESSION['user_id'],
        $_POST['category_id'],
        $photo_path
    ]);
    
    $_SESSION['success_message'] = "Post created successfully!";
    header('Location: myposts.php');
    exit();
    
} catch (PDOException $e) {
    error_log("Error creating post: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred while creating the post. Please try again.";
    $_SESSION['form_data'] = [
        'title' => $_POST['title'],
        'category_id' => $_POST['category_id'],
        'content' => $_POST['content']
    ];
    header('Location: addpost.php');
    exit();
} 