<?php
// =========================
// ⚠️ NEVER commit real passwords in production!
// =========================
$DB_HOST = 'localhost';
$DB_NAME = 'pci dss';
$DB_USER = 'root'; // Use your MySQL user (e.g., root or pci_user)
$DB_PASS = '<YOUR_PASSWORD_HERE>'; // Replace with your actual MySQL password

// Set timezone globally across your app
date_default_timezone_set('Asia/Kolkata'); // ✅ India Standard Time

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Start session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Avoid re-declaring these functions
if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return isset($_SESSION['uid']);
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            header('Location: login.php');
            exit;
        }
    }
}
?>
