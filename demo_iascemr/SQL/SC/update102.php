<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `postopnursingrecord` ADD `version_num` INT( 11 ) NOT NULL ;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `postopnursingrecord` ADD `version_date_time` DATETIME NOT NULL ;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `postopphysicianorders` ADD version_num INT( 11 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `postopphysicianorders` ADD version_date_time DATETIME NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="ALTER TABLE  `preopphysicianorders` ADD `version_num` INT( 11 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopphysicianorders` ADD `version_date_time` DATETIME NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD `version_num` INT( 11 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `version_date_time` DATETIME NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `saveFromChart` TINYINT( 2 ) NOT NULL COMMENT  'value set to 1 if save button used from sign all pre op orders'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `prefilMedicationStatus` VARCHAR( 10 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `prefilMedicationStatusSource` TINYINT NOT NULL COMMENT  'value set to 0 when source is chart and set to 1 when source is sign all pre op orders'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `comments` TEXT NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `chbx_saline_lockStart` VARCHAR( 10 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `chbx_saline_lock` VARCHAR( 10 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `ivSelection` VARCHAR( 255 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `ivSelectionOther` VARCHAR( 255 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `ivSelectionSide` VARCHAR( 255 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `chbx_KVO` VARCHAR( 5 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `chbx_rate` VARCHAR( 5 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `txtbox_rate` VARCHAR( 255 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `chbx_flu` VARCHAR( 5 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `txtbox_flu` VARCHAR( 255 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `gauge` VARCHAR( 32 ) NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `preopnursingrecord` ADD  `gauge_other` TEXT NOT NULL";
imw_query($sql1)or $msg_info[] = imw_error();



$sql1="ALTER TABLE  `patientconfirmation` ADD  `labor_cost` DECIMAL( 10, 2 ) NOT NULL ,
ADD  `supply_cost` DECIMAL( 10, 2 ) NOT NULL"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE `surgery_cost` (
`cost_detail_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`confirmation_id` INT NOT NULL ,
`patient_id` INT NOT NULL ,
`item_type` VARCHAR( 50 ) NOT NULL ,
`item_id` INT NOT NULL ,
`item_name` VARCHAR( 255 ) NOT NULL ,
`item_cost` DECIMAL( 10, 2 ) NOT NULL ,
`item_qty` INT NOT NULL ,
`item_total_cost` DECIMAL( 10, 2 ) NOT NULL ,
`deleted` TINYINT( 1 ) NOT NULL
) ENGINE = MYISAM ;"; 
imw_query($sql1)or $msg_info[] = imw_error();


$sql1=" UPDATE `postopnursingrecord` Set version_num = 1, version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0 ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1=" UPDATE `postopphysicianorders` Set version_num = 1, version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0 ";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1=" UPDATE `preopnursingrecord` Set version_num = 1, version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0 ";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1=" UPDATE `preopphysicianorders` Set version_num = 1, version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0 ";
imw_query($sql1)or $msg_info[] = imw_error();

/*
$sql1="
UPDATE  `postopnursingrecord` PN JOIN postopphysicianorders PP ON (PN.confirmation_id = PP.patient_confirmation_id)
SET 
		PP.version_num = CASE WHEN (PP.form_status <> '' AND PP.version_num = 0 ) Then 1 END,
		PN.version_num = CASE WHEN (PN.form_status <> '' AND PP.form_status = '' And PN.version_num = 0 ) Then 2 
													WHEN (PN.form_status <> '' AND PP.form_status <> '' And PN.version_num = 0 ) Then 1
													WHEN (PN.form_status = '' AND PP.form_status <> '' And PN.version_num = 0 ) Then 1 
										 END,
    
    PN.version_date_time = CASE WHEN (PN.form_status <> '' And PN.version_date_time = '0000-00-00 00:00:00') THEN '".date('Y-m-d H:i:s')."' END,	
    PP.version_date_time = CASE WHEN (PP.form_status <> '' And PP.version_date_time = '0000-00-00 00:00:00') THEN '".date('Y-m-d H:i:s')."' END	    		
		WHERE (PN.version_num = 0 && PP.version_num = 0) AND (PN.form_status <> '' || PP.form_status <> '')
";
imw_query($sql1)or $msg_info[] = imw_error();


$sql1="
UPDATE  `preopnursingrecord` PN JOIN preopphysicianorders PP ON (PN.confirmation_id = PP.patient_confirmation_id)
SET 
		PP.version_num = CASE WHEN (PP.form_status <> '' AND PP.version_num = 0 ) Then 1 END,
		PN.version_num = CASE WHEN (PN.form_status <> '' AND PP.form_status = '' And PN.version_num = 0 ) Then 2 
													WHEN (PN.form_status <> '' AND PP.form_status <> '' And PN.version_num = 0 ) Then 1
													WHEN (PN.form_status = '' AND PP.form_status <> '' And PN.version_num = 0 ) Then 1 
										 END,
    
    PN.version_date_time = CASE WHEN (PN.form_status <> '' And PN.version_date_time = '0000-00-00 00:00:00') THEN '".date('Y-m-d H:i:s')."' END,	
    PP.version_date_time = CASE WHEN (PP.form_status <> '' And PP.version_date_time = '0000-00-00 00:00:00') THEN '".date('Y-m-d H:i:s')."' END
		WHERE (PN.version_num = 0 && PP.version_num = 0) AND (PN.form_status <> '' || PP.form_status <> '')
";
imw_query($sql1)or $msg_info[] = imw_error();

*/

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 102 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 102 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 102</title>
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