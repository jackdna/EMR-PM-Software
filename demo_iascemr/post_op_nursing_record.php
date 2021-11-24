<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 6;
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<title>Surgerycenter EMR</title>
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
$tablename = "postopnursingrecord";
include_once("common/commonFunctions.php"); 
$spec = "
</head>
<body onLoad=\"top.changeColor('".$bgcolor_post_op_nursing_order."');\" onClick=\"closeEpost(); return  top.frames[0].main_frmInner.hideSliders();\">
";
include("common/link_new_file.php");
//START INCLUDE PREDEFINE FUNCTIONS
	include_once("common/post_site.php");  //POST OP  NURSING
	include_once("common/nourishment_kind.php"); //POST OP  NURSING
	include_once("common/recovery_comments_pop.php"); //POST OP  NURSING
//END INCLUDE PREDEFINE FUNCTIONS

include_once("admin/classObjectFunction.php"); 
$objManageData = new manageData;

$operative_timename=$objManageData->setTmFormat($_GET['postOpSiteTime']);
$heparinLockOutTime=$objManageData->setTmFormat($_GET['heparinLockOutTime']);
$heparinLockOutNA = $_REQUEST['heparinLockOutNA'];
extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];

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
	$fieldName = "post_op_nursing_form";
	$pageName = "post_op_nursing_record.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON THEN SHIFT SLIDER LINK FROM RIGHT TO LEFT
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;ascId='.$ascId;

// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$nurseId = $detailConfirmation->nurseId;
// GETTING CONFIRMATION DETAILS

//GETTING NURSE PROFILE 
	$nurseIDProfile = trim($nurseId);
	if(($nurseIProfile=="" || $nurseIDProfile==0) && $_SESSION['loginUserType']=="Nurse") {
		$nurseIDProfile = $_SESSION['loginUserId'];	
	}


// GETTING NURSE SIGN OR NOT
	unset($conditionArr);
	$conditionArr['usersId'] = $nurseId;
	$nurseDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($nurseDetails) {
		foreach($nurseDetails as $nurse){
			$signatureOfNurse = $nurse->signature;
		}
	}	
// GETTING NURSE SIGN OR NOT


