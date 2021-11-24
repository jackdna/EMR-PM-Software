<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");

if($_REQUEST['order_id']!="")
{
$order_id=rtrim($_REQUEST['order_id'],",");
$order_id_array=explode(",",$order_id);
$c="";
	foreach($order_id_array as $id)
	{
		$query=imw_query("UPDATE in_order SET arch_status='archived' WHERE id=".$id);
		if($query)
		$c=1;
	}
	if($c!=="")
	{
		echo $c;
	}
}

if($_REQUEST['un_order_id']!="")
{
	$un_order_id=rtrim($_REQUEST['un_order_id'],",");
	$un_order_id_array=explode(",",$un_order_id);	
	foreach($un_order_id_array as $id)
	{
		$query=imw_query("UPDATE in_order SET arch_status='' WHERE id=".$id);
		if($query)
		$c=0;
	}
	if($c!=="")
	{
		echo $c;
	}
}
?>