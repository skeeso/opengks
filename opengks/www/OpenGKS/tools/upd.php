<?php
include('dbc.php');
$stnum  = $_POST['rstnum'];
$rfnum  = $_POST['refnum'];
$query	= $_POST['query'];
$ststat = $_POST['ststat'];

foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); }
$sql = "INSERT INTO `login` ( `rstnum` , `refnum`, `ststat` )
        VALUES ( '{$_POST['rstnum']}' , '{$_POST['refnum']}', '{$_POST['ststat']}' )
        ON DUPLICATE KEY UPDATE
        ststat='{$_POST['ststat']}'";
mysql_query($sql) or die(mysql_error());
mysql_close();
?>
