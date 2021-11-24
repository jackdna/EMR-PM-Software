<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<?php
	include_once("../common/conDb.php");
	include_once("../user_agent.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Upload Multiple Documents</title>
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
				
				$(".body").css({'min-height':BH+'px', 'max-height':BH+'px'})
				
				$("#iframeScanUpload").attr('height',(BH-100) + 'px')
			
			}
			
			function showFolderContents(id){
				//document.getElementById('closeButton').style.display = 'inline-block';	
				document.getElementById('selectedFolder').value = 'true';
				document.getElementById('folderId').value = id;
				document.newFolderName.submit();
			}

			
			//$(window).resize(function(){ size = [1034,630]; window.resizeTo(size[0],size[1]); });
		</script>
</head>
<body>
		<!-- Loader -->
		<div class="loader">
			<span><b class='fa fa-spinner fa-pulse' ></b>&nbsp;Loading...</span>
		</div>
		<!-- Loader-->
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
/*
if($phpServerIP != $_SERVER['HTTP_HOST'])	
{
	$phpServerIP=$_SERVER['HTTP_HOST'];
	$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
}			
*/

$surgerycenterFolderPath =  $_SERVER['PHP_SELF'];
$surgerycenterFolderNameExplode = explode('/',$surgerycenterFolderPath);
//$surgerycenterFolderName = $surgerycenterFolderNameExplode[1];
$surgerycenterFolderName = $surgeryCenterDirectoryName;
$pConfirmId = $_REQUEST['pConfirmId'];
$patient_id = $_REQUEST['patient_id']; 
$scanIOL = $_REQUEST['scanIOL'];
$IOLScan = $_REQUEST['IOLScan'];
$dosScan = $_REQUEST['dosScan'];


$scanDISCHARGE = $_REQUEST['scanDISCHARGE'];
$DISCHARGEScan = $_REQUEST['DISCHARGEScan'];
$folderId = $_REQUEST['folderId'];
$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$ptStubId = $_REQUEST['ptStubId'];

if($folderId){
	$folderDetails = $objManageData->getRowRecord('scan_documents', 'document_id', $folderId);
	$folderName = $folderDetails->document_name;
	$folderName	=	($folderName == 'Pt. Info')	?	'Patient Information' :	$folderName;
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

	<form action="scanPopUp.php" method="post" name="showAddFolders">
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		<input type="hidden" name="dosScan" value="<?php echo $dosScan; ?>">
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
	</form>
	
	
<div class="box box-sizing">
 <div class="dialog box-sizing">
 	<div class="content box-sizing">

		<div class="header box-sizing text-left ">
			
			<h4 >Upload Multiple Doocuments</h4>
			<span class="right-box"><?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?></span>
			
		</div>
			
		<div class="body">
			<div class="folder_wrap">
				<a onClick="return showFolderContents('<?php echo $folderDetails->document_id; ?>');" style="text-decoration:none; cursor:pointer" >
					<h5  > <?php echo ($folderName == 'Pt. Info') ? 'Patient Information' : $folderName; ?></h5></a>
				
			</div>
			<?php 
					$scanUploadSrc = "uploader/index.php?upload_url=".urlencode("uploader/save_upload.php?method=upload")."&upload_from=patient_scan_docs&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&dosScan=$dosScan&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan";
					
					$arrBrow = browser();
					
					if($arrBrow['name'] == 'msie' && $arrBrow['version'] < 10){
						$scanUploadSrc = "csxthumbupload.php?upload_url=".urlencode("/$surgeryCenterDirectoryName/admin/uploader/save_upload.php?method=upload&activex=1&upload_from=patient_scan_docs&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&dosScan=$dosScan&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan");
					}//echo $arrBrow['name'].$arrBrow['version'].$scanUploadSrc;			
				?>
				<iframe name="iframeScanUpload" id="iframeScanUpload" src="<?php echo $scanUploadSrc;?>" width="75%" scrolling="yes" frameborder="0" style="margin:5px auto; border:dashed 1px #DDD;"> </iframe>	
			
		</div>
		
		<div class="footer">
				
			<a class="btn btn-primary" href="javascript:void(0)"  onClick="document.showAddFolders.submit();"  id="backButton"  >  <b class="fa fa-arrow-left" ></b>&nbsp;Back</a>
			
			<a class="btn btn-primary" href="javascript:void(0)"  onclick="javascript:window.close();" id="closeButton" >  <b class="fa fa-close" ></b>&nbsp;Close</a>
			
		</div>
		
		
	</div>
 </div>
</div>	
	
</body>

</html>	