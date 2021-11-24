<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$loggedUserType = $_SESSION['loginUserType'];
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
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
<script src="js/epost.js"></script>
<?php
require_once("common/user_agent.php");
$tablename = "history_physicial_clearance";

include_once("common/commonFunctions.php");
$blockImg = "images/block.gif";
$noneImg = "images/none.gif";

$spec = "
</head>
<body onLoad=\"top.changeColor('".$commonbg_color."');\" onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();\">
";
include("common/link_new_file.php");
include_once("common/pre_define_medication.php"); //PRE OP  HEALTH QUEST (ALSO FOR LOCAL ANES, PRE-OP GEN ANES)

include_once("admin/classObjectFunction.php");
$objManageData 		= new manageData;


$thisId 	= $_REQUEST['thisId'];
$innerKey 	= $_REQUEST['innerKey'];
$preColor 	= $_REQUEST['preColor'];

//START CODE TO GET SUB TYPE OF USER
$usrAllSubTypeArr = array();
$usrAllQry 							= "SELECT usersId,user_sub_type FROM users";
$usrAllRes							= imw_query($usrAllQry);
if(imw_num_rows($usrAllRes)>0) {
	while($usrAllRow				= imw_fetch_array($usrAllRes)) {
		$usrAllId 					= $usrAllRow["usersId"];
		$userAllSubType 			= $usrAllRow["user_sub_type"];
		$usrAllSubTypeArr[$usrAllId]= $userAllSubType;
	}
}
//END CODE TO GET SUB TYPE OF USER


$patient_id 		= $_REQUEST['patient_id'];
$pConfId 			= $_REQUEST['pConfId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$ascId 				= $_SESSION['ascId'];
$SaveForm_alert 	= $_REQUEST['SaveForm_alert'];
$history_physicial_id = $_REQUEST['history_physicial_id'];
$cancelRecord 		= $_REQUEST['cancelRecord'];

//GETTING CONFIRNATION DETAILS
	$Confirm_patientPrimProc = "";
	$Confirm_patientPrimaryProcedureId = 0;
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
		$hpAssignedSurgeonId = $surgeonId;
		$hpAssignedSurgeonName = stripslashes($surgeon_name);
		$Confirm_patientPrimProc = stripslashes($patient_primary_procedure);
		$Confirm_patientPrimaryProcedureId = $patient_primary_procedure_id;
	}
	
