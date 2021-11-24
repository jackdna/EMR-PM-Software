<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
$countRows = count($menuListArr);
$divWith = $width + 15;
//include "common/linkfile.php";
if(!$surgeryCenterDirectoryName) {
	$surgeryCenterDirectoryName='surgerycenter';
}
if(!$iolinkDirectoryName) {
	$iolinkDirectoryName='iolink';
}
if(!$rootServerPath) { $rootServerPath=$_SERVER['DOCUMENT_ROOT']; }
include_once('admin/classObjectFunction.php');
$objManageData = new manageData;
$loginUser 	= $_SESSION['loginUserId'];
$pConfId 	= $_REQUEST['pConfId'];
$patient_id = $_REQUEST['patient_id'];
if($pConfId && !$_REQUEST['stub_id'])
{
	$stubRecord	=	$objManageData->getExtractRecord('stub_tbl','patient_confirmation_id',$pConfId,'stub_id, appt_id');
	$_REQUEST['stub_id']	=	$stubRecord['stub_id'];
	$iascApptId				=	$stubRecord['appt_id'];
	
}else {
	$stubRecord	=	$objManageData->getExtractRecord('stub_tbl','stub_id',$_REQUEST['stub_id'],'stub_id, appt_id');
	$iascApptId				=	$stubRecord['appt_id'];
}	

unset($userPrivilegesArr);
unset($admin_privilegesArr);
$authenticationDetails 	= $objManageData->getRowRecord('users', 'usersId', $loginUser);
$user_type 				= $authenticationDetails->user_type;
$userPrivileges			= $authenticationDetails->user_privileges;
$admin_privileges 		= $authenticationDetails->admin_privileges;
$userPrivilegesArr 		= explode(', ', $userPrivileges);	
if($admin_privileges){
	$admin_privilegesArr= explode(', ', $admin_privileges);
}else{
	$admin_privilegesArr= array(); 
}

// get Injection/Misc Category Procedure Id


// End get Injection/Misc Category Procedure Id

//gurleen laser
	$chkprocedureConfirmationDetails 	= $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
	$Confirm_patientPrimProc 			= stripslashes($chkprocedureConfirmationDetails->patient_primary_procedure);
	$Confirm_patientPrimaryProcedureId 	= $chkprocedureConfirmationDetails->patient_primary_procedure_id;
	$Confirm_patientDos 				= $chkprocedureConfirmationDetails->dos;
	$Confirm_patientId 					= $chkprocedureConfirmationDetails->patientId;
	$finalizeStatusChk 					= $chkprocedureConfirmationDetails->finalize_status;
	$primary_procedure_is_inj_misc	=	$chkprocedureConfirmationDetails->prim_proc_is_misc;
	$confirm_surgeronID					=	$chkprocedureConfirmationDetails->surgeonId;
	
	$primary_procedureQry 			= "SELECT * FROM procedures WHERE name = '".$Confirm_patientPrimProc."' OR procedureAlias='".$Confirm_patientPrimProc."'";
	$primary_procedureRes 			= imw_query($primary_procedureQry);
	if(imw_num_rows($primary_procedureRes)<=0) {
		$primary_procedureQry 			= "SELECT * FROM procedures WHERE procedureId = '".$Confirm_patientPrimaryProcedureId."'";
		$primary_procedureRes 			= imw_query($primary_procedureQry);
	}
	$patient_primary_procedure_categoryID='';
	if(imw_num_rows($primary_procedureRes)>0) {
		$primary_procedureRow 			= imw_fetch_array($primary_procedureRes);
		$patient_primary_procedure_categoryID = $primary_procedureRow['catId'];
	}
	
			
	if($patient_primary_procedure_categoryID <> '2'  )
	{
		if($primary_procedure_is_inj_misc == '')
		{
			//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $patient_primary_procedure_categoryID);
			$primary_procedure_is_inj_misc		=	$objManageData->verifyProcIsInjMisc($Confirm_patientPrimaryProcedureId);
			//($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
		}
	}
	else
	{
		$primary_procedure_is_inj_misc	=	'';
	}

//gurleen lasers

//set http
if(strpos($_SERVER['HTTP_REFERER'], 'https') !== false){
		$get_http_path = 'https';
         }
	elseif(strpos($_SERVER['HTTP_REFERER'], 'http') !== false)
	{
		$get_http_path= 'http';
	}
//set http

// Load SurgeryCenter Settings as required
$surgeryCenterSettings	=	$objManageData->loadSettings('peer_review, safety_check_list');

// Get Surgeon Practice Match Result if Peer Review Option is on
$surgeryCenterPeerReview=	$surgeryCenterSettings['peer_review'];
$practiceNameMatch	=	'';
if($surgeryCenterPeerReview == 'Y' && $user_type == 'Surgeon')
{
	$practiceNameMatch	=	$objManageData->getPracMatchUserId($loginUser,$confirm_surgeronID);
}
// Get Surgeon Practice Match Result if Peer Review Option is on

// Get Status to show Safety check list chart from admin -> settings
$showCheckListAdmin = $surgeryCenterSettings['safety_check_list'] ? true : false;
// Get Status to show Safety check list chart from admin -> settings

//START CODE TO RESTORE SIGNED CONSENT FORMS OF PREVIOUS PATIENT (IF CANCELED)
$old_days_count = intval(90);
$expireDaysQry = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1' LIMIT 0,1";
$expireDaysRes = imw_query($expireDaysQry) or die(imw_error());
if(imw_num_rows($expireDaysRes)>0) {
	$expireDaysRow = imw_fetch_assoc($expireDaysRes);
	if(trim($expireDaysRow["documentsExpireDays"])) {
		$old_days_count = intval($expireDaysRow["documentsExpireDays"]);	
	}
}
$stubFromDt = $objManageData->getDateSubtract($Confirm_patientDos, $old_days_count); //NINTY DAYS OLDER DOS/DOCUMENTS SHOULD NOT BE RESTORED
$stubToDt 	= $Confirm_patientDos;
/*
$chkRestoreConsentFormQry="SELECT stub_tbl.patient_status,patientconfirmation.patientConfirmationId ,patientconfirmation.dos 
							FROM stub_tbl,patientconfirmation 
							WHERE patientconfirmation.dos < '".$Confirm_patientDos."'
							AND patientconfirmation.patientId='".$Confirm_patientId."'
							AND patientconfirmation.patientConfirmationId=stub_tbl.patient_confirmation_id
							ORDER BY patientconfirmation.dos DESC LIMIT 0,1
						   ";*/
$chkRestoreConsentFormQry="SELECT st.patient_status,pc.patientConfirmationId, pc.dos 
							FROM stub_tbl st, patientconfirmation pc
							WHERE pc.patientId 			 	= '".$Confirm_patientId."'
							AND pc.dos 					   != '".$Confirm_patientDos."'
							AND pc.dos 					   >= '".$stubFromDt."'
							AND st.appt_id 			 	 	= '".$iascApptId."'
							AND (st.patient_status 		 	= 'Canceled' OR st.patient_status = 'No Show' OR st.patient_status = 'Aborted Surgery')
							AND st.patient_confirmation_id 	= pc.patientConfirmationId
							ORDER BY st.stub_id DESC LIMIT 0,1
						  ";			
$chkRestoreConsentFormRes = imw_query($chkRestoreConsentFormQry) or die(imw_error());						
$chkRestoreConsentFormNumRow = imw_num_rows($chkRestoreConsentFormRes); 
if($chkRestoreConsentFormNumRow>0) {
	$chkRestoreConsentFormRow= imw_fetch_array($chkRestoreConsentFormRes);
	$stub_patient_status = $chkRestoreConsentFormRow['patient_status'];
	if($stub_patient_status=='Canceled' || $stub_patient_status=='No Show' || $stub_patient_status=='Aborted Surgery') {
		$chkRestorePtConfId	= $chkRestoreConsentFormRow['patientConfirmationId'];
		$chkRestorePtDos	= $chkRestoreConsentFormRow['dos'];
		$saveRestoreConsentFormQry = "UPDATE consent_multiple_form cm, patientconfirmation pc SET 
									 	cm.confirmation_id			= 	'".$_REQUEST['pConfId']."', 
										cm.restoreFromDos			= 	'".$chkRestorePtDos."' 
										WHERE cm.confirmation_id	=	'".$chkRestorePtConfId."'
										AND (cm.form_status 		= 'completed' OR cm.form_status = 'not completed')
										AND cm.confirmation_id 		= pc.patientConfirmationId
										AND pc.dos 				   >=  '".$stubFromDt."'
								";
		$saveRestoreConsentFormRes = imw_query($saveRestoreConsentFormQry) or die(imw_error());
		
		$saveRestoreHealthQuestQry = "UPDATE preophealthquestionnaire ph, patientconfirmation pc SET 
									 	ph.confirmation_id			= 	'".$_REQUEST['pConfId']."', 
										ph.restoreFromDos			= 	'".$chkRestorePtDos."' 
										WHERE ph.confirmation_id	=	'".$chkRestorePtConfId."'
										AND (ph.form_status 		= 'completed' OR ph.form_status = 'not completed')
										AND ph.confirmation_id 		= pc.patientConfirmationId
										AND pc.dos 				   >=  '".$stubFromDt."'
									";
		$saveRestoreHealthQuestRes = imw_query($saveRestoreHealthQuestQry) or die(imw_error());
				
		//START RESTORE SCANNED ITEM TO NEW DATE
		$restoreScanDocQry = "UPDATE scan_documents sd, scan_upload_tbl su 
								SET sd.confirmation_id= '".$_REQUEST['pConfId']."' , sd.dosOfScan='".$Confirm_patientDos."' 
								WHERE sd.confirmation_id='".$chkRestorePtConfId."'
								AND sd.document_id = su.document_id
								AND su.scan_upload_save_date_time >=  '".$stubFromDt."'
								AND sd.document_name!='Pt. Info'
								AND sd.document_name!='Clinical'
							";
		$restoreScanDocRes = imw_query($restoreScanDocQry) or die(imw_error());
		
		$restoreScanUploadQry = "UPDATE scan_upload_tbl SET confirmation_id= '".$_REQUEST['pConfId']."' 
								 WHERE confirmation_id='".$chkRestorePtConfId."'
								 AND scan_upload_save_date_time >=  '".$stubFromDt."'
								 ";
		$restoreScanUploadRes = imw_query($restoreScanUploadQry) or die(imw_error());
		
		$restoreFolderArr = array('Pt. Info', 'Clinical');
		foreach($restoreFolderArr as $restoreFolder){
		
			$restorePtInfoClinQry = "UPDATE scan_upload_tbl SET 
								document_id=(SELECT document_id FROM scan_documents WHERE confirmation_id= '".$_REQUEST['pConfId']."' AND document_name='".$restoreFolder."' ORDER BY document_id DESC LIMIT 0,1)
								WHERE confirmation_id='".$_REQUEST['pConfId']."'
								AND document_id IN(SELECT document_id FROM scan_documents WHERE confirmation_id= '".$chkRestorePtConfId."' AND document_name='".$restoreFolder."' )
								AND scan_upload_save_date_time >=  '".$stubFromDt."'
								";
			$restorePtInfoClinRes = imw_query($restorePtInfoClinQry) or die(imw_error());
			
		}
		//END RESTORE SCANNED ITEM TO NEW DATE
	}
}
//END CODE TO RESTORE SIGNED CONSENT FORMS OF PRVEIOUS PATIENT (IF CANCELED)

// GET DATA FROM PATIENT RECORDS.
	
	$patientFormDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
		$left_surgery_form 							= $patientFormDetails->surgery_form;
		$left_hippa_form 							= $patientFormDetails->hippa_form;
		$left_assign_benifits_form 					= $patientFormDetails->assign_benifits_form;
		$left_insurance_card_form 					= $patientFormDetails->insurance_card_form;
		$left_pre_op_health_ques_form 				= $patientFormDetails->pre_op_health_ques_form;
		$left_history_physical_form 				= $patientFormDetails->history_physical_form;
		$left_pre_op_nursing_form 					= $patientFormDetails->pre_op_nursing_form;
		$left_pre_nurse_alderate_form				= $patientFormDetails->pre_nurse_alderate_form;
		$left_post_op_nursing_form 					= $patientFormDetails->post_op_nursing_form;	
		$left_post_nurse_alderate_form				= $patientFormDetails->post_nurse_alderate_form;
		$left_pre_op_physician_order_form 			= $patientFormDetails->pre_op_physician_order_form;
		$left_post_op_physician_order_form 			= $patientFormDetails->post_op_physician_order_form;	
		$left_mac_regional_anesthesia_form 			= $patientFormDetails->mac_regional_anesthesia_form;
		$left_pre_op_genral_anesthesia_form 		= $patientFormDetails->pre_op_genral_anesthesia_form;	
		$left_genral_anesthesia_form 				= $patientFormDetails->genral_anesthesia_form;
		$left_genral_anesthesia_nurses_notes_form 	= $patientFormDetails->genral_anesthesia_nurses_notes_form;	
		$left_intra_op_record_form 					= $patientFormDetails->intra_op_record_form;
		$left_laser_procedure_form 					= $patientFormDetails->laser_procedure_form;
		$left_surgical_operative_record_form 		= $patientFormDetails->surgical_operative_record_form;	
		$left_qa_check_list_form 					= $patientFormDetails->qa_check_list_form;
		$left_discharge_summary_form 				= $patientFormDetails->discharge_summary_form;
		$left_post_op_instruction_sheet_form 		= $patientFormDetails->post_op_instruction_sheet_form;
		$left_medication_reconciliation_sheet_form	= $patientFormDetails->medication_reconciliation_sheet_form;
		$left_transfer_and_followups_form 			= $patientFormDetails->transfer_and_followups_form;
		$left_physician_amendments_form 			= $patientFormDetails->physician_amendments_form;
		$left_injection_misc_form 					= $patientFormDetails->injection_misc_form;
// GET DATA FROM PATIENT RECORDS. 

//FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED
	function getMultipleFormStatus($tablename,$pConfId,$consentTemplateId) {					
		$leftNaviConsentStatusQry = "select left_navi_status,form_status from $tablename where confirmation_id = '".$pConfId."' AND consent_template_id = '".$consentTemplateId."'";
		$leftNaviConsentStatusRes = imw_query($leftNaviConsentStatusQry) or die(imw_error());
		$leftNaviConsentStatusRow = imw_fetch_array($leftNaviConsentStatusRes);
		$leftNaviConsentStatus = $leftNaviConsentStatusRow["left_navi_status"];
		$form_status = $leftNaviConsentStatusRow["form_status"];
		//return $leftNaviConsentStatus;
		$statusArr = array($leftNaviConsentStatus,$form_status);
		return $statusArr;
	}

	function getTblFormStatus($tablename,$fieldName,$pConfId) {					
		$chkdFormStatusQry		= "SELECT form_status FROM $tablename WHERE $fieldName = '".$pConfId."' ";
		$chkFormStatusRes		= imw_query($chkdFormStatusQry) or die($chkdFormStatusQry.imw_error());	
		$chkFormStatusNumRow 	= imw_num_rows($chkFormStatusRes);	
		$chkFormStatus 			= imw_fetch_array($chkFormStatusRes);
		$formStatus 			= $chkFormStatus["form_status"];
		$frmStatusArr 			= array($formStatus);
		return $frmStatusArr;
	}
	
	
//END FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED

//START GET iolink_patient_in_waiting_id FROM IOLINK 
	$stub_id = $_REQUEST['stub_id'];
	$iolink_patient_in_waiting_id='';
	if($stub_id) {
		$iolinkIdStubQry 	= "select iolink_patient_in_waiting_id from `stub_tbl` where stub_id='".$stub_id."'";
		$iolinkIdStubRes 	= imw_query($iolinkIdStubQry) or die(imw_error()); 
		$iolinkIdStubNumRow = imw_num_rows($iolinkIdStubRes);
		if($iolinkIdStubNumRow>0) {
			$iolinkIdStubRow= imw_fetch_array($iolinkIdStubRes);
			$iolink_patient_in_waiting_id = $iolinkIdStubRow['iolink_patient_in_waiting_id'];
		}
		
		//UNSET VARIABLE $iolink_patient_in_waiting_id IF DATA HAS ALREADY SYNCRONIZED
		$iolinkPatientInWaitingTblQry = "SELECT patient_in_waiting_id FROM patient_in_waiting_tbl WHERE patient_in_waiting_id='".$iolink_patient_in_waiting_id."' AND iolinkSyncroStatus='Syncronized'";
		$iolinkPatientInWaitingTblRes = imw_query($iolinkPatientInWaitingTblQry) or die(imw_error()); 
		$iolinkPatientInWaitingTblNumRow = imw_num_rows($iolinkPatientInWaitingTblRes);
		if($iolinkPatientInWaitingTblNumRow>0) {
			$iolink_patient_in_waiting_id='';
		}else {
			$iolinkStatusDateTime = date('Y-m-d H:i:s');
			$iolinkUpdatePatientInWaitingTblQry = "UPDATE patient_in_waiting_tbl SET 
													iolinkSyncroStatus='Syncronized',
													iolinkSyncroStatusDateTime='".$iolinkStatusDateTime."'
													WHERE patient_in_waiting_id='".$iolink_patient_in_waiting_id."'";
			$iolinkUpdatePatientInWaitingTblRes = imw_query($iolinkUpdatePatientInWaitingTblQry) or die(imw_error()); 
		}
		//UNSET VARIABLE $iolink_patient_in_waiting_id IF DATA HAS ALREADY SYNCRONIZED
	
	}
	//END GET iolink_patient_in_waiting_id FROM IOLINK 

	//START SET IOLINK VALUES
	if($iolink_patient_in_waiting_id) {	
		//START INSERTING IOLINK ALLERGIS AND MEDICATION VALUES FOR HEALTHQUESTIONNAIRE
		
		
			//START ALLERGIES
			$iolinkAllergiesReactionDetails = $objManageData->getArrayRecords('iolink_patient_allergy', 'patient_in_waiting_id', $iolink_patient_in_waiting_id);
			
			if(count($iolinkAllergiesReactionDetails)>0){
				foreach($iolinkAllergiesReactionDetails as $iolinkAllergyName){
					$iolinkPreOpAllergyId 	= $iolinkAllergyName->pre_op_allergy_id;
					$iolinkAllergy = $iolinkAllergyName->allergy_name;
					$reaction = $iolinkAllergyName->reaction_name;		
					unset($iolinkAddAllergyArr);
					$iolinkAddAllergyArr['allergy_name'] = addslashes($iolinkAllergy);
					$iolinkAddAllergyArr['reaction_name'] = addslashes($reaction);
					$iolinkAddAllergyArr['patient_confirmation_id'] = $_REQUEST['pConfId'];
					$iolinkAddAllergyArr['patient_id'] = $iolinkAllergyName->patient_id;
					$iolinkAddAllergyArr['iolink_pre_op_allergy_id'] = $iolinkPreOpAllergyId;
					
					unset($iolinkAllergyChkArr);
					$iolinkAllergyChkArr['iolink_pre_op_allergy_id']= $iolinkPreOpAllergyId;
					$iolinkAllergyChkArr['patient_confirmation_id'] = $_REQUEST['pConfId'];
					$chkAllergyExist = $objManageData->getMultiChkArrayRecords('patient_allergies_tbl', $iolinkAllergyChkArr);
					if($chkAllergyExist) {
						$pre_op_allergy_id = $chkAllergyExist[0]->pre_op_allergy_id;
						$objManageData->updateRecords($iolinkAddAllergyArr, 'patient_allergies_tbl', 'pre_op_allergy_id', $pre_op_allergy_id);						
					}else {	
						$objManageData->addRecords($iolinkAddAllergyArr, 'patient_allergies_tbl');
					}
				}
			}

			if($stub_id)
			{
				
				$qCheck="SELECT piwt.iolink_allergiesNKDA_status, piwt.iolink_no_medication_status, piwt.iolink_no_medication_comments 
				FROM stub_tbl st 
				INNER JOIN patient_in_waiting_tbl piwt ON (piwt.patient_in_waiting_id=st.iolink_patient_in_waiting_id)
				WHERE st.stub_id='".$stub_id."' AND st.iolink_patient_in_waiting_id != '0'";
				$rCheck=imw_query($qCheck) or die(imw_error());
				if(imw_num_rows($rCheck)>0) {
					$dCheck=imw_fetch_object($rCheck);
					$iolink_allergiesNKDA_status	= $dCheck->iolink_allergiesNKDA_status;
					$iolink_no_medication_status	= $dCheck->iolink_no_medication_status;
					$iolink_no_medication_comments	= $dCheck->iolink_no_medication_comments;
					//START SET NKA STATUS AND NO MEDICATION STATUS TO PATIENT-CONFIRMATION ONLY IF ASC ID IS NOT ASSIGNED
					$updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".addslashes($iolink_allergiesNKDA_status)."', no_medication_status = '".addslashes($iolink_no_medication_status)."', no_medication_comments = '".addslashes($iolink_no_medication_comments)."' where patientConfirmationId = '".$pConfId."'";
					$updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
					
					
					if($iolink_allergiesNKDA_status=='Yes') {
						//DELETE ALLERGIES IF NKA STATUS=YES
						$objManageData->delRecord('patient_allergies_tbl', 'patient_confirmation_id', $pConfId);
					}
					if($iolink_no_medication_status=='Yes') {
						//DELETE ALLERGIES IF NKA STATUS=YES
						$objManageData->delRecord('patient_prescription_medication_healthquest_tbl', 'confirmation_id', $pConfId);
					}
				}
			}
			//END SET NKA STATUS TO PATIENT-CONFIRMATION
			
			
			//END ALLERGIES
			
			//START MEDICATION
			$iolikGetMedicationDetails = $objManageData->getArrayRecords('iolink_patient_prescription_medication', 'patient_in_waiting_id', $iolink_patient_in_waiting_id);
			
			if(count($iolikGetMedicationDetails)>0){
				foreach($iolikGetMedicationDetails as $iolinkMedication){
					$iolinkPrescriptionMedicationId = $iolinkMedication->prescription_medication_id;
					$iolinkMedicationName = $iolinkMedication->prescription_medication_name;
					$iolinkMedicationDesc = $iolinkMedication->prescription_medication_desc;
					$iolinkMedicationSig = $iolinkMedication->prescription_medication_sig;
					unset($iolinkAddMedicationArr);
					$iolinkAddMedicationArr['prescription_medication_name'] = addslashes($iolinkMedicationName);
					$iolinkAddMedicationArr['prescription_medication_desc'] = addslashes($iolinkMedicationDesc);
					$iolinkAddMedicationArr['prescription_medication_sig'] 	= addslashes($iolinkMedicationSig);
					$iolinkAddMedicationArr['confirmation_id'] 				= $_REQUEST['pConfId'];
					$iolinkAddMedicationArr['patient_id'] 					= $iolinkMedication->patient_id;
					$iolinkAddMedicationArr['iolink_prescription_medication_id']= $iolinkPrescriptionMedicationId;
					
					unset($iolinkMedicationChkArr);
					$iolinkMedicationChkArr['iolink_prescription_medication_id']= $iolinkPrescriptionMedicationId;
					$iolinkMedicationChkArr['confirmation_id'] = $_REQUEST['pConfId'];					
					$chkMediExist = $objManageData->getMultiChkArrayRecords('patient_prescription_medication_healthquest_tbl', $iolinkMedicationChkArr);
					if($chkMediExist) {
						$prescription_medication_id = $chkMediExist[0]->prescription_medication_id;
						$objManageData->updateRecords($iolinkAddMedicationArr, 'patient_prescription_medication_healthquest_tbl', 'prescription_medication_id', $prescription_medication_id);						
					}else {	
						$objManageData->addRecords($iolinkAddMedicationArr, 'patient_prescription_medication_healthquest_tbl');
					}

					// Insert IOLINK medications into table related to H&P medications	
					$chkMediExist1 = $objManageData->getMultiChkArrayRecords('patient_anesthesia_medication_tbl', $iolinkAddMedicationArr);
					if($chkMediExist1) {
						$prescription_medication_id_hp = $chkMediExist1[0]->prescription_medication_id;
						$objManageData->updateRecords($iolinkAddMedicationArr, 'patient_prescription_medication_healthquest_tbl', 'prescription_medication_id', $prescription_medication_id_hp);						
					}else {
						$objManageData->addRecords($iolinkAddMedicationArr, 'patient_anesthesia_medication_tbl');
					}

				}
			}
			//END MEDICATION
		//END INSERTING IOLINK ALLERGIS AND MEDICATION VALUES FOR HEALTHQUESTIONNAIRE
		
		
		//START IOL Model
			
		unset($iolinkIolModelArr);
		unset($iolCondArray) ;
		$iolCondArray['patient_in_waiting_id']	=	$iolink_patient_in_waiting_id ;
		$iolCondArray['opRoomDefault']	=	1;
		$iolinkIolModelDetails 	= $objManageData->getMultiChkArrayRecords('iolink_iol_manufacturer',$iolCondArray,'iol_manufacturer_id',' DESC LIMIT 0,1');
		if(!$iolinkIolModelDetails) {
			unset($iolCondArray) ;
			$iolCondArray['patient_in_waiting_id']	=	$iolink_patient_in_waiting_id ;
			$iolCondArray['opRoomDefault']	=	0;
			$iolinkIolModelDetails 	= $objManageData->getMultiChkArrayRecords('iolink_iol_manufacturer',$iolCondArray,'iol_manufacturer_id',' DESC LIMIT 0,1');
		}
		unset($iolinkAddIolModeArr);
		if(count($iolinkIolModelDetails) > 0)
		{
			$manufacturer	=	addslashes($iolinkIolModelDetails[0]->manufacture) ;
			$brand			=	addslashes($iolinkIolModelDetails[0]->lensBrand);
			$model			=	addslashes($iolinkIolModelDetails[0]->model);	
			$diopter		=	addslashes($iolinkIolModelDetails[0]->Diopter);
			
			$iolinkIolModelArr['confirmation_id'] = $_REQUEST['pConfId'];
			
			$chkOperatingRoomTbl	=	$objManageData->getMultiChkArrayRecords('operatingroomrecords',$iolinkIolModelArr);
			if($chkOperatingRoomTbl)
			{
				// Nothing to Do
			}
			else
			{
				
				$iolinkIolModelArr['manufacture'] 	= $manufacturer;
				$iolinkIolModelArr['lensBrand'] 	= $brand;
				$iolinkIolModelArr['model'] 		= $model;
				$iolinkIolModelArr['Diopter'] 		= $diopter;
				
				$objManageData->addRecords($iolinkIolModelArr,'operatingroomrecords');
			}
		}
		//END IOL MODEL
			
		
		//START CREATE PATIENT-INFO, CLINICAL AND OTHER FOLDERS FOR SCAN AND INSERT SCAN CARD FROM IOLINK	
		$pConfId 	= $_REQUEST['pConfId'];
		include("iosync_scan_consent.php");
		
		//END CREATE PATIENT-INFO, CLINICAL AND OTHER FOLDERS FOR SCAN AND INSERT SCAN CARD FROM IOLINK	
		
	}
//END SET IOLINK VALUES

//START ADD ALLERGIES AND MEDICATION FROM PREVIOUS VISIT
if( defined("ADD_PREV_VISIT_ALLERGY_MED") && constant("ADD_PREV_VISIT_ALLERGY_MED") == "YES") {		
	include("prev_visit_allergy_med_add.php");
}
//END ADD ALLERGIES AND MEDICATION FROM PREVIOUS VISIT


//START DELETE DUPLICATE BLANK SCAN FOLDERS
$pConfId 	= $_REQUEST['pConfId'];
include("delete_duplicate_folder.php");
//END DELETE DUPLICATE BLANK SCAN FOLDERS

