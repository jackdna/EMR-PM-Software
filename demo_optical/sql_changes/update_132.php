<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();
imw_query("ALTER TABLE `paymentswriteoff` ADD `optical_order_detail_id` INT(11) NOT NULL") or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 132 run successfully</div>';
}

?>