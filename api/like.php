<?php
ini_set('display_errors', '0');
ob_start();
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

function send_json_like($data, $code = 200) {
    http_response_code($code);
    ob_end_clean();
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_like(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!is_logged_in()) {
    send_json_like(['success' => false, 'message' => 'Please log in to like.'], 401);
}

$user = current_user();
if ($user && $user['status'] === 'blocked') {
    send_json_like(['success' => false, 'message' => 'Your account is blocked.'], 403);
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$csrf = $data['csrf_token'] ?? $_POST['csrf_token'] ?? '';

if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    send_json_like(['success' => false, 'message' => 'CSRF validation failed.'], 403);
}

$post_id = isset($data['post_id']) ? (int)$data['post_id'] : (isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0);

if (!$post_id) {
    send_json_like(['success' => false, 'message' => 'Missing post ID.'], 400);
}

// Check post exists
$stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
if (!$stmt->fetch()) {
    send_json_like(['success' => false, 'message' => 'Post not found.'], 404);
}

$user_id = $_SESSION['user_id'];

// Check if already liked
$stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$existing = $stmt->fetch();

$liked = false;

if ($existing) {
    // Unlike
    $del = $pdo->prepare("DELETE FROM likes WHERE id = ?");
    $del->execute([$existing['id']]);
} else {
    // Like
    $ins = $pdo->prepare("INSERT IGNORE INTO likes (post_id, user_id) VALUES (?, ?)");
    $ins->execute([$post_id, $user_id]);
    $liked = true;
}

// Get new count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$stmt->execute([$post_id]);
$count = (int)$stmt->fetchColumn();

send_json_like(['success' => true, 'liked' => $liked, 'count' => $count]);
