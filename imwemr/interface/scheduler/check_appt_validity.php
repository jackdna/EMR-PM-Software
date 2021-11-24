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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//scheduler object
$obj_scheduler = new appt_scheduler();

$f_dt = $GLOBALS['query_string']['st_date'];								//start date
$f_st = $GLOBALS['query_string']['st_time'];								//start time
$pr_id = $GLOBALS['query_string']['pro_id'];								//provider id
$template_id = $GLOBALS['query_string']["template_id"];						//template id
$sl_pro = $GLOBALS['query_string']['sl_pro'];								//Procedure Id
$f_id = $GLOBALS['query_string']['fac_id'];									//facility id
$pat_id = $GLOBALS['query_string']['pat_id'];								//patient id

//reformatting date to YYYY-MM-DD from MM-DD-YYYY
$get_date_arr = explode("-", $f_dt);
$y = $get_date_arr[2];
$m = $get_date_arr[0];
$d = $get_date_arr[1];

//splitting time in hrs and minutes	
list($f_hr, $f_min) = explode(":", $f_st);
$f_hr = number_format($f_hr);
$f_min = number_format($f_min);

$ts_from = mktime($f_hr, $f_min, 0, $m, $d, $y);
$app_date2 = date("Y-m-d", $ts_from);
$app_time2 = date("H:i:s", $ts_from);
$times_from = date("H:i", $ts_from);

//defining info only and override message/warnings
$arr_info = array("BLOCED_TIME_WARN", "PHY_NOT_IN_OFFICE", "FUTURE_PT_APPT_WARN", "APPT_GAP_WARNING"); //this array is dummy to hold all those type of alerts which do not prompt for password. It has no further use in the code, but must be retianed to store the known typesin one place - amit.
$arr_prompt = array("BLOCED_TIME_WARN","MAX_APPT_PER_TMP", "DR_APPT_OVER_BOOK", "LRP_LABEL_RESTRICTION", "MAX_APPT_PER_PROC_PER_DAY", "PROC_TIMINGS_CHECK", "PAST_APPT_NOT_ALLOWED");

//return variables
$return_text = "";
$override_required = "n";
$discrepency_found = "n";

//SQL QUERIES
$all_proc_arr = array();
$all_proc_qry = "SELECT DISTINCT slot_procedures.acronym FROM slot_procedures WHERE slot_procedures.doctor_id = '0'";
$all_proc_res = imw_query($all_proc_qry);
if(imw_num_rows($all_proc_res) > 0){
	while($tmp_data=imw_fetch_assoc($all_proc_res))
	{
		$all_proc_tmp_arr[]=$tmp_data;
	}
	$n = 0;
	for($mp = 0; $mp < count($all_proc_tmp_arr); $mp++){
		if(trim($all_proc_tmp_arr[$mp]["acronym"]) != ""){
			$all_proc_arr[$n] = trim(strtolower($all_proc_tmp_arr[$mp]["acronym"]));
			$n++;
		}
	}
}

