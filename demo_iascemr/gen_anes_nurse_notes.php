<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "genanesthesianursesnotes";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>General Anesthesia Nurses Notes</title>

<!-- Allow standalone mode on home screen. -->
<meta name="apple-mobile-web-app-capable" content="yes" />

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
	@page
	{
		size:landscape;	
	}
	.land{filter: progidXImageTransform.Microsoft.BasicImage(rotation=1)}
</style>
<?php
include_once("common/commonFunctions.php");
//include("common/linkfile.php");
$spec = '</head><body class="land" onLoad="top.changeColor(\''.$tablebg_local_anes.'\');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();">';
include("common/link_new_file.php");
include_once("admin/classObjectFunction.php"); 
$objManageData = new manageData;

$hhCnt = 12;
$dispAmPM = "inline-block";
if(constant("SHOW_MILITARY_TIME")=="YES") {
	$hhCnt = 24;
	$dispAmPM = "none";	
}
?>

<script >
	top.frames[0].yellow('<?php echo $innerKey;?>','<?php echo $preColor;?>');
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frm_gen_anes_nurse_notes.nurseSign;
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
	var coords = document.applets["app_genAnesNotesSignature"].getSign();
	return coords;
}
function getclear_os(){
	document.applets["app_genAnesNotesSignature"].clearIt();
	changeColorThis(255,0,0);
	document.applets["app_genAnesNotesSignature"].onmouseout();
}
function changeColorThis(r,g,b){				
	document.applets['app_genAnesNotesSignature'].setDrawColor(r,g,b);								
}
//Drawing Grid
var curIconTd="tdArr2";
function setAppIcon(str){
	var objTd = document.getElementById(str);
	var objCurTd = document.getElementById(curIconTd);
	var objApp = document.applets["signs"];
	if(objTd && objApp && (str != curIconTd))
	{
		setText("false");
		if(str == "tdArr1"){
			objApp.setIcon("Triangle_Down");
		}else if(str == "tdArr2"){
			objApp.setIcon("Triangle_Up");
		}else if(str == "tdRfill"){
			objApp.setIcon("Circle_Black");
		}else if(str == "tdRblank"){
			objApp.setIcon("Circle_White");
		}else if(str == "tdCross"){
			objApp.setIcon("Cross_Shape");
		}else if(str == "tdRText"){
			setText("true");			
		}			
		curIconTd = str;
		objTd.style.backgroundColor = "#FFFFCC";
		objCurTd.style.backgroundColor = objCurTd.bgColor;
	}
}

function setText(o)
{
	document.applets["signs"].activateText(o);
}

function clearApp()
{
	if(confirm("Do you want to clear drawing?")){
		document.applets["signs"].clearIt();
		getAppValue();
	}
}

function undoApp()
{
	var coords = document.applets["signs"].unDoIt();
	document.getElementById("elem_signs").value=coords;
}
function redoApp()
{
	var coords = document.applets["signs"].reDoIt();
	document.getElementById("elem_signs").value=coords;
}

function getAppValue()
{
	var coords = "";
	if(typeof(document.applets["signs"].getDrawing) != 'undefined') {
		coords = document.applets["signs"].getDrawing();
		document.getElementById("elem_signs").value=coords;
	}
} 

///Applet
//SAVE NEW NOTES WITH TIME
	
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
	
	//function ajaxtesting() {
	function save_newnotes_value() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
		
		var newNotesDesc_ajax = document.frm_gen_anes_nurse_notes.txt_areaNewNotesDesc.value
		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url="gen_anes_nurse_notes_ajax.php";
		url=url+"?txt_areaNewNotesDesc="+newNotesDesc_ajax
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
		url=url+"&preColor="+escape(preColor1)
		
		if(newNotesDesc_ajax=="") {
			alert('Please Enter New Note');
		}else {
			xmlHttp.onreadystatechange=AjaxTestingFun
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
	}
	function AjaxTestingFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("newNotesId").innerHTML=xmlHttp.responseText;
			}
	}

//END SAVE NEW NOTES WITH TIME

//delete nurse notes
 	function delentry(id){
		
		var ask_nurseNote = confirm("Are you sure to delete this note")
		if(ask_nurseNote==true){
			xmlHttp=GetXmlHttpObject();		
			if (xmlHttp==null){
	
				alert ("Browser does not support HTTP Request");
				return true;
			}		
			var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
			var url='gen_anes_nurse_notes_del_ajax.php?delId='+id+"&pConfId="+pConfId1;
			xmlHttp.onreadystatechange=AjaxTestDel
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
			}
			function AjaxTestDel(){
				if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
					document.getElementById("newNotesId").innerHTML=xmlHttp.responseText;
				}
		}
			
	}
//end delete nurse notes

// Cancel Notes 
function cancel_edit_notes(edtId,$thisHtml) {
		
	//	var $thisHtml	=	$("#noteEdtId"+edtId+"").find('textarea').val().trim();
		
		$("#noteEdtId"+edtId+"").html($thisHtml);
		
		$("#editBtnId"+edtId+"").html('<a class="btn btn-primary glyphicon glyphicon-edit margin_0"  name="del1" href="javascript:void(0)"onClick="editEntry(\''+edtId+'\');"></a>');
		
}

//START EDIT nurse notes
	function editEntry(edtId) {
		var $thisHtml	=	$("#noteEdtId"+edtId+"").html().trim();
		
		$("#noteEdtId"+edtId+"").html('<textarea name="edit_NurseNotesDesc" id="edit_NurseNotesDesc'+edtId+'" class="form-control"  style="overflow:hidden;; width:99%; padding: 1%" >'+$thisHtml+'</textarea>');
		
		$("#editBtnId"+edtId+"").html('<a class="btn btn-info" onClick="return edit_newnotes_value(\''+edtId+'\');">Update </a>&nbsp;<a class="btn btn-danger" onClick="return cancel_edit_notes(\''+edtId+'\', \''+$thisHtml+'\');">Cancel</a>');
		
}		

	function AjaxTestEdit(edtId){
		if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
			document.getElementById("edit_nurse_note_id"+edtId).innerHTML=xmlHttp.responseText;
		}
	}
	
function closeNurseNote(edtId){
	//document.getElementById('edit_nurse_note_id'+edtId).style.display = 'block';
	//alert("edit_nurse_note_id"+edtId);
	if(document.getElementById("edit_nurse_note_id"+edtId)) {
		document.getElementById("edit_nurse_note_id"+edtId).innerHTML='';
		document.getElementById('edit_nurse_note_id'+edtId).style.display = 'none';
	}	
}
//EDIT NURSE NOTE

	function edit_newnotes_value(editId){
		xmlHttp=GetXmlHttpObject();		
		if (xmlHttp==null){

			alert ("Browser does not support HTTP Request");
			return true;
		}
		var newNotesDescEdit_ajax = document.getElementById("edit_NurseNotesDesc"+editId).value;

		var pConfId2 = '<?php echo $_REQUEST["pConfId"];?>';
		var url='gen_anes_nurse_notes_edit_pop.php?editId='+editId+"&pConfId="+pConfId2+"&desc_edit_nurseNote="+newNotesDescEdit_ajax;
		xmlHttp.onreadystatechange=AjaxTestEditNew;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null);	
	}
	function AjaxTestEditNew(){
		if (xmlHttp.readyState==4  || xmlHttp.readyState=="complete"){
			document.getElementById("newNotesId").innerHTML=xmlHttp.responseText;
		}
	}

//SAVE RECOVERY ROOM MEDS
function saveRecoveryRoomMeds() {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
		
		var recovery_drugs_ajax = document.frm_gen_anes_nurse_notes.txt_recovery_drugs.value
		var recovery_dose_ajax = document.frm_gen_anes_nurse_notes.txt_recovery_dose.value
		var recovery_route_ajax = document.frm_gen_anes_nurse_notes.txt_recovery_route.value
		var recovery_time_ajax = document.frm_gen_anes_nurse_notes.txt_recovery_time.value
		var recovery_initial_ajax = document.frm_gen_anes_nurse_notes.txt_recovery_initial.value

		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url="gen_anes_nurse_notes_recovery_ajax.php";
		url=url+"?txt_recovery_drugs="+recovery_drugs_ajax
		url=url+"&txt_recovery_dose="+recovery_dose_ajax
		url=url+"&txt_recovery_route="+recovery_route_ajax
		url=url+"&txt_recovery_time="+recovery_time_ajax
		url=url+"&txt_recovery_initial="+recovery_initial_ajax
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
			xmlHttp.onreadystatechange=recoveryRoomFun
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
	}
	function recoveryRoomFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				document.getElementById("recoveryRoom_id").innerHTML=xmlHttp.responseText;
			}
	}
	
//END SAVE RECOVERY ROOM MEDS
function saveIntake() {

		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
		
		var intake_fluids_ajax = document.frm_gen_anes_nurse_notes.txt_intake_fluids.value
		var intake_amount_given_ajax = document.frm_gen_anes_nurse_notes.txt_intake_amount_given.value

		var thisId1 = '<?php echo $_REQUEST["thisId"];?>';
		var innerKey1 = '<?php echo $_REQUEST["innerKey"];?>';
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url="gen_anes_nurse_notes_intake_ajax.php";
		url=url+"?txt_intake_fluids="+intake_fluids_ajax
		url=url+"&txt_intake_amount_given="+intake_amount_given_ajax
		url=url+"&thisId="+thisId1
		url=url+"&innerKey="+innerKey1
		url=url+"&preColor="+preColor1
		url=url+"&patient_id="+patient_id1
		url=url+"&pConfId="+pConfId1
			xmlHttp.onreadystatechange=intakeRoomFun
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
}
function intakeRoomFun() {
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		{ 
			document.getElementById("intakeRoom_id").innerHTML=xmlHttp.responseText;
		}
}

function checkNurseNotesUserType(nurseNotesUserName) {
	
	var nurseNotesUserNameValue = nurseNotesUserName.value;
	
	<?PHP if($AnesthesiologistDisplay == 'none') { ?>
					$("select#nurseNotes_AnesthesiologistId").selectpicker("hide");
			<?PHP } ?>
			
			<?PHP if($NurseDisplay == 'none') { ?>
					$("select#nurseNotes_NurseId").selectpicker("hide");
			<?PHP } ?>
				
	if(nurseNotesUserNameValue=="Anesthesiologist")	{
			
			$("select#nurseNotes_AnesthesiologistId").selectpicker("show");
			$("select#nurseNotes_NurseId").selectpicker("hide");
			$("select#nurseNotes_BlankId").selectpicker("hide");
						
		//document.getElementById("nurseNotes_AnesthesiologistId")..style.display="block";
		//document.getElementById("nurseNotes_NurseId").style.display="none";
		//document.getElementById("nurseNotes_BlankId").style.display="none";
	
	}else if(nurseNotesUserNameValue=="Nurse")	{
			$("select#nurseNotes_AnesthesiologistId").selectpicker("hide");
			$("select#nurseNotes_NurseId").selectpicker("show");
			$("select#nurseNotes_BlankId").selectpicker("hide");
			
		/*document.getElementById("nurseNotes_AnesthesiologistId").style.display="none";
		document.getElementById("nurseNotes_NurseId").style.display="block";
		document.getElementById("nurseNotes_BlankId").style.display="none";*/
	}else if(nurseNotesUserNameValue=="")	{
			
			$("select#nurseNotes_AnesthesiologistId").selectpicker("hide");
			$("select#nurseNotes_NurseId").selectpicker("hide");
			$("select#nurseNotes_BlankId").selectpicker("show");
			
		/*document.getElementById("nurseNotes_AnesthesiologistId").style.display="none";
		document.getElementById("nurseNotes_NurseId").style.display="none";
		document.getElementById("nurseNotes_BlankId").style.display="block";*/
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
		var preColor1 = '<?php echo $_REQUEST["preColor"];?>';
		var patient_id1 = '<?php echo $_REQUEST["patient_id"];?>';
		var pConfId1 = '<?php echo $_REQUEST["pConfId"];?>';
		var url=pagename
		url=url+"?loggedInUserId="+loggedInUserId
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
			}
	}
	
	//End Display Signature Of Nurse
	
//END FUNCTIONS RELATED TO DISPLAY NURSE SIGNATURE

//FUNCTION TO CHECK IF HIDE OR DISPLAY
	function chk_hide_show(id) {
		var k=document.frm_gen_anes_nurse_notes.rowK.value;
		if(document.getElementById(id).style.display=="block") {
			++k;
		}else {
			--k;
		}
		
		document.frm_gen_anes_nurse_notes.rowK.value=k;
		
	}
//END FUNCTION TO CHECK IF HIDE OR DISPLAY
</script>
<?php

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
	
	$fieldName = "genral_anesthesia_nurses_notes_form";
	$pageName = "gen_anes_nurse_notes.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	if($_REQUEST["cancelRecord"]=="true") {  //IF PRESS CANCEL BUTTON
		$pageName = "blankform.php?patient_id=$patient_id&amp;pConfId=$pConfId";
	}
	include("left_link_hide.php");
//END CODE TO DISABLE SLIDER LINK AT SINGLE CLICK 
$saveLink = '&amp;thisId='.$thisId.'&amp;innerKey='.$innerKey.'&amp;preColor='.$preColor.'&amp;patient_id='.$patient_id.'&amp;pConfId='.$pConfId;

//GET CURRENTLY LOGGED IN NURSE ID
	$currentLoggedinNurseQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
	$currentLoggedinNurseRes = imw_query($currentLoggedinNurseQry) or die(imw_error()); 
	$currentLoggedinNurseRow = imw_fetch_array($currentLoggedinNurseRes); 
	$currentLoggedinNurseId = $currentLoggedinNurseRow["nurseId"];
	
	//GET CURRENTLY LOGGED IN NURSE NAME
		$ViewNurseNameQry = "select * from `users` where  usersId = '".$currentLoggedinNurseId."'";
		$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
		$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
		$currentLoggedinNurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
		$currentLoggedinNurseSignature = $ViewNurseNameRow["signature"];
	//END GET CURRENTLY LOGGED IN NURSE NAME

//END GET CURRENTLY LOGGED IN NURSE ID

//FUNCTION TO GET TIME FROM HIDDEN VALUE
	function get_timeValue($hidd_timeValue) {
		global $objManageData;
		if($hidd_timeValue<>'00:00:00' && $hidd_timeValue<>'') {
			$mainTime = $objManageData->getTmFormat($hidd_timeValue);
			return $mainTime;
			/*
			$time_split = explode(":",$hidd_timeValue);
			if($time_split[0]>=12) {
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
			$mainTime = $time_split[0].":".$time_split[1]." ".$am_pm;
			*/
			
		}	
	}
//END FUNCTION TO GET TIME FROM HIDDEN VALUE

//FUNCTION TO SAVE TIME IN DATABASE
	function SaveTime($txt_NurseTime) {
	   global $objManageData;
	   if($txt_NurseTime<>"") {
		   $txt_NurseTime = $objManageData->setTmFormat($txt_NurseTime);
		   return $txt_NurseTime;
		   /*
		   $time_splitNotes = explode(" ",$txt_NurseTime);
		   
			if($time_splitNotes[1]=="PM" || $time_splitNotes[1]=="pm") {
				
				$time_splitNotestime = explode(":",$time_splitNotes[0]);
				$txt_NurseTimeIncr=$time_splitNotestime[0]+12;
				$txt_NurseTime = $txt_NurseTimeIncr.":".$time_splitNotestime[1].":00";
				
			}elseif($time_splitNotes[1]=="AM" || $time_splitNotes[1]=="am") {
				$time_splitNotestime = explode(":",$time_splitNotes[0]);
				$txt_NurseTime=$time_splitNotestime[0].":".$time_splitNotestime[1].":00";
				
				if($time_splitNotestime[0]=="00" && $time_splitNotestime[1]=="00") {
					$txt_NurseTime=$time_splitNotestime[0].":".$time_splitNotestime[1].":01";
				}
			}
			*/
			
		}	
	}	
//FUNCTION TO SAVE TIME IN DATABASE

