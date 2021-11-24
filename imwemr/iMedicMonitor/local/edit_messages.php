<?php
include_once("../globals.php");
require_once("common_functions.php");

$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;
$text = isset($_REQUEST['msgtxt']) ? trim($_REQUEST['msgtxt']) : '';

$pt_id = 0;
$sch = "SELECT sa_patient_id, sa_app_start_date FROM schedule_appointments WHERE id = '".$sch_id."'";
$res = imw_query($sch);
if(imw_num_rows($res) > 0){
	while($arr = imw_fetch_array($res)){
		$pt_id = $arr["sa_patient_id"];
	}
}

if($pt_id > 0){
	$text = str_replace("&nbsp;", "", $text);
	$qry = "SELECT patient_location_id FROM patient_location WHERE patientId = '".$pt_id."' AND sch_id='".$sch_id."' AND cur_date = '".date("Y-m-d")."' ORDER BY patient_location_id DESC LIMIT 1";
	$res = imw_query($qry);
	if(imw_num_rows($res) > 0){
		while($arr = imw_fetch_array($res)){
			$plid = $arr["patient_location_id"];
		}
		$qry = "UPDATE patient_location SET 
						sch_message  = '".imw_real_escape_string($text)."' 
					WHERE patientId = '".$pt_id."' AND sch_id='".$sch_id."' AND cur_date = '".date("Y-m-d")."'";
		imw_query($qry);
	}else{
		$qry = "INSERT INTO patient_location SET 
						patientId = '".$pt_id."',
						doctor_Id = '0',
						facility_Id = '0',
						app_room = '',
						sch_message  = '".$text."',
						tech_click = '1',
						app_room_time = '',
						cur_date = '".date("Y-m-d")."',
						cur_time  = '".date("H:i:s")."',
						chart_opened = 'no',
						ready4DrId = '0',
						moved2Tech = '0',
						opidSent2Dr  = '0',
						opidSent2Tech = '0',
						sch_id = '".$sch_id."'";
		imw_query($qry);
		
	}
	/*
	if(strtolower(trim($text)) == "done" && $sch_id > 0){
		//check out the patient
		$relative_local_interface_path = '../../interface/';
		require_once($relative_local_interface_path."scheduler_v1_1_1/appt_schedule_functions.php");

		//scheduler object
		$obj_scheduler = new appt_scheduler();

		//logging this action in previous status table
		$obj_scheduler->logApptChangedStatus($sch_id, "", "", "", "11", "", "", "admin", "Checkout Out from iMedicMonitor.", "", false);

		//updating schedule appointments details
		$obj_scheduler->updateScheduleApptDetails($sch_id, "", "", "", "11", "", "", "admin", "", "", false);
	}
	*/
}
?>