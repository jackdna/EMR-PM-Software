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
$dosScan = $_REQUEST['dosScan'];
$user_id = $_REQUEST['user_id'];


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
					$arrayRecord['image_type'] 			= 'SCANED';
					$arrayRecord['document_name'] 		= $fileName;
					$arrayRecord['document_type'] 		= $fileType;
					$arrayRecord['document_size'] 		= $PSize;
					$arrayRecord['img_content'] 		= $fileCon;
					$arrayRecord['document_id'] 		= $folderId;
					$arrayRecord['save_date_time'] 		= date("Y-m-d H:i:s");
					$arrayRecord['operator_id'] 		= $_SESSION['loginUserId'];
					$arrayRecord['user_id'] 			= $user_id;
					
					$userFolder = 'admin/pdfFiles/user_detail';
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
					$insertImage = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl_user');
					$jpgFilePathDatabaseSave = $pdfFolderNameSave."/scan_user_".$insertImage.".jpg";
					unset($arrayRecord);
					$arrayRecord['pdfFilePath'] = $jpgFilePathDatabaseSave;
					$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl_user', 'scan_upload_id', $insertImage);
					$jpgFileFullPath = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
					$fContent 		 = file_get_contents($fileNameTemp);
					@file_put_contents($jpgFileFullPath,$fContent);	
					
					fclose($TempFile);
					$message .= $_FILES['file']['name'][$i]." uploaded.<br>";
				}
			}
		}
		if(!$uploads)  $message = "No files selected!";
	}
}
?>