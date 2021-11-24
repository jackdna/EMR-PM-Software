<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
//$tablename = "operative_record.php";
$tablename = "operativereport";
$table="operatingroomrecords";
//include("common/linkfile.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Discharge Summary</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style>
	.drsElement {
		position: absolute;
		border: 1px solid #333;
	}
	.drsMoveHandle {
		height: 20px;
		background-color: #CCC;
		border-bottom: 1px solid #666;
	}
</style>
<?php
$spec = '</head>
<body  onClick="document.getElementById(\'divSaveAlert\').style.display = \'none\'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");
//include_once("admin/fckeditor/fckeditor.php");
//require_once("admin/ckeditor/ckeditor.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$operative_surgeon_sign=$_POST['operative_surgeon_sign'];
$pagename=explode("/", $_SERVER['REQUEST_URI']);
$page=explode("?",$pagename[2]);
$pageval="operativereport"; 
$pagename=$page[0];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
extract($_GET);
//CODE TO DISABLE SLIDER LINK AT SINGLE CLICK
	$patient_id = $_REQUEST["patient_id"];
	$pConfId = $_REQUEST["pConfId"];
	$thisId = $_REQUEST["thisId"];
	if($innerKey=="") {
		$innerKey = $_REQUEST["innerKey"];
	}
	if($preColor=="") {
		$preColor = $_REQUEST["preColor"];
	}	
	
	$fieldName = "surgical_operative_record_form";
	$pageName = "operative_record.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK

//START GET VOCABULARY OF ASC
$ascInfoArr = $objManageData->getASCInfo($_SESSION["facility"]);
//END GET VOCABULARY OF ASC

