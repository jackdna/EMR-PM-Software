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
$title = "Payments Ledger";
require_once("acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");

$pag_limit_to=20;
if($_REQUEST['acc_pag']>1){
	$acc_pag_limit=($_REQUEST['acc_pag']-1)*$pag_limit_to;
}else{
	$acc_pag_limit=0;
}
$op_name_arr = preg_split("/, /",$_SESSION["authProviderName"]);
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];

//----------------- Patient Detail -----------------//
$qry = "select * from patient_data where pid = '$patient_id'";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$date_of_birth = get_date_format($row['DOB']);
$pat_age = show_age($row['DOB']);
$socialSecurity = $row['ss'];
$patient_name=ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));
$noBalanceBill = $row['noBalanceBill'];

$showAll = $_REQUEST['allCharges'];
$unPaid = $_REQUEST['unPaid'];
$cashChk = $_REQUEST['cashChk'];
$checkChk = $_REQUEST['checkChk'];
$cCardChk = $_REQUEST['cCardChk'];
$etfChk = $_REQUEST['etfChk'];
$amount = $_REQUEST['amount'];
$dosFrom = $_REQUEST['dosFrom'];
$dosTo = $_REQUEST['dosTo'];
$encounterId_srh = $_REQUEST['encounterId_srh'];
if($dosFrom){
	$dateOfServeFrom = getDateFormatDB($dosFrom);
	$dateOfServeTo = getDateFormatDB($dosTo);
}
if($_REQUEST['unPaid']){
	$unpaid_whr=" and a.totalBalance > 0";
}
if($dateOfServeFrom){
	$dat_serv_whr=" and a.date_of_service >= '$dateOfServeFrom'
				   and a.date_of_service <= '$dateOfServeTo'";
}
if(count($_REQUEST['grp_srh'])>0){
	$grp_srh_str=implode("','",$_REQUEST['grp_srh']);
	$grp_id_whr=" and gro_id in('$grp_srh_str')";
}
if($encounterId_srh>0){
	$enc_whr=" and a.encounter_id='$encounterId_srh'";
}
if($cashChk){
	$cash_whr=" and((c.payment_mode = 'Cash') AND (c.payment_amount = '$amount')";
}	
if($checkChk){
	if($cash_whr!=''){
		$set_cash_cond=" or ";
	}else{
		$set_cash_cond=" and(";
	}
	$check_whr="$set_cash_cond(c.payment_mode = 'Check') AND (c.checkNo = '$amount')";
}
if($cCardChk){	
	if($cash_whr!='' || $check_whr!=''){
		$set_check_cond=" or ";
	}else{
		$set_check_cond=" and(";
	}
	$credit_card_whr="$set_check_cond(c.payment_mode = 'Credit Card') AND (c.creditCardNo = '$amount')";
}	
if($etfChk){	
	if($cash_whr!='' || $check_whr!='' || $credit_card_whr!=''){
		$set_cc_cond=" or ";
	}else{
		$set_cc_cond=" and(";
	}
	$etf_whr="$set_cc_cond(c.payment_mode = 'ETF') AND (c.payment_amount = '$amount')";
}

if($cash_whr || $check_whr || $credit_card_whr || $etf_whr){
	if($encounterId_srh>0){
		$pay_enc_whr=" and c.encounter_id='$encounterId_srh'";
	}
	$checkbox_whr = $pay_enc_whr.$cash_whr.$check_whr.$credit_card_whr.$etf_whr.")";
	$payment_join=", patient_chargesheet_payment_info c";
	$payment_join_whr=" and a.encounter_id=c.encounter_id";
}

if($encounterId_srh=="all"){
	$active_enc_whr="";
	$active_chld_whr="";
}else{
	$active_enc_whr="a.del_status='0' and ";
	$active_chld_whr="a.del_status='0' and ";
	$active_chld_whr_b="b.del_status='0' and ";
}
//----------------- Encounters Detail -----------------//
	$getChargeDetailsStr = "SELECT DISTINCT 
		a.charge_list_id,a.gro_id,
		a.submitted,a.firstSubmitDate,
		a.encounter_id,a.overPayment,a.superbillFormId,
		a.copay,a.copayPaid,
		a.coPayNotRequired,a.coPayWriteOff,
		a.totalAmt,a.approvedTotalAmt,
		a.deductibleTotalAmt,a.creditAmount,
		a.amtPaid,a.totalBalance,
		a.case_type_id,a.primaryInsuranceCoId,
		a.secondaryInsuranceCoId,
		a.tertiaryInsuranceCoId,
		a.date_of_service,
		a.auth_no,a.insPaidAmt,
		a.primary_paid,a.secondary_paid,
		a.tertiary_paid,
		a.patientDue,a.insuranceDue,a.del_status,a.claim_status
	FROM 
		patient_charge_list as a
		$payment_join
	WHERE 
		a.patient_id='$patient_id'
		$payment_join_whr
		$checkbox_whr $grp_id_whr
		ORDER BY a.date_of_service DESC,a.charge_list_id DESC";

