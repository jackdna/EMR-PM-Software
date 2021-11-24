<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("common/commonFunctions.php"); 
$tablename = "postopphysicianorders";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >
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

//$pConfId = $_SESSION['pConfId'];
$cancelRecord = $_REQUEST['cancelRecord'];
$submitMe = $_REQUEST['submitMe'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$relivednurse=$_REQUEST['relived_nurse'];
//UPDATING PATIENT STATUS IN STUB TABLE
extract($_GET);

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
 //echo "update stub_tbl set patient_status='Discharged' where stub_id='$stub_id'"; 
 //$update_status=imw_query("update stub_tbl set patient_status='Discharged' where stub_id='$stub_id'");
}
//END UPDATING PATIENT STATUS IN STUB TABLE

//GETTING CONFIRNATION DETAILS
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
		$postOpAssignedSurgeonId = $surgeonId;
		$postOpAssignedSurgeonName = stripslashes($surgeon_name);
		$postOpAssignedPriProcId = $patient_primary_procedure_id;
	}
	
//GETTING CONFIRNATION DETAILS

// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(count($surgeonsDetails)>0){
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon = $usersDetail->signature;
		}
	}
// GETTING SURGEONS SIGN YES OR NO


// GETTING NURSE SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $nurseId;
	$nurseDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(count($nurseDetails)>0){
		foreach($nurseDetails as $usersDetail){
			$signatureOfNurse = $usersDetail->signature;
		}
	}
// GETTING NURSE SIGN YES OR NO

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

if(!$cancelRecord){
	// FORM SHIFT TO RIGHT SLIDER
		$getLeftLinkDetails = $objManageData->getRowRecord('left_navigation_forms', 'confirmationId ', $pConfId);
		$post_op_physician_order_form = $getLeftLinkDetails->post_op_physician_order_form;	
		if($post_op_physician_order_form=='true'){
			$formArrayRecord['post_op_physician_order_form'] = 'false';
			$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
		}
		if($submitMe!='true'){
			unset($arrayRecord);
			$arrayRecord['user_id'] = $_SESSION['loginUserId'];
			$arrayRecord['patient_id'] = $_SESSION['patient_id'];
			$arrayRecord['confirmation_id'] = $pConfId;
			$arrayRecord['form_name'] = 'post_op_physician_order_form';
			$arrayRecord['status'] = 'viewed';
			$arrayRecord['action_date_time'] = date('Y-m-d H:i:s');
			$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
		}
	// FORM SHIFT TO RIGHT SLIDER
}
elseif($cancelRecord){
	$fieldName="post_op_physician_order_form";
	$pageName = "blankform.php?patient_id=$patient_id&pConfId=$pConfId";
	include("left_link_hide.php");
}	
$saveLink = '&thisId='.$thisId.'&innerKey='.$innerKey.'&preColor='.$preColor.'&patient_id='.$patient_id.'&pConfId='.$pConfId.'&fieldName='.$fieldName;

