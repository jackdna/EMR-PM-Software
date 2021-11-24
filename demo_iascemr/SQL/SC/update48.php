<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "ALTER TABLE `patient_data_tbl`  ADD `language` VARCHAR(255) NOT NULL,  ADD `race` VARCHAR(255) NOT NULL,  ADD `ethnicity` VARCHAR(255) NOT NULL";
$row = imw_query($sql) or $msg_info[] = imw_error();
$sql = "ALTER TABLE `stub_tbl`  ADD `patient_language` VARCHAR(255) NOT NULL,  ADD `patient_race` VARCHAR(255) NOT NULL,  ADD `patient_ethnicity` VARCHAR(255) NOT NULL";
$row = imw_query($sql) or $msg_info[] = imw_error();

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 48 run OK";

?>

<html>
<head>
<title>Update 48</title>
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







