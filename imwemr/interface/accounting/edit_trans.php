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
if(strtolower($_REQUEST['trans_mode'])=="paid"){
	$show_mode="Payment";
}elseif(strtolower($_REQUEST['trans_mode'])=="debit credit"){
	$show_mode="Credit";
}else{
	$show_mode=ucfirst(str_replace('_',' ',$_REQUEST['trans_mode']));
}
$title = "Edit ".$show_mode; 
include("acc_header.php"); 
require_once("../../interface/common/assign_new_task.php");

$db_date=date('Y-m-d');
$db_time=date('H:i:s');
$db_date_time = date('Y-m-d H:i:s'); 
function changeDateYMD($dateStr){
	list($mmDate, $ddDate, $yyDate) = explode('-', $dateStr);
	$showDate = $yyDate.'-'.$mmDate.'-'.$ddDate;
	return $showDate;
}

function changeDateMDY($dateStr){
	list($yyDate, $mmDate, $ddDate) = explode('-', $dateStr);
	$showDate = $mmDate.'-'.$ddDate.'-'.$yyDate;
	return $showDate;
}

$patient_id = $_SESSION['patient'];
$operator_id = $_SESSION['authUserID'];
$edit_id = $_REQUEST['edit_id'];
$enc_id = $_REQUEST['enc_id'];
$trans_mode = strtolower($_REQUEST['trans_mode']);