//GET PATIENT DETAIL
	$Operative_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$_REQUEST['patient_id']."'";
	$Operative_patientName_tblRes = imw_query($Operative_patientName_tblQry) or die(imw_error());
	$Operative_patientName_tblRow = imw_fetch_array($Operative_patientName_tblRes);
	$Operative_patientName = $Operative_patientName_tblRow["patient_fname"]." ".$Operative_patientName_tblRow["patient_mname"]." ".$Operative_patientName_tblRow["patient_lname"];
	$imwPatientId 	   = $Operative_patientName_tblRow["imwPatientId"];
	
	
	$Operative_patientNameDobTemp = $Operative_patientName_tblRow["date_of_birth"];
		$Operative_patientNameDob_split = explode("-",$Operative_patientNameDobTemp);
		$Operative_patientNameDob = $Operative_patientNameDob_split[1]."-".$Operative_patientNameDob_split[2]."-".$Operative_patientNameDob_split[0];
	

	$Operative_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Operative_patientConfirm_tblRes = imw_query($Operative_patientConfirm_tblQry) or die(imw_error());
	$Operative_patientConfirm_tblRow = imw_fetch_array($Operative_patientConfirm_tblRes);
	
	$ascId 	= $Operative_patientConfirm_tblRow["ascId"]; //GET ASCID

	//START GET ARRIVAL TIME
	$stubWaitingDetailArr = $objManageData->getStubWaitingDetail($_REQUEST['pConfId'],$Operative_patientConfirm_tblRow["dos"])	;
	$arrivalTime = ($stubWaitingDetailArr[0]) ? $stubWaitingDetailArr[0] : '';	
	//END GET ARRIVAL TIME

	//GET ASSIGNED SURGEON ID AND SURGEON NAME
		$operativeRecordAssignedSurgeonId = $Operative_patientConfirm_tblRow["surgeonId"];
		$operativeRecordAssignedSurgeonName = stripslashes($Operative_patientConfirm_tblRow["surgeon_name"]);
	//END GET ASSIGNED SURGEON ID AND SURGEON NAME
	
		$Operative_patientConfirmDosTemp = $Operative_patientConfirm_tblRow["dos"];
		$Operative_patientConfirmDos_split = explode("-",$Operative_patientConfirmDosTemp);
		$Operative_patientConfirmDos = $Operative_patientConfirmDos_split[1]."-".$Operative_patientConfirmDos_split[2]."-".$Operative_patientConfirmDos_split[0];

	$Operative_patientConfirmSurgeon = $Operative_patientConfirm_tblRow["surgeon_name"];
	$Operative_patientConfirmSiteTemp = $Operative_patientConfirm_tblRow["site"];
	$surgeonId = $Operative_patientConfirm_tblRow["surgeonId"];
	
	// APPLYING NUMBERS TO PATIENT SITE
		if($Operative_patientConfirmSiteTemp == 1) {
			$Operative_patientConfirmSite = "Left Eye";  //OD
		}else if($Operative_patientConfirmSiteTemp == 2) {
			$Operative_patientConfirmSite = "Right Eye";  //OS
		}else if($Operative_patientConfirmSiteTemp == 3) {
			$Operative_patientConfirmSite = "Both Eye";  //OU
		}else if($Operative_patientConfirmSiteTemp == 4) {
			$Operative_patientConfirmSite = "Left Upper Lid";
		}else if($Operative_patientConfirmSiteTemp == 5) {
			$Operative_patientConfirmSite = "Left Lower Lid";
		}else if($Operative_patientConfirmSiteTemp == 6) {
			$Operative_patientConfirmSite = "Right Upper Lid";
		}else if($Operative_patientConfirmSiteTemp == 7) {
			$Operative_patientConfirmSite = "Right Lower Lid";
		}else if($Operative_patientConfirmSiteTemp == 8) {
			$Operative_patientConfirmSite = "Bilateral Upper Lid";
		}else if($Operative_patientConfirmSiteTemp == 9) {
			$Operative_patientConfirmSite = "Bilateral Lower Lid";
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$patient_primary_procedure_id = $Operative_patientConfirm_tblRow["patient_primary_procedure_id"]; 
	$patient_secondary_procedure_id = $Operative_patientConfirm_tblRow["patient_secondary_procedure_id"]; 
	$patient_tertiary_procedure_id = $Operative_patientConfirm_tblRow["patient_tertiary_procedure_id"]; 
	$Operative_patientConfirmPrimProc = $Operative_patientConfirm_tblRow["patient_primary_procedure"];
	$Operative_patientConfirmSecProc  = $Operative_patientConfirm_tblRow["patient_secondary_procedure"];
	$Operative_patientConfirmTeriProc = $Operative_patientConfirm_tblRow["patient_tertiary_procedure"];
	$primary_procedure_is_inj_misc		=	$Operative_patientConfirm_tblRow['prim_proc_is_misc'];
	
	if($Operative_patientConfirmSecProc=="N/A") { $Operative_patientConfirmSecProc="";  }
//END GET PATIENT DETAIL

//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
if($surgeonId<>"") {
	
	$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
	$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
	while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
		$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
	}
	if(is_array($surgeonProfileIdArr)){
		$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
	} else {
		$surgeonProfileIdImplode = 0;
	}
	$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
	$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
	$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
	if($selectSurgeonProcedureNumRow>0) {
		while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
			$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
			if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
				$templateFound = "true";
				$operativeTemplateId = $selectSurgeonProcedureRow['operativeTemplateId'];
			}		
		}
	}	
	//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
	//FROM SURGEON'S DEFAULT PROFILE 
		/*if($templateFound<>"true") {
			$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
			$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
			while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
				$surgeonProfileIdArrNew[] = $selectSurgeonRow['surgeonProfileId'];
			}
			if(is_array($surgeonProfileIdArrNew)){
				$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArrNew);
			}else {
				$surgeonProfileIdImplode = 0;
			}
			$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) AND operativeTemplateId!=''  order by procedureName";
			$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
			$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
			if($selectSurgeonProcedureNumRow>0) {
				$templateFound = "true";
				$selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes);
					$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
					$operativeTemplateId = $selectSurgeonProcedureRow['operativeTemplateId'];
			}
		}	*/
	//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
	//FROM SURGEON'S DEFAULT PROFILE 
	//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
	
	
}

// Start Operative Template ID From Procedure Preference Card
	if($templateFound<>"true") 
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
					$operativeTemplateId	= $procPrefCardRow->operativeTemplateId;
					
					break; 
				}
			}
		}
	
	}
// End Operative Template ID From Procedure Preference Card
	

