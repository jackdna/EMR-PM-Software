<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
include("common/iOLinkCommonFunction.php");
$objManageData = new manageData;
error_reporting(0);
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
$files_to_upload = 1;
$_POST    = $HTTP_POST_VARS;
$_GET     = $HTTP_GET_VARS;
$_SESSION = $HTTP_SESSION_VARS;
$pconfirmId = $_REQUEST['pconfirmId'];	
$patient_id = $_REQUEST['patient_id'];
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$formName = $_REQUEST['formName'];
$folderId = $_REQUEST['folderId'];
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];
$admin = $_REQUEST['admin'];
$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];

$insuranceType = $_REQUEST['insuranceType'];
$INSURANCEScan = $_REQUEST['INSURANCEScan'];

$scaniOLinkConsentId = $_REQUEST['scaniOLinkConsentId'];
$CONSENTScan = $_REQUEST['CONSENTScan'];

$scanPtInfo = $_REQUEST['scanPtInfo'];
$scanClinical = $_REQUEST['scanClinical'];
$scanIOLFolder = $_REQUEST['scanIOLFolder'];
$scanHP = $_REQUEST['scanHP'];
$scanEKG = $_REQUEST['scanEKG'];
$scanHealthQuest = $_REQUEST['scanHealthQuest'];
$scanOcularHx = $_REQUEST['scanOcularHx'];
$scanAnesthesiaConsent = $_REQUEST['scanAnesthesiaConsent'];


$sub_Docs_Id = $_REQUEST['sub_Docs_Id'];

$formArray = explode(',', $formName);
	$formName = $formArray[0];
	$formId = $formArray[1];

if(!$pconfirmId){
	$formName = 'SurgeryCenterLogo';
}
if($pconfirmId) {
	// GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
		$surgeonData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pconfirmId);
		$surgeonName = $surgeonData->surgeon_name;
	// END GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
}else if($patient_in_waiting_id) {
	//GET SURGEON NAME FROM STUB TABLE
		$patientInWaitingSurgeonData = $objManageData->getRowRecord('patient_in_waiting_tbl', 'patient_in_waiting_id', $patient_in_waiting_id);
		$patientInWaitingSurgeonFname = $patientInWaitingSurgeonData->surgeon_fname;
		$patientInWaitingSurgeonMname = $patientInWaitingSurgeonData->surgeon_mname;
		$patientInWaitingSurgeonLname = $patientInWaitingSurgeonData->surgeon_lname;
		if($patientInWaitingSurgeonMname){
			$patientInWaitingSurgeonMname = ' '.$patientInWaitingSurgeonMname;
		}
		$surgeonName = $patientInWaitingSurgeonFname.$patientInWaitingSurgeonMname.' '.$patientInWaitingSurgeonLname;
	//END SURGEON NAME FROM STUB TABLE
}
	
$surgeonName = str_replace(" ","_",$surgeonName);
$surgeonName = str_replace(",","",$surgeonName);
$surgeonName = str_replace("!","",$surgeonName);
$surgeonName = str_replace("@","",$surgeonName);
$surgeonName = str_replace("%","",$surgeonName);
$surgeonName = str_replace("^","",$surgeonName);
$surgeonName = str_replace("$","",$surgeonName);
$surgeonName = str_replace("'","",$surgeonName);
$surgeonName = str_replace("*","",$surgeonName);

