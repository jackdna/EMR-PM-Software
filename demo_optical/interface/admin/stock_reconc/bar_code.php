<?php 

require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
function validate($new_lot_no,$lot_max_arr)
{
	if($lot_max_arr[$new_lot_no])
	{
		$new_lot_no++;
		return validate($new_lot_no,$lot_max_arr);
	}
	else return $new_lot_no;
}
$i=1;
$img="";
$bar_code_id=$_REQUEST['bar_code'];
$bar_code_arr=explode(",",$bar_code_id);
$loc_id = $_SESSION['pro_fac_id'];
$column_data_limit=13;
$rtData = array();
if(isset($_REQUEST['bar_code'])){
	
	$query5=imw_query("select id,color_name,color_code from in_color")or die(imw_error().' 26');
	while($row5=imw_fetch_array($query5)){
		$color_name_arr[$row5['id']]=$row5['color_name'];
	}
	
	$query6=imw_query("select id,color_name,color_code from in_frame_color")or die(imw_error().' 31');
	while($row6=imw_fetch_array($query6)){
		$frame_color_name_arr[$row6['id']]=$row6['color_name'];
	}
	
	$item_qry = "SELECT 
					`i`.`id`, `i`.`upc_code`, `i`.`name`, `i`.`module_type_id`, `m`.`module_type_name`, `i`.`qty_on_hand`, `i`.`color`,
					`i`.`discount`, `i`.`retail_price`, `i`.`retail_price_flag`, `i`.`formula`, `i`.`wholesale_cost`, `i`.`purchase_price`,
					`i`.`manufacturer_id`, `i`.`brand_id`, `i`.`frame_style`, i.a, i.b, i.ed, i.dbl, i.temple, i.bridge, i.fpd,
					`fs`.style_name,
					IFNULL(SUM(`lt`.`stock`), 0) AS 'stock', 
					IFNULL(`v`.`vendor_name`, '') AS 'vendor_name', 
					IF(
						`i`.`module_type_id` = 1, 
						IFNULL(`fb`.`frame_source`, ''), 
						IFNULL(`cb`.`brand_name`, '')
					) AS 'brand_name' 
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
					LEFT JOIN `in_frame_styles`	`fs` ON(`i`.`frame_style` = `fs`.id)
				WHERE 
					`i`.`upc_code` = '".$bar_code_id."' 
					AND `i`.`del_status`=0
				GROUP BY `lt`.`item_id`";
				
	
	$query = imw_query($item_qry)or die(imw_error().' 36');
	
	if(imw_num_rows($query)>0){
		$item_data = imw_fetch_object($query);
		
		$rtData['stock']	= true;
		$rtData['id'] 		= $item_data->id;
		$rtData['upc'] 		= $item_data->upc_code;
		if(strlen($item_data->name)>$column_data_limit){
			$pro_name=substr($item_data->name,0,$column_data_limit).'..';
		}else {$pro_name=$item_data->name;}
		$rtData['name'] 	= $pro_name;
		$rtData['mod_id'] 	= $item_data->module_type_id;
		$rtData['mod_name']	= ucwords($item_data->module_type_name);
		$rtData['qty']		= $item_data->qty_on_hand;
		$rtData['fac_qty']	= $item_data->stock;
		$rtData['vendor']	= ($item_data->vendor_name)?$item_data->vendor_name:"";
		$rtData['style']	= ($item_data->style_name)?$item_data->style_name:"";
		$rtData['size']		= "$item_data->fpd-$item_data->bridge-$item_data->temple";
	/*	$rtData['size']		= "<table style='width:100%;'>
								<tr>
									<td width='25%'><strong>A</strong> $item_data->a</td>
									<td width='25%'><strong>B</strong> $item_data->b</td>
									<td width='25%'><strong>ED</strong> $item_data->ed</td>
									<td width='25%'><strong>DBL</strong> $item_data->dbl</td>
								</tr></table>
								<table style='width:100%;'>
								<tr>
									<td width='33%'><strong>Temple</strong> $item_data->temple</td>
									<td width='33%'><strong>Bridge</strong> $item_data->bridge</td>
									<td width='auto'><strong>FPD</strong> $item_data->fpd</td>
								</tr>
							</table>";*/
		 $brandName=$item_data->brand_name;
		if(strlen($brandName)>$column_data_limit){
			$brand=substr($brandName,0,$column_data_limit).'..';
		}else {$brand=$brandName;}
		
		$rtData['brand']	= $brand;
		if($item_data->color>0){
			if($item_data->module_type_id=="3"){
				$rtData['color']	= ($color_name_arr[$item_data->color])?$color_name_arr[$item_data->color]:"";
			}else{
				$rtData['color']	= ($frame_color_name_arr[$item_data->color])?$frame_color_name_arr[$item_data->color]:"";
			}
		}else{
			$rtData['color']	= "";
		}
		
		$rtData['discount'] = (int)trim($item_data->discount);
		$rtData['retail_price'] = number_format( (float)$item_data->retail_price, 2 );
		$rtData['retail_price_flag'] = (int)$item_data->retail_price_flag;
		
		//get lot detail related to this item and location
		$exist_lot = imw_query("SELECT `lot_no`, stock, wholesale_price, 	purchase_price FROM `in_item_lot_total` WHERE `item_id`='".$item_data->id."' AND `loc_id`='".$loc_id."' and stock>0 order by id desc")or die(imw_error().' 135');
		while($lot_data=imw_fetch_object($exist_lot))
		{
			$lot_max_arr[$lot_data->lot_no]+=$lot_data->stock;
			$lot_wise_wholesale[$lot_data->lot_no]=$lot_data->wholesale_price;
			$lot_wise_purchase[$lot_data->lot_no]=$lot_data->purchase_price;
		}
		
		//get count of already added items in this lot first from temp table
		$q1=imw_query("select lot_no, SUM(qty) as ttlcnt from in_temp_batch_record where `loc_id` ='$loc_id' and `item_id`=$item_data->id group by lot_no")or die(imw_error().' 142');
		while($d1=imw_fetch_assoc($q1))
		{
			$lot_hold_arr[$d1['lot_no']]+=$d1['ttlcnt'];
		}
		//get count of already saved items in this lot second from oringal batch total table
		$q2=imw_query("select record.lot_no, SUM(in_item_quant) as ttlcnt from in_batch_records as record
		left join in_batch_table as batch
		ON record.in_batch_id=batch.id 
		where batch.`facility` ='$loc_id' and record.`in_item_id`=$item_data->id and batch.status='saved' group by record.lot_no")or die(imw_error().' 152');
		while($d2=imw_fetch_object($q2))
		{
			$lot_hold_arr[$d2->lot_no]+=$d2->ttlcnt;
		}
		
		//file_put_contents('test.txt', "\n------------ max allowed qty ------------\n".print_r($lot_max_arr,true)."\n------------ current qty ------------\n".print_r($lot_hold_arr,true));
		$pre_tot=0;
		//now get batch for this item
		foreach($lot_max_arr as $lot=>$max_qty)
		{
			//file_put_contents('test.txt', "\n------------\n $lot=> $max_qty", FILE_APPEND);
			//file_put_contents('test.txt', "\n------------\n $lot_hold_arr[$lot]-$pre_tot+1)>$max_qty", FILE_APPEND);
			if((($lot_hold_arr[$lot]-$pre_tot)+1)>$max_qty)
			{/*$pre_tot+=$max_qty;*/}
			else 
			{$new_lot_no=$lot;break;}
		}
		
		if(!$new_lot_no)
		{
			$new_lot_no=date('Ymd').$_SESSION["authId"].'1';
			$new_lot_no=validate($new_lot_no,$lot_max_arr);
		}
		$rtData['lot_no']=	$new_lot_no;
		
		//file_put_contents('test.txt', "\n------------\ngoing to assign $new_lot_no", FILE_APPEND);
		/*Retail Price Calculation*/
		$retail_price_markup_modules = array(1, 3, 5, 6);	/*List of module type id's for which retail price markup functionality is given*/
		
		$item_data->formula = trim($item_data->formula);
		//$rtData['formula_save'] = $item_data->formula;	/*Value for hidden formula field - edited for the item*/
		
		if( in_array($item_data->module_type_id, $retail_price_markup_modules) ){
			
			if( $item_data->formula == '' ){
				if( $item_data->module_type_id == '1' ){
					$item_data->formula = get_retail_formula($item_data->module_type_id, array('manufacturer_id' => $item_data->manufacturer_id, 'brand_id' => $item_data->brand_id, 'frame_style' => $item_data->frame_style) );
				}
				else{
					$item_data->formula = get_retail_formula($item_data->module_type_id, array('manufacturer_id' => $item_data->manufacturer_id, 'brand_id' => $item_data->brand_id) );
				}
			}
			
			/*Final Retail Price for the Item - based on formula calculation*/
			if( $item_data->formula != '' && ($rtData['retail_price_flag'] == 0 || $rtData['retail_price'] <=0 )){
				$rtData['retail_price'] = calculate_markup_price($item_data->formula, $lot_wise_wholesale[$rtData['lot_no']], $lot_wise_purchase[$rtData['lot_no']]);
				$rtData['retail_price'] = number_format((float)$rtData['retail_price'], 2);
			}
			/*End Final Retail Price for the Item*/
		}
		/*End Retail Price Calculation*/
		$rtData['wholesale_price']=$lot_wise_wholesale[$rtData['lot_no']];
		$rtData['purchase_price']=$lot_wise_purchase[$rtData['lot_no']];
		//add item in temporary table
		$q="insert into in_temp_batch_record set `user_id`='".$_SESSION["authId"]."',
		  `loc_id` ='$loc_id',
		  `item_id`=$item_data->id,
		  `lot_no`='$new_lot_no',
		  `qty`=1,
		  `dated`='".date('Y-m-d')."'";
		imw_query($q)or die(imw_error().' 174');
		$rtData['fac_lot_qty']	=($lot_max_arr[$rtData['lot_no']]>0)?$lot_max_arr[$rtData['lot_no']]:0;
		
	}
}

print json_encode($rtData);
?>