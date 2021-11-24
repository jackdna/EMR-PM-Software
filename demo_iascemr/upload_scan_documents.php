<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
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
$formName = $_REQUEST['formName'];
$folderId = $_REQUEST['folderId'];
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];
$admin = $_REQUEST['admin'];
$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];
$scanANESTHESIA = $_REQUEST['scanANESTHESIA'];
$ANESTHESIAScan = $_REQUEST['ANESTHESIAScan'];


$dosScan = $_REQUEST['dosScan'];
$ptStubId = $_REQUEST['ptStubId'];


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
}else {
	//GET SURGEON NAME FROM STUB TABLE
		$stubTblSurgeonData = $objManageData->getRowRecord('stub_tbl', 'stub_id', $_REQUEST['ptStubId']);
		$stubTblSurgeonFname = $stubTblSurgeonData->surgeon_fname;
		$stubTblSurgeonMname = $stubTblSurgeonData->surgeon_mname;
		$stubTblSurgeonLname = $stubTblSurgeonData->surgeon_lname;
		if($stubTblSurgeonMname){
			$stubTblSurgeonMname = ' '.$stubTblSurgeonMname;
		}
		$surgeonName = $stubTblSurgeonFname.$stubTblSurgeonMname.' '.$stubTblSurgeonLname;
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
					$fileName 		= $_FILES['file']['name'][$i];
					$fileType 		= $_FILES['file']['type'][$i];
					$PSize 			= $_FILES["file"]["size"][$i];
					$TempFile 		= fopen($_FILES["file"]["tmp_name"][$i], "r");		
					$fileCon 		= addslashes(fread($TempFile, $PSize));
					$fileNameTemp	= $_FILES['file']['tmp_name'][$i];
					$extn 			= array_pop(explode('.', $fileName));
					$fileName		= urldecode($fileName);
					$fileName 		= str_ireplace(" ","-",$fileName);
					$fileName 		= str_replace(",","-",$fileName);
					$fileName 		= str_replace("'","-",$fileName);
					unset($arrayRecord);
					$arrayRecord['image_type'] 		= "SCANED";
					$arrayRecord['document_name'] 	= $fileName;
					$arrayRecord['document_size'] 	= $PSize;
					$arrayRecord['confirmation_id'] = $pconfirmId;
					$arrayRecord['patient_id'] 		= $patient_id;
					$arrayRecord['document_id'] 	= $folderId;
					$arrayRecord['dosOfScan'] 		= $dosScan;
					$arrayRecord['stub_id'] 		= $ptStubId;
					$arrayRecord['scan_upload_save_date_time'] = date('Y-m-d H:i:s');;
					
					$pdfFolderName 		= 'admin/pdfFiles/'.$surgeonName;
					$pdfFolderNameSave 	= 'pdfFiles/'.$surgeonName;
					if(is_dir($pdfFolderName)) {
						//DO NOT CREATE FOLDER AGAIN
					}else {
						mkdir($pdfFolderName, 0777);
					}
					$insertImage = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
					$jpgFilePathDatabaseSave = $pdfFolderNameSave."/scan_".$insertImage.".jpg";
					unset($arrayRecord);
					$arrayRecord['pdfFilePath'] = $jpgFilePathDatabaseSave;
					$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl', 'scan_upload_id', $insertImage);
					$jpgFileFullPath = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
					$fContent 		 = file_get_contents($fileNameTemp);
					@file_put_contents($jpgFileFullPath,$fContent);	
	
					if($scanIOL){
						$iol_scan_operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $scanIOL);
						$field_iol_scan_ScanUpload 	= $iol_scan_operatingRoomRecordDetails->iol_ScanUpload;		
						$field_iol_scan_ScanUpload2 = $iol_scan_operatingRoomRecordDetails->iol_ScanUpload2;
						
						$field_iol_scan_ScanStatus 	= $iol_scan_operatingRoomRecordDetails->iol_ScanStatus;		
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
					
					//START SCAN ANESTHESIA	
					if($scanANESTHESIA){
						
						$folderPath = 'admin/pdfFiles/local_anes_detail';
						if(!is_dir($folderPath)) {mkdir($folderPath, 0777);}
						if(file_exists($folderPath.'/anes_'.$scanANESTHESIA.'.pdf')) {
							unlink($folderPath.'/anes_'.$scanANESTHESIA.'.pdf');	
						}
						$scanUploadSavePath = $folderPath.'/anes_'.$scanANESTHESIA.'.jpg';
						copy($_FILES["file"]["tmp_name"][$i],$scanUploadSavePath);

						unset($arrayRecord);
						$arrayRecord['anes_ScanUploadType'] 	= 'application/jpg';
						//$arrayRecord['anes_ScanUpload'] 		= $fileCon;
						$arrayRecord['anes_ScanStatus'] 		= 'SCANED';
						$arrayRecord['anes_ScanUploadName'] 	= $fileName;
						$arrayRecord['anes_ScanUploadDateTime'] = date('Y-m-d H:i:s');
						$arrayRecord['anes_ScanUploadPath'] 	= $scanUploadSavePath;
						$updateScanANESTHESIA = $objManageData->updateRecords($arrayRecord, 'localanesthesiarecord', 'localAnesthesiaRecordId', $scanANESTHESIA);

					}
					//END SCAN ANESTHESIA
					
					//START SCAN SURGERYCENTER LOGO
					if($admin=='true') {
						unset($arrayRecord);
						$arrayRecord['logoType'] = 'SCANED';
						$arrayRecord['logoName'] = $fileName;
						$arrayRecord['surgeryCenterLogo'] = $fileCon;
						$updateSurgeryCenterLogo = $objManageData->updateRecords($arrayRecord, 'surgerycenter', 'surgeryCenterId', 1);
					}
					//END SCAN SURGERYCENTER LOGO
					
					fclose($TempFile);
					$message .= $_FILES['file']['name'][$i]." uploaded.<br>";
				}
			}
		}
		if(!$uploads)  $message = "No files selected!";
	}
}
?>