<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
/*
session_start();
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");

*/
$objManageData = new manageData;
//FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED
	function getFinalizeFormStatus($tablename,$pConfId) {					
		$formStatusQry = "select * from $tablename where confirmation_id = '".$pConfId."'";
		$formStatusRes = imw_query($formStatusQry) or die(imw_error());
		$formStatusRow = imw_fetch_array($formStatusRes);
		$formStatusName = $formStatusRow["form_status"];
		return $formStatusName;
	}	
	function getFinalizeAnotherFormStatus($tablename,$pConfId) {
		$formStatusQry = "select * from $tablename where patient_confirmation_id = '".$pConfId."'";
		$formStatusRes = imw_query($formStatusQry) or die(imw_error());
		$formStatusRow = imw_fetch_array($formStatusRes);
		$formStatusName = $formStatusRow["form_status"];
		return $formStatusName;
	}
//END FUNCTION TO GET STATUS OF EACH FORM  AS COMPLETED OR NOT COMPLETED


	//GET DATE FROM FIANLIZE WARNING DAYS AND FINALIZE DAYS
	
		$getFinalizeWarningDaysQry = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1'";
		$getFinalizeWarningDaysRes = imw_query($getFinalizeWarningDaysQry);
		$getFinalizeWarningNumRow = imw_num_rows($getFinalizeWarningDaysRes);
		if($getFinalizeWarningNumRow>0){
			$getFinalizeWarningDaysRow = imw_fetch_array($getFinalizeWarningDaysRes);
			$finalizeWarningDays  = $getFinalizeWarningDaysRow['finalizeWarningDays'];
			$finalizeDays = $getFinalizeWarningDaysRow['finalizeDays'];
			$finalizeWarningDate =date("Y-m-d",mktime(0, 0, 0, date('m') , date('d')-$finalizeWarningDays, date('Y')));
			//$finalizeDate =date("Y-m-d",mktime(0, 0, 0, date('m') , date('d')-$finalizeDays, date('Y')));
			
		}
	//END GET DATE FROM FINALIZE WARNING DAYS AND FINALIZE DAYS
	
	//GET AND INSERT UNFINALIZE PATIENT
	$getUnfinalizePatientQry = "SELECT * FROM patientconfirmation WHERE patientId!='' AND finalize_status='' and dos<='$finalizeWarningDate' AND unfinalizeWarningMsg!='true'";
	$getUnfinalizePatientres = imw_query($getUnfinalizePatientQry);
	$getUnfinalizePatientNumRow = imw_num_rows($getUnfinalizePatientres);
	if($getUnfinalizePatientNumRow>0){
		while($getUnfinalizePatientRow = imw_fetch_array($getUnfinalizePatientres)) {
			
			$unfinalize_surgeon_id = $getUnfinalizePatientRow['surgeonId'];
			$unfinalize_dos = $getUnfinalizePatientRow['dos'];
			//$unfinalize_msg_detail = $getUnfinalizePatientRow['msg_detail'];
			//$unfinalize_read_status = $getUnfinalizePatientRow['read_status'];
			$unfinalize_ascId = $getUnfinalizePatientRow['ascId'];
			
			
			$unfinalize_confirmation_id = $getUnfinalizePatientRow['patientConfirmationId'];
			$unfinalize_patient_id = $getUnfinalizePatientRow['patientId'];
			$unfinalize_finalize_status = $getUnfinalizePatientRow['finalize_status'];
			
			$unfinalizePatientPrimaryProcedure = $getUnfinalizePatientRow['patient_primary_procedure'];
			$unfinalizePatientSiteNumber = $getUnfinalizePatientRow['site'];
			
			//SET SITE OS/OD/OU
				if($unfinalizePatientSiteNumber=='1') {
					$unfinalizePatientSite =  'OS';
				}else if($unfinalizePatientSiteNumber=='2') {
					$unfinalizePatientSite =  'OD';
				}else if($unfinalizePatientSiteNumber=='3') {
					$unfinalizePatientSite =  'OU';
				}
			//END SITE OS/OD/OU
			
			//GET PATIENT NAME
				$unfinalizePatientNameQry = "select * from patient_data_tbl where patient_id='$unfinalize_patient_id'";
				$unfinalizePatientNameRes = imw_query($unfinalizePatientNameQry) or die(imw_error());
				$unfinalizePatientNameRow = imw_fetch_array($unfinalizePatientNameRes);
					$unfinalizePatientFirstName = $unfinalizePatientNameRow['patient_fname'];
					$unfinalizePatientMiddleName = $unfinalizePatientNameRow['patient_mname'];
					$unfinalizePatientLastName = $unfinalizePatientNameRow['patient_lname'];
					/*
					if($unfinalizePatientMiddleName) {
						$unfinalizePatientMiddleName = $unfinalizePatientMiddleName.' ';
					}
					*/
					$unfinalizePatientName = $unfinalizePatientLastName.', '.$unfinalizePatientFirstName.' '.$unfinalizePatientMiddleName;

			//END GET PATIENT NAME
			
			//GET ASCID, DOS
				$msgConfirmationInfo  = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $unfinalize_confirmation_id);
				$msgPatientAscId = $msgConfirmationInfo->ascId;
				$msgPatientDosTemp = $msgConfirmationInfo->dos;
			
				if(!$msgPatientAscId) {
					$msgPatientAscId='0';
				}
				if($unfinalize_confirmation_id) { 
					$msgPatientDisplayAscId = ' (ASC#: '.$msgPatientAscId.')';
					$msgPatientDetailDisplayAscId = '&nbsp;<a class="link_slid_right" target="_top" style="cursor:hand;" href="patient_confirm.php?pConfId='.$unfinalize_confirmation_id.'">(ASC#: '.$msgPatientAscId.')</a>'; 
				}
				if($msgPatientDosTemp) { 
					list($msgPatientDos_year,$msgPatientDos_month,$msgPatientDos_day) =explode('-',$msgPatientDosTemp);
					$msgPatientDisplayDos = $msgPatientDos_month.'-'.$msgPatientDos_day.'-'.$msgPatientDos_year;
					//$msgPatientDisplayDos = '('.$msgPatientDisplayDos.')';
				}
			//END GET ASCID, DOS
			
			//CREATE MESSAGE
			$unfinalizeSubject = 'Warning: Pt. '.$unfinalizePatientName.$msgPatientDisplayAscId.' DOS '.$msgPatientDisplayDos.' chart notes are not finalized';
			$unfinalize_msg_detail = '
				<table border="0" cellpadding="0" cellspacing="0" class="text_10" >
					<tr>
						<td align="left" class="text_10">
							To:
						</td>
						<td align="left" class="text_10">&nbsp;&nbsp;</td>
						<td align="left" class="text_10b">
							Surgeon
						</td>
					</tr>
					<tr>
						<td align="left" class="text_10" valign="top">
							Subject:
						</td>
						<td align="left" class="text_10" valign="top">&nbsp;&nbsp;</td>
						<td align="left" class="text_10b" valign="top">
							Warning: Pt. '.$unfinalizePatientName.$msgPatientDetailDisplayAscId.' DOS '.$msgPatientDisplayDos.' chart notes are not finalized
						</td>
					</tr>
					<tr>
						<td align="left" class="text_10" valign="top">
							Body&nbsp;-&nbsp;
						</td>
						<td align="left" class="text_10">&nbsp;&nbsp;</td>
						<td align="left" class="text_10b">
							If not finalized, the system will finalize the following forms in '.$finalizeDays.' days for patient '.$unfinalizePatientName.$msgPatientDetailDisplayAscId.' - '.$unfinalizePatientPrimaryProcedure.' ('.$unfinalizePatientSite.')
							<table border="0" cellpadding="0" cellspacing="0" class="text_10" width="100%">
							';
		
						//CODE TO GET FORM STATUS
							//$surgeryConsentFinalizeFormStatus = getFinalizeFormStatus("surgery_consent_form",$unfinalize_confirmation_id);
							//$HippaConsentFinalizeFormStatus = getFinalizeFormStatus("hippa_consent_form",$unfinalize_confirmation_id);
							//$BenefitConsentFinalizeFormStatus = getFinalizeFormStatus("benefit_consent_form",$unfinalize_confirmation_id);
							//$InsuranceConsentFinalizeFormStatus = getFinalizeFormStatus("insurance_consent_form",$unfinalize_confirmation_id);
							$dischargeSummaryFinalizeFormStatus = getFinalizeFormStatus("dischargesummarysheet",$unfinalize_confirmation_id);
							//$preopHealthQuestFinalizeFormStatus = getFinalizeFormStatus("preophealthquestionnaire",$unfinalize_confirmation_id);		
							$preopNursingFinalizeFormStatus = getFinalizeFormStatus("preopnursingrecord",$unfinalize_confirmation_id);
							$postopNursingFinalizeFormStatus = getFinalizeFormStatus("postopnursingrecord",$unfinalize_confirmation_id);		
							$macRegionalAnesthesiaFinalizeFormStatus = getFinalizeFormStatus("localanesthesiarecord",$unfinalize_confirmation_id);
							$preopGenralAnesthesiaFinalizeFormStatus = getFinalizeFormStatus("preopgenanesthesiarecord",$unfinalize_confirmation_id);
							$GenralAnesthesiaFinalizeFormStatus = getFinalizeFormStatus("genanesthesiarecord",$unfinalize_confirmation_id);
							$genralAnesthesiaNursesNotesFinalizeFormStatus = getFinalizeFormStatus("genanesthesianursesnotes",$unfinalize_confirmation_id);		
							$OpRoomRecordFinalizeFormStatus = getFinalizeFormStatus("operatingroomrecords",$unfinalize_confirmation_id);
							$surgicalOperativeRecordFinalizeFormStatus = getFinalizeFormStatus("operativereport",$unfinalize_confirmation_id);
							//$AmendmentsNotesFinalizeFormStatus = getFinalizeFormStatus("amendment",$unfinalize_confirmation_id);		
							$preopPhysicianFinalizeFormStatus = getFinalizeAnotherFormStatus("preopphysicianorders",$unfinalize_confirmation_id);
							$postopPhysicianFinalizeFormStatus = getFinalizeAnotherFormStatus("postopphysicianorders",$unfinalize_confirmation_id);
							//$InstructionSheetFinalizeFormStatus = getFinalizeAnotherFormStatus("patient_instruction_sheet",$unfinalize_confirmation_id);
						//END CODE TO GET FORM STATUS
		
	$msgSaveStatus = false;
	$formNameArr = array();
	if($preopPhysicianFinalizeFormStatus=='' || $preopPhysicianFinalizeFormStatus=='not completed') {
		$formNameArr[0] = 'Pre-Op Physician';
		$msgSaveStatus = true;
	}
	if($postopPhysicianFinalizeFormStatus=='' || $postopPhysicianFinalizeFormStatus=='not completed') {
		$formNameArr[1] = 'Post-Op Physician';
		$msgSaveStatus = true;
	}
	if($OpRoomRecordFinalizeFormStatus=='' || $OpRoomRecordFinalizeFormStatus=='not completed') {
		$formNameArr[2] = 'Intra-Op Record';
		$msgSaveStatus = true;
	}
	if($surgicalOperativeRecordFinalizeFormStatus=='' || $surgicalOperativeRecordFinalizeFormStatus=='not completed') {
		$formNameArr[3] = 'Operative report';
		$msgSaveStatus = true;
	}
	if($dischargeSummaryFinalizeFormStatus=='' || $dischargeSummaryFinalizeFormStatus=='not completed') {
		$formNameArr[4] = 'Discharge Summary';
		$msgSaveStatus = true;
	}		
	
	if($formNameArr) {	
		foreach($formNameArr as $formNameArrName) {
			if($formNameArrName) {
				$unfinalize_msg_detail.='
										<tr>
											<td width="30">&nbsp;</td>
											<td align="left" class="text_10">&nbsp;&nbsp;</td>
											<td>
												'.$formNameArrName.'
											</td>
										</tr>';
			}							
		}							
	}							
		$unfinalize_msg_detail.='			
							</table>
						</td>
					</tr>
				</table>';			
			
			//END CREATE MESSAGE
			$chkMsgTblQry = "select * from msg_tbl where confirmation_id='$unfinalize_confirmation_id' AND patient_id='$unfinalize_patient_id' AND finalize_status=''";
			$chkMsgTblRes = imw_query($chkMsgTblQry) or die(imw_error());
			$chkMsgTblNumRow = imw_num_rows($chkMsgTblRes);
			
			unset($arrayRecord);
			$arrayRecord['msg_user_id'] = $unfinalize_surgeon_id;
			$arrayRecord['dos'] = $unfinalize_dos;
			$arrayRecord['msg_subject'] = addslashes($unfinalizeSubject);
			$arrayRecord['msg_detail'] = addslashes($unfinalize_msg_detail);
			$arrayRecord['msg_date'] = date('Y-m-d');
			$arrayRecord['msg_time'] = date('H:i:s');
			$arrayRecord['read_status'] = '';
			$arrayRecord['asc_id'] = $unfinalize_ascId;
			$arrayRecord['confirmation_id'] = $unfinalize_confirmation_id;
			$arrayRecord['patient_id'] = $unfinalize_patient_id;
			$arrayRecord['finalize_status'] = '';
			$arrayRecord['finalize_date'] = '';
			
			if($chkMsgTblNumRow>0) {
				//DO NOT UPDATE (DO NOTHING)
			}else {
				if($msgSaveStatus==true) { //SAVE MESSAGE ONLY IF SURGERON'S FORM ARE NOT COMPLETED
					//START ADD MESSAGES FOR SURGEON
					$unfinalizedInsertId = $objManageData->addRecords($arrayRecord, 'msg_tbl');
					//END ADD MESSAGES FOR SURGEON
					
					//START ADD MESSAGES FOR THOSE USERS WHO HAVE SUPER PRIVILLIGES
					$chkUsrTblQry = "select * from users where user_privileges Like '%Super User%' AND usersId!='$unfinalize_surgeon_id'";
					$chkUsrTblRes = imw_query($chkUsrTblQry) or die(imw_error());
					$chkUsrTblNumRow = imw_num_rows($chkUsrTblRes);
					if($chkUsrTblNumRow>0) {
						while($chkUsrTblRow = imw_fetch_array($chkUsrTblRes)) {
							$chkUsrTblUserId = $chkUsrTblRow['usersId'];
							
							unset($arrayRecord);
							$arrayRecord['msg_user_id'] = $chkUsrTblUserId;
							$arrayRecord['dos'] = $unfinalize_dos;
							$arrayRecord['msg_subject'] = addslashes($unfinalizeSubject);
							$arrayRecord['msg_detail'] = addslashes($unfinalize_msg_detail);
							$arrayRecord['msg_date'] = date('Y-m-d');
							$arrayRecord['msg_time'] = date('H:i:s');
							$arrayRecord['read_status'] = '';
							$arrayRecord['asc_id'] = $unfinalize_ascId;
							$arrayRecord['confirmation_id'] = $unfinalize_confirmation_id;
							$arrayRecord['patient_id'] = $unfinalize_patient_id;
							$arrayRecord['finalize_status'] = '';
							$arrayRecord['finalize_date'] = '';
							$unfinalizedSuperInsertId = $objManageData->addRecords($arrayRecord, 'msg_tbl');
						}
					}		
					//END ADD MESSAGES FOR THOSE USERS WHO HAVE SUPER PRIVILLIGES
					
				}//END IF 'MESSAGE SAVE STATUS' = TRUE	
				
				//SET unfinalizeWarningMsg TO true IN patientconfirmation TABLE
				unset($arrayRecord);
				$arrayRecord['unfinalizeWarningMsg'] = 'true';
				$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $unfinalize_confirmation_id);	
			}
		}	
	}
	//END GET AND INSERT UNFINALIZE PATIENT
?>
