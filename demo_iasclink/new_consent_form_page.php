<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<script>
window.focus();
function chkDeleteFun() {
	if(document.getElementById('hiddScanPdfId').value=='pdfDelete') {
		if(confirm('Delete Record! Are you sure')) {
			document.frm_new_consent_form_page.submit();
		}
	}else if(document.getElementById('hiddScanPdfId').value=='scanDelete') {
		if(confirm('Delete Record! Are you sure')) {
			top.consent_data.document.forms[0].submit(); 
		}
	}
}
function scanPrintFun() {
	var frme = top.consent_data.document.getElementById('content_child');
	flPath = frme.src;
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;
	flOpn = window.open(flPath,'','width='+parWidth+',height='+parHeight+' top=100,left=100,resizable=yes,scrollbars=1');
	flOpn.print();
}
function formSaveFun() {
	var flag=true;
	var frmNameAction='';
	var frmObj = top.consent_data.document.forms[0];
	if(frmObj.frmAction) {
		frmNameAction = frmObj.frmAction.value;
	}
	
	if(frmObj.name=='frm_consent_multiple') {
		var newfun  = top.consent_data.SetSig();
		if(!newfun) {
			flag=false;
		}
	}
	if(frmObj.name=='frm_health_ques' && frmNameAction=='iolink_pre_op_health_quest.php'){
		//CHECK SIGN OF PATIENT AND WITNESS
		top.consent_data.SetSigPreHlthPtSign();
	}
	if(flag==true) {
		frmObj.submit();
	}	
	
}
</script>
<?php
$spec= "
</head>
<body>";
include_once("common/link_new_file.php");
$patient_id=$_REQUEST['patient_id'];
$intPatientWaitingId=$_REQUEST['intPatientWaitingId'];
$intConsentTemplateId=$_REQUEST['intConsentTemplateId'];
$consentAllMultipleId=$_REQUEST['consentAllMultipleId'];
$scan_consent_id = $_REQUEST['scan_consent_id'];
/*
$scanPtInfo = $_REQUEST['scanPtInfo'];
$scanClinical = $_REQUEST['scanClinical'];
*/
$scanSelectedFolderName = $_REQUEST['scanSelectedFolderName'];
$consentFileName = 'blankform.php';


//START CODE TO DELETE PDF FILE
$hiddDelSubmit = $_POST['hiddDelSubmit'];
$hiddScanPdfId = $_POST['hiddScanPdfId'];

$ampDeleteAlert = '';
if($_REQUEST['deleteAlert']) {
	$ampDeleteAlert = '&amp;deleteAlert=true';
}
if($hiddDelSubmit=='true') { 
	if($hiddScanPdfId=='pdfDelete') {
		$del_iolink_pdf_consent_qry = "DELETE FROM iolink_consent_filled_form WHERE fldPatientWaitingId='".$intPatientWaitingId."' AND consent_template_id='".$intConsentTemplateId."'";
		$del_iolink_pdf_consent_res = imw_query($del_iolink_pdf_consent_qry) or die(imw_error());
	
		$del_iolink_consent_signature_qry = "DELETE FROM iolink_consent_form_signature WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND consent_template_id='".$intConsentTemplateId."'";
		$del_iolink_consent_signature_res = imw_query($del_iolink_consent_signature_qry) or die(imw_error());
		
		$ampDeleteAlert = '&amp;deleteAlert=true';
	}	
}
//END CODE TO DELETE PDF FILE

//START CODE TO CHECK THE SCANED RECORD
$chkConsentScanedQry = "SELECT scan_consent_id  FROM iolink_scan_consent  WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND consent_template_id='".$intConsentTemplateId."' AND consent_template_id!=''";
$chkConsentScanedRes = imw_query($chkConsentScanedQry);
$chkConsentScanedNumRow= imw_num_rows($chkConsentScanedRes);
if($chkConsentScanedNumRow>0) {
	$consentFileName = 'showImg.php';
	$consentFileName = 'print_consent_form.php';
}
if($scan_consent_id) {
	$consentFileName = 'showImg.php';
	$consentFileName = 'print_consent_form.php';
}
//END CODE TO CHECK THE SCANED RECORD

//START CODE TO CHECK THE SIGNED RECORD
$chkConsentSignedQry = "SELECT surgery_consent_id  FROM iolink_consent_filled_form WHERE fldPatientWaitingId='".$intPatientWaitingId."' AND consent_template_id='".$intConsentTemplateId."' AND consent_template_id!=''";
$chkConsentSignedRes = imw_query($chkConsentSignedQry);
$chkConsentSignedNumRow= imw_num_rows($chkConsentSignedRes);
if($chkConsentSignedNumRow>0) {
	$consentFileName = 'print_consent_form.php';
}
//END CODE TO CHECK THE SIGNED RECORD


