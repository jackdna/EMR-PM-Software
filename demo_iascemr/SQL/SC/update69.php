<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
$sql1="ALTER TABLE `lasusedpassword` 
CHANGE `user_id` `user_id` INT(11) NOT NULL DEFAULT '0', 
CHANGE `password1` `password1` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password2` `password2` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password3` `password3` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password4` `password4` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password5` `password5` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password6` `password6` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password7` `password7` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password8` `password8` VARCHAR(255) COLLATE latin1_general_ci NOT NULL, 
CHANGE `password9` `password9` VARCHAR(255) COLLATE latin1_general_ci NOT NULL,
CHANGE `password10` `password10` VARCHAR(255) COLLATE latin1_general_ci NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error())
{
	$msg_info[] = "<br><br><b>Update 69 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 69 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 69</title>
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