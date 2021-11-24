<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Show Item Information
Access Type: Include file
*/
require_once("../../config/config.php");
if($_REQUEST['action']!="" && $_REQUEST['action']=="managestock" && $_REQUEST['item_id']>0)
{
	$qry = imw_query("select * from in_item where upc_code = '".trim($_REQUEST['code'])."' LIMIT 1");
	$returnArr = array();
	$nums = imw_num_rows($qry);
	if($nums > 0)
	{
		while($row = imw_fetch_array($qry))
		{
			$returnArr[] = $row;
		}	
		echo json_encode($returnArr);
	}
}
else if($_REQUEST['action']!="" && $_REQUEST['action']=="manageorder")
{
	$qry = imw_query("select * from in_order_details where id='".$_REQUEST['code']."' LIMIT 1");
	$returnArr = array();
	$nums = imw_num_rows($qry);
	if($nums > 0)
	{
		while($row = imw_fetch_array($qry))
		{
			$returnArr[] = $row;
		}	
		echo json_encode($returnArr);
	}
}
else if($_REQUEST['action']!="" && $_REQUEST['action']=="manageframe")
{
	$qry = imw_query("select item_name,price,discount,total_amount,qty,item_prac_code from in_order_details where id='".$_REQUEST['frame_id']."' LIMIT 1");
	$returnArr = array();
	$nums = imw_num_rows($qry);
	if($nums > 0)
	{
		while($row = imw_fetch_array($qry))
		{
			$returnArr[] = $row;
		}	
		echo json_encode($returnArr);
	}
}
else{
	if($_REQUEST['action']!="" && $_REQUEST['action']=="managestock")
	{
		include_once("../../library/classes/functions.php");
		$itemId = trim($_REQUEST['code']);
		$qry = imw_query("select in_item.*,in_item_loc_total.stock from in_item LEFT JOIN in_item_loc_total on in_item.id=in_item_loc_total.item_id and loc_id='".$_SESSION['pro_fac_id']."' where in_item.id = '".$itemId."' LIMIT 1");
		$returnArr = array();
		$nums = imw_num_rows($qry);
		if($nums > 0)
		{
			while($row = imw_fetch_assoc($qry))
			{
				/*Stock Image for Frame*/
				if( $row['module_type_id']=='1' ){
					$stock_image	= 'no_image_thumb.jpg';
					$stock_image_l	= 'no_image_xl.jpg';
					$image_base_path = $GLOBALS['DIR_PATH'].'/images/frame_stock';
					
					if( trim($row['stock_image'])!='' ){
						$item_image_name= trim($row['stock_image']);
						$stock_image	= 'thumb/'.$item_image_name;
						$stock_image_l	= 'xl/'.$item_image_name;
						/*Use Default Image if no stock Image exists*/
						if( !file_exists($image_base_path.'/'.$stock_image) ){
							$stock_image = 'no_image_thumb.jpg';
						}
						if( !file_exists($image_base_path.'/'.$stock_image_l) ){
							$stock_image_l = 'no_image_xl.jpg';
						}
					}
					$row['stock_image'] = $stock_image;
					$row['stock_image_large'] = $stock_image_l;
					
					if($row['color']!='0'){
						$sqlItemColor = 'SELECT `color_name` FROM `in_frame_color` WHERE `id`=\''.$row['color'].'\'';
						$sqlItemColor = imw_query($sqlItemColor);
						if($sqlItemColor && imw_num_rows($sqlItemColor)){
							$sqlItemColor = imw_fetch_assoc($sqlItemColor);
							$row['color'] = $sqlItemColor['color_name'];
						}
					}
					
					/*Item Stock at locations*/
					$returnArr['stockData'] = array();
					$stockSql = 'SELECT `loc`.`loc_name` AS \'name\', IF(ISNULL(`li`.`stock`), 0, `li`.`stock`) AS \'stock\'
								FROM `in_location` `loc`
								LEFT JOIN `in_item_loc_total` `li` ON(`loc`.`id` = `li`.`loc_id` AND `li`.`item_id` = '.((int)$itemId).')
								WHERE `loc`.`del_status` = 0
								ORDER BY `loc`.`loc_name` ASC';
					$stockResp = imw_query($stockSql);
					if( $stockResp && imw_num_rows($stockResp)>0 ){
						while($stockRow = imw_fetch_object($stockResp)){
							$stockData = array();
							$stockData['name'] = $stockRow->name;
							$stockData['stock'] = $stockRow->stock;
							array_push($returnArr['stockData'], $stockData);
						}
					}
					/*End Item Stock at locations*/
				}
				
				/*Retail Prices Markup caculation*/
					$retail_price_markup_modules = array(1, 3, 5, 6);	/*List of module type id's for which retail price markup functionality is given*/
					if( in_array($row['module_type_id'], $retail_price_markup_modules) && $row['retail_price_flag'] == '0' ){
						
						$row['formula'] = trim($row['formula']);
						if( $row['formula'] == '' ){
							if( $row['module_type_id']=='1' )
								$row['formula'] = get_retail_formula($row['module_type_id'], array('manufacturer_id'=>$row['manufacturer_id'], 'brand_id'=>$row['brand_id'], 'frame_style'=>$row['frame_style']));
							else
								$row['formula'] = get_retail_formula($row['module_type_id'], array('manufacturer_id'=>$row['manufacturer_id'], 'brand_id'=>$row['brand_id']));
						}
						
						/*Final Retail Price for the Item - based on formula calculation*/
						if( $row['formula']!='' ){
							$row['retail_price'] = calculate_markup_price($row['formula'], $row['wholesale_cost'], $row['purchase_price']);
							$row['retail_price'] = number_format((float)$row['retail_price'], 2);
						}
						/*End Final Retail Price for the Item*/
					}
				/*Retail Prices Markup caculation*/
				
				$returnArr[] = $row;
			}	
			echo json_encode($returnArr);
		}
	}
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_brand")
{
	if($_REQUEST['mid']!=0 && $_REQUEST['mid']!="")
	{
		$brand_whr = " and bm.manufacture_id = '".$_REQUEST['mid']."'";
		$sql = "select fs.frame_source,bm.brand_id, bm.manufacture_id from in_brand_manufacture as bm inner join in_frame_sources as fs on fs.id=bm.brand_id where fs.del_status = '0' $brand_whr order by fs.frame_source asc";
	}
	else
	{
		$sql = "SELECT frame_source,id as brand_id FROM in_frame_sources WHERE del_status = 0 ORDER BY frame_source ASC";
	}
	
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
		$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($_REQUEST['bid']==$rows['brand_id'])
			{
				$sel = 'selected';
			}
			else
			{
				$sel="";
			}
			echo "<option ".$sel." value='".$rows['brand_id']."'>".$rows['frame_source']."</option>";
		}
	 }
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_style")
{
	if($_REQUEST['bid']!=0 && $_REQUEST['bid']!="")
	{
		$sty_whr = " and bs.brand_id = '".$_REQUEST['bid']."'";
		$sql = "select fs.style_name,bs.style_id, bs.brand_id from in_style_brand as bs
	 inner join in_frame_styles as fs on fs.id=bs.style_id 
	 where fs.del_status = '0' $sty_whr order by fs.style_name asc";
	}
	else
	{
		$sql = "select id as style_id, style_name from in_frame_styles where del_status='0' order by style_name asc";
	}
	 
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($_REQUEST['sid']==$rows['style_id'])
			{
				$sel = 'selected';
			}
			else
			{
				$sel="";
			}
			echo "<option ".$sel." value='".$rows['style_id']."'>".$rows['style_name']."</option>";
		}
	 }	
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="lens_selection")
{
	if($_REQUEST['type']!="" && $_REQUEST['type'] > 0)
	{
		$type_field = " and type_id = '".$_REQUEST['type']."'";
	}
	if($_REQUEST['progressive']!="" && $_REQUEST['progressive'] > 0)
	{
		$progressive_field = " and progressive_id = '".$_REQUEST['progressive']."'";	
	}
	if($_REQUEST['material']!="" && $_REQUEST['material'] > 0)
	{
		$material_field = " and material_id = '".$_REQUEST['material']."'";
	}
	if($_REQUEST['transition']!="" && $_REQUEST['transition'] > 0)
	{
		$transition_field = " and transition_id = '".$_REQUEST['transition']."'";
	}	
	if($_REQUEST['ar']!="" && $_REQUEST['ar'] > 0)
	{
		$ar_field = " and a_r_id = '".$_REQUEST['ar']."'";	
	}		
	if($_REQUEST['tint']!="" && $_REQUEST['tint'] > 0)
	{
		$tint_field = " and tint_id = '".$_REQUEST['tint']."'";
	}			
	if($_REQUEST['polarized']!="" && $_REQUEST['polarized'] > 0)
	{
		$polarized_field = " and polarized_id	 = '".$_REQUEST['polarized']."'";	
	}	
	if($_REQUEST['edge']!="" && $_REQUEST['edge'] > 0)
	{
		$edge_field = " and edge_id = '".$_REQUEST['edge']."'";
	}
	if($_REQUEST['color']!="" && $_REQUEST['color'] > 0)
	{
		$color_field = " and color = '".$_REQUEST['color']."'";
	}
		
	$fields = $type_field.$progressive_field.$material_field.$transition_field.$ar_field.$polarized_field.$edge_field.$color_field;

	$sql = "select * from in_item where module_type_id='2' $fields";
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($_REQUEST['lens_sel_id']==$rows['id'])
			{
				$sel = 'selected';
			}
			else
			{
				$sel="";
			}
			echo "<option ".$sel." value='".$rows['id']."'>".$rows['name']."</option>";
		}
	 }	
}


