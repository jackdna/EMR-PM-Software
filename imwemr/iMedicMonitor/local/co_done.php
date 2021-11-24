<?php
include_once("../globals.php");
include_once("common_functions.php");

$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;
$done_with_pt = isset($_REQUEST['done_with_pt']) ? intval($_REQUEST['done_with_pt']) : 0;
if($sch_id >0){
	require_once("../../library/classes/scheduler/appt_schedule_functions.php");

	//scheduler object
	$obj_scheduler = new appt_scheduler();
	
	$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;
	$done_with_pt = isset($_REQUEST['done_with_pt']) ? intval($_REQUEST['done_with_pt']) : 0;
	
	if($sch_id > 0 && $done_with_pt==0){
		//logging this action in previous status table
		$obj_scheduler->logApptChangedStatus($sch_id, "", "", "", "11", "", "", "admin", "Checkout Out from iMedicMonitor.", "", false);
	
		//updating schedule appointments details
		$obj_scheduler->updateScheduleApptDetails($sch_id, "", "", "", "11", "", "", "admin", "", "", false);
	}else if($done_with_pt==1){
		
		$sql = "UPDATE patient_location SET pt_with=6, cur_time = '".date('H:i:s')."' WHERE sch_id = '".$sch_id."' AND cur_date = '".date('Y-m-d')."'";
		imw_query($sql);
	}
}
?>