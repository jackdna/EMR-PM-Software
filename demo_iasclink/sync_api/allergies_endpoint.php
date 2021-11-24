<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$ptWaitingArr = array();
$qry = "SELECT patient_in_waiting_id, patient_id, idoc_sch_athena_id FROM patient_in_waiting_tbl ORDER BY patient_in_waiting_id";
$res = imw_query($qry) or $error_result_allergy[] = ($qry.imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$external_mrn = $row["idoc_sch_athena_id"];
		$ptWaitingArr[$external_mrn]["patient_in_waiting_id"] 	= $row["patient_in_waiting_id"];
		$ptWaitingArr[$external_mrn]["patient_id"] 				= $row["patient_id"];
	}
}

$preOpAllergyIdArr = $error_result_allergy = $success_result_allergy = array();
$qry = "SELECT pre_op_allergy_id, patient_in_waiting_id, patient_id, allergy_external_id FROM iolink_patient_allergy WHERE allergy_external_id!='' ORDER BY pre_op_allergy_id ASC";
$res = imw_query($qry) or $error_result_allergy[] = ($qry.imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$chk_pre_op_allergy_id = $row["pre_op_allergy_id"];
		$chk_allergy_external_id = $row["allergy_external_id"];
		$chk_patient_in_waiting_id = $row["patient_in_waiting_id"];
		$chk_patient_id = $row["patient_id"];
		$preOpAllergyIdArr[$chk_allergy_external_id][$chk_patient_in_waiting_id][$chk_patient_id] = $chk_pre_op_allergy_id;
	}
}

$allergyObjArr = $dataArrNew[0]->Allergies;
if(count($allergyObjArr)>0) {
	foreach($allergyObjArr as $allergyObj) {
		$external_id 		= $allergyObj->ExternalId;
		$allergy_name 		= $allergyObj->AllergyCode->Description;
		$reaction_name 		= $allergyObj->ReactionDesc;
		$inteAllergyId		= $allergyObj->inteAllergyId;
		$pt_waiting_id 		= $ptWaitingArr[$external_id]["patient_in_waiting_id"];
		$patient_id 		= $ptWaitingArr[$external_id]["patient_id"];
		unset($addAllergyArr);
		$addAllergyArr['patient_in_waiting_id'] 	= $pt_waiting_id;
		$addAllergyArr['patient_id'] 				= $patient_id;
		$addAllergyArr['allergy_external_id'] 		= $inteAllergyId;
		$addAllergyArr['allergy_name'] 				= addslashes($allergy_name);
		$addAllergyArr['reaction_name'] 			= addslashes($reaction_name);
		$addAllergyArr['endpoint_allergy_date_time']= date("Y-m-d H:i:s");
		if($pt_waiting_id) {
			$chkAllergyExistId = $preOpAllergyIdArr[$inteAllergyId][$pt_waiting_id][$patient_id];
			$savAlrgQry = " INSERT INTO ";
			$savAlrgWhr = "";
			if($chkAllergyExistId) {
				$savAlrgQry = " UPDATE ";
				$savAlrgWhr = " WHERE pre_op_allergy_id = '".$chkAllergyExistId."' ";
			}
			$savAlrgQry .= " iolink_patient_allergy SET
						 patient_in_waiting_id 		= '".$pt_waiting_id."',
						 patient_id 				= '".$patient_id."',
						 allergy_external_id 		= '".$inteAllergyId."',
						 allergy_name 				= '".addslashes($allergy_name)."',
						 reaction_name 				= '".addslashes($reaction_name)."',
						 endpoint_allergy_date_time	= '".date("Y-m-d H:i:s")."'
						 ".$savAlrgWhr;
			$savAlrgRes	= imw_query($savAlrgQry) or $error_result_allergy[] = ($savAlrgQry.imw_error());
			$success_result_allergy[] = $addAllergyArr;
		}else {
			$error_result_allergy[] = $addAllergyArr;	
		}
	}
}
$allergyMsg = "";
if(count($error_result_allergy)>0) {
	$allergyMsg =  "\n Allergy Data Fail";
	$errorOutputAllergy = print_r($error_result_allergy, true);
	if(!trim($error_log_dir)) { $error_log_dir = $pdf_dir; }
	file_put_contents($error_log_dir.'/api_allergy_data_error'.$dt_frmt.'.txt', date("Y-m-d H:i:s")." \n".$errorOutputAllergy, FILE_APPEND);
	file_put_contents($error_log_dir.'/api_allergy_data_error'.$dt_frmt.'.txt', "\n============================\n", FILE_APPEND);
}else if(count($success_result_allergy)>0) {
	$allergyMsg =  "\n Allergy Data Success";
}else {
	$allergyMsg = "\n No allergy data found in iASCLink";
}
echo $allergyMsg;
?>    

