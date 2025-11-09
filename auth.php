<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Optionally enforce roles:
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

