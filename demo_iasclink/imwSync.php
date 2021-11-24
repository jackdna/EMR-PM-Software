<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
set_time_limit(700);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");


include_once("common/conDb.php");
include_once("common/iOLinkCommonFunction.php");
include_once("common/iOlinkFunction.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$selDos = $_REQUEST['selDos'];
$multiPatientInWaitingId = $_REQUEST['multiPatientInWaitingId'];
//START FUNCTION TO GET SCAN CONTENT
function getInsScanContentForImw($insuranceDataScanCardPath) {
	$insuranceDataScanFolder = dirname(__FILE__).'/imedic_uploaddir';
	$insuranceDataScanCard='';
	if($insuranceDataScanCardPath) {
		$insuranceDataScanCardGetPath = $insuranceDataScanFolder.$insuranceDataScanCardPath;
		$insuranceDataScanCardGetPath = realpath($insuranceDataScanCardGetPath);
		clearstatcache();
		if (file_exists($insuranceDataScanCardGetPath)) {	
			$insuranceDataScanCardContents = file_get_contents($insuranceDataScanCardGetPath);	
			$insuranceDataScanCard = addslashes(base64_encode($insuranceDataScanCardContents));
		}
	}
	return $insuranceDataScanCard;
}
//END FUNCTION TO GET SCAN CONTENT

//START FUNCTION TO PUT SCAN CONTENT
function putInsImgContentToImw($scan_card,$insuranceDataScanContent,$demoPtId,$imwDirNme) {
	$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
	$scanCardPath='';
	if($scan_card) {
		$scan_cardExplode = explode('/',$scan_card);
		$iolinkInsScanFolder = $rootServerPath.'/'.$imwDirNme.'/interface/main/uploaddir/PatientId_'.$demoPtId;
		if(!is_dir($iolinkInsScanFolder)){		
			mkdir($iolinkInsScanFolder);
		}
		$scanCardPath = "/PatientId_".$demoPtId.'/'.$scan_cardExplode[2];
		$inScanPutPdfFileName = $iolinkInsScanFolder.'/'.$scan_cardExplode[2];
		file_put_contents($inScanPutPdfFileName,$insuranceDataScanContent);
	}
	return $scanCardPath;
}	
//END FUNCTION TO PUT SCAN CONTENT

//START FUNCTION TO SET PATIENT IMAGE
function putPtImgContentToImw($patient_image_path,$scemr_patient_id,$imw_patient_id) {
	$imwPtSavePath = '';
	global $rootServerPath;
	global $surgeryCenterDirectoryName;
	global $imwDirectoryName;
	if($patient_image_path) {
		$imwPatientImageName = str_ireplace('pdfFiles/patient_images/patient_id_'.$scemr_patient_id.'_','',$patient_image_path);	
		
		if(trim($imwPatientImageName)) {
			$scemrPtImagePath 			= $rootServerPath.'/'.$surgeryCenterDirectoryName.'/admin/'.$patient_image_path;
			if(file_exists($scemrPtImagePath)) {
				$imwPtImageFolder		= $rootServerPath.'/'.$imwDirectoryName.'/interface/main/uploaddir/PatientId_'.$imw_patient_id;
				if(!is_dir($imwPtImageFolder)){		
					mkdir($imwPtImageFolder);
				}
				$imwPtImageFullPath		= $imwPtImageFolder.'/'.$imwPatientImageName;
				$scemrPtImageContent 	= file_get_contents($scemrPtImagePath);
				file_put_contents($imwPtImageFullPath,$scemrPtImageContent);
				$imwPtSavePath			= '/PatientId_'.$imw_patient_id.'/'.$imwPatientImageName;
			}
		}
	}
	return $imwPtSavePath;
}	
//END FUNCTION TO SET PATIENT IMAGE

$procNameArr = $procAliasArr = array();
$procQry = "SELECT * FROM procedures order by `name` asc, del_status desc";
$procRes = imw_query($procQry);			
if(imw_num_rows($procRes)>0) {
	while($procRow = imw_fetch_array($procRes)) {
		$procName = trim($procRow["name"]);
		$procAlias = trim($procRow["procedureAlias"]);
		$procNameArr[$procName] = $procName;
		$procAliasArr[$procName] = $procAlias;
	}
}
//START CODE TO GET NPI OF SURGEON
$surgeonNpiArr = array();
$surgeonQry 				= "SELECT usersId,npi,fname,mname,lname FROM users WHERE deleteStatus!='Yes' AND user_type='Surgeon' ORDER BY usersId";
$surgeonRes 				= imw_query($surgeonQry) or die($surgeonQry.imw_error());
$surgeonNumRow 				= imw_num_rows($surgeonRes);
$sa_doctor_id				= "";
if($surgeonNumRow>0) {
	while($surgeonRow 		= imw_fetch_array($surgeonRes)) {
		$surgeonNpiFname	= trim($surgeonRow['fname']);
		$surgeonNpiMname	= trim($surgeonRow['mname']);
		$surgeonNpiLname	= trim($surgeonRow['lname']);
		$surgeonNpiArr[$surgeonNpiFname][$surgeonNpiMname][$surgeonNpiLname]	= $surgeonRow['npi'];
	}
	
}
//END CODE TO GET NPI OF SURGEON

//START TO GET PATIENTS FROM patient_in_waiting_tbl
	$getPatientInWaitingTblInfoQry 		= "SELECT * FROM patient_in_waiting_tbl WHERE dos='".$selDos."' AND patient_status!='Canceled' AND patient_in_waiting_id IN(".$multiPatientInWaitingId.") ORDER BY surgery_time, patient_in_waiting_id ASC";
	$getPatientInWaitingTblInfoRes 		= imw_query($getPatientInWaitingTblInfoQry) or die(imw_error().$getPatientInWaitingTblInfoQry);
	$getPatientInWaitingTblNumRow 		= imw_num_rows($getPatientInWaitingTblInfoRes);
	if($getPatientInWaitingTblNumRow>0) {
		$incompleteRec = '';
		$incompleteComment = '';
		while($getPatientInWaitingTblRow=imw_fetch_array($getPatientInWaitingTblInfoRes)) {
			include("common/conDb.php");
			$patient_id 				= $getPatientInWaitingTblRow['patient_id'];
			$patientDataTblQry 			= "SELECT * FROM `patient_data_tbl` WHERE patient_id='".$patient_id."'";
			$patientDataTblRes 			= imw_query($patientDataTblQry) or die(imw_error()); 
			$patientDataTblNumRow 		= imw_num_rows($patientDataTblRes);
			
			$patient_first_name 		= '';
			$patient_middle_name 		= '';
			$patient_last_name 			= '';
			$patient_name 				= '';
			$patient_dob_temp			= '';
			$patient_sex 				= '';
			$patient_address1			= '';
			$patient_address2 			= '';
			$patient_city 				= '';
			$patient_state 				= '';
			$patient_zip 				= '';
			$patient_home_phone 		= '';
			$patient_work_phone 		= '';
			$patient_language 			= '';
			$patient_race 				= '';
			$patient_ethnicity 			= '';
			$patient_religion 			= '';
			if($patientDataTblNumRow>0) {
				$patientDataTblRow 		= imw_fetch_array($patientDataTblRes);
				$patient_title 			= stripslashes($patientDataTblRow['title']);
				$patient_first_name 	= stripslashes($patientDataTblRow['patient_fname']);
				$patient_middle_name 	= stripslashes($patientDataTblRow['patient_mname']);
				$patient_last_name 		= stripslashes($patientDataTblRow['patient_lname']);
				$patient_suffix 		= stripslashes($patientDataTblRow['patient_suffix']);
				$patient_name 			= $patient_last_name.", ".$patient_first_name;
				$patient_dob_temp 		= $patientDataTblRow['date_of_birth'];
				$patient_sex 			= $patientDataTblRow['sex'];
				$patient_address1		= stripslashes($patientDataTblRow['street1']);
				$patient_address2 		= stripslashes($patientDataTblRow['street2']);
				$patient_city 			= $patientDataTblRow['city'];
				$patient_state 			= $patientDataTblRow['state'];
				$patient_zip 			= $patientDataTblRow['zip'];
				$patient_home_phone 	= $patientDataTblRow['homePhone'];
				$patient_work_phone 	= $patientDataTblRow['workPhone'];
				$patient_language 		= $patientDataTblRow['language'];
				$patient_race 			= $patientDataTblRow['race'];
				$patient_ethnicity 		= $patientDataTblRow['ethnicity'];
				$patient_religion 		= $patientDataTblRow['religion'];
				$patient_image_path 	= $patientDataTblRow['patient_image_path'];
			}
			$patient_in_waiting_id 		= $getPatientInWaitingTblRow['patient_in_waiting_id'];
			$idoc_sch_athena_id 		= $getPatientInWaitingTblRow['idoc_sch_athena_id'];
			$patient_site 				= $getPatientInWaitingTblRow['site'];
			$surgeon_fname 				= trim(stripslashes($getPatientInWaitingTblRow['surgeon_fname']));
			$surgeon_mname 				= trim(stripslashes($getPatientInWaitingTblRow['surgeon_mname']));
			$surgeon_lname 				= trim(stripslashes($getPatientInWaitingTblRow['surgeon_lname']));
			$surgeon_npi				= $surgeonNpiArr[trim($getPatientInWaitingTblRow['surgeon_fname'])][trim($getPatientInWaitingTblRow['surgeon_mname'])][trim($getPatientInWaitingTblRow['surgeon_lname'])];
			$surgeon_mname_space		= '';
			if($surgeon_mname){ $surgeon_mname_space = ' '.$surgeon_mname; }
			$surgeon_name 				= $surgeon_fname.$surgeon_mname_space.' '.$surgeon_lname;
	
			$patient_dos_temp 			= $getPatientInWaitingTblRow['dos'];
			$patient_dos='';
			if($patient_dos_temp!=0) { $patient_dos = date('m-d-Y',strtotime($patient_dos_temp)); }
	
			$surgery_time = $getPatientInWaitingTblRow['surgery_time'];
			$surgery_time_temp='';
			if($surgery_time) { $surgery_time_temp = date('h:i A',strtotime($surgery_time)); }
			$iasc_facility_id 			= $getPatientInWaitingTblRow['iasc_facility_id'];
			$patient_prim_proc 			= stripslashes(trim($getPatientInWaitingTblRow['patient_primary_procedure']));
			$patient_sec_proc 			= stripslashes(trim($getPatientInWaitingTblRow['patient_secondary_procedure']));
			$patient_ter_proc 			= stripslashes(trim($getPatientInWaitingTblRow['patient_tertiary_procedure']));
			$patient_prim_proc_alias 	= $patient_sec_proc_alias = $patient_ter_proc_alias = '';
			/*
			if($patient_prim_proc) {
				$procAliasQry	= "SELECT procedureAlias FROM procedures WHERE `name`='".addslashes($patient_prim_proc)."'";
				$procAliasRes = imw_query($procAliasQry) or die(imw_error());
				if(imw_num_rows($procAliasRes)>0) {
					$procAliasRow 			 = imw_fetch_array($procAliasRes);	
					$patient_prim_proc_alias = $procAliasRow['procedureAlias'];
				}
			}*/
			$primProcAliasFound = $secProcAliasFound = $terProcAliasFound = false;
			foreach($procNameArr as $procNameKey => $procNameVal) {
				if($primProcAliasFound==false && strtolower($patient_prim_proc)==strtolower($procNameArr[$procNameKey])) {
					$patient_prim_proc_alias=$procAliasArr[$procNameKey];
					$primProcAliasFound = true;
				}
				if($secProcAliasFound==false && strtolower($patient_sec_proc)==strtolower($procNameArr[$procNameKey])) {
					$patient_sec_proc_alias=$procAliasArr[$procNameKey];
					$secProcAliasFound = true;
				}
				if($terProcAliasFound==false && strtolower($patient_ter_proc)==strtolower($procNameArr[$procNameKey])) {
					$patient_ter_proc_alias=$procAliasArr[$procNameKey];
					$terProcAliasFound = true;
				}
			}				
			
			$patient_status 			= $getPatientInWaitingTblRow['patient_status'];
			
			$comment 					= stripslashes(trim($getPatientInWaitingTblRow['comment']));
			$patient_id 				= $getPatientInWaitingTblRow['patient_id'];
			$pickup_time 				= $getPatientInWaitingTblRow['pickup_time'];
			$arrival_time 				= $getPatientInWaitingTblRow['arrival_time'];
			$drOfficePatientId 			= $getPatientInWaitingTblRow['drOfficePatientId'];
			$iAscSyncroCount 			= $getPatientInWaitingTblRow['iAscSyncroCount'];
			
			if($imwSwitchFile == "sync_imwemr.php"){ //FROM COMMON/CON-DB
				//INCLUDE  FILE
				include("sync_imwemr.php");
			}else if($imwSwitchFile == "sync.php"){ //FROM COMMON/CON-DB
				//INCLUDE SQL FILE 
			}else if($imwSwitchFile == "sync_msaccess.php"){ //FROM COMMON/CON-DB
				//INCLUDE ACCESS FILE
			}
		
		}
		
		if($incompleteComment) {echo $incompleteComment;}
		if($incompleteRec) {echo $incompleteRec;}
		
	}
	
//END TO GET PATIENTS FROM patient_in_waiting_tbl

?>