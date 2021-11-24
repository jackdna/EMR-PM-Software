<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_order` ADD  `invoice_no` VARCHAR( 100 ) NOT NULL ,
ADD  `wholesale_price` VARCHAR( 100 ) NOT NULL";
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
	echo '<div style="color:green;"><br><br>Update 84 run successfully...</div>';	
}

?>