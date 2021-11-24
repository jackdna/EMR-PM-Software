<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(500);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
		
//SAVE USER SIGNATURE
	$loggedInUserId = $_GET['loggedInUserId'];
	if(!$loggedInUserId) {
		$loggedInUserId = $_SESSION["loginUserId"];
	}
	$patient_id = $_REQUEST['patient_id'];
	$pConfId = $_REQUEST['pConfId'];
	
//GET USER NAME
	$ViewUserNameQry = "select * from `users` where  usersId = '".$loggedInUserId."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die($ViewUserNameQry.imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserFirstName = $ViewUserNameRow["fname"];
	$loggedInUserMiddleName = $ViewUserNameRow["mname"];
	$loggedInUserLastName = $ViewUserNameRow["lname"];
	
	$signOnFileStatus = 'Yes';
	$signDateTime = date("Y-m-d H:i:s");
	
	$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
//END GET USER  NAME

$configurationDetail	= $objManageData->getExtractRecord('surgerycenter','surgeryCenterId','1','sx_plan_sheet_review');
$sxPlanSheetReviewAdmin	= $configurationDetail['sx_plan_sheet_review'];


//GET CONFIRMATION DETAIL
	$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  patientConfirmationId='".$_REQUEST["pConfId"]."'";
	$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die($getConfirmationDetailQry.imw_error()); 
	$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
	if($getConfirmationDetailNumRow>0) {
		
		$getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes); 
		$confirmationSurgeonId = $getConfirmationDetailRow['surgeonId'];
		$confirmationAnesId = $getConfirmationDetailRow['anesthesiologist_id'];
		$confirmationascId = $getConfirmationDetailRow['ascId'];
		$confirmationsurgery_time = $getConfirmationDetailRow['surgery_time'];
		$confirmationdos = $getConfirmationDetailRow['dos'];
		$confirmationimport_status = $getConfirmationDetailRow['import_status'];
		if(!$patient_id) {
			$patient_id = $getConfirmationDetailRow['patientId'];
		}	
		$Confirm_patientHeaderAllergiesNKDA_status = $getConfirmationDetailRow['allergiesNKDA_status'];	
		$Confirm_patientHeaderAnesthesiologist_name = stripslashes($getConfirmationDetailRow['anesthesiologist_name']);	
		$Confirm_patientHeaderAnes_NA = $getConfirmationDetailRow['anes_NA'];	
		$Confirm_patientHeaderAssist_by_translator = $getConfirmationDetailRow['assist_by_translator'];	
		
		$Operative_patientConfirmDos = date('m-d-Y',strtotime($getConfirmationDetailRow["dos"]));
		$Operative_patientConfirmSiteTemp = $getConfirmationDetailRow["site"];
		// APPLYING NUMBERS TO PATIENT SITE
			if($Operative_patientConfirmSiteTemp == 1) {
				$Operative_patientConfirmSite = "Left Eye";  //OD
			}else if($Operative_patientConfirmSiteTemp == 2) {
				$Operative_patientConfirmSite = "Right Eye";  //OS
			}else if($Operative_patientConfirmSiteTemp == 3) { 
				$Operative_patientConfirmSite = "Both Eye";  //OU
			}
		// END APPLYING NUMBERS TO PATIENT SITE
		$patient_primary_procedure_id = $getConfirmationDetailRow["patient_primary_procedure_id"]; 
		$patient_secondary_procedure_id = $getConfirmationDetailRow["patient_secondary_procedure_id"]; 
		$patient_tertiary_procedure_id = $getConfirmationDetailRow["patient_tertiary_procedure_id"]; 
		$primary_procedure_is_inj_misc	=	$getConfirmationDetailRow['prim_proc_is_misc'];
		$Operative_patientConfirmPrimProc = $getConfirmationDetailRow["patient_primary_procedure"];
		$Operative_patientConfirmSecProc = $getConfirmationDetailRow["patient_secondary_procedure"];
		if($Operative_patientConfirmSecProc=="N/A") { $Operative_patientConfirmSecProc="";  }
	
		$getLaserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$patient_primary_procedure_id."'  AND procedureId!='0'";
		$getLaserCatIdDetailRes = imw_query($getLaserCatIdDetailQry) or die($getLaserCatIdDetailQry.imw_error()); 
		$getLaserCatIdDetailNumRow = imw_num_rows($getLaserCatIdDetailRes);
		if($getLaserCatIdDetailNumRow>0) {
			$getLaserCatIdDetailRow = imw_fetch_array($getLaserCatIdDetailRes);
			$laserSignCatId = $getLaserCatIdDetailRow['catId'];
		}
		
		if($laserSignCatId <> '2' )
		{
			if($primary_procedure_is_inj_misc == '')
			{
				//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $laserSignCatId);
				$primary_procedure_is_inj_misc		=	$objManageData->verifyProcIsInjMisc($patient_primary_procedure_id);
				//($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
			}
		}else
		{
			$primary_procedure_is_inj_misc	=	'';
		}
	
	}
//END GET CONFIRMATION DETAIL

//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
if($confirmationSurgeonId<>"" && $laserSignCatId!='2')
{
		$selectSurgeonProcedureQry = "SELECT spp.procedureId,spp.operativeTemplateId,sp.postOpDrop FROM surgeonprofileprocedure spp INNER JOIN surgeonprofile sp ON(sp.surgeonProfileId = spp.profileId AND sp.surgeonId = '".$confirmationSurgeonId."' and del_status='') WHERE spp.profileId != '0'";
		$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die($selectSurgeonProcedureQry.imw_error());
		$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
		
		if($selectSurgeonProcedureNumRow>0) 
		{
				while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes))
				{
						$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
						if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
							$templateFound = "true";
							$operativeTemplateId = $selectSurgeonProcedureRow['operativeTemplateId'];
							$patientToTakeHome 	= $selectSurgeonProcedureRow['postOpDrop'];
						}		
				}
		}	
		
		//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
		//FROM SURGEON'S DEFAULT PROFILE 
		/*if($templateFound<>"true") {
			$selectSurgeonProcedureQry = "SELECT spp.procedureId,spp.operativeTemplateId,sp.postOpDrop
											FROM surgeonprofileprocedure spp
											INNER JOIN surgeonprofile sp ON(sp.surgeonProfileId = spp.profileId AND sp.surgeonId = '".$confirmationSurgeonId."' AND sp.defaultProfile = '1')
											WHERE spp.profileId != '0'";
			$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die($selectSurgeonProcedureQry.imw_error());
			$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
			if($selectSurgeonProcedureNumRow>0) {
				$selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes);
					$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
					$operativeTemplateId = $selectSurgeonProcedureRow['operativeTemplateId'];
					$patientToTakeHome 	= $selectSurgeonProcedureRow['postOpDrop'];
			}
		}*/
		//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
		//FROM SURGEON'S DEFAULT PROFILE 
		
		// Select Operative Template From Preference Card If Surgeon Profile Not Found
		if($templateFound <> "true")
		{
			$proceduresArr	=	array($patient_primary_procedure_id,$patient_secondary_procedure_id,$patient_tertiary_procedure_id);
			foreach($proceduresArr as $procedureId)
			{
				if($procedureId)
				{		
					$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procedureId."' ";
					$procPrefCardSql		=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
					$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
					if($procPrefCardCnt > 0 )
					{
						$procPrefCardRow		=	imw_fetch_object($procPrefCardSql);
						$patientToTakeHome		= $procPrefCardRow->postOpDrop;
						$operativeTemplateId	= $procPrefCardRow->operativeTemplateId;
						
						break; 
					}
				}
			}
			
		}
		
		
		// Start Select Operative Template From Injection Profile If Procedure is Injection/Misc.
		if($primary_procedure_is_inj_misc)
		{
			$procedureDetails	=	array($patient_primary_procedure_id,$patient_secondary_procedure_id,$patient_tertiary_procedure_id);
			if(is_array($procedureDetails) && count($procedureDetails) > 0 )  
			{
				$injMiscOperativeTemplateId	=	'';
				foreach($procedureDetails as	$procedureID)
				{
					$fields	=	'operativeReportID';
					$defaultProfile	= $objManageData->injectionProfile($procedureID,$surgeonId,$fields);
					
					if($defaultProfile['profileFound'])
					{
						$injMiscOperativeTemplateId		=	$defaultProfile['data']['operativeReportID'];
						break;
					}
				}
				$operativeTemplateId	= ($injMiscOperativeTemplateId)	?	$injMiscOperativeTemplateId	: $operativeTemplateId;
				//IF Operative Template ID Exist in Injection Profile then update otherwirse remain same.
			}
			
		}
		// End Select Operative Template From Injection Profile If Procedure is Injection/Misc.
		
}



