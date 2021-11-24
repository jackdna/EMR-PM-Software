<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
 
$q = "ALTER TABLE `vital_sign_patient` ADD `inhale_O2` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
$r = imw_query($q) or $msg_info[]=imw_error();

$q = "ALTER TABLE `social_history` ADD `smoke_start_date` DATE NOT NULL";
$r = imw_query($q) or $msg_info[]=imw_error();
$q = "ALTER TABLE `social_history` ADD `smoke_end_date` DATE NOT NULL";
$r = imw_query($q) or $msg_info[]=imw_error();

$q = "ALTER TABLE `lists` ADD `procedure_status` VARCHAR( 50 ) NOT NULL DEFAULT '' ";
$r = imw_query($q) or $msg_info[]=imw_error();
$q = "ALTER TABLE `lists` ADD `assigning_authority_UDI` VARCHAR( 50 ) NOT NULL DEFAULT '' ";
$r = imw_query($q) or $msg_info[]=imw_error();

$q = "ALTER TABLE `lists` ADD `med_route` VARCHAR(50) NOT NULL";
$r = imw_query($q) or $msg_info[]=imw_error();
$q = "ALTER TABLE `lists` ADD `severity` VARCHAR(50) NOT NULL";
$r = imw_query($q) or $msg_info[]=imw_error();
$q = "ALTER TABLE `lists` ADD `reaction_code` VARCHAR(20) NOT NULL";
$r = imw_query($q) or $msg_info[]=imw_error();

$q = "ALTER TABLE `patient_data` ADD `lang_code` VARCHAR(10) NOT NULL AFTER `language`;";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 8  run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 8  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 8 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>