<?php
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../../../config/globals.php");

$msg_info=array();

$s = array();
$s[] = "ALTER TABLE `tests_version`  ADD `test_main_options_ids` VARCHAR(200) NULL AFTER `test_main_option_mo_counter`,  ADD `test_main_options_text` VARCHAR(250) NULL AFTER `test_main_options_ids`";
$s[] = "ALTER TABLE `superbill_test_cpt`  ADD `custom_test_variation_id` INT NOT NULL DEFAULT '0' COMMENT 'for custom test variations'";
foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();	
}

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 130  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 130  run successfully!</b>";
    $color = "green";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 130: Work for CPT preferences in Custom Tests</title>
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