//GETTING CONFIRNATION DETAILS
	$primary_procedureQry 			= "SELECT * FROM procedures WHERE name = '".addslashes($Confirm_patientPrimProc)."' OR procedureAlias='".addslashes($Confirm_patientPrimProc)."'";
	$primary_procedureRes 			= imw_query($primary_procedureQry);
	if(imw_num_rows($primary_procedureRes)<=0) {
		$primary_procedureQry 			= "SELECT * FROM procedures WHERE procedureId = '".$Confirm_patientPrimaryProcedureId."'";
		$primary_procedureRes 			= imw_query($primary_procedureQry);
	}
	$patient_primary_procedure_categoryID='';
	if(imw_num_rows($primary_procedureRes)>0) {
		$primary_procedureRow 			= imw_fetch_array($primary_procedureRes);
		$patient_primary_procedure_categoryID = $primary_procedureRow['catId'];
	}


	if(!$cancelRecord){
		////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails 	= $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$history_physical_form 	= $getLeftLinkDetails->history_physical_form;	
			if($history_physical_form=='true'){
				$formArrayRecord['history_physical_form'] = 'false'; 
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
			//MAKE AUDIT STATUS VIEW
			if($_REQUEST['saveRecord']!='true'){
				unset($arrayRecord);
				$arrayRecord['user_id'] 		= $_SESSION['loginUserId'];
				$arrayRecord['patient_id'] 		= $patient_id;
				$arrayRecord['confirmation_id'] = $pConfId;
				$arrayRecord['form_name'] 		= 'history_physical_form';
				$arrayRecord['status'] 			= 'viewed';
				$arrayRecord['action_date_time']= date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
			}
			//MAKE AUDIT STATUS VIEW
			
// DONE BY MAMTA
}elseif($cancelRecord=="true") {  //IF PRESS CANCEL BUTTON
			$fieldName 	= "history_physical_form";
			$pageName 	= "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId";
			include("left_link_hide.php");
}	
	$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;ascId='.$ascId.'&amp;fieldName='.$fieldName;

	//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
//	END DONE BY MAMTA
//adminHealthquestionare
	$selectAdminQuestionsQry	= "select * from healthquestioner";
	$selectAdminQuestions		= imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows	= imw_num_rows($selectAdminQuestions);
	$i=0;
	while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
	{
		foreach($ResultselectAdminQuestions as $key=>$value){
			$question[$i][$key]=$value;
		}
		$i++;	
	}
	
	//echo $question[2]['question'];
//End adminHealthquestionare

	/*
	*
	*	Code To get status of directory H&P
	*/
	$ekgHp		=	$objManageData->getDirContentStatus($pConfId,2);
	$ekgHpLink	=	'';
	if($ekgHp)
	{
			$ekgHpLink	=	'<a href="#" class="btn-sm ajaxDir" data-confirmation-id="'.$pConfId.'" >H&P</a>&nbsp;';	
	}
	$sxPlanSheet = $objManageData->getDirContentStatus($pConfId,3,1,'Sx Planning Sheet');
	if($sxPlanSheet)
	{
			$ekgHpLink	.=	$sxPlanSheet;	
	}
	
	if($_REQUEST['saveRecord'] <> 'true')
	{	
			$tbl	=	'history_physicial_clearance';
			$currHPFormStatus	= $objManageData->getRowRecord($tbl, 'confirmation_id ', $pConfId,'','','form_status');
			$formStatus		=	$currHPFormStatus->form_status ;
			
			if($ekgHp && constant('CHECK_H_AND_P') <> 'NO')	
			{	
				$formStatus	=	'completed';
				imw_query("Update ".$tbl." Set form_status = '".$formStatus."', version_num = if(version_num=0,3,version_num) Where confirmation_id = '".$pConfId."'  ") or die('Error found at line no. '.(__LINE__).': '.imw_error());
			}
			elseif( (!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO'))  && ($formStatus === 'completed' || $formStatus === 'not completed' )) 
			{
				$chartStatus	=	$objManageData->validateChart('history_physicial_clearance.php',$_REQUEST['pConfId'],$patient_primary_procedure_categoryID);
				$formStatus		=	($chartStatus) ? 'completed' : 'not completed';
				imw_query("Update ".$tbl." Set form_status = '".$formStatus."' Where confirmation_id = '".$pConfId."'  ") or die('Error found at line no. '.(__LINE__).': '.imw_error());
				
			}
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";
	}


if($_REQUEST['saveRecord']=='true'){

	$text 							= $_REQUEST['getText'];
	$tablename 					= "history_physicial_clearance";
	unset($arrayRecord);
	$arrayRecord['confirmation_id'] = $pConfId;
	$arrayRecord['patient_id'] = $_REQUEST['patient_id'];
	
	
//START CODE TO CHECK NURSE SIGN IN DATABASE
	$chkUserSignDetails = $objManageData->getRowRecord('history_physicial_clearance', 'confirmation_id', $pConfId);
	if($chkUserSignDetails) {
		$chk_signSurgeon1Id		= $chkUserSignDetails->signSurgeon1Id;
		$chk_signAnesthesia1Id	= $chkUserSignDetails->signAnesthesia1Id;
		$chk_signNurseId 		= $chkUserSignDetails->signNurseId;
		$chk_form_status = $chkUserSignDetails->form_status;
		$chk_version_num	= $chkUserSignDetails->version_num;
		$chk_version_date_time = $chkUserSignDetails->version_date_time;
	}
//END CODE TO CHECK NURSE SIGN IN DATABASE 

	// Start Saving Pre Define Admin Questions
	for ($c = 0; $c < $_REQUEST['ques_count']; $c++){
		$ques = addslashes($_REQUEST['ques_'.$c]);
		$ques_status = $_REQUEST['ques_chk_'.$c];
		$ques_desc = ($ques_status == 'Yes') ? addslashes($_REQUEST['ques_desc_'.$c]) : '';
		
		$action = "Insert Into history_physical_ques Set ";
		$where = "";
		$value = "confirmation_id = '".$pConfId."', patient_id = '".$_REQUEST['patient_id']."', ques = '".$ques."', ques_status = '".$ques_status."', ques_desc = '".$ques_desc."' ";
		if( $chk_form_status == 'completed' || $chk_form_status=='not completed' ) {
			$action = "Update history_physical_ques Set ";
			$where = "Where confirmation_id = '".$pConfId."' And ques = '".$ques."' ";
			$value = "ques_status = '".$ques_status."', ques_desc = '".$ques_desc."' ";
		}
		
		$qry = $action.$value.$where;
		$sql = imw_query($qry) or die('Error found at line no. '.(__LINE__).': '.$qry.' --- '.imw_error());
		
	}
	// End Saving Pre Define Admin Questions
	
	// Check For chart version information
	$version_num	=	$chk_version_num;
	if(!$chk_version_num)
	{
		$version_date_time	=	$chk_version_date_time;
		if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
		{
			$version_date_time	=	date('Y-m-d H:i:s');
		}
				
		if($chk_form_status == 'completed' || $chk_form_status=='not completed'){
			$version_num = 1;
		}else{
			$version_num	=	4;
		}
		$arrayRecord['version_num'] = $version_num;
		$arrayRecord['version_date_time'] = $version_date_time;
	}
	
	
	if( $version_num > 1 )
	{	
		$heartExam = $_REQUEST['chbx_heart_exam'];
		$heartExamDesc = addslashes($_REQUEST['heartExamDesc']);
		$lungExam = $_REQUEST['chbx_lung_exam'];
		$lungExamDesc = addslashes($_REQUEST['lungExamDesc']);
		
		$arrayRecord['heartExam'] = $heartExam;
		$arrayRecord['heartExamDesc'] = ( 'no' == strtolower($heartExam) ) ? $heartExamDesc : '';
		$arrayRecord['lungExam'] = $lungExam;
		$arrayRecord['lungExamDesc'] = ( 'no' == strtolower($lungExam) ) ? $lungExamDesc : '';
	}
	if( $version_num > 2 ) {
		$arrayRecord['discussedAdvancedDirective'] = addslashes($_REQUEST['chbx_advance_directive']);
	}
	if( $version_num > 3 ) {
		
		$highCholesterol = $_REQUEST['chbx_high_cholesterol'];
		$highCholesterolDesc = addslashes($_REQUEST['highCholesterolDesc']);
		$thyroid = $_REQUEST['chbx_thyroid'];
		$thyroidDesc = addslashes($_REQUEST['thyroidDesc']);
		$ulcer = $_REQUEST['chbx_ulcer'];
		$ulcerDesc = addslashes($_REQUEST['ulcerDesc']);
		
		$arrayRecord['highCholesterol'] = $highCholesterol;
		$arrayRecord['highCholesterolDesc'] = ( 'yes' == strtolower($highCholesterol) ) ? $highCholesterolDesc : '';
		$arrayRecord['thyroid'] = $thyroid;
		$arrayRecord['thyroidDesc'] = ( 'yes' == strtolower($thyroid) ) ? $thyroidDesc : '';
		$arrayRecord['ulcer'] = $ulcer;
		$arrayRecord['ulcerDesc'] = ( 'yes' == strtolower($ulcer) ) ? $ulcerDesc : '';
	}

if($ekgHp && constant('CHECK_H_AND_P') <> 'NO') {	$formStatus	=	'completed';	}
elseif( ($_POST['chbx_cad_mi']!='') 
	&& ($_POST['chbx_cva_tia']!='') && ($_POST['chbx_htn_cp']!='')
	&& ($_POST['chbx_anticoagulation_therapy']!='') && ($_POST['chbx_respiratory_asthma']!='') 
	&& ($_POST['chbx_arthritis']!='') && ($_POST['chbx_diabetes']!='')
	&& ($_POST['chbx_recreational_drug']!='') && ($_POST['chbx_gi_gerd']!='')
	&& ($_POST['chbx_ocular']!='') && ($_POST['chbx_kidney_disease']!='')
	&& ($_POST['chbx_hiv_autoimmune']!='') && ($_POST['chbx_history_cancer']!='')
	&& ($_POST['chbx_organ_transplant']!='') && ($_POST['chbx_bad_reaction']!='')
	&& ($_POST['chbx_wear_contact_lenses']!='') && ($_POST['chbx_smoking']!='') 
	&& ($_POST['chbx_drink_alcohal']!='') && ($_POST['chbx_have_automatic']!='')  
	&& ($_POST['chbx_medical_history_obtained']!='') && ($chk_signSurgeon1Id!='0')
	&& ($chk_signAnesthesia1Id!='0' || $patient_primary_procedure_categoryID == '2') && ($chk_signNurseId!='0')
  ) {
		$formStatus = 'completed';
	}
	else
	{
	$formStatus='not completed';
	}

	if((!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO')) && $formStatus == 'completed' && $version_num > 1 && ($_REQUEST['chbx_heart_exam'] == '' || $_REQUEST['chbx_lung_exam'] == '') )
	{
		$formStatus='not completed';
	}
	if((!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO')) && $formStatus == 'completed' && $version_num > 2 && $_REQUEST['chbx_advance_directive'] == '' )
	{
		$formStatus='not completed';
	}
	if((!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO')) && $formStatus == 'completed' && $version_num > 3 && ($_REQUEST['chbx_high_cholesterol'] == '' || $_REQUEST['chbx_thyroid'] == '' || $_REQUEST['chbx_ulcer'] == '' ) )
	{
		$formStatus='not completed';
	}
	
	//START CODE TO RESET THE RECORD
	$save_date_time = date('Y-m-d H:i:s');
	$save_operator_id = $_SESSION['loginUserId'];
	if($_REQUEST['hiddResetStatusId']=='Yes') {
		$formStatus									= '';
		$save_date_time								= '';
		$save_operator_id							= '';
		$arrayRecord['signSurgeon1Id'] 				= '';
		$arrayRecord['signSurgeon1FirstName'] 		= '';
		$arrayRecord['signSurgeon1MiddleName'] 		= '';
		$arrayRecord['signSurgeon1LastName'] 		= '';
		$arrayRecord['signSurgeon1Status'] 			= '';
		$arrayRecord['signSurgeon1DateTime'] 		= '0000-00-00 00:00:00';
		$arrayRecord['signAnesthesia1Id'] 			= '';
		$arrayRecord['signAnesthesia1FirstName'] 	= '';
		$arrayRecord['signAnesthesia1MiddleName'] 	= '';
		$arrayRecord['signAnesthesia1LastName'] 	= '';
		$arrayRecord['signAnesthesia1Status'] 		= '';
		$arrayRecord['signAnesthesia1DateTime'] 	= '0000-00-00 00:00:00';
		$arrayRecord['signNurseId'] 				= '';
		$arrayRecord['signNurseFirstName'] 			= '';
		$arrayRecord['signNurseMiddleName'] 		= '';
		$arrayRecord['signNurseLastName'] 			= '';
		$arrayRecord['signNurseStatus'] 			= '';
		$arrayRecord['chart_copied'] 				= '0';
		$arrayRecord['copied_dos'] 					= '0000-00-00';
		$arrayRecord['signNurseDateTime'] 			= '0000-00-00 00:00:00';
		$arrayRecord['version_num'] 				= '0';
		$arrayRecord['resetDateTime'] 				= date('Y-m-d H:i:s');
		$arrayRecord['resetBy'] 					= $_SESSION['loginUserId'];
	}
	//END CODE TO RESET THE RECORD
	
	if(trim($_POST['date1'])) {
		list($mm,$dd,$yy) = explode("-",trim($_POST['date1']));	
		$date1Post = $yy."-".$mm."-".$dd;
	}
	$arrayRecord['date_of_h_p'] 				= $date1Post;
	$arrayRecord['form_status'] 				= $formStatus;
	$arrayRecord['cadMI'] 						= $_POST['chbx_cad_mi'];
	$arrayRecord['cadMIDesc'] 					= addslashes($_POST['cadMIDesc']);
	$arrayRecord['cvaTIA'] 						= $_POST['chbx_cva_tia'];
	$arrayRecord['cvaTIADesc'] 					= addslashes($_POST['cvaTIADesc']);
	$arrayRecord['htnCP'] 						= $_POST['chbx_htn_cp'];
	$arrayRecord['htnCPDesc'] 					= addslashes($_POST['htnCPDesc']);
	$arrayRecord['anticoagulationTherapy'] 		= $_POST['chbx_anticoagulation_therapy'];
	$arrayRecord['anticoagulationTherapyDesc'] 	= addslashes($_POST['anticoagulationTherapyDesc']);
	$arrayRecord['respiratoryAsthma'] 			= $_POST['chbx_respiratory_asthma'];
	$arrayRecord['respiratoryAsthmaDesc'] 		= addslashes($_POST['respiratoryAsthmaDesc']);
	$arrayRecord['arthritis'] 					= $_POST['chbx_arthritis'];	
	$arrayRecord['arthritisDesc'] 				= addslashes($_POST['arthritisDesc']);	
	$arrayRecord['diabetes'] 					= $_POST['chbx_diabetes'];	
	$arrayRecord['diabetesDesc'] 				= addslashes($_POST['diabetesDesc']);
	$arrayRecord['recreationalDrug']			= $_POST['chbx_recreational_drug'];	
	$arrayRecord['recreationalDrugDesc'] 		= addslashes($_POST['recreationalDrugDesc']);
	$arrayRecord['giGerd'] 						= $_POST['chbx_gi_gerd'];
	$arrayRecord['giGerdDesc'] 					= addslashes($_POST['giGerdDesc']);
	$arrayRecord['ocular'] 						= $_POST['chbx_ocular'];
	$arrayRecord['ocularDesc'] 					= addslashes($_POST['ocularDesc']);
	$arrayRecord['kidneyDisease'] 				= $_POST['chbx_kidney_disease'];
	$arrayRecord['kidneyDiseaseDesc'] 			= addslashes($_POST['kidneyDiseaseDesc']);
	$arrayRecord['hivAutoimmune'] 				= $_POST['chbx_hiv_autoimmune'];
	$arrayRecord['hivAutoimmuneDesc'] 			= addslashes($_POST['hivAutoimmuneDesc']);
	$arrayRecord['historyCancer'] 				= $_POST['chbx_history_cancer'];
	$arrayRecord['historyCancerDesc'] 			= addslashes($_POST['historyCancerDesc']);
	$arrayRecord['organTransplant'] 			= $_POST['chbx_organ_transplant'];
	$arrayRecord['organTransplantDesc'] 		= addslashes($_POST['organTransplantDesc']);
	$arrayRecord['badReaction'] 				= $_POST['chbx_bad_reaction'];
	$arrayRecord['badReactionDesc'] 			= addslashes($_POST['badReactionDesc']);
	$arrayRecord['otherHistoryPhysical'] 		= addslashes($_POST['otherHistoryPhysical']);		
	$arrayRecord['wearContactLenses'] 			= $_POST['chbx_wear_contact_lenses'];
	$arrayRecord['wearContactLensesDesc'] 		= addslashes($_POST['wearContactLensesDesc']);
	$arrayRecord['smoking'] 					= $_POST['chbx_smoking'];
	$arrayRecord['smokingDesc'] 				= addslashes($_POST['smokingDesc']);
	$arrayRecord['drinkAlcohal'] 				= $_POST['chbx_drink_alcohal'];
	$arrayRecord['drinkAlcohalDesc'] 			= addslashes($_POST['drinkAlcohalDesc']);
	$arrayRecord['haveAutomatic'] 				= $_POST['chbx_have_automatic'];
	$arrayRecord['haveAutomaticDesc'] 			= addslashes($_POST['haveAutomaticDesc']);
	$arrayRecord['medicalHistoryObtained'] 		= $_POST['chbx_medical_history_obtained'];
	$arrayRecord['medicalHistoryObtainedDesc'] 	= addslashes($_POST['medicalHistoryObtainedDesc']);
	$arrayRecord['otherNotes'] 					= addslashes($_POST['otherNotes']);
	$arrayRecord['save_date_time'] 				= $save_date_time;
	$arrayRecord['save_operator_id'] 			= $save_operator_id;
	
	
	
	
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] 			= $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] 		= $_SESSION['patient_id'];
	$arrayStatusRecord['confirmation_id'] 	= $pConfId;
	$arrayStatusRecord['form_name'] 		= 'history_physical_form';	
	$arrayStatusRecord['action_date_time'] 	= date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	
	if($history_physicial_id){		
		$objManageData->updateRecords($arrayRecord, 'history_physicial_clearance', 'history_physicial_id', $history_physicial_id);
	}else{
		$history_physicial_id = $objManageData->addRecords($arrayRecord, 'history_physicial_clearance');
	}
	
		
		//CODE START TO SET AUDIT STATUS AFTER SAVE
			unset($conditionArr);
			$conditionArr['confirmation_id']= $pConfId;
			$conditionArr['form_name'] 		= 'history_physical_form';
			$conditionArr['status'] 		= 'created';
			$chkAuditStatus = $objManageData->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);	
			if($chkAuditStatus) {
				//MAKE AUDIT STATUS MODIFIED
				$arrayStatusRecord['status']= 'modified';
			}else {
				//MAKE AUDIT STATUS CREATED
				$arrayStatusRecord['status']= 'created';
			}
			$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');												
		//CODE END TO SET AUDIT STATUS AFTER SAVE
	
	//CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 
	
	
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE

	//CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByAnes = chkAnesSignNew($_REQUEST["pConfId"]);
		$updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='".$chartSignedByAnes."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateAnesStubTblRes = imw_query($updateAnesStubTblQry) or die(imw_error());
	//END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
		
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		
	
		/*
		
		if($formStatus == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		}else if($formStatus=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		}*/
		
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

	//END CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 	

}

?>
<script src="js/jscript.js" ></script>

<script>
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

function copyPrevHpChartConfirm() {
	$('#myModalHP .modal-body>p').addClass('text-center');
	$('#myModalHP .modal-body>p').html("Previous H&P Chart is older than 30 days. Do you want to carry forward ?");
	$("#cancel_hp_yes").removeClass('hidden');
	$("#cancel_hp_no").removeClass('hidden');
	$('#myModalHP').modal('show');
}

//Applet
function get_App_Coords(objElem){
	//alert(objElem);
	var coords,appName;
	var objElemSign = document.frm_history_physicial.elem_signature;
	//appName = objElem.name;
	appName = objElem;
	coords = getCoords(appName);
	objElemSign.value = refineCoords(coords);
}
function get_App_Coords_wit(objElemwit){
	var coordswit,appNamewit;
	var objElemSignwit = document.frm_history_physicial.witnSign;
	//appNamewit = objElemwit.name;
	appNamewit = objElemwit;
	coordswit = getCoords(appNamewit);
	objElemSignwit.value= refineCoords(coordswit);
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
function getCoords(appName){		
	var coords = document.applets[appName].getSign();
	return coords;
}


function getclear_os(objElem){
	document.applets["app_signature"].clearIt();
	changeColorThis(255,0,0);
	//document.applets["app_signature"].onmouseout();
	get_App_Coords(objElem);
}
function getclear_witness(objElemwit){
	document.applets["app_witness_signature"].clearIt();
	changeColorThis(255,0,0);
	//document.applets["app_witness_signature"].onmouseout();
	get_App_Coords_wit(objElemwit)
}
function changeColorThis(r,g,b){				
	document.applets['app_signature'].setDrawColor(r,g,b);								
}
//Applet

//START CODE FOR NEW SIGNATURE APPLET

function OnSignPtHealth() {
	if(document.getElementById("SigPlusPreHlthWtSign")) {
		document.getElementById("SigPlusPreHlthWtSign").TabletState = 0;
	}
	
	document.getElementById("SigPlusPreHlthPtSign").TabletState = 1;	
	if(document.getElementById("tdObjectSigPlusPreHlthPtSign")) {document.getElementById("tdObjectSigPlusPreHlthPtSign").className="consentObjectAfterSign";}
	if(document.getElementById("tdObjectSigPlusPreHlthWtSign")) {document.getElementById("tdObjectSigPlusPreHlthWtSign").className="consentObjectBeforSign";}
}
function OnSignWtHealth() {
	if(document.getElementById("SigPlusPreHlthPtSign")) {
		document.getElementById("SigPlusPreHlthPtSign").TabletState = 0;
	}
	document.getElementById("SigPlusPreHlthWtSign").TabletState = 1;
	if(document.getElementById("tdObjectSigPlusPreHlthWtSign")) {document.getElementById("tdObjectSigPlusPreHlthWtSign").className="consentObjectAfterSign";}
	if(document.getElementById("tdObjectSigPlusPreHlthPtSign")) {document.getElementById("tdObjectSigPlusPreHlthPtSign").className="consentObjectBeforSign";}
	
}

function OnClearPtHealth() {
   document.getElementById("SigPlusPreHlthPtSign").ClearTablet();
}
function OnClearWtHealth() {
   document.getElementById("SigPlusPreHlthWtSign").ClearTablet();
}

function SetSigPreHlthPtSign() {
	if(document.getElementById("SigPlusPreHlthPtSign")) {
		if(document.getElementById("SigPlusPreHlthPtSign").NumberOfTabletPoints==0
		  ){
		  	//DO NOTHING
		}
		else{
			document.getElementById("SigPlusPreHlthPtSign").SigCompressionMode=1;
			document.getElementById("SigDataPt").value=document.getElementById("SigPlusPreHlthPtSign").SigString;
		}
	}
	if(document.getElementById("SigPlusPreHlthWtSign")) {
		if(document.getElementById("SigPlusPreHlthWtSign").NumberOfTabletPoints==0
		   ){
		
			//DO NOTHING
		}
		else{
			document.getElementById("SigPlusPreHlthWtSign").SigCompressionMode=1;
			document.getElementById("SigDataWt").value=document.getElementById("SigPlusPreHlthWtSign").SigString;
		}
	}
	flag=false;
}

function LoadSigPlus() {
	/*
	if(document.getElementById("SigDataPtLoadValue").value==""){
		//document.getElementById("SigPlusPreHlthPtSign").ClearTablet();
		//alert("Please re-enter your first name to display signature")
	}else{
		 document.getElementById("SigPlusPreHlthPtSign").TabletState = 0;
		 document.getElementById("SigPlusPreHlthPtSign").JustifyX=10;
		 document.getElementById("SigPlusPreHlthPtSign").JustifyY=10;
		 //document.getElementById("SigPlusPreHlthPtSign").EncryptionMode=1;
		 //document.getElementById("SigPlusPreHlthPtSign").SigCompressionMode=2;
		 //document.getElementById("SigPlusPreHlthPtSign").DisplayPenWidth=10;
		 //document.getElementById("SigPlusPreHlthPtSign").JustifyMode=5;
		 //document.getElementById("SigPlusPreHlthPtSign").SigString=document.getElementById("SigDataPtLoadValue").value;
	}
	*/
	
}
//END CODE FOR NEW SIGNATURE APPLET
function unCheckSubItems(obj1, obj2, obj3){
	if(document.getElementById(obj1).checked == true){
		document.getElementById(obj2).checked = false;
		document.getElementById(obj3).checked = false;
	}
}
function checkSubItems(obj1, obj2, obj3){
	if(document.getElementById(obj1).checked == false){
		document.getElementById(obj2).checked = false;
		document.getElementById(obj3).checked = false;
	}
}
function showABCOptions(){
	var displayStatus = document.getElementById('chbx_prop_anes_yes').checked;
	if(displayStatus == true){
		document.getElementById('trProposedAnesthetic').style.display = 'inline-block';
	}else{
		document.getElementById('trProposedAnesthetic').style.display = 'none';
		document.getElementById('proposedAnestheticGA').checked = false;
		document.getElementById('proposedAnestheticMAC').checked = false;
		document.getElementById('proposedAnestheticLOC').checked = false;
	}
}
function showHivText(){
	var displayStatus = document.getElementById('chbx_hiv_auto_yes').checked;
	if(displayStatus == true){
		document.getElementById('hivTr').style.display = 'inline-block';
	}else{
		document.getElementById('hivTr').style.display = 'none';
	}
}
function showHidebrestCancerTr(){
	var displayStatus = document.getElementById('chbx_hist_can_yes').checked;	
	if(displayStatus == true){
		document.getElementById('brestCancerTr').style.display = 'inline-block';
	}else{
		document.getElementById('brestCancerTr').style.display = 'none';
	}
}

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
			var assignedSurgeonId = '<?php echo $hpAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $hpAssignedSurgeonName;?>';
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
				//document.write(xmlHttp.responseText);
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
				top.frames[0].setPNotesHeight();
			}
	}
	
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

	function temp()
	{
		//alert("A");
		var div = document.getElementById("iframe_health_quest");
		var win = window.open("about:blank");
		win.document.write("<body><textarea>"+div.innerHTML+"</textarea></body>");
		return true;
	}
	function newWindow1(q){
	
		mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
		mywindow.location.href = 'mycal1.php?md='+q;
		if(mywindow.opener == null)
			mywindow.opener = self;
	}
</script>

<script type="text/javascript">
	$(function () {
			$('.datepickerTxt').datetimepicker({
				
					format: 'MM-DD-YYYY'
			
			});
	});
</script>
<div id="post" style="display:none; position:absolute;"></div>

<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<?php 
// GETTING CONFIRMATION DETAILS
	
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$hpDOS = $detailConfirmation->dos;	
	//$allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;	
// GETTING CONFIRMATION DETAILS

	$table = 'allergies';
	//include("common/pre_defined_popup.php");
	
	//START CODE TO COPY H&P CHART AFTER CONFIRMATION - IF CHART IS OLDER THAN 30 DAYS
	if($_REQUEST["hpCopy"] == "yes") {
		include_once('history_physicial_clearance_copy.php');// Start Copying H&P Fields From Previous Appointment
		echo "<script>top.changeChkMarkImage('".$innerKey."','not completed');</script>";
	}
	//END CODE TO COPY H&P CHART AFTER CONFIRMATION - IF CHART IS OLDER THAN 30 DAYS
	
	if($history_physicial_id){
		$getPreOpQuesDetails = $objManageData->getExtractRecord("history_physicial_clearance", "history_physicial_id", $history_physicial_id, " *, if(date_format(date_of_h_p ,'%m-%d-%Y')='00-00-0000','',date_format(date_of_h_p ,'%m-%d-%Y')) as date_of_h_p_format, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat ");
	}else if($pConfId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord("history_physicial_clearance", "confirmation_id", $pConfId, " *, if(date_format(date_of_h_p ,'%m-%d-%Y')='00-00-0000','',date_format(date_of_h_p ,'%m-%d-%Y')) as date_of_h_p_format, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat ");	
	}
	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
	}
	
	// Checking For Chart Version  
	if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
	else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	4; }
	
	$blurInput = "onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
	$keyPressInput = "onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\"";

	
	//START CODE TO CONFIRM TO COPY H&P CHART IF CHART IS OLDER THAN 30 DAYS
	if(trim($_REQUEST["hpCopy"]) == "") {
		if($form_status == '' && $chart_copied == '0') {
			$prevHpQry	=	"SELECT hp.*,pc.dos FROM history_physicial_clearance hp
											INNER JOIN patientconfirmation pc 
											ON (pc.patientConfirmationId = hp.confirmation_id 
											AND pc.patientId = '".$_REQUEST['patient_id']."' 
											AND pc.dos  < '".$hpDOS."')
											WHERE hp.confirmation_id !='0' 
											And hp.form_status != ''
											And hp.chart_copied = '0' 
											ORDER BY pc.dos Desc, pc.patientConfirmationId Desc Limit 0,1";
											
			$prevHpRes	=	imw_query($prevHpQry) or die($prevHpQry.'---'.imw_error());
			$prevHpCnt	=	imw_num_rows($prevHpRes);
			if($prevHpCnt > 0) {
				$prevHpRow	=	imw_fetch_object($prevHpRes);
				$validDos	=	$objManageData->getDateSubtract($hpDOS,30);
				$isConfirm		=	($validDos > $prevHpRow->dos )	?	true	:	false;
			}
		}
	}
	//END CODE TO CONFIRM TO COPY H&P CHART IF CHART IS OLDER THAN 30 DAYS
	if( $form_status == 'completed' || $form_status == 'not completed') {
		$getAddQuestions = $objManageData->getAllRecords('history_physical_ques','', array('confirmation_id = '=>$pConfId), array(),array('ques + 0'=>'ASC'));
	}
	else {
		$getAddQuestions = $objManageData->getAllRecords('predefine_history_physical',array('id','name as ques'), array('deleted = '=>'0'), array(),array('name + 0'=>'ASC'));
	}
	?>
    
    <form name="frm_history_physicial" id="frm_history_physicial" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="history_physicial_clearance.php?saveRecord=true&amp;SaveForm_alert=true">
        <input type="hidden" name="divId" id="divId">
        <input type="hidden" name="counter" id="counter">
        <input type="hidden" name="secondaryValues" id="secondaryValues">
        <input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
        <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">			
        <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
        <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
        <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
        <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $_REQUEST['patient_id']; ?>">
        <input type="hidden" name="history_physicial_id" id="history_physicial_id" value="<?php echo $history_physicial_id; ?>">
        <input type="hidden" name="getText" id="getText">
        <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
        <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
        <input type="hidden" name="frmAction" id="frmAction" value="history_physicial_clearance.php">
        <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
        <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
        <input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
	    	<input type="hidden" id="vitalSignGridHolder" />
	    	<input type="hidden" id="ques_count" name="ques_count" value="<?php echo count($getAddQuestions); ?>" />
      
      <div class="scheduler_table_Complete" id="" style="">
      		<?php
					$epost_table_name = "history_physicial_clearance";
					include("./epost_list.php");
			?>
                
            <!--
            <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_green">
                <span class="bg_span_green">
                    H & P Clearance
                </span>
				
             </div>	
             -->
             <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
				<?php 
                    $bgCol = '#779169';
                    $borderCol = '#779169';
                    include('saveDivPopUp.php'); 
                ?>
            </div>	
             <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                     <div class="panel panel-default new_panel bg_panel_green">
                       <div class="panel-heading">
                            <h3 class="panel-title rob"> History And Physical </h3>
                       </div>
                       <div class="panel-body " id="p_check_in">
                            <div class="row">
                                <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                
                                                </label>
                                            </Div>
                                            <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>    
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <label class="li_check"> Yes </label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                      <label class="li_check"> No </label>
                                                    </div>
                                                                                       
                                                </div> 
                                            </Div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    CAD/MIN(W/ WO Stent OR CABG)/PVD)
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($cadMI) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_cad_mi_yes','chbx_cad_mi');disp_new(document.frm_history_physicial.chbx_cad_mi_yes,'chbx_cad_mi_tb_id');changeChbxColor('chbx_cad_mi');" <?php if($cadMI=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_cad_mi" id="chbx_cad_mi_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($cadMI) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_cad_mi_no','chbx_cad_mi');disp_none_new(document.frm_history_physicial.chbx_cad_mi_no,'chbx_cad_mi_tb_id');changeChbxColor('chbx_cad_mi');" <?php if(($getPreOpQuesDetails) && ($cadMI=='No')) echo "CHECKED"; ?> value="No" name="chbx_cad_mi" id="chbx_cad_mi_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($cadMI=='Yes') { ?> in <?php }?>" id="chbx_cad_mi_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="cadMIDesc" name="cadMIDesc" ><?php echo stripslashes($cadMIDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    CVA/TIA/ Epilepsy, Neurological
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($cvaTIA) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_cva_tia_yes','chbx_cva_tia');disp_new(document.frm_history_physicial.chbx_cva_tia_yes,'chbx_cva_tia_tb_id');changeChbxColor('chbx_cva_tia');" <?php if($cvaTIA=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_cva_tia" id="chbx_cva_tia_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($cvaTIA) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_cva_tia_no','chbx_cva_tia');disp_none_new(document.frm_history_physicial.chbx_cva_tia_no,'chbx_cva_tia_tb_id');changeChbxColor('chbx_cva_tia');" <?php if(($getPreOpQuesDetails) && ($cvaTIA=='No')) echo "CHECKED"; ?> value="No" name="chbx_cva_tia" id="chbx_cva_tia_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($cvaTIA=='Yes') { ?> in <?php }?>" id="chbx_cva_tia_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="cvaTIADesc" name="cvaTIADesc" ><?php echo stripslashes($cvaTIADesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    HTN/ +/- CP/SOB on Exertion
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($htnCP) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_htn_cp_yes','chbx_htn_cp');disp_new(document.frm_history_physicial.chbx_htn_cp_yes,'chbx_htn_cp_tb_id');changeChbxColor('chbx_htn_cp');" <?php if($htnCP=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_htn_cp" id="chbx_htn_cp_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($htnCP) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_htn_cp_no','chbx_htn_cp');disp_none_new(document.frm_history_physicial.chbx_htn_cp_no,'chbx_htn_cp_tb_id');changeChbxColor('chbx_htn_cp');" <?php if(($getPreOpQuesDetails) && ($htnCP=='No')) echo "CHECKED"; ?> value="No" name="chbx_htn_cp" id="chbx_htn_cp_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($htnCP=='Yes') { ?> in <?php }?>" id="chbx_htn_cp_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="htnCPDesc" name="htnCPDesc" ><?php echo stripslashes($htnCPDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                   Anticoagulation therapy (i.e. Blood Thinners)
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($anticoagulationTherapy) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_anticoagulation_therapy_yes','chbx_anticoagulation_therapy');disp_new(document.frm_history_physicial.chbx_anticoagulation_therapy_yes,'chbx_anticoagulation_therapy_tb_id');changeChbxColor('chbx_anticoagulation_therapy');" <?php if($anticoagulationTherapy=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_anticoagulation_therapy" id="chbx_anticoagulation_therapy_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($anticoagulationTherapy) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_anticoagulation_therapy_no','chbx_anticoagulation_therapy');disp_none_new(document.frm_history_physicial.chbx_anticoagulation_therapy_no,'chbx_anticoagulation_therapy_tb_id');changeChbxColor('chbx_anticoagulation_therapy');" <?php if(($getPreOpQuesDetails) && ($anticoagulationTherapy=='No')) echo "CHECKED"; ?> value="No" name="chbx_anticoagulation_therapy" id="chbx_anticoagulation_therapy_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($anticoagulationTherapy=='Yes') { ?> in <?php }?>" id="chbx_anticoagulation_therapy_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="anticoagulationTherapyDesc" name="anticoagulationTherapyDesc" ><?php echo stripslashes($anticoagulationTherapyDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Respiratory - Asthma / COPD / Sleep Apnea
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($respiratoryAsthma) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_respiratory_asthma_yes','chbx_respiratory_asthma');disp_new(document.frm_history_physicial.chbx_respiratory_asthma_yes,'chbx_respiratory_asthma_tb_id');changeChbxColor('chbx_respiratory_asthma');" <?php if($respiratoryAsthma=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_respiratory_asthma" id="chbx_respiratory_asthma_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($respiratoryAsthma) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_respiratory_asthma_no','chbx_respiratory_asthma');disp_none_new(document.frm_history_physicial.chbx_respiratory_asthma_no,'chbx_respiratory_asthma_tb_id');changeChbxColor('chbx_respiratory_asthma');" <?php if(($getPreOpQuesDetails) && ($respiratoryAsthma=='No')) echo "CHECKED"; ?> value="No" name="chbx_respiratory_asthma" id="chbx_respiratory_asthma_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($respiratoryAsthma=='Yes') { ?> in <?php }?>" id="chbx_respiratory_asthma_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="respiratoryAsthmaDesc" name="respiratoryAsthmaDesc" ><?php echo stripslashes($respiratoryAsthmaDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                   Arthritis
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($arthritis) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_arthritis_yes','chbx_arthritis');disp_new(document.frm_history_physicial.chbx_arthritis_yes,'chbx_arthritis_tb_id');changeChbxColor('chbx_arthritis');" <?php if($arthritis=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_arthritis" id="chbx_arthritis_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($arthritis) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_arthritis_no','chbx_arthritis');disp_none_new(document.frm_history_physicial.chbx_arthritis_no,'chbx_arthritis_tb_id');changeChbxColor('chbx_arthritis');" <?php if(($getPreOpQuesDetails) && ($arthritis=='No')) echo "CHECKED"; ?> value="No" name="chbx_arthritis" id="chbx_arthritis_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($arthritis=='Yes') { ?> in <?php }?>" id="chbx_arthritis_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="arthritisDesc" name="arthritisDesc" ><?php echo stripslashes($arthritisDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Diabetes
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($diabetes) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_diabetes_yes','chbx_diabetes');disp_new(document.frm_history_physicial.chbx_diabetes_yes,'chbx_diabetes_tb_id');changeChbxColor('chbx_diabetes');" <?php if($diabetes=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_diabetes" id="chbx_diabetes_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($diabetes) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_diabetes_no','chbx_diabetes');disp_none_new(document.frm_history_physicial.chbx_diabetes_no,'chbx_diabetes_tb_id');changeChbxColor('chbx_diabetes');" <?php if(($getPreOpQuesDetails) && ($diabetes=='No')) echo "CHECKED"; ?> value="No" name="chbx_diabetes" id="chbx_diabetes_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($diabetes=='Yes') { ?> in <?php }?>" id="chbx_diabetes_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="diabetesDesc" name="diabetesDesc" ><?php echo stripslashes($diabetesDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Recreational Drug Use
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($recreationalDrug) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_recreational_drug_yes','chbx_recreational_drug');disp_new(document.frm_history_physicial.chbx_recreational_drug_yes,'chbx_recreational_drug_tb_id');changeChbxColor('chbx_recreational_drug');" <?php if($recreationalDrug=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_recreational_drug" id="chbx_recreational_drug_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($recreationalDrug) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_recreational_drug_no','chbx_recreational_drug');disp_none_new(document.frm_history_physicial.chbx_recreational_drug_no,'chbx_recreational_drug_tb_id');changeChbxColor('chbx_recreational_drug');" <?php if(($getPreOpQuesDetails) && ($recreationalDrug=='No')) echo "CHECKED"; ?> value="No" name="chbx_recreational_drug" id="chbx_recreational_drug_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($recreationalDrug=='Yes') { ?> in <?php }?>" id="chbx_recreational_drug_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="recreationalDrugDesc" name="recreationalDrugDesc" ><?php echo stripslashes($recreationalDrugDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    GI - GERD / PUD / Liver Disease / Hepatitis
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($giGerd) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_gi_gerd_yes','chbx_gi_gerd');disp_new(document.frm_history_physicial.chbx_gi_gerd_yes,'chbx_gi_gerd_tb_id');changeChbxColor('chbx_gi_gerd');" <?php if($giGerd=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_gi_gerd" id="chbx_gi_gerd_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($giGerd) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_gi_gerd_no','chbx_gi_gerd');disp_none_new(document.frm_history_physicial.chbx_gi_gerd_no,'chbx_gi_gerd_tb_id');changeChbxColor('chbx_gi_gerd');" <?php if(($getPreOpQuesDetails) && ($giGerd=='No')) echo "CHECKED"; ?> value="No" name="chbx_gi_gerd" id="chbx_gi_gerd_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($giGerd=='Yes') { ?> in <?php }?>" id="chbx_gi_gerd_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="giGerdDesc" name="giGerdDesc" ><?php echo stripslashes($giGerdDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Ocular
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($ocular) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ocular_yes','chbx_ocular');disp_new(document.frm_history_physicial.chbx_ocular_yes,'chbx_ocular_tb_id');changeChbxColor('chbx_ocular');" <?php if($ocular=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ocular" id="chbx_ocular_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($ocular) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ocular_no','chbx_ocular');disp_none_new(document.frm_history_physicial.chbx_ocular_no,'chbx_ocular_tb_id');changeChbxColor('chbx_ocular');" <?php if(($getPreOpQuesDetails) && ($ocular=='No')) echo "CHECKED"; ?> value="No" name="chbx_ocular" id="chbx_ocular_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($ocular=='Yes') { ?> in <?php }?>" id="chbx_ocular_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="ocularDesc" name="ocularDesc" ><?php echo stripslashes($ocularDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Kidney Disease, Dialysis, G-U
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($kidneyDisease) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_kidney_disease_yes','chbx_kidney_disease');disp_new(document.frm_history_physicial.chbx_kidney_disease_yes,'chbx_kidney_disease_tb_id');changeChbxColor('chbx_kidney_disease');" <?php if($kidneyDisease=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_kidney_disease" id="chbx_kidney_disease_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($kidneyDisease) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_kidney_disease_no','chbx_kidney_disease');disp_none_new(document.frm_history_physicial.chbx_kidney_disease_no,'chbx_kidney_disease_tb_id');changeChbxColor('chbx_kidney_disease');" <?php if(($getPreOpQuesDetails) && ($kidneyDisease=='No')) echo "CHECKED"; ?> value="No" name="chbx_kidney_disease" id="chbx_kidney_disease_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($kidneyDisease=='Yes') { ?> in <?php }?>" id="chbx_kidney_disease_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="kidneyDiseaseDesc" name="kidneyDiseaseDesc" ><?php echo stripslashes($kidneyDiseaseDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    HIV, Autoimmune Diseases, Contagious Diseases
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($hivAutoimmune) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hiv_autoimmune_yes','chbx_hiv_autoimmune');disp_new(document.frm_history_physicial.chbx_hiv_autoimmune_yes,'chbx_hiv_autoimmune_tb_id');changeChbxColor('chbx_hiv_autoimmune');" <?php if($hivAutoimmune=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hiv_autoimmune" id="chbx_hiv_autoimmune_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($hivAutoimmune) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hiv_autoimmune_no','chbx_hiv_autoimmune');disp_none_new(document.frm_history_physicial.chbx_hiv_autoimmune_no,'chbx_hiv_autoimmune_tb_id');changeChbxColor('chbx_hiv_autoimmune');" <?php if(($getPreOpQuesDetails) && ($hivAutoimmune=='No')) echo "CHECKED"; ?> value="No" name="chbx_hiv_autoimmune" id="chbx_hiv_autoimmune_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($hivAutoimmune=='Yes') { ?> in <?php }?>" id="chbx_hiv_autoimmune_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="hivAutoimmuneDesc" name="hivAutoimmuneDesc" ><?php echo stripslashes($hivAutoimmuneDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    History of Cancer
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($historyCancer) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_history_cancer_yes','chbx_history_cancer');disp_new(document.frm_history_physicial.chbx_history_cancer_yes,'chbx_history_cancer_tb_id');changeChbxColor('chbx_history_cancer');" <?php if($historyCancer=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_history_cancer" id="chbx_history_cancer_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($historyCancer) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_history_cancer_no','chbx_history_cancer');disp_none_new(document.frm_history_physicial.chbx_history_cancer_no,'chbx_history_cancer_tb_id');changeChbxColor('chbx_history_cancer');" <?php if(($getPreOpQuesDetails) && ($historyCancer=='No')) echo "CHECKED"; ?> value="No" name="chbx_history_cancer" id="chbx_history_cancer_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($historyCancer=='Yes') { ?> in <?php }?>" id="chbx_history_cancer_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="historyCancerDesc" name="historyCancerDesc" ><?php echo stripslashes($historyCancerDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Organ Transplant
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($organTransplant) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_organ_transplant_yes','chbx_organ_transplant');disp_new(document.frm_history_physicial.chbx_organ_transplant_yes,'chbx_organ_transplant_tb_id');changeChbxColor('chbx_organ_transplant');" <?php if($organTransplant=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_organ_transplant" id="chbx_organ_transplant_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($organTransplant) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_organ_transplant_no','chbx_organ_transplant');disp_none_new(document.frm_history_physicial.chbx_organ_transplant_no,'chbx_organ_transplant_tb_id');changeChbxColor('chbx_organ_transplant');" <?php if(($getPreOpQuesDetails) && ($organTransplant=='No')) echo "CHECKED"; ?> value="No" name="chbx_organ_transplant" id="chbx_organ_transplant_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($organTransplant=='Yes') { ?> in <?php }?>" id="chbx_organ_transplant_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="organTransplantDesc" name="organTransplantDesc" ><?php echo stripslashes($organTransplantDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                      
                                        
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    A Bad Reaction to Local or General Anesthesia
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($badReaction) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_bad_reaction_yes','chbx_bad_reaction');disp_new(document.frm_history_physicial.chbx_bad_reaction_yes,'chbx_bad_reaction_tb_id');changeChbxColor('chbx_bad_reaction');" <?php if($badReaction=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_bad_reaction" id="chbx_bad_reaction_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($badReaction) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_bad_reaction_no','chbx_bad_reaction');disp_none_new(document.frm_history_physicial.chbx_bad_reaction_no,'chbx_bad_reaction_tb_id');changeChbxColor('chbx_bad_reaction');" <?php if(($getPreOpQuesDetails) && ($badReaction=='No')) echo "CHECKED"; ?> value="No" name="chbx_bad_reaction" id="chbx_bad_reaction_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($badReaction=='Yes') { ?> in <?php }?>" id="chbx_bad_reaction_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="badReactionDesc" name="badReactionDesc" ><?php echo stripslashes($badReactionDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <?php if( $version_num > 3) { ?>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    High Cholesterol
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($highCholesterol) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_high_cholesterol_yes','chbx_high_cholesterol');disp_new(document.frm_history_physicial.chbx_high_cholesterol_yes,'chbx_high_cholesterol_tb_id');changeChbxColor('chbx_high_cholesterol');" <?php if($highCholesterol=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_high_cholesterol" id="chbx_high_cholesterol_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($highCholesterol) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_high_cholesterol_no','chbx_high_cholesterol');disp_none_new(document.frm_history_physicial.chbx_high_cholesterol_no,'chbx_high_cholesterol_tb_id');changeChbxColor('chbx_high_cholesterol');" <?php if(($getPreOpQuesDetails) && ($highCholesterol=='No')) echo "CHECKED"; ?> value="No" name="chbx_high_cholesterol" id="chbx_high_cholesterol_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($highCholesterol=='Yes') { ?> in <?php }?>" id="chbx_high_cholesterol_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="highCholesterolDesc" name="highCholesterolDesc" ><?php echo stripslashes($highCholesterolDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Thyroid
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($thyroid) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_thyroid_yes','chbx_thyroid');disp_new(document.frm_history_physicial.chbx_thyroid_yes,'chbx_thyroid_tb_id');changeChbxColor('chbx_thyroid');" <?php if($thyroid=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_thyroid" id="chbx_thyroid_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($thyroid) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_thyroid_no','chbx_thyroid');disp_none_new(document.frm_history_physicial.chbx_thyroid_no,'chbx_thyroid_tb_id');changeChbxColor('chbx_thyroid');" <?php if(($getPreOpQuesDetails) && ($thyroid=='No')) echo "CHECKED"; ?> value="No" name="chbx_thyroid" id="chbx_thyroid_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($thyroid=='Yes') { ?> in <?php }?>" id="chbx_thyroid_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="thyroidDesc" name="thyroidDesc" ><?php echo stripslashes($thyroidDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                      
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Ulcers
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($ulcer) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ulcer_yes','chbx_ulcer');disp_new(document.frm_history_physicial.chbx_ulcer_yes,'chbx_ulcer_tb_id');changeChbxColor('chbx_ulcer');" <?php if($ulcer=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ulcer" id="chbx_ulcer_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($ulcer) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ulcer_no','chbx_ulcer');disp_none_new(document.frm_history_physicial.chbx_ulcer_no,'chbx_ulcer_tb_id');changeChbxColor('chbx_ulcer');" <?php if(($getPreOpQuesDetails) && ($ulcer=='No')) echo "CHECKED"; ?> value="No" name="chbx_ulcer" id="chbx_ulcer_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($ulcer=='Yes') { ?> in <?php }?>" id="chbx_ulcer_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="ulcerDesc" name="ulcerDesc" ><?php echo stripslashes($ulcerDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <?php } ?>   
                                     <div id="" class="inner_safety_wrap">
                                        <div class="well">
                                        	<div class="row">
                                          	<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                            	<label class="date_r">Other</label>
                                           	</div>
                                            <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                            	<textarea class="form-control" style="resize:none;" id="otherHistoryPhysical" name="otherHistoryPhysical"><?php echo stripslashes($otherHistoryPhysical); ?></textarea>
                                           	</div>
                                         	</div>
                                       	</div> 
                                    	</div>
                                  	
																			<?php if( $version_num > 1) { ?>
                                      <!-- Heart Exam done with stethoscope - Normal -->	
                                      <div class="inner_safety_wrap">
                                      	<div class="row">
                                        	<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                          	<label class="date_r">
                                            	Heart Exam done with stethoscope - Normal
                                           	</label>
                                         	</div>
                                          <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                          	<div class="">
                                            	
                                              <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	&nbsp;</div>
                                              
                                              <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                              	<span class="colorChkBx" style=" <?php if($heartExam) { echo $whiteBckGroundColor;}?>" >
                                                	<input type="checkbox" onClick="javascript:checkSingle('chbx_heart_exam_yes','chbx_heart_exam');disp_none_new(document.frm_history_physicial.chbx_heart_exam_yes,'chbx_heart_exam_tb_id');changeChbxColor('chbx_heart_exam');" <?php if($heartExam=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_heart_exam" id="chbx_heart_exam_yes" >
                                               	</span>
                                             	</div>
                                              
                                              <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                              	<span class="colorChkBx" style=" <?php if($heartExam) { echo $whiteBckGroundColor;}?>" >
                                                	<input type="checkbox" onClick="javascript:checkSingle('chbx_heart_exam_no','chbx_heart_exam');disp_new(document.frm_history_physicial.chbx_heart_exam_no,'chbx_heart_exam_tb_id');changeChbxColor('chbx_heart_exam');" <?php if(($getPreOpQuesDetails) && ($heartExam=='No')) echo "CHECKED"; ?> value="No" name="chbx_heart_exam" id="chbx_heart_exam_no">
                                                </span>
                                             	</div>
                                           	</div>
                                         	</div>
                                       	</div>
                                     	</div>
                                      
                                    	<div class="inner_safety_wrap collapse <?php if($heartExam=='No') { ?> in <?php }?>" id="chbx_heart_exam_tb_id" style="height: auto;">
                                      	<Div class="well">
                                        	<div class="row">
                                          	<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                            	<label class="date_r">Describe</label>
                                           	</div>
                                            <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                            	<textarea style="resize:none;" class="form-control" id="heartExamDesc" name="heartExamDesc" ><?php echo stripslashes($heartExamDesc); ?></textarea>
                                          	</div>
                                         	</div>
                                       	</Div> 
                                    	</div>
                                      
                                      
                                      <!-- Lung Exam done with stethoscope - Normal -->	
                                      <div class="inner_safety_wrap">
                                      	<div class="row">
                                        	<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                          	<label class="date_r">
                                            	Lung Exam done with stethoscope - Normal
                                           	</label>
                                         	</div>
                                          <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                          	<div class="">
                                            	
                                              <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	&nbsp;</div>
                                              
                                              <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                              	<span class="colorChkBx" style=" <?php if($lungExam) { echo $whiteBckGroundColor;}?>" >
                                                	<input type="checkbox" onClick="javascript:checkSingle('chbx_lung_exam_yes','chbx_lung_exam');disp_none_new(document.frm_history_physicial.chbx_lung_exam_yes,'chbx_lung_exam_tb_id');changeChbxColor('chbx_lung_exam');" <?php if($lungExam=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_lung_exam" id="chbx_lung_exam_yes" >
                                               	</span>
                                             	</div>
                                              
                                              <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                              	<span class="colorChkBx" style=" <?php if($lungExam) { echo $whiteBckGroundColor;}?>" >
                                                	<input type="checkbox" onClick="javascript:checkSingle('chbx_lung_exam_no','chbx_lung_exam');disp_new(document.frm_history_physicial.chbx_lung_exam_no,'chbx_lung_exam_tb_id');changeChbxColor('chbx_lung_exam');" <?php if(($getPreOpQuesDetails) && ($lungExam=='No')) echo "CHECKED"; ?> value="No" name="chbx_lung_exam" id="chbx_lung_exam_no">
                                                </span>
                                             	</div>
                                           	</div>
                                         	</div>
                                       	</div>
                                     	</div>
                                      
                                    	<div class="inner_safety_wrap collapse <?php if($lungExam=='No') { ?> in <?php }?>" id="chbx_lung_exam_tb_id" style="height: auto;">
                                      	<Div class="well">
                                        	<div class="row">
                                          	<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                            	<label class="date_r">Describe</label>
                                           	</div>
                                            <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                            	<textarea style="resize:none;" class="form-control" id="lungExamDesc" name="lungExamDesc" ><?php echo stripslashes($lungExamDesc); ?></textarea>
                                          	</div>
                                         	</div>
                                       	</Div> 
                                    	</div>
                                     	
                                      <?php } 
									 if( $version_num > 2) { ?>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Discussed Advanced Directives and Patient Rights and Responsibilities
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($discussedAdvancedDirective) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_advance_directive_yes','chbx_advance_directive');disp_new(document.frm_history_physicial.chbx_advance_directive_yes,'chbx_advance_directive_tb_id');changeChbxColor('chbx_advance_directive');" <?php if($discussedAdvancedDirective=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_advance_directive" id="chbx_advance_directive_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($discussedAdvancedDirective) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_advance_directive_no','chbx_advance_directive');disp_none_new(document.frm_history_physicial.chbx_advance_directive_no,'chbx_advance_directive_tb_id');changeChbxColor('chbx_advance_directive');" <?php if(($getPreOpQuesDetails) && ($discussedAdvancedDirective=='No')) echo "CHECKED"; ?> value="No" name="chbx_advance_directive" id="chbx_advance_directive_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                    <?php 
									} ?> 
                                     	
                                    <!-- Start Print Dynamic Questions -->
                                    <?php 	
                                  		if( is_array($getAddQuestions) && count($getAddQuestions) > 0 ) {
																				$counter = -1;
																				foreach($getAddQuestions as $qArr) { $counter++;
																					$ques = stripslashes($qArr->ques);
																					$ques_fld_name = 'ques_'.$counter;
																					$fld_value = $qArr->ques_status;
																					$txt_value = $qArr->ques_desc;
																					$fld_name = 'ques_chk_'.$counter;
																					$fldy_name = 'ques_yes_'.$counter;
																					$fldn_name = 'ques_no_'.$counter;
																																							
																					$txt_row_id = 'ques_desc_row_'.$counter;	
																					$txt_fld_id	= 'ques_desc_'.$counter;															
																		?>
																				<div class="inner_safety_wrap">
                                       		<div class="row">
                                          	<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                            	<label class="date_r">
                                            	<input type="hidden" name="<?php echo $ques_fld_name;?>" id="ques_<?php echo $ques_fld_name;?>" value="<?php echo $ques;?>" /><?php echo $ques; ?></label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                           	<div class="">
                                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">&nbsp;</div>
                                             	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                             		<span>
                                               		<input type="checkbox" onClick="javascript:checkSingle('<?php echo $fldy_name;?>','<?php echo $fld_name;?>');disp_new(document.frm_history_physicial.<?php echo $fld_name;?>,'<?php echo $txt_row_id;?>');" <?php if( ($getPreOpQuesDetails) && $fld_value=='Yes') echo "CHECKED"; ?> value="Yes" name="<?php echo $fld_name;?>" id="<?php echo $fldy_name;?>" >
                                              	</span>
                                             	</div>
                                                   
                                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                              	<span>
                                               		<input type="checkbox" onClick="javascript:checkSingle('<?php echo $fldn_name;?>','<?php echo $fld_name;?>');disp_none_new(document.frm_history_physicial.<?php echo $fld_name;?>,'<?php echo $txt_row_id;?>');" <?php if(($getPreOpQuesDetails) && ($fld_value=='No')) echo "CHECKED"; ?> value="No" name="<?php echo $fld_name;?>" id="<?php echo $fldn_name;?>">
                                              	</span>
                                            	</div>
                                           	</div> 
                                           	</div>
                                       		</div>	 
                                     		</div>
                                     		
                                     		<div class="inner_safety_wrap collapse <?php if($fld_value=='Yes') { ?> in <?php }?>" id="<?php echo $txt_row_id;?>" style="height: auto;">
                                        	<div class="well">
                                          	<div class="row">
                                            	<div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                              	<label class="date_r">Describe</label>
                                            	</div>
                                             	<div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                              	<textarea style="resize:none;" class="form-control" id="<?php echo $txt_fld_id;?>" name="<?php echo $txt_fld_id;?>" ><?php echo stripslashes($txt_value); ?></textarea>
                                                </div>
                                            </div>
                                        	</div> 
                                      	</div>
																					
																								
																		<?php 												
																				}
																				
																			}
																	
                                    ?>	
                                    <!-- End Print Dynamic Questions -->
                                      	
                                 	</Div>
                           </div>
                      </div>          
                     </div> 
                </div>
                <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                    <Div class="panel panel-default new_panel bg_panel_green">
                        <Div class="panel-heading">
                            <a id="h_and_p_allergies" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="top" onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', {table_name:'hlthQstSpreadTableId'}, parseInt(findPos_X('h_and_p_allergies')+8), parseInt(findPos_Y('h_and_p_allergies')-136)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='iframe_health_quest';"> <span class="fa fa-caret-right"></span>  Allergies   </a>
                        </Div>
                        <div class="panel-body">
                            <div class="inner_safety_wrap">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                       <div class="scheduler_table_Complete ">
                                           <div class="my_table_Checkall table_slider_head">
                                                    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                        <thead class="cf">
                                                            <tr>
                                                                <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Name</th>
                                                                <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Reaction </th>
                                                            </tr>
                                                        </thead>
                                                     </table>
                                           </div>
                                           <div id="iframe_history_physical" class="table_slider">
												<?php  
                                                    $allgNameWidth=215;
                                                    $allgReactionWidth=215;
                                                    include("health_quest_spreadsheet.php");
                                                ?>
                                            </div>
                                          </div>

                                    </div>
                                   
                                </div>	 
                             </div>
                        </div>
                    </Div>
                    
                    <Div class="clearfix margin_adjustment_only"></Div>
                    
                    <Div class="panel panel-default new_panel bg_panel_green">
                        <Div class="panel-heading">
                            <a id="h_and_p_medications" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="top" onClick="return showPreDefineMedFn('medication_name', 'medication_detail', '10', parseInt(findPos_X('h_and_p_medications')), parseInt(findPos_Y('h_and_p_medications')-185)),document.getElementById('selected_frame_name_id').value='iframe_history_physical_medication';"> <span class="fa fa-caret-right"></span>  Medications    </a>
                        </Div>
                        <div class="panel-body">
                            <div class="inner_safety_wrap">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                       
                                       <div class="scheduler_table_Complete ">
                                           <div class="my_table_Checkall table_slider_head">
                                                    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                        <thead class="cf">
                                                            <tr>
                                                                <th class="text-left col-md-5 col-lg-5 col-sm-5 col-xs-5">Name</th>
                                                                <th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3">Dosage </th>
                                                                <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4">Sig </th>
                                                            </tr>
                                                        </thead>
                                                     </table>
                                           </div>
                                           <div class="table_slider" id="iframe_history_physical_medication">
												<?php  
                                                    $medicNameWidth=215;
                                                    $medicDetailWidth=215;
                                                    include("patient_anesthesia_medi_spreadsheet.php");
                                                ?>
                                            </div>
                                          </div>
                                    </div>
                                </div>	 
                             </div>
                        </div>
                    </Div>
                    
                    <Div class="clearfix margin_adjustment_only"></Div>
                    
                    <div class="panel panel-default new_panel bg_panel_green">
                       <div class="panel-heading">
                                    <h3 class="panel-title rob"> Day Of Surgery Notes  </h3>
                       </div>
                       <div id="p_check_in" class="panel-body ">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                        
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>   
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <label class="li_check"> Yes </label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                      <label class="li_check"> No </label>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <Div class="row">
                                                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                        <label class="date_r" for="from">
                                                           Date of Last Menstrual Cycle
                                                        </label>
                                                    </div>	
                                                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                    <div class="input-group datepickerTxt">
                                                        <input type="text" class="form-control datepickerTxt" id="date1" name="date1" value="<?php echo $date_of_h_p_format; ?>" />
                                                        <div class="input-group-addon datepicker">
                                                            <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                                        </div>
                                                    </div>
                                                    </div>	
                                                </Div>			                                                                        	
                                                    
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                &nbsp; 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Wear Contact Lenses
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($wearContactLenses) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_wear_contact_lenses_yes','chbx_wear_contact_lenses');disp_new(document.frm_history_physicial.chbx_wear_contact_lenses_yes,'chbx_wear_contact_lenses_tb_id');changeChbxColor('chbx_wear_contact_lenses');" <?php if($wearContactLenses=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_wear_contact_lenses" id="chbx_wear_contact_lenses_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($wearContactLenses) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_wear_contact_lenses_no','chbx_wear_contact_lenses');disp_none_new(document.frm_history_physicial.chbx_wear_contact_lenses_no,'chbx_wear_contact_lenses_tb_id');changeChbxColor('chbx_wear_contact_lenses');" <?php if(($getPreOpQuesDetails) && ($wearContactLenses=='No')) echo "CHECKED"; ?> value="No" name="chbx_wear_contact_lenses" id="chbx_wear_contact_lenses_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($wearContactLenses=='Yes') { ?> in <?php }?>" id="chbx_wear_contact_lenses_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="wearContactLensesDesc" name="wearContactLensesDesc" ><?php echo stripslashes($wearContactLensesDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Smoking
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($smoking) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_smoking_yes','chbx_smoking');disp_new(document.frm_history_physicial.chbx_smoking_yes,'chbx_smoking_tb_id');changeChbxColor('chbx_smoking');" <?php if($smoking=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_smoking" id="chbx_smoking_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($smoking) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_smoking_no','chbx_smoking');disp_none_new(document.frm_history_physicial.chbx_smoking_no,'chbx_smoking_tb_id');changeChbxColor('chbx_smoking');" <?php if(($getPreOpQuesDetails) && ($smoking=='No')) echo "CHECKED"; ?> value="No" name="chbx_smoking" id="chbx_smoking_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($smoking=='Yes') { ?> in <?php }?>" id="chbx_smoking_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="smokingDesc" name="smokingDesc" ><?php echo stripslashes($smokingDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Drink Alcohol
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($drinkAlcohal) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_drink_alcohal_yes','chbx_drink_alcohal');disp_new(document.frm_history_physicial.chbx_drink_alcohal_yes,'chbx_drink_alcohal_tb_id');changeChbxColor('chbx_drink_alcohal');" <?php if($drinkAlcohal=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_drink_alcohal" id="chbx_drink_alcohal_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($drinkAlcohal) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_drink_alcohal_no','chbx_drink_alcohal');disp_none_new(document.frm_history_physicial.chbx_drink_alcohal_no,'chbx_drink_alcohal_tb_id');changeChbxColor('chbx_drink_alcohal');" <?php if(($getPreOpQuesDetails) && ($drinkAlcohal=='No')) echo "CHECKED"; ?> value="No" name="chbx_drink_alcohal" id="chbx_drink_alcohal_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($drinkAlcohal=='Yes') { ?> in <?php }?>" id="chbx_drink_alcohal_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="drinkAlcohalDesc" name="drinkAlcohalDesc" ><?php echo stripslashes($drinkAlcohalDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Have an automatic internal defibrillator
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($haveAutomatic) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_have_automatic_yes','chbx_have_automatic');disp_new(document.frm_history_physicial.chbx_have_automatic_yes,'chbx_have_automatic_tb_id');changeChbxColor('chbx_have_automatic');" <?php if($haveAutomatic=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_have_automatic" id="chbx_have_automatic_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($haveAutomatic) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_have_automatic_no','chbx_have_automatic');disp_none_new(document.frm_history_physicial.chbx_have_automatic_no,'chbx_have_automatic_tb_id');changeChbxColor('chbx_have_automatic');" <?php if(($getPreOpQuesDetails) && ($haveAutomatic=='No')) echo "CHECKED"; ?> value="No" name="chbx_have_automatic" id="chbx_have_automatic_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($haveAutomatic=='Yes') { ?> in <?php }?>" id="chbx_have_automatic_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="haveAutomaticDesc" name="haveAutomaticDesc" ><?php echo stripslashes($haveAutomaticDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Medical History obtained from
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>  
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($medicalHistoryObtained) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_medical_history_obtained_yes','chbx_medical_history_obtained');disp_new(document.frm_history_physicial.chbx_medical_history_obtained_yes,'chbx_medical_history_obtained_tb_id');changeChbxColor('chbx_medical_history_obtained');" <?php if($medicalHistoryObtained=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_medical_history_obtained" id="chbx_medical_history_obtained_yes" >
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($medicalHistoryObtained) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_medical_history_obtained_no','chbx_medical_history_obtained');disp_none_new(document.frm_history_physicial.chbx_medical_history_obtained_no,'chbx_medical_history_obtained_tb_id');changeChbxColor('chbx_medical_history_obtained');" <?php if(($getPreOpQuesDetails) && ($medicalHistoryObtained=='No')) echo "CHECKED"; ?> value="No" name="chbx_medical_history_obtained" id="chbx_medical_history_obtained_no">
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse <?php if($medicalHistoryObtained=='Yes') { ?> in <?php }?>" id="chbx_medical_history_obtained_tb_id" style="height: auto;">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="medicalHistoryObtainedDesc" name="medicalHistoryObtainedDesc" ><?php echo stripslashes($medicalHistoryObtainedDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                     <div id="" class="inner_safety_wrap">
                                        <div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Notes
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea class="form-control" style="resize:none;" id="otherNotes" name="otherNotes" ><?php echo stripslashes($otherNotes); ?></textarea>
                                                </div>
                                            </div>
                                        </div> 
                                     </div>
                                  </div>
                           </div>
                      </div>          
                     </div>
                    
                        
                 </div>

            <div class="clearfix border-dashed margin_adjustment_only"></div>
            <Div class="clearfix margin_adjustment_only"></Div>                                        
            
			<?php
            //START CODE RELATED TO SURGEON SIGNATURE ON FILE
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
                    $callJavaFunSurgeon = "document.frm_history_physicial.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Surgeon1');";
                }					
                $surgeon1SignOnFileStatus = "Yes";
                $TDsurgeon1NameIdDisplay = "block";
                $TDsurgeon1SignatureIdDisplay = "none";
                $Surgeon1Name = $loggedInUserName;
                $signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
                    $Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
                    $surgeon1SignOnFileStatus = $signSurgeon1Status;	
                    $TDsurgeon1NameIdDisplay = "none";
                    $TDsurgeon1SignatureIdDisplay = "block";
                    //$signSurgeon1DateTimeFormatNew=date("m-d-Y h:i A",strtotime($signSurgeon1DateTime));
					$signSurgeon1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signSurgeon1DateTime);
                }
                if($_SESSION["loginUserId"]==$signSurgeon1Id) {
                    $callJavaFunSurgeonDel = "document.frm_history_physicial.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
                }else {
                    $callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
                }
            //END CODE RELATED TO SURGEON SIGNATURE ON FILE                                    
            
            //START CODE RELATED TO ANESTHESIOLOGIST SIGNATURE ON FILE
                if($loggedInUserType<>"Anesthesiologist") {
                    $loginUserName = $_SESSION['loginUserName'];
                    $callJavaFunAnesthesia = "return noAuthorityFunCommon('Anesthesiologist');";
                }else {
                    $loginUserId = $_SESSION["loginUserId"];
                    $callJavaFunAnesthesia = "document.frm_history_physicial.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Anesthesia1');";
                }					
                $anesthesia1SignOnFileStatus = "Yes";
                $TDanesthesia1NameIdDisplay = "block";
                $TDanesthesia1SignatureIdDisplay = "none";
                $Anesthesia1Name = $loggedInUserName;
                $signAnesthesia1DateTimeFormatNew = date("m-d-Y h:i A");
                if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
                    $Anesthesia1Name = $signAnesthesia1LastName.", ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
                    $anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
                    $TDanesthesia1NameIdDisplay = "none";
                    $TDanesthesia1SignatureIdDisplay = "block";
                    //$signAnesthesia1DateTimeFormatNew=date("m-d-Y h:i A",strtotime($signAnesthesia1DateTime));
					$signAnesthesia1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signAnesthesia1DateTime);
                
                    $Anesthesia1PreFix = 'Dr.';
                    if($usrAllSubTypeArr[$signAnesthesia1Id]=='CRNA') {
                        $Anesthesia1PreFix = '';
                    }
                }
                if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
                    $callJavaFunAnesthesiaDel = "document.frm_history_physicial.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
                }else {
                    $callJavaFunAnesthesiaDel = "alert('Only ".$Anesthesia1PreFix." $Anesthesia1Name can remove this signature');";
                }
            //END CODE RELATED TO ANESTHESIOLOGIST SIGNATURE ON FILE	
            
            //START CODE RELATED TO NURSE SIGNATURE ON FILE    
                if($loggedInUserType<>"Nurse") {
                    $loginUserName = $_SESSION['loginUserName'];
                    $callJavaFun = "return noAuthorityFunCommon('Nurse');";
                }else {
                    $loginUserId = $_SESSION["loginUserId"];
                    $callJavaFun = "document.frm_history_physicial.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Nurse1');";
                }
                $nurseSignOnFileStatus = "Yes";
                $TDnurseNameIdDisplay = "block";
                $TDnurseSignatureIdDisplay = "none";
                $NurseNameShow = $loggedInUserName;
                $pre_op_nurse_sign_date="";
                $signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
                if($signNurseId<>0 && $signNurseId<>"") {
                    $NurseNameShow = $signNurseName;
                    $nurseSignOnFileStatus = $signNurseStatus;
                    //$pre_op_nurse_sign_date =date("m-d-Y h:i A",strtotime($signNurseDateTime));	
					$pre_op_nurse_sign_date = $objManageData->getFullDtTmFormat($signNurseDateTime);
                    
                    $TDnurseNameIdDisplay = "none";
                    $TDnurseSignatureIdDisplay = "block";
                }
                if($_SESSION["loginUserId"]==$signNurseId) {
                    $callJavaFunDel = "document.frm_history_physicial.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','history_physicial_clearance_ajaxSign.php','$loginUserId','Nurse1','delSign');";
                }else {
                    $callJavaFunDel = "alert('Only $signNurseName can remove this signature');";
                }
            //END CODE RELATED TO NURSE SIGNATURE ON FILE	
            
            
            //START SET BACKGROUND COLOR 
            $surgeonSignBackColor=$chngBckGroundColor;
            if($signSurgeon1Id!=0){
                $surgeonSignBackColor==$whiteBckGroundColor; 
            }
            $signAnesthesiaIdBackColor=$chngBckGroundColor;
            if($signAnesthesia1Id!=0){
                $signAnesthesiaIdBackColor==$whiteBckGroundColor; 
            }
            $nurseSignBackColor=$chngBckGroundColor;
            if($signNurseId!=0){
                $nurseSignBackColor==$whiteBckGroundColor; 
            }
            //END SET BACKGROUND COLOR 
            ?>            
            
            <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $surgeonSignBackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
                    </div>
                    <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ".$Surgeon1Name; ?>  </a></span>	     
                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                        <span class="rob full_width"> <b> Signature Date</b> <?php echo $signSurgeon1DateTimeFormatNew;?></span>
                    </div>
           </div>
           <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <?php
					if($patient_primary_procedure_categoryID<>'2') {
					?>
                    <div class="inner_safety_wrap" id="TDanesthesia1NameId" style="display:<?php echo $TDanesthesia1NameIdDisplay;?>;">
                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor?>;" onClick="javascript:<?php echo $callJavaFunAnesthesia;?>"> Anesthesia Provider Signature </a>
                    </div>
                    <div class="inner_safety_wrap collapse" id="TDanesthesia1SignatureId" style="display:<?php echo $TDanesthesia1SignatureIdDisplay;?>;">
                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunAnesthesiaDel;?>"> <?php echo "<b>Anesthesia Provider:</b> ".$Anesthesia1PreFix." ".$Anesthesia1Name; ?>  </a></span>	     
                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $anesthesia1SignOnFileStatus;?></span>
                        <span class="rob full_width"> <b> Signature Date</b> <?php echo $signAnesthesia1DateTimeFormatNew;?></span>
                    </div>
                    <?php
					}
					?>
           </div>
           
           <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                <div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>;">
                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $nurseSignBackColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
                </div>
                <div class="inner_safety_wrap collapse" id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>;">
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow; ?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $nurseSignOnFileStatus;?></span>
                    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $pre_op_nurse_sign_date; ?> <span class="fa fa-edit"></span></span></span>
                </div>
            </div>
          <Div class="clearfix margin_adjustment_only"></Div>
          <Div class="clearfix margin_adjustment_only"></Div>  
      </div>
        
        
    </form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	
    <form name="frm_return_BlankMainForm" method="post" action="history_physicial_clearance.php?cancelRecord=true<?php echo $saveLink;?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<?php 
