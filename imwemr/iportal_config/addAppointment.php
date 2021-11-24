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
File: addAppointment.php
Purpose: Add appointment in iDoc from Patient Portal
Access Type: Direct
*/

$ignoreAuth=true;
include_once("../config/globals.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	die("[Error]:401 Unauthorized Access ");
}

$apptData = json_decode($_POST['otherDT']);	/*Data Required for Adding Appointment*/
$physician = $apptData->physician;	/*Physician ID*/
$facility = $apptData->facility;	/*Facility Id*/
$apptDate = $apptData->appt_date;		/*Appointment ID*/
$procedureId = $apptData->procedure;		/*Selected Procedure Id*/
$procedure = $apptData->procedure_name;		/*Selected Procedure Id*/
$patientName = imw_real_escape_string($apptData->pt_name);		/*Patient Name*/
$patientId = $apptData->pt_id;		/*Patient Id*/


	$qryGetSP = "select spa.times as procTime, spb.id as procId from slot_procedures spa, slot_procedures spb 
				where (spb.id) = ('".$procedureId."') and spa.doctor_id = '0' and spb.proc_time = spa.id
				 ORDER BY spb.id ASC LIMIT 1";
	$rsGetSP = imw_query($qryGetSP);		
	if(imw_num_rows($rsGetSP) > 0){
		$rowGetSP = imw_fetch_array($rsGetSP);
		$intAppProcedure = (int)$rowGetSP['procId'];
		$intAppTimeDuration = (int)$rowGetSP['procTime'];
		$intAppTimeDuration_set = "00:".(int)$rowGetSP['procTime'].":00";
		$appDuration = $intAppTimeDuration * 60;
	}


