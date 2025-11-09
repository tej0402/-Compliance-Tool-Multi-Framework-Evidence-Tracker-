<?php
require 'config.php';
require_login(); // Ensure user is logged in

$tab = $_GET['tab'] ?? 'controls';

include 'header.php';  // Navigation, etc.

switch ($tab) {
    case 'upload':
        include 'upload_excel.php';
        break;
    case 'evidence':
        include 'evidence.php';
        break;
    case 'history':
        include 'history.php';
        break;
    case 'report':
        include 'report.php';
        break;
    case 'manage_users':
        // Only Admin can access this
        if ($_SESSION['role'] === 'Admin') {
            include 'manage_users.php';
        } else {
            echo "<div class='alert alert-danger'>Access Denied: Admins Only</div>";
        }
        break;
    default:
        include 'controls.php';
}

include 'footer.php';
