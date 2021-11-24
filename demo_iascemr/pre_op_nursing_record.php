<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 3;
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.webui-popover.css" />
<script type="text/javascript">
if (typeof(jQuery) == "undefined") {
	var iframeBody = document.getElementsByTagName("body")[0];
	var jQuery = function (selector) { return parent.jQuery(selector, iframeBody); };
	var $ = jQuery;
}
</script>
<script src="js/jquery.webui-popover.js"></script>
<script type="text/javascript" src="js/external-tooltip.js"></script>
<style>
	.margin_adjustment_only { margin : 0 !important; }
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
$tablename = "preopnursingrecord";
include_once("common/commonFunctions.php");
$spec = "
</head>

<body onClick=\"closeEpost(); return top.frames[0].main_frmInner.hideSliders();\" id=\"slider_pre_op_nurse\" onLoad=\"top.changeColor('".$bgcolor_pre_op_nursing_order."');\">
";

include("common/link_new_file.php");
//START INCLUDE PREDEFINE FUNCTIONS
	include_once("common/food_list_pop.php"); //PRE OP  NURSING
	include_once("common/pre_comments.php"); //PRE OP  NURSING
//END INCLUDE PREDEFINE FUNCTIONS

include_once("admin/classObjectFunction.php");

$objManageData = new manageData;
extract($_GET);
$SaveForm_alert = $_REQUEST['SaveForm_alert'];

		//GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION  
			if($nurseId=="" || $nurseId==0) {
				$ViewNurseIdQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
				$ViewNurseIdRes = imw_query($ViewNurseIdQry) or die(imw_error()); 
				$ViewNurseIdRow = imw_fetch_array($ViewNurseIdRes); 
				$nurseId = $ViewNurseIdRow["nurseId"];
			}	
		//END GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION  
		
		$nurseIDProfile = trim($nurseId);
		if(($nurseIProfile=="" || $nurseIDProfile==0) && $_SESSION['loginUserType']=="Nurse") {
			$nurseIDProfile = $_SESSION['loginUserId'];	
		}
	
	//GET NURSE NAME
		$ViewNurseNameQry = "select * from `users` where  usersId = '".$nurseId."'";
		$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
		$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
		$NurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
	//END GET NURSE NAME

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
		
		$fieldName = "pre_op_nursing_form";
		$pageName = "pre_op_nursing_record.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId";
		if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
			$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId";
		}
		include("left_link_hide.php");
	//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 

	$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;ascId='.$ascId;

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

// Getting PRe op Physician Order Chart Version/Form Status
$preOpPhyQry = "Select version_num, form_status from preopphysicianorders Where patient_confirmation_id = ".$_REQUEST['pConfId']." ";
		$preOpPhySql = imw_query($preOpPhyQry);
		$preOpPhyRow = imw_fetch_object($preOpPhySql);
		
		$preOpPhyVersionNum = $preOpPhyRow->version_num;
		$preOpPhyFormStatus = $preOpPhyRow->form_status;  
		
		if(!($preOpPhyVersionNum) && ($preOpPhyFormStatus == 'completed' || $preOpPhyFormStatus == 'not completed')) { $preOpPhyVersionNum	=	1; }
		else if(!($preOpPhyVersionNum) && $preOpPhyFormStatus <> 'completed' && $preOpPhyFormStatus <> 'not completed') { $preOpPhyVersionNum	=	2; }
// End Getting PRe op Physician Order Chart Version/Form Status

		
	//SAVE RECORD TO DATABASE
