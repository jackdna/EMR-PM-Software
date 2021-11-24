<?php
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

$sql2= "ALTER TABLE `patient_relations` 
ADD `general_health_status` INT( 2 ) NOT NULL ,
ADD `erp_pat_rel_id` VARCHAR( 200 ) NOT NULL; ";
imw_query($sql2) or $msg_info[] = imw_error();

$sql2= "ALTER TABLE `patient_relations` 
ADD INDEX del_status(del_status),
ADD INDEX general_health_status(general_health_status),
ADD INDEX erp_pat_rel_id(erp_pat_rel_id);";
imw_query($sql2) or $msg_info[] = imw_error();

$relation_arr = array();
$gen_hlth_rec_exist = false;
$qry = "SELECT id, relation, general_health_status FROM patient_relations WHERE del_status = '0' ORDER BY id";
$res = imw_query($qry) or $msg_info[] = imw_error();
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$relation_arr[] = strtolower($row['relation']);
		if($row['general_health_status']=='1') {
			$gen_hlth_rec_exist = true;	
		}
	}
}

//START CODE to ADD RECORD FOR Medical Hx > General Health > dropdown list of family relation
if(!$gen_hlth_rec_exist) {
	$gen_hlth_rel_arr = array('Brother', 'Daughter', 'Father', 'Mother', 'Sister', 'Son', 'Spouse', 'Grandmother', 'Grandfather');
	foreach($gen_hlth_rel_arr as $val) {
		$ins_id = '';
		if(!in_array(strtolower($val),$relation_arr)) {
			$qry_ins = "INSERT INTO patient_relations (relation, general_health_status) VALUES('".$val."','1')";
			imw_query($qry_ins) or $msg_info[] = imw_error();
			$ins_id = imw_insert_id();
		}
		
		if(!$ins_id) {
			$qry_updt = "UPDATE patient_relations SET general_health_status = '1' WHERE LOWER(relation) = '".strtolower($val)."' ";
			imw_query($qry_updt) or $msg_info[] = imw_error();
		}
	}
}
//END CODE to ADD RECORD FOR Medical Hx > General Health > dropdown list of family relation

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 1 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 1 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>
