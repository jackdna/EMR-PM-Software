<?php
/*
 * File: refresh_data.php
 * Coded in PHP7
 * Purpose: Fetches latest payments
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
include_once(dirname(__FILE__)."/../../config/globals.php");
require_once("../library/classes/function.php"); 
?>
<?php
	$start_table="account_payments";
	$curr_dt=date('Y-m-d H:i:s');
	$start_val=0;
	$end = 1;
	
	if($_REQUEST['start_table']!=""){
		$start_table=$_REQUEST['start_table'];
	}
	
	if($_REQUEST['start_val']>0){
		//$start_val = $_REQUEST['start_val'];
	}
	

	$qry=imw_query("select financial_dashboard from copay_policies");
	$row=imw_fetch_array($qry);
	if($_REQUEST['financial_dashboard_dt']==""){
		$financial_dashboard_dt=$row['financial_dashboard'];
	}
	
	if($start_table=="account_payments"){
		$acc_qry=imw_query("select * from account_payments where date_timestamp>='$financial_dashboard_dt' order by id asc limit $start_val , $end");
		if(imw_num_rows($acc_qry)>0){
			$row=imw_fetch_array($acc_qry);
			$id=$row['id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_id=$row['charge_list_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$copay_chld_id=$row['copay_chld_id'];
			$payment_by=$row['payment_by'];
			$payment_method=$row['payment_method'];
			$check_number=$row['check_number'];
			$cc_type=$row['cc_type'];
			$cc_number=$row['cc_number'];
			$cc_exp_date=$row['cc_exp_date'];
			$ins_id=$row['ins_id'];
			$payment_amount=$row['payment_amount'];
			$payment_date=$row['payment_date'];
			$operator_id=$row['operator_id'];
			$entered_date=$row['entered_date'];
			$payment_code_id=$row['payment_code_id'];
			$del_status=$row['del_status'];
			$del_operator_id=$row['del_operator_id'];
			$del_date_time=$row['del_date_time'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			$payment_type=$row['payment_type'];
			$batch_id=$row['batch_id'];
			
			$tran_qry=imw_query("select id from account_trans where parent_id='$id' and payment_type in('Adjustment','Over Adjustment','Returned Check')");
			if(imw_num_rows($tran_qry)>0){
				$qry_ins_up="Update ";
				$qry_whr=" where parent_id='$id' and payment_type in('Adjustment','Over Adjustment','Returned Check')";
			}else{
				$qry_ins_up="INSERT INTO ";
				$qry_whr=",parent_id='$id'";
			}
			$rs=imw_query("$qry_ins_up account_trans set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
							charge_list_detail_id='$charge_list_detail_id',copay_chld_id='$copay_chld_id',payment_by='$payment_by',payment_method='$payment_method',
							check_number='$check_number',cc_type='$cc_type',cc_number='$cc_number',cc_exp_date='$cc_exp_date',ins_id='$ins_id',payment_amount='$payment_amount',
							payment_date='$payment_date',operator_id='$operator_id',entered_date='$entered_date',payment_code_id='$payment_code_id',del_status='$del_status',
							del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',payment_type='$payment_type',
							batch_id='$batch_id' $qry_whr");

			imw_query("update account_payments set date_timestamp='',report_date_timestamp=report_date_timestamp where id='$id'");				
		}else{
			$start_table="paymentswriteoff";
			$start_val=0;
		}
	}
	
	if($start_table=="paymentswriteoff"){
		$acc_qry=imw_query("select * from paymentswriteoff where date_timestamp>='$financial_dashboard_dt' order by write_off_id asc limit $start_val , $end");
		if(imw_num_rows($acc_qry)>0){
			$row=imw_fetch_array($acc_qry);
			$write_off_id=$row['write_off_id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$write_off_by_id=$row['write_off_by_id'];
			$write_off_amount=$row['write_off_amount'];
			$write_off_operator_id=$row['write_off_operator_id'];
			$entered_date=$row['entered_date'];
			$write_off_date=$row['write_off_date'];
			$delStatus=$row['delStatus'];
			$write_off_del_date=$row['write_off_del_date'];
			$write_off_del_time=$row['write_off_del_time'];
			$del_operator_id=$row['del_operator_id'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			$CAS_type=$row['CAS_type'];
			$CAS_code=$row['CAS_code'];
			$write_off_code_id=$row['write_off_code_id'];
			$paymentStatus=$row['paymentStatus'];
			$era_amt=$row['era_amt'];
			$batch_id=$row['batch_id'];
			$cap_main_id=$row['cap_main_id'];
			
			$payment_by="Patient";
			if($write_off_by_id>0){
				$payment_by="Insurance";
			}
			$paymentStatus="Write Off";
			if(strtolower($row['paymentStatus'])=='discount'){
				$paymentStatus=$row['paymentStatus'];
			}
			$del_date_time="";
			$del_date_time=$row['write_off_del_date'].' '.$row['write_off_del_time'];
			
			$tran_qry=imw_query("select id from account_trans where parent_id='$write_off_id' and payment_type in('Write Off','Discount')");
			if(imw_num_rows($tran_qry)>0){
				$qry_ins_up="Update ";
				$qry_whr=" where parent_id='$write_off_id' and payment_type in('Write Off','Discount')";
			}else{
				$qry_ins_up="INSERT INTO ";
				$qry_whr=",parent_id='$write_off_id'";
			}
			
			$charge_list_id=0;
			if($charge_list_detail_id>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list_details where charge_list_detail_id='$charge_list_detail_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			$rs=imw_query("$qry_ins_up account_trans set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
						charge_list_detail_id='$charge_list_detail_id',payment_by='$payment_by',ins_id='$write_off_by_id',payment_amount='$write_off_amount',
						payment_date='$write_off_date',operator_id='$write_off_operator_id',entered_date='$entered_date',payment_code_id='$write_off_code_id',del_status='$delStatus',
						del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',payment_type='$paymentStatus',
						batch_id='$batch_id',cap_main_id='$cap_main_id',era_amt='$era_amt' $qry_whr");
						
			imw_query("update paymentswriteoff set date_timestamp='',report_date_timestamp=report_date_timestamp where write_off_id='$write_off_id'");					
		}else{
			$start_table="patient_chargesheet_payment_info";
			$start_val=0;
		}
	}
	
	if($start_table=="patient_chargesheet_payment_info"){
		$qry=imw_query("SELECT pcdpi.*,pcpi.encounter_id,pcpi.payment_mode,pcpi.checkNo,pcpi.creditCardCo,pcpi.creditCardNo,pcpi.expirationDate,
				 	pcpi.insCompany,pcpi.paymentClaims,pcpi.insProviderId,pcpi.transaction_date
				 	FROM  patient_chargesheet_payment_info as pcpi JOIN patient_charges_detail_payment_info as pcdpi 
					on pcpi.payment_id=pcdpi.payment_id where pcdpi.date_timestamp>='$financial_dashboard_dt' order by pcdpi.payment_details_id asc limit $start_val , $end");	
		if(imw_num_rows($qry)>0){				
			$row=imw_fetch_array($qry);
			$payment_details_id=$row['payment_details_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$paidBy=$row['paidBy'];
			$payment_amount=$row['paidForProc']+$row['overPayment'];
			$payment_date=$row['paidDate'];
			$operator_id=$row['operator_id'];
			$entered_date=$row['entered_date'];
			$del_status=$row['deletePayment'];
			$del_operator_id=$row['del_operator_id'];
			$del_date_time=$row['deleteDate'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			$payment_type=$row['deleteDate'];
			$batch_id=$row['batch_id'];
			
			$encounter_id=$row['encounter_id'];
			$payment_mode=$row['payment_mode'];
			$checkNo=$row['checkNo'];
			$creditCardCo=$row['creditCardCo'];
			$creditCardNo=$row['creditCardNo'];
			$expirationDate=$row['expirationDate'];
			$paymentClaims=$row['paymentClaims'];
			//$ins_id=$row['insCompany'];
			$ins_id=$row['insProviderId'];
			$transaction_date=$row['transaction_date'];
			
			$tran_qry=imw_query("select id from account_trans where parent_id='$payment_details_id' and payment_type in('paid','Negative Payment','Interest Payment','Deposit')");
			if(imw_num_rows($tran_qry)>0){
				$qry_ins_up="Update ";
				$qry_whr=" where parent_id='$payment_details_id' and payment_type in('paid','Negative Payment','Interest Payment','Deposit')";
			}else{
				$qry_ins_up="INSERT INTO ";
				$qry_whr=",parent_id='$payment_details_id'";
			}
				
			$charge_list_id=0;
			$patient_id=0;
			$copay_chld_id=0;
			if($encounter_id>0){
				$chl_qry=imw_query("select charge_list_id,patient_id from patient_charge_list where encounter_id='$encounter_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
				$patient_id=$chl_row['patient_id'];
				if($charge_list_detail_id==0){
					$chld_qry=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$charge_list_id' and coPayAdjustedAmount='1'");
					$chld_row=imw_fetch_array($chld_qry);
					$copay_chld_id=$chld_row['charge_list_detail_id'];
					
				}
			}
				
			$rs=imw_query("$qry_ins_up account_trans set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
							charge_list_detail_id='$charge_list_detail_id',copay_chld_id='$copay_chld_id',payment_by='$paidBy',
							payment_method='$payment_mode',check_number='$checkNo',cc_type='$creditCardCo',cc_number='$creditCardNo',
							cc_exp_date='$expirationDate',ins_id='$ins_id',payment_amount='$payment_amount',payment_date='$payment_date',
							operator_id='$operator_id',entered_date='$transaction_date',payment_code_id='$payment_code_id',del_status='$del_status',
							del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',
							payment_type='$paymentClaims',batch_id='$batch_id' $qry_whr");
							
			imw_query("update patient_charges_detail_payment_info set date_timestamp='',report_date_timestamp=report_date_timestamp where payment_details_id='$payment_details_id'");				
		}else{
			$start_table="creditapplied";
			$start_val=0;
		}
	}
	if($start_table=="creditapplied"){
		$qry=imw_query("SELECT * FROM  creditapplied  where date_timestamp>='$financial_dashboard_dt' order by crAppId asc limit $start_val , $end");	
		if(imw_num_rows($qry)>0){	
			$row=imw_fetch_array($qry);
			$crAppId=$row['crAppId'];
			$patient_id=$row['patient_id'];
			$amountApplied=$row['amountApplied'];
			$dateApplied=$row['dateApplied'];
			$operatorApplied=$row['operatorApplied'];
			$entered_date=$row['entered_date'];
			$crAppliedTo=$row['crAppliedTo'];
			$crAppliedToEncId=$row['crAppliedToEncId'];
			$type=$row['type'];
			$ins_case=$row['ins_case'];
			$insCompany=$row['insCompany'];
			$payment_mode=$row['payment_mode'];
			$checkCcNumber=$row['checkCcNumber'];
			$creditCardNo=$row['creditCardNo'];
			$creditCardCo=$row['creditCardCo'];
			$expirationDateCc=$row['expirationDateCc'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$charge_list_detail_id_adjust=$row['charge_list_detail_id_adjust'];
			$crAppliedToEncId_adjust=$row['crAppliedToEncId_adjust'];
			$patient_id_adjust=$row['patient_id_adjust'];
			$delete_credit=$row['delete_credit'];
			$del_operator_id=$row['del_operator_id'];
			$del_date_time=$row['del_date_time'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			$batch_id=$row['batch_id'];
			
			if(strtolower($crAppliedTo)=='payment'){
				$payment_type="refund";
			}else{
				$payment_type="debit";
			}
			
			$tran_qry=imw_query("select id from account_trans where parent_id='$crAppId' and payment_type in('refund','credit','debit')");
			if(imw_num_rows($tran_qry)>0){
				$qry_ins_up="Update ";
				$qry_whr=" where parent_id='$crAppId' and payment_type in('refund','credit','debit')";
			}else{
				$qry_ins_up="INSERT INTO ";
				$qry_whr=",parent_id='$crAppId'";
			}
			
			$charge_list_id=0;
			if($crAppliedToEncId>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$crAppliedToEncId'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			
			$rs=imw_query("$qry_ins_up account_trans set patient_id='$patient_id',encounter_id='$crAppliedToEncId',charge_list_id='$charge_list_id',
							charge_list_detail_id='$charge_list_detail_id',payment_by='$type',payment_method='$payment_mode',check_number='$checkCcNumber',
							cc_type='$creditCardCo',cc_number='$creditCardNo',cc_exp_date='$expirationDateCc',ins_id='$insCompany',payment_amount='$amountApplied',
							payment_date='$dateApplied',operator_id='$operatorApplied',entered_date='$entered_date',del_status='$delete_credit',
							del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',
							payment_type='$payment_type',batch_id='$batch_id' $qry_whr");
			
			if($payment_type=="debit"){
				$charge_list_id=0;
				if($crAppliedToEncId>0){
					$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$crAppliedToEncId_adjust'");
					$chl_row=imw_fetch_array($chl_qry);
					$charge_list_id=$chl_row['charge_list_id'];
				}
				
				$rs=imw_query("$qry_ins_up account_trans set patient_id='$patient_id_adjust',encounter_id='$crAppliedToEncId_adjust',charge_list_id='$charge_list_id',
								charge_list_detail_id='$charge_list_detail_id_adjust',payment_by='$type',payment_method='$payment_mode',check_number='$checkCcNumber',
								cc_type='$creditCardCo',cc_number='$creditCardNo',cc_exp_date='$expirationDateCc',ins_id='$insCompany',payment_amount='$amountApplied',
								payment_date='$dateApplied',operator_id='$operatorApplied',entered_date='$entered_date',del_status='$delete_credit',
								del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',
								payment_type='credit',batch_id='$batch_id' $qry_whr");
			}
			imw_query("update creditapplied set date_timestamp='',report_date_timestamp=report_date_timestamp where crAppId='$crAppId'");		
		}else{
			$start_table="";
			$start_val=0;
		}
	}
	
	if($start_table==""){
		imw_query("update copay_policies set financial_dashboard='$curr_dt'");
?>		
		<script type='text/javascript'>
		var div_data="<div style='text-align:center;padding-top:40px;'><img src='../images/done_icon.png' width='75px;'></div><div style='text-align:center;padding-top:25px;'><strong>Your data refreshed Sucessfully.</strong></div>";
		top.removeMessi();
		top.fancyModal(div_data,"300px","200px");
		</script>";
<?php } ?>
<form action="" method="get" name="submit_frm" id="submit_frm">
    <input type="hidden" name="start_table" value="<?php echo $start_table; ?>">
    <input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
</form>

<?php
if($start_table!=""){
?>
<script type="text/javascript">
	//top.UpdateActiveMessi('Fetching for table');
    document.getElementById("submit_frm").submit();
</script>
<?php
}
?>