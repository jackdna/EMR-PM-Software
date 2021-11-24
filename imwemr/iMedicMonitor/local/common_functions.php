<?php
function refine_input($str){
	$str = preg_replace("/-/", " ", $str);
	$str = str_replace("/", " ", $str);
	$str = preg_replace("/&/", "and", $str);
	$str = str_replace('<','&lt;',$str);
	$str = str_replace('>','&gt;',$str);
	return $str;
}

function core_name_format1($lname, $fname, $mname = "", $pfx = "", $sfx = ""){
	$return = "";
	if($lname != "" && $fname != "" && $mname != ""){
		$return .= $lname.", ".$fname." ".substr($mname, 0, 1).".";
	}else if($lname != "" && $fname != ""){
		$return .= $lname.", ".$fname;
	}else if($lname != ""){
		$return .= $lname;
	}
	if($pfx != ""){
		$return = $pfx." ".$return;
	}
	if($sfx != ""){
		$return = $return." ".$sfx;
	}
	return $return;
}

function objectsIntoArray($arrObjData, $arrSkipIndices = array()){
	$arrData = array();

	// if input is object, convert into array
	if (is_object($arrObjData)) {
		$arrObjData = get_object_vars($arrObjData);
	}
	
	if (is_array($arrObjData)) {
		foreach ($arrObjData as $index => $value) {
			if (is_object($value) || is_array($value)) {
				$value = objectsIntoArray($value, $arrSkipIndices); // recursive call
			}
			if (in_array($index, $arrSkipIndices)) {
				continue;
			}
			$arrData[$index] = $value;
		}
	}
	return $arrData;
}

function parse_xml_data($str_xml){
	$xmlObj = simplexml_load_string($str_xml);
	$arrXml = objectsIntoArray($xmlObj);
	//print_r($arrXml);
	return $arrXml;
}

function parse_check_sum_data($str_xml){
	$xmlObj = simplexml_load_string($str_xml);
	$arrXml = objectsIntoArray($xmlObj);
	//print_r($arrXml);
	$return = array();
	if(count($arrXml["record"]) > 0){
		for($c = 0; $c < count($arrXml["record"]); $c++){
			$return[$arrXml["record"][$c]["sum_id"]] = $arrXml["record"][$c]["sum_val"];
		}
	}
	//print_r($return);
	return $return;
	//return array();
}


function iMonProfiles(){
	$profiles=false;
	$q = "SELECT * FROM imonitor_roomview_profiles WHERE delete_status=0 ORDER BY title";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$profiles=array();
		while($rs = imw_fetch_assoc($res)){
			$profiles[$rs['id']]=$rs;
		}
	}
	return $profiles;
}

function ProviderReadyFor($page){
	$ProWiseReady4=false; $zeroProvider = array();
	$q = "SELECT id,provider_id,status_text FROM imonitor_ready_for WHERE delete_status=0 ORDER BY provider_id, sequence, status_text";
	$res = imw_query($q); echo imw_error();
	$ProWiseReady4=array();
	if($res && imw_num_rows($res)>0){
		$prev_provider = '';
		while($rs = imw_fetch_assoc($res)){
			if($rs['provider_id']==0){
				$ProWiseReady4['0']["task_1"] = array("name"=>"Doctor");
				if($page=='main'){
					$ProWiseReady4['0']["task_2"] = array("name"=>"Technician");
					$ProWiseReady4['0']["task_4"] = array("name"=>"Test/Waiting");
				}
			}
			$ProWiseReady4[$rs['provider_id']]["taskc_".$rs['id']] = array("name"=>$rs['status_text']);
			$prev_provider = $rs['provider_id'];
		}
	}
	
	if(count($ProWiseReady4['0'])==0)
	{
		$zeroProvider["task_1"] = array("name"=>"Doctor");
		if($page=='main'){
			$zeroProvider["task_2"] = array("name"=>"Technician");
			$zeroProvider["task_4"] = array("name"=>"Test/Waiting");
		}
		$ProWiseReady4['0'] = $zeroProvider;
	}
	else 
	{
		$zeroProvider = $ProWiseReady4['0'];
	}
	$finalProWiseReady4 = array();
	foreach($ProWiseReady4 as $pro=>$task_arr){
		$finalProWiseReady4[$pro] = array_merge($zeroProvider,$task_arr);
	}
	return $finalProWiseReady4;
}

function ProviderColors(){
	$colors=false;
	$q = "SELECT user_type_name,color FROM user_type WHERE color!='' AND status='1'";
	$res = imw_query($q);
	if($res && imw_num_rows($res)>0){
		$colors=array();
		while($rs = imw_fetch_assoc($res)){
			$colors[$rs['user_type_name']]=$rs;
		}
	}
	return $colors;
}

