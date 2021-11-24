<?php
/*pro_fac_id*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
$column_data_limit=13;
$tid=$_REQUEST['tid'];
$mid=$_REQUEST['mid'];
$vid=$_REQUEST['vid'];
$loc_id = $_SESSION['pro_fac_id']; 
/*Resons List*/
$reason_arr = array();
$query5=imw_query("select id,reason_name from in_reason where del_status='0' order by reason_name");
while($sel_row5=imw_fetch_array($query5)){ 
	$reason_arr[$sel_row5['id']]=$sel_row5['reason_name'];
}

/*Item/Module Types List*/
$module_arr = array();
$query2=imw_query("select * from in_module_type");
while($row2=imw_fetch_array($query2)){
	$module_arr[$row2['id']]=$row2['module_type_name'];
}
$query5=imw_query("select id,color_name,color_code from in_color");
while($row5=imw_fetch_array($query5)){
	$color_name_arr[$row5['id']]=$row5['color_name'];
}

$query6=imw_query("select id,color_name,color_code from in_frame_color");
while($row6=imw_fetch_array($query6)){
	$frame_color_name_arr[$row6['id']]=$row6['color_name'];
}

$query7=imw_query("select id,style_name from in_frame_styles");
while($row7=imw_fetch_array($query7)){
	$frame_style_name_arr[$row7['id']]=$row7['style_name'];
}

