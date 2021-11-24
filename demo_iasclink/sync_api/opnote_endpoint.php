<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$dataObjArr 	= $dataArrNew[0]->opnote;
$error_result 	= $success_result = array();
$pdfMsg = $dataVal = "";
if(count($dataObjArr)>0) {
	foreach($dataObjArr as $dataObj) {
		$opreport_id 				= $dataObj->opreport_id;
		$opreport_patient_id 		= $dataObj->opreport_patient_id;
		$opreport_confirmation_id	= $dataObj->opreport_confirmation_id;
		$opreport_asc_id 			= $dataObj->opreport_asc_id;
		$opreport_appt_dos 			= $dataObj->opreport_appt_dos;
		$iasclink_appt_id 			= $dataObj->iasclink_appt_id;
		$inte_appt_id 				= $dataObj->inte_appt_id;
		$opreport_name 				= stripslashes($dataObj->opreport_name);
		$opreport_pdf_file_name 	= stripslashes($dataObj->opreport_pdf_file_name);
		$opreport_pdf_content 		= base64_decode(stripslashes($dataObj->opreport_pdf_content));
		
		file_put_contents($logFolderPath.'/'.$opreport_pdf_file_name, $opreport_pdf_content);
		$dataVal .= "\n opreport_id=".$opreport_id.", opreport_patient_id=".$opreport_patient_id.", opreport_confirmation_id=".$opreport_confirmation_id.", opreport_asc_id=".$opreport_asc_id.", opreport_appt_dos=".$opreport_appt_dos.", opreport_name=".$opreport_name.", opreport_pdf_file_name=".$opreport_pdf_file_name.", inte_appt_id=".$inte_appt_id.", iasclink_appt_id=".$iasclink_appt_id.", pdf_file_path=".$logFolderPath.'/'.$opreport_pdf_file_name;
		
	}
	file_put_contents($logFolderPath.'/opreport_receive_'.$opreport_confirmation_id.'.txt', $dataVal);
	$pdfMsg = "\n Op Notes PDF Success";
}else {
	$pdfMsg = "\n No Op Notes PDF found in ASCEMR";
}
echo $pdfMsg;
?>    
