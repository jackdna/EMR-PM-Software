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
	$tempPath = $fileinfo['tmp_name'];
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
	//fwrite($aa,$extn." @@ ".$imageType." @@ ".$imageName." @@ ".$PSize." @@ ".$pConfirmId." @@ ".$patient_id." <br> "); //For Debugging
	if($imageType=="image/gif" || $imageType=="image/jpeg" || $imageType=="image/png" || $imageType=="application/pdf"){
		$PSize = $fileinfo['size'];
		unset($arrayRecord);
		$arrayRecord['image_type'] = $imageType;
		$arrayRecord['document_name'] = $imageName;
		$arrayRecord['document_size'] = $PSize;
		$arrayRecord['confirmation_id'] = $pConfirmId;
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['document_id'] = $folderId;
		$arrayRecord['dosOfScan'] = $dosScan;
		$arrayRecord['stub_id'] = $ptStubId;
		$arrayRecord['scan_upload_save_date_time'] = date('Y-m-d H:i:s');
		
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
	
		$pdfFolderName = '../pdfFiles/'.$surgeonName;
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
	}
	//END CODE FOR PDF FILE
}
?>