<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once("../common/conDb.php");
include_once("../user_agent.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Upload Multiple Documents</title>
<link rel="stylesheet" href="../css/form.css" type="text/css" />
<link rel="stylesheet" href="../css/style_surgery.css" type="text/css" />
<script type="text/javascript" src="../js/jsFunction.js"></script>
<script src="../js/jquery.js"></script>
</head>
<body>

<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$iolinkFolderPath =  $_SERVER['PHP_SELF'];
$iolinkFolderNameExplode = explode('/',$iolinkFolderPath);
//$iolinkFolderName = $iolinkFolderNameExplode[1];
$iolinkFolderName = $iolinkDirectoryName;
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

$folderId = $_REQUEST['folderId'];
$folder = $_REQUEST['folder'];
$selectedFolder = $_REQUEST['selectedFolder'];
$ptStubId = $_REQUEST['ptStubId'];

//START CODE TO SET STATIC FOLDER NAME
if($scanPtInfo) 					{	$folderName = 'Patient Information';
}else if($scanClinical) 			{	$folderName = 'Clinical';
}else if($scanIOLFolder) 			{	$folderName = 'IOL';
}else if($scanHP) 					{	$folderName = 'H&P'; 
}else if($scanEKG) 					{	$folderName = 'EKG';
}else if($scanHealthQuest) 			{	$folderName = 'Health Quest';
}else if($scanOcularHx) 			{	$folderName = 'Ocular Hx';
}else if($scanAnesthesiaConsent) 	{	$folderName = 'Consent';
}
//END CODE TO SET STATIC FOLDER NAME

if($folderId){
	$folderDetails = $objManageData->getRowRecord('scan_documents', 'document_id', $folderId);
	$folderName = $folderDetails->document_name;
}
	
// GET PATIENT DATA FOR GIVEN CONFIRMATION ID
/*
$patientData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
$patient_tbl_id = $patientData->patientId;
$ascId = $patientData->ascId;
*/
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
	<form name="showAddFolders" method="post" action="<?php echo 'scanPopUp.php?method=upload&amp;pconfirmId='.$pConfirmId.'&amp;patient_id='.$patient_id.'&amp;ptStubId='.$ptStubId.'&amp;patient_in_waiting_id='.$patient_in_waiting_id.'&amp;scanPtInfo='.$scanPtInfo.'&amp;scanClinical='.$scanClinical.'&amp;scanIOLFolder='.$scanIOLFolder.'&amp;scanHP='.$scanHP.'&amp;scanEKG='.$scanEKG.'&amp;scanHealthQuest='.$scanHealthQuest.'&amp;scanOcularHx='.$scanOcularHx.'&amp;scanAnesthesiaConsent='.$scanAnesthesiaConsent.'&amp;formName='.$formName.'&amp;folderId='.$folderId.'&amp;scanIOL='.$scanIOL.'&amp;IOLScan='.$IOLScan.'&amp;scanDISCHARGE='.$scanDISCHARGE.'&amp;DISCHARGEScan='.$DISCHARGEScan.'&amp;scaniOLinkConsentId='.$scaniOLinkConsentId.'&amp;CONSENTScan='.$CONSENTScan.'&amp;admin='.$admin.'&amp;INSURANCEScan='.$INSURANCEScan.'&amp;insuranceType='.$insuranceType;?>"  >
		<input type="hidden" name="pConfirmId" value="<?php echo $pConfirmId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="ptStubId" value="<?php echo $_REQUEST['ptStubId']; ?>">
		<input type="hidden" name="scanIOL" value="<?php echo $scanIOL; ?>">
		<input type="hidden" name="scanDISCHARGE" value="<?php echo $scanDISCHARGE; ?>">
		
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

<table class="table_pad_bdr" style="width:98%; border:none;">
				  <!-- TITLE BAR -->
				  
				  <tr>
					  <td class="txt_10 vision_bg top_white_border alignCenter valignTop">
						  <table class="table_collapse alignLeft" style="border:none;">
							<tr style="width:25px;">
								<td class="text_10b alignLeft">
									<table class="table_collapse" style="border:none;">
										<tr>
											<?php if($folderName=='Pt. Info'){ $folderName = 'Patient Information';  }?>
											<td class="text_10b alignLeft nowrap" style="width:30%;">Upload Multiple Documents<?php //echo '<b><a href="#" style="text-decoration:none;" class="text_10b" onClick="document.showAddFolders.submit();">Upload Multiple Documents</a></b>'; ?></td>
											<td class="text_10b alignCenter"><?php echo $folderName; ?></td>
											<td class="text_10b alignRight" style="padding-right:20px;">
												<?php echo $patientName; if($ascId){ echo ' ASC# : '.$ascId; } ?>
											</td>
										</tr>
									</table>						
								</td>
							</tr>
						</table> 
				    </td>
				 </tr>
				
				 <!-- END TITLE BAR -->
				 <!-- CONTENT -->
				 <tr><td style="height:5px;"></td></tr>
				 <tr>
				 			
				 <td class="alignCenter">	
 <!-- CONTENT -->

<table class="table_pad_bdr" style="border:none;"> 
	<tr>
		<td>
		
			
		
		</td>
	</tr>
	<tr>
		<td>
			<table class="table_collapse alignCenter" style="border:none;" >
			<tr>
				<td>
			<!-- Upload Applet  -->
            <!--<object type="application/x-java-applet" name="JUpload" title="JUpload" id="JUpload" style="width:960px; height:500px;">
            	<param name="code" value="com.smartwerkz.jupload.classic.JUpload">
                <param name="codebase" value="uploadApplet/jupload/">
                <param name="archive" value="dist/jupload.jar,
                							 dist/commons-codec-1.3.jar,
                                             dist/commons-httpclient-3.0-rc4.jar,
                                             dist/commons-logging.jar,
                                             dist/skinlf/skinlf-6.2.jar">
				<param name="mayscript" value="mayscript">
                <param name="alt" value="JUpload by www.jupload.biz">
                <param name="Files.MaxImageSize" value="16777216">
				<param name="Upload.Http.MaxRequestSize" value="16777216">
                <param name="Config" value="cfg/jupload.default.config">
				<param name="Upload.URL.Action" value="<?php echo $GLOBALS['php_server']."/$iolinkFolderName/admin/uploadApplet/jupload/scripts/php/jupload-post.php?method=upload&amp;pconfirmId=$pConfirmId&amp;patient_id=$patient_id&amp;ptStubId=$ptStubId&amp;patient_in_waiting_id=$patient_in_waiting_id&amp;scanPtInfo=$scanPtInfo&amp;scanClinical=$scanClinical&amp;scanIOLFolder=$scanIOLFolder&amp;scanHP=$scanHP&amp;scanEKG=$scanEKG&amp;scanHealthQuest=$scanHealthQuest&amp;scanOcularHx=$scanOcularHx&amp;scanAnesthesiaConsent=$scanAnesthesiaConsent&amp;formName=$formName&amp;folderId=$folderId&amp;scanIOL=$scanIOL&amp;IOLScan=$IOLScan&amp;scanDISCHARGE=$scanDISCHARGE&amp;DISCHARGEScan=$DISCHARGEScan&amp;scaniOLinkConsentId=$scaniOLinkConsentId&amp;CONSENTScan=$CONSENTScan&amp;admin=$admin&amp;INSURANCEScan=$INSURANCEScan&amp;insuranceType=$insuranceType";?>">
				Your browser does not support Java Applets or you disabled Java Applets in your browser-options.
				To use this applet, please install the newest version of Sun's Java Runtime Environment (JRE).
				You can get it from <a href="http://www.java.com/">java.com</a>
             </object>--> 
            <?php 
			$scanUploadSrc = "uploader/index.php?upload_url=".urlencode("uploader/save_upload.php?method=upload")."&upload_from=patient_scan_docs&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&patient_in_waiting_id=$patient_in_waiting_id&scanPtInfo=$scanPtInfo&scanClinical=$scanClinical&scanIOLFolder=$scanIOLFolder&scanHP=$scanHP&scanEKG=$scanEKG&scanHealthQuest=$scanHealthQuest&scanOcularHx=$scanOcularHx&scanAnesthesiaConsent=$scanAnesthesiaConsent&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan&scaniOLinkConsentId=$scaniOLinkConsentId&CONSENTScan=$CONSENTScan&admin=$admin&INSURANCEScan=$INSURANCEScan&insuranceType=$insuranceType";
			$arrBrow = browser();
			if($arrBrow['name'] == 'msie' && $arrBrow['version'] < 10){
				$scanUploadSrc = "csxthumbupload.php?upload_url=".urlencode("/$iolinkDirectoryName/admin/uploader/save_upload.php?method=upload&activex=1&upload_from=patient_scan_docs&pconfirmId=$pConfirmId&patient_id=$patient_id&ptStubId=$ptStubId&patient_in_waiting_id=$patient_in_waiting_id&scanPtInfo=$scanPtInfo&scanClinical=$scanClinical&scanIOLFolder=$scanIOLFolder&scanHP=$scanHP&scanEKG=$scanEKG&scanHealthQuest=$scanHealthQuest&scanOcularHx=$scanOcularHx&scanAnesthesiaConsent=$scanAnesthesiaConsent&formName=$formName&folderId=$folderId&scanIOL=$scanIOL&IOLScan=$IOLScan&scanDISCHARGE=$scanDISCHARGE&DISCHARGEScan=$DISCHARGEScan&scaniOLinkConsentId=$scaniOLinkConsentId&CONSENTScan=$CONSENTScan&admin=$admin&INSURANCEScan=$INSURANCEScan&insuranceType=$insuranceType");
			}//echo $arrBrow['name'].$arrBrow['version'].$scanUploadSrc;			
			?>
            <iframe name="iframeScanUpload" id="iframeScanUpload" src="<?php echo $scanUploadSrc;?>" width="720" height="500" scrolling="yes"> </iframe>		
             
			<!-- Upload Applet  -->
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>


				</td>
				
				 </tr>
				 <!-- END CONTENT -->
				
				 <!-- LOWER LINE SHADOW  -->
				
				
				 <tr>
				 	<td class="alignCenter">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('backButton','','../images/back_new_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('backButton','','../images/back_new_hover.gif',1)">
								<img src="../images/back_new.gif" style="border:none;" id="backButton" alt="Back" onClick="document.showAddFolders.submit();" />
							</a> 
				 </tr>
				 <!-- END LOWER LINE SHADOW  -->
</table>

<script>
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
	top.document.getElementById("iolinkUploadBtn").style.display = 'inline-block';
}
if(top.document.getElementById("multiUploadImgBtn")) {
	top.document.getElementById("multiUploadImgBtn").style.display = 'none';
}
window.onload = function() {
	$("#iolinkUploadBtn",top.document).click(function(){
		if(top.consent_tree) {
			top.consent_tree.location.reload();	
		}
	})  
}
</script>
</body>
</html>	