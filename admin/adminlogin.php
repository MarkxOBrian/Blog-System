<?php
session_start();
require_once '../includes/DatabaseConnection.php';

$title = 'Admin Login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['adminid'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['user_type'] = 'admin';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

ob_start();
include 'templates/adminlogin.html.php';
$output = ob_get_clean();

include 'templates/layout.html.php';