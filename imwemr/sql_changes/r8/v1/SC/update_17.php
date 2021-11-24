<?php 
/*update to clean label table*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="ALTER TABLE  `schedule_templates` ADD  `MinAppointments` INT NOT NULL AFTER  `fldLunchEdTm`";

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 16 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 16 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 17</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>