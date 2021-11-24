<?php 
require_once("common_functions.php");
/* --------COMMENTED ON 9 DEC 14 FOR TIMEZONE ISSUE 
$tz_value = trim($_GET['local_tz']);
$tz_value = str_replace('p','+',$tz_value);
$tz_value = str_replace('m','-',$tz_value);

if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='safar'){
	$tz_value = '-05:00';
}else if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='centerforsight'){
	$tz_value = '-07:00';
}else if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='pimaeye'){
	$tz_value = '-06:00';
}
*/
$iMonRooms = iMonRooms();
$tz_value = trim(urldecode($_GET['local_tz']));
if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='centerforsight'){
//	$tz_value =  -480;
}else if(isset($iMonitor_Server) && strtolower(trim($iMonitor_Server))=='dedhameye'){
	if($tz_value == '300') $tz_value == '240';
	else if($tz_value == '240') $tz_value == '300'; 
}

$xml_data = '<?xml version="1.0" encoding="UTF-8" ?>
<response>';

/*--------GENERATING check-in patients from schedule_appointments DATA TAGS--------------*/
$schquery = "SELECT schedule_appointments.id, sa_patient_id, sa_patient_name, sa_patient_app_status_id, sa_doctor_id, sa_madeby, sa_facility_id, 
			 TIME_FORMAT(sa_app_starttime, '%h:%i %p') as sa_app_starttime007, sa_comments, pt_priority, u1.fname as u1fname, u1.lname as u1lname, 
			 u1.mname as u1mname, u2.fname as u2fname, u2.lname as u2lname, u2.mname as u2mname, sp.proc, sp.acronym 
			 FROM `schedule_appointments` 
			 JOIN users u1 ON (u1.id = schedule_appointments.sa_doctor_id AND u1.delete_status=0) 
			 LEFT JOIN users u2 ON (u2.id = schedule_appointments.status_update_operator_id AND u2.delete_status=0) 
			 LEFT JOIN slot_procedures sp ON sp.id = schedule_appointments.procedureid 
			 WHERE u1.delete_status=0 and sa_app_start_date='".date("Y-m-d")."' AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
			 ORDER BY sa_app_starttime, imon_sequence DESC, sa_doctor_id";
