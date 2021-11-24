<?php 	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "ALTER TABLE `account_payments` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `payment_deductible` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `patient_chargesheet_payment_info` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `deniedpayment` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `paymentswriteoff` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `defaultwriteoff` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `creditapplied` ADD `facility_id` INT( 11 ) NOT NULL ";

$sql[] = "ALTER TABLE `manual_batch_transactions` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `manual_batch_creditapplied` ADD `facility_id` INT( 11 ) NOT NULL ";

$sql[] = "ALTER TABLE `report_enc_trans` ADD `facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `patient_charge_list` ADD `billing_facility_id` INT( 11 ) NOT NULL ";
$sql[] = "ALTER TABLE `report_enc_detail` ADD `billing_facility_id` INT( 11 ) NOT NULL ";

foreach($sql as $qry)
{
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 143 run successfully</div>';
}

?>
