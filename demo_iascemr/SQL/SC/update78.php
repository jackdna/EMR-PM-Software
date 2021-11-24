<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `surgeonprofile` ADD `otherPreOpOrders` TEXT NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="CREATE TABLE `vital_sign_grid` (`gridRowId` bigint(20) NOT NULL AUTO_INCREMENT, `chartName` varchar(128) NOT NULL, `confirmation_id` int(11) NOT NULL, `start_time` time NOT NULL, `bp` varchar(32) NOT NULL, `pulse` varchar(32) NOT NULL, `temp` varchar(32) NOT NULL, `etco2` varchar(32) NOT NULL, `osat2` varchar(32) NOT NULL, PRIMARY KEY (`gridRowId`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; "; 
imw_query($sql1)or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 78 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 78 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 78</title>
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