?>
<table class="table_collapse" id="show_con_adv">
	 <tr class="listheading sepTH">
	  <th style="width:3%;"><input type="checkbox" id="selectall" value="" /></th>
	  <th style="width:10%;">UPC Code</th>
	  <th style="width:8%;" title="Product Type">P. Type</th>
	  <th style="width:10%;" title="Product Name">P. Name</th>
	  <th style="width:10%;">Size</th>
	  <th style="width:11%;">Brand</th>
	  <th style="width:8%;">Color</th>
	  <th style="width:5%;">Style</th>
	  <th style="width:5%;">Discount</th>
	  <th style="width:5%;" title="Wholesale Price">W. Price</th>
	  <th style="width:5%;" title="Retail Price">R. Price</th>
	  <th style="width:5%;" title="Purchase Price">P. Price</th>
	  <th style="width:5%;" title="Facility Qty.">F. Qty.</th>
	  <th style="width:5%;">Rec. Qty.</th>
	  <th style="width:5%;">
		<select style="height:23px;width:80px;" onChange="adv_reason_sel(this.value);">
		  <option value="0">Reason</option>
		  <?php foreach($reason_arr as $res_key=>$res_val){	?>
			<option value="<?php echo $res_key;?>"><?php echo $res_val;?></option>
		  <?php } ?>
		</select>
	  </th>
	</tr>
	<?php
		if($mid>0){
			$manu_whr=" and i.manufacturer_id='$mid'";
		}
		if($vid>0){
			$vend_whr=" and i.vendor_id='$vid'";
		}
		$item_qry = "SELECT 
					`i`.`id`, `i`.`upc_code`, `i`.`name`, `i`.`module_type_id`, `m`.`module_type_name`, `i`.`qty_on_hand`,i.color, 
					`i`.`a`,`i`.`b`,`i`.`ed`,`i`.`dbl`,`i`.`temple`,`i`.`bridge`,`i`.`fpd`,`i`.`frame_style`,
					IFNULL(`lt`.`stock`, 0) AS 'stock', 
					IFNULL(`v`.`vendor_name`, '') AS 'vendor_name', 
					IF(
						`i`.`module_type_id` = 1, 
						IFNULL(`fb`.`frame_source`, ''), 
						IFNULL(`cb`.`brand_name`, '')
					) AS 'brand_name',
					lot.lot_no, lot.stock as lot_qty
				FROM 
					`in_item` `i`
					LEFT JOIN `in_item_loc_total` `lt` ON(
						`i`.`id` = `lt`.`item_id`
						AND `lt`.`loc_id` = '".$loc_id."'
					)
					LEFT JOIN `in_module_type` `m` ON(`i`.`module_type_id` = `m`.`id`) 
					LEFT JOIN `in_vendor_details` `v` ON(`i`.`vendor_id` = `v`.`id`) 
					LEFT JOIN `in_frame_sources` `fb` ON(`i`.`brand_id` = `fb`.`id`) 
					LEFT JOIN `in_contact_brand` `cb` ON(`i`.`brand_id` = `cb`.`id`) 
					LEFT JOIN `in_item_lot_total` lot ON(`i`.`id` = `lot`.`item_id` AND lot.stock>0)  
				WHERE 
					`i`.`module_type_id` = '".$tid."'
					AND `i`.`del_status`=0 $manu_whr $vend_whr";
	
	$query = imw_query($item_qry);
	
	if(imw_num_rows($query)>0){	
		while($item_data = imw_fetch_object($query)){
			$i++;
		$lot_no=($item_data->lot_no)?$item_data->lot_no:date('Ymd').$_SESSION["authId"].$i;
		$upc_code_tr=str_replace(' ','_',$item_data->upc_code)."_r_".$lot_no;
	?>
	<tr id="<?php echo $upc_code_tr; ?>">
	  <td style="text-align:center;" class="rec_chk_box_td">
		<input name="rec_chk_box[]" id="<?php echo $upc_code_tr; ?>_chk" class="rec_chk_box" type="checkbox" value="<?php echo $upc_code_tr; ?>" /> 
	  </td>
	  <td style="text-align:center;" title="<?php echo '#lot:'.$lot_no; ?>">
	  	<?php echo $item_data->upc_code; ?>
	  	<input name="upc_code[]" type="hidden" value="<?php echo $item_data->upc_code; ?>">
		<input name="in_bat_rec_id[]" type="hidden" value="">
		<input name="fac_quant[]" type="hidden" value="<?php echo $item_data->stock; ?>">
		<input name="fac_lot_quant[]" type="hidden" value="<?php echo $item_data->lot_qty; ?>">
		<input name="tot_qnt[]" type="hidden" value="<?php echo $item_data->qty_on_hand; ?>">
		<input name="item_id[]" type="hidden" value="<?php echo $item_data->id; ?>">
		<input name="module_type[]" class="module_type" type="hidden" value="<?php echo $item_data->module_type_id; ?>">
		<input name="prod_name[]" class="prod_name" type="hidden" value="<?php echo $item_data->name; ?>">
	  </td>
	  <td style="text-align:center;"><?php echo $module_arr[$item_data->module_type_id]; ?></td>
	  <td style="text-align:center;"<?php
		if(strlen($item_data->name)>$column_data_limit){
			$pro_name=substr($item_data->name,0,$column_data_limit).'..';
			echo " data-title=\"".$item_data->name."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
		}else {$pro_name=$item_data->name;}
	?>><?php echo $pro_name; ?></td>
	  <td style="text-align:center;"><?php 
			if($item_data->module_type_id==1){echo"$item_data->fpd-$item_data->bridge-$item_data->temple";
		 /* echo"<table class=''>
				<tr>
					<td><strong>A</strong> $item_data->a</td>
					<td><strong>B</strong> $item_data->b</td>
					<td><strong>ED</strong> $item_data->ed</td>
					<td><strong>DBL</strong> $item_data->dbl</td>
				</tr></table>
				<table>
				<tr>
					<td><strong>Temple</strong> $item_data->temple</td>
					<td><strong>Bridge</strong>$item_data->bridge </td>
					<td><strong>FPD</strong> $item_data->fpd</td>
				</tr>
			</table>";*/
			}
		  ?></td>
  
	  <td style="text-align:center;"<?php
		  $brandName=$item_data->brand_name;
			if(strlen($brandName)>$column_data_limit){
				$brand=substr($brandName,0,$column_data_limit).'..';
				echo " data-title=\"".$brandName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
			}else {$brand=$brandName;}
	?>><?php echo $brand; ?></td>
	  <td style="text-align:center;"<?php
			if($item_data->module_type_id==3){$colorName=$color_name_arr[$item_data->color];}else{$colorName=$frame_color_name_arr[$item_data->color];}
		  
			if(strlen($colorName)>$column_data_limit){
				$color=substr($colorName,0,$column_data_limit).'..';
				echo " data-title=\"".$colorName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
			}else {$color=$colorName;}
	?>><?php echo $color; ?></td>
	<?php
	$styleName=$frame_style_name_arr[$item_data->frame_style];
	if(strlen($styleName)>$column_data_limit){
		$style=substr($styleName,0,$column_data_limit).'..';
		$title= " data-title=\"".$styleName."\" onMouseOver=\"tooltip(this, 'block');\" onMouseOut=\"tooltip(this,'none');\"";
	}else {$style=$styleName;}
		?>
  <td <?php echo $title?>><?php echo $style;?></td>
	  <td style="text-align:center;">
		<input type="text" name="discount[]" id="<?php echo str_replace(" ", "_",$item_data->upc_code).$lot_no; ?>_disc" class="disc_input" value="0">
		</td>
	  <td style="text-align:center;">
		
		<input type="text" name="wholesale_price_exis[]" id="<?php echo str_replace(" ", "_",$item_data->upc_code).$lot_no; ?>_wPriceExis" value="0" class="wprice_input">
		</td>
	  <td style="text-align:center;">
		
		<input type="text" name="retail_price_exis[]" id="<?php echo str_replace(" ", "_",$item_data->upc_code).$lot_no; ?>_rPriceExis" value="0" class="rprice_input" onChange="retailPriceChanged('<?php echo $upc_code_tr; ?>')">
		</td>
	  <td style="text-align:center;">
		
		<input type="text" name="purchase_price_exis[]" id="<?php echo str_replace(" ", "_",$item_data->upc_code).$lot_no; ?>_pPriceExis" value="0" class="pprice_input">
		</td>
	  <td style="text-align:center;"><?php echo $item_data->stock; ?></td>
	  <td style="text-align:center;"><input name="item_quan[]" class="quant_input numberOnly" id="<?php echo str_replace(" ", "_",$item_data->upc_code).$lot_no; ?>" type="text" value="0"></th>
	  <td style="text-align:center;" class="adv_reason_sel">
		<select style="height:23px;width:80px;" class="reason_sel" name="resn_sel[]">
		  <option value="0">Reason</option>
		  <?php foreach($reason_arr as $res_key=>$res_val){	?>
			<option value="<?php echo $res_key;?>"><?php echo $res_val;?></option>
		  <?php } ?>
		</select>
	  </td>
	</tr>
	<?php	
		}
	}
	?>
</table>
<script>
$(document).ready(function () {
	//called when key is pressed in textbox
	$(".numberOnly").keypress(function (e) {
	 //if the letter is not digit then display error and don't type anything
	 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});
});</script>