<?php 
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);
$start_val=0;
if($_REQUEST['start_val']>0){
	$start_val = $_REQUEST['start_val'];
}
$end = 500;
$qry=imw_query("SELECT * FROM  creditapplied order by crAppId asc limit $start_val , $end");	
while($row=imw_fetch_array($qry)){
	
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
	$chk_wrt=imw_query("select id from account_trans where parent_id='$crAppId' and payment_type in('refund','credit','debit')");
	if(imw_num_rows($chk_wrt)==0){
		
		$charge_list_id=0;
		if($crAppliedToEncId>0){
			$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$crAppliedToEncId'");
			$chl_row=imw_fetch_array($chl_qry);
			$charge_list_id=$chl_row['charge_list_id'];
		}
		
		$rs=imw_query("INSERT INTO account_trans set patient_id='$patient_id',encounter_id='$crAppliedToEncId',charge_list_id='$charge_list_id',
						charge_list_detail_id='$charge_list_detail_id',payment_by='$type',payment_method='$payment_mode',check_number='$checkCcNumber',
						cc_type='$creditCardCo',cc_number='$creditCardNo',cc_exp_date='$expirationDateCc',ins_id='$insCompany',payment_amount='$amountApplied',
						payment_date='$dateApplied',operator_id='$operatorApplied',entered_date='$entered_date',del_status='$delete_credit',
						del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',
						payment_type='$payment_type',batch_id='$batch_id',parent_id='$crAppId'");
		
		if($payment_type=="debit"){
			$charge_list_id=0;
			if($crAppliedToEncId>0){
				$chl_qry=imw_query("select charge_list_id from patient_charge_list where encounter_id='$crAppliedToEncId_adjust'");
				$chl_row=imw_fetch_array($chl_qry);
				$charge_list_id=$chl_row['charge_list_id'];
			}
			
			$rs=imw_query("INSERT INTO account_trans set patient_id='$patient_id_adjust',encounter_id='$crAppliedToEncId_adjust',charge_list_id='$charge_list_id',
							charge_list_detail_id='$charge_list_detail_id_adjust',payment_by='$type',payment_method='$payment_mode',check_number='$checkCcNumber',
							cc_type='$creditCardCo',cc_number='$creditCardNo',cc_exp_date='$expirationDateCc',ins_id='$insCompany',payment_amount='$amountApplied',
							payment_date='$dateApplied',operator_id='$operatorApplied',entered_date='$entered_date',del_status='$delete_credit',
							del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',
							payment_type='credit',batch_id='$batch_id',parent_id='$crAppId'");
		}
	}
}

$msg_info[] = "<br><b>".($start_val + $end)." Refund/Credit/Debit Data Import Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Refund/Credit/Debit Data Import</title>
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