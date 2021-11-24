<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();

$sql = "ALTER TABLE  `in_lens_type_vcode` ADD  `entry_type` INT NOT NULL COMMENT  '1 = Seg Type, 2 = Prism'";
$resp = imw_query($sql) or $errors[] = imw_error();

if( $resp )
{
	$sql = "UPDATE `in_lens_type_vcode` SET `entry_type` = 1 where `lens_type_id` != 0 ";
	imw_query($sql) or $errors[]= imw_error();
}

$sql = "ALTER TABLE  `in_order_lens_price_detail` CHANGE  `qty`  `qty` FLOAT( 11 ) NOT NULL";
imw_query($sql) or $errors[] = imw_error();

$sql = "ALTER TABLE  `in_order` ADD  `due_date` DATE NOT NULL";
imw_query($sql) or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 134 run successfully</div>';
}

?>