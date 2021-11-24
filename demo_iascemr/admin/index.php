<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?>
<?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId']; 

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
$userPrivileges = $_SESSION['userPrivileges'];
	$userPrivileges = explode(", ", $userPrivileges);
$admin_privileges = $_SESSION['admin_privileges'];
	if($admin_privileges){
		$admin_privileges = explode(', ', $admin_privileges);
	}    
*/
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<title>Admin Surgery Center</title>
<?php include("adminLinkfile.php");?>
<LINK HREF="adminStyle.css" TYPE="text/css" REL="stylesheet">
<style>
a.black:hover {
	color: "Red";
	text-decoration: none;
}
a.white {
	color: #FFFFFF;
	text-decoration: none;
}
</style>
<?php
include_once("../no_record.php");
?>
<script type="text/javascript" src="../js/jquery.js" ></script>
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

function hideAllButton()
{
		var HideButtons		=	'#saveButton, #deleteSelected, #newUser, #importSupplies, #addNew, #cancelButton, #categoryButton, #backButton, #formsButton, #optionsButton, #diagnosisButton, #CopyFromCommunity, #CopyToCommunity';
		top.frames[0].$(HideButtons).hide(50);	
		
}
function frameSrc(source, id){
	top.iframeHome.iframeMain.location.href = source;
	
	document.getElementById('saveButton').style.display = 'none';
	document.getElementById('deleteSelected').style.display = 'none';
	document.getElementById('newUser').style.display = 'none';
	document.getElementById('addNew').style.display = 'none';
	document.getElementById('cancelButton').style.display = 'none';
	document.getElementById('categoryButton').style.display = 'none';
	document.getElementById('backButton').style.display = 'none';
	document.getElementById('formsButton').style.display = 'none';
	document.getElementById('optionsButton').style.display = 'none';
	document.getElementById('diagnosisButton').style.display = 'none';
	document.getElementById('CopyFromCommunity').style.display = 'none';
	document.getElementById('CopyToCommunity').style.display = 'none';
		
	if(source=='surgeryCenter.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('cancelButton').style.display = 'inline-block';
		
		
	}
	if(source=='userRegistration.php'){
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('newUser').style.display = 'inline-block';
	}
	if(source=='preOpMediOrder.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('addNew').style.display = 'inline-block';
		document.getElementById('categoryButton').style.display = 'inline-block';
	}
	if(source=='surgeonprofile.php'){
	}
	if(source == 'addSurgeonProfile.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('cancelButton').style.display = 'inline-block';
		document.getElementById('backButton').style.display = 'inline-block';
	}
	if(source=='procedureprofile.php'){
	}
	if(source == 'addProcedure.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('cancelButton').style.display = 'inline-block';
		document.getElementById('backButton').style.display = 'inline-block';
	}
	if(source == 'predefineManager.php'){
	}
	if(source == 'preDefinedDataList.php'){
	}
	if(source == 'operativeReportSurgeon.php'){
	}
	if(source == 'operativeReport.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('cancelButton').style.display = 'inline-block';
	}
	if(source == 'instructionSheet.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
	}
	if(source == 'consent_form_select.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
	}
	if(source == 'consentFormMultiple.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('categoryButton').style.display = 'inline-block';
	}
	if(source == 'anesthesia_profile.php'){
	}
	if(source == 'laser_procedure_admin.php'){
	}
	if(source == 'injection_misc.php'){
	}
	
	if(source == 'consent_category.php'){
		document.getElementById('deleteSelected').style.display = 'inline-block';
	}
	if(source=='nurseQuestion.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('addNew').style.display = 'inline-block';
		document.getElementById('categoryButton').style.display = 'inline-block';
	}
	if(source == 'anes_ekg_admin.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
	}
	if(source == 'supplies.php'){
		document.getElementById('saveButton').style.display = 'inline-block';
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('addNew').style.display = 'inline-block';
	}
	if(source == 'facilityRegistration.php'){
		document.getElementById('deleteSelected').style.display = 'inline-block';
		document.getElementById('addNew').style.display = 'inline-block';
	}
	
	
	/*	
	for(var i=1; i<=13; i++){
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
	*/
	document.frameSrc.source.value = source;
	
}
function chkFrameSourse(){
	if(top.frames[0].frames[0].document.getElementById('labelTr')){
		top.frames[0].frames[0].document.getElementById('labelTr').style.display = 'inline-block';
	}
}

