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
$without_pat="yes"; 
$title = "Batch Transactions List";
require_once("../accounting/acc_header.php");
require_once("../../interface/common/assign_new_task.php");
$operator_id=$_SESSION['authId'];
$entered_date=date('Y-m-d H:i:s');
$transactionDate=date('Y-m-d');
?>
<script type="text/javascript">
function post_file(val){
	if(val=='yes'){
		document.getElementById('post_file_val').value='yes';
		document.frm_file.submit();
		if(window.opener.opener){
			window.opener.opener.location.reload();
		}
		if(window.opener.opener.opener){
			window.opener.opener.opener.location.reload();
		}
		if(window.opener){
			window.opener.close();
		}
	}else{
		window.close();
	}
}
</script>
<?php
$sel_policies_batch=imw_query("select in_batch_processing from copay_policies");
$row_policies_batch=imw_fetch_array($sel_policies_batch);
$in_batch_processing=$row_policies_batch['in_batch_processing'];

$post_id=$_REQUEST['post_id'];
$sel_batch=imw_query("select batch_name,tracking,post_status,batch_date,default_transaction_date from manual_batch_file where batch_id='$post_id'");
$row_batch=imw_fetch_array($sel_batch);
$batch_name=$row_batch['batch_name'];
$tracking=$row_batch['tracking'];
$default_transaction_date=$row_batch['default_transaction_date'];
$post_file_val=$_REQUEST['post_file_val'];
$encounter_id_arr=array();
$med_id_arr=array();
$sel_med_id=imw_query("select id from insurance_companies where in_house_code = 'medicare'");
while($med_rec_fetch=imw_fetch_array($sel_med_id)){
	$med_id_arr[]=$med_rec_fetch['id'];
}
$encounter_id_arr_pay=array();
if($post_file_val=='yes' && $row_batch['post_status']!=1){
	
	$getPayment_batch = imw_query("SELECT * FROM manual_batch_tx_payments WHERE batch_id='$post_id' and del_status='0' and post_status='0' order by id asc");
	while($getAccPayRows = imw_fetch_array($getPayment_batch)){
		$batch_tx_post_arr[$getAccPayRows['batch_trans_id']]=$getAccPayRows;
	}
	
	$sel_trans=imw_query("select * from manual_batch_transactions 
							where batch_id='$post_id'
							and post_status = 0 
							and del_status=0 order by trans_id");
	while($fet_trans=imw_fetch_array($sel_trans)){
	
	$trans_id=$fet_trans['trans_id'];
	$patient_id=$fet_trans['patient_id'];
	$encounter_id=$fet_trans['encounter_id'];
	$encounter_id_arr[]=$encounter_id;
	$scan_encounter_id_arr[$encounter_id]=$patient_id;
	$encounter_id_arr_pay[$encounter_id]=$encounter_id;
	$charge_list_id=$fet_trans['charge_list_id'];
	$charge_list_detaill_id=$fet_trans['charge_list_detaill_id'];
	$copay_charge_list_detaill_id=$fet_trans['copay_charge_list_detaill_id'];
	$trans_amt=$fet_trans['trans_amt'];
	$insurance_id=$fet_trans['insurance_id'];
	$ins_selected=$fet_trans['ins_selected'];
	$trans_by=$fet_trans['trans_by'];
	$proc_total_amt=$fet_trans['proc_total_amt'];
	$proc_allow_amt=$fet_trans['proc_allow_amt'];
	$trans_date=$fet_trans['trans_date'];
	$operator_id=$fet_trans['operator_id'];
	$payment_mode=$fet_trans['payment_mode'];
	$check_no=$fet_trans['check_no'];
	$credit_card_type=$fet_trans['credit_card_type'];
	$credit_card_no=$fet_trans['credit_card_no'];
	$credit_card_exp=$fet_trans['credit_card_exp'];
	$payment_claims=$fet_trans['payment_claims'];
	$write_off_code_id=$fet_trans['write_off_code_id'];
	$adj_code_id=$fet_trans['adj_code_id'];
	$cas_type=$fet_trans['cas_type'];
	$cas_code=$fet_trans['cas_code'];
	$facility_id=$fet_trans['facility_id'];
	if($default_transaction_date!='0000-00-00'){
		$payment_dot=$default_transaction_date;
	}else{
		if($in_batch_processing>0){
			if($row_batch['batch_date']!='0000-00-00'){
				$payment_dot=$row_batch['batch_date'];
			}else{
				$payment_dot=$transactionDate;
			}
		}else{
			$payment_dot=$transactionDate;
		}
	}
	
	$entered_date=$payment_dot.' '.date('H:i:s');

	if($encounter_id>0){
		$up_comm=imw_query("update paymentscomment set c_type='' where encounter_id='$encounter_id'");
	}
	//Add deduct amount in accounting
	if($trans_amt>0 && $payment_claims=='Deductible'){
		$deduct_ins=imw_query("insert into payment_deductible  
			set charge_list_detail_id='$charge_list_detaill_id',
			deduct_amount='$trans_amt',deductible_by='$trans_by',
			deduct_ins_id='$insurance_id',deduct_operator_id='$operator_id',
			deduct_date='$trans_date',entered_date='$entered_date',batch_id='$post_id',facility_id='$facility_id'");
		$chld_deduct_up=imw_query("update patient_charge_list_details set deductAmt=deductAmt+$trans_amt where charge_list_detail_id='$charge_list_detaill_id'");
		if($trans_amt>0){
			if($trans_by=="Insurance" && $insurance_id>0){
				$pay_type="ins";
				$ins_type=$ins_selected;
			}else{
				$pay_type="pat";
			}
			patient_proc_tx_update($charge_list_detaill_id,0,$pay_type,$ins_type);
		}
	}
	
	//Add denial amount in accounting
	if($trans_amt>0 && $payment_claims=='Denied'){
		
		$sel_chl=imw_query("select patient_charge_list_details.procCode,patient_charge_list.date_of_service,patient_data.lname,patient_data.fname,cpt_fee_tbl.cpt_prac_code,patient_charge_list.gro_id
		from patient_charge_list_details join patient_charge_list on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
		join patient_data on patient_data.id=patient_charge_list.patient_id 
		join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id=patient_charge_list_details.procCode
		where patient_charge_list_details.charge_list_detail_id='$charge_list_detaill_id' limit 0,1");
		$row_chl=imw_fetch_array($sel_chl);
		
		$denied_whr="";
		$denail_data_arr=array();
		$denail_data_arr['denial_cpt_code']=$row_chl['procCode'];
		if($cas_code!=""){
			$denail_data_arr['denial_cas_code']=$cas_code;
		}else{
			$denail_data_arr['denial_cas_code']=$cas_type;
		}
		$denial_resp=denial_resp_fun($denail_data_arr);
		if($denial_resp>0){
			$denied_whr=",next_responsible_by = '".$operator_id."'";
		}
		
		$denied_ins=imw_query("insert into deniedpayment set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_detail_id='$charge_list_detaill_id',
			deniedBy='$trans_by',deniedById='$insurance_id',deniedAmount='$trans_amt',denialOperatorId='$operator_id',deniedDate='$trans_date',
			entered_date = '$entered_date',batch_id='$post_id',CAS_type='$cas_type',CAS_code='$cas_code',facility_id='$facility_id' $denied_whr");
		$up_chld_allow=imw_query("update patient_charge_list_details set claimDenied='1' where  charge_list_detail_id='$charge_list_detaill_id'");
		
		if($trans_amt>0 && $denial_resp>0){
			if($trans_by=="Insurance" && $insurance_id>0){
				$pay_type="ins";
				$ins_type=$ins_selected;
			}else{
				$pay_type="pat";
			}
			patient_proc_tx_update($charge_list_detaill_id,0,$pay_type,$ins_type);
		}
		
		$task_insert_arr=array();
		$task_insert_arr['patientid']=$patient_id;
		$task_insert_arr['operatorid']=$operator_id;
		$task_insert_arr['section']='reason_code';
		if($cas_type=="" && $cas_code==""){
            $task_insert_arr['status_id']='';
        }else{
            $task_insert_arr['status_id']=$cas_type.'~~'.$cas_code; 
        }

		$task_insert_arr['encounter_id']=$encounter_id;
		$task_insert_arr['date_of_service']=$row_chl['date_of_service'];
		$task_insert_arr['patient_name']=$row_chl['lname'].', '.$row_chl['fname'];
		$task_insert_arr['cpt_code']=$row_chl['cpt_prac_code'];
        $task_insert_arr['task_group']=$row_chl['gro_id'];
        $task_insert_arr['task_ins_comp']=$insurance_id;
		assign_acc_task_rules_to($task_insert_arr);
	}
	//Add Writeoff or Discount amount in accounting
	if($trans_amt>0 && ($payment_claims=='Write Off' || $payment_claims=='Discount')){
		$writeoff_ins=imw_query("insert into paymentswriteoff  
			set patient_id='$patient_id',encounter_id='$encounter_id',
			charge_list_detail_id='$charge_list_detaill_id',
			write_off_by_id='$insurance_id',write_off_amount='$trans_amt',
			write_off_operator_id='$operator_id',write_off_code_id='$write_off_code_id',
			write_off_date='$trans_date',paymentStatus='$payment_claims',entered_date='$entered_date',batch_id='$post_id',
			CAS_type='$cas_type',CAS_code='$cas_code',facility_id='$facility_id'");
			
			$sel_chld_qry=imw_query("select newBalance,paidForProc from patient_charge_list_details 
					where del_status='0' and 
					charge_list_detail_id='$charge_list_detaill_id'");
			$fet_chld_rec=imw_fetch_array($sel_chld_qry);		
			$newBalance=$fet_chld_rec['newBalance'];
			$paidForProc=$fet_chld_rec['paidForProc'];
			
			if($newBalance>=$trans_amt){
				$bal_write_amt=$trans_amt;
				$paid_write_amt=0;
				$ovr_write_amt=0;
			}else{
				$overPayment=$trans_amt-$newBalance;
				$bal_write_amt=$newBalance;
				$ovr_write_amt=$overPayment;
				if($paidForProc>=$overPayment){
					$paid_write_amt=$overPayment;
				}else{
					$paid_write_amt=$paidForProc;
				}
			}
			
			$up_chld=imw_query("update patient_charge_list_details set balForProc=balForProc-$bal_write_amt,newBalance=newBalance-$bal_write_amt,
				 paidForProc=paidForProc-$paid_write_amt,overPaymentForProc=overPaymentForProc+$ovr_write_amt where charge_list_detail_id='$charge_list_detaill_id'");
		
		if($trans_amt>0){
			if($trans_by=="Insurance" && $insurance_id>0){
				$pay_type="ins";
				$ins_type=$ins_selected;
			}else{
				$pay_type="pat";
			}
			patient_proc_tx_update($charge_list_detaill_id,$trans_amt,$pay_type,$ins_type);
		}
		
				 
		$task_insert_arr=array();
		$task_insert_arr['patientid']=$patient_id;
		$task_insert_arr['operatorid']=$operator_id;
		$task_insert_arr['section']='reason_code';
        if($cas_type=="" && $cas_code==""){
            $task_insert_arr['status_id']='';
        }else{
            $task_insert_arr['status_id']=$cas_type.'~~'.$cas_code; 
        }
		
		$task_insert_arr['encounter_id']=$encounter_id;
		
		if($task_insert_arr['status_id']!=""){
			$sel_chl=imw_query("select patient_charge_list_details.procCode,patient_charge_list.date_of_service,patient_data.lname,patient_data.fname,cpt_fee_tbl.cpt_prac_code
			from patient_charge_list_details join patient_charge_list on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			join patient_data on patient_data.id=patient_charge_list.patient_id 
			join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id=patient_charge_list_details.procCode
			where patient_charge_list_details.charge_list_detail_id='$charge_list_detaill_id' limit 0,1");
			$row_chl=imw_fetch_array($sel_chl);
			
			$task_insert_arr['date_of_service']=$row_chl['date_of_service'];
			$task_insert_arr['patient_name']=$row_chl['lname'].', '.$row_chl['fname'];
			$task_insert_arr['cpt_code']=$row_chl['cpt_prac_code'];
			
			assign_acc_task_rules_to($task_insert_arr);	 
		}
	}
	if($trans_amt>0 && ($payment_claims=='Adjustment' || $payment_claims=='Over Adjustment' || $payment_claims=='Co-Insurance' || $payment_claims=='Co-Payment')){
		
		$insertAdjStr = "INSERT INTO account_payments SET
						patient_id = '$patient_id',
						encounter_id = '$encounter_id',
						charge_list_id = '$charge_list_id',
						charge_list_detail_id = '$charge_list_detaill_id',
						payment_by='$trans_by',
						payment_method='$payment_mode',
						check_number = '$credit_card_type',
						cc_type = '$creditCardCo',
						cc_number = '$credit_card_no',
						cc_exp_date = '$credit_card_exp',
						ins_id ='$insurance_id',
						payment_amount='$trans_amt',
						payment_date='$trans_date',
						operator_id='$operator_id',
						payment_code_id='$adj_code_id',
						payment_type='$payment_claims',
						entered_date='$entered_date',
						batch_id='$post_id',facility_id='$facility_id'
						";
		$insertAdjQry = imw_query($insertAdjStr);
		if($payment_claims!='Co-Insurance' && $payment_claims!='Co-Payment'){								
			$sel_chld_qry=imw_query("select * from patient_charge_list_details 
						where del_status='0' and 
						charge_list_detail_id='$charge_list_detaill_id'");
			$fet_chld_rec=imw_fetch_array($sel_chld_qry);		
			$paidForProc=$fet_chld_rec['paidForProc'];
			$balForProc=$fet_chld_rec['balForProc'];
			$newBalance=$fet_chld_rec['newBalance'];
			$overPaymentForProc=$fet_chld_rec['overPaymentForProc'];
			
			if($overPaymentForProc>0){
				if($trans_amt>=$overPaymentForProc){
					$bal_adj_amt=$trans_amt-$overPaymentForProc;
					$ovr_adj_amt=$overPaymentForProc;
				}else{
					$ovr_adj_amt=$trans_amt;
					$bal_adj_amt=0;
				}
			}else{
					$bal_adj_amt=$trans_amt;
					$ovr_adj_amt=0;
			}
		
			$updateStr1 = "UPDATE patient_charge_list_details SET
							overPaymentForProc = overPaymentForProc - $ovr_adj_amt,
							newBalance = newBalance + $bal_adj_amt,
							balForProc = balForProc + $bal_adj_amt,
							paidForProc = paidForProc - $bal_adj_amt
							WHERE charge_list_detail_id='$charge_list_detaill_id'";
			$updateQry1 = imw_query($updateStr1);
		}
		if($trans_amt>0 && ($payment_claims=='Over Adjustment' || $payment_claims=='Co-Insurance' || $payment_claims=='Co-Payment')){
			if($trans_by=="Insurance" && $insurance_id>0){
				$pay_type="ins";
				$ins_type=$ins_selected;
			}else{
				$pay_type="pat";
			}
			patient_proc_tx_update($charge_list_detaill_id,$trans_amt,$pay_type,$ins_type);
		}
	}
	
	if($payment_claims=='Allowed'){
		$sel_chld_qry=imw_query("select approvedAmt,write_off from patient_charge_list_details 
				where del_status='0' and 
				charge_list_detail_id='$charge_list_detaill_id'");
		$fet_chld_rec=imw_fetch_array($sel_chld_qry);		
		$approvedAmt=$fet_chld_rec['approvedAmt'];
		if($trans_amt>=$approvedAmt){
			$batch_allow_amt=$trans_amt-$approvedAmt;
		}else{
			$batch_allow_amt=$trans_amt;
		}
		$final_write_off=$fet_chld_rec['write_off']+$batch_allow_amt;
		$writeoff_ins=imw_query("insert into paymentswriteoff  
			set patient_id='$patient_id',encounter_id='$encounter_id',
			charge_list_detail_id='$charge_list_detaill_id',
			write_off_by_id='$insurance_id',write_off_amount='0.00',era_amt='$trans_amt',
			write_off_operator_id='$operator_id',write_off_code_id='$write_off_code_id',
			write_off_date='$trans_date',paymentStatus='Write Off',entered_date='$entered_date',batch_id='$post_id',facility_id='$facility_id'");
			
		$up_chld_allow=imw_query("update patient_charge_list_details set
				 balForProc=balForProc-$batch_allow_amt,
				 newBalance=newBalance-$batch_allow_amt,
				 approvedAmt=totalAmount-$trans_amt,
				 write_off=write_off+$batch_allow_amt,
				 write_off_code_id='$write_off_code_id',
				 write_off_date='$trans_date',write_off_dot='$payment_dot',write_off_opr_id='$operator_id',
				 batch_id='$post_id'
				 where  charge_list_detail_id='$charge_list_detaill_id'");

		$qry1=imw_query("Insert INTO defaultwriteoff SET
			  patient_id='".$patient_id."',
			  encounter_id='".$encounter_id."',
			  charge_list_id='".$charge_list_id."',
			  charge_list_detail_id='".$charge_list_detaill_id."',
			  write_off_amount='".$trans_amt."',	
			  write_off_by='".$insurance_id."',		  		  
			  write_off_operator_id='".$operator_id."',
			  write_off_dop='".$trans_date."',
			  write_off_dot='".$entered_date."',
			  write_off_code_id='".$write_off_code_id."',
			  batch_id='".$post_id."',facility_id='".$facility_id."'");
	}
	
	if($trans_amt>0 && ($payment_claims=='Negative Payment')){
		$paid_amt=$trans_amt;
		$overPayment=0;
		$paid_ins=imw_query("insert into patient_chargesheet_payment_info   
			set encounter_id='$encounter_id',paid_by='$trans_by',
			payment_amount='$trans_amt',payment_mode='$payment_mode',
			checkNo='$check_no',creditCardNo='$credit_card_no',
			creditCardCo='$credit_card_type',expirationDate='$credit_card_exp',
			operatorId='$operator_id',insProviderId='$insurance_id',
			insCompany='$ins_selected',date_of_payment='$trans_date',
			transaction_date='$payment_dot',paymentClaims='$payment_claims',batch_id='$post_id',facility_id='$facility_id'");
		$paid_ins_id=imw_insert_id();
		$paid_ins=imw_query("insert into patient_charges_detail_payment_info   
			set payment_id='$paid_ins_id',charge_list_detail_id='$charge_list_detaill_id',
			paidBy='$trans_by',paidDate='$trans_date',paidForProc='$paid_amt',
			overPayment='$overPayment',operator_id='$operator_id',entered_date='$payment_dot',batch_id='$post_id'");
		
		$up_trans_qry=imw_query("update  manual_batch_transactions set post_status='1' where trans_id='$trans_id'");
	}
	
	//Add Paid or Deposit amount in accounting
	if($trans_amt>0 && ($payment_claims=='Paid' || $payment_claims=='Deposit' || $payment_claims=='Interest Payment' || $payment_claims=='CoPay')){
		if($payment_claims=='CoPay'){
			$charge_list_detaill_id=$copay_charge_list_detaill_id;
		}
				
		$sel_chld_qry=imw_query("select * from patient_charge_list_details 
					where del_status='0' and 
					charge_list_detail_id='$charge_list_detaill_id'");
		$fet_chld_rec=imw_fetch_array($sel_chld_qry);		
		$paidForProc=$fet_chld_rec['paidForProc'];
		$balForProc=$fet_chld_rec['balForProc'];
		$newBalance=$fet_chld_rec['newBalance'];
		
		if($newBalance>=$trans_amt){
			$paid_amt=$trans_amt;
			$bal_proc_amt=$trans_amt;
			$bal_amt=$trans_amt;
			$overPayment=0;
		}else{
			$overPayment=$trans_amt-$newBalance;
			$paid_amt=$newBalance;
			$bal_proc_amt=$newBalance;
			$bal_amt=$newBalance;
			$overPayment=$overPayment;
		}
		if($payment_claims=='CoPay'){
			$charge_list_detaill_id=0;
		}
		$paid_ins=imw_query("insert into patient_chargesheet_payment_info   
			set encounter_id='$encounter_id',paid_by='$trans_by',
			payment_amount='$trans_amt',payment_mode='$payment_mode',
			checkNo='$check_no',creditCardNo='$credit_card_no',
			creditCardCo='$credit_card_type',expirationDate='$credit_card_exp',
			operatorId='$operator_id',insProviderId='$insurance_id',
			insCompany='$ins_selected',date_of_payment='$trans_date',
			transaction_date='$payment_dot',paymentClaims='$payment_claims',batch_id='$post_id',facility_id='$facility_id'");
		$paid_ins_id=imw_insert_id();
		$paid_ins=imw_query("insert into patient_charges_detail_payment_info   
			set payment_id='$paid_ins_id',charge_list_detail_id='$charge_list_detaill_id',
			paidBy='$trans_by',paidDate='$trans_date',paidForProc='$paid_amt',
			overPayment='$overPayment',operator_id='$operator_id',entered_date='$payment_dot',batch_id='$post_id',
			CAS_type='$cas_type',CAS_code='$cas_code'");
		
		if($payment_claims=='CoPay'){
			$charge_list_detaill_id=$copay_charge_list_detaill_id;
			imw_query("update patient_charge_list set copayPaid='1',coPayPaidDate='$trans_date' where encounter_id='$encounter_id'");
			imw_query("update patient_charge_list_details set coPayAdjustedAmount='1',paidStatus='Paid',superBillUpdate='1' where charge_list_detail_id = '".$copay_charge_list_detaill_id."'");
		}	
		$up_chld_allow=imw_query("update patient_charge_list_details set
			 paidForProc=paidForProc+$paid_amt,
			 balForProc=balForProc-$bal_proc_amt,
			 newBalance=newBalance-$bal_amt,
			 overPaymentForProc=overPaymentForProc+$overPayment,
			 paidStatus='Paid'
			 where  charge_list_detail_id='$charge_list_detaill_id'");
			 // UPDATE LAST PAYMENT DATE AND AMOUNT
			imw_query("UPDATE patient_charge_list SET lastPayment = '$paid_amt',lastPaymentDate = '$trans_date' WHERE encounter_id = '$encounter_id'");
		
			if($trans_amt>0){
				if($trans_by=="Insurance" && $insurance_id>0){
					$pay_type="ins";
					$ins_type=$ins_selected;
				}else{
					$pay_type="pat";
				}
				patient_proc_tx_update($charge_list_detaill_id,$trans_amt,$pay_type,$ins_type);
			}
		

		}
		
		if($trans_amt=='0.00' && $payment_claims=='Paid' && $trans_by=="Insurance" && $insurance_id>0){
			
			$pay_type="ins";
			$ins_type=$ins_selected;
			patient_proc_tx_update($charge_list_detaill_id,$trans_amt,$pay_type,$ins_type);
			
			$paid_amt=$trans_amt;
			$overPayment=0;
			
			$paid_ins=imw_query("insert into patient_chargesheet_payment_info   
				set encounter_id='$encounter_id',paid_by='$trans_by',
				payment_amount='$trans_amt',payment_mode='$payment_mode',
				checkNo='$check_no',creditCardNo='$credit_card_no',
				creditCardCo='$credit_card_type',expirationDate='$credit_card_exp',
				operatorId='$operator_id',insProviderId='$insurance_id',
				insCompany='$ins_selected',date_of_payment='$trans_date',
				transaction_date='$payment_dot',paymentClaims='$payment_claims',batch_id='$post_id',facility_id='$facility_id'");
			$paid_ins_id=imw_insert_id();
			$paid_ins=imw_query("insert into patient_charges_detail_payment_info   
				set payment_id='$paid_ins_id',charge_list_detail_id='$charge_list_detaill_id',
				paidBy='$trans_by',paidDate='$trans_date',paidForProc='$paid_amt',
				overPayment='$overPayment',operator_id='$operator_id',entered_date='$payment_dot',batch_id='$post_id',
				CAS_type='$cas_type',CAS_code='$cas_code'");
				
			$up_chld_allow=imw_query("update patient_charge_list_details set paidStatus='Paid' where charge_list_detail_id='$charge_list_detaill_id'");
		}
		
		if($payment_claims=='Tx Balance' && $batch_tx_post_arr[$trans_id]['id']>0){
			$batch_tx_post_data=$batch_tx_post_arr[$trans_id];
			imw_query("insert into tx_payments set patient_id='".$batch_tx_post_data['patient_id']."',encounter_id='".$batch_tx_post_data['encounter_id']."',charge_list_id = '".$batch_tx_post_data['charge_list_id']."',
			charge_list_detail_id='".$batch_tx_post_data['charge_list_detail_id']."',pri_due='".$batch_tx_post_data['pri_due']."',sec_due='".$batch_tx_post_data['sec_due']."',tri_due='".$batch_tx_post_data['tri_due']."',
			pat_due='".$batch_tx_post_data['pat_due']."',payment_date='".$batch_tx_post_data['payment_date']."',entered_date='".$entered_date."',payment_time='".$batch_tx_post_data['payment_time']."',
			operator_id='".$batch_tx_post_data['operator_id']."',batch_tx_id='".$batch_tx_post_data['id']."'");

			imw_query("update manual_batch_tx_payments set post_status='1' where id='".$batch_tx_post_data['id']."'");

			imw_query("update patient_charge_list_details set pri_due='".$batch_tx_post_data['pri_due_new']."',sec_due='".$batch_tx_post_data['sec_due_new']."',tri_due='".$batch_tx_post_data['tri_due_new']."',
			pat_due='".$batch_tx_post_data['pat_due_new']."' where charge_list_detail_id='".$batch_tx_post_data['charge_list_detail_id']."'");
		}
		
		$up_trans_qry=imw_query("update  manual_batch_transactions set post_status='1' where trans_id='$trans_id'");
		set_payment_trans($encounter_id);
		patient_proc_bal_update($encounter_id);
		//include("manageEncounterAmounts.php");
	}
	$encounter_id_arr_pay=array();
	$sel_trans_crd=imw_query("select * from manual_batch_creditapplied 
							where batch_id='$post_id'
							and post_status = 0 
							and delete_credit=0");
	while($fet_trans_crd=imw_fetch_array($sel_trans_crd)){
		$crAppId=$fet_trans_crd['crAppId'];
		$encounter_id_arr[]=$fet_trans_crd['crAppliedToEncId'];
		$encounter_id_arr[]=$fet_trans_crd['crAppliedToEncId_adjust'];
		$encounter_id_arr_pay[$fet_trans_crd['crAppliedToEncId']]=$fet_trans_crd['crAppliedToEncId'];
		$encounter_id_arr_pay[$fet_trans_crd['crAppliedToEncId_adjust']]=$fet_trans_crd['crAppliedToEncId_adjust'];
		$scan_encounter_id_arr[$fet_trans_crd['crAppliedToEncId']]=$fet_trans_crd['patient_id'];
		$scan_encounter_id_arr[$fet_trans_crd['crAppliedToEncId_adjust']]=$fet_trans_crd['patient_id_adjust'];
		
		$ins_crd="insert into creditapplied set
			patient_id='".$fet_trans_crd['patient_id']."',
			amountApplied='".$fet_trans_crd['amountApplied']."',
			overpayamount='".$fet_trans_crd['overpayamount']."',
			dos='".$fet_trans_crd['dos']."',
			dateApplied='".$fet_trans_crd['dateApplied']."',
			operatorApplied='".$fet_trans_crd['operatorApplied']."',
			crAppliedTo='".$fet_trans_crd['crAppliedTo']."',
			crAppliedToEncId='".$fet_trans_crd['crAppliedToEncId']."',
			type='".$fet_trans_crd['type']."',
			ins_case='".$fet_trans_crd['ins_case']."',
			insCompany='".$fet_trans_crd['insCompany']."',
			credit_note='".$fet_trans_crd['credit_note']."',
			payment_mode='".$fet_trans_crd['payment_mode']."',
			checkCcNumber='".$fet_trans_crd['checkCcNumber']."',
			creditCardNo='".$fet_trans_crd['creditCardNo']."',
			creditCardCo='".$fet_trans_crd['creditCardCo']."',
			expirationDateCc='".$fet_trans_crd['expirationDateCc']."',
			cpt_code='".$fet_trans_crd['cpt_code']."',
			cpt_code_id='".$fet_trans_crd['cpt_code_id']."',
			charge_list_detail_id='".$fet_trans_crd['charge_list_detail_id']."',
			charge_list_detail_id_adjust='".$fet_trans_crd['charge_list_detail_id_adjust']."',
			crAppliedToEncId_adjust='".$fet_trans_crd['crAppliedToEncId_adjust']."',
			patient_id_adjust='".$fet_trans_crd['patient_id_adjust']."',
			modify='".$fet_trans_crd['modify']."',
			credit_applied='".$fet_trans_crd['credit_applied']."',
			entered_date='$entered_date',batch_id='$post_id',facility_id='$facility_id'";
		imw_query($ins_crd);	
		$up_trans_qry=imw_query("update manual_batch_creditapplied set post_status='1' where crAppId='$crAppId'");
		
		if($fet_trans_crd['amountApplied']>0 && $fet_trans_crd['charge_list_detail_id_adjust']>0){
			if($fet_trans_crd['type']=="Insurance" && $fet_trans_crd['ins_case']>0){
				$pay_type="ins";
				$ins_type=$fet_trans_crd['insCompany'];
			}else{
				$pay_type="pat";
			}
			patient_proc_tx_update($fet_trans_crd['charge_list_detail_id_adjust'],$fet_trans_crd['amountApplied'],$pay_type,$ins_type);
		}
		 set_payment_trans($encounter_id_arr_pay,'multi'); 
		 include("manageEncounterAmounts.php");
	}
	
	$getScanStr = "SELECT * FROM upload_lab_rad_data WHERE uplaod_primary_id ='$post_id' AND scan_from='batch_processing' and upload_status='0'";
	$getScanQry = imw_query($getScanStr);
	while($getScanRows = imw_fetch_array($getScanQry)){
		$scan_ins_qry[]="insert into upload_lab_rad_data set scan_from='accounting',upload_file_name='".$getScanRows['upload_file_name']."',
		upload_file_type='".$getScanRows['upload_file_type']."',upload_date='".$getScanRows['upload_date']."',upload_by='".$getScanRows['upload_by']."'";
	}
	
	foreach($scan_encounter_id_arr as $scan_enc_key=>$scan_enc_val){
		foreach($scan_ins_qry as $scan_ins_key=>$scan_ins_val){
			if($scan_ins_qry[$scan_ins_key]!=""){
				$scan_ins_qry_run=$scan_ins_qry[$scan_ins_key].",uplaod_primary_id='".$scan_enc_key."',patient_id='".$scan_enc_val."'";
				imw_query($scan_ins_qry_run);
			}
		}
	}
	
}

if($_REQUEST['post_file_val']=='yes'){
	$today_dat=date('Y-m-d');
	$up_qry=imw_query("update  manual_batch_file set 
			post_status='1',posted_by='$operator_id',
			posted_date='$today_dat'
		 where batch_id='$post_id'");
	$imp_enc=implode(',',$encounter_id_arr);	
	$imp_med_id=implode(',',$med_id_arr);
	$ma18_enc_arr=array(); 
	if(count($med_id_arr)>0){
		$sel_rec=imw_query("select encounter_id from patient_charge_list where 
							del_status='0' and encounter_id in($imp_enc) and secondaryInsuranceCoId>0 
							and primary_paid='true' and primaryInsuranceCoId in($imp_med_id)");
		while($fet_rec=imw_fetch_array($sel_rec)){
			$ma18_enc_arr[]=$fet_rec['encounter_id'];
		}
	}
	$ma18_enc_imp=implode(',',$ma18_enc_arr);
?>
<script type="text/javascript">
	var ma18_enc_imp='<?php echo $ma18_enc_imp; ?>';
	var process_file='hcfa';
	if(ma18_enc_imp){
		window.open("era_hcfa_electronic.php?ma18_enc_imp="+ma18_enc_imp+"&process_file="+process_file,'Era','width=850,height=550,top=75,left=100,scrollbars=yes,resizable=1');
	}
	window.close();
</script>	
<?php   
}
?>
<form name="frm_file" action="batch_post_acc.php" method="post">
	<input type="hidden" name="post_file_val" id="post_file_val" value="">
	<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
</form>
<div class="purple_bar"><label>Batch File Post</label></div>
<div class="row pt10">
    <div class="col-sm-12">
    	<label>Do you want to Post Batch Name:  <?php echo $batch_name; ?> with Tracking#: <?php echo $tracking; ?></label>
    </div>
</div>
<div class="row ad_modal_footer mt10">	
    <div class="col-sm-12 text-center" id="module_buttons">
        <input type="button" name="balance_batch" id="balance_batch" class="btn btn-success" value="Yes" onClick="post_file('yes');">
        <input type="button" name="close" id="close" class="btn btn-danger" value="No" onClick="window.close();">
    </div>
</div> 

</body>
</html>