<?php 
/*update to set serial for scheduler buttons as it was in R7*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="ALTER TABLE  `schedule_status` ADD  `status_color` VARCHAR( 50 ) NOT NULL AFTER  `alias`";
foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 11 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 11 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 11</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>