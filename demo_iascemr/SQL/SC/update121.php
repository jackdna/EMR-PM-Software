<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `postopphysicianorders` ADD `notedByNurse` INT( 2 ) NOT NULL AFTER `postOpEvalDone` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `postopphysicianorders` ADD `signNurse1Id` INT( 11 ) NOT NULL AFTER `signNurseDateTime` ,
ADD `signNurse1FirstName` VARCHAR( 255 ) NOT NULL AFTER `signNurse1Id` ,
ADD `signNurse1MiddleName` VARCHAR( 255 ) NOT NULL AFTER `signNurse1FirstName` ,
ADD `signNurse1LastName` VARCHAR( 255 ) NOT NULL AFTER `signNurse1MiddleName` ,
ADD `signNurse1Status` VARCHAR( 5 ) NOT NULL AFTER `signNurse1LastName` ,
ADD `signNurse1DateTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `signNurse1Status` "; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 121 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 121 Success.</b><br>".$message;
	$color = "green";			
}

?>
<html>
<head>
<title>Update 121</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>