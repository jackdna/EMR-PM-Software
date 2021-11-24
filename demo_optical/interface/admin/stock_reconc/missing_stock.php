<?php
/*pro_fac_id*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
//echo'<pre>';print_r($_SESSION);die('---------');
if($_POST['reset_stock']=='yes' && $_POST['faclity'] && $_POST['reset_qty'])
{
	//get reason id from reason list
	$q1=imw_query("select id from in_reason where reason_name='Reset on reconcile'");
	$d1=imw_fetch_object($q1);
	$reason_id=$d1->id;
	$loc_id=$_POST['faclity'];
	$item_id_list=implode(',',$_POST['reset_qty']);
	if($item_id_list){
		$q2=imw_query("select stock, item_id, lot_no from in_item_lot_total where item_id IN($item_id_list) and loc_id=$loc_id and stock>0");
		while($d2=imw_fetch_object($q2))
		{
			imw_query("insert into in_stock_detail set stock=$d2->stock, 
			reason='$reason_id', 
			item_id='$d2->item_id', 
			loc_id=$loc_id, 
			trans_type='clearance',
			lot_id='$d2->lot_no',
			operator_id='$_SESSION[authId]',
			entered_date='".date('Y-m-d')."',
			entered_time='".date('H:i:s')."',
			source='Reset'");
			if($d2->stock){
				imw_query("update in_item set qty_on_hand=(qty_on_hand-$d2->stock) where id=$d2->item_id");
			}
		}
		$q3="update in_item_loc_total set stock=0 where item_id IN($item_id_list) and loc_id=$loc_id";
		$q4="update in_item_lot_total set stock=0 where item_id IN($item_id_list) and loc_id=$loc_id and stock>0";

		imw_query($q3)or die(imw_error());
		imw_query($q4)or die(imw_error());

		header("location:missing_stock.php?reset=success");
		exit;
	}
}


if($_POST['compare_stock']=='yes' && strlen($_POST['sel_batch'])>0 && $_POST['faclity'])
{
	$batch_ids=$_POST['sel_batch'];
	//get distinct item ids from reconciled batches
	//echo "select distinct(in_item_id),in_item_quant from in_batch_records where in_batch_id IN($batch_ids)";
	$q=imw_query("select distinct(in_item_id),in_item_quant from in_batch_records where in_batch_id IN($batch_ids)");
	while($d=imw_fetch_object($q))
	{
		$item_scanned[$d->in_item_id]=+$d->in_item_quant;
		if($item_scanned[$d->in_item_id]==0 || trim($item_scanned[$d->in_item_id])=='')$item_scanned[$d->in_item_id]=0;
	}
	//get list of item from inventory whose qty is greater than zero
	$q1=imw_query("select distinct(item_id),stock from in_item_loc_total where loc_id =$_POST[faclity] order by item_id");
	while($d1=imw_fetch_object($q1))
	{
		$item_in_stock[$d1->item_id]=($d1->stock>0)?$d1->stock:0;
	}
	$item_in_stock_temp=$item_in_stock;
	//here we are getting not scanned items
	foreach($item_in_stock as $item_id=>$stock)
	{
		if($stock>0)
		{
			if($item_scanned[$item_id]){
				unset($item_in_stock_temp[$item_id]);
			}
		}
		else
		{
			unset($item_in_stock_temp[$item_id]);
		}
	}
	//keep rest of items in seprate array
	$final_missing_items=$item_in_stock_temp;
	//free up memory
	unset($item_in_stock_temp);
	//now get list of item whose items qty doesn't match with reconciled qty
	foreach($item_in_stock as $item_id=>$stock)
	{
		if($stock!=$item_scanned[$item_id]){
			//$stock_diff_item_arr[$item_id]=$item_id;//if we uncomment this then report will show short/over qty scanned too
		}
		$stock_diff_arr[$item_id]['in_stock']=$stock;
		$stock_diff_arr[$item_id]['scan_stock']=$item_scanned[$item_id];
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">var jQ = jQuery.noConflict();</script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>


<script>
var searchUPc = new Array();
$(document).ready(function(){
	
	var val="";
	var sr_no="";
	
	$("#batches").multiSelect({noneSelected:'Select All'});
	
	$("#selectall").click(function(){
		if($(this).is(":checked")){
			$(".reset_qty").attr('checked', 'checked');
		}else{
			$(".reset_qty").removeAttr('checked');
		}
	});
});
	
	function submitForm(){
		$("#loading").show();
		$("#sel_batch").val($('#batches').selectedValuesString());
		$("#reset_stock").val('');
		$("#compare_stock").val('yes');
		$("#submit").click();
	}
	
	function reset_qty()
	{
		var total_checked=0;
		$(".reset_qty").each(function() {
			if($(this).is(":checked")){
				total_checked++;
			}
		});
		if(parseInt(total_checked)<=0)
		{
			top.falert('Please select item to reset qty');
			return false;
		}
		$("#reset_stock").val('yes');
		$("#compare_stock").val('');
		$("#submit").click();
	}
	
	function show_alert_div(a)
	{
		$('.err_div').css({"display":"block","color":"#F00","width":"200px","position":"absolute","left": "430px","top":"-5px"});
		$(".err_div").html("Qty Reset Successfully");
		$('.err_div').delay(3000).hide(1);
	}
</script>
</head>
<body>
<div class="err_div"></div>
<form name="recon_form" id="recon_form" action="" method="post" style="width: 100%">
<input type="submit" name="submit" id="submit" value="yes" style="display: none">
<input type="hidden" name="compare_stock" id="compare_stock" value="">
<input type="hidden" name="reset_stock" id="reset_stock" value="">
<input type="hidden" name="sel_batch" id="sel_batch" value="">
<div class="mt10 rec_con">
	<div id="searchPart" class="border_rounded" style="width:99.7%; height:auto; height:80px;">          
		<div class="listheading border_top_left border_top_left">Missing item from stock</div>
		<table style="width:100%;border:0px none;" class="btn_cls">
			<tr>
				<td style="width: 370px; text-align: left">
				<?php foreach($_POST['batches'] as $key=>$val)$selectedArr[$val]=$val;?>
					<select name="batches" id="batches" multiple style="width: 350px">
					<?php
					$q=imw_query("select b.save_date, b.id, b.status, l.loc_name as facility from in_batch_table as b
					left join in_location as l on b.facility=l.id
					where b.del_status=0 order by b.`save_date` DESC");
					while($d=imw_fetch_object($q))
					{
						$id=$d->id;
						$selected=($selectedArr[$id])?'selected':'';
						$save_date=date("m-d-Y h:i:s",strtotime($d->save_date));
						$status=($d->status=='updated')?'Reconciled':'In Progress';
						$title=$save_date.'-'.$d->facility.'-'.$status;
						echo"<option value='$id' $selected>$title</option>";
					}
					?>
					</select>
				<div class="label">Batches</div>
				</td>
				<td style="width: 210px; text-align: left">
				<select name="faclity" id="faclity" style="width: 200px">
					<option value="">Select Facility</option>
					<?php $fac_name_qry = imw_query("select id, loc_name from in_location where del_status='0' and loc_name!='' order by loc_name asc");
						  while($fac_name_row = imw_fetch_array($fac_name_qry)) {
							  $sel="";
							  if($_POST['faclity'])
							  {
								   if($_POST['faclity']==$fac_name_row['id'])$sel="selected";
							  }
							  else
							  {
								  if($fac_name_row['id']==$_SESSION['pro_fac_id']){ $sel="selected";  } 
							  }
						  ?>
					<option value="<?php echo $fac_name_row['id'];?>" <?php echo $sel; ?>><?php echo $fac_name_row['loc_name']; ?></option>
					<?php } ?>
				</select>
				<div class="label">Facility</div>
				</td>
				<td style="width: auto; text-align: left; vertical-align: top"><input type="button" name="compare" id="compare" value="Compare" onClick="submitForm();"> </td>
			</tr>
		</table>
		</div>
</div>

<div id="show_comparison" style="width: 100%">
<?php
	$top_bar_ht=450;
	$bottom_bar_btn=0;
?>

<div class="uper_cont" style="height:<?php echo $_SESSION['wn_height']-$top_bar_ht;?>px;overflow:hidden;">

<div style="height:<?php echo $_SESSION['wn_height']-($top_bar_ht+$bottom_bar_btn)?>px;overflow-y:scroll;overflow-x:hidden; width:100%">
   
   <form action="" method="post" name="quan_form" id="quan_form" style="width:100%;">
   <?php
	if($_POST){
	if(sizeof($final_missing_items)>0 || sizeof($stock_diff_item_arr)>0){
	/*Item/Module Types List*/
	$module_arr = array();
	$query2=imw_query("select * from in_module_type");
	while($row2=imw_fetch_array($query2)){
	$module_arr[$row2['id']]=$row2['module_type_name'];
	}
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	 
	$html.='<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tbody><tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="220px">&nbsp;Missing Stock</td>
			<td style="text-align:right;" class="reportHeadBG" width="250px">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i').'&nbsp;</td>
		</tr>
	</tbody></table>';
	$html.='<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<thead>
			<tr class="reportHeadBG">
				<td><input type="checkbox" name="select_all" id="selectall" style="display: none"><label for="selectall">S.No</label></td>
				<td>UPC</td>
				<td>Name</td>
				<td>P. Type</td>
				<td>Scan Status</td>
				<td>In Stock</td>
				<!--<td>Scanned</td>-->
			</tr>
		</thead>
		<tbody>';
	//get item detail from in_item table
	$items_arr1=array_keys($final_missing_items);
	$items_arr2=array_keys($stock_diff_item_arr);
	if(sizeof($items_arr1)>0 && sizeof($items_arr2)>0){
		$items_arr=array_merge($items_arr1,$items_arr2);	
	}elseif(sizeof($items_arr1)>0)$items_arr=$items_arr1;	
	else $items_arr=$items_arr2;
	//free up memory
	unset($items_arr1,$items_arr2);
	$items_str=implode(',',$items_arr);
	if($items_str)
	{
		$item_q=imw_query("select id, upc_code, name, module_type_id from in_item where id IN($items_str) order by id");
		while($item_data=imw_fetch_object($item_q))
		{
			$counter++;
			$in_stock=$scanned=0;
			$scan_status=(isset($final_missing_items[$item_data->id])==true)?'Not Scanned':'Scanned';
			//if($scan_status=='Scanned')
			//{
				$in_stock=$stock_diff_arr[$item_data->id]['in_stock'];
				$scanned=$stock_diff_arr[$item_data->id]['scan_stock'];
			//}
			$html.='<tr>
				<td><input type="checkbox" class="reset_qty" name="reset_qty[]" id="'.$counter.'" value="'.$item_data->id.'">'.$counter.'</td>
				<td>'.$item_data->upc_code.'</td>
				<td>'.$item_data->name.'</td>
				<td>'.$module_arr[$item_data->module_type_id].'</td>
				<td>'.$scan_status.'</td>
				<td>'.$in_stock.'</td>
				<!--<td>'.$scanned.'</td>-->
			</tr>';
		}
	}
	$html.='
	</tbody></table>
	<script>
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Reset Qty","top.main_iframe.admin_iframe.reset_qty();");
	top.btn_show("admin",mainBtnArr);
	</script>
	';
	 echo $html;
		
	}else{echo'No missing item detected';}
	}
   ?>
    
    </form>
    
  </div>
     
      
      </div>
</div>
</form>  
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
<?php
if($_REQUEST['reset']=='success'){
	echo"<script>show_alert_div();</script>";
}
?>

</body>
</html>