// Check If Procedure is Injection Procedure
	$primProcDetails	=	$objManageData->getRowRecord('procedures','procedureId',$patient_primary_procedure_id,'','','catId');
	if( $primProcDetails->catId <> '2' )
	{
		if($primary_procedure_is_inj_misc == '')
		{
			//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $primProcDetails->catId);
			$primary_procedure_is_inj_misc		=	$objManageData->verifyProcIsInjMisc($patient_primary_procedure_id);
			//($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
		}
		
	}else
	{
			$primary_procedure_is_inj_misc	=	'';
	}	
// End Check If Procedure is Injection Procedure


/******************************************
 Start Injection/Misc. Procedure Template
******************************************/
	if( $primProcDetails->catId <> '2' && $primary_procedure_is_inj_misc )
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
			$operativeTemplateId	= ($injMiscOperativeTemplateId)?	$injMiscOperativeTemplateId: $operativeTemplateId;
		}
	}
/******************************************
 End Injection/Misc. Procedure Template
******************************************/



$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId;

//SAVE RECORD IN DATABASE
if($_POST['SaveRecordForm']=='yes'){
	$text = $_REQUEST['getText'];
	$tablename = "operativereport";

	//START CODE TO CHECK SURGEON SIGN IN DATABASE
		$chkSurgeonSignDetails = $objManageData->getRowRecord('operativereport', 'confirmation_id', $pConfId);
		if($chkSurgeonSignDetails) {
			$chk_signSurgeon1Id = $chkSurgeonSignDetails->signSurgeon1Id;
		}
	//END CODE TO CHECK SURGEON SIGN IN DATABASE 
	
	$chkoperativeQry = "select * from `operativereport` where confirmation_id='".$_REQUEST["pConfId"]."'";
	$chkoperativeRes = imw_query($chkoperativeQry) or die(imw_error()); 
	$chkoperativeNumRow = imw_num_rows($chkoperativeRes);
		    $TemplateQry = "select * from operative_template";
			$TemplateRes = imw_query($TemplateQry) or die(imw_error()); 
			$TemplateNumRow = imw_num_rows($TemplateRes);
			$TemplateRow = imw_fetch_array($TemplateRes); 
			
			$temp_data = $_REQUEST['FCKeditor1'];
			$temp_data = addslashes($temp_data);
			
			$templateList = $_REQUEST['templateList'];
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if($chk_signSurgeon1Id=="0" || !$temp_data) {
			$form_status = "not completed";
		}
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	if($chkoperativeNumRow>0) {
	  	//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkoperativeRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		$SaveoperativeQry = "update `operativereport` set 
									reportTemplate = '".$temp_data."',
									userId='".$_SESSION['loginUserId']."',
									form_status ='".$form_status."',
									template_id = '".$templateList."',
									opreport_inte_sync_status='0',
									patientId='".$_REQUEST["patient_id"]."'
									WHERE 
									confirmation_id='".$_REQUEST["pConfId"]."'
									";
	}else {
		$SaveoperativeQry = "insert into `operativereport` set 
									reportTemplate = '".$temp_data."',
									form_status ='".$form_status."',
									template_id = '".$templateList."',
									patientId='".$_REQUEST["patient_id"]."',									
									userId='".$_SESSION['loginUserId']."',
									opreport_inte_sync_status='0',
									confirmation_id='".$_REQUEST["pConfId"]."'";
	}
	$SaveoperativeRes = imw_query($SaveoperativeQry) or die(imw_error());
	
	//SAVE ENTRY IN chartnotes_change_audit_tbl 
			
		
		$chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='".$_SESSION['loginUserId']."' AND
									patient_id='".$_REQUEST["patient_id"]."' AND
									confirmation_id='".$_REQUEST["pConfId"]."' AND
									form_name='".$fieldName."' AND
									status = 'created'";
									
		$chkAuditChartNotesRes = imw_query($chkAuditChartNotesQry) or die(imw_error());	
		$chkAuditChartNotesNumRow = imw_num_rows($chkAuditChartNotesRes);	
		if($chkAuditChartNotesNumRow>0) {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='modified',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}else {
			$SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='".$_SESSION['loginUserId']."',
										patient_id='".$_REQUEST["patient_id"]."',
										confirmation_id='".$_REQUEST["pConfId"]."',
										form_name='$fieldName',
										status='created',
										action_date_time='".date("Y-m-d H:i:s")."'";
		}					
		$SaveAuditChartNotesRes = imw_query($SaveAuditChartNotesQry) or die(imw_error());
	//END SAVE ENTRY IN chartnotes_change_audit_tbl
	
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

	//START SENDING OPNOTE TO iDOC
	if(trim($ascId)<>"" && trim($ascId)<>0 && $form_status=="completed" && $imwSwitchFile == "sync_imwemr.php" && $imwPatientId && $_REQUEST["pConfId"]) {
		include_once("sync_operative_record.php");
	}
	if(trim($ascId)<>"" && trim($ascId)<>0 && $form_status=="completed" && $imwSwitchFile == "sync_imwemr.php" && constant("INTE_SYNC") == "YES" && $_REQUEST["pConfId"]) {
		$syncExternalPdf = "yes";
		include_once("operative_recordPdf.php");			
	}
	//END SENDING OPNOTE TO BILLING SOFTWARE iDOC
	
}
//END SAVE RECORD IN DATABASE