if($_POST['SaveRecordForm']=='yes'){

	//CODE FOR DYNAMIC OPTIONS FROM ADMIN	
	$chkPreOpNurseQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPreOpNurseRes = imw_query($chkPreOpNurseQry) or die(imw_error()); 
	$chkPreOpNurseNumRow = imw_num_rows($chkPreOpNurseRes);
	if($chkPreOpNurseNumRow>0) {
		//CODE START TO CHECK FORM STATUS
			$chkPreOpNurseFormStatusRow = imw_fetch_array($chkPreOpNurseRes);
			$chkPreOpNurseFormStatus = $chkPreOpNurseFormStatusRow['form_status'];
			$chkPreOpNurseVersionNum = $chkPreOpNurseFormStatusRow['version_num'];
			$chkPreOpNurseVersionDateTime = $chkPreOpNurseFormStatusRow['version_date_time'];
		//CODE START TO CHECK FORM STATUS
	}
	
	if($chkPreOpNurseFormStatus!='completed' && $chkPreOpNurseFormStatus!='not completed') {	
		
		$chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='".$_REQUEST["pConfId"]."'";
		$chkpreopnursequestionadminRes = imw_query($chkpreopnursequestionadminQry) or die(imw_error());
		$chkpreopnursequestionadminNumRow = imw_num_rows($chkpreopnursequestionadminRes);
		if($chkpreopnursequestionadminNumRow>0) {
			//DO NOTHING
		}else {
			$preOpNurseSavecategoryQry = "SELECT * FROM preopnursecategory ORDER BY categoryId";
			$preOpNurseSavecategoryRes = imw_query($preOpNurseSavecategoryQry) or die(imw_error());
			$preOpNurseSavecategoryNumRow = imw_num_rows($preOpNurseSavecategoryRes);
			if($preOpNurseSavecategoryNumRow>0) {
				$k=0;
				while($preOpNurseSavecategoryRow = imw_fetch_array($preOpNurseSavecategoryRes)) {
					$categoryId = $preOpNurseSavecategoryRow['categoryId'];
					$categoryName = $preOpNurseSavecategoryRow['categoryName'];
					$k++;
			
					$preOpNurseSavequestionQry = "SELECT * FROM preopnursequestion WHERE preOpNurseCatId='".$categoryId."'";
					$preOpNurseSavequestionRes = imw_query($preOpNurseSavequestionQry) or die(imw_error());
					$preOpNurseSavequestionNumRow = imw_num_rows($preOpNurseSavequestionRes);
					if($preOpNurseSavequestionNumRow>0) {
						$t=0;
						while($preOpNurseSavequestionRow=imw_fetch_array($preOpNurseSavequestionRes)) {
							$t++;
							$preOpNurseSaveQuestionId = $preOpNurseSavequestionRow['preOpNurseQuestionId'];
							$preOpNurseSaveQuestionName = stripslashes($preOpNurseSavequestionRow['preOpNurseQuestionName']);
							$showTxtBoxStatus = $preOpNurseSavequestionRow['showTxtBoxStatus'];
							$preOpNurseSaveChkBoxQuestionName = str_replace(' ','_',$preOpNurseSaveQuestionName);
							$preOpNurseSaveChkBoxQuestionName = str_replace('.','SXD',$preOpNurseSaveChkBoxQuestionName);
							$preOpNurseSaveChkBoxQuestionName = str_replace('[','SXOSB',$preOpNurseSaveChkBoxQuestionName);
							
							$inspreOpNurseAdminQry = "INSERT INTO preopnursequestionadmin SET 
													   categoryName='".addslashes($categoryName)."',
													   preOpNurseQuestionName='".addslashes($preOpNurseSaveQuestionName)."',
													   preOpNurseOption='".addslashes($_REQUEST[$preOpNurseSaveChkBoxQuestionName.$k.$t])."',
													   showTxtBoxStatus	='".$showTxtBoxStatus."',
													   confirmation_id	='".$_REQUEST["pConfId"]."',
													   patient_id	='".$patient_id."'
													 ";
							$inspreOpNurseAdminRes = imw_query($inspreOpNurseAdminQry) or die(imw_error());						 
						}//END INNER WHILE
					}//END IF($preOpNurseSavequestionNumRow>0)
				}//END OUTER WHILE
			}//END IF($preOpNurseSavecategoryNumRow>0)
		}//END ELSE PART		
	}
	else if($chkPreOpNurseFormStatus=='completed' || $chkPreOpNurseFormStatus=='not completed') {	
		
		$preOpNurseSavecategoryQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='".$_REQUEST["pConfId"]."' GROUP BY categoryName ORDER BY id";
		$preOpNurseSavecategoryRes = imw_query($preOpNurseSavecategoryQry) or die(imw_error());
		$preOpNurseSavecategoryNumRow = imw_num_rows($preOpNurseSavecategoryRes);
		if($preOpNurseSavecategoryNumRow>0) {
			$k=0;
			while($preOpNurseSavecategoryRow = imw_fetch_array($preOpNurseSavecategoryRes)) {
				$categoryName = $preOpNurseSavecategoryRow['categoryName'];
				$k++;
				$preOpNurseSavequestionQry = "SELECT * FROM preopnursequestionadmin WHERE categoryName='".$categoryName."' AND confirmation_id ='".$_REQUEST["pConfId"]."' ORDER BY id";
				$preOpNurseSavequestionRes = imw_query($preOpNurseSavequestionQry) or die(imw_error());
				$preOpNurseSavequestionNumRow = imw_num_rows($preOpNurseSavequestionRes);
				if($preOpNurseSavequestionNumRow>0) {
					$t=0;
					while($preOpNurseSavequestionRow=imw_fetch_array($preOpNurseSavequestionRes)) {
						$t++;
						$preOpNurseSaveId = $preOpNurseSavequestionRow['id'];
						$preOpNurseSaveQuestionName = stripslashes($preOpNurseSavequestionRow['preOpNurseQuestionName']);
						$preOpNurseSaveChkBoxQuestionName = str_replace(' ','~',$preOpNurseSaveQuestionName);
						$preOpNurseSaveChkBoxQuestionName = str_replace('.','SXD',$preOpNurseSaveChkBoxQuestionName);
						$preOpNurseSaveChkBoxQuestionName = str_replace('[','SXOSB',$preOpNurseSaveChkBoxQuestionName);
						$preOpNurseSaveOption = stripslashes($preOpNurseSavequestionRow['preOpNurseOption']);
					
						$chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='".$_REQUEST["pConfId"]."' AND id='".$preOpNurseSaveId."'";
						$chkpreopnursequestionadminRes = imw_query($chkpreopnursequestionadminQry) or die(imw_error());
						$chkpreopnursequestionadminNumRow = imw_num_rows($chkpreopnursequestionadminRes);
						if($chkpreopnursequestionadminNumRow>0) {
							
							$updatePreOpNurseAdminQry = "UPDATE preopnursequestionadmin SET 
													   preOpNurseOption='".addslashes($_REQUEST[$preOpNurseSaveChkBoxQuestionName.$k.$t])."'
													   WHERE id='".$preOpNurseSaveId."'
													   AND confirmation_id	='".$_REQUEST["pConfId"]."'
													 ";
							
							$updatePreOpNurseAdminRes = imw_query($updatePreOpNurseAdminQry) or die(imw_error());						 
							
						}else {
							//DO NOTHING
						}
					
					}
				}
			}
		}	
	}	
//END CODE FOR DYNAMIC QUESTION FROM ADMIN
	
	$version2Query = "";
	$version_num	=	$chkPreOpNurseVersionNum;
	if(!$chkPreOpNurseVersionNum)
	{
		$version_date_time	=	$chkPreOpNurseVersionDateTime;
		if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
		{
			$version_date_time	=	date('Y-m-d H:i:s');
		}
				
		if($chkPreOpNurseFormStatus == 'completed' || $chkPreOpNurseFormStatus=='not completed'){
			$version_num = 1;
		}else{
			$version_num	=	$current_form_version;
		}
		
		$version2Query .= ", version_num =	'".$version_num."', version_date_time	=	'".$version_date_time."' ";
	}
	
	if( ($version_num > 1 && $preOpPhyVersionNum <> 1 ) || $preOpPhyVersionNum > 1 )
	{	
		$comments = addslashes($_REQUEST['comments']);
		$chbx_saline_lockStart = $_REQUEST['chbx_saline_lockStart'];
		$chbx_saline_lock = $_REQUEST['chbx_saline_lock'];
		$ivSelection = $_REQUEST['ivSelection'];
		$ivSelectionOther = $_REQUEST['ivSelectionOther'];
		$ivSelectionSide =	$_REQUEST['ivSelectionSide'];
		
		$gauge = $gauge_other = $txtbox_other_new = $chbx_KVO = $chbx_rate = $txtbox_rate = $chbx_flu = $txtbox_flu 	= '';
		if($chbx_saline_lock=='iv') {
			$chbx_KVO = $_REQUEST['chbx_KVO'];	
			$chbx_rate = $_REQUEST['chbx_rate'];	
			$txtbox_rate = addslashes($_REQUEST['txtbox_rate']);	
			$chbx_flu = $_REQUEST['chbx_flu'];	
			$txtbox_flu = addslashes($_REQUEST['txtbox_flu']);	
		}
		if(($chbx_saline_lock=='iv' || $chbx_saline_lockStart=='saline') && $ivSelection <> '' && $ivSelection <> 'other' )
		{
			$gauge = $_REQUEST['gauge'];
			$gauge_other	=	($gauge == 'other') ? addslashes($_REQUEST['gauge_other']) : '' ;	
			$txtbox_other_new = addslashes($_REQUEST['txtbox_other_new']);
		}
	
		$version2Query	.=	", comments = '".$comments."', chbx_saline_lockStart = '".$chbx_saline_lockStart."', chbx_saline_lock = '".$chbx_saline_lock."', ivSelection = '".$ivSelection."', ivSelectionOther = '".$ivSelectionOther."', ivSelectionSide =	'".$ivSelectionSide."', chbx_KVO	=	'".$chbx_KVO."', chbx_rate = '".$chbx_rate."', txtbox_rate = '".$txtbox_rate."', chbx_flu =	'".$chbx_flu."', txtbox_flu	= '".$txtbox_flu."', gauge = '".$gauge."', txtbox_other_new = '".$txtbox_other_new."', gauge_other	=	'".$gauge_other."'";
	}
	
	$chkBoxNSChk = $_REQUEST['chkBoxNS'];
	$bsvalueChk = ($chkBoxNSChk) ? '' : $_REQUEST['bsvalue'];
	$text = $_REQUEST['getText'];
	$tablename = "preopnursingrecord";
	$allergies_status_reviewed = $_POST["chbx_drug_react_reviewed"];
	
	//$preopNurseTime = trim($_POST['preopNurseTime']);
	$preopNurseTime = $objManageData->setTmFormat(trim($_POST['preopNurseTime']),'static');
	$foodDrinkToday = $_POST["chbx_fdt"];
	$listFoodTake = addslashes($_POST["txtarea_list_food_take"]);
	$labTest = $_POST["chbx_lab_test"];
	$ekg = $_POST["chbx_ekg"];
	$consentSign = $_POST["chbx_cons_sign"];
	$hp = $_POST["chbx_h_p"];
	$admitted2Hospital = $_POST["chbx_admit_to_hosp"];
	$reason = addslashes($_POST["txtarea_admit_to_hosp"]);
	if($admitted2Hospital=="" || $admitted2Hospital=="No") {
		$reason = "";
	}
	$healthQuestionnaire = $_POST["chbx_hlt_ques"];
	$standingOrders = $_POST["chbx_stnd_odrs"];
	$patVoided = $_POST["chbx_pat_void"];
	
	$hearingAids = $_POST["chbx_hearingAids"];
	$hearingAidsRemoved = $_POST["chbx_hearingAidsRemoved"];
	if($hearingAids=="" || $hearingAids=="No") {
		$hearingAidsRemoved = "";
	}
	$denture = $_POST["chbx_denture"];
	$dentureRemoved = $_POST["chbx_dentureRemoved"];
	if($denture=="" || $denture=="No") {
		$dentureRemoved = "";
	}
	$anyPain = $_POST["chbx_anyPain"];
	$painLevel = $_POST["painLevel"];
	$painLocation = addslashes($_POST["painLocation"]);
	$doctorNotified = $_POST["chbx_doctorNotified"];
	$feet = $_POST["txt_feet"];
	$inch = $_POST["txt_inch"];
	if($feet<>"" && $inch<>"") {
		$patientHeight = addslashes($feet."'".$inch);
	}else {
		$patientHeight = "";
	}
	$patientWeight	= $_POST["txt_patientWeight"];
	$patientBMI			=	$_POST['txt_patientBmi'];
	/*if(!$patientBMI && $patientHeight && $patientWeight)
	{
		$heightInches	=	($feet*12) + $inch;
		$BMIValue	=	$patientWeight * 703 / ($heightInches * $heightInches);	
		$BMIValue	=	number_format($BMIValue,2,'.','');
		$patientBMI	=	$BMIValue ;
		
	}*/
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	
	$vitalSignBpsub  = $_POST["txt_vitalSignBp"];
	$vitalSignBpsubstr  = 	substr($vitalSignBpsub,0,7);
	$vitalSignBp  = $vitalSignBpsubstr;
	//END OF CODE
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignPsub  = $_POST["txt_vitalSignP"];
	$vitalSignPsubstr = substr($vitalSignPsub,0,3); 
	$vitalSignP  = $vitalSignPsubstr;
	//END OF CODE
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignRsub  = $_POST["txt_vitalSignR"];
	$vitalSignRsubstr = substr($vitalSignRsub,0,3);
	$vitalSignR  = $vitalSignRsubstr;
	//END OF CODE 

	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignO2SATsub  = $_POST["txt_vitalSignO2SAT"];
	$vitalSignO2SATsubstr = substr($vitalSignO2SATsub,0,3);
	$vitalSignO2SAT  = $vitalSignO2SATsubstr;
	//END OF CODE 
	
	//INSERT ONLY SPECIFIC CHARACTERS IN THE TABLE
	$vitalSignTemp  = $_POST["txt_vitalSignTemp"];
	//END OF CODE
	$preOpComments = addslashes(trim($_POST["txtarea_pre_operative_comment"]));
	$relivedNurseId = $_POST["relivedNurseIdList"];
	
	//START CODE TO CHECK NURSE SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
		if($chkNurseSignDetails) {
			$chk_signNurseId = $chkNurseSignDetails->signNurseId;
		}
	//END CODE TO CHECK NURSE SIGN IN DATABASE 
	
	
	$chkdata = false;
	if($chkBoxNSChk=="" && $bsvalueChk!=""){
		$chkdata = true;
	}
	elseif($chkBoxNSChk!="" && $bsvalueChk==""){
		$chkdata = true;
	}
	$vitalSignChk=false;
	//START SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
	$postVitalSignBp = trim($_REQUEST['vitalSignBP_main']);
	$postVitalSignP = trim($_REQUEST['vitalSignP_main']);
	$postVitalSignR = trim($_REQUEST['vitalSignR_main']);
	$postVitalSignO2SAT = trim($_REQUEST['vitalSignO2SAT_main']);
	$postVitalSignTemp = trim($_REQUEST['vitalSignTemp_main']);
	$postVitalSignTime = date('Y-m-d H:i:s');

	
	if($postVitalSignBp!='' || $postVitalSignP!='' || $postVitalSignR!='' || $postVitalSignO2SAT!='' || $postVitalSignTemp!=''){
		$SavePostVSignQry = "insert into `preopnursing_vitalsign_tbl` set 
									vitalSignBp = '$postVitalSignBp',
									vitalSignP = '$postVitalSignP', 
									vitalSignR = '$postVitalSignR',
									vitalSignO2SAT = '$postVitalSignO2SAT',
									vitalSignTemp = '$postVitalSignTemp',
									vitalSignTime='$postVitalSignTime',
									ascId='".$_REQUEST["ascId"]."', 
									confirmation_id='".$_REQUEST["pConfId"]."',
									patient_id = '".$_REQUEST["patient_id"]."'";
									
		$SavePostVitalSignRes = imw_query($SavePostVSignQry) or die(imw_error());
		if($SavePostVitalSignRes){
			$vitalSignChk=true;	
		}
	}
	if($vitalSignChk==false){
		$qryChckVitalSignRes="SELECT vitalsign_id from preopnursing_vitalsign_tbl where ascId='".$_REQUEST["ascId"]."'
				and	confirmation_id='".$_REQUEST["pConfId"]."' and	patient_id = '".$_REQUEST["patient_id"]."'";
		$resChckVitalSignRes=imw_query($qryChckVitalSignRes);
		if(imw_num_rows($resChckVitalSignRes)>0){
			$vitalSignChk=true;	
		}
	}
	//END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
		
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if($foodDrinkToday=="" || $labTest=="" || $ekg=="" 
		 || $consentSign=="" || $hp=="" || $admitted2Hospital=="" || $healthQuestionnaire=="" 
		 || $standingOrders=="" || $patVoided=="" || $anyPain=="" || $doctorNotified=="" || $hearingAids=="" || $denture=="" 
		// || $vitalSignBp=="" || $vitalSignP=="" || $vitalSignR=="" || $vitalSignO2SAT=="" || $vitalSignTemp=="" 
		 || $chk_signNurseId=="0" || $chkdata === false || $vitalSignChk==false)
		{
			$form_status = "not completed";
		}
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	
	$chkPreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkPreopnursingRes = imw_query($chkPreopnursingQry) or die(imw_error()); 
	$chkPreopnursingNumRow = imw_num_rows($chkPreopnursingRes);
	if($chkPreopnursingNumRow>0) {
	  	//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkPreopnursingRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		//CODE TO MAKE preOpComments FIELD EMPTY 
		imw_query("update `preopnursingrecord` set preOpComments='' 
	  				WHERE confirmation_id='".$_REQUEST["pConfId"]."'"
				  );
		//CODE TO MAKE preOpComments FIELD EMPTY 		  
		$SavePreopnursingQry = "update `preopnursingrecord` set 
									preopNurseTime = '$preopNurseTime',
									foodDrinkToday = '$foodDrinkToday',
									allergies_status_reviewed = '$allergies_status_reviewed',
									listFoodTake = '$listFoodTake', 
									labTest = '$labTest',
									ekg = '$ekg', 
									consentSign = '$consentSign',
									hp = '$hp', 
									admitted2Hospital = '$admitted2Hospital',
									reason = '$reason',
									healthQuestionnaire = '$healthQuestionnaire', 
									standingOrders = '$standingOrders', 
									patVoided = '$patVoided', 
									hearingAids = '$hearingAids',
									hearingAidsRemoved = '$hearingAidsRemoved',
									denture = '$denture',
									anyPain = '$anyPain',
									painLevel = '$painLevel',
									painLocation = '$painLocation',
									doctorNotified = '$doctorNotified',
									dentureRemoved = '$dentureRemoved',
									patientHeight = '$patientHeight',
									patientWeight = '$patientWeight',
									patientBmi		=	'$patientBMI',
									vitalSignBp  = '$vitalSignBp',
									vitalSignP  = '$vitalSignP',
									vitalSignR  = '$vitalSignR',
									vitalSignO2SAT = '$vitalSignO2SAT',
									vitalSignTemp  = '$vitalSignTemp',
									preOpComments = '$preOpComments', 
									relivedNurseId = '$relivedNurseId',
									preopnursingSaveDateTime = '".date("Y-m-d H:i:s")."',
									form_status ='".$form_status."',
									ascId='".$_REQUEST["ascId"]."', 
									NA = '".$chkBoxNSChk."',
									bsValue ='".$bsvalueChk."',
									saveFromChart =	'".$_REQUEST["saveFromChart"]."'
									".$version2Query."
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else{
		$SavePreopnursingQry = "insert into `preopnursingrecord` set 
									preopNurseTime = '$preopNurseTime',
									foodDrinkToday = '$foodDrinkToday',
									allergies_status_reviewed = '$allergies_status_reviewed',
									listFoodTake = '$listFoodTake', 
									labTest = '$labTest',
									ekg = '$ekg', 
									consentSign = '$consentSign',
									hp = '$hp', 
									admitted2Hospital = '$admitted2Hospital',
									reason = '$reason',
									healthQuestionnaire = '$healthQuestionnaire', 
									standingOrders = '$standingOrders', 
									patVoided = '$patVoided', 
									hearingAids = '$hearingAids',
									hearingAidsRemoved = '$hearingAidsRemoved',
									denture = '$denture',
									anyPain = '$anyPain',
									painLevel = '$painLevel',
									painLocation = '$painLocation',
									doctorNotified = '$doctorNotified',
									dentureRemoved = '$dentureRemoved',
									patientHeight = '$patientHeight',
									patientWeight = '$patientWeight',
									patientBmi		=	'$patientBMI',
									vitalSignBp  = '$vitalSignBp',
									vitalSignP  = '$vitalSignP',
									vitalSignR  = '$vitalSignR',
									vitalSignO2SAT = '$vitalSignO2SAT',
									vitalSignTemp  = '$vitalSignTemp',
									preOpComments = '$preOpComments', 
									relivedNurseId = '$relivedNurseId',
									preopnursingSaveDateTime = '".date("Y-m-d H:i:s")."',
									form_status ='".$form_status."',
									confirmation_id='".$_REQUEST["pConfId"]."',
									NA = '".$chkBoxNSChk."',
									bsValue ='".$bsvalueChk."',
									saveFromChart =	'".$_REQUEST["saveFromChart"]."'
									".$version2Query."
									";
	}
	$SavePreopnursingRes = imw_query($SavePreopnursingQry) or die($SavePreopnursingQry.imw_error());
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
	
	//delete allregy(if chbx_drug_react==yes) when save button clicked and set allergies status in patient confirmation
	 if($_POST['chbx_drug_react']=='Yes') {
		 imw_query("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfId'");
	 }
	 $updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".$_POST['chbx_drug_react']."' where patientConfirmationId = '$pConfId'";
	 $updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
	//end delete(if chbx_drug_react==yes) when save button clicked and set allergies status in patient confirmation
	
	$save = 'true';
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$recentChartSaved = "";
		if(trim($preopNurseTime)) { $recentChartSavedQry = ", recentChartSaved = 'preopnursingrecord' ";}
		
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
	
	//START DISPLAY HEIGHT AND WEIGHT IN HEADER
	$patientHeightHeader = $patientWeightHeader = '';
	if(trim($patientHeight)) {
		$patientHeightHeader	= $patientHeight.'"';		
	}
	if(trim($patientWeight)) {
		$patientWeightHeader	= $patientWeight.' lbs';
	}
	if(trim($patientBMI)) {
		$patientBmiHeader	= $patientBMI;
		
	}
	
	$patientBglHeader	= trim($chkBoxNSChk) ? "N/A" : $bsvalueChk;
		
		echo '<script>
					top.document.getElementById("header_Height").innerHTML = \''.$patientHeightHeader.'\' ;
					top.document.getElementById("header_Weight").innerHTML = \''.$patientWeightHeader.'\' ;
					top.document.getElementById("header_Bmi").innerHTML = \''.$patientBmiHeader.'\' ;
					top.document.getElementById("header_Bgl").innerHTML = \''.$patientBglHeader.'\' ;
				</script>';	
	//END DISPLAY HEIGHT AND WEIGHT IN HEADER
	

}

//END SAVE RECORD TO DATABASE

//VIEW RECORD FROM DATABASE
		$ViewPreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPreopnursingRes = imw_query($ViewPreopnursingQry) or die(imw_error()); 
		$ViewPreopnursingNumRow = imw_num_rows($ViewPreopnursingRes);
		$ViewPreopnursingRow = imw_fetch_array($ViewPreopnursingRes); 
		
		$preOpNursingId	=	$ViewPreopnursingRow['preOpNursingRecordId'];
		$preopnursing_vitalsign_id = $ViewPreopnursingRow["preopnursing_vitalsign_id"]; //new gurleen
		$allergies_status_reviewed = $ViewPreopnursingRow["allergies_status_reviewed"];
		$preopNurseTime = $objManageData->getTmFormat($ViewPreopnursingRow["preopNurseTime"]);
		$foodDrinkToday = $ViewPreopnursingRow["foodDrinkToday"];
		$listFoodTake = $ViewPreopnursingRow["listFoodTake"];
		$labTest = $ViewPreopnursingRow["labTest"];
		$ekg = $ViewPreopnursingRow["ekg"];
		$consentSign = $ViewPreopnursingRow["consentSign"];
		$hp = $ViewPreopnursingRow["hp"];
		$admitted2Hospital = $ViewPreopnursingRow["admitted2Hospital"];
		$reason = $ViewPreopnursingRow["reason"];
		$healthQuestionnaire = $ViewPreopnursingRow["healthQuestionnaire"];
		$standingOrders = $ViewPreopnursingRow["standingOrders"];
		$patVoided = $ViewPreopnursingRow["patVoided"];
		
		$hearingAids = $ViewPreopnursingRow["hearingAids"];
		$hearingAidsRemoved = $ViewPreopnursingRow["hearingAidsRemoved"];
		$denture = $ViewPreopnursingRow["denture"];
		$dentureRemoved = $ViewPreopnursingRow["dentureRemoved"];
		
		$anyPain = $ViewPreopnursingRow["anyPain"];
		$painLevel = $ViewPreopnursingRow["painLevel"];
		$painLocation = stripslashes($ViewPreopnursingRow["painLocation"]);
		$doctorNotified = $ViewPreopnursingRow["doctorNotified"];
		$patientHeight = $ViewPreopnursingRow["patientHeight"];
		$patientWeight = $ViewPreopnursingRow["patientWeight"];
		if($patientHeight<>"") {
			$height= explode("'",$patientHeight);
			$feet=$height[0];
			$inch=$height[1];
		}
		$weight =explode ("lb",$patientWeight);
		if($weight[0]) {
			$patientWeight=$weight[0];
		}
		$patientBMI	=	$ViewPreopnursingRow["patientBmi"];
		if($patientBMI)		$patientBMI=	(float) $patientBMI ;
		if(!$patientBMI && $patientHeight && $patientWeight)
		{
			$heightInches	=	($feet*12) + $inch;
			$BMIValue	=	$patientWeight * 703 / ($heightInches * $heightInches);	
			$BMIValue	=	number_format($BMIValue,2,'.','');
			$patientBMI	=	$BMIValue ;
			
		}
		$vitalSignBp = $ViewPreopnursingRow["vitalSignBp"];
		$vitalSignP = $ViewPreopnursingRow["vitalSignP"];
		$vitalSignR = $ViewPreopnursingRow["vitalSignR"];
		$vitalSignO2SAT = $ViewPreopnursingRow["vitalSignO2SAT"];
		$vitalSignTemp = $ViewPreopnursingRow["vitalSignTemp"];
		$preOpComments = $ViewPreopnursingRow["preOpComments"];
		$relivedNurseId = $ViewPreopnursingRow["relivedNurseId"];
		$form_status =  $ViewPreopnursingRow["form_status"];
		
		$signNurseId =  $ViewPreopnursingRow["signNurseId"];
		$signNurseFirstName =  $ViewPreopnursingRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewPreopnursingRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewPreopnursingRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewPreopnursingRow["signNurseStatus"];
		
		$signNurseSignDate =  $ViewPreopnursingRow["signNurseDateTime"];
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		
		$bsValue =  $ViewPreopnursingRow["bsValue"];
		$NA =  $ViewPreopnursingRow["NA"];
		
		// Version 2 Fields
		$version_num 			=	$ViewPreopnursingRow['version_num'];
		$versionDateTime	=	$ViewPreopnursingRow['version_date_time'];
		if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
		else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	$current_form_version; }
		$saveFromChart =	$ViewPreopnursingRow['saveFromChart'];
		$comments = $ViewPreopnursingRow['comments'];
		$chbx_saline_lockStart = $ViewPreopnursingRow['chbx_saline_lockStart'];
		$chbx_saline_lock = $ViewPreopnursingRow['chbx_saline_lock'];
		$ivSelection = $ViewPreopnursingRow['ivSelection'];
		$ivSelectionOther = $ViewPreopnursingRow['ivSelectionOther'];
		$ivSelectionSide =	$ViewPreopnursingRow['ivSelectionSide'];
		$chbx_KVO	=	$ViewPreopnursingRow['chbx_KVO'];
		$chbx_rate = $ViewPreopnursingRow['chbx_rate'];
		$txtbox_rate = $ViewPreopnursingRow['txtbox_rate'];
		$chbx_flu =	$ViewPreopnursingRow['chbx_flu'];
		$txtbox_flu	= $ViewPreopnursingRow['txtbox_flu'];
		$gauge = $ViewPreopnursingRow['gauge'];
		$gauge_other	=	$ViewPreopnursingRow['gauge_other'];
		if(!$gauge) { $gauge = "22g"; }
		$txtbox_other_new = stripslashes($ViewPreopnursingRow['txtbox_other_new']);
		//$prefilMedicationStatus	=	$ViewPreopnursingRow['prefilMedicationStatus'];

		if($nurseIDProfile && trim($form_status)=="") {
			$nurseProfileQry 			= "select * from `nurse_profile_tbl` where  nurseId = '".$nurseIDProfile."'";
			$nurseProfileRes 			= imw_query($nurseProfileQry) or die(imw_error());
			if(imw_num_rows($nurseProfileRes)>0) {
				$nurseProfileRow 		= imw_fetch_array($nurseProfileRes);
				$nurse_profile_sign_path= $nurseProfileRow['nurse_profile_sign_path'];
				if(trim($nurse_profile_sign_path)) {
					$comments 				= $nurseProfileRow['comments'];
					$chbx_saline_lockStart	= $nurseProfileRow['chbx_saline_lockStart'];
					$chbx_saline_lock 		= $nurseProfileRow['chbx_saline_lock'];
					$ivSelection 			= $nurseProfileRow['ivSelection'];
					$ivSelectionOther 		= $nurseProfileRow['ivSelectionOther'];
					$ivSelectionSide 		= $nurseProfileRow['ivSelectionSide'];
					$chbx_KVO				= $nurseProfileRow['chbx_KVO'];
					$chbx_rate 				= $nurseProfileRow['chbx_rate'];
					$txtbox_rate 			= $nurseProfileRow['txtbox_rate'];
					$chbx_flu 				= $nurseProfileRow['chbx_flu'];
					$txtbox_flu				= $nurseProfileRow['txtbox_flu'];
					$gauge 					= $nurseProfileRow['gauge'];
					$gauge_other			= $nurseProfileRow['gauge_other'];
					if(!$gauge) { $gauge 	= "22g"; }
					$txtbox_other_new 		= stripslashes($nurseProfileRow['txtbox_other_new']);
					
					$foodDrinkToday 		= $nurseProfileRow["foodDrinkToday"];
					$listFoodTake 			= $nurseProfileRow["listFoodTake"];
					$labTest 				= $nurseProfileRow["labTest"];
					$ekg 					= $nurseProfileRow["ekg"];
					$consentSign 			= $nurseProfileRow["consentSign"];
					$hp 					= $nurseProfileRow["hp"];
					$admitted2Hospital 		= $nurseProfileRow["admitted2Hospital"];
					$reason 				= $nurseProfileRow["reason"];
					$healthQuestionnaire 	= $nurseProfileRow["healthQuestionnaire"];
					$standingOrders 		= $nurseProfileRow["standingOrders"];
					$patVoided 				= $nurseProfileRow["patVoided"];
					
					$hearingAids 			= $nurseProfileRow["hearingAids"];
					$hearingAidsRemoved 	= $nurseProfileRow["hearingAidsRemoved"];
					$denture 				= $nurseProfileRow["denture"];
					$dentureRemoved 		= $nurseProfileRow["dentureRemoved"];
					
					$anyPain 				= $nurseProfileRow["anyPain"];
					$painLevel 				= $nurseProfileRow["painLevel"];
					$painLocation 			= stripslashes($nurseProfileRow["painLocation"]);
					$doctorNotified 		= $nurseProfileRow["doctorNotified"];
					
					$preOpComments 			= $nurseProfileRow["preOpComments"];
					$relivedNurseId 		= $nurseProfileRow["relivedNurseIdPre"];
				
				}
			}
		}
		
