<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("../../common/conDb.php");
include_once("../../common/iOLinkCommonFunction.php");
include_once("../classObjectFunction.php");
$objManageData = new manageData;
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}


$pConfirmId = $_REQUEST['pconfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];

$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];

$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];
$folderId = $_REQUEST['folderId'];
$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$ptStubId = $_REQUEST['ptStubId'];


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


$activex = $_REQUEST['activex'];

$upload_arr_new[0]=array();
$upload = $_FILES['files'];
if($_REQUEST['activex']=='1'){
	$upload_arr_new=array();
	$upload_arr_new=$upload;
	$uplode_string="";
	foreach($upload as $nkey=> $up_data){
		$upload_arr_new[0][$nkey]=$up_data;
	}
}else {
	foreach($upload as $nkey=> $up_data){
		foreach($up_data as $my_key => $main_val_arr){
			$upload_arr_new[0][$nkey]=$main_val_arr;
		}
	}
}
//$aa = fopen("data.txt","a"); //For Debugging
foreach($upload_arr_new as $tagname=>$fileinfo) { 
 
	// get the name of the temporarily saved file (e.g. /tmp/php34634.tmp)
	$tempPath 	= $fileinfo['tmp_name'];
	$imageType 	= $fileinfo['type'];
	$imageName 	= $fileinfo['name'];
	$extn 		= array_pop(explode('.', $imageName));
	$imageName 	= urldecode($imageName);
	$imageName 	= str_ireplace(" ","-",$imageName);
	$imageName 	= str_ireplace(",","-",$imageName);
	$imageName 	= str_ireplace("'","-",$imageName);
	if($extn=='gif') 						{$imageType = "image/gif";	
	}else if($extn=='jpg' || $extn=='jpeg') {$imageType = "image/jpeg";	
	}else if($extn=='png') 					{$imageType = "image/png";	
	}else if($extn=='pdf') 					{$imageType = "application/pdf";	
	}
	//fwrite($aa, $extn." @@ ".$imageType." @@ ".$imageName." @@ ".$PSize." @@ ".$pConfirmId." @@ ".$patient_id." @@ ".$patient_in_waiting_id." @@ ".$scanClinical." <br> \n\r "); //For Debugging
	if($imageType=="image/gif" || $imageType=="image/jpeg" || $imageType=="image/png" || $imageType=="application/pdf"){
		$PSize = $fileinfo['size'];
		unset($arrayRecord);
		$arrayRecord['image_type'] = $imageType;
		$arrayRecord['document_name'] = $imageName;
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['patient_in_waiting_id'] = $patient_in_waiting_id;
		$arrayRecord['document_size'] = $PSize;
		$arrayRecord['scan_save_date_time'] = date('Y-m-d H:i:s');
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
	 
		$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'iolink_scan_consent');
		
		//START CODE FOR PDF FILE
		if($pConfirmId) {
			// GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
				$surgeonData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
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
	
		$pdfFolderName = '../pdfFiles/'.$surgeonName;
		$pdfFolderNameSave = 'pdfFiles/'.$surgeonName;
		
		if(is_dir($pdfFolderName)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($pdfFolderName, 0777);
		}
		if(strtolower($imageType) == 'application/pdf') {
			$pdfJpgFilePathDatabaseSave = $pdfFolderNameSave."/iolink_".$inserIdScanUpload.".pdf";
		}else {
			$pdfJpgFilePathDatabaseSave = $pdfFolderNameSave."/iolink_image_".$inserIdScanUpload.".jpg";
		}
		
		$pdfJpgFileFullPath 		= $rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$pdfJpgFilePathDatabaseSave;
		$fContent 					= file_get_contents($tempPath);
		@file_put_contents($pdfJpgFileFullPath,$fContent);		
		unset($arrayRecord);
		$arrayRecord['pdfFilePath'] = $pdfJpgFilePathDatabaseSave;
		$updtScanUpldTbl 			= $objManageData->updateRecords($arrayRecord, 'iolink_scan_consent', 'scan_consent_id', $inserIdScanUpload);
	}
	//END CODE FOR PDF FILE
}
setReSyncroStatus($patient_in_waiting_id,'uploadDoc');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
?>