<?php
declare(strict_types=1);

// Base URL for the project (change if your folder name differs from BMS)
define('BASE_URL', '/BMS');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host   = 'localhost';
$dbname = 'barangay_db';
$dbuser = 'root';
$dbpass = '';            // Change to your MySQL password (use env vars in production)

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed: ' . $e->getMessage());
}