//END VIEW RECORD FROM DATABASE

?>
<script>
	//top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');

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
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		url=url+"&userIdentity="+userIdentity
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
		}
	}
	
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

//FUNCTION TO CHECK IF HIDE OR DISPLAY
	function chk_hide_show(id) {
		var k=document.frm_pre_op_nurs_rec.rowK.value;
		if(document.getElementById(id).style.display=="block") {
			++k;
		}else {
			--k;
		}
		
		document.frm_pre_op_nurs_rec.rowK.value=k;
	}
//END FUNCTION TO CHECK IF HIDE OR DISPLAY

//SAVE VITAL SIGN (BP,P,R,O2SAT,TEMP...)
	function save_vitalsign_value() {
		var m=document.frm_pre_op_nurs_rec.rowM.value;
		++m;
		document.frm_pre_op_nurs_rec.rowM.value=m;
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
			
		var vitalSignBP_main_ajax = document.frm_pre_op_nurs_rec.vitalSignBP_main.value
		var vitalSignP_main_ajax = document.frm_pre_op_nurs_rec.vitalSignP_main.value
		var vitalSignR_main_ajax = document.frm_pre_op_nurs_rec.vitalSignR_main.value
		var vitalSignO2SAT_main_ajax = document.frm_pre_op_nurs_rec.vitalSignO2SAT_main.value
		var vitalSignTemp_main_ajax = document.frm_pre_op_nurs_rec.vitalSignTemp_main.value
		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="pre_op_nursing_record_ajax.php";
		url=url+"?vitalSignBP_main="+vitalSignBP_main_ajax
		url=url+"&vitalSignP_main="+vitalSignP_main_ajax
		url=url+"&vitalSignR_main="+vitalSignR_main_ajax
		url=url+"&vitalSignO2SAT_main="+vitalSignO2SAT_main_ajax
		url=url+"&vitalSignTemp_main="+vitalSignTemp_main_ajax
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+encodeURIComponent(innerKey1)
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		url=url+"&preColor="+encodeURIComponent(preColor1)
		
		xmlHttp.onreadystatechange=AjaxTestingFun
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	
	}

	function AjaxTestingFun() {
		if(xmlHttp.readyState==1) {
			if(typeof(parent.parent.show_loading_image)!="undefined") {
				parent.parent.show_loading_image('block');
			}
			
		}
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{
			if(typeof(parent.parent.show_loading_image)!="undefined") {
				parent.parent.show_loading_image('none');
			}
			
			document.getElementById("vital_sign_main_id").innerHTML=xmlHttp.responseText;
			document.forms[0].submit();
		}
	}
	
//END SAVE VITAL SIGN (BP,P,R,O2SAT,TEMP...  )

//DELETE VITAL SIGN (BP,P,R,O2SAT,TEMP...  )
	function delentry(id){
		var ask = confirm("Are you sure to delete the record.")
		if(ask==true){
			var m=document.frm_pre_op_nurs_rec.rowM.value;
			--m;
			if(m==0){
				document.getElementById('vital_sign_2_id').style.display = 'block';
			}
			document.frm_pre_op_nurs_rec.rowM.value=m;
			xmlHttp=GetXmlHttpObject();		
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request");
				return true;
			}		
			
			if(document.getElementById('hidd_preopnursing_vitalsign_id')) {
				var display_BaseLine=true;
				var hidd_vital_id = document.getElementById('hidd_preopnursing_vitalsign_id').value;
				if(hidd_vital_id) {
					if(hidd_vital_id==id) {
						display_BaseLine=false;
					}
				}else {
					display_BaseLine=false;
				}
				if(display_BaseLine==false) {
					if(top.document.getElementById('header_BP')) {
						top.document.getElementById('header_BP').innerText='';
					}
					if(top.document.getElementById('header_P')) {
						top.document.getElementById('header_P').innerText='';
					}
					if(top.document.getElementById('header_R')) {
						top.document.getElementById('header_R').innerText='';
					}
					if(top.document.getElementById('header_O2SAT')) {
						top.document.getElementById('header_O2SAT').innerText='';
					}
					if(top.document.getElementById('header_Temp')) {
						top.document.getElementById('header_Temp').innerText='';
					}
					
				}
			}
			
			var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
			var url='pre_op_nursing_record_vital_del_ajax.php?delId='+id+'&row='+m+'&pConfId='+pConfId1;
			xmlHttp.onreadystatechange=AjaxTestDel(id)
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
	}		
	function AjaxTestDel(id){
		if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
			//document.getElementById("vital_sign_main_id").innerHTML=xmlHttp.responseText;
		}
		var elem = document.getElementById("BP_div"+id);
		$(elem).hide('slow', function(){$(elem).remove()});
	}
//END DELETE VITAL SIGN (BP,P,R,O2SAT,TEMP...  )
	
//FUNCTION TO CLEAR THE VALUE OF BP,P,R,O2SAT,TEMP...	
	function clearAll_preopnursing(t1,t2,t3,t4)
	{
		document.getElementById(t1).value="";
		document.getElementById(t2).value="";
		document.getElementById(t3).value="";
		document.getElementById(t4).value="";
	}
//END FUNCTION TO CLEAR THE VALUE OF BP,P,R,O2SAT,TEMP...	

function doSomething(e) {
	var rightclick;
	if (!e) var e = window.event;
	if (e.which) rightclick = (e.which == 3);
	else if (e.button) rightclick = (e.button == 2);
	alert('Rightclick: ' + rightclick + e.button); // true or false
}
function changeBaseLine(id, BP_change,P_change,R_change,O2SAT_change,Temp_change) {
	if(confirm('Do you want to make it as Base Line Vital Sign')) {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		} 
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url='pre_op_nursing_header_vital_change_ajax.php?chngId='+id+'&pConfId='+pConfId1;
		//xmlHttp.onreadystatechange=AjaxChangeBaseLine
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
		
		if(document.getElementById('hidd_preopnursing_vitalsign_id')) {
			document.getElementById('hidd_preopnursing_vitalsign_id').value=id;
		}
		
		if(top.document.getElementById('header_BP')) {
			top.document.getElementById('header_BP').innerText=BP_change;
		}
		if(top.document.getElementById('header_P')) {
			top.document.getElementById('header_P').innerText=P_change;
		}
		if(top.document.getElementById('header_R')) {
			top.document.getElementById('header_R').innerText=R_change;
		}
		if(top.document.getElementById('header_O2SAT')) {
			top.document.getElementById('header_O2SAT').innerText=O2SAT_change;
		}
		if(top.document.getElementById('header_Temp')) {
			top.document.getElementById('header_Temp').innerText=Temp_change;
		}
	}
}
</script>
<!--<body onLoad="top.changeColor('<?php echo $bgcolor_pre_op_nursing_order; ?>');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();">-->
<div id="post" style="display:none; position:absolute;"></div>
	
<?php 

// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
	$surgeonID	=	$detailConfirmation->surgeonId;
	$primProcId	=	$detailConfirmation->patient_primary_procedure_id;
	$secProcId	=	$detailConfirmation->patient_secondary_procedure_id;
	$terProcId	=	$detailConfirmation->patient_tertiary_procedure_id;
	$confirmSitePreOpNursing	=	$detailConfirmation->site;
	
	if($confirmSitePreOpNursing == 1) {
		$confirmSitePreOpNursing = "Left Eye";  //OD
	}else if($confirmSitePreOpNursing == 2) {
		$confirmSitePreOpNursing = "Right Eye";  //OS
	}else if($confirmSitePreOpNursing == 3) {
		$confirmSitePreOpNursing = "Both Eye";  //OU
	}else{
		$confirmSitePreOpNursing = "Operative Eye";  //OU
	}	
