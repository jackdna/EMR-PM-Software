<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[] = "Rename table in_contact_options to in_options";
$sql[]="ALTER TABLE `in_options` ADD `module_id` ENUM('3','2','1','5','6','7','0') NOT NULL COMMENT 'Option(1=Frames,2=Lenses,3=Contact Lenses,5=Supplies,6=Medicines,7=Accessories,0=Global)' AFTER `opt_type`";
$sql[]="UPDATE `in_options` SET `module_id`= '0' WHERE opt_type in(1,2)";

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
	echo '<div style="color:green;"><br><br>Update 59 run successfully...</div>';	
}

?>