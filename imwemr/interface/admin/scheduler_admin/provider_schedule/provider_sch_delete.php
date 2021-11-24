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

if($_REQUEST["Start_date_String"]<>""){
	$Start_date_StringArray = explode(",",$_REQUEST["Start_date_String"]);
	array_pop($Start_date_StringArray);
}

$GLOBALDATEFORMAT = $GLOBALS['date_format'];

if($exception == 0){
	$status = 'no';
}else{
	$status = 'all';
}
/*if(trim($sel_child_schedule_name) != "")
{
	$sch_arr = explode('<>',$sel_child_schedule_name);	
}*/

$sch_arr = explode('<>',$sel_schedule_name);	

if($GLOBALDATEFORMAT == "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
{list($date_arr[1],$date_arr[0],$date_arr[2]) = explode('-',$_REQUEST['Start_date']);}
else
{$date_arr = explode('-',$_REQUEST['Start_date']);}

$st_date = date('Y-m-d',mktime(0,0,0,$date_arr[0],$date_arr[1],$date_arr[2]));

//check for child entries
if($sel_child_schedule_name)
{
	//get start and end date for child template
	$qq1=imw_query("select * from provider_schedule_tmp_child where pid=$tmp_record_id and status=1 order by id desc limit 1");
	if(imw_num_rows($qq1)>0){
		$dd1=imw_fetch_assoc($qq1);
		$remove_tmp['child_id']=$dd1['id'];
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
	//validate parent template applied length
	$qP=imw_query("select status from provider_schedule_tmp where status='no' and id=$tmp_record_id");
	$isOneDay=imw_num_rows($qP);
	//delete child template only in case of parent template being deleted for future
	if($isOneDay>0){
		imw_query("update provider_schedule_tmp_child set status=2,
				 deleted_by='$_SESSION[authId]',
				 deleted_on='".date('Y-m-d H:i:s')."' 
				 where pid=$tmp_record_id and status=1");
	}elseif($status == 'all' && $_REQUEST['Start_date'])
	{
		// if child start date greater than parent delete date
		if($remove_tmp['child_start_date']>=$st_date && $remove_tmp['child_id'])
		{
			imw_query("update provider_schedule_tmp_child set status=2,
				 deleted_by='$_SESSION[authId]',
				 deleted_on='".date('Y-m-d H:i:s')."' 
				 where id=".$remove_tmp['child_id']);
		}elseif($remove_tmp['child_start_date']< $st_date && $remove_tmp['child_end_date']> $st_date && $remove_tmp['child_id'])
		{
			//update child template end date
			imw_query("update provider_schedule_tmp_child set end_date ='$st_date'
				 where id=".$remove_tmp['child_id']);
		}
	}
	
	$child_arr=explode('<>',$sel_child_schedule_name);
}
$tmp_id = $sch_arr[0];
$pro_id = $sel_pro;
$fac_id = $sel_facility;

$tmp_id_str=$tmp_id;
//get parent template start and end time
$qq3=imw_query("select morning_start_time,morning_end_time from schedule_templates where id=$tmp_id");
if(imw_num_rows($qq3)>0){
	$dd3=imw_fetch_assoc($qq3);
	$remove_tmp['tmp_id']=$tmp_id;
	$remove_tmp['tmp_start_time']=$dd3['morning_start_time'];
	$remove_tmp['tmp_end_time']=$dd3['morning_end_time'];
}

//check for child template
if($pro_sch_tmp && $child_arr[0]){
	$tmp_id_str.=','.$child_arr[0];
}

//To Delete Single/Multiple Date Templates
if(is_array($Start_date_StringArray)){
	$wrdata_count_status =  count($Start_date_StringArray);
	for($i=0;$i<count($Start_date_StringArray);$i++){
		if($GLOBALDATEFORMAT == "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
		{list($cur_date_arr[1],$cur_date_arr[0],$cur_date_arr[2]) = explode('-',$Start_date_StringArray[$i]);}
		else
		{$cur_date_arr = explode('-',$Start_date_StringArray[$i]);}
		
		$weekdays = date('N',mktime(0,0,0,$cur_date_arr[0],$cur_date_arr[1],$cur_date_arr[2]));
		$week = getWeekCount($cur_date_arr[1]);		
		$last_day_t = trim($_REQUEST['last_day_t']);
		if($cur_date_arr[1]>$last_day_t){$cur_date_arr[1] = $last_day_t;}
		$task_Date = date('Y-m-d',mktime(0,0,0,$cur_date_arr[0],$cur_date_arr[1],$cur_date_arr[2]));
		$start_day = date('N',mktime(0,0,0,$cur_date_arr[0],1,$cur_date_arr[2]));
		
		$wrdata = trim($_REQUEST['wrdata']);
		if($wrdata!= "" && isset($wrdata) && $wrdata_count_status ==1)
		{				
			$wrdata_arr = explode('|',$wrdata);
			if(count($wrdata_arr) == 2)
			{
				$week = $wrdata_arr[0];
				$weekdays = $wrdata_arr[1];					
			}
		}		

		list($theyear,$themonth,$date) = explode('-',$task_Date);
		if($pro_id && $fac_id){
		$qry = "insert into provider_schedule_tmp set provider = $pro_id,facility = $fac_id
				,sch_tmp_id = $tmp_id,week$week  = '$weekdays',today_date = '$task_Date'
				,status = 'no',delete_row = '$status',del_status = 1";
		$qryId = imw_query($qry);	
		tmp_log('Deleted', 'Schedule removed', $pro_id, $fac_id, $tmp_id_str, $task_Date, "week$week /".$weekdays, $status);	
		}
		$where_time_str='';
		if(is_array($remove_tmp) && $remove_tmp['child_tmp_id'])
		{
			$where_time_str=" AND (IF((sa_app_start_date BETWEEN '$remove_tmp[child_start_date]' AND '$remove_tmp[child_end_date]'),(sa_app_starttime BETWEEN '$remove_tmp[child_start_time]' AND '$remove_tmp[child_end_time]'), (sa_app_starttime BETWEEN '$remove_tmp[tmp_start_time]' AND '$remove_tmp[tmp_end_time]')) OR sch_template_id IN ($tmp_id_str))";
			
		}elseif(is_array($remove_tmp) && $remove_tmp[tmp_start_time] && $remove_tmp[tmp_end_time])
		{
			//apply parent template start and end time
			$where_time_str=" AND ((sa_app_starttime BETWEEN '$remove_tmp[tmp_start_time]' AND '$remove_tmp[tmp_end_time]') OR sch_template_id IN ($tmp_id_str))";
		}
		
		if($status == "all"){
			//find all occurance for future and mark them delete
			$qGetAll=imw_query("select today_date from provider_schedule_tmp where provider = $pro_id AND facility = $fac_id AND sch_tmp_id = $tmp_id AND week$week  = '$weekdays' AND today_date > '$task_Date' AND del_status = 0");
			if(imw_num_rows($qGetAll)>0){
				while($allRec=imw_fetch_object($qGetAll))
				{
					//validate does this record have an delete record in response
					$qGetDel=imw_query("select id from provider_schedule_tmp where provider = $pro_id AND facility = $fac_id AND sch_tmp_id = $tmp_id AND week$week  = '$weekdays' AND today_date > '$allRec->today_date' AND delete_row = 'all',del_status = 1");
					if(imw_num_rows($qGetDel)==0)//add delete record for this record
					{
						if($pro_id && $fac_id){
						imw_query("insert into provider_schedule_tmp set provider = $pro_id,facility = $fac_id,
								sch_tmp_id = $tmp_id,week$week  = '$weekdays',today_date = '$allRec->today_date',
								status = 'no',delete_row = '$status',del_status = 1");
						}
					}
				}
			}
			
			$qryFinal = "SELECT id, sa_patient_id, sa_app_starttime, sa_app_start_date 
					FROM `schedule_appointments` 
					WHERE (sa_doctor_id = '".$pro_id."' and sa_facility_id = '".$fac_id."') 
					and (sa_app_start_date >= '".$task_Date."')		
					and CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
					AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekdays."' AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
					$where_time_str
					ORDER BY sa_app_start_date DESC ";
			//query to delete custom label entries
			$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
					AND facility= '".$fac_id."'
					AND (start_date >= '".$task_Date."')		
					AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
					AND DATE_FORMAT( start_date, '%w' ) = '".$weekdays."'
					AND temp_id IN ($tmp_id_str)";
			//query to delete block lock time slot entries
			$delBlockEntries="delete from block_times where provider= '".$pro_id."'
					AND facility= '".$fac_id."'
					AND (start_date >= '".$task_Date."')		
					AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
					AND DATE_FORMAT( start_date, '%w' ) = '".$weekdays."'
					AND temp_id IN ($tmp_id_str)";
		}else{
			$qryFinal = "Select id, sa_patient_id, sa_app_starttime from schedule_appointments where (sa_doctor_id='".$pro_id."' and sa_facility_id='".$fac_id."' 
				AND ('".$task_Date."' between sa_app_start_date and sa_app_end_date) )  
				AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
				$where_time_str";
			
			//query to delete custom label entries
			$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
				AND facility= '".$fac_id."'
				AND start_date ='".$task_Date."'
				AND temp_id IN ($tmp_id_str)";
			//query to delete block lock time slot entries
			$delBlockEntries="delete from block_times where provider= '".$pro_id."'
				AND facility= '".$fac_id."'
				AND start_date ='".$task_Date."'
				AND temp_id IN ($tmp_id_str)";
		}
		//delete schedule_custom_label and block_lock entries on basis of prov_id, fac_id, date, template id
		imw_query($delCustomEntries);
		imw_query($delBlockEntries);
		$re = imw_query($qryFinal);
		while($row = imw_fetch_array($re)){
			
			//logging this action in previous status table
			logApptChangedStatus($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Provider Schedule Deleted.", "", false);

			//updating schedule appointments details
			updateScheduleApptDetails($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Provider Schedule Deleted.", "", false);
		}
		//---- Delete Xml For Future -------
		$dir = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH').'/scheduler_common/load_xml';			
		$op = opendir($dir);
		while($file = readdir($op)){
			$extn = substr($file,-3);
			if(strtolower($extn) == 'sch'){
				$fileDate = explode('-',$file);
				$filePro = explode(".", $fileDate[3]);
				if($status == 'all'){
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]) >= strtotime($task_Date) && $filePro[0] == $pro_id){
						unlink($dir.'/'.$file);
						$cache_week=ceil($fileDate[2]/7);
						$cache_weekdays=date('w', strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]));
						if($week==$cache_week && $weekdays==$cache_weekdays)//delete entries without checking template id
						deleteCustomLabelAndBlockSlot($fileDate[0]."-".$fileDate[1]."-".$fileDate[2],$remove_tmp, $pro_id, $fac_id);
						unset($cache_week,$cache_weekdays);
					}
				}else{
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]) == strtotime($task_Date) && $filePro[0] == $pro_id){
						unlink($dir.'/'.$file);
						deleteCustomLabelAndBlockSlot($fileDate[0]."-".$fileDate[1]."-".$fileDate[2],$remove_tmp, $pro_id, $fac_id);
					}
				}
			}
		}
	}
}
header('location:provider_sch.php?sel_pro_month='.$pro_id.'&thedate='.$cur_date_arr[1].'&theyear='.$theyear.'&themonth='.$themonth);
?>