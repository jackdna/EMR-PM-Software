<?php
	/*
	File: remove_order_detail.php
	Coded in PHP7
	Purpose: View Remove Order Details
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$patient_id=$_SESSION['patient_session_id'];
	
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	if($_REQUEST['delete']!="" && $_REQUEST['delete']=="Delete")
	{
		$itemid = $_POST['update_item_id'];
		for($i=0;$i<count($itemid);$i++)
		{
			$single_item = $itemid[$i];
			$upd_item = imw_query("update in_order_return set del_status='2', del_date='$entered_date', del_time='$entered_time', del_by='$operator_id' where id='$single_item' and del_status='0' and status='remove'");
		}
		
		if($upd_item)
		{
			echo "<script>window.opener.location.reload(); window.close();</script>";
		}
	}
	
	$order_id = $_REQUEST['ord_id'];
	$search_upcCode = $_REQUEST['ucod'];
	$search_itemname = $_REQUEST['itm'];
	$search_del = $_REQUEST['delstatus'];
	$swhere="";
	if($search_upcCode!="")
	{
		$swhere .= " and ord.upc_code like '".$search_upcCode."%'";
	}
	
	if($search_itemname!="")
	{
		$swhere .= " and ord.item_name like '".$search_itemname."%'";
	}
	
	if($search_del==0)
	{
		$swhere .= " and ord_ret.del_status='0'";
	}
	$sql = "select ord_ret.id, ord_ret.module_type_id, ord.upc_code, ord.item_name, ord_ret.item_id, ord_ret.return_qty, rr.return_reason, ord_ret.status, mt.module_type_name, ord_ret.del_status from in_order_return as ord_ret 
	inner join in_module_type as mt on mt.id = ord_ret.module_type_id
	inner join in_order_details as ord on ord.id = ord_ret.order_detail_id
	inner join in_return_reason as rr on rr.id = ord_ret.reason
	where ord_ret.order_id='".$order_id."' and ord_ret.status='remove' and ord.pof_check='0' $swhere";
	$res = imw_query($sql);
	$nums = imw_num_rows($res);
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
			top.fconfirm('Are you sure to delete selected record(s) ?',del_callBack);
		}
	}
	$("#selectall").click(function(){		
		if($(this).is(":checked")){
			$(".getchecked").prop('checked', true);
		}else{
			$(".getchecked").prop('checked', false);
		}
	});
});

function chk_del()
{
	if($('[name="update_item_id[]"]:checked').length==0)
	{
		falert("Please select a record");
		return false;
	}
	if($('[name="update_item_id[]"]:checked').length>0)
	{
		fconfirm('Are you sure to delete selected record(s) ?',chk_del_callBack);
	}

}
function chk_del_callBack(result)
{
	return result;
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
</head>
<body>
	<div style="width:750px; margin:0 auto;">
    <form name="addframe" id="firstform" method="post" style="margin:0px;">
       <div class="module_border">
            <div style="background: #D5EAF7; height:33px;">
				<div style="float:left; width:555px; background: #D5EAF7;">Detail of Order #<?php echo $order_id; ?></div>
				<div style="float:left; width:100px; background: #D5EAF7;">
					<select name="delstatus" id="delstat" style="width:90px; margin-top:2px;">
						<option value="0" <?php if($_REQUEST['delstatus']==0) { echo "selected"; } ?>>Active</option>
						<option value="1" <?php if($_REQUEST['delstatus']==1) { echo "selected"; } ?>>All</option>
					</select>
				</div>
				<div class="btn_cls" style="float:left; width:93px; background: #D5EAF7; padding:0; text-align:left;"><input type="submit" value="Search" name="search" style="width:70px; margin-top:2px;"></div>
			</div>
            <table width="665" class="table_collapse table_cell_padd2 module_label">
                <tr class="listheading" style="text-align:center;">
                  	<td width="10"><input type="checkbox" id="selectall" name="select_all"></td>
                    <td width="80">Category</td>
                    <td width="130" style="text-align: left;">Upc Code</td>
                    <td width="130" style="text-align: left;">Product Name</td>
                    <td width="50">Qty.</td>
                    <td width="130" style="text-align: left;">Reason</td>
                </tr>
         </table>
                <div style="overflow-x:hidden; overflow-y:scroll; height:180px;">
                <table width="664" class="table_collapse table_cell_padd2 module_label">
                <?php 
				if($nums > 0)
				{
					while($row = imw_fetch_array($res)) {
					if($row['del_status']==2)
					{ $rowbg="del_bg"; $dis="none";	}
					else
					{ 
						if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
						$dis="inline-block";
					}
				?>
                <tr class="<?php echo $rowbg; ?>">
                     <td width="25" style="text-align:center;"><input type="checkbox" title="Select" class="getchecked" value="<?php echo $row['id']; ?>" id="checked_<?php echo $row['id']; ?>" name="update_item_id[]" style="display:<?php echo $dis; ?>">
                    </td>
                    <td width="85" style="text-align:center;"><input type="hidden" name="orderid" value="<?php echo $order_id; ?>"><?php echo ucfirst($row['module_type_name']); ?></td>
                    <td width="135" style="text-align:left;"><?php echo $row['upc_code']; ?></td>
                    <td width="140" style="text-align:left;"><?php echo $row['item_name']; ?></td>
                    <td width="49" style="text-align:center;"><?php echo $row['return_qty']; ?></td>
                    <td width="120" style="text-align:left;"><?php echo $row['return_reason']; ?></td>
                </tr>
                <?php } }
				else
				{
				 ?>
                <tr>
                	<td colspan="6" style="text-align:center"> No Records Exists </td>
                </tr>
                <?php } ?>
            </table>
         </div>
       </div>
	    <div class="btn_cls mt10">
			<input type="submit" name="delete" value="Delete" onClick="return chk_del();">      
		 </div>
      </form>
    </div>
</body>
</html>