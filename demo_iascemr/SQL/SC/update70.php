<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `laser_procedure_template` ADD  `laser_preop_medication` TEXT NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopmedicationorder` ADD  `sourcePage` TINYINT NOT NULL DEFAULT  '0' COMMENT  '0 for surgeon profile, 1 for Laser template'"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `patientpreopmedication_tbl` ADD  `sourcePage` TINYINT NOT NULL COMMENT  '0 for PreOp Physician Orders and 1 for Laser Procedure'"; 
imw_query($sql1)or $msg_info[] = imw_error();




if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 70 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 70 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 70</title>
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