//GET LOGGED IN USER TYPE
	unset($conditionArr);
	$conditionArr['usersId'] = $_SESSION["loginUserId"];
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$loggedUserType = $usersDetail->user_type;
		}
	}
//END GET LOGGED IN USER TYPE	

//VIEW RECORD FROM DATABASE
	$ViewoperativeQry = "select *,date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from `operativereport` where confirmation_id='".$_REQUEST["pConfId"]."'";
	$ViewoperativeRes = imw_query($ViewoperativeQry) or die(imw_error()); 
	$ViewoperativeNumRow = imw_num_rows($ViewoperativeRes);
	$ViewoperativeRow = imw_fetch_array($ViewoperativeRes); 
	$operative_surgeon_sign = $ViewoperativeRow["signature"];
	$operative_data = stripslashes($ViewoperativeRow["reportTemplate"]);
	$operative_data_check = $operative_data;
    $form_status = $ViewoperativeRow["form_status"];
	
	$signSurgeon1Id = $ViewoperativeRow["signSurgeon1Id"];
	$signSurgeon1DateTime = $ViewoperativeRow['signSurgeon1DateTime'];
	$signSurgeon1DateTimeFormat = $ViewoperativeRow['signSurgeon1DateTimeFormat'];
	$signSurgeon1FirstName = $ViewoperativeRow["signSurgeon1FirstName"];
	$signSurgeon1MiddleName = $ViewoperativeRow["signSurgeon1MiddleName"];
	$signSurgeon1LastName = $ViewoperativeRow["signSurgeon1LastName"];
	$signSurgeon1Status = $ViewoperativeRow["signSurgeon1Status"];
	
	$template_id 	= $ViewoperativeRow["template_id"];
	$template_id	=	!($template_id) ? $operativeTemplateId : $template_id ;
	if($signSurgeon1Id=='0') {
   	 $form_status = "not completed";
	}
	
	$saveLink = $saveLink."&amp;form_status=".$form_status;
	//FIRST TIME FETCH DATA FROM 'operative_template' TABLE	
		
		if(trim($operative_data)=="") {
			if($operativeTemplateId) {
				$ViewOperativeTemplateQry = "select * from operative_template where template_id = '$operativeTemplateId'";
			/*
			}
			else {
				$ViewOperativeTemplateQry = "select * from operative_template";
			}
			*/
				$ViewOperativeTemplateRes = imw_query($ViewOperativeTemplateQry) or die(imw_error()); 
				$ViewOperativeTemplateNumRow = imw_num_rows($ViewOperativeTemplateRes);
				$ViewOperativeTemplateRow = imw_fetch_array($ViewOperativeTemplateRes); 
				$operative_data = stripslashes($ViewOperativeTemplateRow["template_data"]);
			}
		}
	//FIRST TIME FETCH DATA FROM 'operative_template' TABLE		
	
	//FETCH DATA FROM OPERATINGROOMRECORD TABLE
	$diagnosisQry=imw_query("select preOpDiagnosis , postOpDiagnosis from operatingroomrecords where patient_id='$patient_id' and confirmation_id='$pConfId'");
	$diagnosisRes=imw_fetch_array($diagnosisQry);	
	$preopdiagnosis= $diagnosisRes["preOpDiagnosis"];
	$postopdiagnosis= $diagnosisRes["postOpDiagnosis"];
	if(trim($postopdiagnosis)=="") {
		$postopdiagnosis = $preopdiagnosis;
	}
	// END FETCH DATA FROM OPEARINGROOMRECORD TABLE
	
	
	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
		$operative_data= str_ireplace("{PATIENT ID}","<b>".$Operative_patientName_tblRow["patient_id"]."</b>",$operative_data);
		$operative_data= str_ireplace("{PATIENT FIRST NAME}","<b>".$Operative_patientName_tblRow["patient_fname"]."</b>",$operative_data);
		$operative_data= str_ireplace("{MIDDLE INITIAL}","<b>".$Operative_patientName_tblRow["patient_mname"]."</b>",$operative_data);
		$operative_data= str_ireplace("{LAST NAME}","<b>".$Operative_patientName_tblRow["patient_lname"]."</b>",$operative_data);
		$operative_data= str_ireplace("{DOB}","<b>".$Operative_patientNameDob."</b>",$operative_data);
		$operative_data= str_ireplace("{DOS}","<b>".$Operative_patientConfirmDos."</b>",$operative_data);
		$operative_data= str_ireplace("{SURGEON NAME}","<b>".$Operative_patientConfirm_tblRow["surgeon_name"]."</b>",$operative_data);
		$operative_data= str_ireplace("{ARRIVAL TIME}","<b>".$arrivalTime."</b>",$operative_data);
		$operative_data= str_ireplace("{SITE}","<b>".$Operative_patientConfirmSite."</b>",$operative_data);
		$operative_data= str_ireplace("{PROCEDURE}","<b>".$Operative_patientConfirmPrimProc."</b>",$operative_data);
		$operative_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Operative_patientConfirmSecProc."</b>",$operative_data);
		$operative_data= str_ireplace("{TERTIARY PROCEDURE}","<b>".$Operative_patientConfirmTeriProc."</b>",$operative_data);
		
		$operative_data= str_ireplace("{PRE-OP DIAGNOSIS}","<b>".$preopdiagnosis."</b>",$operative_data);
		$operative_data= str_ireplace("{POST-OP DIAGNOSIS}","<b>".$postopdiagnosis."</b>",$operative_data);
		$operative_data= str_ireplace("{DATE}","<b>".date('m-d-Y')."</b>",$operative_data);
		$operative_data= str_ireplace("{TIME}","<b>".$objManageData->getTmFormat(date('H:i:s'))."</b>",$operative_data);
		$operative_data= str_ireplace("{ASC NAME}",$_SESSION['loginUserFacilityName'],$operative_data);
		$operative_data= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$operative_data);
		$operative_data= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$operative_data);
		
		//file_put_contents('test.txt',$operative_data);
	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