$otherData = explode("~~~",$apptData->timeSlot);
$apptStart =  strtotime($apptData->timeSlot);/*Appointment Start time*/
//$apptEnd = $otherData[1];		/*Appointment End time*/
$apptEnd   = (int)$appDuration+(int)$apptStart;
//$apptDur = $otherData[2];		/*Appointment/Selected Procedure Duration*/
$apptDur = $appDuration;		/*Appointment/Selected Procedure Duration*/
$templd = $apptData->temp_id;		/*Template Id*/
unset($apptData);	/*Unset Because this variable is not more required*/
//echo $physician." && ". $facility." && ".$apptDate." && ".$procedureId." && ".$procedure." && ".$apptStart." && ".$apptEnd." && ". $apptDur." && ".$templd;die();
if($physician!="" && $facility!="" && $apptDate!="" && $procedureId!="" && $procedure!="" && $apptStart!="" && $apptEnd!="" && $apptDur!="" && $templd!=""){
	
	$cDate = date("Y-m-d",time());
	$cTime = date("H:i:s",time());
	
	$apptDate = str_replace("_", "-", $apptDate);
	
	//$apptStart = strtotime($apptStart);
	//$apptEnd = strtotime($apptEnd);
	
	/*Search for Already Applied Label In the selected Time Slot*/
	$sql = "SELECT `schedule_label_id`, `template_label`, `label_type`, `label_color`,
			TIME_FORMAT(`start_time`,'%H:%i:%s') AS 'start_time',
			TIME_FORMAT(`end_time`,'%H:%i:%s') AS 'end_time'
			FROM `schedule_label_tbl`
			WHERE `sch_template_id`='".$templd."'
			AND `start_time`>='".date("H:i", $apptStart)."'
			AND `end_time`<='".date("H:i", $apptEnd)."'
			AND `del_status`=''";
	$sql = imw_query($sql);
	$replaceLabels = array();
	if($sql && imw_num_rows($sql)>0){
		print "";
		while($row = imw_fetch_assoc($sql)){
			$label = $row['template_label'];
			$labels = explode("; ",$label);
			$labels1 = $labels;
			
			$lblId = $row['schedule_label_id'];
			
			/*Find Selected Procedure name in Lables*/
			foreach($labels as $key=>$lbl){
				if($lbl==$procedure){
					unset($labels1[$key]);
					krsort($labels1);
					
					$replaceLabels[$lblId]['type'] = $row['label_type'];
					$replaceLabels[$lblId]['text'] = $label;
					$replaceLabels[$lblId]['show_text'] = implode("; ", $labels1);
					
					$replaceLabels[$lblId]['type'] = $row['label_type'];
					$replaceLabels[$lblId]['color'] = $row['label_color'];
					$replaceLabels[$lblId]['start'] = $row['start_time'];
					$replaceLabels[$lblId]['end'] = $row['end_time'];
					
				}
			}
		}
	}
	imw_free_result($sql);
	
	/*`case_type_id` needs to be fixed Insurance Id*/
	/*Add Appointment*/
	$sql = "INSERT INTO `schedule_appointments` SET 
			`sa_doctor_id`='".$physician."',
			`sa_patient_id`='".$patientId."',
			`sa_patient_name`='".$patientName."',
			`sa_patient_app_status_id`='0',
			`sa_comments`='Appointment Added From Patient Portal',
			`sa_app_time`='".$cDate." ".$cTime."',
			`sa_app_starttime`='".date("H:i:s", $apptStart)."',
			`sa_app_endtime`='".date("H:i:s", $apptEnd)."',
			`sa_app_duration`='".$apptDur."',
			`sa_facility_id`='".$facility."',
			`sa_app_start_date`='".$apptDate."',
			`sa_app_end_date`='".$apptDate."',
			`procedureid`='".$procedureId."',
			`case_type_id`='',
			`sa_madeby`='admin',
			`status_update_operator_id`='1',
			`RoutineExam`='Yes',
			`sch_template_id`='".$templd."'";
	$apptId = false;
	$sql = imw_query($sql);
	if($sql){
		$apptId = imw_insert_id();
	}
	
	/*Add record in Previous Status Table*/
	if($apptId){
		$sql1 = "INSERT INTO `previous_status` SET 
				`sch_id`='".$apptId."',
				`patient_id`='".$patientId."',
				`status_time`='".$cTime."',
				`status_date`='".$cDate."',
				`status`='0',
				`old_date`='".$apptDate."',
				`old_time`='".date("H:i:s", $apptStart)."',
				`old_provider`='".$physician."',
				`old_facility`='".$facility."',
				`statusComments`='Appointment Added From Patient Portal',
				`oldMadeBy`='admin',
				`statusChangedBy`='admin',
				`dateTime`='".$cDate." ".$cTime."',
				`new_facility`='".$facility."',
				`new_provider`='".$physician."',
				`old_status`='0',
				`new_appt_date`='".$apptData."',
				`new_appt_start_time`='".date("H:i:s", $apptStart)."',
				`old_appt_end_time`='".date("H:i:s", $apptEnd)."',
				`new_appt_end_time`='".date("H:i:s", $apptEnd)."',
				`old_procedure_id`='".$procedureId."',
				`new_procedure_id`='".$procedureId."'";
		imw_query($sql1);
		
		if(count($replaceLabels)>0){
			$labelReplaced = "::".$apptId.":".$procedure;
			foreach($replaceLabels as $lbl){
				$sql2 = "INSERT INTO `scheduler_custom_labels` SET 
						`provider`='".$physician."',
						`facility`='".$facility."',
						`start_date`='".$apptDate."',
						`start_time`='".$lbl['start']."',
						`end_time`='".$lbl['end']."',
						`l_type`='".$lbl['type']."',
						`l_text`='".$lbl['text']."',
						`l_show_text`='".$lbl['show_text']."',
						`l_color`='".$lbl['color']."',
						`time_status`='".$cDate." ".$cTime."',
						`system_action`='1',
						`labels_replaced`='".$labelReplaced."'";
				//condition added to stop garbage value
				if($lbl['type']=='Procedure' || $lbl['type']=='Information' || $lbl['type']=='Lunch' || $lbl['type']=='Reserved')
				{
					imw_query($sql2);
				}else{custom_lbl_log('iportal_config\addappointment.php');}
			}
				
		}
	}
	
	if($apptId){
		print json_encode(array('status'=>'success'));
	}
	else{
		print json_encode(array('status'=>'failed'));
	}
}
else{
	die("[Error]:400 Invalid Request");
}
?>