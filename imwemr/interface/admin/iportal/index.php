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

if($_REQUEST['appt_type_req']=='y'){
	$retVal=$apptName="";
	if(trim($_REQUEST['add_appt']) && trim($_REQUEST['appt_edit_id'])==""){
		$apptName=trim(addslashes($_REQUEST['add_appt']));
		$qryInsAppt="INSERT INTO iportal_req_appt_type set appt_type_name='".$apptName."'";
		$resInsAppt=imw_query($qryInsAppt);
		
	}else if(is_numeric($_REQUEST['del_appt'])){
		$qryDelAppt="UPDATE iportal_req_appt_type set del_status='1' WHERE id='".$_REQUEST['del_appt']."'";
		$resDelAppt=imw_query($qryDelAppt);
	}else if(trim($_REQUEST['add_appt']) && is_numeric($_REQUEST['appt_edit_id'])){
		$apptName=trim(addslashes($_REQUEST['add_appt']));
		$qryUpdAppt="UPDATE iportal_req_appt_type set appt_type_name='".$apptName."' WHERE id='".$_REQUEST['appt_edit_id']."'";
		$resDelAppt=imw_query($qryUpdAppt);
	}
	
	$iportal_appt_type = "SELECT id,appt_type_name FROM iportal_req_appt_type WHERE del_status !='1' ORDER BY appt_type_name";
	$iportal_res_appt_type = imw_query($iportal_appt_type) or die(imw_error());
	$i=0;	
	$retVal='	
		<table style="text-align:left;" cellpadding="0" cellspacing="0">
			<tr style="font-weight:bold"> 
				<td style="width:15%; text-align:center; padding-left:5px;">#</td>
				<td style="width:70%;padding-left:5px;">Name</td>
				<td style="width:15%;padding-left:5px;">Action</td>
			</tr>';
		while($iportalRow_appt_type = imw_fetch_assoc($iportal_res_appt_type)){
			$i++;
			$retVal.='
			 <tr>
				<td style="width:15%;padding-left:6px;">'.$i.'.</td>
				<td style="width:70%;padding-left:6px;cursor:pointer;" onClick="add_appt_type(\'edit\','.$iportalRow_appt_type['id'].',\''.$iportalRow_appt_type['appt_type_name'].'\')" >'.$iportalRow_appt_type['appt_type_name'].'</td>
				<td style="width:15%;padding-left:5px;text-align:center;">
				<img style="cursor:pointer;border:0px;" src="../../../images/del.png" onClick="add_appt_type(\'del\','.$iportalRow_appt_type['id'].')"> </td>
			</tr>';
		}
		$retVal.='</table>';
		
	echo $retVal;
	die();
}

