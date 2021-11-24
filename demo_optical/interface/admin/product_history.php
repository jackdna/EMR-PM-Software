<?php 
/*
File: product_history.php
Coded in PHP7
Purpose: View Product History
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 

$item_id=$_REQUEST['item_id'];

//GET PRODUCT NAME
$rs=imw_query("Select upc_code, name from in_item WHERE id='".$item_id."'");
$res=imw_fetch_array($rs);
$itemName=$res['name'];
$itemUPC=$res['upc_code'];

$show_heading='<div class="fl">Product History</div>
<div class="fl" style="width:auto; color:#03F; text-align:left; margin-left:230px;">UPC : '.$itemUPC.'</div>';
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
	function loc_total_fun(){
		var sum_total = 0;
		$('.loc_qty_class').each(function()
		{
			if(!isNaN(parseInt($(this).val(),10))){
				sum_total += parseInt($(this).val(),10);
			}
		});
		$('#loc_total').val(sum_total);
	}
	function sub_form_fun(){
		if(document.getElementById('item_add').value!='yes' && document.getElementById('reason').value==""){
				top.falert("Please select reason");
				return false;
		}else{
			document.getElementById('save_stock').value=1;
			document.loc_form.submit();	
		}
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
	<div style="padding:0px; width:100%;height:100%;">
        <div class="listheading"><?php echo $show_heading;?></div>
        <div>
               <table class="table_collapse listheading">
                <tr>
               	<td style="width:132px; text-align:center;">Location</td>
                <td style="width:50px; text-align:center;">Qty</td>
                <td style="width:75px; text-align:center;">Action</td>
                <td style="width:140px; text-align:center;">Action By</td>
                <td style="width:91px; text-align:center;">Date</td>
                <td style="width:135px; text-align:center">Order ID- Pt. Name</td>
                <td style="width:auto; text-align:center">Reason</td>
                </tr>
               </table>
               <div style="overflow-y:scroll; height:210px; width:99.5%">
               <table class="table_collapse cellBorder table_cell_padd5" style="width:99.9%" >
               <?php
			   $rs=imw_query("Select stockDet.*, CONCAT(pd.lname,', ',pd.fname,' ',pd.mname) as pt_name, DATE_FORMAT(stockDet.entered_date, '%m-%d-%Y') as 'actionDate', in_location.loc_name, users.fname, users.lname, in_reason.reason_name 
			   FROM in_stock_detail stockDet 
			   LEFT JOIN in_location ON in_location.id = stockDet.loc_id 
			   LEFT JOIN users ON users.id = stockDet.operator_id  
			   LEFT JOIN in_reason ON in_reason.id = stockDet.reason 
			   LEFT JOIN in_order ON in_order.id=stockDet.order_id
			   LEFT JOIN patient_data pd ON pd.id=in_order.patient_id 
			   WHERE stockDet.item_id='".$item_id."' and stockDet.stock>0 order by stockDet.id desc");
			   if(imw_num_rows($rs) > 0)
			   {
			   		while($res=imw_fetch_array($rs)){
				    $actionColor='greenColor';
				    $actionType='Added';
				  	if($res['trans_type']=='minus'){
						$actionType='Subtracted';
					 	$actionColor='redColor';
				  	}elseif($res['trans_type']=='clearance'){
						$actionType='Clearance';
				  	}
			   ?>
                    <tr style="height:20px;">
                        <td width="130px" align="left" class="text14" style="text-align:left; padding-left:5px;">
                        <?php echo $res['loc_name']; ?>
                        </td>
                        <td width="38px" class="text14" style="text-align:center;">
                        <?php echo $res['stock']; ?>
                        </td>
                        <td width="76px" style="text-align:center;" class="text14 <?php echo $actionColor;?>">
                        <?php echo $actionType ?>
                        </td>
                        <td width="130px" class="text14" style="text-align:left;">
                        <?php 
							if($res['lname']!='' || $res['fname']!=''){
								echo $res['lname'].', '.$res['fname']; 
							}
						?>
                        </td>
                        <td width="91px" class="text14" style="text-align:center;">
                        <?php echo $res['actionDate']; ?>
                        </td>
						<td width="130px" class="text14" style="text-align:left;">
						<?php if($res['order_id']>0)echo $res['order_id'].'-'.$res['pt_name']; ?>
						</td>
                        <td width="150px" class="text14" style="text-align:left;">
                        <?php echo $res['reason_name']; ?>
                        </td>
                    </tr>
              <?php } } else {?>    
			  		<tr style="height:20px;">
                        <td colspan="6" class="text14" style="text-align:center;">
							Item history does not exists
						</td>
					</tr>
			  <?php } ?>  
              </table>
           </div>
        </div>
        <div class="btn_cls" style="width:96.8%; border-top:1px solid #CCC">
         <input type="button" name="new" value="Close" onClick="javascript:window.self.close();" />
        </div>
 </div>
</body>
</html>