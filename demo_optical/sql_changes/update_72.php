<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_batch_records` ADD  `in_fac_prev_qty` INT NOT NULL AFTER  `in_item_quant`";
$sql[]="ALTER TABLE  `in_batch_records` ADD  `prev_tot_qty` INT NOT NULL AFTER  `in_fac_prev_qty`";
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
	echo '<div style="color:green;"><br><br>Update 72 run successfully...</div>';	
}

?>