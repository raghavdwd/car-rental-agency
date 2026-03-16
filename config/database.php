<?php

declare(strict_types=1);

$host = '127.0.0.1';
$dbname = 'car_rental_agency';
$username = 'root';
$password = 'raghavdwd';


/*
    * For security reasons, it's recommended to use environment variables or a separate configuration file
    * to store sensitive information like database credentials in a real application.
    * The above credentials are hardcoded here for simplicity and demonstration purposes only.
*/

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch results as associative arrays
            PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements for better security
        ]
    );
} catch (PDOException $exception) {
    die('Database connection failed: ' . $exception->getMessage());
}
