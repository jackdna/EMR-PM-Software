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
set_time_limit(300);
$title = "Service Payments";
if($_REQUEST['batch_pat_id']>0){
	$without_pat="yes"; 
}
include("acc_header.php");
require_once("../../interface/common/assign_new_task.php");
$stop_clm_status=1;
if($_REQUEST['batch_pat_id']>0){
	$patient_id = $_REQUEST['batch_pat_id'];
}else if($_REQUEST['crd_patient_id']>0){
	$patient_id = $_REQUEST['crd_patient_id'];
}else{
	$patient_id = $_SESSION['patient'];
}
$operator_id = $_SESSION['authId'];
$openInsCaseId = $_SESSION["currentCaseid"];
$operatorName = $_SESSION['authUser'];
$cur_date = date('m-d-Y');
$cur_date_db = date('Y-m-d');
$cur_time_db = date('H:i:s');
$cur_date_time_db = date('Y-m-d H:i:s');

$encounter_id = xss_rem($_REQUEST['encounter_id']);
$del_charge_list_id= xss_rem($_REQUEST['del_charge_list_id']);

if($_REQUEST['collectEid']<>""){
	$encounter_id = $_REQUEST['collectEid'];
}else{
	$encounter_id = $_REQUEST['encounter_id'];
}
$b_id = $_REQUEST['b_id'];	

$getRefChkStr = "Select refraction,discount_amount,discount_code,return_chk_proc,billing_amount FROM copay_policies WHERE policies_id = '1'";
$getRefChkQry = imw_query($getRefChkStr);
$getRefChkRow = imw_fetch_assoc($getRefChkQry);
$refractionChk = $getRefChkRow['refraction'];
$discount_code_chk = $getRefChkRow['discount_code'];
$pol_discount_amount=str_replace('%','',$getRefChkRow['discount_amount']);
$return_chk_proc=$getRefChkRow['return_chk_proc'];
$billing_amount = $getRefChkRow['billing_amount'];
if($return_chk_proc==""){
	$return_chk_proc="rtn-chk";
}
if($pol_discount_amount>0){
	$adm_dis=$pol_discount_amount;
	if(strpos($getRefChkRow['discount_amount'],'%')>0){
		$dis_type='%';
		$pol_discount_amount_show = '<br><span class="text_sdmaller" style="color:yellow;">(Dis. '.$getRefChkRow['discount_amount'].')</span>';
		$show_dis_amt=$getRefChkRow['discount_amount'];
	}else{
		$dis_type='$';
		$pol_discount_amount_show = '<br><span class="text_smdaller" style="color:yellow;">(Dis. $'.$getRefChkRow['discount_amount'].')</span>';
		$show_dis_amt='$'.$getRefChkRow['discount_amount'];
	}
}

