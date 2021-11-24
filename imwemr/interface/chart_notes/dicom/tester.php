<?php
$ignoreAuth = true; 
error_reporting(-1);
require(dirname(__FILE__).'/dicom_link.php');
require(IMEDIC_DICOM.'/class_dicom.php');

$case = $_GET["op"];

switch($case){
	case "config":	
		$flg = "".DICOM_IS_WORKING;
		$flg=trim($flg);
	
		if(defined('DICOM_IS_WORKING') && $flg=='1'){
			echo nl2br("Dicom is configured for ".PRACTICE_PATH.".
				\n AE Title/port for Storage is ".DICOM_AE."/".DICOM_PORT."
				\n AE Title/port for Worklist is ".DICOM_AE_WLM."/".DICOM_WL_PORT." ");
		}else{
			echo "Dicom is not configured.";
		}
	break;
	/*
	case "upload":
		$d = new dicom_net;
		$d->file = trim("PDF.test");
		$out = $d->send_dcm(''.DICOM_IP, ''.DICOM_PORT, 'Test'.time(), ''.DICOM_AE, 0, 1, "", "", "", "");
		if (!empty($out)) {print "$out\n";}else{print "Dicom File uploaded successfully. Please check in application for patient id 1!\n";}
	break;
	*/
	
	case "storage":
		$d = new dicom_net;
		$out = $d->echoscu(''.DICOM_IP, ''.DICOM_PORT, 'Test'.time(), ''.DICOM_AE, SSL_ENABLE, "", "", "", "");
		if (!empty($out) && stripos($out, "Association Accepted")!==false && (stripos($out, "Received Echo Response (Success)")!==false || stripos($out, "Received Echo Response (Status: Success)")!==false) ) {			
			print "Storage service is working for ".PRACTICE_PATH." ";
		}else{print "Storage service is not working for ".PRACTICE_PATH." ";}
	break;
	case "worklist":
		$d = new dicom_net;
		$out = $d->findscu(''.DICOM_IP, ''.DICOM_WL_PORT, 'Test'.time(), ''.DICOM_AE_WLM, 0, SSL_ENABLE, "", "", "", "");
		if (!empty($out) && stripos($out, "Association Accepted")!==false && stripos($out, "Find Response")!==false) {
			print("Worklist service is working for ".PRACTICE_PATH." ");
		}else{print "Worklist service is not working for ".PRACTICE_PATH." ";}		
	break;
	
	default:
		echo "Invalid operation.";
	break;
}
?>