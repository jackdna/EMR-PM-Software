<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header('Access-Control-Allow-Origin: *');
include_once("../common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}

$pdf_dir = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles";	
//print'<pre>';print_r($_POST);
//$dataArrNew = (object)$_POST; //WITH JQUERY
//print'<pre>';
$output 		= print_r($_POST, true);
file_put_contents($pdf_dir.'/op_test.txt', $output);
$dataArrNew 	= json_decode(stripslashes($_POST['api_data_opnote'])); //WITH CURL
print'<pre>';print_r($_POST);
//print_r($dataArrNew);
$dataObjArr 	= $dataArrNew->opnote;
print_r($dataObjArr);
if(count($dataObjArr)>0) {
	foreach($dataObjArr as $dataObj) {
		$opreport_patient_id 		= $dataObj->opreport_patient_id;
		$opreport_confirmation_id 	= $dataObj->opreport_confirmation_id;
		$opreport_asc_id 			= $dataObj->opreport_asc_id;
		$opreport_appt_dos 			= $dataObj->opreport_appt_dos;
		$opreport_name 				= $dataObj->opreport_name;
		$opreport_pdf_file_name 	= $dataObj->opreport_pdf_file_name;
		$opreport_pdf_content 		= $dataObj->opreport_pdf_content;
	}
}
//die;

if($opreport_patient_id || $opreport_confirmation_id || $opreport_asc_id || $opreport_appt_dos || $opreport_name || $opreport_pdf_file_name) {
	$ret_result =  "Success";
}else {
	$ret_result =  "Fail";
}
echo $ret_result;
?>    
