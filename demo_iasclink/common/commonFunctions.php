<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
//START COLOR CODE TO SET BACKGROUND COLOR FOR THE SIGNATURES USED THRU AJAX FILES
$chngBckGroundColorAjax='background-color:#F6C67A';
//END COLOR CODE TO SET BACKGROUND COLOR FOR THE SIGNATURES USED THRU AJAX FILES

function getData($req, $table, $cond, $match){
	$strQry = imw_query("SELECT  $req FROM $table WHERE $cond = '$match'");
	$strRow = imw_fetch_assoc($strQry);
	return $strRow[$req];
}

//FUNCTION TO CHECK ALL SIGN OF SURGEON
function chkSurgeonSignNew($chkSurgeonConfId) {
	$chkSignArr = array('preopphysicianorders', 'postopphysicianorders','operativereport', 'dischargesummarysheet');
	$chkSurgeonSignColor='green';
	
	//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
	$getLaserConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  patientConfirmationId='".$chkSurgeonConfId."'";
	$getLaserConfirmationDetailRes = imw_query($getLaserConfirmationDetailQry) or die(imw_error()); 
	$getLaserConfirmationDetailNumRow = imw_num_rows($getLaserConfirmationDetailRes);
	if($getLaserConfirmationDetailNumRow>0) {
		$getLaserConfirmationDetailRow = imw_fetch_array($getLaserConfirmationDetailRes);
		$laserConfirmationPrimaryProcedureId = $getLaserConfirmationDetailRow["patient_primary_procedure_id"]; 
		$laserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$laserConfirmationPrimaryProcedureId."'";
		$laserCatIdDetailRes = imw_query($laserCatIdDetailQry) or die(imw_error()); 
		$laserCatIdDetailNumRow = imw_num_rows($laserCatIdDetailRes);
		if($laserCatIdDetailNumRow>0) {
			$laserCatIdDetailRow = imw_fetch_array($laserCatIdDetailRes);
			$laserCatId = $laserCatIdDetailRow['catId'];
		}
	}
	//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
	
	if($laserCatId=='2') {	//IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN
		$chkLaserSignArr = array('laser_procedure_patient_table', 'dischargesummarysheet');
		foreach($chkLaserSignArr as $chkLaserSignArrTableName){
			$chkAndLaserChartRecordQry='';
			if($chkLaserSignArrTableName=='laser_procedure_patient_table') {//CHECK VERIFIED BY SURGEON ALSO
				$chkAndLaserChartRecordQry=" AND verified_surgeon_Id!='0' AND verified_surgeon_Id!=''";
			}
			$chkLaserChartRecordQry = "SELECT * FROM $chkLaserSignArrTableName WHERE confirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Id!='0' AND signSurgeon1Id!='' $chkAndLaserChartRecordQry";
			$chkLaserChartRecordRes = imw_query($chkLaserChartRecordQry) or die($chkPatientChartRecordQry.imw_error());
			$chkLaserChartRecordNumRow = imw_num_rows($chkLaserChartRecordRes);
			if($chkLaserChartRecordNumRow>0) {
				$chkLaserChartRecordRow = imw_fetch_array($chkLaserChartRecordRes);
				$chkLaserChartSignSurgeon1Id = $chkLaserChartRecordRow['signSurgeon1Id'];
				$chkLaserChartFormStatus = $chkLaserChartRecordRow['form_status'];

				if(($chkLaserChartFormStatus=='not completed' || $chkLaserChartFormStatus=='') && $chkSurgeonSignColor=='') {
					$chkSurgeonSignColor='';
				}else if($chkLaserChartFormStatus=='not completed' || $chkLaserChartFormStatus=='') {
					$chkSurgeonSignColor='red';
				}else if($chkLaserChartFormStatus=='completed' && $chkSurgeonSignColor=='') {
					$chkSurgeonSignColor='';
				}
			}else {
				$chkSurgeonSignColor='';
			}
		}	
	}else { //ELSE
		foreach($chkSignArr as $chkSignArrTableName){
			
			if($chkSignArrTableName == "preopphysicianorders" || $chkSignArrTableName == "postopphysicianorders") {
				$signUserconfirmation_id = 'patient_confirmation_id';
			}else if($chkSignArrTableName == "operativereport" || $chkSignArrTableName == "dischargesummarysheet") {
				$signUserconfirmation_id = 'confirmation_id';
			}
			//CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
			$chkPatientChartRecordQry = "SELECT * FROM $chkSignArrTableName WHERE $signUserconfirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Id!='0' AND signSurgeon1Id!=''";
			$chkPatientChartRecordRes = imw_query($chkPatientChartRecordQry) or die($chkPatientChartRecordQry.imw_error());
			$chkPatientChartRecordNumRow = imw_num_rows($chkPatientChartRecordRes);
			
			$chkPatientChartFormStatus='';
			if($chkPatientChartRecordNumRow>0) {
				$chkPatientChartRecordRow = imw_fetch_array($chkPatientChartRecordRes);
				$chkPatientChartFormStatus = $chkPatientChartRecordRow['form_status'];
				if(($chkPatientChartFormStatus=='not completed' || $chkPatientChartFormStatus=='') && $chkSurgeonSignColor=='') {
					$chkSurgeonSignColor='';
				}else if($chkPatientChartFormStatus=='not completed' || $chkPatientChartFormStatus=='') {
					$chkSurgeonSignColor='red';
				}else if($chkPatientChartFormStatus=='completed' && $chkSurgeonSignColor=='') {
					$chkSurgeonSignColor='';
				}
			}else {
				$chkSurgeonSignColor='';
			}
			//END CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
		}
	
		//CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT  
		if($chkSurgeonSignColor!='') {
			$chkOproomSurgeonCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE confirmation_id='".$chkSurgeonConfId."' AND verifiedbySurgeon='Yes'";
			$chkOproomSurgeonCheckMarkRes = imw_query($chkOproomSurgeonCheckMarkQry) or die(imw_error());
			$chkOproomSurgeonCheckMarkNumRow = imw_num_rows($chkOproomSurgeonCheckMarkRes);
			if($chkOproomSurgeonCheckMarkNumRow>0) {
				$chkOproomSurgeonCheckMarkRow = imw_fetch_array($chkOproomSurgeonCheckMarkRes);
				$chkOproomSignVerifybySurgeon = $chkOproomSurgeonCheckMarkRow['verifiedbySurgeon'];
				$chkOproomSignFormStatus = $chkOproomSurgeonCheckMarkRow['form_status'];
				
				if($chkOproomSignFormStatus=='not completed') {
					$chkSurgeonSignColor='red';
				}
			}else {
				$chkSurgeonSignColor='';
			}
		}
		//END CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT
		
	}
	
	//START COMMON CHECKING OF 'SURGEON SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
	if($chkSurgeonSignColor!='') {
		$chkConsentSignAllQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Activate='yes' AND consent_purge_status!='true'";
		$chkConsentSignAllRes = imw_query($chkConsentSignAllQry) or die($chkConsentSignAllQry.imw_error());
		$chkConsentAllSignNumRow = imw_num_rows($chkConsentSignAllRes);
		if($chkConsentAllSignNumRow>0) {
			$chkConsentSignSurgeon1Activate='';
			while($chkConsentSignRow=imw_fetch_array($chkConsentSignAllRes)) {
				$chkConsentSignSurgeon1Activate=$chkConsentSignRow['signSurgeon1Activate'];
				$chkConsentSignSurgeon1Id=$chkConsentSignRow['signSurgeon1Id'];
				$chkConsentFrmFormStatus=$chkConsentSignRow['form_status'];

				if($chkConsentSignSurgeon1Id=='0' || $chkConsentSignSurgeon1Id=='') {
					$chkSurgeonSignColor='';
				}else if(($chkConsentSignSurgeon1Id!='0' && $chkConsentSignSurgeon1Id!='') && $chkConsentFrmFormStatus=='completed'  && $chkSurgeonSignColor=='') {
					$chkSurgeonSignColor='';
				}else if(($chkConsentSignSurgeon1Id!='0' && $chkConsentSignSurgeon1Id!='') && $chkConsentFrmFormStatus=='not completed' && $chkSurgeonSignColor=='') {
					$chkSurgeonSignColor='';
				}else if(($chkConsentSignSurgeon1Id!='0' && $chkConsentSignSurgeon1Id!='') && $chkConsentFrmFormStatus=='completed'  && $chkSurgeonSignColor=='red') {
					$chkSurgeonSignColor='red';
				}else if(($chkConsentSignSurgeon1Id!='0' && $chkConsentSignSurgeon1Id!='') && $chkConsentFrmFormStatus=='not completed') {
					$chkSurgeonSignColor='red';
				}
			}
		
		}
	}	
	//END COMMON CHECKING OF 'SURGEON SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
	
	return $chkSurgeonSignColor;	
}	
//END FUNCTION TO CHECK ALL SIGN OF SURGEON

