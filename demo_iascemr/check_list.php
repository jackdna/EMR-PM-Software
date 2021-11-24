<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
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
$tablename = "surgical_check_list";

include_once("common/commonFunctions.php");
$blockImg = "images/block.gif";
$noneImg = "images/none.gif";

//include("common/linkfile.php");
$spec = "
</head>
<body onLoad=\"top.changeColor('".$commonbg_color."');\" onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost(); return top.frames[0].main_frmInner.hideSliders();\">
";
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
$objManageData 		= new manageData;

$thisId 			= $_REQUEST['thisId'];
$innerKey 			= $_REQUEST['innerKey'];
$preColor 			= $_REQUEST['preColor'];
$uid				= $_SESSION['loginUserId'];
$usertype			= $_SESSION['loginUserType'];

$patient_id 		= $_REQUEST['patient_id'];
$pConfId 			= $_REQUEST['pConfId'];
if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }

$ascId 				= $_SESSION['ascId'];
$SaveForm_alert 	= $_REQUEST['SaveForm_alert'];
$check_list_id 		= $_REQUEST['check_list_id'];
$cancelRecord 		= $_REQUEST['cancelRecord'];
$nurse				= $_REQUEST['nurse'];

//START GET STATUS OF FIRE RISK ANALYSIS FROM SURGERY CENTER
	$surgerycenter_info_qry = "select `fire_risk_analysis` from surgerycenter where surgeryCenterId = '1'";
	$surgerycenter_info_res = imw_query($surgerycenter_info_qry) or die(imw_error());
	$surgerycenter_info_row = imw_fetch_array($surgerycenter_info_res);
	$surgerycenter_fire_risk_analysis = $surgerycenter_info_row["fire_risk_analysis"];
//END GET STATUS OF FIRE RISK ANALYSIS FROM SURGERY CENTER


	if(!$cancelRecord){
		////// FORM SHIFT TO RIGHT SLIDER
			$getLeftLinkDetails 		= $objManageData->getRowRecord('left_navigation_forms', 'confirmationId', $pConfId);
			$surgical_check_list_form 	= $getLeftLinkDetails->surgical_check_list_form;	
			if($surgical_check_list_form=='true'){
				$formArrayRecord['surgical_check_list_form'] = 'false';
				$objManageData->updateRecords($formArrayRecord, 'left_navigation_forms', 'confirmationId', $pConfId);
			}
			//MAKE AUDIT STATUS VIEW
			if($_REQUEST['saveRecord']!='true'){
				unset($arrayRecord);
				$arrayRecord['user_id'] 		= $_SESSION['loginUserId'];
				$arrayRecord['patient_id'] 		= $patient_id;
				$arrayRecord['confirmation_id'] = $pConfId;
				$arrayRecord['form_name'] 		= 'surgical_check_list_form';
				$arrayRecord['status'] 			= 'viewed';
				$arrayRecord['action_date_time']= date('Y-m-d H:i:s');
				$objManageData->addRecords($arrayRecord, 'chartnotes_change_audit_tbl');
			}
			//MAKE AUDIT STATUS VIEW
			
// DONE BY MAMTA
}elseif($cancelRecord=="true"){   //IF PRESS CANCEL BUTTON
			$fieldName 	= "surgical_check_list_form";
			$pageName 	= "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId&amp;ascId=$ascId";
			include("left_link_hide.php");
}	
	$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;ascId='.$ascId.'&amp;fieldName='.$fieldName;

	//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 

