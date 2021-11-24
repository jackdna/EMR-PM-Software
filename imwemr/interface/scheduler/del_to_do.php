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
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_label_functions.php');
//scheduler object
$obj_scheduler = new appt_scheduler();
$obj_label=new appt_label();
$restore_act = '';
$erp_error=array();
if(isERPPortalEnabled()) {
	try {
		include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
		$obj_appointments = new Appointments;
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
}
		
//== Function To collect all providers schedule for restore appointment dates ====///
function get_chache_file_provider_schedule($arr_file_name){
	
	if(!$dir_path)$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/";
	$main_array_return=array();
	if(is_array($arr_file_name) && count($arr_file_name)>0){
		foreach($arr_file_name as $sch_file_name){
			$sch_file=$dir_path.'load_xml/'.$sch_file_name;
			$provider_id=str_replace(".sch","",end(explode("-",$sch_file_name)));
			$appt_date=$appt_time=$sch_file_content="";$sch_tmp_content=array();
			if(file_exists($sch_file)){
				$sch_file_content=file_get_contents($sch_file);
				$sch_tmp_content=unserialize($sch_file_content);
				if(is_array($sch_tmp_content)){//pre($sch_tmp_content);
					$appt_date=$sch_tmp_content["dt"];
					$sch_tmp_slot=$sch_tmp_content[$provider_id]['slots'];
					foreach($sch_tmp_slot as $arr_val){
						list($appt_start_time,$appt_end_time)=explode("-",$arr_val["id"]);
						$fac_id=$arr_val["fac_id"];
						$tempalte_id=$arr_val["tmpId"];
						$main_array_return[$provider_id][$fac_id][$appt_date][$appt_start_time.":00"]=$tempalte_id;
					}
				}
			}
		}
	}
	return $main_array_return;
}
if($_REQUEST["hidAction"] == "del"){

	$toDoIdArr = explode(',',$_REQUEST["to_do_id"]);
	$sch_ids= array_combine($toDoIdArr,$toDoIdArr);
	if(count($sch_ids)>0)
	{
		$appt_status=201;
		if($_REQUEST['todo_avai']=='1'){
			$appt_status=271;
		}
		foreach($sch_ids as $cur_sch_id)
		{
			$obj_scheduler->logApptChangedStatus($cur_sch_id,'','','',203,'','',$_SESSION["authUser"],'','',false);	
			//updating schedule appointments details
            $obj_scheduler->updateScheduleApptDetails($cur_sch_id, "", "", "", 203, "", "", $_SESSION['authUser'], "", "", false);
		}
			
		$sch_ids_string = implode(',',$sch_ids);
		$del_status = imw_query("UPDATE schedule_appointments SET sa_patient_app_show=0 WHERE id IN(".$sch_ids_string.") and sa_patient_app_status_id = ".$appt_status);
		imw_query("delete from schedule_first_avail where sch_id IN (".$sch_ids_string.")");

        
        if($del_status) {
            foreach($sch_ids as $cur_sch_id)
            {
                $params=array();
                $params['section']='appointment';
                $params['sub_section']='appt_deleted'; //appt_canceled,appt_created,appt_deleted,appt_no_show,appt_reschedule
                $params['obj_value']=trim($cur_sch_id);
                $serialized_arr = serialize($params);
                include("../../interface/common/assign_new_task.php");

				if(isERPPortalEnabled()) {
					try {
						$obj_appointments->deleteAppointments($cur_sch_id);
					} catch(Exception $e) {
						$erp_error[]='Unable to connect to ERP Portal';
					}
				}
            }
        }
	}
	
	//deleting first availble direct entries 
	$strToDoFAId = $_REQUEST["to_do_fa_id"];
	if($strToDoFAId)imw_query("delete from schedule_first_avail where id IN (".$strToDoFAId.")");
	//$del_status='&deleted=success';
	$_SESSION['deleted']='success';
}
elseif($_REQUEST["hidAction"] == "cancel"){

	$toDoIdArr = explode(',',$_REQUEST["to_do_id"]);
	$sch_ids= array_combine($toDoIdArr,$toDoIdArr);
	if(count($sch_ids)>0)
	{
		$appt_status=201;
		if($_REQUEST['todo_avai']=='1'){
			$appt_status=271;
		}
		foreach($sch_ids as $cur_sch_id)
		{
			//update previous status table
			$obj_scheduler->logApptChangedStatus($cur_sch_id,'','','',18,'','',$_SESSION['authUser'], $_REQUEST['reason'], "", false);	
			//updating schedule appointments details
            $obj_scheduler->updateScheduleApptDetails($cur_sch_id, "", "", "", 18, "", "", $_SESSION['authUser'], "", "", false);
		}
			
		$sch_ids_string = implode(',',$sch_ids);
		$del_status = imw_query("UPDATE schedule_appointments SET sa_patient_app_show=0 WHERE id IN(".$sch_ids_string.") and sa_patient_app_status_id = ".$appt_status);
		imw_query("delete from schedule_first_avail where sch_id IN (".$sch_ids_string.")");

        
        if($del_status) {
            foreach($sch_ids as $cur_sch_id)
            {
                $params=array();
                $params['section']='appointment';
                $params['sub_section']='appt_deleted'; //appt_canceled,appt_created,appt_deleted,appt_no_show,appt_reschedule
                $params['obj_value']=trim($cur_sch_id);
                $serialized_arr = serialize($params);
                include("../../interface/common/assign_new_task.php");

				if(isERPPortalEnabled()) {
					//$obj_appointments->deleteAppointments($cur_sch_id);
					try {
						$obj_appointments->addUpdateAppointments($cur_sch_id,"",$_REQUEST['reason']);
					} catch(Exception $e) {
						$erp_error[]='Unable to connect to ERP Portal';
					}
				}
            }
        }
	}
	//$del_status='&cancel=success';
	$_SESSION['cancel']='success';
}
elseif($_REQUEST["hidAction"] == "restore"){
	$strAppId = $_REQUEST["hidShcId"];
	$arrAppId = $arr_sch_file=array();
	$arrAppId = explode(",", $strAppId);
	//===The following qry will give us result name of sch file accroding to provider schedule date===//
	$qry_select_sch = "select concat(sa_app_start_date,'-',sa_doctor_id,'.sch') as sch_file_name from schedule_appointments where id IN(".$strAppId." ) group by sa_doctor_id,sa_app_start_date";	
	$res_select_sch=imw_query($qry_select_sch);
	while($row_select_sch=imw_fetch_assoc($res_select_sch)){
		$arr_sch_file[]=$row_select_sch["sch_file_name"];
	}
	//==To collect all providers schedule for restore appointment dates ====///
	$providers_all_schedule=get_chache_file_provider_schedule($arr_sch_file);
	//===========================================================================
$appt_restored=$appt_not_restored="0";$url_done_status="";
	foreach($arrAppId as $intKey => $intVal){
		$intAppId = $intVal;
		$qrySelectSch = "select sa_patient_id, sa_app_start_date, sa_app_starttime, sa_doctor_id, sa_facility_id, sa_madeby, sa_patient_app_status_id, 
		sa_app_endtime, procedureid, sa_patient_app_show from schedule_appointments where id = '".$intAppId."'";					
		$rsSelectSch = imw_query($qrySelectSch);
		if(imw_num_rows($rsSelectSch) > 0){
			$ptID = $proID = $facId = $appStuId = $appProcId = 0;
			$appDate = $appTime = $appMadeBy = $appEndTime = "";
			
			$rowSelectSch = imw_fetch_array($rsSelectSch);
			
			$ptID = $rowSelectSch["sa_patient_id"];
			$appDate = $rowSelectSch["sa_app_start_date"];
			$appTime = $rowSelectSch["sa_app_starttime"];
			$proID = $rowSelectSch["sa_doctor_id"];
			$facId = $rowSelectSch["sa_facility_id"];
			$appMadeBy = $rowSelectSch["sa_madeby"];
			$appStuId = $rowSelectSch["sa_patient_app_status_id"];
			$sa_patient_app_show = $rowSelectSch["sa_patient_app_show"];
			$appEndTime = $rowSelectSch["sa_app_endtime"];
			$appProcId = $rowSelectSch["procedureid"];
			if($providers_all_schedule[$proID][$facId][$appDate][$appTime]){//=To check provider schedule exist for that date time=//
				$sch_template_id=$providers_all_schedule[$proID][$facId][$appDate][$appTime];
				if(defined('SCHEDULER_RESTORE_ON_BLOCK') && constant('SCHEDULER_RESTORE_ON_BLOCK')==1)
				{
					//validate this time against blocked
					$q=imw_query("select * from block_times where provider='$proID' and facility='$facId' and start_date='$appDate' AND IF( temp_id, temp_id =$sch_template_id, temp_id =0 ) and ('$appTime' between start_time and end_time)");
					if(imw_num_rows($q)>=1)continue;
				}
				$appt_restored++;
				$qryInsertAppPre = "INSERT INTO previous_status 
				(sch_id, patient_id, status_time, status_date, status, old_date, old_time, old_provider, old_facility, oldMadeBy, statusChangedBy,
				dateTime, new_facility, new_provider, old_status, new_appt_date, new_appt_start_time, old_appt_end_time, new_appt_end_time, 
				old_procedure_id, new_procedure_id) 
				values ('".$intAppId."','".$ptID."','".date("H:i:s")."','".date("Y-m-d")."','0','".$appDate."','".$appTime."','".$proID."','".$facId."',
				'".$appMadeBy."','".$_SESSION["authUser"]."','".date("Y-m-d H:i:s")."','".$facId."',	'".$proID."','".$appStuId."','".$appDate."','".$appTime."',
				'".$appEndTime."','".$appEndTime."','".$appProcId."','".$appProcId."')";
				$rsInsertAppPre = imw_query($qryInsertAppPre);	
				imw_free_result($rsSelectSch);	
				$qryUpSch = "update schedule_appointments set sa_patient_app_status_id = '0',sch_template_id='".$sch_template_id."', sa_patient_app_show=0 where id = '".$intAppId."'";
				$rsUpSch = imw_query($qryUpSch);
				
				//replace label if any
				if($appStuId==271 && $sa_patient_app_show==0){/*do nothing as appt alraedy on schedule*/}
				else{$obj_label->replaceLabel($appTime, $appEndTime, $appDate, $proID, $facId, $intAppId, $sch_template_id);}
					
				imw_query("delete from schedule_first_avail where sch_id = $intAppId"); 
				//$restore_act = '&restore_act=restore_true';
				$_SESSION['restore_act']='restore_true';
				
				/* MVE PORTAL CREATE NEW APPOINTMENT STARTS HERE*/
				if(isERPPortalEnabled()) {
					try {
						$appt_act_reason = "";
						$obj_appointments->addUpdateAppointments($intAppId,$ptID,$appt_act_reason);
					} catch(Exception $e) {
						$erp_error[]='Unable to connect to ERP Portal';
					}
				}
				/* MVE PORTAL CREATE NEW APPOINTMENT ENDS HERE*/
				
			}else{
				$appt_not_restored++;	
			}
		}else{$appt_not_restored++;}
	}
	if(!$restore_act)
	{
		//$restore_act = '&restore_act=failed';
		$_SESSION['restore_act']='failed';
	}
	$url_done_status="&restored=".$appt_restored."&not_restored=".$appt_not_restored;
}
if($_REQUEST['todo_avai']=='1'){
	header('location: to_do_first_avai.php?reschedule=1'.$restore_act.$url_done_status.$del_status);
}else{
header('location: to_do.php?reschedule=1'.$restore_act.$url_done_status.$del_status);
}
?>