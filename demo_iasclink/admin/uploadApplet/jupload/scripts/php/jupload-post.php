<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("../../../../../common/conDb.php");
//include("../../../../adminLinkfile.php");
include_once("../../../../classObjectFunction.php");
include_once("../../../../../common/iOLinkCommonFunction.php");
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
$scanHP = $_REQUEST['scanHP'];
$scanEKG = $_REQUEST['scanEKG'];
$scanHealthQuest = $_REQUEST['scanHealthQuest'];
$scanOcularHx = $_REQUEST['scanOcularHx'];
$scanAnesthesiaConsent = $_REQUEST['scanAnesthesiaConsent'];



//-------------------
/**
 * JUpload-Post Handler
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

global $_ju_listener, $_ju_uploadRoot, $_ju_fileDir, $_ju_thumbDir, $_ju_maxSize;

// Include a file which provides several helper functions and is configured through the jupload.cfg.php
include_once(dirname(__FILE__) . "/inc/jupload.inc.php");

// Upload is starting
$_ju_listener->onStart($_SERVER["HTTP_X_JUPLOAD_ID"]);

/**
 * Iterate over all received files.
 */
foreach($_FILES as $tagname=>$fileinfo) {
	// get the name of the temporarily saved file (e.g. /tmp/php34634.tmp)
	$tempPath = $fileinfo['tmp_name'];

	// The filename and relative path within the Upload-Tree (eg. "/my documents/important/Laura.jpg")
	$relativePath = $_POST[$tagname . '_relativePath'];
	
	// Do we have a valid file?
	if (!checkSavePath($relativePath) || !$_ju_listener->checkValid($relativePath, $tempPath)) {
		continue;
	}
	
	$original_file=$fileinfo;
	$imageType 	= $fileinfo['type'];
	$imageName 	= $fileinfo['name'];
	$extn 		= array_pop(explode('.', $imageName));
	$imageName 	= urldecode($imageName);
	$imageName 	= str_ireplace(" ","-",$imageName);
	$imageName	= str_ireplace(",","-",$imageName);
	$imageName	= str_ireplace("'","-",$imageName);
	$tmp 		= $fileinfo['tmp_name'];
	$PSize 		= $fileinfo['size'];
	$oTempFile 	= fopen($fileinfo['tmp_name'], "r");		
	$image 		= fread($oTempFile, $PSize);
	$parentFolder = $_REQUEST['parentFolder'];

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

	$pdfFolderName = '../../../../pdfFiles/'.$surgeonName;
	//$pdfFolderName = realpath(dirname(__FILE__).'/../../../../pdfFiles/'.$surgeonName);		
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
	$pdfJpgFileFullPath 			= $rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$pdfJpgFilePathDatabaseSave;
	$fContent 						= file_get_contents($tempPath);
	@file_put_contents($pdfJpgFileFullPath,$fContent);		
	unset($arrayRecord);
	$arrayRecord['pdfFilePath'] 	= $pdfJpgFilePathDatabaseSave;
	$updtScanUpldTbl 				= $objManageData->updateRecords($arrayRecord, 'iolink_scan_consent', 'scan_consent_id', $inserIdScanUpload);
	
	//END CODE FOR PDF FILE
	$files[$relativePath] = $tempPath;
}
setReSyncroStatus($patient_in_waiting_id,'uploadDoc');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
if ($files) {
	foreach ($files as $relativePath => $tempPath)  {
		// Do we have a thumbnail? If it is not a thumbnail, it is a regular file.
		$isThumb = $_POST[$tagname . '_thumbnail'];
	
		// Where to save the file? Determine the target-directory, depending on if it is a thumbnail or a file
		$filepath = $_ju_uploadRoot . ($isThumb ? $_ju_thumbDir : $_ju_fileDir) . "/$relativePath";
	
		// Create folders
		mkdirs(dirname($filepath = normalize($filepath)));
		
		// Move the temporary file to the target directory
		//move_uploaded_file($tempPath, $filepath) or die("Error while moving temporary file to target path: " . $relativePath);
		
		// Tell the listener that another file has successfully been received.
		$_ju_listener->onReceived($filepath, $relativePath, $isThumbs);
	}
}

$_ju_listener->finished();


//-----------------------
?>