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
File: change_pt_room_priority.php
Purpose: Main interface of iMedicMonitor 
Access Type: Include File
*/
include_once("../globals.php");
include_once("common_functions.php");

$sch_id = isset($_REQUEST['sch_id']) 	? intval($_REQUEST['sch_id']) 	: 0;
$task 	= isset($_REQUEST['task']) 		? trim($_REQUEST['task']) 	: '';

if($task=='moveup'){
	$room = isset($_REQUEST['room']) 	? trim($_REQUEST['room']) 	: '';
	if($room!=''){
		$sch = "SELECT GROUP_CONCAT(sch_id) AS sch_ids FROM patient_location WHERE app_room='".imw_real_escape_string($room)."' AND cur_date='".date('Y-m-d')."'";
		$res = imw_query($sch);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			$comm_sch_ids = $arr['sch_ids'];
			//--REMOVE imon_sequence OF ALL APPT----
			imw_query("UPDATE schedule_appointments SET imon_sequence = '0' WHERE id IN (".$comm_sch_ids.")");
			//---SET CURRENT APPOINTMENT imon_secuence to 1.---
			imw_query("UPDATE schedule_appointments SET imon_sequence = '1' WHERE id = '".$sch_id."' LIMIT 1");
		}
	}
	die;	
}



$pt_id = 0;
$sch = "SELECT sa_patient_id, sa_app_start_date,sa_app_starttime,sa_doctor_id,sa_facility_id,pt_priority FROM schedule_appointments WHERE id = '".$sch_id."' LIMIT 0,1";
$res = imw_query($sch);
if(imw_num_rows($res) > 0){
	while($arr = imw_fetch_assoc($res)){
		$pt_id 				= $arr["sa_patient_id"];
		$doc_id				= $arr["sa_doctor_id"];
		$sa_start_date 		= $arr["sa_app_start_date"];
		$sa_start_time 		= $arr["sa_app_starttime"];
		$sa_facility		= $arr["sa_facility_id"];
		$old_priority		= $arr["pt_priority"];
	}
}

if(in_array($task,array('priority_0','priority_1','priority_2','priority_3'))){
	$column = 'pt_priority';
	$value 	= explode('_',$task);
	$value	= $value[1];
}else if(in_array($task,array('task_1','task_2','task_3','task_4','task_6'))){
	$column = 'pt_with';
	$value 	= explode('_',$task);
	$value	= $value[1];
}else if(stristr($task,'taskc_')){
	$column = 'ready4_id';
	$value = explode('_',$task);
	$value = $value[1];
}else if(stristr($task,'room_')){
	$column = 'app_room';
	$temp_roomid 	= explode('_',$task);
	$temp_roomid	= $temp_roomid[1];
	$temp_room_result = imw_query("SELECT room_no FROM mac_room_desc WHERE id = '$temp_roomid'");
	if($temp_room_result && imw_num_rows($temp_room_result)==1){
		$temp_room_rs = imw_fetch_assoc($temp_room_result);
		$value = imw_real_escape_string($temp_room_rs['room_no']);
	}
}

