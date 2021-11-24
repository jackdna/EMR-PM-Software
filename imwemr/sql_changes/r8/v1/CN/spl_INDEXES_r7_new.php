<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();



$sql="SHOW INDEX FROM chart_dialation WHERE key_name = 'form_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `chart_dialation` ADD INDEX (  `form_id` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

//
$arr=array("oct_rnfl", "surgical_tbl", "iol_master_tbl", "test_bscan", "test_cellcnt", "test_gdx", "test_labs", "test_other", "disc", "disc_external", 
			"nfa", "icg", "ivfa", "oct", "pachy", "topography", "vf_gl", "vf");
foreach($arr as $key => $tbl){

	$pphy="formId";
	if($tbl == "oct_rnfl" || $tbl == "surgical_tbl" || $tbl == "iol_master_tbl" || $tbl == "test_gdx"
		|| $tbl == "nfa" || $tbl == "icg" || $tbl == "ivfa" || $tbl == "oct"){ $pphy="form_id"; }
	
	//
	$sql = "SHOW INDEXES FROM ".$tbl." WHERE Column_name = '".$pphy."' ";
	//$sql =  "SELECT ".$pphy." FROM ".$tbl." ";
	$row=sqlQuery($sql);	
	if($row==false){
		$sql=" ALTER TABLE  `".$tbl."` ADD INDEX (  `".$pphy."` ) ; ";		
		$row = imw_query($sql) or $msg_info[] = imw_error();
		echo "<br/>".$sql;
	}
}

$sql="SHOW INDEX FROM insurance_data WHERE key_name = 'expiration_date' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `insurance_data` ADD INDEX(`expiration_date`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM insurance_data WHERE key_name = 'actInsComp' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `insurance_data` ADD INDEX(`actInsComp`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM superbill WHERE key_name = 'dateOfService' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `superbill` ADD INDEX(`dateOfService`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM recent_users WHERE key_name = 'provider_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `recent_users` ADD INDEX(`provider_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_reff WHERE key_name = 'ins_data_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `patient_reff` ADD INDEX(`ins_data_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM test_bscan WHERE key_name = 'patientId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `test_bscan` ADD INDEX (  `patientId` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}


$sql="SHOW INDEX FROM patient_data WHERE key_name = 'providerID' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `patient_data` ADD INDEX (  `providerID` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM schedule_appointments WHERE key_name = 'iolink_connection_settings_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `schedule_appointments` ADD INDEX (  `iolink_connection_settings_id` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'External_MRN_1' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `patient_data` ADD INDEX (  `External_MRN_1` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM patient_data WHERE key_name = 'External_MRN_2' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `patient_data` ADD INDEX (  `External_MRN_2` )";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM order_sets WHERE key_name = 'delete_status' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `order_sets` ADD INDEX (  `delete_status` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM order_set_associate_chart_notes_details WHERE key_name = 'order_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `order_set_associate_chart_notes_details` ADD INDEX (  `order_id` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM order_details WHERE key_name = 'delete_status' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `order_details` ADD INDEX (  `delete_status` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM order_set_associate_chart_notes WHERE key_name = 'plan_num' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE  `order_set_associate_chart_notes` ADD INDEX (  `plan_num` );";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM common_phrases WHERE key_name = 'providerID' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `common_phrases` ADD INDEX(`providerID`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM console_to_do WHERE key_name = 'providerID' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `console_to_do` ADD INDEX(`providerID`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_pt_lock WHERE key_name = 'userId' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_pt_lock` ADD INDEX(`userId`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM superbill WHERE key_name = 'sch_app_id' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `superbill` ADD INDEX(`sch_app_id`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

//
$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'pid_dos_id' ";
$row=sqlQuery($sql);
if($row != false){
$sql="ALTER TABLE `chart_master_table` DROP INDEX pid_dos_id ";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_master_table WHERE key_name = 'date_of_service' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_master_table` ADD INDEX(`date_of_service`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="SHOW INDEX FROM chart_assessment_plans WHERE key_name = 'uid' ";
$row=sqlQuery($sql);
if($row == false){
$sql="ALTER TABLE `chart_assessment_plans` ADD INDEX(`uid`);";
$row = imw_query($sql)or $msg_info[] = imw_error();
echo "<br/>".$sql;
}

$sql="CREATE INDEX chartdialation_examdate ON chart_dialation(exam_date);";
$row=sqlQuery($sql);

//"?

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 7: Update Index run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 7: Update Index run successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Update Indexes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color_sts;?>" size="2">
    <?php echo(@implode("<br>",$msg_info_sts));?>
</font>
</body>
</html>