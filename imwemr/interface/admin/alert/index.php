<?php
require_once('../admin_header.php');
if($phpServerIP != $_SERVER['HTTP_HOST']){
	$phpServerIP = $_SERVER['HTTP_HOST'];
	$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
}	
$rqRf = $_REQUEST['rf'];
$rqSiteCareScanDocid = $_REQUEST['edId'];
$rqDel = $_REQUEST['del'];
$rootDir 	= $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
$imgDir 	= $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
$serverPath = $GLOBALS['php_server']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
if($_REQUEST['del_vitalSign']){
	$qrydel=imw_query("DELETE FROM alert_vital_sign WHERE id='".$_REQUEST['del_vitalSign']."'");
	if($qrydel){
		echo "Done";
	}
}
if($_REQUEST['del_lab']){
	$qrydel=imw_query("DELETE FROM alert_labs WHERE id='".$_REQUEST['del_lab']."'");
	if($qrydel){
		echo "Done";
	}
die();	
}
if($rqSiteCareScanDocid && $rqDel){
	$qryGetSiteCareDoc = "select scan_path from alert_tbl where alertId = '$rqSiteCareScanDocid'";
	$rsGetSiteCareDoc = imw_query($qryGetSiteCareDoc);
	if($rsGetSiteCareDoc){
		if(imw_num_rows($rsGetSiteCareDoc)>0){
			$rowGetSiteCareDoc = imw_fetch_array($rsGetSiteCareDoc);
			$scan_path = $rowGetSiteCareDoc['scan_path'];
			if($scan_path){
				$qryUpdateSiteCareDoc = "update alert_tbl set scan_path = '' where alertId = '$rqSiteCareScanDocid'";
				$rsUpdateSiteCareDoc = imw_query($qryUpdateSiteCareDoc);
				$scan_full_path = $rootDir.$scan_path;
				if(file_exists($scan_full_path)) {
					unlink($scan_full_path);	
				}
			}
		}
	}	
}
$rqDelUpload = $_REQUEST['delUpload'];
if($rqSiteCareScanDocid && $rqDelUpload){
	$qryGetSiteCareDoc = "select upload_path from alert_tbl where alertId = '$rqSiteCareScanDocid'";
	$rsGetSiteCareDoc = imw_query($qryGetSiteCareDoc);
	if($rsGetSiteCareDoc){
		if(imw_num_rows($rsGetSiteCareDoc)>0){
			$rowGetSiteCareDoc = imw_fetch_array($rsGetSiteCareDoc);
			$upload_path = $rowGetSiteCareDoc['upload_path'];
			if($upload_path){
				$qryUpdateSiteCareDoc = "update alert_tbl set upload_path = '' where alertId = '$rqSiteCareScanDocid'";
				$rsUpdateSiteCareDoc = imw_query($qryUpdateSiteCareDoc);
				$scan_path = $upload_path;
				unlink($scan_path);
			}
			echo 'done';	
		}
	}
	exit();	
}

$unit_array = array("","mg","%"); //array("","cc","mg","ml");
$arrQuantity = array("","Tabs","ml","cc");
$eye_array = array("PO","OU","OS","OD","RLL","RUL","LLL","LUL","O/O","IV","IM","Topical","L/R Ear","Both Ears");
$use_array= array("qd","qhs","qAM","qid","bid","tid","qod","__hrs","__Xdaily");		
$substitute_array = array("Permissible","Not Permissible","Brand");	
//$objGetResult = new DataManage;
$auth_id = $_SESSION['authId'];
$qry = "select patient_id from recent_users 
		where provider_id = $auth_id order by enter_date";
$qryRes = imw_query($qry);
$qryRes = imw_num_rows($qryRes);
$searchOption = '';
for($i=0;$i<count($qryRes);$i++){
	$patient_id = $qryRes[$i]['patient_id'];
	$qry = "select concat(lname,', ',fname) as name , mname from patient_data
			where id = $pt_id";
	$patientDetails = imw_query($qry);
	$patientDetails=imw_fetch_assoc($patientDetails);
	$patient_name = ucwords($patientDetails['name'].' '.substr($patientDetails['mname'],0,1));
	$searchOption .= '
		<option value = "'.$patient_id.':'.$patient_name.'">'.$patient_name.' - '.$patient_id.'</option>
	';
}

$editMode = "insert";
// Edit
$dxCodeIdArr=array();
$cptCodeIdArr=array();
$vitalSignIdArr=array();
$add_testsarray=array();
$txt_lab_namearray=array();
$medArray=array();
$pageTitle = "";
$frequencyValue = "";
if(isset($_REQUEST["edId"]) && !empty($_REQUEST["edId"]))
{
	$pageTitle = "Edit Form For Site Care Plan";
	$sql = "SELECT * FROM alert_tbl WHERE alertId='".$_REQUEST["edId"]."' ";
	$row = imw_query($sql);
	$row = imw_fetch_assoc($row);
	if($row != false)
	{
		$editId 			= $row["alertId"];
		$siteCarePlanName	= $row["site_care_plan_name"];
		$siteCarePlanFor	= $row["site_care_plan_for"];
		$siteCareStatus		= $row["site_care_status"];	
		$scpUsrArr			= explode(",",$row["enable_user_ids"]);
		$pt_id 				= $row["patient_id"];
		$status 			= $row["status"];
		
		$editMode 			= "edit";
		$alertContent 		= stripslashes($row["alertContent"]);
		
		$reference          = stripslashes($row["reference"]);
		
		$dxCodeId			= stripslashes($row["dxCodeId"]);
		if($dxCodeId) {
			$dxCodeIdArr	= explode(',',$dxCodeId);
		}
		$add_tests=stripslashes($row["add_tests"]);
		if($add_tests)
		{
		$add_testsarray=explode(',',$add_tests);
		}
		
		$txt_lab_name=stripslashes($row["txt_lab_name"]);
		if($txt_lab_name)
		{
			$txt_lab_namearray=explode(',',$txt_lab_name);
		}
		$txt_lab_criteria=stripslashes($row["txt_lab_criteria"]);
		$txt_lab_result=stripslashes($row["txt_lab_result"]);
		$txt_lab_criteria_to=stripslashes($row["txt_lab_criteria_to"]);
		$txt_lab_result_to=stripslashes($row["txt_lab_result_to"]);
		
		$cptCodeId 			= stripslashes($row["cptCodeId"]);
		if($cptCodeId) {
			$cptCodeIdArr	= explode(',',$cptCodeId);
		}

		$medication 		= stripslashes($row["medication"]);
		$registeredImmunizationId = $row["registered_immunization_id"];
		$frequencyType = (int)$row["frequency_type"];
		$frequencyValue = $row["frequency_value"];
		if($frequencyValue == "200~~" && $frequencyType == 2){
			$frequencyValue = "";
			$frequencyType = "";
		} 
		$vitalSignId 		= stripslashes($row["vitalSignId"]);
		$vitalSignIdFrom 	= stripslashes($row["vitalSignIdFrom"]);
		$vitalSignIdTo 		= stripslashes($row["vitalSignIdTo"]);
		$ageFrom 			= stripslashes($row["ageFrom"]);
		$ageTo 				= stripslashes($row["ageTo"]);
		$gender 			= stripslashes($row["gender"]);
		$diabetes_type 		= stripslashes($row["diabetes_type"]);
		$cdRatio 			= stripslashes($row["cdRatio"]);
		$cdRatio_to			= stripslashes($row["cd_ratio_to"]);
		$cdRatio_from_expr	= stripslashes($row["cd_ratio_from_expr"]);
		$cdRatio_to_expr	= stripslashes($row["cd_ratio_to_expr"]);
		$iopPressure 		= stripslashes($row["iopPressure"]);
		$iopPressure_od_os 	= stripslashes($row["iopPressure_od_os"]);
		$iopPressure_Condition = stripslashes($row["iopPressure_Condition"]);
		$iopPressure_to 	= stripslashes($row["iop_pressure_to"]);
		$iopPressure_Condition_to = stripslashes($row["iop_pressure_to_condition"]);
		$cdRatio_od_os 		= stripslashes($row["cdRatio_od_os"]);
		$pt_language 		= stripslashes($row["pt_language"]);
		$pt_race 			= stripslashes($row["pt_race"]);
		$pt_ethnicity 		= stripslashes($row["pt_ethnicity"]);	
		$dob_from	 		= stripslashes($row["dob_from"]);
		$dob_to		 		= stripslashes($row["dob_to"]);
		$dob_expres			= stripslashes($row["dob_expr"]);
		$ptAllergies		= stripslashes($row["pt_allergies"]);
		$ptalertType		= stripslashes($row["alert_type"]);
		$user_type_arr		= explode(",",$row["user_type"]);
		if($dob_from){
			list($yy,$mm,$dd)=explode("-",$dob_from);	
			$dob_from=$mm."-".$dd."-".$yy;
		}
		if($dob_to){
			list($yy1,$mm1,$dd1)=explode("-",$dob_to);	
			$dob_to=$mm1."-".$dd1."-".$yy1;
		}
		if($row["scan_path"]){
			$siteCareScanDocPath =  $row["scan_path"];
		}
		if($row["upload_path"]){
			$siteCareUploadDocPath =  $row["upload_path"];
		}
		
		$ext = pathinfo($siteCareUploadDocPath);
		$ext = $ext['extension'];
		//$ext = substr($siteCareUploadDocPath, strrpos($siteCareUploadDocPath, '.') + 1); 
		$medArray=explode(",",$medication);
		//print_r($medArray);
	}	
}
else{
	$pageTitle = "New Form For Site Care Plan";	
}

$qryUsersType="SELECT user_type_id,user_type_name FROM user_type";
$UsersResType=imw_query($qryUsersType);
$users_type_arr = array();
if(imw_num_rows($UsersResType)>0) {
	while($usersRowType=imw_fetch_array($UsersResType)) {
		$username=$usersRowType['user_type_name'];
		$users_type_arr[$usersRowType['user_type_id']]=$username;	
	}
}