$getChargeDetailsQry = imw_query($getChargeDetailsStr);
$i=0;
while($row_chl_data=imw_fetch_array($getChargeDetailsQry)){
	
	if($_REQUEST['acc_pag']>1){
		$page_start_rec=(($_REQUEST['acc_pag']-1)*$pag_limit_to)+1;
		$page_end_rec = ($page_start_rec+$pag_limit_to)-1;
	}else{
		$page_start_rec=1;
		$page_end_rec = $pag_limit_to;
	}
	
	$show_enc_srh="";
	$show_dos_srh="";
	$show_unpaid_enc="";
	if($_REQUEST['encounterId_srh']=="all"){
		$show_enc_srh="yes";
	}else if($_REQUEST['encounterId_srh']>0){
		if($_REQUEST['encounterId_srh']==$row_chl_data['encounter_id']){
			$show_enc_srh="yes";
		}
	}else if($row_chl_data['del_status']=='0'){
		$show_enc_srh="yes";
	}
	if(($dosFrom!="" && $row_chl_data['date_of_service']>=$dateOfServeFrom && $row_chl_data['date_of_service']<=$dateOfServeTo) || $dosFrom==""){
		$show_dos_srh="yes";
	}
	if($_REQUEST['unPaid']){
		if($row_chl_data['totalBalance']>0){
			$show_unpaid_enc="yes";
		}
	}else{
		$show_unpaid_enc="yes";
	}
	if($show_enc_srh=="yes" && $show_dos_srh=="yes" && $show_unpaid_enc=="yes"){
		$i=$i+1;
		
		$final_sum_copay=$final_sum_copay+$row_chl_data['copay'];
		$final_sum_charges=$final_sum_charges+$row_chl_data['totalAmt'];
		$final_sum_allow=$final_sum_allow+$row_chl_data['approvedTotalAmt'];
		$final_sum_deduct=$final_sum_deduct+$row_chl_data['deductibleTotalAmt'];
		$sum_deposit=$sum_deposit+$totaldepositAmt;
		$sum_paid=$sum_paid+$amt_paid_arr[$l]['amtPaid'];
		
		
		if($row_chl_data['overPayment']>0){
			$final_sum_ovrpay=$final_sum_ovrpay+$row_chl_data['overPayment'];
		}else{
			$final_sum_balance=$final_sum_balance+$row_chl_data['totalBalance'];
		}
		
		$all_chl_arr[]=$row_chl_data['charge_list_id'];
		$chl_enc_arr[]=$row_chl_data['encounter_id'];
		
	}
	//echo $show_enc_srh."==yes && ".$show_dos_srh."==yes && ".$i.">=".$page_start_rec." && ".$i."<=".$page_end_rec.'<br>';
	if($show_enc_srh=="yes" && $show_dos_srh=="yes" && $show_unpaid_enc=="yes" && $i>=$page_start_rec && $i<=$page_end_rec){
		$chl_id_arr[]['charge_list_id']=$row_chl_data['charge_list_id'];
		$group_id_arr[]['gro_id']=$row_chl_data['gro_id'];
		$submitted_dat_arr[]['submitted']=$row_chl_data['submitted'];
		$ovr_pay_arr[]['overPayment']=$row_chl_data['overPayment'];
		$copay_amt_arr[]['copay']=$row_chl_data['copay'];
		$copay_paid_arr[]['copayPaid']=$row_chl_data['copayPaid'];
		$copay_not_req_arr[]['coPayNotRequired']=$row_chl_data['coPayNotRequired'];
		$copay_writeoff_arr[]['coPayWriteOff']=$row_chl_data['coPayWriteOff'];
		$total_amt_arr[]['totalAmt']=$row_chl_data['totalAmt'];
		$approve_amt_arr[]['approvedTotalAmt']=$row_chl_data['approvedTotalAmt'];
		$deduct_amt_arr[]['deductibleTotalAmt']=$row_chl_data['deductibleTotalAmt'];
		$credit_amt_arr[]['creditAmount']=$row_chl_data['creditAmount'];
		$amt_paid_arr[]['amtPaid']=$row_chl_data['amtPaid'];
		$total_balance_arr[]['totalBalance']=$row_chl_data['totalBalance'];
		$case_type_id_arr[]['case_type_id']=$row_chl_data['case_type_id'];
		$pri_ins_id_arr[]['primaryInsuranceCoId']=$row_chl_data['primaryInsuranceCoId'];
		$sec_ins_id_arr[]['secondaryInsuranceCoId']=$row_chl_data['secondaryInsuranceCoId'];
		$tri_ins_id_arr[]['tertiaryInsuranceCoId']=$row_chl_data['tertiaryInsuranceCoId'];
		$auth_no_arr[]['auth_no']=$row_chl_data['auth_no'];
		$insPaidAmt_arr[]['insPaidAmt']=$row_chl_data['insPaidAmt'];
		$priPaidStat_arr[]['primary_paid']=$row_chl_data['primary_paid'];
		$secPaidStat_arr[]['secondary_paid']=$row_chl_data['secondary_paid'];
		$terPaidStat_arr[]['tertiary_paid']=$row_chl_data['tertiary_paid'];
		$patientDue_arr[]['patientDue']=$row_chl_data['patientDue'];
		$insuranceDue_arr[]['insuranceDue']=$row_chl_data['insuranceDue'];
		$del_status_arr[]['del_status']=$row_chl_data['del_status'];
		$sb_id_arr[]['superbillFormId']=$row_chl_data['superbillFormId'];
		$ar_claim_status_arr[]['claim_status']=$row_chl_data['claim_status'];
		
		
		if($row_chl_data['primaryInsuranceCoId']){
			$ins_arr[]=$row_chl_data['primaryInsuranceCoId'];
			$ins_chl_arr[$row_chl_data['charge_list_id']]=$row_chl_data['primaryInsuranceCoId'];
			$enc_ins_comp_no[$row_chl_data['charge_list_id']][$row_chl_data['primaryInsuranceCoId']]=1;
		}
		if($row_chl_data['secondaryInsuranceCoId']){
			$ins_arr[]=$row_chl_data['secondaryInsuranceCoId'];
			$enc_ins_comp_no[$row_chl_data['charge_list_id']][$row_chl_data['secondaryInsuranceCoId']]=2;
		}
		if($row_chl_data['tertiaryInsuranceCoId']){
			$ins_arr[]=$row_chl_data['tertiaryInsuranceCoId'];
			$enc_ins_comp_no[$row_chl_data['charge_list_id']][$row_chl_data['tertiaryInsuranceCoId']]=3;
		}
		
		if($row_chl_data['firstSubmitDate']!='0000-00-00'){
			$posted_dat_arr[]['firstSubmitDate'] = get_date_format($row_chl_data['firstSubmitDate']);
		}else{
			$posted_dat_arr[]['firstSubmitDate'] = '-';
		}
		
		$getEncountersArr[]['encounter_id']=$row_chl_data['encounter_id'];
		$dateOfService = get_date_format($row_chl_data['date_of_service']);
		$dosArray[]['date_of_service']=$dateOfService;
		$chl_arr[]=$row_chl_data['charge_list_id'];
		$enc_arr[]=$row_chl_data['encounter_id'];
		$case_type_arr[]=$row_chl_data['case_type_id'];
		if($row_chl_data['gro_id']>0){
			$gro_id_arr[$row_chl_data['gro_id']]=$row_chl_data['gro_id'];
		}
	}
	
	if($row_chl_data['date_of_service']!='0000-00-00'){
		$dateOfService_srh = get_date_format($row_chl_data['date_of_service']);
	}else{
		$dateOfService_srh = '-';
	}
	
	$dos_arr[] = $dateOfService_srh;
	$srh_enc_arr[]=$row_chl_data['encounter_id'];
	$db_dos_arr[$row_chl_data['charge_list_id']]=$row_chl_data['date_of_service'];
	$enc_chl_arr[$row_chl_data['charge_list_id']]=$row_chl_data['encounter_id'];
}
$chld_cpt_self_arr=array();
if(count($chl_arr)>0){
	
	//----------------------- Get Group Detail -------------------//
	if(count($gro_id_arr)>0){
		$gro_id_imp=implode(',',$gro_id_arr);	
		$grp_qry=imw_query("select group_color,gro_id,name from groups_new where gro_id in($gro_id_imp)");
		while($grp_row=imw_fetch_array($grp_qry)){
			$group_name_arr[$grp_row['gro_id']]=ucfirst($grp_row['name']);
			$group_color_arr[$grp_row['gro_id']]=$grp_row['group_color'];
		}
	}
	
	$group_name_final=@implode(', ',$group_name_arr);	
	
	//------------------- INSURANCE COMPANY -------------------//
	if(count($ins_arr)>0){
		$ins_arr_imp=implode(',',array_unique($ins_arr));
		$qry=imw_query("select id,in_house_code,name,FeeTable from insurance_companies WHERE id in($ins_arr_imp)");
		while($row=imw_fetch_array($qry)){
			$ins_comp_detail[$row['id']]=$row;	
			$fee_table_column_arr[$row['id']]=$row['FeeTable'];
		}
	}
	
	//------------------- Get Policies detail -------- -----------//
	$get_pol=imw_query("SELECT billing_amount FROM copay_policies");
	$row_pol=imw_fetch_array($get_pol);
	$billing_amount=$row_pol['billing_amount'];
	
	//STATUS ARRAY
	$statusArr = array();
	$qry_claim_status =imw_query("SELECT id,status_name FROM claim_status where del_status='0' ORDER BY status_name ASC");
	while($fet_claim_status=imw_fetch_assoc($qry_claim_status)){
		$statusArr[$fet_claim_status['id']]=$fet_claim_status['status_name'];
	}
	
	$chl_arr_imp=implode(',',$all_chl_arr);
	$getProcedureDetailsStr = "SELECT a.*,b.not_covered,b.cpt_prac_code,b.cpt_desc,c.cpt_fee,c.fee_table_column_id 
							   FROM patient_charge_list_details a 
							   join cpt_fee_tbl b on a.procCode=b.cpt_fee_id
							   join cpt_fee_table c on c.cpt_fee_id=b.cpt_fee_id
							   WHERE $active_chld_whr charge_list_id in ($chl_arr_imp) order by a.charge_list_id,a.display_order,a.charge_list_detail_id";							
	$getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
	while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
		$chl_id_chk=$getProcedureDetailsRows['charge_list_id'];
		$chld_id_chk=$getProcedureDetailsRows['charge_list_detail_id'];
		
		$cptPracCode_arr[$chl_id_chk]['cpt_prac_code'][$chld_id_chk] = $getProcedureDetailsRows['cpt_prac_code'];
		$cptPracCode_arr[$chl_id_chk]['cpt_prac_code_desc'][$chld_id_chk] = $getProcedureDetailsRows['cpt_prac_code'].' - '.$getProcedureDetailsRows['cpt_desc'];
		$chld_cpt_arr[$chl_id_chk][$chld_id_chk] = $getProcedureDetailsRows;
		
		$final_tot_proc_pat_bal_amt_arr[$chl_id_chk][$chld_id_chk]=$getProcedureDetailsRows['pat_due'];
		$final_tot_proc_ins_bal_amt_arr[$chl_id_chk][$chld_id_chk]=$getProcedureDetailsRows['pri_due']+$getProcedureDetailsRows['sec_due']+$getProcedureDetailsRows['tri_due'];

		if($getProcedureDetailsRows['cpt_prac_code']=='92015' && $getProcedureDetailsRows['not_covered']=='1'){
			$chld_cpt_ref_arr[$chld_id_chk]=$getProcedureDetailsRows['approvedAmt'];
		}
		if($getProcedureDetailsRows['pat_due']>=$getProcedureDetailsRows['newBalance']){
		}else{
			$proc_base_flag[$chl_id_chk][$chld_id_chk]=$getProcedureDetailsRows['newBalance'];
		}
		$grand_tot_proc_pat_bal_amt_arr[$chld_id_chk]=$getProcedureDetailsRows['pat_due'];
		$grand_tot_proc_ins_bal_amt_arr[$chld_id_chk]=$getProcedureDetailsRows['pri_due']+$getProcedureDetailsRows['sec_due']+$getProcedureDetailsRows['tri_due'];
		
		if($billing_amount=='Default'){
			$chk_fee_table_column=$fee_table_column_arr[$ins_chl_arr[$chl_id_chk]];
			if($getProcedureDetailsRows['fee_table_column_id']==$chk_fee_table_column){
				$cpt_contract_price_arr[$getProcedureDetailsRows['charge_list_detail_id']]=$getProcedureDetailsRows['cpt_fee'];
			}
		}
		
		$chk_enc_era_arr[$getProcedureDetailsRows['patient_id']][$db_dos_arr[$chl_id_chk]][$getProcedureDetailsRows['cpt_prac_code']]=$enc_chl_arr[$chl_id_chk];
	}
}

//------------------- INSURANCE CASE -------- -----------//
if(count($case_type_arr)>0){
	$case_type_id_imp=implode(',',$case_type_arr);
	$getInsCaseInfoStr = "SELECT b.case_name,a.ins_caseid FROM 
							insurance_case a,
							insurance_case_types b
							WHERE a.ins_caseid in($case_type_id_imp)
							AND a.ins_case_type=b.case_id";
	$getInsCaseInfoQry = imw_query($getInsCaseInfoStr);
	while($getInsCaseInfoQryRow = imw_fetch_array($getInsCaseInfoQry)){
		$ins_caseid=$getInsCaseInfoQryRow['ins_caseid'];
		$ins_case_arr[$ins_caseid]['case_name'][] = $getInsCaseInfoQryRow['case_name'];
	}
	
}	

