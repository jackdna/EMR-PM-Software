<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sel = imw_query("select `lens_item_name` from `in_lens_items_detail` where lens_item_name in ('lens','material','a_r','transition','polarization','tint','uv400','other')");
$num = imw_num_rows($sel);
if($num==0)
{
	$rs=imw_query("INSERT INTO `in_lens_items_detail` (`id`, `lens_item_name`) VALUES('1', 'lens'),('2', 'material'),('3', 'a_r'),('4', 'transition'),('5', 'polarization'),('6', 'tint'),('7', 'uv400'),('8', 'other')") or die(imw_error());
	if($rs){
		echo 'Query Executed Successfuly';
	}else{
		echo 'Error in Query.<br>'.$rs;
	}
}
else
{
	echo 'Data is already Exists';
}
?>