$qryUsers="SELECT id,fname,lname,mname,user_type FROM users WHERE delete_status!='1' order by lname";
$UsersRes=imw_query($qryUsers);
$users_arr = array();
$arr_user_tp=array();
if(imw_num_rows($UsersRes)>0) {
	while($usersRow=imw_fetch_array($UsersRes)) {
		if($usersRow['fname'] && $usersRow['lname']){
			$arr_user_tp[$usersRow['id']]=$usersRow['user_type'];
			$userAccess=$usersRow['access_pri'];
			$userLname=$usersRow['lname'];
			$userfname=$usersRow['fname'];
			$userMname=$usersRow['mname'];
			$username=$userLname.", ".$userfname;
			if(trim($userMname)){
				$username=$username." ".$userMname;
			}
			$users_arr[$usersRow['id']]=$username;
		}
	}
}
if(count($arr_user_tp)>0){
	$user_js_arr=json_encode($arr_user_tp);
}
if($ptAllergies){
	$allergyExpl=explode(",",$ptAllergies);
}


$qry1 = "select concat(lname,', ',fname) as name , mname from patient_data
			where id = '$pt_id'";
	$patientDetails1 = imw_query($qry1);
	$patientDetails1 = imw_fetch_assoc($patientDetails1);
	$patient = ucwords($patientDetails1['name']);
//echo $trFrequencyValueData;

//--- GET LAB / RADIOLOGY DETAILS ----

$qry1 = "select lab_radiology_name, lab_radiology_tbl_id
		from lab_radiology_tbl where lab_radiology_status = '0' and lab_type='Lab'";
		$labQryRes = imw_query($qry1);
$labQryRes = array();
if(imw_num_rows($labQryRes)>0) {
	while($labQryResRow=imw_fetch_array($labQryRes)) {
		$labQryRes[] = $labQryResRow;
	}
}	
		
		
$labOptionData = "";
$txt_lab_name_arr = preg_split('/,/',$txt_lab_name);
for($i=0;$i<count($labQryRes);$i++){
	$lab_radiology_name = $labQryRes[$i]['lab_radiology_name'];
	$tbl_id = $labQryRes[$i]['lab_radiology_tbl_id'];
	$sel = '';
	if(count($txt_lab_name_arr) > 0){
		if(in_array($lab_radiology_name,$txt_lab_name_arr) === true){
			$sel = 'selected="selected"';
		}
	}
	$labOptionData .= "<option value='$lab_radiology_name' $sel>$lab_radiology_name</option>";
}
$labSelectData1 = '
	<select name="txt_lab_name1[]" id="txt_lab_name1" multiple="multiple">
		<option value="">None</option>
		'.$labOptionData.'
	</select>';

$arrVitalSign=array();
$vital_qry1 = "select id,vital_sign from vital_sign_limits where status='1' order by vital_sign";
$vital_run1 = imw_query($vital_qry1);
	while($vital_fet1=imw_fetch_array($vital_run1)) {
		$arrVitalSign[$vital_fet1['id']]=$vital_fet1['vital_sign'];
	}
$arrLabName=array();
$lab_qry1 = "select lab_radiology_name from lab_radiology_tbl WHERE lab_radiology_status !=2";
$lab_run1 = imw_query($lab_qry1);
while($lab_fet1=imw_fetch_array($lab_run1)) {
	$arrLabName[$lab_fet1['lab_radiology_name']]=$lab_fet1['lab_radiology_name'];
	
}

