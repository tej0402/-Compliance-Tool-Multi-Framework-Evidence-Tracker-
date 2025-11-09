<?php
require 'config.php';
require_login();

$rows = $pdo->query("SELECT * FROM controls ORDER BY requirement+0 ASC, requirement ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PCI DSS Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
  <style>
    body { padding: 20px; }
    @media print {
      th:last-child, td:last-child, .dt-buttons {
        display: none !important;
      }
    }
  </style>
</head>
<body>

<h4>PCI DSS Compliance Report</h4>

<div class="table-responsive">
<table id="reportTable" class="table table-bordered table-striped table-sm">
  <thead class="table-dark">
    <tr>
      <th>#</th>
      <th>Requirement</th>
      <th>Title</th>
      <th>Status</th>
      <th>Owner</th>
      <th>Company Comments</th>
      <th>Client Comments</th>
      <th>Actions</th> <!-- Excluded from export -->
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['requirement']) ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td>
          <span class="badge 
            <?= $r['status']=='Gap' ? 'bg-danger' : ($r['status']=='Partial' ? 'bg-warning text-dark' : ($r['status']=='Compliant' ? 'bg-success' : 'bg-secondary')) ?>">
            <?= htmlspecialchars($r['status']) ?>
          </span>
        </td>
        <td><?= htmlspecialchars($r['owner']) ?></td>
        <td><?= nl2br(htmlspecialchars($r['company_comments'])) ?></td>
        <td><?= nl2br(htmlspecialchars($r['client_comments'])) ?></td>
        <td class="no-export">-</td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
  $('#reportTable').DataTable({
    paging: true,
    ordering: true,
    info: true,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        exportOptions: { columns: ':not(:last-child)' },
        title: 'PCI DSS Controls Report'
      },
      {
        extend: 'csvHtml5',
        exportOptions: { columns: ':not(:last-child)' },
        title: 'PCI DSS Controls Report'
      },
      {
        extend: 'pdfHtml5',
        exportOptions: { columns: ':not(:last-child)' },
        title: 'PCI DSS Controls Report'
      },
      {
        extend: 'print',
        exportOptions: { columns: ':not(:last-child)' },
        title: 'PCI DSS Controls Report'
      }
    ]
  });
});
</script>

</body>
</html>
