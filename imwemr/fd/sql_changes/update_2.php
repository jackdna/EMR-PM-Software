<?php 
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);
$start_val=0;
if($_REQUEST['start_val']>0){
	$start_val = $_REQUEST['start_val'];
}
$end = 5000;
$qry=imw_query("select * from paymentswriteoff order by write_off_id asc limit $start_val , $end");	
while($row=imw_fetch_array($qry)){
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
	
	$chk_wrt=imw_query("select id from account_trans where parent_id='$write_off_id' and payment_type in('Write Off','Discount')");
	if(imw_num_rows($chk_wrt)==0){
		$charge_list_id=0;
		if($charge_list_detail_id>0){
			$chl_qry=imw_query("select charge_list_id from patient_charge_list_details where charge_list_detail_id='$charge_list_detail_id'");
			$chl_row=imw_fetch_array($chl_qry);
			$charge_list_id=$chl_row['charge_list_id'];
		}
		$rs=imw_query("INSERT INTO account_trans set patient_id='$patient_id',encounter_id='$encounter_id',charge_list_id='$charge_list_id',
					charge_list_detail_id='$charge_list_detail_id',payment_by='$payment_by',ins_id='$write_off_by_id',payment_amount='$write_off_amount',
					payment_date='$write_off_date',operator_id='$write_off_operator_id',entered_date='$entered_date',payment_code_id='$write_off_code_id',del_status='$delStatus',
					del_operator_id='$del_operator_id',del_date_time='$del_date_time',modified_date='$modified_date',modified_by='$modified_by',payment_type='$paymentStatus',
					batch_id='$batch_id',cap_main_id='$cap_main_id',era_amt='$era_amt',parent_id='$write_off_id'");
	}
}

$msg_info[] = "<br><b>".($start_val + $end)." Writeoff Data Import Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Writeoff Data Import</title>
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