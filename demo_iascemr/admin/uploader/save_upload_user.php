<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../../common/conDb.php");
include("../adminLinkfile.php");
include_once("../classObjectFunction.php");
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
$user_id = $_REQUEST['user_id'];
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
	$PSize 		= $fileinfo['size'];
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
	//fwrite($aa,$extn." @@ ".$imageType." @@ ".$imageName." @@ ".$PSize." @@ ".$pConfirmId." @@ ".$patient_id." @@ ".$user_id." @@ ".$activex." <br> "); //For Debugging
	if($imageType=="image/gif" || $imageType=="image/jpeg" || $imageType=="image/png" || $imageType=="application/pdf"){
		unset($arrayRecord);
		$arrayRecord['image_type'] 		= $imageType;
		$arrayRecord['document_name'] 	= $imageName;
		$arrayRecord['document_size'] 	= $PSize;
		$arrayRecord['document_id'] 	= $folderId;
		$arrayRecord['save_date_time'] 	= date("Y-m-d H:i:s");
		$arrayRecord['user_id'] 		= $user_id;
		
		$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl_user');
		
		$userData 	= $objManageData->getRowRecord('users', 'usersId', $user_id);
		$userFname 	= $userData->fname;
		$userName 	= $userFname."_".$user_id;
			
		$userName = str_ireplace(" ","_",$userName);
		$userName = str_ireplace(",","",$userName);
		$userName = str_ireplace("!","",$userName);
		$userName = str_ireplace("@","",$userName);
		$userName = str_ireplace("%","",$userName);
		$userName = str_ireplace("^","",$userName);
		$userName = str_ireplace("$","",$userName);
		$userName = str_ireplace("'","",$userName);
		$userName = str_ireplace("*","",$userName);

		$userFolder = '../pdfFiles/user_detail';
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
		$updtScanUpldTbl 			= $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl_user', 'scan_upload_id', $inserIdScanUpload);
	}
}
?>