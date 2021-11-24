<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="CREATE TABLE `hl7_received_accno` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `hl7_received_id` INT NOT NULL COMMENT 'pkid of hl7_received', `patient_id` INT NOT NULL COMMENT 'pkid of patient_data', `doctor_id` INT NOT NULL COMMENT 'pkid of users', `facility_id` INT NOT NULL COMMENT 'pkid of facility', `dt_of_visit` DATE NOT NULL COMMENT 'appt date', `tm_of_visit` TIME NOT NULL COMMENT 'appt time', `account_num` VARCHAR(50) NOT NULL COMMENT 'extrnal visit account number', `entered_dt` DATETIME NOT NULL COMMENT 'when record created', `updated_dt` DATETIME NOT NULL COMMENT 'when record updated') ENGINE = MyISAM;";
imw_query($sql1) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 2 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 2 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 2 (HL7)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>