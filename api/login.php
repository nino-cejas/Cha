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
$email    = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

$st = $pdo->prepare(
    'SELECT id, role, full_name, email, password_hash FROM users WHERE email = ?'
);
$st->execute([$email]);
$u = $st->fetch();

if (!$u || !password_verify($password, $u['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    exit;
}

$_SESSION['user'] = [
    'id'        => (int) $u['id'],
    'role'      => $u['role'],
    'full_name' => $u['full_name'],
    'email'     => $u['email'],
];

// Backward-compat keys for existing api/auth.php / app.js
$_SESSION['user_id']    = (int) $u['id'];
$_SESSION['user_name']  = $u['full_name'];
$_SESSION['user_email'] = $u['email'];
$_SESSION['user_role']  = $u['role'];
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

echo json_encode([
    'success' => true,
    'user'    => [
        'id'    => (int) $u['id'],
        'name'  => $u['full_name'],
        'email' => $u['email'],
        'role'  => $u['role'],
    ],
]);
