<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$patient_id = $_REQUEST['patient_id'];
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$pConfId = $_REQUEST['pConfId'];
//if(!$pConfId) {$pConfId = $_SESSION['pConfId'];  }
if(!$patient_id) {$patient_id = $_SESSION['patient_id'];  }
require_once("common/user_agent.php");
$tablename = "iolink_preophealthquestionnaire";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script src="js/jscript.js" ></script>
<script>
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
}
function OnSignWtHealth() {
	if(document.getElementById("SigPlusPreHlthPtSign")) {
		document.getElementById("SigPlusPreHlthPtSign").TabletState = 0;
	}
	document.getElementById("SigPlusPreHlthWtSign").TabletState = 1;
	
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
		document.getElementById('trHepatitis').style.display = 'block';
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
		document.getElementById('hivTr').style.display = 'block';
	}else{
		document.getElementById('hivTr').style.display = 'none';
	}
}
function showHidebrestCancerTr(){
	var displayStatus = document.getElementById('chbx_hist_can_yes').checked;	
	if(displayStatus == true){
		document.getElementById('brestCancerTr').style.display = 'block';
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

function displaySignatureNew(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign,TDUserSignatureDate) {
	var usrSigDt = '<?php echo date("m-d-Y h:i A");?>';
	
	//START CODE TO SAVE WITNESS SIGNATURE ON HITTING SAVE
	if(document.getElementById('hiddSignatureId').value == 'TDwitness1SignatureId') {
		document.getElementById('hiddWitSignatureId').value = loggedInUserId;
		document.getElementById('hiddWitSignClick').value = 'yes';
		
	}else if(document.getElementById('hiddSignatureId').value == 'TDwitness1NameId') {
		document.getElementById('hiddWitSignatureId').value = '';
		document.getElementById('hiddWitSignClick').value = 'yes';
	}
	//END CODE TO SAVE WITNESS SIGNATURE ON HITTING SAVE
	
	
	if(delSign) {
		document.getElementById(TDUserNameId).style.display = 'block';
		document.getElementById(TDUserSignatureId).style.display = 'none';
	}else {
		document.getElementById(TDUserNameId).style.display = 'none';
		document.getElementById(TDUserSignatureId).style.display = 'block';
	}
	
	if(TDUserSignatureDate) {
		if(document.getElementById(TDUserSignatureDate)) {
			document.getElementById(TDUserSignatureDate).innerHTML = '<b>Signature Date : </b>'+usrSigDt;	
		}
	}
		
}
function displaySignature(TDUserNameId,TDUserSignatureId,pagename,loggedInUserId,userIdentity,delSign) {

	if(delSign) {
		document.getElementById(TDUserNameId).style.display = 'block';
		document.getElementById(TDUserSignatureId).style.display = 'none';
	}else {
		document.getElementById(TDUserNameId).style.display = 'none';
		document.getElementById(TDUserSignatureId).style.display = 'block';
	}
	
	//SIGN ON FILE FIELD
	if(document.getElementById('TDnurseSignatureOnFileId')) {
		if(delSign) {
			document.getElementById('TDnurseSignatureOnFileId').style.display = 'none';
		}else {
			document.getElementById('TDnurseSignatureOnFileId').style.display = 'block';
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
	var patient_in_waiting_id1 = '<?php echo $_REQUEST["patient_in_waiting_id"];?>';

	
	//var url="pre_op_nursing_record_ajaxSign.php";
	var url=pagename
	url=url+"?loggedInUserId="+loggedInUserId
	url=url+"&userIdentity="+userIdentity
	url=url+"&thisId="+thisId1
	url=url+"&innerKey="+innerKey1
	url=url+"&preColor="+preColor1
	url=url+"&patient_id="+patient_id1
	url=url+"&pConfId="+pConfId1
	url=url+"&patient_in_waiting_id="+patient_in_waiting_id1
	//url=url+"&ascId="+ascId1
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
			top.frames[0].setPNotesHeight();
		}
}

//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

function openPreDefineAllergyFun(left, top) {
	document.getElementById('iolinkPreDefineAllergyAdminDiv').style.display='inline-block';
	document.getElementById('iolinkPreDefineAllergyAdminDiv').style.top=top+"px";
	document.getElementById('iolinkPreDefineAllergyAdminDiv').style.left=left+"px"; 

	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)

}
function openPreDefineMedicationFun(left, top) {
	document.getElementById('iolinkPreDefineMedAdminDiv').style.display='inline-block';
	document.getElementById('iolinkPreDefineMedAdminDiv').style.top=top+"px";
	document.getElementById('iolinkPreDefineMedAdminDiv').style.left=left+"px"; 

	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			if(document.getElementById("hiddPreDefineId").value == "") {
				preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
			}
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)

}	
function clearMedVal(obj)
{
	if(obj.checked==true)
	{
		//clear	all values
		$('.noMedOn').val('');
		$('.noMedOn').attr('disabled',true);
		$('.noMedOn').css("background-color", "#F0F0F0");
		
		$('#iolink_no_medication_comments').attr('disabled',false);
		$('#iolink_no_medication_comments').css("background-color", "");
	}	
	else 
	{
		$('.noMedOn').attr('disabled',false);
		$('.noMedOn').css("background-color", "");
		
		$('#iolink_no_medication_comments').val('');
		$('#iolink_no_medication_comments').attr('disabled',true);
		$('#iolink_no_medication_comments').css("background-color", "#F0F0F0");
		
	}
}

$(document).ready(function(e) {
    var objMed = document.getElementById('iolink_no_medication_status');
	clearMedVal(objMed);
});
</script>
<script src="js/epost.js"></script>
<?php
$spec= "
</head>
<body onClick=\"document.getElementById('divSaveAlert').style.display = 'none'; closeEpost();\">";
include("common/link_new_file.php");
include_once("common/commonFunctions.php");
include("common/iOLinkCommonFunction.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include("iolinkPreDefineAllergy.php");
include("iolinkPreDefineMed.php");
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
$uid=$_SESSION['iolink_loginUserId'];
$usertypeqry=imw_query("select * from users where usersId='".$uid."'");
$recordId=imw_fetch_array($usertypeqry);
 $usertype=$recordId['user_type'];
 $nurse=$recordId['fname']." ".$recordId['lname'];


$ascId = $_SESSION['ascId'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$preOpHealthQuesId = $_REQUEST['preOpHealthQuesId'];
$cancelRecord = $_REQUEST['cancelRecord'];
$nurse=$_REQUEST['nurse'];


$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId.'&amp;patient_in_waiting_id='.$patient_in_waiting_id.'&amp;ascId='.$ascId.'&amp;fieldName='.$fieldName;

	//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
//	END DONE BY MAMTA
//adminHealthquestionare
	$selectAdminQuestionsQry="select * from healthquestioner order by question";
	$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
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

if($_REQUEST['saveRecord']=='true'){
//updation  in iolink_healthquestionadmin
  
	$selectQry=imw_query("select * from iolink_healthquestionadmin where
							patient_in_waiting_id='$patient_in_waiting_id'");							
	 $count=imw_num_rows($selectQry);						 
	if($count>0){
		$k=0;
		while($selectRes=imw_fetch_array($selectQry)){
			$qustatus=$selectRes['adminQuestionStatus'];
		    $question[]=$selectRes['adminQuestion'];
		}	
		for($key=0;$key<$count;$key++){
		$questionsDes[] = $_REQUEST['questionDes'.$key];
		$adminQuestionArray[]=$_REQUEST['quest'.$key];
		$adminQuestDescArr[] = ($_REQUEST['quest'.$key] !="Yes" ? "" : $_REQUEST['adminQuestDesc'.$key]);
		$updateQry=imw_query("update iolink_healthquestionadmin set
								adminQuestionStatus='".$adminQuestionArray[$key]."',
								adminQuestionDesc='".imw_real_escape_string($adminQuestDescArr[$key])."'
								where
								adminQuestion='".imw_real_escape_string($questionsDes[$key])."' and
								patient_in_waiting_id='$patient_in_waiting_id' and
								patient_id= '$patient_id'");	
											
		}
	}else{
		//insertion in iolink_healthquestionadmin

		$questionsDesc[] = $_REQUEST['questionDesc'];
		$selectAdminQuestionsQry="select * from healthquestioner order by question";
			$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
			$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
			$i=0;
			while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
			{
				$quId[]=$ResultselectAdminQuestions['question'];
			}
		for($key=0;$key<$selectAdminQuestionsRows;$key++)
		{
			$adminQuestionArray[]=$_REQUEST['question'.$key];
			$adminQuestDescArr[] = ($_REQUEST['question'.$key] !="Yes" ? "" : $_REQUEST['adminQuestionDesc'.$key]);
			
			$insertQuestionQry=imw_query("insert into iolink_healthquestionadmin set 
									adminQuestion='".imw_real_escape_string($quId[$key])."',
									adminQuestionStatus='".$adminQuestionArray[$key]."',
									adminQuestionDesc='".imw_real_escape_string($adminQuestDescArr[$key])."',
									patient_in_waiting_id='$patient_in_waiting_id', 
									patient_id= '$patient_id'
									");
		}					
		//End insertion in iolink_healthquestionadmin 

	}

//End updation  in iolink_healthquestionadmin
	$text = $_REQUEST['getText'];
	$tablename = "iolink_preophealthquestionnaire";
	
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['patient_in_waiting_id'] = $patient_in_waiting_id;
	$arrayRecord['heartTrouble'] = addslashes($_POST['chbx_ht']);
	$arrayRecord['heartTroubleDesc']= addslashes($_POST['heartTroubleDesc']);
//START CODE TO CHECK NURSE SIGN IN DATABASE
	$chkNurseSignDetails = $objManageData->getRowRecord('iolink_preophealthquestionnaire', 'patient_in_waiting_id', $patient_in_waiting_id);
	if($chkNurseSignDetails) {
		$chk_signNurseId = $chkNurseSignDetails->signNurseId;
		//CHECK FORM STATUS
		$chk_form_status = $chkNurseSignDetails->form_status;
		//CHECK FORM STATUS
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

if(($_POST['chbx_ht']!='') && ($_POST['chbx_sht']!='') 
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
		$postSigData='';
		$ptWtHealthSrc='';
		$postSigDataPtImgSavePath='';
		$postSigDataWtImgSavePath='';
		
		if($ps==1) { 
			$postSigData = $_POST['SigDataPt']; 
			$postSigDataLoadValue=$_POST['SigDataPtLoadValue'];
		}else if($ps==2) { 
			$postSigData = $_POST['SigDataWt'];
			$postSigDataLoadValue=$_POST['SigDataWtLoadValue']; 
		}
		
		/*if( (!$postSigData  || $postSigData=='undefined' )  ) { 
			if(!$postSigDataLoadValue  && !$_REQUEST['hidden_patient_sign_image_path'] && $ps==1) { 
				$formStatus='not completed';
			}
		}*/
		
		if($postSigData != '' && $postSigData != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postSigData !='undefined'){	
			$ptWtHealthSrc = 'iolinkSignHealth_'.$_REQUEST["patient_in_waiting_id"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
			$path =  realpath(dirname(__FILE__).'/SigPlus_images/').'/iolinkSignHealth_'.$_REQUEST["patient_in_waiting_id"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
			//$pathPDF =  realpath(dirname(__FILE__).'/html2pdfnew/').'/iolinkSignHealth_'.$_REQUEST["patient_in_waiting_id"].'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
			if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
				$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
				$aConn->InitSigPlus();
				$aConn->SigCompressionMode = 2;
				$aConn->SigString=$postSigData;
				$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
				//$aConn->ImageXSize = 500; //width of resuting image in pixels
				//$aConn->ImageYSize =165; //height of resulting image in pixels
				$aConn->ImageXSize = 150; //width of resuting image in pixels
				$aConn->ImageYSize =65; //height of resulting image in pixels
				
				$aConn->ImagePenWidth = 11; //thickness of ink in pixels
				$aConn->JustifyMode = 5;  //center and fit signature to size
				$aConn->WriteImageFile("$path");
			}else {
				//$objManageData->getSigImage($postSigData,$pathPDF,$rootServerPath);
				$objManageData->getSigImage($postSigData,$path,$rootServerPath);//TO STORE DATA IN SIGPLUS
			}
			
			if($ps==1) { 
				
				$postSigDataPtImgSavePath = 'SigPlus_images/'.$ptWtHealthSrc; 
				$arrayRecord['patient_sign_image_path'] = trim(addslashes($postSigDataPtImgSavePath));
				$arrayRecord['patientSign'] = addslashes($postSigData);
			}else if($ps==2) { 
				
				$postSigDataWtImgSavePath = 'SigPlus_images/'.$ptWtHealthSrc; 
				//$arrayRecord['witness_sign_image_path'] = trim(addslashes($postSigDataWtImgSavePath));
				//$arrayRecord['witnessSign'] = addslashes($postSigData);
			}
		}
	}		
	//END SAVE SIGNATURE
	
	if(trim($_REQUEST['hidden_patient_sign_image_path'])) {
		$arrayRecord['patient_sign_image_path'] = trim(addslashes($_REQUEST['hidden_patient_sign_image_path']));		
	}
	
	if( !$arrayRecord['patient_sign_image_path'] ) {
		$formStatus='not completed';	
	}
	
	if($_POST['hiddWitSignClick']=='yes') {
		$witUserId				= '';
		$witUserFirstName		= '';
		$witUserMiddleName		= '';
		$witUserLastName		= '';
		$witSignOnFileStatus	= '';
		$witSignDateTime		= '';
		if(trim($_POST['hiddWitSignatureId'])) {
			$witUserId		 	= trim($_POST['hiddWitSignatureId']);
			$witUserFirstName	= $recordId['fname'];
			$witUserMiddleName	= $recordId['mname'];
			$witUserLastName 	= $recordId['lname'];
			$witSignOnFileStatus= 'Yes';
			$witSignDateTime 	= date("Y-m-d H:i:s");
		}
		$arrayRecord['signWitness1Id']		 	= $witUserId;
		$arrayRecord['signWitness1FirstName']	= $witUserFirstName;
		$arrayRecord['signWitness1MiddleName']	= $witUserMiddleName;
		$arrayRecord['signWitness1LastName'] 	= $witUserLastName;
		$arrayRecord['signWitness1Status'] 	 	= $witSignOnFileStatus;
		$arrayRecord['signWitness1DateTime'] 	= $witSignDateTime;

	}
	if($_POST['chbx_smoke'] != 'Yes') {
		$_POST['smokeAdvise'] = '';
	}
	if($_POST['chbx_drink'] != 'Yes') {
		$_POST['alchoholAdvise'] = '';
	}
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
	$arrayRecord['brestCancerLeft'] 			= addslashes($_POST['brestCancerLeft']);	
	$arrayRecord['cancerHistoryDesc'] 			= addslashes($_POST['cancerHistory']);	
	$arrayRecord['organTransplant'] 			= $_POST['chbx_org_trns'];
	$arrayRecord['organTransplantDesc'] 		= addslashes($_POST['organTransDesc']);	
	$arrayRecord['anesthesiaBadReaction'] 		= $_POST['chbx_bad_react'];
	$arrayRecord['anesthesiaBadReactionDesc'] 	= addslashes($_POST['anesthesiaBadReactionDesc']);
	$arrayRecord['otherTroubles'] 				= addslashes($_POST['otherTroubles']);	
	//$arrayRecord['allergies_status'] 			= $_POST['chbx_drug_react'];// now nkda status will read from patient_in_waiting table
	$arrayRecord['allergies_status_reviewed'] 	= $_POST['chbx_drug_react_reviewed'];
	$arrayRecord['walker'] 						= $_POST['chbx_use_wheel'];
	$arrayRecord['walkerDesc'] 					= addslashes($_POST['walkerDesc']);
	$arrayRecord['contactLenses'] 				= $_POST['chbx_wear_cont'];
	$arrayRecord['contactLensesDesc'] 			= addslashes($_POST['contactLensesDesc']);
	$arrayRecord['smoke'] 						= $_POST['chbx_smoke'];
	$arrayRecord['smokeHowMuch'] 				= addslashes($_POST['smokeHowMuch']);
	$arrayRecord['smokeAdvise'] 				= addslashes($_POST['smokeAdvise']);
	$arrayRecord['alchohol'] 					= $_POST['chbx_drink'];
	$arrayRecord['alchoholHowMuch'] 			= addslashes($_POST['alchoholHowMuch']);
	$arrayRecord['alchoholAdvise'] 				= addslashes($_POST['alchoholAdvise']);
	$arrayRecord['autoInternalDefibrillator'] 	= $_POST['chbx_hav_auto_int'];
	$arrayRecord['autoInternalDefibrillatorDesc']= addslashes($_POST['autoInternalDefibrillatorDesc']);
	$arrayRecord['metalProsthetics'] 			= $_POST['chbx_hav_any_met'];
	$arrayRecord['notes'] 						= addslashes($_POST['notesDesc']);	
	
	//$arrayRecord['patientSign'] = $elem_signature;
	
	//$arrayRecord['patient_sign_image_path'] = addslashes($postSigDataPtImgSavePath);
	//$arrayRecord['witness_sign_image_path'] = addslashes($postSigDataWtImgSavePath);

	$arrayRecord['nursefield'] = addslashes($_REQUEST['nurse']);
	$dateQuest = addslashes($_POST['date']);
	$dateQuest = $objManageData->changeDateYMD($dateQuest);
	$arrayRecord['dateQuestionnaire'] = $dateQuest;
	$arrayRecord['timeQuestionnaire'] = time();
	$arrayRecord['emergencyContactPerson'] = addslashes($_POST['emergencyContactPerson']);
	$arrayRecord['witnessname']=addslashes($_POST['witnessname']);
	
	//$arrayRecord['witnessSign']=$witnSign;
	$arrayRecord['emergencyContactPhone'] = addslashes($_POST['emergencyContactTel']);
	$arrayRecord['progressNotes'] = $_POST[''];
	$arrayRecord['nurseId'] = $_SESSION['iolink_loginUserId'];
	$arrayRecord['form_status'] = $formStatus;
	if($preOpHealthQuesId){		
		$objManageData->updateRecords($arrayRecord, 'iolink_preophealthquestionnaire', 'preOpHealthQuesId', $preOpHealthQuesId);
	}else{
		$preOpHealthQuesId = $objManageData->addRecords($arrayRecord, 'iolink_preophealthquestionnaire');
	}
	
	//CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 
	
	//delete allregy when save button clicked
	 if($_POST['chbx_drug_react']=='Yes') {
		 imw_query("delete from iolink_patient_allergy where patient_in_waiting_id = '$patient_in_waiting_id'");
	 }

	 //START SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
	 unset($arrayNoMedData);
	 $arrayNoMedData['iolink_allergiesNKDA_status'] 		= addslashes($_REQUEST["chbx_drug_react"]);
	 $arrayNoMedData['iolink_no_medication_status'] 		= addslashes($_REQUEST["iolink_no_medication_status"]);
	 $arrayNoMedData['iolink_no_medication_comments'] 		= addslashes($_REQUEST["iolink_no_medication_comments"]);
	 $objManageData->updateRecords($arrayNoMedData, "patient_in_waiting_tbl", "patient_in_waiting_id", $patient_in_waiting_id);
	 //END SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
	 
	 /*
	 $updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '".$_POST['chbx_drug_react']."' where patient_in_waiting_id = '$patient_in_waiting_id'";
	 $updateNKDAstatusRes = imw_query($updateNKDAstatusQry);
	 */
	 setReSyncroStatus($patient_in_waiting_id,'preOpHealth');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
	//end delete when save button clicked
}
$blockImg = "images/block.gif";
$noneImg = "images/none.gif";

?>


<div id="post" style="display:none;"></div>
<?php 
// GETTING CONFIRMATION DETAILS
	/*
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;	
	*/
// GETTING CONFIRMATION DETAILS
	
	//START GET ALLERGY NKDA STATUS
	if($patient_in_waiting_id) {
		$ptWaitQry = "SELECT iolink_allergiesNKDA_status,iolink_no_medication_status,iolink_no_medication_comments FROM patient_in_waiting_tbl WHERE patient_in_waiting_id = '".$patient_in_waiting_id."' LIMIT 0,1";
		$ptWaitRes = imw_query($ptWaitQry) or die($ptWaitQry.imw_error());
		if(imw_num_rows($ptWaitRes)>0) {
			$ptWaitRow = imw_fetch_array($ptWaitRes);	
			$iolink_allergiesNKDA_status = $ptWaitRow["iolink_allergiesNKDA_status"];
			$iolinkNoMedicationStatus 	= $ptWaitRow["iolink_no_medication_status"];
			$iolinkNoMedicationComments = $ptWaitRow["iolink_no_medication_comments"];
		}
		//$objManageData->getRowRecord("patient_in_waiting_tbl", "patient_in_waiting_id", $patient_in_waiting_id);	
	}
	//END GET ALLERGY NKDA STATUS
	
	$table = 'allergies';
	if($preOpHealthQuesId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord('iolink_preophealthquestionnaire', 'preOpHealthQuesId', $preOpHealthQuesId);
	}else if($patient_in_waiting_id){
		$getPreOpQuesDetails = $objManageData->getExtractRecord('iolink_preophealthquestionnaire', 'patient_in_waiting_id', $patient_in_waiting_id);	
	}
	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
	}
	
	$blurInput = "onBlur=\"if(!this.value){this.style.backgroundColor='#F6C67A' }\"";
	$keyPressInput = "onKeyPress=\"javascript:this.style.backgroundColor='#FFFFFF'\"";
	?>
    <form name="frm_health_ques" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px;" action="iolink_pre_op_health_quest.php?saveRecord=true&amp;SaveForm_alert=true">
        <input type="hidden" name="divId" id="divId">
        <input type="hidden" name="counter" id="counter">
        <input type="hidden" name="secondaryValues" id="secondaryValues">
        <input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
        <input type="hidden" name="selected_frame_name" id="selected_frame_name_id"  value="">			
        <input type="hidden" name="innerKey" id="innerKey" value="<?php echo $innerKey; ?>">
        <input type="hidden" name="preColor" id="preColor" value="<?php echo $preColor; ?>">
        <input type="hidden" name="pConfId" id="pConfId" value="<?php echo $pConfId; ?>">
        <input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
        <input type="hidden" name="patient_in_waiting_id" id="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
        <input type="hidden" name="preOpHealthQuesId" id="preOpHealthQuesId" value="<?php echo $preOpHealthQuesId; ?>">
        <input type="hidden" name="getText" id="getText">
        <input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
        <input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>">
        <input type="hidden" name="frmAction" id="frmAction" value="iolink_pre_op_health_quest.php">
        <input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
        <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
        <input type="hidden" name="preDefineDivOpenClose" id="preDefineDivOpenClose" value="">
        <input type="hidden" name="hiddWitSignatureId" id="hiddWitSignatureId" value="<?php echo $signWitness1Id; ?>">
        <input type="hidden" name="hiddWitSignClick" id="hiddWitSignClick" value="">
        <input type="hidden" name="hidden_patient_sign_image_path" id="hidden_patient_sign_image_path" value='<?php echo $patient_sign_image_path;?>' />
        
        <table class="table_collapse alignCenter" style="border:none;" onDblClick="closePreDefineDiv()" onClick="calCloseFun();preCloseFun('evaluationPreDefineDiv');preCloseFun('evaluationPreDefineMedDiv');" onMouseOver="">
            <tr>
                <td class="valignTop alignCenter">
                    <table class="valignTop" style="width:250px; padding:0px;  border:none; border-collapse:collapse; display:inline-block;">
                        <tr>
                            <td class="valignTop" style=" padding:0px; text-align:right;" ><img src="images/left.gif" alt="" style="width:3px; height:26px; border:none; padding:0px;"></td>
                            <td class="text_10b nowrap alignCenter valignMiddle" style="background-color:#BCD2B0; padding:0px;" >Pre-Op Health Questionnaire</td>
                            <td class="alignLeft valignTop" style=" padding:0px;"><img src="images/right.gif" alt="" style="width:3px; height:26px; border:none;padding:0px;"></td>
                            <td>&nbsp;</td>
                        </tr>
                  </table>
                </td>
            </tr>
            <tr>
                <td class="alignLeft" >
                    <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
                        <?php 
                            $bgCol = '#BCD2B0';
                            $borderCol = '#BCD2B0';
                            include('saveDivPopUp.php'); 
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="background-color:#ECF1EA;">
                    <table class="all_border alignCenter" style="border:none; width:99%; border-collapse:collapse; background-color:#FFFFFF;">
                        <tr>
                            <td style="padding-left:4px;">&nbsp;</td>
                            <td class="valignTop" style="width:510px;">
                                <table class="table_collapse ">
                                    <tr style="background-color:#D1E0C9;">
                                        <td class="text_10b" style="height:22px; padding-left:8px;">Have you ever had</td>
                                        <td class="text_10b alignLeft" style="height:20px;">Yes</td>
                                        <td class="text_10b alignLeft" style="height:20px; width:7%;">&nbsp;No</td>
                                        <td class="text_10 alignLeft pad_top_bottom"></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:405px;">
                                        <div style="padding-left:2px; ">Heart Trouble/Heart Attack</div>
                                            <table class="table_collapse alignCenter" id="htha_yes"  style="display:<?php if($heartTrouble=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr><td colspan="3" style="padding-left:2px;"></td></tr>
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="heartTroubleDesc" class="field textarea justi" name="heartTroubleDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($heartTroubleDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop"  onClick="javascript:checkSingle('chbx_ht_yes','chbx_ht'),disp(document.frm_health_ques.chbx_ht,'htha_yes'),changeChbxColor('chbx_ht')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($heartTrouble) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($heartTrouble=='Yes') echo "CHECKED"; ?> name="chbx_ht" type="checkbox" value="Yes" id="chbx_ht_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_ht_no','chbx_ht'),disp_none(document.frm_health_ques.chbx_ht,'htha_yes'),changeChbxColor('chbx_ht')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($heartTrouble) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($heartTrouble=='No')) echo "CHECKED"; ?> name="chbx_ht" type="checkbox" value="No" id="chbx_ht_no" tabindex="7" ></span></span></td>
                                        <td id="arr_4" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'htha_yes','arr_4')" ><img alt=""  src="<?php if($heartTrouble=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>                                    
                                    </tr>
                                    <tr>
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Stroke</div>
                                            <table class="table_collapse alignCenter" id="chbx_sht_tb_id"  style="display:<?php if($stroke=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="strokeDesc" class="field textarea justi" name="strokeDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($strokeDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_sht_yes','chbx_sht'),disp(document.frm_health_ques.chbx_sht,'chbx_sht_tb_id'),changeChbxColor('chbx_sht')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($stroke) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($stroke=='Yes') echo "CHECKED"; ?> name="chbx_sht" type="checkbox" value="Yes" id="chbx_sht_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_sht_no','chbx_sht'),disp_none(document.frm_health_ques.chbx_sht,'chbx_sht_tb_id'),changeChbxColor('chbx_sht')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($stroke) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($stroke=='No')) echo "CHECKED"; ?> name="chbx_sht" type="checkbox" value="No" id="chbx_sht_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_sht_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_sht_tb_id','chbx_sht_img_id')" ><img alt=""  src="<?php if($stroke=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr> 
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">HighBP</div>
                                            <table class="table_collapse alignCenter" id="chbx_HighBP_tb_id"  style="display:<?php if($HighBP=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="HighBPDesc" class="field textarea justi" name="HighBPDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($HighBPDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_HighBP_yes','chbx_HighBP'),disp(document.frm_health_ques.chbx_HighBP,'chbx_HighBP_tb_id'),changeChbxColor('chbx_HighBP')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($HighBP) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($HighBP=='Yes') echo "CHECKED"; ?> name="chbx_HighBP" type="checkbox" value="Yes" id="chbx_HighBP_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_HighBP_no','chbx_HighBP'),disp_none(document.frm_health_ques.chbx_HighBP,'chbx_HighBP_tb_id'),changeChbxColor('chbx_HighBP')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($HighBP) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($HighBP=='No')) echo "CHECKED"; ?> name="chbx_HighBP" type="checkbox" value="No" id="chbx_HighBP_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_HighBP_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_HighBP_tb_id','chbx_HighBP_img_id')" ><img alt=""  src="<?php if($HighBP=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>                                    
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Anticoagulation therapy (i.e. Blood Thinners)</div>
                                            <table class="table_collapse alignCenter" id="chbx_anti_thrp_tb_id"  style="display:<?php if($anticoagulationTherapy=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="anticoagulationTherapyDesc" class="field textarea justi" name="anticoagulationTherapyDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($anticoagulationTherapyDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_anti_thrp_yes','chbx_anti_thrp'),disp(document.frm_health_ques.chbx_anti_thrp,'chbx_anti_thrp_tb_id'),changeChbxColor('chbx_anti_thrp')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($anticoagulationTherapy) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($anticoagulationTherapy=='Yes') echo "CHECKED"; ?> name="chbx_anti_thrp" type="checkbox" value="Yes" id="chbx_anti_thrp_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_anti_thrp_no','chbx_anti_thrp'),disp_none(document.frm_health_ques.chbx_anti_thrp,'chbx_anti_thrp_tb_id'),changeChbxColor('chbx_anti_thrp')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($anticoagulationTherapy) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($anticoagulationTherapy=='No')) echo "CHECKED"; ?> name="chbx_anti_thrp" type="checkbox" value="No" id="chbx_anti_thrp_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_anti_thrp_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_anti_thrp_tb_id','chbx_anti_thrp_img_id')" ><img alt=""  src="<?php if($anticoagulationTherapy=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>                                    
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Asthma, Sleep Apnea, Breathing Problems</div>
                                            <table class="table_collapse alignCenter" id="chbx_ast_slp_tb_id"  style="display:<?php if($asthma=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="asthmaDesc" class="field textarea justi" name="asthmaDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($asthmaDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop"  onClick="javascript:checkSingle('chbx_ast_slp_yes','chbx_ast_slp'),disp(document.frm_health_ques.chbx_ast_slp,'chbx_ast_slp_tb_id'),changeChbxColor('chbx_ast_slp')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($asthma) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($asthma=='Yes') echo "CHECKED"; ?> name="chbx_ast_slp" type="checkbox" value="Yes" id="chbx_ast_slp_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_ast_slp_no','chbx_ast_slp'),disp_none(document.frm_health_ques.chbx_ast_slp,'chbx_ast_slp_tb_id'),changeChbxColor('chbx_ast_slp')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($asthma) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($asthma=='No')) echo "CHECKED"; ?> name="chbx_ast_slp" type="checkbox" value="No" id="chbx_ast_slp_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_ast_slp_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_ast_slp_tb_id','chbx_ast_slp_img_id')" ><img alt=""  src="<?php if($asthma=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Tuberculosis</div>
                                            <table class="table_collapse alignCenter" id="chbx_tuber_tb_id"  style="display:<?php if($tuberculosis=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="tuberculosisDesc" class="field textarea justi" name="tuberculosisDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($tuberculosisDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_tuber_yes','chbx_tuber'),disp(document.frm_health_ques.chbx_tuber,'chbx_tuber_tb_id'),changeChbxColor('chbx_tuber')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($tuberculosis) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($tuberculosis=='Yes') echo "CHECKED"; ?> name="chbx_tuber" type="checkbox" value="Yes" id="chbx_tuber_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_tuber_no','chbx_tuber'),disp_none(document.frm_health_ques.chbx_tuber,'chbx_tuber_tb_id'),changeChbxColor('chbx_tuber')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($tuberculosis) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($tuberculosis=='No')) echo "CHECKED"; ?> name="chbx_tuber" type="checkbox" value="No" id="chbx_tuber_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_tuber_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_tuber_tb_id','chbx_tuber_img_id')" ><img alt=""  src="<?php if($tuberculosis=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px;">
                                            <div style="padding-left:4px;">Diabetes</div>
                                            <table class="alignCenter" id="diab_yes" style="border-collapse:collapse; width:98%;display:<?php if($diabetes=='Yes') echo "inline-block"; else echo "none"; ?>;">
                                                <tr style="height:22px;">
                                                    <td style="padding-left:10px;" class="text_10 valignMiddle alignLeft">Insulin Dependent</td>
                                                    <td style="" class="text_10 alignLeft valignTop" onClick="checkSingle('chbx_subdiab_yes','chbx_subdiab')">
                                                        <input class="field checkbox" type="checkbox" <?php if($insulinDependence=="Yes") echo "Checked";  ?> name="chbx_subdiab" value="Yes" id="chbx_subdiab_yes" tabindex="7" >
                                                    </td>
                                                </tr>
                                                <tr style="height:22px;">
                                                    <td style="padding-left:10px; width:180px;"  class="text_10 valignMiddle alignLeft">Non-Insulin Dependent</td>
                                                    <td class="text_10 alignLeft valignTop" onClick="checkSingle('chbx_subdiab_no','chbx_subdiab')">
                                                        <input class="field checkbox" type="checkbox" <?php if($insulinDependence=="No") echo "Checked";  ?> name="chbx_subdiab" value="No" id="chbx_subdiab_no" tabindex="7" >
                                                    </td>
                                                </tr>
                                                <tr style="height:22px;">
                                                    <td colspan="2" style="padding-left:10px;" class="text_10 valignMiddle alignLeft">
                                                        <table class="text_10">
                                                            <tr>
                                                                <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                                <td class="text_10  alignLeft" style="width:305px;">
                                                                    <textarea id="diabetesDesc" class="field textarea justi" name="diabetesDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($diabetesDesc); ?></textarea>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_diab_yes','chbx_diab'),disp(document.frm_health_ques.chbx_diab,'diab_yes'),changeChbxColor('chbx_diab')" ><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($diabetes) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($diabetes=='Yes') echo "CHECKED"; ?> name="chbx_diab" type="checkbox" value="Yes" id="chbx_diab_yes"  tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_diab_no','chbx_diab'),disp_none(document.frm_health_ques.chbx_diab,'diab_yes'),changeChbxColor('chbx_diab')" ><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($diabetes) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if((($getPreOpQuesDetails) && $diabetes=='No')) echo "CHECKED"; ?>  name="chbx_diab" type="checkbox" value="No" id="chbx_diab_no" tabindex="7" ></span></span></td>
                                        <td id="arr_1" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'diab_yes','arr_1')" class="text_10 alignCenter valignTop"  style="padding-top:3px; "><img alt=""  src="<?php if($diabetes=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Epilepsy, Convulsions, Parkinson's, Vertigo</div>
                                            <table class="table_collapse alignCenter" id="chbx_epile_tb_id"  style="display:<?php if($epilepsy=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="epilepsyDesc" class="field textarea justi" name="epilepsyDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($epilepsyDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_epile_yes','chbx_epile'),disp(document.frm_health_ques.chbx_epile,'chbx_epile_tb_id'),changeChbxColor('chbx_epile')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($epilepsy) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($epilepsy=='Yes') echo "CHECKED"; ?> name="chbx_epile" type="checkbox" value="Yes" id="chbx_epile_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_epile_no','chbx_epile'),disp_none(document.frm_health_ques.chbx_epile,'chbx_epile_tb_id'),changeChbxColor('chbx_epile')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($epilepsy) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($epilepsy=='No')) echo "CHECKED"; ?> name="chbx_epile" type="checkbox" value="No" id="chbx_epile_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_epile_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_epile_tb_id','chbx_epile_img_id')" ><img alt=""  src="<?php if($epilepsy=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Restless Leg Syndrome</div>
                                            <table class="table_collapse alignCenter" id="chbx_restless_tb_id"  style="display:<?php if($restlessLegSyndrome=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr><td colspan="3" style="padding-left:2px;"></td></tr>
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="restlessLegSyndromeDesc" class="field textarea justi" name="restlessLegSyndromeDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($restlessLegSyndromeDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_restless_yes','chbx_restless'),disp(document.frm_health_ques.chbx_restless,'chbx_restless_tb_id'),changeChbxColor('chbx_restless')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($restlessLegSyndrome) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($restlessLegSyndrome=='Yes') echo "CHECKED"; ?> name="chbx_restless" type="checkbox" value="Yes" id="chbx_restless_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_restless_no','chbx_restless'),disp_none(document.frm_health_ques.chbx_restless,'chbx_restless_tb_id'),changeChbxColor('chbx_restless')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($restlessLegSyndrome) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($restlessLegSyndrome=='No')) echo "CHECKED"; ?> name="chbx_restless" type="checkbox" value="No" id="chbx_restless_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_restless_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_restless_tb_id','chbx_restless_img_id')" ><img alt=""  src="<?php if($restlessLegSyndrome=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>                                    
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Hepatitis</div>
                                            <table class="table_collapse alignCenter" id="chbx_hepat_tb_id"  style="display:<?php if($hepatitis=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr class="alignLeft"  id="trHepatitis">
                                                    <td colspan="3" class="alignLeft valignMiddle" >
                                                        <table class="table_collapse">
                                                            <tr>
                                                                <td style="width:70px;">&nbsp;</td>
                                                                <td class="text_10 alignLeft" style="width:70px;" >A<input type="checkbox" name="HepatitisA" id="HepatitisA" value="true" <?php if($hepatitisA == 'true') echo "CHECKED"; ?>></td>
                                                                <td class="text_10 alignLeft" style="width:70px;" >B<input type="checkbox" name="HepatitisB" id="HepatitisB" value="true" <?php if($hepatitisB == 'true') echo "CHECKED"; ?>></td>
                                                                <td class="text_10 alignLeft" style="width:190px;" >C<input type="checkbox" name="HepatitisC" id="HepatitisC" value="true" <?php if($hepatitisC == 'true') echo "CHECKED"; ?>></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>            
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="hepatitisDesc" class="field textarea justi" name="hepatitisDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($hepatitisDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hepat_yes','chbx_hepat'),disp(document.frm_health_ques.chbx_hepat,'chbx_hepat_tb_id'),changeChbxColor('chbx_hepat')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($hepatitis) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($hepatitis=='Yes') echo "CHECKED"; ?> name="chbx_hepat" type="checkbox" value="Yes" id="chbx_hepat_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hepat_no','chbx_hepat'),disp_none(document.frm_health_ques.chbx_hepat,'chbx_hepat_tb_id'),changeChbxColor('chbx_hepat')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($hepatitis) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($hepatitis=='No')) echo "CHECKED"; ?> name="chbx_hepat" type="checkbox" value="No" id="chbx_hepat_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_hepat_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_hepat_tb_id','chbx_hepat_img_id')" ><img alt=""  src="<?php if($hepatitis=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr> 
                                    
                                    <tr class="alignLeft" style="display:<?php if(($hepatitisA == 'true') || ($hepatitisB == 'true') || ($hepatitisC == 'true') || ($hepatitis=="Yes")) echo 'inline-block'; else echo 'none'; ?>;" id="trHepatitis">
                                        <td class="alignLeft valignMiddle" >
                                            <table class="table_collapse">
                                                <tr>
                                                    <td style="width:70px;">&nbsp;</td>
                                                    <td class="text_10 alignLeft" style="width:70px;" >A<input type="checkbox" name="HepatitisA" id="HepatitisA" value="true" <?php if($hepatitisA == 'true') echo "CHECKED"; ?>></td>
                                                    <td class="text_10 alignLeft" style="width:70px;" >B<input type="checkbox" name="HepatitisB" id="HepatitisB" value="true" <?php if($hepatitisB == 'true') echo "CHECKED"; ?>></td>
                                                    <td class="text_10 alignLeft" style="width:190px;" >C<input type="checkbox" name="HepatitisC" id="HepatitisC" value="true" <?php if($hepatitisC == 'true') echo "CHECKED"; ?>></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td colspan="3" class="alignCenter valignMiddle"></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Kidney Disease, Dialysis</div>
                                            <table class="table_collapse alignCenter" id="chbx_kidn_tb_id"  style="display:<?php if($kidneyDisease=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr style="height:22px;">
                                                    <td style="padding-left:10px;" class="text_10 valignMiddle alignLeft">Do you have a Shunt</td>
                                                    <td style="" class="text_10 alignLeft valignTop" >
                                                        <input class="field checkbox" type="checkbox" <?php if($shunt=="Yes") echo "Checked";  ?> value="Yes" name="chbx_subkidnShunt" id="chbx_subkidn_yes" tabindex="7" >
                                                    </td>
                                                </tr>
                                                <tr style="height:22px;">
                                                    <td style="padding-left:10px; width:180px;"  class="text_10 valignMiddle alignLeft">Fistula</td>
                                                    <td class="text_10 alignLeft valignTop" >
                                                        <input class="field checkbox" type="checkbox" <?php if($fistula=="Yes") echo "Checked";  ?> value="Yes" name="chbx_subkidnFistula" id="chbx_subkidn_no" tabindex="7" >
                                                    </td>
                                                </tr>
                                                <tr style="height:22px;">
                                                    <td colspan="2" style="padding-left:10px;" class="text_10 valignMiddle alignLeft">
                                                        <table class="text_10">
                                                            <tr>
                                                                <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                                <td class="text_10  alignLeft" style="width:305px;">
                                                                    <textarea id="kidneyDiseaseDesc" class="field textarea justi" name="kidneyDiseaseDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($kidneyDiseaseDesc); ?></textarea>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_kidn_yes','chbx_kidn'),disp(document.frm_health_ques.chbx_kidn,'chbx_kidn_tb_id'),changeChbxColor('chbx_kidn')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($kidneyDisease) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($kidneyDisease=='Yes') echo "CHECKED"; ?> name="chbx_kidn" type="checkbox" value="Yes" id="chbx_kidn_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_kidn_no','chbx_kidn'),disp_none(document.frm_health_ques.chbx_kidn,'chbx_kidn_tb_id'),changeChbxColor('chbx_kidn')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($kidneyDisease) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($kidneyDisease=='No')) echo "CHECKED"; ?> name="chbx_kidn" type="checkbox" value="No" id="chbx_kidn_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_kidn_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_kidn_tb_id','chbx_kidn_img_id')" ><img alt=""  src="<?php if($kidneyDisease=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    
                                    
                                    
                                    <?php
                                        $table_row = "";
                                        if($browserName=="IE")
                                        {
                                            $table_row = "inline-block";
                                        }else{
                                            $table_row = "table-row";
                                        }
                                    ?>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">HIV, Autoimmune Diseases</div>
                                            <table class="table_collapse alignCenter" id="chbx_hiv_auto_tb_id"  style="display:<?php if($hivAutoimmuneDiseases=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="hivTextArea" class="field textarea justi" name="hivTextArea" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($hivTextArea); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hiv_auto_yes','chbx_hiv_auto'),disp(document.frm_health_ques.chbx_hiv_auto,'chbx_hiv_auto_tb_id'),changeChbxColor('chbx_hiv_auto')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($hivAutoimmuneDiseases) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($hivAutoimmuneDiseases=='Yes') echo "CHECKED"; ?> name="chbx_hiv_auto" type="checkbox" value="Yes" id="chbx_hiv_auto_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hiv_auto_no','chbx_hiv_auto'),disp_none(document.frm_health_ques.chbx_hiv_auto,'chbx_hiv_auto_tb_id'),changeChbxColor('chbx_hiv_auto')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($hivAutoimmuneDiseases) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($hivAutoimmuneDiseases=='No')) echo "CHECKED"; ?> name="chbx_hiv_auto" type="checkbox" value="No" id="chbx_hiv_auto_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_hiv_auto_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_hiv_auto_tb_id','chbx_hiv_auto_img_id')" ><img alt=""  src="<?php if($hivAutoimmuneDiseases=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr id="diab_yes2_main" style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="padding-left:4px; height:22px;">History of cancer</td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hist_can_yes','chbx_hist_can'),changeChbxColor('chbx_hist_can'),disp(document.frm_health_ques.chbx_hist_can,'diab_yes2'); return showHidebrestCancerTr();"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($cancerHistory) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx"  <?php if($cancerHistory=='Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" name="chbx_hist_can" id="chbx_hist_can_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hist_can_no','chbx_hist_can'),changeChbxColor('chbx_hist_can'),disp_none(document.frm_health_ques.chbx_hist_can,'diab_yes2'); return showHidebrestCancerTr();"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($cancerHistory) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($cancerHistory=='No')) echo "CHECKED"; ?>  type="checkbox" value="No" name="chbx_hist_can" id="chbx_hist_can_no" tabindex="7"></span></span></td>
                                        <td id="arr_3" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_hist_can,'diab_yes2','arr_3')" ><img alt=""  src="<?php if($cancerHistory=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none;"/></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;display:<?php if($brest_cancer) echo $table_row; else echo "none"; ?>;" id="brestCancerTr">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="padding-left:10px; height:22px;">
                                            Brest cancer
                                        <span style="padding-left:15px;" class="text_10 pad_top_bottom">Left</span>
                                        <span onClick="javascript:checkSingle('chbx_leftbrest_can_yes','brestCancerLeft');"><input <?php if($brestCancerLeft == 'Yes') echo 'CHECKED'; ?> value="Yes" name="brestCancerLeft" id="chbx_leftbrest_can_yes" class="" type="checkbox"></span>
                                        <span class="text_10 pad_top_bottom">Right</span>
                                        <span onClick="javascript:checkSingle('chbx_leftbrest_can_no','brestCancerLeft');"><input <?php if($brestCancerLeft == 'No') echo 'CHECKED'; ?> value="No" name="brestCancerLeft" id="chbx_leftbrest_can_no" class="" type="checkbox"></span>
                                        </td>
                                        <td class="text_10 pad_top_bottom alignLeft" onClick="javascript:checkSingle('chbx_brest_can_yes','brest_cancer');">
                                            <span style=" margin-left:3px;">
                                                <input class="field checkbox" id="chbx_brest_can_yes" name="brest_cancer" <?php if($brest_cancer == 'Yes') echo "CHECKED"; ?> type="checkbox" value="Yes" tabindex="7">
                                            </span>
                                        </td>
                                        <td class="text_10 pad_top_bottom alignLeft" onClick="javascript:checkSingle('chbx_brest_can_no','brest_cancer');">
                                            <span style=" margin-left:3px;">
                                                <input class="field checkbox" id="chbx_brest_can_no" name="brest_cancer" <?php if($brest_cancer == 'No') echo "CHECKED"; ?> type="checkbox" value="No" tabindex="7" >
                                            </span>
                                        </td>
                                        <td class="text_10 alignCenter valignTop"></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;display:<?php if($cancerHistory=='Yes') echo $table_row; else echo 'none'; ?>;" id="diab_yes2">
                                        <td class="text_10 alignLeft nowrap" style="padding-left:10px; width:305px; ">
                                            Describe<span style="padding-left:15px;"><textarea id="cancerHistory" name="cancerHistory" class="field textarea justi" style=" border:1px solid #cccccc; width:300px; height:40px;" tabindex="6"><?php echo stripslashes($cancerHistoryDesc); ?></textarea></span>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Organ Transplant</div>
                                            <table class="table_collapse alignCenter" id="diab_yes3"  style="display:<?php if($organTransplant=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr><td colspan="3" style="padding-left:2px;"></td></tr>
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="organTransDesc" class="field textarea justi" name="organTransDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($organTransplantDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_org_trns_yes','chbx_org_trns'),disp(document.frm_health_ques.chbx_org_trns,'diab_yes3'),changeChbxColor('chbx_org_trns')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($organTransplant) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($organTransplant=='Yes') echo "CHECKED"; ?> name="chbx_org_trns" type="checkbox" value="Yes" id="chbx_org_trns_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_org_trns_no','chbx_org_trns'),disp_none(document.frm_health_ques.chbx_org_trns,'diab_yes3'),changeChbxColor('chbx_org_trns')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($organTransplant) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($organTransplant=='No')) echo "CHECKED"; ?> name="chbx_org_trns" type="checkbox" value="No" id="chbx_org_trns_no" tabindex="7" ></span></span></td>
                                        <td id="arr_4" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'diab_yes3','arr_4')" ><img alt=""  src="<?php if($organTransplant=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                   
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">A Bad Reaction to Local or General Anesthesia</div>
                                            <table class="table_collapse alignCenter" id="chbx_bad_react_tb_id"  style="display:<?php if($anesthesiaBadReaction=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="anesthesiaBadReactionDesc" class="field textarea justi" name="anesthesiaBadReactionDesc" style=" border:1px solid #cccccc; width:300px;height:40px;" tabindex="6"><?php echo stripslashes($anesthesiaBadReactionDesc); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_bad_react_yes','chbx_bad_react'),disp(document.frm_health_ques.chbx_bad_react,'chbx_bad_react_tb_id'),changeChbxColor('chbx_bad_react')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($anesthesiaBadReaction) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($anesthesiaBadReaction=='Yes') echo "CHECKED"; ?> name="chbx_bad_react" type="checkbox" value="Yes" id="chbx_bad_react_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_bad_react_no','chbx_bad_react'),disp_none(document.frm_health_ques.chbx_bad_react,'chbx_bad_react_tb_id'),changeChbxColor('chbx_bad_react')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($anesthesiaBadReaction) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($anesthesiaBadReaction=='No')) echo "CHECKED"; ?> name="chbx_bad_react" type="checkbox" value="No" id="chbx_bad_react_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_bad_react_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_bad_react_tb_id','chbx_bad_react_img_id')" ><img alt=""  src="<?php if($anesthesiaBadReaction=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF; width:350px;">
                                        <td colspan="4" class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:20px;">
                                             <table class="table_collapse alignCenter" style="border:none;">
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Other</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <textarea id="otherTroubles" name="otherTroubles" class="field textarea justi" style="border:1px solid #cccccc; width:300px;height:40px;"  tabindex="6"><?php echo stripslashes($otherTroubles); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>	
                            </td>
                            <td style="padding-left:2px;"></td>
                            <td class="valignTop" style="width:470px;">
                                <table class="table_collapse alignCenter" style="border:none;">
                                    <tr class="alignLeft" style="background-color:#D1E0C9;">
                                        <td colspan="4" class="text_10b" style="height:22px;">
                                            <table class="table_collapse alignCenter" style="border:none;">
                                                <tr class="alignLeft" style="height:10px; background-color:#D1E0C9;">
                                                    <td id="iolinkPreDefineAllergyTdId" class="text_10b valignMiddle nowrap" style="color:#800080; cursor:pointer; padding-left:4px;" onClick="openPreDefineAllergyFun(parseInt(findPos_X('iolinkPreDefineAllergyTdId'))-250,parseInt(findPos_Y('iolinkPreDefineAllergyTdId')+1));">
                                                        Allergies/Drug Reaction
                                                    </td>
                                                    <td style="height:20px; width:5%;" onClick="javascript:txt_enable_disable_frame1('iframe_health_quest','chbx_drug_react','Allergies_quest','Reaction_quest',10)">
                                                        <input style="vertical-align:top;" class="checkbox"  type="checkbox" <?php if($iolink_allergiesNKDA_status =='Yes'){ echo 'CHECKED'; } ?> value="Yes"  name="chbx_drug_react" id="chbx_drug_react_no" tabindex="7">
                                                    </td>
                                                    <td class="text_10b valignMiddle nowrap" style="color:#800080;">NKA</td>
                                                    <td style="width:5%;" ><!--onClick="javascript:txt_enable_frame1('iframe_health_quest','Allergies_quest','Reaction_quest',10)"-->
                                                        <input style="vertical-align:top; " class="checkbox"  type="checkbox" <?php if($allergies_status_reviewed=='Yes'){ echo 'CHECKED'; } ?>  value="Yes" name="chbx_drug_react_reviewed" id="chbx_drug_react_yes"  tabindex="7" > 
                                                        
                                                    </td>
                                                    <td class="text_10b  valignMiddle nowrap" style="color:#800080;">Allergies Reviewed</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;">
                                        <td colspan="4" class="alignLeft valignMiddle">
                                            <table class="table_collapse" style="border-color:#333333">
                                                <tr style="background-color:#FFFFFF;">
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                    <td class="text_10  pad_top_bottom" style="width:436px;">Name</td>
                                                    <td class="text_10  pad_top_bottom" style="width:512px;">&nbsp;&nbsp;&nbsp;&nbsp;Reaction</td>
                                                </tr>
                                                <tr style="background-color:#F1F4F0;">
                                                    <td colspan="3" style="background-color:#F1F4F0;">
                                                     <!-- <iframe name="iframe_health_quest" src="health_quest_spreadsheet.php?pConfId=<?php echo $pConfId; ?>&amp;patient_id=<?php echo $patient_id; ?>&amp;ascId=<?php echo $ascId; ?>&amp;allgNameWidth=218&amp;allgReactionWidth=218" width="100%" height="95"  frameborder="0"  scrolling="yes" ></iframe> --> 
                                                        <div id="iframe_health_quest" style="height:95px; overflow:auto; overflow-x:hidden; ">
                                                            <?php  
                                                                $allgNameWidth=215;
                                                                $allgReactionWidth=215;
                                                                include("iolink_allergy_healthquest_spreadsheet.php");
                                                            ?>
                                                        </div>
                                                    </td>
                                                </tr>	
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="alignLeft" style=" background-color:#D1E0C9;">
                                    	
                                    	<td colspan="4" class="alignLeft valignMiddle" style="color:#800080; height:22px; padding-left:4px;">
                                        	<table class="table_collapse" style="border:none; border-color:#333333;">
                                            	<tr>
                                                	<td colspan="2" id="iolinkPreDefineMedTdId" class="text_10b" style="color:#800080; height:22px; cursor:pointer;padding-left:4px;" onClick="openPreDefineMedicationFun(parseInt(findPos_X('iolinkPreDefineMedTdId'))-250,parseInt(findPos_Y('iolinkPreDefineMedTdId')+1));">Take Prescription Medications</td>
                                                
                                                </tr>
                                            	<tr>
                                                    <td class="alignCenter text_10" style="height:22px;white-space:nowrap;"><input type="checkbox" name="iolink_no_medication_status" id="iolink_no_medication_status" value="Yes" onClick="clearMedVal(this)" <?php if($iolinkNoMedicationStatus == "Yes") { echo "checked"; }?>>No Medications</td>
                                                    <td class="alignCenter text_10" style="height:22px; white-space:nowrap; padding-left:40px;">Comments<span style="padding-left:5px;vertical-align:middle;"><textarea name="iolink_no_medication_comments" id="iolink_no_medication_comments" class="field text1" style="font-family:verdana; border:1px solid #B9B9B9;  height:22px; width:220px; "><?php echo $iolinkNoMedicationComments;?></textarea></span></td>
                                                
                                                </tr>
                                                
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td colspan="4" class="alignLeft valignMiddle">
                                            <table class="table_collapse" style="border:none; border-color:#333333;">
                                                <tr style="background-color:#FFFFFF;">
                                                    <td class="text_10  " style="width:140px;padding-left:4px;">Name</td>
                                                    <td class="text_10  " style="width:120px; padding-left:3px;">Dosage</td>
                                                    <td class="text_10  " style="width:120px; padding-left:3px;">Sig</td>
                                                </tr>
                                                <tr style="background-color:#F1F4F0;">
                                                    <td colspan="3" style="background-color:#F1F4F0;">
                                                        <!-- <iframe name="iframe_health_quest_medication" src="patient_prescription_medi_spreadsheet.php?pConfId=<?php //echo $pConfId; ?>&amp;patient_id=<?php //echo $patient_id; ?>&amp;ascId=<?php //echo $ascId; ?>&amp;medicNameWidth=218&amp;medicDetailWidth=218" width="100%" height="95"  frameborder="0"  scrolling="yes"></iframe>    -->
                                                        <div id="iframe_health_quest_medication" style="height:95px; overflow:auto; overflow-x:hidden; ">
                                                            <?php  
                                                                $medicNameWidth=150;
                                                                $medicDetailWidth=140;
                                                                include("iolink_medi_healthquest_spreadsheet.php");
                                                            ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10b alignLeft" style="padding-left:5px; height:20px; width:380px; background-color:#D1E0C9;">Do You</td>
                                        <td class="text_10b alignLeft" style="height:20px; background-color:#D1E0C9;">Yes</td>
                                        <td class="text_10b alignLeft" style="height:20px; background-color:#D1E0C9;">No</td>
                                        <td class="text_10b alignLeft" style="height:20px; background-color:#D1E0C9;"></td>
                                    </tr>
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Use a Wheel Chair, Walker or Cane</div>
                                            <table class="table_collapse alignCenter" id="chbx_use_wheel_tb_id"  style="display:<?php if($walker=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px; padding-left:15px;">
                                                        <input type="text" id="walkerDesc" class="field text text_10" name="walkerDesc" value="<?php echo $walkerDesc; ?>" tabindex="1" style="border: 1px solid #cccccc; width:235px; "/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_use_wheel_yes','chbx_use_wheel'),disp(document.frm_health_ques.chbx_use_wheel,'chbx_use_wheel_tb_id'),changeChbxColor('chbx_use_wheel')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($walker) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($walker=='Yes') echo "CHECKED"; ?> name="chbx_use_wheel" type="checkbox" value="Yes" id="chbx_use_wheel_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_use_wheel_no','chbx_use_wheel'),disp_none(document.frm_health_ques.chbx_use_wheel,'chbx_use_wheel_tb_id'),changeChbxColor('chbx_use_wheel')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($walker) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($walker=='No')) echo "CHECKED"; ?> name="chbx_use_wheel" type="checkbox" value="No" id="chbx_use_wheel_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_use_wheel_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_use_wheel_tb_id','chbx_use_wheel_img_id')" ><img alt=""  src="<?php if($walker=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>  
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Wear Contact lenses</div>
                                            <table class="table_collapse alignCenter" id="chbx_wear_cont_tb_id"  style="display:<?php if($contactLenses=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px; padding-left:15px;">
                                                        <input type="text" id="contactLensesDesc" class="field text text_10" name="contactLensesDesc" value="<?php echo $contactLensesDesc; ?>" tabindex="1" style="border: 1px solid #cccccc; width:235px; "/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_wear_cont_yes','chbx_wear_cont'),disp(document.frm_health_ques.chbx_wear_cont,'chbx_wear_cont_tb_id'),changeChbxColor('chbx_wear_cont')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($contactLenses) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($contactLenses=='Yes') echo "CHECKED"; ?> name="chbx_wear_cont" type="checkbox" value="Yes" id="chbx_wear_cont_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_wear_cont_no','chbx_wear_cont'),disp_none(document.frm_health_ques.chbx_wear_cont,'chbx_wear_cont_tb_id'),changeChbxColor('chbx_wear_cont')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($contactLenses) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($contactLenses=='No')) echo "CHECKED"; ?> name="chbx_wear_cont" type="checkbox" value="No" id="chbx_wear_cont_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_wear_cont_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_wear_cont_tb_id','chbx_wear_cont_img_id')" ><img alt=""  src="<?php if($contactLenses=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>                                    
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Smoke</div>
                                            <table class="table_collapse alignCenter" id="chbx_smoke_tb_id"  style="display:<?php if($smoke=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:2px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">How&nbsp;much</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <input type="text" id="smokeHowMuch" class="field text text_10" name="smokeHowMuch" value="<?php echo $smokeHowMuch; ?>" tabindex="1" style="border: 1px solid #cccccc; width:235px; "/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style=" padding-left:2px; width:1px;">&nbsp;</td>
                                                    <td colspan="2" class="text_10  alignLeft" style="width:335px; white-space:nowrap;">
                                                        Patient advised not to smoke 24 H prior <br>to surgery <input class="field checkbox opctyChkBx" type="checkbox" name="smokeAdvise" id="smokeAdvise" value="Yes" <?php if($smokeAdvise == 'Yes') echo "CHECKED"; ?>>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_smoke_yes','chbx_smoke'),disp(document.frm_health_ques.chbx_smoke,'chbx_smoke_tb_id'),changeChbxColor('chbx_smoke')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($smoke) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($smoke=='Yes') echo "CHECKED"; ?> name="chbx_smoke" type="checkbox" value="Yes" id="chbx_smoke_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_smoke_no','chbx_smoke'),disp_none(document.frm_health_ques.chbx_smoke,'chbx_smoke_tb_id'),changeChbxColor('chbx_smoke')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($smoke) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($smoke=='No')) echo "CHECKED"; ?> name="chbx_smoke" type="checkbox" value="No" id="chbx_smoke_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_smoke_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_smoke_tb_id','chbx_smoke_img_id')" ><img alt=""  src="<?php if($smoke=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Drink Alcohol</div>
                                            <table class="table_collapse alignCenter" id="chbx_drink_tb_id"  style="display:<?php if($alchohol=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:2px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">How&nbsp;much</td>
                                                    <td class="text_10  alignLeft" style="width:305px;">
                                                        <input type="text" id="alchoholHowMuch" class="field text text_10" name="alchoholHowMuch" value="<?php echo $alchoholHowMuch; ?>" tabindex="1" style="border: 1px solid #cccccc; width:235px; "/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style=" padding-left:2px; width:1px;">&nbsp;</td>
                                                    <td colspan="2" class="text_10  alignLeft" style="width:335px; white-space:nowrap;">
                                                        Patient advised not to drink 24 H prior <br>to surgery&nbsp;&nbsp;&nbsp;<input class="field checkbox opctyChkBx" type="checkbox" name="alchoholAdvise" id="alchoholAdvise" value="Yes" <?php if($alchoholAdvise == 'Yes') echo "CHECKED"; ?>>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_drink_yes','chbx_drink'),disp(document.frm_health_ques.chbx_drink,'chbx_drink_tb_id'),changeChbxColor('chbx_drink')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($alchohol) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($alchohol=='Yes') echo "CHECKED"; ?> name="chbx_drink" type="checkbox" value="Yes" id="chbx_drink_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_drink_no','chbx_drink'),disp_none(document.frm_health_ques.chbx_drink,'chbx_drink_tb_id'),changeChbxColor('chbx_drink')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($alchohol) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($alchohol=='No')) echo "CHECKED"; ?> name="chbx_drink" type="checkbox" value="No" id="chbx_drink_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_drink_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_drink_tb_id','chbx_drink_img_id')" ><img alt=""  src="<?php if($alchohol=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr> 
                                    <tr style="background-color:#F1F4F0;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px; width:305px;">
                                        <div style="padding-left:2px; ">Have an automatic internal defibrillator</div>
                                            <table class="table_collapse alignCenter" id="chbx_hav_auto_int_tb_id"  style="display:<?php if($autoInternalDefibrillator=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:305px; padding-left:15px;">
                                                        <input type="text" id="autoInternalDefibrillatorDesc" class="field text text_10" name="autoInternalDefibrillatorDesc" value="<?php echo $autoInternalDefibrillatorDesc; ?>" tabindex="1" style="border: 1px solid #cccccc; width:235px; "/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hav_auto_int_yes','chbx_hav_auto_int'),disp(document.frm_health_ques.chbx_hav_auto_int,'chbx_hav_auto_int_tb_id'),changeChbxColor('chbx_hav_auto_int')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($autoInternalDefibrillator) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($autoInternalDefibrillator=='Yes') echo "CHECKED"; ?> name="chbx_hav_auto_int" type="checkbox" value="Yes" id="chbx_hav_auto_int_yes" tabindex="7" ></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hav_auto_int_no','chbx_hav_auto_int'),disp_none(document.frm_health_ques.chbx_hav_auto_int,'chbx_hav_auto_int_tb_id'),changeChbxColor('chbx_hav_auto_int')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($autoInternalDefibrillator) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($autoInternalDefibrillator=='No')) echo "CHECKED"; ?> name="chbx_hav_auto_int" type="checkbox" value="No" id="chbx_hav_auto_int_no" tabindex="7" ></span></span></td>
                                        <td id="chbx_hav_auto_int_img_id" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev(document.frm_health_ques.chbx_diab,'chbx_hav_auto_int_tb_id','chbx_hav_auto_int_img_id')" ><img alt=""  src="<?php if($autoInternalDefibrillator=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
                                    </tr>
                                    <tr style="background-color:#FFFFFF;">
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="height:22px;">
                                        <div style="padding-left:2px; width:340px;">Have any Metal Prosthetics</div>
                                            <table class="table_collapse alignCenter" id="pro_yes3"  style=" display:display:<?php if($metalProsthetics=='Yes') echo 'inline-block'; else echo 'none'; ?>;">
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Notes</td>
                                                    <td class="text_10  alignLeft" style="width:305px; padding-left:35px;">
                                                        <textarea id="notesDesc" class="field textarea justi" name="notesDesc" style="border:1px solid #cccccc; width:235px; height:30px;"  tabindex="6"><?php echo stripslashes($notes); ?></textarea>
                                                    </td>
                                                </tr>
                                               
                                            </table>
                                        </td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hav_any_met_yes','chbx_hav_any_met'),disp(document.frm_health_ques.chbx_hav_any_met,'pro_yes3'),changeChbxColor('chbx_hav_any_met')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($metalProsthetics) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if($metalProsthetics=='Yes') echo "CHECKED"; ?> name="chbx_hav_any_met" type="checkbox" value="Yes" id="chbx_hav_any_met_yes" tabindex="7"></span></span></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('chbx_hav_any_met_no','chbx_hav_any_met'),disp_none(document.frm_health_ques.chbx_hav_any_met,'pro_yes3'),changeChbxColor('chbx_hav_any_met')"><span class="clpChkBx"><span class="colorChkBx" style=" <?php if($metalProsthetics) { echo $whiteBckGroundColor;}?>" ><input class="field checkbox opctyChkBx" <?php if(($getPreOpQuesDetails) && ($metalProsthetics=='No')) echo "CHECKED"; ?> name="chbx_hav_any_met" type="checkbox" value="No" id="chbx_hav_any_met_no" tabindex="7" ></span></span></td>
                                        <td id="arr_7" class="text_10 alignCenter valignTop " style="padding-top:3px; " onClick="javascript:disp_rev(document.frm_health_ques.chbx_hav_any_met,'pro_yes3','arr_7')" ><img alt=""  src="<?php if($metalProsthetics=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>	
                                    </tr>
                                    
                                </table>
                            </td>
                            <td style="padding-left:4px;"></td>
                        </tr>
                    </table>
                    
                </td>
            </tr>
            <tr>
                <td style="padding-left:1px;"></td>
            </tr>
            <tr>
                <td class="valignTop" >
                    <table class="all_border alignCenter" style="border:none; border-collapse:collapse; width:99%; background-color:#FFFFFF;" >
                        
                            <?php 
                            //adminHealthquestionare
                                $selectAdminQuestionsQry="select * from healthquestioner order by question";
                                $selectAdminQuestions=imw_query($selectAdminQuestionsQry);
                                $selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
                                $inc=0;
                                while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
                                {
                                foreach($ResultselectAdminQuestions as $key=>$value){
                                        $question[$inc][$key]=$value;
                                    }
                                    $inc++;	
                                }
                                
                                //echo $question[2]['question'];
                            //End adminHealthquestionare
                            $getQuesQry=imw_query("select * from iolink_healthquestionadmin where patient_in_waiting_id='$patient_in_waiting_id' order by adminQuestion");
                            $k=0;
                            $QuesnumRows=imw_num_rows($getQuesQry);
                         while($getQuesRes=imw_fetch_array($getQuesQry)){
                            foreach($getQuesRes as $key=>$val){
                               $quest[$k][$key]=$val;
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
                            ?>
                            <tr style="background-color:<?php if($k%2==0){ echo "#F1F4F0;";} else {echo "#FFFFFF;";} ?>">
                            
                                <?php
                                    $endTd = $endTd<= $QuesnumRows ? $t + 2 : $QuesnumRows;
                                    for($t=$t;$t<$endTd;$t++){
                                ?>
                                    <td style="width:50%;">
                                        <table class="alignLeft" style="border:none; width:100%;" >
                                            <tr style="height:22px;">
                                    
                                            <?php
                                            if($t%2==0){$width=5; $tdwidth="408"; $align="center"; $alignClass="alignCenter";$txtAreaWidth = "300";}else{$width=20;$tdwidth="364";$txtAreaWidth = "235"; $align="left";$alignClass="alignLeft";}
                                            ?>
                                                <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="width:<?php echo $tdwidth;?>px; height:18px; padding-left:<?php echo $width;?>px;">
                                                <div style="padding-left:2px; "><?php echo $quest[$t]['adminQuestion']; ?></div>
                                                    <table class="table_collapse alignCenter" id="chbx_admin_quest_tb_id<?php echo $t; ?>"  style="display:<?php if($quest[$t]['adminQuestionStatus']=='Yes') echo 'inline-block'; else echo 'none'; ?>; " >
                                                        <tr><td colspan="3" style="padding-left:2px;"></td></tr>
                                                        <tr>
                                                            <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                            <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                            <td class="text_10  alignLeft" style="width:<?php echo $tdwidth;?>px;">
                                                                <textarea id="adminQuestDesc<?php echo $t; ?>" class="field textarea justi" name="adminQuestDesc<?php echo $t; ?>" style=" border:1px solid #cccccc; width:<?php echo $txtAreaWidth;?>px;height:40px;" tabindex="6"><?php echo stripslashes($quest[$t]['adminQuestionDesc']); ?></textarea>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            	<?php
                                                if($t<$QuesnumRows)
                                                {
                                                ?>	
                                                <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('quest_yes<?php echo $t; ?>','quest<?php echo $t; ?>'),disp('','chbx_admin_quest_tb_id<?php echo $t; ?>')"><input class="field checkbox" <?php if($quest[$t]['adminQuestionStatus']=='Yes') echo "CHECKED"; ?> name="quest<?php echo $t; ?>" type="checkbox" value="Yes" id="quest_yes<?php echo $t; ?>" tabindex="10<?php echo $t; ?>" ></td>
                                                <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('quest_no<?php echo $t; ?>','quest<?php echo $t; ?>'),disp_none('','chbx_admin_quest_tb_id<?php echo $t; ?>')"><input class="field checkbox" <?php if(($getPreOpQuesDetails) && ($quest[$t]['adminQuestionStatus']=='No')) echo "CHECKED"; ?> name="quest<?php echo $t; ?>" type="checkbox" value="No" id="quest_no<?php echo $t; ?>" tabindex="10<?php echo $t; ?>" ><input name="questionDes<?php echo $t; ?>" type="hidden" value="<?php echo $quest[$t]['adminQuestion']; ?>"></td>
                                                <td id="arr_tb_id<?php echo $t; ?>" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev('','chbx_admin_quest_tb_id<?php echo $t; ?>','arr_tb_id<?php echo $t; ?>')" ><img alt=""  src="<?php if($quest[$t]['adminQuestionStatus']=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
												<?php 
												}  ?>
                                            </tr>
                                        </table>
                                    </td>
                                <?php		
                                    }
                                ?>
                             </tr>
                            
                                <?php
                                }
                                ?>
                                
                            <?php	
                            }
                            else if($QuesnumRows<1)
                            { $t=0;
                            for($i=0;$i<ceil($selectAdminQuestionsRows/2);$i++)
                            {
                                
                               // $question[$i]['question'];
                                //$question[]= $question[$i]['question']; 
                                $questionid[]=$question[$i]['healthQuestioner'];	
                            ?>
                            <tr style="background-color:<?php if($i%2==0){ echo "#F1F4F0;";} else {echo "#FFFFFF;";} ?>">
                            <?php
                                    $endTd = $endTd<= $selectAdminQuestionsRows ? $t + 2 : $selectAdminQuestionsRows;
                                    for($t=$t;$t<$endTd;$t++){
                                ?>
                            
                            <td style=" width:50%;">
                                <table class="alignLeft" style="border:none; width:100%;" >
                                    <tr style="height:22px;">
                                    <?php
                                        if($t%2==0){$width=5; $tdwidth="408"; $align="center"; $alignClass="alignCenter";$txtAreaWidth = "300";}else{$width=20;$tdwidth="364";$txtAreaWidth = "235"; $align="left";$alignClass="alignLeft";}
                                    ?>
                                        <td class="text_10 pad_top_bottom alignLeft valignMiddle" style="width:<?php echo $tdwidth;?>px; height:18px; padding-left:<?php echo $width;?>px;">
                                            <div style="padding-left:2px; "><?php echo $question[$t]['question']; ?></div>
                                            <table class="table_collapse alignCenter" id="chbx_admin_question_tb_id<?php echo $t; ?>" style="display:<?php if($question[$t]['adminQuestionStatus']=='Yes') echo 'inline-block'; else echo 'none'; ?>; ">
                                                <tr><td colspan="3" style="padding-left:2px;"></td></tr>
                                                <tr>
                                                    <td style=" padding-left:6px; width:1px;">&nbsp;</td>
                                                    <td class="text_10 pad_top_bottom  alignLeft valignMiddle" style="width:30px; padding-right:10px;">Describe</td>
                                                    <td class="text_10  alignLeft" style="width:<?php echo $tdwidth;?>px;">
                                                        <textarea id="adminQuestionDesc<?php echo $t; ?>" class="field textarea justi" name="adminQuestionDesc<?php echo $t; ?>" style=" border:1px solid #cccccc; width:<?php echo $txtAreaWidth;?>px;height:40px;" tabindex="6"><?php echo stripslashes($question[$t]['adminQuestionDesc']); ?></textarea>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
										<?php
                                        if($t<$selectAdminQuestionsRows)
                                        {
                                        ?>	
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('question_yes<?php echo $t; ?>','question<?php echo $t; ?>'),disp('','chbx_admin_question_tb_id<?php echo $t; ?>')"><input class="field checkbox" name="question<?php echo $t; ?>" type="checkbox" value="Yes" id="question_yes<?php echo $t; ?>" tabindex="10<?php echo $t; ?>" ></td>
                                        <td class="text_10 pad_top_bottom lftPad alignLeft valignTop" onClick="javascript:checkSingle('question_no<?php echo $t; ?>','question<?php echo $t; ?>'),disp_none('','chbx_admin_question_tb_id<?php echo $t; ?>')"><input class="field checkbox" name="question<?php echo $t; ?>" type="checkbox" value="No" id="question_no<?php echo $t; ?>" tabindex="10<?php echo $t; ?>" ><input name="questionDes<?php echo $t; ?>" type="hidden" value="<?php echo $question[$t]['question']; ?>"></td>
                                        <td id="arr_tb_id_question<?php echo $t; ?>" class="text_10 alignCenter valignTop"  style="padding-top:3px;" onClick="javascript:disp_rev('','chbx_admin_question_tb_id<?php echo $t; ?>','arr_tb_id_question<?php echo $t; ?>')" ><img alt=""  src="<?php if($question[$t]['adminQuestionStatus']=='Yes') { echo $noneImg;}else { echo $blockImg; }?>" style="cursor:pointer; width:11px; height:13px; border:none; " /></td>
										<?php } ?>
                                    </tr>
                                </table>
                            </td>
                            <?php
                                }
							?>
                            </tr>
                            <?php	
								} 
							}//end for
                                
                            ?>					  
                        
                        
                    </table>
                </td>
            </tr>
            <tr>
                 <td style="padding-left:1px;"></td>
            </tr>
            <tr>
                <td class="valignTop">
                    <table class="all_border alignCenter" style="border:none; border-collapse:collapse; width:99%; background-color:#FFFFFF;">
						<tr><td colspan="7" style="padding-left:1px;"></td></tr>
                        <tr class="alignLeft valignMiddle" style="background-color:#F1F4F0;">
                            <td class="text_10 nowrap" style="width:200px; padding-left:10px;" >Emergency Contact Person:</td>
                            <td ></td>
                            <td class="text_10 alignLeft" style="width:400px;">
                                <input type="text" name="emergencyContactPerson" id="emergencyContactPerson" onFocus="changeTxtGroupColor(1,'emergencyContactPerson');" onKeyUp="changeTxtGroupColor(1,'emergencyContactPerson');" value="<?php echo $emergencyContactPerson; ?>" class="all_border text_10" style="width:150px;<?php if(trim(!$emergencyContactPerson)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>">
                            </td>
                            <td style="padding-left:150px;"></td>
                            <td class="text_10 alignRight" style=" padding-right:5px; padding-left:1px;">Tel.</td>
                            <td class="text_10" >
                                <input type="text"   name="emergencyContactTel" id="emergencyContactTelId" onFocus="changeTxtGroupColor(1,'emergencyContactTelId');" onKeyUp="changeTxtGroupColor(1,'emergencyContactTelId');" value="<?php echo $emergencyContactPhone; ?>" maxlength="12" onBlur="ValidatePhone(this);if(!this.value){this.style.backgroundColor='#F6C67A' }" class="all_border text_10" style="width:120px;<?php if(trim(!$emergencyContactPhone)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>">							
                            </td>
                            <td ></td>
                        </tr>
                        
                        <tr style="background-color:#FFFFFF;">
                            <td class="text_10 alignLeft nowrap"  style=" padding-left:10px; <?php if(!$patientSign) {echo $chngBckGroundColor;}?> ">Patient Signature</td>
                            <td colspan="3"></td>
                            <td class="text_10 alignRight nowrap" style=" padding-right:5px;">Witness Name </td>
                            <td>
                                <input type="text" name="witnessname" id="witnessnameId" onFocus="changeTxtGroupColor(1,'witnessnameId');" onKeyUp="changeTxtGroupColor(1,'witnessnameId');" value="<?php echo $witnessname; ?>" maxlength="15"  class="all_border text_10" style="width:140px;<?php if(trim(!$witnessname)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>">							
                            </td>
                            <?php
                            //CODE RELATED TO NURSE SIGNATURE ON FILE
                                $ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["iolink_loginUserId"]."'";
                                $ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
                                $ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
                                
                                $loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
                                $loggedInUserType = $ViewUserNameRow["user_type"];
                                $loggedInSignatureOfUser = $ViewUserNameRow["signature"];
                                
                                if($loggedInUserType<>"Nurse") {
                                    $loginUserName = $_SESSION['iolink_loginUserName'];
                                    $callJavaFun = "return noAuthorityFunCommon('Nurse');";
                                
                                }else {
                                    $loginUserId = $_SESSION["iolink_loginUserId"];
                                    //$callJavaFun = "document.frm_health_ques.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Nurse1');";
                                    $callJavaFun = "return noAuthorityFunCommon('Nurse');";
                                }
                            
                                $signOnFileStatus = "Yes";
                                $TDnurseNameIdDisplay = "block";
                                $TDnurseSignatureIdDisplay = "none";
                                $NurseNameShow = $loggedInUserName;
                                
                                if($signNurseId<>0 && $signNurseId<>"") {
                                    $NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
                                    $signOnFileStatus = $signNurseStatus;	
                                    
                                    $TDnurseNameIdDisplay = "none";
                                    $TDnurseSignatureIdDisplay = "block";
                                }
                                
                                //CODE TO REMOVE SIGNATURE
                                    if($_SESSION["iolink_loginUserId"]==$signNurseId) {
                                        //$callJavaFunDel = "document.frm_health_ques.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','pre_op_health_quest_ajaxSign.php','$loginUserId','Nurse1','delSign');";
                                        $callJavaFun = "return noAuthorityFunCommon('Nurse');";
                                    }else {
                                        $callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
                                    }
                                //END CODE TO REMOVE SIGNATURE
                            //END CODE RELATED TO NURSE SIGNATURE ON FILE
                            ?>
                            <td >
                            <!--<div id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>; " >
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="text_10b" nowrap  style="cursor:pointer;<?php echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</td>
                                    </tr>
                                </table>
                            </div>
                            <div id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>; ">	
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="text_10" nowrap align="center" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunDel;?>"><?php echo "<b>Nurse:</b>"." ".$NurseNameShow; ?></td>
                                    </tr>
                                    
                                </table>
                            </div>-->
                            
                            </td>
                            
                        </tr>
                        <!-- <tr height="20">
                            <td colspan="8"></td>
                        </tr> -->
                        <tr style="height:85px; background-color:#F1F4F0;" >
                            <!-- <td width="11%" onmouseout="get_App_Coords('app_signature')"><img src="images/tpixel.gif" width="5" height="1"> -->
                                
                                <td >
                                <input type="hidden" name="elem_signature" value="<?php echo $patientSign; ?>">
                                <!-- <applet name="app_signature" code="MyCanvasColored.class" 
                                    archive="DrawApplet.jar" width="150" height="65" 
                                    codebase="common/applet/" >
                                    <param name="bgImage" value="images/white.jpg">
                                    <param name="strpixls" value="<?php //echo $patientSign;?>">
                                    <param name="mode" value="edit">
                                </applet>
                                <img src="images/eraser.gif" onClick="return getclear_os('app_signature');"> 
                                -->
                                <table style="border:none; padding:2px;" id="tbl_sign_pt_health_id">
                                    <tr class="valignTop">
                                        <td style="width:2px;">&nbsp;</td>
                                        <td class="valignTop" >
                                
                                
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
																						if($browserPlatform == "iPad") { 
																				?>
																							<td class="valignTop consentObjectBeforSign" id="tdObjectIpadPreHlthPtSign" style="display:inline-block;background:#FFFFFF;border:solid 1px #FF9900;" >
																								<div style="width:150px; height:75px;">
																									<img alt="" style="cursor:pointer; float:right; margin-top:50px;" src="images/pen.png" id="physicianSigPen" name="physicianSigPen" onClick="OnSignIpadPhy('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','ptHealth','tbl_sign_pt_health_id','1');">
																								</div>
																							</td>    
                                        <?php		
                                            }else {
																				?>
                                        <td class="valignTop consentObjectBeforSign" id="tdObjectSigPlusPreHlthPtSign" style="display:inline-block;background:#FFFFFF;border:solid 1px #FF9900;">
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
                                        <td class="valignBottom" id="Sign_icon_PtHealth">
                                            <img alt="" style="cursor:pointer;" src="images/pen.png" id="SignBtnPtHealth" onClick="OnSignPtHealth();"><br>
                                            <img style="cursor:pointer;" title="Touch Signature" src="images/touch.svg" id="SignBtnTouchPtHealth" name="SignBtnTouchPtHealth" onclick="OnSignIpadPhy('<?php echo $patient_id;?>', '<?php echo $patient_in_waiting_id;?>', 'ptHealth', 'tbl_sign_pt_health_id', '1');"><br>
                                            <img style="cursor:pointer;" src="images/eraser.gif" id="clearSignPtHealth" alt="Clear Sign" onClick="OnClearPtHealth();">
                                        </td>
                                        	
                                        <?php
																						}
                                        }
                                        ?>								
                                    </tr>
                                </table>
                                <input type="hidden" name="SigDataPt" id="SigDataPt">
                                <input type="hidden" name="SigDataPtLoadValue" id="SigDataPtLoadValue" value="<?php echo $patientSign;?>">
                                
                            </td>
                            <td class="text_10 alignRight valignMiddle" style="width:1%;">Date:</td>
                            <td  class="text_10 alignMiddle alignLeft" style="padding-left:2px; width:12%;">
                                <input style="width:90px;" id="date1" type="text"  name="date" value="<?php if($dateQuestionnaire && $objManageData->changeDateMDY($dateQuestionnaire)!='00-00-0000'){echo $objManageData->changeDateMDY($dateQuestionnaire);}else{echo date('m-d-Y');} ?>" size='13' maxlength=10 class="all_border text_10">
                                <a href="javascript:newWindow(1);"><img alt="" src="images/icon_cal.jpg" class="valignMiddle" style="width:20px; height:20px; border:none;"></a>
                            </td>
                            <td class="alignLeft" style="padding-left:4px;"></td>
                            
                            
                            
                            <!--
                            <td class="text_10 alignRight nowrap" style="width:20%; padding-right:5px;" ><span style=" <?php if(!$witnessSign) {echo $chngBckGroundColor;}?> ">Witness Signature</span></td>
                            <td class="alignLeft valignTop nowrap">	
                                <input type="hidden" name="witnSign" value="<?php echo $witnessSign; ?>">
                                <table class="table_pad_bdr">
                                    <tr class="valignTop">
                                        <td style="width:2px;">&nbsp;</td>
                                        <?php
                                        if($witness_sign_image_path) {
                                        ?>
                                        <td class="valignTop"><img alt="" src="<?php echo $witness_sign_image_path;?>" style="width:150px; height:65px;"></td>
                                        <?php
                                        }else {
                                        ?>
                                        <td class="valignTop">
                                            <OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height="75"
                                                id="SigPlusPreHlthWtSign" name="SigPlusPreHlthWtSign"
                                                style="HEIGHT: 65px; WIDTH: 150px; LEFT: 0px; TOP: 0px;" 
                                                VIEWASTEXT>
                                                <PARAM NAME="_Version" VALUE="131095">
                                                <PARAM NAME="_ExtentX" VALUE="4842">
                                                <PARAM NAME="_ExtentY" VALUE="1323">
                                                <PARAM NAME="_StockProps" VALUE="0">
                                            </OBJECT>
                                        </td>
                                        <td class="valignBottom" id="Sign_icon_WtHealth">
                                            <img alt="" style="cursor:pointer;" src="images/pen.png" id="SignBtnWtHealth" onClick="OnSignWtHealth();"><br>
                                            <img style="cursor:pointer;" src="images/eraser.gif" id="clearSignWtHealth" alt="Clear Sign" onClick="OnClearWtHealth();">
                                        </td>	
                                        <?php
                                        }
                                        ?>								
                                    </tr>
    
                                </table>
                                <input type="hidden" name="SigDataWt" id="SigDataWt">
                                <input type="hidden" name="SigDataWtLoadValue" id="SigDataWtLoadValue" value="<?php echo $witnessSign;?>">
                                
                            </td>-->
                            <?php
                            if($witness_sign_image_path) {
                            ?>
                                <td colspan="2" class="text_10b alignLeft" style="width:48%;" >
                                    <table class="table_collapse">
                                        <tr>
                                            <td class="text_10b alignLeft">Witness&nbsp;Signature&nbsp;</td>
                                            <td class="text_10b alignLef" ><img alt="" src="<?php echo $witness_sign_image_path;?>" style="width:150px; height:65px;"></td>
                                        </tr>
                                    </table>
                                </td>
                            <?php
                            }else {
                            ?>						
                                <td colspan="2" class="text_10 alignLeft" style="width:48%;">
                                    <?php
                                        
                                        //if($signWitness1Activate=='yes' || $chkSignWitness1Activate=='yes') {
                                            //GET USER DETAIL(FOR WITNESS SIGNATURE)
                                                if($signWitness1Id) {
                                                    $ViewWitnessUserNameQry = "select * from `users` where  usersId = '".$signWitness1Id."'";
                                                    $ViewWitnessUserNameRes = imw_query($ViewWitnessUserNameQry) or die(imw_error()); 
                                                    $ViewWitnessUserNameRow = imw_fetch_array($ViewWitnessUserNameRes); 
                                                    $witnessUserType 		= $ViewWitnessUserNameRow["user_type"];
                                                }
                                            //END GET USER DETAIL(FOR WITNESS SIGNATURE)
                                            
                                            //CODE RELATED TO NURSE SIGNATURE ON FILE
                                                if($witnessUserType && ($loggedInUserType<>$witnessUserType)) {
                                                    $loginUserName = $_SESSION['loginUserName'];
                                                    $callJavaFunWitness = "return noAuthorityFunCommon('".$witnessUserType."');";
                                                }else {
                                                    $loginUserId = $_SESSION["iolink_loginUserId"];
                                                    $callJavaFunWitness = "document.frm_health_ques.hiddSignatureId.value='TDwitness1SignatureId'; return displaySignatureNew('TDwitness1NameId','TDwitness1SignatureId','','$loginUserId','Witness1','','TDwitness1SignatureDate');";
                                                }
                                            
                                                $signOnFileWitness1Status = "Yes";
                                                $TDwitness1NameIdDisplay = "inline-block";
                                                $TDwitness1SignatureIdDisplay = "none";
                                                $Witness1NameShow = $loggedInUserName;
                                                $signWitness1DateTimeFormatNew = date("m-d-Y h:i A");
                                                if($signWitness1Id<>0 && $signWitness1Id<>"") {
                                                    $Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
                                                    $signOnFileWitness1Status = $signWitness1Status;	
                                                    $TDwitness1NameIdDisplay = "none";
                                                    $TDwitness1SignatureIdDisplay = "inline-block";
													$signWitness1DateTimeFormatNew = date("m-d-Y h:i A",strtotime($signWitness1DateTime));
                                                }
                                                if($_SESSION["iolink_loginUserId"]==$signWitness1Id || (!$signWitness1Id)) {
                                                    $callJavaFunWitnessDel = "document.frm_health_ques.hiddSignatureId.value='TDwitness1NameId'; return displaySignatureNew('TDwitness1NameId','TDwitness1SignatureId','','$loginUserId','Witness1','delSign','TDwitness1SignatureDate');";
                                                }else {
                                                    $callJavaFunWitnessDel = "alert('Only $Witness1NameShow can remove this signature');";
                                                }
                                            //END CODE RELATED TO NURSE SIGNATURE ON FILE
                                        
                                        
                                    ?>
                                            
                                            <div id="TDwitness1NameId" style="display:<?php echo $TDwitness1NameIdDisplay;?>; " >
                                                <table style="border:none; border-collapse:collapse;">
                                                    <tr>
                                                        <td class="text_10b nowrap" style="cursor:pointer;<?php //echo $chngBckGroundColor;?>  " onClick="javascript:<?php echo $callJavaFunWitness;?>">Witness Signature</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div id="TDwitness1SignatureId" style="display:<?php echo $TDwitness1SignatureIdDisplay;?>; ">	
                                                <table style="border:none; border-collapse:collapse;">
                                                    <tr>
                                                        <td class="text_10 nowrap" style="cursor:pointer;" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>"><?php echo "<b>Witness:</b>"." ".$Witness1NameShow; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text_10 nowrap">
                                                            <b>Electronically Signed :&nbsp;</b>
                                                            <?php echo $signOnFileWitness1Status;?>
                                                        </td>
                                                    </tr>
                                                     <tr>
                                                    <td class="text_10 nowrap" style="cursor:pointer;" id="TDwitness1SignatureDate" onClick="javascript:<?php echo $callJavaFunWitnessDel;?>">
                                                        <b>Signature Date : </b><?php echo $signWitness1DateTimeFormatNew;?>
                                                    </td>
                                                </tr>
                                                </table>
                                            </div>
                                            <input type="hidden" name="hidd_signWitness1Activate" value="yes">	
                                </td>
							<?php
                            }
                            ?>
    
                            <td class="alignLeft" style="width:70%;">
                                <!--<div id="TDnurseSignatureOnFileId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>; ">	
                                    <table class="table_pad_bdr">
                                        
                                        <tr>
                                            <td class="text_10 nowrap">
                                                <b>Signature On File :&nbsp;</b>
                                                <?php echo $signOnFileStatus;?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>-->
                            
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
		</table>
    </form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	<form name="frm_return_BlankMainForm" method="post" action="pre_op_health_quest.php?cancelRecord=true<?php echo $saveLink;?>">
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
		<input type="hidden" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
		
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->	

<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "pre_op_health_quest.php";
	//include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($SaveForm_alert == 'true'){
	?>
	<script>
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}
?>
	<script>
		if(top.document.getElementById("anchorShow")) {
			top.document.getElementById("anchorShow").style.display = 'block';
		}
		if(top.document.getElementById("deleteSelected")) {
			top.document.getElementById("deleteSelected").style.display = 'none';
		}
		if(top.document.getElementById("PrintBtn")) {
			top.document.getElementById("PrintBtn").style.display = 'none';
		}
		if(top.document.getElementById("iolinkUploadBtn")) {
			top.document.getElementById("iolinkUploadBtn").style.display = 'none';
		}
		if(top.document.getElementById("multiUploadImgBtn")) {
			top.document.getElementById("multiUploadImgBtn").style.display = 'none';
		}
	</script>	
</body>
</html>