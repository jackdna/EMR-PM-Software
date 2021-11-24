<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php

header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
include_once("../common/user_agent.php");
$objManageData = new manageData;
$user_id = $_REQUEST['user'];
$elem_frmAction = $_POST["elem_frmAction"];
$elem_usersId = $_POST["elem_usersId"];
$elem_mode = $_POST["elem_mode"];

$w = 450;
$h =  90;
$isHTML5OK = isHtml5OK();

//USER RECORD
if($user_id){
	$getUserDetails = $objManageData->getRowRecord('users', 'usersId', $user_id);
	$elem_usersId = $user_id;
	$elem_mode = 2;
	$userTitle = $getUserDetails->userTitle;
	$elem_fname = stripslashes($getUserDetails->fname);
	$elem_mname = stripslashes($getUserDetails->mname);
	$elem_lname = stripslashes($getUserDetails->lname);
	$elem_initial = stripslashes($getUserDetails->initial);
	$elem_type = $getUserDetails->user_type ;
	$elem_sub_type = $getUserDetails->user_sub_type ;
	$elem_coordinator_type = $getUserDetails->coordinator_type ;
	$elem_iolink_max_booking = $getUserDetails->iolink_max_booking ;
	$elem_user_session_timeout= $getUserDetails->session_timeout ;
	$elem_specialty_id_multi = stripslashes($getUserDetails->specialty_id_multi);
	$elem_address = stripslashes($getUserDetails->address);
	$elem_address2 = stripslashes($getUserDetails->address2);	
	$user_city = stripslashes($getUserDetails->user_city);
	$user_state = $getUserDetails->user_state;
	$user_zip = $getUserDetails->user_zip;	
	$elem_contactName = stripslashes($getUserDetails->contactName);	
	$elem_phone = $getUserDetails->phone;
	$elem_fax = $getUserDetails->fax;
	$elem_email = $getUserDetails->email;
	$elem_npi = $getUserDetails->npi;
	$elem_lic = $getUserDetails->lic;
	$elem_federalEin = $getUserDetails->federalEin;
	$elem_sso_identifier = $getUserDetails->sso_identifier;
	$elem_signature = $getUserDetails->signature;
	$elem_signature_path = $getUserDetails->signature_path;
	$elem_practiceName = stripslashes($getUserDetails->practiceName);
	
	$elem_privileges = $getUserDetails->user_privileges;
		$elemPrivilegesArr = explode(", ", $elem_privileges);
	$admin_privileges = $getUserDetails->admin_privileges;
	if($admin_privileges){
		$adminPrivilegesArr = explode(", ", $admin_privileges);
	}else{
		$adminPrivilegesArr = array();
	}
	$elem_loginName= $getUserDetails->loginName;
	$elem_password = $getUserDetails->user_password;	
	$locked	=$getUserDetails->locked;
	
	$hippaReviewedYes = $getUserDetails->hippaReviewedYes;
	$hippaReviewedNo = $getUserDetails->hippaReviewedNo;
	$hippaReviewedDateTime = $getUserDetails->hippaReviewedDateTime;
}else{
	$elemPrivilegesArr = array();
	$adminPrivilegesArr = array();
}

//USER RECORD
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>User Registration</title>
<?php include("adminLinkfile.php");?>
<style>
	form {margin:0px}	
	.sigdrw{border:1px solid orange;display:inline-block;}

</style>

<!--<script type="text/javascript" src="../js/jquery-1.9.0.min.js" ></script>-->
<script type="text/javascript" src="../js/simple_drawing.js"></script>
<script>

function closeMe(){
	top.frames[0].frames[0].document.getElementById('formTr').style.display = 'none';
}
//Applet
function get_App_Coords(objElem){
	var coords,appName;
	var objElemSign = document.frmUserRegistration.elem_signature;
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
	var coords = document.applets["app_signature"].getSign();
	return coords;
}
function getclear_os(){
	document.applets["app_signature"].clearIt();
	changeColorThis(255,0,0);
	document.applets["app_signature"].onmouseout();
}
function changeColorThis(r,g,b){				
	document.applets['app_signature'].setDrawColor(r,g,b);								
}
//Applet