if($_REQUEST['del_id']>0 && $encounter_id>0){
	
	$del_id=$_REQUEST['del_id'];
	$trans_mode=$_REQUEST['trans_mode'];
	$extra_id=$_REQUEST['extra_id'];
	$task_section="transaction_deleted";
	
	// DELETE Write-Off/Discount/Copay Write-Off
	if($trans_mode=="write_off" || $trans_mode=="write_off_copay" || strtolower($trans_mode)=="write off" || strtolower($trans_mode)=="discount"){
		imw_query("UPDATE paymentswriteoff SET delStatus='1',write_off_del_date='$cur_date_db',write_off_del_time='$cur_time_db',del_operator_id='$operator_id' WHERE write_off_id='$del_id' and encounter_id='$encounter_id'");
		if($trans_mode=="write_off_copay"){
			imw_query("UPDATE patient_charge_list SET coPayWriteOff = '0', coPayWriteOffDate = '' WHERE encounter_id = '$encounter_id'");
		}
		$task_chld=$extra_id;
		if(strtolower($trans_mode)=="discount"){
			re_calculate_tax($encounter_id);
		}
	}
	
	// DELETE Denied
	if($trans_mode=="denial"){
		imw_query("UPDATE deniedpayment SET denialDelStatus='1',denialDelDate='$cur_date_db',denialDelTime='$cur_time_db',del_operator_id='$operator_id' WHERE deniedId='$del_id' and encounter_id='$encounter_id'");
		$task_chld=$extra_id;
	}
	
	// DELETE Deductible
	if($trans_mode=="deductible"){
		imw_query("UPDATE payment_deductible SET delete_deduct='1',delete_deduct_date='$cur_date_db',delete_deduct_time='$cur_time_db',delete_operator_id='$operator_id' WHERE deductible_id='$del_id'");
		$task_chld=$extra_id;
	}
	
	// DELETE Adjustment/Over Adjustment/Returned Check
	if($trans_mode=="ovr_adj" || $trans_mode=="returned_check" || $trans_mode=="co_insurance" || $trans_mode=="co_payment"){
		imw_query("UPDATE account_payments SET del_status='1',del_date_time='$cur_date_time_db',del_operator_id='$operator_id' WHERE id='$del_id' and encounter_id='$encounter_id'");
		
		if($trans_mode=="returned_check"){
			$ret_chk_proc_arr=array();
			$sel=imw_query("select cpt_fee_id  from cpt_fee_tbl where LOWER(cpt_prac_code)='".strtolower($return_chk_proc)."'");
			while($row=imw_fetch_array($sel)){
				$ret_chk_proc_arr[]=$row['cpt_fee_id'];
			}
			$ret_chk_proc_imp=implode(',',$ret_chk_proc_arr);
			imw_query("update patient_charge_list_details set del_status='1',del_operator_id='$operator_id',trans_del_date='$cur_date_time_db' WHERE procCode in('$ret_chk_proc_imp') and charge_list_id='$extra_id' and approvedAmt=newBalance");
		}
	}
	
	// DELETE Tx Balance
	if($trans_mode=="tx_balance"){
		imw_query("UPDATE tx_payments SET del_status='1',del_date_time='$cur_date_time_db',del_operator_id='$operator_id' WHERE id='$del_id' and encounter_id='$encounter_id'");
	}
	
	// DELETE Tx Balance
	if($trans_mode=="tx_charge"){
		imw_query("UPDATE tx_charges SET del_status='1',del_date_time='$cur_date_time_db',del_operator_id='$operator_id' WHERE id='$del_id' and encounter_id='$encounter_id'");
	}
	
	// DELETE Credit/Debit/Refund
	if($trans_mode=="refund" || $trans_mode=="credit_debit"){
		imw_query("UPDATE creditapplied SET delete_credit='1',del_date_time='$cur_date_time_db',del_operator_id='$operator_id' WHERE crAppId='$del_id'");
		if($trans_mode=="credit_debit" && $extra_id>0){
			set_payment_trans($extra_id,'',$stop_clm_status);
		}
	}
	
	// DELETE Deposit/Paid/Interest Payment/Negative Payment/Copay
	if($trans_mode=="payment" || $trans_mode=="copay_payment"){
		imw_query("UPDATE patient_charges_detail_payment_info SET deletePayment='1',deleteDate='$cur_date_db',deleteTime='$cur_time_db',del_operator_id='$operator_id' WHERE payment_details_id = $del_id");
		
		$getPaymentDelStr = imw_query("SELECT payment_id,charge_list_detail_id FROM patient_charges_detail_payment_info WHERE payment_details_id='$del_id'");
		$getPaymentDelRow = imw_fetch_array($getPaymentDelStr);
		$del_payment_id=$getPaymentDelRow['payment_id'];
		$task_chld=$getPaymentDelRow['charge_list_detail_id'];
		imw_query("UPDATE patient_chargesheet_payment_info SET markPaymentDelete='1' WHERE payment_id = '$del_payment_id'");
		
		if($trans_mode=="copay_payment"){
			$get_copay_chld_qry = imw_query("SELECT charge_list_detail_id FROM patient_charge_list join patient_charge_list_details 
			on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			WHERE patient_charge_list_details.del_status='0' and patient_charge_list.encounter_id='$encounter_id'
			and patient_charge_list_details.coPayAdjustedAmount='1'");
			$get_copay_chld_row = imw_fetch_array($get_copay_chld_qry);
			$copay_charge_list_detail_id = $get_copay_chld_row['charge_list_detail_id'];
			$task_chld=$copay_charge_list_detail_id;
			imw_query("UPDATE patient_charge_list SET copayPaid='0',coPayPaidDate='0000-00-00',coPayAdjusted='',coPayAdjustedDate='' WHERE encounter_id='$encounter_id'");
			imw_query("UPDATE patient_charge_list_details SET coPayAdjustedAmount = '0' WHERE charge_list_detail_id = '$copay_charge_list_detail_id'");
			imw_query("UPDATE patient_charges_detail_payment_info SET charge_list_detail_id='$copay_charge_list_detail_id' WHERE payment_details_id = '$del_id' and charge_list_detail_id='0'");
			if($copay_charge_list_detail_id>0){
				imw_query("UPDATE report_enc_trans SET trans_type='paid' WHERE master_tbl_id = '$del_id' and trans_type='copay-paid'");
			}
			if(imw_num_rows($get_copay_chld_qry)==1){
				$sql = imw_query("select pcpi.paymentClaims,pcdpi.payment_details_id,pcdpi.paidForProc from patient_chargesheet_payment_info pcpi join patient_charges_detail_payment_info pcdpi
						 on pcpi.payment_id=pcdpi.payment_id WHERE pcpi.encounter_id='".$encounter_id."' and pcdpi.charge_list_detail_id='0' and pcdpi.deletePayment='0'");
				while($row=imw_fetch_array($sql)){
					if($row['paymentClaims']=="Negative Payment"){
						$enc_copay_paid_arr[$encounter_id][$row['payment_details_id']]=-$row['paidForProc'];
					}else{
						$enc_copay_paid_arr[$encounter_id][$row['payment_details_id']]=$row['paidForProc'];
					}
				}
				if(array_sum($enc_copay_paid_arr[$encounter_id])>0){
					imw_query("UPDATE patient_charge_list_details SET coPayAdjustedAmount = '1' WHERE charge_list_detail_id = '$copay_charge_list_detail_id'");
				}
			}
		}
		$task_section="payment_deleted_edited";
	}
	
	
	if($task_chld>0){
		$sel_chl=imw_query("select patient_charge_list.date_of_service,patient_data.lname,patient_data.fname,cpt_fee_tbl.cpt_prac_code
		from patient_charge_list_details join patient_charge_list on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
		join patient_data on patient_data.id=patient_charge_list.patient_id 
		join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id=patient_charge_list_details.procCode
		where patient_charge_list_details.charge_list_detail_id='$task_chld' limit 0,1");
		$row_chl=imw_fetch_array($sel_chl);
		
		$task_insert_arr=array();
		$task_insert_arr['patientid']=$patient_id;
		$task_insert_arr['operatorid']=$operator_id;
		$task_insert_arr['section']=$task_section;
		$task_insert_arr['status_id']=$trans_mode;
		$task_insert_arr['encounter_id']=$encounter_id;
		$task_insert_arr['date_of_service']=$row_chl['date_of_service'];
		$task_insert_arr['cpt_code']=$row_chl['cpt_prac_code'];
		$task_insert_arr['patient_name']=$row_chl['lname'].', '.$row_chl['fname'];
		assign_acc_task_rules_to($task_insert_arr);
	}
	
	//comment
	if($trans_mode=="comment"){
		$delComments = imw_query("DELETE FROM paymentscomment WHERE commentId = '$del_id'");
	}
?>
<script>
	var eId = '<?php echo $encounter_id; ?>';
	top.fmain.location.href="makePayment.php?encounter_id="+eId;
</script>
<?php	
}

$all_prov_ids=$all_enc_ins_ids=array();

if($_REQUEST['batch_trans_del_id']>0){
	$batch_trans_del_id = $_REQUEST['batch_trans_del_id'];
	imw_query("UPDATE manual_batch_transactions  SET del_status = '1' WHERE trans_id = '$batch_trans_del_id'");
}
if($_REQUEST['batch_trans_credit_del_id']>0){
	$batch_trans_credit_del_id = $_REQUEST['batch_trans_credit_del_id'];
	imw_query("UPDATE manual_batch_creditapplied SET delete_credit = '1' WHERE crAppId = '$batch_trans_credit_del_id'");
}
if($_REQUEST['batch_trans_tx_del_id']>0){
	$batch_trans_tx_del_id = $_REQUEST['batch_trans_tx_del_id'];
	imw_query("UPDATE manual_batch_tx_payments SET del_status = '1',del_operator_id='$operator_id',del_date_time='$cur_date_time_db' WHERE id = '$batch_trans_tx_del_id'");
}

$qry = "select * from patient_data where pid = $patient_id limit 0,1";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$patientName = ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));
$noBalanceBill = $row['noBalanceBill'];
$date_of_birth = get_date_format($row['DOB']);

$batch_track_no=array();
$getbatch = imw_query("Select batch_id,tracking FROM manual_batch_file WHERE post_status = '1'");
while($getbatchrow = imw_fetch_array($getbatch)){
	$batch_track_no[$getbatchrow['batch_id']]=$getbatchrow['tracking'];
}

if($_REQUEST['b_id']>0){
	$getbatch = imw_query("Select default_write_code,default_adj_code,default_payment_date,default_transaction_date,default_check_no,default_pay_location,total_payment FROM manual_batch_file WHERE batch_id = '".$_REQUEST['b_id']."'");
	$batch_data_row = imw_fetch_array($getbatch);
	$default_payment_date=$batch_data_row['default_payment_date'];
	$default_check_no=$batch_data_row['default_check_no'];
	$default_write_code=$batch_data_row['default_write_code'];
	$default_adj_code=$batch_data_row['default_adj_code'];
	$default_pay_location=$batch_data_row['default_pay_location'];
	$batch_total_payment=$batch_data_row['total_payment'];
	
	$tot_amt_batch_arr=array();
	$sel_tot_paid=imw_query("select trans_amt,payment_claims from manual_batch_transactions where batch_id='".$_REQUEST['b_id']."' and del_status!=1");
	while($fet_tot_amt=imw_fetch_array($sel_tot_paid)){
		$batch_payment_claims=strtolower($fet_tot_amt['payment_claims']);
		if($batch_payment_claims=="paid" || $batch_payment_claims=="deposit" || $batch_payment_claims=="interest payment"){
			$tot_amt_batch_arr[]=$fet_tot_amt['trans_amt'];
		}
		if($batch_payment_claims=="negative payment"){
			$tot_amt_batch_arr[]=-$fet_tot_amt['trans_amt'];
		}
	}
	$tot_amt_batch=array_sum($tot_amt_batch_arr);
	$batch_remaining_amt=numberFormat(($batch_total_payment - $tot_amt_batch),2,'yes');
	$tot_amt_batch_num=numberFormat($tot_amt_batch,2,'yes');
	echo '<script type="text/javascript">
		top.$("#batch_applied_amt").html("'.$tot_amt_batch_num.'");
		top.$("#batch_remaining_amt").html("'.$batch_remaining_amt.'");
	</script>';
}

$wrt_id_arr=array();
$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
while($sel_write=imw_fetch_array($sel_rec)){
	$wrt_id_arr[]=$sel_write['w_id'];
	$wrt_name_arr[$sel_write['w_id']]=$sel_write['w_code'];
	$wrt_def_arr[$sel_write['w_id']]=$sel_write['w_default'];
	$write_off_code_data[$sel_write['w_id']]=$sel_write;
}
$wrt_drop='<select name="show_write_code" id="show_write_code"  class="selectpicker" data-width="90%"><option value="">Please Select</option>';
foreach($wrt_id_arr as $wrt_id){
	$sel_wrt="";	
	if($wrt_def_arr[$wrt_id]=='yes'){ $sel_wrt="selected";}
	$wrt_drop.='<option value="'.$wrt_id.'" '.$sel_wrt.'>'.$wrt_name_arr[$wrt_id].'</option>';
} 
$wrt_drop.='</select>'; 
$wrt_foot='<button type="button" class="btn btn-success" id="wrt_but_id">OK</button>';
show_modal('write_off_div','Write off Code',$wrt_drop,$wrt_foot);

$show_cas_code_arr=array();
$getCASStr = "SELECT * ,CASE WHEN CAST( cas_code AS UNSIGNED ) =0 THEN 1000 ELSE CAST(cas_code AS UNSIGNED) END AS Casted_Column FROM cas_reason_code ORDER BY Casted_Column, cas_code";
$getCASQry = imw_query($getCASStr);
while($getCASRow = imw_fetch_array($getCASQry)){
	$show_cas_code_arr[$getCASRow['cas_code']]=$getCASRow['cas_desc'];
	$cas_cod=$getCASRow['cas_code'].'--'.$getCASRow['cas_desc'];
 	$stringAllCasCode.="'".addslashes($cas_cod)."',";
	$stringAllCasCode.="'".addslashes($getCASRow['cas_desc'])."',";
	$cas_code_data[$getCASRow['cas_id']]=$getCASRow;
}
$stringAllCasCode = substr($stringAllCasCode,0,-1);
	
/*$sel_rec_comm=imw_query("select * from int_ext_comment where status='0' order by comment");
while($sel_comm=imw_fetch_array($sel_rec_comm)){
	$coment=$sel_comm['comment'];
 	$stringAllComment.="'".addslashes($coment)."',";
}		
$stringAllComment = substr($stringAllComment,0,-1);*/

//------------------------ CPT Code and Fee Detail------------------------//
$qry = imw_query("SELECT cpt_fee_tbl.*,cpt_fee_table.cpt_fee,cpt_fee_table.fee_table_column_id 
				  FROM cpt_fee_tbl join cpt_fee_table on cpt_fee_tbl.cpt_fee_id=cpt_fee_table.cpt_fee_id");
while($row = imw_fetch_array($qry)){
	$cpt_fee_tbl_data[$row['cpt_fee_id']]=$row;
	//$cpt_fee_tbl_table_data[$row['cpt_fee_id']][]=$row;
}
//------------------------ CPT Code and Fee Detail ------------------------//

//------------------------ Write-Off Detail ------------------------//
$qry = imw_query("SELECT * FROM paymentswriteoff WHERE patient_id = '$patient_id' ORDER BY write_off_id DESC");
while($row = imw_fetch_array($qry)){
	$payment_wrt_data[$row['encounter_id']][$row['charge_list_detail_id']][]=$row;
}
//------------------------ Write-Off Detail ------------------------//

//------------------------ Discount Code ------------------------//
$qry = imw_query("SELECT * FROM discount_code order by d_code");
while($row = imw_fetch_array($qry)){
	$discount_code_data[$row['d_id']]=$row;
}
//------------------------ Discount Code ------------------------//

//------------------------ Adj Code ------------------------//
$qry = imw_query("SELECT * FROM adj_code order by a_code");
while($row = imw_fetch_array($qry)){
	$adj_code_data[$row['a_id']]=$row;
}
//------------------------ Adj Code ------------------------//

//------------------------ Submitted Record Detail ------------------------//
$qry = imw_query("SELECT * FROM submited_record where patient_id = '$patient_id' order by submited_id desc");
while($row = imw_fetch_array($qry)){
	$submit_record_data[$row['encounter_id']][]=$row;
}
//------------------------ Submitted Record Detail ------------------------//

//------------------------ Credit/Debit Detail ------------------------//
$qry = imw_query("SELECT * FROM creditapplied where credit_applied='1' and (patient_id = '$patient_id' or patient_id_adjust='$patient_id')");
while($row = imw_fetch_array($qry)){
	$credit_record_data[$row['charge_list_detail_id']][]=$row;
	$credit_record_data[$row['charge_list_detail_id_adjust']][]=$row;
	$credit_record_adj_data[$row['charge_list_detail_id_adjust']][]=$row;
}
//------------------------ Credit/Debit Detail ------------------------//

//------------------------ Credit/Debit Detail ------------------------//
$qry = imw_query("SELECT * FROM account_payments where patient_id='$patient_id' ORDER BY payment_date DESC");
while($row = imw_fetch_array($qry)){
	$account_payments_data[$row['charge_list_detail_id']][]=$row;
}
//------------------------ Credit/Debit Detail ------------------------//

//------------------------ TX Payment Detail ------------------------//
$qry = imw_query("SELECT * FROM tx_payments where patient_id='$patient_id' ORDER BY payment_date DESC");
while($row = imw_fetch_array($qry)){
	$tx_payments_data[$row['charge_list_detail_id']][]=$row;
}
//------------------------ TX Payment Detail ------------------------//

//------------------------ TX Payment Detail ------------------------//
$qry = imw_query("SELECT * FROM tx_charges where patient_id='$patient_id' ORDER BY entered_date DESC");
while($row = imw_fetch_array($qry)){
	$tx_charges_data[$row['charge_list_detail_id']][]=$row;
}
//------------------------ TX Payment Detail ------------------------//

//------------------------ Denied Payment Detail ------------------------//
$qry = imw_query("SELECT * FROM deniedpayment where patient_id='$patient_id' ORDER BY deniedId DESC");
while($row = imw_fetch_array($qry)){
	$denied_payment_data[$row['charge_list_detail_id']][]=$row;
}
//------------------------ Denied Payment Detail ------------------------//

//------------------------ Insurance Case Detail ------------------------//
$qry = imw_query("SELECT a.ins_caseid, b.case_name FROM insurance_case a join insurance_case_types b on a.ins_case_type=b.case_id WHERE a.patient_id='$patient_id'");
while($row = imw_fetch_array($qry)){
	$ins_case_name_arr[$row['ins_caseid']]=$row['case_name'];
}
//------------------------ Insurance Case Detail ------------------------//

//------------------------ Insurance Detail ------------------------//
$qry = imw_query("select provider,type,policy_number from insurance_data where pid in($patient_id) and provider>0");
while($row = imw_fetch_array($qry)){ 
	$ins_data[$row['provider']]=$row;
	$all_enc_ins_ids[$row['provider']] = $row['provider'];
}
//------------------------ Insurance Detail ------------------------//

//------------------------ Payment Method Detail ------------------------//
$qry = imw_query("select pm_id,pm_name from payment_methods where del_status='0' order by default_method desc, pm_name");
while($row = imw_fetch_array($qry)){ 
	$payment_method_arr[$row['pm_id']]=$row['pm_name'];
}
//------------------------ Payment Method Detail ------------------------//

//------------------------ Transation Modify Detail ------------------------//
$qry = imw_query("SELECT * FROM transaction_modify where patient_id='$patient_id' and encounter_id='$encounter_id' and encounter_id>0 ORDER BY trans_id asc");
while($row = imw_fetch_array($qry)){
	$row['payment_id']=$row['trans_id'];
	$row['paidForProc']=$row['trans_amount'];
	$row['paidBy']=$row['trans_by'];
	$row['insProviderId']=$row['trans_ins_id'];
	$row['date_of_payment']=$row['trans_dop'];
	$row['transaction_date']=$row['trans_dot'];
	$row['paymentClaims']=$row['trans_type'];
	$row['operatorId']=$row['trans_operator_id'];
	$row['payment_mode']=$row['trans_method'];
	$row['paidDate']=$row['trans_dop'];
	$row['checkNo']=$row['check_number'];
	$row['creditCardCo']=$row['cc_type'];
	$row['creditCardNo']=$row['cc_number'];
	$row['expirationDate']=$row['cc_exp_date'];
	$row['deleteDate']=$row['trans_del_date'];
	$row['deletePayment']=1;
	$trans_chld_data_arr['pcpi'][$row['charge_list_detail_id']][]=$row;
}
//------------------------ Transation Modify Detail ------------------------//

//------------------------ Facility ------------------------//
$selQry = "select * from facility order by name ASC";
$res = imw_query($selQry);
while($row = imw_fetch_array($res)){
	$fac_data_arr[$row['id']]=$row;
	$fac_name_exp=explode(' ',$row['name']);
	foreach($fac_name_exp as $key=>$val){
		$fac_ins_name[$row['id']][]=substr($fac_name_exp[$key],0,1);
	}
	$fac_ins_name_arr[$row['id']]=strtoupper(implode('',$fac_ins_name[$row['id']]));
}
//------------------------ Facility ------------------------//
		
if($_REQUEST['del_charge_list_id']>0){
	$del_charge_list_id=$_REQUEST['del_charge_list_id'];
	$whr_del_chl="charge_list_id='$del_charge_list_id' and ";
	$whr_del_chld="";
}else{
	if($encounter_id>0){
		$whr_del_chl=" del_status='0' and ";
		$whr_del_chld="";
	}else{
		$whr_del_chl=" del_status='0' and ";
		$whr_del_chld=" del_status='0' and ";
	}
}

if($encounter_id>0){
	$whr_del_chl .=" patient_id = '$patient_id' and encounter_id ='$encounter_id'";
}else{
	$whr_del_chl .=" patient_id = '$patient_id' and (totalBalance>0 or overPayment>0)";
	$whr_del_chld.=" (newBalance>0 or overPaymentForProc>0) and ";	
}

$getEncounterStr = "SELECT * FROM patient_charge_list WHERE $whr_del_chl ORDER BY overPayment desc,date_of_service desc";
$getEncounterQry = imw_query($getEncounterStr);
if(imw_num_rows($getEncounterQry)>0){
	$collection_chk=false;
	while($getEncounterRow = imw_fetch_array($getEncounterQry)){
		
		if($getEncounterRow['collection']=="true"){
			$collection_chk=true;
		}
		
		if($getEncounterRow['date_of_service']>='2011-01-01'){
			$get_vip_wrt_id = set_write_off_trans_vip($getEncounterRow['encounter_id']);
			set_payment_trans($getEncounterRow['encounter_id'],'',$stop_clm_status);
			patient_proc_bal_update($getEncounterRow['encounter_id']);
			if(count($get_vip_wrt_id)>0){
				$get_vip_wrt_ids=implode("','",$get_vip_wrt_id);
				//------------------------ VIP Write-Off Detail ------------------------//
				$wrt_qry = imw_query("SELECT * FROM paymentswriteoff WHERE write_off_id in ('".$get_vip_wrt_ids."') ORDER BY write_off_id DESC");
				while($wrt_row = imw_fetch_array($wrt_qry)){
					$payment_wrt_data[$wrt_row['encounter_id']][$wrt_row['charge_list_detail_id']][]=$wrt_row;
				}
				//------------------------ VIP Write-Off Detail ------------------------//
			}
		}
		$chl_data_arr[$getEncounterRow['encounter_id']]=$getEncounterRow;
		$enc_arr[$getEncounterRow['encounter_id']]=$getEncounterRow['encounter_id'];
		$chl_id_arr[$getEncounterRow['charge_list_id']]=$getEncounterRow['charge_list_id'];
		$gro_arr[$getEncounterRow['gro_id']]=$getEncounterRow['gro_id'];
		
		$all_prov_ids[] = $getEncounterRow['primaryProviderId'];
		$all_prov_ids[] = $getEncounterRow['secondaryProviderId'];
		$all_prov_ids[] = $getEncounterRow['tertiaryProviderId'];
		
		$all_enc_ins_ids[$getEncounterRow['primaryInsuranceCoId']] = $getEncounterRow['primaryInsuranceCoId'];
		$all_enc_ins_ids[$getEncounterRow['secondaryInsuranceCoId']] = $getEncounterRow['secondaryInsuranceCoId'];
		$all_enc_ins_ids[$getEncounterRow['tertiaryInsuranceCoId']] = $getEncounterRow['tertiaryInsuranceCoId'];
		
		$reSubmitDetails=count($submit_record_data[$getEncounterRow['encounter_id']]);
		
		$sql = imw_query("select pcdpi.*,pcpi.* from patient_chargesheet_payment_info pcpi join patient_charges_detail_payment_info pcdpi
				 on pcpi.payment_id=pcdpi.payment_id WHERE pcpi.encounter_id='".$getEncounterRow['encounter_id']."'");
		while($row=imw_fetch_array($sql)){
			if($row['deletePayment']=="0"){
				$payment_chld_paid_data[$row['charge_list_detail_id']][]=$row['paidForProc'];
			}
			$payment_chld_data_arr[$row['charge_list_detail_id']][]=$row;
			$trans_chld_data_arr['pcpi'][$row['charge_list_detail_id']][]=$row;
			if($row['charge_list_detail_id']==0 && $row['deletePayment']=="0"){
				if($row['paymentClaims']=="Negative Payment"){
					$enc_copay_paid_arr[$getEncounterRow['encounter_id']][$row['payment_details_id']]=-$row['paidForProc'];
				}else{
					$enc_copay_paid_arr[$getEncounterRow['encounter_id']][$row['payment_details_id']]=$row['paidForProc'];
				}
			}
		}
		$tot_paid_chk1=array_sum($payment_chld_paid_data[0]);
	
		$copay_write_off_amount=0;
		foreach($payment_wrt_data[$getEncounterRow['encounter_id']][0] as $w_key=>$w_val){
			if($payment_wrt_data[$getEncounterRow['encounter_id']][0][$w_key]['delStatus']=='0'){
				$copay_write_off_amount=$copay_write_off_amount+$payment_wrt_data[$getEncounterRow['encounter_id']][0][$w_key]['write_off_amount'];
			}
		}
		$tot_paid_chk1=$tot_paid_chk1+$copay_write_off_amount;
		
		$tot_paid_chk_arr[$getEncounterRow['encounter_id']]=$tot_paid_chk1;
	}
}else{
?>
	<table class="table table-bordered table-hover table-striped">
        <tr>
            <td class="text-center lead">No Dues For Any DOS.</td>
        </tr>
    </table>    
<?php } ?>
<script type="text/JavaScript">
var customarrayCasCode ="";
<?php
	if($stringAllCasCode!=""){
?>
	var customarrayCasCode= new Array(<?php echo remLineBrk($stringAllCasCode); ?>);
<?php
}?>
</script>
<?php

	if(count($all_enc_ins_ids)>0){
		$all_enc_ins_ids_imp=implode(",",$all_enc_ins_ids);
		//------------------------ Insurance Company ------------------------//
		$qry = "select * from insurance_companies where id in ($all_enc_ins_ids_imp) order by name ASC";
		$res = imw_query($qry);
		while($row = imw_fetch_array($res)){
			$ins_comp_data[$row['id']]=$row;
			$ins_house_code_data[strtolower($row['in_house_code'])]=$row;
			$ins_house_code_data[strtolower($row['name'])]=$row;
			$fee_table_column_arr[$row['id']]=$row['FeeTable'];
		}
		//------------------------ Insurance Company ------------------------//
	}
	
	$sel_qry="select GROUP_CONCAT(DISTINCT groups_new.name SEPARATOR ', ') as group_name from patient_charge_list 
			join groups_new on groups_new.gro_id=patient_charge_list.gro_id
			where patient_charge_list.del_status='0' and patient_charge_list.gro_id>0";
	if($encounter_id>0){
		$sel_qry.=" and patient_charge_list.encounter_id='$encounter_id'";
	}else{
		$sel_qry.=" and patient_charge_list.totalBalance>0";
	}
	$sel_row=imw_query($sel_qry);
	$sel_data=imw_fetch_array($sel_row);
	$group_name_final=$sel_data['group_name'];
?>
<?php
$usr_qry = imw_query("SELECT id,fname,mname,lname,collect_refraction,delete_status FROM users");
while($usr_row = imw_fetch_array($usr_qry)){
	$usr_id=$usr_row['id'];
	$usr_fname=$usr_row['fname'];
	$usr_lname=$usr_row['lname'];
	$usr_mname=$usr_row['mname'];
	$usr_alias_name[$usr_id]=substr($usr_fname,0,1).substr($usr_mname,0,1).substr($usr_lname,0,1);
	$dr_alias_name[$usr_id]=substr($usr_fname,0,1).substr($usr_mname,0,1).substr($usr_lname,0,1);
	$usr_full_name[$usr_id]=$usr_lname.', '.$usr_fname;
	if($refractionChk=='No'){
		if(in_array($usr_id,$all_prov_ids)){
			if($usr_row['collect_refraction']=='1'){
				$refractionChk='yes';
			}
		}
	}
	if($usr_row['delete_status']==0){
		$notes_usr_arr[$usr_id]=$usr_lname.', '.$usr_fname;
	}
}
if($encounter_id<=0){
	$form_all="all_";
}
?>
<form name="makePaymentFrm" id="makePaymentFrm" action="payments.php" method="post" onSubmit="return check();">
    <input type="hidden" name="enc_id_read" id="enc_id_read" value="<?php echo $encounter_id; ?>">
	<input type="hidden" name="copay" id="copay" value="<?php echo $copay; ?>">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
    <input type="hidden" name="cash_dis" id="cash_dis" value="">
    <input type="hidden" name="adm_dis" id="adm_dis" value="<?php echo $adm_dis; ?>" />
    <input type="hidden" name="show_dis_amt" id="show_dis_amt" value="<?php echo $show_dis_amt; ?>" />
    <input type="hidden" name="discount_code_chk" id="discount_code_chk" value="<?php echo $discount_code_chk; ?>" />
    <input type="hidden" name="dis_type" id="dis_type" value="<?php echo $dis_type; ?>" />
    <input type="hidden" name="refractionChk" id="refractionChk" value="<?php echo $refractionChk; ?>" />
    <input type="hidden" name="apply" id="apply">
    <input type="hidden" name="batch_pat_id" id="batch_pat_id" value="<?php echo $_REQUEST['batch_pat_id']; ?>">
    <input type="hidden" name="b_id" id="b_id" value="<?php echo $_REQUEST['b_id']; ?>">
    <input type="hidden" name="deb_patient_id" id="deb_patient_id" value="<?php echo $_REQUEST['deb_patient_id']; ?>">
    <input type="hidden" name="deb_chld_id" id="deb_chld_id" value="<?php echo $_REQUEST['deb_chld_id']; ?>">
    <input type="hidden" name="deb_amt" id="deb_amt" value="<?php echo $_REQUEST['deb_amt']; ?>">
    <input type="hidden" name="deb_ins_type" id="deb_ins_type" value="<?php echo $_REQUEST['deb_ins_type']; ?>">
    <input type="hidden" name="default_check_no" id="default_check_no" value="<?php echo $default_check_no; ?>">
    <input type="hidden" name="default_write_code" id="default_write_code" value="<?php echo $default_write_code; ?>">
    <input type="hidden" name="default_adj_code" id="default_adj_code" value="<?php echo $default_adj_code; ?>">
    <input type="hidden" name="edit_page" id="edit_page" value="makePayment">
    <input type="hidden" name="enc_patient_name" id="enc_patient_name" value="<?php echo $patientName; ?>">
    <input type="hidden" name="valueBtn" id="valueBtn" value="" />
	<div style="height:<?php echo $_SESSION['wn_height']-405; ?>px; overflow-y:auto;" id="enter_payment_div">
		<?php 
			include_once("chargeDetails.php");
		?>
	</div>
    <?php if($form_all!="all_"){?>
    <table class="table table-bordered table-striped" style="margin-bottom:5px !important;">
        <tr>
			<td>
				<div class="checkbox">
					<input type="checkbox" name="task_on_reminder" id="task_on_reminder" value="yes" checked="checked" onClick="task_reminder_date();"/>
					<label for="task_on_reminder">Task On Rem. Date</label>
				</div>
			</td>
            <td>
                <select name="enc_notes_type" id="enc_notes_type" class="form-control minimal">
                    <option value="Internal">Internal</option>
                    <option value="External">External</option>
                </select>
            </td>
            <td>
                <textarea name="enc_notes" id="enc_notes" rows="1" cols="105" class="form-control" placeholder="Add New Note"></textarea>
            </td>
            <td>
                <select class="selectpicker" id="selectAssignFor" name="assignFor[]" data-width="250" data-actions-box="true" data-live-search="true" data-title="Assign as a Notes/Task for" data-size="5" multiple data-selected-text-format="count > 1">
					<?php 
                        $optString = '';
                        if(count($notes_usr_arr) > 0){
                            foreach($notes_usr_arr as $phyId => $phyVal){
                                $sel="";
                                if($phyId==$_SESSION['authId'] && isDefaultUserSelected()){
                                    $sel=" selected";
                                }
                                $optString .= '<option value="'.$phyId.'" '.$sel.'>'.$notes_usr_arr[$phyId].'</option>';
                            }
                        }
                        echo $optString;
                    ?>
                </select>
            </td>
            <td>
                 <div class="input-group">
                    <input type="text" name="notes_reminder_date" id="notes_reminder_date" value="" class="form-control date-pick" autocomplete="off" placeholder="Reminder Date">
                    <label class="input-group-addon pointer" for="notes_reminder_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                </div>	
            </td>
           		
        </tr>
    </table>
    <?php } ?>
</form> 
	
<?php
if($collection_chk==true){
	$coll_but="Un-Collection";
}else{
	$coll_but="Collection";
}
?>
<?php
if($del_charge_list_id>0){
?>
 <script type="text/javascript">
	top.btn_show();
</script>
<?php	
}else{
?>
<script type="text/javascript">
	var ar = [["a_r_bal","Balance View","top.fmain.OpenBalWin('<?php echo $encounter_id; ?>');"],
			  ["applySubmit","Done","top.fmain.applyFn('applySubmit','applyRecieptSubmit');"],
			  ["applyRecieptSubmit","Done & Print Receipt","top.fmain.applyFn('applySubmit','applyRecieptSubmit','print');"],
			  ["printRecieptSubmit","Print Receipt","top.fmain.printReceipt_all();"]
			  <?php if($_REQUEST['encounter_id']>0 && $reSubmitDetails>0){?>
			  ,["post_charges","Re-submit","top.fmain.postCharges_pmt();"]
			  <?php }?>
			  ,["cancel","Cancel","top.fmain.reload_frm('AccountingRP');"]
			  ];
	top.btn_show("ACCOUNT",ar);
</script>
<?php
}
?>
<?php
	list($y,$m,$d)=explode('-',$letter_sent_date);
	$coll_date=$m.'-'.$d.'-'.substr($y,2);
	if($letter_sent_date!="0000-00-00" && $_REQUEST['encounter_id']>0 && $coll_date!='--'){
?>
<script type="text/javascript">
	$('#coll_date').html('<?php echo "Collection Date : ".$coll_date; ?>');
</script>
<?php }?>
<?php if($encounter_id>0){?>
<?php
	$acc_enc="yes";
	$date_of_service_ap=$getEncounterRow['date_of_service'];
}
?>
<script type="text/javascript">
        $('#enc_notes').typeahead({source:phraseArr});
	if($('#cas_code').length>0){
		var obj4 = $('#cas_code').typeahead({source:customarrayCasCode});
	}
	if($('#cas_code1').length>0){
		var obj4 = $('#cas_code1').typeahead({source:customarrayCasCode});
	}
	<?php 
		if(constant('CLAIM_STATUS_REQUEST')=='YES' && $encounter_id>0){
		//--CHECKING IF ANY CLAIM STATUS ENQUIRY/RESPONSE AVAILABLE
		$q_clStatus = "SELECT id FROM claim_status_enquiry WHERE patient_id='$patient_id' AND encounter_id='$encounter_id' AND del_status='0'";
		$res_clStatus = imw_query($q_clStatus);
		if($res_clStatus && imw_num_rows($res_clStatus)>0){$cl_status_btn_class = 'active';}else{$cl_status_btn_class = '';}
		?>
		top.$('#btn_clm_status').removeClass('active');
		top.$('#btn_clm_status').addClass('<?php echo $cl_status_btn_class;?>');
		top.$('#btn_clm_status').show();
	<?php }?>
</script>
<div class="acctotal">
    <ul>
    	<li><h2>Total Balance</h2><span id="ngt_balance"><?php echo numberFormat($ngt_balance,2,'yes'); ?></span></li>
    	<li style="display:none;" id="ngt_payment_td"><h2>Total Payment</h2><span id="ngt_payment"></span></li>
    	<li style="display:none;" id="ngt_adjustment_td"><h2>Total Adjustment</h2><span id="ngt_adjustment"></span></li>
    	<li style="display:none;" id="ngt_deduct_td"><h2>Total Deductible</h2><span id="ngt_deduct"></span></li>
    	<li style="display:none;" id="ngt_denied_td"><h2>Total Denied</h2><span id="ngt_denied"></span></li>
    </ul>
</div>


<?php
$login_facility=$_SESSION['login_facility'];
$operator_id=$_SESSION['authId'];
/* code for pos device in hidden*/
$cookieName="imedicwareposdevice_".$operator_id;
if(isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName]!='') {
    $poscookie=json_decode($_COOKIE[$cookieName],true);
    if(empty($poscookie)==false){
        if($poscookie['login_facility']==$login_facility) {
            if($poscookie['user_id']==$operator_id && $poscookie['expire_time']>time()) {
                if($poscookie['expire_time']<=time()) {
                    unset($_COOKIE[$cookieName]);
                } else {
                    $defaultDevice=$poscookie['device_id'];
                }
            }
        }
    }
}

$pos_device=false;
$devicesArr=array();
$devices_sql="Select *, tsys_device_details.id as d_id from tsys_device_details 
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id 
              WHERE device_status=0 
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0 
              ";
$resp = imw_query($devices_sql);
$devices_option = "";
$counter=0;
if ($resp && imw_num_rows($resp) > 0) {
    $pos_device=true;;
    while ($row = imw_fetch_assoc($resp)) {
        $counter++;
        $ipAddress=$row['ipAddress'];
        $port=$row['port'];
        $device_url=$phpHTTPProtocol.$ipAddress.':'.$port;
        $selected='';
        if(!$defaultDevice && $counter==1) {
            $selected='selected="selected" ';
        } else {
            $selected=($row['d_id']==$defaultDevice)?'selected="selected" ':'';
        }
        $devices_option .= "<option ".$selected." data-device_ip='".$ipAddress."' data-device_url='".$device_url."' value='" . $row['d_id'] . "'>" . $row['deviceName'] . "</option>";
    }
}

$laneID='10000005';
?>

<?php if($pos_device) { ?>
    <div class="clearfix"></div>
    <div class="hide <?php echo $cc_class;?>">
        <div class="col-sm-4">
            <div class="checkbox checkbox-inline form-inline">
                <input type="checkbox" name="cc_icon" id="cc_icon" class="cc_icon"  />
                <label for="cc_icon"><img src="<?php echo $GLOBALS['webroot'] ?>/library/images/pos_icon36x36.png" alt="CC Icon" width="32" height="32"/></label>
            </div>
        </div>
        <div class="col-sm-8">
            <select name="tsys_device_url" id="tsys_device_url" class="form-control minimal" onchange="setDefaultDevice(this);">
                <option value="no_pos_device">No POS</option>
                <?php echo $devices_option; ?>
            </select>
        </div>
        <input type="hidden" name="laneId" id="laneId" value="<?php echo $laneID;?>" />
        <input type="hidden" name="referenceNumber" id="referenceNumber" value="" />
        <input type="hidden" name="tsys_payment_type_log_id" id="tsys_payment_type_log_id" value="" />

        <input type="hidden" name="log_referenceNumber" id="log_referenceNumber" value="" />
        <input type="hidden" name="tsys_transaction_id" id="tsys_transaction_id" value="" />
        <input type="hidden" name="tsys_void_id" id="tsys_void_id" value="" />
        <input type="hidden" name="tsys_last_status" id="tsys_last_status" value="" />
        <input type="hidden" name="card_details_str_id" id="card_details_str_id" value="" />
    </div>

    <div id="div_loading_image" class="text-center" style="z-index:9999;display:none;">
        <div class="loading_container">
            <div class="process_loader"></div>
            <div id="div_loading_text" class="text-info"></div>
        </div>
    </div>
<?php } ?>
<script>
    var pos_device='<?php echo $pos_device; ?>';
    var pos_patient_id='<?php echo $patient_id;?>';
    var pos_encounter_id='<?php echo $encounter_id; ?>';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/jquery.base64.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/pos.js"></script>

<script>
function return_transaction(totalAmt) {
    var laneId=$('#laneId').val();
    var transactionType="02";
    var scheduID=0;
    var transactionNumber=false;
    var encounter_id=pos_encounter_id;

    var totalAmt=parseFloat(totalAmt);
    totalAmt=Math.round(totalAmt*100);

    var posMachine='PRESENT';
    /*Create referenceNumber using ajax Log table entry */
    createReferenceNumber(posMachine);
    var referenceNumber=$('#log_referenceNumber').val();
    if(!referenceNumber) {
        console.log('referenceNumber does not exists.');
        return false;
    }
    var returnid='';
    show_cc_loading_image('show','', 'Please Wait...');

    chargeAmount( totalAmt, laneId, scheduID, encounter_id, transactionType, transactionNumber, referenceNumber, returnid, 'acc_ret' );
}


</script>

<?php require_once("acc_footer.php");?>	
</body>
</html>