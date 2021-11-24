<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[] = "ALTER TABLE `in_item` ADD `cl_type` VARCHAR( 255 ) NOT NULL COMMENT 'Contact Lens Type' AFTER `class_id`;";
$sql[] = "ALTER TABLE `in_item` ADD `cl_wear_schedule` VARCHAR( 255 ) NOT NULL COMMENT 'Contact Wear Scheduler' AFTER `class_id`;";
$sql[] = "ALTER TABLE `in_item` ADD `cl_packaging` INT( 11 ) NOT NULL COMMENT 'Contact Lens Packaging' AFTER `cl_wear_schedule`;";
$sql1 = "UPDATE `in_item` SET `cl_wear_schedule`=`class_id` WHERE `module_type_id`='3';";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)==0){
	imw_query($sql1) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br>Update 55 run successfully...</div>';	
}
?>