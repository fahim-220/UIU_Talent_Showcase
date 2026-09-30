<?php
ini_set('display_errors', '0');
ob_start();
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

function send_json_comment($data, $code = 200) {
    http_response_code($code);
    ob_end_clean();
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_comment(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!is_logged_in()) {
    send_json_comment(['success' => false, 'message' => 'Please log in to comment.'], 401);
}

$user = current_user();
if ($user && $user['status'] === 'blocked') {
    send_json_comment(['success' => false, 'message' => 'Your account is blocked.'], 403);
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$csrf = $data['csrf_token'] ?? $_POST['csrf_token'] ?? '';

if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    send_json_comment(['success' => false, 'message' => 'CSRF validation failed.'], 403);
}

$post_id = isset($data['post_id']) ? (int)$data['post_id'] : (isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0);
$body = trim($data['body'] ?? $_POST['body'] ?? '');

if (!$post_id) {
    send_json_comment(['success' => false, 'message' => 'Missing post ID.'], 400);
}

if ($body === '') {
    send_json_comment(['success' => false, 'message' => 'Comment cannot be empty.'], 400);
}

if (mb_strlen($body) > 500) {
    send_json_comment(['success' => false, 'message' => 'Comment is too long (max 500 chars).'], 400);
}

// Check post exists
$stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
if (!$stmt->fetch()) {
    send_json_comment(['success' => false, 'message' => 'Post not found.'], 404);
}

$user_id = $_SESSION['user_id'];

$ins = $pdo->prepare("INSERT INTO comments (post_id, user_id, body) VALUES (?, ?, ?)");
$ins->execute([$post_id, $user_id, $body]);
$comment_id = $pdo->lastInsertId();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
$stmt->execute([$post_id]);
$count = (int)$stmt->fetchColumn();

// Format response data
$author_name = $user['name'] ?? 'Unknown';
$initials = strtoupper(substr($author_name, 0, 1));
$avatar = $user['avatar'] ?? null;

send_json_comment([
    'success' => true,
    'comment' => [
        'id' => $comment_id,
        'author_name' => $author_name,
        'avatar' => $avatar,
        'initials' => $initials,
        'body' => $body,
        'time_text' => 'Just now'
    ],
    'count' => $count
]);
