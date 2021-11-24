<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
Coded in PHP 7
Purpose: Providing required data to main interface of login (header/footer).
Access Type: include file.
*/
require_once(dirname(__FILE__).'/common_function.php');
class imedicmonitor
{
	private $hash_method, $logged_user, $logged_user_name, $login_facility;
	public $patient_id;
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct()
	{
		$this->session			= $_SESSION;
		$this->logged_user		= (isset($this->session["authId"]) 				&& $this->session["authId"] != "") 				? $this->session["authId"] 				: 0;
		$this->logged_user_type	= (isset($this->session["logged_user_type"]) 	&& $this->session["logged_user_type"] != "") 	? $this->session["logged_user_type"]	: 0;
		$this->logged_user_name	= (isset($this->session["authProviderName"]) 	&& $this->session["authProviderName"] != "") 	? $this->session["authProviderName"]	: '';
		$this->login_facility 	= (isset($this->session["login_facility"]) 		&& $this->session["login_facility"] != "") 		? $this->session["login_facility"] 		: 0;
		$this->hash_method		= constant('HASH_METHOD');
		$this->patient_id		= $this->session['patient'];
	}
	
	
	###################################################################
	#    to get defined rooms/ options or array.
	###################################################################
	function practice_rooms($give='result'){
		$rooms=false;
		$q = "SELECT * FROM mac_room_desc WHERE delete_status=0 AND room_no != '' ORDER BY room_no";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			if($give=='options') $rooms=''; else $rooms=array();
			while($rs = imw_fetch_assoc($res)){
				if($give=='options')$rooms .= '<option value="'.addslashes($rs['room_no']).'">'.stripslashes($rs['room_no']).'</option>'; else $rooms[$rs['id']]=$rs;
			}
		}
		return $rooms;
	}
	
	function ready_for($mode='get',$pt,$sch_id){
		
	}
	
	function pt_checked_in_appts_today($p=''){
		if($p=='') $p = $this->patient_id;
		$q = "SELECT sa.id, sa.sa_doctor_id, sa.sa_patient_id, sa.sa_patient_app_status_id, sa.sa_comments, sa.sa_app_starttime, sa.sa_app_endtime, sp.acronym,sa.pt_priority  
			  FROM schedule_appointments sa JOIN slot_procedures sp ON (sa.procedureid = sp.id ) 
			  WHERE sa.sa_patient_id = '".$p."' AND sa.sa_patient_app_status_id = '13' AND sa.sa_app_start_date = '".date('Y-m-d')."'";
		$res = imw_query($q);
		$result = false;
		if($res && imw_num_rows($res)>0){
			$result = array();
			while($rs = imw_fetch_assoc($res)){
			
				$rs['sa_comments'] = preg_replace("/[^a-zA-Z0-9]+/", "", $rs['sa_comments']);
		//		$rs['sa_comments'] = stripslashes(str_replace(array("'",":"),"",stripslashes($rs['sa_comments'])));
				$result[$rs['id']] = $rs;
			}
		}
		return $result;
	}
	
	function save_imonitor_settings(){
		//imon_sch_id=114641&r4tech=2&imon_rooms=Exam+Room+02&prio2=2&imon_comments=testsfsd+sdfsdfasdfadsf+
		$imon_sch_id 	= $_POST['imon_sch_id'];
		$imon_rooms 	= $_POST['imon_rooms'];
		$imon_comments 	= $_POST['imon_comments'];
		
		$pt_with = -1;
		foreach(array('r4doc','r4tech','r4test','r4wr','r4done') as $r4){
			if(isset($_POST[$r4])) $pt_with = $_POST[$r4];
		}
		
		$patient_location = array();
		$patient_location['doctor_id'] 	= $this->logged_user;
		$patient_location['app_room']	= stripslashes($imon_rooms);
		$patient_location['sch_id'] 	= $imon_sch_id;
		$patient_location['cur_date'] 	= date('Y-m-d');
		$patient_location['cur_time'] 	= date('H:i:s');
		if($pt_with >= 0) {
			$patient_location['pt_with'] 	= $pt_with;
			$action = getiMMStatus($pt_with);			
			if(!empty($action)) patient_monitor_daily($action,'',$imon_sch_id);
		}
		$patient_location['sch_message']= stripslashes(xss_rem($imon_comments));

		$pt_loc_record = $this->get_patient_locaton_record('',$imon_sch_id);
		if($pt_loc_record && is_array($pt_loc_record)){
			//Update record			
			$patient_location_id = $pt_loc_record[0]['patient_location_id'];
			$patient_location_id = UpdateRecords($patient_location_id,'patient_location_id',$patient_location,'patient_location');
		}else{
			//Add record
			$patient_location['patientId'] 	= $this->patient_id;
			$patient_location_id = AddRecords($patient_location,'patient_location');
		}
		
		
		$pt_priority = -1;
		foreach(array('prio0','prio1','prio2','prio3') as $prio){
			if(isset($_POST[$prio])) $pt_priority = $_POST[$prio];
		}
		if($pt_priority >= 0){
			$schedule_appointments = array();
			$schedule_appointments['pt_priority']	= $pt_priority;
			$imon_sch_id = UpdateRecords($imon_sch_id,'id',$schedule_appointments,'schedule_appointments');
		}
	}
	
	function get_patient_locaton_record($pt_id='',$sch_id='',$date='',$returnforjson=false){
		if($pt_id=='') $pt_id = $this->patient_id;
		if($date==''){$date=date('Y-m-d');}
		$q_part = "";
		if($sch_id!=''){$q_part = " AND sch_id='$sch_id'";}
		$q = "SELECT * FROM patient_location WHERE patientId='$pt_id'".$q_part." AND cur_date='$date' ORDER BY patient_location_id DESC";
		$res = imw_query($q);
		if($res && imw_num_rows($res)){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$rs['sch_message'] = preg_replace( "/\r|\n/", " ", $rs['sch_message']);
				
				if($returnforjson) $return[$rs['sch_id']] = $rs;
				else $return[] = $rs;
			}
			return $return;
		}
		return false;
	}
	
} //END CLASS
?>