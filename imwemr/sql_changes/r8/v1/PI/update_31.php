<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info = array();

$sql1[] = "ALTER TABLE `insurance_companies`  ADD `external_id` VARCHAR(200) NOT NULL DEFAULT '0' AFTER `athenaID`";
$sql1[] = "ALTER TABLE `insurance_case`  ADD `external_id` VARCHAR(200) NOT NULL DEFAULT '0' AFTER `athenaID`";
$sql1[] = "ALTER TABLE `schedule_appointments`  ADD `external_id` VARCHAR(200) NOT NULL DEFAULT '0' AFTER `athenaID`";

foreach($sql1 as $sql){imw_query($sql) or $msg_info[] = imw_error();}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 31 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 31 Success.</b>";
	$color = "green";	
}
?>

<html>
<head>
<title>Update 31</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>External_ID column for insurance companies</h3>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>

</body>
</html>