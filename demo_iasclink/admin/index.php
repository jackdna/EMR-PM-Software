<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['iolink_loginUserId']; 


unset($userPrivilegesArr);
unset($admin_privilegesArr);
$authenticationDetails = $objManageData->getRowRecord('users', 'usersId', $loginUser);
$userPrivileges = $authenticationDetails->user_privileges;
$admin_privileges = $authenticationDetails->admin_privileges;
$user_type = $authenticationDetails->user_type;

$userPrivilegesArr = explode(', ', $userPrivileges);
if($admin_privileges){
	$admin_privilegesArr = explode(', ', $admin_privileges);
}else{
	$admin_privilegesArr = array();
}


/*
$userPrivileges = $_SESSION['iolink_userPrivileges'];
	$userPrivileges = explode(", ", $userPrivileges);
$admin_privileges = $_SESSION['iolink_admin_privileges'];
	if($admin_privileges){
		$admin_privileges = explode(', ', $admin_privileges);
	}    
*/
?>
<html>
<head>
<title>Admin Surgery Center</title>
<LINK HREF="adminStyle.css" TYPE="text/css" REL="stylesheet">
<link rel="stylesheet" href="../css/form.css" type="text/css" />
<link rel="stylesheet" href="../css/theme.css" type="text/css" />
<link rel="stylesheet" href="../css/sfdc_header.css" type="text/css" />
<link rel="stylesheet" href="../css/simpletree.css" type="text/css" />
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
</style>
<script>
function LTrim( value ) 
{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
}
// Removes ending whitespaces
function RTrim( value ) 
{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
}
// Removes leading and ending whitespaces
function trim( value ) 
{
	return LTrim(RTrim(value));
}

