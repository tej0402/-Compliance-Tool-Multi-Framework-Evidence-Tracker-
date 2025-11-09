<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xls'])) {
  if ($_FILES['xls']['error'] === 0) {
    $sheet = IOFactory::load($_FILES['xls']['tmp_name'])->getActiveSheet()->toArray(null, true, true, true);
    $stmt = $pdo->prepare("INSERT INTO controls(requirement,title,guidance) VALUES(?,?,?) ON DUPLICATE KEY UPDATE title=VALUES(title), guidance=VALUES(guidance)");
    foreach ($sheet as $row) {
      $req = trim($row['A']);
      if (!preg_match('/^[0-9.]+$/', $req)) continue;
      $stmt->execute([$req, $row['B'], $row['C']]);
    }
    $msg = "✅ Excel imported successfully!";
  } else {
    $err = "❌ File upload error.";
  }
}
?>

<h2 class="text-center mt-4 mb-4">Upload Excel Catalogue</h2>

<div class="upload-wrapper mx-auto mb-5" style="max-width:500px;background:#fff;padding:40px;border-radius:20px;box-shadow:0 8px 30px rgba(0,0,0,0.05);text-align:center;">
  <?php if ($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php elseif ($err): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" id="uploadForm">
    <input type="file" id="fileInput" name="xls" accept=".xlsx,.xls" required hidden>
    <label for="fileInput" class="drop-zone" style="border:2px dashed #7a27d1;padding:40px 20px;border-radius:16px;cursor:pointer;transition:.3s;">
      <i class="bi bi-cloud-upload" style="font-size:40px;color:#2575fc;"></i>
      <p class="mb-0">Drag & drop your Excel file here<br>or click to browse</p>
      <div id="fileName" style="font-size:0.9rem;color:#2575fc;margin-top:6px;font-weight:500;">No file selected</div>
    </label>
    <button type="submit" class="btn btn-primary w-100 mt-3">Upload</button>
  </form>
</div>

<script>
  const fileInput = document.getElementById('fileInput');
  const fileName  = document.getElementById('fileName');
  fileInput.addEventListener('change', () => {
    fileName.textContent = fileInput.files[0]?.name || "No file selected";
  });
</script>
