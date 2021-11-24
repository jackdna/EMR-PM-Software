<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sel = imw_query("select * from in_lens_items_detail where lens_item_name = 'pgx'");
if(imw_num_rows($sel)==0)
{
	$rs=imw_query("INSERT INTO  `in_lens_items_detail` (`id` ,`lens_item_name`)VALUES ('12',  'pgx')") or die(imw_error());
}

$rs1=imw_query("ALTER TABLE  `in_order_details` ADD  `pgx` INT( 2 ) NOT NULL AFTER  `uv400`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>