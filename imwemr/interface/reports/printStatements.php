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
?>
<?php
/*
FILE : printStatements.php
PURPOSE : GENERATE HTML DATA FOR PDF PRINTNG
ACCESS TYPE : DIRECT
*/
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
use PHPMailer\PHPMailer;
set_time_limit(0);
$stop_clm_status=1;

$stat_tpl_qry = imw_query("select * from statement_template");
$stat_tpl_row = imw_fetch_array($stat_tpl_qry);
$statement_data_db = $stat_tpl_row['statement_data'];
$email_subject = $stat_tpl_row['email_subject'];
$email_body = $stat_tpl_row['email_body'];

if($_REQUEST['print_pdf']=='email' || $emailStatement>0){
	$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1");
	if(imw_num_rows($queryEmailCheck)>=1)
	{
		$dEmailCheck=imw_fetch_object($queryEmailCheck);
		$groupEmailConfig['email']=$dEmailCheck->config_email;
		$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
		$groupEmailConfig['host']=$dEmailCheck->config_host;
		$groupEmailConfig['header']=$dEmailCheck->config_header;
		$groupEmailConfig['footer']=$dEmailCheck->config_footer;
		$groupEmailConfig['port']=$dEmailCheck->config_port;
	}
	if(!$groupEmailConfig['email'] || !$groupEmailConfig['pwd'] || !$groupEmailConfig['host'])
	{
		$email_config="error";
		if($_REQUEST['print_pdf']=='email'){
			echo "<body class='whitebox'><h3 style='color:#FD3236'>Email is not configured.</h3></body>";exit();	
		}
		?>  <script>
			top.fAlert("Email not configured.");
			top.show_loading_image('hide');
			</script>
	   <?php
	}

	// require_once '../../library/phpmailer/PHPMailerAutoload.php';

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
	//Set who the message is to be sent to
	//Set the subject line
	if($email_subject!=""){
		$mail->Subject = $email_subject;
	}else{
		$mail->Subject = 'imwemr Account Statement';
	}
}

$show_cas_code_arr=array();
$getCASStr = "SELECT * FROM cas_reason_code ORDER BY cas_code";
$getCASQry = imw_query($getCASStr);
while($getCASRow = imw_fetch_array($getCASQry)){
	$show_cas_code_arr[$getCASRow['cas_code']]=$getCASRow['cas_desc'];
}

$whr_pt_due=$spl_msg="";

//--- GET BILLING POLICIES AGGING CYCLE -----
$polQryRs = imw_query("select elem_arCycle,statement_cycle,ar_aging,min_pay_due_stm,Statement_Elapsed,fully_paid, min_balance_bill,statement_base,full_enc,statement_consolidate from copay_policies where policies_id = '1'");
$polQryRes = imw_fetch_array($polQryRs);
$aggingCycle = $polQryRes['elem_arCycle'];
$statement_cycle=$polQryRes['statement_cycle'];
$ar_aging=$polQryRes['ar_aging'];
$min_pay_due_stm=$polQryRes['min_pay_due_stm'];
$Statement_Elapsed = $polQryRes['Statement_Elapsed'];
$min_balance_bill = $polQryRes['min_balance_bill'];
$statement_base = $polQryRes['statement_base'];
$full_enc = $polQryRes['full_enc'];
$statement_consolidate= $polQryRes['statement_consolidate'];
$Statement_Elapsed_date = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-$Statement_Elapsed,date('Y')));

$hammad_iasc_group="SARATOGA VITREO-RETINAL OPHTH";
$hammad_iasc_add1="465 Maple Ave Unit B";
$hammad_iasc_add2="Saratoga Springs, NY 12866";
$hammad_iasc_phone="(P) 518 - 580 - 0553";

if(count($chargeList) > 0){
	$chargeListId = str_replace('---',",",join(",",$chargeList));
}
$imp_g_code_proc=explode("','",$arr_g_code_proc);
if(in_array(strtolower($billing_global_server_name), array('keystone','essi','cfe','niec','hatsislaservision'))){
	if(in_array(strtolower($billing_global_server_name), array('niec'))){
		$padddingLf_120px   = "80px";
		$width_250px   		= "310px";
	}else{
		$padddingLf_120px   = "145px";
		$width_250px   		= "250px";
	}
	$width_200px   		= "200px";
	$width_150px   		= "150px";
	$width_100px  		= "100px";
	$width_75px  		= "75px";
	$fontSize			= "10px";
	if(in_array(strtolower($billing_global_server_name), array('cowan','forman_cnm','scott','hammad','hammad_iasc'))){
		$RemitfontSize		= "13px";
	}else{
		$RemitfontSize		= "10px";
	}
	$cartWith_60px		= "15px";
	$cartWith_30px		= "15px";
	$master_card		= "MC";
}else{
	$padddingLf_120px   = "20px;";
	if(in_array(strtolower($billing_global_server_name), array('cowan','forman_cnm','scott','hammad','hammad_iasc'))){
		$width_250px   		= "250px";
	}else{
		$width_250px   		= "300px";
	}
	$width_200px   		= "250px";
	$width_150px   		= "200px";
	$width_100px  		= "130px";
	$width_75px  		= "125px";
	$fontSize			= "13px";
	$RemitfontSize		= "13px";
	$cartWith_60px		= "";
	$cartWith_30px		= "";
	$master_card		= 'MASTER CARD';
}
$balance_title="Balance";
if(in_array(strtolower($billing_global_server_name), array('cep','associatedeye'))){
	$balance_title="Pt Balance";
}
$phone_ext="";
if(in_array(strtolower($billing_global_server_name), array('cowan'))){
	$phone_ext=" ext. 4511 or 4516";
}
if(in_array(strtolower($billing_global_server_name), array('forman_cnm'))){
	$phone_ext=" ext. 4511 or 4517";
}
if(in_array(strtolower($billing_global_server_name), array('scott'))){
	$phone_ext=" ext. 4512 or 4513";
}
$force_cond=$_REQUEST['force_cond'];
$isAE=verify_payment_method("AE");
$discover=verify_payment_method("DISCOVER");
$care_credit=verify_payment_method("CareCredit");
$AE_show="";
$AE_show_name="";
if(empty($isAE) === false){
	$AE_show = '
		<td valign="top" style="width:'.$cartWith_30px.';">
			<img src="amr.jpg">&nbsp;
			<img src="checkbox.jpg">&nbsp;
			
		</td>
		<td style="width:'.$cartWith_60px.';">AMEX</td>';
		$AE_show_name = "AMEX";
}

if(empty($discover) === false){
	$AE_show.='<td style="width:'.$cartWith_60px.';"><img src="discover.jpg">&nbsp;<img src="checkbox.jpg">&nbsp;DISCOVER&nbsp;</td>';
	if($AE_show_name!=""){
		$dis_space="     ";
	}
	$AE_show_name.= $dis_space."DISCOVER";	
}
if(empty($care_credit) === false){
	if($AE_show_name!=""){
		$dis_space="     ";
	}
	$AE_show_name.= $dis_space."CARE CREDIT";
}
$display_creditcard="yes";
if(constant("DISABLE_CREDITCARD") == "YES"){
	$display_creditcard="no";
}
$cv2="";
if(in_array(strtolower($billing_global_server_name), array('niec'))){
	$cv2='<td style="padding-left:63px;">&nbsp;</td>
	<td style="border-left:solid #000000; font-size:$fontSize;padding-left:5px;">CVV:</td>';
}