if($_REQUEST['unlock_pt_id']){
	$qryPatientUnLock="Update patient_data set locked=0 where id='".imw_real_escape_string($_REQUEST['unlock_pt_id'])."'";
	$resPatientUnLock=imw_query($qryPatientUnLock);
	
}else{
	if($_POST){
        $iportal_payments_settings=(isset($_POST['iportal_payments_settings']) && $_POST['iportal_payments_settings']!='')?$_POST['iportal_payments_settings']:0;
        $iportal_pos_device=(isset($_POST['iportal_pos_device']) && $_POST['iportal_pos_device']!='' && $iportal_payments_settings!=0)?$_POST['iportal_pos_device']:0;
        $iportal_pos_user=(isset($_POST['iportal_pos_user']) && $_POST['iportal_pos_user']!='' && $iportal_payments_settings!=0)?$_POST['iportal_pos_user']:0;
        $iportal_def_facility=(isset($_POST['iportal_def_facility']) && $_POST['iportal_def_facility']!='' && $iportal_payments_settings!=0)?$_POST['iportal_def_facility']:0;
        
		$iportal_view_access=$_POST['iportal_patient_view_access'];
		$iportal_update_access=$_POST['iportal_patient_update_access'];
		$iportal_default_username=$_POST['iportal_default_username'];
		$iportal_default_password=$_POST['iportal_default_password'];
		$iportal_instructions_detail=addslashes($_POST['iportal_instructions_detail']);
		$iportal_eve_val=$_POST['iportal_eve'];
		$gl_rx=$_POST['gl_rx'];
		$add_appt_from_iportal=$_POST['add_appt_from_iportal'];
		$show_physician_rating=($_POST['show_physician_rating_iportal'])?$_POST['show_physician_rating_iportal']:"0";
		$iportal_eve=$iportal_eve_val."~||~".$gl_rx."~||~".$add_appt_from_iportal."~||~".$show_physician_rating;
		$consentForm=$_POST['consentform'];
		$consentPack=$_POST['consentPackform'];
		$iportal_billing_statement=addslashes(trim($_REQUEST['iportal_billing_statement']));
		$iportal_statement_desc="";
		if($iportal_billing_statement==1){
			$iportal_statement_desc=addslashes(trim($_REQUEST['iportal_statement_desc']));
		}
		
		$prescription=$_REQUEST['gl_rx'];
		$iportal_prescription_desc="";
		if($prescription==1){
			$iportal_prescription_desc=addslashes(trim($_REQUEST['iportal_prescription_desc']));
		}
		$iportal_default_conf_msg=addslashes(trim($_REQUEST['iportal_default_conf_msg']));
		$consentPackIds="";
		if($consentPack){
			$b=0;
			foreach($consentPack as $consetPackVal){
				if($consetPackVal){
					$b++;
					if($b==1){
						$consentPackIds.=$consetPackVal;	
					}else{
						$consentPackIds.=",".$consetPackVal;	
					}
				}
			}	
		}
		$consentValIds="";
		if($consentForm){
			$a=0;
			foreach($consentForm as $consentVal){
				if($consentVal){
					$a++;
					if($a==1) {
						$consentValIds.=$consentVal;	
					}else {
						$consentValIds.=",".$consentVal;
					}
				}
			}	
		}
		
		$qryUpdate="update facility set iportal_patient_view_access='".$iportal_view_access."',iportal_patient_update_access='".$iportal_update_access."',iportal_default_username='".$iportal_default_username."',iportal_default_password='".$iportal_default_password."',iportal_consent='".$consentValIds."',iportal_package_consent='".$consentPackIds."',iportal_instructions_detail='".$iportal_instructions_detail."', iportal_eve='".$iportal_eve."',dis_iportal='".$_REQUEST['dis_iportal']."',iportal_reg_patient='".$_REQUEST['iportal_reg_patient']."',iportal_req_appointment='".$_REQUEST['iportal_req_appointment']."',iportal_dis_all_appointment='".$_REQUEST['iportal_dis_all_appointment']."',iportal_dis_cl='".$_REQUEST['iportal_dis_cl']."',iportal_billing_statement='".$iportal_billing_statement."',iportal_billing_statement_desc='".$iportal_statement_desc."',iportal_prescription_desc='".$iportal_prescription_desc."',iportal_default_conf_msg='".$iportal_default_conf_msg."',iportal_payments_settings='".$iportal_payments_settings."',iportal_pos_device='".$iportal_pos_device."',iportal_pos_user='".$iportal_pos_user."',iportal_def_facility='".$iportal_def_facility."',iportal_lab_report='".$iportal_lab_report."',iportal_diagnosis_imgage='".$iportal_diagnosis_imgage."',iportal_diagnosis_care_plan='".$iportal_diagnosis_care_plan."' where facility_type=1";
		$resUpdate=imw_query($qryUpdate);
		$iportal_direct_email = $_POST["iportal_direct_email"];
		$iportal_direct_password = $_POST["iportal_direct_password"];
		$iportal_direct_updox_id = $_POST["iportal_direct_updox_id"];
		$iportal_URL=$_POST["iportal_URL"];
		$email_subject_reminder=trim(addslashes($_POST["reports_recall_emails_subject_details"])); //RECALL REPORTS EMAIL LETTER SUBJECT DYNAMIC WORK
		if(trim($iportal_direct_email) != "" && trim($iportal_direct_password) != "")
		{
			$pp_qry = "SELECT * FROM default_patient_direct_credentials";
			$pp_qry_obj = imw_query($pp_qry);	
			if(imw_num_rows($pp_qry_obj) > 0)
			{
				$req_qry_pp = "UPDATE default_patient_direct_credentials SET default_email = '".$iportal_direct_email."', default_pass = '".$iportal_direct_password."',iportal_URL='".$iportal_URL."', email_subject_reminder='".$email_subject_reminder."', default_updox_id = '".$iportal_direct_updox_id."' ";
				imw_query($req_qry_pp);	
			}
			else
			{
				$req_qry_pp = "INSERT INTO default_patient_direct_credentials SET default_email = '".$iportal_direct_email."', default_pass = '".$iportal_direct_password."',iportal_URL='".$iportal_URL."', email_subject_reminder='".$email_subject_reminder."', default_updox_id = '".$iportal_direct_updox_id."' ";
				imw_query($req_qry_pp);					
			}
		}
		
		//if($_REQUEST['child_proc_chkbox']){ //this condition creating prblen when we do want to save nothing
	
			$apptProcArr=($_REQUEST['child_proc_chkbox']);
			$str_proc_Arr=htmlentities(serialize($apptProcArr));
			
			$select_iportal="Select id FROM iportal_req_appt_type LIMIT 0,1";
			$res_iportal=imw_query($select_iportal);
			$qry_set=" INSERT INTO ";
			$whr_req="";
			if(imw_num_rows($res_iportal)>0){
				$row_iportal=imw_fetch_assoc($res_iportal);
				$row_iportal_id=$row_iportal["id"];
				$qry_set=" UPDATE ";
				$whr_req=" WHERE id='".$row_iportal_id."'";
			}
				
			$qryUpdAppt_new=$qry_set." iportal_req_appt_type set appt_type_name='".imw_real_escape_string($str_proc_Arr)."' ".$whr_req;
			$resDelAppt=imw_query($qryUpdAppt_new) or die(imw_error());
				
			
		//}
		if($_REQUEST["all_facilities"]){
			$arr_all_facilites=explode(",",$_REQUEST["all_facilities"]);
			$saved_facilities=array();
			if($_REQUEST["faclities"]){
				$saved_facilities=$_REQUEST["faclities"];
			}
			$facility_unset = array_diff($arr_all_facilites, $saved_facilities);
			
			if(count($facility_unset)>0){
				$unset_fac_id=implode($facility_unset,",");
				$update_unset_facility="update facility set show_in_ptportal='' WHERE id in(".$unset_fac_id.")";
				$res_update_unset_facility=imw_query($update_unset_facility);
			}
			if($saved_facilities){
				$set_fac_id=implode($saved_facilities,",");
				$update_set_facility="update facility set show_in_ptportal='1' WHERE id in(".$set_fac_id.")";
				$res_update_set_facility=imw_query($update_set_facility);
			}
		}
				
		$msg="";				
		if($resUpdate){
			$msg="Record Updated Sucessfully";	
		}
	}
}

