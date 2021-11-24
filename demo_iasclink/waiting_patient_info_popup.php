<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
session_start();
$loginUser = $_SESSION['iolink_loginUserId'];
include_once("common/conDb.php");
//include("common/link_new_file.php");
include_once("admin/classObjectFunction.php");
include("common/iOLinkCommonFunction.php");
$objManageData = new manageData;

$practiceName = getPracticeName($loginUser,'Coordinator');

$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$patient_id = $_REQUEST['patient_id'];

$pt_wait_submit = $_POST["pt_wait_submit"];

if($pt_wait_submit<>"") {
	$surgeon_name_id = $_POST["surgeon_name_id"];
	
	//START GET SURGEON NAME
	if($surgeon_name_id) {
		$postSurgeosDetails = $objManageData->getRowRecord('users', 'usersId', $surgeon_name_id);
		if($postSurgeosDetails){
			$postUsersId = $postSurgeosDetails->usersId;
			$postSurgeonFname = trim($postSurgeosDetails->fname);
			$postSurgeonMname = trim($postSurgeosDetails->mname);
			$postSurgeonLname = trim($postSurgeosDetails->lname);
		}	
	}	
	//END GET SURGEON NAME
	
	$patient_dos = $_POST["dos"];
	if($patient_dos) {
		$patient_dos_split = explode("-",$patient_dos);
		$patient_dos_temp = $patient_dos_split[2]."-".$patient_dos_split[0]."-".$patient_dos_split[1];
	}
	$patient_dob = $_POST["dob"];
	if($patient_dob) {
		$patient_dob_split = explode("-",$patient_dob);
		$patient_dob_temp = $patient_dob_split[2]."-".$patient_dob_split[0]."-".$patient_dob_split[1];	
	}
	$surgery_time_temp = trim($_POST["surgery_time"]);
	$surgery_time='';
	if($surgery_time_temp) {
		//surgery_time saved in database
		   $surgery_time_split = explode(" ",$surgery_time_temp);
			if($surgery_time_split[1]=="PM" || $surgery_time_split[1]=="pm") {
				$time_further_split = explode(":",$surgery_time_split[0]);
				$surgery_timeIncr=$time_further_split[0]+12;
				$surgery_time = $surgery_timeIncr.":".$time_further_split[1].":00";
			}elseif($surgery_time_split[1]=="AM" || $surgery_time_split[1]=="am") {
				$time_further_split = explode(":",$surgery_time_split[0]);
				$surgery_time=$time_further_split[0].":".$time_further_split[1].":00";
			}
	}
	
	
	unset ($arrayPatientRecord);
	$arrayPatientRecord['patient_first_name'] = addslashes($_POST["first_name"]);
	$arrayPatientRecord['patient_middle_name'] = addslashes($_POST["middle_name"]);
	$arrayPatientRecord['patient_last_name'] = addslashes($_POST["last_name"]);
	$arrayPatientRecord['patient_sex'] = $_POST["sex_list"];
	$arrayPatientRecord['site'] = $_POST["patient_site_list"];
	$arrayPatientRecord['surgeon_fname'] = addslashes($postSurgeonFname);
	$arrayPatientRecord['surgeon_mname'] = addslashes($postSurgeonMname);
	$arrayPatientRecord['surgeon_lname'] = addslashes($postSurgeonLname);
	$arrayPatientRecord['patient_street1'] = addslashes($_POST["address1"]);
	$arrayPatientRecord['patient_street2'] = addslashes($_POST["address2"]);
	$arrayPatientRecord['patient_city'] = addslashes($_POST["city"]);
	$arrayPatientRecord['patient_state'] = $_POST["state"];
	$arrayPatientRecord['patient_zip'] = $_POST["zip"];
	$arrayPatientRecord['dos'] = $patient_dos_temp;
	$arrayPatientRecord['patient_dob'] = $patient_dob_temp;
	$arrayPatientRecord['surgery_time'] = $surgery_time;
	$arrayPatientRecord['patient_primary_procedure'] = addslashes($_POST["prim_proc"]);
	$arrayPatientRecord['patient_home_phone'] = $_POST["home_phone"];
	$arrayPatientRecord['patient_work_phone'] = $_POST["work_phone"];
	$arrayPatientRecord['comment'] = $_POST["comment"];
	//$arrayPatientRecord['patient_status'] = "Scheduled";
	
	if($patient_in_waiting_id){
		$objManageData->updateRecords($arrayPatientRecord, 'patient_in_waiting_tbl', 'patient_in_waiting_id', $patient_in_waiting_id);
		$patient_in_waiting_id = $patient_in_waiting_id;
	}else {
		unset($conditionArr);
		$conditionArr['patient_first_name'] = addslashes($_POST["first_name"]);
		//$conditionArr['patient_middle_name'] = addslashes($_POST["middle_name"]);
		$conditionArr['patient_last_name'] = addslashes($_POST["last_name"]);
		$conditionArr['patient_dob'] = $patient_dob_temp;
		$conditionArr['dos'] = $patient_dos_temp;
		$chkPatientWaitingDetails = $objManageData->getMultiChkArrayRecords('patient_in_waiting_tbl', $conditionArr);
		if($chkPatientWaitingDetails) {
			//DO NOT INSERT PATIENT SCHEDULE AGAIN
			echo "<script>alert('This patient has already been scheduled for this day');</script>";
		}else {
			unset($conditionArr);
			$conditionArr['patient_fname'] = addslashes($_POST["first_name"]);
			//$conditionArr['patient_mname'] = addslashes($_POST["middle_name"]);
			$conditionArr['patient_lname'] = addslashes($_POST["last_name"]);
			$conditionArr['date_of_birth'] = $patient_dob_temp;
			$conditionArr['zip'] = $_POST["zip"];
			
			$chkPatientDataTblDetails = $objManageData->getMultiChkArrayRecords('patient_data_tbl', $conditionArr);
			if($chkPatientDataTblDetails) {
				foreach($chkPatientDataTblDetails as $patientDataTblDetails){
					$insertPatientDataId = $patientDataTblDetails->patient_id;
					//GET patient_id AND DO NOT INSERT PATIENT DATA IN patient_data_tbl
				}			
			}else {
				unset($arrayPatientDataRecord);
				$arrayPatientDataRecord['patient_fname'] = addslashes($_POST["first_name"]);
				$arrayPatientDataRecord['patient_mname'] = addslashes($_POST["middle_name"]);
				$arrayPatientDataRecord['patient_lname'] = addslashes($_POST["last_name"]);
				$arrayPatientDataRecord['street1'] = addslashes($_POST["address1"]);
				$arrayPatientDataRecord['street2'] = addslashes($_POST["address2"]);
				$arrayPatientDataRecord['city'] = addslashes($_POST["city"]);
				$arrayPatientDataRecord['state'] = $_POST["state"];
				$arrayPatientDataRecord['zip'] = $_POST["zip"];
				$arrayPatientDataRecord['date_of_birth'] = $patient_dob_temp;
				$arrayPatientDataRecord['sex'] = $_POST["sex_list"];
				$arrayPatientDataRecord['homePhone'] = $_POST["home_phone"];
				$arrayPatientDataRecord['workPhone'] = $_POST["work_phone"];
				$insertPatientDataId = $objManageData->addRecords($arrayPatientDataRecord, 'patient_data_tbl');
			}
			
			$arrayPatientRecord['patient_id'] = $insertPatientDataId;
			$patient_in_waiting_id = $objManageData->addRecords($arrayPatientRecord, 'patient_in_waiting_tbl');			
		}
	}
	/*echo "<script>top.iframeHome.iOLinkBookSheetFrameId.location.reload();</script>";*/
}