//CODE FOR FINALIZE FORM
	$finalizePageName = "history_physicial_clearance.php";
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
		document.getElementById('divSaveAlert').style.display = 'inline-block';
	</script>
	<?php
}

include_once("print_page.php");
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>

<div id="myModalHP" class="modal fade in" style="top:20%"> <!--Common Alert Container-->
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Confirm</h4>
			</div>
			<div class="modal-body" style="min-height:auto;">
				<p style="padding: 10px;" class="text-center"></p>
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button id="cancel_hp_yes" class="btn btn-primary hidden" onclick="location.href='history_physicial_clearance.php?patient_id=<?php echo $_REQUEST["patient_id"];?>&pConfId=<?php echo $_REQUEST["pConfId"];?>&rightClick=yes&thisId=<?php echo $_REQUEST["thisId"];?>&innerKey=<?php echo $_REQUEST["innerKey"];?>&hpCopy=yes&preColor=<?php echo $_REQUEST["preColor"];?>'">Yes</button>
				<button id="cancel_hp_no" class="btn btn-danger hidden" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
<?php
if($isConfirm && $_REQUEST["hpCopy"] != "yes") {
?>
	<script>copyPrevHpChartConfirm();</script>
<?php
}
//START SET NKDA STATUS IN HEADER
$detailAllergiesNKDA = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $_REQUEST['pConfId']);
$confirmAllergiesNKDAStatus = $detailAllergiesNKDA->allergiesNKDA_status;	