function emptyForm(){
	top.frames[0].frames[0].location.href = 'userRegistration.php';
}	
function checkForm(objFrm){
	var msg="Please fill in the following:- \n";
	var flag = 0;
	var f1 = objFrm.elem_fname.value;	
	var f2 = objFrm.elem_lname.value;
	var f3 = objFrm.elem_type.value;
	//var f8 = objFrm.elem_privileges.value;
	var f9 = objFrm.elem_loginName.value;
	var f10 = objFrm.elem_password.value;
	var f11 = objFrm.elem_confirmPass.value;
	
	//var f12 = objFrm.elem_city.value;
	//var f13 = objFrm.elem_state.value;
	//var f14 = objFrm.elem_zip.value;
	if(f1==''){ msg = msg+"\t� First Name\n"; ++flag; }
	if(f2==''){ msg = msg+"\t� LastName\n"; ++flag; }
	if(f3==''){ msg = msg+"\t� Type\n"; ++flag; }
	//if(f12==''){ msg = msg+"\t� City\n"; ++flag; }
	//if(f13==''){ msg = msg+"\t� State\n"; ++flag; }
	//if(f14==''){ msg = msg+"\t� Zip\n"; ++flag; }
	//if(f8==''){ msg = msg+"\t� Privileges\n"; ++flag; }
	if(f9==''){ msg = msg+"\t� Login Name\n"; ++flag; }
	if(f10==''){ msg = msg+"\t� Password\n"; ++flag; }
	if(f11==''){ msg = msg+"\t� Confirm Password \n"; ++flag; }
	if(flag > 0){
		alert(msg);
		return false;	
	}
	document.frmUserRegistration.submit();
	return true;	
}
function checkPassowrd(objElem){
	var objFrm = objElem.form;
	if(objElem.value != ""){
		var objPass = objFrm.elem_password;
		if(objPass.value != objElem.value){
			alert("Please Confirm password again.");
			objElem.value = "";
		}
	}
}
function toggleAccLock(objElem){
	/*
	if(objElem.value=='Lock Account'){
		objElem.value = 'Unlock Account';
		document.getElementById('elem_lockAcc').value = 1;
	}else{
		objElem.value = 'Lock Account';
		document.getElementById('elem_lockAcc').value = 0;
	}*/
	if(objElem == 'Lock Account'){
		document.getElementById('elem_btnLockAcc').style.display = 'none';
		document.getElementById('elem_btnUnLockAcc').style.display = 'inline-block';
		document.getElementById('elem_lockAcc').value = 1;
	}else{
		document.getElementById('elem_btnLockAcc').style.display = 'inline-block';
		document.getElementById('elem_btnUnLockAcc').style.display = 'none';
		document.getElementById('elem_lockAcc').value = 0;
	}
}
function resetAcc(){
	document.frmUserRegistration.elem_password.value	= '';
	document.frmUserRegistration.elem_confirmPass.value = '';
	document.frmUserRegistration.elem_hiddResetAccount.value = 'yes';
	
}	
function moveDown(id){
	if(document.getElementById('iFrameUserRegistration').height == "575"){
		document.getElementById('iFrameUserRegistration').height = "325";
		document.getElementById('FrmRegisteration').style.display = "inline-block";
		document.getElementById('slideImg').src = '../images/slideDown.bmp';
	}else{
		document.getElementById('iFrameUserRegistration').height = "575";
		document.getElementById('FrmRegisteration').style.display = "none";
		document.getElementById('slideImg').src = '../images/slideUp.bmp';
	}
}
function isLetter(str) {
  return str.length === 1 && str.match(/[a-z]/i);
}
function countCharFn(obj, prevPass, id){
	var userFname = document.getElementById('elem_fname').value;
	var userLname = document.getElementById('elem_lname').value;
	var loginName = document.getElementById('elem_loginName').value;	
	var str = obj.value;
	var len = str.length;
	var letterExists ='';
	var specialCharExists ='';
	var numericExists ='';
	for(var i=0; i<len; i++){
		var strChar = str.charAt(i);
		if(isLetter(strChar)) {
			letterExists = "true";		
		}else if(isNaN(strChar)){
			specialCharExists = "true";
		}else{
			numericExists = "true";
		}
	}
	if((letterExists != "true") || (specialCharExists != "true") || (numericExists != "true")){
		alert("Must contain alphabet, numeric and special characters.")
		obj.value = '';
		return false;		
	}
	
	if((prevPass!=str) && (prevPass!='')){
		if(len<8){
			alert("Must be at least 8 characters long.")
			obj.value = '';
		}
	}else{
		if((len<8) && (!id)){
			alert("Must be at least 8 characters long.")
			obj.value = '';
		}
	}
	if((str==userFname) || (str==userLname) || (str==loginName)){
		alert("Password can not have user First Name or Last Name or user login id.")
		if(prevPass!=''){
			obj.value = '';
		}else{
			obj.value = '';
		}
	}		
}
function displayPriviliges(){
	var selectVal = document.getElementById("elem_privileges").value;
	if(selectVal=='Admin'){
		document.getElementById("adminPriviliges").style.display = 'inline-block';
		document.getElementById("adminPriviligesSpace").style.display = 'none';		
	}else{
		document.getElementById("adminPriviliges").style.display = 'none';
		document.getElementById("adminPriviligesSpace").style.display = 'inline-block';
	}
}
function changeColor(id){
	for(i=1; i<=10; i++){
		var obj = document.getElementById('tr'+i);
		if(obj){
			obj.style.background = "#FFFFFF";
		}
	}
	document.getElementById('tr'+id).style.background = '#FFFFCC';
	
	//CODE TO HIDE YELLOW COLOR
		document.getElementById('userNameId').style.background = '';
		document.getElementById('userRegisterAddressId').style.background = '';
		document.getElementById('userContactId').style.background = '';
	
	//END CODE TO HIDE YELLOW COLOR
}
//AJAX
function getCityStateFn(obj){
	var z = obj.value;
	if(z){
		var xmlHttp;
		try{		
			xmlHttp=new XMLHttpRequest();
		}
		catch (e){
			try{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e){
				try{
					xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e){
					alert("Your browser does not support AJAX!");
					return false;
				}
			}
		}
		xmlHttp.onreadystatechange=function(){
			if(xmlHttp.readyState==4){
				var val = xmlHttp.responseText;
				if(val!=''){
					var i = val.indexOf(",");
					var city = val.substr(0,i);
					var state = val.substr(i+1);
					document.frmUserRegistration.elem_city.value=city;
					document.frmUserRegistration.elem_state.value=state;
				}else{
					alert('Please enter correct zip code.')
					document.frmUserRegistration.elem_city.value='';
					document.frmUserRegistration.elem_state.value='';
					document.frmUserRegistration.elem_zip.value='';
				}
			}
		}
		xmlHttp.open("GET","getStateZip.php?zip="+z,true);
		xmlHttp.send(null);
	}
}
//AJAX


