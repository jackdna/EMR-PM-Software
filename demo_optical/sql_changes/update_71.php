<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `custom_rx` TINYINT( 1 ) NOT NULL";
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
	echo '<div style="color:green;"><br><br>Update 71 run successfully...</div>';	
}

?>