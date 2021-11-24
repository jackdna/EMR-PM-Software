<?php
/*
 * File: refresh_data.php
 * Coded in PHP7
 * Purpose: Fetch latest payments
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../library/classes/function.php"); 

set_time_limit(0);
?>
<?php
$start_table="account_payments";
$curr_dt=date('Y-m-d H:i:s');
$start_val=0;
$end = 1000;

if($_REQUEST['start_table']!=""){
	$start_table=$_REQUEST['start_table'];
}

if($_REQUEST['start_val']>0){
	//$start_val = $_REQUEST['start_val'];
}
if($long_end_point<=0){
	$end = 100;
}
if($cron_job!=""){
	$end = 100000;
}

$row_arr=$row_ap_arr=$rp_row_trans_arr=array();
	
$parent_id=$master_tbl_id=$patient_id=$encounter_id=$charge_list_id=$charge_list_detail_id=$trans_by=$trans_ins_id=$trans_method="";
$check_number=$cc_type=$cc_number=$cc_exp_date=$trans_type=$trans_amount=$trans_code_id=$batch_id=$cap_main_id="";
$trans_dot=$trans_dot_time=$trans_dop=$trans_dop_time=$trans_operator_id=$era_amt=$cas_type=$cas_code=$trans_qry_type="";
$trans_del_date=$trans_del_time=$trans_del_operator_id=$units="";

if($start_table!=""){
	$ins_qry_trans="insert into account_trans (patient_id,encounter_id,charge_list_id,charge_list_detail_id,copay_chld_id,
	payment_by,payment_method,check_number,cc_type,cc_number,cc_exp_date,ins_id,payment_amount,payment_date,operator_id,
	entered_date,payment_code_id,del_status,del_operator_id,del_date_time,modified_date,modified_by,payment_type,batch_id,
	cap_main_id,era_amt,parent_id) value";
}

if($start_table=="account_payments"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("select * from account_payments where date_timestamp!='0000-00-00 00:00:00' order by id asc limit $start_val , $end");
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['id']]=$acc_row;
			$row_ap_arr[$acc_row['id']]=$acc_row['id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select id,parent_id,payment_type from account_trans where parent_id in('$row_ap_imp') and payment_type in('Adjustment','Over Adjustment','Returned Check') order by id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['parent_id']][$rp_acc_trans_row['payment_type']]=$rp_acc_trans_row;
		}
		
	foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];	
			$master_tbl_id_arr[]=$row['id'];
			$master_tbl_id=$row['id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_id=$row['charge_list_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$trans_by=$row['payment_by'];
			$trans_ins_id=$row['ins_id'];
			$trans_method=$row['payment_method'];
			$check_number=$row['check_number'];
			$cc_type=$row['cc_type'];
			$cc_number=$row['cc_number'];
			$cc_exp_date=$row['cc_exp_date'];
			$trans_type=$row['payment_type'];
			$trans_amount=$row['payment_amount'];
			$trans_dop=$row['payment_date'];
			$trans_dot=$row['entered_date'];
			$trans_operator_id=$row['operator_id'];
			$trans_code_id=$row['payment_code_id'];
			$batch_id=$row['batch_id'];
			$trans_del_date=$row['del_date_time'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			$copay_chld_id=$row['copay_chld_id'];
			$del_status=$row['del_status'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['id']>0){
				$acc_trans_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['id'];
				imw_query("update account_trans set payment_by='$trans_by',payment_method='$trans_method',check_number='$check_number',cc_type='$cc_type',
				cc_number='$cc_number',cc_exp_date='$cc_exp_date',ins_id='$trans_ins_id',payment_amount='$trans_amount',payment_date='$trans_dop',operator_id='$trans_operator_id',
				payment_code_id='$trans_code_id',del_status='$del_status',del_operator_id='$trans_del_operator_id',del_date_time='$trans_del_date',modified_date='$modified_date',
				modified_by='$modified_by',batch_id='$batch_id',cap_main_id='$cap_main_id',era_amt='$era_amt' where id='$acc_trans_id' and payment_type='$trans_type'");
			}else{
				$ins_qry.="('$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$copay_chld_id',
				'$trans_by','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_ins_id','$trans_amount','$trans_dop','$trans_operator_id',
				'$trans_dot','$trans_code_id',$del_status,'$trans_del_operator_id','$trans_del_date','$modified_date','$modified_by',
				'$trans_type','$batch_id','$cap_main_id','$era_amt','$master_tbl_id'),";
			}
			
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update account_payments set date_timestamp='',report_date_timestamp=report_date_timestamp where id in('$master_tbl_id_imp')");

		if($cron_job!=""){
			$start_table="paymentswriteoff";
		}
		
	}else{
		$start_table="paymentswriteoff";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="paymentswriteoff"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("select * from paymentswriteoff where date_timestamp!='0000-00-00 00:00:00' order by write_off_id asc limit $start_val , $end");
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['write_off_id']]=$acc_row;
			$row_ap_arr[$acc_row['write_off_id']]=$acc_row['write_off_id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select id,parent_id,payment_type from account_trans where parent_id in('$row_ap_imp') and payment_type in('Write Off','Discount') order by id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['parent_id']][$rp_acc_trans_row['payment_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];	
			$master_tbl_id_arr[]=$row['write_off_id'];
			$master_tbl_id=$row['write_off_id'];
			$patient_id=$row['patient_id'];
			$encounter_id=$row['encounter_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			
			$trans_by='Patient';
			if($row['write_off_by_id']>0){
				$trans_by='Insurance';
			}
			
			$trans_type="Write Off";
			if($row['paymentStatus']!=""){
				$trans_type=$row['paymentStatus'];
			}
			$trans_ins_id=$row['write_off_by_id'];
			$trans_amount=$row['write_off_amount'];
			$trans_dop=$row['write_off_date'];
			$trans_dot=$row['entered_date'];
			$trans_operator_id=$row['write_off_operator_id'];
			$trans_code_id=$row['write_off_code_id'];
			$batch_id=$row['batch_id'];
			$cap_main_id=$row['cap_main_id'];
			$era_amt=$row['era_amt'];
			$cas_type=$row['CAS_type'];
			$cas_code=$row['CAS_code'];
			$trans_del_date=$row['write_off_del_date'];
			$trans_del_time=$row['write_off_del_time'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			$del_status=$row['delStatus'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			
			
			$charge_list_id=0;
			if($charge_list_detail_id>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list_details where charge_list_detail_id='$charge_list_detail_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['id']>0){
				$acc_trans_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['id'];
				imw_query("update account_trans set payment_by='$trans_by',payment_method='$trans_method',check_number='$check_number',cc_type='$cc_type',
				cc_number='$cc_number',cc_exp_date='$cc_exp_date',ins_id='$trans_ins_id',payment_amount='$trans_amount',payment_date='$trans_dop',operator_id='$trans_operator_id',
				payment_code_id='$trans_code_id',del_status='$del_status',del_operator_id='$trans_del_operator_id',del_date_time='$trans_del_date',modified_date='$modified_date',
				modified_by='$modified_by',batch_id='$batch_id',cap_main_id='$cap_main_id',era_amt='$era_amt' where id='$acc_trans_id' and payment_type='$trans_type'");
			}else{
				$ins_qry.="('$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','',
				'$trans_by','','','','','','$trans_ins_id','$trans_amount','$trans_dop','$trans_operator_id',
				'$trans_dot','$trans_code_id',$del_status,'$trans_del_operator_id','$trans_del_date','$modified_date','$modified_by',
				'$trans_type','$batch_id','$cap_main_id','$era_amt','$master_tbl_id'),";
			}
			
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update paymentswriteoff set date_timestamp='',report_date_timestamp=report_date_timestamp where write_off_id in('$master_tbl_id_imp')");			
		
		if($cron_job!=""){
			$start_table="patient_charges_detail_payment_info";
		}
	
	}else{
		$start_table="patient_charges_detail_payment_info";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="patient_charges_detail_payment_info"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("SELECT pcdpi.*,pcpi.encounter_id,pcpi.payment_mode,pcpi.checkNo,pcpi.creditCardCo,pcpi.creditCardNo,pcpi.expirationDate,
				pcpi.insCompany,pcpi.paymentClaims,pcpi.insProviderId,pcpi.transaction_date
				FROM  patient_chargesheet_payment_info as pcpi JOIN patient_charges_detail_payment_info as pcdpi 
				on pcpi.payment_id=pcdpi.payment_id where pcdpi.date_timestamp!='0000-00-00 00:00:00' order by pcdpi.payment_details_id asc limit $start_val , $end");	
	if(imw_num_rows($acc_qry)>0){
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['payment_details_id']]=$acc_row;
			$row_ap_arr[$acc_row['payment_details_id']]=$acc_row['payment_details_id'];
		}
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select id,parent_id,payment_type from account_trans where parent_id in('$row_ap_imp') and payment_type in('copay','Deposit','Interest Payment','Negative Payment','paid') order by id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['parent_id']][$rp_acc_trans_row['payment_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];					
			$master_tbl_id=$row['payment_details_id'];
			$master_tbl_id_arr[]=$master_tbl_id;
			$encounter_id=$row['encounter_id'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$trans_by=$row['paidBy'];
			$trans_ins_id=$row['insProviderId'];
			$trans_amount=$row['paidForProc']+$row['overPayment'];
			$trans_dop=$row['paidDate'];
			$trans_dop_time=$row['paid_time'];
			$trans_dot=$row['transaction_date'];
			$trans_dot_time='';
			$operator_id=$row['operator_id'];
			$trans_method=$row['payment_mode'];
			$check_number=$row['checkNo'];
			$cc_type=$row['creditCardCo'];
			$cc_number=$row['creditCardNo'];
			$cc_exp_date=$row['expirationDate'];
			$trans_type=$row['paymentClaims'];
			$cas_type=$row['CAS_type'];
			$cas_code=$row['CAS_code'];
			$batch_id=$row['batch_id'];
			$trans_del_date=$row['deleteDate'];
			$trans_del_time=$row['deleteTime'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			$del_status=$row['deletePayment'];
			
			
			$charge_list_id=0;
			$patient_id=0;
			if($encounter_id>0){
				$chl_qry=imw_query("select charge_list_id,patient_id from patient_charge_list where encounter_id='$encounter_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
				$patient_id=$chl_row['patient_id'];
				if($charge_list_detail_id==0){
					$chld_qry=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$charge_list_id' and coPayAdjustedAmount='1'");
					$chld_row=imw_fetch_array($chld_qry);
					$charge_list_detail_id=$chld_row['charge_list_detail_id'];
					$copay_chld_id=$chld_row['charge_list_detail_id'];
					$trans_type="copay";
				}
			}
			
			if($charge_list_detail_id==0){
				$copay_chld_qry=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$charge_list_id' limit 0,1");
				$copay_chld_row=imw_fetch_array($copay_chld_qry);
				$charge_list_detail_id=$copay_chld_row['charge_list_detail_id'];
				$copay_chld_id=$copay_chld_row['charge_list_detail_id'];
			}
			
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['id']>0){
				$acc_trans_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['id'];
				imw_query("update account_trans set payment_by='$trans_by',payment_method='$trans_method',check_number='$check_number',cc_type='$cc_type',
				cc_number='$cc_number',cc_exp_date='$cc_exp_date',ins_id='$trans_ins_id',payment_amount='$trans_amount',payment_date='$trans_dop',operator_id='$trans_operator_id',
				payment_code_id='$trans_code_id',del_status='$del_status',del_operator_id='$trans_del_operator_id',del_date_time='$trans_del_date',modified_date='$modified_date',
				modified_by='$modified_by',batch_id='$batch_id',cap_main_id='$cap_main_id',era_amt='$era_amt' where id='$acc_trans_id' and payment_type='$trans_type'");
			}else{
				$ins_qry.="('$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','$copay_chld_id',
				'$trans_by','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_ins_id','$trans_amount','$trans_dop','$trans_operator_id',
				'$trans_dot','$trans_code_id',$del_status,'$trans_del_operator_id','$trans_del_date','$modified_date','$modified_by',
				'$trans_type','$batch_id','$cap_main_id','$era_amt','$master_tbl_id'),";
			}
			
			
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update patient_charges_detail_payment_info set date_timestamp='',report_date_timestamp=report_date_timestamp where payment_details_id in('$master_tbl_id_imp')");
		
		if($cron_job!=""){
			$start_table="creditapplied";
		}
		
	}else{
		$start_table="creditapplied";
		$start_val=$show_start_val=0;
	}
}
if($start_table=="creditapplied"){
	$ins_qry=$ins_qry_trans;
	$row_arr=$row_ap_arr=$rp_row_trans_arr=$master_tbl_id_arr=array();
	$acc_qry=imw_query("SELECT * FROM  creditapplied  where date_timestamp!='0000-00-00 00:00:00' order by crAppId asc limit $start_val , $end");	
	if(imw_num_rows($acc_qry)>0){	
		while($acc_row=imw_fetch_array($acc_qry)){
			$row_arr[$acc_row['crAppId']]=$acc_row;
			$row_ap_arr[$acc_row['crAppId']]=$acc_row['crAppId'];
		}
		
		
		$row_ap_imp=implode("','",$row_ap_arr);
		$rp_acc_trans_qry=imw_query("select id,parent_id,payment_type from account_trans where parent_id in('$row_ap_imp') and payment_type in('refund','credit','debit') order by id asc");
		while($rp_acc_trans_row=imw_fetch_array($rp_acc_trans_qry)){
			$rp_row_trans_arr[$rp_acc_trans_row['parent_id']][$rp_acc_trans_row['payment_type']]=$rp_acc_trans_row;
		}
		
		foreach($row_arr as $row_key=>$row_val){
			$row=$row_arr[$row_key];
			$master_tbl_id=$row['crAppId'];
			$master_tbl_id_arr[]=$master_tbl_id;
			$patient_id=$row['patient_id'];
			$encounter_id=$row['crAppliedToEncId'];
			$charge_list_detail_id=$row['charge_list_detail_id'];
			$trans_by=$row['type'];
			$trans_ins_id=$row['insCompany'];
			$trans_amount=$row['amountApplied'];
			$trans_dop=$row['dateApplied'];
			$trans_dot=$row['entered_date'];
			$trans_dot_time=$row['entered_date'];
			$operator_id=$row['operatorApplied'];
			$trans_method=$row['payment_mode'];
			$check_number=$row['checkCcNumber'];
			$cc_type=$row['creditCardCo'];
			$cc_number=$row['creditCardNo'];
			$cc_exp_date=$row['expirationDateCc'];
			$cas_type=$row['CAS_type'];
			$cas_code=$row['CAS_code'];
			$batch_id=$row['batch_id'];
			if(strtolower($row['crAppliedTo'])=='payment'){
				$trans_type="refund";
			}else{
				$trans_type="debit";
			}
			
			$patient_id_adjust=$row['patient_id_adjust'];
			$encounter_id_adjust=$row['crAppliedToEncId_adjust'];
			$charge_list_detail_id_adjust=$row['charge_list_detail_id_adjust'];
			$trans_del_date=$row['del_date_time'];
			$trans_del_time=$row['del_date_time'];
			$trans_del_operator_id=$row['del_operator_id'];
			$facility_id=$row['facility_id'];
			$modified_date=$row['modified_date'];
			$modified_by=$row['modified_by'];
			$del_status=$row['delete_credit'];
			
						
			$charge_list_id=0;
			if($encounter_id>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$encounter_id'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			
			$charge_list_id_adjust=0;
			if($trans_type=="debit"){
				if($encounter_id_adjust>0){
					$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$encounter_id_adjust'");
					$chl_row=imw_fetch_array($chl_qry);
					$charge_list_id_adjust=$chl_row['charge_list_id'];
				}
			}	
			
			if($rp_row_trans_arr[$master_tbl_id][$trans_type]['id']>0){
				$whr_trans_type="";
				if($trans_type=="debit"){
					$acc_trans_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['parent_id'];
					$whr_trans_type = "where parent_id='$acc_trans_id' and payment_type in('debit','credit')";
				}else{
					$acc_trans_id=$rp_row_trans_arr[$master_tbl_id][$trans_type]['id'];
					$whr_trans_type = "where id='$acc_trans_id' and payment_type ='$trans_type'";
				}
				
				if($whr_trans_type!=""){
					imw_query("update account_trans set payment_by='$trans_by',payment_method='$trans_method',check_number='$check_number',cc_type='$cc_type',
					cc_number='$cc_number',cc_exp_date='$cc_exp_date',ins_id='$trans_ins_id',payment_amount='$trans_amount',payment_date='$trans_dop',operator_id='$trans_operator_id',
					payment_code_id='$trans_code_id',del_status='$del_status',del_operator_id='$trans_del_operator_id',del_date_time='$trans_del_date',modified_date='$modified_date',
					modified_by='$modified_by',batch_id='$batch_id',cap_main_id='$cap_main_id',era_amt='$era_amt' $whr_trans_type");
				}
						
			}else{
				$ins_qry.="('$patient_id','$encounter_id','$charge_list_id','$charge_list_detail_id','',
				'$trans_by','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_ins_id','$trans_amount','$trans_dop','$trans_operator_id',
				'$trans_dot','$trans_code_id',$del_status,'$trans_del_operator_id','$trans_del_date','$modified_date','$modified_by',
				'$trans_type','$batch_id','$cap_main_id','$era_amt','$master_tbl_id'),";
			
				if($trans_type=="debit"){
					
					$ins_qry.="('$patient_id_adjust','$encounter_id_adjust','$charge_list_id_adjust','$charge_list_detail_id_adjust','',
					'$trans_by','$trans_method','$check_number','$cc_type','$cc_number','$cc_exp_date','$trans_ins_id','$trans_amount','$trans_dop','$trans_operator_id',
					'$trans_dot','$trans_code_id',$del_status,'$trans_del_operator_id','$trans_del_date','$modified_date','$modified_by',
					'credit','$batch_id','$cap_main_id','$era_amt','$master_tbl_id'),";
				}
			}
			
		}
		$ins_qry_run=substr($ins_qry,0,-1).';';
		imw_query($ins_qry_run);
		$master_tbl_id_imp=implode("','",$master_tbl_id_arr);
		imw_query("update creditapplied set date_timestamp='',report_date_timestamp=report_date_timestamp where crAppId in('$master_tbl_id_imp')");
		
		if($cron_job!=""){
			$start_table="";
		}
		
	}else{
		$start_table="";
		$start_val=$show_start_val=0;
	}
}

if($start_table==""){
	imw_query("update copay_policies set financial_dashboard='$curr_dt'");
} 

if($cron_job==""){
	//pre($_REQUEST['start_table']);
	$msg_info = "";
	$close_div = false;
	
	if($start_table=="account_payments"){
		$msg_info="Level 1/7 - Adjustment, Over Adjustment and Returned Check transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="paymentswriteoff"){
		$msg_info="Level 2/7 - Write Off and Discount transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="patient_charges_detail_payment_info"){
		$msg_info="Level 3/7 - Paid, Negative Payment, Interest Payment and Deposit transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else if($start_table=="creditapplied"){
		$msg_info="Level 4/7 - Refund and Credit/Debit transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
	}else{
		$msg_info="All transactions processed successfully.";
		$close_div = true;
	}
?>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_table" value="<?php echo $start_table; ?>">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
		<input type="hidden" name="show_start_val" value="<?php print $_REQUEST['show_start_val'] + $end; ?>">
	</form>
	<script>
		top.fmain.$('#report_create_div .modal-body .alert-info').html('<?php echo $msg_info; ?>');
		<?php 
			if($close_div){ ?>
			top.fmain.$('#report_create_div .modal-body #div_loading_image').addClass('hide');
		<?php		
			}
		?>
	</script>
<?php
	if($start_table!=""){
?>
		<script type="text/javascript">
            document.submit_frm.submit();
        </script>
<?php
	}
}
?>