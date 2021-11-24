<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
﻿<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once("../common/conDb.php");
include_once("classObjectFunction.php");
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='surgerycenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
if(!$iolinkWebrootDirectoryName) 		{ $iolinkWebrootDirectoryName=$iolinkDirectoryName;					}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<link rel="stylesheet" href="../css/form.css" type="text/css" />
<link rel="stylesheet" href="../css/style_surgery.css" type="text/css" />

<?php
$spec= "
</head>
<body>";
include("../common/link_new_file.php");
$objManageData = new manageData;

//print '<pre>';
//print_r($_REQUEST);

$patient_id=$_REQUEST['patient_id'];
$patient_in_waiting_id=$_REQUEST['patient_in_waiting_id'];


$pConfirmId = $_REQUEST['pConfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];


$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];

$scaniOLinkConsentId=$_REQUEST['scaniOLinkConsentId'];
$CONSENTScan = $_REQUEST['CONSENTScan'];

$insuranceType = $_REQUEST['insuranceType'];
$INSURANCEScan = $_REQUEST['INSURANCEScan'];


$scanPtInfo = $_REQUEST['scanPtInfo'];
$scanClinical = $_REQUEST['scanClinical'];
$scanIOLFolder = $_REQUEST['scanIOLFolder'];
$scanHP = $_REQUEST['scanHP'];
$scanEKG = $_REQUEST['scanEKG'];
$scanHealthQuest = $_REQUEST['scanHealthQuest'];
$scanOcularHx = $_REQUEST['scanOcularHx'];
$scanAnesthesiaConsent = $_REQUEST['scanAnesthesiaConsent'];

$consentAllMultipleId = $_REQUEST['consentAllMultipleId'];

//START CODE TO SET STATIC FOLDER NAME
if($scanPtInfo) 				 {	$folderName = 'Patient Information';
}else if($scanClinical) 		 {	$folderName = 'Clinical';
}else if($scanIOLFolder) 		 {	$folderName = 'IOL';
}else if($scanHP) 				 {	$folderName = 'H&P'; 
}else if($scanEKG) 				 {	$folderName = 'EKG';
}else if($scanHealthQuest) 		 {	$folderName = 'Health Quest';
}else if($scanOcularHx) 		 {	$folderName = 'Ocular Hx';
}else if($scanAnesthesiaConsent) {	$folderName = 'Consent';
} 
//END CODE TO SET STATIC FOLDER NAME 

//START CODE TO TRACK PREVIOUS DOCUMENTS
if($scanHP || $scanEKG || $scanPtInfo || $scanClinical || $scanIOLFolder || $scanHealthQuest || $scanOcularHx) {
	$scanPrevHPEkg = 'h&p'; //DEFAULT SETTING
	
	if($scanHP) 				{ $scanPrevHPEkg = 'h&p'; 
	}else if($scanEKG) 			{ $scanPrevHPEkg = 'ekg'; 
	}else if($scanPtInfo) 		{ $scanPrevHPEkg = 'ptInfo'; 
	}else if($scanClinical) 	{ $scanPrevHPEkg = 'clinical'; 
	}else if($scanIOLFolder) 	{ $scanPrevHPEkg = 'iol'; 
	}else if($scanHealthQuest) 	{ $scanPrevHPEkg = 'healthQuest'; 
	}else if($scanOcularHx) 	{ $scanPrevHPEkg = 'ocularHx'; 
	}
	$prevDosQry = "SELECT DISTINCT(DATE_FORMAT(piwt.dos,'%m-%d-%Y')) as dosShow,piwt.patient_in_waiting_id as previousWaitingId FROM patient_in_waiting_tbl piwt,iolink_scan_consent isc 
					WHERE piwt.dos < (SELECT dos FROM patient_in_waiting_tbl WHERE patient_in_waiting_id = '".$patient_in_waiting_id."' AND iolink_scan_folder_name='".$scanPrevHPEkg."')
					AND piwt.patient_id='".$patient_id."'
					AND piwt.patient_in_waiting_id = isc.patient_in_waiting_id ORDER BY piwt.dos DESC";
	$prevDosRes = imw_query($prevDosQry) or die(imw_error());
}
//END CODE TO TRACK PREVIOUS DOCUMENTS

$admin = $_REQUEST['admin'];

$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$web_RootDirectoryName = "surgerycenter";
$webServerRootDirectoryName = "d:/XAMPP/xampp/htdocs/";
$jBOSSServerIP = $_SERVER['HTTP_HOST']; //"127.0.0.1";
$jBOSSServerPort = ":8089";
$phpServerIP = $_SERVER['HTTP_HOST']; //"127.0.0.1";
$phpServerPort = "";
function getAddress(){
    /*** check for https ***/
    $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
    /*** return the full address ***/
    return $protocol.'://';
 }
$phpHTTPProtocol = getAddress();
$jBossHTTPProtocol = getAddress();

/*
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
	$phpHTTPProtocol="https://";
	$jBossHTTPProtocol="https://";
}else{
	$phpHTTPProtocol="http://";
	$jBossHTTPProtocol="http://";
}
*/

$phpServerIP=$_SERVER['HTTP_HOST'];
$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
$GLOBALS['jboss_server'] = $jBossHTTPProtocol.$jBOSSServerIP.$jBOSSServerPort."/iMedicServices/RequestControllerServlet";
$formName = $_REQUEST['formName'];
	$formArray = explode(',', $formName);
		$insertId = $formArray[1];
$folderId = $_REQUEST['folderId'];
$delFolders = $_REQUEST['foldersId'];
if($delFolders){
	foreach($delFolders as $delFolderIds){
		$objManageData->delRecord('scan_documents', 'document_id', $delFolderIds);
		$objManageData->delRecord('scan_upload_tbl', 'document_id', $delFolderIds);
	}
}
if($folderId){
	$folderDetails = $objManageData->getRowRecord('scan_documents', 'document_id', $folderId);
	$folderName = $folderDetails->document_name;
}



if($formName==''){
	$formName = 'Surgery';
}

if($_REQUEST['insertId']){
	$formNameNew = $_REQUEST['formNameNew'];
	$insertId = $_REQUEST['insertId'];
	$formName = $formNameNew.','.$insertId;
}

