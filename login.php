<?php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "❌ User not found";
    } elseif (!password_verify($password, $user['password_hash'])) {
        $error = "❌ Invalid password";
    } else {
		$roleFormatted = ucfirst(strtolower($user['role']));
        $_SESSION['uid']  = $user['id'];
        $_SESSION['name'] = $user['username'];
        $_SESSION['role'] = $roleFormatted;


        $dest = match ($user['role']) {
            'admin'   => 'admin_dashboard.php',
            'auditor' => 'auditor_dashboard.php',
            'viewer'  => 'viewer_home.php',
            default   => 'index.php',
        };
        header("Location: $dest");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login – Compliance Tool</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
  <style>
    :root{
      --clr-primary:#00c6ff;
      --clr-primary-dark:#0072ff;
      --clr-bg-light:rgba(255,255,255,0.15);
      --clr-bg-dark:rgba(30,30,30,0.6);
      --clr-input-light:rgba(255,255,255,0.8);
      --clr-input-dark:rgba(0,0,0,0.6);
      --clr-text-light:#fff;
      --clr-text-dark:#e0e0e0;
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif}
    body{
      display:flex;align-items:center;justify-content:center;height:100vh;
      background:linear-gradient(270deg,#6a11cb,#2575fc,#6a11cb);
      background-size:600% 600%;animation:bgMove 10s ease infinite;
      transition:background-color .3s ease;
    }
    body.dark{
      background:#121212;
    }
    @keyframes bgMove{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}

    form{
      position:relative;width:330px;padding:32px;border-radius:18px;
      background:var(--clr-bg-light);backdrop-filter:blur(12px);
      box-shadow:0 8px 24px rgba(0,0,0,.25);transition:.3s;
    }
    body.dark form{
      background:var(--clr-bg-dark);box-shadow:0 8px 24px rgba(0,0,0,.6);
    }
    h2{color:var(--clr-text-light);text-align:center;margin-bottom:24px;font-size:1.4rem}
    body.dark h2{color:var(--clr-text-dark)}
    .error{
      color:#ffdddd;background:rgba(255,0,0,.25);
      padding:10px;border-radius:6px;text-align:center;margin-bottom:15px;font-size:.9rem
    }

    .input-group{position:relative;margin-bottom:18px}
    input{
      width:100%;padding:10px 44px 10px 12px;font-size:14px;border:none;outline:none;
      border-radius:6px;background:var(--clr-input-light)
    }
    body.dark input{background:var(--clr-input-dark);color:var(--clr-text-dark)}

    .toggle-eye{
      position:absolute;top:50%;right:12px;transform:translateY(-50%);
      cursor:pointer;font-size:18px;color:#555;user-select:none
    }
    body.dark .toggle-eye{color:#aaa}

    button{
      width:100%;padding:12px;border:none;border-radius:6px;
      background:var(--clr-primary);color:#fff;font-weight:bold;font-size:15px;
      cursor:pointer;transition:background .3s,box-shadow .3s
    }
    button:hover{
      background:var(--clr-primary-dark);box-shadow:0 0 12px var(--clr-primary)
    }

    .theme-switch{
      position:absolute;top:14px;right:14px;display:flex;align-items:center;gap:6px;
      cursor:pointer;font-size:.8rem;color:var(--clr-text-light);user-select:none
    }
    body.dark .theme-switch{color:var(--clr-text-dark)}
    .theme-switch input{appearance:none;width:40px;height:20px;border-radius:20px;
      background:#ccc;position:relative;outline:none;cursor:pointer;transition:.3s}
    .theme-switch input::before{
      content:'';position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;
      background:#fff;transition:.3s}
    .theme-switch input:checked{background:var(--clr-primary-dark)}
    .theme-switch input:checked::before{transform:translateX(20px)}
  </style>
</head>
<body>
  <label class="theme-switch">
    <input type="checkbox" id="themeToggle">
    <span id="themeText">Dark&nbsp;mode</span>
  </label>

  <form method="post">
    <h2>🔐 Compliance Tool Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="input-group">
      <input type="text"  name="user" placeholder="Username" autocomplete="username" required>
    </div>

    <div class="input-group">
      <input type="password" name="pass" id="password" placeholder="Password" autocomplete="current-password" required>
      <span class="toggle-eye" id="toggleEye">👁️</span>
    </div>
	 <div class="d-flex justify-content-between align-items-center mb-3">
      <a href="forgot_password.php" class="forgot-link text-primary">Forgot Password?</a>
    </div>

    <button type="submit">Login</button>
  </form>

  <script>
    const pwd = document.getElementById('password');
    const eye = document.getElementById('toggleEye');
    eye.addEventListener('click',()=>{
      const show = pwd.type === 'password';
      pwd.type = show ? 'text' : 'password';
      eye.textContent = show ? '🙈' : '👁️';
    });

    const toggle = document.getElementById('themeToggle');
    const themeTxt = document.getElementById('themeText');
    const setTheme = (dark)=>{
      document.body.classList.toggle('dark',dark);
      toggle.checked = dark;
      themeTxt.textContent = dark ? 'Light mode' : 'Dark mode';
      localStorage.setItem('complianceTheme',dark?'dark':'light');
    };
    setTheme(localStorage.getItem('complianceTheme')==='dark');
    toggle.addEventListener('change',()=>setTheme(toggle.checked));
  </script>
</body>
</html>
