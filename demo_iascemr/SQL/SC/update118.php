<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");


$sql1="ALTER TABLE  `surgerycenter` ADD  `fire_risk_analysis` VARCHAR( 5 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE surgerycenter SET fire_risk_analysis = 'N' WHERE surgeryCenterId = '1' AND fire_risk_analysis = '' ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `surgical_check_list` ADD `surgical_xiphoid` VARCHAR( 5 ) NOT NULL ,
ADD `fire_risk_score` VARCHAR( 5 ) NOT NULL ,
ADD `oxygen_source` VARCHAR( 5 ) NOT NULL ,
ADD `ignition_source` VARCHAR( 5 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `surgical_check_list` ADD `fire_risk_active_status` VARCHAR( 10 ) NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 118 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 118 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 118</title>
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