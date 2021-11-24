<?php
	/*
	File: return_order_itms.php
	Coded in PHP7
	Purpose: Retrun Order Details
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$patient_id=$_SESSION['patient_session_id'];
	
	//OPERATORS
   $usersRs = imw_query("select id, fname,lname from users");
   while($usersRes=imw_fetch_array($usersRs)){
	   if($usersRes['lname']!='' || $usersRes['fname']!=''){
			$arrUsers[$usersRes['id']]=$usersRes['lname'].', '.$usersRes['fname']; 
			//TWO CHARACTERS
			$opInit = substr($usersRes['lname'],0,1);
			$opInit .= substr($usersRes['fname'],0,1);
			$arrUsersTwoChar[$usersRes['id']] = strtoupper($opInit);
	   }
   }
   
	$tax_label = "Tax";
	$tax_label_qry = imw_query("SELECT `tax_label` FROM `in_location` WHERE `id`='".$_SESSION['pro_fac_id']."'");
	if($tax_label_qry && imw_num_rows($tax_label_qry)>0){
		$tax_lbl = imw_fetch_assoc($tax_label_qry);
		if($tax_lbl['tax_label']!="")
			$tax_label = $tax_lbl['tax_label'];
	}
	
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	if(isset($_POST['return']) && $_POST['return']!="")
	{
		$item_id_arr = $_POST['update_item_id'];
		$orderid = $_POST['orderid'];
		$patient = $_POST['patientid'];
		for($i=0;$i<count($item_id_arr);$i++)
		{
			$single_item = $item_id_arr[$i];
			$status = $_POST['status'];
			$reason = $_POST['reason_ret_'.$single_item];
			$ret_qty = $_POST['return_qty_'.$single_item];
			$item_id = $_POST['itemid_'.$single_item];
			$module_id = $_POST['moduleid_'.$single_item];
			if($reason>0 && $ret_qty>0)
			{
				if($_POST['return']=="Return To Inventory")
				{
					update_item_loc_qty($orderid,$single_item,$patient,$item_id,$ret_qty,$module_id,$reason,$status);
					$upd_ord_det = imw_query("update in_order_details set return_qty=return_qty+$ret_qty, modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='$single_item'");
				}
				
				if($_POST['return']=="Remove Inventory")
				{
					$sel_return_rem = imw_query("select id, return_qty from in_order_return where order_id='$orderid' and order_detail_id='$single_item' and patient_id='$patient' and item_id='$item_id' and status='remove' LIMIT 1");
					if(imw_num_rows($sel_return_rem)>0)
					{
						$get_row_rem = imw_fetch_array($sel_return_rem);
						$act_rem="update";
						$whr_rem=", modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='".$get_row_rem['id']."'";
					}
					else
					{
						$act_rem="insert into";
						$whr_rem=", entered_date='$entered_date', entered_time='$entered_time', entered_by='$operator_id'";
					}
					$ret_qry_rem = imw_query("$act_rem in_order_return set order_id='$orderid', patient_id='$patient', item_id='$item_id', order_detail_id='$single_item', module_type_id='$module_id', return_qty='$ret_qty', reason='$reason', status='$status' $whr_rem");
					$getoid_rem = imw_insert_id();
					
					if(imw_num_rows($sel_return_rem)>0)
					{
						$return_id_rem = $get_row_rem['id'];
					}
					else
					{
						$return_id_rem = $getoid_rem;
					}
					$ins_modifier_rem = imw_query("insert into in_return_modifier set ord_return_id='$return_id_rem', qty='$ret_qty', ret_reason='$reason', ret_status='$status', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id'");
				}
			}
		}
		echo "<script>window.opener.main_iframe.".$_REQUEST['frameName'].".location.href=window.opener.main_iframe.".$_REQUEST['frameName'].".location.href+'&s_v=".$_REQUEST["status_val"]."'; window.close();</script>";
	}
	
	if($_POST['del']!="" && $_POST['del']=="Delete")
	{
		if($_POST['edi_ret_id']!="" && $_POST['edi_ret_id']>0)
		{
			$sel_retun_qry = imw_query("select * from in_order_return where id='".$_POST['edi_ret_id']."' and status='return'");
			if(imw_num_rows($sel_retun_qry)>0)
			{
				$fet_data = imw_fetch_array($sel_retun_qry);
				$rev_qt = $fet_data['return_qty'];
				
				$select_fac = imw_query("select loc_tot.id, loc_tot.stock from in_item_loc_total as loc_tot inner join facility as fac on fac.id='".$fet_data['facility_id']."' inner join in_location as loc on loc.pos=fac.fac_prac_code where loc_tot.loc_id=loc.id and loc_tot.item_id='".$fet_data['item_id']."'");
				$fetch_fac = imw_fetch_array($select_fac);
				
				$upd_itm_loc = imw_query("update in_item_loc_total set stock=stock-$rev_qt where id='".$fetch_fac['id']."'");
				
				$item_qry = imw_query("select retail_price, id, qty_on_hand from in_item where id='".$fet_data['item_id']."'");
				$fch_item_qry = imw_fetch_array($item_qry);
				$new_amt=0;
				$new_qty = $fch_item_qry['qty_on_hand']-$rev_qt;
				if($new_qty>0)
				{
					$new_amt = $fch_item_qry['retail_price']*$new_qty;
				}
				$upitm_qty = imw_query("update in_item set qty_on_hand='$new_qty', amount='$new_amt', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='".$fet_data['item_id']."'");
				
				$upd_ord_det = imw_query("update in_order_details set return_qty=return_qty-$rev_qt, modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='".$fet_data['order_detail_id']."'");
				
				$upd_itm_loc = imw_query("update in_order_return set del_status='2',  del_date='$entered_date', del_time='$entered_time', del_by='$operator_id' where id='".$fet_data['id']."'");
			}
		}
	}
	
	$order_id = $_REQUEST['ord_id'];
			
	$sql = "select ord.id, ord.patient_id, ord.return_qty, ord.module_type_id, ord.upc_code, ord.item_name, ord.item_id, ord.qty, ord.qty_right, ord.price, ord.order_status, ord.pt_wear_pic, mt.module_type_name,ord.total_amount from in_order_details as ord inner join in_module_type as mt on mt.id = ord.module_type_id where ord.order_id='".$order_id."' and ord.del_status='0' and ord.pof_check='0' and ord.order_status='dispensed'";
	$res = imw_query($sql);
	$nums = imw_num_rows($res);
	
	$qExDet=imw_query("select total_overall_discount,tax_payable,tax_pt_paid,grand_total from in_order where id='$order_id'");
	$exDet=imw_fetch_array($qExDet);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script type="text/javascript">
$(document).ready(function(){
	selectCurrentCheck = function(ab){
		$("#checked_"+ab).prop('checked', true);
	}
	
	return_stock = function(){
		var chk_arr =document.getElementsByName("update_item_id[]");
		if($('[name="update_item_id[]"]:checked').length==0)
		{
			top.falert("Please select a record");
				return false;
		}		
		for(var i=0; i<chk_arr.length;i++)
		{
			 if(chk_arr[i].checked==true)
			{
				var ch_val = chk_arr[i].value;
				if($("#reason_ret_"+ch_val).val()=="")
				{
					top.falert("Please select reason");
					return false;
				}
			}
		}
		top.fconfirm('Are you sure to return selected record(s) ?',return_stock_callBack);
		
	}
	
	function return_stock_callBack(result)
	{
		if(result==true)
		{
			$("#status").val('return');
			$("#return").val('Return To Inventory');
			$("#firstform").submit();	
		}
	}
	
	remove_stock = function(){
		var chk_arr =document.getElementsByName("update_item_id[]");
		if($('[name="update_item_id[]"]:checked').length==0)
		{
			top.falert("Please select a record");
				return false;
		}
		for(var i=0; i<chk_arr.length;i++)
		{
			if(chk_arr[i].checked==true)
			{
				var ch_val = chk_arr[i].value;
				if($("#reason_ret_"+ch_val).val()=="")
				{
					top.falert("Please select reason");
					return false;
				}
			}
		}
		top.fconfirm('Are you sure to remove selected record(s) ?',remove_stock_callBack);
	}
	function remove_stock_callBack(result)
	{
		if(result==true)
		{
			$("#status").val('remove');
			$("#return").val('Do Not Add To Inventory');
			$("#firstform").submit();	
		}
	}
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$(".getchecked").prop('checked', true);
		}else{
			$(".getchecked").prop('checked', false);
		}
	});
	var status_selected_val =window.opener.main_iframe.<?php echo $_REQUEST['frameName'];?>.document.getElementById("rec_type2").value;
	if(typeof(status_selected_val)!="NULL"){
		$("#status_val").val(status_selected_val);
	}

});

function close_win()
{
	window.close();
}

function compare_qty(txt_qty,ord_qty,retn_qty)
{
	var qtty = parseInt(txt_qty.value)+parseInt(retn_qty);
	if(qtty>ord_qty)
	{
		top.falert("Entered Qty should be equal or less than ordered Qty.");
		$(txt_qty).val(ord_qty-retn_qty);
	}
}

function del_ret_rw(retn_id, e)
{
	e.preventDefault();
	$("#del_item_id").val(retn_id);
	top.fconfirm('Are you sure to delete selected record ?',del_ret_rw_callBack);
}
function del_ret_rw_callBack(result)
{
	if(result==true)
	{
		var retn_id = $("#del_item_id").val();
		$("#edi_ret_id").val(retn_id);
		$("#del").val('Delete');
		$("#firstform").submit();	
	}
	$("#del_item_id").val('');
	$("#del").val('');
}
$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
<style>
.bg_tr { background:#67B9E8; font-weight:bold; color:#FFFFFF; border:1px solid #fff;}
</style>
</head>
<body>
	<div style="width:820px; margin:0 auto;">
    <form name="addframe" id="firstform" method="post" style="margin:0px;">
       <div class="module_border">
            <div class="listheading"> Detail of Order #<?php echo $order_id; ?></div>
            <table class="table_collapse table_cell_padd2 module_label">
                <tr class="listheading" style="text-align:center;">
                   <td width="10"><input type="checkbox" id="selectall" name="select_all"></td>
                    <td width="80">Category</td>
                    <td width="100">Item Name</td>
                    <td width="110">Ordered Qty.</td>
					<td width="110">Returned Qty.</td>
					<td width="110">Pending Qty.</td>
                    <td width="80">Unit Cost</td>
					<td width="100">T.Unit Cost</td>
                    <td width="80" style="text-align: left;">Reason</td>
                </tr>
         </table>
                <div style="overflow-x:hidden; overflow-y:scroll; height:300px;">
                <table width="800" class="table_collapse table_cell_padd2 module_label">
                <?php 
				if($nums > 0)
				{
					while($row = imw_fetch_array($res)) {
					$rowbg="even";
					$tot_qtty = $row['qty'] + $row['qty_right'];
					$return_qt = 0;
					if($row['return_qty']>0)
					{
						$return_qt = $row['return_qty'];
					}
					$edit_id = 0;
					$return_reason = 0;
					$allowed=$row['price']*$tot_qtty;
					if($row['module_type_id']==2){
						$allowed = $row['total_amount'];
					}
					$sel_ord_return = imw_query("select ord_ret.id, ord_ret.facility_id, ord_ret.return_qty, ord_ret.status, DATE_FORMAT(ord_ret.entered_date, '%m-%d-%Y') as enterdate, rr.return_reason, ord_ret.entered_by from in_order_return as ord_ret inner join in_return_reason as rr on rr.id = ord_ret.reason where ord_ret.order_id='$order_id' and ord_ret.order_detail_id='".$row['id']."' and ord_ret.patient_id='".$row['patient_id']."' and ord_ret.item_id='".$row['item_id']."' and ord_ret.return_qty>0 and ord_ret.del_status='0'");	
				?>
                <tr class="<?php echo $rowbg; ?>">
                     <td width="20" style="text-align:center;"><input type="checkbox" title="Select" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $row['id']; ?>" name="update_item_id[]">
                    </td>
                    <td width="80" style="text-align:center;">
						<input type="hidden" name="orderid" value="<?php echo $order_id; ?>">
						<input type="hidden" name="itemid_<?php echo $row['id']; ?>" value="<?php echo $row['item_id']; ?>">
						<input type="hidden" name="patientid" value="<?php echo $row['patient_id']; ?>">
						<input type="hidden" name="moduleid_<?php echo $row['id']; ?>" value="<?php echo $row['module_type_id']; ?>">
						<?php echo ucfirst($row['module_type_name']); ?></td>
                    <td width="200" style="text-align:center;"><?php echo $row['item_name']; ?></td>
                    <td width="95" style="text-align:center;"><?php echo $tot_qtty; ?></td>
					<td width="95" style="text-align:center;"><?php echo $return_qt; ?></td>
					<td width="95" style="text-align:center;"><input name="return_qty_<?php echo $row['id']; ?>" type="text" value="<?php echo $tot_qtty-$return_qt; ?>" style="width:60px;" onChange="compare_qty(this,'<?php echo $tot_qtty; ?>','<?php echo $return_qt; ?>'); selectCurrentCheck('<?php echo $row['id']; ?>')" /></td>
                    <td width="78" style="text-align:right;"><?php echo $row['price']; ?></td>
					<td width="98" style="text-align:right;"><?php echo number_format($allowed,2); ?></td>
                    <td width="80" style="text-align:center;">
					<select name="reason_ret_<?php echo $row['id']; ?>" style="width:80px;" onChange="selectCurrentCheck('<?php echo $row['id']; ?>')" id="reason_ret_<?php echo $row['id'];?>">
						<option value="">-Select-</option>
                   		<?php $sel_reas = imw_query("select * from in_return_reason where del_status='0'");
						if(imw_num_rows($sel_reas)>0)
						{
							while($get_rows = imw_fetch_array($sel_reas))
							{ 
							?>
								<option value="<?php echo $get_rows['id']; ?>"><?php echo $get_rows['return_reason']; ?></option>
					<?php	}
						}
					 ?>
					</select>
                    </td>
                </tr>
                <?php
				if(imw_num_rows($sel_ord_return) > 0)
				{?>
				<tr>
					<td colspan="9">
						<table class="table_collapse table_cell_padd2 module_label">
							<tr class="bg_tr"><td colspan="7" align="center">Return Order Detail</td></tr>
							<tr class="bg_tr" style="text-align:center;">
								<td width="100">Returned Qty</td>
								<td width="100">Return Date</td>
								<td width="120">Facility Name</td>
								<td width="150">Reason</td>
								<td width="80">Status</td>
								<td width="80" style="text-align: left;">Operator</td>
								<td width="80">&nbsp;</td>
							</tr>
							<?php while($fetch_ord_return = imw_fetch_array($sel_ord_return))
								  {
									$edit_id = $fetch_ord_return['id'];
									$sel_faclty = imw_query("select name from facility where id='".$fetch_ord_return['facility_id']."'");
									$feth_facl = imw_fetch_array($sel_faclty);
							?>
							<tr style="text-align:center;">
								<td width="100"><?php echo $fetch_ord_return['return_qty']; ?></td>
								<td width="100"><?php echo $fetch_ord_return['enterdate']; ?></td>
								<td width="120"><?php echo $feth_facl['name']; ?></td>
								<td width="150"><?php echo $fetch_ord_return['return_reason']; ?></td>
								<td width="110"><?php echo ucfirst($fetch_ord_return['status']); ?></td>
								<td width="80"><?php echo $arrUsersTwoChar[$fetch_ord_return['entered_by']]; ?></td>
								<td width="80" class="btn_cls">
								<?php if($fetch_ord_return['status']=="return") { ?><input type="submit" name="del" value="Delete" style="width:70px;" onClick="del_ret_rw('<?php echo $edit_id; ?>', event);"/>
								<?php } ?>
								</td>
							</tr>
							<?php } ?>		
						</table>
					</td>
				</tr>		
				<?php }	} } ?>
				 <tr>
					<td style="text-align:right;" colspan="7">
						<strong style="margin-left:10px;"><?php echo $tax_label; ?> : </strong>
					</td>
					<td style="text-align:right;">
						<?php echo $exDet['tax_payable']; ?>
					</td>
					<td style="text-align:center;">&nbsp;</td>
				</tr>
				 <tr>
				 	<td style="text-align:right;" colspan="7">
						<strong style="margin-left:8px;">Grand Total : </strong>
					</td>
					<td style="text-align:right;">
						<?php echo $exDet['grand_total']; ?>
					</td>
					<td style="text-align:center;">&nbsp;</td>
				</tr>
            </table>
         </div>
       </div>
	   <input type="hidden" name="edi_ret_id" id="edi_ret_id" value="" />
	   <input type="hidden" name="status" id="status" value="" />
       <input type="hidden" name="return" id="return" value="" />
	   <input type="hidden" name="del" id="del" value="" />
	   <input type="hidden" name="status_val" id="status_val" value="" />
        <div class="btn_cls mt10">
			<input type="button" name="returnToInv" value="Return To Inventory" onClick="return return_stock();"/>
			<input type="button" name="doNotReturnToInv" value="Do Not Add To Inventory" onClick="return remove_stock();"/>
			<input type="button" name="cancel" value="Cancel" onClick="close_win();" />                        
        </div>
        </form>
<!-- Element to hold delete item id for falert callback -->
<input type="hidden" id="del_item_id" />
    </div>
</body>
</html>