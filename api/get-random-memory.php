<?php
// ========================================================
// Digital Memory Jar - Get Random Memory API Endpoint
// ========================================================

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in first.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$exclude_id = (int)($_GET['exclude'] ?? 0);

// Count total memories for the user
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM memories WHERE user_id = ?");
$count_stmt->execute([$user_id]);
$total_count = (int)$count_stmt->fetchColumn();

if ($total_count === 0) {
    echo json_encode([
        'success'     => true,
        'total_count' => 0,
        'memory'      => null,
        'message'     => 'Your jar is waiting for its first little memory. Add one to get started! 💌'
    ]);
    exit;
}

// Select a random memory (optionally excluding the current one if more than 1 exists)
$sql = "SELECT id, title, description, memory_date, category, mood FROM memories WHERE user_id = ?";
$params = [$user_id];

if ($total_count > 1 && $exclude_id > 0) {
    $sql .= " AND id != ?";
    $params[] = $exclude_id;
}

$sql .= " ORDER BY RAND() LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$memory = $stmt->fetch();

if ($memory) {
    $category_meta = get_category_meta($memory['category']);

    echo json_encode([
        'success'     => true,
        'total_count' => $total_count,
        'memory'      => [
            'id'             => (int)$memory['id'],
            'title'          => $memory['title'],
            'description'    => $memory['description'],
            'memory_date'    => $memory['memory_date'],
            'formatted_date' => format_memory_date($memory['memory_date']),
            'category'       => $memory['category'],
            'category_icon'  => $category_meta['icon'],
            'category_color' => $category_meta['color'],
            'mood'           => $memory['mood'] ?: '😊 Happy'
        ]
    ]);
} else {
    echo json_encode([
        'success'     => true,
        'total_count' => 0,
        'memory'      => null,
        'message'     => 'Your jar is waiting for its first little memory. Add one to get started! 💌'
    ]);
}