if($_REQUEST['saveRecord']=='true'){
	
	unset($arrayRecord);//unset the array
	
	//START CODE TO CHECK NURSE SIGN IN DATABASE
		$chkNurseSignDetails = $objManageData->getRowRecord('surgical_check_list', 'confirmation_id', $pConfId);
		if($chkNurseSignDetails) {
			$chk_signNurse1Id = $chkNurseSignDetails->signNurse1Id;
			$chk_signNurse2Id = $chkNurseSignDetails->signNurse2Id;
			$chk_signNurse3Id = $chkNurseSignDetails->signNurse3Id;
			$chk_signNurse4Id = $chkNurseSignDetails->signNurse4Id;
			$chk_versionNum		= $chkNurseSignDetails->version_num;
			$chk_versionDateTime	= $chkNurseSignDetails->version_date_time;
			$chk_checklist_old_new = trim($chkNurseSignDetails->checklist_old_new);
			$chk_fire_risk_active_status	=	trim($chkNurseSignDetails->fire_risk_active_status);
			$chk_form_status	=	$chkNurseSignDetails->form_status;
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
			$version_num	=	2;
		}
		
		$arrayRecord['version_num']	=	$version_num;
		$arrayRecord['version_date_time']	=	$version_date_time;
		
	}
	
	if($chk_fire_risk_active_status <> 'Yes' && $surgerycenter_fire_risk_analysis == 'Y' && $chk_form_status == '')
	{
		$chk_fire_risk_active_status	=	'Yes';
		$arrayRecord['fire_risk_active_status']	=	$chk_fire_risk_active_status;
	}
	
	$text 							= $_REQUEST['getText'];
	$tablename 					= "surgical_check_list";
	
	if(($_POST['chbx_ind']!='') 		&& ($_POST['chbx_pro']!='') 
		&& ($_POST['chbx_smbp']!='') 	&& ($_POST['chbx_const']!='') 
		&& ($_POST['chbx_hp']!='') 		&& ($_POST['chbx_preth']!='')
		&& ($_POST['chbx_edi']!='') 	&& ($_POST['chbx_jm']!='') 
		&& ($_POST['chbx_itm']!='') 	&& ($_POST['chbx_coip']!='') 
		&& ($_POST['chbx_smv']!='') 	&& ($_POST['chbx_api']!='') 
		&& ($_POST['chbx_sinc']!='') 	&& ($_POST['chbx_sil']!='') 
		/*
		&& ($_POST['chbx_drts']!='') 	&& ($_POST['chbx_bldpro']!='')
		&& ($_POST['chbx_bbm']!='')  	&& ($_POST['chbx_vtpo']!='') 
		&& ($_POST['chbx_rbl']!='') 	&& ($_POST['chbx_rip']!='') 	
		&& ($_POST['chbx_ec']!='') 		&& ($_POST['chbx_cns']!='') 	
		&& ($_POST['chbx_cd']!='') 		&& ($_POST['chbx_abl']!='') 
		&& ($_POST['chbx_adicon']!='') 	&& ($_POST['chbx_adcn']!='') 	
		&& ($_POST['chbx_nops']!='') 	&& ($_POST['chbx_epa']!='')
		*/
		&& ($chk_signNurse1Id!='0')
		&& ($chk_signNurse3Id!='0')&& ($chk_signNurse4Id!='0')

		)
	{
		$formStatus = 'completed';
	}
	else
	{
		$formStatus='not completed';
	}
	
	if($chk_checklist_old_new == 'old' && $formStatus == 'completed')
	{
		if(($_POST['chbx_drts']!='') 	&& ($_POST['chbx_bldpro']!='')
		&& ($_POST['chbx_bbm']!='')  	&& ($_POST['chbx_vtpo']!='') 
		&& ($_POST['chbx_rbl']!='') 	&& ($_POST['chbx_rip']!='') 	
		&& ($_POST['chbx_ec']!='') 		&& ($_POST['chbx_cns']!='') 	
		&& ($_POST['chbx_cd']!='') 		&& ($_POST['chbx_abl']!='') 
		&& ($_POST['chbx_adicon']!='')&& ($_POST['chbx_adcn']!='') 	
		&& ($_POST['chbx_nops']!='') 	&& ($_POST['chbx_epa']!='')
		)
		{
			$formStatus = 'completed';
		}
		else
		{	
			$formStatus='not completed';
		}
	}
	
	if($version_num < 2 && $formStatus == 'completed')
	{
		if(		($_POST['chbx_ipp']!='') 	&& ($_POST['chbx_asc']!='')
			&&	($_POST['chbx_smpp'] !='') 	&& ($_POST['chbx_pa'] !='') 
			&& 	($_POST['chbx_dar'] !='') 	&& ($_POST['chbx_adcpc']!='')
			&&	($chk_signNurse2Id!='0')
		)
		{
			$formStatus = 'completed';
		}
		else
		{	
			$formStatus='not completed';
		}
	}
	
	//START CODE TO CHECK FIRE RISK ANALYSIS
	if($chk_fire_risk_active_status == 'Yes' && $formStatus == 'completed' ) {
		if(		($_POST['chbx_ssx']!='') && ($_POST['fire_risk_score']!='')
		    && 	($_POST['chbx_oos']!='') && ($_POST['chbx_ais']!='')
		){
			$formStatus = 'completed';	
		}else {
			$formStatus='not completed';
		}
	}
	//END CODE TO CHECK FIRE RISK ANALYSIS

	if($chk_fire_risk_active_status == 'Yes'){
		$arrayRecord['surgical_xiphoid'] 		= addslashes($_POST['chbx_ssx']);
		$arrayRecord['fire_risk_score'] 		= addslashes($_POST['fire_risk_score']);
		$arrayRecord['oxygen_source'] 			= addslashes($_POST['chbx_oos']);
		$arrayRecord['ignition_source'] 		= addslashes($_POST['chbx_ais']);
	}
	$arrayRecord['form_status'] 				= $formStatus;
	$arrayRecord['patient_id']					= $patient_id;
	$arrayRecord['confirmation_id']				= $pConfId;
	$arrayRecord['user_id']						= $uid;
	$arrayRecord['save_date_time']				= date('Y-m-d H:i:s');
	$arrayRecord['identity'] 					= addslashes($_POST['chbx_ind']);
	$arrayRecord['procedureAndProcedureSite'] 	= addslashes($_POST['chbx_pro']);
	$arrayRecord['siteMarkedByPerson'] 			= addslashes($_POST['chbx_smbp']);
	$arrayRecord['consent'] 					= addslashes($_POST['chbx_const']);
	$arrayRecord['historyAndPhysical'] 			= addslashes($_POST['chbx_hp']);
	$arrayRecord['preanesthesiaAssessment']		= addslashes($_POST['chbx_preth']);
	$arrayRecord['diagnosticAndRadiologic']		= addslashes($_POST['chbx_drts']);
	$arrayRecord['bloodProduct'] 				= addslashes($_POST['chbx_bldpro']);
	$arrayRecord['anySpecialEquipment']			= addslashes($_POST['chbx_edi']);
	$arrayRecord['betaBlockerMedication'] 		= addslashes($_POST['chbx_bbm']);
	$arrayRecord['venousThromboembolism']		= addslashes($_POST['chbx_vtpo']);
	$arrayRecord['jormothermiaMeasures']		= addslashes($_POST['chbx_jm']);
	$arrayRecord['confirmIPPSC_signin']			= addslashes($_POST['chbx_ipp']);
	$arrayRecord['siteMarked'] 					= addslashes($_POST['chbx_smpp']);
	$arrayRecord['patientAllergies'] 			= addslashes($_POST['chbx_pa']);	
	$arrayRecord['difficultAirway']				= addslashes($_POST['chbx_dar']);	

	$arrayRecord['riskBloodLoss']				= addslashes($_POST['chbx_rbl']);
	$arrayRecord['bloodLossUnits']				= "";
	if(addslashes($_POST['chbx_rbl']=="Yes")){
		$arrayRecord['bloodLossUnits']			= addslashes($_POST['rbl_no_of_units']);
	}
	$arrayRecord['anesthesiaSafety']			= addslashes($_POST['chbx_asc']);	
	$arrayRecord['allMembersTeam'] 				= addslashes($_POST['chbx_adcpc']);
	$arrayRecord['introducationTeamMember']		= addslashes($_POST['chbx_itm']);
	$arrayRecord['confirmIPPSC'] 				= addslashes($_POST['chbx_coip']);
	$arrayRecord['siteMarkedAndVisible'] 		= addslashes($_POST['chbx_smv']);
	$arrayRecord['relevantImages'] 				= addslashes($_POST['chbx_rip']);
	$arrayRecord['anyEquipmentConcern'] 		= addslashes($_POST['chbx_ec']);	
	$arrayRecord['criticalStep'] 				= addslashes($_POST['chbx_cns']);	
	$arrayRecord['caseDuration'] 				= addslashes($_POST['chbx_cd']);	
	$arrayRecord['anticipatedBloodLoss']		= addslashes($_POST['chbx_abl']);	
	$arrayRecord['antibioticProphylaxis']		= addslashes($_POST['chbx_api']);
	$arrayRecord['anesthesiaAdditionalConcerns']= addslashes($_POST['chbx_adicon']);	
	$arrayRecord['sterilizationIndicators'] 	= addslashes($_POST['chbx_sinc']);
	$arrayRecord['nurseAdditionalConcerns']		= addslashes($_POST['chbx_adcn']);
	$arrayRecord['nameOperativeProcedure']		= addslashes($_POST['chbx_nops']);	
	$arrayRecord['specimensIdentified'] 		= addslashes($_POST['chbx_sil']);
	$arrayRecord['anyEquipmentProblem'] 		= addslashes($_POST['chbx_epa']);
	$arrayRecord['comments']					= addslashes($_POST['comments']);
	$arrayRecord['reliefNurse1']				= addslashes($_POST['relivedNurse1IdList']);
	$arrayRecord['reliefNurse2']				= addslashes($_POST['relivedNurse2IdList']);
	$arrayRecord['reliefNurse3']				= addslashes($_POST['relivedNurse3IdList']);
	$arrayRecord['reliefNurse4']				= addslashes($_POST['relivedNurse4IdList']);
	if(!$chk_checklist_old_new)
	{
		$arrayRecord['checklist_old_new']		= 'new';	
	}
	
			
	
	//MAKE AUDIT STATUS CRATED OR MODIFIED
		unset($arrayStatusRecord);
		$arrayStatusRecord['user_id'] 			= $_SESSION['loginUserId'];
		$arrayStatusRecord['patient_id'] 		= $_SESSION['patient_id'];
		$arrayStatusRecord['confirmation_id'] 	= $pConfId;
		$arrayStatusRecord['form_name'] 		= 'surgical_check_list_form';	
		$arrayStatusRecord['action_date_time'] 	= date('Y-m-d H:i:s');
	//MAKE AUDIT STATUS CRATED OR MODIFIED
	
	if($check_list_id){		
		$objManageData->updateRecords($arrayRecord, 'surgical_check_list', 'check_list_id', $check_list_id);
	}else{
		$check_list_id = $objManageData->addRecords($arrayRecord, 'surgical_check_list');
	}
	
		
		//CODE START TO SET AUDIT STATUS AFTER SAVE
			unset($conditionArr);
			$conditionArr['confirmation_id']= $pConfId;
			$conditionArr['form_name'] 		= 'surgical_check_list_form';
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
	
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script>top.changeChkMarkImage('CheckList','".$formStatus."');</script>";	
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
	
	
	function temp()
	{
		//alert("A");
		var div = document.getElementById("iframe_health_quest");
		var win = window.open("about:blank");
		win.document.write("<body><textarea>"+div.innerHTML+"</textarea></body>");
		return true;
	}

	function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {
	//	alert(delSign);
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
		url=url+"&thisId="+thisId1
		url=url+"&userIdentity="+userIdentity
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		if(delSign){
			if(delSign=="delSign1"){ 
				url=url+"&delSign=yes_1"
			}else if(delSign=="delSign2"){ 
				url=url+"&delSign=yes_2"
			}else if(delSign=="delSign3"){ 
				url=url+"&delSign=yes_3"
			}else if(delSign=="delSign4"){ 
				url=url+"&delSign=yes_4"
			}
		}
		//alert(url);
		xmlHttp.onreadystatechange=displayUserSignFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function displayUserSignFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				//alert(xmlHttp.responseText);
				var objId = document.getElementById('hiddSignatureId').value;
				document.getElementById(objId).innerHTML=xmlHttp.responseText;
				//alert(xmlHttp.responseText);
				top.frames[0].setPNotesHeight();
			}
	}
	
	/*
	function changeFireRiskScore(_this)
	{
			var _thisName	=	_this.getAttribute('name');
			var obj = document.getElementsByName(_thisName);
			var val = '';
			for(var i = 0; i < obj.length; i++)
			{
				if(obj[i].checked == true)
				{
					val = obj[i].value;
					break;
				}
			}
			document.getElementById('fire_risk_score').value = val;
		
	}*/
</script>

<div id="post" style="display:block; position:absolute;"></div>
<script src="js/dragresize.js"></script> 
<script type="text/javascript">
	dragresize.apply(document);