$proc_arr = array();
if(!empty($pr_id) && !empty($sl_pro)){	//Procedure Specific Data
	$arr_default_proc_id = $obj_scheduler->default_proc_to_doctor_proc($sl_pro, $pr_id);		//getting dr specific proc id if any
	list($default_proc_id, $default_proc_time, $default_proc_msg) = explode("~", $arr_default_proc_id);
	$proc_qry = "SELECT slot_procedures.max_allowed, intervals, slot_procedures.id, slot_procedures.proc, slot_procedures.acronym, slot_procedures_timings.after_start_time, slot_procedures_timings.after_end_time, slot_procedures_timings.doctor_id, slot_procedures.doctor_id proc_doctor_id FROM slot_procedures_timings RIGHT JOIN slot_procedures ON slot_procedures_timings.procedureId = slot_procedures.id WHERE slot_procedures.id = '".$default_proc_id."'";
	$proc_res = imw_query($proc_qry);
	if(imw_num_rows($proc_res) > 0){
		while($tmp_data=imw_fetch_assoc($proc_res))
		{
			$proc_arr[]=$tmp_data;
		}
		
		if($arr_appt_procedure[1] != ""){
			$time_to = date("H:i", mktime($time_hr, $tm_min+$arr_appt_procedure[1]));
		}else{
			$time_to = date("H:i", mktime($time_hr, $tm_min+10));		
		}
	}
	if(empty($default_proc_time)){
		$default_proc_time = 10; //default is 10 minutes if no proc timings are saved.
	}
	$ts_to = mktime($f_hr, $f_min + $default_proc_time, 0, $m, $d, $y);
	$app_end_time2 = date("H:i:s",$ts_to);
	$times_to = date("H:i", $ts_to);
}
$temp_arr = array();
$arr_temp_lbl = array();
$arr_temp_tbl[$times_from] = "";
if(!empty($template_id)){	//Template specific data
	$temp_qry = "SELECT start_time, end_time, template_label, morning_start_time, morning_end_time, MaxAppointments, warning_percentage, label_type FROM schedule_templates LEFT JOIN schedule_label_tbl ON schedule_templates.id = schedule_label_tbl.sch_template_id WHERE id = '".$template_id."'";
	$temp_res = imw_query($temp_qry);
	if(imw_num_rows($temp_res) > 0){
		while($tmp_data=imw_fetch_assoc($temp_res))
		{
			$temp_arr[]=$tmp_data;
		}
		foreach($temp_arr as $this_temp_arr){
			if($this_temp_arr["start_time"] != "" && $this_temp_arr["start_time"] == $times_from){
				$arr_temp_tbl[$this_temp_arr["start_time"]] = $this_temp_arr["template_label"]; //required later to check proc restrictions in slots
				$arr_temp_tbl_type[$this_temp_arr["start_time"]] = $this_temp_arr["label_type"]; // Made by Jaswant
			}
		}
	}
}
$doc_arr = array();
if(!empty($pr_id)){		//Doctor specific data
	$doc_qry = "SELECT max_appoint, StopOverBooking FROM users WHERE id = '".$pr_id."'";
	$doc_res = imw_query($doc_qry);
	if(imw_num_rows($doc_res) > 0){
		while($tmp_data=imw_fetch_assoc($doc_res))
		{
			$doc_arr[]=$tmp_data;
		}
	}
}
$btw_arr = array();
if(!empty($pr_id) && !empty($f_id) && !empty($f_st) && !empty($app_date2)){	//Block and Open Timings Data
	$btw_qry = "SELECT time_status, b_desc FROM block_times WHERE provider = '".$pr_id."' AND facility = '".$f_id."' AND '".$f_st."' >= start_time AND '".$f_st."' < end_time AND start_date = '".$app_date2."' ORDER BY id DESC";
	$btw_res = imw_query($btw_qry);
	if(imw_num_rows($btw_res) > 0){
		while($tmp_data=imw_fetch_assoc($btw_res))
		{
			$btw_arr[]=$tmp_data;
		}
	}
}
$sch_arr = array();
if(!empty($app_date2) && !empty($pr_id) && !empty($f_id)){	//Schedule Appointments data
	$sch_qry = "SELECT id, sa_app_start_date, sch_template_id, sa_patient_id, sa_app_starttime, sa_app_endtime, procedureId FROM schedule_appointments WHERE sa_app_start_date = '".$app_date2."' AND sa_doctor_id = '".$pr_id."' AND sa_facility_id = '".$f_id."' AND sa_patient_app_status_id NOT IN(203,201,18,19,20) ORDER BY id ASC";
	$sch_res = imw_query($sch_qry);
	if(imw_num_rows($sch_res) > 0){
		while($tmp_data=imw_fetch_assoc($sch_res))
		{
			$sch_arr[]=$tmp_data;
		}
	}
}

$sch_custom_data_arr = array();
$reserved_custom_exists = 0;
if(!empty($pr_id) && !empty($f_id) && !empty($f_st) && !empty($app_date2)){	//Block and Open Timings Data
	$sch_custom_qry = "SELECT l_type FROM scheduler_custom_labels WHERE provider = '".$pr_id."' AND facility = '".$f_id."' AND start_time = '".$f_st."' AND start_date = '".$app_date2."' AND TRIM(l_show_text) != '' ORDER BY id DESC";
	$sch_custom_res = imw_query($sch_custom_qry);
	if(imw_num_rows($sch_custom_res) > 0){
		while($tmp_data=imw_fetch_assoc($sch_custom_res))
		{
			$sch_custom_data_arr[]=$tmp_data;
		}
		foreach($sch_custom_data_arr as $sch_custom_lbl)
		{
			$sch_custom_lbl = trim($sch_custom_lbl['l_type']);
			if($sch_custom_lbl != "" && strtolower($sch_custom_lbl) == 'reserved')
			{
				$reserved_custom_exists = 1;	
			}
			else
			{
				$reserved_custom_exists = 2;	
			}
		}
	}
}
 
