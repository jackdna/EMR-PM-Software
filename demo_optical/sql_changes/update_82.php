<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_location` ADD  `tax_label`  VARCHAR( 100 ) NOT NULL";
$sql[]="ALTER TABLE  `in_item` ADD  `finish_type_other` VARCHAR( 100 ) NOT NULL AFTER  `finish_type`";
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
	echo '<div style="color:green;"><br><br>Update 82 run successfully...</div>';	
}

?>