if(!$tree4consentFrameHref){
	$tree4consentFrameHref = 'tree4consent_form.php?intPatientWaitingId='.$intPatientWaitingId.'&amp;patient_id='.$patient_id.'&amp;intConsentTemplateId='.$intConsentTemplateId.'&amp;consentAllMultipleId='.$consentAllMultipleId;
}
if(!$consentDataFrameHref){
	$consentDataFrameHref = $consentFileName.'?intPatientWaitingId='.$intPatientWaitingId.'&amp;patient_id='.$patient_id.'&amp;intConsentTemplateId='.$intConsentTemplateId.'&amp;consentAllMultipleId='.$consentAllMultipleId.'&amp;scan_consent_id='.$scan_consent_id.$ampDeleteAlert;
}

//START CODE TO OPEN SCAN WINDOW DIRECTLY FOR EKG AND H&P, OCULAR-Hx, HEALTH-QUEST
if($scanSelectedFolderName) {
	$scanPtClinicalValue = '&'.$scanSelectedFolderName.'=true';
	$consentDataFrameHref = 'admin/scanPopUp.php?patient_in_waiting_id='.$intPatientWaitingId.'&amp;patient_id='.$patient_id.'&amp;scaniOLinkConsentId='.$intConsentTemplateId.'&amp;consentAllMultipleId='.$consentAllMultipleId.$scanPtClinicalValue;
}
//END CODE TO OPEN SCAN WINDOW DIRECTLY FOR EKG AND H&P, OCULAR-Hx, HEALTH-QUEST

?>


<span id='divScanAjaxLoadId' style="position:absolute; top:300px; left:150px; display:none;"><img src="images/ajax-loader5.gif" alt="Please Wait" style="width:80px; height:80px;"></span>
<form name="frm_new_consent_form_page" action="new_consent_form_page.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id;?>">
	<input type="hidden" name="intPatientWaitingId" id="intPatientWaitingId" value="<?php echo $intPatientWaitingId;?>">
	<input type="hidden" name="intConsentTemplateId" id="intConsentTemplateId" value="<?php echo $intConsentTemplateId;?>">
	<input type="hidden" name="consentAllMultipleId" id="consentAllMultipleId" value="<?php echo $consentAllMultipleId;?>">
	<input type="hidden" name="hiddScanPdfId" id="hiddScanPdfId" value="">
	<input type="hidden" name="hiddDelSubmit"  id="hiddDelSubmit" value="true">
</form>

	<table class="table_collapse" style="border:none;background-color:#FFFFFF;">
		<tr>
			<td style="width:20%;">
				<iframe name="consent_tree" id="consent_tree_id" src="<?php echo $tree4consentFrameHref;?>" style="height:600px; width:100%;">
				</iframe>
			</td>
			<td style="width:80%; background-color:#ECF1EA;">
				<iframe name="consent_data" id="consent_data_id" src="<?php echo $consentDataFrameHref;?>" style="height:600px; width:100%;">
				</iframe>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="alignCenter" style="padding-left:250px;"  >
				<table  style="border:none; width:20%; padding:5px; " >
					<tr>
						<td class="nowrap">
							<a id="anchorShow" href="#" style="display:none;" onClick="MM_swapImage('saveBtn','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" style="border:none;" id="saveBtn" alt="save" onClick="formSaveFun();"></a>
						</td>
						<td class="nowrap" >
							<a href="#" onClick="MM_swapImage('PrintBtn','','images/print_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('PrintBtn','','images/print_hover1.jpg',1)"><img src="images/print.jpg" id="PrintBtn" style="display:none;border:none;" alt="Print" onClick="javascript:scanPrintFun();"></a>
						</td>
						<td class="nowrap">
							<a href="#" onClick="MM_swapImage('deleteSelected','','images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteSelected','','images/delete_selected_hover.gif',1)"><img src="images/delete_selected.gif"  id="deleteSelected" style="display:none;border:none; "  alt="Delete" onClick="chkDeleteFun();"/></a>
						</td>
						<td class="nowrap">
							<a href="#" onClick="MM_swapImage('iolinkUploadBtn','','images/save_onclick1.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('iolinkUploadBtn','','images/save_hover1.jpg',1)">
								<img src="images/save.jpg" id="iolinkUploadBtn" style="display:none; border:none; " alt="Upload"  /><!-- onClick="top.consent_tree.location.reload();" -->
							</a>						
						</td>
						<td class="nowrap">
							<a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" id="closeButton" style="border:none;"  alt="Close" onClick="if(opener) { opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();opener.top.iframeHome.iOLinkBookSheetFrameId.focus();}window.close();"/></a>
						</td>
                        <td class="nowrap">
							<a style="width:120px; padding-left:45px;" href="#" onClick="MM_swapImage('multiUploadImgBtn','','images/upload_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('multiUploadImgBtn','','images/upload_hover.gif',1)">
								<img src="images/upload.gif" style="display:none;border:none;" id="multiUploadImgBtn" alt="" onClick="return top.consent_data.showMultiUpload();" />
							</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>