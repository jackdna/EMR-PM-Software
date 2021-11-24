<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');

if(empty($start_val) == true){
	$start_val = 0;
}
$end = 1;

//---- UPDATE PATIENT CHARGE LIST FOR PATIENT DUE AND INSURANCE DUE -----

$qry = imw_query("select report_trans_id,master_tbl_id,trans_type,encounter_id from report_enc_trans where trans_operator_id='0' ORDER BY report_trans_id");
$qryRes = imw_fetch_array($qry);
echo $report_trans_id = $qryRes['report_trans_id'];
$operator_id="";
if(strtolower($qryRes['trans_type'])=="charges"){
	$qry2 = imw_query("select operator_id from patient_charge_list_details where charge_list_detail_id='".$qryRes['master_tbl_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['operator_id'];
}else if(strtolower($qryRes['trans_type'])=="copay" || strtolower($qryRes['trans_type'])=="copay-negative payment" || strtolower($qryRes['trans_type'])=="copay-paid" || strtolower($qryRes['trans_type'])=="deposit" || strtolower($qryRes['trans_type'])=="interest payment" || strtolower($qryRes['trans_type'])=="negative payment" || strtolower($qryRes['trans_type'])=="paid"){
	$qry2 = imw_query("select operator_id from patient_charges_detail_payment_info where  payment_details_id='".$qryRes['master_tbl_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['operator_id'];
}else if(strtolower($qryRes['trans_type'])=="credit" || strtolower($qryRes['trans_type'])=="debit"){
	$qry2 = imw_query("select operatorApplied from creditapplied where crAppId='".$qryRes['master_tbl_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['operatorApplied'];
}else if(strtolower($qryRes['trans_type'])=="returned check"){
	$qry2 = imw_query("select operator_id from account_payments where id='".$qryRes['master_tbl_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['operator_id'];
}else if(strtolower($qryRes['trans_type'])=="write Off"){
	$qry2 = imw_query("select write_off_operator_id from paymentswriteoff where write_off_id='".$qryRes['master_tbl_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['write_off_operator_id'];
}else if(strtolower($qryRes['trans_type'])=="default_writeoff"){
	$qry2 = imw_query("select write_off_operator_id from defaultwriteoff where write_off_id='".$qryRes['master_tbl_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['write_off_operator_id'];
}
if($operator_id<=0){
	$qry2 = imw_query("select operator_id from patient_charge_list where encounter_id='".$qryRes['encounter_id']."'");
	$qryRes2 = imw_fetch_array($qry2);
	$operator_id=$qryRes2['operator_id'];
}
if($operator_id>0){
	imw_query("update report_enc_trans set trans_operator_id='$operator_id' where report_trans_id='$report_trans_id' and trans_operator_id='0'");
}
$msg_info[] = "<br><b>".($start_val + $end)." Records Updates operator id in report table run Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Updates operator id in report table </title>
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