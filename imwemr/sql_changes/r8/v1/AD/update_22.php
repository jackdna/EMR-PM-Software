<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = " ALTER TABLE `ccda_download_log` ADD `ccda_export_schedule_operator_id` INT( 11 ) NOT NULL AFTER `ccda_export_schedule_id` 
";


foreach($qry as $q){imw_query($q) or $msg_info[] = imw_error();}



if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 22 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 22 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 22</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>