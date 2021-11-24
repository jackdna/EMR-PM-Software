<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
	
$template_id = $_REQUEST['template_id'];
$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];

//START GET VOCABULARY OF ASC
$ascInfoArr = $objManageData->getASCInfo($_SESSION["facility"]);
//END GET VOCABULARY OF ASC

//GET PATIENT DETAIL
$Operative_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
$Operative_patientName_tblRes = imw_query($Operative_patientName_tblQry) or die(imw_error());
$Operative_patientName_tblRow = imw_fetch_array($Operative_patientName_tblRes);
	
	$Operative_patientName = $Operative_patientName_tblRow["patient_fname"]." ".$Operative_patientName_tblRow["patient_mname"]." ".$Operative_patientName_tblRow["patient_lname"];
	$Operative_patientNameDobTemp = $Operative_patientName_tblRow["date_of_birth"];
		$Operative_patientNameDob_split = explode("-",$Operative_patientNameDobTemp);
		$Operative_patientNameDob = $Operative_patientNameDob_split[1]."-".$Operative_patientNameDob_split[2]."-".$Operative_patientNameDob_split[0];

	$Operative_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$pConfId."'";
	$Operative_patientConfirm_tblRes = imw_query($Operative_patientConfirm_tblQry) or die(imw_error());
	$Operative_patientConfirm_tblRow = imw_fetch_array($Operative_patientConfirm_tblRes);
	$Operative_patientConfirmDosTemp = $Operative_patientConfirm_tblRow["dos"];
		$Operative_patientConfirmDos_split = explode("-",$Operative_patientConfirmDosTemp);
		$Operative_patientConfirmDos = $Operative_patientConfirmDos_split[1]."-".$Operative_patientConfirmDos_split[2]."-".$Operative_patientConfirmDos_split[0];

	$Operative_patientConfirmSiteTemp = $Operative_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE 
		if($Operative_patientConfirmSiteTemp == 1) {
			$Operative_patientConfirmSite = "Left Eye";  //OD
		}else if($Operative_patientConfirmSiteTemp == 2) {
			$Operative_patientConfirmSite = "Right Eye";  //OS
		}else if($Operative_patientConfirmSiteTemp == 3) {
			$Operative_patientConfirmSite = "Both Eye";  //OU
		}else if($Operative_patientConfirmSiteTemp == 4) {
			$Operative_patientConfirmSite = "Left Upper Lid";
		}else if($Operative_patientConfirmSiteTemp == 5) {
			$Operative_patientConfirmSite = "Left Lower Lid";
		}else if($Operative_patientConfirmSiteTemp == 6) {
			$Operative_patientConfirmSite = "Right Upper Lid";
		}else if($Operative_patientConfirmSiteTemp == 7) {
			$Operative_patientConfirmSite = "Right Lower Lid";
		}else if($Operative_patientConfirmSiteTemp == 8) {
			$Operative_patientConfirmSite = "Bilateral Upper Lid";
		}else if($Operative_patientConfirmSiteTemp == 9) {
			$Operative_patientConfirmSite = "Bilateral Lower Lid";
		}
	// END APPLYING NUMBERS TO PATIENT SITE

	$Operative_patientConfirmPrimProc = $Operative_patientConfirm_tblRow["patient_primary_procedure"];
	$Operative_patientConfirmSecProc = $Operative_patientConfirm_tblRow["patient_secondary_procedure"];
	$Operative_patientConfirmTeriProc = $Operative_patientConfirm_tblRow["patient_tertiary_procedure"];
	if($Operative_patientConfirmSecProc=="N/A") { $Operative_patientConfirmSecProc="";  }

	//FETCH DATA FROM OPEARINGROOMRECORD TABLE
	$diagnosisQry=imw_query("select preOpDiagnosis , postOpDiagnosis from operatingroomrecords where patient_id='$patient_id' and confirmation_id='$pConfId'");
	$diagnosisRes=imw_fetch_array($diagnosisQry);	
	$preopdiagnosis= $diagnosisRes["preOpDiagnosis"];
	$postopdiagnosis= $diagnosisRes["postOpDiagnosis"];
	if(trim($postopdiagnosis)=="") {
		$postopdiagnosis = $preopdiagnosis;
	}
	// END FETCH DATA FROM OPEARINGROOMRECORD TABLE

//END GET PATIENT DETAIL

$detailOperativeTemplate = $objManageData->getRowRecord('operative_template', 'template_id ', $template_id);
if($detailOperativeTemplate) {
	$operative_data = stripslashes($detailOperativeTemplate->template_data);
	
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
		$operative_data= str_ireplace("{PATIENT ID}","<b>".$Operative_patientName_tblRow["patient_id"]."</b>",$operative_data);
		$operative_data= str_ireplace("{PATIENT FIRST NAME}","<b>".$Operative_patientName_tblRow["patient_fname"]."</b>",$operative_data);
		$operative_data= str_ireplace("{MIDDLE INITIAL}","<b>".$Operative_patientName_tblRow["patient_mname"]."</b>",$operative_data);
		$operative_data= str_ireplace("{LAST NAME}","<b>".$Operative_patientName_tblRow["patient_lname"]."</b>",$operative_data);
		$operative_data= str_ireplace("{DOB}","<b>".$Operative_patientNameDob."</b>",$operative_data);
		$operative_data= str_ireplace("{DOS}","<b>".$Operative_patientConfirmDos."</b>",$operative_data);
		$operative_data= str_ireplace("{SURGEON NAME}","<b>".$Operative_patientConfirm_tblRow["surgeon_name"]."</b>",$operative_data);
		$operative_data= str_ireplace("{SITE}","<b>".$Operative_patientConfirmSite."</b>",$operative_data);
		$operative_data= str_ireplace("{PROCEDURE}","<b>".$Operative_patientConfirmPrimProc."</b>",$operative_data);
		$operative_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Operative_patientConfirmSecProc."</b>",$operative_data);
		$operative_data= str_ireplace("{TERTIARY PROCEDURE}","<b>".$Operative_patientConfirmTeriProc."</b>",$operative_data);
		$operative_data= str_ireplace("{PRE-OP DIAGNOSIS}","<b>".$preopdiagnosis."</b>",$operative_data);
		$operative_data= str_ireplace("{POST-OP DIAGNOSIS}","<b>".$postopdiagnosis."</b>",$operative_data);
		$operative_data= str_ireplace("{DATE}","<b>".date('m-d-Y')."</b>",$operative_data);
		$operative_data= str_ireplace("{TIME}","<b>".date('h:i A')."</b>",$operative_data);
		$operative_data= str_ireplace("{ASC NAME}",$_SESSION['loginUserFacilityName'],$operative_data);
		$operative_data= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$operative_data);
		$operative_data= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$operative_data);

	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 
	
	echo $operative_data;
}	
	
?>