if($_REQUEST['method']){
	if($_REQUEST['method'] == "upload"){
		$uploads = false;
		for($i = 0 ; $i < $files_to_upload; $i++){        
			if($_FILES['file']['name'][$i]){
				$uploads = true;
				if($_FILES['file']['name'][$i]){
					$fileName = $_FILES['file']['name'][$i];
					$fileType = $_FILES['file']['type'][$i];
					$PSize = $_FILES["file"]["size"][$i];
					$TempFile = fopen($_FILES["file"]["tmp_name"][$i], "r");		
					$fileCon = addslashes(fread($TempFile, $PSize));
					unset($arrayRecord);
					$arrayRecord['image_type'] = 'SCANED';
					$arrayRecord['document_name'] = $fileName;
					$arrayRecord['document_type'] = $fileType;
					$arrayRecord['document_size'] = $PSize;
					$arrayRecord['img_content'] = $fileCon;
					//$arrayRecord['form_name'] = $formName;			
					$arrayRecord['confirmation_id'] = $pconfirmId;
					$arrayRecord['patient_id'] = $patient_id;
					$arrayRecord['scan_upload_form_id'] = $formId;
					$arrayRecord['document_id'] = $folderId;					

					if($sub_Docs_Id){
						$arrayRecord['parent_sub_doc_id'] = $sub_Docs_Id;
						$existSubImage = $objManageData->getRowRecord('scan_upload_tbl', 'parent_sub_doc_id', $sub_Docs_Id);
						if($existSubImage){
							$objManageData->updateRecords($arrayRecord, 'scan_upload_tbl', 'parent_sub_doc_id', $sub_Docs_Id);
						}else{
							//$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');		
						}						
						$insertImage = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
					}else{
						//$insertImage = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
					}				
	
					if($scanIOL){
						$iol_scan_operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $scanIOL);
						$field_iol_scan_ScanUpload = $iol_scan_operatingRoomRecordDetails->iol_ScanUpload;		
						$field_iol_scan_ScanUpload2 = $iol_scan_operatingRoomRecordDetails->iol_ScanUpload2;
						
						$field_iol_scan_ScanStatus = $iol_scan_operatingRoomRecordDetails->iol_ScanStatus;		
						$field_iol_scan_ScanStatus2 = $iol_scan_operatingRoomRecordDetails->iol_ScanStatus2;		
						
						if($field_iol_scan_ScanUpload && !$field_iol_scan_ScanUpload2) {
							unset($arrayRecord);
							$arrayRecord['iol_type2'] = $fileType;
							$arrayRecord['iol_ScanUpload2'] = $fileCon;
							$arrayRecord['iol_ScanStatus2'] = 'yes';
							
						}else if(!$field_iol_scan_ScanUpload && $field_iol_scan_ScanUpload2) {
							unset($arrayRecord);
							$arrayRecord['iol_type'] = $fileType;
							$arrayRecord['iol_ScanUpload'] = $fileCon;
							$arrayRecord['iol_ScanStatus'] = 'yes';
						}else if($field_iol_scan_ScanUpload && $field_iol_scan_ScanUpload2) {
							//IF FIRST IMAGE IS ALRADY SCANNED THEN SCAN SECOND IMAGE 
							if($field_iol_scan_ScanStatus=='yes' && $field_iol_scan_ScanStatus2=='') {
								unset($arrayRecord);
								$arrayRecord['iol_type2'] = $fileType;
								$arrayRecord['iol_ScanUpload2'] = $fileCon;
								$arrayRecord['iol_ScanStatus2'] = 'yes';
							}else { //ELSE SCAN FIRST IMAGE
								unset($arrayRecord);
								$arrayRecord['iol_type'] = $fileType;
								$arrayRecord['iol_ScanUpload'] = $fileCon;
								$arrayRecord['iol_ScanStatus'] = 'yes';
							
							}	
						
						}else { //ELSE SCAN FIRST IMAGE
							unset($arrayRecord);
							$arrayRecord['iol_type'] = 'SCANED';
							$arrayRecord['iol_ScanUpload'] = $fileCon;
							$arrayRecord['iol_ScanStatus'] = 'yes';
						
						}
						$updateScanIOL = $objManageData->updateRecords($arrayRecord, 'operatingroomrecords', 'operatingRoomRecordsId', $scanIOL);
					}
					
					//START SCAN DISCHARGE SUMMARY
					if($scanDISCHARGE){
						$dis_scan_operatingRoomRecordDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $scanDISCHARGE);
						$field_dis_scan_ScanUpload = $dis_scan_operatingRoomRecordDetails->dis_ScanUpload;		
						$field_dis_scan_ScanUpload2 = $dis_scan_operatingRoomRecordDetails->dis_ScanUpload2;
						
						$field_dis_scan_ScanStatus = $dis_scan_operatingRoomRecordDetails->dis_ScanStatus;		
						$field_dis_scan_ScanStatus2 = $dis_scan_operatingRoomRecordDetails->dis_ScanStatus2;		
						
						if($field_dis_scan_ScanUpload && !$field_dis_scan_ScanUpload2) {
							unset($arrayRecord);
							$arrayRecord['dis_type2'] = $fileType;
							$arrayRecord['dis_ScanUpload2'] = $fileCon;
							$arrayRecord['dis_ScanStatus2'] = 'yes';
							
						}else if(!$field_dis_scan_ScanUpload && $field_dis_scan_ScanUpload2) {
							unset($arrayRecord);
							$arrayRecord['dis_type'] = $fileType;
							$arrayRecord['dis_ScanUpload'] = $fileCon;
							$arrayRecord['dis_ScanStatus'] = 'yes';
						}else if($field_dis_scan_ScanUpload && $field_dis_scan_ScanUpload2) {
							//IF FIRST IMAGE IS ALRADY SCANNED THEN SCAN SECOND IMAGE 
							if($field_dis_scan_ScanStatus=='yes' && $field_dis_scan_ScanStatus2=='') {
								unset($arrayRecord);
								$arrayRecord['dis_type2'] = $fileType;
								$arrayRecord['dis_ScanUpload2'] = $fileCon;
								$arrayRecord['dis_ScanStatus2'] = 'yes';
							}else { //ELSE SCAN FIRST IMAGE
								unset($arrayRecord);
								$arrayRecord['dis_type'] = $fileType;
								$arrayRecord['dis_ScanUpload'] = $fileCon;
								$arrayRecord['dis_ScanStatus'] = 'yes';
							
							}	
						
						}else { //ELSE SCAN FIRST IMAGE
							unset($arrayRecord);
							$arrayRecord['dis_type'] = $fileType;
							$arrayRecord['dis_ScanUpload'] = $fileCon;
							$arrayRecord['dis_ScanStatus'] = 'yes';
						
						}
						$updateScanDISCHARGE = $objManageData->updateRecords($arrayRecord, 'dischargesummarysheet', 'dischargeSummarySheetId', $scanDISCHARGE);
					}
					//END SCAN DISCHARGE SUMMARY	
					
					//START SCAN SURGERYCENTER LOGO
					if($admin=='true') {
						unset($arrayRecord);
						$arrayRecord['logoType'] = 'SCANED';
						$arrayRecord['logoName'] = $fileName;
						$arrayRecord['surgeryCenterLogo'] = $fileCon;
						$updateSurgeryCenterLogo = $objManageData->updateRecords($arrayRecord, 'surgerycenter', 'surgeryCenterId', 1);
					}
					//END SCAN SURGERYCENTER LOGO
					
					//START SCAN IOLINK CARD 
					if($scaniOLinkConsentId || $scanPtInfo || $scanClinical || $scanIOLFolder || $scanHP || $scanEKG || $scanHealthQuest || $scanOcularHx || $scanAnesthesiaConsent){
						
						$fileNameTemp	= $_FILES['file']['tmp_name'][$i];
						$extn 			= array_pop(explode('.', $fileName));
						$fileName		= urldecode($fileName);
						$fileName 		= str_ireplace(" ","-",$fileName);
						$fileName 		= str_replace(",","-",$fileName);
						$fileName 		= str_replace("'","-",$fileName);
						
						unset($arrayRecord);
						$arrayRecord['patient_id'] = $patient_id;
						$arrayRecord['patient_in_waiting_id'] = $patient_in_waiting_id;
						$arrayRecord['scan_save_date_time'] = date('Y-m-d H:i:s');
						$arrayRecord['document_name'] = $fileName;
						$arrayRecord['image_type'] = "SCANED";
						$arrayRecord['document_size'] = $PSize;
						if($scanPtInfo) { 
							$arrayRecord['iolink_scan_folder_name'] = 'ptInfo';
						}else if($scanClinical) {
							$arrayRecord['iolink_scan_folder_name'] = 'clinical';
						}else if($scanIOLFolder) {
							$arrayRecord['iolink_scan_folder_name'] = 'iol';
						}else if($scanHP) {
							$arrayRecord['iolink_scan_folder_name'] = 'h&p'; 
						}else if($scanEKG) {
							$arrayRecord['iolink_scan_folder_name'] = 'ekg';
						}else if($scanHealthQuest) {
							$arrayRecord['iolink_scan_folder_name'] = 'healthQuest';
						}else if($scanOcularHx) {
							$arrayRecord['iolink_scan_folder_name'] = 'ocularHx';
						}else if($scanAnesthesiaConsent) {
							$arrayRecord['iolink_scan_folder_name'] = 'consent';
						}
						
						$insertScaniOLinkConsentId = $objManageData->addRecords($arrayRecord, 'iolink_scan_consent');
						setReSyncroStatus($patient_in_waiting_id,'scanDoc');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
						
						$pdfFolderName = 'admin/pdfFiles/'.$surgeonName;
						$pdfFolderNameSave = 'pdfFiles/'.$surgeonName;
						if(is_dir($pdfFolderName)) {
							//DO NOT CREATE FOLDER AGAIN
						}else {
							mkdir($pdfFolderName, 0777);
						}
						$jpgFilePathDatabaseSave = $pdfFolderNameSave."/iolink_scan_".$insertScaniOLinkConsentId.".jpg";
						unset($arrayRecord);
						$arrayRecord['pdfFilePath'] = $jpgFilePathDatabaseSave;
						$updtScaniOLinkConsentTbl 	= $objManageData->updateRecords($arrayRecord, 'iolink_scan_consent', 'scan_consent_id', $insertScaniOLinkConsentId);
						$jpgFileFullPath 			= $rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
						$fContent 		 			= file_get_contents($fileNameTemp);
						@file_put_contents($jpgFileFullPath,$fContent);	
						//exec("convert ".$jpgFileFullPath." ".$pdfFileFullPathPDF);
						//unlink($jpgFileFullPath);
					}
					//END SCAN IOLINK CARD
					
					//START SCAN INSURANCE CARD
					if($INSURANCEScan){
						unset($arrayInsuranceRecord);
						$arrayInsuranceRecord['patient_id'] = $patient_id;
						$arrayInsuranceRecord['waiting_id'] = $patient_in_waiting_id;
						$arrayInsuranceRecord['type'] = $insuranceType;
						$insuranceDetailsList = $objManageData->getMultiChkArrayRecords('insurance_data', $arrayInsuranceRecord);	
						if(count($insuranceDetailsList)>0) {
							foreach($insuranceDetailsList as $insuranceDetails) {
								$field_insuranceId = $insuranceDetails->id;		
								
								$field_insScan1Upload = $insuranceDetails->insScan1Upload;		
								$field_insScan2Upload = $insuranceDetails->insScan2Upload;
								
								$field_insScan1Status = $insuranceDetails->insScan1Status;		
								$field_insScan2Status = $insuranceDetails->insScan2Status;		
								
								if($field_insScan1Upload && !$field_insScan2Upload) {
									unset($arrayRecord);
									$arrayRecord['insScan2Upload'] = $fileCon;
									$arrayRecord['insScan2Status'] = 'yes';
									$arrayRecord['type'] = $insuranceType;
									
								}else if(!$field_insScan1Upload && $field_insScan2Upload) {
									unset($arrayRecord);
									$arrayRecord['insScan1Upload'] = $fileCon;
									$arrayRecord['insScan1Status'] = 'yes';
									$arrayRecord['type'] = $insuranceType;
								}else if($field_insScan1Upload && $field_insScan2Upload) {
									//IF FIRST IMAGE IS ALRADY SCANNED THEN SCAN SECOND IMAGE 
									if($field_insScan1Status=='yes' && $field_insScan2Status=='') {
										unset($arrayRecord);
										$arrayRecord['insScan2Upload'] = $fileCon;
										$arrayRecord['insScan2Status'] = 'yes';
										$arrayRecord['type'] = $insuranceType;
									}else { //ELSE SCAN FIRST IMAGE
										unset($arrayRecord);
										$arrayRecord['insScan1Upload'] = $fileCon;
										$arrayRecord['insScan1Status'] = 'yes';
										$arrayRecord['type'] = $insuranceType;
									
									}	
								}else { //ELSE SCAN FIRST IMAGE
									unset($arrayRecord);
									$arrayRecord['insScan1Upload'] = $fileCon;
									$arrayRecord['insScan1Status'] = 'yes';
									$arrayRecord['type'] = $insuranceType;
								
								}
								$updateInsuranceData = $objManageData->updateRecords($arrayRecord, 'insurance_data', 'id', $field_insuranceId);
							}
						}else {
							unset($arrayRecord);
							$arrayRecord['insScan1Upload'] 	= $fileCon;
							$arrayRecord['insScan1Status'] 	= 'yes';
							$arrayRecord['type'] 			= $insuranceType;
							$arrayRecord['patient_id'] 		= $patient_id;
							$arrayRecord['waiting_id'] 		= $patient_in_waiting_id;
							$objManageData->addRecords($arrayRecord, 'insurance_data');
						}
						setReSyncroStatus($patient_in_waiting_id,'scanInsurance');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
					}
					//END SCAN INSURANCE CARD
					
					fclose($TempFile);
					$message .= $_FILES['file']['name'][$i]." uploaded.<br>";
				}
			}
		}
		if(!$uploads)  $message = "No files selected!";
	}
}
?>