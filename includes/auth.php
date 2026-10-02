<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    global $pdo;
    if (!is_logged_in()) {
        return null;
    }
    
    // Simple memory cache
    static $user = null;
    if ($user !== null) {
        return $user;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user;
}

function is_admin() {
    $user = current_user();
    return $user && $user['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('/index.php');
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }
}
