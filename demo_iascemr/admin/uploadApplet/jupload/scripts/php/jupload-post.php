<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../../../../../common/conDb.php");
include("../../../../adminLinkfile.php");
include_once("../../../../classObjectFunction.php");
$objManageData = new manageData;
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}

$pConfirmId = $_REQUEST['pconfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];

$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];
$folderId = $_REQUEST['folderId'];
$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$ptStubId = $_REQUEST['ptStubId'];
$dosScan = $_REQUEST['dosScan'];
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
	$imageName 	= str_ireplace(",","-",$imageName);
	$imageName 	= str_ireplace("'","-",$imageName);
	
	$PSize = $fileinfo['size'];
	$oTempFile = fopen($fileinfo['tmp_name'], "r");		
	$image = fread($oTempFile, $PSize);
	$parentFolder = $_REQUEST['parentFolder'];

	unset($arrayRecord);
	$arrayRecord['image_type'] = $imageType;
	$arrayRecord['document_name'] = $imageName;
	$arrayRecord['document_size'] = $PSize;
	$arrayRecord['confirmation_id'] = $pConfirmId;
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['document_id'] = $folderId;
	$arrayRecord['dosOfScan'] = $dosScan;
	$arrayRecord['stub_id'] = $ptStubId;

	$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
	
	
	//START CODE FOR PDF FILE
	if($pConfirmId) {
		// GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
			$surgeonData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
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

	$pdfFolderName = '../../../../pdfFiles/'.$surgeonName;
	//$pdfFolderName = realpath(dirname(__FILE__).'/../../../../pdfFiles/'.$surgeonName);		
	$pdfFolderNameSave = 'pdfFiles/'.$surgeonName;
	
	if(is_dir($pdfFolderName)) {
		//DO NOT CREATE FOLDER AGAIN
	}else {
		mkdir($pdfFolderName, 0777);
	}
	if(strtolower($imageType) == 'application/pdf') {
		$pdfJpgFilePathDatabaseSave = $pdfFolderNameSave."/".$inserIdScanUpload.".pdf";
	}else {
		$pdfJpgFilePathDatabaseSave = $pdfFolderNameSave."/image_".$inserIdScanUpload.".jpg";
	}
	$pdfJpgFileFullPath 		= $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$pdfJpgFilePathDatabaseSave;
	$fContent 					= file_get_contents($tempPath);
	@file_put_contents($pdfJpgFileFullPath,$fContent);		
	unset($arrayRecord);
	$arrayRecord['pdfFilePath'] = $pdfJpgFilePathDatabaseSave;
	$updtScanUpldTbl 			= $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl', 'scan_upload_id', $inserIdScanUpload);

	
	//END CODE FOR PDF FILE
	$files[$relativePath] = $tempPath;
}

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