//SAVE RECORD TO DATABASE
if($_POST['SaveRecordForm']=='yes'){
	$text = $_REQUEST['getText'];
	$tablename = "genanesthesianursesnotes";
	
	$anesthesiaGeneral = $_POST["chbx_anesthesiaGeneral"];
	$anesthesiaRegional = $_POST["chbx_anesthesiaRegional"];
	$anesthesiaEpidural = $_POST["chbx_anesthesiaEpidural"];
	$anesthesiaMAC = $_POST["chbx_anesthesiaMAC"];
	$anesthesiaLocal = $_POST["chbx_anesthesiaLocal"];
	$anesthesiaSpinal = $_POST["chbx_anesthesiaSpinal"];
	$anesthesiaSensationAt = $_POST["chbx_anesthesiaSensationAt"];
	$anesthesiaSensationAtDesc = $_POST["txt_anesthesiaSensationAt"];
	if($anesthesiaSensationAt<>"Yes") {
		$anesthesiaSensationAtDesc = "";
	}
	$genNotesSignApplet = $_POST["elem_signs"];
	
	$temp = $_POST["txt_temp"];
	$o2Sat = $_POST["txt_o2Sat"];
	$painScale = $_POST["txt_painScale"];
	
	$intake_site1 = $_POST["txt_site1"];
	$intake_site2 = $_POST["txt_site2"];
	$intake_site3 = $_POST["txt_site3"];
	$intake_site4 = $_POST["txt_site4"];
	$intake_site5 = $_POST["txt_site5"];
	$intake_site6 = $_POST["txt_site6"];
	$intake_site7 = $_POST["txt_site7"];
	
	$intake_solution1 = $_POST["txt_solution1"];
	$intake_solution2 = $_POST["txt_solution2"];
	$intake_solution3 = $_POST["txt_solution3"];
	$intake_solution4 = $_POST["txt_solution4"];
	$intake_solution5 = $_POST["txt_solution5"];
	$intake_solution6 = $_POST["txt_solution6"];
	$intake_solution7 = $_POST["txt_solution7"];
	
	$intake_credit1 = $_POST["txt_credit1"];
	$intake_credit2 = $_POST["txt_credit2"];
	$intake_credit3 = $_POST["txt_credit3"];
	$intake_credit4 = $_POST["txt_credit4"];
	$intake_credit5 = $_POST["txt_credit5"];
	$intake_credit6 = $_POST["txt_credit6"];
	$intake_credit7 = $_POST["txt_credit7"];   
	
	
	
	$alterTissuePerfusionTime1 = SaveTime($_POST["alterTissuePerfusionTime1"]);
	$alterTissuePerfusionTime2 = SaveTime($_POST["alterTissuePerfusionTime2"]);
	$alterTissuePerfusionTime3 = SaveTime($_POST["alterTissuePerfusionTime3"]);
	
	$alterTissuePerfusionInitials1 = $_POST["alterTissuePerfusionInitials1_list"];
	$alterTissuePerfusionInitials2 = $_POST["alterTissuePerfusionInitials2_list"];
	$alterTissuePerfusionInitials3 = $_POST["alterTissuePerfusionInitials3_list"];
	
	$alterGasExchangeTime1 = SaveTime($_POST["alterGasExchangeTime1"]);
	$alterGasExchangeTime2 = SaveTime($_POST["alterGasExchangeTime2"]);
	$alterGasExchangeTime3 = SaveTime($_POST["alterGasExchangeTime3"]);
	
	$alterGasExchangeInitials1 = $_POST["alterGasExchangeInitials1_list"];
	$alterGasExchangeInitials2 = $_POST["alterGasExchangeInitials2_list"];
	$alterGasExchangeInitials3 = $_POST["alterGasExchangeInitials3_list"];
	
	$alterComfortTime1 = SaveTime($_POST["alterComfortTime1"]);
	$alterComfortTime2 = SaveTime($_POST["alterComfortTime2"]);
	$alterComfortTime3 = SaveTime($_POST["alterComfortTime3"]);
	
	$alterComfortInitials1 = $_POST["alterComfortInitials1_list"];
	$alterComfortInitials2 = $_POST["alterComfortInitials2_list"];
	$alterComfortInitials3 = $_POST["alterComfortInitials3_list"];

	$monitorTissuePerfusion = $_POST["chbx_monitorTissuePerfusion"];
	$implementComplicationMeasure = $_POST["chbx_implementComplicationMeasure"];
	$assessPlus = $_POST["chbx_assessPlus"];
	$telemetry = $_POST["chbx_telemetry"];
	$otherTPNurseInter = $_POST["chbx_otherTPNurseInter"];
	$otherTPNurseInterDesc = addslashes($_POST["txt_otherTPNurseInterDesc"]);
	if($otherTPNurseInter<>"Yes") {
		$otherTPNurseInterDesc = "";
	}
	$assessRespiratoryStatus = $_POST["chbx_assessRespiratoryStatus"];
	$positionOptimalChestExcusion = $_POST["chbx_positionOptimalChestExcusion"];
	$monitorOxygenationGE = $_POST["chbx_monitorOxygenationGE"];
	$otherGENurseInter = $_POST["chbx_otherGENurseInter"];
	$otherGENurseInterDesc = addslashes($_POST["txt_otherGENurseInterDesc"]);
	if($otherGENurseInter<>"Yes") {
		$otherGENurseInterDesc = "";
	}
	$assessPain = $_POST["chbx_assessPain"];
	$usePharmacology = $_POST["chbx_usePharmacology"];
	$monitorOxygenationComfort = $_POST["chbx_monitorOxygenationComfort"];
	$otherComfortNurseInter = $_POST["chbx_otherComfortNurseInter"];
	$otherComfortNurseInterDesc = addslashes($_POST["txt_otherComfortNurseInterDesc"]); 
	if($otherComfortNurseInter<>"Yes") {
		$otherComfortNurseInterDesc = "";
	}

	$goalTPAchieveTime1 = SaveTime($_POST["goalTPAchieveTime1"]); 
	$goalTPArchieve1 = $_POST["chbx_tiss_archive1"]; 
	$goalTPAchieveInitial1  = $_POST["goalTPAchieveInitial1_list"]; 
	
	$goalTPAchieveTime2 = SaveTime($_POST["goalTPAchieveTime2"]); 
	$goalTPAchieve2 = $_POST["chbx_tiss_archive2"]; 
	$goalTPAchieveInitial2  = $_POST["goalTPAchieveInitial2_list"]; 
	
	$goalGEAchieveTime1 = SaveTime($_POST["goalGEAchieveTime1"]); 
	$goalGEAchieve1 = $_POST["chbx_adq_exc1"]; 
	$goalGEAchieveInitial1  = $_POST["goalGEAchieveInitial1_list"]; 

	$goalGEAchieveTime2 = SaveTime($_POST["goalGEAchieveTime2"]); 
	$goalGEAchieve2 = $_POST["chbx_adq_exc2"]; 
	$goalGEAchieveInitial2  = $_POST["goalGEAchieveInitial2_list"]; 
	
	$goalCRPAchieveTime1 = SaveTime($_POST["goalCRPAchieveTime1"]); 
	$goalCRPAchieve1 = $_POST["chbx_excs_pain1"]; 
	$goalCRPAchieveInitial1  = $_POST["goalCRPAchieveInitial1_list"]; 
	
	$goalCRPAchieveTime2 = SaveTime($_POST["goalCRPAchieveTime2"]); 
	$goalCRPAchieve2 = $_POST["chbx_excs_pain2"]; 
	$goalCRPAchieveInitial2  = $_POST["goalCRPAchieveInitial2_list"]; 
	
	$dischargeSummaryTemp = $_POST["txt_dischargeSummaryTemp"];
	$dischangeSummaryBp = $_POST["txt_dischangeSummaryBp"];
	$dischargeSummaryP = $_POST["txt_dischargeSummaryP"];
	$dischangeSummaryRR = $_POST["txt_dischangeSummaryRR"];
	
	$alert = $_POST["chbx_alrt"];
	$hasTakenNourishment = $_POST["chbx_htn"];
	$nauseasVomiting = $_POST["chbx_nau_vom"];
	$voidedQs = $_POST["chbx_voi_qs"];
	$panv = $_POST["chbx_pan"];
	$dressing = $_POST["txt_dressing"];
	$dischargeSummaryOther = $_POST["txt_dischargeSummaryOther"];
	$nurseId = $_POST["hidd_currentLoggedinNurseId"];
	$nurseSign = $_POST["nurseSign"];
	
	$whoUserType = $_POST["whoUserTypeList"];
	if($_POST['whoUserTypeList']=="") {
		$createdByUserIdList = $_POST['nurseNotesBlankId_list'];
	}else if($_POST['whoUserTypeList']=="Anesthesiologist") {
		$createdByUserIdList = $_POST['nurseNotesAnesthesiologistId_list'];
	}else if($_POST['whoUserTypeList']=="Nurse") {
		$createdByUserIdList = $_POST['nurseNotesNurseId_list'];
	}
	$createdByUserId = $createdByUserIdList;
	$relivedNurseId = $_POST["relivedNurseIdList"];
	
	$list_hour = $_POST["list_hour"];
	$list_min = $_POST["list_min"];
	$list_ampm = $_POST["list_ampm"];
	if($list_hour<>"" && $list_min<>"") {
		if($list_ampm=="PM") {
			$list_hour = $list_hour+$hhCnt;
		}
		$dischargeAt  = $list_hour.":".$list_min;
	}
	$comments = addslashes(trim($_POST["txtarea_comments"]));
	
	//RECOVERY ROOM MEDS
	$recovery_room_na = $_POST["recovery_room_na"]; 
	$recovery_new_drugs1 = $_POST["txt_recovery_new_drugs1"];
	$recovery_new_drugs2 = $_POST["txt_recovery_new_drugs2"];
	$recovery_new_drugs3 = $_POST["txt_recovery_new_drugs3"];
	$recovery_new_drugs4 = $_POST["txt_recovery_new_drugs4"];
	
	$recovery_new_dose1 = $_POST["txt_recovery_new_dose1"];
	$recovery_new_dose2 = $_POST["txt_recovery_new_dose2"];
	$recovery_new_dose3 = $_POST["txt_recovery_new_dose3"];
	$recovery_new_dose4 = $_POST["txt_recovery_new_dose4"];

	$recovery_new_route1 = $_POST["txt_recovery_new_route1"];
	$recovery_new_route2 = $_POST["txt_recovery_new_route2"];
	$recovery_new_route3 = $_POST["txt_recovery_new_route3"];
	$recovery_new_route4 = $_POST["txt_recovery_new_route4"];
	
	$recovery_new_time1 = SaveTime($_POST["txt_recovery_new_time1"]);
	$recovery_new_time2 = SaveTime($_POST["txt_recovery_new_time2"]);
	$recovery_new_time3 = SaveTime($_POST["txt_recovery_new_time3"]);
	$recovery_new_time4 = SaveTime($_POST["txt_recovery_new_time4"]);

	
	$recovery_new_initial1 = $_POST["txt_recovery_new_initial1"];
	$recovery_new_initial2 = $_POST["txt_recovery_new_initial2"];
	$recovery_new_initial3 = $_POST["txt_recovery_new_initial3"];
	$recovery_new_initial4 = $_POST["txt_recovery_new_initial4"];
	//RECOVERY ROOM MEDS
	
	//INTAKE
	$intake_new_fluids1 = $_POST["txt_intake_new_fluids1"];
	$intake_new_fluids2 = $_POST["txt_intake_new_fluids2"];
	$intake_new_fluids3 = $_POST["txt_intake_new_fluids3"];
	
	$intake_new_amount_given1 = $_POST["txt_intake_new_amount_given1"];
	$intake_new_amount_given2 = $_POST["txt_intake_new_amount_given2"];
	$intake_new_amount_given3 = $_POST["txt_intake_new_amount_given3"];
	//INTAKE
	
	//START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
		$chkUserSignDetails = $objManageData->getRowRecord('genanesthesianursesnotes', 'confirmation_id', $pConfId);
		if($chkUserSignDetails) {
			$chk_signNurseId = $chkUserSignDetails->signNurseId;
		}
	//END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if(($anesthesiaGeneral=="" && $anesthesiaRegional=="" && $anesthesiaEpidural==""
			&& $anesthesiaMAC=="" && $anesthesiaLocal=="" && $anesthesiaSpinal=="" && $anesthesiaSensationAt==""
		   )
		 ||($recovery_room_na=="" && $recovery_new_drugs1=="" && $recovery_new_drugs2==""
		 	 && $recovery_new_drugs3=="" && $recovery_new_drugs4=="")
		 ||($intake_new_fluids1=="" && $intake_new_fluids2=="" && $intake_new_fluids3=="")
		 
		 ||($alterTissuePerfusionTime1=="" && $alterTissuePerfusionTime2=="" && $alterTissuePerfusionTime3==""
			&& $alterTissuePerfusionInitials1=="" && $alterTissuePerfusionInitials2=="" && $alterTissuePerfusionInitials3==""
		   ) 
		 ||($alterGasExchangeTime1=="" && $alterGasExchangeTime2=="" && $alterGasExchangeTime3==""
			&& $alterGasExchangeInitials1=="" && $alterGasExchangeInitials2=="" && $alterGasExchangeInitials3==""
		   )
		 ||($alterComfortTime1=="" && $alterComfortTime2=="" && $alterComfortTime3==""
			&& $alterComfortInitials1=="" && $alterComfortInitials2=="" && $alterComfortInitials3==""
		   )
		 || ($monitorTissuePerfusion=="" && $implementComplicationMeasure==""
		 	 && $assessPlus=="" && $telemetry==""  && $otherTPNurseInter=="")
		 || ($assessRespiratoryStatus=="" && $positionOptimalChestExcusion==""
		 	 && $monitorOxygenationGE=="" && $otherGENurseInter=="")
		 || ($assessPain=="" && $usePharmacology==""
		 	 && $monitorOxygenationComfort=="" && $otherComfortNurseInter=="")
		 
		 || $goalTPArchieve1=="" || $goalTPAchieve2=="" || $goalGEAchieve1==""
		 || $goalGEAchieve2=="" || $goalCRPAchieve1=="" || $goalCRPAchieve2==""
		 
		 || $alert=="" || $hasTakenNourishment=="" || $nauseasVomiting==""
		 || $voidedQs=="" || $panv=="" 
		 
		 || ($dischargeSummaryTemp=="" && $dischangeSummaryBp=="" && $dischargeSummaryP==""
		 	 && $dischangeSummaryRR=="" && trim($dressing)=="" && $dischargeSummaryOther==""
			)
		 || $dischargeAt=="" || $createdByUserId=="" 
		 || $chk_signNurseId=="0"
		 
		)
 
		{
			$form_status = "not completed";
		}
		
		
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	//START CODE TO RESET THE RECORD
	$resetRecordQry='';
	if($_REQUEST['hiddResetStatusId']=='Yes') {
		$form_status 		= 	'';
		$genNotesSignApplet	=	'';
		$resetRecordQry 	= 	"signNurseId		= '',
								 signNurseFirstName	= '', 
								 signNurseMiddleName= '',
								 signNurseLastName	= '', 
								 signNurseStatus	= '',
								 signNurseDateTime	= '',
								 resetDateTime 		= '".date('Y-m-d H:i:s')."',
								 resetBy 			= '".$_SESSION['loginUserId']."',
								";
		
	}
	//END CODE TO RESET THE RECORD
	
	$chkNurseNotesQry = "select * from `genanesthesianursesnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkNurseNotesRes = imw_query($chkNurseNotesQry) or die(imw_error()); 
	$chkNurseNotesNumRow = imw_num_rows($chkNurseNotesRes);
	if($chkNurseNotesNumRow>0) {
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
			$chkFormStatusRow = imw_fetch_array($chkNurseNotesRes);
			$chk_form_status = $chkFormStatusRow['form_status'];
		//CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
		
		$SaveNurseNotesQry = "update `genanesthesianursesnotes` set 
									anesthesiaGeneral = '$anesthesiaGeneral',
									anesthesiaRegional = '$anesthesiaRegional',
									anesthesiaEpidural = '$anesthesiaEpidural',
									anesthesiaMAC = '$anesthesiaMAC',
									anesthesiaLocal = '$anesthesiaLocal',
									anesthesiaSpinal = '$anesthesiaSpinal',
									anesthesiaSensationAt = '$anesthesiaSensationAt',
									anesthesiaSensationAtDesc = '$anesthesiaSensationAtDesc',
									genNotesSignApplet = '$genNotesSignApplet',
									temp = '$temp',
									o2Sat = '$o2Sat',
									painScale = '$painScale',
									intake_site1 = '$intake_site1',
									intake_site2 = '$intake_site2',
									intake_site3 = '$intake_site3',
									intake_site4 = '$intake_site4',
									intake_site5 = '$intake_site5',
									intake_site6 = '$intake_site6',
									intake_site7 = '$intake_site7',
									intake_solution1 = '$intake_solution1',
									intake_solution2 = '$intake_solution2',
									intake_solution3 = '$intake_solution3',
									intake_solution4 = '$intake_solution4',
									intake_solution5 = '$intake_solution5',
									intake_solution6 = '$intake_solution6',
									intake_solution7 = '$intake_solution7',
									intake_credit1 = '$intake_credit1',
									intake_credit2 = '$intake_credit2',
									intake_credit3 = '$intake_credit3',
									intake_credit4 = '$intake_credit4',
									intake_credit5 = '$intake_credit5',
									intake_credit6 = '$intake_credit6',
									intake_credit7 = '$intake_credit7',
									alterTissuePerfusionTime1 = '$alterTissuePerfusionTime1',
									alterTissuePerfusionTime2 = '$alterTissuePerfusionTime2',
									alterTissuePerfusionTime3 = '$alterTissuePerfusionTime3',
									alterTissuePerfusionInitials1 = '$alterTissuePerfusionInitials1',
									alterTissuePerfusionInitials2 = '$alterTissuePerfusionInitials2',
									alterTissuePerfusionInitials3 = '$alterTissuePerfusionInitials3',
									alterGasExchangeTime1 = '$alterGasExchangeTime1',
									alterGasExchangeTime2 = '$alterGasExchangeTime2',
									alterGasExchangeTime3 = '$alterGasExchangeTime3',
									alterGasExchangeInitials1 = '$alterGasExchangeInitials1',
									alterGasExchangeInitials2 = '$alterGasExchangeInitials2',
									alterGasExchangeInitials3 = '$alterGasExchangeInitials3',
									alterComfortTime1 = '$alterComfortTime1',
									alterComfortTime2 = '$alterComfortTime2',
									alterComfortTime3 = '$alterComfortTime3',
									alterComfortInitials1 = '$alterComfortInitials1',
									alterComfortInitials2 = '$alterComfortInitials2',
									alterComfortInitials3 = '$alterComfortInitials3',
									monitorTissuePerfusion = '$monitorTissuePerfusion',
									implementComplicationMeasure = '$implementComplicationMeasure',
									assessPlus = '$assessPlus',
									telemetry = '$telemetry',
									otherTPNurseInter = '$otherTPNurseInter',
									otherTPNurseInterDesc = '$otherTPNurseInterDesc',
									assessRespiratoryStatus = '$assessRespiratoryStatus',
									positionOptimalChestExcusion = '$positionOptimalChestExcusion',
									monitorOxygenationGE = '$monitorOxygenationGE',
									otherGENurseInter = '$otherGENurseInter',
									otherGENurseInterDesc = '$otherGENurseInterDesc',
									assessPain = '$assessPain',
									usePharmacology = '$usePharmacology',
									monitorOxygenationComfort = '$monitorOxygenationComfort',
									otherComfortNurseInter = '$otherComfortNurseInter',
									otherComfortNurseInterDesc = '$otherComfortNurseInterDesc',
									goalTPArchieve1 = '$goalTPArchieve1',
									goalTPAchieveTime1 = '$goalTPAchieveTime1',
									goalTPAchieveInitial1  = '$goalTPAchieveInitial1',
									goalTPAchieve2 = '$goalTPAchieve2',
									goalTPAchieveTime2 = '$goalTPAchieveTime2',
									goalTPAchieveInitial2  = '$goalTPAchieveInitial2',
									goalGEAchieve1 = '$goalGEAchieve1',
									goalGEAchieveTime1 = '$goalGEAchieveTime1',
									goalGEAchieveInitial1  = '$goalGEAchieveInitial1',
									goalGEAchieve2 = '$goalGEAchieve2',
									goalGEAchieveTime2 = '$goalGEAchieveTime2',
									goalGEAchieveInitial2  = '$goalGEAchieveInitial2',
									goalCRPAchieve1 = '$goalCRPAchieve1',
									goalCRPAchieveTime1 = '$goalCRPAchieveTime1',
									goalCRPAchieveInitial1  = '$goalCRPAchieveInitial1',
									goalCRPAchieve2 = '$goalCRPAchieve2',
									goalCRPAchieveTime2 = '$goalCRPAchieveTime2',
									goalCRPAchieveInitial2  = '$goalCRPAchieveInitial2',
									dischargeSummaryTemp = '$dischargeSummaryTemp',
									dischangeSummaryBp = '$dischangeSummaryBp',
									dischargeSummaryP = '$dischargeSummaryP',
									dischangeSummaryRR = '$dischangeSummaryRR',
									alert = '$alert',
									hasTakenNourishment = '$hasTakenNourishment',
									nauseasVomiting = '$nauseasVomiting',
									voidedQs = '$voidedQs',
									panv = '$panv',
									dressing = '$dressing',
									dischargeSummaryOther = '$dischargeSummaryOther',
									dischargeAt = '$dischargeAt',
									comments = '$comments',
									nurseId = '$nurseId',
									nurseSign = '$nurseSign',
									whoUserType = '$whoUserType',
									createdByUserId = '$createdByUserId',
									relivedNurseId = '$relivedNurseId',
									recovery_room_na='$recovery_room_na',
									recovery_new_drugs1 = '$recovery_new_drugs1',
									recovery_new_drugs2 = '$recovery_new_drugs2',
									recovery_new_drugs3 = '$recovery_new_drugs3',
									recovery_new_drugs4 = '$recovery_new_drugs4',
									recovery_new_dose1 = '$recovery_new_dose1',
									recovery_new_dose2 = '$recovery_new_dose2',
									recovery_new_dose3 = '$recovery_new_dose3',
									recovery_new_dose4 = '$recovery_new_dose4',
									recovery_new_route1 = '$recovery_new_route1',
									recovery_new_route2 = '$recovery_new_route2',
									recovery_new_route3 = '$recovery_new_route3',
									recovery_new_route4 = '$recovery_new_route4',
									recovery_new_time1 = '$recovery_new_time1',
									recovery_new_time2 = '$recovery_new_time2',
									recovery_new_time3 = '$recovery_new_time3',
									recovery_new_time4 = '$recovery_new_time4',
									recovery_new_initial1 = '$recovery_new_initial1',
									recovery_new_initial2 = '$recovery_new_initial2',
									recovery_new_initial3 = '$recovery_new_initial3',
									recovery_new_initial4 = '$recovery_new_initial4',
									intake_new_fluids1 = '$intake_new_fluids1',
									intake_new_fluids2 = '$intake_new_fluids2',
									intake_new_fluids3 = '$intake_new_fluids3',
									intake_new_amount_given1 = '$intake_new_amount_given1',
									intake_new_amount_given2 = '$intake_new_amount_given2',
									intake_new_amount_given3 = '$intake_new_amount_given3',
									$resetRecordQry
									form_status ='".$form_status."'
									WHERE confirmation_id='".$_REQUEST["pConfId"]."'";
	}else {
		$SaveNurseNotesQry = "insert into `genanesthesianursesnotes` set 
									anesthesiaGeneral = '$anesthesiaGeneral',
									anesthesiaRegional = '$anesthesiaRegional',
									anesthesiaEpidural = '$anesthesiaEpidural',
									anesthesiaMAC = '$anesthesiaMAC',
									anesthesiaLocal = '$anesthesiaLocal',
									anesthesiaSpinal = '$anesthesiaSpinal',
									anesthesiaSensationAt = '$anesthesiaSensationAt',
									anesthesiaSensationAtDesc = '$anesthesiaSensationAtDesc',
									genNotesSignApplet = '$genNotesSignApplet',
									temp = '$temp',
									o2Sat = '$o2Sat',
									painScale = '$painScale',
									intake_site1 = '$intake_site1',
									intake_site2 = '$intake_site2',
									intake_site3 = '$intake_site3',
									intake_site4 = '$intake_site4',
									intake_site5 = '$intake_site5',
									intake_site6 = '$intake_site6',
									intake_site7 = '$intake_site7',
									intake_solution1 = '$intake_solution1',
									intake_solution2 = '$intake_solution2',
									intake_solution3 = '$intake_solution3',
									intake_solution4 = '$intake_solution4',
									intake_solution5 = '$intake_solution5',
									intake_solution6 = '$intake_solution6',
									intake_solution7 = '$intake_solution7',
									intake_credit1 = '$intake_credit1',
									intake_credit2 = '$intake_credit2',
									intake_credit3 = '$intake_credit3',
									intake_credit4 = '$intake_credit4',
									intake_credit5 = '$intake_credit5',
									intake_credit6 = '$intake_credit6',
									intake_credit7 = '$intake_credit7',
									alterTissuePerfusionTime1 = '$alterTissuePerfusionTime1',
									alterTissuePerfusionTime2 = '$alterTissuePerfusionTime2',
									alterTissuePerfusionTime3 = '$alterTissuePerfusionTime3',
									alterTissuePerfusionInitials1 = '$alterTissuePerfusionInitials1',
									alterTissuePerfusionInitials2 = '$alterTissuePerfusionInitials2',
									alterTissuePerfusionInitials3 = '$alterTissuePerfusionInitials3',
									alterGasExchangeTime1 = '$alterGasExchangeTime1',
									alterGasExchangeTime2 = '$alterGasExchangeTime2',
									alterGasExchangeTime3 = '$alterGasExchangeTime3',
									alterGasExchangeInitials1 = '$alterGasExchangeInitials1',
									alterGasExchangeInitials2 = '$alterGasExchangeInitials2',
									alterGasExchangeInitials3 = '$alterGasExchangeInitials3',
									alterComfortTime1 = '$alterComfortTime1',
									alterComfortTime2 = '$alterComfortTime2',
									alterComfortTime3 = '$alterComfortTime3',
									alterComfortInitials1 = '$alterComfortInitials1',
									alterComfortInitials2 = '$alterComfortInitials2',
									alterComfortInitials3 = '$alterComfortInitials3',
									monitorTissuePerfusion = '$monitorTissuePerfusion',
									implementComplicationMeasure = '$implementComplicationMeasure',
									assessPlus = '$assessPlus',
									telemetry = '$telemetry',
									otherTPNurseInter = '$otherTPNurseInter',
									otherTPNurseInterDesc = '$otherTPNurseInterDesc',
									assessRespiratoryStatus = '$assessRespiratoryStatus',
									positionOptimalChestExcusion = '$positionOptimalChestExcusion',
									monitorOxygenationGE = '$monitorOxygenationGE',
									otherGENurseInter = '$otherGENurseInter',
									otherGENurseInterDesc = '$otherGENurseInterDesc',
									assessPain = '$assessPain',
									usePharmacology = '$usePharmacology',
									monitorOxygenationComfort = '$monitorOxygenationComfort',
									otherComfortNurseInter = '$otherComfortNurseInter',
									otherComfortNurseInterDesc = '$otherComfortNurseInterDesc',
									goalTPArchieve1 = '$goalTPArchieve1',
									goalTPAchieveTime1 = '$goalTPAchieveTime1',
									goalTPAchieveInitial1  = '$goalTPAchieveInitial1',
									goalTPAchieve2 = '$goalTPAchieve2',
									goalTPAchieveTime2 = '$goalTPAchieveTime2',
									goalTPAchieveInitial2  = '$goalTPAchieveInitial2',
									goalGEAchieve1 = '$goalGEAchieve1',
									goalGEAchieveTime1 = '$goalGEAchieveTime1',
									goalGEAchieveInitial1  = '$goalGEAchieveInitial1',
									goalGEAchieve2 = '$goalGEAchieve2',
									goalGEAchieveTime2 = '$goalGEAchieveTime2',
									goalGEAchieveInitial2  = '$goalGEAchieveInitial2',
									goalCRPAchieve1 = '$goalCRPAchieve1',
									goalCRPAchieveTime1 = '$goalCRPAchieveTime1',
									goalCRPAchieveInitial1  = '$goalCRPAchieveInitial1',
									goalCRPAchieve2 = '$goalCRPAchieve2',
									goalCRPAchieveTime2 = '$goalCRPAchieveTime2',
									goalCRPAchieveInitial2  = '$goalCRPAchieveInitial2',
									dischargeSummaryTemp = '$dischargeSummaryTemp',
									dischangeSummaryBp = '$dischangeSummaryBp',
									dischargeSummaryP = '$dischargeSummaryP',
									dischangeSummaryRR = '$dischangeSummaryRR',
									alert = '$alert',
									hasTakenNourishment = '$hasTakenNourishment',
									nauseasVomiting = '$nauseasVomiting',
									voidedQs = '$voidedQs',
									panv = '$panv',
									dressing = '$dressing',
									dischargeSummaryOther = '$dischargeSummaryOther',
									dischargeAt = '$dischargeAt',
									comments = '$comments',
									nurseId = '$nurseId',
									nurseSign = '$nurseSign',
									whoUserType = '$whoUserType',
									createdByUserId = '$createdByUserId',
									relivedNurseId = '$relivedNurseId',
									recovery_room_na='$recovery_room_na',
									recovery_new_drugs1 = '$recovery_new_drugs1',
									recovery_new_drugs2 = '$recovery_new_drugs2',
									recovery_new_drugs3 = '$recovery_new_drugs3',
									recovery_new_drugs4 = '$recovery_new_drugs4',
									recovery_new_dose1 = '$recovery_new_dose1',
									recovery_new_dose2 = '$recovery_new_dose2',
									recovery_new_dose3 = '$recovery_new_dose3',
									recovery_new_dose4 = '$recovery_new_dose4',
									recovery_new_route1 = '$recovery_new_route1',
									recovery_new_route2 = '$recovery_new_route2',
									recovery_new_route3 = '$recovery_new_route3',
									recovery_new_route4 = '$recovery_new_route4',
									recovery_new_time1 = '$recovery_new_time1',
									recovery_new_time2 = '$recovery_new_time2',
									recovery_new_time3 = '$recovery_new_time3',
									recovery_new_time4 = '$recovery_new_time4',
									recovery_new_initial1 = '$recovery_new_initial1',
									recovery_new_initial2 = '$recovery_new_initial2',
									recovery_new_initial3 = '$recovery_new_initial3',
									recovery_new_initial4 = '$recovery_new_initial4',
									intake_new_fluids1 = '$intake_new_fluids1',
									intake_new_fluids2 = '$intake_new_fluids2',
									intake_new_fluids3 = '$intake_new_fluids3',
									intake_new_amount_given1 = '$intake_new_amount_given1',
									intake_new_amount_given2 = '$intake_new_amount_given2',
									intake_new_amount_given3 = '$intake_new_amount_given3',
									$resetRecordQry
									form_status ='".$form_status."', 
									confirmation_id='".$_REQUEST["pConfId"]."',
									patient_id = '".$_REQUEST["patient_id"]."'";
	}
	$SaveNurseNotesRes = imw_query($SaveNurseNotesQry) or die(imw_error());
	
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
	
	//CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
		$chartSignedByNurse = chkNurseSignNew($_REQUEST["pConfId"]);
		$updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='".$chartSignedByNurse."' WHERE patient_confirmation_id='".$_REQUEST["pConfId"]."'";
		$updateNurseStubTblRes = imw_query($updateNurseStubTblQry) or die(imw_error());
	//END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
	
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
		echo "<script type=\"text/javascript\">top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		/*
		if($form_status == "completed" && ($chk_form_status=="" || $chk_form_status=="not completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}else if($form_status=="not completed" && ($chk_form_status==""  || $chk_form_status=="completed")) {
			echo "<script>top.changeChkMarkImage('".$innerKey."','".$form_status."');</script>";	
		}*/
	//REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
	
}

