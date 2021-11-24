<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "CREATE TABLE `erp_iportal_medication_refill` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`portal_req_id` VARCHAR( 200 ) NOT NULL ,
	`patientExternalId` VARCHAR( 200 ) NOT NULL ,
	`patientMedicationExternalId` VARCHAR( 200 ) NOT NULL ,
	`doctorExternalId` VARCHAR( 200 ) NOT NULL ,
	`status` VARCHAR( 200 ) NOT NULL ,
	`portalCreated` VARCHAR( 200 ) NOT NULL ,
	`patientComments` VARCHAR( 200 ) NOT NULL ,
	`pt_med_name` VARCHAR( 200 ) NOT NULL ,
	`pt_allery_name` text COLLATE utf8_unicode_ci NOT NULL ,
	`last_enc_date` datetime NOT NULL ,
	`approved_declined` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=approved, 2=declined',
	`created_on` datetime NOT NULL,
	`approved_by` int(10) NOT NULL DEFAULT '0',
	`operator` int(10) UNSIGNED NOT NULL DEFAULT '0',
	`action_date` datetime NOT NULL
	)
	";
imw_query($sql2) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 7 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 7 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 7</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