$vitalSaved="";
$num_s=1;
if($editId){
	$qryAlertVital="SELECT id,vital_sign_id,vital_sign_id_from,vital_sign_id_to,unit,vital_sign_from_expr,vital_sign_to_expr FROM alert_vital_sign WHERE alert_id='".$editId."'";
	$resAlertVital=imw_query($qryAlertVital);
	while($rowAlertVital=imw_fetch_assoc($resAlertVital)){
		$num_s++;
		$vitalSaved.='
					<div id="tblag_save_vital_'.$rowAlertVital['vital_sign_id'].'" class="pt10">
						<div class="row">
							<div class="col-sm-2">
								<select onChange="change_option_val(this,\'unit_'.$num_s.'\')" class="form-control minimal" name=\'vitalSignId[]\' id=\'vitalSignId_\' ><option value=\'\'>Select</option>';
										foreach($arrVitalSign as $keyId=>$vitalName){
											$vitalSaved.='<option value="'.$keyId.'"';
											if($rowAlertVital['vital_sign_id']==$keyId){
												$vitalSaved.=' SELECTED ';
											}
											$vitalSaved.='>'.$vitalName.'</option>';
										}			
								$vitalSaved.="</select><input type='hidden' name='vital_alert_id[]' value=".$rowAlertVital['id'].">
							</div>";
					$vitalSaved.='
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<select id="vt_from_criteria" name="vt_from_criteria[]" class="form-control minimal">
										<option></option>
										<option value="greater" ';
										if($rowAlertVital['vital_sign_from_expr']=='greater'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.='> > </option>
										<option value="greater_equal" ';
										if($rowAlertVital['vital_sign_from_expr']=='greater_equal'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > >= </option>
										<option value="equalsto" ';
										if($rowAlertVital['vital_sign_from_expr']=='equalsto'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > = </option>
										<option value="less_equal" ';
										if($rowAlertVital['vital_sign_from_expr']=='less_equal'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > <= </option>
										<option value="less" ';
										if($rowAlertVital['vital_sign_from_expr']=='less'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > < </option>
									</select>
								</div>
								<div class="col-sm-6">
									 <input type="text" name="vitalSignIdFrom[]" id="vitalSignIdFrom" value="'.$rowAlertVital['vital_sign_id_from'].'"  class="form-control">
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<select id="vt_to_criteria" name="vt_to_criteria[]" class="form-control minimal">
										<option></option>
										<option value="greater" ';
										if($rowAlertVital['vital_sign_to_expr']=='greater'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.='> > </option>
										<option value="greater_equal" ';
										if($rowAlertVital['vital_sign_to_expr']=='greater_equal'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > >= </option>
										<option value="equalsto" ';
										if($rowAlertVital['vital_sign_to_expr']=='equalsto'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > = </option>
										<option value="less_equal" ';
										if($rowAlertVital['vital_sign_to_expr']=='less_equal'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > <= </option>
										<option value="less" ';
										if($rowAlertVital['vital_sign_to_expr']=='less'){
											$vitalSaved.=' SELECTED ';
										}
										$vitalSaved.=' > < </option>
									</select>
								</div>
								<div class="col-sm-6">
									<input type="text" name="vitalSignIdTo[]" id="vitalSignIdTo" value="'.$rowAlertVital['vital_sign_id_to'].'" class="form-control">
								</div>
							</div>
						</div>';
					$v_units=$voptionVal="<option value=''></option>";
					if($rowAlertVital['unit']){
						$v_units=$rowAlertVital['unit'];
					}if($rowAlertVital['vital_sign_id']==7){
						$voptionVal='';
						$voptionVal='   <option ';
						if($v_units=='inch'){$voptionVal.=' selected ';}
						$voptionVal.=' value="inch" >inch</option>
										<option ';
						if($v_units=='m'){$voptionVal.=' selected ';}
						$voptionVal.='  value="m" >m</option>
										<option ';
						if($v_units=='cm'){$voptionVal.=' selected ';}
						$voptionVal.=' value="cm">cm</option>';	
					}else if($rowAlertVital['vital_sign_id']==8){
						$voptionVal='';
						$voptionVal='   <option ';
						if($v_units=='lbs'){$voptionVal.=' selected ';}
						$voptionVal.='  value="lbs" >lbs</option>
										<option ';
						if($v_units=='kg'){$voptionVal.=' selected ';}
						$voptionVal.='  value="kg" >kg</option>';	
					}else if($rowAlertVital['vital_sign_id']==6){
						$voptionVal='';
						$voptionVal='   <option';
						if($v_units=='&#176;f'){$voptionVal.=' selected ';}
						$voptionVal.='  value="&#176;f" >°f</option>
										<option';
						if($v_units=='&#176;c'){$voptionVal.=' selected ';}
						$voptionVal.='  value="&#176;c" >°c</option>';	
					}
					
					$vitalSaved.='
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-8">
										<select class="form-control minimal" name="unit[]" id="unit_'.$num_s.'">'.$voptionVal.'</select>
									</div>	
									<div class="col-sm-4">
										<img id="add_vital_row" style="cursor:pointer" src="../../../library/images/closesm.png" onClick="delete_vital_sign('.$rowAlertVital['id'].',\'tblag_save_vital_'.$rowAlertVital['vital_sign_id'].'\');" >
									</div>	
								</div>
							</div>
						</div>
					</div>';
	}
	$num_l=0;
	$qryLab="SELECT id,lab_name,from_creteria,from_val,to_creteria,to_val FROM alert_labs WHERE alert_id='".$editId."'";
	$resLab=imw_query($qryLab);
	while($rowLab=imw_fetch_assoc($resLab)){
		$num_l++;
		$labSaved.='<div id="tblag_save_vital_'.$rowLab['id'].'" class="pt10">
						<div class="row">
						<div class="col-sm-11">
						<div class="row">
						<div class="col-sm-4">
					<select name="txt_lab_name[]" class="form-control minimal" id="txt_lab_name" ><option value=\'\'>Select</option>';
							foreach($arrLabName as $labNameVal){
								$labSaved.='<option value="'.$labNameVal.'"';
								if(strtolower($rowLab['lab_name'])==strtolower($labNameVal)){
									$labSaved.=' SELECTED ';
								}
								$labSaved.='>'.$labNameVal.'</option>';
							}			
					$labSaved.="</select> <input type='hidden' name='lab_alert_id[]' value=".$rowLab['id']."></div>";
					$labSaved.='
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<select id="txt_lab_from_criteria" name="txt_lab_from_criteria[]" class="form-control minimal">
										<option></option>
										<option value="greater" ';
										if($rowLab['from_creteria']=='greater'){
											$labSaved.=' SELECTED ';
										}
										$labSaved.='> > </option>
										<option value="greater_equal" ';
										if($rowLab['from_creteria']=='greater_equal'){
											$labSaved.=' SELECTED ';
										}
										$labSaved.=' > >= </option>
										<option value="equalsto" ';
										if($rowLab['from_creteria']=='equalsto'){
											$labSaved.=' SELECTED ';
										}
										$labSaved.=' > = </option>
										<option value="less_equal" ';
										if($rowLab['from_creteria']=='less_equal'){
											$labSaved.=' SELECTED ';
										}
										$labSaved.=' > <= </option>
										<option value="less" ';
										if($rowLab['from_creteria']=='less'){
											$labSaved.=' SELECTED ';
										}
										$labSaved.=' > < </option>
									</select>
								</div>
								<div class="col-sm-6">
									<input type="text" onFocus="changeBackground(\'phraseTable\');" name="txt_lab_from_val[]" value="'.$rowLab['from_val'].'" class="form-control">
								</div>
							</div>
						</div>
						
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
						
						<select id="vt_to_criteria" name="txt_lab_to_criteria[]" class="form-control minimal">
							<option></option>
							<option value="greater" ';
							if($rowLab['to_creteria']=='greater'){
								$labSaved.=' SELECTED ';
							}
							$labSaved.='> > </option>
							<option value="greater_equal" ';
							if($rowLab['to_creteria']=='greater_equal'){
								$labSaved.=' SELECTED ';
							}
							$labSaved.=' > >= </option>
							<option value="equalsto" ';
							if($rowLab['to_creteria']=='equalsto'){
								$labSaved.=' SELECTED ';
							}
							$labSaved.=' > = </option>
							<option value="less_equal" ';
							if($rowLab['to_creteria']=='less_equal'){
								$labSaved.=' SELECTED ';
							}
							$labSaved.=' > <= </option>
							<option value="less" ';
							if($rowLab['to_creteria']=='less'){
								$labSaved.=' SELECTED ';
							}
							$labSaved.=' > < </option>
						</select>
						</div>
						<div class="col-sm-6">
					<input type="text" onFocus="changeBackground(\'phraseTable\');" name="txt_lab_to_val[]" id="txt_lab_to_val" value="'.$rowLab['to_val'].'" class="form-control">
								</div>
							</div>
						</div>
						</div>
					</div>';
					$labSaved.='
					<div class="col-sm-1">
						<img id="add_lab_row_'.$num_l.'" style="cursor:pointer" src="../../../library/images/closesm.png" onClick="delete_lab('.$rowLab['id'].',\'tblag_save_vital_'.$rowLab['id'].'\');" >
					</div>
						</div>
					</div>';
	}
}
?>
<script type="text/javascript">
function checkFrm(){		
		var msg = "Please fill in the following:-<br />";
		var flag=0;	
		var blFocus = false;	
		if(trim(frmConsoleAlert.txtSiteCarePlan.value).length <= 0){
			msg += "&bullet; Site Care Plan Name<br />";
			flag +=1;
				document.frmConsoleAlert.txtSiteCarePlan.focus();
				blFocus = true;
				document.frmConsoleAlert.txtSiteCarePlan.className='form-control mandatory';
		}
		if(trim(frmConsoleAlert.alertContent.value).length <= 0){
			msg += "&bullet; Alert<br />";
			flag +=1;
				if(blFocus == false){
					document.frmConsoleAlert.alertContent.focus();
				}
			document.frmConsoleAlert.alertContent.className='form-control mandatory';
		}
		if(flag > 0){
			top.fAlert(msg);
			return false;
		}
		document.frmConsoleAlert.submit();
		return true;		
	}
	
function del_scp_scan(id,text,vari,msg){
	if(typeof(msg)!='boolean'){msg = true;}
	var title = "Do you really want to delete this Site Care "+text+"?";
	fancyConfirm(title,'Delete Document','del_scan_upload_docs("'+vari+'","'+id+'");','');
}

function del_scan_upload_docs(call,edit_id){
	$.ajax({
		url:'index.php?'+call+'=yes&edId='+edit_id,
		type:'GET',
		success:function(response){
			if($.trim(response) != ''){
				window.location.reload();	
			}
		}
	});
	
	
}	

function change_option_val(obj1,obj2){
	var newOptions="";
	if(obj1.value==8){
		newOptions = {"lbs": "lbs",
					  "kg": "kg"
					};
	}else if(obj1.value==7){
		newOptions = {"inch": "inch",
					  "m": "m",
					  "cm": "cm"
				};
	}else if(obj1.value==6){
		newOptions = {"°f": "&#176;f",
					  "°c": "&#176;c"
				};
	}
	var $el = $("#"+obj2);
	$el.empty(); // remove old options
	$.each(newOptions, function(key, value) {
	  $el.append($("<option></option>")
		 .attr("value", value).text(key));
	});
}

function chkNumericVal(obj,labl) {
	if(isNaN(obj.value)) { 
		fAlert('Please enter numeric value in '+labl); 
		obj.value='';
		obj.focus();
	}
}
	
function popup_dbl(divid,sourceid,destinationid,act,odiv){
	var modal_title = '';
	if(divid == 'pop_up_allergy'){
		modal_title = 'Please Add/Remove Allergies(s) using Arrow Buttons.';
	}else if(divid == 'pop_up'){
		modal_title = 'Please Add/Remove Medications(s) using Arrow Buttons.';
	}else if(divid == 'pop_up_problem_list'){
		modal_title = 'Please Add/Remove Problems(s) using Arrow Buttons.';
	}else if(divid == 'pop_up_cpt'){
		modal_title = 'Please Add/Remove CPT(s) using Arrow Buttons.';
	}
	
	if(act=="single" || act=="all"){
		if(act=='single')	{
			$("#"+sourceid+" option:Selected").appendTo("#"+destinationid);
		}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
	}else if(act=="single_remove" || act=="all_remove"){
		if(act=="single_remove"){$("#"+sourceid+"  option:Selected").appendTo("#"+destinationid);}
		if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
		$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
			var at = $(a).text(), bt = $(b).text();
			return (at > bt)?1:((at < bt)?-1:0);
		}));
		$("#"+destinationid).val('');
	}else{
		$("#"+destinationid+" option").remove();
		$("#"+odiv+" option").clone().appendTo("#"+destinationid);
		//$("#"+divid).show("clip");
		$('#myModal .modal-content .modal-header .modal-title').text(modal_title);
		$('#'+divid+'').css('display','block');
		$('.'+divid+'').css('display','block');
		$('#myModal').modal('show');
	}	
	$('#myModal').on('hidden.bs.modal', function (e) {
		$('#'+divid+'').css('display','none');
		$('.'+divid+'').css('display','none');
	});
}
function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
	if(action=="done"){
		var sel_cnt=$("#"+sourceid+" option").length;
		$("#"+divid).hide("clip");
		$("#"+destinationid+" option").each(function(){$(this).remove();})
		$("#"+sourceid+" option").appendTo("#"+destinationid);
		$("#"+destinationid+" option").attr({"selected":"selected"});
		$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
		if(sel_cnt>8){
			$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
		}
	}else if(action=="close"){
		$('myModal').modal('hide');
		$('#myModal').on('hidden.bs.modal', function (e) {
			$('#'+divid+'').css('display','none');
			$('.'+divid+'').css('display','none');
		});
	}
}
function delete_vital_sign(vital_id,tr_id){
	if(confirm("Do you want to delete ?")){
	ptinj_ajax_url = 'index.php?del_vitalSign='+vital_id;
		$.ajax({
			url: ptinj_ajax_url,
			success: function(respRes){
				if(respRes){
					$("#"+tr_id).remove();
					top.alert_notification_show("Record deleted");
				}
			}
		});	
	}
}
function delete_lab(vital_id,tr_id){
	if(confirm("Do you want to delete ?")){
	ptinj_ajax_url = 'index.php?del_lab='+vital_id;
		$.ajax({
			url: ptinj_ajax_url,
			success: function(respRes){
				if(respRes){
					$("#"+tr_id).remove();
					top.alert_notification_show("Record deleted");
				}
			}
		});	
	}
}

function addNewRowVitalSign(cnt){
	if(cnt==""){cnt=1;}
	var options_Val="";
	<?php
		$vital_qry = "select id,vital_sign from vital_sign_limits where status='1' order by vital_sign";
		$vital_run = imw_query($vital_qry);
		while($vital_fet=imw_fetch_array($vital_run)) {
	?>
		options_Val+='<option value="<?php echo $vital_fet['id']; ?>"><?php echo $vital_fet['vital_sign']; ?></option>';
	<?php	
	}
	?>
	var imgObj = document.getElementById("add_vital_row_"+cnt);
	imgObj.title = 'Delete Row';
	imgObj.style.height = '16px';
	imgObj.src = '../../../library/images/closesm.png';
	imgObj.onclick=function(){
		var cn='';
		cn=parseInt(imgObj.id-1);
		$("#tblag_vital_"+cn).remove();
	}
	cnt++;
	var td_class = (cnt%2) == 1 ? 'bgcolor' : '';
	imgObj.id=cnt;
	var td1 = "<div class='col-sm-2'><select onChange=\"change_option_val(this,'unit_"+cnt+"')\" name='vitalSignId[]' id='vitalSignId_"+cnt+"' class='form-control minimal'><option value=''>Select</option>"+options_Val+"</select><input type='hidden' name='vital_alert_id[]'></div>";
	var td2 = "<div class='col-sm-4'><div class='row'><div class='col-sm-6'><select id=\"vt_from_criteria\" name=\"vt_from_criteria[]\" class=\"form-control minimal\">";
		td2 +="		<option></option>";
		td2 +="		<option value=\"greater\"> > </option>";
		td2 +="		<option value=\"greater_equal\"> >= </option>";
		td2 +="		<option value=\"equalsto\"> = </option>";
		td2 +="		<option value=\"less_equal\"> <= </option>";
		td2 +="		<option value=\"less\"> < </option>";
		td2 +="     </select></div><div class='col-sm-6'><input type='text'  name='vitalSignIdFrom[]' id='vitalSignIdFrom"+cnt+"' size='2' class='form-control'></div></div></div>";
		td2 +="		<div class='col-sm-4'><div class='row'><div class='col-sm-6'><select id=\"vt_to_criteria\" name=\"vt_to_criteria[]\" class=\"form-control minimal\">";
		td2 +="		<option></option>";
		td2 +="		<option value=\"greater\"> > </option>";
		td2 +="		<option value=\"greater_equal\"> >= </option>";
		td2 +="		<option value=\"equalsto\"> = </option>";
		td2 +="		<option value=\"less_equal\"> <= </option>";
		td2 +="		<option value=\"less\"> < </option>";
		td2 +="     </select></div><div class='col-sm-6'><input type='text' name='vitalSignIdTo[]' id='vitalSignIdTo"+cnt+"' class='form-control'></div></div></div> <div class='col-sm-2'><div class='row'><div class='col-sm-8'><select id=\"unit_"+cnt+"\"  name=\"unit[]\" class='form-control minimal'><option value=\"inch\" >inch</option> <option value=\"m\" >m</option><option value=\"cm\">cm</option></select></div>";
	var td3 = "<div class='col-sm-4'><img id='add_vital_row_"+cnt+"' style='cursor:pointer' src='../../../library/images/add_small.png' alt='Add More' onClick='addNewRowVitalSign(\""+cnt+"\");' ></div></div></div>";
	var tr = "<div class=\"clearfix\"></div><div id='tblag_vital_"+cnt+"' class='"+td_class+" pt10'><div class='row'>" + td1 + td2 + td3 + "</div></div><div class=\"clearfix\"></div>";
	var obj = $("#vital_sign_table").last().append(tr);
	$('#vitalSignId_'+cnt).focus();
}		
function addNewRowLab(cnt){
	if(cnt==""){cnt=1;}
	var options_Val="";
	<?php
		 $lab_qry = "select lab_radiology_name, lab_radiology_tbl_id from lab_radiology_tbl where lab_radiology_status = '0' and lab_type='Lab'";
		$lab_run = imw_query($lab_qry);
		while($lab_fet= imw_fetch_array($lab_run)){
	?>
		options_Val+='<option value="<?php echo $lab_fet['lab_radiology_name']; ?>"><?php echo $lab_fet['lab_radiology_name']; ?></option>';
	<?php	
	}
	?>
	var imgObj = document.getElementById("add_lab_row_"+cnt);
	imgObj.title = 'Delete Row';
	imgObj.style.height = '16px';
	imgObj.src = '../../../library/images/closesm.png';
	imgObj.onclick=function(){
		var cn='';
		cn=parseInt(imgObj.id-1);
		$("#tblag_vital_"+cn).remove();
	}
	cnt++;
	var td_class=(cnt%2) == 1 ? 'bgcolor' : '';
	imgObj.id=cnt;
	var td1 = "<div class='col-sm-11'><div class='row'><div class='col-sm-4'><select name='txt_lab_name[]' id='txt_lab_name_"+cnt+"' class='form-control minimal'><option value=''>Select</option>"+options_Val+"</select><input type='hidden' name='lab_alert_id[]'></div>";
	var td2 = "<div class='col-sm-4'><div class='row'><div class='col-sm-6'><select id=\"txt_lab_from_criteria\" name=\"txt_lab_from_criteria[]\" class=\"form-control minimal\">";
		td2 +="		<option></option>";
		td2 +="		<option value=\"greater\"> > </option>";
		td2 +="		<option value=\"greater_equal\"> >= </option>";
		td2 +="		<option value=\"equalsto\"> = </option>";
		td2 +="		<option value=\"less_equal\"> <= </option>";
		td2 +="		<option value=\"less\"> < </option>";
		td2 +="     </select></div><div class='col-sm-6'><input type='text'  name='txt_lab_from_val[]' id='txt_lab_from_val"+cnt+"' class='form-control'></div></div></div>";
		td2 +="		<div class='col-sm-4'><div class='row'><div class='col-sm-6'><select id=\"txt_lab_to_criteria\" name=\"txt_lab_to_criteria[]\" class=\"form-control minimal\">";
		td2 +="		<option></option>";
		td2 +="		<option value=\"greater\"> > </option>";
		td2 +="		<option value=\"greater_equal\"> >= </option>";
		td2 +="		<option value=\"equalsto\"> = </option>";
		td2 +="		<option value=\"less_equal\"> <= </option>";
		td2 +="		<option value=\"less\"> < </option></select>";
		td2 +="		</div><div class='col-sm-6'><input type='text'  name='txt_lab_to_val[]' id='txt_lab_to_val"+cnt+"' class='form-control'></div></div></div></div></div>";
	var td3 = "<div class='col-sm-1'><img id='add_lab_row_"+cnt+"' style='cursor:pointer' src='../../../library/images/add_small.png' alt='Add More' onClick='addNewRowLab(\""+cnt+"\");' ></div>";
	var tr = "<div class=\"clearfix\"></div><div id='tblag_vital_"+cnt+"' class='"+td_class+" pt10'><div class='row'>" + td1 + td2 + td3 + "</div></div><div class=\"clearfix\"></div>";
	var obj = $("#lab_table").last().append(tr);
	
	$('#txt_lab_name_'+cnt).focus();
	document.getElementById("last_cnt_vital").value = cnt;
}

function scan_site_care(siteCareId,scanOrUpload){
	window.opener.top.popup_win("scan_site_care.php?siteCareId="+siteCareId+"&scanOrUpload="+scanOrUpload,'Scan_upload_window');	
}

function image_DIV(imageSrc,div,siteCareId){		
	siteCareId = siteCareId || "";
	if(imageSrc){
		var imageSrcNew = "<?php echo $imgDir; ?>"+imageSrc;
		rootSrc = "<?php echo $rootDir; ?>"+imageSrc;		
		if(div == "divSiteCareScanDoc"){
			document.getElementById(div).innerHTML = "<img src="+imageSrcNew+" width='47' height='36' style=\"cursor:pointer;\" onClick=\"javascript:get_modal('"+imageSrcNew+"');\"><b class='glyphicon glyphicon-remove-circle pointer circle_del' onClick=\"javascript:del_scp_scan('"+siteCareId+"','scan','del');\"></b>";					
		}else if(div == "divSiteCareUploadDoc" && imageSrc.toLowerCase().indexOf('.pdf') != -1){
			imageSrcNew = "<?php echo $GLOBALS['webroot']; ?>/library/images/pdfimg.png";
			rootSrc = "<?php echo $serverPath; ?>"+imageSrc;		
			document.getElementById(div).innerHTML = "<img src="+imageSrcNew+" width='47' height='36' style=\"cursor:pointer;\" onClick=\"pdfWindow('"+rootSrc+"');\"><b class='glyphicon glyphicon-remove-circle pointer circle_del' onClick=\"javascript:del_scp_scan('"+siteCareId+"','Upload Document','delUpload');\"></b>";					
		}else if(div == "divSiteCareUploadDoc"){
			document.getElementById(div).innerHTML = "<img src="+imageSrcNew+" width='47' height='36' style=\"cursor:pointer;\" onClick=\"javascript:get_modal('"+imageSrcNew+"');\"><b class='glyphicon glyphicon-remove-circle pointer circle_del' onClick=\"javascript:del_scp_scan('"+siteCareId+"','Upload Document','delUpload');\"></b>";				
		}				
	}
}	

function pdfWindow(pdfPath){
	window.opener.top.popup_win(pdfPath,'upload_window');	
}

</script>
<body>
<div class="container-fluid">
	<div class="whtbox">
        <div class="boxheadertop">
            <input type="hidden" name="preObjBack" id="preObjBack" value="">
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <!-- Modal content-->
                    <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="modal_title">Please Add/Remove Allergies(s) using Arrow Buttons.</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <!-- Drug Allergies Modal Box -->
                            <?php
                                $j=1;
                                $divide_hig=$_SESSION['wn_height']/3;
                                $arr_medi=array();
                                $arr_allergies=array();
                                $all_qry = "select allergie_name  from allergies_data where provider_id=0 group by allergie_name order by allergie_name ";
                                $all_run = imw_query($all_qry);
                                while($all_fet= imw_fetch_assoc($all_run)) {
                                    $arr_allergies[$all_fet['allergie_name']]=$all_fet['allergie_name'];
                                }
                            ?>
                            <div class="text-left"  id="pop_up_allergy" style="display:none">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class=" formlabel">
                                            <div class="headinghd">
                                                <h4>List of All Allergies</h4>
                                            </div>
                                            <div>
                                                <select  class="form-control minmal selecicon"  id="allergy1" name="allergy1[]" multiple="multiple" size="35" style="overflow-y:scroll;height:250px!important">
                                                    <?php foreach($arr_allergies as $allergies_name){ ?>
                                                        <option value="<?php echo $allergies_name; ?>"  <?php if(in_array($allergies_name,$allergyExpl)) { echo 'selected'; }?>><?php echo $allergies_name; ?></option>
                                                    <?php	
                                                        }														
                                                    ?>
                                                </select>	
                                            </div>
                                        </div>	
                                    </div>
                                        
                                    <div class="col-lg-2 text-center">
                                        <input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_allergy','allergy1','selected_allergy','all');"><br>
                                        <input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_allergy','allergy1','selected_allergy','single');"><br>
                                        <input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_allergy','selected_allergy','allergy1','single_remove');"><br>
                                        <input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_allergy','selected_allergy','allergy1','all_remove');">	
                                    </div>
                                    <div class="col-lg-5">
                                        <div class=" formlabel">
                                            <div class="headinghd">
                                                <h4>List of Selected Allergies</h4>
                                            </div>
                                            <div>
                                                <select class="form-control"  id="selected_allergy" name="" multiple="multiple" size="35" style="overflow-y:scroll;height:250px!important"></select>	
                                            </div>
                                        </div>
                                    </div>	
                                </div>
                            </div>
                            <!-- Drug Allergies Modal Box Ends-->
                            
                            <!-- Medication Modal Box -->
                            <?php
                                $arr_medi=array();
                                $arr_med_val=array();
                                $med_qry = "select medicine_name from medicine_data where provider_id=0 and del_status = '0' group by medicine_name order by medicine_name";
                                $med_run = imw_query($med_qry);
                                while($med_fetchval= imw_fetch_assoc($med_run)) {
                                    $arr_med_val[]=$med_fetchval['medicine_name'];	 
                                }
                                
                                //sort($arr_medi);
                                $med_options="";	
                                $saved_med=explode(",",$medication);
                                if(count($saved_med)>0){
                                    foreach($saved_med as $saved_med_name){
                                        if(trim($saved_med_name)){
                                            $med_options.="<option value='".$saved_med_name."' selected>".$saved_med_name."</option>";
                                            $med_optionsNew1.=$saved_med_name."\n";
                                        }
                                    }
                                }
                            ?>	
                                <div class="text-left"  id="pop_up" style="display:none">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class=" formlabel">
                                                <div class="headinghd">
                                                    <h4>List of All Medications</h4>
                                                </div>
                                                <div>
                                                     <select  class="form-control"  id="medication1" name="medication1[]" multiple="multiple" style="overflow-y:scroll;height:250px!important" size="35">										
                                                        <?php foreach($arr_med_val as $med_names){?>
                                                              <option value="<?php echo $med_names;?>"  <?php if(in_array($med_names,$medArray)) { echo 'selected'; }?>><?php echo $med_names; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>	
                                        </div>
                                            
                                        <div class="col-lg-2 text-center">
                                            <input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up','medication1','selected_med','all');"><br >
                                            <input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up','medication1','selected_med','single');"><br >
                                            <input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up','selected_med','medication1','single_remove');"><br >
                                            <input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up','selected_med','medication1','all_remove');"><br >	
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="formlabel">
                                                <div class="headinghd">
                                                    <h4 style="white-space:nowrap">List of Selected Medications</h4>
                                                </div>
                                                <div>
                                                    <select  class="form-control"  id="selected_med" name=""  size="35" multiple="multiple" style="overflow-y:scroll;height:250px!important"></select>	
                                                </div>
                                            </div>
                                        </div>	
                                    </div>
                                </div>
                            <!-- Medication Modal Box Ends -->
                            
                            <!-- Problems Modal Box Starts -->
                            <?php
                                $arr_prob_list=array();
                                $diag_qry = "select icd10 as dx_code,id as diagnosis_id,icd10_desc as diag_description from icd10_data order by icd10_desc asc";
                                $diag_run = imw_query($diag_qry);
                                while($diag_fet= imw_fetch_array($diag_run)){
                                    $arr_prob_list[$diag_fet['diagnosis_id']]=$diag_fet['diag_description'].' - '.$diag_fet['dx_code'];	
                                }
                            ?>
                                <div class="text-left"  id="pop_up_problem_list" style="display:none">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class=" formlabel">
                                                <div class="headinghd">
                                                    <h4>List of All Problem List</h4>
                                                </div>
                                                <div>
                                                    <select  class="form-control"  id="problem1" name="problem1[]" multiple="multiple" size="35" style="overflow-y:scroll;height:250px!important">
                                                        <?php 
                                                           foreach($arr_prob_list as $probkey=> $prob_names) {
                                                        ?>
                                                            <option value="<?php echo $probkey; ?>"  <?php if(in_array($probkey,$dxCodeIdArr)) { echo 'selected'; }?> style="word-wrap:break-word;"><?php echo $prob_names; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>	
                                        </div>
                                            
                                        <div class="col-lg-2 text-center">
                                            <input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_problem_list','problem1','selected_prob','all');"><br>
                                            <input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_problem_list','problem1','selected_prob','single');"><br>
                                            <input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_problem_list','selected_prob','problem1','single_remove');"><br>
                                            <input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_problem_list','selected_prob','problem1','all_remove');">	
                                        </div>
                                        
                                        <div class="col-lg-5">
                                            <div class=" formlabel">
                                                <div class="headinghd">
                                                    <h4>List of Selected Problem List</h4>
                                                </div>
        
                                                <div>
                                                     <select  class="form-control"  id="selected_prob" name="" multiple="multiple"  size="35" style="overflow-y:scroll;height:250px!important"></select>
                                                </div>
                                            </div>
                                        </div>	
                                    </div>
                                </div>
                            <!-- Problems Modal Box Ends --> 
                            
                            <!-- CPT Codes Modal Box Starts -->
                                <?php
                                    $arr_cpt_code=array();
                                    $cpt_qry = "select cpt_prac_code,cpt_fee_id,cpt_desc from cpt_fee_tbl where delete_status = '0' order by cpt_desc asc";
                                    $cpt_run = imw_query($cpt_qry);
                                    while($cpt_fet=imw_fetch_array($cpt_run)) {
                                        $arr_cpt_code[$cpt_fet['cpt_fee_id']]=str_replace('"','',$cpt_fet['cpt_desc']).' - '.$cpt_fet['cpt_prac_code'];
                                    }
                                ?>
                                <div class="text-left"  id="pop_up_cpt" style="display:none">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class=" formlabel">
                                                <div class="headinghd">
                                                    <h4>List of All CPT List</h4>
                                                </div>
        
                                                <div>
                                                     <select  class="form-control"  id="cpt1" name="cpt1[]"  size="35" multiple="multiple" style="height:250px!important;overflow-y:scroll">	<?php foreach($arr_cpt_code as $cptkey=> $cpt_names) { ?>
                                                                <option value="<?php echo $cptkey; ?>"  <?php if(in_array($cptkey,$cptCodeIdArr)) { echo 'selected'; }?>><?php echo $cpt_names; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>	
                                        </div>
                                            
                                        <div class="col-lg-2 text-center">
                                            <input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_cpt','cpt1','selected_cpt','all');"><br>
                                            <input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_cpt','cpt1','selected_cpt','single');"><br>
                                            <input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_cpt','selected_cpt','cpt1','single_remove');"><br>
                                            <input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_cpt','selected_cpt','cpt1','all_remove');">	
                                        </div>
                                        <div class="col-lg-5">
                                            <div class=" formlabel">
                                                <div class="headinghd">
                                                    <h4>List of Selected CPT List</h4>
                                                </div>
                                                <div>
                                                    <select  class="form-control"  id="selected_cpt" name=""  size="35" multiple="multiple" style="overflow-y:scroll;height:250px!important"></select>
                                                </div>
                                            </div>
                                        </div>	
                                    </div>
                                </div>
                            <!--  CPT Codes Modal Box Ends  -->
                        </div>	
                    </div>
                    <div id="module_buttons" class="modal-footer ad_modal_footer">
                        <!-- Allergy Footer -->
                        <div class="pop_up_allergy" style="display:none">
                            <button type="button" class="btn btn-success" onClick="selected_ele_close('pop_up_allergy','selected_allergy','drug','scp_div_allergy','done')" data-dismiss="modal">Done</button>
                            
                            <button type="button" class="btn btn-danger" onClick="selected_ele_close('pop_up_allergy','selected_allergy','drug','scp_div_allergy','close')" data-dismiss="modal">Close</button>
                        </div>
                        
                        <!-- Medication List Footer -->
                        <div class="pop_up" style="display:none">
                            <button type="button" class="btn btn-success" onClick="selected_ele_close('pop_up','selected_med','medication','scp_div_disable','done')" data-dismiss="modal">Done</button>
                            
                            <button type="button" class="btn btn-danger" data-dismiss="modal" name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up','selected_med','medication','scp_div_disable','close')">Close</button>
                        </div>
                        
                        <!-- Problem Footer -->
                        <div class="pop_up_problem_list" style="display:none">
                            <button type="button" class="btn btn-success" onClick="selected_ele_close('pop_up_problem_list','selected_prob','elemDxCodeId','scp_div_prob','done')" data-dismiss="modal">Done</button>
                            
                            <button type="button" class="btn btn-danger" data-dismiss="modal" name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_problem_list','selected_prob','elemDxCodeId','scp_div_prob','close')">Close</button>
                        </div>
                        
                        <!-- CPT Codes Footer -->
                        <div class="pop_up_cpt" style="display:none">
                            <button type="button" class="btn btn-success" onClick="selected_ele_close('pop_up_cpt','selected_cpt','elemCptCodeId','scp_div_cpt','done')" data-dismiss="modal">Done</button>
                            
                            <button type="button" class="btn btn-danger" data-dismiss="modal" value="Close" onClick="selected_ele_close('pop_up_cpt','selected_cpt','elemCptCodeId','scp_div_cpt','close')">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        	<!-- Form -->			
        	<div class="editrec tblBg pt10"><h2>Site Care Plan : <?php if($editId<>""){ echo 'Edit'; } else {  echo 'Add'; }?>  Record</h2></div>
    	</div>
    	<div class="clearfix"></div>
        <form name="frmConsoleAlert" action="saveAlert.php" method="post" style="margin:0px;">
        <input type="hidden" name="editId" id="editId" value="<?php echo $editId;?>">
        <input type="hidden" name="editMode" id="editMode" value="<?php echo $editMode;?>">
        <input type="hidden" name="oldStatus" id="oldStatus" value="<?php echo $status;?>">
        <div class="container-fluid"> 
            <div class="row">
                <div class="col-sm-2 lft_panel sitcarplan">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="sitcarplan">
                                <h2>Site Care Plan</h2>
                                <div class="clearfix"></div>
                                <input type="text" class="form-control" name="txtSiteCarePlan" id="txtSiteCarePlan" value="<?php echo $siteCarePlanName; ?>"/>
                                <div class="clearfix"></div>
                                <label>Site Care Plan For</label>
                                <div class="checkbox checkbox-inline">
                                    &nbsp;&nbsp;<input type="checkbox" id="cbPHMS" name="cbPHMS" value="1" <?php if($siteCarePlanFor == 0){echo 'checked';} ?> onClick="disAbleAcc(this);"/><label for="cbPHMS">PHMS</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="sitcarplan">
                                <h2>Alert Type</h2>
                                <div class="clearfix"></div>
                                <div class="radio radio-inline">
                                    <input type="radio" <?php if($ptalertType=="alert" ||$ptalertType==""){echo "checked";} ?> value="alert" id="alert_type_alert" name="alert_type" /><label for="alert_type_alert">Alert</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" <?php if($ptalertType=="doc"){echo "checked";} ?> value="doc" id="alert_type_for" name="alert_type" /><label for="alert_type_for">Document</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="sitcarplan">
                                        <h2>Document Scan</h2>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 text-center">
                                            <figure class="border_box" id="divSiteCareScanDoc">
                                                <?php 
                                                    if($siteCareScanDocPath && file_exists($rootDir.$siteCareScanDocPath)){
                                                        ?>
                                                        <img src="<?php echo $imgDir.$siteCareScanDocPath; ?>" width='47' height='36' style="cursor:pointer;" onClick="get_modal('<?php echo $imgDir.$siteCareScanDocPath; ?>')">
                                                        <b class="glyphicon glyphicon-remove-circle pointer circle_del" onClick="javascript:del_scp_scan('<?php echo $editId ;?>','scan','del');"></b> 
														<?php
                                                    }else{
                                                        ?>
                                                        <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/scan1.png" alt=""/>	
                                                        <?php
                                                    }
                                                ?>
            
                                                
                                            </figure>	
                                        </div>
                                        <div class="col-sm-12 text-center pt10">
                                            <button class="carebut text-center" type="button" onClick="scan_site_care('<?php echo $editId;?>','scan');">Click Here</button>
                                            <label>Supporting Document 1</label>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="sitcarplan">
                                        <h2>Document Upload</h2>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12 text-center">
                                            <figure class="border_box" id="divSiteCareUploadDoc">
                                                <?php 
                                                    if($siteCareUploadDocPath && file_exists($rootDir.$siteCareUploadDocPath) && $ext != "pdf"){
                                                        ?>
                                                         <img src="<?php echo $imgDir.$siteCareUploadDocPath; ?>" width='47' height='36' style="cursor:pointer;" onClick="get_modal('<?php echo $imgDir.$siteCareUploadDocPath; ?>')">
                                                          <b class="glyphicon glyphicon-remove-circle pointer circle_del" onClick="javascript:del_scp_scan('<?php echo $editId ;?>','Upload Document','delUpload');"></b> 
														<?php
                                                    }elseif($siteCareUploadDocPath && file_exists($rootDir.$siteCareUploadDocPath) && $ext == "pdf"){
                                                        ?>
                                                        <img src="<?php echo $GLOBALS['webroot']; ?>/library/images/pdfimg.png" border="0"  style="cursor:pointer;" onClick="pdfWindow('<?php echo $serverPath.$siteCareUploadDocPath; ?>');">
                                                    	<b class="glyphicon glyphicon-remove-circle pointer circle_del" onClick="javascript:del_scp_scan('<?php echo $editId ;?>','Upload Document','delUpload');"></b> 
													<?php
													}else{
													?>
														<img src="<?php echo $GLOBALS['webroot']; ?>/library/images/situpload.png" alt=""/>
													<?php
                                                    }
                                                ?>
                                            </figure>
                                        </div>	
                                        <div class="col-sm-12 text-center pt10">
                                            <button class="carebut" type="button" onClick="scan_site_care('<?php echo $editId;?>','upload');">Click Here</button><br />
                                            <label>Supporting Document 2</label>	
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-10 right_panel">
                    <div class="tblBg">
                    	<div class="row">
                        	<div class="col-sm-12">
                            	<div class="row">
                                	<div class="col-sm-4">
                                    	<div class="row">
                                        	<div class="col-sm-12">
                                            	<div class="form-group">
                                                	<label for="user_type">User Group</label>
                                                    <select class="selectpicker selecicon" id="user_type" name="user_type[]" data-live-search="true" data-actions-box="true" data-width="100%" data-size="10" data-title="Please Select" multiple>
                                                        <?php 
                                                            foreach($users_type_arr as $usersId => $usersName){
                                                                $sel='';
                                                                 if(in_array($usersId,$user_type_arr)) {
                                                                    $sel = "SELECTED";	
                                                                }
                                                                ?>
                                                                <option value="<?php echo $usersId; ?>" <?php echo $sel; ?>><?php echo $usersName; ?></option>
                                                               <?php
                                                              }
                                                        ?>
                                                    </select>
                                                
                                                </div>
                                            </div>	
                                        
                                        	<div class="col-sm-12">
                                            	<div class="form-group">
                                                    <label for="user_name">User</label>
                                                    <select class="selectpicker selecicon" id="user_name" name="user_name[]" data-live-search="true" data-actions-box="true" data-width="100%" data-size="10" data-title="Please Select" multiple >
                                                        <?php 
                                                            foreach($users_arr as $usersIdKey => $usersName){
                                                                $sel='';
                                                                if(in_array($usersIdKey,$scpUsrArr)) {
                                                                    $sel = "SELECTED";	
                                                                }
                                                                ?>
                                                                <option value="<?php echo $usersIdKey; ?>" <?php echo $sel; ?>><?php echo $usersName; ?></option>
                                                               <?php
                                                            }
                                                        ?>
                                                    </select>
                                            	</div>
                                        	</div>	
                                   	 	</div>
                                	</div>	
                                    <div class="col-sm-4">
                                        <label>Alert</label>
                                        <textarea class="form-control" rows="3" name="alertContent" id="alertContent" ><?php print htmlentities($alertContent); ?></textarea>	
                                    </div>	
                                    <div class="col-sm-4">
                                        <label for="Reference">Reference</label>
                                        <textarea name="Reference" id="Reference" class="form-control" rows="3"><?php print htmlentities($reference); ?></textarea>
                                    </div>	
                            	</div>	
                        	</div>	
                    	</div>
                    
                    	<div class="row">
                        <?php
                            $pt_language_arr=array();
                            if($pt_language){
                                $pt_language_arr=explode(",",$pt_language);	
                            }
                            if($pt_race){
                                $pt_race_arr=explode(",",$pt_race);
                            }
                            if($pt_ethnicity){
                                $pt_ethnicity_arr=explode(",",$pt_ethnicity);	
                            }
                            $pt_gen_array=array();
                            if($gender){$pt_gen_array=explode(",",$gender);}
                            if($diabetes_type){$pt_diab_array=explode(",",$diabetes_type);}
                            $arrLanguage = array('English','Spanish','French','German','Russian','Japanese','Portuguese','Italian');
                            sort($arrLanguage);
                            $arrLanguage[] = 'Declined to Specify';
                            $arrLanguage[] = 'Other';
            
                            $arrRace = array(
                                "American Indian or Alaska Native" => "American Indian or Alaska Native",
                                "Asian" => "Asian",
                                "Black or African American" => "Black or African American",
                                "Native Hawaiian or Other Pacific Islander" => "Native Hawaiian or Other Pacific Islander",
                                "Latin American" => "Latin American",
                                "White" => "White",
                                "Declined to Specify" => "Declined to Specify",
                                "Other Race" => "Other"
                                );	
                            $arrEthnicity = array(
                                "African Americans" => "African Americans",
                                "American" => "American",
                                "American Indians" => "American Indians",
                                "Chinese" => "Chinese",
                                "European Americans" => "European Americans",
                                "Hispanic or Latino" => "Hispanic or Latino",
                                "Jewish" => "Jewish",
                                "Not Hispanic or Latino" => "Not Hispanic or Latino",
                                "Unknown" => "Unknown",
                                "Declined to Specify" => "Declined to Specify",
                                "Other" => "Other" 
                                );	
                            ?>
                        
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Race</label>
                                            <select name="pt_race[]" multiple class="selectpicker" data-width="100%" >
                                                <option value="">None</option>
                                                <?php foreach ($arrRace as $r){
                                                    $sel_race="";
                                                    if(in_array($r,$pt_race_arr)){$sel_race="selected";}	
                                                ?>
                                                    <option value="<?php echo $r; ?>" <?php echo $sel_race; ?>><?php echo $r; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Language</label>
                                            <select name="pt_language[]" multiple class="selectpicker" data-width="100%" >
                                                <option value="">None</option>
                                                <?php foreach ($arrLanguage as $s){
                                                        $sel_lng="";
                                                        if(in_array($s,$pt_language_arr)){$sel_lng="selected";}	
                                                ?>
                                                    <option value="<?php echo $s; ?>" <?php echo $sel_lng; ?>><?php echo $s; ?></option>
                                                <?php } ?>                                             	
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Ethnicity</label>
                                            <select name="pt_ethnicity[]" multiple class="selectpicker" data-width="100%" data-size="5">
                                                <option value="">None</option>
                                                <?php foreach ($arrEthnicity as $e){
                                                    $sel_eth="";
                                                    if(in_array($e,$pt_ethnicity_arr)){$sel_eth="selected";}	
                                                ?>
                                                    <option value="<?php echo $e; ?>" <?php echo $sel_eth; ?>><?php echo $e; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Sex</label>
                                            <select name="gender[]"  multiple="multiple" class="selectpicker" data-width="100%" data-size="5">
                                                <option value="">None</option>
                                                <option value="Female" <?php if(in_array("Female",$pt_gen_array)){echo "selected";} ?>>Female</option>
                                                <option value="Male" <?php if(in_array("Male",$pt_gen_array)){echo "selected";} ?>>Male</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Diabetes</label>
                                            <select name="diabetes_type[]"  multiple="multiple" class="selectpicker" data-width="100%" data-size="5">
                                                <option value="">None</option>
                                                <option value="DM Type 1" <?php if(in_array("DM Type 1",$pt_diab_array)){echo "selected";} ?>>DM Type 1</option>
                                                <option value="DM Type 2" <?php if(in_array("DM Type 2",$pt_diab_array)){echo "selected";} ?>>DM Type 2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <label>DOB</label>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="input-group"> 
                                                        <label for="pt_dob1" class="input-group-addon pointer">
                                                            <span>From:</span>	
                                                        </label>
                                                        <input type="text" name="pt_dob1" id="pt_dob1"  class="form-control date-pick" value="<?php echo ($dob_from != '') ? date(phpDateFormat(), strtotime(str_replace('-', '/', $dob_from))) : '' ; ?>">
                                                        <label for="pt_dob1" class="input-group-addon pointer">
                                                            <span class="glyphicon glyphicon-calendar"></span>	
                                                        </label>	
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="input-group"> 
                                                        <label for="pt_dob2" class="input-group-addon pointer">
                                                            <span>To:</span>	
                                                        </label>
                                                        <input type="text" name="pt_dob2" id="pt_dob2" class="form-control date-pick" value="<?php echo ($dob_to != '') ? date(phpDateFormat(), strtotime(str_replace('-', '/', $dob_to))) : '' ; ?>">
                                                        <label for="pt_dob2" class="input-group-addon pointer">
                                                            <span class="glyphicon glyphicon-calendar"></span>	
                                                        </label>	
                                                    </div>	
                                                </div>
                                            </div>	
                                        </div>
                                    </div>
                                </div>
                            </div>	
                        </div>
                    
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group" onClick="return popup_dbl('pop_up_allergy','allergy1','selected_allergy','','drug')">
                                            <label class="text_purple pointer">Drug Allergies</label>	
                                            <select  class="form-control"  style="cursor:pointer" id="drug" name="drug[]" multiple="multiple">										
                                                <?php foreach($allergyExpl as $allergies_save_name){ ?>
                                                       <option value="<?php echo $allergies_save_name; ?>" selected><?php echo $allergies_save_name; ?></option>
                                                <?php	}	?>
                                            </select>		
                                        </div>
                                    </div>
                
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group" onClick="return popup_dbl('pop_up','medication1','selected_med','','medication')">
                                                    <label class="text_purple pointer">Medication List</label>
                                                    <?php
                                                        $arr_medi=array();
                                                        $arr_med_val=array();
                                                        $med_qry = "select medicine_name from medicine_data where provider_id=0 and del_status = '0' group by medicine_name order by medicine_name";
                                                        $med_run = imw_query($med_qry);
                                                        while($med_fetchval=imw_fetch_assoc($med_run)) {
                                                            $arr_med_val[]=$med_fetchval['medicine_name'];	 
                                                        }
                                                        //sort($arr_medi);
                                                        $med_options="";	
                                                        $saved_med=explode(",",$medication);
                                                        if(count($saved_med)>0){
                                                            foreach($saved_med as $saved_med_name){
                                                                if(trim($saved_med_name)){
                                                                    $med_options.="<option value='".$saved_med_name."' selected>".$saved_med_name."</option>";
                                                                    $med_optionsNew1.=$saved_med_name."\n";
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <select class="form-control"  id="medication" name="medication[]" multiple="multiple">										
                                                        <?php echo $med_options; ?>
                                                    </select>	
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <div class="form-group" onClick="return popup_dbl('pop_up_problem_list','problem1','selected_prob','','elemDxCodeId')" style="cursor:pointer;">
                                                    <label class="text_purple pointer">Problem List</label>
                                                    <?php
                                                        $arr_prob_list=array();
                                                        $diag_qry = "select icd10 as dx_code,id as diagnosis_id,icd10_desc as diag_description from icd10_data order by icd10_desc asc";
                                                        $diag_run = imw_query($diag_qry);
                                                        while($diag_fet=imw_fetch_array($diag_run)){
                                                            $arr_prob_list[$diag_fet['diagnosis_id']]=$diag_fet['diag_description'].' - '.$diag_fet['dx_code'];	
                                                        }
                                                    ?>
                                                    <select class="form-control"  id="elemDxCodeId" name="dxCodeId[]" multiple="multiple">										
                                                        <?php foreach($dxCodeIdArr as $dx_val_id){ ?>
                                                        <option value="<?php echo $dx_val_id;?>" selected  style="word-wrap:break-word;"><?php echo $arr_prob_list[$dx_val_id]; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <div class="form-group" onClick="return popup_dbl('pop_up_cpt','cpt1','selected_cpt','','elemCptCodeId')">
                                                    <label class="text_purple pointer">Cpt Code</label>
                                                    <?php
                                                    $arr_cpt_code=array();
                                                    $cpt_qry = "select cpt_prac_code,cpt_fee_id,cpt_desc from cpt_fee_tbl where delete_status = '0' order by cpt_desc asc";
                                                    $cpt_run = imw_query($cpt_qry);
                                                    while($cpt_fet=imw_fetch_array($cpt_run)) {
                                                        $arr_cpt_code[$cpt_fet['cpt_fee_id']]=str_replace('"','',$cpt_fet['cpt_desc']).' - '.$cpt_fet['cpt_prac_code'];
                                                    }
                                                    ?>
                                                        <select class="form-control"  id="elemCptCodeId" name="cptCodeId[]"  multiple="multiple" >										
                                                        <?php foreach($cptCodeIdArr as  $cpt_save_val){ ?>
                                                            <option value="<?php echo $cpt_save_val; ?>" selected><?php echo $arr_cpt_code[$cpt_save_val]; ?></option>
                                                        <?php } ?>
                                                        </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Tests</label>
                                                    <?php
                                                        $arrTestNms = array("A/Scan","OCT-RNFL","VF-GL","Cell Count","External/Anterior","Fundus","GDX","HRT","ICG","IVFA","Laboratories","Other","OCT","Pachy","Topography","VF","Ophthalmoscopy","B-Scan");
                                                        sort($arrTestNms);	
                                                    ?>
                                                    <select class="form-control" name="add_tests[]" id="add_tests_elem" multiple="multiple">
                                                        <option value="">None</option>
                                                        <?php ;
                                                        foreach($arrTestNms as $key => $val){ ?>
                                                        <option value="<?php echo $val; ?>" 												  
                                                        <?php if(in_array($val,$add_testsarray)) { echo 'selected'; }?>><?php echo $val; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>	
                                    </div>	
                                </div>
                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="grpbox sitcarbx">
                                            <div class="head">
                                            <span>Vital Sign</span>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="tblBg">
                                                <div id="vital_sign_table" style="height:120px;overflow-y:auto;overflow-x:hidden">
                                                     <?php if($vitalSaved){	echo $vitalSaved; } ?>
                                                    <div class="row pt10">
                                                        <div class="col-sm-2">
                                                            <select onChange="change_option_val(this,'unit')" name='vitalSignId[]' id='vitalSignId_' class="form-control minimal">
                                                                <option value=''>Select</option>
                                                                <?php
                                                                    $vital_qry = "select id,vital_sign from vital_sign_limits where status='1' order by vital_sign";
                                                                    $vital_run = imw_query($vital_qry);
                                                                    while($vital_fet=imw_fetch_array($vital_run)) {
                                                                ?>
                                                                    <option value="<?php echo $vital_fet['id']; ?>" ><?php echo $vital_fet['vital_sign']; ?></option>
                                                                <?php	
                                                                    }														
                                                                ?>
                                                            </select>
                                                            <input type='hidden' name='vital_alert_id[]'>
                                                        </div>
                                                        
                                                        <div class="col-sm-4">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <select id="vt_from_criteria" name="vt_from_criteria[]" class="form-control minimal">
                                                                        <option></option>
                                                                        <option value="greater"> > </option>											
                                                                        <option value="greater_equal"> >= </option>
                                                                        <option value="equalsto"> = </option>
                                                                        <option value="less_equal"> <= </option>
                                                                        <option value="less"> < </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <input type="text"  name="vitalSignIdFrom[]" id="vitalSignIdFrom" class="form-control">
                                                                </div>	
                                                            </div>	
                                                        </div>
                                                        
                                                        <div class="col-sm-4">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <select id="vt_to_criteria" name="vt_to_criteria[]" class="form-control minimal">
                                                                        <option></option>
                                                                        <option value="greater"> > </option>											
                                                                        <option value="greater_equal"> >= </option>
                                                                        <option value="equalsto"> = </option>
                                                                        <option value="less_equal"> <= </option>
                                                                        <option value="less"> < </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <input type="text"  name="vitalSignIdTo[]" id="vitalSignIdTo" size="2" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-sm-2">
                                                            <div class="row">
                                                                <div class="col-sm-8">
                                                                    <select name="unit[]" id="unit"  class="form-control minimal">
                                                                        <option value="inch" >inch</option>
                                                                        <option value="m" >m</option>
                                                                        <option value="cm">cm</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <img id="add_vital_row_<?php echo $num_s; ?>" style="cursor:pointer" src="<?php echo $GLOBALS['webroot']; ?>/library/images/add_small.png" alt="" onClick="addNewRowVitalSign('<?php echo $num_s; ?>');" />	
                                                                </div>
                                                            </div>
                                                        </div>	
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="col-sm-6">
                                        <div class="grpbox sitcarbx">
                                            <div class="head">
                                            <span>Labs</span>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="tblBg">
                                                <div id="lab_table" style="height:120px;overflow-y:auto;overflow-x:hidden">
                                                <?php if($labSaved){ echo $labSaved; } ?>
                                                    <div class="row pt10">
                                                        <div class="col-sm-11">
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <select name="txt_lab_name[]" class="form-control minimal" id="txt_lab_name" >
                                                                        <option value=''>Select</option>
                                                                        <?php
                                                                            $lab_qry = "select lab_radiology_name, lab_radiology_tbl_id from lab_radiology_tbl where lab_radiology_status = '0' and lab_type='Lab'";
                                                                            $lab_run = imw_query($lab_qry);
                                                                            while($lab_fet=imw_fetch_array($lab_run)){?>
                                                                                <option value="<?php echo $lab_fet['lab_radiology_name']; ?>" ><?php echo $lab_fet['lab_radiology_name']; ?></option>
                                                                        <?php	
                                                                            }														
                                                                        ?>
                                                                    </select>	
                                                                    <input type="hidden" name="lab_alert_id[]" id="lab_alert_id">
                                                                </div>	
                                                                
                                                                <div class="col-sm-4">
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <select id="txt_lab_from_criteria" name="txt_lab_from_criteria[]" class="form-control minimal">
                                                                                <option></option>
                                                                                <option value="greater"> > </option>											
                                                                                <option value="greater_equal"> >= </option>
                                                                                <option value="equalsto"> = </option>
                                                                                <option value="less_equal"> <= </option>
                                                                                <option value="less"> < </option>
                                                                            </select>
                                                                        </div>	
                                                                        <div class="col-sm-6">
                                                                            <input type="text"  name="txt_lab_from_val[]" class="form-control"/>
                                                                        </div>	
                                                                    </div>	
                                                                </div>
                                                                
                                                                <div class="col-sm-4">
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <select id="vt_to_criteria" name="txt_lab_to_criteria[]" class="form-control minimal">
                                                                                <option></option>
                                                                                <option value="greater"> > </option>											
                                                                                <option value="greater_equal"> >= </option>
                                                                                <option value="equalsto"> = </option>
                                                                                <option value="less_equal"> <= </option>
                                                                                <option value="less"> < </option>
                                                                            </select>
                                                                        </div>	
                                                                        <div class="col-sm-6">
                                                                            <input type="text"  name="txt_lab_to_val[]" id="txt_lab_to_val" class="form-control">
                                                                        </div>	
                                                                    </div>	
                                                                </div>
                                                            </div>	
                                                        </div>
                                                        
                                                        <div class="col-sm-1">
                                                            <img id="add_lab_row_<?php echo $num_l; ?>" style="cursor:pointer" src="<?php echo $GLOBALS['webroot']; ?>/library/images/add_small.png" onClick="addNewRowLab('<?php echo $num_l; ?>');" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>	
                            </div>	
                        </div>
            
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="sitecarft">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <select id="ratio_criteria_from" name="ratio_criteria_from" class="form-control minimal">
                                                            <option></option>
                                                            <option value="greater" <?php if($cdRatio_from_expr=="greater"){ echo " SELECTED ";} ?>> > </option>											
                                                            <option value="greater_equal" <?php if($cdRatio_from_expr=="greater_equal"){ echo " SELECTED ";} ?>> >= </option>
                                                            <option value="equalsto" <?php if($cdRatio_from_expr=="equalsto"){ echo " SELECTED ";} ?>> = </option>
                                                            <option value="less_equal" <?php if($cdRatio_from_expr=="less_equal"){ echo " SELECTED ";} ?>> <= </option>
                                                            <option value="less" <?php if($cdRatio_from_expr=="less"){ echo " SELECTED ";} ?>> < </option>
                                                        </select>	
                                                    </div>	
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="cdRatio">From</label>
                                                        <input type="text" class="form-control"  name="cdRatio" id="cdRatio" value="<?php echo $cdRatio;?>">
                                                    </div>	
                                                </div> 
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="ratio_criteria_to">C/D Ratio</label>
                                                        <select id="ratio_criteria_to" name="ratio_criteria_to" class="form-control minimal">
                                                            <option></option>
                                                            <option value="greater" <?php if($cdRatio_to_expr=="greater"){ echo " SELECTED ";} ?>> > </option>											
                                                            <option value="greater_equal" <?php if($cdRatio_to_expr=="greater_equal"){ echo " SELECTED ";} ?>> >= </option>
                                                            <option value="equalsto" <?php if($cdRatio_to_expr=="equalsto"){ echo " SELECTED ";} ?>> = </option>
                                                            <option value="less_equal" <?php if($cdRatio_to_expr=="less_equal"){ echo " SELECTED ";} ?>> <= </option>
                                                            <option value="less" <?php if($cdRatio_to_expr=="less"){ echo " SELECTED ";} ?>> < </option> 
                                                        </select>
                                                    </div>	
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="cdRatio_to">To</label>
                                                        <input type="text" class="form-control"  name="cdRatio_to" id="cdRatio_to" value="<?php echo $cdRatio_to;?>">
                                                    </div>	
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="cdRatio_od_os">Site</label>
                                                        <select   name="cdRatio_od_os" id="cdRatio_od_os" class="form-control minimal">
                                                            <option value="OD" <?php if($cdRatio_od_os=='OD') { echo 'selected'; }?>>OD</option>
                                                            <option value="OS" <?php if($cdRatio_od_os=='OS') { echo 'selected'; }?>>OS</option>
                                                            <option value="OU" <?php if($cdRatio_od_os=='OU' || $cdRatio_od_os=='') { echo 'selected'; }?>>OU</option>
                                                        </select>
                                                    </div>	
                                                </div>	
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label for="iopPressureValCondition" >From</label>
                                                        <select name="iopPressureValCondition" id="iopPressureValCondition"  class="form-control minimal">
                                                            <option value=""></option> 
                                                            <option value="equalsto" <?php if($iopPressure_Condition=='equalsto') { echo 'selected'; }?>>=</option> 
                                                            <option value="less" <?php if($iopPressure_Condition=='less') { echo 'selected'; }?>><</option>
                                                            <option value="greater" <?php if($iopPressure_Condition=='greater') { echo 'selected'; }?>>></option>
                                                            <option value="greater_equal" <?php if($iopPressure_Condition=='greater_equal') { echo 'selected'; }?>>>=</option>
                                                            <option value="less_equal" <?php if($iopPressure_Condition=='less_equal') { echo 'selected'; }?>><=</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="iopPressure" class="wordwrap">IOP pressure<sup> (TA)</sup> </label>
                                                        <input type="text" class="form-control"   name="iopPressure" id="iopPressure" value="<?php echo $iopPressure;?>" />
                                                    </div>	
                                                </div>	
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <select name="iopPressureValCondition_to" id="iopPressureValCondition_to"  class="form-control minimal">
                                                            <option value=""></option> 
                                                            <option value="equalsto" <?php if($iopPressure_Condition_to=='equalsto') { echo 'selected'; }?>>=</option> 
                                                            <option value="less" <?php if($iopPressure_Condition_to=='less') { echo 'selected'; }?>><</option>
                                                            <option value="greater" <?php if($iopPressure_Condition_to=='greater') { echo 'selected'; }?>>></option>
                                                            <option value="greater_equal" <?php if($iopPressure_Condition_to=='greater_equal') { echo 'selected'; }?>>>=</option>
                                                            <option value="less_equal" <?php if($iopPressure_Condition_to=='less_equal') { echo 'selected'; }?>><=</option>
                                                        </select>
                                                    </div>
                                                </div>	
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="iopPressure_to">To</label>
                                                        <input type="text" class="form-control"   name="iopPressure_to" id="iopPressure_to" value="<?php echo $iopPressure_to;?>" />
                                                    </div>
                                                </div>	
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label  for="iopPressure_od_os">Site</label>
                                                        <select name="iopPressure_od_os" id="iopPressure_od_os"  class="form-control minimal">
                                                            <option value="OD" <?php if($iopPressure_od_os=='OD') { echo 'selected'; }?>>OD</option>
                                                            <option value="OS" <?php if($iopPressure_od_os=='OS') { echo 'selected'; }?>>OS</option>
                                                            <option   value="OU" <?php if($iopPressure_od_os=='OU' || $iopPressure_od_os=='') { echo 'selected'; }?>>OU</option>
                                                            <option value="OD_OS" <?php if($iopPressure_od_os=='OD_OS') { echo 'selected'; }?>>OD / OS</option>
                                                        </select>	
                                                    </div>
                                                </div>	
                                            </div>	
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Age From</label>
                                                        <input type="text" class="form-control"  name="ageFrom" value="<?php echo $ageFrom;?>" onBlur="chkNumericVal(this,'Age From');">
                                                    </div>	
                                                </div>	
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Age To</label>
                                                        <input type="text" name="ageTo" class="form-control"  value="<?php echo $ageTo;?>" nBlur="chkNumericVal(this,'Age To');">
                                                    </div>		
                                                </div>	
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select  name="status" id="status" class="form-control minimal" >
                                                            <option value="1" <?php if($status=='1') { echo 'selected'; }?>>on</option>
                                                            <option value="0" <?php if($status=='0') { echo 'selected'; }?>>off</option>
                                                        </select>
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
                <div class="col-sm-12">
                    <div class="row">
                        <div id="divButtons" class="text-center col-sm-12">
                            <div id="module_buttons" class="ad_modal_footer">
                                <input class="btn btn-success" id="save" type="button" name="save" value="Save" onClick="checkFrm();">
                                <input class="btn btn-danger" id="close" type="button" name="close" value="Cancel" onClick="window.close();">
                            </div>
                        </div>	
                    </div>	
                </div>	
            </div>
    	</div>
		</form>
    </div>    
</div>    
    
<script type="text/javascript">
	<?php 
		if($rqRf == "y"){
			?>
			opener.top.fmain.winReload();
			<?php
		}
	?>

<?php
$qry_med="SELECT medicine_name from medicine_data where del_status = '0'";
		$res_med=imw_query($qry_med);
		$med_arr=array();
		while($med_val=imw_fetch_assoc($res_med)){
			$med_arr[]=$med_val['medicine_name'];
		}
		$js_arr_med = json_encode($med_arr);
		echo 'var med_code_arr ='.$js_arr_med.';';
?>
$('#medication').typeahead({source:med_code_arr});
// Need to create xml file for Allergies
customarrayTitle = new Array(<?php print $strAllergyTitle; ?>);
var j = '<?php print $j; ?>';
//new actb(document.getElementById('textTitleA'+j),customarrayTitle,1);
$('#textTitleA'+j).typeahead({source:customarrayTitle});

function get_modal(src){
	var modal_body = '<div class="col-sm-12 text-center"><div class="row"><img src="'+src+'" style="width:100%;height:auto"></div></div>';
	show_modal('div_upload_doc','Site care uploaded document',modal_body,'','570','modal-lg');
}

$(document).ready(function(){
	$('.date-pick').datetimepicker({
		timepicker:false,
		format:window.opener.global_date_format, //'m-d-Y',
		formatDate:'Y-m-d',
		scrollInput:false
	});
	
	$('.allergy_typehead').each(function(id,elem){
		$(elem).typeahead({source:customarrayTitle});
	});
	
	$("#medication1 option:Selected").remove();
	$("#problem1 option:Selected").remove();
	$("#cpt1 option:Selected").remove();
	$("#allergy1 option:Selected").remove();
	
	<?php if(count($saved_med)>8){ ?>	
	$("#scp_div_disable").width(parseInt($("#medication").width()-15)+"px");	
	<?php }
	 if(count($dxCodeIdArr)>8){ ?>
		$("#scp_div_prob").width(parseInt($("#elemDxCodeId").width()-15)+"px");
	<?php } 
	if(count($cptCodeIdArr)>8){ ?>	
		$("#scp_div_cpt").width(parseInt($("#medication").width()-15)+"px");
	<?php }
	if(count($cptCodeIdArr)>8){ ?>	
		$("#scp_div_allergy").width(parseInt($("#medication").width()-15)+"px");
	<?php } ?> 
		
});
$(window).load(function(){
	if(typeof(window.opener.top.innerDim)=='function'){
		var innerDim = window.opener.top.innerDim();
		if(innerDim['w'] > 1600) innerDim['w'] = 1600;
		if(innerDim['h'] > 900) innerDim['h'] = 900;
		window.resizeTo(innerDim['w'],innerDim['h']);
		brows	= get_browser();
		if(brows!='ie') innerDim['h'] = innerDim['h']-35;
		var result_div_height = innerDim['h']-210;
		//$('.mainwhtbox').height(result_div_height+'px');
	}
});


	<?php if($_REQUEST['rec']=='ins'){ ?>
			top.alert_notification_show('SCP Record has been inserted');
	<?php } else if($_REQUEST['rec']=='upd'){?>
			top.alert_notification_show('SCP Record has been updated');
	<?php } ?>

</script>
<input type="hidden" id="last_cnt_vital">
</body>
</html>