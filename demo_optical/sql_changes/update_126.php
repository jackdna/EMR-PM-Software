<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "ALTER TABLE `in_order_details` ADD COLUMN `dominant_eye` CHAR( 2 ) NOT NULL COMMENT 'Contact Lens order - dominant eye'";
$sql[] = "ALTER TABLE `in_order_details` ADD COLUMN `fit_type` TINYINT( 1 ) NOT NULL COMMENT 'Contact Lens order - order fit type'";

$sql[] = "ALTER TABLE `in_lens_lab_detail` ADD COLUMN `vw_user_id` INT( 11 ) NOT NULL COMMENT 'VW user user id, to which location is linked with.'";

foreach($sql as $qry){
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 126 run successfully...</div>';
}

?>