if($_REQUEST['action']!="" && $_REQUEST['action']=="findupc")
{
	if($_REQUEST['manufac']!="" && $_REQUEST['manufac']>0)
	{
		$type_field = " and manufacturer_id = '".$_REQUEST['manufac']."'";
	}
	if($_REQUEST['brand']!="" && $_REQUEST['brand']>0)
	{
		$brand_field = " and brand_id = '".$_REQUEST['brand']."'";	
	}
	if($_REQUEST['color']!="" && $_REQUEST['color']>0)
	{
		$color_field = " and color = '".$_REQUEST['color']."'";
	}
	if($_REQUEST['shape']!="" && $_REQUEST['shape']>0)
	{
		$shape_field = " and frame_shape = '".$_REQUEST['shape']."'";
	}	
	if($_REQUEST['style']!="" && $_REQUEST['style']>0)
	{
		$shape_field = " and frame_style = '".$_REQUEST['style']."'";
	}	
		
	$fields = $type_field.$brand_field.$shape_field.$color_field;

	$sql = "select id from in_item where module_type_id='1' $fields";
	$res = imw_query($sql);
	$nums = imw_num_rows($res);
	$itemarr=array();
	if($nums > 0)
	{
		$sel="";
		$x=0;
		while($rows = imw_fetch_array($res))
		{
			$itemarr[$x] = $rows['id'];
		$x++;
		}
		echo json_encode($itemarr);
	}	
	else
	{
		echo "false";	
	}
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="findLenseUpc")
{
	if($_REQUEST['type_id']!="" && $_REQUEST['type_id']>0)
	{
		$type_field = " and type_id = '".$_REQUEST['type_id']."'";
	}
	if($_REQUEST['progressive_id']!="" && $_REQUEST['progressive_id']>0)
	{
		$brand_field = " and progressive_id = '".$_REQUEST['progressive_id']."'";	
	}
	if($_REQUEST['material_id']!="" && $_REQUEST['material_id']>0)
	{
		$color_field = " and material_id = '".$_REQUEST['material_id']."'";
	}
	if($_REQUEST['transition_id']!="" && $_REQUEST['transition_id']>0)
	{
		$shape_field = " and transition_id = '".$_REQUEST['transition_id']."'";
	}	
	if($_REQUEST['a_r_id']!="" && $_REQUEST['a_r_id']>0)
	{
		$shape_field = " and a_r_id = '".$_REQUEST['a_r_id']."'";
	}	
	if($_REQUEST['tint_id']!="" && $_REQUEST['tint_id']>0)
	{
		$shape_field = " and tint_id = '".$_REQUEST['tint_id']."'";
	}	
	if($_REQUEST['polarized_id']!="" && $_REQUEST['polarized_id']>0)
	{
		$shape_field = " and polarized_id = '".$_REQUEST['polarized_id']."'";
	}	
	if($_REQUEST['edge_id']!="" && $_REQUEST['edge_id']>0)
	{
		$shape_field = " and edge_id = '".$_REQUEST['edge_id']."'";
	}	
	if($_REQUEST['color_id']!="" && $_REQUEST['color_id']>0)
	{
		$shape_field = " and color = '".$_REQUEST['color_id']."'";
	}	
		
	$fields = $type_field.$brand_field.$shape_field.$color_field;

	$sql = "select id from in_item where module_type_id='2' $fields";

	$res = imw_query($sql);
	$nums = imw_num_rows($res);
	$itemarr=array();
	if($nums > 0)
	{
		$sel="";
		$x=0;
		while($rows = imw_fetch_array($res))
		{
			$itemarr[$x] = $rows['id'];
		$x++;
		}
		echo json_encode($itemarr);
	}	
	else
	{
		echo "false";	
	}
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_prac_code_text")
{
	$prac_data = '';
	if($_REQUEST['prac_code_id']!="" && $_REQUEST['prac_code_id']>0){
		
		$qry = imw_query("select cpt_prac_code, cpt_desc from cpt_fee_tbl where cpt_fee_id='".$_REQUEST['prac_code_id']."' and delete_status = '0' and LOWER(status)='active'");
		if($qry && imw_num_rows($qry)>0){
			$res = imw_fetch_assoc($qry);
			$prac_data = $res['cpt_prac_code']."~~~~".$res['cpt_desc'];
		}
	}
	
	if(isset($_REQUEST['modType']) && $_REQUEST['modType']!="" && $prac_data == ''){
		
		$sql = "";
		$flag = false;
		if($_REQUEST['modType']=="1"){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='1'";
			$flag = true;
		}
		elseif($_REQUEST['modType']=="2" && isset($_REQUEST['td_id']) && $_REQUEST['td_id']!=""){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='2' ";
			if($_REQUEST['td_id']=="item_prac_code_1_lensD"){
				$sql .= " AND `sub_module`='type'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_2_lensD"){
				$sql .= " AND `sub_module`='material'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_3_lensD"){
				$sql .= " AND `sub_module`='coating'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_4_lensD"){
				$sql .= " AND `sub_module`='transition'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_5_lensD"){
				$sql .= " AND `sub_module`='polarized'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_6_lensD"){
				$sql .= " AND `sub_module`='tint'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_9_lensD"){
				$sql .= " AND `sub_module`='progressive'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_10_lensD"){
				$sql .= " AND `sub_module`='edge'";
				$flag = true;
			}
			elseif($_REQUEST['td_id']=="item_prac_code_11_lensD"){
				$sql .= " AND `sub_module`='color'";
				$flag = true;
			}
		}
		elseif($_REQUEST['modType']=="3"){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='3'";
			$flag = true;
		}
		elseif($_REQUEST['modType']=="8"){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='8'";
			$flag = true;
		}elseif($_REQUEST['modType']=="5"){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='5'";
			$flag = true;
		}elseif($_REQUEST['modType']=="6"){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='6'";
			$flag = true;
		}elseif($_REQUEST['modType']=="7"){
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='7'";
			$flag = true;
		}
		
		if($sql!="" && $flag){
			$resp = imw_query($sql);
			if($resp && imw_num_rows($resp)>0){
				$resp = imw_fetch_assoc($resp);
				$prac_data = $resp['prac_code']."~~~~".$resp['prac_code'];
			}
		}
	}
	
	print $prac_data;
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_price_from_praccode_material"){
	
	$prac_codes = "";
	$retail_price = "";
	
	$seg_fields = array('Single Vision'=>'sv', 'Progressive'=>'pr', 'BiFocal'=>'bf', 'TriFocal'=>'tf');
	
	$seg_val = $_REQUEST['seg_val'];
	
	$prac_field = (isset($seg_fields[$seg_val]))?$seg_fields[$seg_val]:'prac_code_sv';
	
	$sql = 'SELECT `id`, `prac_code_'.$prac_field.'` AS \'prac_code\', `wholesale_price`, `purchase_price`, `retail_price`, `vw_code`, `material_name`  FROM `'.$_REQUEST['tb_name'].'` WHERE `id`='.$_REQUEST['sel_id'];
	
	$sql = imw_query($sql);
	if($sql && imw_num_rows($sql)>0){
		$row = imw_fetch_assoc($sql);
		$prac_codes = str_replace(";", ",", $row['prac_code']);
		
		$retail_price = json_decode($row['retail_price']);
		
		$retail_price = (isset($seg_fields[$seg_val]))?$retail_price->{$seg_fields[$seg_val]}:$retail_price->sv;
		$retail_price = explode(';', $retail_price);
		
		$vw_code = $row['vw_code'];
		$pracs = explode(";", $row['prac_code']);
		$details = array(array('id'=>$row['id'], 'name'=>$row['material_name']));
		
		$idsPushed = array($row['id']);
		/*Description if more then one Prac Code*/
		if(count($pracs)>1 && $vw_code!=""){
			$vw_code = explode("-", $vw_code);
			$parent_vw_code = $vw_code[0]."-".$vw_code[1]."-NONE-NONE-00";
			$details_resp = imw_query("SELECT `id`, `material_name` FROM `in_lens_material` WHERE `vw_code`='".$parent_vw_code."' order by del_status asc LIMIT 1");
			if($details_resp && imw_num_rows($details_resp)>0){
				$details_resp = imw_fetch_assoc($details_resp);
				$main_detail = explode(" ", $details_resp['material_name']);
				
				$base_details = str_replace($main_detail, "", $details[0]);
				if( trim($base_details['name']) != "" )
					 $details[0] = $base_details;
				 
				 if( !in_array($details_resp['id'], $idsPushed) || trim($details_resp['id']) == '' )
				 {
					array_push($details, array('id'=>$details_resp['id'], 'name'=>$details_resp['material_name']));
					array_push($idsPushed, $details_resp['id']);
				 }
			}
		}
		
		if(count($pracs) > count($details)){
			$elements = count($pracs) - count($details);
			$details1 = array_fill(count($details), $elements, array('id'=>'', 'name'=>''));
			$details = array_merge($details, $details1);
		}
		
		if(count($details)<count($retail_price)){
			foreach($pracs as $key=>$dtval){
				if(!isset($retail_price[$key]))
					$retail_price[$dtval] = "0.00";
				else
					$retail_price[$dtval] = $retail_price[$key];
			}
		}
		
		foreach($pracs as $key=>$dtval){
			if(!isset($retail_price[$key]))
				$retail_price[$dtval] = "0.00";
			else
				$retail_price[$dtval] = $retail_price[$key];
		}
	}
	array_walk_recursive($details,function(&$v) {$v = trim($v);});
	
	$row_details = array();
	if($prac_codes!=""){
		$qry = imw_query("SELECT 
								b.cpt_fee_id, 
								b.cpt_desc, 
								b.cpt_prac_code, 
								a.cpt_fee 
							FROM 
								cpt_fee_table AS a 
								INNER JOIN cpt_fee_tbl AS b on b.cpt_fee_id = a.cpt_fee_id 
							WHERE 
								a.cpt_fee_id IN(".$prac_codes.") 
								AND a.fee_table_column_id = '1'
								AND LOWER(b.status)='active'
								AND delete_status = '0'
								ORDER BY FIELD( a.cpt_fee_id, ".$prac_codes.")");
		if($qry && imw_num_rows($qry)>0){
			while($row = imw_fetch_object($qry)){
				$detail = array_pop($details);
				$prac 	= (string)$row->cpt_prac_code.'-';
				$row_details[$prac]['prac'] = $row->cpt_prac_code;
				$row_details[$prac]['cpt_fee'] = $row->cpt_fee;
				$row_details[$prac]['retail'] = $retail_price[$row->cpt_fee_id];
				$row_details[$prac]['detail'] =($detail['name'])?$detail['name']:$row->cpt_desc;
				$row_details[$prac]['id'] = $detail['id'];
			}
		}
		imw_free_result($qry);
	}
	
	if(count($row_details)==0){
		$sql = "SELECT `dp`.`prac_code`, `dp`.`retail_price`, `f`.`cpt_desc`, `f`.`cpt_fee_id`, `fee`.`cpt_fee`
					FROM
						`in_prac_codes` `dp` LEFT JOIN `cpt_fee_tbl` `f` ON(`dp`.`prac_code` = `f`.`cpt_prac_code`)
						LEFT JOIN `cpt_category_tbl` `cat` ON(`f`.`cpt_cat_id` = `cat`.`cpt_cat_id` AND `cat`.`cpt_category` = 'Optical')
						LEFT JOIN `cpt_fee_table` `fee` ON(`fee`.`cpt_fee_id` = `f`.`cpt_fee_id` AND `fee`.`fee_table_column_id` = 1)
					WHERE `dp`.`module_id` = '2'";
		
		$flag = false;
		if($_REQUEST['tb_name']=="in_lens_material"){
			$sql .=" AND `sub_module`='material'";
			$flag = true;
		}
		
		if($flag){
			$resp = imw_query($sql);
			if($resp && imw_num_rows($resp)>0){
				$row = imw_fetch_object($resp);
				$detail = array_pop($details);
				$retail = array_pop($retail_price);
				$prac 	= (string)$row->prac_code.'-';
				$row_details[$prac]['prac'] = $row->prac_code;
				$row_details[$prac]['cpt_fee'] = '0.00';
				$row_details[$prac]['retail'] = $row->retail_price;
				$row_details[$prac]['detail'] =($detail['name'])?$detail['name']:$row->cpt_desc;
				$row_details[$prac]['id'] = $detail['id'];
				
				
			}
		}
	}
	
	print json_encode($row_details);
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_price_from_praccode")
{
	include_once("../../library/classes/functions.php");
	
	$cpt_prac_code = $cpt_fee = $cpt_fee_id = $cpt_desc = '';
	
	/*Prace Code and Price for Prism diopter row*/
	if( in_array($_REQUEST['tb_name'], array('in_lens_diopter', 'in_lens_oversize')) )
	{
		$entryTypes = array('in_lens_diopter'=>2, 'in_lens_oversize'=>3);
		$entryTypeCode = $entryTypes[$_REQUEST['tb_name']];
		

		$sql  = "SELECT `prac_code`, `prac_id`, `retail_price` FROM `in_lens_type_vcode` WHERE `del_status` = 0 AND `entry_type` = ".$entryTypeCode;
		$resp = imw_query($sql);
		if( $resp && imw_num_rows($resp) === 1 )
		{
			$resp = imw_fetch_assoc($resp);
			$cpt_prac_code = $resp['prac_code'];
			$cpt_fee = $resp['retail_price'];
			$cpt_fee_id = $resp['prac_id'];
		}
		echo $cpt_prac_code."-:".$cpt_fee."-:".$cpt_fee_id;
		exit;
		/*End Prace Code and Price for Prism diopter row*/
	}
	
	/*Prac Code Based on Rx Range for based on Seg Type (Add Price to Design)*/
	$segTypePrice = false;
	if($_REQUEST['tb_name']=="in_lens_design"){
		$segTypePrice = 0;
		/*Seg Type Id*/
		$type_id = (int)$_REQUEST['seg_val'];
		
		$seg_od = $_REQUEST['sph_od'];
		$cyl_od = $_REQUEST['cyl_od'];
		
		/*Only (+ve) Value for Cylinder in DB table*/
		$cyl_od = trim($cyl_od, "-, +");
		
		/*Type cast for validation (only numeriocs)*/
		$seg_od = (float)$seg_od;
		$cyl_od = (float)$cyl_od;
		
		$sql = 'SELECT `prac_id`, `retail_price`, `prac_code` FROM `in_lens_type_vcode` WHERE `lens_type_id` = '.$type_id;
		
		/*Compatision Operators & Columns selection for Seg Type OD (based on Value type (+ve or -ve))*/
		if($seg_od>=0){
			$sql .= ' AND '.$seg_od.' >= CONVERT(`sph_plus_from`, DECIMAL(10, 2))';
			$sql .= ' AND '.$seg_od.' <= CONVERT(`sph_plus_to`, DECIMAL(10, 2))';
		}
		else{
			$sql .= ' AND '.$seg_od.' <= -CONVERT(REPLACE(sph_min_from,\'-\',\'\'), DECIMAL(10, 2))';
			$sql .= ' AND '.$seg_od.' >= -CONVERT(REPLACE(sph_min_to,\'-\',\'\'), DECIMAL(10, 2))';
		}
		
		/*Cylinder Columns*/
		if( $cyl_od >= 0 ){
			$sql .= ' AND '.$cyl_od.' >= CONVERT(ABS(`cyl_from`), DECIMAL(10, 2))';
			$sql .= ' AND '.$cyl_od.' <= CONVERT(ABS(`cyl_to`), DECIMAL(10, 2))';
		}
		else
		{
			$sql .= ' AND '.$cyl_od.' <= CONVERT(ABS(`cyl_from`), DECIMAL(10, 2))';
			$sql .= ' AND '.$cyl_od.' >= CONVERT(ABS(`cyl_to`), DECIMAL(10, 2))';
		}
		$sql .= ' AND `del_status` = 0';
		$sql .= ' LIMIT 1';
		$cpt_fee_id = false;
		$retail_price = "";
		$range_prac_resp = imw_query($sql);
		if($range_prac_resp && imw_num_rows($range_prac_resp)>0){
			$cpt_fee_row = imw_fetch_assoc($range_prac_resp);
			$cpt_fee_id = $cpt_fee_row['prac_id'];
			$retail_price = number_format((float)$cpt_fee_row['retail_price'], 2);
			$segTypeVcode = $cpt_fee_row['prac_code'];
		}
		
		/*$cpt_data = array();
		if($cpt_fee_id){
			$cpt_data = get_price_details_by_cpt_id($cpt_fee_id);
		}*/
		
		if(count($cpt_fee_id)>0){
			/*Add SegType Price To Design Price*/
			$segTypePrice = $retail_price;
		}
	}
	/*Prac Code Based on Rx Range for based on Seg Type (Add Price to Design)*/
	
	$whr_cl = "id";
	$cl = "prac_code";
	if($_REQUEST['for']=="uv")
	{
		$whr_cl = "item_id";
		$cl = "uv_prac_code";
	}
	if($_REQUEST['for']=="pgx")
	{
		$whr_cl = "item_id";
		$cl = "pgx_prac_code";
	}
	if($_REQUEST['for']=="frame")
	{
		$cl = "item_prac_code";
	}
	
	$price_fields = "";
	if(in_array($_REQUEST['tb_name'], array("in_lens_design", "in_lens_material", "in_lens_ar"))){
		$price_fields = ", lt.wholesale_price, lt.purchase_price, lt.retail_price";
	}
	
	$getCPTPriceQry = imw_query("SELECT b.cpt_fee_id, b.cpt_desc, b.cpt_prac_code, a.cpt_fee".$price_fields." FROM `cpt_fee_table` as a inner join ".$_REQUEST['tb_name']." as lt on lt.$whr_cl='".$_REQUEST['sel_id']."' left join cpt_fee_tbl as b on b.cpt_fee_id = lt.$cl
										WHERE 
										a.cpt_fee_id = lt.$cl
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
	$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
	$cpt_fee = ($price_fields != "" && $getCPTPriceRow['retail_price']!="0.00")?$getCPTPriceRow['retail_price']:'0.00';
	$cpt_fee_id = $getCPTPriceRow['cpt_fee_id'];
	$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
	$cpt_desc=$getCPTPriceRow['cpt_desc'];
	if(imw_num_rows($getCPTPriceQry)==0){
		$getCPTPriceQry = imw_query("SELECT b.cpt_fee_id, b.cpt_desc, b.cpt_prac_code, a.cpt_fee".$price_fields." FROM `cpt_fee_table` as a inner join ".$_REQUEST['tb_name']." as lt on lt.$whr_cl='".$_REQUEST['sel_id']."' left join cpt_fee_tbl as b on ( b.cpt4_code = lt.prac_code OR b.cpt_desc = lt.$cl ) 
										WHERE 
										a.cpt_fee_id = lt.$cl
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		$cpt_fee = ($price_fields != "" && $getCPTPriceRow['retail_price']!="0.00")?$getCPTPriceRow['retail_price']:'0.00';
		$cpt_fee_id = $getCPTPriceRow['cpt_fee_id'];
		$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
		$cpt_desc=$getCPTPriceRow['cpt_desc'];
	}
	if($cpt_prac_code==""){
		$sql = "SELECT `dp`.`prac_code`, `dp`.`retail_price`, `f`.`cpt_desc`, `f`.`cpt_fee_id`, `fee`.`cpt_fee` FROM `in_prac_codes` `dp` LEFT JOIN `cpt_fee_tbl` `f` ON(`dp`.`prac_code` = `f`.`cpt_prac_code`) LEFT JOIN `cpt_category_tbl` `cat` ON(`f`.`cpt_cat_id` = `cat`.`cpt_cat_id` AND `cat`.`cpt_category` = 'Optical') LEFT JOIN `cpt_fee_table` `fee` ON(`fee`.`cpt_fee_id` = `f`.`cpt_fee_id` AND `fee`.`fee_table_column_id` = 1) WHERE `dp`.`module_id` = '2'";
		
		$flag = false;
		if($_REQUEST['tb_name']=="in_lens_type"){
			$default_key = $_REQUEST['default_key'];
			$sql .=" AND `dp`.`sub_module`='".$default_key."'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_progressive"){
			$sql .=" AND `dp`.`sub_module`='progressive'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_material"){
			$sql .=" AND `dp`.`sub_module`='material'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_transition"){
			$sql .=" AND `dp`.`sub_module`='transition'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_ar"){
			$sql .=" AND `dp`.`sub_module`='coating'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_tint"){
			$sql .=" AND `dp`.`sub_module`='tint'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_polarized"){
			$sql .=" AND `dp`.`sub_module`='polarized'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_edge"){
			$sql .=" AND `dp`.`sub_module`='edge'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_lens_design"){
			$sql .=" AND `dp`.`sub_module`='design'";
			$flag = true;
		}
		/*elseif($_REQUEST['tb_name']=="in_lens_color"){
			$sql .=" AND `sub_module`='type'";
			$flag = true;
		}*/
		elseif($_REQUEST['tb_name']=="in_lens_color"){
			$sql .=" AND `dp`.`sub_module`='color'";
			$flag = true;
		}
		elseif($_REQUEST['tb_name']=="in_item_price_details" && $_REQUEST['for']=="uv"){
			$sql .=" AND `dp`.`sub_module`='uv400'";
			$flag = true;
		}
		
		$sql .= 'LIMIT 1';
		
		if($flag){
			$resp = imw_query($sql);
			if($resp && imw_num_rows($resp)>0){
				$resp = imw_fetch_assoc($resp);
				
				$cpt_prac_code = $resp['prac_code'];
				//$cpt_fee = ($_REQUEST['tb_name']=="in_lens_type")?'0.00':$resp['cpt_fee'];
				$cpt_fee = $resp['retail_price'];
				$cpt_fee_id = $resp['cpt_fee_id'];
				$cpt_desc = $resp['cpt_desc'];
			}
		}
	}
	
	if($_REQUEST['tb_name']=="in_lens_design"){
		$cpt_fee = number_format((float)$cpt_fee + (float)$segTypePrice, 2);
		//add seg type v code here
		if(constant('SHOW_SEG_VCODE')=='TRUE' && $segTypeVcode)
		{
			//overwrite cpt prac code 
			$cpt_prac_code = $segTypeVcode;
			$cpt_desc = $segTypeVcode;
		}
	}
	echo $cpt_prac_code."-:".$cpt_fee."-:".$cpt_fee_id."-:".$cpt_desc;
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_price_custom_charge")
{
	$feeId = (int)$_REQUEST['fee_id'];
	
	$fee_value = 0.00;
	
	if( $feeId > 0 ){
		$sql = "SELECT `cpt_fee` FROM `cpt_fee_table` WHERE `cpt_fee_id`=".$feeId." AND `fee_table_column_id`=1";
		$resp = imw_query($sql);
		
		if( $resp && imw_num_rows($resp) > 0 ){
			$fee_value = imw_fetch_assoc($resp);
			$fee_value = $fee_value['cpt_fee'];
		}
	}
	echo $fee_value;
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_price_from_praccode_multi")
{
	$sel_ids = explode(',', $_REQUEST['sel_ids']);
	$returnVals = array();
	foreach($sel_ids as $idsKey=>$idsVal){
		$whr_cl = "id";
		$cl = "prac_code";
		if($_REQUEST['for']=="uv")
		{
			$whr_cl = "item_id";
			$cl = "uv_prac_code";
		}
		if($_REQUEST['for']=="pgx")
		{
			$whr_cl = "item_id";
			$cl = "pgx_prac_code";
		}
		if($_REQUEST['for']=="frame")
		{
			$cl = "item_prac_code";
		}
		
		$price_fields = "";
		if(in_array($_REQUEST['tb_name'], array("in_lens_design", "in_lens_material", "in_lens_ar"))){
			$price_fields = ", lt.wholesale_price, lt.purchase_price, lt.retail_price";
		}
		
		$getCPTPriceQry = imw_query("SELECT b.cpt_fee_id, b.cpt_desc, b.cpt_prac_code, a.cpt_fee".$price_fields." FROM `cpt_fee_table` as a inner join ".$_REQUEST['tb_name']." as lt on lt.$whr_cl='".$idsVal."' left join cpt_fee_tbl as b on b.cpt_fee_id = lt.$cl
											WHERE 
											a.cpt_fee_id = lt.$cl
											AND a.cpt_fee_id = b.cpt_fee_id
											AND a.fee_table_column_id = '1'
											AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		/*$cpt_fee = ($price_fields!="" && $getCPTPriceRow['retail_price']!="0.00")?$getCPTPriceRow['retail_price']:$getCPTPriceRow['cpt_fee'];*/
		$cpt_fee = ($price_fields!="" && $getCPTPriceRow['retail_price']!="0.00")?$getCPTPriceRow['retail_price']:'0.00';
		$cpt_fee_id = $getCPTPriceRow['cpt_fee_id'];
		$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
		$cpt_desc=$getCPTPriceRow['cpt_desc'];
		if(imw_num_rows($getCPTPriceQry)==0){
			$getCPTPriceQry = imw_query("SELECT b.cpt_fee_id, b.cpt_desc, b.cpt_prac_code, a.cpt_fee".$price_fields." FROM `cpt_fee_table` as a inner join ".$_REQUEST['tb_name']." as lt on lt.$whr_cl='".$idsVal."' left join cpt_fee_tbl as b on ( b.cpt4_code = lt.prac_code OR b.cpt_desc = lt.$cl ) 
											WHERE 
											a.cpt_fee_id = lt.$cl
											AND a.cpt_fee_id = b.cpt_fee_id
											AND a.fee_table_column_id = '1'
											AND delete_status = '0' order by status asc");
			$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
			/*$cpt_fee = ($price_fields!="" && $getCPTPriceRow['retail_price']!="0.00")?$getCPTPriceRow['retail_price']:$getCPTPriceRow['cpt_fee'];*/
			$cpt_fee = ($price_fields!="" && $getCPTPriceRow['retail_price']!="0.00")?$getCPTPriceRow['retail_price']:'0.00';
			$cpt_fee_id = $getCPTPriceRow['cpt_fee_id'];
			$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
			$cpt_desc=$getCPTPriceRow['cpt_desc'];
		}
		if($cpt_prac_code==""){
			$sql = "SELECT `prac_code`, `retail_price` FROM `in_prac_codes` WHERE `module_id`='2' ";
			$flag = false;
			if($_REQUEST['tb_name']=="in_lens_type"){
				$sql .=" AND `sub_module`='type'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_progressive"){
				$sql .=" AND `sub_module`='progressive'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_material"){
				$sql .=" AND `sub_module`='material'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_transition"){
				$sql .=" AND `sub_module`='transition'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_ar"){
				$sql .=" AND `sub_module`='coating'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_tint"){
				$sql .=" AND `sub_module`='tint'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_polarized"){
				$sql .=" AND `sub_module`='polarized'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_edge"){
				$sql .=" AND `sub_module`='edge'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_color"){
				$sql .=" AND `sub_module`='type'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_lens_design"){
				$sql .=" AND `sub_module`='design'";
				$flag = true;
			}
			/*elseif($_REQUEST['tb_name']=="in_lens_color"){
				$sql .=" AND `sub_module`='type'";
				$flag = true;
			}*/
			elseif($_REQUEST['tb_name']=="in_lens_color"){
				$sql .=" AND `sub_module`='color'";
				$flag = true;
			}
			elseif($_REQUEST['tb_name']=="in_item_price_details" && $_REQUEST['for']=="uv"){
				$sql .=" AND `sub_module`='uv400'";
				$flag = true;
			}
			
			if($flag){
				$resp = imw_query($sql);
				if($resp && imw_num_rows($resp)>0){
					$resp = imw_fetch_assoc($resp);
					$cpt_fee = $resp['retail_price'];
					$cpt_prac_code = $resp['prac_code'];
					$cpt_desc = $resp['prac_code'];
				}
			}
		}
		array_push($returnVals, $cpt_prac_code."~~~".$cpt_fee."~~~".$cpt_fee_id."~~~".$cpt_desc);
	}
	print json_encode($returnVals);
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="get_price_from_otherrow")
{

	$getCPTPriceQry = imw_query("SELECT lt.other_retail, b.cpt_fee_id, b.cpt_desc, b.cpt_prac_code, a.cpt_fee FROM `cpt_fee_table` as a inner join ".$_REQUEST['tb_name']." as lt on lt.item_id='".$_REQUEST['sel_id']."' left join cpt_fee_tbl as b on b.cpt_fee_id = lt.other_prac_code
										WHERE 
										a.cpt_fee_id = lt.other_prac_code
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
	$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
	$cpt_fee = $getCPTPriceRow['cpt_fee'];
	$other_retail = $getCPTPriceRow['other_retail'];
	$cpt_fee_id = $getCPTPriceRow['cpt_fee_id'];
	$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
	$cpt_desc=$getCPTPriceRow['cpt_desc'];
	if(imw_num_rows($getCPTPriceQry)==0){
		$getCPTPriceQry = imw_query("SELECT lt.other_retail, b.cpt_fee_id, b.cpt_desc, b.cpt_prac_code, a.cpt_fee FROM `cpt_fee_table` as a inner join ".$_REQUEST['tb_name']." as lt on lt.item_id='".$_REQUEST['sel_id']."' left join cpt_fee_tbl as b on ( b.cpt4_code = lt.other_prac_code OR b.cpt_desc = lt.other_prac_code ) 
										WHERE 
										a.cpt_fee_id = lt.other_prac_code
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		$cpt_fee = $getCPTPriceRow['cpt_fee'];
		$other_retail = $getCPTPriceRow['other_retail'];
		$cpt_fee_id = $getCPTPriceRow['cpt_fee_id'];
		$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
		$cpt_desc=$getCPTPriceRow['cpt_desc'];
	}
	echo $cpt_prac_code."-:".$other_retail."-:".$cpt_fee_id."-:".$cpt_desc;
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="PlaceNewOrder")
{
	unset($_SESSION['order_id']);
	echo "session unset";
}

if($_REQUEST['action']!="" && $_REQUEST['action']=="dx_code_id")
{
	$dx_code = str_replace("~~~",";",$_REQUEST['dx_code']);
	$getdxCode=imw_query("SELECT * FROM icd10_data WHERE icd9='".$dx_code."' OR icd10='".$dx_code."' OR icd10_desc='".$dx_code."'");
	$get_row=imw_fetch_array($getdxCode);
	echo $dx_code=$get_row['icd10'];
}

if($_REQUEST['action']=="get_lens_types" && $_REQUEST['ids']!=""){
	$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' and module_id='0' AND `del_status`='0' AND `id` IN(".$_REQUEST['ids'].")";
	$selected = (isset($_REQUEST['selectedId']))?$_REQUEST['selectedId']:0;
	$resp = imw_query($sql);
	if($resp && imw_num_rows($resp)>0){
		while($row = imw_fetch_assoc($resp)){
			$sel = "";
			if($selected==$row['id']){$sel='selected="selected"';}
			echo '<option value="'.$row['id'].'" '.$sel.'>'.$row['opt_val'].'</option>';
		}
	}
	elseif($resp){
		$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' and module_id='0' AND `del_status`='0'";
		$selected = (isset($_REQUEST['selectedId']))?$_REQUEST['selectedId']:0;
		$resp = imw_query($sql);
		while($row = imw_fetch_assoc($resp)){
			$sel = "";
			if($selected==$row['id']){$sel='selected="selected"';}
			echo '<option value="'.$row['id'].'" '.$sel.'>'.$row['opt_val'].'</option>';
		}
	}
}

$dataQuery = array(1=>"framesData", 2=>"lensData", 3=>"contactLensData",5=>"suppliesData",6=>"medicineData",7=>"accessoriesData", '1,2,3,4,5,6,7'=>"mixedData");

if(in_array($_REQUEST['type'], $dataQuery) && $_REQUEST['upcMatch']!="" && $_REQUEST['queryElemType']!=""){
	$upcmatch = $_REQUEST['upcMatch'];
	$moduleId = array_search($_REQUEST['type'], $dataQuery);
	$queryElemType = $_REQUEST['queryElemType'];
	
	$queryCol = ($_REQUEST['queryElemType']=="name")?"`name`":"`upc_code`";
	
	$limit = isset($_REQUEST['maxVals'])?$_REQUEST['maxVals']:10;
	
	$sql = "SELECT `id`, `upc_code`, `name`, `dosage`, `module_type_id` FROM `in_item` WHERE ".$queryCol." LIKE '".$upcmatch."%' AND `module_type_id` IN(".$moduleId.") AND `del_status`=0 LIMIT ".$limit;
	$resp = imw_query($sql);
	$response = array();
	if($resp && imw_num_rows($resp)>0){
		while($row = imw_fetch_assoc($resp)){
			$data = array();
			$data['id'] = $row['id'];
			$data['upc'] = trim($row['upc_code']);
			if($row['module_type_id']=="6"){
				$data['name'] = trim($row['name'])." - ".trim($row['dosage']);
			}
			else{
				$data['name'] = trim($row['name']);
			}
			$response[] = $data;
		}
	}
	if(count($response)==0){
		$response['error']="No Match Found";
	}
	print json_encode($response);
}
elseif($_REQUEST['type']=="medNames" && $_REQUEST['upcMatch']!="" && $_REQUEST['queryElemType']!=""){
	$matchElem = addslashes(trim($_REQUEST['upcMatch']));
	$maxVals = trim($_REQUEST['maxVals']);
	
	$resp = imw_query("SELECT 
	DISTINCT(`name`)
FROM 
	`in_item` 
WHERE 
	module_type_id = 6 
	AND `name` LIKE '".$matchElem."%' 
	AND del_status = 0");
	$response = array();
	if($resp && imw_num_rows($resp)>0){
		while($row = imw_fetch_object($resp)){
			$data = array();
			$data['name'] = trim($row->name);
			$data['id'] = 1;
			array_push($response, $data);
		}
	}
	print json_encode($response);
}
elseif($_REQUEST['type']=="contactLensDataStock" && $_REQUEST['upcMatch']!="" && $_REQUEST['queryElemType']!=""){
	
	$upcmatch = $_REQUEST['upcMatch'];
	$moduleId = 3;
	$queryElemType = $_REQUEST['queryElemType'];
	
	$queryCol = ($_REQUEST['queryElemType']=="name")?"`name`":"`upc_code`";
	
	$manufacturer = isset($_REQUEST['manufacturer_name'])?trim($_REQUEST['manufacturer_name']):"";
	$manuf_qry_idoc = "`manufacturer`!=''";
	$manuf_qry_optical = "";
	if($manufacturer!=""){
		$manuf_qry_idoc = "`manufacturer`='".$manufacturer."'";
		$manuf_qry_optical = "AND `manufacturer_id`='".trim($_REQUEST['manufacturer_id'])."' ";
	}
	
	$limit = isset($_REQUEST['maxVals'])?$_REQUEST['maxVals']:10;
	
	/*Contact Lens From iDoc* /
	$clData = array();
	$idoc_cl_resp = imw_query("SELECT TRIM(`make_id`) AS 'make_id', TRIM(`style`) AS 'style' FROM `contactlensemake` WHERE ".$manuf_qry_idoc." AND `style` LIKE '".$upcmatch."%' AND `del_status`=0 ORDER BY `style` ASC LIMIT ".$limit);
	if($idoc_cl_resp && imw_num_rows($idoc_cl_resp)>0){
		while($idoc_cl = imw_fetch_object($idoc_cl_resp)){
			$data = array();
			$data['id'] = "";
			$data['upc'] = "";
			$data['name'] = $idoc_cl->style;
			$data['make_id'] = $idoc_cl->make_id;
			$clData[$idoc_cl->style] = $data;
		}
	}
	/*end contact lens from iDoc*/
	
	$sql = "SELECT `id`, `upc_code`, `name` FROM `in_item` WHERE ".$queryCol." LIKE '".$upcmatch."%' AND `module_type_id`=".$moduleId." AND `del_status`=0 ".$manuf_qry_optical."ORDER BY `name` ASC LIMIT ".$limit;
	$resp = imw_query($sql);
	$response = array();
	if($resp && imw_num_rows($resp)>0){
		while($row = imw_fetch_assoc($resp)){
			
			$name = trim($row['name']);
			if(isset($idocCl[$name])){
				$data = $idocCl[$name];
			}
			else{$data = array();}
			
			$data['id'] = $row['id'];
			$data['upc'] = trim($row['upc_code']);
			$data['name'] = $name;
			
			$clData[$name] = $data;
		}
	}
	uksort($clData, 'strcasecmp');
	
	foreach($clData as $data){
		$response[] = $data;
	}
	
	$response = array_slice($response, 0, $limit);
	
	if(count($response)==0){
		$response['error']="No Match Found";
	}
	print json_encode($response);
}
else if($_REQUEST['type']=="dxCodes" && $_REQUEST['upcMatch']!="" && $_REQUEST['queryElemType']!="")
{
	$dxmatch = $_REQUEST['upcMatch'];
	$queryElemType = $_REQUEST['queryElemType'];
	
	$limit = isset($_REQUEST['maxVals'])?$_REQUEST['maxVals']:10;
	
	//$sql = "SELECT `diagnosis_id`, `d_prac_code`, `diag_description` FROM `diagnosis_code_tbl` WHERE (`diag_description` LIKE '".$dxmatch."%' OR `d_prac_codE` LIKE '".$dxmatch."%') AND `delete_status`='0' AND `diag_cat_id`!='' LIMIT ".$limit;
	$sql = "SELECT id as `diagnosis_id`, icd10 as `d_prac_code`, icd10_desc as `diag_description` FROM `icd10_data` WHERE (`icd10_desc` LIKE '".$dxmatch."%' OR `icd10` LIKE '".$dxmatch."%') AND `deleted`='0' LIMIT ".$limit;
	$sql = imw_query($sql);
	
	$dx_code_arr=array();
	if($sql && imw_num_rows($sql)>0){
		while($row = imw_fetch_assoc($sql)){
			$dxCodeOpts['id'] = $row['d_prac_code'];
			if(strpos(strtolower($row['d_prac_code']), strtolower($dxmatch)) !== false){
				$dxCodeOpts[$queryElemType] = $row['d_prac_code'];
			}
			elseif(strpos(strtolower($row['diag_description']), strtolower($dxmatch)) !== false){
				$dxCodeOpts[$queryElemType] = $row['diag_description'];
			}
			$dx_code_arr[] = $dxCodeOpts;
		}
	}
	print json_encode($dx_code_arr);
}
else if($_REQUEST['type']=="praCode" && $_REQUEST['upcMatch']!="" && $_REQUEST['queryElemType']!="")
{
	$pracmatch = $_REQUEST['upcMatch'];
	$queryElemType = $_REQUEST['queryElemType'];
	$limit = isset($_REQUEST['maxVals'])?$_REQUEST['maxVals']:10;
	
	$cat_id_imp="";
	$cat_id_arr=array();
	$cat_cond="";
	if($queryElemType=="defaultCodeOC"){
		$sql1 = "select cpt_cat_id from cpt_category_tbl where cpt_category like '%optical%' or cpt_category like '%contact lens%' order by cpt_category ASC";
		$rez1 = imw_query($sql1);	
		while($row1=imw_fetch_array($rez1)){
			$cat_id_arr[]=$row1['cpt_cat_id'];
		}
		if(count($cat_id_arr)>0){
			$cat_id_imp=implode(',',$cat_id_arr);
		}
	}
	else if($queryElemType=="defaultCodeO"){
		$sql1 = "select cpt_cat_id from cpt_category_tbl where cpt_category like '%optical%' order by cpt_category ASC";
		$rez1 = imw_query($sql1);	
		while($row1=imw_fetch_array($rez1)){
			$cat_id_arr[]=$row1['cpt_cat_id'];
		}
		if(count($cat_id_arr)>0){
			$cat_id_imp=implode(',',$cat_id_arr);
		}
	}
	else if($queryElemType=="defaultCodeC"){
		$sql1 = "select cpt_cat_id from cpt_category_tbl where cpt_category like '%contact lens%' order by cpt_category ASC";
		$rez1 = imw_query($sql1);	
		while($row1=imw_fetch_array($rez1)){
			$cat_id_arr[]=$row1['cpt_cat_id'];
		}
		if(count($cat_id_arr)>0){
			$cat_id_imp=implode(',',$cat_id_arr);
		}
	}
	if($queryElemType=="defaultCode"){
		$sql1 = "select cpt_cat_id from cpt_category_tbl order by cpt_category ASC";
		$rez1 = imw_query($sql1);	
		while($row1=imw_fetch_array($rez1)){
			$cat_id_arr[]=$row1['cpt_cat_id'];
		}
		if(count($cat_id_arr)>0){
			$cat_id_imp=implode(',',$cat_id_arr);
		}
	}
	
	if($cat_id_imp!=""){
		$cat_cond=" and cpt_cat_id in($cat_id_imp)";
	}
	$sql = "SELECT `cpt_fee_id`, `cpt_prac_code`, `cpt_desc` FROM `cpt_fee_tbl` WHERE (`cpt_desc` LIKE '".$pracmatch."%' OR `cpt_prac_code` LIKE '".$pracmatch."%') AND `delete_status`='0' AND LOWER(status)='active' AND `cpt_cat_id`!='' $cat_cond LIMIT ".$limit;

	$sql = imw_query($sql);
	$prac_code_arr=array();
	if($sql && imw_num_rows($sql)>0){
		while($row = imw_fetch_assoc($sql)){
			$pracCodeOpts['id'] = $row['cpt_fee_id'];
			if(strpos(strtolower($row['cpt_prac_code']), strtolower($pracmatch)) !== false){
				$pracCodeOpts[$queryElemType] = $row['cpt_prac_code'];
			}
			elseif(strpos(strtolower($row['cpt_desc']), strtolower($pracmatch)) !== false){
				$pracCodeOpts[$queryElemType] = $row['cpt_desc'];
			}
			$prac_code_arr[] = $pracCodeOpts;
		}
	}
	print json_encode($prac_code_arr);
}
elseif($_REQUEST['type']=="get_lens_fields" && $_REQUEST['item_id']!="")
{
	//get prac code arr
	$q1=imw_query("select cpt_fee_id,cpt_prac_code from cpt_fee_tbl order by cpt_fee_id asc");
	while($d1=imw_fetch_object($q1))
	{
		$pracArr[$d1->cpt_fee_id]=$d1->cpt_prac_code;
	}
	
	$q1=imw_query("SELECT * FROM  `in_prac_codes` where module_id=2 order by id asc");
	while($d1=imw_fetch_object($q1))
	{
		$pracArrDef[$d1->sub_module]=$d1->prac_code;
	}
	
	$sql="select * from in_item where id='$_REQUEST[item_id]'";
	$sql = imw_query($sql);
	$prac_code_arr=array();
	if($sql && imw_num_rows($sql)>0){
		//get data over here
		$row=imw_fetch_assoc($sql);
		
		/*Return Lens Detail Columns*/
		$itemTypes = array(
							/*1=>array('db_col'=>'type_id',
									 'key'=>'lens',
									 'val'=>'Seg Type',
									 'db_cols'=>array(	/*Detail cols for details* /
									  				'wholesale'	=>	'lens_wholesale',
													'retail'	=>	'lens_retail',
													'prac_code'	=>	'type_prac_code',
													'def_prac'	=>	'type'
									 			)	
									),*/
							2=>array('db_col'=>'design_id',
									 'key'=>'design',
									 'val'=>'Design',
									 'db_cols'=>array(	/*Detail cols*/
									  				'wholesale'	=>	'design_wholesale',
													'retail'	=>	'design_retail',
													'prac_code'	=>	'design_prac_code',
													'def_prac'	=>	'type'
									 			)
									 ),
							/*3=>array('db_col'=>'progressive_id',
									 'key'=>'progressive',
									 'val'=>'Progressive',
									 'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'progressive_wholesale',
													'retail'	=>	'progressive_retail',
													'prac_code'	=>	'progressive_prac_code',
													'def_prac'	=>	'progressive'
									 			)
									 ),*/
							4=>array('db_col'=>'material_id',
									 'key'=>'material',
									 'val'=>'Material',
									 'db_cols'=>array(	/*Detail cols*/
									  				'wholesale'	=>	'material_wholesale',
													'retail'	=>	'material_retail',
													'prac_code'	=>	'material_prac_code',
													'def_prac'	=>	'material'
									 			)
									 ),
							/*5=>array('db_col'=>'transition_id',
									 'key'=>'transition',
									 'val'=>'Transition',
									 'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'transition_wholesale',
													'retail'	=>	'transition_retail',
													'prac_code'	=>	'transition_prac_code',
													'def_prac'	=>	'transition'
									 			)
									 ),*/
							6=>array('db_col'=>'a_r_id',
									 'key'=>'a_r',
									 'val'=>'Treatment',
									 'db_cols'=>array(	/*Detail cols*/
									  				'wholesale'	=>	'a_r_wholesale',
													'retail'	=>	'a_r_retail',
													'prac_code'	=>	'ar_prac_code',
													'def_prac'	=>	'coating'
									 			)
									 ),
							/*7=>array('db_col'=>'tint_id',
									 'key'=>'tint',
									 'val'=>'Tint',
									 'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'tint_wholesale',
													'retail'	=>	'tint_retail',
													'prac_code'	=>	'tint_prac_code',
													'def_prac'	=>	'tint'
									 			)
									 ),
							8=>array('db_col'=>'polarized_id',
									 'key'=>'polarization',
									 'val'=>'Polarization',
									 'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'polarization_wholesale',
													'retail'	=>	'polarization_retail',
													'prac_code'	=>	'polarized_prac_code',
													'def_prac'	=>	'polarized'
									 			)
									 ),
							9=>array('db_col'=>'edge_id',
									 'key'=>'edge',
									 'val'=>'Edge',
									 'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'edge_wholesale',
													'retail'	=>	'edge_retail',
													'prac_code'	=>	'edge_prac_code',
													'def_prac'	=>	'edge'
									 			)
									 ),
							10=>array('db_col'=>'color',
									 'key'=>'color',
									 'val'=>'Color',
									 'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'color_wholesale',
													'retail'	=>	'color_retail',
													'prac_code'	=>	'color_prac_code',
													'def_prac'	=>	'color'
									 			)
									 ),
							11=>array('db_col'=>'uv_check',
									  'key'=>'uv400',
									  'val'=>'UV400',
									  'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'uv400_wholesale',
													'retail'	=>	'uv400_retail',
													'prac_code'	=>	'uv_prac_code',
													'def_prac'	=>	''
									 			)
									  ),
							12=>array('db_col'=>'pgx_check',
									  'key'=>'pgx',
									  'val'=>'PGX',
									  'db_cols'=>array(	/*Detail cols* /
									  				'wholesale'	=>	'pgx_wholesale',
													'retail'	=>	'pgx_retail',
													'prac_code'	=>	'pgx_prac_code',
													'def_prac'	=>	''
									 			)
									  )*/
						);
		
		$multi_vlas = array('a_r'); /*Lens Attributes To be added as Procedure for each Selected value*/
		
		$query="select * from in_item_price_details where item_id='$row[id]'";
		$query=imw_query($query);
		if($query && imw_num_rows($query)>0)
		{
			$row1=imw_fetch_assoc($query);
			
			foreach($itemTypes as $key=>$itemType){ /*Match for Each detail Row to be returned*/
				
				if($row[$itemType['db_col']]>0){
					$wholesale = $itemType['db_cols']['wholesale'];
					$retail = $itemType['db_cols']['retail'];
					$prac_code = $itemType['db_cols']['prac_code'];
					$def_prac = $itemType['db_cols']['def_prac'];
					
					$detail_val = "";
					//if(in_array($itemType['key'], $multi_vlas)){
						$sql = "SELECT `".$itemType['db_col']."` AS 'item_value'  FROM `in_item` WHERE `id`='".$row['id']."'";
						$resp = imw_query($sql);
						if($resp && imw_num_rows($resp)){
							$resp_dt = imw_fetch_assoc($resp);
							$detail_val = $resp_dt['item_value'];
						}
					//}
					
					$prac_code_arr[$itemType['key']]['name'] = $itemType['val'];	/*Item Name in POS*/
					$prac_code_arr[$itemType['key']]['wholesale'] = ($row1[$wholesale]>=1)?$row1[$wholesale]:0;
					$prac_code_arr[$itemType['key']]['retail'] = ($row1[$retail]>=1)?$row1[$retail]:0;
					$prac_code_arr[$itemType['key']]['detail_id'] = $detail_val;
					
					/*Add Seg type value in Design's Retail Price*/
					if( $itemType['db_col'] == 'design_id' && $row['type_id'] > 0 ){
						$segTypeRetail = ($row1['lens_retail']>=1)?$row1['lens_retail']:0;
						$prac_code_arr[$itemType['key']]['retail'] = number_format($prac_code_arr[$itemType['key']]['retail'] + $segTypeRetail, 2);
					}
					
					/*Multiple POS rows for Option*/
					$rtPrac = array();
					$pracCode = explode(";",$row1[$prac_code]);
					foreach($pracCode as $code){
						if($pracArr[$code]){
							array_push($rtPrac, $pracArr[$code]);
						}
						elseif($pracArrDef[$def_prac]){
							array_push($rtPrac, $pracArrDef[$def_prac]);
						}
						else{
							array_push($rtPrac, '');
						}
						
					}
					
					if($itemType['key']=="material" && count($pracCode)>0){
						/*Material Names*/
						$mat_names = array();
						$mat_ids = array();
						$m_resp1 = imw_query("SELECT `id`, `material_name`, `vw_code` FROM `in_lens_material` WHERE `id`='".$row['material_id']."'");
						$mat_names = array();
						if($m_resp1 && imw_num_rows($m_resp1)>0){
							$m_resp_row = imw_fetch_assoc($m_resp1);
							array_push($mat_names, $m_resp_row['material_name']);
							array_push($mat_ids, $m_resp_row['id']);
							$sub_vw_code = explode("-",$m_resp_row['vw_code']);
							if(count($sub_vw_code)>0){
								$main_vw_code = $sub_vw_code[0]."-".$sub_vw_code[1]."-NONE-NONE-00";
								
								/*Query for Main Material Name*/
								$m_resp2 = imw_query("SELECT `id`, `material_name` FROM `in_lens_material` WHERE `vw_code`='".$main_vw_code."' LIMIT 1");
								if($m_resp2 && imw_num_rows($m_resp2)>0){
									$m_vw_code = imw_fetch_assoc($m_resp2);
									
									$m_vw_code1 = explode(" ", $m_vw_code['material_name']);
									$mat_names[0] = str_replace($m_vw_code1, "", $mat_names[0]);
									
									array_push($mat_names, $m_vw_code['material_name']);
									array_push($mat_ids, $m_vw_code['id']);
								}
							}
						}
						$mat_names = array_map('trim', $mat_names);
						$mat_ids = array_map('trim', $mat_ids);
						krsort($mat_names);
						krsort($mat_ids);
						
						$prac_code_arr[$itemType['key']]['details'] = implode(";",$mat_names);
						$prac_code_arr[$itemType['key']]['ids'] = implode(";",$mat_ids);
					}
					$prac_code_arr[$itemType['key']]['prac_code'] = implode(";",$rtPrac);
				}
			}
		}
		
	}
	print json_encode($prac_code_arr);
}
else if($_REQUEST['type']=="physicianData" && $_REQUEST['upcMatch']!="" && $_REQUEST['queryElemType']!=""){
	
	/*Get user types for providers*/
	$match = $_REQUEST['upcMatch'];
	$limit = $_REQUEST['maxVals'];
	$phyTypes = array();
	$types = imw_query('SELECT `user_type_id` FROM `user_type` WHERE user_type_name IN("Physician","Attending Physician","Physician Assistant")');
	if($types && imw_num_rows($types)>0){
		while($row = imw_fetch_assoc($types)){
			$phyTypes[] = $row['user_type_id'];
		}
	}
	$physicians = array();
	if(count($phyTypes)>0){
		$phyTypes = implode(", ", $phyTypes);
		$sql = 'SELECT `id`, IF(`mname`<>"", CONCAT(CONCAT(UCASE(LEFT(`lname`, 1)), SUBSTRING(`lname`, 2)), " ", CONCAT(UCASE(LEFT(`mname`, 1)), SUBSTRING(`mname`, 2)), ", ", CONCAT(UCASE(LEFT(`fname`, 1)), SUBSTRING(`fname`, 2))), CONCAT(CONCAT(UCASE(LEFT(`lname`, 1)), SUBSTRING(`lname`, 2)), ", ", CONCAT(UCASE(LEFT(`fname`, 1)), SUBSTRING(`fname`, 2)))) AS "phy_name" FROM `users` WHERE `user_type` IN ('.$phyTypes .') AND `delete_status`="0" AND (`lname` LIKE "'.$match.'%" OR `fname` LIKE "'.$match.'%") ORDER BY `lname` ASC LIMIT '.$limit;
		
		$sql = imw_query($sql);
		if($sql && imw_num_rows($sql)>0){
			$i = 0;
			while($row = imw_fetch_assoc($sql)){
				$physicians[$i]['id'] = $row['id'];
				$physicians[$i]['name'] = $row['phy_name'];
				$i++;
			}
		}
	}
	print json_encode($physicians);
}
else if($_REQUEST['type']=="cancelOrderItem"){
	
	require_once("../../library/classes/functions.php");
	
	$order_detail_id = trim($_REQUEST['itemId']);
	$order_id = $_SESSION['order_id'];
	
	cancel_item($order_detail_id, $order_id);
}
else if($_REQUEST['action']!="" && $_REQUEST['action']=="getMdDetails" && trim($_REQUEST['item_name'])!=""){
	require_once("../../library/classes/functions.php");
	
	$response = array();
	$med_name = trim($_REQUEST['item_name']);
	/*Detail commin for all options*/
	$detail = imw_query("SELECT 
							`name`, 
							`manufacturer_id`, 
							`vendor_id`, 
							`med_typ`, 
							`pay_by`, 
							`ndc`, 
							`fee`, 
							`discount`, 
							`discount_till`, 
							`harcardous`, 
							`type_desc` 
						FROM 
							`in_item` 
						WHERE 
							`module_type_id` = '6' 
							AND `name` = '".$med_name."' 
							AND `del_status` = 0 
						LIMIT 
							1");
	if($detail && imw_num_rows($detail)>0){
		$response = imw_fetch_assoc($detail);
		$response['discount_till'] = getDateFormat($response['discount_till']);
		
		/*Retail Price Calculation*/
		$response['formula'] = trim($response['formula']);
		$response['formula_save'] = $response['formula'];	/*Value for hidden formula field - edited for the item*/
		
		if( $response['formula'] == '' ){
			$response['formula'] = get_retail_formula(6, array('manufacturer_id'=>$response['manufacturer_id'], 'vendor_id'=>$response['vendor_id']));
		}
	/*End Retail Price Calculation*/
		
		$details_arr = array();
		/*Different Med Options*/
			$details = imw_query("SELECT 
			`id`, 
			`upc_code` AS 'upc', 
			`item_prac_code` AS 'prac', 
			`dx_code` AS 'dx', 
			`dosage`, 
			`units`, 
			`threshold`, 
			`retail_price` AS 'retail', 
			`wholesale_cost` AS 'whole', 
			`purchase_price` AS 'purch', 
			`qty_on_hand` AS 'qty', 
			`amount`, 
			`expiry_date`,
			`retail_price_flag`
		FROM 
			`in_item` 
		WHERE 
			`module_type_id` = '6' 
			AND `name` = '".$med_name."' 
			AND `del_status` = 0");
			if($details && imw_num_rows($details)>0){
				while($row = imw_fetch_assoc($details)){
					$row['expiry_date'] = getDateFormat($row['expiry_date']);
					$details_arr[] = $row;
				}
			}
		$response['details'] = $details_arr;
		/*Different Med Options*/
	}
	
	print json_encode($response);
}
/*Get Frame Color's DropDown*/
elseif( $_REQUEST['type']=='frameColors' && $_REQUEST['upcMatch']!='' ){
	
	$list = array();
	
	$elemName = $_REQUEST['queryElemType'];
	$qryLimit = (int)$_REQUEST['maxVals'];
	
	$upcMatch = $_REQUEST['upcMatch'];
	$upcMatch	= 'SELECT `color_name` AS \''.$elemName.'\', `color_code` AS \'id\' FROM `in_frame_color` WHERE `color_name` LIKE \''.$upcMatch.'%\' AND `del_status`=0 LIMIT '.$qryLimit;
	$upcResp	= imw_query($upcMatch);
	
	while( $row = imw_fetch_assoc($upcResp) ){
		array_push($list, $row);
	}
	
	print json_encode($list);
}
elseif( $_REQUEST['type']=='frameColorsSearch' && $_REQUEST['upcMatch']!='' ){
	
	$list = array();
	
	$elemName = $_REQUEST['queryElemType'];
	$qryLimit = (int)$_REQUEST['maxVals'];
	
	$upcMatch = $_REQUEST['upcMatch'];
	$upcMatch	= 'SELECT `color_name` AS \''.$elemName.'\', id FROM `in_frame_color` WHERE `color_name` LIKE \''.$upcMatch.'%\' AND `del_status`=0 LIMIT '.$qryLimit;
	$upcResp	= imw_query($upcMatch);
	
	while( $row = imw_fetch_assoc($upcResp) ){
		array_push($list, $row);
	}
	
	print json_encode($list);
}

/*Mark Incorrect Rx. (Custom)*/
if($_REQUEST['type']=="markIncorrectRx"){
	
	$operator_id=$_SESSION['authId'];
	$date=date('Y-m-d');
	$time=date('H:i:s');
	
	$rxId = $_REQUEST['rxId'];
	if($rxId!=""){
		$sql = "UPDATE `in_optical_order_form`
				SET
					`incorrect_rx_status`='1',
					`incorrect_rx_operator_id`='".$operator_id."',
					`incorrect_rx_date`='".$date."',
					`incorrect_rx_time`='".$time."'
				WHERE
					`id`='".$rxId."'";
		imw_query($sql);
	}
}

/*Vision Web Dropdown for Lens*/
if(isset($_POST['action']) && $_POST['action']=="getVisionDD"){
	
	//$elem_types = array("seg_type"=>"seg_type", "design"=>"design", "material"=>"material", "treatment"=>"treatment");
	$elem_types = array("design"=>"design", "material"=>"material", "treatment"=>"treatment");
	
	$id = $_POST['value'];
	$element = $_POST['element'];
	//unset($elem_types[$element]);	/*Do Not Return Data for element that triggered the action*/
	
	$sql = false;
	$rq_prInterface = (isset($_REQUEST['ptInterface']))?true:false;
	
	$vw_code = array();
	/*Fetch data to query other elements on the basis of triggering element*/
		if($element=="seg_type" && $_POST['segTypeVal']!='0' && $_POST['segTypeVal']!=''){
			$sql1 = imw_query("SELECT `vw_code` FROM `in_lens_type` WHERE `id`='".$_POST['segTypeVal']."'");
			$row = imw_fetch_assoc($sql1);
			array_push($vw_code, "'".$row['vw_code']."'");
		}
		elseif($element=="design"){
			$elem_types = array("material"=>"material", "treatment"=>"treatment");
		}
		elseif($element=="material"){
			$elem_types = array("treatment"=>"treatment");
		}
		elseif($element=="treatment"){
			$elem_types = array();
		}
	/*End fetch data to query other elements on the basis of triggering element*/
	
	if($element=="seg_type" && count($vw_code)==0){
		
		$returnValues = array('design'=>array(), 'material'=>array(), 'treatment'=>array());
	}
	
	foreach($elem_types as $elem){
		
		/*Fetch Design Values*/
		if($elem=='design' && count($vw_code)>0){
			$returnValues['design'] = array();
			$design_fetch = imw_query("SELECT 
											`id`, 
											CONCAT(UCASE(LEFT(`design_name` , 1)), SUBSTRING(`design_name` , 2)) AS design_name
										FROM 
											`in_lens_design` 
										WHERE 
											`lens_vw_code` IN(".implode(",", $vw_code).")
											AND `del_status`=0");
			while($row = imw_fetch_object($design_fetch)){
				$returnValues['design'][$row->design_name] = $row->id;
			}
			if(isset($returnValues['design'])){
				ksort($returnValues['design']);
				$returnValues['design'] = $returnValues['design'];
			}
			imw_free_result($design_fetch);
		}		
		/*Fetch material values*/
		elseif($elem=="material"){
			
			$returnValues['material'] = array();
			if($element == "seg_type" && count($vw_code)>0){
				$material_fetch = imw_query("SELECT 
													DISTINCT(`lm`.`id`), 
													CONCAT(UCASE(LEFT(`lm`.`material_name` , 1)), SUBSTRING(`lm`.`material_name` , 2)) AS 'material_name' 
												FROM 
													`in_lens_design` `ld` 
													LEFT JOIN `in_lens_material_design` `dm` ON(`ld`.`id` = `dm`.`design_id`) 
													LEFT JOIN `in_lens_material` `lm` ON(`dm`.`material_id` = `lm`.`id`) 
												WHERE 
													`ld`.`lens_vw_code` IN(".implode(",",$vw_code).") 
													AND `lm`.`del_status` = 0");
			}
			elseif($element == "design"){
				$material_fetch = imw_query("SELECT 
													DISTINCT(`lm`.`id`), 
													CONCAT(UCASE(LEFT(`lm`.`material_name` , 1)), SUBSTRING(`lm`.`material_name` , 2)) AS 'material_name' 
												FROM 
													`in_lens_material_design` `dm` 
													INNER JOIN `in_lens_material` `lm` ON(`dm`.`material_id` = `lm`.`id`)
													WHERE `dm`.`design_id`='".$_REQUEST['designVal']."'
													AND `lm`.`del_status`=0");
			}
			while($row = imw_fetch_object($material_fetch)){
				$returnValues['material'][$row->material_name] = $row->id;
			}
			if(isset($returnValues['material'])){
				ksort($returnValues['material']);
				$returnValues['material'] = $returnValues['material'];
			}
			imw_free_result($material_fetch);
		}
		/*Fetch treatment values*/
		elseif($elem=="treatment"){
			
			$returnValues['treatment'] = array();
			if($element == "seg_type" && count($vw_code)>0){
				$vw_code_qry = (count($vw_code)==1)? "`ld`.`lens_vw_code`=".trim($vw_code[0]):"`ld`.`lens_vw_code` IN(".implode(",",$vw_code).")";
				/*Fetch List of Treatments connected to the selected material>design>seg_type*/
				
					/*List all materials ids*/
						$material_ids = array();
						$material_qry = imw_query("SELECT 
															DISTINCT(`dm`.`material_id`) AS `material_id` 
														FROM 
															`in_lens_design` `ld` 
															INNER JOIN `in_lens_material_design` `dm` ON(
																".$vw_code_qry." 
																AND `ld`.`id` = `dm`.`design_id`
															)");
						while($row_t = imw_fetch_object($material_qry)){
							$material_ids[$row_t->material_id] = true;
						}
						imw_free_result($material_qry);
					/*End material list ids*/
				
					/*List Treatment ids*/
						$treatment_ids = array();
						$treatment_qry = imw_query("SELECT 
															DISTINCT(`material_id`) AS `material_id`, 
															`ar_id` 
														FROM 
															`in_lens_ar_material`");
						while($row_t = imw_fetch_object($treatment_qry)){
							if($material_ids[$row_t->material_id]){
								$treatment_ids[$row_t->ar_id] = true;
							}
						}
						imw_free_result($treatment_qry);
						unset($material_ids);
					/*End list Treatment ids*/
				
					if(count($treatment_ids)>0){
						
						/*List all Treatment Values*/
						$treatments_qry = imw_query("SELECT 
															`id`, 
															CONCAT(UCASE(LEFT(`ar_name` , 1)), SUBSTRING(`ar_name` , 2)) AS 'ar_name'
														FROM 
															`in_lens_ar`
															WHERE `del_status`=0");
						while($row_t = imw_fetch_object($treatments_qry)){
							if($treatment_ids[$row_t->id]){
								$returnValues['treatment'][$row_t->ar_name] = $row_t->id;
							}
						}
						imw_free_result($treatments_qry);
						unset($treatment_ids);
						/*End list all Treatment Values*/
					}
				/*End List of Treatments connected to the selected material>design>seg_type*/
			}
			elseif($element == "design"){
				$treatment_fetch = imw_query("SELECT 
													DISTINCT(`ar`.`id`), 
													CONCAT(UCASE(LEFT(`ar`.`ar_name` , 1)), SUBSTRING(`ar`.`ar_name` , 2)) AS 'ar_name'
												FROM 
													`in_lens_material_design` `dm` 
													LEFT JOIN `in_lens_ar_material` `arm` ON(
														`dm`.`material_id` = `arm`.`material_id`
													) 
													LEFT JOIN `in_lens_ar` `ar` ON(`arm`.`ar_id` = `ar`.`id`) 
												WHERE 
													`dm`.`design_id` = '".$_REQUEST['designVal']."'
													AND `ar`.`del_status`=0");
				while($row = imw_fetch_object($treatment_fetch)){
					$returnValues['treatment'][$row->ar_name] = $row->id;
				}
				imw_free_result($treatment_fetch);
			}
			elseif($element == "material"){
				$treatment_fetch = imw_query("SELECT 
													DISTINCT(`ar`.`id`), 
													CONCAT(UCASE(LEFT(`ar`.`ar_name` , 1)), SUBSTRING(`ar`.`ar_name` , 2)) AS 'ar_name'
												FROM 
													`in_lens_ar_material` `arm` 
													LEFT JOIN `in_lens_ar` `ar` ON(`arm`.`ar_id` = `ar`.`id`) 
												WHERE 
													`arm`.`material_id` = '".$_REQUEST['materialVal']."'
													AND `ar`.`del_status`=0");
				while($row = imw_fetch_object($treatment_fetch)){
					$returnValues['treatment'][$row->ar_name] = $row->id;
				}
				imw_free_result($treatment_fetch);
			}
			
			if(isset($returnValues['treatment'])){
				ksort($returnValues['treatment']);
				$returnValues['treatment'] = $returnValues['treatment'];
			}
		}
	}
	
	$trimVals = array_fill(0, count($vw_code), "'");
	
	$returnValues['seg_type'] = array_map('custom_trim', $vw_code, $trimVals);
	print json_encode($returnValues);
}
/*Custom Trim Function*/
function custom_trim(&$val, $trimVal){
	return(trim($val, $trimVal));
}

/*Contact List Vendor by Manufacturer*/
if(isset($_POST['action']) && $_POST['action']=="vendorListCL"){
	$manuf_id = (int)$_POST['manuf'];
	
	$vendor_sql = imw_query("SELECT 
									DISTINCT(`v`.`id`) AS `id`, 
									CONCAT(UCASE(LEFT(`v`.`vendor_name` , 1)), SUBSTRING(`v`.`vendor_name` , 2)) AS 'vendor_name' 
								FROM 
									`in_vendor_manufacture` `vm` 
									INNER JOIN `in_vendor_details` `v` ON(`vm`.`vendor_id` = `v`.`id`) 
								WHERE 
									`vm`.`manufacture_id` = ".$manuf_id." 
									AND `v`.`del_status` = 0 
								ORDER BY 
									`v`.`vendor_name` ASC");
	
	$vendors = array();
	if($vendor_sql && imw_num_rows($vendor_sql)>0){
		while($row = imw_fetch_object($vendor_sql)){
			$vendors[$row->vendor_name] = $row->id;
		}
		ksort($vendors);
	}
	print json_encode($vendors);
}

/*Get Price for the Item by UPC Code and module type*/
if(isset($_POST['action']) && $_POST['action']=="upcPrice"){
	
	$module	= trim($_POST['module']);	/*Module type Id*/
	$upc	= trim($_POST['upc_code']);
	$item_id= trim($_POST['item_id']);
	
	$response = array();
	if($module !='' && $upc!='' && $item_id!=''){
		
		$price_sql = "SELECT `retail_price`, `discount`, `discount_till` FROM `in_item` WHERE `upc_code`='".$upc."' AND `id`=".$item_id." AND `module_type_id`=".$module;
		
		$price_resp = imw_query($price_sql);
		if($price_resp && imw_num_rows($price_resp)){
			$price_resp			= imw_fetch_object($price_resp);
			$response['price']	= $price_resp->retail_price;
			
			$discount		= trim($price_resp->discount);
			$discount_till	= str_replace('-', '', $price_resp->discount_till);
			$current_date	= date("Ymd");	
			
			$response['discount'] = '0';
			if($discount_till == '00000000' || $discount_till >= $current_date && $discount != '')
				$response['discount'] = ($discount != '') ? $discount : '0';
		}
	}
	print json_encode($response);
}

if($_REQUEST['type']=="refPhysicianName"){
	
	$match = $_REQUEST['upcMatch'];
	
	$phySql = 'SELECT physician_Reffer_id AS id, CONCAT_WS(\', \', `LastName`, `FirstName`) AS \'name\'
			FROM `refferphysician`
			WHERE
				`delete_status` = 0 AND
				(`LastName` LIKE \''.$match.'%\' || `FirstName` LIKE \''.$match.'%\')
				ORDER BY `LastName`,`FirstName` ASC
				LIMIT 0,10';
	$phyResp = imw_query( $phySql );
	
	$response = array();
	if( $phyResp && imw_num_rows($phyResp) > 0 ){
		while( $phyRow = imw_fetch_object($phyResp) ){
			$data[] = array();
			$data['id'] = $phyRow->id;
			$data['phyName'] = $phyRow->name;
			array_push($response, $data);
		}
	}
	
	if(count($response)==0){
		$response['error']="No Match Found";
	}
	print json_encode($response);
}

if($_REQUEST['type']=="physicianName"){
	
	$match = $_REQUEST['upcMatch'];
	
	$phySql = 'SELECT `id`, CONCAT_WS(\', \', `lname`, `fname`) AS \'name\'
			FROM `users`
			WHERE
				`user_type` = 1 AND
				`delete_status` = 0 AND
				(`lname` LIKE \''.$match.'%\' || `fname` LIKE \''.$match.'%\')
				ORDER BY `lname`,`fname` ASC';
	$phyResp = imw_query( $phySql );
	
	$response = array();
	if( $phyResp && imw_num_rows($phyResp) > 0 ){
		while( $phyRow = imw_fetch_object($phyResp) ){
			$data[] = array();
			$data['id'] = $phyRow->id;
			$data['phyName'] = $phyRow->name;
			array_push($response, $data);
		}
	}
	
	if(count($response)==0){
		$response['error']="No Match Found";
	}
	print json_encode($response);
}

if( isset($_REQUEST['type']) && $_REQUEST['type']=='uploadTraceFile' ){
	
	$order_id		= $_POST['order_id'];
	$order_detail_id= $_POST['order_dtail_id'];
	
	$response = array('status'=>0);
	
	if($_FILES['traceFile']['name']!="" && $order_detail_id!='' && $order_id!=''){
		
		$trace_type = pathinfo ($_FILES['traceFile']['name'],PATHINFO_EXTENSION);
		$target	= 'trace_file_'.$order_detail_id.'.'.$trace_type;
		
		$path = $GLOBALS['DIR_PATH'].'/interface/patient_interface/uploaddir/trace_file/';
		move_uploaded_file($_FILES['traceFile']['tmp_name'], $path.$target);
		
		imw_query("UPDATE `in_order_details` SET `trace_file`='".$target."' WHERE `id`='".$order_detail_id."'");
		$response['status']	= 1;
		
		$fileName = $target;
		if( strlen($fileName) > 18 ){
			$fileName = substr($fileName, 0, 18).'..';
		}
		
		$response['file']	= $fileName;
		$response['title']	= $target;
	}
	echo json_encode($response);
}

if($_REQUEST['type']!="" && $_REQUEST['type']=="framesStyle")
{
	$list = array();
	$elemName = $_REQUEST['queryElemType'];
	$qryLimit = (int)$_REQUEST['maxVals'];
	$upcMatch = $_REQUEST['upcMatch'];
	$othersVals=$_REQUEST['extraParams'];
		
	if($othersVals['brand']!=0 && $othersVals['brand']!="")
	{
		$sty_whr = " and bs.brand_id = '".$othersVals['brand']."'";
		
		$upcMatch = "SELECT fs.style_name as $elemName, fs.id from in_style_brand as bs
		INNER JOIN in_frame_styles as fs on fs.id=bs.style_id 
		WHERE fs.del_status = '0' 
		and `style_name` LIKE '$upcMatch%' $sty_whr order by fs.style_name asc LIMIT ".$qryLimit;
	}else{
		$upcMatch	= "SELECT style_name as $elemName, id from in_frame_styles 
		WHERE `style_name` LIKE '$upcMatch%' 
		AND `del_status`=0 
		ORDER BY style_name ASC
		LIMIT $qryLimit";
	}
	$upcResp	= imw_query($upcMatch);
	
	while( $row = imw_fetch_assoc($upcResp) ){
		array_push($list, $row);
	}
	
	print json_encode($list);
}
?>