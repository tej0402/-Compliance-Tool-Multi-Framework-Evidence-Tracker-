<?php
require 'config.php';
require 'auth.php';

if ($_SESSION['role'] !== 'Admin') {
    die("Access denied. Admins only.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);
header("Location: user_management.php");
exit;
?>