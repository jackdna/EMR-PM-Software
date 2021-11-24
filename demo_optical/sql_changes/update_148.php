<?php 	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$qry = "ALTER TABLE  `in_order_lens_price_detail` CHANGE  `tax_rate`  `tax_rate` FLOAT( 10, 2 ) NOT NULL DEFAULT  '0'";
imw_query($qry) or $errors[] = imw_error();

$qry = "ALTER TABLE  `in_order_details` CHANGE  `tax_rate`  `tax_rate` FLOAT( 10, 2 ) NOT NULL DEFAULT  '0',
CHANGE  `tax_rate_os`  `tax_rate_os` FLOAT( 10, 2 ) NOT NULL";
imw_query($qry) or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 148: successfull</div>';
}

?>
