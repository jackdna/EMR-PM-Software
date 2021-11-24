<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');

if(empty($start_val) == true){
	$start_val = 0;
}
$end = 1;

$enc_pay_qry = imw_query("SELECT * FROM  `patient_chargesheet_payment_info` WHERE  (`optical_order_id` >0 or markPaymentDelete>0) order by encounter_id desc limit $start_val , $end");
if(imw_num_rows($enc_pay_qry)>0){
	$enc_pay_row=imw_fetch_array($enc_pay_qry);
	$encounter_id=$enc_pay_row['encounter_id'];

	$prnt= $prnt."Encounter Corrected - Payment(".$encounter_id.")<br><br>";
	
	set_payment_trans($encounter_id);
	patient_proc_bal_update($encounter_id);
	patient_bal_update($encounter_id);
}


?>
<html>
<head>
<title>Mysql Updates - update wrong transaction</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2">
    	<?php 
			if($enc_rec<1){$enc_rec=0;}
			echo "Current Encounter Id: ".$encounter_id."<br><br>";
			echo $prnt;
		?>
    </font>
	<form action="update_patient_balance.php?start_val=<?php print $start_val + $end; ?>" method="post" name="submit_frm" id="submit_frm">
    	<input type="hidden" name="chk_start_val" value="<?php print $start_val + $end; ?>">
		<input type="hidden" name="enc_rec" value="<?php print $enc_rec ; ?>">
		<input type="hidden" name="prnt" value="<?php print $prnt ; ?>">
		<input type="hidden" name="old_enc" value="<?php print $encounter_id ; ?>">
	</form>
	<?php	
	
	if(imw_num_rows($enc_pay_qry)>0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>