</script>
<?php 
// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;	
// GETTING CONFIRMATION DETAILS

	//include("common/pre_defined_popup.php");
	if($check_list_id){
		$getCheckListDetail = $objManageData->getExtractRecord('surgical_check_list', 'check_list_id', $check_list_id);
	}else if($pConfId){
		$getCheckListDetail = $objManageData->getExtractRecord('surgical_check_list', 'confirmation_id', $pConfId);	
	}
	if(is_array($getCheckListDetail)){
		extract($getCheckListDetail);
	}
	if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
	else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	2; }
	
	if($fire_risk_active_status != 'Yes' && $surgerycenter_fire_risk_analysis == 'Y' && trim($form_status) == "") {
		$fire_risk_active_status = 'Yes';
	}
	
	$dbNurseName		= "";
	$check_list_nurse_id= "";
	 if($usertype=='Nurse') {
		$dbNurseNameArr = 	getUsrNm($_SESSION['loginUserId']);
		$dbNurseName 	= $dbNurseNameArr[0];
		$check_list_nurse_id = $_SESSION['loginUserId'];
	}
	$dbNurseNameArrP="";
	$procedure_check_in_dbNurseName="";
	if($procedure_check_in_nurse_id){
		$dbNurseNameArrP 				= getUsrNm($procedure_check_in_nurse_id);
		$procedure_check_in_dbNurseName = $dbNurseNameArrP[0];
	}
	$dbNurseNameArrSI="";
	$sign_in_dbNurseName="";
	if($sign_in_nurse_id){
		$dbNurseNameArrSI	 = getUsrNm($sign_in_nurse_id);
		$sign_in_dbNurseName = $dbNurseNameArrSI[0];
	}
	$dbNurseNameArrT="";
	$dbNurseNameArrT="";
	if($time_out_nurse_id){
		$dbNurseNameArrT	 = getUsrNm($time_out_nurse_id);
		$time_out_dbNurseName = $dbNurseNameArrT[0];
	}
	$dbNurseNameArrSO="";
	$sign_out_dbNurseName="";
	if($sign_out_nurse_id){
		$dbNurseNameArrSO	 = getUsrNm($sign_out_nurse_id);
		$sign_out_dbNurseName = $dbNurseNameArrSO[0];
	}
	
	
	$blurInput = "onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
	$keyPressInput = "onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
	
	$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
	$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
	$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
	
	$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
	$loggedInUserType = $ViewUserNameRow["user_type"];
	$loggedInSignatureOfNurse = $ViewUserNameRow["signature"];
							
	if($loggedInUserType<>"Nurse") {
		$loginUserName = $_SESSION['loginUserName'];
		$callJavaFunNurse1 = "return noAuthorityFunCommon('Nurse');";
		$callJavaFunNurse2 = "return noAuthorityFunCommon('Nurse');";
		$callJavaFunNurse3 = "return noAuthorityFunCommon('Nurse');";
		$callJavaFunNurse4 = "return noAuthorityFunCommon('Nurse');";
	}else {
		$loginUserId = $_SESSION["loginUserId"];
		$callJavaFunNurse1 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','check_list_ajax_sign.php','$loginUserId','nurse1');";
		$callJavaFunNurse2 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse2SignatureId'; return displaySignature('TDnurse2NameId','TDnurse2SignatureId','check_list_ajax_sign.php','$loginUserId','nurse2');";
		$callJavaFunNurse3 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse3SignatureId'; return displaySignature('TDnurse3NameId','TDnurse3SignatureId','check_list_ajax_sign.php','$loginUserId','nurse3');";		
		$callJavaFunNurse4 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse4SignatureId'; return displaySignature('TDnurse4NameId','TDnurse4SignatureId','check_list_ajax_sign.php','$loginUserId','nurse4');";
	}
	$arr_users_nurse=array();
	$relivedNurseQry = "select usersId,lname,fname,mname from users where (user_type IN('Nurse','Anesthesiologist') or (user_type='Anesthesiologist' and user_sub_type='CRNA')) and deleteStatus!='Yes' ORDER BY lname";
	$relivedNurseRes = imw_query($relivedNurseQry) or die(imw_error());
	while($relivedNurseRow=imw_fetch_array($relivedNurseRes)) {
		$relivedSelectNurseID= $relivedNurseRow["usersId"];
		$relivedNurseName=trim($relivedNurseRow["lname"].", ".$relivedNurseRow["fname"]." ".$relivedNurseRow["mname"]);
		$arr_users_nurse[$relivedSelectNurseID]=$relivedNurseName;
	}
		
	?>
