<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');
require_once("../../../../library/classes/billing_functions.php");
require_once("../../../../library/classes/class.electronic_billing.php");

if(empty($start_val) == true){
	$start_val = 0;
}

if($start_val>0){
	$whr="and id<$start_val";
}

$get_era = imw_query("SELECT * FROM electronicfiles_tbl WHERE id NOT IN (SELECT electronicFilesTblId FROM era_835_details) and modified_date>='2019-01-01' $whr ORDER BY id DESC");
$get_era_row = imw_fetch_array($get_era);
$electronicFilesTblId = $get_era_row['id'];
$fileContents=$get_era_row['file_contents'];

$startFrom = substr($fileContents, strpos($fileContents, 'TRN'));
$transactionSeg = substr($startFrom, 0, strpos($startFrom, '~'));
$transactionSegArr = explode("*", $transactionSeg);
$trnTypeNumber = $transactionSegArr[2];

$isa_startFrom = substr($fileContents, strpos($fileContents, 'ISA'));
$isa_transactionSeg = substr($isa_startFrom, 0, strpos($isa_startFrom, '~'));
$isa_transactionSegArr = explode("*", $isa_transactionSeg);
$interchange_sender_no = $isa_transactionSegArr[13];

$Duplicte_file=0;
$getDuplicteChkStr_isa = "SELECT interchange_sender_no FROM era_835_details WHERE interchange_sender_no = '$interchange_sender_no'";
$getDuplicteChkQry_isa = imw_query($getDuplicteChkStr_isa);
if(imw_num_rows($getDuplicteChkQry_isa)>0){
	$getDuplicteChkStr = "SELECT TRN_payment_type_number FROM era_835_details WHERE TRN_payment_type_number = '$trnTypeNumber'";
	$getDuplicteChkQry = imw_query($getDuplicteChkStr);
	if(imw_num_rows($getDuplicteChkQry)>0){	
		$Duplicte_file=imw_num_rows($getDuplicteChkQry_isa);
	}
}
if($Duplicte_file>0){			
	$electronicFilesTblId = 'File Already Exists.';
}else{
	include('../../../../interface/billing/createERA835DB.php');
}

$msg_info[] = "<br><b>".($electronicFilesTblId." - ".$get_era_row['file_name'])." ERA Added Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Encounter Payment Paid Flag</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_val" value="<?php print $get_era_row['id']; ?>">
	</form>
	<?php
	if(imw_num_rows($get_era) > 0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>