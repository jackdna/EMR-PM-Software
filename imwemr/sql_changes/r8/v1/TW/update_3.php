<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "ALTER TABLE `as_api_call_log` ADD COLUMN `date_of_service` DATE NULL";
$qry[] = "ALTER TABLE `as_api_call_log` ADD COLUMN `cn_id` INT(10) NULL";
$qry[] = "ALTER TABLE `as_api_call_log` ADD COLUMN `error_message` text NULL";

foreach ($qry  as $sql){
	imw_query($sql) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>TouchWorks Update 3 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>TouchWorks Update 3 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>