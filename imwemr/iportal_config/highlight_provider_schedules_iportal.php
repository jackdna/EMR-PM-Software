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
?><?php
/*
File: highlight_provider_schedules_iportal.php
Purpose: Get Provider's Month Schedule (Avaiable Days Only) for iPortal Appointment Booking
Access Type: Direct
*/
$ignoreAuth=true;
include_once("../config/globals.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	die("[Error]:401 Unauthorized Access ");
}
include_once($GLOBALS['webroot']."/library/classes/scheduler/appt_schedule_functions.php");

//db object
$oDB = $GLOBALS["adodb"]["db"];

//scheduler object
$obj_scheduler = new appt_scheduler();

//get provider array
/*$_REQUEST["prov_id"] = "97";
$_REQUEST["working_day_dt"] = "2015-12-19";
$_REQUEST["loca"] = "1";
$_REQUEST["loaded_month"] = "2015-12-01";*/

$prov_id_arr=explode(',',$_REQUEST["prov_id"]);
$working_day_dt = $_REQUEST["working_day_dt"];
$fac_ids = explode(",", $_REQUEST["loca"]);
//getting next 2 months from the current month
$loaded_month = $_REQUEST["loaded_month"];

list($lm_y, $lm_m, $lm_d) = explode("-", $loaded_month);
//echo "$lm_y, $lm_m, $lm_d";
if($lm_m == 11){
	$nx_m = date("m", mktime(0, 0, 0, (int)$lm_m + 1, 1, (int)$lm_y));
	$nx2_m = date("m", mktime(0, 0, 0, (int)$lm_m + 2, 1, (int)$lm_y + 1));
	$nx_y = (int)$lm_y;
	$nx2_y = (int)$lm_y + 1;
}else if($lm_m == 12){
	$nx_m = date("m", mktime(0, 0, 0, (int)$lm_m + 1, 1, (int)$lm_y + 1));
	$nx2_m = date("m", mktime(0, 0, 0, (int)$lm_m + 2, 1, (int)$lm_y + 1));
	$nx_y = (int)$lm_y + 1;
	$nx2_y = (int)$lm_y + 1;
}else{
	$nx_m = date("m", mktime(0, 0, 0, (int)$lm_m + 1, 1, (int)$lm_y));
	$nx2_m = date("m", mktime(0, 0, 0, (int)$lm_m + 2, 1, (int)$lm_y));
	$nx_y = (int)$lm_y;
	$nx2_y = (int)$lm_y;
}

//echo "$nx_y, $nx_m, $lm_d";
//echo "$nx2_y, $nx2_m, $lm_d";
$str_response1 = "";
$providerAvailableDAtes = array();

//this month
$stts = mktime(0, 0, 0, (int)$lm_m, (int)$lm_d, (int)$lm_y);
$edtsdt = date("t", $stts);
$edts = mktime(23, 59, 59, (int)$lm_m, $edtsdt, (int)$lm_y);

//next month
$stnxts = mktime(0, 0, 0, (int)$nx_m, (int)$lm_d, (int)$nx_y);
$ednxtsdt = date("t", $stnxts);
$ednxts = mktime(23, 59, 59, (int)$nx_m, $ednxtsdt, (int)$nx_y);

