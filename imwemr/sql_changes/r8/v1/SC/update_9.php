<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "ALTER TABLE  `scheduler_custom_labels` ADD  `label_group` TINYINT( 1 ) NOT NULL";
$sql_sts=imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE  `schedule_label_tbl` ADD  `label_group` TINYINT NOT NULL";

$sql_sts=imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 9 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 9 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 9</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>