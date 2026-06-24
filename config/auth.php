<?php
declare(strict_types=1);

/**
 * Redirect to index (login page) if the user is not logged in.
 */
function require_login(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Ensure the logged-in user has the specified role.
 * Calls require_login() first, then checks the role.
 */
function require_role(string $role): void
{
    require_login();

    if (($_SESSION['user']['role'] ?? '') !== $role) {
        $userRole = $_SESSION['user']['role'] ?? '';
        if ($userRole === 'admin') {
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
        } else {
            header('Location: ' . BASE_URL . '/resident/home.php');
        }
        exit;
    }
}
