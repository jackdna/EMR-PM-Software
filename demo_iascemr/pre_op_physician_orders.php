<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$current_form_version = 4;
include_once("common/commonFunctions.php"); 
$tablename = "preopphysicianorders";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
	form{margin:0px;}
	a.black:hover{color:"Red";	text-decoration:none;}
</style>
<?php
$spec = "
</head>
<body onLoad=\"top.changeColor('".$bglight_orange_physician."');\" onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();\">
";
include("common/link_new_file.php");
include_once("common/pre_define_opmed.php"); //PRE OP  PHYSICIAN
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
extract($_GET);
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];

$patient_id = $_REQUEST['patient_id'];
$pConfId = $_REQUEST['pConfId'];

if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}	
$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$relivednurse = $_REQUEST['relived_nurse'];
$Heparin_time=$_REQUEST['Heparin_time'];
$Heparin_start_user=$_REQUEST['Heparin_start_user'];

//UPDATING PATIENT STATUS IN STUB TABLE

 $patientdata=imw_query("select patientconfirmation.patientId,patientconfirmation.dos,
patientconfirmation.surgery_time,patient_data_tbl.patient_fname,patient_mname,
patient_data_tbl.patient_lname,patient_data_tbl.date_of_birth from patientconfirmation 
left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
where  patientconfirmation.patientConfirmationId='$pConfId'");

while($patient_data=imw_fetch_array($patientdata))
{
   $dos= $patient_data['dos'];
   $surgerytime=$patient_data['surgery_time'];
   $patient_fname=$patient_data['patient_fname'];
   $patient_mname=$patient_data['patient_mname'];
   $patient_lname=$patient_data['patient_lname'];
   $dob=$patient_data['date_of_birth'];
}
 $stub_data=imw_query("select * from stub_tbl where dos='$dos' && patient_first_name ='$patient_fname' && patient_middle_name='$patient_mname'
&& patient_last_name='$patient_lname' && patient_dob='$dob' && surgery_time='$surgerytime'");

while($stubtbl_data=imw_fetch_array($stub_data))
{
  $stub_id=$stubtbl_data['stub_id'];
} 
if($_REQUEST['submitMe'])
{
 //echo "update stub_tbl set patient_status='POS' where stub_id='$stub_id'";
 //$update_status=imw_query("update stub_tbl set patient_status='POS' where stub_id='$stub_id'");
}
//END OF CODE OF UPDATING STUB TABLE

// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$surgeonId = $detailConfirmation->surgeonId;
	
	//GET ASSIGNED SURGEON ID AND SURGEON NAME
		$preOpAssignedSurgeonId = $detailConfirmation->surgeonId;
		$preOpAssignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
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
	$anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
		unset($conditionArr);
		$conditionArr['usersId'] = $anesthesiologist_id;
		$anesthesiologistDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
		if($anesthesiologistDetails){
		foreach($anesthesiologistDetails as $usersDetail){
			$anesthesiologistName = $usersDetail->fname.' '.$usersDetail->lname;
			$signatureOfAnesthesiologist = $usersDetail->signature;
		}
	}	
	
	//GET LOGGED IN USER TYPE
	unset($conditionArr);
	$conditionArr['usersId'] = $_SESSION["loginUserId"];
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails){
		foreach($surgeonsDetails as $usersDetail)
		{
			$loggedInUserType = $usersDetail->user_type;
		}
	}
	//END GET LOGGED IN USER TYPE	
	
// GETTING CONFIRMATION DETAILS

if(!$cancelRecord){
	// FORM SHIFT TO RIGHT SLIDER
	$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId ', $pConfId);
	$pre_op_physician_order_form = $getLeftLinkDetails->pre_op_physician_order_form;	
	if($pre_op_physician_order_form=='true'){
		$formArrayRecord['pre_op_physician_order_form'] = 'false';
		$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
	}
	
	//MAKE AUDIT STATUS VIEW
	if(!$_REQUEST['submitMe']){
		unset($arrayRecord);
		$arrayRecord['user_id'] = $_SESSION['loginUserId'];
		$arrayRecord['patient_id'] = $patient_id;
		$arrayRecord['confirmation_id'] = $pConfId;
		$arrayRecord['form_name'] = 'pre_op_physician_order_form';
		$arrayRecord['status'] = 'viewed';
		$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
		$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
	}
	//MAKE AUDIT STATUS VIEW
	
	// FORM SHIFT TO RIGHT SLIDER
}elseif($cancelRecord){
			$fieldName = "pre_op_physician_order_form";
			$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId&ascId=$ascId";
			include("left_link_hide.php");
}			
	$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&ascId='.$ascId.'&fieldName='.$fieldName;

	////// FORM SHIFT TO LEFT SLIDER


//TIME saved in database
	 	   $Heparin_times=$_REQUEST['Heparin_time'];
	       $time_split = explode(" ",$Heparin_times);
	       
		if($time_split[1]=="PM" || $time_split[1]=="pm") {
			
			$time_split = explode(":",$time_split[0]);
			$medsTimeIncr=$time_split[0]+12;
			 $Heparin_time = $medsTimeIncr.":".$time_split[1].":00";
			
		}elseif($time_split[1]=="AM" || $time_split[1]=="am") {
		    $time_split = explode(":",$time_split[0]);
			$Heparin_time=$time_split[0].":".$time_split[1].":00";
			
			if($time_split[0]=="00" && $time_split[1]=="00") {
				$Heparin_time=$time_split[0].":".$time_split[1].":01";
			}
		}
	   //TIME saved in database

//GETTING CONFIRNATION DETAILS
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
	}
//GETTING CONFIRNATION DETAILS

$preopphy_patientConfirmSiteTempSite = $site;
// APPLYING NUMBERS TO PATIENT SITE
	if($preopphy_patientConfirmSiteTempSite == 1) {
		$preopphy_patientConfirmSiteTemp = "Left Eye";  //OD
	}else if($preopphy_patientConfirmSiteTempSite == 2) {
		$preopphy_patientConfirmSiteTemp = "Right Eye";  //OS
	}else if($preopphy_patientConfirmSiteTempSite == 3) {
		$preopphy_patientConfirmSiteTemp = "Both Eye";  //OU
	}else if($preopphy_patientConfirmSiteTempSite == 4) {
		$preopphy_patientConfirmSiteTemp = "Left Upper Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 5) {
		$preopphy_patientConfirmSiteTemp = "Left Lower Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 6) {
		$preopphy_patientConfirmSiteTemp = "Right Upper Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 7) {
		$preopphy_patientConfirmSiteTemp = "Right Lower Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 8) {
		$preopphy_patientConfirmSiteTemp = "Bilateral Upper Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 9) {
		$preopphy_patientConfirmSiteTemp = "Bilateral Lower Lid";
	}else{
		$preopphy_patientConfirmSiteTemp = "Operative Eye";
	}
// END APPLYING NUMBERS TO PATIENT SITE


