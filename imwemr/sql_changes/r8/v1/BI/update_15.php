<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "update patient_charges_detail_payment_info join patient_chargesheet_payment_info on patient_charges_detail_payment_info.payment_id=patient_chargesheet_payment_info.payment_id set 
patient_charges_detail_payment_info.paidDate=patient_chargesheet_payment_info.date_of_payment where patient_charges_detail_payment_info.paidDate!=patient_chargesheet_payment_info.date_of_payment";

foreach(  $sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 15 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 15 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 15</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>