//SAVE RECORD TO DATABASE
if($_POST['SaveRecordForm']=='yes'){
	
	// Code Start Here to save physician orders
	$postOpDropExist = "Yes"; //CHART SHOULD NOT BE CHECKED FOR form_status IF NO MEDICATION EXISTS
	$physicianOrderRecordIdArr	=	$_REQUEST['physicianOrderRecordId'];
	$physicianOrderNameArr	=	$_REQUEST['physicianOrderName'];
	$physicianOrderTimeArr	=	$_REQUEST['physicianOrderTime'];
	$chbxNotGivenArr	=	$_REQUEST['chbxNotGiven'];
	/*
	if(count($physicianOrderNameArr) > 0) {
		foreach($physicianOrderNameArr as $phyOrdNme) {
			if(trim($phyOrdNme)) {
				$postOpDropExist = "Yes";			
			}
		}
	}*/
	
	if(is_array($physicianOrderRecordIdArr) && count($physicianOrderRecordIdArr) > 0 )
	{
		
		foreach($physicianOrderRecordIdArr as $key=>$recordId)
		{
				$orderName	=	$physicianOrderNameArr[$key];
				$orderTime	=	$physicianOrderTimeArr[$key];		
				$orderNotGiven	=	$chbxNotGivenArr[$key];
				//echo $orderName .'--' .$orderTime . '<br>';
				if(trim($orderName))
				{
					if($orderTime)
					{
						//$orderTime = date("H:i:s", strtotime($orderTime));
						$orderTime = $objManageData->setTmFormat($orderTime);
					}
					else
					{
						if($orderNotGiven <> 'Yes')
						{
							$orderTime	= '00:00:00';
							$postOpDropExist 	= "";	
						}
					}
					
					if($orderNotGiven == 'Yes'){
						$orderTime	=	'';		
					}
					
					$dataArray	=	array();
					$dataArray['confirmation_id']		=	$_REQUEST["pConfId"] ;
					$dataArray['chartName']				=	'post_op_physician_order_form';
					$dataArray['physician_order_name']	=	trim($orderName);
					$dataArray['physician_order_time']	=	$orderTime;
					$dataArray['physician_order_not_given']	=	$orderNotGiven;
					//print_r($dataArray);
					if($recordId)
					{
						$objManageData->UpdateRecord($dataArray,'patient_physician_orders','recordId',$recordId);
					}
					else
					{
						$dataChkArray = $dataArray;
						unset($dataChkArray['physician_order_time']);
						unset($dataChkArray['physician_order_not_given']);
						
						$chkRecords	=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$dataChkArray);
						if(!$chkRecords)  {
							$dataArray['physician_order_location']		= 'post_op_nursing_record';
							$dataArray['physician_order_date_time']		= date("Y-m-d H:i:s");
							$dataArray['physician_order_type']			= 'medication';
							$objManageData->addRecords($dataArray,'patient_physician_orders');	
						}else{
							$objManageData->UpdateRecord($dataArray,'patient_physician_orders','recordId',$chkRecords[0]->recordId);	
						}
					}
				}
				else
				{
					if($recordId)
					{
						$objManageData->DeleteRecord('patient_physician_orders','recordId',$recordId);	
					}
				}
			
		}
	}
	// Code End Here to save physician orders 



	//START CODE TO SAVE CHECKLIST POST OP NURSE
	$chkPostOpNurseQry = "Select * from `postopnursingrecord`  Where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPostOpNurseRes = imw_query($chkPostOpNurseQry) or die(imw_error()); 
	$chkPostOpNurseNumRow = imw_num_rows($chkPostOpNurseRes);
	if($chkPostOpNurseNumRow>0) {
		//CODE START TO CHECK FORM STATUS
			$chkPostOpNurseFormStatusRow = imw_fetch_array($chkPostOpNurseRes);
			$chkPostOpNurseFormStatus = $chkPostOpNurseFormStatusRow['form_status'];
			$chkPostOpNurseVersionNum = $chkPostOpNurseFormStatusRow['version_num'];
			$chkPostOpNurseVersionDate= $chkPostOpNurseFormStatusRow['version_date_time'];
		//CODE START TO CHECK FORM STATUS
	}
	
	$versionNumQry = "";
	$version_num	=	$chkPostOpNurseVersionNum;
	if(!$chkPostOpNurseVersionNum)
	{
		$version_date_time	=	$chkPostOpNurseVersionDate;
		if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
		{
			$version_date_time	=	date('Y-m-d H:i:s');
		}
				
		if($chkPostOpNurseFormStatus == 'completed' || $chkPostOpNurseFormStatus=='not completed'){
			$version_num = 1;
		}else{
			$version_num	=	$current_form_version;
		}
		
		$versionNumQry .= ", version_num =	'".$version_num."', version_date_time	=	'".$version_date_time."' ";
	}
	
	$other_mental_status = '';
	if($version_num > 2) {
		$other_mental_status = trim($_REQUEST['other_mental_status']);
		$versionNumQry .= ", other_mental_status =	'".addslashes($other_mental_status)."' ";	
	}
	
	$postopNurseCheckListComplete			= "yes";
	$postopNurseCheckListSaveQry			= "SELECT postopNurseChecklistId AS checkListTemplateId, name AS postOpNurseQuestionName FROM postop_nurse_checklist ORDER BY  postopNurseChecklistId ";
	
	$saveCheckListQry 						= " INSERT INTO ";
	$saveCheckListWhrQry 					= "";
	if($chkPostOpNurseFormStatus=='completed' || $chkPostOpNurseFormStatus=='not completed') {	
		$postopNurseCheckListSaveQry		= "SELECT checkListTemplateId, postOpNurseQuestionName, postOpNurseOption FROM patient_postop_nurse_checklist WHERE confirmation_id = '".$_REQUEST["pConfId"]."' ORDER BY id ";
		$saveCheckListQry 					= " UPDATE ";
	}
	$postopNurseCheckListSaveRes 			= imw_query($postopNurseCheckListSaveQry) or die(imw_error());
	$postopNurseCheckListSaveNumRow 		= imw_num_rows($postopNurseCheckListSaveRes);
	if($postopNurseCheckListSaveNumRow>0) {
		while($postopNurseCheckListSaveRow	= imw_fetch_array($postopNurseCheckListSaveRes)) {
			$postopNurseCheckListTemplateId = $postopNurseCheckListSaveRow['checkListTemplateId'];
			$postopNurseCheckListSaveName 	= $postopNurseCheckListSaveRow['postOpNurseQuestionName'];
			if(trim($saveCheckListQry)=="UPDATE") {
				$saveCheckListWhrQry 		= " WHERE confirmation_id 	= '".$_REQUEST["pConfId"]."' AND checkListTemplateId = '".$postopNurseCheckListTemplateId."' ";
			}
			if(trim($_REQUEST['nrs_chklist_'.$postopNurseCheckListTemplateId]) == "") {
				$postopNurseCheckListComplete = "no";
			}
			
			$insPostOpNurseChecklistQry 	= $saveCheckListQry." patient_postop_nurse_checklist SET 
											   postOpNurseQuestionName	= '".$postopNurseCheckListSaveName."',
											   postOpNurseOption		= '".$_REQUEST['nrs_chklist_'.$postopNurseCheckListTemplateId]."',
											   confirmation_id			= '".$_REQUEST["pConfId"]."',
											   patient_id				= '".$patient_id."',
											   checkListTemplateId		= '".$postopNurseCheckListTemplateId."'
											   ".$saveCheckListWhrQry;
			$inspostOpNurseChecklistRes 	= imw_query($insPostOpNurseChecklistQry) or die($insPostOpNurseChecklistQry . imw_error());						 
		}
	}
	//END CODE TO SAVE CHECKLIST POST OP NURSE
	
	$text = $_REQUEST['getText'];
	$hidd_vitalSignBp=$_POST['vitalSignBP_main'];
	$hidd_vitalSignP=$_POST['vitalSignP_main'];
	$hidd_vitalSignR=$_POST['vitalSignR_main'];
	$hidd_vitalSignO2SAT=$_POST['vitalSignO2SAT_main'];
	$hidd_vitalSignTime1=$_POST['vitalSignTime_main'];
	$hidd_vitalSignTemp=$_POST['vitalSignTemp_main'];
	///

	//Start Saving Post Op Nursing Quality Measure questions
	
	$selectQry	=	imw_query("Select * From qualitymeasures Where confirmation_id='".$pConfId."'");
	$count		=	imw_num_rows($selectQry);
	
	if($count>0)
	{
		$k=0;
		while($selectRes=imw_fetch_array($selectQry))
		{
			$k++;
			$qualityBox			=	$_REQUEST['qtyBox'.$k];			
			$qualityQuestion	=	$_REQUEST['qualityDes'.$k];
			$updateQry			=	imw_query("Update qualitymeasures Set
													qualityStatus='".$qualityBox."'
													Where
													qualityName='".$qualityQuestion."' And
													confirmation_id='".$pConfId."' And
													patient_id= '".$patient_id."' "
												);	
			
		}	
	}
	else
	{	//insertion in qualityMeasure
		
		$selectAdminQuestionsQry	=	"Select * From qualitymeasuresadmin Where status='Active'";
		$selectAdminQuestions		=	imw_query($selectAdminQuestionsQry) or die(imw_error());
		$selectAdminQuestionsRows	=	imw_num_rows($selectAdminQuestions);
		
		$c = 0;
		while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
		{
			$c++;				
			$qualityBox		= $_REQUEST['qtyBox'.$c];			
			$adminQuestion	= $_REQUEST['qualityDes'.$c];
			$insertQuestionQry	=	imw_query("Insert Into qualitymeasures Set 
															qualityName='".$adminQuestion."',
															qualityStatus='".$qualityBox."',
															confirmation_id='".$pConfId."', 
															patient_id= '".$patient_id."'
												");
		}//End insertion in Quality Measure in post op nurse
	}
	//End Saving Post Op Nursing Quality Measure questions

	//vitalSignTime saved in database
	 
	    /*
		   $time_splitvitalSign = explode(" ",$hidd_vitalSignTime1);
	       
		if($time_splitvitalSign[1]=="PM" || $time_splitvitalSign[1]=="pm") {
			
			$time_splitvitalSigntime = explode(":",$time_splitvitalSign[0]);
			$hidd_vitalSignTimeIncr=$time_splitvitalSigntime[0]+12;
			$hidd_vitalSignTime = $hidd_vitalSignTimeIncr.":".$time_splitvitalSigntime[1].":00";
			
		}elseif($time_splitvitalSign[1]=="AM" || $time_splitvitalSign[1]=="am") {
		    $time_splitvitalSigntime = explode(":",$time_splitvitalSign[0]);
			$hidd_vitalSignTime=$time_splitvitalSigntime[0].":".$time_splitvitalSigntime[1].":00";
		}
		*/
		$hidd_vitalSignTime = $objManageData->setTmFormat($hidd_vitalSignTime1);
		if(!$hidd_vitalSignTime) { $hidd_vitalSignTime=date('H:i:s'); }
	//vitalSignTime saved in database
		
		$SaveVitalSignQry = "insert into `vitalsign_tbl` set 
										vitalSignBp = '$hidd_vitalSignBp',
										vitalSignP = '$hidd_vitalSignP', 
										vitalSignR = '$hidd_vitalSignR',
										vitalSignO2SAT = '$hidd_vitalSignO2SAT',
										vitalSignTime = '$hidd_vitalSignTime',
										vitalSignTemp = '$hidd_vitalSignTemp',										
										confirmation_id='".$_REQUEST["pConfId"]."',
										patient_id = '".$_REQUEST["patient_id"]."'";
		if($hidd_vitalSignBp<>"" || $hidd_vitalSignP<>"" || $hidd_vitalSignR<>"" || $hidd_vitalSignO2SAT<>"") {								
			$SaveVitalSignRes = imw_query($SaveVitalSignQry) or die(imw_error());
		}	
	
	$painLevel = $_POST["painLevel"];
	$postOpSite = addslashes($_POST["pos_op_site_area"]);
	$postOpSiteTime = $objManageData->setTmFormat($_POST["postOpSiteTime"]);
	$nourishKind = addslashes($_POST["nour_kind_area"]);
	$bs_na = $_POST['chkBoxNS'];
	$bs_value = ($bs_na) ? '' : $_POST['bsvalue'];
 	$removedIntact = $_POST["chbx_removedIntact"];
	$heparinLockOutTime = $objManageData->setTmFormat($_POST["heparinLockOutTime"]);
	$heparinLockOutNA = $_POST['heparinLockOutNA'];
	$patient_aox3 = $_POST["chbx_aox3"];
	$recoveryComments = addslashes(trim($_POST["recv_comm_area"]));
	$relivedNurseId = $_POST["relivedNurseIdList"];
	$patientReleased2Adult = $_POST["chbx_patientReleased2Adult"];
	
	$patientsRelation = $_POST["patientsRelationList"];
	if($patientReleased2Adult<>"Yes") {
		$patientsRelation = "";
	}
	$patientsRelationOther = $_POST["txt_patientsRelationOther"];
	if($patientsRelation<>"other") {
		$patientsRelationOther = "";
	}
	$patient_transfer = $_POST["chbx_patient_transfer"];
	
	$dischargeTime = $objManageData->setTmFormat($_POST['dischargeTime'],'static');
	$nurseInitials = $_POST["nurseInitials"];
	$chkPostopnursingQry = "select * from `postopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPostopnursingRes = imw_query($chkPostopnursingQry) or die(imw_error()); 
	$chkPostopnursingNumRow = imw_num_rows($chkPostopnursingRes);
		
		//POSTOPSITETIME saved in database
	       /*
		   $time_splitpostop = explode(" ",$postOpSiteTime);
			if($time_splitpostop[1]=="PM" || $time_splitpostop[1]=="pm") {
				$time_splitpostoptime = explode(":",$time_splitpostop[0]);
				$postOpSiteTimeIncr=$time_splitpostoptime[0]+12;
				$postOpSiteTime = $postOpSiteTimeIncr.":".$time_splitpostoptime[1].":00";
			}elseif($time_splitpostop[1]=="AM" || $time_splitpostop[1]=="am") {
				$time_splitpostoptime = explode(":",$time_splitpostop[0]);
				$postOpSiteTime=$time_splitpostoptime[0].":".$time_splitpostoptime[1].":00";
				if($time_splitpostoptime[0]=="00" && $time_splitpostoptime[1]=="00") {
					$postOpSiteTime=$time_splitpostoptime[0].":".$time_splitpostoptime[1].":01";
				}
			}
			*/
	   //POSTOPSITETIME saved in database
	   
	   //HEPARINLOCKOUTTIME saved in database
	   /*
		$time_splitherapinlock = explode(" ",$heparinLockOutTime);
		if($time_splitherapinlock[1]=="PM" || $time_splitherapinlock [1]=="pm") {
			$time_splitherapinlocktime = explode(":",$time_splitherapinlock[0]);
			$heparinLockOutTimeIncr=$time_splitherapinlocktime[0]+12;
			$heparinLockOutTime = $heparinLockOutTimeIncr.":".$time_splitherapinlocktime[1].":00";

		}elseif($time_splitherapinlock[1]=="AM" || $time_splitherapinlock [1]=="am") {
		    $time_splitherapinlocktime = explode(":",$time_splitherapinlock[0]);
			$heparinLockOutTime=$time_splitherapinlocktime[0].":".$time_splitherapinlocktime[1].":00";
		
			if($time_splitherapinlocktime[0]=="00" && $time_splitherapinlocktime[1]=="00") {
				$heparinLockOutTime=$time_splitherapinlocktime[0].":".$time_splitherapinlocktime[1].":01";
			}
		
		}
		*/
		 //HEPARINLOCKOUTTIME saved in database

	//START CODE TO CHECK NURSE SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord('postopnursingrecord', 'confirmation_id', $pConfId);
		if($chkNurseSignDetails) {
			$chk_signNurseId	= $chkNurseSignDetails->signNurseId;
			$chk_versionNum		= $chkNurseSignDetails->version_num;
		}
	//END CODE TO CHECK NURSE SIGN IN DATABASE 
	
	//CHECK BP,P,R IF EXIST ATLEAST ONCE
		$chkPostopNurseVitalSignQry = "select * from `vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
		$chkPostopNurseVitalSignRes = imw_query($chkPostopNurseVitalSignQry) or die(imw_error()); 
		$chkPostopNurseVitalSignNumRow = imw_num_rows($chkPostopNurseVitalSignRes);
		if($chkPostopNurseVitalSignNumRow>0) {
			$chkVitalSignExist = "true";
		}
	//END CHECK BP,P,R IF EXIST ATLEAST ONCE
	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if(trim($postOpSite)=="" || trim($nourishKind)==""  || trim($painLevel)==""
			|| trim($postOpSiteTime)=="" || trim($postOpSiteTime)=="00:00:00" ||  (($heparinLockOutTime=="" || $heparinLockOutTime == "00:00:00") && $heparinLockOutNA=="")
			|| trim($dischargeTime)=="" || trim($dischargeTime)=="00:00:00"
			|| $chkVitalSignExist<>"true" 
			|| $chk_signNurseId=="0"
			|| $postopNurseCheckListComplete!="yes") 
		{
			$form_status = "not completed";
		}
		if($version_num > 1)
		{
			if($postOpDropExist !='Yes')
			{
				$form_status = "not completed";
			}
		}

		if($version_num > 2 && $patient_aox3 <> 'Yes' )
		{
			if( $form_status == 'completed' && $other_mental_status == '')
			{
				$form_status = "not completed";
			}
		}
		
		if( $version_num > 3)
		{
			if($form_status == 'completed' && $bs_na == '' && $bs_value == '' )
			{
				$form_status = "not completed";
			}
		}
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	if($version_num > 3) {
		$versionNumQry .= ", bs_na=".($bs_na ? $bs_na : 'NULL').", bs_value = ".($bs_value ? "'".$bs_value."'" : 'NULL')." ";	
	}
	if($version_num > 4) {
		$versionNumQry .= ", patient_transfer ='".$patient_transfer."' ";	
	}
		
	if($chkPostopnursingNumRow>0) {
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkPostopnursingRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		$SavePostopnursingQry = "update `postopnursingrecord` set 
									painLevel = '$painLevel',
									postOpSite = '$postOpSite',
									postOpSiteTime = '$postOpSiteTime', 
									nourishKind = '$nourishKind',
									removedIntact = '$removedIntact',
									heparinLockOutTime = '$heparinLockOutTime', 
									heparinLockOutNA = '$heparinLockOutNA',
									patient_aox3 = '$patient_aox3',
									recoveryComments = '$recoveryComments', 
									relivedNurseId = '$relivedNurseId',
									patientReleased2Adult = '$patientReleased2Adult',
									patientsRelation = '$patientsRelation',
									patientsRelationOther = '$patientsRelationOther', 
									dischargeTime = '$dischargeTime',
									nurseInitials = '$nurseInitials', 
									form_status ='".$form_status."',
									ascId='".$_REQUEST["ascId"]."', 
									confirmation_id = '".$_REQUEST["pConfId"]."', 
									patient_id = '".$_REQUEST["patient_id"]."'
									".$versionNumQry."
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else {
		$SavePostopnursingQry = "insert into `postopnursingrecord` set 
									painLevel = '$painLevel',
									postOpSite = '$postOpSite',
									postOpSiteTime = '$postOpSiteTime', 
									nourishKind = '$nourishKind',
									removedIntact = '$removedIntact',
									heparinLockOutTime = '$heparinLockOutTime', 
									heparinLockOutNA = '$heparinLockOutNA',
									patient_aox3 = '$patient_aox3',
									recoveryComments = '$recoveryComments', 
									relivedNurseId = '$relivedNurseId',
									patientReleased2Adult = '$patientReleased2Adult',
									patientsRelation = '$patientsRelation',
									patientsRelationOther = '$patientsRelationOther', 
									dischargeTime = '$dischargeTime',
									nurseInitials = '$nurseInitials', 
									form_status ='".$form_status."',									 
									confirmation_id='".$_REQUEST["pConfId"]."',
									patient_id = '".$_REQUEST["patient_id"]."'
									".$versionNumQry."
									";
	}
	
	$SavePostopnursingRes = imw_query($SavePostopnursingQry) or die($SavePostopnursingQry.imw_error());
	
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
	
	$save = 'true';
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$recentChartSaved = "";
		if(trim($postOpSiteTime)) { $recentChartSavedQry = ", recentChartSaved = 'postopnursingrecord' ";}
		
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' ".$recentChartSavedQry." WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."' AND patient_confirmation_id!='0'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";
		/*
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}*/
		

	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
}

	//SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
		if($_POST['hidd_saveVitalSign']=='yes'){
			$hidd_vitalSignBp = $_POST['hidd_vitalSignBp'];
			$hidd_vitalSignP = $_POST['hidd_vitalSignP'];
			$hidd_vitalSignR = $_POST['hidd_vitalSignR'];
			//$hidd_vitalSignTimeBp = $_POST['hidd_vitalSignTime']; 
			$hidd_vitalSignTimeBp = $objManageData->setTmFormat($_POST['hidd_vitalSignTime']);
			$SaveVitalSignQry = "insert into `vitalsign_tbl` set 
										vitalSignBp = '$hidd_vitalSignBp',
										vitalSignP = '$hidd_vitalSignP', 
										vitalSignR = '$hidd_vitalSignR',
										vitalSignTime = '$hidd_vitalSignTimeBp',										
										confirmation_id='".$_REQUEST["pConfId"]."',
										patient_id = '".$_REQUEST["patient_id"]."'";
										
			$SaveVitalSignRes = imw_query($SaveVitalSignQry) or die(imw_error());
		}
		
		
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl

//END SAVE RECORD TO DATABASE


//VIEW RECORD FROM DATABASE
	//if($_POST['SaveRecordForm']==''){	
		$ViewPostopnursingQry = "select * from `postopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPostopnursingRes = imw_query($ViewPostopnursingQry) or die(imw_error()); 
		$ViewPostopnursingNumRow = imw_num_rows($ViewPostopnursingRes);
		$ViewPostopnursingRow = imw_fetch_array($ViewPostopnursingRes); 
		
		$painLevel = $ViewPostopnursingRow["painLevel"];
		$painLevel = ($painLevel == '') ? $painLevel : (int) $painLevel;
		$postOpSite = $ViewPostopnursingRow["postOpSite"];
		
		$postOpSiteTime = $objManageData->getTmFormat($ViewPostopnursingRow["postOpSiteTime"]);
		$hidd_postOpSiteTime = $ViewPostopnursingRow["postOpSiteTime"];
		
		$nourishKind = $ViewPostopnursingRow["nourishKind"];
		$removedIntact = $ViewPostopnursingRow["removedIntact"];
		$heparinLockOutTime = $objManageData->getTmFormat($ViewPostopnursingRow["heparinLockOutTime"]);
		$heparinLockOutNA = $ViewPostopnursingRow["heparinLockOutNA"];
		$patient_aox3 = $ViewPostopnursingRow["patient_aox3"];
		$other_mental_status = $ViewPostopnursingRow["other_mental_status"];
		$patient_transfer = $ViewPostopnursingRow["patient_transfer"];
		$recoveryComments = $ViewPostopnursingRow["recoveryComments"];
		$relivedNurseId = $ViewPostopnursingRow["relivedNurseId"];
		$patientReleased2Adult = $ViewPostopnursingRow["patientReleased2Adult"];
		$patientsRelation = $ViewPostopnursingRow["patientsRelation"];
		$patientsRelationOther = $ViewPostopnursingRow["patientsRelationOther"];
		$dischargeTime = $objManageData->getTmFormat($ViewPostopnursingRow["dischargeTime"]);
		$nurseId = $ViewPostopnursingRow["nurseId"]; 
		$nurseInitials = $ViewPostopnursingRow["nurseInitials"]; 
		$form_status  = $ViewPostopnursingRow["form_status"]; 
		$bs_na = $ViewPostopnursingRow["bs_na"]; 
		$bs_value = $ViewPostopnursingRow["bs_value"]; 
		$signNurseId =  $ViewPostopnursingRow["signNurseId"];
		$signNurseFirstName =  $ViewPostopnursingRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewPostopnursingRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewPostopnursingRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewPostopnursingRow["signNurseStatus"];
		$signNurseDateTime =  $ViewPostopnursingRow["signNurseDateTime"];
		$version_num =  $ViewPostopnursingRow["version_num"];
		if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
		else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	$current_form_version; }
		
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		
		//START PREFILL VALUE FROM POST-OP NURSE PREFERENCE CARD
		if($nurseIDProfile && trim($form_status)=="") {
			$nurseProfileQry 				= "select * from `nurse_profile_tbl` where  nurseId = '".$nurseIDProfile."'";
			$nurseProfileRes 				= imw_query($nurseProfileQry) or die(imw_error());
			if(imw_num_rows($nurseProfileRes)>0) {
				$nurseProfileRow 			= imw_fetch_array($nurseProfileRes);
				$nurse_profile_sign_path	= $nurseProfileRow['nurse_profile_sign_path'];
				if(trim($nurse_profile_sign_path)) {
					$postOpSite 			= $nurseProfileRow["postOpSite"];
					$nourishKind 			= $nurseProfileRow["nourishKind"];
					$removedIntact 			= $nurseProfileRow["removedIntact"];
					$patient_aox3 			= $nurseProfileRow["patient_aox3"];
					$patient_transfer 		= $nurseProfileRow["patient_transfer"];
					$other_mental_status	= $nurseProfileRow["other_mental_status"];
					$recoveryComments 		= $nurseProfileRow["recoveryComments"];
					$relivedNurseId 		= $nurseProfileRow["relivedNurseId"];
					$patientReleased2Adult	= $nurseProfileRow["patientReleased2Adult"];
					$patientsRelation 		= $nurseProfileRow["patientsRelation"];
					$patientsRelationOther 	= $nurseProfileRow["patientsRelationOther"];
				}
			}
		}
		//END PREFILL VALUE FROM POST-OP NURSE PREFERENCE CARD

		//START CODE TO PREFIL NOURISHMENT KIND FROM DEFAULT PRE-DEFINE
		$nourishmentKindDefaultArr = array();
		if($form_status!="not completed" && $form_status!="completed" && trim($nourishKind)=="") {
			foreach($rowNourishmentKindArr as $rowNourishmentKind) { //FROM common/nourishment_kind.php
				$nourishmentKindDefault = $rowNourishmentKind["nourishmentKindDefault"];
				if($nourishmentKindDefault=='1') {
					$nourishmentKindDefaultArr[] = 	stripslashes($rowNourishmentKind["name"]);
				}
			}
			if(count($nourishmentKindDefaultArr)>0) {
				$nourishKind = trim(implode(", ",$nourishmentKindDefaultArr));	
			}
		}
		//END CODE TO PREFIL NOURISHMENT KIND FROM DEFAULT PRE-DEFINE
									
		//GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
			if($nurseId=="" || $nurseId==0) {
				$ViewNurseIdQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
				$ViewNurseIdRes = imw_query($ViewNurseIdQry) or die(imw_error()); 
				$ViewNurseIdRow = imw_fetch_array($ViewNurseIdRes); 
				$nurseId = $ViewNurseIdRow["nurseId"];
			}	
		//END GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
		
	//}
	//END if($_POST['SaveRecordForm']=='')

	//GET NURSE NAME
		$ViewNurseNameQry = "select * from `users` where  usersId = '".$nurseId."'";
		$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
		$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
		$NurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
	//END GET NURSE NAME
	
	//TEMPRARY NAME OF NURSE
		if(trim($NurseName)=="") {
			$NurseName = "Nurse Name";
		}
	//END TEMPRARY NAME OF NURSE
		
	//CODE TO SET POSTOP SITE TIME
		/*
		if($postOpSiteTime=="00:00:00" || $postOpSiteTime=="") {
			$postOpSiteTime="";
		}else {
			$postOpSiteTime=$postOpSiteTime;
			$time_split = explode(":",$postOpSiteTime);
			if($time_split[0]>12) {
				$am_pm = "PM";
			}else {
				$am_pm = "AM";
			}
			if($time_split[0]>=13) {
				$time_split[0] = $time_split[0]-12;
				if(strlen($time_split[0]) == 1) {
					$time_split[0] = "0".$time_split[0];
				}
			}else {
			//DO NOTHNING
			}
			$postOpSiteTime = $time_split[0].":".$time_split[1]." ".$am_pm;
		}*/
	//END CODE TO SET POSTOP SITE TIME
									
	//START CODE TO SET HEPARINLOCKOUT TIME
		/*
		if($heparinLockOutTime=="00:00:00" || $heparinLockOutTime=="") {
			$heparinLockOutTime = "";
		}else {
			$heparinLockOutTime = 	$heparinLockOutTime;
		
			$time_split1 = explode(":",$heparinLockOutTime);
			if($time_split1[0]>12) {
				$am_pm1 = "PM";
			}else {
				$am_pm1 = "AM";
			}	
		
			if($time_split1[0]>=13) {
				$time_split1[0] = $time_split1[0]-12;
				if(strlen($time_split1[0]) == 1) {
					$time_split1[0] = "0".$time_split1[0];
				}
			}else {
				//DO NOTHNING
			}
			$heparinLockOutTime = $time_split1[0].":".$time_split1[1]." ".$am_pm1;
	 	}
		*/
	//END CODE TO SET HEPARINLOCKOUT TIME	

				
		

