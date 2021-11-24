<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
session_start();
?>
<?php include_once("../common/conDb.php");?>
<!DOCTYPE html>
<html>
	
	<head>
		<meta name="viewport" content="width=device-width, maximum-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<title>Scan Documents</title>
		<?php include ("adminLinkfile.php"); ?>
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
	
	<body >
	
		<!-- Loader -->
		<div class="loader">
			<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
		</div>
		<!-- Loader-->
	
	<?php
			
		include_once("classObjectFunction.php");
		$objManageData = new manageData;
		
		//echo $_SERVER['PHP_SELF'];
		//echo "<pre>";
		//print_r($_REQUEST);
		//$ptStubId = $_REQUEST['ptStubId'];  //TO SET BGCOLOR OF SCAN BUTTON
		if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='surgerycenter';	}
		if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iolink';				}
		if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
		if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
		if(!$iolinkWebrootDirectoryName) 		{ $iolinkWebrootDirectoryName=$iolinkDirectoryName;					}
		
		
		$pConfirmId	=	$_REQUEST['pConfirmId'];
		$patient_id	=	$_REQUEST['patient_id']; 
		$scanIOL	=	$_REQUEST['scanIOL'];
		$IOLScan	=	$_REQUEST['IOLScan'];
		$dosScan	=	$_REQUEST['dosScan'];
		$stub_id	=	$_REQUEST['ptStubId'];
		
		$stubIdQry	=	(!$pConfirmId) ?	" AND stub_id = '".$stub_id."' AND stub_id != '0' " : '' ;
		
		$scanDISCHARGE	=	$_REQUEST['scanDISCHARGE'];
		$DISCHARGEScan	=	$_REQUEST['DISCHARGEScan'];

		$scanANESTHESIA =	$_REQUEST['scanANESTHESIA'];
		$ANESTHESIAScan =	$_REQUEST['ANESTHESIAScan'];

		$admin	=	$_REQUEST['admin'];

		$folder	=	$_REQUEST['folder'];
		$selectedFolder	=	$_REQUEST['selectedFolder'];
		$web_RootDirectoryName	=	$surgeryCenterDirectoryName;
		$webServerRootDirectoryName = "d:/XAMPP/xampp/htdocs/";
		$jBOSSServerIP	=	$_SERVER['HTTP_HOST']; //"127.0.0.1";
		$jBOSSServerPort=	":8089";
		$phpServerIP 	=	$_SERVER['HTTP_HOST']; //"127.0.0.1"; 
		$phpServerPort	=	"";
		
		
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
		//START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
		if($patient_id && $_GET['ptStubId']) {
			unset($arrayStubRecord);
			$arrayStubRecord['patient_id_stub']	=	$patient_id;
			$objManageData->updateRecords($arrayStubRecord, 'stub_tbl', 'stub_id', $_GET['ptStubId']);
		}
		//END CODE TO UPDATE PATIENT-ID IN STUB-TABLE
		
		//START CODE TO GET SURGEON-ID 

		//START GET MATCHED PRACTICE OF LOGGED-IN SURGEON AND ASSIGNED SURGEON
		$surgeryQry="select * from surgerycenter where surgeryCenterId='1' LIMIT 0,1";
		$surgeryRes= imw_query($surgeryQry) or die(imw_error());
		$surgeryRow=imw_fetch_array($surgeryRes);
		if($pConfirmId) {
			
			$surgeonData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
			$surgeonIdConf = $surgeonData->surgeonId;
			$practiceNameMatch = "";
			if($_SESSION['loginUserType'] == "Surgeon" && $_SESSION['loginUserId'] != $surgeonIdConf && $surgeryRow['peer_review'] == 'Y') {
				$practiceNameMatch = $objManageData->getPracMatchUserId($_SESSION['loginUserId'],$surgeonIdConf); 
				//GIVE VEIW ONLY ACCESS IF LOGGED-IN SURGEON IS DIFFERENT FROM ASSIGNED SURGEON WITHIN SAME PRACTICE
			}
		}
		elseif($surgeryRow['peer_review'] == 'Y' && $_REQUEST['AD'] == 'yes')
		{
			$practiceNameMatch = 'yes';	
		}
		
		//END GET MATCHED PRACTICE OF LOGGED-IN SURGEON AND ASSIGNED SURGEON
		
		
		
		//END  CODE TO GET SURGEON-ID 

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
			$folderDetails	=	$objManageData->getRowRecord('scan_documents', 'document_id', $folderId);
			$folderName		=	$folderDetails->document_name;
		}
	
		
		$formName =	empty($formName)	?	'Surgery'	:	$formName ;
		
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
		if($patient_id && !$pConfirmId)
		{
			$formFolderExistArr = array('Pt. Info', 'Clinical', 'IOL');
			foreach($formFolderExistArr as $formExistFolder)
			{
				unset($conditionArr);
				$conditionArr['document_name'] 	= $formExistFolder;
				$conditionArr['patient_id'] 	= $patient_id;
				$conditionArr['confirmation_id']= '0';
				$conditionArr['dosOfScan'] 		= $dosScan;
				$conditionArr['stub_id'] 		= $stub_id;
			
				$getFolderExistDetails = $objManageData->getMultiChkArrayRecords('scan_documents', $conditionArr);
				
				if(count($getFolderExistDetails)<=0){
					unset($arrayRecord);
					$arrayRecord['patient_id'] 	 = $patient_id;
					$arrayRecord['document_name']= $formExistFolder;
					$arrayRecord['dosOfScan'] 	 = $dosScan;
					$arrayRecord['stub_id'] 	 = $stub_id;
					$inserExistId = $objManageData->addRecords($arrayRecord, 'scan_documents');
			
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					$document_encounter='';
					if($formExistFolder=='Pt. Info') {
						$document_encounter = 'pt_info_1';
					}else if($formExistFolder=='Clinical') {
						$document_encounter = 'clinical_1';
					}
					
					unset($arrayRecord);
					$arrayRecord['patient_id'] 			= $patient_id;
					$arrayRecord['document_name'] 		= $formExistFolder;
					$arrayRecord['document_id'] 		= $inserExistId;
					$arrayRecord['document_date_time'] 	= date('Y-m-d H:i:s');
					$arrayRecord['document_file_name'] 	= 'scanPopUp.php';
					$arrayRecord['document_encounter'] 	= $document_encounter;
					$arrayRecord['stub_id'] 	 		= $stub_id;
					$inserIdScanLogTbl = $objManageData->addRecords($arrayRecord, 'scan_log_tbl');
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
			
				}
			}
		}	
		
		//END INSERT FOLDERS IF NOT ALREADY EXIST





		//START CODE TO GET SCAN DATA FROM IOLINK
		
		$iolink_patient_in_waiting_id='';	
		
		if($_REQUEST['ptStubId'] && !$pConfirmId) {
			
			$iolinkIdStubQry 	= "SELECT st.iolink_patient_in_waiting_id FROM stub_tbl st  
									INNER JOIN patient_in_waiting_tbl piw ON (piw.patient_in_waiting_id=st.iolink_patient_in_waiting_id AND piw.iolinkSyncroStatus!='Syncronized')
									WHERE stub_id='".$_REQUEST['ptStubId']."'";
			
			$iolinkIdStubRes 	= imw_query($iolinkIdStubQry) or die(imw_error()); 
			
			$iolinkIdStubNumRow = imw_num_rows($iolinkIdStubRes);
			
			if($iolinkIdStubNumRow>0) {
				$iolinkIdStubRow= imw_fetch_array($iolinkIdStubRes);
				$iolink_patient_in_waiting_id 	= $iolinkIdStubRow['iolink_patient_in_waiting_id'];
				$Confirm_patientDos 			= $dosScan;
				$pConfId 						= $pConfirmId;
				include("../iosync_scan_consent.php");
			}
		
		}
		
		//END CODE TO GET SCAN DATA FROM IOLINK


		// SHOW SELECTED FOLDER IMAGES
		if($pConfirmId || $patient_id)
		{
			
			if($folder)
			{
				
				unset($conditionArr);
				if($patient_id && !$pConfirmId) {
					$conditionArr['patient_id'] 		=	$patient_id;
					$conditionArr['confirmation_id']	=	'0';
					$conditionArr['dosOfScan']			=	$dosScan;
					$conditionArr['stub_id']			=	$stub_id;
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
										patient_id 		= '$patient_id',
										stub_id 		= '$stub_id',
										dosOfScan 		= '$dosScan',
										document_name 	= '$newDirectory'") or die(imw_error());
				//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
				if($newDirectory == 'Pt. Info' || $newDirectory == 'Clinical')
				{
					
					$document_encounter='';
					if($newDirectory=='Pt. Info') {
						$document_encounter = 'pt_info_2';
					}else if($newDirectory=='Clinical') {
						$document_encounter = 'clinical_2';
					}
					$insert_scan_log_qry1 = "insert into `scan_log_tbl` set 
												document_id 		= '".imw_insert_id()."',
												document_name 		= '".$newDirectory."',
												confirmation_id 	= '".$pConfirmId."',
												stub_id 			= '$stub_id',
												document_date_time 	= '".date('Y-m-d H:i:s')."',
												document_file_name 	= 'scanPopUp.php',
												document_encounter 	= '".$document_encounter."'
												";
					$insert_scan_log_res1 = imw_query($insert_scan_log_qry1) or die(imw_error());
				}
		//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
	
	}else if($patient_id) {
		$insertFolderQry = imw_query("INSERT INTO scan_documents SET 
										patient_id 	= '$patient_id',
										dosOfScan 	= '$dosScan',
										stub_id 	= '$stub_id',
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
										document_id 		= '".imw_insert_id()."',
										document_name 		= '".$newDirectory."',
										patient_id 			= '".$patient_id."',
										stub_id 			= '".$stub_id."',
										document_date_time 	= '".date('Y-m-d H:i:s')."',
										document_file_name 	= 'scanPopUp.php',
										document_encounter 	= '".$document_encounter."'
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

	//START DELETE DUPLICATE FOLDERS
	if($pConfirmId) {
		$pConfId = $pConfirmId;
		include("../delete_duplicate_folder.php");
	}
	//END DELETE DUPLICATE FOLDERS




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
								AND patient_dob = '$date_of_birth'";
		$stubDataQry = imw_query($stubDataStr) or die(imw_error());
		$stubDataNumRows = imw_num_rows($stubDataQry);
		
		$ptStubIdTemp = array();
		if($stubDataNumRows>0){
			while($stubDataRow = imw_fetch_array($stubDataQry))
				$ptStubIdTemp[] = $stubDataRow['stub_id'];
		
		}
	//END CODE TO GET MULTIPLE STUB ID OF A PATIENT 
	
	
	$andDosScanQry='';
	if(!$pConfirmId) { $andDosScanQry = " AND dosOfScan = '$dosScan' ".$stubIdQry; }
	//CODE TO REMOVE BG COLOR OF SCAN BUTTONS OF A PATIENT IN SCHEDULER (IF NO FILE IS UPLOADED)
		$chk_folderFilesRes = imw_query("SELECT * FROM scan_upload_tbl WHERE patient_id = '$patient_id' AND confirmation_id = '$pConfirmId' $andDosScanQry") or die(imw_error());
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
	$arrayRecord['dosOfScan'] = $dosScan;
	$arrayRecord['stub_id'] = $stub_id;
	$arrayRecord['scan_upload_save_date_time'] = date('Y-m-d H:i:s');;
	
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
$patientName = '';
if($patient_tbl_id){
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
}
$winSafaricmptbl = $_REQUEST['winSafaricmptbl'];
?>
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
	document.getElementById('scanButton').style.display = 'none';	
	document.getElementById('multiUpload').style.display = 'none';	
	document.getElementById('scanTr').style.display = 'inline-block';
	document.getElementById('saveButton').style.display = 'inline-block';		
	
	document.getElementById('deleteImage').style.display = 'none';
	document.getElementById('deleteImage').style.display = 'none';
	document.getElementById('closeButton').style.display = 'inline-block';		
	
}
function ShowImage(id, type, filePath){
	var winobj='';
	if(filePath) {
		winobj = window.open(filePath,'scanJpgPdf', 'menubar=1, top=5, left=10, width=1000, height=650, resizable=1, scrollbars=1');
		winobj.focus();
	}else {
		window.open('scanedImage.php?id='+id+'&type='+type,'scanImg', 'menubar=1, top=5, left=10, width=1000, height=650, resizable=1, scrollbars=1');
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
	if($_GET['IOLScan'] == 'true' || $_GET['DISCHARGEScan'] == 'true' || $_GET['ANESTHESIAScan'] == 'true' || $_GET['admin']=='true'){
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
        <input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
		<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
    <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
		
	</form>
	<form action="upload_multi_docs.php" method="post" name="frm_AddMultiUpload">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
        <input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
		<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
		<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
    <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
	</form>
	
	<form action="scanPopUp.php" method="post" name="newFolderName" style="margin:0px;">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">		
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">	
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">	
		<input type="hidden" name="selectedFolder" id="selectedFolder" value="">
		<input type="hidden" name="folderId" id="folderId" value="">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
		<input type="hidden" name="admin" value="<?php echo $admin; ?>">
		<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
    <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
	</form>
	
		<div class="header box-sizing text-left ">
        	<h4 >Scan Documents</h4>
			<span class="right-box"><?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?></span>
		</div>
		
		<div class="body">
			
			
			
			<form name="uploadImageFrm" action="scanPopUp.php" method="post" enctype="multipart/form-data" style="margin:0px;">
                <input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                <input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
                <input type="hidden" name="folderId" value="<?php echo $folderId; ?>">		
                <input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
                <input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
                <input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
                <input type="hidden" name="admin" value="<?php echo $admin; ?>">
                <input type="hidden" name="parentFolder" value="<?php echo $sub_Docs_Id; ?>"> 
                <input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
                <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
			</form>	
				
			<div class="folder_wrap">
				
				<a onClick="return showFolderContents('<?php echo $folderDetails->document_id; ?>');" style="text-decoration:none; cursor:pointer" >
					<h5  > <?php echo ($folderName == 'Pt. Info') ? 'Patient Information' : $folderName; ?></h5></a>
				
				<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" id="scanTr" style="display:none;">
					
					<!--
					<object type="application/x-java-applet"  width="600" height="400">
                    <param name="code" value="com.imwemr.util.jtwain.web.UploadApplet.class" />
                    <param name="codebase" value="applet" />
                    <param name="archive" value="program.jar, JTwain.jar" />
                    <param name="DOWNLOAD_URL" value="<?php echo $GLOBALS['php_server']."/$surgeryCenterWebrootDirectoryName/admin/applet/AspriseJTwain.dll";?>">
                    <param name="DLL_NAME" value="AspriseJTwain.dll">														
                    <param name="UPLOAD_URL" value="<?php echo $GLOBALS['php_server']."/$surgeryCenterWebrootDirectoryName/".$uploadfileNamePath."?method=upload&amp;pconfirmId=$pConfirmId&amp;patient_id=$patient_id&amp;ptStubId=$stub_id&amp;formName=$formName&amp;folderId=$folderId&amp;scanIOL=$scanIOL&amp;IOLScan=$IOLScan&amp;scanDISCHARGE=$scanDISCHARGE&amp;DISCHARGEScan=$DISCHARGEScan&amp;scanANESTHESIA=$scanANESTHESIA&amp;ANESTHESIAScan=$ANESTHESIAScan&amp;admin=$admin&amp;dosScan=$dosScan";?>">
                    <param name="UPLOAD_PARAM_NAME" value="file[]">
                    <param name="UPLOAD_EXTRA_PARAMS" value="A=B">
                    <param name="UPLOAD_OPEN_URL" value="http://asprise.com/product/jtwain/applet/fileupload.php">
                    <param name="UPLOAD_OPEN_TARGET" value="_blank">
                    Oops, Your browser does not support Java applet!
                	</object>
					-->
				
					<?php
							
						$uploadfileNamePath = "upload_scan_documents.php";
						echo "<script>multiScan='yes';no_of_scans=20;uploadScanURL = '".$GLOBALS['php_server']."/".$surgeryCenterWebrootDirectoryName."/".$uploadfileNamePath."?method=upload&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$stub_id&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan&scanANESTHESIA=$scanANESTHESIA&ANESTHESIAScan=$ANESTHESIAScan&admin=$admin&dosScan=$dosScan';</script>";
						
						include_once("scan_control.php");
					?>
						
						
					
				</div>
				
				<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12" id="imagesTr">
					
						<form name="imageFrm" action="scanPopUp.php" method="post">
							<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
							<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
							<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
							<input type="hidden" name="folderId" value="<?php echo $folderId; ?>">
							<input type="hidden" name="selectedFolder" value="true">
							<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
							<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
							<input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
							<input type="hidden" name="admin" value="<?php echo $admin; ?>">	
							<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
              <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
					
						
							<?php
							
								if($pConfirmId){
									$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE confirmation_id = '$pConfirmId' AND document_id = '$folderId'";
								}else if($patient_id) {
									$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE patient_id = '$patient_id' AND confirmation_id = '0' AND document_id = '$folderId' AND dosOfScan = '$dosScan' ".$stubIdQry;
								}else{
									//$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE document_id = '$folderId'";
								}
                    
								if($folderFilesQry) {
									$folderFilesRes = imw_query($folderFilesQry) or die(imw_error());						
									$filesRows = imw_num_rows($folderFilesRes);
								}
							?>
                   				<ul class="nav">
                        
                        	<?php
								if($filesRows>0)
								{
									$q	=	0;
									while($folderFilesRows = imw_fetch_assoc($folderFilesRes))
									{
										$q++;
										$scan_upload_id	=	$folderFilesRows['scan_upload_id'];						
										$document_name	=	$folderFilesRows['document_name'];
										$image_type		=	$folderFilesRows['image_type'];
										$pdfFilePathDB	=	$folderFilesRows['pdfFilePath'];
										$parent_sub_doc_id=	$folderFilesRows['parent_sub_doc_id'];
										
										if($parent_sub_doc_id!=0)	continue;
										
										if($s == 0)	echo '<tr style="height:75px; ">';
										
										++$s;								
								
							?>
										<li class="col-lg-2 col-md-3 col-sm-4 col-xs-6 clear-not" >
											<?php
												
												/*if($pdfFilePathDB)
												{
													$thumbPath = $pdfFilePathDB;
													$thumHgtWdth = "width:130px; height:130px;";
													if($image_type == 'application/pdf')
													{
														$thumbPath = "../images/icon-pdf.png";
														$thumHgtWdth = "";	
													}else {
														$imageScanedArr[] = $scan_upload_id;
													}
													
											?>
													<img style=" <?php echo $thumHgtWdth; ?> cursor:pointer; border:none;" id="imgThumbNail<?php echo $scan_upload_id; ?>" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image','<?php echo $pdfFilePathDB; ?>');" src="<?php echo $thumbPath; ?>" />
											
											<?php
												}*/
												if($image_type == 'application/pdf')
												{ 
											?>
													<i class="fa fa-file-pdf-o pdf-xl" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'pdf','<?php echo $pdfFilePathDB; ?>');"></i>
											<?php
												}
												else
												{
													$imageScanedArr[] = $scan_upload_id;
													
											?>
													<img style="width:130px; height:130px; cursor:pointer; border:none;" id="imgThumbNail<?php echo $scan_upload_id; ?>" onClick="return ShowImage('<?php echo $scan_upload_id; ?>', 'image','<?php echo $pdfFilePathDB; ?>');" src="<?php echo $pdfFilePathDB; ?>" />
													
											<?php
												}
											?>
												
											<label style="display:block">											
													<input type="checkbox" name="images[]" value="<?php echo $scan_upload_id; ?>">
													<?php echo wordwrap($document_name,18,"\n",1);//substr($document_name, 0, 20); ?>
											</label>	
										
										</li>
							<?php 
										/*if($s == 4 || ($filesRows==$q && $q<4))
										{
											echo '</tr>';  $s = 0;
										}
										else if($filesRows==$q && $s< 4)
										{ 
											$tdcols= 4-$s; 
											echo "<td class='alignLeft' colspan='".$tdcols."'></td></tr>";
										}*/
									}
								}
								else
								{
									echo '<li style="float:none"><label style="margin-top:200px">No Image Found.</label></li>';
								}
								
							?>
								
								</ul>
								
						</form>
					
					
				</div><!-- images Tr -->	
			
			</div><!-- Folder Wrap -->
		
		</div><!-- Body End Here --->
		
		<div class="footer">
			
			<span id="iolRefresh" style="display:none">
			
				<?php
					$adminLoc	=	'';
					if($admin=='true') {
						$adminLocReload = 'opener.document.location.reload();';
					}
				?>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return upload();" id="iolButtonRefresh" >  <b class="fa fa-upload" ></b>&nbsp;Upload</a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="if(opener.iframeIOL) {opener.iframeIOL.location.reload();}else { }displayScanedImage();location.href='scanPopUp.php?winSafaricmptbl=close';" id="closeButtonIOL" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
			
			</span>
			
			
      		<span id="backDelScan">     
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return showFoldersFn();" id="backButton" >  <b class="fa fa-arrow-left" ></b>&nbsp;Back</a>
				<?php
				if($practiceNameMatch != "yes") {
				?>
				<a class="btn btn-primary" href="javascript:void(0)"  id="saveButton" style="display:none;" >  <b class="fa fa-floppy-o" ></b>&nbsp;Save</a><!--id="saveButtonDispId"-->
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return scanDocsFn();" id="scanButton" >  Scan </a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return showMultiUpload();" id="multiUpload" >  <b class="fa fa-upload" ></b>&nbsp;Upload</a>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="return delSelectedImage();" id="deleteImage" style="display:none;" >  <b class="fa fa-trash" ></b>&nbsp;Delete</a>
				<?php
				}
				?>
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="javascript:window.close();" id="closeButton" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
			</span>
      	</div>
		
	<?php
		}
		else
		{
	?>
		
			<div class="header box-sizing text-left">
				<h4 >Scan Documents</h4>
				<span class="right-box"><?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?>	</span>
			</div>
		
			<div class="body">
			
				<!-- Add New Folder Box -->
				
				<div class="add_new_scan_wrap">
				
					<form action="scanPopUp.php" method="post" name="newFolderName" style="margin:0px;">
						<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">		
						<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">	
						<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">	
						<input type="hidden" name="selectedFolder" id="selectedFolder" value="">
						<input type="hidden" name="folderId" id="folderId" value="">
						<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
						<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
						<input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
						<input type="hidden" name="admin" value="<?php echo $admin; ?>">
						<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
            <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
					
					
				  		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12 text-right">
								<label for="add_new" class="add">Name New Folder</label>
							</div>
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
								<input class="form-control" type="text"  name="newDirectory" alt="Add New"  />
							</div>
							<div class="clearfix margin_adjustment_only visible-sm"></div>
							<div class="clearfix margin_adjustment_only visible-xs"></div>
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12" style="text-align:left;">
								<a href="javascript:void(0)" class="btn btn-info" id="add_new" onClick="return submitFrm();" ><b class="fa fa-plus"></b> Add New </a>
							</div>
				  		</div>
					</form>
					
				</div>
				
				<!-- End Add New Folder -->
			
				<div class="clearfix margin_adjustment_only"></div>
			
				<div class="scanner_folders" ><!-- style="max-height:604px;" -->
					<form name="deleteFolderFrm" action="scanPopUp.php" method="post" style="margin:0px;">
						<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
						<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
						<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
						<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
						<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
						<input type="hidden" name="scanANESTHESIA" value="<?php echo $scanANESTHESIA; ?>">
						<input type="hidden" name="admin" value="<?php echo $admin; ?>">
						<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
            <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
				
						<ul class="nav "><!-- class="nav nav-justified" -->
					
						<?php
							//$formNameArr = array('Pt. Info', 'Clinical', 'H&P', 'EKG', 'Health Questionnaire', 'Ocular Hx', 'Anesthesia Consent');
							$formNameArr = array('Pt. Info', 'Clinical', 'IOL');
							
							if($pConfirmId) {
								//$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId' AND dosOfScan = '$dosScan'";
								$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
							}else if($patient_id) {
								$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE patient_id = '$patient_id' AND confirmation_id = '0' AND dosOfScan = '$dosScan' ".$stubIdQry;
							}else {
								//$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
							}
							
							if($docFolderRecordsStr)
							{
								$seq = 0;
								
								$docFolderRecordsQry = imw_query($docFolderRecordsStr) or die(imw_error());
								
								if(imw_num_rows($docFolderRecordsQry) > 0)
								{
									while($docFolderRecord = imw_fetch_assoc($docFolderRecordsQry))
									{	
										$document_name	=	$docFolderRecord['document_name'];
										$document_id	=	$docFolderRecord['document_id'];								
										$seq++;
										
										
											
										
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
													
													echo $document_name;
													
												?>
											</label>
										</li>
										
						<?PHP
										/*if($seq == 6 ) 
										{
											echo '</ul>'; $seq = 0;  echo '<ul class="nav nav-justified">';
										}*/
											
										
									}								
								}
							}	
						?>
					
						</ul> 
					
					</form>
								
				</div>
					
						
					
		</div> <!-- Body End Here --> 
		
		
			<div class="footer">
				<?PHP 
					if($delButton && $practiceNameMatch <> 'yes')
					{
				?>	
						<a class="btn btn-primary" href="javascript:void(0)" onClick="return delFolder();">  <b class="fa fa-trash" ></b>  Delete</a>
				<?PHP 
					}
					
					
				?>
				
				<a class="btn btn-primary" href="javascript:void(0)"  onclick="javascript:window.close();" id="closeButton" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
				
			</div>
		
		
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
	<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
  <input type="hidden" name="AD" value="<?php echo $_REQUEST['AD']; ?>">
</form>

	</div><!-- End Modal Content -->
 </div><!-- End Modal Dialog -->
</div><!-- End Modal Fade -->
<script>
<?php
	if($_GET['IOLScan'] == 'true' || $_GET['DISCHARGEScan'] == 'true' || $_GET['ANESTHESIAScan'] == 'true' || $_GET['admin'] == 'true'){
		?>
		scanDocsFn();
		
		document.getElementById('saveButton').style.display = 'none';
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
	
	if($filesRows > 0){ 
		?>
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
</body></html>