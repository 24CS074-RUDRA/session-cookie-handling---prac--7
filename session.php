<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login from remember token
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once 'config/database.php';
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT id, username FROM users WHERE remember_token = :token";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':token', $_COOKIE['remember_token']);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        }
    } catch (PDOException $e) {
        // Silent fail - user will need to login manually
    }
}

// Basic session security
if (empty($_SESSION['created'])) {
    $_SESSION['created'] = time();
}

// Regenerate session ID periodically for security
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
?>