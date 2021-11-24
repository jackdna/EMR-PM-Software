<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "ALTER TABLE `patient_messages` ADD `pt_resp_party_id` INT NOT NULL COMMENT 'iPortal Authorized Patient Representative'";
imw_query($qry) or $msg_info[] = imw_error();


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 1 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 1 completed successfully.</b>";
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