//END VIEW RECORD FROM DATABASE

?>
<script type="text/javascript">
	//top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frm_post_op_nurse.nurseInitials;
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
	var coords = document.applets["app_PostopNurseSignature"].getSign();
	return coords;
}
function getclear_os(){
	document.applets["app_PostopNurseSignature"].clearIt();
	changeColorThis(255,0,0);
	document.applets["app_PostopNurseSignature"].onmouseout();
}
function changeColorThis(r,g,b){				
	document.applets['app_PostopNurseSignature'].setDrawColor(r,g,b);								
}
//Applet

//SAVE VITAL SIGN (BP, P, R, TIME  )
	
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
	function save_vitalsign_value() {
		var k=document.frm_post_op_nurse.rowK.value;
		++k;
		document.frm_post_op_nurse.rowK.value=k;
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
			
		var vitalSignBP_main_ajax = document.frm_post_op_nurse.vitalSignBP_main.value
		var vitalSignP_main_ajax = document.frm_post_op_nurse.vitalSignP_main.value
		var vitalSignR_main_ajax = document.frm_post_op_nurse.vitalSignR_main.value
		var vitalSignO2SAT_main_ajax = document.frm_post_op_nurse.vitalSignO2SAT_main.value
		var vitalSignTime_main_ajax = document.frm_post_op_nurse.vitalSignTime_main.value
		var vitalSignTemp_main_ajax = document.frm_post_op_nurse.vitalSignTemp_main.value
		
		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo trim($_REQUEST["preColor"], "#");?>';
		
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="post_op_nursing_record_ajax.php";
		url=url+"?vitalSignBP_main="+vitalSignBP_main_ajax
		url=url+"&vitalSignP_main="+vitalSignP_main_ajax
		url=url+"&vitalSignR_main="+vitalSignR_main_ajax
		url=url+"&vitalSignO2SAT_main="+vitalSignO2SAT_main_ajax
		
		url=url+"&vitalSignTime_main="+vitalSignTime_main_ajax
		url=url+"&vitalSignTemp_main="+vitalSignTemp_main_ajax
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		xmlHttp.onreadystatechange=AjaxTestingFun
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	
	}

	function AjaxTestingFun() {
		if(xmlHttp.readyState==1) {
			if(parent.parent) {
				if(typeof(parent.parent.show_loading_image)!="undefined") {
					parent.parent.show_loading_image('block');
				}
			}
			
		}
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			if(parent.parent) {
				if(typeof(parent.parent.show_loading_image)!="undefined") {
					parent.parent.show_loading_image('none');
				}
			}
			document.getElementById("vital_sign_main_id").innerHTML=xmlHttp.responseText;
			clearAll('bp_temp2','bp_temp3','bp_temp8','bp_temp4');
			save_hide_row_idTemp('vital_sign_2_id','bp_temp','bp_temp2','bp_temp3','bp_temp8','bp_temp4');
			document.forms[0].submit();
		}
	}
	
//END SAVE VITAL SIGN (BP, P, R, TIME  )
//delete vital sign
function delentry(id){
	var ask = confirm("Are you sure to delete the record.")
	if(ask==true){
		var k=document.frm_post_op_nurse.rowK.value;
		--k;
		if(k==0){
			document.getElementById('vital_sign_2_id').style.display = 'block';
		}
		document.frm_post_op_nurse.rowK.value=k;
		xmlHttp=GetXmlHttpObject();		
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request");
			return true;
		}		
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url='post_op_nursing_record_vital_del_ajax.php?delId='+id+'&row='+k;
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		
		xmlHttp.onreadystatechange=AjaxTestDel
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}		
function AjaxTestDel(){
	if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
	document.getElementById("vital_sign_main_id").innerHTML=xmlHttp.responseText;

	}
	
}
//delete vital sign
//saveOperativeTime
function saveOperativeTime()
{
	xmlHttp=GetXmlHttpObject();
	if(xmlHttp==null)
	{
	alert ("Browser does not support HTTP Request");
			return true;
	}
	var operative_time=document.frm_post_op_nurse.postOpSiteTime.value;
	
	var url="post_operative_time_ajax.php?operative_time="+operative_time;		
	xmlHttp.onreadystatechange=AjaxTestForoperative
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function AjaxTestForoperative() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("new_time").innerHTML=xmlHttp.responseText;
			}
	}
//saveOperativeTime
//saveHeparinTime
	function saveHeparinTime()
	{
	xmlHttp=GetXmlHttpObject();
		if(xmlHttp==null)
		{
		alert("Browser does not supports HTTP request");
		return true;
		}
		var heparin_time=document.frm_post_op_nurse.heparinLockOutTime.value;
		var url='heparin_time_ajax.php?heparin_time='+heparin_time;
		xmlHttp.onreadystatechange=AjaxTestForHeaparine
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function AjaxTestForHeaparine()
	{
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("heparin_time_td").innerHTML=xmlHttp.responseText;	
			}	
  }	
//saveHeparinTime

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
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,delSign) {

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
		var preColor1 = '<?php echo trim($_REQUEST["preColor"],"#");?>';
		
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		if(delSign) {
			url=url+"&delSign=yes"
		}
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
			}
	}
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

</script>

<!--<body onLoad="top.changeColor('<?php echo $bgcolor_post_op_nursing_order; ?>');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();">-->

<div id="post" style="display:none; position:absolute;"></div>
<?php

// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmationFinalize->finalize_status;
	$patient_primary_procedure_id = $detailConfirmationFinalize->patient_primary_procedure_id; 
	$patient_secondary_procedure_id = $detailConfirmationFinalize->patient_secondary_procedure_id; 
	$patient_tertiary_procedure_id = $detailConfirmationFinalize->patient_tertiary_procedure_id;
	$postOpAssignedSurgeonId	=	$detailConfirmationFinalize->surgeonId;
