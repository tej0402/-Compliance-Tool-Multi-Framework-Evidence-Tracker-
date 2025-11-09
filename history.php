<?php
require 'config.php';
require_login();

$filter = strtolower($_GET['q'] ?? '');
$controls = $pdo->query("
  SELECT id, requirement, title, comment_log FROM controls 
  WHERE comment_log IS NOT NULL AND comment_log != ''
  ORDER BY requirement+0 ASC, requirement ASC
")->fetchAll(PDO::FETCH_ASSOC);

$logs_by_req = [];

foreach ($controls as $ctrl) {
    $req = $ctrl['requirement'];
    $title = $ctrl['title'];
    $log_text = trim($ctrl['comment_log']);
    $entries = preg_split('/=+\s*\d{4}-\d{2}-\d{2} \d{2}:\d{2} by .+?=+/m', $log_text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    preg_match_all('/=+\s*(\d{4}-\d{2}-\d{2} \d{2}:\d{2}) by (.+?)\s*=+/m', $log_text, $headers);

    for ($i = 0; $i < count($headers[0]); $i++) {
        $date = trim($headers[1][$i] ?? '');
        $user = trim($headers[2][$i] ?? '');
        $raw = trim($entries[$i]);

        $lines = explode("\n", $raw);
        $kv = ['status'=>'', 'owner'=>'', 'company'=>'', 'client'=>''];

        foreach ($lines as $line) {
            if (stripos($line, 'status:') === 0)   $kv['status']  = trim(substr($line, 7));
            if (stripos($line, 'owner:') === 0)    $kv['owner']   = trim(substr($line, 6));
            if (stripos($line, 'company:') === 0)  $kv['company'] = trim(substr($line, 8));
            if (stripos($line, 'client:') === 0)   $kv['client']  = trim(substr($line, 7));
        }

        $row_text = strtolower("$req $title $user {$kv['status']} {$kv['owner']} {$kv['company']} {$kv['client']}");
        if ($filter && strpos($row_text, $filter) === false) continue;

        $logs_by_req[$req]['title'] = $title;
        $logs_by_req[$req]['entries'][] = [
            'date'    => $date,
            'user'    => $user,
            'status'  => $kv['status'],
            'owner'   => $kv['owner'],
            'company' => $kv['company'],
            'client'  => $kv['client']
        ];
    }

    if (isset($logs_by_req[$req]['entries'])) {
        usort($logs_by_req[$req]['entries'], fn($a, $b) =>
            strtotime($b['date']) <=> strtotime($a['date'])
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Change History - PCI DSS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { padding: 20px; }
    h5 { margin-top: 1.5rem; }
    td { vertical-align: top; }
    .recent-row { background-color: #fff3cd !important; }
    th { cursor: pointer; }
  </style>
</head>
<body>

<h4>Change History: Company & Client Comments</h4>

<?php if (empty($logs_by_req)): ?>
  <div class="alert alert-warning">No comment history found.</div>
<?php else: ?>
  <?php foreach ($logs_by_req as $req => $data): ?>
    <h5><?= htmlspecialchars($req) ?> — <?= htmlspecialchars($data['title']) ?></h5>
    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Owner</th>
            <th>Company Comment</th>
            <th>Client Comment</th>
            <th>Updated By</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data['entries'] as $log): ?>
            <?php 
              $isRecent = (strtotime('now') - strtotime($log['date'])) <= 86400;
              $rowClass = $isRecent ? 'recent-row' : '';
            ?>
            <tr class="<?= $rowClass ?>">
              <td><?= htmlspecialchars($log['date']) ?></td>
              <td><?= htmlspecialchars($log['status']) ?></td>
              <td><?= htmlspecialchars($log['owner']) ?></td>
              <td><?= htmlspecialchars($log['company']) ?></td>
              <td><?= htmlspecialchars($log['client']) ?></td>
              <td><?= htmlspecialchars($log['user']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('th').forEach(th => {
    th.addEventListener('click', () => {
      const table = th.closest('table');
      const index = [...th.parentNode.children].indexOf(th);
      const asc = th.asc = !th.asc;
      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));

      rows.sort((a, b) => {
        const aText = a.children[index].innerText.trim().toLowerCase();
        const bText = b.children[index].innerText.trim().toLowerCase();

        return aText.localeCompare(bText) * (asc ? 1 : -1);
      });

      rows.forEach(row => tbody.appendChild(row));
    });
  });
});
</script>

</body>
</html>
