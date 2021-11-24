<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();


$sql[]="ALTER TABLE  `in_order_details` CHANGE  `upc_code_os`  `upc_code_os` VARCHAR( 250 ) NOT NULL";
$sql[]="update in_order_details set upc_code_os=upc_code where `module_type_id` =3 and item_name_os=item_name and upc_code_os!=upc_code";
$sql[]="ALTER TABLE  `in_lens_type_vcode` CHANGE  `entry_type`  `entry_type` INT( 11 ) NOT NULL COMMENT  '1 = Seg Type, 2 = Prism, 3 = oversized lens'";

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
    echo '<div style="color:green;"><br><br>Update 140 run successfully</div>';
}

?>