// DELETE SELECTED IMAGES
$imagesDelArr = $_REQUEST['images'];
if($imagesDelArr){
	foreach($imagesDelArr as $imageId){
		$objManageData->delRecord('scan_upload_tbl', 'scan_upload_id', $imageId);
	}
}

//INSERT FOLDERS IF NOT ALREADY EXIST
	if($patient_id && !$pConfirmId) {
		$formFolderExistArr = array('Pt. Info', 'Clinical','IOL');
		foreach($formFolderExistArr as $formExistFolder){
			unset($conditionArr);
			$conditionArr['document_name'] = $formExistFolder;
			$conditionArr['patient_id'] = $patient_id;
			$conditionArr['confirmation_id'] = '0';
			$getFolderExistDetails = $objManageData->getMultiChkArrayRecords('scan_documents', $conditionArr);
			if(count($getFolderExistDetails)<=0){
				unset($arrayRecord);
				$arrayRecord['patient_id'] = $patient_id;
				$arrayRecord['document_name'] = $formExistFolder;
				//$inserExistId = $objManageData->addRecords($arrayRecord, 'scan_documents');
			
				//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
				$document_encounter='';
				if($formExistFolder=='Pt. Info') {
					$document_encounter = 'pt_info_1';
				}else if($formExistFolder=='Clinical') {
					$document_encounter = 'clinical_1';
				}else if($formExistFolder=='IOL') {
					$document_encounter = 'iol_1';
				}
				unset($arrayRecord);
				$arrayRecord['patient_id'] = $patient_id;
				$arrayRecord['document_name'] = $formExistFolder;
				$arrayRecord['document_id'] = $inserExistId;
				$arrayRecord['document_date_time'] = date('Y-m-d H:i:s');
				$arrayRecord['document_file_name'] = 'scanPopUp.php';
				$arrayRecord['document_encounter'] = $document_encounter;
				//$inserIdScanLogTbl = $objManageData->addRecords($arrayRecord, 'scan_log_tbl');
				//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
			
			}
		}
	}	
//END INSERT FOLDERS IF NOT ALREADY EXIST

// SHOW SELECTED FOLDER IMAGES
if($pConfirmId || $patient_id){ 
	if($folder){
		unset($conditionArr);
		if($patient_id && !$pConfirmId) {
			$conditionArr['patient_id'] = $patient_id;
			$conditionArr['confirmation_id'] = '0';
		}else {
			$conditionArr['confirmation_id'] = $pConfirmId;
		}
		$conditionArr['document_name'] = $folder;
		$getFolderId = $objManageData->getMultiChkArrayRecords('scan_documents', $conditionArr);
		if($getFolderId) {
			foreach($getFolderId as $foldersInfo){
				$folder_id = $foldersInfo->document_id;
			}
		}	
	}
}
// INSERT NEW FOLDER
$newFolderName = $_REQUEST['newFolder'];
$newDirectory = $_REQUEST['newDirectory'];

