<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "ALTER TABLE `chart_assessment_plans` ADD `doctorName_id` INT(10) NOT NULL AFTER `refer_to`;";

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