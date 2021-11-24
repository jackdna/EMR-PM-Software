<?php
include_once("../globals.php");
include_once("common_functions.php");

$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;
$text = isset($_REQUEST['newtime']) ? intval(trim($_REQUEST['newtime'])) : 20;

if($sch_id >0){
		
	$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;
	$newtime = isset($_REQUEST['newtime']) ? intval(trim($_REQUEST['newtime'])) : 0;
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
		$qry = "SELECT patient_location_id, cur_time FROM patient_location WHERE patientId = '".$pt_id."' AND doctor_mess='A/P Dilated' AND cur_date = '".date("Y-m-d")."' LIMIT 1";
		$res = imw_query($qry);// echo imw_error();
		if(imw_num_rows($res) > 0){
			while($arr = imw_fetch_array($res)){
				$plid = $arr["patient_location_id"];
				$curr_time = $arr["cur_time"];
				list($hour,$minute,$second) = explode(':',$curr_time);
			}
			$oldTime = date("H:i:s",mktime($hour,$minute,$second));
			$newTime = date("H:i:s",mktime(date('H'),(date('i')-21)+$newtime,date('s')));
			//echo $oldTime.' :: '.$newTime;
			$qry = "UPDATE patient_location SET cur_time='$newTime'	WHERE patient_location_id = '".$plid."'";
			imw_query($qry);
		}
	}

}
?>