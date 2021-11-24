<?php 
$ignoreAuth = true;
if($_REQUEST['start_table']=="account_payments"){
	$msg_info="Level 1/7 - Adjustment, Over Adjustment and Returned Check transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
}else if($_REQUEST['start_table']=="paymentswriteoff"){
	$msg_info="Level 2/7 - Write Off and Discount transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
}else if($_REQUEST['start_table']=="patient_charges_detail_payment_info"){
	$msg_info="Level 3/7 - Paid, Negative Payment, Interest Payment and Deposit transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
}else if($_REQUEST['start_table']=="creditapplied"){
	$msg_info="Level 4/7 - Refund and Credit/Debit transactions are being processed. 0 - ".$_REQUEST['show_start_val'];
}else{
	//$msg_info="All transactions processed successfully.";
}
?>
<html>
<head>
<title>Release 8 Updates 6 </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="green" size="2">
        <strong><?php echo $msg_info;?></strong>
    </font>
</body>
</html>
<?php
$long_end_point=1000;
include("../interface/fd_closed_day.php");
?>
