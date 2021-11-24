<?php
/**
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * Use this software under MIT License
 * 
 * File: appt_schedule_functions.php
 * Purpose: Define Scheduler Functions
 * Access Type: Include
 */

//$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/"
require_once($GLOBALS['fileroot'].'/library/classes/class.language.php');
class appt_scheduler extends core_lang{

	private $obj_db;
	public $objCoreLang,$schScrollWidthFlag;
	
	/**
	 * Function: Constructor of appt_scheduler
	 * Purpose: captures db object in private variable
	 * Author: ravi, prabh
	 */
	function __construct()
	{
		$this->objCoreLang = new core_lang;
		$this->schScrollWidthFlag = 0;
	}
	
	/**
	 * Function: generate_month_list
	 * Purpose: to create lists for month in month view scheduler
	 * Author: ravi, prabh
	 */
	function generate_month_list($db_load_date)
	{
		
		list($yr, $mn, $dt) = explode("-", $db_load_date);		
		$ts = mktime(0, 0, 0, $mn, $dt, $yr);
		$load_month = date("F", $ts);
		$load_mn = $mn;
		$load_yr = $yr;
		$str_load_mn = "<option value=\"".$load_mn."-01-".$load_yr."|\" selected=\"selected\">".$load_month." ".$load_yr."</option>";

		$str_next_mn = $str_last_mn = $str_last_wk = $str_next = $str_last = "";
		$next_yr = $last_yr = $yr;
		$next_mn = $last_mn = $mn;

		for($less = 3; $less > 0; $less--)
		{
			$last_ts = mktime(0, 0, 0, $mn - $less, 1, $yr);
			$last_month = date("F", $last_ts);
			$last_mn = date("m", $last_ts);
			$last_yr = date("Y", $last_ts);
			$str_last_mn .= "<option value=\"".$last_mn."-01-".$last_yr."|\">".$last_month." ".$last_yr."</option>";
			$str_last = ($str_last == "" && $less == 1) ? $last_mn."-01-".$last_yr."|" : $str_last;
		}

		for($plus = 1; $plus < 16; $plus++)
		{			
			$next_ts = mktime(0, 0, 0, $mn + $plus, 1, $yr);
			$next_month = date("F", $next_ts);
			$next_mn = date("m", $next_ts);
			$next_yr = date("Y", $next_ts);
			$str_next_mn .= "<option value=\"".$next_mn."-01-".$next_yr."|\">".$next_month." ".$next_yr."</option>";
			$str_next = ($str_next == "") ? $next_mn."-01-".$next_yr."|" : $str_next;
		}

		return array($str_last_mn.$str_load_mn.$str_next_mn, $str_next, $str_last, $load_mn."|".$load_yr."|");
	}
	
	/**
	 * Function: get_session_cache
	 * Purpose: to get scheduler cached data
	 * Author: ravi, prabh
	 */
	function get_session_cache($c_type, $c_marker)
	{
		$return = false;
		$qry = "SELECT 
					c_content 
				FROM 
					`scheduler_cached_data` 
				WHERE 
					c_marker = '".$c_marker."' 
				AND 
					c_type = '".$c_type."'
				";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0)
		{
			$arr = imw_fetch_assoc($res);
			$return = $arr;
		}
		return $return;
	}
	
	/**
	 * Function: save_session_cache
	 * Purpose: to cache scheduler data
	 * Author: ravi, prabh
	 */
	function save_session_cache($c_type, $c_content, $c_marker)
	{ //marker is user id, type - FAC_SELECT, PRO_SELECT
		$qry0 ="SELECT 
					c_id 
				FROM 
					`scheduler_cached_data` 
				WHERE 
					c_marker = '".$c_marker."' 
				AND 
					c_type = '".$c_type."'
				";
		$res0 = imw_query($qry0);
		if(imw_num_rows($res0) > 0)
		{
			$arr0 = imw_fetch_assoc($res0);
			$qry = "UPDATE 
						`scheduler_cached_data` 
					SET 
						`c_type` = '".$c_type."', 
						`c_marker` = '".$c_marker."', 
						`c_content` = '".addslashes($c_content)."', 
						`c_modified_on` = ".date('Y-m-d').", 
						`c_modified_by` = '".$c_marker."' 
					WHERE 
						c_id = '".$arr0["c_id"]."'";
		}
		else
		{
			$qry = "INSERT INTO 
						`scheduler_cached_data` 
					SET 
						`c_type` = '".$c_type."',
						`c_marker` = '".$c_marker."',
						`c_content` = '".addslashes($c_content)."',
						`c_modified_on` = ".date('Y-m-d').",
						`c_modified_by` = '".$c_marker."'
					";
		}
		imw_query($qry);
	}

	/**
	 * Function: load_procedures
	 * Purpose: To load_procedures in drop down
	 * Author: Ravi, Prabh
	 * Returns: ARRAY with provider ids / STRING select options DEPENDING UPON THE calling parameter retury_type = "ARRAY" / "OPTIONS"
	*/
	function load_procedures($selected_val = 0)
	{
		$return_array = array();
		$user_type=$_SESSION["authGroupId"];
		$return = "";$user_proc_NA=0;
		$default_proc_id_found = false;
		if(empty($selected_val))
		{
			$default_proc_id_found = true;
		}

		$qry = "SELECT 
					id, 
					proc, 
					acronym, 
					user_group, 
					labels,
					ref_management,
					verification_req,
					source 
				FROM 
					`slot_procedures` 
				WHERE 
					times = '' 
				AND 
					proc != '' 
				AND 
					doctor_id = 0 
				AND 
					active_status = 'yes' 
				ORDER BY proc";
		
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0)
		{
			while($arr_proc = imw_fetch_assoc($res))
			{
				$sel = "";$arr_proc_user_group="";
				if($selected_val == $arr_proc["id"])
				{
					$sel = "selected";
					$default_proc_id_found = true;
				}
				$arr_proc_user_group=explode(",",$arr_proc["user_group"]);
				if(!in_array($user_type,$arr_proc_user_group) && $user_type!="")
				{
					if($selected_val!=$arr_proc["id"])
					{
						continue;
					}
					else
					{
						if($arr_proc["user_group"])$user_proc_NA=1;	
					}
				}
				
				
				if(DEFAULT_PRODUCT == "imwemr")
				{
					$return .= ((trim($arr_proc["acronym"]) == "") ? "<option $sel value=\"".$arr_proc["id"]."\" data-labels=\"".$arr_proc["labels"]."\">".$arr_proc["proc"]."</option>" : "<option $sel value=\"".$arr_proc["id"]."\" data-labels=\"".$arr_proc["labels"]."\" data-referral=\"".$arr_proc['ref_management']."\" data-verification=\"".$arr_proc['verification_req']."\">".$arr_proc["acronym"]."</option>");
				}
				else
				{
					$return .= "<option $sel value=\"".$arr_proc["id"]."\" data-labels=\"".$arr_proc["labels"]."\" data-referral=\"".$arr_proc['ref_management']."\" data-verification=\"".$arr_proc['verification_req']."\">".$arr_proc["proc"]."</option>";
				}
				$return_array[$arr_proc["id"]] = $arr_proc["proc"];
			}
		}
		if($default_proc_id_found == false)
		{
			$qry2 = "SELECT 
						id, 
						proc, 
						acronym, 
						active_status, 
						labels 
					FROM 
						`slot_procedures` 
					WHERE 
						id = '".$selected_val."'";
			$res2 = imw_query($qry2);
			if(imw_num_rows($res2) > 0)
			{
				$arr_sel = imw_fetch_assoc($res2);
				$fontColor ='';
				if($arr_sel['active_status']=='no' || $arr_sel['active_status']=='del')
				{
					$fontColor = "style=\"color:#CC0000;\"";	$sel = "selected=\"selected\"";
				}
				if(DEFAULT_PRODUCT == "imwemr")
				{
					$return .= ((trim($arr_sel["acronym"]) == "") ? "<option $sel value=\"".$arr_sel["id"]."\" data-labels=\"".$arr_sel["labels"]."\">".$arr_sel["proc"]."</option>" : "<option $sel value=\"".$arr_sel["id"]."\" data-labels=\"".$arr_proc["labels"]."\">".$arr_sel["acronym"]."</option>");
				}
				else
				{
					$return .= "<option value=\"".$arr_sel["id"]."\" ".$fontColor." $sel data-labels=\"".$arr_sel["labels"]."\">".$arr_sel["proc"]."</option>";
				}
				$return_array[$arr_sel["id"]] = $arr_sel["proc"];
			}
		}
		
		return array($return, $return_array,$user_proc_NA);
	}
	
	/**
	 * Function: set_auto_status
	 * Purpose: Check last 24/48 or multiple of 24hrs appointments and update status no shore if no action taken aganist appointed patient
	 * Author: Ravi, Prabh
	 * Returns: null
	 */
	function set_auto_status()
	{
		$delayed_auto_update = 48; //in hours 24 / 48/ 72/ multiple of 24
		$delayed_auto_days = $delayed_auto_update / 24;
		$arr_pt_appt = array();
		$arr_pt = array();
		$check_dt = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")) - (86400 * $delayed_auto_days));
		//get list of appointments
		$qry = "SELECT 
					id, 
					sa_patient_id 
				FROM 
					`schedule_appointments` 
				WHERE 
					DATEDIFF('".date("Y-m-d")."', DATE_FORMAT(sa_app_start_date, '%Y-%m-%d')) = '".$delayed_auto_days."' 
				AND 
					sa_patient_app_status_id IN (0, 202) 
				ORDER BY 
					sa_app_starttime DESC";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			while($arr_sch = imw_fetch_assoc($res)){
				$arr_pt_appt[$arr_sch["sa_patient_id"]][] = $arr_sch["id"];
				$arr_pt[] = $arr_sch["sa_patient_id"];
			}
			//check appointed patient aganist superbill entry
			if(count($arr_pt_appt) > 0){
				$qry1 = "SELECT 
							idSuperBill, 
							patientId 
						FROM 
							`superbill` 
						WHERE 
							patientId IN (".implode(",", $arr_pt).") 
						AND 
							dateOfService = '".$check_dt."' 
						AND 
							del_status='0'";
				$res1 =  imw_query($qry1);
				if(imw_num_rows($res1) > 0)
				{
					while($arr_sch1 = imw_fetch_assoc($res1))
					{
						if(in_array($arr_sch1["patientId"], $arr_pt) == true)
						{
							unset($arr_pt_appt[$arr_sch1["patientId"]]);
						}
					}
				}
			}
			
			//check appointed patient aganist charge list entry
			if(count($arr_pt_appt) > 0){
				$qry2 = "SELECT 
							charge_list_id, 
							patient_id 
						FROM 
							`patient_charge_list` 
						WHERE 
							del_status='0' 
						AND 
							patient_id IN (".implode(",", $arr_pt).") 
						AND 
							date_of_service = '".$check_dt."'
						";
				$res2 =  imw_query($qry2);
				if(imw_num_rows($res2) > 0)
				{
					while($arr_sch2 = imw_fetch_assoc($res2))
					{
						if(in_array($arr_sch2["patient_id"], $arr_pt) == true)
						{
							unset($arr_pt_appt[$arr_sch2["patientId"]]);
						}
					}
				}
			}
			
			//check appointed patient aganist cc history entry
			if(count($arr_pt_appt) > 0)
			{
				$qry3 = "SELECT 
							cc_id, 
							patient_id 
						FROM 
							`chart_left_cc_history` 
						WHERE 
							patient_id IN (".implode(",", $arr_pt).") 
						AND 
							date_of_service = '".$check_dt."'
						";
				$res3 =  imw_query($qry3);
				if(imw_num_rows($res3) > 0){
					while($arr_sch3 = imw_fetch_assoc($res3)){
						if(in_array($arr_sch3["patient_id"], $arr_pt) == true){
							unset($arr_pt_appt[$arr_sch3["patientId"]]);
						}
					}
				}
			}
			
			if(count($arr_pt_appt) > 0){
				$str_pt_appt = "-1";
				foreach($arr_pt_appt as $this_pt_appt){
					$str_pt_appt .= ",".implode(",", $this_pt_appt);
				}
				//update appointment status to NS if no action taken aganist it
				$qry4 = "UPDATE 
							schedule_appointments 
						SET 
							sa_patient_app_status_id = '3' 
						WHERE 
							id IN (".$str_pt_appt.")";
				imw_query($qry4);

				$arr_hist_pt = explode(",", $str_pt_appt);
				for($n = 0; $n < count($arr_hist_pt); $n++){
					if($arr_hist_pt[$n] != "-1"){
						$this->logApptChangedStatus($arr_hist_pt[$n], "", "", "", "3", "", "", "admin", "STATUS AUTO SET TO NO-SHOW", "");
					}
				}
			}
		}
	}
	
	/**
	 * Function: setApptInIolink
	 * Purpose: this function logs all the appointment status changes in previous_status table
	 * Author: ravi, prabh
	 */
	function logApptChangedStatus($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId, $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername, $strChangeReason, $intNewApptProcedureId, $blUpdateNew = false, $intNewApptSecProcedureId = 0, $intNewApptTerProcedureId = 0, $wait_list_indicator = 'First Available',$ref_management = 0,$sa_verification_req = 0)
	{

		$strQry = "	SELECT 
						procedureid,
						sec_procedureid,
						tertiary_procedureid,
						sa_patient_app_status_id,
						sa_patient_id, 
						sa_app_start_date, 
						sa_app_starttime,
						sa_app_endtime,
						sa_comments,
						sa_facility_id, 
						sa_madeby, 
						sa_doctor_id,
						sa_comments,
						sa_ref_management  
					FROM 
						schedule_appointments 
					WHERE 
						id = '".$intApptId."'";
		$rsData = imw_query($strQry);	
		$arrData = imw_fetch_assoc($rsData);
		
		$intPatientId = $arrData['sa_patient_id'];				//patient id
        $dtOldApptDate = $arrData['sa_app_start_date'];			//old_appt_date
		$tmOldApptStartTime = $arrData['sa_app_starttime'];			//old_appt_start_time
		$tmOldApptEndTime = $arrData['sa_app_endtime'];			//old_appt_end_time
		$intOldApptStatusId = $arrData['sa_patient_app_status_id'];	//old_status
		$intOldApptProviderId = $arrData['sa_doctor_id'];			//old_provider
		$intOldApptFacilityId = $arrData['sa_facility_id'];		//old_facility
		$strOldApptOpUsername = $arrData['sa_madeby'];				//oldMadeBy
		$intOldApptProcedureId = $arrData['procedureid'];
		$intOldApptSecProcedureId = $arrData['sec_procedureid'];
		$intOldApptTerProcedureId = $arrData['tertiary_procedureid'];						//oldMadeBy
		$intOldRefManagement = $arrData['sa_ref_management'];						//oldMadeBy
		$strOldApptComments = core_refine_user_input($arrData['sa_comments']);				//oldMadeBy
		if($intApptId)
		{
            $verification_data=$this->get_verification_data($intApptId);
            $intOldVerificationReq = $verification_data['v_required'];
        }
		
		
		if($blUpdateNew == false)
		{
			$dtNewApptDate = $arrData['sa_app_start_date'];			//New_appt_date
			$tmNewApptStartTime = $arrData['sa_app_starttime'];			//New_appt_start_time
			$tmNewApptEndTime = $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId = $arrData['sa_doctor_id'];			//New_provider
			$intNewApptFacilityId = $arrData['sa_facility_id'];		//New_facility
			$intNewApptProcedureId = $arrData['procedureid'];				//NewMadeBy
			$intNewApptSecProcedureId = $arrData['sec_procedureid'];
			$intNewApptTerProcedureId = $arrData['tertiary_procedureid'];
			$intNewRefManagement = $arrData['sa_ref_management'];
            $intNewVerificationReq = $sa_verification_req;
		}
		
		$wait_list_indicator_qry = '';
		if($intNewApptStatusId == 201)
		{
			$wait_list_indicator_qry = " , wait_list_indicator = '".$wait_list_indicator."' ";
		}
		
		//making log 
		$strInsQry = "INSERT INTO 
						`previous_status` 
					SET
						sch_id = '".$intApptId."',
						patient_id = '".$intPatientId."',
						status_time = '".date("H:i:s")."',
						status_date = '".date("Y-m-d")."',
						status = '".$intNewApptStatusId."',
						old_date = '".$dtOldApptDate."',
						old_time = '".$tmOldApptStartTime."',
						old_provider = '".$intOldApptProviderId."',
						old_facility = '".$intOldApptFacilityId."',
						statusComments = '".$strOldApptComments."',
						oldStatusComments = '".$strOldApptComments."',
						oldMadeBy = '".$strOldApptOpUsername."',
						statusChangedBy = '".$strNewApptOpUsername."',
						dateTime = '".date("Y-m-d H:i:s")."',
						new_facility = '".$intNewApptFacilityId."',
						new_provider = '".$intNewApptProviderId."',
						old_status = '".$intOldApptStatusId."',
						old_appt_end_time = '".$tmOldApptEndTime."',
						new_appt_date = '".$dtNewApptDate."',
						new_appt_start_time = '".$tmNewApptStartTime."',
						new_appt_end_time = '".$tmNewApptEndTime."',
						old_procedure_id = '".$intOldApptProcedureId."',
						new_procedure_id = '".$intNewApptProcedureId."',
						old_sec_procedure_id = '".$intOldApptSecProcedureId."',
						new_sec_procedure_id = '".$intNewApptSecProcedureId."',
						old_ter_procedure_id = '".$intOldApptTerProcedureId."',
						new_ter_procedure_id = '".$intNewApptTerProcedureId."',
						old_ref_management = '".$intOldRefManagement."',
						new_ref_management = '".$intNewRefManagement."',
						old_verification_req = '".$intOldVerificationReq."',
						new_verification_req = '".$intNewVerificationReq."',
						change_reason = '".core_refine_user_input($strChangeReason)."'".$wait_list_indicator_qry;
		
		imw_query($strInsQry); 
	}
	/**
	 * Function: load_providers
	 * Purpose: To load providers in the top multiple select
	 * Author: Ravi, Prabh
	 * Returns: ARRAY with provider ids / STRING select options DEPENDING UPON THE calling parameter retury_type = "ARRAY" / "OPTIONS"
	 */
	function load_providers($return_type = "ARRAY", $sel_val = "")
	{
		$return = false;
		$key=0;
		$qry = "SELECT 
					id, 
					fname, 
					lname, 
					mname 
				FROM 
					`users` 
				WHERE 
					Enable_Scheduler = '1' 
				AND 
					delete_status = '0' 
				ORDER BY 
					lname, fname";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0)
		{
			$arr_cmb_new = array();
			$arr_cmb_default = array();
			while($arr_cmb_val = imw_fetch_assoc($res))
			{
				$key++;
				$user_fname_sc = strtolower($arr_cmb_val["fname"]);
				if($user_fname_sc == "testing")
				{
					$arr_cmb_new[$key] = $arr_cmb_val;
				}
				else
				{
					$arr_cmb_default[$key] = $arr_cmb_val;	
				}
			}
			
			$arr = array_merge($arr_cmb_default,$arr_cmb_new);			
			$arr_return = array();
			$str_return = "";
			$int_cnt = 0;
			for($f = 0; $f < count($arr); $f++)
			{
				if(trim($arr[$f]["mname"]) != "")
				{
					$arr[$f]["mname"] = " ".$arr[$f]["mname"];	
				}
				$prov_name = core_name_format($arr[$f]["lname"], $arr[$f]["fname"]).$arr[$f]["mname"];
				
				$sel = "";
				if($sel_val == $arr[$f]["id"]){
					$sel = "selected";
				}
				//options
				$str_return .= "<option value=\"".$arr[$f]["id"]."\" ".$sel.">".$prov_name."</option>";
				
				//array
				$arr_return[$int_cnt]["id"] = $arr[$f]["id"];
				$arr_return[$int_cnt]["name"] = $prov_name;
				$int_cnt++;
			}
			if($return_type == "ARRAY"){
				$return = $arr_return;
			}else if($return_type == "OPTIONS"){
				$return = $str_return;
			}
		}
		return $return;
	}
	
	/*
	Function: load_facilities
	Purpose: to load facilities in the top multiple select
	Author: ravi, prabh
	Returns: ARRAY with facility ids / STRING select options DEPENDING UPON THE calling parameter retury_type = "ARRAY" / "OPTIONS"
	*/
	function load_facilities($prov_id = 0, $return_type = "ARRAY", $sel_fac = "0"){
		$return = false;
		if($prov_id != 0){
			
			$qry = "select id, name from facility order by name";	
			$res = imw_query($qry);
			if(imw_num_rows($res) > 0){
				$int_default = 0;
				$arr_default = $this->get_prov_default_facilities($prov_id);
				if($arr_default !== false){
					$int_default = count($arr_default);
				}

				$arr_return = array();
				$str_return = "";
				$int_cnt = 0;
				while($arr = imw_fetch_assoc($res)){
					if($int_default > 0){
						if(in_array($arr["id"], $arr_default)){
							//options
							$sel ="";
							if($sel_fac == $arr["id"]){
								$sel = "selected";
							}
							$str_return .= "<option value=\"".$arr["id"]."\" ".$sel.">".$arr["name"]."</option>";
							
							//array
							$arr_return[$int_cnt]["id"] = $arr["id"];
							$arr_return[$int_cnt]["name"] = $arr["name"];
							$int_cnt++;
						}	
						
					}else{
						//options
						$str_return .= "<option value=\"".$arr["id"]."\">".$arr["name"]."</option>";
						
						//array
						$arr_return[$int_cnt]["id"] = $arr["id"];
						$arr_return[$int_cnt]["name"] = $arr["name"];
						$int_cnt++;
					}
				}
				if($return_type == "ARRAY"){
					$return = $arr_return;
				}else if($return_type == "OPTIONS"){
					$return = $str_return;
				}
			}
		}
		return $return;
	}
	/*
	Function: get_prov_default_facilities
	Purpose: to get default facilities for a provider
	Author: ravi, prabh
	Returns: ARRAY with default facility ids
	*/
	function get_prov_default_facilities($prov_id = 0){
		$return = false;
		if($prov_id != 0){
			$qry = "SELECT default_facility, sch_facilities FROM users WHERE id = '".$prov_id."'";
			$res = imw_query($qry);
			if(imw_num_rows($res) > 0){
				$arr = imw_fetch_assoc($res);
				$return = explode(";", $arr["sch_facilities"]);
			}
		}
		return $return;
	}
	
	/*
	Function: load_calendar
	Purpose: to load scheduler calendar
	Author: ravi, prabh
	Returns: STRING with Calendar HTML
	*/
	function load_calendar($working_day_dt){
		$return = array("", "", "");
		
		list($y, $m, $d) = explode("-", $working_day_dt);
		if($m == 11){
			$next_month1 = date("m", mktime(0, 0, 0, $m + 1, 1, $y));
			$next_month2 = date("m", mktime(0, 0, 0, $m + 2, 1, $y + 1));
			$next_year1 = $y;
			$next_year2 = $y + 1;
		}else if($m == 12){
			$next_month1 = date("m", mktime(0, 0, 0, $m + 1, 1, $y + 1));
			$next_month2 = date("m", mktime(0, 0, 0, $m + 2, 1, $y + 1));
			$next_year1 = $y + 1;
			$next_year2 = $y + 1;
		}else{
			$next_month1 = date("m", mktime(0, 0, 0, $m + 1, 1, $y));
			$next_month2 = date("m", mktime(0, 0, 0, $m + 2, 1, $y));
			$next_year1 = $y;
			$next_year2 = $y;
		}
		
		//reading cache - CACHE NOT IN USE AS IT IS NOT YET MULTIPLE FACILITY SPECIFIC HIGHLIGHTING COMPATIBLE - AMIT
		$qry = "select c_content, c_marker from scheduler_cached_data where (c_marker = '".$m."-".$y."' OR c_marker = '".$next_month1."-".$next_year1."' OR c_marker = '".$next_month2."-".$next_year2."') and c_type = 'CALENDAR_BASE'";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			while($arr=imw_fetch_assoc($res)){
				if($arr["c_marker"] == $m."-".$y){
					$return[0] = $arr["c_content"];
				}
				if($arr["c_marker"] == $next_month1."-".$next_year1){
					$return[1] = $arr["c_content"];
				}
				if($arr["c_marker"] == $next_month2."-".$next_year2){
					$return[2] = $arr["c_content"];
				}
			}
		}
		
		if($return[0] == ""){
			$return[0] = $this->create_calendar($working_day_dt);
		}
		if($return[1] == ""){
			$return[1] = $this->create_calendar($next_year1."-".$next_month1."-01");
		}
		if($return[2] == ""){
			$return[2] = $this->create_calendar($next_year2."-".$next_month2."-01");
		}
		return $return;
	}
	
	/*
	Function: create_calendar
	Purpose: to create new scheduler calendar
	Author: ravi, prabh
	Returns: STRING with Calendar HTML
	*/
	function create_calendar($calendar_load_dt, $working_day_dt=''){
		
		$calendar = "";
		list($loadyear, $loadmonth, $loadday) = explode("-", $calendar_load_dt);
		if($working_day_dt)
		list($selyear, $selmonth, $selday) = explode("-", $working_day_dt);
		
		//first day of month
		$first_of_month = mktime(0, 0, 0, $loadmonth, 1, $loadyear);
		$dateMonthName = date("F", $first_of_month);
		
		//getting day names
		$day_names = ""; #generate all the day names according to the current locale
		$get_sunday=strtotime("2000-01-02");
		for($n = 0, $t = $get_sunday; $n < 7; $n++, $t += 86400){ #946789200=January 2, 2000 was a Sunday
			$d = ucfirst(strftime('%A', $t)); #%A means full textual day name
			$day_names .= "<div class=\"fl cl_d_h\">".htmlentities($day_name_length < 4 ? substr($d, 0, 3) : $d)."</div>";
		}
		
		list($month, $year, $month_name, $weekday) = explode(",", strftime("%m,%Y,%B,%w", $first_of_month));
		
		//getting month title
		$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names
		
		//last month
		
		if($weekday > 0){
			$last_month = $month - 1;
			$last_year = $year;
			if($last_month <= 0){
				$last_month = 12;
				$last_year = $year - 1;
			}
			$first_of_last_month = mktime(0, 0, 0, $last_month, 1, $last_year);
			$days_in_last_month = date("t", $first_of_last_month);
			$dateLastMonthName = date("M", $first_of_last_month);
			
			if($weekday != 7)
				$add_factor = $weekday;
			else
				$add_factor = 0;
			for($l_cnt = 0, $ld = $days_in_last_month - $add_factor + 1; $ld <= $days_in_last_month; $ld++, $l_cnt++){
				if($l_cnt > 5){
					$ld_cls = "fl cl_s_p";
				}else{
					$ld_cls = "fl cl_p_d";
				}
				
				$on_mouse_over_out = "onclick=\"javascript:change_date('new_date', '".$last_year."-".$last_month."-".$ld."');\" 
				onmousedown=\"javascript:show_cal_context_menu('dtblk-".str_replace(" ", "-", $ld_cls)."-curr_".$ld."_".(int)$last_month."', '".$last_year."-".$last_month."-".$ld."', event);\" onmouseover=\"javascript:highlight_date('dtblk-".str_replace(" ", "-", $ld_cls)."-last_".$ld."_".(int)$last_month."', 'fl cl_hili');show_schedule_details('dtblk-curr_".(int)$ld."_".(int)$last_month."_detail',event);\" onmouseout=\"javascript:highlight_date('dtblk-".str_replace(" ", "-", $ld_cls)."-last_".$ld."_".(int)$last_month."', '".$ld_cls."');\"";
			
				if($selday == $ld && intval($selmonth) == intval($last_month)){
					$ld_cls = "fl cl_hili";
					$on_mouse_over_out = "";
				}
				$calendar .= "<div id=\"dtblk-".str_replace(" ", "-", $ld_cls)."-last_".$ld."_".(int)$last_month."\" class=\"".$ld_cls."\" ".$on_mouse_over_out.">".$ld."<br><span class=\"cl_m_f\">".$dateLastMonthName."</span></div><div id=\"dtblk-last_".(int)$day."_".(int)$month."_detail\" style=\"display:none;\"></div>"; #initial 'empty' days
			}
		}
		
		//this month
		for($day = 1, $days_in_month = date("t", $first_of_month); $day <= $days_in_month; $day++, $weekday++){
			$int_this_day = date("w", mktime(0, 0, 0, $month, $day, $year));
			if($int_this_day == 0){
				$weekday   = 0; #start a new week
				$calendar .= " ";
			}
			if($int_this_day == 6 || $int_this_day == 0){
				$ld_cls = "fl cl_s_d";
			}else{
				$ld_cls = "fl cl_d_d";
			}
			$on_mouse_over_out = "onclick=\"javascript:change_date('new_date', '".$year."-".$month."-".$day."');\" onmousedown=\"javascript:show_cal_context_menu('dtblk-".str_replace(" ", "-", $ld_cls)."-curr_".$day."_".(int)$month."', '".$year."-".$month."-".$day."', event);\" onmouseover=\"javascript:highlight_date('dtblk-".str_replace(" ", "-", $ld_cls)."-curr_".$day."_".(int)$month."', 'fl cl_hili');show_schedule_details('dtblk-curr_".(int)$day."_".(int)$month."_detail',event);\" onmouseout=\"javascript:highlight_date('dtblk-".str_replace(" ", "-", $ld_cls)."-curr_".$day."_".(int)$month."', '".$ld_cls."');hide_schedule_details();\"";
			
			if($selday == $day && intval($selmonth) == intval($month)){
				$ld_cls = "fl cl_hili";
				$on_mouse_over_out = "";
			}
			$calendar .= "<div id=\"dtblk-".str_replace(" ", "-", $ld_cls)."-curr_".(int)$day."_".(int)$month."\" class=\"".$ld_cls."\" ".$on_mouse_over_out.">".$day."</div><div id=\"dtblk-curr_".(int)$day."_".(int)$month."_detail\" style=\"display:none;\"></div>";
		}

		//next month
		if($weekday != 7){
			//echo $weekday;
			$next_month = $month + 1;
			$next_year = $year;
			if($next_month > 12){
				$next_month = 1;
				$next_year = $year + 1;
			}
			$ts_next_month = mktime(0, 0, 0, $next_month, 1, $next_year);
			$first_of_next_month = date("j", $ts_next_month);
			$dateNextMonthName = date("M", $ts_next_month);
			//echo (7 - $weekday);
			for($nd = $first_of_next_month; $nd <= (7 - $weekday); $nd++){
				if($nd == (7 - $weekday)){
					$ld_cls = "fl cl_s_p";
				}else{
					$ld_cls = "fl cl_p_d";
				}
				$on_mouse_over_out = "onclick=\"javascript:change_date('new_date', '".$next_year."-".$next_month."-".$nd."');\" onmouseover=\"javascript:highlight_date('dtblk-".str_replace(" ", "-", $ld_cls)."-next_".$nd."_".(int)$next_month."', 'fl cl_hili');\" onmouseout=\"javascript:highlight_date('dtblk-".str_replace(" ", "-", $ld_cls)."-next_".$nd."_".(int)$next_month."', '".$ld_cls."');\"";
			
				if($selday == $nd && intval($selmonth) == intval($next_month)){
					$ld_cls = "fl cl_hili";
					$on_mouse_over_out = "";
				}
				$calendar .= "<div id=\"dtblk-".str_replace(" ", "-", $ld_cls)."-next_".$nd."_".(int)$next_month."\" class=\"".$ld_cls."\" ".$on_mouse_over_out.">".$nd."<br><span class=\"cl_m_f\">".$dateNextMonthName."</span></div><div id=\"dtblk-next".(int)$day."_".(int)$month."_detail\" style=\"display:none;\"></div>"; #initial 'empty' days
				
			}
		}
		
		$calendar = "<div class=\"fl cl_m_h\">".$title."</div><div style=\"clear:both;\"></div>
		<div class=\"fl cl_d_b\">".$day_names."</div> <div class=\"cl_d_c\" onmouseout=\"javascript:hide_schedule_details();\">".$calendar."</div>";

		$c_marker = $month."-".$year;
		$this->create_calendar_cache($calendar, $c_marker);

		return $calendar;
	}
	
	/*
	Function: create_calendar_cache
	Purpose: to cache newly made scheduler calendar
	Author: ravi, prabh
	Returns: NULL
	*/
	function create_calendar_cache($str_cal_html, $c_marker){
		$qry = "Select c_id from scheduler_cached_data where c_marker = '".$c_marker."' and c_type = 'CALENDAR_BASE'";
		$res = imw_query($qry);

		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			$qry = "update scheduler_cached_data set c_marker = '".$c_marker."', c_type = 'CALENDAR_BASE', c_content = '".addslashes($str_cal_html)."', c_modified_on = '".date('Y-m-d')."', c_modified_by = '".$_SESSION["authId"]."' where c_id = '".$arr["c_id"]."'";
			imw_query($qry);
		}else{
			$qry = "insert into scheduler_cached_data set c_marker = '".$c_marker."', c_type = 'CALENDAR_BASE', c_content = '".addslashes($str_cal_html)."', c_modified_on = '".date('Y-m-d')."', c_modified_by = '".$_SESSION["authId"]."'";
			imw_query($qry);
		}
	}
	
	/*
	Function: load_sch_templates
	Purpose: to load schedule templates in the select box
	Author: ravi, prabh
	Returns: ARRAY with schedule template ids / STRING select options DEPENDING UPON THE calling parameter retury_type = "ARRAY" / "OPTIONS"
	*/
	function load_sch_templates($return_type = "ARRAY",$temp_type=''){
		$return = false;
		$qry = "select id, schedule_name, template_type from schedule_templates WHERE del_status='' order by schedule_name";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr_return = array();
			$str_return = "";
			$int_cnt = 0;
			while($arr=imw_fetch_assoc($res)){
				if($temp_type==1 && $arr["template_type"]=="SYSTEM"){continue;}
				//options
				$str_return .= "<option value=\"".$arr["id"]."\">".$arr["schedule_name"]."</option>";
				
				//array
				$arr_return[$int_cnt]["id"] = $arr["id"];
				$arr_return[$int_cnt]["name"] = $arr["schedule_name"];
				$int_cnt++;
			}
			if($return_type == "ARRAY"){
				$return = $arr_return;
			}else if($return_type == "OPTIONS"){
				$return = $str_return;
			}
		}
		return $return;
	}
	
	function cache_prov_working_hrs($working_day_dt, $arr_prov_id = array(), $dir_path = '' , $forcefully = false){ 
	if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common";
		//variable declarations
		$arr_prov_db = $arr_pr_detail = $arr_schedule = $arr_name_sort = $arr_type_sort = $arr_tmp_id = $arr_pro_id = $arr_prv_st_en_time = $arr_mainSt_time = $arr_mainEn_time = $arr_sch_index_sort= array();	$q2 = $r2 = $qry = $pr_detail = $str_sch_tmp_id = "";
		
		//getting provider schedules
		$arr_prov_sch = $this->get_provider_schedules($working_day_dt);
		//provider details
		$qry = "SELECT id, fname, lname, mname, user_type, max_appoint, provider_color, sch_index FROM users WHERE delete_status = '0' AND Enable_Scheduler = '1' ORDER BY sch_index ASC, lname ASC";
		$pr_detail = imw_query($qry);
		if(imw_num_rows($pr_detail) > 0){
			while($arr_pr_detail = imw_fetch_assoc($pr_detail)){
				$arr_prov_db[$arr_pr_detail["id"]]["id"] = $arr_pr_detail["id"];
				$arr_prov_db[$arr_pr_detail["id"]]["fname"] = $arr_pr_detail["fname"];
				$arr_prov_db[$arr_pr_detail["id"]]["lname"] = ucwords($arr_pr_detail["lname"]);
				$arr_prov_db[$arr_pr_detail["id"]]["mname"] = $arr_pr_detail["mname"];
				$arr_prov_db[$arr_pr_detail["id"]]["user_type"] = $arr_pr_detail["user_type"];
				$arr_prov_db[$arr_pr_detail["id"]]["max_appoint"] = $arr_pr_detail["max_appoint"];
				$arr_prov_db[$arr_pr_detail["id"]]["provider_color"] = $arr_pr_detail["provider_color"];
				$arr_prov_db[$arr_pr_detail["id"]]["sch_index"] = ($arr_pr_detail["sch_index"]>0)?$arr_pr_detail["sch_index"]:20;
				//for sorting of doctors in front of tests in appt scheduler
				if($arr_pr_detail["user_type"] == 5){	//5 is for tests
					$arr_prov_db[$arr_pr_detail["id"]]["utype"] = $arr_pr_detail["user_type"];
				}else{
					$arr_prov_db[$arr_pr_detail["id"]]["utype"] = 1; 
				}
			}
		}

		if(is_array($arr_prov_id) && count($arr_prov_id) > 0){
				for($s = 0; $s < count($arr_prov_sch); $s++){
					if(isset($arr_prov_db[$arr_prov_sch[$s]["provider"]]["id"])){ //refining deleted or exempted from scheduler doctors here
						if(isset($arr_schedule[$arr_prov_sch[$s]["provider"]]["cnt"])){							
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["sch"][$arr_schedule[$arr_prov_sch[$s]["provider"]]["cnt"]]["facility"] =  $arr_prov_sch[$s]["facility"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["sch"][$arr_schedule[$arr_prov_sch[$s]["provider"]]["cnt"]]["sch_tmp_id"] = $arr_prov_sch[$s]["sch_tmp_id"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["cnt"] = $arr_schedule[$arr_prov_sch[$s]["provider"]]["cnt"] + 1;
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["id"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["id"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["fname"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["fname"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["lname"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["lname"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["mname"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["mname"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["utype"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["utype"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["user_type"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["user_type"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["sch_index"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["sch_index"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["max_appoint"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["max_appoint"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["provider_color"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["provider_color"];
							$arr_name_sort[$arr_prov_sch[$s]["provider"]] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["lname"];
							$arr_type_sort[$arr_prov_sch[$s]["provider"]] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["utype"];
							$arr_sch_index_sort[$arr_prov_sch[$s]["provider"]] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["sch_index"];
						}else{
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["cnt"] = 1;
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["sch"][0]["facility"] = $arr_prov_sch[$s]["facility"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["sch"][0]["sch_tmp_id"] = $arr_prov_sch[$s]["sch_tmp_id"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["id"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["id"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["fname"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["fname"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["lname"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["lname"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["mname"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["mname"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["utype"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["utype"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["user_type"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["user_type"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["sch_index"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["sch_index"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["max_appoint"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["max_appoint"];
							$arr_schedule[$arr_prov_sch[$s]["provider"]]["provider_color"] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["provider_color"];
							$arr_name_sort[$arr_prov_sch[$s]["provider"]] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["lname"];
							$arr_type_sort[$arr_prov_sch[$s]["provider"]] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["utype"];
							$arr_sch_index_sort[$arr_prov_sch[$s]["provider"]] = $arr_prov_db[$arr_prov_sch[$s]["provider"]]["sch_index"];
						}
						$arr_tmp_id[] = $arr_prov_sch[$s]["sch_tmp_id"];
						$arr_pro_id[$arr_prov_sch[$s]["provider"]][]= $arr_prov_sch[$s]["sch_tmp_id"];
					}
				}
			//}
		}
		//sorting by user type i.e doctors first and tests last and then by last name
		array_multisort($arr_sch_index_sort, SORT_ASC, SORT_NUMERIC, $arr_name_sort, SORT_ASC, SORT_FLAG_CASE, $arr_schedule);
		$str_sch_tmp_id = "('".implode("','", $arr_tmp_id)."')";
		$str_timings1 = "select id, TIMESTAMP(CONCAT('2007-11-30 ', morning_start_time)) as morning_start_time, TIMESTAMP(CONCAT('2007-11-30 ', morning_end_time)) as morning_end_time from schedule_templates WHERE id IN ".$str_sch_tmp_id." group by id";
		$res_timings1 = imw_query($str_timings1);
		if(imw_num_rows($res_timings1) > 0){
			foreach($arr_prov_sch as $key => $arr_ptmp_id){
				while($arr_timings_tmp=imw_fetch_assoc($res_timings1)){
					$arr_timings1[]=$arr_timings_tmp;
				}
				//print_r($arr_ptmp_id);
				$pro_id = $arr_ptmp_id["provider"];
				for($t = 0; $t < count($arr_timings1); $t++){
					if($arr_timings1[$t]["id"] == $arr_ptmp_id["sch_tmp_id"]){
						if(isset($arr_prv_st_en_time[$pro_id]["st_match"])){
							if($arr_prv_st_en_time[$pro_id]["st_match"] > $arr_timings1[$t]["morning_start_time"]){
								$st = explode(" ", $arr_timings1[$t]["morning_start_time"]);
								$arr_prv_st_en_time[$pro_id]["st"] = $st[1];
								$arr_prv_st_en_time[$pro_id]["st_match"] = $arr_timings1[$t]["morning_start_time"];

								$arr_mainSt_time[] = $arr_timings1[$t]["morning_start_time"];
							}
						}else{
							$st = explode(" ", $arr_timings1[$t]["morning_start_time"]);
							$arr_prv_st_en_time[$pro_id]["st"] = $st[1];
							$arr_prv_st_en_time[$pro_id]["st_match"] = $arr_timings1[$t]["morning_start_time"];
							$arr_mainSt_time[] = $arr_timings1[$t]["morning_start_time"];
						}
						if(isset($arr_prv_st_en_time[$pro_id]["ed_match"])){
							if($arr_prv_st_en_time[$pro_id]["ed_match"] < $arr_timings1[$t]["morning_end_time"]){								
								$ed = explode(" ", $arr_timings1[$t]["morning_end_time"]);
								$arr_prv_st_en_time[$pro_id]["ed"] = $ed[1];
								$arr_prv_st_en_time[$pro_id]["ed_match"] = $arr_timings1[$t]["morning_end_time"];

								$arr_mainEn_time[] = $arr_timings1[$t]["morning_end_time"];
							}
						}else{
							$ed = explode(" ", $arr_timings1[$t]["morning_end_time"]);
							$arr_prv_st_en_time[$pro_id]["ed"] = $ed[1];
							$arr_prv_st_en_time[$pro_id]["ed_match"] = $arr_timings1[$t]["morning_end_time"];
							$arr_mainEn_time[] = $arr_timings1[$t]["morning_end_time"];
						}
					}
				}
			}
		}
		//print_r($arr_mainSt_time);
		//print_r($arr_mainEn_time);
		$arr_office_time = array();
		$working_minutes = 0;
		if(count($arr_mainSt_time) > 0){
			list($mainStDt, $mainStTm) = explode(" ", min($arr_mainSt_time));
			list($mainEdDt, $mainEdTm) = explode(" ", max($arr_mainEn_time));
			//echo $mainStDt.", ".$mainStTm."  ".$mainEdDt.", ".$mainEdTm;
			$st_time = substr($mainStTm, 0, 5);
			$en_time = substr($mainEdTm, 0, 5);
			$arr_start_time = explode(":", $st_time);
			$start_hr = $arr_start_time[0];
			$start_hr = $start_hr < 10 ? '0'.(int)$start_hr : $start_hr;
			$start_mn = $arr_start_time[1];
			$start_mn = $start_mn < 10 ? '0'.(int)$start_mn : $start_mn;
			$start_slot_hr = $start_hr;
			$start_slot_mn = $start_mn;
			$str_start_time = $start_slot_hr.":".$start_mn;
			$arr_end_time = explode(":", $en_time);
			$end_hr = $arr_end_time[0];
			$end_hr = $end_hr < 10 ? '0'.(int)$end_hr : $end_hr;
			$end_mn = $arr_end_time[1];
			$end_mn = $end_mn < 10 ? '0'.(int)$end_mn : $end_mn;
			$str_end_time = $end_hr.":".$end_mn;			
			// GET OFFICE START AND CLOSE TIME
			$end_hr2 = $end_hr;
			$office_end_time = $end_hr2.":".$end_mn;
			$arr_office_time['st']= $str_start_time;
			$arr_office_time['et']= $office_end_time;	
			
			$arr_end_time[2]=(isset($arr_end_time[2]))?$arr_end_time[2]:00;
			$arr_start_time[2]=(isset($arr_start_time[2]))?$arr_start_time[2]:00;
			
			$working_minutes = (mktime($arr_end_time[0], $arr_end_time[1], $arr_end_time[2]) - mktime($arr_start_time[0], $arr_start_time[1], $arr_start_time[2])) / 60;
		}
		
		//getting notes status
		$arr_nts = array();
		$arr_nts_match = array();
		$prov_id = "('".implode("','", $arr_prov_id)."')";
		$str_nts = "SELECT COUNT(*) as rowCount, provider_id, facility_id FROM provider_notes WHERE provider_id IN ".$prov_id." AND delete_status = '0' and notes_date = '".$working_day_dt."' group by provider_id";
		$res_nts = imw_query($str_nts);
		if(imw_num_rows($res_nts) > 0){
			while($arr_nts=imw_fetch_assoc($res_nts)){
				$arr_nts_match[$arr_nts["provider_id"]] = $arr_nts["rowCount"];
			}
		}
		
		//lunch timings
		$arr_lunch_raw =  array();
		$arr_lunch_match = array();
		$qry_lunch = "select id, fldLunchStTm, fldLunchEdTm, TIME_FORMAT(morning_end_time, '%H:%i') morning_end_time, TIME_FORMAT(morning_start_time, '%H:%i') morning_start_time from schedule_templates st WHERE id IN ".$str_sch_tmp_id;
		$res_lunch = imw_query($qry_lunch);
		if(imw_num_rows($res_lunch) > 0){
			while($arr_lunch_raw=imw_fetch_assoc($res_lunch)){
				$arr_lunch_match[$arr_lunch_raw["id"]] = $arr_lunch_raw;
			}
		}
		
		//template labels
		$arr_label_raw =  array();
		$qry_label = "select sch_template_id, template_label, start_time, label_type, label_color, label_group from schedule_label_tbl where sch_template_id in ".$str_sch_tmp_id;
		$res_label = imw_query($qry_label);
		if(imw_num_rows($res_label) > 0){
			while($res_label_raw=imw_fetch_assoc($res_label)){
				$tmp_id=$res_label_raw['sch_template_id'];
				$stime=strtotime($res_label_raw['start_time']);
				$arr_template_lbl[$tmp_id][$stime] = $res_label_raw;
			}
		}
		//getting block timings
		$arr_blk_match = array();
		$qry_blk = "select b_desc, id, time_status, start_time, end_time, facility, provider from block_times where start_date = '".$working_day_dt."'";
		$res_blk = imw_query($qry_blk);
		if(imw_num_rows($res_blk) > 0){
			while($arr_blk_raw=imw_fetch_assoc($res_blk)){
				$arr_blk_match[$arr_blk_raw["provider"]."-".$arr_blk_raw["facility"]][] = $arr_blk_raw;
			}
		}

		//getting facility name
		$arr_fac_match = array();
		$qry_fac = "select id, name from facility";
		$res_fac = imw_query($qry_fac);
		if(imw_num_rows($res_fac) > 0){
			while($arr_fac_raw = imw_fetch_assoc($res_fac)){
				$arr_fac_match[$arr_fac_raw["id"]] = $arr_fac_raw["name"];
			}
		}
		
		//creating cache data provider wise for each facility
		$return_array = array();
		$return_order = array();
		$fac_order = array();
		if(is_array($arr_schedule) && count($arr_schedule) > 0){
			foreach($arr_schedule as $col => $val){
				$return_order[] = $val["id"];
				if(in_array($val["id"], $arr_prov_id)){
					$this_prov_id = $val["id"];
					$existing_file = $dir_path."/load_xml/".$working_day_dt."-".$this_prov_id.".sch";
					$file_exists = false;
					if(file_exists($existing_file)){
						$file_exists = true;
					}
					if($forcefully == true || $file_exists == false){
						
						$return_array = array();
						$return_array["dt"] = $working_day_dt;
						$return_array[$this_prov_id]["id"] = $this_prov_id;
						$return_array[$this_prov_id]["dt"] = $working_day_dt;
						$return_array[$this_prov_id]["name"] = core_name_format($val["lname"], $val["fname"],$val["mname"]);
						$return_array[$this_prov_id]["hover_name"] = core_name_format($val["lname"], $val["fname"])." ".$val["mname"];
						$return_array[$this_prov_id]["color"] = $val["provider_color"];
						$return_array[$this_prov_id]["type"] = $val["user_type"];
						$return_array[$this_prov_id]["max_appoint"] = $val["max_appoint"];
						//$return_array[$this_prov_id]["fac_id"] = $this_fac_id;
						//$return_array[$this_prov_id]["fac_name"] = $arr_fac_match[$this_fac_id];						
						$return_array[$this_prov_id]["fac_id"] ='';
						$return_array[$this_prov_id]["fac_name"] = '';
							
						if(is_array($val["sch"]) && count($val["sch"]) > 0){
							$int_sch_cnt = 0;
							$tmp_storage = array();
							foreach($val["sch"] as $scol => $sval){								
								$this_fac_id = $sval["facility"];
								$this_tmp_id = $sval["sch_tmp_id"];								
								$fac_order[] = $this_fac_id;								
								$notes_flag = 0;
								if(isset($arr_nts_match[$this_prov_id])){								
									$notes_flag = $arr_nts_match[$this_prov_id];
								}
								$return_array[$this_prov_id]["notes"] = $notes_flag;
								$return_array[$this_prov_id]["fac_ids"] = $this_fac_id;								
								//reset timings
								$start_slot_hr = $start_hr;
								$start_slot_mn = $start_mn;								
								$arr_old_fac = array();								
								for($w = 0; $w < $working_minutes; $w = $w + DEFAULT_TIME_SLOT){									
									//adjusting start time
									if($start_slot_mn >= 60){
										$start_slot_hr++;
										$start_slot_mn = $start_slot_mn - 60;
									}
									//calculating end time
									$end_slot_hr = $start_slot_hr;
									$end_slot_mn = $start_slot_mn + DEFAULT_TIME_SLOT;
									//adjusting end time
									if($end_slot_mn >= 60){
										$end_slot_hr++;
										$end_slot_mn = $end_slot_mn - 60;
									}									
									//preceding zero
									$start_slot_hr = $start_slot_hr < 10 ? '0'.(int)$start_slot_hr : $start_slot_hr;
									$start_slot_mn = $start_slot_mn < 10 ? '0'.(int)$start_slot_mn : $start_slot_mn;
									$end_slot_hr = $end_slot_hr < 10 ? '0'.(int)$end_slot_hr : $end_slot_hr;
									$end_slot_mn = $end_slot_mn < 10 ? '0'.(int)$end_slot_mn : $end_slot_mn;
									$timing = $start_slot_hr.":".$start_slot_mn."-".$end_slot_hr.":".$end_slot_mn;
									$times_from = $start_slot_hr.":".$start_slot_mn.":00";
									//getting slot specific information									
									$arr_lunch =  array();
									if(isset($arr_lunch_match[$sval["sch_tmp_id"]])){
										$arr_lunch = $arr_lunch_match[$sval["sch_tmp_id"]];
									}									
									#PHYSICIAN IN OFFICE STATUS
									$status = "off";
									$slot_color = DEFAULT_OFFICE_CLOSED_COLOR;
									if(strtotime($arr_lunch["morning_start_time"]) <= strtotime($times_from) && strtotime($arr_lunch["morning_end_time"]) > strtotime($times_from)){
										$status = "on";
										$slot_color = $val["provider_color"];
									}
	
									#CHECKING BLOCK/OPEN TIME STATUS
									$label = "";
									$label_type = "";
									if(isset($arr_blk_match[$val["id"]."-".$sval["facility"]])){
										for($blk_mth = 0; $blk_mth < count($arr_blk_match[$val["id"]."-".$sval["facility"]]); $blk_mth++){
											$arr_blk = $arr_blk_match[$val["id"]."-".$sval["facility"]][$blk_mth];
											$blk_checked = false;
											
											if(strtotime($times_from) >= strtotime($arr_blk["start_time"]) && strtotime($times_from) < strtotime($arr_blk["end_time"]) && strtotime($times_from) >= strtotime($arr_lunch[morning_start_time]) && strtotime($times_from) < strtotime($arr_lunch[morning_end_time])){
												if($arr_blk["time_status"] == "block"){													
													$status = "block";
													$slot_color = "#000000";
													$label = "Blocked Time";
													$blk_checked = true;
												}else if($arr_blk["time_status"] == "lock"){
													$status = "lock";
													$slot_color = "#999999";
													$label = "Locked Time";
													$blk_checked = true;
												}else if($arr_blk["time_status"] == "open"){
													$status = "on";
													$slot_color = $val["provider_color"];
													$blk_checked = true;
												}
												if(trim($arr_blk["b_desc"]) != "" && $blk_checked == true){
													$label = $arr_blk["b_desc"];
												}
												if($blk_checked == true){
													break;
												}
											}
										}
									}
									#GETTING ADDED LABELS IF THE SLOT IS NOT BLOCKED									
									if($label == "" && sizeof($arr_template_lbl) > 0 && $arr_template_lbl[$sval["sch_tmp_id"]][strtotime($times_from)]){	
										//pre($arr_template_lbl);die();
										$label = $label_type = $l_text = $label_group = '';
										$arr_label_raw=$arr_template_lbl[$sval["sch_tmp_id"]][strtotime($times_from)];						
										if($arr_label_raw["sch_template_id"] == $sval["sch_tmp_id"] && strtotime($arr_label_raw["start_time"]) == strtotime($times_from)){
											$label = $arr_label_raw["template_label"];
											$slot_color = (trim($arr_label_raw["label_color"]) != "") ? $arr_label_raw["label_color"] : $slot_color;
											$label_type = $arr_label_raw["label_type"];
											$l_text = $arr_label_raw["template_label"];
											$label_group = $arr_label_raw["label_group"];
										}
									}
									#OVERWRITING LABEL IF LUNCH TIME
									if(strtotime($arr_lunch["fldLunchStTm"]) <= strtotime($times_from) && strtotime($arr_lunch["fldLunchEdTm"]) > strtotime($times_from)){
										$label = "Lunch";
										$label_type = "Lunch";
									}
									$this_slot_id = $timing;									
									if(isset($tmp_storage) && count($tmp_storage) > 0){
										if(!in_array($this_fac_id, $arr_old_fac)){
											$return_array[$this_prov_id]["fac_ids"] = $tmp_storage["fac_ids"].",".$this_fac_id;
										}
										if($this_slot_id == $tmp_storage["slots"][$this_slot_id]["id"]){
											if($tmp_storage["slots"][$this_slot_id]["status"] == "off"){
												$return_array[$this_prov_id]["slots"][$this_slot_id]["id"] = $this_slot_id;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["timing"] = $timing;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["status"] = $status;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["color"] = $slot_color;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["label"] = $label;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["l_text"] = $l_text;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["label_type"] = $label_type;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["label_group"] = $label_group;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["tmpId"] = $sval["sch_tmp_id"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["tmp_start_time"] = core_time_format($arr_lunch["morning_start_time"]);
												$return_array[$this_prov_id]["slots"][$this_slot_id]["tmp_end_time"] = core_time_format($arr_lunch["morning_end_time"]);
												$return_array[$this_prov_id]["slots"][$this_slot_id]["fac_id"] = $this_fac_id;
												$return_array[$this_prov_id]["slots"][$this_slot_id]["fac_name"] = $arr_fac_match[$this_fac_id];										
												$return_array[$this_prov_id]["slots"][$this_slot_id]["entry"] = "overwritten";
											}else{												
												$return_array[$this_prov_id]["slots"][$this_slot_id]["id"] = $tmp_storage["slots"][$this_slot_id]["id"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["timing"] = $tmp_storage["slots"][$this_slot_id]["timing"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["status"] = $tmp_storage["slots"][$this_slot_id]["status"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["color"] = $tmp_storage["slots"][$this_slot_id]["color"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["l_text"] = $tmp_storage["slots"][$this_slot_id]["l_text"];

												$return_array[$this_prov_id]["slots"][$this_slot_id]["label"] = $tmp_storage["slots"][$this_slot_id]["label"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["label_type"] = $tmp_storage["slots"][$this_slot_id]["label_type"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["label_group"] = $tmp_storage["slots"][$this_slot_id]["label_group"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["tmpId"] = $tmp_storage["slots"][$this_slot_id]["tmpId"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["tmp_start_time"] = $tmp_storage["slots"][$this_slot_id]["tmp_start_time"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["tmp_end_time"] = $tmp_storage["slots"][$this_slot_id]["tmp_end_time"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["fac_id"] = $tmp_storage["slots"][$this_slot_id]["fac_id"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["fac_name"] = $tmp_storage["slots"][$this_slot_id]["fac_name"];
												$return_array[$this_prov_id]["slots"][$this_slot_id]["entry"] = "fresh";
											}
										}
									}else{										
										$return_array[$this_prov_id]["slots"][$this_slot_id]["id"] = $this_slot_id;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["timing"] = $timing;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["status"] = $status;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["color"] = $slot_color;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["label"] = $label;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["l_text"] = $l_text;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["label_type"] = $label_type;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["label_group"] = $label_group;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["tmpId"] = $sval["sch_tmp_id"];
										$return_array[$this_prov_id]["slots"][$this_slot_id]["tmp_start_time"] = core_time_format($arr_lunch["morning_start_time"]);
										$return_array[$this_prov_id]["slots"][$this_slot_id]["tmp_end_time"] = core_time_format($arr_lunch["morning_end_time"]);
										$return_array[$this_prov_id]["slots"][$this_slot_id]["fac_id"] = $this_fac_id;
										$return_array[$this_prov_id]["slots"][$this_slot_id]["fac_name"] = $arr_fac_match[$this_fac_id];
										$return_array[$this_prov_id]["slots"][$this_slot_id]["entry"] = "fresh";									
									}
									//increment start time
									$start_slot_mn = $start_slot_mn + DEFAULT_TIME_SLOT;
									array_push($arr_old_fac, $this_fac_id);								
								}								
								$int_sch_cnt++;
								$tmp_storage = $return_array[$this_prov_id];								
							}
						}
						
						$wd = $return_array[$this_prov_id]['dt'];
						$tmp_max_appointments_arr = array();
						$sch_tmp_avail_ids_us = array();
						$sch_tmp_avail_ids_us_for_prov = array();
						$prov_sch_ids_data_arr_us = $this->get_provider_schedules($wd,array(0=>$this_prov_id));
						foreach($prov_sch_ids_data_arr_us as $target_prov_sch_us)
						{
							if(isset($target_prov_sch_us['sch_tmp_id']) && trim($target_prov_sch_us['sch_tmp_id']) != "")
							{
								$sch_tmp_avail_ids_us[$target_prov_sch_us['facility']][] = $target_prov_sch_us['sch_tmp_id'];
								$sch_tmp_avail_ids_us_for_prov[] = $target_prov_sch_us['sch_tmp_id'];
							}							
						}
						
						foreach($sch_tmp_avail_ids_us as $selected_fac_us => $sch_tmp_avail_ids_by_fac)
						{
							$sch_temp_ids_for_req = implode(',',$sch_tmp_avail_ids_by_fac);
							$max_appt_qry = "SELECT SUM(schedule_templates.MaxAppointments) as max_appts, SUM(schedule_templates.MinAppointments) as min_appts, AVG(IF(`warning_percentage`>0, warning_percentage, NULL)) as max_war FROM schedule_templates WHERE id IN(".$sch_temp_ids_for_req.")";
							$max_appt_qry_obj = imw_query($max_appt_qry);
							$max_appt_qry_obj_result = imw_fetch_assoc($max_appt_qry_obj);							
							$tmp_max_appointments_arr[$selected_fac_us]['max_appts'] = $max_appt_qry_obj_result['max_appts'];
							$tmp_max_appointments_arr[$selected_fac_us]['min_appts'] = $max_appt_qry_obj_result['min_appts'];
							$tmp_max_appointments_arr[$selected_fac_us]['max_war'] = $max_appt_qry_obj_result['max_war'];					 
						}
						
						$sch_tmp_avail_ids_us_for_prov = array_unique($sch_tmp_avail_ids_us_for_prov);
						$return_array[$this_prov_id]["template_ids"]= implode(',',$sch_tmp_avail_ids_us_for_prov);
						$return_array[$this_prov_id]["fac_max_appts"]= $tmp_max_appointments_arr;
						//deleting off slot hours from cache
						$set_slots = $return_array[$this_prov_id]["slots"];
						$set_slots = array_values($set_slots);
						for($sscnt = count($set_slots)-1; $sscnt > 0; $sscnt--){
							if($set_slots[$sscnt]["status"] == "off"){
								unset($return_array[$this_prov_id]["slots"][$set_slots[$sscnt]["id"]]);
							}else{
								break;
							}
						}
						for($sscnt = 0; $sscnt < count($set_slots); $sscnt++){
							if($set_slots[$sscnt]["status"] == "off"){
								unset($return_array[$this_prov_id]["slots"][$set_slots[$sscnt]["id"]]);
							}else{
								break;
							}
						}
						//check  is scheduler_common dir exist
						if( !is_dir($dir_path) ){
							mkdir( $dir_path, 0755, true );
							chown( $dir_path, 'apache' );
						}
						//check  is load_xml dir exist
						if( !is_dir($dir_path."/load_xml") ){
							mkdir( $dir_path."/load_xml", 0755, true );
							chown( $dir_path."/load_xml", 'apache' );
						}
						
						$content = serialize($return_array);
						//creating file cache
						$file_name = $dir_path."/load_xml/".$working_day_dt."-".$this_prov_id.".sch";
						if(file_exists($file_name)){
							unlink($file_name);
						}
						file_put_contents($file_name, $content);
					}else{
						if(is_array($val["sch"]) && count($val["sch"]) > 0){
							foreach($val["sch"] as $scol => $sval){
								$this_fac_id = $sval["facility"];
								$fac_order[] = $this_fac_id;
							}
						}
					}
				}
			}
		}	
		
		//check  is scheduler_common dir exist
		if( !is_dir($dir_path) ){
			mkdir( $dir_path, 0755, true );
			chown( $dir_path, 'apache' );
		}
		//check  is load_xml dir exist
		if( !is_dir($dir_path."/load_xml") ){
			mkdir( $dir_path."/load_xml", 0755, true );
			chown( $dir_path."/load_xml", 'apache' );
		}
		
		//creating file cache
		$file_name = $dir_path."/load_xml/".$working_day_dt."-order.sch";
		if(file_exists($file_name)){
			unlink($file_name);
		}
			
		$content = serialize($return_order)."~~~~~".serialize($fac_order)."~~~~~".serialize($arr_prv_st_en_time)."~~~~~".serialize($arr_office_time);
		file_put_contents($file_name, $content);
	}
	/*
	Function: get_provider_schedules
	Purpose: This function gets all the active schedules of the given provider if any else for all providers for the given date
	Author: ravi, prabh
	Arguments: accepts Date in Y-m-d format only
	Returns: ARRAY
	*/
	function get_provider_schedules($wd, $ap = array(), $arrFacility = array()){
		//get list of child template for current date
		$qStr="select pid, sch_tmp_id, sch_tmp_pid from provider_schedule_tmp_child 
				WHERE status=1 
				AND IF(UNIX_TIMESTAMP(start_date) != 0, '$wd' BETWEEN start_date AND end_date, 1=1)";
		$query=imw_query($qStr);
		while($data=imw_fetch_object($query))
		{
			$childTemplate[$data->pid]=$data->sch_tmp_id;
		}
		
		//variable declarations

		$pr = false;	$wno = $dno = 0;	$ar_wd = $arr_sch = $arr_del_sch = $arr_sch_tmp = $arr_sch2 = array();	$q = $r = $str_sch = "";

		//selected provider
		if(count($ap) > 0){	$pr = "(".implode(",", $ap).")";	}

		//selected facility
		$strFacility='';
		if(count($arrFacility) > 0){ $strFacility = implode(",", $arrFacility);	}

		//calculating week day no and week no
		$ar_wd = explode("-", $wd);	$wno = ceil($ar_wd[2] / 7);	$dno = date("w", mktime(0, 0, 0, $ar_wd[1], $ar_wd[2], $ar_wd[0]));	if($dno == 0) $dno = 7;
		
		$i=0;
		//quering provider schedules
		$q = "select id, del_status, delete_row, status, provider, facility, today_date, sch_tmp_id from provider_schedule_tmp where today_date <= '".$wd."' and week".$wno." = '".$dno."' ";
		if($pr != false){	$q .= " and provider IN ".$pr." ";	}
		if(empty($strFacility)==false){ $q .= " and facility IN (".$strFacility.") ";}
		$q .= "order by provider, facility, sch_tmp_id, id";//, today_date
		$r = imw_query($q);
		if(imw_num_rows($r)> 0){
			while($arr_sch_tmp1 = imw_fetch_assoc($r)){
				$arr_sch[]=$arr_sch_tmp1;
			}
		}
		
		$arr_sch_tmp = $arr_sch;
		for($i = 0; $i < count($arr_sch_tmp); $i++){
			//removing deleted schedules
			if($arr_sch_tmp[$i]["del_status"] == 1){
				$arr_del_sch[] = $arr_sch_tmp[$i];
				unset($arr_sch[$i]);
			}
		}
		
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);
		
		//removing shcedules which have been deleted for future
		$arr_sch_tmp = $arr_sch;
		if(count($arr_del_sch)>0){
			for($j = 0; $j < count($arr_del_sch); $j++){
				for($k = 0; $k < count($arr_sch_tmp); $k++){
					if(strtolower($arr_del_sch[$j]["delete_row"]) == "all"){
						if($arr_del_sch[$j]["provider"] == $arr_sch_tmp[$k]["provider"] && $arr_del_sch[$j]["facility"] == $arr_sch_tmp[$k]["facility"] && $arr_del_sch[$j]["sch_tmp_id"] == $arr_sch_tmp[$k]["sch_tmp_id"] && strtotime($arr_del_sch[$j]["today_date"]) >= strtotime($arr_sch_tmp[$k]["today_date"])){							
							unset($arr_sch[$k]);
						}
					}
					if(strtolower($arr_del_sch[$j]["delete_row"]) == "no"){
						if($arr_del_sch[$j]["provider"] == $arr_sch_tmp[$k]["provider"] && $arr_del_sch[$j]["facility"] == $arr_sch_tmp[$k]["facility"] && $arr_del_sch[$j]["sch_tmp_id"] == $arr_sch_tmp[$k]["sch_tmp_id"] && strtotime($arr_del_sch[$j]["today_date"]) == strtotime($wd)){							
							unset($arr_sch[$k]);
						}
					}
				}
			}
		}
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);

		//removing schedules which were created for a single day earlier than the sought date
		$arr_sch_tmp = $arr_sch;
		if(count($arr_sch_tmp)>0){	
			for($i = 0; $i < count($arr_sch_tmp); $i++){
				if(strtotime($arr_sch_tmp[$i]["today_date"]) < strtotime($wd) && strtolower($arr_sch_tmp[$i]["status"]) == "no"){
					$arr_del_sch[] = $arr_sch_tmp[$i];					
					unset($arr_sch[$i]);
				}
			}
		}
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);
		
		//removing duplicate records if any
		if(count($arr_sch)>0){
			$arr_sch_tmp = array();	//resetting array
			for($i = 0; $i < count($arr_sch); $i++){
				$arr_sch_tmp[] = $arr_sch[$i]["id"];
			}
			$str_sch = join(',', $arr_sch_tmp);
			$q = "select id, facility , provider, sch_tmp_id, today_date, iportal_enable_slot from provider_schedule_tmp where id in (".$str_sch.") ";
			if($pr != false){	$q .= "and provider IN ".$pr." ";	}
			if(empty($strFacility)==false){ $q .= " and facility IN (".$strFacility.") ";	 }
			$q .= "order by provider, facility, sch_tmp_id, id";//, pst.today_date
			$r = imw_query($q);
			if(imw_num_rows($r) > 0){
				
				while($arr_sch2_tmp = imw_fetch_assoc($r)){
					$arr_sch2[] = $arr_sch2_tmp;
				}
				$arr_sch = $arr_sch2;
				for($n = 0; $n < count($arr_sch2); $n++){
					if(isset($arr_sch2[$n]) && isset($arr_sch2[$n+1])){
						if($arr_sch2[$n]['sch_tmp_id'] == $arr_sch2[$n+1]['sch_tmp_id'] && $arr_sch2[$n]['facility'] == $arr_sch2[$n+1]['facility'] && $arr_sch2[$n]['provider'] == $arr_sch2[$n+1]['provider']){
							//$arr_del_sch[] = $arr_sch2[$n];
							unset($arr_sch[$n]);
						}
					}
				}
			}			
		
		}
		
		//additional check to remove duplicate
		if(isset($arr_sch)){
			$arr_sch3 = array_values($arr_sch);
			$provider1 = 0;
			$facility1 = 0;
			$sch_tmp_id1 = 0;			
			for($i=0;$i<count($arr_sch3);$i++){
				$provider = $arr_sch3[$i]['provider'];
				$facility = $arr_sch3[$i]['facility'];
				$sch_tmp_id = $arr_sch3[$i]['sch_tmp_id'];
				$id = $arr_sch3[$i]['id'];
				
				$provider1 = $arr_sch3[$i+1]['provider'];
				$facility1 = $arr_sch3[$i+1]['facility'];
				$sch_tmp_id1 = $arr_sch3[$i+1]['sch_tmp_id'];
				if($provider == $provider1 && $facility == $facility1 && $sch_tmp_id == $sch_tmp_id1){
					unset($arr_sch[$i]);
				}
			}
			unset($provider1,$facility1, $sch_tmp_id1, $arr_sch3);
			//$arr_sch = $arr_sch3;
		}
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		//if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);

		//unsetting variables
		unset($pr, $wno, $dno, $ar_wd, $arr_del_sch, $arr_sch_tmp, $arr_sch2, $arr_sch3, $q, $r, $str_sch);
		
		if(isset($arr_sch)){
			foreach($arr_sch as $key=>$arr)
			{
				if(isset($childTemplate[$arr['id']]))
				{
					$new_arr_tmp['id'] 			=$arr['id'];
					$new_arr_tmp['facility'] 	=$arr['facility'];
					$new_arr_tmp['provider'] 	=$arr['provider'];
					$new_arr_tmp['sch_tmp_id'] 	=$childTemplate[$arr['id']];//parent temp id swaped with child temp id
					$new_arr_tmp['today_date'] 	=$arr['today_date'];
					$new_arr_tmp['iportal_enable_slot'] =$arr['iportal_enable_slot']; 
				}
				else
				{
					$new_arr_tmp['id'] 			=$arr['id'];
					$new_arr_tmp['facility'] 	=$arr['facility'];
					$new_arr_tmp['provider'] 	=$arr['provider'];
					$new_arr_tmp['sch_tmp_id'] 	=$arr['sch_tmp_id'];//parent temp id swaped with child temp id
					$new_arr_tmp['today_date'] 	=$arr['today_date'];
					$new_arr_tmp['iportal_enable_slot'] =$arr['iportal_enable_slot']; 
				}
				$new_arr[]=$new_arr_tmp;
			}
		}
		if(isset($new_arr) && count($new_arr)>0) $arr_sch = $new_arr;
		return $arr_sch;
	}
	
	/*
	Function: get_scroll_settings
	Purpose: This function retrieves the last appt accessed
	Author: ravi, prabh
	Returns: ARRAY with SCROLL_ID, SCROLL_TIME and SCROLL_DATE, if found else returns false
	*/
	function get_scroll_settings($uid, $sch_id = 0){
		$return  = false;
		$sql = "select current_time_id, sch_id, sa.sa_doctor_id, sa.id, sa.sa_app_starttime, sa.sa_app_start_date from current_time_locator left join schedule_appointments sa on sa.id = current_time_locator.sch_id where uid = '".$uid."'";
		if($sch_id > 0){
			$sql .= " and sch_id = '".$sch_id."' ";
		}
		$sql .= " order by current_time_id desc limit 0,1";
		$res = imw_query($sql);
		if(imw_num_rows($res)> 0){
			$arr = imw_fetch_assoc($res);
			$return_arr = array();
			if(!empty($arr["sa_app_starttime"]) && !empty($arr["sa_app_start_date"])){
				$return_arr["SCROLL_ID"] = $arr["current_time_id"];
				$return_arr["SCROLL_TIME"] = $arr["sa_app_starttime"];
				$return_arr["SCROLL_DATE"] = $arr["sa_app_start_date"];
				$return_arr["SCROLL_PROV_ID"] = $arr["sa_doctor_id"];
				$return = $return_arr;
			}
		}
		return $return;
	}
	/*
	Function: set_scroll_settings
	Purpose: This function set the last appt accessed data
	Author: ravi, prabh
	Arguments: Appointment Id and User Id
	Returns: NULL
	*/
	function set_scroll_settings($appt_id, $uid){
		$arr_scroll = $this->get_scroll_settings($uid);
		if($arr_scroll !== false){
			$sql_pfx = "UPDATE current_time_locator SET ";
			$sql_sfx = "WHERE current_time_id = '".$arr_scroll["SCROLL_ID"]."'";
		}else{
			$sql_pfx = "INSERT INTO current_time_locator SET ";
			$sql_sfx = "";
		}
		$sql = $sql_pfx;
		$sql .= " sch_id = '".$appt_id."', uid = '".$uid."', `dated`='".date('Y-m-d')."' ";
		$sql .= $sql_sfx;
		imw_query($sql);
	}
	
	/*
	Function: read_prov_working_hrs
	Purpose: This reads provider schedules from the cached files
	Author: ravi, prabh
	*/
	function read_prov_working_hrs($working_day_dt, $arr_prov_id = array(), $dir_path = ""){
	if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common";
		$order_file = $dir_path."/load_xml/".$working_day_dt."-order.sch";
		$arr_xml = false;
		if(file_exists($order_file)){
			$str_tmp_order = file_get_contents($order_file);
			list($str_order, $str_fac, $str_prov_sch_timings) = explode("~~~~~", $str_tmp_order);
			$arr_order = unserialize($str_order);
			$arr_prov_sch_timings = unserialize($str_prov_sch_timings);	
			$max_time = 0;
			$min_time = mktime(23, 59, 59, date("m"), date("d"), date("Y")); 
			if(is_array($arr_prov_sch_timings) && count($arr_prov_sch_timings) > 0){
				foreach($arr_prov_sch_timings as $provId => $provDet){
					if($min_time > strtotime($provDet["st"])){
						$min_time = strtotime($provDet["st"]);
					}
					if($max_time < strtotime($provDet["ed"])){
						$max_time = strtotime($provDet["ed"]);
					}
				}
				$max_time = $max_time + 3600;
				$min_time = $min_time - 3600;
			}
			if(is_array($arr_order) && count($arr_order) > 0){
				$int_frag_cnt = 0;
				foreach($arr_order as $prov_id){
					if(in_array($prov_id, $arr_prov_id)){
						$file_name = $dir_path."/load_xml/".$working_day_dt."-".$prov_id.".sch";
						$str_fragment = file_get_contents($file_name);
						$arr_fragment = unserialize($str_fragment);
						if($int_frag_cnt == 0){
							$arr_xml = $arr_fragment;
						}else{
							$arr_xml[$prov_id] = $arr_fragment[$prov_id];
						}
						$int_frag_cnt++;
					}
				}
			}
			/*print "<textarea>";
			print_r($arr_xml);
			print "</textarea>";*/
			$arr_final_xml = array();
			if(is_array($arr_xml) && count($arr_xml) > 0){
				foreach($arr_xml as $provId => $fragment){
					if($provId != "dt"){
						$arr_final_xml[$provId] = $fragment;
						$new_slots = array();
						//padding at start
						$loop_timings = "";
						$loop_timings = $min_time;
						if($loop_timings != ""){
							while($loop_timings < strtotime($arr_prov_sch_timings[$provId]["st"])){
								$loop_hr_mn = date("H:i", $loop_timings);
								list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
								if($loop_mn == 60){
									$loop_mn = 0;
									$loop_hr++;
								}
								$ed_loop_hr = $loop_hr;
								$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
								if($ed_loop_mn == 60){
									$ed_loop_mn = 0;
									$ed_loop_hr++;
								}								
								$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
								$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
								$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
								$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
								$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;								
								$new_slots[$this_time] = array(
									"id" => $this_time,
									"timing" => $this_time,
									"status" => "off",
									"color" => DEFAULT_OFFICE_CLOSED_COLOR,
									"label" => "",
									"label_type" => "",
									"l_text" => "",
									"tmpId" => 0,
									"tmp_start_time" => "",
									"tmp_end_time" => "",
									"fac_id" => 0,
									"fac_name" => "",
									"entry" => "fresh"
								);							
								$loop_timings += (DEFAULT_TIME_SLOT * 60);
							}
						}
						//shifting existing time slots as it is
						foreach($fragment["slots"] as $timings => $details){
							$new_slots[$timings] = $details;
						}
						//padding at end
						$loop_timings = "";
						$loop_timings = strtotime($arr_prov_sch_timings[$provId]["ed"]);
						if($loop_timings != ""){
							while($loop_timings < $max_time){ 
								$loop_hr_mn = date("H:i", $loop_timings);
								list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);
								if($loop_mn == 60){
									$loop_mn = 0;
									$loop_hr++;
								}
								$ed_loop_hr = $loop_hr;
								$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
								if($ed_loop_mn == 60){
									$ed_loop_mn = 0;
									$ed_loop_hr++;
								}								
								$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
								$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;
								$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
								$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
								$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;							
								$new_slots[$this_time] = array(
									"id" => $this_time,
									"timing" => $this_time,
									"status" => "off",
									"color" => DEFAULT_OFFICE_CLOSED_COLOR,
									"label" => "",
									"label_type" => "",
									"l_text" => "",
									"tmpId" => 0,
									"tmp_start_time" => "",
									"tmp_end_time" => "",
									"fac_id" => 0,
									"fac_name" => "",
									"entry" => "fresh"
								);								
								$loop_timings += (DEFAULT_TIME_SLOT * 60);
							}	
						}
						$arr_final_xml[$provId]["slots"] = $new_slots;
					}
				}
				$arr_final_xml["dt"] = $arr_xml["dt"];
			}
		}
		return $arr_final_xml;
	}
	
	/*
	Function: write_html_content
	Purpose: writes html from the array for appt template, sch_type may be "main" or "physician"
	Author: ravi, prabh
	Returns: ARRAY containing Headings, Appt Time Slots, And Time Pane
	*/
	function write_html_content($arr_xml, $arr_sel_fac = array(), $arr_sel_prov = array(), $admin_priv = 0, $time_array, $sch_type = "main", $sch_overrider_privilege=0){
		$str_header = "";
		$str_time_pane = "";
		$str_appt_slots = "";
		$str_proc_summary = "";
		$arr_processed = array();
		$arr_not_processed = array();		
		if(is_array($arr_xml) && count($arr_xml) > 0 && is_array($arr_sel_fac) && count($arr_sel_fac) > 0 && is_array($arr_sel_prov) && count($arr_sel_prov) > 0){
			//height and rowspan for time pane slot
			$tp_sl_height = (12 * (DEFAULT_TIME_SLOT / 5)) + 20;
			$tp_row_span = 60 / DEFAULT_TIME_SLOT;
			$up_height = ((DEFAULT_TIME_SLOT / 5) * 11) + 20;
			$eff_date_add_sch = core_date_format($arr_xml["dt"], "m-d-Y");
			//commenting this because we didn't find any declartion of core_date_format constant
			//$div_slot_height = (DEFAULT_TIME_SLOT / 5) * 11;//this need to change in sc_script.js too
			
			if(DEFAULT_TIME_SLOT == 15){
				$tp_sl_height = round($tp_sl_height * 85 / 100);
				$up_height = round($up_height * 85 / 100);
				$div_slot_height = round($div_slot_height * 85 / 100);
			}			
			//procedure site conversion array
			$arr_proc_site = $this->eye_site('Info');
			//getting all procedure for day summary div
			$arr_all_proc = array();
			$all_proc_lbl = array();
			$sql_proc = "SELECT id, acronym, proc, proc_color FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 AND active_status!='del'";
			$res_proc = imw_query($sql_proc);
			if(imw_num_rows($res_proc) > 0){
				while($this_proc= imw_fetch_assoc($res_proc))
				{
					$arr_all_proc[$this_proc["id"]]["acronym"] = trim($this_proc["acronym"]);
					$arr_all_proc[$this_proc["id"]]["name"] = $this_proc["proc"];
					$arr_all_proc[$this_proc["id"]]["color"] = $this_proc["proc_color"];
					$all_proc_lbl[trim($this_proc["acronym"])] = $this_proc["proc_color"];
				}
			}
			//==================================================================//
			//===================Get Insurance_details================//
			$arr_ins_comp=array();
			if($arr_xml['dt']){
				$qry_ins="SELECT DISTINCT ins.type AS ins_type, ins.ins_caseid AS case_id, ins.pid AS patient_id, ic.name AS ins_name
				FROM insurance_data AS ins
				INNER JOIN insurance_companies AS ic ON ( ic.id = ins.provider ) 
				INNER JOIN schedule_appointments sa 
				ON ( sa.sa_patient_id = ins.pid
					AND sa.sa_app_start_date =  '".$arr_xml['dt']."'
					AND sa.sa_patient_app_status_id NOT IN ( 203, 201, 18, 19, 20 ) 
					)";
				
				$res_ins=imw_query($qry_ins);
				if(imw_num_rows($res_ins)>0){
					while($row_ins=imw_fetch_assoc($res_ins)){
						$ins_pid=$row_ins['patient_id'];
						$ins_type=$row_ins['ins_type'];
						$ins_name=$row_ins['ins_name'];
						$ins_case_id=$row_ins['case_id'];
						$arr_ins_comp[$ins_pid][$ins_case_id][$ins_type]=$ins_name;
					}
				}
			}
			//===============================================//
			//getting facility details
			$arr_all_fac = array();
			$sql_fac = "SELECT id, facility_color FROM facility";
			$res_fac = imw_query($sql_fac);
			if(imw_num_rows($res_fac) > 0){
				while($this_fac = imw_fetch_assoc($res_fac)){
						$arr_all_fac[$this_fac["id"]]["facility_color"] = $this_fac["facility_color"];
				}
			}
			//setting widths
			$arr_widths = $this->set_column_width($arr_xml, $arr_sel_fac, $arr_sel_prov);
			list($column_width, $innerContainer, $scroll_width, $div_width, $total_prov) = $arr_widths;
			$name_header_width = $column_width;
			$extra_padding = "";
			$phy_margin_left = "";
			$pos_abs = "position:absolute;";
			if($sch_type == "physician"){
				$column_width = 100;//415
				$innerContainer = 96;
				$scroll_width = 100;//420
				$div_width = 95;//390
				$name_header_width = 98;//500
				$phy_margin_left = "margin-left:2%;";
				$pos_abs = "";
			}
			if($name_header_width<33)
			$name_header_width=$name_header_width-0.10;
			else
			$name_header_width=$name_header_width-1;
			
			$prov_name_ln = 0;
			$column_width_minus=1;
			
			if($total_prov > 5){
				$prov_name_ln = 9;
				$column_width_minus=0.35;
			}elseif($total_prov == 5){
				$prov_name_ln = 9;
				$column_width_minus=1;
			}else if($total_prov == 4){
				$prov_name_ln = 15;
				$column_width_minus=1;
			}else if($total_prov == 3){
				$prov_name_ln = 15;
				$column_width_minus=1.5;
			}else if($total_prov == 2){
				$prov_name_ln = 20;
				$column_width_minus=2.5;
			}else if($total_prov == 1){
				$prov_name_ln = 100;
			}

			//calculating "on" slots
			$int_total_slots = 0;

			foreach($arr_xml as $pr_id => $pr_detail){
				if(isset($pr_detail["slots"])){
					foreach($pr_detail["slots"] as $sl_id => $sl_detail){
						//echo $sl_detail["status"];
						if($sl_detail["status"] == "on"){
							//echo "a";
							$int_total_slots++;
						}
					}
				}
			}
			
			$int_max_cnt_arr=$int_warn_percentage_arr=array();
			$arr_user_types=array();
			//create arry to get user max count for appt 
			$qry_u = "SELECT max_per, max_appoint, id, user_type FROM users";
			$res_u = imw_query($qry_u);
			if(imw_num_rows($res_u) > 0){
				while($arr_u = imw_fetch_assoc($res_u))
				{
					$int_max_cnt_arr[$arr_u["id"]] = (!empty($arr_u["max_appoint"])) ? $arr_u["max_appoint"] : 0;
					$int_warn_percentage_arr[$arr_u["id"]] = (!empty($arr_u["max_per"])) ? $arr_u["max_per"] : 100;

					//===================Get Facility Type Providers Array====================//
					if($arr_u['user_type']==22){
						$user_id=$arr_u["id"];
						$arr_user_types[$user_id]="1";	
					}
				}
			}
			
			$intCnt = 0;
			$intSCnt = 1;
			$arr_slot_content = array();
			foreach($arr_xml as $pr_id => $pr_detail){
				if($pr_id != "dt"){
					//determining whether to process or not
					$bl_process = false;
					if(count($arr_sel_fac) > 0){
						$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
						for($multi = 0; $multi < count($arr_multi_fac); $multi++){
							if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){
								if(count($arr_sel_prov) > 0){
									if(in_array($pr_detail["id"], $arr_sel_prov)){
										$bl_process = true;
										break;
									}
								}else{
									$bl_process = true;
									break;
								}
							}
						}
					}else{
						if(count($arr_sel_prov) > 0){
							if(in_array($pr_id, $arr_sel_prov)){
								$bl_process = true;
							}
						}else{
							$bl_process = true;
						}


					}					
					//getting facilites to process
					$str_process_fac = $pr_detail["fac_ids"];
					$arr_process_fac = explode(",", $pr_detail["fac_ids"]);					
					if(count($arr_sel_fac) > 0){
						$temp_arr_process_fac = $arr_process_fac;
						for($multip = 0; $multip < count($temp_arr_process_fac); $multip++){
							if(!in_array($temp_arr_process_fac[$multip], $arr_sel_fac)){
								unset($arr_process_fac[$multip]);
							}
						}
						$str_process_fac = implode(",", $arr_process_fac);
					}					
					if($bl_process == true){

						//getting allowed max count
						$int_max_cnt = 0;
						$int_warn_percentage = 100;
						if($int_warn_percentage_arr[$pr_id])
						{
							$int_max_cnt = $int_max_cnt_arr[$pr_id];
							$int_warn_percentage = $int_warn_percentage_arr[$pr_id];
						}

						##################################
						#WRITING HEADINGS AND DAY PROCEDURE SUMMARY
						##################################
						$tddblclick_todo = '';
						if($admin_priv == 1 || $sch_overrider_privilege == 1)
						{
							$tddblclick_todo = " onMouseDown=\"todo_options(".$pr_id.",".$str_process_fac.",'get'); \" ";	
						}						
						//$str_header="";
						$style="margin-left:0px";
						if($intSCnt>1)
						{
							if($total_prov>3)
							$style="margin-left:0px";
							else
							$style="margin-left:10px";
						}
						$str_header .= "<div style=\"width:".($name_header_width)."%;$style\" class=\"heading_container\" ".$tddblclick_todo." >";
						$prov_name = ucwords($pr_detail["name"]);	
						$prov_name_hover = ucwords($pr_detail["hover_name"]);						
						//getting appt count and procedures
						$appt_cnt = 0;
						//getting custom labels
						$arr_custom_labels = array();//this could moved out of loop
						if($str_process_fac && $pr_id && $pr_detail["dt"]){
							$qry_cl = "select facility, l_type, label_group, l_text, l_show_text, l_color, start_date, start_time, labels_replaced from scheduler_custom_labels where facility IN (".$str_process_fac.") and provider = '".$pr_id."' and start_date = '".$pr_detail["dt"]."' order by start_time";					
							$res_cl = imw_query($qry_cl);
							if(imw_num_rows($res_cl) > 0){							
								while($this_cl=imw_fetch_assoc($res_cl)){
									$arr_custom_labels[$this_cl["facility"]][$this_cl["start_time"]] = $this_cl;
								}
							}			
						}
						unset($arr_sch,$res_sch);
						$qry_sch = "select sa.id, sa.sa_facility_id, sa.sa_app_starttime,sa.sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id, sa.procedure_site, sa.procedure_sec_site, sa.procedure_ter_site, sa.EMR, sa.pt_info_updt_alert, sp.proc_color, sp.acronym, sp.proc_type, st.status_name, st.alias, st.status_color as alias_color, st.status_icon, sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId,sa.iolink_connection_settings_id,ics.iolink_practice_name,sa.iolink_ocular_chart_form_id,sa.sa_comments,sa.case_type_id, sa.ref_phy_changed, sa.ref_phy_comments ";
						
						if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
							$qry_sch .= ", rtme.transection_error as elTransectionError, rtme.EB_responce as elEBLoopResp ";
						}
						$qry_sch .= "from schedule_appointments sa USE INDEX(sa_multiplecol)
						LEFT JOIN slot_procedures sp on sp.id = sa.procedureid
						LEFT JOIN slot_procedures sp2 on sp2.id = sp.proc_time
						LEFT JOIN schedule_status st on st.id = sa.sa_patient_app_status_id ";
						if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
							$qry_sch .= " LEFT JOIN real_time_medicare_eligibility as rtme ON rtme.id = sa.rte_id ";
						}
						$qry_sch .= " LEFT JOIN iolink_connection_settings as ics ON ics.iolink_id = sa.iolink_connection_settings_id ";
						
						$qry_sch .= " where sa_facility_id IN (".$str_process_fac.") and sa_doctor_id = '".$pr_id."' and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and '".$pr_detail["dt"]."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime, sa.sa_app_time desc";						
						$res_sch = imw_query($qry_sch);
						if(imw_num_rows($res_sch) > 0){
							$appt_cnt = imw_num_rows($res_sch);
							while($arr_sch_tmp=imw_fetch_assoc($res_sch))
							{
								$arr_sch[] = $arr_sch_tmp;
							}
						}

						// code to find the no. of appointments before and after the lunch time (specify lunch time as 12:00 if not defined)
						$before_lunchTm = "12:00:00";
						$available_temp_ids = $arr_xml[$pr_id]['template_ids'];
						$sch_temp_qry = 'SELECT fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE id IN('.$available_temp_ids.')';
						$sch_temp_qry_obj = imw_query($sch_temp_qry);
						
						while($sch_temp_qry_data = imw_fetch_assoc($sch_temp_qry_obj))
						{
							$sch_temp_qry_data['fldLunchStTm'] = trim($sch_temp_qry_data['fldLunchStTm']);
							if(isset($sch_temp_qry_data['fldLunchStTm']) && $sch_temp_qry_data['fldLunchStTm'] != "00:00:00")
							{
								$before_lunchTm = $sch_temp_qry_data['fldLunchStTm'];
								break;	
							}								
						}
						
						$beforeLunchApptsQry = 'select count(id) as appts_count FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ('.$str_process_fac.') and sa_doctor_id = "'.$pr_id.'" and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_starttime < "'.$before_lunchTm.'" and sa_app_starttime !="00:00:00" and "'.$pr_detail["dt"].'" between sa_app_start_date and sa_app_end_date ';
						
						$afterLunchApptsQry = 'select count(id) as appts_count FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ('.$str_process_fac.') and sa_doctor_id = "'.$pr_id.'" and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_starttime >= "'.$before_lunchTm.'" and "'.$pr_detail["dt"].'" between sa_app_start_date and sa_app_end_date ';
													
						$beforeLunchApptsQryObj = imw_query($beforeLunchApptsQry);
						$afterLunchApptsQryObj = imw_query($afterLunchApptsQry);
						
						$no_of_appts_beforeLunch = imw_fetch_assoc($beforeLunchApptsQryObj);
						$no_of_appts_afterLunch = imw_fetch_assoc($afterLunchApptsQryObj);
						
						$prov_name_cnt_mo = " (".$appt_cnt.")";
						$prov_name_cnt = " (<span id=\"prov_appt_cnt_".$pr_id."\">".$no_of_appts_beforeLunch['appts_count']."/".$no_of_appts_afterLunch['appts_count']."</span>)";						
						$disp_prov_name = $prov_name;
						if($total_prov > 1)
						{
							if(strlen($prov_name) > $prov_name_ln)
							$disp_prov_name = substr($prov_name, 0, $prov_name_ln)."..";
							else 
							$disp_prov_name = $prov_name;
						}
						else 
						$disp_prov_name = $prov_name;
						
						if($this->schScrollWidthFlag == 1 && strlen($prov_name) > 9)
						{
							$disp_prov_name = substr($prov_name, 0, 7)."..";	
						}
						$disp_prov_name .= $prov_name_cnt;
						
						$str_ob_head_class = "";

						if($pr_detail["notes"] > 0){
							$str_ob_head_class = "ob_notes_head";
						}

						//$int_total_slots = 45;// / 60 / DEFAULT_TIME_SLOT;
						$cal_warn_per = 0;
						if($int_max_cnt > 0){
							$int_set_cnt = imw_num_rows($res_sch);
							//$str_response .= round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100)." >= ".$int_warn_percentage;							
							$cal_warn_per = round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100, 0);
							if($cal_warn_per >= $int_warn_percentage){
								$str_ob_head_class = "ob_warn_head";
								$prov_name_cnt_mo .=  " ".$cal_warn_per."% Booked.";
							}
						}
						
						$str_ob_head_class='';
						//$str_ob_head_class='ob_notify_threshold_head';
						$wd = $pr_detail['dt'];
						$wd_arr = explode('-',$wd);
						$wno = ceil($wd_arr[2] / 7);
						$dno = date("w", mktime(0, 0, 0, $wd_arr[1], $wd_arr[2], $wd_arr[0]));
						if($dno == 0) $dno = 7;
						// for getting the no. of appointments by facilities
						$qry_by_fac = "select sa_facility_id,count(id) as app_count FROM schedule_appointments where sa_facility_id IN (".$str_process_fac.") and sa_doctor_id = '".$pr_id."' and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20)  AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and (sa_app_start_date = '".$pr_detail["dt"]."' or sa_app_end_date='".$pr_detail["dt"]."') group by sa_facility_id";						
						$res_by_fac = imw_query($qry_by_fac);
						$result_appt_fac_arr = array();
						if(imw_num_rows($res_by_fac)>0)
						{
							while($result_by_fac = imw_fetch_assoc($res_by_fac))
							{
								$result_appt_fac_arr[$result_by_fac['sa_facility_id']] = $result_by_fac['app_count'];
							}
							$tmp_max_appointments_arr=$pr_detail["fac_max_appts"];
							foreach($tmp_max_appointments_arr as $fac_id_key => $pr_fac_max_appts)
							{
								
								if($appt_cnt > 0 && $int_warn_percentage > 0){
									if($pr_fac_max_appts['max_appts']>0)
									{
										if(!isset($result_appt_fac_arr[$fac_id_key])){ continue; }
										/*if($pr_fac_max_appts['max_war']>0 && round(($result_appt_fac_arr[$fac_id_key]/$pr_fac_max_appts['max_appts'])*100) >= $pr_fac_max_appts['max_war'])
										{
											$str_ob_head_class= "ob_warn_threshold_head";	
											break;
										}
										else */if(round(($result_appt_fac_arr[$fac_id_key]/$pr_fac_max_appts['max_appts'])*100) >= $int_warn_percentage)
										{
											$str_ob_head_class= "ob_warn_threshold_head";	
											break;
										}
									}
								}
								
								if($pr_fac_max_appts['min_appts']>0)
								{
									if(!isset($result_appt_fac_arr[$fac_id_key])){ continue; }
									if($result_appt_fac_arr[$fac_id_key]<$pr_fac_max_appts['min_appts'])
									{
										$str_ob_head_class= "ob_notify_threshold_head";	
										break;
									}
								}
							}	
						}else{
							//if we have no appt then we do need to check min appt check directly against facility
							$temp_arr=explode(',',$str_process_fac);
							$tmp_max_appointments_arr=$pr_detail["fac_max_appts"];
							foreach($temp_arr as $fac_id){
								if($tmp_max_appointments_arr[$fac_id]['min_appts']>0)
								{
									$str_ob_head_class= "ob_notify_threshold_head";	
									break;
								}
							}
							unset($temp_arr);
						}
						$styleForIcon=$styleForFont="";
						$str_header .= "<div class=\"rhttop $str_ob_head_class\"><div class=\"pull-left\">";
						if($total_prov>=4)
						{
							$str_header .= "<h4 title=\"$prov_name_hover.$prov_name_cnt_mo\">$disp_prov_name</h4>";
							$styleForIcon="style=\"width:22px\"";
							$styleForFont="styleForFont";
						}
						else
						{
							$str_header .= "<h2 title=\"$prov_name_hover.$prov_name_cnt_mo\">$disp_prov_name</h2>";
						}
						$str_header .= "</div><div class=\"pull-right\">";
						if($sch_type != "physician"){
									$str_header .= "<span class=\"sccont pointer $styleForFont\" id=\"sticky_".$pr_id."_".$pr_detail["dt"]."\"
									onclick=\"javascript:hide_provider_notes();show_provider_notes('".$pr_id."', '".$pr_detail["dt"]."');\">
									".$pr_detail["notes"]."</span>";
						}
						$str_header .= "<img src=\"../../library/images/scprint.png\" title=\"Print Day Appointments\" 
									onClick=\"javascript:day_print_options('".$pr_id."', '', '2');\" class=\" pointer\" $styleForIcon/> 
									<img src=\"../../library/images/not.png\" title=\"Day Summary\" 
									onClick=\"javascript:day_proc_summary('".$pr_id."');\" class=\" pointer\" $styleForIcon/></div>
									<div class=\"clearfix\"></div></div>";
						
						$str_header .="</div>";//<br class=\"clearfix\">						
						$arrStartTiming = array();
						if($appt_cnt > 0){
							for($a = 0; $a < count($arr_sch); $a++){
								$arrStartTiming[$arr_sch[$a]["sa_app_starttime"]][] = $arr_sch[$a];
							}
						}
																		
						if(DEFAULT_TIME_SLOT == 10){
							$arrStartTiming = $this->plugin_to_show_5min_appt($arrStartTiming);
						}
						if(DEFAULT_TIME_SLOT == 15){
							$arrStartTiming = $this->plugin_to_show_10min_appt($arrStartTiming);
						}		

						$div_id = 'dive_'.str_replace("-", "_", $pr_detail["dt"]).'_'.$pr_detail["fac_id"].'_'.$pr_id;
						$imgid = "im".$div_id;
						$tabid = "tab".$div_id;
						//owl specific div
						//$str_appt_slots .= "<div class=\"item\">";
						$str_appt_slots .= "<div class=\"fl\" style=\"width:".($column_width-$column_width_minus)."%; position: relative\"><div id=\"".$tabid."\" class=\"appt_cont\" style=\"height:".$tp_sl_height."px;\">";
						$arrPrevThisAppt = array();			
						//if($intCnt == 0){$str_time_pane.='<div id="expand_collapse_icon">&nbsp;</div><div class="clearfix"></div>';}
						foreach($pr_detail["slots"] as $sl_id => $sl_detail){
							if($intCnt == 0){								
								$tp_st_hr = number_format(substr($sl_id, 0, 2));
								$tp_st_mn = number_format(substr($sl_id, 3, 2));								
								$str_time_pane .= "<div class=\"";
								$str_time_pane .=($tp_st_mn == 0)?"time_pane title_pane":"time_pane";
								$ln_height=($tp_st_mn == 0)?$tp_sl_height+10:$tp_sl_height;
								$str_time_pane .="\" style=\"height:".$tp_sl_height."px;line-height:".$ln_height."px;\">";
								if($tp_st_mn == 0){

									$str_time_pane .= "<div class=\"hr_pane\">";
									$str_time_pane .= $time_array[$tp_st_hr];
									$str_time_pane .= "</div> ";									
								}else{
									$str_time_pane .= "<div class=\"hr_pane\"><div class=\"mn_pane\">";
									$str_time_pane .= $tp_st_mn;
									$str_time_pane .= "</div></div> ";	
								}
								$str_time_pane .= "</div> ";								
							}
							$intSCnt++;
							$intStartHr = substr($sl_id, 0, 2);
							$intStartMin = substr($sl_id, 3, 2);
							$times_from = $intStartHr.":".$intStartMin.":00";							
							$intEndHr = substr($sl_id, 6, 2);
							$intEndMin = substr($sl_id, 9, 2);
							$times_to = $intEndHr.":".$intEndMin.":00";			
							
							//adjusting previouse slot appointment in this slot
							if(count($arrPrevThisAppt) > 0){
								if(isset($arrStartTiming[$times_from]))
									$intExistingApptsForThisSlot = count($arrStartTiming[$times_from]);							
								else
									$intExistingApptsForThisSlot=0;
									
									
								$arrTempTimings = $arrStartTiming;
								$intNewApptsForThisSlot = 0;
								foreach ($arrPrevThisAppt as $arrThisPrevThisAppt){
									$arrStartTiming[$times_from][$intNewApptsForThisSlot] = $arrThisPrevThisAppt;
									$intNewApptsForThisSlot++;
								}
								$k5 = 0;
								for($k6 = $intNewApptsForThisSlot; $k6 < ($intExistingApptsForThisSlot+$intNewApptsForThisSlot); $k6++){
									$arrStartTiming[$times_from][$k6] = $arrTempTimings[$times_from][$k5];
									$k5++;
								}
								//resetting array
								$arrPrevThisAppt = array();
							}							
							$tddblclick = "";
							$tddblclick_one = "";
							$tddblclick_blk = "";
							$strMsg = "";							
							$slot_color = $sl_detail['color'];
							$label_type = $sl_detail['label_type'];
							$label_group = $sl_detail['label_group'];
							$l_text = $sl_detail['l_text'];
							if(isset($arr_custom_labels[$sl_detail["fac_id"]][$times_from]) && ($sl_detail["status"] != "block" && $sl_detail["status"] != "lock" && $sl_detail["status"] != "off")){		
								$arr_clbl_temp = explode("; ", $arr_custom_labels[$sl_detail["fac_id"]][$times_from]["l_show_text"]);
								asort($arr_clbl_temp);
								if($arr_custom_labels[$sl_detail["fac_id"]][$times_from]["l_color"]){
								$slot_color = $arr_custom_labels[$sl_detail["fac_id"]][$times_from]["l_color"];}
								$sl_detail["label"] = implode("; ", $arr_clbl_temp);
								$sl_detail["l_text"] = $l_text = $arr_custom_labels[$sl_detail["fac_id"]][$times_from]["l_text"];
								$sl_detail["label_type"] = $label_type = $arr_custom_labels[$sl_detail["fac_id"]][$times_from]["l_type"];
								$sl_detail["label_group"] = $label_group = $arr_custom_labels[$sl_detail["fac_id"]][$times_from]["label_group"];
								$sl_detail["label_replaced"]=$arr_custom_labels[$sl_detail["fac_id"]][$times_from]["labels_replaced"];
							}
							$sl_detail["label"] = addslashes($sl_detail["label"]);
							$tdMouseUp = "";
							$tddblclick_blk = "";
							if($sch_type != "physician"){
								$procedure_limit=-1;
								$lb_arr=array();
								if($sl_detail["label_type"]=='Procedure')
								{
									if($sl_detail["l_text"])
									{
										//check no of procedure are there	
										if($sl_detail['label_group']==1)
										{
											$lb_arr[0]=$sl_detail["l_text"];
										}
										else
										{
											$lb_arr=explode('; ',$sl_detail["l_text"]);
										}
										$procedure_limit=sizeof($lb_arr);
									}else $procedure_limit=0;
								}
								
								if(!$sl_detail["fac_id"])$sl_detail["fac_id"]=0;
								if(!$sl_detail["label_type"])$sl_detail["label_type"]='';
								if(!$sl_detail["label_group"])$sl_detail["label_group"]=0;
								if(!$arr_user_types[$pr_id])$arr_user_types[$pr_id]=0;
								
								$tdMouseUp = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '', '".$sl_detail["label_type"]."', 'no','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" ";
								$tddblclick_blk = " onMouseDown=\"pop_menu_time('".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','[{MODE}]', '".$label_type."', '".$sl_detail["label"]."', '".$slot_color."','".$sl_detail["tmpId"]."'); \" onClick=\"TestOnMenu(); hide_tool_tip();\" ";
								if(($admin_priv == 1 || $sch_overrider_privilege==1) && array_key_exists($times_from, $arrStartTiming) == false){			$tddblclick_blk = " onMouseDown=\"pop_menu_time('".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','[{MODE}]', '".$label_type."', '".$sl_detail["label"]."', '".$slot_color."','".$sl_detail["tmpId"]."'); \" ";	
								}
							}
							
							//if rail is disabled then disable double click too
							if(constant("DISABLE_CLICK_SCHEDULER_RAIL")==1){
								$dblClickEvt_dem_raw="";
							}else
							{
							$dblClickEvt_dem_raw = " ondblclick=\"load_set_appt('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '%s', '".$sl_detail["label_type"]."', '%s');\" ";
							}
							
							if(in_array($sl_detail["fac_id"], $arr_process_fac)){	
								$mouse_over_slot_detail = $sl_detail["fac_name"].": ".$sl_detail["tmp_start_time"]." - ".$sl_detail["tmp_end_time"];
								if($sl_detail["status"] == "block"){
									$str_appt_slots .= "<div style=\"color:#999;width:100%;height:".$tp_sl_height."px".$extra_padding."\">";
									if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
										$RAIL_CLICK_EVENT=$tdMouseUp;
										if(constant("DISABLE_CLICK_SCHEDULER_RAIL")==1){
											$RAIL_CLICK_EVENT="";
										}else
										{
											$RAIL_CLICK_EVENT=str_replace('[{LABELS}]',$sl_detail["label"],$RAIL_CLICK_EVENT);
										}
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$RAIL_CLICK_EVENT." data-avaiLabel=\"".$sl_detail["l_text"]."\"></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$slot_color.";\" ".str_replace("[{MODE}]","block",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
									}else{
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); alert('Locked Time.');\"></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$slot_color.";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('Blocked Time.');stopEventsinSch();\" ".str_replace("[{MODE}]","block",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
									}
								}else if($sl_detail["status"] == "lock"){
									$str_appt_slots .= "<div style=\"width:100%;height:".$tp_sl_height."px".$extra_padding."\">";
									if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){							$RAIL_CLICK_EVENT=$tdMouseUp;
										if(constant("DISABLE_CLICK_SCHEDULER_RAIL")==1){
											$RAIL_CLICK_EVENT="";
										}	
										else
										{
											$RAIL_CLICK_EVENT=str_replace('[{LABELS}]',$sl_detail["label"],$RAIL_CLICK_EVENT);
										}
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$RAIL_CLICK_EVENT." data-avaiLabel=\"".$sl_detail["l_text"]."\"></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$slot_color.";\" ".str_replace("[{MODE}]","lock",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
									}else{
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); alert('Locked Time.');\"></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$slot_color.";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('Locked Time.');stopEventsinSch();\" ".str_replace("[{MODE}]","lock",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
									}
								}else if($sl_detail["status"] == "off"){
									$str_appt_slots .= "<div style=\"width:100%;height:".$tp_sl_height."px".$extra_padding."\">";
									if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); top.fAlert('Office is Closed.');\"></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$slot_color.";\" ".str_replace("[{MODE}]","off",$tddblclick_blk)." title=\"Office is Closed.\">";
									}else{
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); top.fAlert('Office is Closed.');\"></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$slot_color.";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('Locked Time.');stopEventsinSch();\" ".str_replace("[{MODE}]","off",$tddblclick_blk)." title=\"Office is Closed.\">";
									}								
								}else if($sl_detail["status"] == "on"){
									list($st,$et)=explode("-",$sl_detail["id"]);
									$str_appt_slots .= "<div style=\"width:100%;height:".$tp_sl_height."px".$extra_padding."\">";
									$tdMouseUp_c="";
									if(!$sl_detail["label_type"] || trim($sl_detail["label_type"])==""){$tdMouseUp_c=$tdMouseUp;}									
									elseif($sl_detail["label_type"] && trim($sl_detail["label"])=='' && trim($sl_detail["label_replaced"])=='')
									{
										$tdMouseUp_c=$tdMouseUp;
									}	
									
									$RAIL_CLICK_EVENT=$tdMouseUp;
									if(constant("DISABLE_CLICK_SCHEDULER_RAIL")==1){
										$RAIL_CLICK_EVENT="";
									}
									else
									{
										$RAIL_CLICK_EVENT=str_replace('[{LABELS}]',$sl_detail["label"],$RAIL_CLICK_EVENT);
									}
									$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,"","no");
									$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$RAIL_CLICK_EVENT." data-avaiLabel=\"".$sl_detail["l_text"]."\"></div><div id='".str_ireplace(":","_",$st)."' class=\"fl slt_border cls_".$innerContainer."\" ".$tdMouseUp_c." ".$dblClickEvt_dem." style=\"cursor:pointer;height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".$pr_detail["color"]."\" ".str_replace("[{MODE}]","on",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
								
								}
								$appt_st_hr=(isset($appt_st_hr))?$appt_st_hr:0;
								$appt_st_mn=(isset($appt_st_mn))?$appt_st_mn:0;
								$appt_st_sc=(isset($appt_st_sc))?$appt_st_sc:0;
								if(array_key_exists($times_from, $arrStartTiming)){									
									//getting acronym labels
									$arr_label_data = array();
									$arr_toshow_label_data = array();
									$arr_shown_label_data = array();
									$arr_label_data_tempo = array();
									if(strtolower($sl_detail["label"]) != "lunch" && $sl_detail["label"] != "" && $sl_detail["status"]  != "block" && $sl_detail["status"] != "lock"){
										$arr_label_data = array();
										if($sl_detail["label_group"]==1)$arr_label_data_tempo[0] = $sl_detail["label"];
										else $arr_label_data_tempo = explode(";", $sl_detail["label"]);
										if(count($arr_label_data_tempo) > 0){
											foreach($arr_label_data_tempo as $this_arr_lbl){
												if(trim($this_arr_lbl) != ""){
													$arr_label_data[] = $this_arr_lbl;
												}
											}
										}
										asort($arr_label_data);
									}									
									//provider has appointments
									$str_appt_slots .= "<div style=\"width:".($innerContainer+3)."%;margin-top:0px; margin-left:0px; ".$pos_abs."\" id=\"".$div_id.$times_from."\">[{(".$pr_id."::".$times_from.")}]";
									$intTotApptInSlot = count($arrStartTiming[$times_from]);
									$intShowAppt = 0;
									$intPrevApptCnt = 0;									
									$arrPrevThisAppt = array();									
									$used_positions = array();
									foreach($arrStartTiming[$times_from] as $arrThisAppt){	
										//echo "a".$times_from."::".$arrThisAppt['id']."a".$arrThisAppt["repeat"]."<br>";
										if(isset($arrThisAppt["repeat"]) && !empty($arrThisAppt["repeat"])){
											$this_pos = $arrThisAppt["position"];											
										}else{
											$this_pos = $this->get_next_appt_pos($used_positions);
										}
										array_push($used_positions, $this_pos);
										//calculating height
										$appt_duration = (strtotime($arrThisAppt["sa_app_endtime"]) - strtotime($arrThisAppt["sa_app_starttime"])) / 60;
										list($appt_st_hr, $appt_st_mn, $appt_st_sc) = explode(":", $arrThisAppt["sa_app_starttime"]);
										if($appt_duration <= 0){
											$appt_duration = 10;
										}
										$divRepeat = (isset($arrThisAppt['repeat']) && !empty($arrThisAppt['repeat'])) ? $arrThisAppt['repeat'] - 1 : 0;
										$divHeightDiffFactor = $divRepeat * DEFAULT_TIME_SLOT;
										$divHeight = (($tp_sl_height / DEFAULT_TIME_SLOT) * ($appt_duration - $divHeightDiffFactor)) + 0;
										$divZindex = 5000 + ((int)$appt_st_mn + ((int)$appt_st_hr * 60));										
										$divTopBorder = (isset($arrThisAppt['repeat'])) ? "" : "border:1px solid #000000;";										
										$divColor = ($arrThisAppt['proc_color'] != "") ? $arrThisAppt['proc_color'] : "#FFFFFF";										
										$disp_content = "";
										$mouse_over_content = "";
										$sec_ter_acronyms = "";
										if(isset($arr_all_proc[$arrThisAppt["sec_procedureid"]]["acronym"]) && trim($arr_all_proc[$arrThisAppt["sec_procedureid"]]["acronym"]) != "")
										{
											$sec_ter_acronyms .= ", ".$arr_all_proc[$arrThisAppt["sec_procedureid"]]["acronym"];
											if($arrThisAppt["procedure_sec_site"]){
												$sec_ter_acronyms .= " ".$arr_proc_site[$arrThisAppt["procedure_sec_site"]];
											}
											if(isset($arr_all_proc[$arrThisAppt["tertiary_procedureid"]]["acronym"]) && trim($arr_all_proc[$arrThisAppt["tertiary_procedureid"]]["acronym"]) != "")
											{
												$sec_ter_acronyms .= ", ".$arr_all_proc[$arrThisAppt["tertiary_procedureid"]]["acronym"];
												if($arrThisAppt["procedure_ter_site"]){
													$sec_ter_acronyms .= " ".$arr_proc_site[$arrThisAppt["procedure_ter_site"]];
												}
											}
										}
										
                                        //break glass privilege check
                                        $askForReason = $this->sch_core_get_restricted_status($arrThisAppt["sa_patient_id"]);
                                        
										$proc_type_hover="";
										/*if(defined('SCHEDULER_SHOW_PROC_TYPE') && constant('SCHEDULER_SHOW_PROC_TYPE')==true && $arrThisAppt['proc_type'] && $total_prov>3)
										{
											$proc_type_hover="($arrThisAppt[proc_type])";
										}*/
												
										$mouse_over_content .= $arrThisAppt['acronym'].$proc_type_hover." ".$arr_proc_site[$arrThisAppt["procedure_site"]].$sec_ter_acronyms." - ".stripslashes($arrThisAppt['sa_patient_name']);
										if(count($arr_label_data) > 0 && in_array(trim($arrThisAppt['acronym']), $arr_label_data)){
											$arr_shown_label_data[] = $arrThisAppt['acronym'];
										}										
										$pp_menu = "";
										$on_click_appt = "javascript:void(0);";
										$on_click_appt1 = "javascript:void(0);";
										$on_dclick_appt = "javascript:void(0);";										
										if($sch_type != "physician"){
											$pp_menu = "onMouseDown = \"pop_menu('".$arrThisAppt['id']."','".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','".$arrThisAppt['sa_patient_id']."', '', '".$label_type."', '".$sl_detail["label"]."', '".$slot_color."','".$arrThisAppt["iolink_connection_settings_id"]."','".$arrThisAppt["iolink_practice_name"]."','".$arrThisAppt["iolink_ocular_chart_form_id"]."','".$askForReason."'); set_init_timings('".$arrThisAppt["sa_app_starttime"]."','".$arrThisAppt["sa_app_endtime"]."','".$arrThisAppt['acronym']."','".$pr_detail["id"]."','".$sl_detail["fac_id"]."');\"";
											$on_click_appt = "showIolinkPdf('".$arrThisAppt['sa_patient_id']."');";
											$on_click_appt1 = "javascript:pre_load_front_desk('".$arrThisAppt['sa_patient_id']."', '".$arrThisAppt['id']."');";
											$on_dclick_appt = "javascript:drag_name('".$arrThisAppt['id']."', '".$arrThisAppt['sa_patient_id']."', 'reschedule');stopEventsinSch();";
										}else if($sch_type == "physician"){
											$pp_menu = "onMouseDown = \"javascript:addPatientNotes('".$arrThisAppt["sa_patient_id"]."','".$arrThisAppt["sa_patient_name"]."','".$_SESSION["authId"]."','".$eff_date_add_sch."');\"";
											$on_click_appt1 = "init_showPatientDiagnosisWindow('".$arrThisAppt["sa_patient_id"]."');";
											$on_dclick_appt = "double_click=1;showWorkViewWindow('".$arrThisAppt["sa_patient_id"]."');";										
										}										
										if(!(isset($arrThisAppt['repeat']) && !empty($arrThisAppt['repeat']))){											
											if($arrThisAppt["EMR"] == 1){
												$emrsymbol=" - <strong>e</strong>";
											}else{
												$emrsymbol="&nbsp;";
											}
											$disp_content .= "<div class=\"appt_txt\" id='".$arrThisAppt['id']."'>";									
											if(defined('SCHEDULER_SHOW_STATUS_COLOR') && constant('SCHEDULER_SHOW_STATUS_COLOR')==true && $arrThisAppt['alias_color'])
											{
												$disp_content .= "<div style=\"height:".($divHeight-2)."px;background-color:".$arrThisAppt['alias_color']."\" class=\"sts_clr\">&nbsp;</div>";
											}else{
												if($arrThisAppt['alias']=='RS' && constant('PRACTICE_PATH')=='bennett'){}
												else $disp_content .= "<div class=\"fl act_symbol\">".$arrThisAppt['alias']."</div>";
											}
											if($arrThisAppt['iolinkPatientId'] != 0 && $arrThisAppt['iolinkPatientWtId'] != 0){
												$disp_content .= "<div class=\"fl io_1\" onclick=\"".$on_click_appt."\">I</div><div class=\"fl io_2\" onclick=\"".$on_click_appt."\">O</div>";
											}		
											if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
												if($arrThisAppt['elTransectionError'] != ""){
													$elToolTip = "Eligibility Status: Error \n".$arrThisAppt['elTransectionError'];
													$disp_content .= "<div class=\"fl elRed\" title=\"".$elToolTip."\">eR</div>";
												}
												elseif($arrThisAppt['elEBLoopResp'] != ""){		
													$strEBResponce = $this->objCoreLang->get_vocabulary("vision_share_271", "EB", (string)trim($arrThisAppt['elEBLoopResp']));
													$elToolTip = "Eligibility Status: ".$strEBResponce;
													if(($arrThisAppt['elEBLoopResp'] == "6") || ($arrThisAppt['elEBLoopResp'] == "7") || ($arrThisAppt['elEBLoopResp'] == "8") || ($arrThisAppt['elEBLoopResp'] == "V")){
														$disp_content .= "<div class=\"fl elRed\" title=\"".$elToolTip."\">eR</div>";
													}
													else{
														$disp_content .= "<div class=\"fl elGreen\" title=\"".$elToolTip."\">eR</div>";
													}
												}
											}
											if($arrThisAppt['ref_phy_changed']==1){
												$info='';
												$info=($arrThisAppt['ref_phy_comments'])?$arrThisAppt['ref_phy_comments']:'Referring physician information updated';
												$disp_content .= "<div class=\"fl\"><img src='../../library/images/flag_yellow_black_border.png' title='".$info."'></div>";
											}
											if(defined('SCHEDULER_SHOW_PROC_TYPE') && constant('SCHEDULER_SHOW_PROC_TYPE')==true && $arrThisAppt['proc_type'] && $total_prov<=3)
											{
												$disp_content .= "<div class=\"fl\"><img src='../../library/images/$arrThisAppt[proc_type].png' title='".$arrThisAppt['proc_type']." Appointment' width='28px'></div>";
											}
											
											$disp_content .= $mouse_over_content;
											//check do we have assign room to patient
											$room_str='';
											if($eff_date_add_sch==date("m-d-Y") && $arrThisAppt['sa_patient_id'] && $arrThisAppt['status_name'] !="" && $arrThisAppt['status_name'] != "Checked Out")
											{
												/*$pt_rm_q=imw_query("SELECT app_room FROM patient_location WHERE patientId = '".$arrThisAppt['sa_patient_id']."' AND cur_date = '".date("Y-m-d")."' ORDER BY patient_location_id DESC LIMIT 1");
												if(imw_num_rows($pt_rm_q)>=1)
												{
													$pt_rm_d=imw_fetch_object($pt_rm_q);
													if($pt_rm_d->app_room!='N/A')$room_str="<br clear='all'/><div style='padding-left:3px; text-align:left; font-weight:bold'>($pt_rm_d->app_room)</div>";
												}*/
											}
											$disp_content .= "<i>".$emrsymbol."</i></div>$room_str";
										}else{
											$disp_content .= "<div class=\"fl\" id='".$arrThisAppt['id']."' ".$pp_menu." onclick=\"".$on_click_appt1."\" ondblclick=\"".$on_dclick_appt."\"></div>";
										}
										$appt_comment=$appt_case_ins="";
										//this string throwing error $____ so removeing it
										if(trim($arrThisAppt["sa_comments"])){$appt_comment="\n".str_replace('__','--',$arrThisAppt["sa_comments"]);}
										$case_type_id=$arrThisAppt["case_type_id"];
										$sa_patient_id_get=$arrThisAppt['sa_patient_id'];
										$case_type_id_get=$arrThisAppt["case_type_id"];
										if($case_type_id_get && $arr_ins_comp[$sa_patient_id_get][$case_type_id_get]['primary']){
											$appt_case_ins.="\nPri: ".$arr_ins_comp[$sa_patient_id_get][$case_type_id_get]['primary'];
											if(isset($arr_ins_comp[$sa_patient_id_get][$case_type_id_get]['secondary'])){
												$appt_case_ins.="\nSec: ".$arr_ins_comp[$sa_patient_id_get][$case_type_id_get]['secondary'];
											}
										}
										$mouse_over_content .= " (".core_time_format($arrThisAppt["sa_app_starttime"])." - ".core_time_format($arrThisAppt["sa_app_endtime"]).")".$appt_case_ins.$appt_comment;
										if(strstr($arrThisAppt["pt_info_updt_alert"],'~:~medical')){$mouse_over_content .= "\n Clinical Information updated";}
										if(strstr($arrThisAppt["pt_info_updt_alert"],'~:~demographics')){$mouse_over_content .= "\n Demographics Information updated";}
										$str_hidden_vision = "";
										if(isset($arrThisAppt['repeat']) && $arrThisAppt['repeat'] > 0){
											$str_hidden_vision = "visibility:hidden;";
										}
										$str_appt_slots_div = "<div id=\"appt_".$arrThisAppt['id']."\" class=\"sdf\" style=\"".$str_hidden_vision."position:absolute;[{(_BORDER_BOTTOM_)}]".$divTopBorder."background-color:".$divColor.";height:".$divHeight."px;width:[{(WIDTH".$arrThisAppt['id']."WIDTH)}]%;z-index:".$divZindex.";left:[{(LEFT".$arrThisAppt["id"]."LEFT)}]%;".$phy_margin_left."\" title=\"".$mouse_over_content."\" ".$pp_menu." onclick=\"".$on_click_appt1."\" ondblclick=\"".$on_dclick_appt."\">";
										$str_appt_slots_div .= "[{(DEBUGGER)}]".$disp_content;
										$str_appt_slots_div .= "</div>";										
										//slot end time 
										$arrTempSlotEndTime = explode(":",$times_to);
										$tsSlotEndTime = mktime($arrTempSlotEndTime[0],$arrTempSlotEndTime[1],$arrTempSlotEndTime[2]);
										//appt end time
										$arrTempAppEndTime = explode(":", $arrThisAppt['sa_app_endtime']);
										$tsAppEndTime = mktime($arrTempAppEndTime[0],$arrTempAppEndTime[1],$arrTempAppEndTime[2]);										
										if(isset($arrThisAppt['repeat']) && $arrThisAppt['repeat'] != ""){											
											if($tsAppEndTime > $tsSlotEndTime){
												$arrTemp = array("repeat" => $arrThisAppt['repeat'] + 1, "position" => $this_pos);												
												$arrPrevThisAppt[$intPrevApptCnt] = array_merge($arrThisAppt,$arrTemp);
												$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "", $str_appt_slots_div);
											}else{
												$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "border-bottom:1px solid #000000;", $str_appt_slots_div);
											}
											$appt_repeat_type = "repeated";
										}else{											
											if($tsAppEndTime > $tsSlotEndTime){
												$arrTemp = array("repeat" => 2, "position" => $this_pos);
												$arrPrevThisAppt[$intPrevApptCnt] = array_merge($arrThisAppt,$arrTemp);
												$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "", $str_appt_slots_div);
											}else{
												$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "border-bottom:1px solid #000000;", $str_appt_slots_div);
											}
											$appt_repeat_type = "new";
										}
										if(!isset($arrThisAppt["repeat"]))$arrThisAppt["repeat"]='';
										$arr_slot_content[$pr_id][$times_from][$this_pos] = array("html" => $str_appt_slots_div, 
																							"wide" => "",
																							"left" => "",
																							"prid" => $pr_id,
																							"appt" => $arrThisAppt["id"],
																							"rept" => $arrThisAppt["repeat"],
																							"type" => $appt_repeat_type,
																							"position" => $this_pos
																					);
										$intPrevApptCnt++;
										$intShowAppt++;
									}																		
									$arr_toshow_label_data = $arr_label_data;
									$this_pos = $this->get_next_appt_pos($used_positions);
									$procedure_limit=-1;
									if($sl_detail["label_type"]=='Procedure')
									{
										if($sl_detail["l_text"])
										{
											if($sl_detail["label_group"]==1){$lb_arr[0]=$sl_detail["l_text"];}
											else{
											//check no of procedure are there	
											$lb_arr=explode('; ',$sl_detail["l_text"]);
											}
											$procedure_limit=sizeof($lb_arr);
										}else $procedure_limit=0;
									}
									if(count($arr_toshow_label_data) > 0){
										$appt_repeat_type = "label";
										//decide display label hight -1 than overall slot hight
										$divHeight = (($tp_sl_height / DEFAULT_TIME_SLOT) * (DEFAULT_TIME_SLOT)) - 1;
										$divZindex = 5000 + ((int)$appt_st_mn + ((int)$appt_st_hr * 60));
										$lbl_width_division = (count($arr_toshow_label_data) > 0) ? ((count($arr_toshow_label_data) > 4) ? 4 : count($arr_toshow_label_data))  : 1;
										$lbl_border="";
										$lbl_border="border-top:1px solid #000000;border-right:1px solid #000000;";
										for($ald = 0; $ald < count($arr_toshow_label_data); $ald++){
											$index_arr_label_data = trim($arr_toshow_label_data[$ald]);
											$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,$arr_toshow_label_data[$ald],"yes");
											//if(isset($all_proc_lbl[$index_arr_label_data])){
											if(isset($all_proc_lbl[$index_arr_label_data]) && $sl_detail["label_type"] == "Procedure"){
												$tdMouseUp2 = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '".$arr_toshow_label_data[$ald]."', '".$sl_detail["label_type"]."', 'yes','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" onmousedown = \"set_replace_label('".$arr_toshow_label_data[$ald]."');\" $dblClickEvt_dem ";
												$proc_color = "";
												$proc_color = (isset($all_proc_lbl[$index_arr_label_data]) && !empty($all_proc_lbl[$index_arr_label_data])) ? $all_proc_lbl[$index_arr_label_data] : $slot_color;
												if($arr_toshow_label_data[$ald] != ""){
													$str_appt_slots_lbl = "<div id=\"more_options_".$times_from."\" ".$tdMouseUp2." onMouseDown=\"pop_menu_time('".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','on', '".$label_type."', '".stripslashes($sl_detail["label"])."', '".$slot_color."','".$sl_detail["tmpId"]."'); \" style=\"position:absolute;cursor:pointer;".$lbl_border."background-color:".$proc_color.";height:".$divHeight."px;width:[{(WIDTHA".$ald."ppWIDTH)}]%;z-index:".$divZindex.";left:[{(LEFTA".$ald."ppLEFT)}]%;\" title=\"".implode("; ", $arr_toshow_label_data)."\">";
													$str_appt_slots_lbl .= "<span class=\"sdf leave_text\" style=\"font-weight:bold;\">[{(DEBUGGER)}]".$arr_toshow_label_data[$ald]."&nbsp; </span>";
													$str_appt_slots_lbl .= "</div>";
												}
												$arr_slot_content[$pr_id][$times_from][$this_pos] = array("html" => $str_appt_slots_lbl, 
																								"wide" => "",
																								"left" => "",
																								"prid" => $pr_id,
																								"appt" => "A".$ald."pp",
																								"rept" => "",
																								"type" => $appt_repeat_type,
																								"position" => $this_pos
																						);
												$intShowAppt++;
												array_push($used_positions, $this_pos);
												$this_pos = $this->get_next_appt_pos($used_positions);
											}else if($sl_detail["label_type"] == "Information"){
												$reduced_coloring = "purple_stripe_pattern";
												$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,$arr_toshow_label_data[$ald],"no");
											
												$tdMouseUp2 = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '".$arr_toshow_label_data[$ald]."', '".$sl_detail["label_type"]."', 'no','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" onmousedown = \"set_replace_label('".$arr_toshow_label_data[$ald]."');\" $dblClickEvt_dem ";
												$proc_color = "";
												$proc_color = (isset($sl_detail["l_color"]) && !empty($sl_detail["l_color"])) ? $sl_detail["l_color"] : $slot_color;
												if($arr_toshow_label_data[$ald] != ""){
													$str_appt_slots_lbl = "<div id=\"more_options_".$times_from."\" ".$tdMouseUp2." onMouseDown=\"pop_menu_time('".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','on', '".$label_type."', '".stripslashes($sl_detail["label"])."', '".$slot_color."','".$sl_detail["tmpId"]."'); \" style=\"position:absolute;cursor:pointer;".$lbl_border."background-color:".$proc_color.";height:".$divHeight."px;width:[{(WIDTHA".$ald."ppWIDTH)}]%;z-index:".$divZindex.";left:[{(LEFTA".$ald."ppLEFT)}]%;\" title=\"".implode("; ", $arr_toshow_label_data)."\" class=\"".$reduced_coloring."\">";
													$str_appt_slots_lbl .= "<span class=\"sdf\" style=\"font-weight:bold;\">[{(DEBUGGER)}]".$arr_toshow_label_data[$ald]."&nbsp; </span>";
													$str_appt_slots_lbl .= "</div>";
												}
												$arr_slot_content[$pr_id][$times_from][$this_pos] = array("html" => $str_appt_slots_lbl, 
																								"wide" => "",
																								"left" => "",
																								"prid" => $pr_id,
																								"appt" => "A".$ald."pp",
																								"rept" => "",
																								"type" => $appt_repeat_type,
																								"position" => $this_pos
																						);
												$intShowAppt++;
												array_push($used_positions, $this_pos);
												$this_pos = $this->get_next_appt_pos($used_positions);												
											}
										}
									}
									$str_appt_slots .= "</div>";
								}else{
									//print_r($sl_detail);
									if(strtolower($sl_detail["label"]) != "" && $sl_detail["status"]  != "block" && $sl_detail["status"] != "lock"){
										//decide display label hight -1 than overall slot hight
										$divHeight = (($tp_sl_height / DEFAULT_TIME_SLOT) * (DEFAULT_TIME_SLOT)) - 1;
										$divZindex = 5000 + ((int)$appt_st_mn + ((int)$appt_st_hr * 60));
										$lbl_border="";
										$lbl_border="border-top:1px solid #000000;border-right:1px solid #000000;";
										if($sl_detail["label_type"] == "Information"){
											$reduced_coloring = "purple_stripe_pattern";
										}else{
											$reduced_coloring = "";
										}
										$arr_label_data = $arr_label_data_tempo = array();
										
										if($sl_detail["label_group"]==1)$arr_label_data_tempo[0] = $sl_detail["label"];
										else $arr_label_data_tempo = explode(";", $sl_detail["label"]);
										
										if(count($arr_label_data_tempo) > 0){
											foreach($arr_label_data_tempo as $this_arr_lbl){
												if(trim($this_arr_lbl) != ""){
													$arr_label_data[] = $this_arr_lbl;
												}
											}
										}
										asort($arr_label_data);
										if(count($arr_label_data) > 1){
											for($ald = 0; $ald < count($arr_label_data); $ald++){
												$index_arr_label_data = trim($arr_label_data[$ald]);
													
												//if(isset($all_proc_lbl[$index_arr_label_data])){
												if($sl_detail["label_type"] == "Procedure" && isset($all_proc_lbl[$index_arr_label_data])){
													$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,$arr_label_data[$ald],"yes");
													$tdMouseUp2 = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '".$arr_label_data[$ald]."', '".$sl_detail["label_type"]."', 'yes','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" onmousedown = \"set_replace_label('".$arr_label_data[$ald]."');\" $dblClickEvt_dem ";
												
													$proc_color = "";
													$proc_color = (isset($all_proc_lbl[$index_arr_label_data]) && !empty($all_proc_lbl[$index_arr_label_data])) ? $all_proc_lbl[$index_arr_label_data] : "#FFFFFF";
													$str_appt_slots .= "<div id=\"more_options_".$times_from."\" ".$tdMouseUp2." class=\"fl\" style=\"cursor:pointer;".$lbl_border."background-color:".$proc_color.";height:".$divHeight."px;width:".(($div_width) / count($arr_label_data))."%;z-index:".$divZindex.";left:[{(LEFT-1LEFT)}]%;\" title=\"".$arr_label_data[$ald]."\">";
													$str_appt_slots .= "<span class=\"sdf leave_text\" style=\"font-weight:bold;\">".$arr_label_data[$ald]."&nbsp; </span>";
													$str_appt_slots .= "</div>";
												}else{
													$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,$arr_label_data[$ald],"no");
													$tdMouseUp2 = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '".$arr_label_data[$ald]."', '".$sl_detail["label_type"]."', 'no','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" onmousedown = \"set_replace_label('".$arr_label_data[$ald]."');\" $dblClickEvt_dem ";
												
													$str_appt_slots .= "<div id=\"more_options_".$times_from."\" ".$tdMouseUp2." class=\"fl purple_stripe_pattern\" style=\"cursor:pointer;".$lbl_border."background-color:".$slot_color.";height:".$divHeight."px;width:".(($div_width) / count($arr_label_data))."%;z-index:".$divZindex.";left:[{(LEFT-1LEFT)}]%;\" title=\"".$arr_label_data[$ald]."\">";
													$str_appt_slots .= "<span class=\"sdf\" style=\"font-weight:bold;color:#000000;\">".$arr_label_data[$ald]."&nbsp; </span>";
													$str_appt_slots .= "</div>";
												}
											}
										}else{
											$index_arr_label_data = trim($arr_label_data[0]);
												
											if($sl_detail["label_type"] == "Procedure" && isset($all_proc_lbl[$index_arr_label_data])){
												$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,$index_arr_label_data,"yes");
												$tdMouseUp2 = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '".$index_arr_label_data."', '".$sl_detail["label_type"]."', 'yes','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" onmousedown = \"set_replace_label('".$index_arr_label_data."');\" $dblClickEvt_dem ";
											
												$proc_color = (isset($all_proc_lbl[$index_arr_label_data]) && !empty($all_proc_lbl[$index_arr_label_data])) ? $all_proc_lbl[$index_arr_label_data] : "#FFFFFF";
												$str_appt_slots .= "<div id=\"more_options_".$times_from."\" ".$tdMouseUp2." class=\"fl\" style=\"cursor:pointer;".$lbl_border."background-color:".$proc_color.";height:".$divHeight."px;width:".($div_width)."%;z-index:".$divZindex.";left:[{(LEFT-1LEFT)}]%;\" title=\"".$arr_label_data[0]."\">";
												$str_appt_slots .= "<span class=\"sdf leave_text\" style=\"font-weight:bold;\">".$arr_label_data[0]."&nbsp; </span>";
												$str_appt_slots .= "</div>";
											}else{
												if(!$sl_detail["label_type"])$sl_detail["label_type"]=0;
												if(!$sl_detail["tmpId"])$sl_detail["tmpId"]=0;
												if(!$arr_user_types[$pr_id])$arr_user_types[$pr_id]=0;
												
												$dblClickEvt_dem=sprintf($dblClickEvt_dem_raw,$index_arr_label_data,"no");
												$tdMouseUp2 = "onClick=\"top.fmain.sch_drag_id('".$times_from."','".$eff_date_add_sch."','".$sl_detail["fac_id"]."','".$pr_id."','".$sl_detail["tmpId"]."', '".$index_arr_label_data."', '".$sl_detail["label_type"]."', 'no','[{LABELS}]','".$procedure_limit."','".$arr_user_types[$pr_id]."', '".$sl_detail["label_group"]."');TestOnMenu();\" onmousedown = \"set_replace_label('".$index_arr_label_data."');\" $dblClickEvt_dem ";
											
											$str_appt_slots .= "<div id=\"more_options_".$times_from."\" ".$tdMouseUp2." class=\"fl ".$reduced_coloring."\" style=\"cursor:pointer;".$lbl_border."background-color:".$slot_color.";height:".$divHeight."px;width:".($div_width)."%;z-index:".$divZindex.";left:[{(LEFT-1LEFT)}]%;\" title=\"".$arr_label_data[0]."\">";
												$str_appt_slots .= "<span class=\"sdf\" style=\"font-weight:bold;color:#000000;\">".$arr_label_data[0]."&nbsp; </span>";
												$str_appt_slots .= "</div>";
											}
										}
									}else{
										$str_appt_slots .=  "<div><strong>".stripslashes($sl_detail["label"])."</strong></div>";
									}
								}
								if($sl_detail["status"] == "off"){
									$str_appt_slots .= "</div><div class=\"fl off\" style=\"height:".($tp_sl_height)."px\" title=\"Office is closed.\"></div></div> ";
								}else{
									$str_appt_slots .= "</div><div class=\"fl\" style=\"height:".($tp_sl_height)."px;width:10px;background-color:".(($arr_all_fac[$sl_detail["fac_id"]]["facility_color"] == "") ? "#9d9a8b" : $arr_all_fac[$sl_detail["fac_id"]]["facility_color"])."\" title=\"".$mouse_over_slot_detail."\"></div></div> ";
								}
							}else{
								$str_appt_slots .= "<div style=\"width:100%;height:".$tp_sl_height."px\">";
								if(($admin_priv == 1 || $sch_overrider_privilege==1) && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
									$str_appt_slots .= "<div class=\"fl\" style=\"height:".$tp_sl_height."px;\"><div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); top.fAlert('Office is Closed.');\"></div></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".DEFAULT_OFFICE_CLOSED_COLOR.";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); top.fAlert('Office is Closed.');stopEventsinSch();\"  onMouseDown=\"pop_menu_time('".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','off', '".$label_type."', '".stripslashes($sl_detail["label"])."', '".$slot_color."','".$sl_detail["tmpId"]."'); \"  title=\"Office is closed.\"></div><div class=\"fl off\" style=\"height:".($tp_sl_height)."px\" title=\"Office is closed.\"></div></div> ";
								}else{
									$str_appt_slots .= "<div class=\"fl\" style=\"height:".$tp_sl_height."px;\"><div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); top.fAlert('Office is Closed.');\"></div></div><div class=\"fl slt_border cls_".$innerContainer."\" style=\"height:".$tp_sl_height."px;width:".$innerContainer."%;background-color:".DEFAULT_OFFICE_CLOSED_COLOR.";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('Locked Time.');stopEventsinSch();\" title=\"Office is closed.\" ></div></div> ";
								}	
							}
						}//slots foreach ends here
						$str_appt_slots .= "</div></div>";
						//owl specific div
						//$str_appt_slots .= "</div>";
						$intCnt++;
					}
				}
			}
			if(is_array($arr_slot_content) && count($arr_slot_content) > 0){
				$arr_new_slot_content = array();
				$arr_new_slot_content_tmp = array();
				foreach($arr_slot_content as $pr_key => $this_pr_arr_slot_content){
					if(is_array($this_pr_arr_slot_content) && count($this_pr_arr_slot_content) > 0){
						foreach($this_pr_arr_slot_content as $this_timings => $this_arr_value){
							ksort($this_arr_value);
							$arr_new_slot_content_tmp[$this_timings] = $this_arr_value;
						}
						$arr_new_slot_content[$pr_key] = $arr_new_slot_content_tmp;
					}
				}
				$arr_slot_content = $arr_new_slot_content;
				$arr_slot_content_tmp = $arr_slot_content;					
				$arr_appt_widths = array();
				$arr_appt_slot_widths = array();
				$int_max_cnt_val = 0;
				foreach($arr_xml as $pr_id => $pr_detail){
					if($pr_id != "dt"){
						//determining whether to process or not
						$bl_process = false;
						if(count($arr_sel_fac) > 0){
							$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
							for($multi = 0; $multi < count($arr_multi_fac); $multi++){
								if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){
									if(count($arr_sel_prov) > 0){
										if(in_array($pr_detail["id"], $arr_sel_prov)){
											$bl_process = true;
											break;
										}
									}else{
										$bl_process = true;
										break;
									}
								}
							}
						}else{
							if(count($arr_sel_prov) > 0){
								if(in_array($pr_id, $arr_sel_prov)){
									$bl_process = true;
								}
							}else{
								$bl_process = true;
							}
						}
						//getting facilites to process
						$str_process_fac = $pr_detail["fac_ids"];
						$arr_process_fac = explode(",", $pr_detail["fac_ids"]);
						if(count($arr_sel_fac) > 0){	
							$temp_arr_process_fac = $arr_process_fac;
							for($multip = 0; $multip < count($temp_arr_process_fac); $multip++){
								if(!in_array($temp_arr_process_fac[$multip], $arr_sel_fac)){
									unset($arr_process_fac[$multip]);
								}
							}
							$str_process_fac = implode(",", $arr_process_fac);
						}						
						if($bl_process == true){
							$int_max_cnt_val = 0;
							foreach($pr_detail["slots"] as $sl_id => $sl_detail){									
								$intStartHr = substr($sl_id, 0, 2);
								$intStartMin = substr($sl_id, 3, 2);
								$times_from = $intStartHr.":".$intStartMin.":00";								
								if(in_array($sl_detail["fac_id"], $arr_process_fac)){	
									if(isset($arr_slot_content_tmp[$pr_id][$times_from])){											
										if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0 && count($arr_slot_content_tmp[$pr_id][$times_from]) > $int_max_cnt_val){
											$int_max_cnt_val = count($arr_slot_content_tmp[$pr_id][$times_from]);
										}
									}
								}
							}							
							if($int_max_cnt_val > 0){									
								$arr_appt_widths[$pr_id] = ($div_width) / $int_max_cnt_val;
								foreach($pr_detail["slots"] as $sl_id => $sl_detail){										
									$int_max_cnt_slot_val = $int_max_cnt_val;
									$intStartHr = substr($sl_id, 0, 2);
									$intStartMin = substr($sl_id, 3, 2);
									$times_from = $intStartHr.":".$intStartMin.":00";									
									if(in_array($sl_detail["fac_id"], $arr_process_fac)){	
										if(isset($arr_slot_content_tmp[$pr_id][$times_from])){												
											if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0 && count($arr_slot_content_tmp[$pr_id][$times_from]) == 1){
												$int_max_cnt_slot_val = $div_width - 1;
											}
											if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0 && count($arr_slot_content_tmp[$pr_id][$times_from]) == 2){
												$int_max_cnt_slot_val = ($div_width - 1) / 2;
											}
											if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0 && count($arr_slot_content_tmp[$pr_id][$times_from]) == 3){
												$int_max_cnt_slot_val = ($div_width - 1) / 3;
											}
										}
									}
									$arr_appt_slot_widths[$pr_id][$times_from] = $int_max_cnt_slot_val;
								}
							}
						}
					}
				}
				//replacing widths in appts				
				$str_slot_tmp_html = "";
				foreach($arr_xml as $pr_id => $pr_detail){
					if($pr_id != "dt"){
						//determining whether to process or not
						$bl_process = false;
						if(count($arr_sel_fac) > 0){
							$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
							for($multi = 0; $multi < count($arr_multi_fac); $multi++){
								if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){
									if(count($arr_sel_prov) > 0){
										if(in_array($pr_detail["id"], $arr_sel_prov)){
											$bl_process = true;
											break;
										}
									}else{
										$bl_process = true;
										break;
									}
								}
							}
						}else{
							if(count($arr_sel_prov) > 0){
								if(in_array($pr_id, $arr_sel_prov)){
									$bl_process = true;
								}
							}else{
								$bl_process = true;
							}
						}
						//getting facilites to process
						$str_process_fac = $pr_detail["fac_ids"];
						$arr_process_fac = explode(",", $pr_detail["fac_ids"]);
						if(count($arr_sel_fac) > 0){	
							$temp_arr_process_fac = $arr_process_fac;
							for($multip = 0; $multip < count($temp_arr_process_fac); $multip++){
								if(!in_array($temp_arr_process_fac[$multip], $arr_sel_fac)){
									unset($arr_process_fac[$multip]);
								}
							}
							$str_process_fac = implode(",", $arr_process_fac);
						}
						if($bl_process == true){							
							foreach($pr_detail["slots"] as $sl_id => $sl_detail){								
								$left = 0;
								$str_slot_tmp_html = "";								
								$intStartHr = substr($sl_id, 0, 2);
								$intStartMin = substr($sl_id, 3, 2);
								$times_from = $intStartHr.":".$intStartMin.":00";								
								if(in_array($sl_detail["fac_id"], $arr_process_fac)){
									if(isset($arr_slot_content[$pr_id][$times_from])){
										if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0){
											$first_appt_loaded = 0;
											$left = 0;
											foreach($arr_slot_content_tmp[$pr_id][$times_from] as $appt_det){												
												$left = ($arr_appt_widths[$pr_id] * $appt_det["position"]) - $arr_appt_widths[$pr_id];

												if(count($arr_slot_content_tmp[$pr_id][$times_from]) == 1 && ($appt_det["rept"] < 2 || $appt_det["rept"] == "")){
													$str_slot_tmp_html_tmp = str_replace("[{(LEFT".$appt_det["appt"]."LEFT)}]", $left, $appt_det["html"]);
													$str_slot_tmp_html_tmp = str_replace("[{(WIDTH".$appt_det["appt"]."WIDTH)}]", $arr_appt_slot_widths[$pr_id][$times_from], $str_slot_tmp_html_tmp);
													$str_slot_tmp_html_tmp = str_replace("[{(DEBUGGER)}]", "", $str_slot_tmp_html_tmp);													
													$str_slot_tmp_html .= $str_slot_tmp_html_tmp;
												}else if(count($arr_slot_content_tmp[$pr_id][$times_from]) == 2 && ($appt_det["rept"] < 2 || $appt_det["rept"] == "")){
													$left = ($arr_appt_slot_widths[$pr_id][$times_from] * $appt_det["position"]) - $arr_appt_slot_widths[$pr_id][$times_from];
													$str_slot_tmp_html_tmp = str_replace("[{(LEFT".$appt_det["appt"]."LEFT)}]", $left, $appt_det["html"]);
													$str_slot_tmp_html_tmp = str_replace("[{(WIDTH".$appt_det["appt"]."WIDTH)}]", $arr_appt_slot_widths[$pr_id][$times_from], $str_slot_tmp_html_tmp);
													$str_slot_tmp_html_tmp = str_replace("[{(DEBUGGER)}]", "", $str_slot_tmp_html_tmp);													

													$str_slot_tmp_html .= $str_slot_tmp_html_tmp;
												}else if(count($arr_slot_content_tmp[$pr_id][$times_from]) == 3 && ($appt_det["rept"] < 2 || $appt_det["rept"] == "")){
													$left = ($arr_appt_slot_widths[$pr_id][$times_from] * $appt_det["position"]) - $arr_appt_slot_widths[$pr_id][$times_from];
													$str_slot_tmp_html_tmp = str_replace("[{(LEFT".$appt_det["appt"]."LEFT)}]", $left, $appt_det["html"]);
													$str_slot_tmp_html_tmp = str_replace("[{(WIDTH".$appt_det["appt"]."WIDTH)}]", $arr_appt_slot_widths[$pr_id][$times_from], $str_slot_tmp_html_tmp);
													$str_slot_tmp_html_tmp = str_replace("[{(DEBUGGER)}]", "", $str_slot_tmp_html_tmp);													
													$str_slot_tmp_html .= $str_slot_tmp_html_tmp;
												}else{
													$str_slot_tmp_html_tmp = str_replace("[{(LEFT".$appt_det["appt"]."LEFT)}]", $left, $appt_det["html"]);
													$str_slot_tmp_html_tmp = str_replace("[{(WIDTH".$appt_det["appt"]."WIDTH)}]", $arr_appt_widths[$pr_id], $str_slot_tmp_html_tmp);
													$str_slot_tmp_html_tmp = str_replace("[{(DEBUGGER)}]", "", $str_slot_tmp_html_tmp);													
													$str_slot_tmp_html .= $str_slot_tmp_html_tmp;
												}
											}
										}
									}
								}
								$str_appt_slots = str_replace("[{(".$pr_id."::".$times_from.")}]", $str_slot_tmp_html, $str_appt_slots);
								#remove left rail specific string
								$str_appt_slots = str_replace("[{LABELS}]", "", $str_appt_slots);
							}
						}
					}
				}
			}
		}
		#remove left rail specific string
		$str_appt_slots = str_replace("[{LABELS}]", "", $str_appt_slots);
		//print "<textarea>".$str_appt_slots."</textarea>";
		$total_prov=(isset($total_prov))?$total_prov:0;
		$arr_widths=(isset($arr_widths))?$arr_widths:'';
		return array($str_header, $str_time_pane, $str_appt_slots, $str_proc_summary, $total_prov, $arr_widths, $arr_not_processed, $arr_processed);
	}
	
	
	/*
	Function: set_column_width
	Purpose: to set the column width according to the number of providers on screen
	Author: ravi, prabh
	Returns: ARRAY containing Headings, Appt Time Slots, And Time Pane
	*/
	function set_column_width($arr_xml, $arr_sel_fac = array(), $arr_sel_prov = array()){
		$prov_no = 0;
		$column_width = 0;
		$innerContainer = 0;
		$scroll_width = 0;
		$div_width = 0;
		foreach($arr_xml as $pr_id => $pr_detail){
			
			if($pr_id != "dt" && isset($pr_detail["name"])){
				
				if(count($arr_sel_fac) > 0){
					
					$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
					
					for($multi = 0; $multi < count($arr_multi_fac); $multi++){
						if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){
							
							if(count($arr_sel_prov) > 0){
								
								if(in_array($pr_detail["id"], $arr_sel_prov)){
									
									$prov_no++;
									break;
								}
							}else{
								$prov_no++;
								break;
							}
						}
					}
				}else{
					if(count($arr_sel_prov) > 0){
						if(in_array($pr_detail["id"], $arr_sel_prov)){
							$prov_no++;
						}
					}else{
						$prov_no++;
					}
				}			
			}
		}
		//NOTE :: we have same name classes as $innerContainer width so to make any change here we have make change in schedulermain.css too
		if($prov_no > 5){
			$column_width = round((100/$prov_no),2);//210
			$innerContainer=90;
			$scroll_width = 93;//210
			$div_width = 91;//172
		}
		elseif($prov_no == 5){
			$column_width = 20;//210
			$innerContainer=90;
			$scroll_width = 93;//210
			$div_width = 91;//172
		}else if($prov_no == 4){
			$column_width = 25;//210
			$innerContainer=91;
			$scroll_width = 94;//210
			$div_width = 92;//172
		}else if($prov_no == 3){
			$column_width = 33;//210
			$innerContainer=92;
			$scroll_width = 96;//210
			$div_width = 94;//172
		}else if($prov_no == 2){		
			$column_width = 50;//315
			$innerContainer=93;
			$scroll_width = 97;//$prov_no * 50;//315
			$div_width = 95;//280
		}else{
			$column_width = 100;//630
			$innerContainer=94;
			$scroll_width = 100;//680
			$div_width = 96;//590
		}
		//commenting this code due we do not want this in new scroller idea
		/*if($this->schScrollWidthFlag == 1) 
		{
			$column_width = 33;//210
			$innerContainer=94;
			$scroll_width = 97;	//220		
			$div_width = 94;//170
		}*/
		
		
		return array($column_width, $innerContainer, $scroll_width, $div_width, $prov_no);
	}
	/*
	Function: doctor_proc_to_default_proc
	Purpose: to get default procedure id from doctor specific proc id
	Author: ravi, prabh
	Returns: INT
	*/
	function doctor_proc_to_default_proc($appt_procedure_id){

		$proc_id_qry = "SELECT procedureId FROM slot_procedures WHERE id = '".$appt_procedure_id."'";
		$res_proc_id = imw_query($proc_id_qry);
		$arr_proc_id = array();
		if(imw_num_rows($res_proc_id) > 0){
			$arr_proc_id = imw_fetch_assoc($res_proc_id);
		}
		if($arr_proc_id["procedureId"] == 0){
			$default_procedure_id = $appt_procedure_id;
		}else{
			$default_procedure_id = $arr_proc_id["procedureId"];
		}

		return $default_procedure_id;
	}
	
	function plugin_to_show_10min_appt($arrStartTiming){
		$arrNewStartTiming = array();
		if(is_array($arrStartTiming) && count($arrStartTiming) > 0){
			foreach($arrStartTiming as $k => $v){
				$arrK = explode(":", $k);
				list($h, $m, $s) = $arrK;
				$old_m = $m;
				switch($m){
					case 5: case 20: case 35: case 50:		$m = $m-5;		break;
					case 10: case 25: case 40: case 55:		$m = $m+5;		break;
				}
				
				if($m != $old_m){
					if($m == 0){
						$m = "00";
					}
					$k = $h.":".$m.":".$s;
					//modifying value
					for($iV = 0; $iV < count($v); $iV++){
						$v[$iV]['sa_app_starttime'] = $k;
						$v[$iV][1] = $k;
						$v[$iV]['timeadjusted'] = "yes";
					}
				}else{
					for($iV = 0; $iV < count($v); $iV++){
						$v[$iV]['timeadjusted'] = "no";
					}
				}	
						
				$exist_appt_arr_len = count($arrNewStartTiming[$k]); 
				$cur_appt_filter_arr_len = count($v);
				if($exist_appt_arr_len > 0)
				{
					$jk = 0;
					for($ik = $exist_appt_arr_len; $ik < $exist_appt_arr_len+$cur_appt_filter_arr_len;$ik++,$jk++)
					{
						$arrNewStartTiming[$k][$ik] = $v[$jk];		
					}
				}
				else
				{
					$arrNewStartTiming[$k]= $v;										
				}
			}
		}
		return $arrNewStartTiming;
	}

	/*
	Purpose: this function adjusts the end time of all the appointments with time as multiple of 5min to show them in 10 min slots
	Author: ravi, prabh
	Returns: ARRAY containing adjustded timings
	*/
	function plugin_to_show_5min_appt($arrStartTiming){
		$arrNewStartTiming = array();
		if(is_array($arrStartTiming) && count($arrStartTiming) > 0){
			foreach($arrStartTiming as $k => $v){
				$arrK = explode(":", $k);
				list($h, $m, $s) = $arrK;
				if($m%10 != 0 && $m%5 == 0){
					//modifying key
					$m = $m-5;
					if($m == 0){
						$m = "00";
					}
					$k = $h.":".$m.":".$s;
					//modifying value
					for($iV = 0; $iV < count($v); $iV++){
						$v[$iV]['sa_app_starttime'] = $k;
						$v[$iV][1] = $k;
						$v[$iV]['timeadjusted'] = "yes";
					}
				}else{
					for($iV = 0; $iV < count($v); $iV++){
						$v[$iV]['timeadjusted'] = "no";
					}
				}
				
				if(array_key_exists($k,$arrNewStartTiming))
				$exist_appt_arr_len = count($arrNewStartTiming[$k]); 
				
				$cur_appt_filter_arr_len = count($v);
				if(isset($exist_appt_arr_len) && $exist_appt_arr_len > 0)
				{
					$jk = 0;
					for($ik = $exist_appt_arr_len; $ik < $exist_appt_arr_len+$cur_appt_filter_arr_len;$ik++,$jk++)
					{
						$arrNewStartTiming[$k][$ik] = $v[$jk];		
					}
				}
				else
				{
					$arrNewStartTiming[$k]= $v;										
				}				
			}
		}
		return $arrNewStartTiming;
	}

	function get_next_appt_pos($used_positions, $int_new_position=''){
		for($i = 1; $i < 20; $i++){
			if(!in_array($i, $used_positions)){
				$return = $i;
				break;
			}
		}
		return $return;
	}
	
	/*
	Purpose: to get patient details
	Returns: ARRAY if found, else false
	*/
	function get_patient_details($pat_id = 0, $pat_fields = "*"){
		$return = false;
		if($pat_id > 0){
			$qry = "select ".$pat_fields." from patient_data pd where pd.id = '".$pat_id."'";
			$res = imw_query($qry);
			
			if(imw_num_rows($res) == 0){
				$qry = "select ".$pat_fields." from patient_data pd where pd.athenaID = '".$pat_id."'";
				$res = imw_query($qry);
			}

			if(imw_num_rows($res) > 0){
				$return = imw_fetch_assoc($res);
			}	
		}
		
		return $return;
	}
	
	/*
	Purpose: to get details of selected / upcoming appointment
	Author: ravi, prabh
	Returns: ARRAY if found, else FALSE
	*/
	function get_appointment_details($sch_fields = "*", $pat_id = 0, $sch_id = 0, $where = ""){
		$return = false;
		if($sch_id > 0){
			$qry = "SELECT ".$sch_fields." FROM schedule_appointments sa INNER JOIN slot_procedures sp ON sp.id = sa.procedureid INNER JOIN users u ON u.id = sa.sa_doctor_id INNER JOIN facility f ON f.id = sa.sa_facility_id where sa_patient_id = '".$pat_id."' and sa.id = '".$sch_id."'";
		}else{
			$qry = "SELECT ".$sch_fields." FROM schedule_appointments sa INNER JOIN slot_procedures sp ON sp.id = sa.procedureid INNER JOIN users u ON u.id = sa.sa_doctor_id INNER JOIN facility f ON f.id = sa.sa_facility_id where sa_patient_id = '".$pat_id."' and sa_patient_app_status_id NOT IN(271,201,18,19,20)";
		}
		if(!empty($where)){
			$qry .= $where;
		}
		$qry .= " order by sa.sa_app_start_date, sa.sa_app_starttime ASC limit 0, 1";
		//echo $qry;
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$return = imw_fetch_assoc($res);			
		}
		return $return;
	}
	
	/**
	 * Purpose: To Get Doctor Specific Proc Id From Default Proc Id
	 * Author: Ravi, Prabh
	 * Returns: STRING
	 */
	function default_proc_to_doctor_proc($default_procedure_id, $doctor_id){
				
		$proc_id_qry = "SELECT 
							sp1.id, 
							sp2.times, 
							sp1.proc_mess,
							sp1.exp_arrival_time							
						FROM 
							slot_procedures sp1 
							LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
						WHERE 
							sp1.times = ''
						AND 
							sp1.active_status!='del'
						AND 
							sp1.proc != '' 
						AND 
							sp1.doctor_id = '".$doctor_id."' 
						AND 
							sp1.procedureId = '".$default_procedure_id."'";
		
		$res_proc_id = imw_query($proc_id_qry);
		
		$arr_proc_id = array();
		
		if(imw_num_rows($res_proc_id) > 0)
		{
			$arr_proc_id = imw_fetch_assoc($res_proc_id);
			
			if($arr_proc_id["times"] != "")
			{
				return $arr_proc_id["id"]."~".$arr_proc_id["times"]."~".$arr_proc_id["proc_mess"]."~".$arr_proc_id["exp_arrival_time"];
			}
			else
			{
				$proc_id_qry3 = "SELECT 
									sp1.id,
									sp2.times,
									sp1.proc_mess,
									sp1.exp_arrival_time	
								FROM 
									slot_procedures sp1 
									LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
								WHERE 
									sp1.times = '' 
								AND 
									sp1.proc != '' 
								AND 
									sp1.doctor_id = '0' 
								AND 
									sp1.id = '".$default_procedure_id."'
								";
								
				$res_proc_id3 = imw_query($proc_id_qry3);
				
				$arr_proc_id3 = array();
				
				if(imw_num_rows($res_proc_id3) > 0)
				{
					$arr_proc_id3 = imw_fetch_assoc($res_proc_id3);
					//echo "here";
					return $arr_proc_id["id"]."~".$arr_proc_id3["times"]."~".$arr_proc_id["proc_mess"];
				}
				else
				{
					//echo "there";
					return $arr_proc_id["id"]."~".$arr_proc_id["times"]."~".$arr_proc_id["proc_mess"]."~".$arr_proc_id["exp_arrival_time"];
				}
			}
		}
		else
		{
			$proc_id_qry2 ="SELECT 
								sp1.id,
								sp2.times,
								sp1.proc_mess,
								sp1.exp_arrival_time								
							FROM 
								slot_procedures sp1 
								LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id 
							WHERE 
								sp1.times = '' 
							AND 
								sp1.proc != '' 
							AND 
								sp1.doctor_id = '0' 
							AND 
								sp1.id = '".$default_procedure_id."'
							";
			
			$res_proc_id2 = imw_query($proc_id_qry2);
			$arr_proc_id2 = array();
			
			if(imw_num_rows($res_proc_id2) > 0)
			{
				$arr_proc_id2 = imw_fetch_assoc($res_proc_id2);
				return $arr_proc_id2["id"]."~".$arr_proc_id2["times"]."~".$arr_proc_id2["proc_mess"]."~".$arr_proc_id2["exp_arrival_time"];
			}
			else
			{
				return false;
			}
		}
	}
	
	/*
	Purpose: to get todo appointments for a patient
	Author: ravi, prabh
	Returns: STRING
	*/
	function get_todo_appointments($sch_fields = "*", $pat_id = 0, $where = ""){
		$return = false;
		if(!empty($pat_id)){
			$qry = "SELECT ".$sch_fields." FROM schedule_appointments sa 
			INNER JOIN slot_procedures sp ON sp.id = sa.procedureid 
			INNER JOIN users u ON u.id = sa.sa_doctor_id 
			INNER JOIN facility f ON f.id = sa.sa_facility_id 
			where sa_patient_id = '".$pat_id."' and sa_patient_app_status_id IN(201)";
			if(!empty($where)){
				$qry .= $where;
			}
			$res = imw_query($qry);
			if(imw_num_rows($res) > 0){
				while($ret=imw_fetch_assoc($res)){
					$return[] = $ret;		
				}
			}
		}
		return $return;
	}
	
	/*
	Purpose: to get provider details
	Author: ravi, prabh
	Returns: ARRAY if found, else false
	*/
	function get_provider_details($prov_id = 0, $prov_fields = "*"){
		$return = false;
		if($prov_id > 0){
			$qry = "select ".$prov_fields." from users where id = '".$prov_id."'";
			$res = imw_query($qry);			
			if(imw_num_rows($res) > 0){
				$return = imw_fetch_assoc($res);
			}	
		}
		return $return;
	}
	/*
	Purpose: to get provider list for scheduler
	Author: ravi, Prabh
	Returns: ARRAY if found, else false
	*/
	function get_provider_list(){
		$return = false;
		$qry = "select id, fname, lname, mname from users where Enable_Scheduler = 1 and delete_status = 0 order by lname, fname";
		$res = imw_query($qry);			
		if(imw_num_rows($res) > 0){
			while($ret=imw_fetch_assoc($res))
			{
				$return[] = $ret;	
			}
		}
		return $return;
	}
	
	/*
	Function: update_recent_pt_list
	Purpose: to update the list of the patients searched recently by the logged in user
	Author: AA
	Returns: NULL
	*/
	function update_recent_pt_list($prov_id, $pat_id, $pat_status){
		$is_in_recent_pt_list = false;
		
		//getting recent list
		$qry = "select patient_id, recent_user_id from recent_users where provider_id = '".$prov_id."' order by enter_date desc";
		$res = imw_query($qry);
		$int_list_cnt=imw_num_rows($res);
		if($int_list_cnt > 0){
			while($arrTmp=imw_fetch_assoc($res))
			{
				$arr[]=	$arrTmp;
			}
			
			for($i = 0; $i < $int_list_cnt; $i++)
			{
				if($pat_id == $arr[$i]["patient_id"]){
					$is_in_recent_pt_list = true;
					$recent_user_id = $arr[$i]["recent_user_id"];

					$qry = "update recent_users set patient_id = '".$pat_id."', enter_date = '".date('Y-m-d H:i:s')."' where recent_user_id = '".$recent_user_id."'";
					imw_query($qry);
				}
			}
			if($is_in_recent_pt_list == false){	
				if($int_list_cnt >= 5){
					$recent_user_id = $arr[4]["recent_user_id"];
					$qry = "update recent_users set patient_id = '".$pat_id."', provider_id = '".$prov_id."', patientFindBy = '".$pat_status."', enter_date = '".date('Y-m-d H:i:s')."' where recent_user_id = '".$recent_user_id."'";
				}else{			

					$qry = "insert into recent_users set patient_id = '".$pat_id."', provider_id = '".$prov_id."', patientFindBy = '".$pat_status."', enter_date = '".date('Y-m-d H:i:s')."'";
				}
				imw_query($qry);
			}
		}
	}
	
	/*
	Purpose: to reduce the size of a string appended with custom chars
	Author: Ravi, Prabh
	Returns: STRING
	*/
	function reduce_display_string($input_str, $int_check, $int_set = 0, $append_chars = ".."){
		if($int_set == 0) $int_set = $int_check - 3;
		if(strlen($input_str) >= $int_check){
			return substr($input_str, 0, $int_set).$append_chars;
		}else{
			return $input_str;
		}
	}
	/*
	Purpose: to calculate age from the DOB
	Author: Ravi, Prabh
	Returns: STRING
	Arguments: DOB in Y-m-d format
	*/
	function get_age($dob){
		$qry = "SELECT TIMESTAMPDIFF(YEAR,'".$dob."','".date('Y-m-d H:i:s')."') AS age";
		$res = imw_query($qry);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			$return = $arr["age"];
		}
		return $return;		
	}
	
	
	/*
	Purpose: to highlight provider schedules in the calendar if single provider is selected
	Author: Ravi, Prabh
	Returns: STRING with Highlighting HTML
	*/
	function highlight_provider_schedules($working_day_dt, $j = 0, $prov_id = 0, $fac_ids = array(), $max_appt = array(), $arr_all_tmp = array()){
		list($year, $month, $day) = explode("-", $working_day_dt);
		$return = "";
		if(count($fac_ids) > 0 && $prov_id > 0){

			##############################
			#getting schedules
			##############################

			$int_total_secs = 0;

			$arr_prov_sch = $this->get_provider_schedules($working_day_dt, array(0 => $prov_id));
			//once again re-getting prov sch temp data in order to get the records in proper order
			$str_final_tmp = "";
			$str_sch_tmp_id = "";
			$arr_final_tmp = array();
			$arr_sch_tmp_id = array();
			$sch_tmp_avail_ids_us_h = array();
			$tmp_max_appointments_arr_h = array();
						
			for($i = 0; $i < count($arr_prov_sch); $i++){
				$arr_final_tmp[] = $arr_prov_sch[$i]["id"];
				$arr_sch_tmp_id[] = $arr_prov_sch[$i]["sch_tmp_id"];
				if(in_array($arr_prov_sch[$i]["facility"],$fac_ids))
				{
					$sch_tmp_avail_ids_us_h[$arr_prov_sch[$i]["facility"]][] = $arr_prov_sch[$i]['sch_tmp_id'];		
				}				
			}
			$str_final_tmp = join(",", $arr_final_tmp);
			$str_sch_tmp_id = join(",", $arr_sch_tmp_id);
			
			foreach($sch_tmp_avail_ids_us_h as $selected_fac_us => $sch_tmp_avail_ids_by_fac)
			{
				if(sizeof($max_appt)<=0)
				{	
					$sch_temp_ids_for_req = implode(',',$sch_tmp_avail_ids_by_fac);
					$max_appt_qry = "SELECT SUM(schedule_templates.MaxAppointments) as max_appts FROM schedule_templates WHERE id IN(".$sch_temp_ids_for_req.")";
					$max_appt_qry_obj = imw_query($max_appt_qry);
					$max_appt_qry_obj_result = imw_fetch_assoc($max_appt_qry_obj);							
					$tmp_max_appointments_arr_h[$selected_fac_us] = $max_appt_qry_obj_result['max_appts'];	
				}else{
					foreach($sch_tmp_avail_ids_by_fac as $temp_id)
					{
					 	$temp_id=trim($temp_id);
						if(isset($max_appt[$temp_id]) && $max_appt[$temp_id]>0)
						{
							$tmp_max_appointments_arr_h[$selected_fac_us] += (int)$max_appt[$temp_id];
						}
					}
				}								 
			}
		if($str_final_tmp){		
			if(sizeof($arr_all_tmp)<=0){
				//getting timings for office
				$arr_all_tmp = array();
				$str_tmp = "select id, TIME_FORMAT(morning_start_time, '%h:%i %p') as morning_start_time, morning_start_time as raw_morning_start_time, TIME_FORMAT(morning_end_time, '%h:%i %p') as morning_end_time, morning_end_time as raw_morning_end_time from schedule_templates order by id";
				$res_tmp = imw_query($str_tmp);
				if(imw_num_rows($res_tmp) > 0){
					while($arr_tmp=imw_fetch_assoc($res_tmp)){
						$arr_all_tmp[$arr_tmp["id"]]["start_time"] = $arr_tmp["morning_start_time"];
						$arr_all_tmp[$arr_tmp["id"]]["end_time"] = $arr_tmp["morning_end_time"];
						$arr_all_tmp[$arr_tmp["id"]]["raw_start_time"] = $arr_tmp["raw_morning_start_time"];
						$arr_all_tmp[$arr_tmp["id"]]["raw_end_time"] = $arr_tmp["raw_morning_end_time"];
					}
				}
			}
			//get child template on basis of date 
			$qStr="select pid, sch_tmp_id, sch_tmp_pid from provider_schedule_tmp_child 
					WHERE status=1 
					AND IF(UNIX_TIMESTAMP(start_date) != 0, '$working_day_dt' BETWEEN start_date AND end_date, 1=1) 
					AND pid IN($str_final_tmp)";
			$query=imw_query($qStr);
			while($data=imw_fetch_object($query))
			{
				$sch_tmp_id_arr[$data->pid]=$data->sch_tmp_id;
			}
			
			$str_fnl = "select f.id, f.name, u.id as uid, u.lname, u.fname, u.provider_color, u.user_type, u.max_appoint, pst.facility, pst.sch_tmp_id, pst.provider, pst.id AS pid from provider_schedule_tmp pst join facility f on pst.facility = f.id join users u on pst.provider = u.id where pst.id in (".$str_final_tmp.") and u.delete_status = 0 and u.Enable_Scheduler = 1 group by facility,sch_tmp_id, provider order by u.id";
			$res_fnl = imw_query($str_fnl);
			if(imw_num_rows($res_fnl) > 0){
				$processed = 0;
				while($arr_fnl=imw_fetch_assoc($res_fnl)){
					if(in_array($arr_fnl["id"], $fac_ids)){
						if($sch_tmp_id_arr[$arr_fnl["pid"]])$arr_fnl["sch_tmp_id"]=$sch_tmp_id_arr[$arr_fnl["pid"]];
						$processed++;
						$str_fac_name = strip_tags(str_replace(array("&", "&amp;"), "and", $arr_fnl["name"]));
						$return .= str_replace(" ", "&nbsp;", $str_fac_name).":&nbsp;<strong>".str_replace(" ", "&nbsp;", trim($arr_all_tmp[$arr_fnl["sch_tmp_id"]]["start_time"]))."&nbsp;-&nbsp;".str_replace(" ", "&nbsp;", trim($arr_all_tmp[$arr_fnl["sch_tmp_id"]]["end_time"]))."</strong><br>";

						$h =  strtotime($arr_all_tmp[$arr_fnl["sch_tmp_id"]]["raw_start_time"]);
						$h2 = strtotime($arr_all_tmp[$arr_fnl["sch_tmp_id"]]["raw_end_time"]);
						
						$int_total_secs += $h2 - $h;
					}
				}
				if($processed > 0){
					$return = $pfx_return.$return;
				}				
			}
			}
		}
		return array($return, $int_total_secs, $tmp_max_appointments_arr_h);
	}
	 
	function get_labels_by_provider($file_name)
	{
		$response_string=file_get_contents($file_name);
		echo $response_string;	
	}
	
	function get_hl_dates_by_labels($provider_dates,$selected_date,$provider_id,$selected_facilities,$labels_arr)
	{		
		$provider_dates=$provider_dates.','.$selected_date;
		$selected_facilities_arr_ex=explode(',',$selected_facilities);
		$selected_facilities_arr=array();
		foreach($selected_facilities_arr_ex as $fac_id_val)
		{
			$selected_facilities_arr[]=trim($fac_id_val);
		}
		$provider_dates_arr=explode(',',$provider_dates);
		$labels_exist_dates=array();
		
		foreach($provider_dates_arr as $p_date)
		{
			$sch_file=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$p_date.'-'.$provider_id.'.sch';
			if(file_exists($sch_file))
			{
				$sch_file_content=file_get_contents($sch_file);
				$sch_tmp_content=unserialize($sch_file_content);
				if(is_array($sch_tmp_content))
				{
					$sch_tmp_slot=$sch_tmp_content[$provider_id]['slots'];
					
					$sch_custom_lbl_qry='SELECT date_format(start_time,"%H:%i") as start_time,date_format(end_time,"%H:%i") as end_time, l_type, label_group, l_text, l_show_text, label_group FROM scheduler_custom_labels WHERE provider='.$provider_id.' and facility IN ('.$selected_facilities.') and start_date="'.$p_date.'" ORDER BY start_time';
					$sch_custom_lbl_qry_obj=imw_query($sch_custom_lbl_qry);
					
					if(imw_num_rows($sch_custom_lbl_qry_obj) > 0)
					{
						while($tmp_obj=imw_fetch_assoc($sch_custom_lbl_qry_obj))
						{
							$sch_custom_lbl_arr[]=$tmp_obj;		
						}
						
						foreach($sch_custom_lbl_arr as $sctarr)
						{
							$sc_st_time=trim($sctarr['start_time']);
							$sc_ed_time=trim($sctarr['end_time']);
							$sch_time_slot_key=$sc_st_time.'-'.$sc_ed_time;
							if(isset($sch_tmp_slot[$sch_time_slot_key]))
							{
								$sch_tmp_slot[$sch_time_slot_key]['label_type']=$sctarr['l_type'];
								$sch_tmp_slot[$sch_time_slot_key]['label']=$sctarr['l_show_text'];
								$sch_tmp_slot[$sch_time_slot_key]['l_text']=$sctarr['l_text'];
								$sch_tmp_slot[$sch_time_slot_key]['label_group']=$sctarr['label_group'];									
							}
						}
					}	

					foreach($sch_tmp_slot as $st_key => $st_arr)
					{
						if($st_arr['status']=="on" && in_array(trim($st_arr['fac_id']),$selected_facilities_arr))
						{															
							if(trim($st_arr['label'])!="")
							{
								if($st_arr['label_group']==1)$sch_get_lbl_arr[0]=$st_arr['label'];
								else $sch_get_lbl_arr=explode(';',$st_arr['label']);
								
								$sch_get_lbl_type=trim($st_arr['label_type']);
								
								foreach($sch_get_lbl_arr as $sch_label_val)
								{
									$slvwp=trim($sch_label_val)."~~".$sch_get_lbl_type;
									
									if(in_array($slvwp,$labels_arr))
									{
										$labels_exist_dates[]="'".$p_date."'";

										break 2;
									}
								}	
							}
							else
							{
								$sl_nlbl = 'Slot without labels~~NA';
								if(trim($st_arr['label'])=="" && trim($st_arr['label_type'])=="" && in_array($sl_nlbl,$labels_arr))
								{
									$labels_exist_dates[]="'".$p_date."'";
									break;	
								}
							}							
						}
					}	
				}								
			}
		}
		
		echo json_encode($labels_exist_dates);
	} 
	
	function save_clbl_get_case($ap_procedure = "", $tempproc = ""){
		$return = "";
		$ap_procedure=trim($ap_procedure);
		$tempproc=trim($tempproc);
		if(!empty($ap_procedure)){
			if(!empty($tempproc)){
				$return = "TEMP_APPT";
			}else{
				$return = "APPT";
			}
		}else{
			if(!empty($tempproc)){
				$return = "TEMP";
			}
		}
		return $return;
	}
	
	function set_labels_on_template($obj_db, $arr_custom_lbl_id, $doctor_id, $facility_id, $start_date, $start_time, $tempproc, $tmp_id, $start_loop_time, $end_loop_time, $ap_procedure_match, $ap_label_match=''){

		if($ap_label_match)
		{
			$lbl_str=str_replace('~:~',';',strtolower($ap_label_match));
			$lbl_str=str_replace('; ',';',$lbl_str);
			//match label first
			$ap_label_match_temp_arr=explode(';',$lbl_str);
			
			foreach($ap_label_match_temp_arr as $key=>$val)
			{
				$ap_label_match_arr[$key]=trim($val);
			}
		}
		$qry0 = "select id, l_text, label_group, l_show_text, labels_replaced from scheduler_custom_labels where provider = '".$doctor_id."' and facility = '".$facility_id."' and start_date = '".$start_date."' and start_time >= '".$start_loop_time."' and end_time <= '".$end_loop_time."' LIMIT 1";
		
		$res0 = imw_query($qry0);
		if(imw_num_rows($res0) > 0){ 
			$arr0=imw_fetch_assoc($res0);
			
			//remove spaces
			if($arr0["label_group"]==1)$arr_proc_acro[] = $arr0["l_show_text"];
			else $arr_proc_acro = explode(";", $arr0["l_show_text"]);
			foreach($arr_proc_acro as $key => $value)
			{
				$arr_proc_acro[$key] = trim($value);	
			}
			
			$tempproc = trim($tempproc);
			if(count($arr_proc_acro) > 0 && trim($arr_proc_acro[0]) != ""){
				//$ap_label_match_arr,$arr_proc_acro
				$arr_proc_acro_temp = array_map('strtolower', $arr_proc_acro);
				//match label
				if($matched_arr=array_intersect($ap_label_match_arr,$arr_proc_acro_temp)){
					$matched_arr=array_values($matched_arr);
					$new_l_show_text = array();
					$match_found = false;
					//match procedure acronyam with label
					foreach($arr_proc_acro as $this_proc_acro){
						if(trim(strtolower($this_proc_acro)) == strtolower($matched_arr[0]) && $match_found == false){
							$match_found = true;
							//if(!$_REQUEST['matched_lbl']){
							$_REQUEST['matched_lbl'][$arr0["id"]]=$this_proc_acro;
							//}
						}else{
							$new_l_show_text[] = trim($this_proc_acro);
						}
					}
					
					$text_to_add = implode("; ", $new_l_show_text);
					$qry = "UPDATE scheduler_custom_labels SET system_action = '1', l_show_text = '".$text_to_add."', time_status = '".date('Y-m-d H:i:s')."' WHERE id = '".$arr0["id"]."'";
					imw_query($qry);
					array_push($arr_custom_lbl_id, $arr0["id"]);
				}
				//if no match found then match acronyam
				if(!$match_found)
				{
				if(in_array($tempproc, $arr_proc_acro)){
					$new_l_show_text = array();
					$match_found = false;
					//match procedure acronyam with label
					foreach($arr_proc_acro as $this_proc_acro){
						if(trim($this_proc_acro) == $tempproc && $match_found == false){
							$match_found = true;
						}else{
							$new_l_show_text[] = trim($this_proc_acro);
						}
					}
					$text_to_add = implode("; ", $new_l_show_text);
					$qry = "UPDATE scheduler_custom_labels SET system_action = '1', l_show_text = '".$text_to_add."', time_status = '".date('Y-m-d H:i:s')."' WHERE id = '".$arr0["id"]."'";
					imw_query($qry);
					array_push($arr_custom_lbl_id, $arr0["id"]);
				}
				}
			}
		}else{ 
			//getting schedule template labels
			$qry2 = "SELECT template_label, label_type, label_color, label_group FROM schedule_label_tbl WHERE sch_template_id = '".$tmp_id."' AND TIME_FORMAT(start_time, '%H:%i:00') = '".$start_loop_time."' AND TIME_FORMAT(end_time, '%H:%i:00') = '".$end_loop_time."' ";
			$res2 = imw_query($qry2);
			if(imw_num_rows($res2) > 0)
			{ 
				while($tmp_data=imw_fetch_assoc($res2))
				{
					$arr2[]=$tmp_data;	
				}
				$arr_proc_acro=array();
				if($arr2[0]['label_group']==1)
				{$arr_proc_acro[0] = $arr2[0]["template_label"];}
				else{$arr_proc_acro = explode(";", $arr2[0]["template_label"]);}
				
				foreach($arr_proc_acro as $key => $value)
				{
					$arr_proc_acro[$key] = trim($value);
				}
				$tempproc = trim($tempproc);
				if(count($arr_proc_acro) > 0 && trim($arr_proc_acro[0]) != ""){
					if(in_array($tempproc, $arr_proc_acro)){
						$new_l_show_text = array();
						$match_found = false;
						foreach($arr_proc_acro as $this_proc_acro){
							if(trim($this_proc_acro) == $tempproc && $match_found == false){
								$match_found = true;
							}else{
								$new_l_show_text[] = trim($this_proc_acro);
							}
						}
						$text_to_add = implode("; ", $new_l_show_text);
						
						$qry = "INSERT INTO scheduler_custom_labels set provider = '".$doctor_id."', facility = '".$facility_id."', start_date = '".$start_date."', start_time = '".$start_loop_time."', end_time = '".$end_loop_time."', l_text = '".$arr2[0]["template_label"]."', l_show_text = '".$text_to_add."', l_type = '".addslashes($arr2[0]["label_type"])."', label_group = '".addslashes($arr2[0]["label_group"])."', l_color = '".addslashes($arr2[0]["label_color"])."', time_status = '".date('Y-m-d H:i:s')."', system_action = '1' ";
						imw_query($qry);
						array_push($arr_custom_lbl_id, imw_insert_id());
					}
				}
			}
		}		
		return $arr_custom_lbl_id;
	}
	/*
	Purpose: to get duration of the appt from start and end time
	Author: Prabh
	Returns: STRING
	*/
	function get_duration($st_time, $end_time){
		
		$st_time_tmp = explode(":", $st_time);
		$ed_time_tmp = explode(":", $end_time);
		
		$st_hr = $st_time_tmp[0];
		$st_min = $st_time_tmp[1];
		$ed_hr = $ed_time_tmp[0];
		$ed_min = $ed_time_tmp[1];
			
		$st_total_mins = ($st_hr * 60) + $st_min;
		$ed_total_mins = ($ed_hr * 60) + $ed_min;
			
		if($ed_total_mins >= $st_total_mins)
		{
			$duration = ($ed_total_mins - $st_total_mins) * 60;	
		}else
		{
			$ed_total_mins = (24 * 60);			
			$duration = ($ed_total_mins - $st_total_mins) * 60;			
		}
		
		return 	$duration;		
	}
	
	/**
	 * Purpose: To Get Appointment Arrival 
	 * Author: Anand
	 * Returns: STRING
	 */
	function get_arrival_time( $st_time, $arrival_time )
	{
		$st_time_tmp = $st_total_mins = $arrival_time_duration = "";
		
		$st_time_tmp = strtotime($st_time);
		
		if( !empty( $arrival_time ) )
		{	
			$st_total_mins = $st_time_tmp-($arrival_time * 60);
			$arrival_time_duration = date('h:i A',$st_total_mins);
		}
		else if( !empty($_REQUEST["ap_arrival_time"] ))
		{
			$arrival_time_duration = $_REQUEST["ap_arrival_time"];
		}
		else
		{
		 $arrival_time_duration = '';	
		}	
		
		return 	$arrival_time_duration;		
	}
	
	//to update patient status
	function updatePatientStatus($pt_id){
		$qry = "update patient_data set patientStatus ='Active' where id='".$pt_id."'";
		imw_query($qry);	
	}
	
	
	/*
	Purpose: this function updates appointment details in schedule_appointments tabel
	Author: Ravi, Prabh
	*/
	function updateScheduleApptDetails($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId, $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername, $strNewApptComments, $intNewApptProcedureId, $blUpdateNew = false){
		
		if($blUpdateNew == false){
			$strQry = "	SELECT 
							procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime,
							sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments, iolink_iosync_waiting_id 
						FROM 
							schedule_appointments 
						WHERE 
							id = '".$intApptId."'";
			$rsData = imw_query($strQry);	
			$arrData = imw_fetch_array($rsData);
			
			$intPatientId = $arrData['sa_patient_id'];				//patient id
			$iolink_iosync_waiting_id = $arrData['iolink_iosync_waiting_id']; //USE FOR iolink-sync
			
			$dtNewApptDate = $arrData['sa_app_start_date'];			//New_appt_date
			$tmNewApptStartTime = $arrData['sa_app_starttime'];			//New_appt_start_time
			$tmNewApptEndTime = $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId = $arrData['sa_doctor_id'];			//New_provider
			$intNewApptFacilityId = $arrData['sa_facility_id'];		//New_facility
			$intNewApptProcedureId = $arrData['procedureid'];				//NewMadeBy
		}
		
		$strUpdQry = "	UPDATE schedule_appointments SET 
						sa_doctor_id = '".$intNewApptProviderId."',
						sa_patient_app_status_id = '".$intNewApptStatusId."',
						
						sa_app_starttime = '".$tmNewApptStartTime."',
						sa_app_endtime = '".$tmNewApptEndTime."',
						sa_facility_id = '".$intNewApptFacilityId."',
						sa_app_start_date = '".$dtNewApptDate."',
						sa_app_end_date = '".$dtNewApptDate."',
						procedureid = '".$intNewApptProcedureId."',
						sa_madeby = '".$strNewApptOpUsername."',
						status_update_operator_id = '".$_SESSION["authId"]."' 
						WHERE id = '".$intApptId."'";
		imw_query($strUpdQry);
		if($intNewApptStatusId == 13 || $intNewApptStatusId == 11)
		{	
			if($intPatientId == "")
			{
				$get_pt_id_qry = "SELECT sa_patient_id FROM schedule_appointments WHERE id =".$intApptId;
				$pt_id_obj = imw_query($get_pt_id_qry);	
				$pt_id_val_arr = imw_fetch_assoc($pt_id_obj);
				$intPatientId = $pt_id_val_arr['sa_patient_id'];
			}
			$action_name = $intNewApptStatusId == 13 ? 'CHECK_IN' : 'CHECK_OUT';
			patient_monitor_daily($action_name,$intPatientId,$intApptId);
		}
		
		//CODE FOR IOLINK
		if(($intNewApptStatusId=='202' || $intNewApptStatusId=='18') && (constant('DEFAULT_PRODUCT')=='imwemr' || constant("IDOC_IASC_SAME")=="YES")) {
				$iolink_patient_status='Scheduled';
				if($intNewApptStatusId=='18') { 
					$iolink_patient_status = 'Canceled'; 
					$this->setApptInIolink($iolink_iosync_waiting_id,$dtNewApptDate,$iolink_patient_status,$tmNewApptStartTime,$strNewApptComments,'','','','','');			
				}
		}
		//CODE FOR IOLINK
		
		/*********NEW HL7 ENGINE START************/
		require_once(dirname(__FILE__)."/../../../hl7sys/api/class.HL7Engine.php");
		$objHL7Engine = new HL7Engine();
		$objHL7Engine->application_module = 'scheduler';
		$objHL7Engine->msgSubType = $intNewApptStatusId;
		$objHL7Engine->source_id = $intApptId;
		$objHL7Engine->generateHL7();
		/*********NEW HL7 ENGINE END*************/
		
		/* Purpose: Generate hl7 message on saving and modification of appointment */
		$allowedEvents = $GLOBALS["HL7_SIU_EVENTS"];
		
		if(constant("HL7_SIU_GENERATION")==true && is_array($allowedEvents) && in_array($intNewApptStatusId, $allowedEvents))
		{
			require_once( dirname(__FILE__).'/../../../hl7sys/hl7GP/hl7FeedData.php');
			$hl7 = new hl7FeedData();
			
			$hl7->PD['id'] = $intPatientId;
			$hl7->PD['schid'] = $intApptId;
			
			$hl7->setTrigger('SIU', $intNewApptStatusId);
			
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
			unset($hl7);
		}
		/* End code*/
	}
	/*
	Function: setApptInIolink
	Purpose: to update appt status for iolink
	Author: Ravi, Prabh
	*/
	function setApptInIolink($iolink_iosync_waiting_id,$dtNewApptDate,$iolink_patient_status,$tmNewApptStartTime,$strNewApptComment,$pick_up_time='',$arrival_times='',$iolinkSite='',$iAscSchId,$prevDosForEMR) {
		
		global $gbl_sc_connect;
		list($server_name, $port, $uname, $pass, $dbname_temp) = $gbl_sc_connect;
		$dbname = $dbname_temp.".";

		//START CODE TO GET DB NAME OF iOLink
		$iolinkDBName = $this->getDbNameIolink($iAscSchId,$iolink_patient_status);
		if(trim($iolinkDBName)) {
			$dbname = trim($iolinkDBName).".";
		}
		//END CODE TO GET DB NAME OF iOLink

		if($iolink_iosync_waiting_id && $iolink_iosync_waiting_id!='0') {
			if($iolink_patient_status == 'Canceled') {
				$updtIolinkWaitingTblQry = "UPDATE ".$dbname."patient_in_waiting_tbl SET patient_status='".$iolink_patient_status."',comment='".addslashes($strNewApptComment)."'
											WHERE  patient_in_waiting_id = '".$iolink_iosync_waiting_id."'";
			}else{
				$updtIolinkWaitingTblQry = "UPDATE ".$dbname."patient_in_waiting_tbl 
											  SET dos				=	'".$dtNewApptDate."',
												  surgery_time		=	'".$tmNewApptStartTime."',
												  pickup_time		=	'".$pick_up_time."',
												  arrival_time		=	'".$arrival_times."',
												  site				=	'".$iolinkSite."',
												  comment			=	'".addslashes($strNewApptComment)."',
												  iAscReSyncroStatus= 	'yes',
												  reSyncroVia		=	'iAsc'
												  
											  WHERE  patient_in_waiting_id = '".$iolink_iosync_waiting_id."'";
			
			}
			$updtIolinkWaitingTblRes = imw_query($updtIolinkWaitingTblQry);
		}
		//IN EMR - CANCEL PREVIOUS APPT DATE(IF EXIST) FROM IT IS MOVED
		if($prevDosForEMR && $iAscSchId && $iolink_patient_status != 'Canceled') {
			$chkEmrApptExistQry = "SELECT stub_id FROM ".$dbname."stub_tbl WHERE patient_status='Scheduled' AND dos='".$prevDosForEMR."' AND dos >= '".date('Y-m-d')."' AND appt_id='".$iAscSchId."'";
			$chkEmrApptExistRes = imw_query($chkEmrApptExistQry);
			if(imw_num_rows($chkEmrApptExistRes)>0) {
				$cancelEmrPrevDosQry = "UPDATE ".$dbname."stub_tbl SET patient_status='Canceled' WHERE dos='".$prevDosForEMR."' AND dos >= '".date('Y-m-d')."' AND appt_id='".$iAscSchId."'";
				$cancelEmrPrevDosRes = imw_query($cancelEmrPrevDosQry);
			}
		}
		//IN EMR - CANCEL PREVIOUS APPT DATE(IF EXIST) FROM IT IS MOVED						
	}
	
		
	/*
	Function: getDbNameIolink
	Purpose: to get database name of iOLink
	Author: SP
	*/
	function getDbNameIolink($schID,$iolink_patient_status) {
		//START CODE TO GET DB NAME OF iOLink
		$dbName = '';
		$qryGetIOlinkUrl = "SELECT ic.iolink_url as iolinkUrl
							 FROM schedule_appointments sa 
							 INNER JOIN iolink_connection_settings ic ON(ic.iolink_id=sa.iolink_connection_settings_id)
							 WHERE sa.id='".$schID."' LIMIT 0,1 ";
		$resGetIOlinkUrl = imw_query($qryGetIOlinkUrl);
		
		if(imw_num_rows($resGetIOlinkUrl)>0) {
			$rowGetIOlinkUrl = imw_fetch_assoc($resGetIOlinkUrl);
			$iolinkUrl 		 = 	$rowGetIOlinkUrl["iolinkUrl"];
			$cur = curl_init();
			$postArr = array();
			$postArr['downloadForm'] 	= 'NO';
			$postArr['iolinkSync'] 		= 'get_db_name';
			curl_setopt($cur,CURLOPT_URL,$iolinkUrl);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($cur, CURLOPT_POSTFIELDS,$postArr); 
			$dbName = curl_exec($cur);
			if (curl_errno($cur)){
				echo  "Curl Error ".$iolink_patient_status." iDOC to iOLink: " . curl_error($cur). " ";
			}
			curl_close($cur);
		}
		return $dbName;
		//END CODE TO GET DB NAME OF iOLink
	}
	
	function keep_track_of_replaced_labels($obj_db, $int_lbl_id, $str_lbl, $appt_id){
		if(!empty($int_lbl_id) && (trim($str_lbl) != "" || is_array($str_lbl)) && !empty($appt_id)){
			// This qry is applied because the concat function fails in case of NULL field value exists.
			$ins = "UPDATE scheduler_custom_labels SET labels_replaced = if(labels_replaced IS NULL, '', labels_replaced) WHERE id IN (".$int_lbl_id.")"; 
			imw_query($ins);
			//if we have different label for each slot to replace
			if(is_array($str_lbl))
			{
				$int_lbl_id=str_replace("'","",$int_lbl_id);
				$int_lbl_id_arr=explode(',',$int_lbl_id);
				
				foreach($int_lbl_id_arr as $id)
				{
					$id=trim($id);
					$replaced=$str_lbl[$id];
					$ins = "UPDATE scheduler_custom_labels SET labels_replaced = CONCAT(labels_replaced, '::', '".$appt_id.":".trim($replaced)."') WHERE id ='$id'";
					imw_query($ins);	
				}
			}
			else
			{
				
				$ins = "UPDATE scheduler_custom_labels SET labels_replaced = CONCAT(labels_replaced, '::', '".$appt_id.":".trim($str_lbl)."') WHERE id IN (".$int_lbl_id.")";
				imw_query($ins);
			}
		}
	}
	
	function get_ap_ids_by_patient_and_sel_date($pt_id,$sel_date,$return=false)
	{
		$return = ($return == 'true') ? true : false;
		
		$qryAppt='SELECT DISTINCT id FROM schedule_appointments WHERE sa_patient_id='.trim($pt_id).' and sa_app_start_date="'.trim($sel_date).'" and sa_patient_app_status_id NOT IN (11,13,14,18,19,20,201,203,271)';
		$result_ap_ids_obj=imw_query($qryAppt);
		$result_ap_ids_array=array();
		
		if(imw_num_rows($result_ap_ids_obj) >0)
		{
			while($tmp_data=imw_fetch_assoc($result_ap_ids_obj))
			{
				$result_ap_ids_array[]=$tmp_data;
			}
		}
		
		$result_ids=array();
		
		foreach($result_ap_ids_array as $value)
		{
			$result_ids[]=$value['id'];
		}
		
		if( $return ) return $result_ids;
		else echo json_encode($result_ids);
	}
	
	function get_ap_ids_by_patient_and_sel_dateCheckOut($pt_id,$sel_date)
	{
		$qryAppt='SELECT DISTINCT id FROM schedule_appointments WHERE sa_patient_id='.trim($pt_id).' and sa_app_start_date="'.trim($sel_date).'" and sa_patient_app_status_id NOT IN (271,201,18,19,20,203,14,11)';
		$result_ap_ids_obj=imw_query($qryAppt);
		$result_ap_ids_array=array();
		
		if(imw_num_rows($result_ap_ids_obj) >0)
		{
			while($tmp_data=imw_fetch_assoc($result_ap_ids_obj))
			{
				$result_ap_ids_array[]=$tmp_data;
			}
		}
		
		$result_ids=array();
		
		foreach($result_ap_ids_array as $value)
		{
			$result_ids[]=$value['id'];
		}
		
		echo json_encode($result_ids);
	}
	/*
	Purpose: to manipulate string for js compatibliity
	Author: Ravi, Prabh
	Returns: STRING
	*/
	function refine_string_for_js($input_str){
		return urlencode($input_str);
	}
	// ADDED BY JASWANT
	// FUNCTION ACCEPT TIME AND CONVERT IT TO AM PM TIME
	function getAmPmTime($time){
		list($hour, $min)= explode(":", $time);	
		$retun_str='';
		$ampm = "1";
		if($hour > 12){
			$hour = (int)$hour - 12;
			if(strlen($hour) < 2){
				$hour = "0".$hour;
			}
			$ampm = "2";
		}
		if(strlen($min) < 2){
			$min = "0".$min;
		}
		$return_str=$hour."~".$min."~".$ampm;
		return $return_str;
	}
	

	/*
	Function: generate_week_list
	Purpose: to create lists for week in week view scheduler
	Author: Ravi, Prabh
	*/
	function generate_week_list($db_load_date){		
		list($yr, $mn, $dt) = explode("-", $db_load_date);
		$ts = mktime(0, 0, 0, $mn, $dt, $yr);
		$load_week = date("W", $ts);
		$load_day = date("w", $ts);
		
		$load_dt_ts = mktime(0, 0, 0, $mn, ($dt - $load_day) + 1, $yr);
		$load_dt_st = date("m-d-Y", $load_dt_ts);
		$load_dt_st	= get_date_format($load_dt_st,'mm-dd-yyyy');
		$load_dt_ed = date("m-d-Y", $load_dt_ts + (6 * 86400));
		$load_dt_ed = get_date_format($load_dt_ed,'mm-dd-yyyy');
		$str_load_wk = "<option value=\"".$load_dt_st."|".$load_dt_ed."\" selected=\"selected\">".$load_dt_st." - ".$load_dt_ed."</option>";

		$str_next_wk = "";
		$str_last_wk = "";
		$str_next = "";
		$str_last = "";
		
		for($less = 10, $plus = 1; $less > 0, $plus < 11; $less--, $plus++){
			//echo $less." - ".$plus."<br>";
			$next_dt_ts = $load_dt_ts + ($plus * 7 * 86400);
			$next_dt_st = date("m-d-Y", $next_dt_ts);
			$next_dt_st = get_date_format($next_dt_st,'mm-dd-yyyy');
			$next_dt_ed = date("m-d-Y", $next_dt_ts + (6 * 86400));
			$next_dt_ed = get_date_format($next_dt_ed,'mm-dd-yyyy');
			$str_next_wk .= "<option value=\"".$next_dt_st."|".$next_dt_ed."\">".$next_dt_st." - ".$next_dt_ed."</option>";
			$str_next = ($str_next == "") ? $next_dt_st."|".$next_dt_ed : $str_next;

			$last_dt_ts = $load_dt_ts - ($less * 7 * 86400);
			$last_dt_st = date("m-d-Y", $last_dt_ts);
			$last_dt_st = get_date_format($last_dt_st,'mm-dd-yyyy');
			$last_dt_ed = date("m-d-Y", $last_dt_ts + (6 * 86400));
			$last_dt_ed = get_date_format($last_dt_ed,'mm-dd-yyyy');
			$str_last_wk .= "<option value=\"".$last_dt_st."|".$last_dt_ed."\">".$last_dt_st." - ".$last_dt_ed."</option>";
			$str_last = ($str_last == "" && $less == 1) ? $last_dt_st."|".$last_dt_ed : $str_last;
		}
		return array($str_last_wk.$str_load_wk.$str_next_wk, $str_next, $str_last, $load_dt_st."|".$load_dt_ed);
	}
	function cache_prov_working_hrs_weekly($working_day_dt, $arr_prov_id = array(), $dir_path = "", $forcefully = false){
		if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common";
		list($yr, $mn, $dt) = explode("-", $working_day_dt);
		$ts = mktime(0, 0, 0, $mn, $dt, $yr);
		$load_week = date("W", $ts);
		$load_day = date("w", $ts);
		
		$load_dt_ts = mktime(0, 0, 0, $mn, ($dt - $load_day) + 1, $yr);
		$load_dt_st = date("m-d-Y", $load_dt_ts);
		$load_dt_ed = date("m-d-Y", $load_dt_ts + (6 * 86400));
		//echo $str_load_wk = $load_dt_st." - ".$load_dt_ed;

		for($this_ts = $load_dt_ts; $this_ts <= ($load_dt_ts + (6 * 86400)); $this_ts += 86400){
			$this_working_dt = date("Y-m-d", $this_ts);			
			$this->cache_prov_working_hrs($this_working_dt, $arr_prov_id, $dir_path, $forcefully);
		}
	}
	function read_prov_working_hrs_weekly($working_day_dt, $arr_prov_id = array() , $dir_path = ""){
		if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common";
		$arr_final_xml = false;

		list($yr, $mn, $dt) = explode("-", $working_day_dt);
		$ts = mktime(0, 0, 0, $mn, $dt, $yr);
		$load_week = date("W", $ts);
		$load_day = date("w", $ts);
		
		$load_dt_ts = mktime(0, 0, 0, $mn, ($dt - $load_day) + 1, $yr);
		$load_dt_st = date("m-d-Y", $load_dt_ts);
		$load_dt_ed = date("m-d-Y", $load_dt_ts + (6 * 86400));
		
		$i = 0;
		$int_st_hr = mktime(10, 0, 0);
		$int_ed_hr = mktime(10, 0, 0);
		
		for($this_ts = $load_dt_ts; $this_ts <= ($load_dt_ts + (6 * 86400)); $this_ts += 86400){
			
			$this_working_dt = date("Y-m-d", $this_ts);			
			$file_name = $dir_path."/load_xml/".$this_working_dt."-".$arr_prov_id[0].".sch";
			$arr_populate =  array();
			if(!file_exists($file_name)){	//no provider schedule found
				//getting provider dtails
				$arr_pr_detail = array();
				$name = $color = $user_type = $max_appoint = "";
				$qry = "SELECT id, fname, lname, mname, user_type, max_appoint, provider_color FROM users WHERE delete_status = 0 AND Enable_Scheduler = 1 AND id = '".$arr_prov_id[0]."'";
				$pr_detail = imw_query($qry);
				if(imw_num_rows($pr_detail) > 0){
					while($dataTmp=imw_fetch_assoc($pr_detail))
					{$arr_pr_detail[] = $dataTmp;}
					
					$name = core_name_format($arr_pr_detail[0]["lname"], $arr_pr_detail[0]["fname"], $arr_pr_detail[0]["mname"]);
					$color = $arr_pr_detail[0]["provider_color"];
					$user_type = ($arr_pr_detail[0]["user_type"] == 5) ? $arr_pr_detail[0]["user_type"] : 1;
					$max_appoint = $arr_pr_detail[0]["max_appoint"];
				}

				//echo "no schedule";
				$arr_xml[$i]["dt"] = $this_working_dt;
				$arr_xml[$i][$arr_prov_id[0]]["id"] = $arr_prov_id[0];
				$arr_xml[$i][$arr_prov_id[0]]["dt"] = $this_working_dt;
				$arr_xml[$i][$arr_prov_id[0]]["name"] = $name;
				$arr_xml[$i][$arr_prov_id[0]]["color"] = $color;
				$arr_xml[$i][$arr_prov_id[0]]["type"] = $user_type;
				$arr_xml[$i][$arr_prov_id[0]]["max_appoint"] = $max_appoint;
				$arr_xml[$i][$arr_prov_id[0]]["fac_id"] = "";
				$arr_xml[$i][$arr_prov_id[0]]["fac_name"] = "";
				$arr_xml[$i][$arr_prov_id[0]]["notes"] = 0;
				$arr_xml[$i][$arr_prov_id[0]]["fac_ids"] = "";

				$load_st_st = mktime(7, 0, 0);
				
				//echo $load_st_st + (12 * 3600);
				//echo date("H:i", $load_st_st + (12 * 3600));
				
				for($this_st_ts = $load_st_st; $this_st_ts <= ($load_st_st + (10 * 3600)); $this_st_ts += 3600){
					$int_st_ref = date("H", $this_st_ts);
					$int_st_ref = (int)$int_st_ref;
					for($m = 0; $m < 60; $m += DEFAULT_TIME_SLOT){
							
						$st_hr = ($int_st_ref < 10) ? "0".$int_st_ref : $int_st_ref;
						$st_mn = ($m < 10) ? "0".$m : $m;
						
						$ed_hr = ($int_st_ref < 10) ? "0".$int_st_ref : $int_st_ref;
						$ed_mn = $m + DEFAULT_TIME_SLOT;
						$ed_mn = ($ed_mn < 10) ? "0".$ed_mn : $ed_mn;

						if((int)$ed_mn == 60){
							(int)$ed_hr += 1;
							$ed_mn = 0;
						}
						$ed_hr = ((int)$ed_hr < 10) ? "0".(int)$ed_hr : (int)$ed_hr;
						$ed_mn = ((int)$ed_mn < 10) ? "0".(int)$ed_mn : (int)$ed_mn;

						$this_time = $st_hr.":".$st_mn."-".$ed_hr.":".$ed_mn;
						
						$arr_populate[$this_time] = array(
							"id" => $this_time,
							"timing" => $this_time,
							"status" => "off",
							"color" => DEFAULT_OFFICE_CLOSED_COLOR,
							"label" => "",
							"label_type" => "",
							"l_text" => "",
							"tmpId" => 0,
							"tmp_start_time" => "",
							"tmp_end_time" => "",
							"fac_id" => 0,
							"fac_name" => "",
							"entry" => "fresh"
						);
					}	
				}
				$arr_xml[$i][$arr_prov_id[0]]["slots"] = $arr_populate;	
				
				if(mktime(7, 0, 0) < $int_st_hr){
					$int_st_hr = mktime(7, 0, 0);
				}
				if(mktime(18, 0, 0) > $int_ed_hr){
					$int_ed_hr = mktime(19, 0, 0);
				}
			}else{
				$str_fragment = file_get_contents($file_name);
				$arr_fragment = unserialize($str_fragment);				
				if(isset($arr_fragment[$arr_prov_id[0]]["slots"])){
					foreach($arr_fragment[$arr_prov_id[0]]["slots"] as $time => $details){
						list($cmphr, $cmpmn, $cmpsc) = explode(":", $time);
						$cmp_tm = mktime($cmphr, $cmpmn, $cmpsc);
						if($cmp_tm < $int_st_hr && $cmp_tm > mktime(0, 0, 0)){
							$int_st_hr = $cmp_tm;
						}
						if($cmp_tm > $int_ed_hr && $cmp_tm < mktime(22, 0, 0)){
							$int_ed_hr = $cmp_tm + 3600;
						}
					}
					$arr_xml[$i] = $arr_fragment;
				}
			}
			$i++;
		}

		$arr_final_xml = $arr_xml;

		for($i = 0; $i < count($arr_xml); $i++){
			
			$int_this_st_hr = mktime(10, 0, 0);
			$int_this_ed_hr = mktime(10, 0, 0);
			
			$arr_start_padd = array();
			$arr_end_padd = array();
			
			if(isset($arr_xml[$i][$arr_prov_id[0]]["slots"])){
				foreach($arr_xml[$i][$arr_prov_id[0]]["slots"] as $time => $details){
					list($cmpsthr, $cmpstmn, $cmpstsc) = explode(":", $time);
					$cmpsttm = mktime($cmpsthr, $cmpstmn, $cmpstsc);
					if($cmpsttm > $int_this_st_hr && $cmpsttm > mktime(0, 0, 0)){
						$int_this_st_hr = $cmpsttm;
					}
					if($cmpsttm > $int_this_ed_hr && $cmpsttm < mktime(22, 0, 0)){						
						$int_this_ed_hr = $cmpsttm + (60 * DEFAULT_TIME_SLOT);
					}
				}

				//padding at start
				if($int_this_st_hr >= $int_st_hr){
					for($s = $int_st_hr; $s < $int_this_st_hr; $s += (60 * DEFAULT_TIME_SLOT)){
						list($thr, $tmn, $tsc) = explode(":", date("H:i:s", $s));
						$thr = (int)$thr;
						$tmn = (int)$tmn;

						$st_hr = ($thr < 10) ? "0".$thr : $thr;//echo " - ";
						$st_mn = ($tmn < 10) ? "0".$tmn : $tmn;//echo " ";
							
						$ed_hr = $st_hr; //echo " - ";
						$ed_mn = $tmn + DEFAULT_TIME_SLOT;
						$ed_mn = ($ed_mn < 10) ? "0".$ed_mn : $ed_mn; //echo " ";

						if((int)$ed_mn == 60){
							(int)$ed_hr += 1;
							$ed_mn = 0;
						}
						$ed_hr = ((int)$ed_hr < 10) ? "0".(int)$ed_hr : (int)$ed_hr;
						$ed_mn = ((int)$ed_mn < 10) ? "0".(int)$ed_mn : (int)$ed_mn;

						$this_time = $st_hr.":".$st_mn."-".$ed_hr.":".$ed_mn;
							
						$arr_start_padd[$this_time] = array(
							"id" => $this_time,
							"timing" => $this_time,
							"status" => "off",
							"color" => DEFAULT_OFFICE_CLOSED_COLOR,
							"label" => "",
							"label_type" => "",
							"l_text" => "",
							"tmpId" => 0,
							"tmp_start_time" => "",
							"tmp_end_time" => "",
							"fac_id" => 0,
							"fac_name" => "",
							"entry" => "fresh"
						);				
					}
					$arr_slot_xml = array_merge($arr_start_padd, $arr_final_xml[$i][$arr_prov_id[0]]["slots"]);
					$arr_final_xml[$i][$arr_prov_id[0]]["slots"] = $arr_slot_xml;
				}

				//padding at end
				if($int_this_ed_hr <= $int_ed_hr){

					for($s = $int_this_ed_hr; $s < $int_ed_hr; $s += (60 * DEFAULT_TIME_SLOT)){
						list($thr, $tmn, $tsc) = explode(":", date("H:i:s", $s));
						$thr = (int)$thr;
						$tmn = (int)$tmn;
						
						$st_hr = ($thr < 10) ? "0".$thr : $thr;
						$st_mn = ($tmn < 10) ? "0".$tmn : $tmn;
						
						$ed_hr = $st_hr;
						$ed_mn = $tmn + DEFAULT_TIME_SLOT;
						$ed_mn = ($ed_mn < 10) ? "0".$ed_mn : $ed_mn;

						if((int)$ed_mn == 60){
							(int)$ed_hr++;
							$ed_mn = 0;
						}
						$ed_hr = ((int)$ed_hr < 10) ? "0".(int)$ed_hr : (int)$ed_hr;
						$ed_mn = ((int)$ed_mn < 10) ? "0".(int)$ed_mn : (int)$ed_mn;

						$this_time = $st_hr.":".$st_mn."-".$ed_hr.":".$ed_mn; //echo "<br>";

						$arr_end_padd[$this_time] = array(
							"id" => $this_time,
							"timing" => $this_time,
							"status" => "off",
							"color" => DEFAULT_OFFICE_CLOSED_COLOR,
							"label" => "",
							"label_type" => "",
							"l_text" => "",
							"tmpId" => 0,
							"tmp_start_time" => "",
							"tmp_end_time" => "",
							"fac_id" => 0,
							"fac_name" => "",
							"entry" => "fresh"
						);
					}


					$arr_slot_xml = array_merge($arr_final_xml[$i][$arr_prov_id[0]]["slots"], $arr_end_padd);
					$arr_final_xml[$i][$arr_prov_id[0]]["slots"] = $arr_slot_xml;
				}
			}
		}
		//echo "<textarea>";
		//print_r($arr_final_xml);
		//echo "</textarea>";
		return $arr_final_xml;
	}
	
	/*
	Function: write_html_content_weekly
	Purpose: writes html from the array for appt template, sch_type may be "main" or "physician"
	Author: Ravi, Prabh
	Returns: ARRAY containing Headings, Appt Time Slots, And Time Pane
	*/
	function write_html_content_weekly($arr_combined_xml, $arr_sel_fac = array(), $arr_sel_prov = array(), $admin_priv = 0, $time_array){
		$str_header = "";
		$str_time_pane = "";
		$str_appt_slots = "";
		$str_proc_summary = "";
		$arr_processed = array();
		$arr_not_processed = array();
		$intCnt = 0;	
		if(is_array($arr_combined_xml) && count($arr_combined_xml) > 0 && is_array($arr_sel_fac) && count($arr_sel_fac) > 0 && is_array($arr_sel_prov) && count($arr_sel_prov) > 0){
			
			//getting allowed max count
			$int_max_cnt = 0;
			$int_warn_percentage = 100;
			$qry_u = "SELECT max_per, max_appoint FROM users WHERE id = '".$arr_sel_prov[0]."'";
			$res_u = imw_query($qry_u);
			if(imw_num_rows($res_u) > 0){
				$arr_u = imw_fetch_assoc($res_u);
				$int_max_cnt = (!empty($arr_u["max_appoint"])) ? $arr_u["max_appoint"] : 0;
				$int_warn_percentage = (!empty($arr_u["max_per"])) ? $arr_u["max_per"] : 100;
			}
			//getting all procedure for day summary div
			$arr_all_proc = array();
			$all_proc_lbl = array();
			$sql_proc = "SELECT id, acronym, proc, proc_color FROM slot_procedures WHERE doctor_id = 0 AND proc != '' and active_status!='del'";
			$res_proc = imw_query($sql_proc);
			if(imw_num_rows($res_proc) > 0){
				while($this_proc=imw_fetch_assoc($res_proc)){
					$arr_all_proc[$this_proc["id"]]["acronym"] = trim($this_proc["acronym"]);
					$arr_all_proc[$this_proc["id"]]["name"] = $this_proc["proc"];
					$arr_all_proc[$this_proc["id"]]["color"] = $this_proc["proc_color"];
					$all_proc_lbl[trim($this_proc["acronym"])] = $this_proc["proc_color"];
				}

			}
				
			//procedure site conversion array
			$arr_proc_site = $this->eye_site('Info');

			//getting facility details
			$arr_all_fac = array();
			$sql_fac = "SELECT id, facility_color FROM facility";
			$res_fac = imw_query($sql_fac);
			if(imw_num_rows($res_fac) > 0){
					while($this_fac=imw_fetch_assoc($res_fac)){
						$arr_all_fac[$this_fac["id"]]["facility_color"] = $this_fac["facility_color"];
					}

			}
			//height and rowspan for time pane slot
			$tp_sl_height = (12 * (DEFAULT_TIME_SLOT / 5)) + 15;
			$tp_row_span = 60 / DEFAULT_TIME_SLOT;
			$up_height = ((DEFAULT_TIME_SLOT / 5) * 11) + 15;
			$div_slot_height = (core_date_format / 5) * 11;//this need to change in sc_script.js too
			if(DEFAULT_TIME_SLOT == 15){
				$tp_sl_height = round($tp_sl_height * 85 / 100);
				$up_height = round($up_height * 85 / 100);
				$div_slot_height = round($div_slot_height * 85 / 100);
			}
			//setting widths
			$total_prov = 2;
			$column_width = 239;
			$scroll_width = 239;
			$div_width = 210;
			$name_header_width = $column_width;
			$extra_padding = "";
			$phy_margin_left = "";
			$pos_abs = "position:absolute;";				
			$prov_name_ml = 3;				
			$intSCnt = 1;

			$arr_slot_content = array();
			foreach($arr_combined_xml as $arr_xml){
				$eff_date_add_sch = core_date_format($arr_xml["dt"], "m-d-Y");
				//calculating "on" slots
				$int_total_slots = 0;				
				foreach($arr_xml as $pr_id => $pr_detail){
					foreach($pr_detail["slots"] as $sl_id => $sl_detail){
						//echo $sl_detail["status"];
						if($sl_detail["status"] == "on"){
							//echo "a";
							$int_total_slots++;
						}
					}
				}
				
				//echo $int_total_slots;

				foreach($arr_xml as $pr_id => $pr_detail){					
					if($pr_id != "dt"){						
						
						//determining whether to process or not
						$bl_process = false;
						if(count($arr_sel_fac) > 0){						
							$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
							for($multi = 0; $multi < count($arr_multi_fac); $multi++){
								if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){									
									if(count($arr_sel_prov) > 0){										
										if(in_array($pr_detail["id"], $arr_sel_prov)){											
											$bl_process = true;
											break;
										}
									}else{
										$bl_process = true;
										break;
									}
								}
							}
						}else{
							if(count($arr_sel_prov) > 0){
								if(in_array($pr_id, $arr_sel_prov)){
									$bl_process = true;
								}
							}else{
								$bl_process = true;
							}
						}

						//getting facilites to process
						$str_process_fac = $pr_detail["fac_ids"];
						$arr_process_fac = explode(",", $pr_detail["fac_ids"]);

						if(count($arr_sel_fac) > 0){	
							$temp_arr_process_fac = $arr_process_fac;
							for($multip = 0; $multip < count($temp_arr_process_fac); $multip++){
								if(!in_array($temp_arr_process_fac[$multip], $arr_sel_fac)){
									unset($arr_process_fac[$multip]);
								}
							}
							$str_process_fac = implode(",", $arr_process_fac);
						}
						//echo $pr_detail["dt"]." ".$pr_detail["fac_ids"]."<br>";						
							
						if($bl_process == true || $pr_detail["fac_ids"] == ""){
							//getting custom labels
							$arr_custom_labels = array();
							if($str_process_fac && $pr_id && $pr_detail["dt"]){
								$qry_cl = "select facility,l_type, label_group, l_text, l_show_text, l_color, start_date, start_time from scheduler_custom_labels where facility IN (".$str_process_fac.") and provider = '".$pr_id."' and start_date = '".$pr_detail["dt"]."' order by start_time";					
								$res_cl = imw_query($qry_cl);
								if(imw_num_rows($res_cl) > 0){		
									while($this_cl=imw_fetch_assoc($res_cl)){
										$arr_custom_labels[$this_cl["facility"]][$this_cl["start_time"]] = $this_cl;
									}
								}
							}
							
							##################################
							#WRITING HEADINGS AND DAY PROCEDURE SUMMARY
							##################################														
							//getting appt count and procedures
							$appt_cnt = 0;
							unset($arr_sch);
							list($sh_y, $sh_m, $sh_d) = explode("-", $pr_detail["dt"]);
							$sh_date = date("D", mktime(0, 0, 0, $sh_m, $sh_d, $sh_y));
							$sh_week_date_view = get_date_format($sh_m.'-'.$sh_d.'-'.$sh_y,'mm-dd-yyyy');
							$sh_date .= ', '.$sh_week_date_view;
							$send_date_load=date("m-d-Y", mktime(0, 0, 0, $sh_m, $sh_d, $sh_y));

							$qry_sch = "select sa.id, sa.sa_facility_id, sa.sa_app_starttime,sa.sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, sa.EMR, sp.proc_color, sp.acronym, sp.proc_type, st.status_name, st.alias, st.status_color as alias_color, st.status_icon, sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId, sa.ref_phy_changed
							FROM schedule_appointments sa USE INDEX(sa_multiplecol)
							LEFT JOIN slot_procedures sp on sp.id = sa.procedureid 
							LEFT JOIN slot_procedures sp2 on sp2.id = sp.proc_time 
							LEFT JOIN schedule_status st on st.id = sa.sa_patient_app_status_id 
							WHERE sa_facility_id IN (".$str_process_fac.") 
							AND sa_doctor_id = '".$pr_id."' 
							AND sa_test_id = 0 
							AND sa_patient_app_status_id NOT IN (271,203,201,18,19,20) 
							AND '".$pr_detail["dt"]."' between sa_app_start_date AND sa_app_end_date 
							ORDER BY sa.sa_app_starttime, sa.sa_app_time desc";
							$res_sch = imw_query($qry_sch);
							if(imw_num_rows($res_sch) > 0){
								$appt_cnt = imw_num_rows($res_sch);
								while($tempData=imw_fetch_assoc($res_sch))
								{
									$arr_sch[] = $tempData;
								}
							}
							
							$str_ob_head_class = "heading_container";
							if($appt_cnt > 0 && $int_warn_percentage > 0){
								$wd = $pr_detail['dt'];
								$wd_arr = explode('-',$wd);
								$wno = ceil($wd_arr[2] / 7);
								$dno = date("w", mktime(0, 0, 0, $wd_arr[1], $wd_arr[2], $wd_arr[0]));
								if($dno == 0) $dno = 7;
								// for getting the no. of appointments by facilities
								$qry_by_fac = "select sa_facility_id,count(id) as app_count FROM schedule_appointments where sa_facility_id IN (".$str_process_fac.") and sa_doctor_id = '".$pr_id."' and sa_test_id = 0 and sa_patient_app_status_id NOT IN (271,203,201,18,19,20) and (sa_app_start_date = '".$pr_detail["dt"]."' or sa_app_end_date='".$pr_detail["dt"]."') group by sa_facility_id";						
								$res_by_fac = imw_query($qry_by_fac);
								$result_appt_fac_arr = array();
								if(imw_num_rows($res_by_fac)>0)
								{
									while($result_by_fac = imw_fetch_assoc($res_by_fac))
									{
										$result_appt_fac_arr[$result_by_fac['sa_facility_id']] = $result_by_fac['app_count'];
									}								
									$tmp_max_appointments_arr=$pr_detail["fac_max_appts"];
									foreach($tmp_max_appointments_arr as $fac_id_key => $pr_fac_max_appts)
									{
										if($pr_fac_max_appts['max_appts']>0)
										{
											
											/*if($pr_fac_max_appts['max_war']>0 && round(($result_appt_fac_arr[$fac_id_key]/$pr_fac_max_appts['max_appts'])*100) >= $pr_fac_max_appts['max_war'])
											{
												$str_ob_head_class= "ob_warn_threshold_head";	
												break;
											}else */ if(round(($result_appt_fac_arr[$fac_id_key]/$pr_fac_max_appts['max_appts'])*100) >= $int_warn_percentage)
											{
												$str_ob_head_class = "heading_container_bgRed";	
												break;
											}
										}
									}
																
								}
							}
														
							$str_header .= "<div style=\"width:".($name_header_width)."px;\" class=\"fl ".$str_ob_head_class." sdf\">";							
							
							$str_ob_class = "ob_percentage";
							//$int_total_slots = 45;// / 60 / DEFAULT_TIME_SLOT;
							$cal_warn_per = 0;
							if($int_max_cnt > 0 && $int_total_slots>0){
								$int_set_cnt = $appt_cnt;
								//$str_response .= round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100)." >= ".$int_warn_percentage;
								$cal_warn_per = round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100);
								if($cal_warn_per >= $int_warn_percentage){
									$str_ob_class = "ob_warn_percentage";
								}else{
									$str_ob_class = "ob_percentage";
								}
							}
							// code to find the no. of appointments before and after the lunch time (specify lunch time as 12:00 if not defined)
							$before_lunchTm = "12:00:00";
							$available_temp_ids = $arr_xml[$pr_id]['template_ids'];
							$sch_temp_qry = 'SELECT fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE id IN('.$available_temp_ids.')';
							$sch_temp_qry_obj = imw_query($sch_temp_qry);
							
							while($sch_temp_qry_data = imw_fetch_assoc($sch_temp_qry_obj))
							{
								$sch_temp_qry_data['fldLunchStTm'] = trim($sch_temp_qry_data['fldLunchStTm']);
								if(isset($sch_temp_qry_data['fldLunchStTm']) && $sch_temp_qry_data['fldLunchStTm'] != "00:00:00")
								{
									$before_lunchTm = $sch_temp_qry_data['fldLunchStTm'];
									break;	
								}								
							}
													
							$beforeLunchApptsQry = 'select count(id) as appts_count FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ('.$str_process_fac.') and sa_doctor_id = "'.$pr_id.'" and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_starttime < "'.$before_lunchTm.'" and sa_app_starttime !="00:00:00" and "'.$pr_detail["dt"].'" between sa_app_start_date and sa_app_end_date ';
							
							$afterLunchApptsQry = 'select count(id) as appts_count FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ('.$str_process_fac.') and sa_doctor_id = "'.$pr_id.'" and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_starttime >= "'.$before_lunchTm.'" and "'.$pr_detail["dt"].'" between sa_app_start_date and sa_app_end_date ';
							
														
							$beforeLunchApptsQryObj = imw_query($beforeLunchApptsQry);
							$afterLunchApptsQryObj = imw_query($afterLunchApptsQry);
							
							$no_of_appts_beforeLunch = imw_fetch_assoc($beforeLunchApptsQryObj);
							$no_of_appts_afterLunch = imw_fetch_assoc($afterLunchApptsQryObj);
							
							if(trim($no_of_appts_beforeLunch['appts_count']) == ''){ $no_of_appts_beforeLunch['appts_count'] = 0; }
							if(trim($no_of_appts_afterLunch['appts_count']) == ''){ $no_of_appts_afterLunch['appts_count'] = 0; }
							
							$prov_name_cnt_mo = " (".$appt_cnt.")";
							$prov_name_cnt = " (<span id=\"prov_appt_cnt_".$pr_id."\">".$no_of_appts_beforeLunch['appts_count']."/".$no_of_appts_afterLunch['appts_count']."</span>)";						
						
							$disp_prov_name = $sh_date;
							$disp_prov_name .= $prov_name_cnt;
							// appointment before and after lunch divison done
							
							$str_header .= "<div class=\"fl pt5 pl5 schMDtState \" style=\"margin-left:".$prov_name_ml."px;\" title=\"".$sh_date.$prov_name_cnt_mo."\" onClick=\"javascript:toggle_sch_type('day','".$send_date_load."');\"><strong>".$disp_prov_name."</strong></div>";

							$str_header .= "<div class=\"fl ml5\"><input type=\"button\" value=\"&nbsp;\"";
							if($appt_cnt == 0){
								$str_header .= " disabled=\"disabled\" ";
							}
							$str_header .= " onClick=\"javascript:day_print_options('all', '".$pr_detail["dt"]."','1');\" class=\"sc_print_button\" style=\"margin-top:3px;\" title=\"Print Day Appointments\"></div>";
							$str_header .= "<div class=\"fl\"><input type=\"button\" value=\"&nbsp;\"";
							if($appt_cnt == 0){
								$str_header .= " disabled=\"disabled\" ";
							}
							$str_header .= " onClick=\"javascript:day_proc_summary('".$pr_id."', '".$pr_detail["dt"]."');\" class=\"sc_summary_button\" style=\"margin-top:3px;\" title=\"Day Summary\"></div>";
							$str_header .= "<div id=\"sticky_".$pr_id."_".$pr_detail["dt"]."\" class=\"fl notes_active_sticky\" onclick=\"javascript:hide_provider_notes();show_provider_notes('".$pr_id."', '".$pr_detail["dt"]."');\">".$pr_detail["notes"]."</div>";
							$str_header .= "<div class=\"ml5 fl ".$str_ob_class."\">".$cal_warn_per."%</div>";
							
							$str_header .=  "</div>";

							##################################
							#WRITING APPOINTMENT SLOTS with appointments
							##################################
							$arrStartTiming = array();
							if($appt_cnt > 0){
								for($a = 0; $a < count($arr_sch); $a++){
									$arrStartTiming[$arr_sch[$a]["sa_app_starttime"]][] = $arr_sch[$a];
								}
							}
							
							if(DEFAULT_TIME_SLOT == 10){
								$arrStartTiming = $this->plugin_to_show_5min_appt($arrStartTiming);						
							}

							//echo '<pre>';
							//print_r($arrStartTiming);
							//exit;
							
							$div_id = 'dive_'.str_replace("-", "_", $pr_detail["dt"]).'_'.$pr_detail["fac_id"].'_'.$pr_id;
							$imgid = "im".$div_id;
							$tabid = "tab".$div_id;
							$str_appt_slots .= "<div class=\"fl\" style=\"width:".$column_width."px;\"><div id=\"".$tabid."\" class=\"appt_cont\" style=\"height:".$tp_sl_height."px;\">";
							$arrPrevThisAppt = array();
							
							foreach($pr_detail["slots"] as $sl_id => $sl_detail){
								if($intCnt == 0){
									##################################
									#WRITING TIME PANE STARTS
									##################################
									$tp_st_hr = number_format(substr($sl_id, 0, 2));
									$tp_st_mn = number_format(substr($sl_id, 3, 2));
									
									$str_time_pane .= "
									<div class=\"";
									$str_time_pane .=($tp_st_mn == 0)?"time_pane title_pane":"time_pane";
									$ln_height=($tp_st_mn == 0)?$tp_sl_height+10:$tp_sl_height;
									$str_time_pane .="\" style=\"height:".$tp_sl_height."px;line-height:".$ln_height."px;\">";
									
									if($tp_st_mn == 0){									
										$str_time_pane .= "<div class=\"hr_pane\">";
										$str_time_pane .= $time_array[$tp_st_hr];
										$str_time_pane .= "</div> ";									
									}else{
										$str_time_pane .= "<div class=\"hr_pane\"><div class=\"mn_pane\">";
										$str_time_pane .= $tp_st_mn;
										$str_time_pane .= "</div></div> ";	
									}
									$str_time_pane .= "</div> ";
									##################################
									#WRITING TIME PANE ENDS
									##################################
								}

								$intSCnt++;
								$intStartHr = substr($sl_id, 0, 2);
								$intStartMin = substr($sl_id, 3, 2);
								$times_from = $intStartHr.":".$intStartMin.":00";
								
								$intEndHr = substr($sl_id, 6, 2);
								$intEndMin = substr($sl_id, 9, 2);
								$times_to = $intEndHr.":".$intEndMin.":00";
								
								//adjusting previouse slot appointment in this slot
								if(count($arrPrevThisAppt) > 0){							
									$intExistingApptsForThisSlot = count($arrStartTiming[$times_from]);							
									$arrTempTimings = $arrStartTiming;
									$intNewApptsForThisSlot = 0;
									foreach ($arrPrevThisAppt as $arrThisPrevThisAppt){
										$arrStartTiming[$times_from][$intNewApptsForThisSlot] = $arrThisPrevThisAppt;
										$intNewApptsForThisSlot++;							
									}
									$k5 = 0;
									for($k6 = $intNewApptsForThisSlot; $k6 < ($intExistingApptsForThisSlot+$intNewApptsForThisSlot); $k6++){
										$arrStartTiming[$times_from][$k6] = $arrTempTimings[$times_from][$k5];
										$k5++;
									}
									//resetting array
									$arrPrevThisAppt = array();
								}								

								$tddblclick_blk = $tdMouseUp = $strMsg = $tddblclick_blk = $tddblclick_one = $tddblclick = $tdOnClick ="";
									
								$slot_color = $sl_detail['color'];
								$label_type = $sl_detail['label_type'];
								$label_group = $sl_detail['label_group'];
								$l_text 	= $sl_detail['l_text'];
								if(isset($arr_custom_labels[$sl_detail['fac_id']][$times_from]) && ($sl_detail["status"] != "block" && $sl_detail["status"] != "lock")){
									//print_r($arr_custom_labels[$times_from]);
									$arr_clbl_temp = explode("; ", $arr_custom_labels[$sl_detail['fac_id']][$times_from]["l_show_text"]);
									asort($arr_clbl_temp);
									$slot_color = $arr_custom_labels[$sl_detail['fac_id']][$times_from]["l_color"];
									$sl_detail["label"] = implode("; ", $arr_clbl_temp);
									$sl_detail["l_text"] = $arr_custom_labels[$sl_detail['fac_id']][$times_from]["l_text"];
									$sl_detail["label_type"] = $label_type = $arr_custom_labels[$sl_detail['fac_id']][$times_from]["l_type"];
									$sl_detail["label_group"] = $label_group = $arr_custom_labels[$sl_detail['fac_id']][$times_from]["label_group"];
									$sl_detail["label_replaced"] = $arr_custom_labels[$sl_detail['fac_id']][$times_from]["labels_replaced"];
								}
								
								$tdMouseUp = "onClick=\"TestOnMenu();\" ";
								$tddblclick_blk = " onClick=\"TestOnMenu();\" ";								
								
								if(in_array($sl_detail["fac_id"], $arr_process_fac)){	
									
									$mouse_over_slot_detail = $sl_detail["fac_name"].": ".$sl_detail["tmp_start_time"]." - ".$sl_detail["tmp_end_time"];
									
									$tdOnClick = "onClick=\"javascript:add_appt_weekly('$pr_id','$_SESSION[patient]','$sl_detail[fac_id]', '$times_from','$eff_date_add_sch','$sl_detail[tmpId]');\"";
									
									//provider not in office
									if($sl_detail["status"] == "off"){
										
										$tddblclick_one = "";
										if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
										}else{
											$strMsg = "Physician not in office";
										}
										$str_appt_slots .= "
											<div style=\"width:".$scroll_width."px;height:".$tp_sl_height."px".$extra_padding."\">";
										if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false){
											$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$tdMouseUp."></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$sl_detail['color'].";\" ".$tddblclick_one." ".str_replace("[{MODE}]", "off", $tddblclick_blk)." alt=\"Office is closed.\" title=\"Office is closed.\">";
										}else{
											$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\"></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$sl_detail['color'].";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\" ".str_replace("[{MODE}]","off",$tddblclick_blk)." alt=\"Office is closed.\" title=\"Office is closed.\">";
										}
										
									}else if($sl_detail["status"] == "block" || $sl_detail["label_type"] == "Reserved"){
										$tddblclick_one = "";
										if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
										}else{
											$strMsg = "Blocked Time";
										}
										$str_appt_slots .= "<div style=\"width:".$scroll_width."px;height:".$tp_sl_height."px".$extra_padding."\">";
										if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
											$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$tdMouseUp."></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$sl_detail['color'].";\" ".$tddblclick_one." ".str_replace("[{MODE}]","block",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
										}else{
											$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\"></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$sl_detail['color'].";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\" ".str_replace("[{MODE}]","block",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
										}
									
									}else if($sl_detail["status"] == "lock"){
										$tddblclick_one = "";
										$strMsg = "This time slot is locked.";									
										$str_appt_slots .= "<div style=\"width:".$scroll_width."px;height:".$tp_sl_height."px".$extra_padding."\">";
										if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
											$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$tdMouseUp."></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$sl_detail['color'].";\" ".$tddblclick_one." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
										}else{
											$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\"></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$sl_detail['color'].";\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\" ".str_replace("[{MODE}]","block",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
										}
									
									}else if($sl_detail["status"] == "open"){
										$tddblclick_one = "";
										if($sch_type != "physician"){
											//$tddblclick_one = "onDblClick=new_appoint_loc('".$sl_detail["fac_id"]."','".$pr_id."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','".$sl_detail['tmpId']."');";
										}
											
										$str_appt_slots .= "<div style=\"width:".$scroll_width."px;height:".$tp_sl_height."px".$extra_padding."\">";
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$tdMouseUp."></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$slot_color.";\" ".$tddblclick_one." ".str_replace("[{MODE}]","open",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
										
									}else if($sl_detail["status"] == "on"){	//provider in office
									
										/*$tddblclick_one = "";
										$tddblclick = "";
										if(array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
											$tddblclick_one = "onDblClick=new_appoint_loc('".$sl_detail["fac_id"]."','".$pr_id."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','".$sl_detail['tmpId']."');";
										}
										if($sch_type != "physician"){
											$tddblclick = "new_appoint_loc('".$sl_detail["fac_id"]."','".$pr_id."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','".$sl_detail['tmpId']."');";
										}*/
										
										$str_appt_slots .= "<div style=\"width:".$scroll_width."px;height:".$tp_sl_height."px".$extra_padding."\">";
										$str_appt_slots .= "<div class=\"fl\" style=\"height:".$tp_sl_height."px;background-color:#FFFFFF\">";
												
										if(($pr_detail["max_appoint"] > 1 || $pr_detail["max_appoint"] == "") && array_key_exists($times_from, $arrStartTiming) && $sch_type != "physician"){
											$str_appt_slots .=  "<div class=\"fl\" onDblClick=\"".$tddblclick."\" class=\"add_new_appointment\" style=\"cursor:hand; height:".$div_slot_height."px;\"></div>";
										}
										
										$str_appt_slots .= "<div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$tdOnClick."></div></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".$pr_detail["color"].";\" ".$tddblclick_one." ".str_replace("[{MODE}]","on",$tddblclick_blk)." alt=\"".$mouse_over_slot_detail."\" title=\"".$mouse_over_slot_detail."\">";
										
									}
									//showing appointments in slot
									if(array_key_exists($times_from, $arrStartTiming)){
										//provider has appointments
										$str_appt_slots .= "<div style=\"width:".($column_width-22)."px;margin-top:0px; margin-left:0px; ".$pos_abs."\" id=\"".$div_id.$times_from."\">[{(".$pr_id."::".$times_from.")}]";
										$intTotApptInSlot = count($arrStartTiming[$times_from]);
										$intShowAppt = 0;
										$intPrevApptCnt = 0;

										$arrPrevThisAppt = array();	

										// This is the code for appointments setting of per slot.
										// $arrStartTiming[$times_from] represents the slot and its available appointments.
										foreach($arrStartTiming[$times_from] as $arrThisAppt){
												
											$appt_duration = (strtotime($arrThisAppt["sa_app_endtime"]) - strtotime($arrThisAppt["sa_app_starttime"])) / 60;
											list($appt_st_hr, $appt_st_mn, $appt_st_sc) = explode(":", $arrThisAppt["sa_app_starttime"]);
											if($appt_duration <= 0){
												$appt_duration = 10;
											}
											//$commWidth = ($div_width + 4 - $subt_width) / ($intTotApptInSlot - $subt_no);
											
											$divRepeat = (isset($arrThisAppt['repeat']) && !empty($arrThisAppt['repeat'])) ? $arrThisAppt['repeat'] - 1: 0;
											$divHeightDiffFactor = $divRepeat * DEFAULT_TIME_SLOT;
											$divHeight = (($tp_sl_height / DEFAULT_TIME_SLOT) * ($appt_duration - $divHeightDiffFactor)) + 3;
											//$divZindex = 5000 - (int)$appt_st_mn - (int)$appt_st_hr;

											$divZindex = 5000 + ((int)$appt_st_mn + ((int)$appt_st_hr * 60));


											if($intTotApptInSlot == 0){
												$divWidth = ($div_width + 4);
											}else{
												$divWidth = ($div_width + 4) / ($intTotApptInSlot);
											}
											$divTopBorder = (isset($arrThisAppt['repeat'])) ? "" : "b1";											
											$divColor = ($arrThisAppt['proc_color'] != "") ? $arrThisAppt['proc_color'] : "#FFFFFF";											
											if(($divLeft + $divWidth) > ($div_width + 6)){
												$divLeft = 0;												
											}
											
											$disp_content = "";
											$mouse_over_content = "";
											$mouse_over_content .= $arrThisAppt['acronym']." ".$arr_proc_site[$arrThisAppt["procedure_site"]]." - ".stripslashes($arrThisAppt['sa_patient_name']);
											
											$pp_menu = "";

											/*if($sch_type != "physician"){
												$pp_menu = "onMouseDown = \"javascript:pop_menu('".$arrThisAppt['id']."','".$sl_detail["fac_id"]."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','".$arrThisAppt['sa_patient_id']."', 'weekly','','','','".$arrThisAppt["iolink_connection_settings_id"]."','".$arrThisAppt["iolink_practice_name"]."');\"";
												$on_click_appt = "showIolinkPdf('".$arrThisAppt['sa_patient_id']."');";
												$on_click_appt1 = "javascript:pre_load_front_desk('".$arrThisAppt['sa_patient_id']."', '".$arrThisAppt['id']."');";
												$on_dclick_appt = "javascript:drag_name_weekly('".$arrThisAppt['id']."', '".$arrThisAppt['sa_patient_id']."', '".$arrThisAppt['sa_patient_name']."', '".$arrThisAppt['procedureid']."', 'reschedule');";
											}*/
											
											if(!(isset($arrThisAppt['repeat']) && !empty($arrThisAppt['repeat']))){
												
												if($arrThisAppt["EMR"] == 1){
													$emrsymbol=" - <strong>e</strong>";
												}else{
													$emrsymbol="&nbsp;";
												}
												
												
												//$disp_content .= "<div style=\"float:left\"><a style=\"";
												
												//if(DEFAULT_PRODUCT == "imwemr"){
												//	$disp_content .= "display:none;";
												//}
												
												//$disp_content .= "\" href=\"javascript:void(0);\" onClick='edit_schedule(\"".$arrThisAppt['id']."\",\"\",\"".$arrSlot['tmpId']."\",\"".$arrProvider['id']."\",\"".$arrThisAppt['sa_facility_id']."\",\"".$arrThisAppt['timeadjusted']."\");'>"."<img src='' id='edit_appt_img' name='edit_appt_img' alt='Update'>"."</a>&nbsp;</div>";
												
												$disp_content .= "<div class=\"appt_txt\" id='".$arrThisAppt['id']."'>";
												
												if(defined('SCHEDULER_SHOW_STATUS_COLOR') && constant('SCHEDULER_SHOW_STATUS_COLOR')==true && $arrThisAppt['alias_color'])
												{
													$disp_content .= "<div style=\"height:".($divHeight-2)."px;background-color:".$arrThisAppt['alias_color']."\" class=\"sts_clr\">&nbsp;</div>";
												}else{
													if($arrThisAppt['alias']=='RS' && constant('PRACTICE_PATH')=='bennett'){}
													else $disp_content .= "<div class=\"fl act_symbol\">".$arrThisAppt['alias']."</div>";
												}
												
												if($arrThisAppt['iolinkPatientId'] != 0 && $arrThisAppt['iolinkPatientWtId'] != 0){
													$disp_content .= "<div class=\"fl io_1\" onclick=\"".$on_click_appt."\">I</div><div class=\"fl io_2\" onclick=\"".$on_click_appt."\">O</div>";
												}
																								
												/*if(($arrThisAppt['status_icon']<>"") && (file_exists("../../images/".$arrThisAppt["status_icon"]))){
													$disp_content .= "<div class=\"fl\"><img src=\"../../images/".$arrThisAppt['status_icon']."\" alt=\"".$arrThisAppt['status_name']."\"></div>";
												}*/
												
												if($arrThisAppt['ref_phy_changed']==1){
													$info='';
													$info=($arrThisAppt['ref_phy_comments'])?$arrThisAppt['ref_phy_comments']:'Referring physician information updated';
													$disp_content .= "<div class=\"fl\"><img src='../../library/images/flag_yellow_black_border.png' title='".$info."'></div>";
												}
												if(defined('SCHEDULER_SHOW_PROC_TYPE') && constant('SCHEDULER_SHOW_PROC_TYPE')==true && $arrThisAppt['proc_type'])
												{
													$disp_content .= "<div class=\"fl\"><img src='../../library/images/$arrThisAppt[proc_type].png' title='".$arrThisAppt['proc_type']." Appointment' width='28px'></div>";
												}
												
												$disp_content .= $mouse_over_content;
												//check do we have assign room to patient
												$room_str='';
												/*if($eff_date_add_sch==date("m-d-Y") && $arrThisAppt['sa_patient_id'])
												{
													$pt_rm_q=imw_query("SELECT app_room FROM patient_location WHERE patientId = '".$arrThisAppt['sa_patient_id']."' AND cur_date = '".date("Y-m-d")."' LIMIT 1");
													if(imw_num_rows($pt_rm_q)>=1)
													{
														$pt_rm_d=imw_fetch_object($pt_rm_q);
														if($pt_rm_d->app_room)$room_str="<br clear='all'/><div style='padding-left:3px; text-align:left; font-weight:bold'>($pt_rm_d->app_room)</div>";// && $pt_rm_d->app_room!='N/A'
													}
												}*/
												$disp_content .= "<i>".$emrsymbol."</i></div>$room_str";
											}else{
												$disp_content .= "<div class=\"fl\" id='".$arrThisAppt['id']."' ".$pp_menu." onclick=\"".$on_click_appt1."\" ondblclick=\"".$on_dclick_appt."\"></div>";
											}
											$mouse_over_content .= " (".core_time_format($arrThisAppt["sa_app_starttime"])." - ".core_time_format($arrThisAppt["sa_app_endtime"]).")";
											
											$str_appt_slots_div = "<div id=\"appt_".$arrThisAppt['id']."\" class=\"sdf appt_box b1\" style=\"[{(_BORDER_BOTTOM_)}]background-color:".$divColor.";height:".$divHeight."px;width:[{(WIDTH".$arrThisAppt['id']."WIDTH)}]px;z-index:".$divZindex.";left:[{(LEFT".$arrThisAppt["id"]."LEFT)}]px;".$phy_margin_left."\" title=\"".$mouse_over_content."\" ".$pp_menu." onclick=\"".$on_click_appt1."\" ondblclick=\"".$on_dclick_appt."\">";
											$str_appt_slots_div .= $disp_content;
											$str_appt_slots_div .= "</div>";
											
											//slot end time 
											$arrTempSlotEndTime = explode(":",$times_to);
											$tsSlotEndTime = mktime($arrTempSlotEndTime[0],$arrTempSlotEndTime[1],$arrTempSlotEndTime[2]);
											
											//appt end time
											$arrTempAppEndTime = explode(":",$arrThisAppt['sa_app_endtime']);
											$tsAppEndTime = mktime($arrTempAppEndTime[0],$arrTempAppEndTime[1],$arrTempAppEndTime[2]);

											if(isset($arrThisAppt['repeat']) && $arrThisAppt['repeat'] > 0){
												$str_appt_slots_div = "";
											}
											
											if(isset($arrThisAppt['repeat']) && $arrThisAppt['repeat'] != ""){
												if($tsAppEndTime > $tsSlotEndTime){									
													$arrTemp = array("repeat" => $arrThisAppt['repeat'] + 1);
													unset($arrThisAppt['repeat']);
													$arrPrevThisAppt[$intPrevApptCnt] = array_merge($arrThisAppt,$arrTemp);
													$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "", $str_appt_slots_div);
												}else{
													$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "border-bottom:1px solid #000000;", $str_appt_slots_div);
												}
											}else{
												if($tsAppEndTime > $tsSlotEndTime){
													$arrTemp = array("repeat" => 2);													
													$arrPrevThisAppt[$intPrevApptCnt] = array_merge($arrThisAppt,$arrTemp);
													$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "", $str_appt_slots_div);
												}else{
													$str_appt_slots_div = str_replace("[{(_BORDER_BOTTOM_)}]", "border-bottom:1px solid #000000;", $str_appt_slots_div);
												}
											}
											//$str_appt_slots .= $str_appt_slots_div;
											
											$arr_slot_content[$pr_id][$times_from][$intShowAppt] = array("html" => $str_appt_slots_div, 
																								"wide" => "",
																								"left" => "",
																								"prid" => $pr_id,
																								"appt" => $arrThisAppt["id"],
																								"rept" => $arrThisAppt["repeat"]
																						);
											
											$divLeft += $divWidth;
											$intPrevApptCnt++;
											$intShowAppt++;
										}

										$str_appt_slots .= "</div>";
										
									}else{
										if($sl_detail["label"] != ""){
											if($sl_detail["label_type"]=="Information")
											{
												$str_appt_slots .= "<div class=\"label_txt purple_stripe_pattern bdr\" style=\"height:".$tp_sl_height."px;background-color:".$slot_color."\">";
												$str_appt_slots .= '<span>'.ucfirst($sl_detail["label"]).'</span>';
												$str_appt_slots .= "</div>";											
											}
											else
											{
												$str_appt_slots .= "<div class=\"label_txt bdr\" style=\"height:".$tp_sl_height."px;background-color:".$slot_color."\">";
												$str_appt_slots .= '<span>'.ucfirst($sl_detail["label"]).'</span>';
												$str_appt_slots .= "</div>";
											}
										}else{
											$str_appt_slots .=  "<div></div>";
										}
									}
									if($sl_detail["status"] == "off"){
										$str_appt_slots .= "</div><div class=\"fl off\" style=\"height:".($tp_sl_height)."px\" title=\"Office is closed.\"></div></div> ";
									}else{
										$str_appt_slots .= "</div><div class=\"fl\" style=\"height:".($tp_sl_height)."px;width:10px;background-color:".(($arr_all_fac[$sl_detail["fac_id"]]["facility_color"] == "") ? "#9d9a8b" : $arr_all_fac[$sl_detail["fac_id"]]["facility_color"])."\" title=\"".$mouse_over_slot_detail."\"></div></div> ";
									}
								}else{
									$tddblclick_one = "";
									if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false && $sch_type != "physician"){
										//$tddblclick_one = "onDblClick=new_appoint_loc('".$sl_detail["fac_id"]."','".$pr_id."','".$pr_id."','".$times_from."','".$eff_date_add_sch."','".$sl_detail['tmpId']."');";
									}else{
										$strMsg = "Physician not in office";
									}
									$str_appt_slots .= "<div style=\"width:".$scroll_width."px;height:".$tp_sl_height."px\">";
									if($admin_priv == 1 && array_key_exists($times_from, $arrStartTiming) == false){
										$str_appt_slots .= "
										<div class=\"fl\" style=\"height:".$tp_sl_height."px;background-color:#f3f3f3\"><div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" ".$tdMouseUp."></div></div><div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px;background-color:".DEFAULT_OFFICE_CLOSED_COLOR.";\" ".$tddblclick_one." ".str_replace("[{MODE}]", "off", $tddblclick_blk)." title=\"Office is closed.\">";
									}else{
										$str_appt_slots .= "
										<div class=\"fl\" style=\"height=:".$tp_sl_height."px;\"><div class=\"fl inv_img\" style=\"height:".$tp_sl_height."px;\" onClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\"></div></div>
										<div class=\"fl slt_border\" style=\"height:".$tp_sl_height."px;width:".($column_width-22)."px\" onDblClick=\"TestOnMenu(); hide_tool_tip(); alert('".$strMsg."');\" ".str_replace("[{MODE}]","off",$tddblclick_blk)." bgcolor=\"".$slot_color."\" title=\"Office is closed.\">";
									}
									$str_appt_slots .= "</div><div class=\"fl off\" style=\"height:".($tp_sl_height)."px\" title=\"Office is closed.\"></div></div> ";
								}
							}
							$str_appt_slots .= "</div></div>";
							$intCnt++;	
							
						}
					}

				}

				if(is_array($arr_slot_content) && count($arr_slot_content) > 0){
					
					$arr_slot_content_tmp = $arr_slot_content;
					//print "<pre>";
					//print_r($arr_slot_content);
					//setting widths				
					$arr_appt_widths = array();
					$arr_appt_slot_widths = array();
					$int_max_cnt_val = 0;
					foreach($arr_xml as $pr_id => $pr_detail){
						if($pr_id != "dt"){
							//print "<pre>";
							//print_r($fac_detail);

							//determining whether to process or not
							$bl_process = false;
							if(count($arr_sel_fac) > 0){
								$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
								for($multi = 0; $multi < count($arr_multi_fac); $multi++){
									if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){
										if(count($arr_sel_prov) > 0){
											if(in_array($pr_detail["id"], $arr_sel_prov)){
												$bl_process = true;
												break;
											}
										}else{
											$bl_process = true;
											break;
										}
									}
								}
							}else{
								if(count($arr_sel_prov) > 0){
									if(in_array($pr_id, $arr_sel_prov)){
										$bl_process = true;
									}
								}else{
									$bl_process = true;
								}
							}

							//getting facilites to process
							$str_process_fac = $pr_detail["fac_ids"];
							$arr_process_fac = explode(",", $pr_detail["fac_ids"]);

							if(count($arr_sel_fac) > 0){	
								$temp_arr_process_fac = $arr_process_fac;
								for($multip = 0; $multip < count($temp_arr_process_fac); $multip++){
									if(!in_array($temp_arr_process_fac[$multip], $arr_sel_fac)){
										unset($arr_process_fac[$multip]);
									}
								}
								$str_process_fac = implode(",", $arr_process_fac);
							}

							if($bl_process == true){	
								$int_max_cnt_val = 0;
								foreach($pr_detail["slots"] as $sl_id => $sl_detail){									
									
									$intStartHr = substr($sl_id, 0, 2);
									$intStartMin = substr($sl_id, 3, 2);
									$times_from = $intStartHr.":".$intStartMin.":00";									
									
									//echo count($arr_slot_content_tmp[$pr_id][$times_from]);
									if(in_array($sl_detail["fac_id"], $arr_process_fac)){	
										if(isset($arr_slot_content_tmp[$pr_id][$times_from])){
											//echo " ".$int_max_cnt_val." ";
											//echo "<br>".$times_from." ".count($arr_slot_content_tmp[$pr_id][$times_from])."<br>";

											if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0 && count($arr_slot_content_tmp[$pr_id][$times_from]) > $int_max_cnt_val){
												$int_max_cnt_val = count($arr_slot_content_tmp[$pr_id][$times_from]);
											}
										}
									}									

								}
								if($int_max_cnt_val > 0){	
									//echo " ".$int_max_cnt_val." ";
									$arr_appt_widths[$pr_id] = ($div_width + 4) / $int_max_cnt_val;
									foreach($pr_detail["slots"] as $sl_id => $sl_detail){
										
										$int_max_cnt_slot_val = $int_max_cnt_val;

										$intStartHr = substr($sl_id, 0, 2);
										$intStartMin = substr($sl_id, 3, 2);
										$times_from = $intStartHr.":".$intStartMin.":00";									
										
										if(in_array($sl_detail["fac_id"], $arr_process_fac)){	
											if(isset($arr_slot_content_tmp[$pr_id][$times_from])){
												if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0 && count($arr_slot_content_tmp[$pr_id][$times_from]) == 1){
													$int_max_cnt_slot_val = $div_width + 4;
												}
											}
										}
										
										$arr_appt_slot_widths[$pr_id][$times_from] = $int_max_cnt_slot_val;
										
									}
								}
							}
						}
					}					

					//replacing widths in appts				
					$str_slot_tmp_html = "";
					
					foreach($arr_xml as $pr_id => $pr_detail){
						
						if($pr_id != "dt"){

							//determining whether to process or not
							$bl_process = false;
							if(count($arr_sel_fac) > 0){
								$arr_multi_fac = explode(",", $pr_detail["fac_ids"]);
								for($multi = 0; $multi < count($arr_multi_fac); $multi++){
									if(in_array($arr_multi_fac[$multi], $arr_sel_fac)){
										if(count($arr_sel_prov) > 0){
											if(in_array($pr_detail["id"], $arr_sel_prov)){
												$bl_process = true;
												break;
											}
										}else{
											$bl_process = true;
											break;
										}
									}
								}
							}else{
								if(count($arr_sel_prov) > 0){
									if(in_array($pr_id, $arr_sel_prov)){
										$bl_process = true;
									}
								}else{
									$bl_process = true;
								}
							}

							//getting facilites to process
							$str_process_fac = $pr_detail["fac_ids"];
							$arr_process_fac = explode(",", $pr_detail["fac_ids"]);

							if(count($arr_sel_fac) > 0){	
								$temp_arr_process_fac = $arr_process_fac;
								for($multip = 0; $multip < count($temp_arr_process_fac); $multip++){
									if(!in_array($temp_arr_process_fac[$multip], $arr_sel_fac)){
										unset($arr_process_fac[$multip]);
									}
								}
								$str_process_fac = implode(",", $arr_process_fac);
							}

							if($bl_process == true){
								
								foreach($pr_detail["slots"] as $sl_id => $sl_detail){
									$left = 0;
									$str_slot_tmp_html = "";
									
									$intStartHr = substr($sl_id, 0, 2);
									$intStartMin = substr($sl_id, 3, 2);
									$times_from = $intStartHr.":".$intStartMin.":00";

									if(in_array($sl_detail["fac_id"], $arr_process_fac)){

										if(isset($arr_slot_content[$pr_id][$times_from])){
											//echo " ".$int_max_cnt_val." ";
											//echo " ".count($arr_slot_content_tmp[$pr_id][$times_from])." ";

											if(count($arr_slot_content_tmp[$pr_id][$times_from]) > 0){
												$first_appt_loaded = 0;
												//echo "<br>".$times_from." ".count($arr_slot_content_tmp[$pr_id][$times_from])."<br>";
												foreach($arr_slot_content_tmp[$pr_id][$times_from] as $appt_det){
													$left = $left + $arr_appt_widths[$pr_id];
													if($first_appt_loaded == 0){
														$left = 0;
														$first_appt_loaded = 1;
													}
													
													if(count($arr_slot_content_tmp[$pr_id][$times_from]) == 1 && ($appt_det["rept"] < 2 || $appt_det["rept"] == "")){
														$str_slot_tmp_html_tmp = str_replace("[{(LEFT".$appt_det["appt"]."LEFT)}]", $left, $appt_det["html"]);
														$str_slot_tmp_html_tmp = str_replace("[{(WIDTH".$appt_det["appt"]."WIDTH)}]", $arr_appt_slot_widths[$pr_id][$times_from], $str_slot_tmp_html_tmp);
														$str_slot_tmp_html .= $str_slot_tmp_html_tmp;
													}else{
														$str_slot_tmp_html_tmp = str_replace("[{(LEFT".$appt_det["appt"]."LEFT)}]", $left, $appt_det["html"]);
														$str_slot_tmp_html_tmp = str_replace("[{(WIDTH".$appt_det["appt"]."WIDTH)}]", $arr_appt_widths[$pr_id], $str_slot_tmp_html_tmp);
														$str_slot_tmp_html .= $str_slot_tmp_html_tmp;
													}
												}
											}
										}
									}
									$str_appt_slots = str_replace("[{(".$pr_id."::".$times_from.")}]", $str_slot_tmp_html, $str_appt_slots);
								}								
							}
						}
					}
				}
				// Reset the array so that the previous day slots content do not override array of the next day slots content.
				$arr_slot_content=array();
			}
		}
		//echo "<textarea>".$str_header."</textarea>";
		//echo "<textarea>".$str_time_pane."</textarea>";
		//echo "<textarea>".$str_appt_slots."</textarea>";
		//echo "<textarea>".$str_proc_summary."</textarea>";
		//echo $total_prov;
		
		return array($str_header, $str_time_pane, $str_appt_slots, $str_proc_summary, $total_prov, $arr_widths, $arr_not_processed, $arr_processed);
	}
	

	/*
	Function: create_month_calendar
	Purpose: to create new scheduler calendar for month view
	Author: Ravi, Prabh
	Returns: STRING with Calendar HTML
	*/
	function create_month_calendar($calendar_load_dt, $arr_sess_prov = array(), $arr_sess_facs = array()){
		if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common";
		$cmc_print_arr = array();
		$style = "";

		$block_height = $div_height = (($_SESSION["wn_height"] - 345) / 5);
		$calendar = "";
		$prov_id = (empty($prov_id)) ? 0 : $prov_id;
		list($loadyear, $loadmonth, $loadday) = explode("-", $calendar_load_dt);

		list($selyear, $selmonth, $selday) = explode("-", $working_day_dt);
		
		//first day of month
		$first_of_month = mktime(0, 0, 0, $loadmonth, 1, $loadyear);
		$dateMonthName = date("F", $first_of_month);
		
		//getting day names
		$day_name_length = 10;
		$day_names = ""; #generate all the day names according to the current locale
		$get_sunday=strtotime("2000-01-02");
		for($n = 0, $t = $get_sunday; $n < 7; $n++, $t += 86400){ #946789200 January 2, 2000 was a Sunday
			$d = ucfirst(strftime('%A', $t)); #%A means full textual day name
			$day_names .= "<div class=\"fl mv_cl_d_h\">".htmlentities($day_name_length < 4 ? substr($d, 0, 3) : $d)."</div>";
		}
		list($month, $year, $month_name, $weekday) = explode(",", strftime("%m,%Y,%B,%w", $first_of_month));
		
		//getting month title
		//$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names
		
		######### CODE TO GET START DATE AND END DATE FOR PRESENTED MONTH ###########
		$from_date_range = $to_date_range = '';//it will hold date string
		//last month
		if($weekday > 0)
		{
			$last_month = $month - 1;
			$last_year = $year;
			if($last_month <= 0){
				$last_month = 12;
				$last_year = $year - 1;
			}
			$first_of_last_month = mktime(0, 0, 0, $last_month, 1, $last_year);
			$days_in_last_month = date("t", $first_of_last_month);
			$dateLastMonthName = date("M", $first_of_last_month);
			$last_month_no = date("m", $first_of_last_month);
			$last_month_yr = date("Y", $first_of_last_month);
			$from_date_range = $last_month_yr."-".$last_month_no."-".(($days_in_last_month - $weekday) + 1);
			
		}
		
		//this month
		$ts_this_month = mktime(0, 0, 0, $month, 1, $year);
		$this_month_no = date("m", $ts_this_month);
		$this_month_yr= date("Y", $ts_this_month);
		if($from_date_range=='')$from_date_range = $this_month_yr."-".$this_month_no."-01";
		$to_date_range = $this_month_yr."-".$this_month_no."-".date("t", $first_of_month);
		for($day = 1, $days_in_month = date("t", $first_of_month); $day <= $days_in_month; $day++, $weekdayTmp++){
			if($weekdayTmp == 7){
				$weekdayTmp   = 0; #start a new week
			}
		}
		if($weekdayTmp != 7){
			$next_month = $month + 1;
			$next_year = $year;
			if($next_month > 12){
				$next_month = 1;
				$next_year = $year + 1;
			}
			$ts_next_month = mktime(0, 0, 0, $next_month, 1, $next_year);
			$first_of_next_month = date("j", $ts_next_month);
			$dateNextMonthName = date("M", $ts_next_month);
			$next_month_no = date("m", $ts_next_month);
			$next_month_yr = date("Y", $ts_next_month);
			$to_date_range = $next_month_yr."-".$next_month_no."-".(7 - $weekdayTmp);
		}
		######### CODE TO GET START DATE AND END DATE FOR PRESENTED MONTH END HERE ###########
		
		$qry_sch = "select facility.name as fac_name, dr.lname, dr.mname, dr.fname, sa.sa_app_start_date, sa.id, sa.sa_facility_id, sa.sa_app_room, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, TIME_FORMAT(sa.sa_app_endtime, '%h:%i %p') as sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, sa.EMR, sp.proc_color, sp.acronym, sp.proc, st.status_name, st.status_icon, sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId from schedule_appointments sa left join slot_procedures sp on sp.id = sa.procedureid left join slot_procedures sp2 on sp2.id = sp.proc_time left join schedule_status st on st.id = sa.sa_patient_app_status_id left join users dr on dr.id = sa.sa_doctor_id left join facility on facility.id = sa.sa_facility_id where sa_facility_id IN (".implode(",", $arr_sess_facs).") and sa_doctor_id IN (".implode(",", $arr_sess_prov).") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (271,203,201,18,19,20) and (sa_app_start_date between '".$from_date_range."' and '".$to_date_range."') order by sa.sa_app_starttime, sa.sa_app_time desc";					
		$res_sch = imw_query($qry_sch);
		while($row_sch = imw_fetch_assoc($res_sch)){
			$dated=$row_sch['sa_app_start_date'];
			$appt_arr[$dated][]=$row_sch;
		}
		//last month
		if($weekday > 0){
			$last_month = $month - 1;
			$last_year = $year;
			if($last_month <= 0){
				$last_month = 12;
				$last_year = $year - 1;
			}
			$first_of_last_month = mktime(0, 0, 0, $last_month, 1, $last_year);
			$days_in_last_month = date("t", $first_of_last_month);
			$dateLastMonthName = date("M", $first_of_last_month);
			$last_month_no = date("m", $first_of_last_month);
			$last_month_yr = date("Y", $first_of_last_month);
		
			for($l_cnt = 0, $ld = $days_in_last_month - $weekday + 1; $ld <= $days_in_last_month; $ld++, $l_cnt++){
				if($l_cnt > 4){
					$ld_cls = "fl mv_cl_s_p";
				}else{
					$ld_cls = "fl mv_cl_p_d";
				}
				if($selday == $ld && intval($selmonth) == intval($last_month)){
					$ld_cls = "fl mv_cl_hili";
					$on_mouse_over_out = "";
				}

				$this_db_date = $last_month_yr."-".$last_month_no."-".$ld;
				$this->cache_prov_working_hrs($this_db_date, $arr_sess_prov);
				$order_file = $dir_path."/load_xml/".$this_db_date."-order.sch";
				$arr_xml = false;
				$str_tmp_order = file_get_contents($order_file);
				list($str_order, $str_fac, $str_prov_sch_timings) = explode("~~~~~", $str_tmp_order);
				$arr_order = unserialize($str_order);
				$arr_fac = unserialize($str_fac);
				$arr_prov_sch_timings = unserialize($str_prov_sch_timings);
				//getting appointments for this date
				
				// Coding to Get Patient Appointment Slots
				/*$qry_sch = "select facility.name as fac_name, dr.lname, dr.mname, dr.fname, sa.id, sa.sa_facility_id, sa.sa_app_room, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, sa.sa_app_endtime as sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, sa.EMR, sp.proc_color, sp.acronym, sp.proc, st.status_name, st.status_icon, sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId from schedule_appointments sa 
				left join slot_procedures sp on sp.id = sa.procedureid 
				left join slot_procedures sp2 on sp2.id = sp.proc_time 
				left join schedule_status st on st.id = sa.sa_patient_app_status_id 
				left join users dr on dr.id = sa.sa_doctor_id 
				left join facility on facility.id = sa.sa_facility_id 
				where sa_facility_id IN (".implode(",", $arr_sess_facs).") and sa_doctor_id IN (".implode(",", $arr_sess_prov).") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (271,203,201,18,19,20) and '".$this_db_date."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime, sa.sa_app_time desc";					
				$res_sch = imw_query($qry_sch); */
				$arr_occupy_slots = array();
				$arr_appt_schedule = array();
				$arr_empty_provider = array();
				$calendar_data = "";
				//while($row_sch = imw_fetch_array($res_sch)){
				foreach($appt_arr[$this_db_date] as $key=>$row_sch){
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["patient_id"][] = $row_sch["sa_patient_id"];
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["start_time"][] = $row_sch["sa_app_starttime"];
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["end_time"][] = $row_sch["sa_app_endtime"];
						$assign_loop_timings = strtotime($row_sch["sa_app_starttime"]);
						while($assign_loop_timings < strtotime($row_sch["sa_app_endtime"])){
							$loop_hr_mn = date("H:i", $assign_loop_timings);
							list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
							if($loop_mn == 60){
								$loop_mn = 0;
								$loop_hr++;
							}
							$ed_loop_hr = $loop_hr;
							$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
							if($ed_loop_mn == 60){
								$ed_loop_mn = 0;
								$ed_loop_hr++;
							}								
							$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
							$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
							$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
							$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
							$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
							$arr_occupy_slots[$row_sch["sa_doctor_id"]][] = $this_time;
							$assign_loop_timings += (DEFAULT_TIME_SLOT * 60);
					}

				}
				if(count($arr_prov_sch_timings) > 0){	
					$calendar .= "<div class=\"mnth_bl_sh section ".$ld_cls."\" ".$on_mouse_over_out." style=\"height:auto;\">";
				}
				else{
					$calendar .= "<div class=\"mnth_bl_sh section ".$ld_cls."\" ".$on_mouse_over_out." style=\"height:auto; background-color:".DEFAULT_OFFICE_CLOSED_COLOR."\">";
				}
				if(strlen(trim($ld)) == "1"){
					$last_day = "0".$ld;
				}
				else{
					$last_day = $ld;
				}
				$base_day_scheduler = $last_month_no."-".$last_day."-".$last_month_yr;
				
				$cmc_print_arr[$dateLastMonthName."-".$ld]['day_name'] = $dateLastMonthName." ".$ld;
				
				$calendar .="<div class=\"section_header\" style=\"text-align:left; \" >".$dateLastMonthName." ".$ld." </div>
					<div id=\"hold_".$ld."_".$last_month_no."\" style=\"min-height:".($div_height - 50)."px;\">
						<div id=\"wn_".$ld."_".$last_month_no."\" style=\"min-height:".($div_height - 50)."px;\">
							<div id=\"lyr1_".$ld."_".$last_month_no."\" class=\"col-sm-12\">";
							$all_day_provider = array();
								if(count($arr_prov_sch_timings) > 0){		
									unset($new_arr);
									for($i=0; $i<count($arr_sess_prov); $i++){
										$class = "";
										if($ap_cnt % 2 == 0){
											$class = "background-color:#F0ECEC;";
										}
										
										$file_name = $dir_path."/load_xml/".$this_db_date."-".$arr_sess_prov[$i].".sch";
										$str_fragment = file_get_contents($file_name);
										$arr_fragment = unserialize($str_fragment);
										if($int_frag_cnt == 0){
											$arr_xml = $arr_fragment;
										}else{
											$arr_xml[$arr_sess_prov[$i]] = $arr_fragment[$arr_sess_prov[$i]];
										}	
										
										$max_time = 0;
										$min_time = mktime(23, 59, 59, date("m"), date("d"), date("Y")); 
										if($min_time > strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"])){
											$min_time = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]);
										}
										if($max_time < strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"])){
											$max_time = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"]);
										}
										
										$loop_timings = $min_time;
										$loop_timings = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]);
										$new_facility_name = "";
										$arr_disp_provider = array();
										$x=0;
										$p = 0;
										$previous_end_time = "";
										
										// Coding to get provider Facility availability
										while($loop_timings < strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"])){
												$previous_facility = $new_facility_name;
												$previous_status = $new_status;
												$loop_hr_mn = date("H:i", $loop_timings);
												list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
												if($loop_mn == 60){
													$loop_mn = 0;
													$loop_hr++;
												}
												$ed_loop_hr = $loop_hr;
												$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
												if($ed_loop_mn == 60){
													$ed_loop_mn = 0;
													$ed_loop_hr++;
												}								
												$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
												$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
												$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
												$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
												$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
												
												$loop_timings += (DEFAULT_TIME_SLOT * 60);
												$new_facility_name = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_name"];
												$new_status = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["status"];
																																																
												if($previous_facility != $new_facility_name || ($previous_status != $new_status)){
													$arr_disp_provider[$arr_sess_prov[$i]]["facility_name"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_name"];
													$arr_disp_provider[$arr_sess_prov[$i]]["start_time"][$x] = $st_hr_fnl.":".$st_mn_fnl.":00";
													$arr_disp_provider[$arr_sess_prov[$i]]["status"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["status"];
													$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_id"];
													$x++;
												}
												$arr_disp_provider[$arr_sess_prov[$i]]["end_time"][(int)$x-1] = $ed_hr_fnl.":".$ed_mn_fnl.":00";
												$arr_disp_provider[$arr_sess_prov[$i]]["date"][(int)$x-1] = $this_db_date;
												
												
												// TO get Provider Empty Slots
												if(!(in_array($this_time, $arr_occupy_slots[$arr_sess_prov[$i]]))){
													$new_start_time_slot = $st_hr_fnl.":".$st_mn_fnl.":00";
													$new_end_time_slot = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													if($previous_end_time_slot != $new_start_time_slot ){
														if($p == 0){
															for($a=0;$a<count($arr_disp_provider[$arr_sess_prov[$i]]);$a++){ 
																if($arr_disp_provider[$arr_sess_prov[$i]]["status"][$a] == "on" && in_array($arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a],$arr_sess_facs)){
																	$provider_start_time = $arr_disp_provider[$arr_sess_prov[$i]]["start_time"][$a];
																}
															}
															$arr_empty_provider[$arr_sess_prov[$i]]["start_time"][$p] = $provider_start_time;
														}
														else{
															$arr_empty_provider[$arr_sess_prov[$i]]["start_time"][$p] = $st_hr_fnl.":".$st_mn_fnl.":00";
														}
														$p++;
													}
													$arr_empty_provider[$arr_sess_prov[$i]]["end_time"][(int)$p-1] = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													$previous_start_time_slot = $st_hr_fnl.":".$st_mn_fnl.":00";
													$previous_end_time_slot = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													
												} 
												
												
										}
										if(count($arr_disp_provider[$arr_sess_prov[$i]]) != '0'){
												for($a=0;$a<count($arr_disp_provider[$arr_sess_prov[$i]]); $a++){
													if(in_array($arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a],$arr_sess_facs)){
														$provider_detail = getUserDetails($arr_sess_prov[$i]);
														$provider_id = $arr_sess_prov[$i];
														$fac_id = $arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a];	
														
														$provider_fn = $provider_detail['lname'].', '.$provider_detail['fname'].' '.$provider_detail['mname'];
														$placeBr = '';
														if(strlen($provider_detail['mname'])>3)
														{
															$placeBr = '<br/>';	
														}														
														$provider_name = substr($provider_detail['fname'],0,1).substr($provider_detail['lname'],0,1).' '.$provider_detail['mname'];
														$no_of_appts = count($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["patient_id"]);
														if($arr_disp_provider[$arr_sess_prov[$i]]["status"][$a] == "on" && !isset($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["repeat"])){																
																$date_or = $last_month_yr."-".$last_month_no."-".$ld;
																$reqQry = 'SELECT sch_or_id,assign_or FROM schedule_or_allocations WHERE date_or = "'.$date_or.'" and provider_id = "'.$provider_id.'" and facility_id = "'.$fac_id.'" order by sch_or_id DESC LIMIT 0,1';
																$result_data = imw_query($reqQry);
																$result_data_arr = imw_fetch_assoc($result_data);
																$or_val = '';
																if(imw_num_rows($result_data)>0)
																{
																	$or_val = $result_data_arr['assign_or'];	
																}
																$dash_appt_prov = ' - ';
																if($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["start_time"][0] == ""){ $start_time_arr = explode(".",$arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]); $start_tm_init = explode(":",$start_time_arr[0]); $start_time = date("h:i A",mktime($start_tm_init[0],$start_tm_init[1])); }else{$start_time = $arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["start_time"][0];}																
																$calendar_data2 = "<div class=\"row bdr_btm  padd_3\" style=\" ".$class."\" onDblClick=\"window.location.href='base_day_scheduler.php?sel_date=".$base_day_scheduler."&sel_fac=".$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]."&sel_prov=".$arr_sess_prov[$i]."'\"><div class=\"col-sm-8 text-left\" style=\"cursor:pointer;\" title=\" Provider: ".$provider_fn." \n Facility: ".$arr_disp_provider[$arr_sess_prov[$i]]["facility_name"][$a]." \n No. of Appts: ".$no_of_appts."\"> ".$provider_name.' - '.$no_of_appts.$dash_appt_prov.$placeBr.$start_time."</div><div class=\"col-sm-4\"> <input type=\"text\" size=\"5\" value=\"".$or_val."\" onblur=\"add_or_record('".$provider_id."','".$fac_id."','".$date_or."',this); \" class=\"form-control\" /></div></div> ";
																$key=$this->getNextkey(strtotime($start_time),$new_arr);
																$new_arr[$key]=$calendar_data2;			
																$arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["repeat"] = 1;	
																$cmc_print_arr[$dateLastMonthName."-".$ld]['providers'][$provider_id]['pname'] = $provider_name;
																$cmc_print_arr[$dateLastMonthName."-".$ld]['providers'][$provider_id]['no_of_appts'] = $no_of_appts;
																$cmc_print_arr[$dateLastMonthName."-".$ld]['providers'][$provider_id]['start_time'] = $start_time;
																$cmc_print_arr[$dateLastMonthName."-".$ld]['providers'][$provider_id]['or_val'] = $or_val;
														}
													}
												}														
											
										}
										
								}
								
								ksort($new_arr);
								$calendar_data.=implode('',$new_arr);
								unset($new_arr);
								$display_msg = "0";
								//$total_appt = imw_num_rows($res_sch);
								$total_appt = sizeof($appt_arr[$this_db_date]);
								}else{
									$display_msg = "1";
									$calendar_data .= "<div class=\"ml10\" style=\"padding-top:30px;\">Office Closed</div>";
									$total_appt = 0;
									$cmc_print_arr[$dateLastMonthName."-".$ld]['closed_status'] = 'active';
								}
								if($calendar_data != ""){
										$calendar .= $calendar_data;
										$cmc_print_arr[$dateLastMonthName."-".$ld]['status'] = 'open';
								}else{
									$calendar .= "<div class=\"ml10\">No Appointments</div>";
									$cmc_print_arr[$dateLastMonthName."-".$ld]['status'] = 'no_appts';									
								}
				$calendar .= "
							</div></div></div><br clear=\"all\"><div class=\"fr mt10 mr3\">Total Appt. ".$total_appt."</div></div>"; #initial 'empty' days
				$cmc_print_arr[$dateLastMonthName."-".$ld]['total_appts'] = $total_appt;
			}
		
		}
		//this month
		for($day = 1, $days_in_month = date("t", $first_of_month); $day <= $days_in_month; $day++, $weekday++){
			if($weekday == 7){
				$weekday   = 0; #start a new week
				$calendar .= " ";
			}
			if($weekday == 5 || $weekday == 6){
				$ld_cls = "fl mv_cl_s_d";
			}else{
				$ld_cls = "fl mv_cl_d_d";
			}

			$ts_this_month = mktime(0, 0, 0, $month, 1, $year);
			$dateThisMonthName = date("M", $ts_this_month);
			$this_month_no = date("m", $ts_this_month);
			$this_month_yr= date("Y", $ts_this_month);
	
			if($selday == $day && intval($selmonth) == intval($month)){
				$ld_cls = "fl mv_cl_hili";
				$on_mouse_over_out = "";
			}
			if($day<10){
				$day = '0'.$day;
			}
			$this_db_date = $this_month_yr."-".$this_month_no."-".$day;
			$this->cache_prov_working_hrs($this_db_date, $arr_sess_prov);
			$order_file = $dir_path."/load_xml/".$this_db_date."-order.sch";
			$arr_xml = false;
			$str_tmp_order = file_get_contents($order_file);
			list($str_order, $str_fac, $str_prov_sch_timings) = explode("~~~~~", $str_tmp_order);
			$arr_order = unserialize($str_order);
			$arr_fac = unserialize($str_fac);
			$arr_prov_sch_timings = unserialize($str_prov_sch_timings);
			
			//getting appointments for this date
			/*$qry_sch = "select facility.name as fac_name, dr.lname, dr.mname, dr.fname, sa.id, sa.sa_facility_id, sa.sa_app_room, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, TIME_FORMAT(sa.sa_app_endtime, '%h:%i %p') as sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, sa.EMR, sp.proc_color, sp.acronym, sp.proc, st.status_name, st.status_icon, sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId from schedule_appointments sa left join slot_procedures sp on sp.id = sa.procedureid left join slot_procedures sp2 on sp2.id = sp.proc_time left join schedule_status st on st.id = sa.sa_patient_app_status_id left join users dr on dr.id = sa.sa_doctor_id left join facility on facility.id = sa.sa_facility_id where sa_facility_id IN (".implode(",", $arr_sess_facs).") and sa_doctor_id IN (".implode(",", $arr_sess_prov).") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (271,203,201,18,19,20) and '".$this_db_date."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime, sa.sa_app_time desc";					
			$res_sch = imw_query($qry_sch);*/
			$arr_occupy_slots = array();
				$arr_appt_schedule = array();
				$arr_empty_provider = array();
				$calendar_data = "";
				//while($row_sch = imw_fetch_array($res_sch)){
				foreach($appt_arr[$this_db_date] as $key=>$row_sch){
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["patient_id"][] = $row_sch["sa_patient_id"];
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["start_time"][] = $row_sch["sa_app_starttime"];
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["end_time"][] = $row_sch["sa_app_endtime"];
						$assign_loop_timings = strtotime($row_sch["sa_app_starttime"]);
						while($assign_loop_timings < strtotime($row_sch["sa_app_endtime"])){
							$loop_hr_mn = date("H:i", $assign_loop_timings);
							list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
							if($loop_mn == 60){
								$loop_mn = 0;
								$loop_hr++;
							}
							$ed_loop_hr = $loop_hr;
							$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
							if($ed_loop_mn == 60){
								$ed_loop_mn = 0;
								$ed_loop_hr++;
							}								
							$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
							$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
							$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
							$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
							$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
							$arr_occupy_slots[$row_sch["sa_doctor_id"]][] = $this_time;
							$assign_loop_timings += (DEFAULT_TIME_SLOT * 60);
					}

				}

			if(count($arr_prov_sch_timings) > 0){	
					$calendar .= "<div class=\"mnth_bl_sh section ".$ld_cls."\" ".$on_mouse_over_out." style=\"height:auto;\">";
				}
				else{
					$calendar .= "<div class=\"mnth_bl_sh section ".$ld_cls."\" ".$on_mouse_over_out." style=\"height:auto; background-color:".DEFAULT_OFFICE_CLOSED_COLOR."\">";
				}
			if(strlen(trim($day)) == "1"){
					$this_day = "0".$day;
				}
				else{
					$this_day = $day;
				}
			$base_day_scheduler = $this_month_no."-".$this_day."-".$this_month_yr;
			$cmc_print_arr[$dateThisMonthName."-".$day]['day_name'] = $dateThisMonthName." ".$day;
			$calendar .= "
				<div class=\"section_header\" style=\"text-align:left;\">".$dateThisMonthName." ".$day."</div>
				<div id=\"hold_".$day."_".$this_month_no."\" style=\"min-height:".($div_height - 50)."px\">
					<div id=\"wn_".$day."_".$this_month_no."\" style=\"min-height:".($div_height - 50)."px\">
						<div id=\"lyr1_".$day."_".$this_month_no."\" class=\"col-sm-12\">";	
						$all_day_provider = array();								
								if(count($arr_prov_sch_timings) > 0){	
									unset($new_arr);
									for($i=0; $i<count($arr_sess_prov); $i++){
										$class = "";
										if($ap_cnt % 2 == 0){
											$class = "background-color:#F0ECEC;";
										}
										
										$file_name = $dir_path."/load_xml/".$this_db_date."-".$arr_sess_prov[$i].".sch";
										$str_fragment = file_get_contents($file_name);
										$arr_fragment = unserialize($str_fragment);
										if($int_frag_cnt == 0){
											$arr_xml = $arr_fragment;
										}else{
											$arr_xml[$arr_sess_prov[$i]] = $arr_fragment[$arr_sess_prov[$i]];
										}	
										
										
										
										
										$max_time = 0;
										$min_time = mktime(23, 59, 59, date("m"), date("d"), date("Y")); 
										if($min_time > strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"])){
											$min_time = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]);
										}
										if($max_time < strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"])){
											$max_time = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"]);
										}
										
										$loop_timings = $min_time;
										$loop_timings = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]);
										$new_facility_name = "";
										$arr_disp_provider = array();
										$x=0;
										$p = 0;
										$previous_end_time = "";
										
										// Coding to get provider Facility availability
										while($loop_timings < strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"])){
												$previous_facility = $new_facility_name;
												$previous_status = $new_status;
												$loop_hr_mn = date("H:i", $loop_timings);
												list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
												if($loop_mn == 60){
													$loop_mn = 0;
													$loop_hr++;
												}
												$ed_loop_hr = $loop_hr;
												$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
												if($ed_loop_mn == 60){
													$ed_loop_mn = 0;
													$ed_loop_hr++;
												}								
												$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
												$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
												$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
												$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
												$check_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
												
												$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
												$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
												$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
												$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
												$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
												
												$loop_timings += (DEFAULT_TIME_SLOT * 60);
												$new_facility_name = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_name"];
												$new_status = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["status"];																																				
												
												if($previous_facility != $new_facility_name || ($previous_status != $new_status)){
													$arr_disp_provider[$arr_sess_prov[$i]]["facility_name"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_name"];
													$arr_disp_provider[$arr_sess_prov[$i]]["start_time"][$x] = $st_hr_fnl.":".$st_mn_fnl.":00";
													$arr_disp_provider[$arr_sess_prov[$i]]["status"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["status"];
													$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_id"];
													$x++;
												}
												$arr_disp_provider[$arr_sess_prov[$i]]["end_time"][(int)$x-1] = $ed_hr_fnl.":".$ed_mn_fnl.":00";
												$arr_disp_provider[$arr_sess_prov[$i]]["date"][(int)$x-1] = $this_db_date;
												
												
												// TO get Provider Empty Slots
												if(!(in_array($this_time, $arr_occupy_slots[$arr_sess_prov[$i]]))){
													$new_start_time_slot = $st_hr_fnl.":".$st_mn_fnl.":00";
													$new_end_time_slot = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													if($previous_end_time_slot != $new_start_time_slot ){
														if($p == 0){
															for($a=0;$a<count($arr_disp_provider[$arr_sess_prov[$i]]);$a++){ 
																if($arr_disp_provider[$arr_sess_prov[$i]]["status"][$a] == "on" && in_array($arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a],$arr_sess_facs)){
																	$provider_start_time = $arr_disp_provider[$arr_sess_prov[$i]]["start_time"][$a];
																}
															}
															$arr_empty_provider[$arr_sess_prov[$i]]["start_time"][$p] = $provider_start_time;
														}
														else{
															$arr_empty_provider[$arr_sess_prov[$i]]["start_time"][$p] = $st_hr_fnl.":".$st_mn_fnl.":00";
														}
														$p++;
													}
													$arr_empty_provider[$arr_sess_prov[$i]]["end_time"][(int)$p-1] = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													$previous_start_time_slot = $st_hr_fnl.":".$st_mn_fnl.":00";
													$previous_end_time_slot = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													
												} 
												
												
										}
										if(count($arr_disp_provider[$arr_sess_prov[$i]]) != '0'){
												for($a=0;$a<count($arr_disp_provider[$arr_sess_prov[$i]]); $a++){
													if(in_array($arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a],$arr_sess_facs)){
														
														$provider_detail = getUserDetails($arr_sess_prov[$i]);
														$provider_id = $arr_sess_prov[$i];
														$fac_id = $arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a];	
														
														$provider_fn = $provider_detail['lname'].', '.$provider_detail['fname'].' '.$provider_detail['mname'];
														$provider_name_show = substr($provider_detail['fname'],0,1).substr($provider_detail['lname'],0,1).' '.$provider_detail['mname'];
														$provider_name = substr($provider_detail['fname'],0,1).trim(substr($provider_detail['mname'],0,1))." ".$provider_detail['lname'];
														$placeBr = '';
														if(strlen($provider_detail['mname'])>3)
														{
															$placeBr = '<br/>';	
														}
														$no_of_appts = count($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["patient_id"]);
														if($arr_disp_provider[$arr_sess_prov[$i]]["status"][$a] == "on" && !isset($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["repeat"])){																																
																$date_or = $this_month_yr."-".$this_month_no."-".$day;
																$reqQry = 'SELECT sch_or_id,assign_or FROM schedule_or_allocations WHERE date_or = "'.$date_or.'" and provider_id = "'.$provider_id.'" and facility_id = "'.$fac_id.'" order by sch_or_id DESC LIMIT 0,1';
																$result_data = imw_query($reqQry);
																$result_data_arr = imw_fetch_assoc($result_data);
																$or_val = '';
																if(imw_num_rows($result_data)>0)
																{
																	$or_val = $result_data_arr['assign_or'];	
																}
																$dash_appt_prov = ' - '; //if($no_of_appts > 0){  }else{ $dash_appt_prov = ''; }
																if($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["start_time"][0] == ""){ $start_time_arr = explode(".",$arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]); $start_tm_init = explode(":",$start_time_arr[0]); $start_time = date("h:i A",mktime($start_tm_init[0],$start_tm_init[1])); }else{$start_time = $arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["start_time"][0];}																
																$calendar_data2 = "
																	<div class=\"row bdr_btm  padd_3\" style=\" ".$class."\" onDblClick=\"window.location.href='base_day_scheduler.php?sel_date=".$base_day_scheduler."&sel_fac=".$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]."&sel_prov=".$arr_sess_prov[$i]."'\">
																		<div class=\"col-sm-8 text-left\" style=\"cursor:pointer;\" title=\" Provider: ".$provider_fn." \n Facility: ".$arr_disp_provider[$arr_sess_prov[$i]]["facility_name"][$a]." \n No. of Appts: ".$no_of_appts."\"> ".$provider_name_show.' - '.$no_of_appts.$dash_appt_prov.$placeBr.$start_time."</div>
																		<div class=\"col-sm-4\"> <input type=\"text\" size=\"5\" value=\"".$or_val."\" onblur=\"add_or_record('".$provider_id."','".$fac_id."','".$date_or."',this); \" class=\"form-control\" /></div>	
																	</div>
																	 ";
																$key=$this->getNextkey(strtotime($start_time),$new_arr);
																$new_arr[$key]=$calendar_data2;	
																$arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["repeat"] = 1;
																$cmc_print_arr[$dateThisMonthName."-".$day]['providers'][$provider_id]['pname'] = $provider_name;
																$cmc_print_arr[$dateThisMonthName."-".$day]['providers'][$provider_id]['no_of_appts'] = $no_of_appts;
																$cmc_print_arr[$dateThisMonthName."-".$day]['providers'][$provider_id]['start_time'] = $start_time;
																$cmc_print_arr[$dateThisMonthName."-".$day]['providers'][$provider_id]['or_val'] = $or_val;																
														}
														
													}
												}														
											
										}
										
								}
								ksort($new_arr);
								$calendar_data.=implode('',$new_arr);
								unset($new_arr);
									
								$display_msg = "0";
								//$total_appt = imw_num_rows($res_sch);
								$total_appt = sizeof($appt_arr[$this_db_date]);
								}else{
									$display_msg = "1";
									$calendar_data .= "<div class=\"ml10\" style=\"padding-top:30px;\">Office Closed</div>";
									$total_appt = 0;
									$cmc_print_arr[$dateThisMonthName."-".$day]['closed_status'] = 'active';
								}
								if($calendar_data != ""){
										$calendar .= $calendar_data;
										$cmc_print_arr[$dateThisMonthName."-".$day]['status'] = 'open';
								}else{
									$calendar .= "<div class=\"ml10\">No Appointments</div>";
									$cmc_print_arr[$dateThisMonthName."-".$day]['status'] = 'no_appts';
								}
				$calendar .= "</div></div></div><div class=\"fr mt10 mr3\">Total Appt. ".$total_appt."</div></div>";
			$cmc_print_arr[$dateThisMonthName."-".$day]['total_appts'] = $total_appt;
		}

		//next month
		if($weekday != 7){
			$next_month = $month + 1;
			$next_year = $year;
			if($next_month > 12){
				$next_month = 1;
				$next_year = $year + 1;
			}
			$ts_next_month = mktime(0, 0, 0, $next_month, 1, $next_year);
			$first_of_next_month = date("j", $ts_next_month);
			$dateNextMonthName = date("M", $ts_next_month);
			$next_month_no = date("m", $ts_next_month);
			$next_month_yr = date("Y", $ts_next_month);
		
			for($nd = $first_of_next_month; $nd <= (7 - $weekday); $nd++){
				if($nd == (7 - $weekday) || $nd == (7 - $weekday - 1)){
					$ld_cls = "fl mv_cl_s_p";
				}else{
					$ld_cls = "fl mv_cl_p_d";
				}
				if($selday == $nd && intval($selmonth) == intval($next_month)){
					$ld_cls = "fl mv_cl_hili";
					$on_mouse_over_out = "";
				}
				$nd=($nd<10)?"0".$nd:$nd;
				$this_db_date = $next_month_yr."-".$next_month_no."-".$nd;
				$this->cache_prov_working_hrs($this_db_date, $arr_sess_prov);
				$order_file = $dir_path."/load_xml/".$this_db_date."-order.sch";
				$arr_xml = false;
				$str_tmp_order = file_get_contents($order_file);
				list($str_order, $str_fac, $str_prov_sch_timings) = explode("~~~~~", $str_tmp_order);
				$arr_order = unserialize($str_order);
				$arr_fac = unserialize($str_fac);
				$arr_prov_sch_timings = unserialize($str_prov_sch_timings);


				//getting appointments for this date
				/*$qry_sch = "select facility.name as fac_name, dr.lname, dr.mname, dr.fname, sa.id, sa.sa_facility_id, sa.sa_app_room, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, TIME_FORMAT(sa.sa_app_endtime, '%h:%i %p') as sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, sa.EMR, sp.proc_color, sp.acronym, sp.proc, st.status_name, st.status_icon, sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId from schedule_appointments sa left join slot_procedures sp on sp.id = sa.procedureid left join slot_procedures sp2 on sp2.id = sp.proc_time left join schedule_status st on st.id = sa.sa_patient_app_status_id left join users dr on dr.id = sa.sa_doctor_id left join facility on facility.id = sa.sa_facility_id where sa_facility_id IN (".implode(",", $arr_sess_facs).") and sa_doctor_id IN (".implode(",", $arr_sess_prov).") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (271,203,201,18,19,20) and '".$this_db_date."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime, sa.sa_app_time desc";					
				$res_sch = imw_query($qry_sch);*/
				$arr_occupy_slots = array();
				$arr_appt_schedule = array();
				$arr_empty_provider = array();
				$calendar_data = "";
				//while($row_sch = imw_fetch_array($res_sch)){
				foreach($appt_arr[$this_db_date] as $key=>$row_sch){
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["patient_id"][] = $row_sch["sa_patient_id"];
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["start_time"][] = $row_sch["sa_app_starttime"];
						$arr_appt_schedule[$row_sch["sa_facility_id"]][$row_sch["sa_doctor_id"]]["end_time"][] = $row_sch["sa_app_endtime"];
						$assign_loop_timings = strtotime($row_sch["sa_app_starttime"]);
						while($assign_loop_timings < strtotime($row_sch["sa_app_endtime"])){
							$loop_hr_mn = date("H:i", $assign_loop_timings);
							list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
							if($loop_mn == 60){
								$loop_mn = 0;
								$loop_hr++;
							}
							$ed_loop_hr = $loop_hr;
							$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
							if($ed_loop_mn == 60){
								$ed_loop_mn = 0;
								$ed_loop_hr++;
							}								
							$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
							$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
							$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
							$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
							$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
							$arr_occupy_slots[$row_sch["sa_doctor_id"]][] = $this_time;
							$assign_loop_timings += (DEFAULT_TIME_SLOT * 60);
					}

				}
				
				if(count($arr_prov_sch_timings) > 0){	
					$calendar .= "<div class=\"mnth_bl_sh section ".$ld_cls."\" ".$on_mouse_over_out." style=\"height:auto;\">";
				}
				else{
					$calendar .= "<div class=\"mnth_bl_sh section ".$ld_cls."\" ".$on_mouse_over_out." style=\"height:auto; background-color:".DEFAULT_OFFICE_CLOSED_COLOR."\">";
				}
				
				if(strlen(trim($nd)) == "1"){
					$next_day = "0".$nd;
				}
				else{
					$next_day = $nd;
				}
				$base_day_scheduler = $next_month_no."-".$next_day."-".$next_month_yr;
				$cmc_print_arr[$dateNextMonthName."-".$nd]['day_name'] = $dateNextMonthName." ".$nd;				
				$calendar .= "
					<div class=\"section_header\" style=\"text-align:left;\">".$dateNextMonthName." ".$nd."	</div>
					<div id=\"hold_".$nd."_".$next_month_no."\" style=\"min-height:".($div_height - 50)."px\">
						<div id=\"wn_".$nd."_".$next_month_no."\" style=\"min-height:".($div_height - 50)."px\">
							<div id=\"lyr1_".$nd."_".$next_month_no."\" class=\"col-sm-12\">";		
							$all_day_provider = array();							
								if(count($arr_prov_sch_timings) > 0){	
									unset($new_arr);
									for($i=0; $i<count($arr_sess_prov); $i++){
										$class = "";
										if($ap_cnt % 2 == 0){
											$class = "background-color:#F0ECEC;";
										}
										
										$file_name = $dir_path."/load_xml/".$this_db_date."-".$arr_sess_prov[$i].".sch";
										$str_fragment = file_get_contents($file_name);
										$arr_fragment = unserialize($str_fragment);
										if($int_frag_cnt == 0){
											$arr_xml = $arr_fragment;
										}else{
											$arr_xml[$arr_sess_prov[$i]] = $arr_fragment[$arr_sess_prov[$i]];
										}	
																																								
										$max_time = 0;
										$min_time = mktime(23, 59, 59, date("m"), date("d"), date("Y")); 
										if($min_time > strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"])){
											$min_time = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]);
										}
										if($max_time < strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"])){
											$max_time = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"]);
										}
										
										$loop_timings = $min_time;
										$loop_timings = strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]);
										$new_facility_name = "";
										$arr_disp_provider = array();
										$x=0;
										$p = 0;
										$previous_end_time = "";
										
										// Coding to get provider Facility availability
										while($loop_timings < strtotime($arr_prov_sch_timings[$arr_sess_prov[$i]]["ed"])){
												$previous_facility = $new_facility_name;
												$previous_status = $new_status;
												$loop_hr_mn = date("H:i", $loop_timings);
												list($loop_hr, $loop_mn) = explode(":", $loop_hr_mn);								
												if($loop_mn == 60){
													$loop_mn = 0;
													$loop_hr++;
												}
												$ed_loop_hr = $loop_hr;
												$ed_loop_mn = $loop_mn + DEFAULT_TIME_SLOT;
												if($ed_loop_mn == 60){
													$ed_loop_mn = 0;
													$ed_loop_hr++;
												}								
												$st_hr_fnl = ((int)$loop_hr < 10) ? "0".(int)$loop_hr : (int)$loop_hr;
												$st_mn_fnl = ((int)$loop_mn < 10) ? "0".(int)$loop_mn : (int)$loop_mn;								
												$ed_hr_fnl = ((int)$ed_loop_hr < 10) ? "0".(int)$ed_loop_hr : (int)$ed_loop_hr;
												$ed_mn_fnl = ((int)$ed_loop_mn < 10) ? "0".(int)$ed_loop_mn : (int)$ed_loop_mn;								
												$this_time = $st_hr_fnl.":".$st_mn_fnl."-".$ed_hr_fnl.":".$ed_mn_fnl;
												
												$loop_timings += (DEFAULT_TIME_SLOT * 60);
												$new_facility_name = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_name"];
												$new_status = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["status"];
												
												if($previous_facility != $new_facility_name || ($previous_status != $new_status)){
													$arr_disp_provider[$arr_sess_prov[$i]]["facility_name"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_name"];
													$arr_disp_provider[$arr_sess_prov[$i]]["start_time"][$x] = $st_hr_fnl.":".$st_mn_fnl.":00";
													$arr_disp_provider[$arr_sess_prov[$i]]["status"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["status"];
													$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$x] = $arr_xml[$arr_sess_prov[$i]]["slots"][$this_time]["fac_id"];
													$x++;
												}
												$arr_disp_provider[$arr_sess_prov[$i]]["end_time"][(int)$x-1] = $ed_hr_fnl.":".$ed_mn_fnl.":00";
												$arr_disp_provider[$arr_sess_prov[$i]]["date"][(int)$x-1] = $this_db_date;
												
												
												// TO get Provider Empty Slots
												if(!(in_array($this_time, $arr_occupy_slots[$arr_sess_prov[$i]]))){
													$new_start_time_slot = $st_hr_fnl.":".$st_mn_fnl.":00";
													$new_end_time_slot = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													if($previous_end_time_slot != $new_start_time_slot ){
														if($p == 0){
															for($a=0;$a<count($arr_disp_provider[$arr_sess_prov[$i]]);$a++){ 
																if($arr_disp_provider[$arr_sess_prov[$i]]["status"][$a] == "on" && in_array($arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a],$arr_sess_facs)){
																	$provider_start_time = $arr_disp_provider[$arr_sess_prov[$i]]["start_time"][$a];
																}
															}
															$arr_empty_provider[$arr_sess_prov[$i]]["start_time"][$p] = $provider_start_time;
														}
														else{
															$arr_empty_provider[$arr_sess_prov[$i]]["start_time"][$p] = $st_hr_fnl.":".$st_mn_fnl.":00";
														}
														$p++;
													}
													$arr_empty_provider[$arr_sess_prov[$i]]["end_time"][(int)$p-1] = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													$previous_start_time_slot = $st_hr_fnl.":".$st_mn_fnl.":00";
													$previous_end_time_slot = $ed_hr_fnl.":".$ed_mn_fnl.":00";
													
												} 
										}
										if(count($arr_disp_provider[$arr_sess_prov[$i]]) != '0'){
												for($a=0;$a<count($arr_disp_provider[$arr_sess_prov[$i]]); $a++){
													if(in_array($arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a],$arr_sess_facs)){
														$provider_detail = getUserDetails($arr_sess_prov[$i]);
														$provider_id = $arr_sess_prov[$i];
														$fac_id = $arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a];	
														
														$provider_fn = $provider_detail['lname'].', '.$provider_detail['fname'].' '.$provider_detail['mname'];
														$provider_name = substr($provider_detail['fname'],0,1).substr($provider_detail['lname'],0,1).' '.$provider_detail['mname'];
														$placeBr = '';
														if(strlen($provider_detail['mname'])>3)
														{
															$placeBr = '<br/>';	
														}														
														$no_of_appts = count($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["patient_id"]);
														if($arr_disp_provider[$arr_sess_prov[$i]]["status"][$a] == "on" && !isset($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["repeat"])){																														
															$date_or = $next_month_yr."-".$next_month_no."-".$nd;
															$reqQry = 'SELECT sch_or_id,assign_or FROM schedule_or_allocations WHERE date_or = "'.$date_or.'" and provider_id = "'.$provider_id.'" and facility_id = "'.$fac_id.'" order by sch_or_id DESC LIMIT 0,1';
															$result_data = imw_query($reqQry);
															$result_data_arr = imw_fetch_assoc($result_data);
															$or_val = '';
															if(imw_num_rows($result_data)>0)
															{
																$or_val = $result_data_arr['assign_or'];	
															}
															$dash_appt_prov = ' - '; //if($no_of_appts > 0){  }else{ $dash_appt_prov = ''; }
															if($arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["start_time"][0] == ""){ $start_time_arr = explode(".",$arr_prov_sch_timings[$arr_sess_prov[$i]]["st"]); $start_tm_init = explode(":",$start_time_arr[0]); $start_time = date("h:i A",mktime($start_tm_init[0],$start_tm_init[1])); }else{$start_time = $arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["start_time"][0];}																
															$calendar_data2 = "<div class=\"row bdr_btm  padd_3\" style=\" ".$class."\" onDblClick=\"window.location.href='base_day_scheduler.php?sel_date=".$base_day_scheduler."&sel_fac=".$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]."&sel_prov=".$arr_sess_prov[$i]."'\"><div class=\"col-sm-8 text-left\" style=\"cursor:pointer;\" title=\" Provider: ".$provider_fn." \n Facility: ".$arr_disp_provider[$arr_sess_prov[$i]]["facility_name"][$a]." \n No. of Appts: ".$no_of_appts."\">".$provider_name.' - '.$no_of_appts.$dash_appt_prov.$placeBr.$start_time."</div><div class=\"col-sm-4\"><input type=\"text\" size=\"5\" value=\"".$or_val."\" onblur=\"add_or_record('".$provider_id."','".$fac_id."','".$date_or."',this); \" class=\"form-control\" /></div></div> ";
															$key=$this->getNextkey(strtotime($start_time),$new_arr);
															$new_arr[$key]=$calendar_data2;	
															
															$arr_appt_schedule[$arr_disp_provider[$arr_sess_prov[$i]]["fac_id"][$a]][$arr_sess_prov[$i]]["repeat"] = 1;
															$cmc_print_arr[$dateNextMonthName."-".$nd]['providers'][$provider_id]['pname'] = $provider_name;
															$cmc_print_arr[$dateNextMonthName."-".$nd]['providers'][$provider_id]['no_of_appts'] = $no_of_appts;
															$cmc_print_arr[$dateNextMonthName."-".$nd]['providers'][$provider_id]['start_time'] = $start_time;
															$cmc_print_arr[$dateNextMonthName."-".$nd]['providers'][$provider_id]['or_val'] = $or_val;															
														}
													}
												}														
											
										}
										
								}
								ksort($new_arr);
								$calendar_data.=implode('',$new_arr);
								unset($new_arr);
								$display_msg = "0";
								//$total_appt = imw_num_rows($res_sch);
								$total_appt = sizeof($appt_arr[$this_db_date]);
								}else{
									$display_msg = "1";
									$calendar_data .= "<div class=\"ml10\" style=\"padding-top:30px;\">Office Closed</div>";
									$total_appt = 0;
									$cmc_print_arr[$dateNextMonthName."-".$nd]['closed_status'] = 'active';
								}
								if($calendar_data != ""){
										$calendar .= $calendar_data;
										$cmc_print_arr[$dateNextMonthName."-".$nd]['status'] = 'open';										
								}else{
									$calendar .= "<div class=\"ml10\">No Appointments</div>";
									$cmc_print_arr[$dateNextMonthName."-".$nd]['status'] = 'no_appts';									
								}
				$calendar .= "</div></div></div><div class=\"fr mt10 mr3\">Total Appt. ".$total_appt."</div></div>"; #initial 'empty' days
				$cmc_print_arr[$dateNextMonthName."-".$nd]['total_appts'] = $total_appt;
			}
		}
		
		$calendar = "<style>".$style."</style><div class=\"fl mv_cl_d_b\">".$day_names."</div> <div class=\"fl\" style=\"width:100%;height:".($_SESSION["wn_height"] - 300)."px;overflow:auto;\">
		<div id=\"mnth_calendar_container\" class=\"fl\" style=\"width:100%;\">".$calendar."</div>
		</div>";
		
		// Calendar print data
		$arr_sess_prov_str = implode(',',$arr_sess_prov);
		$qry = "SELECT fname,lname,mname FROM users WHERE id IN($arr_sess_prov_str)";
		$prov_name_obj = imw_query($qry);
		$printMonthDiv = '<h3>Selected Providers</h3><table cellpadding="0" cellspacing="0">';
		while($prov_name_arr = imw_fetch_assoc($prov_name_obj))
		{
			 $printMonthDiv .= '<tr><td style="padding:5px;border:1px solid #333333;">'.core_name_format($prov_name_arr['lname'],$prov_name_arr['fname'],$prov_name_arr['mname']).'</td></tr>';
		}
		$printMonthDiv .= '</table><h3>Selected Facilities</h3><table cellpadding="0" cellspacing="0">';
		
		$arr_sess_facs_str = implode(',',$arr_sess_facs);
		$qry = "SELECT name FROM facility WHERE id IN($arr_sess_facs_str)";
		$facility_obj = imw_query($qry);
		while($fac_name = imw_fetch_assoc($facility_obj))
		{
			$printMonthDiv .= '<tr><td style="padding:5px;border:1px solid #333333;">'.$fac_name['name'].'</td></tr>';
		}
		$printMonthDiv .= '</table><br/><br/>';
		
		$limit_days_row = 7;$row_start = 0;		
		$printMonthDiv .= '<table style=\"width:100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>';
		foreach($cmc_print_arr as $printData)
		{
			if($row_start == $limit_days_row)
			{
				$printMonthDiv .= '</tr><tr>';	
				$row_start = 0;
			}
			$printMonthDiv .= '<td style="width:140px;min-height:100px;vertical-align:top;border:1px solid #000;">';
			$printMonthDiv .= '<table cellpadding="0" cellspacing="0" border="0"><tr><td style="width:128px;padding:3px;background-color:#666666;color:#ffffff;">'.$printData['day_name'].'</td></tr><tr><td style="width:128px;padding:3px;text-align:right;background-color:#666666;color:#ffffff;"> Total Appointments : '.$printData['total_appts'].' </td></tr>';
			if(isset($printData['closed_status']) && $printData['closed_status'] == 'active')
			{
				$printMonthDiv .= '<tr><td style="width:128px;padding:6px;height:50px;"> Office Closed </td></tr>';		
			}
			else
			{				
				if($printData['status'] == 'open')
				{
					foreach($printData['providers'] as $prov_data)
					{
						if($prov_data['start_time'] != "")
						{
							$prov_data['start_time'] = $prov_data['start_time'];	
						}
						if($prov_data['or_val'] != "")
						{
							$prov_data['or_val'] = " - ".$prov_data['or_val'];	
						}
						$printMonthDiv .= '<tr><td style="width:128px;padding:6px;">'.$prov_data['start_time']." ".$prov_data['pname'].' - '.$prov_data['no_of_appts'].$prov_data['or_val'].'</td></tr>';
						$print_time_key=$this->getNextkey(strtotime($prov_data['start_time']),$arr_print_month_div);
						$arr_print_month_div[$print_time_key]=$printMonthDiv1;
					}
					
					//foreach($arr_print_month_div as $__print_pdf_val){$printMonthDiv.=$__print_pdf_val;}
				 	$printMonthDiv.=implode('',$arr_print_month_div);
					
				}
				elseif($printData['status'] == 'no_appts')
				{
						$printMonthDiv .= '<tr><td style="width:128px;padding:6px;height:50px;"> No Appointments </td></tr>';
				}				
			}
			$printMonthDiv .= '</table></td>';
			$row_start++;
		}
		$printMonthDiv .= '</tr></table>';	
	$strHTML = "<page backtop=\"10mm\" backbottom=\"10mm\">
					<page_header>
						<table style=\"width:100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">

							<tr>
								<td width=\"900\" align=\"left\"><strong>Scheduler Monthly Schedule</strong></td>
								<td nowrap=\"nowrap\" style=\"text-align:right;\">Monthly Appointment Status</td>
							</tr>							
						</table>					
					</page_header>
					<page_footer>
						<table style=\"width:100%;\">								
							<tr>
								<td style=\"text-align:center;width:100%\">Page [[page_cu]]/[[page_nb]]</td>
							</tr>
						</table>
					</page_footer>";	
		$strHTML .= $printMonthDiv.'</page>';			
		//$html_file_name = "month_schedule_".$_SESSION['authId'];
		//file_put_contents('../reports/new_html2pdf/'.$html_file_name.'.html',$strHTML);			
		return $show_div.$calendar.'~~::~~'.$strHTML;
	}
	
	//function to validate label_replaced entries in schedule_custom_lbl table
	function validate_label_replaced($custom_lbl_arr)
	{
		$l_text_array = explode('; ',$custom_lbl_arr['l_text']);
		foreach($l_text_array as $lbl)
		{
			$new_arr[trim($lbl)][]=($lbl);	
		}
		$lbl_record_id = trim($custom_lbl_arr['id']);
		$lbl_replaced = trim($custom_lbl_arr['labels_replaced']);

		$lbl_replaced_array = array();
		$lbl_replaced_array = explode('::',$lbl_replaced);
		foreach($lbl_replaced_array as $lbl_replaced_entity)
		{
			$lbl_replaced_entity = trim($lbl_replaced_entity);
			if($lbl_replaced_entity!="")
			{
					list($lbl_replaced_appt_id,$get_lbl_replaced) = explode(':',$lbl_replaced_entity); 
					$get_lbl_replaced = trim($get_lbl_replaced);
					if($lbl_replaced_appt_id)
					{
						$q="select sa_app_start_date, sa_app_starttime, sa_app_endtime from schedule_appointments 
						where id='$lbl_replaced_appt_id' 
						AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
						AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 )
						AND '$custom_lbl_arr[start_date]' between sa_app_start_date and sa_app_end_date
						AND '$custom_lbl_arr[start_time]' between sa_app_starttime and sa_app_endtime";
						//validate this id aganist schedule appt table
						$qSch=imw_query($q);
						if(imw_num_rows($qSch)==0)
						{
							//label_replaced appt id is wrong
							$target_replace = '::'.$lbl_replaced_appt_id.':'.$get_lbl_replaced;
							$rsc_update_qry = 'UPDATE scheduler_custom_labels SET l_show_text = concat(TRIM(l_show_text),if(TRIM(l_show_text)="","'.$get_lbl_replaced.'","; '.$get_lbl_replaced.'")), labels_replaced = replace(labels_replaced,"'.$target_replace.'","") WHERE id ='.$lbl_record_id;
							imw_query($rsc_update_qry);	
						}
						
						unset($new_arr[$get_lbl_replaced][0]);
						$new_arr=array_values($new_arr);
					}							
												
			}
		}
		
		$new_sub_arry=array();
		foreach($new_arr as $sub_arr)
		{
			$new_sub_arry=array_merge($new_sub_arry,$sub_arr);
		}
		$l_text=implode('; ',$new_sub_arry);
		$rsc_update_qry = "UPDATE scheduler_custom_labels SET l_show_text = '$l_text' WHERE id =$lbl_record_id";
		mysql_query($rsc_update_qry);
	}
	
	
	//Used in Phy. day schedule
	/*
	Function: getReminderPtList
	Purpose: to get the physician notes
	Author: AA
	*/
	function getReminderPtList($u_id){
		$arrPat = array();
		$patRs = imw_query("Select patient_id from patient_notes WHERE provider_id = '".$u_id."'");
		if(imw_num_rows($patRs) > 0){
			while($this_pat = imw_fetch_array($patRs)){
				$arrPat[$this_pat['patient_id']] = $this_pat['patient_id'];	
			}
		}
		return $arrPat;
	}
	
	/*
	Function: getCheckedInPtList
	Purpose: to get the list of checked in patients
	Author: AA
	*/
	function getCheckedInPtList($int_fac, $dt_selected, $u_id){

		$arrP = $this->getReminderPtList($u_id);
		
		$strHTML = "";
		$sel_sch = "select 
					sa.id, sa.sa_app_start_date, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, TIME_FORMAT(sa.sa_app_endtime, '%h:%i %p') as sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,sa.procedure_site, 
					sa.EMR, sa.checked, sa.pt_priority, 
					sp.proc_color, sp.acronym, 
					st.status_name, st.status_icon, 
					sp2.times,sa.iolinkPatientId,sa.iolinkPatientWtId 
					from schedule_appointments sa 
					left join slot_procedures sp on sp.id = sa.procedureid 
					left join slot_procedures sp2 on sp2.id = sp.proc_time 
					left join schedule_status st on st.id = sa.sa_patient_app_status_id 
					where sa_facility_id = '".$int_fac."' and sa_doctor_id = '".$u_id."' and sa_test_id=0 and sa_patient_app_status_id IN (13) and '".$dt_selected."' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime";
		$res_sch = imw_query($sel_sch);
		$int_filled = 0;
		if(imw_num_rows($res_sch) > 0){
			while($row = imw_fetch_array($res_sch)){
				$arr_sch[] = $row;
			}
			if(is_array($arr_sch) && count($arr_sch) > 0){
				foreach($arr_sch as $this_sch){
					
					if($billing_global_server_name != 'miramar'){
					//skip DONE mark patients
					$checkPtIsDone=imw_query("select patient_location_id from patient_location where sch_id='".$this_sch["id"]."' and pt_with=6 ORDER BY patient_location_id desc limit 0,1")or die(imw_error().' 1');
					if(imw_num_rows($checkPtIsDone)!=0)continue;//skipp this record
					//skip Finalized Chart patients
					$checkPtIsFinalized=imw_query("select id from chart_master_table where patient_id='".$this_sch["sa_patient_id"]."' and date_of_service='".$this_sch["sa_app_start_date"]."' and finalize=1")or die(imw_error().' 2');
					if(imw_num_rows($checkPtIsFinalized)!=0)continue;//skip this record
					}
					
					$room_qry = "SELECT app_room, ready4DrId, pt_with, doctor_mess,app_room FROM patient_location WHERE patientId = '".$this_sch["sa_patient_id"]."' AND doctor_id = '".$this_sch["sa_doctor_id"]."' AND cur_date = '".$this_sch["sa_app_start_date"]."' ORDER BY patient_location_id DESC LIMIT 1";
					$room_res =imw_query($room_qry);
					$room_arr = array();
					if(imw_num_rows($room_res) > 0){
						while($row = imw_fetch_array($room_res)){
							$room_arr[] = $row;
						}
					}
					$flag='';
					if(in_array($this_sch["sa_patient_id"], $arrP)){
						$flag = "
						<div id=\"flag_".$this_sch["sa_patient_id"]."\" class='col-xs-12' onclick=\"javascript:showPatientNotes(document.getElementById('load_dt').value, '".$this_sch["sa_patient_id"]."')\"><span class='glyphicon glyphicon-flag text-success' title='Patient has added notes'></span></div>"; 
					}else{
						$flag = "
						<div id=\"flag_".$this_sch["sa_patient_id"]."\" class='col-xs-12' onclick=\"javascript:showPatientNotes(document.getElementById('load_dt').value, '".$this_sch["sa_patient_id"]."')\"><span class='glyphicon glyphicon-flag text-success' title='Patient has added notes'></span></div>"; 
					}
					
					if($room_arr[0]["ready4DrId"] > 0 || $room_arr[0]['pt_with'] == 1)
					{
						$flag .= "<div class='col-xs-12' title=\"".$room_arr[0]['doctor_mess']."\"><span class='glyphicon glyphicon-time'></span></div>";
					}
					
					$on_click_1 = "init_showPatientDiagnosisWindow('".$this_sch["sa_patient_id"]."');";
					$on_click_2 = "double_click=1;showWorkViewWindow('".$this_sch["sa_patient_id"]."');";
					$on_click_3 = "addPatientNotes('".$this_sch["sa_patient_id"]."','".$this_sch["sa_patient_name"]."','".$_SESSION["authId"]."','".$dt_selected."');";	// ADDED BY JASWANT
					$room_name = ($room_arr[0]["app_room"] != "") ? $room_arr[0]["app_room"] : "N/A";
					$priority_arr = array(0=>'Normal',1=>'Priority 1',2=>'Priority 2',3=>'Priority 3');
					$this_sch["pt_priority"] = $priority_arr[$this_sch["pt_priority"]];
					$strHTML .= "<tr id=\"checked_in_layers\" onclick=\"".$on_click."\" ondblclick=\"".$on_dbl_click."\">
						<td><div class='row'><div class='col-xs-2 text-center'>".$flag."</div><div class='col-xs-10'><span>".$this_sch["sa_app_starttime"]."-".$this_sch["sa_app_endtime"]."</span></div></div></td><td class='pointer' onDblClick=\"javascript:".$on_click_2."\" onclick=\"javascript:".$on_click_2."\"  oncontextmenu=\"javascript:".$on_click_3."; return false;\" title=\"Patient Chart Note\">".$this_sch["acronym"]." - ".$this_sch["sa_patient_name"]."</td><td class='pointer' onclick=\"javascript:".$on_click_1."\" title=\"Patient at a glance\">".$room_name."</td><td>".$this_sch["pt_priority"]."</td></tr>";
					$int_filled++;
				}
			}
		}
		if($int_filled < 8){
			$int_to_fill = 8 - $int_filled;
			for($f = 0; $f < $int_to_fill; $f++){
				$strHTML .= "<tr id=\"checked_in_layers\" style='height:35px'><td colspan='4'><div class=\"fl\" onclick=\"javascript:void(0);\" style=\"width:150px;\"></div><div class=\"fl\" onclick=\"javascript:void(0);\"></div></td></tr>";
			}
		}
		return $strHTML;
	}
	
	//function to get next key
	function getNextkey($key,$arr)
	{
		$keyNoFound=true;
		while($keyNoFound==true)
		{
			if($arr[$key])
			{
				$key++;	
			}
			else
			{
				$keyNoFound=false;	
			}	
		}	
		return $key;
	}
	
	/*
	Function: show_image_thumb
	Purpose: to show the patient photo
	Author: AA
	*/
	function show_image_thumb($fileName, $targetWidth = 116, $targetHeight = 116){
		$return = "";
		$path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$fileName;
		if(file_exists($path)){	
			$img_size = getimagesize($path);
			$width = $img_size[0];
			$height = $img_size[1];

			do{
				if($width > $targetWidth){
					$width = $targetWidth;
					$percent = $img_size[0] / $width;
					$height = $img_size[1] / $percent; 
				}
				if($height > $targetHeight){
					$height = $targetHeight;
					$percent = $img_size[1] / $height;
					$width = $img_size[0] / $percent; 
				}
			}while($width > $targetWidth || $height > $targetHeight);
			
			$imageWebPath = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$fileName;
			$return = "<img id=\"imgfrontDeskPatient\" src=\"".$imageWebPath."\" style=\"width:".$width."px;height:".$height."px\">";
		}
		return $return;
	}
	/*
	Function: schedule_status
	Purpose: get list of schedule status
	Author: Prabh
	*/
	function schedule_status()
	{
		$status_arr[0]='Restore';
		$query=imw_query("select id,status_name from schedule_status where status=1");
		while($data=imw_fetch_object($query))
		{
			$status_arr[$data->id]=$data->status_name;
		}
		asort($status_arr);
		return $status_arr;
	}
	/*
	Function: schedule_status
	Purpose: get list of schedule status
	Author: Prabh
	*/
	function eye_site($custom='')
	{
		if($custom=='Info')
		{
			$eye_site_options= array(""=>"", "Left"=>"(OS)", "Right"=>"(OD)", "Bilateral"=>"(OU)", "Left Upper Lid"=>"(LUL)", "Left Lower Lid"=>"(LLL)", 
			"Right Upper Lid"=>"(RUL)", "Right Lower Lid"=>"(RLL)", "Bilateral Upper Lid"=>"(BUL)", "Bilateral Lower Lid"=>"(BLL)");
		}
		else{
			$eye_site_options= array(""=>"Clear", "Left"=>"Left", "Right"=>"Right", "Bilateral"=>"Bilateral", "Left Upper Lid"=>"LUL", "Left Lower Lid"=>"LLL", "Right Upper Lid"=>"RUL", "Right Lower Lid"=>"RLL", "Bilateral Upper Lid"=>"BUL", "Bilateral Lower Lid"=>"BLL");
		}
		return $eye_site_options;
	}
	
	/*
	Function: get_simple_menu_sch
	Purpose: create context menu
	Author: Prabh
	*/
	function get_simple_menu_sch($arrMenu, $menuId, $elemTextId, $selected="0", $menu_dropdown_height=310,$position='', $multi="0")
	{
		if($selected=='Clear')$selected='0';
		$custom_id=$menuId;
		global $menuId;
		$str = "
		<label class='dropdown_toggle_trigger'>
			<button class='dropdown-toggle' type='button' id='".$custom_id."' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>";
		if(trim($selected))$str.= "<a href=\"javascript:void(0)\" class='status_icon '>$selected</a>";
		else $str.="<img src='../../library/images/eyeicon1.png' width='20' height='13' alt='Site' title='Site' class='pointer'/>";

		$str.="</button>";			   
		if((count($arrMenu) > 0) && (!isset($$menuId) || empty($$menuId))){
			$c = $menuId."-1";
			$str .= $this->get_menu_options_sch($arrMenu,$custom_id,$c,$elemTextId,$menu_dropdown_height,'',$selected, $position);
			$$menuId = "1";
		}
		

		$str .= "<input type=\"hidden\" name=\"elemTargetName\" id=\"elemTargetName\" value=\"".$elemTextId."\">";
		$str .= "<input type=\"hidden\" name=\"elemMenuMulti\" id=\"elemMenuMulti\" value=\"".$multi."\">";
		$str .= "</label>";
		return $str; 		
	}
	
	/*
	Function: get_menu_options_sch
	Purpose: create context menu
	Author: Prabh
	*/
	function get_menu_options_sch($arrMenu,$custom_id,$c,$elemTextId,$menu_dropdown_height,$menuStyle,$selected,$position='')
	{	
		$counter = 1;
		$strRet = $str_options = '';
		$over_flow="";
		if($menu_dropdown_height>0){
			$over_flow="overflow-y:scroll;";
		}
		foreach($arrMenu as $key => $val)
		{
			$optionLabel = $val[0];
			$optionSubMenu = $val[1];
			$optionValue = $val[2];
			
			if(is_array($optionSubMenu))
			{
				$menuStyle = '';
				$str_options .= "<li class='dropdown-submenu lead'>".
						   "<a class=\"\" href=\"javascript:void(0);\" onClick=\"set_val_text_sch('','".$custom_id."','".$elemTextId."','".$optionLabel."')\"><label>".$optionLabel."</label>$checked</a>
						   ".$this->get_menu_options_sch($optionSubMenu,$custom_id,$c,$elemTextId,$menu_dropdown_height,$menuStyle,$selected)."
						   ".
						   "</li>";	
			}						
			else
			{	
				if($optionValue==$selected && $selected!='Clear')$checked="<span class=\"glyphicon glyphicon-ok check-mark\"></span>";
				else $checked="";
				
				$menuStyle = 'max-height:'.$menu_dropdown_height.'px;'.$over_flow;
				$str_options .= "<li class='lead'>".
						   "<a class=\"\" href=\"javascript:void(0);\" onClick=\"set_val_text_sch(this,'".$custom_id."','".$elemTextId."','".$optionLabel."')\">".$optionLabel.$checked."
						   </a>".
						   "<input type=\"hidden\" name=\"menuOptionValue\" id=\"menuOptionValue\" value=\"".$optionValue."\">".
						   "</li>";	   
			}
			$counter++;	
		}
		$strRet = '<ul class="dropdown-menu '.$position.' menu_id_'.$counter.'" id="'.$menuId.'" style="'.$menuStyle.'">'.$str_options.'</ul>';
		return $strRet;
	}
		
	/*
	Function: is_first_available
	Purpose: give alert if first availabe appt desired time is available
	Author: Prabh
	*/
	function is_first_available()
	{
		$pt_id=$_SESSION['patient'];
		$highlight=$hover=$alert='';
		/* call function and store date array*/
		$dateArr=week_array();//fucntion from appt_page_function
		#CHECK IS PATIENT HAVE APPT IN FIRST AVAILBE FOR FUTURE
		list($year,$month)= explode('-',date('Y-m'));
		/*$query="SELECT fa.* FROM schedule_first_avail fa 
				LEFT JOIN schedule_appointments sa
				ON fa.sch_id=sa.id
			 	WHERE fa.del_status=0
			 	AND IF(fa.sch_id<>0, sa.sa_patient_id=$pt_id, fa.pat_id=$pt_id)
				AND fa.sel_year>='$year'
				AND fa.sel_month>='$month'";*/
		  $query="SELECT fa.id, fa.sch_id, fa.pat_id as sa_patient_id, fa.provider_id,fa.facility_id as sa_facility_id, 
			date_format(fa.date_of_act,'%m-%d-%y') as sa_app_start_date
			FROM schedule_first_avail fa 
			 WHERE fa.del_status=0 and fa.sch_id=0 
			 and fa.pat_id=$pt_id
			 UNION
			 
			 SELECT id, id sch_id,sa_patient_id, sa_doctor_id as provider_id,sa_facility_id,
			 date_format(sa_app_start_date,'%m-%d-%y') as sa_app_start_date
			FROM schedule_appointments sa 
			WHERE sa.sa_patient_app_status_id = 271  
			and sa_patient_app_status_id = 271
			and sa_patient_id=$pt_id";
		$is_fa=imw_query($query);
		if(imw_num_rows($is_fa)>0)
		{
			while($result_fa_row=imw_fetch_assoc($is_fa))
			{
				$key=$year=$month=$week='';
				$sch_id=$result_fa_row['sch_id'];
				$year=($sch_id)?desireTime($sch_id,'sch_id','other','sel_year'):desireTime($result_fa_row['id'],'id','other','sel_year');
				$month=($sch_id)?desireTime($sch_id,'sch_id','other','sel_month'):desireTime($result_fa_row['id'],'id','other','sel_month');
				$week=($sch_id)?desireTime($sch_id,'sch_id','other','sel_week'):desireTime($result_fa_row['id'],'id','other','sel_week');
				$month=($month<10)?'0'.$month:$month;

				$key=$year.'-'.$month.'-'.$week;
				if($dateArr[$key])
				{
					$arrDates='';
					$arrDates=$dateArr[$key];
					$_REQUEST['current_provider']=$result_fa_row['provider_id'];
					$_REQUEST['facility_sel']=$result_fa_row['sa_facility_id'];
					$_REQUEST['sch_timing']=($sch_id)?desireTime($sch_id,'sch_id','other','sel_time'):desireTime($result_fa_row['id'],'id','other','sel_time');
					$_REQUEST['sch_timing']=($_REQUEST['sch_timing']=='AM')?'morning':'afternoon';
					$_REQUEST['current_date']=date('Y-m-d');
					$_REQUEST['sel_label']='Slot without labels';
					require($GLOBALS['fileroot'].'/interface/scheduler/next_available_slots.php');
					if($hover)$alert.=$hover.'</br>';
					//reset values
					$highlight=$hover='';
				}
			}
		}
		return $alert;
	}
    
    
    function save_verification_data($appt_id) {
        
        $v_required=($_REQUEST['pt_verification']==1)?$_REQUEST['pt_verification']:0;
        
        $vr_sql='select * from verification where appt_id="'.$appt_id.'" ';
        $vr_rs=imw_query($vr_sql);
        
        if($vr_rs && imw_num_rows($vr_rs)==1) {
            $initial='UPDATE ';
            $qpart='Where appt_id="'.$appt_id.'" ';
        } else {
            $initial='INSERT INTO ';
            $qpart =',appt_id="'.$appt_id.'" ';
        }
        
        $insertQry=$initial. 'verification set v_required="'.$v_required.'" '.$qpart.' ';
        imw_query($insertQry);
        $insertID=imw_insert_id();
        
        return $insertID;
    }
    
    function get_verification_data($appt_id) {
        $row=array();
        $vr_sql='select id,appt_id,v_required from verification where appt_id="'.$appt_id.'" ';
        $vr_rs=imw_query($vr_sql);
        if($vr_rs && imw_num_rows($vr_rs)==1) {
            $row=imw_fetch_assoc($vr_rs);
            
        }
        return $row;
    }
    
    
    ###################################################################
	#   Getting provider's restricted status for particular patient
	###################################################################
	function sch_core_get_restricted_status($patient_id){	
		$askForReason = false;
		
		if(empty($patient_id)){
			return $askForReason;
		}
		
		if(isset($_SESSION["glassBreaked_ptId"])){
			if($_SESSION["glassBreaked_ptId"] == $patient_id){
				return $askForReason;
			}
		}
		
		$sql_getRestricted = "SELECT restrict_providers FROM restricted_providers where patient_id ='".imw_real_escape_string($patient_id)."' and restrict_providers != ''";
		$res_Restricted = imw_query($sql_getRestricted);				
		$num_rows = 0;	
		if($res_Restricted){
			$num_rows = imw_num_rows($res_Restricted);
			if($num_rows > 0){
				$resultRow = imw_fetch_array($res_Restricted);
				$explodeArray = explode(",", $resultRow["restrict_providers"]);
				if(is_array($explodeArray)){
					if(in_array($_SESSION["authId"], $explodeArray)){
						$askForReason = true;
					}
				}
			}
		}
		return $askForReason;
	}
    ###################################################################
	#   function to manage log for custom labels
	###################################################################    
	function custom_lbl_log($prov_id, $fac_id, $dated, $start_time, $end_time, $l_type, $l_text, $l_before, $l_after, $temp_id, $act, $act_id)
	{
		imw_query("insert into `scheduler_custom_labels_log` set `l_provider`='$prov_id',
				  `l_facility`='$fac_id',
				  `l_date`='$dated',
				  `l_start_time`='$start_time',
				  `l_end_time`='$end_time',
				  `l_type`='$l_type',
				  `l_text`='$l_text',
				  `l_text_before`='$l_before',
				  `l_text_after`='$l_after',
				  `temp_id`='$temp_id',
				  `act`='$act',
				  `act_id`='$act_id',
				  `time_stamp`='".date('Y-m-d H:i:s')."',
				  `operator_id`='$_SESSION[authId]'");
	}
	
	function patients_sync($appt_id){
		if( is_updox('telemedicine_appt') && $appt_id){
			header("Cache-control: private, no-cache"); 
			header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
			header("Pragma: no-cache");
			$qry = 	"SELECT sa.sa_doctor_id, sa.sa_patient_app_status_id, sa.sa_patient_id, sa.sa_app_start_date, sa.sa_app_starttime, sa.sa_app_endtime, sa.sa_facility_id, sa.sa_doctor_id, sa.updox_appt_id, 
					pd.pid, pd.fname, pd.mname, pd.lname, pd.sex, pd.street, pd.street2, pd.city, pd.state, pd.postal_code, pd.DOB, pd.email, pd.phone_home,  
					pd.phone_biz, pd.phone_cell, pd.patientStatus, pd.hipaa_email, pd.hipaa_voice, pd.hipaa_text, pd.pt_sync_updox,
					CONCAT(u.lname,', ',u.fname,' ',u.mname) AS provider_name, u.provider_color
					FROM schedule_appointments sa 
					INNER JOIN patient_data pd ON(pd.pid = sa.sa_patient_id)
					INNER JOIN slot_procedures sp ON(sp.id = sa.procedureid AND sp.proc_type IN('Telemedicine'))
					INNER JOIN users u ON(u.id = sa.sa_doctor_id)
					WHERE sa.id = '".$appt_id."'";
			$dt_frmt = date("Y-m-d");$up_dir = data_path();$updoxMsg2 = $qry;
			file_put_contents($up_dir.'PatientId_'.$_REQUEST["pt_id"].'/updox_log'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$updoxMsg2, FILE_APPEND);	
			file_put_contents($up_dir.'PatientId_'.$_REQUEST["pt_id"].'/updox_log'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);
			$res = imw_query($qry);	
			if(imw_num_rows($res)>0) {
				$row = imw_fetch_assoc($res);
				$pt_sync_updox 							= $row['pt_sync_updox'];
				
				$demographicData = $infoArr = $bulkDemographicArr = $apptData = array();
				$demographicInfo = array();
				$demographicInfo['internalId'] 				= $row['pid'];
				$demographicInfo['firstName'] 				= $row['fname'];
				$demographicInfo['middleName'] 				= $row['mname'];
				$demographicInfo['lastName'] 				= $row['lname'];
				$demographicInfo['sex'] 					= $row['sex'];
				$demographicInfo['address1'] 				= $row['street'];
				$demographicInfo['address2'] 				= $row['street2'];
				$demographicInfo['city'] 					= $row['city'];
				$demographicInfo['state'] 					= $row['state'];
				$demographicInfo['zip5'] 					= $row['postal_code'];
				
				$demographicInfo['dob'] 					= $row['DOB'];
				$demographicInfo['emailAddress']			= $row['email'];
				$demographicInfo['homePhone']				= $row['phone_home'];
				$demographicInfo['workPhone']				= $row['phone_biz'];
				$demographicInfo['mobileNumber']			= $row['phone_cell'];
				$activeStatus = false;
				$demographicInfo['active']					= ($row['patientStatus'] == 'Active') ? 'true' : 'false';
				
				$communicationMethod 						= '';
				if($row['hipaa_email'] == '1') {
					$communicationMethod 	= 	'EMAIL';
				}elseif($row['hipaa_text'] == '1') {
					$communicationMethod 	= 	'TEXT';
				}elseif($row['hipaa_voice'] == '1') {
					$communicationMethod 	= 	'VOICE';
				}
				if($communicationMethod) {
					//$demographicInfo['communicationConsent'][]	= array('consent'=>true,'method'=>$communicationMethod);
				}
				$demographicData[] 							= $demographicInfo;
				
				$providerData = array();
				$providerData['id'] 						= $row['sa_doctor_id'];
				$providerData['title'] 						= trim($row['provider_name']);
				$providerData['color'] 						= trim($row['provider_color']) ? $row['provider_color'] : '#FFFFFF';
				$providerData['textColor'] 					= '#000000';
				$providerData['publicCalendar'] 			= false;
				$providerData['active'] 					= true;
				$providerData['reminderTurnOff'] 			= false;
				
				
				$apptInfo = array();
				$apptInfo['id'] 							= $appt_id;
				$apptInfo['date'] 							= $row['sa_app_start_date'].' '.substr($row['sa_app_starttime'], 0, 5);
				$apptInfo['patientId'] 						= $row['sa_patient_id'];
				$apptInfo['cancelled'] 						= ($row['sa_patient_app_status_id'] == '18') ? true : false;
				$apptInfo['updoxId'] 						= $row['updox_appt_id'];
				$apptInfo['calendarId'] 					= $row['sa_doctor_id'];
				//$apptInfo['locationId'] 					= $row['sa_facility_id'];
				//$apptInfo['blocked']						= false;
	
				$apptData[] 								= $apptInfo;
	
				//=================UPDOX PATIENT SYNC WORKS STARTS HERE======================
				include($GLOBALS['srcdir'].'/updox/updoxFax.php');  //UPDOX LIBRAY FILE
				$updox = new updoxFax();  //UPDOX OBJECT
				$dateTm = date('Y-m-d H:i:s');
				$respProvider = $updox->providerSync($providerData);
				if($respProvider['status']=='success'){
					foreach($respProvider['data']->statuses as $key => $statusesInfo) {
						$infoArr[] 	= $statusesInfo->id;
						if($statusesInfo->id) {
							$qryDemo = "UPDATE users SET updox_sync_status = '1', updox_sync_date_time = '".$dateTm."' WHERE id='".$statusesInfo->id."' ";
							$resDemo = imw_query($qryDemo);	
						}
					}
				}
				$respDemographic = $updox->patientSync($demographicData);
				if($respDemographic['status']=='success'){
					foreach($respDemographic['data']->statuses as $key => $statusesInfo) {
						$infoArr[] 	= $statusesInfo->id;
						if($statusesInfo->id) {
							$qryDemo = "UPDATE patient_data SET pt_sync_updox = '1', pt_sync_updox_date_time = '".$dateTm."' WHERE pid='".$statusesInfo->id."' AND pid != '0' ";
							$resDemo = imw_query($qryDemo);	
						}
					}
				}
				$respAppt = $updox->apptSync($apptData);
				if($respAppt['status']=='success'){
					foreach($respAppt['data']->statuses as $key => $statusesInfo) {
						$infoArr[] 	= $statusesInfo->id;
						if($statusesInfo->id) {
							$updox_appt_id = $statusesInfo->updoxId;
							$qryDemo = "UPDATE schedule_appointments SET appt_sync_updox = '1', appt_sync_updox_date_time = '".$dateTm."', updox_appt_id = '".$updox_appt_id."' WHERE id='".$statusesInfo->id."' ";
							$resDemo = imw_query($qryDemo);	
						}
					}
				}
			}
		}
		//print	json_encode($bulkDemographicArr);
		/*
		$dt_frmt = date("Y-m-d");$up_dir = data_path();$updoxMsg2 = 'Save3';
		file_put_contents($up_dir.'PatientId_'.$_REQUEST["pt_id"].'/updox_log'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$updoxMsg2, FILE_APPEND);	
		file_put_contents($up_dir.'PatientId_'.$_REQUEST["pt_id"].'/updox_log'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);
		*/
	}
	
    
}
?>