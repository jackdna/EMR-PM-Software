<?php 
/*update to set serial for scheduler buttons as it was in R7*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="ALTER TABLE  `schedule_appointments` ADD  `pt_info_updt_alert` VARCHAR( 50 ) NOT NULL";
foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 12 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 12 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 12</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>