<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../common/conDb.php");
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
//echo "<pre>";
//print_r($_REQUEST);
//$ptStubId = $_REQUEST['ptStubId'];  //TO SET BGCOLOR OF SCAN BUTTON
$pConfirmId = $_REQUEST['pConfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];

$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];

$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$web_RootDirectoryName = "surgerycenter";
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
$admin = $_REQUEST['admin'];

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
		$formFolderExistArr = array('Pt. Info', 'Clinical');
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
				$inserExistId = $objManageData->addRecords($arrayRecord, 'scan_documents');
			
				//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
				$document_encounter='';
				if($formExistFolder=='Pt. Info') {
					$document_encounter = 'pt_info_1';
				}else if($formExistFolder=='Clinical') {
					$document_encounter = 'clinical_1';
				}
				unset($arrayRecord);
				$arrayRecord['patient_id'] = $patient_id;
				$arrayRecord['document_name'] = $formExistFolder;
				$arrayRecord['document_id'] = $inserExistId;
				$arrayRecord['document_date_time'] = date('Y-m-d H:i:s');
				$arrayRecord['document_file_name'] = 'scanPopUp.php';
				$arrayRecord['document_encounter'] = $document_encounter;
				$inserIdScanLogTbl = $objManageData->addRecords($arrayRecord, 'scan_log_tbl');
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
		if($newDirectory=='Pt. Info' || $newDirectory=='Clinical') {
			$document_encounter='';
			if($newDirectory=='Pt. Info') {
				$document_encounter = 'pt_info_2';
			}else if($newDirectory=='Clinical') {
				$document_encounter = 'clinical_2';
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
		if($newDirectory=='Pt. Info' || $newDirectory=='Clinical') {
			$document_encounter='';
			if($newDirectory=='Pt. Info') {
				$document_encounter = 'pt_info_3';
			}else if($newDirectory=='Clinical') {
				$document_encounter = 'clinical_3';
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
								WHERE patient_first_name = '$patient_fname'
								AND patient_middle_name = '$patient_mname'
								AND patient_last_name = '$patient_lname'
								AND patient_dob = '$date_of_birth'";
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
						if(opener.document.getElementById('scan_bgId'+ptStubId)) {
							opener.document.getElementById('scan_bgId'+ptStubId).className='';
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
			$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');		
		}
	}else{
		$inserIdScanUpload = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
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

		$pdfFolderName = 'pdfFiles/'.$surgeonName;
		
		if(is_dir($pdfFolderName)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($pdfFolderName, 0777);
		}
		
		$pdfFilePath = $pdfFolderName."/".$inserIdScanUpload.".pdf";
		unset($arrayRecord);
		$arrayRecord['pdfFilePath'] = $pdfFilePath;
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
<html>
<head>
</head>
<body>
<script>
function showFolderContents(id){
	//document.getElementById('closeButton').style.display = 'block';	
	document.getElementById('selectedFolder').value = 'true';
	document.getElementById('folderId').value = id;
	document.newFolderName.submit();
}
function showFoldersFn(){	
	document.showAddFolders.submit();
}
function scanDocsFn(id){
	document.getElementById('imagesTr').style.display = 'none';
	document.getElementById('scanBtn').style.display = 'none';	
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
		var ask = confirm("Delete file � Are you sure?");
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
<?php
	if($_GET['IOLScan'] == 'true' || $_GET['DISCHARGEScan'] == 'true'){
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
		
	</form>
	<table width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr height="25">
			<td align="left" class="text_10b">
				<table border="0" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<?php if($folderName=='Pt. Info'){ $folderName = 'Patient Information';  }?>
						<td width="30%" align="Left"  class="text_10b"><?php echo '<b><a href="#" style="text-decoration:none;" class="text_10b" onClick="return showFoldersFn();">Scan Documents</a></b>'; ?></td>
						<td width="30%" align="center" class="text_10b"><?php echo $folderName; ?></td>
						<td align="right" class="text_10b" style="padding-right:20px;">
							<?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?>
						</td>
					</tr>
				</table>						
			</td>
		</tr>
		<!-- <TR height="25">
			<td align="Left" style="padding-left:100px;" class="text_10b"><?php echo '<b><a href="#" style="text-decoration:none;" class="text_10b" onClick="return showFoldersFn();">Scan Documents</a></b>'; ?></td>
		</TR> -->
		<!-- SCAN -->
		<tr height="400" valign="top" id="scanTr" style="display:none;">
			<td width="20%" align="justify">
				<Center>
					<!-- <applet code="com.imwemr.util.jtwain.web.UploadApplet.class" codebase="applet"
						archive="program.jar, JTwain.jar" width="600" height="400">
						<param name="DOWNLOAD_URL" value="<?php //echo $GLOBALS['php_server']."/surgerycenter/admin/applet/AspriseJTwain.dll";?>">
						<param name="DLL_NAME" value="AspriseJTwain.dll">														
						<param name="UPLOAD_URL" value="<?php //echo $GLOBALS['php_server']."/surgerycenter/upload_scan_documents.php?method=upload&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan";?>">
						<param name="UPLOAD_PARAM_NAME" value="file[]">
						<param name="UPLOAD_EXTRA_PARAMS" value="A=B">
						<param name="UPLOAD_OPEN_URL" value="http://asprise.com/product/jtwain/applet/fileupload.php">
						<param name="UPLOAD_OPEN_TARGET" value="_blank">
						Oops, Your browser does not support Java applet!
					</applet> -->
					<?php
					if($scanIOL || $scanDISCHARGE) {
						$uploadfileNamePath = "upload_scan_documents.php";
					}else {
						$uploadfileNamePath = "admin/demoApplet/fileupload.php";
					}
					//echo $uploadfileNamePath;
					?>
					<applet code="scanDocs.class" 
						codebase="<?php echo $GLOBALS['php_server']."/surgerycenter/admin/DemoApplet/";?>"
						archive="scan.jar,JTwain.jar"
						width="600" height="400">
						<param name="DOWNLOAD_URL" value="<?php echo $GLOBALS['php_server']."/surgerycenter/admin/DemoApplet/AspriseJTwain.dll";?>">
						<param name="DLL_NAME" value="AspriseJTwain.dll">
						<!-- <param name="UPLOAD_URL" value="<?php //echo $GLOBALS['php_server']."/surgerycenter/upload_scan_documents.php?method=upload&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan";?>"> -->
						<param name="UPLOAD_URL" value="<?php echo $GLOBALS['php_server']."/surgerycenter/".$uploadfileNamePath."?method=upload&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan";?>">
						<param name="UPLOAD_PARAM_NAME" value="file[]">
						<param name="UPLOAD_EXTRA_PARAMS" value="A=B">
						<!--<param name="UPLOAD_OPEN_URL" value="http://localhost/demoApplet/fileupload.php">-->
						<param name="UPLOAD_OPEN_TARGET" value="_blank">
						Oops, Your browser does not support Java applet!
					</applet>
				</Center>
			</td>
		</tr>
		<tr>
			<td style="padding-left:100px;"><span class="text_10b"></span></td>
		</tr>		
		
		<!-- SCAN -->

		<!-- UPLOAD -->
		<form name="uploadImageFrm" action="scanPopUp.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">		
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="parentFolder" value="<?php echo $sub_Docs_Id; ?>"> 
		<tr height="50" valign="middle" id="uoloadTr" style="display:none;">
		  <td align="left" style="padding-left:85px;">				
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="center" valign="top"><span class="text_10b">Upload File :</span><input size="55" type="file" class="text_9" name="uploadFile"></td>
						<td width="10"></td>
						<td align="center">
							<!-- <a href="#" onClick="MM_swapImage('saveButton','','../images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveButton','','../images/save_hover1.jpg',1)">
								<img src="../images/save.jpg" name="saveButton" id="saveButton" border="0"  alt="Save" onClick="document.uploadImageFrm.submit();"/>
							</a> -->
						</td>
					</tr>
				</table>
			</td>			
		</tr>
		</form>	
		<!-- UPLOAD -->

		<!-- IMAGES -->
		<tr height="500" id="imagesTr">
			<td align="left" valign="top">
				<div style="position:absolute;height:500px; width:760px;overflow:auto;">
				<form name="imageFrm" action="scanPopUp.php" method="post">
				<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
				<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
				<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
				<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
				<input type="hidden" name="selectedFolder" value="true">
				<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
				<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr height="25">
							<td colspan="8">&nbsp;</td>
						</tr>
						<?php
						if($pConfirmId){
							$folderFilesQry = imw_query("SELECT * FROM scan_upload_tbl WHERE confirmation_id = '$pConfirmId' AND document_id = '$folderId'") or die(imw_error());
							
						}else if($patient_id) {
							$folderFilesQry = imw_query("SELECT * FROM scan_upload_tbl WHERE patient_id = '$patient_id' AND confirmation_id = '0' AND document_id = '$folderId'") or die(imw_error());
							
						}else{
							$folderFilesQry = imw_query("SELECT * FROM scan_upload_tbl WHERE document_id = '$folderId'") or die(imw_error());
							
						}						
						$filesRows = imw_num_rows($folderFilesQry);
						if($filesRows>0){
							while($folderFilesRows = imw_fetch_assoc($folderFilesQry)){
								$scan_upload_id = $folderFilesRows['scan_upload_id'];								
								$document_name = $folderFilesRows['document_name'];
								$image_type = $folderFilesRows['image_type'];
								$parent_sub_doc_id = $folderFilesRows['parent_sub_doc_id'];								
								if($parent_sub_doc_id!=0)
									continue;
								if($s == 0)	echo '<tr height="75">';
								++$s;								
								?>
								<td align="left">
									<table border="1" width="150" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
										<tr>
											<td align="center" height="150">
												<?php
												if($image_type == 'application/pdf'){
													?>													
													<img   src="../images/icon-pdf.png" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'pdf');">
													<?php
												}else{
													$imageScanedArr[] = $scan_upload_id;
													?>
													 <img width="130" height="130" border="0" id="imgThumbNail<?php echo $scan_upload_id; ?>" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image');" style="cursor:default;" src="logoImg.php?from=ScanPopUP&imageId=<?php echo $scan_upload_id; ?>"> 
													<?php
												}
												?>
											</td>
										</tr>
										<tr>
											<td align="center" class="text_10b">												
												<input type="checkbox" name="images[]" value="<?php echo $scan_upload_id; ?>">
												<?php echo wordwrap($document_name,18,"\n",1);//substr($document_name, 0, 20); ?>
											</td>
										</tr>
									</table>
								</td>
								<td width="25">&nbsp;</td>
								<?php
								if($s == 4){	echo '</tr>';  $s = 0; echo "<tr height='25'><td>&nbsp;</td></tr>"; }
							}
						}else{
							echo '<td height="250" class="text_9" align="center" valign="bottom"><b>NO Image Found.</b></td>';
						}
						?>
					</table>
					</form>
				</div>				
			</td>
		</tr>
		<tr height="15">
			<td>&nbsp;</td>
		</tr>
		<tr height="15" id="iolRefresh" style="display:none;" align="center">
			<td>
				<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('iolRefresh','','../images/upload_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('iolRefresh','','../images/upload_hover.gif',1)">
					<img src="../images/upload.gif" name="iolRefresh"  border="0" id="iolRefresh" alt="" onClick="opener.document.frames['iframeIOL'].location.reload();displayScanedImage();window.close();" />
				</a>

			</td>
		</tr>

		<tr id="backDelScan">
			<td align="center">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td align="left">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('backButton','','../images/back_new_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('backButton','','../images/back_new_hover.gif',1)">
								<img src="../images/back_new.gif" name="backButton"  border="0" id="backButton" alt="Back" onClick="return showFoldersFn();" />
							</a>
						</td>
						<td align="center" id="saveButtonDispId" style="display:none;"><img src="../images/tpixel.gif" width="20" height="1" />
							<a href="#" onClick="MM_swapImage('saveButton','','../images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveButton','','../images/save_hover1.jpg',1)">
								<img src="../images/save.jpg" name="saveButton" id="saveButton" border="0"  alt="Save" onClick="if(document.uploadImageFrm.uploadFile.value!='') { document.uploadImageFrm.submit(); }else { window.open('<?php echo $GLOBALS['php_server']."/surgerycenter/admin/demoApplet/pdfDemo.php?pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan";?>');}"/>
							</a> 
						</td>
						<td align="center" id="scanBtn">
							<a style="width:120px; padding-left:45px;" href="#" onMouseOver="MM_swapImage('scanButton','','../images/scan_hover.gif',1)" onMouseOut="MM_swapImgRestore()">
								<img src="../images/scan.gif" name="scanButton"  border="0" id="scanButton" alt="Scan" onClick="return scanDocsFn();" />
							</a>							
						</td>
						<td align="center" id="delImageBtn" style="display:none;">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('deleteSelected','','../images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteSelected','','../images/delete_selected_hover.gif',1)"><img src="../images/delete_selected.gif" name="deleteSelected" id="deleteImage" style="display:none;" border="0"  alt="Delete" onClick="return delSelectedImage();"/></a>
						</td>
						<td width="25">&nbsp;</td>
						<td align="left" valign="top">
							<a href="#" onClick="MM_swapImage('closeButton','','../images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','../images/close_hover.gif',1)"><img src="../images/close.gif" name="closeButton" id="closeButton" border="0"  alt="Close" onClick="window.close()"/></a>
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
		<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
			<tr height="20">
				<td align="left">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr id="scanHead">
							<td align="left" class="text_10b">Scan Documents</td>
							<td align="right" class="text_10b" style="padding-right:20px;">
								<?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td align="center">
					<div style="position:relative;height:500px; overflow:auto;">
					<form name="deleteFolderFrm" action="scanPopUp.php" method="post">
					<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
					<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
					<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
					<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
					<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
					<table border="0" cellpadding="0" cellspacing="0">
						<?php
						//$formNameArr = array('Consent', 'Ophthalmologist', 'Internist');
						$formNameArr = array('Pt. Info', 'Clinical');
						if($pConfirmId) {
							$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
						}else if($patient_id) {
							$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE patient_id = '$patient_id' AND confirmation_id = '0'";
						}else {
							$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
						}
						$docFolderRecordsQry = imw_query($docFolderRecordsStr) or die(imw_error());
							if(imw_num_rows($docFolderRecordsQry) > 0){
								while($docFolderRecord = imw_fetch_assoc($docFolderRecordsQry)){	
								$document_name = $docFolderRecord['document_name'];
								$document_id = $docFolderRecord['document_id'];								
								if($seq == 0)	echo '<tr>';
								++$seq;
									?>
									<td width="150" align="center">
										<table border="0" cellpadding="0" cellspacing="">
											<tr>
												<?php if(!in_array( $document_name, $formNameArr)){ ?>
													<td></td>
												<?php } ?>
												<td align="center" class="text_10b">
													<img src="../images/folder_icon1.gif" name="scanFolder[]" style="cursor:hand;" onClick="return showFolderContents('<?php echo $document_id; ?>');">
												</td>
											</tr>
											<tr>
												<?php if(!in_array( $document_name, $formNameArr)){ $delButton = true; ?>
													<td width="10"><input name="foldersId[]" value="<?php echo $document_id; ?>" type="checkbox"></td>
												<?php } ?>
												<td align="center" class="text_10" style="cursor:hand;" onClick="return showFolderContents('<?php echo $document_id; ?>');"><?php echo $document_name; ?></td>
											</tr>
										</table>										
									</td>	
									<?php
								if($seq == 5){	echo '</tr>';  $seq = 0; echo "<tr height='25'><td>&nbsp;</td></tr>"; }
								
								}								
							}
						?>
					</table>
					</form>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<form action="scanPopUp.php" method="post" name="newFolderName">
					<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">		
					<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">	
					<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">	
					<input type="hidden" name="selectedFolder" value="">
					<input type="hidden" name="folderId" value="">
					<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
					<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
					<table border="0" cellpadding="0" cellspacing="0" align="center" width="550">
						<tr>
							<td width="163" align="right" class="text_10b">New&nbsp;Folder&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td width="150" align="left"><input type="text" size="25" name="newDirectory"></td>
							<td width="10"></td>
							<td width="229" align="left">
							<a href="#" onClick="return submitFrm();" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('addNew','','../images/add_new_hover.gif',1)">
								<img src="../images/add_new.gif" name="addNew" id="addNew" border="0"  alt="Add New" onClick=""/>  <!-- return getPageSrc('Add New'); -->
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
				<td align="center"><a href="#" onClick="MM_swapImage('deleteSelected','','../images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteSelected','','../images/delete_selected_hover.gif',1)"><img src="../images/delete_selected.gif" name="deleteSelected" id="deleteSelected" style="display:none;" border="0"  alt="Delete" onClick="return delFolder();"/></a></td>
			</tr>
		</table>
		<?php
	}

?>
</html>
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
if($_GET['IOLScan'] == 'true' || $_GET['DISCHARGEScan'] == 'true'){
	?>
	scanDocsFn();
	document.getElementById('uoloadTr').style.display = 'none';
	document.getElementById('saveButtonDispId').style.display = 'none';
	document.getElementById('backDelScan').style.display = 'none';
	document.getElementById('closeButton').style.display = 'none';
	document.getElementById('iolRefresh').style.display = 'block';
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
</script>