$ptAllergiesTblQry = "SELECT `allergy_name` FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '".$_REQUEST["pConfId"]."'";
$ptAllergiesTblRes = imw_query($ptAllergiesTblQry) or die(imw_error());
$ptAllergiesTblNumRow = imw_num_rows($ptAllergiesTblRes);
if($ptAllergiesTblNumRow>0) {
	$ptAllergiesTblRow = imw_fetch_array($ptAllergiesTblRes);
	$ptAllergyName = $ptAllergiesTblRow['allergy_name'];
	if(trim(strtoupper($ptAllergyName))=='NKA' && $ptAllergiesTblNumRow==1) {
		$ptAllergiesValue = 'NKA';
	}else {
		$ptAllergiesValue = '<img src="images/Interface_red_image003.gif" style="width:17px; height:15px;vertical-align:middle;" onClick="showAllergiesPopUpFn('.$_REQUEST["pConfId"].');">';
	}
}else if($confirmAllergiesNKDAStatus=="Yes") {
	$ptAllergiesValue = 'NKA';
}else {
	$ptAllergiesValue = '';
}
?>
<script>
if(top.document.getElementById('allergiesHeaderId')) {
	top.document.getElementById('allergiesHeaderId').innerHTML='<?php echo $ptAllergiesValue;?>';
}
</script>
<?php	
//END SET NKDA STATUS IN HEADER
?>	
</body>
</html>