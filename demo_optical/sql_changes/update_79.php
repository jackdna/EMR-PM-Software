<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `axis_od_va` VARCHAR(50) NOT NULL AFTER  `axis_od`";
$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `axis_os_va` VARCHAR(50) NOT NULL AFTER  `axis_os`";
$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `add_od_va` VARCHAR(50) NOT NULL AFTER  `add_od`";
$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `add_os_va` VARCHAR(50) NOT NULL AFTER  `add_os`";

$sql[]="ALTER TABLE  `in_order` ADD  `tax_prac_code` VARCHAR(50) DEFAULT ''";
$sql[]="ALTER TABLE  `in_order` ADD  `tax_rate` DOUBLE(12,2) DEFAULT  '0.00'";
$sql[]="ALTER TABLE  `in_order` ADD  `tax_payable` DOUBLE(12,2) DEFAULT  '0.00'";
$sql[]="ALTER TABLE  `in_order` ADD  `grand_total` DOUBLE(12,2) DEFAULT  '0.00'";
$sql[]="ALTER TABLE  `in_order` ADD  `tax_chld` INT( 11 ) NOT NULL";



$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 79 run successfully...</div>';	
}

?>