$qryDirectCredentials = "SELECT * FROM default_patient_direct_credentials";
$qryDirectCredentials_obj = imw_query($qryDirectCredentials);
$rs_direct_credentials = imw_fetch_assoc($qryDirectCredentials_obj);
$iportal_direct_email_val = $rs_direct_credentials["default_email"];
$iportal_direct_password_val = $rs_direct_credentials["default_pass"];
$iportal_direct_updox_id_val = $rs_direct_credentials["default_updox_id"];
$iportal_URL = $rs_direct_credentials["iportal_URL"];
$emailSubjectReminder = stripslashes($rs_direct_credentials["email_subject_reminder"]);


$qryFacility="SELECT iportal_patient_view_access,iportal_patient_update_access,iportal_default_username,iportal_default_password,iportal_consent,iportal_package_consent,iportal_instructions_detail,iportal_eve,dis_iportal,iportal_billing_statement,iportal_billing_statement_desc,iportal_prescription_desc,iportal_default_conf_msg, iportal_reg_patient, iportal_req_appointment, iportal_dis_all_appointment, iportal_dis_cl,iportal_lab_report,iportal_diagnosis_imgage,iportal_diagnosis_care_plan from facility where facility_type=1";

$resFacility=imw_query($qryFacility)or die(imw_error());
$rs_d=imw_fetch_assoc($resFacility);
$iportal_patient_view_access   = $rs_d["iportal_patient_view_access"];
$iportal_patient_update_access = $rs_d["iportal_patient_update_access"];
$iportal_default_username      = $rs_d["iportal_default_username"];
$iportal_default_password      = $rs_d["iportal_default_password"];
$iportal_consent		       = $rs_d["iportal_consent"];
$iportal_consent_package	   = $rs_d["iportal_package_consent"];
$iportal_instructions_detail   = stripslashes($rs_d["iportal_instructions_detail"]);
$iportal_billing_statement	   = $rs_d["iportal_billing_statement"];
$iportal_billing_statement_desc= stripslashes($rs_d["iportal_billing_statement_desc"]);
$iportal_prescription_desc	   = stripslashes($rs_d["iportal_prescription_desc"]);
$iportal_default_conf_msg	   = stripslashes($rs_d["iportal_default_conf_msg"]);			
list($eve,$glrx,$add_appt_from_iportal_chk,$show_physician_rating_iportal)=explode("~||~",$rs_d["iportal_eve"]);
$dis_iportal = $rs_d['dis_iportal'];
$iportal_reg_patient = $rs_d['iportal_reg_patient'];
$iportal_req_appointment = $rs_d['iportal_req_appointment'];
$iportal_dis_all_appointment = $rs_d['iportal_dis_all_appointment'];
$iportal_dis_cl = $rs_d['iportal_dis_cl'];
$iportal_lab_report = $rs_d['iportal_lab_report'];
$iportal_diagnosis_imgage = $rs_d['iportal_diagnosis_imgage'];
$iportal_diagnosis_care_plan = $rs_d['iportal_diagnosis_care_plan'];
if($iportal_consent){
	$patConsentArr=explode(",",$iportal_consent);	
}
if($iportal_consent_package){
	$patConsentPackArr=explode(",",$iportal_consent_package);		
}
$consentQry = "SELECT cf.consent_form_id,cf.consent_form_name,cf.cat_id,cc.category_name as consent_category_name FROM consent_form cf 
			   INNER JOIN consent_category cc ON(cc.cat_id=cf.cat_id)
			   WHERE 1=1 ORDER BY cc.category_name,cf.consent_form_name	
			  ";
