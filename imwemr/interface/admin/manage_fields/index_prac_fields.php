<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once("../admin_header.php");

if($_GET[demographics_mandatory] != ""){
$qry_update="update demographics_mandatory set heardAboutUs= '$_POST[heardAboutUs]', name= '$_POST[name]', address = '$_POST[address]', address_1 = '$_POST[address_1]', city = '$_POST[city]', 
state = '$_POST[state]', zip = '$_POST[zip]', eMail = '$_POST[eMail]', provider = '$_POST[provider]', homePhone = '$_POST[homePhone]', 
workPhone = '$_POST[workPhone]', mobilePhone = '$_POST[mobilePhone]', facility = '$_POST[facility]', drivingLicense = '$_POST[drivingLicense]',
ptPortalAccess = '$_POST[ptPortalAccess]', loginId = '$_POST[loginId]', password = '$_POST[password]', reEnterPassword = '$_POST[reEnterPassword]', 
pt_title = '$_POST[pt_title]', ptDOB = '$_POST[ptDOB]', ptMaritalStatus = '$_POST[ptMaritalStatus]', ptEmergencyContactName = '$_POST[ptEmergencyContactName]',
ptEmergencyPhone = '$_POST[ptEmergencyPhone]', ptCreatedBy = '$_POST[ptCreatedBy]', ptReferringPhysician = '$_POST[ptReferringPhysician]', 
ptSex = '$_POST[ptSex]', ptsocialSecurityNumber = '$_POST[ptsocialSecurityNumber]', rPName = '$_POST[rPName]', rPRelation = '$_POST[rPRelation]', rPAddress = '$_POST[rPAddress]', 
rPAddress_1 = '$_POST[rPAddress_1]', resCity = '$_POST[resCity]', resState = '$_POST[resState]', resZip = '$_POST[resZip]', rPDOB = '$_POST[rPDOB]', 
rPHomePhone = '$_POST[rPHomePhone]', rPWorkPhone = '$_POST[rPWorkPhone]', rPMobilePhone = '$_POST[rPMobilePhone]', 
rPScanDrivingLicense = '$_POST[rPScanDrivingLicense]', socialSec = '$_POST[socialSec]', rPSex = '$_POST[rPSex]',
rPMaritalStatus = '$_POST[rPMaritalStatus]', rPDrivingLicense = '$_POST[rPDrivingLicense]', rPCity = '$_POST[rPCity]', rPState = '$_POST[rPState]', 
rPCountry = '$_POST[rPCountry]', occupation = '$_POST[occupation]', employer = '$_POST[employer]', monthlyIncome = '$_POST[monthlyIncome]',
occCity = '$_POST[occCity]', occEmpAddress1 = '$_POST[occEmpAddress1]', occEmpAddress2 = '$_POST[occEmpAddress2]',
occZipCode = '$_POST[occZipCode]', occState = '$_POST[occState]', miscHippaChoices = '$_POST[miscHippaChoices]',
miscHippaAllowMail = '$_POST[miscHippaAllowMail]', miscHippaAllowVoiceMsg = '$_POST[miscHippaAllowVoiceMsg]', 
miscHippaUserDif1 = '$_POST[miscHippaUserDif1]', miscHippaUserDif2 = '$_POST[miscHippaUserDif2]', miscHippaLanguage = '$_POST[miscHippaLanguage]', 
miscHippaRace = '$_POST[miscHippaRace]', miscHippaEth = '$_POST[miscHippaEth]', miscHippaInterpreter = '$_POST[miscHippaInterpreter]', 
InsPriProvider = '$_POST[InsPriProvider]', InsPriPolicy = '$_POST[InsPriPolicy]', InsPriGroup = '$_POST[InsPriGroup]', InsPriPlanName = '$_POST[InsPriPlanName]', 
InsPriActDate = '$_POST[InsPriActDate]', InsPriExpDate = '$_POST[InsPriExpDate]', InsPriCoPay = '$_POST[InsPriCoPay]', InsPriRefReq = '$_POST[InsPriRefReq]', 
InsPriSubRelation = '$_POST[InsPriSubRelation]', InsPriInsName = '$_POST[InsPriInsName]', InsPriDOB = '$_POST[InsPriDOB]', InsPriSS = '$_POST[InsPriSS]', 
InsPriSex = '$_POST[InsPriSex]', InsPriPhone = '$_POST[InsPriPhone]', InsPriPymtAuth = '$_POST[InsPriPymtAuth]', InsPriSignFile = '$_POST[InsPriSignFile]', 
InsPriAddress = '$_POST[InsPriAddress]', InsPriZip = '$_POST[InsPriZip]', InsPriCity = '$_POST[InsPriCity]', InsPriSate = '$_POST[InsPriSate]', 
InsPriRefPhysician = '$_POST[InsPriRefPhysician]', InsPriRefReferral = '$_POST[InsPriRefReferral]', InsPriRefMode = '$_POST[InsPriRefMode]', 
InsPriRefRefDate = '$_POST[InsPriRefRefDate]', InsPriRefVisits = '$_POST[InsPriRefVisits]', InsPriRefStEffDate = '$_POST[InsPriRefStEffDate]', 
InsPriRefEndEffDate = '$_POST[InsPriRefEndEffDate]', InsPriRefNote = '$_POST[InsPriRefNote]', InsPriRefUpload = '$_POST[InsPriRefUpload]', 
InsSecProvider = '$_POST[InsSecProvider]', InsSecPolicy = '$_POST[InsSecPolicy]', InsSecGroup = '$_POST[InsSecGroup]', 
InsSecPlanName = '$_POST[InsSecPlanName]', InsSecActDate = '$_POST[InsSecActDate]', InsSecExpDate = '$_POST[InsSecExpDate]',
InsSecCoPay = '$_POST[InsSecCoPay]', InsSecRefReq = '$_POST[InsSecRefReq]', InsSecSubRelation = '$_POST[InsSecSubRelation]', 
InsSecInsName = '$_POST[InsSecInsName]', InsSecDOB = '$_POST[InsSecDOB]', InsSecSS = '$_POST[InsSecSS]', InsSecSex = '$_POST[InsSecSex]', 
InsSecPhone = '$_POST[InsSecPhone]', InsSecPymtAuth = '$_POST[InsSecPymtAuth]', InsSecSignFile = '$_POST[InsSecSignFile]', 
InsSecAddress = '$_POST[InsSecAddress]', InsSecZip = '$_POST[InsSecZip]', InsSecCity = '$_POST[InsSecCity]', InsSecSate = '$_POST[InsSecSate]', 
InsSecRefPhysician = '$_POST[InsSecRefPhysician]', InsSecRefReferral = '$_POST[InsSecRefReferral]', InsSecRefMode = '$_POST[InsSecRefMode]', 
InsSecRefRefDate = '$_POST[InsSecRefRefDate]', InsSecRefVisits = '$_POST[InsSecRefVisits]', InsSecRefStEffDate = '$_POST[InsSecRefStEffDate]', 
InsSecRefEndEffDate = '$_POST[InsSecRefEndEffDate]', InsSecRefNote = '$_POST[InsSecRefNote]', InsSecRefUpload = '$_POST[InsSecRefUpload]', 
InsTerProvider = '$_POST[InsTerProvider]', InsTerPolicy = '$_POST[InsTerPolicy]', InsTerGroup = '$_POST[InsTerGroup]', 
InsTerPlanName = '$_POST[InsTerPlanName]', InsTerActDate = '$_POST[InsTerActDate]', InsTerExpDate = '$_POST[InsTerExpDate]',
InsTerCoPay = '$_POST[InsTerCoPay]', InsTerRefReq = '$_POST[InsTerRefReq]', InsTerSubRelation = '$_POST[InsTerSubRelation]', 
InsTerInsName = '$_POST[InsTerInsName]', InsTerDOB = '$_POST[InsTerDOB]', InsTerSS = '$_POST[InsTerSS]', InsTerSex = '$_POST[InsTerSex]',
InsTerPhone = '$_POST[InsTerPhone]', InsTerPymtAuth = '$_POST[InsTerPymtAuth]', InsTerSignFile = '$_POST[InsTerSignFile]', 
InsTerAddress = '$_POST[InsTerAddress]', InsTerZip = '$_POST[InsTerZip]', InsTerCity = '$_POST[InsTerCity]', InsTerSate = '$_POST[InsTerSate]', 
InsTerRefPhysician = '$_POST[InsTerRefPhysician]', InsTerRefReferral = '$_POST[InsTerRefReferral]', InsTerRefMode = '$_POST[InsTerRefMode]', 
InsTerRefRefDate = '$_POST[InsTerRefRefDate]', InsTerRefVisits = '$_POST[InsTerRefVisits]', InsTerRefStEffDate = '$_POST[InsTerRefStEffDate]', 
InsTerRefEndEffDate = '$_POST[InsTerRefEndEffDate]', InsTerRefNote = '$_POST[InsTerRefNote]', InsTerRefUpload = '$_POST[InsTerRefUpload]', 
appt_name = '$_POST[appt_name]', appt_procedure = '$_POST[appt_procedure]', appt_date = '$_POST[appt_date]', appt_startTime = '$_POST[appt_startTime]',
appt_endTime = '$_POST[appt_endTime]', appt_faciltiy = '$_POST[appt_faciltiy]', appt_dob = '$_POST[appt_dob]', appt_street = '$_POST[appt_street]', 
appt_room = '$_POST[appt_room]', appt_workPhone = '$_POST[appt_workPhone]', appt_mobile = '$_POST[appt_mobile]', 
appt_homePhone = '$_POST[appt_homePhone]', appt_insCase = '$_POST[appt_insCase]'  where  demographics_mandatory ='$_GET[demographics_mandatory]'";
//getting perticuler Practice Mandatory after save update 
imw_query($qry_update) or die(imw_error());	
//if (imw_affected_rows()>0){
	echo "<script> top.alert_notification_show('Advisory  Fields Updated Successfully'); </script>"; 
//}
}
//if edit---	
if($intMandatoryId != "") {		
		$strQry = "SELECT * from demographics_mandatory WHERE demographics_mandatory = '".$intMandatoryId."'";
	}
	else{
		$strQry = "select * from demographics_mandatory ORDER BY demographics_mandatory limit 1";
	}
	$arrResult =imw_query($strQry);
	$row=imw_fetch_array($arrResult);
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_prac_fields.js"></script>
<body>
<div class="whtbox pdnon" id="pracfields" >
	<form><input type="hidden" name="preObjBack" id="preObjBack" value=""></form>
	<form name="demographicsMmandatoryFrm" action="index_prac_fields.php?action=savepracticemandatory&demographics_mandatory=<?php echo $row['demographics_mandatory'] ?>" method="post" style="margin:0px;">
	<div class="headinghd pd10 ">
		<h2>Patient Information</h2>
	</div>
	<div class="lightgray pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="patientInfoTable1">
				<table class="table table-bordered table-hover" style="background-color:#fff;">
				<tr class="lhtgrayhead">
					<th>Demographics</th>
					<th style="width:20%" class="text-center">Advisory</th>
					<th style="width:20%" class="text-center">Mandatory</th>
				</tr>
				<tr>
					<td>Heard about us</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" name="heardAboutUs" id="heardAboutUs1" value="1" <?php if($row['heardAboutUs']==1) { ?> checked="checked" <?php } ?>><label for="heardAboutUs1"></label></div>
					</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" name="heardAboutUs" id="heardAboutUs2" value="2"  <?php if($row['heardAboutUs']==2) { ?> checked="checked" <?php } ?> ><label for="heardAboutUs2"></label></div>
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" name="name" id="name1" value="1"  <?php if($row['name']==1) { ?> checked="checked" <?php } ?>><label for="name1"></label></div></td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" name="name" id="name2" value="2" <?php if($row['name']==2) { ?> checked="checked" <?php } ?>><label for="name2"></label></div></td>
				</tr>
				<tr>
					<td>Address</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" name="address" id="address1" value="1" <?php if($row['address']==1) { ?> checked="checked" <?php } ?> ><label for="address1"></label></div>
					</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" name="address" id="address2" value="2" <?php if($row['address']==2) { ?> checked="checked" <?php } ?> ><label for="address2"></label></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;Street 1</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" id="address_1" <?php if($row['address_1']==1) { ?> checked="checked" <?php } ?> name="address_1" value="1"><label for="address_1"></label></div>
					</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" id="address_2" <?php if($row['address_1']==2) { ?> checked="checked" <?php } ?> name="address_1" value="2"><label for="address_2"></label></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;City</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" id="city1" name="city"  <?php if($row['city']==1) { ?> checked="checked" <?php } ?>  value="1"><label for="city1"></label></div>
					</td>
					<td class="text-center">
						<div class="checkbox"><input type="checkbox" id="city2" name="city" value="2" <?php if($row['city']==2) { ?> checked="checked" <?php } ?>><label for="city2"></label></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;State</td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" id="state1" name="state" value="1" <?php if($row['state']==1) { ?> checked="checked" <?php } ?>><label for="state1"></label></div></td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" id="state2" name="state" value="2" <?php if($row['state']==2) { ?> checked="checked" <?php } ?>><label for="state2"></label></div></td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php getZipPostalLabel(); ?></td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" id="zip1" name="zip" value="1" <?php if($row['zip']==1) { ?> checked="checked" <?php } ?>><label for="zip1"></label></div></td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" id="zip2" name="zip" value="2"<?php if($row['zip']==2) { ?> checked="checked" <?php } ?>><label for="zip2"></label></div></td>
				</tr>	
				<tr>
					<td>EMail</td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" name="eMail" id="eMail1" value="1" <?php if($row['eMail']==1) { ?> checked="checked" <?php } ?>><label for="eMail1"></label></div></td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" name="eMail" id="eMail2" value="2" <?php if($row['eMail']==2) { ?> checked="checked" <?php } ?>><label for="eMail2"></label></div></td>
				</tr>
				<tr>
					<td>Provider</td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" name="provider" id="provider1" value="1" <?php if($row['provider']==1) { ?> checked="checked" <?php } ?>><label for="provider1"></label></div></td>
					<td class="text-center"><div class="checkbox"><input type="checkbox" name="provider" id="provider2" value="2" <?php if($row['provider']==2) { ?> checked="checked" <?php } ?>><label for="provider2"></label></div></td>
				</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="patientInfoTable2">
				<table class="table table-bordered table-hover" style="background-color:#fff;">
					<tr class="lhtgrayhead">
						<th>Demographics</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Home Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="homePhone" id="homePhone1" value="1" <?php if($row['homePhone']==1) { ?> checked="checked" <?php } ?>><label for="homePhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="homePhone" id="homePhone2" value="2" <?php if($row['homePhone']==2) { ?> checked="checked" <?php } ?>><label for="homePhone2"></label></div></td>
					</tr>
					<tr>
						<td>Work Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="workPhone" id="workPhone1" value="1" <?php if($row['workPhone']==1) { ?> checked="checked" <?php } ?>><label for="workPhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="workPhone" id="workPhone2" value="2"<?php if($row['workPhone']==2) { ?> checked="checked" <?php } ?>><label for="workPhone2"></label></div></td>
					</tr>
					<tr>
						<td>Mobile Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="mobilePhone"  id="mobilePhone1" value="1" <?php if($row['mobilePhone']==1) { ?> checked="checked" <?php } ?>><label for="mobilePhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="mobilePhone"  id="mobilePhone2" value="2" <?php if($row['mobilePhone']==2) { ?> checked="checked" <?php } ?>><label for="mobilePhone2"></label></div></td>
					</tr>
					<tr>
						<td>Facility</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="facility" id="facility1" value="1"<?php if($row['facility']==1) { ?> checked="checked" <?php } ?>><label for="facility1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="facility" id="facility2" value="2"<?php if($row['facility']==2) { ?> checked="checked" <?php } ?>><label for="facility2"></label></div></td>
					</tr>
					<tr>
						<td>Driving License</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="drivingLicense" id="drivingLicense1" value="1" <?php if($row['drivingLicense']==1) { ?> checked="checked" <?php } ?>><label for="drivingLicense1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="drivingLicense" id="drivingLicense2" value="2"<?php if($row['drivingLicense']==2) { ?> checked="checked" <?php } ?>><label for="drivingLicense2"></label></div></td>
					</tr>
					<tr>
						<td>Pt. Portal Access</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptPortalAccess"  id="ptPortalAccess1" value="1" <?php if($row['ptPortalAccess']==1) { ?> checked="checked" <?php } ?>><label for="ptPortalAccess1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptPortalAccess"  id="ptPortalAccess2" value="2"  <?php if($row['ptPortalAccess']==2) { ?> checked="checked" <?php } ?>><label for="ptPortalAccess2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;Login id</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="loginId1" name="loginId" value="1"<?php if($row['loginId']==1) { ?> checked="checked" <?php } ?>><label for="loginId1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="loginId2" name="loginId" value="2"<?php if($row['loginId']==2) { ?> checked="checked" <?php } ?>><label for="loginId2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;Password</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="password1" name="password" value="1"<?php if($row['password']==1) { ?> checked="checked" <?php } ?>><label for="password1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="password2" name="password" value="2" <?php if($row['password']==2) { ?> checked="checked" <?php } ?>><label for="password2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;Reenter Password</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="reenterPassword1" name="reEnterPassword" value="1"<?php if($row['reEnterPassword']==1) { ?> checked="checked" <?php } ?>><label for="reenterPassword1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="reenterPassword2" name="reEnterPassword" value="2"<?php if($row['reEnterPassword']==2) { ?> checked="checked" <?php } ?>><label for="reenterPassword2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="patientInfoTable3">
				<table class="table table-bordered table-hover" style="background-color:#fff;">
					<tr class="lhtgrayhead">
						<th>Demographics</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Title</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="pt_title" id="pt_title1" value="1" <?php if($row['pt_title']==1) { ?> checked="checked" <?php } ?>><label for="pt_title1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="pt_title" id="pt_title2" value="2" <?php if($row['pt_title']==2) { ?> checked="checked" <?php } ?>><label for="pt_title2"></label></div></td>
					</tr>
					<tr>
						<td>DOB</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptDOB" id="ptDOB1" value="1" <?php if($row['ptDOB']==1) { ?> checked="checked" <?php } ?>><label for="ptDOB1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptDOB" id="ptDOB2" value="2" <?php if($row['ptDOB']==2) { ?> checked="checked" <?php } ?>><label for="ptDOB2"></label></div></td>
					</tr>
					<tr>
						<td>Marital Status</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptMaritalStatus" id="ptMaritalStatus1" value="1" <?php if($row['ptMaritalStatus']==1) { ?> checked="checked" <?php } ?>><label for="ptMaritalStatus1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptMaritalStatus" id="ptMaritalStatus2" value="2" <?php if($row['ptMaritalStatus']==2) { ?> checked="checked" <?php } ?>><label for="ptMaritalStatus2"></label></div></td>
					</tr>
					<tr>
						<td>Emergency Contact Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptEmergencyContactName" id="ptEmergencyContactName1" value="1"<?php if($row['ptEmergencyContactName']==1) { ?> checked="checked" <?php } ?>><label for="ptEmergencyContactName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptEmergencyContactName" id="ptEmergencyContactName2" value="2" <?php if($row['ptEmergencyContactName']==2) { ?> checked="checked" <?php } ?>><label for="ptEmergencyContactName2"></label></div></td>
					</tr>
					<tr>
						<td>Emergency Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptEmergencyPhone" id="ptEmergencyPhone1" value="1" <?php if($row['ptEmergencyPhone']==1) { ?> checked="checked" <?php } ?>><label for="ptEmergencyPhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptEmergencyPhone" id="ptEmergencyPhone2" value="2" <?php if($row['ptEmergencyPhone']==2) { ?> checked="checked" <?php } ?>><label for="ptEmergencyPhone2"></label></div></td>
					</tr>
					<tr>
						<td>Created By</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptCreatedBy" id="ptCreatedBy1" value="1" <?php if($row['ptCreatedBy']==1) { ?> checked="checked" <?php } ?>><label for="ptCreatedBy1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptCreatedBy" id="ptCreatedBy2" value="2" <?php if($row['ptCreatedBy']==2) { ?> checked="checked" <?php } ?>><label for="ptCreatedBy2"></label></div></td>
					</tr>
					<tr>
						<td>Referring Physician</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptReferringPhysician" id="ptReferringPhysician1" value="1" <?php if($row['ptReferringPhysician']==1) { ?> checked="checked" <?php } ?>><label for="ptReferringPhysician1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptReferringPhysician" id="ptReferringPhysician2" value="2" <?php if($row['ptReferringPhysician']==2) { ?> checked="checked" <?php } ?>><label for="ptReferringPhysician2"></label></div></td>
					</tr>
					<tr>
						<td>Sex</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptSex" id="ptSex1" value="1" <?php if($row['ptSex']==1) { ?> checked="checked" <?php } ?>><label for="ptSex1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptSex" id="ptSex2" value="2" <?php if($row['ptSex']==2) { ?> checked="checked" <?php } ?>><label for="ptSex2"></label></div></td>
					</tr>
					<tr>
						<td>Social Security Number</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptsocialSecurityNumber" id="ptsocialSecurityNumber1"  value="1" <?php if($row['ptsocialSecurityNumber']==1) { ?> checked="checked" <?php } ?>><label for="ptsocialSecurityNumber1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="ptsocialSecurityNumber" id="ptsocialSecurityNumber2"  value="2" <?php if($row['ptsocialSecurityNumber']==2) { ?> checked="checked" <?php } ?>><label for="ptsocialSecurityNumber2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10">
		<h2>Responsible Party</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="respPartyTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Responsible Party</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPName" id="rPName1" value="1" <?php if($row['rPName']==1) { ?> checked="checked" <?php } ?>><label for="rPName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPName" id="rPName2" value="2" <?php if($row['rPName']==2) { ?> checked="checked" <?php } ?>><label for="rPName2"></label></div></td>
					</tr>
					<tr>
						<td>Relation</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPRelation" id="rPRelation1" value="1" <?php if($row['rPRelation']==1) { ?> checked="checked" <?php } ?>><label for="rPRelation1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPRelation" id="rPRelation2" value="2" <?php if($row['rPRelation']==2) { ?> checked="checked" <?php } ?>><label for="rPRelation2"></label></div></td>
					</tr>
					<tr>
						<td>Address</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPAddress" id="rPAddress1" value="1" <?php if($row['rPAddress']==1) { ?> checked="checked" <?php } ?> ><label for="rPAddress1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPAddress" id="rPAddress2" value="2" <?php if($row['rPAddress']==2) { ?> checked="checked" <?php } ?> ><label for="rPAddress2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;Street 1</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="rPAddress_1" name="rPAddress_1" value="1" <?php if($row['rPAddress_1']==1) { ?> checked="checked" <?php } ?> ><label for="rPAddress_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="rPAddress_2" name="rPAddress_1" value="2"<?php if($row['rPAddress_1']==2) { ?> checked="checked" <?php } ?> ><label for="rPAddress_2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;City</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="resCity1" name="resCity" value="1"<?php if($row['resCity']==1) { ?> checked="checked" <?php } ?>><label for="resCity1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="resCity2" name="resCity" value="2" <?php if($row['resCity']==2) { ?> checked="checked" <?php } ?>><label for="resCity2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;State</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="resState1" name="resState" value="1"<?php if($row['resState']==1) { ?> checked="checked" <?php } ?>><label for="resState1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="resState2" name="resState" value="2"<?php if($row['resState']==2) { ?> checked="checked" <?php } ?>><label for="resState2"></label></div></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;<?php getZipPostalLabel(); ?></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="resZip1" name="resZip" value="1" <?php if($row['resZip']==1) { ?> checked="checked" <?php } ?> ><label for="resZip1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="resZip2" name="resZip" value="2" <?php if($row['resZip']==2) { ?> checked="checked" <?php } ?>><label for="resZip2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="respPartyTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Responsible Party</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>DOB</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPDOB" id="rPDOB1" value="1" <?php if($row['rPDOB']==1) { ?> checked="checked" <?php } ?>><label for="rPDOB1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPDOB" id="rPDOB2" value="2"<?php if($row['rPDOB']==2) { ?> checked="checked" <?php } ?>><label for="rPDOB2"></label></div></td>
					</tr>
					<tr>
						<td>Home Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPHomePhone" id="rPHomePhone1" value="1"<?php if($row['rPHomePhone']==1) { ?> checked="checked" <?php } ?>><label for="rPHomePhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPHomePhone" id="rPHomePhone2" value="2"<?php if($row['rPHomePhone']==2) { ?> checked="checked" <?php } ?>><label for="rPHomePhone2"></label></div></td>
					</tr>
					<tr>
						<td>Work Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPWorkPhone" id="rPWorkPhone1" value="1" <?php if($row['rPWorkPhone']==1) { ?> checked="checked" <?php } ?>><label for="rPWorkPhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPWorkPhone" id="rPWorkPhone2" value="2" <?php if($row['rPWorkPhone']==2) { ?> checked="checked" <?php } ?>><label for="rPWorkPhone2"></label></div></td>
					</tr>
					<tr>
						<td>Mobile Phone#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPMobilePhone" id="rPMobilePhone1" value="1" <?php if($row['rPMobilePhone']==1) { ?> checked="checked" <?php } ?>><label for="rPMobilePhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPMobilePhone" id="rPMobilePhone2" value="2" <?php if($row['rPMobilePhone']==2) { ?> checked="checked" <?php } ?>><label for="rPMobilePhone2"></label></div></td>
					</tr>
					<tr>
						<td>Scan Driving License</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPScanDrivingLicense" id="rPScanDrivingLicense1" value="1"<?php if($row['rPScanDrivingLicense']==1) { ?> checked="checked" <?php } ?>><label for="rPScanDrivingLicense1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPScanDrivingLicense" id="rPScanDrivingLicense2" value="2" <?php if($row['rPScanDrivingLicense']==2) { ?> checked="checked" <?php } ?>><label for="rPScanDrivingLicense2"></label></div></td>
					</tr>
					<tr>
						<td>SS</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="socialSec" id="socialSec1" value="1" <?php if($row['socialSec']==1) { ?> checked="checked" <?php } ?>><label for="socialSec1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="socialSec" id="socialSec2" value="2" <?php if($row['socialSec']==2) { ?> checked="checked" <?php } ?>><label for="socialSec2"></label></div></td>
					</tr>
					<tr>
						<td>Sex</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPSex" id="rPSex1" value="1"<?php if($row['rPSex']==1) { ?> checked="checked" <?php } ?>><label for="rPSex1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPSex" id="rPSex2" value="2"<?php if($row['rPSex']==2) { ?> checked="checked" <?php } ?>><label for="rPSex2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="respPartyTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Responsible Party</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Marital Status</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPMaritalStatus" id="rPMaritalStatus1" value="1" <?php if($row['rPMaritalStatus']==1) { ?> checked="checked" <?php } ?>><label for="rPMaritalStatus1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPMaritalStatus" id="rPMaritalStatus2" value="2" <?php if($row['rPMaritalStatus']==2) { ?> checked="checked" <?php } ?>><label for="rPMaritalStatus2"></label></div></td>
					</tr>
					<tr>
						<td>Driving License</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPDrivingLicense" id="rPDrivingLicense1" value="1" <?php if($row['rPDrivingLicense']==1) { ?> checked="checked" <?php } ?> ><label for="rPDrivingLicense1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPDrivingLicense" id="rPDrivingLicense2" value="2"<?php if($row['rPDrivingLicense']==2) { ?> checked="checked" <?php } ?>><label for="rPDrivingLicense2"></label></div></td>
					</tr>
					<tr>
						<td>City</td> 
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPCity" id="rPCity1" value="1"<?php if($row['rPCity']==1) { ?> checked="checked" <?php } ?>><label for="rPCity1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPCity" id="rPCity2" value="2" <?php if($row['rPCity']==2) { ?> checked="checked" <?php } ?>><label for="rPCity2"></label></div></td>
					</tr>
					<tr>
						<td>State</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPState" id="rPState1"  value="1"<?php if($row['rPState']==1) { ?> checked="checked" <?php } ?>><label for="rPState1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPState" id="rPState2"  value="2"<?php if($row['rPState']==2) { ?> checked="checked" <?php } ?>><label for="rPState2"></label></div></td>
					</tr>
					<tr>
						<td>Country</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPCountry" id="rPCountry1" value="1" <?php if($row['rPCountry']==1) { ?> checked="checked" <?php } ?>><label for="rPCountry1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="rPCountry" id="rPCountry2" value="2" <?php if($row['rPCountry']==2) { ?> checked="checked" <?php } ?>><label for="rPCountry2"></label></div></td>
					</tr> 
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Occupation</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-6 ocupatbox1">
			<div class="table-responsive respotable" id="occupationTable1 ">
				<table class="table">
					<tr class="">
						<th>Occupation</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Occupation</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occupation"  id="occupation1" value="1" <?php if($row['occupation']==1) { ?> checked="checked" <?php } ?>><label for="occupation1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occupation"  id="occupation2" value="2" <?php if($row['occupation']==2) { ?> checked="checked" <?php } ?>><label for="occupation2"></label></div></td>
					</tr>
					<tr>
						<td>Employer</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="employer" id="employer1" value="1" <?php if($row['employer']==1) { ?> checked="checked" <?php } ?>><label for="employer1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="employer" id="employer2" value="2" <?php if($row['employer']==2) { ?> checked="checked" <?php } ?>><label for="employer2"></label></div></td>
					</tr>
					<tr>
						<td>Monthly Income</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="monthlyIncome" id="monthlyIncome1" value="1" <?php if($row['monthlyIncome']==1) { ?> checked="checked" <?php } ?>><label for="monthlyIncome1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="monthlyIncome" id="monthlyIncome2" value="2" <?php if($row['monthlyIncome']==2) { ?> checked="checked" <?php } ?>><label for="monthlyIncome2"></label></div></td>
					</tr>
					<tr>
						<td>City</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occCity" id="occCity1" value="1" <?php if($row['occCity']==1) { ?> checked="checked" <?php } ?>><label for="occCity1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occCity" id="occCity2" value="2" <?php if($row['occCity']==2) { ?> checked="checked" <?php } ?>><label for="occCity2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-6 ocupatbox2">
			<div class="table-responsive respotable" id="occupationTable2">
				<table class="table">
					<tr >
						<th>Occupation</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Employer Street 1</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occEmpAddress1" id="occEmpAddress1_1" value="1" <?php if($row['occEmpAddress1']==1) { ?> checked="checked" <?php } ?>><label for="occEmpAddress1_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occEmpAddress1" id="occEmpAddress1_2" value="2" <?php if($row['occEmpAddress1']==2) { ?> checked="checked" <?php } ?>><label for="occEmpAddress1_2"></label></div></td>
					</tr>
					<tr>
						<td>Employer Street 1</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occEmpAddress2" id="occEmpAddress2_1" value="1" <?php if($row['occEmpAddress2']==1) { ?> checked="checked" <?php } ?>><label for="occEmpAddress2_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occEmpAddress2" id="occEmpAddress2_2" value="2" <?php if($row['occEmpAddress2']==2) { ?> checked="checked" <?php } ?>><label for="occEmpAddress2_2"></label></div></td>
					</tr>
					<tr>
						<td><?php getZipPostalLabel(); ?></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occZipCode" id="occZipCode1" value="1" <?php if($row['occZipCode']==1) { ?> checked="checked" <?php } ?>><label for="occZipCode1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occZipCode" id="occZipCode2" value="2" <?php if($row['occZipCode']==2) { ?> checked="checked" <?php } ?>><label for="occZipCode2"></label></div></td>
					</tr>
					<tr>
						<td>State</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occState" id="occState1" value="1" <?php if($row['occState']==1) { ?> checked="checked" <?php } ?>><label for="occState1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="occState" id="occState2" value="2" <?php if($row['occState']==2) { ?> checked="checked" <?php } ?>><label for="occState2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10">
		<h2>Miscellaneous</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="miscellaneousTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Miscellaneous</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>HIPAA Choices</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaChoices" id="miscHippaChoices1" value="1" <?php if($row['miscHippaChoices']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaChoices1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaChoices" id="miscHippaChoices2" value="2" <?php if($row['miscHippaChoices']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaChoices2"></label></div></td>
					</tr>
					<tr>
						<td>Allow Mail</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaAllowMail" id="miscHippaAllowMail1" value="1" <?php if($row['miscHippaAllowMail']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaAllowMail1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaAllowMail" id="miscHippaAllowMail2" value="2" <?php if($row['miscHippaAllowMail']==2) { ?> checked="checked" <?php } ?> ><label for="miscHippaAllowMail2"></label></div></td>
					</tr>
					<tr>
						<td>Allow Voice Msg.</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaAllowVoiceMsg" id="miscHippaAllowVoiceMsg1" value="1" <?php if($row['miscHippaAllowVoiceMsg']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaAllowVoiceMsg1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaAllowVoiceMsg" id="miscHippaAllowVoiceMsg2" value="2" <?php if($row['miscHippaAllowVoiceMsg']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaAllowVoiceMsg2"></label></div></td>
					</tr>								
					<tr>
						<td>User Defined 1</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaUserDif1" id="miscHippaUserDif1_1" value="1"  <?php if($row['miscHippaUserDif1']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaUserDif1_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaUserDif1" id="miscHippaUserDif1_2" value="2" <?php if($row['miscHippaUserDif1']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaUserDif1_2"></label></div></td>
					</tr>	
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="miscellaneousTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Miscellaneous</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
									<td>User Defined 2</td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaUserDif2" id="miscHippaUserDif2_1" value="1"  <?php if($row['miscHippaUserDif2']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaUserDif2_1"></label></div></td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaUserDif2" id="miscHippaUserDif2_2" value="2" <?php if($row['miscHippaUserDif2']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaUserDif2_2"></label></div></td>
								</tr>
								<tr>
									<td>Language</td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaLanguage" id="miscHippaLanguage1" value="1" <?php if($row['miscHippaLanguage']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaLanguage1"></label></div></td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaLanguage" id="miscHippaLanguage2" value="2" <?php if($row['miscHippaLanguage']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaLanguage2"></label></div></td>
								</tr>
								<tr>
									<td>Race</td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaRace" id="miscHippaRace1" value="1" <?php if($row['miscHippaRace']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaRace1"></label></div></td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaRace" id="miscHippaRace2"  value="2" <?php if($row['miscHippaRace']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaRace2"></label></div></td>
								</tr>
								<tr >
									<td>Ethnicity</td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaEth" id="miscHippaEth1" value="1" <?php if($row['miscHippaEth']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaEth1"></label></div></td>
									<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaEth" id="miscHippaEth2" value="2" <?php if($row['miscHippaEth']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaEth2"></label></div></td>
								</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="miscellaneousTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Miscellaneous</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Interpreter</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaInterpreter" id="miscHippaInterpreter1" value="1" <?php if($row['miscHippaInterpreter']==1) { ?> checked="checked" <?php } ?>><label for="miscHippaInterpreter1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="miscHippaInterpreter" id="miscHippaInterpreter2" value="2" <?php if($row['miscHippaInterpreter']==2) { ?> checked="checked" <?php } ?>><label for="miscHippaInterpreter2"></label></div></td>			
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Insurance(Primary)</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsurancePriTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Ins. Provider</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriProvider" id="InsPriProvider1" value="1" <?php if($row['InsPriProvider']==1) { ?> checked="checked" <?php } ?>><label for="InsPriProvider1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriProvider" id="InsPriProvider2" value="2" <?php if($row['InsPriProvider']==2) { ?> checked="checked" <?php } ?>><label for="InsPriProvider2"></label></div></td>
					</tr>
					<tr>
					<td>Policy#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPolicy" id="InsPriPolicy1" value="1" <?php if($row['InsPriPolicy']==1) { ?> checked="checked" <?php } ?>><label for="InsPriPolicy1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPolicy" id="InsPriPolicy2" value="2" <?php if($row['InsPriPolicy']==2) { ?> checked="checked" <?php } ?>><label for="InsPriPolicy2"></label></div></td>
					</tr>
					<tr>
						<td>Group#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriGroup" id="InsPriGroup1" value="1" <?php if($row['InsPriGroup']==1) { ?> checked="checked" <?php } ?>><label for="InsPriGroup1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriGroup" id="InsPriGroup2" value="2" <?php if($row['InsPriGroup']==2) { ?> checked="checked" <?php } ?>><label for="InsPriGroup2"></label></div></td>
					</tr>
					<tr>
						<td>Plan Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPlanName" id="InsPriPlanName1" value="1" <?php if($row['InsPriPlanName']==1) { ?> checked="checked" <?php } ?> ><label for="InsPriPlanName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPlanName" id="InsPriPlanName2" value="2" <?php if($row['InsPriPlanName']==2) { ?> checked="checked" <?php } ?>><label for="InsPriPlanName2"></label></div></td>
					</tr>
					<tr>
						<td>Act. date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriActDate" id="InsPriActDate1" value="1" <?php if($row['InsPriActDate']==1) { ?> checked="checked" <?php } ?>><label for="InsPriActDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriActDate" id="InsPriActDate2" value="2"  <?php if($row['InsPriActDate']==2) { ?> checked="checked" <?php } ?>><label for="InsPriActDate2"></label></div></td>
					</tr>
					<tr>
						<td>Exp. date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriExpDate" id="InsPriExpDate1" value="1" <?php if($row['InsPriExpDate']==1) { ?> checked="checked" <?php } ?>><label for="InsPriExpDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriExpDate" id="InsPriExpDate2" value="2" <?php if($row['InsPriExpDate']==2) { ?> checked="checked" <?php } ?>><label for="InsPriExpDate2"></label></div></td>
					</tr>
					<tr>
						<td>CoPay</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriCoPay" id="InsPriCoPay1" value="1" <?php if($row['InsPriCoPay']==1) { ?> checked="checked" <?php } ?>><label for="InsPriCoPay1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriCoPay" id="InsPriCoPay2" value="2" <?php if($row['InsPriCoPay']==2) { ?> checked="checked" <?php } ?>><label for="InsPriCoPay2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsurancePriTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Refer Req</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefReq" id="InsPriRefReq1" value="1" <?php if($row['InsPriRefReq']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefReq1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefReq"  id="InsPriRefReq2" value="2" <?php if($row['InsPriRefReq']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefReq2"></label></div></td>
					</tr>
					<tr>
						<td>Sub. Relation</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSubRelation" id="InsPriSubRelation1" value="1" <?php if($row['InsPriSubRelation']==1) { ?> checked="checked" <?php } ?>><label for="InsPriSubRelation1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSubRelation" id="InsPriSubRelation2" value="2" <?php if($row['InsPriSubRelation']==2) { ?> checked="checked" <?php } ?>><label for="InsPriSubRelation2"></label></div></td>
					</tr>
					<tr>
						<td>Ins.Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriInsName" id="InsPriInsName1" value="1" <?php if($row['InsPriInsName']==1) { ?> checked="checked" <?php } ?>><label for="InsPriInsName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriInsName" id="InsPriInsName2" value="2" <?php if($row['InsPriInsName']==2) { ?> checked="checked" <?php } ?>><label for="InsPriInsName2"></label></div></td>
					</tr>
					<tr>
						<td>D.O.B</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriDOB" id="InsPriDOB1" value="1" <?php if($row['InsPriDOB']==1) { ?> checked="checked" <?php } ?>><label for="InsPriDOB1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriDOB" id="InsPriDOB2" value="2"<?php if($row['InsPriDOB']==2) { ?> checked="checked" <?php } ?> ><label for="InsPriDOB2"></label></div></td>
					</tr>
					<tr>
						<td>S.S#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSS" id="InsPriSS1" value="1" <?php if($row['InsPriSS']==1) { ?> checked="checked" <?php } ?>><label for="InsPriSS1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSS" id="InsPriSS2" value="2" <?php if($row['InsPriSS']==2) { ?> checked="checked" <?php } ?>><label for="InsPriSS2"></label></div></td>
					</tr>
					<tr>
						<td>Sex</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSex" id="InsPriSex1" value="1" <?php if($row['InsPriSex']==1) { ?> checked="checked" <?php } ?> ><label for="InsPriSex1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSex" id="InsPriSex2" value="2" <?php if($row['InsPriSex']==2) { ?> checked="checked" <?php } ?>><label for="InsPriSex2"></label></div></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPhone" id="InsPriPhone1" value="1" <?php if($row['InsPriPhone']==1) { ?> checked="checked" <?php } ?> ><label for="InsPriPhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPhone" id="InsPriPhone2" value="2" <?php if($row['InsPriPhone']==2) { ?> checked="checked" <?php } ?>><label for="InsPriPhone2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsurancePriTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Pymt. Auth</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPymtAuth" id="InsPriPymtAuth1" value="1" <?php if($row['InsPriPymtAuth']==1) { ?> checked="checked" <?php } ?>><label for="InsPriPymtAuth1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriPymtAuth" id="InsPriPymtAuth2" value="2" <?php if($row['InsPriPymtAuth']==2) { ?> checked="checked" <?php } ?>><label for="InsPriPymtAuth2"></label></div></td>
					</tr>
					<tr>
						<td>Sign. on File</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSignFile" id="InsPriSignFile1" value="1" <?php if($row['InsPriSignFile']==1) { ?> checked="checked" <?php } ?>><label for="InsPriSignFile1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSignFile" id="InsPriSignFile2" value="2" <?php if($row['InsPriSignFile']==2) { ?> checked="checked" <?php } ?>><label for="InsPriSignFile2"></label></div></td>
					</tr>
					<tr>
						<td>Address</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriAddress" id="InsPriAddressId_1" value="1" onClick="insurenceSelAddMark(this);" <?php if($row['InsPriAddress']==1) { ?> checked="checked" <?php } ?> data-type="Pri"><label for="InsPriAddressId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriAddress" id="InsPriAddressId_2" value="2" onClick="insurenceSelAddMark(this);" <?php if($row['InsPriAddress']==2) { ?> checked="checked" <?php } ?> data-type="Pri"><label for="InsPriAddressId_2"></label></div></td>
					</tr>
					<tr>
						<td><?php getZipPostalLabel(); ?></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriZip" id="InsPriZipId_1" value="1" <?php if($row['InsPriZip']==1) { ?> checked="checked" <?php } ?>><label for="InsPriZipId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriZip" id="InsPriZipId_2" value="2" <?php if($row['InsPriZip']==2) { ?> checked="checked" <?php } ?>><label for="InsPriZipId_2"></label></div></td>
					</tr>
					<tr>
						<td>City</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriCity" id="InsPriCityId_1" value="1" <?php if($row['InsPriCity']==1) { ?> checked="checked" <?php } ?>><label for="InsPriCityId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriCity" id="InsPriCityId_2" value="2"<?php if($row['InsPriCity']==2) { ?> checked="checked" <?php } ?>><label for="InsPriCityId_2"></label></div></td>
					</tr>
					<tr>
						<td>State</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSate" id="InsPriSateId_1" value="1" <?php if($row['InsPriSate']==1) { ?> checked="checked" <?php } ?>><label for="InsPriSateId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriSate" id="InsPriSateId_2" value="2" <?php if($row['InsPriSate']==2) { ?> checked="checked" <?php } ?>><label for="InsPriSateId_2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Referral Case(Primary)</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsurancePriReffTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Ref. Physician</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefPhysician" id="InsPriRefPhysician1"  value="1" <?php if($row['InsPriRefPhysician']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefPhysician1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefPhysician" id="InsPriRefPhysician2" value="2" <?php if($row['InsPriRefPhysician']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefPhysician2"></label></div></td>
					</tr>
					<tr>
						<td>Referral#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefReferral" id="InsPriRefReferral1" value="1" <?php if($row['InsPriRefReferral']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefReferral1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefReferral" id="InsPriRefReferral2" value="2" <?php if($row['InsPriRefReferral']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefReferral2"></label></div></td>
					</tr>
					<tr>
						<td>Mode</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefMode" id="InsPriRefMode1" value="1" <?php if($row['InsPriRefMode']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefMode1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefMode" id="InsPriRefMode2" value="2" <?php if($row['InsPriRefMode']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefMode2"></label></div></td>
					</tr>
					<tr>
						<td>Referral date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefRefDate" id="InsPriRefRefDate1" value="1" <?php if($row['InsPriRefRefDate']==1) { ?> checked="checked" <?php } ?> ><label for="InsPriRefRefDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefRefDate" id="InsPriRefRefDate2" value="2" <?php if($row['InsPriRefRefDate']==2) { ?> checked="checked" <?php } ?> ><label for="InsPriRefRefDate2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsurancePriReffTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Visits</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefVisits" id="InsPriRefVisits1" value="1" <?php if($row['InsPriRefVisits']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefVisits1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefVisits" id="InsPriRefVisits2" value="2"  <?php if($row['InsPriRefVisits']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefVisits2"></label></div></td>
					</tr>
					<tr>
						<td>St. Effec. Date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefStEffDate" id="InsPriRefStEffDate1" value="1" <?php if($row['InsPriRefStEffDate']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefStEffDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefStEffDate" id="InsPriRefStEffDate2" value="2" <?php if($row['InsPriRefStEffDate']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefStEffDate2"></label></div></td>
					</tr>
					<tr>
						<td>End. Effec. Date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefEndEffDate" id="InsPriRefEndEffDate1" value="1" <?php if($row['InsPriRefEndEffDate']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefEndEffDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefEndEffDate" id="InsPriRefEndEffDate2" value="2" <?php if($row['InsPriRefEndEffDate']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefEndEffDate2"></label></div></td>
					</tr>
					<tr>
						<td>Notes</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefNote" id="InsPriRefNote1" value="1" <?php if($row['InsPriRefNote']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefNote1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefNote" id="InsPriRefNote2" value="2" <?php if($row['InsPriRefNote']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefNote2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsurancePriReffTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Upload</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefUpload" id="InsPriRefUpload1" value="1" <?php if($row['InsPriRefUpload']==1) { ?> checked="checked" <?php } ?>><label for="InsPriRefUpload1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsPriRefUpload" id="InsPriRefUpload2" value="2" <?php if($row['InsPriRefUpload']==2) { ?> checked="checked" <?php } ?>><label for="InsPriRefUpload2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Insurance(Secondary)</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceSecTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Ins. Provider</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecProvider" id="InsSecProvider1" value="1" <?php if($row['InsSecProvider']==1) { ?> checked="checked" <?php } ?>><label for="InsSecProvider1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecProvider" id="InsSecProvider2" value="2" <?php if($row['InsSecProvider']==2) { ?> checked="checked" <?php } ?>><label for="InsSecProvider2"></label></div></td>
					</tr>
					<tr>
						<td>Policy#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecPolicy" id="InsSecPolicy1" value="1" <?php if($row['InsSecPolicy']==1) { ?> checked="checked" <?php } ?>><label for="InsSecPolicy1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecPolicy" id="InsSecPolicy2" value="2" <?php if($row['InsSecPolicy']==2) { ?> checked="checked" <?php } ?>><label for="InsSecPolicy2"></label></div></td>
					</tr>
					<tr>
						<td>Group#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecGroup" id="InsSecGroup1" value="1" <?php if($row['InsSecGroup']==1) { ?> checked="checked" <?php } ?>><label for="InsSecGroup1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecGroup" id="InsSecGroup2" value="2" <?php if($row['InsSecGroup']==2) { ?> checked="checked" <?php } ?>><label for="InsSecGroup2"></label></div></td>
					</tr>
					<tr>
						<td>Plan Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecPlanName" id="InsSecPlanName1" value="1"  <?php if($row['InsSecPlanName']==1) { ?> checked="checked" <?php } ?>><label for="InsSecPlanName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecPlanName" id="InsSecPlanName2" value="2"  <?php if($row['InsSecPlanName']==2) { ?> checked="checked" <?php } ?>><label for="InsSecPlanName2"></label></div></td>
					</tr>
					<tr>
						<td>Act. date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecActDate" id="InsSecActDate1" value="1"  <?php if($row['InsSecActDate']==1) { ?> checked="checked" <?php } ?>><label for="InsSecActDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecActDate" id="InsSecActDate2" value="2" <?php if($row['InsSecActDate']==2) { ?> checked="checked" <?php } ?>><label for="InsSecActDate2"></label></div></td>
					</tr>
					<tr>
						<td>Exp. date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecExpDate" id="InsSecExpDate1" value="1" <?php if($row['InsSecExpDate']==1) { ?> checked="checked" <?php } ?>><label for="InsSecExpDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecExpDate" id="InsSecExpDate2" value="2" <?php if($row['InsSecExpDate']==2) { ?> checked="checked" <?php } ?>><label for="InsSecExpDate2"></label></div></td>
					</tr>
					<tr>
						<td>CoPay</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecCoPay" id="InsSecCoPay1" value="1" <?php if($row['InsSecCoPay']==1) { ?> checked="checked" <?php } ?>><label for="InsSecCoPay1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecCoPay" id="InsSecCoPay2" value="2" <?php if($row['InsSecCoPay']==2) { ?> checked="checked" <?php } ?>><label for="InsSecCoPay2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceSecTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Refer Req</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefReq" id="InsSecRefReq1" value="1" <?php if($row['InsSecRefReq']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefReq1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefReq" id="InsSecRefReq2" value="2" <?php if($row['InsSecRefReq']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefReq2"></label></div></td>
					</tr>
					<tr>
						<td>Sub. Relation</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSubRelation" id="InsSecSubRelation1" value="1" <?php if($row['InsSecSubRelation']==1) { ?> checked="checked" <?php } ?>><label for="InsSecSubRelation1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSubRelation" id="InsSecSubRelation2" value="2" <?php if($row['InsSecSubRelation']==2) { ?> checked="checked" <?php } ?>><label for="InsSecSubRelation2"></label></div></td>
					</tr>
					<tr>
						<td>Ins. Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecInsName" id="InsSecInsName1" value="1" <?php if($row['InsSecInsName']==1) { ?> checked="checked" <?php } ?>><label for="InsSecInsName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecInsName" id="InsSecInsName2" value="2" <?php if($row['InsSecInsName']==2) { ?> checked="checked" <?php } ?>><label for="InsSecInsName2"></label></div></td>
					</tr>
					<tr>
						<td>D.O.B</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecDOB" id="InsSecDOB1" value="1" <?php if($row['InsSecDOB']==1) { ?> checked="checked" <?php } ?>><label for="InsSecDOB1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecDOB" id="InsSecDOB2" value="2" <?php if($row['InsSecDOB']==2) { ?> checked="checked" <?php } ?>><label for="InsSecDOB2"></label></div></td>
					</tr>
					<tr>
						<td>S.S#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSS" id="InsSecSS1" value="1" <?php if($row['InsSecSS']==1) { ?> checked="checked" <?php } ?>><label for="InsSecSS1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSS" id="InsSecSS2" value="2" <?php if($row['InsSecSS']==2) { ?> checked="checked" <?php } ?>><label for="InsSecSS2"></label></div></td>
					</tr>
					<tr>
						<td>Sex</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSex" id="InsSecSex1" value="1" <?php if($row['InsSecSex']==1) { ?> checked="checked" <?php } ?>><label for="InsSecSex1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSex" id="InsSecSex2" value="2" <?php if($row['InsSecSex']==2) { ?> checked="checked" <?php } ?>><label for="InsSecSex2"></label></div></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td class="text-center">
						<div class="checkbox"><input type="checkbox" id="InsSecPhone1" name="InsSecPhone" value="1" <?php if($row['InsSecPhone']==1) { ?> checked="checked" <?php } ?>><label for="InsSecPhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecPhone" id="InsSecPhone2" value="2" <?php if($row['InsSecPhone']==2) { ?> checked="checked" <?php } ?>><label for="InsSecPhone2"></label></div></td>
					</tr>	
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceSecTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Pymt. Auth</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="InsSecPymtAuth1" name="InsSecPymtAuth" value="1" <?php if($row['InsSecPymtAuth']==1) { ?> checked="checked" <?php } ?>><label for="InsSecPymtAuth1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="InsSecPymtAuth2" name="InsSecPymtAuth" value="2" <?php if($row['InsSecPymtAuth']==2) { ?> checked="checked" <?php } ?>><label for="InsSecPymtAuth2"></label></div></td>
					</tr>
					<tr>
						<td>Sign. on File</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="InsSecSignFile1" name="InsSecSignFile" value="1" <?php if($row['InsSecSignFile']==1) { ?> checked="checked" <?php } ?>><label for="InsSecSignFile1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" id="InsSecSignFile2" name="InsSecSignFile" value="2" <?php if($row['InsSecSignFile']==2) { ?> checked="checked" <?php } ?>><label for="InsSecSignFile2"></label></div></td>
					</tr>
					<tr>
						<td>Address</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" <?php if($row['InsSecAddress']==1) { ?> checked="checked" <?php } ?> name="InsSecAddress" value="1" id="InsSecAddressId_1" onClick="insurenceSecSelAddMark('1');" ><label for="InsSecAddressId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" <?php if($row['InsSecAddress']==2) { ?> checked="checked" <?php } ?> name="InsSecAddress" value="2" id="InsSecAddressId_2" onClick="insurenceSecSelAddMark('2');"><label for="InsSecAddressId_2"></label></div></td>
					</tr>
					<tr>
						<td><?php getZipPostalLabel(); ?></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecZip" value="1" id="InsSecZipId_1" <?php if($row['InsSecZip']==1) { ?> checked="checked" <?php } ?>><label for="InsSecZipId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecZip" value="2" id="InsSecZipId_2" <?php if($row['InsSecZip']==2) { ?> checked="checked" <?php } ?>><label for="InsSecZipId_2"></label></div></td>
					</tr>
					<tr>
						<td>City</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecCity" value="1" id="InsSecCityId_1" <?php if($row['InsSecCity']==1) { ?> checked="checked" <?php } ?>><label for="InsSecCityId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecCity" value="2" id="InsSecCityId_2" <?php if($row['InsSecCity']==2) { ?> checked="checked" <?php } ?>><label for="InsSecCityId_2"></label></div></td>
					</tr>
					<tr>
						<td>State</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSate" value="1" id="InsSecSateId_1" <?php if($row['InsSecSate']==1) { ?> checked="checked" <?php } ?>><label for="InsSecSateId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecSate" value="2" id="InsSecSateId_2" <?php if($row['InsSecSate']==2) { ?> checked="checked" <?php } ?>><label for="InsSecSateId_2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Referral Case(Secondary)</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceSecReffTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Ref. Physician</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefPhysician" id="InsSecRefPhysician1"  value="1"  <?php if($row['InsSecRefPhysician']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefPhysician1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefPhysician" id="InsSecRefPhysician2" value="2"  <?php if($row['InsSecRefPhysician']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefPhysician2"></label></div></td>
					</tr>
					<tr>
						<td>Referral#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefReferral" id="InsSecRefReferral1" value="1"  <?php if($row['InsSecRefReferral']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefReferral1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefReferral" id="InsSecRefReferral2" value="2"  <?php if($row['InsSecRefReferral']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefReferral2"></label></div></td>
					</tr>
					<tr>
						<td>Mode</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefMode" id="InsSecRefMode1" value="1"  <?php if($row['InsSecRefMode']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefMode1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefMode" id="InsSecRefMode2" value="2"  <?php if($row['InsSecRefMode']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefMode2"></label></div></td>
					</tr>
					<tr>
						<td>Referral date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefRefDate" id="InsSecRefRefDate1" value="1"   <?php if($row['InsSecRefRefDate']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefRefDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefRefDate" id="InsSecRefRefDate2" value="2"  <?php if($row['InsSecRefRefDate']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefRefDate2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceSecReffTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Visits</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefVisits" id="InsSecRefVisits1" value="1"  <?php if($row['InsSecRefVisits']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefVisits1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefVisits" id="InsSecRefVisits2" value="2"  <?php if($row['InsSecRefVisits']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefVisits2"></label></div></td>
					</tr>
					<tr>
						<td>St. Effec. Date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefStEffDate" id="InsSecRefStEffDate1" value="1"  <?php if($row['InsSecRefStEffDate']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefStEffDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefStEffDate" id="InsSecRefStEffDate2" value="2"  <?php if($row['InsSecRefStEffDate']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefStEffDate2"></label></div></td>
					</tr>
					<tr>
						<td>End. Effec. Date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefEndEffDate" id="InsSecRefEndEffDate1" value="1"  <?php if($row['InsSecRefEndEffDate']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefEndEffDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefEndEffDate" id="InsSecRefEndEffDate2" value="2"  <?php if($row['InsSecRefEndEffDate']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefEndEffDate2"></label></div></td>
					</tr>
					<tr>
						<td>Notes</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefNote" id="InsSecRefNote1" value="1"  <?php if($row['InsSecRefNote']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefNote1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefNote" id="InsSecRefNote2" value="2"  <?php if($row['InsSecRefNote']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefNote2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceSecReffTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Upload</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefUpload" id="InsSecRefUpload1" value="1" <?php if($row['InsSecRefUpload']==1) { ?> checked="checked" <?php } ?>><label for="InsSecRefUpload1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsSecRefUpload" id="InsSecRefUpload2" value="2" <?php if($row['InsSecRefUpload']==2) { ?> checked="checked" <?php } ?>><label for="InsSecRefUpload2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Insurance(Tertiary)</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceTerTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Ins.Provider</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerProvider" id="InsTerProvider1" value="1" <?php if($row['InsTerProvider']==1) { ?> checked="checked" <?php } ?>><label for="InsTerProvider1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerProvider" id="InsTerProvider2" value="2" <?php if($row['InsTerProvider']==2) { ?> checked="checked" <?php } ?>><label for="InsTerProvider2"></label></div></td>
					</tr>
					<tr>
						<td>Policy#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPolicy" id="InsTerPolicy1" value="1" <?php if($row['InsTerPolicy']==1) { ?> checked="checked" <?php } ?>><label for="InsTerPolicy1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPolicy" id="InsTerPolicy2" value="2" <?php if($row['InsTerPolicy']==2) { ?> checked="checked" <?php } ?>><label for="InsTerPolicy2"></label></div></td>
					</tr>
					<tr>
						<td>Group#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerGroup" id="InsTerGroup1" value="1" <?php if($row['InsTerGroup']==1) { ?> checked="checked" <?php } ?>><label for="InsTerGroup1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerGroup" id="InsTerGroup2" value="2" <?php if($row['InsTerGroup']==2) { ?> checked="checked" <?php } ?>><label for="InsTerGroup2"></label></div></td>
					</tr>
					<tr>
						<td>Plan Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPlanName" id="InsTerPlanName1" value="1" <?php if($row['InsTerPlanName']==1) { ?> checked="checked" <?php } ?>><label for="InsTerPlanName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPlanName" id="InsTerPlanName2" value="2" <?php if($row['InsTerPlanName']==2) { ?> checked="checked" <?php } ?>><label for="InsTerPlanName2"></label></div></td>
					</tr>
					<tr>
						<td>Act. date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerActDate" id="InsTerActDate1" value="1" <?php if($row['InsTerActDate']==1) { ?> checked="checked" <?php } ?>><label for="InsTerActDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerActDate" id="InsTerActDate2" value="2" <?php if($row['InsTerActDate']==2) { ?> checked="checked" <?php } ?>><label for="InsTerActDate2"></label></div></td>
					</tr>
					<tr>
						<td>Exp. date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerExpDate" id="InsTerExpDate1" value="1" <?php if($row['InsTerExpDate']==1) { ?> checked="checked" <?php } ?>><label for="InsTerExpDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerExpDate" id="InsTerExpDate2" value="2" <?php if($row['InsTerExpDate']==2) { ?> checked="checked" <?php } ?>><label for="InsTerExpDate2"></label></div></td>
					</tr>
					<tr>
						<td>CoPay</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerCoPay" id="InsTerCoPay1" value="1"  <?php if($row['InsTerCoPay']==1) { ?> checked="checked" <?php } ?>><label for="InsTerCoPay1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerCoPay" id="InsTerCoPay2" value="2" <?php if($row['InsTerCoPay']==2) { ?> checked="checked" <?php } ?>><label for="InsTerCoPay2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceTerTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Refer Req</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefReq" id="InsTerRefReq1" value="1" <?php if($row['InsTerRefReq']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefReq1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefReq" id="InsTerRefReq2" value="2"<?php if($row['InsTerRefReq']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefReq2"></label></div></td>
					</tr>
					<tr>
						<td>Sub.Relation</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSubRelation" id="InsTerSubRelation1" value="1" <?php if($row['InsTerSubRelation']==1) { ?> checked="checked" <?php } ?>><label for="InsTerSubRelation1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSubRelation" id="InsTerSubRelation2" value="2" <?php if($row['InsTerSubRelation']==2) { ?> checked="checked" <?php } ?>><label for="InsTerSubRelation2"></label></div></td>
					</tr>
					<tr>
						<td>Ins.Name</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerInsName" id="InsTerInsName1" value="1" <?php if($row['InsTerInsName']==1) { ?> checked="checked" <?php } ?>><label for="InsTerInsName1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerInsName" id="InsTerInsName2" value="2" <?php if($row['InsTerInsName']==2) { ?> checked="checked" <?php } ?>><label for="InsTerInsName2"></label></div></td>
					</tr>
					<tr>
						<td>D.O.B</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerDOB" id="InsTerDOB1" value="1" <?php if($row['InsTerDOB']==1) { ?> checked="checked" <?php } ?>><label for="InsTerDOB1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerDOB" id="InsTerDOB2" value="2" <?php if($row['InsTerDOB']==2) { ?> checked="checked" <?php } ?>><label for="InsTerDOB2"></label></div></td>
					</tr>
					<tr>
						<td>S.S#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSS" id="InsTerSS1" value="1" <?php if($row['InsTerSS']==1) { ?> checked="checked" <?php } ?>><label for="InsTerSS1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSS" id="InsTerSS2" value="2" <?php if($row['InsTerSS']==2) { ?> checked="checked" <?php } ?>><label for="InsTerSS2"></label></div></td>
					</tr>
					<tr>
						<td>Sex</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSex" id="InsTerSex1" value="1" <?php if($row['InsTerSex']==1) { ?> checked="checked" <?php } ?>><label for="InsTerSex1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSex" id="InsTerSex2" value="2" <?php if($row['InsTerSex']==2) { ?> checked="checked" <?php } ?>><label for="InsTerSex2"></label></div></td>
					</tr>
					<tr>
						<td>Phone</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPhone" id="InsTerPhone1" value="1" <?php if($row['InsTerPhone']==1) { ?> checked="checked" <?php } ?>><label for="InsTerPhone1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPhone" id="InsTerPhone2" value="2" <?php if($row['InsTerPhone']==2) { ?> checked="checked" <?php } ?>><label for="InsTerPhone2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceTerTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Insurance</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Pymt. Auth</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPymtAuth" id="InsTerPymtAuth1" value="1"  <?php if($row['InsTerPymtAuth']==1) { ?> checked="checked" <?php } ?>><label for="InsTerPymtAuth1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerPymtAuth" id="InsTerPymtAuth2" value="2"  <?php if($row['InsTerPymtAuth']==2) { ?> checked="checked" <?php } ?>><label for="InsTerPymtAuth2"></label></div></td>
					</tr>
					<tr>
						<td>Sign. on File</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSignFile" id="InsTerSignFile1" value="1"  <?php if($row['InsTerSignFile']==1) { ?> checked="checked" <?php } ?>><label for="InsTerSignFile1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSignFile" id="InsTerSignFile2" value="2"  <?php if($row['InsTerSignFile']==2) { ?> checked="checked" <?php } ?>><label for="InsTerSignFile2"></label></div></td>
					</tr>
					<tr>
						<td>Address</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerAddress"  <?php if($row['InsTerAddress']==1) { ?> checked="checked" <?php } ?> value="1" id="InsTerAddressId_1" onClick="insurenceTerSelAddMark('1');" ><label for="InsTerAddressId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerAddress"  <?php if($row['InsTerAddress']==2) { ?> checked="checked" <?php } ?> value="2" id="InsTerAddressId_2" onClick="insurenceTerSelAddMark('2');" ><label for="InsTerAddressId_2"></label></div></td>
					</tr>
					<tr>
						<td><?php getZipPostalLabel(); ?></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerZip" value="1" id="InsTerZipId_1"  <?php if($row['InsTerZip']==1) { ?> checked="checked" <?php } ?>><label for="InsTerZipId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerZip" value="2" id="InsTerZipId_2"  <?php if($row['InsTerZip']==2) { ?> checked="checked" <?php } ?>><label for="InsTerZipId_2"></label></div></td>
					</tr>
					<tr>
						<td>City</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerCity" value="1" id="InsTerCityId_1"  <?php if($row['InsTerCity']==1) { ?> checked="checked" <?php } ?>><label for="InsTerCityId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerCity" value="2" id="InsTerCityId_2"  <?php if($row['InsTerCity']==2) { ?> checked="checked" <?php } ?>><label for="InsTerCityId_2"></label></div></td>
					</tr>
					<tr>
						<td>State</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSate" value="1" id="InsTerSateId_1"  <?php if($row['InsTerSate']==1) { ?> checked="checked" <?php } ?>><label for="InsTerSateId_1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerSate" value="2" id="InsTerSateId_2"  <?php if($row['InsTerSate']==2) { ?> checked="checked" <?php } ?>><label for="InsTerSateId_2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	<div class="headinghd pd10 ">
		<h2>Referral Case(Tertiary)</h2>
	</div>
	<div class="pd10"><div class="row">
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceTerReffTable1">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Ref. Physician</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefPhysician" id="InsTerRefPhysician1" value="1" <?php if($row['InsTerRefPhysician']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefPhysician1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefPhysician" id="InsTerRefPhysician2" value="2" <?php if($row['InsTerRefPhysician']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefPhysician2"></label></div></td>
					</tr>
					<tr>
						<td>Referral#</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefReferral" id="InsTerRefReferral1" value="1" <?php if($row['InsTerRefReferral']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefReferral1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefReferral" id="InsTerRefReferral2" value="2" <?php if($row['InsTerRefReferral']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefReferral2"></label></div></td>
					</tr>
					<tr>
						<td>Mode</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefMode" id="InsTerRefMode1" value="1" <?php if($row['InsTerRefMode']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefMode1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefMode" id="InsTerRefMode2" value="2" <?php if($row['InsTerRefMode']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefMode2"></label></div></td>
					</tr>
					<tr>
						<td>Referral date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefRefDate" id="InsTerRefRefDate1" value="1" <?php if($row['InsTerRefRefDate']==1) { ?> checked="checked" <?php } ?> ><label for="InsTerRefRefDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefRefDate" id="InsTerRefRefDate2" value="2" <?php if($row['InsTerRefRefDate']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefRefDate2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceTerReffTable2">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Visits</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefVisits" id="InsTerRefVisits1" value="1"  <?php if($row['InsTerRefVisits']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefVisits1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefVisits" id="InsTerRefVisits2" value="2"  <?php if($row['InsTerRefVisits']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefVisits2"></label></div></td>
					</tr>
					<tr>
						<td>St. Effec. Date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefStEffDate" id="InsTerRefStEffDate1" value="1" <?php if($row['InsTerRefStEffDate']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefStEffDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefStEffDate" id="InsTerRefStEffDate2" value="2" <?php if($row['InsTerRefStEffDate']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefStEffDate2"></label></div></td>
					</tr>
					<tr>
						<td>End. Effec. Date</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefEndEffDate" id="InsTerRefEndEffDate1" value="1" <?php if($row['InsTerRefEndEffDate']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefEndEffDate1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefEndEffDate" id="InsTerRefEndEffDate2"value="2" <?php if($row['InsTerRefEndEffDate']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefEndEffDate2"></label></div></td>
					</tr>
					<tr>
						<td>Notes</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefNote" id="InsTerRefNote1" value="1" <?php if($row['InsTerRefNote']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefNote1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefNote" id="InsTerRefNote2" value="2" <?php if($row['InsTerRefNote']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefNote2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="table-responsive respotable adminnw" id="InsuranceTerReffTable3">
				<table class="table table-bordered table-hover">
					<tr class="lhtgrayhead">
						<th>Referral Case</th>
						<th style="width:20%" class="text-center">Advisory</th>
						<th style="width:20%" class="text-center">Mandatory</th>
					</tr>
					<tr>
						<td>Upload</td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefUpload" id="InsTerRefUpload1" value="1" <?php if($row['InsTerRefUpload']==1) { ?> checked="checked" <?php } ?>><label for="InsTerRefUpload1"></label></div></td>
						<td class="text-center"><div class="checkbox"><input type="checkbox" name="InsTerRefUpload" id="InsTerRefUpload2" value="2" <?php if($row['InsTerRefUpload']==2) { ?> checked="checked" <?php } ?>><label for="InsTerRefUpload2"></label></div></td>
					</tr>
				</table>
			</div>
		</div>
	</div></div>
	</form>
</div>
<?php
	require_once("../admin_footer.php");
?>