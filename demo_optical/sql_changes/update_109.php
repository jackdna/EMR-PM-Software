<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql	= array();
$error	= array();

$sql[]	= "ALTER TABLE `in_lens_lab` DROP `vw_user_id`";
$sql[]	= "ALTER TABLE `in_vision_web` ADD `last_import` DATETIME NOT NULL";
$sql[]	= "CREATE TABLE `in_vw_user_lab` (`vw_user_id` INT( 11 ) NOT NULL ,`lab_id` INT( 11 ) NOT NULL) ENGINE = MYISAM ;";
$sql[]	= "ALTER TABLE `in_order_details` ADD `lab_ship_detail_id` INT( 11 ) NOT NULL AFTER `lab_detail_id` ";


foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 109 run successfully...</div>';	
}
?>