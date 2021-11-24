<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
$tablename = "transfer_followups";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >
<?php
$spec = "
</head>
<body onLoad=\"top.changeColor('".$bglight_orange_physician."');\" onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();\">
";
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$patient_id = $_SESSION['patient_id'];
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	

$cancelRecord = $_REQUEST['cancelRecord'];
$submitMe = $_REQUEST['submitMe'];
//UPDATING PATIENT STATUS IN STUB TABLE
extract($_GET);


//GETTING CONFIRNATION DETAILS
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
		$TFAssignedSurgeonId = $surgeonId;
		$TFAssignedSurgeonName = stripslashes($surgeon_name);
		$TFAssignedPriProcId = $patient_primary_procedure_id;
	}
	
//GETTING CONFIRNATION DETAILS

if(!$cancelRecord){
	// FORM SHIFT TO RIGHT SLIDER
		$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId ', $pConfId);
		$transfer_and_followups_form = $getLeftLinkDetails->transfer_and_followups_form;	
		if($transfer_and_followups_form== 'true' ){
			$formArrayRecord['transfer_and_followups_form'] = 'false';
			$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
		}
	// FORM SHIFT TO RIGHT SLIDER
}
elseif($cancelRecord){
	$fieldName="transfer_and_followups_form";
	$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
	include("left_link_hide.php");
}	
$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;

if($submitMe=='true')
{
	$tablename = "transfer_followups";
	$transferFollowupId		=	$_POST['transferFollowupId'];
	//START CODE TO CHECK NURSE,SURGEON SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord($tablename, 'confirmation_id', $pConfId);
		if($chkNurseSignDetails)
		{
			$chk_signNurseId = $chkNurseSignDetails->signNurseId;
			$chk_signNurse1Id = $chkNurseSignDetails->signNurse1Id;
			$chk_signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
			$chk_vitalSignGridStatus	=	$chkNurseSignDetails->vitalSignGridStatus;
			//CHECK FORM STATUS
			$chk_form_status = $chkNurseSignDetails->form_status;
			//CHECK FORM STATUS
		}
		
		$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($chk_form_status,$chk_vitalSignGridStatus,'transferFollowup');
		
		
	//END CODE TO CHECK NURSE SIGN IN DATABASE 
	//echo '<pre>'; print_r($_POST);
	
	$formStatus = 'completed';
	if( 	   ($_POST['transfer_reason'] <> 'Emergency' &&  $_POST['transfer_reason'] <> 'Non-Emergency')
		|| ($_POST['hospital_contacted'] <> '1' )
		|| ($_POST['contacted_time'] == '' )
		|| ($_POST['transfer_method'] <> 'Taxi' &&  $_POST['transfer_method'] <> 'Private-Car' &&  $_POST['transfer_method'] <> 'Ambulance' )
		|| ($_POST['lv_running'] <> '1' &&  $_POST['lv_running'] <> '2' )
		|| ($_POST['airway_support'] <> '1' &&  $_POST['airway_support'] <> '2' )
		|| ($_POST['transfer_forms'] <> '1' && $_POST['transfer_forms'] <> '2' &&  $_POST['transfer_forms'] <> '3' )
		|| ($_POST['demographics'] <> '1' &&  $_POST['demographics'] <> '2' &&  $_POST['demographics'] <> '3' )
		|| ($_POST['chart_note'] <> '1' &&  $_POST['chart_note'] <> '2' &&  $_POST['chart_note'] <> '3' )
		|| ($_POST['ekg'] <> '1' &&  $_POST['ekg'] <> '2' && $_POST['ekg'] <> '3' )
		|| ($_POST['advance_directive'] <> '1' &&  $_POST['advance_directive'] <> '2' &&  $_POST['advance_directive'] <> '3' )
		|| ($_POST['cpr_report'] <> '1' &&  $_POST['cpr_report'] <> '2' &&  $_POST['cpr_report'] <> '3' )
		|| ($_POST['patient_belongings'] == '' )
		|| ($_POST['surgeon_reassessment'] <> 1)
		|| ($_POST['summary_of_care_time'] == '' )
		|| ($_POST['date_discharge_from_hospital'] == '' )
		|| ($_POST['fDate'] == '' )	
		|| ($chk_signNurseId == "0")
		|| ($chk_signNurse1Id == "0")
		|| ($chk_signSurgeon1Id == "0")
		
	){
		
		$formStatus = 'not completed';
	}
	
	$_POST['form_status']	=	$formStatus;
	
	unset($arrayRecord);
	$arrayRecord['confirmation_id']	=	$pConfId ;
	
	$tableFields	=	array('form_status','transfer_reason','transfer_reason_detail','hospital_contacted','hospital_name','contacted_time','transfer_method','ambulance_provider','lv_running','airway_support','o2at','transfer_forms','demographics','chart_note','lab_work','ekg','advance_directive','cpr_report','patient_belongings','additional_comments','surgeon_reassessment','summary_of_care_time','summary_of_care_notes','followup_status_filled','date_discharge_from_hospital','fDate','discharge_comments');
	
	foreach($tableFields as $field)
	{
		$_POST[$field] = addslashes(trim($_POST[$field]));
		if(($field == 'contacted_time' || $field == 'summary_of_care_time') && trim($_POST[$field]) )
			$val	=	date("H:i:s", strtotime(trim($_POST[$field])));	
		else if(($field == 'date_discharge_from_hospital' || $field == 'fDate') && trim($_POST[$field]) )
			$val	=	$objManageData->changeDateYMD(trim($_POST[$field]));	
		else	
			$val	=	isset($_POST[$field])		?	trim($_POST[$field])	:	'' ;
			
		$arrayRecord[$field]		=	$val;
	}
	
	if($chk_form_status <> 'completed' && $chk_form_status <> 'not completed' )
	{
			$arrayRecord['vitalSignGridStatus']		=	$vitalSignGridStatus;	
	}
	
	//START CODE TO RESET THE RECORD
	if($_REQUEST['hiddResetStatusId']=='Yes')
	{
		$formStatus	=	'';
		$arrayRecord['form_status'] 			= $formStatus;
		
		$arrayRecord['signNurseId'] 			= '';
		$arrayRecord['signNurseFirstName'] 		= '';
		$arrayRecord['signNurseMiddleName'] 	= '';
		$arrayRecord['signNurseLastName'] 		= '';
		$arrayRecord['signNurseStatus'] 		= '';
		$arrayRecord['signNurseDateTime'] 		= '0000-00-00 00:00:00';
		
		$arrayRecord['signNurse1Id'] 			= '';
		$arrayRecord['signNurse1FirstName'] 		= '';
		$arrayRecord['signNurse1MiddleName'] 	= '';
		$arrayRecord['signNurse1LastName'] 		= '';
		$arrayRecord['signNurse1Status'] 		= '';
		$arrayRecord['signNurse1DateTime'] 		= '0000-00-00 00:00:00';

		$arrayRecord['signSurgeon1Id'] 			= '';
		$arrayRecord['signSurgeon1FirstName'] 	= '';
		$arrayRecord['signSurgeon1MiddleName'] 	= '';
		$arrayRecord['signSurgeon1LastName'] 	= '';
		$arrayRecord['signSurgeon1Status'] 		= '';
		$arrayRecord['signSurgeon1DateTime'] 	= '0000-00-00 00:00:00';

		$arrayRecord['resetDateTime'] 			= $objManageData->getFullDtTmFormat(date('Y-m-d H:i:s'));
		$arrayRecord['resetBy'] 				= $_SESSION['loginUserId'];
		
	}
	//END CODE TO RESET THE RECORD
	
	//echo '<pre>'; print_r($arrayRecord); exit;
	
	//MAKE AUDIT STATUS REPORT
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] 					= $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] 				= $_SESSION['patient_id'];
	$arrayStatusRecord['confirmation_id'] 	= $pConfId;
	$arrayStatusRecord['form_name'] 			= 'transfer_and_followups_form';
	$arrayStatusRecord['action_date_time'] 	= $objManageData->getFullDtTmFormat(date('Y-m-d H:i:s'));
	//MAKE AUDIT STATUS REPORT
	
	if($transferFollowupId){
		$objManageData->updateRecords($arrayRecord, $tablename, 'transferFollowupId', $transferFollowupId);
	}else{
		$objManageData->addRecords($arrayRecord, $tablename);
	}
	
	
	// Code start here to save vital sign grid data 
	if($vitalSignGridStatus)
	{
		$vitalSignGridRecordIdArr	=	$_POST['vitalSignGridRecordId'] ;
		
		if(is_array($vitalSignGridRecordIdArr) && count($vitalSignGridRecordIdArr) > 0 )
		{
			foreach($vitalSignGridRecordIdArr as $key => $gridRowId)	
			{
					$row		=	$key +1;
					$vTime		=	$_POST['vitalSignGrid_'.$row.'_1'];
					$vSystolic	=	$_POST['vitalSignGrid_'.$row.'_2'];//$vBp
					$vDiastolic	=	$_POST['vitalSignGrid_'.$row.'_3'];
					$vPulse		=	$_POST['vitalSignGrid_'.$row.'_4'];
					$vRR			=	$_POST['vitalSignGrid_'.$row.'_5'];
					$vTemp		=	$_POST['vitalSignGrid_'.$row.'_6'];
					$vEtco2		=	$_POST['vitalSignGrid_'.$row.'_7'];
					$vosat2		=	$_POST['vitalSignGrid_'.$row.'_8'];
					
					if( $vTime && ($vSystolic || $vDiastolic || $vPulse || $vRR || $vTemp || $vEtco2 || $vosat2)  )
					{
						$vTime	=	date('H:i:s',strtotime($vTime));
						//echo $vTime.'<br>';
						$dataArray	=	array();
						$dataArray['chartName']			=	'transfer_and_followups_form';
						$dataArray['confirmation_id']	=	$pConfId ;
						$dataArray['start_time']			=	$vTime;
						$dataArray['systolic']				=	$vSystolic;
						$dataArray['diastolic']				=	$vDiastolic;
						$dataArray['pulse']					=	$vPulse;
						$dataArray['rr']						=	$vRR;
						$dataArray['temp']					=	$vTemp;
						$dataArray['etco2']					=	$vEtco2;
						$dataArray['osat2']					=	$vosat2;
						
						if($gridRowId)
						{
							$objManageData->UpdateRecord($dataArray,'vital_sign_grid','gridRowId',$gridRowId);
						}
						else
						{
							$chkRecords	=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$dataArray);
							if( !$chkRecords)
								$objManageData->addRecords($dataArray,'vital_sign_grid');	
						}
					}
					else
					{
						if($gridRowId)
						{
							$objManageData->DeleteRecord('vital_sign_grid','gridRowId',$gridRowId);	
						}
					}
			
					
			}
		}
	
	}
	// Code end here to save vital sign grid data 
	
	
	//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'transfer_and_followups_form';
		$conditionArr['status'] = 'created';
		$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);	
		if($chkAuditStatus) {
			//MAKE AUDIT STATUS MODIFIED
			$arrayStatusRecord['status'] = 'modified';
		}else {
			//MAKE AUDIT STATUS CREATED
			$arrayStatusRecord['status'] = 'created';
		}
		$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');												
	//CODE END TO SET AUDIT STATUS AFTER SAVE
	
	
	
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon	= chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry 		=	"UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
	
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse 		= chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry= "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

}