// GETTING CONFIRMATION DETAILS

	
	// Start Prefilling/Retreiving Saved/Prefilled Pre Op Medication Details filled in pre op physician order
	$preOpPhysicianOrderRow	=	$objManageData->getExtractRecord('preopphysicianorders','patient_confirmation_id',$pConfId,'preOpPhysicianOrdersId,prefilMedicationStatus');
	$preOpPhysicianOrdersId	=	$preOpPhysicianOrderRow	['preOpPhysicianOrdersId'];
	$prefilMedicationStatus	=	$preOpPhysicianOrderRow	['prefilMedicationStatus'];
	
	if($prefilMedicationStatus <> 'true')
	{
		
		//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
		$otherPreOpOrdersFound = "";
		$surgeonProfileIdFound = "";
		$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonID' and del_status=''";
		$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
		while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
			$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
		}
		if(is_array($surgeonProfileIdArr)){
			$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
		}else {
			$surgeonProfileIdImplode = 0;
		}
		$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
		$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
		$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
		if($selectSurgeonProcedureNumRow>0)
		{
			while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
				$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
				 
				if($primProcId == $surgeonProfileProcedureId) {
					$surgeonProfileIdFound = $selectSurgeonProcedureRow['profileId'];
				}
			}
			
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";
			
			$selectSurgeonProfileFoundRes = imw_query($selectSurgeonProfileFoundQry) or die(imw_error());
			$selectSurgeonProfileFoundNumRow = imw_num_rows($selectSurgeonProfileFoundRes);
			if($selectSurgeonProfileFoundNumRow > 0) {
				$selectSurgeonProfileFoundRow = imw_fetch_array($selectSurgeonProfileFoundRes);
				$preOpOrdersFound = $selectSurgeonProfileFoundRow['preOpOrders'];
				$otherPreOpOrdersFound = $selectSurgeonProfileFoundRow['otherPreOpOrders'];
				$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
				//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW
				for($k=0;$k<=count($preOpOrdersFoundExplode);$k++) {
								if($preOpOrdersFoundExplode[$k]<>"") {
									
									$selectPreOpmedicationOrderQry = "select * from preopmedicationorder where preOpMedicationOrderId = '$preOpOrdersFoundExplode[$k]'";
									$selectPreOpmedicationOrderRes = imw_query($selectPreOpmedicationOrderQry) or die(imw_error());
									$selectPreOpmedicationOrderRow = imw_fetch_array($selectPreOpmedicationOrderRes);
									
									$selectMedicationName = addslashes($selectPreOpmedicationOrderRow['medicationName']);
									$selectStrength 			= addslashes($selectPreOpmedicationOrderRow['strength']);
									$selectDirections 		= addslashes($selectPreOpmedicationOrderRow['directions']);
									
									$chk_patientMedicationQry	=	"Select * From patientpreopmedication_tbl Where 
																												medicationName	= '".$selectMedicationName."'
																												And strength 		= '".$selectStrength."'
																												And direction 	= '".$selectDirections."'
																												And preOpPhyOrderId = '".$preOpPhysicianOrdersId."'
																												And patient_confirmation_id = '".$pConfId."'
																										";
									$chk_patientMedicationSql	=	imw_query($chk_patientMedicationQry);
									$chk_patientMedicationCnt	=	imw_num_rows($chk_patientMedicationSql);
									
									if($chk_patientMedicationCnt == 0)
									{
											$insPatientPreOpMediQry = "Insert Into patientpreopmedication_tbl Set
																										preOpPhyOrderId = '".$preOpPhysicianOrdersId."',
																										patient_confirmation_id = '".$pConfId."',
																										medicationName = '".$selectMedicationName."',
																										strength = '".$selectStrength."',
																										direction = '".$selectDirections."'
																								";
											$insPatientPreOpMediRes = imw_query($insPatientPreOpMediQry) or die(imw_error());
									}
									
									/*
									$updatePreOpPhysicianOrderQry = "update preopphysicianorders set
																		prefilMedicationStatus = 'true'
																		WHERE patient_confirmation_id = '$pConfId'
																	";
									$updatePreOpPhysicianOrderRes = imw_query($updatePreOpPhysicianOrderQry) or die(imw_error());
									*/
								}
							}
				//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW	
			}
			
		}	
		
		if(!$surgeonProfileIdFound)
		{
				/*****
				* Start Procedure Preference Card 
				*****/
				{
					$proceduresArr	=	array($primProcId,$secProcId,$terProcId);
					foreach($proceduresArr as $procedureId)
					{
						if($procedureId)
						{		
							$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procedureId."' ";
							$procPrefCardSql		=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
							$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
							if($procPrefCardCnt > 0 )
							{
								$procPrefCardRow	=	imw_fetch_object($procPrefCardSql);
								$preOpOrdersFound = $procPrefCardRow->preOpOrders;
								$otherPreOpOrdersFound = $procPrefCardRow->otherPreOpOrders;
								$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
				
								//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW
					
								for($k=0;$k<=count($preOpOrdersFoundExplode);$k++)
								{
											if($preOpOrdersFoundExplode[$k]<>"") 
											{
												$selectPreOpmedicationOrderQry = "select * from preopmedicationorder where preOpMedicationOrderId = '$preOpOrdersFoundExplode[$k]'";
												$selectPreOpmedicationOrderRes = imw_query($selectPreOpmedicationOrderQry) or die(imw_error());
												$selectPreOpmedicationOrderRow = imw_fetch_array($selectPreOpmedicationOrderRes);
												
												$selectMedicationName = addslashes($selectPreOpmedicationOrderRow['medicationName']);
												$selectStrength 			= addslashes($selectPreOpmedicationOrderRow['strength']);
												$selectDirections 		= addslashes($selectPreOpmedicationOrderRow['directions']);
												
												$chk_patientMedicationQry	=	"Select * From patientpreopmedication_tbl Where 
																												medicationName	= '".$selectMedicationName."'
																												And strength 		= '".$selectStrength."'
																												And direction 	= '".$selectDirections."'
																												And patient_confirmation_id = '".$pConfId."'
																										";
												$chk_patientMedicationSql	=	imw_query($chk_patientMedicationQry);
												$chk_patientMedicationCnt	=	imw_num_rows($chk_patientMedicationSql);
												
												if($chk_patientMedicationCnt == 0)
												{
														$insPatientPreOpMediQry = "Insert into patientpreopmedication_tbl set
																												preOpPhyOrderId = '".$preOpPhysicianOrdersId."',
																												patient_confirmation_id = '".$pConfId."',
																												medicationName = '".$selectMedicationName."',
																												strength = '".$selectStrength."',
																												direction = '".$selectDirections."'
																											";
														$insPatientPreOpMediRes = imw_query($insPatientPreOpMediQry) or die(imw_error());							
												}
												/*
												$updatePreOpPhysicianOrderQry = "Update preopphysicianorders Set
																					prefilMedicationStatus = 'true'
																					WHERE patient_confirmation_id = '$pConfId'
																				";
												$updatePreOpPhysicianOrderRes = imw_query($updatePreOpPhysicianOrderQry) or die(imw_error());
												*/
											}
										}
								
								//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW
								
								break; 
							}
						}
					}
				}
				
				/*****
				* End Procedure Preference Card 
				*****/
		}
		//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID

	}
	
	$preOpMedicationDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $pConfId,'patientPreOpMediId','ASC'," And preOpPhyOrderId = '".$preOpPhysicianOrdersId."' ");
	
	// End Prefilling/Retreiving Saved/Prefilled Pre Op Medication Details filled in pre op physician order 
	
	
	
	//START CODE TO GET DOCUMENTS OF EKG H&P CONSENT
	$ekgHpLink = "";
	$anesConsentEkgHpArr = array('EKG', 'H&P', 'Consent','Ocular Hx','Health Questionnaire','Sx Planning Sheet');
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
	$focusInput = "onFocus=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
?>
<form name="frm_pre_op_nurs_rec" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="pre_op_nursing_record.php">
	<input type="hidden" name="divId" id="divId">
	<input type="hidden" name="counter" id="counter">
	<input type="hidden" name="secondaryValues" id="secondaryValues">
    <input type="hidden" name="hiddTertiaryValue" id="hiddTertiaryValue" value="">
	<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
	<input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
	<input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	<input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
	<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
  <input type="hidden" name="saveFromChart" id="saveFromChart" value="0">
  <input type="hidden" name="saveRecord" id="saveRecord" value="true">
	<input type="hidden" name="getText" id="getText">
	<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
	<input type="hidden" name="go_pageval"  id="go_pageval" value="<?php echo $tablename;?>">
	<input type="hidden" name="frmAction" id="frmAction" value="pre_op_nursing_record.php">
	<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">
	<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
	<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
 	<input type="hidden" name="preOpNursingId" id="preOpNursingId" value="<?=$preOpNursingId?>" />
  <input type="hidden" id="vitalSignGridHolder" />
    
	<div class="scheduler_table_Complete" id="" style="">
		  <?php
				$epost_table_name = "preopnursingrecord";
				include("./epost_list.php");
		 ?>
          
			<div id="divSaveAlert" style="position:absolute;left:350px; display:none; z-index:1000;">
				<?php 
					$bgCol = $title_pre_op_nursing_order;
					$borderCol = $title_pre_op_nursing_order;
					include('saveDivPopUp.php'); 
				?>
			</div>
            
		   <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
				  <Div class="panel panel-default bg_panel_qint">
					  <Div class="panel-heading">
						  <a id="pre_op_nursing_allergies" class="panel-title rob alle_link show-pop-trigger2 btn btn-default " onClick="return showPreDefineFnNew('Allergies_quest', 'Reaction_quest', '10',  parseInt(findPos_X('pre_op_nursing_allergies')+10), parseInt(findPos_Y('pre_op_nursing_allergies')-136)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='iframe_allergies_pre_op_nurse_rec';"> <span class="fa fa-caret-right"></span>Allergies/Drug Reaction</a>
						  <div class="right_label">
							<label style="display:inline-block;" for="chbx_drug_react_no">
								<input type="checkbox" onClick="javascript:txt_enable_disable_frame1('hlthQstSpreadTableId','chbx_drug_react_no','Allergies_quest','Reaction_quest',10)" <?php if($allergiesNKDA_patientconfirmation_status=="Yes"){echo 'CHECKED';} ?> value="Yes"  name="chbx_drug_react" id="chbx_drug_react_no" tabindex="7"/> NKA</label>&nbsp;&nbsp;
							<label style="display:inline-block;" for="chbx_drug_react_yes"><input type="checkbox" <?php if($allergies_status_reviewed=='Yes'){echo 'CHECKED';} ?>  value="Yes" name="chbx_drug_react_reviewed" id="chbx_drug_react_yes"  tabindex="7" /> Allergies Reviewed</label>
						  </div>
					  </Div>
					  <div class="panel-body">
						  <div class="inner_safety_wrap">
							  <div class="row">
								  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
									 <!--- ---- Table --->
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
										 <div class="table_slider">          
												<?php include("health_quest_spreadsheet.php");?>
										   </div>                

										</div>
<!--- ---- Table --->
								  </div>
								  <!-- Col-3 ends  -->
							  </div>	 
						   </div>
					  </div> <!-- Panel Body -->
				  </Div>
				  
				  <Div class="clearfix margin_adjustment_only"></Div>
			 </div>
<!------------------------------------------------ Col -2 STarts  ------------------------------------------------>
			  <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
				  
				  
				  <Div class="panel panel-default bg_panel_qint">
					  <Div class="panel-heading">
						  <a id="meds_taken_today_nursting" class="panel-title rob alle_link btn btn-default " data-placement="top" onClick="return showPreDefineMedFnNew('medication_name', 'medication_detail', '10',  parseInt(findPos_X('meds_taken_today_nursting')+8), parseInt(findPos_Y('meds_taken_today_nursting')+58)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='iframe_medication_pre_op_nurse';"> <span class="fa fa-caret-right"></span>Meds Taken Today</a>
					  	  <div class="right_label" style="top:6px;">
						  	<a id="meds_taken_today_prehealth" class="panel-title rob alle_link btn btn-default " data-placement="top" onClick="return preDefineSavedHealthQuestMedFn('medication_name', 'medication_detail', '10',  parseInt(findPos_X('meds_taken_today_prehealth')-300), parseInt(findPos_Y('meds_taken_today_prehealth')+58)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='iframe_medication_pre_op_nurse',document.getElementById('hiddTertiaryValue').value='medication_sig';"> <span class="fa fa-caret-right"></span>Meds From Health Quest.</a>
						  </div>	
					  </Div>
					  <div class="panel-body">
						  <div class="inner_safety_wrap">
							  <div class="row">
								  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
									 <!--- ---- Table --->
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
										 <div class="table_slider" id="iframe_medication_pre_op_nurse">          
												   <?php include("patient_prescription_medi_spreadsheet.php"); ?>
										   </div>                

										</div>
