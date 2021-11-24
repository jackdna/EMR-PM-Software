<?php

/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
Purpose: Providing required data to main interface of iMedicMonitor.
Access Type: include file.
*/
require_once(dirname(__FILE__).'/common_functions.php');
class immDb
{
	public $selectedFacs,$selectedProvs,$allFacsRS,$allProvsRS,$sort_by,$sort_order;
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct($facs='',$provs='',$tz_value='',$sort_by='',$sort_order=''){
		$this->allFacsRS = $this->getApptFacilities();
		$this->allProvsRS = $this->getApptProviders();
		$this->sort_by = $sort_by;
		$this->sort_order = $sort_order;
		$this->selectedFacs = !empty($facs)   ? $facs  : (is_array($this->allFacsRS) ? $this->allFacsRS['ApptFacilities'] : 0);
		$this->selectedProvs = !empty($provs) ? $provs : (is_array($this->allProvsRS) ? $this->allProvsRS['ApptProviders'] : 0);
		$this->allFacsRS = is_array($this->allFacsRS) ? $this->allFacsRS['resultSet'] : false;
		$this->allProvsRS = is_array($this->allProvsRS) ? $this->allProvsRS['resultSet'] : false;
		if(!$this->allFacsRS || !$this->allProvsRS) die('Exceptional Error!. Master data load failed.');
	}
	
