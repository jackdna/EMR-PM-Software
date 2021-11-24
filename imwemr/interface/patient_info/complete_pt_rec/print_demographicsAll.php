<?php
ob_start();
if($_REQUEST['patient_info'][0] == 'all'){
	include_once("print_demographics2.php");
	include_once("printMedicalHistory.php");
	include_once("insurance_print.php");								
	include_once("print_legal.php");
}
else if($_REQUEST['patient_info'][0] == 'face_sheet'){
	include_once("print_demographics2.php");
	include_once("insurance_print.php");
}
else if($_REQUEST['patient_info'][0] == 'printMedicalHistory.php'){
	include_once("printMedicalHistory.php");
}else{
	foreach($_REQUEST['patient_info'] as $val){
		echo("<table><tr><td width='100%'>");
		if($val== 'printMedicalHistory.php'){
			include_once("printMedicalHistory.php");
		}else{
			include_once($val);
		}
		echo("</td></tr></table>");
	}
}
$patient_print_data = ob_get_contents();
$patient_print_data=str_ireplace("<page></page>","",$patient_print_data);
$file_location = write_html($patient_print_data);
$PathForBrowserPDFDownload = "";
$PathForBrowserPDFDownload = "?file_location=".$file_location;

if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1){
	echo '<script type="text/javascript">window.location="sendfax_chart_summary.php?file_location='.$file_location.'&txtFaxRecipent='.trim($_REQUEST['selectReferringPhy']).'&txtFaxNo='.trim($_REQUEST['send_fax_number']).'";</script>';
	exit;
}
if(isset($_REQUEST['emailSubmit']) && intval($_REQUEST['emailSubmit'])==1){
	echo '<script type="text/javascript">window.location="send_email_chart_summary.php?txtEmailId='.trim($_REQUEST['send_email_id']).'&txtEmailName='.trim($_REQUEST['selectReferringPhyEmail']).'";</script>';
	exit;
}
ob_end_clean();
?>
<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php<?php echo $PathForBrowserPDFDownload; ?>" method="POST">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="P" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="images" value="<?php print $ChartNoteImagesStringFinal; ?>" >
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>
<script type="text/javascript">
	top.$("#div_loading_image").hide();
	document.printFrmALLPDF.submit();
</script>
