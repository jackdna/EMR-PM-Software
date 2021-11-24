<?php
	/*
	File: order_ajax.php
	Coded in PHP7
	Purpose: Edit/Save Order Status
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$patient_id=$_SESSION['patient_session_id'];

	//print_r($_POST);die;
	//TYPES
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	   }
	$total_balance = 0; 
	$tax_label = "Tax";
	$tax_label_qry = imw_query("SELECT `tax_label` FROM `in_location` WHERE `id`='".$_SESSION['pro_fac_id']."'");
	if($tax_label_qry && imw_num_rows($tax_label_qry)>0){
		$tax_lbl = imw_fetch_assoc($tax_label_qry);
		if($tax_lbl['tax_label']!="")
			$tax_label = $tax_lbl['tax_label'];
	}
	
	function find_path($mt_id)
	{
		$img_path = array();
		$img_path['1']="../../../images/frame_stock/";
		$img_path['2']="../../../images/lense_stock/";
		$img_path['3']="../../../images/contact_lens_stock/";
		$img_path['5']="../../../images/supply_stock/";
		$img_path['6']="../../../images/medicine_stock/";
		$img_path['7']="../../../images/accessories_stock/";
		return $img_path[$mt_id];
	}
	
	if($_REQUEST['reorder']!="" && $_REQUEST['reorder']=="reorder" && $_REQUEST['save']=="" && $_REQUEST['cancel']=="")
	{
		make_reorder($_REQUEST['orderid'],$_REQUEST['update_item_id']);
		echo "<script>window.opener.main_iframe.".$_REQUEST['frameName'].".location.href=window.opener.main_iframe.".$_REQUEST['frameName'].".location.href; window.close();</script>";
	}
	
	if($_REQUEST['save']!="" && $_REQUEST['save']=="Save")
	{
		$item_id = $_POST['update_item_id'];
		$orderid = $_POST['orderid'];
		for($i=0;$i<count($item_id);$i++)
		{
			$single_item = $item_id[$i];
			$status = $_POST['status_'.$single_item];
			$red_st = $_POST['reduc_stock'];
			$sel_ord = change_item_status($item_id[$i], $status, '', $red_st);
			//echo $sel_ord;

			//UPDATE INVOICE,WS PRICE
			$qry="Update in_order_details SET wholesale_price='".imw_real_escape_string(trim(preg_replace('/[^0-9|.]/','',$_POST['wholesale_price'.$single_item])))."' 
			WHERE id='".$item_id[$i]."'";
			$rs=imw_query($qry);
			unset($rs);
			
			//UPDATE STOCK WS PRICE 
			if($_POST['updateitems_ws']=='yes' && $_POST['wholesale_price'.$single_item]>0){
				$itemId='';
				$rs=imw_query("Select item_id FROM in_order_details WHERE id='".$single_item."'");
				$res=imw_fetch_assoc($rs);
				$itemId=$res['item_id'];
				unset($rs);
	
				if($itemId>0){
					$qry="Update in_item SET wholesale_cost='".$_POST['wholesale_price'.$single_item]."',
					modified_date='".date('Y-m-d')."',
					modified_time='".date('H:i:s')."',
					modified_by='".$_SESSION['authId']."' WHERE id='".$itemId."'";
					$rs=imw_query($qry);
					unset($rs);
				}
			}
		}

		//UPDATE PARENT TABLE IF WHOLE SALE PRICE EXIST
		$wholesaleTot='';
		$qry="Select wholesale_price FROM in_order_details WHERE order_id='".$_REQUEST['ord_id']."' AND del_status='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$wholesaleTot+=$res['wholesale_price'];
		}unset($rs);

		if($wholesaleTot>0){
			$qry="Update in_order SET wholesale_price='".imw_real_escape_string(trim(preg_replace('/[^0-9|.]/','',$wholesaleTot)))."' 
			WHERE id='".$_REQUEST['ord_id']."'";
			$rs=imw_query($qry);
			unset($rs);
		}
		
		$get_status = update_in_order_status($orderid);
	
		if($get_status)
		{
		echo "<script>window.opener.main_iframe.".$_REQUEST['frameName'].".location.href=window.opener.main_iframe.".$_REQUEST['frameName'].".location.href; window.close();</script>";
		}

	}
	
	if($_REQUEST['cancel']!="" && $_REQUEST['cancel']=="Cancel")
	{
		$item_id = $_POST['update_item_id'];
		$orderid = $_POST['orderid'];

		$operator_id=$_SESSION['authId'];
		$entered_date=date('Y-m-d');
		$entered_time=date('H:i:s');
		$en_date = date("Y-m-d H:i:s");
		
		for($i=0;$i<count($item_id);$i++)
		{
			/*Delete Item along with their associated values*/
			$single_item = $item_id[$i];
			
			cancel_item($single_item, $orderid);
			//imw_query("update in_order_details set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where id='$single_item'");
		}
		
		//update qty and price in in_order table
		$get_status=update_qty_price_order($orderid);
		//update order overall status if all items cancelled
		update_ordre_del_status($orderid);
		if($get_status)
		{
		echo "<script>window.opener.main_iframe.".$_REQUEST['frameName'].".location.href=window.opener.main_iframe.".$_REQUEST['frameName'].".location.href; window.close();</script>";
		}

	}
	
	$order_id = $_REQUEST['ord_id'];
	$page = $_REQUEST['page'];
	if($_REQUEST['show_all']==''){
		if($page=='pt_hst'){ $page='all';}
		$_REQUEST['show_all']=$page;
	}
	/*if($page=="pending")
	{
		$wh = " and (ord.order_status='".$page."' or order_status='')";
	}
	elseif($page=="all")
	{
		$wh = "";
	}
	elseif($page=="pt_hst")
	{
		$wh = " and ord.patient_id='$patient_id'";
	}
	elseif($page=="archived")
	{
		$wh="";
	}
	else
	{
		$wh = " and ord.order_status='".$page."'";
	}*/
	
	//if(!$_REQUEST['show_all'])$del_check=" and ord.del_status='0'";
	switch($_REQUEST['show_all']){
		case 'all':
			$del_check="";
		break;
		case 'active':
			$del_check=" AND ord.order_status IN('','pending', 'ordered', 'received') AND ord.del_status='0'";
		break;
		case 'cancelled':
			$del_check=" AND ord.del_status='1'";
		break;
		default:
			if($_REQUEST['show_all']=='pending'){
				$del_check=" AND (ord.order_status='".$_REQUEST['show_all']."' OR ord.order_status='') AND ord.del_status='0'";
			}else{
				$del_check=" AND ord.order_status='".$_REQUEST['show_all']."' AND ord.del_status='0'";
			}
	}
	
	//if($_REQUEST['show_all']=='' || $_REQUEST['show_all']=='pending'){
	//	$del_check=" AND (ord.order_status='".$page."' or order_status='')";
	//}
	

	$sql = "select ord.id, ord.module_type_id, ord.upc_code, ord.item_name, ord.item_id, ord.qty, ord.qty_right, ord.price,ord.total_amount,ord.pt_paid,ord.order_status,ord.discount,ord.discount_val, ord.pt_wear_pic, ord.del_status, ord.wholesale_price,ord.patient_id, ord.lens_frame_id,ord.vw_status,ord.vw_order_id, 
	ord.item_name_os, ord.item_id_os, ord.price_os, ord.total_amount, ord.pt_paid_os, ord.discount_os from in_order_details as ord where ord.order_id='".$order_id."' $del_check and ord.pof_check='0'".$wh;
	$res = imw_query($sql);
	$nums = imw_num_rows($res);	
	
	//get patient name
	$query=imw_query("select patient_id from in_order_details where order_id='$order_id'")or die(imw_error());
	$res1 = imw_fetch_object($query);
	$ptq=imw_query("select fname, mname, lname, id as patient_id from patient_data where id='$res1->patient_id'")or die(imw_error());
	$rows=imw_fetch_array($ptq);
	unset($query,$res1);
	
	//get overall discount
	$qExDet=imw_query("select total_overall_discount,tax_payable,tax_pt_paid from in_order where id='$order_id'")or die(imw_error());
	
	$exDet=imw_fetch_array($qExDet);
	
	/*Remake Data*/
	$remakeData = array();
	$check_remake = imw_query("SELECT `re_make_id` FROM `in_order` WHERE `id`='".$order_id."'");
	if($check_remake && imw_num_rows($check_remake)>0){
		$check_remake = imw_fetch_assoc($check_remake);
		if($check_remake['re_make_id']!="0"){
			$remake_qry = imw_query("SELECT `prac_code_id`, `price`, `qty`, `allowed`, `discount`, `total_amount`, `ins_amount`, `pt_paid`, `pt_resp` FROM `in_order_remake_details` WHERE `order_id`='".$order_id."'");
			if($remake_qry && imw_num_rows($remake_qry)>0){
				$row = imw_fetch_assoc($remake_qry);
				$remakeData['name'] = "Remake Charges";
				$remakeData['prac_code'] = $row['prac_code_id'];
				$remakeData['price'] = $row['price'];
				$remakeData['qty'] = $row['qty'];
				$remakeData['allowed'] = $row['allowed'];
				$remakeData['discount'] = $row['discount'];
				$remakeData['total_amount'] = $row['total_amount'];
				$remakeData['ins_amount'] = $row['ins_amount'];
				$remakeData['pt_paid'] = $row['pt_paid'];
				$remakeData['pt_resp'] = $row['pt_resp'];
			}
		}
	}
	/*End Remake Data*/
	
	$ret_qry=imw_query("select in_order_return.order_detail_id,in_order_return.return_qty,in_return_reason.return_reason
	 from in_order_return join in_return_reason on in_return_reason.id=in_order_return.reason where order_id='".$order_id."'");
	while($ret_row=imw_fetch_array($ret_qry)){
		$ret_data[$ret_row['order_detail_id']]['return_qty'][]=$ret_row['return_qty'];
		$ret_data[$ret_row['order_detail_id']]['reason'][]=$ret_row['return_reason'];
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<style type="text/css">
.delRow{text-decoration:line-through;color:#F00}
tr td{vertical-align:top;}
</style>
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->

<script type="text/javascript">
function del_callBack(result)
{
	if(result==true)
	{
		$("#del_hidden").val("1");
		$("#firstform").submit();
	}	
}
$(document).ready(function(){
	selectCurrentCheck = function(ab){
		$("#checked_"+ab).prop('checked', true);
	}
	del = function(){
	 	if( $(".getchecked:checked").length == 0 ){
           falert('Please check atleast one record');
        }else{
			fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$("tr:not(.delRow) .getchecked").prop('checked', true);
		}else{
			$(".getchecked").prop('checked', false);
		}
	});
});

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
function show_lab_hx(vw_ord_id){
	$("#loading").show();
	var alrt_msg="";
	var dataString = 'action=find_vw_lab_hx&vw_order_id='+vw_ord_id;
	$.ajax({
		type: "POST",
		url: "../ajax.php",
		data: dataString,
		cache: false,
		
		success: function(response)
		{	
			if(response!=""){
				$("#loading").hide();
				falert(response);
			}
		}
	});
}
function refresh_lab_hx(order_id){
	$("#loading").show();
	var alrt_msg="";
	var dataString = 'method=GetTrackingHistory&order_id='+order_id;
	$.ajax({
		type: "POST",
		url: "../other/vw_refresh_data.php",
		data: dataString,
		cache: false,
		
		success: function(response)
		{	
			//alert(response);
		},
		complete: function(){
			window.location.reload();
		}
	});
}
</script>
</head>

<body><div id="modal-window" style="position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; z-index: 1050; overflow: auto; display:none">
<div class="modal-box modal-size-normal" style="position: absolute; top: 50%; left: 50%; margin-top: -102.5px; margin-left: -280px;">
<div class="modal-inner"><div class="modal-title">
<h3 id="modal-window-title">imwemr</h3>
<a class="modal-close-btn" onClick="closeMe()"></a></div>
<div id="modal-window-detail" class="modal-text">detail will be here</div>
<div class="modal-buttons">
<a class="modal-btn" onClick="closeMe()">Cancel</a>
<a class="modal-btn btn-light-blue" onClick="submitMe();">Confirm</a>
</div></div></div></div>
	<div style="width:900px; margin:0 auto;">
    <form name="addframe" id="firstform1" method="post" style="margin:0px;">
       <div class="module_border">
            <div class="listheading"> Detail of Order #<?php echo $order_id; ?>
			
            <div style="float:right;">
            <select name="rec_type" id="rec_type" onChange="show_records(this)">
                <option value="all" <?php echo ($_REQUEST['show_all']=='all')?'selected':'';?>>All</option>
                <option value="active" <?php echo ($_REQUEST['show_all']=='active')?' selected':'';?>>Active</option>
                <option value="cancelled" <?php echo ($_REQUEST['show_all']=='cancelled')?'selected':'';?>>Cancelled</option>
				<option value="dispensed" <?php echo ($_REQUEST['show_all']=='dispensed')?'selected':'';?>>Dispensed</option>
				<option value="notified" <?php echo ($_REQUEST['show_all']=='notified')?'selected':'';?>>Notified</option>
                <option value="ordered" <?php echo ($_REQUEST['show_all']=='ordered')?'selected':'';?>>Ordered</option>
                <option value="pending" <?php echo ($_REQUEST['show_all']=='pending')?'selected':'';?>>Pending</option>
                <option value="received" <?php echo ($_REQUEST['show_all']=='received')?'selected':'';?>>Received</option>
            </select>
            <script type="text/javascript">
            function show_records(obj)
			{
				//var strUser = obj.options[obj.selectedIndex].value;
				var strUser=document.getElementById('rec_type').value;
				var page='<?php echo $_REQUEST[page];?>';
				var ord_id=	'<?php echo $_REQUEST[ord_id];?>';
				
				window.location.href="order_ajax.php?ord_id="+ord_id+"&page="+page+"&show_all="+strUser;
			}
            </script>
            </div><div style="text-align:center; width:500px; float:right"><?php echo $rows['lname'].",&nbsp;".$rows['fname']."&nbsp;".$rows['mname']." - ".$rows['patient_id']; ?></div>
            </div>
            
            <table class="table_collapse table_cell_padd2">
                <tr class="listheading">
                   <td width="10"><input type="checkbox" id="selectall" name="select_all"></td>
                    <td width="70">Category</td>
                   <!-- <td width="70">UPC</td>-->
                    <td width="130">Item Name</td>
                    <td width="35">Qty.</td>
                    <td width="95">T. Unit Cost</td>
                    <td width="70">Discount</td>
                    <td width="70">Pt. Paid</td>
                    <td width="75">Balance</td>
                    <td width="80">WS Price</td>
                    <td width="90" style="text-align: left;">Status</td>
                    <td width="140">Lab Status <?php if($GLOBALS['connect_visionweb']!=""){?><a href="javascript:void(0);" class="text_purpule" onClick="refresh_lab_hx('<?php echo $order_id; ?>')"><img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/refresh_icon.png" style="vertical-align:middle;" title="Refresh Lab Status"></a><?php }?></td>
                </tr>
         </table>         
                <div style="overflow-x:hidden; overflow-y:scroll; height:380px;">
                <table class="table_collapse table_cell_padd2">
<?php 
if($nums > 0)
{
	while($row = imw_fetch_array($res)) {
					if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
					$get_item_img = imw_query("select stock_image from in_item where id='".$row['item_id']."'");
					$imgg = imw_fetch_assoc($get_item_img);
					$sel_img_path = find_path($row['module_type_id']);
					$file = $sel_img_path.$imgg['stock_image']; 
					if (file_exists($file) && $imgg['stock_image']!="") {
						$img = $sel_img_path.$imgg['stock_image'];
					} else {
						$img = "../../../images/no_image.jpg";
					}
					
					$exp_discount="";
					$exp_discount=explode('%',$row['discount']);
					$final_discount="0";
					
					if($row['module_type_id'] == '3'){
						$allowed = $row['price'] * $row['qty_right'];
						$allowed_os = $row['price_os'] * $row['qty'];
						
						if(count($exp_discount)>1){
							$final_discount = ($allowed * $exp_discount[0])/100;
						}else{
							$final_discount = $exp_discount[0];
						}
						$final_discount = ($final_discount == "")?"0":$final_discount;
						
						
						$exp_discount=explode('%',$row['discount_os']);
						if(count($exp_discount)>1){
							$final_discount_os = ($allowed_os * $exp_discount[0])/100;
						}else{
							$final_discount_os = $exp_discount[0];
						}
						$final_discount_os = ($final_discount_os == "")?"0":$final_discount_os;
					}
					else{
						$allowed=$row['price']*($row['qty'] + $row['qty_right']);
						
						if($row['module_type_id']==2){
							$final_discount = $row['discount_val'];
							$allowed = $row['total_amount'];
						}
						else{
							if(count($exp_discount)>1){
								$final_discount=($allowed*$exp_discount[0])/100;
							}else{
								$final_discount=$exp_discount[0];
							}
							$final_discount = ($final_discount=="")?"0":$final_discount;
						}
					}
					
?>
                <tr class="<?php echo $rowbg; echo(($row['del_status']==1)?" delRow":""); ?>">
                     <td width="10" style="text-align:center;">
                     <?php
                     if($row['module_type_id']==2)
					 {
						?><input type="hidden" name="lens_frame_id_<?php echo $row['id']; ?>" id="lens_frame_id_<?php echo $row['id']; ?>" value="<?php echo $row['lens_frame_id']; ?>">
                        <?php 
					}
					 ?>
                     <script>
                     function check_frame(val)
					 {
						var frame_id=$('#lens_frame_id_'+val).val();
					
						if($('#checked_'+val).is(':checked')==true)
						document.getElementById('checked_'+frame_id).checked=true;
						else
						document.getElementById('checked_'+frame_id).checked=false;
						
					}
                     </script>
                     <input type="checkbox" title="Select" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $row['id']; ?>" name="update_item_id[]" <?php echo($row['del_status']==1)?' disabled':''; echo ($row['module_type_id']==2)?' onClick="check_frame(this.value)"':'';?>>
                    </td> 
                    <td width="70" style="text-align:left;"><input type="hidden" name="orderid" value="<?php echo $order_id; ?>"><?php if($row['module_type_id']=='8') { echo "Tax"; } else { echo ucfirst($arrTypes[$row['module_type_id']]); } ?></td>
                   <!-- <td width="60" style="text-align:left;"><?php echo $row['upc_code']; ?></td>-->
                    <td width="105" style="text-align:left;">
						<?php
							if($row['module_type_id']==3){
								echo '<span class="blueColor" style="font-weight:bold;">OD</span>:'.$row['item_name'];
							}
							else{
								echo $row['item_name'];
							}
						?>
					</td>
                    <td width="40" style="text-align:right;"><?php echo ($row['module_type_id']==3)?$row['qty_right']:$row['qty'] + $row['qty_right']; ?></td>
                    <td width="80" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($allowed,2); ?></td>
                    <td width="80" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($final_discount,2); ?></td>
                    <td width="70" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($row['pt_paid'],2); ?></td>
                    <td width="78" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format(($allowed-$row['pt_paid']-$final_discount),2);
						if($row['del_status']!=1){
							$total_balance += $allowed-$row['pt_paid']-$final_discount;
						} ?></td>
                    <td width="78" style="text-align:right;">
						<input name="wholesale_price<?php echo $row['id'];?>" value="<?php echo number_format(($row['wholesale_price']),2); ?>"  onChange="selectCurrentCheck('<?php echo $row['id']; ?>')" style="width:60px" <?php echo($row['del_status']==1)?' readonly':'';?>>
                    </td>
                    
                    <td width="80" style="text-align:center;">
                    <?php if($page=="dispensed" || $row['order_status']=="dispensed") { echo ucfirst($row['order_status']); } else { ?>
                        <select name="status_<?php echo $row['id'];?>" style="width:80px;" onChange="selectCurrentCheck('<?php echo $row['id']; ?>')" class="status_dropdown_<?php echo $row['id'];?>" <?php echo($row['del_status']==1)?' disabled':'';?>>
							<option <?php if($row['order_status']=="dispensed") { echo "selected='selected'"; } ?> value="dispensed">Dispensed</option>
							<option <?php if($row['order_status']=="notified") { echo "selected='selected'"; } ?> value="notified">Notified</option>
                           <option <?php if($row['order_status']=="ordered") { echo "selected='selected'"; } ?> value="ordered">Ordered</option>
						   <option <?php if($row['order_status']=="pending" || $row['order_status']=="") { echo "selected='selected'"; } ?> value="pending">Pending</option>
                            <option <?php if($row['order_status']=="received") { echo "selected='selected'"; } ?> value="received">Received</option>
                      </select>
                      <?php } ?>
                    </td>
                     <td width="130" style="text-align:left;"><a href="javascript:void(0);" class="text_purpule" onClick="show_lab_hx('<?php echo $row['vw_order_id']; ?>')"><?php if($row['vw_order_id']!=""){echo vw_lab_status($row['vw_status']);} ?></a></td>
                </tr>                
<?php
	if($row['module_type_id']=='3' && $row['item_id_os']!=''){
		if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
?>
		<tr class="<?php echo $rowbg; echo(($row['del_status']==1)?" delRow":""); ?>">
			<td width="10"></td>
			<td width="70"></td>
			<td width="105" style="text-align:left;">
				<span class="greenColor" style="font-weight:bold;">OS</span>: <?php echo $row['item_name_os']; ?>
			</td>
			<td width="40" style="text-align:right;">
				<?php echo $row['qty']; ?>
			</td>
			<td width="80" style="text-align:right;">
				<?php echo currency_symbol(); ?><?php echo number_format($allowed_os, 2); ?>
			</td>
			<td width="80" style="text-align:right;">
				<?php echo currency_symbol(); ?><?php echo number_format($final_discount_os, 2); ?>
			</td>
			<td width="70" style="text-align:right;">
				<?php echo currency_symbol(); ?><?php echo number_format($row['pt_paid_os'], 2); ?>
			</td>
			 <td width="78" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format(($allowed_os-$row['pt_paid_os']-$final_discount_os),2);
						if($row['del_status']!=1){
							$total_balance += $allowed_os-$row['pt_paid_os']-$final_discount_os;
						}
			?>
			</td>
		</tr>
<?php
			//$allowed = $allowed + $allowed_os;
			//$final_discount = $final_discount + $final_discount_os;
	}
if(count($ret_data[$row['id']]['return_qty'])>0){
?>
<tr class="<?php echo $rowbg;?>"><td></td><td colspan="10">
<span style="padding-right:50px;"><strong>Returned Qty : </strong> <?php echo array_sum($ret_data[$row['id']]['return_qty']); ?> </span>
<span><strong>Reason : </strong> <?php echo implode(', ',$ret_data[$row['id']]['reason']); ?> </span>
</td></tr>
<?php
}
/*List contact Lens Disinfectant*/
if($row['module_type_id']==3){
	$di_sql = "SELECT * FROM `in_order_cl_detail` WHERE `order_detail_id`='".$row['id']."' AND `order_id`='".$order_id."' AND `del_status`='0' AND `item_type`='DI' ORDER BY `id` DESC LIMIT 1";
	$di_resp = imw_query($di_sql);
	if($di_resp && imw_num_rows($di_resp)>0){
		$disinf = imw_fetch_assoc($di_resp);
		$disinf_name = "";
		$disinf_qry = imw_query("SELECT `name` FROM `in_cl_disinfecting` WHERE `id`='".$disinf['item_id']."'");
		if($disinf_qry && imw_num_rows($disinf_qry)>0){
			$disinf_qry = imw_fetch_assoc($disinf_qry);
			$disinf_name = $disinf_qry['name'];
		}
		
		$di_allowed = $disinf['price']*$disinf['qty'];
		$di_exp_discount="";
		$di_exp_discount=explode('%',$disinf['discount']);
		$di_final_discount="0";
		if(count($di_exp_discount)>1){
			$di_final_discount=($di_allowed*$di_exp_discount[0])/100;
		}else{
			$di_final_discount=$di_exp_discount[0];
		}
		$di_final_discount = ($di_final_discount=="")?"0":$di_final_discount;
?>
		<tr>
			<td></td>
			<td>Disinfecting</td>
			<td><?php echo $disinf_name; ?></td>
			<td style="text-align:right;"><?php echo $disinf['qty']; ?></td>
			<td style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($di_allowed, 2); ?></td>
			<td style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($di_final_discount, 2); ?></td>
			<td style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($disinf['pt_paid'],2); ?></td>
			<td style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format(($disinf['total_amount']-$disinf['pt_paid']-$di_final_discount),2); if(($disinf['del_status']!=1)){$total_balance += $disinf['total_amount']-$disinf['pt_paid']-$di_final_discount;} ?></td>
			<td></td>
			<td></td>
            <td></td>
		</tr>
<?
	}
}
/*End List contact Lens Disinfectant*/
}

if(count($remakeData)>0): 
	if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
	
	$allowed=$remakeData['price']*$remakeData['qty'];
	$exp_discount="";
	$exp_discount=explode('%',$remakeData['discount']);
	$final_discount="0";
	if(count($exp_discount)>1){
		$final_discount=($allowed*$exp_discount[0])/100;
	}else{
		$final_discount=$exp_discount[0];
	}
	$final_discount = ($final_discount=="")?"0":$final_discount;
?>
	<tr class="<?php echo $rowbg; ?>">
		 <td width="185" style="text-align:center;" colspan="3"><?php echo $remakeData['name']; ?></td>
		<td width="40" style="text-align:right;"><?php echo $remakeData['qty']; ?></td>
		<td width="80" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($allowed,2); ?></td>
		<td width="80" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($final_discount,2); ?></td>
		<td width="70" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format($remakeData['pt_paid'],2); ?></td>
		<td width="78" style="text-align:right;"><?php echo currency_symbol(); ?><?php echo number_format(($remakeData['total_amount']-$row['pt_paid']),2); $total_balance += $remakeData['total_amount']-$remakeData['pt_paid']; ?></td>
		<td width="78" style="text-align:right;"></td>
		<td width="80" style="text-align:center;"></td>
        <td width="90" style="text-align:center;"></td>
	</tr>	

<?php
endif;
	if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
	$total_balance = (($total_balance-$exDet['tax_pt_paid'])-$exDet['total_overall_discount'])+$exDet['tax_payable'];
?>
				<tr class="<?php echo $rowbg; echo(($row['del_status']==1)?" delRow":""); ?>">
					<td style="text-align:right;" colspan="7">
						<strong>Ovreall Disc. : </strong><?php echo currency_symbol().$exDet['total_overall_discount']; ?>
						
						<strong style="margin-left:10px;"><?php echo $tax_label; ?> : </strong><?php echo currency_symbol().$exDet['tax_payable']; ?>
						<strong style="margin-left:10px;"><?php echo $tax_label; ?> Paid : </strong><?php echo currency_symbol().$exDet['tax_pt_paid']; ?>
					
						<strong style="margin-left:8px;">T. Balance : </strong>
					</td>
					<td style="text-align:right;">
						<?php echo currency_symbol().number_format($total_balance,2); ?>
					</td>
					<td style="text-align:center;">&nbsp;</td>
					<td style="text-align:center;">&nbsp;</td>
					<td style="text-align:center;">&nbsp;</td>
				</tr>
                <?php
				} ?>
            </table>
         </div>
       </div>
        <div class="btn_cls mt10">
        <input type="button" name="print" value="Print Order" onClick="printSelPos('<?php echo $_REQUEST['ord_id'];?>','Frame and Lense Selection');"/> 
            <input type="button" name="save" value="Save" onClick="check_dispensed(window.top);"/> 
            
             <input type="button" name="cancelbtn" value="Cancel Items" onClick="check_cancelled();"/>
             <input type="button" name="reorder" value="Reorder" onClick="item_reorder();">  
             <input type="hidden" name="pagename" id="pagename" value="item_detail" >                      
        </div>
        <input type="hidden" name="save" id="save" value="Save" />
        <input type="hidden" name="cancel" id="cancel" value="Cancel" />
        <input type="hidden" name="reorder" id="reorder" value="reorder" />
		<input type="hidden" name="reduc_stock" id="reduc_stock" value="no">
        <input type="hidden" name="updateitems_ws" id="updateitems_ws" value="no">
	    <input type="hidden" name="reason_id" id="reason_id" value="">
        </form>
    </div>
    
    <div id="reason_div_to_show" style="display:none">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
<tr>
    <td><select id="status_reason" style="width:100%">
    <option value="">Please Select Reason</option>
    <?php
    $q=imw_query("select * from in_reason where del_status=0 order by reason_name")or die(imw_error());
    while($dlist=imw_fetch_object($q)){
        echo'<option value="'.$dlist->id.'">'.$dlist->reason_name.'</option>';
    }
    ?></select></td>
</tr>
<tr>
    <td><label id="reduce_qty_lb"><input type="checkbox" id="reduce_qty" name="reduce_qty" value="1">
    Yes, I want to reduce Qty from stock.</label></td>
</tr>
<tr>
    <td><label id="update_wholesale_lb"><input type="checkbox" id="update_wholesale" name="update_wholesale" value="1">
    Yes, I want to update whole sale price for stock.</label></td>
</tr>
</table>
</div>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
</body>
</html>