if($_REQUEST['frm_submit']=="yes"){
	$paidBy=$_REQUEST['paidBy'];
	$trans_amount=str_replace('-','',$_REQUEST['trans_amount']);
	$old_trans_amount=$_REQUEST['old_trans_amount'];
	$paymentMode=$_REQUEST['paymentMode'];
	$checkNo=$creditCardNo=$creditCardCo=$expirationDate="";
	if($paymentMode=="Check" || $paymentMode=="Money Order" || $paymentMode=="VEEP" || $paymentMode=="EFT" || stripos($paymentMode,'Check')>0){
		$checkNo=$_REQUEST['checkNo'];
	}
	if($paymentMode=="Credit Card"){
		$creditCardNo=$_REQUEST['cCNo'];
		$creditCardCo=$_REQUEST['creditCardCo'];
		$expirationDate=$_REQUEST['expireDate'];
	}
	if($paidBy=="Insurance"){
		$insProviderId=$_REQUEST['insProviderName'];
		$insCompany=$_REQUEST['insSelected'];
	}
	$date_of_payment = changeDateYMD($_REQUEST['paidDate']);
	$extra_id=$_REQUEST['extra_id'];
	$write_off_code=$_REQUEST['write_off_code'];
	$adj_code=$_REQUEST['adj_code'];
	$credit_note=$_REQUEST['credit_note'];
	$billing_facility_id=$_REQUEST['billing_facility_id'];
	if($trans_mode=="paid" || $trans_mode=="deposit" || $trans_mode=="negative payment" || $trans_mode=="interest payment" || $trans_mode=="copay_payment"){
		
		$data_qry = imw_query("select pcpi.insProviderId,pcpi.payment_mode,pcpi.checkNo,pcpi.creditCardNo,pcpi.creditCardCo,
		pcpi.expirationDate,pcpi.paymentClaims,pcpi.facility_id,pcpid.charge_list_detail_id,pcpid.paidBy,pcpid.paidForProc,
		pcpid.overPayment,pcpid.entered_date,pcpid.paidDate,pcpid.paid_time,pcpid.operator_id,pcpid.modified_date,pcpid.modified_by
		from patient_chargesheet_payment_info pcpi join patient_charges_detail_payment_info pcpid on pcpid.payment_id = pcpi.payment_id
		where pcpi.encounter_id ='$enc_id' and pcpid.deletePayment ='0' and pcpid.payment_details_id='$edit_id'");
		$data_row = imw_fetch_array($data_qry);
		
		imw_query("insert into transaction_modify set master_tbl_id='$edit_id',patient_id='$patient_id',encounter_id='$enc_id',charge_list_id='".$_REQUEST['chl_id']."',charge_list_detail_id='".$data_row['charge_list_detail_id']."',
		trans_by='".$data_row['paidBy']."',trans_ins_id='".$data_row['insProviderId']."',trans_method='".$data_row['payment_mode']."',check_number='".$data_row['checkNo']."',cc_type='".$data_row['creditCardCo']."',cc_number='".$data_row['creditCardNo']."',
		cc_exp_date='".$data_row['expirationDate']."',trans_type='".$data_row['paymentClaims']."',trans_amount='".($data_row['paidForProc']+$data_row['overPayment'])."',trans_dot='".$data_row['entered_date']."',
		trans_dot_time='',trans_dop='".$data_row['paidDate']."',trans_dop_time='".$data_row['paid_time']."',trans_operator_id='".$data_row['operator_id']."',
		modified_date='".$data_row['modified_date']."',modified_by='".$data_row['modified_by']."',facility_id='".$data_row['facility_id']."'");
		
		if($trans_mode=="copay_payment" && $trans_amount>0){
			if($trans_amount>$_POST['old_trans_amount']){
				$new_copay_trans_amt=$trans_amount-$_POST['old_trans_amount'];
				if(($_POST['tot_copay_paid']+$new_copay_trans_amt)>$_POST['proc_copay']){
					$trans_amount=$_POST['old_trans_amount']+($_POST['proc_copay']-$_POST['tot_copay_paid']);
					if($trans_amount<=0){
						$trans_amount=$_POST['old_trans_amount'];
					}
				}
			}
		}

		imw_query("update patient_charges_detail_payment_info set overPayment='0',paidForProc='$trans_amount',paidBy='$paidBy',modified_date='$db_date',modified_by='$operator_id',paidDate='$date_of_payment' where payment_details_id='$edit_id'");
		imw_query("update patient_chargesheet_payment_info set paid_by='$paidBy',payment_mode='$paymentMode',checkNo='$checkNo',creditCardNo='$creditCardNo',creditCardCo='$creditCardCo',expirationDate='$expirationDate',payment_amount='$trans_amount',date_of_payment='$date_of_payment',insProviderId='$insProviderId',insCompany='$insCompany',facility_id='$billing_facility_id' where payment_id='$extra_id'");
		if($trans_mode=="copay_payment"){
			$copayPaid=1;
			if($trans_amount<$_REQUEST['proc_copay']){
				$copayPaid=0;
			}
			imw_query("update patient_charge_list set copayPaid='$copayPaid' where encounter_id='$enc_id' and del_status='0'");
			
			if($_POST['charge_list_detail_id']!=$_POST['proc_name'] && $_POST['proc_name']>0){
				imw_query("update patient_charge_list_details set coPayAdjustedAmount='0' where charge_list_detail_id='".$_POST['charge_list_detail_id']."'");
				imw_query("update patient_charge_list_details set coPayAdjustedAmount='1' where charge_list_detail_id='".$_POST['proc_name']."'");
				imw_query("UPDATE report_enc_trans SET charge_list_detail_id='".$_POST['proc_name']."' WHERE master_tbl_id = '$edit_id' and trans_type='copay-paid'");
			}
		}
		
		$getPaymentDelStr = imw_query("SELECT charge_list_detail_id FROM patient_charges_detail_payment_info WHERE payment_details_id='$edit_id'");
		$getPaymentDelRow = imw_fetch_array($getPaymentDelStr);
		$task_chld=$getPaymentDelRow['charge_list_detail_id'];
		
		$sel_chl=imw_query("select patient_charge_list.date_of_service,patient_data.lname,patient_data.fname,cpt_fee_tbl.cpt_prac_code
		from patient_charge_list_details join patient_charge_list on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
		join patient_data on patient_data.id=patient_charge_list.patient_id 
		join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id=patient_charge_list_details.procCode
		where patient_charge_list_details.charge_list_detail_id='$task_chld' limit 0,1");
		$row_chl=imw_fetch_array($sel_chl);
		
		$task_insert_arr=array();
		$task_insert_arr['patientid']=$patient_id;
		$task_insert_arr['operatorid']=$operator_id;
		$task_insert_arr['section']='payment_deleted_edited';
		$task_insert_arr['status_id']='';
		$task_insert_arr['encounter_id']=$enc_id;
		$task_insert_arr['date_of_service']=$row_chl['date_of_service'];
		$task_insert_arr['cpt_code']=$row_chl['cpt_prac_code'];
		$task_insert_arr['patient_name']=$row_chl['lname'].', '.$row_chl['fname'];
		assign_acc_task_rules_to($task_insert_arr);
	}elseif($trans_mode=="discount" || $trans_mode=="write off" || $trans_mode=="write_off_copay"){
		imw_query("update paymentswriteoff set write_off_amount='$trans_amount',write_off_date='$date_of_payment',write_off_by_id='$insProviderId',write_off_code_id='$write_off_code',modified_date='$db_date_time',modified_by='$operator_id',facility_id='$billing_facility_id' where write_off_id='$edit_id'");
		if($trans_mode=="discount"){
			re_calculate_tax($enc_id);
		}
	}elseif($trans_mode=="denied"){
		imw_query("update deniedpayment set deniedAmount='$trans_amount',deniedDate='$date_of_payment',deniedBy='$paidBy',deniedById='$insProviderId',modified_date='$db_date_time',modified_by='$operator_id',facility_id='$billing_facility_id' where deniedId='$edit_id'");
	}elseif($trans_mode=="deductible"){
		imw_query("update payment_deductible set deduct_amount='$trans_amount',deduct_date='$date_of_payment',deductible_by='$paidBy',deduct_ins_id='$insProviderId',modified_date='$db_date_time',modified_by='$operator_id',facility_id='$billing_facility_id' where deductible_id='$edit_id'");
	}elseif($trans_mode=="adjustment" || $trans_mode=="over adjustment" || $trans_mode=="returned check" || $trans_mode=="co-insurance" || $trans_mode=="co-payment"){
		if($trans_mode!="returned check"){
			$up_more_cond=",payment_method='$paymentMode',check_number='$checkNo',cc_number='$creditCardNo',cc_type='$creditCardCo',cc_exp_date='$expirationDate',payment_code_id='$adj_code'";
		}
		imw_query("update account_payments set payment_amount='$trans_amount',payment_date='$date_of_payment',payment_by='$paidBy',ins_id='$insProviderId',modified_date='$db_date_time',modified_by='$operator_id',facility_id='$billing_facility_id' $up_more_cond where id='$edit_id'");
	}elseif($trans_mode=="refund" || $trans_mode=="debit credit"){
		imw_query("update creditapplied set amountApplied='$trans_amount',dateApplied='$date_of_payment',type='$paidBy',ins_case='$insProviderId',insCompany='$insCompany',payment_mode='$paymentMode',checkCcNumber='$checkNo',creditCardNo='$creditCardNo',creditCardCo='$creditCardCo',expirationDateCc='$expirationDate',credit_note='$credit_note',modified_date='$db_date_time',modified_by='$operator_id',facility_id='$billing_facility_id' where crAppId='$edit_id'");
		if($trans_mode=="debit credit" && $extra_id>0){
			set_payment_trans($extra_id);
		}
	}
	set_payment_trans($enc_id);
?>
	<script type="text/javascript">
		var eId = '<?php echo $enc_id; ?>';
		window.opener.top.fmain.location.href="makePayment.php?encounter_id="+eId;
		window.close();
	</script>
<?php

}

$qry = "select * from patient_data where pid = '$patient_id'";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$patient_name=ucwords(trim($row['lname'].", ".$row['fname']." ".$row['mname']));
if($trans_mode=="paid" || $trans_mode=="deposit" || $trans_mode=="negative payment" || $trans_mode=="interest payment" || $trans_mode=="copay_payment"){
	$data_qry = "select pcpid.paidForProc,pcpid.overPayment,pcpid.charge_list_detail_id,pcpid.paidDate,pcpi.facility_id,
			pcpi.paid_by,pcpi.insProviderId,pcpi.payment_mode,pcpi.checkNo,pcpi.creditCardNo,pcpi.creditCardCo,pcpi.expirationDate,pcpi.payment_id
			from patient_chargesheet_payment_info pcpi join
			patient_charges_detail_payment_info pcpid on pcpid.payment_id = pcpi.payment_id
			where pcpi.encounter_id ='$enc_id' and pcpid.deletePayment ='0' and pcpid.payment_details_id='$edit_id'";
	$data_res = imw_query($data_qry);
	$data_row = imw_fetch_array($data_res);
	$charge_list_detail_id=$data_row['charge_list_detail_id'];
	$trans_amt=$data_row['paidForProc']+$data_row['overPayment'];
	$paidDate=$data_row['paidDate'];
	$paidBy=$data_row['paid_by'];
	$insProviderId=$data_row['insProviderId'];
	$payment_mode=$data_row['payment_mode'];
	$checkNo=$data_row['checkNo'];
	$creditCardNo=$data_row['creditCardNo'];
	$creditCardCo=$data_row['creditCardCo'];
	$expirationDate=$data_row['expirationDate'];
	$extra_id=$data_row['payment_id'];
	$billing_facility_id=$data_row['facility_id'];
}elseif($trans_mode=="discount" || $trans_mode=="write off" || $trans_mode=="write_off_copay"){
	$data_qry = "select * from paymentswriteoff where encounter_id ='$enc_id' and write_off_id='$edit_id'";
	$data_res = imw_query($data_qry);
	$data_row = imw_fetch_array($data_res);
	$charge_list_detail_id=$data_row['charge_list_detail_id'];
	$trans_amt=$data_row['write_off_amount'];
	$paidDate=$data_row['write_off_date'];
	if($data_row['write_off_by_id']>0){
		$paidBy='Insurance';
	}else{
		$paidBy='Patient';
	}
	$insProviderId=$data_row['write_off_by_id'];
	$write_off_code_id=$data_row['write_off_code_id'];
	$billing_facility_id=$data_row['facility_id'];
}elseif($trans_mode=="denied"){
	$data_qry = "select * from deniedpayment where encounter_id ='$enc_id' and deniedId='$edit_id'";
	$data_res = imw_query($data_qry);
	$data_row = imw_fetch_array($data_res);
	$charge_list_detail_id=$data_row['charge_list_detail_id'];
	$trans_amt=$data_row['deniedAmount'];
	$paidDate=$data_row['deniedDate'];
	$paidBy=$data_row['deniedBy'];
	$insProviderId=$data_row['deniedById'];
	$billing_facility_id=$data_row['facility_id'];
}elseif($trans_mode=="deductible"){
	$data_qry = "select * from payment_deductible where deductible_id='$edit_id'";
	$data_res = imw_query($data_qry);
	$data_row = imw_fetch_array($data_res);
	$charge_list_detail_id=$data_row['charge_list_detail_id'];
	$trans_amt=$data_row['deduct_amount'];
	$paidDate=$data_row['deduct_date'];
	$paidBy=$data_row['deductible_by'];
	$insProviderId=$data_row['deduct_ins_id'];
	$billing_facility_id=$data_row['facility_id'];
}elseif($trans_mode=="adjustment" || $trans_mode=="over adjustment" || $trans_mode=="returned check" || $trans_mode=="returned check(copay)" || $trans_mode=="co-insurance" || $trans_mode=="co-payment"){
	$data_qry = "select * from account_payments where encounter_id ='$enc_id' and id='$edit_id'";
	$data_res = imw_query($data_qry);
	$data_row = imw_fetch_array($data_res);
	$charge_list_detail_id=$data_row['charge_list_detail_id'];
	$trans_amt=$data_row['payment_amount'];
	$paidDate=$data_row['payment_date'];
	$paidBy=$data_row['payment_by'];
	$insProviderId=$data_row['ins_id'];
	$payment_mode=$data_row['payment_method'];
	$checkNo=$data_row['check_number'];
	$creditCardNo=$data_row['cc_number'];
	$creditCardCo=$data_row['cc_type'];
	$expirationDate=$data_row['cc_exp_date'];
	$payment_code_id=$data_row['payment_code_id'];
	$billing_facility_id=$data_row['facility_id'];
}elseif($trans_mode=="refund" || $trans_mode=="debit credit"){
	if($trans_mode=="debit credit"){
		$crt_whr=" and crAppliedToEncId_adjust ='$enc_id'";
	}else{
		$crt_whr=" and crAppliedToEncId ='$enc_id'";
	}
	$data_qry = "select * from creditapplied where crAppId='$edit_id' $crt_whr";
	$data_res = imw_query($data_qry);
	$data_row = imw_fetch_array($data_res);
	if($trans_mode=="debit credit"){
		$charge_list_detail_id=$data_row['charge_list_detail_id_adjust'];
	}else{
		$charge_list_detail_id=$data_row['charge_list_detail_id'];
	}
	$trans_amt=$data_row['amountApplied'];
	$paidDate=$data_row['dateApplied'];
	$paidBy=$data_row['type'];
	$insProviderId=$data_row['ins_case'];
	$payment_mode=$data_row['payment_mode'];
	$checkNo=$data_row['checkCcNumber'];
	$creditCardNo=$data_row['creditCardNo'];
	$creditCardCo=$data_row['creditCardCo'];
	$expirationDate=$data_row['expirationDateCc'];
	$credit_note=$data_row['credit_note'];
	$extra_id=$data_row['crAppliedToEncId'];
	$billing_facility_id=$data_row['facility_id'];
}
if($trans_mode=="copay_payment"){
	$chld_whr=" and patient_charge_list_details.coPayAdjustedAmount='1'";
}else{
	$chld_whr=" and patient_charge_list_details.charge_list_detail_id='$charge_list_detail_id'";
}
$chl_qry="select patient_charge_list_details.procCode,patient_charge_list_details.totalAmount,patient_charge_list_details.approvedAmt,patient_charge_list_details.charge_list_detail_id,
 		  patient_charge_list_details.paidForProc,patient_charge_list_details.newBalance,patient_charge_list.copay,patient_charge_list.charge_list_id,
		  patient_charge_list.primaryInsuranceCoId,patient_charge_list.secondaryInsuranceCoId,patient_charge_list.tertiaryInsuranceCoId
		  from patient_charge_list join patient_charge_list_details 
		  on patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id
		  where patient_charge_list.encounter_id='$enc_id' $chld_whr";
$chl_res = imw_query($chl_qry);
$chl_row = imw_fetch_array($chl_res);
$procCode=$chl_row['procCode'];
$totalAmount=$chl_row['totalAmount'];
$approvedAmt=$chl_row['approvedAmt'];
$paidForProc=$chl_row['paidForProc'];
$newBalance=$chl_row['newBalance'];
$primaryInsProviderId = $chl_row['primaryInsuranceCoId'];
$secondaryInsProviderId = $chl_row['secondaryInsuranceCoId'];
$tertiaryInsProviderId = $chl_row['tertiaryInsuranceCoId'];
$charge_list_id = $chl_row['charge_list_id'];

if($trans_mode=="copay_payment"){
	$charge_list_detail_id=$chl_row['charge_list_detail_id'];
	if($charge_list_id==0){
		$copay_chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$enc_id'");
		$copay_chl_row = imw_fetch_array($copay_chl_qry);
		$charge_list_id = $copay_chl_row['charge_list_id'];
	}
	$sel_proc_qry=imw_query("select charge_list_detail_id,procCode,diagnosis_id1,diagnosis_id2,diagnosis_id3,diagnosis_id4,diagnosis_id5,diagnosis_id6,
							diagnosis_id7,diagnosis_id8,diagnosis_id9,diagnosis_id10,diagnosis_id11,diagnosis_id12,modifier_id1,modifier_id2,modifier_id3 
		 					from patient_charge_list_details where charge_list_id ='$charge_list_id' and charge_list_id>0 and del_status='0' ORDER BY display_order,charge_list_detail_id ASC");
	while($sel_proc_row = imw_fetch_array($sel_proc_qry)){
		$chld_proc_id_arr[$sel_proc_row['procCode']]=$sel_proc_row['charge_list_detail_id'];
		$chld_id_proc_arr[$sel_proc_row['charge_list_detail_id']]=$sel_proc_row['procCode'];
		$chld_copay_proc_arr[$sel_proc_row['charge_list_detail_id']]=$sel_proc_row;
	}
	$procCode = implode(',',$chld_id_proc_arr);
	
	$sql = imw_query("select pcdpi.charge_list_detail_id,pcdpi.deletePayment,pcpi.paymentClaims,pcdpi.payment_details_id,pcdpi.paidForProc from patient_chargesheet_payment_info pcpi join patient_charges_detail_payment_info pcdpi
			 on pcpi.payment_id=pcdpi.payment_id WHERE pcpi.encounter_id='".$enc_id."' and pcdpi.charge_list_detail_id='0' and pcdpi.deletePayment='0'");
	while($row=imw_fetch_array($sql)){
		if($row['paymentClaims']=="Negative Payment"){
			$enc_copay_paid_arr[$enc_id][$row['payment_details_id']]=-$row['paidForProc'];
		}else{
			$enc_copay_paid_arr[$enc_id][$row['payment_details_id']]=$row['paidForProc'];
		}
	}
	
}

$cpt_qry=imw_query("select cpt_fee_id,cpt4_code,cpt_desc,cpt_prac_code from cpt_fee_tbl where cpt_fee_id in($procCode) order by cpt4_code,cpt_desc asc");
while($cpt_row = imw_fetch_array($cpt_qry)){
	//$cpt_code_id_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt_fee_id'];
	//$cpt_code_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt4_code'];
	$cpt_prac_code_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt_prac_code'];
	$cpt_desc_arr[$cpt_row['cpt_fee_id']]=$cpt_row['cpt_desc'];
}

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

$ins_comp_name_arr[]=array();	
$ins_comp_ihc_arr[]=array();
$getPrimaryInsCoNameStr = "SELECT * FROM insurance_companies WHERE id = '$primaryInsProviderId' or id = '$secondaryInsProviderId' or id = '$tertiaryInsProviderId' or in_house_code='unknown' or name='unknown'";
$getPrimaryInsCoNameQry = imw_query($getPrimaryInsCoNameStr);
while($getPrimaryInsCoNameRow = imw_fetch_array($getPrimaryInsCoNameQry)){
	if($primaryInsProviderId==$getPrimaryInsCoNameRow['id'] && $primaryInsProviderId>0){
		$primaryInsCoId = $getPrimaryInsCoNameRow['id'];
		$primaryInsCoName = $getPrimaryInsCoNameRow['in_house_code'];
		if($primaryInsCoName==""){
			$primaryInsCoName = $getPrimaryInsCoNameRow['name'];
		}
	}
	if($secondaryInsProviderId==$getPrimaryInsCoNameRow['id'] && $secondaryInsProviderId>0){
		$secondaryInsCoId = $getPrimaryInsCoNameRow['id'];
		$secondaryInsCoName = $getPrimaryInsCoNameRow['in_house_code'];
		if($secondaryInsCoName==""){
			$secondaryInsCoName = $getPrimaryInsCoNameRow['name'];
		}	
	}
	if($tertiaryInsProviderId==$getPrimaryInsCoNameRow['id'] && $tertiaryInsProviderId>0){
		$tertiaryInsCoId = $getPrimaryInsCoNameRow['id'];
		$tertiaryInsCoName = $getPrimaryInsCoNameRow['in_house_code'];
		if($tertiaryInsCoName==""){
			$tertiaryInsCoName = $getPrimaryInsCoNameRow['name'];
		}
	}
	if(strtolower($getPrimaryInsCoNameRow['in_house_code'])=="unknown" or strtolower($getPrimaryInsCoNameRow['name'])=="unknown"){
		$unknown_ins_id=$getPrimaryInsCoNameRow['id'];
		$unknown_ins_name = $getPrimaryInsCoNameRow['in_house_code'];
		if($unknown_ins_name==""){
			$unknown_ins_name = $getPrimaryInsCoNameRow['name'];
		}
	}
	
	$insProvidersIdArr = array($primaryInsCoId, $secondaryInsCoId, $tertiaryInsCoId,$unknown_ins_id);
	$insProvidersNameArr = array($primaryInsCoName, $secondaryInsCoName, $tertiaryInsCoName, $unknown_ins_name);
}

$qry = imw_query("select pm_id,pm_name from payment_methods where del_status='0' order by default_method desc, pm_name");
while($row = imw_fetch_array($qry)){ 
	$payment_method_arr[$row['pm_id']]=$row['pm_name'];
}

$qry = imw_query("select modifiers_id,mod_prac_code from modifiers_tbl where delete_status='0' order by modifiers_id");
while($row = imw_fetch_array($qry)){ 
	$modifiers_data_arr[$row['modifiers_id']]=$row['mod_prac_code'];
}
?>
<script>
function tot_pay_chg(){
	$('#paidAmountNow').val($('#trans_amount').val());
}
function chk_amt(){
	var msg = "Please Enter following information\n";
	var flag = 0;
	var paymentMethod = $("#paymentMode").val();
	var paidBy = $('#paidBy').val();
	if($("#paymentMode").length>0){
		var f1 = $("#paymentMode").val();
	}
	
	if($("#paymentClaims").length>0){
		var paymentClaim = $("#paymentClaims").val();
	}
	if(paidBy=='Insurance'){
		if($("#insProviderCoId").length>0){
			if($("#insProviderCoId").val()==''){
				alert("Please select insurance company, N/A is allowed.")
				return false;
			}
		}
		if($("#insProviderCoId").length>0){
			var insCoIS = $("#insProviderCoId option:selected").index();
			$("#insSelected").val(insCoIS);
		}
	}
	if(f1=="Cash"){
		if($("#paidBy").length>0)
			var paidBy = $("#paidBy").val();
		if($("#date1").length>0)
			var date1 = $("#date1").val();
		if($("#paidAmountId").length>0)
			var paidAmt = $("#paidAmountId").val();

		if(date1==""){ msg+="-Paid Date\n";	++flag; }
		if(paidBy==""){ msg+="-Paid By\n";	++flag; }
		if(flag>0){
			alert(msg)
			return false;
		}

	}
	if(paymentClaim == 'Paid' || paymentClaim == 'Deposit' || paymentClaim == "Interest Payment" || paymentClaim == "Refund"){
		if(f1=="Check" || f1=="EFT" || f1=="Money Order" || f1=="VEEP"){		
			if($("#checkNoId").length>0)
				var chkNo = $("#checkNoId").val();
			if($("#date1").length>0)
				var date1 = $("#date1").val();
			if($("#paidBy").length>0)
				var paidBy = $("#paidBy").val();
			if($("#paidAmountId".length>0))
				var paidAmt = $("#paidAmountId").val();
	
			if(chkNo==""){ msg+="-Check No\n";	++flag; }
			if(date1==""){ msg+="-Paid Date\n";	++flag; }
			if(paidBy==""){ msg+="-Paid By\n";	++flag; }
			
			if(flag>0){
				alert(msg)
				return false;
			}
		}
	}
	
	if(f1=="Credit Card"){
		if(paymentClaim != 'Debit_Credit' && paymentClaim != 'Refund'){
			var creditCNo = $("#cCNoId").val();
			var creditCardCo = $("#creditCardCoId").val();
			var date1 = $("#date1").val();
			var expDate = $("#date2").val();
			var paidBy = $("#paidBy").val();
			var paidAmt = $("#paidAmountId").val();
	
			if(creditCardCo==""){ msg+="-Credit Card Type \n"; ++flag; }
			if(creditCNo==""){ msg+="-Credit Card # \n"; ++flag; }
			//if(expDate==""){ msg+="-Credit Card Exp. Date \n"; ++flag; }
			if(paidBy==""){ msg+="-Paid By\n"; ++flag; }
			if(date1==""){ msg+="-Paid Date\n"; ++flag; }
			if(flag>0){
				alert(msg)
				return false;
			}				
		}
	
	}
	document.editPaymentFrm.submit();
}

$(function(){
	$('.date-pick2').datetimepicker({
		timepicker:false,
		format:'<?php echo phpDateFormat(); ?>',
		formatDate:'Y-m-d',
		scrollInput:false
	});
});
</script>
<div class="purple_bar">
	<div class="row">
		<div class="col-sm-4">
			<label><?php echo $show_mode; ?> Transaction</label>
		</div>
		<div class="col-sm-5">
			<label>Patient Name :</label><span> <?php echo $patient_name.' ('.$patient_id.')'; ?></span>
		</div>
		
		<div class="col-sm-3">
			<label>EId :</label><span> <?php echo $enc_id; ?></span>	
		</div>
	</div>	
</div>
<form name="editPaymentFrm" method="post" action="edit_trans.php">
	<input type="hidden" id="chl_id" name="chl_id" value="<?php echo $charge_list_id; ?>">
	<input type="hidden" id="enc_id" name="enc_id" value="<?php echo $enc_id; ?>">
	<input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>">
	<input type="hidden" id="trans_mode" name="trans_mode" value="<?php echo $trans_mode; ?>">
	<input type="hidden" id="extra_id" name="extra_id" value="<?php echo $extra_id; ?>">
	<input type="hidden" id="proc_copay" name="proc_copay" value="<?php echo $chl_row['copay']; ?>">
    <input type="hidden" id="tot_copay_paid" name="tot_copay_paid" value="<?php echo array_sum($enc_copay_paid_arr[$enc_id]); ?>">
	<input type="hidden" id="frm_submit" name="frm_submit" value="yes">
	<div class="table-responsive" style="margin:0px; height:340px; overflow-x:auto; width:100%;">
		<table class="table table-bordered">
			<tr class="grythead">
				<th>CPT - Description<?php if($trans_mode=="copay_payment"){echo " - Mod/Dx";}?></th>
				<th class="text-nowrap">Total Charges</th>
				<th>Allowed</th>
				<th class="text-nowrap">Total Paid</th>
				<th class="text-nowrap" style="width:150px;"><?php echo $show_mode; ?> Amount</th>
				<th>Balance</th>
			</tr>
			<tr class="text-center">
				<td>
					<input type="hidden" name="charge_list_detail_id" id="charge_list_detail_id" value="<?php echo $charge_list_detail_id; ?>">
                    <?php
						if($trans_mode=="copay_payment"){
					?>
                        <select name="proc_name" id="proc_name" class="form-control minimal" style="width:380px;">
                            <option value="">CPT Code</option>
                            <?php
                            	foreach($chld_id_proc_arr as $key => $val){
									$show_mod_arr=$show_dx_code_arr=array();
									$show_mod_str=$show_dx_code_str="";
									for($j=1;$j<=3;$j++){
										if($chld_copay_proc_arr[$key]['modifier_id'.$j]>0){
											$show_mod_arr[]=$modifiers_data_arr[$chld_copay_proc_arr[$key]['modifier_id'.$j]];
										}
									}
									for($j=1;$j<=12;$j++){
										if($chld_copay_proc_arr[$key]['diagnosis_id'.$j]!=""){
											$show_dx_code_arr[]=$chld_copay_proc_arr[$key]['diagnosis_id'.$j];
										}
									}
								$show_mod_str=implode(', ',$show_mod_arr);
								if(count($show_mod_arr)>0){
									$show_dx_code_str="/";
								}	
								$show_dx_code_str.=implode(', ',$show_dx_code_arr);
								
							?>
                            	<option value="<?php echo $key; ?>" <?php if($key==$charge_list_detail_id) echo 'SELECTED'; ?>><?php echo $cpt_prac_code_arr[$val].' - '.$cpt_desc_arr[$val].' - '.$show_mod_str.$show_dx_code_str; ?></option>
                            <?php	
                            }
                            ?>
                        </select>
                    <?php		
						}else{
							echo $cpt_prac_code_arr[$chl_row['procCode']].' - '.$cpt_desc_arr[$chl_row['procCode']]; 
						}
					?>
				</td>
				<td>
					<?php echo '$'.number_format($totalAmount, 2); ?>
					<input type="hidden" name="total_amount" id="total_amount" value="<?php echo $totalAmount; ?>">
				</td>
				<td>
				  <?php echo '$'.number_format($approvedAmt, 2); ?>
				  <input type="hidden" name="approved_amt" id="approved_amt" value="<?php echo $approvedAmt; ?>">							  
				</td>
				<td>
					<?php echo '$'.number_format($paidForProc, 2); ?>
					<input type="hidden" name="total_paid" id="total_paid" value="<?php echo $paidForProc; ?>">
				</td>
				<td>
					<div class="input-group">
						<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
						<input type="text" name="trans_amount" id="trans_amount" class="form-control" value="<?php echo $trans_amt; ?>" onChange="tot_pay_chg();">
					</div>
					<input type="hidden" name="old_trans_amount" id="old_trans_amount" value="<?php echo $trans_amt; ?>">
				</td>
				<td>
					<?php echo '$'.number_format($newBalance, 2); ?>
					<input type="hidden" name="current_balance" id="current_balance" value="<?php echo $newBalance; ?>">
				</td>
			</tr>
		</table>
	</div>
	
	<div class="row">
		<div class="col-sm-2">
			<label for="paidAmountNow">Amount</label>
			<div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span></div>
				<input type="text" name="paidAmountNow" id="paidAmountNow" readonly value="<?php echo number_format($trans_amt, 2); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-sm-2">
			<label for="date1"><?php echo $show_mode; ?> Date</label>
			<div class="input-group">
				<input type="text"  name="paidDate" id="date1" value="<?php echo date(phpDateFormat(), strtotime(str_replace('-', '/', $paidDate))); ?>" onBlur="checkdate(this);" class="form-control date-pick2" />
				<div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
			</div>
		</div>
        <div class="col-sm-2">
			<label for="billing_facility_id">Pay Location</label>
			<select name="billing_facility_id" id="billing_facility_id" class="selectpicker" data-width="100%">
                <option value="">Pay Location</option>
                <?php
                    foreach($fac_data_arr as $fac_key=>$fac_val){
                        $FacilityDetails=$fac_data_arr[$fac_key];				
                        $id = $FacilityDetails['id'];
                        $sel = $billing_facility_id == $id ? 'selected="selected"': '';
                        print '<option '.$sel.' value="'.$id.'">'.$FacilityDetails['name'].'</option>';
                    }
                ?>
            </select>
		</div>
		<div class="col-sm-2">
			<label for="paymentClaims">Trans Type</label>
			<select name="paymentClaims" id="paymentClaims" class="selectpicker" data-width="100%" disabled>
				<option value="Paid" <?php if($trans_mode=="paid") echo 'selected'; ?>>Paid</option>
				<option value="Deposit" <?php if($trans_mode=="deposit") echo 'selected'; ?>>Deposit</option>
				<option value="Discount" <?php if($trans_mode=="discount") echo 'selected'; ?>>Discount</option>
				<option value="Denied" <?php if($trans_mode=="denied") echo 'selected'; ?>>Denied</option>
				<option value="Deductible" <?php if($trans_mode=="deductible") echo 'selected'; ?>>Deductible</option>
				<option value="Write Off" <?php if($trans_mode=="write off") echo 'selected'; ?>>Write-Off</option>
				<option value="Adjustment" <?php if($trans_mode=="adjustment") echo 'selected'; ?>>Adjustment</option>
				<option value="Over Adjustment" <?php if($trans_mode=="over adjustment") echo 'selected'; ?>>Over Adjustment</option>
				<option value="Interest Payment" <?php if($trans_mode=="interest payment") echo 'selected'; ?>>Interest Payment</option>
				<option value="Returned Check" <?php if($trans_mode=="returned check") echo 'selected'; ?>>Returned Check</option>
				<option value="Refund" <?php if($trans_mode=="refund") echo 'selected'; ?>>Refund</option>
				<option value="Debit_Credit" <?php if($trans_mode=="debit credit") echo 'selected'; ?>>Debit/Credit (Adj)</option>
				<option value="Negative Payment" <?php if($trans_mode=="negative payment") echo 'selected'; ?>>Negative Payment</option>
                <option value="Co-Insurance" <?php if($trans_mode=="co-insurance") echo 'selected'; ?>>Co-Insurance</option>
                <option value="Co-Payment" <?php if($trans_mode=="co-payment") echo 'selected'; ?>>Co-Payment</option>
			</select>
		</div>
		<div class="col-sm-2" id="who_paid_td">
			<label for="paidBy">Who Paid</label>
			<input type="hidden" name="insSelected" id="insSelected" value="">
			<select name="paidBy" id="paidBy" class="selectpicker" data-width="100%" onChange="return paymentModeFn();">
				<option value="Patient" <?php if($paidBy=="Patient") echo 'SELECTED'; ?>>Patient</option>
				<option value="Res. Party" <?php if($paidBy=="Res. Party") echo 'SELECTED'; ?>>Res. Party</option>
				<?php
				if(($primaryInsProviderId) || ($secondaryInsProviderId) || ($tertiaryInsProviderId)){
					?>
					<option value="Insurance" <?php if($paidBy=="Insurance") echo 'SELECTED'; ?>>Insurance</option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="col-sm-2" id="insCoNames" style="display:<?php if($paidBy=="Insurance"){ echo 'block'; }else{ echo 'none'; } ?>;">
			<?php
				if(count($insProvidersNameArr)>0){
			?>
			<label for="insProviderCoId">Ins. Pr.</label>
			<select name="insProviderName" id="insProviderCoId" class="selectpicker" data-width="100%">
				<option value=""></option>
				<?php
				foreach($insProvidersNameArr as $id => $insCoName){
					foreach($insProvidersIdArr as $key => $insCoId){
						if($id==$key){
							if($insCoName==''){ $insCoName = 'N/A';  }
							$lenInsCoName = strlen($insCoName);
							if($lenInsCoName>10){
								$insCoName = substr($insCoName, 0, 10)."..";
							}
						?>
							<option value="<?php echo $insCoId; ?>" <?php if($insCoId==$insProviderId) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
						<?php
						}
					}
				}
				?>
			</select>
		<?php } ?>
		</div>
	</div>
	
	<div class="row pt10">
		<?php if($trans_mode!="discount" && $trans_mode!="write off" && $trans_mode!="denied" && $trans_mode!="deductible" && $trans_mode!="returned check" && $trans_mode!="co-insurance" && $trans_mode!="co-payment"){?>
			<div id="changeMethod">
				<div class="col-sm-2">
					<label for="paymentMode">Method</label>
					<div id="pay_all_meth">
						<select name="paymentMode" id="paymentMode" onChange="return showRow(this.value);" class="selectpicker" data-width="100%">
							<?php foreach($payment_method_arr as $method_key=>$method_val){?>
								<option value="<?php echo $method_val; ?>" <?php if(strtolower($payment_mode)==strtolower($method_val)) echo 'SELECTED'; ?>><?php echo $method_val; ?></option>
							<?php }?>
						</select>
					</div>
				</div>
			
				<div class="col-sm-2" id="checkRow" style="display:<?php if($payment_mode=="Check" || $payment_mode=="EFT" || $payment_mode=="Money Order" || $payment_mode=="VEEP" || stripos($payment_mode,'Check')>0){ echo 'block'; }else{ echo 'none'; } ?>;">
					<label for="checkNoId">Check #</label>
					<input type="text" name="checkNo" id="checkNoId" class="form-control" value="<?php echo $checkNo; ?>">
				</div>
			</div>	
			 
			<div class="col-sm-6" id="creditCardRow" style="display:<?php if($payment_mode=="Credit Card"){ echo 'block'; }else{ echo 'none'; } ?>;">
				<div class="row">
					<div id="creditCardCoTd" class="col-sm-5">
						<label for="creditCardCoId">CC Type</label>
						<select name="creditCardCo" id="creditCardCoId" class="selectpicker" data-width="100%" onChange="return cCCompany();">
							<option value=""></option>
							<option value="AX" <?php if($creditCardCo == "AX") echo 'SELECTED'; ?>>American Express</option>
							<option value="Care Credit" <?php if($creditCardCo == "Care Credit") echo 'SELECTED'; ?>>Care Credit</option>
							<option value="Dis" <?php if($creditCardCo == "Dis") echo 'SELECTED'; ?>>Discover</option>
							<option value="MC" <?php if($creditCardCo == "MC") echo 'SELECTED'; ?>>Master Card</option>
							<option value="Visa" <?php if($creditCardCo == "Visa") echo 'SELECTED'; ?>>Visa</option>
						</select>
					</div>
					<div class="col-sm-5">
						<label for="cCNoId">CC #</label>
						<input name="cCNo" id="cCNoId" type="text" class="form-control" value="<?php echo $creditCardNo; ?>">
					</div>
					
					<div class="col-sm-2">
						<label for="date2">Exp. Date</label>
						<input type="text" name="expireDate" id="date2" value="<?php echo $expirationDate; ?>" onBlur="return expDate();" class="form-control" />
					</div>
				</div>	
			</div>
		<?php } ?>
		<?php if($trans_mode=="write off"){?>	
			<div class="col-sm-2" id="write_off_box">
				<label for="write_off_code">Write off Code</label>
				<select name="write_off_code"  id="write_off_code" class="selectpicker" data-width="100%">
					<option value="">Write off Code</option>
					<?php
					$sel_rec=imw_query("select w_id,w_code,w_default from write_off_code");
					while($sel_write=imw_fetch_array($sel_rec)){
					?>
						<option value="<?php echo $sel_write['w_id'];?>" <?php if($sel_write['w_id']==$write_off_code_id){ echo "selected";} ?>><?php echo $sel_write['w_code'];?></option>
				<?php } ?>
				</select>
			</div> 
		<?php } ?>
		<?php if($trans_mode=="discount"){?>
			<div class="col-sm-2" id="discount_box">
				<label for="write_off_code">Discount Code</label>
				<select name="write_off_code" id="write_off_code" class="selectpicker" data-width="100%">
					<option value="">Discount Code</option>
					<?php
					$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
					while($sel_write=imw_fetch_array($sel_rec)){
					?>
						<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$write_off_code_id){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
					<?php } ?>
				</select>
			</div>
		<?php }?> 
		<?php if($trans_mode=="adjustment" || $trans_mode=="over adjustment"){?>
		<div class="col-sm-2" id="adj_off_box">
			<label for="adj_code">Adj Code</label>
			<select name="adj_code" id="adj_code" class="selectpicker" data-width="100%">
				<option value="">Adj off Code</option>
				<?php
				$sel_rec_adj=imw_query("select a_id,a_code from adj_code");
				while($sel_adj=imw_fetch_array($sel_rec_adj)){
				?>
					<option value="<?php echo $sel_adj['a_id'];?>" <?php if($sel_adj['a_id']==$payment_code_id){ echo "selected";} ?>><?php echo $sel_adj['a_code'];?></option>
				<?php } ?>
			</select>
		</div>
		<?php } ?> 
		<?php if($trans_mode=="refund" || $trans_mode=="debit credit"){?>
		<div class="col-sm-3" id="refund_note">
			<label for="credit_note"><?php echo $show_mode; ?> Note</label>
			<input type="text" id="credit_note" name="credit_note" value="<?php echo $credit_note; ?>" class="form-control">
		</div> 
		<?php } ?> 
	</div>
	<div class="row pt10">	
		<div class="col-sm-12 text-center" id="module_buttons">
			<input type="button" name="UpdateBtn" id="UpdateBtn" class="btn btn-success" value="Update" onClick="return chk_amt();">
			<input type="button" name="CancelBtn" id="CancelBtn" class="btn btn-danger" value="Cancel" onClick="window.close();">
		</div>
	</div>
</form>	
</div>
</body>
</html>