// SAVE SUBMITTED FORM
if($_REQUEST['submitMe']){

	$text = $_REQUEST['getText'];
	$tablename = "preopphysicianorders";
	$formStatus = 'completed';
	unset($arrayRecord);
	$medicationTime = $_REQUEST['medicationTime'];
	if(!$medicationTime){
		$medicationTime = time();
	}
	$preOpPhysicianOrdersId = $_REQUEST['preOpPhysicianOrdersId'];	
	

	//START CODE TO CHECK NURSE,SURGEON SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfId);
		if($chkNurseSignDetails) {
			$chk_signNurseId = $chkNurseSignDetails->signNurseId;
			$chk_signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
			$chk_signNurseId1 = $chkNurseSignDetails->signNurse1Id;
			//CHECK FORM STATUS
				$chk_form_status = $chkNurseSignDetails->form_status;
			//CHECK FORM STATUS
			
			$chkVersionNum = $chkNurseSignDetails->version_num;
			$chkVersionDateTime = $chkNurseSignDetails->version_date_time;
		
		}
	//END CODE TO CHECK NURSE SIGN IN DATABASE 
	
	
	// Add Into Save Array list 
	if($chk_form_status <> 'completed' && $chk_form_status <> 'not completed')
	{
		$arrayRecord['prefilMedicationStatus'] = 'true';		
	}
	
	$honanBallon = $_REQUEST['chbx_hboes'];
	if($honanBallon == 'Yes'){
		$arrayRecord['honanBallon'] = '1';
		$honanBallonMin = $_REQUEST['list_eye_site'];
		$arrayRecord['honanBallonTime'] = $honanBallonMin;
	}else{
		$arrayRecord['honanBallon'] = '0';
	}
	
	
	$version_num	=	$chkVersionNum;
	if(!$chkVersionNum)
	{
		$version_date_time	=	$chkVersionDateTime;
		if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
		{
			$version_date_time	=	date('Y-m-d H:i:s');
		}
				
		if($chk_form_status == 'completed' || $chk_form_status=='not completed'){
			$version_num = 1;
		}else{
			$version_num	=	$current_form_version;
		}
		
		$arrayRecord['version_num']	=	$version_num;
		$arrayRecord['version_date_time']	=	$version_date_time;
	}
	
	// Start Validate Chart
	if( $chk_signSurgeon1Id=="0" || $chk_signNurseId1 == "0" || !$_REQUEST['chbx_noted_by_nurse']){
		$formStatus = 'not completed';
	}
	
	if($version_num == 1 && $formStatus == 'completed' && $chk_signNurseId=="0")
	{
		$formStatus = 'not completed';
	}
	if($formStatus == 'completed' && $version_num > 2 && !$_REQUEST['chbx_evaluated_patient'] )
	{
		$formStatus='not completed';
	}
	
	// End Validate Chart
	
	
	if($version_num <  2)
	{
			$arrayRecord['ivSelection'] = $_REQUEST['postop_physician'];
			$arrayRecord['ivSelectionOther'] = $_REQUEST['otherHeparinLock'];
			$arrayRecord['ivSelectionSide'] = $_REQUEST['chbx_ec'];	
			$arrayRecord['comments']= addslashes($_REQUEST['comments']);
			//$arrayRecord['prefilMedicationStatus'] = 'true';
			$arrayRecord['chbx_heparin_lockStart']= $_REQUEST['chbx_heparin_lockStart'];
			$arrayRecord['chbx_heparin_lock']= $_REQUEST['chbx_heparin_lock'];
			$chbx_KVO	 		= '';	
			$chbx_rate		= '';	
			$txtbox_rate 	= '';	
			$chbx_flu 		= '';	
			$txtbox_flu 	= '';	
			if($_REQUEST['chbx_heparin_lock']=='iv') {
				$chbx_KVO = $_REQUEST['chbx_KVO'];	
				$chbx_rate = $_REQUEST['chbx_rate'];	
				$txtbox_rate = $_REQUEST['txtbox_rate'];	
				$chbx_flu = $_REQUEST['chbx_flu'];	
				$txtbox_flu = $_REQUEST['txtbox_flu'];	
			}
			$arrayRecord['chbx_KVO'] = $chbx_KVO;
			$arrayRecord['chbx_rate'] = $chbx_rate;
			$arrayRecord['txtbox_rate'] = $txtbox_rate;
			$arrayRecord['chbx_flu'] = $chbx_flu;	
			$arrayRecord['txtbox_flu'] = $txtbox_flu;	
	
	}
	if( $version_num > 2 ) {
		$arrayRecord['evaluatedPatient'] = $_REQUEST['chbx_evaluated_patient'];	
	}
	$arrayRecord['preOpOrdersOther'] = addslashes($_REQUEST['otherPreOpOrders']);
	$arrayRecord['surgeonId'] = $surgeonId;
	$arrayRecord['anesthesiologistId'] = $anesthesiologist_id;
	$arrayRecord['surgeonSign'] = $_REQUEST['elem_signature1'];
	$arrayRecord['anesthesiologistSign'] = $_REQUEST['elem_signature2'];
	$arrayRecord['preOpPhysicianOrdersTime'] = $medicationTime;
	$arrayRecord['ascId'] = $ascId;
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['saveFromChart'] = $_REQUEST['saveFromChart'];
	$arrayRecord['patient_confirmation_id'] = $pConfId;	
	$arrayRecord['form_status'] = $formStatus;	
	$arrayRecord['relivednurse'] = $_REQUEST['relived_nurse'];
	
	
	
	$arrayRecord['notedByNurse'] = $_REQUEST['chbx_noted_by_nurse'];	
	$arrayRecord['medicationStartTime']=$_REQUEST['startTimeVal'][0];
	//MAKE AUDIT STATUS
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] = $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] = $patient_id;
	$arrayStatusRecord['confirmation_id'] = $pConfId;
	$arrayStatusRecord['form_name'] = 'pre_op_physician_order_form';		
	$arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS

	// UPDATE PATIENT STATUS POS
		unset($arrayPatientStatus);
		$arrayPatientStatus['patientStatus'] = 'POS';
		$objManageData->updateRecords($arrayPatientStatus, 'patientconfirmation', 'patientConfirmationId', $pConfId);
	// UPDATE PATIENT STATUS POS
	
	if(!$preOpPhysicianOrdersId){
		$insertId = $objManageData->addRecords($arrayRecord, 'preopphysicianorders');
		
	}else{
		$insertId = $preOpPhysicianOrdersId;			
		$objManageData->updateRecords($arrayRecord, 'preopphysicianorders', 'preOpPhysicianOrdersId', $preOpPhysicianOrdersId);
	}
	//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'pre_op_physician_order_form';
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

	$parientPreOpMediOrderId = $_REQUEST['parientPreOpMediOrderId'];
	$preOpPatientMediOrderArr = $_REQUEST['preOpMediOrderArr'];
	$preOpPatientStrengthArr = $_REQUEST['strengthArr'];
	$preOpPatientDirectionArr = $_REQUEST['directionArr'];
	$preOpPatienttimeArr = $_REQUEST['timemedsArr'];
	//echo '<pre>';print_r($_REQUEST);
	if($preOpPatientMediOrderArr) {
		foreach($preOpPatientMediOrderArr as $key => $medicationList){
			//CHECK MEDICATION ALREADY PREDEFINED OR NOT					
				unset($condArr);
				$condArr['medicationName'] = $medicationList;
				$condArr['strength'] = $preOpPatientStrengthArr[$key];
				$condArr['directions'] = $preOpPatientDirectionArr[$key];
				$getExistsOrNot = $objManageData->getMultiChkArrayRecords('preopmedicationorder', $condArr);
				if(count($getExistsOrNot)<=0){
					$objManageData->addRecords($condArr, 'preopmedicationorder');
				}		
			//CHECK MEDICATION ALREADY PREDEFINED OR NOT
			
			if(($medicationList=='') && ($parientPreOpMediOrderId[$key]!='')){
				$objManageData->delRecord('patientpreopmedication_tbl', 'patientPreOpMediId', $parientPreOpMediOrderId[$key]);
			}
			$tempr = $key+1;
			$preOpPatienttimeArr1 = $_REQUEST['starttimeExtra'.$tempr];
			
			if($medicationList!=''){
				unset($arrayRecord);
				$arrayRecord['preOpPhyOrderId'] = $insertId;
				$arrayRecord['patient_confirmation_id'] = $pConfId;
				$arrayRecord['medicationName'] = $medicationList;
				$arrayRecord['strength'] = $preOpPatientStrengthArr[$key];
				$arrayRecord['direction'] = $preOpPatientDirectionArr[$key];
				
				if($version_num < 2)
				{
					$arrayRecord['timemeds'] 	= $preOpPatienttimeArr[$key];
					$arrayRecord['timemeds1'] = $preOpPatienttimeArr1[0];
					$arrayRecord['timemeds2'] = $preOpPatienttimeArr1[1];
					$arrayRecord['timemeds3'] = $preOpPatienttimeArr1[2];
					$arrayRecord['timemeds4'] = $preOpPatienttimeArr1[3];
					$arrayRecord['timemeds5'] = $preOpPatienttimeArr1[4];
					$arrayRecord['timemeds6'] = $preOpPatienttimeArr1[5];
					$arrayRecord['timemeds7'] = $preOpPatienttimeArr1[6];
					$arrayRecord['timemeds8'] = $preOpPatienttimeArr1[7];
					$arrayRecord['timemeds9'] = $preOpPatienttimeArr1[8];
				}
				
				if(!$parientPreOpMediOrderId[$key]){
					$objManageData->addRecords($arrayRecord, 'patientpreopmedication_tbl');
				}else{ 
					$objManageData->updateRecords($arrayRecord, 'patientpreopmedication_tbl', 'patientPreOpMediId',$parientPreOpMediOrderId[$key]);
					
				}
			} 
		}
	}
	//CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedBySurgeon = chkSurgeonSignNew($_REQUEST["pConfId"]);
		$updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='".$chartSignedBySurgeon."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateStubTblRes = imw_query($updateStubTblQry) or die(imw_error());
	//END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE

	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
		
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

}
// SAVE SUBMITTED FORM

// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
	$getPreOpPhyDetails = $objManageData->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfId);
	if($getPreOpPhyDetails){ 
		$preOpPhysicianOrdersId = $getPreOpPhyDetails->preOpPhysicianOrdersId;
		$ivSelection = $getPreOpPhyDetails->ivSelection;
		$chbx_heparin_lockStart= $getPreOpPhyDetails->chbx_heparin_lockStart;
		$chbx_heparin_lock= $getPreOpPhyDetails->chbx_heparin_lock;
		$ivSelectionSide = $getPreOpPhyDetails->ivSelectionSide;
		$ivSelectionOther = $getPreOpPhyDetails->ivSelectionOther;
		
		$chbx_KVO = $getPreOpPhyDetails->chbx_KVO;
		$chbx_rate = $getPreOpPhyDetails->chbx_rate;
		$txtbox_rate = $getPreOpPhyDetails->txtbox_rate;
		$chbx_flu = $getPreOpPhyDetails->chbx_flu;
		$txtbox_flu = $getPreOpPhyDetails->txtbox_flu;
		$chbx_noted_by_nurse = $getPreOpPhyDetails->notedByNurse;
		$chbx_evaluated_patient = $getPreOpPhyDetails->evaluatedPatient;
		$honanBallon = $getPreOpPhyDetails->honanBallon;
		$honanBallonTime = $getPreOpPhyDetails->honanBallonTime;
		$preOpOrdersOther = $getPreOpPhyDetails->preOpOrdersOther;	
		$Heparin_userid=	$getPreOpPhyDetails->Heparin_user;
		$relivednurse=     $getPreOpPhyDetails->relivednurse;
		$Heparin_time=   $getPreOpPhyDetails->Heparin_time;
		$comments=	$getPreOpPhyDetails->comments;
		$medicationStartTimeVal  = $getPreOpPhyDetails->medicationStartTime;
		$prefilMedicationStatus=	$getPreOpPhyDetails->prefilMedicationStatus;
		$anesthesiologistId =$getPreOpPhyDetails->anesthesiologistId;
		
		$signSurgeon1Id =$getPreOpPhyDetails->signSurgeon1Id;
		$signSurgeon1FirstName =$getPreOpPhyDetails->signSurgeon1FirstName;
		$signSurgeon1MiddleName =$getPreOpPhyDetails->signSurgeon1MiddleName;
		$signSurgeon1LastName =$getPreOpPhyDetails->signSurgeon1LastName;
		$signSurgeon1Status =$getPreOpPhyDetails->signSurgeon1Status;
		
		$signNurseId =$getPreOpPhyDetails->signNurseId;
		$signNurseFirstName =$getPreOpPhyDetails->signNurseFirstName;
		$signNurseMiddleName =$getPreOpPhyDetails->signNurseMiddleName;
		$signNurseLastName =$getPreOpPhyDetails->signNurseLastName;
		$signNurseStatus =$getPreOpPhyDetails->signNurseStatus;
		$signNurseDateTime =$getPreOpPhyDetails->signNurseDateTime;
		
		$signNurse1Id =$getPreOpPhyDetails->signNurse1Id;
		$signNurse1FirstName =$getPreOpPhyDetails->signNurse1FirstName;
		$signNurse1MiddleName =$getPreOpPhyDetails->signNurse1MiddleName;
		$signNurse1LastName =$getPreOpPhyDetails->signNurse1LastName;
		$signNurse1Status =$getPreOpPhyDetails->signNurse1Status;
		$signNurse1DateTime =$getPreOpPhyDetails->signNurse1DateTime;
		
		
		$signSurgeonDateTime =$getPreOpPhyDetails->signSurgeon1DateTime;
		$form_status = $getPreOpPhyDetails->form_status;
		$saveFromChart = $getPreOpPhyDetails->saveFromChart;
		
		$version_num 			=	$getPreOpPhyDetails->version_num;
		$versionDateTime	=	$getPreOpPhyDetails->version_date_time;
		if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
		else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	$current_form_version; }
		
	}
	else{
	
		$version_num 			=	$current_form_version;
		$versionDateTime	=	'';
		
		//GETTING SURGEON PROFILE TO SHOW FIRST VIEW "$surgeonId"
			unset($conditionArr);
			
			$conditionArr['surgeonId'] = $surgeonId;
			$conditionArr['del_status'] = '';
			$profilesDetail = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
			if($profilesDetail){
				foreach($profilesDetail as $profile){
					$surgeonProfileId = $profile->surgeonProfileId;
					$proceduresList = $profile->procedures;
					$preOpOrder = $profile->preOpOrders;
					if(strpos($proceduresList, ", ")){
						$proceduresArray = explode(", ", $proceduresList);
						if(in_array(trim($patient_primary_procedure), $proceduresArray)){
							$procedureFound = 'true';
							break;
						}
					}else{
						if(trim($patient_primary_procedure)==trim($proceduresList)){
							$procedureFound = 'true';
							break;
						}
						$proceduresArray[] = $proceduresList;
					}
				}
			}	

			$profileIDToShow = $surgeonProfileId;
			// PROFILE TO DISPLAY
				unset($conditionArr);
				$conditionArr['surgeonProfileId'] = $profileIDToShow;
				$showProfileDetails = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
			// PROFILE TO DISPLAY
			
			// GETTING PRE-OP ORDERS MEDICATION NAMES
			if(count($showProfileDetails)>0){
				foreach($showProfileDetails as $profile){
					$proceduresList = $profile->procedures;
					$preOpOrders = $profile->preOpOrders;
					if(strpos($preOpOrders, ", ")){
						$preOpOrdersArr = explode(", ", $preOpOrders);
					}else{
						$preOpOrdersArr[] = $preOpOrders;
					}
				}
			}
			// GETTING PRE-OP ORDERS MEDICATION NAMES
			
		//GETTING SURGEON PROFILE TO SHOW FIEST VIEW "$surgeonId"
		
		/*****
		* Start Procedure Preference Card to show first view
		*****/
		if( count($showProfileDetails) == 0 )
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
						$procPrefCardRow	=	imw_fetch_object($procPrefCardSql);
						$preOpOrders 		= $procPrefCardRow->preOpOrders;
						if(strpos($preOpOrders, ", ")){
							$preOpOrdersArr = explode(",", $preOpOrders);
						}else{
							$preOpOrdersArr[] = $preOpOrders;
						}
						
						break; 
					}
				}
			}
		}
		
		
		/*****
		* End Procedure Preference Card to show first view
		*****/
		
	}
// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
	
