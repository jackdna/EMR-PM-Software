<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<?php 
	session_start();
	include_once("../common/conDb.php");?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<title>Scan Documents</title>
<?php include("adminLinkfile.php"); ?>
<script type="text/javascript" src="../js/jquery-1.11.3.js"></script>
		
		<script>
			$(window).load(function() 
			{
				$(".loader").fadeOut(1000).hide(1000); 
				bodySize();
			});
			$(window).resize(function()
			{
				bodySize();
			});
			
			var bodySize = function()
			{
				var HH	=	$(".header").height();
				var FH	=	$(".footer").height();
				var DH	=	$(window).height();
				var BH	=	DH - ( HH + FH )  - 70;
				//alert('HEader'  + HH + '\n Footer -  ' + FH + '\n Document - ' + DH + '\nBody' + BH);
				
				$(".body").css({'min-height':BH+'px', 'max-height':BH+'px' })
			
			}
			
			//$(window).resize(function(){ size = [1034,630]; window.resizeTo(size[0],size[1]); });
		</script>
</head>
<body>
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
//echo $_SERVER['PHP_SELF'];
//echo "<pre>";
//print_r($_REQUEST);
//$ptStubId = $_REQUEST['ptStubId'];  //TO SET BGCOLOR OF SCAN BUTTON
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
if(!$iolinkWebrootDirectoryName) 		{ $iolinkWebrootDirectoryName=$iolinkDirectoryName;					}
$pConfirmId = $_REQUEST['pConfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];
$dosScan = $_REQUEST['dosScan'];

$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];

$user_id = $_REQUEST['user_id'];
$admin = $_REQUEST['admin'];

$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$web_RootDirectoryName = $surgeryCenterDirectoryName;
$webServerRootDirectoryName = "d:/XAMPP/xampp/htdocs/";
$jBOSSServerIP = $_SERVER['HTTP_HOST']; //"127.0.0.1";
$jBOSSServerPort = ":8089";
$phpServerIP = $_SERVER['HTTP_HOST']; //"127.0.0.1"; 
$phpServerPort = "";
//print '<pre>';
//print_r($_SERVER);
function getAddress(){
    /*** check for https ***/
    $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
    /*** return the full address ***/
    return $protocol.'://';
 }
$phpHTTPProtocol = getAddress();
$jBossHTTPProtocol = getAddress();

if($user_id) {
	$getUsrNameQry = "SELECT fname, mname,lname FROM users WHERE usersId='".$user_id."'";
	$getUsrNameRes = imw_query($getUsrNameQry) or die(imw_error());
	if(imw_num_rows($getUsrNameRes)>0) {
		$getUsrNameRow = imw_fetch_assoc($getUsrNameRes);
		$usrFname = $getUsrNameRow["fname"];
		$usrMname = $getUsrNameRow["mname"];
		$usrLname = $getUsrNameRow["lname"];
		if($usrMname) {
			$usrMname = ' '.$usrMname;
		}
		$usrName = $usrLname.', '.$usrFname.$usrMname;
			
	}
}
//START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
	if($patient_id && $_GET['ptStubId']) {
		unset($arrayStubRecord);
		$arrayStubRecord['patient_id_stub']=$patient_id;
		$objManageData->updateRecords($arrayStubRecord, 'stub_tbl', 'stub_id', $_GET['ptStubId']);
	}
//END CODE TO UPDATE PATIENT-ID IN STUB-TABLE

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
		$objManageData->delRecord('scan_documents_user', 'document_id', $delFolderIds);
		$objManageData->delRecord('scan_upload_tbl_user', 'document_id', $delFolderIds);
	}
}

if($user_id){
	//AUTO CREATE CREDENTIAL FOLDER
	unset($conditionArr);
	$conditionArr['user_id'] = $user_id;
	$conditionArr['document_name'] = "Credential";
	$getCredFolderDetail = $objManageData->getMultiChkArrayRecords('scan_documents_user', $conditionArr);
	if($getCredFolderDetail) {
		foreach($getCredFolderDetail as $credFoldersInfo){
			$credFolderId = $credFoldersInfo->document_id;
		}
	}else {// Auto Create Credential Folder
		$insertFolderQry = imw_query("INSERT INTO scan_documents_user SET 
										save_date_time = '".date("Y-m-d H:i:s")."',
										user_id = '".$user_id."',
										operator_id = '".$_SESSION['loginUserId']."',
										document_name = 'Credential'") or die(imw_error());
		$credFolderId = imw_insert_id();								
	}
	if($_GET['selectedFolder']=='true') { $folderId = $credFolderId; }
}


if($folderId){
	$folderDetails = $objManageData->getRowRecord('scan_documents_user', 'document_id', $folderId);
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
		$objManageData->delRecord('scan_upload_tbl_user', 'scan_upload_id', $imageId);
	}
}

