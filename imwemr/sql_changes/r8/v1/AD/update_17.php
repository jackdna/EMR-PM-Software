<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "ALTER TABLE  `vital_sign_limits` ADD  `vital_sign_desc` VARCHAR( 255 ) NOT NULL AFTER  `vital_sign`";
$qry[] = "UPDATE `vital_sign_limits` SET  `vital_sign_desc` =  `vital_sign` Where vital_sign_desc = '' ";

foreach($qry as $q){imw_query($q) or $msg_info[] = imw_error();}



if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 17 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 17 completed successfully.</b>";
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