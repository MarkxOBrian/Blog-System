<?php
try {
    $host = 'localhost';
    $dbname = 'blogs';
    $username = 'root';
    $password = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Log the error but don't expose it to the user
    error_log("Database connection failed: " . $e->getMessage());
    
    // Show a user-friendly error message
    die("Sorry, there was a problem connecting to the database. Please try again later.");
} 