//CODE TO TICK CHECK BOX OF SURGEON IN OPERATING ROOM RECORD
	
	//START SUB-QUERY FOR SX PLANNING SHEET REVIEW BY SURGEON
	$opRoomFormStatus = $sxPlanReviewedBySurgeonChk = "";
	$sxPrQry = "SELECT form_status, sxPlanReviewedBySurgeonChk FROM operatingroomrecords WHERE confirmation_id='".$_REQUEST["pConfId"]."' LIMIT 0, 1 ";
	$sxPrRes = imw_query($sxPrQry) or die($sxPrQry.imw_error());
	if(imw_num_rows($sxPrRes)>0) {
		$sxPrRow = imw_fetch_assoc($sxPrRes);
		$opRoomFormStatus 			= $sxPrRow["form_status"];
		$sxPlanReviewedBySurgeonChk = $sxPrRow["sxPlanReviewedBySurgeonChk"];
	}
	$updOpRoomSubQry = "";
	if(($sxPlanSheetReviewAdmin=="1" && $opRoomFormStatus == "") || $sxPlanReviewedBySurgeonChk=="1") {
		$updOpRoomSubQry = " sxPlanReviewedBySurgeonChk = '1', sxPlanReviewedBySurgeon = 'Yes', sxPlanReviewedBySurgeonDateTime = IF(sxPlanReviewedBySurgeonDateTime = '0000-00-00', '".date("Y-m-d H:i:s")."', sxPlanReviewedBySurgeonDateTime),";	
	}
	//END SUB-QUERY FOR SX PLANNING SHEET REVIEW BY SURGEON
	
	if(($confirmationSurgeonId == $loggedInUserId)  && $laserSignCatId!='2' && !($primary_procedure_is_inj_misc) ) { 
		$verifiedbySurgeon = 'Yes';
		$updateOpRoomRecordQry = "UPDATE `operatingroomrecords` set 									
									verifiedbySurgeon = '$verifiedbySurgeon',
									$updOpRoomSubQry
								 	version_date_time = IF(version_num = '0', '".date("Y-m-d H:i:s")."', version_date_time),
									version_num = IF(version_num = '0', '2', version_num)
								 WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateOpRoomRecordRes = imw_query($updateOpRoomRecordQry) or die($updateOpRoomRecordQry.imw_error());						 
	}	
//END CODE TO TICK CHECK BOX OF SURGEON IN OPERATING ROOM RECORD		