//---- CHANGE STATEMENT PRINT STATUS ------
if(count($chargeList)){
	//----- INSERT CHARGE LIST ID IN TO STATEMENT TABLE -----
	$statementUpDetail = $statementCntArr = array();
	if($statement_base>0){
		foreach($chargeList as $val_first){
			$val_first_arr=array();
			$val_first_arr=explode('---',$val_first);
			foreach($val_first_arr as $val){
				$st_qry=imw_query("select patient_data.id,patient_data.acc_statement_count,patient_data.acc_statement_date, 
				patient_charge_list.gro_id from patient_data join patient_charge_list 
				on patient_charge_list.patient_id=patient_data.id where patient_charge_list.charge_list_id = '$val'");	
				$st_row=imw_fetch_array($st_qry);
				$sat_patient_id=$st_row['id'];
				$acc_statement_date_unserz=unserialize($st_row['acc_statement_date']);
				$acc_statement_date=$acc_statement_date_unserz[$st_row['gro_id']];
				if(strlen($st_row['acc_statement_date'])==10){
					$acc_statement_date=$st_row['acc_statement_date'];
				}
				$acc_statement_date_unserz[$st_row['gro_id']]=date('Y-m-d');
				$acc_statement_date_serz=serialize($acc_statement_date_unserz);
				if($st_row['acc_statement_count']>0){
					$acc_statement_count=$st_row['acc_statement_count'];
					$qry=imw_query("select patient_charges_detail_payment_info.payment_id
							from patient_charges_detail_payment_info join 
							patient_chargesheet_payment_info 
							on patient_chargesheet_payment_info.payment_id = 
							patient_charges_detail_payment_info.payment_id
							join patient_charge_list on patient_charge_list.encounter_id = 
							patient_chargesheet_payment_info.encounter_id
							where patient_charge_list.del_status='0' and 
							patient_charge_list.patient_id = '$sat_patient_id'
							and patient_chargesheet_payment_info.paid_by = 'Patient'
							and patient_chargesheet_payment_info.date_of_payment
							between '$acc_statement_date' and '".date('Y-m-d')."'
							and patient_chargesheet_payment_info.paymentClaims != 'Interest Payment'
							and patient_charges_detail_payment_info.deletePayment = '0'");
					$qryRes = imw_fetch_array($qry);
					if(imw_num_rows($qry)>0){
						//imw_query("update patient_charge_list set statement_count = '1',statement_date='".date('Y-m-d')."' where charge_list_id = '$val' ");
						//imw_query("update patient_data set acc_statement_count='1',acc_statement_date='".$acc_statement_date_serz."' where id='$sat_patient_id'");
						$acc_statement_date=date('Y-m-d');
						$acc_statement_count=1;
						$statementUpDetail[$sat_patient_id][$val]['count']=$acc_statement_count;
						$statementUpDetail[$sat_patient_id][$val]['pat_date']=$acc_statement_date_serz;
					}
					if($acc_statement_count>1){
						$sel_chl_det=imw_query("select sum(totalBalance) as sum_totalBalance from patient_charge_list where date_of_service<='$acc_statement_date' and patient_id='$sat_patient_id'");
						$row_chl_det=imw_fetch_array($sel_chl_det);
						if($row_chl_det['sum_totalBalance']<=0){
							//imw_query("update patient_charge_list set statement_count = '1',statement_date='".date('Y-m-d')."' where charge_list_id = '$val' ");
							//imw_query("update patient_data set acc_statement_count='1',acc_statement_date='".$acc_statement_date_serz."' where id='$sat_patient_id'");
							$acc_statement_date=date('Y-m-d');
							$acc_statement_count=1;
							$statementUpDetail[$sat_patient_id][$val]['count']=$acc_statement_count;
							$statementUpDetail[$sat_patient_id][$val]['pat_date']=$acc_statement_date_serz;
						}
					}
					if($acc_statement_date>$Statement_Elapsed_date){
						$statementCntArr[$sat_patient_id]['statement_count'] = $acc_statement_count;
						//imw_query("update patient_charge_list set statement_count = '$acc_statement_count' where charge_list_id = '$val' and statement_count='0' and totalBalance>0 and del_status='0'");
						//$statementUpDetail[$sat_patient_id][$val]['count']=$acc_statement_count;
						//$statementUpDetail[$sat_patient_id][$val]['pat_date']=$acc_statement_date_serz;
					}else{
						if($statementCntArr[$sat_patient_id]['statement_count']>0){
							$up_statement_count = $statementCntArr[$sat_patient_id]['statement_count'];
						}else{
							$statementCntArr[$sat_patient_id]['statement_count'] = $acc_statement_count+1;
							$up_statement_count = $acc_statement_count+1;
						}
						//imw_query("update patient_data set acc_statement_count='$up_statement_count',acc_statement_date='".$acc_statement_date_serz."' where id='$sat_patient_id'");
						//imw_query("update patient_charge_list set statement_status = '1',statement_date = '".date('Y-m-d')."',statement_count = '$up_statement_count' where charge_list_id = '$val'");
						$statementUpDetail[$sat_patient_id][$val]['count']=$up_statement_count;
						$statementUpDetail[$sat_patient_id][$val]['pat_date']=$acc_statement_date_serz;
					}
				}else{
					$statementCntArr[$sat_patient_id]['statement_count'] = 1;
					//imw_query("update patient_data set acc_statement_count='1',acc_statement_date='".$acc_statement_date_serz."' where id='$sat_patient_id'");
					//imw_query("update patient_charge_list set statement_status = '1',statement_date = '".date('Y-m-d')."',statement_count = '1' where charge_list_id = '$val'");	
					$statementUpDetail[$sat_patient_id][$val]['count']=1;
					$statementUpDetail[$sat_patient_id][$val]['pat_date']=$acc_statement_date_serz;
				}
			}
		}	
	}else{
		foreach($chargeList as $val){
			//imw_query("delete from statement_tbl where charge_list_id = '$val'");
			//imw_query("insert into statement_tbl set charge_list_id = '$val',statement_date = '".date('Y-m-d')."'");
			$qry = imw_query("select statement_status,statement_date,statement_count,totalBalance,patient_id from patient_charge_list where del_status='0' and charge_list_id = '$val'");
			$chargeListRes = imw_fetch_array($qry);
			
			$statementCntArr[$val]['patient_id'] = $chargeListRes['patient_id'];
			$statementCntArr[$val]['statement_status'] = $chargeListRes['statement_status'];
			$statementCntArr[$val]['statement_count'] = $chargeListRes['statement_count'];
			$totalBalance = $chargeListRes['totalBalance'];		
			if($chargeListRes['statement_status'] != '1'){
				$statementCntArr[$val]['statement_count'] = $chargeListRes['statement_count']+1;			
				//imw_query("update patient_charge_list set statement_status = '1',statement_date = '".date('Y-m-d')."',statement_count = statement_count+1 where charge_list_id = '$val'");
			}else{
				$statementCntArr[$val]['statement_date'] = $chargeListRes['statement_date'];			
				//imw_query("update patient_charge_list set statement_status = '1',statement_date = '".date('Y-m-d')."',statement_count = statement_count+1 where charge_list_id = '$val' and statement_date < '$Statement_Elapsed_date'");
			}
			
			//---- GET PAYMENT STATUS FOR MESSAGES -------
			$statement_date = $statementCntArr[$val]['statement_date'];
			if($totalBalance > 0){
				if($statement_date){
					$qry=imw_query("select patient_charges_detail_payment_info.payment_id
							from patient_charges_detail_payment_info join 
							patient_chargesheet_payment_info 
							on patient_chargesheet_payment_info.payment_id = 
							patient_charges_detail_payment_info.payment_id
							join patient_charge_list on patient_charge_list.encounter_id = 
							patient_chargesheet_payment_info.encounter_id
							where patient_charge_list.del_status='0' and patient_charge_list.charge_list_id = '$val'
							and patient_chargesheet_payment_info.paid_by = 'Patient'
							and patient_chargesheet_payment_info.date_of_payment
							between '$statement_date' and '".date('Y-m-d')."'
							and patient_chargesheet_payment_info.paymentClaims != 'Interest Payment'
							and patient_charges_detail_payment_info.deletePayment = '0'");
					$qryRes = imw_fetch_array($qry);
					if(imw_num_rows($qry)>0){
						//imw_query("update patient_charge_list set statement_count = '1' where charge_list_id = '$val' ");
						$statementCntArr[$val]['statement_count'] = 1;
					}
					
					/*$qry = imw_query("select statement_count from patient_charge_list where del_status='0' and charge_list_id = '$val'");
					$chargeListRes2 = imw_fetch_array($qry);
					if($chargeListRes2['statement_count']>0){
						$statementCntArr[$val]['statement_count'] = $chargeListRes2['statement_count'];
					}*/
				}else{
					$statementCntArr[$val]['statement_count'] = 1;
				}
			}else{
				$statementCntArr[$val]['statement_count'] = 'f';
			}
		}
	}
}

$requestArr = array();
if($statement_cycle=='1'){
$requestArr['f'] = 'Fully Paid';
	for($r=1;$r<=20;$r++){
		if($r == 1){
			$re = '1st Request';
		}
		else if($r == 2){
			$re = '2nd Request';
		}
		else if($r == 3){
			$re = '3rd Request';
		}
		else{
			$re = $r.'th Request';
		}
		$requestArr[$r] = $re;
	}
}

//--- GET STATEMENT MARGIN DATA ----
$qry = imw_query("select * from create_margins where margin_type = 'statement'");
$marginQryRes = imw_fetch_array($qry);
$pat_statements = array();

//--- GET STATEMENTS MESSAGES -----
$qry = imw_query("select statements_messages,statements_messages_count from statements_messages where statements_messages_status='0'");
$statementMsgArr = array();
while($messageRes = imw_fetch_array($qry)){
	$statements_messages = $messageRes['statements_messages'];
	$statements_messages_count = strtolower($messageRes['statements_messages_count']);
	$statementMsgArr[$statements_messages_count] = $statements_messages;
}
	
//--- SET INSURANCE COMPANY DROP DOWN ---
$qry = imw_query("select * from insurance_companies");
$insCompanyArr = array();
while($insQryRes = imw_fetch_array($qry)){		
	$ins_id = $insQryRes['id'];
	$ins_name = $insQryRes['in_house_code'];
	if($ins_name == ''){
		$ins_name = $insQryRes['name'];
		if(strlen($ins_name) > 20){
			$ins_name = substr($ins_name,0,20).'....';
		}
	}
	$insCompanyArr[$ins_id] = $ins_name;
}

//--- GET ALL GROUPS DETAILS ----
$qry = imw_query("select * from groups_new");
$groupDataArr = array();
while($groupRes = imw_fetch_array($qry)){	
	$groupId = $groupRes["gro_id"];
	$groupDataArr[$groupId] = $groupRes;
	$group_id_arr[$groupRes["gro_id"]]=$groupRes["gro_id"];
}

$qry_pos_fac=imw_query("select pos_facilityies_tbl.facilityPracCode as name,pos_facilityies_tbl.pos_facility_id as id,pos_tbl.pos_prac_code,pos_facilityies_tbl.posfacilitygroup_id
						from pos_facilityies_tbl
						left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
						order by pos_facilityies_tbl.facilityPracCode");	
while($fet_pos_fac=imw_fetch_array($qry_pos_fac)){
		$pos_fac_arr[$fet_pos_fac['id']]=$fet_pos_fac['name'];
		$poc_fac_all_arr[$fet_pos_fac['id']]=$fet_pos_fac['pos_prac_code'];
		$poc_fac_grp_all_arr[$fet_pos_fac['id']]=$fet_pos_fac['posfacilitygroup_id'];
}

$posFacGroup = isPosFacGroupEnabled();
if($posFacGroup){
	$qryPosGrp = "SELECT * FROM pos_facility_group where delete_status='0'";
	$qry_pos_grp_query = imw_query($qryPosGrp);
	while($posQryRes=imw_fetch_array($qry_pos_grp_query)){
		$pos_fac_grp[$posQryRes['pos_fac_grp_id']] = $posQryRes;
	}
}

$sel_prov=imw_query("select id,fname,mname,lname,pro_title,pro_suffix from users order by lname,fname asc");
while($fet_prov=imw_fetch_array($sel_prov)){
	$PhyNameArr["LAST_NAME"] = $fet_prov['lname'];
	$PhyNameArr["FIRST_NAME"] = $fet_prov['fname'];
	$PhyNameArr["MIDDLE_NAME"] = $fet_prov['mname'];
	$PhyName = changeNameFormat($PhyNameArr);
	$phy_id_name[$fet_prov['id']]=$PhyName;
	$phy_id_arr[$fet_prov['id']]=$fet_prov;
}

if($imp_g_code_proc!=""){
	$imp_g_code_proc="'".$imp_g_code_proc."'";
	$exclude_g_code_proc=" and cpt_fee_tbl.cpt_prac_code not in($imp_g_code_proc)";
}
//--- GET PQRI AND G-CODE PROCEDURE ID -----
$cptQry = imw_query("select cpt_fee_tbl.cpt_fee_id from cpt_category_tbl join cpt_fee_tbl
		on cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
		where (cpt_category_tbl.cpt_category like 'PQRI%'
		or cpt_fee_tbl.cpt_prac_code like 'G%') $exclude_g_code_proc");
//or lower(cpt_fee_tbl.cpt_desc) like '%comanage%'		
$cptCodeArr = array();
while($cptQryRes=imw_fetch_array($cptQry)){	
	$cptCodeArr[] = $cptQryRes['cpt_fee_id'];
}
$cptCodeStr = join(',',$cptCodeArr);

$page_close_cnt=0;
if($statement_consolidate==0){
	$group_id_arr=array();
	$group_id_arr[0]=0;
}
foreach($group_id_arr as $key_grp_id=> $val_grp_id){
	if($statement_consolidate>0){
		$whr_gro_id=" and patient_charge_list.gro_id='".$val_grp_id."'";
	}
	$stat_tpl_arr=array();
	//------- GET UNIQUE PATIENT ID FROM PATIENT CHARGE LIST TABLE ----------
	$patientQry = imw_query("select distinct(patient_charge_list.patient_id), patient_data.lname,
			patient_data.fname, patient_data.mname, patient_data.street, patient_data.street2,
			patient_data.city, patient_data.state, patient_data.postal_code,patient_data.email,patient_data.noBalanceBill,
			patient_data.providerID,patient_data.default_facility 
			from patient_charge_list join patient_data on patient_data.id = patient_charge_list.patient_id
			where patient_charge_list.del_status='0' and patient_charge_list.charge_list_id in ($chargeListId)
			$whr_gro_id order by patient_data.lname,patient_data.fname");
	$todayDate = date('m-d-Y');
	
	//--- START PATIENT LOOP ---
	while($patientRes=imw_fetch_array($patientQry)){		
		$p=$page_close_cnt;
		$patient_id = $patientRes['patient_id'];
		$pt_pos_facility=$pos_fac_arr[$patientRes['default_facility']];
		$pt_pri_eye_care=$phy_id_name[$patientRes['providerID']];
		
		//--- PATIENT RESPONSIBLE PARTY DETAILS -------
		$qry= imw_query("select lname,fname,mname,address,address2,city, state, zip,email from resp_party where patient_id = '$patient_id'");
		$resQryRes = imw_fetch_array($qry);
		$respartyNameArr = array();
		$respartyNameArr["LAST_NAME"] = $resQryRes['lname'];
		$respartyNameArr["FIRST_NAME"] = $resQryRes['fname'];
		$respartyNameArr["MIDDLE_NAME"] = $resQryRes['mname'];
		$respartyName = changeNameFormat($respartyNameArr);
		
		$respartyAdd = $resQryRes['address'];
		if(trim($resQryRes['address2']) != ''){
			if(in_array(strtolower($billing_global_server_name), array('edison'))){
				$respartyAdd .= ',<br> '.trim($resQryRes['address2']);
			}else{
				$respartyAdd .= ', '.trim($resQryRes['address2']);
			}
		}
		$respartyAdd1 = $resQryRes['city'].', ';
		$respartyAdd1 .= $resQryRes['state'].' ';
		$respartyAdd1 .= $resQryRes['zip'];
		$respartyAdd1 = ucfirst(trim($respartyAdd1));
		if($respartyAdd1[0] == ','){
			$respartyAdd1 = substr($respartyAdd1,1);
		}
		$respartyCity=$resQryRes['city'];
		$respartyState=$resQryRes['state'];
		$respartyZip=$resQryRes['zip'];
		$respartyEmail=$resQryRes['email'];
		
		$txt_respartyName=$respartyName;
		$txt_respartyAdd=$respartyAdd;
		$txt_respartyAdd1=$respartyAdd1;
		$txt_respartyEmail=$respartyEmail;
		
		$stat_arr = array();
		$charge_list_id_arr = array();
		$totalInsPaid_arr=array();
		$totalAdj_arr=array();
		$totalPatPaid_arr=array();
		$chl_qry= imw_query("select patient_data.fname,patient_data.lname,patient_data.mname,
				patient_data.primary_care,patient_data.providerID,patient_charge_list.encounter_id,
				date_format(patient_charge_list.date_of_service,'%m-%d-%y') as dateOfService,
				primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId,
				patient_charge_list.gro_id,patient_charge_list.totalBalance,patient_charge_list.comment,
				patient_charge_list.charge_list_id, patient_data.noBalanceBill,patient_charge_list.patient_id,
				patient_charge_list.primaryProviderId 
				from patient_charge_list
				join patient_data on patient_data.id = patient_charge_list.patient_id
				where patient_charge_list.del_status='0' and patient_id = '$patient_id' and charge_list_id in ($chargeListId)
				$whr_gro_id");
		$statementHeader = NULL;
		if($p > 0 || $page_close_cnt>0){
			$statementHeader = '</page>';
		}
		$page_close_cnt++;
		$totalCharges = 0;
		$totalUnits = 0;
		$totalPaidForProc = 0;
		$totalNewBalance = 0;
		$ins_payment_amount = 0;
		$ins_adj_amount = 0;
		$copay_paid = 0;
		$claims_encounter_id = array();
		$payment_amount = 0;
		$patientNoBalanceBill = 0;
		
		if($force_cond!='yes' && $full_enc==0){
			$whr_pt_due=" and (patient_charge_list_details.pat_due>0)";
		}
		$chargesQry = "select patient_charge_list_details.*,patient_charge_list.totalBalance,patient_charge_list.encounter_id,cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt4_code
					from patient_charge_list_details join cpt_fee_tbl 
					on patient_charge_list_details.procCode = cpt_fee_tbl.cpt_fee_id
					join patient_charge_list on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
					where patient_charge_list_details.del_status='0' and patient_charge_list_details.patient_id = '$patient_id' 
					and patient_charge_list_details.charge_list_id in($chargeListId)
					$whr_pt_due $whr_gro_id";
		if(empty($cptCodeStr) === false){
			$chargesQry .= " and patient_charge_list_details.procCode not in ($cptCodeStr)";
		}
		$chargesQryRun = imw_query($chargesQry);
		
		$totalBalance = $pchld_posFacilityId = 0;
		while($chargeListDetails_all = imw_fetch_array($chargesQryRun)){	
			$balForProc1 = $chargeListDetails_all['newBalance'];
			$overPaymentForProc1 = $chargeListDetails_all['overPaymentForProc'];
			/*if($overPaymentForProc1>0){
				$balForProc1 = ' - '.$overPaymentForProc1;
			}*/	
			if($force_cond!='yes' && $full_enc==0){	
				$totalBalance += $chargeListDetails_all['pat_due']-$overPaymentForProc1;
			}else{
				$totalBalance += $balForProc1-$overPaymentForProc1;
			}
			if($chargeListDetails_all['totalBalance']>0){
				$chk_statement_cnt['bal']=$chargeListDetails_all['charge_list_id'];
			}else{
				$chk_statement_cnt['ovr']=$chargeListDetails_all['charge_list_id'];
			}
			if($chargeListDetails_all['newBalance']!=($chargeListDetails_all['pri_due']+$chargeListDetails_all['sec_due']+$chargeListDetails_all['tri_due']+$chargeListDetails_all['pat_due'])){
				set_payment_trans($chargeListDetails_all['encounter_id'],'',$stop_clm_status);
			}
			/*if($pchld_posFacilityId<=0){
				$pchld_posFacilityId=$chargeListDetails_all['posFacilityId'];
			}*/
		}
		
		if($patientRes['default_facility']>0){
			$pchld_posFacilityId=$patientRes['default_facility'];
		}
		
		$totalBalance = number_format($totalBalance,2);
		$pa=0;
		while($chargeDetails = imw_fetch_array($chl_qry)){	
			//------- GET GROUP DETAILS -----------
			$primaryProviderId=$chargeDetails['primaryProviderId'];
			$gro_id = $chargeDetails['gro_id'];		
			$groupRes = $groupDataArr[$gro_id];		
			$groupName = $groupRes['name'];
			if($groupRes['rem_address1']!=""){
				$group_City=$groupRes['rem_city'];
				$group_State=$groupRes['rem_state'];
				$group_Zip=$groupRes['rem_zip'];
				$groupAddress = $groupRes['rem_address1'].' '.$groupRes['rem_address2'];
				$groupAddress2 = $groupRes['rem_city'].', '.$groupRes['rem_state'].' '.$groupRes['rem_zip'];		
				$group_Telephone_arr = ($groupRes['rem_telephone']);	
				$group_Telephone1=substr($group_Telephone_arr,0,3);
				$group_Telephone2=substr($group_Telephone_arr,3,3);
				$group_Telephone3=substr($group_Telephone_arr,6,4);
				$group_Telephone_ext=$groupRes['rem_telephone_ext'];
				$groupFaxArr = $groupRes['rem_fax'];
				$groupFaxArr1=substr($groupFaxArr,0,3);
				$groupFaxArr2=substr($groupFaxArr,3,3);
				$groupFaxArr3=substr($groupFaxArr,6,4);
			}else{
				$group_City=$groupRes['group_City'];
				$group_State=$groupRes['group_State'];
				$group_Zip=$groupRes['group_Zip'];
				$groupAddress = $groupRes['group_Address1'].' '.$groupRes['group_Address2'];
				$groupAddress2 = $groupRes['group_City'].', '.$groupRes['group_State'].' '.$groupRes['group_Zip'];		
				$group_Telephone_arr = ($groupRes['group_Telephone']);	
				$group_Telephone1=substr($group_Telephone_arr,0,3);
				$group_Telephone2=substr($group_Telephone_arr,3,3);
				$group_Telephone3=substr($group_Telephone_arr,6,4);
				$group_Telephone_ext=$groupRes['group_Telephone_ext'];
				$groupFaxArr = $groupRes['group_Fax'];
				$groupFaxArr1=substr($groupFaxArr,0,3);
				$groupFaxArr2=substr($groupFaxArr,3,3);
				$groupFaxArr3=substr($groupFaxArr,6,4);
			}
			
			if($posFacGroup){
				if($pchld_posFacilityId>0 && $poc_fac_grp_all_arr[$pchld_posFacilityId]>0){
					$pos_fac_grp_data=$pos_fac_grp[$poc_fac_grp_all_arr[$pchld_posFacilityId]];
					$groupName = $pos_fac_grp_data['pos_facility_group'];
					$group_City=$pos_fac_grp_data['fac_group_city'];
					$group_State=$pos_fac_grp_data['fac_group_state'];
					$group_Zip=$pos_fac_grp_data['fac_group_zip'];
					$groupAddress = $pos_fac_grp_data['fac_group_address'].' '.$pos_fac_grp_data['fac_group_address2'];
					$groupAddress2 = $pos_fac_grp_data['fac_group_city'].', '.$pos_fac_grp_data['fac_group_state'].' '.$pos_fac_grp_data['fac_group_zip'];		
					$group_Telephone_arr = str_replace('-','',$pos_fac_grp_data['fac_phone']);	
					$group_Telephone1=substr($group_Telephone_arr,0,3);
					$group_Telephone2=substr($group_Telephone_arr,3,3);
					$group_Telephone3=substr($group_Telephone_arr,6,4);
					$group_Telephone_ext=$pos_fac_grp_data['phone_ext'];
					$groupFaxArr = str_replace('-','',$pos_fac_grp_data['fac_fax']);
					$groupFaxArr1=substr($groupFaxArr,0,3);
					$groupFaxArr2=substr($groupFaxArr,3,3);
					$groupFaxArr3=substr($groupFaxArr,6,4);
				}
			}
			
			if(in_array(strtolower($billing_global_server_name), array('cowan','forman_cnm','scott'))){
				$group_Telephone ='(973) 541 - 9101'.$phone_ext;
				$group_Telephone_show ='(973) 541 - 9101';
				$groupFax ="";
				$groupFax_show="";
			}else{
				$grp_ext="";
				if($group_Telephone_ext!=""){
					$grp_ext=" ext. ".$group_Telephone_ext;
				}
				$group_Telephone = $group_Telephone1.' - '.$group_Telephone2.' - '.$group_Telephone3.$grp_ext;
				$group_Telephone_show = '(P) '.$group_Telephone1.' - '.$group_Telephone2.' - '.$group_Telephone3.$grp_ext;
				
				if($groupFaxArr!=""){
					$groupFax = '(F) '.$groupFaxArr1.' - '.$groupFaxArr2.' - '.$groupFaxArr3;
					$groupFax_show = $groupFaxArr1.' - '.$groupFaxArr2.' - '.$groupFaxArr3;
				}
			}
			
			//--- PATIENT NAME --
			$patNameArr = array();
			$patNameArr["LAST_NAME"] = $patientRes['lname'];
			$patNameArr["FIRST_NAME"] = $patientRes['fname'];
			$patNameArr["MIDDLE_NAME"] = $patientRes['mname'];
			$patient_name = changeNameFormat($patNameArr);
			
			//--- PATIENT ADDRESS ---
			$patientAddress = trim($patientRes['street']).' ';
			if(trim($patientRes['street2']) != ''){
				if(in_array(strtolower($billing_global_server_name), array('edison'))){
					$patientAddress .= ',<br> '.trim($patientRes['street2']);
				}else{
					$patientAddress .= ', '.trim($patientRes['street2']);
				}
			}
			$patientAddress2 = $patientRes['city'].', ';
			$patientAddress2 .= $patientRes['state'].' ';
			$patientAddress2 .= $patientRes['postal_code'];
			$patientAddress2 = ucfirst(trim($patientAddress2));
			if($patientAddress2[0] == ','){
				$patientAddress2 = substr($patientAddress2,1);
			}
			
			$patientNoBalanceBill = $patientRes['noBalanceBill'];			
			$charge_list_id_arr[] = $chargeDetails['charge_list_id'];
			$dateOfService = $chargeDetails['dateOfService'];
			$encounter_id = $chargeDetails['encounter_id'];
			$claims_encounter_id[] = $chargeDetails['encounter_id'];
			$charge_list_id_msg = $chargeDetails['charge_list_id'];
			$patient_id = $chargeDetails['patient_id'];
			if($totalBalance>0){
				$charge_list_id_msg=$chk_statement_cnt['bal'];
			}
			if($statement_base>0){
				//---- STATEMENT MESSAGES ----
				$cnt = $statementCntArr[$patient_id]['statement_count'];
			}else{
				//---- STATEMENT MESSAGES ----
				$cnt = $statementCntArr[$charge_list_id_msg]['statement_count'];
			}
			
			if(trim($rePrint) != ''){
				//$cnt--;
			}
			//$cnt = $cnt == '0' ? '1' : $cnt;
			if($cnt<=0){ $cnt=1;}
			
			//--- RESPONSIBLE PERSON CHECK ------
			if(empty($respartyName) == true){
				$respartyName = $patient_name;
			}
			
			if(empty($respartyAdd) == true){
				$respartyAdd = $patientAddress;
				$respartyAdd1 = $patientAddress2;
				$respartyCity=$patientRes['city'];
				$respartyState=$patientRes['state'];
				$respartyZip=$patientRes['postal_code'];
				$respartyEmail=$patientRes['email'];
			}
			
			$txt_patientName=$patient_name;
			$txt_patientAdd=$patientAddress;
			$txt_patientAdd1=$patientAddress2;
			$txt_patientEmail=$patientRes['email'];
			
			$stementMsgPrint='';
			//--- STATEMENT MESSAGES --------
			if($statement_cycle=='1'){
				if($statementMsgArr[$cnt]==""){
					$stementMsgPrint=$statementMsgArr['last'];
				}else{
					$stementMsgPrint = $statementMsgArr[$cnt];
				}
				if($patientNoBalanceBill == 1){
					$stementMsgPrint = "No Balance Bill";			
				}
			}
			
			//--- SET PRINTING MARGIN -----
			$pageHeight = $marginQryRes['top_margin'];
			$bottomHeight = $marginQryRes['bottom_margin'];
			if(trim($bottomHeight) == ''){
				$bottomHeight = 15;
			}
			
			//--- GET CHARGE LIST DETAILS ----	
			$rowCountArr = array();	
			if($force_cond!='yes' && $full_enc==0){
				$whr_pt_due=" and (patient_charge_list_details.pat_due>0)";
			}
			$chargesQry = "select patient_charge_list_details.*, cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt4_code
						from patient_charge_list_details join cpt_fee_tbl 
						on patient_charge_list_details.procCode = cpt_fee_tbl.cpt_fee_id
						where patient_charge_list_details.del_status='0' and patient_charge_list_details.charge_list_id = '".$chargeDetails['charge_list_id']."'
						$whr_pt_due";
			if(empty($cptCodeStr) === false){
				$chargesQry .= " and patient_charge_list_details.procCode not in ($cptCodeStr)";
			}
			$chargesQryRun=imw_query($chargesQry);
			while($chargeListDetails=imw_fetch_array($chargesQryRun)){
				$notes = $chargeListDetails['notes'];
				$charge_list_detail_id = $chargeListDetails['charge_list_detail_id'];
				$units = $chargeListDetails['units'];
				$overPaymentForProc = $chargeListDetails['overPaymentForProc'];
				$balForProc = $chargeListDetails['newBalance']-$overPaymentForProc;
				/*if($overPaymentForProc>0){
					$balForProc = ' - '.$overPaymentForProc;
				}*/		
				$procCharges = $chargeListDetails['procCharges'] * $units;
				$totalCharges += $procCharges;
				$totalUnits +=$units;
				$totalPaidForProc += $paidForProc;
				if($force_cond!='yes' && $full_enc==0){		
					$totalNewBalance += $chargeListDetails['pat_due']-$overPaymentForProc;
					$balForProc = $chargeListDetails['pat_due']-$overPaymentForProc;
				}else{
					$totalNewBalance += $balForProc;
				}
				$write_off_chld = $chargeListDetails['write_off'];
								
				//---- GET CPT COLUMN DETAILS -------
				$paidDate = array($dateOfService);
				if(trim($chargeListDetails['cpt_desc'])!=""){
					$cpt_desc = array(ucfirst(strtolower(substr($chargeListDetails['cpt_desc'],0,45))));
				}else{
					$cpt_desc=array(ucfirst(strtolower(substr($chargeListDetails['cpt4_code'],0,45))));
				}
				
				$cpt_dx = array($chargeListDetails['cpt4_code']);	
				$units_arr = array($units);	
				$procCharges_arr = array('$'.number_format($procCharges,2));
							
				
				$diagnosis_id1 = trim($chargeListDetails['diagnosis_id1']);
				$diagnosis_id2 = trim($chargeListDetails['diagnosis_id2']);
				$diagnosis_id3 = trim($chargeListDetails['diagnosis_id3']);
				$diagnosis_id4 = trim($chargeListDetails['diagnosis_id4']);
				$diagnosis_id5 = trim($chargeListDetails['diagnosis_id5']);
				$diagnosis_id6 = trim($chargeListDetails['diagnosis_id6']);
				$diagnosis_id7 = trim($chargeListDetails['diagnosis_id7']);
				$diagnosis_id8 = trim($chargeListDetails['diagnosis_id8']);
				$diagnosis_id9 = trim($chargeListDetails['diagnosis_id9']);
				$diagnosis_id10 = trim($chargeListDetails['diagnosis_id10']);
				$diagnosis_id11 = trim($chargeListDetails['diagnosis_id11']);
				$diagnosis_id12 = trim($chargeListDetails['diagnosis_id12']);
				$all_dx="";
				$proc_paid_arr = array("");
				$pat_paid_arr = array("");
				$ins_paid_arr = array("");
				$adj_amt_arr = array("");
				$all_dx_arr=array();
				$balForProc_arr=array();
				if($diagnosis_id1!=""){
					$all_dx_arr[]=$diagnosis_id1;
				}
				if($diagnosis_id2!=""){
					$all_dx_arr[]=$diagnosis_id2;
				}
				if($diagnosis_id3!=""){
					$all_dx_arr[]=$diagnosis_id3;
				}
				if($diagnosis_id4!=""){
					$all_dx_arr[]=$diagnosis_id4;
				}
				if($diagnosis_id5!=""){
					$all_dx_arr[]=$diagnosis_id5;
				}
				if($diagnosis_id6!=""){
					$all_dx_arr[]=$diagnosis_id6;
				}
				if($diagnosis_id7!=""){
					$all_dx_arr[]=$diagnosis_id7;
				}
				if($diagnosis_id8!=""){
					$all_dx_arr[]=$diagnosis_id8;
				}
				if($diagnosis_id9!=""){
					$all_dx_arr[]=$diagnosis_id9;
				}
				if($diagnosis_id10!=""){
					$all_dx_arr[]=$diagnosis_id10;
				}
				if($diagnosis_id11!=""){
					$all_dx_arr[]=$diagnosis_id11;
				}
				if($diagnosis_id12!=""){
					$all_dx_arr[]=$diagnosis_id12;
				}
				$all_dx=implode(', ',$all_dx_arr);
				
				if($all_dx!="" && constant("DISABLE_DXCODE") == ""){
					$cpt_desc[] = "Diagnosis: ".$all_dx;
					$proc_paid_arr[] = "";
					$pat_paid_arr[] = "";
					$ins_paid_arr[] = "";
					$adj_amt_arr[]= "";
					$paidDate[] = '';
					$cpt_dx[] = '  ';
					$units_arr[] = '  ';
					$procCharges_arr[] = '  ';
					$balForProc_arr[] = '  ';
				}
				$primaryInsuranceCoId = $chargeDetails['primaryInsuranceCoId'];
				$secondaryInsuranceCoId = $chargeDetails['secondaryInsuranceCoId'];
				$tertiaryInsuranceCoId = $chargeDetails['tertiaryInsuranceCoId'];
				
				
				$primary_deduct_amount = 0;
				if($primaryInsuranceCoId>0 || $secondaryInsuranceCoId>0 || $tertiaryInsuranceCoId>0){
					//--- GET DEDUCT AMOUNT DETAILS FOR ALL INSURANCE COMPANIES ----
					$qry = imw_query("select deduct_amount,date_format(deduct_date,'%m-%d-%y') as deduct_date,cas_type,cas_code,deduct_ins_id 
							from payment_deductible where deduct_ins_id > 0 
							and charge_list_detail_id = '$charge_list_detail_id' and delete_deduct = '0' order by deductible_id asc");
					while($deductQryRes=imw_fetch_array($qry)){
						$paidDate[] = '';
						$cpt_dx[] = '  ';
						$cpt_desc[] = $deductQryRes['deduct_date'].' Ded. - '.$insCompanyArr[$deductQryRes['deduct_ins_id']].'('.numberFormat($deductQryRes['deduct_amount'],2).')';
						$proc_paid_arr[] = '  ';
						$pat_paid_arr[]='';
						$ins_paid_arr[]='';
						$adj_amt_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
						$show_cas_code=show_cas_code_fun($deductQryRes['cas_type'],$deductQryRes['cas_code']);
						if($show_cas_code!="" && strtolower($show_reason_code_statment)=="yes"){
							$paidDate[] = '';
							$cpt_dx[] = '  ';
							$cpt_desc[] = 'CASCODE'.$show_cas_code;
							$proc_paid_arr[] = '  ';
							$pat_paid_arr[]='';
							$ins_paid_arr[]='';
							$adj_amt_arr[]='';
							$units_arr[] = '  ';
							$procCharges_arr[] = '  ';
							$balForProc_arr[] = '  ';
						}
					}
					
					//--- GET DENIED AMOUNT DETAILS FOR ALL INSURANCE COMPANIES ---------
					$qry = imw_query("select deniedAmount,date_format(deniedDate,'%m-%d-%y') as deniedDate,CAS_type,CAS_code,deniedById 
							from deniedpayment where deniedById >0 
							and charge_list_detail_id = '$charge_list_detail_id' and denialDelStatus = '0' and patient_id = '$patient_id' order by deniedId asc");
					while($deductQryRes=imw_fetch_array($qry)){	
						$paidDate[] = '';
						$cpt_dx[] = '  ';
						$cpt_desc[] = $deductQryRes['deniedDate'].' Denied - '.$insCompanyArr[$deductQryRes['deniedById']].'('.numberFormat($deductQryRes['deniedAmount'],2).')';
						$proc_paid_arr[] = '  ';
						$pat_paid_arr[]='';
						$ins_paid_arr[]='';
						$adj_amt_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
						$show_cas_code=show_cas_code_fun($deductQryRes['CAS_type'],$deductQryRes['CAS_code']);
						if($show_cas_code!="" && strtolower($show_reason_code_statment)=="yes"){
							$paidDate[] = '';
							$cpt_dx[] = '  ';
							$cpt_desc[] = 'CASCODE'.$show_cas_code;
							$proc_paid_arr[] = '  ';
							$pat_paid_arr[]='';
							$ins_paid_arr[]='';
							$adj_amt_arr[]='';
							$units_arr[] = '  ';
							$procCharges_arr[] = '  ';
							$balForProc_arr[] = '  ';
						}
					}				
				}
				
				//--- GET WRITE OFF DETAILS FOR PATIENT AND ALL INSURANCE COMPANIES -----				
				$qry = imw_query("select write_off_by_id,paymentStatus,write_off_amount, date_format(write_off_date,'%m-%d-%y') as write_off_date,CAS_type,CAS_code
						from paymentswriteoff where charge_list_detail_id = '$charge_list_detail_id'
						and delStatus = '0' and patient_id = '$patient_id' and write_off_amount>0 order by write_off_id asc");
				$write_off_amount = 0;
				while($write_off_QryRes=imw_fetch_array($qry)){		
					//$ins_payment_amount += $write_off_QryRes['write_off_amount'];
					$ins_adj_amount += $write_off_QryRes['write_off_amount'];
					$write_off_amount += $write_off_QryRes['write_off_amount'];
					$paidDate[] = '';
					$cpt_dx[] = '  ';
					if($write_off_QryRes['paymentStatus']=="Discount"){
						if($write_off_QryRes['write_off_by_id']>0){
							$cpt_desc[] = $write_off_QryRes['write_off_date'].' Discount - '.$insCompanyArr[$write_off_QryRes['write_off_by_id']];
						}else{
							$cpt_desc[] = $write_off_QryRes['write_off_date'].' Discount - Patient';
						}
					}else{
						if($write_off_QryRes['write_off_by_id']>0){
							$cpt_desc[] = $write_off_QryRes['write_off_date'].' Write-off - '.$insCompanyArr[$write_off_QryRes['write_off_by_id']];
						}else{
							$cpt_desc[] = $write_off_QryRes['write_off_date'].' Write-off - Patient';
						}
					}
					$proc_paid_arr[] = numberFormat($write_off_QryRes['write_off_amount'],2);
					$pat_paid_arr[]='';
					$ins_paid_arr[]='';
					$adj_amt_arr[]=numberFormat($write_off_QryRes['write_off_amount'],2);
					$totalAdj_arr[]=$write_off_QryRes['write_off_amount'];
					$units_arr[] = '  ';
					$procCharges_arr[] = '  ';
					$balForProc_arr[] = '  ';
					
					$show_cas_code=show_cas_code_fun($write_off_QryRes['CAS_type'],$write_off_QryRes['CAS_code']);
					if($show_cas_code!="" && strtolower($show_reason_code_statment)=="yes"){
						$paidDate[] = '';
						$cpt_dx[] = '  ';
						$cpt_desc[] = 'CASCODE'.$show_cas_code;
						$proc_paid_arr[] = '';
						$pat_paid_arr[]='';
						$ins_paid_arr[]='';
						$adj_amt_arr[]='';
						$totalAdj_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
					}
				}
				
				if($write_off_chld>0){
					list($wrt_y,$wrt_m,$wrt_d)=explode('-',$chargeListDetails['write_off_date']);
					$paidDate[] = '';
					$cpt_dx[] = '  ';
					$cpt_desc[] = $wrt_m.'-'.$wrt_d.'-'.substr($wrt_y,2).' Write-off ';
					$proc_paid_arr[] = numberFormat($write_off_chld,2);
					$pat_paid_arr[]='';
					$ins_paid_arr[]='';
					$adj_amt_arr[]=numberFormat($write_off_chld,2);
					$totalAdj_arr[]=$write_off_chld;
					$units_arr[] = '  ';
					$procCharges_arr[] = '  ';
					$balForProc_arr[] = '  ';
				}
				
				$ret_chk_arr=array();
				$getAccPayStr = "SELECT *,date_format(payment_date,'%m-%d-%y') as dateApply,cas_type,cas_code FROM account_payments
									WHERE patient_id = '$patient_id'
									AND encounter_id = '$encounter_id'
									AND charge_list_detail_id = '$charge_list_detail_id'
									and del_status='0'";
				$getAccPayQry = imw_query($getAccPayStr);
				$countAccPayRowsCount = imw_num_rows($getAccPayQry);
				while($getAccPayRows = imw_fetch_array($getAccPayQry)){
					$adj_val="";
					$ins_id=$getAccPayRows['ins_id'];
					$payment_amount_acc=$getAccPayRows['payment_amount'];
					$dateApplied=$getAccPayRows['dateApply'];
					if($getAccPayRows['payment_type']=="Returned Check"){
						$check_number = $getAccPayRows['check_number'];
						$ret_chk_arr[]=$check_number;
					}
					if($getAccPayRows['payment_type']=="Adjustment"){
						$adj_val = 'Adjustment : Patient';
						if($getAccPayRows['payment_by'] != 'Patient'){
							$adj_val = 'Adjustment : '.$insCompanyArr[$ins_id];
						}
						
						if($adj_val!=""){
							$paidDate[] = '';
							$cpt_dx[] = '  ';
							$cpt_desc[] = $dateApplied.' '.$adj_val;	
							$proc_paid_arr[] = '';
							$pat_paid_arr[]='';
							$ins_paid_arr[]='';
							if($payment_amount_acc>0){
								$adj_amt_arr[]=str_replace('$','-$',numberFormat($payment_amount_acc,2));
							}else{
								$adj_amt_arr[]='';
							}
							$totalAdj_arr[]=-$payment_amount_acc;
							$units_arr[] = '  ';
							$procCharges_arr[] = '  ';
							$balForProc_arr[] = '  ';
						}
					}
					if($getAccPayRows['payment_type']=="Over Adjustment"){
						$adj_val = 'Over Adjustment : Patient';
						if($getAccPayRows['payment_by'] != 'Patient'){
							$adj_val = 'Over Adjustment : '.$insCompanyArr[$ins_id];
						}
						if($adj_val!=""){
							$paidDate[] = '';
							$cpt_dx[] = '  ';
							$cpt_desc[] = $dateApplied.' '.$adj_val;	
							$proc_paid_arr[] = '';
							if($getAccPayRows['payment_by'] != 'Patient'){
								$pat_paid_arr[]='';
								$ins_paid_arr[]=numberFormat($payment_amount_acc,2);
							}else{
								$pat_paid_arr[]=numberFormat($payment_amount_acc,2);
								$ins_paid_arr[]='';
							}
							$adj_amt_arr[]='';
							$totalAdj_arr[]=$payment_amount_acc;
							$units_arr[] = '  ';
							$procCharges_arr[] = '  ';
							$balForProc_arr[] = '  ';
						}
					}
					
					if($getAccPayRows['payment_type']=="Co-Insurance" || $getAccPayRows['payment_type']=="Co-Payment"){
						$co_insurance=" Co-Insurance";
						if(trim($getAccPayRows['cas_type'])=="PR" && trim($getAccPayRows['cas_code'])=="3"){
							$co_insurance = " Co-Payment";
						}
						if($getAccPayRows['payment_type']=="Co-Payment"){
							$co_insurance = " Co-Payment";
						}
						$paidDate[] = '';
						$cpt_dx[] = '  ';
						$cpt_desc[] = $dateApplied.$co_insurance.' - '.$insCompanyArr[$ins_id].'('.numberFormat($payment_amount_acc,2).')';
						$proc_paid_arr[] = '  ';
						$pat_paid_arr[]='';
						$ins_paid_arr[]='';
						$adj_amt_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
						$show_cas_code=show_cas_code_fun($getAccPayRows['cas_type'],$getAccPayRows['cas_code']);
					}
					
					$show_cas_code=show_cas_code_fun($getAccPayRows['cas_type'],$getAccPayRows['cas_code']);
					if($show_cas_code!="" && strtolower($show_reason_code_statment)=="yes"){
						$paidDate[] = '';
						$cpt_dx[] = '  ';
						$cpt_desc[] = 'CASCODE'.$show_cas_code;
						$proc_paid_arr[] = '';
						$pat_paid_arr[]='';
						$ins_paid_arr[]='';
						$adj_amt_arr[]='';
						$totalAdj_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
					}
					
				}
					
				//--- GET CREDIT/DEBIT AND ADJUSTMENT DETAILS ----------				
				$qry = imw_query("select *,date_format(dateApplied,'%m-%d-%y') as dateApply
						from creditapplied where credit_applied = '1'
						and delete_credit = '0' and amountApplied > '0'
						and (charge_list_detail_id = '$charge_list_detail_id' or
						charge_list_detail_id_adjust = '$charge_list_detail_id')");
				while($creditQryRes=imw_fetch_array($qry)){	
					$crAppliedTo = $creditQryRes['crAppliedTo'];
					$amountApplied = $creditQryRes['amountApplied'];
					$crAppliedToEncId = $creditQryRes['crAppliedToEncId'];
					$type = $creditQryRes['type'];
					$ins_case = $creditQryRes['ins_case'];
					$credit_note = $creditQryRes['credit_note'];
					$dateApplied = $creditQryRes['dateApply'];
					$payment_mode = $creditQryRes['payment_mode'];
					$checkCcNumber = $creditQryRes['checkCcNumber'];
					$crAppliedToEncId_adjust = $creditQryRes['crAppliedToEncId_adjust'];
					$charge_list_detail_id_ad = $creditQryRes['charge_list_detail_id'];
					$charge_list_detail_id_adjust = $creditQryRes['charge_list_detail_id_adjust'];
					$credit_val = NULL;
					if($crAppliedTo == 'adjustment'){
						if($charge_list_detail_id == $charge_list_detail_id_ad && $charge_list_detail_id != $charge_list_detail_id_adjust){
							$credit_val = 'Adjustment Debit : Patient';
							if($type != 'Patient'){
								$credit_val = 'Adjustment Debit : '.$insCompanyArr[$ins_case];
							}
						}
						else if($charge_list_detail_id != $charge_list_detail_id_ad && $charge_list_detail_id == $charge_list_detail_id_adjust){
							$credit_val = 'Adjustment Credit : Patient';
							if($type != 'Patient'){
								$credit_val = 'Adjustment Credit : '.$insCompanyArr[$ins_case];
							}
						}
					}
					else{
						$credit_val = 'Refund : Patient';
						if($type != 'Patient'){
							$credit_val = 'Refund : '.$insCompanyArr[$ins_case];
						}
						
					}
					if($credit_val){
						$paidDate[] = '';
						$cpt_dx[] = '  ';
						$cpt_desc[] = $dateApplied.' '.$credit_val;	
						$proc_paid_arr[] = numberFormat($amountApplied,2);
						$pat_paid_arr[]='';
						$ins_paid_arr[]='';
						$adj_amt_arr[]=numberFormat($amountApplied,2);
						$totalAdj_arr[]=$amountApplied;
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
					}
				}
						
				//---- GET ALL PAYMENT DETAILS --------
				$qry = imw_query("select patient_charges_detail_payment_info.paidForProc,
						date_format(patient_charges_detail_payment_info.paidDate,'%m-%d-%y')
						as paidDate,patient_charges_detail_payment_info.payment_id,
						patient_charges_detail_payment_info.paidBy,
						patient_charges_detail_payment_info.overPayment,
						patient_chargesheet_payment_info.insProviderId,
						patient_chargesheet_payment_info.paymentClaims,
						patient_chargesheet_payment_info.payment_mode,
						patient_chargesheet_payment_info.checkNo,patient_charges_detail_payment_info.CAS_type,patient_charges_detail_payment_info.CAS_code
						from patient_charges_detail_payment_info join patient_chargesheet_payment_info 
						on patient_chargesheet_payment_info.payment_id = 
						patient_charges_detail_payment_info.payment_id
						where patient_charges_detail_payment_info.charge_list_detail_id = '$charge_list_detail_id'
						and patient_charges_detail_payment_info.deletePayment = '0'
						and patient_chargesheet_payment_info.paymentClaims != 'Interest Payment'
						and patient_chargesheet_payment_info.encounter_id = '$encounter_id'");
				$paidForProcArr = array();
				$paidBy = array();			
				while($pay_res=imw_fetch_array($qry)){	
					$payment_val = $pay_res['paidForProc'] + $pay_res['overPayment'];
					$paymentMethod = $pay_res['payment_mode'];
					$pay_chk_number="";
					if($paymentMethod=='Check'){
						$pay_chk_number = $pay_res['checkNo'];
					}
					if(in_array($pay_chk_number,$ret_chk_arr)){
					}else{
						if($payment_val >= 0){
							$payDesc = 'Paid';
							if($pay_res['paymentClaims'] == 'Negative Payment'){
								$payment_val = '-'.$payment_val;
								$payDesc = 'Negative Payment';
							}
							if($pay_res['paymentClaims'] == 'Deposit'){
								$payDesc = 'Deposit';
							}
	
						
							$paidForProcArr[] = $payment_val;
							$paidBy[] = $pay_res['paidBy'];
		
							if($pay_res['paidBy'] == 'Insurance'){
								$insProviderId = $pay_res['insProviderId'];
								$cpt_dx[] = '  ';
								$cpt_desc[] = $pay_res['paidDate'].' '.$payDesc.' - '.$insCompanyArr[$insProviderId];
								$ins_payment_amount += $payment_val;
								$totalInsPaid_arr[]=$payment_val;
								if(in_array(strtolower($billing_global_server_name), array('arkansasoutpatient'))){
									$payment_val = numberFormat($payment_val,2);
								}else{
									$payment_val = numberFormat($payment_val,2,1);
								}
								if($payment_val=="0.00"){$payment_val="$0.00";}
								$proc_paid_arr[] = $payment_val;
								$pat_paid_arr[]='&nbsp;';
								$ins_paid_arr[]=$payment_val;
								$paidDate[] = '';	
								$adj_amt_arr[]='';	
								$units_arr[] = '  ';
								$procCharges_arr[] = '  ';
								$balForProc_arr[] = '  ';
							}	
							else{
								$cpt_dx[] = '  ';
								$cpt_desc[] = $pay_res['paidDate'].' '.$payDesc.' - Patient';
								$payment_amount += $payment_val;
								$totalPatPaid_arr[]=$payment_val;
								if(in_array(strtolower($billing_global_server_name), array('arkansasoutpatient'))){
									$payment_val = numberFormat($payment_val,2);
								}else{
									$payment_val = numberFormat($payment_val,2,1);
								}
								if($payment_val=="0.00"){$payment_val="$0.00";}
								$proc_paid_arr[] = $payment_val;
								$pat_paid_arr[]=$payment_val;
								$ins_paid_arr[]='&nbsp;';
								$paidDate[] = '';
								$adj_amt_arr[]='';
								$units_arr[] = '  ';	
								$procCharges_arr[] = '  ';
								$balForProc_arr[] = '  ';	
							}
							
							$show_cas_code=show_cas_code_fun($pay_res['CAS_type'],$pay_res['CAS_code']);
							if($show_cas_code!="" && strtolower($show_reason_code_statment)=="yes"){
								$paidDate[] = '';
								$cpt_dx[] = '  ';
								$cpt_desc[] = 'CASCODE'.$show_cas_code;
								$proc_paid_arr[] = '';
								$pat_paid_arr[]='';
								$ins_paid_arr[]='';
								$adj_amt_arr[]='';
								$totalAdj_arr[]='';
								$units_arr[] = '  ';
								$procCharges_arr[] = '  ';
								$balForProc_arr[] = '  ';
							}
							
						}
					}
				}
				
				$paidForProc = array_sum($paidForProcArr);
				
				//---- GET COPAY DETAILS --------
				$coPayAdjustedAmount = $chargeListDetails['coPayAdjustedAmount'];
				if($coPayAdjustedAmount == 1){
					$qry = imw_query("select patient_charges_detail_payment_info.paidForProc,
							date_format(patient_charges_detail_payment_info.paidDate,'%m-%d-%y')
							as paidDate,patient_charges_detail_payment_info.payment_id,
							patient_charges_detail_payment_info.paidBy,
							patient_charges_detail_payment_info.overPayment,
							patient_chargesheet_payment_info.insProviderId,
							patient_chargesheet_payment_info.paymentClaims
							from patient_charges_detail_payment_info join patient_chargesheet_payment_info 
							on patient_chargesheet_payment_info.payment_id = 
							patient_charges_detail_payment_info.payment_id
							where patient_charges_detail_payment_info.charge_list_detail_id = '0'
							and patient_charges_detail_payment_info.deletePayment = '0'
							and patient_chargesheet_payment_info.paymentClaims != 'Interest Payment'
							and patient_chargesheet_payment_info.encounter_id = '$encounter_id'");
					$copay_qry_res=imw_fetch_array($qry);	
					$totalCopayPaidAmount = $copay_qry_res['paidForProc'] + $copay_qry_res['overPayment'];
					if($copay_qry_res['paymentClaims'] == 'Negative Payment'){
						$totalCopayPaidAmount = '-'.$totalCopayPaidAmount;
					}
					
					if($copay_qry_res['paidBy'] == 'Insurance'){
						$insProviderId = $copay_qry_res['insProviderId'];
						$cpt_dx[] = '  ';
						$cpt_desc[] = $copay_qry_res['paidDate'].' Copay Paid - '.$insCompanyArr[$insProviderId];
						$ins_payment_amount += $totalCopayPaidAmount;
						$totalPaidForProc += $totalCopayPaidAmount;
						$copay_paid += $totalCopayPaidAmount;
						$totalInsPaid_arr[]=$totalCopayPaidAmount;
						$totalCopayPaidAmount = numberFormat($totalCopayPaidAmount,2);
						
						$proc_paid_arr[] = $totalCopayPaidAmount;
						$pat_paid_arr[]='&nbsp;';
						$ins_paid_arr[]=$totalCopayPaidAmount;
						$paidDate[] = '';
						$adj_amt_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
					}	
					else{
						$cpt_dx[] = '  ';
						$cpt_desc[] = $copay_qry_res['paidDate'].' Copay Paid - Patient';
						$totalPaidForProc += $totalCopayPaidAmount;
						$copay_paid += $totalCopayPaidAmount;
						$totalPatPaid_arr[]=$totalCopayPaidAmount;
						$totalCopayPaidAmount = numberFormat($totalCopayPaidAmount,2);
						
						$proc_paid_arr[] = $totalCopayPaidAmount;
						$pat_paid_arr[]=$totalCopayPaidAmount;
						$ins_paid_arr[]='&nbsp;';
						$paidDate[] = '';
						$adj_amt_arr[]='';
						$units_arr[] = '  ';
						$procCharges_arr[] = '  ';
						$balForProc_arr[] = '  ';
					}	
				}
					
				$rowCountArr[] = count($proc_paid_arr);
				//echo "<pre>";
				//print_r($ins_paid_arr);
				//print_r($adj_amt_arr);
				//print_r($pat_paid_arr);exit();
				$proc_paid_str = join('<br>',$proc_paid_arr);
				$pat_paid_str = join('<br>',$pat_paid_arr);
				$ins_paid_str = join('<br>',$ins_paid_arr);
				$adj_amt_str = join('<br>',$adj_amt_arr);
				$cptDesc = join('<br>',$cpt_desc);
				$paid_date = join('<br>',$paidDate);
				$cptDx = join('<br>',$cpt_dx);
				$unitsArr = join('<br>',$units_arr);
				$balForProc_arr[] = '$'.number_format($balForProc,2);
				$procCharges_str = join('<br>',$procCharges_arr);
				$balForProc_str = join('<br>',$balForProc_arr);
				
				$procCharges = number_format($procCharges,2);
				$balForProc = number_format($balForProc,2);
				$notes_data = NULL;
				if($notes != '' and $inc_chr_amt == 'yes'){
					$note_arr['notes'][$patient_id][]=$notes;
					$stat_tpl_arr['notes'][$patient_id][] = $notes;
					$notes_data ='<tr><td style="border:thin #000000 1px; padding-left:10px;" colspan="6" class="text_13">'.$notes.'</td></tr>';
				}else{
					$stat_tpl_arr['notes'][$patient_id][] = $notes;
				}
				
				//--- GET ALL CREDIT AMOUNT DETAILS ---------
				/*$qry = imw_query("select amountApplied,patient_id,charge_list_detail_id_adjust,
							crAppliedToEncId_adjust from creditapplied where patient_id_adjust in($patient_id_str) 
							and charge_list_detail_id_adjust in($charge_list_detail_id_str)");*/
								
				$proc_chr_arr = array();
				$bal_chr_arr = array();
				$proc_chr_arr[] = "$".$procCharges;
				$bal_chr_arr[] = "$".$balForProc;
				$stat_arr['enc'][] = $encounter_id;
				$stat_arr['date'][$encounter_id][] = $paidDate;
				$stat_arr['units'][$encounter_id][] = $units;
				$stat_arr['cpt'][$encounter_id][] = $cpt_dx;
				$stat_arr['desc'][$encounter_id][] = $cpt_desc;
				$stat_arr['chr'][$encounter_id][] = $proc_chr_arr;
				$stat_arr['ins_paid'][$encounter_id][] = str_replace("&nbsp;",' ',$ins_paid_arr);
				$stat_arr['adj_paid'][$encounter_id][] = $adj_amt_arr;
				$stat_arr['pat_paid'][$encounter_id][] = str_replace("&nbsp;",' ',$pat_paid_arr);
				$stat_arr['paid'][$encounter_id][] = $proc_paid_arr;
				$stat_arr['bal'][$encounter_id][] = $bal_chr_arr;
				$show_balForProc='$'.$balForProc;
				$show_balForProc=str_replace('$-','-$',$show_balForProc);
				
				if(strstr($statement_data_db,"{encounter_info}")){
					$stat_tpl_arr['date'][$patient_id][] = $paidDate;
					$stat_tpl_arr['cpt'][$patient_id][] = $cpt_dx;
					$stat_tpl_arr['desc'][$patient_id][] = $cpt_desc;
					$stat_tpl_arr['units'][$patient_id][] = $units_arr;
					$stat_tpl_arr['chr'][$patient_id][] = $procCharges_arr;
					$stat_tpl_arr['ins_paid'][$patient_id][] = $ins_paid_arr;
					$stat_tpl_arr['adj_paid'][$patient_id][] = $adj_amt_arr;
					$stat_tpl_arr['pat_paid'][$patient_id][] = $pat_paid_arr;
					$stat_tpl_arr['bal'][$patient_id][] = $balForProc_arr;
				}else{
					$stat_tpl_arr['date'][$patient_id][] = $paid_date;
					$stat_tpl_arr['cpt'][$patient_id][] = $cptDx;
					$stat_tpl_arr['desc'][$patient_id][] = $cptDesc;
					$stat_tpl_arr['units'][$patient_id][] = $unitsArr;
					$stat_tpl_arr['chr'][$patient_id][] = $procCharges_str;
					$stat_tpl_arr['ins_paid'][$patient_id][] = $ins_paid_str;
					$stat_tpl_arr['adj_paid'][$patient_id][] = $adj_amt_str;
					$stat_tpl_arr['pat_paid'][$patient_id][] = $pat_paid_str;
					$stat_tpl_arr['bal'][$patient_id][] = str_replace('$-','-$',$balForProc_str);
				}
			}
			
			$rowCount = array_sum($rowCountArr);
			$rowCount = floor($rowCount/30);
			if($rowCount <= 0){
				$rowCount = 1;
			}
			$show_adj_tot_row="yes";
			$show_home_fac_row="yes";
			if(in_array(strtolower($billing_global_server_name), array('cowan','forman_cnm','scott','hammad','hammad_iasc'))){
				$acc_no_title="ACCOUNT#";
				if(in_array(strtolower($billing_global_server_name), array('scott','hammad','hammad_iasc'))){
					$add_cc_title_in_chk="CC/Check#";
				}else{
					$add_cc_title_in_chk="Check#";
				}
			}else if(in_array(strtolower($billing_global_server_name), array('liasc','precision'))){
				$acc_no_title="ACCOUNT NBR";
				$add_cc_title_in_chk="CC/Check#";
				$show_adj_tot_row="no";
				$show_home_fac_row="no";
			}else{
				$acc_no_title="ACCOUNT MRN";
				$add_cc_title_in_chk="CC/Check#";
			}
			$top_grp=$groupName;
			if(in_array(strtolower($billing_global_server_name), array('allianceretina'))){
				$top_grp="J. Gregory Rosenthal, M.D.";
			}
			if($pa == 0){
				$txt_arr[$p][] = $requestArr[$cnt];
				$txt_arr[$p][] = $top_grp;
				$txt_arr[$p][] = "IF PAYING BY CREDIT CARD, FILL OUT BELOW";
				$txt_arr[$p][] = "CHECK CARD USING FOR PAYMENT";
				$txt_arr[$p][] = $groupAddress;
				$txt_arr[$p][] = $AE_show_name;
				$txt_arr[$p][] = "MASTER CARD";
				$txt_arr[$p][] = "VISA";
				$txt_arr[$p][] = $groupAddress2;
				$txt_arr[$p][] = $add_cc_title_in_chk;
				$txt_arr[$p][] = "AMOUNT";
				$txt_arr[$p][] = "SIGNATURE";
				$txt_arr[$p][] = "EXP. DATE";
				$txt_arr[$p][] = "STATEMENT";
				$txt_arr[$p][] = "STATEMENT DATE";
				$txt_arr[$p][] = "PAY THIS AMOUNT";
				$txt_arr[$p][] = $acc_no_title;
				$txt_arr[$p][] = $todayDate;
				$txt_arr[$p][] = "$".$totalBalance;
				$txt_arr[$p][] = $patient_id;
				$txt_arr[$p][] = "ADDRESSEE:";
				$txt_arr[$p][] = "REMIT TO:";
				$txt_arr[$p][] = "SHOW AMOUNT";
				$txt_arr[$p][] = "PAID HERE $";
				$txt_arr[$p][] = $respartyName;
				if(in_array(strtolower($billing_global_server_name), array('hammad_iasc_stop'))){
					$txt_arr[$p][] = $groupName;
					$txt_arr[$p][] = str_replace('<br>','',$txt_patientAdd);
					$txt_arr[$p][] = $hammad_iasc_add1;
					$txt_arr[$p][] = $txt_patientAdd1;
					$txt_arr[$p][] = $hammad_iasc_add2;
				}else{
					$txt_arr[$p][] = $groupName;
					$txt_arr[$p][] = str_replace('<br>','',$respartyAdd);
					$txt_arr[$p][] = $groupAddress;
					$txt_arr[$p][] = $respartyAdd1;
					$txt_arr[$p][] = $groupAddress2;
				}
				$txt_arr[$p][] = "Please check box if above address is ";
				$txt_arr[$p][] = "incorrect or insurance information has ";	
				$txt_arr[$p][] = "changed, and indicate change(s) on ";
				$txt_arr[$p][] = "reverse side.";
				$txt_arr[$p][] = "PLEASE DETACH AND RETURN TOP PORTION WITH ";
				$txt_arr[$p][] = "YOUR PAYMENT ".$group_Telephone.$phone_ext;
		
				$respartyName_exp=explode(",",$respartyName);								
				$respartyName=$respartyName_exp[1]." ".$respartyName_exp[0];
				$res_br="";
				if($respartyAdd!="" && $respartyAdd1!=""){
					$res_br="</br>";
				}
	
				$backbottom='10mm';
				$agingFooterData='';
				if($ar_aging=='1'){
					$backbottom='40mm';
					$stm_aging_to=120;
					include('print_ar_aging.php');
					if($agingPdfData!=""){
						$agingFooterData='<tr><td>'.$agingPdfData.'</td></tr>';
					}
				}
				
				$txt_arr[$p][] = $chargeDetails['comment'];
				$comments = $chargeDetails['comment'];
				
				$txt_arr[$p][] = "DOS";
				$txt_arr[$p][] = "CPT";
				$txt_arr[$p][] = "Description";
				$txt_arr[$p][] = "Units";
				$txt_arr[$p][] = "T. Charges";
				$txt_arr[$p][] = "Ins Paid";
				$txt_arr[$p][] = "Adjust";
				$txt_arr[$p][] = "Pt Paid";
				$txt_arr[$p][] = "Balance";
			}	
			if($txt_arr[$p][47] != "TOTAL AMOUNT:"){
				$txt_arr[$p][46] = $stat_arr;
			}			
			$pa++;
		}
		
		//----- PAYMENT COMMENTS FOR SINGLE ENCOUNTER ---------
		$pay_comments = NULL;
		$pay_txt_comments = NULL;
		$claimsEncounterId = join(',',$claims_encounter_id);
		$qry = imw_query("SELECT encComments, date_format(encCommentsDate,'%m-%d-%Y') as encCommentsDate 
						FROM paymentscomment WHERE encounter_id in($claimsEncounterId) AND commentsType = 'External' and c_type != 'batch'");
		$total_page_count_var += imw_num_rows($qry)+5;
		while($externalCommentsRes=imw_fetch_array($qry)){
			$encCommentsDate = $externalCommentsRes['encCommentsDate'];
			$encComments = $externalCommentsRes['encComments'];
			if(in_array(strtolower($billing_global_server_name), array('fairview'))){
				$pay_txt_comments .= ucfirst($encCommentsDate.' '.$encComments)."\r\n";
				$pay_comments .= ucfirst($encCommentsDate.' '.$encComments).'<br>';
			}else{
				$pay_txt_comments .= substr(ucfirst($encCommentsDate.' '.$encComments),0,110)."\r\n";
				$pay_comments .= substr(ucfirst($encCommentsDate.' '.$encComments),0,500).'<br>';
			}
		}
		
		$totalPaidArr = array($payment_amount, $ins_payment_amount, $copay_paid);
		$total_paid =  array_sum($totalPaidArr);
		
		$totalCharges = numberFormat($totalCharges, 2);	
		$totalNewBalance = numberFormat($totalNewBalance, 2);
		$total_paid_txt = numberFormat($total_paid, 2);
		$total_paid = numberFormat($total_paid, 2);	
		$payment_amount_txt = numberFormat($payment_amount, 2);
		$payment_amount = numberFormat($payment_amount, 2);	
		$ins_payment_amount_txt = numberFormat($ins_payment_amount, 2);
		$ins_payment_amount = numberFormat($ins_payment_amount, 2);
		$ins_adj_amount_txt = numberFormat($ins_adj_amount, 2);
		$ins_adj_amount = numberFormat($ins_adj_amount, 2);
		$totalInsPaid = numberFormat(array_sum($totalInsPaid_arr), 2);
		$totalAdj = numberFormat(array_sum($totalAdj_arr), 2);
		$totalPatPaid = numberFormat(array_sum($totalPatPaid_arr), 2);
		--$pa;
		
		$txt_arr[$p][] = "TOTAL AMOUNT:";
		$txt_arr[$p][] = $totalUnits;
		$txt_arr[$p][] = $totalCharges;
		$txt_arr[$p][] = $totalInsPaid;
		$txt_arr[$p][] = $totalAdj;
		$txt_arr[$p][] = $totalPatPaid;
		$txt_arr[$p][] = $totalNewBalance;
		$patient_id_bal[$gro_id][$patient_id] = str_replace(',','',str_replace('$','',$totalNewBalance));
		
		$txt_arr[$p][] = "Please Pay:";
		$txt_arr[$p][] = $totalNewBalance;
		$txt_arr[$p][] = "Comments : ".$pay_txt_comments;	
		$txt_arr[$p][] = "Account Number";
		$txt_arr[$p][] = "New Balance";
		$txt_arr[$p][] = "New Payment";
		$txt_arr[$p][] = "Ins.Paid";
		$txt_arr[$p][] = "Adjustment";
		$txt_arr[$p][] = "Current Due";
		$txt_arr[$p][] = substr($patient_name,0,20) ."-". $patient_id;
		$txt_arr[$p][] = $totalNewBalance;
		$txt_arr[$p][] = $payment_amount;
		$txt_arr[$p][] = $ins_payment_amount;
		$txt_arr[$p][] = $totalAdj;
		$txt_arr[$p][] = $totalNewBalance;
		$txt_arr[$p][] = $stementMsgPrint;
		
		$statement_tpl_data="";
		$statement_tpl_data=$statement_data_db;

		$data_show_arr=array();
		$data_show_sec_arr=array();
		$data_show_all_arr=explode('data-show="',$statement_tpl_data);
		for($ds=0;$ds<=count($data_show_all_arr);$ds++){
			$data_show_sec_arr=explode('"',$data_show_all_arr[$ds]);
			$data_show_arr[]=$data_show_sec_arr[0];
		}
		if(in_array('home_facility',$data_show_arr)){
			$txt_arr[$p][] = "Home Facility: ".$pt_pos_facility;
		}else{
			$txt_arr[$p][] = "";
		}

		if(strstr($statement_tpl_data,"{PHYSICIAN LAST NAME}")){
			$txt_arr[$p][] = "Physician: ".$phy_id_arr[$primaryProviderId]['lname'].', '.$phy_id_arr[$primaryProviderId]['fname'];
		}else if(in_array('primary_eye_care',$data_show_arr)){
			$txt_arr[$p][] = "Primary Eye Care: ".$pt_pri_eye_care;
		}else{
			$txt_arr[$p][] = "";
		}
		$txt_arr[$p][] = $gro_id;
		
		$late_fee_exp=explode('data-latefee="',$statement_tpl_data);
		$late_fee_exp_final=explode('"',$late_fee_exp[1]);
		$late_interest_exp=explode('data-interest="',$statement_tpl_data);
		$late_interest_exp_final=explode('"',$late_interest_exp[1]);
		
		if($late_fee_exp_final[0]>0){
			$txt_arr[$p][] = "Late Fee:";
			$txt_arr[$p][] = "$".$late_fee_exp_final[0];
		}else{
			$txt_arr[$p][] = "";
			$txt_arr[$p][] = "";
		}
		if($late_interest_exp_final[0]!=""){
			$txt_arr[$p][] = "Interest Charges of ".$late_interest_exp_final[0].":";
			$txt_arr[$p][] = "";
		}else{
			$txt_arr[$p][] = "";
			$txt_arr[$p][] = "";
		}
		$txt_arr[$p][] = $spl_msg;
		$txt_arr[$p][] = $agingFooterData;
		
		if(in_array(strtolower($billing_global_server_name), array('hammad_iasc_stop'))){
			$txt_arr[$p][] = $txt_patientEmail;
		}else{
			$txt_arr[$p][] = $respartyEmail;
		}
		
		if(in_array(strtolower($billing_global_server_name), array('hattiesburg','stileseyecare'))){
			$txt_arr[$p][] = "GUARANTOR:";
			if($txt_respartyName==""){
				$txt_arr[$p][] = $txt_patientName;
				$txt_arr[$p][] = str_replace('<br>','',$txt_patientAdd);
				$txt_arr[$p][] = $txt_patientAdd1;
				$txt_arr[$p][] = $txt_patientEmail;
			}else{
				$txt_arr[$p][] = $txt_respartyName;
				$txt_arr[$p][] = str_replace('<br>','',$txt_respartyAdd);
				$txt_arr[$p][] = $txt_respartyAdd1;
				$txt_arr[$p][] = $txt_respartyEmail;
			}
		}else{
			$txt_arr[$p][] = "";
			$txt_arr[$p][] = "";
			$txt_arr[$p][] = "";
			$txt_arr[$p][] = "";
			$txt_arr[$p][] = "";
		}
		
		$min_pay_due_stm=trim(str_replace('$','',$min_pay_due_stm));
		if(stristr($min_pay_due_stm,"%")) {
			$min_pay_due_stm_arr=explode('%',$min_pay_due_stm);
			$min_pay_due_stm_show=(trim(str_replace('$','',$totalNewBalance))*$min_pay_due_stm_arr[0])/100;
		}else{
			$min_pay_due_stm_show=trim($min_pay_due_stm);
		}
		
		
		$statement_tpl_data = str_ireplace('/'.$web_RootDirectoryName.'/data/'.PRACTICE_PATH.'/gn_images/',$fileroot.'/data/'.PRACTICE_PATH.'/gn_images/',$statement_tpl_data);
		$statement_tpl_data = str_ireplace($GLOBALS['webroot'].'/library/images/',$fileroot.'/library/images/',$statement_tpl_data);
		$statement_tpl_data=str_ireplace('/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/','',$statement_tpl_data);	
		$statement_tpl_data=str_ireplace('{GROUP NAME}',$groupName,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{GROUP ADDRESS}',$groupAddress,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{GROUP CITY}',$group_City,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{GROUP STATE}',$group_State,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{GROUP ZIP}',$group_Zip,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{GROUP PHONE}',$group_Telephone,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{GROUP FAX}',$groupFax_show,$statement_tpl_data);
		
		$statement_tpl_data=str_ireplace('{STATEMENT REQUEST}',$requestArr[$cnt],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{STATEMENT DATE}',$todayDate,$statement_tpl_data);
		
		$statement_tpl_data=str_ireplace('{PATIENT ID}',$patient_id,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT FIRST NAME}',$patientRes['fname'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT MIDDLE NAME}',$patientRes['mname'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT LAST NAME}',$patientRes['lname'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT ADDRESS}',$respartyAdd,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT CITY}',$patientRes['city'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT STATE}',$patientRes['state'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PATIENT ZIP}',$patientRes['postal_code'],$statement_tpl_data);
		
		$statement_tpl_data=str_ireplace('{RESPONSIBLE NAME}',$respartyName,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{RESPONSIBLE ADDRESS}',$respartyAdd,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{RESPONSIBLE CITY}',$respartyCity,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{RESPONSIBLE STATE}',$respartyState,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{RESPONSIBLE ZIP}',$respartyZip,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{POS Facility}',$pt_pos_facility,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{Primary Eye Care}',$pt_pri_eye_care,$statement_tpl_data);
		
		$statement_tpl_data=str_ireplace('{PHYSICIAN NAME TITLE}',$phy_id_arr[$primaryProviderId]['pro_title'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PHYSICIAN FIRST NAME}',$phy_id_arr[$primaryProviderId]['fname'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_id_arr[$primaryProviderId]['mname'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PHYSICIAN LAST NAME}',$phy_id_arr[$primaryProviderId]['lname'],$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_id_arr[$primaryProviderId]['pro_suffix'],$statement_tpl_data);
		
		$mlb_tl="border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;";
		$mlb_tlr="border-top-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-right-width: 1px; border-left-width: 1px; border-top-style: solid; border-right-style: solid; border-left-style: solid;";
		$lb_tl="border-left-color: rgb(0, 0, 0); border-left-width: 1px; border-left-style: solid;";
		$lb_tlr="border-right-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-right-width: 1px; border-left-width: 1px; border-right-style: solid; border-left-style: solid;";

		$stat_tpl_arr2='Date</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">CPT</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">Description</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">Units</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">T. Charges</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">Ins Paid</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">Adj</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tl.'">Pt Paid</td>
				<td style="text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$mlb_tlr.'">'.$balance_title.'</td>
			</tr>';
		for($g=0;$g<count($stat_tpl_arr['date'][$patient_id]);$g++){
			if(strstr($statement_tpl_data,"{encounter_info}")){
				foreach($stat_tpl_arr['date'][$patient_id][$g] as $stat_key => $stat_val){
					$slb_tl=$mlb_tl;
					$slb_tlr=$mlb_tlr;
					if($stat_tpl_arr['date'][$patient_id][$g][$stat_key]==""){
						$slb_tl=$lb_tl;
						$slb_tlr=$lb_tlr;
					}
					$stat_tpl_arr2 .='<tr><td style="width: 50px; text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['date'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 50px; text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['cpt'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 235px; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.str_replace('CASCODE','',$stat_tpl_arr['desc'][$patient_id][$g][$stat_key]).'</td>';
					$stat_tpl_arr2 .='<td style="width: 40px; text-align: center; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['units'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 55px; text-align: right; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['chr'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 65px; text-align: right; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['ins_paid'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 40px; text-align: right; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['adj_paid'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 55px; text-align: right; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tl.'">'.$stat_tpl_arr['pat_paid'][$patient_id][$g][$stat_key].'</td>';
					$stat_tpl_arr2 .='<td style="width: 20px; text-align: right; padding-top: 1px; padding-bottom: 1px; vertical-align: top; '.$slb_tlr.'">'.str_replace('$-','-$',$stat_tpl_arr['bal'][$patient_id][$g][$stat_key]).'</td></tr>';
				}
			}else{
				$stat_tpl_arr2 .='<tr><td style="width: 50px; text-align: center; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['date'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 50px; text-align: center; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['cpt'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 235px; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['desc'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 40px; text-align: center; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['units'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 55px; text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['chr'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 65px; text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['ins_paid'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 40px; text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['adj_paid'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 55px; text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$stat_tpl_arr['pat_paid'][$patient_id][$g].'</td>';
				$stat_tpl_arr2 .='<td style="width: 20px; text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-right-width: 1px; border-left-width: 1px; border-top-style: solid; border-right-style: solid; border-left-style: solid;">'.$stat_tpl_arr['bal'][$patient_id][$g].'</td></tr>';
			}
			
			if($stat_tpl_arr['notes'][$patient_id][$g]!="" && !in_array(strtolower($billing_global_server_name), array('dso','cep'))){
				$stat_tpl_arr2 .="<tr><td style='border:thin #000000 1px; padding-left:10px;' colspan='9' class='text_13'>".$stat_tpl_arr['notes'][$patient_id][$g]."</td></tr>";
			}
		}
		if(count($stat_tpl_arr['date'][$patient_id])>0){
			$stat_tpl_arr2 .='<tr>
					<td colspan="3" style="text-align: right; padding:2px 5px 2px 2px; font-weight: bold; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">TOTAL AMOUNT:</td>
					<td style="text-align: center; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$totalUnits.'</td>
					<td style="text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$totalCharges.'</td>
					<td style="text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$totalInsPaid.'</td>
					<td style="text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$totalAdj.'</td>
					<td style="text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-left-width: 1px; border-top-style: solid; border-left-style: solid;">'.$totalPatPaid.'</td>
					<td style="text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top; border-top-color: rgb(0, 0, 0); border-right-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-right-width: 1px; border-left-width: 1px; border-top-style: solid; border-right-style: solid; border-left-style: solid;">
					'.$totalNewBalance.'</td>
				</tr>';	
		}
		$stat_tpl_arr2 .='<tr>
					<td colspan="8" style="width: 610px; text-align: right; padding:2px 5px 2px 2px; font-weight: bold; vertical-align: top; border-top-color: rgb(0, 0, 0); border-bottom-color: rgb(0, 0, 0); border-left-color: rgb(0, 0, 0); border-top-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-style: solid; border-bottom-style: solid; border-left-style: solid;">Please Pay:</td>
					<td style="border: 1px solid rgb(0, 0, 0); border-image: none; width: 80px; text-align: right; padding-top: 2px; padding-bottom: 2px; vertical-align: top;">'.$totalNewBalance;	
		if(strstr($statement_tpl_data,"{encounter_info}")){
			$statement_tpl_data=str_ireplace('{encounter_info}',$stat_tpl_arr2,$statement_tpl_data);
			if($late_fee_exp_final[0]>0 || $late_interest_exp_final[0]!=""){
				$statement_tpl_data=str_ireplace('data-colspan=""','colspan="9"',str_ireplace('colspan="9"','',$statement_tpl_data));
			}
		}else{
			$statement_tpl_data=str_ireplace('{DOS}',implode('<br>',$stat_tpl_arr['date'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{CPT}',implode('<br>',$stat_tpl_arr['cpt'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{CPT DESCRIPTION}',implode('<br>',$stat_tpl_arr['desc'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{UNIT}',implode('<br>',$stat_tpl_arr['units'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{CHARGES}',implode('<br>',$stat_tpl_arr['chr'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{INSURANCE PAID}',implode('<br>',$stat_tpl_arr['ins_paid'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{ADJUSTMENT}',implode('<br>',$stat_tpl_arr['adj_paid'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{PT PAID}',implode('<br>',$stat_tpl_arr['pat_paid'][$patient_id]),$statement_tpl_data);
			$statement_tpl_data=str_ireplace('{BALANCE}',implode('<br>',$stat_tpl_arr['bal'][$patient_id]),$statement_tpl_data);
		}
		
		$statement_tpl_data=str_ireplace('{TOTAL UNIT}',$totalUnits,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{TOTAL CHARGES}',$totalCharges,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{TOTAL INSURANCE PAID}',$totalInsPaid,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{TOTAL ADJUSTMENT}',$totalAdj,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{TOTAL PT PAID}',$totalPatPaid,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{TOTAL BALANCE}',$totalNewBalance,$statement_tpl_data);
		
		$statement_tpl_data=str_ireplace('{NEW BALANCE SINCE LAST BILL}',$totalNewBalance,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{NEW PAYMENT SINCE LAST BILL}',$payment_amount,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{INSURANCE PAID SINCE LAST BILL}',$ins_payment_amount,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{COMMENT}',$pay_comments,$statement_tpl_data);
		$statement_tpl_data=str_ireplace('{MIN PAY DUE IN STATEMENT}','$'.number_format($min_pay_due_stm_show,2),$statement_tpl_data);
	
		
		$statementHeader.='<page backtop="'.$pageHeight.'" backbottom="'.$backbottom.'">
					 <page_footer>
						<table border="0" style="width:700px;">
							'.$agingFooterData.'
							<tr>
								<td style="text-align:center; width:700px;" class="text_13b">'.$stementMsgPrint.'</td>
							</tr>
							<tr>
								<td style="text-align:left; width:700px;" class="text_13b">'.$spl_msg.'</td>
							</tr>';
		if(in_array(strtolower($billing_global_server_name), array('associatedeye'))){
			$statementHeader.='	<tr><td style="text-align:center; width:700px;font-weight:bold;" class="text_13b">STATEMENT</td></tr>';
		}
		$statementHeader.='<tr>
								<td height="'.$bottomHeight.'"></td>
							</tr>
						</table>
					</page_footer>';
		$statementData=$statementHeader;			
		$statementData.=$statement_tpl_data;
		$statements .= $statementData;
		//--- CREATE ARRAY FOR PREVIOUS STATEMENTS -------
		$statementData = preg_replace('/<\/page>/','',$statementData);
		$pat_statements[$gro_id][$patient_id] = $statementData.'</page>';
		
		if($_REQUEST['print_pdf']=='email')
		{
			$pt_email=$patientRes['email'];
			$respartyName=ucwords(trim($patientRes['lname'].", ".$patientRes['fname']." ".$patientRes['mname']));
			$divData = $pat_statements[$gro_id][$patient_id];
			include('emailStatements.php');
		}
	}
	if(imw_num_rows($patientQry)>0 && $_REQUEST['print_pdf']!='email'){	
		$divData = $statements.'</page>';
	}
}
include "statement_txt.php";
if($st_srh_frm!="Accounting"){
	if($text_print>0 || $_REQUEST['print_pdf']=='email' || $emailStatement>0){
	}else{
		if($st_start>0){
			$chk_file_append="yes";
		}
		$html = $divData;
		$filePath=write_html($html,'',$chk_file_append);
		if($st_start > $tot_pat_len && $_REQUEST['print_pdf']!='email'){
			echo "<script type='text/javascript'>html_to_pdf('".$filePath."','p','','','','html_to_pdf_reports');</script>";
		}
	}
}
?>