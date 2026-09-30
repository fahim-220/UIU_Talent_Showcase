<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

function send_response($success, $message, $redirect = null, $status = 200) {
    http_response_code($status);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit;
}

if (!is_logged_in()) {
    send_response(false, 'You must be logged in to upload.', null, 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Invalid request method.', null, 405);
}

// Handle empty POST (usually means post_max_size was exceeded)
if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    send_response(false, 'The uploaded file exceeds the maximum allowed size (post_max_size).', null, 400);
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    send_response(false, 'CSRF validation failed.', null, 403);
}

$type = $_POST['type'] ?? '';
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!in_array($type, ['video', 'audio', 'text'])) {
    send_response(false, 'Invalid post type.', null, 400);
}

if (empty($title)) {
    send_response(false, 'Title is required.', null, 400);
}

if (mb_strlen($title) > 150) {
    send_response(false, 'Title must not exceed 150 characters.', null, 400);
}

if ($type === 'text' && empty($description)) {
    send_response(false, 'Description is required for text posts.', null, 400);
}

function handle_upload($file_array, $allowed_exts, $allowed_mimes, $max_size, $folder) {
    if (!isset($file_array) || $file_array['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $error_msg = $upload_errors[$file_array['error']] ?? 'Unknown upload error.';
        send_response(false, $error_msg, null, 400);
    }

    if ($file_array['size'] > $max_size) {
        send_response(false, 'File is too large.', null, 400);
    }

    $ext = strtolower(pathinfo($file_array['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) {
        send_response(false, 'Invalid file extension.', null, 400);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file_array['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_mimes)) {
        send_response(false, 'Invalid MIME type.', null, 400);
    }

    $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
    $target_dir = __DIR__ . '/../uploads/' . $folder . '/';
    $target_file = $target_dir . $new_name;

    if (!move_uploaded_file($file_array['tmp_name'], $target_file)) {
        send_response(false, 'Failed to save uploaded file.', null, 500);
    }

    return 'uploads/' . $folder . '/' . $new_name;
}

$file_path = '';
$cover_image = null;
$uploaded_files = [];

try {
    if ($type === 'video') {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            send_response(false, 'File is required for video posts.', null, 400);
        }
        $file_path = handle_upload(
            $_FILES['file'], 
            ['mp4', 'webm'], 
            ['video/mp4', 'video/webm'], 
            100 * 1024 * 1024, 
            'video'
        );
        $uploaded_files[] = __DIR__ . '/../' . $file_path;
    } elseif ($type === 'audio') {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            send_response(false, 'File is required for audio posts.', null, 400);
        }
        $file_path = handle_upload(
            $_FILES['file'], 
            ['mp3', 'wav', 'ogg', 'm4a'], 
            ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/x-m4a'], 
            20 * 1024 * 1024, 
            'audio'
        );
        $uploaded_files[] = __DIR__ . '/../' . $file_path;
    } elseif ($type === 'text') {
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $cover_image = handle_upload(
                $_FILES['cover_image'], 
                ['jpg', 'jpeg', 'png', 'webp'], 
                ['image/jpeg', 'image/png', 'image/webp'], 
                5 * 1024 * 1024, 
                'images'
            );
            $uploaded_files[] = __DIR__ . '/../' . $cover_image;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, type, title, description, file_path, cover_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $type,
        $title,
        $description,
        $file_path,
        $cover_image
    ]);

    $redirects = [
        'video' => '/video.php',
        'audio' => '/audio.php',
        'text' => '/blog.php'
    ];

    send_response(true, 'Post successfully created!', BASE_URL . $redirects[$type]);

} catch (Exception $e) {
    // Delete any uploaded files on DB error
    foreach ($uploaded_files as $f) {
        if (file_exists($f)) {
            unlink($f);
        }
    }
    send_response(false, 'A database error occurred while saving the post.', null, 500);
}