//FUNCTION TO CHECK ALL SIGN OF Anes
function chkAnesSignNew($chkAnesConfId) {
	$chkSignAnesArr = array('operatingroomrecords');
	$chkAnesSignColor='green';
	$chkAnesRecordExist=false;
	
	//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
	$getAnesLaserConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  patientConfirmationId='".$chkAnesConfId."'";
	$getAnesLaserConfirmationDetailRes = imw_query($getAnesLaserConfirmationDetailQry) or die(imw_error()); 
	$getAnesLaserConfirmationDetailNumRow = imw_num_rows($getAnesLaserConfirmationDetailRes);
	if($getAnesLaserConfirmationDetailNumRow>0) {
		$getAnesLaserConfirmationDetailRow = imw_fetch_array($getAnesLaserConfirmationDetailRes);
		$laserAnesConfirmationPrimaryProcedureId = $getAnesLaserConfirmationDetailRow["patient_primary_procedure_id"]; 
		$laserCatIdAnesDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$laserAnesConfirmationPrimaryProcedureId."'";
		$laserCatIdAnesDetailRes = imw_query($laserCatIdAnesDetailQry) or die(imw_error()); 
		$laserCatIdAnesDetailNumRow = imw_num_rows($laserCatIdAnesDetailRes);
		if($laserCatIdAnesDetailNumRow>0) {
			$laserCatIdAnesDetailRow = imw_fetch_array($laserCatIdAnesDetailRes);
			$laserCatIdAnes = $laserCatIdAnesDetailRow['catId'];
		}
	}
	//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
	
	if($laserCatIdAnes=='2') {	//IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN  
		$chkAnesSignColor=''; //NOT REQUIRED FOR ANESTHESIOLOGIST
	}else { //ELSE	
		foreach($chkSignAnesArr as $chkSignAnesArrTableName){
			
			$chkOproomAnesCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE confirmation_id='".$chkAnesConfId."' AND verifiedbyAnesthesiologist='Yes'";
			$chkOproomAnesCheckMarkRes = imw_query($chkOproomAnesCheckMarkQry) or die(imw_error());
			$chkOproomAnesCheckMarkNumRow = imw_num_rows($chkOproomAnesCheckMarkRes);
			
			$chkOproomSignVerifybyAnes='';
			$chkOproomSignAnesFormStatus='';
			
			if($chkOproomAnesCheckMarkNumRow>0) {
				//$chkAnesRecordExist = true;
				$chkOproomAnesCheckMarkRow = imw_fetch_array($chkOproomAnesCheckMarkRes);
				$chkOproomSignVerifybyAnes = $chkOproomAnesCheckMarkRow['verifiedbyAnesthesiologist'];
				$chkOproomSignAnesFormStatus = $chkOproomAnesCheckMarkRow['form_status'];
				
				if($chkOproomSignAnesFormStatus=='not completed') {
					$chkAnesSignColor='red';
				}
				
			}else {
				$chkAnesSignColor='';
			}
		}
		
		//END CHECK IF Anes VERIFIED THE OPERATING ROOM RECORD OR NOT
	
		//CHECK EITHER OF 'LOCAL ANESTHESIA' OR 'GENERAL ANESTHESIA' REOCRD HAS SIGN OF ANESTHESIOLOGIST (IF NOT THEN SET $chkAnesSignColor TO '')
		if($chkAnesSignColor!=''){
			$chkSignLocalAnesQry = "SELECT * FROM localanesthesiarecord WHERE confirmation_id='".$chkAnesConfId."'";
			$chkSignLocalAnesRes = imw_query($chkSignLocalAnesQry) or die($chkSignLocalAnesQry.imw_error());
			$chkSignLocalAnesNumRow = imw_num_rows($chkSignLocalAnesRes);
			
			$chkSignGeneralAnesQry = "SELECT * FROM genanesthesiarecord WHERE confirmation_id='".$chkAnesConfId."'";
			$chkSignGeneralAnesRes = imw_query($chkSignGeneralAnesQry) or die($chkSignGeneralAnesQry.imw_error());
			$chkSignGeneralAnesNumRow = imw_num_rows($chkSignGeneralAnesRes);
			
			if($chkSignLocalAnesNumRow>0 || $chkSignGeneralAnesNumRow>0){
				if($chkSignLocalAnesNumRow>0){
					$chkSignLocalAnesRow = imw_fetch_array($chkSignLocalAnesRes);
					$LocalsignAnesthesia1Id=$chkSignLocalAnesRow['signAnesthesia1Id'];
					$LocalsignAnesthesia2Id=$chkSignLocalAnesRow['signAnesthesia2Id'];
					$LocalsignAnesthsia3Id=$chkSignLocalAnesRow['signAnesthesia3Id'];
					$form_status_localAnes=$chkSignLocalAnesRow['form_status'];
					
					$allLocalSignAnesthsia='';
					$localAnesFormNotInUse='';
					if($LocalsignAnesthesia1Id && $LocalsignAnesthesia2Id && $LocalsignAnesthsia3Id) {
						$allLocalSignAnesthsia='true';
					}
					
					if($allLocalSignAnesthsia=='true' && ($form_status_localAnes=='not completed' || $form_status_localAnes=='')) {
						$chkAnesSignColor='red';
					}else if($allLocalSignAnesthsia!='true' && ($form_status_localAnes=='not completed' || $form_status_localAnes=='completed')) {
						$chkAnesSignColor='';
					}else if($allLocalSignAnesthsia!='true' && $form_status_localAnes!='completed' && $form_status_localAnes!='not completed') {
						$localAnesFormNotInUse=true;
					}
					
				}
				if($chkAnesSignColor!=''){
					if($chkSignGeneralAnesNumRow>0){
						$chkSignGeneralAnesRow = imw_fetch_array($chkSignGeneralAnesRes);
						$form_status_GeneralAnes=$chkSignGeneralAnesRow['form_status'];
						$GeneralsignAnesthesia1Id=$chkSignGeneralAnesRow['signAnesthesia1Id'];
						
						if($GeneralsignAnesthesia1Id && ($form_status_GeneralAnes=='not completed' || $form_status_GeneralAnes=='')) {
							$chkAnesSignColor='red';
						}else if(!$GeneralsignAnesthesia1Id && ($form_status_GeneralAnes=='not completed' || $form_status_GeneralAnes=='completed')) {
							$chkAnesSignColor='';
						}else if(!$GeneralsignAnesthesia1Id && $form_status_GeneralAnes!='not completed' && $form_status_GeneralAnes!='completed' && $localAnesFormNotInUse==true) {
							$chkAnesSignColor='';
						}
					}
				}
			}else{
				$chkAnesSignColor='';
			}
		}
	}
	//START COMMON CHECKING OF 'ANESTHESIOLOGIST SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
	if($chkAnesSignColor!='') {
		$chkConsentSignAllAnesthesiaQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$chkAnesConfId."' AND signAnesthesia1Activate='yes' AND consent_purge_status!='true'";
		$chkConsentSignAllAnesthesiaRes = imw_query($chkConsentSignAllAnesthesiaQry) or die($chkConsentSignAllAnesthesiaQry.imw_error());
		$chkConsentAllSignAnesthesiaNumRow = imw_num_rows($chkConsentSignAllAnesthesiaRes);
		if($chkConsentAllSignAnesthesiaNumRow>0) {
			$chkConsentSignAnesthesia1Activate='';
			while($chkConsentSignAnesthesiaRow=imw_fetch_array($chkConsentSignAllAnesthesiaRes)) {
				$chkConsentSignAnesthesia1Activate=$chkConsentSignAnesthesiaRow['signAnesthesia1Activate'];
				$chkConsentSignAnesthesia1Id=$chkConsentSignAnesthesiaRow['signAnesthesia1Id'];
				$chkConsentFrmFormAnesthesiaStatus=$chkConsentSignAnesthesiaRow['form_status'];
				
				if($chkConsentSignAnesthesia1Id=='0' || $chkConsentSignAnesthesia1Id=='') {
					$chkAnesSignColor='';
				}else if(($chkConsentSignAnesthesia1Id!='0' && $chkConsentSignAnesthesia1Id!='') && $chkConsentFrmFormAnesthesiaStatus=='completed'  && $chkAnesSignColor=='') {
					$chkAnesSignColor='';
				}else if(($chkConsentSignAnesthesia1Id!='0' && $chkConsentSignAnesthesia1Id!='') && $chkConsentFrmFormAnesthesiaStatus=='not completed' && $chkAnesSignColor=='') {
					$chkAnesSignColor='';
				}else if(($chkConsentSignAnesthesia1Id!='0' && $chkConsentSignAnesthesia1Id!='') && $chkConsentFrmFormAnesthesiaStatus=='completed'  && $chkAnesSignColor=='red') {
					$chkAnesSignColor='red';
				}else if(($chkConsentSignAnesthesia1Id!='0' && $chkConsentSignAnesthesia1Id!='') && $chkConsentFrmFormAnesthesiaStatus=='not completed') {
					$chkAnesSignColor='red';
				}
			}
		
		}
	}	
	//END COMMON CHECKING OF 'ANESTHESIOLOGIST SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
			
	return $chkAnesSignColor;
}	
//END FUNCTION TO CHECK ALL SIGN OF Anes

