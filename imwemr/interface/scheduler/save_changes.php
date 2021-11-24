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
*/
//------FILE INCLUSION------
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//------XML FUNCTION--------
require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
$OBJCommonFunction = new CLSCommonFunction;

//------SCHEDULER OBJECT----
$obj_scheduler = new appt_scheduler();
$_REQUEST['init_st_time_rs'] =$_REQUEST['init_et_time_rs'] =$_REQUEST['init_date_rs'] ='';

//------IF WE MISSING START TIME, DATE OR END TIME IN REQUEST PARAMETERS THEN DIRECTLY ACQUIRE THEM FROM DATABASE
if(isset($_REQUEST['init_st_time_rs']) && $_REQUEST['ap_id'])
{
	//WE WERE LOSING APPT PREVIOUS DETAIL AT THE TIME OF RESCHEDULING AND RESULTING IN  LOSING LABELS, SO WE ARE REASSIGNING THESE VARIABLE
	$qApptDet=imw_query("
						SELECT 
							sa_doctor_id, 
							sa_facility_id, 
							sa_app_starttime, 
							sa_app_endtime, 
							sa_app_start_date 
						FROM 
							`schedule_appointments` 
						WHERE 
							id='$_REQUEST[ap_id]'
						");
	if(imw_num_rows($qApptDet)>=1)
	{
		$rApptDet=imw_fetch_object($qApptDet);
		
		$_REQUEST['init_provider_id']=$rApptDet->sa_doctor_id;	
		$_REQUEST['init_fac_id']=$rApptDet->sa_facility_id;
		$_REQUEST['init_date_rs']=$rApptDet->sa_app_start_date;
		$_REQUEST['init_st_time_rs']=$rApptDet->sa_app_starttime;
		$_REQUEST['init_et_time_rs']=$rApptDet->sa_app_endtime;
	}
}

//------MANAGING MULTI APPT REQUEST HERE------
if($_REQUEST['multi_sel_string'])
{
	$sel_arr=explode('~::~',$_REQUEST['multi_sel_string']);
	
	foreach($sel_arr as $newString)
	{
		list($_REQUEST['start_time'], $_REQUEST['tmp_id'], $_REQUEST['label_t'], $_REQUEST['doctor_id'], $_REQUEST['ap_procedure'], $_REQUEST['facility_id'])=explode('~:~',$newString);
		
		//$_REQUEST['ap_procedure']=$_REQUEST['tempproc'];
	
	//------GETTING ALL PROC ACRONYMS------
	$arr_proc_acro = array();
	$q = "	SELECT 
				id, 
				proc, 
				acronym 
			FROM 
				`slot_procedures` 
			WHERE 
				times = '' 
			AND 
				proc != '' 
			AND 
				doctor_id = 0 
			AND 
				active_status!='del' 
			ORDER BY proc";
	$r = imw_query($q);
	if(imw_num_rows($r) > 0)
	{
		while($a=imw_fetch_assoc($r))
		{
			$arr_proc_acro[$a["id"]] = $a["acronym"];
		}
	}
	
	$int_custom_lbl_id = 0;
	$str_custom_lbl_acronym = "";
	$arr_st_date = explode("-", $_REQUEST["start_date"]);
	$st_date = date("Y-m-d", mktime(0, 0, 0, $arr_st_date[0], $arr_st_date[1], $arr_st_date[2]));
	
	if(isset($_REQUEST["save_type"]) && $_REQUEST["save_type"] != "save")
	{
		$clbl_case = $obj_scheduler->save_clbl_get_case($_REQUEST["ap_procedure"], $_REQUEST["tempproc"]);
		
		if($clbl_case == "TEMP")
		{
			//------SETTING DROPPED ON PROCEDURE FOR THIS APPT------
			$_REQUEST["ap_procedure"] = "";
			
			if(count($arr_proc_acro) > 0)
			{
				foreach($arr_proc_acro as $loop_id => $loop_val)
				{
					if(trim($_REQUEST["tempproc"]) == trim($loop_val))
					{
						$_REQUEST["ap_procedure"] = $loop_id;
					}
				}
			}
		}

		$ap_procedure_match = (isset($arr_proc_acro[$_REQUEST["ap_procedure"]])) ? $arr_proc_acro[$_REQUEST["ap_procedure"]] : "";
		
		list($st_hr,$st_min,$st_sec)=explode(':',$_REQUEST["start_time"]);
		$_REQUEST["start_time"]=date("H:i:00", mktime($st_hr,$st_min,0));
		
		$sttm = strtotime($_REQUEST["start_time"]);
		$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $_REQUEST["doctor_id"]);
		$arr_appt_procedure = explode("~", $str_appt_procedure);
		
		list($time_hr, $tm_min, $tm_sec) = explode(":", $_REQUEST["start_time"]);
		
		if($arr_appt_procedure[1] != "")
		{
			$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1], 0));
		}
		else
		{
			$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10, 0));		
		}
		
		$edtm = strtotime($time_to);
		
		$arr_start_date = explode("-", $_REQUEST["start_date"]);
		$start_date = date("Y-m-d", mktime(0, 0, 0, $arr_start_date[0], $arr_start_date[1], $arr_start_date[2]));
		
		$arr_custom_lbl_id = array();
	
		switch($clbl_case)
		{
			case "TEMP_APPT":
				for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60))
				{
					$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
					
					$start_loop_time = date("H:i:00", $looptm);
					$end_loop_time = date("H:i:00", $edtm2);
	
					//echo $start_loop_time." - ".$end_loop_time."<br>";
					$arr_custom_lbl_id = $obj_scheduler->set_labels_on_template($obj_db, $arr_custom_lbl_id, $_REQUEST["doctor_id"], $_REQUEST["facility_id"], $start_date, $_REQUEST["start_time"], $_REQUEST["tempproc"], $_REQUEST["tmp_id"], $start_loop_time, $end_loop_time, $ap_procedure_match);
				}
				$int_custom_lbl_id = "'".implode("','", $arr_custom_lbl_id)."'";
				$str_custom_lbl_acronym = trim($_REQUEST["tempproc"]);		
				break;
			case "APPT":			
				for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60))
				{
					$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
					
					$start_loop_time = date("H:i:00", $looptm);
					$end_loop_time = date("H:i:00", $edtm2);
					
					//echo $start_loop_time." - ".$end_loop_time."<br>";
					$arr_custom_lbl_id = $obj_scheduler->set_labels_on_template($obj_db, $arr_custom_lbl_id, $_REQUEST["doctor_id"], $_REQUEST["facility_id"], $start_date, $_REQUEST["start_time"], $ap_procedure_match, $_REQUEST["tmp_id"], $start_loop_time, $end_loop_time, $ap_procedure_match);
				}
				$int_custom_lbl_id = "'".implode("','", $arr_custom_lbl_id)."'";
				$str_custom_lbl_acronym = $ap_procedure_match;
				break;
			case "TEMP":
				for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60))
				{
					$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
					
					$start_loop_time = date("H:i:00", $looptm);
					$end_loop_time = date("H:i:00", $edtm2);
					
					//echo $start_loop_time." - ".$end_loop_time."<br>";
					$arr_custom_lbl_id = $obj_scheduler->set_labels_on_template($obj_db, $arr_custom_lbl_id, $_REQUEST["doctor_id"], $_REQUEST["facility_id"], $start_date, $_REQUEST["start_time"], $_REQUEST["tempproc"], $_REQUEST["tmp_id"], $start_loop_time, $end_loop_time, $ap_procedure_match);
				}
				$int_custom_lbl_id = "'".implode("','", $arr_custom_lbl_id)."'";
				$str_custom_lbl_acronym = trim($_REQUEST["tempproc"]);
				break;
			default :
				$str_custom_lbl_acronym = $ap_procedure_match;
			break;	
		}
	}
	
	if(isset($_REQUEST["save_type"]) && !empty($_REQUEST["save_type"]) && isset($_REQUEST["pt_id"]) && !empty($_REQUEST["pt_id"]))
	{
		if($_REQUEST["save_type"] == "addnew")
		{
			$arr_start_date = explode("-", $_REQUEST["start_date"]);
			$start_date = date("Y-m-d", mktime(0, 0, 0, $arr_start_date[0], $arr_start_date[1], $arr_start_date[2]));
			
			$arr_ap["sa_app_start_date"] = $start_date;
			$arr_ap["sa_app_end_date"] = $start_date;
			$arr_ap["sa_app_starttime"] = $_REQUEST["start_time"];
			$arr_ap["sa_facility_id"] = $_REQUEST["facility_id"];
			$arr_ap["facility_type_provider"] = $_REQUEST["facility_type_provider"];
			$appt_doctor_id = $arr_ap["sa_doctor_id"] = $_REQUEST["doctor_id"];
			$arr_ap["sch_template_id"] = $_REQUEST["tmp_id"];
			$arr_ap["sa_patient_name"] = addslashes(core_name_format($_REQUEST["pt_lname"], $_REQUEST["pt_fname"], $_REQUEST["pt_mname"]));
			$arr_ap["EMR"] = $_REQUEST["pt_emr"];
			$arr_ap["iolink_sa_is_modify"] = 0;
			$arr_ap["sa_patient_app_status_id"] = 0;
	
			$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $appt_doctor_id);
			$arr_appt_procedure = explode("~", $str_appt_procedure);
			$proc_id = $arr_appt_procedure[0];
			
			list($time_hr, $tm_min, $tm_sec) = explode(":", $arr_ap["sa_app_starttime"]);
			
			if($arr_appt_procedure[1] != "")
			{
				$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1], 0));
			}
			else
			{
				$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10, 0));		
			}
			$app_duration = $obj_scheduler->get_duration($arr_ap["sa_app_starttime"], $time_to);
			$app_arrival_time = $obj_scheduler->get_arrival_time($arr_ap["sa_app_starttime"], $arr_appt_procedure[3]);
			
			$obj_scheduler->updatePatientStatus($pt_id, $obj_db);
		}
		
		$iolink_prev_dos_emr="";
		if($_REQUEST["save_type"] == "reschedule")
		{
			$arr_start_date = explode("-", $_REQUEST["start_date"]);
			$start_date = date("Y-m-d", mktime(0, 0, 0, $arr_start_date[0], $arr_start_date[1], $arr_start_date[2]));
			
			$arr_ap["sa_app_start_date"] = $start_date;
			$arr_ap["sa_app_end_date"] = $start_date;
			$arr_ap["sa_app_starttime"] = $_REQUEST["start_time"];
			$arr_ap["sa_facility_id"] = $_REQUEST["facility_id"];
			$appt_doctor_id = $arr_ap["sa_doctor_id"] = $_REQUEST["doctor_id"];
			$arr_ap["sch_template_id"] = $_REQUEST["tmp_id"];
			//$arr_ap["sa_patient_name"] = addslashes(core_name_format($_REQUEST["pt_lname"], $_REQUEST["pt_fname"], $_REQUEST["pt_mname"]));
			$arr_ap["facility_type_provider"] = $_REQUEST["facility_type_provider"];
			$arr_ap["EMR"] = $_REQUEST["pt_emr"];
			$arr_ap["iolink_sa_is_modify"] = 1;
			$arr_ap["sa_patient_app_status_id"] = 202;
			
			$arr_appt = $obj_scheduler->get_appointment_details("sa_doctor_id, sa_app_starttime, sa_app_start_date, iolink_iosync_waiting_id, procedure_site, sa.procedureid", $_REQUEST["pt_id"], $_REQUEST["ap_id"]);
			list($time_hr, $tm_min, $tm_sec) = explode(":", $arr_ap["sa_app_starttime"]);
			
			$iolink_prev_dos_emr = $arr_appt["sa_app_start_date"];
			$arr_ap["iolink_iosync_waiting_id"] = $arr_appt["iolink_iosync_waiting_id"];
			$arr_ap["procedure_site"] = $arr_appt["procedure_site"];
			
			$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $appt_doctor_id);
			$arr_appt_procedure = explode("~", $str_appt_procedure);
			$proc_id = $arr_appt_procedure[0];
			
			if($arr_appt_procedure[1] != "")
			{
				$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1], 0));
			}
			else
			{
				$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10, 0));		
			}
			
			$app_duration = $obj_scheduler->get_duration($arr_ap["sa_app_starttime"], $time_to);
			$app_arrival_time = $obj_scheduler->get_arrival_time($arr_ap["sa_app_starttime"], $arr_appt_procedure[3]);
			$obj_scheduler->updatePatientStatus($pt_id, $obj_db);		
		}
		
		if($_REQUEST["save_type"] == "save" || $_REQUEST["save_type"] == "addnew" || $_REQUEST["save_type"] == "reschedule")
		{
			if($_REQUEST["save_type"] == "save" && isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"]) && !empty($_REQUEST["pt_id"]))
			{
				$arr_appt = $obj_scheduler->get_appointment_details("sa_doctor_id, sa_app_starttime, sa_app_start_date, iolink_iosync_waiting_id", $_REQUEST["pt_id"], $_REQUEST["ap_id"]);		
				$appt_start_time = $arr_appt["sa_app_starttime"];
				
				$arr_ap["iolink_iosync_waiting_id"] = $arr_appt["iolink_iosync_waiting_id"];
				$arr_ap["sa_app_start_date"]		= $arr_appt["sa_app_start_date"];
				$arr_ap["sa_app_starttime"] 		= $arr_appt["sa_app_starttime"];
				
				$appt_doctor_id = $arr_appt["sa_doctor_id"];
				list($time_hr, $tm_min, $tm_sec) = explode(":", $arr_appt["sa_app_starttime"]);
				$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $appt_doctor_id);
				$arr_appt_procedure = explode("~", $str_appt_procedure);
				$proc_id = $arr_appt_procedure[0];				
				if($arr_appt_procedure[1] != "")
				{
					$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1],0));
				}
				else
				{
					$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10,0));		
				}
				$app_duration = $obj_scheduler->get_duration($appt_start_time, $time_to);
				$app_arrival_time = $obj_scheduler->get_arrival_time($arr_ap["sa_app_starttime"], $arr_appt_procedure[3]);
			}
	
			//------SETTING PATIENT SPECIFIC CHANGES------
			//------REF PHY SAVING IF NEW-----------------
			if(trim($_REQUEST["pt_ref_phy"]))
			{
				$intRefPhyId = 0;
				$strRefPhyName = "";
				if($_REQUEST['pt_ref_phy_id'] == '' && $_REQUEST['pt_ref_phy']!="")
				{	
					list($intRefPhyId, $strRefPhyName) = $OBJCommonFunction->chk_create_ref_phy($_REQUEST['pt_ref_phy'], 4);
				}
				else
				{
					$ref_phy_id = $_REQUEST['pt_ref_phy_id'];
					$ref_phy_name = $_REQUEST['pt_ref_phy'];
				}
				if((empty($intRefPhyId) == false) && (empty($strRefPhyName) == false))
				{
					$ref_phy_id = $intRefPhyId;
					$ref_phy_name = $strRefPhyName;
					$_REQUEST['pt_ref_phy_id'] = $intRefPhyId;
					$_REQUEST['pt_ref_phy'] = $strRefPhyName;
				}
			}
			else if($_REQUEST['pt_ref_phy_id'] >0 && $_REQUEST['pt_ref_phy'] == "")
			{
				$q = "	UPDATE 
							patient_multi_ref_phy 
						SET 
							status = '1'
						WHERE 
							patient_id = '".$_REQUEST["pt_id"]."' 
						AND 
							phy_type = 1
						AND 
							ref_phy_id = '".$_REQUEST['pt_ref_phy_id']."'
							";
				imw_query($q);
			}
			
			if(trim($_REQUEST['pt_pcp_phy']))
			{
				$intPCPId = 0;
				$strPCPName = "";
				if($_REQUEST['pt_pcp_phy_id'] == '' && $_REQUEST['pt_pcp_phy']!="")
				{	
					list($intPCPId, $strPCPName) = $OBJCommonFunction->chk_create_ref_phy($_REQUEST['pt_pcp_phy'], 4);
				}
				else
				{
					$pcp_phy_id = trim($_REQUEST["pt_pcp_phy_id"]);
					$pcp_phy_name = trim($_REQUEST["pt_pcp_phy"]);
				}
				if((empty($intPCPId) == false) && (empty($strPCPName) == false))
				{
					$_REQUEST['pt_pcp_phy_id'] = $intPCPId;
					$_REQUEST['pt_pcp_phy'] = $strPCPName;
					$pcp_phy_id = $intPCPId;
					$pcp_phy_name = $strPCPName;
				}
			}
			else if($_REQUEST['pt_pcp_phy_id'] >0 && $_REQUEST['pt_pcp_phy'] == "")
			{
				$q = "	UPDATE 
							patient_multi_ref_phy 
						SET 
							status = '1'
						WHERE 
							patient_id = '".$_REQUEST["pt_id"]."' 
						AND 
							phy_type = 4
						AND 
							ref_phy_id = '".$_REQUEST['pt_pcp_phy_id']."'
							";
				imw_query($q);
			}
			$_REQUEST["pt_status"] = $_REQUEST["pt_status"] != '' ? $_REQUEST["pt_status"]: 'Active';
			
			
			//------SETTING APPOINTMENT SPECIFIC CHANGES------
			$arr_ap["RoutineExam"] = $_REQUEST["ap_routine_exam"];
			$arr_ap["case_type_id"] = $_REQUEST["ap_ins_case_id"];
			$arr_ap["sa_comments"] = (trim($_REQUEST["ap_notes"]) != "Appointment Comment") ? core_refine_user_input(rawurldecode($_REQUEST["ap_notes"])) : "";
			$arr_ap["procedureid"] = $proc_id;
			
			if(!$arr_ap["procedure_site"])
			{
				if($_REQUEST["pri_eye_site"]!='undefined')
				$arr_ap["procedure_site"] = $_REQUEST["pri_eye_site"];
			}
			if(!$arr_ap["procedure_sec_site"])
			{
				if($_REQUEST["sec_eye_site"]!='undefined')
				$arr_ap["procedure_sec_site"] = $_REQUEST["sec_eye_site"];
			}
			if(!$arr_ap["procedure_ter_site"])
			{
				if($_REQUEST["ter_eye_site"]!='undefined')
				$arr_ap["procedure_ter_site"] = $_REQUEST["ter_eye_site"];
			}
			
			$arr_ap["facility_type_provider"] = $_REQUEST["facility_type_provider"];
			$arr_ap["sec_procedureid"] = $_REQUEST['sec_ap_procedure'];
			$arr_ap["tertiary_procedureid"] = $_REQUEST['ter_ap_procedure'];
			$arr_ap["pick_up_time"] = core_refine_user_input(urldecode($_REQUEST["ap_pickup_time"]));
			$arr_ap["arrival_time"] = core_refine_user_input(urldecode($app_arrival_time));
			$arr_ap["sa_app_endtime"] = $time_to;	
			$arr_ap["sa_app_duration"] = $app_duration;
			$arr_ap["sa_patient_id"] = $pt_id;
			$arr_ap["sa_madeby"] = $_SESSION["authUser"]; //username
			$arr_ap["status_update_operator_id"] = $_SESSION["authId"];
			$arr_ap["iolink_sa_is_modify"] = 1;
			
		}
		if(!$proc_id)continue;
		//------UPDATING APPT SPECIFIC DATA------
		$str_ap_qry = "";
		if(is_array($arr_ap) && count($arr_ap) > 0 && $arr_ap["sa_app_start_date"]>date('2015-01-01'))
		{	//------DATE CHECK INCLUDED TO STOP NULL DATE ENTRANCE
			foreach($arr_ap as $column => $value)
			{
				$str_ap_qry .= " ".$column." = '".$value."',";
			}
			$str_ap_qry = substr($str_ap_qry, 0, -1);
		}
		if($str_ap_qry != ""){
			
			$fa_qry = "	SELECT 
							id 
						FROM 
							`schedule_appointments` 
						WHERE 
							sa_patient_id = '".$_REQUEST["pt_id"]."' 
						AND 
							sa_patient_app_status_id = 271";
							
			$result_fa_obj = imw_query($fa_qry);
			if(imw_num_rows($result_fa_obj) > 0)
			{
				$fa_sch_data = imw_fetch_assoc($result_fa_obj);
				$_REQUEST["ap_id"] = $fa_sch_data["id"];
				$obj_scheduler->logApptChangedStatus($_REQUEST["ap_id"], $arr_ap["sa_app_start_date"], $arr_ap["sa_app_starttime"], $arr_ap["sa_app_endtime"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_doctor_id"], $arr_ap["sa_facility_id"], $arr_ap["sa_madeby"], $ap_act_reason, $arr_ap["procedureid"], true,$arr_ap["sec_procedureid"],$arr_ap["tertiary_procedureid"]);
			}
	
			if(isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"]))
			{
				$init = "UPDATE ";
				$where = " WHERE id = '".$_REQUEST["ap_id"]."'";
			}
			else
			{
				$init = "INSERT INTO ";
				$where = "";
			}
			
			//------LOG RESCHEDULER------
			if($_REQUEST["save_type"] == "reschedule"){
				//------REASON, IF ANY FOR ACTION------
				$ap_act_reason = core_refine_user_input(urldecode($_REQUEST["ap_act_reason"]));
				$obj_scheduler->logApptChangedStatus($_REQUEST["ap_id"], $arr_ap["sa_app_start_date"], $arr_ap["sa_app_starttime"], $arr_ap["sa_app_endtime"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_doctor_id"], $arr_ap["sa_facility_id"], $arr_ap["sa_madeby"], $ap_act_reason, $arr_ap["procedureid"], true,$arr_ap["sec_procedureid"],$arr_ap["tertiary_procedureid"]);
			}
			//------CHECK PATIENT APPOINTMENT ON SLOT - FOR FIX ISSUE DOUBLE CLICK ON SCHEDULER RAIL------
			$QRY_CHECK_SLOT_TIME="	SELECT 
										id 
									FROM 
										`schedule_appointments` 
									WHERE 
										sa_doctor_id='".$_REQUEST["doctor_id"]."' 
									AND 
										sa_patient_id='".$_REQUEST["pt_id"]."' 
									AND 
										sa_app_starttime='".$_REQUEST["start_time"]."' 
									AND 
										sa_facility_id='".$_REQUEST["facility_id"]."' 
									AND 
										sa_app_start_date='".$start_date."' 
									AND 
										procedureid='".$proc_id."' 
									AND 
										sa_patient_app_status_id NOT IN(201,18,19,20,203)";
										
			$RES_CHECK_SLOT_TIME=imw_query($QRY_CHECK_SLOT_TIME);
			//------------------------------------------------------------------------------------------------//
			$str_ap_qry = $init." schedule_appointments SET ".$str_ap_qry.", sa_patient_app_show=0, sa_app_time = '".date("Y-m-d H:i:s")."' ".$where;
			//echo $str_ap_qry;
			$remote_req = 0;
			if(isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"]) && trim($_REQUEST["ap_id"]) != "undefined")
			{
				imw_query($str_ap_qry);
				$intApptId = $_REQUEST["ap_id"];
				
				if($_REQUEST["save_type"] != "save")
				{
					$obj_scheduler->keep_track_of_replaced_labels($obj_db, $int_custom_lbl_id, $str_custom_lbl_acronym, $intApptId, $_REQUEST["tmp_id"]);
				}
				$remote_req = 1;
			}
			else if(isset($arr_ap["sa_doctor_id"]) && trim($arr_ap["sa_doctor_id"]) != "0" && trim($arr_ap["sa_doctor_id"]) != "" && trim($arr_ap["sa_doctor_id"]) != "undefined" && (imw_num_rows($RES_CHECK_SLOT_TIME)==0 || !imw_num_rows($RES_CHECK_SLOT_TIME)))
			{
				imw_query($str_ap_qry);
				$intApptId = imw_insert_id();			
				if($_REQUEST["save_type"] != "save")
				{
					
					$obj_scheduler->keep_track_of_replaced_labels($obj_db, $int_custom_lbl_id, $str_custom_lbl_acronym, $intApptId, $_REQUEST["tmp_id"]);
				}	
				//time locator
				$obj_scheduler->set_scroll_settings($intApptId, $_SESSION["authId"]);
				
				//log new appt
				if($_REQUEST["save_type"] == "addnew")
				{
					//reason, if any for action
					$ap_act_reason = "";
					$obj_scheduler->logApptChangedStatus($intApptId, $arr_ap["sa_app_start_date"], $arr_ap["sa_app_starttime"], $arr_ap["sa_app_endtime"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_doctor_id"], $arr_ap["sa_facility_id"], $arr_ap["sa_madeby"], $ap_act_reason, $arr_ap["procedureid"], false,$arr_ap["sec_procedureid"],$arr_ap["tertiary_procedureid"]);
				}
				$remote_req = 1;						
			}
			$obj_scheduler->patients_sync($intApptId);
			
		}
		if($_REQUEST["save_type"] == "save" || $_REQUEST["save_type"] == "reschedule")
		{		
			//$iolinkSite = (isset($_REQUEST["procedure_site"]) && !empty($_REQUEST["procedure_site"])) ? strtolower($_REQUEST["procedure_site"]) : "";
			$iolinkSite = (isset($arr_ap["procedure_site"]) && !empty($arr_ap["procedure_site"]) && empty($_REQUEST["pri_eye_site"])) ? strtolower($arr_ap["procedure_site"]) : $_REQUEST["pri_eye_site"];
			$iolinkSite = (strtolower($iolinkSite) == "bilateral") ? "both" : $iolinkSite;
			if(constant('DEFAULT_PRODUCT')=='imwemr' || constant("IDOC_IASC_SAME")=="YES")
			{
				$obj_scheduler->setApptInIolink($arr_ap["iolink_iosync_waiting_id"], $arr_ap["sa_app_start_date"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_app_starttime"], $arr_ap["sa_comments"], $arr_ap["pick_up_time"], $arr_ap["arrival_time"], $iolinkSite, $_REQUEST["ap_id"], $iolink_prev_dos_emr);
			}	
		}
		if($_REQUEST["save_type"] == "reschedule")
		{
			$init_st_time_rs=$_REQUEST['init_st_time_rs'];
			$init_et_time_rs=$_REQUEST['init_et_time_rs'];
			$init_acronym_rs=$_REQUEST['init_acronym_rs'];	
			$init_date_rs = $_REQUEST['init_date_rs'];
			
			$init_fac_id = trim($_REQUEST['init_fac_id']);
			$init_provider_id = trim($_REQUEST['init_provider_id']);
			
			$provider_id= $init_provider_id;
			$res_facility_id= $init_fac_id;
			
			$dsStartTime=$_REQUEST["start_time"];
			list($stHr,$stMn,$stSec)=explode(':',$dsStartTime);
			$stDurMin=ceil($app_duration/60);
			$dsEndTime=date("H:i:00", mktime($stHr, $stMn + $stDurMin, 0));
	
			$rs_qry ='SELECT 
						id, 
						provider, 
						facility, 
						start_date, 
						start_time, 
						end_time, 
						labels_replaced, 
						l_text, 
						l_show_text 
					FROM 
						`scheduler_custom_labels` 
					WHERE 
						(
							(start_time>="'.$init_st_time_rs.'" and end_time<="'.$init_et_time_rs.'") 
							OR 
							(
								("'.$init_st_time_rs.'" between start_time and end_time) 
							AND 
								("'.$init_et_time_rs.'" between start_time and end_time)
							)
						) 
					AND 
						start_date="'.$init_date_rs.'" 
					AND 
						provider="'.$provider_id.'" 
					AND 
						facility="'.$res_facility_id.'"';
						
			$appt_id = trim($_REQUEST["ap_id"]);		
			$rs_qry_result = imw_query($rs_qry);
			$lbl_appt_arr = array();
			if(imw_num_rows($rs_qry_result))
			{
				//$lbl_replaced_arr = $rs_qry_result->GetArray();
				
				while($lbl_appt_val=imw_fetch_assoc($rs_qry_result))
				{
					$lbl_record_id = trim($lbl_appt_val['id']);
					$lbl_replaced = trim($lbl_appt_val['labels_replaced']);
					
					#temp fix to retrive labels if it wasn't in label_replaced field
					if(!$lbl_replaced && !trim($lbl_appt_val["l_show_text"]))
					{ 
						$sct_id=$lbl_appt_val["id"];
						$provider=$lbl_appt_val["provider"];
						$facility=$lbl_appt_val["facility"];	
						$start_date=$lbl_appt_val["start_date"];
						$start_time=$lbl_appt_val["start_time"];
						$end_time=$lbl_appt_val["end_time"];
						$l_text=$lbl_appt_val["l_text"];
						
						$qry_appt="	SELECT 
										id,
										sch_template_id 
									FROM 
										`schedule_appointments` 
									WHERE 
										sa_doctor_id='".$provider."' 
									AND 
										sa_facility_id='".$facility."' 
									AND 
										sa_app_start_date='".$start_date."' 
									AND 
										sa_app_starttime='".$start_time."' 
									AND 
										sa_patient_app_status_id NOT IN (203,201,18,19,20) 
									AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 )
									";
						
						$res_appt=imw_query($qry_appt);
						if(imw_num_rows($res_appt)>0){
							$row_appt=imw_fetch_assoc($res_appt);	
							$sch_template_id=$row_appt["sch_template_id"];
							$HH=$MM=$HH1=$MM1="";
							list($HH,$MM)=explode(":",$start_time);
							list($HH1,$MM1)=explode(":",$end_time);
							$new_start_time=$HH.":".$MM;
							$new_end_time=$HH1.":".$MM1;
							$labels_replaced="";
							$label_name=$l_text;
							if(strstr($l_text,";")){
								list($label_name)=explode(";",$l_text);	
							}
							if($row_appt["id"] && trim($label_name)){
								$labels_replaced="::".$row_appt["id"].":".trim($label_name);
								if($row_appt["id"]==$appt_id){$lbl_replaced=$labels_replaced;}
							}
							$qry_update="UPDATE 
											scheduler_custom_labels 
										SET 
											labels_replaced='".$labels_replaced."' 
										WHERE 
											id='".$sct_id."'";
							$res_update=imw_query($qry_update);
						}else if(imw_num_rows($res_appt)==0){
							$qry_update="UPDATE 
											scheduler_custom_labels 
										SET 
											l_show_text=l_text WHERE id='".$sct_id."'";
							$res_update=imw_query($qry_update);
						}	
					}
					//------TEMP FIX ENDS HERE------
					$lbl_replace_act=false;
					if($lbl_replaced!="" && $lbl_record_id!="")
					{
						$lbl_replaced_array = array();
						$lbl_replaced_array = explode('::',$lbl_replaced);
						foreach($lbl_replaced_array as $lbl_replaced_entity)
						{
							$lbl_replaced_entity = trim($lbl_replaced_entity);
							if($lbl_replaced_entity!="")
							{
									list($lbl_replaced_appt_id,$get_lbl_replaced) = explode(':',$lbl_replaced_entity); 
									$get_lbl_replaced = trim($get_lbl_replaced);
									if($appt_id == $lbl_replaced_appt_id)
									{
										
										$target_replace = '::'.$appt_id.':'.$get_lbl_replaced;
										$rsc_update_qry = '	UPDATE 
																scheduler_custom_labels 
															SET 
																l_show_text = concat(TRIM(l_show_text),
																if(TRIM(l_show_text)="","'.$get_lbl_replaced.'","; '.$get_lbl_replaced.'")), 
																labels_replaced = replace(labels_replaced,"'.$target_replace.'","") 
															WHERE 
																id ='.$lbl_record_id;
										imw_query($rsc_update_qry);
										$lbl_replace_act=true;
										break;
										
									}							
																
							}
						}
						//------REVALIDATE IS WE HAVE REVERED LABEL OR NOT------
						if($lbl_replace_act==false)
						{
							//------VALIDATE ALL LABEL REPLACED RECORD----------
							$obj_scheduler->validate_label_replaced($lbl_appt_val);
						}						
					}					
				}
			}
		}		
		
        //------INSERT IN VERIFICATION TABLE------
        $obj_scheduler->save_verification_data($intApptId);
        	
        
        //if($_REQUEST["save_type"] == "addnew"){
            
			$params=array();
            $params['patientid']=$pt_id;
            //$params['operatorid']=$_SESSION["authId"];
            $params['operatorid']=$_REQUEST['doctor_id'];
            $params['section']='appointment';

            switch($_REQUEST["save_type"])
			{
                case'addnew':
                    $sub_section='appt_created';
                    break;
                default:
                    $sub_section='other_action';
                    break;
            }
            $params['sub_section']=$sub_section; //appt_canceled,appt_created,appt_deleted,appt_no_show,appt_reschedule
            $params['obj_value']=$intApptId;
            $serialized_arr = serialize($params);
            include_once("../../interface/common/assign_new_task.php");
        //}
    
		/* MVE PORTAL CREATE NEW APPOINTMENT STARTS HERE*/
		$erp_error=array();
		if(isERPPortalEnabled()) {
			try {
				include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
				$obj_appointments = new Appointments;
				$appt_act_reason = "";
				if(isset($_REQUEST["ap_act_reason"]) && $_REQUEST["ap_act_reason"]!=''){
					$appt_act_reason = core_refine_user_input(urldecode($_REQUEST["ap_act_reason"]));
				}
				$obj_appointments->addUpdateAppointments($intApptId,$pt_id,$appt_act_reason);
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}
		/* MVE PORTAL CREATE NEW APPOINTMENT ENDS HERE*/
            
		list($yr, $mn, $dt) = explode("-", $arr_ap["sa_app_start_date"]);
		echo $_REQUEST["save_type"];
		echo "~";
		echo date("l", mktime(0, 0, 0, $mn, $dt, $yr));
		echo "~";
		echo $arr_ap["sa_app_start_date"];
		
		/*********NEW HL7 ENGINE START************/
		require_once(dirname(__FILE__)."/../../hl7sys/api/class.HL7Engine.php");
		$objHL7Engine = new HL7Engine();
		$objHL7Engine->application_module = 'scheduler';
		$objHL7Engine->msgSubType = $arr_ap["sa_patient_app_status_id"];
		$objHL7Engine->source_id = $intApptId;
		$objHL7Engine->generateHL7();
		/*********NEW HL7 ENGINE END*************/
		
		/* Purpose: Generate hl7 message on saving and modification of appointment */
		$allowedEvents = $GLOBALS["HL7_SIU_EVENTS"];
	
		if(constant("HL7_SIU_GENERATION")==true && is_array($allowedEvents) && in_array($arr_ap["sa_patient_app_status_id"], $allowedEvents) && $intApptId != '' && $pt_id != '')
		{
			require_once( dirname(__FILE__).'/../../hl7sys/hl7GP/hl7FeedData.php');
			$hl7 = new hl7FeedData();

			$hl7->PD['id'] = $pt_id;
			$hl7->PD['schid'] = $intApptId;

			$hl7->setTrigger('SIU', $arr_ap["sa_patient_app_status_id"]);

			if( isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING']) )
			{
				$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
				$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
				$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
			}

			$hl7->addEVN($hl7->msgtypes['SIU']['trigger_event']);

			if( isset($GLOBALS['HL7_SIU_SEGMENTS']) && is_array($GLOBALS['HL7_SIU_SEGMENTS']) )
			{
				foreach( $GLOBALS['HL7_SIU_SEGMENTS'] as $segment )
				{
					$hl7->insertSegment($segment, 'SIU');
				}
			}

			$hl7->log_message();
		}
		/* End code*/
	}	
	
	//unset all created object here
	unset($hl7,$appt_sync_obj);
	}
}
else
{
//------GETTING ALL PROC ACRONYMS------
$arr_proc_acro = $arr_proc_label = array();
$q ="SELECT 
		id, 
		proc, 
		acronym, 
		labels 
	FROM 
		`slot_procedures` 
	WHERE 
		times = '' 
	AND 
		proc != '' 
	AND 
		doctor_id = 0 
	AND 
		active_status!='del' 
	ORDER BY proc";
$r = imw_query($q);
if(imw_num_rows($r) > 0)
{
	while($tmp_data=imw_fetch_assoc($r))
	{
		$a[]=$tmp_data;	
	}
	for($l = 0; $l < count($a); $l++)
	{
		$arr_proc_acro[$a[$l]["id"]] = $a[$l]["acronym"];
		$arr_proc_label[$a[$l]["id"]] = $a[$l]["labels"];
	}
}

$int_custom_lbl_id = 0;
$str_custom_lbl_acronym = "";
$arr_st_date = explode("-", $_REQUEST["start_date"]);
$st_date = date("Y-m-d", mktime(0, 0, 0, $arr_st_date[0], $arr_st_date[1], $arr_st_date[2]));

if(isset($_REQUEST["save_type"]) && $_REQUEST["save_type"]=="save" && isset($_REQUEST["pt_id"]) && !empty($_REQUEST["pt_id"]) && strtolower($GLOBALS["LOCAL_SERVER"])=='uram')
{
	
	$pid = $_REQUEST["pt_id"];
	if(constant('HL7_ADT_GENERATION')==true){
		require_once( dirname(__FILE__).'/../../hl7sys/old/CLS_makeHL7.php');
		$makeHL7		= new makeHL7;	
		
		//logging HL7 messages to send to IDX & Forum.
		if($makeHL7){$makeHL7->log_HL7_message($pid,'Update_Patient');}
	}
}

if(isset($_REQUEST["save_type"]) && $_REQUEST["save_type"] != "save")
{
	$clbl_case = $obj_scheduler->save_clbl_get_case($_REQUEST["ap_procedure"], $_REQUEST["tempproc"]);
	if($clbl_case == "TEMP"){
		//setting dropped on procedure for this appt
		$_REQUEST["ap_procedure"] = "";
		$acronym=$label=false;
		if(count($arr_proc_acro) > 0){
				foreach($arr_proc_acro as $loop_id => $loop_val){
					if(trim($_REQUEST["tempproc"]) == trim($loop_val)){
						$_REQUEST["ap_procedure"] = $loop_id;
						$acronym=true;
					}
				}
			
		}
	}
	
	$ap_procedure_match = (isset($arr_proc_acro[$_REQUEST["ap_procedure"]])) ? $arr_proc_acro[$_REQUEST["ap_procedure"]] : "";
	$ap_label_match = (isset($arr_proc_label[$_REQUEST["ap_procedure"]])) ? $arr_proc_label[$_REQUEST["ap_procedure"]] : "";
	
	list($st_hr,$st_min,$st_sec)=explode(':',$_REQUEST["start_time"]);
	$_REQUEST["start_time"]=date("H:i:00", mktime($st_hr,$st_min,0));
	
	$sttm = strtotime($_REQUEST["start_time"]);
	$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $_REQUEST["doctor_id"]);
	$arr_appt_procedure = explode("~", $str_appt_procedure);
	list($time_hr, $tm_min, $tm_sec) = explode(":", $_REQUEST["start_time"]);
	if($arr_appt_procedure[1] != "")
	{
		$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1], 0));
	}
	else
	{
		$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10, 0));		
	}
	$edtm = strtotime($time_to);
	
	$arr_start_date = explode("-", $_REQUEST["start_date"]);
	$start_date = date("Y-m-d", mktime(0, 0, 0, $arr_start_date[0], $arr_start_date[1], $arr_start_date[2]));
	$arr_custom_lbl_id = array();

	switch($clbl_case)
	{
		case "TEMP_APPT":
			for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
				$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
				
				$start_loop_time = date("H:i:00", $looptm);
				$end_loop_time = date("H:i:00", $edtm2);
				
				//echo $start_loop_time." - ".$end_loop_time."<br>";
				$arr_custom_lbl_id = $obj_scheduler->set_labels_on_template($obj_db, $arr_custom_lbl_id, $_REQUEST["doctor_id"], $_REQUEST["facility_id"], $start_date, $_REQUEST["start_time"], $_REQUEST["tempproc"], $_REQUEST["tmp_id"], $start_loop_time, $end_loop_time, $ap_procedure_match);
			}
			$int_custom_lbl_id = "'".implode("','", $arr_custom_lbl_id)."'";
			if($_REQUEST['matched_lbl']){$str_custom_lbl_acronym = $_REQUEST['matched_lbl'];}
			else{$str_custom_lbl_acronym = trim($_REQUEST["tempproc"]);}		
			
			break;
		case "APPT":			
			for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
				$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
				
				$start_loop_time = date("H:i:00", $looptm);
				$end_loop_time = date("H:i:00", $edtm2);
				
				//echo $start_loop_time." - ".$end_loop_time."<br>";
				$arr_custom_lbl_id = $obj_scheduler->set_labels_on_template($obj_db, $arr_custom_lbl_id, $_REQUEST["doctor_id"], $_REQUEST["facility_id"], $start_date, $_REQUEST["start_time"], $ap_procedure_match, $_REQUEST["tmp_id"], $start_loop_time, $end_loop_time, $ap_procedure_match, $ap_label_match);
			}
			$int_custom_lbl_id = "'".implode("','", $arr_custom_lbl_id)."'";
			if($_REQUEST['matched_lbl']){$str_custom_lbl_acronym = $_REQUEST['matched_lbl'];}
			else{$str_custom_lbl_acronym = $ap_procedure_match;}
			break;
		case "TEMP":
			for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
				$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);
				
				$start_loop_time = date("H:i:00", $looptm);
				$end_loop_time = date("H:i:00", $edtm2);
				
				//echo $start_loop_time." - ".$end_loop_time."<br>";
				$arr_custom_lbl_id = $obj_scheduler->set_labels_on_template($obj_db, $arr_custom_lbl_id, $_REQUEST["doctor_id"], $_REQUEST["facility_id"], $start_date, $_REQUEST["start_time"], $_REQUEST["tempproc"], $_REQUEST["tmp_id"], $start_loop_time, $end_loop_time, $ap_procedure_match);
			}
			$int_custom_lbl_id = "'".implode("','", $arr_custom_lbl_id)."'";
			if($_REQUEST['matched_lbl']){$str_custom_lbl_acronym = $_REQUEST['matched_lbl'];}
			else{$str_custom_lbl_acronym = trim($_REQUEST["tempproc"]);}
			break;
		default :
			$str_custom_lbl_acronym = $ap_procedure_match;
		break;	
	}
}
unset($_REQUEST["matched_lbl"]);
if(isset($_REQUEST["save_type"]) && !empty($_REQUEST["save_type"]) && isset($_REQUEST["pt_id"]) && !empty($_REQUEST["pt_id"]))
{

	$status_updated = false;
	$apptCancelOnDeceased = 0;
	if ((isset($_REQUEST["pt_status"]) && $_REQUEST["pt_status"] != '') )
	{
		$pt_sql="SELECT 
					id,
					patientStatus as ptsts 
				FROM 
					patient_data 
				WHERE 
					id='".$_REQUEST["pt_id"]."'
				";
		$pt_rs = imw_query($pt_sql);
		if ($pt_rs && imw_num_rows($pt_rs) == 1)
		{
			$row = imw_fetch_assoc($pt_rs);
			
			if (trim($row['ptsts']) != trim($_REQUEST["pt_status"]))
			{
				$status_updated = true;
			}
		}
		//Cancel all future appointments if patient status changed to deceased
		if( $status_updated && $_REQUEST["pt_status"] === 'Deceased' ) {
			$apptCancelOnDeceased = cancel_future_appointments($pid);
		}

	}

	if($_REQUEST["save_type"] == "addnew" && $_REQUEST["ap_procedure"])
	{
		$arr_start_date = explode("-", $_REQUEST["start_date"]);
		$start_date = date("Y-m-d", mktime(0, 0, 0, $arr_start_date[0], $arr_start_date[1], $arr_start_date[2]));
		
		$arr_ap["sa_app_start_date"] = $start_date;
		$arr_ap["sa_app_end_date"] = $start_date;
		$arr_ap["sa_app_starttime"] = $_REQUEST["start_time"];
		$arr_ap["sa_facility_id"] = $_REQUEST["facility_id"];
		$appt_doctor_id = $arr_ap["sa_doctor_id"] = $_REQUEST["doctor_id"];
		$arr_ap["sch_template_id"] = $_REQUEST["tmp_id"];
		$arr_ap["sa_patient_name"] = addslashes(core_name_format($_REQUEST["pt_lname"], $_REQUEST["pt_fname"], $_REQUEST["pt_mname"]));
		$arr_ap["EMR"] = $_REQUEST["pt_emr"];
		$arr_ap["iolink_sa_is_modify"] = 0;
		$arr_ap["sa_patient_app_status_id"] = 0;
		$arr_ap["facility_type_provider"] = $_REQUEST["facility_type_provider"];
		$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $appt_doctor_id);
		$arr_appt_procedure = explode("~", $str_appt_procedure);
		$proc_id = $arr_appt_procedure[0];
		list($time_hr, $tm_min, $tm_sec) = explode(":", $arr_ap["sa_app_starttime"]);
		
		if($arr_appt_procedure[1] != "")
		{
			$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1], 0));
		}
		else
		{
			$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10, 0));		
		}
		$app_duration = $obj_scheduler->get_duration($arr_ap["sa_app_starttime"], $time_to);
		$app_arrival_time = $obj_scheduler->get_arrival_time($arr_ap["sa_app_starttime"], $arr_appt_procedure[3]);
		
		$obj_scheduler->updatePatientStatus($pt_id, $obj_db);
	}
	$iolink_prev_dos_emr="";
	if($_REQUEST["save_type"] == "reschedule" && $_REQUEST["ap_procedure"])
	{
		$arr_start_date = explode("-", $_REQUEST["start_date"]);
		$start_date = date("Y-m-d", mktime(0, 0, 0, $arr_start_date[0], $arr_start_date[1], $arr_start_date[2]));
		
		$arr_ap["sa_app_start_date"] = $start_date;
		$arr_ap["sa_app_end_date"] = $start_date;
		$arr_ap["sa_app_starttime"] = $_REQUEST["start_time"];
		$arr_ap["sa_facility_id"] = $_REQUEST["facility_id"];
		$appt_doctor_id = $arr_ap["sa_doctor_id"] = $_REQUEST["doctor_id"];
		$arr_ap["sch_template_id"] = $_REQUEST["tmp_id"];
		//$arr_ap["sa_patient_name"] = addslashes(core_name_format($_REQUEST["pt_lname"], $_REQUEST["pt_fname"], $_REQUEST["pt_mname"]));
		$arr_ap["EMR"] = $_REQUEST["pt_emr"];
		$arr_ap["iolink_sa_is_modify"] = 1;
		$arr_ap["sa_patient_app_status_id"] = 202;
		$arr_ap["facility_type_provider"] = $_REQUEST["facility_type_provider"];
		
		$arr_appt = $obj_scheduler->get_appointment_details("sa_doctor_id, sa_app_starttime, sa_app_start_date, iolink_iosync_waiting_id, procedure_site", $_REQUEST["pt_id"], $_REQUEST["ap_id"]);
		list($time_hr, $tm_min, $tm_sec) = explode(":", $arr_ap["sa_app_starttime"]);
		$iolink_prev_dos_emr = $arr_appt["sa_app_start_date"];
		
		$arr_ap["iolink_iosync_waiting_id"] = $arr_appt["iolink_iosync_waiting_id"];
		$arr_ap["procedure_site"] = $arr_appt["procedure_site"];
		
		$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $appt_doctor_id);
		$arr_appt_procedure = explode("~", $str_appt_procedure);
		$proc_id = $arr_appt_procedure[0];
		
		if($arr_appt_procedure[1] != "")
		{
			$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1], 0));
		}
		else
		{
			$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10, 0));		
		}
		
		$app_duration = $obj_scheduler->get_duration($arr_ap["sa_app_starttime"], $time_to);
		$app_arrival_time = $obj_scheduler->get_arrival_time($arr_ap["sa_app_starttime"], $arr_appt_procedure[3]);
		
		$obj_scheduler->updatePatientStatus($pt_id, $obj_db);		
	}
	if($_REQUEST["save_type"] == "save" || $_REQUEST["save_type"] == "addnew" || $_REQUEST["save_type"] == "reschedule")
	{
		if($_REQUEST["save_type"] == "save" && isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"]) && !empty($_REQUEST["pt_id"]) && $_REQUEST["ap_procedure"])
		{			
			$arr_appt = $obj_scheduler->get_appointment_details("sa_doctor_id, sa_app_starttime, sa_app_start_date, iolink_iosync_waiting_id, sa.procedureid", $_REQUEST["pt_id"], $_REQUEST["ap_id"]);		
			$appt_start_time = $arr_appt["sa_app_starttime"];
			
			$arr_ap["iolink_iosync_waiting_id"] = $arr_appt["iolink_iosync_waiting_id"];
			$arr_ap["sa_app_start_date"]		= $arr_appt["sa_app_start_date"];
			$arr_ap["sa_app_starttime"] 		= $arr_appt["sa_app_starttime"];
			
			$appt_doctor_id = $arr_appt["sa_doctor_id"];
			list($time_hr, $tm_min, $tm_sec) = explode(":", $arr_appt["sa_app_starttime"]);
			$str_appt_procedure = $obj_scheduler->default_proc_to_doctor_proc($_REQUEST["ap_procedure"], $appt_doctor_id);
			$arr_appt_procedure = explode("~", $str_appt_procedure);
			$proc_id = $arr_appt_procedure[0];				
			
			if($arr_appt_procedure[1] != "")
			{
				$time_to = date("H:i:00", mktime($time_hr, $tm_min + $arr_appt_procedure[1],0));
			}
			else
			{
				$time_to = date("H:i:00", mktime($time_hr, $tm_min + 10,0));		
			}
			//this code will ignore previousely set manual end time(if any) and update it as per new procedure assigned
			if($proc_id==$arr_appt["procedureid"])
			{
				if(trim($_REQUEST['appt_duration'])>0){$time_to = date("H:i:00", mktime($time_hr, $tm_min + $_REQUEST['appt_duration'],0));}
			}
			$app_duration = $obj_scheduler->get_duration($appt_start_time, $time_to);
			$app_arrival_time = $obj_scheduler->get_arrival_time($arr_ap["sa_app_starttime"], $arr_appt_procedure[3]);
		}

		//setting patient specific changes		
		//--  Ref phy saving if new  --
		if(trim($_REQUEST["pt_ref_phy"]))
		{
			$intRefPhyId = 0;
			$strRefPhyName = "";
			if($_REQUEST['pt_ref_phy_id'] == '' && $_REQUEST['pt_ref_phy']!="")
			{	
				list($intRefPhyId, $strRefPhyName) = $OBJCommonFunction->chk_create_ref_phy($_REQUEST['pt_ref_phy'], 4);
			}
			else
			{
				$ref_phy_id = $_REQUEST['pt_ref_phy_id'];
				$ref_phy_name = $_REQUEST['pt_ref_phy'];
			}
			if((empty($intRefPhyId) == false) && (empty($strRefPhyName) == false))
			{
				$ref_phy_id = $intRefPhyId;
				$ref_phy_name = $strRefPhyName;
				$_REQUEST['pt_ref_phy_id'] = $intRefPhyId;
				$_REQUEST['pt_ref_phy'] = $strRefPhyName;
			}
		}
		else if($_REQUEST['pt_ref_phy_id'] >0 && $_REQUEST['pt_ref_phy'] == "")
		{
			$q = 	"UPDATE 
						patient_multi_ref_phy 
					SET 
						status = '1'
					WHERE 
						patient_id = '".$_REQUEST["pt_id"]."' 
					AND 
						phy_type = 1
					AND 
						ref_phy_id = '".$_REQUEST['pt_ref_phy_id']."'
					";
			imw_query($q);
		}
		if(trim($_REQUEST['pt_pcp_phy']))
		{
			$intPCPId = 0;
			$strPCPName = "";
			//if(trim($_REQUEST["hidd_pcp"])!=trim($_REQUEST["pt_pcp_phy"])){
			if($_REQUEST['pt_pcp_phy_id'] == '' && $_REQUEST['pt_pcp_phy']!="")
			{	
				list($intPCPId, $strPCPName) = $OBJCommonFunction->chk_create_ref_phy($_REQUEST['pt_pcp_phy'], 4);
			}
			else
			{
				$pcp_phy_id = trim($_REQUEST["pt_pcp_phy_id"]);
				$pcp_phy_name = trim($_REQUEST["pt_pcp_phy"]);
			}
			if((empty($intPCPId) == false) && (empty($strPCPName) == false))
			{
				$_REQUEST['pt_pcp_phy_id'] = $intPCPId;
				$_REQUEST['pt_pcp_phy'] = $strPCPName;
				$pcp_phy_id = $intPCPId;
				$pcp_phy_name = $strPCPName;
			}
		}
		else if($_REQUEST['pt_pcp_phy_id'] >0 && $_REQUEST['pt_pcp_phy'] == "")
		{
			$q="UPDATE 
					patient_multi_ref_phy 
				SET 
					status = '1'
				WHERE 
					patient_id = '".$_REQUEST["pt_id"]."' 
				AND 
					phy_type = 4
				AND 
					ref_phy_id = '".$_REQUEST['pt_pcp_phy_id']."'
						";
			imw_query($q);
		}
		
		$_REQUEST["pt_status"] = $_REQUEST["pt_status"] != '' ? $_REQUEST["pt_status"]: 'Active';
		$_REQUEST["pt_dod_patient"] = ($_REQUEST["pt_dod_patient"] != '00-00-0000' && $_REQUEST["pt_dod_patient"] != '') ? getDateFormatDB($_REQUEST["pt_dod_patient"]): '';
		$_REQUEST['pt_photo_ref']=($_REQUEST['pt_photo_ref']==1)?$_REQUEST['pt_photo_ref']:0;

		$arr_pt = array(
			"street"				=>	core_refine_user_input(urldecode($_REQUEST["pt_street1"])),
			"street2"				=>	core_refine_user_input(urldecode($_REQUEST["pt_street2"])),
			"city"					=>	core_refine_user_input(urldecode($_REQUEST["pt_city"])),
			"state"					=>	core_refine_user_input(urldecode($_REQUEST["pt_state"])),
			"postal_code"			=>	core_refine_user_input(urldecode($_REQUEST["pt_zip"])),
			"zip_ext"				=>	core_refine_user_input(urldecode($_REQUEST["pt_zip_ext"])),

			"email"					=>	core_refine_user_input(urldecode($_REQUEST["pt_email"])),
			"photo_ref"				=>	$_REQUEST['pt_photo_ref'],

			"phone_home"			=>	core_refine_user_input(urldecode($_REQUEST["pt_home_ph"])),
			"phone_biz"				=>	core_refine_user_input(urldecode($_REQUEST["pt_work_ph"])),
			"phone_cell"			=>	core_refine_user_input(urldecode($_REQUEST["pt_cell_ph"])),
			
			"patientStatus"			=>	$_REQUEST["pt_status"],
			"dod_patient"			=>	$_REQUEST["pt_dod_patient"],
			"otherPatientStatus"	=>	core_refine_user_input(urldecode($_REQUEST["pt_other_status"])),

			"primary_care"			=>	core_refine_user_input($ref_phy_name),				//ref phy
			"primary_care_id"		=>  $ref_phy_id,
			"primary_care_phy_name"	=>	core_refine_user_input($pcp_phy_name),				//pcp
			"primary_care_phy_id"	=>	$pcp_phy_id,

			"providerID"			=>	$_REQUEST["pt_doctor_id"]										//primary physician
		);
		if($arr_pt["street"] == "Street 1"){ $arr_pt["street"] = ""; }
		if($arr_pt["street2"] == "Street 2"){ $arr_pt["street2"] = ""; }
		if($arr_pt["postal_code"] == "Zip Code"){ $arr_pt["postal_code"] = ""; }
		if($arr_pt["zip_ext"] == "Ext"){ $arr_pt["zip_ext"] = ""; }
		if($arr_pt["state"] == "State"){ $arr_pt["state"] = "";	}
		if($arr_pt["city"] == "City"){ $arr_pt["city"] = ""; }
		if($arr_pt["phone_home"] == "Home Phone"){ $arr_pt["phone_home"] = ""; }
		if($arr_pt["phone_biz"] == "Work Phone"){ $arr_pt["phone_biz"] = ""; }
		if($arr_pt["phone_cell"] == "Cell Phone"){ $arr_pt["phone_cell"] = ""; }
		
		$old_street=$_REQUEST['hidd_prev_pt_street1'];
		$old_street2=$_REQUEST['hidd_prev_pt_street2'];
		$old_zip=$_REQUEST['hidd_prev_pt_zip'];
		$old_zip_ext=$_REQUEST['hidd_prev_pt_zip_ext'];
		$old_city=$_REQUEST['hidd_prev_pt_city'];
		$old_state=$_REQUEST['hidd_prev_pt_state'];
		$old_phone_home=core_phone_unformat($_REQUEST['hidd_prev_pt_home_ph']);
		$old_phone_biz=core_phone_unformat($_REQUEST['hidd_prev_pt_work_ph']);
		$old_phone_cell=core_phone_unformat($_REQUEST['hidd_prev_pt_cell_ph']);
		if($old_phone_home == "Home Phone"){ $old_phone_home = ""; }
		if($old_phone_biz == "Work Phone"){ $old_phone_biz = ""; }
		if($old_phone_cell == "Cell Phone"){ $old_phone_cell = ""; }
		$old_email=$_REQUEST['hidd_prev_pt_email'];
		
		if ((trim($old_street) != "" && (trim($old_street) != trim($arr_pt['street']))) || (trim($old_street2) != "" && (trim($old_street2) != trim($arr_pt['street2']))) || (trim($old_zip) != "" && (trim($old_zip) != trim($arr_pt['postal_code']))) || (trim($old_city) != "" && (trim($old_city) != trim($arr_pt['city']))) || (trim($old_state) != "" && (trim($old_state) != trim($arr_pt['state'])))) {
        //SAVE PATIENT-ADDRESS
        $demog_log_q1 = "INSERT INTO patient_previous_data SET
						patient_id = '" . $_REQUEST["pt_id"] . "', save_date_time = '" . date('Y-m-d H:i:s') . "',
						operator_id = '" . $_SESSION['authId'] . "',
						patient_section_name = 'patientAddress',
						new_street = '" . addslashes(convertUcfirst($arr_pt["street"])) . "',
						new_street2 = '" . addslashes(convertUcfirst($arr_pt["street2"])) . "',
						new_postal_code = '" . $arr_pt["postal_code"] . "',
						new_city = '" . addslashes(trim(convertUcfirst($arr_pt["city"]))) . "',
						new_state = '" . addslashes(trim(ucwords($arr_pt["state"]))) . "',
						prev_street = '" . addslashes(convertUcfirst($old_street)) . "', 
						prev_street2 = '" . addslashes(convertUcfirst($old_street2)) . "',
						prev_postal_code = '" . $old_zip . "',
						prev_city = '" . addslashes(trim(convertUcfirst($old_city))) . "',
						prev_state = '" . addslashes(trim(ucwords($old_state))) . "';";
    }

		$phone_home = core_phone_unformat($arr_pt['phone_home']);
		$phone_biz = core_phone_unformat($arr_pt['phone_biz']);
		$phone_cell = core_phone_unformat($arr_pt['phone_cell']);
		$ptDemoEmail=$_REQUEST["pt_email"];
		if ((trim($old_phone_home) != "" && (trim($old_phone_home) != trim($phone_home))) || 
			(trim($old_phone_biz) != "" && (trim($old_phone_biz) != trim($phone_biz))) || 
			(trim($old_phone_cell) != "" && (trim($old_phone_cell) != trim($phone_cell))) || 
			(trim($old_email) != "" && (trim($old_email) != trim($ptDemoEmail)))) 
		{
			//SAVE PATIENT-CONTACT
			$demog_log_q2= "INSERT INTO patient_previous_data SET
							patient_id = '$_REQUEST[pt_id]', save_date_time = '" . date('Y-m-d H:i:s') . "',
							operator_id = '" . $_SESSION['authId'] . "',";
			if (trim($old_phone_home) != "" && (trim($old_phone_home) != trim($phone_home))) {
				$demog_log_q2 .= "prev_phone_home = '" . core_phone_unformat($old_phone_home) . "',
								new_phone_home = '" . core_phone_unformat($phone_home) . "',";
			}

			if (trim($old_phone_biz) != "" && (trim($old_phone_biz) != trim($phone_biz))) {
				$demog_log_q2 .= " prev_phone_biz = '" . core_phone_unformat($old_phone_biz) . "',
								new_phone_biz = '" . core_phone_unformat($phone_biz) . "',";
			}

			if (trim($old_phone_cell) != "" && (trim($old_phone_cell) != trim($phone_cell))) {
				$demog_log_q2 .= "prev_phone_cell 	= '" . core_phone_unformat($old_phone_cell) . "',
								new_phone_cell 		= '" . core_phone_unformat($phone_cell) . "',";
			}

			if (trim($old_email) != "" && (trim($old_email) != trim($ptDemoEmail))) {
				$demog_log_q2 .= " prev_email = '" . addslashes(trim($old_email)) . "',
								   new_email  = '" . addslashes($ptDemoEmail) . "',";
			}
			$demog_log_q2 .= "patient_section_name= 'patientContact';";
		}
		//END INSERT CHANGED ENTRY IN patient_previous_data	TABLE

		//setting appointment specific changes
		$arr_ap["RoutineExam"] = $_REQUEST["ap_routine_exam"];
		$arr_ap["case_type_id"] = $_REQUEST["ap_ins_case_id"];
		$arr_ap["sa_comments"] = (trim($_REQUEST["ap_notes"]) != "Appointment Comment") ? core_refine_user_input(rawurldecode($_REQUEST["ap_notes"])) : "";
		$arr_ap["procedureid"] = $proc_id;
		if(!$arr_ap["procedure_site"])
		{
			if($_REQUEST["pri_eye_site"]!='undefined')
			$arr_ap["procedure_site"] = $_REQUEST["pri_eye_site"];
		}
		if(!$arr_ap["procedure_sec_site"])
		{
			if($_REQUEST["sec_eye_site"]!='undefined')
			$arr_ap["procedure_sec_site"] = $_REQUEST["sec_eye_site"];
		}
		if(!$arr_ap["procedure_ter_site"])
		{
			if($_REQUEST["ter_eye_site"]!='undefined')
			$arr_ap["procedure_ter_site"] = $_REQUEST["ter_eye_site"];
		}
		
		$arr_ap["facility_type_provider"] = $_REQUEST["facility_type_provider"];
		$arr_ap["sec_procedureid"] = $_REQUEST['sec_ap_procedure'];
		$arr_ap["tertiary_procedureid"] = $_REQUEST['ter_ap_procedure'];
		$arr_ap["pick_up_time"] = core_refine_user_input(urldecode($_REQUEST["ap_pickup_time"]));
		$arr_ap["arrival_time"] = core_refine_user_input(urldecode($app_arrival_time));
		$arr_ap["sa_app_endtime"] = $time_to;	
		$arr_ap["sa_app_duration"] = $app_duration;
		$arr_ap["sa_patient_id"] = $pt_id;
		$arr_ap["sa_madeby"] = $_SESSION["authUser"]; //username
		$arr_ap["status_update_operator_id"] = $_SESSION["authId"];
		$arr_ap["iolink_sa_is_modify"] = 1;
		$arr_ap["facility_type_provider"] = $_REQUEST['facility_type_provider'];
		$arr_ap['sa_ref_management']=($_REQUEST['pt_referral']==1)?$_REQUEST['pt_referral']:0;
		$sa_verification_req=($_REQUEST['pt_verification']==1)?$_REQUEST['pt_verification']:0;
	}

	//updating pt specific data
	$str_pt_qry = "";
	if(is_array($arr_pt) && count($arr_pt) > 0)
	{
		foreach($arr_pt as $column => $value)
		{
			$str_pt_qry .= " ".$column." = '".$value."',";
		}
		$str_pt_qry = substr($str_pt_qry, 0, -1);
	}
	if($str_pt_qry != "")
	{
		$str_pt_qry="UPDATE 
						patient_data 
					SET 
						".$str_pt_qry." 
					WHERE 
						id = '".$_REQUEST["pt_id"]."'";
		imw_query($str_pt_qry);
		if($demog_log_q1)imw_query($demog_log_q1)or die(imw_error());
		if($demog_log_q2)imw_query($demog_log_q2)or die(imw_error());
		//------BEGIN ADD/UPDATE REFERRING PHYSICIAN AND PCP IN PATIENT_MULTI_REF_PHY ------//	
		if($ref_phy_id != "")
		{
			$qry_sel_multi_phy ="SELECT 
									id 
								FROM 
									patient_multi_ref_phy 
								WHERE 
									patient_id = '".$_REQUEST["pt_id"]."' 
								AND 
									phy_type=1 
								AND 
									status=0 
								ORDER BY 
									id ASC LIMIT 0,1";
			$res_multi_phy = imw_query($qry_sel_multi_phy);
			if(imw_num_rows($res_multi_phy)>0)
			{
					$row_multi_phy = imw_fetch_assoc($res_multi_phy);
					
					$qry_update_multi_phy ="UPDATE 
												patient_multi_ref_phy 
											SET 
												ref_phy_id='".$ref_phy_id."',
												ref_phy_name='".core_refine_user_input($ref_phy_name)."',
												modified_by = '".$_SESSION['authId']."', 
												modified_by_date_time = '".date('Y-m-d H:i:s')."' 
											WHERE 
												id = '".$row_multi_phy["id"]."'";
					imw_query($qry_update_multi_phy);
			}
			else
			{
				$qry_update_multi_phy = "INSERT INTO 
											patient_multi_ref_phy 
										SET 
											ref_phy_id='".$ref_phy_id."',
											ref_phy_name='".core_refine_user_input($ref_phy_name)."',
											phy_type='1',
											patient_id = '".$_REQUEST["pt_id"]."',
											created_by='".$_SESSION['authId']."',
											created_by_date_time='".date('Y-m-d H:i:s')."'";
				imw_query($qry_update_multi_phy);
			}
		}
		if($pcp_phy_id != ""){
			$qry_sel_multi_pcp ="SELECT 
									id,
									phy_type 
								FROM 
									patient_multi_ref_phy 
								WHERE 
									patient_id = '".$_REQUEST["pt_id"]."' 
								AND 
									(phy_type=3 or phy_type=4 )
								AND 
									status=0 
								ORDER BY id ASC LIMIT 0,1";
			$res_multi_pcp = imw_query($qry_sel_multi_pcp);
			if(imw_num_rows($res_multi_pcp)>0)
			{
					$row_multi_pcp = imw_fetch_assoc($res_multi_pcp);
					$qry_update_multi_pcp ="UPDATE 
												patient_multi_ref_phy 
											SET 
												ref_phy_id='".$pcp_phy_id."',
												ref_phy_name='".core_refine_user_input($pcp_phy_name)."',
												modified_by = '".$_SESSION['authId']."', 
												modified_by_date_time = '".date('Y-m-d H:i:s')."' 
											WHERE 
												id = '".$row_multi_pcp["id"]."'";
					imw_query($qry_update_multi_pcp);
			}
			else{
				$qry_update_multi_phy = "INSERT INTO 
											patient_multi_ref_phy 
										SET 
											ref_phy_id='".$pcp_phy_id."',
											ref_phy_name='".core_refine_user_input($pcp_phy_name)."',
											phy_type='4',
											patient_id = '".$_REQUEST["pt_id"]."',
											created_by='".$_SESSION['authId']."',
											created_by_date_time='".date('Y-m-d H:i:s')."'
										";
				imw_query($qry_update_multi_phy);
			}
		}
	//------END ADD/UPDATE REFERRING PHYSICIAN AND PCP IN PATIENT_MULTI_REF_PHY------//		
	}
	
	//updating appt specific data
	$str_ap_qry = "";
    $update_appt_task=false;
	if(is_array($arr_ap) && count($arr_ap) > 0 && $arr_ap["sa_app_start_date"]>date('2015-01-01')){//date check included to stop null date entrance
		foreach($arr_ap as $column => $value){
			$str_ap_qry .= " ".$column." = '".$value."',";
		}
		$str_ap_qry = substr($str_ap_qry, 0, -1);
	}
	if($str_ap_qry != "")
	{
		$fa_qry="SELECT 
					id 
				FROM 
					schedule_appointments 
				WHERE 
					sa_patient_id = '".$_REQUEST["pt_id"]."' 
				AND 
					sa_patient_app_status_id = 271";
					
		$result_fa_obj = imw_query($fa_qry);
		if(imw_num_rows($result_fa_obj) > 0)
		{
			$fa_sch_data = imw_fetch_assoc($result_fa_obj);
			$_REQUEST["ap_id"] = $fa_sch_data["id"];
			$obj_scheduler->logApptChangedStatus($_REQUEST["ap_id"], $arr_ap["sa_app_start_date"], $arr_ap["sa_app_starttime"], $arr_ap["sa_app_endtime"], '271', $arr_ap["sa_doctor_id"], $arr_ap["sa_facility_id"], $arr_ap["sa_madeby"], $ap_act_reason, $arr_ap["procedureid"], true,$arr_ap["sec_procedureid"],$arr_ap["tertiary_procedureid"],'',$arr_ap['sa_ref_management'],$sa_verification_req);
		}

		if(isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"]))
		{
            $update_appt_task=true;
			$init = "UPDATE ";
			$where = " WHERE id = '".$_REQUEST["ap_id"]."'";
		}
		else
		{
			$init = "INSERT INTO ";
			$where = "";
		}
		
		//log reschedule
		if($_REQUEST["save_type"] == "reschedule")
		{
			//reason, if any for action
			$ap_act_reason = core_refine_user_input(urldecode($_REQUEST["ap_act_reason"]));
			$obj_scheduler->logApptChangedStatus($_REQUEST["ap_id"], $arr_ap["sa_app_start_date"], $arr_ap["sa_app_starttime"], $arr_ap["sa_app_endtime"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_doctor_id"], $arr_ap["sa_facility_id"], $arr_ap["sa_madeby"], $ap_act_reason, $arr_ap["procedureid"], true,$arr_ap["sec_procedureid"],$arr_ap["tertiary_procedureid"],'',$arr_ap['sa_ref_management'],$sa_verification_req);
		}
	
	 	$str_ap_qry = $init." schedule_appointments SET ".$str_ap_qry.", sa_patient_app_show=0, sa_app_time = '".date('Y-m-d H:i:s')."' ".$where;
		//echo $str_ap_qry;
		
		//------Check Patient Appointment on SLOT - FOR FIX ISSUE DOUBLE CLICK ON SCHEDULER RAIL------//
		$QRY_CHECK_SLOT_TIME="	SELECT 
									id 
								FROM 
									schedule_appointments 
								WHERE 
									sa_doctor_id='".$_REQUEST["doctor_id"]."' 
								AND 
									sa_patient_id='".$_REQUEST["pt_id"]."' 
								AND 
									sa_app_starttime='".$_REQUEST["start_time"]."' 
								AND 
									sa_facility_id='".$_REQUEST["facility_id"]."' 
								AND 
									sa_app_start_date='".$start_date."' 
								AND 
									procedureid='".$proc_id."' 
								AND 
									sa_patient_app_status_id NOT IN(201,18,19,20,203)";
									
		$RES_CHECK_SLOT_TIME=imw_query($QRY_CHECK_SLOT_TIME);
		//--------------------------------------------------------------------------------------------//
			
		$remote_req = 0;
		if(isset($_REQUEST["ap_id"]) && !empty($_REQUEST["ap_id"]) && trim($_REQUEST["ap_id"]) != "undefined")
		{
			imw_query($str_ap_qry);
			$intApptId = $_REQUEST["ap_id"];
			if($_REQUEST["save_type"] != "save")
			{
				$obj_scheduler->keep_track_of_replaced_labels($obj_db, $int_custom_lbl_id, $str_custom_lbl_acronym, $intApptId, $_REQUEST["tmp_id"]);
			}
			$remote_req = 1;
		}
		else if(isset($arr_ap["sa_doctor_id"]) && trim($arr_ap["sa_doctor_id"]) != "0" && trim($arr_ap["sa_doctor_id"]) != "" && trim($arr_ap["sa_doctor_id"]) != "undefined" && (imw_num_rows($RES_CHECK_SLOT_TIME)==0 || !imw_num_rows($RES_CHECK_SLOT_TIME)))
		{
			imw_query($str_ap_qry);
			$intApptId = imw_insert_id();			
			if($_REQUEST["save_type"] != "save")
			{
				$obj_scheduler->keep_track_of_replaced_labels($obj_db, $int_custom_lbl_id, $str_custom_lbl_acronym, $intApptId, $_REQUEST["tmp_id"]);
			}	
			//time locator
			$obj_scheduler->set_scroll_settings($intApptId, $_SESSION["authId"]);
			
			//log new appt
			if($_REQUEST["save_type"] == "addnew")
			{
				//reason, if any for action
				$ap_act_reason = "";
				$obj_scheduler->logApptChangedStatus($intApptId, $arr_ap["sa_app_start_date"], $arr_ap["sa_app_starttime"], $arr_ap["sa_app_endtime"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_doctor_id"], $arr_ap["sa_facility_id"], $arr_ap["sa_madeby"], $ap_act_reason, $arr_ap["procedureid"], false,$arr_ap["sec_procedureid"],$arr_ap["tertiary_procedureid"],'',$arr_ap['sa_ref_management'],$sa_verification_req);
			}
			$remote_req = 1;						
		}
		$obj_scheduler->patients_sync($intApptId);
		
	}
	if($_REQUEST["save_type"] == "save" || $_REQUEST["save_type"] == "reschedule")
	{		
		//$iolinkSite = (isset($_REQUEST["procedure_site"]) && !empty($_REQUEST["procedure_site"])) ? strtolower($_REQUEST["procedure_site"]) : "";
		$iolinkSite = (isset($arr_ap["procedure_site"]) && !empty($arr_ap["procedure_site"]) && empty($_REQUEST["pri_eye_site"])) ? strtolower($arr_ap["procedure_site"]) : $_REQUEST["pri_eye_site"];
		$iolinkSite = (strtolower($iolinkSite) == "bilateral") ? "both" : strtolower($iolinkSite);
		if(constant('DEFAULT_PRODUCT')=='imwemr' || constant("IDOC_IASC_SAME")=="YES")
		{
			$obj_scheduler->setApptInIolink($arr_ap["iolink_iosync_waiting_id"], $arr_ap["sa_app_start_date"], $arr_ap["sa_patient_app_status_id"], $arr_ap["sa_app_starttime"], $arr_ap["sa_comments"], $arr_ap["pick_up_time"], $arr_ap["arrival_time"], $iolinkSite, $_REQUEST["ap_id"], $iolink_prev_dos_emr);
		}	
	}
	if($_REQUEST["save_type"] == "reschedule")
	{
		$init_st_time_rs=$_REQUEST['init_st_time_rs'];
		$init_et_time_rs=$_REQUEST['init_et_time_rs'];
		$init_acronym_rs=$_REQUEST['init_acronym_rs'];	
		$init_date_rs = $_REQUEST['init_date_rs'];
		
		$init_fac_id = trim($_REQUEST['init_fac_id']);
		$init_provider_id = trim($_REQUEST['init_provider_id']);
		
		$provider_id= $init_provider_id;
		$res_facility_id= $init_fac_id;
		
		$dsStartTime=$_REQUEST["start_time"];
		list($stHr,$stMn,$stSec)=explode(':',$dsStartTime);
		$stDurMin=ceil($app_duration/60);
		$dsEndTime=date("H:i:00", mktime($stHr, $stMn + $stDurMin, 0));

		$rs_qry ='
					SELECT 
						id, 
						provider, 
						facility, 
						start_date, 
						start_time, 
						end_time, 
						labels_replaced, 
						l_text, 
						l_show_text 
					FROM 
						scheduler_custom_labels 
					WHERE 
						(
							(start_time>="'.$init_st_time_rs.'" and end_time<="'.$init_et_time_rs.'") 
							OR 
							(
								("'.$init_st_time_rs.'" between start_time and end_time) 
								AND 
								("'.$init_et_time_rs.'" between start_time and end_time)
							)
						) 
						AND 
							start_date="'.$init_date_rs.'" 
						AND 
							provider="'.$provider_id.'" 
						AND 
							facility="'.$res_facility_id.'"';
							
		$appt_id = trim($_REQUEST["ap_id"]);		
		$rs_qry_result = imw_query($rs_qry);
		$lbl_appt_arr = array();
		if(imw_num_rows($rs_qry_result))
		{
			//$lbl_replaced_arr = $rs_qry_result->GetArray();
			
			while($lbl_appt_val=imw_fetch_array($rs_qry_result))
			{
				$lbl_record_id = trim($lbl_appt_val['id']);
				$lbl_replaced = trim($lbl_appt_val['labels_replaced']);
				
				#temp fix to retrive labels if it wasn't in label_replaced field
				if(!$lbl_replaced && !trim($lbl_appt_val["l_show_text"])){
					if($st_date==$_REQUEST['init_date_rs'] && $_REQUEST["start_time"]==$_REQUEST['init_st_time_rs'])
					{//nothing to do
					}
					else
					{
						$sct_id=$lbl_appt_val["id"];
						$provider=$lbl_appt_val["provider"];
						$facility=$lbl_appt_val["facility"];	
						$start_date=$lbl_appt_val["start_date"];
						$start_time=$lbl_appt_val["start_time"];
						$end_time=$lbl_appt_val["end_time"];
						$l_text=$lbl_appt_val["l_text"];
	
						$qry_appt="	SELECT 
										id,
										sch_template_id 
									FROM 
										`schedule_appointments` 
									WHERE 
										sa_doctor_id='".$provider."' 
									AND 
										sa_facility_id='".$facility."' 
									AND 
										sa_app_start_date='".$start_date."' 
									AND 
										sa_app_starttime='".$start_time."' 
									AND 
										sa_patient_app_status_id NOT IN (203,201,18,19,20) 
									AND 
										IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 )
								";
						
						$res_appt=imw_query($qry_appt);
						if(imw_num_rows($res_appt)>0)
						{
							$row_appt=imw_fetch_assoc($res_appt);		
							$sch_template_id=$row_appt["sch_template_id"];
							$HH=$MM=$HH1=$MM1="";
							list($HH,$MM)=explode(":",$start_time);
							list($HH1,$MM1)=explode(":",$end_time);
							$new_start_time=$HH.":".$MM;
							$new_end_time=$HH1.":".$MM1;
							$labels_replaced="";
							$label_name=$l_text;
							if(strstr($l_text,";"))
							{
								list($label_name)=explode(";",$l_text);	
							}
							if($row_appt["id"] && trim($label_name))
							{
								$labels_replaced="::".$row_appt["id"].":".trim($label_name);
								if($row_appt["id"]==$appt_id){$lbl_replaced=$labels_replaced;}
							}
							$qry_update="UPDATE 
											scheduler_custom_labels 
										SET 
											labels_replaced='".$labels_replaced."' 
										WHERE 
											id='".$sct_id."'";
							$res_update=imw_query($qry_update);
						
						}
						else if(imw_num_rows($res_appt)==0)
						{
							$qry_update="UPDATE 
											scheduler_custom_labels 
										SET 
											l_show_text=l_text 
										WHERE 
											id='".$sct_id."'";
							$res_update=imw_query($qry_update);
						}
					}
				}
				#temp fix ends here
				
				$lbl_replace_act=false;
				if($lbl_replaced!="" && $lbl_record_id!="")
				{					
					$lbl_replaced_array = array();
					$lbl_replaced_array = explode('::',$lbl_replaced);
					foreach($lbl_replaced_array as $lbl_replaced_entity)
					{
						$lbl_replaced_entity = trim($lbl_replaced_entity);
						if($lbl_replaced_entity!="")
						{
								list($lbl_replaced_appt_id,$get_lbl_replaced) = explode(':',$lbl_replaced_entity); 
								$get_lbl_replaced = trim($get_lbl_replaced);
								if($appt_id == $lbl_replaced_appt_id)
								{
									
									$target_replace = '::'.$appt_id.':'.$get_lbl_replaced;
									$rsc_update_qry='UPDATE 
														scheduler_custom_labels 
													SET 
														l_show_text = concat(TRIM(l_show_text),
														if(TRIM(l_show_text)="","'.$get_lbl_replaced.'","; '.$get_lbl_replaced.'")), 
														labels_replaced = replace(labels_replaced,"'.$target_replace.'","") 
													WHERE 
														id ='.$lbl_record_id;
									imw_query($rsc_update_qry);
									$lbl_replace_act=true;
									break;
									
								}							
															
						}
					}
					//re-validate is we have reversed label or not	
					if($lbl_replace_act==false)
					{
						//validate all label replaced record
						$obj_scheduler->validate_label_replaced($lbl_appt_val);
					}	
				}					
			}
		}
	}
    
    //Insert in verification table
    $obj_scheduler->save_verification_data($intApptId);
        
	/*  for task manager */
    if((isset($_REQUEST["save_type"]) && $_REQUEST["save_type"]!="save") || $update_appt_task==true)
	{
        $params=array();
        $params['patientid']=$pt_id;
        //$params['operatorid']=$_SESSION["authId"];
        $params['operatorid']=$_REQUEST["doctor_id"];
        $params['section']='appointment';
        
        switch($_REQUEST["save_type"])
		{
            case'addnew':
                $sub_section='appt_created';
                break;
            case'reschedule':
                $sub_section='appt_reschedule';
                break;
            default:
                $sub_section='other_action';
                break;
        }
        if($update_appt_task==true)$sub_section='appt_created';
        $params['sub_section']=$sub_section; //appt_canceled,appt_created,appt_deleted,appt_no_show,appt_reschedule
        $params['obj_value']=$intApptId;
        $serialized_arr = serialize($params);
        include_once("../../interface/common/assign_new_task.php");
    }
    
    /* MVE PORTAL CREATE NEW APPOINTMENT STARTS HERE*/
	$erp_error=array();
    if(isERPPortalEnabled()) {
		try {
			include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
			$obj_appointments = new Appointments;
			$appt_act_reason = "";
			if(isset($_REQUEST["ap_act_reason"]) && $_REQUEST["ap_act_reason"]!=''){
				$appt_act_reason = core_refine_user_input(urldecode($_REQUEST["ap_act_reason"]));
			}
			$obj_appointments->addUpdateAppointments($intApptId,$pt_id,$appt_act_reason);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
    }
    /* MVE PORTAL CREATE NEW APPOINTMENT ENDS HERE*/
    
	list($yr, $mn, $dt) = explode("-", $arr_ap["sa_app_start_date"]);
	echo $_REQUEST["save_type"];
	echo "~";
	echo date("l", mktime(0, 0, 0, $mn, $dt, $yr));
	echo "~";
	echo $arr_ap["sa_app_start_date"];
	echo "~";
	echo $apptCancelOnDeceased;
	
	/*********NEW HL7 ENGINE START************/
	require_once(dirname(__FILE__)."/../../hl7sys/api/class.HL7Engine.php");
	$objHL7Engine = new HL7Engine();
	$objHL7Engine->application_module = 'scheduler';
	$objHL7Engine->msgSubType = $arr_ap["sa_patient_app_status_id"];
	$objHL7Engine->source_id = $intApptId;
	$objHL7Engine->generateHL7();
	/*********NEW HL7 ENGINE END*************/
		
	/*  Purpose: Generate hl7 message on saving and modification of appointment */
	$allowedEvents = $GLOBALS["HL7_SIU_EVENTS"];
	
	if(constant("HL7_SIU_GENERATION")==true && is_array($allowedEvents) && in_array($arr_ap["sa_patient_app_status_id"], $allowedEvents) && $intApptId != '' && $pt_id != '')
	{
		require_once( dirname(__FILE__).'/../../hl7sys/hl7GP/hl7FeedData.php');
		$hl7 = new hl7FeedData();

		$hl7->PD['id'] = $pt_id;
		$hl7->PD['schid'] = $intApptId;

		$hl7->setTrigger('SIU', $arr_ap["sa_patient_app_status_id"]);

		if( isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING']) )
		{
			$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
			$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
			$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
		}

		$hl7->addEVN($hl7->msgtypes['SIU']['trigger_event']);

		if( isset($GLOBALS['HL7_SIU_SEGMENTS']) && is_array($GLOBALS['HL7_SIU_SEGMENTS']) )
		{
			foreach( $GLOBALS['HL7_SIU_SEGMENTS'] as $segment )
			{
				$hl7->insertSegment($segment, 'SIU');
			}
		}

		$hl7->log_message();
	}
	/* End code*/
}
}

//------FILE INCLUDED TO CHECK AND SEND APPOINTMENT DETAIL AT API------
$rs=imw_query("	SELECT 
					proc 
				FROM 
					slot_procedures 
				WHERE id='".$proc_id."'
			");
$res=imw_fetch_assoc($rs);
$proc_name= strtolower($res['proc']);


//------API------
if(sizeof($GLOBALS["API_PROCEDURES"])>0 && $GLOBALS["API_PROCEDURES"][$proc_name])
{
	if(file_exists('sending_info_to_api.php'))
	{
		include('sending_info_to_api.php');
	}
}
?>