<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
unset($conditionArr);
list($mm,$dd,$yy)=explode("-",$_REQUEST["patient_dob"]);
$pat_dob=$yy."-".$mm."-".$dd;
$conditionArr['patient_fname'] = addslashes($_REQUEST["patient_fname"]);
$conditionArr['patient_lname'] = addslashes($_REQUEST["patient_lname"]);
$conditionArr['date_of_birth'] = addslashes($pat_dob);
$conditionArr['zip'] 		   = addslashes($_REQUEST["patient_zip"]);

$chkPatientDataTblDetails = $objManageData->getMultiChkArrayRecords('patient_data_tbl', $conditionArr,'patient_id','ASC');
if($chkPatientDataTblDetails) { //IN CASE TO ADD PATIENT INFO
	foreach($chkPatientDataTblDetails as $patientDataTblDetails){
		$patient_id = $patientDataTblDetails->patient_id;
	}
}
if($patient_id){
	$qryGetPatientWaitingTbl="SELECT patient_in_waiting_id FROM patient_in_waiting_tbl where patient_status='Canceled' and patient_id='".$patient_id."' ORDER by dos DESC LIMIT 1";
	$resGetPatientWaitingTbl=imw_query($qryGetPatientWaitingTbl) or die(imw_error());
	$rowGetPatientWaitingTbl=imw_fetch_assoc($resGetPatientWaitingTbl);
	echo $patientWaitingId=$rowGetPatientWaitingTbl['patient_in_waiting_id'];		
}
?>