//END SAVE RECORD TO DATABASE


//VIEW RECORD FROM DATABASE
	/*
	if($_POST['SaveRecordForm']==''){	
	*/	
		$ViewgenAnesNurseQry = "select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat from `genanesthesianursesnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewgenAnesNurseRes = imw_query($ViewgenAnesNurseQry) or die(imw_error()); 
		$ViewgenAnesNurseNumRow = imw_num_rows($ViewgenAnesNurseRes);
		$ViewgenAnesNurseRow = imw_fetch_array($ViewgenAnesNurseRes); 
		
		
		$anesthesiaGeneral = $ViewgenAnesNurseRow["anesthesiaGeneral"];
		$anesthesiaRegional = $ViewgenAnesNurseRow["anesthesiaRegional"];
		$anesthesiaEpidural = $ViewgenAnesNurseRow["anesthesiaEpidural"];
		$anesthesiaMAC = $ViewgenAnesNurseRow["anesthesiaMAC"];
		$anesthesiaLocal = $ViewgenAnesNurseRow["anesthesiaLocal"];
		$anesthesiaSpinal = $ViewgenAnesNurseRow["anesthesiaSpinal"];
		$anesthesiaSensationAt = $ViewgenAnesNurseRow["anesthesiaSensationAt"];
		$anesthesiaSensationAtDesc = $ViewgenAnesNurseRow["anesthesiaSensationAtDesc"];
		
		$genNotesSignApplet = $ViewgenAnesNurseRow["genNotesSignApplet"];
		$temp = $ViewgenAnesNurseRow["temp"];
		$o2Sat = $ViewgenAnesNurseRow["o2Sat"];
		$painScale = $ViewgenAnesNurseRow["painScale"];
		
		$intake_site1 = $ViewgenAnesNurseRow["intake_site1"];
		$intake_site2 = $ViewgenAnesNurseRow["intake_site2"];
		$intake_site3 = $ViewgenAnesNurseRow["intake_site3"];
		$intake_site4 = $ViewgenAnesNurseRow["intake_site4"];
		$intake_site5 = $ViewgenAnesNurseRow["intake_site5"];
		$intake_site6 = $ViewgenAnesNurseRow["intake_site6"];
		$intake_site7 = $ViewgenAnesNurseRow["intake_site7"];
		
		$intake_solution1 = $ViewgenAnesNurseRow["intake_solution1"];
		$intake_solution2 = $ViewgenAnesNurseRow["intake_solution2"];
		$intake_solution3 = $ViewgenAnesNurseRow["intake_solution3"];
		$intake_solution4 = $ViewgenAnesNurseRow["intake_solution4"];
		$intake_solution5 = $ViewgenAnesNurseRow["intake_solution5"];
		$intake_solution6 = $ViewgenAnesNurseRow["intake_solution6"];
		$intake_solution7 = $ViewgenAnesNurseRow["intake_solution7"];
		
		$intake_credit1 = $ViewgenAnesNurseRow["intake_credit1"];
		$intake_credit2 = $ViewgenAnesNurseRow["intake_credit2"];
		$intake_credit3 = $ViewgenAnesNurseRow["intake_credit3"];
		$intake_credit4 = $ViewgenAnesNurseRow["intake_credit4"];
		$intake_credit5 = $ViewgenAnesNurseRow["intake_credit5"];
		$intake_credit6 = $ViewgenAnesNurseRow["intake_credit6"];
		$intake_credit7 = $ViewgenAnesNurseRow["intake_credit7"];
			
		
		$alterTissuePerfusionTime1 = $ViewgenAnesNurseRow["alterTissuePerfusionTime1"];
		$hidd_alterTissuePerfusionTime1 = $ViewgenAnesNurseRow["alterTissuePerfusionTime1"];
		
		$alterTissuePerfusionTime2 = $ViewgenAnesNurseRow["alterTissuePerfusionTime2"];
		$hidd_alterTissuePerfusionTime2 = $ViewgenAnesNurseRow["alterTissuePerfusionTime2"];
		
		$alterTissuePerfusionTime3 = $ViewgenAnesNurseRow["alterTissuePerfusionTime3"];
		$hidd_alterTissuePerfusionTime3 = $ViewgenAnesNurseRow["alterTissuePerfusionTime3"];
		
		$alterTissuePerfusionInitials1 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials1"];
		$alterTissuePerfusionInitials2 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials2"];
		$alterTissuePerfusionInitials3 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials3"];
		
		$alterGasExchangeTime1 = $ViewgenAnesNurseRow["alterGasExchangeTime1"];
		$hidd_alterGasExchangeTime1 = $ViewgenAnesNurseRow["alterGasExchangeTime1"];
		
		$alterGasExchangeTime2 = $ViewgenAnesNurseRow["alterGasExchangeTime2"];
		$hidd_alterGasExchangeTime2 = $ViewgenAnesNurseRow["alterGasExchangeTime2"];
		
		$alterGasExchangeTime3 = $ViewgenAnesNurseRow["alterGasExchangeTime3"];
		$hidd_alterGasExchangeTime3 = $ViewgenAnesNurseRow["alterGasExchangeTime3"];
		
		
		$alterGasExchangeInitials1 = $ViewgenAnesNurseRow["alterGasExchangeInitials1"];
		$alterGasExchangeInitials2 = $ViewgenAnesNurseRow["alterGasExchangeInitials2"];
		$alterGasExchangeInitials3 = $ViewgenAnesNurseRow["alterGasExchangeInitials3"];
	
		
		$alterComfortTime1 = $ViewgenAnesNurseRow["alterComfortTime1"];
		$hidd_alterComfortTime1 = $ViewgenAnesNurseRow["alterComfortTime1"];
		
		$alterComfortTime2 = $ViewgenAnesNurseRow["alterComfortTime2"];
		$hidd_alterComfortTime2 = $ViewgenAnesNurseRow["alterComfortTime2"];
		
		$alterComfortTime3 = $ViewgenAnesNurseRow["alterComfortTime3"];
		$hidd_alterComfortTime3 = $ViewgenAnesNurseRow["alterComfortTime3"];
		
		$alterComfortInitials1 = $ViewgenAnesNurseRow["alterComfortInitials1"];
		$alterComfortInitials2 = $ViewgenAnesNurseRow["alterComfortInitials2"];
		$alterComfortInitials3 = $ViewgenAnesNurseRow["alterComfortInitials3"];
	
		
		
		$monitorTissuePerfusion = $ViewgenAnesNurseRow["monitorTissuePerfusion"];
		$implementComplicationMeasure = $ViewgenAnesNurseRow["implementComplicationMeasure"];
		$assessPlus = $ViewgenAnesNurseRow["assessPlus"];
		$telemetry = $ViewgenAnesNurseRow["telemetry"];
		$otherTPNurseInter = $ViewgenAnesNurseRow["otherTPNurseInter"];
		$otherTPNurseInterDesc = $ViewgenAnesNurseRow["otherTPNurseInterDesc"];
		$assessRespiratoryStatus = $ViewgenAnesNurseRow["assessRespiratoryStatus"];
		$positionOptimalChestExcusion = $ViewgenAnesNurseRow["positionOptimalChestExcusion"];
		$monitorOxygenationGE = $ViewgenAnesNurseRow["monitorOxygenationGE"];
		$otherGENurseInter = $ViewgenAnesNurseRow["otherGENurseInter"];
		$otherGENurseInterDesc = $ViewgenAnesNurseRow["otherGENurseInterDesc"];
		$assessPain = $ViewgenAnesNurseRow["assessPain"];
		$usePharmacology = $ViewgenAnesNurseRow["usePharmacology"];
		$monitorOxygenationComfort = $ViewgenAnesNurseRow["monitorOxygenationComfort"];
		$otherComfortNurseInter = $ViewgenAnesNurseRow["otherComfortNurseInter"];
		$otherComfortNurseInterDesc = $ViewgenAnesNurseRow["otherComfortNurseInterDesc"]; 
		
		$goalTPAchieveTime1 = $ViewgenAnesNurseRow["goalTPAchieveTime1"]; 
		$hidd_goalTPAchieveTime1 = $ViewgenAnesNurseRow["goalTPAchieveTime1"]; 
		$goalTPArchieve1 = $ViewgenAnesNurseRow["goalTPArchieve1"]; 
		$goalTPAchieveInitial1  = $ViewgenAnesNurseRow["goalTPAchieveInitial1"]; 
		
		$goalTPAchieveTime2 = $ViewgenAnesNurseRow["goalTPAchieveTime2"]; 
		$hidd_goalTPAchieveTime2 = $ViewgenAnesNurseRow["goalTPAchieveTime2"]; 
		$goalTPAchieve2 = $ViewgenAnesNurseRow["goalTPAchieve2"]; 
		$goalTPAchieveInitial2  = $ViewgenAnesNurseRow["goalTPAchieveInitial2"]; 
		
		$goalGEAchieveTime1 = $ViewgenAnesNurseRow["goalGEAchieveTime1"]; 
		$hidd_goalGEAchieveTime1 = $ViewgenAnesNurseRow["goalGEAchieveTime1"]; 
		$goalGEAchieve1 = $ViewgenAnesNurseRow["goalGEAchieve1"]; 
		$goalGEAchieveInitial1  = $ViewgenAnesNurseRow["goalGEAchieveInitial1"]; 
	
		$goalGEAchieveTime2 = $ViewgenAnesNurseRow["goalGEAchieveTime2"]; 
		$hidd_goalGEAchieveTime2 = $ViewgenAnesNurseRow["goalGEAchieveTime2"]; 
		$goalGEAchieve2 = $ViewgenAnesNurseRow["goalGEAchieve2"]; 
		$goalGEAchieveInitial2  = $ViewgenAnesNurseRow["goalGEAchieveInitial2"]; 
		
		$goalCRPAchieveTime1 = $ViewgenAnesNurseRow["goalCRPAchieveTime1"]; 
		$hidd_goalCRPAchieveTime1 = $ViewgenAnesNurseRow["goalCRPAchieveTime1"]; 
		$goalCRPAchieve1 = $ViewgenAnesNurseRow["goalCRPAchieve1"]; 
		$goalCRPAchieveInitial1  = $ViewgenAnesNurseRow["goalCRPAchieveInitial1"]; 
		
		$goalCRPAchieveTime2 = $ViewgenAnesNurseRow["goalCRPAchieveTime2"]; 
		$hidd_goalCRPAchieveTime2 = $ViewgenAnesNurseRow["goalCRPAchieveTime2"]; 
		$goalCRPAchieve2 = $ViewgenAnesNurseRow["goalCRPAchieve2"]; 
		$goalCRPAchieveInitial2  = $ViewgenAnesNurseRow["goalCRPAchieveInitial2"]; 
		
		$dischargeSummaryTemp = $ViewgenAnesNurseRow["dischargeSummaryTemp"];
		$dischangeSummaryBp = $ViewgenAnesNurseRow["dischangeSummaryBp"];
		$dischargeSummaryP = $ViewgenAnesNurseRow["dischargeSummaryP"];
		$dischangeSummaryRR = $ViewgenAnesNurseRow["dischangeSummaryRR"];
		
		$alert = $ViewgenAnesNurseRow["alert"];
		$hasTakenNourishment = $ViewgenAnesNurseRow["hasTakenNourishment"];
		$nauseasVomiting = $ViewgenAnesNurseRow["nauseasVomiting"];
		$voidedQs = $ViewgenAnesNurseRow["voidedQs"];
		$panv = $ViewgenAnesNurseRow["panv"];
		$dressing = $ViewgenAnesNurseRow["dressing"];
		$dischargeSummaryOther = $ViewgenAnesNurseRow["dischargeSummaryOther"];
		$dischargeAt = $ViewgenAnesNurseRow["dischargeAt"];
		$comments = $ViewgenAnesNurseRow["comments"];
		$form_status = $ViewgenAnesNurseRow["form_status"];
			list($list_hour,$list_min,$secnd) = explode(":",$dischargeAt);
			if($list_hour>$hhCnt) {
				$list_hour=$list_hour-$hhCnt;
				$list_ampm="PM";
			}
		$nurseId = $ViewgenAnesNurseRow["nurseId"];
		$nurseSign = $ViewgenAnesNurseRow["nurseSign"];
		
		$whoUserType = $ViewgenAnesNurseRow["whoUserType"];
		$createdByUserId = $ViewgenAnesNurseRow["createdByUserId"];
		$relivedNurseId = $ViewgenAnesNurseRow["relivedNurseId"];
		
		
		$signNurseId =  $ViewgenAnesNurseRow["signNurseId"];
		$signNurseDateTime =  $ViewgenAnesNurseRow["signNurseDateTime"];
	    $signNurseDateTimeFormat =  $ViewgenAnesNurseRow["signNurseDateTimeFormat"];
		$signNurseFirstName =  $ViewgenAnesNurseRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewgenAnesNurseRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewgenAnesNurseRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewgenAnesNurseRow["signNurseStatus"];
		
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		//$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
		$recovery_room_na = $ViewgenAnesNurseRow["recovery_room_na"];
		$recovery_new_drugs = array();
		$recovery_new_dose = array();
		$recovery_new_route = array();
		$recovery_new_time = array();
		$recovery_new_initial = array();
		for($i=1;$i<=4;$i++) {
			$recovery_new_drugs[$i] =  $ViewgenAnesNurseRow["recovery_new_drugs$i"];
			$recovery_new_dose[$i] =  $ViewgenAnesNurseRow["recovery_new_dose$i"];
			$recovery_new_route[$i] =  $ViewgenAnesNurseRow["recovery_new_route$i"];
			$recovery_new_time[$i] =  get_timeValue($ViewgenAnesNurseRow["recovery_new_time$i"]);
			$recovery_new_initial[$i] =  $ViewgenAnesNurseRow["recovery_new_initial$i"];
			
		}
		
		$intake_new_fluids = array();
		$intake_new_amount_given = array();
		for($j=1;$j<=3;$j++) {
			$intake_new_fluids[$j] =  $ViewgenAnesNurseRow["intake_new_fluids$j"];
			$intake_new_amount_given[$j] =  $ViewgenAnesNurseRow["intake_new_amount_given$j"];
			
		}
		
		$ascId = $ViewgenAnesNurseRow["ascId"];
		$confirmation_id = $ViewgenAnesNurseRow["confirmation_id"];
		$patient_id = $ViewgenAnesNurseRow["patient_id"];
		
		
			
		//CODE TO SET gennurseNotes TISSUE TIME
			if($alterTissuePerfusionTime1=="00:00:00" || $alterTissuePerfusionTime1=="") {
				$hidd_alterTissuePerfusionTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime1 = 	$alterTissuePerfusionTime1;
			}	
			
			if($alterTissuePerfusionTime2=="00:00:00" || $alterTissuePerfusionTime2=="") {
				$hidd_alterTissuePerfusionTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime2 = 	$alterTissuePerfusionTime2;
			}
			
			if($alterTissuePerfusionTime3=="00:00:00" || $alterTissuePerfusionTime3=="") {
				$hidd_alterTissuePerfusionTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime3 = 	$alterTissuePerfusionTime3;
			}
			
			$alterTissuePerfusionTime1 = get_timeValue($hidd_alterTissuePerfusionTime1);
			$alterTissuePerfusionTime2 = get_timeValue($hidd_alterTissuePerfusionTime2);
			$alterTissuePerfusionTime3 = get_timeValue($hidd_alterTissuePerfusionTime3);
		//END CODE TO SET gennurseNotes TISSUE TIME
		
		//CODE TO SET gennurseNotes GAS EXCHANGE TIME
			if($alterGasExchangeTime1=="00:00:00" || $alterGasExchangeTime1=="") {
				$hidd_alterGasExchangeTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime1 = 	$alterGasExchangeTime1;
			}	
			
			if($alterGasExchangeTime2=="00:00:00" || $alterGasExchangeTime2=="") {
				$hidd_alterGasExchangeTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime2 = 	$alterGasExchangeTime2;
			}
			
			if($alterGasExchangeTime3=="00:00:00" || $alterGasExchangeTime3=="") {
				$hidd_alterGasExchangeTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime3 = 	$alterGasExchangeTime3;
			}
			
			$alterGasExchangeTime1 = get_timeValue($hidd_alterGasExchangeTime1);
			$alterGasExchangeTime2 = get_timeValue($hidd_alterGasExchangeTime2);
			$alterGasExchangeTime3 = get_timeValue($hidd_alterGasExchangeTime3);
		//END CODE TO SET gennurseNotes GAS EXCHANGE TIME
		
		//CODE TO SET gennurseNotes CONFORT TIME
			if($alterComfortTime1=="00:00:00" || $alterComfortTime1=="") {
				$hidd_alterComfortTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime1 = 	$alterComfortTime1;
			}	
			
			if($alterComfortTime2=="00:00:00" || $alterComfortTime2=="") {
				$hidd_alterComfortTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime2 = 	$alterComfortTime2;
			}
			
			if($alterComfortTime3=="00:00:00" || $alterComfortTime3=="") {
				$hidd_alterComfortTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime3 = 	$alterComfortTime3;
			}
			
			$alterComfortTime1 = get_timeValue($hidd_alterComfortTime1);
			$alterComfortTime2 = get_timeValue($hidd_alterComfortTime2);
			$alterComfortTime3 = get_timeValue($hidd_alterComfortTime3);
		//END CODE TO SET gennurseNotes CONFORT TIME
		
		//CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
			if($goalTPAchieveTime1=="00:00:00" || $goalTPAchieveTime1=="") {
				$hidd_goalTPAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalTPAchieveTime1 = 	$goalTPAchieveTime1;
			}	
			
			if($goalTPAchieveTime2=="00:00:00" || $goalTPAchieveTime2=="") {
				$hidd_goalTPAchieveTime2 = "";//date("H:i:s"); 
			}else {
				$hidd_goalTPAchieveTime2 = 	$goalTPAchieveTime2;
			}
			$goalTPAchieveTime1 = get_timeValue($hidd_goalTPAchieveTime1);
			$goalTPAchieveTime2 = get_timeValue($hidd_goalTPAchieveTime2);
		//END CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
		
		//CODE TO SET gennurseNotes GOAL GE ACHIVE TIME
			if($goalGEAchieveTime1=="00:00:00" || $goalGEAchieveTime1=="") {
				$hidd_goalGEAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalGEAchieveTime1 = 	$goalGEAchieveTime1;
			}	
			
			if($goalGEAchieveTime2=="00:00:00" || $goalGEAchieveTime2=="") {
				$hidd_goalGEAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalGEAchieveTime2 = 	$goalGEAchieveTime2;
			}
			$goalGEAchieveTime1 = get_timeValue($hidd_goalGEAchieveTime1);
			$goalGEAchieveTime2 = get_timeValue($hidd_goalGEAchieveTime2);
		//END TO SET gennurseNotes GOAL GE ACHIVE TIME
		
		//CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
			if($goalCRPAchieveTime1=="00:00:00" || $goalCRPAchieveTime1=="") {
				$hidd_goalCRPAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalCRPAchieveTime1 = 	$goalCRPAchieveTime1;
			}	
			
			if($goalCRPAchieveTime2=="00:00:00" || $goalCRPAchieveTime2=="") {
				$hidd_goalCRPAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalCRPAchieveTime2 = 	$goalCRPAchieveTime2;
			}
			$goalCRPAchieveTime1 = get_timeValue($hidd_goalCRPAchieveTime1);
			$goalCRPAchieveTime2 = get_timeValue($hidd_goalCRPAchieveTime2);
		//END CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
		
	/*
	}	
	*/
