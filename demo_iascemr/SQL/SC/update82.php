<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `preopphysicianorders` ADD  `signNurse1Id` int(11) NOT NULL DEFAULT '0',
  ADD  `signNurse1FirstName` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  ADD  `signNurse1MiddleName` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  ADD  `signNurse1LastName` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  ADD  `signNurse1Status` varchar(5) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  ADD  `signNurse1DateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `nourishmentkind`  ADD `nourishmentKindDefault` int(11) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `vital_sign_grid` CHANGE  `bp`  `systolic` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `vital_sign_grid` ADD  `diastolic` VARCHAR( 32 ) NOT NULL AFTER  `systolic`"; 
imw_query($sql1)or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 82 Failed! </b>";
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 82 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 82</title>
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