<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "ALTER TABLE `chart_assessment_plans` ADD `refer_to_id` INT(10) NOT NULL AFTER `soc_desc`, ADD `transition_reason` TINYTEXT NOT NULL AFTER `refer_to_id`, ADD `transition_notes` TINYTEXT NOT NULL AFTER `transition_reason`;";
$s[] = "ALTER TABLE `chart_assessment_plans` ADD `refer_to` VARCHAR(200) NOT NULL AFTER `transition_notes`;";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 73</title>
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