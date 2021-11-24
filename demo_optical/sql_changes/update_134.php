<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "CREATE TABLE IF NOT EXISTS `in_user_notes` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`note` VARCHAR( 255 ) NOT NULL ,
`user_id` INT NOT NULL ,
`dated` DATETIME NOT NULL
) ENGINE = MYISAM ;";

$sql[] = "ALTER TABLE  `in_user_notes` ADD  `patient_id` BIGINT NOT NULL ,
ADD  `patient_name` VARCHAR( 100 ) NOT NULL ,
ADD  `deleted_by` INT NOT NULL ,
ADD  `deleted_on` DATETIME NOT NULL ,
ADD  `status` TINYINT NOT NULL";

//$sql[] = "update `in_user_notes` set status=1";
$sql[] = "ALTER TABLE  `in_user_notes` ADD `user_name` VARCHAR( 20 ) NOT NULL";
	
foreach($sql as $qry){
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 134 run successfully...</div>';
}

?>