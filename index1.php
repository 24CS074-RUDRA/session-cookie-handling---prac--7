<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use lowercase folder names
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// Simple check if user is logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>