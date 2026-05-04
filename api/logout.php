<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';

session_unset();
session_destroy();

// If called from a browser link (not AJAX), redirect to login page
if (isset($_GET['redirect'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logged out']);
