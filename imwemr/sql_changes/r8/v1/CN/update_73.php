<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "ALTER TABLE `chart_pc_mr_values` CHANGE `txt_1` `txt_1` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL, CHANGE `txt_2` `txt_2` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";

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