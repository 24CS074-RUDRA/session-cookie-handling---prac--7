<?php
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Simple CSRF token functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
}

// Check if user is logged in, if not redirect to login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

// Check if user is logged in, if yes redirect to dashboard
function requireGuest() {
    if (isLoggedIn()) {
        redirect('dashboard.php');
    }
}
?>