$consentRes = imw_query($consentQry) or die(imw_error());
$consent_form_arr = array();
if(imw_num_rows($consentRes)>0) {
	while($consentRow = imw_fetch_array($consentRes)) {
		$consent_form_id 								= $consentRow['consent_form_id'];
		$consent_form_name 								= stripslashes($consentRow['consent_form_name']);
		$consent_category_name 							= stripslashes($consentRow['consent_category_name']);
		$consent_form_arr[$consent_form_id] 			= $consent_form_name;
		$consent_category_name_arr[$consent_form_id] 	= $consent_category_name;
	}
}

?>
	<script type="text/javascript">
	// calculate the current window height //
	function pageHeight() { return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;}
	function submit_form(){
		frm_iportal.submit();
	}
	var mode='';
	function iPortalConsent(){
		var str_path = "../iportal/";
		url=str_path+'open.php';
		top.popup_win(url,'ap2','width=800,height=490,resizable =0,scrollbars=0,titlebar=0,menubar=no,location=no,left=10');
	}  
	function ulock_pt(obj){
		if(obj){
			pt_id=obj;
			if(document.getElementById('unlock_pt_id')){
				document.getElementById('unlock_pt_id').value=obj;	
				frm_iportal.submit();
			}
		}
	}
	function add_appt_type(action_type,appt_id,appt_val,delConf){
		var objVal=document.getElementById('appt_txt');
		apptValue=objVal.value;
		if(action_type=='save'){
			if(apptValue){
				var edit_id_con="";
				msg="added"
				if($('#edit_appt_id').val()!=""){
					edit_id_con='&appt_edit_id='+$('#edit_appt_id').val();	
					msg="updated";
				}
				
				ptinj_ajax_url = 'index.php?appt_type_req=y&add_appt='+apptValue+edit_id_con;
				$.ajax({
					url: ptinj_ajax_url,
					success: function(respRes){
						if(respRes){
							objVal.value="";
							$('#edit_appt_id').val('');
							$('#div_appt_type').html(respRes);
							top.alert_notification_show("Appointment type "+msg+" sucessfully");
						}
					}
				});
			}
		}else if(action_type=='del'){
			if(appt_id && delConf=="yes"){
				ptinj_ajax_url = 'index.php?appt_type_req=y&del_appt='+appt_id;
				$.ajax({
					url: ptinj_ajax_url,
					success: function(respRes){
						if(respRes){
							objVal.value="";
							$('#div_appt_type').html(respRes);
							top.alert_notification_show("Appointment type deleted sucessfully");
							$('#edit_appt_id').val('');
						}
					}
				});
			}else{
				top.fancyConfirm("Are you sure to delete?","", "window.top.fmain.add_appt_type('del',"+appt_id+",'','yes')");	
			}
		}else if(action_type=='edit'){
			$('#edit_appt_id').val(appt_id);
			$('#appt_txt').val(appt_val);
			
		}
	}
	function checked_uncheck(obj1,obj_v){
		if(obj1){
			$("#"+obj_v).attr('disabled',true);
			if($("#"+obj1).is(":checked")){
				$("#"+obj_v).removeAttr('disabled');
			}
		}
	}
	</script>
	</head>
