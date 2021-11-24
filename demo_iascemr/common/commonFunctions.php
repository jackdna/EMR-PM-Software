<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$get_http_path = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
//START COLOR CODE TO SET BACKGROUND COLOR FOR THE SIGNATURES USED THRU AJAX FILES
$chngBckGroundColorAjax='background-color:#F6C67A';
//END COLOR CODE TO SET BACKGROUND COLOR FOR THE SIGNATURES USED THRU AJAX FILES
function getUsrNm($id,$returnString = false) {
	$nurseName="";
	if($id)	 {
		$userNurseQry	= imw_query("select * from users where usersId='".$id."'");
		$userNurseRow	= imw_fetch_array($userNurseQry);
		$nurseName 		= $userNurseRow['lname'].", ".$userNurseRow['fname']." ".$userNurseRow['mname'];
	}
	$usrArr = $returnString ? $nurseName : array($nurseName);
	return $usrArr;
}

function getData($req, $table, $cond, $match){
	$strQry = imw_query("SELECT  $req FROM $table WHERE $cond = '$match'");
	$strRow = imw_fetch_assoc($strQry);
	return $strRow[$req];
}
function getPracticeUser($practiceName,$andCond,$usrDot) {
	$strQuery1Part=$usrDotCon='';
	$fieldName = "practiceName";
	if($practiceName){
		$practiceNameArr = explode(",",$practiceName);
		
		if($usrDot){
			$fieldName=$usrDot.".practiceName";	
		}
		foreach($practiceNameArr as $prNme){
			if(trim($prNme)) {
				$strQuery1Part .= " ".$prNme." IN($fieldName) OR ";
			}
		}
		if($strQuery1Part != ''){
			$strQuery1Part = substr($strQuery1Part,0,-4);
			$strQuery1Part = " ".$andCond." (".$strQuery1Part.")";
		}
	}else {
		$strQuery1Part = " '' = $fieldName";	
		$strQuery1Part = " ".$andCond." (".$strQuery1Part.")";
	}
	return $strQuery1Part;
}
// GETTING LOGGED IN USER SUB TYPE
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
// GETTING LOGGED IN USER TYPE

