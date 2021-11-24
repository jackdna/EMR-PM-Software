<?php
/*****SET BACKLOG DATA START DATE & END DATE HERE**/
$begin_date = '2016-07-01';	//yyyy-mm-dd
$end_date = date('Y-m-d'); //yyyy-mm-dd
/******DO NOT EDIT THE CODE BELOW*******/



//This script creates ADT messages for unique patient's appointments during a given date range.





$ignoreAuth = true;
set_time_limit(0);
require_once("../../../../config/globals.php");
if(constant('HL7_ADT_GENERATION_OLD')!=true){die('ADT Generation not enabled.');}
require_once("../../../../hl7sys/old/CLS_makeHL7.php");
set_time_limit(0);
$st = isset($_GET['st']) ? intval($_GET['st']) : 0;
$upto = 1000;

$msg_info=array();

//$q	=	"SELECT id FROM patient_data WHERE patientStatus = 'Active'	ORDER BY id LIMIT $st,$upto";
$q	=	"SELECT DISTINCT(sa_patient_id) as id FROM schedule_appointments 
		 WHERE sa_patient_app_status_id NOT IN (203,201,18,19,20,21,3,6) 
		 	AND sa_app_start_date >= '$begin_date' AND sa_app_end_date <= '$end_date' 
			AND sa_patient_id > 0 AND sa_doctor_id > 0 AND sa_facility_id > 0 
			ORDER BY id LIMIT $st,$upto";
$res = imw_query($q);

$rs_opr = false;
$q_opr = "SELECT id,username FROM users WHERE LOWER(fname) = 'hl7' AND LOWER(lname) = 'hl7' LIMIT 0,1";
$res_opr = imw_query($q_opr);
if($res_opr && imw_num_rows($res_opr)==1){
	$rs_opr = imw_fetch_assoc($res_opr);
//	 $this->operator_id = $rs['id'];
//	 $this->operator_username = $rs['username'];
}
if($res && imw_num_rows($res)>0){
	$makeHL7		= new makeHL7;
	while($rs = imw_fetch_assoc($res)){
		$pat_id 		= $rs['id'];
		//------------ADT---------(start)---------------
		$makeHL7->patient_id = $pat_id;
		if($rs_opr){
			$makeHL7->authId 	 = $rs_opr['id'];
		}else{
			$makeHL7->authId 	 = 1;
		}
		$makeHL7->log_HL7_message($pat_id,'Update_Patient');
			
	}
	?>Script is running. Do not close.
    <script type="text/javascript">window.onload = function(){window.location.href = 'clearwave_adt_export.php?st=<?php echo ($st+$upto);?>';}</script>
    <?php
}else{
	echo 'NO record found for given conditions OR Process completed. You may close this script.';
}
?>