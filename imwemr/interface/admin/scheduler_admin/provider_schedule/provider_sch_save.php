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

require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
require_once('../../../../library/classes/cls_common_function.php');
$pid=0;
if($_REQUEST["Start_date_String"]<>""){
	$Start_date_StringArray = explode(",",$_REQUEST["Start_date_String"]);
	array_pop($Start_date_StringArray);
}
if($_REQUEST['replace_pid'] || $_REQUEST['replace_facility'])$trackArr["action"]='Replaced';
else $trackArr["action"]='Added';

if($exception == 0){
	$status = 'no';
}else{
	$status = 'yes';
}


//file_put_contents('test.txt',print_r($_REQUEST, true)."\n");

if($_POST[child_from])
{
	$childFromArr=explode('-',$_POST[child_from]);
	$childFrom=$childFromArr[2].'-'.$childFromArr[0].'-'.$childFromArr[1];
}
if($_POST[child_to])
{
	$childToArr=explode('-',$_POST[child_to]);
	$childTo=$childToArr[2].'-'.$childToArr[0].'-'.$childToArr[1];
}

if(trim($sel_child_schedule_name) != "" && !$replace_pid)
{
	$child_sch_arr = explode('<>',$sel_child_schedule_name);	
}

$sch_arr = explode('<>',$sel_schedule_name);	
$previous_template=$sch_arr[0];


$old_temp_id = $old_temp_id_str = $new_temp_id ="";
$old_temp_id_str= $old_temp_id = $sch_arr[0];
if(trim($sel_child_schedule_name))
{
	$child_sch_arr_old = explode('<>',$sel_child_schedule_name);
	if($child_sch_arr_old[0]){
	$old_temp_id_str.=", ".$child_sch_arr_old[0];
	}	
}
if(isset($_REQUEST["replace_pid"]) && trim($_REQUEST["replace_pid"]) != "")
{
	
	$new_temp_id = trim($_REQUEST["replace_pid"]);
	$sch_arr = explode('<>',$new_temp_id);	
	$new_temp_id=$sch_arr[0];
	$replace_action ='template';
	
	//get parent template start and end time
	$qq3=imw_query("select morning_start_time,morning_end_time from schedule_templates where id=$old_temp_id");
	if(imw_num_rows($qq3)>0){
		$dd3=imw_fetch_assoc($qq3);
		$remove_tmp['tmp_id']=$tmp_id;
		$remove_tmp['tmp_start_time']=$dd3['morning_start_time'];
		$remove_tmp['tmp_end_time']=$dd3['morning_end_time'];
	}
}


if(trim($replace_cid) != "")
{
	$child_sch_arr = explode('<>',$replace_cid);	
	$new_temp_id.=", ".$child_sch_arr[0];
	if($_REQUEST[replace_c_from])
	{
		$childFromArr=explode('-',$_REQUEST[replace_c_from]);
		$childFrom=$childFromArr[2].'-'.$childFromArr[0].'-'.$childFromArr[1];
	}
	if($_REQUEST[replace_c_to])
	{
		$childToArr=explode('-',$_REQUEST[replace_c_to]);
		$childTo=$childToArr[2].'-'.$childToArr[0].'-'.$childToArr[1];
	}
}

$sch_id = $sch_arr[0];
$pro_id = $sel_pro;
$fac_id = $sel_facility;


if($_REQUEST["replace_facility"])
{
	$old_facility = $sel_facility;	
	$new_facility = trim($_REQUEST["replace_facility"]);
	$replace_appt_act =$_REQUEST['replace_appt_act'];	
	$replace_action =$_REQUEST['replace_action'];	
	
	$fac_id=$new_facility;
}

$sch_id_str=$sch_id;
//check for child template
if($child_sch_arr[0]){
	$sch_id_str.=','.$child_sch_arr[0];
}

$date_status = $start_time.','.$end_time;
if(strtoupper($end_time) == 'AM'){
	if($end_hour == 12){
		$end_hour = $end_hour - 12;	
		$end_hour = '0'.$end_hour;	
	}
}
if(strtoupper($start_time) == 'AM'){
	if($start_hour == 12){
		$start_hour = $start_hour - 12;
		$start_hour = '0'.$start_hour;
	}
}
if(strtoupper($end_time) == 'PM'){
	if($end_hour != 12){
		$end_hour = $end_hour + 12;
	}
}
if(strtoupper($start_time) == 'PM'){
	if($start_hour != 12){
		$start_hour = $start_hour + 12;
	}
}