//MAX_APPT_PER_TMP - maximum appts in a template for the day in this facility - CHECKED
if(!empty($template_id)){
	if(!empty($temp_arr[0]["MaxAppointments"])){
		$int_tmp_cnt = 0;
		if(count($sch_arr) > 0){
			foreach($sch_arr as $this_sch_arr){
				if($this_sch_arr["sch_template_id"] == $template_id){
					$int_tmp_cnt++;
				}
			}
		}
		
		if($temp_arr[0]["warning_percentage"] && round((($int_tmp_cnt+1)/ $temp_arr[0]["MaxAppointments"])*100)>= $temp_arr[0]["warning_percentage"]){
			$discrepency_found = "y";			
			$return_text .= " - Maximum Appointment (%) Limit for this template will exceed.<br>";
		}elseif($int_tmp_cnt > $temp_arr[0]["MaxAppointments"]){			
			$discrepency_found = "y";
			if(in_array("MAX_APPT_PER_TMP",$arr_prompt)){
				$override_required = "y";
				$return_text .= " - <b>Maximum Appointment Limit for this template exceeded.</b><br>";
			}else{
				$return_text .= " - Maximum Appointment Limit for this template exceeded.<br>";
			}
		}	
	}
}

//DR_APPT_OVER_BOOK - maxiumum appts in a slot for this doctor in this facility - CHECKED
if(!empty($pr_id)){
	$pro_limit=false;
	$bar_msg = "Maximum Appointment Overbooking Limit exceeded.";
	
	if(constant('ENABLE_SCHEDULER_RAIL_CHECK')==1)
	{
		if($_REQUEST['procedure_limit']!='-1')
		{
			$int_bar_limit = $_REQUEST['procedure_limit'];
			$pro_limit=true;
		}
		else
		{$int_bar_limit = $doc_arr[0]["max_appoint"];}
	}
	else
	{$int_bar_limit = $doc_arr[0]["max_appoint"];}
	
	if($doc_arr[0]["StopOverBooking"] == "Yes"){
		$bar_msg = "Only One Appointment per slot is allowed.";
		$int_bar_limit = 1;
	}
	if(!empty($int_bar_limit) || $pro_limit){
		$int_overbook_cnt = 0;
		if(count($sch_arr) > 0){
			foreach($sch_arr as $this_sch_arr){
				$arr_st = explode(":", $this_sch_arr["sa_app_starttime"]);
				$ts_st = mktime($arr_st[0], $arr_st[1], 0, $m, $d, $y);
				
				$arr_ed = explode(":", $this_sch_arr["sa_app_endtime"]);
				$ts_ed = mktime($arr_ed[0], $arr_ed[1], 0, $m, $d, $y);

				if($ts_from >= $ts_st && $ts_from < $ts_ed){
					$int_overbook_cnt++;
				}
			}
		}
		
		if($int_overbook_cnt >= $int_bar_limit){			
			$discrepency_found = "y";
			if(in_array("DR_APPT_OVER_BOOK",$arr_prompt)){
				$override_required = "y";
				$return_text .= " - <b>".$bar_msg."</b><br>";
			}else{
				$return_text .= " - ".$bar_msg."<br>";
			}
		}
	}
}

if(!empty($template_id) && !empty($sl_pro)){
	if(!empty($pr_id) && !empty($f_id) && !empty($f_st) && !empty($app_date2)){
		$sch_custom_qry_remove = "SELECT start_time FROM scheduler_custom_labels WHERE provider = '".$pr_id."' AND facility = '".$f_id."' AND start_time = '".$f_st."' AND start_date = '".$app_date2."' AND TRIM(l_show_text) = '' ORDER BY id DESC";
		$res_sch_custom_qry_remove=imw_query($sch_custom_qry_remove);
		if(imw_num_rows($res_sch_custom_qry_remove)){
			$row_sch_custom_qry_remove=imw_fetch_assoc($res_sch_custom_qry_remove);
			$HH=$MM=$SS="";
			list($HH,$MM,$SS)=explode(":",$row_sch_custom_qry_remove["start_time"]);
			if($replace_label_time){$arr_temp_tbl_type[$replace_label_time]="";}
		}
	}
	if((trim(strtolower($arr_temp_tbl_type[$times_from])) == "reserved" || $reserved_custom_exists == 1) && $reserved_custom_exists!=2){
		$return_text .= " <b> - Reserved time. </b> <br>";
		$discrepency_found = "y";
		if(in_array("LRP_LABEL_RESTRICTION",$arr_prompt)){
			$override_required = "y";
		}
	}
}

