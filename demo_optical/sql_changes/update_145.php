<?php 	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$qry = "ALTER TABLE  `in_order_details` ADD  `item_name_other` VARCHAR( 100 ) NOT NULL";
imw_query($qry) or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 145: successfull</div>';
}

?>
