<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE `patient_location` ADD INDEX `pt_with` ( `pt_with` );";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `patient_location` ADD INDEX `sch_id` ( `sch_id` );";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `patient_location` ADD INDEX `cur_date` ( `cur_date` );";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 2 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 2 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>