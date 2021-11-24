<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_batch_table` ADD  `user_detail` TEXT NOT NULL AFTER  `facility`";
$sql[]="ALTER TABLE  `in_batch_records` ADD  `reason` INT NOT NULL AFTER  `in_batch_id`";
$sql[]="ALTER TABLE  `in_log_quant_edit` ADD  `batch_rec_id` INT NOT NULL AFTER  `updated_quant`";
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
	echo '<div style="color:green;"><br><br>Update 69 run successfully...</div>';	
}

?>