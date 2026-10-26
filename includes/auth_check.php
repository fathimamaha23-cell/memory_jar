<?php
// ========================================================
// Digital Memory Jar - Authentication Guard
// ========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: index.php?msg=auth_required");
    exit;
}

$currentUser = [
    'id'    => (int)$_SESSION['user_id'],
    'name'  => $_SESSION['user_name'] ?? 'Friend',
    'email' => $_SESSION['user_email'] ?? ''
];
