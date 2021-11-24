<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
include_once("common/commonFunctions.php");
$objManageData = new manageData;
		
//SAVE USER SIGNATURE
	$loggedInUserId = $_GET['loggedInUserId'];
	if(!$loggedInUserId) {
		$loggedInUserId = $_SESSION["loginUserId"];
	}
	$patient_id = $_REQUEST['patient_id'];
	$pConfId = $_REQUEST['pConfId'];
	$innerKey		=	$_REQUEST['innerKey'] ; 
	
//GET USER NAME
	$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
	$loggedInUserName='';
	if(imw_num_rows($ViewUserNameRes)>0) {
		$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
		
		$loggedInUserFirstName = addslashes($ViewUserNameRow["fname"]);
		$loggedInUserMiddleName = addslashes($ViewUserNameRow["mname"]);
		$loggedInUserLastName = addslashes($ViewUserNameRow["lname"]);
		
		$signOnFileStatus = 'Yes';
		$signDateTime = date("Y-m-d H:i:s");
		
		$loggedInUserName = addslashes($ViewUserNameRow["lname"]).", ".addslashes($ViewUserNameRow["fname"])." ".addslashes($ViewUserNameRow["mname"]);
	}
//END GET USER  NAME

$configurationDetail	= $objManageData->getExtractRecord('surgerycenter','surgeryCenterId','1','sx_plan_sheet_review');
$sxPlanSheetReviewAdmin	= $configurationDetail['sx_plan_sheet_review'];

//GET CONFIRMATION DETAIL
	$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  patientConfirmationId='".$_REQUEST["pConfId"]."'";
	$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
	$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
	if($getConfirmationDetailNumRow>0) {
		$getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes); 
		$confirmationAnesthesiaId = $getConfirmationDetailRow['anesthesiologist_id'];
		
		$Confirm_patientHeaderAllergiesNKDA_status = $getConfirmationDetailRow['allergiesNKDA_status'];	
		$Confirm_patientHeaderAnesthesiologist_name = stripslashes($getConfirmationDetailRow['anesthesiologist_name']);	
		$Confirm_patientHeaderAnes_NA = $getConfirmationDetailRow['anes_NA'];	
		$Confirm_patientHeaderAssist_by_translator = $getConfirmationDetailRow['assist_by_translator'];	
		$patient_primary_procedure_id = $getConfirmationDetailRow["patient_primary_procedure_id"]; 
	
		$getLaserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$patient_primary_procedure_id."' AND procedureId!='0'";
		$getLaserCatIdDetailRes = imw_query($getLaserCatIdDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
		$getLaserCatIdDetailNumRow = imw_num_rows($getLaserCatIdDetailRes);
		if($getLaserCatIdDetailNumRow>0) {
			$getLaserCatIdDetailRow = imw_fetch_array($getLaserCatIdDetailRes);
			$laserSignCatId = $getLaserCatIdDetailRow['catId'];
		}
	}
//END GET CONFIRMATION DETAIL

