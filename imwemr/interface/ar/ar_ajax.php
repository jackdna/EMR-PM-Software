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
set_time_limit(900);
require_once '../../config/globals.php';
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
// require_once '../../library/phpmailer/PHPMailerAutoload.php';
include_once('../../library/classes/acc_functions.php');
use PHPMailer\PHPMailer;

$operator_id = $_SESSION['authId'];
$facility_id = $_SESSION['login_facility'];
$curr_time = date('H:i:s');
$curr_date = date('Y-m-d');
$curr_date_time = date('Y-m-d H:i:s');
$ar_ajax="yes";
$global_date_format = phpDateFormat();
$showCurrencySymbol = showCurrency();
$getSqlDateFormat = get_sql_date_format();
$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());

if($_POST['action_type']=="write_off_update"){
	if(count($_POST['chld_arr'])>0){
		$chld_chk_imp=implode(',',$_POST['chld_arr']);
		$show_write_codes_arr=$_POST['show_write_codes'];
		//------------------------ Reason Code ------------------------//
		$qry = imw_query("SELECT * FROM cas_reason_code where cas_action_type='Write Off'");
		while($row = imw_fetch_array($qry)){
			$reason_code_data[$row['cas_id']]=$row;
		}
		//------------------------ Reason Code ------------------------//
		foreach($show_write_codes_arr as $wry_key=>$wrt_val){
			$write_off_code_drop_val=$show_write_codes_arr[$wry_key];
			if(strpos($write_off_code_drop_val,'_wrt')>0){
				$write_off_code_id=str_replace('_wrt','',$write_off_code_drop_val);
			}
			if(strpos($write_off_code_drop_val,'_cas')>0){
				$cas_code_id=str_replace('_cas','',$write_off_code_drop_val);
				$cas_code_arr=array();
				if($reason_code_data[$cas_code_id]['cas_code']!=""){
					$cas_code_arr=explode(' ',trim($reason_code_data[$cas_code_id]['cas_code']));
					if(count($cas_code_arr)<=1){
						$cas_code_arr=explode('-',trim($reason_code_data[$cas_code_id]['cas_code']));
					}
					if($cas_type!=""){
						$cas_type=$cas_type.','.$cas_code_arr[0];
						$cas_code=$cas_code.','.$cas_code_arr[1];
					}else{
						$cas_type=$cas_code_arr[0];
						$cas_code=$cas_code_arr[1];
					}
				}
			}
		}
		
		$chld_qry=imw_query("select charge_list_detail_id,newBalance,patient_id,charge_list_id from patient_charge_list_details where charge_list_detail_id in($chld_chk_imp) and newBalance>0 and del_status='0' order by charge_list_id asc");
		while($chld_row=imw_fetch_assoc($chld_qry)){
			$charge_list_detail_id=$chld_row['charge_list_detail_id'];
			$patient_id=$chld_row['patient_id'];
			$charge_list_id=$chld_row['charge_list_id'];
			$encounter_id=$_POST['enc_chld'][$charge_list_detail_id];
			$writeOffAmt=$_POST['chld_balance'][$charge_list_detail_id];
			if($writeOffAmt>0){
				$ins_id='';
				if($cas_type==""){$cas_type="AR Worksheet";}
				$insertWriteOffStr = "INSERT INTO paymentswriteoff SET
									patient_id = '$patient_id',
									encounter_id = '$encounter_id',
									charge_list_detail_id = '$charge_list_detail_id',
									write_off_by_id='$ins_id',
									write_off_amount = '$writeOffAmt',
									write_off_operator_id = '$operator_id',
									write_off_date = '$curr_date',
									paymentStatus = 'Write Off',
									write_off_code_id='$write_off_code_id',
									entered_date='$curr_date_time',
									facility_id='$facility_id',
									cas_type='$cas_type',
									cas_code='$cas_code'";
				$insertWriteOffQry = imw_query($insertWriteOffStr);
				set_payment_trans($encounter_id);
				patient_proc_bal_update($encounter_id);
			}
		}
		$ret_data['write_off_update']=imw_num_rows($chld_qry);
	}
}elseif($_POST['action_type']=="rebill_update" || $_POST['action_type']=="claim_update"){
	if(count($_POST['chld_arr'])>0){
		$chld_chk_imp=implode(',',$_POST['chld_arr']);
		$chld_qry=imw_query("select patient_charge_list_details.charge_list_detail_id,patient_charge_list_details.patient_id,patient_charge_list_details.charge_list_id,
			patient_charge_list.primaryInsuranceCoId,patient_charge_list.secondaryInsuranceCoId,patient_charge_list.tertiaryInsuranceCoId,patient_charge_list.encounter_id,
			patient_charge_list.reff_phy_id,patient_charge_list.primaryProviderId,patient_charge_list.reff_phy_nr
			from patient_charge_list join patient_charge_list_details on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			where patient_charge_list_details.charge_list_detail_id in($chld_chk_imp) and patient_charge_list_details.newBalance>0 and patient_charge_list_details.del_status='0' order by patient_charge_list_details.charge_list_id asc");
		while($chld_row=imw_fetch_assoc($chld_qry)){
			$chl_data[$chld_row['charge_list_id']][$chld_row['charge_list_detail_id']]=$chld_row;
		}
		foreach($chl_data as $chl_key => $chl_val){
			$submitToIns='true';
			$day_charges_chk="yes";
			$chld_ids_arr=array();
			foreach($chl_data[$chl_key] as $chld_key => $chld_val){
				$post_pat_id=$chl_data[$chl_key][$chld_key]['patient_id'];
				$encounter_id=$chl_data[$chl_key][$chld_key]['encounter_id'];
				$reffPhyscianId=$chl_data[$chl_key][$chld_key]['reff_phy_id'];
				$primaryProviderId=$chl_data[$chl_key][$chld_key]['primaryProviderId'];
				$primaryInsuranceCoId=$chl_data[$chl_key][$chld_key]['primaryInsuranceCoId'];
				$reff_phy_nr=$chl_data[$chl_key][$chld_key]['reff_phy_nr'];
				$chld_ids_arr[$chld_key]=$chld_key;
				if($chl_data[$chl_key][$chld_key]['primaryInsuranceCoId']==$_POST['ins_comp_id']){
					$_REQUEST["post_for"]=1;
				}else if($chl_data[$chl_key][$chld_key]['secondaryInsuranceCoId']==$_POST['ins_comp_id']){
					$_REQUEST["post_for"]=2;
				}else if($chl_data[$chl_key][$chld_key]['tertiaryInsuranceCoId']==$_POST['ins_comp_id']){
					$_REQUEST["post_for"]=3;
				}
			}
			$chld_day_ids=implode(',',$chld_ids_arr);
			include "../accounting/postCharges.php";
			
			if($_POST['action_type']=="claim_update"){
				$priSubmit = 1;
				$patient_qry=imw_query("select providerID,sex from patient_data where id='$post_pat_id'");
				$patientDetail=imw_fetch_assoc($patient_qry);
				if($reffPhyscianId>0){
					$reff_qry=imw_query("select LastName,FirstName,MiddleName,NPI,Texonomy from refferphysician where physician_Reffer_id='$reffPhyscianId'");
					$reffDetail=imw_fetch_assoc($reff_qry);
					$reffPhysicianLname = $reffDetail['LastName'];
					$reffPhysicianFname = $reffDetail['FirstName'];
					$reffPhysicianMname = $reffDetail['MiddleName'];
					$npiNumber = $reffDetail['NPI'];
					$Texonomy = $reffDetail['Texonomy'];
				}
				$rendering_qry=imw_query("select user_npi,TaxonomyId from users where id='$primaryProviderId'");
				$renderingPhyDetail=imw_fetch_assoc($rendering_qry);
				//---- Patient Validate Check -------
				$validation = false;
				if($primaryInsuranceCoId==0 && $validation == false){
					$validation = true;
					$error[$chl_key] = 'Patient Primary Infomation is Required.';
				}
				if($patientDetail['sex']== '' && $validation == false){
					$validation = true;
					$error[$chl_key] = 'Patient Gender Infomation is Required.';
				}
				if($reffPhyscianId>0 && $reff_phy_nr==0 && ($print_paper_type=='PrintCms' || $print_paper_type=='PrintCms_white')){
					if($npiNumber == '' && $validation == false){
						$validation = true;
						$error[$chl_key] = 'Referring Physician NPI # is Required.';
					}
				}
				if($renderingPhyDetail['user_npi'] == '' && $validation == false){
					$validation = true;
					$error[$chl_key] = 'Rendering Physician NPI # is Required.';
				}
				if($renderingPhyDetail['TaxonomyId'] == '' && $validation == false){
					$validation = true;
					$error[$chl_key] = 'Rendering Physician Taxonomy # is Required.';
				}
				if($validation == true){
					$invalidChargeListId[0] = $chl_key;
				}else{
					if($primaryInsuranceCoId>0){
						$validChargeListId[] = $chl_key;
					}
				}
			}
		}
		
		if(count($validChargeListId)>0){
			$InsComp=$_REQUEST["post_for"];
			$validChargeListId=array_unique($validChargeListId);
			$chld_ids=$chld_chk_imp;
			$print_paper_type=$_POST['print_paper_type'];
			if($print_paper_type=="Printub" || $print_paper_type=="WithoutPrintub"){
				require_once("../billing/print_ub.php");
			}else{
				require_once("../billing/print_hcfa_form.php");
			}
			$ret_data['claim_path']=$final_path;
		}
		if($_POST['action_type']=="claim_update"){
			$ret_data['error']=$error;
		}
		$ret_data[$_POST['action_type']]=imw_num_rows($chld_qry);
	}
}elseif($_POST['action_type']=="status_update"){
	if(count($_POST['chld_arr'])>0 && $_POST['claim_status']){
		$chld_chk_imp=implode(',',$_POST['chld_arr']);
		$chld_qry=imw_query("select patient_charge_list_details.charge_list_id, patient_charge_list_details.patient_id from patient_charge_list join patient_charge_list_details on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			where patient_charge_list_details.charge_list_detail_id in($chld_chk_imp) and patient_charge_list_details.newBalance>0 and patient_charge_list_details.del_status='0' order by patient_charge_list_details.charge_list_id asc");
		
		while($chld_row=imw_fetch_assoc($chld_qry))
		{
			$chl_data[$chld_row['charge_list_id']]=$chld_row['charge_list_id'];
		}
		
		if(sizeof($chl_data)>0)
		{
			$chl_ids=implode(',',$chl_data);
			imw_query("update patient_charge_list set claim_status=".$_POST['claim_status']." where charge_list_id IN ($chl_ids)");
			$ret_data[$_POST['action_type']]=imw_affected_rows();
		}
	}
}elseif($_POST['action_type']=='assign_to_update' || $_POST['action_type']=='followup_update'){
	if(count($_POST['chld_arr'])>0){
		$chld_chk_imp=implode(',',$_POST['chld_arr']);
		if($_POST['action_type']=='assign_to_update'){
			$task_assign_for=implode(',',$_POST['task_users']);
			imw_query("update patient_charge_list_details set ar_assign_to='$task_assign_for' where charge_list_detail_id in($chld_chk_imp)");
		}else{
			$chld_qry=imw_query("select patient_charge_list_details.charge_list_id, patient_charge_list_details.procCode, patient_charge_list.encounter_id, patient_charge_list.patient_id, patient_charge_list.date_of_service, patient_data.fname, patient_data.lname,patient_charge_list_details.charge_list_detail_id from patient_charge_list 
			JOIN patient_charge_list_details ON patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			JOIN patient_data ON patient_charge_list.patient_id=patient_data.id
			WHERE patient_charge_list_details.charge_list_detail_id in($chld_chk_imp) and patient_charge_list_details.newBalance>0 and patient_charge_list_details.del_status='0' order by patient_charge_list_details.charge_list_id asc");
	
			while($chld_row=imw_fetch_assoc($chld_qry))
			{
				$chl_data[$chld_row['charge_list_id']]=$chld_row;
				$chl_proc_data[$chld_row['charge_list_id']][$chld_row['procCode']]=$chld_row['procCode'];
			}
			
			if(sizeof($chld_qry)>0)
			{
				$qry=imw_query("SELECT `cpt_fee_id`, `cpt_prac_code` FROM `cpt_fee_tbl`");
				while($res=imw_fetch_assoc($qry))
				{
					$cpt_arr[$res['cpt_fee_id']]=$res['cpt_prac_code'];
				}
			}
			$added=0;
			foreach($chl_data as $ch_id=>$enc_det)
			{
				if($enc_det['encounter_id']>0)
				{
					$procCode=array();
					foreach($chl_proc_data[$ch_id] as $ch_proc_id=>$ch_proc_val)
					{
						$procCode[]=$cpt_arr[$chl_proc_data[$ch_id][$ch_proc_id]];
					}
					$task_assign="1";
					$task_assign_for=$task_assign_by=$task_assign_date="";
					$notes_reminder_date=getDateFormatDB($_POST['task_dated']);
					$task_assign_for=implode(',',$_POST['task_users']);
					if($task_assign_for!=""){
						$task_assign_by=$operator_id;
						$task_assign="2";
						$task_assign_date=$curr_date_time;
					}
					$enc_notes=imw_real_escape_string($_POST['task_notes']);
					$task_on_reminder=(isset($_POST['task_on_reminder']) && $_POST['task_on_reminder']=='yes') ?'1':'0';
					$comment_rs = imw_query("INSERT INTO paymentscomment SET patient_id = '".$enc_det['patient_id']."',
											encounter_id = '".$enc_det['encounter_id']."',
											commentsType = 'Internal',
											encComments = '$enc_notes',
											encCommentsDate = '$curr_date',
											encCommentsTime='$curr_time',
											encCommentsOperatorId = '$operator_id',
											reminder_date='$notes_reminder_date',
											task_assign='$task_assign',
											task_assign_by='$task_assign_by',
											task_assign_for='$task_assign_for',
											task_onreminder='".$task_on_reminder."',
											task_assign_date='$task_assign_date'");
	
					if($comment_rs){
						$comment_insert_id=imw_insert_id();
						$pat_lname=$enc_det['lname'];
						if($enc_det['fname']){
							$pat_fname=', '.$enc_det['fname'];
						}
						$tm_patient_name=$pat_lname.$pat_fname;
	
						imw_query("INSERT INTO tm_assigned_rules set section_name='Accounting Notes', 
								status=0, 
								changed_value='" . $enc_notes . "', 
								cpt_code='" . implode(', ',$procCode) . "', 
								date_of_service='" . $enc_det['date_of_service'] . "', 
								encounter_id=" . $enc_det['encounter_id'] . ", 
								patientid=" . $enc_det['patient_id'] . ", 
								patient_name='" . $tm_patient_name . "', 
								operatorid=" . $operator_id . ", 
								payment_comtId=" . $comment_insert_id . ",
								notes_users='" . $task_assign_for . "',
								task_on_reminder='".$task_on_reminder."',
								reminder_date='" . $notes_reminder_date . "'");
						$added++;
					}
				}
			}
		}
	}
	$ret_data[$_POST['action_type']]=$added;
}elseif($_POST['action_type']=="statement_update"){
	/*$chld_chk_imp=implode(',',$_POST['chld_arr']);
	$chld_qry=imw_query("select patient_charge_list_details.charge_list_id from patient_charge_list join patient_charge_list_details on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
		where patient_charge_list_details.charge_list_detail_id in($chld_chk_imp) and patient_charge_list_details.newBalance>0 and patient_charge_list_details.del_status='0' order by patient_charge_list_details.charge_list_id asc");
	while($chld_row=imw_fetch_assoc($chld_qry))
	{
		$chargeList[$chld_row['charge_list_id']]=$chld_row['charge_list_id'];
	}
	if($_POST['ins_comp_id']>0){
		$_REQUEST['force_cond']='yes';
	}
	require_once('../reports/printStatements.php');
	if($divData!=""){
		
		if(count($pat_statements)>0){

			if($text_print){
				$srh_text_print=1;
			}else{
				$srh_text_print=0;
			}
			
			$force_cond="";
			if($_POST['ins_comp_id']>0){
				$force_cond="yes";
			}
			
			$search_option_arr['from']='AR Worksheet';
			$search_option_arr['grp_id']="";
			$search_option_arr['startLname']="";
			$search_option_arr['endLname']="";
			$search_option_arr['rePrint']="";
			$search_option_arr['fully_paid']="";
			$search_option_arr['text_print']=$srh_text_print;
			$search_option_arr['force_cond']=$force_cond;
			$search_option_arr['inc_chr_amt']="";
			$search_option_arr['show_min_amt']="";
			$search_option_arr['show_new_statements']="";
		
			$search_options_serz=serialize($search_option_arr);
			require_once('../reports/update_statement.php');
		}
		 
		$ret_data['claim_path']=write_html($divData);
	}*/
}elseif($_POST['action_type']=="letter_update"){
	/*letter_template,
	letter_type,
	letter_to*/
	$customMsg = '';
	if($_POST['letter_type']=='email')
	{
		$returnArr = array();
		$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(mysql_error());
		if(imw_num_rows($queryEmailCheck)>=1)
		{
			$dEmailCheck=imw_fetch_object($queryEmailCheck);
			$groupEmailConfig['email']=$dEmailCheck->config_email;
			$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
			$groupEmailConfig['host']=$dEmailCheck->config_host;
			$groupEmailConfig['header']=$dEmailCheck->config_header;
			$groupEmailConfig['footer']=$dEmailCheck->config_footer;
			$groupEmailConfig['port']=$dEmailCheck->config_port;
			$groupEmailConfig['email_subject_reminder']= 'imwemr Collection Letter';
		}
		imw_free_result($queryEmailCheck);
		
		if(!$groupEmailConfig['email'] || !$groupEmailConfig['pwd'] || !$groupEmailConfig['host']){
			$customMsg = "Error: Email not configured";
			goto noMoreProcessRequired;
		}
		
		//Create a new PHPMailer instance
		$mail = new PHPMailer\PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $groupEmailConfig['host'];
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $groupEmailConfig['port'];
		//$mail->SMTPSecure = 'ssl';//ssl, tls
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		// SMTP connection will not close after each email sent, reduces SMTP overhead
		$mail->SMTPKeepAlive = true;
		//Username to use for SMTP authentication
		$mail->Username = $groupEmailConfig['email'];
		//Password to use for SMTP authentication
		$mail->Password = $groupEmailConfig['pwd'];
		//Set who the message is to be sent from
		$mail->setFrom($groupEmailConfig['email'], '');
		//Set an alternative reply-to address
		//$mail->addReplyTo('replyto@example.com', 'First Last');
		//Set the subject line
		$mail->Subject = $groupEmailConfig['email_subject_reminder']; //'imwemr Appt. Reminder';
	}
	
	if($_POST['letter_template']){
		$collectionTemplateId=$_POST['letter_template'];
		$andQry=" AND id='".$collectionTemplateId."'";
	}
	$coll_qry = imw_query("select * from collection_letter_template WHERE 1=1 $andQry order by id desc");
	$qryRes_template = imw_fetch_array($coll_qry);
	$collection_data=html_entity_decode(stripslashes($qryRes_template['collection_data']));
	$template_id = $qryRes_template['id'];

	$insCompArr = array();
	$qry =imw_query("select id,name,fax,email from insurance_companies order by name");	
	while($row=imw_fetch_assoc($qry)){
		$insCompArr[$row['id']]=$row;
	}
	
	if(count($_POST['chld_arr'])>0){
		$chld_chk_imp=implode(',',$_POST['chld_arr']);
		$chld_qry=imw_query("select 
							patient_charge_list_details.charge_list_id, 
							patient_charge_list_details.procCode, 
							patient_charge_list_details.charge_list_detail_id,
							patient_charge_list_details.newBalance,
							
							patient_charge_list.encounter_id, 
							patient_charge_list.patient_id, 
							patient_charge_list.primaryInsuranceCoId, 
							patient_charge_list.secondaryInsuranceCoId, 
							patient_charge_list.tertiaryInsuranceCoId,
							date_format(patient_charge_list.date_of_service,'".$getSqlDateFormatSmall."') as date_of_service,
							
							patient_data.fname, patient_data.mname, patient_data.lname, patient_data.pat_account_status,
							patient_data.street, patient_data.street2, patient_data.suffix,	patient_data.phone_home, patient_data.phone_biz, patient_data.phone_cell, patient_data.title, patient_data.title, patient_data.city, patient_data.state, 
							patient_data.postal_code, date_format(patient_data.DOB,'".$getSqlDateFormat."') as pat_dob, patient_data.External_MRN_1,
							patient_data.External_MRN_2, patient_data.race, patient_data.otherRace, patient_data.language, patient_data.ethnicity,
							patient_data.otherEthnicity, patient_data.email
							
							FROM patient_charge_list 
							JOIN patient_charge_list_details 
								ON patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
							JOIN patient_data 
								ON patient_charge_list.patient_id=patient_data.id
							LEFT JOIN users
								ON patient_charge_list.primaryProviderId=users.id
							WHERE 
								patient_charge_list_details.charge_list_detail_id in($chld_chk_imp) 
								AND patient_charge_list_details.newBalance>0 
								AND patient_charge_list_details.del_status='0' 
							ORDER BY patient_charge_list_details.charge_list_id asc")or die(imw_error());
		$main_encounter_id_arr = $mainEncResArr = $main_patient_id_arr = $mainPatResArr = $mainPatACStsArr = $mainPatDOSArr = array();
		$mainPatTotAmtArr = $mainPatTotBalArr = array();
		//$encounter_id = 0;
		while($pt_coll_row=imw_fetch_assoc($chld_qry))
		{
			$encounter_id = $pt_coll_row['encounter_id'];
			//$main_encounter_id_arr[] = $encounter_id;
			//$mainEncResArr[$encounter_id][] = $pt_coll_row;
			$main_patient_id_arr[$pt_coll_row['patient_id']] = $pt_coll_row['patient_id'];
			//insurance wise patient array
			$main_pt_id_ins_wise_arr[$pt_coll_row['primaryInsuranceCoId']][$pt_coll_row['patient_id']] = $pt_coll_row['patient_id'];
			//$mainPatACStsArr[$pt_coll_row['patient_id']] = $pt_coll_row['pat_account_status'];
			$mainPatResArr[$pt_coll_row['patient_id']][] = $pt_coll_row;
			$mainPatTotBalArr[$pt_coll_row['patient_id']][] = $pt_coll_row['newBalance'];
			$mainPatDOSArr[$pt_coll_row['patient_id']][$pt_coll_row['date_of_service']] = $pt_coll_row['date_of_service'];

		}
		if($str_pat_imp=implode(',',$main_patient_id_arr))
		{
			// GET RESP PARTY ARRAY
			$qry_party = imw_query("select lname as res_lname,fname as res_fname,
						mname as res_mname,address,address2,suffix as res_suffix,
						title as res_title,patient_id,home_ph,work_ph,mobile,city,state,zip from resp_party WHERE patient_id IN(".$str_pat_imp.")");
			$res_party_arr = array();
			while ($qry_res_party = imw_fetch_assoc($qry_party)) {	
				$patient_id = $qry_res_party['patient_id'];
				$res_party_arr[$patient_id][] = $qry_res_party;
			}
		}
	}
	include('collection_letter.php');
	
	if($_POST['letter_type']!='print')
	{
		//remove html and pdf file from disk
		unlink($letter_html_file,$letter_pdf_file);
		unset($letter_html_file,$letter_pdf_file);
	}else
	{
		//remove pdf and HOLD html file for print task
		unlink($letter_pdf_file);
		unset($letter_pdf_file);
	}
	$arr['msg']=$customMsg;
	$arr['html_file']=$letter_html_file;
	//goto process variable
	noMoreProcessRequired:
	$ret_data[$_POST['action_type']]=$arr;
}
echo json_encode($ret_data);
?>
