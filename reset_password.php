<?php
require 'config.php';

$err = $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash=?, reset_token=NULL, token_expiry=NULL WHERE id=?");
        $stmt->execute([$hash, $user['id']]);
        $msg = "✅ Password reset successful. You can now <a href='login.php'>Login</a>.";
    } else {
        $err = "❌ Invalid or expired token.";
    }
} elseif (!isset($_GET['token'])) {
    $err = "❌ No reset token found in URL.";
}
$token = $_GET['token'] ?? $_POST['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reset Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(270deg,#6a11cb,#2575fc,#6a11cb);
      background-size: 600% 600%;
      animation: bgMove 10s ease infinite;
      display: flex; justify-content: center; align-items: center; height: 100vh;
    }
    @keyframes bgMove {
      0%{background-position:0 50%}
      50%{background-position:100% 50%}
      100%{background-position:0 50%}
    }
    form {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      padding: 32px;
      border-radius: 12px;
      color: #fff;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      width: 330px;
    }
    h2 { text-align: center; margin-bottom: 20px; }
    input {
      width: 100%; padding: 10px; margin-bottom: 15px;
      border-radius: 6px; border: none; outline: none;
    }
    button {
      width: 100%; padding: 12px; background: #00c6ff; color: #fff;
      border: none; border-radius: 6px; font-weight: bold;
      cursor: pointer;
    }
    .error, .success {
      background: rgba(255,255,255,0.15); padding: 10px; border-radius: 6px;
      margin-bottom: 15px; font-size: 0.9rem;
    }
  </style>
</head>
<body>
<form method="post">
  <h2>🔒 Reset Password</h2>
  <?php if ($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
  <?php if ($msg): ?><div class="success"><?= $msg ?></div><?php endif; ?>
  <?php if (!$msg): ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Update Password</button>
  <?php endif; ?>
</form>
</body>
</html>