/*--GETTING BROWSER INFO--*/
$GLOBALS['gl_browser_name'] = get_browser_info();

function re_arrange_priority($sch_id){
	$sch = "SELECT sa_patient_id, sa_app_start_date,sa_app_starttime,sa_doctor_id,sa_facility_id,pt_priority FROM schedule_appointments WHERE id = '".$sch_id."' LIMIT 0,1";
	$res = imw_query($sch);
	if(imw_num_rows($res) == 1){
		$arr = imw_fetch_assoc($res);
		$pt_id 				= $arr["sa_patient_id"];
		$doc_id				= $arr["sa_doctor_id"];
		$sa_start_date 		= $arr["sa_app_start_date"];
		$sa_start_time 		= $arr["sa_app_starttime"];
		$sa_facility		= $arr["sa_facility_id"];
		$done_appt_priority	= $arr["pt_priority"];
		
		//--REMOVE PRIORITY OF CURRENT APPT (WHICH IS JUST "DONE" MARKED----
		imw_query("UPDATE schedule_appointments SET pt_priority = '0' WHERE id = '".$sch_id."'");
		
		//--GET APPOINTMENTS WHICH ARE "DONE" MARKED-----
		$DONE_APPTS = array();
		$ptl_q = "SELECT sch_id FROM patient_location WHERE cur_date='".date('Y-m-d')."' AND pt_with IN (5,6)";
		$ptl_res = imw_query($ptl_q);
		if($ptl_res && imw_num_rows($ptl_res)>0){
			while($ptl_rs = imw_fetch_assoc($ptl_res)){
				if(trim($ptl_rs['sch_id'])=='' || trim($ptl_rs['sch_id'])=='0'){continue;}
				$DONE_APPTS[] = trim($ptl_rs['sch_id']);
			}
		}
		
		//--ADDING CURRENT APPOINTMENT IN THIS LIST TO IGNORE--
		$DONE_APPTS[] = $sch_id;
		$STR_DONE_APPTS = implode(',',array_unique($DONE_APPTS));
		//--GET NEXT THREE APPOINTMENT JUST AFTER THE ABOVE--
		$q = "SELECT id,pt_priority 
				FROM schedule_appointments 
				WHERE sa_app_start_date = '".$sa_start_date."' 
						AND sa_facility_id='".$sa_facility."' 
						AND sa_doctor_id = '".$doc_id."' 
						AND sa_patient_app_status_id='13' 
						AND id NOT IN ($STR_DONE_APPTS) 
				ORDER BY pt_priority DESC, sa_app_starttime ASC LIMIT 0,3";
		$res = imw_query($q);
		$records_found = imw_num_rows($res);
		if($res && $records_found>0){
			$q = '';
			while($rs = imw_fetch_assoc($res)){
				//echo 'P1='.$priority1_found."\n P2=".$priority2_found."\n P3=".$priority3_found."\n\n";
				$new_sch_id = $rs['id'];
				$current_priority = $rs['pt_priority'];
				
				//GET OUT FROM THE LOOP; DON'T SET PRIORITY FOR ANY ONE, BECAUSE REMAINING APPT DON'T HAVE ANY PRIORITY.
				if($first_record && $current_priority=='0')	break;
				
				if($done_appt_priority=='1'){
					if($current_priority=='2' || $current_priority=='3')
						$q = "UPDATE schedule_appointments SET pt_priority = (ABS(pt_priority)-1) WHERE pt_priority IN ('2','3') AND id = '".$new_sch_id."'";
					else if($current_priority=='0')
						$q = "UPDATE schedule_appointments SET pt_priority = '3' WHERE pt_priority = '0' AND id = '".$new_sch_id."'";
				}else if($done_appt_priority=='2'){
					if($current_priority=='3')
						$q = "UPDATE schedule_appointments SET pt_priority = '2' WHERE pt_priority = '3' AND id = '".$new_sch_id."'";
					else if($current_priority=='0')
						$q = "UPDATE schedule_appointments SET pt_priority = '3' WHERE pt_priority = '0' AND id = '".$new_sch_id."'";
				}else if($done_appt_priority=='3'){
					if($current_priority=='0')
						$q = "UPDATE schedule_appointments SET pt_priority = '3' WHERE pt_priority = '0' AND id = '".$new_sch_id."'";
				}
				//echo $q.'<br>'.imw_error().'<hr>';
				if($q!= '') imw_query($q);
				$q = '';
			}
		}
	}
}


