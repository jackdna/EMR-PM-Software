<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../../../../../common/conDb.php");
include("../../../../adminLinkfile.php");
include_once("../../../../classObjectFunction.php");
$objManageData = new manageData;

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
$user_id = $_REQUEST['user_id'];
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
	
	
	$fpath = $fileinfo['name'];
	$fext = array_pop(explode('.', $fpath));
	$original_file=$fileinfo;
	
	$imageName = $fileinfo['name'];
	$imageName = urldecode($imageName);
	$imageName = str_ireplace(" ","-",$imageName);
	$imageName = str_replace(",","-",$imageName);
	
	$tmp 							= $fileinfo['tmp_name'];
	$imageType 						= $fileinfo['type'];
	$PSize 							= $fileinfo['size'];
	$oTempFile 						= fopen($fileinfo['tmp_name'], "r");		
	$image 							= fread($oTempFile, $PSize);
	$parentFolder 					= $_REQUEST['parentFolder'];

	unset($arrayRecord);
	$arrayRecord['image_type'] 		= $imageType;
	$arrayRecord['document_name'] 	= $imageName;
	$arrayRecord['document_size'] 	= $PSize;
	//$arrayRecord['confirmation_id'] = $pConfirmId;
	//$arrayRecord['patient_id'] 		= $patient_id;
	$arrayRecord['document_id'] 	= $folderId;
	//$arrayRecord['dosOfScan'] 		= date("Y-m-d");
	$arrayRecord['save_date_time'] = date("Y-m-d H:i:s");
	$arrayRecord['user_id'] 		= $user_id;
	
	//CODE FOR PDF FILE OR OTHER FILE
	if($imageType == 'application/pdf'){
		//DO NOTHING
	}else {
		//SAVE IMAGE
		$image = addslashes($image);
		$arrayRecord['img_content'] = $image;
	}
	//END CODE FOR PDF FILE OR OTHER FILE
	
	$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl_user');
	
	
	//START CODE FOR PDF FILE
	if($imageType == 'application/pdf'){
		$userData 		= $objManageData->getRowRecord('users', 'usersId', $user_id);
		$userFname 		= $userData->fname;
		$userName 		= $userFname."_".$user_id;
			
		$userName = str_replace(" ","_",$userName);
		$userName = str_replace(",","",$userName);
		$userName = str_replace("!","",$userName);
		$userName = str_replace("@","",$userName);
		$userName = str_replace("%","",$userName);
		$userName = str_replace("^","",$userName);
		$userName = str_replace("$","",$userName);
		$userName = str_replace("'","",$userName);
		$userName = str_replace("*","",$userName);

		$userFolder = '../../../../pdfFiles/user_detail';
		$userFolderSave = 'pdfFiles/user_detail';
		if(is_dir($userFolder)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($userFolder, 0777);
		}
		
		$pdfFolderName = $userFolder.'/'.$userName;
		if(is_dir($pdfFolderName)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($pdfFolderName, 0777);
		}
		
		$pdfFolderNameSave = $userFolderSave.'/'.$userName;		
		
		$pdfFilePath = $pdfFolderName."/".$inserIdScanUpload.".pdf";
		$pdfFilePathDatabaseSave = $pdfFolderNameSave."/".$inserIdScanUpload.".pdf";
		unset($arrayRecord);
		$arrayRecord['pdfFilePath'] = $pdfFilePathDatabaseSave;
		$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl_user', 'scan_upload_id', $inserIdScanUpload);
		
		if(is_dir($pdfFolderName)) {
			$fileOpen = fopen($pdfFilePath,"w");
			$getdata = fwrite($fileOpen,$image);
			fclose($fileOpen);
		}
	
	}
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