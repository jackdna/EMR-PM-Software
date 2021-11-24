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

//get parent schedule detail
$query=imw_query("select * from provider_schedule_tmp where id =$tmp_record_id");
$parent_data=imw_fetch_object($query);

$GLOBALDATEFORMAT = $GLOBALS['date_format'];
$status = 'all';
$sch_id = $child_temp_id;
$pro_id = $parent_data->provider;
$fac_id = $parent_data->facility;
$Start_date_StringArray[]=$dated;
//To Delete Single/Multiple Date Templates
if(is_array($Start_date_StringArray)){
	$wrdata_count_status =  count($Start_date_StringArray);
	for($i=0;$i<count($Start_date_StringArray);$i++){
		$cur_date_arr = explode('-',$Start_date_StringArray[$i]);
		
		$weekdays = date('N',mktime(0,0,0,$cur_date_arr[1],$cur_date_arr[2],$cur_date_arr[0]));
		$week = getWeekCount($cur_date_arr[1]);		
		$last_day_t = trim($_REQUEST['last_day_t']);
		//if($cur_date_arr[1]>$last_day_t){$cur_date_arr[1] = $last_day_t;}
		$task_Date = date('Y-m-d',mktime(0,0,0,$cur_date_arr[1],$cur_date_arr[2],$cur_date_arr[0]));
		$start_day = date('N',mktime(0,0,0,$cur_date_arr[1],1,$cur_date_arr[0]));
		list($theyear,$themonth,$date) = explode('-',$task_Date);
		$qryData = imw_query("select * from provider_schedule_tmp_child where pid=$_REQUEST[tmp_record_id] and sch_tmp_id=$_REQUEST[child_temp_id] and status=1");
		$resData = imw_fetch_object($qryData);
		
		$qry = "update provider_schedule_tmp_child set status=2,
					 deleted_by='$_SESSION[authId]',
					 deleted_on='".date('Y-m-d H:i:s')."' 
					 where pid=$_REQUEST[tmp_record_id] and sch_tmp_id=$_REQUEST[child_temp_id] and status=1";
		
		$qryId = imw_query($qry);	
		tmp_log('Deleted', 'Provider schedule deleted', $pro_id, $fac_id, $sch_id, $task_Date, "week$week /".$weekdays, $status);	
		$sch_id_str=$sch_id;
					
		if($status == "all"){
			$qryFinal = "SELECT id, sa_patient_id, sa_app_starttime, sa_app_start_date 
					FROM `schedule_appointments` 
					WHERE (sa_doctor_id = '".$pro_id."' and sa_facility_id = '".$fac_id."') 
					and (sa_app_start_date BETWEEN '".$resData->start_date."' and '".$resData->end_date."')		
					and CEIL( SUBSTRING( sa_app_start_date, 9, 2 ) /7 ) = '".$week."' 
					AND DATE_FORMAT( sa_app_start_date, '%w' ) = '".$weekdays."' AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
					AND sch_template_id IN ($sch_id_str)
					ORDER BY sa_app_start_date DESC ";
			//query to delete custom label entries
			$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
					AND facility= '".$fac_id."'
					AND (start_date BETWEEN '".$resData->start_date."' and '".$resData->end_date."')	
					AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
					AND DATE_FORMAT( start_date, '%w' ) = '".$weekdays."'
					AND temp_id IN ($sch_id_str)";
			//query to delete block lock time slot entries
			$delBlockEntries="delete from block_times where provider= '".$pro_id."'
					AND facility= '".$fac_id."'
					AND (start_date BETWEEN '".$resData->start_date."' and '".$resData->end_date."')
					AND CEIL( SUBSTRING( start_date, 9, 2 ) /7 ) = '".$week."' 
					AND DATE_FORMAT( start_date, '%w' ) = '".$weekdays."'
					AND temp_id IN ($sch_id_str)";
		}else{
			$qryFinal = "Select id, sa_patient_id, sa_app_starttime from schedule_appointments where (sa_doctor_id='".$pro_id."' and sa_facility_id='".$fac_id."' 
				AND (sa_app_start_date BETWEEN '".$resData->start_date."' and '".$resData->end_date."'))  
				AND sa_patient_app_status_id NOT IN (203,201,18,19,20)
				AND sch_template_id IN ($sch_id_str) ";
			
			//query to delete custom label entries
			$delCustomEntries="delete from scheduler_custom_labels where provider= '".$pro_id."'
				AND facility= '".$fac_id."'
				AND start_date ='".$task_Date."'
				AND temp_id IN ($sch_id_str)";
			//query to delete block lock time slot entries
			$delBlockEntries="delete from block_times where provider= '".$pro_id."'
				AND facility= '".$fac_id."'
				AND start_date ='".$task_Date."'
				AND temp_id IN ($sch_id_str)";
		}
		//delete schedule_custom_label and block_lock entries on basis of prov_id, fac_id, date, template id
		mysql_query($delCustomEntries);
		mysql_query($delBlockEntries);
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
					$fileDT=strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]);
					if(($fileDT >= strtotime($resData->start_date) && $fileDT <= strtotime($resData->end_date)) && $filePro[0] == $pro_id){
						unlink($dir.'/'.$file);
						$cache_week=ceil($fileDate[2]/7);
						$cache_weekdays=date('w', strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]));
						if($week=$cache_week && $weekdays==$cache_weekdays)//delete entries without checking template id
						deleteCustomLabelAndBlockSlot($fileDate[0]."-".$fileDate[1]."-".$fileDate[2],$sch_id, $pro_id, $fac_id);
						unset($cache_week,$cache_weekdays);
					}
				}else{
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2]) == strtotime($task_Date) && $filePro[0] == $pro_id){
						//echo $dir.'/'.$file;
						unlink($dir.'/'.$file);
						deleteCustomLabelAndBlockSlot($fileDate[0]."-".$fileDate[1]."-".$fileDate[2],$sch_id, $pro_id, $fac_id);
					}
				}
			}
		}
	}
}
echo 'Done';
?>