<form name="frm_surgical_check_list" id="frm_surgical_check_list" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="check_list.php?saveRecord=true&amp;SaveForm_alert=true">
  <input type="hidden" name="divId" id="divId">
  <input type="hidden" name="counter" id="counter">
  <input type="hidden" name="secondaryValues" id="secondaryValues">
  <input type="hidden" name="formIdentity" id="formIdentity" value="check_list">
  <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
  <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
  <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
  <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
  <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $_REQUEST['patient_id']; ?>">
  <input type="hidden" name="check_list_id" id="check_list_id" value="<?php echo $check_list_id;?>">
  <input type="hidden" name="getText" id="getText">
  <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
  <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
  <input type="hidden" name="frmAction" id="frmAction" value="check_list.php">
  <input type="hidden" name="check_list_nurse_id" id="check_list_nurse_id" value="<?php echo $check_list_nurse_id;?>">
  <input type="hidden" name="procedure_check_in_nurse_id" id="procedure_check_in_nurse_id" value="<?php echo $procedure_check_in_nurse_id;  ?>" >
  <input type="hidden" name="sign_in_nurse_id" id="sign_in_nurse_id" value="<?php echo $sign_in_nurse_id;  ?>"  >
  <input type="hidden" name="time_out_nurse_id" id="time_out_nurse_id" value="<?php echo $time_out_nurse_id;  ?>" >
  <input type="hidden" name="sign_out_nurse_id" id="sign_out_nurse_id" value="<?php echo $sign_out_nurse_id;  ?>" >
  <input type="hidden" id="vitalSignGridHolder" />
  <div class="slider_content scheduler_table_Complete" id="" style="">
    <?php
					$epost_table_name = "surgical_check_list";
					include("./epost_list.php");
			?>
    				<!--
            <div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_or">
                <span class="bg_span_or">
                    Safety Check List
                </span>
				
             </div>-->
    
    <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
      <?php 
                    $bgCol = $bgdark_orange_physician;
                    $borderCol = $bgdark_orange_physician;
                    include('saveDivPopUp.php'); 
                ?>
    </div>
    <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
      <div class="panel panel-default bg_panel_or">
        <div class="panel-heading haed_p_clickable">
          <h3 class="panel-title rob"> PROCEDURE CHECK-IN </h3>
          <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i> </span> </div>
        <div class="panel-body " id="p_check_in">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <div class="scanner_win new_s">
                <h4> <span>IN HOLDING AREA</span> </h4>
              </div>
            </div>
            <p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-5"> Patient / Patient Representative confirms with nurse</p>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
              <div class="inner_safety_wrap">
                <div class="row">
                  <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-3" for="n_select"> Relief Nurse / Anesthesia </label>
                  <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-9">
                    <select name="relivedNurse1IdList"id="relivedNurse1IdList" class="selectpicker" >
                      <option value="">Select</option>
                      <?php
                                                
                                                    foreach($arr_users_nurse as $relivedSelectNurseID=>$relivedNurseName) {
                                                        $sel="";
                                                        if($reliefNurse1==$relivedSelectNurseID) {
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
            </div>
            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
              <div class="clearfix border-dashed margin_adjustment_only"></div>
            </Div>
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r" id=""> Identity </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($identity) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ind_yes','chbx_ind'),changeChbxColor('chbx_ind');" <?php if($identity=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ind" id="chbx_ind_yes">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($identity) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ind_no','chbx_ind'),changeChbxColor('chbx_ind');" <?php if($identity=='No') echo "CHECKED"; ?> value="No" name="chbx_ind" id="chbx_ind_no">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($identity) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ind_na','chbx_ind'),changeChbxColor('chbx_ind');" <?php if(stripslashes($identity)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_ind" id="chbx_ind_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Site Marked and Verified </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarkedByPerson) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smbp_yes','chbx_smbp'),changeChbxColor('chbx_smbp')" <?php if($siteMarkedByPerson=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_smbp" id="chbx_smbp_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarkedByPerson) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smbp_no','chbx_smbp'),changeChbxColor('chbx_smbp')" <?php if($siteMarkedByPerson=='No') echo "CHECKED"; ?> value="No" name="chbx_smbp" id="chbx_smbp_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarkedByPerson) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smbp_na','chbx_smbp'),changeChbxColor('chbx_smbp')" <?php if(stripslashes($siteMarkedByPerson)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_smbp" id="chbx_smbp_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <!--   @2nd col starts    -->
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap visible-lg">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap visible-md">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Procedure and procedure site </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($procedureAndProcedureSite) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_pro_yes','chbx_pro'),changeChbxColor('chbx_pro')" <?php if($procedureAndProcedureSite=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_pro" id="chbx_pro_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($procedureAndProcedureSite) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_pro_no','chbx_pro'),changeChbxColor('chbx_pro')" <?php if($procedureAndProcedureSite=='No') echo "CHECKED"; ?> value="No" name="chbx_pro" id="chbx_pro_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($procedureAndProcedureSite) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_pro_na','chbx_pro'),changeChbxColor('chbx_pro')" <?php if(stripslashes($procedureAndProcedureSite)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_pro" id="chbx_pro_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Consent(s) </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($consent) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_const_yes','chbx_const'),changeChbxColor('chbx_const')" <?php if($consent=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_const" id="chbx_const_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($consent) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_const_no','chbx_const'),changeChbxColor('chbx_const')" <?php if($consent=='No') echo "CHECKED"; ?> value="No" name="chbx_const" id="chbx_const_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($consent) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_const_na','chbx_const'),changeChbxColor('chbx_const')" <?php if(stripslashes($consent)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_const" id="chbx_const_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <!--   @2nd col Ends     --> 
          </div>
          <p class="rob l_height_28"> Nurse confirms presence of: </p>
          <div class="clearfix border-dashed margin_adjustment_only"></div>
          <Div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> History and physical </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($historyAndPhysical) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_hp_yes','chbx_hp'),changeChbxColor('chbx_hp')" <?php if($historyAndPhysical=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_hp" id="chbx_hp_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($historyAndPhysical) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_hp_no','chbx_hp'),changeChbxColor('chbx_hp')" <?php if($historyAndPhysical=='No') echo "CHECKED"; ?> value="No" name="chbx_hp" id="chbx_hp_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($historyAndPhysical) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_hp_na','chbx_hp'),changeChbxColor('chbx_hp')" <?php if(stripslashes($historyAndPhysical)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_hp" id="chbx_hp_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Diagnostic and radiologic test results </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($diagnosticAndRadiologic) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_drts_yes','chbx_drts'),changeChbxColor('chbx_drts')" <?php if($diagnosticAndRadiologic=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_drts" id="chbx_drts_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($diagnosticAndRadiologic) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_drts_no','chbx_drts'),changeChbxColor('chbx_drts')" <?php if($diagnosticAndRadiologic=='No') echo "CHECKED"; ?> value="No" name="chbx_drts" id="chbx_drts_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($diagnosticAndRadiologic) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_drts_na','chbx_drts'),changeChbxColor('chbx_drts')" <?php if(stripslashes($diagnosticAndRadiologic)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_drts" id="chbx_drts_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Any special equipment, devices, implants </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anySpecialEquipment) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_edi_yes','chbx_edi'),changeChbxColor('chbx_edi')" <?php if($anySpecialEquipment=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_edi" id="chbx_edi_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anySpecialEquipment) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_edi_no','chbx_edi'),changeChbxColor('chbx_edi')" <?php if($anySpecialEquipment=='No') echo "CHECKED"; ?> value="No" name="chbx_edi" id="chbx_edi_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anySpecialEquipment) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_edi_na','chbx_edi'),changeChbxColor('chbx_edi')" <?php if(stripslashes($anySpecialEquipment)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_edi" id="chbx_edi_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Venous thromboembolism prophylaxis ordered </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($venousThromboembolism) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_vtpo_yes','chbx_vtpo'),changeChbxColor('chbx_vtpo')" <?php if($venousThromboembolism=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_vtpo" id="chbx_vtpo_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($venousThromboembolism) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_vtpo_no','chbx_vtpo'),changeChbxColor('chbx_vtpo')" <?php if($venousThromboembolism=='No') echo "CHECKED"; ?> value="No" name="chbx_vtpo" id="chbx_vtpo_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($venousThromboembolism) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_vtpo_na','chbx_vtpo'),changeChbxColor('chbx_vtpo')" <?php if(stripslashes($venousThromboembolism)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_vtpo" id="chbx_vtpo_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Preanesthesia assessment </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($preanesthesiaAssessment) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_preth_yes','chbx_preth'),changeChbxColor('chbx_preth')" <?php if($preanesthesiaAssessment=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_preth" id="chbx_preth_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($preanesthesiaAssessment) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_preth_no','chbx_preth'),changeChbxColor('chbx_preth')" <?php if($preanesthesiaAssessment=='No') echo "CHECKED"; ?> value="No" name="chbx_preth" id="chbx_preth_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($preanesthesiaAssessment) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_preth_na','chbx_preth'),changeChbxColor('chbx_preth')" <?php if(stripslashes($preanesthesiaAssessment)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_preth" id="chbx_preth_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Blood product </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($bloodProduct) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_bldpro_yes','chbx_bldpro'),changeChbxColor('chbx_bldpro')" <?php if($bloodProduct=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_bldpro" id="chbx_bldpro_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($bloodProduct) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_bldpro_no','chbx_bldpro'),changeChbxColor('chbx_bldpro')" <?php if($bloodProduct=='No') echo "CHECKED"; ?> value="No" name="chbx_bldpro" id="chbx_bldpro_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($bloodProduct) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_bldpro_na','chbx_bldpro'),changeChbxColor('chbx_bldpro')" <?php if(stripslashes($bloodProduct)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_bldpro" id="chbx_bldpro_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Beta blocker medication given </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($betaBlockerMedication) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_bbm_yes','chbx_bbm'),changeChbxColor('chbx_bbm')" <?php if($betaBlockerMedication=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_bbm" id="chbx_bbm_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($betaBlockerMedication) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_bbm_no','chbx_bbm'),changeChbxColor('chbx_bbm')" <?php if($betaBlockerMedication=='No') echo "CHECKED"; ?> value="No" name="chbx_bbm" id="chbx_bbm_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($betaBlockerMedication) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_bbm_na','chbx_bbm'),changeChbxColor('chbx_bbm')" <?php if(stripslashes($betaBlockerMedication)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_bbm" id="chbx_bbm_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Normothermia measures </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($jormothermiaMeasures) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_jm_yes','chbx_jm'),changeChbxColor('chbx_jm')" <?php if($jormothermiaMeasures=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_jm" id="chbx_jm_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($jormothermiaMeasures) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_jm_no','chbx_jm'),changeChbxColor('chbx_jm')" <?php if($jormothermiaMeasures=='No') echo "CHECKED"; ?> value="No" name="chbx_jm" id="chbx_jm_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($jormothermiaMeasures) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_jm_na','chbx_jm'),changeChbxColor('chbx_jm')" <?php if(stripslashes($jormothermiaMeasures)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_jm" id="chbx_jm_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <?php
                                        $signOnFileStatusNurse1 = "Yes";
                                        $TDnurseNameIdDisplayNurse1 = "block";
                                        $TDnurseSignatureIdDisplayNurse1 = "none";
                                        $Nurse1NameShow = $loggedInUserName;
                                        if($signNurse1Id<>0 && $signNurse1Id<>"") {
                                            $Nurse1NameShow = $signNurse1LastName.", ".$signNurse1FirstName." ".$signNurse1MiddleName;
                                            $signOnFileStatusNurse1 = $signNurse1FileStatus;
                                            //$signDateTimeStatusNurse1 = date("m-d-Y h:i A",strtotime($signNurse1DateTime));
                                            $signDateTimeStatusNurse1 = $objManageData->getFullDtTmFormat($signNurse1DateTime);
											
                                            $TDnurseNameIdDisplayNurse1 = "none";
                                            $TDnurseSignatureIdDisplayNurse1 = "block";
                                        }
                                        if($_SESSION["loginUserId"]==$signNurse1Id) {
                                            $callJavaFunDelNurse1 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse1NameId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','check_list_ajax_sign.php','$loginUserId','nurse1','delSign1');";
                                        }else {
                                            $callJavaFunDelNurse1 = "alert('Only $Nurse1NameShow can remove this signature');";
                                        }
                                    ?>
              <div class="inner_safety_wrap" id="TDnurse1NameId" style="display:<?php echo $TDnurseNameIdDisplayNurse1;?>;"> <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunNurse1;?>"> Nurse Signature </a> </div>
              <div class="inner_safety_wrap collapse" id="TDnurse1SignatureId" style="display:<?php echo $TDnurseSignatureIdDisplayNurse1;?>;"> <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDelNurse1;?>"> <?php echo $Nurse1NameShow;?> </a></span> <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatusNurse1;?></span> <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse1DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signDateTimeStatusNurse1; ?> <span class="fa fa-edit"></span></span></span> </div>
            </div>
          </Div>
        </div>
      </div>
    </div>
    <?php if($version_num < 2) { ?>
    <!--- Sign IN STARTS HERE -->
    <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
      <div class="panel panel-default bg_panel_or">
        <div class="panel-heading haed_p_clickable">
          <h3 class="panel-title rob">SIGN IN </h3>
          <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i> </span> </div>
        <div class="panel-body " id="p_check_in">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <div class="scanner_win new_s">
                <h4> <span>Before Induction of Anesthesia</span> </h4>
              </div>
            </div>
            <p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-5"> Nurse and anesthesia care provider confirm:</p>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
              <div class="inner_safety_wrap">
                <div class="row">
                  <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-3" for="n_select"> Relief Nurse / Anesthesia </label>
                  <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-9">
                    <select name="relivedNurse2IdList" class="selectpicker">
                      <option value="">Select</option>
                      <?php
													foreach($arr_users_nurse as $relivedSelectNurseID=>$relivedNurseName) {
														$sel="";
														if($reliefNurse2==$relivedSelectNurseID) {
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
            </div>
            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
              <div class="clearfix border-dashed margin_adjustment_only"></div>
            </Div>
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Confirmation of: identify, procedure, procedure site and consent(s) </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC_signin) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ipp_yes','chbx_ipp'),changeChbxColor('chbx_ipp')" <?php if($confirmIPPSC_signin=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ipp" id="chbx_ipp_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC_signin) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ipp_no','chbx_ipp'),changeChbxColor('chbx_ipp')" <?php if($confirmIPPSC_signin=='No') echo "CHECKED"; ?> value="No" name="chbx_ipp" id="chbx_ipp_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC_signin) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ipp_na','chbx_ipp'),changeChbxColor('chbx_ipp')" <?php if(stripslashes($confirmIPPSC_signin)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_ipp" id="chbx_ipp_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Patient allergies </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($patientAllergies) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_pa_yes','chbx_pa'),changeChbxColor('chbx_pa')" <?php if($patientAllergies=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_pa" id="chbx_pa_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($patientAllergies) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_pa_no','chbx_pa'),changeChbxColor('chbx_pa')" <?php if($patientAllergies=='No') echo "CHECKED"; ?> value="No" name="chbx_pa" id="chbx_pa_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($patientAllergies) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_pa_na','chbx_pa'),changeChbxColor('chbx_pa')" <?php if(stripslashes($patientAllergies)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_pa" id="chbx_pa_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Risk of blood loss (>500 ml) </label>
                    <span class="date_r" id="rblno_of_units" style="display:<?php echo $displayStatus=($riskBloodLoss=='Yes')? "inline-block" : "none"; ?>;"> # of units available:
                    <input type="text" name="rbl_no_of_units"  value="<?php echo $bloodLossUnits; ?>" id="rbl_no_of_units">
                    </span> </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_yes','chbx_rbl'),changeChbxColor('chbx_rbl');disp(document.frm_surgical_check_list.chbx_rbl,'rblno_of_units');" <?php if($riskBloodLoss=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_rbl" id="chbx_rbl_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_no','chbx_rbl'),changeChbxColor('chbx_rbl');disp_none(document.frm_surgical_check_list.chbx_rbl,'rblno_of_units');" <?php if($riskBloodLoss=='No') echo "CHECKED"; ?> value="No" name="chbx_rbl" id="chbx_rbl_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($riskBloodLoss) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_rbl_na','chbx_rbl'),changeChbxColor('chbx_rbl');disp_none(document.frm_surgical_check_list.chbx_rbl,'rblno_of_units');" <?php if(stripslashes($riskBloodLoss)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_rbl" id="chbx_rbl_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </Div>
            <!--   @2nd col starts    -->
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap visible-lg">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap visible-md">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Site marked by person performing the procedure </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarked) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smpp_yes','chbx_smpp'),changeChbxColor('chbx_smpp')" <?php if($siteMarked=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_smpp" id="chbx_smpp_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarked) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smpp_no','chbx_smpp'),changeChbxColor('chbx_smpp')" <?php if($siteMarked=='No') echo "CHECKED"; ?> value="No" name="chbx_smpp" id="chbx_smpp_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarked) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smpp_na','chbx_smpp'),changeChbxColor('chbx_smpp')" <?php if(stripslashes($siteMarked)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_smpp" id="chbx_smpp_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Difficult airway or aspiration risk? </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($difficultAirway) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_dar_yes','chbx_dar'),changeChbxColor('chbx_dar')" <?php if($difficultAirway=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_dar" id="chbx_dar_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($difficultAirway) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_dar_no','chbx_dar'),changeChbxColor('chbx_dar')" <?php if($difficultAirway=='No') echo "CHECKED"; ?> value="No" name="chbx_dar" id="chbx_dar_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($difficultAirway) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_dar_na','chbx_dar'),changeChbxColor('chbx_dar')" <?php if(stripslashes($difficultAirway)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_dar" id="chbx_dar_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Anesthesia safety check completed </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anesthesiaSafety) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_asc_yes','chbx_asc'),changeChbxColor('chbx_asc')" <?php if($anesthesiaSafety=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_asc" id="chbx_asc_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anesthesiaSafety) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_asc_no','chbx_asc'),changeChbxColor('chbx_asc')" <?php if($anesthesiaSafety=='No') echo "CHECKED"; ?> value="No" name="chbx_asc" id="chbx_asc_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anesthesiaSafety) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_asc_na','chbx_asc'),changeChbxColor('chbx_asc')" <?php if(stripslashes($anesthesiaSafety)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_asc" id="chbx_asc_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <!--   @2nd col Ends     --> 
          </div>
          <p class="rob l_height_28"> Briefing: </p>
          <div class="clearfix border-dashed margin_adjustment_only"></div>
          <Div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> All members of the team have discussed care plan and addressed concerns </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($allMembersTeam) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adcpc_yes','chbx_adcpc'),changeChbxColor('chbx_adcpc')" <?php if($allMembersTeam=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_adcpc" id="chbx_adcpc_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($allMembersTeam) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adcpc_no','chbx_adcpc'),changeChbxColor('chbx_adcpc')" <?php if($allMembersTeam=='No') echo "CHECKED"; ?> value="No" name="chbx_adcpc" id="chbx_adcpc_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($allMembersTeam) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adcpc_na','chbx_adcpc'),changeChbxColor('chbx_adcpc')" <?php if(stripslashes($allMembersTeam)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_adcpc" id="chbx_adcpc_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <?php
                                        $signOnFileStatusNurse2 = "Yes";
                                        $TDnurseNameIdDisplayNurse2 = "block";
                                        $TDnurseSignatureIdDisplayNurse2 = "none";
                                        $Nurse2NameShow = $loggedInUserName;
                                        if($signNurse2Id<>0 && $signNurse2Id<>"") {
                                            $Nurse2NameShow = $signNurse2LastName.", ".$signNurse2FirstName." ".$signNurse2MiddleName;
                                            $signOnFileStatusNurse2 = $signNurse2FileStatus;	
                                            //$signDateTimeStatusNurse2 = date("m-d-Y h:i A",strtotime($signNurse2DateTime));
                                            $signDateTimeStatusNurse2 = $objManageData->getFullDtTmFormat($signNurse2DateTime);
											$TDnurseNameIdDisplayNurse2 = "none";
                                            $TDnurseSignatureIdDisplayNurse2 = "block";
                                        }
                                        if($_SESSION["loginUserId"]==$signNurse2Id) {
                                            $callJavaFunDelNurse2 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse2NameId'; return displaySignature('TDnurse2NameId','TDnurse2SignatureId','check_list_ajax_sign.php','$loginUserId','nurse2','delSign2');";
                                        }else {
                                            $callJavaFunDelNurse2 = "alert('Only $Nurse2NameShow can remove this signature');";
                                        }
                                    ?>
              <div class="inner_safety_wrap" id="TDnurse2NameId" style="display:<?php echo $TDnurseNameIdDisplayNurse2;?>;"> <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunNurse2;?>"> Nurse Signature </a> </div>
              <div class="inner_safety_wrap collapse" id="TDnurse2SignatureId" style="display:<?php echo $TDnurseSignatureIdDisplayNurse2;?>;"> <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDelNurse2;?>"> <?php echo $Nurse2NameShow;?> </a></span> <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatusNurse2;?></span> <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse2DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signDateTimeStatusNurse2; ?> <span class="fa fa-edit"></span></span></span> </div>
            </div>
          </Div>
        </div>
      </div>
    </div>
    <!-- Sign In Ends Here --> 
    <?php } ?>
    <!--- ----  Time Out Starts here -->
    
    <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
      <div class="panel panel-default bg_panel_or">
        <div class="panel-heading haed_p_clickable">
          <h3 class="panel-title rob"> TIME OUT </h3>
          <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i> </span> </div>
        <div class="panel-body " id="p_check_in">

          <!-- Start Fire Risk Assessment Guide -->
		  <?php
		  if($fire_risk_active_status == 'Yes') {
		  ?>
          
		  <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <div class="scanner_win new_s">
                <h4> <span>Fire Risk Assessment Guide</span> </h4>
              </div>
            </div>
            
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
            
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
			  
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r" id=""> Surgical Site Above Xiphoid (incision above waist)</label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($surgical_xiphoid) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ssx_yes','chbx_ssx'),changeChbxColor('chbx_ssx');" <?php if($surgical_xiphoid =='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ssx" id="chbx_ssx_yes">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($surgical_xiphoid) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ssx_no','chbx_ssx'),changeChbxColor('chbx_ssx');" <?php if($surgical_xiphoid=='No') echo "CHECKED"; ?> value="No" name="chbx_ssx" id="chbx_ssx_no">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($surgical_xiphoid) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ssx_na','chbx_ssx'),changeChbxColor('chbx_ssx');" <?php if(stripslashes($surgical_xiphoid)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_ssx" id="chbx_ssx_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <div class="col-xs-3 col-md-4 col-lg-3 ">
                    	<div class="row">
                      	<label class="date_r" id="">Fire Risk Score</label>
                     	</div>   
                   	</div>   
                    <Div class="col-xs-9 col-md-8 col-lg-9 ">
                      <div class="row">
                        <div class="col-xs-9" style=" <?php if($fire_risk_score) { echo $whiteBckGroundColor;}else { echo $chngBckGroundColor;}?>"> 
							<select name="fire_risk_score" id="fire_risk_score_id" class="selectpicker select-mandatory " onChange=" javascript:changeSelectpickerColor('.select-mandatory');">
								<option value="">Select</option>
								<option value="1" <?php if($fire_risk_score == '1') { echo "SELECTED";}?> >1(Low Risk)</option>
								<option value="2" <?php if($fire_risk_score == '2') { echo "SELECTED";}?> >2(Low Risk w/Potential to convert</option>
								<option value="3" <?php if($fire_risk_score == '3') { echo "SELECTED";}?> >3(High Risk)</option>
							</select>
                      	</div>
                      </div>
                  	</Div>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                 	</Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="clearfix margin_adjustment_only visible-sm visible-xs"></div>
           		<div class="clearfix border-dashed margin_adjustment_only visible-sm visible-xs"></div>
              
            </Div>
            
            
            
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
            
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r" id=""> Open Oxygen Source (nasal cannula, oxygen face mask)</label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($oxygen_source) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_oos_yes','chbx_oos'),changeChbxColor('chbx_oos');" <?php if($oxygen_source =='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_oos" id="chbx_oos_yes">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($oxygen_source) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_oos_no','chbx_oos'),changeChbxColor('chbx_oos');" <?php if($oxygen_source=='No') echo "CHECKED"; ?> value="No" name="chbx_oos" id="chbx_oos_no">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($oxygen_source) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_oos_na','chbx_oos'),changeChbxColor('chbx_oos');" <?php if(stripslashes($oxygen_source)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_oos" id="chbx_oos_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r" id=""> Available Ignition Source (cautery, laser, fiber optic light source)</label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($ignition_source) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ais_yes','chbx_ais'),changeChbxColor('chbx_ais');" <?php if($ignition_source =='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ais" id="chbx_ais_yes">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($ignition_source) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ais_no','chbx_ais'),changeChbxColor('chbx_ais');" <?php if($ignition_source=='No') echo "CHECKED"; ?> value="No" name="chbx_ais" id="chbx_ais_no">
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($ignition_source) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ais_na','chbx_ais'),changeChbxColor('chbx_ais');" <?php if(stripslashes($ignition_source)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_ais" id="chbx_ais_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
          	    
            </Div>
            
            
          </div>
		  <?php
		  }
		  ?>
          <!-- End Fire Risk Assessment Guide --> 
          
		  
		  <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <div class="scanner_win new_s">
                <h4> <span>Before Incision</span> </h4>
              </div>
            </div>
            <p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-5"> Initiated by designated team Member:</p>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
              <div class="inner_safety_wrap">
                <div class="row">
                  <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-3" for="n_select"> Relief Nurse / Anesthesia </label>
                  <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-9">
                    <select class="selectpicker" name="relivedNurse3IdList">
                      <option value="">Select</option>
                      <?php
													foreach($arr_users_nurse as $relivedSelectNurseID=>$relivedNurseName) {
														$sel="";
														if($reliefNurse3==$relivedSelectNurseID) {
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
            </div>
            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
              <div class="clearfix border-dashed margin_adjustment_only"></div>
            </Div>
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Introduction of team member </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($introducationTeamMember) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_itm_yes','chbx_itm'),changeChbxColor('chbx_itm')" <?php if($introducationTeamMember=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_itm" id="chbx_itm_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($introducationTeamMember) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_itm_no','chbx_itm'),changeChbxColor('chbx_itm')" <?php if($introducationTeamMember=='No') echo "CHECKED"; ?> value="No" name="chbx_itm" id="chbx_itm_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($introducationTeamMember) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_itm_na','chbx_itm'),changeChbxColor('chbx_itm')" <?php if(stripslashes($introducationTeamMember)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_itm" id="chbx_itm_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <!--   @2nd col starts    -->
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap visible-lg">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap visible-md">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <!--   @2nd col Ends     --> 
          </div>
          <p class="rob l_height_28"> All: </p>
          <div class="clearfix border-dashed margin_adjustment_only"></div>
          <Div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Confirmation of: identify, procedure, procedure site and consent(s) </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_coip_yes','chbx_coip'),changeChbxColor('chbx_coip')" <?php if($confirmIPPSC=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_coip" id="chbx_coip_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_coip_no','chbx_coip'),changeChbxColor('chbx_coip')" <?php if($confirmIPPSC=='No') echo "CHECKED"; ?> value="No" name="chbx_coip" id="chbx_coip_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($confirmIPPSC) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_coip_na','chbx_coip'),changeChbxColor('chbx_coip')" <?php if(stripslashes($confirmIPPSC)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_coip" id="chbx_coip_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Relevant Images properly labeled and displayed </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($relevantImages) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_rip_yes','chbx_rip'),changeChbxColor('chbx_rip')" <?php if($relevantImages=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_rip" id="chbx_rip_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($relevantImages) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_rip_no','chbx_rip'),changeChbxColor('chbx_rip')" <?php if($relevantImages=='No') echo "CHECKED"; ?> value="No" name="chbx_rip" id="chbx_rip_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($relevantImages) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_rip_na','chbx_rip'),changeChbxColor('chbx_rip')" <?php if(stripslashes($relevantImages)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_rip" id="chbx_rip_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Site is marked and visible </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarkedAndVisible) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smv_yes','chbx_smv'),changeChbxColor('chbx_smv')" <?php if($siteMarkedAndVisible=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_smv" id="chbx_smv_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarkedAndVisible) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smv_no','chbx_smv'),changeChbxColor('chbx_smv')" <?php if($siteMarkedAndVisible=='No') echo "CHECKED"; ?> value="No" name="chbx_smv" id="chbx_smv_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($siteMarkedAndVisible) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_smv_na','chbx_smv'),changeChbxColor('chbx_smv')" <?php if(stripslashes($siteMarkedAndVisible)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_smv" id="chbx_smv_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Any equipment concern?(Yes/No) </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anyEquipmentConcern) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ec_yes','chbx_ec'),changeChbxColor('chbx_ec')" <?php if($anyEquipmentConcern=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_ec" id="chbx_ec_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anyEquipmentConcern) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ec_no','chbx_ec'),changeChbxColor('chbx_ec')" <?php if($anyEquipmentConcern=='No') echo "CHECKED"; ?> value="No" name="chbx_ec" id="chbx_ec_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anyEquipmentConcern) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_ec_na','chbx_ec'),changeChbxColor('chbx_ec')" <?php if(stripslashes($anyEquipmentConcern)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_ec" id="chbx_ec_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
          </Div>
          <div class="scanner_win new_s">
            <h4> <span>Anticipated Critical Events</span> </h4>
          </div>
          <?php 
                         if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
          <p class="rob l_height_28"> Surgeon: </p>
          <p class="rob l_height_28"> States The Following: </p>
          <div class="clearfix border-dashed margin_adjustment_only"></div>
          <?php
						 }
						?>
          <Div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Critical or nonroutine steps </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($criticalStep) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_cns_yes','chbx_cns'),changeChbxColor('chbx_cns')" <?php if($criticalStep=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_cns" id="chbx_cns_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($criticalStep) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_cns_no','chbx_cns'),changeChbxColor('chbx_cns')" <?php if($criticalStep=='No') echo "CHECKED"; ?> value="No" name="chbx_cns" id="chbx_cns_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($criticalStep) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_cns_na','chbx_cns'),changeChbxColor('chbx_cns')" <?php if(stripslashes($criticalStep)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_cns" id="chbx_cns_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Anticipated blood loss: </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anticipatedBloodLoss) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_abl_yes','chbx_abl'),changeChbxColor('chbx_abl')" <?php if($anticipatedBloodLoss=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_abl" id="chbx_abl_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anticipatedBloodLoss) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_abl_no','chbx_abl'),changeChbxColor('chbx_abl')" <?php if($anticipatedBloodLoss=='No') echo "CHECKED"; ?> value="No" name="chbx_abl" id="chbx_abl_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anticipatedBloodLoss) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_abl_na','chbx_abl'),changeChbxColor('chbx_abl')" <?php if(stripslashes($anticipatedBloodLoss)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_abl" id="chbx_abl_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> case duration: </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($caseDuration) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_cd_yes','chbx_cd'),changeChbxColor('chbx_cd')" <?php if($caseDuration=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_cd" id="chbx_cd_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($caseDuration) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_cd_no','chbx_cd'),changeChbxColor('chbx_cd')" <?php if($caseDuration=='No') echo "CHECKED"; ?> value="No" name="chbx_cd" id="chbx_cd_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($caseDuration) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_cd_na','chbx_cd'),changeChbxColor('chbx_cd')" <?php if(stripslashes($caseDuration)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_cd" id="chbx_cd_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
          </Div>
          <Div class="clearfix"></Div>
          <p class="rob l_height_28"> Anesthesia provider: </p>
          <div class="clearfix border-dashed margin_adjustment_only"></div>
          <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Antibiotic prophylaxis within one hour before incision </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($antibioticProphylaxis) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_api_yes','chbx_api'),changeChbxColor('chbx_api')" <?php if($antibioticProphylaxis=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_api" id="chbx_api_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($antibioticProphylaxis) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_api_no','chbx_api'),changeChbxColor('chbx_api')" <?php if($antibioticProphylaxis=='No') echo "CHECKED"; ?> value="No" name="chbx_api" id="chbx_api_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($antibioticProphylaxis) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_api_na','chbx_api'),changeChbxColor('chbx_api')" <?php if(stripslashes($antibioticProphylaxis)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_api" id="chbx_api_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Additional concerns? </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anesthesiaAdditionalConcerns) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adicon_yes','chbx_adicon'),changeChbxColor('chbx_adicon')" <?php if($anesthesiaAdditionalConcerns=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_adicon" id="chbx_adicon_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anesthesiaAdditionalConcerns) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adicon_no','chbx_adicon'),changeChbxColor('chbx_adicon')" <?php if($anesthesiaAdditionalConcerns=='No') echo "CHECKED"; ?> value="No" name="chbx_adicon" id="chbx_adicon_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anesthesiaAdditionalConcerns) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adicon_na','chbx_adicon'),changeChbxColor('chbx_adicon')" <?php if(stripslashes($anesthesiaAdditionalConcerns)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_adicon" id="chbx_adicon_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
          </Div>
          <Div class="clearfix"></Div>
          <p class="rob l_height_28"> Scrub and circulating nurse: </p>
          <div class="clearfix border-dashed margin_adjustment_only"></div>
          <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Sterilization Class 5 indicators have been confirmed </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($sterilizationIndicators) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_sinc_yes','chbx_sinc'),changeChbxColor('chbx_sinc')" <?php if($sterilizationIndicators=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_sinc" id="chbx_sinc_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($sterilizationIndicators) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_sinc_no','chbx_sinc'),changeChbxColor('chbx_sinc')" <?php if($sterilizationIndicators=='No') echo "CHECKED"; ?> value="No" name="chbx_sinc" id="chbx_sinc_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($sterilizationIndicators) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_sinc_na','chbx_sinc'),changeChbxColor('chbx_sinc')" <?php if(stripslashes($sterilizationIndicators)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_sinc" id="chbx_sinc_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <?php 
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Additional concerns? </label>
                  </div>
                  <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($nurseAdditionalConcerns) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adcn_yes','chbx_adcn'),changeChbxColor('chbx_adcn')" <?php if($nurseAdditionalConcerns=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_adcn" id="chbx_adcn_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($nurseAdditionalConcerns) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adcn_no','chbx_adcn'),changeChbxColor('chbx_adcn')" <?php if($nurseAdditionalConcerns=='No') echo "CHECKED"; ?> value="No" name="chbx_adcn" id="chbx_adcn_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($nurseAdditionalConcerns) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_adcn_na','chbx_adcn'),changeChbxColor('chbx_adcn')" <?php if(stripslashes($nurseAdditionalConcerns)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_adcn" id="chbx_adcn_na">
                        </span> </div>
                    </div>
                  </div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <?php
                                        $signOnFileStatusNurse3 = "Yes";
                                        $TDnurseNameIdDisplayNurse3 = "block";
                                        $TDnurseSignatureIdDisplayNurse3 = "none";
                                        $Nurse3NameShow = $loggedInUserName;
                                        if($signNurse3Id<>0 && $signNurse3Id<>"") {
                                            $Nurse3NameShow = $signNurse3LastName.", ".$signNurse3FirstName." ".$signNurse3MiddleName;
                                            $signOnFileStatusNurse3 = $signNurse3FileStatus;	
                                            //$signDateTimeStatusNurse3 = date("m-d-Y h:i A",strtotime($signNurse3DateTime));
                                            $signDateTimeStatusNurse3 = $objManageData->getFullDtTmFormat($signNurse3DateTime);
											$TDnurseNameIdDisplayNurse3 = "none";
                                            $TDnurseSignatureIdDisplayNurse3 = "block";
                                        }
                                        if($_SESSION["loginUserId"]==$signNurse3Id) {
                                            $callJavaFunDelNurse3 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse3NameId'; return displaySignature('TDnurse3NameId','TDnurse3SignatureId','check_list_ajax_sign.php','$loginUserId','nurse3','delSign3');";
                                        }else {
                                            $callJavaFunDelNurse3 = "alert('Only $Nurse3NameShow can remove this signature');";
                                        }
                                    ?>
              <div class="inner_safety_wrap" id="TDnurse3NameId" style="display:<?php echo $TDnurseNameIdDisplayNurse3;?>;"> <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunNurse3;?>"> Nurse Signature </a> </div>
              <div class="inner_safety_wrap collapse" id="TDnurse3SignatureId" style="display:<?php echo $TDnurseSignatureIdDisplayNurse3;?>;"> <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDelNurse3;?>"> <?php echo $Nurse3NameShow;?> </a></span> <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatusNurse3;?></span> <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse3DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signDateTimeStatusNurse3; ?> <span class="fa fa-edit"></span></span></span> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--- - -- --- Time Out Ends Here --> 
    
    <!--- ----  Sign Out Starts here -->
    
    <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
      <div class="panel panel-default bg_panel_or">
        <div class="panel-heading haed_p_clickable">
          <h3 class="panel-title rob"> SIGN OUT </h3>
          <span class="clickable"><i class="glyphicon glyphicon-chevron-up"></i> </span> </div>
        <div class="panel-body " id="p_check_in">
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <div class="scanner_win new_s">
                <h4> <span>Before the Patient Leaves the operating Room</span> </h4>
              </div>
            </div>
            <p class="rob l_height_28 col-md-12 col-sm-12 col-xs-12 col-lg-5"> Nurse confirms: </p>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
              <div class="inner_safety_wrap">
                <div class="row">
                  <label class="rob col-md-12 col-sm-12 col-xs-12 col-lg-3" for="n_select"> Relief Nurse / Anesthesia </label>
                  <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-9">
                    <select name="relivedNurse4IdList" class="selectpicker" >
                      <option value="">Select</option>
                      <?php
													foreach($arr_users_nurse as $relivedSelectNurseID=>$relivedNurseName) {
														$sel="";
														if($reliefNurse4==$relivedSelectNurseID) {
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
            </div>
            <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 clearfix">
              <div class="clearfix border-dashed margin_adjustment_only"></div>
            </Div>
            <?php 
                                 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Name of operative procedure Completion of sponge, sharp and instrument counts </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($nameOperativeProcedure) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_nops_yes','chbx_nops'),changeChbxColor('chbx_nops')" <?php if($nameOperativeProcedure=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_nops" id="chbx_nops_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($nameOperativeProcedure) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_nops_no','chbx_nops'),changeChbxColor('chbx_nops')" <?php if($nameOperativeProcedure=='No') echo "CHECKED"; ?> value="No" name="chbx_nops" id="chbx_nops_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($nameOperativeProcedure) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_nops_na','chbx_nops'),changeChbxColor('chbx_nops')" <?php if(stripslashes($nameOperativeProcedure)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_nops" id="chbx_nops_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <?php
									 }
									 ?>
            </Div>
            <?php
                                 }
                                 ?>
            <!--   @2nd col starts    -->
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap visible-lg">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap visible-md">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> </label>
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
                        <label class="li_check"> N/A </label>
                      </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Specimens identified and labeled </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($specimensIdentified) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_sil_yes','chbx_sil'),changeChbxColor('chbx_sil')" <?php if($specimensIdentified=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_sil" id="chbx_sil_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($specimensIdentified) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_sil_no','chbx_sil'),changeChbxColor('chbx_sil')" <?php if($specimensIdentified=='No') echo "CHECKED"; ?> value="No" name="chbx_sil" id="chbx_sil_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($specimensIdentified) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_sil_na','chbx_sil'),changeChbxColor('chbx_sil')" <?php if(stripslashes($specimensIdentified)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_sil" id="chbx_sil_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <?php 
                                 if(trim($checklist_old_new)=='old') { //Removed this row for which are yet to save - 25 May, 2016 ?>
            <div class="clearfix"></div>
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                    <label class="date_r"> Any equipment problem to be addressed </label>
                  </Div>
                  <Div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center" >
                    <div class="">
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anyEquipmentProblem) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_epa_yes','chbx_epa'),changeChbxColor('chbx_epa')" <?php if($anyEquipmentProblem=='Yes') echo "CHECKED"; ?> value="Yes" name="chbx_epa" id="chbx_epa_yes" >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anyEquipmentProblem) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_epa_no','chbx_epa'),changeChbxColor('chbx_epa')" <?php if($anyEquipmentProblem=='No') echo "CHECKED"; ?> value="No" name="chbx_epa" id="chbx_epa_no"  >
                        </span> </div>
                      <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4"> <span class="colorChkBx" style=" <?php if($anyEquipmentProblem) { echo $whiteBckGroundColor;}?>" >
                        <input type="checkbox" onClick="javascript:checkSingle('chbx_epa_na','chbx_epa'),changeChbxColor('chbx_epa')" <?php if(stripslashes($anyEquipmentProblem)=='N/A') echo "CHECKED"; ?> value="N/A" name="chbx_epa" id="chbx_epa_na">
                        </span> </div>
                    </div>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            <?php
									 }
									 ?>
            <div class="clearfix"></div>
            <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
              <div class="inner_safety_wrap">
                <div class="row">
                  <Div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                    <label class="date_r"> Comments </label>
                  </Div>
                  <Div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center" >
                    <textarea class="form-control" style="resize:none;" name="comments" id="comments"  style="  border-color:<?php echo $bgmid_orange_physician;?>;" ><?php if($comments){echo stripslashes($comments); } ?></textarea>
                  </Div>
                  <!-- Col-3 ends  --> 
                </div>
              </div>
            </Div>
            
            <!--   @2nd col Ends     --> 
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
              <?php
                                    $signOnFileStatusNurse4 = "Yes";
                                    $TDnurseNameIdDisplayNurse4 = "block";
                                    $TDnurseSignatureIdDisplayNurse4 = "none";
                                    $Nurse4NameShow = $loggedInUserName;
                                    if($signNurse4Id<>0 && $signNurse4Id<>"") {
                                        $Nurse4NameShow = $signNurse4LastName.", ".$signNurse4FirstName." ".$signNurse4MiddleName;
                                        $signOnFileStatusNurse4 = $signNurse4FileStatus;	
                                        //$signDateTimeStatusNurse4 = date("m-d-Y h:i A",strtotime($signNurse4DateTime));
                                        $signDateTimeStatusNurse4 = $objManageData->getFullDtTmFormat($signNurse4DateTime);
										$TDnurseNameIdDisplayNurse4 = "none";
                                        $TDnurseSignatureIdDisplayNurse4 = "block";
                                    }
                                    if($_SESSION["loginUserId"]==$signNurse4Id) {
                                        $callJavaFunDelNurse4 = "document.frm_surgical_check_list.hiddSignatureId.value='TDnurse4NameId'; return displaySignature('TDnurse4NameId','TDnurse4SignatureId','check_list_ajax_sign.php','$loginUserId','nurse4','delSign4');";
                                    }else {
                                        $callJavaFunDelNurse4 = "alert('Only $Nurse4NameShow can remove this signature');";
                                    }
                                ?>
              <div class="inner_safety_wrap" id="TDnurse4NameId" style="display:<?php echo $TDnurseNameIdDisplayNurse4;?>;"> <a href="javascript:void(0);" class="sign_link" style="cursor:pointer;<?php echo $chngBckGroundColor?>;" onClick="javascript:<?php echo $callJavaFunNurse4;?>"> Nurse Signature </a> </div>
              <div class="inner_safety_wrap collapse" id="TDnurse4SignatureId" style="display:<?php echo $TDnurseSignatureIdDisplayNurse4;?>;"> <span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDelNurse4;?>"> <?php echo $Nurse4NameShow;?> </a></span> <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatusNurse4;?></span> <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurse4DateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signDateTimeStatusNurse4; ?> <span class="fa fa-edit"></span></span></span> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--- - -- --   Sign -Out Ends Here --> 
    
  </div>