function get_master_data(){
	$xml_data = '<?xml version="1.0" encoding="UTF-8" ?>
	<response>';

	/*--------GENERATING FACILITY DATA TAGS--------------*/
	$facquery = "SELECT id, name, facility_type, waiting_timer FROM `facility`";
	$facresult = imw_query($facquery);
	if($facresult && imw_num_rows($facresult)>0){
	$xml_data .= '
		<facs>';
		while($facrs = imw_fetch_array($facresult)){
			$xml_data .= '
			<fac>
				<id>'.$facrs['id'].'</id>
				<type>'.$facrs['facility_type'].'</type>
				<name>'.refine_input($facrs['name']).'</name>
				<wait_timer>'.$facrs['waiting_timer'].'</wait_timer>
			</fac>';
		}
	$xml_data .= '
		</facs>';	
	}

	/*--------GENERATING PROVIDERS DATA TAGS--------------*/
	$docquery = "SELECT id, lname, fname, mname, user_type, username FROM `users` WHERE delete_status='0' AND Enable_Scheduler='1' ORDER BY lname, fname";
	$docresult = imw_query($docquery);
	if($docresult && imw_num_rows($docresult)>0){
	$xml_data .= '
		<docs>';
		while($docrs = imw_fetch_array($docresult)){
			$xml_data .= '
			<doc>
				<id>'.$docrs['id'].'</id>
				<type>'.$docrs['user_type'].'</type>
				<username>'.$docrs['username'].'</username>
				<name>'.refine_input(core_name_format($docrs['lname'], $docrs['fname'], $docrs['mname'])).'</name>
			</doc>';
		}
	$xml_data .= '
		</docs>';	
	}
	
	/*--------GENERATING PROCS (PROCEDURES) DATA TAGS--------------*/
	$proquery = "SELECT id, proc FROM `slot_procedures` WHERE active_status='yes' AND proc != ''";
	$proresult = imw_query($proquery);
	if($proresult && imw_num_rows($proresult)>0){
	$xml_data .= '
		<procs>';
		while($prors = imw_fetch_array($proresult)){
			$xml_data .= '
			<proc>
				<id>'.$prors['id'].'</id>
				<name>'.refine_input($prors['proc']).'</name>
			</proc>';
		}
	$xml_data .= '
		</procs>';	
	}
	
	/*--------GROUPS (NON-INSTITUTIONAL) DATA TAGS--------------*/
	$groquery = "SELECT gro_id, name FROM `groups_new` WHERE `group_institution` ='0' AND del_status='0' ORDER BY gro_id LIMIT 0,1";
	$groresult = imw_query($groquery);
	if($groresult && imw_num_rows($groresult)>0){
	$xml_data .= '
		<groups>';
		while($grors = imw_fetch_array($groresult)){
			$xml_data .= '
			<group>
				<id>'.$grors['gro_id'].'</id>
				<name>'.refine_input($grors['name']).'</name>
			</group>';
		}
	$xml_data .= '
		</groups>';	
	}
	

	/*--------iMedicMonitor GROUPS (Room groups)--------------*/
	$groquery2 = "SELECT id,group_name FROM imonitor_room_groups WHERE delete_status=0 ORDER BY group_order,group_name";
	$groresult2 = imw_query($groquery2);
	if($groresult2 && imw_num_rows($groresult2)>0){
	$xml_data .= '
		<iMongroup>';
		while($grors2 = imw_fetch_assoc($groresult2)){
			$xml_data .= '
			<iMongroup>
				<id>'.$grors2['id'].'</id>
				<name>'.refine_input($grors2['group_name']).'</name>
			</iMongroup>';
		}
	$xml_data .= '
		</iMongroup>';	
	}
	
	/*--------iMedicMonitor Rooms--------------*/
	$groquery3 = "SELECT id, mac_address,room_no,fac_id FROM mac_room_desc WHERE room_no!='' AND delete_status=0 ORDER BY room_no";
	$groresult3 = imw_query($groquery3);
	if($groresult3 && imw_num_rows($groresult3)>0){
	$xml_data .= '
		<iMonRooms>';
		while($grors3 = imw_fetch_assoc($groresult3)){
			$xml_data .= '
			<iMonRooms>
				<room_id>'.$grors3['id'].'</room_id>
				<room_no>'.refine_input($grors3['room_no']).'</room_no>
				<fac_id>'.$grors3['fac_id'].'</fac_id>
			</iMonRooms>';
		}
	$xml_data .= '
		</iMonRooms>';
	}

	
	/*--after all data genration closing main tag--*/
	$xml_data .= '
	</response>';
	return $xml_data;	
}

function get_imm_saved_configuration(){
	/*******GETTING SAVED IMM SETTINGS*******/
	$im_setting_res = imw_query("SELECT setting_name, practice_value FROM imonitor_settings");
	$im_settings_arr = array();
	if($im_setting_res){
		while($im_setting_rs = imw_fetch_assoc($im_setting_res)){
			$im_settings_arr[$im_setting_rs['setting_name']] = $im_setting_rs['practice_value'];
		}
	}
	return $im_settings_arr;
}
?>