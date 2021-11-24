<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[] = "ALTER TABLE `in_order_details` ADD `contact_sphere_min` VARCHAR( 20 ) NOT NULL AFTER `contact_cat_id`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_sphere_max` VARCHAR( 20 ) NOT NULL AFTER `contact_sphere_min`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_cylinder_min` VARCHAR( 20 ) NOT NULL AFTER `contact_sphere_max`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_cylinder_max` VARCHAR( 20 ) NOT NULL AFTER `contact_cylinder_min`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_axis_min` VARCHAR( 20 ) NOT NULL AFTER `contact_cylinder_max`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_usage` INT( 11 ) NOT NULL AFTER `contact_axis_min`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_type` VARCHAR( 20 ) NOT NULL AFTER `contact_usage`;";
$sql[] = "ALTER TABLE `in_order_details` ADD `contact_disinfecting` INT( 11 ) NOT NULL AFTER `contact_type`;";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br>Update 56 run successfully...</div>';	
}
?>