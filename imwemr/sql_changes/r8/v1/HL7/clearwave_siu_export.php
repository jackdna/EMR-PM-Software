<?php
$ignoreAuth = true;
set_time_limit(0);

require_once("../../../../config/globals.php");

if(constant('HL7_SIU_GENERATION')!=true){die('SIU generation not enabled.');}
$path = dirname(__FILE__)."/../../../../hl7sys/hl7GP/";
include_once($path."hl7FeedData.php");
set_time_limit(0);
$st = isset($_GET['st']) ? intval($_GET['st']) : 0;
$upto = 500;
//require_once("../../../../hl7sys/old/CLS_makeHL7.php");


$msg_info=array();

$q	=	"SELECT id,sa_patient_id,sa_patient_app_status_id,sa_app_start_date,sa_doctor_id FROM schedule_appointments 
		 WHERE sa_patient_app_status_id NOT IN (203,201,18,19,20,21,3,6,11) 
		 	AND sa_app_start_date >= '".date('Y-m-d')."' 
			AND sa_patient_id > 0 AND sa_doctor_id > 0 AND sa_facility_id > 0 
			ORDER BY id LIMIT $st,$upto";
$res = imw_query($q);//echo $q.'<hr>';
set_time_limit(0);
if($res && imw_num_rows($res)>0){
	$allowedEvents = $GLOBALS["HL7_SIU_EVENTS"];
//	$makeHL7		= new makeHL7;
	while($rs = imw_fetch_assoc($res)){
		$sa_sch_id 		= $rs['id'];
		$sa_patient_id 	= $rs['sa_patient_id'];
		$sa_status_id 	= $rs['sa_patient_app_status_id'];
		$sa_doctor_id	= $rs['sa_doctor_id'];
		//-----------SIU-----(start)--------------------
		$hl7 = new hl7FeedData();
		$hl7->filePath = $path;
		$hl7->PD['id'] = $sa_patient_id;
		$hl7->PD['schid'] = $sa_sch_id;
		
		$hl7->setTrigger("SIU", $sa_status_id);
		$hl7->addEVN($hl7->msgtypes['SIU']['trigger_event']);
		if( isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING']) )
		{
			$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
			$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
			$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
		}
		
		if( isset($GLOBALS['HL7_SIU_SEGMENTS']) && is_array($GLOBALS['HL7_SIU_SEGMENTS']) )
		{
			foreach( $GLOBALS['HL7_SIU_SEGMENTS'] as $segment )
			{
				$hl7->insertSegment($segment, 'SIU');
			}
		}
		
		$hl7->log_message("ATHENA_SIU");
		unset($hl7);
		//------------SIU---------(end)-----------------
		/*//------------ADT---------(start)---------------
		$makeHL7->patient_id = $sa_patient_id;
		$makeHL7->authId 	 = $sa_doctor_id;
		$makeHL7->log_HL7_message($sa_patient_id,'Update_Patient');*/
			
	}
	echo 'Process End';?>
    <script type="text/javascript">
	window.onload = function(){
		
		window.location.href = 'clearwave_siu_export.php?st=<?php echo ($st+$upto);?>'
		
	}
	</script>
    <?php
	
}else{
	$msg_info[] = 'NO FUTURE APPOINTMENT found for given conditions.';
}

?>