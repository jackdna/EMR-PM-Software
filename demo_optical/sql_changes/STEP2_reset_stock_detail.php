<?php 
/* 
 * Purpose: add an manual entry in_stock_detail to set stock log as per we have qty in in_item_loc_total table
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

//get reason id from reason list
$q1=imw_query("select id from in_reason where reason_name='Manual Reset'");
$d1=imw_fetch_object($q1);
$reason_id=$d1->id;

//total records
$totrec = 0;
if(isset($_REQUEST['totRec']) && !empty($_REQUEST['totRec'])){
	$totrec = $_REQUEST['totRec'];
}else{
	$getTotRec = "SELECT COUNT(id) as totRec FROM in_item_loc_total";
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
	
	$loc_q=imw_query("select * from in_item_loc_total ORDER BY id LIMIT $startFrom, $endTo")or die(imw_error().' ln51');
	while($loc=imw_fetch_object($loc_q))
	{
		//get sum of stock detail aganist loc wise item
		$stock_q=$query_str=$minus_val="";
		$stock_q=imw_query("SELECT SUM(CASE WHEN `trans_type` = 'add' THEN CONCAT('+',Stock)
							 WHEN `trans_type` = 'minus' THEN CONCAT('-',Stock)
							 END)  As `StockVal` FROM `in_stock_detail` where item_id=$loc->item_id and loc_id=$loc->loc_id GROUP BY `item_id`,`loc_id`")or die(imw_error().' ln55');
		$stock=imw_fetch_object($stock_q);
		$query_str=", item_id='$loc->item_id', loc_id='$loc->loc_id', reason='$reason_id', operator_id='1', entered_date='".date('Y-m-d')."', entered_time='".date('H:i:s')."', source='Reset'";
		
		if($stock->StockVal>$loc->stock)
		{
			//add manual entry for minus
			$minus_val=$stock->StockVal-$loc->stock;
			imw_query("insert into in_stock_detail set stock='$minus_val', trans_type='minus' $query_str")or die(imw_error().' ln71');
		}
		elseif($stock->StockVal<$loc->stock)
		{
			//add manual entry for add
			$plus_val=$loc->stock-$stock->StockVal;
			imw_query("insert into in_stock_detail set stock='$plus_val', trans_type='add' $query_str")or die(imw_error().' ln77');
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
