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
<title>iOLink Consent Image</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
</head>
<body>
<?php
$patient_id=$_REQUEST['patient_id'];
$intPatientWaitingId=$_REQUEST['intPatientWaitingId'];
$intConsentTemplateId=$_REQUEST['intConsentTemplateId'];
$consentAllMultipleId = $_REQUEST['consentAllMultipleId'];
$consentFileName = 'consentFormDetails.php';
$scan_consent_id = $_REQUEST['scan_consent_id'];

$hiddDelSubmit = $_POST['hiddDelSubmit'];
$hidd_scanConsentId = $_POST['hidd_scanConsentId'];
if($hiddDelSubmit=='true') {
	if($hidd_scanConsentId) {
		$del_iolink_scan_consent_qry = "DELETE FROM iolink_scan_consent WHERE scan_consent_id='".$hidd_scanConsentId."'";
		$del_iolink_scan_consent_res = imw_query($del_iolink_scan_consent_qry) or die(imw_error());
		
		$consentDataFrameHref = $consentFileName.'?intPatientWaitingId='.$intPatientWaitingId.'&patient_id='.$patient_id.'&intConsentTemplateId='.$intConsentTemplateId;
		echo "<script>top.location.href='new_consent_form_page.php?intPatientWaitingId=".$intPatientWaitingId."&patient_id=".$patient_id."&intConsentTemplateId=".$intConsentTemplateId."&consentAllMultipleId=".$consentAllMultipleId."&deleteAlert=true';</script>";
	}
}


//START CODE TO CHECK THE SIGNED RECORD
$chkConsentScanedQry = "SELECT scan_consent_id,pdfFilePath,image_type  FROM iolink_scan_consent  WHERE patient_in_waiting_id='".$intPatientWaitingId."' AND scan_consent_id='".$scan_consent_id."'";
$chkConsentSignedRes = imw_query($chkConsentScanedQry) or die(imw_error());
$chkConsentSignedNumRow= imw_num_rows($chkConsentSignedRes);
if($chkConsentSignedNumRow>0) {
	$chkConsentSignedRow = imw_fetch_array($chkConsentSignedRes);
	$scanConsentId = $chkConsentSignedRow['scan_consent_id'];
	$pdfFilePath = $chkConsentSignedRow['pdfFilePath'];
	$imageType = $chkConsentSignedRow['image_type'];
}
//END CODE TO CHECK THE SIGNED RECORD

?>
<form name="frm_showImg" action="showImg.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id;?>">
	<input type="hidden" name="intPatientWaitingId" value="<?php echo $intPatientWaitingId;?>">
	<input type="hidden" name="intConsentTemplateId" value="<?php echo $intConsentTemplateId;?>">
	<input type="hidden" name="consentAllMultipleId" value="<?php echo $consentAllMultipleId;?>">
	<input type="hidden" name="hidd_scanConsentId" value="<?php echo $scanConsentId;?>">	
	<input type="hidden" name="hiddDelSubmit" value="true">
</form>

<table class="table_collapse" style="border:none;">
	<tr>
		<td class="alignCenter">
			<?php
			if($imageType=='application/pdf' || $pdfFilePath){
			?>
				<iframe name="content_child" id="content_child" src="admin/<?php echo $pdfFilePath; ?>" style="width:99%; height:560px;"></iframe>
			<?php
			}else{
			?>
				<img id="imageTD" alt="" src="logoImg.php?from=iolink_scan_consent&amp;id=<?php echo $scanConsentId;?>">
			<?php	
			}
			?>
		</td>
	</tr>
</table>
<script>
	if(top.document.getElementById("anchorShow")) {
		top.document.getElementById("anchorShow").style.display = 'none';
	}
	if(top.document.getElementById("iolinkUploadBtn")) {
		top.document.getElementById("iolinkUploadBtn").style.display = 'none';
	}
	if(top.document.getElementById("PrintBtn")) {
		top.document.getElementById("PrintBtn").style.display = 'block';
	}
	if(top.document.getElementById("deleteSelected")) {
		top.document.getElementById("deleteSelected").style.display = 'block';
	}
	if(top.document.getElementById("hiddScanPdfId")) {
		top.document.getElementById("hiddScanPdfId").value = 'scanDelete';
	}
	if(top.document.getElementById("intConsentTemplateId")) {
		top.document.getElementById("intConsentTemplateId").value = '<?php echo $intConsentTemplateId;?>';
	}	
	if(top.document.getElementById("multiUploadImgBtn")) {
		top.document.getElementById("multiUploadImgBtn").style.display = 'none';
	}
</script>
<?php
	if($imageType=='application/pdf'){
		?>
		
		<script>
			if(top.document.getElementById("PrintBtn")) {
				top.document.getElementById("PrintBtn").style.display = 'none';
			}
		</script>
	<?php
	}
?>

</body>
</html>
