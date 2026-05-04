<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true) ?? [];
$fullName = trim($data['full_name'] ?? $data['name'] ?? '');
$email    = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';
$confirm  = $data['confirm'] ?? $data['confirm_password'] ?? $password;

if ($fullName === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
    exit;
}

if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Passwords do not match']);
    exit;
}

$st = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$st->execute([$email]);
if ($st->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Email already registered']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$st   = $pdo->prepare(
    'INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)'
);
$st->execute([$fullName, $email, $hash, 'resident']);

echo json_encode(['success' => true, 'message' => 'Account created successfully']);