//next 2 next month
$stnx2ts = mktime(0, 0, 0, (int)$nx2_m, (int)$lm_d, (int)$nx2_y);
$ednx2tsdt = date("t", $stnx2ts);
$ednx2ts = mktime(23, 59, 59, (int)$nx2_m, $ednx2tsdt, (int)$nx2_y);
//check is that single provider or more
if(sizeof($prov_id_arr)==1)
{
	$prov_id = (int) $_REQUEST["prov_id"];
	#### check for physician schedule ####
	//getting allowed max count
	$int_max_cnt = 0;
	$int_warn_percentage = 100;
	$qry_u = "SELECT max_per, max_appoint FROM users WHERE id =".$prov_id;
	$res_u = $oDB->Execute($qry_u);
	if($res_u->_numOfRows > 0){
		$arr_u = $res_u->GetArray();
		$int_max_cnt = (!empty($arr_u[0]["max_appoint"])) ? $arr_u[0]["max_appoint"] : 0;
		$int_warn_percentage = (!empty($arr_u[0]["max_per"])) ? $arr_u[0]["max_per"] : 100;
	}
	
	//getting appt count for these months
	$arr_day_appt = array();
	$arr_fac_appt = array();
	$arr_day_am_appt = array();
	$arr_day_pm_appt = array();
	$qry_sch = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime < '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	$res_sch = $oDB->Execute($qry_sch);
	if($res_sch->_numOfRows > 0){
		$arr_sch = $res_sch->GetArray();	
		for($a = 0; $a < count($arr_sch); $a++){
			$arr_day_am_appt[$arr_sch[$a]["sa_app_start_date"]] += $arr_sch[$a]["appt_cnt"];
			$arr_day_appt[$arr_sch[$a]["sa_app_start_date"]] += $arr_sch[$a]["appt_cnt"];
			$arr_fac_appt[$arr_sch[$a]["sa_app_start_date"]][$arr_sch[$a]["sa_facility_id"]] += $arr_sch[$a]["appt_cnt"];
		}
	}
	
	$arr_sch = array();
	$qry_sch = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime >= '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	$res_sch = $oDB->Execute($qry_sch);
	if($res_sch->_numOfRows > 0){
		$arr_sch = $res_sch->GetArray();	
		for($a = 0; $a < count($arr_sch); $a++){
			$arr_day_pm_appt[$arr_sch[$a]["sa_app_start_date"]] += $arr_sch[$a]["appt_cnt"];
			$arr_day_appt[$arr_sch[$a]["sa_app_start_date"]] += $arr_sch[$a]["appt_cnt"];
			$arr_fac_appt[$arr_sch[$a]["sa_app_start_date"]][$arr_sch[$a]["sa_facility_id"]] += $arr_sch[$a]["appt_cnt"];
		}
	}
	
	//this month
	for($stval = $stts; $stval <= $edts; $stval = $stval + 86400){
		$working_this_dt = date("Y-m-d", $stval);
		$str_response = "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		list($str_div_content, $int_total_secs, $tmp_max_appointments_arr_h) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id, $fac_ids);
		$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
		//$str_response .= $str_div_content;
		if(trim($str_div_content) != "")
		{
			$am_appts = 0; $pm_appts = 0;
			if(isset($arr_day_am_appt[$working_this_dt])){ $am_appts = $arr_day_am_appt[$working_this_dt];}
			if(isset($arr_day_pm_appt[$working_this_dt])){ $pm_appts = $arr_day_pm_appt[$working_this_dt];}
			//$str_response .= 'Appointments: <b>'.$am_appts.'/'.$pm_appts.'</b>';
			$str_response .= true;
			$date = (int)date("Y", $stval)."_".date("m", $stval)."_".(int)date("d", $stval);
			$providerAvailableDAtes[$date] = true;
		}
		else{
			continue;
		}
		$str_response .= "~~~";
		if(isset($arr_day_appt[$working_this_dt])){		
			$default_flag = 0;
			if(is_array($tmp_max_appointments_arr_h) && $int_warn_percentage > 0)
			{
				$tmp_max_appointments_arr = $tmp_max_appointments_arr_h;								
				foreach($tmp_max_appointments_arr as $fac_id_key => $pr_fac_max_appts)
				{	
					if($pr_fac_max_appts>0)
					{
						if(round(($arr_fac_appt[$working_this_dt][$fac_id_key]/$pr_fac_max_appts)*100) >= $int_warn_percentage)
						{
							$str_response .= "exceed_appt";
							$default_flag = 1;	
							break;
						}
					}
				}
			}		
			if($default_flag == 0)
			{
				if($int_max_cnt > 0){
					$int_set_cnt = $arr_day_appt[$working_this_dt];
					//$str_response .= round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100)." >= ".$int_warn_percentage;
					if(round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100) >= $int_warn_percentage){
						$str_response .= "alert";
					}else{
						$str_response .= "default";
					}
				}else{
					$str_response .= "default";	
				}
			}			
		}else{
			$str_response .= "default";	
		}
		$str_response .= ":~:~:";
		$str_response1 .= $str_response;
	}
	
	//next month
	for($stval = $stnxts; $stval <= $ednxts; $stval = $stval + 86400){
		$working_this_dt = date("Y-m-d", $stval);
		$str_response = "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		list($str_div_content, $int_total_secs, $tmp_max_appointments_arr_h) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id, $fac_ids);
		$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
		//$str_response .= $str_div_content;
		if(trim($str_div_content) != "")
		{
			$am_appts = 0; $pm_appts = 0;
			if(isset($arr_day_am_appt[$working_this_dt])){ $am_appts = $arr_day_am_appt[$working_this_dt];}
			if(isset($arr_day_pm_appt[$working_this_dt])){ $pm_appts = $arr_day_pm_appt[$working_this_dt];}
			//$str_response .= 'Appointments: <b>'.$am_appts.'/'.$pm_appts.'</b>';
			$str_response .= true;
			$date = (int)date("Y", $stval)."_".date("m", $stval)."_".(int)date("d", $stval);
			$providerAvailableDAtes[$date] = true;
		}
		else{
			continue;
		}
		$str_response .= "~~~";
		if(isset($arr_day_appt[$working_this_dt])){
			$default_flag = 0;
			if(is_array($tmp_max_appointments_arr_h) && $int_warn_percentage > 0)
			{
				$tmp_max_appointments_arr = $tmp_max_appointments_arr_h;								
				foreach($tmp_max_appointments_arr as $fac_id_key => $pr_fac_max_appts)
				{	
					if($pr_fac_max_appts>0)
					{
						if(round(($arr_fac_appt[$working_this_dt][$fac_id_key]/$pr_fac_max_appts)*100) >= $int_warn_percentage)
						{
							$str_response .= "exceed_appt";
							$default_flag = 1;	
							break;
						}
					}
				}
			}
			if($default_flag == 0)
			{		
				if($int_max_cnt > 0){
					$int_set_cnt = $arr_day_appt[$working_this_dt];
					//$str_response .= round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100)." >= ".$int_warn_percentage;
					if(round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100) >= $int_warn_percentage){
						$str_response .= "alert";
					}else{
						$str_response .= "default";	
					}
				}else{
					$str_response .= "default";	
				}
			}
		}else{
			$str_response .= "default";	
		}
		$str_response .= ":~:~:";
		$str_response1 .= $str_response;
	}
	
	//next 2 next month
	for($stval = $stnx2ts; $stval <= $ednx2ts; $stval = $stval + 86400){
		$working_this_dt = date("Y-m-d", $stval);
		$str_response = "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		list($str_div_content, $int_total_secs, $tmp_max_appointments_arr_h) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id, $fac_ids);
		$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
		//$str_response .= $str_div_content;
		if(trim($str_div_content) != "")
		{
			$am_appts = 0; $pm_appts = 0;
			if(isset($arr_day_am_appt[$working_this_dt])){ $am_appts = $arr_day_am_appt[$working_this_dt];}
			if(isset($arr_day_pm_appt[$working_this_dt])){ $pm_appts = $arr_day_pm_appt[$working_this_dt];}
			//$str_response .= 'Appointments: <b>'.$am_appts.'/'.$pm_appts.'</b>';
			$str_response .= true;
			$date = (int)date("Y", $stval)."_".date("m", $stval)."_".(int)date("d", $stval);
			$providerAvailableDAtes[$date] = true;
		}
		else{
			continue;
		}
		$str_response .= "~~~";
		if(isset($arr_day_appt[$working_this_dt])){
			$default_flag = 0;
			if(is_array($tmp_max_appointments_arr_h) && $int_warn_percentage > 0)
			{
				$tmp_max_appointments_arr = $tmp_max_appointments_arr_h;								
				foreach($tmp_max_appointments_arr as $fac_id_key => $pr_fac_max_appts)
				{	
					if($pr_fac_max_appts>0)
					{
						if(round(($arr_fac_appt[$working_this_dt][$fac_id_key]/$pr_fac_max_appts)*100) >= $int_warn_percentage)
						{
							$str_response .= "exceed_appt";
							$default_flag = 1;	
							break;
						}
					}
				}
			}
			if($default_flag == 0)
			{		
				if($int_max_cnt > 0){
					$int_set_cnt = $arr_day_appt[$working_this_dt];
					//$str_response .= round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100)." >= ".$int_warn_percentage;
					if(round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100) >= $int_warn_percentage){
						$str_response .= "alert";
					}else{
						$str_response .= "default";	
					}
				}else{
					$str_response .= "default";	
				}
			}
		}else{
			$str_response .= "default";	
		}
		$str_response .= ":~:~:";
		$str_response1 .= $str_response;
	}
}

/*$procedures = $obj_scheduler->load_procedures();
$procedures = $procedures[1];*/
print json_encode($providerAvailableDAtes);
?>