//END VIEW RECORD FROM DATABASE
//SELECT SIGNATURE FROM OPERATINGRECORD TABLE
$signQry= imw_query("select * from operativereport where patientId='".$_REQUEST["patient_id"]."' AND confirmation_id='".$_REQUEST["pConfId"]."'");
$res=imw_fetch_array($signQry);
$signatureVar= $res['signature'];
//END SELECT SIGNATURE FROM OPERATIVEREPORT TABLE

//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE

	$ViewOpRoomRecordQry = "select operatingRoomRecordsId,iol_ScanUpload,iol_ScanUpload2,post2OperativeReport from `operatingroomrecords` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
	$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
	if($ViewOpRoomRecordNumRow>0) {
		$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
		$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
		$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
		$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
		$post2OperativeReport = $ViewOpRoomRecordRow["post2OperativeReport"];
	}	
//UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE

?>
<script type="text/javascript" src="admin/ckeditor/ckeditor.js"></script>
<script>
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frm_operative_record.operative_surgeon_sign;
	appName = objElem.name;
	coords = getCoords(appName);
	objElemSign.value = refineCoords(coords);
}
function refineCoords(coords){	
	isEmpty = coords.lastIndexOf(";");	
	if(isEmpty == -1){
		coords += ";";	
	}else{
		coords = coords.substr(0,isEmpty+1);		
	}		
	return coords;	
}
function getCoords(){
	var coords = document.applets["app_operativeSignature"].getSign();
	return coords;
}
function getclear_os(){
	document.applets["app_operativeSignature"].clearIt();
	changeColorThis(255,0,0);
	document.applets["app_operativeSignature"].onmouseout();
}
function changeColorThis(r,g,b){				
	document.applets['app_operativeSignature'].setDrawColor(r,g,b);								
}
//Applet
	function changeSliderColor(){
		top.changeColor('#BCD2B0');
	}
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
	
	
//FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE
	
	function noAuthorityFun(userName) {
		alert("You are not authorised to make signature of "+userName);
		return false;
	}
	function noSignInAdmin() {
		alert("You have yet to make signature in Admin");
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

		//START TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			var signCheck='true';
			var assignedSurgeonId = '<?php echo $operativeRecordAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $operativeRecordAssignedSurgeonName;?>';
			var assignedSurgeonName = '<?php echo $operativeRecordAssignedSurgeonName;?>';
			var loggedInUserType = '<?php echo $loggedUserType;?>';
			if(loggedInUserId!=assignedSurgeonId && !delSign && loggedInUserType=='Surgeon') {
				var rCheck = confirmOtherSurgeon("This patient is registered to Dr. "+assignedSurgeonName+"\t\t\t\tAre you sure you want to sign the Chart notes of this patient");
				if(rCheck==false) {
					signCheck='false';
				}else {
					signCheck='true';
				}
				
			}
		//END TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
		
		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
			
			//SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
			if(userIdentity=='Surgeon1'){
				if(top.document.forms[0]){
					if(top.document.forms[0].hidd_chkDisplaySurgeonSign) {
						top.document.forms[0].hidd_chkDisplaySurgeonSign.value = 'true';
					}
				}
			}		
			//END SET HIDDEN FIELD (hidd_chkDisplaySurgeonSign) TO TRUE AT MAINPAGE
		
		}else {
			if(signCheck=='true') {
				document.getElementById(TDUserNameId).style.display = 'none';
				document.getElementById(TDUserSignatureId).style.display = 'block';
			}
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
		if(signCheck=='true') { //TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			xmlHttp.onreadystatechange=displayUserSignFun;
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
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

//FUNCTION TO CHANGE TEMPLATE DATA IN EDITOR	
	function showTemplate(tempId) {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		}
		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="operative_ajax_template.php"
		url=url+"?template_id="+tempId
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		url=url+"&preColor="+preColor1
		
		//if(tempId) { //if tempId is not empty
			xmlHttp.onreadystatechange=SetContents;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
		//}	
	}
	function SetContents() // FUNCTION IMPLEMENT ACCORDING TO NEW CKEDITOR
	{
		
		// Get the editor instance that we want to interact with.
		var oEditor = CKEDITOR.instances.FCKeditor1;
	
		// Set editor contents (replace current contents).
		// http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.editor.html#setData
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
			oEditor.setData( xmlHttp.responseText );
		}
	}
	
	function showTemplateFun() {
		// This functions shows that you can interact directly with the editor area
		// DOM. In this way you have the freedom to do anything you want with it.
		
		// Get the editor instance that we want to interact with.
		var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
		// Get the Editor Area DOM (Document object).
		var oDOM = oEditor.EditorDocument ;
		// The are two diffent ways to get the text (without HTML markups).
		// It is browser specific.
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
			if ( document.all ) { // If Internet Explorer.
				oDOM.body.innerHTML =xmlHttp.responseText
			}				
			top.frames[0].setPNotesHeight();
		}
	}
	
	
		
