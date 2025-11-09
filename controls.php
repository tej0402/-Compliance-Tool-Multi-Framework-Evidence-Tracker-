<?php
require 'config.php';
require_login();

/* ---------- inline save ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['upd'])){
  $history  = "=== ".date('Y-m-d H:i')." by ".$_SESSION['name']." ===\n";
  $history .= "Status:  ". $_POST['status']           ."\n";
  $history .= "Owner:   ". $_POST['owner']            ."\n";
  $history .= "Company: ". $_POST['company_comments'] ."\n";
  $history .= "Client:  ". $_POST['client_comments']  ."\n\n";
  $pdo->prepare("UPDATE controls SET status=?,owner=?,company_comments=?,client_comments=?,comment_log=CONCAT(IFNULL(comment_log,''),?) WHERE id=?")
      ->execute([ $_POST['status'],$_POST['owner'],$_POST['company_comments'],$_POST['client_comments'],$history,$_POST['id'] ]);
  header('Location: '. strtok($_SERVER['REQUEST_URI'],'?') .'?'. $_SERVER['QUERY_STRING']);
  exit;
}

$rows=$pdo->query("SELECT * FROM controls ORDER BY requirement+0, requirement, FIELD(status,'Gap','Partial','Compliant','')")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ShieldAudit – PCI Compliance Tool</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
 body { padding: 20px }
 textarea { resize: vertical }
 thead.table-light {
   background: linear-gradient(to right, #1e3c72, #2a5298);
   color: white;
 }
 table.dataTable thead th,
 table.dataTable thead td {
   border-bottom: 1px solid #dee2e6;
 }
 table.dataTable td {
   border-bottom: 1px solid #dee2e6;
 }
 th.sorting::after, th.sorting_asc::after, th.sorting_desc::after {
   font-family: "Font Awesome 6 Free";
   font-weight: 900;
   padding-left: 6px;
 }
 th.sorting::after { content: "\f0dc" }
 th.sorting_asc::after { content: "\f0de" }
 th.sorting_desc::after { content: "\f0dd" }
 @media print {
   td:last-child, th:last-child { display: none }
 }
</style>
</head>
<body>
<h4>Controls</h4>
<div class="table-responsive">
<table id="controlsTable" class="table table-sm table-bordered align-middle">
 <thead class="table-light">
  <tr>
   <th>Requirement</th>
   <th style="width:18%">Title</th>
   <th>Status</th>
   <th>Owner</th>
   <th style="width:15%">Company Comments</th>
   <th style="width:15%">Client Comments</th>
   <th style="width:11%">Actions</th>
  </tr>
 </thead>
 <tbody>
<?php foreach($rows as $r):?>
  <tr>
   <td><?=htmlspecialchars($r['requirement'])?></td>
   <td><?=htmlspecialchars($r['title'])?></td>
   <td><span class="badge <?=
          $r['status']=='Gap'?'bg-danger':($r['status']=='Partial'?'bg-warning text-dark':($r['status']=='Compliant'?'bg-success':'bg-secondary'))
        ?>"><?=htmlspecialchars($r['status'])?></span></td>
   <td><?=htmlspecialchars($r['owner'])?></td>
   <td><?=nl2br(htmlspecialchars($r['company_comments']))?></td>
   <td><?=nl2br(htmlspecialchars($r['client_comments']))?></td>
   <td>
     <form method="post" class="d-flex flex-column" style="gap:6px">
      <input type="hidden" name="id" value="<?=$r['id']?>">
      <select name="status" class="form-select form-select-sm">
        <?php foreach(['Gap','Partial','Compliant'] as $s)echo "<option value='$s'".($r['status']==$s?' selected':'').">$s</option>";?>
      </select>
      <input name="owner" class="form-control form-control-sm" placeholder="Owner" value="<?=htmlspecialchars($r['owner'])?>">
      <textarea name="company_comments" rows="2" class="form-control form-control-sm" placeholder="Company comments"><?=htmlspecialchars($r['company_comments'])?></textarea>
      <textarea name="client_comments" rows="2" class="form-control form-control-sm" placeholder="Client comments"><?=htmlspecialchars($r['client_comments'])?></textarea>
      <div class="d-grid gap-1">
        <button name="upd" class="btn btn-sm btn-primary">Save</button>
        <a href="?tab=evidence&cid=<?=$r['id']?>" class="btn btn-sm btn-outline-info">Evidence</a>
      </div>
     </form>
   </td>
  </tr>
<?php endforeach;?>
 </tbody>
</table>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
  $('#controlsTable').DataTable({
    paging:false,
    ordering:true,
    info:false,
    searching: false
  });
});
</script>
</body>
</html>
