<?php


/*
    * This file contains the database connection setup for the Car Rental Agency application.
    * It uses environment variables to securely manage database credentials and establishes a PDO connection.
    * The connection is configured to throw exceptions on errors and to use prepared statements for security.
*/

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    if ($_ENV['APP_ENV'] === 'development') {
        die($exception->getMessage());
    } else {
        die('Database connection failed.');
    }
}