$morning_start_time = $start_hour.':'.$start_min.':00';
$morning_end_time = $end_hour.':'.$end_min.':00';

//inserting new template
if($new_sch){
	if($start_hour > 0 || $end_hour > 0){
		$qry = "insert into schedule_templates set morning_start_time = '$morning_start_time', morning_end_time = '$morning_end_time',date_status  = '$date_status', check_true = 'true',schedule_name = '".addslashes($template_label)."'";	
		imw_query($qry);

		$sch_id = imw_insert_id();
	}
}
$disable_templates_slot="";
if($_REQUEST['enable_templates_slot']){
	$disable_templates_slot=$_REQUEST['enable_templates_slot'];
}
//To single/Save Multiple Date Templates
if(is_array($Start_date_StringArray)){
	$wrdata_count_status =  count($Start_date_StringArray);
	for($i=0;$i<count($Start_date_StringArray);$i++){
		$cur_date_arr=array();	
		$cur_date_arr = explode('-',$Start_date_StringArray[$i]);
		if(strtolower(substr(trim($GLOBALS['date_format']),0,2))=="dd"){// Internation Date Formating 
			$cur_date_arr_exp=explode('-',$Start_date_StringArray[$i]);
			$cur_date_arr[0]=$cur_date_arr_exp[1];
			$cur_date_arr[1]=$cur_date_arr_exp[0];
			$cur_date_arr[2]=$cur_date_arr_exp[2];
		}
		$weekDays = date('N',mktime(0,0,0,$cur_date_arr[0],$cur_date_arr[1],$cur_date_arr[2]));
		$week = getWeekCount($cur_date_arr[1]);		
		$last_day_t = trim($_REQUEST['last_day_t']);
		if($cur_date_arr[1]>$last_day_t){$cur_date_arr[1] = $last_day_t;}
		$task_Date = date('Y-m-d',mktime(0,0,0,$cur_date_arr[0],$cur_date_arr[1],$cur_date_arr[2]));
		$next_working_date = date('Y-m-d',mktime(0,0,0,$cur_date_arr[0],($cur_date_arr[1]+1),$cur_date_arr[2]));
		list($year, $month, $day) = explode('-',$task_Date);
		$start_day = date('N',mktime(0,0,0,$cur_date_arr[0],1,$cur_date_arr[2]));		
		$wrdata = trim($_REQUEST['wrdata']);
		if($wrdata!= "" && isset($wrdata) && $wrdata_count_status ==1)
		{			
			$wrdata_arr = explode('|',$wrdata);
			if(count($wrdata_arr) == 2)
			{
				$week = $wrdata_arr[0];
				$weekDays = $wrdata_arr[1];					
			}
		}
		
		$qry = "select id, delete_row as db_delete_row from  provider_schedule_tmp where provider = '$pro_id' and  facility = '$fac_id' and sch_tmp_id = '$sch_id' and week$week='$weekDays' and today_date = '$task_Date' and del_status = 1";		
		$queryId = imw_query($qry);
		$sum_str="Provider schedule added";
		if($replace_action){
			$sum_str=($replace_action=='facility')?"Schedule added by replacing facility (a)":"Schedule added by replacing template (a)";
		}
		if($sch_app_id && $pro_id && $fac_id){
			$qry = "update provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', sch_tmp_id='$sch_id', today_date='$task_Date', status='$status', del_status = 0, delete_row = '',iportal_enable_slot='".$disable_templates_slot."' where id = '$sch_app_id'";
			$pid=$sch_app_id;
			
			tmp_log('Updated', $sum_str, $pro_id, $fac_id, $sch_id_str, $task_Date, "week$week /".$weekDays, $status,'Provider schedule added and system used deleted  one('.$sch_app_id.')');
		}else if(imw_num_rows($queryId)<=0  && $pro_id && $fac_id){
			$qry = "insert into provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', 	sch_tmp_id='$sch_id', today_date='$task_Date', status='$status', del_status = 0,iportal_enable_slot='".$disable_templates_slot."'";
			tmp_log($trackArr["action"], $sum_str, $pro_id, $fac_id, $sch_id_str, $task_Date, "week$week /".$weekDays, $status);
			$new_added=true;
		}else if($pro_id && $fac_id){
			list ($id, $db_delete_row) = imw_fetch_array($queryId);
			if($db_delete_row=='all' && $status=='no'){
				//add an delete entry for future date
				imw_query("insert into provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', sch_tmp_id='$sch_id', today_date='$next_working_date', status='no', del_status = 1 , delete_row = 'all' ");
			}
			$qry = "update provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', sch_tmp_id='$sch_id', today_date='$task_Date', status='$status', del_status = 0, delete_row = '',iportal_enable_slot='".$disable_templates_slot."' where id = $id";
			$pid=$id;
			tmp_log('Updated', $sum_str, $pro_id, $fac_id, $sch_id_str, $task_Date, "week$week /".$weekDays, $status, 'Provider schedule added and system used deleted one('.$id.')');
		}
		$qryId = imw_query($qry);
		//get parent id
		if($child_sch_arr[0] && $new_added==true)$pid=imw_insert_id();
		
		if($task_Date != '0000-00-00' && $task_Date != '')
		{
			if($status == 'yes')
			{
				if(isset($tmp_expiry_dt) && trim($tmp_expiry_dt)!="")
				{
					$tmp_expiry_dt_arr = explode('-',$tmp_expiry_dt);
					$db_tmp_exp_dt = $tmp_expiry_dt_arr[2].'-'.$tmp_expiry_dt_arr[0].'-'.$tmp_expiry_dt_arr[1];				
					$setRepQry = "DELETE FROM provider_schedule_tmp WHERE provider='$pro_id' and week$week='$weekDays' and facility='$fac_id' and sch_tmp_id='$sch_id' and today_date > '$task_Date' and today_date < '$db_tmp_exp_dt' and status='no' and del_status = 1 and delete_row = 'all'";
				}
				else
				{
					$setRepQry = "DELETE FROM provider_schedule_tmp WHERE provider='$pro_id' and week$week='$weekDays' and facility='$fac_id' and sch_tmp_id='$sch_id' and today_date > '$task_Date' and status='no' and del_status = 1 and delete_row = 'all' ";	
				}
			}elseif($status == 'no' && $pid)
			{
				$setRepQry = "delete from provider_schedule_tmp where provider = '$pro_id' and facility = '$fac_id' and sch_tmp_id = '$sch_id' and week$week='$weekDays' and today_date = '$task_Date' and id not in ($pid)";	
			}
			
			if($setRepQry)imw_query($setRepQry);			
		}
		$template_expiry_exists = 0;
		if(isset($tmp_expiry_dt) && trim($tmp_expiry_dt)!="" && $status == 'yes' && $pro_id && $fac_id)
		{ 
			$tmp_expiry_dt_arr = explode('-',$tmp_expiry_dt);
			$db_tmp_exp_dt = $tmp_expiry_dt_arr[2].'-'.$tmp_expiry_dt_arr[0].'-'.$tmp_expiry_dt_arr[1];
			$exp_dt_qry = "INSERT INTO provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', sch_tmp_id='$sch_id', today_date='$db_tmp_exp_dt', status='no', del_status = 1, delete_row = 'all',iportal_enable_slot='".$disable_templates_slot."' ";
			imw_query($exp_dt_qry);
			$template_expiry_exists = 1;
			if($childTo && $childTo>$db_tmp_exp_dt)$childTo=$db_tmp_exp_dt;
			elseif(!$childTo)$childTo=$db_tmp_exp_dt;
		}
		//delete any existing child entry for removed template
		if($pid){
			imw_query("update provider_schedule_tmp_child set status=2,
					 deleted_by='$_SESSION[authId]',
					 deleted_on='".date('Y-m-d H:i:s')."' 
					 where pid=$pid and status=1");
			//get start and end date for child template
			$qq1=imw_query("select * from provider_schedule_tmp_child where pid=$pid order by id desc limit 1");
			if(imw_num_rows($qq1)>0){
				$dd1=imw_fetch_assoc($qq1);
				$remove_tmp['child_tmp_id']=$dd1['sch_tmp_id'];
				$remove_tmp['child_start_date']=$dd1['start_date'];
				$remove_tmp['child_end_date']=$dd1['end_date'];
				//get child template start and end time
				$qq2=imw_query("select morning_start_time,morning_end_time from schedule_templates where id=$dd1[sch_tmp_id]");
				if(imw_num_rows($qq2)>0){
					$dd2=imw_fetch_assoc($qq2);
					$remove_tmp['child_start_time']=$dd2['morning_start_time'];
					$remove_tmp['child_end_time']=$dd2['morning_end_time'];
				}
			}
		}
		
		if($child_sch_arr[0]>0)
		{
			imw_query("insert into provider_schedule_tmp_child set pid='$pid',
					 sch_tmp_pid='$sch_id',
					 sch_tmp_id='".$child_sch_arr[0]."',
					 provider='$pro_id',
					 facility='$fac_id',
					 start_date='$childFrom',
					 end_date='$childTo',
					 status=1,
					 applied_by='$_SESSION[authId]',
					 applied_on='".date('Y-m-d H:i:s')."'");
		}
		
		/*// code comes here regarding the template is the child template 
		$check_child_sch = "SELECT id,parent_id FROM schedule_templates WHERE id = (SELECT parent_id FROM schedule_templates WHERE id = ".$sch_id.") ";
		$check_child_sch_obj = imw_query($check_child_sch);*/
		if(trim($new_temp_id) != "")
		{
			// confirmation regarding the template is the child template.
			//$parent_temp_data = imw_fetch_assoc($check_child_sch_obj);
			if(trim($new_temp_id) != "")
			{					
				$parent_temp_data['id'] = $old_temp_id;					
			}			
			if(trim($parent_temp_data['id']) != "")
			{
				$template_match = 0;
				$schedule_match = array();
				// if the parent template schedule is set then set its deactivation and activation
				$req_wrdata_sent = $week.'|'.$weekDays;
				$provider_sch_exist_arr = getSchTmpData($task_Date,$pro_id,$req_wrdata_sent);
				//print_r($provider_sch_exist_arr); exit;
				if(count($provider_sch_exist_arr)>0)
				{
					foreach($provider_sch_exist_arr as $provider_sch_exists_vals)
					{
						if($provider_sch_exists_vals['sch_tmp_id'] == $parent_temp_data['id'] && $fac_id == $provider_sch_exists_vals['facility'])
						{
							$template_match = 1;
							$schedule_match = $provider_sch_exists_vals;
							break;
						}
					}
					
					if($template_match == 1)
					{						
						$sch_tmp_id_parent = $schedule_match['sch_tmp_id'];
						if($pro_id && $fac_id)
						// deleting the parent template schedule for the child template start date / activation date
						$parent_tmp_set_qry = "insert into provider_schedule_tmp set provider = $pro_id,facility = $fac_id
												,sch_tmp_id = $sch_tmp_id_parent, week$week='$weekDays',today_date = '$task_Date'
												,status = 'no',delete_row = 'all',del_status = 1,iportal_enable_slot='".$disable_templates_slot."'";
						imw_query($parent_tmp_set_qry);
						tmp_log('Deleted', 'Schedule removed by replacing template (b)', $pro_id, $fac_id, $old_temp_id_str, $task_Date, "week$week /".$weekDays, $status);
																	
						if(trim($schedule_match['status']) == "yes" && $template_expiry_exists == 1)
						{							
							// If the expiry date of the parent template exists and less than the child template expiry date then parent template should not activate in the future. 
							$parent_tmp_expiry = "SELECT id FROM provider_schedule_tmp WHERE provider='$pro_id' and week$week='$weekDays' and facility='$fac_id' and sch_tmp_id='$sch_tmp_id_parent' and today_date > '$task_Date' and today_date <= '$db_tmp_exp_dt' and status = 'no' and delete_row = 'all' and del_status = 1";							
							$parent_tmp_expiry_obj = imw_query($parent_tmp_expiry);
							if(imw_num_rows($parent_tmp_expiry_obj) <= 0 && $pro_id && $fac_id)
							{
								// Activating the parent template when the child template expires and if the parent template schedule is for the future.
								$parent_tmp_set_qry = "insert into provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', sch_tmp_id='$sch_tmp_id_parent', today_date='$db_tmp_exp_dt', status='yes', del_status = 0,iportal_enable_slot='".$disable_templates_slot."'";
								imw_query($parent_tmp_set_qry);																
							}
						}
						
						if(trim($schedule_match['status']) == "yes" && $status == "no" && $pro_id && $fac_id)
						{
							$parent_task_dt_arr = explode('-',$task_Date);
							$parent_task_dt_arr[2]++;
							$target_parent_activate_dt = date('Y-m-d',mktime(0,0,0,$parent_task_dt_arr[1],$parent_task_dt_arr[2],$parent_task_dt_arr[0]));
							// Activating the parent template when the child template deactivated and if the parent template schedule is for the future.
							$parent_tmp_set_qry = "insert into provider_schedule_tmp set provider='$pro_id', week$week='$weekDays', facility='$fac_id', sch_tmp_id='$sch_tmp_id_parent', today_date='$target_parent_activate_dt', status='yes', del_status = 0,iportal_enable_slot='".$disable_templates_slot."'";
							imw_query($parent_tmp_set_qry);							
						}
						
						// get the fields of the applied Child template so that update the appts. template id for available time range.
						$applied_child_sch_qry = "SELECT * FROM schedule_templates WHERE id = $sch_id";						
						$applied_child_sch_qry_obj = imw_query($applied_child_sch_qry);
						$applied_child_sch_qry_data = imw_fetch_assoc($applied_child_sch_qry_obj);
						$applied_sch_start_time = strtotime($applied_child_sch_qry_data['morning_start_time']);
						$applied_sch_end_time = strtotime($applied_child_sch_qry_data['morning_end_time']);
						
						// if the parent template is assigned then make the operations on the appointments.
						if($status == "yes" && $template_expiry_exists == 1)
						{
							$qryFinal = "SELECT id, sa_patient_id, sa_app_starttime, sa_app_start_date ,sa_app_endtime
									FROM `schedule_appointments` 
									WHERE (sa_doctor_id = '".$pro_id."' and sa_facility_id = '".$fac_id."') 
									and (sa_app_start_date >= '".$task_Date."')	
									and (sa_app_start_date < '".$db_tmp_exp_dt."')		
									and CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekDays."' 
									AND sch_template_id IN ($old_temp_id_str)
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
									ORDER BY sa_app_start_date DESC";
							//query to delete custom label entries
							$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
									AND facility= '".$fac_id."'
									AND (start_date >= '".$task_Date."')	
									AND (start_date < '".$db_tmp_exp_dt."')	
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";
							//query to delete block lock time slot entries
							$delBlockEntries="delete from block_times where provider= '".$pro_id."'
									AND facility= '".$fac_id."'
									AND (start_date >= '".$task_Date."')
									AND (start_date < '".$db_tmp_exp_dt."')			
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";						
						}
						else if($status == "yes" && $template_expiry_exists == 0){
							$qryFinal = "SELECT id, sa_patient_id, sa_app_starttime, sa_app_start_date ,sa_app_endtime
									FROM `schedule_appointments` 
									WHERE (sa_doctor_id = '".$pro_id."' and sa_facility_id = '".$fac_id."') 
									and (sa_app_start_date >= '".$task_Date."')		
									and CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekDays."' 
									AND sch_template_id IN ($old_temp_id_str)
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
									ORDER BY sa_app_start_date DESC ";
							//query to delete custom label entries
							$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
									AND facility= '".$fac_id."'
									AND (start_date >= '".$task_Date."')	
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";
							//query to delete block lock time slot entries
							$delBlockEntries="delete from block_times where provider= '".$pro_id."'
									AND facility= '".$fac_id."'
									AND (start_date >= '".$task_Date."')	
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";
						}else{
							
							$qryFinal = "Select id, sa_patient_id, sa_app_starttime, sa_app_endtime, sa_app_start_date from schedule_appointments 
									where (sa_doctor_id='".$pro_id."' 
									AND sa_facility_id='".$fac_id."' 
									AND ('".$task_Date."' between sa_app_start_date and sa_app_end_date) ) 
									AND sch_template_id IN ($old_temp_id_str)
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)";
			
							//query to delete custom label entries
							$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
									AND facility= '".$fac_id."'
									AND start_date ='".$task_Date."'
									AND temp_id IN ($old_temp_id_str)";
							//query to delete block lock time slot entries
							$delBlockEntries="delete from block_times where provider= '".$pro_id."'
									AND facility= '".$fac_id."'
									AND start_date ='".$task_Date."'
									AND temp_id IN ($old_temp_id_str)";
						}
						imw_query($delCustomEntries);
						imw_query($delBlockEntries);
						$re = imw_query($qryFinal);
						while($row = imw_fetch_array($re)){
							$sch_appt_id = $row['id'];
							$sch_start_time_up = strtotime($row['sa_app_starttime']);
							
							$sttm = strtotime($row["sa_app_starttime"]);
                            $edtm = strtotime($row["sa_app_endtime"]);
							######################################################
							# Code to remove entry from sch_custom_lbl
							######################################################
                            for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
								$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);

								$start_loop_time = date("H:i:00", $looptm);
								$end_loop_time = date("H:i:00", $edtm2);
								 $q2 = "SELECT id, l_text, l_show_text, labels_replaced FROM scheduler_custom_labels WHERE provider = '".$pro_id."' AND facility = '".$fac_id."' AND start_date = '".$task_Date."' AND start_time = '".$start_loop_time."' AND end_time = '".$end_loop_time."'";
								$r2 = imw_query($q2);
								while($row1 = imw_fetch_array($r2)){
									$new_entry = $row1["labels_replaced"];
									$l_text = $row1["l_show_text"];
									
									if(trim($row1["labels_replaced"]) != ""){ 
											$arr_lbl_replaced = explode("::", $row1["labels_replaced"]);
											if(count($arr_lbl_replaced) > 0){ 
													foreach($arr_lbl_replaced as $this_lbl_replaced){
															$arr_this_replaced2 = explode(":", $this_lbl_replaced);
															if(trim($arr_this_replaced2[0]) == $sch_appt_id){ 
																	$new_entry = str_replace("::".$arr_this_replaced2[0].":".$arr_this_replaced2[1], "", $row1["labels_replaced"]);

																	if(trim($row1["l_show_text"]) != ""){
																			$l_text = $row1["l_show_text"]."; ".$arr_this_replaced2[1];
																	}else{
																			$l_text = $arr_this_replaced2[1];
																	}
															}
													}
											}
									}
									$upd22 = "UPDATE scheduler_custom_labels SET l_show_text = '".$l_text."', labels_replaced = '".$new_entry."' WHERE id =	'".$row1["id"]."'";
									imw_query($upd22);
							}
							}
							######################################################
							# End of code to remove entry from sch_custom_lbl
							######################################################
							if($sch_start_time_up >= $applied_sch_start_time && $sch_start_time_up < $applied_sch_end_time)
							{
								$new_template_id=$sch_id;
								if($child_sch_arr[0])
								{
									$appt_date=strtotime($row['sa_app_start_date']);
									if($appt_date>=strtotime($childFrom) && $appt_date<=strtotime($childTo))
									{
										$new_template_id=$child_sch_arr[0];
									}
								}
								$update_schappt_tb_qry = "UPDATE schedule_appointments SET sch_template_id = $new_template_id WHERE id = $sch_appt_id";
								imw_query($update_schappt_tb_qry);
								
								//keep appointments to replac label later.
								$arr['provider']=$pro_id;
								$arr['facility']=$fac_id;
								$arr['template']=$new_template_id;
								$arr_dates[$row['sa_app_start_date']]=$row['sa_app_start_date'];
								$ids_to_replace_label[$sch_appt_id]=$sch_appt_id;
							}
							else
							{
								//logging this action in previous status table
								logApptChangedStatus($sch_appt_id, "", "", "", "201", "", "", $_SESSION['authUser'], "Provider Schedule Deleted.", "", false);
					
								//updating schedule appointments details
								updateScheduleApptDetails($sch_appt_id, "", "", "", "201", "", "", $_SESSION['authUser'], "Provider Schedule Deleted.", "", false);
							}
						}						
					}
					
				}
			}
		}//end of replace template code		
		//}####
		
		if($replace_action=='facility')
		{
			
			// confirmation regarding the template is the child template.
						
			if(trim($new_facility) != "")
			{
				$template_match = 0;
				$schedule_match = array();
				// if the parent template schedule is set then set its deactivation and activation
				$req_wrdata_sent = $week.'|'.$weekDays;
				$provider_sch_exist_arr = getSchTmpData($task_Date,$pro_id,$req_wrdata_sent);
				if(count($provider_sch_exist_arr)>0)
				{
					foreach($provider_sch_exist_arr as $provider_sch_exists_vals)
					{
						if($provider_sch_exists_vals['sch_tmp_id'] == $sch_id && $old_facility == $provider_sch_exists_vals['facility'])
						{
							$template_match = 1;
							$schedule_match = $provider_sch_exists_vals;
							break;
						}
					}
					
					if($template_match == 1 && $pro_id && $old_facility)
					{						
						$sch_tmp_id_parent = $schedule_match['sch_tmp_id'];
						// deleting the parent template schedule for the child template start date / activation date
						$parent_tmp_set_qry = "insert into provider_schedule_tmp set provider = $pro_id,facility = $old_facility
												,sch_tmp_id = $sch_tmp_id_parent, week$week='$weekDays',today_date = '$task_Date'
												,status = 'no',del_status = 1,iportal_enable_slot='".$disable_templates_slot."'";
						$parent_tmp_set_qry.=($status=='no')?", delete_row = 'no'":", delete_row = 'all'";
						imw_query($parent_tmp_set_qry);
						tmp_log('Deleted', 'Schedule removed by replacing facility (b)', $pro_id, $old_facility, $old_temp_id_str, $task_Date, "week$week /".$weekDays, 'yes');
						
						// get the fields of the applied Child template so that update the appts. template id for available time range.
						$applied_child_sch_qry = "SELECT * FROM schedule_templates WHERE id = $sch_id";						
						$applied_child_sch_qry_obj = imw_query($applied_child_sch_qry);
						$applied_child_sch_qry_data = imw_fetch_assoc($applied_child_sch_qry_obj);
						$applied_sch_start_time = strtotime($applied_child_sch_qry_data['morning_start_time']);
						$applied_sch_end_time = strtotime($applied_child_sch_qry_data['morning_end_time']);
						
						// if the parent template is assigned then make the operations on the appointments.
						if($status == "yes" && $template_expiry_exists == 1)
						{
							$qryFinal = "SELECT id, sa_patient_id, sa_app_starttime, sa_app_start_date ,sa_app_endtime, sa_doctor_id, sa_facility_id, procedureid
									FROM `schedule_appointments` 
									WHERE (sa_doctor_id = '".$pro_id."' and sa_facility_id = '".$old_facility."') 
									and (sa_app_start_date >= '".$task_Date."')	
									and (sa_app_start_date < '".$db_tmp_exp_dt."')		
									and CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekDays."' 
									AND sch_template_id IN ($old_temp_id_str)
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
									ORDER BY sa_app_start_date DESC";
							//query to delete custom label entries
							/*$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
									AND facility= '".$old_facility."'
									AND (start_date >= '".$task_Date."')	
									AND (start_date < '".$db_tmp_exp_dt."')	
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";*/
							//query to delete block lock time slot entries
							/*$delBlockEntries="delete from block_times where provider= '".$pro_id."'
									AND facility= '".$old_facility."'
									AND (start_date >= '".$task_Date."')
									AND (start_date < '".$db_tmp_exp_dt."')			
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";	*/					
						}
						else if($status == "yes" && $template_expiry_exists == 0){
							$qryFinal = "SELECT id, sa_patient_id, sa_app_starttime, sa_app_start_date ,sa_app_endtime, sa_doctor_id, sa_facility_id, procedureid
									FROM `schedule_appointments` 
									WHERE (sa_doctor_id = '".$pro_id."' and sa_facility_id = '".$old_facility."') 
									and (sa_app_start_date >= '".$task_Date."')		
									and CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekDays."' 
									AND sch_template_id IN ($old_temp_id_str)
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
									ORDER BY sa_app_start_date DESC ";
							//query to delete custom label entries
							/*$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
									AND facility= '".$old_facility."'
									AND (start_date >= '".$task_Date."')	
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";
							//query to delete block lock time slot entries
							$delBlockEntries="delete from block_times where provider= '".$pro_id."'
									AND facility= '".$old_facility."'
									AND (start_date >= '".$task_Date."')	
									AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
									AND DATE_FORMAT( start_date, '%w' ) = '".$weekDays."'
									AND temp_id IN ($old_temp_id_str)";*/
						}else{
							
							$qryFinal = "Select id, sa_patient_id, sa_app_starttime, sa_app_endtime, sa_doctor_id, sa_facility_id, procedureid, sa_app_start_date from schedule_appointments 
									where (sa_doctor_id='".$pro_id."' 
									AND sa_facility_id='".$old_facility."' 
									AND ('".$task_Date."' between sa_app_start_date and sa_app_end_date) ) 
									AND sch_template_id IN ($old_temp_id_str)
									AND sa_patient_app_status_id NOT IN (203,201,18,19,20)";
			
							//query to delete custom label entries
							/*$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
									AND facility= '".$old_facility."'
									AND start_date ='".$task_Date."'
									AND temp_id IN ($sch_tmp_id_parent)";*/
							//query to delete block lock time slot entries
							/*$delBlockEntries="delete from block_times where provider= '".$pro_id."'
									AND facility= '".$old_facility."'
									AND start_date ='".$task_Date."'
									AND temp_id IN ($sch_tmp_id_parent)";*/
						}
						//imw_query($delCustomEntries);
						//imw_query($delBlockEntries);
						
						$re = imw_query($qryFinal);
						while($row = imw_fetch_array($re)){
							$sch_appt_id = $row['id'];
							$sch_start_time_up = strtotime($row['sa_app_starttime']);
							
							$sttm = strtotime($row["sa_app_starttime"]);
                            $edtm = strtotime($row["sa_app_endtime"]);
							######################################################
							# Code to remove entry from sch_custom_lbl
							######################################################
                            for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
								$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);

								$start_loop_time = date("H:i:00", $looptm);
								$end_loop_time = date("H:i:00", $edtm2);
								 $q2 = "update scheduler_custom_labels set facility = '".$fac_id."' WHERE provider = '".$pro_id."' AND facility = '".$old_facility."' AND start_date = '".$task_Date."' AND start_time = '".$start_loop_time."' AND end_time = '".$end_loop_time."'";
								$r2 = imw_query($q2);
							}
							if($sch_start_time_up >= $applied_sch_start_time && $sch_start_time_up < $applied_sch_end_time && $replace_appt_act=='keep_appt')
							{
								//logging this action in previous status table
								logApptChangedStatus($row['id'], $row['sa_app_start_date'], $row['sa_app_starttime'], $row['sa_app_endtime'], "202", $row['sa_doctor_id'], $fac_id, $_SESSION['authUser'], "Provider Schedule Deleted.", $row['procedureid'], true);
								
								$update_schappt_tb_qry = "UPDATE schedule_appointments SET sa_facility_id = $fac_id, sa_patient_app_status_id=202 WHERE id = $sch_appt_id";
								imw_query($update_schappt_tb_qry);
								
								//keep appointments to replac label later.
								$arr['provider']=$pro_id;
								$arr['facility']=$fac_id;
								$arr['template']=$old_temp_id_str;
								$arr_dates[$row['sa_app_start_date']]=$row['sa_app_start_date'];
								$ids_to_replace_label[$sch_appt_id]=$sch_appt_id;
								
							}
							else
							{
								//logging this action in previous status table
								logApptChangedStatus($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Provider Schedule Deleted.", "", false);
					
								//updating schedule appointments details
								updateScheduleApptDetails($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Provider Schedule Deleted.", "", false);
							}
						}						
					}
					
				}
			}
		
		}//end of replace facility block
		
		//---- Delete Xml For Future -------
		$dir = realpath($GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH').'/scheduler_common/load_xml');
		$op = opendir($dir);
		while($file = readdir($op)){
			$extn = substr($file,-3);
			if(strtolower($extn) == 'sch'){
				$fileDate = explode('-',$file);
				$filePro = explode(".", $fileDate[3]);
				if($replace_action=='facility')$fac_id=$old_facility;
				elseif($replace_action=='template') $sch_id=$old_temp_id_str;
				
				if($status == 'yes'){
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]) >= strtotime($task_Date) && $filePro[0] == $pro_id){
						//echo $dir.'/'.$file;
						unlink($dir.'/'.$file);
						if($replace_action=='facility' || $replace_action=='template'){
							$cache_week=ceil($fileDate[2]/7);
							$cache_weekdays=date('w', strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]));
							if($week==$cache_week && $weekDays==$cache_weekdays){//delete entries without checking template id
								deleteCustomLabelAndBlockSlot($fileDate[0]."-".$fileDate[1]."-".$fileDate[2], $remove_tmp, $pro_id, $fac_id);
							}
						}
					}
				}else{
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]) == strtotime($task_Date) && $filePro[0] == $pro_id){
						//echo $dir.'/'.$file;
						unlink($dir.'/'.$file);
						if($replace_action=='facility' || $replace_action=='template'){
							deleteCustomLabelAndBlockSlot($fileDate[0]."-".$fileDate[1]."-".$fileDate[2], $remove_tmp, $pro_id, $fac_id);
						}
					
					}
				}
			}
		}
	}
	if($replace_action=='facility' || $replace_action=='template'){
		include('replace_label.php');
	}
}
header('location:provider_sch.php?sel_pro_month='.$pro_id.'&thedate='.$cur_date_arr[1].'&theyear='.$cur_date_arr[2].'&themonth='.$cur_date_arr[0]);
?>