<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `password_change_reset_audit_tbl` ADD `operator_id` INT NOT NULL AFTER `status`, ADD `operator_date_time` DATETIME NOT NULL AFTER `operator_id`, ADD `comments` VARCHAR(255) NOT NULL AFTER `operator_date_time`;";
$sql[] = "ALTER TABLE `surgerycenter` ADD `vital_time_slot` INT NOT NULL AFTER `fire_risk_analysis`;";
$sql[] = "UPDATE `surgerycenter` SET `vital_time_slot`=5";
$sql[] = "ALTER TABLE `vitalsign_tbl` ADD `vitalSignTemp` VARCHAR(32) NOT NULL AFTER `vitalSignO2SAT`;";
$sql[] = "ALTER TABLE `postopnursingrecord` ADD `bs_na` TINYINT(1) NULL AFTER `version_date_time`, ADD `bs_value` VARCHAR(32) NULL AFTER `bs_na`;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 145 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 145 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 145</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>