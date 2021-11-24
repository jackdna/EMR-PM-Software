<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("../../common/conDb.php");


$sql1="ALTER TABLE `patient_data_tbl` ADD `source` VARCHAR( 10 ) NOT NULL COMMENT 'From where record created'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patient_in_waiting_tbl` ADD `source` VARCHAR( 10 ) NOT NULL COMMENT 'From where record created'";
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 94 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 94 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 94</title>
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