function changeBlockColor(obj) {
	for(i=1; i<=10; i++){
		var objTr = document.getElementById('tr'+i);
		if(objTr){
			objTr.style.background = "#FFFFFF";
		}
	}
	if(obj=='userNameId') {
		document.getElementById('userNameId').style.background = '#FFFFCC';
		document.getElementById('userRegisterAddressId').style.background = '';
		document.getElementById('userContactId').style.background = '';
	} else if(obj=='userRegisterAddressId') {
		document.getElementById('userNameId').style.background = '';
		document.getElementById('userRegisterAddressId').style.background = '#FFFFCC';
		document.getElementById('userContactId').style.background = '';
	} else if(obj=='userContactId') {
		document.getElementById('userNameId').style.background = '';
		document.getElementById('userRegisterAddressId').style.background = '';
		document.getElementById('userContactId').style.background = '#FFFFCC';
	}	
} 
function chkAllAdmin(obj){
	for(i=1;i<=9;i++){
		if(obj.checked == true){
			//document.getElementById('tdSubAdmin').style.display = 'inline-block';
			document.getElementById('tdSubAdmin').style.visibility = 'visible';
			document.getElementById('admin'+i).checked = true;			
		}else{
			document.getElementById('admin'+i).checked = false;
			//document.getElementById('tdSubAdmin').style.display = 'none';
			document.getElementById('tdSubAdmin').style.visibility = 'hidden';
		}
	}
}
function preSelectPrivillige(objValue) {
	//PrivilligeId
	//alert(objValue);
	if(objValue=='Nurse') {
		document.getElementById('nursePrivilligeId').checked = true;
		document.getElementById('anesthesiaPrivilligeId').checked = false;
		document.getElementById('surgeonPrivilligeId').checked = false;
		document.getElementById('staffPrivilligeId').checked = false;
		document.getElementById('coordinatorPrivilligeId').checked = false;
		//START CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
			document.getElementById('practiceNameTxtID').style.display='inline-block';
			//document.getElementById('practiceNameDropDownID').style.display='none';
			document.getElementById('hidd_practiceNameId').value='practiceTxt';
			if(document.getElementById('elem_coordinator_type')) {
				document.getElementById('elem_coordinator_type').disabled=true;
			}
			if(document.getElementById('elem_iolink_max_booking')) {
				document.getElementById('elem_iolink_max_booking').disabled=true;
			}
			if(document.getElementById('elem_specialty_id_multi')) {
				document.getElementById('elem_specialty_id_multi').disabled=true;
				
			}
		//END CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
	}else if(objValue=='Anesthesiologist' || objValue=='Anesthesiologist-CRNA') { //Certified Registered Nurse Anesthetist (CRNA)
		document.getElementById('nursePrivilligeId').checked = false;
		document.getElementById('anesthesiaPrivilligeId').checked = true;
		document.getElementById('surgeonPrivilligeId').checked = false;
		document.getElementById('staffPrivilligeId').checked = false;
		document.getElementById('coordinatorPrivilligeId').checked = false;
		//START CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
			document.getElementById('practiceNameTxtID').style.display='inline-block';
			//document.getElementById('practiceNameDropDownID').style.display='none';
			document.getElementById('hidd_practiceNameId').value='practiceTxt';
			if(document.getElementById('elem_coordinator_type')) {
				document.getElementById('elem_coordinator_type').disabled=true;
			}
			if(document.getElementById('elem_iolink_max_booking')) {
				document.getElementById('elem_iolink_max_booking').disabled=true;
			}
			if(document.getElementById('elem_specialty_id_multi')) {
				document.getElementById('elem_specialty_id_multi').disabled=true;
			}
		//END CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
	}else if(objValue == 'Surgeon') {
		document.getElementById('nursePrivilligeId').checked = false;
		document.getElementById('anesthesiaPrivilligeId').checked = false;
		document.getElementById('surgeonPrivilligeId').checked = true;
		document.getElementById('staffPrivilligeId').checked = false;
		document.getElementById('coordinatorPrivilligeId').checked = false;
		
		//START CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
			document.getElementById('practiceNameTxtID').style.display='inline-block';
			//document.getElementById('practiceNameDropDownID').style.display='none';
			document.getElementById('hidd_practiceNameId').value='practiceTxt';
			if(document.getElementById('elem_coordinator_type')) {
				document.getElementById('elem_coordinator_type').disabled=true;
			}
			if(document.getElementById('elem_iolink_max_booking')) {
				document.getElementById('elem_iolink_max_booking').disabled=false;
			}
			if(document.getElementById('elem_specialty_id_multi')) {
				document.getElementById('elem_specialty_id_multi').disabled=false;
			}
		//END CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
	}else if(objValue == 'Staff') {
		document.getElementById('nursePrivilligeId').checked = false;
		document.getElementById('anesthesiaPrivilligeId').checked = false;
		document.getElementById('surgeonPrivilligeId').checked = false;
		document.getElementById('staffPrivilligeId').checked = true;
		document.getElementById('coordinatorPrivilligeId').checked = false;

		//START CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
			document.getElementById('practiceNameTxtID').style.display='inline-block';
			//document.getElementById('practiceNameDropDownID').style.display='none';
			document.getElementById('hidd_practiceNameId').value='practiceTxt';
			if(document.getElementById('elem_coordinator_type')) {
				document.getElementById('elem_coordinator_type').disabled=true;
			}
			if(document.getElementById('elem_iolink_max_booking')) {
				document.getElementById('elem_iolink_max_booking').disabled=true;
			}
			if(document.getElementById('elem_specialty_id_multi')) {
				document.getElementById('elem_specialty_id_multi').disabled=true;
			}
		//END CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
	}else if(objValue=='Coordinator') {
		document.getElementById('nursePrivilligeId').checked = false;
		document.getElementById('anesthesiaPrivilligeId').checked = false;
		document.getElementById('surgeonPrivilligeId').checked = false;
		document.getElementById('staffPrivilligeId').checked = false;
		document.getElementById('coordinatorPrivilligeId').checked = true;
		
		//START CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
			//document.getElementById('practiceNameTxtID').style.display='none';
			//document.getElementById('practiceNameDropDownID').style.display='inline-block';
			//document.getElementById('hidd_practiceNameId').value='practiceDropDown';
			if(document.getElementById('elem_coordinator_type')) {
				document.getElementById('elem_coordinator_type').disabled = false;
			}
			if(document.getElementById('elem_iolink_max_booking')) {
				document.getElementById('elem_iolink_max_booking').disabled=true;
			}
			if(document.getElementById('elem_specialty_id_multi')) {
				document.getElementById('elem_specialty_id_multi').disabled=true;
			}
			$("#elem_coordinator_type").selectpicker('refresh');
		//END CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
	}else {
		//START CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
			document.getElementById('practiceNameTxtID').style.display='inline-block';
			//document.getElementById('practiceNameDropDownID').style.display='none';
			document.getElementById('hidd_practiceNameId').value='practiceTxt';
			if(document.getElementById('elem_coordinator_type')) {
				document.getElementById('elem_coordinator_type').disabled=true;
			}
			if(document.getElementById('elem_iolink_max_booking')) {
				document.getElementById('elem_iolink_max_booking').disabled=true;
			}
			if(document.getElementById('elem_specialty_id_multi')) {
				document.getElementById('elem_specialty_id_multi').disabled=true;
			}		
		//END CODE TO DISPLAY TEXTBOX OR DROPDOWN OF PRACTICE ACCORDING USERTYPE
	}
	
	if(document.getElementById('elem_lic')) {
		document.getElementById('elem_lic').style.backgroundColor='#CCC';
		document.getElementById('elem_lic').disabled=true;
		if(objValue=='Surgeon' || objValue=='Anesthesiologist' || objValue=='Anesthesiologist-CRNA') {
			document.getElementById('elem_lic').style.backgroundColor='#FFF';
			document.getElementById('elem_lic').disabled=false;	
			
		}
	}
	$("select#elem_specialty_id_multi").selectpicker('render');
	//$('button[data-id="elem_specialty_id_multi"]').removoClass('disabled');
	
}
var isHTML5OK="<?php echo $isHTML5OK;?>";
$(document).ready(function () {
	
	
	if(isHTML5OK=="1"){	
	
		$(".wrap_inside_admin").scroll(function(e) {
            
			$("canvas").each(function()
			{
				var $this=	$(this);
				var T	=	$this.offset().top
				var L	=	$this.offset().left;
				
				var SD	=	$("#sig_data"+this.id+"").val()
				var SM	=	$("#sig_img"+this.id+"").val()
				if( SD === '' || SM !== '')
				{
					$this.attr('data-top-pos',T);
					$this.attr('data-left-pos',L); 
				
					oSimpDrw[this.id]['left_pos']	=	L;
					oSimpDrw[this.id]['top_pos']	=	T;
				}
				
				
			});
			
        });
		
		$("canvas").each(function() { 
			
			var $this=	$(this);
			var T	=	$this.offset().top + $(".head_scheduler").outerHeight();
			var L	=	$this.offset().left;
			var W	=	$this.parent().width();
			
			$this.attr('data-top-pos',T);
			$this.attr('data-left-pos',L); 
			$this.attr('width',W); 	
				
			oSimpDrw[this.id]	=	new SimpleDrawing(this.id);
			oSimpDrw[this.id].init();
			
			
		});
		
	}
});
function getClear(ap){	
	if(typeof(isHTML5OK)!="undefined" && isHTML5OK==1){
		if(oSimpDrw && oSimpDrw[ap]){oSimpDrw[ap].clearCanvas();	}		
	}
}
var isHTML5OK="<?php echo $isHTML5OK;?>";
</script>


