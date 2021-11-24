<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="CREATE TABLE `patient_physician_orders` (
		  `recordId` int(11) NOT NULL AUTO_INCREMENT,
		  `confirmation_id` int(11) NOT NULL,
		  `chartName` varchar(128) NOT NULL,
		  `physician_order_name` varchar(255) NOT NULL,
		  `physician_order_time` time NOT NULL,
		  PRIMARY KEY (`recordId`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `postopphysicianorders` ADD  `patientAssessed` CHAR( 3 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `patientBmi` VARCHAR( 255 ) NOT NULL AFTER  `patientWeight`"; 
imw_query($sql1)or $msg_info[] = imw_error();


imw_query("ALTER TABLE  `preopphysicianorders` ADD  `notedByNurse` TINYINT( 1 ) NOT NULL")or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 80 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 80 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 80</title>
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