//START VIEW RECORD OF WAITING PATIENT 

//$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$patient_dos='';
$patient_dob='';
if($patient_id) {

	$patientDataTblQry = "SELECT * FROM `patient_data_tbl` WHERE patient_id='".$patient_id."'";
	$patientDataTblRes = imw_query($patientDataTblQry) or die(imw_error()); 
	$patientDataTblNumRow = imw_num_rows($patientDataTblRes);
	if($patientDataTblNumRow>0) {
		$patientDataTblRow = imw_fetch_array($patientDataTblRes);

		$patient_first_name 	= $patientDataTblRow['patient_fname'];
		$patient_middle_name 	= $patientDataTblRow['patient_mname'];
		$patient_last_name 		= $patientDataTblRow['patient_lname'];
		$patient_name 			= $patient_last_name.", ".$patient_first_name;
		
		$patient_sex 			= $patientDataTblRow['sex'];
		
		$patient_address1		= $patientDataTblRow['street1'];
		$patient_address2 		= $patientDataTblRow['street2'];
		$patient_city 			= $patientDataTblRow['city'];
		$patient_state 			= $patientDataTblRow['state'];
		$patient_zip 			= $patientDataTblRow['zip'];
		$patient_home_phone 	= $patientDataTblRow['homePhone'];
		$patient_work_phone 	= $patientDataTblRow['workPhone'];
		
		$patient_dob_temp 		= $patientDataTblRow['date_of_birth'];
		$patient_dob='';
		if($patient_dob_temp) { $patient_dob = date('m-d-Y',strtotime($patient_dob_temp)); }
	
	}
	
	$patientInWaitingTblQry = "SELECT * FROM `patient_in_waiting_tbl` WHERE patient_id='".$patient_id."' AND dos>='".date('Y-m-d')."' ORDER BY dos ASC limit 0,1";
	$patientInWaitingTblRes = imw_query($patientInWaitingTblQry) or die(imw_error()); 
	$patientInWaitingTblNumRow = imw_num_rows($patientInWaitingTblRes);
	if($patientInWaitingTblNumRow<=0) {
		$patientInWaitingTblQry = "SELECT * FROM `patient_in_waiting_tbl` WHERE patient_id='".$patient_id."' ORDER BY dos DESC limit 0,1";
		$patientInWaitingTblRes = imw_query($patientInWaitingTblQry) or die(imw_error()); 
		$patientInWaitingTblNumRow = imw_num_rows($patientInWaitingTblRes);
	}
	//echo $patientInWaitingTblQry;
	if($patientInWaitingTblNumRow>0) {
		$patientInWaitingTblRow = imw_fetch_array($patientInWaitingTblRes);
		//$stubTableDetails = $objManageData->getRowRecord('patient_in_waiting_tbl', 'patient_id', $patient_id, 'dos');
		$patient_in_waiting_id 	= $patientInWaitingTblRow['patient_in_waiting_id'];
		$patient_first_name 	= $patientInWaitingTblRow['patient_first_name'];
		$patient_middle_name 	= $patientInWaitingTblRow['patient_middle_name'];
		$patient_last_name 		= $patientInWaitingTblRow['patient_last_name'];
		$patient_name 			= $patient_last_name.", ".$patient_first_name;
		$patient_sex 			= $patientInWaitingTblRow['patient_sex'];
		$patient_site 			= $patientInWaitingTblRow['site'];
		
		$surgeon_fname 			= trim($patientInWaitingTblRow['surgeon_fname']);
		$surgeon_mname 			= trim($patientInWaitingTblRow['surgeon_mname']);
		$surgeon_lname 			= trim($patientInWaitingTblRow['surgeon_lname']);
		if($surgeon_mname){ $surgeon_mname = ' '.$surgeon_mname; }
		$surgeon_name 			= $surgeon_fname.$surgeon_mname.' '.$surgeon_lname;
		
		$patient_address1 		= $patientInWaitingTblRow['patient_street1'];
		$patient_address2 		= $patientInWaitingTblRow['patient_street2'];
		$patient_city 			= $patientInWaitingTblRow['patient_city'];
		$patient_state 			= $patientInWaitingTblRow['patient_state'];
		$patient_zip 			= $patientInWaitingTblRow['patient_zip'];
		
		$patient_dos_temp 		= $patientInWaitingTblRow['dos'];
		$patient_dos='';
		if($patient_dos_temp) { $patient_dos = date('m-d-Y',strtotime($patient_dos_temp)); }
	
		$patient_dob_temp = $patientInWaitingTblRow['patient_dob'];
		$patient_dob='';
		if($patient_dob_temp) { $patient_dob = date('m-d-Y',strtotime($patient_dob_temp)); }
		
		$surgery_time = $patientInWaitingTblRow['surgery_time'];
		$surgery_time_temp='';
		if($surgery_time) { $surgery_time_temp = date('h:i A',strtotime($surgery_time)); }
		
		$patient_prim_proc 	= stripslashes(trim($patientInWaitingTblRow['patient_primary_procedure']));
		$patient_home_phone = $patientInWaitingTblRow['patient_home_phone'];
		$patient_work_phone = $patientInWaitingTblRow['patient_work_phone'];
		$comment 			= $patientInWaitingTblRow['comment'];
		$patient_id 		= $patientInWaitingTblRow['patient_id'];
	}
}	
//END VIEW RECORD OF WAITING PATIENT
?>

	<script language="javascript">
	window.focus();
	
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());
	var mon=month+1;
	if(mon<=9){
		mon='0'+mon;
	}
	var todaydate=mon+'-'+day+'-'+year;
	function y2k(number){
		return (number < 1000)? number+1900 : number;
	}
	function newWindow(q){
		
		mywindow=open('mycal1.php?md='+q+'&rf=yes','','width=200,height=250,top=200,left=300');
		if(mywindow.opener == null)
			mywindow.opener = self;
	}
	function restart(q){
		fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
		if(q==8){
			if(fillDate > todaydate){
				alert("Date Of Service can not be a future date")
				return false;
			}
		}
		//document.getElementById("date"+q).value=fillDate;
		document.getElementById("date"+q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}


function doDateCheck(from, to) {
	if(chkdate(to) && chkdate(from) ){
	
	if (Date.parse(from.value) >= Date.parse(to.value)) {
	//alert("The dates are valid.");
	}
	else {
		if (from.value == "" || to.value == ""){ 
		//alert("Both dates must be entered.");
		}
		else{ 
		to.value="";
		alert("Date of birth can not be greater than current date.");
		   }
		}
	}
}


function validtion_pt_confirm(){
	var msg = 'Please enter following fields.\n';
	var flag = 0;
	f1 = document.frm_waiting_patient.first_name.value;
	f2 = document.frm_waiting_patient.last_name.value;
	f3 = document.frm_waiting_patient.surgeon_name_id.value;
	f4 = document.frm_waiting_patient.city.value;
	f5 = document.frm_waiting_patient.state.value;
	f6 = document.frm_waiting_patient.zip.value;
	f7 = document.frm_waiting_patient.dos.value;
	f8 = document.frm_waiting_patient.dob.value;
	f9 = document.frm_waiting_patient.surgery_time.value;
	f10 = document.frm_waiting_patient.prim_proc.value;
	f11 = document.frm_waiting_patient.home_phone.value;
	
	if(f1==''){	msg+='. First Name\n'; ++flag; }
	if(f2==''){	msg+='. Last Name\n'; ++flag; }
	if(f3==''){	msg+='. Surgeon Name\n'; ++flag; }
	if(f4==''){	msg+='. City\n'; ++flag; }
	if(f5==''){	msg+='. State\n'; ++flag; }
	if(f6==''){	msg+='. Zip\n'; ++flag; }
	if(f7==''){ msg+='. DOS\n'; ++flag; }
	if(f8==''){ msg+='. Date Of Birth\n'; ++flag; }
	if(f9==''){ msg+='. Surgery Time\n'; ++flag; }
	if(f10==''){ msg+='. Primary Procedure\n'; ++flag; }
	if(f11==''){ msg+='. Home Phone\n'; ++flag; }
	
	
	if(flag>0){
		alert(msg)
		return false;
	}else{
		//document.frm_waiting_patient.submit();
		return true;
	}
}

</script>

<!-- <?php
//$WaitingPatientIdSubDivDisplay = 'none';
//if($_REQUEST['patient_in_waiting_id'] && $_REQUEST['patient_in_waiting_id']<>'undefined') { $WaitingPatientIdSubDivDisplay = 'block'; }
?>			

<div id="WaitingPatientSubIdDiv" style="position:static; display:<?php echo $WaitingPatientIdSubDivDisplay;?> width:100%;overflow:hidden; vertical-align:top; ">
 -->
 <table align="left"  bgcolor="#ECF1EA" onDblClick="closePreDefineDiv()" onMouseOver="closePreDefineDiv();"  width="455" height="300"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" align="left" width="100%">
				<form name="frm_waiting_patient"  enctype="multipart/form-data" method="post" style="margin:0px; " action="" onSubmit="return validtion_pt_confirm();">
				<input type="hidden" id="patient_in_waiting_id" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
				<input type="hidden" id="patient_id" name="patient_id" value="<?php echo $patient_id; ?>">
				<input type="hidden" name="pt_wait_submit" id="pt_wait_submit" value="Save">
				<tr>
					<td><img src="images/tpixel.gif" width="1" height="2"></td>
				</tr>
				<tr valign="top">
					<td>
						<table border="0" cellpadding="2" cellspacing="0" width="90%">
								<!-- <tr style="font-size:9px;" class="text_homeb" align="left" valign="middle"  bgcolor="#F8F9F7">
									<td width="20%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">Patient Name</td>
									<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">DOB</td>
									<td width="40%" height="20" ><img src="images/tpixel.gif" width="3" height="1">Address</td>
									<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">Gender</a></td>
									<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1">ID</a></td>
								</tr> -->
								<?php
								$patient_gender='';
								if($patient_sex=='m') {
									$patient_gender='Male';
								}else if($patient_sex=='f') {
									$patient_gender='Female';
								}
								?>
								<!-- <tr style="font-size:9px;" class="text_homeb" align="left" valign="middle"  bgcolor="#F8F9F7">
									<td width="20%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1"><?php echo $patient_name;?></td>
									<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1"><?php echo $patient_dob;?></td>
									<td width="40%" height="20" ><img src="images/tpixel.gif" width="3" height="1"><?php echo stripslashes($patient_address1);?></td>
									<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1"><?php echo $patient_gender;?></a></td>
									<td width="10%" height="20" nowrap><img src="images/tpixel.gif" width="3" height="1"><?php echo $patient_id; ?></a></td>
								</tr> -->
						</table>
					</td>
				</tr>						
				<tr>
					<td class="text_smallb" width="100%">
						<table width="90%" border="0" align="left" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<table border="0"  width="99%" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
										<tr>
											<td><img src="images/tpixel.gif" width="2" height="1"></td>
											<td width="40%"  valign="top" align="left" bgcolor="#F1F4F0"><ul >
											<li class="" id="fo65li7">
												<label class="text_smallb" id="title7" style="font-size:9px;background:background-color:#F1F4F0;">Name</label>
												<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;">
												<input type="text" id="first_name_id" name="first_name" class="field text" style=" border:1px solid #ccccc; width:55px; height:15px;" tabindex="1" value="<?php echo $patient_first_name;?>"  />
												<input type="text" id="middle_name_id" name="middle_name" class="field text" style=" border:1px solid #ccccc; width:25px; height:15px;" tabindex="1" value="<?php echo $patient_middle_name;?>"  />
												<input type="text" id="last_name_id" name="last_name" class="field text" style=" border:1px solid #ccccc; width:55px; height:15px;" tabindex="1" value="<?php echo $patient_last_name;?>"  />
												</span>
												<span class="text_small" style=" vertical-align:top;padding-left:8;margin:0px 0 0 0px;  white-space:nowrap;">
													<i class="text_small" style="font-size:9px; vertical-align:top; ">First Name</i>
													<img src="images/tpixel.gif" width="2" height="1">
													<i class="text_small" style="font-size:9px;vertical-align:top; ">MI</i>
													<img src="images/tpixel.gif" width="9" height="1">
													<i class="text_small" style="font-size:9px;vertical-align:top; ">Last Name</i>
												</span>
											</li>
											
											
											<li class="" id="fo65li7" >
												<label class="text_smallb" id="title7"  style="background:background-color:#F1F4F0; white-space:nowrap;">Primary Procedure</label>
												<span class="text_small" style="background:background-color:#F1F4F0; height:20px;padding-left:8;">
													<select name="prim_proc" class="field text_small"  style="width:130px;border:1px solid #cccccc;">
														<option value="">Select Any</option>
														<?php
														$getProcedureDetails = imw_query("select * from  procedures where procedureAlias!='' order by `name`");
														while($PrimaryProcedureList=imw_fetch_array($getProcedureDetails)){
															$ProcedureId = $PrimaryProcedureList['procedureId'];
															$ProcedureCatergoryId = $PrimaryProcedureList['catId'];
															$ProcedureName = stripslashes(trim($PrimaryProcedureList['name']));
															$ProcedureAliasName = stripslashes(trim($PrimaryProcedureList['procedureAlias']));
															if($ProcedureCatergoryId=='2'){
																$category_display="(LP)&nbsp;";
															}
															else{
																$category_display="";
															}
															?>
															<option value="<?php echo $ProcedureAliasName; ?>" <?php if($patient_prim_proc && ($patient_prim_proc==$ProcedureName || $patient_prim_proc==$ProcedureAliasName)) { echo 'selected'; }?>><?php echo $category_display.$ProcedureAliasName;?></option>
															<?php	  
														}
														?>
													</select>
												</span>
											</li>
											<li class="" id="fo65li7" style="margin:0 4px 0 8px;padding:0 0 8px 0; ">
												<label class="text_smallb" id="title7"  >Address</label>
												<span class="text_small" style="margin:0 4px 0 8px;padding:0 0 8px 0; " >
													<textarea id="address1_id" name="address1" class="field textarea large text_small" style="font-family:verdana; border:1px solid #cccccc; width:120px; height:15; " rows="1" cols="50" tabindex="6"  ><?php echo stripslashes($patient_address1);?></textarea>
													<i class="text_small" style="font-size:9px;vertical-align:top; ">Address</i> </span> <span class="text_small" style="margin:0 4px 0 8px;padding:0 0 8px 0; ">
													<textarea id="address2_id" name="address2" class="field textarea large text_small" style="font-family:verdana; border:1px solid #cccccc; width:120px; height:15; " rows="1" cols="50" tabindex="6"  ><?php echo stripslashes($patient_address2);?></textarea>
													<i class="text_small" style="font-size:9px;vertical-align:top; ">Address2</i></span> <span class="text_small" style="padding-left:8; margin:0 0 0 0; vertical-align:top;  white-space:nowrap; ">
													<input type="text" id="txt_field_patient_pos" name="city" class="field text" style=" border:1px solid #ccccc;white-space:nowrap; width:55px; height:18px;" tabindex="1" value="<?php echo stripslashes($patient_city);?>"  />
													<input type="text" id="txt_field_patient_pos" name="state" class="field text" style=" border:1px solid #ccccc;white-space:nowrap; width:25px; height:18px;" tabindex="1" value="<?php echo stripslashes($patient_state);?>"  />
													<input type="text" id="txt_field_patient_pos" name="zip" class="field text" style=" border:1px solid #ccccc;white-space:nowrap; width:55px; height:18px;" tabindex="1" value="<?php echo $patient_zip;?>"  />
												</span>
												<span class="text_small" style=" vertical-align:top;padding-left:8;  white-space:nowrap;">
												<i class="text_small" style="font-size:9px;vertical-align:top;">City</i>
												<img src="images/tpixel.gif" width="30" height="1">
												<i class="text_small" style="font-size:9px;vertical-align:top;">State</i>
												<img src="images/tpixel.gif" width="2" height="1">
												<i class="text_small" style="font-size:9px;vertical-align:top;">Zip</i>
												</span>
											</li></ul>
											</td>
											<td><!-- <img src="images/tpixel.gif" width="1" height="1"> --></td>
											<td width="30%"  valign="top" bgcolor="#F1F4F0">
												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td valign="top" bgcolor="#F1F4F0">
														
														<ul>
															
															<li class="" id="fo65li7">
																<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0;white-space:nowrap;">Surgery Time</label>
																<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8;height:15px;white-space:nowrap;">
																	<input type="text" id="surgery_time_id" name="surgery_time" maxlength="8"  class="field text" style=" border:1px solid #ccccc; width:70px; height:15px;" tabindex="1" value="<?php if($surgery_time_temp) {echo $surgery_time_temp;}else if(!$patient_in_waiting_id) { echo date("h:i A");}?>"  />
																</span>
																<span class="text_small" style=" vertical-align:top;padding-left:8;margin:0px 0 0 0px;  white-space:nowrap;">
																	<i class="text_small" style="font-size:9px; vertical-align:top; ">HH:MM AM/PM</i>
																	<img src="images/tpixel.gif" width="40" height="1">
																	<i class="text_small" style="font-size:9px;vertical-align:top; "></i>
																	<img src="images/tpixel.gif" width="30" height="1">
																	<i class="text_small" style="font-size:9px;vertical-align:top; "></i>
																</span>
															</li>
															<li class="" id="fo65li7">
																<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0;white-space:nowrap;">Surgeon Name</label>
																<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8;margin:0px 0 0 0px; white-space:nowrap;height:15px;">
																	<select name="surgeon_name_id" class="field text_small"  style="width:100px;border:1px solid #cccccc;">
																		
																		<?php
																		/*unset($conditionArr);
																		$conditionArr['user_type']='Surgeon';
																		$conditionArr['practiceName']=$practiceName;
																		$getSurgeosDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr, 'lname','ASC');
																		*/
																		$getSurgeosDetails=array();
																		$strQuery1Part="";
																		if($coordinatorType!='Master') { //DISPLAY ALL SUGEON FOR MASTER COORDINATOR
																			$strQuery1Part=getPracticeUser($practiceName,"AND");   
																		}
																		$qrySurgeonDetail="Select * FROM users Where user_type='Surgeon' ".$strQuery1Part." ORDER BY lname ASC";
																		$resSurgeonDetail=imw_query($qrySurgeonDetail)or die(imw_error());
																		if(imw_num_rows($resSurgeonDetail)>0){
																			while($rowSurgeosDetails=imw_fetch_object($resSurgeonDetail)){
																				$getSurgeosDetails[]=$rowSurgeosDetails;
																			}
																		}
																		
																		if(count($getSurgeosDetails)>=2 || !$getSurgeosDetails) {//IF NO SURGEON EXIST OR MORE THAN ONE SURGEON EXIST THEN DISPLAY THIS BLANK OPTION
																		?>
																			<option value="">Select Any</option>
																		<?php
																		}
																		if($getSurgeosDetails) {
																			foreach($getSurgeosDetails as $surgeonsList){
																				$usersId = $surgeonsList->usersId;
																				$surgeonFname = trim($surgeonsList->fname);
																				$surgeonLname = trim($surgeonsList->lname);
																				$surgeonMname = trim($surgeonsList->mname);
																				if($surgeonMname) {
																					$surgeonMname = ' '.$surgeonMname;
																				}
																				$surgeonName = $surgeonFname.$surgeonMname.' '.$surgeonLname;
																				$surgeon_deleteStatus = $surgeonsList->deleteStatus;
																				if($surgeon_deleteStatus=="Yes") {
																				}else{
																					$surgeonSelected="";
																					if($reConfirmId) {  //IF PATIENT IS RECONFIRMING THEN MATCH 'SURGEON ID' WITH EXISTING 'SURGEON ID' 
																						if($surgeonId == $usersId) {
																							$surgeonSelected = "selected";
																						}
																					}else {  //IF PATIENT IS CONFIRMING FROM IMW THEN MATCH 'SURGEON NAME' WITH 'SURGEON NAME' FROM IMW(i.e stub_tbl) 
																						if($surgeon_name == trim($surgeonName)) {
																							$surgeonSelected = "selected";
																						}
																					}
																				?>
																					<option value="<?php echo $usersId; ?>" <?php echo $surgeonSelected;?>><?php echo stripslashes($surgeonLname.', '.$surgeonFname.' '.$surgeonMname); ?></option>
																				<?php
																				}
																			}
																		}	
																		?>
																	</select>
																</span>
																
															</li>
															<li class="" id="fo65li7" >
																<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0; ">DOS</label>
																<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8; height:15px;white-space:nowrap;">
																	<input type="text" id="date2" name="dos" class="field text" maxlength="10" style=" border:1px solid #ccccc; width:70px; height:15px; " tabindex="1" value="<?php echo $patient_dos;?>"  />
																	<img src="images/tpixel.gif" width="7" height="1"><img onClick="newWindow(2)" src="images/icon_cal.jpg" width="21" height="15"   ></span>
																<span class="text_small" style=" vertical-align:top;padding-left:8;margin:0px 0 0 0px;  white-space:nowrap;">
																	<i class="text_small" style="font-size:9px; vertical-align:top; ">MM-DD-YYYY</i>
																</span>
															</li>
																<li class="" id="fo65li7">
																	<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0;white-space:nowrap; ">Home Phone</label>
																	<span class="text_small" style="background:background-color:#F1F4F0;height:15px;padding-left:8;">
																		<input type="text" id="txt_field_patient_pos" name="home_phone" class="field text" style=" border:1px solid #ccccc; width:100px; height:15px;" tabindex="1" value="<?php echo $patient_home_phone;?>"  />
																	</span>
																</li>
																<li class="" id="fo65li7" >
																	<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0; ">Comments</label>
																	<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8;height:15px;">
																		<textarea id="iOLinkCommentId" name="comment" class="field textarea large text_small" style="font-family:verdana; border:1px solid #cccccc; width:120px; height:15; " rows="1" cols="50" tabindex="6"  ><?php echo stripslashes($comment);?></textarea>
																	</span>
																</li>
															</ul>
														</td>
														<td><img src="images/tpixel.gif" width="4" height="1"></td>
														<td>
															<ul>
																
															</ul>
														</td>
													</tr>

													<tr>
														<td colspan="5">
															<table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" >
																
															</table>
														</td>
													</tr>
												</table>
											</td>
											<td><!-- <img src="images/tpixel.gif" width="1" height="1"> --></td>
											<td width="30%"  valign="top" bgcolor="#F1F4F0">
												<table border="0" cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td valign="top" bgcolor="#F1F4F0">
															<?php
															//GET AGE OF PATIENT
																$patient_ageNew='';
																if($patient_dob) {
																	list($temp_month,$temp_day,$temp_year) = explode('-',$patient_dob);
																	$dformatNew="-";
																	$patient_ageNew=round(dateDiffCommon($dformatNew,date("m-d-Y", time()), $patient_dob)/365, 0);
																	if(date("m")<$temp_month || date("d")<$temp_day){
																		$patient_ageNew=$patient_ageNew-1;
																	}
																}
															//END GET AGE OF PATIENT
															?>
															<ul>
																<li class="" id="fo65li7">
																	<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0; ">DOB</label><label class="text_small">&nbsp;<?php if($patient_ageNew) { echo '('.$patient_ageNew.')';}?></label>
																	<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8; height:19px;white-space:nowrap;">
																		<input type="text" id="date1" name="dob"  class="field text" maxlength="10" style=" border:1px solid #ccccc; width:70px; height:18px;" tabindex="1" value="<?php echo $patient_dob;?>"  />
																		<img src="images/tpixel.gif" width="1" height="1"><img onClick="newWindow(1)" src="images/icon_cal.jpg"  width="21" height="18"> <a style="vertical-align:top "></a>
																	</span>
																	<span class="text_small" style=" vertical-align:top;padding-left:8;margin:0px 0 0 0px;  white-space:nowrap;">
																		<i class="text_small" style="font-size:9px; vertical-align:top; ">MM-DD-YYYY</i>
																	</span>
																</li>
																<li class="" id="fo65li7">
																<label class="text_smallb" id="title7" style="background:background-color:#F1F4F0; white-space:nowrap; ">Sex </label>
																<span class="text_small" style="background:background-color:#F1F4F0;padding-left:8; height:18px; white-space:nowrap;">
																	<select name="sex_list" id="sex_list_id" class="field text_small" style="width:70px; font-size:9px; border:1px solid #cccccc;">
																		<option value="m" <?php if($patient_sex=="m") { echo "selected"; }?> >Male</option>
																		<option value="f" <?php if($patient_sex=="f") { echo "selected"; }?> >Female</option>
																	</select>
																</span>
															</li>
															 </ul>	
														</td>
														<td><img src="images/tpixel.gif" width="4" height="1"></td>
														<td>
															<ul>
																
															</ul>
														</td>
													</tr>
												</table>
											</td>	
										</tr>
									</table>
								</td>
							</tr>
							
					</table>
				</form>
			</table>
		</td>
	</tr>
</table>
<!-- </div>	 -->
