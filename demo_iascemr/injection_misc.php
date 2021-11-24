<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "injection";
//SET COLOR VARIABLE TO ROWS
$title_injection_misc="#004587"; 
$bgcolor_injection_misc="#CFE1F7";
$border_injection_misc="#004587";
$heading_injection_misc="#80A7D6";
$rowcolor_injection_misc="#E2EDFB";
//END SET COLOR VARIABLE TO ROWS
?>
<!DOCTYPE html>
<html>
<head>
<title>Injection/Miscellaneous</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<?php
$spec = '</head>
<body onLoad="'.$onloadFun.'; setScrollTopFn();" onClick="document.getElementById(\'divSaveAlert\').style.display = \'none\'; closeEpost();return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");
include_once("common/commonFunctions.php"); 
include_once("admin/injectionMiscSpreadSheet.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$scan = $_REQUEST['scan'];
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	
$patient_id = $_REQUEST["patient_id"];
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}
$ascId = $_REQUEST["ascId"];
$thisId = $_REQUEST["thisId"];
if($innerKey=="") {
	$innerKey = $_REQUEST["innerKey"];
}
if($preColor=="") {
	$preColor = $_REQUEST["preColor"];
}	

$fieldName = "injection_misc_form";
$pageName = "injection_misc.php?patient_id=$patient_id&amp;pConfId=$pConfId";
if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId";
}
include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 


//Logged In User
	$loginIdUser=$_SESSION["loginUserId"];
	$viewLoggedInUserQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
	$viewLoggedInUserRes = imw_query($viewLoggedInUserQry) or die(imw_error()); 
	$viewLoggedInUserRow = imw_fetch_array($viewLoggedInUserRes); 

	$loggedInUserName = addslashes($viewLoggedInUserRow["lname"].", ".$viewLoggedInUserRow["fname"]." ".$viewLoggedInUserRow["mname"]);
	$loggedInUserType = $viewLoggedInUserRow["user_type"];
	$loggedInUserId		=	$_SESSION["loginUserId"];
//end Logged In User


// GETTING CONFIRMATION DETAILS for signatures
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	
	$surgeonId = $detailConfirmation->surgeonId;
	$patientConfirmSiteTempSite = $detailConfirmation->site;
	$patient_primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
	$patient_primary_procedure_cat_misc = $detailConfirmation->prim_proc_is_misc;
	$patient_primary_procedure_name	=	$detailConfirmation->patient_primary_procedure;
	$ascIdConfirm = $detailConfirmation->ascId;

//START GET PATIENT DETAIL
	$imwPatientIdInjection = "";
	$injectionPatientName_tblQry 	= "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$injectionPatientName_tblRes 	= imw_query($injectionPatientName_tblQry) or die(imw_error());
	$injectionPatientName_tblRow 	= imw_fetch_array($injectionPatientName_tblRes);
	$imwPatientIdInjection 	   		= $injectionPatientName_tblRow["imwPatientId"];
