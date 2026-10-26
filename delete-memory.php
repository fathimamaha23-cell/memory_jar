<?php
// ========================================================
// Digital Memory Jar - Delete Memory Handler
// ========================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

$user_id = $currentUser['id'];
$memory_id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if ($memory_id > 0) {
    // Delete memory strictly verifying ownership
    $stmt = $pdo->prepare("DELETE FROM memories WHERE id = ? AND user_id = ?");
    $stmt->execute([$memory_id, $user_id]);
}

// Return JSON for AJAX calls or redirect for standard browser requests
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
           || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

header("Location: memories.php?msg=deleted");
exit;
