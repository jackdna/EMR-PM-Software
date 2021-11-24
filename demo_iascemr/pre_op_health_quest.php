<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
?>
<!DOCTYPE HTML>
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
$tablename = "preophealthquestionnaire";

include_once("common/commonFunctions.php");
$blockImg = "images/block.gif";
$noneImg = "images/none.gif";

//include("common/linkfile.php");
$spec = "
</head>
<body onLoad=\"top.changeColor('".$commonbg_color."');\" onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();\" id=\"pre_op_health\">
";
include("common/link_new_file.php");
include_once("common/pre_define_medication.php"); //PRE OP  HEALTH QUEST (ALSO FOR LOCAL ANES, PRE-OP GEN ANES)

include_once("admin/classObjectFunction.php");
$objManageData 		= new manageData;
$thisId 			= $_REQUEST['thisId'];
$innerKey 			= $_REQUEST['innerKey'];
$preColor 			= $_REQUEST['preColor'];
$uid				= $_SESSION['loginUserId'];
$usertypeqry		= imw_query("select * from users where usersId='".$uid."'");
$recordId			= imw_fetch_array($usertypeqry);
$usertype			= $recordId['user_type'];
$nurse				= $recordId['fname']." ".$recordId['lname'];

$patient_id 		= $_REQUEST['patient_id'];
$pConfId 			= $_REQUEST['pConfId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$ascId 				= $_SESSION['ascId'];
$SaveForm_alert 	= $_REQUEST['SaveForm_alert'];
$preOpHealthQuesId 	= $_REQUEST['preOpHealthQuesId'];
$cancelRecord 		= $_REQUEST['cancelRecord'];
$nurse				= $_REQUEST['nurse'];

	if(!$cancelRecord){
		////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails 		= $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$pre_op_health_ques_form 	= $getLeftLinkDetails->pre_op_health_ques_form;	
			if($pre_op_health_ques_form=='true'){
				$formArrayRecord['pre_op_health_ques_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
			//MAKE AUDIT STATUS VIEW
			if($_REQUEST['saveRecord']!='true'){
				unset($arrayRecord);
				$arrayRecord['user_id'] 		= $_SESSION['loginUserId'];
				$arrayRecord['patient_id'] 		= $patient_id;
				$arrayRecord['confirmation_id'] = $pConfId;
				$arrayRecord['form_name'] 		= 'pre_op_health_ques_form';
				$arrayRecord['status'] 			= 'viewed';
				$arrayRecord['action_date_time']= date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
			}
			//MAKE AUDIT STATUS VIEW
			
// DONE BY MAMTA
}elseif($cancelRecord=="true") {  //IF PRESS CANCEL BUTTON
			$fieldName 	= "pre_op_health_ques_form";
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
			$question[$i][$key]=stripslashes($value);
		}
		$i++;	
	}
	
	//echo $question[2]['question'];
//End adminHealthquestionare

	/*
	*
	*	Code To get status of directory H&P
	*/
	//
	$ekgHp		=	$objManageData->getDirContentStatus($pConfId,2);
	$ekgHpLink	=	'';
	if($ekgHp)
	{
			$ekgHpLink	.=	'<a href="#" class="btn-sm ajaxDir" data-confirmation-id="'.$pConfId.'" >H&P</a>&nbsp;';	
	}
	$sxPlanSheet = $objManageData->getDirContentStatus($pConfId,3,1,'Sx Planning Sheet');
	if($sxPlanSheet)
	{
			$ekgHpLink	.=	$sxPlanSheet;	
	}
	
	if($_REQUEST['saveRecord'] <> 'true')
	{	
			$tbl	=	'preophealthquestionnaire';
			$currHPFormStatus	= $objManageData->getRowRecord($tbl, 'confirmation_id ', $pConfId,'','','form_status');
			$formStatus		=	$currHPFormStatus->form_status ;
			
			if($ekgHp && constant('CHECK_H_AND_P') <> 'NO')	
			{	
				$formStatus	=	'completed';
				imw_query("Update ".$tbl." Set form_status = '".$formStatus."' Where confirmation_id = '".$pConfId."'  ") or die('Error found at line no. '.(__LINE__).': '.imw_error());
			}
			elseif( (!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO'))  && ($formStatus === 'completed' || $formStatus === 'not completed' )) 
			{
				$chartStatus	=	$objManageData->validateChart('pre_op_health_quest.php',$_REQUEST['pConfId']);
				$formStatus		=	($chartStatus) ? 'completed' : 'not completed';
				imw_query("Update ".$tbl." Set form_status = '".$formStatus."' Where confirmation_id = '".$pConfId."'  ") or die('Error found at line no. '.(__LINE__).': '.imw_error());
				
			}
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";
	}


if($_REQUEST['saveRecord']=='true'){
//updation  in healthquestionadmin
  
	$selectQry=imw_query("select * from healthquestionadmin where
							confirmation_id='$pConfId'");							
	 $count=imw_num_rows($selectQry);						 
	if($count>0){
		$k=0;
		while($selectRes=imw_fetch_array($selectQry)){
			$qustatus=$selectRes['adminQuestionStatus'];
		    $question[]=stripslashes($selectRes['adminQuestion']);
		}	
		for($key=0;$key<$count;$key++){
		$questionsDes[] = $_REQUEST['questionDes'.$key];
		$adminQuestionArray[]=$_REQUEST['quest'.$key];
		$adminQuestDescArr[] = ($_REQUEST['quest'.$key] !="Yes" ? "" : $_REQUEST['adminQuestDesc'.$key]);
								
		$updateQry=imw_query("update healthquestionadmin set
								adminQuestionStatus='".$adminQuestionArray[$key]."',
								adminQuestionDesc='".addslashes($adminQuestDescArr[$key])."'
								where
								adminQuestion='".addslashes($questionsDes[$key])."' and
								confirmation_id='$pConfId'") or die(imw_error());	
											
		}
	}else{
		//insertion in healthquestionadmin

		$questionsDesc[] 			= $_REQUEST['questionDesc'];
		$selectAdminQuestionsQry	= "select * from healthquestioner";
			$selectAdminQuestions	= imw_query($selectAdminQuestionsQry);
			$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
			$i=0;
			while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
			{
				$quId[]=stripslashes($ResultselectAdminQuestions['question']);
			}
		for($key=0;$key<$selectAdminQuestionsRows;$key++)
		{
			$adminQuestionArray[]= $_REQUEST['question'.$key];
			$adminQuestDescArr[] = ($_REQUEST['question'.$key] !="Yes" ? "" : $_REQUEST['adminQuestDescNew'.$key]);
			$insertQuestionQry=imw_query("insert into healthquestionadmin set 
									adminQuestion='".addslashes($quId[$key])."',
									adminQuestionStatus='".addslashes($adminQuestionArray[$key])."',
									adminQuestionDesc='".$adminQuestDescArr[$key]."',
									confirmation_id='$pConfId', 
									patient_id= '$patient_id'
									");
		}					
		//End insertion in healthquestionadmin 

	}

//End updation  in healthquestionadmin
	$text 							= $_REQUEST['getText'];
	$tablename 						= "preophealthquestionnaire";
	$arrayRecord['confirmation_id'] = $pConfId;
	$arrayRecord['heartTrouble'] 	= $_POST['chbx_ht'];
	$arrayRecord['heartTroubleDesc']= addslashes($_POST['heartTroubleDesc']);
	
	
//START CODE TO CHECK NURSE SIGN IN DATABASE
	$chkNurseSignDetails = $objManageData->getRowRecord('preophealthquestionnaire', 'confirmation_id', $pConfId);
	if($chkNurseSignDetails) {
		$chk_signNurseId = $chkNurseSignDetails->signNurseId;
		//CHECK FORM STATUS
		$chk_form_status = $chkNurseSignDetails->form_status;
		//CHECK FORM STATUS
		
		//START CHECK SIGNATURE IMAGE PATH OF PATIENT AND WITNESS TO UNLINK ON RESETTING THE RECORD
		$chk_patient_sign_image_path = $chkNurseSignDetails->patient_sign_image_path;
		$chk_witness_sign_image_path = $chkNurseSignDetails->witness_sign_image_path;
		//END CHECK SIGNATURE IMAGE PATH OF PATIENT AND WITNESS TO UNLINK ON RESETTING THE RECORD
	}
//END CODE TO CHECK NURSE SIGN IN DATABASE 

$elem_signature=$_POST["elem_signature"];
if(($elem_signature == '255-0-0:;') || ($elem_signature == '0-0-0:;')){
	$elem_signature = '';
}
$witnSign=$_POST["witnSign"];
if(($witnSign == '255-0-0:;') || ($witnSign == '0-0-0:;')){
	$witnSign = '';
}

if($ekgHp && constant('CHECK_H_AND_P') <> 'NO') { $formStatus	=	'completed';	}
elseif(($_POST['chbx_ht']!='') && ($_POST['chbx_sht']!='') 
	&& ($_POST['chbx_HighBP']!='') && ($_POST['chbx_anti_thrp']!='') 
	&& ($_POST['chbx_ast_slp']!='') && ($_POST['chbx_tuber']!='')
	&& ($_POST['chbx_diab']!='') && ($_POST['chbx_epile']!='')
	&& ($_POST['chbx_restless']!='') && ($_POST['chbx_hepat']!='')  
	&& ($_POST['chbx_kidn']!='') && ($_POST['chbx_hiv_auto']!='') 
	&& ($_POST['chbx_hist_can']!='') //&& ($_POST['brest_cancer']!='') 
	&& ($_POST['chbx_org_trns'] !='') && ($_POST['chbx_bad_react'] !='') 
	&& ($_POST['chbx_use_wheel'] !='') && ($_POST['chbx_wear_cont']!='') 
	&& ($_POST['chbx_smoke']!='') &&($_POST['chbx_drink']!='') 
	&& ($_POST['chbx_hav_auto_int']!='') && ($_POST['chbx_hav_any_met']!='') 
	&& ($_POST['emergencyContactPerson']!='') && ($_POST['emergencyContactTel']!='') 
	//&& ($elem_signature!='') 
	&& ($_POST['witnessname']!='')
	//&& ($witnSign!='')  
	//&& ($chk_signNurseId<>"0")
  ) {
		$formStatus = 'completed';
	}
	else
	{
	$formStatus='not completed';
	}
	
	//START SAVE SIGNATURE
	for($ps=1;$ps<=2;$ps++){	
		$postSigData				= '';
		$ptWtHealthSrc				= '';
		$postSigDataPtImgSavePath	= '';
		$postSigDataWtImgSavePath	= '';
		
		if($ps==1) { 
			$postSigData 						= $_POST['SigDataPt']; 
			$postSigDataLoadValue		= $_POST['SigDataPtLoadValue'];
			$postSignImagePathStatus	= $_POST['hiddPatientSignImagePathStatus'] ;
		}else if($ps==2) { 
			$postSigData 						=	$_POST['SigDataWt'];
			$postSigDataLoadValue		=	$_POST['SigDataWtLoadValue']; 
			$postSignImagePathStatus	=	''	;
		}
		/*
		if( (!$postSigData  || $postSigData=='undefined' )  ) { echo '<br>ekg - '.$postSigDataLoadValue;
			if(( (!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO')) && !$postSigDataLoadValue) && $ps==1 &&  $postSignImagePathStatus <> 1 ) { //$ps=1 check only for patient signature.(b'coz Witnes Signature Box is removed)
				$formStatus='not completed';
			}
		}*/
		
		if($postSigData!='' && $postSigData!='undefined') {
			$sigDateTime=date('d_m_y_h_i_s');
			$ptWtHealthSrc = 'signHealth_'.$_REQUEST["pConfId"].'_'.$sigDateTime.'_'.$ps.'.jpg';
			$path =  realpath(dirname(__FILE__).'/SigPlus_images').'/'.$ptWtHealthSrc;
			if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
				$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
				$aConn->InitSigPlus();
				$aConn->SigCompressionMode = 2;
				$aConn->SigString=$postSigData;
				$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
				$aConn->ImageXSize = 150; //width of resuting image in pixels
				$aConn->ImageYSize =65; //height of resulting image in pixels
				$aConn->ImagePenWidth = 11; //thickness of ink in pixels
				$aConn->JustifyMode = 5;  //center and fit signature to size
				$aConn->WriteImageFile("$path");
			}else {
				$objManageData->getSigImage($postSigData,$path,$rootServerPath);//TO STORE DATA IN SIGPLUS
			}
			if($ps==1) { 
				$postSigDataPtImgSavePath 				= 'SigPlus_images/'.$ptWtHealthSrc; 
				$arrayRecord['patient_sign_image_path'] = addslashes($postSigDataPtImgSavePath);
				$arrayRecord['patientSign'] 			= addslashes($postSigData);
			}else if($ps==2) { 
				$postSigDataWtImgSavePath 				= 'SigPlus_images/'.$ptWtHealthSrc; 
				$arrayRecord['witness_sign_image_path'] = addslashes($postSigDataWtImgSavePath);
				$arrayRecord['witnessSign'] 			= addslashes($postSigData);
			}
		}
	}
	if(trim($_REQUEST['hidden_patient_sign_image_path'])) {
		$arrayRecord['patient_sign_image_path'] = addslashes($_REQUEST['hidden_patient_sign_image_path']);		
	}
	if(!$arrayRecord['patient_sign_image_path'] && (!$ekgHp || ($ekgHp && constant('CHECK_H_AND_P') == 'NO'))) {
		$formStatus='not completed';	
	}
	//END SAVE SIGNATURE
	
	//START CODE TO RESET THE RECORD
	if($_REQUEST['hiddResetStatusId']=='Yes') {
		$formStatus='';
		$arrayRecord['patient_sign_image_path'] = '';
		$arrayRecord['patientSign'] 			= '';
		$arrayRecord['witness_sign_image_path'] = '';
		$arrayRecord['witnessSign'] 			= '';
		
		$arrayRecord['signNurseId'] 			= '';
		$arrayRecord['signNurseFirstName'] 		= '';
		$arrayRecord['signNurseMiddleName'] 	= '';
		$arrayRecord['signNurseLastName'] 		= '';
		$arrayRecord['signNurseStatus'] 		= '';
		$arrayRecord['signNurseDateTime'] 		= '0000-00-00 00:00:00';

		$arrayRecord['signWitness1Id'] 			= '';
		$arrayRecord['signWitness1FirstName'] 	= '';
		$arrayRecord['signWitness1MiddleName'] 	= '';
		$arrayRecord['signWitness1LastName'] 	= '';
		$arrayRecord['signWitness1Status'] 		= '';
		$arrayRecord['signWitness1DateTime'] 	= '0000-00-00 00:00:00';

		$arrayRecord['resetDateTime'] 			= date('Y-m-d H:i:s');
		$arrayRecord['resetBy'] 				= $_SESSION['loginUserId'];
		
		if($chk_patient_sign_image_path) {
			if(@file_exists($chk_patient_sign_image_path)) {
				@unlink($chk_patient_sign_image_path);
			}
		}
		if($chk_witness_sign_image_path) {
			if(@file_exists($chk_witness_sign_image_path)) {
				@unlink($chk_witness_sign_image_path);
			}
		}
		
	}
	//END CODE TO RESET THE RECORD
	
	if($_POST['chbx_smoke'] != 'Yes') {
		$_POST['smokeAdvise'] = '';
	}
	if($_POST['chbx_drink'] != 'Yes') {
		$_POST['alchoholAdvise'] = '';
	}
	$arrayRecord['form_status'] 				= $formStatus;
	$arrayRecord['stroke'] 						= $_POST['chbx_sht'];
	$arrayRecord['strokeDesc'] 					= addslashes($_POST['strokeDesc']);
	$arrayRecord['HighBP'] 						= $_POST['chbx_HighBP'];
	$arrayRecord['HighBPDesc'] 					= addslashes($_POST['HighBPDesc']);
	$arrayRecord['heartAttack'] 				= $_POST['chbx_sht'];
	$arrayRecord['anticoagulationTherapy'] 		= $_POST['chbx_anti_thrp'];
	$arrayRecord['anticoagulationTherapyDesc'] 	= addslashes($_POST['anticoagulationTherapyDesc']);
	$arrayRecord['asthma'] 						= $_POST['chbx_ast_slp'];
	$arrayRecord['asthmaDesc'] 					= addslashes($_POST['asthmaDesc']);
	$arrayRecord['tuberculosis'] 				= $_POST['chbx_tuber'];
	$arrayRecord['tuberculosisDesc'] 			= addslashes($_POST['tuberculosisDesc']);
	$arrayRecord['sleepApnea'] 					= $_POST['chbx_ast_slp'];
	$arrayRecord['breathingProbs'] 				= $_POST['chbx_ast_slp'];
	$arrayRecord['TB'] 							= $_POST['chbx_ast_slp'];
	$arrayRecord['diabetes'] 					= $_POST['chbx_diab'];
	$arrayRecord['diabetesDesc'] 				= addslashes($_POST['diabetesDesc']);
	$arrayRecord['insulinDependence'] 			= $_POST['chbx_subdiab'];
	$arrayRecord['epilepsy'] 					= $_POST['chbx_epile'];
	$arrayRecord['epilepsyDesc'] 				= addslashes($_POST['epilepsyDesc']);
	$arrayRecord['convulsions'] 				= $_POST['chbx_epile'];
	$arrayRecord['parkinsons'] 					= $_POST['chbx_epile'];
	$arrayRecord['vertigo'] 					= $_POST['chbx_epile'];
	$arrayRecord['restlessLegSyndrome'] 		= $_POST['chbx_restless'];
	$arrayRecord['restlessLegSyndromeDesc'] 	= addslashes($_POST['restlessLegSyndromeDesc']);
	$arrayRecord['hepatitis'] 					= $_POST['chbx_hepat'];	
	$arrayRecord['hepatitisDesc'] 				= addslashes($_POST['hepatitisDesc']);	
	$arrayRecord['hepatitisA'] 					= $_POST['HepatitisA'];	
	$arrayRecord['hepatitisB'] 					= $_POST['HepatitisB'];
	$arrayRecord['hepatitisC']					= $_POST['HepatitisC'];	
	$arrayRecord['kidneyDisease'] 				= $_POST['chbx_kidn'];
	$arrayRecord['kidneyDiseaseDesc'] 			= addslashes($_POST['kidneyDiseaseDesc']);
	$arrayRecord['shunt'] 						= $_POST['chbx_subkidnShunt'];
	$arrayRecord['fistula'] 					= $_POST['chbx_subkidnFistula'];
	$arrayRecord['hivAutoimmuneDiseases'] 		= $_POST['chbx_hiv_auto'];
	$arrayRecord['hivTextArea'] 				= addslashes($_POST['hivTextArea']);
	$arrayRecord['cancerHistory'] 				= $_POST['chbx_hist_can'];	
	$arrayRecord['brest_cancer'] 				= addslashes($_POST['brest_cancer']);	
	$arrayRecord['brestCancerLeft'] 			= $_POST['brestCancerLeft'];	
	$arrayRecord['cancerHistoryDesc'] 			= addslashes($_POST['cancerHistory']);	
	$arrayRecord['organTransplant'] 			= $_POST['chbx_org_trns'];
	$arrayRecord['organTransplantDesc'] 		= addslashes($_POST['organTransDesc']);	
	$arrayRecord['anesthesiaBadReaction'] 		= $_POST['chbx_bad_react'];
	$arrayRecord['anesthesiaBadReactionDesc'] 	= addslashes($_POST['anesthesiaBadReactionDesc']);
	$arrayRecord['otherTroubles'] 				= addslashes($_POST['otherTroubles']);	
	//$arrayRecord['allergies_status'] 			= $_POST['chbx_drug_react'];//nkda now nkda will read from patientconfirmation table
	$arrayRecord['allergies_status_reviewed'] 	= $_POST['chbx_drug_react_reviewed'];
	$arrayRecord['walker'] 						= $_POST['chbx_use_wheel'];
	$arrayRecord['walkerDesc'] 					= addslashes($_POST['walkerDesc']);
	$arrayRecord['contactLenses'] 				= $_POST['chbx_wear_cont'];
	$arrayRecord['contactLensesDesc'] 			= addslashes($_POST['contactLensesDesc']);
	$arrayRecord['smoke'] 						= $_POST['chbx_smoke'];
	$arrayRecord['smokeHowMuch'] 				= addslashes($_POST['smokeHowMuch']);
	$arrayRecord['smokeAdvise'] 				= $_POST['smokeAdvise'];
	$arrayRecord['alchohol'] 					= $_POST['chbx_drink'];
	$arrayRecord['alchoholHowMuch'] 			= addslashes($_POST['alchoholHowMuch']);
	$arrayRecord['alchoholAdvise'] 				= $_POST['alchoholAdvise'];
	$arrayRecord['autoInternalDefibrillator'] 	= $_POST['chbx_hav_auto_int'];
	$arrayRecord['autoInternalDefibrillatorDesc']= addslashes($_POST['autoInternalDefibrillatorDesc']);
	$arrayRecord['metalProsthetics'] 			= $_POST['chbx_hav_any_met'];
	$arrayRecord['notes'] 						= addslashes($_POST['notesDesc']);
	//print'<pre>';print_r($arrayRecord);die;
	//$arrayRecord['patientSign'] 				= $elem_signature;
	
	//$arrayRecord['patient_sign_image_path'] 	= addslashes($postSigDataPtImgSavePath);
	//$arrayRecord['witness_sign_image_path'] 	= addslashes($postSigDataWtImgSavePath);

	$arrayRecord['nursefield'] = $_REQUEST['nurse'];
	$dateQuest = $_POST['date'];
	$dateQuest = $objManageData->changeDateYMD($dateQuest);
	$arrayRecord['dateQuestionnaire'] 		= $dateQuest;
	$arrayRecord['timeQuestionnaire'] 		= time();
	$arrayRecord['emergencyContactPerson'] 	= addslashes($_POST['emergencyContactPerson']);
	$arrayRecord['witnessname']				= addslashes($_POST['witnessname']);
	
	//$arrayRecord['witnessSign']=$witnSign;
	$arrayRecord['emergencyContactPhone'] 	= $_POST['emergencyContactTel'];
	$arrayRecord['progressNotes'] 			= $_POST[''];
	$arrayRecord['nurseId'] 				= $_SESSION['loginUserId'];
	
	
	
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] 			= $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] 		= $_SESSION['patient_id'];
	$arrayStatusRecord['confirmation_id'] 	= $pConfId;
	$arrayStatusRecord['form_name'] 		= 'pre_op_health_ques_form';	
	$arrayStatusRecord['action_date_time'] 	= date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	
	if($preOpHealthQuesId){		
		$objManageData->updateRecords($arrayRecord, 'preophealthquestionnaire', 'preOpHealthQuesId', $preOpHealthQuesId);
	}else{
		$preOpHealthQuesId = $objManageData->addRecords($arrayRecord, 'preophealthquestionnaire');
	}
	
		
		//CODE START TO SET AUDIT STATUS AFTER SAVE
			unset($conditionArr);
			$conditionArr['confirmation_id']= $pConfId;
			$conditionArr['form_name'] 		= 'pre_op_health_ques_form';
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
	
	//delete allregy when save button clicked
	 if($_POST['chbx_drug_react']=='Yes') {
		 imw_query("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfId'");
	 }
	 
	 //START SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
	 unset($arrayNoMedData);
	 $arrayNoMedData['allergiesNKDA_status'] 		= $_REQUEST["chbx_drug_react"];
	 $arrayNoMedData['no_medication_status'] 		= $_REQUEST["no_medication_status"];
	 $arrayNoMedData['no_medication_comments'] 		= $_REQUEST["no_medication_comments"];
	 $objManageData->updateRecords($arrayNoMedData, "patientconfirmation", "patientConfirmationId", $pConfId);
	 //END SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
	 
	//end delete when save button clicked
	
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
$(document).ready(function () {
	
	xmlHttp1=GetXmlHttpObject();
	if (xmlHttp1==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	} 
	var refresh_pConfId='<?php echo $_REQUEST["pConfId"]?>';
	url_refresh_meds="common/patient_health_quest_saved_medication.php?refresh_meds=yes&pConfId="+refresh_pConfId+"&test="+Math.random();
	xmlHttp1.onreadystatechange=function(){
		if(xmlHttp1.readyState==4){
			var refresh_meds_val = xmlHttp1.responseText;
			if(refresh_meds_val!=''){
				if(top.document.getElementById("preDefineSavedHealthQuestMedSubDiv")) {
					top.document.getElementById("preDefineSavedHealthQuestMedSubDiv").innerHTML = refresh_meds_val;	
				}
			}
		}
	}
	xmlHttp1.open("GET",url_refresh_meds,true)
	xmlHttp1.send(null)

	//START CODE FOR NO MEDICATIONS
	var objMed = document.getElementById('no_medication_status');
	clearMedVal(objMed);
	//END CODE FOR NO MEDICATIONS
	
		
});
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
//Applet
function get_App_Coords(objElem){
	//alert(objElem);
	var coords,appName;
	var objElemSign = document.frm_health_ques.elem_signature;
	//appName = objElem.name;
	appName = objElem;
	coords = getCoords(appName);
	objElemSign.value = refineCoords(coords);
}
function get_App_Coords_wit(objElemwit){
	var coordswit,appNamewit;
	var objElemSignwit = document.frm_health_ques.witnSign;
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
	var displayStatus = document.getElementById('chbx_hepat_yes').checked;
	if(displayStatus == true){
		document.getElementById('trHepatitis').style.display = 'inline-block';
	}else{
		document.getElementById('trHepatitis').style.display = 'none';
		document.getElementById('HepatitisA').checked = false;
		document.getElementById('HepatitisB').checked = false;
		document.getElementById('HepatitisC').checked = false;
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

		if(delSign) {
			document.getElementById(TDUserNameId).style.display = 'inline-block';
			document.getElementById(TDUserSignatureId).style.display = 'none';
		}else {
			document.getElementById(TDUserNameId).style.display = 'none';
			document.getElementById(TDUserSignatureId).style.display = 'inline-block';
		}
		
		//SIGN ON FILE FIELD
		if(TDUserNameId=='TDnurseNameId') {
			if(document.getElementById('TDnurseSignatureOnFileId')) {
				if(delSign) {
					document.getElementById('TDnurseSignatureOnFileId').style.display = 'none';
				}else {
					document.getElementById('TDnurseSignatureOnFileId').style.display = 'inline-block';
				}
			}
		}		
		//SIGN ON FILE FIELD
		

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
		//var ascId1 = '<?php echo $_REQUEST["ascId"];?>';

		//var url="pre_op_nursing_record_ajaxSign.php";
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&userIdentity="+userIdentity
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		//url=url+"&ascId="+ascId1
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

	$(document).ready(function () {
		$("#allergies_drug").click(function(){
			alert("TEST");
		});
		$('.right_label label').click(function(){
			var assign_span =	$(this).children('span');
			var checked_class = "fa-check-square-o";
			var unchecked_class = "fa-square-o"	;
			if($(assign_span).hasClass(checked_class) ){
				$(assign_span).addClass(unchecked_class);
				$(assign_span).removeClass(checked_class);
			}
			else {
				$(assign_span).removeClass(unchecked_class);
				$(assign_span).addClass(checked_class);
			}
		});
		
		$('.toggle_desc').on('click', function(){
		 if($(this).hasClass('collapsed')){
			 $(this).children('span').addClass('fa-angle-double-up');
			 $(this).children('span').removeClass('fa-angle-double-down');
		  }
		  else{
			 $(this).children('span').removeClass('fa-angle-double-up');
			 $(this).children('span').addClass('fa-angle-double-down');
		  }
		  top.frames[0].setPNotesHeight();
						
		});	
		$('.inner_safety_wrap');
		$('.inner_safety_wrap a.li_check').click(function(){
			var assign_span =	$(this).children('span');
			var checked_class = "fa-check-square-o";
			var unchecked_class = "fa-square-o"	;
			if($(assign_span).hasClass(checked_class) ){
				$(assign_span).addClass(unchecked_class);
				$(assign_span).removeClass(checked_class);
			}
			
			else {
				$(assign_span).removeClass(unchecked_class);
				$(assign_span).addClass(checked_class);
			}
		});
			
	});
	
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
	$finalizeStatus 							= $detailConfirmation->finalize_status;	
	$allergiesNKDA_patientconfirmation_status 	= $detailConfirmation->allergiesNKDA_status;	
	$noMedicationStatus 						= $detailConfirmation->no_medication_status;	
	$noMedicationComments 						= $detailConfirmation->no_medication_comments;	

// GETTING CONFIRMATION DETAILS
	
	$table = 'allergies';
	//include("common/pre_defined_popup.php");
	if($preOpHealthQuesId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord("preophealthquestionnaire", "preOpHealthQuesId", $preOpHealthQuesId, " *, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat ");
	}else if($pConfId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord("preophealthquestionnaire", "confirmation_id", $pConfId, " *, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat ");	
	}
	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
	}
	
	$hiddPatientSignImagePathStatus	=	($patient_sign_image_path) ? 1 : 0 ;
	$blurInput = "onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
	$keyPressInput = "onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
	?>
	<form name="frm_health_ques" id="frm_health_ques" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="pre_op_health_quest.php?saveRecord=true&amp;SaveForm_alert=true">
        <input type="hidden" name="divId" id="divId">
        <input type="hidden" name="counter" id="counter">
        <input type="hidden" name="secondaryValues" id="secondaryValues">
        <input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
        <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">			
        <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
        <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
        <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
        <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $_REQUEST['patient_id']; ?>">
        <input type="hidden" name="preOpHealthQuesId" id="preOpHealthQuesId" value="<?php echo $preOpHealthQuesId; ?>">
        <input type="hidden" name="getText" id="getText">
        <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
        <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
        <input type="hidden" name="frmAction" id="frmAction" value="pre_op_health_quest.php">
        <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
        <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
        <input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
        <input type="hidden"  name="hiddPatientSignImagePathStatus" id="hiddPatientSignImagePathStatus" value="<?=$hiddPatientSignImagePathStatus?>" />
        <input type="hidden" name="hidden_patient_sign_image_path" id="hidden_patient_sign_image_path" value='<?php echo $patient_sign_image_path;?>' />
      
      <!--slider_content-->
      <div class=" scheduler_table_Complete" id="" style="">
      		<?php
					$epost_table_name = "preophealthquestionnaire";
					include("./epost_list.php");
			?>
                
            <!--
            <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_green">
                <span class="bg_span_green">
                    Pre-Op Health Questionnaire
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
                     <div class="panel panel-default bg_panel_green">
                       <div class="panel-heading">
                                    <h3 class="panel-title rob"> Have you ever had ?  </h3>
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
                                                        <label class="li_check"> Yes </label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                      <label class="li_check"> No </label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       &nbsp;
                                                    </div>    		                               
                                                </div> 
                                            </Div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Heart Trouble 
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($heartTrouble) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ht_yes','chbx_ht'),disp(document.frm_health_ques.chbx_ht,'htha_yes'),changeChbxColor('chbx_ht')" <?php if($heartTrouble=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ht" id="chbx_ht_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($heartTrouble) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ht_no','chbx_ht'),disp_none(document.frm_health_ques.chbx_ht,'htha_yes'),changeChbxColor('chbx_ht')" <?php if(($getPreOpQuesDetails) && ($heartTrouble=='No')) echo "CHECKED"; ?> value="No" name="chbx_ht" id="chbx_ht_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_htha_yes" style="cursor:pointer;" class="fa <?php if($heartTrouble=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'htha_yes','spn_htha_yes');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="htha_yes" style="display:<?php if($heartTrouble=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="heartTroubleDesc" name="heartTroubleDesc" ><?php echo stripslashes($heartTroubleDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Stroke 
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($stroke) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_sht_yes','chbx_sht'),disp(document.frm_health_ques.chbx_sht,'chbx_sht_tb_id'),changeChbxColor('chbx_sht')" <?php if($stroke=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_sht" id="chbx_sht_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($stroke) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_sht_no','chbx_sht'),disp_none(document.frm_health_ques.chbx_sht,'chbx_sht_tb_id'),changeChbxColor('chbx_sht')" <?php if(($getPreOpQuesDetails) && ($stroke=='No')) echo "CHECKED"; ?> value="No" name="chbx_sht" id="chbx_sht_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_sht_tb_id" style="cursor:pointer;" class="fa <?php if($stroke=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_sht_tb_id','spn_chbx_sht_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_sht_tb_id" style="display:<?php if($stroke=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="strokeDesc" name="strokeDesc" ><?php echo stripslashes($strokeDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    High BP 
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($HighBP) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_HighBP_yes','chbx_HighBP'),disp(document.frm_health_ques.chbx_HighBP,'chbx_HighBP_tb_id'),changeChbxColor('chbx_HighBP')" <?php if($HighBP=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_HighBP" id="chbx_HighBP_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($HighBP) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_HighBP_no','chbx_HighBP'),disp_none(document.frm_health_ques.chbx_HighBP,'chbx_HighBP_tb_id'),changeChbxColor('chbx_HighBP')" <?php if(($getPreOpQuesDetails) && ($HighBP=='No')) echo "CHECKED"; ?> value="No" name="chbx_HighBP" id="chbx_HighBP_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_HighBP_tb_id" style="cursor:pointer;" class="fa <?php if($HighBP=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_HighBP_tb_id','spn_chbx_HighBP_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_HighBP_tb_id" style="display:<?php if($HighBP=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="HighBPDesc" name="HighBPDesc" ><?php echo stripslashes($HighBPDesc); ?></textarea>
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
                                                        <span class="colorChkBx" style=" <?php if($anticoagulationTherapy) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_anti_thrp_yes','chbx_anti_thrp'),disp(document.frm_health_ques.chbx_anti_thrp,'chbx_anti_thrp_tb_id'),changeChbxColor('chbx_anti_thrp')" <?php if($anticoagulationTherapy=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_anti_thrp" id="chbx_anti_thrp_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($anticoagulationTherapy) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_anti_thrp_no','chbx_anti_thrp'),disp_none(document.frm_health_ques.chbx_anti_thrp,'chbx_anti_thrp_tb_id'),changeChbxColor('chbx_anti_thrp')" <?php if(($getPreOpQuesDetails) && ($anticoagulationTherapy=='No')) echo "CHECKED"; ?> value="No" name="chbx_anti_thrp" id="chbx_anti_thrp_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_anti_thrp_tb_id" style="cursor:pointer;" class="fa <?php if($anticoagulationTherapy=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_anti_thrp_tb_id','spn_chbx_anti_thrp_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_anti_thrp_tb_id" style="display:<?php if($anticoagulationTherapy=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
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
                                                    Asthma, Sleep Apnea, Breathing Problems
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($asthma) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ast_slp_yes','chbx_ast_slp'),disp(document.frm_health_ques.chbx_ast_slp,'chbx_ast_slp_tb_id'),changeChbxColor('chbx_ast_slp')" <?php if($asthma=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ast_slp" id="chbx_ast_slp_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($asthma) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_ast_slp_no','chbx_ast_slp'),disp_none(document.frm_health_ques.chbx_ast_slp,'chbx_ast_slp_tb_id'),changeChbxColor('chbx_ast_slp')" <?php if(($getPreOpQuesDetails) && ($asthma=='No')) echo "CHECKED"; ?> value="No" name="chbx_ast_slp" id="chbx_ast_slp_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_ast_slp_tb_id" style="cursor:pointer;" class="fa <?php if($asthma=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_ast_slp_tb_id','spn_chbx_ast_slp_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse" id="chbx_ast_slp_tb_id" style="display:<?php if($asthma=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="asthmaDesc" name="asthmaDesc" ><?php echo stripslashes($asthmaDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Tuberculosis
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($tuberculosis) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_tuber_yes','chbx_tuber'),disp(document.frm_health_ques.chbx_tuber,'chbx_tuber_tb_id'),changeChbxColor('chbx_tuber')" <?php if($tuberculosis=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_tuber" id="chbx_tuber_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($tuberculosis) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_tuber_no','chbx_tuber'),disp_none(document.frm_health_ques.chbx_tuber,'chbx_tuber_tb_id'),changeChbxColor('chbx_tuber')" <?php if(($getPreOpQuesDetails) && ($tuberculosis=='No')) echo "CHECKED"; ?> value="No" name="chbx_tuber" id="chbx_tuber_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_tuber_tb_id" style="cursor:pointer;" class="fa <?php if($tuberculosis=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_tuber_tb_id','spn_chbx_tuber_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     <div class="inner_safety_wrap collapse" id="chbx_tuber_tb_id" style="display:<?php if($tuberculosis=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="tuberculosisDesc" name="tuberculosisDesc" ><?php echo stripslashes($tuberculosisDesc); ?></textarea>
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
                                                        <span class="colorChkBx" style=" <?php if($diabetes) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_diab_yes','chbx_diab'),disp(document.frm_health_ques.chbx_diab,'chbx_diab_tb_id'),changeChbxColor('chbx_diab')" <?php if($diabetes=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_diab" id="chbx_diab_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($diabetes) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_diab_no','chbx_diab'),disp_none(document.frm_health_ques.chbx_diab,'chbx_diab_tb_id'),changeChbxColor('chbx_diab')" <?php if(($getPreOpQuesDetails) && ($diabetes=='No')) echo "CHECKED"; ?> value="No" name="chbx_diab" id="chbx_diab_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_diab_tb_id" style="cursor:pointer;" class="fa <?php if($diabetes=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_diab_tb_id','spn_chbx_diab_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_diab_tb_id" style="display:<?php if($diabetes=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <label class="date_r">
                                                        Insulin Dependent
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                   
                                                    <input type="checkbox" onClick="checkSingle('chbx_subdiab_yes','chbx_subdiab');" <?php if($insulinDependence=="Yes") echo "Checked";  ?> name="chbx_subdiab" value="Yes" id="chbx_subdiab_yes" >
                                                </div>
                                             </div>
                                             <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <label class="date_r">
                                                        Non-Insulin Dependent
                                                    </label>
                                                    
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <input type="checkbox" onClick="checkSingle('chbx_subdiab_no','chbx_subdiab');" <?php if($insulinDependence=="No") echo "Checked";  ?> name="chbx_subdiab" value="No" id="chbx_subdiab_no" >
                                                </div>
                                             </div>   
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
                                                    Epilepsy, Convulsions, Parkinson's, Vertigo
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($epilepsy) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_epile_yes','chbx_epile'),disp(document.frm_health_ques.chbx_epile,'chbx_epile_tb_id'),changeChbxColor('chbx_epile')" <?php if($epilepsy=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_epile" id="chbx_epile_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($epilepsy) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_epile_no','chbx_epile'),disp_none(document.frm_health_ques.chbx_epile,'chbx_epile_tb_id'),changeChbxColor('chbx_epile')" <?php if(($getPreOpQuesDetails) && ($epilepsy=='No')) echo "CHECKED"; ?> value="No" name="chbx_epile" id="chbx_epile_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_epile_tb_id" style="cursor:pointer;" class="fa <?php if($epilepsy=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_epile,'chbx_epile_tb_id','spn_chbx_epile_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_epile_tb_id" style="display:<?php if($epilepsy=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="epilepsyDesc" name="epilepsyDesc" ><?php echo stripslashes($epilepsyDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Restless Leg Syndrome
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($restlessLegSyndrome) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_restless_yes','chbx_restless'),disp(document.frm_health_ques.chbx_restless,'chbx_restless_tb_id'),changeChbxColor('chbx_restless')" <?php if($restlessLegSyndrome=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_restless" id="chbx_restless_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($restlessLegSyndrome) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_restless_no','chbx_restless'),disp_none(document.frm_health_ques.chbx_restless,'chbx_restless_tb_id'),changeChbxColor('chbx_restless')" <?php if(($getPreOpQuesDetails) && ($restlessLegSyndrome=='No')) echo "CHECKED"; ?> value="No" name="chbx_restless" id="chbx_restless_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_restless_tb_id" style="cursor:pointer;" class="fa <?php if($restlessLegSyndrome=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_restless,'chbx_restless_tb_id','spn_chbx_restless_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_restless_tb_id" style="display:<?php if($restlessLegSyndrome=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="restlessLegSyndromeDesc" name="restlessLegSyndromeDesc" ><?php echo stripslashes($restlessLegSyndromeDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Hepatitis
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($hepatitis) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hepat_yes','chbx_hepat'),disp(document.frm_health_ques.chbx_hepat,'chbx_hepat_tb_id'),changeChbxColor('chbx_hepat')" <?php if($hepatitis=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hepat" id="chbx_hepat_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($hepatitis) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hepat_no','chbx_hepat'),disp_none(document.frm_health_ques.chbx_hepat,'chbx_hepat_tb_id'),changeChbxColor('chbx_hepat')" <?php if(($getPreOpQuesDetails) && ($hepatitis=='No')) echo "CHECKED"; ?> value="No" name="chbx_hepat" id="chbx_hepat_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_hepat_tb_id" style="cursor:pointer;" class="fa <?php if($hepatitis=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_hepat,'chbx_hepat_tb_id','spn_chbx_hepat_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_hepat_tb_id" style="display:<?php if($hepatitis=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-4 col-lg-12">
                                                    <label class="date_r">A</label><span style="padding-left:5px;padding-right:10px;"><input type="checkbox" name="HepatitisA" id="HepatitisA" value="true" <?php if($hepatitisA == 'true') echo "CHECKED"; ?>></span>
                                                    <label class="date_r">B</label><span style="padding-left:5px;padding-right:10px;"><input type="checkbox" name="HepatitisB" id="HepatitisB" value="true" <?php if($hepatitisB == 'true') echo "CHECKED"; ?>></span>
                                                    <label class="date_r">C</label><span style="padding-left:5px;padding-right:10px;"><input type="checkbox" name="HepatitisC" id="HepatitisC" value="true" <?php if($hepatitisC == 'true') echo "CHECKED"; ?>></span>
                                            	</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="hepatitisDesc" name="hepatitisDesc" ><?php echo stripslashes($hepatitisDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Kidney Disease, Dialysis
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($kidneyDisease) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_kidn_yes','chbx_kidn'),disp(document.frm_health_ques.chbx_kidn,'chbx_kidn_tb_id'),changeChbxColor('chbx_kidn')" <?php if($kidneyDisease=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_kidn" id="chbx_kidn_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($kidneyDisease) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_kidn_no','chbx_kidn'),disp_none(document.frm_health_ques.chbx_kidn,'chbx_kidn_tb_id'),changeChbxColor('chbx_kidn')" <?php if(($getPreOpQuesDetails) && ($kidneyDisease=='No')) echo "CHECKED"; ?> value="No" name="chbx_kidn" id="chbx_kidn_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_kidn_tb_id" style="cursor:pointer;" class="fa <?php if($kidneyDisease=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_kidn,'chbx_kidn_tb_id','spn_chbx_kidn_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_kidn_tb_id" style="display:<?php if($kidneyDisease=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <label class="date_r">
                                                        Do you have a Shunt
                                                    </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <input type="checkbox" <?php if($shunt=="Yes") echo "Checked";  ?> value="Yes" name="chbx_subkidnShunt" id="chbx_subkidn_yes" >
                                                </div>
                                             </div>
                                             <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <label class="date_r">
                                                        Fistula
                                                    </label>
                                                    
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                                    <input type="checkbox" <?php if($fistula=="Yes") echo "Checked";  ?> value="Yes" name="chbx_subkidnFistula" id="chbx_subkidn_no" >
                                                </div>
                                             </div> 
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
                                                    HIV, Autoimmune Diseases
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($hivAutoimmuneDiseases) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hiv_auto_yes','chbx_hiv_auto'),disp(document.frm_health_ques.chbx_hiv_auto,'chbx_hiv_auto_tb_id'),changeChbxColor('chbx_hiv_auto')" <?php if($hivAutoimmuneDiseases=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hiv_auto" id="chbx_hiv_auto_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($hivAutoimmuneDiseases) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hiv_auto_no','chbx_hiv_auto'),disp_none(document.frm_health_ques.chbx_hiv_auto,'chbx_hiv_auto_tb_id'),changeChbxColor('chbx_hiv_auto')" <?php if(($getPreOpQuesDetails) && ($hivAutoimmuneDiseases=='No')) echo "CHECKED"; ?> value="No" name="chbx_hiv_auto" id="chbx_hiv_auto_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_hiv_auto_tb_id" style="cursor:pointer;" class="fa <?php if($hivAutoimmuneDiseases=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_hiv_auto,'chbx_hiv_auto_tb_id','spn_chbx_hiv_auto_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_hiv_auto_tb_id" style="display:<?php if($hivAutoimmuneDiseases=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="hivTextArea" name="hivTextArea" ><?php echo stripslashes($hivTextArea); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    History of cancer
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($cancerHistory) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hist_can_yes','chbx_hist_can'),disp(document.frm_health_ques.chbx_hist_can,'chbx_hist_can_tb_id'),changeChbxColor('chbx_hist_can')" <?php if($cancerHistory=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hist_can" id="chbx_hist_can_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($cancerHistory) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hist_can_no','chbx_hist_can'),disp_none(document.frm_health_ques.chbx_hist_can,'chbx_hist_can_tb_id'),changeChbxColor('chbx_hist_can')" <?php if(($getPreOpQuesDetails) && ($cancerHistory=='No')) echo "CHECKED"; ?> value="No" name="chbx_hist_can" id="chbx_hist_can_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_hist_can_tb_id" style="cursor:pointer;" class="fa <?php if($cancerHistory=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_hist_can,'chbx_hist_can_tb_id','spn_chbx_hist_can_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div> 
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_hist_can_tb_id" style="display:<?php if($cancerHistory=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                            	<div class="col-md-12 col-sm-12 col-xs-4 col-lg-12">
                                                    <label class="date_r">Breast Cancer</label>
                                                    <span style="padding-left:10px;padding-right:10px;"><label class="date_r">Left</label><span style="padding-left:5px;"><input type="checkbox" onClick="javascript:checkSingle('chbx_leftbrest_can_yes','brestCancerLeft');" <?php if($brestCancerLeft == 'Yes') echo 'CHECKED'; ?> value="Yes" name="brestCancerLeft" id="chbx_leftbrest_can_yes" ></span></span>
                                                    <span style="padding-left:10px;padding-right:10px;"><label class="date_r">Right</label><span style="padding-left:5px;"><input type="checkbox" onClick="javascript:checkSingle('chbx_leftbrest_can_no','brestCancerLeft');" <?php if($brestCancerLeft == 'No') echo 'CHECKED'; ?> value="No" name="brestCancerLeft" id="chbx_leftbrest_can_no"></span></span>
                                            	</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="cancerHistory" name="cancerHistory" ><?php echo stripslashes($cancerHistoryDesc); ?></textarea>
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
                                                        <span class="colorChkBx" style=" <?php if($organTransplant) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_org_trns_yes','chbx_org_trns'),disp(document.frm_health_ques.chbx_org_trns,'chbx_org_trns_tb_id'),changeChbxColor('chbx_org_trns')" <?php if($organTransplant=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_org_trns" id="chbx_org_trns_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($organTransplant) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_org_trns_no','chbx_org_trns'),disp_none(document.frm_health_ques.chbx_org_trns,'chbx_org_trns_tb_id'),changeChbxColor('chbx_org_trns')" <?php if(($getPreOpQuesDetails) && ($organTransplant=='No')) echo "CHECKED"; ?> value="No" name="chbx_org_trns" id="chbx_org_trns_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_org_trns_tb_id" style="cursor:pointer;" class="fa <?php if($organTransplant=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_org_trns,'chbx_org_trns_tb_id','spn_chbx_org_trns_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_org_trns_tb_id" style="display:<?php if($organTransplant=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="organTransDesc" name="organTransDesc" ><?php echo stripslashes($organTransplantDesc); ?></textarea>
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
                                                        <span class="colorChkBx" style=" <?php if($anesthesiaBadReaction) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_bad_react_yes','chbx_bad_react'),disp(document.frm_health_ques.chbx_bad_react,'chbx_bad_react_tb_id'),changeChbxColor('chbx_bad_react')" <?php if($anesthesiaBadReaction=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_bad_react" id="chbx_bad_react_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($anesthesiaBadReaction) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_bad_react_no','chbx_bad_react'),disp_none(document.frm_health_ques.chbx_bad_react,'chbx_bad_react_tb_id'),changeChbxColor('chbx_bad_react')" <?php if(($getPreOpQuesDetails) && ($anesthesiaBadReaction=='No')) echo "CHECKED"; ?> value="No" name="chbx_bad_react" id="chbx_bad_react_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_bad_react_tb_id" style="cursor:pointer;" class="fa <?php if($anesthesiaBadReaction=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_bad_react,'chbx_bad_react_tb_id','spn_chbx_bad_react_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_bad_react_tb_id" style="display:<?php if($anesthesiaBadReaction=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="anesthesiaBadReactionDesc" name="anesthesiaBadReactionDesc" ><?php echo stripslashes($anesthesiaBadReactionDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     
                                   <div id="" class="inner_safety_wrap">
                                        <div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Other
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea class="form-control" style="resize:none;" id="otherTroubles" name="otherTroubles"><?php echo stripslashes($otherTroubles); ?></textarea>
                                                </div>
                                            </div>
                                        </div> 
                                     </div>
									<?php 
                                    //adminHealthquestionare
                                    $selectAdminQuestionsQry="select * from healthquestioner";
                                    $selectAdminQuestions=imw_query($selectAdminQuestionsQry);
                                    $selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
                                    $inc=0;
                                    while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
                                    {
										foreach($ResultselectAdminQuestions as $key=>$value){
											$question[$inc][$key]=stripslashes($value);
										}
                                    	$inc++;	
                                    }
                                    
                                    //echo $question[2]['question'];
                                    //End adminHealthquestionare
                                    $getQuesQry=imw_query("select * from healthquestionadmin where confirmation_id='$pConfId'");
                                    $k=0;
                                    $QuesnumRows=imw_num_rows($getQuesQry);
                                    while($getQuesRes=imw_fetch_array($getQuesQry)){
										foreach($getQuesRes as $key=>$val){
											$quest[$k][$key]=stripslashes($val);
										}
                                    	$k++;
                                    }
									if($QuesnumRows>0)
									{
										$t = 0;
										for($k=0;$k<ceil($QuesnumRows/2);$k++)
										{
											$i = $k;
											$quest[$k]['adminQuestion']; 
											$questionid[]=$quest[$k]['adminQuestionStatus'];
											$endTd = $endTd<= $QuesnumRows ? $t + 2 : $QuesnumRows;
											for($t=$t;$t<$endTd;$t++){
													
										?>
                                        		<?php if(!empty($quest[$t]['adminQuestion'])): ?>
                                                <div class="inner_safety_wrap">
                                                	<div class="row">
                                                    		<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                                <label class="date_r">
                                                                     <?php echo $quest[$t]['adminQuestion']; ?>
                                                                </label>
                                                            </div>
                                                        <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                            <div class="">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                    <span>
                                                                        <input type="checkbox" onClick="javascript:checkSingle('quest_yes<?php echo $t; ?>','quest<?php echo $t; ?>'),disp(document.frm_health_ques.quest<?php echo $t; ?>,'chbx_admin_quest_tb_id<?php echo $t; ?>');" name="quest<?php echo $t; ?>" value="Yes" id="quest_yes<?php echo $t; ?>" <?php if($quest[$t]['adminQuestionStatus']=="Yes"){ echo "CHECKED";} ?>  >
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                    <span>
                                                                        <input type="checkbox" onClick="javascript:checkSingle('quest_no<?php echo $t; ?>','quest<?php echo $t; ?>'),disp_none(document.frm_health_ques.quest<?php echo $t; ?>,'chbx_admin_quest_tb_id<?php echo $t; ?>')" name="quest<?php echo $t; ?>" value="No" id="quest_no<?php echo $t; ?>" <?php if($quest[$t]['adminQuestionStatus']=="No"){ echo "CHECKED";} ?> >
                                                                        <input name="questionDes<?php echo $t; ?>" type="hidden" value="<?php echo $quest[$t]['adminQuestion']; ?>">
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                    <span id="spn_chbx_admin_quest_tb_id<?php echo $t; ?>" style="cursor:pointer;" class="fa <?php if($quest[$t]['adminQuestionStatus']=="Yes") { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.quest<?php echo $t; ?>,'chbx_admin_quest_tb_id<?php echo $t; ?>','spn_chbx_admin_quest_tb_id<?php echo $t; ?>');"></span>
                                                                </div>
                                                                    		                               
                                                            </div> 
                                                        </div>
                                                    	
                                                    </div>	 
                                            	</div>
                                                <div class="inner_safety_wrap collapse" id="chbx_admin_quest_tb_id<?php echo $t; ?>" style="display:<?php if($quest[$t]['adminQuestionStatus']=="Yes") echo 'inline-block'; else echo 'none'; ?>; ">
                                                    <Div class="well">
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                                <label class="date_r">
                                                                    Describe
                                                                </label>
                                                            </div>
                                                            <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                                <textarea style="resize:none;" class="form-control" id="adminQuestDesc<?php echo $t; ?>" name="adminQuestDesc<?php echo $t; ?>" ><?php echo stripslashes($quest[$t]['adminQuestionDesc']); ?></textarea>
                                                            </div>
                                                        </div>
                                                    </Div> 
                                                 </div>
                                                <?php endif; ?>
                                        <?php
											}
										}
									}
									else if($QuesnumRows<1)
									{ 	$t=0;
										for($i=0;$i<ceil($selectAdminQuestionsRows/2);$i++)
										{
									
											$questionid[]=$question[$i]['healthQuestioner'];	
											$endTd = ($endTd<= $selectAdminQuestionsRows && $selectAdminQuestionsRows >1) ? $t + 2 : $selectAdminQuestionsRows;
											
											for($t=$t;$t<$endTd;$t++){
												
										?>
                                        		<?php if(!empty($question[$t]['question'])): ?>
                                                <div class="inner_safety_wrap">
                                                    <div class="row">
                                                        <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                            <label class="date_r">
                                                                 <?php echo $question[$t]['question']; ?>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                            <div class="">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                    <span>
                                                                        <input type="checkbox" onClick="javascript:checkSingle('question_yes<?php echo $t; ?>','question<?php echo $t; ?>'),disp(document.frm_health_ques.question<?php echo $t; ?>,'chbx_admin_quest_tb_id_new<?php echo $t; ?>');" name="question<?php echo $t; ?>" value="Yes" id="question_yes<?php echo $t; ?>">
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                    <span>
                                                                        <input type="checkbox" onClick="javascript:checkSingle('question_no<?php echo $t; ?>','question<?php echo $t; ?>'),disp_none(document.frm_health_ques.question<?php echo $t; ?>,'chbx_admin_quest_tb_id_new<?php echo $t; ?>')" name="question<?php echo $t; ?>" value="No" id="question_no<?php echo $t; ?>">
                                                                        <input name="questionDesc<?php echo $t; ?>" type="hidden" value="<?php echo $question[$t]['question']; ?>">
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                    <span id="spn_chbx_admin_quest_tb_id_new<?php echo $t; ?>" style="cursor:pointer;" class="fa  " onClick="javascript:disp_rev(document.frm_health_ques.question<?php echo $t; ?>,'chbx_admin_quest_tb_id_new<?php echo $t; ?>','spn_chbx_admin_quest_tb_id_new<?php echo $t; ?>');"></span>
                                                                </div>    		                               
                                                            </div> 
                                                        </div>
                                              		</div>	 
                                               	</div>
                                                <div class="inner_safety_wrap collapse" id="chbx_admin_quest_tb_id_new<?php echo $t; ?>" >
                                                    <Div class="well">
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                                <label class="date_r">
                                                                    Describe
                                                                </label>
                                                            </div>
                                                            <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                                <textarea style="resize:none;" class="form-control" id="adminQuestDescNew<?php echo $t; ?>" name="adminQuestDescNew<?php echo $t; ?>" ></textarea>
                                                            </div>
                                                        </div>
                                                    </Div> 
                                                 </div>
                                                 <?php endif; ?>
                                            <?php
											}
										} 
									}//end for
									?>					  
                                  </Div>
                           </div>
                      </div>          
                     </div> 
                </div>
                <?php
					$allergy1 = "Select * from patient_allergies_tbl where patient_confirmation_id=$pConfId";
					$result = imw_query($allergy1);
					$num = imw_num_rows($result);													
					?>
                <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                    <Div class="panel panel-default new_panel bg_panel_green">
                        <div class="panel-heading">
                            <a class="panel-title rob alle_link btn btn-default " data-placement="bottom"
                             id="allergies_drug" onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', {table_name:'hlthQstSpreadTableId'}, parseInt(findPos_X('allergies_drug')+8), parseInt(findPos_Y('allergies_drug')-135)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='iframe_health_quest';"> <span class="fa fa-caret-right"></span>  Allergies / Drug Reaction    </a>
 							
                            <div class="right_label">
                                	<label onClick="javascript:txt_enable_disable_frame1('iframe_health_quest','chbx_drug_react_no','Allergies_quest','Reaction_quest',{table_name:'hlthQstSpreadTableId'})"><input type="checkbox" <?php if($allergiesNKDA_patientconfirmation_status =='Yes'){ echo 'CHECKED'; } ?> value="Yes"  name="chbx_drug_react" id="chbx_drug_react_no" > NKA</label>
                                <label><input  type="checkbox" <?php if($allergies_status_reviewed=='Yes'){ echo 'CHECKED'; } ?>  value="Yes" name="chbx_drug_react_reviewed" id="chbx_drug_react_yes"> Allergies Reviewed </label>
                                
                            </div>
                        </div>
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
                                           <div class="table_slider" id="iframe_health_quest">          
												<?php  
                                                    $allgNameWidth=220;
                                                    $allgReactionWidth=220;
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
                        <div class="panel-heading col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4" style="color:#fff; top:4px;">
                            	<a id="prescription_medications_pre_op" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="bottom" onClick="return showPreDefineMedFn('medication_name', 'medication_detail', '10', parseInt(findPos_X('prescription_medications_pre_op')), parseInt(findPos_Y('prescription_medications_pre_op')-185)),document.getElementById('selected_frame_name_id').value='iframe_health_quest_medication';"> <span class="fa fa-caret-right"></span>  Take Prescription Medications    </a>	
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3" style="color:#fff; top:10px;">
                            	<label><input type="checkbox" name="no_medication_status" id="no_medication_status" value="Yes" onClick="clearMedVal(this)" <?php if($noMedicationStatus == "Yes") { echo "checked"; }?>>No Medications</label>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-5 col-lg-5">
                            	<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 " style="color:#fff; top:13px;"><label>Comments </label></div>	
                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8"><textarea name="no_medication_comments" id="no_medication_comments" class="form-control" style="font-family:verdana; border:1px solid #B9B9B9;  height:35px;  "><?php echo $noMedicationComments;?></textarea></div>	
                            </div>
                        </div>
                       <!-- <div id="listContent2" style="display:none;" class="">
                            <ul class="list-group">
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2 </a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2</a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2  </a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2 </a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2</a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2  </a></li>                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2 </a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2</a></li>
                              <li class="list-group-item"><a href="javascript:void(0)"> Sample 2  </a></li>                           </ul>
                        </div>-->
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
                                           <div class="table_slider">          
                                                 <div  class="table_slider" id="iframe_health_quest_medication">
													<?php  
                                                        include("patient_prescription_medi_healthquest_spreadsheet.php");
                                                    ?>
                                                </div>
                                             </div>                

                                          </div>
                                    </div>
                                    
                                </div>	 
                             </div>
                        </div>
                    </Div>
                    
                    <Div class="clearfix margin_adjustment_only"></Div>
                    
                    <div class="panel panel-default new_panel bg_panel_green" style="">
                       <div class="panel-heading"  style="">
                                    <h3 class="panel-title rob"> Do You  </h3>
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
                                                        <label class="li_check"> Yes </label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                      <label class="li_check"> No </label>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                       
                                                       &nbsp;
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Use a Wheel Chair, Walker or Cane
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($walker) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_use_wheel_yes','chbx_use_wheel'),disp(document.frm_health_ques.chbx_use_wheel,'chbx_use_wheel_tb_id'),changeChbxColor('chbx_use_wheel')" <?php if($walker=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_use_wheel" id="chbx_use_wheel_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($walker) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_use_wheel_no','chbx_use_wheel'),disp_none(document.frm_health_ques.chbx_use_wheel,'chbx_use_wheel_tb_id'),changeChbxColor('chbx_use_wheel')" <?php if(($getPreOpQuesDetails) && ($walker=='No')) echo "CHECKED"; ?> value="No" name="chbx_use_wheel" id="chbx_use_wheel_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_use_wheel_tb_id" style="cursor:pointer;" class="fa <?php if($walker=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_use_wheel,'chbx_use_wheel_tb_id','spn_chbx_use_wheel_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_use_wheel_tb_id" style="display:<?php if($walker=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="walkerDesc" name="walkerDesc" ><?php echo stripslashes($walkerDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Wear Contact lenses
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($contactLenses) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_wear_cont_yes','chbx_wear_cont'),disp(document.frm_health_ques.chbx_wear_cont,'chbx_wear_cont_tb_id'),changeChbxColor('chbx_wear_cont')" <?php if($contactLenses=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_wear_cont" id="chbx_wear_cont_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($contactLenses) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_wear_cont_no','chbx_wear_cont'),disp_none(document.frm_health_ques.chbx_wear_cont,'chbx_wear_cont_tb_id'),changeChbxColor('chbx_wear_cont')" <?php if(($getPreOpQuesDetails) && ($contactLenses=='No')) echo "CHECKED"; ?> value="No" name="chbx_wear_cont" id="chbx_wear_cont_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_wear_cont_tb_id" style="cursor:pointer;" class="fa <?php if($contactLenses=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_wear_cont,'chbx_wear_cont_tb_id','spn_chbx_wear_cont_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_wear_cont_tb_id" style="display:<?php if($contactLenses=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="contactLensesDesc" name="contactLensesDesc" ><?php echo stripslashes($contactLensesDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>

                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Smoke
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($smoke) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_smoke_yes','chbx_smoke'),disp(document.frm_health_ques.chbx_smoke,'chbx_smoke_tb_id'),changeChbxColor('chbx_smoke')" <?php if($smoke=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_smoke" id="chbx_smoke_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($smoke) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_smoke_no','chbx_smoke'),disp_none(document.frm_health_ques.chbx_smoke,'chbx_smoke_tb_id'),changeChbxColor('chbx_smoke')" <?php if(($getPreOpQuesDetails) && ($smoke=='No')) echo "CHECKED"; ?> value="No" name="chbx_smoke" id="chbx_smoke_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_smoke_tb_id" style="cursor:pointer;" class="fa <?php if($smoke=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_smoke,'chbx_smoke_tb_id','spn_chbx_smoke_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_smoke_tb_id" style="display:<?php if($smoke=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        How much
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="smokeHowMuch" name="smokeHowMuch" ><?php echo stripslashes($smokeHowMuch); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    <label class="date_r" for="smokeAdvise">
                                                        Patient advised not to smoke 24 H prior to surgery <input type="checkbox" name="smokeAdvise" id="smokeAdvise" value="Yes" <?php if($smokeAdvise == 'Yes') echo "CHECKED"; ?>>
                                                    </label>
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
                                                        <span class="colorChkBx" style=" <?php if($alchohol) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_drink_yes','chbx_drink'),disp(document.frm_health_ques.chbx_drink,'chbx_drink_tb_id'),changeChbxColor('chbx_drink')" <?php if($alchohol=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_drink" id="chbx_drink_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($alchohol) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_drink_no','chbx_drink'),disp_none(document.frm_health_ques.chbx_drink,'chbx_drink_tb_id'),changeChbxColor('chbx_drink')" <?php if(($getPreOpQuesDetails) && ($alchohol=='No')) echo "CHECKED"; ?> value="No" name="chbx_drink" id="chbx_drink_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_drink_tb_id" style="cursor:pointer;" class="fa <?php if($alchohol=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_drink,'chbx_drink_tb_id','spn_chbx_drink_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_drink_tb_id" style="display:<?php if($alchohol=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        How much
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="alchoholHowMuch" name="alchoholHowMuch" ><?php echo stripslashes($alchoholHowMuch); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    <label class="date_r" for="alchoholAdvise">
                                                        Patient advised not to drink 24 H prior to surgery <input type="checkbox" name="alchoholAdvise" id="alchoholAdvise" value="Yes" <?php if($alchoholAdvise == 'Yes') echo "CHECKED"; ?>>
                                                    </label>
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
                                                        <span class="colorChkBx" style=" <?php if($autoInternalDefibrillator) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hav_auto_int_yes','chbx_hav_auto_int'),disp(document.frm_health_ques.chbx_hav_auto_int,'chbx_hav_auto_int_tb_id'),changeChbxColor('chbx_hav_auto_int')" <?php if($autoInternalDefibrillator=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hav_auto_int" id="chbx_hav_auto_int_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($autoInternalDefibrillator) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hav_auto_int_no','chbx_hav_auto_int'),disp_none(document.frm_health_ques.chbx_hav_auto_int,'chbx_hav_auto_int_tb_id'),changeChbxColor('chbx_hav_auto_int')" <?php if(($getPreOpQuesDetails) && ($autoInternalDefibrillator=='No')) echo "CHECKED"; ?> value="No" name="chbx_hav_auto_int" id="chbx_hav_auto_int_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_hav_auto_int_tb_id" style="cursor:pointer;" class="fa <?php if($autoInternalDefibrillator=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_hav_auto_int,'chbx_hav_auto_int_tb_id','spn_chbx_hav_auto_int_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_hav_auto_int_tb_id" style="display:<?php if($autoInternalDefibrillator=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Describe
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="autoInternalDefibrillatorDesc" name="autoInternalDefibrillatorDesc" ><?php echo stripslashes($autoInternalDefibrillatorDesc); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>

                                     <div class="inner_safety_wrap">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                <label class="date_r">
                                                    Have any Metal Prosthetics
                                                </label>
                                            </div>
                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                <div class="">
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($metalProsthetics) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hav_any_met_yes','chbx_hav_any_met'),disp(document.frm_health_ques.chbx_hav_any_met,'chbx_hav_any_met_tb_id'),changeChbxColor('chbx_hav_any_met')" <?php if($metalProsthetics=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hav_any_met" id="chbx_hav_any_met_yes">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span class="colorChkBx" style=" <?php if($metalProsthetics) { echo $whiteBckGroundColor;}?>" >
                                                            <input type="checkbox" onClick="javascript:checkSingle('chbx_hav_any_met_no','chbx_hav_any_met'),disp_none(document.frm_health_ques.chbx_hav_any_met,'chbx_hav_any_met_tb_id'),changeChbxColor('chbx_hav_any_met')" <?php if(($getPreOpQuesDetails) && ($metalProsthetics=='No')) echo "CHECKED"; ?> value="No" name="chbx_hav_any_met" id="chbx_hav_any_met_no">
                                                        </span>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                        <span id="spn_chbx_hav_any_met_tb_id" style="cursor:pointer;" class="fa <?php if($metalProsthetics=='Yes') { echo 'fa-angle-double-up';}else { echo 'fa-angle-double-down';}?> " onClick="javascript:disp_rev(document.frm_health_ques.chbx_hav_any_met,'chbx_hav_any_met_tb_id','spn_chbx_hav_any_met_tb_id');"></span>
                                                    </div>    		                               
                                                </div> 
                                            </div>
                                        </div>	 
                                     </div>
                                     
                                     <div class="inner_safety_wrap collapse" id="chbx_hav_any_met_tb_id" style="display:<?php if($metalProsthetics=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                        <Div class="well">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                    <label class="date_r">
                                                        Notes
                                                    </label>
                                                </div>
                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
                                                    <textarea style="resize:none;" class="form-control" id="notesDesc" name="notesDesc" ><?php echo stripslashes($notes); ?></textarea>
                                                </div>
                                            </div>
                                        </Div> 
                                     </div>
                                  </div>
                           </div>
                      </div>          
                     </div>
                    
                        
                 </div>

            <div class="clearfix border-dashed margin_adjustment_only"></div>
            <Div class="clearfix margin_adjustment_only"></Div>                                        
            <div class="form_outer"> 
                 <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="form_reg">
                            <div class="row">
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <label for="contact" class="text-left"> 
                                               Emergency Contact Person    
                                        </label>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <input type="text" class="form-control" name="emergencyContactPerson" id="emergencyContactPerson" onFocus="changeTxtGroupColor(1,'emergencyContactPerson');" onKeyUp="changeTxtGroupColor(1,'emergencyContactPerson');" value="<?php echo $emergencyContactPerson; ?>"  style=" <?php if(trim(!$emergencyContactPerson)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>">
                                    </div>
                            </div>
                        </div>
                 </div>
                 <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="form_reg">
                            <div class="row">
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <label for="contact" class="text-left"> 
                                              Telephone   
                                        </label>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <input type="text" class="form-control"   name="emergencyContactTel" id="emergencyContactTelId" onFocus="changeTxtGroupColor(1,'emergencyContactTelId');" onKeyUp="changeTxtGroupColor(1,'emergencyContactTelId');" value="<?php echo $emergencyContactPhone; ?>" maxlength="12" onBlur="ValidatePhone(this);if(!this.value){this.style.backgroundColor='#F6C67A' }" style=" <?php if(trim(!$emergencyContactPhone)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>">
                                    </div>
                            </div>
                        </div>
                 </div>
                 <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="form_reg">
                            <div class="row">
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <label for="contact" class="text-left"> 
                                               Witness Name
                                        </label>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <input type="text" class="form-control" name="witnessname" id="witnessnameId" onFocus="changeTxtGroupColor(1,'witnessnameId');" onKeyUp="changeTxtGroupColor(1,'witnessnameId');" value="<?php echo $witnessname; ?>" maxlength="15"  style=" <?php if(trim(!$witnessname)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>">
                                    </div>
                            </div>
                        </div>
                 </div>
            </div>
            <Div class="clearfix margin_adjustment_only"></Div> 
            <div class="clearfix border-dashed margin_adjustment_only"></div>
           
            <div class=" col-lg-4 col-md-4 col-sm-6 col-xs-12">
                 <div class="form_reg">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
                            <label for="sign" class=""> 
                               Patient Signature
                            </label>
                        </div>
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                          <div class="clearfix margin_adjustment_only"></div>
                            <div class="img_sign_wrap media">
                                <input type="hidden" name="SigDataPt" id="SigDataPt">
                                <table style="border:none; padding:2px;" id="tbl_sign_pt_health_id">
                                    <tr class="valignTop">
                                        <td style="width:2px;">&nbsp;</td>
                                        <td class="valignTop">
                                            <?php
                                            if($patient_sign_image_path) {
                                            ?>
                                            	<img alt="" src="<?php echo $patient_sign_image_path;?>" style="border:none; width:150px; height:65px;">
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <?php
                                        if(!$patient_sign_image_path) { 
                                            if($browserPlatform == "iPad") {?>
                                                <td class="valignTop consentObjectBeforSign" id="tdObjectIpadPreHlthPtSign" style="display:inline-block;" >
                                                    <div style="width:150px; height:65px;">
                                                        <img alt="" style="cursor:pointer; float:right; margin-top:50px;" src="images/pen.png" id="physicianSigPen" name="physicianSigPen" onClick="OnSignIpadPhy('<?php echo $patient_id; ?>','<?php echo $pConfId; ?>','ptHealth','tbl_sign_pt_health_id','1');">
                                                    </div>
                                                </td>    
                                        <?php		
                                            }else {?>
                                                <td class="valignTop consentObjectBeforSign" id="tdObjectSigPlusPreHlthPtSign" style="display:inline-block;">
                                                    <OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height="95"
                                                        id="SigPlusPreHlthPtSign" name="SigPlusPreHlthPtSign"
                                                        style="HEIGHT: 95px; WIDTH: 150px; LEFT: 0px; TOP: 0px;" 
                                                        VIEWASTEXT>
                                                        <PARAM NAME="_Version" VALUE="131095">
                                                        <PARAM NAME="_ExtentX" VALUE="4842">
                                                        <PARAM NAME="_ExtentY" VALUE="1323">
                                                        <PARAM NAME="_StockProps" VALUE="0">
                                                    </OBJECT>
                                                </td>
                                                <td class="valignBottom" id="Sign_icon_PtHealth" style="display:inline-block;">
                                                    <img alt="" style="cursor:pointer;" src="images/pen.png" id="SignBtnPtHealth" onClick="OnSignPtHealth();"><br>
                                                    <img title="Touch Signature" style="display:inline-block;position:relative;cursor:pointer;width:37px ; height:30px;" src="images/touch.svg" id="SignBtnTouchPtHealth" name="SignBtnTouchPtHealth" onclick="OnSignIpadPhy('<?php echo $patient_id;?>','<?php echo $pConfId;?>','ptHealth','tbl_sign_pt_health_id','1')"><br> 
                                                    <img style="cursor:pointer;" src="images/eraser.gif" id="clearSignPtHealth" alt="Clear Sign" onClick="OnClearPtHealth();">
                                                </td>	
                                        <?php
                                            }
                                        }?>	
                                        
                                        							
                                    </tr>
                                </table>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
           		<textarea name="SigDataPtLoadValue" id="SigDataPtLoadValue" style=" visibility:hidden; width:10px; height:2px;"><?php echo $patientSign;?></textarea>
           </div>
           
           <div class="col-md-4 col-sm-6 col-lg-4 col-xs-12">
                <div class="form_reg">
                   <div class="row">
                      <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        <label class="" for="date1">
                           Date		
                        </label>
                      </div>
                      <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                            <div class="input-group datepickerTxt">
                                <input type="text" class="form-control datepickerTxt" id="date1" name="date" value="<?php if($objManageData->changeDateMDY($dateQuestionnaire)!='00-00-0000'){echo $objManageData->changeDateMDY($dateQuestionnaire);}else{echo date('m-d-Y');} ?>" maxlength=10>
                                <div class="input-group-addon datepicker">
                                    <a href="javascript:void(0)"><span class="glyphicon glyphicon-calendar"></span></a>
                                </div>
                            </div>
                      </div>
                  </div>     
                </div>
           </div>
           <Div class="clearfix margin_adjustment_only visible-sm"></Div>
           <div class="clearfix border-dashed margin_adjustment_only visible-sm"></div>
           <Div class="col-sm-4 visible-sm"></Div>

            <div class="col-md-4 col-sm-8 col-lg-4 col-xs-12">
				<?php
                if($signWitness1Id) {
                    $ViewWitnessUserNameQry = "select * from `users` where  usersId = '".$signWitness1Id."'";
                    $ViewWitnessUserNameRes = imw_query($ViewWitnessUserNameQry) or die(imw_error()); 
                    $ViewWitnessUserNameRow = imw_fetch_array($ViewWitnessUserNameRes); 
                    $witnessUserType 		= $ViewWitnessUserNameRow["user_type"];
                }
                //END GET USER DETAIL(FOR WITNESS SIGNATURE)
                
                //CODE RELATED TO NURSE SIGNATURE ON FILE
                $loginUserName = $_SESSION['loginUserName'];
                $loginUserId = $_SESSION["loginUserId"];
                if($witnessUserType && ($loggedInUserType<>$witnessUserType)) {
                    $callJavaFunWitness = "return noAuthorityFunCommon('".$witnessUserType."');";
                }else {
                    $callJavaFunWitness = "document.frm_health_ques.hiddSignatureId.value='TDwitness1SignatureId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Witness1');";
                }
                
                $signOnFileWitness1Status = "Yes";
                $TDwitness1NameIdDisplay = "inline-block";
                $TDwitness1SignatureIdDisplay = "none";
                $Witness1NameShow = $loggedInUserName;
                $signWitness1DateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if($signWitness1Id<>0 && $signWitness1Id<>"") {
                    $Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
                    $signOnFileWitness1Status = $signWitness1Status;	
                    $TDwitness1NameIdDisplay = "none";
                    $TDwitness1SignatureIdDisplay = "inline-block";
                    //$signWitness1DateTimeFormatNew = $signWitness1DateTimeFormat;
					$signWitness1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signWitness1DateTime);
                }
                if($_SESSION["loginUserId"]==$signWitness1Id) {
                    $callJavaFunWitnessDel = "document.frm_health_ques.hiddSignatureId.value='TDwitness1NameId'; return displaySignature('TDwitness1NameId','TDwitness1SignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Witness1','delSign');";
                }else {
                    $callJavaFunWitnessDel = "alert('Only $Witness1NameShow can remove this signature');";
                }
                //END CODE RELATED TO NURSE SIGNATURE ON FILE 
    
                if($signWitness1Id || !trim($witness_sign_image_path)) {?>
                
                    <div class="inner_safety_wrap" id="TDwitness1NameId" style="display:<?php echo $TDwitness1NameIdDisplay;?>;">
                    	<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitness;?>"> Witness Signature </a>
                    </div>
                    <div class="inner_safety_wrap collapse" id="TDwitness1SignatureId" style="display:<?php echo $TDwitness1SignatureIdDisplay;?>;">
                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"> <?php echo "<b>Witness:</b>"." ".$Witness1NameShow; ?>  </a></span>	     
                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileWitness1Status;?></span>
                        <span class="rob full_width"> <b> Signature Date</b> <?php echo $signWitness1DateTimeFormatNew;?></span>
                    </div>
                    <input type="hidden" name="hidd_signWitness1Activate" value="yes">	
				<?php
                }else if($witness_sign_image_path) {
				?>
					<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
						<label for="witness_sign" class=""> 
							Witness Signature
						</label>
					</div>
					<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
						<img alt="" src="<?php echo $witness_sign_image_path;?>" style="width:150px; height:65px;">
					</div>
				<?php
				}?>
            </div>
          <Div class="clearfix margin_adjustment_only"></Div>
      </div>
    </form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	<form name="frm_return_BlankMainForm" method="post" action="pre_op_health_quest.php?cancelRecord=true<?php echo $saveLink;?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_op_health_quest.php";
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
?>

</body>
</html>