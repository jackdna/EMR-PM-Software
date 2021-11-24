<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
set_time_limit(700);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
if(!$surgeryCenterDirectoryName) {
	$surgeryCenterDirectoryName='surgerycenter';
}
if(!$iolinkDirectoryName) {
	$iolinkDirectoryName='iolink';
}

include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$rootServerPath = $_SERVER['DOCUMENT_ROOT'];

$selDos = $_REQUEST['selDos'];
$multiPatientInWaitingId = $_REQUEST['multiPatientInWaitingId'];
//START TO SET PATIENT RECORD FROM IOLINK TO SURGERYCENTER
	
	//$getPatientInWaitingTblInfoQry 	= "SELECT * FROM patient_in_waiting_tbl WHERE dos='".$selDos."' AND patient_status!='Canceled' AND iolinkSyncroStatus!='Syncronized' ORDER BY surgery_time ASC";
	$getPatientInWaitingTblInfoQry 	= "SELECT * FROM patient_in_waiting_tbl WHERE dos='".$selDos."' AND patient_status!='Canceled' AND patient_in_waiting_id IN(".$multiPatientInWaitingId.") ORDER BY surgery_time ASC";
	$getPatientInWaitingTblInfoRes 	= imw_query($getPatientInWaitingTblInfoQry) or die(imw_error());
	$getPatientInWaitingTblNumRow 	= imw_num_rows($getPatientInWaitingTblInfoRes);
	$patientIdArr = array();
	if($getPatientInWaitingTblNumRow>0) {
		while($getPatientInWaitingTblRow=imw_fetch_array($getPatientInWaitingTblInfoRes)) {
			$iolink_allergiesNKDA_status=0;
			$iolink_allergiesNKDA_status	= $getPatientInWaitingTblRow['iolink_allergiesNKDA_status'];
			$iolink_no_medication_status	= $getPatientInWaitingTblRow['iolink_no_medication_status'];
			$iolink_no_medication_comments	= $getPatientInWaitingTblRow['iolink_no_medication_comments'];
			$patient_id 			= $getPatientInWaitingTblRow['patient_id'];
			$patientIdExtractedTbl	= $getPatientInWaitingTblRow['patient_id']; //This variable is used below
			$iAscSyncroCount		= $getPatientInWaitingTblRow['iAscSyncroCount'];
			
			$patientDataTblQry 		= "SELECT * FROM `patient_data_tbl` WHERE patient_id='".$patient_id."'";
			$patientDataTblRes 		= imw_query($patientDataTblQry) or die(imw_error()); 
			$patientDataTblNumRow 	= imw_num_rows($patientDataTblRes);
			if($patientDataTblNumRow>0) {
				$patientDataTblRow 	= imw_fetch_array($patientDataTblRes);
				$patient_first_name = $patientDataTblRow['patient_fname'];
				$patient_middle_name= $patientDataTblRow['patient_mname'];
				$patient_last_name 	= $patientDataTblRow['patient_lname'];
				$patient_name 		= $patient_last_name.", ".$patient_first_name;
				$patient_zip 		= $patientDataTblRow['zip'];
				$patient_dob 		= $patientDataTblRow['date_of_birth'];
				
			}
			$patient_in_waiting_id 	= $getPatientInWaitingTblRow['patient_in_waiting_id'];
			$patient_in_waiting_comment 	= $getPatientInWaitingTblRow['comment'];
			$patient_dos_temp 		= $getPatientInWaitingTblRow['dos'];
			$patient_dos='';
			if($patient_dos_temp!=0) { $patient_dos = date('m-d-Y',strtotime($patient_dos_temp)); }
			unset($conditionArr);
			/*
			$conditionArr['patient_first_name']		= addslashes($patient_first_name);
			$conditionArr['patient_last_name']		= addslashes($patient_last_name);
			$conditionArr['patient_dob']			= $patient_dob;
			$conditionArr['patient_zip']			= $patient_zip;
			$conditionArr['dos']					= $patient_dos_temp;
			*/
			$conditionArr['iolink_patient_in_waiting_id']	= $patient_in_waiting_id;
			$conditionArr['dos']							= $selDos;
			//$conditionArr['patient_status !']	= 'Canceled';
			
			$getStubDetails = $objManageData->getMultiChkArrayRecords("stub_tbl", $conditionArr,"stub_id","ASC LIMIT 0,1"," AND patient_status !='Canceled' ");
			if($getStubDetails) {
				foreach($getStubDetails as $stubTableDetails){
					$stubTblStubId 			 		= $stubTableDetails->stub_id;
					$stubTblConfirmationId 	 		= $stubTableDetails->patient_confirmation_id;
					$stubTblPatientStatus 	 		= $stubTableDetails->patient_status;
					
					if($stubTblPatientStatus!='Canceled') {
						$patientIdArr[]				= $patientIdExtractedTbl;
						/*
						if($iAscSyncroCount == 0) {//ADD COMMENT ONLY ONCE
							unset($arrayPatientWaitingRecord);
							$arrayPatientWaitingRecord['comment'] = $patient_in_waiting_comment;
							$objManageData->updateRecords($arrayPatientWaitingRecord, 'stub_tbl', 'stub_id', $stubTblStubId);
						}*/
						if(!$stubTblConfirmationId) {
							$patient_zip 			= $stubTableDetails->patient_zip;
							$pickup_time 			= $stubTableDetails->pickup_time;
							$arrival_time 			= $stubTableDetails->arrival_time;
							$patient_site 			= $stubTableDetails->site;
							$surgeon_fname 			= stripslashes(trim($stubTableDetails->surgeon_fname));
							$surgeon_mname 			= stripslashes(trim($stubTableDetails->surgeon_mname));
							$surgeon_lname 			= stripslashes(trim($stubTableDetails->surgeon_lname));
							
							if($surgeon_mname){
								$surgeon_mname 		= ' '.$surgeon_mname;
							}
							$surgeon_name 			= $surgeon_fname.$surgeon_mname.' '.$surgeon_lname;
							
							$surgery_time 			= $stubTableDetails->surgery_time;
							$imwPatientId 		= $stubTableDetails->imwPatientId;
							$patient_prim_proc 		= stripslashes($stubTableDetails->patient_primary_procedure);
							$patient_sec_proc 		= stripslashes($stubTableDetails->patient_secondary_procedure);
						
							$patient_anes_fname 	= stripslashes($stubTableDetails->anesthesiologist_fname);
							$patient_anes_mname 	= stripslashes($stubTableDetails->anesthesiologist_mname);
							$patient_anes_lname 	= stripslashes($stubTableDetails->anesthesiologist_lname);
							if($patient_anes_mname) {
								$patient_anes_mname = ' '.$patient_anes_mname;
							}
							$patient_anes_name 		= $patient_anes_fname.$patient_anes_mname.' '.$patient_anes_lname;
							
							$patient_nurse_fname 	= stripslashes($stubTableDetails->confirming_nurse_fname);
							$patient_nurse_mname 	= stripslashes($stubTableDetails->confirming_nurse_mname);
							$patient_nurse_lname 	= stripslashes($stubTableDetails->confirming_nurse_lname);
							if($patient_nurse_mname) {
								$patient_nurse_mname= ' '.$patient_nurse_mname;
							}
							$conf_nurse 			= $patient_nurse_fname.$patient_nurse_mname.' '.$patient_nurse_lname;
					
							$patient_dos_temp 		= $stubTableDetails->dos;
							$assist_by_trans 		= $stubTableDetails->assisted_by_translator;											
						
							//GET primary procedure id
								if($patient_prim_proc) {
									$primaryprocedure = imw_query("select `procedureId`,`name` from procedures where (name='".addslashes($patient_prim_proc)."' OR procedureAlias = '".addslashes($patient_prim_proc)."') AND LOWER(del_status) != 'yes' ");
									if(imw_num_rows($primaryprocedure)>0) {
										$primary_proc_data	= imw_fetch_array($primaryprocedure);
										$PrimaryProcedure_id= $primary_proc_data['procedureId'];
										$patient_prim_proc 	= $primary_proc_data['name'];
									}
								}	
							//END GET primary procedure id
							
							//GET secondary procedure id	
								if($patient_sec_proc) {
									$secondaryprocedure=imw_query("select `procedureId`,`name` from procedures where (name='".addslashes($patient_sec_proc)."' OR procedureAlias = '".addslashes($patient_sec_proc)."') AND LOWER(del_status) != 'yes' ");
									if(imw_num_rows($secondaryprocedure)>0) {
										$secondary_proc_data	= imw_fetch_array($secondaryprocedure);
										$SecondaryProcedure_id 	= $secondary_proc_data['procedureId'];
										$patient_sec_proc 		= $secondary_proc_data['name'];
									}
								}		
							//END GET secondary id			
						
							//GET surgeon id	
								//$getSurgeonIdQry=imw_query("select * from `users` where fname='".addslashes($surgeon_fname)."' AND mname='".addslashes($surgeon_mname)."' AND lname='".addslashes($surgeon_lname)."'");
								$getSurgeonIdQry=imw_query("select usersId from `users` where fname='".addslashes($surgeon_fname)."' AND lname='".addslashes($surgeon_lname)."'");
								if(imw_num_rows($getSurgeonIdQry)>0) {
									$getSurgeonIdRow	=	imw_fetch_array($getSurgeonIdQry);
									$surgeon_id 		= 	$getSurgeonIdRow['usersId'];
								}	
							//END GET surgeon id			
							
							//GET anesthesiologist id	
								//$getAnesthesiologistIdQry=imw_query("select * from `users` where fname='".addslashes($patient_anes_fname)."' AND mname='".addslashes($patient_anes_mname)."' AND lname='".addslashes($patient_anes_lname)."'");
								$getAnesthesiologistIdQry=imw_query("select usersId from `users` where fname='".addslashes($patient_anes_fname)."' AND lname='".addslashes($patient_anes_lname)."'");
								if(imw_num_rows($getAnesthesiologistIdQry)>0) {
									$getAnesthesiologistIdRow	=	imw_fetch_array($getAnesthesiologistIdQry);
									$anes_id 					= 	$getAnesthesiologistIdRow['usersId'];
								}	
							//END GET anesthesiologist id			
						
							//GET nurse id	
								$getNurseIdQry=imw_query("select usersId from `users` where fname='".addslashes($patient_nurse_fname)."' AND mname='".addslashes($patient_nurse_mname)."' AND lname='".addslashes($patient_nurse_lname)."'");
								if(imw_num_rows($getNurseIdQry)>0) {
									$getNurseIdRow=imw_fetch_array($getNurseIdQry);
									$confNurse_id = $getNurseIdRow['usersId'];
								}	
							//END GET nurse id			
									
							//APPLYING NUMBERS TO PATIENT SITE
								$patient_site_no='';
								if($patient_site == "left") {
									$patient_site_no = 1;
								}else if($patient_site == "right") {
									$patient_site_no = 2;
								}else if($patient_site == "both") {
									$patient_site_no = 3;
								}else if($patient_site == "left upper lid") {
									$patient_site_no = 4;
								}else if($patient_site == "left lower lid") {
									$patient_site_no = 5;
								}else if($patient_site == "right upper lid") {
									$patient_site_no = 6;
								}else if($patient_site == "right lower lid") {
									$patient_site_no = 7;
								}else if($patient_site == "bilateral upper lid") {
									$patient_site_no = 8;
								}else if($patient_site == "bilateral lower lid") {
									$patient_site_no = 9;
								}
							//END APPLYING NUMBERS TO PATIENT SITE
								unset($arrayRecord);
								$arrayRecord['patientId'] 						= $patient_id;
								$arrayRecord['dos'] 							= $patient_dos_temp;
								$arrayRecord['surgery_time'] 					= $surgery_time;
								$arrayRecord['pickup_time'] 					= $pickup_time;
								$arrayRecord['arrival_time'] 					= $arrival_time;
								$arrayRecord['assist_by_translator'] 			= $assist_by_trans;
								$arrayRecord['patient_primary_procedure'] 		= addslashes($patient_prim_proc);
								$arrayRecord['patient_primary_procedure_id']	= $PrimaryProcedure_id;
								$arrayRecord['patient_secondary_procedure'] 	= addslashes($patient_sec_proc);
								$arrayRecord['patient_secondary_procedure_id'] 	= $SecondaryProcedure_id;
								$arrayRecord['site'] 							= $patient_site_no;
								$arrayRecord['zip'] 							= $patient_zip;
								$arrayRecord['surgeonId'] 						= $surgeon_id;
								$arrayRecord['surgeon_name'] 					= addslashes($surgeon_name);
								$arrayRecord['anesthesiologist_name'] 			= addslashes($patient_anes_name);
								$arrayRecord['anesthesiologist_id'] 			= $anes_id;			
								$arrayRecord['confirm_nurse'] 					= addslashes($conf_nurse);
								$arrayRecord['nurseId'] 						= $confNurse_id;
								$arrayRecord['patientStatus'] 					= 'Scheduled';
								$arrayRecord['dateConfirmation'] 				= date("Y-m-d H:i:s");
								$arrayRecord['imwPatientId'] 					= $imwPatientId;
								//$arrayRecord['stub_id'] 						= $stubTblStubId;
								$arrayRecord['allergiesNKDA_status'] 			= $iolink_allergiesNKDA_status;
								$arrayRecord['no_medication_status'] 			= $iolink_no_medication_status;
								$arrayRecord['no_medication_comments'] 			= $iolink_no_medication_comments;
								
								//CHECK IF PATIENT ALREADY CONFIRMED (IF NOT THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
									$chkPatientAlreadyConfirmedQry 		= "SELECT patientconfirmation.patientConfirmationId FROM patientconfirmation,stub_tbl 
																			WHERE patientconfirmation.patientId			= '$patient_id' 
																			  AND patientconfirmation.dos				= '$patient_dos_temp'
																			  AND stub_tbl.patient_status		   	   != 'Canceled'
																			  AND stub_tbl.iolink_patient_in_waiting_id	= '".$patient_in_waiting_id."'
																			  AND stub_tbl.patient_confirmation_id	= patientconfirmation.patientConfirmationId
																			  ";
									$chkPatientAlreadyConfirmedRes 		= imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
									$chkPatientAlreadyConfirmedNumRow 	= imw_num_rows($chkPatientAlreadyConfirmedRes);
									if($chkPatientAlreadyConfirmedNumRow>0) {
										
										$chkPatientAlreadyConfirmedRow 	= imw_fetch_array($chkPatientAlreadyConfirmedRes);
										$stubTblConfirmationId 			= $chkPatientAlreadyConfirmedRow['patientConfirmationId'];
										//$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $stubTblConfirmationId);
									
									}else { //(IF NOT ALREADY CONFIRMED THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
									
										$stubTblConfirmationId = $objManageData->addRecords($arrayRecord, 'patientconfirmation');
										
										// UPDATE IN STUB TABLE 
										$update_stub_status_qry 	= "update `stub_tbl` set 
																			patient_confirmation_id = '$stubTblConfirmationId'
																			WHERE stub_id = '".$stubTblStubId."'";
										$update_stub_status_res 	= 	imw_query($update_stub_status_qry) or die(imw_error());							
									
									}
								//CHECK IF PATIENT ALREADY CONFIRMED
							//END INSERT/UPDATE IN CONFIRMATION TABLE
						
							//INSERT CONFIRMATION_ID AND PATIENT_ID IN  left_navigation_forms TABLE 	
								$chk_left_menu_ins_query=imw_query("select `id` from `left_navigation_forms` where confirmationId='".$stubTblConfirmationId."' AND patient_id='".$patient_id."'");
								if(imw_num_rows($chk_left_menu_ins_query)>0) {
									//DO NOTHING
								}else {
									$left_menu_ins_query 	= "insert into left_navigation_forms set confirmationId = '$stubTblConfirmationId', patient_id = '$patient_id'";
									$left_menu_ins_res 		= 	imw_query($left_menu_ins_query) or die(imw_error());		
								}
							//END INSERT CONFIRMATION_ID AND PATIENT_ID IN left_navigation_forms TABLE
						}
						
						//START SYNCRONIZING RECORDS
						
						//START ALLERGIES
						$iolinkAllergiesReactionDetails 	= $objManageData->getArrayRecords('iolink_patient_allergy', 'patient_in_waiting_id', $patient_in_waiting_id);
						if(count($iolinkAllergiesReactionDetails)>0){
							foreach($iolinkAllergiesReactionDetails as $iolinkAllergyName){
								$iolinkPreOpAllergyId 	= $iolinkAllergyName->pre_op_allergy_id;
								$iolinkAllergy 	= $iolinkAllergyName->allergy_name;
								$reaction 		= $iolinkAllergyName->reaction_name;		
								unset($iolinkAddAllergyArr);
								$iolinkAddAllergyArr['allergy_name'] 			= addslashes($iolinkAllergy);
								$iolinkAddAllergyArr['reaction_name'] 			= addslashes($reaction);
								$iolinkAddAllergyArr['patient_confirmation_id'] = $stubTblConfirmationId;
								$iolinkAddAllergyArr['patient_id'] 				= $iolinkAllergyName->patient_id;
								$iolinkAddAllergyArr['iolink_pre_op_allergy_id'] = $iolinkPreOpAllergyId;
								
								unset($iolinkAllergyChkArr);
								$iolinkAllergyChkArr['iolink_pre_op_allergy_id']= $iolinkPreOpAllergyId;
								$iolinkAllergyChkArr['patient_confirmation_id'] = $stubTblConfirmationId;
								$chkAllergyExist = $objManageData->getMultiChkArrayRecords('patient_allergies_tbl', $iolinkAllergyChkArr);
								if($chkAllergyExist) {
									$pre_op_allergy_id = $chkAllergyExist[0]->pre_op_allergy_id;
									$objManageData->updateRecords($iolinkAddAllergyArr, 'patient_allergies_tbl', 'pre_op_allergy_id', $pre_op_allergy_id);
								}else {	
									$objManageData->addRecords($iolinkAddAllergyArr, 'patient_allergies_tbl');
								}
							}
						}
						//END ALLERGIES
						
						
						
						//START IOL MODEL
						unset($iolCondArray) ;
						$iolCondArray['patient_in_waiting_id']	=	$patient_in_waiting_id ;
						$iolCondArray['opRoomDefault']	=	1;
						$iolinkIolModelDetails 	= $objManageData->getMultiChkArrayRecords('iolink_iol_manufacturer',$iolCondArray,'iol_manufacturer_id',' DESC LIMIT 0,1');
						if(!$iolinkIolModelDetails) {
							unset($iolCondArray) ;
							$iolCondArray['patient_in_waiting_id']	=	$patient_in_waiting_id ;
							$iolCondArray['opRoomDefault']	=	0;
							$iolinkIolModelDetails 	= $objManageData->getMultiChkArrayRecords('iolink_iol_manufacturer',$iolCondArray,'iol_manufacturer_id',' DESC LIMIT 0,1');
						}
						unset($iolinkAddIolModeArr);
						if(count($iolinkIolModelDetails) > 0)
						{
							$manufacturer	=	addslashes($iolinkIolModelDetails[0]->manufacture) ;
							$brand				=	addslashes($iolinkIolModelDetails[0]->lensBrand);
							$model			=	addslashes($iolinkIolModelDetails[0]->model);	
							$diopter			=	addslashes($iolinkIolModelDetails[0]->Diopter);
							
							$opRoomwhere		=	"Where confirmation_id = '".$stubTblConfirmationId."' And form_status <> 'completed' And form_status <> 'not completed' " ;
							$opRoomChkQry	=	"Select * From operatingroomrecords ".$opRoomwhere	;
							$opRoomChkSql		=	imw_query($opRoomChkQry) or die(imw_error()); 
							$opRoomChkCnt		=	imw_num_rows($opRoomChkSql) ;
							if( $opRoomChkCnt > 0  )
							{
								$opRoomQry		=	"Update operatingroomrecords Set  manufacture = '".$manufacturer."', lensBrand = '".$brand."', model = '".$model."', Diopter = '".$diopter."' ".$opRoomwhere	;
								$opRoomSql		=	imw_query($opRoomQry) or die(imw_error());
								
							}
							
						}
						//END IOL MODEL
						
						
						//START MEDICATION
						$iolikGetMedicationDetails = $objManageData->getArrayRecords('iolink_patient_prescription_medication', 'patient_in_waiting_id', $patient_in_waiting_id);
						
						if(count($iolikGetMedicationDetails)>0){
							foreach($iolikGetMedicationDetails as $iolinkMedication){
								$iolinkPrescriptionMedicationId = $iolinkMedication->prescription_medication_id;
								$iolinkMedicationName = $iolinkMedication->prescription_medication_name;
								$iolinkMedicationDesc = $iolinkMedication->prescription_medication_desc;
								$iolinkMedicationSig = $iolinkMedication->prescription_medication_sig;
								unset($iolinkAddMedicationArr);
								$iolinkAddMedicationArr['prescription_medication_name'] = addslashes($iolinkMedicationName);
								$iolinkAddMedicationArr['prescription_medication_desc'] = addslashes($iolinkMedicationDesc);
								$iolinkAddMedicationArr['prescription_medication_sig'] = addslashes($iolinkMedicationSig);
								$iolinkAddMedicationArr['confirmation_id'] 				= $stubTblConfirmationId;
								$iolinkAddMedicationArr['patient_id'] 					= $iolinkMedication->patient_id;
								$iolinkAddMedicationArr['iolink_prescription_medication_id']= $iolinkPrescriptionMedicationId;
								
								unset($iolinkMedicationChkArr);
								$iolinkMedicationChkArr['iolink_prescription_medication_id']= $iolinkPrescriptionMedicationId;
								$iolinkMedicationChkArr['confirmation_id'] = $stubTblConfirmationId;
								$chkMediExist = $objManageData->getMultiChkArrayRecords('patient_prescription_medication_healthquest_tbl', $iolinkMedicationChkArr);
								if($chkMediExist) {
									$prescription_medication_id = $chkMediExist[0]->prescription_medication_id;
									$objManageData->updateRecords($iolinkAddMedicationArr, 'patient_prescription_medication_healthquest_tbl', 'prescription_medication_id', $prescription_medication_id);
								}else {	
									$objManageData->addRecords($iolinkAddMedicationArr, 'patient_prescription_medication_healthquest_tbl');
								}

								// Insert IOLINK medications into table related to H&P medications	
								$chkMediExist1 = $objManageData->getMultiChkArrayRecords('patient_anesthesia_medication_tbl', $iolinkMedicationChkArr);
								if($chkMediExist1) {
									$prescription_medication_id_hp = $chkMediExist1[0]->prescription_medication_id;
									$objManageData->updateRecords($iolinkAddMedicationArr, 'patient_anesthesia_medication_tbl', 'prescription_medication_id', $prescription_medication_id_hp);
								}else {
									$objManageData->addRecords($iolinkAddMedicationArr, 'patient_anesthesia_medication_tbl');
								}

							}
						}
						//END MEDICATION					
						
						//START INSERTING QUESTIONS OF ADMIN FOR HEALTHQUESTIONNAIRE CHARTNOTE
						unset($conditionArrChkHealthQuest);
						$conditionArrChkHealthQuest['confirmation_id']	= $stubTblConfirmationId;
						$conditionArrChkHealthQuest['form_status']		= "completed";
						$chkHealthQuestCompleteRecordExist = $objManageData->getMultiChkArrayRecords('preophealthquestionnaire', $conditionArrChkHealthQuest);
						$iolinkHealthquestionAdminDetails = $objManageData->getArrayRecords('iolink_healthquestionadmin', 'patient_in_waiting_id', $patient_in_waiting_id);
						unset($iolinkAdminQuestionArr);
						if(count($iolinkHealthquestionAdminDetails)>0){
							foreach($iolinkHealthquestionAdminDetails as $iolinkHealthquestionAdmin){
								$iolinkAdminQuestion 		= $iolinkHealthquestionAdmin->adminQuestion;
								$iolinkAdminQuestionStatus 	= $iolinkHealthquestionAdmin->adminQuestionStatus;
								$iolinkAdminQuestionDesc 	= $iolinkHealthquestionAdmin->adminQuestionDesc;
									
								$iolinkAdminQuestionArr['adminQuestion'] 		= addslashes($iolinkAdminQuestion);
								$iolinkAdminQuestionArr['adminQuestionStatus'] 	= addslashes($iolinkAdminQuestionStatus);
								$iolinkAdminQuestionArr['adminQuestionDesc'] 	= addslashes($iolinkAdminQuestionDesc);
								$iolinkAdminQuestionArr['confirmation_id'] 		= $stubTblConfirmationId;
								$iolinkAdminQuestionArr['patient_id'] 			= $iolinkHealthquestionAdmin->patient_id;
								
								$iolinkAdminQuestionConditionArr['adminQuestion'] 	= addslashes($iolinkAdminQuestion);
								$iolinkAdminQuestionConditionArr['confirmation_id'] = $stubTblConfirmationId;
								$iolinkAdminQuestionConditionArr['patient_id'] 		= $iolinkHealthquestionAdmin->patient_id;
								
								$iolinkAdminQuestionExist = $objManageData->getMultiChkArrayRecords('healthquestionadmin', $iolinkAdminQuestionConditionArr);
								if($iolinkAdminQuestionExist) {
									foreach($iolinkAdminQuestionExist as $iolinkAdminQuestionDetail){
										$iolinkAdminQuestionId 	= $iolinkAdminQuestionDetail->id;
										if(!$chkHealthQuestCompleteRecordExist) {//UPDATE ONLY IF FLAG IS NOT GREEN IN SURGERYCENTER
											$objManageData->updateRecords($iolinkAdminQuestionArr, 'healthquestionadmin', 'id', $iolinkAdminQuestionId);
										}
									}
								}else if(!$chkHealthQuestCompleteRecordExist) {//UPDATE ONLY IF FLAG IS NOT GREEN IN SURGERYCENTER	
									$objManageData->addRecords($iolinkAdminQuestionArr, 'healthquestionadmin');
								}
							}
						}	
						//END INSERTING QUESTIONS OF ADMIN FOR HEALTHQUESTIONNAIRE CHARTNOTE					
						
						//START CREATE 'PATIENT-INFO AND CLINICAL AND IOL' FOLDER FOR SCAN AND INSERT SCAN CARD FROM IOLINK	
						$iolinkFormFolderArr = array('Pt. Info', 'Clinical','IOL','H&P','EKG','Health Questionnaire','Ocular Hx','Consent');
						//$iolinkFormFolderArr = array('Pt. Info', 'Clinical');
						foreach($iolinkFormFolderArr as $iolinkFormFolder){
							
							$iolinkScanFolderName = $iolinkFormFolder; //DEFAULT SETTING
							if($iolinkFormFolder=='Pt. Info') 					{	$iolinkScanFolderName = 'ptInfo';
							}else if($iolinkFormFolder=='Clinical') 			{	$iolinkScanFolderName = 'clinical';
							}else if($iolinkFormFolder=='IOL') 			{	$iolinkScanFolderName = 'iol';
							}else if($iolinkFormFolder=='H&P') 					{	$iolinkScanFolderName = 'h&p';
							}else if($iolinkFormFolder=='EKG') 					{	$iolinkScanFolderName = 'ekg';
							}else if($iolinkFormFolder=='Health Questionnaire') {	$iolinkScanFolderName = 'healthQuest';
							}else if($iolinkFormFolder=='Ocular Hx') 			{	$iolinkScanFolderName = 'ocularHx';
							}else if($iolinkFormFolder=='Consent') 				{	$iolinkScanFolderName = 'consent';
							}	
							
							$iolink_scan_consent_qry 			= "select * from iolink_scan_consent where patient_in_waiting_id = '".$patient_in_waiting_id."' AND iolink_scan_folder_name = '".$iolinkScanFolderName."' AND patient_id = '".$patient_id."'";
							$iolink_scan_consent_res 			= imw_query($iolink_scan_consent_qry) or die(imw_error());
							$iolink_scan_consent_numrow 		= imw_num_rows($iolink_scan_consent_res);
							
							$chk_iolink_scan_document_qry 	= "select document_id from scan_documents where document_name = '".$iolinkFormFolder."' AND patient_id = '".$patient_id."' AND confirmation_id = '".$stubTblConfirmationId."' AND stub_id = '".$stubTblStubId."'";
							$chk_iolink_scan_document_res 	= imw_query($chk_iolink_scan_document_qry) or die(imw_error());
							$chk_iolink_scan_document_numrow= imw_num_rows($chk_iolink_scan_document_res);
							
							if($iolink_scan_consent_numrow>0 || $iolinkFormFolder=='Pt. Info' || $iolinkFormFolder=='Clinical' || $iolinkFormFolder == 'IOL' ) {
								if($chk_iolink_scan_document_numrow<=0) {
									unset($iolinkArrayScanRecord);
									$iolinkArrayScanRecord['patient_id'] 		= $patient_id;
									$iolinkArrayScanRecord['confirmation_id'] 	= $stubTblConfirmationId;
									$iolinkArrayScanRecord['document_name'] 	= $iolinkFormFolder;
									$iolinkArrayScanRecord['dosOfScan'] 		= $patient_dos_temp;
									$iolinkArrayScanRecord['stub_id'] 			= $stubTblStubId;
									$iolinkInsertScanId = $objManageData->addRecords($iolinkArrayScanRecord, 'scan_documents');
								}else if($chk_iolink_scan_document_numrow>0) {
									$chk_iolink_scan_document_row = imw_fetch_array($chk_iolink_scan_document_res);
									$iolinkInsertScanId = $chk_iolink_scan_document_row['document_id'];
								}
							}	
							if($iolink_scan_consent_numrow>0) {
								while($iolink_scan_consent_row 	= imw_fetch_array($iolink_scan_consent_res)) {
									$iolinkScanConsentId 		= $iolink_scan_consent_row['scan_consent_id'];
									$iolink_img_content 		= $iolink_scan_consent_row['scan1Upload'];
									$iolink_upload_document_name= urldecode($iolink_scan_consent_row['document_name']);
									$iolink_mask 				= urldecode($iolink_scan_consent_row['mask']);
									
									//START REMOVE .JPG FROM DOCUMENT NAME
									
									if($iolink_upload_document_name) {
										$iolinkDocumentNameArr = explode('.',$iolink_upload_document_name);
										if(trim($iolinkDocumentNameArr[1])=='jpg') {
											$iolink_upload_document_name = $iolinkDocumentNameArr[0];
										}	
									}
									if($iolink_mask) { //IOLINK MASK IS USED FOR IMWPURPOSE
										$iolinkMaskArr 			= explode('.',$iolink_mask);
										$iolink_upload_document_name = $iolinkMaskArr[0];
									}
									//END REMOVE .JPG FROM DOCUMENT NAME
									
									$iolink_image_type 			= $iolink_scan_consent_row['image_type'];
									$iolink_document_size 		= $iolink_scan_consent_row['document_size'];
									$iolink_pdfFilePath 		= urldecode($iolink_scan_consent_row['pdfFilePath']);
									$iolink_pdfFilePath 		= str_ireplace("//","/",$iolink_pdfFilePath);
									//if($iolink_img_content) {
										unset($iolinkArrayScanUploadRecord);
										$iolinkArrayScanUploadRecord['image_type'] 				= $iolink_image_type;
										$iolinkArrayScanUploadRecord['pdfFilePath'] 			= $iolink_pdfFilePath;
										$iolinkArrayScanUploadRecord['document_size'] 			= $iolink_document_size;
										$iolinkArrayScanUploadRecord['document_name'] 			= addslashes($iolink_upload_document_name);
										$iolinkArrayScanUploadRecord['document_size'] 			= '';
										$iolinkArrayScanUploadRecord['confirmation_id'] 		= $stubTblConfirmationId;
										$iolinkArrayScanUploadRecord['patient_id'] 				= $patient_id;
										$iolinkArrayScanUploadRecord['document_id'] 			= $iolinkInsertScanId;
										$iolinkArrayScanUploadRecord['img_content'] 			= addslashes($iolink_img_content);
										$iolinkArrayScanUploadRecord['iolink_scan_consent_id'] 	= $iolinkScanConsentId;
										$iolinkArrayScanUploadRecord['dosOfScan'] 				= $selDos;
										$iolinkArrayScanUploadRecord['stub_id'] 				= $stubTblStubId;
										
										if($iolink_pdfFilePath) {
											
											$iolinkPdfFileFullPath 			= 	$rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$iolink_pdfFilePath;
											$surgerycenterPdfFileFullPath 	= 	$rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$iolink_pdfFilePath;
											
											$iolink_pdfFilePathExplode = explode('/',$iolink_pdfFilePath);
											
											$iolink_pdfFilePathExplodeNew = '/'.$iolink_pdfFilePathExplode[1];
											if(stristr($iolink_pdfFilePathExplode[1],".pdf") !== false || stristr($iolink_pdfFilePathExplode[1],".jpg") !== false) {
												$iolink_pdfFilePathExplodeNew = '';
												$oldPdfFolderNameInSurgerycenter = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$iolink_pdfFilePathExplode[0].'/'.$iolink_pdfFilePathExplode[1];
												if(is_dir($oldPdfFolderNameInSurgerycenter)) {
													$newPdfFolderNameInSurgerycenter = str_ireplace(".","_",$oldPdfFolderNameInSurgerycenter);
													rename($oldPdfFolderNameInSurgerycenter,$newPdfFolderNameInSurgerycenter);
												}
											}
											$pdfFolderNameInSurgerycenter = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$iolink_pdfFilePathExplode[0].$iolink_pdfFilePathExplodeNew;
											
											//if(!file_exists($surgerycenterPdfFileFullPath)){
												if(is_dir($pdfFolderNameInSurgerycenter)) {
													//DO NOT CREATE FOLDER AGAIN
												}else {
													mkdir($pdfFolderNameInSurgerycenter, 0777);
												}
												
												$iolinkPdfFileContent = @file_get_contents($iolinkPdfFileFullPath);
												@file_put_contents($surgerycenterPdfFileFullPath,$iolinkPdfFileContent);	
											//}											
										}
										//GET INSERT OR UPDATE
										$iolinkScanedCardAlreadyExist = $objManageData->getRowRecord('scan_upload_tbl', 'iolink_scan_consent_id', $iolinkScanConsentId);
										if($iolinkScanedCardAlreadyExist){
											$objManageData->updateRecords($iolinkArrayScanUploadRecord, 'scan_upload_tbl', 'iolink_scan_consent_id', $iolinkScanConsentId);
										}else{
											$objManageData->addRecords($iolinkArrayScanUploadRecord, 'scan_upload_tbl');		
										}
									//}
								}
							}
						}
						//END CREATE 'PATIENT-INFO AND CLINICAL AND IOL ' FOLDER FOR SCAN AND INSERT SCAN CARD FROM IOLINK	
	
						//START SET NKA STATUS TO PATIENT-CONFIRMATION ONLY IF ASC ID IS NOT ASSIGNED
						$checkAscId = imw_query("select ascId from patientconfirmation where ascId<>0 and patientConfirmationId = '".$stubTblConfirmationId."'");
						if(imw_num_rows($checkAscId)==0)
						{
							$updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".addslashes($iolink_allergiesNKDA_status)."', no_medication_status = '".addslashes($iolink_no_medication_status)."', no_medication_comments = '".addslashes($iolink_no_medication_comments)."'  where patientConfirmationId = '".$stubTblConfirmationId."'";
							$updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
							
							if($iolink_allergiesNKDA_status=='Yes') {
								//DELETE ALLERGIES IF NKA STATUS=YES
								$objManageData->delRecord('patient_allergies_tbl', 'patient_confirmation_id', $stubTblConfirmationId);
							}
							if($iolink_no_medication_status=='Yes') {
								//DELETE ALLERGIES IF NKA STATUS=YES
								$objManageData->delRecord('patient_prescription_medication_healthquest_tbl', 'confirmation_id', $stubTblConfirmationId);
							}
						}
						//END SET NKA STATUS TO PATIENT-CONFIRMATION
									
						$tbleArr = array('preophealthquestionnaire', 'consent_multiple_form', 'history_physicial_clearance');
						foreach($tbleArr as $tblename) {
							
							//START ENTERING VALUE IN PREOP-HEALTHQUESTIONNIRE CHART NOTE FROM IOLINK	
							if($tblename=="preophealthquestionnaire") {
								$getPreOpQuesDetails = $objManageData->getExtractRecord('iolink_preophealthquestionnaire', 'patient_in_waiting_id', $patient_in_waiting_id);	
								
								if(is_array($getPreOpQuesDetails)){
									extract($getPreOpQuesDetails);
									$patient_id=$patientIdExtractedTbl; //SET PATIENT-ID ALEADY IN WORKING
									
									
									//START CODE TO COPY SIGNATURE IMAGES OF PATIENT & WITNETSS FOR PREOP- HEALTH QUEST
									$patient_sign_image_path = trim($patient_sign_image_path);
									if($patient_sign_image_path) {
										$iolinkPatientSignFullPath 			= $rootServerPath.'/'.$iolinkDirectoryName.'/'.$patient_sign_image_path;
										$surgerycenterPatientSignFullPath 	= $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.$patient_sign_image_path;
										if(!file_exists($surgerycenterPatientSignFullPath)){
											if(file_exists($iolinkPatientSignFullPath)){
												@copy($iolinkPatientSignFullPath,$surgerycenterPatientSignFullPath);
											}
										}
									}
									if($witness_sign_image_path) {
										$iolinkWitnessSignFullPath 			= $rootServerPath.'/'.$iolinkDirectoryName.'/'.$witness_sign_image_path;
										$surgerycenterWitnessSignFullPath 	= $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.$witness_sign_image_path;
										if(!file_exists($surgerycenterWitnessSignFullPath)){
											if(file_exists($iolinkWitnessSignFullPath)){
												@copy($iolinkWitnessSignFullPath,$surgerycenterWitnessSignFullPath);
											}
										}
									}
									//END CODE TO COPY SIGNATURE IMAGES OF PATIENT & WITNETSS FOR PREOP - HEALTH QUEST
									
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
									//$arrayRecord['allergies_status'] 				= addslashes($iolink_allergiesNKDA_status);//now nkda will read from patientconfirmation table
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
									
									$arrayRecord['confirmation_id'] 				= $stubTblConfirmationId;
									
									$arrayRecord['heartTroubleDesc'] 				= addslashes($heartTroubleDesc);
									$arrayRecord['strokeDesc'] 						= addslashes($strokeDesc);
									$arrayRecord['HighBPDesc'] 						= addslashes($HighBPDesc);
									$arrayRecord['anticoagulationTherapyDesc'] 		= addslashes($anticoagulationTherapyDesc);
									$arrayRecord['asthmaDesc'] 						= addslashes($asthmaDesc);
									$arrayRecord['tuberculosisDesc'] 				= addslashes($tuberculosisDesc);
									$arrayRecord['diabetesDesc'] 					= addslashes($diabetesDesc);
									$arrayRecord['epilepsyDesc'] 					= addslashes($epilepsyDesc);
									$arrayRecord['restlessLegSyndromeDesc'] 		= addslashes($restlessLegSyndromeDesc);
									$arrayRecord['hepatitisDesc'] 					= addslashes($hepatitisDesc);
									$arrayRecord['kidneyDiseaseDesc'] 				= addslashes($kidneyDiseaseDesc);
									$arrayRecord['anesthesiaBadReactionDesc'] 		= addslashes($anesthesiaBadReactionDesc);
									$arrayRecord['walkerDesc'] 						= addslashes($walkerDesc);
									$arrayRecord['contactLensesDesc'] 				= addslashes($contactLensesDesc);
									$arrayRecord['autoInternalDefibrillatorDesc']	= addslashes($autoInternalDefibrillatorDesc);
									
									$arrayRecord['signWitness1Id']					= addslashes($signWitness1Id);
									$arrayRecord['signWitness1FirstName']			= addslashes($signWitness1FirstName);
									$arrayRecord['signWitness1MiddleName']			= addslashes($signWitness1MiddleName);
									$arrayRecord['signWitness1LastName']			= addslashes($signWitness1LastName);
									$arrayRecord['signWitness1Status']				= addslashes($signWitness1Status);
									$arrayRecord['signWitness1DateTime']			= addslashes($signWitness1DateTime);
									
									unset($conditionArrHealthQuest);
									$conditionArrHealthQuest['confirmation_id']	= $stubTblConfirmationId;
									$chkHealthQuestRecordExist = $objManageData->getMultiChkArrayRecords('preophealthquestionnaire', $conditionArrHealthQuest);
									if($chkHealthQuestRecordExist) {
										foreach($chkHealthQuestRecordExist as $chkHealthQuestRecord) {
											$health_form_status = $chkHealthQuestRecord->form_status;
											if($health_form_status != "completed") {//DO NOT OVERWRITE HEALTH QUEST CHART IF FLAG IS GREEN IN ASCEMR
												$objManageData->updateRecords($arrayRecord, 'preophealthquestionnaire', 'confirmation_id', $stubTblConfirmationId);
											}
										}
									}else {	
										$objManageData->addRecords($arrayRecord, 'preophealthquestionnaire');
									}					
									
									unset($leftNaviPreHealthArr);
									$leftNaviPreHealthArr['pre_op_health_ques_form'] = 'false';
									$objManageData->updateRecords($leftNaviPreHealthArr, 'left_navigation_forms', 'confirmationId', $stubTblConfirmationId);
									
								}
							}
							//END ENTERING VALUE IN PREOP-HEALTHQUESTIONNIRE CHART NOTE FROM IOLINK						
							
							//START ENTERING VALUE IN H&P CHART NOTE FROM IOLINK	
							if($tblename=="history_physicial_clearance") {
												
								$getHPDetails = $objManageData->getExtractRecord('iolink_history_physical', 'pt_waiting_id', $patient_in_waiting_id);	
								if(is_array($getHPDetails)){
									
									extract($getHPDetails);
									$patient_id=$patientIdExtractedTbl; //SET PATIENT-ID ALEADY IN WORKING
									
									unset($arrayRecord);
									$arrayRecord['confirmation_id'] = $stubTblConfirmationId;
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
									
									unset($conditionArrHistoryPhysical);
									$conditionArrHistoryPhysical['confirmation_id']	= $stubTblConfirmationId;
									$chkHPRecordExist = $objManageData->getMultiChkArrayRecords('history_physicial_clearance', $conditionArrHistoryPhysical);
									if($chkHPRecordExist) {
										foreach($chkHPRecordExist as $chkHPRecord) {
											$hp_form_status = $chkHPRecord->form_status;
											if($hp_form_status != "completed") {//DO NOT OVERWRITE H&P CHART IF FLAG IS GREEN IN ASCEMR
												$objManageData->updateRecords($arrayRecord, 'history_physicial_clearance', 'confirmation_id', $stubTblConfirmationId);
											}
										}
									}else {	
										$objManageData->addRecords($arrayRecord, 'history_physicial_clearance');
									}					
									
									unset($leftNaviHPArr);
									$leftNaviHPArr['history_physical_form'] = 'false';
									$objManageData->updateRecords($leftNaviHPArr, 'left_navigation_forms', 'confirmationId', $stubTblConfirmationId);
									
									
									unset($conditionArrHistoryPhysical);
									$conditionArrHistoryPhysical['confirmation_id']	= $stubTblConfirmationId;
									$chkHPRecordExist = $objManageData->getMultiChkArrayRecords('history_physicial_clearance', $conditionArrHistoryPhysical);
									$hp_form_status = $chkHPRecord->form_status;
									
									
									
										//START INSERTING CUSTOM QUESTIONS FOR H&P Chart
										$iolinkHPQuestionDetails = $objManageData->getArrayRecords('iolink_history_physical_ques', 'pt_waiting_id', $patient_in_waiting_id);
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
												$iolinkHPQuestionArr['source'] 		= addslashes($iolinkHPQuestionSrc);
												$iolinkHPQuestionArr['confirmation_id'] = $stubTblConfirmationId;
												$iolinkHPQuestionArr['patient_id'] = $iolinkHPQuestions->patient_id;

												$iolinkHPQuestionConditionArr['ques'] 	= addslashes($iolinkHPQuestion);
												$iolinkHPQuestionConditionArr['confirmation_id'] = $stubTblConfirmationId;
												$iolinkHPQuestionConditionArr['patient_id'] 		= $iolinkHPQuestions->patient_id;

												$iolinkHPQuestionExist = $objManageData->getMultiChkArrayRecords('history_physical_ques', $iolinkHPQuestionConditionArr);
												if($iolinkHPQuestionExist) {
													foreach($iolinkHPQuestionExist as $iolinkHPQuestionDetail){
														$iolinkHPQuestionId 	= $iolinkHPQuestionDetail->id;
														if( $hp_form_status != 'completed') {//UPDATE ONLY IF FLAG IS NOT GREEN IN SURGERYCENTER
															$objManageData->updateRecords($iolinkHPQuestionArr, 'history_physical_ques', 'id', $iolinkHPQuestionId);
														}
													}
												}else {	
													$objManageData->addRecords($iolinkHPQuestionArr, 'history_physical_ques');
												}
											}
										}	
										//END INSERTING CUSTOM QUESTIONS FOR H&P Chart
									}
							}
							//END ENTERING VALUE IN H&P CHART NOTE FROM IOLINK	
							
									
							//START ENTERING VALUE IN CONSENT FORMS FROM IOLINK
							if($tblename=="consent_multiple_form") {
								//START SAVE IOLINK SIGNATURE IMAGES IN html2pdfnew FOLDER
								$iolinkSigDataQry 			= "SELECT * from iolink_consent_form_signature 
																WHERE patient_in_waiting_id = '".$patient_in_waiting_id."'";
								$iolinkSigDataRes 			= imw_query($iolinkSigDataQry) or die(imw_error());
								$iolinkSigDataNumRow 		= imw_num_rows($iolinkSigDataRes); 
								if($iolinkSigDataNumRow>0) {
									while($iolinkSigDataRow	= imw_fetch_array($iolinkSigDataRes)) {
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
								
								$iolinkIdConsentFilledFormQry 			= "SELECT * FROM `iolink_consent_filled_form` WHERE fldPatientWaitingId='".$patient_in_waiting_id."'"; 
								$iolinkIdConsentFilledFormRes 			= imw_query($iolinkIdConsentFilledFormQry) or die(imw_error()); 
								$iolinkIdConsentFilledFormNumRow 		= imw_num_rows($iolinkIdConsentFilledFormRes);
								if($iolinkIdConsentFilledFormNumRow>0) {
									while($iolinkIdConsentFilledFormRow = imw_fetch_array($iolinkIdConsentFilledFormRes)) {
										$iolink_surgery_consent_name 	= stripslashes($iolinkIdConsentFilledFormRow['surgery_consent_name']);
										$iolink_surgery_consent_alias 	= stripslashes($iolinkIdConsentFilledFormRow['surgery_consent_alias']);
										$iolink_surgery_consent_data 	= stripslashes($iolinkIdConsentFilledFormRow['surgery_consent_data']);
										$iolink_form_status 			= $iolinkIdConsentFilledFormRow['form_status'];
										$iolink_consent_category_id 	= $iolinkIdConsentFilledFormRow['consent_category_id'];
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
										
										$get_patient_date = $iolinkIdConsentFilledFormRow['consent_save_date_time'];
										
										//START GET CONSENT CATEGORY-ID
										if(!$iolink_consent_category_id) {
											$getConsentCategoryIdDetails 	= $objManageData->getRowRecord('consent_forms_template', 'consent_id', $iolink_consent_template_id);
											if($getConsentCategoryIdDetails) {
												$iolink_consent_category_id = stripslashes($getConsentCategoryIdDetails->consent_category_id);
											}
										}	
										//END GET CONSENT CATEGORY-ID
										unset($consentRecordArr);
										$consentRecordArr['surgery_consent_name']	=	addslashes($iolink_surgery_consent_name);
										$consentRecordArr['surgery_consent_alias']	=	addslashes($iolink_surgery_consent_alias);
										$consentRecordArr['surgery_consent_data']	=	addslashes($iolink_surgery_consent_data);
										$consentRecordArr['form_status']			=	$iolink_form_status;
										$consentRecordArr['consent_category_id']	=	$iolink_consent_category_id;
										$consentRecordArr['left_navi_status']		=	'false';
										$consentRecordArr['signSurgeon1Activate']	=	$iolink_signSurgeon1Activate;
										$consentRecordArr['signSurgeon1Id']			=	$iolink_signSurgeon1Id;
										$consentRecordArr['signSurgeon1FirstName']	=	$iolink_signSurgeon1FirstName;
										$consentRecordArr['signSurgeon1MiddleName']	=	$iolink_signSurgeon1MiddleName;
										$consentRecordArr['signSurgeon1LastName']	=	$iolink_signSurgeon1LastName;
										$consentRecordArr['signSurgeon1Status']		=	$iolink_signSurgeon1Status;
										$consentRecordArr['signSurgeon1DateTime']	=	$iolink_signSurgeon1DateTime;
										
										$consentRecordArr['signNurseActivate']		=	$iolink_signNurseActivate;
										$consentRecordArr['signNurseId']			=	$iolink_signNurseId;
										$consentRecordArr['signNurseFirstName']	=	$iolink_signNurseFirstName;
										$consentRecordArr['signNurseMiddleName']	=	$iolink_signNurseMiddleName;
										$consentRecordArr['signNurseLastName']	=	$iolink_signNurseLastName;
										$consentRecordArr['signNurseStatus']		=	$iolink_signNurseStatus;
										$consentRecordArr['signNurseDateTime']	=	$iolink_signNurseDateTime;
										
										$consentRecordArr['signAnesthesia1Activate']=	$iolink_signAnesthesia1Activate;
										$consentRecordArr['signAnesthesia1Id']			=	$iolink_signAnesthesia1Id;
										$consentRecordArr['signAnesthesia1FirstName']	=	$iolink_signAnesthesia1FirstName;
										$consentRecordArr['signAnesthesia1MiddleName']	=	$iolink_signAnesthesia1MiddleName;
										$consentRecordArr['signAnesthesia1LastName']	=	$iolink_signAnesthesia1LastName;
										$consentRecordArr['signAnesthesia1Status']		=	$iolink_signAnesthesia1Status;
										$consentRecordArr['signAnesthesia1DateTime']	=	$iolink_signAnesthesia1DateTime;
										
										$consentRecordArr['signWitness1Activate']	=	$iolink_signWitness1Activate;
										$consentRecordArr['signWitness1Id']			=	$iolink_signWitness1Id;
										$consentRecordArr['signWitness1FirstName']	=	$iolink_signWitness1FirstName;
										$consentRecordArr['signWitness1MiddleName']	=	$iolink_signWitness1MiddleName;
										$consentRecordArr['signWitness1LastName']	=	$iolink_signWitness1LastName;
										$consentRecordArr['signWitness1Status']		=	$iolink_signWitness1Status;
										$consentRecordArr['signWitness1DateTime']	=	$iolink_signWitness1DateTime;
										
										$consentRecordArr['consent_template_id']	=	$iolink_consent_template_id;
										$consentRecordArr['confirmation_id']		=	$stubTblConfirmationId;
										
										unset($conditionArrConsentForm);
										$conditionArrConsentForm['confirmation_id']		=	$stubTblConfirmationId;
										$conditionArrConsentForm['consent_template_id']	=	$iolink_consent_template_id;
										$chkConsentRecordExist = $objManageData->getMultiChkArrayRecords('consent_multiple_form', $conditionArrConsentForm);
										if($chkConsentRecordExist) {
											foreach($chkConsentRecordExist as $chkConsentRecord) {
												$surgery_consent_id = $chkConsentRecord->surgery_consent_id;
												$surgery_form_status = $chkConsentRecord->form_status;
												if($surgery_form_status != "completed") {//DO NOT OVERWRITE CONSENT FORM IF FLAG IS GREEN IN ASCEMR
													$objManageData->updateRecords($consentRecordArr, 'consent_multiple_form', 'surgery_consent_id', $surgery_consent_id);
												}
											}
										}else {
											$objManageData->addRecords($consentRecordArr, 'consent_multiple_form');
										}
									}
								}	
							}
							//END ENTERING VALUE IN CONSENT FORMS FROM IOLINK	
						
						//END SYNCRONIZING RECORDS
						}
						//START SET iolinkSyncroStatus TO Syncronized IN TABLE patient_in_waiting_tbl
						$iolinkStatusDateTime = date('Y-m-d H:i:s');
						$iolinkUpdatePatientInWaitingTblQry = "UPDATE patient_in_waiting_tbl SET 
																iolinkSyncroStatus='Syncronized',
																iolinkSyncroStatusDateTime='".$iolinkStatusDateTime."'
																WHERE patient_in_waiting_id='".$patient_in_waiting_id."'";
						$iolinkUpdatePatientInWaitingTblRes = imw_query($iolinkUpdatePatientInWaitingTblQry) or die(imw_error()); 
						//END SET iolinkSyncroStatus TO Syncronized IN TABLE patient_in_waiting_tbl
						
					}
				}	
			}
		}
		if($patientIdArr) {
			$patientIdImplode = implode(",",$patientIdArr);
			//Send Patient Alert from iOlink to Surgerycenter
			if($patientIdImplode) {
				$updtPtAlertQry = "UPDATE iolink_patient_alert_tbl SET iosync_status='Syncronized' WHERE patient_id IN(".$patientIdImplode.")";
				$updtPtAlertRes = imw_query($updtPtAlertQry) or die(imw_error()); 
			}
		}
	}	
//END TO SET PATIENT RECORD FROM IOLINK TO SURGERYCENTER

?>