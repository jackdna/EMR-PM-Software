<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql18="ALTER TABLE `tsys_payment_type_log` 
ADD `operator_id` INT( 10 ) NOT NULL ,
ADD `scheduID` INT( 10 ) NOT NULL ,
ADD `encounter_id` INT( 10 ) NOT NULL ,
ADD `laneId` INT( 10 ) NOT NULL ,
ADD `merchant_id` INT( 10 ) NOT NULL ,
ADD `facility_id` INT( 10 ) NOT NULL ,
ADD `transactionAmount` DECIMAL( 10, 2 ) NOT NULL ,
ADD `device_id` INT( 10 ) NOT NULL ,
ADD `device_url` VARCHAR( 512 ) NOT NULL ,
ADD `final_hex_string` VARCHAR( 512 ) NOT NULL,
ADD `tsysOrderNumber` text COLLATE utf8_unicode_ci NOT NULL,
ADD `transactionType` VARCHAR( 50 ) NOT NULL,
ADD `transactionNumber` VARCHAR( 150 ) NOT NULL ";
imw_query($sql18) or $msg_info[] = imw_error();


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 106  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 106  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 106</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>