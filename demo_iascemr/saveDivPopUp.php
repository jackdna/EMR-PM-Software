<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<input type="hidden" name="hiddSaveAndPrintId" id="hiddSaveAndPrintId">
<?php
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
$rowcolor_discharge_summary_sheet="#FBF5EE";
if($_REQUEST['hiddResetStatusId']=='Yes') {
	$saveSuccessfullyMessage = "Record(s) Reset Successfully.";
}
if($_REQUEST['hiddPurgeResetStatusId']=='Yes') {
	$saveSuccessfullyMessage = "Record(s) Purged Successfully.";
}
if(!$saveSuccessfullyMessage) {
	$saveSuccessfullyMessage = "Record(s) Saved Successfully";
}
if($_REQUEST['SaveForm_alert'] == 'true') {
	if($_REQUEST['hiddInstrTmpltChangeId']!='yes') {//FROM INSTRUCTION SHEET
		echo "<script>top.frames[0].alert_msg('update','','<strong>".$saveSuccessfullyMessage."</strong>');</script> ";
	}
}

//IF SAVE AND PRINT BUTTON IS CLICKED THEN PRINT THIS CHART AFTER SAVE
if($_REQUEST["hiddSaveAndPrintId"]=="yes") {
	echo "<script>top.sav_print_pdf('".$_REQUEST['pConfId']."','".$get_http_path."','".$_REQUEST["go_pageval"]."');</script>";
}
//IF SAVE AND PRINT BUTTON IS CLICKED THEN PRINT THIS CHART AFTER SAVE

?>