if($newDirectory){
	$pConfirmId = $_REQUEST['pConfirmId'];
	if($pConfirmId){
	
		$insertFolderQry = imw_query("INSERT INTO scan_documents SET 
										confirmation_id = '$pConfirmId',
										document_name = '$newDirectory'") or die(imw_error());
		//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
		if($newDirectory=='Pt. Info' || $newDirectory=='Clinical' || $newDirectory=='IOL' ) {
			$document_encounter='';
			if($newDirectory=='Pt. Info') {
				$document_encounter = 'pt_info_2';
			}else if($newDirectory=='Clinical') {
				$document_encounter = 'clinical_2';
			}else if($newDirectory=='IOL') {
				$document_encounter = 'iol_2';
			}
			$insert_scan_log_qry1 = "insert into `scan_log_tbl` set 
										document_id = '".imw_insert_id()."',
										document_name = '".$newDirectory."',
										confirmation_id = '".$pConfirmId."',
										document_date_time = '".date('Y-m-d H:i:s')."',
										document_file_name = 'scanPopUp.php',
										document_encounter = '".$document_encounter."'
										";
			$insert_scan_log_res1 = imw_query($insert_scan_log_qry1) or die(imw_error());
		}
		//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
	
	}else if($patient_id) {
		$insertFolderQry = imw_query("INSERT INTO scan_documents SET 
										patient_id = '$patient_id',
										document_name = '$newDirectory'") or die(imw_error());
		
		//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
		if($newDirectory=='Pt. Info' || $newDirectory=='Clinical' || $newDirectory=='IOL' ) {
			$document_encounter='';
			if($newDirectory=='Pt. Info') {
				$document_encounter = 'pt_info_3';
			}else if($newDirectory=='Clinical') {
				$document_encounter = 'clinical_3';
			}else if($newDirectory=='IOL') {
				$document_encounter = 'iol_3';
			}
			
			$insert_scan_log_qry2 = "insert into `scan_log_tbl` set 
										document_id = '".imw_insert_id()."',
										document_name = '".$newDirectory."',
										patient_id = '".$patient_id."',
										document_date_time = '".date('Y-m-d H:i:s')."',
										document_file_name = 'scanPopUp.php',
										document_encounter = '".$document_encounter."'
										";
			$insert_scan_log_res2 = imw_query($insert_scan_log_qry2) or die(imw_error());
		}
		//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
	
	}else{/*
		$insertFolderQry = imw_query("INSERT INTO scan_documents SET										
										document_name = '$newDirectory'") or die(imw_error());
		*/
	}
}

//SET BG COLOR OF SCAN IMAGE IN SCHEDULER

	//START CODE TO GET MULTIPLE STUB ID OF A PATIENT 
	
		$patientDataStr = "SELECT * FROM patient_data_tbl 
								WHERE patient_id = '$patient_id'";
		$patientDataQry = imw_query($patientDataStr);
		$patientDataRow = imw_fetch_array($patientDataQry);
		$patient_fname  = $patientDataRow['patient_fname'];
		$patient_mname  = $patientDataRow['patient_mname'];
		$patient_lname  = $patientDataRow['patient_lname'];
		$date_of_birth  = $patientDataRow['date_of_birth'];
								
		$stubDataStr = "SELECT * FROM stub_tbl 
								WHERE patient_first_name = '".addslashes($patient_fname)."'
								AND patient_middle_name = '".addslashes($patient_mname)."'
								AND patient_last_name = '".addslashes($patient_lname)."'
								AND patient_dob = '".$date_of_birth."'";
		$stubDataQry = imw_query($stubDataStr) or die(imw_error());
		$stubDataNumRows = imw_num_rows($stubDataQry);
		
		$ptStubIdTemp = array();
		if($stubDataNumRows>0){
			while($stubDataRow = imw_fetch_array($stubDataQry))
				$ptStubIdTemp[] = $stubDataRow['stub_id'];
		
		}
	//END CODE TO GET MULTIPLE STUB ID OF A PATIENT 
	
	//CODE TO REMOVE BG COLOR OF SCAN BUTTONS OF A PATIENT IN SCHEDULER (IF NO FILE IS UPLOADED)
		$chk_folderFilesRes = imw_query("SELECT * FROM scan_upload_tbl WHERE patient_id = '$patient_id' AND confirmation_id = '$pConfirmId'") or die(imw_error());
		$chk_folderFilesNumRow = imw_num_rows($chk_folderFilesRes);
		if($chk_folderFilesNumRow>0) {
			//DO NOTHING
		}else  {
			if($ptStubIdTemp) {
				foreach($ptStubIdTemp as $ptStubId) {
		?>
					 <script>
						var ptStubId = '<?php echo $ptStubId;?>';
						//alert(opener.document.getElementById('scan_bgId'+patient_id));
						if(opener) {
							if(opener.document.getElementById('scan_bgId'+ptStubId)) {
								opener.document.getElementById('scan_bgId'+ptStubId).className='';
							}
						}
					</script> 
		
		<?php
				}
			}
		}
	//END CODE TO REMOVE BG COLOR OF SCAN BUTTONS OF A PATIENT IN SCHEDULER (IF NO FILE IS UPLOADED)
	
//END SET BG COLOR OF SCAN IMAGE IN SCHEDULER
// UPLOAD IMAGE
if(!empty($_FILES["uploadFile"]["name"])){
	
	$imageName = $_FILES["uploadFile"]["name"];
	$imageName = str_replace(" ","-",$imageName);
	$imageName = str_replace(",","-",$imageName);
	
	
	$tmp = $_FILES["uploadFile"]["tmp_name"];
	$imageType = $_FILES["uploadFile"]["type"];
	$PSize = $_FILES["uploadFile"]["size"];
	$oTempFile = fopen($_FILES["uploadFile"]["tmp_name"], "r");		
	$image = fread($oTempFile, $PSize);
	
	$parentFolder = $_REQUEST['parentFolder'];
	
	unset($arrayRecord);
	$arrayRecord['image_type'] = $imageType;
	//$arrayRecord['img_content'] = $image;
	$arrayRecord['document_name'] = $imageName;
	$arrayRecord['document_size'] = $PSize;
	$arrayRecord['confirmation_id'] = $pConfirmId;
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['document_id'] = $folderId;
	
	//CODE FOR PDF FILE OR OTHER FILE
	if($imageType == 'application/pdf'){
		//DO NOTHING
	}else {
		//SAVE IMAGE
		$image = addslashes($image);
		$arrayRecord['img_content'] = $image;
	}
	//END CODE FOR PDF FILE OR OTHER FILE
	
	if($parentFolder){
		$arrayRecord['parent_sub_doc_id'] = $parentFolder;
		//GET INSERT OR UPDATE
		$existSubImage = $objManageData->getRowRecord('scan_upload_tbl', 'parent_sub_doc_id', $parentFolder);
		if($existSubImage){
			$objManageData->updateRecords($arrayRecord, 'scan_upload_tbl', 'parent_sub_doc_id', $parentFolder);
		}else{
			//$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');		
		}
	}else{
		//$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
	}

	//CODE FOR PDF FILE OR OTHER FILE
	if($imageType == 'application/pdf'){
		
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

		$pdfFolderName 		= 'pdfFiles/'.$surgeonName;
		//$pdfFolderName 		= realpath(dirname(__FILE__).'/pdfFiles/'.$surgeonName);	
		$pdfFolderNameSave 	= 'pdfFiles/'.$surgeonName;
		if(is_dir($pdfFolderName)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($pdfFolderName, 0777);
		}
		
		$pdfFilePath = $pdfFolderName."/".$inserIdScanUpload.".pdf";
		$pdfFilePathSave = $pdfFolderNameSave."/".$inserIdScanUpload.".pdf";
		unset($arrayRecord);
		$arrayRecord['pdfFilePath'] = $pdfFilePathSave;
		$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl', 'scan_upload_id', $inserIdScanUpload);
		
		if(is_dir($pdfFolderName)) {
			$fileOpen = fopen($pdfFilePath,"w");
			$getdata = fwrite($fileOpen,$image);
			fclose($fileOpen);
		}
	
	}else {
		//DO NOTHING
	}
	//END CODE FOR PDF FILE OR OTHER FILE
	
	//SET BG COLOR OF SCAN BUTTON IN SCHEDULER
	if($ptStubIdTemp) {
		foreach($ptStubIdTemp as $ptStubId) {
	
	?>
			 <script>
				var ptStubId = '<?php echo $ptStubId;?>';
				//alert(opener.document.getElementById('scan_bgId'+patient_id));
				if(opener.document.getElementById('scan_bgId'+ptStubId)) {
					opener.document.getElementById('scan_bgId'+ptStubId).className='tab_bg';
				}
			</script> 
	<?php
		}
	}
	//END SET BG COLOR OF SCAN BUTTON IN SCHEDULER		
}

// GET PATIENT DATA FOR GIVEN CONFIRMATION ID
$patientData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
$patient_tbl_id = $patientData->patientId;
$ascId = $patientData->ascId;
if(!$patient_tbl_id) {
	$patient_tbl_id = $_REQUEST['patient_id'];
}
$getPatientNameQry = imw_query("SELECT patient_fname,patient_mname,patient_lname
						FROM patient_data_tbl WHERE patient_id = '$patient_tbl_id'");
$getPatientNameNumRow = imw_num_rows($getPatientNameQry);
if($getPatientNameNumRow>0) {
	$getPatientNameRow = imw_fetch_array($getPatientNameQry);
	$patientFName = $getPatientNameRow['patient_fname'];
	$patientMName = $getPatientNameRow['patient_mname'];
	$patientLName = $getPatientNameRow['patient_lname'];
}
if($patientMName) {
	$patientMName = ' '.$patientMName;
}
$patientName = $patientLName.', '.$patientFName.$patientMName;

?>

<script src="../js/epost.js"></script>
<script>
var xmlHttp;
function showFolderContents(id){
	//document.getElementById('closeButton').style.display = 'block';	
	document.getElementById('selectedFolder').value = 'true';
	document.getElementById('folderId').value = id;
	document.newFolderName.submit();
}
function showFoldersFn(){	
	document.showAddFolders.submit();
}
function showMultiUpload(){	
	document.frm_AddMultiUpload.submit();
}


function scanDocsFn(id){
	document.getElementById('imagesTr').style.display = 'none';
	document.getElementById('scanBtn').style.display = 'none';	
	document.getElementById('multiUploadBtn').style.display = 'none';	
	
	document.getElementById('scanTr').style.display = 'block';
	document.getElementById('uoloadTr').style.display = 'block';
	document.getElementById('saveButtonDispId').style.display = 'block';		
	document.getElementById('deleteImage').style.display = 'none';
	document.getElementById('delImageBtn').style.display = 'none';
	document.getElementById('closeButton').style.display = 'block';		
}
function ShowImage(id, type){
	window.open('scanedImage.php?id='+id+'&type='+type,'scanImg', 'menubar=1, top=5, left=10, width=1000, height=650, resizable=1, scrollbars=1');
}
function submitFrm(){
	var objValue = document.newFolderName.newDirectory.value;
	if((objValue == '') || (objValue == ' ')){
		alert('Please enter folder name.')
	}else{
		document.newFolderName.submit();
	}
}
function delFolder(){
	var flag = 0;
	var obj = document.getElementsByName('foldersId[]');
	var objLen = obj.length;	
	for(i=0;i<objLen;i++){
		if(obj[i].checked == true){
			++flag;
		}
	}
	if(flag==0){
		alert('Please select folder to delete.')
	}else{
		var ask = confirm("Are you sure to delete the folder.");
		if(ask == true){
			document.deleteFolderFrm.submit();
		}
	}
}
function delSelectedImage(){
	var flag = 0;
	var obj = document.getElementsByName('images[]');
	var objLen = obj.length;	
	for(i=0;i<objLen;i++){
		if(obj[i].checked == true){
			++flag;
		}


	}
	if(flag==0){
		alert('Please select image to delete.')
	}else{
		var ask = confirm("Delete file – Are you sure?");
		if(ask == true){
			document.imageFrm.submit();
		}
	}
}

function displayScanedImage() {
	//top.location.reload();
	if(opener) {
		if(opener.document.getElementById('uploadBtn')){
			opener.document.getElementById('uploadBtn').click();
		}
		if(opener.top.iframeHome) {
			if(opener.top.iframeHome.iOLinkBookSheetFrameId) {
				opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();	
			}
		}	
	}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function abc(){
var ultags=document.getElementById('treemenu1').getElementsByTagName("ul");
for(i=0;i<ultags.length;i++)
ddtreemenu.expandSubTree('treemenu1',ultags[i]);
}

//START SCRIPT TO CLOSE THE OPENED CONSENT WINDOW
/*
var scaniOLinkConsentId = '<?php echo $scaniOLinkConsentId;?>';
if(scaniOLinkConsentId) {
	if(opener) {
		opener.close();
	}
}
*/
//END SCRIPT TO CLOSE THE OPENED CONSENT WINDOW

function copyHpEKgAjaxFun(currentWatingId,folderName) {
	var previousWaitingId='';
	var folderNameShow = folderName;
	if(folderName=='H&P') { folderNameShow ='HP';}
	if(document.getElementById('previousWaitingId')) {
		previousWaitingId = document.getElementById('previousWaitingId').value	
		if(!previousWaitingId) {
			alert('Please select DOS to copy documents.');
			document.getElementById('previousWaitingId').focus();	
		}
		if(previousWaitingId) {
			if(confirm('Copy previous document(s) of '+folderName+' ! Are you sure.')) {
				xmlHttp=GetXmlHttpObject()
				if (xmlHttp==null){
					alert ("Browser does not support HTTP Request")
					return
				 }
				var url="copyHpEKgAjax.php"
				url=url+"?previousWaitingId="+previousWaitingId
				url=url+"&currentWatingId="+currentWatingId
				url=url+"&folderName="+folderNameShow
				xmlHttp.onreadystatechange=function() {
					if(xmlHttp.readyState==1) {
						if(top.document.getElementById("divScanAjaxLoadId")) {
							top.document.getElementById("divScanAjaxLoadId").style.display='block';
						}
					}
					if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
						if(top.document.getElementById("divScanAjaxLoadId")) {
							top.document.getElementById("divScanAjaxLoadId").style.display='none';
						}						
						alert(xmlHttp.responseText);
						if(top.consent_tree) {
							top.consent_tree.location.reload();
						}
					} 
				};
				xmlHttp.open("GET",url,true)
				xmlHttp.send(null)
			}
		}
	}
}
</script>
<?php 
	
	if($_REQUEST['IOLScan'] == 'true' || $_REQUEST['DISCHARGEScan'] == 'true' || $_REQUEST['CONSENTScan'] == 'true' 
		|| $_REQUEST['admin']=='true' || $_REQUEST['scanPtInfo'] == 'true' || $_REQUEST['scanClinical'] == 'true' || $_REQUEST['scanIOLFolder'] == 'true' 
		|| $_REQUEST['INSURANCEScan'] == 'true' || $_REQUEST['scanHP'] == 'true' || $_REQUEST['scanEKG'] == 'true'
		|| $_REQUEST['scanHealthQuest'] == 'true' || $_REQUEST['scanOcularHx'] == 'true' || $_REQUEST['scanAnesthesiaConsent'] == 'true'){
		$selectedFolder = 'true';		
	}
	if($selectedFolder == "true"){	
	?>
	<form action="scanPopUp.php" method="post" name="showAddFolders">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId; ?>">
		<input type="hidden" name="consentAllMultipleId" value="<?php echo $consentAllMultipleId; ?>">
		
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
	</form>
	<form action="upload_multi_docs.php" method="post" name="frm_AddMultiUpload">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
		<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
		
		<input type="hidden" name="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId; ?>">
		<input type="hidden" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
		<input type="hidden" name="scanPtInfo" value="<?php echo $scanPtInfo; ?>">
		<input type="hidden" name="scanClinical" value="<?php echo $scanClinical; ?>">
        <input type="hidden" name="scanIOLFolder" value="<?php echo $scanIOLFolder; ?>">
		<input type="hidden" name="scanHP" value="<?php echo $scanHP; ?>">
		<input type="hidden" name="scanEKG" value="<?php echo $scanEKG; ?>">
		<input type="hidden" name="scanHealthQuest" value="<?php echo $scanHealthQuest; ?>">
		<input type="hidden" name="scanOcularHx" value="<?php echo $scanOcularHx; ?>">
		<input type="hidden" name="scanAnesthesiaConsent" value="<?php echo $scanAnesthesiaConsent; ?>">
		<input type="hidden" name="consentAllMultipleId" value="<?php echo $consentAllMultipleId; ?>">
		
	</form>
	
	<table style="width:100%; padding:2px; border:none;">
		<tr style="height:25px;">
			<td class="text_10b alignLeft">
				<table class="table_collapse" style="border:none;">
					<tr> 
						<?php if($folderName=='Pt. Info'){ $folderName = 'Patient Information';  }?>
						<td class="text_10b alignLeft" style="width:30%;"><?php if($selectedFolder) { echo '<b>Scan Documents</b>'; }else {echo '<b><a href="#" style="text-decoration:none;" class="text_10b" onClick="return showFoldersFn();">Scan Documents</a></b>';} ?></td>
						<td class="text_10b alignCenter" style="width:30%;"><?php echo $folderName; ?></td>
						<td class="text_10b alignRight" style="padding-right:20px;">
							<?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?>
						</td>
					</tr>
				</table>						
			</td>
		</tr>
		<!-- SCAN -->
		<tr id="scanTr" class="valignTop" style="display:none; height:500px;">
			<td class="alignCenter" style="width:20%;">
				
					<?php
					$uploadfileNamePath = "upload_scan_documents.php";
					?>
					<!--<object type="application/x-java-applet"  style="width:960px; height:500px;">
                    	<param name="code" value="com.tripatinfoways.util.jtwain.web.UploadApplet.class" />
                      	<param name="codebase" value="applet" />
                        <param name="archive" value="program.jar, JTwain.jar" />
						<param name="DOWNLOAD_URL" value="<?php echo $GLOBALS['php_server']."/$iolinkWebrootDirectoryName/admin/applet/AspriseJTwain.dll";?>">
						<param name="DLL_NAME" value="AspriseJTwain.dll">														
                        <param name="UPLOAD_URL" value="<?php echo $GLOBALS['php_server']."/$iolinkWebrootDirectoryName/".$uploadfileNamePath."?method=upload&amp;pconfirmId=$pConfirmId&amp;patient_id=$patient_id&amp;ptStubId=$ptStubId&amp;patient_in_waiting_id=$patient_in_waiting_id&amp;scanPtInfo=$scanPtInfo&amp;scanClinical=$scanClinical&amp;scanIOLFolder=$scanIOLFolder&amp;scanHP=$scanHP&amp;scanEKG=$scanEKG&amp;scanHealthQuest=$scanHealthQuest&amp;scanOcularHx=$scanOcularHx&amp;scanAnesthesiaConsent=$scanAnesthesiaConsent&amp;formName=$formName&amp;folderId=$folderId&amp;scanIOL=$scanIOL&amp;IOLScan=$IOLScan&amp;scanDISCHARGE=$scanDISCHARGE&amp;DISCHARGEScan=$DISCHARGEScan&amp;&amp;scaniOLinkConsentId=$scaniOLinkConsentId&amp;&amp;CONSENTScan=$CONSENTScan&amp;admin=$admin&amp;INSURANCEScan=$INSURANCEScan&amp;insuranceType=$insuranceType";?>">
                        <param name="UPLOAD_PARAM_NAME" value="file[]">
						<param name="UPLOAD_EXTRA_PARAMS" value="A=B">
						<param name="UPLOAD_OPEN_URL" value="http://asprise.com/product/jtwain/applet/fileupload.php">
						<param name="UPLOAD_OPEN_TARGET" value="_blank">
						Oops, Your browser does not support Java applet!
                    </object>-->                    
                <?php
				echo "<script>multiScan='yes';no_of_scans=20;uploadScanURL = '".$GLOBALS['php_server']."/".$iolinkWebrootDirectoryName."/".$uploadfileNamePath."?method=upload&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&patient_in_waiting_id=$patient_in_waiting_id&scanPtInfo=$scanPtInfo&scanClinical=$scanClinical&scanIOLFolder=$scanIOLFolder&scanHP=$scanHP&scanEKG=$scanEKG&scanHealthQuest=$scanHealthQuest&scanOcularHx=$scanOcularHx&scanAnesthesiaConsent=$scanAnesthesiaConsent&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan&&scaniOLinkConsentId=$scaniOLinkConsentId&CONSENTScan=$CONSENTScan&admin=$admin&INSURANCEScan=$INSURANCEScan&insuranceType=$insuranceType';</script>";
				include_once("scan_control.php");?>
				
			</td>
		</tr>
		<tr>
			<td style="padding-left:100px;"><span class="text_10b"></span></td>
		</tr>		
		
		<!-- SCAN -->

		<!-- UPLOAD -->
		<tr id="uoloadTr" class="valignMiddle" style="display:none; height:50px;">
		  <td class="alignLeft" style="padding-left:85px;">				
            <form name="uploadImageFrm" action="scanPopUp.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                <input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
                <input type="hidden" name="folderId" value="<?php echo $folderId; ?>">		
                <input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
                <input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
                <input type="hidden" name="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId; ?>">
                <input type="hidden" name="admin" value="<?php echo $admin; ?>">
                <input type="hidden" name="parentFolder" value="<?php echo $sub_Docs_Id; ?>"> 
				
                <table class="table_pad_bdr" style="border:none;">
					<tr>
						<td class="alignCenter valignTop"><span class="text_10b">Upload File :</span><input style="width:100px;" type="file" class="text_9" name="uploadFile"></td>
						<td style="width:10px;"></td>
						<td class="alignCenter">
						</td>
					</tr>
				</table>
			 </form>
            </td>			
		</tr>
			
		<!-- UPLOAD -->

		<!-- IMAGES -->
		<tr id="imagesTr" style="height:500px;">
			<td class="alignLeft valignTop">
				<div style="position:absolute;height:500px; width:760px;overflow:auto;">
				<form name="imageFrm" action="scanPopUp.php" method="post">
				<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
				<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
				<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
				<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
				<input type="hidden" name="selectedFolder" value="true">
				<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
				<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
				<input type="hidden" name="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId; ?>">
				<input type="hidden" name="admin" value="<?php echo $admin; ?>">	
					<table class="table_collapse" style="border:none;">
						<tr style="height:25px;">
							<td >&nbsp;</td>
						</tr>
                        <tr>
						<?php
						if($pConfirmId){
							$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE confirmation_id = '$pConfirmId' AND document_id = '$folderId'";
						}else if($patient_id) {
							$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE patient_id = '$patient_id' AND confirmation_id = '0' AND document_id = '$folderId'";
						}else{
							//$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE document_id = '$folderId'";
						}
						if($folderFilesQry) {
							$folderFilesRes = imw_query($folderFilesQry) or die(imw_error());						
							$filesRows = imw_num_rows($folderFilesRes);
						}
						if($filesRows>0){
							while($folderFilesRows = imw_fetch_assoc($folderFilesRes)){
								$scan_upload_id = $folderFilesRows['scan_upload_id'];								
								$document_name = $folderFilesRows['document_name'];
								$image_type = $folderFilesRows['image_type'];
								$pdfFilePathDB = $folderFilesRows['pdfFilePath'];
								$parent_sub_doc_id = $folderFilesRows['parent_sub_doc_id'];								
								if($parent_sub_doc_id!=0)
									continue;
								if($s == 0)	echo '<tr height="75">';
								++$s;								
								?>
								<td class="alignLeft">
									<table class="table_pad_bdr" style="border:1px solid; width:150px; border-color:#FFFFFF;">
										<tr>
											<td class="alignCenter" style="height:150px;">
												<?php
												if($image_type == 'application/pdf'){
													?>													
													<img src="../images/icon-pdf.png" alt="Show PDF" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'pdf');">
													<?php
												}else if($pdfFilePathDB){
													$imageScanedArr[] = $scan_upload_id;
													
													?>
													
													 <img id="imgThumbNail<?php echo $scan_upload_id; ?>" alt="show file" style="width:130px; height:130px; border:none;cursor:pointer;" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image');" src="<?php echo $pdfFilePathDB; ?>"> 
													<?php
												}else{
													$imageScanedArr[] = $scan_upload_id;
													
													?>
													
													 <img id="imgThumbNail<?php echo $scan_upload_id; ?>" alt="show file" style="width:130px; height:130px; border:none;cursor:pointer;" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image');" src="logoImg.php?from=ScanPopUP&imageId=<?php echo $scan_upload_id; ?>"> 
													<?php
												}
												?>
											</td>
										</tr>
										<tr>
											<td class="text_10b alignCenter">
												<input type="checkbox" name="images[]" value="<?php echo $scan_upload_id; ?>">
												<?php echo wordwrap($document_name,18,"\n",1);//substr($document_name, 0, 20); ?>
											</td>
										</tr>
									</table>
								</td>
								<td style="width:25px;">&nbsp;</td>
								<?php
								if($s == 4){	echo '</tr>';  $s = 0; echo "<tr style='height:25px;'><td>&nbsp;</td></tr>"; }
							}
						}else{
							echo '<td class="text_9 alignCenter valignBottom" style="height:250px;"><b>NO Image Found.</b></td>';
						}
						?>
                        </tr>
					</table>
					</form>
				</div>				
			</td>
		</tr>
		<!-- <tr height="2">
			<td>&nbsp;</td>
		</tr> -->
		<tr id="iolRefreshTrId" class="alignCenter" style="display:none; height:15px;">
			 <td>
				<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('iolRefresh','','../images/upload_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('iolRefresh','','../images/upload_hover.gif',1)">
					<img src="../images/upload.gif" style="border:none;"  id="iolRefresh" alt="" onClick="javascript:displayScanedImage();window.close();" />
				</a>

			</td> 
		</tr>
		<tr>
			<td style="width:100%;">
            	<table class="table_collapse" style="border:none;">
                	<tr>
                        <td style="width:33%;">
							<?php
							if($scanHP || $scanIOLFolder || $scanEKG || $scanPtInfo || $scanClinical || $scanHealthQuest || $scanOcularHx) {
								if(imw_num_rows($prevDosRes)>0) {?>
                                    <table class="table_collapse" style="border:none;">
                                        <tr class="alignLeft" style="height:10px;">
                                            <td colspan="2" class="text_10b nowrap" style="font-size:11px; padding-left:40px; padding-right:3px;">Copy Documents From</td>
                                        </tr>  
                                        <tr class="alignLeft">
                                            
                                            <td class="text_10" style="font-size:11px; padding-left:40px; padding-right:3px; width:100px;">
                                                <select name="previousWaitingId" class="field text" id="previousWaitingId"  style=" width:90px;">
                                                    	<option value="">Select</option>
											<?php 	while($prevDosRow = imw_fetch_array($prevDosRes)) {
                                                        $previousWaitingId = $prevDosRow['previousWaitingId'];
														$prevDos = $prevDosRow['dosShow'];?>
                                                        <option value="<?php echo $previousWaitingId;?>"><?php echo $prevDos;?></option>
											<?php	}?>
                                                </select>
                                            </td>
                                            <td> 
                                                <a style="width:120px; padding-left:4px;" href="#" onClick="MM_swapImage('copyButton','','../images/copy_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('copyButton','','../images/copy_hover.gif',1)">
                                                    <img src="../images/copy.gif" id="copyButton" alt="Copy" onClick="copyHpEKgAjaxFun('<?php echo $patient_in_waiting_id;?>','<?php echo $folderName;?>');" style="cursor:pointer; border:none;" />
                                                </a>
                                            </td>
                                        </tr>
                                    </table> 
                            <?php
								}
							}?>   
                        </td>
                        <!--<td id="multiUploadBtnId" class="alignLeft" style="display:none; " >
                            <a style="width:100px; padding-left:65px; padding-top:13px;" href="#" onClick="MM_swapImage('multiUploadImg','','../images/upload_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('multiUploadImg','','../images/upload_hover.gif',1)">
                                <img src="../images/upload.gif" id="multiUploadImg" style="border:none;" alt="" onClick="return showMultiUpload();" />
                            </a>							
                        </td>-->
                    </tr>
                </table>
            </td>    
		</tr>
		<tr id="backDelScan">
			<td class="alignCenter">
				<table class="table_pad_bdr">
					<tr>
						<td class="alignLeft">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('backButton','','../images/back_new_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('backButton','','../images/back_new_hover.gif',1)">
								<img src="../images/back_new.gif" id="backButton" style="border:none;" alt="Back" onClick="return showFoldersFn();" />
							</a>
						</td>
						<td id="saveButtonDispId" class="alignCenter" style="display:none; padding-left:20px;">
							<a href="#" onClick="MM_swapImage('saveButton','','../images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveButton','','../images/save_hover1.jpg',1)">
								<!-- <img src="../images/save.jpg" name="saveButton" id="saveButton" border="0"  alt="Save" onClick="if(document.uploadImageFrm.uploadFile.value!='') { document.uploadImageFrm.submit(); }else { window.open('<?php //echo $GLOBALS['php_server']."/surgerycenter/admin/demoApplet/pdfDemo.php?pconfirmId=$pConfirmId&amp;patient_id=$patient_id&amp;ptStubId=$ptStubId&amp;formName=$formName&amp;folderId=$folderId&amp;scanIOL=$scanIOL&amp;IOLScan=$IOLScan&amp;scanDISCHARGE=$scanDISCHARGE&amp;DISCHARGEScan=$DISCHARGEScan";?>');}"/> -->
								<img src="../images/save.jpg" id="saveButton" style="border:none;" alt="Save" onClick="document.uploadImageFrm.submit();"/>
							</a> 
						</td>
						<td id="scanBtn" class="alignCenter">
							<a style="width:120px; padding-left:45px;" href="#" onMouseOver="MM_swapImage('scanButton','','../images/scan_hover.gif',1)" onMouseOut="MM_swapImgRestore()">
								<img src="../images/scan.gif" style="border:none;" id="scanButton" alt="Scan" onClick="return scanDocsFn();" />
							</a>							
						</td>
						<td id="multiUploadBtn" class="alignCenter">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('multiUpload','','../images/upload_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('multiUpload','','../images/upload_hover.gif',1)">
								<img src="../images/upload232.gif" style="border:none;" id="multiUpload" alt="" onClick="return showMultiUpload();" />
							</a>							
						</td>
						<td id="delImageBtn" class="alignCenter" style="display:none;">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('deleteImage','','../images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteImage','','../images/delete_selected_hover.gif',1)"><img src="../images/delete_selected.gif" id="deleteImage" style="display:none;" alt="Delete" onClick="return delSelectedImage();"/></a>
						</td>
						<td style="width:25px;">&nbsp;</td>
						<td class="alignLeft valignTop">
							<a href="#" onClick="MM_swapImage('closeButton','','../images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','../images/close_hover.gif',1)"><img src="../images/close.gif" id="closeButton" style="border:none;" alt="Close" onClick="window.close();"/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		<!-- IMAGES -->
	</table>
	<?php
	}else{
		?>
		<table class="table_collapse alignCenter" style="border:none;">
			<tr style="height:20px;">
				<td class="alignLeft">
					<table class="table_collapse" style="border:none;">
						<tr id="scanHead">
							<td class="text_10b alignLeft">Scan Documents</td>
							<td class="text_10b alignRight" style="padding-right:20px;">
								<?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="height:20px;"></td>
			</tr>
			<tr>
				<td class="alignCenter">
					<div style="position:relative;height:500px; overflow:auto;">
					<form name="deleteFolderFrm" action="scanPopUp.php" method="post">
					<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
					<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
					<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
					<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
					<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
					<input type="hidden" name="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId; ?>">
					<input type="hidden" name="admin" value="<?php echo $admin; ?>">
					<table class="table_pad_bdr" style="border:none;">
						<?php
						//$formNameArr = array('Consent', 'Ophthalmologist', 'Internist');
						$formNameArr = array('Pt. Info', 'Clinical','IOL');
						if($pConfirmId) {
							$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
						}else if($patient_id) {
							$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE patient_id = '$patient_id' AND confirmation_id = '0'";
						}else {
							//$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
						}
						if($docFolderRecordsStr) {
							$docFolderRecordsQry = imw_query($docFolderRecordsStr) or die(imw_error());
							if(imw_num_rows($docFolderRecordsQry) > 0){
								while($docFolderRecord = imw_fetch_assoc($docFolderRecordsQry)){	
								$document_name = $docFolderRecord['document_name'];
								$document_id = $docFolderRecord['document_id'];					
								if($seq == 0)	echo '<tr>';
								++$seq;
									?>
									<td class="alignCenter" style="width:150px;">
										<table class="table_pad_bdr" style="border:none;">
											<tr>
												<?php if(!in_array( $document_name, $formNameArr)){ ?>
													<td></td>
												<?php } ?>
												<td class="text_10b alignCenter">
													<img src="../images/folder_icon1.gif" name="scanFolder[]" style="cursor:pointer;" onClick="return showFolderContents('<?php echo $document_id; ?>');">
												</td>
											</tr>
											<tr>
												<?php if(!in_array( $document_name, $formNameArr)){ $delButton = true; ?>
													<td style="width:10px;"><input name="foldersId[]" value="<?php echo $document_id; ?>" type="checkbox"></td>
												<?php } ?>
												<td class="text_10 alignCenter" style="cursor:pointer;" onClick="return showFolderContents('<?php echo $document_id; ?>');"><?php echo $document_name; ?></td>
											</tr>
										</table>										
									</td>	
									<?php
								if($seq == 5){	echo '</tr>';  $seq = 0; echo "<tr style='height:25px;'><td>&nbsp;</td></tr>"; }
								
								}								
							}
						}	
						?>
					</table>
					</form>
					</div>
				</td>
			</tr>
			<tr>
				<td class="alignCenter">
					<form action="scanPopUp.php" method="post" name="newFolderName">
					<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">		
					<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">	
					<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">	
					<input type="hidden" name="selectedFolder" value="">
					<input type="hidden" name="folderId" value="">
					<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
					<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
					<input type="hidden" name="scaniOLinkConsentId" value="<?php echo $scaniOLinkConsentId; ?>">
					<input type="hidden" name="admin" value="<?php echo $admin; ?>">
					<table class="table_pad_bdr alignCenter" style="border:none; width:550px;">
						<tr>
							<td class="text_10b alignRight" style="width:163px;">New&nbsp;Folder&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td class="alignLeft" style="width:150px;"><input type="text" size="25" name="newDirectory"></td>
							<td style="width:10px;"></td>
							<td class="alignLeft" style="width:229px;">
							<a href="#" onClick="return submitFrm();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('addNew','','../images/add_new_hover.gif',1)">
								<img src="../images/add_new.gif" id="addNew" style="border:none;"  alt="Add New" onClick=""/>  <!-- return getPageSrc('Add New'); -->
							</a>
							</td>
						</tr>
				  	</table>
					</form>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="alignCenter"><a href="#" onClick="MM_swapImage('deleteSelected','','../images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteSelected','','../images/delete_selected_hover.gif',1)"><img src="../images/delete_selected.gif" id="deleteSelected" style="display:none; border:none;" alt="Delete" onClick="return delFolder();"/></a></td>
			</tr>
		</table>
		<?php
	}

?>

<form name="frmSubDocs" action="scanPopUp.php" method="post">
	<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
	<input type="hidden" name="selectedFolder" value="true">
	<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
	<input type="hidden" name="subDocs" value="<?php echo $sub_Docs_Id; ?>">
</form>
<script>
<?php 

if($_REQUEST['IOLScan'] == 'true' || $_REQUEST['DISCHARGEScan'] == 'true' || $_REQUEST['CONSENTScan'] == 'true' 
	|| $_REQUEST['admin'] == 'true' || $_REQUEST['scanPtInfo'] == 'true' || $_REQUEST['scanClinical'] == 'true'  || $_REQUEST['scanIOLFolder'] == 'true' 
	|| $_REQUEST['INSURANCEScan'] == 'true' || $_REQUEST['scanHP'] == 'true' || $_REQUEST['scanEKG'] == 'true'
	|| $_REQUEST['scanHealthQuest'] == 'true' || $_REQUEST['scanOcularHx'] == 'true' || $_REQUEST['scanAnesthesiaConsent'] == 'true'
	){
	?>
	scanDocsFn();
	document.getElementById('uoloadTr').style.display = 'none';
	document.getElementById('saveButtonDispId').style.display = 'none';
	document.getElementById('backDelScan').style.display = 'none';
	document.getElementById('closeButton').style.display = 'none';
	if(document.getElementById('iolRefreshTrId')) {
		document.getElementById('iolRefreshTrId').style.display = 'none';
	}
	
	<?php
	if($_REQUEST['INSURANCEScan'] != 'true') {
	?>
	if(document.getElementById('multiUploadBtnId')) {
		document.getElementById('multiUploadBtnId').style.display = 'block';
	}
	<?php
	}
}
if($_REQUEST['INSURANCEScan'] == 'true'){
?>
	if(document.getElementById('iolRefreshTrId')) {
		document.getElementById('iolRefreshTrId').style.display = 'block';
	}
<?php
}
if($folder){
	?>
	var id = <?php echo $folder_id; ?>;
	document.getElementById('selectedFolder').value = 'true';
	document.getElementById('folderId').value = id;
	document.newFolderName.submit();
	<?php
}
if($delButton==true){
	?>
		document.getElementById('deleteSelected').style.display = 'block';
	<?php
}
if($filesRows>0){
	?>
	document.getElementById('delImageBtn').style.display = 'block';
	document.getElementById('deleteImage').style.display = 'block';	
	<?php
}
if(count($imageScanedArr)>0){

	foreach($imageScanedArr as $id){
		?>
		var id = <?php echo $id; ?>;
		var imgWidth = document.getElementById('imgThumbNail'+id).width;
		var imgHeight = document.getElementById('imgThumbNail'+id).height;
		var target = 100;
		if((imgHeight>=150) || (imgWidth>=150)){
			if (imgWidth > imgHeight) { 
				percentage = (target/imgWidth); 
			} else { 
				percentage = (target/imgHeight);
			} 
			widthNew = imgWidth*percentage; 
			heightNew = imgHeight*percentage; 	
			document.getElementById('imgThumbNail'+id).height = heightNew;
			document.getElementById('imgThumbNail'+id).width = widthNew;
		}
		<?php
	}
}
?>
if(top.document.getElementById("anchorShow")) {
	top.document.getElementById("anchorShow").style.display = 'none';
}
if(top.document.getElementById("deleteSelected")) {
	top.document.getElementById("deleteSelected").style.display = 'none';
}
if(top.document.getElementById("PrintBtn")) {
	top.document.getElementById("PrintBtn").style.display = 'none';
}
if(top.document.getElementById("iolinkUploadBtn")) {
	top.document.getElementById("iolinkUploadBtn").style.display = 'none';
}
if(top.document.getElementById("iolinkUploadBtn")) {
	top.document.getElementById("iolinkUploadBtn").style.display = 'inline-block';
}
if(top.document.getElementById("multiUploadImgBtn")) {
	top.document.getElementById("multiUploadImgBtn").style.display = 'inline-block';
}
</script>
</body>
</html>