	function getApptFacilities(){
		$facIDs = '0';
		$q = "SELECT id,name FROM facility ORDER BY name";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$tempArrFacIDs = array();
			$returnArr = array();
			while($rs = imw_fetch_assoc($res)){
				$tempArrFacIDs[] = $rs['id'];
				$returnArr[$rs['id']] = $rs['name'];
			}
			$facIDs = implode(',',$tempArrFacIDs);	
			return array('resultSet'=>$returnArr,'ApptFacilities'=>$facIDs);
		}else{
			return false;
		}
	}
		
	function getApptProviders(){
		$ProvIDs = '0';
		$q = "SELECT id,lname,fname,mname FROM users WHERE Enable_Scheduler='1' AND delete_status='0' ORDER BY lname,fname";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$tempArrProvIDs = array();
			$returnArr = array();
			while($rs = imw_fetch_assoc($res)){
				$tempArrProvIDs[] = $rs['id'];
				
				$id = $rs['id'];
				$arr_proName = array();
				$arr_proName['LAST_NAME']=$rs['lname'];
				$arr_proName['FIRST_NAME']=$rs['fname'];
				$arr_proName['MIDDLE_NAME']=$rs['mname'];
				$proDisplayName = changeNameFormat($arr_proName);
				unset($rs['fname']);
				unset($rs['lname']);
				unset($rs['mname']);
				$rs['name'] = $proDisplayName;				
				$returnArr[$rs['id']] = $rs['name'];
			}
			$ProvIDs = implode(',',$tempArrProvIDs);	
			return array('resultSet'=>$returnArr,'ApptProviders'=>$ProvIDs);
		}else{
			return false;
		}
	}
	
	function getApptsToday(){
		$q = "SELECT schedule_appointments.id, sa_patient_id, sa_patient_name, sa_patient_app_status_id, sa_doctor_id, status_update_operator_id, sa_facility_id, ";
		$q .="sa_app_starttime, sa_comments, pt_priority, sp.proc, sp.acronym ";
		$q .="FROM `schedule_appointments` ";
		$q .="LEFT JOIN slot_procedures sp ON sp.id = schedule_appointments.procedureid ";
		$q .="JOIN users u ON u.id = schedule_appointments.sa_doctor_id ";
		$q .="WHERE sa_app_start_date='".date("Y-m-d")."' AND sa_patient_app_status_id NOT IN (203,201,18,19,20) ";
		$q .="AND sa_facility_id IN (".$this->selectedFacs.") AND sa_doctor_id IN (".$this->selectedProvs.") ";
		$sort_by = "";
		if($this->sort_by=='doctor'){
			$sort_by = "CONCAT(u.fname,u.lname) ".strtoupper($this->sort_order).", ";
		}else if($this->sort_by=='appt_type'){
			$sort_by = "sp.proc ".strtoupper($this->sort_order).", ";
		}
		$q .="ORDER BY ".$sort_by."sa_app_starttime, imon_sequence DESC";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$allApptRS = array();
			while($rs = imw_fetch_assoc($res)){
				$rs['sa_doctor_name'] = $this->allProvsRS[$rs['sa_doctor_id']];
				$rs['sa_operator_name'] = $this->allProvsRS[$rs['status_update_operator_id']];
				unset($rs['status_update_operator_id']);
				$allApptRS[$rs['id']] = $rs;
			}
			return $allApptRS;
		}
		return '';
	}
	
	function getApptStatus($apptId=''){
		$where = ""; if(!empty($apptId)) $where = " AND sch_id = '".$apptId."'";
		$q = "SELECT sch_id, CONCAT(status_date,' ',status_time) as status_time, status FROM previous_status WHERE status IN ('4','13','11') ";
		$q .= "AND status_date='".date("Y-m-d")."'".$where;
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$sch_id = $rs['sch_id'];
				$status_id = $rs['status'];
				$dt = date_create($rs['status_time']);
				$return[$sch_id][$status_id]['db'] = $rs['status_time'];
				$return[$sch_id][$status_id]['show'] = date_format($dt,'h:i a');
			}
			return $return;
		}
	}
	
	function getPatientLocation($apptId=''){
		$where = ""; if(!empty($apptId)) $where = " AND sch_id = '".$apptId."'";
		$q="SELECT doctor_Id,app_room,doctor_mess,app_room_time,cur_date,cur_time,chart_opened,pt_with,sch_id FROM patient_location ";
		$q .= "WHERE cur_date='".date('Y-m-d')."'".$where." ORDER BY app_room_time";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			$ready4RS = $this->getReady4Status();
			//$getUsersNTypes = $this->getUsersNTypes();
			while($rs = imw_fetch_assoc($res)){
				$rs['ready4_text'] = $ready4RS[$rs['ready4_id']]['status_text'];
				$rs['ready4_color'] = $ready4RS[$rs['ready4_id']]['status_color'];
				$rs['ready4_textcolor'] = $ready4RS[$rs['ready4_id']]['status_text_color'];
			//	$rs['user_type_id']	= $getUsersNTypes[$rs['doctor_Id']]['user_type_id'];
			//	$rs['user_type_name'] = $getUsersNTypes[$rs['doctor_Id']]['user_type_name'];
				$return[$rs['sch_id']] = $rs;
			}
			return $return;
		}
		return false;		
	}
	
	function getPatientMonitor($apptId=''){
		if(empty($apptId)) return;
		$q="SELECT user_id,user_type_id,scheduler_appt_id as appt_id,patient_id,action_name,DATE_FORMAT(action_date_time,'%H:%i:%s') AS action_time,"; 	
		$q.="app_room FROM patient_monitor_daily WHERE DATE_FORMAT(action_date_time,'%Y-%m-%d')='".date('Y-m-d')."' AND scheduler_appt_id='".$apptId."' ";
		$q.="ORDER BY action_date_time";
	//	echo $q.'<br><br><br>';die;
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs;
			}
			return $return;
		}
		return false;		
	}
	
	function FSprintToday(){
		$q="SELECT appt_id FROM `pt_docs_patient_templates` WHERE appt_id>0 AND print_from='scheduler' AND created_date>'".date('Y-m-d 00:00:00')."'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[]= $rs['appt_id'];
			}
			return $return;
		}
		return false;		
	}
	
	function getReady4Status(){
		$q = "SELECT id,status_text,status_color,status_text_color FROM imonitor_ready_for WHERE delete_status='0' ORDER BY sequence,status_text";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[$rs['id']] = $rs;
			}
			return $return;
		}
		return false;
	}
	
	function getUsersNTypes(){
		$q = "SELECT users.id,user_type_id,user_type_name,color,CONCAT(users.lname,', ',users.fname)as user_name,fname,lname,mname FROM users JOIN user_type ON (users.user_type=user_type.user_type_id)";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$uid = $rs['id']; 	unset($rs['id']);
				$rs['initials'] = strtoupper(substr($rs['fname'],0,1).substr($rs['lname'],0,1));
				$return[$uid] = $rs;
			}
			return $return;
		}
		return false;		
	}
	
	function getRooms($idwise = false){
		$rooms=false;
		$q = "SELECT id,room_no FROM mac_room_desc WHERE delete_status=0";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rooms=array();
			while($rs = imw_fetch_assoc($res)){
				if($idwise) $rooms[$rs['id']]=$rs['room_no'];
				else $rooms[$rs['room_no']]=$rs['id'];
			}
			return $rooms;
		}else{
			return false;
		}
	}
	
	function getReadyFor(){
		$q = "SELECT id,provider_id,status_text FROM imonitor_ready_for WHERE delete_status=0 ORDER BY provider_id, sequence, status_text";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$ProWiseReady4=array();
			while($rs = imw_fetch_assoc($res)){
				$ProWiseReady4[] = $rs;
			}
			return $ProWiseReady4;
		}else{
			return false;
		}
	}
	
	function getImmExtendedCols(){
		$q = "SELECT id,show_status FROM imonitor_extended_cols ORDER BY id";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return=array();
			while($rs = imw_fetch_assoc($res)){
				$return[$rs['id']] = $rs['show_status'];
			}
			return $return;
		}
		return false;
	}
	
	function get_imm_configuration(){
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
}
?>