//END GET PATIENT DETAIL
	
	//check whether procedure is injection/Misc
		$str_procedure_category= "SELECT P.catId, PC.isMisc FROM procedures P Join procedurescategory PC on P.catId = PC.proceduresCategoryId  WHERE P.procedureId  = '".$patient_primary_procedure_id."'";
		$qry_procedure_category= imw_query($str_procedure_category);
		$fetchRows_procedure_category = imw_fetch_array($qry_procedure_category);
		$patient_primary_procedure_categoryID = $fetchRows_procedure_category['catId'];
	//check whether procedure is injection/Misc
	
	//if(!$patient_primary_procedure_cat_misc )
	//{	
		//$patient_primary_procedure_cat_misc	= $fetchRows_procedure_category['isMisc'];
	//}
	
	//GET ASSIGNED SURGEON ID AND SURGEON NAME
	$assignedSurgeonId = $detailConfirmation->surgeonId;
	$assignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
	//END GET ASSIGNED SURGEON ID AND SURGEON NAME
	
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$signatureOfSurgeon = $usersDetail->signature;
		}
	}	
	//get site from header
	
	
	// APPLYING NUMBERS TO PATIENT SITE
	$patientConfirmSiteName='';
			if($patientConfirmSiteTempSite == 1) {
				$patientConfirmSiteTempSite = "Left Eye";  //OD
				$patientConfirmSiteName = "left eye";  //OD
			}else if($patientConfirmSiteTempSite == 2) {
				$patientConfirmSiteTempSite = "Right Eye";  //OS
				$patientConfirmSiteName = "right eye";  //OS
			}else if($patientConfirmSiteTempSite == 3) {
				$patientConfirmSiteTempSite = "Both Eye";  //OU
				$patientConfirmSiteName = "both eye";  //OU
			}else{
				$patientConfirmSiteTempSite = "Operative Eye";  //OU
			}
	// END APPLYING NUMBERS TO PATIENT SITE

	//**************************************GET LOGGED IN USER TYPE
		unset($conditionArr);
		$conditionArr['usersId'] = $_SESSION["loginUserId"];
		$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
		if($surgeonsDetails){
			foreach($surgeonsDetails as $usersDetail)
			{
				$loggedUserType = $usersDetail->user_type;
			}
		}
	/**************************************END GET LOGGED IN USER TYPE**************************************/
	

	//SAVE Into TABLE PATIENT DETAIL
	if(!$cancelRecord){
			$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$injLeft_form = $getLeftLinkDetails->injection_misc_form;
			if($injLeft_form == 'true'){
				$formArrayRecord['injection_misc_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
	}elseif($cancelRecord){
				$fieldName = "injection_misc_form";
				$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
				include("left_link_hide.php");
	}			
	
	//$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId.'&fieldName='.$fieldName;
		
	//MAKE AUDIT STATUS VIEW
	if($_POST['SaveRecordForm']!='yes'){
		unset($arrayRecord);
		$arrayRecord['user_id'] = $_SESSION['loginUserId'];
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['confirmation_id'] = $pConfId;
		$arrayRecord['form_name'] = 'injection_misc_form';
		$arrayRecord['status'] = 'viewed';
		$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
		$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
	}
	//MAKE AUDIT STATUS VIEW	
		
	//SAVE RECORD TO DATABASE
if($_REQUEST['SaveRecordForm']=='yes'){
		
		/*************************************
			Start Sorting Medications Here
		*************************************/
			
			/* Sorting Pre Op Medication */
			$PreOpMed	=	$_REQUEST['PreOpMed'];
			$PreOpLot	=	$_REQUEST['PreOpLot'];
			
			$preOpMedsArray	=	array();
			$preOpMedsDB		=	'';
			
			if(is_array($PreOpMed) && count($PreOpMed) > 0)
			{
				foreach($PreOpMed as $key=>$medicationName)
				{
					if(trim($medicationName))
					{
						$preOpMedsArray[]	=	$medicationName.'@#@'.$PreOpLot[$key];
					}
				}
				$preOpMedsDB	=	implode('~@~',$preOpMedsArray);
			}
			
			/* Sorting Intravitreal Medication */
			$IntravitrealMed	=	$_REQUEST['IntravitrealMed'];
			$IntravitrealLot	=	$_REQUEST['IntravitrealLot'];
			
			$intravitrealMedsArray	=	array();
			$intravitrealMedsDB			=	'';
			
			if(is_array($IntravitrealMed) && count($IntravitrealMed) > 0)
			{
				foreach($IntravitrealMed as $key=>$medicationName)
				{
					if(trim($medicationName))
					{
						$intravitrealMedsArray[]	=	$medicationName.'@#@'.$IntravitrealLot[$key];
					}
				}
				$intravitrealMedsDB	=	implode('~@~',$intravitrealMedsArray);
			}
			
			
			/* Sorting Post Op Medication */
			$PostOpMed=	$_REQUEST['PostOpMed'];
			$PostOpLot=	$_REQUEST['PostOpLot'];
			
			$postOpMedsArray	=	array();
			$postOpMedsDB			=	'';
			
			if(is_array($PostOpMed) && count($PostOpMed) > 0)
			{
				foreach($PostOpMed as $key=>$medicationName)
				{
					if(trim($medicationName))
					{
						$postOpMedsArray[]	=	$medicationName.'@#@'.$PostOpLot[$key];
					}
				}
				$postOpMedsDB	=	implode('~@~',$postOpMedsArray);
			}
		
		
		/**************************************
			End Sorting Medications Here
		**************************************/
		
		
		//Start Getting Already Saved values 
			$dataRow	=	$objManageData->getExtractRecord($tablename,'confirmation_id',$pConfId);
			$chk_injectionId=	$dataRow['injId'];
			$chk_surgeon1Id	=	$dataRow['signSurgeon1Id'];
			$chk_nurse1Id		=	$dataRow['signNurse1Id'];
			$chk_nurse2Id		=	$dataRow['signNurse2Id'];
			$chk_formStatus	=	$dataRow['form_status'];
			$chk_timeoutReq	=	$dataRow['timeoutReq'];
			$chk_startTime	=	$dataRow['startTime'];
			$chk_endTime		=	$dataRow['endTime'];
			
			if($chk_formStatus <> 'completed' && $chk_formStatus <> 'not completed')
			{		// If Form is not saved for once then load default profile
					// for timeout required or not
					$fields	=	'timeoutReq';
					$defaultProfile	= $objManageData->injectionProfile($patient_primary_procedure_id,$assignedSurgeonId,$fields);
					if($defaultProfile['profileFound'])
					{
						$chk_timeoutReq	=	$defaultProfile['data']['timeoutReq'];
					}
			}
		//End Getting Already Saved values 
		
		
		//Start Chart Validation Here
			$formStatus	=	'completed';
			
			$preVital		=	array($_POST['preVitalTime'],$_POST['preVitalBp'],$_POST['preVitalPulse'],$_POST['preVitalResp'],$_POST['preVitalSpo']);
			$postVital	=	array($_POST['postVitalTime'],$_POST['postVitalBp'],$_POST['postVitalPulse'],$_POST['postVitalResp'],$_POST['postVitalSpo']);
			$compValid	=	array($_POST['complications'],$_POST['comments']);
			$andArray		= array($_POST['startTime'],$_POST['endTime'],$_POST['chkConsentSigned'],$_POST['procedureComments'],$_POST['postIop'],$_POST['postIopSite'],$_POST['postIopTime']);
			
			if(		!($objManageData->validateGroupOR($preVital))
				||	!($objManageData->validateGroupOR($postVital))
				||	!($objManageData->validateGroupOR($compValid))
				|| 	!($objManageData->validateGroupAND($andArray))
				||	!($chk_nurse1Id)
				||	!($chk_nurse2Id)
				||	!($chk_surgeon1Id)
				)
			{
				$formStatus	=	'not completed';		
			}
			
			//Start Validate timeout fields if timeout required is checked from default profile/saved value
			if($chk_timeoutReq && $formStatus == 'completed')	
			{
				$timeoutArray		= array($_POST['timeoutTime'],$_POST['timeoutProcVerified'],$_POST['timeoutSiteVerified']);
				if(!($objManageData->validateGroupAND($timeoutArray)))
				{
					$formStatus	=	'not completed';	
				}
			}
			//End Validate timeout fields if timeout required is checked from default profile/saved value
			
		//End Chart Validation Here
			
			$_POST['preVitalTime']	=	trim($_POST['preVitalTime'])	?	$objManageData->setTmFormat($_POST['preVitalTime'])	:	'';
			$_POST['timeoutTime']		=	trim($_POST['timeoutTime'])		?	$objManageData->setTmFormat($_POST['timeoutTime'])	:	'';
			$_POST['startTime']			=	trim($_POST['startTime'])			?	$objManageData->setTmFormat($_POST['startTime'])		:	'';
			$_POST['endTime']				=	trim($_POST['endTime'])				?	$objManageData->setTmFormat($_POST['endTime'])			:	'';
			$_POST['postVitalTime']	=	trim($_POST['postVitalTime'])	?	$objManageData->setTmFormat($_POST['postVitalTime']):	'';
			$_POST['postIopTime']		=	trim($_POST['postIopTime'])		?	$objManageData->setTmFormat($_POST['postIopTime'])	:	'';
			
			// Check if surgery start time or surgery end time field values changed
			$start_time_staus	=	'0';
			if(	 	 ($_POST['startTime'] && $_POST['startTime'] <> date('H:i:s', strtotime($chk_startTime)) )
					|| ($_POST['endTime'] && $_POST['endTime'] <> date('H:i:s', strtotime($chk_endTime)) )
			)
			{
				$start_time_staus	=	'1';			 
			}
			
			
			$arrayRecord['preVitalTime']				=	$_POST['preVitalTime'];
			$arrayRecord['preVitalBp']					=	addslashes($_POST['preVitalBp']);
			$arrayRecord['preVitalPulse']				=	addslashes($_POST['preVitalPulse']);
			$arrayRecord['preVitalResp']				=	addslashes($_POST['preVitalResp']);
			$arrayRecord['preVitalSpo']					=	addslashes($_POST['preVitalSpo']);
			
			$arrayRecord['timeoutReq']					=	$chk_timeoutReq;
			$arrayRecord['timeoutTime']					=	$_POST['timeoutTime'];
			$arrayRecord['timeoutProcVerified']	=	addslashes($_POST['timeoutProcVerified']);
			$arrayRecord['timeoutSiteVerified']	=	addslashes($_POST['timeoutSiteVerified']);
			
			$arrayRecord['startTime']						=	$_POST['startTime'];
			$arrayRecord['endTime']							=	$_POST['endTime'];
			$arrayRecord['chkConsentSigned']		=	addslashes($_POST['chkConsentSigned']);
			$arrayRecord['procedureComments']		=	addslashes($_POST['procedureComments']);
			
			$arrayRecord['preOpMeds']						=	addslashes($preOpMedsDB);
			$arrayRecord['intravitrealMeds']		=	addslashes($intravitrealMedsDB);
			$arrayRecord['postOpMeds']					=	addslashes($postOpMedsDB);
			
			$arrayRecord['complications']				=	addslashes($_POST['complications']);
			$arrayRecord['comments']						=	addslashes($_POST['comments']);
			
			$arrayRecord['postVitalTime']				=	$_POST['postVitalTime'];
			$arrayRecord['postVitalBp']					=	addslashes($_POST['postVitalBp']);
			$arrayRecord['postVitalPulse']			=	addslashes($_POST['postVitalPulse']);
			$arrayRecord['postVitalResp']				=	addslashes($_POST['postVitalResp']);
			$arrayRecord['postVitalSpo']				=	addslashes($_POST['postVitalSpo']);
			
			$arrayRecord['postIop']							=	addslashes($_POST['postIop']);
			$arrayRecord['postIopSite']					=	addslashes($_POST['postIopSite']);
			$arrayRecord['postIopTime']					=	$_POST['postIopTime'];
			$arrayRecord['form_status']					=	$formStatus;
			$arrayRecord['start_time_status']		=	$start_time_staus;
			
			if($chk_injectionId)
			{
				$objManageData->UpdateRecord($arrayRecord,'injection','confirmation_id',$pConfId);
			}
			else
			{
				$objManageData->addRecords($arrayRecord,'injection');	
			}
			
			// Update patientconfirmation table on only first save of chart
			if($chk_formStatus <> 'completed' && $chk_formStatus <> 'not completed')
			{
				unset($arrayConfirmationRecord);
				$arrayConfirmationRecord['prim_proc_is_misc']	=	$objManageData->verifyProcIsInjMisc($patient_primary_procedure_id);
				$objManageData->UpdateRecord($arrayConfirmationRecord,'patientconfirmation','patientConfirmationId',$pConfId);	
			}
			// End Update patientconfirmation table on only first save of chart
			
		
		/*******************************
			Creating Audit Status on Save
		********************************/
		
		//MAKE AUDIT STATUS
		unset($arrayStatusRecord);		
		$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
		$arrayStatusRecord['patient_id'] = $patient_id;
		$arrayStatusRecord['confirmation_id'] = $pConfId;
		$arrayStatusRecord['form_name'] = 'injection_procedure_form';		
		$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
		//MAKE AUDIT STATUS
		
		//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'injection_misc_form';
		$conditionArr['status'] = 'created';
		$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl',$conditionArr);	
		if($chkAuditStatus) {
			$arrayStatusRecord['status'] = 'modified';
		}else {
			$arrayStatusRecord['status'] = 'created';
		}
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');												
		//CODE END TO SET AUDIT STATUS AFTER SAVE
		
		
		/************************************
			End Creating Audit Status on Save
		*************************************/
		
		
		//CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$chartSignedByNurse 	= chkNurseSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry 		= "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."', chartSignedByNurse='".$chartSignedByNurse."' ".$recentChartSavedQry." WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."' AND patient_confirmation_id!='0'";
		$updateStubTblRes 		= imw_query($updateStubTblQry) or die(imw_error());
		//END CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
		
		
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		if($formStatus == "completed" && ($chk_formStatus=="" || $chk_formStatus=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script >";	
		}else if($formStatus=="not completed" && ($chk_formStatus==""  || $chk_formStatus=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script >";	
		}
		//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

		//Start Copy Pre Op Vital Sign Into Header After Saving Chart
		echo "
		<script>
			if(top.document.getElementById('header_BP')) {
				top.document.getElementById('header_BP').innerText='".$_POST['preVitalBp']."';
			}
			if(top.document.getElementById('header_P')) {
				top.document.getElementById('header_P').innerText='".$_POST['preVitalPulse']."';
			}
			if(top.document.getElementById('header_R')) {
				top.document.getElementById('header_R').innerText='".$_POST['preVitalResp']."';
			}
			if(top.document.getElementById('header_O2SAT')) {
				top.document.getElementById('header_O2SAT').innerText='".$_POST['preVitalSpo']."';
			}
			if(top.document.getElementById('header_Temp')) {
				top.document.getElementById('header_Temp').innerText='N/A';
			}
			
		</script>
		";
		//End Copy Pre Op Vital Sign Into Header After Saving Chart
		
		//START SEND LASER CHART TO IDOC OPERATIVE NOTE
		$iDocOpNoteSave = "";
		if(trim($ascIdConfirm) && $formStatus=="completed" && $imwSwitchFile == "sync_imwemr.php" && $imwPatientIdInjection && $_REQUEST["pConfId"]) {
			$iDocOpNoteSave = "yes";
			include("injection_misc_pdf.php");
		}
		//END SEND LASER CHART TO IDOC OPERATIVE NOTE

}
//END SAVE RECORD TO DATABASE



//VIEW RECORD FROM DATABASE
	$ViewInjectionQry = "select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTimeFormat, date_format(signNurse2DateTime,'%m-%d-%Y %h:%i %p') as signNurse2DateTimeFormat from `injection` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewInjectionRes = imw_query($ViewInjectionQry) or die(imw_error()); 
	$ViewInjectionNumRow = imw_num_rows($ViewInjectionRes);
	$ViewInjectionRow = imw_fetch_array($ViewInjectionRes); 
	extract($ViewInjectionRow);
	
	if(!$form_status)
	{
			//Get Default Profile
			$fields	=	'timeoutReq,preOpMeds,intravitrealMeds,postOpMeds,consentTemplateId';
			$defaultProfile	= $objManageData->injectionProfile($patient_primary_procedure_id,$assignedSurgeonId,$fields);
			if($defaultProfile['profileFound'])
			{
				extract($defaultProfile['data']);
				
				// Check if consent forms signed
				if($consentTemplateId)
				{
					$consentChkQry	=	"Select * From consent_multiple_form Where consent_template_id IN (".$consentTemplateId.") And confirmation_id = '".$pConfId."' And (form_status = 'not completed' OR form_status = '') And consent_purge_status <> 'true' ";
					$consentChkSql	=	imw_query($consentChkQry) or die($consentChkQry. imw_error());
					$consentChkCnt	=	imw_num_rows($consentChkSql);
					$chkConsentSigned	=	1;
					if($consentChkCnt > 0 )
					{
						$chkConsentSigned	=	0;	
					}
				}
				// End Check if consent forms signed
				
			}
	}
	
	$preVitalTime		=	($preVitalTime <> '00:00:00')		?	$objManageData->getTmFormat($preVitalTime) :	'';
	$timeoutTime		=	($timeoutTime <> '00:00:00')		?	$objManageData->getTmFormat($timeoutTime)		:	'';
	$startTime			=	($startTime <> '00:00:00')			?	$objManageData->getTmFormat($startTime)			:	'';
	$endTime				=	($endTime <> '00:00:00')				?	$objManageData->getTmFormat($endTime)				:	'';
	$postVitalTime	=	($postVitalTime <> '00:00:00')	?	$objManageData->getTmFormat($postVitalTime)	:	'';
	$postIopTime		=	($postIopTime <> '00:00:00')		?	$objManageData->getTmFormat($postIopTime)		:	'';
		
	$preVitalGroupArr	=	array($preVitalTime,$preVitalBp,$preVitalPulse,$preVitalResp,$preVitalSpo);
	$preVitalBgColor	=	($objManageData->validateGroupOR($preVitalGroupArr)) ? $whiteBckGroundColor : $chngBckGroundColor;
	
	$timeoutTimeBgColor	=	(!$timeoutTime && $timeoutReq)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	$timeoutProcBgColor	=	(!$timeoutProcVerified && $timeoutReq)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	$timeoutSiteBgColor	=	(!$timeoutSiteVerified && $timeoutReq)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	
	$consentSignBgColor	=	(!$chkConsentSigned)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	$startTimeBgColor	 	=	(!$startTime)					?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	$endTimeBgColor	 	 	=	(!$endTime)						?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	$procCommentsBgColor=	(!$procedureComments)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;
	
	$complicationsArr		=	array($complications,$comments);
	$complicationsBgColor	=	($objManageData->validateGroupOR($complicationsArr)) ? $whiteBckGroundColor : $chngBckGroundColor;
	$complicationsGrpFun	=	"changeDiffChbxColorNew('1','.complications');";
	
	$postVitalGroupArr	=	array($postVitalTime,$postVitalBp,$postVitalPulse,$postVitalResp,$postVitalSpo);
	$postVitalBgColor		=	($objManageData->validateGroupOR($postVitalGroupArr)) ? $whiteBckGroundColor : $chngBckGroundColor;
	//$postVitalGrpFun	=	"changeDiffChbxColorNew('1','.postVital');";
	
	$postIopBgColor			=	(!$postIop)			?	$chngBckGroundColor	:	$whiteBckGroundColor ;	
	$postIopSiteBgColor	=	(!$postIopSite)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;	
	$postIopTimeBgColor	=	(!$postIopTime)	?	$chngBckGroundColor	:	$whiteBckGroundColor ;	
	
		
		
	/************************************************
		Start Getting Signatures of Surgeon and Nurse	
	*************************************************/
	
	if($loggedInUserType <> "Surgeon") 
	{
		$callJavaFunSurgeon1 = "return noAuthorityFunCommon('Surgeon');";
	}
	else
	{
		$callJavaFunSurgeon1 = "document.frm_injection_misc.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','injection_misc_ajaxSign.php','$loggedInUserId','Surgeon1');";
	}
	
	$surgeon1SignOnFileStatus 		= "Yes";
	$TDsurgeon1NameIdDisplay			= "block";
	$TDsurgeon1SignatureIdDisplay = "none";
	$Surgeon1NameShow 								= $loggedInUserName;
	$signSurgeon1DateTimeFormatNew = date("m-d-Y h:i A");
	
	if($signSurgeon1Id<>0 && $signSurgeon1Id<>"")
	{
			$Surgeon1NameShow = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
			$surgeon1SignOnFileStatus = $signSurgeon1Status;	
			$TDsurgeon1NameIdDisplay = "none";
			$TDsurgeon1SignatureIdDisplay = "block";
			$signSurgeon1DateTimeFormatNew = $signSurgeon1DateTimeFormat;
	}
	
	if($_SESSION["loginUserId"] == $signSurgeon1Id)
	{
			$callJavaFunSurgeon1Del = "document.frm_injection_misc.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','injection_misc_ajaxSign.php','$loggedInUserId','Surgeon1','delSign');";
	}
	else
	{
			$callJavaFunSurgeon1Del = "alert('Only Dr. ".addslashes($Surgeon1NameShow)." can remove this signature');";
	}
	
	
	// Nurse 1 Signature
	if($loggedInUserType <> "Nurse")
	{
		$callJavaFunNurse1 = "return noAuthorityFunCommon('Nurse');";
	}
	else
	{
		$callJavaFunNurse1 = "document.frm_injection_misc.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','injection_misc_ajaxSign.php','$loggedInUserId','Nurse1');";
	}
	
	$nurse1SignOnFileStatus 		=	"Yes";
	$TDnurse1NameIdDisplay 			= "block";
	$TDnurse1SignatureIdDisplay	= "none";
	$Nurse1NameShow 						= $loggedInUserName;
	
	$signNurse1DateTimeFormatNew = date("m-d-Y h:i A");
	
	if($signNurse1Id<>0 && $signNurse1Id<>"")
	{
		$Nurse1NameShow = $signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName;
		$nurse1SignOnFileStatus = $signNurse1Status;	
		$TDnurse1NameIdDisplay = "none";
		$TDnurse1SignatureIdDisplay = "block";
		$signNurse1DateTimeFormatNew = $signNurse1DateTimeFormat;
	}
	
	if($_SESSION["loginUserId"]==$signNurse1Id)
	{
		$callJavaFunNurse1Del = "document.frm_injection_misc.hiddSignatureId.value='TDnurse1NameId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','injection_misc_ajaxSign.php','$loggedInUserId','Nurse1','delSign');";
	}
	else
	{
		$callJavaFunNurse1Del = "alert('Only ".addslashes($Nurse1NameShow)." can remove this signature');";
	}
	
	
	// Nurse 2 Signature
	if($loggedInUserType <> "Nurse")
	{
		$callJavaFunNurse2 = "return noAuthorityFunCommon('Nurse');";
	}
	else
	{
		$callJavaFunNurse2 = "document.frm_injection_misc.hiddSignatureId.value='TDnurse2SignatureId'; return displaySignature('TDnurse2NameId','TDnurse2SignatureId','injection_misc_ajaxSign.php','$loggedInUserId','Nurse2');";
	}

	$nurse2SignOnFileStatus 		=	"Yes";
	$TDnurse2NameIdDisplay 			= "block";
	$TDnurse2SignatureIdDisplay	= "none";
	$Nurse2NameShow 						= $loggedInUserName;
	
	$signNurse2DateTimeFormatNew = date("m-d-Y h:i A");
	
	if($signNurse2Id<>0 && $signNurse2Id<>"")
	{
		$Nurse2NameShow = $signNurse2LastName.", ".$signNurse2FirstName." ".$signNurse2MiddleName;
		$nurse2SignOnFileStatus = $signNurse2Status;	
		$TDnurse2NameIdDisplay = "none";
		$TDnurse2SignatureIdDisplay = "block";
		$signNurse2DateTimeFormatNew = $signNurse2DateTimeFormat;
	}
	
	if($_SESSION["loginUserId"]==$signNurse2Id)
	{
		$callJavaFunNurse2Del = "document.frm_injection_misc.hiddSignatureId.value='TDnurse2NameId'; return displaySignature('TDnurse2NameId','TDnurse2SignatureId','injection_misc_ajaxSign.php','$loggedInUserId','Nurse2','delSign');";
	}
	else
	{
		$callJavaFunNurse2Del = "alert('Only ".addslashes($Nurse2NameShow)." can remove this signature');";
	}
	
	$surgeon1SignBackColor=	($signSurgeon1Id > 0 )?	$whiteBckGroundColor	:	$chngBckGroundColor;
	$nurse1SignBackColor	=	($signNurse1Id > 0 )	?	$whiteBckGroundColor	:	$chngBckGroundColor;
	$nurse2SignBackColor	=	($signNurse2Id > 0 )	?	$whiteBckGroundColor	:	$chngBckGroundColor;
	
	/************************************************
		End Getting Signatures of Surgeon and Nurse		
	*************************************************/
	
	
	// End View Record From Database
	
?>
<script type="text/javascript">


//FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE
	
	function noAuthorityFun(userName) {
		alert("You are not authorised to make signature of "+userName);
		return false;
	}
	function noSignInAdmin() {
		alert("You have yet to make signature in Admin");
		return false;
	}
	
	function alreadySignOnce(userTypeNum) {
		alert('You have already signed at '+userTypeNum);
	}
	//Display Signature Of Nurse
	function GetXmlHttpObject()
	{ 
				
		var objXMLHttp=null
		if (window.XMLHttpRequest)
		{
		objXMLHttp=new XMLHttpRequest()
		}
		else if (window.ActiveXObject)
		{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
		}
		return objXMLHttp
	}			
	
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {

		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
		}else {
			document.getElementById(TDUserNameId).style.display = 'none';
			document.getElementById(TDUserSignatureId).style.display = 'block';
		}

		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 

		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		url=url+"&preColor="+preColor1
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
				top.frames[0].setPNotesHeight();
			}
	}
	//End Display Signature Of Nurse
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

function setScrollTopFn() {
	if(top.document.getElementById('hiddScrollTop')) {
		var frmScrlTop=parseFloat(top.document.getElementById('hiddScrollTop').value);
		frmScrlTop=parseFloat(frmScrlTop);
		if(top.mainFrame) {
			if(top.mainFrame.main_frmInner) {
				if(top.document.getElementById('hiddScrollTop').value>100){
					top.mainFrame.main_frmInner.document.body.scrollTop=parseFloat(frmScrlTop);
					if(document.getElementById('divSaveAlert')) {
						document.getElementById('divSaveAlert').style.top=frmScrlTop;
					}
					top.document.getElementById('hiddScrollTop').value='';
				}
			}	
		}	
	}	
}

var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display == "block"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.frames[0].frames[0].document.getElementById(Id)) {
				if(top.frames[0].frames[0].document.getElementById(Id).style.display == "block"){
					top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
}

$(function(){
	
	$("#preOpMedsPop,#intravitrealMedsPop,#postOpMedsPop").click(function(){
		
		var $this	=	$(this);
		var Parent=	$this.parent('h3');
		var SFName=	$this.attr('data-frame-name');
		var obj		=	$("#medicationPopupAdmin");
		var CLeft	=	setHorizontalPosition($this,$(document),obj)
		var CTop	=	setVerticalPosition($this,$(document),obj);
		
		$("#selected_frame_name_id").val(SFName);
		obj.css({'left' : CLeft +'px' , 'top' : CTop + 'px' , 'display':'block'});
		
		$("#counter").val('20');
		
		//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if($("#hiddPreDefineId")) {
				$("#hiddPreDefineId").val('');
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100) ;
		}
		//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
		
	});
	
		
	$('body').on('focus keyup change blur ','[data-group="preVital"],[data-group="postVital"],[data-group="complications"]',function(){
				var isValueFilled = false;
				var DG	=	$(this).attr('data-group');
				
				$('[data-group="'+DG+'"]').each(function(){
					
					if($(this).is(':checkbox') && $(this).is(':checked'))
					{
						isValueFilled = true;
					}
					else 
					{
						if($(this).val() !== '') 
						{
							isValueFilled = true;
						}
					}
						
				});
				
				var BgColor	=	(isValueFilled) ? '#FFF' : '#F6C67A' ;
				$('[data-group="'+DG+'"]:not(":checkbox")').css('background-color',BgColor);
				$('[data-group="'+DG+'"]:checkbox').parent().css('background-color',BgColor);
		});
	
});
</script>
<?php
// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS

