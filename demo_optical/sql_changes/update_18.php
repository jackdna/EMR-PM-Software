<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sel = imw_query("select * from in_lens_items_detail where lens_item_name in ('progressive','edge','color')");
if(imw_num_rows($sel)==0)
{
	$rs=imw_query("INSERT INTO `in_lens_items_detail` (`id`, `lens_item_name`)VALUES ('9', 'progressive'),('10', 'edge'),('11', 'color')") or die(imw_error());
}
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>