//MAX_APPT_PER_PROC_PER_DAY - max appts for a procedure in a day - CHECKED
if(!empty($pr_id) && !empty($sl_pro)){
	if(!empty($proc_arr[0]["max_allowed"])){
		$int_proc_cnt = 0;
		if(count($sch_arr) > 0){
			foreach($sch_arr as $this_sch_arr){
				if($this_sch_arr["procedureId"] ==  $sl_pro){
					$int_proc_cnt++;
				}
			}
		}		
		if($int_proc_cnt > $proc_arr[0]["max_allowed"]){			
			$discrepency_found = "y";
			if(in_array("MAX_APPT_PER_PROC_PER_DAY",$arr_prompt)){
				$override_required = "y";
				$return_text .= " - <b>Maximum Allowed appointments Limit for \"".$proc_arr[0]["proc"]."\" exceeded.</b><br>";
			}else{
				$return_text .= " - Maximum Allowed appointments Limit for \"".$proc_arr[0]["proc"]."\" exceeded.<br>";
			}
		}
	}
}

//PROC_TIMINGS_CHECK - the appt is in or out the recommended timings for this procedure - CHECKED
if(!empty($pr_id) && !empty($sl_pro)){
	if(count($proc_arr) > 0){
		$bl_discrepency_found0 = 0;
		$bl_discrepency_found1 = 0;
		$bl_discrepency_found2 = 0;
		$bl_discrepency_found3 = 0;

		$bl_timings_added = false;
		for($i = 0; $i < 4; $i++){
			if($proc_arr[$i]["after_start_time"] != "" && $proc_arr[$i]["after_end_time"] != ""){
				$bl_timings_added = true;
				$arr_st = explode(":", $proc_arr[$i]["after_start_time"]);
				$ts_st = mktime($arr_st[0], $arr_st[1], 0, $m, $d, $y);
				
				$arr_ed = explode(":", $proc_arr[$i]["after_end_time"]);
				$ts_ed = mktime($arr_ed[0], $arr_ed[1], 0, $m, $d, $y);
				
				if($ts_from >= $ts_st && $ts_to <= $ts_ed){
					$bl_name = "bl_discrepency_found".$i;
					$$bl_name = 1;
				}
			}
		}
		//$return_text .= ($bl_discrepency_found0 + $bl_discrepency_found1 + $bl_discrepency_found2 + $bl_discrepency_found3);
		if($bl_timings_added == true && ($bl_discrepency_found0 + $bl_discrepency_found1 + $bl_discrepency_found2 + $bl_discrepency_found3) == 0){			
			$discrepency_found = "y";
			if(in_array("PROC_TIMINGS_CHECK",$arr_prompt)){
				$override_required = "y";
				$return_text .= " - <b>Appointment outside allowed timings for this procedure.</b><br>";
			}else{
				$return_text .= " - Appointment outside allowed timings for this procedure.<br>";
			}
		}
	}
}

//PHY_NOT_IN_OFFICE - CHECKED
if(!empty($pr_id)){
	$opened_time = false;
	if(count($btw_arr) > 0){
		foreach($btw_arr as $this_btw_arr){
			if($this_btw_arr["time_status"] == "open"){
				$opened_time = true;
			}
		}
	}
	if($opened_time == false){
		list($st_hr, $st_mi, $st_se) = explode(":", $temp_arr[0]["morning_start_time"]);
		$ts_st = mktime($st_hr, $st_mi, 0, $m, $d, $y);
		
		list($ed_hr, $ed_mi, $ed_se) = explode(":", $temp_arr[0]["morning_end_time"]);
		$ts_ed = mktime($ed_hr, $ed_mi, 0, $m, $d, $y);
		//$return_text .= "Appt From ".$ts_from." to ".$ts_to.", Template From ".$ts_st." To ".$ts_ed;
		if($ts_from < $ts_st || $ts_from >= $ts_ed || $ts_to > $ts_ed){			
			$discrepency_found = "y";
			if(in_array("PHY_NOT_IN_OFFICE",$arr_prompt)){
				$override_required = "y";
				$return_text .= " - <b>Physician not in office.</b><br>";
			}else{
				$return_text .= " - Physician not in office.<br>";
			}
		}
	}
}

