<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[]="ALTER TABLE  `in_order_details` ADD  `item_prac_code_default` VARCHAR( 255 ) NOT NULL COMMENT  'Saving Default Practice Code' AFTER  `item_prac_code`";
$sql[]="ALTER TABLE  `in_order_lens_price_detail` ADD  `item_prac_code_default` VARCHAR( 255 ) NOT NULL COMMENT  'Saving Default Practice Code' AFTER  `item_prac_code`";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 64 run successfully...</div>';	
}

?>