<body>
<div class="whtbox">
	<form name="frm_iportal" id="frm_iportal" method="post">
		<input type="hidden" name="iportal_patient_view_access" id="iportal_patient_view_access" value="1">
		<input type="hidden" name="iportal_patient_update_access" id="iportal_patient_update_access" value="1">
		<div class="tblBg">
			<div class="row">
				<div class="col-sm-9">
					<div class="row pt10 prnt_left_col">
						<div class="col-sm-12">
							<div class="adminbox">
								<div class="head">
									<span>Default Authorization Settings</span>	
								</div>
								<div class="tblBg">
									<div class="row">
										<div class="col-sm-12">
											<div class="adminbox">
												<div style="margin-top:5px;margin-bottom:10px;">
													<div class="row">
														<div class="col-sm-3">
															<span class="head" style="border-bottom: none!important;">Patient Direct Credentials</span>
														</div>
														<div class="col-sm-9 content_box">
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($dis_iportal==1){ echo "checked"; } ?> name="dis_iportal" value="1" id="dis_iportal" /> 	<label for="dis_iportal">Disable iPortal Information</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($eve==1){ echo "checked"; } ?> name="iportal_eve" value="1" id="eve_val" />
																<label for="eve_val">Evening Time Slot</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($add_appt_from_iportal_chk==1){ echo "checked"; } ?> name="add_appt_from_iportal" value="1" id="add_appt_from_iportal" />
																<label for="add_appt_from_iportal">Add Appointment From iPortal</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($show_physician_rating_iportal==1){ echo "checked"; } ?> name="show_physician_rating_iportal" value="1" id="show_physician_rating_iportal" />
																<label for="show_physician_rating_iportal">Show Physician Rating</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_reg_patient==1){ echo "checked"; } ?> name="iportal_reg_patient" value="1" id="iportal_reg_patient" />
																<label for="iportal_reg_patient">Disable Register New Patient</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_req_appointment==1){ echo "checked"; } ?> name="iportal_req_appointment" value="1" id="iportal_req_appointment" />
																<label for="iportal_req_appointment">Disable Request Appointment</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_dis_all_appointment==1){ echo "checked"; } ?> name="iportal_dis_all_appointment" value="1" id="iportal_dis_all_appointment" />
																<label for="iportal_dis_all_appointment">Show Physician Appointments</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_dis_cl==1){ echo "checked"; } ?> name="iportal_dis_cl" value="1" id="iportal_dis_cl" />
																<label for="iportal_dis_cl">Show Contact Lens</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_lab_report==1){ echo "checked"; } ?> name="iportal_lab_report" value="1" id="iportal_lab_report" />
																<label for="iportal_lab_report">Show Lab Reports</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_diagnosis_imgage==1){ echo "checked"; } ?> name="iportal_diagnosis_imgage" value="1" id="iportal_diagnosis_imgage" />
																<label for="iportal_diagnosis_imgage">Show Diagnosis Image Reports</label>
															</div>
															<div class="checkbox checkbox-inline">
																<input type="checkbox" <?php if($iportal_diagnosis_care_plan==1){ echo "checked"; } ?> name="iportal_diagnosis_care_plan" value="1" id="iportal_diagnosis_care_plan" />
																<label for="iportal_diagnosis_care_plan">Show Diagnosis and Care Plan</label>
															</div>															
														</div>	
													</div>
												</div>
												<div class="tblBg">
													<div class="row">
														
														<div class="col-sm-2">
															<label>Direct Email</label>  
															<input type="text" class="form-control" name="iportal_direct_email" value="<?php echo $iportal_direct_email_val; ?>" />
														</div>
														<div class="col-sm-2">
															<label>Direct Password</label>
															<input type="text" class="form-control" name="iportal_direct_password" value="<?php echo $iportal_direct_password_val; ?>" />
														</div>
														<div class="col-sm-4">
															<label>iPortal URL</label>  
															<input type="text" class="form-control" name="iportal_URL" value="<?php echo $iportal_URL; ?>" />
														</div>
														<div class="col-sm-4">
															<label>Instructions Detail</label>
															<textarea class="form-control" rows="1" name="iportal_instructions_detail" id="iportal_instructions_detail"><?php echo $iportal_instructions_detail; ?></textarea>
														</div>
													</div>
													<div class="row">
														<div class="col-sm-2">
															<?php if(is_updox('direct')){ ?>
																<label>Updox Direct user id</label>
																<input type="text" class="form-control" name="iportal_direct_updox_id" value="<?php echo $iportal_direct_updox_id_val; ?>" />
															<?php } ?>
														</div>
														<div class="col-sm-2">
														</div>
														<div class="col-sm-4">
															<label>Recall Email Subject</label>
															<input type="text" class="form-control" name="reports_recall_emails_subject_details" value="<?php echo $emailSubjectReminder; ?>" />
														</div>
														<div class="col-sm-4">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>	
									<div class="row">
										<div class="col-sm-4">
											<div class="adminbox">
												<div class="head">
													<span>Billing</span>	
												</div>
												<div class="tblBg">
													<div class="row">
														<div class="col-sm-5">
															<label>Patient Statements</label>	
														</div>
														<div class="col-sm-7 text-right">
															<div class="checkbox text-center">
																<input onClick="checked_uncheck('iportal_billing_statement','iportal_statement_desc')" type="checkbox" name="iportal_billing_statement" id="iportal_billing_statement" value="1" <?php if($iportal_billing_statement==1){ echo "checked"; } ?>/><label for="iportal_billing_statement">Do Not Show Statements</label>
															</div>	
														</div>	
														<div class="col-sm-12">
															<textarea <?php if(!$iportal_billing_statement){ echo 'disabled'; }?> name="iportal_statement_desc" id="iportal_statement_desc" class="form-control" rows="2"><?php echo $iportal_billing_statement_desc;?></textarea>
														</div>	
													</div>	
												</div>	
											</div>	
										</div>
										
										<div class="col-sm-4">
											<div class="adminbox">
												<div class="head">
													<span>Prescription</span>	
												</div>
												<div class="tblBg">
													<div class="row">
														<div class="col-sm-5">
															<label>&nbsp;</label>	
														</div>
														<div class="col-sm-7 text-right">
															<div class="checkbox text-center">
																<input onClick="checked_uncheck('gl_rx','iportal_prescription_desc')" type="checkbox" <?php if($glrx==1){ echo "checked"; } ?> name="gl_rx" value="1" id="gl_rx" /><label for="gl_rx">Do Not Show Prescription</label>
															</div>	
														</div>	
														<div class="col-sm-12">
															<textarea <?php if(!$glrx){echo 'disabled';} ?> name="iportal_prescription_desc" id="iportal_prescription_desc" class="form-control" rows="2"><?php echo $iportal_prescription_desc;?></textarea>
														</div>	
													</div>	
												</div>	
											</div>	
										</div>
										
										<div class="col-sm-4">
											<div class="adminbox">
												<div class="head">
													<span>Default Confirmation Message</span>	
												</div>
												<div class="tblBg">
													<div class="row">
														<div class="col-sm-12">
															<label>&nbsp;</label>
															<textarea name="iportal_default_conf_msg" id="iportal_default_conf_msg" class="form-control" rows="2"><?php echo $iportal_default_conf_msg;?></textarea>
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
					
					<div class="row">
						<div class="col-sm-4 left_col">
							<div class="adminbox">
								<div class="head">
									<span>Locked Patients</span>	
								</div>
								<div class="tblBg">
									<div class="row">
										<div class="table-responsive respotable" id="div_locked_pt">
											<table class="table table-bordered adminnw">
												<thead>
													<tr>
														<th align="center" valign="middle">Patient ID</th>
														<th valign="middle">Patient Name</th>
														<th valign="middle">Action</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$qryPatientLock="Select id,fname,lname from patient_data where locked=1 order by fname";
													$resPatientLock=imw_query($qryPatientLock);
													if(imw_num_rows($resPatientLock)>0){?>
													<?php 
													while($rowPatient=imw_fetch_assoc($resPatientLock)){
													$i++;
													?>
													<tr>
														<td><?php echo $rowPatient['id']; ?></td>
														<td><?php echo $rowPatient['fname']." ".$rowPatient['lname']; ?></td>
														<td><input type="button"  class="btn btn-primary" value="Unlock" id="<?php echo $rowPatient['id']; ?>" onClick="ulock_pt(this.id)"></td>
													</tr>

													<?php
													}
													}else {
													echo '<tr><td colspan="3" align="center">No Record Found</td></tr>';
												}?>
												</tbody>
											</table><input type="hidden" name="unlock_pt_id" id="unlock_pt_id" /> 
										</div>
									</div>	
								</div>	
							</div>
						</div>

						<div class="col-sm-4 left_col">
							<div class="adminbox">
								<div class="head">
									<span>Facility</span>	
								</div>
								<div class="tblBg">
									<div class="row">
										<div class="table-responsive respotable adminnw" id="div_facility">
											<table class="table table-bordered">
											<?php
												$iportalQryFac = "SELECT id,name,facility_type,show_in_ptportal from facility ORDER by name";
												$iportalResFac = imw_query($iportalQryFac) or die(imw_error());
												$i=0;$arr_facilities=array();
												if(imw_num_rows($iportalResFac)>0){
												while($iportalRowFac = imw_fetch_assoc($iportalResFac)) {
												$facility_type=$iportalRowFac["facility_type"];
												$checked=($iportalRowFac["show_in_ptportal"]==1)?" checked=checked ":"";
												$label_HQ=($facility_type==1)?"<b><sup>HQ</sup></b>":"";
												$i++;
												$arr_facilities[]=$iportalRowFac['id'];
												?>
												<tr>
													<td style="width:30px;">
														<div class="checkbox">
															<input type="checkbox" <?php echo $checked; ?> name="faclities[]" id="faclities<?php echo $iportalRowFac['id']; ?>" value="<?php echo $iportalRowFac['id']; ?>">
															<label for="faclities<?php echo $iportalRowFac['id']; ?>"></label>
														</div>
													</td>
													<td>
														<label class="pointer" for="faclities<?php echo $iportalRowFac['id']; ?>"><?php echo $iportalRowFac['name'].$label_HQ; ?></label>
													</td>
												</tr>
												<?php
												}
												?>
												<?php }else{ echo "<tr><td id='td_consent_pckg' colspan='3' style=' vertical-align:top;font-weight:bold;'><center> No Record Found</center>"; } 
												$all_facilities=implode($arr_facilities,",")
												?>
											</table>
											<input type="hidden" name="all_facilities" value="<?php echo $all_facilities; ?>" />
										</div>
									</div>	
								</div>	
							</div>
						</div>
						
						<div class="col-sm-4 left_col">
							<div class="adminbox">
								<div class="head">
									<span>Consent Packages</span>
								</div>
								<div class="tblBg">
									<div class="row">
										<div class="table-responsive respotable adminnw" id="div_consent_packages">
											<table class="table table-bordered">
											<?php
												$iportalQry = "SELECT package_category_id, package_category_name, package_consent_form FROM consent_package WHERE delete_status!='yes' ORDER BY package_category_name";
												$iportalRes = imw_query($iportalQry) or die(imw_error());
												$i=0;
												if(imw_num_rows($iportalRes)>0){
    												while($iportalRow = imw_fetch_assoc($iportalRes))
    												{
        												$i++;
        												$checked="";
        												if(in_array($iportalRow['package_category_id'],$patConsentPackArr)){
        												    $checked="checked";	
        												}
        												?>
        												<tr>
        													<td style="width:30px;">
            													<div class="checkbox">
            														<input id="checkbox_form_<?php echo $i; ?>" <?php echo $checked; ?> type="checkbox" name="consentPackform[]" value="<?php echo $iportalRow['package_category_id']; ?>">
            														<label for="checkbox_form_<?php echo $i; ?>"></label>
            													</div>
        													</td>
        													<td>
        														<label class="pointer" for="checkbox_form_<?php echo $i; ?>"><?php echo $iportalRow['package_category_name']; ?></label>
        													</td>
        												</tr>
        												<?php
    												}
												}
												else
												{
												    echo "<tr><td id='td_consent_pckg' colspan='3' style=' vertical-align:top;font-weight:bold;'><center> No Record Found</center></td></tr>";
												} ?>
											</table>
										</div>
									</div>	
								</div>
							</div>
						</div>	
					</div>
				</div>	
				<div class="col-sm-3 right_col" style="height:300px;">
					<div class="row pt10">
						<div class="col-sm-12">
							<div class="adminbox">
								<div class="head">
									<span>APPOINTMENT REASON</span>	
								</div>
								<div class="tblBg">
									<div class="appt_div_blk">
										<?php 
										$qry_pro_req="select appt_type_name FROM iportal_req_appt_type LIMIT 0,1";
										$res_pro_req=imw_query($qry_pro_req);
										$row_pro_req=imw_fetch_assoc($res_pro_req);
										$arr_pro_req=unserialize(html_entity_decode($row_pro_req["appt_type_name"]));

										$iportal_appt_type = "SELECT sp1.id, sp1.proc FROM slot_procedures sp1 LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id WHERE sp1.times = '' AND sp1.proc != '' AND sp1.doctor_id = 0 AND LOWER(sp1.active_status)='yes' ORDER BY sp1.proc";
										$iportal_res_appt_type = imw_query($iportal_appt_type) or die(imw_error());
										$i=0;
										if(imw_num_rows($iportal_res_appt_type)>0){?>
										<div class="table-responsive respotable" id="div_appt_type">
											<table class="table table-bordered adminnw">
												<thead>
													<tr> 
														<th style="width:30px;">
															<div class="checkbox">
																<input type="checkbox" id="chk_sel_all">
																<label for="chk_sel_all"></label>
															</div>
														</th>
														<th><b>Procedure Name</b></th>
													</tr>
												</thead>
												<?php 
												while($iportalRow_appt_type = imw_fetch_assoc($iportal_res_appt_type)){
												$i++; 
												$proc_details=$iportalRow_appt_type['id']."~|~".$iportalRow_appt_type['proc'];
												$checked_proc="";
												if(in_array($proc_details,$arr_pro_req)){$checked_proc=" checked='checked' ";}
												?>
												<tr>
												<td style="width:30px;">
													<div class="checkbox">
														<input type="checkbox" class="chk_sel" name="child_proc_chkbox[]" id="child_proc_chkbox<?php echo $proc_details; ?>" value="<?php echo $proc_details; ?>" <?php echo $checked_proc; ?> />
														<label for="child_proc_chkbox<?php echo $proc_details; ?>"></label>
													</div>
												</td>
												<td><label class="pointer" style="white-space:inherit!important;" for="child_proc_chkbox<?php echo $proc_details; ?>"><?php echo $iportalRow_appt_type['proc']; ?></label></td>
												</tr>
												<?php
												}
												?>
											</table>
									  </div>	
									<?php } ?>
									</div>
								</div>	
							</div>
						</div>
					</div>		
				</div>	
			</div>
            
            <?php
                $login_facility=$_SESSION['login_facility'];
                
                $pos_device=false;
                $devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
                              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
                              WHERE device_status=0 
                              AND merchant_status=0
                              ";
                $resp = imw_query($devices_sql);
                if($resp && imw_num_rows($resp)>0){
                    $pos_device=true;
                }
            
                if($pos_device && $resp) {
                    $deiveSql="select iportal_payments_settings,iportal_pos_device,iportal_pos_user,iportal_def_facility from facility where facility_type=1";
                    $deiveRes=imw_query($deiveSql);
                    $deviceRow=imw_fetch_assoc($deiveRes);
                    
                    $devices_option = "";
                    while ($row = imw_fetch_assoc($resp)) {
                        $ipAddress=$row['ipAddress'];
                        $port=$row['port'];
                        $device_url=$phpHTTPProtocol.$ipAddress.':'.$port;
                        $selected=($row['d_id']==$deviceRow['iportal_pos_device'])?'selected="selected" ':'';
                        $devices_option .= "<option ".$selected." data-device_ip='".$ipAddress."' data-device_url='".$device_url."' value='" . $row['d_id'] . "'>" . $row['deviceName'] . "</option>";
                    }

                    $users_option='';
                    $sql = "select `id`,`fname`,`mname`,`lname`,`username`,`user_type`,`Enable_Scheduler` 
                            from `users` 
                            where (user_type IN(1,2,7,8,9,10,11,12,14) OR Enable_Scheduler = '1')
                            and `delete_status` = '0' 
                            order by `lname` ASC";
                    $sql_rs = imw_query($sql);
                    if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                        $users_option .= '<option value="">-Select User-</option>';
                        while ($row1 = imw_fetch_assoc($sql_rs)) {
                            $sel1=($row1['id']==$deviceRow['iportal_pos_user'])?' selected="selected" ':'';
                            $prov_name = core_name_format($row1['lname'], $row1['fname'], $row1['mname']);
                            $users_option .= '<option value="'.$row1['id'].'"  '.$sel1.'>' . $prov_name . '</opiton>';
                        }
                    }
                    
                    $facility_option='';
                    $fac_qry=imw_query("select id,name from facility order by name");
                    if ($fac_qry && imw_num_rows($fac_qry) > 0) {
                        $facility_option .= '<option value="">-Select Facility-</option>';
                        while($fac_row=imw_fetch_array($fac_qry)){
                            $sel2=($fac_row['id']==$deviceRow['iportal_def_facility'])?' selected="selected" ':'';
                            $facility_option .= '<option value="'.$fac_row['id'].'"  '.$sel2.'>'.trim($fac_row['name']).'</opiton>';
                        }
                    }

                ?>
                <div class="row">
                    <div class="col-sm-9">
                        <div class="adminbox">
                            <div class="head">
                                <span>Enable Payments on iPortal</span>
                            </div>
                            <div class="tblBg">
                                <div class="row">
                                    <div class="table-responsive respotable adminnw">
                                        <div class="col-sm-2">
                                            <div class="checkbox">
                                                <input type="checkbox" <?php echo ($deviceRow["iportal_payments_settings"]==1 ? "checked" : ""); ?> name="iportal_payments_settings" id="iportal_payments_settings" value="1" onchange="showDevice_list()" autocomplete="off">
                                                <label for="iportal_payments_settings" style="padding:2px;">Enable iPortal Payments</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 form-inline show_fields">
                                            <label for="iportal_pos_device" style="margin-bottom:-5px!important;">POS&nbsp;</label>
                                            <select name="iportal_pos_device" id="iportal_pos_device" class="form-control minimal" style="width:210px">
                                                <?php echo $devices_option; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-inline show_fields">
                                            <label for="iportal_pos_user" style="margin-bottom:-5px!important;">iPortal User&nbsp;</label>
                                            <select name="iportal_pos_user" id="iportal_pos_user" class="form-control minimal" style="width:180px">
                                                <?php echo $users_option; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-4 form-inline show_fields">
                                            <label for="iportal_def_facility" style="margin-bottom:-5px!important;">iPortal Facility&nbsp;</label>
                                            <select name="iportal_def_facility" id="iportal_def_facility" class="form-control minimal" style="width:200px">
                                                <?php echo $facility_option; ?>
                                            </select>
                                        </div>	
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php } ?>
		</div>
	</form>	