// GETTING FINALIZE STATUS


// ***************************************************************
// START GET POST OP ORDERS FROM SURGERON PROFILE
if($form_status=='') {
	$surgeonProfileQry="
		SELECT a.postOpDrop FROM surgeonprofile a,surgeonprofileprocedure b
		WHERE a.surgeonId			=	'".$postOpAssignedSurgeonId."'
		AND   b.procedureId			=	'".$patient_primary_procedure_id."'
		AND   a.surgeonProfileId	=	b.profileId
		AND   a.del_status=''
	";
	$surgeonProfileRes = imw_query($surgeonProfileQry) or die(imw_error());
	$surgeonProfileNumRow = imw_num_rows($surgeonProfileRes);
	if($surgeonProfileNumRow>0) {
		$surgeonProfileRow = imw_fetch_array($surgeonProfileRes);
		$patientToTakeHome = stripslashes($surgeonProfileRow['postOpDrop']);
	}
	else
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
						$patientToTakeHome	= $procPrefCardRow->postOpDrop;
						
						break; 
					}
				}
			}
			
			/* End Procedure Preference Card if surgeon's profile/Default  Not found*/
		/*}*/
	}
}
//END GET POST OP ORDERS FROM SURGEON PROFILE

//Start Get Default Post Op Order
$defaultPostOpOrder	=	'';
if( $patientToTakeHome == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
{
	$defaultPostOpOrder	= $objManageData->getDefault('postopdrops','name',"@@");
	$explodeDefault		= true;
	$patientToTakeHome 	= $defaultPostOpOrder;
}
//End Get Default Post Op Order


	//START CODE TO GET DOCUMENTS OF EKG H&P CONSENT
	$ekgHpLink = "";
	$anesConsentEkgHpArr = array('H&P','Sx Planning Sheet');
	foreach($anesConsentEkgHpArr as $anesConsentEkgHpName) {
		$eKgHpCLickFun='';
		$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
							 FROM  scan_upload_tbl sut,scan_documents sd
							 WHERE sd.confirmation_id 	= '".$pConfId."'
							 AND   sut.confirmation_id 	= '".$pConfId."'
							 AND   sd.document_name 	= '".$anesConsentEkgHpName."'
							 AND   sd.document_id 		= sut.document_id
							 ORDER BY sd.document_id, sut.document_id
							"; 
		$scnFoldrEkgHpRes = imw_query($scnFoldrEkgHpQry) or die(imw_error());
		$scnFoldrEkgHpNumRow = imw_num_rows($scnFoldrEkgHpRes);
		if($scnFoldrEkgHpNumRow<=0) {
			if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P' || $anesConsentEkgHpName=='Sx Planning Sheet') {
				if($anesConsentEkgHpName=='EKG' || $anesConsentEkgHpName=='H&P') {
					$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
										 FROM  scan_upload_tbl sut,scan_documents sd 
										 WHERE sd.confirmation_id 	= '".$pConfId."'
										 AND   sut.confirmation_id 	= '".$pConfId."'
										 AND   sut.document_name like '%".$anesConsentEkgHpName."%'
										 AND   sd.document_id 		= sut.document_id
										 ORDER BY sd.document_id, sut.document_id
										"; 
				}
				if($anesConsentEkgHpName=='Sx Planning Sheet') {
					$scnFoldrEkgHpQry = "SELECT sut.scan_upload_id,sut.image_type,sut.pdfFilePath 
										 FROM  scan_upload_tbl sut,scan_documents sd 
										 WHERE sd.confirmation_id 	= '".$pConfId."'
										 AND   sut.confirmation_id 	= '".$pConfId."'
										 AND   sd.document_name 	= 'Clinical'
										 AND   sut.pdfFilePath 	 LIKE '%Sx_Planing_Sheet_%'
										 AND   sd.document_id 		= sut.document_id
										 ORDER BY sd.document_id, sut.document_id
										"; 
				}
				$scnFoldrEkgHpRes = imw_query($scnFoldrEkgHpQry) or die(imw_error());
				$scnFoldrEkgHpNumRow = imw_num_rows($scnFoldrEkgHpRes);
			}
		}
		if($scnFoldrEkgHpNumRow>0) {
			while($scnFoldrEkgHpRow = imw_fetch_array($scnFoldrEkgHpRes)) {
				$scan_upload_id = $scnFoldrEkgHpRow['scan_upload_id'];
				$image_type = $scnFoldrEkgHpRow['image_type'];
				$pdfFilePath = $scnFoldrEkgHpRow['pdfFilePath'];
				if($image_type=='application/pdf') $image_type = 'pdf';
				$imageTypeNew = "\'".$image_type."\'";
				$scanUploadId = "\'".$scan_upload_id."\'";
				$pdfFilePathNew = "\'".$pdfFilePath."\'";
				$eKgHpCLickFun.= 'top.openImage('.$scanUploadId.','.$imageTypeNew.','.$pdfFilePathNew.');';
			}
		}
		if($eKgHpCLickFun!='') {
			$anesConsentEkgHpNameNew = $anesConsentEkgHpName;
			$anesConsentEkgHpNameNew = str_ireplace('H&P','H&amp;P',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Ocular Hx','OCX',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Health Questionnaire','HQ',$anesConsentEkgHpNameNew);
			$anesConsentEkgHpNameNew = str_ireplace('Sx Planning Sheet','SxP',$anesConsentEkgHpNameNew);
			$ekgHpLink.='<a href="#" class="btn-sm" onclick="'.$eKgHpCLickFun.'">'.$anesConsentEkgHpNameNew.'</a>&nbsp;';
		}
	}
	//END CODE TO GET DOCUMENTS OF EKG H&P CONSENT

?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<?php
$blurInput = "onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
$keyPressInput = "onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
$keyUpInput = "onKeyUp=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
include("common/pre_defined_popup.php");
?>
<div id="divSaveAlert" style="position:absolute;left:350px; top:200px; display:none; z-index:2"><!-- Alert On Saving Form -->
	<?php 
		$bgCol = $title_post_op_nursing_order;
		$borderCol = $title_post_op_nursing_order;
		include('saveDivPopUp.php'); 
		
	?>
</div>
<form name="frm_post_op_nurse" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="post_op_nursing_record.php" autocomplete="off">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
	<input type="hidden" name="selected_frame_name" id="selected_frame_name_id" value="">
	<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="nurseId" id="nurseId" value="<?php echo $nurseId;?>">
	<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes"> 
	<input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $_REQUEST["patient_id"];?>">
	<input type="hidden" name="formIdentity" id="formIdentity" value="">
	<input type="hidden" name="getText" id="getText">	
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="frmAction" id="frmAction" value="post_op_nursing_record.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
	<input type="hidden" id="vitalSignGridHolder" />
    
