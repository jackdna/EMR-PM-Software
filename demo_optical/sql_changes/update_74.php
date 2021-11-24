<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `sel_cat_dpd_od` TEXT NOT NULL AFTER  `base_od`";
$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `sel_cat_npd_od` TEXT NOT NULL AFTER  `dist_pd_od`";
$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `sel_cat_dpd_os` TEXT NOT NULL AFTER  `base_os`";
$sql[]="ALTER TABLE  `in_optical_order_form` ADD  `sel_cat_npd_os` TEXT NOT NULL AFTER  `dist_pd_os`";

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
	echo '<div style="color:green;"><br><br>Update 74 run successfully...</div>';	
}

?>