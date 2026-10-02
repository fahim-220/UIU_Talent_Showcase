<?php
ini_set('display_errors', '0');
ob_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

function send_json_points($data, $code = 200) {
    http_response_code($code);
    ob_end_clean();
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_points(['success' => false, 'message' => 'Invalid method.'], 405);
}

if (!is_admin()) {
    send_json_points(['success' => false, 'message' => 'Admin privileges required.'], 403);
}

$csrf = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    send_json_points(['success' => false, 'message' => 'CSRF validation failed.'], 403);
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
if ($post_id <= 0) {
    send_json_points(['success' => false, 'message' => 'Points can only be awarded from a post.'], 400);
}

$comp_id = !empty($_POST['competition_id']) ? (int)$_POST['competition_id'] : null;
$points = isset($_POST['points']) ? (int)$_POST['points'] : 0;
$note = trim($_POST['note'] ?? '');

if ($points === 0 || $points < -1000 || $points > 1000) {
    send_json_points(['success' => false, 'message' => 'Points must be between -1000 and 1000 (not 0).'], 400);
}
if (mb_strlen($note) < 1 || mb_strlen($note) > 255) {
    send_json_points(['success' => false, 'message' => 'Note is required (max 255 chars).'], 400);
}

$stmt = $pdo->prepare("SELECT user_id, type FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    send_json_points(['success' => false, 'message' => 'Post not found.'], 404);
}
$user_id = (int)$post['user_id'];
$post_type = $post['type'];

// Check competition if provided
if ($comp_id !== null) {
    $stmt = $pdo->prepare("SELECT id, category FROM competitions WHERE id = ?");
    $stmt->execute([$comp_id]);
    $comp = $stmt->fetch();
    if (!$comp) {
        send_json_points(['success' => false, 'message' => 'Competition not found.'], 404);
    }
    if ($comp['category'] !== $post_type) {
        send_json_points(['success' => false, 'message' => 'Competition category does not match post type.'], 400);
    }
}

// Insert
$stmt = $pdo->prepare("INSERT INTO points (user_id, competition_id, post_id, points, note) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $comp_id, $post_id, $points, $note]);

// Totals
$stmt = $pdo->prepare("SELECT SUM(points) FROM points WHERE post_id = ?");
$stmt->execute([$post_id]);
$post_total = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(points) FROM points WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_total = (int)$stmt->fetchColumn();

send_json_points([
    'success' => true,
    'message' => 'Points added successfully.',
    'post_total' => $post_total,
    'user_total' => $user_total
]);
