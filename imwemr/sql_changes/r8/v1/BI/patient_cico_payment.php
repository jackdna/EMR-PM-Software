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

$qry = imw_query("SELECT * from  check_in_out_payment where del_status='0' and total_payment>0");
while($qryRes = imw_fetch_array($qry)){

	$qry2 = imw_query("SELECT sum(item_payment) as tot_item_payment FROM check_in_out_payment_details WHERE payment_id='".$qryRes['payment_id']."' and status='0'");
	$qryRes2 = imw_fetch_array($qry2);
	
	if($qryRes['total_payment']>$qryRes2['tot_item_payment']){
		
		$created_on = $qryRes['created_on'];
		list($year, $month, $day) = explode('-',$created_on);
		$created_on = $month."-".$day."-".$year;
		
		echo $qryRes['patient_id'].'-'.$created_on.'-'.$qryRes['payment_id'].'-'.$qryRes['total_payment'].'-'.$qryRes2['tot_item_payment'].'<br>';
	}
	
}

$msg_info[] = "<br><b>".($start_val + $end)." Records Updates CI/CO Payment Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - CI/CO Payment Update</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>