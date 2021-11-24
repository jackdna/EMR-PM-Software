<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE  `patientconfirmation` ADD  `cost_procedure_id` INT NOT NULL AFTER  `asc_id_before_merge`";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `patientconfirmation` ADD  `cost_procedure_name` TEXT NOT NULL AFTER  `cost_procedure_id`";
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 133 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 133 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 133</title>
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