function frameSrc(source, id){
	document.iframeMain.location.href = source;	
	document.getElementById('saveButton').style.display = 'block';	
	if(source=='surgeryCenter.php'){
		document.getElementById('saveButton').style.display = 'block';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'block';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
		
	}
	if(source=='userRegistration.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'block';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source=='preOpMediOrder.php'){
		document.getElementById('saveButton').style.display = 'block';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'block';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'block';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'surgeonprofile.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'predefineManager.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'operativeReportSurgeon.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'operativeReport.php'){
		document.getElementById('saveButton').style.display = 'block';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'instructionSheet.php'){
		document.getElementById('saveButton').style.display = 'block';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'consent_form_select.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'consentFormMultiple.php'){
		document.getElementById('saveButton').style.display = 'block';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'block';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'anesthesia_profile.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source == 'laser_procedure_admin.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'none';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
	}
	
	if(source == 'consent_category.php'){
		document.getElementById('saveButton').style.display = 'none';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'none';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}
	if(source=='nurseQuestion.php'){
		document.getElementById('saveButton').style.display = 'block';
		document.getElementById('deleteSelected').style.display = 'block';
		document.getElementById('newUser').style.display = 'none';
		document.getElementById('addNew').style.display = 'block';
		document.getElementById('cancelButton').style.display = 'none';
		document.getElementById('medicationButton').style.display = 'none';
		document.getElementById('categoryButton').style.display = 'block';
		document.getElementById('backButton').style.display = 'none';
		document.getElementById('formsButton').style.display = 'none';
		document.getElementById('optionsButton').style.display = 'none';
	}	
	for(var i=1; i<=12; i++){
		if(document.getElementById('Tab'+i)){
			document.getElementById('Tab'+i).style.background = "#BCD2B0";
			document.getElementById('Tab'+i+'Link').className = "black";
			document.getElementById('img'+i+'Left').src ="../images/left.gif";
			document.getElementById('img'+i+'Right').src ="../images/right.gif";
		}
	}
	document.getElementById("Tab"+id).style.background = "#003300";
	document.getElementById("Tab"+id+"Link").className = "white";
	document.getElementById('img'+id+'Left').src ="../images/leftDark.gif";
	document.getElementById('img'+id+'Right').src ="../images/rightDark.gif";
	document.frameSrc.source.value = source;
}
function chkFrameSourse(){
	if(top.frames[0].frames[0].document.getElementById('labelTr')){
		top.frames[0].frames[0].document.getElementById('labelTr').style.display = 'block';
	}
}
function getPageSrc(toDo){	
	var frameSrc = document.frameSrc.source.value;
	//alert(frameSrc)	;
	if(frameSrc=='surgeryCenter.php'){
	<!-- SURGERY CENTER -->
	if(toDo=='Save'){
			objFrm = top.frames[0].frames[0].document.frmSurgeryCenter;
			var flag = 0;
			var msg = "Please Insert thr following fields.\n";
			var f1 = objFrm.sergeryCenerName.value;
			var f2 = objFrm.sergeryCenerAddress.value;
			var f3 = objFrm.sergeryCenerPhone.value;
			var f4 = objFrm.sergeryCenerFax.value;
			var f5 = objFrm.sergeryCenerEmail.value;
			//var f6 = objFrm.sergeryCenerNPI.value;
			//var f7 = objFrm.sergeryCenerFederal.value;
			var f8 = objFrm.elem_maxRecentlyUsedPass.value;
			var f9 = objFrm.elem_maxLoginAttempts.value;
			var f10 = objFrm.elem_maxPassExpiresDays.value;
			var f12 = objFrm.elem_finalizeDays.value;
			var f13 = objFrm.elem_finalizeWarningDays.value;
			var f14 = objFrm.elem_city.value;
			var f15 = objFrm.elem_state.value;
			var f16 = objFrm.elem_zip.value;
			if(f1==''){ var msg = msg+"\t� Surgery Center Name.\n"; ++flag; }
			if(f2==''){ var msg = msg+"\t� Surgery Center Address.\n"; ++flag; }
			if(f14==''){ var msg = msg+"\t� City.\n"; ++flag; }
			if(f15==''){ var msg = msg+"\t� State.\n"; ++flag; }
			if(f16==''){ var msg = msg+"\t� Zip.\n"; ++flag; }
			if(f3==''){ var msg = msg+"\t� Surgery Center Phone.\n"; ++flag; }
			if(f4==''){ var msg = msg+"\t� Surgery Center Fax.\n"; ++flag; }
			//if(f6==''){ var msg = msg+"\t� Surgery Center NPI#.\n"; ++flag; }
			//if(f7==''){ var msg = msg+"\t� Surgery Center Federal#.\n"; ++flag; }
			if(f8==''){ var msg = msg+"\t� Max. Recently Used Password.\n"; ++flag; }
			if(f9==''){ var msg = msg+"\t� Max. Login Attempts.\n"; ++flag; }
			if(f10==''){ var msg = msg+"\t� Max. Password Expires Days.\n"; ++flag; }	
			if(f12==''){ var msg = msg+"\t� Finalize Days.\n"; ++flag; }
			if(f13==''){ var msg = msg+"\t� Finalize Warning Days.\n"; ++flag; }
			if(flag>0){
				alert(msg)
				return false;
			}
			top.frames[0].frames[0].document.frmSurgeryCenter.submit();
			
		} else if(toDo=='Cancel'){	
			var objFrame = top.document.getElementById("iframeHome");
			objFrame.src = "admin/index.php";
		}
	<!-- SURGERY CENTER -->		
	
	}else if(frameSrc=='userRegistration.php'){	
	<!-- USER REGISTERATION -->
		if(toDo=='New'){
			top.frames[0].frames[0].document.getElementById('formTr').style.display = 'block';
			//alert(top.frames[0].frames[0].frames[0].document.forms[0].name);
			document.getElementById('newUser').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'block';
			document.getElementById('cancelButton').style.display = 'block';
			
			var innerObj = top.frames[0].frames[0].frames[0];
			innerObj.document.getElementById('elem_btnResetAcc').style.display = 'none';
			if(innerObj.document.getElementById('elem_btnUnLockAcc')) {
				innerObj.document.getElementById('elem_btnUnLockAcc').style.display = 'none';
			}
			if(innerObj.document.getElementById('elem_btnLockAcc')) {
				innerObj.document.getElementById('elem_btnLockAcc').style.display = 'none';
			}
			objFrm = top.frames[0].frames[0].frames[0].document.forms[0];
			//alert(objFrm.elem_mode.value);
			
			if(objFrm.elem_mode.value==2) {
				if(confirm("Form is modified. Do you want to save the changes.")) {
					objFrm.elem_SwitchMode.value = "NewUser";
					getPageSrc('Save');
					document.getElementById('newUser').style.display = 'none';
					document.getElementById('deleteSelected').style.display = 'none';
					document.getElementById('saveButton').style.display = 'block';
					document.getElementById('cancelButton').style.display = 'block';
			
				}
			}
			
			objFrm.elem_usersId.value = '';
			objFrm.elem_mode.value = 1;
			objFrm.userTitleList.value = '';
			objFrm.elem_fname.value = '';
			objFrm.elem_mname.value = '';	
			objFrm.elem_lname.value = '';
			objFrm.elem_address.value = '';
			objFrm.elem_address2.value = '';
			objFrm.elem_city.value = '';
			objFrm.elem_state.value = '';
			objFrm.elem_zip.value = '';
			objFrm.elem_type.value = '';
			objFrm.elem_npi.value = '';
			objFrm.elem_practiceName.value = '';
			objFrm.elem_contactName.value = '';
			objFrm.elem_phone.value = '';
			objFrm.elem_fax.value = '';
			objFrm.elem_email.value = '';
			objFrm.elem_federalEin.value = '';
			//objFrm.elem_privileges.value = '';
			objFrm.elem_loginName.value = '';			
			objFrm.elem_password.value = '';
			objFrm.elem_confirmPass.value = '';
			objFrm.priviligeUser.checked = false;
			objFrm.priviligePreMedication.checked = false;
			objFrm.priviligePredefines.checked = false;
			objFrm.privilegeDischargeSummary.checked = false;
			top.frames[0].frames[0].frames[0].document.applets['app_signature'].clearIt();
			top.frames[0].frames[0].frames[0].document.getElementById('adminPriviliges'). style.display = 'none';
		}else if(toDo=='Delete'){
			var frameObj = top.frames[0].frames[0].frames[1];
			var boxesChk = false;
			obj = frameObj.document.getElementsByName("chkBox[]");
			objLength = frameObj.document.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select User(s) to be deleted.")
			}else{
				var ask = confirm('Are you sure to delete the user record(s).')
				if(ask==true){
					frameObj.document.listUsersFrm.submit();
				}
			}			
		}else if(toDo=='Save'){
			var objFrm = top.frames[0].frames[0].frames[0].document.frmUserRegistration;

			var msg="Please fill in the following:- \n";
			var flag = 0;
			var f1 = objFrm.elem_fname.value;	
			var f2 = objFrm.elem_lname.value;
			var f3 = objFrm.elem_type.value;
			//var f4 = objFrm.elem_practiceName.value;
			//var f5 = objFrm.elem_address.value;
			//var f6 = objFrm.elem_federalEin.value;
			//var f7 = objFrm.elem_npi.value;
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
			//if(f4==''){ msg = msg+"\t� Practice Name\n"; ++flag; }
			//if(f5==''){ msg = msg+"\t� Practice Address\n"; ++flag; }
			//if(f12==''){ msg = msg+"\t� City\n"; ++flag; }
			//if(f13==''){ msg = msg+"\t� State\n"; ++flag; }
			//if(f14==''){ msg = msg+"\t� Zip\n"; ++flag; }
			//if(f6==''){ msg = msg+"\t� Federal #\n"; ++flag; }
			//if(f7==''){ msg = msg+"\t� NPI #\n"; ++flag; }
			if(f8==''){ msg = msg+"\t� Privileges\n"; ++flag; }
			if(f9==''){ msg = msg+"\t� Login Name\n"; ++flag; }
			if(f10==''){ msg = msg+"\t� Password\n"; ++flag; }
			if(f11==''){ msg = msg+"\t� Confirm Password \n"; ++flag; }
			if(flag > 0){
				alert(msg);
				return false;	
			}
			objFrm.submit();
			document.getElementById('newUser').style.display = 'block';		
			document.getElementById('deleteSelected').style.display = 'block';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('cancelButton').style.display = 'none';
			return true;			
		
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('newUser').style.display = 'block';
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'block';
		}
		
		
	<!-- USER REGISTERATION -->
	}else if(frameSrc=='preOpMediOrder.php'){
	<!-- PRE OP MEDICATION ORDER -->
		if(toDo=='Add New'){
			top.frames[0].frames[0].document.getElementById('newEnteryTr').style.display = 'block';
			document.getElementById('cancelButton').style.display = 'block';
		}
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.preOpMediOrderListFrm;
			
			formObj.sbtForm.value = 'true';
			document.getElementById('cancelButton').style.display = 'none';
			formObj.submit();
			
		}else if(toDo=='Delete'){
			
			var docObj = top.frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete selected records.')
				if(ask==true){
					docObj.preOpMediOrderListFrm.delRecords.value = 'true';
					docObj.preOpMediOrderListFrm.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
		} else if(toDo=='Category'){	
			//frameSrc('preOpMediOrderCategory.php?contentOf=Medications Category', 3);
			frameSrc = "preOpMediOrderCategory.php?contentOf=Medications Category";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('categoryButton').style.display = 'none';
			document.getElementById('medicationButton').style.display = 'block';
			
		}
	<!-- PRE OP MEDICATION ORDER  -->
	
	}else if(frameSrc=='preOpMediOrderCategory.php?contentOf=Medications Category'){
		
	<!-- PRE OP MEDICATION ORDER CATEGORY WHEN CLICK ON 'MANAGE CATEGORY BUTTON'  -->
		
		var docObj = top.frames[0].frames[0].document;
		if(docObj.frmPreOpMediOrderCategory){
			var id = docObj.frmPreOpMediOrderCategory.contentOf.value;
		}else{
			var id = docObj.perDefineProceduresFrm.contentOf.value;
		}
		if(toDo=='Save'){			
			if(id == 'Procedures'){
				docObj.perDefineProceduresFrm.submit();
			}else{
				docObj.frmPreOpMediOrderCategory.submit();
			}
			
		}else if(toDo=='Delete'){
			document.getElementById('cancelButton').style.display = 'none';
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the selected record(s)')
				if(ask==true){
					if(id == 'Procedures'){
						docObj.perDefineProceduresFrm.deleteSelected.value = 'true';
						docObj.perDefineProceduresFrm.submit();			
					}else{
						docObj.frmPreOpMediOrderCategory.sbmtFrm.value = '';
						docObj.frmPreOpMediOrderCategory.deleteSelected.value = 'true';
						docObj.frmPreOpMediOrderCategory.submit();
					}
				}
			}
		}else if(toDo=='Add New'){
			document.getElementById('cancelButton').style.display = 'block';
			if(top.frames[0].frames[0].location){
				if(id == 'Procedures'){
					top.frames[0].frames[0].frames[1].document.getElementById('procedureTr').style.display = 'block';
				}else{
					top.frames[0].frames[0].document.getElementById('mediCategoryTr').style.display = 'block';
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			
		} else if(toDo=='Medication'){	
			//frameSrc('preOpMediOrderCategory.php?contentOf=Medications Category', 3);
			frameSrc = "preOpMediOrder.php";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('categoryButton').style.display = 'block';
			document.getElementById('medicationButton').style.display = 'none';
			
		}
	<!-- PRE OP MEDICATION ORDER CATEGORY WHEN CLICK ON 'MANAGE CATEGORY BUTTON'  -->
	
	}else if(frameSrc=='surgeonprofile.php'){
	<!-- SURGEON PROFILE -->
		var docObj = top.frames[0].frames[0].frames[0].document;
		if(toDo=='Save'){
			var msg = 'Please fill following fields.\n';
			var flag = 0;	
			f1 = docObj.frmSurgeonProfile.elem_profileName.value;
			//f2 = docObj.frmSurgeonProfile.elem_operativeReportTemplate.value;
			//f3 = docObj.frmSurgeonProfile.elem_instructions.value;	
			//f4 = docObj.getElementById('elem_proceduresId').value;
			//f5 = docObj.getElementById('elem_preOpOrders_id').value;
			if(f1==''){  ++flag;  msg+='� Profile Name.\n'; }
			//if(f2==''){  ++flag;  msg+='� Operative Report Template.\n'; }
			//if(f3==''){  ++flag;  msg+='� Operative Instructions Template.\n'; }
			//if(f4==''){  ++flag;  msg+='� Procedures.\n'; }
			//if(f5==''){  ++flag;  msg+='� Pre Operative Medication Order.\n'; }
			
			if(flag>0){
				alert(msg)
				return false;
			}else{
				//docObj.frmSurgeonProfile.sbtSaveProfile.value = 'true';
				top.frames[0].frames[0].frames[0].frames[0].document.forms[0].submit();
				//docObj.frmSurgeonProfile.submit();
				//docObj.getElementById('surgeonLabelId').style.display = 'none';
				//docObj.getElementById('surgeonsList_id').style.display = 'none';
				//docObj.getElementById('selectedSurgeonNameId').style.display = 'block';

			}
		}else if(toDo=='Delete'){
			//alert(top.frames[0].frames[0].frames[0].name);
			
			var ask = confirm('Are you sure to delete the profile.')
			if(ask==true)	{
				//docObj.frmSurgeonProfile.sbtDelProfile.value = 'true';
				//docObj.frmSurgeonProfile.submit();
				docObj.frmSurgeonDeleteProfile.sbtDelProfile.value = 'true';
				docObj.frmSurgeonDeleteProfile.submit();
				
			}
			
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			
		} else if(toDo=='Back'){	
			
			var surgeonId = top.frames[0].frames[0].frames[0].document.forms[0].surgeonId.value;
			var profileId = top.frames[0].frames[0].frames[0].document.forms[0].profileId.value;
			top.frames[0].frames[0].frames[0].location.href = 'addSurgeonProcedure.php?surgeonId='+surgeonId+'&surgeonsList='+surgeonId+'&profileId='+profileId;
			
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('backButton').style.display = 'none';
		
		}
	<!-- SURGEON PROFILE -->
	}else if(frameSrc=='addSurgeonProfile.php'){
	<!-- SAVE SURGEON PROFILE -->
		var docObj = top.frames[0].frames[0].frames[0].document;
		
		if(toDo=='Save'){
			
			top.frames[0].frames[0].frames[0].frames[0].document.frm_surgeonProfileSpeadsheet.submit();
			//docObj.forms[0].submit();
		} else if(toDo=='Cancel'){	
			frameSrc = "surgeonprofile.php";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('backButton').style.display = 'none';
			
		}else if(toDo=='Delete'){
			
			var ask = confirm('Are you sure to delete the profile.')
			if(ask==true)	{
				docObj.frmSurgeonDeleteProfile.sbtDelProfile.value = 'true';
				docObj.frmSurgeonDeleteProfile.submit();
			}
		} else if(toDo=='Back'){	
			
			var surgeonId = top.frames[0].frames[0].frames[0].document.forms[0].surgeonId.value;
			var profileId = top.frames[0].frames[0].frames[0].document.forms[0].profileId.value;
			if(profileId) {
				top.frames[0].frames[0].frames[0].location.href = 'addSurgeonProcedure.php?surgeonId='+surgeonId+'&surgeonsList='+surgeonId+'&profileId='+profileId;
			} else {
				top.frames[0].frames[0].frames[0].document.forms[2].submit();		
			}
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('backButton').style.display = 'none';
		}
	<!-- SAVE SURGEON PROFILE -->	
	}else if(frameSrc=='predefineManager.php'){
	<!-- PREDEFINE MANAGER -->
		var docObj = top.frames[0].frames[0].frames[1].document;
		if(docObj.preDefineForm){
			var id = docObj.preDefineForm.contentOf.value;
		}else if(docObj.perDefineProceduresFrm){
			var id = docObj.perDefineProceduresFrm.contentOf.value;
		}else if(docObj.perDefineDiagnosisFrm){
			var id = docObj.perDefineDiagnosisFrm.contentOf.value;
		}
		if(toDo=='Save'){			
			if(id == 'Procedures'){
				docObj.perDefineProceduresFrm.submit();
			}else if(id == 'Diagnosis'){
				docObj.perDefineDiagnosisFrm.submit();
			}else{
				docObj.preDefineForm.submit();
			}
		}else if(toDo=='Delete'){
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the selected record(s)')
				if(ask==true){
					if(id == 'Procedures'){
						docObj.perDefineProceduresFrm.deleteSelected.value = 'true';
						docObj.perDefineProceduresFrm.submit();			
					}else if(id == 'Diagnosis'){
						docObj.perDefineDiagnosisFrm.deleteSelected.value = 'true';
						docObj.perDefineDiagnosisFrm.submit();			
					}else{
						docObj.preDefineForm.sbmtFrm.value = '';
						docObj.preDefineForm.deleteSelected.value = 'true';
						docObj.preDefineForm.submit();
					}
				}
			}
		}else if(toDo=='Add New'){
			if(top.frames[0].frames[0].frames[1].location){
				if(id == 'Procedures'){
					top.frames[0].frames[0].frames[1].document.getElementById('procedureTr').style.display = 'block';
				}else if(id == 'Diagnosis'){
					
					top.frames[0].frames[0].frames[1].document.getElementById('diagnosisTr').style.display = 'block';
				}else{
					top.frames[0].frames[0].frames[1].document.getElementById('predefimeTr').style.display = 'block';
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('addNew').style.display = 'none';
			document.getElementById('cancelButton').style.display = 'none';
			
		}
	<!-- PREDEFINE MANAGER -->
	}else if(frameSrc=='operativeReport.php'){
	<!-- OPERATIVE TEMPLATE -->
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.frmOperativeReport;
			if(formObj.template_name.value==''){
				alert('Please enter template name.')
			}else{
				formObj.sbtTemplate.value = 'true';
				formObj.submit();
				document.getElementById('cancelButton').style.display = 'none';
			}
		}else if(toDo=='Delete'){
			var docObj = top.frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the template')
				if(ask==true){
					docObj.frmOperativeReport.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		} else if(toDo=='Cancel'){	
			//document.iframeMain.location.href = frameSrc;
			document.iframeMain.location.href = 'operativeReportSurgeon.php';
			
			document.getElementById('cancelButton').style.display = 'none';
			
		}
	<!-- OPERATIVE TEMPLATE -->
	}else if(frameSrc=='instructionSheet.php'){
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.frmInstruction;
			if(formObj.instruction_name.value==''){
				alert('Please enter template name.')
			}else{
				formObj.sbtInstruction.value = 'true';
				formObj.submit();
				document.getElementById('cancelButton').style.display = 'none';
			}
		}else if(toDo=='Delete'){
			var docObj = top.frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the template')
				if(ask==true){
					docObj.frmInstruction.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			
		}
	}else if(frameSrc=='consentForm.php'){
	<!-- CONSENT FORM TEMPLATE -->
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.frmConsentForm;
			/*
			if(formObj.template_name.value==''){
				alert('Please enter template name.')
			}else{
			*/
				formObj.sbtConsent.value = 'true';
				formObj.submit();
			//}
		
		} else if(toDo=='Cancel'){	
			var objFrame = top.document.getElementById("iframeHome");
			objFrame.src = "admin/index.php";
		}
		/*
		else if(toDo=='Delete'){
			var docObj = top.frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the template')
				if(ask==true){
					docObj.frmConsentForm.submit();
				}
			} 
		}*/ 
	<!-- CONSENT FORM TEMPLATE -->
	
	}else if(frameSrc=='consent_category.php'){
		if(toDo=='Forms'){
			/*var boxChksub = false;
			objsub = top.frames[0].frames[0].document.getElementsByName("chkBoxSub[]");
			objLengthsub = objsub.length;
			var count_sub=0;
			for(i=0; i<objLengthsub; i++){
				if(objsub[i].checked == true){
					count_sub++;
					var boxChksub = true;
				}
			}*/
		
			/*if(boxChksub==false)
			{
				alert("Please select the category");
			}else if(count_sub>1){
					alert("Please select only single category");
				}	
				else {
					var cat_id=objsub[0].value;	
					alert(cat_id);*/
				frameSrc = "consentFormMultiple.php";
				
				document.iframeMain.location.href = frameSrc;
				document.frameSrc.source.value = frameSrc;
				
				document.getElementById('categoryButton').style.display = 'block';
				document.getElementById('formsButton').style.display = 'none';
				document.getElementById('addNew').style.display = 'none';
				//}
		} else if(toDo=='Add New'){
			var objFrm = top.frames[0].frames[0].document.frmcategory;
			top.frames[0].frames[0].document.getElementById('newCategory').style.display = 'block';
			document.getElementById('cancelButton').style.display = 'block';
		} else if(toDo=='Save'){
			var objFrm = top.frames[0].frames[0].document.frmcategory;
			if(top.frames[0].frames[0].document.frmcategory.category_name.value==''){
				alert("Please specify category name");
				top.frames[0].frames[0].document.getElementById('newCategory').style.display = 'block';
			}else{
				top.frames[0].frames[0].document.frmcategory.insert_category.value = 'true';
				top.frames[0].frames[0].document.frmcategory.delete_category.value = '';
				top.frames[0].frames[0].document.frmcategory.submit();
			}
		}else if(toDo=='Delete'){
			var boxChksub_del = false;
			objsubdel = top.frames[0].frames[0].document.getElementsByName("chkBoxSub[]");
			objLengthsubdel = objsubdel.length;
			for(i=0; i<objLengthsubdel; i++){
				if(objsubdel[i].checked == true){
					boxChksub_del = true;
				}
			}
			if(boxChksub_del!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the category and its Consent forms?')
				if(ask==true){
					top.frames[0].frames[0].document.frmcategory.delete_category.value = 'true';
					top.frames[0].frames[0].document.frmcategory.insert_category.value = '';
					top.frames[0].frames[0].document.frmcategory.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		} 
	}
<!--  CONSENT FORM TEMPLATE END-->

	 else if(frameSrc=='consentFormMultiple.php'){
		<!-- MULTIPLE CONSENT FORM TEMPLATE -->
		//alert(toDo);
		
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.frmConsent;
			if(trim(formObj.category_consent.value)==''){
				alert('Select the category');
			}
			else if(trim(formObj.consent_name.value)==''){
				alert('Please enter template name.')
			}else if(trim(formObj.consent_alias.value)==''){
				alert('Please enter template alias.')
			}else{
				formObj.sbtConsent.value = 'true';
				formObj.submit();
				document.getElementById('cancelButton').style.display = 'none';
			}
			
		}else if(toDo=='Delete'){
			var docObj = top.frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the template')
				if(ask==true){
					docObj.frmConsent.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			
		} else if(toDo=='Category'){
			frameSrc = "consent_category.php";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('categoryButton').style.display = 'none';
		}
	<!-- MULTIPLE CONSENT FORM TEMPLATE -->
	
	}else if(frameSrc=='anesthesia_profile_save.php'){
	<!-- ANESTHESIA PROFILE -->
		//alert(top.iframeHome.frames[0].frames[0]);
		var docObj = top.frames[0].frames[0].frames[0].document.forms[0];	
		if(toDo=='Save'){
			//SET TOPAZ SIGNATURE VALUE OF ANESTHESIOLOGIST
				/*
				if(top.frames[0].frames[0].frames[0].document.SigPlus1) {
					var objSigPlus1 = top.frames[0].frames[0].frames[0].document.SigPlus1;
					if(objSigPlus1.NumberOfTabletPoints!=0 && objSigPlus1.TabletState==1) {
						  objSigPlus1.TabletState=0;
						  objSigPlus1.EncryptionMode=1;
						  objSigPlus1.SigCompressionMode=2;
						  
						  docObj.anesthesia_profile_sign.value=objSigPlus1.SigString;
					}
				}
				*/
			//SET TOPAZ SIGNATURE VALUE OF ANESTHESIOLOGIST
			
			top.frames[0].frames[0].frames[0].document.forms[0].submit();
		
		} else if(toDo=='Cancel'){	
			frameSrcChange = 'anesthesia_profile.php';
			document.iframeMain.location.href = frameSrcChange;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			
		}
	<!-- ANESTHESIA PROFILE --> 
	
	}else if(frameSrc=='nurseQuestion.php'){
	<!-- PRE OP NURSE QUESTION -->
		if(toDo=='Add New'){
			top.frames[0].frames[0].document.getElementById('nurseQuestionEnteryTr').style.display = 'block';
			document.getElementById('cancelButton').style.display = 'block';
		}
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.preOpNurseQuestionListFrm;
			
			formObj.sbtForm.value = 'true';
			document.getElementById('cancelButton').style.display = 'none';
			formObj.submit();
			
		}else if(toDo=='Delete'){
			
			var docObj = top.frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete selected records.')
				if(ask==true){
					docObj.preOpNurseQuestionListFrm.delRecords.value = 'true';
					docObj.preOpNurseQuestionListFrm.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
		} else if(toDo=='Category'){	
			//frameSrc('preOpMediOrderCategory.php?contentOf=Medications Category', 3);
			frameSrc = "nurseQuestionCategory.php?contentOf=Pre-Op Nurse Question Category";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('categoryButton').style.display = 'none';
			document.getElementById('optionsButton').style.display = 'block';
			
			
		}
	<!-- PRE OP NURSE QUESTION  -->
	
	}else if(frameSrc=='nurseQuestionCategory.php?contentOf=Pre-Op Nurse Question Category'){
		
	<!-- PRE OP NURSE QUESTION CATEGORY WHEN CLICK ON 'MANAGE CATEGORY BUTTON'  -->
		
		var docObj = top.frames[0].frames[0].document;
			var id = docObj.frmPreOpnurseQuestionCategory.contentOf.value;
		if(toDo=='Save'){			
			docObj.frmPreOpnurseQuestionCategory.submit();
			
		}else if(toDo=='Delete'){
			document.getElementById('cancelButton').style.display = 'none';
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete selected Category and its option(s)')
				if(ask==true){
					docObj.frmPreOpnurseQuestionCategory.sbmtFrm.value = '';
					docObj.frmPreOpnurseQuestionCategory.deleteSelected.value = 'true';
					docObj.frmPreOpnurseQuestionCategory.submit();
				}
			}
		}else if(toDo=='Add New'){
			document.getElementById('cancelButton').style.display = 'block';
			if(top.frames[0].frames[0].location){
				top.frames[0].frames[0].document.getElementById('nurseQuestionCategoryTr').style.display = 'block';
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			
		} else if(toDo=='Options'){	
			frameSrc = "nurseQuestion.php";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('categoryButton').style.display = 'block';
			document.getElementById('optionsButton').style.display = 'none';
			
		}
	<!-- PRE OP NURSE QUESTION CATEGORY WHEN CLICK ON 'MANAGE CATEGORY BUTTON'  -->
	
	
	}else if(frameSrc=='laser_procedure_admin.php'){
	<!-- LASER PROCEDURE -->
		//alert(top.iframeHome.frames[0].frames[0]);
		
		var docObj = top.frames[0].frames[0].document.forms[0];	
		//alert(docObj);
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].frames[0].document.frmlaserprocedure_templete;
			if(trim(formObj.template_name.value)==''){
				alert('Please enter template name.');
			}else{
				formObj.save_id.value = 'true';
				formObj.submit();
			//	document.getElementById('cancelButton').style.display = 'none';
			}
			
		} else if(toDo=='Cancel'){	
			frameSrcChange = 'laser_procedure_admin.php';
			document.iframeMain.location.href = frameSrcChange;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
		}else if(toDo=='Delete'){
			var docObj = top.frames[0].frames[0].frames[0].document;
			var boxesChk = false;
			obj = docObj.getElementsByName("chkBox[]");
			objLength = docObj.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select records to delete.")
			}else{
				var ask = confirm('Are you sure to delete the template')
				if(ask==true){
					docObj.frmlaserprocedure_templete.submit();
					//document.getElementById('cancelButton').style.display = 'none';
				}
			}
		}
	}
	<!-- LASER PROCEDURE -->
}
function closeMe(){
	//CHANGE TAB COLOR OF ADMIN
		if(top.document.getElementById("adminTab")) {
			top.document.getElementById("adminTab").className="link_a";
			top.document.getElementById("TDadminTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
			top.document.getElementById("TDadminMiddleTab").style.background="url(images/bg_tab.jpg)";
			top.document.getElementById("TDadminBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
		}	
		
		if(top.document.getElementById("anesthesiaTab")) {
			top.document.getElementById("anesthesiaTab").className="link_a";
			top.document.getElementById("TDanesthesiaTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
			top.document.getElementById("TDanesthesiaMiddleTab").style.background="url(images/bg_tab.jpg)";
			top.document.getElementById("TDanesthesiaBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
		}	
	//END CHANGE TAB COLOR OF ADMIN, AUDIT, REPORT
		
	top.frames[0].location = '../home_inner_front.php';
}


function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

//FUNCTION FOR SAVE, ADDNEW, DELETE, BUTTON ON MOUSEOVER,OUT, AND PRELOAD 
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//END FUNCTION FOR SAVE, ADDNEW, DELETE, BUTTON ON MOUSEOVER,OUT, AND PRELOAD
</script>
</head>
<body topmargin="0" leftmargin="0" style="overflow-y:hidden;" onLoad="MM_preloadImages('../images/add_new_hover.gif','../images/save_hover1.jpg','../images/new_user_hover.gif','../images/delete_selected_hover.gif','../images/cancel_hover1.jpg')">
<form name="frameSrc" method="" action="">
	<input type="hidden" name="source">
</form>
<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">
	<tr>
		<td width="97%" align="center">
			<table cellpadding="0" cellspacing="0" border="0" align="left">
					<tr>
						<?php 
						if(in_array("Super User", $userPrivilegesArr) || in_array("Admin", $userPrivilegesArr)){
							++$hr;
							?>
							<td align="left">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr style="cursor:pointer;">
										<td width="1" align="right"><img id="img1Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" nowrap valign="middle" bgcolor="#BCD2B0" id="Tab1" class="text_10bAdmin"><a id="Tab1Link" href="javascript:frameSrc('surgeryCenter.php', 1);" class="black">ASC</a></td>
										<td align="left" valign="top"><img id="img1Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
							<?php
						}
						if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("User", $admin_privilegesArr))){
							++$hr;
							?>
							<td align="right">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr onClick="" style="cursor:pointer;">
										<td width="6" align="right"><img id="img2Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" nowrap valign="middle" id="Tab2" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab2Link" href="javascript:frameSrc('userRegistration.php', 2);" class="black">Users</a></td>
										<td align="left" valign="top"><img id="img2Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
							<?php
						}
						if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("Pre-Op Med", $admin_privilegesArr))){
							++$hr;
							?>
							<td align="left">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr onClick="" style="cursor:pointer;">
										<td width="6" align="right"><img id="img3Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" nowrap valign="middle" id="Tab3" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab3Link" href="javascript:frameSrc('preOpMediOrder.php', 3);" class="black">Pre-Op Med. Order</a></td>
										<td align="left" valign="top"><img id="img3Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
							<?php
						}
						if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("Surgeon profile", $admin_privilegesArr))){
							++$hr;
							?>
							<td align="left">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr onClick="" style="cursor:pointer;">
										<td width="6" align="right"><img id="img4Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" nowrap valign="middle" id="Tab4" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab4Link" href="javascript:frameSrc('surgeonprofile.php', 4);" class="black">Surgeon Profile</a></td>
										<td align="left" valign="top"><img id="img4Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
							<?php
						}
						if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("Predefines", $admin_privilegesArr))){
							++$hr;
							?>
							<td align="left">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr onClick="" style="cursor:pointer;">
										<td width="6" align="right"><img id="img5Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" nowrap valign="middle" id="Tab5" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab5Link" href="javascript:frameSrc('predefineManager.php', 5);" class="black"  onMouseOver="return chkFrameSourse();">Pre-define Manager</a></td>
										<td align="left" valign="top"><img id="img5Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
							<?php
						}
						if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("Operative Reports", $admin_privilegesArr))){
							++$hr;
							?>
							<td align="left">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr onClick="" style="cursor:pointer;">
										<td width="6" align="right"><img id="img6Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" nowrap valign="middle" id="Tab6" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab6Link" href="javascript:frameSrc('operativeReportSurgeon.php', 6);" class="black">Op-Report</a></td>
										<td align="left" valign="top"><img id="img6Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
							<?php
						}
						if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("Operative Reports", $admin_privilegesArr))){
							?>
							<!--
							// -------------------- DISCHARGE SUMMARY SHEET ------------- //
							++$hr;
 							<td align="left">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr onClick="" style="cursor:pointer;">
										<td width="6" align="right"><img id="img7Left" src="../images/left.gif" width="3" height="24"></td>
										<td align="center" valign="middle" id="Tab7" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab7Link" href="javascript:frameSrc('dischargesummarysheet.php', 7);" class="black">Discharge Summary Sheet</a></td>
										<td align="left" valign="top"><img id="img7Right" src="../images/right.gif" width="3" height="24"></td>
									</tr>
								</table>
							</td>
						// -------------------- DISCHARGE SUMMARY SHEET ------------- //
						 -->						
 						<?php
					}
					if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("Instruction Sheet", $admin_privilegesArr))){
						++$hr;
						?>
						<td align="left">
							<table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr onClick="" style="cursor:pointer;">
									<td width="6" align="right"><img id="img8Left" src="../images/left.gif" width="3" height="24"></td>
									<td align="center" nowrap valign="middle" id="Tab8" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab8Link" href="javascript:frameSrc('instructionSheet.php', 8);" class="black">Instruct. Sheet</a></td>
									<td align="left" valign="top"><img id="img8Right" src="../images/right.gif" width="3" height="24"></td>
								</tr>
							</table>
						</td>
						<?php
					}
					if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privilegesArr))){
						++$hr;
						?>
						<td align="left">
							<table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr onClick="" style="cursor:pointer;">
									<td width="6" align="right"><img id="img9Left" src="../images/left.gif" width="3" height="24"></td>
									<td align="center" nowrap valign="middle" id="Tab9" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab9Link" href="javascript:frameSrc('consentFormMultiple.php', 9);" class="black"  >Consent Form</a></td>
									<td align="left" valign="top"><img id="img9Right" src="../images/right.gif" width="3" height="24"></td>
								</tr>
							</table>
						</td>
						<?php
					}
					//echo $user_type;
					if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privilegesArr))){
						++$hr;
						?>
						<td align="left">
							<table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr onClick="" style="cursor:pointer;">
									<td width="6" align="right"><img id="img10Left" src="../images/left.gif" width="3" height="24"></td>
									<td align="center" nowrap valign="middle" id="Tab10" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab10Link" href="javascript:frameSrc('anesthesia_profile.php', 10);" class="black"  >Ans Profile</a></td>
									<td align="left" valign="top"><img id="img10Right" src="../images/right.gif" width="3" height="24"></td>
								</tr>
							</table>
						</td>
						<?php
					}
					if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privilegesArr))){
						++$hr;
						?>
						<td align="left">
							<table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr onClick="" style="cursor:pointer;">
									<td width="6" align="right"><img id="img11Left" src="../images/left.gif" width="3" height="24"></td>
									<td align="center" nowrap valign="middle" id="Tab11" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab11Link" href="javascript:frameSrc('nurseQuestion.php', 11);" class="black"  >Pre-op. Nurse</a></td>
									<td align="left" valign="top"><img id="img11Right" src="../images/right.gif" width="3" height="24"></td>
								</tr>
							</table>
						</td>
						<?php
					}
					if(in_array("Super User", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privilegesArr))){
						++$hr;
						?>
						<td align="left">
							<table border="0" align="center" cellpadding="0" cellspacing="0">
								<tr onClick="" style="cursor:pointer;">
									<td width="6" align="right"><img id="img12Left" src="../images/left.gif" width="3" height="24"></td>
									<td align="center" nowrap valign="middle" id="Tab12" bgcolor="#BCD2B0" class="text_10bAdmin"><a id="Tab12Link" href="javascript:frameSrc('laser_procedure_admin.php', 12);"  class="black"  >Laser</a></td>
									<td align="left" valign="top"><img id="img12Right" src="../images/right.gif" width="3" height="24"></td>
								</tr>
							</table>
						</td>
						<?php
					}
					?>
				</tr>					
				<tr height="5" bgcolor="#003300">
					<td colspan="<?php echo $hr; ?>"></td>
				</tr>	
			</table>
		</td>
		<td width="3%" align="left"><img border="0" src="../images/close.jpg" onClick="return closeMe();"></td>
	</tr>
	<tr>
		<td colspan="5" >
			<iframe name="iframeMain" src="" width="100%" height="535"  frameborder="0"></iframe>
		</td>
	</tr>
	<tr>
		
		<td colspan="5" align="center">
			<table  border="0" cellpadding="0" cellspacing="0">
				  <!-- <tr>
					<td><input style="display:none;width:125px;" type="button" class="button" value="Add New" onClick="return getPageSrc('Add New');" name="addNew"></td>
					<td><input style="display:none;width:125px;" type="button" class="button" value="Save" onClick="return getPageSrc('Save');" name="saveButton"></td>
					<td><input style="display:none;width:125px;" type="button" class="button" value="New User" onClick="return getPageSrc('New');" name="newUser"></td>
					<td><input style="display:none;width:125px;" type="button" class="button" value="Delete Selected" onClick="return getPageSrc('Delete');" name="deleteSelected"></td>
					<td><a href="javascript:frameSrc('surgeonprofile.php', 4);" onClick="MM_swapImage('cancelButton','','../images/cancel_onclick1.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('cancelButton','','../images/cancel_hover1.jpg',1)"><img src="../images/cancel.jpg" name="cancelButton" width="70" height="25" border="0" id="cancelButton" style="display:none;" alt="Cancel"  /></a></td>
					
				</tr>  -->
				 <tr>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td align="left"><a style="width:120px; padding-left:45px; " href="#" onClick="MM_swapImage('backButton','','../images/back_new_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('backButton','','../images/back_new_hover.gif',1)"><img src="../images/back_new.gif" name="backButton"  border="0" id="backButton" style="display:none;" alt="Back" onClick="return getPageSrc('Back');"  /></a></td>
							
							<td align="left">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td><a href="#" onClick="MM_swapImage('addNew','','../images/add_new_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('addNew','','../images/add_new_hover.gif',1)"><img src="../images/add_new.gif" name="addNew" id="addNew" style="display:none; " border="0"  alt="Add New" onClick="return getPageSrc('Add New');"/></a></td>
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('newUser','','../images/new_user_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('newUser','','../images/new_user_hover.gif',1)"><img src="../images/new_user.gif" name="newUser" id="newUser" style="display:none; " border="0"  alt="New User" onClick="return getPageSrc('New');"/></a></td>
										<td>&nbsp;</td>
										<!-- <td><a href="#" onClick="MM_swapImage('backButton','','../images/back_new_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('backButton','','../images/back_new_hover.gif',1)"><img src="../images/back_new.gif" name="backButton"  border="0" id="backButton" style="display:none;" alt="Back" onClick="return getPageSrc('Back');"  /></a></td>
										<td>&nbsp;</td> -->
										<td><a href="#" onClick="MM_swapImage('saveButton','','../images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveButton','','../images/save_hover1.jpg',1)"><img src="../images/save.jpg" name="saveButton" id="saveButton" style="display:none; " border="0"  alt="Save" onClick="return getPageSrc('Save');"/></a> </td>
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('deleteSelected','','../images/delete_selected_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('deleteSelected','','../images/delete_selected_hover.gif',1)"><img src="../images/delete_selected.gif" name="deleteSelected" id="deleteSelected" style="display:none; " border="0"  alt="Delete" onClick="return getPageSrc('Delete');"/></a></td>
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('cancelButton','','../images/cancel_onclick1.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('cancelButton','','../images/cancel_hover1.jpg',1)"><img src="../images/cancel.jpg" name="cancelButton" width="70" height="25" border="0" id="cancelButton" style="display:none;" alt="Cancel" onClick="return getPageSrc('Cancel');"  /></a></td>
										<!-- <td><a href="javascript:frameSrc('surgeonprofile.php', 4);" onClick="MM_swapImage('cancelButton','','../images/cancel_onclick1.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('cancelButton','','../images/cancel_hover1.jpg',1)"><img src="../images/cancel.jpg" name="cancelButton" width="70" height="25" border="0" id="cancelButton" style="display:none;" alt="Cancel"  /></a></td>  -->
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('categoryButton','','../images/manageCategory_onclick1.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('categoryButton','','../images/manageCategory_hover1.gif',1)"><img src="../images/manageCategory.gif" name="categoryButton"  border="0" id="categoryButton" style="display:none;" alt="Manage Category" onClick="return getPageSrc('Category');"  /></a></td>
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('medicationButton','','../images/manageMedication_onclick1.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('medicationButton','','../images/manageMedication_hover1.gif',1)"><img src="../images/manageMedication.gif" name="medicationButton"  border="0" id="medicationButton" style="display:none;" alt="Manage Medication" onClick="return getPageSrc('Medication');"  /></a></td>
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('formsButton','','../images/manageformsButton_onclick.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('formsButton','','../images/manageformsButton_hover.gif',1)"><img src="../images/manageformsButton.gif" name="formsButton"  border="0" id="formsButton" style="display:none;" alt="Manage Forms" onClick="return getPageSrc('Forms');"  /></a></td>
										<td>&nbsp;</td>
										<td><a href="#" onClick="MM_swapImage('optionsButton','','../images/manageOptions_onclick1.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('optionsButton','','../images/manageOptions_hover1.gif',1)"><img src="../images/manageOptions.gif" name="optionsButton"  border="0" id="optionsButton" style="display:none;" alt="Manage Options" onClick="return getPageSrc('Options');"  /></a></td>
									
									</tr>
								</table>
							</td>
						</tr>
						<!-- <tr>
							<td colspan="2" align="center">
								Copyrights &copy; 2007, 2008, 2009. imwemr &reg; All rights reserved.
							</td>
						</tr> -->
					</table>
				</tr>  
				
			</table>
		</td>
	</tr>
</table>
<script>
//IF LOGGED IN USER IS ANESTHESIOLOGIST THEN -->
	var anesthesiologistList = '<?php echo $_REQUEST["anesthesiologistList"];?>';
	if(anesthesiologistList) {
		//alert(top.iframeHome.frames[0].frames[0])
		document.iframeMain.location.href='anesthesia_profile.php?anesthesiologistList='+anesthesiologistList;
	}
//END IF LOGGED IN USER IS ANESTHESIOLOGIST
</script>
</body>
</html>