//END FUNCTION TO CHANGE TEMPLATE DATA IN EDITOR

//START FUNCTIONS FOR IOL SCAN UPLOAD STICKER
function MM_openBrOpRoomWindow(theURL,winName,features) {
  window.open(theURL,winName,features);
}		
	
function showImgWindow(strImgNumber) {
	if(!strImgNumber) {
		strImgNumber = '';
	}
	var opRoomId = '<?php echo $operatingRoomRecordsId;?>';
	MM_openBrOpRoomWindow('opRoomImagePopUp.php?from=op_room_record&id='+opRoomId+'&imgNmbr='+strImgNumber,'OpRoomImage','scrollbars=yes,width=900,height=400,resizable=yes,location=yes,status=yes');
}
//END FUNCTIONS FOR IOL SCAN UPLOAD STICKER	
</script>

<div id="post" style="display:none; position:absolute;"></div>
<?php 
// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
// GETTING FINALIZE STATUS
//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>
	<form name="frm_operative_record" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="operative_record.php?saveRecord=true<?php echo $saveLink;?>" >
		<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
		<input type="hidden" name="formIdentity" id="formIdentity" value="">
		<input type="hidden" name="getText" id="getText">			
		<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
		<input type="hidden" name="ascId" id="ascId" value="<?php echo $ascId; ?>">
		<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
		<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $pageval;?>" />
		<input type="hidden" name="frmAction" id="frmAction" value="operative_record.php">
		<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
        
        <?php
				$epost_table_name = "operativereport";
				include("./epost_list.php");
			?>
            
        <!--<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_green">
            <span class="bg_span_green">Operative Report</span>
			
        </div>-->
         <?php   
			if(!$operative_data_check){
				$operativeReport_BackColor=$chngBckGroundColor;					
			}
            if($surgeonId) {
                $templateListsDetail = $objManageData->getArrayRecords('operative_template','surgeonId',$surgeonId, 'template_name', 'ASC');
				$condition_arr['1']='1';
				$communityTemplateLists = $objManageData->getMultiChkArrayRecords('operative_template', $condition_arr, 'template_name', 'ASC', " AND surgeonId='0'");
            }

		?>	
        <div class="scanner_win new_s bg_green_Sty">
         <Div class="change_temp_div">
        		<label class="rob col-md-5 col-sm-5 col-xs-5 col-lg-5 text-right" for="n_select">
                            Change Template
                </label>
                <Div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
                	
                    <select name="templateList" id="templateList" class="selectpicker form-control" onChange="javascript:showTemplate(this.value);">
                        <option value="">Select</option>
                        <?php if($templateListsDetail){?>
                    <optgroup label="Surgeon Templates" data-icon="glyphicon glyphicon-hand-right" class="optgroup">
                        <?php
                        
                            foreach($templateListsDetail as $key => $list){
                                $templateSelected='';
                                if($template_id == $list->template_id) {
                                    $templateSelected = 'selected';
                                }
												?>
                                <option value="<?php echo $list->template_id;?>" <?php echo $templateSelected;?>><?php echo stripslashes($list->template_name);?></option>
                        <?php }?>
                        </optgroup>
                        <?php }
                        if($communityTemplateLists){
						?>
                        <optgroup label="Community Templates" data-icon="glyphicon glyphicon-hand-right" class="optgroup">
                        <?php
                            foreach($communityTemplateLists as $key => $list){
                                $templateSelected='';
                                if($template_id == $list->template_id) {
                                    $templateSelected = 'selected';
                                }
																
                        ?>
                                <option value="<?php echo $list->template_id;?>" <?php echo $templateSelected;?>><?php echo stripslashes($list->template_name);?></option>
                        <?php }?>
                        </optgroup>
                        <?php }?>
                    </select>
                </Div>
         </Div>	
         <h4>
            <span style=" <?php echo $operativeReport_BackColor;?> ">Operative Record</span>
         </h4>
      
        </div>
            <div id="divSaveAlert" style="position:absolute; left:350px; top:30px; display:none; z-index:2">
                <?php 
                    $bgCol = '#779169';
                    $borderCol = '#779169';
                    include('saveDivPopUp.php'); 
                ?>
            </div>
        
        <Div class="consent_wrap_slider">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="op_right_main">
                    <textarea name="FCKeditor1" id="FCKeditor1" rows="" cols="" ><?php echo $operative_data;?></textarea>
                    <script>CKEDITOR.replace( 'FCKeditor1' );</script>
                </div>
            </div>
            <Div class="clearfix margin_adjustment_only"></Div>
            <Div class="clearfix margin_adjustment_only"></Div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                
                	<?php 
					//if($post2OperativeReport=="Yes") {
						if($iol_ScanUpload) {
					?>
							<img id="imgThumbNail" class="thumbnail" style="cursor:pointer; height:100px; width:150px;float:left;display:inline-block;margin-right:10px;" src="admin/logoImg.php?from=op_room_record&amp;id=<?php echo $operatingRoomRecordsId; ?>" onClick="showImgWindow();">&nbsp;&nbsp;
					<?php
						}
						if($iol_ScanUpload2) {
					?>
							<img id="imgThumbNail2" class="thumbnail" style="float:left;display:inline-block;cursor:pointer; height:100px; width:150px;" src="admin/logoImg2.php?from=op_room_record&amp;id=<?php echo $operatingRoomRecordsId; ?>" onClick="showImgWindow('secondImage');">
					<?php 
						} 
					//}
					?>
                
            </div>
			<?php
			//CODE RELATED TO SURGEON SIGNATURE ON FILE
				$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
				$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
				$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
				
				$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
				$loggedInUserType = $ViewUserNameRow["user_type"];
				$loggedInSignatureOfUser = $ViewUserNameRow["signature"];
				if($loggedInUserType<>"Surgeon") {
					$loginUserName = $_SESSION['loginUserName'];
					$callJavaFunSurgeon = "return noAuthorityFunCommon('Surgeon');";
				}else {
					$loginUserId = $_SESSION["loginUserId"];
					$callJavaFunSurgeon = "document.frm_operative_record.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','operative_record_ajaxSign.php','$loginUserId','Surgeon1');";
				}					
				$surgeon1SignOnFileStatus = "Yes";
				$TDsurgeon1NameIdDisplay = "block";
				$TDsurgeon1SignatureIdDisplay = "none";
				$Surgeon1Name = $loggedInUserName;
				//$signSurgeon1DateTimeFormatNew = date("m-d-Y h:i A");
				$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
				//echo $signSurgeon1Id;
				if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
					$Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
					$surgeon1SignOnFileStatus = $signSurgeon1Status;	
					$TDsurgeon1NameIdDisplay = "none";
					$TDsurgeon1SignatureIdDisplay = "block";
					$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signSurgeon1DateTime);
					
				}
				//CODE TO REMOVE SURGEON 1 SIGNATURE	
					if($_SESSION["loginUserId"]==$signSurgeon1Id) {
						$callJavaFunSurgeonDel = "document.frm_operative_record.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','operative_record_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
					}else {
						$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
					}
				//END CODE TO REMOVE SURGEON 1 SIGNATURE	
			//END CODE RELATED TO SURGEON SIGNATURE ON FILE
			
			//START SET BACKGROUND COLOR 
				$operativeReportSurgeonSignBackColor=$chngBckGroundColor;
				if($signSurgeon1Id!=0){
					$operativeReportSurgeonSignBackColor==$whiteBckGroundColor; 
				}
			//START SET BACKGROUND COLOR 
			?>            
            <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $operativeReportSurgeonSignBackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
                    </div>
                    <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ". $Surgeon1Name; ?>  </a></span>	     
                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                    	<span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signSurgeon1DateTimeFormatNew; ?> <span class="fa fa-edit fa-editsurg"></span></span></span>
                    </div>
           </div>
            
       </Div>
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="operative_record.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "operative_record.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($finalizeStatus!='true'){
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

//START CODE, ONLY THE SIGNED SURGEON CAN MODIFY THIS CHARTNOTE EXCEPT SUPER USER(i.e DISABLE 'Save' AND 'Save&Print' BUTTON FOR OTHER THAN SIGNED SURGEON EXCEPT SUPER USER)
$privileges 	= $_SESSION['userPrivileges'];
$privilegesArr 	= array();
$privilegesArr 	= explode(', ', $privileges);
if($signSurgeon1Id<>0 && $signSurgeon1Id<>"" && $_SESSION["loginUserId"]<>$signSurgeon1Id && !in_array('Super User', $privilegesArr)) {
	?>
	<script>
		if(top.document.getElementById('saveBtn')) {
			top.document.getElementById('saveBtn').style.display = 'none';
		}
		if(top.document.getElementById('SavePrintBtn')) {
			top.document.getElementById('SavePrintBtn').style.display = 'none';
		}
	</script>
	<?php
}
//END CODE, ONLY THE SIGNED SURGEON CAN MODIFY THIS CHARTNOTE(i.e DISABLE 'Save' AND 'Save&Print' BUTTON FOR OTHER THAN SIGNED SURGEON)

?>
<div id="imgDiv" style="position:absolute;display:none;left:150px;top:50px;width:550px;height:500">
	<iframe width="550" height="500" name="imageUplades" src="opRoomImagePopUp.php?from=op_room_record&amp;id=<?php echo $operatingRoomRecordsId; ?>"></iframe>
</div>
<?php include("print_page.php");?>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>