<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = "ALTER TABLE  `schedule_appointments` ADD  `ref_phy_changed` TINYINT( 1 ) NOT NULL ,
ADD  `ref_phy_comments` TEXT NOT NULL";
$sql_sts=imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 8 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 8 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 8</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>