$schresult = imw_query($schquery);
if($schresult && imw_num_rows($schresult)>0){
$arr_appts = array();
$arr_sch = array();
$arr_pts =  array();
while($schrs = imw_fetch_array($schresult)){
	$arr_appts[] = $schrs['id'];
	$arr_sch[] = $schrs;
	$arr_pts[] = $schrs['sa_patient_id'];
}
array_push($arr_appts, -1);
array_push($arr_pts, -1);
$arr_pts = array_unique($arr_pts);
$str_appts = "'".implode("','", $arr_appts)."'";
$str_pts = "'".implode("','", $arr_pts)."'";

$arr_chk_appt = array();
//$qry2 = "SELECT sch_id, CONCAT(status_date,' ',status_time) as status_time, CONCAT(status_date,' ',if(status_time>=old_time,status_time,old_time)) AS tz_time FROM previous_status WHERE sch_id IN (".$str_appts.") AND status = '13' AND status_date='".date("Y-m-d")."' ORDER BY id ASC";
$qry2 = "SELECT sch_id, CONCAT(status_date,' ',status_time) as status_time, CONCAT(status_date,' ',if(status_time>=old_time,status_time,old_time)) AS tz_time FROM previous_status WHERE status = '13' AND status_date='".date("Y-m-d")."'";
$res2 = imw_query($qry2);//TIME_FORMAT(status_time, '%h:%i %p')
$prev_chkin_schID = ''; 
if(imw_num_rows($res2) > 0){
	while($schrs = imw_fetch_array($res2)){
		if(!in_array($schrs['sch_id'],$arr_appts)){continue;}
		if($schrs["sch_id"]==$prev_chkin_schID){continue;}else{$prev_chkin_schID=$schrs["sch_id"];}
		if(isset($tz_value) && $tz_value!=''){
			//$TZ_query = "SELECT DATE_FORMAT(CONVERT_TZ('".$schrs['status_time']."','".date_default_timezone_get()."','".$tz_value."'),'%h:%i %p') as status_time1, DATE_FORMAT('".$schrs['status_time']."','%h:%i %p') as status_time";
			$TZ_query = "SELECT CONVERT_TZ('".$schrs['tz_time']."','".date_default_timezone_get()."','".$tz_value."') as status_time1, DATE_FORMAT('".$schrs['status_time']."','%h:%i %p') as status_time";
		}else{
			$TZ_query = "SELECT '".$schrs['tz_time']."' as status_time1, DATE_FORMAT('".$schrs['status_time']."','%h:%i %p') as status_time";	
		}//echo($TZ_query);
		$TZ_res = imw_query($TZ_query);
		$TZ_rs = imw_fetch_assoc($TZ_res);
		$arr_chk_appt[$schrs["sch_id"]] = $TZ_rs["status_time"];
		$arr_chk_appt_picktime[$schrs["sch_id"]] = $TZ_rs["status_time1"];
	}
}


$arr_chkout_appt = array();
//$qryout2 = "SELECT sch_id, CONCAT(status_date,' ',status_time) as status_time FROM previous_status WHERE sch_id IN (".$str_appts.") AND status = '11' ORDER BY id ASC";
$qryout2 = "SELECT sch_id, CONCAT(status_date,' ',status_time) as status_time FROM previous_status WHERE status = '11' AND status_date='".date("Y-m-d")."' ORDER BY id ASC";
$resout2 = imw_query($qryout2);
if(imw_num_rows($resout2) > 0){
	while($schrsout = imw_fetch_array($resout2)){
		if(!in_array($schrsout['sch_id'],$arr_appts)){continue;}
			
		if(isset($tz_value) && $tz_value!=''){
			$TZCO_query = "SELECT DATE_FORMAT(CONVERT_TZ('".$schrsout['status_time']."','".date_default_timezone_get()."','".$tz_value."'),'%h:%i %p') as status_time";
		}else{
			$TZCO_query = "SELECT DATE_FORMAT('".$schrsout['status_time']."','%h:%i %p') as status_time";	
		}
		$TZCO_res = imw_query($TZCO_query);
		$TZCO_rs = imw_fetch_assoc($TZCO_res);
		$arr_chkout_appt[$schrsout["sch_id"]] = $TZCO_rs["status_time"];
	}
}

$arr_pl_det = array();
//$pl_qry = "SELECT patient_location.*,CONCAT(cur_date,' ',cur_time) AS cur_time FROM patient_location WHERE patientId IN (".$str_pts.") AND cur_date = '".date("Y-m-d")."' AND sch_id IN ($str_appts) ORDER BY patient_location_id DESC";
$pl_qry = "SELECT patient_location.*,CONCAT(cur_date,' ',cur_time) AS cur_time FROM patient_location WHERE cur_date = '".date("Y-m-d")."' ORDER BY patient_location_id DESC";
if(isset($tz_value) && $tz_value!=''){
	/*
	$pl_qry = "SELECT pl.*,
					CONCAT(cur_date,' ',cur_time) AS cur_time, 
					CONVERT_TZ(CONCAT(cur_date,' ',cur_time),'".date_default_timezone_get()."','".$tz_value."') as cur_time1, 
					irf.status_text AS ready_for_status_text,
					REPLACE(irf.status_color,'#','') AS ready_for_status_color, 
					REPLACE(irf.status_text_color,'#','') AS ready_for_status_text_color  
					FROM patient_location pl 
					LEFT JOIN imonitor_ready_for irf ON (pl.ready4_id=irf.id) 
					WHERE pl.cur_date = '".date("Y-m-d")."' 
						AND pl.sch_id IN ($str_appts) 
						AND pl.patientId IN (".$str_pts.") 
					ORDER BY patient_location_id DESC";
					*/
	$pl_qry = "SELECT pl.*,
					CONCAT(cur_date,' ',cur_time) AS cur_time, 
					CONVERT_TZ(CONCAT(cur_date,' ',cur_time),'".date_default_timezone_get()."','".$tz_value."') as cur_time1, 
					irf.status_text AS ready_for_status_text,
					REPLACE(irf.status_color,'#','') AS ready_for_status_color, 
					REPLACE(irf.status_text_color,'#','') AS ready_for_status_text_color  
					FROM patient_location pl 
					LEFT JOIN imonitor_ready_for irf ON (pl.ready4_id=irf.id) 
					WHERE pl.cur_date = '".date("Y-m-d")."' 
					ORDER BY patient_location_id DESC";
}
$pl_res = imw_query($pl_qry);
if(imw_num_rows($pl_res) > 0){
	while($pl_arr = imw_fetch_array($pl_res)){
		if(!in_array($pl_arr['sch_id'],$arr_appts)){continue;}
		if(!in_array($pl_arr['patientId'],$arr_pts)){continue;}
		
		$arr_pl_det[$pl_arr["sch_id"]] = $pl_arr;
	}
}
//echo '<pre>';print_r($arr_pl_det);die();
$arr_dil_det = array();
$frm_id_qry = "SELECT id AS form_id,patient_id,provIds FROM chart_master_table WHERE date_of_service='".date("Y-m-d")."'";
$frm_id_res = imw_query($frm_id_qry);
if($frm_id_res && imw_num_rows($frm_id_res)>0){
	$tmp_form_ID_array = array();
	while($frm_id_rs = imw_fetch_assoc($frm_id_res)){
		$tmp_form_ID_array[] = $frm_id_rs['form_id'];
		$arr_dil_det[$frm_id_rs["patient_id"]]["scribe_or_tech"] = get_imon_recent_tech_scribe_from_list($frm_id_rs['provIds']);
		//$arr_dil_det[$dil_arr["patient_id"]]["exam_date"] 	= $dil_arr["exam_date"];
	}
	$todays_form_IDs = implode(',',$tmp_form_ID_array);
	unset($tmp_form_ID_array); imw_free_result($frm_id_res);

	$dil_qry = "SELECT dilated_time, exam_date, noDilation, patient_id,spDialTime FROM chart_dialation WHERE form_id IN ($todays_form_IDs) ORDER BY dilated_time DESC";
	$dil_res = imw_query($dil_qry);
	if(imw_num_rows($dil_res) > 0){
		while($dil_arr = imw_fetch_assoc($dil_res)){
			if(!in_array($dil_arr["patient_id"],$arr_pts)){continue;}
			if(stristr($dil_arr['spDialTime'],'Refuse Dilation')) {$dil_arr["noDilation"] = 1;}
			$arr_dil_det[$dil_arr["patient_id"]]["dilated_time"] = trim($dil_arr["dilated_time"]);
			$arr_dil_det[$dil_arr["patient_id"]]["noDilation"] 	= $dil_arr["noDilation"];
			$arr_dil_det[$dil_arr["patient_id"]]["exam_date"] 	= $dil_arr["exam_date"];
		}
	}
}
/*----GETTING ALL PROVIDERS WITH THEIR TYPES-*/
$arr_providerDet = array();
$query_ptype="SELECT u.id, u.fname, u.mname, u.lname, u.Enable_Scheduler, ut.user_type_name,ut.color FROM users u 
			  JOIN user_type ut ON(ut.user_type_id=u.user_type) 
			  WHERE u.delete_status='0' 
			  AND (ut.user_type_name='Physician' OR ut.user_type_name='Technician' OR ut.user_type_name='Test' OR ut.user_type_name='Scribe')";
$result_ptype = imw_query($query_ptype);
if($result_ptype){
	while($rs_ptype=imw_fetch_array($result_ptype)){
		$phyId = $rs_ptype['id'];
		$phyType = $rs_ptype['user_type_name'];
		if($phyType=='Scribe') $phyType = 'Technician';
		$phyColor_Arr = explode(',',$rs_ptype['color']);
		$phyColor = trim(strtolower($phyColor_Arr[0]));
		$schEna = $rs_ptype['Enable_Scheduler'];
		if($phyType=='Test' && $schEna == '0'){continue;}
		$arr_providerDet[$phyId]['utype'] = $phyType;
		$arr_providerDet[$phyId]['ucolor'] = $phyColor;
		$arr_providerDet[$phyId]['sch'] = $schEna;
		$arr_providerDet[$phyId]['LocDocNm'] = core_name_format($rs_ptype['lname'], $rs_ptype['fname'], $rs_ptype['mname']);
	}
}
//echo '<pre>';print_r($arr_providerDet);die();

$int_wait_timer = 600;
$facquery = "SELECT waiting_timer FROM `facility` WHERE facility_type = '1'";
$facresult = imw_query($facquery);
if($facresult && imw_num_rows($facresult)>0){
	while($facrs = imw_fetch_array($facresult)){
		$int_wait_timer = (int)$facrs["waiting_timer"];
	}
	if(empty($int_wait_timer)){
		$int_wait_timer = 600;
	}
}

$xml_data .= '
	<pts>';
	for($s = 0; $s < count($arr_sch); $s++){
		$chk_Tm = (isset($arr_chk_appt[$arr_sch[$s]['id']])) ? $arr_chk_appt[$arr_sch[$s]['id']] : "N/A";
		$waiting_4long = 0;
		if($chk_Tm != "N/A"){
			$chk_temp = explode(":", $chk_Tm);
			$chk_Tm_h = isset($chk_temp[0]) ? (int)$chk_temp[0] : 0;
			$chk_Tm_m = isset($chk_temp[1]) ? (int)$chk_temp[1] : 0;
			$chk_Tm_s = isset($chk_temp[2]) ? (int)$chk_temp[2] : 0;
			$time_diff = mktime(date("H"), date("i"), date("s")) - mktime($chk_Tm_h, $chk_Tm_m, $chk_Tm_s);
			if($time_diff >= $int_wait_timer){
				$waiting_4long = 1;
			}
		}
		$msg = ($arr_sch[$s]['sa_comments'] != "") ? $arr_sch[$s]['sa_comments'] : "";

		$doctor_mess = "N/A";
		$tech_click = "N/A";
		$app_room_time = "N/A";
		$chart_opened = "N/A";
		$ready4DrId = "N/A";
		$moved2Tech = "N/A";
		$opidSent2Dr = "N/A";			
		$opidSent2Tech = "N/A";
		$sent2SC = 0;
		$app_room = '';
		$room_op = '';		
		$room_op_type = '';
		$room_op_color = '';		
		$room_op_name = '';
		$waiting_pt = 'N';
		$watingg_pt_icon = '';
		$waiting_start_from = '';
		$pt_with = '';
		$ready_for_status_text = '';
		$ready_for_status_color = '';
		$ready_for_status_text_color = '';
		
		
		if(isset($arr_pl_det[$arr_sch[$s]["id"]])){
			$doctor_mess = $arr_pl_det[$arr_sch[$s]["id"]]["doctor_mess"];
			$sch_message = $arr_pl_det[$arr_sch[$s]["id"]]["sch_message"];
			$tech_click = $arr_pl_det[$arr_sch[$s]["id"]]["tech_click"];
			$chart_opened = $arr_pl_det[$arr_sch[$s]["id"]]["chart_opened"];
			$ready4DrId = $arr_pl_det[$arr_sch[$s]["id"]]["ready4DrId"];
			$moved2Tech = $arr_pl_det[$arr_sch[$s]["id"]]["moved2Tech"];
			$opidSent2Dr = $arr_pl_det[$arr_sch[$s]["id"]]["opidSent2Dr"];
			$opidSent2Tech = $arr_pl_det[$arr_sch[$s]["id"]]["opidSent2Tech"];
			$sent2SC = $arr_pl_det[$arr_sch[$s]["id"]]["sent2SC"];
			$app_room = $arr_pl_det[$arr_sch[$s]["id"]]["app_room"];
			$ready_for_status_text = $arr_pl_det[$arr_sch[$s]["id"]]["ready_for_status_text"];
			$ready_for_status_color = $arr_pl_det[$arr_sch[$s]["id"]]["ready_for_status_color"];
			$ready_for_status_text_color = $arr_pl_det[$arr_sch[$s]["id"]]["ready_for_status_text_color"];
			$pt_with = $arr_pl_det[$arr_sch[$s]["id"]]["pt_with"];
				if($app_room == NULL){$app_room='N/A';}
			if($app_room != 'N/A'){$room_no = $iMonRooms[$app_room];}else{$room_no=0;}
			$room_op = !empty($app_room) ? $arr_pl_det[$arr_sch[$s]["id"]]["doctor_Id"] : '';
			$room_op_type = !empty($app_room) ? $arr_providerDet[$room_op]['utype'] : '';
			$room_op_color = !empty($app_room) ? $arr_providerDet[$room_op]['ucolor'] : '';
			$room_op_name = !empty($app_room) ? $arr_providerDet[$room_op]['LocDocNm'] : '';
			$waiting_start_from = $arr_pl_det[$arr_sch[$s]["id"]]["cur_time"];
			$waiting_start_from1 = $arr_pl_det[$arr_sch[$s]["id"]]["cur_time1"];
			if($pt_with=='4'){$room_op_type=''; $waiting_pt='Y';}
			
			if(stristr($doctor_mess,'A/P Dilated')){
				if($arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["noDilation"]==0){
					$waiting_pt_icon = 'icon16_dilation';
					$waiting_pt = 'Y';
					$doctor_mess='';
				}else if($arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["noDilation"]==1){
					$doctor_mess = 'A/P Dilated -(No Dilation)';
				}
			}
			if(stristr($doctor_mess,'ready for test')){
				$waiting_pt_icon = 'icon16_test';
				$waiting_pt = 'Y';
				$doctor_mess='';
			}
			if(stristr($doctor_mess,'ready for cl') || stristr($doctor_mess,'ready for contact') || stristr($doctor_mess,'ready for contact lens')){
				$waiting_pt_icon = 'icon16_cl';
				$waiting_pt = 'Y';
				$doctor_mess='';
			}
			if($sent2SC>0){
				$waiting_pt_icon = 'icon16_surgiCordi';
				$waiting_pt = 'Y';			
			}
		}
		$q_TZ = "SELECT CONVERT_TZ('".$waiting_start_from."','".date_default_timezone_get()."','".$tz_value."') AS waiting_start_from1";
		$res_TZ = imw_query($q_TZ);
		$row_TZ = imw_fetch_assoc($res_TZ);
		$arr = explode(" ",$waiting_start_from);
		$waiting_start_from = isset($arr[1]) ? $arr[1] : '';
		//$arrDialated = unserialize($arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["dilated_time"]);
		$dilated_time = isset($arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["dilated_time"]) ? $arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["dilated_time"] : '';//$arrDialated[0]['time'];
		$dilated_time = trim($arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["exam_date"]." ".date("H:i:s",strtotime($dilated_time)));
		$scribe_or_tech = trim($arr_dil_det[$arr_sch[$s]["sa_patient_id"]]["scribe_or_tech"]);
		$doctor_mess = (isset($sch_message) && $sch_message!='') ? $sch_message : $doctor_mess;
		$sch_message = '';
		$arr_sch[$s]['sa_patient_name'] = str_replace('  ',' ',$arr_sch[$s]['sa_patient_name']);
		
		/****filtering msg***/
		$msg = str_replace('<','&lt;',$msg);
		$msg = str_replace('>','&gt;',$msg);
		$msg = str_replace(array("\r", "\n"), '', $msg);
		
		$arr_sch[$s]['proc'] 		= str_replace(array("\r", "\n"), '', $arr_sch[$s]['proc']);
		$doctor_mess 				= str_replace(array("\r", "\n"), '', $doctor_mess);
		$app_room 					= str_replace(array("\r", "\n"), '', $app_room);
		$ready_for_status_text 		= str_replace(array("\r", "\n"), '', $ready_for_status_text);
		
		
		$xml_data .= '
		<pt>
			<id>'.$arr_sch[$s]['id'].'</id>
			<pid>'.$arr_sch[$s]['sa_patient_id'].'</pid>
			<st>'.$arr_sch[$s]['sa_patient_app_status_id'].'</st>
			<name>'.htmlentities(addslashes($arr_sch[$s]['sa_patient_name'])).'</name>
			<doc>'.$arr_sch[$s]['sa_doctor_id'].'</doc>
			<scribeortech>'.$scribe_or_tech.'</scribeortech>
			<docnm>'.addslashes(core_name_format($arr_sch[$s]['u1lname'], $arr_sch[$s]['u1fname'], $arr_sch[$s]['u1mname'])).'</docnm>
			<opr>'.$arr_sch[$s]['sa_madeby'].'</opr>
			<oprnm>'.addslashes(core_name_format($arr_sch[$s]['u2lname'], $arr_sch[$s]['u2fname'], $arr_sch[$s]['u2mname'])).'</oprnm>
			<fac>'.$arr_sch[$s]['sa_facility_id'].'</fac>
			<pt_priority>'.$arr_sch[$s]['pt_priority'].'</pt_priority>
			<msg>'.htmlentities($msg, ENT_QUOTES).'</msg>
			<tm>'.$arr_sch[$s]['sa_app_starttime007'].'</tm>
			<proc>'.htmlentities($arr_sch[$s]['proc']).'</proc>
			<ci>'.$chk_Tm.'</ci>
			<ci_picktime>'.$arr_chk_appt_picktime[$arr_sch[$s]['id']].'</ci_picktime>
			<co>'.$arr_chkout_appt[$arr_sch[$s]['id']].'</co>
			<doctor_mess>'.htmlentities($doctor_mess, ENT_QUOTES).'</doctor_mess>
			<tech_click>'.$tech_click.'</tech_click>
			<chart_opened>'.$chart_opened.'</chart_opened>
			<ready4DrId>'.$ready4DrId.'</ready4DrId>
			<moved2Tech>'.$moved2Tech.'</moved2Tech>
			<opidSent2Dr>'.$opidSent2Dr.'</opidSent2Dr>
			<opidSent2Tech>'.$opidSent2Tech.'</opidSent2Tech>
			<sent2SC>'.$sent2SC.'</sent2SC>
			<pt_with>'.$pt_with.'</pt_with>
			<room_no>'.$room_no.'</room_no>
			<room>'.stripslashes($app_room).'</room>
			<roomop>'.$room_op.'</roomop>
			<roomopnm>'.$room_op_name.'</roomopnm>
			<roomoptype>'.$room_op_type.'</roomoptype>
			<roomopcolor>'.$room_op_color.'</roomopcolor>
			<locCurrTime>'.$waiting_start_from.'</locCurrTime>
			<locCurrTime1>'.$row_TZ['waiting_start_from1'].'</locCurrTime1>
			<waitingPt>'.$waiting_pt.'</waitingPt>
			<waitingPtIcon>'.$waiting_pt_icon.'</waitingPtIcon>
			<waiting_4long>'.$waiting_4long.'</waiting_4long>
			<dilated_time>'.$dilated_time.'</dilated_time>
			<ready_for_status_text>'.$ready_for_status_text.'</ready_for_status_text>
			<ready_for_status_color>'.$ready_for_status_color.'</ready_for_status_color>
			<ready_for_status_text_color>'.$ready_for_status_text_color.'</ready_for_status_text_color>
		</pt>';
	}
$xml_data .= '
	</pts>';
}
/*--after all data genration closing main tag--*/
$xml_data .= '
</response>';
//$xml_data = preg_replace('/[^a-zA-Z0-9_*\- ~.:<>?\/="]/',' ',$xml_data);
echo $xml_data;
?>