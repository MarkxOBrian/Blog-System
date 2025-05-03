<?php
require_once 'includes/DatabaseConnection.php';

$new_password = 'admin123'; // Change this to your desired password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    echo "Admin password has been reset successfully!";
} catch (PDOException $e) {
    echo "Error resetting password: " . $e->getMessage();
} 