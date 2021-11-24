<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "ALTER TABLE `in_location` ADD COLUMN `external_id` VARCHAR( 20 ) NOT NULL COMMENT 'External Location ID to be used in HL7 messages'";

foreach($sql as $qry){
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 127 run successfully...</div>';
}

?>