if($submitMe=='true'){
	
	//START CODE TO CHECK NURSE,SURGEON SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord('postopphysicianorders', 'patient_confirmation_id', $pConfId);
		if($chkNurseSignDetails) {
			$chk_signNurseId = $chkNurseSignDetails->signNurseId;
			$chk_signNurse1Id = $chkNurseSignDetails->signNurse1Id;
			$chk_signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
			$chk_form_status = $chkNurseSignDetails->form_status;
			$chk_versionNum	=	$chkNurseSignDetails->version_num;
			$chk_versionDateTime	=	$chkNurseSignDetails->version_date_time;
		}
	//END CODE TO CHECK NURSE SIGN IN DATABASE 

	$version_num = $chk_versionNum;
	if(!$chk_versionNum)
	{
		$version_date_time = $chk_versionDateTime;
		if($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00')
		{
			$version_date_time	=	date('Y-m-d H:i:s');
		}
				
		if($chk_form_status == 'completed' || $chk_form_status=='not completed'){
			$version_num = 1;
		}else{
			$version_num	=	3;
		}
		
		$arrayRecord['version_num']	=	$version_num;
		$arrayRecord['version_date_time']	=	$version_date_time;
		
	}
	
	
	// Code Start Here to save physician orders
	$postOpDropExist = "";
	
	$physicianOrderRecordIdArr	=	$_REQUEST['physicianOrderRecordId'];
	$physicianOrderNameArr	=	$_REQUEST['physicianOrderName'];
	$physicianOrderTimeArr	=	$_REQUEST['physicianOrderTime'];
	$physicianOrderTypeArr	=	$_REQUEST['physicianOrderType'];
	if(count($physicianOrderNameArr) > 0) {
		foreach($physicianOrderNameArr as $phyOrdNme) {
			if(trim($phyOrdNme)) {
				$postOpDropExist = "Yes";			
			}
		}
	}
	$phyOrdersAdded = "";
	if(is_array($physicianOrderRecordIdArr) && count($physicianOrderRecordIdArr) > 0 )
	{
		foreach($physicianOrderRecordIdArr as $key=>$recordId)
		{
				$orderName	=	$physicianOrderNameArr[$key];
				$orderTime	=	$physicianOrderTimeArr[$key];
				$orderType	=	$physicianOrderTypeArr[$key];		
				//echo $orderName .'--' .$orderTime . '<br>';
				if(trim($orderName))
				{
					if($orderTime)
					{
						$timeSplit 			= explode(":",$orderTime);
						$timeAmPm 			= strtolower($timeSplit[1][3]);
						if( $timeAmPm == "p" ) {
							$timeSplit[0] 	= $timeSplit[0]+12;
						}
						$orderTime = $timeSplit[0].":".$timeSplit[1][0].$timeSplit[1][1].":00";
					}
					else
					{
						$orderTime			= '00:00:00';
						if($version_num <  2) {
							$postOpDropExist 	= "";	
						}
					}
					
					$dataArray	=	array();
					
					$dataArray['confirmation_id']		=	$pConfId ;
					$dataArray['chartName']				=	'post_op_physician_order_form';
					$dataArray['physician_order_name']	=	trim($orderName);
					if($version_num <  2) {
						$dataArray['physician_order_time']	=	$orderTime;
					}
					$dataArray['physician_order_type']	=	trim($orderType);
					//print_r($dataArray);
					
					if($recordId)
					{
						if(trim($orderName) && trim($orderType)=="medication") {
							unset($chkMedArr);
							$chkMedArr['recordId'] = $recordId;
							$chkMedArr['physician_order_type'] = trim($orderType); 
							$chkMedRecords	=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$dataArray);
							if(!$chkMedRecords) {
								$phyOrdersAdded = "yes";	
							}
						}
						$objManageData->UpdateRecord($dataArray,'patient_physician_orders','recordId',$recordId);
					}
					else
					{
						$chkRecords	=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$dataArray);
						if( !$chkRecords) 
						{
							$dataArray['physician_order_location']		= 'post_op_physician_orders';
							$dataArray['physician_order_date_time']		= date("Y-m-d H:i:s");
							$objManageData->addRecords($dataArray,'patient_physician_orders');	
							if(trim($orderType)=="medication") {
								$phyOrdersAdded = "yes";
							}
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
		
		//START CODE - IF ANY NEW PHY-ORDER ADDED AND FLAG OF POST-NURSING CHART IS ALREADY GREEN THEN MARK THIS FLAG AS RED FOR NURSE.
		if($phyOrdersAdded == "yes") {//IF ANY NEW PHY-ORDER ADDED THEN
			$updtPostOpNursingQry = "UPDATE postopnursingrecord SET form_status = 'not completed' WHERE confirmation_id = '".$_REQUEST["pConfId"]."' AND form_status = 'completed'";	
			$updtPostOpNursingRes = imw_query($updtPostOpNursingQry) or die($updtPostOpNursingQry.imw_error());
		}
		//END CODE - IF ANY NEW PHY-ORDER ADDED AND FLAG OF POST-NURSING CHART IS ALREADY GREEN THEN MARK THIS FLAG AS RED FOR NURSE.
	}
	
	// Code End Here to save physician orders 
	

	$text = $_REQUEST['getText'];
	$tablename = "postopphysicianorders";
	$postOpPhysOrderId = $_REQUEST['postOpPhysOrderId'];
	$arrayRecord['patientAssessed'] = $_REQUEST['chbx_pa'];
	$arrayRecord['vitalSignStable'] = $_REQUEST['chbx_vs'];
	$arrayRecord['postOpEvalDone'] = $_REQUEST['chbx_ec'];
	
	$formStatus = 'completed';
	if(($_REQUEST['chbx_pa']!='Yes' || $_REQUEST['chbx_vs']!='Yes') 
		||($_REQUEST['chbx_ec']!='Yes') 
		||($_REQUEST['chbx_wr']!='Yes') 
		||($_REQUEST['chbx_vbl']!='Yes') 
		||($_REQUEST['chbx_waar']!='Yes') 
		||($chk_signNurseId=="0")
		||($chk_signNurse1Id=="0" && $version_num >2) 
		||($chk_signSurgeon1Id=="0")
		||($postOpDropExist !='Yes')
		||(!$_REQUEST['chbx_noted_by_nurse'] && $version_num >2)
		
	){
		$formStatus = 'not completed';
	}
	
	$arrayRecord['postOpInstructionMethodWritten'] 	= $_REQUEST['chbx_wr'];
	$arrayRecord['postOpInstructionMethodVerbal'] 	= $_REQUEST['chbx_vbl'];
	$arrayRecord['postOpPhyTime'] 					= trim($_REQUEST['postOpPhyTime']);
	$arrayRecord['patientAccompaniedSafely']		= $_REQUEST['chbx_waar'];
	/*
	$arrayRecord['patientToTakeHome'] 				= addslashes($_REQUEST['patientToTakeHome']);
	$arrayRecord['physician_order_date_time']		= "";
	if(trim($_REQUEST['patientToTakeHome'])){
		$arrayRecord['physician_order_date_time']   = date("m-d-Y h:i A");
	}
	*/
	$arrayRecord['saved_operator_id']			= $_SESSION["loginUserId"];
	$arrayRecord['comment']							= addslashes($_REQUEST['comments']);
	$arrayRecord['surgeonId'] 						= $surgeonId;
	$arrayRecord['nurseId'] 							= $nurseId;
	$arrayRecord['surgeonSign'] 					= $_REQUEST['elem_signature1'];
	$arrayRecord['nurseSign'] 						= $_REQUEST['elem_signature2'];
	$arrayRecord['ascId'] 								= $ascId;	
	$arrayRecord['patient_confirmation_id'] 	= $pConfId;
	$arrayRecord['relivednurse'] 					= $_REQUEST['relived_nurse'];
	$arrayRecord['form_status'] 					= $formStatus;
	$arrayRecord['notedByNurse'] 					= $_REQUEST['chbx_noted_by_nurse'];
	//MAKE AUDIT STATUS REPORT
	unset($arrayStatusRecord);
	$arrayStatusRecord['user_id'] 					= $_SESSION['loginUserId'];
	$arrayStatusRecord['patient_id'] 				= $_SESSION['patient_id'];
	$arrayStatusRecord['confirmation_id'] 	= $pConfId;
	$arrayStatusRecord['form_name'] 			= 'post_op_physician_order_form';
	$arrayStatusRecord['action_date_time'] 	= date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS REPORT

	// UPDATE PATIENT STATUS DISCHARGED
		unset($arrayPatientStatus);
		$arrayPatientStatus['patientStatus'] = 'Discharged';
		$objManageData->updateRecords($arrayPatientStatus, 'patientconfirmation', 'patientConfirmationId', $pConfId);
	// UPDATE PATIENT STATUS DISCHARGED

	if($postOpPhysOrderId){
		$objManageData->updateRecords($arrayRecord, 'postopphysicianorders', 'postOpPhysicianOrdersId', $postOpPhysOrderId);
	}else{
		$objManageData->addRecords($arrayRecord, 'postopphysicianorders');
	}
	
	
	
	//CODE START TO SET AUDIT STATUS AFTER SAVE
		unset($conditionArr);
		$conditionArr['confirmation_id'] = $pConfId;
		$conditionArr['form_name'] = 'post_op_physician_order_form';
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
		/*
		if($formStatus == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		}else if($formStatus=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$formStatus."');</script>";	
		}*/
		
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)

}
?>
<script type="text/javascript">
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
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
			var assignedSurgeonId = '<?php echo $postOpAssignedSurgeonId;?>';
			var assignedSurgeonName = '<?php echo $postOpAssignedSurgeonName;?>';
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

</script>
<div id="post" style="display:none; position:absolute;"></div>
<?php

// GETTING FINALIZE STATUS
	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalize_status = $detailConfirmationFinalize->finalize_status;
	$patient_primary_procedure_id = $detailConfirmationFinalize->patient_primary_procedure_id; 
	$patient_secondary_procedure_id = $detailConfirmationFinalize->patient_secondary_procedure_id; 
	$patient_tertiary_procedure_id = $detailConfirmationFinalize->patient_tertiary_procedure_id; 
// GETTING FINALIZE STATUS
//show all epost ravi
?>
<script src="js/dragresize.js"></script>
<script type="text/javascript">
	dragresize.apply(document);
</script>

<?php
$table = 'patient2takehome';
$width = '250';
include("common/pre_defined_popup.php");

//GETTING POST OP IS OR NOT
$getPostOpDetails = $objManageData->getExtractRecord('postopphysicianorders', 'patient_confirmation_id', $pConfId);
if($getPostOpDetails){
	extract($getPostOpDetails);
}
if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	3; }
		
//GETTING POST OP IS OR NOT

//START GET POST OP ORDERS FROM SURGERON PROFILE
if($form_status=='') {
	$surgeonProfileQry="
		SELECT a.postOpDrop FROM surgeonprofile a,surgeonprofileprocedure b
		WHERE a.surgeonId			=	'".$postOpAssignedSurgeonId."'
		AND   b.procedureId			=	'".$postOpAssignedPriProcId."'
		AND   a.surgeonProfileId	=	b.profileId
		AND   a.del_status=''
	";
	$surgeonProfileRes = imw_query($surgeonProfileQry) or die(imw_error());
	$surgeonProfileNumRow = imw_num_rows($surgeonProfileRes);
	if($surgeonProfileNumRow>0) {
		$surgeonProfileRow = imw_fetch_array($surgeonProfileRes);
		$patientToTakeHome = stripslashes($surgeonProfileRow['postOpDrop']);
	}else {	/*//ELSE SELECT DEFAULT PROFILE OF SURGOEN
		$defaultProfileQry = "select postOpDrop from surgeonprofile where surgeonId = '".$surgeonId."' AND defaultProfile = '1'";
		$defaultProfileRes = imw_query($defaultProfileQry) or die(imw_error());
		$defaultProfileNumRow = imw_num_rows($defaultProfileRes);
		if($defaultProfileNumRow>0) {
			$defaultProfileRow = imw_fetch_array($defaultProfileRes);
			$patientToTakeHome = stripslashes($defaultProfileRow['postOpDrop']);
		}
		else
		{*/
			/* Start Procedure Preference Card if surgeon's profile/Default  Not found*/
			
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

//START GET LOCAL ANES POST-OP SIGNATURE
$localAnesQry = "SELECT signAnesthesia3Id,signAnesthesia3FirstName,signAnesthesia3MiddleName,signAnesthesia3LastName,signAnesthesia3Status,signAnesthesia3DateTime, date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat FROM localanesthesiarecord WHERE confirmation_id = '".$pConfId."'";
$localAnesRes =imw_query($localAnesQry);
if(imw_num_rows($localAnesRes)>0) {
	$localAnesRow 					= imw_fetch_array($localAnesRes);	
	$signAnesthesia3Id 				= $localAnesRow["signAnesthesia3Id"];
	$signAnesthesia3FirstName 		= $localAnesRow["signAnesthesia3FirstName"];
	$signAnesthesia3MiddleName 		= $localAnesRow["signAnesthesia3MiddleName"];
	$signAnesthesia3LastName 		= $localAnesRow["signAnesthesia3LastName"];
	$signAnesthesia3Status 			= $localAnesRow["signAnesthesia3Status"];
	$signAnesthesia3DateTime 		= $localAnesRow["signAnesthesia3DateTime"];
	$signAnesthesia3DateTimeFormat 	= $localAnesRow["signAnesthesia3DateTimeFormat"];
	if($signAnesthesia3Id<>0 && $signAnesthesia3Id<>"") {
		$Anesthesia3SubType = getUserSubTypeFun($signAnesthesia3Id); //FROM common/commonFunctions.php
	}
	$Anesthesia3PreFix = 'Dr.';
	if($Anesthesia3SubType=='CRNA') {
		$Anesthesia3PreFix = '';
	}				
}
//END GET LOCAL ANES POST-OP SIGNATURE
?>
<form name="frm_post_op_phys" action="post_op_physician_orders.php?submitMe=true" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;">
    <input type="hidden" name="divId" id="divId">
    <input type="hidden" name="counter" id="counter">
    <input type="hidden" name="secondaryValues" id="secondaryValues">
    <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
    <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
    <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
    <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
    <input type="hidden" name="postOpPhysOrderId" id="postOpPhysOrderId" value="<?php echo $postOpPhysicianOrdersId; ?>">
    <input type="hidden" name="getText" id="getText">
    <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
    <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
    <input type="hidden" name="frmAction" id="frmAction" value="post_op_physician_orders.php">	
    <input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">		
    <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
    <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
    <input type="hidden" id="bp" name="bp_hidden">
   <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
		<?php 
            $bgCol = $bgdark_orange_physician;
            $borderCol = $bgdark_orange_physician;
            include('saveDivPopUp.php'); 
        ?>
    </div>
    <div class="scheduler_table_Complete" id="" style="">
    		<?php
				$epost_table_name = "postopphysicianorders";
				include("./epost_list.php");
			?>
            
        <!--<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_or">
            <span class="bg_span_or">
                Post-Op Physician Orders
            </span>
			
         </div>	-->
         <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                 <div class="panel panel-default bg_panel_or">
                   <div class="panel-heading">
                                <h3 class="panel-title rob"> Post Op Orders </h3>

                   </div>
                   <div class="panel-body " id="p_check_in">
                        <div class="row">
                             <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                <div class="scanner_win new_s">
                                 <h4>
                                    <span>Discharge Patient when</span>      
                                 </h4>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                            	
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
                                            <label>DONE</label>
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                                 
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                             <label for="chbx_vs">Patient Assessed. Patient has recovered satisfactorily from sedation. This patient may be discharged after instructions given.</label>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3  text-center">
                                           <span class="colorChkBx" style="	<?php if($patientAssessed) { echo $whiteBckGroundColor;}?> " >
                                           <input type="checkbox" name="chbx_pa" <?php if($patientAssessed == 'Yes') echo "CHECKED"; ?> value="Yes" id="chbx_pa"  onClick="changeChbxColor('chbx_pa')" />       
                                           </span>                                                               
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                                 
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                             <label for="chbx_vs">Vital signs are stable</label>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3  text-center">
                                           <span class="colorChkBx" style="	<?php if($vitalSignStable) { echo $whiteBckGroundColor;}?> " >
                                           <input type="checkbox" name="chbx_vs" <?php if($vitalSignStable == 'Yes') echo "CHECKED"; ?> value="Yes" id="chbx_vs"  onClick="changeChbxColor('chbx_vs')" />       
                                           </span>                                                               
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                                 
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                             <label for="chbx_ec">Post-Op Evaluation Completed</label>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3  text-center">
                                            <span class="colorChkBx" style=" <?php if($postOpEvalDone) { echo $whiteBckGroundColor;}?>" >
                                            	<input type="checkbox" name="chbx_ec" <?php if($postOpEvalDone=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_ec" onClick="changeChbxColor('chbx_ec')" />                                                                      
                                            </span>
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                            </div>
                            <div class="clearfix margin_adjustment_only visible-sm"></div>
                            <?php 
							$phyOrderDisp = 'display:none;';
							$col = "9";
							$cols = "1";
							$colz = "2";
							if(($form_status == 'completed' || $form_status == 'not completed') && $version_num < 2) {
								$phyOrderDisp = '';
								$col = "4";
								$cols = "4";
								$colz = "4";
							}
							?>
														<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                             	<div class="inner_safety_wrap">
                                    <div class="row">
                                    
                                        <div class="clearfix margin_adjustment_only"></div>             
                                        <!--
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 text-center">
                                            <textarea class="form-control" id="pat_tak_hom_area_id" name="patientToTakeHome" ><?php echo stripslashes($patientToTakeHome); ?></textarea>                                                                      
                                        </div> <!-- Col-3 ends  -->
                                        
                                        
                                        <div class="scheduler_table_Complete ">
										 <div class="my_table_Checkall table_slider_head">
                                         	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf  table-striped">
                                            	<thead class="cf">
                                                	<tr>
                                                    	<th class="text-left col-md-<?php echo ($col-1);?> col-lg-<?php echo ($col-1);?> col-sm-<?php echo ($col-1);?> col-xs-<?php echo ($col-1);?>" id="TP_PHY_ORD">
                                                        	<a class="rob alle_link show-pop-list_g btn btn-default " data-placement="top" onClick="return showPatientTakeHomeNew('pat_tak_hom_area_id', '', 'no', parseInt(findPos_X('TP_PHY_ORD'))+12, parseInt(findPos_Y('TP_PHY_ORD')+20)-$(document).scrollTop()),document.getElementById('selected_frame_name_id').value='';" > <span class="fa fa-caret-right"></span> Physician Orders/Medications</a></th>
                                                        <th class="text-left col-md-<?php echo $cols;?> col-lg-<?php echo $cols;?> col-sm-<?php echo $cols;?> col-xs-<?php echo $cols;?>" style=" <?php echo $phyOrderDisp;?> ">Time </th>
                                                        <th class="text-left col-md-<?php echo ($colz+1);?> col-lg-<?php echo ($colz+1);?> col-sm-<?php echo ($colz+1);?> col-xs-<?php echo ($colz+1);?>" >Order Type </th>
                                                 	</tr>
                                              	</thead>
                                        	</table>
                                     	</div>
                                        
										 <div class="table_slider" style="max-height:170px;">          
												<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped" style="background-color:#F1F4F0;" >
                                                	<tbody>
                                                    <input type="hidden" id="vitalSignGridHolder" />	
                                                    <?php
														
														$condArr		=	array();
                            $condArr['confirmation_id']	=	$pConfId ;
                            $condArr['chartName']		=	'post_op_physician_order_form' ;
														$pOrderData		=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_name','ASC');
														$pOrderCounter	=	1;
														if(is_array($pOrderData) && count($pOrderData) > 0  )
														{
															
															foreach($pOrderData as $pOrderRow)
															{
																$time	=	($pOrderRow->physician_order_time <> '00:00:00') ? $objManageData->getTmFormat($pOrderRow->physician_order_time) : '' ;
																
																$ordrColor = $whiteBckGroundColor;
																$ordrTimeColor = $whiteBckGroundColor;
																if(!trim($pOrderRow->physician_order_name) && $pOrderCounter == '1') {
																	$ordrColor = $chngBckGroundColor;
																	$ordrTimeColor = $chngBckGroundColor;	
																}
																if(trim($pOrderRow->physician_order_name) && !$time) {
																	$ordrTimeColor = $chngBckGroundColor;
																}
																
																$medSel=$ordrSel="";
																if(($pOrderRow->physician_order_type=="" && $form_status == "") || $pOrderRow->physician_order_type=="medication" || ($pOrderRow->physician_order_type=="" && $form_status != "" && $pOrderRow->physician_order_name=="")) {
																	$medSel = "selected";
																}
																if($pOrderRow->physician_order_type=="order") {
																	$ordrSel = "selected";
																}
																	
																	
																
																echo '<tr style="padding-left:0px; background-color:#FFFFFF; ">';
															
																echo '<input type="hidden" name="physicianOrderRecordId[]" value="'.$pOrderRow->recordId.'">';
																
																echo '<td class="text-left col-md-$col col-lg-$col col-sm-$col col-xs-$col">';
																echo '<input style="'.$ordrColor.'" type="text"  name="physicianOrderName[]" id="orderName_'.$loop.'" class="form-control" tabindex="1" value="'.$pOrderRow->physician_order_name.'" />';
																echo '</td>';
																
																
																echo '<td class="text-left col-md-$cols col-lg-$col col-sm-$cols col-xs-$cols" style="'.$phyOrderDisp.'">';
																echo '<input style="'.$ordrTimeColor.'" type="text" name="physicianOrderTime[]" id="timeStamp_'.$pOrderCounter.'" class="form-control timeStamp" tabindex="2" value="'.$time.'" />';
																echo '</td>';
																
																echo '<td class="text-left col-md-$colz col-lg-$col col-sm-$colz col-xs-$colz" >';
																echo '<select name="physicianOrderType[]" id="physicianOrderType_'.$pOrderCounter.'" class="selectpicker form-control" tabindex="2"><option value="" >Select</option><option value="medication" '.$medSel.'>Medication</option><option value="order" '.$ordrSel.' >Order</option></select>';
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
																$ordrColor = $whiteBckGroundColor;
																$ordrTimeColor = $whiteBckGroundColor;
																if(!trim($pOrderRow) && $loop == '1') {
																	$ordrColor = $chngBckGroundColor;
																	$ordrTimeColor = $chngBckGroundColor;	
																}
																if(trim($pOrderRow)) {
																	$ordrTimeColor = $chngBckGroundColor;
																}
																$ordrTimeColor = $chngBckGroundColor;
																echo '<tr style="padding-left:0px; background-color:#FFFFFF; ">';
															
																echo '<input type="hidden" name="physicianOrderRecordId[]" value="">';
																
																echo '<td class="text-left col-md-$col col-lg-$col col-sm-$col col-xs-$col">';
																echo '<input style="'.$ordrColor.'" type="text"  name="physicianOrderName[]" id="orderName_'.$loop.'" class="form-control" tabindex="1"  value="'.$pOrderRow.'"  />';
																echo '</td>';
																
																echo '<td class="text-left col-md-$cols col-lg-$col col-sm-$cols col-xs-$cols" style="'.$phyOrderDisp.'">';
																echo '<input style="'.$ordrTimeColor.'" type="text" name="physicianOrderTime[]" id="timeStamp_'.$pOrderCounter.'" class="form-control timeStamp" tabindex="2"  />';
																echo '</td>';
																
																echo '<td class="text-left col-md-$colz col-lg-$col col-sm-$colz col-xs-$colz" >';
																echo '<select name="physicianOrderType[]" id="physicianOrderType_'.$pOrderCounter.'" class="selectpicker form-control " tabindex="2"><option value="" >Select</option><option value="medication" selected>Medication</option><option value="order">Order</option></select>';
																echo '</td>';
																
																echo '</tr>';	
															
																$pOrderCounter++ ;
															}
														}
															
														
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
															
															echo '<td class="text-left col-md-$col col-lg-$col col-sm-$col col-xs-$col">';
															echo '<input style="'.$ordrColor.'" type="text"  name="physicianOrderName[]" id="orderName_'.$loop.'" class="form-control" tabindex="1"  />';
															echo '</td>';
															
															echo '<td class="text-left col-md-$cols col-lg-$col col-sm-$cols col-xs-$cols" style="'.$phyOrderDisp.'">';
															echo '<input style="'.$ordrTimeColor.'" type="text" name="physicianOrderTime[]" id="timeStamp_'.$loop.'" class="form-control timeStamp" tabindex="1"  />';
															echo '</td>';
															
															echo '<td class="text-left col-md-$colz col-lg-$col col-sm-$colz col-xs-$colz" >';
															echo '<select name="physicianOrderType[]" id="physicianOrderType_'.$loop.'" class="selectpicker form-control " tabindex="2"><option value="" >Select</option><option value="medication" selected>Medication</option><option value="order">Order</option></select>';
															echo '</td>';
															
															echo '</tr>';	
														}
													?>		
                                                    </tbody>
                                              	</table>      
										   </div>                

										</div>
                                    
                                    
                                    </div>	 
                             	</div>
                            
                            
                            </div>
                        <!--   @2nd col Ends     -->
                      </div>
					  
					  
                  </div>          
                 </div> 
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
                    $callJavaFunSurgeon = "document.frm_post_op_phys.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','post_op_physician_orders_ajaxSign.php','$loginUserId','Surgeon1');";
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
					$signSurgeon1DateTimeFormatNew=$objManageData->getFullDtTmFormat($signSurgeon1DateTime);
                }
                if($_SESSION["loginUserId"]==$signSurgeon1Id) {
                    $callJavaFunSurgeonDel = "document.frm_post_op_phys.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','post_op_physician_orders_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
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
                    $callJavaFun = "document.frm_post_op_phys.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_physician_orders_ajaxSign.php','$loginUserId','Nurse1');";
                }
				
				if($loggedInUserType<>"Nurse") {
						$loginUserName = $_SESSION['loginUserName'];
						$callJavaFun1 = "return noAuthorityFunCommon('Nurse');";
				}else {
						$loginUserId = $_SESSION["loginUserId"];
						$callJavaFun1 = "document.frm_post_op_phys.hiddSignatureId.value='TDnurseSignatureId1'; return displaySignature('TDnurseNameId1','TDnurseSignatureId1','post_op_physician_orders_ajaxSign.php','$loginUserId','Nurse2');";
				}
                $signOnFileStatus = "Yes";
                $TDnurseNameIdDisplay = "block";
                $TDnurseSignatureIdDisplay = "none";
                $NurseNameShow = $loggedInUserName;
				$signNurse1DateTimeFormatNew="";
                if($signNurseId<>0 && $signNurseId<>"") {
                    $NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
                    $TDnurseNameIdDisplay = "none";
                    $TDnurseSignatureIdDisplay = "block";
					$signNurse1DateTimeFormatNew=$objManageData->getFullDtTmFormat($signNurseDateTime);
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
                    $callJavaFunDel = "document.frm_post_op_phys.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_physician_orders_ajaxSign.php','$loginUserId','Nurse1','delSign');";
                }else {
                    $callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
                }
				if($_SESSION["loginUserId"]==$signNurse1Id) {
					$callJavaFunDel1 = "document.frm_post_op_phys.hiddSignatureId.value='TDnurseNameId1'; return displaySignature('TDnurseNameId1', 'TDnurseSignatureId1', 'post_op_physician_orders_ajaxSign.php', '$loginUserId','Nurse2','delSign');";
				}else {
					$callJavaFunDel1 = "alert('Only ".addslashes($NurseNameShow1)." can remove this signature');";
				}
            //END CODE RELATED TO NURSE SIGNATURE ON FILE
            
            //START SET BACKGROUND COLOR 
            $postPhysSurgeonSignBackColor=$chngBckGroundColor;
            if($signSurgeon1Id!=0){
                $postPhysSurgeonSignBackColor==$whiteBckGroundColor; 
            }
            
            $postPhysnurseSignBackColor=$chngBckGroundColor;
            if($signNurseId!=0){
                $postPhysnurseSignBackColor==$whiteBckGroundColor; 
            }
			$prePhysnurseSignBackColor1=$chngBckGroundColor;
            if($signNurseId1!=0){
                $prePhysnurseSignBackColor1==$whiteBckGroundColor; 
            }
            //END SET BACKGROUND COLOR 
			
			if($version_num > 2) {
			?>
			
				<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix  margin_adjustment_only">
					<div class="clearfix border-dashed margin_adjustment_only"></div>
				</div> 
				<div class="inner_safety_wrap" style="font-size:18px">
					<div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12"><label for="chbx_noted_by_nurse"><span class="colorChkBx" style=" <?php if($notedByNurse==1) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" name="chbx_noted_by_nurse" id="chbx_noted_by_nurse" value="1" <?php if($notedByNurse==1)echo' checked';?> onClick="changeChbxColor('chbx_noted_by_nurse')"></span>Post-Op orders noted by nurse</label></div>
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
				<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix  margin_adjustment_only">
					<div class="clearfix border-dashed margin_adjustment_only"></div>
				</div> 
			 <?php
			}
			 ?>

            
            <div class="clearfix margin_adjustment_only"></div>
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                 <div class="panel panel-default bg_panel_or">
                   <div class="panel-heading">
                                <h3 class="panel-title rob"> Post Op Instruction Given </h3>

                   </div>
                   <div class="panel-body " id="p_check_in">
                        <div class="row">
                            <div class="clearfix"></div>
                            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                            &nbsp;
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3  text-center">
                                            <label>  DONE   </label>
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                                 
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                             <label for="chbx_wr_yes">Written</label>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3  text-center">
                                            <span class="colorChkBx" style=" <?php if($postOpInstructionMethodWritten) { echo $whiteBckGroundColor;}?>" onclick="checkSingle('chbx_wr_yes','chbx_wr');changeChbxColor('chbx_wr');">
                                            <input type="checkbox"  <?php if($postOpInstructionMethodWritten=='Yes') echo "CHECKED"; ?> name="chbx_wr" value="Yes" id="chbx_wr_yes"/>                                                                      
                                            </span>
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                                 
                                <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                             <label for="chbx_vbl_yes">Verbal</label>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3 text-center">
                                            <span class="colorChkBx" style=" <?php if($postOpInstructionMethodVerbal) { echo $whiteBckGroundColor;}?>">
                                            <input type="checkbox" <?php if($postOpInstructionMethodVerbal=='Yes') echo "CHECKED"; ?> name="chbx_vbl"  value="Yes" id="chbx_vbl_yes"  onClick="changeChbxColor('chbx_vbl');checkSingle('chbx_vbl_yes','chbx_vbl');" />
                                            </span>                                                                      
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                                 <div class="inner_safety_wrap">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-9 col-xs-9 col-lg-9">
                                             <label for="chbx_waar_yes"><!--Patient Accompanied Safely to Waiting Area-->Patient safely discharged from the center</label>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 col-lg-3  text-center">
                                           <span class="colorChkBx" style=" <?php if($patientAccompaniedSafely) { echo $whiteBckGroundColor;}?>" >
                                           <input type="checkbox" name="chbx_waar" <?php if($patientAccompaniedSafely=='Yes') echo "CHECKED"; ?> value="Yes" id="chbx_waar_yes" onClick="changeChbxColor('chbx_waar');checkSingle('chbx_waar_yes','chbx_waar')" />
                                           </span>                                                                      
                                        </div> <!-- Col-3 ends  -->
                                    </div>	 
                                 </div>
                            </div>
                            <div class="clearfix margin_adjustment_only visible-sm"></div>
                            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                             <div class="inner_safety_wrap">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-4 col-lg-2 text-left">
                                        <label for="bp_temp"> Time </label>
                                    </div> <!-- Col-3 ends  -->
                                    <div class="col-md-4 col-sm-8 col-xs-8 col-lg-6 text-center">
                                        <input class="form-control"  name="postOpPhyTime" id="bp_temp" onKeyUp="displayText1=this.value" onClick="getShowNewPos(parseInt(findPos_Y('bp_temp'))+25,parseInt(findPos_X('bp_temp')),'flag1');clearVal_c();return displayTimeAmPm('bp_temp');"  value="<?php echo $objManageData->getTmFormat($postOpPhyTime);?>"/>                                                                    </div>
                                </div>	 
                             </div>
                           </div>
                      <!--   @2nd col Ends     -->
                      </div>
                  </div>          
                 </div> 
            </div>
            
            <!--- - -- --   Sign -Out Ends Here -->
            <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
               <div class="well">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-xs-4 col-lg-1">
                        <label class="rob alle_link" for="comments">Comments</label>
                    </div>
                    <div class="clearfix visible-sm margin_adjustment_only"></div>
                    <div class="col-md-8 col-sm-12 col-xs-8 col-lg-11 text-center">
                        <textarea style="resize:none;" class="form-control"  name="comments" id="comments" onKeyUp="textAreaAdjust(this);"><?php echo stripslashes($comment); ?></textarea> 
                    </div> <!-- Col-3 ends  -->
                </div>
                </div> 
            </div>	
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
                <div class="clearfix border-dashed margin_adjustment_only"></div>
            </div>	
            <div class="clearfix margin_adjustment_only"></div>
            
            <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="inner_safety_wrap" id="TDsurgeon1NameId" style="display:<?php echo $TDsurgeon1NameIdDisplay;?>;">
                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $postPhysSurgeonSignBackColor?>;" onClick="javascript:<?php echo $callJavaFunSurgeon;?>"> Surgeon Signature </a>
                    </div>
                    <div class="inner_safety_wrap collapse" id="TDsurgeon1SignatureId" style="display:<?php echo $TDsurgeon1SignatureIdDisplay;?>;">
                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunSurgeonDel;?>"> <?php echo "<b>Surgeon:</b>". " Dr. ". $Surgeon1Name; ?>  </a></span>	     
                        <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $surgeon1SignOnFileStatus;?></span>
                        <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signSurgeon1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $signSurgeon1DateTimeFormatNew; ?> <span class="fa fa-edit fa-editsurg"></span></span></span>
                    </div>
           </div>
            
           <div class="clearfix margin_adjustment_only visible-md"></div>        
           <div class="clearfix margin_adjustment_only visible-sm"></div>     
            <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                <div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>;">
                    <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $postPhysnurseSignBackColor?>;" onClick="javascript:<?php echo $callJavaFun;?>"> Nurse Signature </a>
                </div>
                <div class="inner_safety_wrap collapse" id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>;">
                    <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"> <?php echo "<b>Nurse:</b> ". $NurseNameShow; ?>  </a></span>	     
                    <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;?></span>
                    <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="patient_confirmation_id"> <?php echo $signNurse1DateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
                </div>
            </div>
            
            
             <div class=" col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="inner_safety_wrap">
                       <div class="row">
                            <label for="relived_nurse" class="col-md-6 col-lg-4 col-xs-12 col-sm-12"> Relief Nurse</label>
                            <div class="clearfix margin_adjustment_only visible-sm"></div>
                            <div class="col-md-6 col-lg-8 col-xs-12 col-sm-12">
                             <select name="relived_nurse" id="relived_nurse" class="selectpicker form-control"> 
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
                    </div>  
           </div>
           <div class="clearfix margin_adjustment_only"></div>          
           <div class="clearfix margin_adjustment_only"></div>
           
           <div class=" col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <?php if($signAnesthesia3Id <> 0 && $signAnesthesia3Id<>"") { ?>
                    <div class="inner_safety_wrap" id="TDanesthesia3SignatureId">
                        <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" >
												<?php echo "<b>Anesthesia Provider:</b> ". $Anesthesia3PreFix." ". $signAnesthesia3LastName.", ".$signAnesthesia3FirstName." ".$signAnesthesia3MiddleName; ?>  </a></span>	     
                        <span class="rob full_width"> <b> Electronically Signed </b> Yes</span>
                        <span class="rob full_width"> <b> Signature Date</b> <?php echo $objManageData->getFullDtTmFormat($signAnesthesia3DateTime);?></span>
                    </div>
                    <?php } else { ?>
                    <div class="inner_safety_wrap" id="TDanesthesia3NameId" >
                        <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;"> Anesthesia Provider Signature </a>
                    </div>
                    <?php } ?>
           </div>
           
           <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">&nbsp;</div>
           
           <div class="clearfix margin_adjustment_only"></div>          
           <div class="clearfix margin_adjustment_only"></div>
             
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