<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");

$sql="ALTER TABLE `patient_in_waiting_tbl` ADD `iAscSyncroCount` INT( 11)NOT NULL ";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql1="UPDATE patient_in_waiting_tbl SET iAscSyncroCount = '1' WHERE iAscSyncroCount = '0' AND (iAscSyncroStatus='Syncronized' OR iAscReSyncroStatus='yes')  ";
$row1 = imw_query($sql1) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Updates 12 run OK";

?>

<html>
<head>
<title>Mysql Updates After Launch</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