<script>

var LD	=	function()
{
		var T	=	parent.$("#userFrame").height() - $(".head_scheduler").outerHeight(true);
		$('#data-body').css( {'overflow':'hidden', 'overflow-y':'auto', 'min-height': T+'px', 'max-height': T+'px'} );
		//console.log('User Registration Form Height :' T );
}
$(window).load(function(){ LD(); });
$(window).resize(function(e) { LD(); });

</script>
</head>
<body onLoad="MM_preloadImages('../images/unlock-_account_hover.gif','../images/lock_account_hover.gif','../images/reset_account_hover.gif','../images/save_hover1.jpg')">
<form name="frmUserRegistration" action="saveForm.php" class="wufoo topLabel alignCenter" method="post" target="iFrameUserRegistration" onSubmit="return checkForm(this)">

<input type="hidden" name="frmName" id="frmName" value="User Registration">
<input type="hidden" name="elem_usersId" id="elem_usersId" value="<?php  echo $elem_usersId;?>">
<input type="hidden" name="elem_mode" id="elem_mode" value="<?php echo !empty($elem_mode) ? $elem_mode : "1" ; ?>">
<input type="hidden" name="elem_SwitchMode" id="elem_SwitchMode" value="">
<input type="hidden" name="hiddSigIpadIdAdminPhy" id="hiddSigIpadIdAdminPhy" value="">
<input type="hidden" name="elem_hiddResetAccount" id="elem_hiddResetAccount" value="">
<?php
	
	$licEnableDisable	=	($elem_type == "Surgeon" || $elem_type == "Anesthesiologist" || $elem_type == "CRNA")	?	''		:	'disabled';
	
	$licBgColor 		=	($elem_type == "Surgeon" || $elem_type == "Anesthesiologist" || $elem_type == "CRNA")	?	"#FFF"	:	'#CCC';
	
	$disableMaxBooking	=	($elem_type !='Surgeon' )	?	'disabled'	:	''	;

	$disableSpeciality	=	($elem_type !='Surgeon' )	?	'disabled'	:	''	;
	
	$elem_iolink_max_booking=	(!$elem_iolink_max_booking) ?	''	:	$elem_iolink_max_booking;
	
	$elem_user_session_timeout	=	empty($elem_user_session_timeout) ? (60*30) : $elem_user_session_timeout ;
	$specialty_id_saved_arr=	array();
	 
	// Practice Name Option List 	 
	$practiceNameTxtDisplay			=	($elem_type == "Coordinator")	?	'none'				:	'inline-block'	;
	$practiceNameDropDownDisplay	=	($elem_type == "Coordinator")	?	'inline-block'		:	'none';
	$practiceNameShowID 			=	($elem_type == "Coordinator")	?	'practiceDropDown'	:	'practiceTxt'; 
	//GET IT HIDDEN VALUE TO CHECK IF IT IS DROPDOWN OR TEXTBOX
	
	$arrPracticeName =	array();
	
	$qryPracticeName =	"SELECT practice_id,name,del_status FROM practice_name ORDER BY name";
	$resPracticeName =	imw_query($qryPracticeName)or die(imw_error());

	while( $rowPracticeName = imw_fetch_assoc($resPracticeName))
	{
		$practice_id	 =	$rowPracticeName['practice_id'];
		$practice_name	 =	$rowPracticeName['name'];
		$practice_del	 =	$rowPracticeName['del_status'];
		
		$practice_name_saved_arr	=	explode(",",$elem_practiceName);
		
		$sel			=	(in_array($practice_id,$practice_name_saved_arr))	?	'SELECTED'	:	''	;
		if($practice_del!="yes" || in_array($practice_id,$practice_name_saved_arr))
		{
			$pracOptions .='<option value="'.$practice_id.'" '.$sel.'>'.$practice_name.'</option>';
		}
		
	} 
	
	
	// Specialities Option List 
	$qrySpecialtyName	=	"SELECT specialty_id,specialty_name,del_status FROM specialty ORDER BY specialty_name";
	$resSpecialtyName	=	imw_query($qrySpecialtyName)or die(imw_error());
	 
	while( $rowSpecialtyName = imw_fetch_assoc($resSpecialtyName))
	{
	 		$specialty_id	 = $rowSpecialtyName['specialty_id'];
			$specialty_name	 = $rowSpecialtyName['specialty_name'];
            $specialty_del	 = $rowSpecialtyName['del_status'];
            $specialty_id_saved_arr	=	explode(",",$elem_specialty_id_multi);
			
			$sel			=	(in_array($specialty_id,$specialty_id_saved_arr))	?	'selected'	:	'';
			
			if($specialty_del!="yes" || in_array($specialty_id,$specialty_id_saved_arr)){
				$specialtyOptions .='<option value="'.$specialty_id.'" '.$sel.'>'.$specialty_name.'</option>';
			}
	}
	