function hasClassJSID(selector,$this) {
  	var className =	" " + selector + " ";
		if ($this.nodeType === 1 && (" " + $this.className + " ").replace('/[\n\t\r]/g', " ").indexOf(className) > -1) {
            return true;
        }
		return false;
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
			//var f1 = objFrm.sergeryCenerName.value;
			//var f2 = objFrm.sergeryCenerAddress.value;
			//var f3 = objFrm.sergeryCenerPhone.value;
			//var f4 = objFrm.sergeryCenerFax.value;
			//var f5 = objFrm.sergeryCenerEmail.value;
			//var f6 = objFrm.sergeryCenerNPI.value;
			//var f7 = objFrm.sergeryCenerFederal.value;
			var f8 = objFrm.elem_maxRecentlyUsedPass.value;
			var f9 = objFrm.elem_maxLoginAttempts.value;
			var f10 = objFrm.elem_maxPassExpiresDays.value;
			var f12 = objFrm.elem_finalizeDays.value;
			var f13 = objFrm.elem_finalizeWarningDays.value;
			//var f14 = objFrm.elem_city.value;
			//var f15 = objFrm.elem_state.value;
			//var f16 = objFrm.elem_zip.value;
			//if(f1==''){ var msg = msg+"\t� Surgery Center Name.\n"; ++flag; }
			//if(f2==''){ var msg = msg+"\t� Surgery Center Address.\n"; ++flag; }
			//if(f14==''){ var msg = msg+"\t� City.\n"; ++flag; }
			//if(f15==''){ var msg = msg+"\t� State.\n"; ++flag; }
			//if(f16==''){ var msg = msg+"\t� Zip.\n"; ++flag; }
			//if(f3==''){ var msg = msg+"\t� Surgery Center Phone.\n"; ++flag; }
			//if(f4==''){ var msg = msg+"\t� Surgery Center Fax.\n"; ++flag; }
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
	
	}
	else if(frameSrc=='userRegistration.php'){	
	<!-- USER REGISTERATION -->
		if(toDo=='New'){
			
			//top.frames[0].frames[0].document.getElementById('tdFrameUserRegistration').style.display = 'none';
			
			//top.frames[0].frames[0].document.getElementById('formTr').style.display = 'block';
			top.frames[0].frames[0].document.getElementById('userFrame').src= 'userRegistrationForm.php';
			/*top.frames[0].frames[0].$("canvas").each(function(){ 
				
			 });*/
			//alert(top.frames[0].frames[0].frames[0].document.forms[0].name);
			document.getElementById('newUser').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'inline-block';
			document.getElementById('cancelButton').style.display = 'inline-block';
			
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
					document.getElementById('saveButton').style.display = 'inline-block';
					document.getElementById('cancelButton').style.display = 'inline-block';
			
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
			
			if(typeof(top.frames[0].frames[0].frames[0].document.applets['app_signature'])!="undefined") {
				if(typeof(top.frames[0].frames[0].frames[0].document.applets['app_signature']).clearIt!="undefined") {
					top.frames[0].frames[0].frames[0].document.applets['app_signature'].clearIt();
				}
			}
			top.frames[0].frames[0].frames[0].document.getElementById('adminPriviliges'). style.display = 'none';
		}
		else if(toDo=='Delete'){
			
			var frameObj = top.frames[0].frames[0].frames[0];
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
		}
		else if(toDo=='Save'){
			var objFrm = top.frames[0].frames[0].frames[0].document.frmUserRegistration;

			var msg="Please fill in the following:- \n";
			var flag = 0;
			var f1 = objFrm.elem_fname.value;	
			var f2 = objFrm.elem_lname.value;
			var f3 = objFrm.elem_type.value;
			//var f4 = objFrm.elem_practiceName.value;
			//var f5 = objFrm.elem_address.value;
			//var f6 = objFrm.elem_federalEin.value;
			var f7 = objFrm.elem_npi.value;
			//var f8 = objFrm.elem_privileges.value;
			var f9 = objFrm.elem_loginName.value;
			var f10 = objFrm.elem_password.value;
			var f11 = objFrm.elem_confirmPass.value;
			
			//var f12 = objFrm.elem_city.value;
			//var f13 = objFrm.elem_state.value;
			//var f14 = objFrm.elem_zip.value;
			var f15	=	objFrm.elem_initial.value;
			var f16	=	objFrm.elem_sso_identifier.value;
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
			
			//check usernme and passowrd if already exists
			var usrId = objFrm.elem_usersId.value;
			frm_data = 'user_id='+usrId+'&npi='+f7+'&username='+f9+'&pass='+f10+'&initial='+f15+'&sso_identifier='+f16;
			$.ajax({
				type: "POST",
				url: "userRegistrationForm_ajax.php",
				data: frm_data,
				success: function(d){
					if(d) {
						alert(d);
						return false;
					}else {
						objFrm.submit();
						document.getElementById('newUser').style.display = 'inline-block';		
						document.getElementById('deleteSelected').style.display = 'inline-block';
						document.getElementById('saveButton').style.display = 'none';
						document.getElementById('cancelButton').style.display = 'none';
						return true;			
					}
					
				}
			});	
		}
		else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('newUser').style.display = 'inline-block';
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'inline-block';
		}
		
		
	<!-- USER REGISTERATION -->
	}
	else if(frameSrc=='preOpMediOrder.php'){
	<!-- PRE OP MEDICATION ORDER -->
		if(toDo=='Add New'){
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			top.frames[0].frames[0].add_btn_click();
			//top.frames[0].frames[0].document.getElementById('newEnteryTr').style.display = 'inline-table';
			//document.getElementById('cancelButton').style.display = 'inline-block';
		}
		if(toDo=='Save'){
			document.getElementById('saveButton').style.display = 'inline-block';
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
			document.getElementById('saveButton').style.display = 'inline-block';
		} else if(toDo=='Category'){	
			//frameSrc('preOpMediOrderCategory.php?contentOf=Medications Category', 3);
			frameSrc = "preOpMediOrderCategory.php?contentOf=Medications Category";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('categoryButton').style.display = 'none';
			document.getElementById('medicationButton').style.display = 'inline-block';
			
		}
	<!-- PRE OP MEDICATION ORDER  -->
	
	}
	else if(frameSrc=='preOpMediOrderCategory.php?contentOf=Medications Category'){
		
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
				var ask = confirm('Are you sure to delete the selected category and its medication(s)')
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
			if(top.frames[0].frames[0].location){
				
				if(id == 'Procedures'){
					document.getElementById('cancelButton').style.display = 'inline-block';
					top.frames[0].frames[0].frames[1].document.getElementById('procedureTr').style.display = 'inline-block';
				}else{
					document.getElementById('cancelButton').style.display = 'none';
					document.getElementById('saveButton').style.display = 'none';
					top.frames[0].frames[0].add_btn_click();
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
			document.getElementById('categoryButton').style.display = 'inline-block';
			document.getElementById('medicationButton').style.display = 'none';
			
		}
	<!-- PRE OP MEDICATION ORDER CATEGORY WHEN CLICK ON 'MANAGE CATEGORY BUTTON'  -->
	
	}
	else if(frameSrc=='surgeonprofile.php'){
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
				//docObj.getElementById('selectedSurgeonNameId').style.display = 'inline-block';

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
			document.getElementById('backButton').style.display = 'none';
			
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
	}
	else if(frameSrc=='addSurgeonProfile.php'){
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
	}
	else if(frameSrc=='procedureprofile.php'){
		
		var docObj = top.frames[0].frames[0].document;
		
		if(toDo=='Save'){
			docObj.procedureListFrm.submit();
		}
		else if(toDo=='Cancel')
		{	
			top.openAdminTab('procedureprofile');
		}
	}
	else if(frameSrc=='predefineManager.php'){
	<!-- PREDEFINE MANAGER -->
		var docObj = top.frames[0].frames[0].frames[0].document;
		if(docObj.preDefineForm){
			var id = docObj.preDefineForm.contentOf.value;
		}else if(docObj.perDefineProceduresFrm){
			var id = docObj.perDefineProceduresFrm.contentOf.value;
		}else if(docObj.perDefineDiagnosisFrm){
			var id = docObj.perDefineDiagnosisFrm.contentOf.value;
		}else if(docObj.perDefineManufacturerLensBrandFrm){
			var id = docObj.perDefineManufacturerLensBrandFrm.contentOf.value;
		}else if(docObj.perDefineZipCodeFrm){
			var id = docObj.perDefineZipCodeFrm.contentOf.value;
		}else if(docObj.perDefineChartUnlockFrm){
			var id = docObj.perDefineChartUnlockFrm.contentOf.value;
		}else if(docObj.perDefineModifierFrm){
			var id = docObj.perDefineModifierFrm.contentOf.value;
		}else if(docObj.perDefinePreOpNurseFrm){
			var id = docObj.perDefinePreOpNurseFrm.contentOf.value;
		}else if(docObj.perDefineCustomCheckListFrm){
			var id = docObj.perDefineCustomCheckListFrm.contentOf.value;
		}else if(docObj.perDefineProcedureGroup){
			var id = docObj.perDefineProcedureGroup.contentOf.value;
		}else if(docObj.macRegionalQuestionsFrm){
			var id = docObj.macRegionalQuestionsFrm.contentOf.value;
		}else {
			var id = docObj.preDefineForm.contentOf.value;
		}
		if(toDo=='Save'){			
			if(id == 'Procedures'){
				docObj.perDefineProceduresFrm.submit();
			}
			else if(id == 'Diagnosis ICD9' || id == 'Diagnosis ICD10'){
				
				if(id == 'Diagnosis ICD10')
				{
						var L	=	docObj.getElementsByName('preDefineDiagCode10[]');
						var len	=	(L.length-1);
							
						if( hasClassJSID('in',docObj.getElementById('diagnosisTr') ))
						{
								var V	=	L[len].value.trim();
								if(V === '')
								{
										alert("ICD 10 field should not be blank"); 
										L[len].focus();
										return false;
								}
									
						}
						else
						{
								for (var i = 0 ; i < len ; i++)
								{
									var V	=	L[i].value.trim();
									if(V === '')
									{
											alert("ICD 10 field can't be blank"); 
											L[i].focus();
											return false;
									}
								}
						}
				}
				
				docObj.perDefineDiagnosisFrm.submit();
			}
			else if(id == 'Manufacturer Lens Brand'){
				docObj.perDefineManufacturerLensBrandFrm.submit();
			}
			else if(id == 'Zip Codes'){
				docObj.perDefineZipCodeFrm.submit();
			}
			else if(id == 'Modifiers'){
				docObj.perDefineModifierFrm.submit();
			}
			else if(id == 'Pre-Op Nurse'){
				docObj.perDefinePreOpNurseFrm.submit();
			}
			else if(id == 'Custom Check List'){
				docObj.perDefineCustomCheckListFrm.submit();
			}
			else if(id == 'Procedures Group'){
				docObj.perDefineProcedureGroup.submit();
			}
			else if(id == 'Mac/Regional Questions'){
				docObj.macRegionalQuestionsFrm.submit();
			}
			else{
				if(id == 'Supplies Used')
				{
					var L	=	docObj.getElementsByName('cat_id[]');
					var L1= docObj.getElementsByName('preDefines[]');
					
					var len	=	(L.length-1);
					if( hasClassJSID('in',docObj.getElementById('predefimeTr') ))
					{
						var V	=	L[len].value.trim();
						var V1= L1[len].value.trim();
						if(V === ''){
							alert("Supply category should not be blank"); 
							L[len].focus();
							return false;
						}
						else if(V1 === ''){
							alert("Supply used should not be blank"); 
							L1[len].focus();
							return false;
						}
					}
					else{
						for (var i = 0 ; i < len ; i++){
							var V	=	L[i].value.trim();
							var V1= L1[i].value.trim();
							
							if(V === ''){
								alert("Supply category field can't be blank"); 
								L[i].focus();
								return false;
							}
							else if(V1 === ''){
								alert("Supply used field can't be blank"); 
								L1[i].focus();
								return false;
							}
						}
					}
					
				}
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
				
				if(id == 'Supplies Used')
					var ask = confirm('Are you sure to delete the selected record(s).\n Deleted Supplies Used will not be shown in Procedure Supplies Templates. ');
				else if(id == 'Pre-Op Nurse Category' || id == 'Custom Check List Category')
					var ask = confirm('Are you sure to delete selected Category and its option(s)'); 	
				else
					var ask = confirm('Are you sure to delete the selected record(s)')
				if(ask==true){
					if(id == 'Procedures'){
						docObj.perDefineProceduresFrm.deleteSelected.value = 'true';
						docObj.perDefineProceduresFrm.submit();			
					}else if(id == 'Diagnosis ICD9' || id == 'Diagnosis ICD10'){
						docObj.perDefineDiagnosisFrm.deleteSelected.value = 'true';
						docObj.perDefineDiagnosisFrm.submit();			
					}else if(id == 'Manufacturer Lens Brand'){
						docObj.perDefineManufacturerLensBrandFrm.deleteSelected.value = 'true';
						docObj.perDefineManufacturerLensBrandFrm.submit();			
					}else if(id == 'Zip Codes'){
						docObj.perDefineZipCodeFrm.deleteSelected.value = 'true';
						docObj.perDefineZipCodeFrm.submit();			
					}else if(id == 'Chart Unlock'){
						docObj.perDefineChartUnlockFrm.deleteSelected.value = 'true';
						docObj.perDefineChartUnlockFrm.submit();			
					}else if(id == 'Modifiers'){
						docObj.perDefineModifierFrm.deleteSelected.value = 'true';
						docObj.perDefineModifierFrm.submit();			
					}else if(id == 'Pre-Op Nurse'){
						docObj.perDefinePreOpNurseFrm.deleteSelected.value = 'true';
						docObj.perDefinePreOpNurseFrm.submit();			
					}else if(id == 'Custom Check List'){
						docObj.perDefineCustomCheckListFrm.deleteSelected.value = 'true';
						docObj.perDefineCustomCheckListFrm.submit();			
					}else if(id == 'Procedures Group'){
						docObj.perDefineProcedureGroup.deleteSelected.value = 'true';
						docObj.perDefineProcedureGroup.submit();			
					}else if(id == 'Mac/Regional Questions'){
						docObj.macRegionalQuestionsFrm.deleteSelected.value = 'true';
						docObj.macRegionalQuestionsFrm.submit();			
					}else{
						docObj.preDefineForm.sbmtFrm.value = '';
						docObj.preDefineForm.deleteSelected.value = 'true';
						docObj.preDefineForm.submit();
					}
				}
			}
		}else if(toDo=='Add New'){
			if(top.frames[0].frames[0].frames[0].location){
				if(id == 'Procedures'){
					//top.frames[0].frames[0].frames[0].document.getElementById('procedureTr').style.display = 'inline-block';
					top.frames[0].frames[0].frames[0].$('#predefimeTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Diagnosis ICD9' || id == 'Diagnosis ICD10'){
					//top.frames[0].frames[0].frames[0].document.getElementById('diagnosisTr').style.display = 'inline-block';
					top.frames[0].frames[0].frames[0].$('#diagnosisTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Manufacturer Lens Brand'){
					//top.frames[0].frames[0].frames[0].document.getElementById('manufacturerLensBrandTr').style.display = 'inline-block';
					top.frames[0].frames[0].frames[0].$('#manufacturerLensBrandTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Modifiers'){
					top.frames[0].frames[0].frames[0].$('#modifierTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Pre-Op Nurse'){
					top.frames[0].frames[0].frames[0].$('#preOpNurseTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Custom Check List'){
					top.frames[0].frames[0].frames[0].$('#customChecklistTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Procedures Group'){
					top.frames[0].frames[0].frames[0].$('#proGroupTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Zip Codes'){
					top.frames[0].frames[0].frames[0].$('#zipCodeTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else if(id == 'Mac/Regional Questions'){
					top.frames[0].frames[0].frames[0].$('#macQuesTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}else{
					//top.frames[0].frames[0].frames[0].document.getElementById('predefimeTr').style.display = 'inline-block';
					top.frames[0].frames[0].frames[0].$('#predefimeTr').modal({
					show: true,
					backdrop: true,
					keyboard: true
					});
				}
			}
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('addNew').style.display = 'none';
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('importSupplies').style.display = 'none';
			
		}
		else if(toDo=='Close'){	
			if(top.frames[0].frames[0].frames[0].location){
				if(id == 'Procedures'){
					top.frames[0].frames[0].frames[0].document.getElementById('procedureTr').style.display = 'inline-block';
				}else if(id == 'Diagnosis'){
					top.frames[0].frames[0].frames[0].$('#diagnosisTr').modal({
					show: false,
					backdrop: true,
					keyboard: true
					});

				}else if(id == 'Manufacturer Lens Brand'){
					top.frames[0].frames[0].frames[0].document.getElementById('manufacturerLensBrandTr').style.display = 'inline-block';
				}else if(id == 'Zip Codes'){
					top.frames[0].frames[0].frames[0].document.getElementById('zipTr').style.display = 'inline-block';
				}else if(id == 'Modifiers'){
					top.frames[0].frames[0].frames[0].document.getElementById('modifierTr').style.display = 'inline-block';
				}else if(id == 'Pre-Op Nurse'){
					top.frames[0].frames[0].frames[0].document.getElementById('preOpNurseTr').style.display = 'inline-block';
				}else if(id == 'Custom Check List'){
					top.frames[0].frames[0].frames[0].document.getElementById('customChecklistTr').style.display = 'inline-block';
				}else{
					top.frames[0].frames[0].frames[0].document.getElementById('predefimeTr').style.display = 'inline-block';
				}
			}
		}
		else if(toDo=='Import Supplies'){	
			var frm_data = '';
			$.ajax({
				type: "POST",
				url: "supplies_import.php",
				data: frm_data,
				beforeSend: function()
				{
					top.frames[0].frames[0].frames[0].$(".loader").fadeIn(500);
				},
				complete: function()
				{
					top.frames[0].frames[0].frames[0].$(".loader").fadeOut(500);
				},
				success: function(d){
					if(d) {
						if(d=='Success') {
							top.frames[0].frames[0].frames[0].location.href = 'predefineFrmForm.php?contentOf=Supplies Used';
							top.frames[0].alert_msg('update','','Supplies Imported Successfuly');
						}else {
							top.modalAlert(d);
						}
					}
				}
			});	
		
		}
	<!-- PREDEFINE MANAGER -->
	}
	else if(frameSrc=='operativeReport.php'){
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
					docObj.getElementById('act').value='delete';
					docObj.frmOperativeReport.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		}
		else if(toDo=='CopyToCommunity'){
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
				alert("Please select records to copy.")
			}else{
				var ask = confirm('Are you sure to copy template')
				if(ask==true){
					
					docObj.getElementById('act').value='CopyToCommunity';
					docObj.frmOperativeReport.submit();
					document.getElementById('cancelButton').style.display = 'none';
				}
			}
		}
		else if(toDo=='CopyFromCommunity'){
			top.frames[0].frames[0].open_div_surgeon(true);
		}
		
		 else if(toDo=='Cancel'){	
			//document.iframeMain.location.href = frameSrc;
			document.iframeMain.location.href = 'operativeReportSurgeon.php';
			
			document.getElementById('cancelButton').style.display = 'none';
			
		}
	<!-- OPERATIVE TEMPLATE -->
	}
	else if(frameSrc=='instructionSheet.php'){
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
	}
	else if(frameSrc=='consentForm.php'){
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
	
	}
	else if(frameSrc=='consent_category.php'){
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
				
				document.getElementById('categoryButton').style.display = 'inline-block';
				document.getElementById('formsButton').style.display = 'none';
				document.getElementById('addNew').style.display = 'none';
				//}
		} else if(toDo=='Add New'){
			
			//var objFrm = top.frames[0].frames[0].document.frmcategory;
			top.frames[0].frames[0].$('#newCategory').modal({
			show: true,
			backdrop: true,
			keyboard: true
			});
			//document.getElementById('cancelButton').style.display = 'inline-block';
			
		} else if(toDo=='Save'){
			var objFrm = top.frames[0].frames[0].document.frmcategory;
			top.frames[0].frames[0].document.frmcategory.delete_category.value = '';
			top.frames[0].frames[0].document.frmcategory.submit();
			
			/*
			if(top.frames[0].frames[0].document.frmcategory.category_name.value=='' && top.frames[0].frames[0].document.getElementById('newCategory').style.display == 'inline-block'){
				alert("Please specify category name");
				//top.frames[0].frames[0].document.getElementById('newCategory').style.display = 'inline-block';
			}else{
				//top.frames[0].frames[0].document.frmcategory.insert_category.value = 'true';
				top.frames[0].frames[0].document.frmcategory.delete_category.value = '';
				top.frames[0].frames[0].document.frmcategory.submit();
			}*/
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
	
	}
	else if(frameSrc=='anesthesia_profile_save.php'){
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
	
	}
	else if(frameSrc=='nurse_profile_save.php'){
	<!-- NURSE PROFILE -->
		//alert(top.iframeHome.frames[0].frames[0]);
		var docObj = top.frames[0].frames[0].frames[0].document.forms[0];	
		if(toDo=='Save'){
			top.frames[0].frames[0].frames[0].document.forms[0].submit();
		} else if(toDo=='Cancel'){	
			frameSrcChange = 'nurse_profile.php';
			document.iframeMain.location.href = frameSrcChange;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			
		}
	<!-- ANESTHESIA PROFILE --> 
	
	}
	else if(frameSrc=='nurseQuestion.php'){
	<!-- PRE OP NURSE QUESTION -->
		if(toDo=='Add New'){
			top.frames[0].frames[0].add_btn_click();
			//top.frames[0].frames[0].document.getElementById('nurseQuestionEnteryTr').style.display = 'inline-block';
			//document.getElementById('cancelButton').style.display = 'inline-block';
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
			document.getElementById('optionsButton').style.display = 'inline-block';
			
			
		}
	<!-- PRE OP NURSE QUESTION  -->
	
	}
	else if(frameSrc=='nurseQuestionCategory.php?contentOf=Pre-Op Nurse Question Category'){
		
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
			//document.getElementById('cancelButton').style.display = 'inline-block';
			/*if(top.frames[0].frames[0].location){
				top.frames[0].frames[0].document.getElementById('nurseQuestionCategoryTr').style.display = 'inline-block';
			}*/
			top.frames[0].frames[0].add_btn_click();
		} else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			
		} else if(toDo=='Options'){	
			frameSrc = "nurseQuestion.php";
			document.iframeMain.location.href = frameSrc;
			document.frameSrc.source.value = frameSrc;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('categoryButton').style.display = 'inline-block';
			document.getElementById('optionsButton').style.display = 'none';
			
		}
	<!-- PRE OP NURSE QUESTION CATEGORY WHEN CLICK ON 'MANAGE CATEGORY BUTTON'  -->
	
	
	}
	else if(frameSrc=='laser_procedure_admin.php'){
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
	else if(frameSrc=='injection_misc.php'){
		var docObj = top.frames[0].frames[0].document.forms[0];	
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].frames[0].document.frmInjectionMiscTemplate;
			if(trim(formObj.templateName.value)==''){
				alert('Please enter template name.');
			}else{
				formObj.submitForm.value = 'true';
				formObj.submit();
			}
			
		} else if(toDo=='Cancel'){	
			frameSrcChange = 'injection_misc.php';
			document.iframeMain.location.href = frameSrcChange;
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
		} else if(toDo=='Delete'){
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
					docObj.frmInjectionMiscTemplate.submit();
				}
			}
		}
	}
	else if(frameSrc=='supplies.php'){
		// Procedure Supplies
		
		if(toDo=='Add New'){
			document.getElementById('saveButton').style.display = 'none';
			top.frames[0].frames[0].add_btn_click();
		}
		if(toDo=='Save'){
			
			var docObj = top.frames[0].frames[0];
			
			if(!docObj.validateFields()) return false;
			
			var formObj = top.frames[0].frames[0].document.suppliesFrm;
			formObj.sbtForm.value = 'true';
			formObj.submit();
			document.getElementById('saveButton').style.display = 'inline-block';
			
		}
		else if(toDo=='Cancel'){
				
			document.getElementById('saveButton').style.display = 'inline-block';

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
					docObj.suppliesFrm.delRecords.value = 'true';
					docObj.suppliesFrm.submit();
				}
			}
		} 
		//Procedure Supplies
	
	}
	else if(frameSrc=='anes_ekg_admin.php'){
		if(toDo=='Save'){
			var formObj = top.frames[0].frames[0].document.frmSaveAnesEkgAdmin;
			formObj.submit();
			
		}
	}	
	<!-- LASER PROCEDURE -->
	else if(frameSrc=='facilityRegistration.php'){
	<!-- FACILITY REGISTERATION -->
		if(toDo=='Add New'){
			
			//top.frames[0].frames[0].document.getElementById('tdFrameUserRegistration').style.display = 'none';
			
			//top.frames[0].frames[0].document.getElementById('formTr').style.display = 'block';
			top.frames[0].frames[0].document.getElementById('userFrame').src= 'facilityRegistrationForm.php';
			/*top.frames[0].frames[0].$("canvas").each(function(){ 
				
			 });*/
			//alert(top.frames[0].frames[0].frames[0].document.forms[0].name);
			document.getElementById('addNew').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'none';
			document.getElementById('saveButton').style.display = 'inline-block';
			document.getElementById('cancelButton').style.display = 'inline-block';
			
			var innerObj = top.frames[0].frames[0].frames[0];
			
			
		}
		else if(toDo=='Delete'){
			
			var frameObj = top.frames[0].frames[0].frames[0];
			var boxesChk = false;
			obj = frameObj.document.getElementsByName("chkBox[]");
			objLength = frameObj.document.getElementsByName("chkBox[]").length;
			for(i=0; i<objLength; i++){
				if(obj[i].checked == true){
					var boxesChk = true;
				}
			}
			if(boxesChk!=true){
				alert("Please select Facility(s) to be deleted.")
			}else{
				var ask = confirm('Are you sure to delete the facility record(s).')
				if(ask==true){
					frameObj.document.listUsersFrm.submit();
				}
			}			
		}
		else if(toDo=='Save'){
			var objFrm = top.frames[0].frames[0].frames[0].document.frmFacilityRegistration;

			var msg="Please fill in the following:- \n";
			var flag = 0;
			var f1 = objFrm.sergeryCenerName.value;	
			var f2 = objFrm.idoc_facility.value;
			
			if(f1==''){ msg = msg+"\t Name\n"; ++flag; }
			if(f2==''){ msg = msg+"\t iASC Facility\n"; ++flag; }
			
			if(flag > 0){
				alert(msg);
				return false;	
			}
			
			objFrm.submit();
			document.getElementById('addNew').style.display = 'inline-block';		
			document.getElementById('deleteSelected').style.display = 'inline-block';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('cancelButton').style.display = 'none';
			return true;
		
		}
		else if(toDo=='Cancel'){	
			document.iframeMain.location.href = frameSrc;
			document.getElementById('addNew').style.display = 'inline-block';
			document.getElementById('cancelButton').style.display = 'none';
			document.getElementById('saveButton').style.display = 'none';
			document.getElementById('deleteSelected').style.display = 'inline-block';
		}
		
		
	<!-- USER REGISTERATION -->
	}
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

$(document).ready(function() {
	
	if(top.document.getElementById('crsBtnId')) {
		top.document.getElementById('crsBtnId').style.display = 'inline-block';	
	}
	var hidTabObj = top.document.getElementById('hidTab');
	
	if(hidTabObj) {
		var hidTabObjVal = hidTabObj.value;
		var adminTabFile = "surgeryCenter.php";
		var adminTabNum = "1";
		
		if(hidTabObjVal=="admin_asc") {
			adminTabFile = 'surgeryCenter.php';
			adminTabNum = "1";
		}else if(hidTabObjVal=="admin_users") {
			adminTabFile = 'userRegistration.php';
			adminTabNum = "2";
			
		}else if(hidTabObjVal=="admin_pre_op_med_order") {
			adminTabFile = 'preOpMediOrder.php';
			adminTabNum = "3";
		}
		else if(hidTabObjVal=="surgeon_profile") {
			adminTabFile = 'surgeonprofile.php';
			adminTabNum = "4";
		}
		else if(hidTabObjVal=="addSurgeonProfile") {
			adminTabFile = 'addSurgeonProfile.php';
			adminTabNum = "4";
		}
		else if(hidTabObjVal=="pre-define") {
			adminTabFile = 'predefineManager.php';
			adminTabNum = "5";
		}
		else if(hidTabObjVal=="op-report") {
			adminTabFile = 'operativeReportSurgeon.php';
			adminTabNum = "6";
		}
		else if(hidTabObjVal=="instruct-sheet") {
			adminTabFile = 'instructionSheet.php';
			adminTabNum = "7";
		}
		else if(hidTabObjVal=="consentFormMultiple") {
			adminTabFile = 'consentFormMultiple.php';
			adminTabNum = "8";
		}
		else if(hidTabObjVal=="anesthesia_profile") {
			adminTabFile = 'anesthesia_profile.php';
			adminTabNum = "9";
		}
		else if(hidTabObjVal=="nurseQuestion") {
			adminTabFile = 'nurseQuestion.php';
			adminTabNum = "10";
		}
		else if(hidTabObjVal=="laser_procedure_admin") {
			adminTabFile = 'laser_procedure_admin.php';
			adminTabNum = "11";
		}
		else if(hidTabObjVal=="anes_ekg_admin") {
			adminTabFile = 'anes_ekg_admin.php';
			adminTabNum = "12";
		}
		else if(hidTabObjVal=="Anesthesia") {
			var sessLogUserId = '<?php echo $loginUser;?>';
			adminTabFile = "anesthesia_profile.php?anesthesiologistList="+sessLogUserId;
			adminTabNum = "1";
		}
		else if(hidTabObjVal=="supplies") {
			adminTabFile = 'supplies.php';
			adminTabNum = "13";
		}
		else if(hidTabObjVal=="procedureprofile") {
			adminTabFile = 'procedureprofile.php';
			adminTabNum = "14";
		}
		else if(hidTabObjVal=="facility_list") {
			adminTabFile = 'facilityRegistration.php';
			adminTabNum = "15";
		}
		else if(hidTabObjVal=="injectionMisc") {
			adminTabFile = 'injection_misc.php';
			adminTabNum = "16";
		}
		else if(hidTabObjVal=="nurse_profile") {
			adminTabFile = "nurse_profile.php";
			adminTabNum = "17";
		}
		//addSurgeonProfile
		document.frameSrc.source.value = adminTabFile;
		frameSrc(adminTabFile, adminTabNum);
	}
});

var LOD	=	function()
{
		var BH = $(window).height();		//parent.top.$("#iframeHome").attr("height");
		BH		=	BH - $("#div_innr_btn").outerHeight(true)  ;
		//console.log('Inserted BH :' + BH)
		
		$("#iframeMain").attr('height',BH );
};
	
$(window).load(function()
{	LOD(); });
$(window).resize(function(){
	LOD();
});




</script>
</head>
<body style="margin:0px; overflow-y:hidden;" onLoad="MM_preloadImages('../images/add_new_hover.gif','../images/save_hover1.jpg','../images/new_user_hover.gif','../images/delete_selected_hover.gif','../images/cancel_hover1.jpg')">
<form name="frameSrc">
  <input type="hidden" name="source" id="source">
</form>
<div class="alert alert-success alert-msg " id="alert_success" > <strong>Record(s) Saved Successfully</strong> </div>
<div class="alert alert-success alert-msg " id="alert_delete" > <strong id="alert_del">Record Deleted</strong></div>
<div class="alert alert-danger alert-msg " id="alert_error" > <strong >Error !</strong> Something went wrong.....try again please </div>

<div >
  <div id="div_ovrflow" class="wrap_inside_admin inner_iframe" > 
    <!-- style="height:<?php echo $_SESSION['winHght']-100; ?>px;" -->
    <iframe class="padding_0 inner_iframe embed-responsive-item " scrolling="no" id="iframeMain" name="iframeMain" src="surgeryCenter.php"  width="100%" frameborder="0" ></iframe>
  </div>
</div>
<div id="div_innr_btn" class="btn-footer-slider shadow_adjust_above" style="position:static; bottom:0;"> 
	<a href="javascript:void(0)" class="btn btn-info" id="backButton" style="display:none;" onClick="return getPageSrc('Back');"> Back</a> 
    <a href="javascript:void(0)" class="btn btn-primary" data-hover="Import Supplies" id="importSupplies" style="display:none;" onClick="return getPageSrc('Import Supplies');"> Import Supplies</a> 
    <a href="javascript:void(0)" class="btn btn-primary" id="addNew" style="display:none;" onClick="return getPageSrc('Add New');"> <b class="fa fa-plus"></b> Add New</a> 
    <a href="javascript:void(0)" class="btn btn-info " id="newUser" style="display:none;" onClick="return getPageSrc('New');"> New User</a> 
    <a href="javascript:void(0)" class="btn btn-success" id="saveButton" style="display:none;" onClick="return getPageSrc('Save');"><b class="fa fa-save"></b> Save </a> 
    <a href="javascript:void(0)" class="btn btn-danger" id="deleteSelected" style="display:none;" onClick="return getPageSrc('Delete');"><b class="fa fa-trash"></b> Delete</a> 
    <a href="javascript:void(0)" class="btn btn-danger"	id="cancelButton"  	style="display:none;" onClick="return getPageSrc('Cancel');"><b class="fa fa-times"></b> Close</a> 
    <a href="javascript:void(0)" class="btn btn-info" id="categoryButton" style="display:none;" onClick="return getPageSrc('Category');"> Manage Category</a> 
    <a href="javascript:void(0)" class="btn btn-info" id="medicationButton" style="display:none;" onClick="return getPageSrc('Medication');"> Manage Medication</a> 
    <a href="javascript:void(0)" class="btn btn-info" id="formsButton" style="display:none;" onClick="return getPageSrc('Forms');"> Manage Forms</a> 
    <a href="javascript:void(0)" class="btn btn-info" id="optionsButton" style="display:none;" onClick="return getPageSrc('Options');"> Manage Options</a> 
    <a href="javascript:void(0)" class="btn btn-info" id="diagnosisButton" style="display:none;" onClick="return getPageSrc('Diagnosis');"> Manage Diagnosis</a>
    
    <a href="javascript:void(0)" class="btn btn-info" id="CopyFromCommunity" onClick="return getPageSrc('CopyFromCommunity');" style="display:none;">Copy From Community</a>
    <a href="javascript:void(0)" class="btn btn-info" id="CopyToCommunity" onClick="return getPageSrc('CopyToCommunity');" style="display:none;">Copy To Community</a>
     
    <a href="javascript:void(0)" class="btn btn-info" id="" style="visibility:hidden;">Hidden</a> </div>
<div class="push"></div>
<div id="admin_audit_report_id" style="display:none; margin-top:5px;"></div>
<!--</div>--> 

<script>
//SET ASC TAB AS DEFAULT SELECTED TAB
if(document.getElementById('Tab1')){
	document.getElementById('Tab1').style.background = "#003300";
	document.getElementById('Tab1Link').className = "white";
	document.getElementById('img1Left').src ="../images/leftDark.gif";
	document.getElementById('img1Right').src ="../images/rightDark.gif";
	document.getElementById('Tab1Link').click();
}
//SET ASC TAB AS DEFAULT SELECTED TAB

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