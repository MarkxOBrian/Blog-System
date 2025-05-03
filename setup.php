<?php
require_once 'includes/config.php';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO('mysql:host=' . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $pdo->exec("USE " . DB_NAME);

    // Import the SQL file
    $sql = file_get_contents('blogs.sql');
    if ($sql === false) {
        throw new Exception("Could not read SQL file");
    }
    
    $pdo->exec($sql);

    // Create uploads directory if it doesn't exist
    $upload_dir = __DIR__ . '/uploads/posts';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    echo "Database setup completed successfully!<br>";
    echo "Default credentials:<br>";
    echo "Admin: username: admin, password: password<br>";
    echo "Student: email: student@example.com, password: password<br>";
    echo "Default category: Tech<br>";
    echo "<a href='index.php'>Go to Home Page</a>";
} catch (PDOException $e) {
    die("Error setting up database: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 