if($pt_id > 0){
	if($column=='app_room' || $column=='pt_with' || $column=='ready4_id'){
		$upInsQuery_part = "";
		$qry = "SELECT patient_location_id, pt_with, ready4_id FROM patient_location WHERE patientId = '".$pt_id."' AND sch_id='".$sch_id."' AND cur_date = '".date("Y-m-d")."' ORDER BY patient_location_id DESC LIMIT 1";
		$res = imw_query($qry);
		$upInsQuery = '';
		if(imw_num_rows($res) > 0){
			while($arr = imw_fetch_assoc($res)){
				$plid = $arr["patient_location_id"];
				$db_pt_with = $arr["pt_with"];
				//$db_value2 = $arr["ready4_id"];
				//if($column=='pt_with' && $value=='4' && $db_pt_with!='4' && $value2=='' && $db_value2 != ''){$value2 = $db_value2;}
				//if($column=='app_room' && $value!=''){$column2 = 'pt_with'; $value2 = '';}
				if($column=='app_room' && ($db_pt_with=='3' || $db_pt_with=='4')){
					$upInsQuery_part = ", pt_with='0'";
				}
				if($column=='pt_with'){
					$action = getiMMStatus($value);
					patient_monitor_daily($action,$pt_id,$sch_id);
				}
			}
			$upInsQuery = "UPDATE patient_location SET ".$column." = '".$value."'".$upInsQuery_part." WHERE patientId = '".$pt_id."' AND sch_id='".$sch_id."' AND cur_date = '".date("Y-m-d")."'";
		}else{
			$upInsQuery = "INSERT INTO patient_location SET 
							patientId = '".$pt_id."',
							doctor_Id = '0',
							facility_Id = '0',
							`".$column."` = '".$value."',
							doctor_mess  = '',
							tech_click = '0',
							app_room_time = '',
							cur_date = '".date("Y-m-d")."',
							cur_time  = '".date("H:i:s")."',
							chart_opened = 'no',
							ready4DrId = '0',
							moved2Tech = '0',
							opidSent2Dr  = '0',
							opidSent2Tech = '0',
							sch_id = '".$sch_id."'";
		}
		
		
		if($upInsQuery!='') {
			imw_query($upInsQuery);
			//-------------RE-ARRANGING PATIENT PRIORITY---------------
			if($column=='pt_with' && $value=='6' && $old_priority > 0){re_arrange_priority($sch_id);}
		}
		
		if(strtolower($iMonitor_Server)=='miramar' && $column=='pt_with' && $value=='6'){
			//MARK CHECKOUT ALSO.
			$relative_local_interface_path = '../../interface/';
			require_once($relative_local_interface_path."scheduler_v1_1_1/appt_schedule_functions.php");
			$obj_scheduler = new appt_scheduler();	//scheduler object
			//logging this action in previous status table
			$obj_scheduler->logApptChangedStatus($sch_id, "", "", "", "11", "", "", "admin", "Checkout Out from iMedicMonitor.", "", false);
		
			//updating schedule appointments details
			$obj_scheduler->updateScheduleApptDetails($sch_id, "", "", "", "11", "", "", "admin", "", "", false);
			patient_monitor_daily('CHECK_OUT',$pt_id,$sch_id);
		}
	}else if($column == 'pt_priority'){
		if(intval($value)> intval($old_priority)){
			$mode='+';	
		}else if(intval($value)< intval($old_priority)){
			$mode='-';	
		}else{
			//do nothing.
			die();
		}
		
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
		$STR_DONE_APPTS = implode(',',$DONE_APPTS);

		$q = "SELECT id,pt_priority 
				FROM schedule_appointments 
				WHERE sa_app_start_date = '".$sa_start_date."' 
				AND sa_facility_id='".$sa_facility."' 
				AND sa_doctor_id = '".$doc_id."' 
				AND sa_patient_app_status_id='13' 
				AND pt_priority != '0' 
				AND id NOT IN ($STR_DONE_APPTS) 
				ORDER BY pt_priority DESC";
		$res = imw_query($q);
		
		if($res && imw_num_rows($res)>0){
			$first_record = true;
			$priority1_found = false;
			$priority2_found = false;
			$priority3_found = false;
			
			$arr_appt_priority = array();
			$arr_appt_ids_reprior = array();
			//$arr_priority_found = array();
			while($rs = imw_fetch_assoc($res)){
				if($rs['pt_priority']=='1') 		$priority1_found = true;
				else if($rs['pt_priority']=='2') 	$priority2_found = true;
				else if($rs['pt_priority']=='3') 	$priority3_found = true;
				$tmp_priority 	= $rs['pt_priority'];
				$tmp_apptid		= $rs['id'];
				$arr_appt_priority[$tmp_apptid] = $tmp_priority;
				$arr_appt_ids_reprior[] = $tmp_apptid;
				//$arr_priority_found[] = $tmp_priority;
			}
			$str_appt_ids_reprior = implode(',',$arr_appt_ids_reprior);		
			unset($tmp_apptid);
			unset($tmp_priority);
			unset($arr_appt_ids_reprior);
			imw_free_result($res);

			//LOGIC HERE TO UPDATE PRIORITIES.............
			if($mode=='+'){
				if($old_priority=='0'){
					if($value=='1'){
						if(($priority1_found && $priority2_found && $priority3_found) || ($priority1_found && $priority2_found)){
							imw_query("UPDATE schedule_appointments SET pt_priority='0' WHERE pt_priority = '3' AND id IN ($str_appt_ids_reprior) LIMIT 1");
							imw_query("UPDATE schedule_appointments SET pt_priority=(ABS(pt_priority)+1) WHERE pt_priority IN ('1','2') AND id IN ($str_appt_ids_reprior) LIMIT 2");
						}else if($priority1_found){
							imw_query("UPDATE schedule_appointments SET pt_priority = '2' WHERE pt_priority = '1' AND id IN ($str_appt_ids_reprior) LIMIT 1");
						}
					}else if($value=='2'){
						if($priority2_found){
							imw_query("UPDATE schedule_appointments SET pt_priority='0' WHERE pt_priority = '3' AND id IN ($str_appt_ids_reprior) LIMIT 1");
							imw_query("UPDATE schedule_appointments SET pt_priority='3' WHERE pt_priority = '2' AND id IN ($str_appt_ids_reprior) LIMIT 1");
						}
					}else if($value=='3'){
						if($priority3_found && $priority2_found){
							imw_query("UPDATE schedule_appointments SET pt_priority='0' WHERE pt_priority = '3' AND id IN ($str_appt_ids_reprior) LIMIT 1");
						}
					}
				}else if($old_priority=='1'){
					if($value=='2' && $priority2_found){
						imw_query("UPDATE schedule_appointments SET pt_priority='1' WHERE pt_priority = '2' AND id IN ($str_appt_ids_reprior) LIMIT 1");
					}else if($value=='3' && ($priority2_found || $priority3_found)){
						imw_query("UPDATE schedule_appointments SET pt_priority='1' WHERE pt_priority = '2' AND id IN ($str_appt_ids_reprior) LIMIT 1");
						imw_query("UPDATE schedule_appointments SET pt_priority='2' WHERE pt_priority = '3' AND id IN ($str_appt_ids_reprior) LIMIT 1");
					}
				}else if($old_priority=='2'){
					if($value=='3' && $priority3_found){
						imw_query("UPDATE schedule_appointments SET pt_priority='2' WHERE pt_priority = '3' AND id IN ($str_appt_ids_reprior) LIMIT 1");
					}
				}
			}else if($mode=='-'){
				if($value=='0'){
					if($old_priority=='1' && ($priority2_found || $priority3_found)){
						imw_query("UPDATE schedule_appointments SET pt_priority=(ABS(pt_priority)-1) WHERE pt_priority IN ('2','3') AND id IN ($str_appt_ids_reprior) LIMIT 2");
					}else if($old_priority=='2' && $priority3_found){
						imw_query("UPDATE schedule_appointments SET pt_priority='2' WHERE pt_priority ='3' AND id IN ($str_appt_ids_reprior) LIMIT 1");
					}
				}else if($value=='1'){
					if($old_priority=='2' && $priority1_found){
						imw_query("UPDATE schedule_appointments SET pt_priority='2' WHERE pt_priority = '1' AND id IN ($str_appt_ids_reprior) LIMIT 1");
					}else if($old_priority=='3' && ($priority1_found || $priority2_found)){
						imw_query("UPDATE schedule_appointments SET pt_priority=(ABS(pt_priority)+1) WHERE pt_priority IN ('1','2') AND id IN ($str_appt_ids_reprior) LIMIT 2");
					}
				}else if($value=='2'){
					if($old_priority=='3' && $priority2_found){
						imw_query("UPDATE schedule_appointments SET pt_priority='3' WHERE pt_priority = '2' AND id IN ($str_appt_ids_reprior) LIMIT 1");
					}
				}
			}
		}

		imw_query("UPDATE schedule_appointments SET ".$column."='".$value."' WHERE id = '".$sch_id."' LIMIT 1");
		//echo imw_error();
	}
}
?>