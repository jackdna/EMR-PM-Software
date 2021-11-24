<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql="ALTER TABLE `dischargesummarysheet` ADD `icd10_code` TEXT NOT NULL AFTER `diag_ids`";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `dischargesummarysheet` ADD `icd10_id` TEXT NOT NULL AFTER `icd10_code`";
imw_query($sql) or $msg_info[] = imw_error();


$sql="ALTER TABLE `surgerycenter` ADD `diagnosis_code_type` VARCHAR( 255 ) NOT NULL ";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `surgeonprofileprocedure`  ADD `dx_id_icd10` TEXT NOT NULL,  ADD `dx_id_default_icd10` TEXT NOT NULL ";
imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 58 run OK";

?>

<html>
<head>
<title>Update 57</title>
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







