<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
$basePrevQry	=	"SELECT hq.form_status FROM preophealthquestionnaire hq 
					 INNER JOIN patientconfirmation pc ON (pc.patientConfirmationId = hq.confirmation_id)
					 Where hq.confirmation_id = '".$_REQUEST['pConfId']."' ";
$basePrevRes	=	imw_query($basePrevQry) or die('Error Found at Line No.'.(__LINE__).':--- '.$basePrevQry.'---'.imw_error());

//START GET PREVIOUS VISIT
$basePrevFormStatus = "";
if(imw_num_rows($basePrevRes) > 0) {
	$basePrevRow		=	imw_fetch_object($basePrevSql);
	$basePrevFormStatus	=	$basePrevRow->form_status;
}

if(trim($basePrevFormStatus) == "" || $iolink_patient_in_waiting_id) { //COPY ALLERGY/MED IF HEALTH QUESTIONNAIRE NOT SAVED YET OR COPY  ALLERGY/MED IF CHART LOAD FIRST TIME 
	
	$prevVisitQry 	= "SELECT pc.patientConfirmationId,pc.allergiesNKDA_status,no_medication_status,no_medication_comments FROM patientconfirmation pc
						INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_status !='Canceled' AND st.patient_status !='No Show' AND st.patient_status !='Aborted Surgery')
						WHERE pc.dos  < '".$Confirm_patientDos."'
						AND pc.patientId = '".$_REQUEST['patient_id']."'
						ORDER BY pc.dos DESC, pc.patientConfirmationId DESC LIMIT 0,1
						";
	$prevVisitRes	=	imw_query($prevVisitQry) or die($prevVisitQry.'---'.imw_error());
	$prevVisitNumRow=	imw_num_rows($prevVisitRes);
	if($prevVisitNumRow >0) {
		$prevVisitRow	=	imw_fetch_object($prevVisitRes);

		 //START SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
		 unset($arrayNoMedData);
		 $arrayNoMedData['allergiesNKDA_status'] 		= $prevVisitRow->allergiesNKDA_status;
		 $arrayNoMedData['no_medication_status'] 		= $prevVisitRow->no_medication_status;
		 $arrayNoMedData['no_medication_comments'] 		= addslashes($prevVisitRow->no_medication_comments);
		 $objManageData->updateRecords($arrayNoMedData, "patientconfirmation", "patientConfirmationId", $_REQUEST['pConfId']);
		 //END SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
		
		
		//START COPY ALLERGIES FROM PREVIOUS HealthQuestionnaire CHART
		$prevAllergyCopyGetQry	=	"Select pre_op_allergy_id, allergy_name, reaction_name From patient_allergies_tbl Where patient_confirmation_id = '".$prevVisitRow->patientConfirmationId."' AND patient_confirmation_id!='0' ";
		$prevAllergyCopyGetRes	=	imw_query($prevAllergyCopyGetQry) or die($prevAllergyCopyGetQry.'---'.imw_error());
		$prevAllergyCopyGetNumRow	=	imw_num_rows($prevAllergyCopyGetRes);
		if($prevAllergyCopyGetNumRow) {
			while($prevAllergyCopyGetRow	=	imw_fetch_object($prevAllergyCopyGetRes)) {
				unset($prevAllergyCopyArr);
				$prevAllergyCopyArr['allergy_name'] 			= addslashes($prevAllergyCopyGetRow->allergy_name);
				$prevAllergyCopyArr['reaction_name'] 			= addslashes($prevAllergyCopyGetRow->reaction_name);
				$prevAllergyCopyArr['patient_confirmation_id'] 	= $_REQUEST['pConfId'];
				$prevAllergyCopyArr['patient_id'] 				= $_REQUEST['patient_id'];
				
				$chkPrevAllergyCopyExist = $objManageData->getMultiChkArrayRecords('patient_allergies_tbl', $prevAllergyCopyArr);
				if($chkPrevAllergyCopyExist) {
					//DO NOT UPDATE/COPY
				}else {
					$prevAllergyCopyArr['operator_name'] 		= $_SESSION['loginUserName'];
					$prevAllergyCopyArr['operator_id'] 			= $_SESSION['loginUserId'];
					$objManageData->addRecords($prevAllergyCopyArr, 'patient_allergies_tbl');
				}
			}
		}
		//END COPY ALLERGIES FROM PREVIOUS HealthQuestionnaire CHART
		
		//START COPY MEDICATION FROM PREVIOUS HealthQuestionnaire CHART
		$prevMedCopyGetQry	=	"SELECT prescription_medication_id, prescription_medication_name, prescription_medication_desc, prescription_medication_sig 
								 FROM patient_prescription_medication_healthquest_tbl WHERE confirmation_id = '".$prevVisitRow->patientConfirmationId."' ";
		$prevMedCopyGetRes	=	imw_query($prevMedCopyGetQry) or die($prevMedCopyGetQry.'---'.imw_error());
		$prevMedCopyGetNumRow	=	imw_num_rows($prevMedCopyGetRes);
		if($prevMedCopyGetNumRow)
		{
			while($prevMedCopyGetRow	=	imw_fetch_object($prevMedCopyGetRes)) {
				unset($prevAddMedicationArr);
				$prevAddMedicationArr['prescription_medication_name'] 	= addslashes($prevMedCopyGetRow->prescription_medication_name);
				$prevAddMedicationArr['prescription_medication_desc'] 	= addslashes($prevMedCopyGetRow->prescription_medication_desc);
				$prevAddMedicationArr['prescription_medication_sig'] 	= addslashes($prevMedCopyGetRow->prescription_medication_sig);
				$prevAddMedicationArr['confirmation_id'] 				= $_REQUEST['pConfId'];
				$prevAddMedicationArr['patient_id'] 					= $_REQUEST['patient_id'];
				
				$chkPrevMediExist = $objManageData->getMultiChkArrayRecords('patient_prescription_medication_healthquest_tbl', $prevAddMedicationArr);
				if($chkPrevMediExist) {
					//$objManageData->updateRecords($prevAddMedicationArr, 'patient_prescription_medication_healthquest_tbl', 'prescription_medication_id', $medicationId[$Key]);
				}else {	
					$prevAddMedicationArr['operator_name'] 				= $_SESSION['loginUserName'];
					$prevAddMedicationArr['operator_id'] 				= $_SESSION['loginUserId'];
					$objManageData->addRecords($prevAddMedicationArr, 'patient_prescription_medication_healthquest_tbl');
				}
			}
		}
		//END COPY MEDICATION FROM PREVIOUS HealthQuestionnaire CHART
	}
}

//END GET PREVIOUS VISIT


?>