<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$folderNameArr 		= array("Facesheet"=>"ptInfo","Ocular Hx"=>"ocularHx","Sx Planning Sheet"=>"clinical","General Health"=>"clinical","IOL"=>"iol","EKG"=>"ekg","H&P"=>"h&p","Other"=>"clinical");
$dataObjArr 		= $dataArrNew[0]->pdf_data;
$error_result = $success_result = array();
if(count($dataObjArr)>0) {
	$qry 			= "SELECT patient_in_waiting_id, patient_id, surgeon_fname, surgeon_mname, surgeon_lname, idoc_sch_athena_id FROM patient_in_waiting_tbl WHERE idoc_sch_athena_id !='' ORDER BY patient_in_waiting_id";
	$res 			= imw_query($qry) or $error_result[] = ($qry.imw_error());
	$wtRowArr 		= array();
	if(imw_num_rows($res)>0) {
		while($row	= imw_fetch_assoc($res)) {
			$wtRowArr[$row["idoc_sch_athena_id"]] =$row;
		}
	}
	foreach($dataObjArr as $dataObj) {
		$api_pdf_category 					= stripslashes($dataObj->api_pdf_category);
		$api_pdf_file_name 					= stripslashes($dataObj->api_pdf_file_name);
		$api_appt_id 						= $dataObj->api_appt_id;
		$api_patient_id 					= $dataObj->api_patient_id;
		$api_appt_dos 						= $dataObj->api_appt_dos;
		$api_pdf_content 					= $dataObj->api_pdf_content;
		$api_pdf_id_primary					= $dataObj->api_pdf_id_primary;
		
		$patientInWaitingDataRow 			= $wtRowArr[$api_appt_id];
		$patient_in_waiting_id 				= $patientInWaitingDataRow["patient_in_waiting_id"];
		$patient_id 						= $patientInWaitingDataRow["patient_id"];
		if($api_appt_id && $patient_in_waiting_id) {
			$api_pdf_file_name 				= str_ireplace(".pdf","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace(".jpg","",$api_pdf_file_name);
			$api_pdf_file_name_new 			= $api_pdf_file_name;
			//$api_pdf_file_name 				= trim(preg_replace("/[^0-9a-zA-Z_\s]/","",$api_pdf_file_name));
			$api_pdf_file_name 				= str_ireplace(" ","_",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace(",","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("!","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("@","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("%","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("^","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("$","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("'","",$api_pdf_file_name);
			$api_pdf_file_name 				= str_ireplace("*","",$api_pdf_file_name);
			$api_pdf_file_name 				= trim($api_pdf_file_name);
			$api_pdf_file_name_new 			= trim(preg_replace("/[^A-Za-z0-9]/","",$api_pdf_file_name));
			if(strtolower(end(explode(".",$api_pdf_file_name)))!="pdf") {
				$api_pdf_file_name 			= $api_pdf_file_name.".pdf";	
			}

			$patientInWaitingSurgeonFname 	= $patientInWaitingDataRow["surgeon_fname"];
			$patientInWaitingSurgeonMname 	= $patientInWaitingDataRow["surgeon_mname"];
			$patientInWaitingSurgeonLname 	= $patientInWaitingDataRow["surgeon_lname"];
			if($patientInWaitingSurgeonMname){
				$patientInWaitingSurgeonMname= ' '.$patientInWaitingSurgeonMname;
			}
			$surgeonName = $patientInWaitingSurgeonFname.$patientInWaitingSurgeonMname.' '.$patientInWaitingSurgeonLname;
			$surgeonName = str_replace(" ","_",$surgeonName);
			$surgeonName = str_replace(",","",$surgeonName);
			$surgeonName = str_replace("!","",$surgeonName);
			$surgeonName = str_replace("@","",$surgeonName);
			$surgeonName = str_replace("%","",$surgeonName);
			$surgeonName = str_replace("^","",$surgeonName);
			$surgeonName = str_replace("$","",$surgeonName);
			$surgeonName = str_replace("'","",$surgeonName);
			$surgeonName = str_replace("*","",$surgeonName);
		
			if($folderNameArr[$api_pdf_category]) {
				$iolinkScanFolderName 	= $folderNameArr[$api_pdf_category];
			}else {
				$iolinkScanFolderName 	= "clinical";	
			}	
			$imageType 					= "application/pdf";
			
			$pdfFolderName 				= $pdf_dir."/".$surgeonName;
			$pdfFolderNameSave 			= 'pdfFiles/'.$surgeonName;
			
			if(is_dir($pdfFolderName)) {
				//DO NOT CREATE FOLDER AGAIN
			}else {
				mkdir($pdfFolderName, 0775);
			}

			unset($arrayRecord);
			$arrayRecord['image_type'] 					= $imageType;
			$arrayRecord['document_name'] 				= addslashes($api_pdf_file_name);
			$arrayRecord['patient_id'] 					= $patient_id;
			$arrayRecord['patient_in_waiting_id'] 		= $patient_in_waiting_id;
			$arrayRecord['scan_save_date_time'] 		= date('Y-m-d H:i:s');
			$arrayRecord['iolink_scan_folder_name'] 	= addslashes($iolinkScanFolderName);
			$arrayRecord['pdf_external_id'] 			= $api_pdf_id_primary;
			$scan_consent_id 	= "";
			//$chkQry 			= "SELECT scan_consent_id FROM iolink_scan_consent WHERE pdf_external_id = '".$api_pdf_id_primary."' AND pdf_external_id != '' AND patient_id = '".$patient_id."' AND patient_in_waiting_id = '".$patient_in_waiting_id."' AND document_name = '".addslashes($api_pdf_file_name)."' AND iolink_scan_folder_name = '".addslashes($iolinkScanFolderName)."' ORDER BY scan_consent_id DESC LIMIT 0,1";				
			$chkQry 			= "SELECT scan_consent_id FROM iolink_scan_consent WHERE pdf_external_id = '".$api_pdf_id_primary."' AND pdf_external_id != '' AND patient_id = '".$patient_id."' AND patient_in_waiting_id = '".$patient_in_waiting_id."' ORDER BY scan_consent_id DESC LIMIT 0,1";				
			$chkRes 			= imw_query($chkQry) or $error_result[] = ($chkQry.imw_error());
			$chkNumRow 			= imw_num_rows($chkRes);						
			$scan_consent_id 	= "";
			$savQry = " INSERT INTO ";
			$savWhr = "";
			$scan_consent_id = "";
			if($chkNumRow>0){
				$chkRow 		= imw_fetch_assoc($chkRes);
				$scan_consent_id= $chkRow["scan_consent_id"];
				$savQry = " UPDATE ";
				$savWhr = " WHERE scan_consent_id = '".$scan_consent_id."' ";
			}
			$savQry .= " iolink_scan_consent SET
						 image_type 			= '".$imageType."',
						 document_name 			= '".addslashes($api_pdf_file_name)."',
						 patient_id 			= '".$patient_id."',
						 patient_in_waiting_id 	= '".$patient_in_waiting_id."',
						 scan_save_date_time 	= '".date('Y-m-d H:i:s')."',
						 iolink_scan_folder_name= '".addslashes($iolinkScanFolderName)."',
						 pdf_external_id 		= '".$api_pdf_id_primary."' 
						 ".$savWhr;
			$savRes	= imw_query($savQry) or $error_result[] = ($savQry.imw_error());
			if(!$scan_consent_id) {
				$scan_consent_id = imw_insert_id();	
			}
			if($scan_consent_id) {
				$fileNameSave 						= "iolink_ext_".$patient_in_waiting_id."_".$scan_consent_id.".pdf";
				$pdfJpgFilePathDatabaseSave 		= $pdfFolderNameSave."/".$fileNameSave;
				$putPdfFilePath 					= $pdfFolderName."/".$fileNameSave;
				$putPdfDocdata 						= base64_decode($api_pdf_content);
				file_put_contents($putPdfFilePath,$putPdfDocdata);
				$PSize = "";
				if(file_exists($putPdfFilePath)) {
					$PSize = filesize($putPdfFilePath);	
				}
				
				unset($arrayRecord);
				$arrayRecord['pdfFilePath'] 		= $pdfJpgFilePathDatabaseSave;
				$arrayRecord['document_size'] 		= $PSize;
				$updtQry = "UPDATE iolink_scan_consent SET pdfFilePath = '".$pdfJpgFilePathDatabaseSave."', document_size = '".$PSize."' WHERE scan_consent_id = '".$scan_consent_id."' ";
				$updtRes = imw_query($updtQry) or $error_result[] = ($updtQry.imw_error());
				$success_result[] = $arrayRecord;
			}else {
				$error_result[] = $arrayRecord;	
			}
		}
	}
}
$pdfMsg = "";
if(count($error_result)>0) {
	$pdfMsg = "\n PDF Fail";
	$errorOutput = print_r($error_result, true);
	if(!trim($error_log_dir)) { $error_log_dir = $pdf_dir; }
	file_put_contents($error_log_dir.'/api_data_error'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$errorOutput, FILE_APPEND);
	file_put_contents($error_log_dir.'/api_data_error'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);
	
}else if(count($success_result)>0) {
	$pdfMsg = "\n PDF Success";
}else {
	$pdfMsg = "\n No PDF record found in iASCLink";
}
echo $pdfMsg;
?>    
