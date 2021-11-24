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
File: highlight_provider_schedules.php
Purpose: Highlight provider schedules
Access Type: Direct
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');


//scheduler object
$obj_scheduler = new appt_scheduler();

//get provider array
$prov_id_arr=explode(',',$_REQUEST["prov_id"]);
$working_day_dt = $_REQUEST["working_day_dt"];
$fac_ids = explode(",", $_REQUEST["loca"]);
$before_lunchTm = "12:00:00";
/*
//get lunch time for all templates
$sch_temp_qry = 'SELECT fldLunchStTm,id FROM schedule_templates';
$sch_temp_qry_obj = imw_query($sch_temp_qry);

while($sch_temp_qry_data = imw_fetch_assoc($sch_temp_qry_obj))
{
	$sch_temp_qry_data['fldLunchStTm'] = trim($sch_temp_qry_data['fldLunchStTm']);
	if(isset($sch_temp_qry_data['fldLunchStTm']))
	{	$sch_temp_qry_data['fldLunchStTm']=(trim($sch_temp_qry_data['fldLunchStTm'])=='00:00:00')?$before_lunchTm:trim($sch_temp_qry_data['fldLunchStTm']);
		$before_lunchTmArr[$sch_temp_qry_data['fldLunchStTm']] = $sch_temp_qry_data['fldLunchStTm'];
		$sch_templ_arr[$sch_temp_qry_data['fldLunchStTm']][]=$sch_temp_qry_data['id'];
	}								
}
*/
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
$str_response = "";

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
if(sizeof($prov_id_arr)>1 && sizeof($fac_ids)==1)
{
	$prov_name_arr1=$obj_scheduler->get_provider_details($prov_id_arr[0], "CONCAT(lname,' ',fname) as name");
	$prov_name_arr2=$obj_scheduler->get_provider_details($prov_id_arr[1], "CONCAT(lname,' ',fname) as name");
	$prov_name1='<strong>'.$prov_name_arr1[0]['name'].'</strong>';
	$prov_name2='<strong>'.$prov_name_arr2[0]['name'].'</strong>';
	
	$int_max_cnt1 = 0;
	$int_warn_percentage1 = 100;
	$int_max_cnt2 = 0;
	$int_warn_percentage2 = 100;
	$pro_id_str=implode(',',$prov_id_arr);
	if($pro_id_str)
	{
		$qry_u1 = "SELECT id, max_per, max_appoint FROM users WHERE id IN($pro_id_str)";
		$res_u1 = imw_query($qry_u1);
		if(imw_num_rows($res_u1) > 0){
			$arr_u1 = imw_fetch_assoc($res_u1);
			if($arr_u1['id']==$prov_id_arr[0])
			{
				$int_max_cnt1 = (!empty($arr_u1["max_appoint"])) ? $arr_u1["max_appoint"] : 0;
				$int_warn_percentage1 = (!empty($arr_u1["max_per"])) ? $arr_u1["max_per"] : 100;
			}
			elseif($arr_u1['id']==$prov_id_arr[1])
			{
				$int_max_cnt2 = (!empty($arr_u1["max_appoint"])) ? $arr_u1["max_appoint"] : 0;
				$int_warn_percentage2 = (!empty($arr_u1["max_per"])) ? $arr_u1["max_per"] : 100;
			}
		}
	}
	
	//getting appt count for these months
	$arr_day_appt1 = array();
	$arr_fac_appt1 = array();
	$arr_day_am_appt1 = array();
	$arr_day_pm_appt1 = array();
	
	$arr_day_appt2 = array();
	$arr_fac_appt2 = array();
	$arr_day_am_appt2 = array();
	$arr_day_pm_appt2 = array();
	
	$arr_sch1 = array();
	$arr_sch2 = array();
	
	$qry_sch1 = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id_arr[0]." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime < '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test_query.txt',"\n 1=".$qry_sch1, FILE_APPEND);
	$res_sch1 = imw_query($qry_sch1);
	if(imw_num_rows($res_sch1) > 0){
		while($arr_sch1=imw_fetch_assoc($res_sch1))
		{
			$arr_day_am_appt1[$arr_sch1["sa_app_start_date"]] += $arr_sch1["appt_cnt"];
			$arr_day_appt1[$arr_sch1["sa_app_start_date"]] += $arr_sch1["appt_cnt"];
			$arr_fac_appt1[$arr_sch1["sa_app_start_date"]][$arr_sch1["sa_facility_id"]] += $arr_sch1["appt_cnt"];
		}
	}
	
	$qry_sch2 = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id_arr[1]." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime < '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test_query.txt',"\n 2=".$qry_sch2, FILE_APPEND);
	$res_sch2 = imw_query($qry_sch2);
	if(imw_num_rows($res_sch2) > 0){
		while($arr_sch2=imw_fetch_assoc($res_sch2)){
			$arr_day_am_appt2[$arr_sch2["sa_app_start_date"]] += $arr_sch2["appt_cnt"];
			$arr_day_appt2[$arr_sch2["sa_app_start_date"]] += $arr_sch2["appt_cnt"];
			$arr_fac_appt2[$arr_sch2["sa_app_start_date"]][$arr_sch2["sa_facility_id"]] += $arr_sch2["appt_cnt"];
		}
	}
	
	$qry_sch3 = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id_arr[0]." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime >= '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	$res_sch3 = imw_query($qry_sch3);
	//file_put_contents('test.txt',"\n 3=".$qry_sch3, FILE_APPEND);
	if(imw_num_rows($res_sch3) > 0){
		while($arr_sch3=imw_fetch_assoc($res_sch3)){
			$arr_day_pm_appt1[$arr_sch3["sa_app_start_date"]] += $arr_sch3["appt_cnt"];
			$arr_day_appt1[$arr_sch3["sa_app_start_date"]] += $arr_sch3["appt_cnt"];
			$arr_fac_appt1[$arr_sch3["sa_app_start_date"]][$arr_sch3["sa_facility_id"]] += $arr_sch3["appt_cnt"];
		}
	}
	
	$qry_sch4 = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id_arr[1]." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime >= '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test.txt',"\n 4=".$qry_sch4, FILE_APPEND);
	$res_sch4 = imw_query($qry_sch4);
	if(imw_num_rows($res_sch4) > 0){
		while($arr_sch4=imw_fetch_assoc($res_sch4)){
			$arr_day_pm_appt2[$arr_sch4["sa_app_start_date"]] += $arr_sch4["appt_cnt"];
			$arr_day_appt2[$arr_sch4["sa_app_start_date"]] += $arr_sch4["appt_cnt"];
			$arr_fac_appt2[$arr_sch4["sa_app_start_date"]][$arr_sch4["sa_facility_id"]] += $arr_sch4["appt_cnt"];
		}
	}
	
	//this month
	for($stval = $stts; $stval <= $edts; $stval = $stval + 86400){
		$working_this_dt = date("Y-m-d", $stval);
		$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		$str_div_content1=$str_div_content2='';
		list($str_div_content1, $int_total_secs1, $tmp_max_appointments_arr_h1) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id_arr[0], $fac_ids);
		
		list($str_div_content2, $int_total_secs2, $tmp_max_appointments_arr_h2) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id_arr[1], $fac_ids);
		$am_appts1=$pm_appts1=$am_appts2=$pm_appts2=0;
		if($str_div_content1 && $str_div_content2)
		{
			if(isset($arr_day_am_appt1[$working_this_dt])){ $am_appts1= $arr_day_am_appt1[$working_this_dt];}
			if(isset($arr_day_pm_appt1[$working_this_dt])){ $pm_appts1= $arr_day_pm_appt1[$working_this_dt];}
			
			if(isset($arr_day_am_appt2[$working_this_dt])){ $am_appts2= $arr_day_am_appt2[$working_this_dt];}
			if(isset($arr_day_pm_appt2[$working_this_dt])){ $pm_appts2= $arr_day_pm_appt2[$working_this_dt];}
			$str_response .= $prov_name1.'<br/>';
			$str_response .= $str_div_content1;
			$str_response .= 'Appointments: <b>'.$am_appts1.'/'.$pm_appts1.'</b><br/>';
			$str_response .= $prov_name2.'<br/>';
			$str_response .= $str_div_content2;
			$str_response .= 'Appointments: <b>'.$am_appts2.'/'.$pm_appts2.'</b>';	
		}
		$str_response .= "~~~";
		$str_response .= "default";	
		$str_response .= ":~:~:";
	}
	
	//next month
	for($stval = $stnxts; $stval <= $ednxts; $stval = $stval + 86400){
		$working_this_dt = date("Y-m-d", $stval);
		$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		$str_div_content1=$str_div_content2='';
		list($str_div_content1, $int_total_secs1, $tmp_max_appointments_arr_h1) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id_arr[0], $fac_ids);
		list($str_div_content2, $int_total_secs2, $tmp_max_appointments_arr_h2) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id_arr[1], $fac_ids);
		$am_appts1=$pm_appts1=$am_appts2=$pm_appts2=0;
		if($str_div_content1 && $str_div_content2)
		{
			if(isset($arr_day_am_appt1[$working_this_dt])){ $am_appts1= $arr_day_am_appt1[$working_this_dt];}
			if(isset($arr_day_pm_appt1[$working_this_dt])){ $pm_appts1= $arr_day_pm_appt1[$working_this_dt];}
			
			if(isset($arr_day_am_appt2[$working_this_dt])){ $am_appts2= $arr_day_am_appt2[$working_this_dt];}
			if(isset($arr_day_pm_appt2[$working_this_dt])){ $pm_appts2= $arr_day_pm_appt2[$working_this_dt];}
			$str_response .= $prov_name1.'<br/>';
			$str_response .= $str_div_content1;
			$str_response .= 'Appointments: <b>'.$am_appts1.'/'.$pm_appts1.'</b><br/>';
			$str_response .= $prov_name2.'<br/>';
			$str_response .= $str_div_content2;
			$str_response .= 'Appointments: <b>'.$am_appts2.'/'.$pm_appts2.'</b>';	
		}
		$str_response .= "~~~";
		$str_response .= "default";	
		$str_response .= ":~:~:";
	}
	
	//next 2 next month
	for($stval = $stnx2ts; $stval <= $ednx2ts; $stval = $stval + 86400){
		$working_this_dt = date("Y-m-d", $stval);
		$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		$str_div_content1=$str_div_content2='';
		list($str_div_content1, $int_total_secs1, $tmp_max_appointments_arr_h1) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id_arr[0], $fac_ids);
		list($str_div_content2, $int_total_secs2, $tmp_max_appointments_arr_h2) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id_arr[1], $fac_ids);
		$am_appts1=$pm_appts1=$am_appts2=$pm_appts2=0;
		if($str_div_content1 && $str_div_content2)
		{
			if(isset($arr_day_am_appt1[$working_this_dt])){ $am_appts1= $arr_day_am_appt1[$working_this_dt];}
			if(isset($arr_day_pm_appt1[$working_this_dt])){ $pm_appts1= $arr_day_pm_appt1[$working_this_dt];}
			
			if(isset($arr_day_am_appt2[$working_this_dt])){ $am_appts2= $arr_day_am_appt2[$working_this_dt];}
			if(isset($arr_day_pm_appt2[$working_this_dt])){ $pm_appts2= $arr_day_pm_appt2[$working_this_dt];}
			$str_response .= $prov_name1.'<br/>';
			$str_response .= $str_div_content1;
			$str_response .= 'Appointments: <b>'.$am_appts1.'/'.$pm_appts1.'</b><br/>';
			$str_response .= $prov_name2.'<br/>';
			$str_response .= $str_div_content2;
			$str_response .= 'Appointments: <b>'.$am_appts2.'/'.$pm_appts2.'</b>';		
		}
		$str_response .= "~~~";
		$str_response .= "default";	
		$str_response .= ":~:~:";
	}
}
elseif(sizeof($prov_id_arr)==1)
{
	//read from cache file that which facility have schedule
	
	$prov_id = (int) $_REQUEST["prov_id"];
	#### check for physician schedule ####
	//getting allowed max count
	$int_max_cnt = 0;
	$int_warn_percentage = 100;
	$qry_u = "SELECT max_per, max_appoint FROM users WHERE id =".$prov_id;
	$res_u = imw_query($qry_u);
	if(imw_num_rows($res_u) > 0){
		$arr_u = imw_fetch_assoc($res_u);
		$int_max_cnt = (!empty($arr_u["max_appoint"])) ? $arr_u["max_appoint"] : 0;
		$int_warn_percentage = (!empty($arr_u["max_per"])) ? $arr_u["max_per"] : 100;
	}
	$tmp_max_appointments_arr=array();
	############################################################################################
	# placing an dummy entry to restrict $obj_scheduler->highlight_provider_schedules function
	# to fire an query when this does not any size for tmp_max_appointments_arr array
	############################################################################################
	$tmp_max_appointments_arr['999999'] = 1000;
	$max_appt_qry = "SELECT id,MaxAppointments FROM schedule_templates where MaxAppointments>0";
	$max_appt_qry_obj = imw_query($max_appt_qry);
	while($maxRs = imw_fetch_assoc($max_appt_qry_obj)){
		$tmp_max_arr[$maxRs['id']] = $maxRs['MaxAppointments'];
	}
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
	//getting appt count for these months
	$arr_day_appt = array();
	$arr_fac_appt = array();
	$arr_day_am_appt = array();
	$arr_day_pm_appt = array();
	
	/*this is being used to show facility color on schedule calendar , that means it will show facility related color on date instead on purple color*/
	if(defined('SCHEDULER_FAC_COLOR_ON_CAL') && constant('SCHEDULER_FAC_COLOR_ON_CAL')==true)
	{
		//query to get facility colors
		$fac_colors=imw_query("select id, facility_color from facility");
		while($fac_colors_data=imw_fetch_object($fac_colors))
		{
			$fac_colors_arr[$fac_colors_data->id]=$fac_colors_data->facility_color;
		}
	}
	/*foreach($before_lunchTmArr as $key=>$lunchType)
	{
		$sch_ids=implode(',',$sch_templ_arr[$key]);
		if($sch_ids)
		{
			$qry_sch = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime < '$lunchType' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 and sch_template_id IN($sch_ids) GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test.txt',"\n 0=".$qry_sch, FILE_APPEND);
			$res_sch = imw_query($qry_sch);
			if(imw_num_rows($res_sch) > 0){
				while($arr_sch = imw_fetch_assoc($res_sch))
				{
					$arr_day_am_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
					$arr_day_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
					$arr_fac_appt[$arr_sch["sa_app_start_date"]][$arr_sch["sa_facility_id"]] += $arr_sch["appt_cnt"];
				}
			}
			$arr_sch = array();
			$qry_sch = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime >= '$lunchType' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 and sch_template_id IN($sch_ids) GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test.txt',"\n 0.1=".$qry_sch, FILE_APPEND);
			$res_sch = imw_query($qry_sch);
			if(imw_num_rows($res_sch) > 0){
				while($arr_sch = imw_fetch_assoc($res_sch))
				{
					$arr_day_pm_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
					$arr_day_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
					$arr_fac_appt[$arr_sch["sa_app_start_date"]][$arr_sch["sa_facility_id"]] += $arr_sch["appt_cnt"];
				}
			}
		}
	}*/
	/*---------previous code imposed again-----------*/
	$qry_sch = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime < '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test.txt',"\n 0.3=".$qry_sch, FILE_APPEND);
	$res_sch = imw_query($qry_sch);
	if(imw_num_rows($res_sch) > 0){	
		while($arr_sch = imw_fetch_assoc($res_sch)){
			$arr_day_am_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
			$arr_day_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
			$arr_fac_appt[$arr_sch["sa_app_start_date"]][$arr_sch["sa_facility_id"]] += $arr_sch["appt_cnt"];
		}
	}
	$arr_sch = array();
	$qry_sch = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$_REQUEST["loca"].") and sa_doctor_id =".$prov_id." and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime >= '12:00:00' and sa_app_start_date between '".date("Y-m-d", $stts)."' and '".date("Y-m-d", $ednx2ts)."' and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
	//file_put_contents('test.txt',"\n 0.4=".$qry_sch, FILE_APPEND);
	$res_sch = imw_query($qry_sch);
	if(imw_num_rows($res_sch) > 0){		
		while($arr_sch = imw_fetch_assoc($res_sch)){
			$arr_day_pm_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
			$arr_day_appt[$arr_sch["sa_app_start_date"]] += $arr_sch["appt_cnt"];
			$arr_fac_appt[$arr_sch["sa_app_start_date"]][$arr_sch["sa_facility_id"]] += $arr_sch["appt_cnt"];
		}
	}
	/*--------- previous code imposed again end here -----------*/
	
	//this month
	for($stval = $stts; $stval <= $edts; $stval = $stval + 86400){
		$default_flag = 0;
		$color='';
		$working_this_dt = date("Y-m-d", $stval);
		$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		list($str_div_content, $int_total_secs, $tmp_max_appointments_arr_h) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id, $fac_ids, $tmp_max_arr, $arr_all_tmp);
		if(sizeof($tmp_max_appointments_arr_h)==1)
		{
			//get facility color if we have schedule in one facility only
			$key_fac_id=array_keys($tmp_max_appointments_arr_h);
			if($key_fac_id[0])
			{
				$color=$fac_colors_arr[$key_fac_id[0]];
			}
		}
		$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
		$str_response .= $str_div_content;
		if(trim($str_div_content) != "")
		{
			$am_appts = 0; $pm_appts = 0;
			if(isset($arr_day_am_appt[$working_this_dt])){ $am_appts = $arr_day_am_appt[$working_this_dt];}
			if(isset($arr_day_pm_appt[$working_this_dt])){ $pm_appts = $arr_day_pm_appt[$working_this_dt];}
			$str_response .= 'Appointments: <b>'.$am_appts.'/'.$pm_appts.'</b>';			
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
		$str_response .= "~~~";
		if($default_flag == 0){
			$str_response .= $color;
		}
		$str_response .= ":~:~:";
	}
	
	//next month
	for($stval = $stnxts; $stval <= $ednxts; $stval = $stval + 86400){
		$default_flag = 0;
		$color='';
		$working_this_dt = date("Y-m-d", $stval);
		$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		list($str_div_content, $int_total_secs, $tmp_max_appointments_arr_h) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id, $fac_ids, $tmp_max_arr, $arr_all_tmp);
		if(sizeof($tmp_max_appointments_arr_h)==1)
		{
			//get facility color if we have schedule in one facility only
			$key_fac_id=array_keys($tmp_max_appointments_arr_h);
			if($key_fac_id[0])
			{
				$color=$fac_colors_arr[$key_fac_id[0]];
			}
		}
		$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
		$str_response .= $str_div_content;
		if(trim($str_div_content) != "")
		{
			$am_appts = 0; $pm_appts = 0;
			if(isset($arr_day_am_appt[$working_this_dt])){ $am_appts = $arr_day_am_appt[$working_this_dt];}
			if(isset($arr_day_pm_appt[$working_this_dt])){ $pm_appts = $arr_day_pm_appt[$working_this_dt];}
			$str_response .= 'Appointments: <b>'.$am_appts.'/'.$pm_appts.'</b>';			
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
		$str_response .= "~~~";
		if($default_flag == 0){
			$str_response .= $color;
		}
		$str_response .= ":~:~:";
	}
	
	//next 2 next month
	for($stval = $stnx2ts; $stval <= $ednx2ts; $stval = $stval + 86400){
		$default_flag = 0;
		$color='';
		$working_this_dt = date("Y-m-d", $stval);
		$str_response .= "dtblk-fl-cl_s_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-fl-cl_d_d-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."~~~";
		$str_response .= "dtblk-curr_".(int)date("d", $stval)."_".(int)date("m", $stval)."_detail~~~";
		list($str_div_content, $int_total_secs, $tmp_max_appointments_arr_h) = $obj_scheduler->highlight_provider_schedules($working_this_dt, 0, $prov_id, $fac_ids, $tmp_max_arr, $arr_all_tmp);
		if(sizeof($tmp_max_appointments_arr_h)==1)
		{
			//get facility color if we have schedule in one facility only
			$key_fac_id=array_keys($tmp_max_appointments_arr_h);
			if($key_fac_id[0])
			{
				$color=$fac_colors_arr[$key_fac_id[0]];
			}
		}
		$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
		$str_response .= $str_div_content;
		if(trim($str_div_content) != "")
		{
			$am_appts = 0; $pm_appts = 0;
			if(isset($arr_day_am_appt[$working_this_dt])){ $am_appts = $arr_day_am_appt[$working_this_dt];}
			if(isset($arr_day_pm_appt[$working_this_dt])){ $pm_appts = $arr_day_pm_appt[$working_this_dt];}
			$str_response .= 'Appointments: <b>'.$am_appts.'/'.$pm_appts.'</b>';			
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
		$str_response .= "~~~";
		if($default_flag == 0){
			$str_response .= $color;
		}
		$str_response .= ":~:~:";
	}
}
echo $str_response;
?>