?>
<script type="text/javascript">


//FUNCTIONS RELATED TO DISPLAY SIGNATURE
	
	function noAuthorityFun(userName) {
		alert("You are not authorised to make signature of "+userName);
		return false;
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
			
			//SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
			if(userIdentity == 'Surgeon'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
						top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
					}
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE	
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
		
		var url=pagename;
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
	
//END FUNCTIONS RELATED TO DISPLAY SIGNATURE

</script>
<div id="post" style="display:none; position:absolute;"></div>
<?php

// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalize_status = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS


// Get Transfer Followps Details 
	$transferFollowpDetails	=	$objManageData->getExtractRecord('transfer_followups', 'confirmation_id', $pConfId);
	extract($transferFollowpDetails);
	
	$vitalSignGridStatus	=	$objManageData->loadVitalSignGridStatus($form_status,$vitalSignGridStatus,'transferFollowup');
	
	$signNurseName 		= $transferFollowpDetails['signNurseLastName'].", ".$transferFollowpDetails['signNurseFirstName']." ".$transferFollowpDetails['signNurseMiddleName'];
	
	$signNurse1Name 		= $transferFollowpDetails['signNurse1LastName'].", ".$transferFollowpDetails['signNurse1FirstName']." ".$transferFollowpDetails['signNurse1MiddleName'];

	$signSurgeon1Name 		= $transferFollowpDetails['signSurgeon1LastName'].", ".$transferFollowpDetails['signSurgeon1FirstName']." ".$transferFollowpDetails['signSurgeon1MiddleName'];
	
	
	$loggedInUserDetail	=	$objManageData->getRowRecord('users', 'usersId', $_SESSION["loginUserId"]);
	$loggedInUserName	= $loggedInUserDetail->lname.", ".$loggedInUserDetail->fname." ".$loggedInUserDetail->mname;
	$loggedInUserType		=	$loggedInUserDetail->user_type;
	$loggedInUserSig		=	$loggedInUserDetail->signature;
	
	if($loggedInUserType <> "Nurse") 
	{
		$loginUserName		= $_SESSION['loginUserName'];
		$callJavaFunNurse	= "return noAuthorityFunCommon('Nurse');";
		$callJavaFunNurse1	=	"return noAuthorityFunCommon('Nurse');";
	}
	else
	{
		$loginUserId			=	$_SESSION["loginUserId"];
		$callJavaFunNurse	=	"document.frm_transfer_followups.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse');";
		$callJavaFunNurse1	=	"document.frm_transfer_followups.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse1');";
	}
	// Nurse Signature
	$signOnFileStatusNurse			= "Yes";
	$TDnurseNameIdDisplay 		= "" ;
	$TDnurseSignatureIdDisplay	= "";
	$showNameNurse					= $loggedInUserName;
	$signBackColorNurse	=	$chngBckGroundColor;
	$signNurseDateTimeNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
	if($signNurseId<> 0 && $signNurseId<>"" )
	{
		$showNameNurse = $signNurseName;
		$signOnFileStatusNurse = $signNurseStatus;
		//$signNurseDateTime = date("m-d-Y h:i A",strtotime($signNurseDateTime));
		$signNurseDateTimeNew = $objManageData->getFullDtTmFormat($signNurseDateTime);
		$TDnurseNameIdDisplay = "none";
		$TDnurseSignatureIdDisplay = "in";
		$signBackColorNurse = $whiteBckGroundColor; 
	}
	
	$signNoToggleNurse	= 'data-target="#TDnurseSignatureId" data-toggle="collapse"';
	if($_SESSION["loginUserId"] == $signNurseId) 
	{
			$callJavaFunNurseDel = "document.frm_transfer_followups.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse','delSign');";
	}
	else 
	{
			$callJavaFunNurseDel = "alert('Only ".str_ireplace("'","\'",$signNurseName)." can remove this signature');";
			$signNoToggleNurse = '';
	}
	
	if($signNurseId <> 0)	{	 	}
	// End Nurse Signature
	
	
	// Nurse1 Signature
	$signOnFileStatusNurse1			= "Yes";
	$TDnurse1NameIdDisplay 		= "" ;
	$TDnurse1SignatureIdDisplay	= "";
	$showNameNurse1					= $loggedInUserName;
	$signBackColorNurse1				=	$chngBckGroundColor;
	$signNurse1DateTimeNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
	if($signNurse1Id<> 0 && $signNurse1Id<>"" )
	{
		$showNameNurse1 = $signNurse1Name;
		$signOnFileStatusNurse1 = $signNurse1Status;
		//$signNurse1DateTime = date("m-d-Y h:i A",strtotime($signNurse1DateTime));
		$signNurse1DateTimeNew = $objManageData->getFullDtTmFormat($signNurse1DateTime);
		$TDnurse1NameIdDisplay = "none";
		$TDnurse1SignatureIdDisplay = "in";
		$signBackColorNurse1 =	$whiteBckGroundColor; 
	}
	
	$signNoToggleNurse1	= 'data-target="#TDnurse1SignatureId" data-toggle="collapse" ';
	if($_SESSION["loginUserId"] == $signNurse1Id) 
	{
			$callJavaFunNurse1Del = "document.frm_transfer_followups.hiddSignatureId.value='TDnurse1NameId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Nurse1','delSign');";
	}
	else 
	{
			$callJavaFunNurse1Del = "alert('Only ".str_ireplace("'","\'",$signNurse1Name)." can remove this signature');";
			$signNoToggleNurse1 = '';
	}
	
	// End Nurse 1 Signature
	
	
	// Surgeon Sign
	
	if($loggedInUserType <> "Surgeon") 
	{
		$loginUserName		= $_SESSION['loginUserName'];
		$callJavaFunSurgeon1	= "return noAuthorityFunCommon('Surgeon');";
	}
	else
	{
		$loginUserId				=	$_SESSION["loginUserId"];
		$callJavaFunSurgeon1	=	"document.frm_transfer_followups.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Surgeon');";
	}
	
	$signOnFileStatusSurgeon1		= "Yes";
	$TDsurgeonNameIdDisplay 	= "" ;
	$TDsurgeonSignatureIdDisplay= "";
	$showNameSurgeon1				= $loggedInUserName;
	$signBackColorSurgeon1			=	$chngBckGroundColor;
	$signSurgeon1DateTimeNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
	if($signSurgeon1Id<> 0 && $signSurgeon1Id<>"" )
	{
		$showNameSurgeon1 = $signSurgeon1Name;
		$signOnFileStatusSurgeon1 = $signSurgeon1Status;
		//$signSurgeon1DateTime = date("m-d-Y h:i A",strtotime($signSurgeon1DateTime));
		$signSurgeon1DateTimeNew = $objManageData->getFullDtTmFormat($signSurgeon1DateTime);
		$TDsurgeon1NameIdDisplay = "none";
		$TDsurgeon1SignatureIdDisplay = "in";
		$signBackColorSurgeon1 =	$whiteBckGroundColor; 
	}
	$signNoToggleSurgeon1	= 'data-target="#TDsurgeon1SignatureId" data-toggle="collapse"';
	if($_SESSION["loginUserId"] == $signSurgeon1Id) 
	{
			$callJavaFunSurgeon1Del = "document.frm_transfer_followups.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','transfer_followups_ajaxSign.php','$loginUserId','Surgeon','delSign');";
	}
	else 
	{
			$callJavaFunSurgeon1Del = "alert('Only ".str_ireplace("'","\'",$signSurgeon1Name)." can remove this signature');";
			$signNoToggleSurgeon1 = '';
	}
	
	// End Surgeon Sign
	
// End Get Transfer Followups Details

?>
<form name="frm_transfer_followups" action="transfer_followups.php?submitMe=true" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
    
    <input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
		<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
		<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
		<input type="hidden" name="transferFollowupId" value="<?php echo $transferFollowupId; ?>">	
		<input type="hidden" name="frmAction" id="frmAction" value="transfer_followups.php">
		<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
    <input type="hidden" name="formStatus" id="formStatus" value="<?=$formStatus?>">
    <input type="hidden" name="go_pageval" value="<?php echo $tablename;?>">
    <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
    <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
    <input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
    <input type="hidden" name="vitalSignGridHolder" id="vitalSignGridHolder" />
    
    <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
		<?php include('saveDivPopUp.php');  ?>
    </div>
    <div class="scheduler_table_Complete" id="transferPanel" style="">
    		<?php
				$epost_table_name = $tablename;
				include("./epost_list.php");
			?>
        	<div class=" col-lg-12 col-sm-12 col-xs-12 col-md-12">
            		
                    <!--<div class="scanner_win new_s" ><h4 ><span>Transfer & Followups</span></h4></div>-->
                    	<div class="clearfix margin_adjustment_only"></div>
                        <div class="clearfix margin_adjustment_only"></div>
                        	
                    	
                    			
                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" >
                                	
                                    	<div class="inner_safety_wrap">
                                        	<div class="row">
                                            	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                	<label >Reason for Transfer</label>
                                                </div>
                                                <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 padding_0 ">
                                                	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                    	<label for ="chbx_tre">Emergency</label>
                                                        <span class="colorChkBx" style=" <?php if($transfer_reason) { echo $whiteBckGroundColor;}?> " >
                                                            <input type="checkbox" name="transfer_reason" <?php if($transfer_reason == 'Emergency') echo "CHECKED"; ?> value="Emergency" id="chbx_tre"  onClick="javascript:checkSingle('chbx_tre','transfer_reason'),changeChbxColor('transfer_reason')" />       
                                                        </span>
                                                        
                                                    </div>
                                                
                                                	<div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                                    	<label for="chbx_trne">Non-Emergency</label>
                                                        <span class="colorChkBx" style=" <?php if($transfer_reason) { echo $whiteBckGroundColor;}?> " >
                                                            <input type="checkbox" name="transfer_reason" <?php if($transfer_reason == 'Non-Emergency') echo "CHECKED"; ?> value="Non-Emergency" id="chbx_trne"  onClick="javascript:checkSingle('chbx_trne','transfer_reason'),changeChbxColor('transfer_reason')" />       
                                                        </span>
                                                        
                                                    </div>
                                                    
                                                    
                                                
												</div>                                                
                                       		</div>
                                      	</div>      
                                            
                                      	<div class="clearfix margin_adjustment_only "></div>
                                            
                                        <div class="inner_safety_wrap">    
                                            <div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                	<label >Reason for Transfer Details</label>
                                                </div>
                                                <div class="clearfix margin_adjustment_only "></div>
                                                <div class="clearfix margin_adjustment_only "></div>
                                                
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
                                                	<textarea class="form-control" name="transfer_reason_detail" id="transfer_reason_detail" ><?=$transfer_reason_detail?></textarea>
                                                </div>                                                
                                       		
                                            </div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                                        <div class="clearfix margin_adjustment_only "></div>
                                        
                                        <div class="inner_safety_wrap">
                                        	<div class="row">
                                            	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                	<label >Hospital Contacted</label>
                                                </div>
                                                <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9  ">
                                                
                                                	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 padding_0 ">
                                                    	<label for ="chbx_hospital_contacted" class="pl10" >Hospital</label>
                                                        <span class="colorChkBx" style=" <?php if($hospital_contacted) { echo $whiteBckGroundColor;}?> " >
                                                            <input type="checkbox"  name="hospital_contacted" <?php if($hospital_contacted == '1') echo "CHECKED"; ?> value="1" id="chbx_hospital_contacted"  onClick="javascript:checkSingle('chbx_hospital_contacted','hospital_contacted'),changeChbxColor('hospital_contacted');" />       
                                                        </span>
                                                  	</div>
                                                	
                                                    <div class=" col-md-5 col-sm-6 col-xs-6 col-lg-6   ">
                                                    	<?php
															$hospitalNameBackColor	=	($hospital_name) ? '#FFF' : '#F6C67A';
														?>
                                                        <label class="col-md-3 col-sm-3 col-xs-3 col-lg-3  padding_0" >Hospital Name</label>
                                                        <span class="col-md-9 col-sm-9 col-xs-9 col-lg-9 ">
                                                        <input type="text" name="hospital_name" class="form-control " id="hospital_name" onfocus="changeTxtGroupColor(1,'hospital_name');" onkeyup="changeTxtGroupColor(1,'hospital_name');" onblur="if(this.value){this.style.backgroundColor='#FFF' }" style=" background-color:<?=$hospitalNameBackColor?>; height:28px; " value="<?=$hospital_name?>"  />
                                                        </span>
                                                    </div>
                                                    <?php
														//$contactedTime = date('h:i A', strtotime($contacted_time));
														$contactedTime = $objManageData->getTmFormat($contacted_time);
														if(($form_status <> 'completed' || $form_status <> 'not completed') && $contacted_time ==  '00:00:00')
														{
															$contactedTime = '';	
														}
														$contactedTimeBackColor	=	($contactedTime) ? '#FFF' : '#F6C67A';
													?>
                                                	<div class=" col-md-4 col-sm-3 col-xs-3 col-lg-3 row padding_0  ">
                                                    	<label class="padding_0  col-lg-4 col-md-4 col-sm-4 col-xs-4 " >Time&nbsp;</label>
                                                        <span class="padding_0 col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                        	<input type="text" class="form-control vitalSignGrid timeBox padding_0" name="contacted_time" id="contacted_time" onfocus="changeTxtGroupColor(1,'contacted_time');" onkeyup="changeTxtGroupColor(1,'contacted_time');" onblur="if(this.value){this.style.backgroundColor='#FFF' }" style=" background-color:<?=$contactedTimeBackColor?>; height:28px; " value="<?=$contactedTime?>" />
                                                      	</span>
                                                        
                                                    </div>
                                                    
                                              	</div>                                                
                                       		</div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                                        
                                        <div class="inner_safety_wrap">
                                        	<div class="row">
                                            	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
                                                	<label >Method of Transfer</label>
                                                </div>
                                                <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9 padding_0 ">
                                                
                                                	<div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 ">
                                                    	<label for ="chbx_taxi" style="padding-left:35px;">Taxi</label>
                                                        <span class="colorChkBx" style=" <?php if($transfer_method) { echo $whiteBckGroundColor;}?> " >
                                                            <input type="checkbox" name="transfer_method" <?php if($transfer_method == 'Taxi') echo "CHECKED"; ?> value="Taxi" id="chbx_taxi"  onClick="javascript:checkSingle('chbx_taxi','transfer_method'),changeChbxColor('transfer_method');" />       
                                                        </span>
                                                        
                                                    </div>
                                                
                                                	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5  ">
                                                    	<label for="chbx_pcar" style="padding-right:25px;" >Private Car</label>
                                                        <span class="colorChkBx" style=" <?php if($transfer_method) { echo $whiteBckGroundColor;}?> " >
                                                            <input type="checkbox" name="transfer_method" <?php if($transfer_method == 'Private-Car') echo "CHECKED"; ?> value="Private-Car" id="chbx_pcar"  onClick="javascript:checkSingle('chbx_pcar','transfer_method'),changeChbxColor('transfer_method')" />       
                                                        </span>
                                                        
                                                    </div>
                                                    
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4  ">
                                                    	<label for="chbx_ambulance">Ambulance</label>
                                                        <span class="colorChkBx" style=" <?php if($transfer_method) { echo $whiteBckGroundColor;}?> " >
                                                            <input type="checkbox" name="transfer_method" <?php if($transfer_method == 'Ambulance') echo "CHECKED"; ?> value="Ambulance" id="chbx_ambulance"  onClick="javascript:checkSingle('chbx_ambulance','transfer_method'),changeChbxColor('transfer_method')" />       
                                                        </span>
                                                        
                                                    </div>
                                                    
                                                
												</div>                                                
                                       		</div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                                        
                                        <div class="inner_safety_wrap">    
                                            <div class="row">
                                          		<div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">  	
                                            		<label >Ambulance Provider</label>
                                                	<div class="clearfix margin_adjustment_only "></div>
                                                	<div class="clearfix margin_adjustment_only "></div>
                                                	
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 padding_0 ">
                                                        <textarea class="form-control" name="ambulance_provider" id="ambulance_provider" placeholder="Ambulance Provider " ><?=$ambulance_provider?></textarea>
                                                    </div>
                                                
                                                </div>
                                            	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 padding_0" >  
                                                	<div class="inner_safety_wrap" id="TDnurseNameId">
                                                    	<a style="display:<?php echo $TDnurseNameIdDisplay; ?>; <?=$signBackColorNurse?>;" <?=$signNoToggleNurse?> class="sign_link collapsed" href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFunNurse;?>">Nurse Signature</a>
                                                  	</div>      
                                                 	<div id="TDnurseSignatureId" class="inner_safety_wrap collapse <?php echo $TDnurseSignatureIdDisplay;?>" >
                                                            <span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunNurseDel;?>" >Nurse: <?php echo $showNameNurse; ?>  </span>	     
                                                            <span class="rob full_width" > <b> Electronically Signed </b> <?php echo $signOnFileStatusNurse; ?></span>
                                                            <span class="rob full_width" > <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurseDateTimeNew; ?> <span class="fa fa-edit"></span></span></span>
                                                    </div>       
                                                </div>
                                                                                                
                                       		
                                            </div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                                        
                                        <div class="inner_safety_wrap">    
                                            <div class="row">
                                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                	<label >&nbsp;</label>
                                                </div>
                                                
                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                	<div class="col-md-2 col-sm-3 col-xs-4 col-lg-2 padding_0 ">
                                                    	<label><b>Yes</b></label>	
                                                    </div>
                                                    <div class="col-md-2 col-sm-3 col-xs-4 col-lg-2 padding_0 ">
                                                    	<label><b>No</b></label>	
                                                    </div>	
                                                    <div class="col-md-8 col-sm-6 col-xs-6 col-lg-8 ">
                                                    &nbsp;
                                                    </div>		
                                                </div>                                                
                                       		
                                            </div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                                        
                                        <div class="inner_safety_wrap">    
                                            <div class="row">
                                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                	<label >IV Running</label>
                                                </div>
                                                
                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                	<div class="col-md-2 col-sm-3 col-xs-4 col-lg-2  padding_0">
														<span class="colorChkBx" style=" <?php if($lv_running) { echo $whiteBckGroundColor;}?> " >
                                                        	<input type="checkbox" name="lv_running" value="2" <?=($lv_running == 2 ? 'checked' : '')?> id="chk_lv_yes" onClick="javascript:checkSingle('chk_lv_yes','lv_running'),changeChbxColor('lv_running')" />
                                                      	</span>
                                                   	</div>
                                                    <div class="col-md-2 col-sm-3 col-xs-4 col-lg-2 padding_0">
                                                    	<span class="colorChkBx" style=" <?php if($lv_running) { echo $whiteBckGroundColor;}?> " >
                                                        	<input type="checkbox" name="lv_running" value="1" <?=($lv_running == 1 ? 'checked' : '')?> id="chk_lv_no" onClick="javascript:checkSingle('chk_lv_no','lv_running'),changeChbxColor('lv_running')" />
                                                      	</span>
                                                        
                                                    </div>	
                                                    <div class="col-md-8 col-sm-6 col-xs-6 col-lg-8 ">
                                                    &nbsp;
                                                    </div>		
                                                </div>                                                
                                       		
                                            </div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                                        
                                        <div class="inner_safety_wrap">    
                                            <div class="row">
                                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                	<label >Airway Support ?</label>
                                                </div>
                                                
                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                	<div class="col-md-2 col-sm-3 col-xs-4 col-lg-2 padding_0 ">
														<span class="colorChkBx" style=" <?php if($airway_support) { echo $whiteBckGroundColor;}?> " >
                                                        	<input type="checkbox" name="airway_support" value="2" <?=($airway_support == 2 ? 'checked' : '')?> id="chk_air_yes" onClick="javascript:checkSingle('chk_air_yes','airway_support'),changeChbxColor('airway_support')" />
                                                      	</span>
                                                   	</div>
                                                    <div class="col-md-2 col-sm-3 col-xs-4 col-lg-2 padding_0">
                                                    	<span class="colorChkBx" style=" <?php if($airway_support) { echo $whiteBckGroundColor;}?> " >
                                                        	<input type="checkbox" name="airway_support" value="1" <?=($airway_support == 1 ? 'checked' : '')?> id="chk_air_no" onClick="javascript:checkSingle('chk_air_no','airway_support'),changeChbxColor('airway_support')" />
                                                      	</span>
                                                        
                                                    </div>	
                                                    <div class="col-md-8 col-sm-6 col-xs-6 col-lg-8 ">
                                                    		<span class="col-lg-2 col-md-2 col-sm-2 col-xs-2 padding_0">
                                                            	<label>O2@ &nbsp;</label>
                                                          	</span>
                                                            <span class="col-lg-9 col-md-9 col-sm-10 col-xs-10 ">
                                                            	<input type="text" class="form-control" name="o2at" id="o2at" value="<?=$o2at?>" />
                                                           	</span>     
                                                            
                                                    </div>		
                                                </div>                                                
                                       		
                                            </div>
                                      	</div>
                                        
                                        <div class="clearfix margin_adjustment_only "></div>
                               			<div class="clearfix margin_adjustment_only "></div>
                                    
                                    <?php if($vitalSignGridStatus) { ?>	         
                                        <!-- Vital Sign Grid Starts Here -->
                                        <div class="scanner_win new_s">
                                        		<h4><span>Vital Signs</span></h4>
                                       	</div>
                                        
                                        <div class="panel panel-default new_panel bg_panel_or" id="vital-grid" >
                                                    
                                                    <div class="panel-heading haed_p_clickable" >
                                                    	<div class="  col-md-12 col-sm-12 col-xs-12 col-lg-12 rob" style="color:white;">
                                                            <div class=" row col-md-6 col-sm-5 col-xs-5 col-lg-5" >
                                                                
                                                                
                                                                <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">Time</span>
                                                                <span  class="col-md-8 col-lg-8 col-sm-8 col-xs-8">B/P<br>
                                                                <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Systolic</span>
                                                                <span  class="col-md-6 col-lg-6 col-sm-6 col-xs-6">Diastolic</span></span>
                                                            </div>
                                                        
                                                            <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                                                
                                                                
                                                                <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Pulse</span>
                                                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">RR</span>
                                                                <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">Temp<sup>O</sup> C</span>
                                                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">EtCO<sub>2</sub></span>
                                                                <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">OSat<sub>2</sub></span>
                                                            </div>
                                                            <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
                                                    	</div>
                                                    </div>
                                                
                                                    <div class="panel-body" >
                                                        
                                                        <?php
                                                            $condArr		=	array();
                                                            $condArr['confirmation_id']	=	$pConfId ;
                                                            $condArr['chartName']	=	'transfer_and_followups_form' ;
                                                            
                                                            $gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time,gridRowId','Asc');
                                                            $gCounter	=	1;
                                                            if(is_array($gridData) && count($gridData) > 0  )
                                                            {
                                                                foreach($gridData as $gridRow)
                                                                {	
																	//$fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
																	$fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
																	$fieldId2 = 'vitalSignGrid_'.$gCounter.'_2' ;	$fieldValue2= $gridRow->systolic;
																	$fieldId3 = 'vitalSignGrid_'.$gCounter.'_3' ;	$fieldValue3= $gridRow->diastolic;
																	$fieldId4 = 'vitalSignGrid_'.$gCounter.'_4' ;	$fieldValue4= $gridRow->pulse;
																	$fieldId5 = 'vitalSignGrid_'.$gCounter.'_5' ;	$fieldValue5= $gridRow->rr;
																	$fieldId6 = 'vitalSignGrid_'.$gCounter.'_6' ;	$fieldValue6= $gridRow->temp;
																	$fieldId7 = 'vitalSignGrid_'.$gCounter.'_7' ;	$fieldValue7= $gridRow->etco2;
																	$fieldId8 = 'vitalSignGrid_'.$gCounter.'_8' ;	$fieldValue8= $gridRow->osat2;
                                                        ?>		
                                                                    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                                                                        <input type="hidden" name="vitalSignGridRecordId[]" value="<?=$gridRow->gridRowId?>"	/>
                                                                        <div class="row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                                                                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                <input type="text" name="<?=$fieldId1?>" class="vitalSignGrid" id="<?=$fieldId1?>" value="<?=$fieldValue1?>" data-row-id = "<?=$gCounter?>?" data-record-id="<?=$gridRow->gridRowId?>" />
                                                                            </span>
                                                                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                <input type="text" name="<?=$fieldId2?>" class="vitalSignGrid" id="<?=$fieldId2?>"  value="<?=$fieldValue2?>" />
                                                                            </span>
                                                                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                <input type="text" name="<?=$fieldId3?>" class="vitalSignGrid" id="<?=$fieldId3?>"  value="<?=$fieldValue3?>" />
                                                                            </span>
                                                                        </div>
                                                                        <div class="  col-md-6 col-sm-7 col-xs-7 col-lg-7" >
                                                                            <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3" >
                                                                                <input type="text" name="<?=$fieldId4?>" class="vitalSignGrid" id="<?=$fieldId4?>" value="<?=$fieldValue4?>"  />
                                                                            </span>
                                                                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                                <input type="text" name="<?=$fieldId5?>" class="vitalSignGrid" id="<?=$fieldId5?>"  value="<?=$fieldValue5?>" />
                                                                            </span>
                                                                            <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                                                                <input type="text" name="<?=$fieldId6?>" class="vitalSignGrid" id="<?=$fieldId6?>" value="<?=$fieldValue6?>"  />
                                                                            </span>
                                                                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                                <input type="text" name="<?=$fieldId7?>" class="vitalSignGrid" id="<?=$fieldId7?>" value="<?=$fieldValue7?>"  />
                                                                            </span>
                                                                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                                <input type="text" name="<?=$fieldId8?>" class="vitalSignGrid" id="<?=$fieldId8?>"  value="<?=$fieldValue8?>" />
                                                                            </span>
                                                                        </div>      
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                						<?php
                                            						$gCounter++ ;
																}
															}
														?>
                                
														<?php 
                                                            $dataStartRow = $gCounter ;	
                                                            for ($gRow = $dataStartRow; $gRow < ($dataStartRow+15) ; $gRow++)
                                                            {
                                                                    $fieldId1 = 'vitalSignGrid_'.$gRow.'_1' ;
                                                                    $fieldId2 = 'vitalSignGrid_'.$gRow.'_2' ;
                                                                    $fieldId3 = 'vitalSignGrid_'.$gRow.'_3' ;
                                                                    $fieldId4 = 'vitalSignGrid_'.$gRow.'_4' ;
                                                                    $fieldId5 = 'vitalSignGrid_'.$gRow.'_5' ;
                                                                    $fieldId6 = 'vitalSignGrid_'.$gRow.'_6' ;
                                                                    $fieldId7 = 'vitalSignGrid_'.$gRow.'_7' ;
																																		$fieldId8 = 'vitalSignGrid_'.$gRow.'_8' ;
                                                        ?>
                                                                    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                                        <input type="hidden" name="vitalSignGridRecordId[]" />
                                                                        <div class="row col-md-6 col-sm-5 col-xs-5 col-lg-5">
                                                                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                <input type="text" name="<?=$fieldId1?>" class="vitalSignGrid timeBox" id="<?=$fieldId1?>" data-row-id = "<?=$gRow?>" />
                                                                            </span>
                                                                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                <input type="text" name="<?=$fieldId2?>" class="vitalSignGrid" id="<?=$fieldId2?>"  />
                                                                            </span>
                                                                            <span  class="col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                <input type="text" name="<?=$fieldId3?>" class="vitalSignGrid" id="<?=$fieldId3?>"  />
                                                                            </span>
                                                                        </div>
                                                                        <div class=" col-md-6 col-sm-7 col-xs-7 col-lg-7">
                                                                            <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                                                                <input type="text" name="<?=$fieldId4?>" class="vitalSignGrid" id="<?=$fieldId4?>"  />
                                                                            </span>
                                                                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                                <input type="text" name="<?=$fieldId5?>" class="vitalSignGrid" id="<?=$fieldId5?>"  />
                                                                            </span>
                                                                            <span  class="col-md-3 col-lg-3 col-sm-3 col-xs-3">
                                                                                <input type="text" name="<?=$fieldId6?>" class="vitalSignGrid" id="<?=$fieldId6?>"  />
                                                                            </span>
                                                                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                                <input type="text" name="<?=$fieldId7?>" class="vitalSignGrid" id="<?=$fieldId7?>"  />
                                                                            </span>
                                                                            <span  class="col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                                                <input type="text" name="<?=$fieldId8?>" class="vitalSignGrid" id="<?=$fieldId8?>"  />
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                        <?php		
                                                            }
                                                        ?>
                                                        
                                                    
                                                    </div>
                                                    
                                      	</div>
                                        <!-- Vital Sign Grid Ends Here -->
                                  	<?php } ?>	
                                    
                                    
                              	</div>
                                
                                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" >
                        				<div class="panel panel-default new_panel bg_panel_or"  >
                                                    
                                                    <div class="panel-heading" >
                                                    	<div class="  col-md-12 col-sm-12 col-xs-12 col-lg-12 rob" style="color:white;">
                                                        	Document Check List
                                                    	</div>
                                                    </div>
                                                		
                                                    <div class="panel-body" >
                                                        
                                                        <div class="row">	
                                                  			<div class="inner_safety_wrap">
                                                            	<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">&nbsp;</div>
                                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                                                	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center"><label>Sent</label></div>
                                                                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-center"><label>Not Sent</label></div>
                                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"><label>N/A</label></div>
                                                                </div>
                                                          	</div> 
                                                            
                                                            <?php
                                                            	$documents	=	array(
																									'transfer_forms'=>'Transfer Forms',
																									'demographics'=>'Demographics',
																									'chart_note'=> 'Chart Note',
																									'lab_work' => 'Lab Work',
																									'ekg'=>'EKG',
																									'advance_directive'=>'Advance Directive<br>(if available)',
																									'cpr_report' => 'CPR Report'
																								);
																foreach($documents as $key=>$document)
																{
															?>
                                                                    <div class="inner_safety_wrap">
                                                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7  "><label><?=$document?></label></div>
                                                                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5  ">
                                                                        	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                                                                                <span class="colorChkBx" style=" <?php if($$key) { echo $whiteBckGroundColor;}?> " >
                                                                                    <input type="checkbox" name="<?=$key?>" value="3" <?=($$key == 3 ? 'checked' : '')?> id="chk_<?=$key?>_sent" onClick="javascript:checkSingle('chk_<?=$key?>_sent','<?=$key?>'),changeChbxColor('<?=$key?>')" />
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-center">
                                                                                <span class="colorChkBx" style=" <?php if($$key) { echo $whiteBckGroundColor;}?> " >
                                                                                    <input type="checkbox" name="<?=$key?>" value="2" <?=($$key == 2 ? 'checked' : '')?> id="chk_<?=$key?>_not_sent" onClick="javascript:checkSingle('chk_<?=$key?>_not_sent','<?=$key?>'),changeChbxColor('<?=$key?>')" />
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-left">
                                                                                <span class="colorChkBx" style=" <?php if($$key) { echo $whiteBckGroundColor;}?> " >
                                                                                    <input type="checkbox" name="<?=$key?>" value="1" <?=($$key == 1 ? 'checked' : '')?> id="chk_<?=$key?>_na" onClick="javascript:checkSingle('chk_<?=$key?>_na','<?=$key?>'),changeChbxColor('<?=$key?>')" />
                                                                                </span>
                                                                            </div>
                                                                       	</div>
                                                                    </div>
                                                                
                                                                    <div class="clearfix margin_adjustment_only "></div>
                                                            <?php
																
																}
															
															?>
                                          					
                                                            <?php
																
																$patientBelongingsBackColor	=	($patient_belongings)	?	'#FFF'	:	'#F6C67A'	;
															?>                   
                                                           	<div class="inner_safety_wrap">
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><label>Patient Belongings</label></div>
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        	<textarea class= "form-control" name="patient_belongings" id="patient_belongings" placeholder="Patient Belongings" onfocus="changeTxtGroupColor(1,'patient_belongings');" onkeyup="changeTxtGroupColor(1,'patient_belongings');" onblur="if(!this.value){this.style.backgroundColor='#F6C67A' }" style=" background-color:<?=$patientBelongingsBackColor?>; "><?=$patient_belongings?></textarea>	
                                                                      	</div> 
                                                            </div>	
                                                            
                                                            <div class="clearfix margin_adjustment_only "></div>
                                                            <div class="clearfix margin_adjustment_only "></div>
                                                            
                                                            <div class="inner_safety_wrap">
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><label>Additional Comments</label></div>
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        	<textarea class= "form-control" name="additional_comments" id="additional_comments" placeholder="Additional Comments"><?=$additional_comments?></textarea>	
                                                                       	</div> 
                                                           	</div>	
                                                            
                                                            <div class="clearfix margin_adjustment_only "></div>
                                                            <div class="clearfix margin_adjustment_only "></div>
                                                            
                                                        </div>                                                    
                                                    </div>
                                                    
                                      	</div>
                             	</div>
                        
                        		
                                <div class="clearfix margin_adjustment_only "></div>
                                <div class="clearfix margin_adjustment_only "></div>
                                
                        		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                        				
                                        <div class="scanner_win new_s">
                                        		<h4><span>Summary of Care</span></h4>
                                       	</div>
                                        
                                        <div class="inner_safety_wrap">
                                        	<div class="row">
                                            	
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
                                                	<textarea class= "form-control" name="summary_of_care_notes" id="summary_of_care_notes" placeholder="Summary of Care Notes"><?=$summary_of_care_notes?></textarea>
                                              	</div>
                                            	
                                                <div class="clearfix margin_adjustment_only "></div>
                                                <div class="clearfix margin_adjustment_only "></div>
                                                
                                            	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5">
                                                	<label for ="chbx_surgeon_reassessment">Surgeon Reassessment</label>
                                                    <span class="colorChkBx" style=" <?php if($surgeon_reassessment) { echo $whiteBckGroundColor;}?> " >
                                                   		<input type="checkbox" name="surgeon_reassessment" <?php if($surgeon_reassessment == '1') echo "CHECKED"; ?> value="1" id="chbx_surgeon_reassessment"  onClick="javascript:checkSingle('chbx_surgeon_reassessment','surgeon_reassessment'),changeChbxColor('surgeon_reassessment')" />
                                                   	</span>
                                                	<div class="clearfix margin_adjustment_only "></div>
                                                    <label>It is my medical judgement that this transfer will not create a medical hazard to the patient.</label>
                                                </div>
                                                <div class="col-md-7 col-sm-7 col-xs-7 col-lg-7 padding_0 ">
                                                	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 padding_0">
                                                        <div class="inner_safety_wrap" id="TDsurgeon1NameId">
                                                        	<a style="display:<?php echo $TDsurgeon1NameIdDisplay; ?>; <?=$signBackColorSurgeon1?>;" <?=$signNoToggleSurgeon1?>class="sign_link collapsed" href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFunSurgeon1;?>">Surgeon Signature</a>
                                                     	</div>
                                                        <div id="TDsurgeon1SignatureId" class="inner_safety_wrap collapse <?php echo $TDsurgeon1SignatureIdDisplay;?>" >
                                                                <span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunSurgeon1Del;?>">Sugeon: <?php echo $showNameSurgeon1; ?>  </span>	     
                                                                <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatusSurgeon1; ?></span>
                                                                <span class="rob full_width" > <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signSurgeon1DateTimeNew; ?> <span class="fa fa-edit fa-editsurg"></span></span></span>
                                                     	</div>
                                                    </div>
                                                	<?php
														//$summaryCareTime = date('h:i A', strtotime($summary_of_care_time));
													    $summaryCareTime = $objManageData->getTmFormat($summary_of_care_time);
														if(($form_status <> 'completed' || $form_status <> 'not completed') && $summary_of_care_time ==  '00:00:00')
														{
															$summaryCareTime = '';	
														}
														$summaryCareTimeBackColor	=	($summaryCareTime) ? '#FFF' : '#F6C67A'; 
													?>
                                                	<div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
                                                        <span class="col-lg-2 col-md-3 col-sm-3 col-xs-3 ">
                                                        	<label >&nbsp;Time</label>
                                                       	</span>     
                                                      	<span class="col-lg-4 col-md-4 col-sm-5 col-xs-5 ">
                                                        	<input type="text" class="form-control  vitalSignGrid timeBox" name="summary_of_care_time" id="summary_of_care_time" onfocus="changeTxtGroupColor(1,'summary_of_care_time');" onkeyup="changeTxtGroupColor(1,'summary_of_care_time');" onblur="if(this.value){this.style.backgroundColor='#FFF' }" style=" background-color:<?=$summaryCareTimeBackColor?>; height:28px; " value="<?=$summaryCareTime?>" />
                                                        </span>
                                                    </div>
                                              	</div>
                                                
                                                <div class="clearfix margin_adjustment_only "></div>
                                          	
                                            </div>
                                      	</div>      
                                            
                                      	<div class="clearfix margin_adjustment_only "></div>
                                        
                             	</div>
                        
                        		
                                <div class="clearfix margin_adjustment_only "></div>
                                <div class="clearfix margin_adjustment_only "></div>
                        		
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12" >
                        				
                                        <div class="scanner_win new_s">
                                        		<h4><span>Hospital Transfer Follow Up</span></h4>
                                       	</div>
                                        
                                        <div class="inner_safety_wrap">
                                        	<div class="row">
                                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                	<label >Date Discharged from Hospital</label>
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 padding_0">
                                                    	<?php
																$dateDischarge	=	'';	
																$backgroundColor	=	'#F6C67A';
																if($date_discharge_from_hospital && $date_discharge_from_hospital <> '0000-00-00')
																{
																	$dateDischarge	=	date('m-d-Y', strtotime($date_discharge_from_hospital));
																	$backgroundColor	=	'#FFF';	
																}
																
															?>
                                                		<div class="input-group datepickertxt">
                                                        	<input type="text" aria-describedby="basic-addon1" name="date_discharge_from_hospital" placeholder="MM-DD-YYYY" class="form-control  datepickertxt required" value="<?=$dateDischarge?>" onfocus="changeTxtGroupColor(1,'date_discharge_from_hospital');" onkeyup="changeTxtGroupColor(1,'date_discharge_from_hospital');" onblur="if(this.value){this.style.backgroundColor='#FFF' }" style=" background-color: <?=$backgroundColor?>"  />
                                                            <div class="input-group-addon datepicker">
                                            					<a href="javascript:void(0)">
                                                                	<span class="glyphicon glyphicon-calendar"></span>
                                                               	</a>
                                        					</div>
                                   						</div>
   													
			                                  				</div>
                                              	</div>
                                              	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                	<label >Date</label>
                                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 padding_0">
                                                    	<?php
																$followupDate		=	'';	
																$backgroundColor	=	'#F6C67A';
																
																if($fDate && $fDate <> '0000-00-00')
																{
																	$followupDate	=	date('m-d-Y', strtotime($fDate));
																	$backgroundColor	=	'#FFF';	
																}
																
														?>
                                                		<div class="input-group datepickertxt">
                                                        	<input type="text" aria-describedby="basic-addon1" name="fDate" placeholder="MM-DD-YYYY" class="form-control  datepickertxt required" value="<?=$followupDate?>" onfocus="changeTxtGroupColor(1,'date_discharge_from_hospital');" onkeyup="changeTxtGroupColor(1,'date_discharge_from_hospital');" onblur="if(this.value){this.style.backgroundColor='#FFF' }" style=" background-color: <?=$backgroundColor?>"  />
                                                            <div class="input-group-addon datepicker">
                                            					<a href="javascript:void(0)">
                                                                	<span class="glyphicon glyphicon-calendar"></span>
                                                              	</a>
                                        					</div>
                                   						</div>
                                              		</div>
                                               	</div>
                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                	<div class="inner_safety_wrap" id="TDnurse1NameId">
                                                        	<a style="display:<?php echo $TDnurse1NameIdDisplay; ?>; <?=$signBackColorNurse1?>;" <?=$signNoToggleNurse1?> class="sign_link collapsed" href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFunNurse1;?>">Nurse Signature</a>
                                                     	</div>
                                                        <div id="TDnurse1SignatureId" class="inner_safety_wrap collapse <?php echo $TDnurse1SignatureIdDisplay;?>">
                                                                <span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunNurse1Del;?>">Nurse: <?php echo $showNameNurse1; ?>  </span>	     
                                                                <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatusNurse1; ?></span>
                                                                <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurse1DateTimeNew; ?> <span class="fa fa-edit"></span></span></span>
                                                     	</div>
                                               	</div>
                                              	
                                                
                                                <div class="clearfix margin_adjustment_only "></div>
                                                
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
                                                	<label>Discharge Comments</label>
                                                    <textarea class= "form-control" name="discharge_comments" id="discharge_comments" placeholder="Discharge Comments"><?=$discharge_comments?></textarea>
                                              	</div>
                                                
                                                
                                       		</div>
                                      	</div>      
                                            
                                      	<div class="clearfix margin_adjustment_only "></div>
                                        
                             	</div>
    		</div>
	</div> 
     
    
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="post_op_physician_orders.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "post_op_physician_orders.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM
if($finalize_status!='true'){
	?>
	<script>
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
	</script>
	<?php
	include('privilege_buttons.php');
}else{
	?>
	<script>
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
	</script>
	<?php
}
if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}

include("print_page.php");

?>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>