if($user_id){
	// SHOW SELECTED FOLDER IMAGES
	if($folder){
		unset($conditionArr);
		$conditionArr['user_id'] = $user_id;
		$conditionArr['document_name'] = $folder;
		$getFolderId = $objManageData->getMultiChkArrayRecords('scan_documents_user', $conditionArr);
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
	$user_id = $_REQUEST['user_id'];
	if($user_id){ 
		$insertFolderQry = imw_query("INSERT INTO scan_documents_user SET 
										save_date_time = '".date("Y-m-d H:i:s")."',
										user_id = '".$user_id."',
										operator_id = '".$_SESSION['loginUserId']."',
										document_name = '$newDirectory'") or die(imw_error());
	}
}

//SET BG COLOR OF SCAN BUTTON  FOR USER
$chkScnExistQry = "SELECT sdu.document_id FROM scan_documents_user sdu 
					INNER JOIN scan_upload_tbl_user sutu ON sutu.document_id = sdu.document_id 
					WHERE sdu.user_id='".$user_id."'";
$chkScnExistRes = imw_query($chkScnExistQry) or die(imw_error());
$scan_user_class = '';
if(imw_num_rows($chkScnExistRes)>0) { $scan_user_class='tab_bg';}
?>
<script>
	var user_id = '<?php echo $user_id;?>';
	var scan_user_class = '<?php echo $scan_user_class;?>';
	if(opener) {
		if(opener.document.getElementById('scan_user_bgId'+user_id)) {
			opener.document.getElementById('scan_user_bgId'+user_id).className=scan_user_class;
		}
	}
</script> 
<?php	
//END SET BG COLOR OF SCAN BUTTON FOR USER		

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
	//$arrayRecord['confirmation_id'] = $pConfirmId;
	//$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['document_id'] = $folderId;
	//$arrayRecord['dosOfScan'] = "";
	$arrayRecord['save_date_time'] = date("Y-m-d H:i:s");
	
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
		$existSubImage = $objManageData->getRowRecord('scan_upload_tbl_user', 'parent_sub_doc_id', $parentFolder);
		if($existSubImage){
			$objManageData->updateRecords($arrayRecord, 'scan_upload_tbl_user', 'parent_sub_doc_id', $parentFolder);
		}else{
			$objManageData->addRecords($arrayRecord, 'scan_upload_tbl_user');		
		}
	}else{
		$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl_user');
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

		//$pdfFolderName = 'pdfFiles/'.$surgeonName;
		$pdfFolderName 		= realpath(dirname(__FILE__).'/pdfFiles/'.$surgeonName);	
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
		$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl_user', 'scan_upload_id', $inserIdScanUpload);
		
		if(is_dir($pdfFolderName)) {
			$fileOpen = fopen($pdfFilePath,"w");
			$getdata = fwrite($fileOpen,$image);
			fclose($fileOpen);
		}
	
	}else {
		//DO NOTHING
	}
	//END CODE FOR PDF FILE OR OTHER FILE
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




$winSafaricmptbl = $_REQUEST['winSafaricmptbl'];
?>
<script src="../js/jquery.js"></script>
<script type="text/javascript">
//START CODE TO CLOSE THE WINDOW (MODIFIED FOR SAFARI BROWSER)
var winSafaricmptbl = '<?php echo $winSafaricmptbl;?>';
if(winSafaricmptbl=='close') {
	window.close();
}
//START CODE TO CLOSE THE WINDOW (MODIFIED FOR SAFARI BROWSER)

window.focus();
function showFolderContents(id){
	//document.getElementById('closeButton').style.display = 'inline-block';	
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
	//document.getElementById('scanBtn').style.display = 'none';	
	//document.getElementById('multiUploadBtn').style.display = 'none';	
	
	document.getElementById('scanTr').style.display = 'inline-block';
	//document.getElementById('uoloadTr').style.display = 'inline-block';
	//document.getElementById('saveButtonDispId').style.display = 'inline-block';		
	document.getElementById('deleteImage').style.display = 'none';
	//document.getElementById('delImageBtn').style.display = 'none';
	document.getElementById('closeButton').style.display = 'inline-block';		
}
function ShowImage(id, type,from,tblName, filePath){
	var winobj='';
	if(filePath) {
		winobj = window.open(filePath,'scanJpgPdfUser', 'menubar=1, top=5, left=10, width=1000, height=650, resizable=1, scrollbars=1');
		winobj.focus();
	}else {
		window.open('scanedImage.php?id='+id+'&type='+type+'&from='+from+'&tblName='+tblName,'scanImgUser', 'menubar=1, top=5, left=10, width=1000, height=650, resizable=1, scrollbars=1');
	}
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
	if(opener.document.getElementById('uploadBtn')){
		opener.document.getElementById('uploadBtn').click();
	}
}

</script>
<div class="box box-sizing">
 <div class="dialog box-sizing">
 	<div class="content box-sizing">
<?php
	//$selectedFolder = 'true';
	//$folderId='32266432';
	
	if($_GET['IOLScan'] == 'true' || $_GET['DISCHARGEScan'] == 'true' || $_GET['admin']=='true'){
		$selectedFolder = 'true';		
	}
	if($selectedFolder == "true"){	
	?>
	<form action="scanPopUpUser.php" method="post" name="showAddFolders">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
		<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        
		
	</form>
	<form action="upload_multi_docs_user.php" method="post" name="frm_AddMultiUpload">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
		<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
		<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
	</form>
	
	
    
	<div class="header box-sizing text-left ">
        	<h4  onClick="return showFoldersFn();">Scan Doocuments</h4>
			<span class="right-box"><?php echo $usrName;  ?></span>
	</div>
	
    <div class="body">
        
				<div class="folder_wrap">
				
				<a onClick="return showFolderContents('<?php echo $folderDetails->document_id; ?>');" style="text-decoration:none; cursor:pointer" >
						
                        <h5  > <?php echo ($folderName == 'Pt. Info') ? 'Patient Information' : $folderName; ?></h5>
              	
                </a>
				
				<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" id="scanTr" style="display:none;">
                		
                        <?php
							$uploadfileNamePath = "upload_scan_documents_user.php";
							
							echo "<script>multiScan='yes';no_of_scans=20;uploadScanURL = '".$GLOBALS['php_server']."/".$surgeryCenterWebrootDirectoryName."/".$uploadfileNamePath."?method=upload&user_id=$user_id&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$stub_id&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan&scanANESTHESIA=$scanANESTHESIA&ANESTHESIAScan=$ANESTHESIAScan&admin=$admin&dosScan=$dosScan';</script>";
							
							$autoScanPop='yes';
							
							include_once("scan_control.php");
						?>
                        
            	</div>            
				
              
				<form name="uploadImageFrm" action="scanPopUpUser.php" method="post" enctype="multipart/form-data" style="margin:0px;">
                        <input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
                        <input type="hidden" name="folderId" value="<?php echo $folderId; ?>">		
                        <input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
                        <input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
                        <input type="hidden" name="admin" value="<?php echo $admin; ?>">
                        <input type="hidden" name="parentFolder" value="<?php echo $sub_Docs_Id; ?>"> 
                        <input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
				</form>
                
               
            <div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" id="imagesTr">
            
                    <form name="imageFrm" action="scanPopUpUser.php" method="post">
                    <input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                    <input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
                    <input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
                    <input type="hidden" name="selectedFolder" value="true">
                    <input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
                    <input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
                    <input type="hidden" name="admin" value="<?php echo $admin; ?>">	
                    <input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        
                            
                        <?php
                        if($user_id){
                            $folderFilesQry = "SELECT * FROM scan_upload_tbl_user WHERE user_id = '$user_id' AND document_id = '$folderId'";
                        }
                        
                        if($folderFilesQry) {
                            $folderFilesRes = imw_query($folderFilesQry) or die(imw_error());						
                            $filesRows = imw_num_rows($folderFilesRes);
                        }?>
                        <ul class="nav">     
                            
                            <?php
                            if($filesRows>0)
							{
                                $q=0; 
                                while($folderFilesRows = imw_fetch_assoc($folderFilesRes)){
                                    $q++;
                                    $scan_upload_id = $folderFilesRows['scan_upload_id'];								
                                    $document_name = $folderFilesRows['document_name'];
                                    $image_type = $folderFilesRows['image_type'];
                                    $pdfFilePathDB = $folderFilesRows['pdfFilePath'];
                                    $parent_sub_doc_id = $folderFilesRows['parent_sub_doc_id'];								
                                    if($parent_sub_doc_id!=0)
                                        continue;
                                    if($s == 0)	echo '<tr style="height:75px; ">';
                                    ++$s;								
                                    
                                    ?>
                                    <li class="col-lg-2 col-md-3 col-sm-4 col-xs-6 clear-not" >
                                            	<?php
												if($pdfFilePathDB){
													$thumbPath = $pdfFilePathDB;
													$thumHgtWdth = "width:130px; height:130px;";
													if($image_type == 'application/pdf'){
														//$thumbPath = "../images/icon-pdf.png";
														//$thumHgtWdth = "";
														?>
                                                        <i class="fa fa-file-pdf-o pdf-xl" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image','ScanPopUPUser','scan_upload_tbl_user','<?php echo $pdfFilePathDB; ?>');"></i>
                                                        <?php	
													}else {
														$imageScanedArr[] = $scan_upload_id;
														?>
                                                        <img style=" <?php echo $thumHgtWdth; ?> cursor:pointer; border:none;" id="imgThumbNail<?php echo $scan_upload_id; ?>" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image','ScanPopUPUser','scan_upload_tbl_user','<?php echo $pdfFilePathDB; ?>');" src="<?php echo $thumbPath; ?>">
                                                        <?php
													}
													?>
													  
													<?php
												}else if($image_type == 'application/pdf'){
                                                ?>													
                                                			<i class="fa fa-file-pdf-o pdf-xl" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'pdf','<?php echo $pdfFilePathDB; ?>');"></i>
                                            	<?php
													
                                                    }
													else
													{
                                                        $imageScanedArr[] = $scan_upload_id;
                                           		?>
                                                				<img style="width:130px; height:130px; cursor:pointer; border:none;" id="imgThumbNail<?php echo $scan_upload_id; ?>" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image','ScanPopUPUser','scan_upload_tbl_user','');" src="logoImg.php?from=ScanPopUPUser&amp;imageId=<?php echo $scan_upload_id; ?>"> 
												<?php
													
													}
                                             	?>
                                                <label style="display:block">											
                                                        <input type="checkbox" name="images[]" value="<?php echo $scan_upload_id; ?>">
                                                        <?php echo wordwrap($document_name,18,"\n",1);//substr($document_name, 0, 20); ?>
                                                </label>	
                                            
                             		</li>
    
                                    
                                    <?php 
                                   
                                }
                            }
							else{
                                echo '<li style="float:none; text-align:center;"><label style="margin-top:200px">No Image Found.</label></li>';
                            }
                            ?>
                            </ul>
                        </form>
            
            </div>				
		
        
        	</div>
  	</div>
    
    <div class="footer">
			
			<span id="iolRefresh" style="display:none">
					<?php
							$adminLoc	=	''	;
							if($admin=='true') 
							{
									$adminLocReload = 'opener.document.location.reload();';
							}
					?>
                    
                    <a class="btn btn-primary" href="javascript:void(0)" id="iolButtonRefresh"  onclick="if(opener.iframeIOL) {opener.iframeIOL.location.reload();}else { <?php echo $adminLocReload;?>  }displayScanedImage();location.href='scanPopUpUser.php?winSafaricmptbl=close';" >  <b class="fa fa-upload" ></b>&nbsp;Upload</a>
                    
                    <a class="btn btn-primary" href="javascript:void(0)"  id="closeButtonIOL" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
                    
			
			</span>
			
			
      		<span id="backDelScan">     
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return showFoldersFn();" id="backButton" >  <b class="fa fa-arrow-left" ></b>&nbsp;Back</a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  id="saveButton" style="display:none;" >  <b class="fa fa-floppy-o" ></b>&nbsp;Save</a><!--id="saveButtonDispId"-->
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return scanDocsFn();" id="scanButton" >  Scan </a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return showMultiUpload();" id="multiUpload" >  <b class="fa fa-upload" ></b>&nbsp;Upload</a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return delSelectedImage();" id="deleteImage" style="display:none;" >  <b class="fa fa-trash" ></b>&nbsp;Delete</a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="javascript:window.close();" id="closeButton" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
			</span>
   	</div>
        
      
	<?php
	}else{
		?>
		
        <div class="header box-sizing text-left ">
        	<h4  onClick="return showFoldersFn();">Scan Doocuments</h4>
			<span class="right-box"><?php echo $usrName;  ?></span>
		</div>
    	
        <div class="body">
				
                <div class="add_new_scan_wrap">
                
					<form action="scanPopUpUser.php" method="post" name="newFolderName" style="margin:0px;">
                        <input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">		
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">	
                        <input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">	
                        <input type="hidden" name="selectedFolder" id="selectedFolder" value="">
                        <input type="hidden" name="folderId" id="folderId" value="">
                        <input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
                        <input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
                        <input type="hidden" name="admin" value="<?php echo $admin; ?>">
                        <input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
						
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
							<div class="col-md-2 col-lg-2 col-xs-12 col-sm-2 text-right">
								<label for="add_new"  class="text-right">New Folder</label>
							</div>
							
                            <div class="clearfix margin_adjustment_only visible-xs"></div>
                            
							<div class="col-md-6 col-lg-6 col-xs-12 col-sm-6">
								<input class="form-control" type="text"  name="newDirectory" alt="Add New"  />
							</div>
							
                            <div class="clearfix margin_adjustment_only visible-xs"></div>
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4" style="text-align:left;">
								<a href="javascript:void(0)" class="btn btn-info" id="add_new" onClick="return submitFrm();" ><b class="fa fa-plus"></b> Add New </a>
							</div>
				  		</div>
                        
                 	</form>
             	</div>
                	
				<div class="scanner_folders" ><!-- style="max-height:604px;" -->
                
       					<form name="deleteFolderFrm" action="scanPopUpUser.php" method="post" style="margin:0px;">
                        
                        		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
                                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                                <input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
                                <input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
                                <input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
                                <input type="hidden" name="admin" value="<?php echo $admin; ?>">
                                <input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
					
                    			<ul class="nav "><!-- class="nav nav-justified" -->
								<?php
									$formNameArr = array('Credential');
									if($user_id) {
										$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents_user WHERE user_id = '$user_id'";
									}
									if($docFolderRecordsStr)
									{
											$docFolderRecordsQry = imw_query($docFolderRecordsStr) or die(imw_error());
											if(imw_num_rows($docFolderRecordsQry) > 0)
											{
													while($docFolderRecord = imw_fetch_assoc($docFolderRecordsQry))
													{	
															$document_name = $docFolderRecord['document_name'];
															$document_id = $docFolderRecord['document_id'];								
															
								?>
                                							<li class="scan col-lg-2 col-md-3 col-sm-4 col-xs-6 clear-not">
                                                            	
                                                                <a href="javascript:void(0);" onClick="return showFolderContents('<?php echo $document_id; ?>');" class="fa fa-folder-open-o"></a>
                                                                <label>
                                                                    <?PHP
                                                                        if(!in_array($document_name,$formNameArr))
                                                                        {
                                                                            $delButton	=	true;
                                                                            
                                                                            echo '<input name="foldersId[]" value="'.$document_id.'" type="checkbox" style="margin-top:15px;" />&nbsp;';
                                                                        }
                                                                        
                                                                        //echo '<b onClick="return showFolderContents('.$document_id.');">'.$document_name.'</b>';
																		echo $document_name;
                                                                        
                                                                    ?>
                                                                </label>
                                                            </li>
                                        
									
									<?php
															
														
															
													}
											}
									}	
								?>
                                
                                </ul>
                                
						</form>
                        
                </div>
				
        </div>
         
        <div class="footer">
			<?PHP 
                if($delButton)
                {
            ?>	
                    <a class="btn btn-primary" href="javascript:void(0)" onClick="return delFolder();" id="deleteSelected">  <b class="fa fa-trash" ></b>  Delete</a>
            <?PHP 
                }
                
                
            ?>
            
            <a class="btn btn-primary" href="javascript:void(0)"  onclick="javascript:window.close();" id="closeButton" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
            
        </div>
        
        <?php
			}
		?>
<form name="frmSubDocs" action="scanPopUpUser.php" method="post">
	<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
	<input type="hidden" name="selectedFolder" value="true">
	<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
	<input type="hidden" name="subDocs" value="<?php echo $sub_Docs_Id; ?>">
	<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
</form>
<script>
<?php
	if($_GET['IOLScan'] == 'true' || $_GET['DISCHARGEScan'] == 'true' || $_GET['admin'] == 'true'){
		?>
		scanDocsFn();
		document.getElementById('uoloadTr').style.display = 'none';
		document.getElementById('saveButtonDispId').style.display = 'none';
		document.getElementById('backDelScan').style.display = 'none';
		document.getElementById('closeButton').style.display = 'none';
		document.getElementById('iolRefresh').style.display = 'inline-block';
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
			document.getElementById('deleteSelected').style.display = 'inline-block';
		<?php
	}
	if($filesRows>0){
		?>
		//document.getElementById('delImageBtn').style.display = 'inline-block';
		document.getElementById('deleteImage').style.display = 'inline-block';	
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

</script>
<?php
if($_GET['selectedFolder']=='true') {?>
<script>
scanDocsFn();
</script>
<?php	
}
?>
</body></html>