<!--- ---- Table --->
									 
								  </div>
								   <!-- Col-3 ends  -->
							  </div>	 
						   </div>
					  </div> <!-- Panel Body -->
				  </Div>
				  
			   </div>
		  <Div class="clearfix margin_adjustment_only"></Div>
				  
            
            <!-- Start HTML Form Fields for Version2 -->
            <?PHP if(($version_num > 1 && $preOpPhyVersionNum <> 1) || $preOpPhyVersionNum > 1) { ?>
						
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            	<div class="panel panel-default bg_panel_qint">
                
                		<div class="panel-heading">
                    	<h3 class="panel-title rob">Pre Op Orders</h3>
                    </div>
                    
                  	<div class="panel-body " id="p_check_in">
                   		<div class="row">
                        
                        		<p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-12"> On arrival the following drops will be given to the <?php echo $confirmSitePreOpNursing;?></p>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
                                <div class="clearfix border-dashed margin_adjustment_only"></div>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                <div class="scanner_win new_s">
                                	<h4><span>List of Pre-OP Medication Orders</span></h4>
                              	</div>
                          	</div> 
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            	
                              <div class="scheduler_table_Complete ">
                              
                              	<div class="col-xs-12 full_width "><div class="row">
                                		<?php 
																			$paddingR	=	(count($preOpMedicationDetails) > 3) ? 15 : 0;
																		?>
                                
                                 		<table class="col-xs-12 padding_0 table-bordered  table-condensed cf  table-striped" style="padding-right:<?=$paddingR?>px !important;">
                                        <thead class="cf">
                                          <tr>
                                            <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Medication</th>
                                            <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Strength</th>
                                            <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Direction</th>
                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6" colspan="6" style="border-right:none;"></th>
                                        	</tr>
                                        </thead>
                                 		</table>
                               	</div></div>     
                                
                                 
                              	<div class="col-xs-12 max-height-adjust-post full_width "><div class="row" id="preOpMedicationsShowAjaxId">
                                		<table class=" col-xs-12 padding_0 table-bordered  table-condensed cf  table-striped " >
                                        <tbody>
                                        
                                        <?php
																		
																				if(is_array($preOpMedicationDetails) && count($preOpMedicationDetails) > 0)
																				{
																				
																				?>
                                  <input type="hidden" id="bp" name="bp_hidden">
                                  <?php if(!$medicationStartTimeVal) $medicationStartTimeVal = "";?>
                                  <input type="hidden" name="hourVal" id="hourVal" value="<?php print substr($medicationStartTimeVal,0,2); ?>" >
                                  <input type="hidden" name="minuteVal" id="minuteVal" value="<?php print substr($medicationStartTimeVal,3,-2); ?>" >
                                  <input type="hidden" name="statusVal" id="statusVal" value="<?php print substr($medicationStartTimeVal,5); ?>" >
                                      	<?php
																						foreach($preOpMedicationDetails as $detailsOfMedication)
																						{
																							
																							$patientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
																							if(trim($detailsOfMedication->medicationName))
																							{
																								$preDefined = $detailsOfMedication->medicationName;
																								$strength = $detailsOfMedication->strength;
																								$directions = $detailsOfMedication->direction;
																								$timemeds = $objManageData->getTmFormat($detailsOfMedication->timemeds);
																								//$timemeds = $detailsOfMedication->timemeds;
																								$timemeds1=array();
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds1);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds2);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds3);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds4);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds5);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds6);
																								
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds7);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds8);
																								$timemeds1[] = $objManageData->getTmFormat($detailsOfMedication->timemeds9);
																								++$k;
																								
																								if($k==1)
																								{
																									$disptr='block';
																								}	
																								else
																								{
																								$disptr='none';
																								}
																								
																								$dir = explode('X',strtoupper($directions));
																								$freq = substr(trim($dir[1]),0,1);
																								$freq = $freq > 6 ? 6 : $freq;
																								$minsDir = explode('Q',strtoupper($dir[1]));
																								//if(count($minsDir)<=1) $freq = '';
																								$min=substr(trim($minsDir[1]),0,-3);
																							
																							
																							
																				?>			
                                                    <tr id="row<?php echo $seq; ?>">
                                                      <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                      	<input type="hidden" name="patientPreOpMediOrderId[]" id="IDS<?php echo $patientPreOpMediOrderId; ?>" value="<?php echo $patientPreOpMediOrderId; ?>">
																												<?=stripslashes($preDefined)?>	
                                                      </td>
                                                      <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">
																												<?=stripslashes($strength)?>
                                                     	</td>
                                                      <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                                      	<?=stripslashes($directions)?>
                                                        <input type="hidden" name="feq[]" value="<?php print $freq; ?>" >
                                                        <input type="hidden" name="min[]" value="<?php print $min; ?>" >
                                                      </td>
                                                      <td class="text-left col-md-1 col-lg-1 col-sm-1 col-xs-1">
                                                      	<input class="form-control padding_2" type="text" placeholder="" id="starttime<?php echo $k;?>[]" name="timemedsArr[]" value="<?php echo $timemeds;?>" onClick="if(!this.value) { return displayTimeAmPm('starttime<?php echo $k;?>[]');}" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $patientPreOpMediOrderId;?>','<?php echo "timemeds";?>');"><!--cnvrtStrToTime(this.id,this.value);-->
                                                      </td>
                                                      
                                                      <?php
																												if($freq > 1 )
																												{ 
																												
																													for($td=1;$td<$freq;$td++)
																													{
																													
																														$timeStatusArr = explode(':',$medicationStartTimeVal);
																														$minsInt = $min * $td;
																														$timeVar = $minsInt + substr($timeStatusArr[1],0,2);
																														$timeStatus = substr($timeStatusArr[1],3);
																														if($timeVar>=60)
																														{
																															$timeStatusArr[0]=$timeStatusArr[0]+1;
																															$timeVar= $timeVar - 60;
																														}
																														if($timeStatusArr[0]>12)
																														{
																															$timeStatusArr[0]= $timeStatusArr[0]-12;
																														}
																														if($timeVar < 10)
																														{
																															$timeVar= '0'.$timeVar;
																														}
																														if($timeStatusArr[0]!='')
																														{
																														$tdTime = $timeStatusArr[0].':'.$timeVar.''.$timeStatus;
																														}
																														else
																														{
																														$tdTime=' ';
																														}
																													
																														$tdNew = $td-1;	
																													
																											?>
                                                      			<td class="text-left col-md-1 col-lg-1 col-sm-1 col-xs-1">
                                                            		
                                                            		<input class="form-control padding_6" type="text" name="starttimeExtra<?php echo $k;?>[]" id="starttimeExtraId<?php echo $k.$tdNew?>" onClick="if(!this.value){return displayTimeAmPm('starttimeExtraId<?php echo $k.$tdNew?>');}" value="<?php  echo($timemeds1[$tdNew]);//print_r($$timemedsram[0]);?>" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $patientPreOpMediOrderId;?>','<?php echo "timemeds".$td;?>');" />
                                                                
                                                            </td>
                                                    	
																											<?php
																													}
																													
																													if($td < 6)
																													{
																														$cols	=	6 - $freq;
																														echo '<td class="text-left col-md-'.$cols.' col-lg-'.$cols.' col-sm-'.$cols.' col-xs-'.$cols.'" colspan="'.$cols.'">&nbsp;</td>';	
																													}
																												}
																												else
																												{
																											?>
                                              						      
                                                    				<td class="text-left col-md-5 col-lg-5 col-sm-5 col-xs-5" colspan="5">&nbsp;</td>
																				<?php	
																												}
																							}
																						}
																					}
																				?>	
                                        
                                          
                                        </tbody>
                                 		</table>
                                
                                </div></div>   
                     						
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix padding_0">
                                	<br /><div class="clearfix border-dashed margin_adjustment_only"></div>
                               	</div>
                                
                                <div class="clearfix margin_adjustment_only">&nbsp;</div>
                                
                                <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                	<div class="row">
                                  	<label for="text_comment" class="col-md-4 col-sm-12 col-xs-12 col-lg-2 text-right"> Comments </label>
                                    <div class="clearfix visible-sm margin_adjustment_only"></div>
                                    <div class="col-md-8 col-sm-12 col-xs-12 col-lg-10">
                                    	<textarea class="form-control" style="resize:none;" name="comments"><?php echo stripslashes($comments); ?></textarea>
                                   	</div>
                                	</div>
                              	</div>
                                
                                <div class="clearfix margin_adjustment_only"></div>
                                
                            	</div>
                              
                   					</div>
                    
              				</div>
           					</div>
            
                    <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                      <div class="panel panel-default bg_panel_qint">
                        <div class="panel-body " id="p_check_in">
                          <div class="inner_safety_wrap heparin_div">
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                              <div class="row">
                              
                                <div class="col-md-12 col-lg-2 col-xs-12 col-sm-12 padding_0">
                                  <label for="chbx_saline_lockStart" class="">
                                    <input type="checkbox" name="chbx_saline_lockStart" id="chbx_saline_lockStart" <?php if($chbx_saline_lockStart=='saline')echo "checked"; ?> value="saline" onClick="disp_one_hide_other_onchangeNew('ivSelection_id','lft_rgt_id','other_id','chbx_saline_lockStart','chbx_iv','iv_sub_id');"/>Start Saline Lock </label> 
                                  <label for="chbx_iv">
                                    <input type="checkbox" name="chbx_saline_lock" id="chbx_iv" <?php if($chbx_saline_lock=='iv')echo "checked"; ?> value="iv" onClick="disp_one_hide_other_onchangeNew('ivSelection_id','lft_rgt_id','other_id','chbx_saline_lockStart','chbx_iv','iv_sub_id');" />&nbsp;IV</label>
                                </div>
                                
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-10">
                                  <div class="row">
                                    
                                    <div class="col-md-2 col-lg-1 col-xs-3 col-sm-2 padding_0">
                                      <select class="selectpicker" name="ivSelection" id="ivSelection_id" onChange="javascript:disp_one_hide_other_onchangeNew('ivSelection_id','lft_rgt_id','other_id','chbx_saline_lockStart','chbx_iv','iv_sub_id');">
                                        <option value="">No IV</option>
                                        <option value="hand" <?php if($ivSelection=='hand') echo "SELECTED"; ?>>Hand</option>
                                        <option value="wrist" <?php if($ivSelection=='wrist') echo "SELECTED"; ?>>Wrist</option>
                                        <option value="arm" <?php if($ivSelection=='arm') echo "SELECTED"; ?>>Arm</option>
                                        <option value="antecubital" <?php if($ivSelection=='antecubital') echo "SELECTED"; ?>>Antecubital</option>
                                        <option value="other" <?php if($ivSelection=='other') echo "SELECTED"; ?>>Other</option>
                                      </select>
                                    </div>
                                    
                                    <div class="row col-md-10 col-lg-11 col-xs-9 col-sm-10" style="white-space:nowrap;">
                                      
                                      <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="lft_rgt_id" style="display:<?php if($ivSelection=='' || $ivSelection=='other' || ($chbx_saline_lockStart!='saline' && $chbx_saline_lock!='iv')) echo "none"; ?>;">
                                        
                                        <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5 padding_0  ">
                                          <div class="col-md-2 col-lg-2 col-xs-3 col-sm-3 padding_6">
                                            <input type="checkbox" name="ivSelectionSide" <?php if($ivSelectionSide=='right') echo "CHECKED"; ?> value="right" id="chbx_ec_right" onClick="javascript:checkSingle('chbx_ec_right','ivSelectionSide')"/><label>Right</label>
                                          </div>
                                          <div class="col-md-2 col-lg-2 col-xs-3 col-sm-3 padding_6" style="padding-left:5px; ">
                                            
                                            <input type="checkbox" name="ivSelectionSide"  <?php if($ivSelectionSide=='left') echo "CHECKED"; ?> value="left" id="chbx_ec_left" onClick="javascript:checkSingle('chbx_ec_left','ivSelectionSide')" /><label > Left</label>&nbsp;
                                          </div>
                                          <div class="col-md-4 col-lg-4 col-xs-2 col-sm-3 padding_6">
                                            <select class="selectpicker" name="gauge" id="gauge_id" onChange="if($(this).val() == 'other'){ $('#gauge_other_id').fadeIn('fast'); }else{$('#gauge_other_id').fadeOut('fast');}">
                                              <option value="" >Select Gauge</option>
                                              <option value="20g" <?php if($gauge=='20g') echo "SELECTED"; ?>>20g</option>
                                              <option value="22g" <?php if($gauge=='22g') echo "SELECTED"; ?>>22g</option>
                                              <option value="24g" <?php if($gauge=='24g') echo "SELECTED"; ?>>24g</option>
                                              <option value="other" <?php if($gauge=='other') echo "SELECTED"; ?>>Other</option>
                                            </select>	
                                            <input class="form-control" type="text" name="gauge_other" id="gauge_other_id"  value="<?=$gauge_other?>" style="display:<?=($gauge == 'other' ? 'block' : 'none')?>; " placeholder="Others Gauge" />
                                            
                                          </div>
                          <div class="col-md-4 col-lg-4 col-xs-4 col-sm-3 padding_6">
                                            <input type="text" class="form-control col-md-3 col-lg-2 col-xs-3 col-sm-3" name="txtbox_other_new" value="<?php echo $txtbox_other_new;?>" >
                                          </div>
                                        </div>
                                        
                                        
                            <?php if($chbx_saline_lock=='iv' && $ivSelection!='' && $ivSelection!='other') { $iv_sub_id_display =  'block'; }else { $iv_sub_id_display = 'none'; } ?>
                                        
                                        <div id="iv_sub_id" class="col-md-7 col-lg-7 col-xs-7 col-sm-7" style="display:<?php echo $iv_sub_id_display; ?> ">
                                          <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                                            <input type="checkbox" name="chbx_KVO"  value="Yes" <?php if($chbx_KVO=='Yes') { echo "checked";  }?>>
                                            <label>KVO</label>
                                          </div>
                                          <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                                            <input type="checkbox" name="chbx_rate" value="Yes" <?php if($chbx_rate=='Yes') { echo "checked";  }?>>
                                            <label >Rate</label>
                                          </div>
                                          <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 padding_6 " >
                                            <div class="form-group" >
                                              <input type="text" class="form-control" name="txtbox_rate" id="txtbox_rate" style="float:left;width:70%;display:inline-block;" value="<?php echo $txtbox_rate;?>"><label for="txtbox_rate"> /hr</label>
                                              
                                            </div>
                                            
                                          </div>
                                          <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                                            <input type="checkbox" name="chbx_flu"  value="Yes" <?php if($chbx_flu=='Yes') { echo "checked";  }?>>
                                            <label>Flu&nbsp;</label>
                                          </div>
                                          <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_6">
                                            <input type="text" class="form-control col-md-3 col-lg-2 col-xs-3 col-sm-3" name="txtbox_flu" value="<?php echo $txtbox_flu;?>" >
                                          </div>
                                        </div>
                        
                                      </div>
                                      
                                      <div id="other_id" style="display:<?php if($ivSelection=='other') {echo "block";}else {echo "none";} ?> ; " class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                        <textarea id="Field3" name="ivSelectionOther" class="form-control col-md-12 col-lg-12 col-xs-12 col-sm-12" ><?php echo stripslashes($ivSelectionOther) ; ?></textarea>
                                      </div>
                                    
                                    </div>
                                    
                                  </div>
                                </div>
                              
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
            
            <?PHP } ?>
            <!-- End HTML Form Fields for Version2 -->
            
		  