//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
	$otherPreOpOrdersFound = "";
	$surgeonProfileIdFound = "";
	$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
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
	if($selectSurgeonProcedureNumRow>0) {
		while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
			$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
			 
			if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
				$surgeonProfileIdFound = $selectSurgeonProcedureRow['profileId'];
			}
		}
		/*if($surgeonProfileIdFound) {*/
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";
		/*}else {	//ELSE SELECT DEFAULT PROFILE OF SURGOEN
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
		}*/
			
			$selectSurgeonProfileFoundRes = imw_query($selectSurgeonProfileFoundQry) or die(imw_error());
			$selectSurgeonProfileFoundNumRow = imw_num_rows($selectSurgeonProfileFoundRes);
			if($selectSurgeonProfileFoundNumRow > 0) {
				$selectSurgeonProfileFoundRow = imw_fetch_array($selectSurgeonProfileFoundRes);
				$preOpOrdersFound = $selectSurgeonProfileFoundRow['preOpOrders'];
				$otherPreOpOrdersFound = $selectSurgeonProfileFoundRow['otherPreOpOrders'];
				$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
				//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW
					
					if($prefilMedicationStatus<>'true') {
						
						for($k=0;$k<=count($preOpOrdersFoundExplode);$k++) {
							if($preOpOrdersFoundExplode[$k]<>"") {
								
								$selectPreOpmedicationOrderQry = "select * from preopmedicationorder where preOpMedicationOrderId = '$preOpOrdersFoundExplode[$k]'";
								$selectPreOpmedicationOrderRes = imw_query($selectPreOpmedicationOrderQry) or die(imw_error());
								$selectPreOpmedicationOrderRow = imw_fetch_array($selectPreOpmedicationOrderRes);
								
								$selectMedicationName = addslashes($selectPreOpmedicationOrderRow['medicationName']);
								$selectStrength 			= addslashes($selectPreOpmedicationOrderRow['strength']);
								$selectDirections 		= addslashes($selectPreOpmedicationOrderRow['directions']);
								
								$chk_patientMedicationQry	=	"Select * From patientpreopmedication_tbl Where 
																							medicationName = '".$selectMedicationName."'
																							And strength = '".$selectStrength."'
																							And direction = '".$selectDirections."'
																							And patient_confirmation_id = '".$pConfId."'
																						";
								$chk_patientMedicationSql	=	imw_query($chk_patientMedicationQry);
								$chk_patientMedicationCnt	=	imw_num_rows($chk_patientMedicationSql);
														
								if(	$chk_patientMedicationCnt == 0 )
								{
										$insPatientPreOpMediQry 	= "Insert into patientpreopmedication_tbl set
																										preOpPhyOrderId = '".$preOpPhysicianOrdersId."',
																										patient_confirmation_id = '".$pConfId."',
																										medicationName = '".$selectMedicationName."',
																										strength = '".$selectStrength."',
																										direction = '".$selectDirections."'
																								";
										$insPatientPreOpMediRes 	= imw_query($insPatientPreOpMediQry) or die(imw_error());
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
						$procPrefCardRow	=	imw_fetch_object($procPrefCardSql);
						$preOpOrdersFound = $procPrefCardRow->preOpOrders;
						$otherPreOpOrdersFound = $procPrefCardRow->otherPreOpOrders;
						$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
		
						//PREFIL THE VALUES IN 'PATIENT PREOP MEDICATION' AT FIRST VIEW
			
							if($prefilMedicationStatus<>'true') 
							{
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
																									medicationName = '".$selectMedicationName."'
																									And strength = '".$selectStrength."'
																									And direction = '".$selectDirections."'
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

//SHOW DETAIL OF PATIENT PRE OP MEDICATION
	$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $_REQUEST["pConfId"],'patientPreOpMediId','ASC');
//SHOW DETAIL OF PATIENT PRE OP MEDICATION

	
	if($form_status!="not completed" && $form_status!="completed") {
		$preOpOrdersOther = $otherPreOpOrdersFound;
	}
?>
<script>
top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
function showMedTime(){
	var oldRead = document.getElementById('time_med').innerHTML;
	var nowTime = document.getElementById('textTime').value;
	return false;
	var newTime = oldRead+'&nbsp;&nbsp;'+nowTime;
	var isNewVal = document.getElementById('timeToSave').value;
	if(isNewVal == ''){
		document.getElementById('time_med').innerHTML = newTime;
		document.getElementById('textTime').style.display="block";
		document.getElementById('textTime').value = nowTime;
	}
}
//Applet
function get_App_Coords(objElem, id){
	var coords,appName;
	var objElemSign = document.getElementById('elem_signature'+id);
	appName = objElem.name;
	coords = getCoords(appName, id);	
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
function getCoords(appName, id){		
	var coords = document.applets["app_signature"+id].getSign();
	return coords;
}
function getclear_os(id){
	document.applets["app_signature"+id].clearIt();
	changeColorThis(255,0,0, id);
	document.applets["app_signature"+id].onmouseout();
}
function changeColorThis(r,g,b, id){				
	document.applets['app_signature'+id].setDrawColor(r,g,b);								
}
//Applet

function chkFn(obj){
	alert(obj.checked)
}
//
//save time function done by mamta
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
	
	function save_medication_time_value() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
	var medication_time=document.getElementById('textTime').value;
	var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
	var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
	
	var url="pre_op_physician_record_ajax.php";
	url=url+"?medicationTime="+medication_time+'&patient_id='+patient_id1+'&pConfId='+pConfId1;
	xmlHttp.onreadystatechange=AjaxTestingFunTimeMedicated
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function AjaxTestingFunTimeMedicated() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 		
				document.getElementById("timeMedicatedId").innerHTML=xmlHttp.responseText;
				document.getElementById('timeToSave').value = '';
				
			}
	}
	
//save time function End done by mamta
/***********************************************/
//save medication done by mamta
	
	function save_physician_time() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return true;
			}
		var timeFieldsObj = document.getElementsByName('startTimeVal[]');
		var timeFieldsVal = timeFieldsObj(0).value;
		
		var url="pre_op_medication_order_time_save.php";
		var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
		
		url=url+'?timeVal='+timeFieldsVal+'&patient_id='+patient_id1+'&pConfId='+pConfId1;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	
	function save_medication(frmVersion) {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return true;
			}
		var medication_name	= encodeURIComponent(document.getElementById('preOpMediOrder').value);
		var strength		= encodeURIComponent(document.getElementById('strength').value);
		var directions		= encodeURIComponent(document.getElementById('direction').value);	
		var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
		
		var url="pre_op_medication_order_ajex.php";
		url=url+'?medication_name='+medication_name+'&strength='+strength+'&directions='+directions+'&patient_id='+patient_id1+'&pConfId='+pConfId1+'&frmVersion='+frmVersion;
		xmlHttp.onreadystatechange=AjaxTestFunPhysicianOrder 
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	
	function AjaxTestFunPhysicianOrder() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("preOpPhysicianShowAjaxId").innerHTML=xmlHttp.responseText;
				document.getElementById('preOpMediOrder').value = '';
				document.getElementById('strength').value = '';
				document.getElementById('direction').value = '';
			
			}
	}
//end	
//delete medication done by mamta	
 	function delentry(id,DD,frmVersion){
		xmlHttp=GetXmlHttpObject();		
		if (xmlHttp==null){

			alert ("Browser does not support HTTP Request");
			return true;
		}		
		var patient_id1 	= '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 		= '<?php echo $_REQUEST["pConfId"];?>';
		
		var url='pre_op_med_del_ajax.php?delId='+id+'&patient_id='+patient_id1+'&pConfId='+pConfId1+'&frmVersion='+frmVersion;
		xmlHttp.onreadystatechange=AjaxTestDel
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function AjaxTestDel(){
		if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
			document.getElementById("preOpPhysicianShowAjaxId").innerHTML=xmlHttp.responseText;
		}
		
	}
		
		

//end delete medication done by mamta	

function showPreOpMediDiv(name1,name2,c,posLeft,posTop){
	document.getElementById("PreOpMedicationDiv").style.display = 'block';
	document.getElementById("PreOpMedicationDiv").style.left = posLeft+'px';
	document.getElementById("PreOpMedicationDiv").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
	
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS		
		top.frames[0].setPNotesHeight();
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
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
	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {
		
		
		//START TO CHECK IF OTHER THAN ASSIGNED SURGEON WANTS TO MAKE SIGNATURE THEN
			var signCheck='true';
			var assignedSurgeonId = '<?php echo $preOpAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $preOpAssignedSurgeonName;?>';
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
function saveTimeBlur(id,autoIncrId,fieldName) {
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return true;
		}
	var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
	var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
	
	var url="pre_op_medication_order_blur_ajex.php";
	url=url+'?timeId='+id;
	url=url+"&autoIncrId="+autoIncrId;
	url=url+"&fieldName="+fieldName;
	url=url+"&patient_id="+patient_id1;
	url=url+"&pConfId="+pConfId1;
	
	xmlHttp.onreadystatechange=AjaxTestFunPhysicianOrderTimeId
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function AjaxTestFunPhysicianOrderTimeId() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
		//ONLY SAVE TIME VALUES
	}
}

</script>
<script>
function displayNew(){
	var objDisplay = document.getElementById('newMedicationTr').style.display;
	if(objDisplay=='none'){
		document.getElementById('newMedicationTr').style.display = 'block';
	}else{
		document.getElementById('newMedicationTr').style.display = 'none';
	}
}
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

<?php
$table = 'preopmedicationorder';
$width = '300';

$preOpHeight='150';
if($sect=="print_emr") {
	if(count($preOpPatientDetails)>0) {
		$preOpHeightPrint=(count($preOpPatientDetails)*24);
		if($preOpHeightPrint>$preOpHeight) {
			$preOpHeight = $preOpHeightPrint;	
		}
	}
}
?>
<form action="pre_op_physician_orders.php?submitMe=true" name="frm_pre_op_phy_order" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
    <!--<input type="hidden" name="strengthArr2[]" value="<?php //echo $strength; ?>">-->
    <input type="hidden" name="divId" id="divId">
    <input type="hidden" name="counter" id="counter">
    <input type="hidden" name="timeToSave" id="timeToSave">
    <input type="hidden" name="secondaryValues" id="secondaryValues">
    <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value=""> 
    <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
    <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
    <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
    <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
    <input type="hidden" name="saveFromChart" id="saveFromChart" value="0">
    <input type="hidden" name="ascId" id="ascId" value="<?php echo $ascId; ?>">
    <input type="hidden" name="preOpPhysicianOrdersId" id="preOpPhysicianOrdersId" value="<?php echo $preOpPhysicianOrdersId; ?>">		
    <input type="hidden" name="medicationTime" id="medicationTime" value="">
    <input type="hidden" name="getText" id="getText">
    <input type="hidden" name="Heparin_start_user" id="Heparin_start_user" value="<?php echo $Heparin_start_user;?>">
    <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
    <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
    <input type="hidden" name="frmAction" id="frmAction" value="pre_op_physician_orders.php">
    <input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">	
    <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
    <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
	    <input type="hidden" id="vitalSignGridHolder" />

 <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
		<?php 
            $bgCol = $bgdark_orange_physician;
            $borderCol = $bgdark_orange_physician;
            include('saveDivPopUp.php'); 
        ?>
    </div>
	<div class=" scheduler_table_Complete">
        	
            <?php
				$epost_table_name = "preopphysicianorders";
				include("./epost_list.php");
			?>
            
				<?PHP if($version_num > 1) { ?> 
        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
             <div class="panel panel-default bg_panel_or">
               <div class="panel-heading">
                  <h3 class="panel-title rob"> Pre Op Orders  </h3>
               </div>
               <div class="panel-body " id="p_check_in">
                    <div class="row">
                        <p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-12"> On arrival the following drops will be given to the <?php echo $preopphy_patientConfirmSiteTemp;?></p>
                        
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
                            <div class="clearfix border-dashed margin_adjustment_only"></div>
                        </div>
                          
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                          <div class="scanner_win new_s">
                            <h4><span>List of Pre-OP Medication Orders</span></h4>
                          </div>
                        </div> 
                        
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            <div class="full_width">
                              <div id="" class="inner_safety_wrap padding_adjust_post">
                                <div class="row">
                                  <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                    <div class="row">
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                        <label>Medication</label>
                                      </div>
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                        <label>Strength</label>
                                      </div>
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                        <label>Direction</label>
                                      </div>
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                        
                                      </div>
                                    </div>
                                  </div>   
                                </div>
                              </div>
                            </div>
            
                            <div class=" max-height-adjust-post full_width">
                              <div class="inner_safety_wrap padding_adjust_post" id="preOpPhysicianShowAjaxId" >
                              <?php 
                                if(!$getPreOpPhyDetails){
                                  unset($conditionArr);											
                                  if(count($preOpOrdersArr)>0)
                                  {
                                    foreach($preOpOrdersArr as $preDefined)
                                    {			
                                      $preOpMediDetails = $objManageData->getRowRecord('preopmedicationorder', 'medicationName', $preDefined);
                                      $strength = $preOpMediDetails->strength;
                                      $directions = $preOpMediDetails->directions;
                                      ++$seq;
                              ?>
                                      <div class="row">
                                        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                            <div class="row medicine_row" id="row<?php echo $seq; ?>">
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Medication" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Direction" name="strengthArr[]" value="<?php echo stripslashes($strength); ?>" />                                                                            </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Strength" name="directionArr[]" value="<?php echo stripslashes($directions); ?>"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                  <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-center">
                                                    <a style="margin:0" class="btn btn-danger" href="javascript:void(0)" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','<?=$version_num?>');">X</a>
                                                  </div>	
                                                </div>
                                                
                                            </div>
                                        </div>
                                      </div>
                                
                              <?php
                                    }
                                  }
                                }
                                else if($getPreOpPhyDetails)
                                {
                                  if(count($preOpPatientDetails)>0)
                                  {
                            ?>
                            <input type="hidden" id="bp" name="bp_hidden">
                            <?php if(!$medicationStartTimeVal) $medicationStartTimeVal = "";//date('h:i A'); ?>
                            <input type="hidden" name="hourVal" id="hourVal" value="<?php print substr($medicationStartTimeVal,0,2); ?>" >
                            <input type="hidden" name="minuteVal" id="minuteVal" value="<?php print substr($medicationStartTimeVal,3,-2); ?>" >
                            <input type="hidden" name="statusVal" id="statusVal" value="<?php print substr($medicationStartTimeVal,5); ?>" >
                            <?php
                                    foreach($preOpPatientDetails as $detailsOfMedication)
                                    {
                                      $parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
                                      if(trim($detailsOfMedication->medicationName))
                                      {
                                          $preDefined = $detailsOfMedication->medicationName;
                                          $strength = $detailsOfMedication->strength;
                                          $directions = $detailsOfMedication->direction;
                                          $timemeds = $detailsOfMedication->timemeds;
                                          $timemeds1=array();
                                          $timemeds1[] = $detailsOfMedication->timemeds1;
                                          $timemeds1[] = $detailsOfMedication->timemeds2;
                                          $timemeds1[] = $detailsOfMedication->timemeds3;
                                          $timemeds1[] = $detailsOfMedication->timemeds4;
                                          $timemeds1[] = $detailsOfMedication->timemeds5;
                                          $timemeds1[] = $detailsOfMedication->timemeds6;
                                          
                                          $timemeds1[] = $detailsOfMedication->timemeds7;
                                          $timemeds1[] = $detailsOfMedication->timemeds8;
                                          $timemeds1[] = $detailsOfMedication->timemeds9;
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
                                          <div class="row medicine_row">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                              <div class="row" id="row<?php echo $seq; ?>">
                                              
                                                  <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input type="hidden" name="parientPreOpMediOrderId[]" id="IDS<?php echo $parientPreOpMediOrderId; ?>" value="<?php echo $parientPreOpMediOrderId; ?>">
                                                    <input class="form-control" type="text" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"/>
                                                  </div>
                                                  
                                                  <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text"  name="strengthArr[]" value="<?php echo stripslashes($strength); ?>"/>
                                                  </div>
                                                  
                                                  <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" name="directionArr[]" value="<?php echo stripslashes($directions); ?>">
                                                    <input type="hidden" name="feq[]" value="<?php print $freq; ?>" >
                                                    <input type="hidden" name="min[]" value="<?php print $min; ?>" >
                                                  </div>
                                                  
                                                  <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <div class="row">
                                                      <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-left">
                                                        <a style="margin:0" class="btn btn-danger" href="javascript:void(0)" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','<?=$version_num?>');">X</a>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  
                                              </div>
                                            </div>
                                          </div>
                                          
                            <?php
                                      }
                                    }
                            ?>
                
                            <?php 
                                  }
                                }
                              ?>
                              </div>
                            </div>
                            
                            <div class="inner_safety_wrap collapse padding_adjust_post" id="Med_collapsible" >
                              <div class="row">
                                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                    <h4> Add Medication Below:</h4>
                                    <div class="row">
                                      
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                        <input class="form-control" type="text" placeholder="Medication" name="preOpMediOrder" id="preOpMediOrder" onClick="return showPreOpMediDiv('preOpMediOrderAreaId', '','no',parseInt(findPos_X('preOpMediOrder')), parseInt(findPos_Y('preOpMediOrder'))-188);" />
                                      </div>
                                      
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                          <input class="form-control" type="text" placeholder="Strength"  name="strength" id="strength"/>
                                      </div>
                                      
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                          <input class="form-control" type="text" placeholder="Direction" name="direction" id="direction"/>
                                      </div>
                                            
                                      <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                        <div class="row">
                                          <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
                                            <a style="margin:0" id="saveBtn" class="btn btn-info" href="javascript:void(0)" onClick="return save_medication(2)">  Save </a>
                                          </div>
                                        </div>
                                      </div>
                                    
                                    </div>
                                </div>
                              </div>
                            </div>
            
                            <div class="row">
                              <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                <a style="margin:0;margin-top: 10px;" class="btn btn-default" data-target="#Med_collapsible" data-toggle="collapse" href="javascript:void(0)">
                                  <span class="fa fa-plus-circle"></span> Add New Medication 
                                </a>																	
                              </div>
                            </div>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix padding_0">
                              <div class="clearfix border-dashed margin_adjustment_only"></div>
                            </div>  
                            
                        </div>
                    <!--   @2nd col Ends     -->
                   </div>
              </div>          
             </div> 
        </div>
       	<?php } ?>
        
             
       	<?PHP if($version_num < 2) { ?>
        		
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                 <div class="panel panel-default bg_panel_or">
                   	<div class="panel-heading">
                    	<h3 class="panel-title rob"> Pre Op Orders  </h3>
                   	</div>
                   	<div class="panel-body " id="p_check_in">
                        <div class="row">
                           	<p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-12"> On arrival the following drops will be given to the <?php echo $preopphy_patientConfirmSiteTemp;?></p>
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
                            		<div class="clearfix border-dashed margin_adjustment_only"></div>
                            </div>  
                            
                          	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                <div class="scanner_win new_s">
                                 <h4>
                                    <span>List of Pre-OP Medication Orders</span>      
                                 </h4>
                                </div>
                            </div> 
                            
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                <div class="full_width">
                                  <div id="" class="inner_safety_wrap padding_adjust_post">
                                    <div class="row">
                                      <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
                                        <div class="row">
                                          <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                            <label>Medication</label>
                                          </div>
                                          <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                            <label>Strength</label>
                                          </div>
                                          <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                            <label>Direction</label>
                                          </div>
                                          <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                            
                                          </div>
                                        </div>
                                      </div>   
                                    </div>
                                  </div>
                                </div>
								
                               	<div class=" max-height-adjust-post full_width">
                                  <div class="inner_safety_wrap padding_adjust_post" id="preOpPhysicianShowAjaxId" >
                                 <?php
																 $k = 0;
								  if(!$getPreOpPhyDetails){
										unset($conditionArr);											
										if(count($preOpOrdersArr)>0){
											foreach($preOpOrdersArr as $preDefined){			
												$preOpMediDetails = $objManageData->getRowRecord('preopmedicationorder', 'medicationName', $preDefined);
												$strength = $preOpMediDetails->strength;
												$directions = $preOpMediDetails->directions;
												++$seq;
									?>
                                    <div class="row">
                                        <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
                                			
                                            <div class="row medicine_row" id="row<?php echo $seq; ?>">
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Medication" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Direction" name="strengthArr[]" value="<?php echo stripslashes($strength); ?>" />
                                               	</div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Strength" name="directionArr[]" value="<?php echo stripslashes($directions); ?>"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                     <div class="row">
                                                        <div class="col-md-8 col-lg-8 col-xs-8 col-sm-8">
                                                            <input class="form-control" type="text" placeholder="" name="timemedsArr[]" value="<?php echo $timemeds; ?>"/>
														</div>	
                                                        <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-center">
														   <a style="margin:0" class="btn btn-danger" href="javascript:void(0)" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','<?=$version_num?>');">X</a>
														</div>	
                                                     </div>
                                                </div>
                                            </div>
                                        </div>	   
                                    </div>
                                     <?php
												}
											}
									   }else if($getPreOpPhyDetails){
											if(count($preOpPatientDetails)>0){
									?>
                    <input type="hidden" id="bp" name="bp_hidden">
										<?php if(!$medicationStartTimeVal) $medicationStartTimeVal = "";//date('h:i A'); ?>
                    <input type="hidden" name="hourVal" id="hourVal" value="<?php print substr($medicationStartTimeVal,0,2); ?>" >
                    <input type="hidden" name="minuteVal" id="minuteVal" value="<?php print substr($medicationStartTimeVal,3,-2); ?>" >
                    <input type="hidden" name="statusVal" id="statusVal" value="<?php print substr($medicationStartTimeVal,5); ?>" >
											<?php
												foreach($preOpPatientDetails as $detailsOfMedication)
												{
														$parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
														if(trim($detailsOfMedication->medicationName))
														{
															$preDefined = $detailsOfMedication->medicationName;
															$strength = $detailsOfMedication->strength;
															$directions = $detailsOfMedication->direction;
															$timemeds = $detailsOfMedication->timemeds;
															$timemeds1=array();
															$timemeds1[] = $detailsOfMedication->timemeds1;
															$timemeds1[] = $detailsOfMedication->timemeds2;
															$timemeds1[] = $detailsOfMedication->timemeds3;
															$timemeds1[] = $detailsOfMedication->timemeds4;
															$timemeds1[] = $detailsOfMedication->timemeds5;
															$timemeds1[] = $detailsOfMedication->timemeds6;
															
															$timemeds1[] = $detailsOfMedication->timemeds7;
															$timemeds1[] = $detailsOfMedication->timemeds8;
															$timemeds1[] = $detailsOfMedication->timemeds9;
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
                              <div class="row medicine_row">
                                <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
                                	<div class="row" id="row<?php echo $seq; ?>">
                                  	
                                    <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                    	<input type="hidden" name="parientPreOpMediOrderId[]" id="IDS<?php echo $parientPreOpMediOrderId; ?>" value="<?php echo $parientPreOpMediOrderId; ?>">
                                      <input class="form-control" type="text" name="preOpMediOrderArr[]" value="<?php echo stripslashes($preDefined); ?>"/>
                                  	</div>
                                    
                                   	<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                    	<input class="form-control" type="text"  name="strengthArr[]" value="<?php echo stripslashes($strength); ?>"/>
                                   	</div>
                                    
                                    <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                    	<input class="form-control" type="text" name="directionArr[]" value="<?php echo stripslashes($directions); ?>">
                                      <input type="hidden" name="feq[]" value="<?php print $freq; ?>" >
                                      <input type="hidden" name="min[]" value="<?php print $min; ?>" >
                                  	</div>
                                    
                                    <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                    	<div class="row">
                                      	
                                        <div class="col-md-8 col-lg-8 col-xs-8 col-sm-8">
                                        	<input class="form-control" type="text" placeholder="" id="starttime<?php echo $k;?>[]" name="timemedsArr[]" value="<?php echo $timemeds;//echo $medicationStartTimeVal; ?>" onClick="if(!this.value) { return displayTimeAmPm('starttime<?php echo $k;?>[]');}" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds";?>');">
                                      	</div>
                                    		<div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 text-center">
                                        	<a style="margin:0" class="btn btn-danger" href="javascript:void(0)" onClick="return delentry('<?php echo $parientPreOpMediOrderId; ?>', '<?php echo $k; ?>','<?=$version_num?>');">X</a>
                                       	</div>	
                                        
                                    	</div>
                                   	</div>
                                    
                                	</div>
                               	</div>
                                
                                <div class="clearfix visible-sm margin_adjustment_only"></div>
                                
                                <div class="col-md-5 col-lg-5 col-sm-12 col-xs-12">
                                	<div class="row">
                              <?php 
																		if($freq>1 )
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
                                      	<div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                        	<input class="form-control" type="text"name="starttimeExtra<?php echo $k;?>[]" id="starttimeExtraId<?php echo $k.$tdNew?>" onClick="if(!this.value){return displayTimeAmPm('starttimeExtraId<?php echo $k.$tdNew?>');}" value="<?php  echo($timemeds1[$tdNew]);//print_r($$timemedsram[0]);?>" onDblClick="this.select();" onBlur="saveTimeBlur(this.value,'<?php echo $parientPreOpMediOrderId;?>','<?php echo "timemeds".$td;?>');" />
                                       	</div>
                             	<?php 
																			}
																		}
															?>
                              		</div>
                               	</div>
                                
                              </div>
											<?php
														
														}
												}
											?>
											
											<?php 
												}
									   	}
											?>
                                  </div>
                              	</div>
								 
																<div class="inner_safety_wrap collapse padding_adjust_post" id="Med_collapsible" >
                                    <div class="row">
                                        <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12">
                                            <h4> Add Medication Below:</h4>
                                            <div class="row">
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Medication" name="preOpMediOrder" id="preOpMediOrder" onClick="return showPreOpMediDiv('preOpMediOrderAreaId', '','no',parseInt(findPos_X('preOpMediOrder')), parseInt(findPos_Y('preOpMediOrder'))-188);" />
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Strength"  name="strength" id="strength"/>                                                                            </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                    <input class="form-control" type="text" placeholder="Direction" name="direction" id="direction"/>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3">
                                                     <div class="row">
                                                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 text-left">
                                                            <a style="margin:0" id="saveBtn" class="btn btn-info" href="javascript:void(0)" onClick="return save_medication('1')">  Save </a>
                                                            </div>	
                                                     </div>
                                                </div>
                                            </div>
                                        </div>	   
                                   </div>      
                                </div>
								
                                <div class="row">
                                  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                    <a style="margin:0;margin-top: 10px;" class="btn btn-default" data-target="#Med_collapsible" data-toggle="collapse" href="javascript:void(0)">
                                      <span class="fa fa-plus-circle"></span> Add New Medication 
                                    </a>																	
                                  </div>
                                </div>
                                
                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix padding_0">
                                  <div class="clearfix border-dashed margin_adjustment_only"></div>
                                </div> 
                                 
                              	<div class="inner_safety_wrap">
                                  <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 col-lg-6">
                                        <div class="row">
                                            <label for="text_comment" class="col-md-4 col-sm-12 col-xs-12 col-lg-2 text-right"> Comments </label>
                                            <div class="clearfix visible-sm margin_adjustment_only"></div>
                                            <div class="col-md-8 col-sm-12 col-xs-12 col-lg-10">
                                                <textarea class="form-control" style="resize:none;" name="comments" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($comments); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix margin_adjustment_only"></div>
                                   	<!-- <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
                                        <div class="clearfix border-dashed margin_adjustment_only"></div>
                                    </div>-->
                                 </div>
                               	</div>
                            </div>
                        <!--   @2nd col Ends     -->
                       	</div>
                  	</div>          
                 </div> 
            </div>
          	   
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                 <div class="panel panel-default bg_panel_or">
                   <div class="panel-body " id="p_check_in">
                       <div class="inner_safety_wrap heparin_div">
                            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                <div class="row">
                                    <div class="col-md-3 col-lg-2 col-xs-12 col-sm-12">
                                        <label for="chbx_heparin_lockStart" class=""> <input type="checkbox" name="chbx_heparin_lockStart" id="chbx_heparin_lockStart" <?php if($chbx_heparin_lockStart=='heparin')echo "checked"; ?> value="heparin" onClick="disp_one_hide_other_onchangeNew('postop_physician_id','lft_rgt_id','other_id','chbx_heparin_lockStart','chbx_iv','iv_sub_id');"/>  Start Heparin Lock </label> 
                                      
                                        <label for="chbx_iv"> <input type="checkbox" name="chbx_heparin_lock" id="chbx_iv" <?php if($chbx_heparin_lock=='iv')echo "checked"; ?> value="iv" onClick="disp_one_hide_other_onchangeNew('postop_physician_id','lft_rgt_id','other_id','chbx_heparin_lockStart','chbx_iv','iv_sub_id');" /> IV  </label>
                                    </div>
                                    <div class="col-md-9 col-sm-12 col-xs-12 col-lg-10">
                                     <div class="row">
                                        <div class="col-md-3 col-lg-2 col-xs-4 col-sm-3">
                                            <select class="selectpicker" name="postop_physician" id="postop_physician_id" onChange="javascript:disp_one_hide_other_onchangeNew('postop_physician_id','lft_rgt_id','other_id','chbx_heparin_lockStart','chbx_iv','iv_sub_id');">
                                             <option value="">No IV</option>
                                            <option value="hand" <?php if($ivSelection=='hand') echo "SELECTED"; ?>>Hand</option>
                                            <option value="wrist" <?php if($ivSelection=='wrist') echo "SELECTED"; ?>>Wrist</option>
                                            <option value="arm" <?php if($ivSelection=='arm') echo "SELECTED"; ?>>Arm</option>
                                            <option value="antecubital" <?php if($ivSelection=='antecubital') echo "SELECTED"; ?>>Antecubital</option>
                                            <option value="other" <?php if($ivSelection=='other') echo "SELECTED"; ?>>Other</option>                                                                            
                                            </select>	
                                        </div>
                                        <div class="col-md-9 col-lg-10 col-xs-8 col-sm-9">
                                        
                           <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="lft_rgt_id" style="display:<?php if($ivSelection=='' || $ivSelection=='other' || ($chbx_heparin_lockStart!='heparin' && $chbx_heparin_lock!='iv')) echo "none"; ?>;">
               					<div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                	<input type="checkbox" name="chbx_ec" <?php if($ivSelectionSide=='right') echo "CHECKED"; ?> value="right" id="chbx_ec_right" onClick="javascript:checkSingle('chbx_ec_right','chbx_ec')"/><label>Right</label>
                            	</div>
			                    <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                      <input type="checkbox" name="chbx_ec"  <?php if($ivSelectionSide=='left') echo "CHECKED"; ?> value="left" id="chbx_ec_left" onClick="javascript:checkSingle('chbx_ec_left','chbx_ec')" />
           						       <label> Left</label>
			                    </div>
                    <?php if($chbx_heparin_lock=='iv' && $ivSelection!='' && $ivSelection!='other') { $iv_sub_id_display =  'block'; }else { $iv_sub_id_display = 'none'; } ?>
                    
                    	<div id="iv_sub_id" class="col-md-8 col-lg-8 col-xs-8 col-sm-8" style="display:<?php echo $iv_sub_id_display; ?> ">
                        
                              <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2"><input type="checkbox" name="chbx_KVO"  value="Yes" <?php if($chbx_KVO=='Yes') { echo "checked";  }?>>
                              	<label>KVO</label></div>	
                               <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                               <input type="checkbox" name="chbx_rate" value="Yes" <?php if($chbx_rate=='Yes') { echo "checked";  }?>>				
                               <label >Rate</label>
                               </div>	
                               <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                <div class="form-group">
                               
                               <input type="text" class="form-control" name="txtbox_rate" id="txtbox_rate" style="width:50%; display:inline-block;" value="<?php echo $txtbox_rate;?>"><label style="float:right;" for="txtbox_rate"> /hr</label>
                               </div>
                               </div>
                                <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                 <input type="checkbox" name="chbx_flu"  value="Yes" <?php if($chbx_flu=='Yes') { echo "checked";  }?>>		
                                 <label>Flu&nbsp;</label>
                                 </div>
                                <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
                                <input type="text" class="form-control col-md-3 col-lg-2 col-xs-3 col-sm-3" name="txtbox_flu" value="<?php echo $txtbox_flu;?>" ></div>
                                
                    </div>
                
            </div>
                    <div id="other_id" style="display:<?php if($ivSelection=='other') {echo "block";}else {echo "none";} ?> ; " class="col-md-12 col-lg-12 col-xs-12 col-sm-12"> 
                                                    <textarea id="Field3" name="otherHeparinLock" onKeyUp="textAreaAdjust(this);" class="form-control col-md-12 col-lg-12 col-xs-12 col-sm-12" ><?php echo stripslashes($ivSelectionOther) ; ?></textarea>
                                          
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
            <!--- - -- --   Sign -Out Ends Here -->
         		<?php } ?>
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
            <?php
				$defaultPreOpOrdersOther	=	'';
				if( $preOpOrdersOther == '' && $form_status <> 'completed' && $form_status <> 'not completed' )
				{
					$defaultPreOpOrdersOther	= $objManageData->getDefault('medications','name');
					$preOpOrdersOther			= $defaultPreOpOrdersOther;
				}
			   
			   
			?>
      
      <?php 
				if($version_num < 2)
				{
					$onclickOtherDiv	=	"return showPreMedsFn('pre_op_phy_area_id', '', 'no', parseInt(findPos_X('preop_order_button')), parseInt(findPos_Y('preop_order_button'))-186),document.getElementById('selected_frame_name_id').value='';";
				}
				else
				{
					$onclickOtherDiv	=	"return showPreMedsFn('pre_op_phy_area_id', '', 'no', parseInt(findPos_X('preop_order_button')), parseInt(findPos_Y('preop_order_button'))+30),document.getElementById('selected_frame_name_id').value='';";
				}
			?>
                 <div class="panel panel-default bg_panel_or">
                      <div class="panel-body">
                           <div class="col-md-4 col-lg-2 col-xs-12 col-sm-4" >
                                <label id="preop_order_button" data-placement="top" class="rob alle_link btn btn-default " onClick="<?=$onclickOtherDiv?>"> <span class="fa fa-caret-right">
                                    </span>	Other Pre-Op Orders
                                </label>
                           </div>		
                           <div class="col-md-8 col-lg-10 col-xs-12 col-sm-8">
                             <textarea class="form-control"  id="pre_op_phy_area_id" name="otherPreOpOrders" onKeyUp="textAreaAdjust(this);" style="resize:none;"><?php echo stripslashes($preOpOrdersOther); ?></textarea>
                           </div>		         
                           <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix  margin_adjustment_only">
                                <div class="clearfix border-dashed margin_adjustment_only"></div>
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
																		$callJavaFunSurgeon = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Surgeon1');";
																}					
																$surgeon1SignOnFileStatus = "Yes";
																$TDsurgeon1NameIdDisplay = "block";
																$TDsurgeon1SignatureIdDisplay = "none";
																$Surgeon1Name = $loggedInUserName=$surgeon1SignDateTime;
																
																if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
																		$Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
																		$surgeon1SignOnFileStatus = $signSurgeon1Status;	
																		$surgeon1SignDateTime = $objManageData->getFullDtTmFormat($signSurgeonDateTime);
																		$TDsurgeon1NameIdDisplay = "none";
																		$TDsurgeon1SignatureIdDisplay = "block";
																}
																if($_SESSION["loginUserId"]==$signSurgeon1Id) {
																		$callJavaFunSurgeonDel = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
																}else {
																		$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
																}
																//END CODE RELATED TO SURGEON SIGNATURE ON FILE
														
														
														
														//CODE RELATED TO NURSE SIGNATURE ON FILE
																if($loggedInUserType<>"Nurse") {
																		$loginUserName = $_SESSION['loginUserName'];
																		$callJavaFun = "return noAuthorityFunCommon('Nurse');";
																}else {
																		$loginUserId = $_SESSION["loginUserId"];
																		$callJavaFun = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse1');";
																}
							
																if($loggedInUserType<>"Nurse") {
																		$loginUserName = $_SESSION['loginUserName'];
																		$callJavaFun1 = "return noAuthorityFunCommon('Nurse');";
																}else {
																		$loginUserId = $_SESSION["loginUserId"];
																		$callJavaFun1 = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseSignatureId1'; return displaySignature('TDnurseNameId1','TDnurseSignatureId1','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse2');";
																}
														
																$signOnFileStatus = "Yes";
																$TDnurseNameIdDisplay = "block";
																$TDnurseSignatureIdDisplay = "none";
																$NurseNameShow = $loggedInUserName;
																$signNurse1DateTimeFormatNew="";
																if($signNurseId<>0 && $signNurseId<>"") {
																		$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
																		$signOnFileStatus = $signNurseStatus;
																		$signNurse1DateTimeFormatNew =$objManageData->getFullDtTmFormat($signNurseDateTime);
																		
																		$TDnurseNameIdDisplay = "none";
																		$TDnurseSignatureIdDisplay = "block";
																}
							
																$signOnFileStatus1 = "Yes";
																$TDnurseNameIdDisplay1 = "block";
																$TDnurseSignatureIdDisplay1 = "none";
																$NurseNameShow1 = $loggedInUserName;
																$signNurse1DateTimeFormatNew1="";
																if($signNurse1Id<>0 && $signNurse1Id<>"") {
																		$NurseNameShow1 = $signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName;
																		$signOnFileStatus1 = $signNurse1Status;
																		$signNurse1DateTimeFormatNew1 =$objManageData->getFullDtTmFormat($signNurse1DateTime);
																		
																		$TDnurseNameIdDisplay1 = "none";
																		$TDnurseSignatureIdDisplay1 = "block";
																}
							
																if($_SESSION["loginUserId"]==$signNurseId) {
																		$callJavaFunDel = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_physician_orders_ajaxSign.php','$loginUserId','Nurse1','delSign');";
																}else {
																		$callJavaFunDel = "alert('Only ".addslashes($NurseNameShow)." can remove this signature');";
																}
							
																if($_SESSION["loginUserId"]==$signNurse1Id) {
																		$callJavaFunDel1 = "document.frm_pre_op_phy_order.hiddSignatureId.value='TDnurseNameId1'; return displaySignature('TDnurseNameId1', 'TDnurseSignatureId1', 'pre_op_physician_orders_ajaxSign.php', '$loginUserId','Nurse2','delSign');";
																}else {
																		$callJavaFunDel1 = "alert('Only ".addslashes($NurseNameShow)." can remove this signature');";
																}
														//END CODE RELATED TO NURSE SIGNATURE ON FILE
                                
														//START SET BACKGROUND COLOR 
														$prePhysSurgeonSignBackColor	=	($signSurgeon1Id!=0)	?	$whiteBckGroundColor	:	$chngBckGroundColor;
														$prePhysnurseSignBackColor		=	($signNurseId!=0)			?	$whiteBckGroundColor	:	$chngBckGroundColor;
														$prePhysnurseSignBackColor1		=	($signNurseId1!=0)		?	$whiteBckGroundColor	:	$chngBckGroundColor;
														//START SET BACKGROUND COLOR 
                        
                            ?>
                            
                            <div class="row">
                           <div class="panel-body">
                           <div class="inner_safety_wrap" style="font-size:18px">
                           <?php
                           $anesPlumDisBackColor='';
                                if($chbx_noted_by_nurse==1) { 
                                    $anesPlumDisBackColor=$whiteBckGroundColor; 
                                }
						   ?>
                           <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12"><label><span class="colorChkBx" style=" <?php echo $anesPlumDisBackColor;?>" onClick="changeDiffChbxColor(1,'chbx_noted_by_nurse');"><input type="checkbox" name="chbx_noted_by_nurse" id="chbx_noted_by_nurse" value="1" <?php if($chbx_noted_by_nurse==1)echo' checked';?>></span>Pre-Op orders noted by nurse</label></div>
                           
                           <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                           
                                    <div class="inner_safety_wrap" id="TDnurseNameId1" style="display:<?php echo $TDnurseNameIdDisplay1;?>;">
                                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $prePhysnurseSignBackColor1?>;" onClick="javascript:<?php echo $callJavaFun1;?>"> Nurse Signature</a>
                                    </div>
                                    <div class="inner_safety_wrap collapse" id="TDnurseSignatureId1" style="display:<?php echo $TDnurseSignatureIdDisplay1;?>;">
                                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel1;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow1; ?>  </a></span>	     
                                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus1;?></span>
                                        <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $signNurse1DateTimeFormatNew1; ?> <span class="fa fa-edit"></span></span></span>
                                    </div>
                                </div>
                           </div>
                           </div>
                           </div>
                           <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix  margin_adjustment_only">
                                <div class="clearfix border-dashed margin_adjustment_only"></div>
                            </div>
                            <?php
							if( $version_num > 2 ) {
								$evaluatedLabel = "I have evaluated the patient and determined they meet requirements for admission to the ASC for the proposed procedure and anesthesia.";
								if( $version_num > 3 ) {
									$evaluatedLabel = "I have evaluated the patient's medical records including related Diagnosis and Diagnostic tests prior to admission for surgery. The chosen order on this form reflect and are included as per the appropriate and best care on day of Surgery.";	
								}
							?>
                            <div class="row">
                            	<div class="panel-body">
                            		<div class="inner_safety_wrap" style="font-size:18px">
                                        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label><span class="colorChkBx" style=" <?php if($chbx_evaluated_patient==1) { echo $whiteBckGroundColor;}?> " onClick="changeDiffChbxColor(1,'chbx_evaluated_patient');"><input type="checkbox" name="chbx_evaluated_patient" id="chbx_evaluated_patient" value="1" <?php if($chbx_evaluated_patient==1)echo' checked';?>></span><?php echo $evaluatedLabel;?></label>
                                        </div>
                            		</div>
                            	</div>
                            </div>
                            <?php
							}
							?>
                            <div class="row">
                                
                                <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                	<div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $prePhysSurgeonSignBackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
                                    </div>
                                    <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                                        <span class="rob full_width"><a  class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ". $Surgeon1Name; ?>  </a></span>	     
                                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                                        <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $surgeon1SignDateTime; ?> <span class="fa fa-edit fa-editsurg"></span></span></span>
                                    </div>
                                   
                           			</div>
                                
                                <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                                <!-- Nurse Signature Appears If form Version is < 2 -->
                                			<?php if($version_num < 2): ?>
                                      <div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>;">
                                          <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $prePhysnurseSignBackColor?>;"onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature</a>
                                      </div>
                                      <div class="inner_safety_wrap collapse" id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>;">
                                          <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow; ?>  </a></span>	     
                                          <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                                          <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $signNurse1DateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
                                      </div>
                                      <?php endif; ?>
                                </div>  
                                
                                <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12 pull-right">
                                    <?php if($version_num < 2): ?>
                                    <div class="inner_safety_wrap">
                                        <label for="relived_nurse" class="col-md-3 col-lg-4 col-xs-12 col-sm-12 text-right"> Relief Nurse</label>
                                        <div class="col-md-9 col-lg-8 col-xs-12 col-sm-12">
                                            <select id="relived_nurse" name="relived_nurse" class="selectpicker form-control bs-select-hidden"> 
                                            <option value="">Select</option>	
                                                <?php
												
                                                $Qry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                $Res = imw_query($Qry) or die(imw_error());
                                                while($Row=imw_fetch_array($Res)) {
                                                    $nurseID = $Row["usersId"];
                                                    $nurseName = $Row["lname"].", ".$Row["fname"]." ".$Row["mname"];
                                                    $sel="";
                                                    if($nurseID==$relivednurse) {
                                                        $sel = "selected";
                                                    } 
                                                    else {
                                                        $sel = "";
                                                    }
                                                    if($Row["deleteStatus"]<>'Yes' || $nurseID==$relivednurse) {												
                                                ?>	
                                                        <option value="<?php echo $nurseID;?>" <?php echo $sel;?>><?php echo $nurseName;?></option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                    </select>
                                         </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                           </div>
                           
                               
                           
                      </div>
                 </div>
            </div>		                                                
            
              
  </div>

    
</form>
<!-- WHEN CLICK ON CANCEL BUTTON -->
<form name="frm_return_BlankMainForm" method="post" action="pre_op_physician_orders.php?cancelRecord=true">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">
</form>
<!-- END WHEN CLICK ON CANCEL BUTTON -->

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_op_physician_orders.php";
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
include("pre_op_meds_div.php");
include("print_page.php");
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>

</body>
</html>