// Start Copying H&P Fields From Previous Appointment
include_once('history_physicial_clearance_copy.php');
// End Copying H&P Fields From Previous Appointment
?>
<script>
var consentMultipleId='';
var consentMultipleAutoIncrId1='';
var hiddPurgestatus1='';
var editProcedure1='';
function left_link_click(pageName,thisId1,innerKey1,preColor1,patient_id1,pConfId1,ascId1,consentMultipleId,consentMultipleAutoIncrId1,hiddPurgestatus1,editProcedure1) {
	if(pageName!='consent_multiple_form.php' && editProcedure1!=1) {
		var innerKey1 = innerKey1-1;
	}	
	if(editProcedure1==1) {//WHEN EDIT PROCEDURE FROM MAINPAGE (common/header.php)
		preColor1 = escape(preColor1);
	}
	if(top.frames[0].document.forms[0]){
		top.frames[0].document.forms[0].innerKeyText.value = innerKey1;
	}
	if(consentMultipleId) {
		consentMultipleId ='&consentMultipleId='+consentMultipleId;
	}else {
		consentMultipleId ='';
	}
	//PURGE
	var consentMultipleAutoIncrIdLink='';
	if(consentMultipleAutoIncrId1) {
		consentMultipleAutoIncrIdLink ='&consentMultipleAutoIncrId='+consentMultipleAutoIncrId1;
	}
	var hiddPurgestatusLink='';
	if(hiddPurgestatus1){
		hiddPurgestatusLink='&hiddPurgestatus='+hiddPurgestatus1;
	}
	//PURGE
	if(typeof(top.frames[0])!="undefined") {
		if(typeof(top.frames[0].frames[0])!="undefined") {		
			top.frames[0].frames[0].location = pageName+'?patient_id='+patient_id1+'&pConfId='+pConfId1+'&rightClick=yes&thisId='+thisId1+'&innerKey='+innerKey1+consentMultipleId+consentMultipleAutoIncrIdLink+hiddPurgestatusLink+'&preColor='+preColor1;
		}
	}
	if(editProcedure1!=1)
	{
		$("#toggle_btn1").trigger('click');
	}
	
}
function consent_consent_up(obj_cat)
{
		if(document.getElementById(consent_display).style.display=="block") {
			document.getElementById(consent_display).style.display="none";
		}else if(document.getElementById(consent_display).style.display=="none") {
			document.getElementById(consent_display).style.display="block";
	}
}
function accessAlert(){ 
	alert('Access denied – You do not have permission to access this form') 
}
//print function
function printCategoryConsentFn(confId,catId,path)
{
 window.open('consent_multiple_category_print_pop.php?categoryId='+catId+'&pConfId='+confId+'&get_http_path='+path);
}

