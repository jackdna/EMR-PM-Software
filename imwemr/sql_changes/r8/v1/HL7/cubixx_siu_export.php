<?php
$ignoreAuth = true;
set_time_limit(0);

require_once("../../../../config/globals.php");

if(constant('HL7_SIU_GENERATION')!=true){die('SIU generation not enabled.');}
$path = dirname(__FILE__)."/../../../../hl7sys/hl7GP/";
include_once($path."hl7FeedData.php");

$msg_info=array();

//$excluded_status = "203,201,18,19,20,21,3,6";
$q	=	"SELECT id,sa_patient_id,sa_patient_app_status_id,sa_app_start_date,sa_doctor_id FROM schedule_appointments 
		 WHERE sa_patient_app_status_id NOT IN (203,201,18,19,20,21,3,6) 
		 	AND sa_app_start_date >= '2019-05-06' AND sa_app_start_date <= '2019-05-13' 
			AND sa_patient_id > 0 AND sa_doctor_id = '111' AND sa_facility_id > 0 
			ORDER BY id";
$res = imw_query($q);
set_time_limit(0);
if($res && imw_num_rows($res)>0){
	while($rs = imw_fetch_assoc($res)){
		$sa_sch_id 		= $rs['id'];
		$sa_patient_id 	= $rs['sa_patient_id'];
		$sa_status_id 	= 0;//$rs['sa_patient_app_status_id'];
		$sa_doctor_id	= $rs['sa_doctor_id'];
		//-----------SIU-----(start)--------------------
		$hl7 = new hl7FeedData();
		$hl7->filePath = $path;
		$hl7->PD['id'] = $sa_patient_id;
		$hl7->PD['schid'] = $sa_sch_id;
		$hl7->setTrigger("SIU", $sa_status_id);
		if(isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING'])){
			$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
			$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
			$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
		}
		$hl7->addEVN($hl7->msgtypes['SIU']['trigger_event']);
		if(isset($GLOBALS['HL7_SIU_SEGMENTS']) && is_array($GLOBALS['HL7_SIU_SEGMENTS'])){
			foreach($GLOBALS['HL7_SIU_SEGMENTS'] as $segment){
				$hl7->insertSegment($segment, 'SIU');
			}
		}
		$hl7->log_message();
		unset($hl7);
		//die;
		//------------SIU---------(end)-----------------
	}
	echo 'Process End';
}else{
	$msg_info[] = 'NO FUTURE APPOINTMENT found for given conditions.';
}



?>