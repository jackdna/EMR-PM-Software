<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("common/conDb.php");

$query1="
UPDATE `laser_procedure_template` SET `laser_surgeonID`='all' WHERE `laser_surgeonID`='';
";



imw_query($query1) or die(imw_error());
$msg_info[] = "<br><br><b>Laser Template Updated Successfully</b>";
?>
<html>
<head>
<title>Mysql Updates After Launch </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>