function CheckList(pageName,patient_id1,pConfId1) {
	if(typeof(top.frames[0])!="undefined") {
		if(typeof(top.frames[0].frames[0])!="undefined") {		
			top.frames[0].frames[0].location = pageName+'?patient_id='+patient_id1+'&pConfId='+pConfId1+'&rightClick=yes';
			$("#toggle_btn1").trigger('click');
		}
	}
	
}
//print function
</script>
<style>
.breakWord
{
	word-wrap:break-word;
	word-break:break-all;
	
	 /* These are technically the same, but use both */
	  overflow-wrap: break-word;
	  word-wrap: break-word;
	
	  -ms-word-break: break-all;
	  /* This is the dangerous one in WebKit, as it breaks things wherever */
	  word-break: break-all;
	  /* Instead use this non-standard one: */
	  word-break: break-word;
	
	  /* Adds a hyphen where the word breaks, if supported (No Blink) */
	  -ms-hyphens: auto;
	  -moz-hyphens: auto;
	  -webkit-hyphens: auto;
	  hyphens: auto;
}
</style>
<div class="toggled " id="slider_wrapper" >
	<input type="hidden" value="false" name="slideOut" id="slideOut">
	<input type="hidden" name="slider_color" id="slider_color"  />
	<input type="hidden" value="false" name="sliderRightOut" id="sliderRightOut">
	<input type="hidden" value="0" name="leftMainOpen" id="leftMainOpen">
	<input type="hidden" value="<?php echo $leftCounter; ?>" name="leftInnerOpen" id="leftInnerOpen">
	<input type="hidden" value="0" name="rightMainOpen" id="rightMainOpen">
	<input type="hidden" value="1" name="rightInnerOpen" id="rightInnerOpen">
	<input type="hidden" value="" name="mainMenu" id="mainMenu">
	<input type="hidden" value="" name="subMenuFld" id="subMenuFld">
	<input type="hidden" value="" name="pre_color" id="pre_color">
	<!-- PURGE -->
	<input type="hidden" value="" name="pre_hiddPurgestatus" id="pre_hiddPurgestatus">	
	<!-- PURGE -->	
    <a class="btn btn-info" id="toggle_btn1" style=" margin-top:-37px; margin-left:5%;">
		Today's Visit
	</a>
    <a class="toggle_btn" id="" style="visibility:hidden">
        	<span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
	</a>
    
	<span id="SliderHeadBox"   >
    	<span class="btn" id="SliderHeadConsent"></span>
        <span class="btn" id="SliderHeadTitle"></span>
        <span class="btn" id="SliderHeadEpost"></span>
    </span>
    
    
	<nav class="navbar navbar-inverse bs-sidebar-navbar-collapse-1 toggled toggled_1" id="sidebar-wrapper" role="navigation">
        
        <!--<div class=" head_ul_side">
            <a href="javascript:void(0)"><Span class="span_over">  Today's Visit 	</Span>	</a>
        </div>-->
        	
        <div class="navbar-header">
        		<a class="btn btn-info" id="toggle_btn1" style=" margin:0px !important;padding:8px 8px 8px 0;">
                    Today's Visit
                </a>
              <!--<a class="toggle_btn style_2_Toggle" >
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>-->
                
        </div>
        
        <ul class="nav sidebar-nav tabs ">
		<?php
		$checkListFormsStatusArr = getTblFormStatus('surgical_check_list','confirmation_id',$pConfId);
		$checkListFormsStatus = $checkListFormsStatusArr[0];
		if($checkListFormsStatus== 'completed' ) {
			$chkMrkImageChkLst = "<img src='images/green_flag.png' style='width:12px; height:14px; border:none;'>";
		}else if($checkListFormsStatus== 'not completed') {
			$chkMrkImageChkLst = "<img src='images/red_flag.png' style='width:12px; height:14px; border:none;'>";
		}else {
			$chkMrkImageChkLst = "";
		}
		$i=0;
		$innKeys = 1;
		foreach($menuListArr as $key => $sliderListText){
			
			$level			=	0 ;
			$mainCounter	=	count($menuListArr);
			$innerCounter	=	count($subMenuListArr[$key]);
			$dataTarget		=	'';
			$dataToggle		=	"";
			
			
			if($innerCounter > 0)
			{
				$class		.=	' dropdown'.sprintf('%02d',($i+1)).' f_sidebar';
				$menuID		 =	preg_replace("/[^ \w]+/", "",$sliderListText).'_ul';
				$menuID		 =	str_replace(" ","_",$menuID);
				$spanIcon	 =	'<span class="span_over caret"></span>';
				$dataTarget	 =	'data-target = "#'.$menuID.'"';
				$dataToggle	 =	'data-toggle = "collapse"';	
					
			}
			else
			{	
				$class	.=	'f_sidebar';
				
			}
			
			
			?>
			
			<?php  if($key==0)
					{	
						$showCheckList = ( !$showCheckListAdmin && ($checkListFormsStatus == 'completed' || $checkListFormsStatus == 'not completed')) ? true : $showCheckListAdmin;
						$showCheckListStatus =  $objManageData->getChartShowStatus($pConfId,'checklist');
						$showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? true : ($showCheckListStatus == 2 ? false : $showCheckList)) : $showCheckList;
						if($Confirm_patientDos>=constant('CHECKLIST_DATE') && $showCheckList ) 
						{
                            $checkListFun = " CheckList('check_list.php','".$patient_id."','".$pConfId."'); ";
                            if($patient_primary_procedure_categoryID =='2') {  
                                //$checkListFun = " accessAlert(); ";
                            }	
            ?>
							<li class=" border_top_ul <?=$class?>" onclick="javascript:<?php echo (!$popMain ? $checkListFun : $popMain) ;?>" >
                            	<a href="javascript:void(0)">
                                	<span class="span_over">Check List</span>
                                    <label id="chkMrkImageStatusIdCheckList"><?php echo $chkMrkImageChkLst; ?></label>
                                </a>
                           	</li>     
			<?php
						}else {
							echo '<li class=" border_top_ul" style="display:none"></li>';
						}
			?>
            
            			<li class="<?=$class?>"  <?=$dataTarget?> <?=$dataToggle?> >
                        	<a href="javascript:void(0);">
                            	<span class="span_over"><?php echo $sliderListText; ?></span>
                                <?=$spanIcon?>
                           	</a>
                       	</li>     
			<?php		
					}
					else if($sliderListText=='ePostIt')
					{ //CODE FOR EPOST-IT
						
						$linkStatus = false;
						if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))
						{
							$linkStatus = true;
						}
						
						if($practiceNameMatch == 'yes')
						{
							$linkStatus = false;
						}
						//CHECK OF PATIENT IS FINALIZED THEN DO NOT INSERT EPOST-IT 
						//$chkEpostConfirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
						if($chkprocedureConfirmationDetails)
						{
							//$lnkname="under_construction.php";
							//$finalizeStatusChk = $chkEpostConfirmationDetails->finalize_status;
							if($linkStatus == false) {
								$popMain = "accessAlert();";
							}else if($finalizeStatusChk=="true") {
								$popMain = "void(0);";
							}else {
								$popMain = "if(typeof(epostpop)!='undefined'){epostpop('350','23');}";
							}	
						}
						//END CHECK OF PATIENT IS FINALIZED THEN DO NOT INSERT EPOST-IT	
				
			?>
            			<li class="<?=$class?>" onClick="javascript:<?php echo $popMain; ?>" >
                        	<a href="javascript:void(0)" >
								<span class="span_over"><?php echo $sliderListText; ?></span>
                          	</a>
                      	</li>
                              
			<?php
            		}
					else
					{
						
			?>
            			<li class="<?=$class?>" <?=$dataTarget?> <?=$dataToggle?> >
                        	<a href="javascript:void(0)" >
                            	<span class="span_over"><?php echo $sliderListText; ?></span>
                                <?=$spanIcon?>
                           	</a>
                     	</li>
			
            <?php
            		}
					
					$i++;
					
					/*
					*
					*Level 1 Menu Ends Here
					*
					*/
					
					if(count($subMenuListArr[$key])>0)
					{
						
						$ct=1;
						$level	=	sprintf('%02d',1)	;
						
						$collapsable	= true;
						$mainID			='main'.$key;
						$chkArray		=	array('main3','main5','main6','main7');
						if($user_type == 'Surgeon' && in_array($mainID,$chkArray) )
						{
							$collapsable	=	false;		
						}
			?>
            
            			<ul class="dropdown-menu<?=$level?> <?=(($collapsable) ? 'collapse' : '')?>" data-filter="<?=$mainID?>" id="<?=$menuID?>" role="menu">
                        
						<?php
							
                        	$consentMultipleId='';				
                        	
							foreach($subMenuListArr[$key] as $innKey => $inner)
							{
                            	$linkStatus = true;
                           		$displayStatus = true;
								$accessPermission = true;
								if($innKey == 0){
									$color = $subMenuListArr[$key][$innKey];
								}  	                                             
                            	if($innKey > 0)
								{ 
								
									$consentLeftNaviStatus='';
									//START CONSENT FORMS
									$chkConsentTemplateArrId = $consentFormTemplateSelectConsentId[$innKey-1]; //FROM MAIN SLIDER(common/mainslider.php)
									
									$chkConsentSurgeryArrQry = "select consent_template_id,surgery_consent_alias from `consent_multiple_form` where  confirmation_id = '".$pConfId."' AND consent_template_id='".$chkConsentTemplateArrId."'";
									$chkConsentSurgeryArrRes = imw_query($chkConsentSurgeryArrQry) or die(imw_error()); 
									$chkConsentSurgeryArrNumRow = imw_num_rows($chkConsentSurgeryArrRes);
									$chkConsentSurgeryArrId='';
									if($chkConsentSurgeryArrNumRow>0) {
										$chkConsentSurgeryArrRow = imw_fetch_array($chkConsentSurgeryArrRes);
										$chkConsentSurgeryArrId = $chkConsentSurgeryArrRow['consent_template_id'];
										$chkConsentSurgeryArrAlias = $chkConsentSurgeryArrRow['surgery_consent_alias'];
									}
									
									$tblename="consent_multiple_form";
									$consentMultipleId = $chkConsentSurgeryArrId;
									
									if(!$consentMultipleId){ $consentMultipleId = $chkConsentTemplateArrId; }
									if($subMenuListArr[0][$chkConsentSurgeryArrId] ==$subMenuListArr[$key][$chkConsentSurgeryArrId]){ 
										$lnkname="consent_multiple_form.php";
										$formName = "surgery_form";
										$patientconfirmationid1 ="confirmation_id";
										$tblename="consent_multiple_form";
			
									}
									//END CONSENT FORMS
									
									if($subMenuListArr[1][1] ==$subMenuListArr[$key][$innKey]){
										$consentMultipleId		= "";
										$lnkname				= "pre_op_health_quest.php";
										$formName 				= "pre_op_health_ques_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "preophealthquestionnaire";
										if($left_pre_op_health_ques_form!='false'){
											if( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 		= '#D1E0C9';
												$linkStatus = true;
											}else{
												$color 		= '#999999';
												$linkStatus = false;								
											}
										}else{
											$color 			= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus = true;
											}else{
												$linkStatus = false;
											}
										}							
									}
									else if($subMenuListArr[1][2] ==$subMenuListArr[$key][$innKey]){
										$consentMultipleId		= "";
										$lnkname				= "history_physicial_clearance.php";
										$formName 				= "history_physical_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "history_physicial_clearance";
										if($left_history_physical_form!='false'){
											if( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 		= '#D1E0C9';
												$linkStatus = true;
											}else{
												$color 		= '#999999';
												$linkStatus = false;								
											}
										}else{
											$color 			= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus = true;
											}else{
												$linkStatus = false;
											}
										}							
									}
									else if($subMenuListArr[2][1] ==$subMenuListArr[$key][$innKey]) {
										
										$lnkname				= "pre_op_nursing_record.php";
										$formName 				= "pre_op_nursing_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "preopnursingrecord";
										if($left_pre_op_nursing_form!='false'){
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 		= '#EFE492';
												$linkStatus = true;
											}else{
												$color 		= '#999999';
												$linkStatus = false;
											}								
										}else{
											$color = '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus = true;
											}else{
												$linkStatus = false;
											}
										}
										
									}
									else if($subMenuListArr[2][2] ==$subMenuListArr[$key][$innKey]) {
										
										$lnkname				= "pre_nurse_alderate_record.php";
										$formName 				= "pre_nurse_alderate_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "pre_nurse_alderate";
										if($left_pre_nurse_alderate_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#EFE492';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}
									}
									else if($subMenuListArr[2][3] ==$subMenuListArr[$key][$innKey]) {
										
										$lnkname				= "post_op_nursing_record.php";
										$formName 				= "post_op_nursing_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "postopnursingrecord";
										if($left_post_op_nursing_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#EFE492';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[2][4] ==$subMenuListArr[$key][$innKey]) {
										
										$lnkname				= "post_nurse_alderate_record.php";
										$formName 				= "post_nurse_alderate_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "post_nurse_alderate";
										
										if($left_post_nurse_alderate_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#EFE492';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[3][1] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "pre_op_physician_orders.php";
										$formName 				= "pre_op_physician_order_form";
										$patientconfirmationid1 = "patient_confirmation_id";
										$tblename				= "preopphysicianorders";
										if($left_pre_op_physician_order_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#DEA068';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[3][2] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "post_op_physician_orders.php";
										$formName 				= "post_op_physician_order_form";
										$patientconfirmationid1 = "patient_confirmation_id";
										$tblename				= "postopphysicianorders";
										if($left_post_op_physician_order_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#DEA068';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[4][1] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "local_anes_record.php";
										$formName 				= "mac_regional_anesthesia_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "localanesthesiarecord";
										if($left_mac_regional_anesthesia_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80AFEF';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color = '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[4][2] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "pre_op_general_anes.php";
										$formName 				= "pre_op_genral_anesthesia_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "preopgenanesthesiarecord";
										if($left_pre_op_genral_anesthesia_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80AFEF';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[4][3] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "gen_anes_rec.php";
										$formName 				= "genral_anesthesia_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "genanesthesiarecord";
										if($left_genral_anesthesia_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80AFEF';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[4][4] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "gen_anes_nurse_notes.php";
										$formName 				= "genral_anesthesia_nurses_notes_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "genanesthesianursesnotes";
										if($left_genral_anesthesia_nurses_notes_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80AFEF';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && ( in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[5][1] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "op_room_record.php";
										$formName 				= "intra_op_record_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "operatingroomrecords";
										if($left_intra_op_record_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80A7D6'; 
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
												$displayStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && !$primary_procedure_is_inj_misc) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
												$displayStatus 	= false;
											}
										}
									}
									else if($subMenuListArr[5][2] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "laser_procedure.php";
										$formName 				= "laser_procedure_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "laser_procedure_patient_table";
										if($left_laser_procedure_form!='false'){ 
											if(($patient_primary_procedure_categoryID =='2' && !$primary_procedure_is_inj_misc) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80A7D6';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
												$displayStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID =='2' && !$primary_procedure_is_inj_misc) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
												$displayStatus 	= false;
											}
										}	
									}
									else if($subMenuListArr[5][3] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "injection_misc.php";
										$formName 				= "injection_misc_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "injection";
										if($left_injection_misc_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2' && $primary_procedure_is_inj_misc) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#80A7D6'; 
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
												$displayStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2' && $primary_procedure_is_inj_misc) && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
												$displayStatus 	= false;
											}
										}
									}
									else if($subMenuListArr[6][1] ==$subMenuListArr[$key][$innKey]){
										$lnkname				= "operative_record.php";
										$formName 				= "surgical_operative_record_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "operativereport";
										if($left_surgical_operative_record_form!='false'){ 
											if(($patient_primary_procedure_categoryID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$color 			= '#D1E0C9';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;								
											}
										}else{
											$color 				= '#999999';
											if(($patient_primary_procedure_categoryID !='2') && (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr))){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}							
									}
									else if($subMenuListArr[7][1] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				= "discharge_summary_sheet.php";
										$formName 				= "discharge_summary_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "dischargesummarysheet";
										if($left_discharge_summary_form!='false'){
											if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 			= '#FCBE6F';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}
									}
									else if($subMenuListArr[8][1] ==$subMenuListArr[$key][$innKey]) {						
										$lnkname				= "instructionsheet.php";
										$formName 				= "post_op_instruction_sheet_form";
										$patientconfirmationid1 = "patient_confirmation_id";
										$tblename				= "patient_instruction_sheet";
										if($left_post_op_instruction_sheet_form!='false'){
											if(in_array("Super User", $userPrivilegesArr) || in_array("Billing", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 			= '#D1E0C9';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Billing", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}								
									}
									else if($subMenuListArr[8][2] ==$subMenuListArr[$key][$innKey]) {						
										
										$lnkname				= "medication_reconciliation_sheet.php";
										$formName 				= "medication_reconciliation_sheet_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "patient_medication_reconciliation_sheet";
										if($left_medication_reconciliation_sheet_form!='false'){
											if(in_array("Super User", $userPrivilegesArr) || in_array("Billing", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 			= '#D1E0C9';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Billing", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}								
									}
									else if($subMenuListArr[9][1] ==$subMenuListArr[$key][$innKey]) {						
										$lnkname				= "transfer_followups.php";
										$formName 				= "transfer_and_followups_form";
										$patientconfirmationid1 = "confirmation_id";
										$tblename				= "transfer_followups";
										if($left_transfer_and_followups_form!='false'){
											if(in_array("Super User", $userPrivilegesArr) || in_array("Billing", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 			= '#DEA068';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Billing", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}								
									}
									else if($subMenuListArr[10][1] ==$subMenuListArr[$key][$innKey]) {
										$lnkname				="amendments_notes.php";
										$formName 				= "physician_amendments_form";
										$patientconfirmationid1 ="patient_confirmation_id";
										if($left_physician_amendments_form!='false'){ 
											if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$color 			= '#D0D0ED';
												$linkStatus 	= true;
											}else{
												$color 			= '#999999';
												$linkStatus 	= false;
											}
										}else{
											$color 				= '#999999';
											if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
												$linkStatus 	= true;
											}else{
												$linkStatus 	= false;
											}
										}
									}
									else if($subMenuListArr[11][1] ==$subMenuListArr[$key][$innKey]) {
										/*
										//CHECK OF PATIENT IS FINALIZED THEN DO NOT INSERT EPOST-IT 
										$chkEpostConfirmationDetails = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
										if($chkEpostConfirmationDetails) {
											$lnkname="under_construction.php";
											$finalizeStatusChk = $chkEpostConfirmationDetails->finalize_status;
											if($finalizeStatusChk=="true") {
												$pop = "void(0);";
											}else {
												$pop = "epostpop('350','23');";
											}	
										}
										//END CHECK OF PATIENT IS FINALIZED THEN DO NOT INSERT EPOST-IT	
										$linkStatus = false;
										if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr)){
											$linkStatus = true;
										}
										$epostStatus = "yes";
										*/
									}
								   
									
									$kId			= $key.'&innerKey='.$innKeys.'&preColor='.urlencode($color);
									$tempPrecolor 	= urlencode($color);
									$linkSessionId 	= "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
									$subId 			= $innKeys;
									$innKeys++;
									
									//CODE TO CHECK IF FORM IS ALREADY IN USE FOR TODAY'S DATE
										$andConsentMultipleIdQry		= "";
										if($tblename=="consent_multiple_form") {
											$patientconfirmationid1		= 'confirmation_id';
											$andConsentMultipleIdQry 	= " AND consent_template_id = '$consentMultipleId'";
										}
										$chkdFormUsedQry="SELECT form_status FROM $tblename WHERE 
													$patientconfirmationid1 = '".$_REQUEST['pConfId']."' 
													$andConsentMultipleIdQry    
													";
										$chkFormUsedRes		= imw_query($chkdFormUsedQry) or die($chkdFormUsedQry.imw_error());	
										$chkFormUsedNumRow 	= imw_num_rows($chkFormUsedRes);	
										if($chkFormUsedNumRow > 0) {
											
										}else {
											$insConsentMultipleIdQry="";
											if($tblename=="consent_multiple_form") {
												$consentQry = "select consent_category_id from `consent_forms_template` where consent_id='".$consentMultipleId."'";
												$consentRes = imw_query($consentQry) or die(imw_error()); 
												$consentRow = imw_fetch_array($consentRes);
												$consent_cat_id=$consentRow['consent_category_id'];		
												$insConsentMultipleIdQry = "consent_category_id ='$consent_cat_id',consent_template_id = '$consentMultipleId', left_navi_status='true',";
											}
											$blankInsertQry = "insert into $tblename set 
																$patientconfirmationid1 = '".$_REQUEST['pConfId']."',
																$insConsentMultipleIdQry
																form_status= ''";
											$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
											
											
											if($tblename == "preopphysicianorders" || $tblename == "postopphysicianorders" || $tblename == "operativereport" || $tblename == "dischargesummarysheet") {
											?>
												<script>
													if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
														top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
													}	
												</script>
											<?php
											}
											$chkFormUsedRes		= imw_query($chkdFormUsedQry) or die($chkdFormUsedQry.imw_error());	
											$chkFormUsedNumRow 	= imw_num_rows($chkFormUsedRes);
										}
										//===========insert confirmation_id surgical_check_list=========//
										
										$chkFormUsed 		= imw_fetch_array($chkFormUsedRes);
										$formStatus 		= $chkFormUsed["form_status"];
										
										
										if(($formName === 'pre_op_health_ques_form') || $formName === 'history_physical_form')
										{
											$chkHPFolder	=	$objManageData->getDirContentStatus($pConfId,2);
											
											if($chkHPFolder && constant('CHECK_H_AND_P') <> 'NO')
											{
												$formStatus	=	'completed';
												imw_query("Update $tblename Set form_status = '".$formStatus."' Where ".$patientconfirmationid1." = '".$_REQUEST['pConfId']."'  ") or die('Error found at line no. '.(__LINE__).': '.imw_error());
											}
											elseif(($formStatus === 'completed' || $formStatus === 'not completed'))
											{
												$chartStatus	=	$objManageData->validateChart($lnkname,$_REQUEST['pConfId'],$patient_primary_procedure_categoryID);
												$formStatus		=	($chartStatus) ? 'completed' : 'not completed';
												imw_query("Update $tblename Set form_status = '".$formStatus."' Where ".$patientconfirmationid1." = '".$_REQUEST['pConfId']."'  ") or die('Error found at line no. '.(__LINE__).': '.imw_error());
											}
										}
										
										
										if($chkFormUsedNumRow > 0 &&  $formStatus== 'completed') {
											$chkMrkImage 	= "<img src='images/green_flag.png' width='12' height='14' border='0'>";
										}else if($chkFormUsedNumRow > 0 &&  $formStatus== 'not completed') {
											$chkMrkImage 	= "<img src='images/red_flag.png' width='12' height='14' border='0'>";
										}else {
											$chkMrkImage 	= "";
										}
										
										$chk_surgical_checkList=imw_query("SELECT form_status FROM surgical_check_list WHERE 
													confirmation_id = '".$_REQUEST['pConfId']."'");							
										$chk_surgical_checkList_num_row=imw_num_rows($chk_surgical_checkList);
										if($chk_surgical_checkList_num_row>0){
											//do nothing
										}else{
											$blankInsertQry_CheckList="insert into surgical_check_list set 
											confirmation_id = '".$_REQUEST['pConfId']."'";
											$resCheckListQry=imw_query($blankInsertQry_CheckList);
										}
										//==========End Check List================//
										if($iolink_patient_in_waiting_id) {
											
											//START ENTERING VALUE IN CONSENT FORMS FROM IOLINK
											if($tblename=="consent_multiple_form") {
												//START SAVE IOLINK SIGNATURE IMAGES IN html2pdfnew FOLDER
												$iolinkSigDataQry 		= "SELECT * from iolink_consent_form_signature 
																			WHERE patient_in_waiting_id = '".$iolink_patient_in_waiting_id."'
																			AND consent_template_id = '".$consentMultipleId."'";
												$iolinkSigDataRes 		= imw_query($iolinkSigDataQry) or die(imw_error());
												$iolinkSigDataNumRow 	= imw_num_rows($iolinkSigDataRes); 
												if($iolinkSigDataNumRow>0) {
													while($iolinkSigDataRow= imw_fetch_array($iolinkSigDataRes)) {
														$iolinkSignatureImagePathTemp 		= 	trim(str_ireplace('\\','/',$iolinkSigDataRow['signature_image_path']));
														$iolinkSignatureFolder = "html2pdfnew";
														if(stristr($iolinkSignatureImagePathTemp,'SigPlus_images')) {
															$iolinkSignatureFolder = "SigPlus_images";
														}
														$iolinkSignatureImagePathExplode	= 	explode($iolinkSignatureFolder."/",$iolinkSignatureImagePathTemp);	
														$iolinkSignatureImagePath 			= 	$iolinkSignatureImagePathExplode[1];
														
														$iolinkSignatureImagePathReplace	= 	$rootServerPath.'/'.$iolinkDirectoryName.'/'.$iolinkSignatureFolder.'/'.$iolinkSignatureImagePath;
														$surgerycenterConsentImageFullPath 	= 	$rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.$iolinkSignatureFolder.'/'.$iolinkSignatureImagePath;
														if(!file_exists($surgerycenterConsentImageFullPath)){
															if(file_exists($iolinkSignatureImagePathReplace)){
																@copy($iolinkSignatureImagePathReplace,$surgerycenterConsentImageFullPath);
															}
														}
													}
												}
												//END SAVE IOLINK SIGNATURE IMAGES IN html2pdfnew FOLDER
												
												$iolinkIdConsentFilledFormQry 		= "select * from `iolink_consent_filled_form` where fldPatientWaitingId='".$iolink_patient_in_waiting_id."' AND consent_template_id='".$consentMultipleId."'";
												$iolinkIdConsentFilledFormRes 		= imw_query($iolinkIdConsentFilledFormQry) or die(imw_error()); 
												$iolinkIdConsentFilledFormNumRow 	= imw_num_rows($iolinkIdConsentFilledFormRes);
												if($iolinkIdConsentFilledFormNumRow>0) {
													$iolinkIdConsentFilledFormRow 	= imw_fetch_array($iolinkIdConsentFilledFormRes);
													$iolink_surgery_consent_name 	= stripslashes($iolinkIdConsentFilledFormRow['surgery_consent_name']);
													$iolink_surgery_consent_alias 	= stripslashes($iolinkIdConsentFilledFormRow['surgery_consent_alias']);
													$iolink_surgery_consent_data 	= stripslashes($iolinkIdConsentFilledFormRow['surgery_consent_data']);
													$iolink_form_status 			= $iolinkIdConsentFilledFormRow['form_status'];
													$iolink_consent_template_id 	= $iolinkIdConsentFilledFormRow['consent_template_id'];
													$iolink_signSurgeon1Activate 	= $iolinkIdConsentFilledFormRow['signSurgeon1Activate'];
													$iolink_signSurgeon1Id 			= $iolinkIdConsentFilledFormRow['signSurgeon1Id'];
													$iolink_signSurgeon1FirstName 	= $iolinkIdConsentFilledFormRow['signSurgeon1FirstName'];
													$iolink_signSurgeon1MiddleName 	= $iolinkIdConsentFilledFormRow['signSurgeon1MiddleName'];
													$iolink_signSurgeon1LastName 	= $iolinkIdConsentFilledFormRow['signSurgeon1LastName'];
													$iolink_signSurgeon1Status 		= $iolinkIdConsentFilledFormRow['signSurgeon1Status'];
													$iolink_signSurgeon1DateTime 	= $iolinkIdConsentFilledFormRow['signSurgeon1DateTime'];
													
													$iolink_signNurseActivate 		= $iolinkIdConsentFilledFormRow['signNurseActivate'];
													$iolink_signNurseId 			= $iolinkIdConsentFilledFormRow['signNurseId'];
													$iolink_signNurseFirstName 	= $iolinkIdConsentFilledFormRow['signNurseFirstName'];
													$iolink_signNurseMiddleName 	= $iolinkIdConsentFilledFormRow['signNurseMiddleName'];
													$iolink_signNurseLastName 	= $iolinkIdConsentFilledFormRow['signNurseLastName'];
													$iolink_signNurseStatus 		= $iolinkIdConsentFilledFormRow['signNurseStatus'];
													$iolink_signNurseDateTime 	= $iolinkIdConsentFilledFormRow['signNurseDateTime'];
													
													$iolink_signAnesthesia1Activate = $iolinkIdConsentFilledFormRow['signAnesthesia1Activate'];
													$iolink_signAnesthesia1Id 			= $iolinkIdConsentFilledFormRow['signAnesthesia1Id'];
													$iolink_signAnesthesia1FirstName 	= $iolinkIdConsentFilledFormRow['signAnesthesia1FirstName'];
													$iolink_signAnesthesia1MiddleName 	= $iolinkIdConsentFilledFormRow['signAnesthesia1MiddleName'];
													$iolink_signAnesthesia1LastName 	= $iolinkIdConsentFilledFormRow['signAnesthesia1LastName'];
													$iolink_signAnesthesia1Status 		= $iolinkIdConsentFilledFormRow['signAnesthesia1Status'];
													$iolink_signAnesthesia1DateTime 	= $iolinkIdConsentFilledFormRow['signAnesthesia1DateTime'];
													
													$iolink_signWitness1Activate 	= $iolinkIdConsentFilledFormRow['signWitness1Activate'];
													$iolink_signWitness1Id 			= $iolinkIdConsentFilledFormRow['signWitness1Id'];
													$iolink_signWitness1FirstName 	= $iolinkIdConsentFilledFormRow['signWitness1FirstName'];
													$iolink_signWitness1MiddleName 	= $iolinkIdConsentFilledFormRow['signWitness1MiddleName'];
													$iolink_signWitness1LastName 	= $iolinkIdConsentFilledFormRow['signWitness1LastName'];
													$iolink_signWitness1Status 		= $iolinkIdConsentFilledFormRow['signWitness1Status'];
													$iolink_signWitness1DateTime 	= $iolinkIdConsentFilledFormRow['signWitness1DateTime'];
												
													$updateConsentMultipleFormQry 	= "UPDATE  consent_multiple_form SET
																						surgery_consent_name	= '".addslashes($iolink_surgery_consent_name)."',
																						surgery_consent_alias	= '".addslashes($iolink_surgery_consent_alias)."',
																						surgery_consent_data 	= '".addslashes($iolink_surgery_consent_data)."',
																						form_status 			= '".$iolink_form_status."',
																						left_navi_status		= 'false',
																						signSurgeon1Activate	= '".$iolink_signSurgeon1Activate."',
																						signSurgeon1Id			= '".$iolink_signSurgeon1Id."',
																						signSurgeon1FirstName	= '".$iolink_signSurgeon1FirstName."',
																						signSurgeon1MiddleName	= '".$iolink_signSurgeon1MiddleName."',
																						signSurgeon1LastName	= '".$iolink_signSurgeon1LastName."',
																						signSurgeon1Status		= '".$iolink_signSurgeon1Status."',
																						signSurgeon1DateTime	= '".$iolink_signSurgeon1DateTime."',
																						
																						signNurseActivate		= '".$iolink_signNurseActivate."',
																						signNurseId			= '".$iolink_signNurseId."',
																						signNurseFirstName	= '".$iolink_signNurseFirstName."',
																						signNurseMiddleName	= '".$iolink_signNurseMiddleName."',
																						signNurseLastName	= '".$iolink_signNurseLastName."',
																						signNurseStatus		= '".$iolink_signNurseStatus."',
																						signNurseDateTime	= '".$iolink_signNurseDateTime."',
																						
																						signAnesthesia1Activate	= '".$iolink_signAnesthesia1Activate."',
																						signAnesthesia1Id			= '".$iolink_signAnesthesia1Id."',
																						signAnesthesia1FirstName	= '".$iolink_signAnesthesia1FirstName."',
																						signAnesthesia1MiddleName	= '".$iolink_signAnesthesia1MiddleName."',
																						signAnesthesia1LastName	= '".$iolink_signAnesthesia1LastName."',
																						signAnesthesia1Status		= '".$iolink_signAnesthesia1Status."',
																						signAnesthesia1DateTime	= '".$iolink_signAnesthesia1DateTime."',
																						
																						signWitness1Activate	= '".$iolink_signWitness1Activate."',
																						signWitness1Id			= '".$iolink_signWitness1Id."',
																						signWitness1FirstName	= '".$iolink_signWitness1FirstName."',
																						signWitness1MiddleName	= '".$iolink_signWitness1MiddleName."',
																						signWitness1LastName	= '".$iolink_signWitness1LastName."',
																						signWitness1Status		= '".$iolink_signWitness1Status."',
																						signWitness1DateTime	= '".$iolink_signWitness1DateTime."'
																																
																						WHERE confirmation_id 	= '".$_REQUEST['pConfId']."'
																						AND consent_template_id = '".$consentMultipleId."'
																						AND form_status 		!= 'completed'
																					  ";
													$updateConsentMultipleFormRes 	= imw_query($updateConsentMultipleFormQry) or die(imw_error());
													
													//START SET COLOR AND FLAG FOR CONSENT FORMS
													$color = '#999999';
													if($iolink_form_status== 'completed' ) {
														//$chkMrkImage = "<img src='images/green_flag.png' width='12' height='14' border='0'>";
														$chkMrkImage_consent = "<img src='images/green_flag.png' width='12' height='14' border='0'>";
														
													}else if($iolink_form_status== 'not completed') {
														//$chkMrkImage = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
														$chkMrkImage_consent = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
													}else {
														//$chkMrkImage = "";
														$chkMrkImage_consent = "";
													}
													//END SET COLOR AND FLAG FOR CONSENT FORMS
													
												}
											}
											//END ENTERING VALUE IN CONSENT FORMS FROM IOLINK	
											
											//START ENTERING VALUE IN PREOP-HEALTHQUESTIONNIRE CHART NOTE FROM IOLINK	
											if($tblename=="preophealthquestionnaire" && $formStatus <> 'completed' ) {
												
												$getPreOpQuesDetails = $objManageData->getExtractRecord('iolink_preophealthquestionnaire', 'patient_in_waiting_id', $iolink_patient_in_waiting_id);	
												
												if(is_array($getPreOpQuesDetails)){
													
													extract($getPreOpQuesDetails);
													$patient_id=$_REQUEST['patient_id']; //SET PATIENT-ID ALEADY IN WORKING
													
													$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
													//START CODE TO COPY SIGNATURE IMAGES OF PATIENT & WITNETSS FOR PREOP- HEALTH QUEST
													$patient_sign_image_path = trim($patient_sign_image_path);
													if($patient_sign_image_path) {
														$iolinkPatientSignFullPath = $rootServerPath.'/'.$iolinkDirectoryName.'/'.$patient_sign_image_path;
														if(!file_exists($patient_sign_image_path)){
															$iolinkPatientSignFullPath = $rootServerPath.'/'.$iolinkDirectoryName.'/'.$patient_sign_image_path;
															if(file_exists($iolinkPatientSignFullPath)){
																@copy($iolinkPatientSignFullPath,$patient_sign_image_path);
															}
														}
													}
													if($witness_sign_image_path) {
														$iolinkWitnessSignFullPath = $rootServerPath.'/'.$iolinkDirectoryName.'/'.$witness_sign_image_path;
														if(!file_exists($witness_sign_image_path)){
															$iolinkWitnessSignFullPath = $rootServerPath.'/'.$iolinkDirectoryName.'/'.$witness_sign_image_path;
															if(file_exists($iolinkWitnessSignFullPath)){
																@copy($iolinkWitnessSignFullPath,$witness_sign_image_path);
															}
														}
													}
													//END CODE TO COPY SIGNATURE IMAGES OF PATIENT & WITNETSS FOR PREOP- HEALTH QUEST
													
													unset($arrayRecord);
													$arrayRecord['heartTrouble'] 					= addslashes($heartTrouble);
													
													$arrayRecord['patient_sign_image_path'] 		= addslashes($patient_sign_image_path);
													$arrayRecord['patientSign'] 					= addslashes($patientSign);
													
													$arrayRecord['witness_sign_image_path'] 		= addslashes($witness_sign_image_path);
													$arrayRecord['witnessSign'] 					= addslashes($witnessSign);
			
													$arrayRecord['stroke'] 							= addslashes($stroke);
													$arrayRecord['heartAttack'] 					= addslashes($heartAttack);
													$arrayRecord['anticoagulationTherapy'] 			= addslashes($anticoagulationTherapy);
													$arrayRecord['asthma'] 							= addslashes($asthma);
													$arrayRecord['sleepApnea'] 						= addslashes($sleepApnea);
													$arrayRecord['breathingProbs'] 					= addslashes($breathingProbs);
													$arrayRecord['TB'] 								= addslashes($TB);
													$arrayRecord['diabetes'] 						= addslashes($diabetes);
													$arrayRecord['insulinDependence'] 				= addslashes($insulinDependence);
													$arrayRecord['epilepsy'] 						= addslashes($epilepsy);
													$arrayRecord['convulsions'] 					= addslashes($convulsions);
													$arrayRecord['parkinsons'] 						= addslashes($parkinsons);
													$arrayRecord['vertigo'] 						= addslashes($vertigo);
													$arrayRecord['restlessLegSyndrome'] 			= addslashes($restlessLegSyndrome);
													$arrayRecord['hepatitis'] 						= addslashes($hepatitis);	
													$arrayRecord['hepatitisA'] 						= addslashes($hepatitisA);	
													$arrayRecord['hepatitisB'] 						= addslashes($hepatitisB);
													$arrayRecord['hepatitisC'] 						= addslashes($hepatitisC);	
													$arrayRecord['kidneyDisease'] 					= addslashes($kidneyDisease);
													$arrayRecord['shunt'] 							= addslashes($shunt);
													$arrayRecord['fistula'] 						= addslashes($fistula);
													$arrayRecord['hivAutoimmuneDiseases'] 			= addslashes($hivAutoimmuneDiseases);
													$arrayRecord['hivTextArea'] 					= addslashes($hivTextArea);
													$arrayRecord['cancerHistory'] 					= addslashes($cancerHistory);	
													$arrayRecord['brest_cancer'] 					= addslashes($brest_cancer);	
													$arrayRecord['brestCancerLeft'] 				= addslashes($brestCancerLeft);	
													$arrayRecord['cancerHistoryDesc'] 				= addslashes($cancerHistoryDesc);	
													$arrayRecord['organTransplant'] 				= addslashes($organTransplant);
													$arrayRecord['organTransplantDesc'] 			= addslashes($organTransplantDesc);	
													$arrayRecord['anesthesiaBadReaction'] 			= addslashes($anesthesiaBadReaction);
													$arrayRecord['tuberculosis'] 					= addslashes($tuberculosis);
													$arrayRecord['HighBP'] 							= addslashes($HighBP);
													$arrayRecord['otherTroubles'] 					= addslashes($otherTroubles);	
													//$arrayRecord['allergies_status'] 				= addslashes($allergies_status);//nkda will now read from patientconfirmation table
													$arrayRecord['allergies_status_reviewed']		= addslashes($allergies_status_reviewed);
													$arrayRecord['walker'] 							= addslashes($walker);
													$arrayRecord['contactLenses'] 					= addslashes($contactLenses);
													$arrayRecord['smoke'] 							= addslashes($smoke);
													$arrayRecord['smokeHowMuch'] 					= addslashes($smokeHowMuch);
													$arrayRecord['smokeAdvise'] 					= addslashes($smokeAdvise);
													$arrayRecord['alchohol'] 						= addslashes($alchohol);
													$arrayRecord['alchoholHowMuch'] 				= addslashes($alchoholHowMuch);
													$arrayRecord['alchoholAdvise'] 					= addslashes($alchoholAdvise);
													$arrayRecord['autoInternalDefibrillator']		= addslashes($autoInternalDefibrillator);
													$arrayRecord['metalProsthetics'] 				= addslashes($metalProsthetics);
													$arrayRecord['notes'] 							= addslashes($notes);										
												
													$arrayRecord['nursefield'] 						= addslashes($nursefield);
													$arrayRecord['dateQuestionnaire'] 				= addslashes($dateQuestionnaire);
													$arrayRecord['timeQuestionnaire'] 				= addslashes($timeQuestionnaire);
													$arrayRecord['emergencyContactPerson'] 			= addslashes($emergencyContactPerson);
													$arrayRecord['witnessname']						= addslashes($witnessname);
			
													$arrayRecord['emergencyContactPhone'] 			= addslashes($emergencyContactPhone);
													$arrayRecord['progressNotes'] 					= addslashes($progressNotes);
													$arrayRecord['nurseId'] 						= $nurseId;
													$arrayRecord['form_status'] 					= addslashes($form_status);

													$arrayRecord['heartTroubleDesc']				= addslashes($heartTroubleDesc);
													$arrayRecord['strokeDesc']						= addslashes($strokeDesc);
													$arrayRecord['HighBPDesc']						= addslashes($HighBPDesc);
													$arrayRecord['anticoagulationTherapyDesc']		= addslashes($anticoagulationTherapyDesc);
													$arrayRecord['asthmaDesc']						= addslashes($asthmaDesc);
													$arrayRecord['tuberculosisDesc']				= addslashes($tuberculosisDesc);
													$arrayRecord['diabetesDesc']					= addslashes($diabetesDesc);
													$arrayRecord['epilepsyDesc']					= addslashes($epilepsyDesc);
													$arrayRecord['restlessLegSyndromeDesc']			= addslashes($restlessLegSyndromeDesc);
													$arrayRecord['hepatitisDesc']					= addslashes($hepatitisDesc);
													$arrayRecord['kidneyDiseaseDesc']				= addslashes($kidneyDiseaseDesc);
													$arrayRecord['anesthesiaBadReactionDesc']		= addslashes($anesthesiaBadReactionDesc);
													$arrayRecord['walkerDesc']						= addslashes($walkerDesc);
													$arrayRecord['contactLensesDesc']				= addslashes($contactLensesDesc);
													$arrayRecord['autoInternalDefibrillatorDesc']	= addslashes($autoInternalDefibrillatorDesc);
													
													$arrayRecord['signWitness1Id']					= addslashes($signWitness1Id);
													$arrayRecord['signWitness1FirstName']			= addslashes($signWitness1FirstName);
													$arrayRecord['signWitness1MiddleName']			= addslashes($signWitness1MiddleName);
													$arrayRecord['signWitness1LastName']			= addslashes($signWitness1LastName);
													$arrayRecord['signWitness1Status']				= addslashes($signWitness1Status);
													$arrayRecord['signWitness1DateTime']			= addslashes($signWitness1DateTime);
													
													$objManageData->updateRecords($arrayRecord, 'preophealthquestionnaire', 'confirmation_id', $_REQUEST['pConfId']);
													
													unset($leftNaviPreHealthArr);
													$leftNaviPreHealthArr['pre_op_health_ques_form'] = 'false';
													$objManageData->updateRecords($leftNaviPreHealthArr, 'left_navigation_forms', 'confirmationId', $_REQUEST['pConfId']);
													
													//START SET COLOR AND FLAG FOR PREOP-HEALT QUEST
													$color = '#999999';
													if($form_status== 'completed' ) {
														$chkMrkImage = "<img src='images/green_flag.png' style='width:12px; height:14px; border:none;'>";
													}else if($form_status== 'not completed') {
														$chkMrkImage = "<img src='images/red_flag.png' style='width:12px; height:14px; border:none;'>";
													}else {
														$chkMrkImage = "";
													}
													
													//START SET COLOR AND FLAG FOR PREOP-HEALT QUEST			
													
												}
												
												//START INSERTING QUESTIONS OF ADMIN FOR HEALTHQUESTIONNAIRE CHARTNOTE
												$iolinkHealthquestionAdminDetails = $objManageData->getArrayRecords('iolink_healthquestionadmin', 'patient_in_waiting_id', $iolink_patient_in_waiting_id);
												unset($iolinkAdminQuestionArr);
												if(count($iolinkHealthquestionAdminDetails)>0){
													foreach($iolinkHealthquestionAdminDetails as $iolinkHealthquestionAdmin){
														$iolinkAdminQuestion = $iolinkHealthquestionAdmin->adminQuestion;
														$iolinkAdminQuestionStatus = $iolinkHealthquestionAdmin->adminQuestionStatus;
														$iolinkAdminQuestionDesc   = $iolinkHealthquestionAdmin->adminQuestionDesc;

														$iolinkAdminQuestionArr['adminQuestion'] 			= addslashes($iolinkAdminQuestion);
														$iolinkAdminQuestionArr['adminQuestionStatus'] 		= addslashes($iolinkAdminQuestionStatus);
														$iolinkAdminQuestionArr['adminQuestionDesc'] 		= addslashes($iolinkAdminQuestionDesc);
														$iolinkAdminQuestionArr['confirmation_id'] 			= $_REQUEST['pConfId'];
														$iolinkAdminQuestionArr['patient_id'] 				= $iolinkHealthquestionAdmin->patient_id;

														$iolinkAdminQuestionConditionArr['adminQuestion'] 	= addslashes($iolinkAdminQuestion);
														$iolinkAdminQuestionConditionArr['confirmation_id'] = $_REQUEST['pConfId'];
														$iolinkAdminQuestionConditionArr['patient_id'] 		= $iolinkHealthquestionAdmin->patient_id;

														$iolinkAdminQuestionExist = $objManageData->getMultiChkArrayRecords('healthquestionadmin', $iolinkAdminQuestionConditionArr);
														if($iolinkAdminQuestionExist) {
															//$objManageData->updateRecords($iolinkAdminQuestionArr, 'patient_prescription_medication_healthquest_tbl', 'prescription_medication_id', $medicationId[$Key]);
														}else {	
															$objManageData->addRecords($iolinkAdminQuestionArr, 'healthquestionadmin');
														}
													}
												}	
												//END INSERTING QUESTIONS OF ADMIN FOR HEALTHQUESTIONNAIRE CHARTNOTE
												
											}
											//END ENTERING VALUE IN PREOP-HEALTHQUESTIONNIRE CHART NOTE FROM IOLINK	
											
											//START ENTERING VALUE IN H&P CHART NOTE FROM IOLINK	
											if($tblename=="history_physicial_clearance" && $formStatus <> 'completed' ) {
												
												$getHPDetails = $objManageData->getExtractRecord('iolink_history_physical', 'pt_waiting_id', $iolink_patient_in_waiting_id);	
												
												if(is_array($getHPDetails)){
													
													extract($getHPDetails);
													$patient_id=$_REQUEST['patient_id']; //SET PATIENT-ID ALEADY IN WORKING
													
													$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
													
													unset($arrayRecord);
													$arrayRecord['cadMI'] = addslashes($cadMI);
													$arrayRecord['cadMIDesc'] = addslashes($cadMIDesc);
													$arrayRecord['cvaTIA'] = addslashes($cvaTIA);
													$arrayRecord['cvaTIADesc'] = addslashes($cvaTIADesc);
													$arrayRecord['htnCP'] = addslashes($htnCP);
													$arrayRecord['htnCPDesc'] = addslashes($htnCPDesc);
													$arrayRecord['anticoagulationTherapy'] = addslashes($anticoagulationTherapy);
													$arrayRecord['anticoagulationTherapyDesc'] = addslashes($anticoagulationTherapyDesc);
													$arrayRecord['respiratoryAsthma'] = addslashes($respiratoryAsthma);
													$arrayRecord['respiratoryAsthmaDesc'] = addslashes($respiratoryAsthmaDesc);
													$arrayRecord['arthritis'] = addslashes($arthritis);
													$arrayRecord['arthritisDesc'] = addslashes($arthritisDesc);
													$arrayRecord['diabetes'] = addslashes($diabetes);
													$arrayRecord['diabetesDesc'] = addslashes($diabetesDesc);
													
													$arrayRecord['recreationalDrug'] = addslashes($recreationalDrug);
													$arrayRecord['recreationalDrugDesc'] = addslashes($recreationalDrugDesc);
													$arrayRecord['giGerd'] = addslashes($giGerd);
													$arrayRecord['giGerdDesc'] = addslashes($giGerdDesc);
													$arrayRecord['ocular'] = addslashes($ocular);
													$arrayRecord['ocularDesc'] = addslashes($ocularDesc);
													$arrayRecord['kidneyDisease'] = addslashes($kidneyDisease);
													$arrayRecord['kidneyDiseaseDesc'] = addslashes($kidneyDiseaseDesc);
													$arrayRecord['hivAutoimmune'] = addslashes($hivAutoimmune);
													$arrayRecord['hivAutoimmuneDesc'] = addslashes($hivAutoimmuneDesc);
													$arrayRecord['historyCancer'] = addslashes($historyCancer);
													$arrayRecord['historyCancerDesc'] = addslashes($historyCancerDesc);
													
													
													$arrayRecord['organTransplant'] = addslashes($organTransplant);
													$arrayRecord['organTransplantDesc'] = addslashes($organTransplantDesc);	
													$arrayRecord['badReaction'] = addslashes($badReaction);	
													$arrayRecord['badReactionDesc'] = addslashes($badReactionDesc);
													$arrayRecord['otherHistoryPhysical'] = addslashes($otherHistoryPhysical);	
													$arrayRecord['heartExam'] = addslashes($heartExam);
													$arrayRecord['heartExamDesc'] = addslashes($heartExamDesc);
													$arrayRecord['lungExam'] = addslashes($lungExam);
													$arrayRecord['lungExamDesc'] = addslashes($lungExamDesc);
													$arrayRecord['discussedAdvancedDirective'] = addslashes($discussedAdvancedDirective);
													$arrayRecord['version_num'] = $version_num;	
													$arrayRecord['version_date_time'] = $version_date_time;	
													$arrayRecord['save_date_time'] = $save_date_time;	
													$arrayRecord['save_operator_id'] = $save_operator_id;	
													//$arrayRecord['create_date_time'] = $create_date_time;
													//$arrayRecord['create_operator_id'] = $create_operator_id;	
													$arrayRecord['highCholesterol'] = addslashes($highCholesterol);
													$arrayRecord['highCholesterolDesc'] = addslashes($highCholesterolDesc);
													$arrayRecord['thyroid'] = addslashes($thyroid);
													$arrayRecord['thyroidDesc'] = addslashes($thyroidDesc);	
													$arrayRecord['ulcer'] = addslashes($ulcer);
													$arrayRecord['ulcerDesc'] = addslashes($ulcerDesc);
													$arrayRecord['form_status'] = addslashes($form_status);
														
													$objManageData->updateRecords($arrayRecord, 'history_physicial_clearance', 'confirmation_id', $_REQUEST['pConfId']);
													
													unset($leftNaviHPArr);
													$leftNaviHPArr['history_physical_form'] = 'false';
													$objManageData->updateRecords($leftNaviHPArr, 'left_navigation_forms', 'confirmationId', $_REQUEST['pConfId']);
													
													//START SET COLOR AND FLAG FOR H&P Chart
													$color = '#999999';
													if($form_status== 'completed' ) {
														$chkMrkImage = "<img src='images/green_flag.png' style='width:12px; height:14px; border:none;'>";
													}else if($form_status== 'not completed') {
														$chkMrkImage = "<img src='images/red_flag.png' style='width:12px; height:14px; border:none;'>";
													}else {
														$chkMrkImage = "";
													}
													
													//START SET COLOR AND FLAG FOR H&P Chart			
													
												}
												
												//START INSERTING CUSTOM QUESTIONS FOR H&P Chart
												$iolinkHPQuestionDetails = $objManageData->getArrayRecords('iolink_history_physical_ques', 'pt_waiting_id', $iolink_patient_in_waiting_id);
												unset($iolinkHPQuestionArr);
												if(count($iolinkHPQuestionDetails)>0){
													foreach($iolinkHPQuestionDetails as $iolinkHPQuestions){
														$iolinkHPQuestion = $iolinkHPQuestions->ques;
														$iolinkHPQuestionStatus = $iolinkHPQuestions->ques_status;
														$iolinkHPQuestionDesc   = $iolinkHPQuestions->ques_desc;
														$iolinkHPQuestionSrc   = $iolinkHPQuestions->source;

														$iolinkHPQuestionArr['ques'] = addslashes($iolinkHPQuestion);
														$iolinkHPQuestionArr['ques_status'] = addslashes($iolinkHPQuestionStatus);
														$iolinkHPQuestionArr['ques_desc'] = addslashes($iolinkHPQuestionDesc);
														$iolinkHPQuestionArr['source'] = addslashes($iolinkHPQuestionSrc);
														$iolinkHPQuestionArr['confirmation_id'] = $_REQUEST['pConfId'];
														$iolinkHPQuestionArr['patient_id'] = $iolinkHPQuestions->patient_id;

														$iolinkHPQuestionConditionArr['ques'] 	= addslashes($iolinkHPQuestion);
														$iolinkHPQuestionConditionArr['confirmation_id'] = $_REQUEST['pConfId'];
														$iolinkHPQuestionConditionArr['patient_id'] 		= $iolinkHPQuestions->patient_id;

														$iolinkHPQuestionExist = $objManageData->getMultiChkArrayRecords('history_physical_ques', $iolinkHPQuestionConditionArr);
														if($iolinkHPQuestionExist) {
															// Do Nothing
														}else {	
															$objManageData->addRecords($iolinkHPQuestionArr, 'history_physical_ques');
														}
													}
												}	
												//END INSERTING CUSTOM QUESTIONS FOR H&P Chart
												
											}
											//END ENTERING VALUE IN H&P CHART NOTE FROM IOLINK	
											
										}
										
										//END CODE TO CHECK IF FORM IS ALREADY IN USE FOR TODAY'S DATE						
										if($lnkname!='consent_multiple_form.php' && $displayStatus == true)
										{
								   ?>													
											<input type="hidden" name="keyId" id="keyId" value="innerContent<?php echo $kId;?>" />
											
											<li id="sub<?php echo $subId; ?>" 
												onClick="javascript:<?php echo (($linkStatus == true) ?   "left_link_click('$lnkname','$key','$innKeys','$tempPrecolor','$patient_id','$pConfId','$ascId','$consentMultipleId'); " : "accessAlert();"); ?> <?php echo $pop;?>" 	
											>
												<a 	title="<?php echo stripslashes($inner);?>"  href="javascript:void(0);" style="background-color:<?php echo $color;?>;" onclick="(this.style.backgroundColor !== '#999999' ?  this.style.backgroundColor = '#999999' : '' )"  >
													<span class="span_over" style="width:175px;">
														<b class="fa fa-caret-right"></b>
                                                        <b class="fa fa-caret-right"></b>
														<?php echo stripslashes($inner);  ?>
                                                    </span>
													<label id="chkMrkImageStatusId<?php echo $subId; ?>" ><?php echo $chkMrkImage; ?></label>
												</a>
												
											</li>
									<?php 			
										
										}
								
								}                   
                            
							
								$ct=$ct+1;
                        	} // End Sub Menu Level 2
							
							
							/*
							*
							*	Consent Categories Start Here -  Level 2 & Level 3
							*
							*/
							
							if($key == 0)
							{
								$i_cat			=	0;
								$innKeys_cat	=	1;
								$image_color	=	1;
								//select category
								$consentFormTemplateCatSelectQry = "SELECT * FROM `consent_category` ORDER BY category_name";
								$consentFormTemplateCatSelectRes = imw_query($consentFormTemplateCatSelectQry) or die(imw_error()); 
								$consentFormTemplateCatSelectNumRow = imw_num_rows($consentFormTemplateCatSelectRes);
								if($consentFormTemplateCatSelectNumRow > 0) 
								{
									while($consentFormTemplateCatSelectRow = imw_fetch_array($consentFormTemplateCatSelectRes)) 
									{
										if($image_color%2 == 0)	$color_set="#00c0ef";
										else	$color_set="#00a7d0";
										
										$name_cat	=	$consentFormTemplateCatSelectRow['category_name'];
										$category_id=	$consentFormTemplateCatSelectRow['category_id'];
										$category_status	=	$consentFormTemplateCatSelectRow['category_status'];
										
										if($category_status == "true")
										{
											$patientconfirmation_idchkcateg	=	'confirmation_id';
											$andCategoryId = " and consent_category_id='$category_id' and (form_status='completed' OR form_status='not completed')";
											$cat_chkQry="SELECT form_status FROM consent_multiple_form  WHERE 
															$patientconfirmation_idchkcateg = '".$_REQUEST['pConfId']."' 
															$andCategoryId";
											$cat_chkRes	= imw_query($cat_chkQry) or die($cat_chkQry.imw_error());	
											$cat_chkNumRow = imw_num_rows($cat_chkRes);	
											if($cat_chkNumRow==0){ 
												// Do Nothing
											}
											else
											{
			?>
												<li class="dropdown02 f_sidebar" data-target="#consentFormsCatConsentID<?php echo $category_id;?>" data-toggle="collapse"  >
                                        			<a href="javascript:void(0);" style=" background-color:<?php echo $color_set;?>;">
                                                    <span class="span_over">
                                                    	<b class="fa fa-chevron-right"></b>
														<?php echo ucfirst($name_cat);  ?>
                                                   	</span>
                                                	<span class="glyphicon glyphicon-print" style="float:right;"
                                                          onclick="printCategoryConsentFn('<?php echo $patient_confID; ?>','<?php echo $category_id?>','<?php echo $get_http_path; ?>');" >
                                                    </span>
                                          			</a>
                                      			</li>
           	<?php
											}
											
								 		}// End IF Category Status is TRUE
																		
										
										
										//START
										$consentFormTemplateCatFormSelectChkQry = "select consent_id from `consent_forms_template` where consent_category_id='".$category_id."' AND consent_delete_status!='true'  order by (consent_name+0 != 'zzz' IS NOT TRUE) ,cast(consent_name as unsigned) ASC, consent_name";
										$consentFormTemplateCatFormSelectChkRes = imw_query($consentFormTemplateCatFormSelectChkQry) or die(imw_error()); 
										$consentFormTemplateCatFormSelectChkNumRow = imw_num_rows($consentFormTemplateCatFormSelectChkRes);
								
								
										$patientconfirmation_idchkcateg	=	'confirmation_id';
										$andCategoryId = " and consent_category_id='$category_id' and (form_status='completed' OR form_status='not completed')";
										$cat_chkQry	=	"SELECT form_status FROM consent_multiple_form  WHERE 
															$patientconfirmation_idchkcateg = '".$_REQUEST['pConfId']."' 
															$andCategoryId";
										$cat_chkRes	= imw_query($cat_chkQry) or die($cat_chkQry.imw_error());	
										$cat_chkNumRow = imw_num_rows($cat_chkRes);	
								
								
										if($consentFormTemplateCatFormSelectChkNumRow > 0 || $cat_chkNumRow > 0)
										{
											if($category_status != "true")
											{
												
						?>					
												<li  data-target="#consentFormsCatConsentID<?php echo $category_id;?>" data-toggle="collapse"  >
                                        			<a href="javascript:void(0);" style=" background-color:<?php echo $color_set;?>;">
                                                    <span class="span_over">
														<b class="fa fa-chevron-right"></b>
														<?php echo ucfirst($name_cat);  ?>
                                                   	</span>
                                                	<span 	class="glyphicon glyphicon-print" style="float:right;"
                                                            onclick="printCategoryConsentFn('<?php echo $pConfId; ?>','<?php echo $category_id?>','<?php echo $get_http_path; ?>');" >
                                                    </span>
                                          			</a>
                                      			</li>
						<?PHP
												$image_color++;
												
											}
											
										}	// End IF --------------	
									
										
										
										/*
										*
										* Consent Forms Level 3 Starts Here
										*
										*/
										
										$consentFormTemplateCatFormSelectQry 	= "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` where consent_category_id='".$category_id."' order by (consent_name+0 != 'zzzzzz' IS NOT TRUE) ,cast(consent_name as unsigned) ASC, consent_name";
										$consentFormTemplateCatFormSelectRes 	= imw_query($consentFormTemplateCatFormSelectQry) or die(imw_error()); 
										$consentFormTemplateCatFormSelectNumRow = imw_num_rows($consentFormTemplateCatFormSelectRes);
										
										$cssConsent = '';
										
										if($consentFormTemplateCatFormSelectNumRow >= 3) 
										{
											$cssConsent = 'height:50px;overflow-x:hidden; overflow-y:auto; width:'.$width.';';
										}												
										
										
										if($consentFormTemplateCatFormSelectNumRow > 0)
										{
											echo ' <ul class="dropdown-menu02 collapse" id="consentFormsCatConsentID'.$category_id.'">' ;
											
											while($consentFormTemplateCatFormSelectRow = imw_fetch_array($consentFormTemplateCatFormSelectRes))
											{
												$consentMultipleId = $consentFormTemplateCatFormSelectRow['consent_id'];
												//PURGE
												$consentchkFrmStatusQry			= "SELECT confirmation_id FROM consent_multiple_form  WHERE 
																				confirmation_id = '".$_REQUEST['pConfId']."' 
																				AND consent_template_id = '$consentMultipleId' and consent_purge_status='true' ";
												$consentchkFrmStatusRes			= imw_query($consentchkFrmStatusQry) or die($consentchkFrmStatusQry.imw_error());	
												$consentchkFrmStatusNumRow 		= imw_num_rows($consentchkFrmStatusRes);	
												//$consentchkRow = imw_fetch_array($consentchkFrmStatusRes);											
												
												$strikeConsentPurge = '';
												if($consentchkFrmStatusNumRow > 0 )
												{
													//START CODE TO ADD SUBSTITUE OF PURGED CONSENT FORM IF NOT EXISTS
													$consentchkPurgeSubsituteQry			= "SELECT confirmation_id FROM consent_multiple_form  WHERE 
																								confirmation_id = '".$_REQUEST['pConfId']."' 
																								AND consent_template_id = '$consentMultipleId' and consent_purge_status='' ";
													$consentchkPurgeSubsituteRes			= imw_query($consentchkPurgeSubsituteQry) or die($consentchkPurgeSubsituteQry.imw_error());	
													if(imw_num_rows($consentchkPurgeSubsituteRes)<=0) {
															$blankInsertPurgeQry = "insert into consent_multiple_form  set 
																				confirmation_id = '".$_REQUEST['pConfId']."',
																				consent_category_id ='".$category_id."',
																				consent_template_id = '".$consentMultipleId."',
																				left_navi_status='true',
																				form_status= ''";
															$blankInsertPurgeRes = imw_query($blankInsertPurgeQry) or die(imw_error());
													}
													//END CODE TO ADD SUBSTITUE OF PURGED CONSENT FORM IF NOT EXISTS
													
													//echo	$consentMultipleId;	
													$consentPurgeStatusQry		= "SELECT * FROM consent_multiple_form  WHERE 
																					confirmation_id = '".$_REQUEST['pConfId']."' 
																					AND consent_template_id = '$consentMultipleId'";
													$consentPurgeStatusRes		= imw_query($consentPurgeStatusQry) or die($consentPurgeStatusQry.imw_error());	
													$consentPurgeStatusNumRow 	= imw_num_rows($consentPurgeStatusRes);	
													
													
													if($consentPurgeStatusNumRow > 0)
													{
														while($consentPurgeStatusRow=imw_fetch_array($consentPurgeStatusRes))
														{
															$consent_id_purge=$consentPurgeStatusRow['surgery_consent_id']; 
		
															$linkStatus 		= true;
															$accessPermission_cat= true;
															if($innKey==0) $color_cat = $subMenuListArr[$key][$innKey];                                             
															if($innKey > 0)
															{ 
																$consentLeftNaviStatus_cat='';
																$lnkname_cat	= "consent_multiple_form.php";
																$formName_cat 	= "surgery_form";
																$patientconfirmationid1_cat ="confirmation_id";
																$tblename_cat	= "consent_multiple_form";
																
																//$consentLeftNaviStatus_cat = getMultipleFormStatus($tblename_cat,$pConfId,$consentMultipleId);
																$consentLeftNaviStatus_cat=$consentPurgeStatusRow['left_navi_status'];
																if($consentLeftNaviStatus_cat!='false')
																{ 
																	if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
																		$color_cat 		= '#D1E0C9';
																		$linkStatus_cat = true;
																	}else{
																		$color_cat 		= '#999999';
																		$linkStatus_cat = false;
																	}
																}
																else
																{ 
																	$color_cat = '#999999';
																	if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
																		$linkStatus_cat = true;	
																	}else{
																		$linkStatus_cat = false;	
																	}
																}	
															}
															$consentFormTemplateCatConsentAlias = $consentFormTemplateCatFormSelectRow['consent_alias'];
															$consentMultipleId=$consentFormTemplateCatFormSelectRow['consent_id'];
															$consentStatus=$consentFormTemplateCatFormSelectRow['consent_delete_status'];
															$patientconfirmation_id	= 'confirmation_id';
															$andConsentId 		= " AND surgery_consent_id = '$consent_id_purge'";
															$consentchkQry		= "SELECT form_status, consent_purge_status FROM consent_multiple_form  WHERE $patientconfirmation_id = '".$_REQUEST['pConfId']."' $andConsentId";
															$consentchkRes		= imw_query($consentchkQry) or die($consentchkQry.imw_error());	
															$consentchkNumRow 	= imw_num_rows($consentchkRes);
															
															if($consentchkNumRow>0) {
																$consentchkRow 	= imw_fetch_array($consentchkRes);
																$status_consent	= $consentchkRow['form_status'];
																$consent_purge_status_chk=$consentchkRow['consent_purge_status'];
															}
															if($consentchkNumRow > 0 &&  $status_consent== 'completed' ) {
																$chkMrkImage_consent = "<img src='images/green_flag.png' style='width:12px; height:14px; border:none;'>";
															}else if($consentchkNumRow > 0 &&  $status_consent== 'not completed') {
																$chkMrkImage_consent = "<img src='images/red_flag.png' style='width:12px; height:14px; border:none;'>";
															}else {
																$chkMrkImage_consent = "";
															}
															
															
															
															if($consentStatus == "true")
															{
															
																if($status_consent=="completed" || $status_consent=="not completed")
																{
																	$kId = $key.'&innerKey='.$innKeys_cat.'&preColor='.urlencode($color_cat);
																	$tempPrecolor 	= urlencode($color_cat);
																	$linkSessionId 	= "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
																	$subId 			= $innKeys_cat;
																	if($consent_purge_status_chk=="true"){	  
																		 $strikeConsentPurge 	= "background-image:url(images/strike_image.jpg); background-repeat:repeat-x; background-position:center;";
																	}else{	
																		 $strikeConsentPurge	= '';
																	}
																	
																	if(($linkStatus_cat == true) && (!$pop))
																	{
																		$href	=	"javascript:left_link_click('".$lnkname."','".$key."','".$innKeys_cat."','".$tempPrecolor."','".$patient_id."','".$pConfId."','".$ascId."','".$consentMultipleId."','".$consent_id_purge."','".$consent_purge_status_chk."')";
																	}
																	else if(!$pop)
																	{
																		$href	=	"javascript:accessAlert();";	
																	}
																		
																	?>
																	
																	<li class="" id="sub<?php echo $subId; ?>">
																		<a 	href="<?=$href?>"
																			style="background-color:<?php echo $color_cat;?>;<?php echo $strikeConsentPurge;?> "
																			title="<?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>"
																			onClick="javascript: <?php echo $pop;?>  "
																			
																		>
																			<span class="span_over">
																				<b class="fa fa-caret-right"></b>
																				<b class="fa fa-caret-right"></b>
																				<?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>
																			</span>
																		</a>
																	</li>
																	
							<?php
									
																	$innKeys_cat++;
																					
																}
																else {
																	//NOT FOUND
																}
															
															} // Consent Form status is True
															
															if($consentStatus!="true") 
															{
																
																$kId			= $key.'&innerKey='.$innKeys_cat.'&preColor='.urlencode($color_cat);
																$tempPrecolor 	= urlencode($color_cat);
																$linkSessionId 	= "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
																$subId 			= $innKeys_cat;
																
																if($consent_purge_status_chk=="true"){	  
																	 $strikeConsentPurge = "background-image:url(images/strike_image.jpg)  !important; background-repeat:repeat-x  !important; background-position:center  !important;";
																}else{	
																	 $strikeConsentPurge='';
																}
																
																if(($linkStatus_cat == true) && (!$pop))
																{
																	$href	=	"javascript:left_link_click('".$lnkname."','".$key."','".$innKeys_cat."','".$tempPrecolor."','".$patient_id."','".$pConfId."','".$ascId."','".$consentMultipleId."','".$consent_id_purge."','".$consent_purge_status_chk."')";								
																}
																else if(!$pop)
																{
																	$href	=	"javascript:accessAlert();";	
																}
																
																
						?> 
																
																	<li class="" id="sub<?php echo $subId; ?>">
																		<a 	href="<?=$href?>"
																			style="background-color:<?php echo $color_cat;?>;<?php echo $strikeConsentPurge;?> <?php if($linkStatus_cat != true) echo "color:#666666;"; ?> "
																			title="<?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>"
																			onClick="javascript: <?php echo $pop;?>  "
																			
																		>
																			<span class="span_over">
																				<b class="fa fa-caret-right"></b>
																				<b class="fa fa-caret-right"></b>
																				<?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>
																			</span>
																			<label id="consent_chkMrkImageStatusId<?php echo $subId; ?>">
																				<?php echo $chkMrkImage_consent; ?>
																			</label>
																		</a>
																	</li>
																
						<?php
																	$innKeys_cat++;
																	
															}	// Consent Form Status is Not True
													
														}
													}
													

												} // IF PART END Purged Records
												
												
												else
												{	
												
													//PURGE
													$linkStatus 			= true;
													$accessPermission_cat 	= true;
													
													if($innKey==0)	$color_cat = $subMenuListArr[$key][$innKey];
													
													if($innKey > 0 )
													{ 
													
														$consentLeftNaviStatus_cat	=	'';
														$lnkname_cat		= "consent_multiple_form.php";
														$formName_cat 		= "surgery_form";
														$patientconfirmationid1_cat = "confirmation_id";
														$tblename_cat		= "consent_multiple_form";
														
														
														$multiStatusArr = getMultipleFormStatus($tblename_cat,$pConfId,$consentMultipleId);
														$consentLeftNaviStatus_cat = $multiStatusArr[0];
														$multiFormStatus = $multiStatusArr[1];
														
													
														if($consentLeftNaviStatus_cat!='false')
														{ 
															if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
																$color_cat 		= '#D1E0C9';
																$linkStatus_cat = true;
															}else{
																$color_cat 		= '#999999';
																$linkStatus_cat = false;
															}
														}
														else
														{ 
															$color_cat 			= '#999999';
															if(in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr)){
																$linkStatus_cat = true;	
															}else{
																$linkStatus_cat = false;	
															}
														}
													
													}
													
													$consentFormTemplateCatConsentAlias = $consentFormTemplateCatFormSelectRow['consent_alias'];
													$consentMultipleId					= $consentFormTemplateCatFormSelectRow['consent_id'];
													$consentStatus						= $consentFormTemplateCatFormSelectRow['consent_delete_status'];
													$patientconfirmation_id	= 'confirmation_id';
													$andConsentId 			= " AND consent_template_id = '$consentMultipleId'";
													$consentchkQry			= "SELECT form_status,surgery_consent_id FROM consent_multiple_form  WHERE $patientconfirmation_id = '".$_REQUEST['pConfId']."' $andConsentId";
													$consentchkRes					= imw_query($consentchkQry) or die($consentchkQry.imw_error());	
													$consentchkNumRow 				= imw_num_rows($consentchkRes);	
													$consentchkRow 					= imw_fetch_array($consentchkRes);
													
													//if($chkFormUsedNumRow > 0)
													$consent_id_purge 	= '';
													$status_consent 	= '';
													if($consentchkNumRow>0)
													{
														$consent_id_purge			= $consentchkRow['surgery_consent_id']; 
														$status_consent				= $consentchkRow['form_status'];
													}
														
													if($multiFormStatus=='completed' || ($chkFormUsedNumRow > 0 &&  $status_consent== 'completed') ) {
														$chkMrkImage_consent 	= "<img src='images/green_flag.png' style='width:12px; height:14px; border:none'>";
													}else if($multiFormStatus=='not completed' || ($chkFormUsedNumRow > 0 &&  $status_consent== 'not completed')) {
														$chkMrkImage_consent 	= "<img src='images/red_flag.png' style='width:12px; height:14px; border:none;'>";
													}
													else {
														$chkMrkImage_consent 	= "";
													}
													
													
													
													if($consentStatus=="true")
													{
												
														if($status_consent=="completed" || $status_consent=="not completed")
														{
															$kId=$key.'&innerKey='.$innKeys_cat.'&preColor='.urlencode($color_cat);
															$tempPrecolor 	= urlencode($color_cat);
															$linkSessionId 	= "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
															$subId 			= $innKeys_cat;
															
															if(($linkStatus_cat == true) && (!$pop))
															{
																$href	=	"javascript:left_link_click('".$lnkname."','".$key."','".$innKeys_cat."','".$tempPrecolor."','".$patient_id."','".$pConfId."','".$ascId."','".$consentMultipleId."','".$consent_id_purge."','')";								
															}
															else if(!$pop)
															{
																$href	=	"javascript:accessAlert();";	
															}
															
					?>
													  		<li class="" id="sub<?php echo $subId; ?>">
                                                                <a 	href="<?=$href?>"
                                                                    style="background-color:<?php echo $color_cat;?>;<?php echo $strikeConsentPurge;?> <?php if($linkStatus_cat != true) echo "color:#666666;"; ?> "
                                                                    title="<?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>"
                                                                    onClick="javascript: <?php echo $pop;?>  "
                                                                    
                                                                >
                                                                    <span class="span_over">
                                                                        <b class="fa fa-caret-right"></b>
                                                                        <b class="fa fa-caret-right"></b>
                                                                        <?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>
                                                                    </span>
                                                                    <label id="consent_chkMrkImageStatusId<?php echo $subId; ?>">
                                                                        <?php echo $chkMrkImage_consent; ?>
                                                                    </label>
                                                                </a>
                                                            </li>
                                                            
				<?php
															$innKeys_cat++;
														}
														else
														{
															//echo "not found";
														}
												
													}
													
													if($consentStatus!="true")
													{
													
														$kId			= $key.'&innerKey='.$innKeys_cat.'&preColor='.urlencode($color_cat);
														$tempPrecolor 	= urlencode($color_cat);
														$linkSessionId 	= "&patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";						
														$subId 			= $innKeys_cat;
														
														if(($linkStatus_cat == true) && (!$pop))
														{
															$href	=	"javascript:left_link_click('".$lnkname."','".$key."','".$innKeys_cat."','".$tempPrecolor."','".$patient_id."','".$pConfId."','".$ascId."','".$consentMultipleId."','".$consent_id_purge."','')";								
														}
														else if(!$pop)
														{
															$href	=	"javascript:accessAlert();";	
														}
				?>
												  
                                                  		<li class="" id="sub<?php echo $subId; ?>">
                                                            <a 	href="<?=$href?>"
                                                                style="background-color:<?php echo $color_cat;?>;<?php echo $strikeConsentPurge;?> <?php if($linkStatus_cat != true) echo "color:#666666;"; ?> "
                                                                title="<?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>"
                                                                onClick="javascript: <?php echo $pop;?>  "
                                                                
                                                            >
                                                                <span class="span_over" style="width:175px;">
                                                                    <b class="fa fa-caret-right"></b>
                                                                    <b class="fa fa-caret-right"></b>
                                                                    <?php echo stripslashes(ucfirst($consentFormTemplateCatConsentAlias));  ?>
                                                                </span>
                                                                <label id="consent_chkMrkImageStatusId<?php echo $subId; ?>">
                                                                	<?php echo $chkMrkImage_consent; ?>
                                                                </label>
                                                            </a>
                                                        </li>
                                                  
                                                  		
				<?php				
														$innKeys_cat++;
				
													}	
												
												
												} 		
												// Else Part End (Not Purged)
												
												
												
											} // End While Consent Form Template 
										
										
											echo '</ul>' ;
										} // End IF  consentFormTemplateCatFormSelectNumRow > 0
										
									
									} // End While
								
							
							
								}// $consentFormTemplateCatSelectNumRow > 0
								
							
							
							} //  if($key==0)
						?>
						
                        </ul>
                        
                        
			<?php                           
			
					} // End IF SubMenuListArr[$key] Contains greator than zer0 values
			?>
			</li>
			<?php
		}                                                        
		?>
	</ul>	
	</nav><!--END TABNAV-->
</div>
<script language="javascript">
function yellow(subMenu,preColor,hiddPurgestatus){//alert(subMenu+' -- '+preColor+' -- '+hiddPurgestatus);
	if(document.getElementById("subMenuFld")){
		var subMenuId = document.getElementById("subMenuFld");
	}
	if(document.getElementById("pre_color")){
		var preColorChange = document.getElementById("pre_color");
	}
	if(subMenuId.value != ''){			
		var id = subMenuId.value;
		if(preColorChange.value) {
			if(document.getElementById("sub"+id)){
				document.getElementById("sub"+id).style.background = preColorChange.value;
			}
		}else {
				document.getElementById("sub"+id).style.background = "#999999";
				if(document.getElementById("pre_hiddPurgestatus").value=='true'){ 
					document.getElementById("sub"+id).style.backgroundImage = "url(images/strike_image.jpg) !important"; 
					document.getElementById("sub"+id).style.backgroundRepeat="repeat-x !important";
					document.getElementById("sub"+id).style.backgroundPosition="center !important";
				}
				
		}
		
	}
	if(document.getElementById("sub"+subMenu)){
		document.getElementById("sub"+subMenu).style.background = "#FFFF99";
	}
	if(hiddPurgestatus =="true"){ 
		document.getElementById("sub"+subMenu).style.backgroundImage = "url(images/strike_image.jpg)  !important"; 
		document.getElementById("sub"+subMenu).style.backgroundRepeat="repeat-x  !important";
		document.getElementById("sub"+subMenu).style.backgroundPosition="center  !important";
		document.getElementById("pre_hiddPurgestatus").value='true';
		
	}else {
		document.getElementById("pre_hiddPurgestatus").value='';
	}
	subMenuId.value = subMenu;		
	preColorChange.value = preColor;	
}

function epostpop(posLeft, posTop){	
	var chrtopen = false;
	if(typeof(top.frames[0])!="undefined") {
		if(typeof(top.frames[0].frames[0])!="undefined") {		
			if (top.frames[0].frames[0].document.getElementById('evaluationEPostDiv')){
				chrtopen=true;
				top.frames[0].frames[0].document.getElementById('evaluationEPostDiv').style.display = 'block';
				top.frames[0].frames[0].document.getElementById('evaluationEPostDiv').style.left = posLeft;
				top.frames[0].frames[0].document.getElementById('evaluationEPostDiv').style.top = posTop;
			}
		}
	}
	if(chrtopen==false) {
		alert("Please open any form for epost") 	
	}
	
}
function eclose(){
	if(typeof(top.frames[0])!="undefined") {
		if(typeof(top.frames[0].frames[0])!="undefined") {		
			top.frames[0].frames[0].document.getElementById('evaluationEPostDiv').style.display = 'none';
		}
	}
}

var userType = '<?php echo $user_type;?>';
if(userType == 'Surgeon') {
	if(document.getElementById('main3')) {
		document.getElementById('main3').style.display="block";
	}
	if(document.getElementById('main5')) {
		document.getElementById('main5').style.display="block";
	}
	if(document.getElementById('main6')) {
		document.getElementById('main6').style.display="block";
	}
	if(document.getElementById('main7')) {
		document.getElementById('main7').style.display="block";
	}
}

var formType = '' ;
function changeChkMarkImage(ChkMarkImageStId,frmStatus,formType){
	//alert(ChkMarkImageStId+'\n'+frmStatus);
	var flagObj	=	"chkMrkImageStatusId"+ChkMarkImageStId ;
	formType	=	(typeof formType == 'undefined') ? '' : formType ;
	flagObj		=	(formType !== '') ?	formType + '_' + flagObj :	flagObj ;
	
	if(ChkMarkImageStId) {
		if(document.getElementById(flagObj)) {
			if(frmStatus=='not completed') {
				document.getElementById(flagObj).innerHTML = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
			}else if(frmStatus=='completed') {
				document.getElementById(flagObj).innerHTML = "<img src='images/green_flag.png' width='12' height='14' border='0'>";
			}else {
				document.getElementById(flagObj).innerHTML = "";
			}
		}	
	}	
}
</script>