function chkNurseSignNew($chkNurseConfId) {
	$chkSignNurseArr = array('preopnursingrecord', 'postopnursingrecord', 'preopphysicianorders', 'postopphysicianorders');
	$chkNurseSignColor='green';
	$chkNurseRecordExist=false;
	
	
	//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
	$getNurseLaserConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  patientConfirmationId='".$chkNurseConfId."'";
	$getNurseLaserConfirmationDetailRes = imw_query($getNurseLaserConfirmationDetailQry) or die(imw_error()); 
	$getNurseLaserConfirmationDetailNumRow = imw_num_rows($getNurseLaserConfirmationDetailRes);
	if($getNurseLaserConfirmationDetailNumRow>0) {
		$getNurseLaserConfirmationDetailRow = imw_fetch_array($getNurseLaserConfirmationDetailRes);
		$laserNurseConfirmationPrimaryProcedureId = $getNurseLaserConfirmationDetailRow["patient_primary_procedure_id"]; 
		$laserCatIdNurseDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$laserNurseConfirmationPrimaryProcedureId."'";
		$laserCatIdNurseDetailRes = imw_query($laserCatIdNurseDetailQry) or die(imw_error()); 
		$laserCatIdNurseDetailNumRow = imw_num_rows($laserCatIdNurseDetailRes);
		if($laserCatIdNurseDetailNumRow>0) {
			$laserCatIdNurseDetailRow = imw_fetch_array($laserCatIdNurseDetailRes);
			$laserCatIdNurse = $laserCatIdNurseDetailRow['catId'];
		}
	}
	//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
	
	if($laserCatIdNurse=='2') {	//IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN (CHECK SIGN AND VERIFIED BY NURSE IN QUERY)
		$chkLaserChartNurseRecordQry = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='".$chkNurseConfId."' AND signNurseId!='0' AND signNurseId!='' AND verified_nurse_name!=''";
		$chkLaserChartNurseRecordRes = imw_query($chkLaserChartNurseRecordQry) or die($chkLaserChartNurseRecordQry.imw_error());
		$chkLaserChartNurseRecordNumRow = imw_num_rows($chkLaserChartNurseRecordRes);
		if($chkLaserChartNurseRecordNumRow>0) {
			$chkLaserChartNurseRecordRow = imw_fetch_array($chkLaserChartNurseRecordRes);
			$chkLaserChartNurseSignNurse1Id = $chkLaserChartNurseRecordRow['signNurseId'];
			$chkLaserChartNurseFormStatus = $chkLaserChartNurseRecordRow['form_status'];
			if($chkLaserChartNurseFormStatus=='not completed' || $chkLaserChartNurseFormStatus=='') {
				$chkNurseSignColor='red';
			}
		}else {
			$chkNurseSignColor='';
		}
	}else { //ELSE
		foreach($chkSignNurseArr as $chkSignNurseArrTableName){

			if($chkSignNurseArrTableName == "preopphysicianorders" || $chkSignNurseArrTableName == "postopphysicianorders" ) {
				$signNurseconfirmation_id = 'patient_confirmation_id'; 
			}else if($chkSignNurseArrTableName == "preopnursingrecord" || $chkSignNurseArrTableName == "postopnursingrecord") {
				$signNurseconfirmation_id = 'confirmation_id';
			}
			
			//CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  NURSE SIGN COLOR FOR STUB TABLE)
			$chkPatientChartNurseRecordQry = "SELECT * FROM $chkSignNurseArrTableName WHERE $signNurseconfirmation_id='".$chkNurseConfId."' AND signNurseId!='0' AND signNurseId!=''";
			$chkPatientChartNurseRecordRes = imw_query($chkPatientChartNurseRecordQry) or die($chkPatientChartNurseRecordQry.imw_error());
			$chkPatientChartNurseRecordNumRow = imw_num_rows($chkPatientChartNurseRecordRes);
			
			$chkPatientChartNurseFormStatus='';
			if($chkPatientChartNurseRecordNumRow>0) {
				$chkNurseRecordExist = true;
				$chkPatientChartNurseRecordRow = imw_fetch_array($chkPatientChartNurseRecordRes);
				$chkPatientChartNurseFormStatus = $chkPatientChartNurseRecordRow['form_status'];
				
				if(($chkPatientChartNurseFormStatus=='not completed' || $chkPatientChartNurseFormStatus=='') && $chkNurseSignColor=='') {
					$chkNurseSignColor='';
				}else if($chkPatientChartNurseFormStatus=='not completed' || $chkPatientChartNurseFormStatus=='') {
					$chkNurseSignColor='red';
				}else if($chkPatientChartNurseFormStatus=='completed' && $chkNurseSignColor=='') {
					$chkNurseSignColor='';
				}
			}else {
				$chkNurseSignColor='';
			}
			//END CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  NURSE SIGN COLOR FOR STUB TABLE)
		}
		//START CHECK SIGN FOR PRE-OP HEALTH QUESTIONAIRE (OPTIONAL) 
		if($chkNurseSignColor!='') {
			$chkNurseSignHealthQuestQry = "SELECT * FROM preophealthquestionnaire WHERE confirmation_id='".$chkNurseConfId."'";
			$chkNurseSignHealthQuestRes = imw_query($chkNurseSignHealthQuestQry) or die($chkNurseSignHealthQuestQry.imw_error());
			$chkNurseSignHealthQuestNumRow = imw_num_rows($chkNurseSignHealthQuestRes);
			if($chkNurseSignHealthQuestNumRow>0) {
				$chkNurseSignHealthQuestRow = imw_fetch_array($chkNurseSignHealthQuestRes);
				$chkHealthQuestNurseFormStatus=$chkNurseSignHealthQuestRow['form_status'];	
				$chkHealthQuestSignNurseId=$chkNurseSignHealthQuestRow['signNurseId'];	
				if($chkHealthQuestSignNurseId && ($chkHealthQuestNurseFormStatus=='not completed' || $chkHealthQuestNurseFormStatus=='')) {
					$chkNurseSignColor='red';
				}else if(!$chkHealthQuestSignNurseId && ($chkHealthQuestNurseFormStatus=='not completed' || $chkHealthQuestNurseFormStatus=='completed')) {
					$chkNurseSignColor='';
				}				
			}
		}
		//END CHECK SIGN FOR PRE-OP HEALTH QUESTIONAIRE (OPTIONAL)
		
		//START CHECK MAC REGIONAL FORM IS IN USE OR NOT
		if($chkNurseSignColor!='') {
			 
			 
			$chkLocalAnesFormInUseQry = "SELECT * FROM localanesthesiarecord WHERE ((signAnesthesia1Id!='0' AND signAnesthesia2Id!='0' AND signAnesthesia3Id!='0') OR form_status='not completed' OR form_status='completed') AND confirmation_id='".$chkAnesConfId."'";
			$chkLocalAnesFormInUseRes = imw_query($chkLocalAnesFormInUseQry) or die(imw_error());
			$chkLocalAnesFormInUseNumRow = imw_num_rows($chkLocalAnesFormInUseRes);
			if($chkLocalAnesFormInUseNumRow>0) {
				//DO NOTHING
			}else {
				//MAKE 'FOR LOOP' TO CHECK 'PRE-OPGENRAL','GENERAL' AND 'GENERAL NURSE NOTES' IS IN USE
				
				$chkSignAnesAllGeneralArr = array('preopgenanesthesiarecord ', 'genanesthesiarecord', 'genanesthesianursesnotes');
				$preOpGenAnesFormInUse='';
				foreach($chkSignAnesAllGeneralArr as $chkSignAnesAllGeneralArrTableName){
			
					$chkPatientAnesAllGeneralRecordQry = "SELECT * FROM $chkSignAnesAllGeneralArrTableName WHERE confirmation_id='".$chkNurseConfId."'";
					$chkPatientAnesAllGeneralRecordRes = imw_query($chkPatientAnesAllGeneralRecordQry) or die(imw_error());
					$chkPatientAnesAllGeneralRecordNumRow = imw_num_rows($chkPatientAnesAllGeneralRecordRes);
					if($chkPatientAnesAllGeneralRecordNumRow>0) {
						$chkPatientAnesAllGeneralRecordRow = imw_fetch_array($chkPatientAnesAllGeneralRecordRes);
						$chkPatientAnesAllGeneralFormStatus = $chkPatientAnesAllGeneralRecordRow['form_status'];
						
						if($chkSignAnesAllGeneralArrTableName=='preopgenanesthesiarecord') {
							if($chkPatientAnesAllGeneralFormStatus=='not completed' || $chkPatientAnesAllGeneralFormStatus=='completed') {
								$chkPreOpGenAnesFormInUse=true;
							}
						}
						if($chkSignAnesAllGeneralArrTableName=='genanesthesiarecord') {
							$chkPatientAnesAllSignAnesthesia1Id = $chkPatientAnesAllGeneralRecordRow['signAnesthesia1Id'];
							if($chkPatientAnesAllSignAnesthesia1Id || $chkPatientAnesAllGeneralFormStatus=='not completed' || $chkPatientAnesAllGeneralFormStatus=='completed') {
								$chkGenAnesFormInUse=true;
							}
						}
						if($chkSignAnesAllGeneralArrTableName=='genanesthesianursesnotes') {
							$chkPatientAnesAllSignNurseId = $chkPatientAnesAllGeneralRecordRow['signNurseId'];
							if($chkPatientAnesAllSignNurseId || $chkPatientAnesAllGeneralFormStatus=='not completed' || $chkPatientAnesAllGeneralFormStatus=='completed') {
								if($chkPatientAnesAllSignNurseId && ($chkPatientAnesAllGeneralFormStatus=='not completed' || $chkPatientAnesAllGeneralFormStatus=='')) {
									$chkNurseSignColor='red';
								}else if(!$chkPatientAnesAllSignNurseId && ($chkPatientAnesAllGeneralFormStatus=='not completed' || $chkPatientAnesAllGeneralFormStatus=='completed')) {
									$chkNurseSignColor='';
								}else if(!$chkPatientAnesAllSignNurseId && $chkPatientAnesAllGeneralFormStatus!='not completed' && $chkPatientAnesAllGeneralFormStatus!='completed' && ($chkPreOpGenAnesFormInUse==true || $chkGenAnesFormInUse==true)) {
									$chkNurseSignColor='';
								}
							
							}
						}
					}
				}
			}
		}
		//END CHECK MAC REGIONAL FORM IS IN USE OR NOT
	
		//CHECK IF NURSE VARIFIED THE OPERATING ROOM RECORD OR NOT
		if($chkNurseSignColor!='') {
			$chkOproomNurseCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE  (signNurse1Id!='0' AND signNurse1Id!='') AND verifiedbyNurse='Yes' AND confirmation_id='".$chkNurseConfId."'";
			$chkOproomNurseCheckMarkRes = imw_query($chkOproomNurseCheckMarkQry) or die(imw_error());
			$chkOproomNurseCheckMarkNumRow = imw_num_rows($chkOproomNurseCheckMarkRes);
			if($chkOproomNurseCheckMarkNumRow>0) {
				$chkOproomNurseCheckMarkRow = imw_fetch_array($chkOproomNurseCheckMarkRes);
				$chkOproomSignVerifybyNurse = $chkOproomNurseCheckMarkRow['verifiedbyNurse'];
				$chkOproomSignNurseFormStatus = $chkOproomNurseCheckMarkRow['form_status'];
				$chkOproom_iol_na = $chkOproomNurseCheckMarkRow['iol_na'];
				
				$chkOproomSignNurseId = $chkOproomNurseCheckMarkRow['signNurseId'];
				$chkOproomSignNurse1Id = $chkOproomNurseCheckMarkRow['signNurse1Id'];
				
				if(!$chkOproom_iol_na && !$chkOproomSignNurseId) {
					$chkNurseSignColor='';
				}else if($chkOproomSignNurseFormStatus=='not completed') {
					$chkNurseSignColor='red';
				}
			}
			else{
				$chkNurseSignColor='';
			}
		}
		//END CHECK IF NURSE VARIFIED THE OPERATING ROOM RECORD OR NOT
	}
	
	//START COMMON CHECKING OF 'NURSE SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
	if($chkNurseSignColor!='') {
		$chkConsentSignAllNurseQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$chkNurseConfId."' AND signNurseActivate='yes' AND consent_purge_status!='true'";
		$chkConsentSignAllNurseRes = imw_query($chkConsentSignAllNurseQry) or die($chkConsentSignAllNurseQry.imw_error());
		$chkConsentAllSignNurseNumRow = imw_num_rows($chkConsentSignAllNurseRes);
		if($chkConsentAllSignNurseNumRow>0) {
			$chkConsentSignNurse1Activate='';
			while($chkConsentSignNurseRow=imw_fetch_array($chkConsentSignAllNurseRes)) {
				$chkConsentSignNurse1Activate=$chkConsentSignNurseRow['signNurseActivate'];
				$chkConsentSignNurse1Id=$chkConsentSignNurseRow['signNurseId'];
				$chkConsentFrmFormNurseStatus=$chkConsentSignNurseRow['form_status'];
				
				if($chkConsentSignNurse1Id=='0' || $chkConsentSignNurse1Id=='') {
					$chkNurseSignColor='';
				}else if(($chkConsentSignNurse1Id!='0' && $chkConsentSignNurse1Id!='') && $chkConsentFrmFormNurseStatus=='completed'  && $chkNurseSignColor=='') {
					$chkNurseSignColor='';
				}else if(($chkConsentSignNurse1Id!='0' && $chkConsentSignNurse1Id!='') && $chkConsentFrmFormNurseStatus=='not completed' && $chkNurseSignColor=='') {
					$chkNurseSignColor='';
				}else if(($chkConsentSignNurse1Id!='0' && $chkConsentSignNurse1Id!='') && $chkConsentFrmFormNurseStatus=='completed'  && $chkNurseSignColor=='red') {
					$chkNurseSignColor='red';
				}else if(($chkConsentSignNurse1Id!='0' && $chkConsentSignNurse1Id!='') && $chkConsentFrmFormNurseStatus=='not completed') {
					$chkNurseSignColor='red';
				}
			}
		
		}
	}
	//END COMMON CHECKING OF 'NURSE SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
	
	
	return $chkNurseSignColor;	
}
function getUserSubTypeFun($subUserId) {	
	$getUserSubType='';
	$getUserSubTypeQry = "SELECT  * FROM `users` WHERE usersId = '".$subUserId."'";
	$getUserSubTypeRes = imw_query($getUserSubTypeQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
	$getUserSubTypeNumRow = imw_num_rows($getUserSubTypeRes);
	if($getUserSubTypeNumRow>0) {
		$getUserSubTypeRow = imw_fetch_array($getUserSubTypeRes);
		$getUserSubType = $getUserSubTypeRow['user_sub_type'];
	}
	return $getUserSubType;
}	
?>