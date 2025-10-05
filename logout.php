<?php
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    // Clear remember token from database
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "UPDATE users SET remember_token = NULL WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
}

// Clear session data
$_SESSION = array();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear remember me cookie
setcookie('remember_token', '', time() - 3600, '/');

// Destroy session
session_destroy();

// Redirect to login page
redirect('login.php');
?>