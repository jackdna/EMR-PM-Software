<?php 
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);
$start_val=0;
if($_REQUEST['start_val']>0){
	$start_val = $_REQUEST['start_val'];
}
$end = 100;
$qry=imw_query("SELECT * from account_trans where charge_list_detail_id='0' and payment_type in('paid','Negative Payment','Interest Payment','Deposit') order by id asc limit $start_val , $end");	
while($row=imw_fetch_array($qry)){
	$id=$row['id'];
	$encounter_id=$row['encounter_id'];
	$charge_list_detail_id=$row['charge_list_detail_id'];
	$copay_chld_id="";
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
	if($copay_chld_id>0){
		imw_query("update account_trans set copay_chld_id='$copay_chld_id' where copay_chld_id='0' and id='$id' and payment_type in('paid','Negative Payment','Interest Payment','Deposit')");
	}
}

$msg_info[] = "<br><b>".($start_val + $end)." Payments Data Import Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Copay Payments Data Import</title>
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