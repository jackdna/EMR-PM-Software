<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_optical_order_form` DROP  `sel_cat_npd_od`";
$sql[]="ALTER TABLE  `in_optical_order_form` DROP  `sel_cat_dpd_od`";
$sql[]="ALTER TABLE  `in_optical_order_form` DROP  `sel_cat_dpd_os`";
$sql[]="ALTER TABLE  `in_optical_order_form` DROP  `sel_cat_npd_os`";

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
	echo '<div style="color:green;"><br><br>Update 76 run successfully...</div>';	
}

?>