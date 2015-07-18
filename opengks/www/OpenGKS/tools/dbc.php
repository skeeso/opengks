<?php
$link = mysql_connect('127.0.0.1', 'opengks', 'gksadmin');
if (!$link) {
    die('Not connected : ' . mysql_error());
}
if (! mysql_select_db('opengks') ) {
    die ('Can\'t use foo : ' . mysql_error());
}
?>

