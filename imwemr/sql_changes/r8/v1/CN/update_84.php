<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$sql = "select * from merge_patient_tables where table_name = 'chart_vitreous' ";
$result = imw_query($sql);
$r = imw_num_rows($result);
if(empty($r)){
$s[] = "INSERT INTO `merge_patient_tables` (`id`, `table_name`, `pk_id`, `pt_id`, `database_name`, `created_on`, `status`) VALUES 
(NULL, 'chart_vitreous', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_drawings', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_lac_sys', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_lesion', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_lids', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_lid_pos', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_ant_chamber', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_conjunctiva', 'id', 'patient_id', '', NOW(), '1'),

(NULL, 'chart_cornea', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_iris', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_lens', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_blood_vessels', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_macula', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_periphery', 'id', 'patient_id', '', NOW(), '1'),

(NULL, 'chart_retinal_exam', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_vitreous', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_vis_master', 'id', 'patient_id', '', NOW(), '1'),
(NULL, 'chart_vis_lasik', 'id', 'patient_id', '', NOW(), '1');";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 84</title>
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