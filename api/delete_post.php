<?php
ini_set('display_errors', '0');
ob_start();
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

function send_json_delete($data, $code = 200) {
    http_response_code($code);
    ob_end_clean();
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_delete(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!is_logged_in()) {
    send_json_delete(['success' => false, 'message' => 'Please log in to delete a post.'], 401);
}

$user = current_user();
if ($user && $user['status'] === 'blocked') {
    send_json_delete(['success' => false, 'message' => 'Your account is blocked.'], 403);
}

$csrf = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    send_json_delete(['success' => false, 'message' => 'CSRF validation failed.'], 403);
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$user_id = $_SESSION['user_id'];

if (!$post_id) {
    send_json_delete(['success' => false, 'message' => 'Missing post ID.'], 400);
}

// Ensure post exists and belongs to the user
$stmt = $pdo->prepare("SELECT id, file_path, cover_image FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

if (!$post) {
    send_json_delete(['success' => false, 'message' => 'Post not found or you do not have permission to delete it.'], 403);
}

// Delete from DB (FKs will cascade likes and comments, and set competition_entries.post_id to NULL)
$delStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$delStmt->execute([$post_id]);

// Delete physical files safely
$upload_dir = realpath(__DIR__ . '/../uploads');

if ($upload_dir) {
    // Delete main file
    if ($post['file_path'] && strpos($post['file_path'], 'uploads/') === 0) {
        $file_abs = realpath(__DIR__ . '/../' . $post['file_path']);
        if ($file_abs && strpos($file_abs, $upload_dir) === 0 && file_exists($file_abs)) {
            unlink($file_abs);
        }
    }
    
    // Delete cover image
    if ($post['cover_image'] && strpos($post['cover_image'], 'uploads/') === 0) {
        $cover_abs = realpath(__DIR__ . '/../' . $post['cover_image']);
        if ($cover_abs && strpos($cover_abs, $upload_dir) === 0 && file_exists($cover_abs)) {
            unlink($cover_abs);
        }
    }
}

// Get remaining posts count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$countStmt->execute([$user_id]);
$remaining = (int)$countStmt->fetchColumn();

send_json_delete([
    'success' => true,
    'message' => 'Post deleted successfully.',
    'posts' => $remaining
]);
