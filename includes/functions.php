<?php

function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

function set_flash($key, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function redirect($path) {
    // If the path doesn't start with BASE_URL and is not absolute http, prepend BASE_URL
    if (strpos($path, 'http') !== 0 && strpos($path, BASE_URL) !== 0) {
        $path = BASE_URL . '/' . ltrim($path, '/');
    }
    header("Location: " . $path);
    exit;
}
