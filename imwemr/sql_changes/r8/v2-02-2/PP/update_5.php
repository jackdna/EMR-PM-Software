<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "CREATE TABLE `erp_iportal_patients_data` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`pt_portal_id` VARCHAR( 200 ) NOT NULL ,
	`portalCreated` VARCHAR( 200 ) NOT NULL ,
	`contactPrefixExternalId` VARCHAR( 200 ) NOT NULL ,
	`firstName` VARCHAR( 200 ) NOT NULL ,
	`middleName` VARCHAR( 200 ) NOT NULL ,
	`lastName` VARCHAR( 200 ) NOT NULL ,
	`contactSuffixExternalId` VARCHAR( 200 ) NOT NULL ,
	`sexExternalId` VARCHAR( 200 ) NOT NULL ,
	`username` VARCHAR( 200 ) NOT NULL ,
	`birthday` VARCHAR( 200 ) NOT NULL ,
	`address1` VARCHAR( 255 ) NOT NULL ,
	`address2` VARCHAR( 255 ) NOT NULL ,
	`city` VARCHAR( 200 ) NOT NULL ,
	`state` VARCHAR( 200 ) NOT NULL ,
	`zipCode` VARCHAR( 200 ) NOT NULL ,
	`countryName` VARCHAR( 200 ) NOT NULL ,
	`communicationsVoicePhone` VARCHAR( 200 ) NOT NULL ,
	`communicationsTextPhone` VARCHAR( 200 ) NOT NULL ,
	`communicationsEmail` VARCHAR( 200 ) NOT NULL,
	`approved_declined` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=approved, 2=declined',
	`created_on` datetime NOT NULL,
	`approved_by` int(10) NOT NULL DEFAULT '0',
	`operator` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`action_date` datetime NOT NULL
	)
	";
imw_query($sql2) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `erp_iportal_patients_data` ADD `patient_id` INT(11) NOT NULL ";
$result = imw_query($qry) or $msg_info[] = imw_error();

$sql3= "ALTER TABLE `erp_iportal_patients_data` ADD `is_reconciled` TINYINT(5) NOT NULL DEFAULT '0' COMMENT '1=new patient created, 2=reconciled with existing patient' ";
imw_query($sql3) or $msg_info[] = imw_error();

$sql3= "ALTER TABLE `erp_iportal_patients_data` ADD `pt_data_before_reconciled` text COLLATE utf8_unicode_ci NOT NULL ";
imw_query($sql3) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 5 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 5 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
