<?php 
/*
File: other_orders.php
Coded in PHP7
Purpose: Other Orders Information
Access Type: Direct access
*/

$ModType = "";
if($other_orders_module=="1")
{
	$style = "margin:10px 0;overflow-y:auto;";
	$ModType = ",'1'";
}
elseif($other_orders_module=="2")
{
	$style = "margin:10px 0;";
	$ModType = ",'2'";
}
elseif($other_orders_module=="3")
{
	$style = "margin:5px 0 0 0; width:200px;overflow-y: hidden;overflow-x:auto;padding:5px;";
	$style1="display:inline-block;vertical-align:top;margin:0px 10px 0 10px;";
	echo "<style>ul li{float:left;}</style>";
}

$sel_frame = "select ord.id,ord.item_id,ord.lens_frame_id,ord.item_name as name from in_order_details as ord  where ord.order_id ='$order_id' and ord.patient_id='$patient_id' and ord.module_type_id='$other_orders_module' and ord.del_status='0'";
$res_frame = imw_query($sel_frame);
$num_frame = imw_num_rows($res_frame);
if($num_frame>0)
	{
if(!$lensFrame){
?>

<div class="row" style="<?php echo $style1; ?>">
<div class="col-md-12">
<div class="module_border" style="<?php echo $style; ?>">
	<ul style="width:<?php echo $num_frame*70; ?>px;">
<?php
}
else{
	$order_detail_idBk = $order_detail_id;
	$order_detail_id = $lensFrameSel;
	if($other_orders_module=="1"){
?>
	<h5 style="text-align:center;margin:0 0 5px;font-weight: bold;">Frames</h5>
<?php
	}
	elseif($other_orders_module=="2"){
?>
	<h5 style="text-align:center;margin:0 0 5px;font-weight: bold;">Lenses</h5>
<?php
	}
?>
	<ul style="width:<?php echo $num_frame*70; ?>px">
<?php
}

while($row_frames = imw_fetch_array($res_frame))
{
	$get_item_img = imw_query("select stock_image from in_item where id='".$row_frames['item_id']."'");
	$imgg = imw_fetch_assoc($get_item_img);
	$exist_frame_id_arr[]=$row_frames['lens_frame_id'];
	$file = "../../images/".$img_path.$imgg['stock_image']; 
	if (file_exists($file) && $imgg['stock_image']!="") 
	{
		$img = $img_path.$imgg['stock_image'];
	}
	else
	{
		$img = "no_image.jpg";
	}
?><li <?php if($order_detail_id==$row_frames['id']) { echo 'class="selected_frame"'; } else { echo 'class="text_purpule"'; } ?>  style="padding:2px; text-align:center; display:inline-block; cursor:pointer;">
	<img class="module_border" src="../../images/<?php echo $img; ?>" alt="otherframe" onClick="javascript:old_order_detail('<?php echo $row_frames['id']; ?>'<?php echo $ModType;?>);" style="width:50px; height:50px; margin:5px 5px 0;"><br>
	<span title="<?php echo $row_frames['name']; ?>" style="width:65px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" onClick="javascript:old_order_detail('<?php echo $row_frames['id']; ?>'<?php echo $ModType;?>);"><?php if(strlen($row_frames['name'])>12) { echo substr($row_frames['name'],0,9).".."; } else { echo $row_frames['name']; } ?></span>
</li><?php }
if(!$lensFrame){
?>
	</ul>
</div>
</div>
</div>
<?php 
}
else{
	$order_detail_id = $order_detail_idBk;
?>
	</ul>
<?php
}
}
?>