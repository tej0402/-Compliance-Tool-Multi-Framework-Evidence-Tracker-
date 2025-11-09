<?php
require 'config.php';
require 'auth.php';

if ($_SESSION['role'] !== 'Admin') {
    die("Access denied. Admins only.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    header("Location: user_management.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Add New User</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required class="form-control mb-2">
        <input type="password" name="password" placeholder="Password" required class="form-control mb-2">
        <select name="role" class="form-control mb-2">
            <option value="Admin">Admin</option>
            <option value="Auditor">Auditor</option>
            <option value="Client">Client</option>
        </select>
        <button class="btn btn-primary">Create</button>
        <a href="user_management.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>