</form>
<form name="frm_return_BlankMainForm" id="frm_return_BlankMainForm" method="post" action="check_list.php?cancelRecord=true">
  <input type="hidden" name="patient_id"	 value="<?php echo $patient_id; ?>">
  <input type="hidden" name="pConfId"		 value="<?php echo $pConfId; ?>">
  <input type="hidden" name="ascId"		 value="<?php echo $ascId; ?>">
  <input type="hidden" name="innerKey"	 value="<?php echo $innerKey; ?>">
  <input type="hidden" name="preColor"	 value="<?php echo $preColor; ?>">
  <input type="hidden" name="pConfId"		 value="<?php echo $pConfId; ?>">
  <input type="hidden" name="thisId"		 value="<?php echo $thisId; ?>">
</form>
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "check_list.php";
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
		var primary_procedure_cat="<?php echo $patient_laser_consent_categoryID;?>";
		if(primary_procedure_cat==2){

			top.document.getElementById('header_BP').innerText=document.getElementById('bp_temp5').value;
			top.document.getElementById('header_P').innerText=document.getElementById('bp_temp6').value;
			top.document.getElementById('header_R').innerText=document.getElementById('bp_temp7').value;
			//top.document.getElementById('header_O2SAT').innerText=document.getElementById('O2SAT').value;
			//top.document.getElementById('header_Temp').innerText=document.getElementById('temp').value;
			
		//SET BP, P, R, TEMP VALUES IN HEADER
		}
		
		
	</script>
<?php 
		include("pre_op_meds_div.php");
		include_once("print_page.php");
	?>
<script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body></html>