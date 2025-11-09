<?php require 'config.php'; require_login();
$f=basename($_GET['f']??'');
$path=__DIR__."/evidence/$f";
if(!file_exists($path))die('Not found');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$f.'"');
readfile($path);