<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "ALTER TABLE `operative_procedures` ADD `dx_code_id` TINYTEXT NOT NULL AFTER `count`;";
$s[] = "ALTER TABLE `chart_pt_assessment_plans` ADD `dxcode` VARCHAR(255) NOT NULL AFTER `diag_order`;";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 76</title>
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