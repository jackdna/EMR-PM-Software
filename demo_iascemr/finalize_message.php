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
	function getFinalizeFormStatusNew($tablename,$pConfId) {					
		$formStatusQry = "select * from $tablename where confirmation_id = '".$pConfId."'";
		$formStatusRes = imw_query($formStatusQry) or die(imw_error());
		$formStatusRow = imw_fetch_array($formStatusRes);
		$formStatusName = $formStatusRow["form_status"];
		/*
		if($formStatusName<>'completed') {
			$statusUpdateQry = "update $tablename set form_status='completed' where confirmation_id = '".$pConfId."'";
			imw_query($statusUpdateQry) or die(imw_error());
		}
		*/
		return $formStatusName;
	}	
	function getFinalizeAnotherFormStatusNew($tablename,$pConfId) {
		$formStatusQry = "select * from $tablename where patient_confirmation_id = '".$pConfId."'";
		$formStatusRes = imw_query($formStatusQry) or die(imw_error());
		$formStatusRow = imw_fetch_array($formStatusRes);
		$formStatusName = $formStatusRow["form_status"];
		/*
		if($formStatusName<>'completed') {
			$statusUpdateQry = "update $tablename set form_status='completed' where patient_confirmation_id = '".$pConfId."'";
			imw_query($statusUpdateQry) or die(imw_error());
		}
		*/
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
			//$finalizeWarningDate =date("Y-m-d",mktime(0, 0, 0, date('m') , date('d')-$finalizeWarningDays, date('Y')));
			$finalizeDate =date("Y-m-d",mktime(0, 0, 0, date('m') , date('d')-$finalizeDays, date('Y')));
			
		}
	//END GET DATE FROM FINALIZE WARNING DAYS AND FINALIZE DAYS
	
	
	//GET AND INSERT UNFINALIZE PATIENT
	
	$getUnfinalizePatientQry = "SELECT * FROM patientconfirmation PC 
												LEFT JOIN ( SELECT MAX(finalize_history_id) max_id, patient_confirmation_id FROM finalize_history GROUP BY patient_confirmation_id ) FHM 
												ON (FHM.patient_confirmation_id = PC.patientConfirmationId) 
												LEFT JOIN finalize_history FH ON (FH.finalize_history_id = FHM.max_id) 
												WHERE	 PC.patientId!=''  
												AND PC.finalize_status=''  
												AND  ( 
													CASE FH.finalize_action 
													WHEN 'unfinalize'  
													THEN  DATE(FH.finalize_action_datetime) <= '".$finalizeDate."' 
													ELSE PC.dos<='".$finalizeDate."' 
													END 
												)";
	
	
	//$getUnfinalizePatientQry = "SELECT * FROM patientconfirmation WHERE patientId!='' AND finalize_status='' and dos<='$finalizeDate'";
	$getUnfinalizePatientres = imw_query($getUnfinalizePatientQry);
	$getUnfinalizePatientNumRow = imw_num_rows($getUnfinalizePatientres);
	if($getUnfinalizePatientNumRow>0){
		
		while($getUnfinalizePatientRow = imw_fetch_array($getUnfinalizePatientres)) {
			
			$unfinalize_surgeon_id = $getUnfinalizePatientRow['surgeonId'];
			$unfinalize_dos = $getUnfinalizePatientRow['dos'];
			//$unfinalize2Finalize_msg_detail = $getUnfinalizePatientRow['msg_detail'];
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
			$unfinalize2FinalizeSubject = 'Finalized: Pt. '.$unfinalizePatientName.$msgPatientDisplayAscId.' DOS '.$msgPatientDisplayDos.' chart notes were finalized by the system.';
			
			$unfinalize2Finalize_msg_detail = '
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
							Finalized: Pt. '.$unfinalizePatientName.$msgPatientDetailDisplayAscId.' DOS '.$msgPatientDisplayDos.' chart notes were finalized by the system.
						</td>
					</tr>
					<tr>
						<td align="left" class="text_10" valign="top">
							Body&nbsp;-&nbsp;
						</td>
						<td align="left" class="text_10">&nbsp;&nbsp;</td>
						<td align="left" class="text_10b">
							Following chart notes for patient '.$unfinalizePatientName.$msgPatientDetailDisplayAscId.' - '.$unfinalizePatientPrimaryProcedure.' ('.$unfinalizePatientSite.') were finalized.
							<table border="0" cellpadding="0" cellspacing="0" class="text_10" width="100%">
							';
		
						//CODE TO GET FORM STATUS
							$preopPhysicianFinalizeFormStatus = getFinalizeAnotherFormStatusNew("preopphysicianorders",$unfinalize_confirmation_id);
							$postopPhysicianFinalizeFormStatus = getFinalizeAnotherFormStatusNew("postopphysicianorders",$unfinalize_confirmation_id);
							$OpRoomRecordFinalizeFormStatus = getFinalizeFormStatusNew("operatingroomrecords",$unfinalize_confirmation_id);
							$surgicalOperativeRecordFinalizeFormStatus = getFinalizeFormStatusNew("operativereport",$unfinalize_confirmation_id);
							$dischargeSummaryFinalizeFormStatus = getFinalizeFormStatusNew("dischargesummarysheet",$unfinalize_confirmation_id);
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
						$unfinalize2Finalize_msg_detail.='
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
				$unfinalize2Finalize_msg_detail.='			
									</table>
								</td>
							</tr>
						</table>';			
					
					//END CREATE MESSAGE
			/*$chkMsgTblQry = "select * from msg_tbl where confirmation_id='$unfinalize_confirmation_id' AND patient_id='$unfinalize_patient_id' AND finalize_status='true'";
			$chkMsgTblRes = imw_query($chkMsgTblQry) or die(imw_error());
			$chkMsgTblNumRow = imw_num_rows($chkMsgTblRes);
			
			unset($arrayRecord);
			$arrayRecord['msg_user_id'] = $unfinalize_surgeon_id;
			$arrayRecord['dos'] = $unfinalize_dos;
			$arrayRecord['msg_subject'] = addslashes($unfinalize2FinalizeSubject);
			$arrayRecord['msg_detail'] = addslashes($unfinalize2Finalize_msg_detail);
			$arrayRecord['msg_date'] = date('Y-m-d');
			$arrayRecord['msg_time'] = date('H:i:s');
			$arrayRecord['read_status'] = '';
			$arrayRecord['asc_id'] = $unfinalize_ascId;
			$arrayRecord['confirmation_id'] = $unfinalize_confirmation_id;
			$arrayRecord['patient_id'] = $unfinalize_patient_id;
			$arrayRecord['finalize_status'] = 'true';
			$arrayRecord['finalize_date'] = date('Y-m-d');
			
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
							$arrayRecord['msg_subject'] = addslashes($unfinalize2FinalizeSubject);
							$arrayRecord['msg_detail'] = addslashes($unfinalize2Finalize_msg_detail);
							$arrayRecord['msg_date'] = date('Y-m-d');
							$arrayRecord['msg_time'] = date('H:i:s');
							$arrayRecord['read_status'] = '';
							$arrayRecord['asc_id'] = $unfinalize_ascId;
							$arrayRecord['confirmation_id'] = $unfinalize_confirmation_id;
							$arrayRecord['patient_id'] = $unfinalize_patient_id;
							$arrayRecord['finalize_status'] = 'true';
							$arrayRecord['finalize_date'] = date('Y-m-d');
							$unfinalizedSuperInsertId = $objManageData->addRecords($arrayRecord, 'msg_tbl');
						}
					}
					//END ADD MESSAGES FOR THOSE USERS WHO HAVE SUPER PRIVILLIGES
				}//END IF 'MESSAGE SAVE STATUS' = TRUE	
			} */
			unset($arrayRecord);
			$arrayRecord['finalize_status'] = 'true';
			//$arrayRecord['amendment_finalize_status'] = 'true';
			$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $unfinalize_confirmation_id);	
			$objManageData->calculateCost($unfinalize_confirmation_id);
			
			// Store Finalize history in finalize_history table 
					$FHistory	=	$objManageData->getRowRecord('finalize_history',  'patient_confirmation_id', $unfinalize_confirmation_id, 'finalize_history_id', 'DESC', ' count(*) as resultRows');
					
					$insertRecords		=	array();
					$insertRecords['patient_confirmation_id']		=	$unfinalize_confirmation_id;
					$insertRecords['finalize_action']						=	'finalize';
					$insertRecords['finalize_action_script	']		=	'auto';
					$insertRecords['finalize_action_type']			=	($FHistory->resultRows > 0 ) ? 'revised' : 'original' ;
					$insertRecords['finalize_action_user_id']		=	0;
					$insertRecords['finalize_action_datetime']	=	date('Y-m-d H:i:s' );
					
					$objManageData->addRecords($insertRecords, 'finalize_history');
			
			// End store finalize history
				
			//DESTROY ALL EPOST-IT
				$epostConfirmDetails = $objManageData->getRowRecord('eposted', 'patient_conf_id', $unfinalize_confirmation_id);		
				if($epostConfirmDetails) {	
					$objManageData->delRecord('eposted', 'patient_conf_id', $unfinalize_confirmation_id);	
				}	
			//END DESTROY ALL EPOST-IT
			
			//DESTROY ALL Patient Alerts
			unset($arrayRecord);
			$arrayRecord['alert_disabled'] 			= 'yes';
			$arrayRecord['alert_disabled_date_time']= date('Y-m-d H:i:s');
			$arrayRecord['alert_disabled_by'] 		= $_SESSION["loginUserId"];
			$arrayRecord['disabled_section'] 		= 'auto finalize';
			
			$objManageData->updateRecords($arrayRecord, 'iolink_patient_alert_tbl', 'patient_id', $unfinalize_patient_id);	
			//END DESTROY ALL Patient Alerts
			
			/*******HL7- DFT GENERATION***********/
			if( defined('DCS_DFT_GENERATION') && constant('DCS_DFT_GENERATION')==true && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('keywhitman','gnysx','mackool'))){
				$addHL7Bool = true;
				if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('mackool'))) {
					$sendToVal =  'KARNEY';
					$getHL7SentStatusQry = "SELECT id FROM hl7_sent WHERE sch_id = '".$unfinalize_confirmation_id."' AND msg_type = 'DFT' AND send_to ='".$sendToVal."' LIMIT 0,1 ";
					$getHL7SentStatusRes= imw_query($getHL7SentStatusQry) or die($getHL7SentStatusQry.imw_error());
					$getHL7SentStatusNumRow =imw_num_rows($getHL7SentStatusRes);
					if($getHL7SentStatusNumRow >0) { //DO NOT GENERATE DFT IF ALREADY GENERATED (IN CASE OF ALBANY-PPMC)
						$addHL7Bool == false;	
					}
				}
				if($addHL7Bool == true) {
					include(dirname(__FILE__)."/dft_hl7_generate.php");
				}
			}
			/*******DFT GENERATION END************/
			
			/*Post charges to Advanced MD, by using Advanced MD API call*/
			if( defined( 'AMD_POST_CHARGES' ) && AMD_POST_CHARGES === "YES" && (int)$unfinalize_confirmation_id > 0 )
			{
				$apptConfirmationID = $unfinalize_confirmation_id;
				$apptPatientId = $unfinalize_patient_id;
				
				$callFromSC = true;
				include(dirname(__DIR__).'/'.$iolinkDirectoryName.'/library/amd/amd_post_charges.php');
				
				if( isset($_SESSION['amd_error']) )
					unset($_SESSION['amd_error']);
				if( isset($_SESSION['amd_charge_error']) )
					unset($_SESSION['amd_charge_error']);
			}
			/*Post charges to Advanced MD, by using Advanced MD API call*/
		}	
	}
	//END GET AND INSERT UNFINALIZE PATIENT
?>
