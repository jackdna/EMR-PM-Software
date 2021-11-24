<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "ALTER TABLE `chart_drawings` ADD UNIQUE `form_id_2`( `form_id`, `purged`, `exam_name`);";
$s[] = "ALTER TABLE `chart_lac_sys` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_lac_sys` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_lesion` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_lid_pos` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_lids` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_lens` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_iris` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_ant_chamber` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_cornea` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_conjunctiva` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_retinal_exam` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_blood_vessels` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_periphery` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_macula` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";
$s[] = "ALTER TABLE `chart_vitreous` ADD UNIQUE `form_id_2`( `form_id`, `purged`);";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();	
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 70</title>
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