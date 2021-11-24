<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();

$sql[] = "CREATE TABLE `in_order_lot_details` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`order_id` INT NOT NULL ,
`order_detail_id` INT NOT NULL ,
`lot_no` VARCHAR( 255 ) NOT NULL ,
`qty` INT( 6 ) NOT NULL ,
`ordered_date` DATE NOT NULL
) ENGINE = MYISAM ;";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 102 run successfully...</div>';	
}
?>