//----------------------- PAID BY -----------------------//
if(count($enc_arr)>0){
	$enc_arr_imp=implode(',',$chl_enc_arr);
	
	$crd_deb_data = "select * from creditapplied where (crAppliedToEncId_adjust in($enc_arr_imp) or crAppliedToEncId in($enc_arr_imp))
			and credit_applied = '1' and delete_credit ='0'";
	$crd_deb_data_qry = imw_query($crd_deb_data);
	while($crd_deb_data_row = imw_fetch_array($crd_deb_data_qry)){
		if($crd_deb_data_row['crAppliedToEncId']>0){
			$tot_enc_neg_paid_amt_arr[$crd_deb_data_row['crAppliedToEncId']][$crd_deb_data_row['insCompany']][]=$crd_deb_data_row['amountApplied'];
			$tot_chld_neg_paid_amt_arr[$crd_deb_data_row['charge_list_detail_id']][$crd_deb_data_row['insCompany']][]=$crd_deb_data_row['amountApplied'];
			$final_tot_neg_paid_amt_arr[$crd_deb_data_row['patient_id']][$crd_deb_data_row['insCompany']][]=$crd_deb_data_row['amountApplied'];
		}
		if($crd_deb_data_row['crAppliedToEncId_adjust']>0){
			$tot_enc_paid_amt_arr[$crd_deb_data_row['crAppliedToEncId_adjust']][$crd_deb_data_row['insCompany']][]=$crd_deb_data_row['amountApplied'];
			$tot_chld_paid_amt_arr[$crd_deb_data_row['charge_list_detail_id_adjust']][$crd_deb_data_row['insCompany']][]=$crd_deb_data_row['amountApplied'];
			$final_tot_paid_amt_arr[$crd_deb_data_row['patient_id_adjust']][$crd_deb_data_row['insCompany']][]=$crd_deb_data_row['amountApplied'];
		}
		
	}
	
	
	$getPaidByStr = "SELECT a.paid_by,a.insProviderId,
					 a.insCompany,b.overPayment,
					 a.paymentClaims,a.encounter_id,
					 b.paidForProc,b.charge_list_detail_id
					 FROM patient_chargesheet_payment_info as a,
					 patient_charges_detail_payment_info b
					 WHERE 
					 a.encounter_id in($enc_arr_imp)
					 AND a.payment_id = b.payment_id
					 AND b.deletePayment='0'";
	$getPaidByQry = imw_query($getPaidByStr);
	while($getPaidByRows = imw_fetch_array($getPaidByQry)){
		$enc_id=$getPaidByRows['encounter_id'];
		if($getPaidByRows['paymentClaims']=='Negative Payment'){
			$tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
			$tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
			
			$tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
			$tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
			
			$final_tot_neg_paid_amt_arr[$patient_id][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
			$final_tot_neg_paid_amt_arr[$patient_id][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
		}else{
			$tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
			$tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
			
			$tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
			$tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
			
			if($getPaidByRows['charge_list_detail_id']==0){
				$tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
				$tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
			}
			$final_tot_paid_amt_arr[$patient_id][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
			$final_tot_paid_amt_arr[$patient_id][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
		}
		if($getPaidByRows['paymentClaims']=='Deposit'){
			$paid_proc_arr[$enc_id]['paidForProc'][] = $getPaidByRows['paidForProc'];
			$deposit_proc_arr[$enc_id][$getPaidByRows['charge_list_detail_id']][] = $getPaidByRows['paidForProc'];
		}
		$chl_id=$getPaidByRows['charge_list_detail_id'];
		if($getPaidByRows['paid_by']=='Insurance'){
			//$insCoDetails = $objManageData->getRecords("insurance_companies", "id", $getPaidByRows['insProviderId']);
			 $paid_by = $ins_comp_detail[$getPaidByRows['insProviderId']]['in_house_code'];
			 if($paid_by==''){
				$paid_by = $ins_comp_detail[$getPaidByRows['insProviderId']]['name'];
			 }						 
			 if(strlen($paid_by)>10){
				$paid_by = substr($paid_by, 0, 8).'...';
			 }
			$paid_by_arr[$enc_id]['paid_by'][]=$paid_by; 
			$paid_by_proc_arr[$enc_id][$chl_id]['paid_by'][]=$paid_by; 
		}else{
			if($getPaidByRows['paid_by']=='Patient'){
				$paid_by_arr[$enc_id]['paid_by'][]=$getPaidByRows['paid_by']; 
				$paid_by_proc_arr[$enc_id][$chl_id]['paid_by'][]=$getPaidByRows['paid_by'];
			}
		}
	}
}

if(count($enc_arr)>0){
	$enc_arr_imp=implode(',',$chl_enc_arr);
	$write_off_amt_arr=array();
	$write_off_amt_enc_arr=array();
	$write_off_amt_enc_final_arr=array();
	$getWriteByStr = "SELECT encounter_id,charge_list_detail_id,write_off_amount
					 FROM paymentswriteoff
					 WHERE 
					 encounter_id in($enc_arr_imp)
					 AND delStatus='0'";
	$getWriteByqry = imw_query($getWriteByStr);
	while($getWriteByRows = imw_fetch_array($getWriteByqry)){
		$write_off_amt_arr[$getWriteByRows['encounter_id']][$getWriteByRows['charge_list_detail_id']][]=$getWriteByRows['write_off_amount'];
		$write_off_amt_enc_arr[$getWriteByRows['encounter_id']][]=$getWriteByRows['write_off_amount'];
		$write_off_amt_enc_final_arr[]=$getWriteByRows['write_off_amount'];
	}
	
	$getWriteByStr = "SELECT encounter_id,charge_list_detail_id,payment_amount,payment_type,ins_id,charge_list_id,patient_id	
					 FROM account_payments
					 WHERE 
					 encounter_id in($enc_arr_imp)
					 AND del_status='0'
					 and (payment_type='Adjustment' or payment_type='Over Adjustment' or payment_type='Returned Check')";
	$getWriteByqry = imw_query($getWriteByStr);
	while($getWriteByRows = imw_fetch_array($getWriteByqry)){
		if($getWriteByRows['payment_type']=='Adjustment' || $getWriteByRows['payment_type']=='Over Adjustment'){
			$write_off_amt_arr[$getWriteByRows['encounter_id']][$getWriteByRows['charge_list_detail_id']][]=$getWriteByRows['payment_amount'];
			$write_off_amt_enc_arr[$getWriteByRows['encounter_id']][]=$getWriteByRows['payment_amount'];
			$write_off_amt_enc_final_arr[]=$getWriteByRows['payment_amount'];
		}
		
		if($getWriteByRows['payment_type']=='Returned Check'){
			$ap_ins_id=0;
			if($getWriteByRows['ins_id']>0){
				$ap_ins_id=$enc_ins_comp_no[$getWriteByRows['charge_list_id']][$getWriteByRows['ins_id']];
			}
			$tot_enc_neg_paid_amt_arr[$getWriteByRows['encounter_id']][$ap_ins_id][]=$getWriteByRows['payment_amount'];
			$tot_chld_neg_paid_amt_arr[$getWriteByRows['charge_list_detail_id']][$ap_ins_id][]=$getWriteByRows['payment_amount'];
			$final_tot_neg_paid_amt_arr[$getWriteByRows['patient_id']][$ap_ins_id][]=$getWriteByRows['payment_amount'];
		}
	}

	$qry = imw_query("SELECT insd.provider,insd.type,insd.provider,insd.policy_number,inscomp.in_house_code,inscomp.City,inscomp.State,inscomp.Zip,
			inscomp.name,inscomp.contact_address,inscomp.phone,insc.ins_caseid
			FROM insurance_data insd 
			JOIN insurance_case insc ON insc.ins_caseid = insd.ins_caseid 
			JOIN insurance_companies inscomp ON inscomp.id = insd.provider
			LEFT JOIN insurance_case_types insct ON insct.case_id = insc.ins_case_type 
			WHERE insc.patient_id='$patient_id' AND 
			insc.case_status='Open' AND insd.provider>0 
			AND inscomp.in_house_code != 'n/a'
			and (insd.type='primary' or insd.type='secondary' or insd.type='tertiary')
			ORDER BY insd.actInsComp desc,insct.normal desc, insc.ins_caseid");
	$pri_ins_name = array();
	$sec_ins_name = array();
	$ter_ins_name = array();
	$ins_tooltip_data=array();
	$ins_comp_case_id=0;
	while($caseRes = imw_fetch_array($qry)){
		if($ins_comp_case_id==0 || $ins_comp_case_id==$caseRes['ins_caseid']){	
			if($caseRes['type']=="primary" && $caseRes['in_house_code']!=""){
				$pri_ins_name[] = $caseRes['in_house_code'];
				$pri_ins_id[] = $caseRes['provider'];
				$ins_tooltip_data[1][]=show_ins_tooltip($caseRes);
				$ins_comp_case_id=$caseRes['ins_caseid'];
			}
			if($caseRes['type']=="secondary" && $caseRes['in_house_code']!=""){
				$sec_ins_name[] = $caseRes['in_house_code'];
				$sec_ins_id[] = $caseRes['provider'];
				$ins_tooltip_data[2][]=show_ins_tooltip($caseRes);
			}
			if($caseRes['type']=="tertiary" && $caseRes['in_house_code']!=""){
				$ter_ins_name[] = $caseRes['in_house_code'];
				$ter_ins_id[] = $caseRes['provider'];
				$ins_tooltip_data[3][]=show_ins_tooltip($caseRes);
			}
		}
	}
	if($pri_ins_name[0]==""){
		$pri_ins_name[0]="-";
	}
	if($sec_ins_name[0]==""){
		$sec_ins_name[0]="-";
	}
	if($ter_ins_name[0]==""){
		$ter_ins_name[0]="-";
	}

	$add_zero_pat_id='000000'.$patient_id;
	$sel_era=imw_query("select era_835_patient_details.835_Era_Id,era_835_proc_details.REF_prov_identifier,era_835_proc_details.DTM_date,
							electronicfiles_tbl.file_name,era_835_proc_details.SVC_proc_code,era_835_proc_details.SVC_mod_code,
							electronicfiles_tbl.id,electronicfiles_tbl.post_status,era_835_proc_details.ERA_patient_details_id,
							era_835_details.TRN_payment_type_number,date_format(era_835_details.chk_issue_EFT_Effective_date,'%m-%d-%y') as chkEffectiveDate
							from era_835_patient_details join 
							era_835_proc_details on  era_835_patient_details.ERA_patient_details_id = era_835_proc_details.ERA_patient_details_id
							join era_835_details on era_835_details.835_Era_Id = era_835_patient_details.835_Era_Id
							join electronicfiles_tbl on electronicfiles_tbl.id=era_835_details.electronicFilesTblId
							where CLP_claim_submitter_id ='$patient_id' or CLP_claim_submitter_id ='$add_zero_pat_id'");
	while($era_row=imw_fetch_array($sel_era)){
		$CLP_claim_submitter_id=$patient_id;
		$REF_prov_identifier=$era_row['REF_prov_identifier'];
		$DOS = $era_row['DTM_date'];
		$file_name = $era_row['file_name'];
		$SVC_proc_code = $era_row['SVC_proc_code'];
		$SVC_mod_code = $era_row['SVC_mod_code'];
		$mcrPos = strpos($REF_prov_identifier, 'MCR');
		if($mcrPos2){
			// REF*6R EXISTS
			$encounter_id = trim(substr($REF_prov_identifier, 0, $mcrPos));
			$restStr = substr($REF_prov_identifier, $mcrPos+3);
			if(strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator)){
				$tsucPos = strpos($restStr, $billing_global_tsuc_separator.'TSUC'.$billing_global_tsuc_separator);
				$tsucId = $tsucPos+6;
			}else if(strpos($restStr, '_TSUC_')){
				$tsucPos = strpos($restStr, '_TSUC_');
				$tsucId = $tsucPos+6;
			}else if(strpos($REF_prov_identifier, 'TSUC')){
				$tsucPos = strpos($restStr, 'TSUC');
				$tsucId = $tsucPos+4;
			}
			if($tsucId){
				$chargeListDetailId = substr($restStr, 0, $tsucPos);
				$tsuc_identifier = substr($restStr, $tsucId);					
				if(strpos($tsuc_identifier, ',')){
					$tsuc_identifier = trim(substr($tsuc_identifier, 0, strpos($tsuc_identifier, ',')));
				}
			}else{
				$chargeListDetailId = '';
			}
		}else{
			// REF*6R DOES NOT EXISTS
			$encounter_id = '';
			$chargeListDetailId = '';
			$CLP_claim_submitter_id=intval($CLP_claim_submitter_id);
			// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
			if(is_numeric($CLP_claim_submitter_id)){
				$encounter_id=$chk_enc_era_arr[$CLP_claim_submitter_id][$DOS][$SVC_proc_code];
			}
			// GET ENCOUNTER AND CHARGE LIST DETAILS BASED ON PATIENT ID
		}
		
		$era_enc_arr[$encounter_id][$file_name]=$era_row;
		$era_id_arr[$era_row['id']]=$era_row['id'];
	}
	
	$getScanStr = "SELECT upload_lab_rad_data_id,uplaod_primary_id FROM upload_lab_rad_data
					 WHERE uplaod_primary_id in($enc_arr_imp) AND scan_from='accounting' and patient_id='$patient_id'";
	$getScanQry = imw_query($getScanStr);
	while($getScanRows = imw_fetch_array($getScanQry)){
		$scan_file_arr[$getScanRows['uplaod_primary_id']]=$getScanRows['uplaod_primary_id'];
	}
}
if(count($enc_arr)>0){
	$enc_imp=implode(',',$enc_arr);	
	$getPostedDateStr = "SELECT posted_date,encounter_id FROM posted_record WHERE encounter_id in($enc_imp) ORDER BY id DESC";
	$getPostedDateQry = imw_query($getPostedDateStr);
	if(imw_num_rows($getPostedDateQry)>0){
		while($getPostedDaterow = imw_fetch_array($getPostedDateQry)){
			$enc_posted_arr[$getPostedDaterow['encounter_id']][]=$getPostedDaterow['posted_date'];
		}
	}
	
	$getSubmittedDateStr = "SELECT submited_date,encounter_id FROM submited_record WHERE encounter_id in($enc_imp) ORDER BY submited_id DESC";
	$getSubmittedDateQry = imw_query($getSubmittedDateStr);
	if(imw_num_rows($getSubmittedDateQry)>0){
		while($getSubmittedDateRow = imw_fetch_array($getSubmittedDateQry)){
			$enc_submitted_arr[$getSubmittedDateRow['encounter_id']][]=$getSubmittedDateRow['submited_date'];
		}
	}
} 

?>
<div class="purple_bar  <?php echo $review_chrg_srh; ?>">
	<form name="reviewChargeFrm" action="review_payments.php" method="post" style="margin:0px;">
		<input type="hidden" name="proc_details" id="proc_details" value="">
		<input type="hidden" id="auth_id" name="auth_id" value="<?php echo $_SESSION["authId"];?>">
        <table width="100%">
    	<tr>
        	<td>
              	<div class="checkbox checkbox-inline">
                   	<input name="allCharges" id="allCharges" <?php if((!$_POST) || ($_REQUEST['allCharges'])) echo "CHECKED"; ?> type="checkbox" onClick="return doWhat();"/>
                    <label for="allCharges">All Charges</label>
                </div>
            </td>
            <td>
              	 <div class="checkbox checkbox-inline">
                    <input name="unPaid" id="unPaid" <?php if($_REQUEST['unPaid']) echo "CHECKED"; ?> type="checkbox" onClick="return unPaidShow();"/>
                    <label for="unPaid">Unpaid Charges</label>  
                </div>
            </td>
            <td>
            	<span>Group:</span>
                <select name="grp_srh[]" id="grp_srh" class="selectpicker" data-width="200" multiple data-actions-box="true" data-title="Select Group" data-size="10">
					 <?php
						foreach($group_name_arr as $g_key => $g_id){
							if(in_array($g_key,$_REQUEST['grp_srh'])){
								$sel = 'selected="selected"';
							}else{
								$sel = '';
							}
					?>
							<option value="<?php echo $g_key; ?>" <?php echo $sel; ?>><?php echo $group_name_arr[$g_key]; ?></option>
							<?php
						}
                    ?>
                </select>
            </td>
            <td>
            	<span>DOS From:</span>
                <select name="dosFrom" onChange="return uncheckAll();" class="selectpicker" data-width="auto" data-size="10">
                    <option value="">All</option>
                    <?php
                        $dos=array_values(array_unique($dos_arr));
                        for($i=0;$i<count($dos);$i++){
                    ?>
                            <option value="<?php echo $dos[$i]; ?>" <?php if($dosFrom==$dos[$i]) echo "SELECTED"; ?>><?php echo $dos[$i]; ?></option>
                    <?php
                        }
                    ?>
                </select>
            </td>
            <td>
            	<span>DOS To:</span>
                <select name="dosTo" class="selectpicker" data-width="auto" data-size="10">
                    <option value="">All</option>
                    <?php
                        for($j=0;$j<count($dos);$j++){
                    ?>
                            <option value="<?php echo $dos[$j]; ?>" <?php if($dosTo==$dos[$j]) echo "SELECTED"; ?>><?php echo $dos[$j]; ?></option>
                    <?php
                        }
                    ?>
                </select>
            </td>
            <td>
            	<span>E Id:</span>
                <select name="encounterId_srh" id="encounterId_srh" onChange="unselectEvery();" class="selectpicker" data-width="auto" data-size="10">
                    <option value="all" <?php if($encounterId_srh=="all") echo "SELECTED"; ?>>All</option>
                    <option value="active" <?php if($encounterId_srh=="active" || $encounterId_srh=="") echo "SELECTED"; ?>>Active</option>
                    <?php
                        for($k=0;$k<count($srh_enc_arr);$k++){
                    ?>
                            <option value="<?php echo $srh_enc_arr[$k]; ?>" <?php if($encounterId_srh==$srh_enc_arr[$k]) echo "SELECTED"; ?>><?php echo $srh_enc_arr[$k]; ?></option>
                    <?php
                        }
                    ?>
                </select>	
            </td>
            <td>
            	<div class="checkbox checkbox-inline">
                	<input name="cashChk" id="cashChk" <?php if($_REQUEST['cashChk']) echo "CHECKED"; ?> type="checkbox"/>
                    <label for="cashChk">Cash</label>
                </div>	
                <div class="checkbox checkbox-inline">
                	<input name="checkChk" id="checkChk" <?php if($_REQUEST['checkChk']) echo "CHECKED"; ?> type="checkbox"/>
					<label for="checkChk">Ch <?php getHashOrNo();?></label>
                </div>
                <div class="checkbox checkbox-inline">
                	<input name="cCardChk" id="cCardChk" <?php if($_REQUEST['cCardChk']) echo "CHECKED"; ?> type="checkbox"/>
					<label for="cCardChk">CC <?php getHashOrNo();?></label>
                </div>
                <div class="checkbox checkbox-inline">
                	<input name="etfChk" id="etfChk" <?php if($_REQUEST['etfChk']) echo "CHECKED"; ?> type="checkbox"/>
					<label for="etfChk">EFT <?php getHashOrNo();?></label>
                </div>
            </td>
            <td>
            	<input name="amount" type="text" value="<?php echo $amount; ?>" class="form-control" style="width:70%;">
            </td>
            <td>
            	<span>Page <?php getHashOrNo();?>:</span>
				<?php
                 $pag_qry = imw_query("SELECT charge_list_id FROM patient_charge_list as a WHERE $active_enc_whr a.patient_id='$patient_id' $enc_whr $unpaid_whr $dat_serv_whr $checkbox_whr $grp_id_whr ORDER BY a.date_of_service DESC");
                 $pag_divide=ceil(imw_num_rows($pag_qry)/$pag_limit_to);
                ?>
                <select name="acc_pag" onChange="document.reviewChargeFrm.submit();"  class="selectpicker" data-width="50%" data-size="10">
                    <?php for($n=1;$n<=$pag_divide;$n++){?>
                    <option value="<?php echo $n; ?>" <?php if($_REQUEST['acc_pag']==$n){ echo "selected";} ?>><?php echo $n; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </table>
	</form>
</div>
<div class="clearfix"></div>		
<div class="table-responsive" id="rev_pay_inner_div" style="margin:0px; height:<?php echo $_SESSION['wn_height']-355;?>px; overflow-x:auto; width:100%;">
		<?php
		$StrHtml='
		<table class="table table-bordered">
			<tr class="grythead">
				<th rowspan="2" onClick="show_multi_proc();" class="text_purple" style="cursor:pointer;">S.No.</th>
				<th rowspan="2">DOS</th>
				<th rowspan="2" colspan="2">E. Id</th>
				<th rowspan="2">Ins. Case</th>
				<th rowspan="2">CPT</th>
				<th rowspan="2">CoPay</th>
				<th>Charges</th>
				<th colspan="7">Payment Details</th>
				<th rowspan="2">Pt Balance</th>
				<th rowspan="2">Ins. Balance</th>
				<th rowspan="2">New Balance</th>
				<th colspan="2"> Date </th>
				<th rowspan="2">AR Status</th>
			</tr>
			<tr class="grythead">
				<th>T. Charges</th>
				<th>Allowed</th>
				<th>Deductible </th>
				<th '.$ins_tooltip_data[1][0].' data-container="body">'.$pri_ins_name[0].'</th>
				<th '.$ins_tooltip_data[2][0].' data-container="body">'.$sec_ins_name[0].'</th>
				<th '.$ins_tooltip_data[3][0].' data-container="body">'.$ter_ins_name[0].'</th>
				<th>Pt Paid</th>
				<th>Adj</th>
				<th>Posted</th>
				<th>Submitted</th>
			</tr>';
			$pri_insurance_nm_pdf = $sec_insurance_nm_pdf = $ter_insurance_nm_pdf = '';
			$pri_insurance_nm_pdf = $pri_ins_name[0];
			$sec_insurance_nm_pdf = $sec_ins_name[0];
			$ter_insurance_nm_pdf = $ter_ins_name[0];
			
			if($pri_ins_name[0] != '-' && empty($pri_ins_name[0]) === false && strlen($pri_ins_name[0]) > 7){
				$pri_insurance_nm_pdf = substr($pri_ins_name[0],0,7);
			}
			
			if($sec_ins_name[0] != '-' && empty($sec_ins_name[0]) === false && strlen($sec_ins_name[0]) > 7){
				$sec_insurance_nm_pdf = substr($sec_ins_name[0],0,7);
			}
			
			if($ter_ins_name[0] != '-' && empty($ter_ins_name[0]) === false && strlen($ter_ins_name[0]) > 7){
				$ter_insurance_nm_pdf = substr($ter_ins_name[0],0,7);
			}
			if($socialSecurity!=""){
				$socialSecurity=substr_replace($socialSecurity,'XXX-XX',0,6);
			}
			$PdfHtml='
		<table class="table_separate"  style="background:#FFF3E8;">
			<tr  style="height:25px; background:#4684ab;" class="text_b_w">
				<td class="text_b_w" colspan="5">&nbsp;Patient Payment Summary</td>
				<td class="text_b_w" colspan="7" style="text-align:center;">'.$patient_name.' - '.$patient_id.'</td>
				<td class="text_b_w" colspan="3">&nbsp;DOB : '.$date_of_birth.' ( '.$pat_age.' )</td>
				<td class="text_b_w" colspan="4">&nbsp;SS# : '.$socialSecurity.'</td>
			</tr>
			<tr  style="height:25px; background:#4684ab;" class="text_b_w">
				<td class="text_b_w" colspan="15">&nbsp;Group Name : '.$group_name_final.'</td>
				<td class="text_b_w" colspan="4">&nbsp;Created by : '.strtoupper($op_name).' on '. date("m-d-y h:i A") .'</td>
			</tr>
			<tr  style="height:20px; background:#4684ab;" class="text_b_w">
				<td rowspan="2" style="text-align:center;" class="text_b_w">S.No.</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">DOS</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">E. Id</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">Ins. Case</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">CPT</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">CoPay</td>
				<td  style="text-align:center;" class="text_b_w">Charges</td>
				<td colspan="7" style="text-align:center;" class="text_b_w">Payment Details</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">Pt <br>Balance</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">Ins. <br>Balance</td>
				<td rowspan="2" style="text-align:center;" class="text_b_w">New <br>Balance</td>
				<td colspan="2" style="text-align:center;" class="text_b_w"> Date </td>
			</tr>
			<tr class="text_b" style="height:20px; background:#4684ab;" >
				<td style="text-align:center;" class="text_b_w">T.Charges</td>
				<td style="text-align:center;" class="text_b_w">Allowed</td>
				<td style="text-align:center;" class="text_b_w">Deduct</td>
				<td style="text-align:center;" class="text_b_w">'.$pri_insurance_nm_pdf.'</td>
				<td style="text-align:center;" class="text_b_w">'.$sec_insurance_nm_pdf.'</td>
				<td style="text-align:center;" class="text_b_w">'.$ter_insurance_nm_pdf.'</td>
				<td style="text-align:center;" class="text_b_w">Pt Paid</td>
				<td style="text-align:center;" class="text_b_w">Adj</td>
				<td style="text-align:center;" class="text_b_w">Posted</td>
				<td style="text-align:center;" class="text_b_w">Submitted</td>
			</tr>';
			if(count($getEncountersArr)<=0){
				$StrHtml .='<tr>
					<td colspan="21" class="text-center lead">'.imw_msg('no_rec').'</td>
				</tr>';
				$PdfHtml .='<tr style="background:#ffffff;">
					<td colspan="21" class="text-center lead">'.imw_msg('no_rec').'</td>
				</tr>';
			}else{
				$sum_copay=0;
				$sum_charges=0;
				$sum_allow=0;
				$sum_deduct=0;
				$sum_deposit=0;
				$sum_paid=0;
				$sum_balance=0;
				for($l=0;$l<count($getEncountersArr);$l++){
					$charge_list_id = $chl_id_arr[$l]['charge_list_id'];
					$gro_id = $group_id_arr[$l]['gro_id'];
					$submitted = $submitted_dat_arr[$l]['submitted'];
					$postedDate = $posted_dat_arr[$l]['firstSubmitDate'];
					if($submitted=='true'){
						$postedDate=$postedDate;
						$posted_opt_false="";
					}else{
						$postedDate="-";
						$posted_opt_false="<option>-</option>";
					}
					$encounter_id = $getEncountersArr[$l]['encounter_id'];
					$overPaymentTotal = $ovr_pay_arr[$l]['overPayment'];
					$copay = $copay_amt_arr[$l]['copay'];					
					$copayPaid = $copay_paid_arr[$l]['copayPaid'];
					$coPayNotRequired = $copay_not_req_arr[$l]['coPayNotRequired'];
					$coPayWriteOff =  $copay_writeoff_arr[$l]['coPayWriteOff'];
					$totalAmt = $total_amt_arr[$l]['totalAmt'];
					$approvedTotalAmt = $approve_amt_arr[$l]['approvedTotalAmt'];
					$deductibleTotalAmt = $deduct_amt_arr[$l]['deductibleTotalAmt'];
					$amtCredit = $credit_amt_arr[$l]['creditAmount'];
					$amtPaid = $amt_paid_arr[$l]['amtPaid'];
					$newBal = $total_balance_arr[$l]['totalBalance'];
					$case_type_id = $case_type_id_arr[$l]['case_type_id'];
					$primaryInsuranceCoId = $pri_ins_id_arr[$l]['primaryInsuranceCoId'];
					$secondaryInsuranceCoId = $sec_ins_id_arr[$l]['secondaryInsuranceCoId'];
					$tertiaryInsuranceCoId = $tri_ins_id_arr[$l]['tertiaryInsuranceCoId'];
					$date_of_service=$dosArray[$l]['date_of_service'];
					$superbillFormId = $sb_id_arr[$l]['superbillFormId'];
					$ar_claim_status_id = $ar_claim_status_arr[$l]['claim_status'];
					if($auth_no_arr[$l]['auth_no']){
						$auth_no_final=$auth_no_arr[$l]['auth_no'];
					}else{
						$auth_no_final='-';
					}
					$multi_ins_name="";	
					$multi_ins_id_arr=array();
					if($case_type_id>0 && ($primaryInsuranceCoId>0 || $secondaryInsuranceCoId>0 || $tertiaryInsuranceCoId>0)){
						$insCaseTypeNameId = $ins_case_arr[$case_type_id]['case_name'][0];
						if($primaryInsuranceCoId>0){
							 //$insCoDetails = $objManageData->getRecords("insurance_companies", "id", $primaryInsuranceCoId);
							 $ins_name_chk = $ins_comp_detail[$primaryInsuranceCoId]['in_house_code'];
							 if($ins_name_chk==''){
								$ins_name_chk = $ins_comp_detail[$primaryInsuranceCoId]['name'];
							 }
							 $multi_ins_id_arr[]=$ins_name_chk;
						}
						if($secondaryInsuranceCoId>0){
							 //$insCoDetails = $objManageData->getRecords("insurance_companies", "id", $secondaryInsuranceCoId);
							 $ins_name_chk = $ins_comp_detail[$secondaryInsuranceCoId]['in_house_code'];
							 if($ins_name_chk==''){
								$ins_name_chk = $ins_comp_detail[$secondaryInsuranceCoId]['name'];
							 }
							 $multi_ins_id_arr[]=$ins_name_chk;
						}
						if($tertiaryInsuranceCoId>0){
							 //$insCoDetails = $objManageData->getRecords("insurance_companies", "id", $tertiaryInsuranceCoId);
							$ins_name_chk = $ins_comp_detail[$tertiaryInsuranceCoId]['in_house_code'];
							if($ins_name_chk==''){
								$ins_name_chk = $ins_comp_detail[$tertiaryInsuranceCoId]['name'];
							}
							$multi_ins_id_arr[]=$ins_name_chk;
						}
					}else{
						$insCaseTypeNameId = 'Self Pay';
					}	
					$multi_ins_name = implode('<br>',$multi_ins_id_arr);
					$multi_cpt="";
					//----------------------- PROCEDURE DETAILS -----------------------//
					if(count($cptPracCode_arr[$charge_list_id]['cpt_prac_code'])==1){
						foreach($cptPracCode_arr[$charge_list_id]['cpt_prac_code'] as $cpt_key=>$cpt_val){
							$cptPracCode=$cpt_val;
						}
					}		
					if(count($cptPracCode_arr[$charge_list_id]['cpt_prac_code'])>1){
						$cptPracCode='Multi';
						$multi_cpt=implode('<br>',$cptPracCode_arr[$charge_list_id]['cpt_prac_code_desc']);
					}else{
						$multi_cpt=implode('<br>',$cptPracCode_arr[$charge_list_id]['cpt_prac_code_desc']);
					}		
					if($cptPracCode==""){
						$cptPracCode='-';
					}
					
					//----------------------- PAID BY -----------------------//
					$paid_by = "";
					$paid_by_final="";
					$totaldepositAmt=0;
					
					if($paid_proc_arr[$encounter_id]['paidForProc']){
						$totaldepositAmt=array_sum($paid_proc_arr[$encounter_id]['paidForProc']);
					}else{
						$totaldepositAmt=0;
					}
					$paid_by_arr_unique="";
					if($paid_by_arr[$encounter_id]['paid_by']){
						$paid_by_arr_unique=array_values(array_unique($paid_by_arr[$encounter_id]['paid_by']));
					}
					if(count($paid_by_arr_unique)==1){
						$paid_by = $paid_by_arr[$encounter_id]['paid_by'][0];
					}
					$multi_paid_by="";
					if(count($paid_by_arr_unique)>1){
						$paid_by = "Multi";
						$multi_paid_by=implode('<br>',$paid_by_arr_unique);
					}
					
					if($paid_by == ''){
						$paid_by_final = '-';
					}else if($paid_by=='Multi'){
						$paid_by_final="Multi";
					}else if($paid_by=='Patient'){
						$paid_by_final="Patient";
					}else{
						if(count($paid_by_arr_unique)==1){
							$paid_by_final=$paid_by;
						}
					}
					++$seq;
					
						//$groupDetail = $objManageData->getRecords('groups_new','gro_id',$gro_id);
						$group_color=$group_color_arr[$gro_id];
						if($group_color){
							if($group_color=='#FFFFFF'){
								$g_color="#ffffff";
							}else{
								$g_color=$group_color;
							}
						}else{
							$g_color="#ffffff";
						}
						$sum_copay=$sum_copay+$copay_amt_arr[$l]['copay'];
						$sum_charges=$sum_charges+$total_amt_arr[$l]['totalAmt'];
						$sum_allow=$sum_allow+$approve_amt_arr[$l]['approvedTotalAmt'];
						$sum_deduct=$sum_deduct+$deduct_amt_arr[$l]['deductibleTotalAmt'];
						$sum_deposit=$sum_deposit+$totaldepositAmt;
						$sum_paid=$sum_paid+$amt_paid_arr[$l]['amtPaid'];
						$final_tot_pat_adj_amt_arr=$final_tot_pat_adj_amt_arr+array_sum($write_off_amt_enc_arr[$encounter_id]);
						
						if($ovr_pay_arr[$l]['overPayment']>0){
							$sum_ovrpay=$sum_ovrpay+$ovr_pay_arr[$l]['overPayment'];
						}else{
							$sum_balance=$sum_balance+$total_balance_arr[$l]['totalBalance'];
						}
						
						
						$contract_fee=0;
						$pri_contract_flag=0;
						$pri_ins_paid=0;
						$contract_flag=$sb_flag=$scan_flag="";
						if($primaryInsuranceCoId>0 && $secondaryInsuranceCoId==0){
							foreach($cptPracCode_arr[$charge_list_id]['cpt_prac_code'] as $cpt_key=>$cpt_val){
								$contract_fee=$contract_fee+$cpt_contract_price_arr[$cpt_key];
							}
							$pri_ins_paid=$insPaidAmt_arr[$l]['insPaidAmt'];
							if($pri_ins_paid>0 && $contract_fee>$pri_ins_paid){
								$pri_contract_flag=1;
							}
						}
						$icon_wd=7;
						if($pri_contract_flag>0){
							//$g_color="#F2D89B";
							$contract_flag='<img src="../../library/images/flag_orange_n.gif" style="width:14px; height:16px;">';
							$icon_wd=$icon_wd+14;
						}
						if($superbillFormId>0){
							$sb_flag='<img src="../../library/images/sb.png" '.show_tooltip("Superbill").' style="width:16px; height:16px;">';
							$icon_wd=$icon_wd+16;
						}
						if($scan_file_arr[$encounter_id]>0){
							$scan_flag='<img src="../../library/images/scanDcs_active.png" style="width:25px; height:24px;" class="pointer" onClick="top.popup_win(\''.$GLOBALS['php_server'].'/interface/billing/scan/view_batch_images.php?upload_from=accounting&lab_id='.$encounter_id.'\',\'resizable=yes\')" >';
							$icon_wd=$icon_wd+25;
						}else{
							$scan_flag='<img src="../../library/images/scanDcs_deactive.png" style="width:25px; height:24px;" class="pointer" onClick="top.popup_win(\''.$GLOBALS['php_server'].'/interface/billing/scan/view_batch_images.php?upload_from=accounting&lab_id='.$encounter_id.'\',\'resizable=yes\')" >';
							$icon_wd=$icon_wd+25;
						}
						$chld_id_show_arr=array();
						foreach($chld_cpt_arr[$charge_list_id] as $cpt_key=>$cpt_val){	
							$chld_id_show_arr[]=$chld_cpt_arr[$charge_list_id][$cpt_key]['charge_list_detail_id'];
						}
						$chld_id_show_str=implode(',',$chld_id_show_arr);
						
						$del_charge_list_id="0";
						$f_class="";
						if($del_status_arr[$l]['del_status']>0){
							$del_charge_list_id=$charge_list_id;
							$f_class="text-danger";
						}
					$show_tooltip_ins=show_tooltip($multi_ins_name);
					$StrHtml .='
					<tr class="text-center">
						<td '.show_gro_color($group_color).'>
							<form name="editPatientChargeFrm'.$encounter_id.'_'.$del_charge_list_id.'" id="editPatientChargeFrm'.$encounter_id.'_'.$del_charge_list_id.'" method="post" target="_parent" action="makePayment.php">
								<input name="encounter_id" type="hidden" value="'.$encounter_id.'">
								<input name="del_charge_list_id" type="hidden" value="'.$del_charge_list_id.'">
							</form>     
							<a href="javascript:show_enc_proc(\''.$chld_id_show_str.'\',\''.$encounter_id.'\');" class="text_purple">
								'.$seq.'
							</a>
						</td>
						<td>
							<a class="'.$f_class.' text-nowrap" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
								'.$date_of_service.'
							</a>
						</td>
						<td style="border-right-color:white;">
							<a class="'.$f_class.' text-nowrap" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
								'.$encounter_id.'
							</a>
						</td>
						<td>
							<span class="pull-right" style="width:'.$icon_wd.'px;">
								'.$contract_flag.'
								'.$sb_flag.'
								'.$scan_flag.'
							</span>
						</td>
						<td>
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');" '.$show_tooltip_ins.'>						
								'.$insCaseTypeNameId.'
							</a>
						</td>';
					if($seq>1){	
						$PdfHtml .='<tr><td colspan="19" style="text-align:center;" class="text_10b">&nbsp;</td></tr>';
					}
					$PdfHtml .='
					<tr style="background-color:'.$g_color.'; height:18px;">
						<td style="text-align:center;'.$del_style.'" class="text_10b">
								'.$seq.'
						</td>
						<td style="text-align:center; white-space:nowrap;'.$del_style.'" class="text_10b">
								'.$date_of_service.'
						</td>
						<td style="text-align:right; white-space:nowrap;'.$del_style.'" class="text_10b">
								'.$encounter_id.'
						</td>
						<td style="text-align:center;'.$del_style.'" class="text_10b">
								'.$insCaseTypeNameId.'
						</td>';
						$show_tooltip_cpt=show_tooltip($multi_cpt);
						 $StrHtml .='<td class="text-left">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');" '.$show_tooltip_cpt.'>						
								&nbsp;'.$cptPracCode.'
							</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">';	
						$PdfHtml .=' <td style="width:88px; text-align:left;'.$del_style.'" class="text_10b">
								&nbsp;'.$cptPracCode.'
						</td>
						<td style="text-align:right;'.$del_style.'"  class="text_10b">';							
								if($coPayNotRequired == 0){
									if($coPayWriteOff!='1'){
										if(!empty($copayPaid) || ($copay<=0)){
											$StrHtml .="<span style=\"color:Green\">";
											$PdfHtml .="<span style=\"color:Green\">";
										}else{
											$StrHtml .="<span style=\"color:red\">";
											$PdfHtml .="<span style=\"color:red\">";
										}
										$StrHtml .= numberFormat($copay,2);
										$StrHtml .= "</span>";
										$PdfHtml .= numberFormat($copay,2);
										$PdfHtml .= "</span>";
									}else{
									$StrHtml .='<span style="color:#FF0000;">'.numberFormat($copay,2).'</span>
									<span style="font-family:Arial; font-size:12px; font-weight:bold; color:#0000FF;">W O</span>';
									$PdfHtml .='<span style="color:#FF0000;">'.numberFormat($copay,2).' W O</span>';
									}
								}else{
									$StrHtml .='<span style="color:#FF0000;">'.numberFormat($copay,2).'</span>
									<span style="font-family:Arial; font-size:12px; font-weight:bold; color:#0000FF;">NR</span>';
									$PdfHtml .='<span style="color:#FF0000;">'.numberFormat($copay,2).' NR</span>';
								}
							$StrHtml .='</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								'.numberFormat($totalAmt,2).'
							</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								'.numberFormat($approvedTotalAmt,2).'
							</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								<span style="color:#FFC000;font-weight:bold;">
									'.numberFormat($deductibleTotalAmt,2).'
								</span>
							</a>
						</td>
						';
						$PdfHtml .='
						</td>
						<td style="text-align:right;'.$del_style.'"  class="text_10b">
								'.numberFormat($totalAmt,2).'
						</td>
						<td style="text-align:right;'.$del_style.'"  class="text_10b">
								'.numberFormat($approvedTotalAmt,2).'
						</td>
						<td style="text-align:right;'.$del_style.'"  class="text_10b">
								<span style="color:#FFC000;font-weight:bold;">
									'.numberFormat($deductibleTotalAmt,2).'
								</span>
						</td>';
							$pri_bg_paid_col="";
							$sec_bg_paid_col="";
							$ter_bg_paid_col="";
							$pri_bor_paid_col="";
							$sec_bor_paid_col="";
							$ter_bor_paid_col="";
							$pat_bor_paid_col="";
							$pat_heard_ins="false";
							if($primaryInsuranceCoId==0){
								$pri_bg_paid_col="background:#CBCED4;";
							}
							if($secondaryInsuranceCoId==0){
								$sec_bg_paid_col="background:#CBCED4;";
							}
							if($tertiaryInsuranceCoId==0){
								$ter_bg_paid_col="background:#CBCED4;";
							}
							if(array_sum($proc_base_flag[$charge_list_id])>0){
								if($priPaidStat_arr[$l]['primary_paid']=="false" && $primaryInsuranceCoId>0){
									$pri_bor_paid_col="red_border";
								}else if($secPaidStat_arr[$l]['secondary_paid']=="false" && $secondaryInsuranceCoId>0){
									$sec_bor_paid_col="red_border";
								}else if($terPaidStat_arr[$l]['tertiary_paid']=="false" && $tertiaryInsuranceCoId>0){
									$ter_bor_paid_col="red_border";
								}else{
									if($newBal>0){
										$pat_bor_paid_col="red_border";
									}
									$pat_heard_ins="true";
								}
							}else{
								if(array_sum($final_tot_proc_pat_bal_amt_arr[$charge_list_id])>0 && array_sum($final_tot_proc_pat_bal_amt_arr[$charge_list_id])>=array_sum($proc_base_flag[$charge_list_id])){
									$pat_bor_paid_col="red_border";
									$pat_heard_ins="true";
								}
							}
							
							$enc_submitted_detail=array();
							$enc_submitted_detail=$enc_submitted_arr[$encounter_id];
							
							$enc_posted_detail=array();
							$enc_posted_detail=$enc_posted_arr[$encounter_id];
							
							if(count($enc_submitted_detail)==0){
								$pri_bor_paid_col="";
								$sec_bor_paid_col="";
								$ter_bor_paid_col="";
							}
							
							
						$StrHtml .='<td style="'.$pri_bg_paid_col.'" class="'.$pri_bor_paid_col.' text-right">';
						$PdfHtml .='<td style="text-align:right; '.$pri_bg_paid_col.''.$del_style.'" class="text_10b '.$pri_bor_paid_col.'">';
							if($primaryInsuranceCoId>0){ 
							$primaryInsuranceCoId_tot_enc=array_sum($tot_enc_paid_amt_arr[$encounter_id][1])-array_sum($tot_enc_neg_paid_amt_arr[$encounter_id][1]);
							$tot_paid_amt_arr[1][]=$primaryInsuranceCoId_tot_enc;
							if(count($tot_enc_paid_amt_arr[$encounter_id][1])>0){	
								$show_green_pri="style=color:Green;";
							}else{
								$show_green_pri="style=color:black;";
							}
						$StrHtml .='<a class="'.$show_green_pri.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
									'.numberFormat($primaryInsuranceCoId_tot_enc,2,"yes").'
								</a>';
						$PdfHtml .=numberFormat($primaryInsuranceCoId_tot_enc,2,"yes");		
							}
						$StrHtml .='</td>
						<td style="'.$sec_bg_paid_col.'" class="'.$sec_bor_paid_col.' text-right">';
						$PdfHtml .='</td>
						<td style="text-align:right; '.$sec_bg_paid_col.''.$del_style.'" class="text_10b '.$sec_bor_paid_col.'">';
							if($secondaryInsuranceCoId>0){
								$secondaryInsuranceCoId_tot_enc=array_sum($tot_enc_paid_amt_arr[$encounter_id][2])-array_sum($tot_enc_neg_paid_amt_arr[$encounter_id][2]);
								$tot_paid_amt_arr[2][]=$secondaryInsuranceCoId_tot_enc;
								if(count($tot_enc_paid_amt_arr[$encounter_id][2])>0){
									$show_green_sec="style=color:Green;";
								}else{
									$show_green_sec="style=color:black;";
								} 
								$StrHtml .='<a class="'.$f_class.'" '.$show_green_sec.' href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								   '.numberFormat($secondaryInsuranceCoId_tot_enc,2,"yes").'
								</a>';	
								$PdfHtml .= numberFormat($secondaryInsuranceCoId_tot_enc,2,"yes");	
							}
						$StrHtml .='</td>
						<td style="'.$ter_bg_paid_col.'" class="'.$ter_bor_paid_col.' text-right">';
						$PdfHtml .='</td>
						<td style="text-align:right; '.$ter_bg_paid_col.''.$del_style.'" class="text_10b '.$ter_bor_paid_col.'">';
							if($tertiaryInsuranceCoId>0){
								$tertiaryInsuranceCoId_tot_enc=array_sum($tot_enc_paid_amt_arr[$encounter_id][3])-array_sum($tot_enc_neg_paid_amt_arr[$encounter_id][3]);
								$tot_paid_amt_arr[3][]=$tertiaryInsuranceCoId_tot_enc;
								if(count($tot_enc_paid_amt_arr[$encounter_id][3])>0){
									$show_green_ter="style=color:Green;";
								}else{
									$show_green_ter="style=color:black;";
								} 
								$StrHtml .='<a class="'.$f_class.'" '.$show_green_ter.' href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								   '.numberFormat($tertiaryInsuranceCoId_tot_enc,2,"yes").'
								</a>';	
								$PdfHtml .=numberFormat($tertiaryInsuranceCoId_tot_enc,2,"yes");	
							}	
						$without_ins_tot_enc=array_sum($tot_enc_paid_amt_arr[$encounter_id][0])-array_sum($tot_enc_neg_paid_amt_arr[$encounter_id][0]);	
						$tot_paid_amt_arr[0][]=$without_ins_tot_enc;
						$adj_tot_enc_sum=array_sum($write_off_amt_enc_arr[$encounter_id]);
						
						$tot_pt_balance="";	
						$tot_pt_balance=array_sum($final_tot_proc_pat_bal_amt_arr[$charge_list_id]);
						$tot_ins_balance=array_sum($final_tot_proc_ins_bal_amt_arr[$charge_list_id]);
						
						$final_tot_pat_bal_amt_arr[]=$tot_pt_balance;
						$final_tot_ins_bal_amt_arr[]=$tot_ins_balance;
						
						if(count($tot_enc_paid_amt_arr[$encounter_id][0])>0){
							$show_green_sec="style=color:Green;";
						}else{
							$show_green_sec="style=color:black;";
						} 
						
						$StrHtml .='</td>
						 <td class="text-right">
							<a class="'.$f_class.'" '.$show_green_sec.' href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								'.numberFormat($without_ins_tot_enc,2,"yes").'
							</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'"  href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								'.numberFormat($adj_tot_enc_sum,2,"yes").'
							</a>
						</td>
						<td class="'.$pat_bor_paid_col.' text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								<span>'.numberFormat($tot_pt_balance,2,"yes").'</span>
							</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								<span>'.numberFormat($tot_ins_balance,2,"yes").'</span>
							</a>
						</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">';
						$PdfHtml .='</td>
						<td style="text-align:right;'.$del_style.'" class="text_10b">
							'.numberFormat($without_ins_tot_enc,2,"yes").'
						</td>
						<td style="text-align:right;'.$del_style.'" class="text_10b">
							'.numberFormat($adj_tot_enc_sum,2,"yes").'
						</td>
						<td style="text-align:right;'.$del_style.'" class="text_10b '.$pat_bor_paid_col.'">
							'.numberFormat($tot_pt_balance,2,"yes").'
						</td>
						<td style="text-align:right;'.$del_style.'" class="text_10b">
							'.numberFormat($tot_ins_balance,2,"yes").'
						</td>
						<td style="text-align:right; vertical-align:middle;'.$del_style.'" class="text_10b">';	
								if($overPaymentTotal>0){ 
									$StrHtml .='<span style="color:#5D738E;"><b>-'.numberFormat($overPaymentTotal, 2).'</b></span>'; 
									$PdfHtml .='<span style="color:#5D738E;"><b>-'.numberFormat($overPaymentTotal, 2).'</b></span>'; 
								}else{
									$amt_var_col="";
									if($newBal <= 0){
										$amt_var_col="Green";
									}else{
										$amt_var_col="Red";
									}
									$StrHtml .='<span style="color:'.$amt_var_col.'">'.numberFormat($newBal,2).'</span>';
									$PdfHtml .='<span style="color:'.$amt_var_col.'">'.numberFormat($newBal,2).'</span>';
								}
								
					$StrHtml .='</a></td>';
					$PdfHtml .='</td>';	
					$StrHtml .='<td>';
					$PdfHtml .='<td style="text-align:center;'.$del_style.'" class="text_10b">';
							if(count($enc_posted_detail)>0){
							$pdf_for_posted_date=array();
							$StrHtml .='<select name="postedDate" class="selectpicker" data-width="100%" data-size="5">'.$posted_opt_false;
										foreach($enc_posted_detail as $val){			
											$postedDate = $val;					
											$postedDateFinal = get_date_format($postedDate);
											$pdf_for_posted_date[]=$postedDateFinal;
											$StrHtml .='<option>'.$postedDateFinal.'</option>';
										} 
							$StrHtml .='</select>';
							$PdfHtml .=$pdf_for_posted_date[0];
							}else{ $StrHtml .='-';$PdfHtml .='-';}
					$StrHtml .='</td>';
					$PdfHtml .='</td>';
					$StrHtml .='<td>';
					$PdfHtml .='
						<td style="text-align:center;'.$del_style.'" class="text_10b">';		
								
							if(count($enc_submitted_detail)>0){
							$pdf_for_submitted_date=array();
							$StrHtml .='<select name="hcfaSubmittedDat"  class="selectpicker" data-width="100%" data-size="5">';
										foreach($enc_submitted_detail as $val){			
											$hcfaSubmittedDate = $val;					
											$hcfaSubmittedDate = get_date_format($hcfaSubmittedDate);
											$pdf_for_submitted_date[]=$hcfaSubmittedDate;
								$StrHtml .='<option>'.$hcfaSubmittedDate.'</option>';
										
							} 
							$StrHtml .='</select>';
							$PdfHtml .=$pdf_for_submitted_date[0];
							}else{ $StrHtml .='-';$PdfHtml .='-';}
						$StrHtml .='</td>
						<td class="text-right">
							<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">
								<span>'.$statusArr[$ar_claim_status_id].'</span>
							</a>
						</td>
					</tr>';
					$PdfHtml .='</td>
					</tr>';
					$proc_det_arr = $chld_cpt_arr[$charge_list_id];
					if(count($proc_det_arr)>1){
						foreach($proc_det_arr as $cpt_key=>$cpt_val){
							$g=$cpt_key;
							$proc_name = $proc_det_arr[$g]['cpt_prac_code'];
							$proc_cpt_desc = $proc_det_arr[$g]['cpt_desc'];
							$proc_totalAmount  = $proc_det_arr[$g]['totalAmount'];
							$proc_approvedAmt  = $proc_det_arr[$g]['approvedAmt'];
							$proc_deductAmt  = $proc_det_arr[$g]['deductAmt'];
							$proc_paidForProc = $proc_det_arr[$g]['paidForProc'];
							$proc_newBalance = $proc_det_arr[$g]['newBalance'];
							$proc_overPaymentForProc = $proc_det_arr[$g]['overPaymentForProc'];
							$proc_coPayAdjustedAmount = $proc_det_arr[$g]['coPayAdjustedAmount'];
							$proc_selfpay = $proc_det_arr[$g]['proc_selfpay'];
							$proc_pat_due = $proc_det_arr[$g]['pat_due'];
							$proc_ins_due = $proc_det_arr[$g]['pri_due']+$proc_det_arr[$g]['sec_due']+$proc_det_arr[$g]['tri_due'];
							 
							$chld_id=$proc_det_arr[$g]['charge_list_detail_id'];
							if($deposit_proc_arr[$encounter_id][$chld_id]){
								$proc_deposit=array_sum($deposit_proc_arr[$encounter_id][$chld_id]);
							}
							if($write_off_amt_arr[$encounter_id][$chld_id]){
								$proc_adj_amt=array_sum($write_off_amt_arr[$encounter_id][$chld_id]);
							}		
													
							$multi_paid_proc_by="";
							$paid_by_proc_arr_unique="";
							$paid_proc_by="";
							$paid_proc_by_final="";
							$paid_by_proc_arr_chk=array();
							$paid_by_proc_arr_copay=array();
							$paid_by_proc_arr1=array();
							
							$paid_by_proc_arr1=$paid_by_proc_arr[$encounter_id][$chld_id]['paid_by'];
							if($proc_coPayAdjustedAmount>0){
								$paid_by_proc_arr_copay=$paid_by_proc_arr[$encounter_id][0]['paid_by'];
							}
							
							$paid_by_proc_arr_chk=array_merge((array)$paid_by_proc_arr1,(array)$paid_by_proc_arr_copay);
							if($paid_by_proc_arr_chk){
								$paid_by_proc_arr_unique=array_values(array_unique($paid_by_proc_arr_chk));
							}
							if(count($paid_by_proc_arr_unique)==1){
								$paid_proc_by = $paid_by_proc_arr_chk[0];
							}
							
							if(count($paid_by_proc_arr_unique)>1){
								$paid_proc_by = "Multi";
								$multi_paid_proc_by=implode('<br>',$paid_by_proc_arr_unique);
							}
							
							if($paid_proc_by == ''){
								$paid_proc_by_final = '-';
							}else if($paid_proc_by=='Multi'){
								$paid_proc_by_final="Multi";
							}else if($paid_proc_by=='Patient'){
								$paid_proc_by_final="Patient";
							}else{
								if(count($paid_by_proc_arr_unique)==1){
									$paid_proc_by_final=$paid_proc_by;
								}
							}
							$chld_del_style=$f_class="";
							if($proc_det_arr[$g]['del_status']>0){
								$chld_del_style="color:#a94442;text-decoration:line-through;";
								$f_class="text-danger";
							}
							if($_REQUEST['details']==""){$det_dis="display:none;";}
						$StrHtml .='
							<tr id="td_proc_details_row_'.$chld_id.'" style="'.$det_dis.'">
								<td '.show_gro_color($group_color).'>&nbsp;</td>
								<td colspan="4">
									<input type="hidden" name="proc_details_row[]" value="'.$chld_id.'">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.$proc_cpt_desc.'
									</a>
								</td>
								<td>
									<a class="'.$f_class.'" '.show_tooltip($proc_name.' - '.$proc_cpt_desc).' href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										&nbsp;'.$proc_name.'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">';						
						$PdfHtml .='
							<tr id="td_proc_details_row_'.$chld_id.'" style="background-color:'.$g_color.' ; '.$det_dis.''.$chld_del_style.'">
								<td class="text_10">&nbsp;</td>
								<td colspan="3" style="width:190px; text-align:left;'.$chld_del_style.'" class="text_10">
										'.$proc_cpt_desc.'
								</td>
								<td style="width:85px; text-align:left;'.$chld_del_style.'" class="text_10">
										&nbsp;'.$proc_name.'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">';					 
										if($proc_coPayAdjustedAmount>0){
											$StrHtml .="<span style=\"color:Green;\">".numberFormat($copay,2)."</span>";
											$PdfHtml .="<span style=\"color:Green;\">".numberFormat($copay,2)."</span>";
										}else{
											$StrHtml .="0.00";
											$PdfHtml .="0.00";
										}
								$tot_chld_pri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][1])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][1]);	
								$tot_chld_sec_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][2])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][2]);
								$tot_chld_ter_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][3])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][3]);
								$tot_chld_pat_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][0])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][0]);
								if($proc_coPayAdjustedAmount>0){
									$tot_chld_pat_paid_amt_all=$tot_chld_pat_paid_amt_all+array_sum($tot_chld_paid_amt_copay_arr[$encounter_id][0]);
								}
								$tot_chld_pat_adj_amt_all=array_sum($write_off_amt_arr[$encounter_id][$chld_id]);	
								
								$tot_pt_balance_proc="";
								$ref_amt="";
								$ref_amt=$chld_cpt_ref_arr[$chld_id];
								
								$tot_pt_balance_proc=$proc_pat_due;
								$tot_ins_balance_proc=$proc_ins_due;
								$StrHtml .='</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($proc_totalAmount,2).'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($proc_approvedAmt,2).'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										<span style="color:#FFC000;font-weight:bold;">
											'.numberFormat($proc_deductAmt,2).'
										</span>
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_chld_pri_paid_amt_all,2,"yes").'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_chld_sec_paid_amt_all,2,"yes").'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_chld_ter_paid_amt_all,2,"yes").'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_chld_pat_paid_amt_all,2,"yes").'
									</a>
								</td>
								 <td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_chld_pat_adj_amt_all,2,"yes").'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_pt_balance_proc,2,"yes").'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">						
										'.numberFormat($tot_ins_balance_proc,2,"yes").'
									</a>
								</td>
								<td class="text-right">
									<a class="'.$f_class.'" href="javascript:editPaymentList(\''.$encounter_id.'\',\''.$del_charge_list_id.'\',\''.$review_chrg_srh.'\');">';	
								$PdfHtml .='
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										'.numberFormat($proc_totalAmount,2).'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										'.numberFormat($proc_approvedAmt,2).'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										<span style="color:#FFC000;font-weight:bold;">
											'.numberFormat($proc_deductAmt,2).'
										</span>
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										'.numberFormat($tot_chld_pri_paid_amt_all,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										'.numberFormat($tot_chld_sec_paid_amt_all,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										'.numberFormat($tot_chld_ter_paid_amt_all,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
										 '.numberFormat($tot_chld_pat_paid_amt_all,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
									 '.numberFormat($tot_chld_pat_adj_amt_all,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
									 '.numberFormat($tot_pt_balance_proc,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">
									 '.numberFormat($tot_ins_balance_proc,2,"yes").'
								</td>
								<td style="text-align:right;'.$chld_del_style.'" class="text_10">';
											if($proc_overPaymentForProc>0){ 
												$StrHtml .='<span style="color:#5D738E;"><b>-'.numberFormat($proc_overPaymentForProc, 2).'</b></span>'; 
												$PdfHtml .='<span style="color:#5D738E;"><b>-'.numberFormat($proc_overPaymentForProc, 2).'</b></span>'; 
											}else{
												$amt_var_col="";
												if($proc_newBalance <= 0){
													$amt_var_col="Green";
												}else{
													$amt_var_col="Red";
												}
												$StrHtml .='<span style="color:'.$amt_var_col.'">$'.$proc_newBalance.'</span>';
												$PdfHtml .='<span style="color:'.$amt_var_col.'">$'.$proc_newBalance.'</span>';
											}
									$StrHtml .='</a>
								</td>
								<td colspan="3"></td>
							</tr>';
							$PdfHtml .='
								</td>
								<td colspan="3" class="text_10">&nbsp;</td>
							</tr>';
						}
					}

					$era_enc_arr_list=$era_enc_arr[$encounter_id];
					foreach($era_enc_arr_list as $era_key => $era_enc_arr_list_id){
						$fileId=$era_enc_arr_list[$era_key]['id'];
						$ERA_patient_details_id=$era_enc_arr_list[$era_key]['ERA_patient_details_id'];
						
						$chkEffectiveDate = $era_enc_arr_list[$era_key]['chkEffectiveDate'];
						$era_835_Id = $era_enc_arr_list[$era_key]['835_Era_Id'];
						$TRN_payment_type_number = $era_enc_arr_list[$era_key]['TRN_payment_type_number'];
						$f_class=$chld_del_style="";
						if($_REQUEST['details']==""){$det_dis="display:none;";}
						$StrHtml .='<tr id="td_enc_era_row_'.$encounter_id.'" class="era_rows era_row_'.$encounter_id.'" style=" '.$det_dis.''.$chld_del_style.' font-weight:bold;">
							<td '.show_gro_color($group_color).' class="text-right">
								<a class="'.$f_class.'" href="javascript:viewDetailsFn(\''.$fileId.'\',\''.$era_835_Id.'\',\''.$ERA_patient_details_id.'\');">	
									ERA 
								</a>
							</td>
							<td colspan="4">
							<input type="hidden" name="era_details_row[]" value="'.$encounter_id.'">
								<a class="'.$f_class.'" href="javascript:viewDetailsFn(\''.$fileId.'\',\''.$era_835_Id.'\',\''.$ERA_patient_details_id.'\');">	
									'.$era_enc_arr_list[$era_key]['file_name'].'
								</a>
							</td>
							<td>
								<a class="'.$f_class.'" href="javascript:viewDetailsFn(\''.$fileId.'\',\''.$era_835_Id.'\',\''.$ERA_patient_details_id.'\');">	
									'.$chkEffectiveDate.'
								</a>	
							</td>
							<td>
								<a class="'.$f_class.'" href="javascript:viewDetailsFn(\''.$fileId.'\',\''.$era_835_Id.'\',\''.$ERA_patient_details_id.'\');">	
									'.$era_enc_arr_list[$era_key]['post_status'].'
								</a>	
							</td>
							<td colspan="14">&nbsp;</td>
						</tr>';
						$PdfHtml .='<tr style="background-color:'.$g_color.'; '.$det_dis.''.$chld_del_style.'">
							<td style="text-align:left;" class="text_10b">ERA </td>
							<td colspan="3" style="text-align:left;'.$chld_del_style.'" class="text_10">
								'.substr($era_enc_arr_list[$era_key]['file_name'],0,28).'
							</td>
							<td style="text-align:left;'.$chld_del_style.'" nowrap class="text_10">
								'.$chkEffectiveDate.'
							</td>
							<td colspan="2" style="text-align:left;'.$chld_del_style.'" class="text_10">
								'.$era_enc_arr_list[$era_key]['post_status'].'
							</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
							<td style="text-align:left;" class="text_10">&nbsp;</td>
						</tr>';
					}	
				}
			}
			if(count($getEncountersArr)>0){
				if($sum_ovrpay>$sum_balance){
					$sum_balance=$sum_ovrpay-$sum_balance;
					if($sum_balance>0){
						$tot_credit_sign='-';
					}
				}else{
					$sum_balance=$sum_balance-$sum_ovrpay;
				}
				
				$final_tot_pri_paid_amt_arr=array_sum($final_tot_paid_amt_arr[$patient_id][1])-array_sum($final_tot_neg_paid_amt_arr[$patient_id][1]);	
				$final_tot_sec_paid_amt_arr=array_sum($final_tot_paid_amt_arr[$patient_id][2])-array_sum($final_tot_neg_paid_amt_arr[$patient_id][2]);
				$final_tot_ter_paid_amt_arr=array_sum($final_tot_paid_amt_arr[$patient_id][3])-array_sum($final_tot_neg_paid_amt_arr[$patient_id][3]);
				$final_tot_pat_paid_amt_arr=array_sum($final_tot_paid_amt_arr[$patient_id][0])-array_sum($final_tot_neg_paid_amt_arr[$patient_id][0]);	
				//$final_tot_pat_adj_amt_arr=array_sum($write_off_amt_enc_final_arr);
				//$final_tot_pat_bal_amt=array_sum($final_tot_pat_bal_amt_arr);
				$final_tot_pat_bal_amt=array_sum($final_tot_pat_bal_amt_arr);
				$final_tot_ins_bal_amt=array_sum($final_tot_ins_bal_amt_arr);
				
			$StrHtml .='<tr class="purple_bar">
				<td colspan="6" class="text-right">Total:</td>
				<td class="text-right">'.numberFormat($sum_copay,2).'</td>
				<td class="text-right">'.numberFormat($sum_charges,2).'</td>
				<td class="text-right">'.numberFormat($sum_allow,2).'</td>
				<td class="text-right">'.numberFormat($sum_deduct,2).'</td>
				<td class="text-right">'.numberFormat(array_sum($tot_paid_amt_arr[1]),2,"yes").'</td>
				<td class="text-right">'.numberFormat(array_sum($tot_paid_amt_arr[2]),2,"yes").'</td>
				<td class="text-right">'.numberFormat(array_sum($tot_paid_amt_arr[3]),2,"yes").'</td>
				<td class="text-right">'.numberFormat(array_sum($tot_paid_amt_arr[0]),2,"yes").'</td>
				<td class="text-right">'.numberFormat($final_tot_pat_adj_amt_arr,2,"yes").'</td>
				<td class="text-right">'.numberFormat($final_tot_pat_bal_amt,2,"yes").'</td>
				<td class="text-right">'.numberFormat($final_tot_ins_bal_amt,2,"yes").'</td>
				<td class="text-right">'.$tot_credit_sign.numberFormat($sum_balance,2).'</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>';
			$PdfHtml .='<tr style="background:#ffffff;">
				<td colspan="5" class="text_10b" style="text-align:right;">Total:</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($sum_copay,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($sum_charges,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($sum_allow,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($sum_deduct,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($tot_paid_amt_arr[1]),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($tot_paid_amt_arr[2]),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($tot_paid_amt_arr[2]),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($tot_paid_amt_arr[0]),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_pat_adj_amt_arr,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_pat_bal_amt,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_ins_bal_amt,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.$tot_credit_sign.numberFormat($sum_balance,2).'</td>
				<td class="text_10b" style="text-align:right;"></td>
				<td class="text_10b" style="text-align:right;"></td>
				<td class="text_10b" style="text-align:right;"></td>
			</tr>';
			
			
			if($final_sum_ovrpay>$final_sum_balance){
				$final_sum_balance=$final_sum_ovrpay-$final_sum_balance;
			}else{
				$final_sum_balance=$final_sum_balance-$final_sum_ovrpay;
			}
			
			$StrHtml .='<tr class="purple_bar">
				<td colspan="6" class="text-right">Final Total:</td>
				<td class="text-right">'.numberFormat($final_sum_copay,2).'</td>
				<td class="text-right">'.numberFormat($final_sum_charges,2).'</td>
				<td class="text-right">'.numberFormat($final_sum_allow,2).'</td>
				<td class="text-right">'.numberFormat($final_sum_deduct,2).'</td>
				<td class="text-right">'.numberFormat($final_tot_pri_paid_amt_arr,2,"yes").'</td>
				<td class="text-right">'.numberFormat($final_tot_sec_paid_amt_arr,2,"yes").'</td>
				<td class="text-right">'.numberFormat($final_tot_ter_paid_amt_arr,2,"yes").'</td>
				<td class="text-right">'.numberFormat($final_tot_pat_paid_amt_arr,2,"yes").'</td>
				<td class="text-right">'.numberFormat(array_sum($write_off_amt_enc_final_arr),2,"yes").'</td>
				<td class="text-right">'.numberFormat(array_sum($grand_tot_proc_pat_bal_amt_arr),2,"yes").'</td>
				<td class="text-right">'.numberFormat(array_sum($grand_tot_proc_ins_bal_amt_arr),2,"yes").'</td>
				<td class="text-right">'.$tot_credit_sign.numberFormat($final_sum_balance,2).'</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>';
			
			$PdfHtml .='<tr style="background:#ffffff;">
				<td colspan="5" class="text_10b" style="text-align:right;">Final Total:</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_sum_copay,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_sum_charges,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_sum_allow,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_sum_deduct,2).'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_pri_paid_amt_arr,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_sec_paid_amt_arr,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_ter_paid_amt_arr,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat($final_tot_pat_paid_amt_arr,2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($write_off_amt_enc_final_arr),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($grand_tot_proc_pat_bal_amt_arr),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.numberFormat(array_sum($grand_tot_proc_ins_bal_amt_arr),2,"yes").'</td>
				<td class="text_10b" style="text-align:right;">'.$tot_credit_sign.numberFormat($final_sum_balance,2).'</td>
				<td class="text_10b" style="text-align:right;"></td>
				<td class="text_10b" style="text-align:right;"></td>
				<td class="text_10b" style="text-align:right;"></td>
			</tr>';
			
			} 
		$StrHtml .=' </table>';
	 	$PdfHtml .=' </table>';
		echo $StrHtml; 
	 ?>
</div>
<?php
	if(trim($PdfHtml) != ""){
		$PdfText = $PdfHtml;
		$filePath=write_html($PdfText);
	}
if($review_chrg_srh != 'hide'){?>
<script type="text/javascript">
	  <?php if(count($getEncountersArr)>0){ ?>
		var ar = [["a_r_bal","Balance View","top.fmain.OpenBalWin('');"],
				  ["print_post_pay","Print","top.fmain.html_to_pdf('<?php echo $filePath; ?>','l');"],
				  ["search_btn","Search","top.fmain.checkCreteriaRev();"]];
		top.btn_show("ACCOUNT",ar);
	  <?php } ?>
	
	$(document).ready(function(e) {
        var report_call='<?php echo $_SESSION['report_call'];?>';
		if(report_call=='1'){
			url="<?php echo $GLOBALS['rootdir'];?>/reports/<?php echo $_SESSION['report_file_name'];?>";
			window.open(url,'',"scrollbars=1,menubar=0,toolbar=0,status=0,width=1280,height=<?php echo $_SESSION['wn_height']-150;?>");
			window.focus();
		}
    });
</script>
<?php
	unset($_SESSION['report_call']);
	unset($_SESSION['report_file_name']);
	require_once("acc_footer.php");
}
?>