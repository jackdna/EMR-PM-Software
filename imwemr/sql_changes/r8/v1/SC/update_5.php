<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE `schedule_appointments` ADD `sa_ref_management` TINYINT(2) NOT NULL;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `previous_status` ADD `old_ref_management` TINYINT(2) NOT NULL, ADD `new_ref_management` TINYINT(2) NOT NULL;";
imw_query($qry) or $msg_info[] = imw_error();


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 5 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 5 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>