//CODE TO MAKE SIGNATURES OF SURGEON IN ALL CHART NOTES OF PATIENT(IN OPENED DOS)
	$SaveSignArr = array('history_physicial_clearance', 'preopphysicianorders', 'postopphysicianorders','laser_procedure_patient_table','operativereport', 'dischargesummarysheet','transfer_followups','injection','patient_medication_reconciliation_sheet');
	$dis_sumry_filled = ''; //Initialize Discharge Summary filled Satus
	foreach($SaveSignArr as $SaveSignArrTableName){
		
		$signUserId = 'signSurgeon1Id';
		$signUserFirstName = 'signSurgeon1FirstName'; 
		$signUserMiddleName = 'signSurgeon1MiddleName';
		$signUserLastName = 'signSurgeon1LastName'; 
		$signUserStatus = 'signSurgeon1Status';
		$signUserDateTime = 'signSurgeon1DateTime';
		
		if($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders") {
			$signUserconfirmation_id = 'patient_confirmation_id';
		}else if($SaveSignArrTableName ==  "history_physicial_clearance" || $SaveSignArrTableName ==  "transfer_followups" || $SaveSignArrTableName ==  "laser_procedure_patient_table" || $SaveSignArrTableName == "operativereport" || $SaveSignArrTableName == "dischargesummarysheet" || $SaveSignArrTableName == "injection" || $SaveSignArrTableName == "patient_medication_reconciliation_sheet") {
			$signUserconfirmation_id = 'confirmation_id';
		}
		
		$chkSignQry = "SELECT * FROM $SaveSignArrTableName WHERE ($signUserId='0' OR $signUserId='') AND $signUserconfirmation_id='".$_REQUEST["pConfId"]."'";
		$chkSignRes = imw_query($chkSignQry) or die($chkSignQry.imw_error());
		$chkSignNumRow = imw_num_rows($chkSignRes);
		if($chkSignNumRow>0) {
			$chkSignRow = imw_fetch_assoc($chkSignRes);
			$surgeonLaserVerifyFields='';
			if($SaveSignArrTableName=='laser_procedure_patient_table') {
				$surgeonLaserVerifyFields=", verified_surgeon_Id = '".$loggedInUserId."',
											 verified_surgeon_Name = '".$loggedInUserName."',
											 verified_surgeon_timeout = '".$signDateTime."',
											 chk_laser_patient_examined = 'Yes',
											 chk_laser_patient_evaluated = 'Yes'";
			}
			
			// Add Surgeon Signature If H&P And Transfer & Followups charts are in use
			$saveSignFrmStatusWhere	=	'';
			if($SaveSignArrTableName ==  "history_physicial_clearance" || $SaveSignArrTableName ==  "transfer_followups" || $SaveSignArrTableName == "patient_medication_reconciliation_sheet")
			{
				$saveSignFrmStatusWhere	= " AND (form_status='not completed' OR form_status='completed') ";
			}
			// Add Surgeon Signature If H&P And Transfer & Followups charts are in use
			
			
			$SaveSignQry = "update $SaveSignArrTableName set 
										$signUserId = '$loggedInUserId',
										$signUserFirstName = '$loggedInUserFirstName', 
										$signUserMiddleName = '$loggedInUserMiddleName',
										$signUserLastName = '$loggedInUserLastName', 
										$signUserStatus = '$signOnFileStatus',
										$signUserDateTime = '$signDateTime'
										$surgeonLaserVerifyFields
										WHERE $signUserconfirmation_id='".$_REQUEST["pConfId"]."' ".$saveSignFrmStatusWhere;
			//START DO NOT MAKE SIGN ON 'laser chartnote' if procedure is not laser
			if($SaveSignArrTableName=='laser_procedure_patient_table') {
				if($laserSignCatId!='2') {
					$SaveSignQry='';
				}
			}
			//END DO NOT MAKE SIGN ON 'laser chartnote' if procedure is not laser
			
			//START DO NOT MAKE SIGN ON 'preopphysicianorders AND postopphysicianorders AND operativereport AND injection  chartnote' if procedure is laser
			if($laserSignCatId=='2') {
				if($SaveSignArrTableName=='preopphysicianorders' || $SaveSignArrTableName=='postopphysicianorders' || $SaveSignArrTableName=='operativereport' || $SaveSignArrTableName=='injection') {
					$SaveSignQry='';
				}
			}
			//END //DO NOT MAKE SIGN ON 'preopphysicianorders AND postopphysicianorders  chartnote' if procedure is laser
			
			
			//START DO NOT MAKE SIGN ON 'preopphysicianorders AND postopphysicianorders chartnote' if procedure is injection/misc
			if($laserSignCatId <> '2' && $primary_procedure_is_inj_misc ) {
				if($SaveSignArrTableName=='preopphysicianorders' || $SaveSignArrTableName=='postopphysicianorders') {
					$SaveSignQry='';
				}
			}
			//END //DO NOT MAKE SIGN ON 'preopphysicianorders AND postopphysicianorders AND preopnursingrecord  chartnote' if procedure is injection/misc
			
			
			//MAKE SIGN ON laser chartntoe if procedure is laser
			if($SaveSignQry!='') {
				$SaveSignRes = imw_query($SaveSignQry) or die($SaveSignQry.imw_error());
			}
			
		}
		
	//SET FORM STATUS(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
		$hptfFrmStatusQry="";
		$ekgHpCount	=	0	;
		if($SaveSignArrTableName=="history_physicial_clearance" ) 
		{	//CHANGE FORM STATUS IF THIS FORM IS ALREADY SAVED AT LEAST ONCE
			$ekgHpCount		=	$objManageData->getDirContentStatus($pConfId,2);
			if( !$ekgHpCount)
			{
				$hptfFrmStatusQry	= " AND (form_status='not completed' OR form_status='completed') ";
			}
		}
		else if($SaveSignArrTableName=="transfer_followups" || $SaveSignArrTableName == "patient_medication_reconciliation_sheet" ) 
		{
				$hptfFrmStatusQry	= " AND (form_status='not completed' OR form_status='completed') ";
		}
		
		$tableFormStatusQry = "SELECT * FROM $SaveSignArrTableName WHERE $signUserconfirmation_id='".$_REQUEST["pConfId"]."'".$hptfFrmStatusQry;
		$tableFormStatusRes = imw_query($tableFormStatusQry) or die($tableFormStatusQry.imw_error());
		$tableFormStatusRow = imw_fetch_array($tableFormStatusRes);
		$tableFormStatus = 'completed';
		if($SaveSignArrTableName == "history_physicial_clearance") {		
			if( !$ekgHpCount)
			{ 
				$leftNaviFieldName 		= "history_physical_form";
				$cadMI 					= trim($tableFormStatusRow['cadMI']);
				$cvaTIA 				= trim($tableFormStatusRow['cvaTIA']);
				$htnCP 					= trim($tableFormStatusRow['htnCP']);
				$anticoagulationTherapy = trim($tableFormStatusRow['anticoagulationTherapy']);
				$respiratoryAsthma 		= trim($tableFormStatusRow['respiratoryAsthma']);
				$arthritis 				= trim($tableFormStatusRow['arthritis']);
				$diabetes 				= trim($tableFormStatusRow['diabetes']);
				$recreationalDrug 		= trim($tableFormStatusRow['recreationalDrug']);
				$giGerd 				= trim($tableFormStatusRow['giGerd']);
				$ocular 				= trim($tableFormStatusRow['ocular']);
				$kidneyDisease 			= trim($tableFormStatusRow['kidneyDisease']);
				$hivAutoimmune 			= trim($tableFormStatusRow['hivAutoimmune']);
				$historyCancer 			= trim($tableFormStatusRow['historyCancer']);
				$organTransplant 		= trim($tableFormStatusRow['organTransplant']);
				$badReaction 			= trim($tableFormStatusRow['badReaction']);
				$wearContactLenses 		= trim($tableFormStatusRow['wearContactLenses']);
				$smoking 				= trim($tableFormStatusRow['smoking']);
				$drinkAlcohal 			= trim($tableFormStatusRow['drinkAlcohal']);
				$haveAutomatic 			= trim($tableFormStatusRow['haveAutomatic']);
				$medicalHistoryObtained = trim($tableFormStatusRow['medicalHistoryObtained']);
				$signSurgeon1IdHP 		= trim($tableFormStatusRow['signSurgeon1Id']);
				$signAnesthesia1IdHP	= trim($tableFormStatusRow['signAnesthesia1Id']);
				$signNurseIdHP 			= trim($tableFormStatusRow['signNurseId']);
				
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
					$tableFormStatus = 'not completed';
				}
			}
			
		}
		else if($SaveSignArrTableName == "preopphysicianorders" && $laserSignCatId!='2' && !$primary_procedure_is_inj_misc) {		
			$leftNaviFieldName = "pre_op_physician_order_form";
			
			$chkPreOpPhysicianFormStatus 			= $tableFormStatusRow['form_status'];
			$chkPreOpPhysicianVersionNum 			= $tableFormStatusRow['version_num'];
			$chkPreOpPhysicianVersionDateTime = $tableFormStatusRow['version_date_time'];
			$version_num	=	$chkPreOpPhysicianVersionNum;
			
			// Check & update Form's Version if form not saved once.
			if(!$chkPreOpPhysicianVersionNum)
			{
				$version_date_time	=	$chkPreOpPhysicianVersionDateTime;
				if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
				{
					$version_date_time	=	date('Y-m-d H:i:s');
				}
						
				if($chkPreOpPhysicianFormStatus == 'completed' || $chkPreOpPhysicianFormStatus == 'not completed'){
					$version_num = 1;
				}else{
					$version_num	=	3;
				}
				$updtQry	=	"Update ".$SaveSignArrTableName." Set version_num = '".$version_num."', version_date_time = '".$version_date_time."' Where $signUserconfirmation_id='".$_REQUEST["pConfId"]."' ";
				imw_query($updtQry);
			}
			//End Check & update Form's Version if form not saved once.
			
			
			$signNurseIdPreOpPhy 		= $tableFormStatusRow['signNurseId'];
			$signNurse1IdPreOpPhy 	= $tableFormStatusRow['signNurse1Id'];
			$signSurgeon1IdPreOpPhy = $tableFormStatusRow['signSurgeon1Id'];
			$notedByNurse = $tableFormStatusRow['notedByNurse'];
			
			if($signSurgeon1IdPreOpPhy =="0" || $signNurse1IdPreOpPhy == "0" || $notedByNurse==0){	
				$tableFormStatus = 'not completed';
			}
			
			// Start Validate chart if form is on version no. 2 or greater and surgeon signature not on chart
			if($version_num == 1 && $tableFormStatus == 'completed' && $signNurseIdPreOpPhy == "0")
			{
				$tableFormStatus = 'not completed';
			}
			// End Validate chart if form is on version no. 2 or greater and surgeon signature not on chart
			
		
		}
		else if($SaveSignArrTableName == "postopphysicianorders" && $laserSignCatId!='2' && !$primary_procedure_is_inj_misc ) {		
			$leftNaviFieldName = "post_op_physician_order_form";
			
			$chkPostOpPhysicianFormStatus 			= $tableFormStatusRow['form_status'];
			$chkPostOpPhysicianVersionNum 			= $tableFormStatusRow['version_num'];
			$chkPostOpPhysicianVersionDateTime = $tableFormStatusRow['version_date_time'];
			$version_num	=	$chkPostOpPhysicianVersionNum;
			
			// Check & update Form's Version if form not saved once.
			if(!$chkPostOpPhysicianVersionNum)
			{
				$version_date_time	=	$chkPostOpPhysicianVersionDateTime;
				if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
				{
					$version_date_time	=	date('Y-m-d H:i:s');
				}
						
				if($chkPostOpPhysicianFormStatus == 'completed' || $chkPostOpPhysicianFormStatus == 'not completed'){
					$version_num = 1;
				}else{
					$version_num	=	3;
				}
				$updtQry	=	"Update ".$SaveSignArrTableName." Set version_num = '".$version_num."', version_date_time = '".$version_date_time."' Where $signUserconfirmation_id='".$_REQUEST["pConfId"]."' ";
				imw_query($updtQry);
			}
			//End Check & update Form's Version if form not saved once.
			
			
			//START CODE TO PREFILL POST OP DROP IN POST_OP_PHYSICIAN ORDER FROM SURGEON PROFILE
			$postOpDropExist = "";
			if(trim($patientToTakeHome) && trim($chkPostOpPhysicianFormStatus)=="") {
				$pOrderData	=	explode(',',str_replace("\n",",",$patientToTakeHome));
				foreach($pOrderData as $pOrderRow) {
					$dataArray	=	array();
					$dataArray['confirmation_id']		=	$_REQUEST["pConfId"] ;
					$dataArray['chartName']				=	'post_op_physician_order_form';
					$dataArray['physician_order_name']	=	trim(addslashes($pOrderRow));
					$chkRecords	=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$dataArray);
					if(!$chkRecords)  {
						$savePostOpDropQry = "INSERT INTO patient_physician_orders SET 
													confirmation_id = '".$_REQUEST["pConfId"]."',
													chartName = 'post_op_physician_order_form',
													physician_order_name = '".addslashes($pOrderRow)."',
													physician_order_location	= 'sign_all_surgeon',
													physician_order_type	= 'medication',
													physician_order_date_time 	= '".date("Y-m-d H:i:s")."'
													";	
						$savePostOpDropRes = imw_query($savePostOpDropQry) or die($savePostOpDropQry.imw_error()); 						  
					}
				}
			}
			$chkPostOpDropQry = "SELECT recordId,physician_order_time FROM patient_physician_orders 
								 WHERE confirmation_id = '".$_REQUEST["pConfId"]."' 
								 AND chartName = 'post_op_physician_order_form'";
			$chkPostOpDropRes = imw_query($chkPostOpDropQry) or die($chkPostOpDropQry.imw_error()); 
			if(imw_num_rows($chkPostOpDropRes)>0)	 {
				$postOpDropExist = "Yes";
				while($chkPostOpDropRow = imw_fetch_assoc($chkPostOpDropRes)) {
					$physician_order_time = $chkPostOpDropRow["physician_order_time"];
					if($physician_order_time=='00:00:00' || $physician_order_time=='0') {
						//$postOpDropExist = "";			
					}
				}
			}
			//END CODE TO PREFILL POST OP DROP IN POST_OP_PHYSICIAN ORDER FROM SURGEON PROFILE
			
			
			$patientAssessed = $tableFormStatusRow['patientAssessed'];
			$vitalSignStable = $tableFormStatusRow['vitalSignStable'];
			$postOpEvalDone = $tableFormStatusRow['postOpEvalDone'];
			$postOpNotedByNurse  = $tableFormStatusRow['notedByNurse'];
			$postOpInstructionMethodWritten = $tableFormStatusRow['postOpInstructionMethodWritten'];
			$postOpInstructionMethodVerbal = $tableFormStatusRow['postOpInstructionMethodVerbal'];
			$patientAccompaniedSafely = $tableFormStatusRow['patientAccompaniedSafely'];
			$signNurseIdPostOpPhy = $tableFormStatusRow['signNurseId'];
			$signNurseId1PostOpPhy = $tableFormStatusRow['signNurse1Id'];
			$signSurgeon1IdPostOpPhy = $tableFormStatusRow['signSurgeon1Id'];
			
			if(($patientAssessed!='Yes') 
			||($vitalSignStable!='Yes') 
			||($postOpEvalDone!='Yes') 
			||($postOpInstructionMethodWritten!='Yes') 
			||($postOpInstructionMethodVerbal!='Yes') 
			||($patientAccompaniedSafely!='Yes') 
			||($signNurseIdPostOpPhy=="0") || ($signSurgeon1IdPostOpPhy=="0")
			||($signNurseId1PostOpPhy == "0" && $version_num >2)
			||($postOpNotedByNurse == "0" && $version_num >2)
			||($postOpDropExist!='Yes')
			){
				
				$tableFormStatus = 'not completed';
			}
			
		}
		else if($SaveSignArrTableName == "operativereport") {		
			$leftNaviFieldName = "surgical_operative_record_form";
		}
		else if($SaveSignArrTableName == "dischargesummarysheet") {
			$leftNaviFieldName = "discharge_summary_form";
				
			$signSurgeon1IdDischargeSummary = $tableFormStatusRow['signSurgeon1Id'];
			$procedures_name = $tableFormStatusRow['procedures_name'];
			$procedures_code = $tableFormStatusRow['procedures_code'];
			$diag_ids = $tableFormStatusRow['diag_ids'];
			$icd10_code = $tableFormStatusRow['icd10_code'];
			$disAttached = $tableFormStatusRow['disAttached'];	
			$otherMiscellaneous 	 = stripslashes($tableFormStatusRow['otherMiscellaneous']);
			$commentDischargeSummary = stripslashes($tableFormStatusRow['comment']);
			$dis_ScanUpload 		 = $tableFormStatusRow['dis_ScanUpload'];
			$dis_ScanUpload2 		 = $tableFormStatusRow['dis_ScanUpload2'];
			$other1DischargeSummary  = stripslashes($tableFormStatusRow['other1']);
			$other2DischargeSummary  = stripslashes($tableFormStatusRow['other2']);
			//if(!$signSurgeon1IdDischargeSummary || )
			if(($procedures_code=='' && $otherMiscellaneous=='' && $commentDischargeSummary=='' && $other1DischargeSummary=='' && $other2DischargeSummary=='' && ($disAttached=='' || (!$dis_ScanUpload && !$dis_ScanUpload2)))){
				$tableFormStatus  = 'not completed';
				$dis_sumry_filled = 'no';
			}
			if(trim($confirmationascId) && $tableFormStatus=="completed" && $confirmationimport_status=="false") {
				unset($conditionArr);
				$conditionArr['usersId'] = $confirmationSurgeonId;
				$ascId = $confirmationascId;
				$surgeryTime = $confirmationsurgery_time;
				$dos = $confirmationdos;
				$import_status = $confirmationimport_status;
				$proc_code = $procedures_code;
				$diag_ids = $diag_ids;
				$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
				if(is_array($surgeonsDetails)){
					foreach($surgeonsDetails as $usersDetail){
						$signatureOfSurgeon  = $usersDetail->signature;
						$fname_Surgeon  = $usersDetail->fname;
						$mname_Surgeon  = $usersDetail->mname;
						$lname_Surgeon  = $usersDetail->lname;
						$npi_Surgeon  = trim($usersDetail->npi);
						$nameOfSurgeon  = $lname_Surgeon.", ".$fname_Surgeon." ".$mname_Surgeon;
					}
				}
				//START GET ASSIGNED ANES DETAIL
				unset($conditionArr);
				$conditionArr['usersId'] = $confirmationAnesId;
				$anesDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
				if(is_array($anesDetails)){
					foreach($anesDetails as $usersDetail){
						$signatureOfAnes= $usersDetail->signature;
						$fname_Anes  	= $usersDetail->fname;
						$mname_Anes  	= $usersDetail->mname;
						$lname_Anes  	= $usersDetail->lname;
						$npi_Anes  	 	= trim($usersDetail->npi);
						$nameOfAnes  	= $lname_Anes.", ".$fname_Anes." ".$mname_Anes;
					}
				}
				//END GET ASSIGNED ANES DETAIL
				
				$form_status = 	$tableFormStatus;
				if(constant('STOP_SYNC_SUPERBILL') != "YES") {
					include_once("sync_superbill.php");
				}
				unset($conditionArr);
			}
			
		}
		else if($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId=='2') {		
			$leftNaviFieldName = "laser_procedure_form";
			
			$chk_laser_chief_complaint =$tableFormStatusRow['chk_laser_chief_complaint'];
			$laser_chief_complaint=$tableFormStatusRow['laser_chief_complaint'];
			
			$chk_laser_past_med_hx =$tableFormStatusRow['chk_laser_past_med_hx'];
			$laser_past_med_hx=$tableFormStatusRow['laser_past_med_hx'];
			
			$chk_laser_present_illness_hx =$tableFormStatusRow['chk_laser_present_illness_hx'];
			$laser_present_illness_hx=$tableFormStatusRow['laser_present_illness_hx'];
			
			$chk_laser_medication =$tableFormStatusRow['chk_laser_medication'];
			$laser_medication=$tableFormStatusRow['laser_medication'];		
			
			$allergies_status_reviewed=$tableFormStatusRow['allergies_status_reviewed'];
			
			$verified_nurse_name =$tableFormStatusRow['verified_nurse_name'];	
			$verified_surgeon_Name =$tableFormStatusRow['verified_surgeon_Name'];		 			  
			
			$best_correction_vision_R=$tableFormStatusRow['best_correction_vision_R'];
			$best_correction_vision_L=$tableFormStatusRow['best_correction_vision_L'];
			$laser_sle=$tableFormStatusRow['laser_sle'];
			$laser_mental_state=$tableFormStatusRow['laser_mental_state'];
			$pre_laser_IOP_R=$tableFormStatusRow['pre_laser_IOP_R'];
			$pre_laser_IOP_L=$tableFormStatusRow['pre_laser_IOP_L'];
			$pre_iop_na=$tableFormStatusRow['pre_iop_na'];
			$laser_fundus_exam=$tableFormStatusRow['laser_fundus_exam'];
			$laser_comments=$tableFormStatusRow['laser_comments'];
			$laser_other=$tableFormStatusRow['laser_other'];
			
			
			$chk_laser_pre_op_diagnosis=$tableFormStatusRow['chk_laser_pre_op_diagnosis'];
			$pre_op_diagnosis=$tableFormStatusRow['pre_op_diagnosis'];
			$laser_other_pre_medication=$tableFormStatusRow['laser_other_pre_medication'];
			
			$prelaserVitalSignBP=$tableFormStatusRow['prelaserVitalSignBP'];
			$prelaserVitalSignP=$tableFormStatusRow['prelaserVitalSignP'];
			$prelaserVitalSignR=$tableFormStatusRow['prelaserVitalSignR'];
			$laser_spot_duration=$tableFormStatusRow['laser_spot_duration'];
			$laser_spot_size=$tableFormStatusRow['laser_spot_size'];
			$laser_power=$tableFormStatusRow['laser_power'];
			$laser_shots=$tableFormStatusRow['laser_shots'];
			$laser_total_energy=$tableFormStatusRow['laser_total_energy'];
			$laser_degree_of_opening=$tableFormStatusRow['laser_degree_of_opening'];
			$laser_exposure=$tableFormStatusRow['laser_exposure'];
			$laser_count=$tableFormStatusRow['laser_count'];
			$postlaserVitalSignBP=$tableFormStatusRow['postlaserVitalSignBP'];
			$postlaserVitalSignP=$tableFormStatusRow['postlaserVitalSignP'];
			$postlaserVitalSignR=$tableFormStatusRow['postlaserVitalSignR'];
			$post_op_operative_comment=$tableFormStatusRow['post_op_operative_comment'];
			$laser_post_progress=$tableFormStatusRow['laser_post_progress'];
			$laser_post_operative=$tableFormStatusRow['laser_post_operative'];
			$iop_pressure_l=$tableFormStatusRow['iop_pressure_l'];//
			$iop_pressure_r=$tableFormStatusRow['iop_pressure_r'];//
			
			$iop_na=$tableFormStatusRow['iop_na'];			
			
			$signNurseIdlaser_procedure = $tableFormStatusRow['signNurseId'];
			$signSurgeon1Idlaser_procedure = $tableFormStatusRow['signSurgeon1Id'];
			
			
			if(  (($laser_chief_complaint =='') && ($chk_laser_chief_complaint=='on') &&
			      ($laser_past_med_hx == '' ) && ($chk_laser_past_med_hx=='on') &&
				  ($laser_present_illness_hx == '') &&  ($chk_laser_present_illness_hx=='on') &&
				  ($laser_medication == '') && ($chk_laser_medication=='on')
				  )||
				   ($verified_nurse_name=='' || $verified_surgeon_Name==''
				 ) ||
				/*
				(($laser_sle == '') && ($laser_mental_state == '') &&
				 ($laser_fundus_exam == '') && ($laser_other == '')
				) ||  
				*/
				(($pre_op_diagnosis=='') && ($chk_laser_pre_op_diagnosis=='on')
				) ||
				(($prelaserVitalSignBP =='') && ($prelaserVitalSignP == '' ) &&
				 ($prelaserVitalSignR == '') && ($laser_spot_duration == '') &&
				 ($laser_spot_size == '') && ($laser_power == '' ) &&
				 ($laser_shots == '') && ($laser_total_energy == '') &&
				 ($laser_degree_of_opening == '') && ($laser_exposure == '' ) &&
				 ($laser_count == '') && ($postlaserVitalSignBP == '') &&
				 ($postlaserVitalSignP == '') && ($postlaserVitalSignR == '') &&
				 ($laser_post_progress == '') //&&
				 //($laser_post_operative== '')
				) ||
				(($pre_laser_IOP_R == '') && ($pre_laser_IOP_L == '' ) && ($pre_iop_na == '' )
				)||
				(($iop_pressure_l == '') && ($iop_pressure_r == '') && ($iop_na == '')
				)||
				($signNurseIdlaser_procedure=="0") || ($signSurgeon1Idlaser_procedure=="0")
			  )		
			{
				$tableFormStatus = 'not completed';
			}
			
			
			
		}
		else if($SaveSignArrTableName == "injection" && $laserSignCatId <> '2' && $primary_procedure_is_inj_misc) {		
			// Start Prefilling If Procedure is injection
			if($tableFormStatusRow['form_status'] <> 'completed' && $tableFormStatusRow['form_status'] <> 'not completed')
			{
					//Get Default Profile
					$fields	=	'timeoutReq,preOpMeds,intravitrealMeds,postOpMeds';
					//$defaultProfile	= $objManageData->injectionProfile($patient_primary_procedure_id,$assignedSurgeonId,$fields);
					if($defaultProfile['profileFound'])
					{
						$timeoutReq_injection	=	$defaultProfile['data']['timeoutReq'];
						$preOpMeds_injection	=	$defaultProfile['data']['preOpMeds'];
						$intravitrealMeds_injection	=	$defaultProfile['data']['intravitrealMeds'];
						$postOpMeds_injection	=	$defaultProfile['data']['postOpMeds'];
						
						$prefillInjectionQry	=	"Update injection Set timeoutReq = '".$timeoutReq_injection."', preOpMeds = '".$preOpMeds_injection."', intravitrealMeds = '".$intravitrealMeds_injection."', postOpMeds = '".$postOpMeds_injection."' Where $signUserconfirmation_id='".$_REQUEST["pConfId"]."' ";
						
						imw_query($prefillInjectionQry);
						
						$tableFormStatusRow['timeoutReq']	=	$timeoutReq_injection;
					}
			
			}
			// End Prefilling If Procedure is injection
			
			$leftNaviFieldName = "injection_misc_form";
			
			$preVitalTime =	$tableFormStatusRow['preVitalTime'];
			$preVitalBp =	$tableFormStatusRow['preVitalBp'];
			$preVitalPulse =	$tableFormStatusRow['preVitalPulse'];
			$preVitalResp =	$tableFormStatusRow['preVitalResp'];
			$preVitalSpo =	$tableFormStatusRow['preVitalSpo'];
			
			$postVitalTime =	$tableFormStatusRow['postVitalTime'];
			$postVitalBp =	$tableFormStatusRow['postVitalBp'];
			$postVitalPulse =	$tableFormStatusRow['postVitalPulse'];
			$postVitalResp =	$tableFormStatusRow['postVitalResp'];
			$postVitalSpo =	$tableFormStatusRow['postVitalSpo'];
			
			$complications =	$tableFormStatusRow['complications'];
			$comments =	$tableFormStatusRow['comments'];
			$startTime =	$tableFormStatusRow['startTime'];
			
			$endTime =	$tableFormStatusRow['endTime'];
			$chkConsentSigned =	$tableFormStatusRow['chkConsentSigned'];
			$procedureComments =	$tableFormStatusRow['procedureComments'];
			$postIop =	$tableFormStatusRow['postIop'];
			$postIopSite =	$tableFormStatusRow['postIopSite'];
			$postIopTime =	$tableFormStatusRow['postIopTime'];
			
			$timeoutReq =	$tableFormStatusRow['timeoutReq'];
			$timeoutTime =	$tableFormStatusRow['timeoutTime'];
			$timeoutProcVerified =	$tableFormStatusRow['timeoutProcVerified'];
			$timeoutSiteVerified =	$tableFormStatusRow['timeoutSiteVerified'];
			
			$signNurse1Id_injection		= $tableFormStatusRow['signNurse1Id'];
			$signNurse2Id_injection		= $tableFormStatusRow['signNurse2Id'];
			$signSurgeon1Id_injection	= $tableFormStatusRow['signSurgeon1Id'];
			
			
			$tableFormStatus	=	'completed';
			$preVital		=	array($preVitalTime,$preVitalBp,$preVitalPulse,$preVitalResp,$preVitalSpo);
			$postVital	=	array($postVitalTime,$postVitalBp,$postVitalPulse,$postVitalResp,$postVitalSpo);
			$compValid	=	array($complications,$comments);
			$andArray		= array($startTime,$endTime,$chkConsentSigned,$procedureComments,$postIop,$postIopSite,$postIopTime);
			
			if(		!($objManageData->validateGroupOR($preVital))
				||	!($objManageData->validateGroupOR($postVital))
				||	!($objManageData->validateGroupOR($compValid))
				|| 	!($objManageData->validateGroupAND($andArray))
				||	!($signNurse1Id_injection)
				||	!($signNurse2Id_injection)
				||	!($signSurgeon1Id_injection)
				)
			{
				$tableFormStatus	=	'not completed';		
			}
			
			//Start Validate timeout fields if timeout required is checked from default profile/saved value
			if($timeoutReq && $tableFormStatus == 'completed')	
			{
				$timeoutArray		= array($timeoutTime,$timeoutProcVerified,$timeoutSiteVerified);
				if(!($objManageData->validateGroupAND($timeoutArray)))
				{
					$tableFormStatus	=	'not completed';	
				}
			}
			//End Validate timeout fields if timeout required is checked from default profile/saved value
			
			
		}
		else if($SaveSignArrTableName == "transfer_followups") {		
			
				$leftNaviFieldName	= "transfer_and_followups_form";
				
				$transfer_reason = trim($tableFormStatusRow['transfer_reason']);
				$hospital_contacted = trim($tableFormStatusRow['hospital_contacted']);
				$contacted_time = trim($tableFormStatusRow['contacted_time']);
				$transfer_method = trim($tableFormStatusRow['transfer_method']);
				$lv_running = trim($tableFormStatusRow['lv_running']);
				$airway_support = trim($tableFormStatusRow['airway_support']);
				$transfer_forms = trim($tableFormStatusRow['transfer_forms']);
				$demographics = trim($tableFormStatusRow['demographics']);
				$chart_note = trim($tableFormStatusRow['chart_note']);
				$ekg = trim($tableFormStatusRow['ekg']);
				$advance_directive = trim($tableFormStatusRow['advance_directive']);
				$cpr_report = trim($tableFormStatusRow['cpr_report']);
				$patient_belongings = trim($tableFormStatusRow['patient_belongings']);
				$surgeon_reassessment = trim($tableFormStatusRow['surgeon_reassessment']);
				$summary_of_care_time = trim($tableFormStatusRow['summary_of_care_time']);
				$date_discharge_from_hospital = trim($tableFormStatusRow['date_discharge_from_hospital']);
				$fDate = trim($tableFormStatusRow['fDate']);
				$chk_signNurseId 			= trim($tableFormStatusRow['signNurseId']);
				$chk_signNurse1Id 			= trim($tableFormStatusRow['signNurse1Id']);
				$chk_signSurgeon1Id 		= trim($tableFormStatusRow['signSurgeon1Id']);
				
				
				if( 	   ($transfer_reason <> 'Emergency' &&  $transfer_reason <> 'Non-Emergency')
						|| ($hospital_contacted <> '1' )
						|| ($contacted_time == '' )
						|| ($transfer_method <> 'Taxi' &&  $transfer_method <> 'Private-Car' &&  $transfer_method <> 'Ambulance' )
						|| ($lv_running <> '1' &&  $lv_running <> '2' )
						|| ($airway_support <> '1' &&  $airway_support <> '2' )
						|| ($transfer_forms <> '1' && $transfer_forms <> '2' &&  $transfer_forms <> '3' )
						|| ($demographics <> '1' &&  $demographics <> '2' &&  $demographics <> '3' )
						|| ($chart_note <> '1' &&  $chart_note <> '2' &&  $chart_note <> '3' )
						|| ($ekg <> '1' &&  $ekg <> '2' && $ekg <> '3' )
						|| ($advance_directive <> '1' &&  $advance_directive <> '2' &&  $advance_directive <> '3' )
						|| ($cpr_report <> '1' &&  $cpr_report <> '2' &&  $cpr_report <> '3' )
						|| ($patient_belongings == '' )
						|| ($surgeon_reassessment <> 1)
						|| ($summary_of_care_time == '' )
						|| ($date_discharge_from_hospital == '' )
						|| ($fDate == '' )	
						|| ($chk_signNurseId == "0")
						|| ($chk_signNurse1Id == "0")
						|| ($chk_signSurgeon1Id == "0")
						
					){
						
						$tableFormStatus = 'not completed';
					}
			
		
		}
		else if($SaveSignArrTableName == "patient_medication_reconciliation_sheet") {		
			
				$leftNaviFieldName	= "medication_reconciliation_sheet_form";
				
				$chk_signNurseId 			= trim($tableFormStatusRow['signNurseId']);
				$chk_signSurgeon1Id 	= trim($tableFormStatusRow['signSurgeon1Id']);
				
				
				if( $chk_signNurseId == "0" || $chk_signSurgeon1Id == "0" )
				{
					$tableFormStatus = 'not completed';
				}
		}

		if($SaveSignArrTableName == "operativereport") {
			//DO NOT UPDATE FORM STATUS FOR Laser Chartnote if procedure is not laser
		}else if($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId!='2') {
			//DO NOT UPDATE FORM STATUS FOR Laser Chartnote if procedure is not laser
		}else if($laserSignCatId=='2' && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders")) {
			//DO NOT UPDATE FORM STATUS FOR preopphysicianorders AND postopphysicianorders Chartnote if procedure is laser
		}else if($laserSignCatId <> '2' && $primary_procedure_is_inj_misc && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders")) {
			//DO NOT UPDATE FORM STATUS FOR preopphysicianorders AND postopphysicianorders Chartnote if procedure is injection
		}else{	
			
			$updateTableFormStatusQry = "update $SaveSignArrTableName SET form_status='$tableFormStatus' WHERE $signUserconfirmation_id='".$_REQUEST["pConfId"]."'".$hptfFrmStatusQry;
			$updateTableFormStatusRes = imw_query($updateTableFormStatusQry) or die($updateTableFormStatusQry.imw_error());
		
			//START SHIFTING LEFT LINK TO RIGHT SLIDER 
			if($SaveSignArrTableName!="history_physicial_clearance" && $SaveSignArrTableName!="transfer_followups" && $SaveSignArrTableName != "patient_medication_reconciliation_sheet" ) {	
				$updateLeftNavigationTableQry = "update `left_navigation_forms` set $leftNaviFieldName = 'false' WHERE confirmationId='".$_REQUEST["pConfId"]."'";
				$updateLeftNavigationTableRes = imw_query($updateLeftNavigationTableQry) or die($updateLeftNavigationTableQry.imw_error());		
			}
			//END SHIFTING LEFT LINK TO RIGHT SLIDER
		
			//START SET INTE FLAG IN DISCHARGE SUMMARY SHEET TO PREPARE TO SEND CHARGES TO INTEGRITY
			if(constant("INTE_SYNC") && constant("INTE_SYNC") == 'YES' && $SaveSignArrTableName == "dischargesummarysheet") {
				$updateCptInteStatusQry = "update dischargesummarysheet SET cpt_inte_sync_flag = '1', cpt_inte_sync_status = '0' WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
				$updateCptInteStatusRes = imw_query($updateCptInteStatusQry) or die($updateCptInteStatusQry.imw_error());
			}
			//END SET INTE FLAG IN DISCHARGE SUMMARY SHEET TO PREPARE TO SEND CHARGES TO INTEGRITY
		}
		
		/*******HL7- DFT GENERATION***********/
		if($SaveSignArrTableName == "dischargesummarysheet" && $tableFormStatus == "completed" && constant('DCS_DFT_GENERATION')==true && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham','islandeye','valleyeye'))) {

			/*
			 * PRL Check - Waltham
			 * Check if ann the surgeon charts are completed and signed.
			 * Setting this to true allways here, Because this is under the condition of discharge summary completed
			 * and executed on sign all by surgeon option.
			*/
			if ( strtolower($GLOBALS["LOCAL_SERVER"]) ==  'waltham' )
			{	$log_prl_hl7 = true;
				/*
				 * Sugeon - Sign all check
				*/
				/*$sql = "SELECT `chartSignedBySurgeon` FROM `stub_tbl` WHERE `patient_confirmation_id`=".(INT)$_REQUEST["pConfId"];
				$resp = imw_query($sql);
				if( $resp && imw_num_rows($resp) > 0 )
				{
					$resp = imw_fetch_assoc($resp);
					if( trim($resp['chartSignedBySurgeon']) === 'green' )
					{
						$log_prl_hl7 = true;
					}
				}*/
			}

			$chkHl7SentQry = "SELECT id,sent FROM hl7_sent WHERE sch_id='".$_REQUEST["pConfId"]."' AND msg_type = 'DFT' AND send_to ='PPMC' LIMIT 0,1 ";	
			$chkHl7SentRes = imw_query($chkHl7SentQry) or die($chkHl7SentQry.imw_error());
			$hl4_thisconfID_sent = 1;
			if($chkHl7SentRes && imw_num_rows($chkHl7SentRes)>0){
				$chkHl7SentRS = imw_fetch_assoc($chkHl7SentRes);
				$hl4_thisconfID_sent = $chkHl7SentRS['sent'];
			}
			if(imw_num_rows($chkHl7SentRes) <= 0 || $hl4_thisconfID_sent=='0') { //IF MSG NOT GENERATED THEN GENERATE DFT MSG
				$chkLocalAnesQry = "SELECT localAnesthesiaRecordId FROM localanesthesiarecord WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND form_status = 'completed' ";	
				$chkLocalAnesRes = imw_query($chkLocalAnesQry) or die($chkLocalAnesQry.imw_error());
				if(imw_num_rows($chkLocalAnesRes)>0 || in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye','waltham'))) {//IF FLAG IS GREEN FOR LOCAL ANES AND DISCHARGE SUMMARY THEN GENERATE DFT MSG
					$pConfId = $_REQUEST["pConfId"];
					if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
						$chkHl7SentRes = imw_query("SELECT id,sent FROM hl7_sent WHERE sch_id='".$_REQUEST["pConfId"]."' AND msg_type = 'DFT' LIMIT 0,1");
						if(imw_num_rows($chkHl7SentRes)==0){
							include(dirname(__FILE__)."/dft_hl7_generate.php");
						}
					}else{
						include(dirname(__FILE__)."/dft_hl7_generate.php");
					}
				}
			}
			
		}
		/*******HL7- DFT GENERATION***********/
		
	//END SET FORM STATUS(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
	
	
	
	}
	//START CODE TO MAKE SIGNATURE IN CONSENT FORM
		$chkSignConsentQry = "SELECT * FROM consent_multiple_form WHERE (signSurgeon1Id='0' OR signSurgeon1Id='') AND consent_purge_status!='true' AND confirmation_id='".$_REQUEST["pConfId"]."'";
		$chkSignConsentRes = imw_query($chkSignConsentQry) or die($chkSignConsentQry.imw_error());
		$chkSignConsentNumRow = imw_num_rows($chkSignConsentRes);
		$SaveSignConsentQry = "";
		if($chkSignConsentNumRow>0) {
			$SaveSignConsentQry = "update consent_multiple_form set 
										signSurgeon1Id = '$loggedInUserId',
										signSurgeon1FirstName = '$loggedInUserFirstName', 
										signSurgeon1MiddleName = '$loggedInUserMiddleName',
										signSurgeon1LastName = '$loggedInUserLastName', 
										signSurgeon1Status = '$signOnFileStatus',
										signSurgeon1DateTime = '$signDateTime'
										WHERE (signSurgeon1Id='0' OR signSurgeon1Id='')
										AND signSurgeon1Activate='yes'
										AND consent_purge_status!='true'
										AND confirmation_id='".$_REQUEST["pConfId"]."'";
			$SaveSignConsentRes = imw_query($SaveSignConsentQry) or die($SaveSignConsentQry.imw_error());
		}
	//END CODE TO MAKE SIGNATURE IN CONSENT FORM	
	
	//START SET FORM STATUS OF SIGNED CONSENT FORM
		$chkConsentSignSurgeonNurseQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$_REQUEST["pConfId"]."' AND consent_purge_status!='true' AND (signSurgeon1Activate='yes' || signNurseActivate='yes' || signAnesthesia1Activate='yes' || signWitness1Activate='yes')";
		$chkConsentSignSurgeonNurseRes = imw_query($chkConsentSignSurgeonNurseQry) or die($chkConsentSignAllQry.imw_error());
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
				
				if($chkConsentSignSurgeon1Activate=='yes' && !$SaveSignConsentQry) {
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
				$updateConsentSurgeonNurseFormStatusRes = imw_query($updateConsentSurgeonNurseFormStatusQry) or die($updateConsentSurgeonNurseFormStatusQry.imw_error());
			
			}
		}
	//END SET FORM STATUS OF SIGNED CONSENT FORM	
	
	//START CODE TO MAKE SIGNATURE IN INSTRUCTION SHEET
		$chkSignInstructionQry = "SELECT * FROM patient_instruction_sheet WHERE (signSurgeon1Id='0' OR signSurgeon1Id='') AND patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$chkSignInstructionRes = imw_query($chkSignInstructionQry) or die($chkSignInstructionQry.imw_error());
		$chkSignInstructionNumRow = imw_num_rows($chkSignInstructionRes);
		if($chkSignInstructionNumRow>0) {
			$SaveSignInstructionQry = "update patient_instruction_sheet set 
										signSurgeon1Id = '$loggedInUserId',
										signSurgeon1FirstName = '$loggedInUserFirstName', 
										signSurgeon1MiddleName = '$loggedInUserMiddleName',
										signSurgeon1LastName = '$loggedInUserLastName', 
										signSurgeon1Status = '$signOnFileStatus',
										signSurgeon1DateTime = '$signDateTime'
										WHERE (signSurgeon1Id='0' OR signSurgeon1Id='')
										AND signSurgeon1Activate='yes'
										AND patient_confirmation_id='".$_REQUEST["pConfId"]."'";
			$SaveSignInstructionRes = imw_query($SaveSignInstructionQry) or die($SaveSignInstructionQry.imw_error());
		}
	//END CODE TO MAKE SIGNATURE IN INSTRUCTION SHEET
	
	//START SET FORM STATUS OF SIGNED INSTRUCTION SHEET
		$chkInstructionSignSurgeonNurseQry = "SELECT * FROM patient_instruction_sheet WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'AND (signSurgeon1Activate='yes' || signNurseActivate='yes' || signWitness1Activate='yes')";
		$chkInstructionSignSurgeonNurseRes = imw_query($chkInstructionSignSurgeonNurseQry) or die($chkInstructionSignAllQry.imw_error());
		$chkInstructionSignSurgeonNurseNumRow = imw_num_rows($chkInstructionSignSurgeonNurseRes);
		if($chkInstructionSignSurgeonNurseNumRow>0) {
			while($chkInstructionSignSurgeonNurseRow = imw_fetch_array($chkInstructionSignSurgeonNurseRes)){
				$chkInstructionSurgeonNurseFormStatus='completed';
				$chkInstructionSignSurgeryInstructionId = $chkInstructionSignSurgeonNurseRow['patient_instruction_id'];
				$chkInstructionSignSurgeryInstructionData = $chkInstructionSignSurgeonNurseRow['instruction_sheet_data'];
				
				$chkInstructionSignSurgeon1Id = $chkInstructionSignSurgeonNurseRow['signSurgeon1Id'];
				$chkInstructionSignNurseId = $chkInstructionSignSurgeonNurseRow['signNurseId'];
				$chkInstructionSignWitness1Id = $chkInstructionSignSurgeonNurseRow['signWitness1Id'];
				
				$chkInstructionSignSurgeon1Activate = $chkInstructionSignSurgeonNurseRow['signSurgeon1Activate'];
				$chkInstructionSignNurseActivate = $chkInstructionSignSurgeonNurseRow['signNurseActivate'];
				$chkInstructionSignWitness1Activate = $chkInstructionSignSurgeonNurseRow['signWitness1Activate'];
				
				if($chkInstructionSignSurgeon1Activate=='yes') {
					if($chkInstructionSignSurgeon1Id=='0' || $chkInstructionSignSurgeon1Id=='') {
						$chkInstructionSurgeonNurseFormStatus = "not completed";
					}	
				}
				if($chkInstructionSignNurseActivate=='yes') {
					if($chkInstructionSignNurseId=='0' || $chkInstructionSignNurseId=='') {
						$chkInstructionSurgeonNurseFormStatus = "not completed";
					}		
				}
				
				if($chkInstructionSignWitness1Activate=='yes') {
					if($chkInstructionSignWitness1Id=='0' || $chkInstructionSignWitness1Id=='') {
						$chkInstructionSurgeonNurseFormStatus = "not completed";
					}	
				}
				if(strpos($chkInstructionSignSurgeryInstructionData, '{SIGNATURE}') !== false){ //IF PATIENT NOT SIGNED CHARTNOTE THEN-
					$chkInstructionSurgeonNurseFormStatus = "not completed";
				}
				$updateInstructionSurgeonNurseFormStatusQry = "update `patient_instruction_sheet` SET form_status='".$chkInstructionSurgeonNurseFormStatus."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
				$updateInstructionSurgeonNurseFormStatusRes = imw_query($updateInstructionSurgeonNurseFormStatusQry) or die($updateInstructionSurgeonNurseFormStatusQry.imw_error());
			
			}
		}
	//END SET FORM STATUS OF SIGNED INSTRUCTION SHEET
	
	
	//START SET FORM STATUS OF 'OPREATING ROOM RECORD'(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
		$opRoomRecordStatusQry = "SELECT * FROM operatingroomrecords  WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
		$opRoomRecordStatusRes = imw_query($opRoomRecordStatusQry) or die($opRoomRecordStatusQry.imw_error());
		$opRoomRecordStatusNumRow = imw_num_rows($opRoomRecordStatusRes);
		
		//$opRoomRecordFormStatus = 'not completed';
		if($opRoomRecordStatusNumRow>0  && $laserSignCatId!='2' && !$primary_procedure_is_inj_misc) {	
			$opRoomRecordStatusRow = imw_fetch_array($opRoomRecordStatusRes);
			
			//START CODE TO SET VITAL SIGN GRID STATUS
			$opRoomVitalSignGridQuery = '';
			if($opRoomRecordStatusRow['form_status'] == '') {
				$opRoomVitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($opRoomRecordStatusRow['form_status'],$opRoomRecordStatusRow['vitalSignGridStatus'],'oproom');
				$opRoomVitalSignGridQuery	=	", vitalSignGridStatus = '".$opRoomVitalSignGridStatus."'  ";
			}
			//END CODE TO SET VITAL SIGN GRID STATUS
			
			$verifiedbyNurse = $opRoomRecordStatusRow['verifiedbyNurse'];
			$verifiedbySurgeon = $opRoomRecordStatusRow['verifiedbySurgeon'];
			$verifiedbyAnesthesiologist = $opRoomRecordStatusRow['verifiedbyAnesthesiologist'];
			
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
			
			if(($verifiedbyNurse!="" && $verifiedbySurgeon!="" )
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
			$updateOpRoomRecordFormStatusRes = imw_query($updateOpRoomRecordFormStatusQry) or die($updateOpRoomRecordFormStatusQry.imw_error());
		
			//START SHIFTING LEFT LINK OF 'OPERATING ROOM RECORD' TO RIGHT SLIDER 
				$updateLeftNavigationOpRoomRecordQry = "update `left_navigation_forms` set intra_op_record_form = 'false' WHERE confirmationId='".$_REQUEST["pConfId"]."'";
				$updateLeftNavigationOpRoomRecordRes = imw_query($updateLeftNavigationOpRoomRecordQry) or die($updateLeftNavigationOpRoomRecordQry.imw_error());		
			//END SHIFTING LEFT LINK OF 'OPERATING ROOM RECORD TO RIGHT SLIDER
		
		}	
	//END SET FORM STATUS OF 'OPREATING ROOM RECORD'(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
	
//END CODE TO MAKE SIGNATURES OF SURGEON IN ALL CHART NOTES OF PATIENT(IN OPENED DOS)	


//START CODE TO SAVE OPERATIVE DEFAULT REPORT
	if($laserSignCatId!='2' ) {	//IF PROCEDURE IS NOT LASER
		$ViewoperativeQry = "select * from `operativereport` where confirmation_id='".$_REQUEST["pConfId"]."'";
		$ViewoperativeRes = imw_query($ViewoperativeQry) or die($ViewoperativeQry.imw_error()); 
		$ViewoperativeNumRow = imw_num_rows($ViewoperativeRes);
		if($ViewoperativeNumRow>0) {
			$ViewoperativeRow = imw_fetch_array($ViewoperativeRes); 
			$signSurgeon1IdOpReport = $ViewoperativeRow['signSurgeon1Id'];
			$operative_data = trim(stripslashes($ViewoperativeRow["reportTemplate"]));
			if($ViewoperativeRow["template_id"]) {
				$operativeTemplateId = $ViewoperativeRow["template_id"];
			}
			//$form_status = $ViewoperativeRow["form_status"];
		}
		//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
		if(trim($operative_data)=="") {
			if($operativeTemplateId) {
				$ViewOperativeTemplateQry = "select * from operative_template where template_id = '".$operativeTemplateId."'";
				
				$ViewOperativeTemplateRes = imw_query($ViewOperativeTemplateQry) or die($ViewOperativeTemplateQry.imw_error()); 
				$ViewOperativeTemplateNumRow = imw_num_rows($ViewOperativeTemplateRes);
				$ViewOperativeTemplateRow = imw_fetch_array($ViewOperativeTemplateRes); 
				$operative_data = stripslashes($ViewOperativeTemplateRow["template_data"]);
			}
		}
	
		$Operative_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
		$Operative_patientName_tblRes = imw_query($Operative_patientName_tblQry) or die($Operative_patientName_tblQry.imw_error());
		if(imw_num_rows($Operative_patientName_tblRes)>0) {
			$Operative_patientName_tblRow = imw_fetch_array($Operative_patientName_tblRes);
			$Operative_patientNameDob = date('m-d-Y',strtotime($Operative_patientName_tblRow["date_of_birth"]));
			$imwPatientId 	   	  = $Operative_patientName_tblRow["imwPatientId"];
		}
		
		//DIAGNOSIS FROM OPERATING ROOM RECORD (SEE ABOVE)
		if(trim($postOpDiagnosis)=="") {
			$postOpDiagnosis = $preOpDiagnosis;
		}
		//DIAGNOSIS FROM OPERATING ROOM RECORD (SEE ABOVE)
		
		//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
			$operative_data= str_replace("{PATIENT FIRST NAME}","<b>".$Operative_patientName_tblRow["patient_fname"]."</b>",$operative_data);
			$operative_data= str_replace("{MIDDLE INITIAL}","<b>".$Operative_patientName_tblRow["patient_mname"]."</b>",$operative_data);
			$operative_data= str_replace("{LAST NAME}","<b>".$Operative_patientName_tblRow["patient_lname"]."</b>",$operative_data);
			$operative_data= str_replace("{DOB}","<b>".$Operative_patientNameDob."</b>",$operative_data);
			$operative_data= str_replace("{DOS}","<b>".$Operative_patientConfirmDos."</b>",$operative_data);
			$operative_data= str_replace("{SURGEON NAME}","<b>".$getConfirmationDetailRow["surgeon_name"]."</b>",$operative_data);
			$operative_data= str_replace("{SITE}","<b>".$Operative_patientConfirmSite."</b>",$operative_data);
			$operative_data= str_replace("{PROCEDURE}","<b>".$Operative_patientConfirmPrimProc."</b>",$operative_data);
			$operative_data= str_replace("{SECONDARY PROCEDURE}","<b>".$Operative_patientConfirmSecProc."</b>",$operative_data);
			$operative_data= str_replace("{PRE-OP DIAGNOSIS}","<b>".$preOpDiagnosis."</b>",$operative_data);
			$operative_data= str_replace("{POST-OP DIAGNOSIS}","<b>".$postOpDiagnosis."</b>",$operative_data);
			$operative_data= str_replace("{DATE}","<b>".date('m-d-Y')."</b>",$operative_data);
			$operative_data= str_replace("{TIME}","<b>".date('h:i A')."</b>",$operative_data);
	
		//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
		
		$formStatusOpReport='completed';
		if($signSurgeon1IdOpReport=="0" || !$operative_data){
			$formStatusOpReport = 'not completed';
		}
		
		$SaveoperativeQry = "update `operativereport` set 
								reportTemplate = '".addslashes($operative_data)."',
								userId='".$_SESSION['loginUserId']."',
								form_status ='".$formStatusOpReport."',
								template_id = '".$operativeTemplateId."',
								patientId='".$patient_id."'
								WHERE 
								confirmation_id='".$_REQUEST["pConfId"]."'
									";
		$SaveoperativeRes = imw_query($SaveoperativeQry) or die($SaveoperativeQry.imw_error());							
	
		//START SHIFTING LEFT LINK OF 'Operative Report' TO RIGHT SLIDER 
			$updateLeftNavigationOperativeReportQry = "update `left_navigation_forms` set surgical_operative_record_form = 'false' WHERE confirmationId='".$_REQUEST["pConfId"]."'";
			$updateLeftNavigationOperativeReportRes = imw_query($updateLeftNavigationOperativeReportQry) or die($updateLeftNavigationOperativeReportQry.imw_error());		
		//END SHIFTING LEFT LINK OF 'Operative Report' TO RIGHT SLIDER

		//START SENDING OPNOTE TO iDOC
		$ascId = $confirmationascId;
		if(trim($ascId)<>"" && trim($ascId)<>0 && $formStatusOpReport=="completed" && $imwSwitchFile == "sync_imwemr.php" && $imwPatientId && $_REQUEST["pConfId"]) {
			include_once("sync_operative_record.php");
		}
		if(trim($ascId)<>"" && trim($ascId)<>0 && $formStatusOpReport=="completed" && $imwSwitchFile == "sync_imwemr.php" && constant("INTE_SYNC") && constant("INTE_SYNC") == "YES" && $_REQUEST["pConfId"]) {
			$syncExternalPdf = "yes";
			include_once("operative_recordPdf.php");			
		}
		//END SENDING OPNOTE TO BILLING SOFTWARE iDOC
		
	}	
//END CODE TO SAVE OPERATIVE DEFAULT REPORT

//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
	$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
	$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
	$updateStubTblRes = imw_query($updateStubTblQry) or die($updateStubTblQry.imw_error());
//END CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE

//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
	$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
	$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
	$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die($updateAnesStubTblQry.imw_error());
//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE

//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
	$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
	$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
	$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die($updateNurseStubTblQry.imw_error());
//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE


//SET ALLERGIES VALUE IN HEADER
	$patient_allergies_tblQry = "SELECT * FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '".$_REQUEST["pConfId"]."'";
	$patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die($patient_allergies_tblQry.imw_error());
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
	if($laserSignCatId!=2 && !$primary_procedure_is_inj_misc) {
		$selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingHeaderRes = imw_query($selectPreOpNursingHeaderQry) or die($selectPreOpNursingHeaderQry.imw_error());
		$selectPreOpNursingHeaderNumRow = imw_num_rows($selectPreOpNursingHeaderRes);
		if($selectPreOpNursingHeaderNumRow>0) {
			$selectPreOpNursingHeaderRow = imw_fetch_array($selectPreOpNursingHeaderRes);
			$preopnursing_vitalsign_id = $selectPreOpNursingHeaderRow["preopnursing_vitalsign_id"];
			$vitalSignBp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignBp"];
			$vitalSignP_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignP"];
			$vitalSignR_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignR"];
			$vitalSignO2SAT_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignO2SAT"];
			$vitalSignTemp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignTemp"];
			
			if($vitalSignTemp_NursingHeader<>"") {
				$vitalSignTemp_NursingHeader = $vitalSignTemp_NursingHeader;
			}
		}
	}
	elseif($laserSignCatId!=2 && $primary_procedure_is_inj_misc) {
		$selectVitalSignCopyHeaderQry = "SELECT * FROM `injection` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectVitalSignCopyHeaderRes = imw_query($selectVitalSignCopyHeaderQry) or die($selectVitalSignCopyHeaderQry.imw_error());
		$selectVitalSignCopyHeaderNumRow = imw_num_rows($selectVitalSignCopyHeaderRes);
		if($selectVitalSignCopyHeaderNumRow>0) {
			$selectVitalSignCopyHeaderRow 	= imw_fetch_array($selectVitalSignCopyHeaderRes);
			$vitalSignBp_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalBp"];
			$vitalSignP_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalPulse"];
			$vitalSignR_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalResp"];
			$vitalSignO2SAT_NursingHeader 	= $selectVitalSignCopyHeaderRow["preVitalSpo"];
			$vitalSignTemp_NursingHeader		=	'N/A';
			$vitalSignHeight_NursingHeader	= "N/A";
			$vitalSignWeight_NursingHeader	= "N/A";
			$vitalSignBmi_NursingHeader			= "N/A";
		}
	}
	else{
		$selectLaserProedureHeaderQry = "SELECT * FROM `laser_procedure_patient_table` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectLaserProedureHeaderRes = imw_query($selectLaserProedureHeaderQry) or die($selectLaserProedureHeaderQry.imw_error());
		$selectLaserProedureHeaderNumRow = imw_num_rows($selectLaserProedureHeaderRes);
		if($selectLaserProedureHeaderNumRow>0) {
			$selectlaserProcedureRow = imw_fetch_array($selectLaserProedureHeaderRes);
			$vitalSignBp_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignBP"];
			$vitalSignP_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignP"];
			$vitalSignR_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignR"];
			$vitalSignO2SAT_NursingHeader = 'N/A';
			$vitalSignTemp_NursingHeader = 'N/A';
			$vitalSignHeight_NursingHeader	= "N/A";
			$vitalSignWeight_NursingHeader	= "N/A";
			$vitalSignBmi_NursingHeader			= "N/A";
				
		}
	}	

if($laserSignCatId==2 || ( $laserSignCatId <> 2 && $primary_procedure_is_inj_misc) ) {
?>
	<script>
		//SET BP, P, R, TEMP VALUES IN HEADER
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
			if(top.document.getElementById('header_Bmi')) {
				top.document.getElementById('header_Bmi').innerText='<?php echo $vitalSignBmi_NursingHeader;?>';
			}
		//SET BP, P, R, TEMP VALUES IN HEADER
	</script>
<?php
}
//END SET BASE LINE VITAL SIGN IN HEADER

//SET ANESTHESIOLOGIST NAME IN HEADER
	if($laserSignCatId==2 || ( $laserSignCatId <> 2 && $primary_procedure_is_inj_misc)) {
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

$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId&dis_sumry_filled=$dis_sumry_filled";	
	?>
	
<script>
	var locationFrame = '<?php echo $pageName;?>';
	//top.mainFrame.location.href = locationFrame;
	var dis_sumry_filled = '<?php echo $dis_sumry_filled;?>';
	if(dis_sumry_filled == 'no') {
		alert("Discharge Summary is not complete");
	}
	top.location.reload();
	//top.setPNotesHeight();
</script>
	<table  height="450" bgcolor="#ECF1EA" cellpadding="0" cellspacing="0" border="0" align="center" width="100%"  >
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>