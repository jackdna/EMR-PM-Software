<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$ptWaitingArr = $error_result_med = $success_result_med = array();
$qry = "SELECT patient_in_waiting_id, patient_id, idoc_sch_athena_id FROM patient_in_waiting_tbl ORDER BY patient_in_waiting_id";
$res = imw_query($qry)  or $error_result_med[] = ($qry.imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$external_mrn = $row["idoc_sch_athena_id"];
		$ptWaitingArr[$external_mrn]["patient_in_waiting_id"] 	= $row["patient_in_waiting_id"];
		$ptWaitingArr[$external_mrn]["patient_id"] 				= $row["patient_id"];
	}
}

$medicationIdArr = array();
$qry = "SELECT prescription_medication_id, patient_in_waiting_id, patient_id, medication_external_id FROM iolink_patient_prescription_medication WHERE medication_external_id!='' ORDER BY prescription_medication_id ASC";
$res = imw_query($qry) or $error_result_med[] = ($qry.imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$chk_med_prescription_medication_id = $row["prescription_medication_id"];
		$chk_med_external_id 				= $row["medication_external_id"];
		$chk_med_patient_in_waiting_id 		= $row["patient_in_waiting_id"];
		$chk_med_patient_id 				= $row["patient_id"];
		$medicationIdArr[$chk_med_external_id][$chk_med_patient_in_waiting_id][$chk_med_patient_id] = $chk_med_prescription_medication_id;
	}
}

$medicationsObjArr = $dataArrNew[0]->Medication;
if(count($medicationsObjArr)>0) {
	foreach($medicationsObjArr as $medicationsObj) {
		$external_id 					= $medicationsObj->ExternalId;
		$prescription_medication_name 	= $medicationsObj->DrugName;
		$prescription_medication_desc  	= $medicationsObj->DosageQuantity->Value;
		$prescription_medication_sig  	= $medicationsObj->DosageQuantity->Units;
		$inteMedId						= $medicationsObj->inteMedId;
		$pt_waiting_id 					= $ptWaitingArr[$external_id]["patient_in_waiting_id"];
		$patient_id 					= $ptWaitingArr[$external_id]["patient_id"];
		unset($addMedicationArr);
		$addMedicationArr['prescription_medication_name'] 	= addslashes($prescription_medication_name);
		$addMedicationArr['prescription_medication_desc'] 	= addslashes($prescription_medication_desc);
		$addMedicationArr['prescription_medication_sig'] 	= addslashes($prescription_medication_sig);
		$addMedicationArr['patient_in_waiting_id'] 			= $pt_waiting_id;
		$addMedicationArr['patient_id'] 					= $patient_id;
		$addMedicationArr['endpoint_med_date_time'] 		= date("Y-m-d H:i:s");
		$addMedicationArr['medication_external_id'] 		= $inteMedId;
		if($pt_waiting_id) {
			$chkMedExistId = $medicationIdArr[$inteMedId][$pt_waiting_id][$patient_id];
			$savMedQry = " INSERT INTO ";
			$savMedWhr = "";
			if($chkMedExistId) {
				$savMedQry = " UPDATE ";
				$savMedWhr = " WHERE prescription_medication_id = '".$chkMedExistId."' ";
			}
			$savMedQry .= " iolink_patient_prescription_medication SET
						 prescription_medication_name 	= '".addslashes($prescription_medication_name)."',
						 prescription_medication_desc	= '".addslashes($prescription_medication_desc)."',
						 prescription_medication_sig	= '".addslashes($prescription_medication_sig)."',
						 patient_in_waiting_id 			= '".$pt_waiting_id."',
						 patient_id 					= '".$patient_id."',
						 endpoint_med_date_time			= '".date("Y-m-d H:i:s")."',
						 medication_external_id 		= '".$inteMedId."'
						 ".$savMedWhr;
			
			$savMedRes	= imw_query($savMedQry) or $error_result_med[] = ($savMedQry.imw_error());
			$success_result_med[] = $addMedicationArr;
		}else {
			$error_result_med[] = $addMedicationArr;	
		}
	}
}
$medMsg = "";
if(count($error_result_med)>0) {
	$medMsg =  "\n Medication Data Fail";
	$errorOutputMed = print_r($error_result_med, true);
	if(!trim($error_log_dir)) { $error_log_dir = $pdf_dir; }
	file_put_contents($error_log_dir.'/api_med_data_error'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$errorOutputMed, FILE_APPEND);
	file_put_contents($error_log_dir.'/api_med_data_error'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);
}else if(count($success_result_med)>0) {
	$medMsg =  "\n Medication Data Success";
}else {
	$medMsg = "\n No Medication data found in iASCLink";
}
echo $medMsg;
?>    