//CODE TO MAKE SIGNATURES OF ANESTHESIOLOGIST IN SPECIFIED CHART NOTES OF PATIENT(IN OPENED DOS)
	$AnesthesiaSignArr = array('signAnesthesia1Id', 'signAnesthesia2Id', 'signAnesthesia3Id', 'signAnesthesia4Id');
	foreach($AnesthesiaSignArr as $AnesthesiaSignArrFieldName){
		$chkAnesthesiaSignQry = "SELECT * FROM localanesthesiarecord WHERE ($AnesthesiaSignArrFieldName='0' OR $AnesthesiaSignArrFieldName='') AND confirmation_id='".$_REQUEST["pConfId"]."'";
		$chkAnesthesiaSignRes = imw_query($chkAnesthesiaSignQry) or die($chkAnesthesiaSignQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkAnesthesiaSignNumRow = imw_num_rows($chkAnesthesiaSignRes);
		if($chkAnesthesiaSignNumRow>0) {
			if($AnesthesiaSignArrFieldName=='signAnesthesia1Id') {
				$signUserId 		 = 'signAnesthesia1Id';
				$signUserFirstName	 = 'signAnesthesia1FirstName'; 
				$signUserMiddleName  = 'signAnesthesia1MiddleName';
				$signUserLastName 	 = 'signAnesthesia1LastName'; 
				$signUserStatus 	 = 'signAnesthesia1Status';
				$signUserDateTime 	 = 'signAnesthesia1DateTime';
			}else if($AnesthesiaSignArrFieldName=='signAnesthesia2Id') {
				$signUserId 		 = 'signAnesthesia2Id';
				$signUserFirstName	 = 'signAnesthesia2FirstName'; 
				$signUserMiddleName  = 'signAnesthesia2MiddleName';
				$signUserLastName 	 = 'signAnesthesia2LastName'; 
				$signUserStatus 	 = 'signAnesthesia2Status';
				$signUserDateTime 	 = 'signAnesthesia2DateTime';
			}else if($AnesthesiaSignArrFieldName=='signAnesthesia3Id') {
				$signUserId 		 = 'signAnesthesia3Id';
				$signUserFirstName	 = 'signAnesthesia3FirstName'; 
				$signUserMiddleName  = 'signAnesthesia3MiddleName';
				$signUserLastName 	 = 'signAnesthesia3LastName'; 
				$signUserStatus 	 = 'signAnesthesia3Status';
				$signUserDateTime 	 = 'signAnesthesia3DateTime';
			}else if($AnesthesiaSignArrFieldName=='signAnesthesia4Id') {
				$signUserId 		 = 'signAnesthesia4Id';
				$signUserFirstName	 = 'signAnesthesia4FirstName'; 
				$signUserMiddleName  = 'signAnesthesia4MiddleName';
				$signUserLastName 	 = 'signAnesthesia4LastName'; 
				$signUserStatus 	 = 'signAnesthesia4Status';
				$signUserDateTime 	 = 'signAnesthesia4DateTime';
			}
			
			$SaveAnesthesiaSignQry = "update localanesthesiarecord set 
										$signUserId = '$loggedInUserId',
										$signUserFirstName = '$loggedInUserFirstName', 
										$signUserMiddleName = '$loggedInUserMiddleName',
										$signUserLastName = '$loggedInUserLastName', 
										$signUserStatus = '$signOnFileStatus',
										$signUserDateTime = '$signDateTime'
										WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
					
									
			$SaveAnesthesiaSignRes = imw_query($SaveAnesthesiaSignQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		}
	}	

	//CODE TO TICK CHECK BOX OF ANESTHESIA IN OPERATING ROOM RECORD
		//START SUB-QUERY FOR SX PLANNING SHEET REVIEW BY SURGEON
		$opRoomFormStatus = $sxPlanReviewedBySurgeonChk = "";
		$sxPrQry = "SELECT form_status FROM operatingroomrecords WHERE confirmation_id='".$_REQUEST["pConfId"]."' LIMIT 0, 1 ";
		$sxPrRes = imw_query($sxPrQry) or die($sxPrQry.imw_error());
		if(imw_num_rows($sxPrRes)>0) {
			$sxPrRow = imw_fetch_assoc($sxPrRes);
			$opRoomFormStatus 			= $sxPrRow["form_status"];
		}
		$updOpRoomSubQry = "";
		if($sxPlanSheetReviewAdmin=="1" && $opRoomFormStatus == "") {
			$updOpRoomSubQry = " sxPlanReviewedBySurgeonChk = '1', ";	
		}
		//END SUB-QUERY FOR SX PLANNING SHEET REVIEW BY SURGEON

		//if($confirmationAnesthesiaId==$loggedInUserId) { 
			$verifiedbyAnesthesiologist = 'Yes';
			$updateOpRoomRecordQry = "UPDATE `operatingroomrecords` set 									
										verifiedbyAnesthesiologist = '$verifiedbyAnesthesiologist',
									 	".$updOpRoomSubQry."
										verifiedbyAnesthesiologistName = '$loggedInUserName',
										version_date_time = IF(version_num = '0', '".date("Y-m-d H:i:s")."', version_date_time),
										version_num = IF(version_num = '0', '2', version_num)
									 WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateOpRoomRecordRes = imw_query($updateOpRoomRecordQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());						 
		//}	
	//END CODE TO TICK CHECK BOX OF ANESTHESIA IN OPERATING ROOM RECORD		

	//START CODE TO MAKE SIGNATURE IN CONSENT FORM 
		$chkSignConsentQry = "SELECT * FROM consent_multiple_form WHERE (signAnesthesia1Id='0' OR signAnesthesia1Id='') AND consent_purge_status!='true' AND confirmation_id='".$_REQUEST["pConfId"]."'";
		$chkSignConsentRes = imw_query($chkSignConsentQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkSignConsentNumRow = imw_num_rows($chkSignConsentRes);
		if($chkSignConsentNumRow>0) {
			$SaveSignConsentQry = "update consent_multiple_form set 
										signAnesthesia1Id = '$loggedInUserId',
										signAnesthesia1FirstName = '$loggedInUserFirstName', 
										signAnesthesia1MiddleName = '$loggedInUserMiddleName',
										signAnesthesia1LastName = '$loggedInUserLastName', 
										signAnesthesia1Status = '$signOnFileStatus',
										signAnesthesia1DateTime = '$signDateTime'
										WHERE (signAnesthesia1Id='0' OR signAnesthesia1Id='')
										AND signAnesthesia1Activate='yes'
										AND consent_purge_status!='true'
										AND confirmation_id='".$_REQUEST["pConfId"]."' ";
			$SaveSignConsentRes = imw_query($SaveSignConsentQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		}
	//END CODE TO MAKE SIGNATURE IN CONSENT FORM	

	//START CODE TO MAKE SIGNATURE IN H & P (history & Physical) Form 
		$SaveSignHistoryPhysicalQry = "update history_physicial_clearance set 
									signAnesthesia1Id = '$loggedInUserId',
									signAnesthesia1FirstName = '$loggedInUserFirstName', 
									signAnesthesia1MiddleName = '$loggedInUserMiddleName',
									signAnesthesia1LastName = '$loggedInUserLastName', 
									signAnesthesia1Status = '$signOnFileStatus',
									signAnesthesia1DateTime = '$signDateTime'
									WHERE (signAnesthesia1Id='0' OR signAnesthesia1Id='')
									AND confirmation_id='".$_REQUEST["pConfId"]."' AND (form_status='not completed' OR form_status='completed') ";
		$SaveSignHistoryPhysicalRes = imw_query($SaveSignHistoryPhysicalQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		
		$ekgHpCount		=	$objManageData->getDirContentStatus($pConfId,2);
		if( $ekgHpCount)
		{
			$historyPhysicalFormStatus = 'completed';
			$updateHistoryPhysicalFormStatusQry = "update `history_physicial_clearance` SET form_status='$historyPhysicalFormStatus' WHERE confirmation_id='".$_REQUEST["pConfId"]."' ";
			$updateHistoryPhysicalFormStatusRes = imw_query($updateHistoryPhysicalFormStatusQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());	
		}
		else
		{
			
			$chkSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND (form_status='not completed' OR form_status='completed')";
			$chkSignHistoryPhysicalRes = imw_query($chkSignHistoryPhysicalQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
			$chkSignHistoryPhysicalNumRow = imw_num_rows($chkSignHistoryPhysicalRes);
			if($chkSignHistoryPhysicalNumRow>0) {
			$chkSignHistoryPhysicalRow = imw_fetch_array($chkSignHistoryPhysicalRes);
			$historyPhysicalFormStatus = 'completed';
			$cadMI 					= trim($chkSignHistoryPhysicalRow['cadMI']);
			$cvaTIA 				= trim($chkSignHistoryPhysicalRow['cvaTIA']);
			$htnCP 					= trim($chkSignHistoryPhysicalRow['htnCP']);
			$anticoagulationTherapy = trim($chkSignHistoryPhysicalRow['anticoagulationTherapy']);
			$respiratoryAsthma 		= trim($chkSignHistoryPhysicalRow['respiratoryAsthma']);
			$arthritis 				= trim($chkSignHistoryPhysicalRow['arthritis']);
			$diabetes 				= trim($chkSignHistoryPhysicalRow['diabetes']);
			$recreationalDrug 		= trim($chkSignHistoryPhysicalRow['recreationalDrug']);
			$giGerd 				= trim($chkSignHistoryPhysicalRow['giGerd']);
			$ocular 				= trim($chkSignHistoryPhysicalRow['ocular']);
			$kidneyDisease 			= trim($chkSignHistoryPhysicalRow['kidneyDisease']);
			$hivAutoimmune 			= trim($chkSignHistoryPhysicalRow['hivAutoimmune']);
			$historyCancer 			= trim($chkSignHistoryPhysicalRow['historyCancer']);
			$organTransplant 		= trim($chkSignHistoryPhysicalRow['organTransplant']);
			$badReaction 			= trim($chkSignHistoryPhysicalRow['badReaction']);
			$wearContactLenses 		= trim($chkSignHistoryPhysicalRow['wearContactLenses']);
			$smoking 				= trim($chkSignHistoryPhysicalRow['smoking']);
			$drinkAlcohal 			= trim($chkSignHistoryPhysicalRow['drinkAlcohal']);
			$haveAutomatic 			= trim($chkSignHistoryPhysicalRow['haveAutomatic']);
			$medicalHistoryObtained = trim($chkSignHistoryPhysicalRow['medicalHistoryObtained']);
			$signSurgeon1IdHP 		= trim($chkSignHistoryPhysicalRow['signSurgeon1Id']);
			$signAnesthesia1IdHP	= trim($chkSignHistoryPhysicalRow['signAnesthesia1Id']);
			$signNurseIdHP 			= trim($chkSignHistoryPhysicalRow['signNurseId']);
			if(($cadMI=='') || ($cvaTIA=='') 
			||($htnCP=='') || ($anticoagulationTherapy=='') 
			||($respiratoryAsthma=='') || ($arthritis=='') 
			||($diabetes=='') || ($recreationalDrug=='') 
			||($giGerd=='') || ($ocular=='') 
			||($kidneyDisease=='') || ($hivAutoimmune=='')
			||($historyCancer=='') || ($organTransplant=='') 
			||($badReaction=='') || ($wearContactLenses=='') 
			||($smoking=='') || ($drinkAlcohal=='') 
			||($haveAutomatic=='') || ($medicalHistoryObtained=='') 
			||($signSurgeon1IdHP=='0') || ($signAnesthesia1IdHP=='0')
			||($signNurseIdHP=='0')
			){
				$historyPhysicalFormStatus = 'not completed';
			}
			$updateHistoryPhysicalFormStatusQry = "update `history_physicial_clearance` SET form_status='$historyPhysicalFormStatus' WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND (form_status='not completed' OR form_status='completed')";
			$updateHistoryPhysicalFormStatusRes = imw_query($updateHistoryPhysicalFormStatusQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		}
		}
		
	//END CODE TO MAKE SIGNATURE IN H & P (history & Physical) Form 

if($laserSignCatId!=2) {//IF PROCEDURE IS NOT LASER, ONLY THEN CHANGE FORM STATUS
	//START SET FORM STATUS OF 'LOCAL ANESTHESIA RECORD'(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER			
	$localAnesRecordDetails = $objManageData->getExtractRecord('localanesthesiarecord', 'confirmation_id', $_REQUEST['pConfId']);
	if($localAnesRecordDetails){
		extract($localAnesRecordDetails);
			//START CODE TO SET VITAL SIGN GRID STATUS
			$localAnesVitalSignGridQuery = '';
			if($form_status == '') {
				$localAnesVitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($form_status,$vitalSignGridStatus,'macAnes');
				$localAnesVitalSignGridQuery	=	" , vitalSignGridStatus = '".$localAnesVitalSignGridStatus."'  ";
			}
			//END CODE TO SET VITAL SIGN GRID STATUS
		
		$localAnesRecordDetailsMedGrid = $objManageData->getExtractRecord('localanesthesiarecordmedgrid', 'confirmation_id', $_REQUEST['pConfId']);
		if($localAnesRecordDetailsMedGrid){
			$localAnesRecordDetailsMedGrid = array_map('stripslashes',$localAnesRecordDetailsMedGrid);
			extract($localAnesRecordDetailsMedGrid);
		}
		$localAnesRecordDetailsMedGridSec = $objManageData->getExtractRecord('localanesthesiarecordmedgridsec', 'confirmation_id', $_REQUEST['pConfId']);
		if($localAnesRecordDetailsMedGridSec){
			$localAnesRecordDetailsMedGridSec = array_map('stripslashes',$localAnesRecordDetailsMedGridSec);
			extract($localAnesRecordDetailsMedGridSec);
		}
		
		$localAnesFormStatus='completed';
		
		$localAnesVersionQuery	=	"";
		$localAnesVersionNum = $version_num;
		$localAnesFormStatusStored = "";
		
		if(!($localAnesVersionNum) )
		{
			if($localAnesFormStatusStored == 'completed' || $localAnesFormStatusStored == 'not completed') { $localAnesVersionNum	=	1; }
			else if($localAnesFormStatusStored <> 'completed' && $localAnesFormStatusStored <> 'not completed') { $localAnesVersionNum	=	2; }
			$localAnesVersionDateTime	=	date('Y-m-d H:i:s');
			
			$localAnesVersionQuery	=	", version_num = '".$localAnesVersionNum."', version_date_time = '".$localAnesVersionDateTime."' ";
		}
		
		if($anes_ScanUploadPath || $anes_ScanUpload) {//IF DOCUMENT IS SCANED OR UPLOADED THEN RECORD IS SAID TO BE COMPLETED.(No need to check below conditions)
			$localAnesFormStatus = 'completed';	  
		}else
		{
				//|| $procedureSecondaryVerified==''
			 if($patientInterviewed=='' || $chartNotesReviewed=='' 
		   || (!$alertOriented) 
		   || $procedurePrimaryVerified=='' 
			 || $siteVerified=='' 
		   || (constant('ANES_VITAL_SIGN_MANDATORY') <> 'OFF' && $bp=="" && $P=="" && $rr=="" && $sao=="")
		   || $evaluation2=='' 
		   || $allQuesAnswered=='' 
		   || $asaPhysicalStatus=='' 
		   || (!$startTime)
		   || ($ivCatheter==''
			   && $hand_right=='' && $hand_left==''
			   && $wrist_right=='' && $wrist_left==''
			   && $arm_right=='' && $arm_left==''
			   && $anti_right=='' && $anti_left==''
			   && trim($ivCatheterOther)==''
			  )
		   || ($topical4PercentLidocaine=='' && $Intracameral1percentLidocaine==''
			   && $Peribulbar2percentLidocaine=='' && $Retrobulbar4percentLidocaine==''
			   && $Hyalauronidase4percentLidocaine=='' && $VanLindrHalfPercentLidocaine==''	
			   && $lidEpi5ug=='' && $otherRegionalAnesthesiaWydase15u==''	&& $TopicalBlock1Block2==''
			  )
		   || ($ocular_pressure_na=='' && $none=='' && $digital=='' && $honanballon=='' && $honanBallonAnother=='' )
		   
		   || ($signAnesthesia1Id=="0" && $Confirm_patientHeaderAnes_NA!='Yes')
		   || ($signAnesthesia2Id=="0" && $Confirm_patientHeaderAnes_NA!='Yes')	
		    
	   	   || ($blank1_1	=='' && $blank1_2   =='' && $blank1_3	 =='' && $blank1_4 ==''
			   && $blank1_5	=='' && $blank1_6   =='' && $blank1_7	 =='' && $blank1_8 ==''	
			   && $blank1_9	=='' && $blank1_10  =='' && $blank1_11	 =='' && $blank1_12=='' 
			   && $blank1_13=='' && $blank1_14  ==''&& $blank1_15	 =='' && $blank1_16=='' 
			   && $blank1_17=='' && $blank1_18  ==''	&& $blank1_19=='' && $blank1_20==''	   
		   
			   && $blank2_1	=='' && $blank2_2	==''   	&& $blank2_3	=='' && $blank2_4	==''
			   && $blank2_5	=='' && $blank2_6	==''	&& $blank2_7	=='' && $blank2_8	==''	
			   && $blank2_9	=='' && $blank2_10	==''  	&& $blank2_11	=='' && $blank2_12	=='' 
			   && $blank2_13=='' && $blank2_14	=='' 	&& $blank2_15	=='' && $blank2_16	=='' 
			   && $blank2_17=='' && $blank2_18	=='' 	&& $blank2_19	=='' && $blank2_20	==''
	
			   && $blank3_1	=='' && $blank3_2	==''   	&& $blank3_3	=='' && $blank3_4	==''
			   && $blank3_5	=='' && $blank3_6	==''	&& $blank3_7	=='' && $blank3_8	==''	
			   && $blank3_9	=='' && $blank3_10	==''  	&& $blank3_11	=='' && $blank3_12	=='' 
			   && $blank3_13=='' && $blank3_14	=='' 	&& $blank3_15	=='' && $blank3_16	=='' 
			   && $blank3_17=='' && $blank3_18	=='' 	&& $blank3_19	=='' && $blank3_20	==''
			   
			   && $blank4_1	=='' && $blank4_2	==''   	&& $blank4_3	=='' && $blank4_4	==''
			   && $blank4_5	=='' && $blank4_6	==''	&& $blank4_7	=='' && $blank4_8	==''	
			   && $blank4_9	=='' && $blank4_10	==''  	&& $blank4_11	=='' && $blank4_12	=='' 
			   && $blank4_13=='' && $blank4_14	=='' 	&& $blank4_15	=='' && $blank4_16	=='' 
			   && $blank4_17=='' && $blank4_18	=='' 	&& $blank4_19	=='' && $blank4_20	==''
			   
			   && $propofol_1	=='' && $propofol_2	==''   	&& $propofol_3	=='' && $propofol_4	==''
			   && $propofol_5	=='' && $propofol_6	==''	&& $propofol_7	=='' && $propofol_8	==''	
			   && $propofol_9	=='' && $propofol_10==''  	&& $propofol_11	=='' && $propofol_12=='' 
			   && $propofol_13	=='' && $propofol_14=='' 	&& $propofol_15	=='' && $propofol_16=='' 
			   && $propofol_17	=='' && $propofol_18=='' 	&& $propofol_19	=='' && $propofol_20==''
			   
			   && $midazolam_1	=='' && $midazolam_2 ==''   && $midazolam_3	=='' && $midazolam_4 ==''
			   && $midazolam_5	=='' && $midazolam_6 ==''	&& $midazolam_7	=='' && $midazolam_8 ==''	
			   && $midazolam_9	=='' && $midazolam_10==''  	&& $midazolam_11=='' && $midazolam_12=='' 
			   && $midazolam_13	=='' && $midazolam_14=='' 	&& $midazolam_15=='' && $midazolam_16=='' 
			   && $midazolam_17	=='' && $midazolam_18=='' 	&& $midazolam_19=='' && $midazolam_20==''
			   
			   && $Fentanyl_1	=='' && $Fentanyl_2 ==''   && $Fentanyl_3  =='' && $Fentanyl_4 ==''
			   && $Fentanyl_5	=='' && $Fentanyl_6 ==''	&& $Fentanyl_7 =='' && $Fentanyl_8 ==''	
			   && $Fentanyl_9	=='' && $Fentanyl_10==''  	&& $Fentanyl_11=='' && $Fentanyl_12=='' 
			   && $Fentanyl_13	=='' && $Fentanyl_14=='' 	&& $Fentanyl_15=='' && $Fentanyl_16=='' 
			   && $Fentanyl_17	=='' && $Fentanyl_18=='' 	&& $Fentanyl_19=='' && $Fentanyl_20==''
			  
			   && $ketamine_1	=='' && $ketamine_2 ==''   && $ketamine_3  =='' && $ketamine_4 ==''
			   && $ketamine_5	=='' && $ketamine_6 ==''	&& $ketamine_7 =='' && $ketamine_8 ==''	
			   && $ketamine_9	=='' && $ketamine_10==''  	&& $ketamine_11=='' && $ketamine_12=='' 
			   && $ketamine_13	=='' && $ketamine_14=='' 	&& $ketamine_15=='' && $ketamine_16=='' 
			   && $ketamine_17	=='' && $ketamine_18=='' 	&& $ketamine_19=='' && $ketamine_20==''
	
			   && $labetalol_1	=='' && $labetalol_2 ==''   && $labetalol_3  =='' && $labetalol_4 ==''
			   && $labetalol_5	=='' && $labetalol_6 ==''	&& $labetalol_7 =='' && $labetalol_8 ==''	
			   && $labetalol_9	=='' && $labetalol_10==''  	&& $labetalol_11=='' && $labetalol_12=='' 
			   && $labetalol_13	=='' && $labetalol_14=='' 	&& $labetalol_15=='' && $labetalol_16=='' 
			   && $labetalol_17	=='' && $labetalol_18=='' 	&& $labetalol_19=='' && $labetalol_20==''
	
			   && $spo2_1	=='' && $spo2_2 ==''    && $spo2_3 =='' && $spo2_4 ==''
			   && $spo2_5	=='' && $spo2_6 ==''	&& $spo2_7 =='' && $spo2_8 ==''	
			   && $spo2_9	=='' && $spo2_10==''  	&& $spo2_11=='' && $spo2_12=='' 
			   && $spo2_13	=='' && $spo2_14=='' 	&& $spo2_15=='' && $spo2_16=='' 
			   && $spo2_17	=='' && $spo2_18=='' 	&& $spo2_19=='' && $spo2_20==''
	
			   && $o2lpm_1	=='' && $o2lpm_2 ==''   && $o2lpm_3 =='' && $o2lpm_4 ==''
			   && $o2lpm_5	=='' && $o2lpm_6 ==''	&& $o2lpm_7 =='' && $o2lpm_8 ==''	
			   && $o2lpm_9	=='' && $o2lpm_10==''  	&& $o2lpm_11=='' && $o2lpm_12=='' 
			   && $o2lpm_13	=='' && $o2lpm_14=='' 	&& $o2lpm_15=='' && $o2lpm_16=='' 
			   && $o2lpm_17	=='' && $o2lpm_18=='' 	&& $o2lpm_19=='' && $o2lpm_20==''
			   )
			   
		  )
		  {
			
			$localAnesFormStatus = 'not completed';
		  }
		  
		  //check dow we need to make these fields compulsory
			if($_SESSION['loginUserType']!='Anesthesiologist')
			{
				if(($anyKnowAnestheticComplication=='' && $stableCardiPlumFunction2=='' && $satisfactoryCondition4Discharge=='')
				 || trim($evaluation)=='' || ($signAnesthesia3Id=="0" && $Confirm_patientHeaderAnes_NA!='Yes')	)
				{
					$localAnesFormStatus = 'not completed';
				}
			}
			
			// Validate Chart if form version no. is 2 
			if($localAnesVersionNum > 1)
			{
				if(		 $confirmIPPSC_signin == '' || $siteMarked=='' 
						|| $patientAllergies == ''    || $difficultAirway == '' 
						|| $anesthesiaSafety == ''    || $allMembersTeam == '' 
						|| $riskBloodLoss == ''   
						|| ($signAnesthesia4Id=="0" && $Confirm_patientHeaderAnes_NA != 'Yes') )
				{
						$localAnesFormStatus = 'not completed';
				}
				
			}
			
		}
		  $updatelocalAnesRecordFormStatusQry = "update `localanesthesiarecord` SET form_status='$localAnesFormStatus' ".$localAnesVersionQuery.$localAnesVitalSignGridQuery." WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
		  $updatelocalAnesRecordFormStatusRes = imw_query($updatelocalAnesRecordFormStatusQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());

		  //echo "<script>top.changeChkMarkImage('".$innerKey."','".$localAnesFormStatus."');</script >";	
		
		  //START SHIFTING LEFT LINK OF 'OPERATING ROOM RECORD' TO RIGHT SLIDER 
			  $updateLeftNavigationlocalAnesRecordQry = "update `left_navigation_forms` set mac_regional_anesthesia_form = 'false' WHERE confirmationId='".$_REQUEST["pConfId"]."'";
			  $updateLeftNavigationlocalAnesRecordRes = imw_query($updateLeftNavigationlocalAnesRecordQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());		
		  //END SHIFTING LEFT LINK OF 'OPERATING ROOM RECORD TO RIGHT SLIDER
		/*******HL7- DFT GENERATION***********/
		if($localAnesFormStatus == "completed" && constant('DCS_DFT_GENERATION')==true && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham'))) {
			$chkHl7SentQry = "SELECT id,sent FROM hl7_sent WHERE sch_id='".$_REQUEST["pConfId"]."' AND msg_type = 'DFT' AND send_to ='PPMC' LIMIT 0,1 ";	
			$chkHl7SentRes = imw_query($chkHl7SentQry) or die($chkHl7SentQry.imw_error());
			$hl4_thisconfID_sent = 1;
			if($chkHl7SentRes && imw_num_rows($chkHl7SentRes)>0){
				$chkHl7SentRS = imw_fetch_assoc($chkHl7SentRes);
				$hl4_thisconfID_sent = $chkHl7SentRS['sent'];
			}
			if(imw_num_rows($chkHl7SentRes) <= 0 || $hl4_thisconfID_sent=='0') { //IF MSG NOT GENERATED THEN GENERATE DFT MSG
				$chkLocalAnesQry = "SELECT dischargeSummarySheetId FROM dischargesummarysheet WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND form_status = 'completed' ";	
				$chkLocalAnesRes = imw_query($chkLocalAnesQry) or die($chkLocalAnesQry.imw_error());
				if(imw_num_rows($chkLocalAnesRes)>0) {//IF FLAG IS GREEN FOR LOCAL ANES AND DISCHARGE SUMMARY THEN GENERATE DFT MSG
					$pConfId = $_REQUEST["pConfId"];
					include(dirname(__FILE__)."/dft_hl7_generate.php");
				}
			}
			
		}
		/*******HL7- DFT GENERATION***********/
		  
	}
	//END SET FORM STATUS OF 'LOCAL ANESTHESIA RECORD'(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER			


	//START SET FORM STATUS OF SIGNED CONSENT FORM
		$chkConsentSignSurgeonNurseQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND consent_purge_status!='true' AND (signSurgeon1Activate='yes' || signNurseActivate='yes' || signAnesthesia1Activate='yes' || signWitness1Activate='yes')";
		$chkConsentSignSurgeonNurseRes = imw_query($chkConsentSignSurgeonNurseQry) or die($chkConsentSignAllQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkConsentSignSurgeonNurseNumRow = imw_num_rows($chkConsentSignSurgeonNurseRes);
		if($chkConsentSignSurgeonNurseNumRow>0) {
			while($chkConsentSignSurgeonNurseRow = imw_fetch_array($chkConsentSignSurgeonNurseRes)){
				$chkConsentSurgeonNurseFormStatus='completed';
				$chkConsentSignSurgeryConsentId = $chkConsentSignSurgeonNurseRow['surgery_consent_id'];
				$chkConsentSignSurgeryConsentData = $chkConsentSignSurgeonNurseRow['surgery_consent_data'];
				
				$chkConsentSignSurgeon1Id = $chkConsentSignSurgeonNurseRow['signSurgeon1Id'];
				$chkConsentSignNurseId = $chkConsentSignSurgeonNurseRow['signNurseId'];
				$chkConsentSignAnesthesia1Id = $chkConsentSignSurgeonNurseRow['signAnesthesia1Id'];
				$chkConsentSignWitness1Id = $chkConsentSignSurgeonNurseRow['signWitness1Id'];
				
				$chkConsentSignSurgeon1Activate = $chkConsentSignSurgeonNurseRow['signSurgeon1Activate'];
				$chkConsentSignNurseActivate = $chkConsentSignSurgeonNurseRow['signNurseActivate'];
				$chkConsentSignAnesthesia1Activate = $chkConsentSignSurgeonNurseRow['signAnesthesia1Activate'];
				$chkConsentSignWitness1Activate = $chkConsentSignSurgeonNurseRow['signWitness1Activate'];
				
				if($chkConsentSignSurgeon1Activate=='yes') {
					if($chkConsentSignSurgeon1Id=='0' || $chkConsentSignSurgeon1Id=='') {
						$chkConsentSurgeonNurseFormStatus = "not completed";
					}	
				}
				if($chkConsentSignNurseActivate=='yes') {
					if($chkConsentSignNurseId=='0' || $chkConsentSignNurseId=='') {
						$chkConsentSurgeonNurseFormStatus = "not completed";
					}		
				}
				if($chkConsentSignAnesthesia1Activate=='yes') {
					if(($chkConsentSignAnesthesia1Id=='0' || $chkConsentSignAnesthesia1Id=='') && $Confirm_patientHeaderAnes_NA!='Yes') {
						$chkConsentSurgeonNurseFormStatus = "not completed";
					}	
				}
				if($chkConsentSignWitness1Activate=='yes') {
					if($chkConsentSignWitness1Id=='0' || $chkConsentSignWitness1Id=='') {
						$chkConsentSurgeonNurseFormStatus = "not completed";
					}	
				}
				if(strpos($chkConsentSignSurgeryConsentData, '{SIGNATURE}') !== false){ //IF PATIENT NOT SIGNED CHARTNOTE THEN-
					$chkConsentSurgeonNurseFormStatus = "not completed";
				}
				$updateConsentSurgeonNurseFormStatusQry = "update `consent_multiple_form` SET form_status='".$chkConsentSurgeonNurseFormStatus."' WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND surgery_consent_id='".$chkConsentSignSurgeryConsentId."'";
				$updateConsentSurgeonNurseFormStatusRes = imw_query($updateConsentSurgeonNurseFormStatusQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
			
			}
		}
	//END SET FORM STATUS OF SIGNED CONSENT FORM	
		
	//START SET FORM STATUS OF 'OPREATING ROOM RECORD'(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
		$opRoomRecordStatusQry = "SELECT * FROM operatingroomrecords  WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
		$opRoomRecordStatusRes = imw_query($opRoomRecordStatusQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		$opRoomRecordStatusNumRow = imw_num_rows($opRoomRecordStatusRes);
		
		//$opRoomRecordFormStatus = 'not completed';
		if($opRoomRecordStatusNumRow>0) {	
			$opRoomRecordStatusRow = imw_fetch_array($opRoomRecordStatusRes);

			//START CODE TO SET VITAL SIGN GRID STATUS
			$opRoomVitalSignGridQuery = '';
			if($opRoomRecordStatusRow['form_status'] == '') {
				$opRoomVitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($opRoomRecordStatusRow['form_status'],$opRoomRecordStatusRow['vitalSignGridStatus'],'oproom');
				$opRoomVitalSignGridQuery	=	", vitalSignGridStatus = '".$opRoomVitalSignGridStatus."'  ";
			}
			//END CODE TO SET VITAL SIGN GRID STATUS
			
			$verifiedbyNurse = addslashes($opRoomRecordStatusRow['verifiedbyNurse']);
			$verifiedbySurgeon = addslashes($opRoomRecordStatusRow['verifiedbySurgeon']);
			$verifiedbyAnesthesiologist = addslashes($opRoomRecordStatusRow['verifiedbyAnesthesiologist']);
			
			$preOpDiagnosis = $opRoomRecordStatusRow['preOpDiagnosis'];
			$postOpDiagnosis = $opRoomRecordStatusRow['postOpDiagnosis'];
			
			$operativeProcedures = $opRoomRecordStatusRow['operativeProcedures'];
			$postOpDrops = $opRoomRecordStatusRow['postOpDrops'];
			
			$product_control_na = $opRoomRecordStatusRow["product_control_na"];
			$bssValue = $opRoomRecordStatusRow['bssValue']; 
			$Epinephrine03 = $opRoomRecordStatusRow['Epinephrine03']; 
			$Vancomycin01 = $opRoomRecordStatusRow['Vancomycin01']; 
			$Vancomycin02 = $opRoomRecordStatusRow['Vancomycin02']; 
			$InfusionOtherChk = $opRoomRecordStatusRow['InfusionOtherChk']; 
			
			$Healon = $opRoomRecordStatusRow['Healon']; 
			$Occucoat = $opRoomRecordStatusRow['Occucoat']; 
			$Provisc = $opRoomRecordStatusRow['Provisc']; 
			$Miostat = $opRoomRecordStatusRow['Miostat']; 
			$HealonGV = $opRoomRecordStatusRow['HealonGV']; 
			$Discovisc = $opRoomRecordStatusRow['Discovisc']; 
			$AmviscPlus = $opRoomRecordStatusRow['AmviscPlus']; 
			$TrypanBlue = $opRoomRecordStatusRow['TrypanBlue']; 
			$Healon5 = $opRoomRecordStatusRow['Healon5']; 
			$Viscoat = $opRoomRecordStatusRow['Viscoat']; 
			$Miochol = $opRoomRecordStatusRow['Miochol']; 
			$OtherSuppliesUsed  = $opRoomRecordStatusRow['OtherSuppliesUsed']; 
			$XylocaineMPF = $opRoomRecordStatusRow['XylocaineMPF']; 
			
			$manufacture = $opRoomRecordStatusRow['manufacture']; 
			$model  = $opRoomRecordStatusRow['model']; 
			$Diopter  = $opRoomRecordStatusRow['Diopter']; 
			
			$prep_solution_na  = $opRoomRecordStatusRow['prep_solution_na']; 
			$Betadine  = $opRoomRecordStatusRow['Betadine']; 
			$Saline  = $opRoomRecordStatusRow['Saline']; 
			$Alcohol  = $opRoomRecordStatusRow['Alcohol']; 
			$Prcnt5Betadinegtts  = $opRoomRecordStatusRow['Prcnt5Betadinegtts']; 
			$prepSolutionsOther  = $opRoomRecordStatusRow['prepSolutionsOther']; 
			
			$surgeryORNumber	= $opRoomRecordStatusRow["surgeryORNumber"];
			$surgeryTimeIn		= $opRoomRecordStatusRow["surgeryTimeIn"];
			$surgeryStartTime 	= $opRoomRecordStatusRow["surgeryStartTime"];
			if($surgeryStartTime=="00:00:00" || $surgeryStartTime=="") {
				$surgeryStartTime = "";
			}else {
				$surgeryStartTime = $surgeryStartTime; 		      
			}
			$surgeryEndTime   	= $opRoomRecordStatusRow['surgeryEndTime'];  
			if($surgeryEndTime=="00:00:00" ||$surgeryEndTime=="") {
				$surgeryEndTime = "";
			}else {
				   $surgeryEndTime = $surgeryEndTime; 		      
			}
			$surgeryTimeOut   	= $opRoomRecordStatusRow['surgeryTimeOut'];  
			
			$pillow_under_knees  = $opRoomRecordStatusRow['pillow_under_knees']; 
			$head_rest  = $opRoomRecordStatusRow['head_rest']; 
			$safetyBeltApplied  = $opRoomRecordStatusRow['safetyBeltApplied']; 
			$other_position   = $opRoomRecordStatusRow['other_position']; 
			$surgeryPatientPositionOther   = $opRoomRecordStatusRow['surgeryPatientPositionOther']; 
			
			$Solumedrol   = $opRoomRecordStatusRow['Solumedrol']; 
			$Dexamethasone   = $opRoomRecordStatusRow['Dexamethasone']; 
			$Kenalog   = $opRoomRecordStatusRow['Kenalog']; 
			$Vancomycin   = $opRoomRecordStatusRow['Vancomycin']; 
			$Ancef   = $opRoomRecordStatusRow['Ancef']; 
			$Gentamicin   = $opRoomRecordStatusRow['Gentamicin']; 
			$Depomedrol   = $opRoomRecordStatusRow['Depomedrol']; 
			$postOpInjOther   = $opRoomRecordStatusRow['postOpInjOther']; 
			$patch   = $opRoomRecordStatusRow['patch']; 
			$shield   = $opRoomRecordStatusRow['shield'];  
			$nurseNotes  = $opRoomRecordStatusRow['nurseNotes'];
			
			$anesthesia_service  = $opRoomRecordStatusRow['anesthesia_service'];
			$TopicalBlock  = $opRoomRecordStatusRow['TopicalBlock'];
			$collagenShield  = $opRoomRecordStatusRow['collagenShield']; 
			
			$scrubTechId1  = $opRoomRecordStatusRow['scrubTechId1'];
			$scrubTechId2  = $opRoomRecordStatusRow['scrubTechId2'];
			
			$chk_signNurseId  = $opRoomRecordStatusRow['signNurseId']; 
			$chk_signNurse1Id  = $opRoomRecordStatusRow['signNurse1Id'];
			$chk_signAnesthesia2Id  = $opRoomRecordStatusRow['signAnesthesia2Id']; 
			$chk_iol_ScanUpload  = $opRoomRecordStatusRow['iol_ScanUpload'];
			$chk_iol_ScanUpload2  = $opRoomRecordStatusRow['iol_ScanUpload2'];
			$iol_na  = $opRoomRecordStatusRow['iol_na'];
			
			$condArray	=	array(); 
			$condArray['confirmation_id =']	=	$_REQUEST["pConfId"] ;
			$condArray['displayStatus =']		=	1 ;
			$condArray['suppChkStatus =']	=	1 ;
			$anySupplyChecked	=	$objManageData->getRowCount('operatingroomrecords_supplies',$condArray);
			
			if(($verifiedbyNurse!="" && $verifiedbySurgeon!="")
			&& ($verifiedbyAnesthesiologist!="" || $Confirm_patientHeaderAnes_NA=="Yes") 
			&& (trim($preOpDiagnosis)!="" ||trim($postOpDiagnosis)!="") 
			&& ( trim($operativeProcedures)!="" || trim($postOpDrops)!="")
			&& ($product_control_na!="" || $bssValue!="" || $Epinephrine03!="" || $Vancomycin01!="" || $Vancomycin02!="" || $InfusionOtherChk!=""
				|| $anySupplyChecked || $OtherSuppliesUsed != "" )
			&& ($manufacture != "" || $model != ""  || $Diopter != "" || $chk_iol_ScanUpload || $chk_iol_ScanUpload2 || $iol_na=="Yes")
			
			&& ($prep_solution_na != "" || $Betadine != "" || $Saline != "" || $Alcohol != "" 
				|| $Prcnt5Betadinegtts != "" || trim($prepSolutionsOther) != "")
			//&& ($surgeryStartTime != "00:00:00" || $surgeryEndTime != "00:00:00")
			&& ($surgeryORNumber != "" || $surgeryTimeIn != "" || $surgeryStartTime != "" || $surgeryEndTime != "" || $surgeryTimeOut != "" )
			&&($pillow_under_knees != "" || $head_rest != "" || $safetyBeltApplied != "" || $other_position!="" || $surgeryPatientPositionOther != "")
			
			&& ($anesthesia_service != "" || $TopicalBlock != "" 
				|| $collagenShield != "")
			
			&& ($scrubTechId1 != "" || $scrubTechId2 != "")
			&& ($chk_signNurseId<>"0" || $iol_na=="Yes")
			&& ($chk_signNurse1Id<>"0")
			//&& ($chk_signAnesthesia2Id<>"0")
			//&& ($chk_iol_ScanUpload || $chk_iol_ScanUpload2 || $iol_na=="Yes")
			)
			 {      
			  $opRoomRecordFormStatus = "completed";
			 }
			 else {
			   $opRoomRecordFormStatus = "not completed";
			 }
			  
			$updateOpRoomRecordFormStatusQry = "update `operatingroomrecords` SET form_status='$opRoomRecordFormStatus' ".$opRoomVitalSignGridQuery." WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
			$updateOpRoomRecordFormStatusRes = imw_query($updateOpRoomRecordFormStatusQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		
			//START SHIFTING LEFT LINK OF 'OPERATING ROOM RECORD' TO RIGHT SLIDER 
				$updateLeftNavigationOpRoomRecordQry = "update `left_navigation_forms` set intra_op_record_form = 'false' WHERE confirmationId='".$_REQUEST["pConfId"]."'";
				$updateLeftNavigationOpRoomRecordRes = imw_query($updateLeftNavigationOpRoomRecordQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());		
			//END SHIFTING LEFT LINK OF 'OPERATING ROOM RECORD TO RIGHT SLIDER
		
		}	
	//END SET FORM STATUS OF 'OPREATING ROOM RECORD'(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER			

}//END IF PROCEDURE IS NOT LASER		
	
	
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
	
	//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
		$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
	//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE

	//START CODE IF "A" IN SAN IS GREEN THEN ADD NARCOTICS DATA (FROM MAC/REGIONAL ANES) FOR NARCOTICS REPORT 
	if(strtolower(trim($chartSignedByAnes)) == "green") {
		$objManageData->calculate_narcotics_data($_REQUEST["pConfId"]); 	
	}
	//END CODE IF "A" IN SAN IS GREEN THEN ADD NARCOTICS DATA (FROM MAC/REGIONAL ANES) FOR NARCOTICS REPORT 

	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE

//END CODE TO MAKE SIGNATURES OF ANESTHESIOLOGIST IN SPECIFIED CHART NOTES OF PATIENT(IN OPENED DOS)

//SET ALLERGIES VALUE IN HEADER
	$patient_allergies_tblQry = "SELECT * FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '".$_REQUEST["pConfId"]."'";
	$patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
	$patient_allergies_tblNumRow = imw_num_rows($patient_allergies_tblRes);
	if($patient_allergies_tblNumRow>0) {
		$allergiesValue = 'Yes';
		while($patient_allergies_tblRow= imw_fetch_array($patient_allergies_tblRes)) {
			$chk_allergy_name = trim($patient_allergies_tblRow['allergy_name']);
			if($chk_allergy_name=='NKA' && $patient_allergies_tblNumRow==1) {
				$allergiesValue = 'NKA';
			}
		}
	
	}else if($Confirm_patientHeaderAllergiesNKDA_status=="Yes") {
		$allergiesValue = 'NKA';
	}else {
		$allergiesValue = '';
	}
?>
<script>
	var allergiesValueId = '<?php echo $allergiesValue;?>';
	if(allergiesValueId==''){
		//DO NOTHING
	}else if(allergiesValueId=='NKA') {
		//DO NOTHING
	}else {
		allergiesValueId = '<img src="images/Interface_red_image003.gif" width="17" height="15" align="middle" onclick="showAllergiesPopUpFn(<?php echo $_REQUEST["pConfId"];?>);">';
	}
	if(top.allergiesHeaderId) {
		top.allergiesHeaderId.innerHTML = allergiesValueId;
	}	
</script>
<?php	
//END SET ALLERGIES VALUE IN HEADER

//SET BASE LINE VITAL SIGN IN HEADER
	$vitalSignBp_NursingHeader = "";
	$vitalSignP_NursingHeader = "";
	$vitalSignR_NursingHeader = "";
	$vitalSignO2SAT_NursingHeader = "";
	$vitalSignTemp_NursingHeader = "";
	$vitalSignHeight_NursingHeader		=	'';
	$vitalSignWeight_NursingHeader	=	'';
	if($laserSignCatId!=2) {
		$selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingHeaderRes = imw_query($selectPreOpNursingHeaderQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		$selectPreOpNursingHeaderNumRow = imw_num_rows($selectPreOpNursingHeaderRes);
		
		if($selectPreOpNursingHeaderNumRow>0) {
			$selectPreOpNursingHeaderRow = imw_fetch_array($selectPreOpNursingHeaderRes);
			$preopnursing_vitalsign_id = $selectPreOpNursingHeaderRow["preopnursing_vitalsign_id"];
			$vitalSignHeight_NursingHeader		=	$selectPreOpNursingHeaderRow["patientHeight"]	;
			$vitalSignHeight_NursingHeader		.=	!empty($vitalSignHeight_NursingHeader)	?	'"' : '';
			$vitalSignWeight_NursingHeader	=	$selectPreOpNursingHeaderRow["patientWeight"]	;
			$vitalSignWeight_NursingHeader	.=	!empty($vitalSignWeight_NursingHeader)	?	' lbs' : '';
			$vitalSignBp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignBp"];
			$vitalSignP_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignP"];
			$vitalSignR_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignR"];
			$vitalSignO2SAT_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignO2SAT"];
			$vitalSignTemp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignTemp"];
		}
	}else{
		$selectLaserProedureHeaderQry = "SELECT * FROM `laser_procedure_patient_table` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectLaserProedureHeaderRes = imw_query($selectLaserProedureHeaderQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		$selectLaserProedureHeaderNumRow = imw_num_rows($selectLaserProedureHeaderRes);
		if($selectLaserProedureHeaderNumRow>0) {
			$selectlaserProcedureRow = imw_fetch_array($selectLaserProedureHeaderRes);
			$vitalSignBp_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignBP"];
			$vitalSignP_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignP"];
			$vitalSignR_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignR"];
			$vitalSignO2SAT_NursingHeader = 'N/A';
			$vitalSignTemp_NursingHeader = 'N/A';
			$vitalSignHeight_NursingHeader		=	'N/A';
			$vitalSignWeight_NursingHeader	=	'N/A';
		}
	}	

if($laserSignCatId==2) {
?>
	<script>
		//SET BP, P, R, TEMP, Height, Weight VALUES IN HEADER
			if(top.document.getElementById('header_BP')) {
				top.document.getElementById('header_BP').innerText='<?php echo $vitalSignBp_NursingHeader;?>';
			}
			if(top.document.getElementById('header_P')) {
				top.document.getElementById('header_P').innerText='<?php echo $vitalSignP_NursingHeader;?>';
			}
			if(top.document.getElementById('header_R')) {
				top.document.getElementById('header_R').innerText='<?php echo $vitalSignR_NursingHeader;?>';
			}
			if(top.document.getElementById('header_O2SAT')) {
				top.document.getElementById('header_O2SAT').innerText='<?php echo $vitalSignO2SAT_NursingHeader;?>';
			}
			if(top.document.getElementById('header_Temp')) {
				top.document.getElementById('header_Temp').innerText='<?php echo $vitalSignTemp_NursingHeader;?>';
			}
			if(top.document.getElementById('header_Height')) {
				top.document.getElementById('header_Height').innerText='<?php echo $vitalSignHeight_NursingHeader;?>';
			}
			if(top.document.getElementById('header_Weight')) {
				top.document.getElementById('header_Weight').innerText='<?php echo $vitalSignWeight_NursingHeader;?>';
			}
		//SET BP, P, R, TEMP, Height, Weight VALUES IN HEADER
	</script>
<?php
}
//END SET BASE LINE VITAL SIGN IN HEADER

//SET ANESTHESIOLOGIST NAME IN HEADER
	if($laserSignCatId==2) {
		$Confirm_patientHeaderAnesthesiologist_name="N/A";
	}
?>
<script>
	if(top.document.getElementById('headerAnesNameID')) {
		top.document.getElementById('headerAnesNameID').innerText='<?php echo $Confirm_patientHeaderAnesthesiologist_name;?>';
	}
</script>
<?php	
//END SET ANESTHESIOLOGIST NAME IN HEADER

//SET Assisted by Translator IN HEADER
?>
	<script>
		var assistByTransChecked=false;
		var Confirm_patientHeaderAssist_by_translator = '<?php echo $Confirm_patientHeaderAssist_by_translator;?>';
		if(Confirm_patientHeaderAssist_by_translator=='yes') { assistByTransChecked=true;}
		if(top.document.getElementById('headerAssistID')) {
			top.document.getElementById('headerAssistID').checked=assistByTransChecked;
		}
	</script>
<?php	
//END SET Assisted by Translator IN HEADER

$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";	
	?>
	
<script>
	var locationFrame = '<?php echo $pageName;?>';
	//top.mainFrame.location = locationFrame;
	top.location.reload();
	//top.setPNotesHeight();
</script>
	<table  height="450" bgcolor="#ECF1EA" cellpadding="0" cellspacing="0" border="0" align="center" width="100%"  >
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>