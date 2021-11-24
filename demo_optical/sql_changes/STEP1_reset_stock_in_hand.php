<?php 
/* 
 * Purpose: Reset item qty in in_item, loc_total, lot_total, stock_detail
 */

$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();

//end
$endTo = 500;
$startFrom = 0;
if((isset($_REQUEST['st']) == true) && (empty($_REQUEST['st']) == false)){
	$startFrom = $_REQUEST['st'];
}

//total records
$totrec = 0;
if(isset($_REQUEST['totRec']) && !empty($_REQUEST['totRec'])){
	$totrec = $_REQUEST['totRec'];
}else{
	$getTotRec = "SELECT COUNT(id) as totRec FROM in_item";
	$rsGetTotRec = imw_query($getTotRec)or die(imw_error().' ln24');
	if($rsGetTotRec){
		if(imw_num_rows($rsGetTotRec)>0){
			$rowGetTotRec = imw_fetch_array($rsGetTotRec);
			$totrec= $rowGetTotRec["totRec"];	
		}
	}
}

if($totrec == 0){
?>
	<font face="Arial, Helvetica, sans-serif" size="2">
		<span align='center' class='failureMsg'>STEP 1: Nothing found.</span>
	</font>
<?php	
}
else if($startFrom >= $totrec){
	//show success message over here
	echo'Records updated successfully';
}
else
{
	//code for processing
	$msg = "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> records.</b>"; 
	echo $msg;
	
	$item_q=imw_query("select id, qty_on_hand, retail_price from in_item ORDER BY id LIMIT $startFrom, $endTo")or die(imw_error().' ln52');
	while($item=imw_fetch_object($item_q))
	{
		$qty_in_hand=0;
		$loc_q=imw_query("select * from in_item_loc_total where item_id=$item->id");
		while($loc=imw_fetch_object($loc_q))
		{
			$lot_q=imw_query("select SUM(stock) as total_qty from in_item_lot_total where item_id=$loc->item_id and loc_id=$loc->loc_id");
			$lot=imw_fetch_object($lot_q);
			if($lot->total_qty>$loc->stock)
			{
				//update loc total
				$qty_in_hand+=$lot->total_qty;
				imw_query("update in_item_loc_total set stock='$lot->total_qty' where item_id=$item->id and loc_id=$loc->loc_id")or die(imw_error().' ln64');
			}
			elseif($lot->total_qty<$loc->stock)
			{
				//add custom #lot in lot table
				$stock_diff=$loc->stock-$lot->total_qty;
				$qty_in_hand+=$loc->stock;
				$new_lot_no="CUS-$loc->item_id-$loc->loc_id-".date('ymd');
				imw_query("INSERT INTO `in_item_lot_total` set `item_id`='$loc->item_id', 
							`loc_id`='$loc->loc_id', 
							`lot_no`='$new_lot_no', 
							`stock`='$stock_diff'")or die(imw_error().' ln75');
			}else{$qty_in_hand+=$lot->total_qty;}
		}
		if($item->qty_on_hand!=$qty_in_hand)
		{
			//update in_item qty on hand
			$new_amt = $item->retail_price*$qty_in_hand;
			imw_query("update in_item set qty_on_hand='$qty_in_hand', amount='$new_amt' where id=$item->id")or die(imw_error().' ln80');
		}
	}
	?>
	<form action="" method="get" name="frmApptRecord">
		<input type="hidden" name="st" value="<?php echo intval($startFrom)+$endTo; ?>"/>			
		<input type="hidden" name="totRec" value="<?php echo intval($totrec); ?>"/>		
	</form>
	<script language="javascript">
		document.frmApptRecord.submit();
	</script>
	<?php
}