//END VIEW RECORD FROM DATABASE

?>

<!-- <body onLoad="top.changeColor('<?php echo $tablebg_local_anes; ?>');" onClick="closeEpost(); return top.frames[0].main_frmInner.hideSliders();"> -->
<div id="post" style="display:none;"></div>
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

include("common/pre_defined_popup.php");
include("common/pre_op_medication_order.php");
include("common/pre_define_medicationNurse.php");
?>

<div class="main_wrapper">
         <form name="frm_gen_anes_nurse_notes" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " >
			<input type="hidden" name="divId" id="divId">
			<input type="hidden" name="counter" id="counter">
			<input type="hidden" name="secondaryValues" id="secondaryValues">
			<input type="hidden" name="selected_frame_name_id" id="selected_frame_name_id" value="">
			<input type="hidden" name="hidd_currentLoggedinNurseId" id="hidd_currentLoggedinNurseId" value="<?php echo $currentLoggedinNurseId;?>">			
			<input type="hidden" name="formIdentity" id="formIdentity" value="healthQues">			
			<input type="hidden" name="SaveRecordForm" id="SaveRecordForm" value="yes">
			<input type="hidden" name="saveRecord" id="saveRecord" value="true">
			<input type="hidden" name="getText" id="getText">
			<input type="hidden" name="hiddSignatureId" id="hiddSignatureId">
			<input type="hidden" name="go_pageval" id="go_pageval" value="<?php echo $tablename;?>" >
			<input type="hidden" name="frmAction" id="frmAction" value="gen_anes_nurse_notes.php">
			<input type="hidden" name="SaveForm_alert" id="SaveForm_alert" value="true">	
			<input type="hidden" name="hiddCalPopId" id="hiddCalPopId">
			<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
			<input type="hidden" name="hiddResetStatusId" id="hiddResetStatusId">
      <input type="hidden" name="preColor" value="<?php echo $preColor; ?>">
      <input type="hidden" name="innerKey" value="<?php echo $innerKey; ?>">
	    <input type="hidden" id="vitalSignGridHolder" />
            
            	
          	<div class="slider_content scheduler_table_Complete" style="background-color:<?php echo $tablebg_local_anes; ?>" class="table_pad_bdr alignCenter" onDblClick="closePreDefineDiv()" onClick="calCloseFun();preCloseFun('evaluationPreDefineDiv');preCloseFun('PreOpMedDiv');" onMouseOver="">
            
            		<?php
							$epost_table_name = "genanesthesianursesnotes";
							include("./epost_list.php");
					?>
                                	
            		<!--<div class="head_scheduler padding-top-adjustment text-center new_head_slider border_btm_anesth">
                    			
                                <span class="bg_span_anesth" style="background-color:<?php echo $bgdark_blue_local_anes ;?>; color:<?php echo $title1_color;?>">
                                			General Anesthesia Nurses Notes 		 	
                               	</span>
                                
                                
                  	</div>-->
                    
                    <div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
                    		<?php 
									$bgCol = $bgdark_blue_local_anes;
									$borderCol = $tablebg_local_anes;
									include('saveDivPopUp.php'); 
							?>
                	</div>
                    
                    
                    
                    <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                         		<div class="panel panel-default bg_panel_anesth">
                                              		<div class="panel-heading">
                                                    	<a class="panel-title rob alle_link show-pop-trigger2 btn btn-default " onClick="return showPreDefineFnNew('Allergies_quest', 'reaction_quest', '10', ($(this).offset().left)+10, parseInt($(this).offset().top - $(document).scrollTop() - 136 ) ),document.getElementById('selected_frame_name_id').value='iframe_allergies_genanes_nurse_notes';">
                                                        		<span class="fa fa-caret-right"></span>  Allergies 
                                                      	</a>
                                                  	</div>
                                                    
                                                    
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
                                                                       <div class="table_slider" id="iframe_allergies_genanes_nurse_notes">    
                                                                       				
																						<?php  
                                                                                            $allgNameWidth="225";
                                                                                            $allgReactionWidth="225";
                                                                                            include("health_quest_spreadsheet.php");
                                                                                        ?>
                                                                              
                                                                                 
                                                                         </div>                
                      
                    												  </div>
                      <!--- ---- Table --->
                                                                </div>
                                                                <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                    </div> <!-- Panel Body -->
                                                </div>
                                           </div>
                                           
                                           <?php
								$chbxGenRegLocalBackColor=$chngBckGroundColor;
								if($anesthesiaGeneral || $anesthesiaRegional || $anesthesiaEpidural || $anesthesiaMAC || $anesthesiaLocal || $anesthesiaSpinal || $anesthesiaSensationAt) { 
									$chbxGenRegLocalBackColor=$whiteBckGroundColor; 
								}
								?>
                                           <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                         		<div class="panel panel-default bg_panel_anesth">
                                              		<div class="panel-heading">
                                                    	  <label>    Anesthesia   </label>
                                                    </div>
                                                    <div class="panel-body">   
                                                    	<div class="full_width padding_15 wrap_right_inner_anesth">
                                                        	<div class="row">
																<div class="full_width padding_15">
                                                                        	
                                                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3 f_size"><span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_anesthesiaGeneral_id" name="chbx_anesthesiaGeneral" <?php if($anesthesiaGeneral=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;General </label>
                                                                        	
                                                                            
                                                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3 f_size"> <span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_anesthesiaRegional_id" name="chbx_anesthesiaRegional" <?php if($anesthesiaRegional=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;Regional  </label>
                                                                        	
                                                                            
                                                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3 f_size"> <span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_anesthesiaEpidural_id" name="chbx_anesthesiaEpidural" <?php if($anesthesiaEpidural=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; Epidural  </label>
                                                                        	
                                                                            
                                                                            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-3 f_size"> <span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_anesthesiaMAC_id" name="chbx_anesthesiaMAC" <?php if($anesthesiaMAC=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; MAC  </label>
                                                                </div>
                                                                <div class="full_width padding_15">
                                                                        	<label class="col-md-3 col-lg-3 col-sm-3 col-xs-3 f_size"> <span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_anesthesiaLocal_id" name="chbx_anesthesiaLocal" <?php if($anesthesiaLocal=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; Local </label>
                                                                            
                                                                        	<label class="col-md-3 col-lg-3 col-sm-3 col-xs-3 f_size"> <span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_anesthesiaSpinal_id" name="chbx_anesthesiaSpinal" <?php if($anesthesiaSpinal=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;Spinal  </label>
                                                                        	
                                                                            <label class="col-md-6 col-lg-6 col-sm-6 col-xs-6 f_size"> <span class="colorChkBx" onClick="changeDiffChbxColor(7,'chbx_anesthesiaGeneral_id','chbx_anesthesiaRegional_id','chbx_anesthesiaEpidural_id','chbx_anesthesiaMAC_id','chbx_anesthesiaLocal_id','chbx_anesthesiaSpinal_id','chbx_anesthesiaSensationAt_id');" style=" <?php echo $chbxGenRegLocalBackColor;?>" ><input class="field"  type="checkbox" value="Yes" id="chbx_anesthesiaSensationAt_id" name="chbx_anesthesiaSensationAt" <?php if($anesthesiaSensationAt=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; Sensation at &nbsp;
                                                                            	
                                                                          	<input type="text" id="txt_anesthesiaSensationAt_id" name="txt_anesthesiaSensationAt" class="inline_form_text form-control inline-block-imp" tabindex="1" value="<?php echo $anesthesiaSensationAtDesc;?>"  /> 
                                                                            </label>
                                                                            
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <!-- Panel Body -->
                                                </div>
                                           </div>
                                           <div class="full_width padding_15">
                                           		<div class="row">
                                                	<div class="col-md-offset-1 col-lg-offset-1 col-md-8 col-sm-9 col-xs-9 col-lg-9">
                                                        <div class="inner_right_slider_anesth  col-md-12 col-sm-12 col-xs-12 col-lg-12" onMouseOut="javascript:getAppValue();">
                                                            <?php 
															 if($sect=="print_emr") {
																 include("imageSc/imgGd.php");
																 $imgName = 'bgGridNN.jpg';
																 drawOnImage2($genNotesSignApplet,$imgName,"html2pdf/tess.jpg");	
																 echo '<img src="html2pdf/tess.jpg" width="350" height="300">';
															 }else {?>
															
																<object type="application/x-java-applet" name="signs" id="signs" width="100%" height="419">
																  <param name="code" value="Test2NN.class" />
																  <param name="codebase" value="common/applet/" />
																  <param name="archive" value="signs.jar" />
																	<param name="bgImg" value="images/icon/bgGridNN.jpg">
																	<param name="signs" value="<?php echo $genNotesSignApplet;?>">
																	<param name="iconSize" value="14">
																	<param name="txtActivate" value="inactive">
																</object>                           
															<?php
															 }
															?>
                                    <input type="hidden" name="elem_signs" id="elem_signs" value="<?php echo $genNotesSignApplet;?>">
									<!-- Applet -->
                                                        </div>
                                                    </div>	
                                                    <div class="col-md-2 col-sm-3 col-xs-3 col-lg-2">
                                                        <div class="inner_left_slider_anesth border_adj">
                                                        
                                                            <span class="left_controls_scroll bg_white" onClick="setAppIcon('tdArr1')" id="tdArr1">
                                                            	<b class="fa fa-caret-down"></b>
                                                          	</span>
                                                            
                                                            <span class="left_controls_scroll" onClick="setAppIcon('tdArr1')"> Systolic  </span>
                                                            
                                                            <span class="left_controls_scroll bg_bl"  onClick="setAppIcon('tdArr1')"> Pressure </span>
                                                            <span class="left_controls_scroll " onClick="setAppIcon('tdArr2')"  id="tdArr2" >
                                                            	<b class="fa fa-caret-up"></b>
                                                           	</span>
                                                            
                                                            <span class="left_controls_scroll bg_bl" onClick="setAppIcon('tdArr2')"> Diastolic</span>
                                                            
                                                            <span class="left_controls_scroll" onClick="setAppIcon('tdArr2')"> Pressure </span>
                                                            <span class="left_controls_scroll bg_white" onClick="setAppIcon('tdRfill')" id="tdRfill">
                                                            	<b style="color:#333;" class="fa fa-circle"></b>
                                                           	</span>
                                                            
                                                            <span class="left_controls_scroll" onClick="setAppIcon('tdRfill')"> Heart </span>
                                                            
                                                            <span class="left_controls_scroll bg_bl" onClick="setAppIcon('tdRblank')"> Rate </span>
                                                            
                                                            <span class="left_controls_scroll" onClick="setAppIcon('tdRblank')" id="tdRblank" >
                                                            	<b class="fa fa-circle-o"></b>
                                                          	</span>
                                                            
                                                            <span class="left_controls_scroll bg_bl" onClick="setAppIcon('tdRblank')"> Spontaneous</span>
                                                            
                                                            <span class="left_controls_scroll" onClick="setAppIcon('tdRblank')"> Respiration </span>
                                                            
                                                            <span class="left_controls_scroll bg_bl" onClick="setAppIcon('tdRText')" id="tdRText">
                                                            	<b> T </b>
                                                           	</span>
                                                            
                                                            <span class="left_controls_scroll" onClick="setAppIcon('tdRText')"> Input Value</span>
                                                            <span class="left_controls_scroll bg_white"> 
                                                                <b class="fa fa-eraser" style="margin-right:2px;margin-left:2px; color:#333;" onClick="clearApp()"></b>
                                                                <b class=" rotate_icon2 fa fa-mail-reply" onClick="undoApp()" style="margin-right:2px;margin-left:2px; color:#333;"></b>
                                                                <b class=" rotate_icon fa fa-mail-forward" onClick="redoApp()" style="margin-right:2px;margin-left:2px; color:#333;"></b>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                           </div>
                                           <div class="margin_adjustment_only"></div>
                                           <?php
												  for($k=1; $k<=4;$k++) {
													if($recovery_room_na || $recovery_new_drugs[1] || $recovery_new_drugs[2] || $recovery_new_drugs[3] || $recovery_new_drugs[4]) { 
														$roomMedsBackColor=$whiteBckGroundColor; 
													}
												  }	
						  					?>
                                           <div class="full_width">
                                           			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                           <!--- ---- Table --->
                                                           <div class="scheduler_table_Complete anesth_table">
                                                               <div class="my_table_Checkall">
                                                               			<?php
																						$recoveryRoomNotesQry = "select * from `gen_nursenotes_recovery_meds` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
																						$recoveryRoomNotesRes = imw_query($recoveryRoomNotesQry) or die(imw_error()); 
																						$recoveryRoomNotesNumRow = imw_num_rows($recoveryRoomNotesRes);
																						if($recoveryRoomNotesNumRow>0) {
																							while($recoveryRoomNotesRow = imw_fetch_array($recoveryRoomNotesRes)) {
																							
																								$recovery_drugs = $recoveryRoomNotesRow["recovery_drugs"];
																								$recovery_dose = $recoveryRoomNotesRow["recovery_dose"];
																								$recovery_route = $recoveryRoomNotesRow["recovery_route"];
																								$recovery_timeTemp = $recoveryRoomNotesRow["recovery_time"];
																								$recovery_initial = $recoveryRoomNotesRow["recovery_initial"];
																								
																								//CODE TO SET RECOVERY MEDS TIME									
																									$recovery_time_split = explode(":",$recovery_timeTemp);
																									if($recovery_time_split[0]>=12) {
																										$am_pm_recovery = "PM";
																									}else {
																										$am_pm_recovery = "AM";
																									}
																									if($recovery_time_split[0]>=13) {
																										$recovery_time_split[0] = $recovery_time_split[0]-12;
																										if(strlen($recovery_time_split[0]) == 1) {
																											$recovery_time_split[0] = "0".$recovery_time_split[0];
																										}
																									}else {
																										//DO NOTHNING
																									}
																									$recovery_time = $recovery_time_split[0].":".$recovery_time_split[1]." ".$am_pm_recovery;
																								//CODE TO SET RECOVERY MEDS TIME									
																							}
																						}	
												
																					?>
                                                                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                                        	<caption class="text-left padding_15"> <label> Recovery Room Meds &nbsp;&nbsp;  <span class="colorChkBx" onClick="changeDiffChbxColor(5,'recovery_room_na_id','preopmedId1','preopmedId2','preopmedId3','preopmedId4');" style=" <?php echo $roomMedsBackColor;?>" >
									<input <?php if($recovery_room_na=='Yes') echo 'CHECKED'; ?>  type="checkbox" value="Yes" id="recovery_room_na_id" name="recovery_room_na" tabindex="7"></span>&nbsp; N/A</label> </caption>
                                                                            <thead class="cf" >
                                                                                <tr id="recoveryRoom_id">
                                                                                	
                                                                                    <th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3">Medication</th>
                                                                                    <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"> Dose </th>
                                                                                    <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">Route</th>
                                                                                    <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"> Time </th>
                                                                                    <th class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3">Initial</th>
                                                                                </tr>
                                                                            </thead>
                                                                         </table>
                                                               </div>
                                                               <div class="wrap_right_inner_anesth">          
                                                                         <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                                            <tbody>
                                                                            <?php
																					$calcIncr =5;
																					$roomMedsBackColor=$chngBckGroundColor;
																					for($k=1; $k<=4;$k++) 
																					{
																						if($recovery_room_na || $recovery_new_drugs[1] || $recovery_new_drugs[2] || $recovery_new_drugs[3] || $recovery_new_drugs[4]) { 
																							$roomMedsBackColor=$whiteBckGroundColor; 
																						}
																						
																			?>
                                                                            				<tr>
                                                                                                  <td class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3" >
                                                                                                    &nbsp;<input class="form-control" type="text" name="txt_recovery_new_drugs<?php echo $k;?>"  id="preopmedId<?php echo $k;?>" onFocus="changeDiffChbxColor(5,'recovery_room_na_id','preopmedId1','preopmedId2','preopmedId3','preopmedId4');" onKeyUp="changeDiffChbxColor(5,'recovery_room_na_id','preopmedId1','preopmedId2','preopmedId3','preopmedId4');" style=" <?php echo $roomMedsBackColor;?> " value="<?php echo $recovery_new_drugs[$k];?>"  >
                                                                                                  </td>
                                                                                                  
                                                                                                  <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2" >
												  	&nbsp;<input class="form-control" type="text" name="txt_recovery_new_dose<?php echo $k;?>" value="<?php echo $recovery_new_dose[$k];?>"  >
												  													</td>
												  
                                                                                                  <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2"  >
                                                                                                    <select name="txt_recovery_new_route<?php echo $k;?>" class="form-control selectpicker" title="">
                                                                                                        <option value="IV" <?php if($recovery_new_route[$k]=="IV") { echo "selected"; }?>>IV</option>	
                                                                                                        <option value="IM" <?php if($recovery_new_route[$k]=="IM") { echo "selected"; }?>>IM</option>	
                                                                                                        <option value="PO" <?php if($recovery_new_route[$k]=="PO") { echo "selected"; }?>>PO</option>	
                                                                                                    </select>
                                                                                                </td>
												  												
                                                                                                <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2" >&nbsp;
													 											<?php  if($recovery_new_time[$k]=="00:00:00") { $recovery_new_time[$k]= ""; }?>
													 													<input class="form-control" type="text" name="txt_recovery_new_time<?php echo $k;?>" maxlength="8" value="<?php echo $recovery_new_time[$k];?>" id="bp_temp<?php echo $calcIncr;?>" onKeyUp="displayText<?php echo $calcIncr;?>=this.value" onClick="getShowNewPos($(this).offset().left,$(this).offset().top,'flag<?php echo $calcIncr;?>');clearVal_c();return displayTimeAmPm('bp_temp<?php echo $calcIncr;?>');"  /> 
                                                                                              	</td>
                                                                                               
                                                                                               <td class="text-left col-md-3 col-lg-3 col-sm-3 col-xs-3" >
																									<select name="txt_recovery_new_initial<?php echo $k;?>" class="form-control selectpicker" title="">
																										<option value="" selected>Select</option>
																											<?php
                                                                                                            $currentLoggedNurseQry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                                                                            $currentLoggedNurseRes = imw_query($currentLoggedNurseQry) or die(imw_error());
                                                                                                            while($currentLoggedNurseRow=imw_fetch_array($currentLoggedNurseRes)) {
                                                                                                                $currentLoggedSelectNurseID = $currentLoggedNurseRow["usersId"];
                                                                                                                $currentLoggedNurseName = $currentLoggedNurseRow["lname"].", ".$currentLoggedNurseRow["fname"]." ".$currentLoggedNurseRow["mname"];
                                                                                                                $sel="";
                                                                                                                if($recovery_new_initial[$k]=="") {
                                                                                                                    if($currentLoggedinNurseId==$currentLoggedSelectNurseID) {
                                                                                                                        $sel = "selected";
                                                                                                                    } 
                                                                                                                    else {
                                                                                                                        $sel = "";
                                                                                                                    }
                                                                                                                }else {
                                                                                                                    if($recovery_new_initial[$k]==$currentLoggedSelectNurseID) {
                                                                                                                        $sel = "selected";
                                                                                                                    } 
                                                                                                                    else {
                                                                                                                        $sel = "";
                                                                                                                    }
                                                                                                                }	
                                                                                                                                        
                                                                                                            ?>	
																											<option value="<?php echo $currentLoggedSelectNurseID;?>" <?php echo $sel;?>><?php echo $currentLoggedNurseName;?></option>
																											<?php
                                                                                                            }
                                                                                                            ?>
																										</select>
												  												</td>
																							
                                                                                            </tr>
                                                                               
                                                                            <?php
																						$calcIncr++;
																				}
																			?>        
                                                                                
                                                                            </tbody>
                                                                     </table>
                                                                 </div>                
                                                              </div>
                                                        </div>
                                           </div>
                                           <div class="clearfix margin_small_only"></div>
                                           <div class="full_width">
                                           			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12"  id="intakeRoom_id">
                                                           <!--- ---- Table --->
                                                           
                                                           <?php
														   
																	$intakeRoomNotesQry = "select * from `gen_nursenotes_intake_room` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
																	$intakeRoomNotesRes = imw_query($intakeRoomNotesQry) or die(imw_error()); 
																	$intakeRoomNotesNumRow = imw_num_rows($intakeRoomNotesRes);
																	if($intakeRoomNotesNumRow>0) {
																		while($intakeRoomNotesRow = imw_fetch_array($intakeRoomNotesRes)) {
																		
																			$intake_fluids = $intakeRoomNotesRow["intake_fluids"];
																			$intake_amount_given = $intakeRoomNotesRow["intake_amount_given"];
																		}
																	}	
												
															?>	
                                                           
                                                           <div class="scheduler_table_Complete anesth_table">
                                                               <div class="my_table_Checkall">
                                                                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                                        	<caption class="text-left padding_15"> <label> Intake </label> </caption>
                                                                            <thead class="cf">
                                                                                <tr>
                                                                                    <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">IV fluids</th>
                                                                                    <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6"> Amount Given 	 </th>
                                                                                </tr>
                                                                            </thead>
                                                                         </table>
                                                               </div>
                                                               <div class="wrap_right_inner_anesth">          
                                                                         <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                                                            <tbody>
                                                                            <?php
																			  $intakeFluidsBackColor=$chngBckGroundColor;
																			  for($j=1;$j<=3;$j++) {
																					if($intake_new_fluids[1] || $intake_new_fluids[2] || $intake_new_fluids[3]) { 
																						$intakeFluidsBackColor=$whiteBckGroundColor; 
																					}								  
																			 ?>
                                                                                <tr>
                                                                                    <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                    		<input class="form-control" type="text" name="txt_intake_new_fluids<?php echo $j;?>" id="txt_intake_new_fluids_id<?php echo $j;?>" onFocus="changeDiffChbxColor(3,'txt_intake_new_fluids_id1','txt_intake_new_fluids_id2','txt_intake_new_fluids_id3');" onKeyUp="changeDiffChbxColor(3,'txt_intake_new_fluids_id1','txt_intake_new_fluids_id2','txt_intake_new_fluids_id3');"  style=" <?php echo $intakeFluidsBackColor;?>" value="<?php echo $intake_new_fluids[$j];?>"  >
										  											</td>
                                                                                    <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                    		<input class="form-control" type="text" name="txt_intake_new_amount_given<?php echo $j;?>" value="<?php echo $intake_new_amount_given[$j];?>"  />
                                                                                   	</td>
                                                                                    
                                                                                </tr>
                                                                                
                                                                                
                                                                                
                                                                            
                                                                            <?php
																				 }
								 											?> 
                                                                            
                                                                            </tbody>
                                                                     </table>
                                                                </div>                
                                                           </div>
                                                     </div>
                                           </div>
                                           <div class="clearfix margin_small_only"></div>
                                           <div class="full_width">
                                           			<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                           <!--- ---- Table --->
                                                           <div class="scheduler_table_Complete adj_tp_table">
                                                               <div class="my_table_Checkall">
                                                                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf">
                                                                        	<thead class="cf">
                                                                                <tr>
                                                                                    <th class="text-left col-md-2 col-lg-2 col-sm-3 col-xs-3">Time</th>
                                                                                    <th class="text-left col-md-10 col-lg-10 col-sm-9 col-xs-9" > Nurse Notes </th>
                                                                                   
                                                                               </tr>
                                                                            </thead>
                                                                         </table>
                                                               </div>
                                                               <div class="wrap_right_inner_anesth" id="newNotesId"> 
                                                               		<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf ">
                                                                        		<tbody>
																					<?php
                                            
                                                                                            $newnotesQry = "select * from `genanesthesianursesnewnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
																							
																							$newnotesRes = imw_query($newnotesQry) or die(imw_error()); 
                                                                                            $newnotesNumRow = imw_num_rows($newnotesRes);
                                                                                            if($newnotesNumRow>0) {
																							
                                                                                                while($newnotesRow = imw_fetch_array($newnotesRes)) {
																									
																									$newnotes_id = $newnotesRow["newnotes_id"];
                                                                                                    $newnotes_desc = $newnotesRow["newnotes_desc"];
                                                                                                    $newnotes_timeTemp = $newnotesRow["newnotes_time"];
                                                                                                    //CODE TO SET THE TIME									
                                                                                                    $time_split = explode(":",$newnotes_timeTemp);
                                                                                                    if($time_split[0]>=12) {
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
                                                                                                    $newnotes_time = $time_split[0].":".$time_split[1]." ".$am_pm;
                                                                                                //END CODE TO SET THE TIME									
                                                
                                                                                    ?>
                                                                        
                                                                        
                                                                                		<tr>
                                                                                        		<td class="text-left  col-md-2 col-lg-2 col-sm-3 col-xs-3">
                                                                                                		<?php echo $newnotes_time;?>
                                                                                                </td>
                                                                                    			<td class="text-left col-md-7 col-lg-7 col-sm-7 col-xs-7" id="noteEdtId<?php echo $newnotes_id;?>">
																										<?php echo stripslashes($newnotes_desc);?>
                                                                                                </td>
                                                                                                <td  class="col-md-2 col-lg-2 col-sm- col-xs-2 text-center" id="editBtnId<?php echo $newnotes_id;?>">
                                                                                                		<a class="btn btn-primary glyphicon glyphicon-edit margin_0"  name="edit<?php echo $newnotes_id;?>" href="javascript:void(0)"onClick="editEntry('<?php echo $newnotes_id; ?>');"></a>
                                                                                                </td>
                                                                                                <td  class="col-md-1 col-lg-1 col-sm-2 col-xs-1 text-center">
                                                                                                		<a class="btn btn-danger glyphicon glyphicon-remove margin_0"  onClick="return delentry('<?php echo $newnotes_id; ?>');" name="del<?php echo $newnotes_id;?>" href="javascript:void(0)"></a>
                                                                                                </td>
                                                                                                
                                                                                                
                                                                                                
                                                                                  		</tr>
                                                                                        
                                                                                
                                                                                	<?PHP
																					
																								}
																								
																							}
																					?>
                                                                            </tbody>
                                                                     </table>
                                                                     
                                                                     
                                                                 </div>                
                                                              </div>
                                                        </div>
                                           </div>
                                           <div class="clearifx margin_small_only"></div>
                                         
                                           <div class="full_width">
	    	                                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                    <div class="full_width">
    	    	                                        <div class="border-dashed margin_small_only"></div>
                	                                </div>	
                                           			<div class="wrap_right_inner_anesth">          
                                                    	 <div class="row">
                                                         	  <div class="col-md-2 col-lg-2 col-xs-12 col-sm-12 text-left">
                                                              	   <label>	Add New Note 	</label>	
                                                              </div>	
                                                              <div class="clearfix visible-sm"></div>
                                                              <div class="col-md-9 col-lg-9 col-xs-12 col-sm-12">
                                                              		<div class="row">
                                                                    	<div class="col-md-10 col-sm-10 col-xs-10 col-lg-11">
                                                                     		<textarea id="Field3" name="txt_areaNewNotesDesc" class="form-control" rows="2" cols="50" tabindex="6"  ></textarea>
                                                                        </div>	
                                                                        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-1" id="genAnesNurseNewNotesSaveId" style=" visibility:visible; " >
                                                                        	<a id="" class="btn btn-info "  href="javascript:void(0)" onClick="return save_newnotes_value();top.frames[0].setPNotesHeight();MM_swapImage('saveBtn','','images/save_onclick1.jpg',1);" >
                                                                               <b class="fa fa-save"></b>	Save
                                                                             </a>
                                                                        </div>
                                                                    </div>
                                                             </div>	
                                                         </div> 	    
                                                    </div>     
                                                </div>
                                           </div>
                                           
                                           <?php 
														$calculatorTopChangeGetValue=0;
														$calculatorTopChangeValue = "12";
														if($recoveryRoomNotesNumRow>0) { $calculatorTopChangeGetValue=($recoveryRoomNotesNumRow*$calculatorTopChangeValue);} 
														if($intakeRoomNotesNumRow>0) { $calculatorTopChangeGetValue=($intakeRoomNotesNumRow*$calculatorTopChangeValue);}
														if($newnotesNumRow>0) {  $calculatorTopChangeGetValue=($newnotesNumRow*$calculatorTopChangeValue);} 
												
											?>
                                           <div class="clearfix margin_small_only"></div>
                                           <div class="full_width wrap_right_inner_anesth">
                                           		<div class="col-lg-3 col-sm-12 col-xs-12 col-md-4">
                                                    <div class="panel panel-default bg_panel_anesth">
                                                        <div class="panel-heading">
                                                              <label class="margin_0"> NURSING DIAGNOSIS   </label>
                                                        </div>
                                                        <div class="panel-body">   
                                                            <div class="full_width padding_15">
                                                            	 <p class="rob l_height_28 full_width f_size border-bottom-p">  Alteration in tissue perfusion </p>
                                                                 <div class="full_width">
                                                                 			<div class="scheduler_table_Complete">
                                                                                   <div class="my_table_Checkall">
                                                                                   			<?php
																								$calculatorAlterSetValue = $calculatorTopChangeGetValue+1000;
																								
																								$alterTissuePerfusionBackColor=$chngBckGroundColor;
																								if($alterTissuePerfusionTime1 || $alterTissuePerfusionInitials1 || $alterTissuePerfusionTime2 || $alterTissuePerfusionInitials2 || $alterTissuePerfusionTime3 || $alterTissuePerfusionInitials3) { 
																									$alterTissuePerfusionBackColor=$whiteBckGroundColor; 
																								}
																							?>	
                                                                                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf ">
                                                                                                <thead class="cf">
                                                                                                    <tr>
                                                                                                        <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Time</th>
                                                                                                        <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6"> Nurse Notes </th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                
                                                                                                    <tr>
                                                                                                        <td id="sTimeTissueID1" class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6"> 
                                                                                                        		<div class="input-group">
                                                                                                                		<input type="text" name="alterTissuePerfusionTime1" id="bp_temp9" onFocus="changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" onKeyUp="displayText9=this.value;changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue;?>,95,'flag9');<?php if($alterTissuePerfusionTime1=="") {?>clearVal_c();displayTimeAmPm('bp_temp9');changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id'); <?php } ?>" class="form-control" style=" <?php echo $alterTissuePerfusionBackColor;?> " tabindex="1" value="<?php echo $alterTissuePerfusionTime1;?>"  />
                                                                                                                        
                                                                                                                        <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp9');changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');"><span class="fa fa-clock-o"></span></div>
                                                                                                      			</div>
                                                                                                        </td>
                                                                                                       
                                                                                                       <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
																												<select class="selectpicker form-control" onChange="changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" name="alterTissuePerfusionInitials1_list" id="alterTissuePerfusionInitials1_list_id" style=" <?php echo $alterTissuePerfusionBackColor;?>">
																														<option value="" selected>Select</option>	
																														<?php
                                                                                                                            $alterTissuePerfusionInitials1Qry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                                                                                            $alterTissuePerfusionInitials1Res = imw_query($alterTissuePerfusionInitials1Qry) or die(imw_error());
                                                                                                                            while($alterTissuePerfusionInitials1Row=imw_fetch_array($alterTissuePerfusionInitials1Res)) {
                                                                                                                                $TissuePerfusionInitials1_nurseID = $alterTissuePerfusionInitials1Row["usersId"];
                                                                                                                                $TissuePerfusionInitials1_nurseName = $alterTissuePerfusionInitials1Row["lname"].", ".$alterTissuePerfusionInitials1Row["fname"]." ".$alterTissuePerfusionInitials1Row["mname"];
                                                                                                                                $sel="";
                                                                                                                                if($TissuePerfusionInitials1_nurseID==$alterTissuePerfusionInitials1) {
                                                                                                                                    $sel = "selected";
                                                                                                                                } else if($alterTissuePerfusionInitials1=="" && $TissuePerfusionInitials1_nurseID==$currentLoggedinNurseId) {
                                                                                                                                    $sel = "selected";
                                                                                                                                }else {
                                                                                                                                    $sel = "";
                                                                                                                                }
                                                                                                                        
                                                                                                                        ?>	
																																					<option value="<?php echo $TissuePerfusionInitials1_nurseID;?>" <?php echo $sel;?>><?php echo $TissuePerfusionInitials1_nurseName;?></option>
																														<?php
                                                                                                                            }
                                                                                                                        ?>	
																
																												</select>
																										</td> 
                                                                                                        	
                                                                                                       
                                                                                                        
                                                                                                    </tr>
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td id="sTimeTissueID2" class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6"> 
                                                                                                        		<div class="input-group">
                                                                                                                		<input type="text" name="alterTissuePerfusionTime2" id="bp_temp10" onFocus="changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" onKeyUp="displayText10=this.value;changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+(22*1);?>,95,'flag10');<?php if($alterTissuePerfusionTime2=="") {?>clearVal_c();displayTimeAmPm('bp_temp10');changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id'); <?php } ?>" class="form-control" style=" <?php echo $alterTissuePerfusionBackColor;?> " tabindex="1" value="<?php echo $alterTissuePerfusionTime2;?>"  />
                                                                                                                        
                                                                                                                        <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp10');changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');"><span class="fa fa-clock-o"></span></div>
                                                                                                      			</div>
                                                                                                        </td>
                                                                                                       
                                                                                                       <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
																												<select class="selectpicker form-control" onChange="changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" name="alterTissuePerfusionInitials2_list" id="alterTissuePerfusionInitials2_list_id" style=" <?php echo $alterTissuePerfusionBackColor;?>">
																														<option value="" selected>Select</option>	
																														<?php
																															$alterTissuePerfusionInitials2Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																															$alterTissuePerfusionInitials2Res = imw_query($alterTissuePerfusionInitials2Qry) or die(imw_error());
																															while($alterTissuePerfusionInitials2Row=imw_fetch_array($alterTissuePerfusionInitials2Res)) {
																																$TissuePerfusionInitials2_nurseID = $alterTissuePerfusionInitials2Row["usersId"];
																																$TissuePerfusionInitials2_nurseName = $alterTissuePerfusionInitials2Row["lname"].", ".$alterTissuePerfusionInitials2Row["fname"]." ".$alterTissuePerfusionInitials2Row["mname"];
																																$sel="";
																																if($TissuePerfusionInitials2_nurseID==$alterTissuePerfusionInitials2) {
																																	$sel = "selected";
																																} else if($alterTissuePerfusionInitials2=="" && $TissuePerfusionInitials2_nurseID==$currentLoggedinNurseId) {
																																	$sel = "selected";
																																}else {
																																	$sel = "";
																																}
																														
																														?>		
																																					<option value="<?php echo $TissuePerfusionInitials2_nurseID;?>" <?php echo $sel;?>><?php echo $TissuePerfusionInitials2_nurseName;?></option>
																														<?php
                                                                                                                            }
                                                                                                                        ?>	
																
																												</select>
																										</td> 
                                                                                                        	
                                                                                                       
                                                                                                        
                                                                                                    </tr>
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td id="sTimeTissueID3" class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6"> 
                                                                                                        		<div class="input-group">
                                                                                                                		<input type="text"  name="alterTissuePerfusionTime3" id="bp_temp11" onFocus="changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" onKeyUp="displayText11=this.value;changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+(22*2);?>,95,'flag11');<?php if($alterTissuePerfusionTime3=="") {?>clearVal_c();displayTimeAmPm('bp_temp11');changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id'); <?php } ?>" class="form-control" style=" <?php echo $alterTissuePerfusionBackColor;?> " tabindex="1" value="<?php echo $alterTissuePerfusionTime3;?>"  />
                                                                                                                        
                                                                                                                        <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp11');changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');"><span class="fa fa-clock-o"></span></div>
                                                                                                      			</div>
                                                                                                        </td>
                                                                                                       
                                                                                                       <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
																												<select class="selectpicker form-control" onChange="changeDiffChbxColor(6,'bp_temp9','alterTissuePerfusionInitials1_list_id','bp_temp10','alterTissuePerfusionInitials2_list_id','bp_temp11','alterTissuePerfusionInitials3_list_id');" name="alterTissuePerfusionInitials3_list" id="alterTissuePerfusionInitials3_list_id"  style=" <?php echo $alterTissuePerfusionBackColor;?>">
																														<option value="" selected>Select</option>	
																														<?php
																																$alterTissuePerfusionInitials3Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																																$alterTissuePerfusionInitials3Res = imw_query($alterTissuePerfusionInitials3Qry) or die(imw_error());
																																while($alterTissuePerfusionInitials3Row=imw_fetch_array($alterTissuePerfusionInitials3Res)) {
																																	$TissuePerfusionInitials3_nurseID = $alterTissuePerfusionInitials3Row["usersId"];
																																	$TissuePerfusionInitials3_nurseName = $alterTissuePerfusionInitials3Row["lname"].", ".$alterTissuePerfusionInitials3Row["fname"]." ".$alterTissuePerfusionInitials3Row["mname"];
																																	$sel="";
																																	if($TissuePerfusionInitials3_nurseID==$alterTissuePerfusionInitials3) {
																																		$sel = "selected";
																																	} else if($alterTissuePerfusionInitials3=="" && $TissuePerfusionInitials3_nurseID==$currentLoggedinNurseId) {
																																		$sel = "selected";
																																	}else {
																																		$sel = "";
																																	}
																															
																																?>	
																
																																			<option value="<?php echo $TissuePerfusionInitials3_nurseID;?>" <?php echo $sel;?>><?php echo $TissuePerfusionInitials3_nurseName;?></option>
																																<?php
                                                                                                                                }
                                                                                                                                ?>
																
																												</select>
																										</td> 
                                                                                                        	
                                                                                                       
                                                                                                        
                                                                                                    </tr>
                                                                                               </tbody>
                                                                                         </table>
                                                                                  </div>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!--- New LAyout -->
                                                        <div class="full_width padding_15">
                                                                 <p class="rob l_height_28 full_width f_size border-bottom-p">  Alteration in gas exchange. </p>
                                                                 <div class="full_width">
                                                                            <div class="scheduler_table_Complete">
                                                                                   <div class="my_table_Checkall">
                                                                                   			<?php
																									$alterGasExchangeBackColor=$chngBckGroundColor;
																									if($alterGasExchangeTime1 || $alterGasExchangeInitials1 || $alterGasExchangeTime2 || $alterGasExchangeInitials2 || $alterGasExchangeTime3 || $alterGasExchangeInitials3) { 
																										$alterGasExchangeBackColor=$whiteBckGroundColor; 
																									}
																							?>
                                                                                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf ">
                                                                                                <thead class="cf">
                                                                                                    <tr>
                                                                                                        <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Time</th>
                                                                                                        <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6"> Initials</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                
                                                                                                    <tr>
                                                                                                        <td class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6" id="sTimeExchangeID1">
                                                                                                            <div class="input-group">
                                                                                                                <input type="text" id="bp_temp14" onFocus="changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" onKeyUp="displayText14=this.value;changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+185;?>,95,'flag14');<?php if($alterGasExchangeTime1=="") {?>clearVal_c();displayTimeAmPm('bp_temp14');changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id'); <?php } ?>" name="alterGasExchangeTime1"  class="form-control" style=" <?php echo $alterGasExchangeBackColor;?> " tabindex="1" value="<?php echo $alterGasExchangeTime1;?>"  />
                                                                                                                <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp14');changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');">
                                                                                                                    <span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                                            <select title="Select" onChange="changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" name="alterGasExchangeInitials1_list" id="alterGasExchangeInitials1_list_id" class="selectpicker form-control"  style=" <?php echo $alterGasExchangeBackColor;?>">
                                                                                                          		<option value="" selected>Select</option>
																												<?php
																														$alterGasExchangeInitials1Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																														$alterGasExchangeInitials1Res = imw_query($alterGasExchangeInitials1Qry) or die(imw_error());
																														while($alterGasExchangeInitials1Row=imw_fetch_array($alterGasExchangeInitials1Res)) {
																															$GasExchangeInitials1_nurseID = $alterGasExchangeInitials1Row["usersId"];
																															$GasExchangeInitials1_nurseName = $alterGasExchangeInitials1Row["lname"].", ".$alterGasExchangeInitials1Row["fname"]." ".$alterGasExchangeInitials1Row["mname"];
																															$sel="";
																															if($GasExchangeInitials1_nurseID==$alterGasExchangeInitials1) {
																																$sel = "selected";
																															} else if($alterGasExchangeInitials1=="" && $GasExchangeInitials1_nurseID==$currentLoggedinNurseId) {
																																$sel = "selected";
																															}else {
																																$sel = "";
																															}
																												
																												?>	
																																	<option value="<?php echo $GasExchangeInitials1_nurseID;?>" <?php echo $sel;?>><?php echo $GasExchangeInitials1_nurseName;?></option>
																												<?php
																													}
																												?>
                                                                                                            </select>		
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6" id="sTimeExchangeID2">
                                                                                                            <div class="input-group">
                                                                                                                <input type="text" name="alterGasExchangeTime2" id="bp_temp15" onFocus="changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" onKeyUp="displayText15=this.value;changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+185+(22*1);?>,95,'flag15');<?php if($alterGasExchangeTime2=="") {?>clearVal_c();displayTimeAmPm('bp_temp15');changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id'); <?php } ?>" class="form-control" style=" <?php echo $alterGasExchangeBackColor;?> " tabindex="1" value="<?php echo $alterGasExchangeTime2;?>"  />
                                                                                                                <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp15');changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');">
                                                                                                                    <span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                                            <select title="Select" onChange="changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" name="alterGasExchangeInitials2_list" id="alterGasExchangeInitials2_list_id" class="selectpicker form-control"  style=" <?php echo $alterGasExchangeBackColor;?>">
                                                                                                          		<option value="" selected>Select</option>
																												<?php
																															$alterGasExchangeInitials2Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																															$alterGasExchangeInitials2Res = imw_query($alterGasExchangeInitials2Qry) or die(imw_error());
																															while($alterGasExchangeInitials2Row=imw_fetch_array($alterGasExchangeInitials2Res)) {
																																$GasExchangeInitials2_nurseID = $alterGasExchangeInitials2Row["usersId"];
																																$GasExchangeInitials2_nurseName = $alterGasExchangeInitials2Row["lname"].", ".$alterGasExchangeInitials2Row["fname"]." ".$alterGasExchangeInitials2Row["mname"];
																																$sel="";
																																if($GasExchangeInitials2_nurseID==$alterGasExchangeInitials2) {
																																	$sel = "selected";
																																} else if($alterGasExchangeInitials2=="" && $GasExchangeInitials2_nurseID==$currentLoggedinNurseId) {
																																	$sel = "selected";
																																}else {
																																	$sel = "";
																																}
																												
																												?>	
																																		<option value="<?php echo $GasExchangeInitials2_nurseID;?>" <?php echo $sel;?>><?php echo $GasExchangeInitials2_nurseName;?></option>
																												<?php
																													}
																												?>
                                                                                                            </select>		
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6" id="sTimeExchangeID3">
                                                                                                            <div class="input-group">
                                                                                                                <input type="text" name="alterGasExchangeTime3" id="bp_temp16" onFocus="changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" onKeyUp="displayText16=this.value;changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+185+(22*3);?>,95,'flag16');<?php if($alterGasExchangeTime3=="") {?>clearVal_c();displayTimeAmPm('bp_temp16');changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id'); <?php } ?>"  class="form-control" style=" <?php echo $alterGasExchangeBackColor;?> " tabindex="1" value="<?php echo $alterGasExchangeTime3;?>"  />
                                                                                                                <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp16');changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');">
                                                                                                                    <span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                                            <select title="Select" onChange="changeDiffChbxColor(6,'bp_temp14','alterGasExchangeInitials1_list_id','bp_temp15','alterGasExchangeInitials2_list_id','bp_temp16','alterGasExchangeInitials3_list_id');" name="alterGasExchangeInitials3_list" id="alterGasExchangeInitials3_list_id" class="selectpicker form-control"  style=" <?php echo $alterGasExchangeBackColor;?>">
                                                                                                            	<option value="" selected >Select</option>
                                                                                                          		<?php
																														$alterGasExchangeInitials3Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																														$alterGasExchangeInitials3Res = imw_query($alterGasExchangeInitials3Qry) or die(imw_error());
																														while($alterGasExchangeInitials3Row=imw_fetch_array($alterGasExchangeInitials3Res)) {
																															$GasExchangeInitials3_nurseID = $alterGasExchangeInitials3Row["usersId"];
																															$GasExchangeInitials3_nurseName = $alterGasExchangeInitials3Row["lname"].", ".$alterGasExchangeInitials3Row["fname"]." ".$alterGasExchangeInitials3Row["mname"];
																															$sel="";
																															if($GasExchangeInitials3_nurseID==$alterGasExchangeInitials3) {
																																$sel = "selected";
																															} else if($alterGasExchangeInitials3=="" && $GasExchangeInitials3_nurseID==$currentLoggedinNurseId) {
																																$sel = "selected";
																															}else {
																																$sel = "";
																															}
																													
																													?>	
																															<option value="<?php echo $GasExchangeInitials3_nurseID;?>" <?php echo $sel;?>><?php echo $GasExchangeInitials3_nurseName;?></option>
																													<?php
																														}
																													?>
                                                                                                            </select>		
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    
                                                                                               </tbody>
                                                                                         </table>
                                                                                  </div>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                        <div class="full_width padding_15">
                                                                 <p class="rob l_height_28 full_width f_size border-bottom-p">  Alteration in comfort related pain</p>
                                                                 <div class="full_width">
                                                                            <div class="scheduler_table_Complete">
                                                                                   <div class="my_table_Checkall">
                                                                                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf ">
                                                                                                <thead class="cf">
                                                                                                    <tr>
                                                                                                        <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">Time</th>
                                                                                                        <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6"> Initials</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <?php
																											$alterComfortBackColor=$chngBckGroundColor;
																											if($alterComfortTime1 || $alterComfortInitials1 || $alterComfortTime2 || $alterComfortInitials2 || $alterComfortTime3 || $alterComfortInitials3) { 
																												$alterComfortBackColor=$whiteBckGroundColor; 
																											}
																								?>
                                                                                                <tbody>
                                                                                                
                                                                                                    <tr>
                                                                                                        <td class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6"  >
                                                                                                            <div class="input-group" id="sTimeComfortID1">
                                                                                                           		<input type="text" name="alterComfortTime1" id="bp_temp19" onFocus="changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" onKeyUp="displayText19=this.value;changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+320+(22*1);?>,95,'flag19');<?php if($alterComfortTime1=="") {?>clearVal_c();displayTimeAmPm('bp_temp19');changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id'); <?php } ?>" class="form-control" style=" <?php echo $alterComfortBackColor;?>" tabindex="1" value="<?php echo $alterComfortTime1;?>"  />     	 
                                                                                                                <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp19');changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');">
                                                                                                                    <span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                                            <select onChange="changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" name="alterComfortInitials1_list" id="alterComfortInitials1_list_id" class="selectpicker form-control" style=" <?php echo $alterComfortBackColor;?>" title="Select">
                                                                                                            		<option value="" selected>Select</option>	
																													<?php
																															$alterComfortInitials1Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																															$alterComfortInitials1Res = imw_query($alterComfortInitials1Qry) or die(imw_error());
																															while($alterComfortInitials1Row=imw_fetch_array($alterComfortInitials1Res)) {
																																$ComfortInitials1_nurseID = $alterComfortInitials1Row["usersId"];
																																$ComfortInitials1_nurseName = $alterComfortInitials1Row["lname"].", ".$alterComfortInitials1Row["fname"]." ".$alterComfortInitials1Row["mname"];
																																$sel="";
																																if($ComfortInitials1_nurseID==$alterComfortInitials1) {
																																	$sel = "selected";
																																} else if($alterComfortInitials1=="" && $ComfortInitials1_nurseID==$currentLoggedinNurseId) {
																																	$sel = "selected";
																																}else {
																																	$sel = "";
																																}
                                                                                                                
                                                                                                                ?>	
                                                                                                                        			<option value="<?php echo $ComfortInitials1_nurseID;?>" <?php echo $sel;?>><?php echo $ComfortInitials1_nurseName;?></option>
                                                                                                                <?php
                                                                                                                  			  }
                                                                                                                ?>
                                                            											</select>		
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6"  >
                                                                                                            <div class="input-group" id="sTimeComfortID2">
                                                                                                           		<input type="text" name="alterComfortTime2" id="bp_temp20" onFocus="changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" onKeyUp="displayText20=this.value;changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+320+(22*2);?>,95,'flag20');<?php if($alterComfortTime2=="") {?>clearVal_c();displayTimeAmPm('bp_temp20');changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id'); <?php } ?>"  class="form-control" style=" <?php echo $alterComfortBackColor;?>" tabindex="1" value="<?php echo $alterComfortTime2;?>"  />     	 
                                                                                                                <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp20');changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');">
                                                                                                                    <span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                                            <select onChange="changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" name="alterComfortInitials2_list" id="alterComfortInitials2_list_id" class="selectpicker form-control" style=" <?php echo $alterComfortBackColor;?>" title="Select">
                                                                                                            		<option value="" selected>Select</option>	
																													<?php
																															$alterComfortInitials2Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																															$alterComfortInitials2Res = imw_query($alterComfortInitials2Qry) or die(imw_error());
																															while($alterComfortInitials2Row=imw_fetch_array($alterComfortInitials2Res)) {
																																$ComfortInitials2_nurseID = $alterComfortInitials2Row["usersId"];
																																$ComfortInitials2_nurseName = $alterComfortInitials2Row["lname"].", ".$alterComfortInitials2Row["fname"]." ".$alterComfortInitials2Row["mname"];
																																$sel="";
																																if($ComfortInitials2_nurseID==$alterComfortInitials2) {
																																	$sel = "selected";
																																} else if($alterComfortInitials2=="" && $ComfortInitials2_nurseID==$currentLoggedinNurseId) {
																																	$sel = "selected";
																																}else {
																																	$sel = "";
																																}
																													
																													?>	
																																	<option value="<?php echo $ComfortInitials2_nurseID;?>" <?php echo $sel;?>><?php echo $ComfortInitials2_nurseName;?></option>
																													<?php
																															}
																													?>
                                                            											</select>		
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td class="text-left  col-md-6 col-lg-6 col-sm-6 col-xs-6"  >
                                                                                                            <div class="input-group" id="sTimeComfortID3">
                                                                                                           		<input type="text" name="alterComfortTime3" id="bp_temp21" onFocus="changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" onKeyUp="displayText21=this.value;changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+320+(22*3);?>,95,'flag21');<?php if($alterComfortTime3=="") {?>clearVal_c();displayTimeAmPm('bp_temp21');changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id'); <?php } ?>"class="form-control" style=" <?php echo $alterComfortBackColor;?>" tabindex="1" value="<?php echo $alterComfortTime3;?>"  />     	 
                                                                                                                <div class="input-group-addon" onClick="displayTimeAmPm('bp_temp21');changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');">
                                                                                                                    <span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                                                                                            <select onChange="changeDiffChbxColor(6,'bp_temp19','alterComfortInitials1_list_id','bp_temp20','alterComfortInitials2_list_id','bp_temp21','alterComfortInitials3_list_id');" name="alterComfortInitials3_list" id="alterComfortInitials3_list_id" class="selectpicker form-control" style=" <?php echo $alterComfortBackColor;?>" title="Select">
                                                                                                            		<option value="" selected>Select</option>	
																													<?php
																																$alterComfortInitials3Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																																$alterComfortInitials3Res = imw_query($alterComfortInitials3Qry) or die(imw_error());
																																while($alterComfortInitials3Row=imw_fetch_array($alterComfortInitials3Res)) {
																																	$ComfortInitials3_nurseID = $alterComfortInitials3Row["usersId"];
																																	$ComfortInitials3_nurseName = $alterComfortInitials3Row["lname"].", ".$alterComfortInitials3Row["fname"]." ".$alterComfortInitials3Row["mname"];
																																	$sel="";
																																	if($ComfortInitials3_nurseID==$alterComfortInitials3) {
																																		$sel = "selected";
																																	} else if($alterComfortInitials3=="" && $ComfortInitials3_nurseID==$currentLoggedinNurseId) {
																																		$sel = "selected";
																																	}else {
																																		$sel = "";
																																	}
																													
																													?>	
																																			<option value="<?php echo $ComfortInitials3_nurseID;?>" <?php echo $sel;?>><?php echo $ComfortInitials3_nurseName;?></option>
																													<?php
																														}
																													?>
                                                            											</select>		
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    
                                                                                               </tbody>
                                                                                               
                                                                                         </table>
                                                                                         
                                                                                  </div>
                                                                                  
                                                                 </div>
                                                            </div>
                                                        </div>
                                                        <!--- HILNEw Layout -->
                                                        
                                                    </div>
                                               </div>
                                           </div>
                                           
                                           <div class="col-lg-5 col-sm-12 col-xs-12 col-md-4">
                                           			<?php
														$monitorTissueBackColor=$chngBckGroundColor;
														if($monitorTissuePerfusion || $implementComplicationMeasure || $assessPlus || $telemetry || $otherTPNurseInter) { 
															$monitorTissueBackColor=$whiteBckGroundColor; 
														}
													?>	
                                                    
                                                    <div class="panel panel-default bg_panel_anesth">
                                                        <div class="panel-heading">
                                                              <label class="margin_0"> NURSING INTERVENTION   </label>
                                                        </div>
                                                        <div class="panel-body">   
                                                            <div class="full_width padding_15">
                                                                 <div class="full_width">
                                                                 	<label class="full_width f_normal"> <span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_monitorTissuePerfusion_id','chbx_implementComplicationMeasure_id','chbx_assessPlus_id','chbx_telemetry_id','chbx_otherTPNurseInter_id');" style=" <?php echo $monitorTissueBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_monitorTissuePerfusion_id" name="chbx_monitorTissuePerfusion" <?php if($monitorTissuePerfusion=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Monitor the patient for changes in
	Systemic and peripheral tissue perfusion </label>
    
    												            	<label class="full_width f_normal"><span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_monitorTissuePerfusion_id','chbx_implementComplicationMeasure_id','chbx_assessPlus_id','chbx_telemetry_id','chbx_otherTPNurseInter_id');" style=" <?php echo $monitorTissueBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_implementComplicationMeasure_id" name="chbx_implementComplicationMeasure" <?php if($implementComplicationMeasure=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Implement measure to minimize
	complication and on dimished perfusion </label>	
    	
    																<label class="full_width f_normal">  <span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_monitorTissuePerfusion_id','chbx_implementComplicationMeasure_id','chbx_assessPlus_id','chbx_telemetry_id','chbx_otherTPNurseInter_id');" style=" <?php echo $monitorTissueBackColor;?>" ><input class="field"  type="checkbox" value="Yes" id="chbx_assessPlus_id" name="chbx_assessPlus"  <?php if($assessPlus=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;Assess pulse </label>
                                                                    			
                                                                    <label class="full_width f_normal"> <span class="colorChkBx" onClick="changeDiffChbxColor(5,'chbx_monitorTissuePerfusion_id','chbx_implementComplicationMeasure_id','chbx_assessPlus_id','chbx_telemetry_id','chbx_otherTPNurseInter_id');" style=" <?php echo $monitorTissueBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_telemetry_id" name="chbx_telemetry" <?php if($telemetry=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;Telemetry </label>		
                                                                    <label class="full_width f_normal"> <span class="colorChkBx"  onClick="changeDiffChbxColor(5,'chbx_monitorTissuePerfusion_id','chbx_implementComplicationMeasure_id','chbx_assessPlus_id','chbx_telemetry_id','chbx_otherTPNurseInter_id');" style=" <?php echo $monitorTissueBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_otherTPNurseInter_id" name="chbx_otherTPNurseInter" <?php if($otherTPNurseInter=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;	Other &nbsp; <input type="text" id="txt_otherTPNurseInterDesc_id" name="txt_otherTPNurseInterDesc"  class="inline-block-imp inline_form_text"  style=" border:1px solid #ccccc; width:100px;" tabindex="1" value="<?php echo stripslashes($otherTPNurseInterDesc);?>" /> </label>		
                                                          	
                                                            	  </div>
                                                                	<div class="full_width ">
                                                                   		<div class="border-dashed margin_small_only"></div>
                                                                    </div>		          
                                                                    <div class="full_width">
                                                                    <?php
																				$assessRespiratoryBackColor=$chngBckGroundColor;
																				if($assessRespiratoryStatus || $positionOptimalChestExcusion || $monitorOxygenationGE || $otherGENurseInter) { 
																					$assessRespiratoryBackColor=$whiteBckGroundColor; 
																				}
																	?>		
                                                                 	<label class="full_width f_normal"> <span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_assessRespiratoryStatus_id','chbx_positionOptimalChestExcusion_id','chbx_monitorOxygenationGE_id','chbx_otherGENurseInter_id');" style=" <?php echo $assessRespiratoryBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_assessRespiratoryStatus_id" name="chbx_assessRespiratoryStatus" <?php if($assessRespiratoryStatus=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; Assess respiratory status  </label>
                                                                    
    												            	<label class="full_width f_normal"> <span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_assessRespiratoryStatus_id','chbx_positionOptimalChestExcusion_id','chbx_monitorOxygenationGE_id','chbx_otherGENurseInter_id');" style=" <?php echo $assessRespiratoryBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_positionOptimalChestExcusion_id" name="chbx_positionOptimalChestExcusion" <?php if($positionOptimalChestExcusion=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Position the patient for optimal chest
	Excursion and its exchange  </label>		
    																<label class="full_width f_normal"> <span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_assessRespiratoryStatus_id','chbx_positionOptimalChestExcusion_id','chbx_monitorOxygenationGE_id','chbx_otherGENurseInter_id');" style=" <?php echo $assessRespiratoryBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_monitorOxygenationGE_id" name="chbx_monitorOxygenationGE" <?php if($monitorOxygenationGE=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; Monitor oxygenation  </label>			
                                                                    
                                                                    <label class="full_width f_normal"> <span class="colorChkBx" onClick="changeDiffChbxColor(4,'chbx_assessRespiratoryStatus_id','chbx_positionOptimalChestExcusion_id','chbx_monitorOxygenationGE_id','chbx_otherGENurseInter_id');" style=" <?php echo $assessRespiratoryBackColor;?>" ><input  type="checkbox" value="Yes" id="chbx_otherGENurseInter_id" name="chbx_otherGENurseInter" <?php if($otherGENurseInter=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;  	Other &nbsp; <input type="text" id="txt_otherGENurseInterDesc_id" name="txt_otherGENurseInterDesc" class="inline-block-imp inline_form_text"  tabindex="1" value="<?php echo stripslashes($otherGENurseInterDesc);?>"  /> </label>		
                                                          	
                                                            	  </div> 	                                                                 			
                                                                  	<div class="full_width ">
                                                                   		<div class="border-dashed margin_small_only"></div>
                                                                    </div>		          
                                                                    <div class="full_width">
                                                                    <?php
																			$assessPainBackColor=$chngBckGroundColor;
																			if($assessPain || $usePharmacology || $monitorOxygenationComfort || $otherComfortNurseInter) { 
																				$assessPainBackColor=$whiteBckGroundColor; 
																			}
																	?>	
                                                                 	<label class="full_width f_normal"> <span onClick="changeDiffChbxColor(4,'chbx_assessPain_id','chbx_usePharmacology_id','chbx_monitorOxygenationComfort_id','chbx_otherComfortNurseInter_id');" class="colorChkBx" style=" <?php echo $assessPainBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_assessPain_id" name="chbx_assessPain" <?php if($assessPain=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Assess pain  </label>
    												            	<label class="full_width f_normal"> <span onClick="changeDiffChbxColor(4,'chbx_assessPain_id','chbx_usePharmacology_id','chbx_monitorOxygenationComfort_id','chbx_otherComfortNurseInter_id');" class="colorChkBx" style=" <?php echo $assessPainBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_usePharmacology_id" name="chbx_usePharmacology" <?php if($usePharmacology=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Use pharmacology interventions to
	relieve pain </label>		
    																<label class="full_width f_normal"> <span onClick="changeDiffChbxColor(4,'chbx_assessPain_id','chbx_usePharmacology_id','chbx_monitorOxygenationComfort_id','chbx_otherComfortNurseInter_id');" class="colorChkBx" style=" <?php echo $assessPainBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_monitorOxygenationComfort_id" name="chbx_monitorOxygenationComfort" <?php if($monitorOxygenationComfort=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;Monitor oxygenation</label>	
                                                                    		
                                                                    <label class="full_width f_normal"> <span onClick="changeDiffChbxColor(4,'chbx_assessPain_id','chbx_usePharmacology_id','chbx_monitorOxygenationComfort_id','chbx_otherComfortNurseInter_id');" class="colorChkBx" style=" <?php echo $assessPainBackColor;?>" ><input type="checkbox" value="Yes" id="chbx_otherComfortNurseInter_id" name="chbx_otherComfortNurseInter" <?php if($otherComfortNurseInter=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Other &nbsp; <input type="text" id="txt_otherComfortNurseInterDesc_id" name="txt_otherComfortNurseInterDesc" tabindex="1" value="<?php echo stripslashes($otherComfortNurseInterDesc);?>" class="inline-block-imp inline_form_text" /> </label>		
                                                          	
                                                            	  </div> 	                                                                 			
                                                                
                                                            </div>
                                                        
                                                        <!--- New LAyout -->
                                                        
                                                        
                                                    </div>
                                               </div>
                                           </div>
                                           
                                           <div class="col-lg-4 col-sm-12 col-xs-12 col-md-4">
                                                    <div class="panel panel-default bg_panel_anesth">
                                                        <div class="panel-heading">
                                                              <label class="margin_0">  GOAL/EVALUATION    </label>
                                                        </div>
                                                        <div class="panel-body">   
                                                            <div class="full_width padding_15">
                                                                 <div class="full_width">
                                                                 	<p class="rob l_height_28 full_width f_size border-bottom-p">  Patient will show signs of adequate tissue
perfusion </p>														
																	<div class="full_width">
                                                                 			<div class="scheduler_table_Complete">
                                                                                   <div class="my_table_Checkall">
                                                                                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf ">
                                                                                                    <tr>
                                                                                                    	
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left">
                                                                                                             <label class="padding_15 f_size"> 																																						
                                                                                                               Achieve		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                             <label class="f_normal" onClick="javascript:checkSingle('chbx_tiss_archive1_yes','chbx_tiss_archive1')"> 																																						
                                                                                                               	<span class="colorChkBx" onClick="changeChbxColor('chbx_tiss_archive1')" style=" <?php if($goalTPArchieve1) { echo $whiteBckGroundColor;}?>" ><input class="field"  type="checkbox" value="Yes" id="chbx_tiss_archive1_yes" name="chbx_tiss_archive1" <?php if($goalTPArchieve1=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; Yes		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                            <label class="f_normal" onClick="javascript:checkSingle('chbx_tiss_archive1_no','chbx_tiss_archive1')"> 																																						
                                                                                                               	<span class="colorChkBx"  onClick="changeChbxColor('chbx_tiss_archive1')" style=" <?php if($goalTPArchieve1) { echo $whiteBckGroundColor;}?>" ><input class="field"  type="checkbox" value="No" id="chbx_tiss_archive1_no" name="chbx_tiss_archive1" <?php if($goalTPArchieve1=="No") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; No	
                                                                                                            </label>
                                                                                                            </th>
                                                                                                    </tr>
                                                                                           
                                                                                                    <tr>
                                                                                                    	<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left border_adjust_tb">&nbsp; 

                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                                           	<div class="input-group"  id="sTimegoalTPID1">
	                                                                                                         	<input type="text" name="goalTPAchieveTime1" id="bp_temp12" onKeyUp="displayText12=this.value" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+(22*1);?>,545,'flag12');<?php if($goalTPAchieveTime1=="") {?>clearVal_c();return displayTimeAmPm('bp_temp12'); <?php } ?>" class="form-control" tabindex="1" value="<?php echo $goalTPAchieveTime1;?>"  />
                                                                                                                <div class="input-group-addon" onClick="return displayTimeAmPm('bp_temp12');">
                                                                                                                	<span class="fa fa-clock-o" ></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
																											<select class="selectpicker form-control bs-select-hidden"  title="Select" name="goalTPAchieveInitial1_list">
                                                                                                            	<option value="" selected>Select</option>	
																													<?php
                                                                                                                    $goalTPAchieveInitial1Qry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                                                                                    $goalTPAchieveInitial1Res = imw_query($goalTPAchieveInitial1Qry) or die(imw_error());
                                                                                                                    while($goalTPAchieveInitial1Row=imw_fetch_array($goalTPAchieveInitial1Res)) {
                                                                                                                        $goalTPAchieveInitial1_nurseID = $goalTPAchieveInitial1Row["usersId"];
                                                                                                                        $goalTPAchieveInitial1_nurseName = $goalTPAchieveInitial1Row["lname"].", ".$goalTPAchieveInitial1Row["fname"]." ".$goalTPAchieveInitial1Row["mname"];
                                                                                                                        $sel="";
                                                                                                                        if($goalTPAchieveInitial1_nurseID==$goalTPAchieveInitial1) {
                                                                                                                            $sel = "selected";
                                                                                                                        } else if($goalTPAchieveInitial1=="" && $goalTPAchieveInitial1_nurseID==$currentLoggedinNurseId) {
                                                                                                                            $sel = "selected";
                                                                                                                        }else {
                                                                                                                            $sel = "";
                                                                                                                        }
                                                                                                                
                                                                                                                ?>	
                                                                                                                        <option value="<?php echo $goalTPAchieveInitial1_nurseID;?>" <?php echo $sel;?>><?php echo $goalTPAchieveInitial1_nurseName;?></option>
                                                                                                                <?php
                                                                                                                    }
                                                                                                                ?>	
                                                                                                           	</select>
                                                                                                         </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                    	
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left">
                                                                                                             <label class="f_size padding_15"> 						
                                                                                                             	Achieve
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                             <label class="f_normal" onClick="javascript:checkSingle('chbx_tiss_archive2_yes','chbx_tiss_archive2')"> 																																						
                                                                                                               		<span class="colorChkBx"  onClick="changeChbxColor('chbx_tiss_archive2')" style=" <?php if($goalTPAchieve2) { echo $whiteBckGroundColor;}?>" ><input class="field"  type="checkbox" value="Yes" id="chbx_tiss_archive2_yes" name="chbx_tiss_archive2" <?php if($goalTPAchieve2=="Yes") { echo "checked"; }?>  tabindex="7" ></span>&nbsp;Yes		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                            <label class="f_normal" onClick="javascript:checkSingle('chbx_tiss_archive2_no','chbx_tiss_archive2')"> 																																						
                                                                                                               		<span class="colorChkBx" onClick="changeChbxColor('chbx_tiss_archive2')" style=" <?php if($goalTPAchieve2) { echo $whiteBckGroundColor;}?>" ><input class="field"  type="checkbox" value="No" id="chbx_tiss_archive2_no" name="chbx_tiss_archive2" <?php if($goalTPAchieve2=="No") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; No	
                                                                                                            </label></th>
                                                                                                    </tr>
                                                                                           
                                                                                                    <tr>
                                                                                                    	<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 border_adjust_tb border_bottom_adj ">&nbsp; 

                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                                           	<div class="input-group" id="sTimegoalTPID2">
	                                                                                                         	 <input type="text" id="bp_temp13" onKeyUp="displayText13=this.value" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+(22*2);?>,545,'flag13');<?php if($goalTPAchieveTime2=="") {?>clearVal_c();return displayTimeAmPm('bp_temp13'); <?php } ?>" name="goalTPAchieveTime2" class="form-control" tabindex="1" value="<?php echo $goalTPAchieveTime2;?>"  /> 
                                                                                                                <div class="input-group-addon" onClick="return displayTimeAmPm('bp_temp13');">
                                                                                                                	<span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
																											<select class="selectpicker form-control bs-select-hidden" name="goalTPAchieveInitial2_list" title="Select">
                                                                                                            	<option value="" selected>Select</option>	
																												<?php
                                                                                                                $goalTPAchieveInitial2Qry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                                                                                $goalTPAchieveInitial2Res = imw_query($goalTPAchieveInitial2Qry) or die(imw_error());
                                                                                                                while($goalTPAchieveInitial2Row=imw_fetch_array($goalTPAchieveInitial2Res)) {
                                                                                                                    $goalTPAchieveInitial2_nurseID = $goalTPAchieveInitial2Row["usersId"];
                                                                                                                    $goalTPAchieveInitial2_nurseName = $goalTPAchieveInitial2Row["lname"].", ".$goalTPAchieveInitial2Row["fname"]." ".$goalTPAchieveInitial2Row["mname"];
                                                                                                                    $sel="";
                                                                                                                    if($goalTPAchieveInitial2_nurseID==$goalTPAchieveInitial2) {
                                                                                                                        $sel = "selected";
                                                                                                                    } else if($goalTPAchieveInitial2=="" && $goalTPAchieveInitial2_nurseID==$currentLoggedinNurseId) {
                                                                                                                        $sel = "selected";
                                                                                                                    }else {
                                                                                                                        $sel = "";
                                                                                                                    }
                                                                                                            
                                                                                                            ?>	
                                                                                                                    <option value="<?php echo $goalTPAchieveInitial2_nurseID;?>" <?php echo $sel;?>><?php echo $goalTPAchieveInitial2_nurseName;?></option>
                                                                                                            <?php
                                                                                                                }
                                                                                                            ?>	
                                                                                                           	</select>
                                                                                                         </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    
                                                                                               </tbody>
                                                                                         </table>
                                                                                  </div>
                                                                		 </div>
                                                            	</div>
                                                               <p class="rob l_height_28 full_width f_size border-bottom-p">  Maintain patient airways with adequate
exchange. </p>		
                                                             	<div class="full_width">
                                                                 			<div class="scheduler_table_Complete">
                                                                                   <div class="my_table_Checkall">
                                                                                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf ">
                                                                                                    <tr>
                                                                                                    	
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left">
                                                                                                             <label class="padding_15 f_size"> 																																						
                                                                                                               Achieve		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                             <label class="f_normal" onClick="javascript:checkSingle('chbx_adq_exc1_yes','chbx_adq_exc1')"> 																																						
                                                                                                               	<span class="colorChkBx"  onClick="changeChbxColor('chbx_adq_exc1')" style=" <?php if($goalGEAchieve1) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_adq_exc1_yes" name="chbx_adq_exc1" <?php if($goalGEAchieve1=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; Yes		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                            <label class="f_normal" onClick="javascript:checkSingle('chbx_adq_exc1_no','chbx_adq_exc1')" > 																																						
                                                                                                               	<span class="colorChkBx"   onClick="changeChbxColor('chbx_adq_exc1')" style=" <?php if($goalGEAchieve1) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="No" id="chbx_adq_exc1_no" name="chbx_adq_exc1" <?php if($goalGEAchieve1=="No") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; No	
                                                                                                            </label>
                                                                                                            </th>
                                                                                                    </tr>
                                                                                           
                                                                                                    <tr>
                                                                                                    	<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left border_adjust_tb">&nbsp; 

                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                                           	<div class="input-group" id="sTimegoalGEID1" >
	                                                                                                         	 <input type="text" name="goalGEAchieveTime1" id="bp_temp17" onKeyUp="displayText17=this.value" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+185+(22*1);?>,545,'flag17');<?php if($goalGEAchieveTime1=="") {?>clearVal_c();return displayTimeAmPm('bp_temp17'); <?php } ?>" tabindex="1" value="<?php echo $goalGEAchieveTime1;?>"  class="form-control" />	
                                                                                                                <div class="input-group-addon">
                                                                                                                	<span class="fa fa-clock-o" onClick="return displayTimeAmPm('bp_temp17');"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
																											<select name="goalGEAchieveInitial1_list" class="selectpicker form-control bs-select-hidden" title="Select">
                                                                                                            	<option value="" selected>Select</option>
                                                                                                            <?php
																													$goalGEAchieveInitial1Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																													$goalGEAchieveInitial1Res = imw_query($goalGEAchieveInitial1Qry) or die(imw_error());
																													while($goalGEAchieveInitial1Row=imw_fetch_array($goalGEAchieveInitial1Res)) {
																														$goalGEAchieveInitial1_nurseID = $goalGEAchieveInitial1Row["usersId"];
																														$goalGEAchieveInitial1_nurseName = $goalGEAchieveInitial1Row["lname"].", ".$goalGEAchieveInitial1Row["fname"]." ".$goalGEAchieveInitial1Row["mname"];
																														$sel="";
																														if($goalGEAchieveInitial1_nurseID==$goalGEAchieveInitial1) {
																															$sel = "selected";
																														} else if($goalGEAchieveInitial1=="" && $goalGEAchieveInitial1_nurseID==$currentLoggedinNurseId) {
																															$sel = "selected";
																														}else {
																															$sel = "";
																													}
																											
																											?>	
																													<option value="<?php echo $goalGEAchieveInitial1_nurseID;?>" <?php echo $sel;?>><?php echo $goalGEAchieveInitial1_nurseName;?></option>
																											<?php
																												}
																											?>	
                                                                                                            </select>
                                                                                                         </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                    	
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left">
                                                                                                             <label class="f_size padding_15"> 						
                                                                                                             	Achieve
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                             <label class="f_normal" onClick="javascript:checkSingle('chbx_adq_exc2_yes','chbx_adq_exc2')"> 																																						
                                                                                                               	<span onClick="changeChbxColor('chbx_adq_exc2')" class="colorChkBx" style=" <?php if($goalGEAchieve2) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_adq_exc2_yes" name="chbx_adq_exc2" <?php if($goalGEAchieve2=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Yes		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                            <label class="f_normal" onClick="javascript:checkSingle('chbx_adq_exc2_no','chbx_adq_exc2')"> 																																						
                                                                                                               	<span onClick="changeChbxColor('chbx_adq_exc2')" class="colorChkBx" style=" <?php if($goalGEAchieve2) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="No" id="chbx_adq_exc2_no" name="chbx_adq_exc2" <?php if($goalGEAchieve2=="No") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; No	
                                                                                                            </label></th>
                                                                                                    </tr>
                                                                                           
                                                                                                    <tr>
                                                                                                    	<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 border_adjust_tb border_bottom_adj ">&nbsp; 

                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                                           	<div class="input-group" id="sTimegoalGEID2">
	                                                                                                         	<input type="text" name="goalGEAchieveTime2" id="bp_temp18" onKeyUp="displayText18=this.value" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+185+(22*2);?>,545,'flag18');<?php if($goalGEAchieveTime2=="") {?>clearVal_c();return displayTimeAmPm('bp_temp18'); <?php } ?>" tabindex="1" value="<?php echo $goalGEAchieveTime2;?>" class="form-control" />	
                                                                                                                <div class="input-group-addon" onClick="return displayTimeAmPm('bp_temp18');">
                                                                                                                	<span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
																											<select name="goalGEAchieveInitial2_list"class="selectpicker form-control bs-select-hidden" title="Select">
                                                                                                                    <option value="" selected>Select</option>	
																													<?php
                                                                                                                        $goalGEAchieveInitial2Qry = "select * from users where user_type='Nurse' ORDER BY lname";
                                                                                                                        $goalGEAchieveInitial2Res = imw_query($goalGEAchieveInitial2Qry) or die(imw_error());
                                                                                                                        while($goalGEAchieveInitial2Row=imw_fetch_array($goalGEAchieveInitial2Res)) {
                                                                                                                            $goalGEAchieveInitial2_nurseID = $goalGEAchieveInitial2Row["usersId"];
                                                                                                                            $goalGEAchieveInitial2_nurseName = $goalGEAchieveInitial2Row["lname"].", ".$goalGEAchieveInitial2Row["fname"]." ".$goalGEAchieveInitial2Row["mname"];
                                                                                                                            $sel="";
                                                                                                                            if($goalGEAchieveInitial2_nurseID==$goalGEAchieveInitial2) {
                                                                                                                                $sel = "selected";
                                                                                                                            } else if($goalGEAchieveInitial2=="" && $goalGEAchieveInitial2_nurseID==$currentLoggedinNurseId) {
                                                                                                                                $sel = "selected";
                                                                                                                            }else {
                                                                                                                                $sel = "";
                                                                                                                            }
                                                                                                                    
                                                                                                                    ?>	
                                                                                                                            <option value="<?php echo $goalGEAchieveInitial2_nurseID;?>" <?php echo $sel;?>><?php echo $goalGEAchieveInitial2_nurseName;?></option>
                                                                                                                    <?php
                                                                                                                        }
                                                                                                           			 ?>
                                                                                                            </select>
                                                                                                         </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    
                                                                                               </tbody>
                                                                                         </table>
                                                                                  </div>
                                                                		 </div>
                                                            	</div>
                                                                <p class="rob l_height_28 full_width f_size border-bottom-p">  Patient denies or shows no sign of excessive pain. </p>														   
                                                                <div class="full_width">
                                                                 			<div class="scheduler_table_Complete">
                                                                                   <div class="my_table_Checkall">
                                                                                            <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf ">
                                                                                                    <tr>
                                                                                                    	
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left">
                                                                                                             <label class="padding_15 f_size"> 																																						
                                                                                                               Achieve		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                             <label class="f_normal" onClick="javascript:checkSingle('chbx_excs_pain1_yes','chbx_excs_pain1')"> 																																						
                                                                                                               	<span onClick="changeChbxColor('chbx_excs_pain1')" class="colorChkBx" style=" <?php if($goalCRPAchieve1) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_excs_pain1_yes" name="chbx_excs_pain1" <?php if($goalCRPAchieve1=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp; Yes		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                            <label class="f_normal" onClick="javascript:checkSingle('chbx_excs_pain1_no','chbx_excs_pain1')"> 																																						
                                                                                                               	<span onClick="changeChbxColor('chbx_excs_pain1')" class="colorChkBx" style=" <?php if($goalCRPAchieve1) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="No" id="chbx_excs_pain1_no" name="chbx_excs_pain1" <?php if($goalCRPAchieve1=="No") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; No	
                                                                                                            </label>
                                                                                                            </th>
                                                                                                    </tr>
                                                                                           
                                                                                                    <tr>
                                                                                                    	<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left border_adjust_tb">&nbsp; 

                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                                           	<div class="input-group" id="sTimegoalCRPID1">
	                                                                                                         	 <input type="text" name="goalCRPAchieveTime1" id="bp_temp22" onKeyUp="displayText22=this.value" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+350+(22*1);?>,545,'flag22');<?php if($goalCRPAchieveTime1=="") {?>clearVal_c();return displayTimeAmPm('bp_temp22'); <?php } ?>" class="form-control" tabindex="1" value="<?php echo $goalCRPAchieveTime1;?>"  />
                                                                                                                <div class="input-group-addon" onClick="return displayTimeAmPm('bp_temp22');">
                                                                                                                	<span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
																											<select  name="goalCRPAchieveInitial1_list"  class="selectpicker form-control bs-select-hidden" title="Select">
                                                                                                            	<option value="" selected> Select   </option>
                                                                                                           		<?php
																														$goalCRPAchieveInitial1Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																														$goalCRPAchieveInitial1Res = imw_query($goalCRPAchieveInitial1Qry) or die(imw_error());
																														while($goalCRPAchieveInitial1Row=imw_fetch_array($goalCRPAchieveInitial1Res)) {
																															$goalCRPAchieveInitial1_nurseID = $goalCRPAchieveInitial1Row["usersId"];
																															$goalCRPAchieveInitial1_nurseName = $goalCRPAchieveInitial1Row["lname"].", ".$goalCRPAchieveInitial1Row["fname"]." ".$goalCRPAchieveInitial1Row["mname"];
																															$sel="";
																															if($goalCRPAchieveInitial1_nurseID==$goalCRPAchieveInitial1) {
																																$sel = "selected";
																															} else if($goalCRPAchieveInitial1=="" && $goalCRPAchieveInitial1_nurseID==$currentLoggedinNurseId) {
																																$sel = "selected";
																															}else {
																																$sel = "";
																															}
																													
																													?>	
																															<option value="<?php echo $goalCRPAchieveInitial1_nurseID;?>" <?php echo $sel;?>><?php echo $goalCRPAchieveInitial1_nurseName;?></option>
																													<?php
																														}
																													?>		
                                                                                                            </select>
                                                                                                         </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                    	
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-left">
                                                                                                             <label class="f_size padding_15"> 						
                                                                                                             	Achieve
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                             <label class="f_normal" onClick="javascript:checkSingle('chbx_excs_pain2_yes','chbx_excs_pain2')"> 																																						
                                                                                                               	<span onClick="changeChbxColor('chbx_excs_pain2')" class="colorChkBx" style=" <?php if($goalCRPAchieve2) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_excs_pain2_yes" name="chbx_excs_pain2" <?php if($goalCRPAchieve2=="Yes") { echo "checked"; }?>  tabindex="7" /></span>&nbsp;Yes		
                                                                                                            </label>
                                                                                                         </th>
                                                                                                        <th class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">
                                                                                                            <label class="f_normal" onClick="javascript:checkSingle('chbx_excs_pain2_no','chbx_excs_pain2')"> 																																						
                                                                                                               		<span onClick="changeChbxColor('chbx_excs_pain2')" class="colorChkBx" style=" <?php if($goalCRPAchieve2) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="No" id="chbx_excs_pain2_no" name="chbx_excs_pain2" <?php if($goalCRPAchieve2=="No") { echo "checked"; }?>  tabindex="7" ></span>&nbsp; No	
                                                                                                            </label></th>
                                                                                                    </tr>
                                                                                           
                                                                                                    <tr>
                                                                                                    	<td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 border_adjust_tb border_bottom_adj ">&nbsp; 

                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
                                                                                                           	<div class="input-group" id="sTimegoalCRPID2" >
	                                                                                                         	 <input type="text" name="goalCRPAchieveTime2" id="bp_temp23" onKeyUp="displayText23=this.value" onClick="getShowNewPos(<?php echo $calculatorAlterSetValue+350+(22*3);?>,545,'flag23');<?php if($goalCRPAchieveTime2=="") {?>clearVal_c();return displayTimeAmPm('bp_temp23'); <?php } ?>" class="form-control"  tabindex="1" value="<?php echo $goalCRPAchieveTime2;?>"  />
                                                                                                                <div class="input-group-addon" onClick="return displayTimeAmPm('bp_temp23');">
                                                                                                                	<span class="fa fa-clock-o"></span>
                                                                                                                </div>
                                                                                                            </div>	
                                                                                                                
                                                                                                        </td>
                                                                                                        <td class="text-left  col-md-4 col-lg-4 col-sm-4 col-xs-4">
																											<select  name="goalCRPAchieveInitial2_list" class="selectpicker form-control bs-select-hidden" title="Select">
                                                                                                            		<option value="" selected>Select</option>	
																													<?php
																															$goalCRPAchieveInitial2Qry = "select * from users where user_type='Nurse' ORDER BY lname";
																															$goalCRPAchieveInitial2Res = imw_query($goalCRPAchieveInitial2Qry) or die(imw_error());
																															while($goalCRPAchieveInitial2Row=imw_fetch_array($goalCRPAchieveInitial2Res)) {
																																$goalCRPAchieveInitial2_nurseID = $goalCRPAchieveInitial2Row["usersId"];
																																$goalCRPAchieveInitial2_nurseName = $goalCRPAchieveInitial2Row["lname"].", ".$goalCRPAchieveInitial2Row["fname"]." ".$goalCRPAchieveInitial2Row["mname"];
																																$sel="";
																																if($goalCRPAchieveInitial2_nurseID==$goalCRPAchieveInitial2) {
																																	$sel = "selected";
																																} else if($goalCRPAchieveInitial2=="" && $goalCRPAchieveInitial2_nurseID==$currentLoggedinNurseId) {
																																	$sel = "selected";
																																}else {
																																	$sel = "";
																																}
                                                                                                                
                                                                                                                ?>	
                                                                                                                       			<option value="<?php echo $goalCRPAchieveInitial2_nurseID;?>" <?php echo $sel;?>><?php echo $goalCRPAchieveInitial2_nurseName;?></option>
                                                                                                                <?php
                                                                                                                   			 }
                                                                                                                ?>
                                                                                                            </select>
                                                                                                         </td>
                                                                                                    </tr>
                                                                                                    
                                                                                                    
                                                                                               </tbody>
                                                                                         </table>
                                                                                  </div>
                                                                		 </div>
                                                            	</div>
                                                                
                                                                
                                                                
                                                                				
                                                          	  </div>
                                                            </div>
                                                        
                                                        <!--- New LAyout -->
                                                        
                                                        
                                                    </div>
                                               </div>
                                           </div>
                                           <!--   NEW  Discahrge SUmmary    -->
                                           <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                                <div class="panel panel-default bg_panel_anesth">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title rob"> Discharge Summary</h3>
                                                        <!--<div class="right_label top_yes_no color_jet">
                                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right">
                                                                <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  Yes  </label>
                                                                <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  No  </label>	                                                        		
                                                                <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label>
                                                            </div>
                                                        </div>-->
                                                    </div>
                                                    <?php
															$calculatorSetValue = $calculatorTopChangeGetValue+1300;
															
															$bprTempDrsDisBackColor=$chngBckGroundColor;
															if($dischargeSummaryTemp || $dischangeSummaryBp || $dischargeSummaryP || $dischangeSummaryRR || $dressing || $dischargeSummaryOther) { 
																$bprTempDrsDisBackColor=$whiteBckGroundColor; 
															}
													?>
                                                    <div class="panel-body">
                                                         <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                    <label class="f_size col-md-5 col-sm-5 col-xs-5 col-lg-5">
                                                                         <b> Temp </b> 
                                                                    </label>
                                                                    <div class="col-md-7 col-sm-7 col-xs-7 col-lg-7">
                                                                     	<input id="bp_temp" type="text" name="txt_dischargeSummaryTemp" onFocus="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');"  onKeyUp="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" value="<?php echo $dischargeSummaryTemp;?>" maxlength="8" size="8" class="form-control" style=" <?php echo $bprTempDrsDisBackColor;?> " onClick="getShowTemp(parseInt($(this).offset().top)-190,parseInt($(this).offset().left),'flag1',22);">
																		<input type="hidden" id="bp" name="bp_hidden">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                                    <div class="">
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                        	<div class="row">
                                                                            	    <label class="f_size  col-md-4 col-sm-5 col-xs-5 col-lg-5">
                                                                                         <b> BP </b> 
                                                                                    </label>
                                                                                    <div class="col-md-8 col-sm-7 col-xs-7 col-lg-7">
                                                                                        <input id="bp_temp2" type="text" name="txt_dischangeSummaryBp" onFocus="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" onKeyUp="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" value="<?php echo $dischangeSummaryBp;?>" maxlength="7" size="7" class="form-control" style="<?php echo $bprTempDrsDisBackColor;?> " onClick="getShowNewPos(parseInt($(this).offset().top)-190,parseInt($(this).offset().left),'flag2',22);" />
                                                                                    </div>	
                                                                            </div>	
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                              <div class="row">
                                                                            	    <label class="f_size col-md-4 col-sm-5 col-xs-5 col-lg-5">
                                                                                         <b> P </b> 
                                                                                    </label>
                                                                                    <div class="col-md-8 col-sm-7 col-xs-7 col-lg-7">
                                                                                        <input id="bp_temp3" type="text" name="txt_dischargeSummaryP" onFocus="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" onKeyUp="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" value="<?php echo $dischargeSummaryP;?>" size="1" maxlength="3" class="form-control" style=" <?php echo $bprTempDrsDisBackColor;?> " onClick="getShowNewPos(parseInt($(this).offset().top)-190,parseInt($(this).offset().left),'flag3',22);" />
                                                                                    </div>	
                                                                            </div>	
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                      	   <div class="row">
                                                                            	    <label class="f_size col-md-4 col-sm-5 col-xs-5 col-lg-5">
                                                                                         <b> RR </b> 
                                                                                    </label>
                                                                                    <div class="col-md-8 col-sm-7 col-xs-7 col-lg-7">
                                                                                        <input id="bp_temp4" type="text" name="txt_dischangeSummaryRR" onFocus="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" onKeyUp="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" value="<?php echo $dischangeSummaryRR;?>" size="1" maxlength="3" class="form-control" style=" <?php echo $bprTempDrsDisBackColor;?> " onClick="getShowNewPos(parseInt($(this).offset().top)-190,parseInt($(this).offset().left),'flag4',22);"/>
                                                                                    </div>	
                                                                            </div>	
                                                                        </div>
                                                                                                              
                                                                    </div> 
                                                                </div> <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                         <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                    <label class="f_size full_width padding_15">
                                                                        Dressing  
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                                   <div class="padding_15">
                                                                    <input type="text" id="txt_dressing_id" name="txt_dressing" onFocus="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" onKeyUp="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" class="form-control" style=" <?php echo $bprTempDrsDisBackColor;?> " tabindex="1" value="<?php echo $dressing;?>"  />                                                                
                                                                   </div>

                                                                </div> <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                         <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                    <label class="f_size full_width padding_15">
                                                                         Other  
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                                   <div class="padding_15">
                                                                    <input type="text" id="txt_dischargeSummaryOther_id" name="txt_dischargeSummaryOther" onFocus="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" onKeyUp="changeDiffChbxColor(6,'bp_temp','bp_temp2','bp_temp3','bp_temp4','txt_dressing_id','txt_dischargeSummaryOther_id');" class="form-control" style=" <?php echo $bprTempDrsDisBackColor;?> "   tabindex="1" value="<?php echo $dischargeSummaryOther;?>"  />                                                   
                                                                   </div>

                                                                </div> <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                         
                                                        	<?php
																$disTimeBackColor=$chngBckGroundColor;
																if($list_hour!='00') {
																	$disTimeBackColor=$whiteBckGroundColor;
																}
															?>
                                                                
                                                         <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">
                                                                    <label class="f_size full_width padding_15">
                                                                          Discharge At  
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-8 col-sm-8 col-xs-8 col-lg-8 ">
                                                                   <div class="full_width">
                                                                   		  <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4" style=" <?php echo $disTimeBackColor;?> ">	
                                                                            <select onChange="changeDiffChbxColor(1,'list_hour_id');changeSelectpickerColor('.select-mandatory');" name="list_hour" id="list_hour_id" class="selectpicker form-control select-mandatory" >
                                                                            	<option value="">HH</option>
																				<?php
																						for($i_HH=1;$i_HH<=$hhCnt;$i_HH++) 
																						{
																								if(strlen($i_HH) == 1) {
																										$i_HH	=	'0'.$i_HH;
																								}
																				?>
                                                                                				<option value="<?php echo $i_HH;?>" <?php if($list_hour==$i_HH) { echo "selected"; }?>><?php echo $i_HH;?></option>
                                                                             	<?php
																						}
																				?>
                                                                                
                                                                            </select>	
                                                                        </div>
                                                                         <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                        	<select name="list_min" id="list_min_id"  class="selectpicker form-control">
                                                                            	<option value="00">MM</option>
																			
																					<?php
                                                                                    for($i_MM=0;$i_MM<=59;$i_MM++) {
                                                                                        if(strlen($i_MM)==1) {
                                                                                            $i_MM='0'.$i_MM;
                                                                                        }
                                                                                    ?>
                                                                                        <option value="<?php echo $i_MM;?>" <?php if($list_min==$i_MM) { echo "selected"; }?>><?php echo $i_MM;?></option>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                            </select>	
                                                                        </div>
                                                                         <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4" style="display:<?php echo $dispAmPM;?>">	
                                                                        	<select name="list_ampm" class="selectpicker form-control" title="">
                                                                            	<option value="AM" <?php if($list_ampm=="AM") { echo "selected"; }?>>AM</option>
																				<option value="PM" <?php if($list_ampm=="PM") { echo "selected"; }?>>PM</option>
                                                                            </select>	
                                                                        </div>
                                                                   </div>

                                                                </div> <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                         
                                                       <div class="inner_safety_wrap wrap_right_inner_anesth" id="">
                                                        <div class="well">
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-5 col-xs-4 col-lg-4">
                                                                    <label class="date_r">
                                                                      	Comments
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-8 col-sm-7 col-xs-8 col-lg-8 text-center">
	                                                                <textarea id="txtarea_comments" style="resize:none;" class="form-control" name="txtarea_comments"  rows="2" tabindex="6"  ><?php echo stripslashes($comments);?></textarea>
                                                                </div> <!-- Col-3 ends  -->
                                                            </div>
                                                        </div> 
                                                     </div>
                                                         	
                                                    </div>
                                                </div>           
                                           </div>
                                           <div class="col-lg-6 col-sm-12 col-xs-12 col-md-6">
                                                <div class="panel panel-default bg_panel_anesth">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title rob">&nbsp; </h3>
                                                        <div class="right_label top_yes_no color_jet">
                                                            <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 pull-right">
                                                                <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  Yes  </label>
                                                                <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  No  </label>	                                                        		
                                                                <label class="col-md-4 col-sm-4 col-xs-4 col-lg-4">  &nbsp; </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="inner_safety_wrap">
                                                            <div class="row">
                                                                <div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">
                                                                    <label class="f_size">
                                                                     Alert
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                                    <div class="">
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                            <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_alrt_yes','chbx_alrt')"><span onClick="changeChbxColor('chbx_alrt')" class="colorChkBx" style=" <?php if($alert) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_alrt_yes" name="chbx_alrt" <?php if($alert=="Yes") { echo "checked"; }?>   tabindex="7" /></span> </a> 
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                               <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_alrt_no','chbx_alrt')"><span onClick="changeChbxColor('chbx_alrt')" class="colorChkBx" style=" <?php if($alert) { echo $whiteBckGroundColor;}?>" ><input class="field"  type="checkbox" value="No" id="chbx_alrt_no" name="chbx_alrt" <?php if($alert=="No") { echo "checked"; }?>  tabindex="7" /></span></a> 
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
                                                                    <label class="f_size">
                                                                        	Has taken nourishment
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                                    <div class="">
                                                                     
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                            <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_htn_yes','chbx_htn')"><span onClick="changeChbxColor('chbx_htn')" class="colorChkBx" style=" <?php if($hasTakenNourishment) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_htn_yes" name="chbx_htn" <?php if($hasTakenNourishment=="Yes") { echo "checked"; }?>  tabindex="7" ></span></a> 
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                               <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_htn_no','chbx_htn')"><span onClick="changeChbxColor('chbx_htn')" class="colorChkBx" style=" <?php if($hasTakenNourishment) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="No" id="chbx_htn_no" name="chbx_htn" <?php if($hasTakenNourishment=="No") { echo "checked"; }?>  tabindex="7" ></span></a> 
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
                                                                    <label class="f_size">
                                                                     	Nausea Vomiting
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                                    <div class="">
                                                                     
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                            <a href="javascript:void(0)"  onClick="javascript:checkSingle('chbx_nau_vom_yes','chbx_nau_vom')"><span onClick="changeChbxColor('chbx_nau_vom')" class="colorChkBx" style=" <?php if($nauseasVomiting) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" value="Yes" id="chbx_nau_vom_yes" name="chbx_nau_vom" <?php if($nauseasVomiting=="Yes") { echo "checked"; }?>  tabindex="7" ></span></a> 
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                               <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_nau_vom_no','chbx_nau_vom')"><span onClick="changeChbxColor('chbx_nau_vom')" class="colorChkBx" style=" <?php if($nauseasVomiting) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" name="chbx_nau_vom" <?php if($nauseasVomiting=="No") { echo "checked"; }?> value="No"  id="chbx_nau_vom_no"/></span></a> 
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
                                                                    <label class="f_size">
                                                                        	Voided Q.S
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                                    <div class="">
                                                                     
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                            <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_voi_qs_yes','chbx_voi_qs')"><span onClick="changeChbxColor('chbx_voi_qs')" class="colorChkBx" style=" <?php if($voidedQs) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" name="chbx_voi_qs" value="Yes" <?php if($voidedQs=="Yes") { echo "checked"; }?>  id="chbx_voi_qs_yes"/></span></a> 
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                               <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_voi_qs_no','chbx_voi_qs')"><span onClick="changeChbxColor('chbx_voi_qs')" class="colorChkBx" style=" <?php if($voidedQs) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" class="field" name="chbx_voi_qs" value="No" <?php if($voidedQs=="No") { echo "checked"; }?>  id="chbx_voi_qs_no"/></span></a> 
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
                                                                    <label class="f_size">
                                                                        Pain
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">
                                                                    <div class="">
                                                                     
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                            <a href="javascript:void(0)"  onClick="javascript:checkSingle('chbx_pan_yes','chbx_pan')"><span onClick="changeChbxColor('chbx_pan')" class="colorChkBx" style=" <?php if($panv) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" name="chbx_pan" value="Yes" <?php if($panv=="Yes") { echo "checked"; }?>  id="chbx_pan_yes"/></span></a> 
                                                                        </div>
                                                                        <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                               <a href="javascript:void(0)" onClick="javascript:checkSingle('chbx_pan_no','chbx_pan')"><span onClick="changeChbxColor('chbx_pan')"  class="colorChkBx" style=" <?php if($panv) { echo $whiteBckGroundColor;}?>" ><input type="checkbox" name="chbx_pan" value="No" <?php if($panv=="No") { echo "checked"; }?>  id="chbx_pan_no"/></span></a> 
                                                                        </div>
                                                                         <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">	
                                                                            &nbsp;
                                                                        </div>                                      
                                                                    </div> 
                                                                </div> <!-- Col-3 ends  -->
                                                            </div>	 
                                                         </div>
                                                    </div>
                                                </div>           
                                           </div>
                                           <!--NEW DISCHARGE SUMMARY -->
                                           <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12">
                                                 <div class="panel panel-default bg_panel_op">
                                                      <div class="panel-body">
                                                      	<div class="row">
                                                        <div class="col-md-6  col-sm-12 col-lg-3 col-xs-12">
                                                            <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                                <label class="col-md-12 col-lg-6 col-xs-12 col-sm-12 f_size" for=""> Who </label>
                                                               <div class="margin_small_only hidden-lg"></div>			
                                                                <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                                 <select class="selectpicker form-control" name="whoUserTypeList" onChange="checkNurseNotesUserType(this)"> 
                                                                     <option value="">Who</option>
																			<option value="Anesthesiologist" <?php if($whoUserType=='Anesthesiologist') { echo 'selected'; }?>>Anesthesia Provider</option>
																			<option value="Nurse" <?php if($whoUserType=='Nurse') { echo 'selected'; }?>>Nurse</option>
                                                                  </select>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6  col-sm-12 col-lg-3 col-xs-12 ">
                                                            <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                                <label class="col-md-12 col-lg-6 col-xs-12 col-sm-12 f_size" for=""> Created By </label>
                                                               <div class="margin_small_only hidden-lg"></div>	
                                                               <?php
																		if($whoUserType=='') { $blankDisplay = "block"; } else { $blankDisplay = "none"; }
																		if($whoUserType=='Anesthesiologist') { $AnesthesiologistDisplay = "block"; } else { $AnesthesiologistDisplay = "none"; }
																		if($whoUserType=='Nurse') { $NurseDisplay = "block"; } else { $NurseDisplay = "none"; }
																?>		
                                                                <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                                 <select class="selectpicker form-control" name="nurseNotesBlankId_list" id="nurseNotes_BlankId" style="display:<?php echo $blankDisplay;?>;<?php echo $chngBckGroundColor;?> " title="Created By"> 
                                                                     <option value="" selected>Created By</option>
                                                                  </select>
                                                                  
                                                                   <?php
																		$createdByBackColor=$chngBckGroundColor;
																		if($createdByUserId){ $createdByBackColor=$whiteBckGroundColor;}
																		//display:<?php echo $;
																?>
                                                                  
                                                               	<select onChange="changeDiffChbxColor(1,'nurseNotes_AnesthesiologistId');" name="nurseNotesAnesthesiologistId_list" class="form-control selectpicker"  id="nurseNotes_AnesthesiologistId" style=" <?php echo $createdByBackColor;?> ">
																			<option value="">Created By</option>
																			<?php 
																			$nurseNotesUserNameQry = "select * from users where user_type = 'Anesthesiologist' ORDER BY lname";
																			$nurseNotesUserNameRes = imw_query($nurseNotesUserNameQry) or die(imw_error());
																			$nurseNotesUserNumRow = imw_num_rows($nurseNotesUserNameRes);
																			if($nurseNotesUserNumRow) {
																				while($nurseNotesUserRow = imw_fetch_array($nurseNotesUserNameRes)) {
																					$nurseNotesUserId = $nurseNotesUserRow["usersId"];
																					$nurseNotesUserName = $nurseNotesUserRow["lname"].", ".$nurseNotesUserRow["fname"]." ".$nurseNotesUserRow["mname"];
																					if($nurseNotesUserRow["deleteStatus"]<>'Yes' || $createdByUserId==$nurseNotesUserId) {
																			?>
																						<option  value="<?php echo $nurseNotesUserId;?>" <?php if($createdByUserId==$nurseNotesUserId) { echo 'selected'; }?>><?php echo $nurseNotesUserName;?></option>
																			<?php
																					}
																				}
																			}
																			
																			?>
																		</select>
                                                                        
                                                                        <select onChange="changeDiffChbxColor(1,'nurseNotes_NurseId');" name="nurseNotesNurseId_list" id="nurseNotes_NurseId" class="form-control selectpicker" style=" <?php echo $createdByBackColor;?> ">
																			<option value="">Created By</option>
																			<?php 
																			$nurseNotesUserNameQry = "select * from users where user_type = 'Nurse' ORDER BY lname";
																			$nurseNotesUserNameRes = imw_query($nurseNotesUserNameQry) or die(imw_error());
																			$nurseNotesUserNumRow = imw_num_rows($nurseNotesUserNameRes);
																			if($nurseNotesUserNumRow) {
																				while($nurseNotesUserRow = imw_fetch_array($nurseNotesUserNameRes)) {
																					$nurseNotesUserId = $nurseNotesUserRow["usersId"];
																					$nurseNotesUserName = $nurseNotesUserRow["lname"].", ".$nurseNotesUserRow["fname"]." ".$nurseNotesUserRow["mname"];
																					if($nurseNotesUserRow["deleteStatus"]<>'Yes' || $createdByUserId==$nurseNotesUserId) {
																			?>
																						<option value="<?php echo $nurseNotesUserId;?>" <?php if($createdByUserId==$nurseNotesUserId) { echo 'selected'; }?>><?php echo $nurseNotesUserName;?></option>
																			<?php
																					}
																				}
																			}
																			
																			?>
																  </select>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                       <div class="margin_adjustment_only visible-md"></div>
                                                      
			
                                                     	<div class="col-md-6  col-sm-12 col-lg-3 col-xs-12 ">
                                                            <div class="inner_safety_wrap wrap_right_inner_anesth">
                                                                <label class="col-md-12 col-lg-6 col-xs-12 col-sm-12 f_size" for="r_nurse">Relief Nurse </label>
                                                               <div class="margin_small_only hidden-lg"></div>			
                                                                <div class="col-md-12 col-lg-6 col-xs-12 col-sm-12">
                                                                 <select class="selectpicker form-control" name="relivedNurseIdList" id="relivedNurseIdList" title ="Select"> 
                                                                     <option value="" selected>Select</option>	
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
                                                                 </div>
                                                            </div>
                                                        </div>
                                                        <?php
																$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
																$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
																$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
																
																$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
																$loggedInUserType = $ViewUserNameRow["user_type"];
																$loggedInSignatureOfNurse = $ViewUserNameRow["signature"];
																
																if($loggedInUserType<>"Nurse") {
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFun = "return noAuthorityFunCommon('Nurse');";
																}else {
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFun = "document.frm_gen_anes_nurse_notes.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','gen_anes_nurse_notes_ajaxSign.php','$loginUserId');";
																}
															
																$signOnFileStatus = "Yes";
																$TDnurseNameIdDisplay = "block";
																$TDnurseSignatureIdDisplay = "none";
																$NurseNameShow = $loggedInUserName;
																//$signNurseDateTimeFormatNew = date("m-d-Y h:i A");
																$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat(date("Y-m-d H:i:s"));
																if($signNurseId<>0 && $signNurseId<>"") {
																	$NurseNameShow = $signNurseName;
																	$signOnFileStatus = $signNurseStatus;	
																	$TDnurseNameIdDisplay = "none";
																	$TDnurseSignatureIdDisplay = "block";
																	$signNurseDateTimeFormatNew = $objManageData->getFullDtTmFormat($signNurseDateTime);
																}
																
																
																//CODE TO REMOVE NURSE SIGNATURE
																	if($_SESSION["loginUserId"]==$signNurseId) {
																		$callJavaFunDel = "document.frm_gen_anes_nurse_notes.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','gen_anes_nurse_notes_ajaxSign.php','$loginUserId','delSign');";
																	}else {
																		$callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
																	}	
																//END CODE TO REMOVE NURSE SIGNATURE															
															?>
                                                        <div class="col-md-6 col-sm-12 col-lg-3 col-xs-12">
                                                            <div class="inner_safety_wrap padding_15 wrap_right_inner_anesth">
                                         						<div class="inner_safety_wrap" id="TDnurseNameId" style="display:<?php echo $TDnurseNameIdDisplay;?>; " >
                                                                    <a  class="sign_link "  href="javascript:void(0);" style="cursor:pointer;<?php echo $signAnesthesiaIdBackColor?>;" onClick="javascript:<?php echo $callJavaFun;?>">Nurse Signature</a>
                                                                </div>
                                                                <div class="inner_safety_wrap " id="TDnurseSignatureId" style="display:<?php echo $TDnurseSignatureIdDisplay;?>; ">
                                                                     <span class="sign_link rob full_width" onClick="javascript:<?php echo $callJavaFunDel;?>">Nurse: <?php echo $NurseNameShow; ?>  </span>	     
                                                                     <span class="rob full_width"> <b> Electronically Signed </b> <?php echo $signOnFileStatus;  ?></span>
                                                                     <span class="rob full_width"> <b> Signature Date</b> <span class="dynamic_sig_dt" data-field-name="signNurseDateTime" data-table-name="<?=$tablename?>" data-id-value="<?=$pConfId?>" data-id-name="confirmation_id"> <?php echo $signNurseDateTimeFormatNew; ?> <span class="fa fa-edit"></span></span></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        </div>
														
                                                        
                                                        
                                                      </div>
                                                 </div>        
                                      </div>           
                                         	<!--- - -- --   Sign -Out Ends Here -->
                                  </div>
                                 </div>      

			

</form>
	<!-- WHEN CLICK ON CANCEL BUTTON -->
	<form name="frm_return_BlankMainForm" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="gen_anes_nurse_notes.php?cancelRecord=true<?php echo $saveLink;?>" target="_self">
	</form>
	<!-- END WHEN CLICK ON CANCEL BUTTON -->
</div>
<script type="text/javascript">
	top.frames[0].setPNotesHeight();
	
	$(function(){
			
			
			<?PHP if($blankDisplay == 'none') { ?>
					$("select#nurseNotes_BlankId").selectpicker("hide");
			<?PHP } ?>
			
			<?PHP if($AnesthesiologistDisplay == 'none') { ?>
					$("select#nurseNotes_AnesthesiologistId").selectpicker("hide");
			<?PHP } ?>
			
			<?PHP if($NurseDisplay == 'none') { ?>
					$("select#nurseNotes_NurseId").selectpicker("hide");
			<?PHP } ?>
			
	});
</script>
<?php
//CODE FOR FINALIZE FORM
	$finalizePageName = "gen_anes_nurse_notes.php";
	include('finalize_form.php');
//END CODE FOR FINALIZE FORM

if($finalizeStatus!='true'){
	?>
	<script type="text/javascript">
		top.frames[0].setPNotesHeight();
		top.frames[0].displayMainFooter();	
	</script>
	<?php
	include('privilege_buttons.php');
}else{
	?>
	<script type="text/javascript">
		top.frames[0].setPNotesHeight();		
		top.document.getElementById('footer_button_id').style.display = 'none';
	</script>
	<?php
}
if($SaveForm_alert == 'true'){
	?>
	<script type="text/javascript">
		document.getElementById('divSaveAlert').style.display = 'block';
	</script>
	<?php
}
include("print_page.php");
?><script src="js/vitalSignGrid.js" type="text/javascript" ></script>
</body>
</html>