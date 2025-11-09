<?php require 'config.php'; require_login();
$cid=(int)($_GET['cid'] ?? 0);
if(!$cid){echo'<p>No control selected.</p>';return;}
$ctrl=$pdo->prepare('SELECT * FROM controls WHERE id=?');$ctrl->execute([$cid]);$ctrl=$ctrl->fetch();
if(!$ctrl){echo'<p>Invalid control.</p>';return;}
if(isset($_POST['upload'])){
  $f=$_FILES['file'];
  $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
  $whitelist=['pdf','png','jpg','jpeg','xlsx','csv','zip'];
  if(!in_array($ext,$whitelist))die('Bad file type');
  $new=bin2hex(random_bytes(8)).".$ext";
  move_uploaded_file($f['tmp_name'],__DIR__."/evidence/$new");
  $pdo->prepare('INSERT INTO evidence(control_id,file_name,note,uploaded_by)VALUES(?,?,?,?)')
      ->execute([$cid,$new,$_POST['note'],$_SESSION['name']]);
}
$ev=$pdo->prepare('SELECT * FROM evidence WHERE control_id=? ORDER BY uploaded_at DESC');$ev->execute([$cid]);$ev=$ev->fetchAll();
?>
<h4>Evidence – <?=htmlspecialchars($ctrl['requirement'].' '.$ctrl['title'])?></h4>
<form method="post" enctype="multipart/form-data" class="mb-3">
  <input type="file" name="file" required>
  <input type="text" name="note" placeholder="Note (optional)" class="form-control d-inline-block" style="width:200px">
  <button class="btn btn-sm btn-primary" name="upload">Upload</button>
</form>
<table class="table table-sm"><tr><th>File</th><th>Note</th><th>When</th><th>By</th></tr>
<?php foreach($ev as $e): ?>
<tr>
 <td><a href="download.php?f=<?=$e['file_name']?>">📄 <?=$e['file_name']?></a></td>
 <td><?=htmlspecialchars($e['note'])?></td>
 <td><?=$e['uploaded_at']?></td>
 <td><?=$e['uploaded_by']?></td>
</tr>
<?php endforeach;?></table>