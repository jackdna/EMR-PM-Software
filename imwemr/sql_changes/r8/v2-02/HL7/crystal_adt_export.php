<?php
/*****SET BACKLOG DATA START DATE & END DATE HERE**/
$begin_date = date('Y-m-01');	//yyyy-mm-dd
$end_date   = date('Y-m-30');
/******DO NOT EDIT THE CODE BELOW*******/



//This script creates ADT messages for unique patient's appointments during a given date range.





$ignoreAuth = true;
set_time_limit(0);
require_once(dirname(__FILE__)."/../../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../../hl7sys/api/class.HL7Engine.php");
set_time_limit(0);

$st = isset($_GET['st']) ? intval($_GET['st']) : 0;
$upto = 1000;

$msg_info=array();

//$q	=	"SELECT id FROM patient_data WHERE patientStatus = 'Active'	ORDER BY id LIMIT $st,$upto";
$q	=	"SELECT DISTINCT(sa_patient_id) as pat_id FROM schedule_appointments 
		 WHERE sa_patient_app_status_id NOT IN (203,201,18,19,20,21,3,6) 
		 	AND sa_app_start_date >= '$begin_date' AND sa_app_start_date < '$end_date' 
			AND sa_patient_id > 0 AND sa_doctor_id > 0 AND sa_facility_id > 0 
			ORDER BY id LIMIT $st,$upto";
$res = imw_query($q);

$rs_opr = false;
$q_opr = "SELECT id,username FROM users WHERE LOWER(fname) = 'hl7' AND LOWER(lname) = 'hl7' LIMIT 0,1";
$res_opr = imw_query($q_opr);
if($res_opr && imw_num_rows($res_opr)==1){
	$rs_opr = imw_fetch_assoc($res_opr);
}

if($res && imw_num_rows($res)>0){
	
	while($rs = imw_fetch_assoc($res)){
		$pat_id 		= $rs['pat_id'];
		/*********NEW HL7 ENGINE START************/
		$objHL7Engine = new HL7Engine();
		$objHL7Engine->application_module = 'demographics';
		$objHL7Engine->msgSubType = 'update_patient';
		$objHL7Engine->source_id = $pat_id;
		$objHL7Engine->generateHL7();
		unset($objHL7Engine);
		/*********NEW HL7 ENGINE END*************/
			
	}
	?>Script is running. Do not close.
    <script type="text/javascript">window.onload = function(){window.location.href = 'crystal_adt_export.php?st=<?php echo ($st+$upto);?>';}</script>
    <?php
}else{
	echo 'NO record found for given conditions OR Process completed. You may close this script.';
}
?>