?>
							
							
<div class="padding_0 clear ">	         
		
		
		<div class="head_scheduler new_head_slider padding_head_adjust_admin">
			
			<span>Registration Form</span>
        
        </div><!-- Page Heading -->
		
		<div class="wrap_inside_admin" id="data-body">
		
				<div class="form_outer">
                	
                    
                					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
						
							<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="name"> Name </label>
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
								
								
								<div class="col-md-2 col-lg-2 col-sm-6 col-xs-6 padding_0">
									<select class="selectpicker form-control " id="userTitleList" name="userTitleList" data-header="Title" title='Title' >
										<option value="Dr."		<?php if($userTitle == 'Dr.'  ) { echo "selected"; }?> > Dr. </option>
										<option value="Mr."		<?php if($userTitle == 'Mr.'  ) { echo "selected"; }?> > Miss </option>
										<option value="Mrs."	<?php if($userTitle == 'Mrs.' ) { echo "selected"; }?> > Mr. </option>
										<option value="Miss."	<?php if($userTitle == 'Miss.') { echo "selected"; }?> > Mrs. </option>
								  	</select>
									<small> Title </small>
								</div>
								
								<div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 paddingLR_6">
									<input type="text"  name="elem_fname" id="elem_fname"  class="form-control"  value="<?php echo $elem_fname; ?>" />
									<small>First Name</small>
								</div>
								
								<div class="clearfix visible-sm"></div>
								
              	<div class="col-md-2 col-lg-2 col-sm-4 col-xs-12 paddingLR_6">
									<input type="text" name="elem_mname" id="elem_mname" class="form-control" value="<?php echo $elem_mname; ?>" />
									<small>Middle Name</small>
								</div>
								
                
								<div class="col-md-3 col-lg-3 col-sm-4 col-xs-12 paddingLR_6">
									<input type="text" name="elem_lname" id="elem_lname" class="form-control" value="<?php echo $elem_lname; ?>" />
									<small>Last Name</small>
								</div>
                <div class="col-md-2 col-lg-2 col-sm-4 col-xs-12 paddingLR_6">
									<input type="text" name="elem_initial" id="elem_initial" class="form-control" value="<?php echo $elem_initial; ?>" />
									<small>Initial</small>
								</div>
								
														   
							</div><!-- Column 1 End -->
							
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 padding_0">
									
								<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" name="elem_npi" id="elem_npi" value="<?php echo $elem_npi; ?>" />
									<small>NPI# </small>
								</div>
								
								
								<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" name="elem_lic" id="elem_lic" value="<?php echo $elem_lic; ?>" <?php echo $licEnableDisable;?> style="background-color:<?php echo $licBgColor;?>" />
									<small>Lic# </small>
								</div>
								
							</div><!-- Column 2 End -->
							
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 padding_0">
									
								<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                                    <input type="text" name="elem_federalEin" value="<?php echo $elem_federalEin; ?>" class="form-control" />
                                    <small>Federal EIN#</small>
								</div>
								
								
								<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" name="elem_sso_identifier" id="elem_sso_identifier" value="<?php echo $elem_sso_identifier; ?>" />
									<small>SSO ID</small>
								</div>
								
							</div>
                             
							
						
					
					</div> <!-- End Row -->
					
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
							<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="address"> Address</label>
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
								<input type="text" class="form-control" name="elem_address" value="<?php echo $elem_address; ?>">
								<small>Address Line 1</small>	    		
							</div><!-- Column 1 End -->
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
								<input type="text" class="form-control" name="elem_address2" value="<?php echo $elem_address2; ?>" onFocus="return changeBlockColor('userRegisterAddressId');" />
								<small>Address Line 2</small>
							</div><!-- Column 2 End -->
							
								
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 padding_0">
								
								<div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
									<input type="text" class="form-control" name="elem_city" value="<?php echo $user_city; ?>" onFocus="return changeBlockColor('userRegisterAddressId');" />
									<small>City</small>
								</div>
								
								<div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_0">
									<input type="text" class="form-control" name="elem_state" value="<?php echo $user_state; ?>"  onFocus="return changeBlockColor('userRegisterAddressId');" />
									<small>State</small>
								</div>
								
								<div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
									<input type="text" class="form-control" name="elem_zip" value="<?php echo $user_zip; ?>" onFocus="return changeBlockColor('userRegisterAddressId');" />
									<small>Zip</small> 	
								</div>
							
							</div><!-- Column 3 End --> 
							
						
					
					</div> <!-- End Row -->
					
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
							<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="contact"> Contact </label>
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 padding_0">
							
								<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 ">
									<input type="text" class="form-control" name="elem_contactName" value="<?php echo $elem_contactName; ?>" onFocus="return changeBlockColor('userContactId');" />
									<small>Name</small>
								</div>
								
								<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 ">
									<input type="text" class="form-control" name="elem_phone" value="<?php echo $elem_phone; ?>" maxlength="12" onBlur="ValidatePhone(this);" onFocus="return changeBlockColor('userContactId');" />
									<small>Phone</small>
								</div>
							
							</div><!-- Column 1 End -->
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
								<input type="text" class="form-control" id="fax" name="elem_fax" value="<?php echo $elem_fax; ?>" maxlength="12" onBlur="ValidatePhone(this);" onFocus="return changeBlockColor('userContactId');"	/>
								<small>Fax</small>
							</div><!-- Column 2 End -->
							
								
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
								<input type="text" class="form-control"  name="elem_email" value="<?php echo $elem_email; ?>" onFocus="return changeBlockColor('userContactId');"/>
								<small>Email</small>
							</div><!-- Column 3 End --> 
							
						
					
					</div> <!-- End Row -->
					
					<div class="clearfix margin_adjustment_only"></div>
					<div class="clearfix border-dashed margin_adjustment_only"></div>
					
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 padding_0">
							
								<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="usertype"> User Type </label>
								
								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-6">
							
									<select name="elem_type" class="selectpicker form-control" onChange="javascript:preSelectPrivillige(this.value)" data-header="User Type" title='User Type'>
										
										<option value="Nurse" <?php echo ($elem_type == "Nurse") ? "selected" : "" ?>>Nurse</option>
										<option value="Anesthesiologist" <?php echo ($elem_type == "Anesthesiologist" && !$elem_sub_type) ? "selected" : "" ?>>Anesthesiologist</option>
										<option value="Surgeon" <?php echo ($elem_type == "Surgeon") ? "selected" : "" ?>>Surgeon</option>
										<option value="Staff" <?php echo ($elem_type == "Staff") ? "selected" : "" ?>>Staff</option>
										<option value="Scrub Technician" <?php echo ($elem_type == "Scrub Technician") ? "selected" : "" ?>>Scrub Technician</option>
										<option value="Coordinator" <?php echo ($elem_type == "Coordinator") ? "selected" : "" ?>>Surgical Coordinator</option>
										<option value="Anesthesiologist-CRNA" <?php echo ($elem_type == "Anesthesiologist" && $elem_sub_type=='CRNA') ? "selected" : "" ?>>CRNA</option>
									</select>
									<small>User Type</small>
								</div>
								
								<div class="col-md-6 col-lg-6 col-xs-12 col-sm-6">
									
									<select name="elem_coordinator_type" id="elem_coordinator_type" class="selectpicker form-control" <?php echo ($elem_type == "Coordinator") ? '' : 'disabled';?>  onFocus="return changeColor('1');" >
										<option value="" selected>Select Coord. Type</option>
										<option value="Practice" <?php echo (!$elem_coordinator_type || $elem_coordinator_type == "Practice") ? "selected" : "" ?>>Practice</option>
										<option value="Master" <?php echo ($elem_coordinator_type == "Master") ? "selected" : "" ?>>ASC</option>
									</select>
									<small>Coord. Type</small>
								</div>
								
							</div><!-- Column 1 End -->
							
							<div class="col-md-8 col-lg-8 col-xs-12 col-sm-8 ">
								
								<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="Practice"> Practice Name</label>
								<select multiple="multiple"  name="elem_practiceName[]" id="elem_practiceName" class=" selectpicker form-control" data-header="Practice Name" title='Practice Name' >
									<?php echo $pracOptions; ?>
								</select>
								<input type="hidden" name="hidd_practiceNameId" id="hidd_practiceNameId" value="<?php echo $practiceNameShowID;?>">
								
								<span id="practiceNameTxtID" style="background-color:#F1F4F0;padding-left:0px;margin:0px 0 0 0px; white-space:nowrap;"></span>
								
								<div id="pop_up1" class="div_popup" style=" display:none; width:260px; top:30px; left:50%; right:50%; position:absolute;">
									
									<div class="text_10b" style="background-color:#c0aa1e; height:20px;">
										<span class="closeBtnAdmin" onClick="close_popup('pop_up1');" ></span>Practice Name
									</div>
									
									<select name="elem_practiceName1[]" id="elem_practiceName1" multiple size="15" class="selectpicker form-control" data-header="Practice Name" title='Practice Name'  >
                                		<?php echo $pracOptions; ?>
                            		</select>
									
									<div class="alignCenter" style="background-color:#c0aa1e; height:25px;">
										
                                        <a href="javascript:void(0);" class="btn btn-group btn-success" id="selectPracButton" onClick="selected('pop_up1','elem_practiceName');"/>
                                        	<b class="fa fa-save"></b>&nbsp;Save
                                        </a>
                                        
                                        <a href="javascript:void(0);" class="btn btn-group btn-info" id="closePracButton" onClick="close_popup('pop_up1');"/>
                                        	<b class="fa fa-close"></b>&nbsp;Close
                                        </a>
										
									</div>
									
								</div>
									
							
								
								
								
							</div><!-- Merged Column 2 & 3 End -->
							
								
					</div> <!-- End Row -->
					
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
						<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 padding_0">
								
                                <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                		<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0" for="usertype"> Max Booking </label>
                                        <input type="text" <?php echo $disableMaxBooking;?> name="elem_iolink_max_booking" id="elem_iolink_max_booking" value="<?php echo $elem_iolink_max_booking;?>" class="form-control" onFocus="return changeColor('2');" />
                              	</div>
                                
                                <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                		<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0" for="usertype"> Session Timeout </label>
                                        <select class="form-control selectpicker" name="elem_user_session_timeout" id="elem_user_session_timeout">
                               			<option value="60" <?=($elem_user_session_timeout == 60) ? 'selected': ''?>>1 Minute</option>
                                        <option value="120" <?=($elem_user_session_timeout == 120) ? 'selected': ''?>>2 Minutes</option>
                                        <option value="300" <?=($elem_user_session_timeout == 300) ? 'selected': ''?>>5 Minutes</option>
                                        <option value="600" <?=($elem_user_session_timeout == 600) ? 'selected': ''?>>10 Minutes</option>
                                        <option value="900" <?=($elem_user_session_timeout == 900) ? 'selected': ''?>>15 Minutes</option>
                                        <option value="1800" <?=($elem_user_session_timeout == 1800) ? 'selected': ''?>>30 Minutes</option>
                                        <option value="3600" <?=($elem_user_session_timeout == 3600) ? 'selected': ''?>>1 Hour</option>
                                        <option value="7200" <?=($elem_user_session_timeout == 7200) ? 'selected': ''?>>2 Hours</option>
                                        <option value="10800" <?=($elem_user_session_timeout == 10800) ? 'selected': ''?>>3 Hours</option>
                                        <option value="14400" <?=($elem_user_session_timeout == 14400) ? 'selected': ''?>>4 Hours</option>
                                        <option value="18000" <?=($elem_user_session_timeout == 18000) ? 'selected': ''?>>5 Hours</option>
                                        <option value="21600" <?=($elem_user_session_timeout == 21600) ? 'selected': ''?>>6 Hours</option>
                                        
                                        </select>
                              	</div>
                                
                                          
							
						</div><!-- Column 1 End -->
						
						<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
							<label class="text-left col-lg-4 col-md-4 col-sm-4 col-xs-4 padding_0" for="usertype"> Speciality </label>
							<select multiple="multiple" <?php echo $disableSpeciality;?>  name="elem_specialty_id_multi[]" id="elem_specialty_id_multi" class="selectpicker form-control" data-header="Speciality" title='Speciality' >
								<?php echo $specialtyOptions; ?>
							</select>
						</div><!-- Column 2 End --> 
							
						<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 text-left">
							
							<?PHP
								if($hippaReviewedYes=='Yes')
								{
									echo '<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0" for="hippaReviewedYes">HIPAA Review</label>';
									echo '<input '.($hippaReviewedYes =='Yes' ? "checked" : '').' disabled  type="checkbox" id="hippaReviewedYes" name="hippaReviewedYes" />';
									if($hippaReviewedDateTime!='0000-00-00 00:00:00')
									{
										//date('m-d-Y g:i:s A');
										$hippaReviewedDateNew=date("m-d-Y",strtotime($hippaReviewedDateTime));
										$hippaReviewedTimeNew=date("g:i:s",strtotime($hippaReviewedDateTime));
										echo '&nbsp;On '.$hippaReviewedDateNew.' At '.$hippaReviewedTimeNew;
									}
									
								}
								else
								{
									echo '<label class="text-left col-lg-4 col-md-4 col-sm-4 col-xs-4 padding_0" for="hippaReviewedYes"><br />';
									echo '<input disabled type="checkbox" id="hippaReviewedYes" name="hippaReviewedYes" />&nbsp;HIPAA Review';
									echo '</label>';
								}
							?>
							
								
                            </label>	
							
						</div><!-- Column 3 End --> 
						
						
					
					</div> <!-- End Row -->
					
					<div class="clearfix margin_adjustment_only"></div>
					<div class="clearfix border-dashed margin_adjustment_only"></div>
					
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
							<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="privileges"> Privileges </label>
							
							<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
								<div class="transit_priveldge">
								
									<ul>
										
										<li>
											<label>
												<input id="superPrivilligeId" name="userPrivileges[]" value="Super User" type="checkbox" <?php if(in_array("Super User", $elemPrivilegesArr)) echo "checked"; ?> />
												Super User 
											</label>
										</li>
										
										<li>
											<label>
												<input id="admin_checked" <?php if(in_array("Admin", $elemPrivilegesArr)) echo "checked"; ?> name="userPrivileges[]" value="Admin" type="checkbox" onClick="return chkAllAdmin(this);" />
												Admin
											</label>
										</li>
										<li class="clearfix visible-sm"></li>
										<li class="clearfix visible-xs"></li>
										<li id="tdSubAdmin" style="visibility:<?php if(in_array("Admin", $elemPrivilegesArr)) echo "visible"; else echo "hidden"; ?>; "><ul>
										<li >
											<label><input id="admin1" <?php if(in_array("User", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="User" type="checkbox" />User </label>
										</li>
										<li >
											<label><input id="admin2" <?php if(in_array("Pre-Op Med", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Pre-Op Med" type="checkbox" />Pre-Op Med.</label>
										</li>
										<li >
											<label><input id="admin3" <?php if(in_array("Surgeon profile", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Surgeon profile" type="checkbox" />Surgeon profile</label>
										</li>
										<li >
											<label><input id="admin4" <?php if(in_array("Predefines", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Predefines" type="checkbox" />Predefines </label>
										</li>
										<li >
											<label><input id="admin5" <?php if(in_array("Operative Reports", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Operative Reports" type="checkbox" />Operative Reports </label>
										</li>
										<li >
											<label><input id="admin6" <?php if(in_array("Instruction Sheet", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Instruction Sheet" type="checkbox" onFocus="return changeColor('10');">Inst. Sheet </label>
										</li>
										<li >
											<label><input id="admin7" <?php if(in_array("Reports", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Reports" type="checkbox" />Report </label>
										</li>
										<li >
											<label><input id="admin8" <?php if(in_array("Audit", $adminPrivilegesArr)) echo "checked"; ?> name="adminOther[]" value="Audit" type="checkbox" />Audit </label>
										</li>
										<li >
											<label><input id="admin9" <?php if(in_array("EMR", $adminPrivilegesArr)) echo "CHECKED"; ?> name="adminOther[]" value="EMR" type="checkbox" />EMR </label>
										</li>
										</ul></li>
										<li class="clearfix"></li>
										
										
										<li>
											<label>
												<input id="staffPrivilligeId" name="userPrivileges[]" <?php if(in_array("Staff", $elemPrivilegesArr)) echo "checked"; ?> value="Staff" type="checkbox" />
												Staff
											</label>
										</li>
										
										<li>
											<label>
												<input id="nursePrivilligeId" name="userPrivileges[]" <?php if(in_array("Nursing Record", $elemPrivilegesArr)) echo "checked"; ?> value="Nursing Record" type="checkbox" />
												Nurse
											</label>
										</li>	
										<li>
											<label>
												<input id="anesthesiaPrivilligeId" name="userPrivileges[]" <?php if(in_array("Anesthesia", $elemPrivilegesArr)) echo "checked"; ?> value="Anesthesia" type="checkbox" />
												Anesthesia 
											</label>
										</li>	
										<li>
											<label>
												<input id="surgeonPrivilligeId"  name="userPrivileges[]" <?php if(in_array("Surgeon", $elemPrivilegesArr)) echo "checked"; ?> value="Surgeon" type="checkbox" />
												Surgeon 
											</label>
										</li>	
										<li>
											<label>
												<input id="auditPrivilligeId"  name="userPrivileges[]" <?php if(in_array("Audit", $elemPrivilegesArr)) echo "checked"; ?> value="Audit" type="checkbox" />
												Audit 
											</label>
										</li>
										<li>
											<label>
												<input id="billingPrivilligeId" name="userPrivileges[]" <?php if(in_array("Billing", $elemPrivilegesArr)) echo "CHECKED"; ?> value="Billing" type="checkbox" />
												Billing 
											</label>
										</li>	  
										<li>
											<label>
												<input id="reportPrivilligeId" name="userPrivileges[]" <?php if(in_array("Report", $elemPrivilegesArr)) echo "CHECKED"; ?> value="Report" type="checkbox" />
												Report 
											</label>
										</li>
										<li>
											<label>
												<input id="coordinatorPrivilligeId" name="userPrivileges[]" <?php if(in_array("Coordinator", $elemPrivilegesArr)) echo "CHECKED"; ?> value="Coordinator" type="checkbox" />  
												Surgical Coordinator
											</label>
										</li>	   
									</ul>
                            	
									<ul style="display:<?php echo ($elem_privileges!='Admin' ? 'none' : 'block'); ?>" id="adminPriviliges" >
										<li>
											<label><input type="checkbox" <?php if($priviligeUser==1) echo 'checked'; ?> name="priviligeUser" value="1" />&nbsp;User</label>
										</li>
										<li>
											<label><input type="checkbox" <?php if($priviligePreMedication==1) echo 'checked'; ?> name="priviligePreMedication" value="1" />&nbsp;Pre-Op Med Orders</label>
										</li>
										<li>
											<label><input type="checkbox" <?php if($priviligePredefines==1) echo 'checked'; ?> name="priviligePredefines" value="1" />&nbsp;Predefines</label>
										</li>
										<li>
											<label><input type="checkbox" <?php if($privilegeDischargeSummary==1) echo 'checked'; ?> name="privilegeDischargeSummary" value="1" />&nbsp;Discharge Summary</label>
										</li>	
									</ul>
									
								</div>
                                                  
							</div><!-- Column 1 & 2 & 3 End --> 
							
						
					
					</div> <!-- End Row -->
					
					
					<div class="clearfix margin_adjustment_only"></div>
					<div class="clearfix border-dashed margin_adjustment_only"></div>
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
								<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 " for="elem_loginName"> Login Name </label>
								<input type="text" name="elem_loginName" id="elem_loginName" value="<?php echo $elem_loginName; ?>" class="form-control" onFocus="return changeColor('5');" />
							</div><!-- Column 1 End -->
							
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
								<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 " for="elem_password"> Password </label>
								<input type="password" autocomplete="off" name="elem_password" id="elem_password" value="<?php echo $elem_password; ?>" class="form-control" onBlur="return countCharFn(this, '<?php echo $elem_password; ?>', '<?php echo $elem_usersId; ?>')" onFocus="return changeColor('5');">
							</div><!-- Column 2 End -->
							
								
							<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
								<label class="text-left col-lg-12 col-md-12 col-sm-12 col-xs-12 padding_0 " for="elem_confirmPass"> Confirm Password </label>
								<input type="password"  name="elem_confirmPass" id="elem_confirmPass" value="<?php echo $elem_password; ?>" onChange="checkPassowrd(this)" class="form-control" onFocus="return changeColor('5');" />
							</div><!-- Column 3 End --> 
							
					
					</div><!-- End Row -->
					
					<div class="clearfix margin_adjustment_only"></div>
					<div class="clearfix border-dashed margin_adjustment_only"></div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						
						<div class="col-md-6 col-lg-6 col-xs-12 col-sm-6 padding_0" >
								
							<label class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 " for="signature"> Signature </label>
								
							<div class="col-md-2 col-lg-2 col-xs-12 col-sm-2">&nbsp;</div>
							<div class="col-md-8 col-lg-8 col-xs-10 col-sm-8" id="signature" >
								
								<?php
								
									if($isHTML5OK)
									{ 
										$showLaserImgPath			=	'';
										$elem_signature_path_encode	=	'';
										
										if(!$surgeryCenterWebrootDirectoryName) { $surgeryCenterWebrootDirectoryName=$surgeryCenterDirectoryName;	}
										
										if(!empty($elem_signature_path) && file_exists($elem_signature_path))
										{
											$showLaserImgPath			=	"/".$surgeryCenterWebrootDirectoryName."/admin/".$elem_signature_path;
											$elem_signature_path_encode =	base64_encode($showLaserImgPath);	
										}	
								?>
                                		
    									<span class=" col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0" style="height:100px;" >
											
                                            <canvas id="sign" style="border:dashed 1px #333; " height="100" ></canvas>
                                            <input type="hidden" name="sig_datasign"  id="sig_datasign" value="<?php echo $elem_signature_path_encode;?>"/>
                                            <input type="hidden" name="sig_imgsign"  id="sig_imgsign" value="<?php echo $showLaserImgPath;?>" />
                                        </span>
								<?php 
									}
									else
									{
								?>
										<ul style="list-style:none;">
											<li>
                                                   
                                                    <span class="text_10 col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0" style="background-color:#F1F4F0; ">
                                                        <input type="hidden" name="elem_signature" value="<?php echo $elem_signature; ?>">
                                                        <object type="application/x-java-applet" name="app_signature" id="app_signature" class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onMouseOut="get_App_Coords(this)">
                                                          <param name="code" value="MyCanvasColored.class" />
                                                          <param name="codebase" value="../common/applet/" />
                                                          <param name="bgImage" value="../images/white.jpg" />
                                                          <param name="strpixls" value="<?php echo $elem_signature;?>" />
                                                          <param name="mode" value="edit" />
                                                          <param name="archive" value="DrawApplet.jar" />
                                                        </object>
                                                    </span>
                                                </li>
                                            </ul>
									<?php
                                    }?>          
									
							</div>
							<div class="col-md-2 col-lg-2 col-xs-2 col-sm-2" style="max-height:100px; min-height:100px;">
								<img src="../images/eraser.gif" onClick="return getClear('sign');" style="position:absolute; bottom:0; left:0;">
							</div>
						</div><!-- Column 1 End -->
							
						<div class="col-md-6 col-lg-6 col-xs-12 col-sm-6 visible-lg padding_0">
							<div class="side_btns_lg text-center">
								  <div class="btn-footer-slider">
									
									<input type="hidden" id="elem_lockAcc" name="elem_lockAcc" value="<?php echo $locked; ?>">
									
									<?PHP
										
										$lockStyle	=	($locked == 1) ?	'none' 			:	'inline-block'	;
										$unlockStyle=	($locked == 1) ?	'inline-block'	:	'none'			;
									
									?>
									
									<a href="javascript:void(0)" class="btn btn-default" onClick="javascript:toggleAccLock('Unlock Account');" style="display:<?=$unlockStyle?>" id="elem_btnUnLockAcc">
										<b class="fa fa-unlock"></b> Unlock Account 
									</a>
									
									<a href="javascript:void(0)" class="btn btn-default" onClick="javascript:toggleAccLock('Lock Account');" style="display:<?=$lockStyle?>" id="elem_btnLockAcc">
										<b class="fa fa-lock"></b> Lock Account 
									</a>
									
									<a href="javascript:void(0)" class="btn btn-default" onClick="javascript:resetAcc();" style="display:inline-table" id="elem_btnResetAcc">
									   <b class="fa fa-refresh "></b> Reset Account
									</a>
								</div> 
								
						</div><!-- Column 2  End -->
							
								
					</div> <!-- End Row -->
					
					
					
					
			
				</div> <!-- Form Outer -->
					
		</div><!-- Wrap Inside Admin -->	

</div> <!-- End Container Padding 0 Div -->

</form>
</body>
</html>
