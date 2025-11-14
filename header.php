<?php
require 'config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Compliance Tool</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f3f4f7;
      margin: 0;
      padding-top: 80px;
    }

    .navbar-custom {
      background: linear-gradient(90deg, #6a11cb, #2575fc);
      background-size: 400% 400%;
      animation: gradientBG 10s ease infinite;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 0 0 14px 14px;
      padding: .75rem 1.5rem;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .navbar-brand {
      color: #fff !important;
      font-size: 1.8rem;
      font-weight: 700;
    }

    .navbar-nav .nav-link {
      color: #fff !important;
      font-weight: 500;
      border-radius: 20px;
      padding: 6px 16px;
      margin-right: 10px;
      background: rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
      background: rgba(255, 255, 255, 0.25);
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
    }

    .navbar-nav .nav-link.active {
      background: #fff;
      color: #2575fc !important;
      font-weight: 600;
      box-shadow: 0 0 12px rgba(255, 255, 255, 0.6);
    }

    .navbar-text {
      color: #f8f9fa;
      font-weight: 500;
    }

    .btn-logout {
      border: 1px solid #fff;
      color: white;
      background: rgba(255, 255, 255, 0.1);
      padding: 6px 12px;
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    .btn-logout:hover {
      background: #fff;
      color: #2575fc;
    }

    .btn-manage {
      margin-right: 10px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-sm navbar-dark navbar-custom fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Compliance Tool</a>

    <div class="navbar-nav">
      <a class="nav-link <?= ($tab ?? '') === 'controls' ? 'active' : '' ?>" href="index.php?tab=controls">Controls</a>
      <a class="nav-link <?= ($tab ?? '') === 'upload' ? 'active' : '' ?>" href="index.php?tab=upload">Upload Excel</a>
      <a class="nav-link <?= ($tab ?? '') === 'history' ? 'active' : '' ?>" href="index.php?tab=history">History</a>
      <a class="nav-link <?= ($tab ?? '') === 'report' ? 'active' : '' ?>" href="index.php?tab=report">Report</a>
    </div>
<?php if (isset($_SESSION['uid'])): ?>
  <div class="navbar-nav">
    <a class="nav-link <?= ($tab ?? '') === 'chat' ? 'active' : '' ?>" href="chat.php">
      💬 Chat
    </a>
  </div>
<?php endif; ?>
    <div class="navbar-nav ms-auto align-items-center">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
        <a href="user_management.php" class="btn btn-sm btn-light btn-manage">
          👥 Manage Users
        </a>
      <?php endif; ?>
      <span class="navbar-text me-3">
        <i class="bi bi-person-fill"></i> <?= htmlspecialchars($_SESSION['name'] ?? '') ?> (<?= htmlspecialchars($_SESSION['role'] ?? '') ?>)
      </span>
      <a class="btn btn-sm btn-logout" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

