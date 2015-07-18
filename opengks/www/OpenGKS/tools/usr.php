<?php
include('dbc.php');
$refid  = $_GET['refid'];
$query	= $_GET['query'];
$result = mysql_query("SELECT $query FROM `smsusers` WHERE stmobile='$refid' AND onhold='0';");
$row = mysql_fetch_array($result);
$num_results = mysql_num_rows($result);
if ($num_results > 0){
        echo mysql_result($result, 0);
}else{
        echo 'Invalid command or keyword not found. Are you sure you have entered a correct or valid keyword? Ask your administrator for assistance.';
}
?>