//BLOCED_TIME_WARN - CHECKED
if(!empty($f_st)){	
	if(count($btw_arr) > 0){
		foreach($btw_arr as $this_btw_arr){
			if(trim($this_btw_arr["time_status"]) == "block"){				
				$discrepency_found = "y";
				if(in_array("BLOCED_TIME_WARN",$arr_prompt)){
					$override_required = "y";
					if(trim($this_btw_arr["b_desc"]) != ""){
						$return_text .= " - <b>Blocked time \"".$this_btw_arr["b_desc"]."\".</b><br>";
					}else{
						$return_text .= " - <b>Blocked time.</b><br>";
					}
				}else{
					if(trim($this_btw_arr["b_desc"]) != ""){
						$return_text .= " - Blocked time \"".$this_btw_arr["b_desc"]."\".<br>";
					}else{
						$return_text .= " - Blocked time.<br>";
					}
				}
				break;
			}
			else if(trim($this_btw_arr["time_status"]) == "lock")
			{
				$discrepency_found = "y";
				if(in_array("BLOCED_TIME_WARN",$arr_prompt)){
					$override_required = "y";
					if(trim($this_btw_arr["b_desc"]) != ""){
						$return_text .= " - <b>Locked time \"".$this_btw_arr["b_desc"]."\".</b><br>";
					}else{
						$return_text .= " - <b>Locked time.</b><br>";
					}
				}else{
					if(trim($this_btw_arr["b_desc"]) != ""){
						$return_text .= " - Locked time \"".$this_btw_arr["b_desc"]."\".<br>";
					}else{
						$return_text .= " - Locked time.<br>";
					}
				}
				break;
			}
		}
	}
}

//PAST_APPT_NOT_ALLOWED - CHECKED
if($ts_from < time()){	
	$discrepency_found = "y";
	if(in_array("PAST_APPT_NOT_ALLOWED",$arr_prompt)){
		$override_required = "y";
		$return_text .= " - <b>Past appointments are not allowed.</b><br>";
	}else{
		$return_text .= " - Past appointments are not allowed.<br>";
	}
}

//FUTURE_PT_APPT_WARN - CHECKED
if($GLOBALS['query_string']['querytype'] == "addnew" && !empty($pat_id)){
	$bl_future_appt = false;
	$this_appt_st_dt = "";
	$this_appt_st_tm = "";
	if(count($sch_arr) > 0){
		foreach($sch_arr as $this_sch_arr){
			if($pat_id == $this_sch_arr["sa_patient_id"]){
				$arr_st = explode(":", $this_sch_arr["sa_app_starttime"]);
				$ts_st = mktime($arr_st[0], $arr_st[1], 0, $m, $d, $y);
				if($ts_from < $ts_st){
					$this_appt_st_dt = $this_sch_arr["sa_app_start_date"];
					$this_appt_st_tm = $this_sch_arr["sa_app_starttime"];
					$bl_future_appt = true;
					break;
				}
			}
		}
	}
	if($bl_future_appt == true){
		list($tmp_yr, $tmp_mn, $tmp_dt) = explode("-", $this_appt_st_dt);
		list($tmp_hr, $tmp_mi, $tmp_se) = explode(":", $this_appt_st_tm);
		$str_appt_time = date("h:i A", mktime($tmp_hr, $tmp_mi, $tmp_se, $tmp_mn, $tmp_dt, $tmp_yr));
		$discrepency_found = "y";
		if(in_array("FUTURE_PT_APPT_WARN",$arr_prompt)){
			$override_required = "y";
			$return_text .= " - <b>Patient also has an appointment on ".$tmp_mn."-".$tmp_dt."-".$tmp_yr." at ".$str_appt_time."</b>.<br>";
		}else{
			$return_text .= " - Patient also has an appointment on ".$tmp_mn."-".$tmp_dt."-".$tmp_yr." at ".$str_appt_time.".<br>";
		}
	}
}

//no override password required for users with these priveleges
if(core_check_privilege(array("priv_Sch_Override")) == true){
	$override_required = "n";
}
if($discrepency_found == "y"){
	$return_text .= "<br><br>Do you still wish to continue?";
}

echo $discrepency_found."~~~".$override_required."~~~".$return_text;
?>