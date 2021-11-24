<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

$objManageData = new manageData;
	$patient_id = $_REQUEST['patient_id'];
	$pConfId 	= $_REQUEST['pConfId'];
	if(!$patient_id) {
		$patient_id = $_SESSION['patient_id'];
	}
	if(!$pConfId) {
		$pConfId	= $_SESSION['pConfId'];
	}	
	//$timeId 	= $_REQUEST['timeId'];
	$timeId 	= $objManageData->setTmFormat($_REQUEST['timeId'],'static');
	$autoIncrId = $_REQUEST['autoIncrId'];
	$fieldName 	= $_REQUEST['fieldName'];
	
	
	$updatePatientPreOpMedicationQry = "update patientpreopmedication_tbl set $fieldName = '$timeId' where patientPreOpMediId = '$autoIncrId'"; 
	$updatePatientPreOpMedicationRes = imw_query($updatePatientPreOpMedicationQry) or die(imw_error());
?>