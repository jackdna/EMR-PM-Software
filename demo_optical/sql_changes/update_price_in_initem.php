<?php 
//update in item wholesale and purchase price from latest lot	
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$update=0;

$q=imw_query("select id, wholesale_cost, purchase_price from in_item where del_status=0 order by id asc");
while($d=imw_fetch_object($q))
{
	$qLot=imw_query("select wholesale_price,purchase_price from in_item_lot_total where item_id='$d->id' and (wholesale_price<>'' OR purchase_price<>'') order by id desc limit 0,1");
	if(imw_num_rows($qLot)>0)
	{
		$dLot=imw_fetch_assoc($qLot);
		$wholesale_price=$dLot[wholesale_price];
		$purchase_price=$dLot[purchase_price];
		if($d->wholesale_cost!=$wholesale_price || $d->purchase_price!=$purchase_price){
			$update_query="update in_item set wholesale_cost='$wholesale_price', purchase_price='$purchase_price' where id = '$d->id'";
			imw_query($update_query)or die(imw_error());
			$update++;
			//echo "$d->wholesale_price!=$wholesale_price || $d->purchase_price!=$purchase_price then ".$update.': '.$update_query.'<br/>';
		}
	}
}
if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Purchase and wholesale price updated for ('.$update.') items successfully</div>';
}

?>
