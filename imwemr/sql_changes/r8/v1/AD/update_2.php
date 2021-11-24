<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "
CREATE TABLE `groups_npi` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`group_id` INT(11) NOT NULL ,
`npi` VARCHAR( 255 ) NOT NULL ,
`ins_type` VARCHAR( 20 ) NOT NULL ,
`default_val` TINYINT NOT NULL DEFAULT '0',
`del_status` TINYINT NOT NULL DEFAULT '0'
) ENGINE = MYISAM ;
";
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