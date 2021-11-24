<?php 
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);
$start_val=0;
if($_REQUEST['start_val']>0){
	$start_val = $_REQUEST['start_val'];
}
$end = 1000;
$qry=imw_query("SELECT pcdpi.*,pcpi.encounter_id,pcpi.payment_mode,pcpi.checkNo,pcpi.creditCardCo,pcpi.creditCardNo,pcpi.expirationDate,
				 	pcpi.insCompany,pcpi.paymentClaims,pcpi.insProviderId,pcpi.transaction_date
				 	FROM  patient_chargesheet_payment_info as pcpi JOIN patient_charges_detail_payment_info as pcdpi 
					on pcpi.payment_id=pcdpi.payment_id
					order by pcdpi.payment_details_id asc limit $start_val , $end");	
while($row=imw_fetch_array($qry)){
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
	$ins_id=$row['insProviderId'];
	$transaction_date=$row['transaction_date'];
	//$ins_id=$row['insCompany'];
	$paymentClaims=$row['paymentClaims'];
	
	$chk_wrt=imw_query("select id from account_trans where parent_id='$payment_details_id' and payment_type in('paid','Negative Payment','Interest Payment','Deposit')");
	if(imw_num_rows($chk_wrt)==0){
		
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
		
		$rs=imw_query("INSERT INTO account_trans set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
						charge_list_detail_id='$charge_list_detail_id',copay_chld_id='$copay_chld_id',payment_by='$paidBy',
						payment_method='$payment_mode',check_number='$checkNo',cc_type='$creditCardCo',cc_number='$creditCardNo',
						cc_exp_date='$expirationDate',ins_id='$ins_id',payment_amount='$payment_amount',payment_date='$payment_date',
						operator_id='$operator_id',entered_date='$transaction_date',payment_code_id='$payment_code_id',del_status='$del_status',
						del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',
						payment_type='$paymentClaims',batch_id='$batch_id',parent_id='$payment_details_id'");
	}else{
		imw_query("update account_trans set ins_id='$ins_id' where parent_id='$payment_details_id' and parent_id>0 and payment_type in('paid','Negative Payment','Interest Payment','Deposit')");
	}
}

$msg_info[] = "<br><b>".($start_val + $end)." Payments Data Import Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Payments Data Import</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
	</form>
	<?php
	if(imw_num_rows($qry) > 0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>