<div class="inner_surg_middle scheduler_table_Complete" id="" style="">
	  <?php
	  		$epost_table_name = "postopnursingrecord";
			include("./epost_list.php");
	  ?>
      <!--
      <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_qint">
		  <span class="bg_span_qint">
			 Post-Op Nursing Record
		  </span>
		<?php
			
			if($ekgHpLink) { ?>
                <span id="ekgHpLink" class="nowrap valignBottom"><?php echo $ekgHpLink; ?></span>
        <?php 
            } ?>
	   </div>	
    	-->

	<Div class="clearfix margin_adjustment_only"></Div>
	<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
        <?php
		$pLg = "12";
		if($version_num > 1 ) {
			$pLg = "7";
		?>
        <div class="col-lg-5 col-sm-5 col-xs-5 col-md-5">

			<Div class="panel panel-default bg_panel_qint">
				<div class="panel-heading rob" style="color:#FFF;">
                    <div class="row">
						<div class="col-md-6 col-sm-4 col-xs-4 col-lg-6">
							Physician Orders/Medications
						</div>
						<div class="col-md-3 col-sm-4 col-xs-4 col-lg-3">
							Time
						</div>
						<div class="col-md-3 col-sm-4 col-xs-4 col-lg-3">
							Not Given
						</div>
					</div>
				</div>
				 <!--<div class="scheduler_table_Complete">
                 	<div class=" my_table_Checkall table_slider_head  ">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                            <thead >
                                <tr style="height:35px;">
                                    <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6" id="TP_PHY_ORD">
                                        <a class="rob alle_link show-pop-list_g btn btn-default " data-placement="top" onClick="return showPatientTakeHomeNew('pat_tak_hom_area_id', '', 'no', parseInt(findPos_X('TP_PHY_ORD'))+12, parseInt(findPos_Y('TP_PHY_ORD')+20)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='';" > <span class="fa fa-caret-right"></span> Physician Orders</a></th>
                                    <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Time </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>-->
                <div class="table_slider">          
                    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped" style="background-color:#F1F4F0; " >
                        <tbody>
                        <?php
                            
                            $condArr		=	array();
                            $condArr['confirmation_id']	=	$pConfId ;
                            $condArr['chartName']		=	'post_op_physician_order_form' ;
                            
                            $pOrderData		=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_type="medication" DESC,recordId','Asc');
                            
                            $pOrderCounter	=	1;
                            $medOrdrExists =false;
							if(is_array($pOrderData) && count($pOrderData) > 0  )
                            {
                                
                                foreach($pOrderData as $pOrderRow)
                                {
                                    $medOrdrExists =false;
									if(strtolower($pOrderRow->physician_order_type) == "medication" || (trim($pOrderRow->physician_order_type)=="" && $pOrderRow->physician_order_time != "00:00:00")) { 
										$medOrdrExists = true;
									}else { 
										//continue;
									}
									if($version_num > 5 ) {
										$medOrdrExists = true; //SHOW TIMESTAMP FOR ALL MEDS AND ORDERS (IN THIS VERSION)
									}
									$time	=	($pOrderRow->physician_order_time <> '00:00:00') ? $objManageData->getTmFormat($pOrderRow->physician_order_time) : '' ;
                                    
																		$chk_not_given	=	($pOrderRow->physician_order_not_given == 'Yes') ? 'checked' : '' ;
																		
                                    $ordrColor = $whiteBckGroundColor;
                                    $ordrTimeColor = $whiteBckGroundColor;
                                    if(!trim($pOrderRow->physician_order_name) && $pOrderCounter == '1') {
                                        $ordrColor = $chngBckGroundColor;
                                        $ordrTimeColor = $chngBckGroundColor;	
                                    }
                                    if(trim($pOrderRow->physician_order_name) && !$time && !$chk_not_given) {
                                        $ordrTimeColor = $chngBckGroundColor;
                                    }
                                    
																		echo '<tr style="padding-left:0px; background-color:#FFFFFF; ">';
                                
                                    if($medOrdrExists == true) {
										echo '<input type="hidden" name="physicianOrderRecordId[]" value="'.$pOrderRow->recordId.'">';
									}
                                    echo '<td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">'.$pOrderRow->physician_order_name;
                                    echo '<input style="'.$ordrColor.'" type="hidden"  name="physicianOrderName[]" id="orderName_'.$loop.'" class="form-control" tabindex="1" value="'.$pOrderRow->physician_order_name.'" />';
                                    echo '</td>';
                                    
                                    echo '<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4">';
                                    if($medOrdrExists == true) {
										echo '<input style="'.$ordrTimeColor.'" type="text" name="physicianOrderTime[]" id="timeStamp_'.$pOrderCounter.'" class="form-control timeStamp" tabindex="2" value="'.$time.'" />';
									}
									echo '</td>';
                                    
									echo '<td class="text-center col-md-2 col-lg-2 col-sm-2 col-xs-2">';
									if($medOrdrExists == true) {
										echo '<input type="checkbox" name="chbxNotGiven['.($pOrderCounter-1).']" id="chbxNotGiven_'.$pOrderCounter.'" value="Yes" '.$chk_not_given.' onChange="changeBgColor(this,\'timeStamp_'.$pOrderCounter.'\')" />';
									}
									echo '</td>';
									
                                    echo '</tr>';	
                                
                                    $pOrderCounter++ ;
                                }
                            }
                            elseif($patientToTakeHome)
                            {
                                if($explodeDefault)
																	$pOrderData	=	explode('@@',$patientToTakeHome);
																else
																	$pOrderData	=	explode(',',$patientToTakeHome);
																
                                
                                foreach($pOrderData as $pOrderRow)
                                {
                                    
									$medOrdrExists = true;
									$ordrColor = $whiteBckGroundColor;
                                    $ordrTimeColor = $whiteBckGroundColor;
                                    if(!trim($pOrderRow) && $pOrderCounter == '1') {
                                        $ordrColor = $chngBckGroundColor;
                                        $ordrTimeColor = $chngBckGroundColor;	
                                    }
                                    if(trim($pOrderRow)) {
                                        $ordrTimeColor = $chngBckGroundColor;
                                    }
                                    $ordrTimeColor = $chngBckGroundColor;
                                    echo '<tr style="padding-left:0px; background-color:#FFFFFF; ">';
                                
                                    echo '<input type="hidden" name="physicianOrderRecordId[]" value="">';
                                    
                                    echo '<td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">'.$pOrderRow;
                                    echo '<input style="'.$ordrColor.'" type="hidden"  name="physicianOrderName[]" id="orderName_'.$pOrderCounter.'" class="form-control" tabindex="1"  value="'.$pOrderRow.'"  />';
                                    echo '</td>';
                                    
                                    echo '<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4">';
                                    echo '<input style="'.$ordrTimeColor.'" type="text" name="physicianOrderTime[]" id="timeStamp_'.$pOrderCounter.'" class="form-control timeStamp" tabindex="2"  />';
                                    echo '</td>';
                                    
									echo '<td class="text-center col-md-2 col-lg-2 col-sm-2 col-xs-2">';
								    echo '<input type="checkbox" name="chbxNotGiven['.($pOrderCounter-1).']" id="chbxNotGiven_'.$pOrderCounter.'" value = "Yes" onChange="changeBgColor(this,\'timeStamp_'.$pOrderCounter.'\')"/>';
                                    echo '</td>';
									
                                    echo '</tr>';	
                                
                                    $pOrderCounter++ ;
                                }
                            }else {
								echo '<tr style="padding-left:0px; background-color:#FFFFFF; "><td class="text-left col-md-12 col-lg-12 col-sm-12 col-xs-12">No Medication Found</td></tr>';	
							}
                                
                            /*
                            $startRow	=	$pOrderCounter; 
                            $endRow		=	$startRow+20;
                            for($loop = $startRow; $loop < $endRow ; $loop++)
                            {
                                $ordrColor = $whiteBckGroundColor;
                                $ordrTimeColor = $whiteBckGroundColor;
                                if(!trim($pOrderRow->physician_order_name) && $loop == '1') {
                                    $ordrColor = $chngBckGroundColor;
                                    $ordrTimeColor = $chngBckGroundColor;	
                                }
                                echo '<tr style="padding-left:0px; background-color:#FFFFFF; ">';
                                
                                echo '<input type="hidden" name="physicianOrderRecordId[]" value="">';
                                
                                echo '<td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">';
                                echo '<input style="'.$ordrColor.'" type="hidden"  name="physicianOrderName[]" id="orderName_'.$loop.'" class="form-control" tabindex="1"  />';
                                echo '</td>';
                                
                                echo '<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4">';
                                echo '<input style="'.$ordrTimeColor.'" type="text" name="physicianOrderTime[]" id="timeStamp_'.$loop.'" class="form-control timeStamp" tabindex="1"  />';
                                echo '</td>';
                                
								echo '<td class="text-center col-md-2 col-lg-2 col-sm-2 col-xs-2">';
                                echo '<input type="checkbox" name="chbxNotGiven[]" id="chbxNotGiven_'.$pOrderCounter.'" value="Yes" '.$chk_not_given.' />';
                                echo '</td>';
                                
                                echo '</tr>';	
                            }
							*/
                        ?>		
                        </tbody>
                    </table>      
               </div>
           </Div>     
        </div>
        <?php
		}
		$painLevelBackColor=$chngBckGroundColor;
		if(trim($painLevel)!=""){
			 $painLevelBackColor=$whiteBckGroundColor; 
		}
		?>
        <div class="col-lg-<?php echo $pLg;?> col-sm-<?php echo $pLg;?> col-xs-<?php echo $pLg;?> col-md-<?php echo $pLg;?>">
            <Div class="panel panel-default bg_panel_qint">
                <div class="panel-heading rob">
                    <div class="row">
                        <div class="col-md-8 col-sm-6 col-xs-6 col-lg-8">
                            <a href="javascript:void(0)" data-toggle="collapse" data-target="#vital_sign_2_id"  style="color:#FFF;">
                                <span class="fa fa-stethoscope" style="font-size:21px;"></span> Recovery Vital Signs
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-6 col-lg-4 "  >
                            <div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 " for="painLevel" style="color:#FFF;">Pain Level</div>
                            <div class="col-md-7 col-sm-7 col-xs-7 col-lg-7 " style=" <?php echo $painLevelBackColor;?>">
                                <Select class="selectpicker select-mandatory pain-level-select form-control" name="painLevel" id="painLevel" style="width:60%;display: inline-block; " onChange="changeSelectpickerColor('.select-mandatory');">
                                    <option value="" selected></option>
                                    <?php
                                    for($i=0;$i<=10;$i++) {
                                    ?>
                                        <option value="<?php echo $i;?>" <?php if($painLevel === $i) echo 'selected'; ?>><?php echo $i;?></option>
                                    <?php
                                    }
                                    ?>
                                </Select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="vital_sign_main_id">
    <?php
        $ViewPostopNurseVitalSignQry = "select * from `vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
        $ViewPostopNurseVitalSignRes = imw_query($ViewPostopNurseVitalSignQry) or die(imw_error()); 
        $ViewPostopNurseVitalSignNumRow = imw_num_rows($ViewPostopNurseVitalSignRes);
        if($ViewPostopNurseVitalSignNumRow>0) {
            $k=1;
            while($ViewPostopNurseVitalSignRow = imw_fetch_array($ViewPostopNurseVitalSignRes)) {
                $vitalsign_id=$ViewPostopNurseVitalSignRow["vitalsign_id"];  
                $vitalSignBp = $ViewPostopNurseVitalSignRow["vitalSignBp"];
                $vitalSignP = $ViewPostopNurseVitalSignRow["vitalSignP"];
                $vitalSignR = $ViewPostopNurseVitalSignRow["vitalSignR"];
                $vitalSignO2SAT = $ViewPostopNurseVitalSignRow["vitalSignO2SAT"];
								$vitalSignTemp = $ViewPostopNurseVitalSignRow["vitalSignTemp"];
                //$vitalSignTime = $ViewPostopNurseVitalSignRow["vitalSignTime"];
				$vitalSignTime = $objManageData->getTmFormat($ViewPostopNurseVitalSignRow["vitalSignTime"]);
                if($k%2==0) {
                    $bg_color_post_op_nurse = $rowcolor_post_op_nursing_order;
                }else {
                    $bg_color_post_op_nurse = "#FFFFFF";
                } 
            ?>
            
            <div class="inner_safety_wrap" id="id=vs_<?php echo $vitalsign_id; ?>">
                <div class="row">
                      
                    <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    	<div class="rown">
                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                            <Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">BP</label>
                                <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5 text-center">
                                    <span class="inner_span"><?php echo $vitalSignBp;?></span>                 
                                </span>
                            </Div>
                        </div>
                       	<div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 plr5">
                        	<div class="rown">
                          	
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 plr5"> 
                            	<Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">P</label>
                                <Span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5 text-center">
                                  <span class="inner_span"><?php echo $vitalSignP;?></span>             
                                </Span>
                              </Div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 plr5">   
                              <Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">R</label>
                                <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                                  <span class="inner_span"><?php echo $vitalSignR;?></span>    
                                </span>
                            	</Div>
                           	</div> 
                            
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-5 plr5">
                            	<Div class="rown">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">O<sub>2</sub>SAT</label>
                                <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                                  <span class="inner_span"><?php echo $vitalSignO2SAT;?></span>
                                </span>
                              </Div>
                           	</div>
                            
                         	</div>   
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                            <Div class="rown nowrap">
                                <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">Time</label>
                                <Span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                                    <span class="inner_span"><?php echo $vitalSignTime;?></span>
                                </Span>
                            </Div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                        	<Div class="rown">
                            <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label">Temp</label>
                            <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5  text-center">
                              <span class="inner_span"><?php echo $vitalSignTemp;?></span>    
                            </span>
                          </Div>
                       	</div>
                        
                        <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 plr5 text-center">
                        	 <a href="javascript:void(0)" class="btn btn-danger" style="margin:0" onClick="delentry(<?php echo $vitalsign_id; ?>);">  X </a>
                       </div>
                   		</div>    
                    </Div>
                </div>
            </div>
    <?php		
            $k++;
            } 
        }
        
    $calculatorTopValue = $k*22+65;
    $calculatorTopChangeValue = "0";
    if($ViewPostopNurseVitalSignNumRow>0) { $Displayvital_sign_2_id=""; $calculatorTopChangeValue=($ViewPostopNurseVitalSignNumRow*20);}else{ $Displayvital_sign_2_id="in";} 
    $calculatorSetValue = $calculatorTopValue;
    
    ?>
                </div>
              <div class="inner_safety_wrap collapse <?php echo $Displayvital_sign_2_id; ?>" id="vital_sign_2_id">
              	<input type="hidden"  id="rowK" name="row" value="<?php echo $k;?>">
             		<input type="hidden" id="bp" name="bp_hidden">
                <div class="row">
                  <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="rown"> 
                    <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                      <Div class="rown">
                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label" for="bp_temp">BP</label>
                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5 ">
                          <input class="form-control" type="text" id="bp_temp" name="vitalSignBP_main" style="<?php if($vitalSignBp){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?> " onFocus="changeTxtGroupColor(1,'bp_temp');" onBlur="changeTxtGroupColor(1,'bp_temp');" onKeyUp="changeTxtGroupColor(1,'bp_temp');displayText1=this.value" onClick="getShowpos(parseInt(findPos_Y('bp_temp'))+100,parseInt(findPos_X('bp_temp')),'flag1');clearVal_c();"/>
                        </span>
                      </Div>
                    </div>
                    
                    <div class="col-md-5 col-sm-5 col-xs-5 col-lg-5 plr5">
                      <div class="rown">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 plr5">
                          <Div class="rown">
                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label" for="bp_temp2">P</label>    
                        <Span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5">
                          <input class="form-control" type="text" name="vitalSignP_main" id="bp_temp2" style="<?php if($vitalSignP){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?> " onFocus="changeTxtGroupColor(1,'bp_temp2');" onBlur="changeTxtGroupColor(1,'bp_temp2');" onKeyUp="changeTxtGroupColor(1,'bp_temp2');displayText2=this.value" onClick="getShowpos(parseInt(findPos_Y('bp_temp2')),parseInt(findPos_X('bp_temp2')),'flag2');clearVal_c();"/>
                        </Span>
                      </Div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 plr5">
                          <Div class="rown">
                            <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label" for="bp_temp3">R</label>
                            <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5">
                              <input class="form-control" type="text" name="vitalSignR_main" id="bp_temp3" style="<?php if($vitalSignR){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?> " onFocus="changeTxtGroupColor(1,'bp_temp3');" onBlur="changeTxtGroupColor(1,'bp_temp3');" onKeyUp="changeTxtGroupColor(1,'bp_temp3');displayText3=this.value"  onClick="getShowpos(parseInt(findPos_Y('bp_temp3')),parseInt(findPos_X('bp_temp3')),'flag3');clearVal_c();"/>
                            </span>
                          </Div>	
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-5 plr5">  
                          <Div class="rown">
                            <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label" for="bp_temp8">O<sub>2</sub>SAT</label>    
                            <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5">
                              <input class="form-control" type="text" name="vitalSignO2SAT_main" id="bp_temp8" style="<?php if($vitalSignO2SAT){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?> " onFocus="changeTxtGroupColor(1,'bp_temp8');" onBlur="changeTxtGroupColor(1,'bp_temp8');" onKeyUp="changeTxtGroupColor(1,'bp_temp8');displayText8=this.value"  onClick="getShowpos(parseInt(findPos_Y('bp_temp8')),parseInt(findPos_X('bp_temp8')),'flag8');clearVal_c();"/>
                            </span>
                          </Div>
                        </div>	
                      </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                      <Div class="rown">
                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label" for="bp_temp4">Time</label>
                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5">
                          <input class="form-control" type="text" name="vitalSignTime_main" id="bp_temp4" style="<?php if($vitalSignTime){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?>  " onFocus="changeTxtGroupColor(1,'bp_temp4');" onBlur="changeTxtGroupColor(1,'bp_temp4');" onKeyUp="changeTxtGroupColor(1,'bp_temp4');displayText4=this.value" onClick="this.style.backgroundColor='#FFFFFF';getShowpos(parseInt(findPos_Y('bp_temp4')),parseInt(findPos_X('bp_temp4')),'flag4');clearVal_c();return displayTimeAmPm('bp_temp4');"/>
                        </span>
                      </Div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 plr5">
                      <Div class="rown">
                        <label class="col-md-12 col-lg-4 col-xs-12 col-sm-12 plr5 text-center vs_label" for="bp_temp9">Temp</label>
                        <span class="col-md-12 col-lg-8 col-xs-12 col-sm-12 plr5">
                          <input class="form-control" type="text" name="vitalSignTemp_main" id="bp_temp9" onKeyUp="displayText4=this.value" onClick="getShowTemp(parseInt(findPos_Y('bp_temp9'))+34,parseInt(findPos_X('bp_temp9')),'flag9');clearVal_c();"/>
                        </span>
                      </Div>
                    </div>
                    
                    <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 plr5 text-center">
                      <label class="hidden-lg">&nbsp;</label>
                      <a id="postopNurseVitalSaveId" href="javascript:void(0)" class="btn btn-success" style="margin:10% 0" onClick="javascript:if(document.getElementById('bp_temp4').value=='') {alert('Please select Time!');} else { save_vitalsign_value(); clearAll('bp_temp','bp_temp2','bp_temp3','bp_temp8','bp_temp4'); }">  Save </a>
                    </div>
                    </div>   
                  </Div>  
                </div>  
             	</div>
           	</div>
            </Div>
        </div>
   </div>
   <Div class="clearfix margin_adjustment_only"></Div>
   <!-----------------------------------   Second Col      ---------------------------------------->
   
   <Div class="clearfix margin_adjustment_only"></Div>
   <?php
   	$defaultPostOpSite	=	'';
	if( $form_status <> 'completed' && $form_status <> 'not completed' && trim($postOpSite)=="")
	{
		$defaultPostOpSite	= $objManageData->getDefault('site','name');
		$postOpSite			= $defaultPostOpSite;
	}
		$txtarea_hgt = ( $version_num > 3 ) ? 'height:82px;' : ''; 
   ?>
   <div class="col-lg-4 col-sm-12 col-xs-12 col-md-4 ">
		<div class="well">
		  	<?php $preoprativeSiteSetValue = $calculatorTopChangeValue+22; //55 ?>
			 <Div class="row">
				<label class="date_r col-md-4 col-sm-4 col-xs-12 col-lg-4 plr5">
					<a id="pos_op_site" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="top" onClick="return showPostSiteFn('pos_op_site_area_id', '', 'no', parseInt(findPos_X('pos_op_site')-20), (parseInt($(this).offset().top - 228 ) < $(document).scrollTop() ? parseInt($(this).offset().top+10) : parseInt($(this).offset().top - 228 )  )),document.getElementById('selected_frame_name_id').value='';">
						<span class="fa fa-caret-right"></span> Post-Operative Site
					</a>
				</label>
       	<label class="col-md-8 col-sm-8 col-xs-12 col-lg-8 plr5">
					<textarea id="pos_op_site_area_id" class="form-control" style="font-weight:normal;resize:none; <?php echo $txtarea_hgt; ?><?php if($postOpSite){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?>" onFocus="changeTxtGroupColor(1,'pos_op_site_area_id');" onBlur="changeTxtGroupColor(1,'pos_op_site_area_id');" onKeyUp="changeTxtGroupColor(1,'pos_op_site_area_id');" name="pos_op_site_area"><?php echo stripslashes($postOpSite);?></textarea>	
				</label>
			 </Div>
		</div>           		
   </div>
   <Div class="clearfix visible-sm margin_adjustment_only"></Div>
  <div class="col-lg-4 col-sm-12 col-xs-12 col-md-4">
		<div class="well"> 
			<Div class="row">
				<label class="date_r col-md-4 col-sm-4 col-xs-12 col-lg-4 plr5">
					<a id="nourishmentkind" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " data-placement="top" onClick="return showNourishmentKind('nour_kind_area_id', '', 'no', parseInt(findPos_X('nourishmentkind')), (parseInt($(this).offset().top - 228 ) < $(document).scrollTop() ? parseInt($(this).offset().top+10) : parseInt($(this).offset().top - 228 )  )),document.getElementById('selected_frame_name_id').value='';">
						<span class="fa fa-caret-right"></span> Nourishment Kind
					</a>
				</label>	
				<label class="col-md-8 col-sm-8 col-xs-12 col-lg-8 plr5">
					<textarea id="nour_kind_area_id"class="form-control" name="nour_kind_area" style="resize:none;<?php echo $txtarea_hgt; ?>font-weight:normal;<?php if($nourishKind){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?>" onFocus="changeTxtGroupColor(1,'nour_kind_area_id');" onBlur="changeTxtGroupColor(1,'nour_kind_area_id');" onKeyUp="changeTxtGroupColor(1,'nour_kind_area_id');"><?php echo stripslashes($nourishKind);?></textarea>
				</label>
			</Div>
		</div>           		
   </div>
	
 	<Div class="clearfix visible-sm margin_adjustment_only"></Div>
  
  <div class="col-lg-4 col-sm-12 col-xs-12 col-md-4">
  	<div class="well"> 
    	<div class="row">
      	<input type="hidden" name="hidd_postOpSiteTime" id="hidd_timeId" value="<?php echo $hidd_postOpSiteTime;?>">
        <label class="date_r col-md-4 col-sm-4 col-xs-12 col-lg-4 plr5" for="time"> Time</label>	
        <label class="col-md-8 col-sm-8 col-xs-12 col-lg-8 plr5">
        	<input class="form-control" type="text" name="postOpSiteTime" id="bp_temp5" onFocus="changeTxtGroupColor(1,'bp_temp5');" onBlur="changeTxtGroupColor(1,'bp_temp5');" onKeyUp="changeTxtGroupColor(1,'bp_temp5');displayText5=this.value" onClick="this.style.backgroundColor='#FFFFFF';getShowNewPos(parseInt($(this).offset().top+1),parseInt(findPos_X('bp_temp5')),'flag5');if(this.value=='') {clearVal_c();return displayTimeAmPm('bp_temp5');}" value="<?php echo $postOpSiteTime;?>" style=" <?php if($postOpSiteTime){ echo $whiteBckGroundColor;} else{ echo $chngBckGroundColor;} ?>"/>
     		</label>
    	
      </div>
      <?php
				$getoptimeqry=imw_query ("select time from post_operative_site_time where	
																											patient_id='$patient_id' and
																											confirmation_id ='$pConfId'");
				$numrows=imw_num_rows($getoptimeqry); 		
				if($numrows>0){
					$i=1;
					while($getTime=imw_fetch_array($getoptimeqry)){
						//CODE TO SET OPERATIVE TIME
						$Time=$getTime["time"];
						$time_split2 = explode(":",$Time);
						if($time_split2[0]>12) {
							$am_pm2 = "PM";
						}else {
							$am_pm2 = "AM";
						}
						if($time_split2[0]>=13) {
							$time_split2[0] = $time_split2[0]-12;
							if(strlen($time_split2[0]) == 1) {
								$time_split2[0] = "0".$time_split2[0];
							}
						}else {
							//DO NOTHNING
						}
						echo $opTime = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
						$time=explode(":",$Time); 
						$timemin=$time[0].":".$time[1]."&nbsp;";
						$i++;
					}
				}
			?>
		</div>
    
    <?php if( $version_num > 3 ) { ?>
    <div class="well"> 
    	<div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 plr5">
        	<div class="rown">
          	<?php 
							$bsBgColor = $chngBckGroundColor;
							if ( !is_null($bs_na) || $bs_value <> '' ){
								$bsBgColor = $whiteBckGroundColor;
							}
						?>
          	<label class="col-xs-2 col-sm-2 col-md-4 col-lg-4 plr5">Blood&nbsp;Sugar</label>
           	<span class="col-xs-2 col-sm-2 col-md-3 col-lg-3 pd0">
            	<span class="rown">
                <span class=" col-xs-12 plr5 nowrap clpChkBx" onclick="changeDiffChbxColor(2,'chkBoxNS','bsvalue');" style="height:100%;">
                  <span class="colorChkBx" style=" <?php echo $bsBgColor; ?>">
                    <input type="checkbox" name="chkBoxNS" id="chkBoxNS" value="1" <?php echo ($bs_na ? 'checked' : ''); ?> onchange="javascript:if(this.checked==true){this.value='1';document.frm_post_op_nurse.bsvalue.value = '';}else{this.value='0';}">
                  </span><label for="chkBoxNS">N/A</label>
                </span>
              </span> 
           	</span>
            <div class="col-xs-8 col-sm-8 col-md-5 col-lg-5 plr5">	
            	<input class="form-control" type="text" name="bsvalue" id="bsvalue" onkeyup="changeDiffChbxColor(2,'chkBoxNS','bsvalue');bsValueUnchk(this,'frm_post_op_nurse');" onblur="changeDiffChbxColor(2,'chkBoxNS','bsvalue');bsValueUnchk(this,'frm_post_op_nurse');" value="<?php echo $bs_value;?>" style=" <?php echo $bsBgColor;?>" >
          	</div>
        	</div>
        </div>
      </div>
      
		</div>
   	<?php } ?>            		
	</div>
	   
   <Div class="clearfix margin_adjustment_only"> </Div>
   <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
		<div class="panel panel-default bg_panel_qint">
			<div class="panel-heading">
				<h3 class="panel-title rob"> IV Discontinued </h3>
			</div>
			  <Div class="clearfix margin_adjustment_only"> </Div>
			<Div class="panel-body post_O_check">
				<Div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
					<label for="pt_label">
						<input type="checkbox" name="chbx_removedIntact" id="chbx_removedIntact_id" <?php if($removedIntact=="Yes") { echo "checked";}?> value="Yes" /> &nbsp; Removed Intact/Pressure Dressing Applied</label>
				</Div>
				<Div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
					<Div class="row">
<?php
	$heparinLockOutBackColor=$chngBckGroundColor;
	if($heparinLockOutTime || $heparinLockOutNA){
		$heparinLockOutBackColor=$whiteBckGroundColor; 
	}
?>
						<label class="date_r col-md-1 col-sm-1 col-xs-12 col-lg-1" for="bp_temp6"> Time</label>
						<input type="hidden" name="hidd_time1Id" value="<?php echo $hidd_heparinLockOutTime;?>">
						<label class="col-md-11 col-sm-11 col-xs-12 col-lg-11">
							<input class="form-control" type="text" name="heparinLockOutTime" id="bp_temp6" onFocus="changeDiffChbxColor(2,'bp_temp6','heparinLockOutNAId');" onBlur="changeDiffChbxColor(2,'bp_temp6','heparinLockOutNAId');" onKeyUp="changeDiffChbxColor(2,'bp_temp6','heparinLockOutNAId');displayText6=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp6'))+22,parseInt(findPos_X('bp_temp6')),'flag6');if(this.value=='') {clearVal_c();displayTimeAmPm('bp_temp6');}changeDiffChbxColor(2,'bp_temp6','heparinLockOutNAId');" value="<?php echo $heparinLockOutTime;//echo date('h:i A');?>" style="font-weight:normal;<?php echo $heparinLockOutBackColor;?>"/>
						</label>
					</Div>
				</Div>
				<Div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
					<label for="pt_label">
						<span class="colorChkBx" style=" <?php echo $heparinLockOutBackColor;?>" >
							<input type="checkbox" onClick="changeDiffChbxColor(2,'bp_temp6','heparinLockOutNAId');" value="Yes" name="heparinLockOutNA" id="heparinLockOutNAId" <?php if($heparinLockOutNA=="Yes") { echo "checked"; }?> /></span> &nbsp; N/A</label>
				</Div>
<?php
	$getheparintimeqry=imw_query ("select time from heparin_lockout_time where	
				patient_id='$patient_id' and
				 confirmation_id ='$pConfId'");
	$numrows=imw_num_rows($getheparintimeqry); 		
	if($numrows>0){
		$i=1;
		while($getTime=imw_fetch_array($getheparintimeqry))	
		{
		
		 //CODE TO SET HEPARIN TIME
		 $Time=$getTime["time"];
	   $time_split2 = explode(":",$Time);
	   if($time_split2[0]>12) {
		   $am_pm2 = "PM";
	   }else {
		   $am_pm2 = "AM";
	   }
	   if($time_split2[0]>=13) {
		   $time_split2[0] = $time_split2[0]-12;
		   if(strlen($time_split2[0]) == 1) {
			   $time_split2[0] = "0".$time_split2[0];
		   }
	   }else {
		   //DO NOTHNING
	   }
		//echo $heparinTime = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
		//END CODE TO SET HEPARIN TIME	
		$time=explode(":",$Time); 
			$timemin=$time[0].":".$time[1]."&nbsp;";
	   $i++;
	   }
	}	
?>
				<Div class="clearfix margin_adjustment_only hidden-sm"></Div>
				<Div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
					<?php
						$patientMentalStatusBgColor = $chngBckGroundColor;
						if($patient_aox3=="Yes" || $other_mental_status ){
							$patientMentalStatusBgColor=$whiteBckGroundColor; 
						}
					?>
					<label for="pt_label">
					<span class="colorChkBx" style=" <?php echo $patientMentalStatusBgColor;?>" >
						<input type="checkbox" name="chbx_aox3" value="Yes" id="chbx_aox3_id" onClick="changeDiffChbxColor(2,'other_mental_status','chbx_aox3_id');" <?php if($patient_aox3=="Yes") { echo "checked";}?> /></span> &nbsp;Patient Awake, Alert and Oriented times 3</label>
         	<?php if($version_num > 2 ) { ?>  
         	<div class="row">
           	<div class="col-xs-3 col-md-5 col-lg-4 pt10	">
            	<label for="other_mental_status" class="text-nowrap pl7">Other Mental Status</label>
           	</div>
            <div class="col-xs-9 col-md-7 col-lg-8">
            	<textarea class="form-control" style="height:40px;<?php echo $patientMentalStatusBgColor;?>" name="other_mental_status" id="other_mental_status" placeholder="Other Mental Status..." onFocus="changeDiffChbxColor(2,'other_mental_status','chbx_aox3_id');" onBlur="changeDiffChbxColor(2,'other_mental_status','chbx_aox3_id');" onKeyUp="changeDiffChbxColor(2,'other_mental_status','chbx_aox3_id');" onClick="changeDiffChbxColor(2,'other_mental_status','chbx_aox3_id');"><?php echo $other_mental_status;?></textarea>
           	</div>
        	</div> 
          <?php } ?>      
				</Div>
<?php if($patientReleased2Adult=="Yes") { $relationDisplay="in";}else { $relationDisplay=""; $patientsRelationOtherDisplay = "none"; }?>
				<Div class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
					<label for="chbx_relationship_id" data-toggle="collapse" data-target="#toggle_rel">
						<input type="checkbox" onClick="javascript:list_text_disp('chbx_relationship_id','relation_heading_id','relation_id','txt_otherRelation_id');" name="chbx_patientReleased2Adult" value="Yes" <?php if($patientReleased2Adult=="Yes") { echo "checked"; }?> id="chbx_relationship_id"/> &nbsp;Patient Discharged To Home Via</label>
					<Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 collapse padding_0 <?php echo $relationDisplay;?>" id="toggle_rel">
					<Div class="row">
						<label class="date_r col-md-4 col-sm-4 col-xs-12 col-lg-4" for="relationlist_id">Relationship </label>	
						<label class="col-md-8 col-sm-8 col-xs-12 col-lg-8"> 
							<Select name="patientsRelationList" class="selectpicker form-control" id="relationlist_id" onChange="javascript:disp_hide_id('relationlist_id','txt_otherRelation_id');"> 
								<option value="Family" <?php if($patientsRelation=="Family") { echo "selected";}?>>Family</option>
								<option value="Husband" <?php if($patientsRelation=="Husband") { echo "selected";}?>>Husband</option>
								<option value="Wife" <?php if($patientsRelation=="Wife") { echo "selected";}?>>Wife</option>
								<option value="Son" <?php if($patientsRelation=="Son") { echo "selected";}?>>Son</option>
								<option value="Daughter" <?php if($patientsRelation=="Daughter") { echo "selected";}?>>Daughter</option>
								<option value="Sister" <?php if($patientsRelation=="Sister") { echo "selected";}?>>Sister</option>
								<option value="Brother" <?php if($patientsRelation=="Brother") { echo "selected";}?>>Brother</option>
								<option value="Mother" <?php if($patientsRelation=="Mother") { echo "selected";}?>>Mother</option>
								<option value="Father" <?php if($patientsRelation=="Father") { echo "selected";}?>>Father</option>
								<option value="Friend" <?php if($patientsRelation=="Friend") { echo "selected";}?>>Friend</option>
								<option value="Transportation Driver" <?php if($patientsRelation=="Transportation Driver") { echo "selected";}?>>Transportation Driver</option>
								<option value="other" <?php if($patientsRelation=="other") { echo "selected";}?>>Other</option>
							</Select> 
						</label>
<?php if($patientsRelation=="other") { $patientsRelationOtherDisplay = "block";} else { $patientsRelationOtherDisplay = "none";} ?>
					</Div>     
						<div id="txt_otherRelation_id" class="well" style="display:<?php echo $patientsRelationOtherDisplay;?>">
							<input class="form-control" name="txt_patientsRelationOther" placeholder="Other" type="text" value="<?php echo $patientsRelationOther; ?>" />
						</div>
					</Div>
				</Div>
			   <?php if($version_num > 4 ) { ?>
               <Div class="col-md-2 col-sm-12 col-xs-12 col-lg-2" id="">
					<label for=""><input type="checkbox" name="chbx_patient_transfer" value="Yes" id="chbx_patient_transfer_id" <?php if($patient_transfer=="Yes") { echo "checked";}?> /> &nbsp;Patient Transferred To Hospital</label>
               </Div>
               <?php } ?>
                <Div class="col-md-2 col-sm-12 col-xs-12 col-lg-2" id="">    
                    <label class="date_r col-md-7 col-sm-7 col-xs-12 col-lg-5" for="time">Discharge&nbsp;Time</label>
                    <label class="col-md-7 col-sm-7 col-xs-12 col-lg-7"> 
						<input class="form-control" type="text" name="dischargeTime" id="bp_temp7" onFocus="changeDiffChbxColor(1,'bp_temp7');" onBlur="changeDiffChbxColor(1,'bp_temp7');" onKeyUp="changeDiffChbxColor(1,'bp_temp7');displayText7=this.value" 
						 onClick="getShowNewPos(parseInt(findPos_Y('bp_temp7'))-15,parseInt(findPos_X('bp_temp7')),'flag7');if(this.value=='') {clearVal_c();return displayTimeAmPm('bp_temp7');};changeDiffChbxColor(1,'bp_temp7')" value="<?php echo $dischargeTime;?>" style="font-weight:normal;<?php echo $dischargeTime?$whiteBckGroundColor:$chngBckGroundColor;?>"/> 
                    </label>
				</Div>
			   
			   <Div class="clearfix margin_adjustment_only"></Div>
			</Div> <!--- - - --- - Panel Body - ------------>
		</div>           
   </div>
               
   <?php
    //SHOW OR TIME FORM operatingroomrecords
	$opRoomOrQry 				= "SELECT surgeryTimeIn, surgeryStartTime, surgeryEndTime, surgeryTimeOut FROM operatingroomrecords WHERE confirmation_id = '".$_REQUEST["pConfId"]."' LIMIT 0,1 ";
    $opRoomOrRes 				= imw_query($opRoomOrQry) or die(imw_error());
    $opRoomOrNumRow 			= imw_num_rows($opRoomOrRes);
    $surgeryTimeInOpRoom = $surgeryStartTimeOpRoom = $surgeryEndTimeOpRoom = $surgeryTimeOutOpRoom = "";
	if($opRoomOrNumRow>0) {
		$opRoomOrRow			= imw_fetch_array($opRoomOrRes);
		$surgeryTimeInOpRoom 	= $objManageData->getTmFormat($opRoomOrRow["surgeryTimeIn"]);
		$surgeryStartTimeOpRoom = $objManageData->getTmFormat($opRoomOrRow["surgeryStartTime"]);
		$surgeryEndTimeOpRoom 	= $objManageData->getTmFormat($opRoomOrRow["surgeryEndTime"]);
		$surgeryTimeOutOpRoom 	= $objManageData->getTmFormat($opRoomOrRow["surgeryTimeOut"]);
	}
   ?>
   <Div class="clearfix margin_adjustment_only"> </Div>
   <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
		<div class="panel panel-default bg_panel_qint">
			<div class="panel-heading">
				<h3 class="panel-title rob"> Surgery (OR) </h3>
			</div>
			  <Div class="clearfix margin_adjustment_only"> </Div>
			<Div class="panel-body post_O_check">
                <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <label for="">  In Room Time <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php echo ($surgeryTimeInOpRoom ? $surgeryTimeInOpRoom : "______");?></span></label>
                    <label for="">  Surgery Start Time <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php echo ($surgeryStartTimeOpRoom ? $surgeryStartTimeOpRoom : "______");?></span></label>
                    <label for="">  Surgery End Time <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php echo ($surgeryEndTimeOpRoom ? $surgeryEndTimeOpRoom : "______");?></span></label>
                    <label for="">  Out of Room <span style="color:#0033CC; font-weight:normal; margin-right:80px;"><?php echo ($surgeryTimeOutOpRoom ? $surgeryTimeOutOpRoom : "______");?></span></label>
				</Div>
				<Div class="clearfix margin_adjustment_only hidden-sm"></Div>
			</Div> <!--- - - --- - Panel Body - ------------>
		</div>           
   </div>	
	<?php
    $postopNurseCheckListQry 			= "SELECT postopNurseChecklistId AS checkListTemplateId, name AS postOpNurseQuestionName FROM postop_nurse_checklist ORDER BY  `name` ";
    if($form_status=='completed' || $form_status=='not completed') {
        $postopNurseCheckListQry 		= "SELECT checkListTemplateId, postOpNurseQuestionName, postOpNurseOption FROM patient_postop_nurse_checklist WHERE confirmation_id = '".$_REQUEST["pConfId"]."' ORDER BY `postOpNurseQuestionName` ";
    }
    $postopNurseCheckListRes 			= imw_query($postopNurseCheckListQry) or die(imw_error());
    $postopNurseCheckListNumRow 		= imw_num_rows($postopNurseCheckListRes);
    if($postopNurseCheckListNumRow>0) {
    ?>
       <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            <div class="panel panel-default bg_panel_qint">
                <?php
				for($w=1;$w<=2;$w++) {
				?>
                <div class="panel-heading col-lg-6 col-sm-12 col-xs-12 col-md-6">
                    <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9"><h3 class="panel-title rob"><?php echo ($w==1) ? "Nurse Post-Op Checklist" : "&nbsp;"; ?></h3></div>
                    <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-left">
                        <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6"><h3 class="panel-title rob"><?php echo ($w==2 && $postopNurseCheckListNumRow<=1) ? "&nbsp;" : "Yes"; ?></h3></div>
                        <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6"><h3 class="panel-title rob"><?php echo ($w==2 && $postopNurseCheckListNumRow<=1) ? "&nbsp;" : "No"; ?></h3></div>
                    </div>
                </div>
                <?php
				}
				?>
                <Div class="clearfix margin_adjustment_only"> </Div>
                <Div class="panel-body post_O_check">
                    <?php    
                    while($postopNurseCheckListRow=imw_fetch_array($postopNurseCheckListRes)) {
                        $checkListTemplateId 		= $postopNurseCheckListRow['checkListTemplateId'];
                        $postopNurseCheckListName 	= $postopNurseCheckListRow['postOpNurseQuestionName'];
                        $postopNurseCheckListOption = $postopNurseCheckListRow['postOpNurseOption'];
                    ?>
                        <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                          <div class="well"> 
                               <Div class="row" style="height:35px;">
                                    <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                        <label class="date_r col-md-12 col-sm-12 col-xs-12 col-lg-12"><?php echo $postopNurseCheckListName;?></label>
                                    </div>
                                    <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                        <div class="">
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">	
                                                <span class="colorChkBx" style=" <?php if($postopNurseCheckListOption) { echo $whiteBckGroundColor;}?>">
                                                    <input type="checkbox" onClick="javascript:checkSingle('nrs_chklist_<?php echo $checkListTemplateId;?>_yes','nrs_chklist_<?php echo $checkListTemplateId;?>'),changeChbxColor('nrs_chklist_<?php echo $checkListTemplateId;?>')" <?php if($postopNurseCheckListOption=='Yes') echo "CHECKED"; ?> value="Yes" name="nrs_chklist_<?php echo $checkListTemplateId;?>" id="nrs_chklist_<?php echo $checkListTemplateId;?>_yes">
                                                </span>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">	
                                                <span class="colorChkBx" style=" <?php if($postopNurseCheckListOption) { echo $whiteBckGroundColor;}?>">
                                                    <input type="checkbox" onClick="javascript:checkSingle('nrs_chklist_<?php echo $checkListTemplateId;?>_no','nrs_chklist_<?php echo $checkListTemplateId;?>'),changeChbxColor('nrs_chklist_<?php echo $checkListTemplateId;?>')" <?php if($postopNurseCheckListOption=='No') echo "CHECKED"; ?> value="No" name="nrs_chklist_<?php echo $checkListTemplateId;?>" id="nrs_chklist_<?php echo $checkListTemplateId;?>_no">
                                                </span>
                                            </div>
                                        </div> 
                                    </div>
                                </Div>
                            </div>
                        </div>
                        <Div class="clearfix visible-sm margin_adjustment_only"></Div>
                    <?php
                    }
                    ?>
                </Div>
            </div>
       </div>         
    <?php			
    }
    ?>                            
               
   <div id="descr_21" class="inner_safety_wrap" style="height: auto;">
   <?php
   	$defaultRComments	=	'';
	if( trim($recoveryComments) == '' &&  $form_status <> 'completed' && $form_status <> 'not completed' )
	{
		$defaultRComments	= $objManageData->getDefault('recoverycomments','recoveryComments',"\n");
		$recoveryComments	= $defaultRComments;
	}
   
   
   ?>
		<div class="well">
			<div class="row">
				<div class="col-md-4 col-sm-12 col-xs-4 col-lg-1">
					<a id="comment_id" data-placement="top" class="panel-title rob alle_link show-pop-list_g btn btn-default" onClick="return showRecoveryCommentsFn('recv_comm_area_id', '', 'no', parseInt(findPos_X('comment_id')), parseInt(findPos_Y('comment_id'))-225),document.getElementById('selected_frame_name_id').value='';"><span class="fa fa-caret-right"></span>Comments </a>
				</div>
				<div class="clearfix visible-sm margin_adjustment_only"></div>
				<div class="col-md-8 col-sm-12 col-xs-8 col-lg-11 text-center">
					<textarea id="recv_comm_area_id" name="recv_comm_area" class="form-control" style="resize:none;"><?php echo stripslashes($recoveryComments);?></textarea> 
				</div> <!-- Col-3 ends  -->
			</div>
		</div> 
	 </div>
 	
  	<?php
		$qryQualityMeasures="Select qualityId,qualityName,qualityStatus from qualitymeasures where  confirmation_id = '".$_REQUEST["pConfId"]."' order by qualityName";//First Check Record on User Table
		$resQualityMeasuresAdmin=imw_query($qryQualityMeasures);
		$getNumberRow=imw_num_rows($resQualityMeasuresAdmin);
		$runOn="userTbl";
		
		if($getNumberRow<=0){
			
			$qryQualityMeasuresAdmin="Select qualityMeasuresId,name,status from qualitymeasuresadmin where status='active' order by name ";
			$resQualityMeasuresAdmin=imw_query($qryQualityMeasuresAdmin);
			$getNumberRow=imw_num_rows($resQualityMeasuresAdmin);
			$runOn="adminTbl";
		}
		$colorCtr=1;
		$tdCtr=1;
		if($getNumberRow>0)
		{
	?>
			<div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            	<div class="panel panel-default bg_panel_qint">
                <?php for($w=1;$w<=2;$w++) { ?>
                	<div class="panel-heading col-lg-6 col-sm-12 col-xs-12 col-md-6">
                    	<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9"><h3 class="panel-title rob"><?php echo ($w==1) ? "ASC Quality Control Measures" : "&nbsp;"; ?></h3></div>
                        <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-left">
                        	<div class="col-md-6 col-sm-6 col-xs-6 col-lg-6"><h3 class="panel-title rob"><?php echo ($w==2 && $postopNurseCheckListNumRow<=1) ? "&nbsp;" : "Yes"; ?></h3></div>
                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6"><h3 class="panel-title rob"><?php echo ($w==2 && $postopNurseCheckListNumRow<=1) ? "&nbsp;" : "No"; ?></h3></div>
                      	</div>
                  	</div>
                <?php
				}
				?>
                <Div class="clearfix margin_adjustment_only"> </Div>
                <Div class="panel-body post_O_check">
                <?php
					while($rowsQualityMeasuresAdmin = imw_fetch_array($resQualityMeasuresAdmin)) {
						if($runOn=="userTbl"){
							$qualityName=$rowsQualityMeasuresAdmin['qualityName'];
							$getQualityStatus=$rowsQualityMeasuresAdmin['qualityStatus'];
						}
						if($runOn=="adminTbl"){
							$qualityName=$rowsQualityMeasuresAdmin['name'];
						}
             	?>
                        <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                          <div class="well"> 
                               <Div class="row" style="height:35px;">
                                    <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                        <input type="hidden" name="qualityDes<?=$tdCtr?>" value="<?php echo($qualityName);  ?>">
                                        <label class="date_r col-md-12 col-sm-12 col-xs-12 col-lg-12"><?php echo $qualityName;?></label>
                                  	</div>
                                    <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                        <div class="">
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">	
                                                <span>
                                                	<input type="checkbox" <?php  if($runOn=="userTbl"){ if($getQualityStatus=="Yes"){ echo "checked"; } } ?> onClick="javascript:checkSingle('chkBox_Yes<?=$tdCtr?>','qtyBox<?=$tdCtr?>');" value="Yes" name="qtyBox<?=$tdCtr?>" id="chkBox_Yes<?=$tdCtr?>" />
                                              	</span>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6">	
                                                <span>
                                                	<input type="checkbox" <?php if($runOn=="userTbl"){ if($getQualityStatus=="No"){ echo "checked"; } } ?> onClick="javascript:checkSingle('chkBox_No<?=$tdCtr?>','qtyBox<?=$tdCtr?>');" value="No" name="qtyBox<?=$tdCtr?>" id="chkBox_No<?=$tdCtr?>">
                                                </span>
                                            </div>
                                        </div> 
                                    </div>
                                </Div>
                            </div>
                        </div>
                        <Div class="clearfix visible-sm margin_adjustment_only"></Div>
                    <?php
                    	$tdCtr++;
					}
                    ?>
                </Div>
            </div>
       </div>	
	  
    <?php
		}
	?>
    
    <div class="clearfix border-dashed margin_adjustment_only"></div>
   	<Div class="clearfix margin_adjustment_only"></Div>    
 
<?php
	$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
	$loggedInUserType = $ViewUserNameRow["user_type"];
	$loggedInSignatureOfNurse = $ViewUserNameRow["signature"];
	
	$signNoToggle = false;
	if($loggedInUserType<>"Nurse") {
		$loginUserName = $_SESSION['loginUserName'];
		$callJavaFun = "return noAuthorityFunCommon('Nurse');";
		$signNoToggle = true;
	}else {
		$loginUserId = $_SESSION["loginUserId"];
		$callJavaFun = "document.frm_post_op_nurse.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_nursing_record_ajaxSign.php','$loginUserId');";
	}

	$signOnFileStatus = "Yes";
	$TDnurseNameIdDisplay = "";
	$TDnurseSignatureIdDisplay = "";
	$NurseNameShow = $loggedInUserName;
	
	if($signNurseId<>0 && $signNurseId<>"") {
		$NurseNameShow = $signNurseName;
		$signOnFileStatus = $signNurseStatus;
		//$singNursePostOpDateTime=date("m-d-Y h:i A",strtotime($signNurseDateTime));
		$singNursePostOpDateTime = $objManageData->getFullDtTmFormat($signNurseDateTime);
		
		$TDnurseNameIdDisplay = "none";
		$TDnurseSignatureIdDisplay = "in";
	}
	
	if($_SESSION["loginUserId"]==$signNurseId) {
		$callJavaFunDel = "document.frm_post_op_nurse.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_nursing_record_ajaxSign.php','$loginUserId','delSign');";
	}else {
		$callJavaFunDel = "alert('Only $signNurseName can remove this signature');";
		$signNoToggle = true;
	}
	
	$nurseSignBackColor=$chngBckGroundColor;
	if($signNurseId!=0){
		$nurseSignBackColor==$whiteBckGroundColor; 
	}
?>
    <div class=" col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="inner_safety_wrap" id="TDnurseNameId">
			<a style="display:<?php echo $TDnurseNameIdDisplay; ?>" <?php if(!$signNoToggle){ ?>data-target="#TDnurseSignatureId" data-toggle="collapse" <?php } ?>class="sign_link collapsed" href="javascript:void(0);" onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</a>
		</div>
		<div id="TDnurseSignatureId" class="inner_safety_wrap collapse <?php echo $TDnurseSignatureIdDisplay;?>" style="height: 6px;">
			<span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunDel;?>">Nurse: <?php echo $NurseNameShow; ?>  </span>	     
			<span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus; ?></span>
			<span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $singNursePostOpDateTime; ?> <span class="fa fa-edit"></span></span></span>
		</div>
	</div>
	<div class="col-md-4 col-lg-4 col-xs-12 hidden-sm">
	
	</div>
		 
   <div class="col-md-4 col-sm-6 col-lg-4 col-xs-12 pull-right">
		<div class="inner_safety_wrap">
			<label class="col-md-6 col-lg-6 col-xs-12 col-sm-12" for="relivedNurseIdList"> Relief Nurse</label>
			<Div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
				<select class="selectpicker form-control" name="relivedNurseIdList" id="relivedNurseIdList"> 
					<option value="">Select</option>	
					<?php
						$relivedNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
						$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
						while($relivedNurseRow=imw_fetch_array($relivedNurseRes)) {
							$relivedSelectNurseID = $relivedNurseRow["usersId"];
							$relivedNurseName = $relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"];
							$sel="";
							if($relivedNurseId==$relivedSelectNurseID) {
								$sel = "selected";
							} 
							else {
								$sel = "";
							}
							if($relivedNurseRow["deleteStatus"]<>'Yes' || $relivedNurseId==$relivedSelectNurseID) {						
						?>	
								<option value="<?php echo $relivedSelectNurseID;?>" <?php echo $sel;?>><?php echo $relivedNurseName;?></option>
						<?php
							}
						}
					?>                                            
				</select>
			</Div>
		</div>
	</div>
  <Div class="clearfix margin_adjustment_only"></Div>
  <Div class="clearfix margin_adjustment_only"></Div>  
</div>

</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="post_op_nursing_record.php?cancelRecord=true<?php echo $saveLink;?>" target="_self" autocomplete="off">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->
    
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "post_op_nursing_record.php";
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

?>
<script>
/*function color_highlight(){

 var value_bp_temp = document.frm_post_op_nurse.bp_temp.value;
 var value_bp_temp2 = document.frm_post_op_nurse.bp_temp2.value;
 var value_bp_temp3 = document.frm_post_op_nurse.bp_temp3.value;
 var value_bp_temp4 = document.frm_post_op_nurse.bp_temp4.value;
 var value_pos_op_site_area = document.frm_post_op_nurse.pos_op_site_area_id.value;
 var value_nour_kind_area_id = document.frm_post_op_nurse.nour_kind_area_id.value;
 var value_bp_temp5  = document.frm_post_op_nurse.bp_temp5.value;
 var value_bp_temp6 = document.frm_post_op_nurse.bp_temp6.value;

 if(value_bp_temp==''){
	document.frm_post_op_nurse.bp_temp.style.backgroundColor = '#F6C67A';
 }

 if(value_bp_temp2==''){
	document.frm_post_op_nurse.bp_temp2.style.backgroundColor = '#F6C67A';
 }

 if(value_bp_temp3==''){
	document.frm_post_op_nurse.bp_temp3.style.backgroundColor = '#F6C67A';
 }
 
 if(value_bp_temp4==''){
	document.frm_post_op_nurse.bp_temp4.style.backgroundColor = '#F6C67A';
 }

 if(value_pos_op_site_area==''){
	document.frm_post_op_nurse.pos_op_site_area_id.style.backgroundColor = '#F6C67A';
 }

 if(value_nour_kind_area_id==''){
	document.frm_post_op_nurse.nour_kind_area_id.style.backgroundColor = '#F6C67A';
 }

 if(value_bp_temp5==''){
	document.frm_post_op_nurse.bp_temp5.style.backgroundColor = '#F6C67A';
 }
 
  if(value_bp_temp6==''){
	document.frm_post_op_nurse.bp_temp6.style.backgroundColor = '#F6C67A';
 }

}*/
if(parent.parent) {
	if(typeof(parent.parent.show_loading_image)!="undefined") {
		parent.parent.show_loading_image('none');
	}
}
</script>
<?php include("print_page.php");?>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>