if($finalizeStatus=='true'){
	$onloadFun = "hide_footer();";
}
?>
<div id="post" style="display:none; position:absolute;"></div>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<form enctype="multipart/form-data" name="frm_injection_misc" method="post" style="margin:0px;">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
	<input type="hidden" name="saveRecord" id="saveRecord" value="true">
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId" value="">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>"/>
	<input type="hidden" name="frmAction" id="frmAction" value="injection_misc.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">				
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
  <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="ascId" id="ascId" value="<?php echo $ascId; ?>">
	<input type="hidden" name="thisId" id="thisId" value="<?php echo $thisId; ?>">
	<input type="hidden" id="vitalSignGridHolder" />
  
	<div id="divSaveAlert" style="position:absolute; display:none; z-index:999;">
				<?php 
            $bgCol = $title_injection_misc;
            $borderCol = $title_injection_misc;
            include('saveDivPopUp.php'); 
        ?>
  </div>
  <div class="scheduler_table_Complete" id="" style="">
  		<?php
				$epost_table_name = "injection";
				include("./epost_list.php");
			?>
 	</div>
  
  <!-- Do Html Code Here-->
  
  <div class="container-fluid">
  
  	<div class="panel panel-default bg_panel_op">
        
    	<div class="panel-heading"><h3  class="panel-title rob">Vital Signs - Pre Op</h3></div>
        
        <div class="panel-body" style="float:none;" >
          <div class="row col-lg-12 col-sm-12 col-md-12 col-xs-12">
        
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0  ">
              <label class="col-sm-4 col-md-3 padding_6">Time</label>
              <span class="col-sm-8 col-md-9 padding_6 ">
                <input type="text" class="form-control vitalSignGrid timeBox" name="preVitalTime" id="preVitalTime" value="<?=$preVitalTime?>" data-group="preVital" style="<?=$preVitalBgColor?>"  />
              </span>
                 
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-3 col-md-2 padding_6 text-right">BP</label>
              <span class="col-sm-9 col-md-10 padding_6">
                <input type="text " class="form-control vitalSignGrid" data-group="preVital" name="preVitalBp" id="preVitalBp" value="<?=$preVitalBp?>" style="<?=$preVitalBgColor?>" />
              </span>
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-3 col-md-2 padding_6 text-right">P</label>
              <span class="col-sm-9 col-md-10 padding_6">
                <input type="text" class="form-control vitalSignGrid" data-group="preVital" name="preVitalPulse" id="preVitalPulse" value="<?=$preVitalPulse?>" style="<?=$preVitalBgColor?>" />
              </span>
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-3 col-md-3 padding_6 text-right">R</label>
              <span class="col-sm-9 col-md-9 padding_6">
                <input type="text" class="form-control vitalSignGrid" data-group="preVital" name="preVitalResp" id="preVitalResp" value="<?=$preVitalResp?>" style="<?=$preVitalBgColor?>" />
              </span>
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-4 col-md-3 padding_6 text-right">Spo2</label>
              <span class="col-sm-8 col-md-9 padding_6">
                <div class="input-group" style="width:100% !important;">
                  <input type="text" class="form-control vitalSignGrid" data-group="preVital" name="preVitalSpo" id="preVitalSpo" value="<?=$preVitalSpo?>" style="<?=$preVitalBgColor?>" />
                  <div class="input-group-addon padding_2"><b>%</b></div>
                </div>
              </span>
            </div>
          </div>
        </div>
        
  	</div>
    	
    <div class="clearfix"></div>
    
    <div class="row">
      <div class=" col-lg-6 col-md-12 col-sm-12 col-xs-12 margin_top_5 ">
          <div class="panel panel-default bg_panel_op">
            
            <div class="panel-heading"><h3  class="panel-title rob">Timeout</h3></div>
            
            <div class="panel-body" style="float:none;" >
              <div class="row col-lg-5 col-sm-6 col-md-5 col-xs-12">
                <span class="colorChkBx" style="<?=$timeoutSiteBgColor?>" <?php if($timeoutReq){?>onclick="changeChbxColor('timeoutSiteVerified');" <?php } ?>>
                  <input type="checkbox" value="1" <?=($timeoutSiteVerified ? 'checked' : '')?> name="timeoutSiteVerified" id="timeoutSiteVerified" />
                </span>
                <label>Site verified  :</label>&nbsp;<?=$patientConfirmSiteTempSite?>
              </div>
              
              <div class="clearfix visible-xs">&nbsp;</div>
              
              <div class=" col-lg-7 col-sm-6 col-md-7 col-xs-12">
                <span class="colorChkBx" style="<?=$timeoutProcBgColor?>" <?php if($timeoutReq){?>onclick="changeChbxColor('timeoutProcVerified');"<?php } ?>>
                  <input type="checkbox" value="1" <?=($timeoutProcVerified ? 'checked' : '')?> name="timeoutProcVerified" id="timeoutProcVerified" />
                </span>
                <label>Procedure Verified :</label>&nbsp;<?=$patient_primary_procedure_name?>	
              </div>
              
              <div class="clearfix margin-adjustment-only">&nbsp;</div>
              <div class="clearfix margin-adjustment-only">&nbsp;</div>
              
              <div class="row col-lg-5 col-sm-6 col-md-5 col-xs-12">
                <label class="col-sm-4 col-md-3 col-lg-2 padding_0  ">Time:</label>
                <span class="col-sm-8 col-md-9 col-lg-10 ">
                  <input type="text" class="form-control vitalSignGrid timeBox" name="timeoutTime" id="timeoutTime" value="<?=$timeoutTime?>" style="<?=$timeoutTimeBgColor?>" <?php if($timeoutReq){?> onFocus="changeTxtGroupColor(1,'timeoutTime');" onBlur="changeTxtGroupColor(1,'timeoutTime');" onKeyUp="changeTxtGroupColor(1,'timeoutTime');" <?php } ?> />
                </span>
              </div>
              
              <div class=" col-lg-7 col-sm-6 col-md-7 col-xs-12">
                  
                  <div class="inner_safety_wrap" id="TDnurse1NameId" style="display:<?php echo $TDnurse1NameIdDisplay;?>;">
                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurse1SignBackColor?>;" onClick="javascript:<?php echo $callJavaFunNurse1;?>"> Nurse Signature </a>
                  </div>
                  
                  <div class="inner_safety_wrap collapse" id="TDnurse1SignatureId" style="display:<?php echo $TDnurse1SignatureIdDisplay;?>;">
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunNurse1Del;?>"> <?php echo "<b>Nurse:</b> ".$Nurse1NameShow; ?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed: </b> <?php echo $nurse1SignOnFileStatus;?></span>
                    <span class="rob full_width">
                      <b> Signature Date :</b>
                      <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"><?=$objManageData->getFullDtTmFormat($signNurse1DateTime)?>
                        <span class="fa fa-edit"></span>
                      </span>
                    </span>
                  </div>
              
              </div>
              
              
            </div>
            
        </div>
        
      </div>
   		<div class="clearfix visible-xs">&nbsp;</div>
      
      <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 margin_top_5 ">
        
          <div class="panel panel-default bg_panel_op">
            
            <div class="panel-heading"><h3  class="panel-title rob"> Procedure </h3></div>
            <div class="clearfix"></div>
            
            <div class="panel_body" style="min-height:108px;">
            	
              <div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-sm-6 col-md-5 col-lg-5 col-xs-6 margin_top_5 " >
                  <label>Site:&nbsp;</label><?=$patientConfirmSiteTempSite?>
                </div>
              
                <div class="col-sm-6 col-md-7 col-lg-7 col-xs-12 margin_top_5" >
                  <label>Procedure:&nbsp;</label><?=$patient_primary_procedure_name?>
                </div>
              
              	<div class="clearfix border-dashed" style="max-height:10px;">&nbsp;</div>
              
                <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12  " >
                  <div class="col-sm-4 col-md-3 col-lg-3 col-xs-4 margin_top_2 padding_0" >
                    <span>
                      <span class="colorChkBx" style="<?=$consentSignBgColor?>" onclick="changeChbxColor('chkConsentSigned');" >
                        <input type="checkbox" value="1" name="chkConsentSigned" id="chkConsentSigned" <?=($chkConsentSigned ? 'checked' : '')?> />
                      </span>Consent Signed
                    </span>
                  </div>
                  
                  <div class="col-sm-8 col-md-9 col-lg-9 col-xs-8 margin_top_2 padding_2" >
                    
                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6 padding_0" >
                      <label class="col-lg-5 col-md-5 col-sm-6 col-xs-12 padding_0 text-right">Start Time:&nbsp;</label>
                      <span class="col-lg-7 col-md-7 col-sm-6 col-xs-12 padding_0">
                        <input type="text" class="form-control vitalSignGrid timeBox" name="startTime" value="<?=$startTime?>" id="startTime" style="<?=$startTimeBgColor?>" onFocus="changeTxtGroupColor(1,'startTime');" onBlur="changeTxtGroupColor(1,'startTime');" onKeyUp="changeTxtGroupColor(1,'startTime');"  />
                      </span>
                    </div>
                 
                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6 padding_0" >
                      <label class="col-lg-5 col-md-5 col-sm-6 col-xs-12 padding_0 text-right">End Time:&nbsp;</label>
                      <span class="col-lg-7 col-md-7 col-sm-6 col-xs-12 padding_0">
                        <input type="text" class="form-control vitalSignGrid timeBox" name="endTime" id="endTime" style="<?=$endTimeBgColor?>" onFocus="changeTxtGroupColor(1,'endTime');" onBlur="changeTxtGroupColor(1,'endTime');" onKeyUp="changeTxtGroupColor(1,'endTime');" value="<?=$endTime?>" />
                      </span>
                    </div>
                
                  </div>
                  
                </div>
              
                <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12 margin_top_5" >
                	<span class="col-lg-2 col-md-2 col-xs-2 col-sm-2 padding_0"><label>Comments:&nbsp;</label></span>
                  <span class="col-lg-10 col-md-10 col-xs-10 col-sm-10 padding_0">
                  	<textarea class="form-control" rows="2" name="procedureComments" id="procedureComments" style="<?=$procCommentsBgColor?>" onFocus="changeTxtGroupColor(1,'procedureComments')" onBlur="changeTxtGroupColor(1,'procedureComments')" onKeyup="changeTxtGroupColor(1,'procedureComments')" ><?=$procedureComments?></textarea>
                 	</span>
                     
                </div>
                
                <div class="clearfix" style="margin-bottom:5px;"></div>
                
           		</div></div>
            </div>
          
          		
          
          
        </div>
      </div>
   	</div>
   	
    <div class="clearfix"></div>
    
    
    <div class="row margin_top_5">
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="panel panel-default bg_panel_op">
        
          <div class="panel-heading">
          	<h3 class="panel-title rob">
            	<a class="rob alle_link show-pop-list_g btn btn-default" style="padding:2px 4px !important;" id="preOpMedsPop" data-frame-name="PreOp">
              	<span class="fa fa-caret-right"></span> Pre-OP Meds
            	</a>
           	</h3>
        	</div>
          
          <div class="clearfix"></div>
          
          <div class=" table-responsive lsttable">
            <div class="fixed-table">
              <div class="table-content">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf width_table table-striped" >
                  <thead>
                    <tr>
                      <th style="width:45%;"> Medication </th>
                      <th > #Lot </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="2" style="padding:1px 0">
                        <div class="over_wrap" id="spreadSheetIntravitrealMed" >
                        <?php printSpreadSheet('PreOp',$preOpMeds);?>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                 </table>
              </div>
            </div>
          </div>
          

        </div>
      </div>
      
      <div class="clearfix visible-sm visible-xs"></div>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
      	<div class="panel panel-default bg_panel_op"><div class="panel-heading">
        	<h3 class="panel-title rob">
          	<a class="rob alle_link show-pop-list_g btn btn-default" style="padding:2px 4px !important;" id="intravitrealMedsPop" data-frame-name="Intravitreal">
              	<span class="fa fa-caret-right"></span> Intravitreal Med
            </a>
         	</h3>
       	</div>
      	<div class="clearfix"></div>
      	<div class="table-responsive lsttable">
      		<div class="fixed-table">
              <div class="table-content">
                <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf width_table table-striped" >
                  <thead>
                    <tr>
                      <th style="width:45%;"> Medication </th>
                      <th > #Lot </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="2" style="padding:1px 0">
                        <div class="over_wrap" id="spreadSheetIntravitrealMed" >
                        <?php printSpreadSheet('Intravitreal',$intravitrealMeds);?>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                 </table>
              </div>
  </div>

