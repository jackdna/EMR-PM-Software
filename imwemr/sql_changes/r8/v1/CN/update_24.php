<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = "UPDATE `user_type` SET `color` = '#cdffb5,#347C17' WHERE `user_type`.`user_type_id` = 3;";

imw_query($q) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 24 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 24 completed successfully. </b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 24</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>