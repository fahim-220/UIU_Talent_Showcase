<?php
ini_set('display_errors', '0');
ob_start();
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

function send_json_join($data, $code = 200) {
    http_response_code($code);
    ob_end_clean();
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_join(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!is_logged_in()) {
    send_json_join(['success' => false, 'message' => 'Please log in to register.'], 401);
}

$user = current_user();
if ($user && $user['status'] === 'blocked') {
    send_json_join(['success' => false, 'message' => 'Your account is blocked.'], 403);
}

$csrf = $_POST['csrf_token'] ?? '';

if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    send_json_join(['success' => false, 'message' => 'CSRF validation failed.'], 403);
}

$comp_id = isset($_POST['competition_id']) ? (int)$_POST['competition_id'] : 0;
$post_id = !empty($_POST['post_id']) ? (int)$_POST['post_id'] : null;
$user_id = $_SESSION['user_id'];

if (!$comp_id) {
    send_json_join(['success' => false, 'message' => 'Missing competition ID.'], 400);
}

// Check competition exists and is open
$stmt = $pdo->prepare("SELECT id, status, deadline, category FROM competitions WHERE id = ?");
$stmt->execute([$comp_id]);
$comp = $stmt->fetch();

if (!$comp) {
    send_json_join(['success' => false, 'message' => 'Competition not found.'], 404);
}

if ($comp['status'] === 'closed' || $comp['deadline'] < date('Y-m-d')) {
    send_json_join(['success' => false, 'message' => 'This competition is closed.'], 400);
}

// Check if user already joined
$stmt = $pdo->prepare("SELECT id FROM competition_entries WHERE competition_id = ? AND user_id = ?");
$stmt->execute([$comp_id, $user_id]);
if ($stmt->fetch()) {
    send_json_join(['success' => false, 'message' => 'You have already registered for this competition.'], 400);
}

// If post_id is provided, check if it belongs to user and matches category
if ($post_id) {
    $stmt = $pdo->prepare("SELECT id, type FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        send_json_join(['success' => false, 'message' => 'Selected post not found or you do not have permission.'], 403);
    }
    
    if ($post['type'] !== $comp['category']) {
        send_json_join(['success' => false, 'message' => 'The selected post type does not match the competition category.'], 400);
    }
}

// Insert entry
$stmt = $pdo->prepare("INSERT INTO competition_entries (competition_id, user_id, post_id) VALUES (?, ?, ?)");
$stmt->execute([$comp_id, $user_id, $post_id]);

// Get new count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM competition_entries WHERE competition_id = ?");
$stmt->execute([$comp_id]);
$count = (int)$stmt->fetchColumn();

send_json_join([
    'success' => true, 
    'message' => 'Registration successful!', 
    'entries' => $count
]);