</div>
<script type="text/javascript">
var ar = [["alerts_new","Save","top.fmain.submit_form();"]];
top.btn_show("ADMN",ar);
top.show_loading_image('none');

function set_col_heights(){
	rght_col = $('.right_col');
	var prnt_height = parseInt($('.whtbox').height() - 20);
	$('.appt_div_blk').css({
		'height':prnt_height,
		'max-height':prnt_height,
		'overflowX':'hiddden',
		'overflowY':'auto',
	});
	rght_col.removeAttr('style');
	
	var left_lwr_div_height = parseInt(rght_col.outerHeight() - ($('.prnt_left_col').outerHeight()));
	$('.left_col').each(function(id,elem){
		var target_elem = $(elem).find('.respotable');
		var height_diff = target_elem.position();
		var new_height = parseInt(left_lwr_div_height - (height_diff.top + 20));
		target_elem.css({
			'height':new_height,
			'max-height':new_height,
			'overflowX':'hidden',
			'overflowY':'auto',
		});
	});
}

function showDevice_list() {
    if ($('#iportal_payments_settings').is(':checked')==true) {
        $('.show_fields').show();
        $('#iportal_payments_settings').val('1');
    } else {
        $('.show_fields').hide();
        $('#iportal_payments_settings').val('0');
    }
}


$(document).ready(function(){
    check_checkboxes();
	set_header_title('iPortal Settings');
	
	//Setting Column heights
	set_col_heights();
    
    showDevice_list();
});

$(window).resize(function(){
	set_col_heights();
});


</script>
<?php 
	require_once('../admin_footer.php');
?>