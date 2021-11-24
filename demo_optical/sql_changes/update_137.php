<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

$sql[] = "ALTER TABLE  `in_batch_table` ADD  `del_status` TINYINT NOT NULL ,
ADD  `del_by` INT NOT NULL ,
ADD  `del_on` DATETIME NOT NULL";
//$sql[] = "ALTER TABLE  `in_item_lot_total` ADD  `retail_price` DOUBLE(12, 2) NOT NULL AFTER `wholesale_price`";

foreach($sql as $qry)
{
	imw_query($qry) or $errors[] = imw_error();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 137 run successfully</div>';
}

?>
