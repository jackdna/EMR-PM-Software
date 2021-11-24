<?php 
require_once("common_functions.php");

$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;

$pt_id = 0;
$sch = "SELECT sa_patient_id, sa_app_start_date FROM schedule_appointments WHERE id = '".$sch_id."'";
$res = imw_query($sch);
if(imw_num_rows($res) > 0){
	while($arr = imw_fetch_array($res)){
		$pt_id = $arr["sa_patient_id"];
	}
}

if($pt_id > 0){
	$qry = "SELECT patient_location_id FROM patient_location WHERE patientId = '".$pt_id."' AND cur_date = '".date("Y-m-d")."' LIMIT 1";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
		while($arr = imw_fetch_array($res)){
			$plid = $arr["patient_location_id"];
		}
		$qry = "UPDATE patient_location SET 
						ready4DrId = '1' 
					WHERE patient_location_id = '".$plid."'";
		imw_query($qry);
	}else{
		$qry = "INSERT INTO patient_location SET 
						patientId = '".$pt_id."',
						doctor_Id = '0',
						facility_Id = '0',
						app_room = '',
						doctor_mess  = '',
						tech_click = '0',
						app_room_time = '',
						cur_date = '".date("Y-m-d")."',
						cur_time  = '".date("H:i:s")."',
						chart_opened = 'no',
						ready4DrId = '1',
						moved2Tech = '0',
						opidSent2Dr  = '0',
						opidSent2Tech = '0'";
		imw_query($qry);
	}
}
?>