</div>
			</div>
  	
    	</div>
   	
    </div>
    	 
    <div class="row margin_top_5">
    	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
      	<div class="panel panel-default bg_panel_op">
        	<div class="panel-heading">
          	<h3 class="panel-title rob">
            	<a class="rob alle_link show-pop-list_g btn btn-default" style="padding:2px 4px !important;" id="postOpMedsPop" data-frame-name="PostOp">
              	<span class="fa fa-caret-right"></span> Post-Op Meds
           		</a>
            </h3>
         	</div>
        	<div class="clearfix"></div>
          <div class="table-responsive lsttable">
            <div class="fixed-table">
                <div class="table-content">
                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-condensed cf width_table table-striped" >
                              <thead>
                                <tr>
                                  <th style="width:45%;"> Medication </th>
                                  <th > #Lot </th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="2" style="padding:1px 0">
                                    <div class="over_wrap" id="spreadSheetIntravitrealMed" >
                                    <?php printSpreadSheet('PostOp',$postOpMeds);?>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                             </table>
                </div>
            </div>
          </div>
   			</div>
			</div>
			
      <div class="clearfix visible-sm visible-xs"></div>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
  			<div class="panel panel-default bg_panel_op">
        	<div class="panel-heading">
          	<h3  class="panel-title rob">Complications</h3>
        	</div>
          <div class="clearfix "></div>
          <div class="panel_body padding_10">
          
          	<div class="row">
            	<div class="col-xs-12 col-sm-12 padding_0 ">
                <div class="col-sm-3 col-lg-2">
                  <label class="checkbox-inline padding_0"><b>Complications:</b></label>
                </div>
                <div class="col-sm-9 col-lg-10">   
                  <span>
                    <span class="colorChkBx" style="<?=$complicationsBgColor?>" onclick="checkSingle('complicationsYes','complications'); <?=$complicationsGrpFun?>" >
                      <input type="checkbox" id="complicationsYes" value="Yes" class="complications" name="complications" <?=($complications == 'Yes' ? 'checked' : '')?> /> 
                    </span>Yes   
                  </span>
                  <span >
                    <span class="colorChkBx"  style="<?=$complicationsBgColor?>"  onclick="checkSingle('complicationsNo','complications');<?=$complicationsGrpFun?>" >
                      <input type="checkbox" id="complicationsNo" value="No" class="complications" name="complications" <?=($complications == 'No' ? 'checked' : '')?>  />
                    </span> No
                  </span>
                </div>
           		</div>
             
      				<div class="clearfix margin-adjustment-only"></div>
            	
              <div class="col-xs-12 col-sm-12 margin_top_5 padding_0 ">
              	<div class="col-sm-3 col-lg-2"><label>Comments:</label></div>
                <div class="col-sm-9 col-lg-10">
                	<textarea class="form-control complications" rows="3" name="comments" id="comments" style="<?=$complicationsBgColor?>" onFocus="<?=$complicationsGrpFun?>" onBlur="<?=$complicationsGrpFun?>" onKeyup="<?=$complicationsGrpFun?>" ><?=$comments?></textarea>
               	</div>
            	</div>    
            
            </div>
        	</div>
     		</div>
    		<div class="clearfix"></div>
    	</div>
    
		</div>
    
    <div class="clearfix"></div>
    
    <div class="panel panel-default bg_panel_op margin_top_5">
        
    	<div class="panel-heading"><h3  class="panel-title rob">Vital Signs - Post Op</h3></div>
        
        <div class="panel-body" style="float:none;" >
          <div class="row col-lg-12 col-sm-12 col-md-12 col-xs-12">
        
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0  ">
              <label class="col-sm-4 col-md-3 padding_6">Time</label>
              <span class="col-sm-8 col-md-9 padding_6 ">
                <input type="text" class="form-control vitalSignGrid timeBox" name="postVitalTime" id="postVitalTime" value="<?=$postVitalTime?>" data-group="postVital" style="<?=$postVitalBgColor?>"  />
              </span>
                 
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-3 col-md-2 padding_6 text-right">BP</label>
              <span class="col-sm-9 col-md-10 padding_6">
                <input type="text " class="form-control vitalSignGrid" data-group="postVital" name="postVitalBp" id="postVitalBp" value="<?=$postVitalBp?>" style="<?=$postVitalBgColor?>" />
              </span>
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-3 col-md-2 padding_6 text-right">P</label>
              <span class="col-sm-9 col-md-10 padding_6">
                <input type="text" class="form-control vitalSignGrid" data-group="postVital" name="postVitalPulse" id="postVitalPulse" value="<?=$postVitalPulse?>" style="<?=$postVitalBgColor?>" />
              </span>
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-3 col-md-3 padding_6 text-right">R</label>
              <span class="col-sm-9 col-md-9 padding_6">
                <input type="text" class="form-control vitalSignGrid" data-group="postVital" name="postVitalResp" id="postVitalResp" value="<?=$postVitalResp?>" style="<?=$postVitalBgColor?>" />
              </span>
            </div>
            
            <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
              <label class="col-sm-4 col-md-3 padding_6 text-right">Spo2</label>
              <span class="col-sm-8 col-md-9 padding_6">
                <div class="input-group" style="width:100% !important;">
                  <input type="text" class="form-control vitalSignGrid" data-group="postVital" name="postVitalSpo" id="postVitalSpo" value="<?=$postVitalSpo?>" style="<?=$postVitalBgColor?>" />
                  <div class="input-group-addon padding_2"><b>%</b></div>
                </div>
              </span>
            </div>
          </div>
        
        	<div class="row col-lg-12 col-sm-12 col-md-12 col-xs-12">
          		
              <div class="col-xs-6 col-sm-20 col-md-20 padding_0  ">
                <label class="col-sm-4 col-md-3 padding_6">IOP</label>
                <span class="col-sm-8 col-md-9 padding_6 ">
                	<span class="col-sm-12 padding_2 " style="<?=$postIopBgColor?>">
                  <select class="form-control selectpicker select-mandatory" data-show-subtext="true" name="postIop" id="postIop" data-width="100%" onChange="javascript:changeSelectpickerColor('.select-mandatory');" >
    								<option value="" selected>- Select -</option>
                    <option value="TA" data-subtext="A" <?=(($postIop == 'TA') ? 'selected' : '')?>>T</option>
                    <option value="TP" data-subtext="P" <?=(($postIop == 'TP') ? 'selected' : '')?>>T</option>
                    <option value="TT" data-subtext="T" <?=(($postIop == 'TT') ? 'selected' : '')?>>T</option>
                    <option value="TX" data-subtext="X" <?=(($postIop == 'TX') ? 'selected' : '')?>>T</option>
                  </select>
                  </span>
                </span>
                 
            	</div>
              
              <div class="col-xs-6 col-sm-3 col-md-20 padding_0 ">
                <span class="col-sm-12 col-md-12 padding_6" style="white-space:nowrap" >
                	<label style="margin-left:15px;">
                  	<span class="colorChkBx" style="<?=$postIopSiteBgColor?>" onclick="checkSingle('postIopSiteOD','postIopSite'); changeDiffChbxColorNew('1','.postIopSite');" >
                    	<input type="checkbox" id="postIopSiteOD" value="OD" class="postIopSite" name="postIopSite" <?=($postIopSite == 'OD' ? 'checked' : '')?> />
                		</span>OD
                  </label>
                  <label style="margin-left:15px;">
                  	<span class="colorChkBx" style="<?=$postIopSiteBgColor?>"  onclick="checkSingle('postIopSiteOS','postIopSite');changeDiffChbxColorNew('1','.postIopSite');" >
                    	<input type="checkbox" id="postIopSiteOS" value="OS" class="postIopSite" name="postIopSite" <?=($postIopSite == 'OS' ? 'checked' : '')?> />
                		</span>OS
                	</label>
                </span>
              </div>
              
              <div class="col-xs-6 col-sm-20 col-md-20 padding_0 ">
                <label class="col-sm-3 col-md-2 padding_6 text-right">Time</label>
                <span class="col-sm-9 col-md-10 padding_6">
                  <input type="text " class="form-control vitalSignGrid timeBox" name="postIopTime" id="postIopTime" value="<?=$postIopTime?>" style="<?=$postIopTimeBgColor?>" onFocus="changeTxtGroupColor(1,'postIopTime');" onBlur="changeTxtGroupColor(1,'postIopTime');" onKeyUp="changeTxtGroupColor(1,'postIopTime');" />
                </span>
              </div>
          
          </div>
          
        </div>
        
  	</div>
    
    
		<div class="panel panel-default bg_panel_op margin_top_5">
    
    	<div class="panel-heading">
      	<h3  class="panel-title rob">Patient discharged to home in good condition with responsible adult.</h3>
     	</div>
      <div class="clearfix"></div>
      
      <div class="panel_body">
      	<div class="row">
        	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          
          	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <div class="inner_safety_wrap" id="TDnurse2NameId" style="display:<?php echo $TDnurse2NameIdDisplay;?>;">
                <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurse2SignBackColor?>;" onClick="javascript:<?php echo $callJavaFunNurse2;?>"> Nurse Signature </a>
              </div>
              
              <div class="inner_safety_wrap collapse" id="TDnurse2SignatureId" style="display:<?php echo $TDnurse2SignatureIdDisplay;?>;">
                <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunNurse2Del;?>"> <?php echo "<b>Nurse:</b> ".$Nurse2NameShow; ?>  </a></span>	     
                <span class="rob full_width"> <b> Electronically Signed: </b> <?php echo $nurse2SignOnFileStatus;?></span>
                <span class="rob full_width">
                  <b> Signature Date :</b>
                  <span class="dynamic_sig_dt" data-field-name="signNurse2DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"><?=$objManageData->getFullDtTmFormat($signNurse2DateTime)?>
                    <span class="fa fa-edit"></span>
                  </span>
                </span>
              </div>
          	
          	</div>
          	<div class="clearfix margin-adjustment-only border-dashed visible-xs"></div>
          	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          		
              <div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $surgeon1SignBackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon1;?>"> Surgeon Signature </a>
              </div>
              
              <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeon1Del;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ".$Surgeon1NameShow; ?>  </a></span>	     
                <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                <span class="rob full_width">
                  <b> Signature Date :</b>
                  <span class="dynamic_sig_dt"><?=$objManageData->getFullDtTmFormat($signSurgeon1DateTime)?></span>
                </span>
              </div>
          </div>
          </div>
    		</div>
     	</div>
  	
    </div>
    
    
  
  </div>
  </form>

<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="injection_misc.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->


<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "injection_misc.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($finalizeStatus!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
	</script>
	<?php
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
		
	</script>
	<?php
}

if($SaveForm_alert == 'true')
{
?>
<script>document.getElementById('divSaveAlert').style.display = 'block';</script>
<?php
}
?>	
<?php
	if($finalizeStatus!='true')
	{
		include('privilege_buttons.php');
	}
	include("print_page.php");
?>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
<?php include("common/medication_pop_admin.php"); ?>
</body>
</html>