<!------------------------------------------------ Col -2 Ends  ------------------------------------------------>
		 <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
			  <Div class="panel panel-default bg_panel_qint">
				  <div class="panel-heading">
					  <!--h3 class="panel-title rob"-->
						<Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right" style="padding-left:22px; cursor: pointer;color: #fff;">
							  <label class="col-sm-5 col-xs-5 col-lg-5" style="margin-bottom:0px; padding-top:5px;">  Yes  </label>
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4" style="margin-bottom:0px; padding-top:5px;">  No  </label>	                                                        		
							  <!--label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label-->
						  </Div>
						<div class="clearfix"></div>
					  <!--/h3-->
					  <!--Div class="right_label top_yes_no">
						  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right">
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  Yes  </label>
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  No  </label>	                                                        		
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label>
						  </Div>
					  </Div-->
				  </div>
				  <div class="panel-body">
					  <div class="inner_safety_wrap">
					   <Div class="clearfix margin_adjustment_only"></Div>
						  <div class="row">
							  <div class="col-md-4 col-sm-5 col-xs-12 col-lg-4">
								  <label class="date_r" for="arrival_time">
									  Arrival Time
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <input class="form-control" type="text" id="bp_temp6" name="preopNurseTime" onKeyUp="displayText6=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp6'))+25,parseInt(findPos_X('bp_temp6')),'flag6');if(this.value=='') {clearVal_c();return displayTimeAmPm('bp_temp6');}" maxlength="8" tabindex="1"  value="<?php echo $preopNurseTime;//echo date('h:i A');?>"/>  
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   
						<?php
							$defaultFoodDrinkToday	=	$listFoodTake;
							if( $foodDrinkToday == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
							{
								$defaultFoodDrinkToday	= $objManageData->getDefault('fooddrinkslist','name');
							}
						?>			  
					  <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									  Food or Drink Today
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_fdt')">
										<span class="colorChkBx" style=" <?php if($foodDrinkToday) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox" name="chbx_fdt" id="chbx_fdt_yes" onClick="javascript:checkSingle('chbx_fdt_yes','chbx_fdt'),enable_chk_unchk('chbx_fdt_yes','chbx_fdt_no','txtarea_list_food_take');" <?php if($foodDrinkToday=="Yes") { echo "checked"; }?> tabindex="7" value="Yes" />
										</span>
										</span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_fdt')">
										<span class="colorChkBx" style=" <?php if($foodDrinkToday) { echo $whiteBckGroundColor;}?>" >
											<input type="checkbox" name="chbx_fdt" id="chbx_fdt_no" value="No" onClick="javascript:checkSingle('chbx_fdt_no','chbx_fdt'),enable_chk_unchk('chbx_fdt_yes','chbx_fdt_no','txtarea_list_food_take');" <?php if($foodDrinkToday=="No") { echo "checked"; }?> tabindex="7" />
										</span>
										</span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>	
					   <div style="height: auto;" class="inner_safety_wrap" id="descr_21">
						  <div class="well">
							  <div class="row">
								  <div class="col-md-4 col-sm-5 col-xs-4 col-lg-3">
                                      <a data-placement="top" class="panel-title rob alle_link show-pop-list_g btn btn-default" id="precomment_id" onClick="return showFoodListFn('Field3', '', 'no', parseInt($(this).offset().left), parseInt($(this).offset().top)-420),document.getElementById('selected_frame_name_id').value='';"><span class="fa fa-caret-right"></span>List Food Taken</a>
								  </div>
								  <div class="col-md-8 col-sm-7 col-xs-8 col-lg-9 text-center">
									  <input type="hidden" id="defaultListFoodTake" value="<?=$defaultFoodDrinkToday?>" />
                                      <textarea id="txtarea_list_food_take" style="resize:none;" class="form-control" name="txtarea_list_food_take" <?php if($foodDrinkToday=="No" || $foodDrinkToday=="") { echo "disabled"; }?> tabindex="6"><?php echo stripslashes($listFoodTake);?></textarea>
								  </div> <!-- Col-3 ends  -->
							  </div>
						  </div> 
					   </div>
					   
					  <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Lab Test
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
								   
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_lab_test')">
											<span class="colorChkBx" style=" <?php if($labTest) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_lab_test_yes','chbx_lab_test')" value="Yes" name="chbx_lab_test" id="chbx_lab_test_yes" <?php if($labTest=="Yes") { echo "checked"; }?> tabindex="7" />
											</span>
										</span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_lab_test')">
											<span class="colorChkBx" style=" <?php if($labTest) { echo $whiteBckGroundColor;}?>">
											<input type="checkbox" onClick="javascript:checkSingle('chbx_lab_test_no','chbx_lab_test')" value="No" name="chbx_lab_test" id="chbx_lab_test_no" <?php if($labTest=="No") { echo "checked"; }?> tabindex="7" />
										</span>
										</span>
									  </div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 EKG
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_ekg')">
										<span class="colorChkBx" style=" <?php if($ekg) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox" onClick="javascript:checkSingle('chbx_ekg_yes','chbx_ekg')" value="Yes" name="chbx_ekg" id="chbx_ekg_yes" <?php if($ekg=="Yes") { echo "checked"; }?> tabindex="7" />
										</span>
										</span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_ekg')">
										<span class="colorChkBx" style=" <?php if($ekg) { echo $whiteBckGroundColor;}?>">
											<input type="checkbox" onClick="javascript:checkSingle('chbx_ekg_no','chbx_ekg')" value="No" name="chbx_ekg" id="chbx_ekg_no" <?php if($ekg=="No") { echo "checked"; }?> tabindex="7"/>
											</span></span>
									  </div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   
					   <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Consent Signed
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_cons_sign')">
										<span class="colorChkBx" style=" <?php if($consentSign) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox" onClick="javascript:checkSingle('chbx_cons_sign_yes','chbx_cons_sign')" value="Yes" name="chbx_cons_sign" id="chbx_cons_sign_yes" <?php if($consentSign=="Yes") { echo "checked"; }?> tabindex="7" />
										</span></span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_cons_sign')">
										<span class="colorChkBx" style=" <?php if($consentSign) { echo $whiteBckGroundColor;}?>" >
											<input type="checkbox" onClick="javascript:checkSingle('chbx_cons_sign_no','chbx_cons_sign')" value="No" name="chbx_cons_sign" id="chbx_cons_sign_no" <?php if($consentSign=="No") { echo "checked"; }?> tabindex="7" />
										</span></span>
									  </div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
						 <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 H & P
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_h_p')">
										<span class="colorChkBx" style=" <?php if($hp) { echo $whiteBckGroundColor;}?>" />
										<input type="checkbox" onClick="javascript:checkSingle('chbx_h_p_yes','chbx_h_p')" value="Yes" name="chbx_h_p" id="chbx_h_p_yes" <?php if($hp=="Yes") { echo "checked"; }?> tabindex="7" />
										</span></span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_h_p')">
										<span class="colorChkBx" style=" <?php if($hp) { echo $whiteBckGroundColor;}?>">
											<input type="checkbox" onClick="javascript:checkSingle('chbx_h_p_no','chbx_h_p')" value="No" name="chbx_h_p" id="chbx_h_p_no" <?php if($hp=="No") { echo "checked"; }?> tabindex="7" />
											</span></span>
									  </div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   
					   <div class="inner_safety_wrap" id="adm_hosp_id_main">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Admitted To Hospital in Past 30 Days
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" onClick="" style=" <?php if($admitted2Hospital) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox"  onClick="javascript:checkSingle('chbx_admit_to_hosp_yes','chbx_admit_to_hosp'),disp_new(this,'descr_11'),changeChbxColor('chbx_admit_to_hosp')" value="Yes" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_yes" <?php if($admitted2Hospital=="Yes") { echo "checked"; }?> class="<?php if($admitted2Hospital=="Yes") { echo "uncollapse"; }?>" tabindex="7" />
										</span>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_admit_to_hosp')">
										<span class="colorChkBx" style=" <?php if($admitted2Hospital) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox" onClick="javascript:checkSingle('chbx_admit_to_hosp_no','chbx_admit_to_hosp'),disp_none_new(this,'descr_11')" value="No" name="chbx_admit_to_hosp" id="chbx_admit_to_hosp_no" <?php if($admitted2Hospital=="No") { echo "checked"; }?> tabindex="7" />
										</span></span>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
									  <a id="chbx_admit_to_hosp_collapse" href="javascript:void(0)" data-toggle="collapse" data-target="#descr_11" class="toggle_desc collapsed">
										   <span class="fa fa-angle-double-down"></span>
									   </a>
									</div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   
					   <div id="descr_11" class="inner_safety_wrap collapse <?php if($admitted2Hospital=="Yes") { echo "in"; }?>" style="height: auto;">
						  <div class="well">
							  <div class="row">
								  <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
									  <label class="date_r">
										  Reason
									  </label>
								  </div>
								  <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
									  <textarea class="form-control" style="resize:none;" name="txtarea_admit_to_hosp" tabindex="6"><?php echo stripslashes($reason);?></textarea> 
								  </div> <!-- Col-3 ends  -->
							  </div>
						  </div> 
					   </div>
					   
						<div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
								  <label class="date_r">
									  Blood Sugar 	
								  </label>
							  </div>
							  <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
								  <div class="row">
									<div class="col-md-5 col-sm-4 col-xs-6 col-lg-5">	
										  <label class="date_r" for="na">
											<span class="clpChkBx" onClick="changeDiffChbxColor(2,'chkBoxNS','bsvalue');">
													<span class="colorChkBx" style=" <?php if($NA=="1" || $bsValue ) { echo $whiteBckGroundColor;}?>" >
													<input type="checkbox" name="chkBoxNS" id="chkBoxNS" value="1" <?php if($NA=="1") { echo "checked";  }?> tabindex="7" onChange="javascript:if(this.checked==true){this.value='1';document.frm_pre_op_nurs_rec.bsvalue.value = '';}else{this.value='0';}"/></span></span> N/A </label>	
									  </div>
									  <div class="col-md-7 col-sm-8 col-xs-6 col-lg-7">	
										   <div class="row">
											<div class="col-md-6 col-sm-4 col-xs-6 col-lg-6">	
												  <label class="date_r" for="val_sugar"> Value</label>
											  </div>
											  <div class="col-md-6 col-sm-8 col-xs-6 col-lg-6">	
													<input class="form-control" type="text" name="bsvalue" id="bsvalue" tabindex="1" onKeyUp="changeDiffChbxColor(2,'chkBoxNS','bsvalue'); bsValueUnchk(this,'frm_pre_op_nurs_rec');" onBlur="changeDiffChbxColor(2,'chkBoxNS','bsvalue');bsValueUnchk(this,'frm_pre_op_nurs_rec');" value="<?php echo $bsValue;?>" />
											  </div>
										  </div>
									  </div>
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  	  <?php
							  if($version_num > 2) {
							  ?>
                                  <div class="col-md-10 col-sm-10 col-xs-12 col-lg-10" style="font-weight:bold;">
                                      **Normal blood glucose level is lower than 140 mg/dL (7.8 mmol/L)
                                  </div>
                                  <div class="col-md-2 col-sm-2 col-xs-12 col-lg-2">
                                      &nbsp;
                                  </div>
                              <?php
							  }
							  ?>
                          </div>	 
					   </div>
				  </div>
			  </Div>           
		 </div>
		 <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
			  <Div class="panel panel-default bg_panel_qint">
				  <div class="panel-heading">
					  <!-- h3 class="panel-title rob" -->
						<a href="javascript:void(0)" id="toggleBp-elements" data-toggle="collapse" data-target="#BP_div" class="">
							<span class="fa fa-stethoscope" style="font-size:21px;color: #fff;"></span> 
						</a>
						
						<Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right" style="padding-left:22px; cursor: pointer;color: #fff;">
							  <label class="col-sm-5 col-xs-5 col-lg-5" style="margin-bottom:0px; padding-top:5px;">  Yes  </label>
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4" style="margin-bottom:0px; padding-top:5px;">  No  </label>	                                                        		
							  <!--label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label-->
						  </Div>
						<div class="clearfix"></div>
					  <!--/h3>
					  <Div class="right_label top_yes_no">
						  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right">
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  Yes  </label>
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  No  </label>	                                                        		
							  <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label>
						  </Div>
					  </Div-->
				  </div>
				  <div class="panel-body">
					  <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Health Questionnaire
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_hlt_ques')">
											<span class="colorChkBx" style=" <?php if($healthQuestionnaire) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox" onClick="javascript:checkSingle('chbx_hlt_ques_yes','chbx_hlt_ques')" value="Yes" name="chbx_hlt_ques" id="chbx_hlt_ques_yes" <?php if($healthQuestionnaire=="Yes") { echo "checked"; }?> tabindex="7" />
										</span></span>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="clpChkBx" onClick="changeChbxColor('chbx_hlt_ques')">
										<span class="colorChkBx" style=" <?php if($healthQuestionnaire) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_hlt_ques_no','chbx_hlt_ques')" value="No" name="chbx_hlt_ques" id="chbx_hlt_ques_no" <?php if($healthQuestionnaire=="No") { echo "checked"; }?> tabindex="7" />
										</span></span>
										
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										&nbsp;
									</div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>	
					   <div class="inner_safety_wrap">
							<div class="row">
								<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
									<label class="date_r">
										Standing Orders
									</label>
								</div>
								<div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
									<div class="">
										<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
											<span class="colorChkBx" style=" <?php if($standingOrders) { echo $whiteBckGroundColor;}?>" >
												<input type="checkbox" onClick="javascript:checkSingle('chbx_stnd_odrs_yes','chbx_stnd_odrs'); changeChbxColor('chbx_stnd_odrs');" value="Yes" name="chbx_stnd_odrs" id="chbx_stnd_odrs_yes" <?php if($standingOrders=="Yes") { echo "checked"; }?> tabindex="7" />
											</span>
										</div>
										<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
											<span class="colorChkBx" style=" <?php if($standingOrders) { echo $whiteBckGroundColor;}?>" >
												<input type="checkbox" onClick="javascript:checkSingle('chbx_stnd_odrs_no','chbx_stnd_odrs'); changeChbxColor('chbx_stnd_odrs');" value="No" name="chbx_stnd_odrs" id="chbx_stnd_odrs_no" <?php if($standingOrders=="No") { echo "checked"; }?> tabindex="7" />
											</span>
										</div>
										 <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
											&nbsp;
										</div>                                      
									</div> 
								</div> <!-- Col-3 ends  -->
							</div>
					   </div>
					   
					  <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Pat. Voided
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($patVoided) { echo $whiteBckGroundColor;}?>" >
										<input type="checkbox" onClick="javascript:checkSingle('chbx_pat_void_yes','chbx_pat_void'),changeChbxColor('chbx_pat_void')" value="Yes" name="chbx_pat_void" id="chbx_pat_void_yes" <?php if($patVoided=="Yes") { echo "checked"; }?> tabindex="7" />
										</span>
									  </div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($patVoided) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_pat_void_no','chbx_pat_void'),changeChbxColor('chbx_pat_void')" value="No" name="chbx_pat_void" id="chbx_pat_void_no" <?php if($patVoided=="No") { echo "checked"; }?> tabindex="7"/>
										</span>
									</div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Hearing Aids
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
										<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
											<span class="colorChkBx" style=" <?php if($hearingAids) { echo $whiteBckGroundColor;}?>">
												<input type="checkbox" onClick="javascript:checkSingle('chbx_hearingAids_yes','chbx_hearingAids'),disp_new(this,'descr_h_aid'),changeChbxColor('chbx_hearingAids')" <?php if($hearingAids=='Yes') echo "CHECKED"; ?> name="chbx_hearingAids" value="Yes" id="chbx_hearingAids_yes"  tabindex="7" />
											</span>
										</div>
										<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
											<span class="colorChkBx" style=" <?php if($hearingAids) { echo $whiteBckGroundColor;}?>">
												<input type="checkbox" onClick="javascript:checkSingle('chbx_hearingAids_no','chbx_hearingAids'),disp_none_new(this,'descr_h_aid'),changeChbxColor('chbx_hearingAids')" <?php if($hearingAids=='No') echo "CHECKED"; ?>  name="chbx_hearingAids" value="No" id="chbx_hearingAids_no" tabindex="7" />
											</span>
										</div>
										<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
											<a href="javascript:void(0)" data-toggle="collapse" data-target="#descr_h_aid" class="toggle_desc collapsed">
											  <span class="fa fa-angle-double-down"></span>
										  </a>
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
						<div id="descr_h_aid" class="inner_safety_wrap collapse <?php if($hearingAids=='Yes') echo "in"; ?>" style="height: auto;">
						  <div class="well">
							  <label class="date_r"  for="removed">
							   <input class="" type="checkbox" onClick="checkyes('chbx_hearingAids_yes','chbx_hearingAidsRemoved_yes','chbx_hearingAids_no'),checkSingle('chbx_hearingAidsRemoved_yes','chbx_hearingAidsRemoved')"  <?php if($hearingAidsRemoved=="Yes") echo "Checked";  ?> name="chbx_hearingAidsRemoved" value="Yes" id="chbx_hearingAidsRemoved_yes" tabindex="7" /> Removed
							  </label> &nbsp; &nbsp;
							   <label class="date_r" for="covered">
							   <input class="" type="checkbox" onClick="checkyes('chbx_hearingAids_yes','chbx_hearingAidsRemoved_no','chbx_hearingAids_no'),checkSingle('chbx_hearingAidsRemoved_no','chbx_hearingAidsRemoved')" <?php if($hearingAidsRemoved=="No") echo "Checked";  ?> name="chbx_hearingAidsRemoved" value="No" id="chbx_hearingAidsRemoved_no" tabindex="7" /> Covered
							  </label>
						  </div> 
					   </div>
					   
					   <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Denture
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($denture) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_denture_yes','chbx_denture'),disp_new(this,'descr_h_den'),changeChbxColor('chbx_denture')" <?php if($denture=='Yes') echo "CHECKED"; ?> name="chbx_denture" value="Yes" id="chbx_denture_yes"  tabindex="7" />
										</span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($denture) { echo $whiteBckGroundColor;}?>" >
											<input type="checkbox" onClick="javascript:checkSingle('chbx_denture_no','chbx_denture'),disp_none_new(this,'descr_h_den'),changeChbxColor('chbx_denture')" <?php if($denture=='No') echo "CHECKED"; ?>  name="chbx_denture" value="No" id="chbx_denture_no" tabindex="7" />
										</span>
									  </div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
											<a href="javascript:void(0)" data-toggle="collapse" data-target="#descr_h_den" class="toggle_desc collapsed">
											  <span class="fa fa-angle-double-down"></span>
										  </a>
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
						<div id="descr_h_den" class="inner_safety_wrap collapse <?php if($denture=='Yes') echo "in"; ?>" style="height: auto;">
						  <div class="well">
							   <div class="row">
								  <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
									  Removed
								  </div>
								   <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8">
										 <label class="date_r"  for="chbx_dentureRemoved_yes">
											   <input class="" type="checkbox" onClick="checkyes('chbx_denture_yes','chbx_dentureRemoved_yes','chbx_denture_no'),checkSingle('chbx_dentureRemoved_yes','chbx_dentureRemoved')" <?php if($dentureRemoved=="Yes") echo "Checked";  ?> name="chbx_dentureRemoved" value="Yes" id="chbx_dentureRemoved_yes" tabindex="7" /> Yes
										  </label> &nbsp; &nbsp;
										   <label class="date_r" for="no">
											   <input class="" type="checkbox" onClick="checkyes('chbx_denture_yes','chbx_dentureRemoved_no','chbx_denture_no'),checkSingle('chbx_dentureRemoved_no','chbx_dentureRemoved')" <?php if($dentureRemoved=="No") echo "checked";  ?> name="chbx_dentureRemoved" value="No" id="chbx_dentureRemoved_no" tabindex="7" /> No
										  </label>        
								   </div>
							   </div>   	
						  </div> 
					   </div>
					   
					   <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Any Pain
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($anyPain) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_anyPain_yes','chbx_anyPain'),changeChbxColor('chbx_anyPain')" value="Yes" name="chbx_anyPain" id="chbx_anyPain_yes" <?php if($anyPain=="Yes") { echo "checked"; }?> tabindex="7" />
										</span>
									  </div>
									  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($anyPain) { echo $whiteBckGroundColor;}?>">
											<input type="checkbox" onClick="javascript:checkSingle('chbx_anyPain_no','chbx_anyPain'),changeChbxColor('chbx_anyPain')" value="No" name="chbx_anyPain" id="chbx_anyPain_no" <?php if($anyPain=="No") { echo "checked"; }?> tabindex="7" />
										</span>
									  </div>
									   <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										  &nbsp;
									  </div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   
					   
					   <div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
								  <Div class="row">
									  <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
											<label class="date_r" for="Pain">
												Pain Level
											</label>		        
									  </div>
									  
									  <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
											<select class="form-control selectpicker" name="painLevel" id="Pain"> 
												<option value=""></option>
													<?php
													for($i=0;$i<=10;$i++) {
													?>
														<option value="<?php echo $i;?>" <?php if($painLevel==$i) echo 'selected'; ?>><?php echo $i;?></option>
													<?php
													}
													?>
												</select>
											</select>
									  </div>
								  </Div>
							  </div>
							  <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
								  <Div class="row">
									  <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
											<label class="date_r" for="location">
												 Location
											  </label>		        
									  </div>
									  
									  <div class="col-md-6 col-sm-12 col-xs-6 col-lg-6">
											  <input class="form-control" type="text"  name="painLocation" value="<?php echo $painLocation;?>" size="7" id="location"/>
									  </div>
								  </Div>
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
						
						<div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
								  <label class="date_r">
									 Dr. Notified
								  </label>
							  </div>
							  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
								  <div class="">
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($doctorNotified) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_doctorNotified_yes','chbx_doctorNotified'),changeChbxColor('chbx_doctorNotified')" value="Yes" name="chbx_doctorNotified" id="chbx_doctorNotified_yes" <?php if($doctorNotified=="Yes") { echo "checked"; }?> tabindex="7" />
										</span>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
										<span class="colorChkBx" style=" <?php if($doctorNotified) { echo $whiteBckGroundColor;}?>">
										<input type="checkbox" onClick="javascript:checkSingle('chbx_doctorNotified_no','chbx_doctorNotified'),changeChbxColor('chbx_doctorNotified')"  value="No" name="chbx_doctorNotified" id="chbx_doctorNotified_no" <?php if($doctorNotified=="No") { echo "checked"; }?> tabindex="7" />
										</span>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
										&nbsp;
									</div>                                      
								  </div> 
							  </div> <!-- Col-3 ends  -->
						  </div>	 
					   </div>
					   
					<div class="inner_safety_wrap">
						  <div class="row">
							  <div class="col-md-12 col-sm-12 col-xs-4 col-lg-4">
								 <Div class="row">
									  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
											<label class="date_r" for="txt_feet">
												Height
											  </label>		        
									  </div>
									  
									  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
										   <Div class="row">
											  <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">
												 <select class="form-control selectpicker" type="text" name="txt_feet" id="txt_feet"> 
													  <option value="" <?php if($feet=="") { echo "selected"; }?>>Ft</option>
													<option value="1" <?php if($feet==1) { echo "selected"; }?>>1</option>
													<option value="2" <?php if($feet==2) { echo "selected"; }?>>2</option>
													<option value="3" <?php if($feet==3) { echo "selected"; }?>>3</option>
													<option value="4" <?php if($feet==4) { echo "selected"; }?>>4</option>
													<option value="5" <?php if($feet==5) { echo "selected"; }?>>5</option>
													<option value="6" <?php if($feet==6) { echo "selected"; }?>>6</option>
													<option value="7" <?php if($feet==7) { echo "selected"; }?>>7</option>
													</select>
												  <small> feet</small>                                                                            
											  </div>
											  <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">
												 <select class="form-control selectpicker" type="text" name="txt_inch" id="txt_inch"> 
													<option value="">In</option>
													<option value="0" <?php if($inch=="0") { echo "selected"; }?>>0</option>
													<option value="1" <?php if($inch=="1") { echo "selected"; }?>>1</option>
													<option value="2" <?php if($inch=="2") { echo "selected"; }?>>2</option>
													<option value="3" <?php if($inch=="3") { echo "selected"; }?>>3</option>
													<option value="4" <?php if($inch=="4") { echo "selected"; }?>>4</option>
													<option value="5" <?php if($inch=="5") { echo "selected"; }?>>5</option>
													<option value="6" <?php if($inch=="6") { echo "selected"; }?>>6</option>
													<option value="7" <?php if($inch=="7") { echo "selected"; }?>>7</option>
													<option value="8" <?php if($inch=="8") { echo "selected"; }?>>8</option>
													<option value="9" <?php if($inch=="9") { echo "selected"; }?>>9</option>
													<option value="10" <?php if($inch=="10") { echo "selected"; }?>>10</option>
													<option value="11" <?php if($inch=="11") { echo "selected"; }?>>11</option>                                                 
												  </select>
												  <small> inch</small>                        
											  </div>
										   </Div>
									  </div>
								  </Div>
							  </div>
<?php 
	$calculatorTopValue = "122";
	$calculatorTopChangeValue = "20";
	if($hearingAids=="Yes") { $calculatorTopChangeValue = $calculatorTopChangeValue+20; }
	if($denture=="Yes") { $calculatorTopChangeValue = $calculatorTopChangeValue+20; }
	$calculatorWeightSetValue = $calculatorTopValue+$calculatorTopChangeValue;
	
	$bpPrTempTopValue = "20";
	$calculatorBP_P_R_Temp_SetValue = $bpPrTempTopValue+$calculatorTopValue+$calculatorTopChangeValue;
	
	$pre_operative_commentValue = "138";
	$pre_operative_commentSetValue = $pre_operative_commentValue+$calculatorTopValue+$calculatorTopChangeValue;
?>
							  <div class="col-md-12 col-sm-12 col-xs-4 col-lg-4">
								 <Div class="row">
									  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
											<label class="date_r" for="bp_temp5">
												Weight
											  </label>		        
									  </div>
									  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 text-center">
										   <input class="form-control" type="text" id="bp_temp5" name="txt_patientWeight" size="2" value="<?php echo $patientWeight; ?>" tabindex="1" style="border: 1px solid #cccccc;" onKeyUp="displayText5=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp5'))-185,parseInt(findPos_X('bp_temp5'))-70,'flag5');"/>
										   <small> lbs </small>
									  </div>
								  </Div>
							  </div>
                              
                              <div class="col-md-12 col-sm-12 col-xs-4 col-lg-4">
								 <Div class="row">
									  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
											<label class="date_r" for="txt_feet">
												BMI
											  </label>		        
									  </div>
									  
									  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
										   <Div class="row">
											  <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 text-center">
												<input type="text" id="bmiValue" name="txt_patientBmi"  class="form-control" readonly value="<?=$patientBMI?>" /> 
												 
											  </div>
											  
										   </Div>
									  </div>
								  </Div>
							  </div>
                              
							  
						  </div>	 
					   </div>



<div class="inner_safety_wrap" id="vital_sign_main_id">
<?php
$ViewPreopNurseVitalSignQry = "select * from `preopnursing_vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
$ViewPreopNurseVitalSignRes = imw_query($ViewPreopNurseVitalSignQry) or die(imw_error()); 
$ViewPreopNurseVitalSignNumRow = imw_num_rows($ViewPreopNurseVitalSignRes);
if($ViewPreopNurseVitalSignNumRow>0) {
	$m=1;
	while($ViewPreopNurseVitalSignRow = imw_fetch_array($ViewPreopNurseVitalSignRes)) {
		$vitalsign_id=$ViewPreopNurseVitalSignRow["vitalsign_id"];  
		$vitalSignBp = $ViewPreopNurseVitalSignRow["vitalSignBp"];
		$vitalSignP = $ViewPreopNurseVitalSignRow["vitalSignP"];
		$vitalSignR = $ViewPreopNurseVitalSignRow["vitalSignR"];
		$vitalSignO2SAT = $ViewPreopNurseVitalSignRow["vitalSignO2SAT"];
		$vitalSignTemp = $ViewPreopNurseVitalSignRow["vitalSignTemp"];
		//$vitalSignTime = $ViewPreopNurseVitalSignRow["vitalSignTime"];
		if($m%2==0) {
			$bg_color_pre_op_nurse = $rowcolor_pre_op_nursing_order;
		}else {
			$bg_color_pre_op_nurse = "#FFFFFF";
		} 
		
?>

					   <div class="inner_safety_wrap" id="BP_div<?php echo $vitalsign_id; ?>">
							<div class="row">
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12 " style="padding:6px 12px;color:#800080;cursor: pointer; font-weight:bold;" onClick="changeBaseLine('<?php echo $vitalsign_id; ?>','<?php echo $vitalSignBp;?>','<?php echo $vitalSignP;?>','<?php echo $vitalSignR;?>','<?php echo $vitalSignO2SAT;?>','<?php echo $vitalSignTemp;?>');" >
										  BP
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12 padding_2">
									  <span class="form-control no-controle-style"><?php echo $vitalSignBp;?></span>                                                                   
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12" style=" padding:6px 12px;font-weight:bold;">
										  P
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12 padding_2">
									  <span class="form-control no-controle-style"><?php echo $vitalSignP;?></span>
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12" for="r" style=" font-weight:bold; padding:6px 12px;">
										  R
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12 padding_2">
									  <span class="form-control no-controle-style">
										<?php echo $vitalSignR;?>
									  </span>
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-3 col-sm-3 col-xs-3 col-lg-2">
								  <Div class="row">
									  <label class="col-md-12 col-lg-3 col-xs-12 col-sm-12" for="O2" style=" font-weight:bold; padding:6px 12px;">
										  O<sub>2</sub>SAT
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12">
									  <span class="form-control no-controle-style">
										<?php echo $vitalSignO2SAT;?>
									  </span>
									  </Span>
								  </Div>
							  </div>
							  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-3">
								  <Div class="row">
									  <label class="col-md-12 col-lg-2 col-xs-12 col-sm-12" for="Temp" style="padding:6px 12px;">
										  Temp
									  </label>    
									  <Span class="col-md-12 col-lg-9 col-xs-12 col-sm-12">
                                          <span class="form-control no-controle-style">
                                            <?php echo $vitalSignTemp;?>
                                          </span>
                                      </Span>
                                      
								  </Div>
							  </div>
							   <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 text-center">
								  <a href="javascript:void(0)" onClick="delentry(<?php echo $vitalsign_id; ?>);" class="btn btn-danger" style="margin:10% 0">  X </a>
							  </div>
							</div>  
					   </div>
							
<?php
			$m++;
		}
	}else {
		//DO NOTHING
	}
	
	//$calculatorTopValue = $k*22+65;
	//$calculatorTopChangeValue = "0";
	//echo "dfg = ".$Displayvital_sign_2_id;
	if($ViewPreopNurseVitalSignNumRow>0) {
		 $Displayvital_sign_2_id="";
		 $calculatorTopChangeValue=($ViewPreopNurseVitalSignNumRow*20);
		 $preVitalBackColor==$whiteBckGroundColor;
	}else{
		 $Displayvital_sign_2_id="in";
		 $preVitalBackColor=$chngBckGroundColor;
	}
?>
					<div class="inner_safety_wrap collapse <?php echo $Displayvital_sign_2_id; ?>" id="BP_div">
						<input type="hidden"  id="hidd_preopnursing_vitalsign_id" name="hidd_preopnursing_vitalsign_id" value="<?php echo $preopnursing_vitalsign_id;?>">
						<div class="row">
						  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
								<input type="hidden"  id="rowM" name="rowM" value="<?php echo $m;?>">
							  <Div class="row">
								  <label class="col-md-12 col-lg-12 col-xs-12 col-sm-12" for="bp_temp">
									  BP
								  </label>    
								  <Span class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="padding-right:2px !important">
									<input class="form-control padding_2" type="text" id="bp_temp" name="vitalSignBP_main" style="<?php if($ViewPreopNurseVitalSignNumRow>0){ echo $whiteBckGroundColor;}else{ echo $chngBckGroundColor;} ?>" onKeyUp="displayText1=this.value;changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt($(this).offset().top -160),parseInt($(this).offset().left),'flag1');clearVal_c();" onFocus="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" /><input type="hidden" id="bp" name="bp_hidden" onBlur="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');">
								  </Span>
							  </Div>
						  </div>
						  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
							  <Div class="row">
								  <label class="col-md-12 col-lg-12 col-xs-12 col-sm-12" for="bp_temp2">
									  P
								  </label>    
								  <Span class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="padding-right:2px !important">
									<input class="form-control padding_2" type="text" id="bp_temp2" name="vitalSignP_main" style="<?php if($ViewPreopNurseVitalSignNumRow>0){ echo $whiteBckGroundColor;}else{ echo $chngBckGroundColor;} ?>" onKeyUp="displayText2=this.value;changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt($(this).offset().top -160),parseInt($(this).offset().left),'flag2');clearVal_c();" onFocus="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');"  onBlur="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" />
								  </Span>
							  </Div>
						  </div>
						  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
							  <Div class="row">
								  <label class="col-md-12 col-lg-12 col-xs-12 col-sm-12" for="bp_temp3">
									  R
								  </label>    
								  <Span class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="padding-right:2px !important">
								  <input class="form-control padding_2" type="text" name="vitalSignR_main" id="bp_temp3" style="<?php if($ViewPreopNurseVitalSignNumRow>0){ echo $whiteBckGroundColor;}else{ echo $chngBckGroundColor;} ?>" onKeyUp="displayText3=this.value;changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt($(this).offset().top -160),parseInt($(this).offset().left),'flag3');clearVal_c();"  onFocus="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');"  onBlur="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" />
								  </Span>
							  </Div>
						  </div>
						  <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3">
							  <Div class="row">
								  <label class="col-md-12 col-lg-12 col-xs-12 col-sm-12" for="bp_temp4">
									  O<sub>2</sub>SAT
								  </label>    
								  <Span class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="padding-right:2px !important">
								  <input class="form-control padding_2" type="text" name="vitalSignO2SAT_main" id="bp_temp4" style="<?php if($ViewPreopNurseVitalSignNumRow>0){ echo $whiteBckGroundColor;}else{ echo $chngBckGroundColor;} ?>" onKeyUp="displayText4=this.value;changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" onClick="getShowNewPos(parseInt($(this).offset().top -160),parseInt($(this).offset().left),'flag4');clearVal_c();"  onFocus="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');"  onBlur="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" />                                                                    
								  </Span>
							  </Div>
						  </div>
						  <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">
							  <Div class="row">
								  <label class="col-md-12 col-lg-12 col-xs-12 col-sm-12" for="bp_temp7">
									  Temp
								  </label>    
								  <Span class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="padding-right:2px !important">
								  <input class="form-control padding_2" type="text" name="vitalSignTemp_main" id="bp_temp7"style="<?php if($ViewPreopNurseVitalSignNumRow>0){ echo $whiteBckGroundColor;}else{ echo $chngBckGroundColor;} ?>" onKeyUp="displayText7=this.value;changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7')" onClick="getShowTemp(parseInt($(this).offset().top -160),parseInt($(this).offset().left - 100),'flag7');clearVal_c();"  onFocus="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');"  onBlur="changeTxtGroupColor(5,'bp_temp3','bp_temp4','bp_temp','bp_temp2','bp_temp7');" />                                                                    
								  </Span>
							  </Div>
						  </div>
						   <div class="col-md-1 col-sm-1 col-xs-1 col-lg-1 text-center"><br />
							  <a href="javascript:void(0)" id="preopNurseVitalSaveId" class="btn btn-success" style="margin:10% 0" onClick="javascript:save_vitalsign_value();save_hide_row_idTemp('vital_sign_2_id','bp_temp','bp_temp2','bp_temp3','bp_temp4','bp_temp7');"><span class="fa fa-save"></span></a>
						  </div>
						</div>  
				   </div>

</div>					
				  </div>
			  </Div>           
		 </div>
	<!-----------------------------------   Second Col      ---------------------------------------->
	<Div class="clearfix margin_adjustment_only"></Div>
<?php
if(($form_status!='completed' && $form_status!='not completed') || $saveFromChart == '1') {

	//echo $form_status;
	$preopnursecategoryQry = "SELECT * FROM preopnursecategory ORDER BY categoryId";
	$preopnursecategoryRes = imw_query($preopnursecategoryQry) or die(imw_error());
	$preopnursecategoryNumRow = imw_num_rows($preopnursecategoryRes);
	if($preopnursecategoryNumRow>0) {
		$k=0;
		while($preopnursecategoryRow = imw_fetch_array($preopnursecategoryRes)) {
			$categoryId = $preopnursecategoryRow['categoryId'];
			$categoryName = $preopnursecategoryRow['categoryName'];
			$k++;
			
			$preopnursequestionQry = "SELECT * FROM preopnursequestion WHERE preOpNurseCatId='".$categoryId."'";
			$preopnursequestionRes = imw_query($preopnursequestionQry) or die(imw_error());
			$preopnursequestionNumRow = imw_num_rows($preopnursequestionRes);
			if($preopnursequestionNumRow>0) {
?>
		<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
		  <div class="well"> 
			   <Div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
						<label class="date_r col-md-12 col-sm-12 col-xs-12 col-lg-12"><?php echo $categoryName;?></label>
					</div>
					<div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
						<div class="row">
						<?php
							$t=0;
							while($preopnursequestionRow=imw_fetch_array($preopnursequestionRes)) {
								$t++;
								$preOpNurseQuestionId = $preopnursequestionRow['preOpNurseQuestionId'];
								$preOpNurseQuestionName = stripslashes($preopnursequestionRow['preOpNurseQuestionName']);
								$preOpNurseChkBoxQuestionName = str_replace(' ','_',$preOpNurseQuestionName);
								$preOpNurseChkBoxQuestionName = str_replace('.','SXD',$preOpNurseChkBoxQuestionName);
								$preOpNurseChkBoxQuestionName = str_replace('[','SXOSB',$preOpNurseChkBoxQuestionName);
								$showTxtBoxStatus 			  = $preopnursequestionRow['showTxtBoxStatus'];
							?>
								<label class="col-md-6 col-sm-6 col-xs-12 col-lg-6 nurse_question_label">
									<?php
									if($showTxtBoxStatus=='1') {echo $preOpNurseQuestionName;?>
                                    	<input type="text" class="form-control" name="<?php echo $preOpNurseChkBoxQuestionName.$k.$t;?>" tabindex="1" >
									<?php
									}else {?>
                                    	<input type="checkbox" name="<?php echo $preOpNurseChkBoxQuestionName.$k.$t;?>" value="<?php echo 'Yes'; ?>" /><?php echo $preOpNurseQuestionName;?> 
									<?php
									}
									?> 
                               </label>
						<?php
							}
						?>
						</div>
					</div>
				</Div>
			</div>
		</div>
		<Div class="clearfix visible-sm margin_adjustment_only"></Div>
		<?php
			}
		}
	}
}
else if($form_status=='completed' || $form_status=='not completed') {
	
	$preopnursecategoryQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id='".$_REQUEST["pConfId"]."' GROUP BY categoryName ORDER BY id";
	$preopnursecategoryRes = imw_query($preopnursecategoryQry) or die(imw_error());
	$preopnursecategoryNumRow = imw_num_rows($preopnursecategoryRes);
	if($preopnursecategoryNumRow>0) {
		$k=0;
		while($preopnursecategoryRow = imw_fetch_array($preopnursecategoryRes)) {
			$categoryName = $preopnursecategoryRow['categoryName'];
			$k++;
	
			$preopnursequestionQry = "SELECT * FROM preopnursequestionadmin WHERE categoryName='".$categoryName."' AND confirmation_id='".$_REQUEST["pConfId"]."' ORDER BY id";
			$preopnursequestionRes = imw_query($preopnursequestionQry) or die(imw_error());
			$preopnursequestionNumRow = imw_num_rows($preopnursequestionRes);
			if($preopnursequestionNumRow>0) {
	?>		
			<div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
				<div class="well"> 
					<Div class="row">
						<div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
							<label class="date_r col-md-12 col-sm-12 col-xs-12 col-lg-12"><?php echo $categoryName;?></label>
						</div>
						<div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
						  <div class="row">
							<?php
								$t=0;
								while($preopnursequestionRow=imw_fetch_array($preopnursequestionRes)) {
									$t++;
									$preOpNurseQuestionName = stripslashes($preopnursequestionRow['preOpNurseQuestionName']);
									$preOpNurseChkBoxQuestionName = str_replace(' ','~',$preOpNurseQuestionName);
									$preOpNurseChkBoxQuestionName = str_replace('.','SXD',$preOpNurseChkBoxQuestionName);
									$preOpNurseChkBoxQuestionName = str_replace('[','SXOSB',$preOpNurseChkBoxQuestionName);
									$preOpNurseOption = stripslashes($preopnursequestionRow['preOpNurseOption']);
									$showTxtBoxStatus = $preopnursequestionRow['showTxtBoxStatus'];
								?>
									<label class="col-md-6 col-sm-6 col-xs-12 col-lg-6 nurse_question_label">
										<?php
                                        if($showTxtBoxStatus=='1') {echo $preOpNurseQuestionName;?>
                                            <input type="text" class="form-control" name="<?php echo $preOpNurseChkBoxQuestionName.$k.$t;?>" value="<?php echo $preOpNurseOption;?>" tabindex="1" >
                                        <?php
                                        }else {?>
                                            <input type="checkbox" name="<?php echo $preOpNurseChkBoxQuestionName.$k.$t;?>" value="<?php echo 'Yes'; ?>" <?php if($preOpNurseOption=='Yes') { echo 'checked'; }?> /><?php echo $preOpNurseQuestionName;?> 
                                        <?php
                                        }
                                        ?> 
                                    </label>
							<?php
								}
							?>
						</div>
					</div>
				</Div>
			</div>
		</div>
		<Div class="clearfix visible-sm margin_adjustment_only"></Div>
		<?php
			}
		}
	}
}
?>
		 <div id="descr_21" class="inner_safety_wrap" style="height: auto;">
			  <div class="well">
				  <div class="row">
					  <div class="col-md-4 col-sm-12 col-xs-4 col-lg-2">
						  <a data-placement="top" class="panel-title rob alle_link show-pop-list_g btn btn-default" id="precomment_id" onClick="return showPreCommentsFnNew('pre_operative_comment_id', '', 'no',$(this).offset().left, parseInt($(this).offset().top - 185 )),document.getElementById('selected_frame_name_id').value='';"><span class="fa fa-caret-right"></span>Preoperative Comments</a>
					  </div>
					  <div class="clearfix visible-sm margin_adjustment_only"></div>
					  <div class="col-md-8 col-sm-12 col-xs-8 col-lg-10 text-center">
						<?php
							$defaultComments	=	'';
							if( $form_status <> 'completed' && $form_status <> 'not completed' && trim($preOpComments)== "")
							{
								$defaultComments	= $objManageData->getDefault('preopcomments','comments',"\n");
								$preOpComments		= $defaultComments;
							}
							
							
							if($preOpComments) {
								$preOpCommentsWithTime = $preOpComments;
							}else {
								$preOpCommentsWithTime = "";
							}
						?>
						  <textarea class="form-control" style="resize:none;" id="pre_operative_comment_id" name="txtarea_pre_operative_comment"><?php echo stripslashes($preOpCommentsWithTime);?></textarea> 
					  </div> <!-- Col-3 ends  -->
				  </div>
			  </div> 
		   </div>
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
		$callJavaFun = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_nursing_record_ajaxSign.php','$loginUserId','Nurse');";
	}

	$signOnFileStatus = "Yes";
	$TDnurseNameIdDisplay = "";
	$TDnurseSignatureIdDisplay = "";
	$NurseNameShow = $loggedInUserName;
	$pre_op_nurse_sign_date="";
	if($signNurseId<>0 && $signNurseId<>"") {
		$NurseNameShow = $signNurseName;
		$signOnFileStatus = $signNurseStatus;
		//$pre_op_nurse_sign_date =date("m-d-Y h:i A",strtotime($signNurseSignDate));	
		$pre_op_nurse_sign_date = $objManageData->getFullDtTmFormat($signNurseSignDate);
		$TDnurseNameIdDisplay = "none";
		$TDnurseSignatureIdDisplay = "in";
	}
	if($_SESSION["loginUserId"]==$signNurseId) {
		$callJavaFunDel = "document.frm_pre_op_nurs_rec.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_nursing_record_ajaxSign.php','$loginUserId','Nurse','delSign');";
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
				<div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay; ?>">
				  <a class="sign_link" href="javascript:void(0);" style="cursor:pointer;<?php echo $nurseSignBackColor?>;" onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</a>
			  </div>
			  <div id="TDnurseSignatureId" class="inner_safety_wrap collapse <?php echo $TDnurseSignatureIdDisplay;?>" style="height: 6px;">
				  <span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunDel;?>">Nurse: <?php echo $NurseNameShow; ?>  </span>	     
				  <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus; ?></span>
				  <span class="rob full_width"> <b> Signature Date</b><span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $pre_op_nurse_sign_date; ?> <span class="fa fa-edit"></span></span></span>
			  </div>
		 </div>
     
		 <div class="col-md-4 col-lg-4 col-xs-12 ">&nbsp;</div>
		 <div class="col-md-4 col-sm-6 col-lg-4 col-xs-12 pull-right">
			  <div class="inner_safety_wrap">
				  <label class="col-md-6 col-lg-6 col-xs-12 col-sm-12" for="r_nurse"> Relief Nurse</label>
				  <Div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
					<select class="selectpicker form-control" name="relivedNurseIdList" id="relivedNurseIdList"> 
						<option value="">Select</option>	
						<?php
						$relivedNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
						$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
						while($relivedNurseRow=imw_fetch_array($relivedNurseRes)) {
							$relivedSelectNurseID 			= $relivedNurseRow["usersId"];
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
	<!--  Middle Wrap -->
  
    <!-- NEcessary PUSH     -->	 
    <Div class="push"></Div>
    <!-- NEcessary PUSH     -->
</div>
</div>   
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="pre_op_nursing_record.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_op_nursing_record.php";
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
if($save == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
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
	//SET BP, P, R, TEMP VALUES IN HEADER
		//alert(document.getElementById('bp_temp').value);
		if(document.getElementById('bp_temp').value) {
			top.document.getElementById('header_BP').innerText=document.getElementById('bp_temp').value;
		}
		if(document.getElementById('bp_temp2').value) {
			top.document.getElementById('header_P').innerText=document.getElementById('bp_temp2').value;
		}
		if(document.getElementById('bp_temp3').value) {
			top.document.getElementById('header_R').innerText=document.getElementById('bp_temp3').value;
		}
		if(document.getElementById('bp_temp4').value) {
			top.document.getElementById('header_O2SAT').innerText=document.getElementById('bp_temp4').value;
		}
		if(document.getElementById('bp_temp7').value) {
			top.document.getElementById('header_Temp').innerText=document.getElementById('bp_temp7').value;
		}
	//SET BP, P, R, TEMP VALUES IN HEADER

	//SET BP, P, R, TEMP VALUES IN HEADER
		/*top.document.getElementById('header_BP').innerText=document.getElementById('bp_temp').value;
		top.document.getElementById('header_P').innerText=document.getElementById('bp_temp2').value;
		top.document.getElementById('header_R').innerText=document.getElementById('bp_temp3').value;
		top.document.getElementById('header_O2SAT').innerText=document.getElementById('bp_temp4').value;
		top.document.getElementById('header_Temp').innerText=document.getElementById('bp_temp7').value;
		*/
	//SET BP, P, R, TEMP VALUES IN HEADER
if(parent.parent) {
	parent.parent.show_loading_image('none');
}
</script>

<script type="text/javascript">
	$(function () {
			$('#datetimepicker1').datetimepicker({
				
					format: 'MM.DD.YYYY'
			
			});
			$('#datetimepicker2').datetimepicker({
				
					format: 'MM.DD.YYYY'
			
			});
	});
</script>
<script type="text/javascript">
	
	$(document).on('click', '.panel-heading.haed_p_clickable', function(e){
		var $this = $(this);
		if(!$this.hasClass('panel-collapsed')) {
			$this.parents('.panel').find('.panel-body').slideUp();
			$this.addClass('panel-collapsed');
			$this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
		} else {
			$this.parents('.panel').find('.panel-body').slideDown();
			$this.removeClass('panel-collapsed');
			$this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
		}
	})	
	$(document).ready(function () {
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
	

	$(document).ready(function() {
		//change date and time text to update form
	   $(document).on('click','#toggle_btn1',function(){	
				$('.toggled_1').toggleClass('toggle_AGAIN').first().stop().delay('slow');
		});
	   $(document).on('click','#toggle_btn3',function(){	
			$(this).hide('fast');
			$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
		});	
		$(document).on('click','#toggle_btn2',function(){	
			$('.toggled_2').toggleClass('toggle_AGAIN').stop().delay('slow');
			$('#toggle_btn3').show('fast');
		});	
	
	/*	
	*/
		$('#patient_form_m').click(function(){
					$('#patient_form').modal({show:true,backdrop:true});		
		});
		$('[data-toggle="tooltip"]').tooltip()
		$('.search_button').click(function(){
				$('.search_wrap').collapse('toggle');
				$('.date_wrap').collapse('toggle');
			});
		$(".dropdown").click(
			function() {
			
			});
			
		$(".dropdown-menu #a_allactivecancelled").click(function(){
			$("#allactivecancelled").hide();	
		});
	
		$(".dropdown-menu .radioFilter").prop('checked','true',function(){
			$("#allactivecancelled").hide();	
		});						
		
		$('.clickable').click(function(){
			$(this).closest('tr').next('tr').toggle('fade');
		 }); 
		$(".selectpicker").selectpicker();
		
		$(document).on('change blur', 'select#txt_feet, select#txt_inch, input#bp_temp5', function(){
			
			var Bmi	=	$("#bmiValue");
			var Feet	=	parseInt($("select#txt_feet").val());
				  Feet	=	(Feet) ? Feet * 12 : 0 ;
			var Inch	=	parseInt($("select#txt_inch").val());
				  Inch	= (Inch) ? Inch : 0; 
			var Wgt	=	$("input#bp_temp5").val();
			
			if(Feet && Wgt)
			{
				var Inches=	Feet  + Inch ;
				var BVal	=	(Wgt * 703 / (Inches * Inches));
					   BVal	=	BVal.toFixed(2);
				Bmi.val(BVal); 
				return false;
			}
			Bmi.val(''); 
		});
	});
	// Dropdown 
	// Dropdown
</script>

<script>
	
	function saveTimeBlur(medFieldValue,medRecordId,medFieldName)
	{
		//if(medFieldValue)
		{ 
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return true;
			}

			var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
			var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
			var recordId		=	document.getElementById('preOpNursingId').value;
		
			var url="pre_op_nurse_medication_ajax.php";
			url	+=	'?medFieldValue='+medFieldValue;
			url	+=	"&medRecordId="+medRecordId;
			url	+=	"&medFieldName="+medFieldName;
			url	+=	"&patient_id="+patient_id1;
			url	+=	'&recordId='+recordId;
			url	+=	'&action=saveTime';
			
			xmlHttp.onreadystatechange=AjaxSaveTime;
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
	}
	
	function AjaxSaveTime() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
			//ONLY SAVE TIME VALUES
			///alert(xmlHttp.responseText);
		}
	}

</script>
<?php
include("pre_op_meds_div.php");
include_once("print_page.php");
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>