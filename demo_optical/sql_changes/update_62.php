<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[]="ALTER TABLE `in_order_details` ADD `cl_packaging_id` INT(11) NOT NULL AFTER `contact_disinfecting`;";
$sql[]="ALTER TABLE `in_order_details` ADD `cl_wear_sch_id` INT(11) NOT NULL AFTER `cl_packaging_id`;";
$sql[]="ALTER TABLE `in_order_details` ADD `cl_replacement_id` INT(11) NOT NULL AFTER `cl_wear_sch_id`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_bc_od` VARCHAR(20) NOT NULL AFTER `contact_axis_min`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_diameter_od` VARCHAR(20) NOT NULL AFTER `contact_bc_od`;";
$sql[]="ALTER TABLE `in_order_details` CHANGE `contact_sphere_min` `contact_sphere_min_od` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
$sql[]="ALTER TABLE `in_order_details` CHANGE `contact_sphere_max` `contact_sphere_max_od` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
$sql[]="ALTER TABLE `in_order_details` CHANGE `contact_cylinder_min` `contact_cylinder_min_od` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
$sql[]="ALTER TABLE `in_order_details` CHANGE `contact_axis_min` `contact_axis_min_od` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
$sql[]="ALTER TABLE `in_order_details` CHANGE `contact_cylinder_max` `contact_cylinder_max_od` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_sphere_min_os` VARCHAR(20) NOT NULL AFTER `contact_diameter_od`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_sphere_max_os` VARCHAR(20) NOT NULL AFTER `contact_sphere_min_os`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_cylinder_min_os` VARCHAR(20) NOT NULL AFTER `contact_sphere_max_os`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_cylinder_max_os` VARCHAR(20) NOT NULL AFTER `contact_cylinder_min_os`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_axis_min_os` VARCHAR(20) NOT NULL AFTER `contact_cylinder_max_os`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_bc_os` VARCHAR(20) NOT NULL AFTER `contact_axis_min_os`;";
$sql[]="ALTER TABLE `in_order_details` ADD `contact_diameter_os` VARCHAR(20) NOT NULL AFTER `contact_bc_os`;";

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
	echo '<div style="color:green;"><br><br>Update 62 run successfully...</div>';	
}

?>