<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

$qry = "ALTER TABLE `patient_messages` ADD `iportal_msg_id` VARCHAR(255) NOT NULL AFTER `pt_resp_party_id`;";
$result = imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `patient_messages` ADD `from_rep` VARCHAR(255) NOT NULL AFTER `iportal_msg_id`;";
$result = imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `patient_messages` ADD `in_pt_comm` INT(2) NOT NULL AFTER `from_rep`;";
$result = imw_query($qry) or $msg_info[] = imw_error();

$qry = "ALTER TABLE `user_messages` ADD `pt_msg_id` INT(10) NOT NULL AFTER `saved_folder_id`;";
$result = imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 4  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 4 run successfully!</b>";
    $color = "green";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 4</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>
