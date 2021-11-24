<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();
imw_query("ALTER TABLE  `in_batch_records` ADD  `purchase_price_flag` TINYINT NOT NULL ,
ADD  `purchase_price` DOUBLE( 12, 2 ) NOT NULL") or $errors[] = imw_error();

imw_query("ALTER TABLE  `in_batch_records` ADD  `wholesale_price` DOUBLE NOT NULL") or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 130 run successfully. '.$rec.' records updated</div>';
}

?>