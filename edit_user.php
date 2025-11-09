<?php
require 'config.php';
require 'auth.php';

if ($_SESSION['role'] !== 'Admin') {
    die("Access denied. Admins only.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $role, $id]);
    header("Location: user_management.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Edit User</h2>
    <form method="post">
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="form-control mb-2">
        <select name="role" class="form-control mb-2">
            <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="Auditor" <?= $user['role'] === 'Auditor' ? 'selected' : '' ?>>Auditor</option>
            <option value="Client" <?= $user['role'] === 'Client' ? 'selected' : '' ?>>Client</option>
        </select>
        <button class="btn btn-primary">Update</button>
        <a href="user_management.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>