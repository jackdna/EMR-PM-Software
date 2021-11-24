<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[] = "ALTER TABLE `in_item` ADD `cl_replacement` VARCHAR(255) NOT NULL AFTER `cl_wear_schedule`;";
$sql[]="ALTER TABLE `in_item` CHANGE `type_id` `type_id` VARCHAR(255) NOT NULL;";
$sql[]="ALTER TABLE `in_item` CHANGE `color` `color` VARCHAR(255) NOT NULL;";
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
	echo '<div style="color:green;"><br><br>Update 61 run successfully...</div>';	
}

?>