//FUNCTION TO CHECK ALL SIGN OF SURGEON
function chkSurgeonSignNew($chkSurgeonConfId) {
$chkSurgeonSignColor='green';

//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
$getLaserConfirmationDetailQry = "SELECT PC.* FROM `patientconfirmation` PC WHERE  PC.patientConfirmationId='".$chkSurgeonConfId."' ";
$getLaserConfirmationDetailRes = imw_query($getLaserConfirmationDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
$getLaserConfirmationDetailNumRow = imw_num_rows($getLaserConfirmationDetailRes);
if($getLaserConfirmationDetailNumRow>0) {
	$getLaserConfirmationDetailRow = imw_fetch_array($getLaserConfirmationDetailRes);
	$laserConfirmationPrimaryProcedureId = $getLaserConfirmationDetailRow["patient_primary_procedure_id"]; 
	$laserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$laserConfirmationPrimaryProcedureId."'";
	$laserCatIdDetailRes = imw_query($laserCatIdDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
	$laserCatIdDetailNumRow = imw_num_rows($laserCatIdDetailRes);
	if($laserCatIdDetailNumRow>0) {
		$laserCatIdDetailRow = imw_fetch_array($laserCatIdDetailRes);
		$laserCatId = $laserCatIdDetailRow['catId'];
	}

	// Start Code to check if procedure is injection/Misc
	$primary_procedure_is_inj_misc	=	$getLaserConfirmationDetailRow['prim_proc_is_misc'];
	if($laserCatId <> '2' )
	{
		if($primary_procedure_is_inj_misc == '')
		{
			$chkProcedureCatQry	=	"Select isMisc,isInj From procedurescategory Where proceduresCategoryId = '".$laserCatId."'  ";
			$chkProcedureCatSql	=	imw_query($chkProcedureCatQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
			$chkProcedureCatRow	=	imw_fetch_assoc($chkProcedureCatSql);
		
			$primary_procedure_is_inj_misc	=	'';
			if($chkProcedureCatRow['isInj'])				$primary_procedure_is_inj_misc	=	'injection';
			elseif($chkProcedureCatRow['isMisc'])		$primary_procedure_is_inj_misc	=	'misc';
		}
		
	}else
	{
		$primary_procedure_is_inj_misc	=	'';
	}
	//End Code to check if procedure is injection/Misc
	
}
//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE


$chkSignArr = array('preopphysicianorders', 'postopphysicianorders','operativereport', 'dischargesummarysheet');

if($laserCatId=='2') {	//IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN
	$chkLaserSignArr = array('laser_procedure_patient_table', 'dischargesummarysheet');
	foreach($chkLaserSignArr as $chkLaserSignArrTableName){
		$chkAndLaserChartRecordQry='';
		if($chkLaserSignArrTableName=='laser_procedure_patient_table') {//CHECK VERIFIED BY SURGEON ALSO
			$chkAndLaserChartRecordQry=" AND verified_surgeon_Id!='0' AND verified_surgeon_Id!=''";
		}
		$chkLaserChartRecordQry = "SELECT * FROM $chkLaserSignArrTableName WHERE confirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Id!='0' AND signSurgeon1Id!='' $chkAndLaserChartRecordQry";
		$chkLaserChartRecordRes = imw_query($chkLaserChartRecordQry) or die($chkPatientChartRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
}
else if($laserCatId <> '2' && $primary_procedure_is_inj_misc) { //ELSE
	$chkInjectionSignArr = array('injection','operativereport','dischargesummarysheet');
	
	foreach($chkInjectionSignArr as $chkSignArrTableName){
		$signUserconfirmation_id = 'confirmation_id';
		//CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
		$chkPatientChartRecordQry = "SELECT * FROM $chkSignArrTableName WHERE $signUserconfirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Id!='0' AND signSurgeon1Id!=''";
		$chkPatientChartRecordRes = imw_query($chkPatientChartRecordQry) or die($chkPatientChartRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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

}
else { //ELSE
	
	foreach($chkSignArr as $chkSignArrTableName){
		
		if($chkSignArrTableName == "preopphysicianorders" || $chkSignArrTableName == "postopphysicianorders") {
			$signUserconfirmation_id = 'patient_confirmation_id';
		}else if($chkSignArrTableName == "operativereport" || $chkSignArrTableName == "dischargesummarysheet") {
			$signUserconfirmation_id = 'confirmation_id';
		}
		//CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
		
		$chkPatientChartRecordQry = "SELECT * FROM $chkSignArrTableName WHERE $signUserconfirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Id!='0' AND signSurgeon1Id!=''";
		$chkPatientChartRecordRes = imw_query($chkPatientChartRecordQry) or die($chkPatientChartRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
		$chkOproomSurgeonCheckMarkRes = imw_query($chkOproomSurgeonCheckMarkQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
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

//START CHECK SURGEON SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL) 
	$dirName			=	'H&P' ; 	
	$scanDirQry		= "Select sut.scan_upload_id From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '".$chkSurgeonConfId."' And sut.confirmation_id 	= '".$chkSurgeonConfId."' And sd.document_name = '".$dirName."' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
	$scanDirSql		=	imw_query($scanDirQry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
	$scanDirNum	=	imw_num_rows($scanDirSql);
	$ekgHpCount	=	$scanDirNum;
	
	if( !$ekgHpCount)
	{
			if($chkSurgeonSignColor!='') {
				$chkSurgeonSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='".$chkSurgeonConfId."'";
				$chkSurgeonSignHistoryPhysicalRes = imw_query($chkSurgeonSignHistoryPhysicalQry) or die($chkSurgeonSignHistoryPhysicalQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
				$chkSurgeonSignHistoryPhysicalNumRow = imw_num_rows($chkSurgeonSignHistoryPhysicalRes);
				if($chkSurgeonSignHistoryPhysicalNumRow>0) {
					$chkSurgeonSignHistoryPhysicalRow = imw_fetch_array($chkSurgeonSignHistoryPhysicalRes);
					$chkHistoryPhysicalSurgeonFormStatus=$chkSurgeonSignHistoryPhysicalRow['form_status'];	
					$chkHistoryPhysicalSignSurgeon1Id=$chkSurgeonSignHistoryPhysicalRow['signSurgeon1Id'];	
					if($chkHistoryPhysicalSignSurgeon1Id && ($chkHistoryPhysicalSurgeonFormStatus=='not completed')) {// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
						$chkSurgeonSignColor='red';
					}else if(!$chkHistoryPhysicalSignSurgeon1Id && ($chkHistoryPhysicalSurgeonFormStatus=='not completed' || $chkHistoryPhysicalSurgeonFormStatus=='completed')) {
						$chkSurgeonSignColor='';
					}				
				}
			}
	}
	//END CHECK SURGEON SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL)
	

// Start Common Checking of surgeon signature in Transfer & Followup Chart
if($chkSurgeonSignColor!='') 
{
	$chkSurgeonSignTFQry = "SELECT * FROM transfer_followups WHERE confirmation_id='".$chkSurgeonConfId."'";
	$chkSurgeonSignTFRes = imw_query($chkSurgeonSignTFQry) or die($chkSurgeonSignTFQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
	$chkSurgeonSignTFNumRow = imw_num_rows($chkSurgeonSignTFRes);
	if($chkSurgeonSignTFNumRow>0) 
	{
		$chkSurgeonSignTFRow = imw_fetch_array($chkSurgeonSignTFRes);
		$chkTFSurgeonFormStatus=$chkSurgeonSignTFRow['form_status'];	
		$chkTFSignSurgeon1Id=$chkSurgeonSignTFRow['signSurgeon1Id'];	
		if($chkTFSignSurgeon1Id && ($chkTFSurgeonFormStatus=='not completed')) 
		{// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
			$chkSurgeonSignColor='red';
		} 
		else if(!$chkTFSignSurgeon1Id && ($chkTFSurgeonFormStatus == 'not completed' || $chkTFSurgeonFormStatus=='completed')) 
		{
			$chkSurgeonSignColor='';
		}
	}
}
// End  Common Checking of surgeon signature in Transfer & Followup Chart


//START COMMON CHECKING OF 'SURGEON SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
if($chkSurgeonSignColor!='') {
	$chkConsentSignAllQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$chkSurgeonConfId."' AND signSurgeon1Activate='yes' AND consent_purge_status!='true' AND (form_status ='completed' OR form_status ='not completed') ";
	$chkConsentSignAllRes = imw_query($chkConsentSignAllQry) or die($chkConsentSignAllQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
$getAnesLaserConfirmationDetailQry = "SELECT patient_primary_procedure_id,anes_NA FROM `patientconfirmation` WHERE  patientConfirmationId='".$chkAnesConfId."'";
$getAnesLaserConfirmationDetailRes = imw_query($getAnesLaserConfirmationDetailQry) or die(imw_error()); 
$getAnesLaserConfirmationDetailNumRow = imw_num_rows($getAnesLaserConfirmationDetailRes);
if($getAnesLaserConfirmationDetailNumRow>0) {
	$getAnesLaserConfirmationDetailRow = imw_fetch_array($getAnesLaserConfirmationDetailRes);
	$laserAnesConfirmationPrimaryProcedureId = $getAnesLaserConfirmationDetailRow["patient_primary_procedure_id"]; 
	$anesNA = $getAnesLaserConfirmationDetailRow["anes_NA"]; 
	$laserCatIdAnesDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$laserAnesConfirmationPrimaryProcedureId."'";
	$laserCatIdAnesDetailRes = imw_query($laserCatIdAnesDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
	$laserCatIdAnesDetailNumRow = imw_num_rows($laserCatIdAnesDetailRes);
	if($laserCatIdAnesDetailNumRow>0) {
		$laserCatIdAnesDetailRow = imw_fetch_array($laserCatIdAnesDetailRes);
		$laserCatIdAnes = $laserCatIdAnesDetailRow['catId'];
	}
	
	// Start Code to check if procedure is injection/Misc
	$primary_procedure_is_inj_misc	=	$laserCatIdAnesDetailRow['prim_proc_is_misc'];
	if($laserCatIdAnes <> '2'  )
	{
		if($primary_procedure_is_inj_misc == '')
		{
			$chkProcedureCatQry	=	"Select isMisc, isInj From procedurescategory Where proceduresCategoryId = '".$laserCatIdAnes."'  ";
			$chkProcedureCatSql	=	imw_query($chkProcedureCatQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
			$chkProcedureCatRow	=	imw_fetch_assoc($chkProcedureCatSql);
		
			$primary_procedure_is_inj_misc	=	'';
			if($chkProcedureCatRow['isInj'])				$primary_procedure_is_inj_misc	=	'injection';
			elseif($chkProcedureCatRow['isMisc'])		$primary_procedure_is_inj_misc	=	'misc';
			
		}
		
	}else
	{
		$primary_procedure_is_inj_misc	=	'';
	}
	//End Code to check if procedure is injection/Misc
	
}
//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE

if($laserCatIdAnes=='2' || $anesNA=='Yes') {	//IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' OR IF ANES. NOT REQUIRED THEN  
$chkAnesSignColor=''; //NOT REQUIRED FOR ANESTHESIOLOGIST
}
elseif(($laserCatIdAnes <> '2' && $primary_procedure_is_inj_misc) || $anesNA=='Yes') 
{	//IF CATEGORY OF PROCEDURE IS 'INJECTION PROCEDURE' OR IF ANES. NOT REQUIRED THEN  
	$chkAnesSignColor=''; //NOT REQUIRED FOR ANESTHESIOLOGIST
}
else { //ELSE	
	foreach($chkSignAnesArr as $chkSignAnesArrTableName){
		
		$chkOproomAnesCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE confirmation_id='".$chkAnesConfId."' AND verifiedbyAnesthesiologist='Yes'";
		$chkOproomAnesCheckMarkRes = imw_query($chkOproomAnesCheckMarkQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
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
		$chkSignLocalAnesRes = imw_query($chkSignLocalAnesQry) or die($chkSignLocalAnesQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkSignLocalAnesNumRow = imw_num_rows($chkSignLocalAnesRes);
		
		$chkSignGeneralAnesQry = "SELECT * FROM genanesthesiarecord WHERE confirmation_id='".$chkAnesConfId."'";
		$chkSignGeneralAnesRes = imw_query($chkSignGeneralAnesQry) or die($chkSignGeneralAnesQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkSignGeneralAnesNumRow = imw_num_rows($chkSignGeneralAnesRes);
		
		if($chkSignLocalAnesNumRow>0 || $chkSignGeneralAnesNumRow>0){
			if($chkSignLocalAnesNumRow>0){
				$chkSignLocalAnesRow 	= imw_fetch_array($chkSignLocalAnesRes);
				$LocalsignAnesthesia1Id	= $chkSignLocalAnesRow['signAnesthesia1Id'];
				$LocalsignAnesthesia2Id	= $chkSignLocalAnesRow['signAnesthesia2Id'];
				$LocalsignAnesthesia4Id	= $chkSignLocalAnesRow['signAnesthesia4Id'];
				$anes_ScanUploadPath		= $chkSignLocalAnesRow['anes_ScanUploadPath'];
				$anes_ScanUpload				= $chkSignLocalAnesRow['anes_ScanUpload'];
				$form_status_localAnes	= $chkSignLocalAnesRow['form_status'];
				$version_num_local_anes	=	$chkSignLocalAnesRow['version_num'];
				
				$allLocalSignAnesthsia	= '';
				$localAnesFormNotInUse	= '';
				
				if($version_num_local_anes == '2' || ($version_num_local_anes = 0 && $form_status_localAnes == ''))
				{
					if($anes_ScanUploadPath || $anes_ScanUpload || ($LocalsignAnesthesia1Id && $LocalsignAnesthesia2Id && $LocalsignAnesthesia4Id))
					{
						$allLocalSignAnesthsia='true';
					}
				}
				else
				{
					if($anes_ScanUploadPath || $anes_ScanUpload || ($LocalsignAnesthesia1Id && $LocalsignAnesthesia2Id))
					{
						$allLocalSignAnesthsia='true';
					}	
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
					$GeneralsignAnesthesia2Id=$chkSignGeneralAnesRow['signAnesthesia2Id'];
					$GeneralsignAnesVersionNum=$chkSignGeneralAnesRow['version_num'];
					
					$allGeneralSignAnesthsia	=	'';
					if($GeneralsignAnesVersionNum == '2' || ($GeneralsignAnesVersionNum = 0 && $form_status_GeneralAnes == ''))
					{
						if($GeneralsignAnesthesia1Id && $GeneralsignAnesthesia2Id )
						{
							$allGeneralSignAnesthsia='true';
						}
					}
					else
					{
						if($GeneralsignAnesthesia1Id )
						{
							$allGeneralSignAnesthsia='true';
						}	
					}
				
				
					if($allGeneralSignAnesthsia=='true' && ($form_status_GeneralAnes=='not completed' || $form_status_GeneralAnes=='')) {
						$chkAnesSignColor='red';
					}else if($allGeneralSignAnesthsia!='true' && ($form_status_GeneralAnes=='not completed' || $form_status_GeneralAnes=='completed')) {
						$chkAnesSignColor='';
					}else if($allGeneralSignAnesthsia!='true' && $form_status_GeneralAnes!='completed' && $form_status_GeneralAnes!='not completed' && $localAnesFormNotInUse==true) {
						$chkAnesSignColor = '';
					}
				
				}
			}
		}else{
			$chkAnesSignColor='';
		}
	}
	
}

//START COMMON CHECK Anesthesiologist SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL) 
	$dirName			=	'H&P' ; 	
	$scanDirQry		= "Select sut.scan_upload_id From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '".$chkAnesConfId."' And sut.confirmation_id 	= '".$chkAnesConfId."' And sd.document_name = '".$dirName."' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
	$scanDirSql		=	imw_query($scanDirQry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
	$scanDirNum	=	imw_num_rows($scanDirSql);
	$ekgHpCount		=	$scanDirNum;
	
	if( !$ekgHpCount)
	{
		if($chkAnesSignColor!='') {
		$chkAnesthesiaSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='".$chkAnesConfId."'";
		$chkAnesthesiaSignHistoryPhysicalRes = imw_query($chkAnesthesiaSignHistoryPhysicalQry) or die($chkAnesthesiaSignHistoryPhysicalQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkAnesthesiaSignHistoryPhysicalNumRow = imw_num_rows($chkAnesthesiaSignHistoryPhysicalRes);
		if($chkAnesthesiaSignHistoryPhysicalNumRow>0) {
			$chkAnesthesiaSignHistoryPhysicalRow = imw_fetch_array($chkAnesthesiaSignHistoryPhysicalRes);
			$chkHistoryPhysicalAnesthesiaFormStatus=$chkAnesthesiaSignHistoryPhysicalRow['form_status'];	
			$chkHistoryPhysicalSignAnesthesia1Id=$chkAnesthesiaSignHistoryPhysicalRow['signAnesthesia1Id'];	
			if($chkHistoryPhysicalSignAnesthesia1Id && ($chkHistoryPhysicalAnesthesiaFormStatus=='not completed')) {// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
				$chkAnesSignColor='red';
			}else if(!$chkHistoryPhysicalSignAnesthesia1Id && ($chkHistoryPhysicalAnesthesiaFormStatus=='not completed' || $chkHistoryPhysicalAnesthesiaFormStatus=='completed')) {
				$chkAnesSignColor='';
			}				
		}
	}
	}
//END CHECK SURGEON SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL)

	
//START COMMON CHECKING OF 'ANESTHESIOLOGIST SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
if($chkAnesSignColor!='') {
	$chkConsentSignAllAnesthesiaQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$chkAnesConfId."' AND signAnesthesia1Activate='yes' AND consent_purge_status!='true' AND (form_status ='completed' OR form_status ='not completed') ";
	$chkConsentSignAllAnesthesiaRes = imw_query($chkConsentSignAllAnesthesiaQry) or die($chkConsentSignAllAnesthesiaQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
$getNurseLaserConfirmationDetailRes = imw_query($getNurseLaserConfirmationDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
$getNurseLaserConfirmationDetailNumRow = imw_num_rows($getNurseLaserConfirmationDetailRes);
if($getNurseLaserConfirmationDetailNumRow>0) {
	$getNurseLaserConfirmationDetailRow = imw_fetch_array($getNurseLaserConfirmationDetailRes);
	$laserNurseConfirmationPrimaryProcedureId = $getNurseLaserConfirmationDetailRow["patient_primary_procedure_id"]; 
	$laserCatIdNurseDetailQry = "SELECT * FROM `procedures` WHERE procedureId='".$laserNurseConfirmationPrimaryProcedureId."'";
	$laserCatIdNurseDetailRes = imw_query($laserCatIdNurseDetailQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
	$laserCatIdNurseDetailNumRow = imw_num_rows($laserCatIdNurseDetailRes);
	if($laserCatIdNurseDetailNumRow>0) {
		$laserCatIdNurseDetailRow = imw_fetch_array($laserCatIdNurseDetailRes);
		$laserCatIdNurse = $laserCatIdNurseDetailRow['catId'];
	}
	
	// Start Code to check if procedure is injection/Misc
	$primary_procedure_is_inj_misc	=	$laserCatIdNurseDetailRow['prim_proc_is_misc'];
	if($laserCatIdNurse <> '2' )
	{
		if($primary_procedure_is_inj_misc == '')
		{
			$chkProcedureCatQry	=	"Select isMisc, isInj From procedurescategory Where proceduresCategoryId = '".$laserCatIdNurse."'  ";
			$chkProcedureCatSql	=	imw_query($chkProcedureCatQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error()); 
			$chkProcedureCatRow	=	imw_fetch_assoc($chkProcedureCatSql);
			
			$primary_procedure_is_inj_misc	=	'';
			if($chkProcedureCatRow['isInj'])				$primary_procedure_is_inj_misc	=	'injection';
			elseif($chkProcedureCatRow['isMisc'])		$primary_procedure_is_inj_misc	=	'misc';
		}
	}else
	{
		$primary_procedure_is_inj_misc	=	'';
	}
	//End Code to check if procedure is injection/Misc
	
}
//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE

if($laserCatIdNurse=='2') {	//IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN (CHECK SIGN AND VERIFIED BY NURSE IN QUERY)
	$chkLaserChartNurseRecordQry = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='".$chkNurseConfId."' AND signNurseId!='0' AND signNurseId!='' AND verified_nurse_name!=''";
	$chkLaserChartNurseRecordRes = imw_query($chkLaserChartNurseRecordQry) or die($chkLaserChartNurseRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
}
elseif($laserCatIdNurse <> '2' && $primary_procedure_is_inj_misc)
{	//IF CATEGORY OF PROCEDURE IS 'INJECTION PROCEDURE' THEN (CHECK SIGN AND VERIFIED BY NURSE IN QUERY)
	$chkLaserChartNurseRecordQry = "SELECT * FROM injection WHERE confirmation_id='".$chkNurseConfId."' AND signNurse2Id!='0' AND signNurse2Id!='' AND  signNurse1Id!='0' AND signNurse1Id!='' ";
	$chkLaserChartNurseRecordRes = imw_query($chkLaserChartNurseRecordQry) or die($chkLaserChartNurseRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
}
else { //ELSE
	foreach($chkSignNurseArr as $chkSignNurseArrTableName){

		if($chkSignNurseArrTableName == "preopphysicianorders" || $chkSignNurseArrTableName == "postopphysicianorders" ) {
			$signNurseconfirmation_id = 'patient_confirmation_id'; 
		}else if($chkSignNurseArrTableName == "preopnursingrecord" || $chkSignNurseArrTableName == "postopnursingrecord") {
			$signNurseconfirmation_id = 'confirmation_id';
		}
		$nurse2check="";
		$chartVersionNum = '';
		if($chkSignNurseArrTableName == "preopphysicianorders" || $chkSignNurseArrTableName == "postopphysicianorders")
		{
			
			
			$tblQry	=	"Select version_num From ".$chkSignNurseArrTableName." Where $signNurseconfirmation_id='".$chkNurseConfId."' ";
			$tblSql	=	imw_query($tblQry); 
			$tblRow	=	imw_fetch_object($tblSql);
			$chartVersionNum	=	$tblRow->version_num;
			if($chartVersionNum > 1 && $chkSignNurseArrTableName == "preopphysicianorders") {
				$nurse2check=" AND signNurse1Id!='0' AND signNurse1Id!=''";		
			}else if($chartVersionNum > 2 && $chkSignNurseArrTableName == "postopphysicianorders") {
				$nurse2check=" AND signNurse1Id!='0' AND signNurse1Id!=''";		
			}
		}
		
		//CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  NURSE SIGN COLOR FOR STUB TABLE)
		$chkPatientChartNurseRecordQry = "SELECT * FROM $chkSignNurseArrTableName WHERE $signNurseconfirmation_id='".$chkNurseConfId."' AND signNurseId!='0' AND signNurseId!='' $nurse2check";
		
		if($chkSignNurseArrTableName == "preopphysicianorders" && $chartVersionNum > 1)
		{
			$chkPatientChartNurseRecordQry = "SELECT * FROM $chkSignNurseArrTableName WHERE $signNurseconfirmation_id='".$chkNurseConfId."' $nurse2check";
		}
		
		$chkPatientChartNurseRecordRes = imw_query($chkPatientChartNurseRecordQry) or die($chkPatientChartNurseRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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
	/*
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
	}*/
	//END CHECK SIGN FOR PRE-OP HEALTH QUESTIONAIRE (OPTIONAL)

	
	//START CHECK MAC REGIONAL FORM IS IN USE OR NOT
	if($chkNurseSignColor!='') {
		 
		 
		$chkLocalAnesFormInUseQry = "SELECT * FROM localanesthesiarecord WHERE ((signAnesthesia1Id!='0' AND signAnesthesia2Id!='0' AND signAnesthesia3Id!='0') OR form_status='not completed' OR form_status='completed') AND confirmation_id='".$chkNurseConfId."'";
		$chkLocalAnesFormInUseRes = imw_query($chkLocalAnesFormInUseQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkLocalAnesFormInUseNumRow = imw_num_rows($chkLocalAnesFormInUseRes);
		if($chkLocalAnesFormInUseNumRow>0) {
			//DO NOTHING
		}else {
			//MAKE 'FOR LOOP' TO CHECK 'PRE-OPGENRAL','GENERAL' AND 'GENERAL NURSE NOTES' IS IN USE
			
			$chkSignAnesAllGeneralArr = array('preopgenanesthesiarecord ', 'genanesthesiarecord', 'genanesthesianursesnotes');
			$preOpGenAnesFormInUse='';
			foreach($chkSignAnesAllGeneralArr as $chkSignAnesAllGeneralArrTableName){
		
				$chkPatientAnesAllGeneralRecordQry = "SELECT * FROM $chkSignAnesAllGeneralArrTableName WHERE confirmation_id='".$chkNurseConfId."'";
				$chkPatientAnesAllGeneralRecordRes = imw_query($chkPatientAnesAllGeneralRecordQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
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
							}
							/* else if(!$chkPatientAnesAllSignNurseId && $chkPatientAnesAllGeneralFormStatus!='not completed' && $chkPatientAnesAllGeneralFormStatus!='completed' && ($chkPreOpGenAnesFormInUse==true || $chkGenAnesFormInUse==true)) {
								$chkNurseSignColor='';
							}*/
						
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
		$chkOproomNurseCheckMarkRes = imw_query($chkOproomNurseCheckMarkQry) or die('Error Found at line no. '.(__LINE__).': '.imw_error());
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


//START CHECK SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL) 
	$dirName			=	'H&P' ; 	
	$scanDirQry		= "Select sut.scan_upload_id From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '".$chkNurseConfId."' And sut.confirmation_id 	= '".$chkNurseConfId."' And sd.document_name = '".$dirName."' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
	$scanDirSql		=	imw_query($scanDirQry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
	$scanDirNum	=	imw_num_rows($scanDirSql);
	$ekgHpCount	=	$scanDirNum;
	
	if( !$ekgHpCount)
	{
		if($chkNurseSignColor!='') 
		{
			$chkNurseSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='".$chkNurseConfId."'";
			$chkNurseSignHistoryPhysicalRes = imw_query($chkNurseSignHistoryPhysicalQry) or die($chkNurseSignHistoryPhysicalQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
			$chkNurseSignHistoryPhysicalNumRow = imw_num_rows($chkNurseSignHistoryPhysicalRes);
			if($chkNurseSignHistoryPhysicalNumRow>0) {
				$chkNurseSignHistoryPhysicalRow = imw_fetch_array($chkNurseSignHistoryPhysicalRes);
				$chkHistoryPhysicalNurseFormStatus=$chkNurseSignHistoryPhysicalRow['form_status'];	
				$chkHistoryPhysicalSignNurseId=$chkNurseSignHistoryPhysicalRow['signNurseId'];	
				if($chkHistoryPhysicalSignNurseId && ($chkHistoryPhysicalNurseFormStatus=='not completed')) {
					$chkNurseSignColor='red';
				}else if(!$chkHistoryPhysicalSignNurseId && ($chkHistoryPhysicalNurseFormStatus=='not completed' || $chkHistoryPhysicalNurseFormStatus=='completed')) {
					$chkNurseSignColor='';
				}				
			}
		}
	}
//END CHECK SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL)
	

// Start Common Checking of nurse signatures in Transfer & Followup Chart
if($chkNurseSignColor!='') 
{
	$chkNurseSignTFQry = "SELECT * FROM transfer_followups WHERE confirmation_id='".$chkNurseConfId."'";
	$chkNurseSignTFRes = imw_query($chkNurseSignTFQry) or die($chkNurseSignTFQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
	$chkNurseSignTFNumRow = imw_num_rows($chkNurseSignTFRes);
	if($chkNurseSignTFNumRow>0) 
	{
		$chkNurseSignTFRow = imw_fetch_array($chkNurseSignTFRes);
		$chkTFNurseFormStatus=$chkNurseSignTFRow['form_status'];	
		$chkTFSignNurseId=$chkNurseSignTFRow['signNurseId'];	
		$chkTFSignNurse1Id=$chkNurseSignTFRow['signNurse1Id'];	
		
		$checkTFSignNurseStatus = false;
		if( $chkTFSignNurseId && $chkTFSignNurse1Id)
		{
			$checkTFSignNurseStatus = true;	
		}
		
		if($checkTFSignNurseStatus && ($chkTFNurseFormStatus=='not completed')) 
		{// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
			$chkNurseSignColor='red';
		} 
		else if(!$checkTFSignNurseStatus && ($chkTFNurseFormStatus == 'not completed' || $chkTFNurseFormStatus=='completed')) 
		{
			$chkNurseSignColor='';
		}
	}
}
// End  Common Checking of nurse signatures in Transfer & Followup Chart


//START COMMON CHECKING OF 'NURSE SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
$qrySC = "Select safety_check_list from surgerycenter Where surgeryCenterId = 1 LIMIT 0,1";
$sqlSC = imw_query($qrySC) or die(imw_error());
$resSc = imw_fetch_assoc($sqlSC);
// Get Form Status of check list page
$qryCHK = "SELECT form_status FROM surgical_check_list WHERE confirmation_id='".$chkNurseConfId."'";
$sqlCHK = imw_query($qryCHK) or die(imw_error());
$resCHK = imw_fetch_assoc($sqlCHK);

$showCheckListAdmin = $resSc['safety_check_list'] ? true : false;
$showCheckList = ( !$showCheckListAdmin && $resCHK['form_status'] == 'completed' || $resCHK['form_status'] == 'not completed'  ) ? true : $showCheckListAdmin ;
$showCheckListStatus = 	getChartShowStatus($chkNurseConfId,'checklist');
$showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? true : ($showCheckListStatus == 2 ? false : $showCheckList)) : $showCheckList;

if($chkNurseSignColor!='' && $showCheckList ) {
	if(constant('CHECKLIST_DATE')) {
		$chkConfirmDateQry = "SELECT patientConfirmationId FROM patientconfirmation WHERE patientConfirmationId='".$chkNurseConfId."' AND dos >= '".constant('CHECKLIST_DATE')."'";	
		$chkConfirmDateRes = imw_query($chkConfirmDateQry) or die($chkConfirmDateQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkConfirmDateNumRow = imw_num_rows($chkConfirmDateRes);
	}	
	if($chkConfirmDateNumRow>0 || !constant('CHECKLIST_DATE')) {
		$version2Query = " AND (CASE version_num WHEN '1' THEN signNurse2Id!='0' AND signNurse2Id!='' ELSE 1=1 END )";
		$chkSrgChkListNurseRecordQry = "SELECT form_status FROM surgical_check_list WHERE confirmation_id='".$chkNurseConfId."' AND signNurse1Id!='0' AND signNurse1Id!='' AND signNurse3Id!='0' AND signNurse3Id!=''  AND signNurse4Id!='0' AND signNurse4Id!='' $version2Query ";
		$chkSrgChkListNurseRecordRes = imw_query($chkSrgChkListNurseRecordQry) or die($chkSrgChkListNurseRecordQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
		$chkSrgChkListNurseRecordNumRow = imw_num_rows($chkSrgChkListNurseRecordRes);
		if($chkSrgChkListNurseRecordNumRow>0) {
			$chkSrgChkListNurseRecordRow = imw_fetch_array($chkSrgChkListNurseRecordRes);
			$chkSrgChkListNurseFormStatus = $chkSrgChkListNurseRecordRow['form_status'];
			if($chkSrgChkListNurseFormStatus=='not completed' || $chkSrgChkListNurseFormStatus=='') {
				$chkNurseSignColor='red';
			}
		}else {
			$chkNurseSignColor='';
		}
	}
}
if($chkNurseSignColor!='') {
	$chkConsentSignAllNurseQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='".$chkNurseConfId."' AND signNurseActivate='yes' AND consent_purge_status!='true' AND (form_status ='completed' OR form_status ='not completed') ";
	$chkConsentSignAllNurseRes = imw_query($chkConsentSignAllNurseQry) or die($chkConsentSignAllNurseQry.'Error Found at line no. '.(__LINE__).': '.imw_error());
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

// Function to convert words into 4 or less charaters
function subStrWord($word) {
	if($word) {
		$word = trim($word);
		$wordArr = explode(' ',$word);
		$strLabel='';
		$wordCount = count($wordArr);
		if($wordCount==1){
			for($i=0;$i<$wordCount;$i++) {
				$strLabel.=substr($wordArr[$i],0,4); //GET 4 CHARACTERS
			}
		}else if($wordCount==2){
			for($i=0;$i<$wordCount;$i++) {
				$strLabel.=substr($wordArr[$i],0,2); //GET FIRST 2 CHARACTERS OF BOTH 2 WORDS
			}
		}else if($wordCount==3){
			for($i=0;$i<$wordCount;$i++) { //GET FIRST CHARACTERS OF FIRST 2 WORDS AND FIRST 2 CHARACTERS OF 3rd WORD
				if($i==0 || $i==1) {
					$strLabel.=substr($wordArr[$i],0,1);
				}else if($i==2) {
					$strLabel.=substr($wordArr[$i],0,2);
				}
			}
		}else if($wordCount>=4){
			for($i=0;$i<4;$i++) {
				$strLabel.=substr($wordArr[$i],0,1); //GET FIRST CHARACTER OF ALL FOUR WORDS
			}
		}
		
		if($strLabel) {
			$strLabel=strtoupper($strLabel);
		}
	}
	return 	$strLabel;
}

// To get Modifiers for CPT Codes from iDoc
function idoc_modifiers()
{
	global $surgeryCenterDirectoryName;
	include ( $_SERVER['DOCUMENT_ROOT'] .'/'.$surgeryCenterDirectoryName .'/connect_imwemr.php');
	$return = array();
	$query = "Select cpt_prac_code,mod1,mod2,mod3,units From cpt_fee_tbl Where (mod1 <> '' || mod2 <> '' || mod3 <> '' || units <> '') And delete_status = 0 Order By cpt_fee_id ";
	$sql = imw_query($query) or die(imw_error());
	$cnt = imw_num_rows($sql);
	if($cnt > 0 )
	{
		while( $row = imw_fetch_object($sql) )
		{
			if(!array_key_exists($row->cpt_prac_code,$return))
				$return[$row->cpt_prac_code] = array('mod1' => $row->mod1, 'mod2' => $row->mod2, 'mod3' => $row->mod3, 'units' => $row->units);
		}
	}
	include ( $_SERVER['DOCUMENT_ROOT'] .'/'.$surgeryCenterDirectoryName  .'/common/conDb.php');
	return $return;
}
function pre($arr, $debug = 0){
	print "<pre>";
	print_r($arr);
	print "</pre>";
	if($debug == 1){
		die("Debugging");
	}
}
function getChartShowStatus($confId, $form) {
	$form = trim($form);
	$confId = (int)$confId;
	
	if( $form && $confId) {
		$arrTbl = array('checklist'=>'show_checklist');

		$fld = $arrTbl[$form];
		$qry = "SELECT ".$fld." FROM patientconfirmation WHERE patientConfirmationId = ".$confId;
		$sql = imw_query($qry) or die($qry. imw_error());
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 ) {
			$row = imw_fetch_assoc($sql);
			return $result = $row[$fld];
		}
	}
	return false;
}

function procedure_units()
{
	$return = array();
	$qry = "SELECT procedureId, units FROM procedures WHERE del_status = 0 ORDER BY procedureId ";
	$res = imw_query($qry) or die(imw_error());
	$cnt = imw_num_rows($res);
	if($cnt > 0 )
	{
		while( $row = imw_fetch_object($res) )
		{
			$return[$row->procedureId] = array('units' => $row->units);
		}
	}
	return $return;
}
?>