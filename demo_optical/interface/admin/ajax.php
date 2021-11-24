<?php
/*
File: ajax.php
Coded in PHP7
Purpose: Get Data from UPC code, Get Frame Brand, Style, Color, Manufacutre, Vendor, Get Price and City/State
Access Type: Include File
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php");
if($_REQUEST['action']!="" && $_REQUEST['action']=="managestock")
{
	$qry = imw_query("select in_item.*, DATE_FORMAT(in_item.discount_till,'%m-%d-%Y') as discount_till, DATE_FORMAT(in_item.expiry_date,'%m-%d-%Y') as expiry_date from in_item where id = '".trim($_REQUEST['upc'])."' ");
	$returnArr = array();
	while($row = imw_fetch_array($qry))
	{
		//if($row['wholesale_cost']<1 || $row['purchase_price']<1){
			$qLot=imw_query("select wholesale_price,purchase_price from in_item_lot_total where item_id='$row[id]' order by id desc limit 0,1");
			$dLot=imw_fetch_assoc($qLot);
			$update_str="";
			if($row['wholesale_cost']!=$dLot['wholesale_price'])
			{
				$row['wholesale_cost']=$dLot['wholesale_price'];
				$update_str.=",wholesale_cost='$dLot[wholesale_price]'";
			}
			if($row['purchase_price']!=$dLot['purchase_price']){
				$row['purchase_price']=$dLot['purchase_price'];
				$update_str.=",purchase_price='$dLot[purchase_price]'";
			}
			if($update_str)
			{	
				$update_str=substr($update_str,1,strlen($update_str));
				imw_query("update in_item set $update_str where id = '".$row[id]."' ");
			}
		//}
		/*Stock Image for Frame*/
		if( $row['module_type_id']=='1' ){
			
			$stock_image_l	= 'no_image_xl.jpg';
			$image_base_path = $GLOBALS['DIR_PATH'].'/images/frame_stock';
			
			if( trim($row['stock_image'])!='' ){
				
				$item_image_name= trim($row['stock_image']);
				$stock_image_l	= 'xl/'.$item_image_name;
				
				/*Use Default Image if no stock Image exists*/
				if( !file_exists($image_base_path.'/'.$stock_image_l) ){
					$stock_image_l = 'no_image_xl.jpg';
				}
			}
			$row['stock_image'] = $stock_image_l;
			
			if($row['frame_style']!='0'){
				$sqlItemColor = 'SELECT `style_name` FROM `in_frame_styles` WHERE `id`=\''.$row['frame_style'].'\'';
				$sqlItemColor = imw_query($sqlItemColor);
				if($sqlItemColor && imw_num_rows($sqlItemColor)){
					$sqlItemColor = imw_fetch_assoc($sqlItemColor);
					$row['frame_style_name'] = $sqlItemColor['style_name'];
				}
			}
			
			if($row['color']!='0'){
				$sqlItemColor = 'SELECT `color_name` FROM `in_frame_color` WHERE `id`=\''.$row['color'].'\'';
				$sqlItemColor = imw_query($sqlItemColor);
				if($sqlItemColor && imw_num_rows($sqlItemColor)){
					$sqlItemColor = imw_fetch_assoc($sqlItemColor);
					$row['color_name'] = $sqlItemColor['color_name'];
				}
			}
			
		}
		
	/*Retail Price Calculation*/
		$retail_price_markup_modules = array(1, 3, 5, 6);	/*List of module type id's for which retail price markup functionality is given*/
		
		$row['formula'] = trim($row['formula']);
		$row['formula_save'] = $row['formula'];	/*Value for hidden formula field - edited for the item*/
		
		if( in_array($row['module_type_id'], $retail_price_markup_modules) ){
			
			if( $row['formula'] == '' ){
				if( $row['module_type_id']=='1' ){
					$row['formula'] = get_retail_formula($row['module_type_id'], array('manufacturer_id'=>$row['manufacturer_id'], 'brand_id'=>$row['brand_id'], 'frame_style'=>$row['frame_style']));
				}
				else{
					$row['formula'] = get_retail_formula($row['module_type_id'], array('manufacturer_id'=>$row['manufacturer_id'], 'brand_id'=>$row['brand_id']));
				}
			}
			/*Final Retail Price for the Item - based on formula calculation*/
			if( $row['formula']!='' && $row['retail_price'] <= 0 ){
				$row['retail_price']= calculate_markup_price($row['formula'], $row['wholesale_cost'], $row['purchase_price']);
				$row['amount']		=$row['retail_price']*$row['qty_on_hand'];
				$row['retail_price']= number_format((float)$row['retail_price'], 2);
			}
			/*End Final Retail Price for the Item*/
		}
	/*End Retail Price Calculation*/
	
		
		$returnArr[] = $row;
	}	
	echo json_encode($returnArr);
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="managestock_idoc_cl")
{
	$id = trim($_REQUEST['id']);
	$qry = imw_query("select `manufacturer`, `style`, `type`, `cpt_fee_id`, `price`, `base_curve`, `diameter` from contactlensemake WHERE make_id = '".trim($id)."' LIMIT 1");
	$returnArr = array();
	$nums = imw_num_rows($qry);
	if($nums > 0)
	{
		while($row = imw_fetch_assoc($qry))
		{
			$row['idcoCL'] = true;
			$returnArr[] = $row;
		}	
		echo json_encode($returnArr);
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_brand")
{
	$return_json= ( isset($_REQUEST['return_json']) ) ? true : false;
	$reurn_data	= array();
	
	if($_REQUEST['mid']!="" && $_REQUEST['mid']!=0)
	{
		$sql = "select fs.frame_source,bm.brand_id, bm.manufacture_id from in_brand_manufacture as bm
	 inner join in_frame_sources as fs on fs.id=bm.brand_id 
	 where bm.manufacture_id = '".$_REQUEST['mid']."' and fs.del_status = '0' order by fs.frame_source asc";
	 }
	 else
	 {
	 	$sql = "select frame_source, id as brand_id from in_frame_sources where del_status='0' order by frame_source asc";
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
			
			if(!$return_json){
				echo "<option ".$sel." value='".$rows['brand_id']."'>".$rows['frame_source']."</option>";
			}
			else{
				$name = html_entity_decode($rows['frame_source']);
				$reurn_data[$name] = $rows['brand_id'];
			}
		}
	 }
	
	if($return_json){
		echo json_encode($reurn_data);
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_brand_contact")
{
	$return_json= ( isset($_REQUEST['return_json']) ) ? true : false;
	$reurn_data	= array();
	
	if($_REQUEST['mid']!="" && $_REQUEST['mid']!=0)
	{
		$sql = "select fs.brand_name,bm.brand_id, bm.manufacture_id, fs.source_idoc from in_contact_brand_manufacture as bm
	 inner join in_contact_brand as fs on fs.id=bm.brand_id 
	 where bm.manufacture_id = '".$_REQUEST['mid']."' and fs.del_status = '0' order by fs.brand_name asc";
	 }
	 else
	 {
	 	$sql = "select brand_name, id as brand_id, source_idoc from in_contact_brand where del_status='0' order by brand_name asc";
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
			
			if(!$return_json){
				echo "<option ".$sel." value='".$rows['brand_id']."' idoc_source='".$rows['source_idoc']."'>".$rows['brand_name']."</option>";
			}
			else{
				$name = html_entity_decode($rows['brand_name']);
				$reurn_data[$name] = $rows['brand_id'];
			}
		}
	 }
	 if($return_json){
		echo json_encode($reurn_data);
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_style")
{
	$return_json= ( isset($_REQUEST['return_json']) ) ? true : false;
	$reurn_data	= array();
	
	$framesData = (isset($_REQUEST['framesData']) && $_REQUEST['framesData']==true) ? true : false;
	
	if($_REQUEST['bid']!="" && $_REQUEST['bid']!=0)
	{
		$sql = "select fs.style_name, bs.style_id, bs.brand_id";
	 
	 	if($framesData)
			$sql .= ', fs.del_status';
			
	 	$sql .=' from in_style_brand as bs inner join in_frame_styles as fs on fs.id=bs.style_id 
				 where bs.brand_id = "'.$_REQUEST['bid'].'"';
		
		if($framesData)
			$sql .= ' and fs.del_status != 2';
		else
			$sql .= ' and fs.del_status = 0';
		
		$sql .= ' order by fs.style_name asc';		 
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
			
			if(!$return_json){
				echo "<option ".$sel." value='".$rows['style_id']."'>".$rows['style_name']."</option>";
			}
			else{
				$name = html_entity_decode($rows['style_name']);
				
				if($framesData){
					$reurn_data[$name]['id'] = $rows['style_id'];
					$reurn_data[$name]['del'] = $rows['del_status'];
				}
				else
					$reurn_data[$name] = $rows['style_id'];
			}
		}
	 }
	
	if($return_json){
		echo json_encode($reurn_data);
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="color_code")
{
	$sql = "select color_code from in_frame_color where id = '".$_REQUEST['color_id']."'";
	 
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($rows['color_code']!="")
			{
				$color_code=$rows['color_code'];
			}
			else
			{
				$color_code="";	
			}
		}
	 }	
	 echo $color_code;
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="style")
{
	$sql = "select `style` from `in_item` where id = '".$_REQUEST['style']."'";
	 
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($rows['style']!="")
			{
				$style=$rows['style'];
			}
			else
			{
				$style="";	
			}
		}
	 }	
	 echo $style;
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_manufacture")
{
	$chk=array();
	$chk['1'] = "frames_chk='1'";
	$chk['2'] = "lenses_chk='1'";
	$chk['3'] = "cont_lenses_chk ='1'";
	$chk['5'] = "supplies_chk='1'";
	$chk['6'] = "medicine_chk ='1'";
	$chk['7'] = "accessories_chk='1'";
	
	$sql = "SELECT id, manufacturer_name FROM in_manufacturer_details where ".$chk[$_REQUEST['tid']]."  and del_status = '0' order by manufacturer_name asc";
	 
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	
		while($rows = imw_fetch_array($res)) 
		{
			$sel="";
			if($_REQUEST['mid']==$rows['id'])
			{
				$sel = 'selected';
			}
			echo "<option ".$sel." value='".$rows['id']."'>".$rows['manufacturer_name']."</option>";
		}
	 }
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_vendor_brand")
{
	if($_REQUEST['mod_id']!="3")
	{
		if($_REQUEST['vid']!="" && $_REQUEST['vid']!=0)
		{
			$sql = "SELECT fs.id,fs.frame_source  FROM `in_frame_sources` as fs  inner join `in_brand_manufacture`  as bm on bm.brand_id = fs.id inner join `in_vendor_manufacture` as vm on vm.manufacture_id = bm.manufacture_id  where vm.vendor_id = '".$_REQUEST['vid']."'  and del_status = '0' group by bm.brand_id order by frame_source asc";
		 } 
		 else
		 {
			$sql = "SELECT frame_source,id FROM in_frame_sources WHERE del_status = '0' ORDER BY frame_source ASC";
		 }
	 }
	 elseif($_REQUEST['mod_id']=="3")
	 {
	 	$sql = "select id,brand_name as frame_source from in_contact_brand where del_status='0' order by brand_name asc";
	 }
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
		while($rows = imw_fetch_array($res)) 
		{
			$sel='';
			if($_REQUEST['bid']==$rows['id'])
			{
				$sel = 'selected';
			}
			echo "<option ".$sel." value='".$rows['id']."'>".$rows['frame_source']."</option>";
		}
	 }
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_manufacturer_brand")
{
	//get only frame brands
	$sql = "SELECT in_frame_sources.frame_source,in_frame_sources.id FROM in_frame_sources left join in_brand_manufacture on in_frame_sources.id=in_brand_manufacture.brand_id WHERE in_frame_sources.del_status = '0' and in_brand_manufacture.manufacture_id='$_REQUEST[mid]' ORDER BY in_frame_sources.frame_source ASC";
	
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
		while($rows = imw_fetch_array($res)) 
		{
			$sel='';
			if($_REQUEST['bid']==$rows['id'])
			{
				$sel = 'selected';
			}
			echo "<option ".$sel." value='".$rows['id']."'>".$rows['frame_source']."</option>";
		}
	 }
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_brand_collection")
{
	//get only frame brands
	$sql = "SELECT * from xml_frames_collections where BrandFramesMasterID='$_REQUEST[bid]'";
	
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
		while($rows = imw_fetch_array($res)) 
		{
			$sel='';
			if($_REQUEST['cid']==$rows['CollectionFramesMasterID'])
			{
				$sel = 'selected';
			}
			echo "<option ".$sel." value='".$rows['CollectionFramesMasterID']."'>".$rows['CollectionName']."</option>";
		}
	 }
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_vendor")
{
	$return_json= ( isset($_REQUEST['return_json']) ) ? true : false;
	$reurn_data	= array();
	
	if($_REQUEST['mid']!="" && $_REQUEST['mid']!=0)
	{
		$sql = "SELECT vd.id,vd.vendor_name  FROM `in_vendor_details` as vd  inner join `in_vendor_manufacture` as vm on vm.vendor_id = vd.id where vm.manufacture_id = '".$_REQUEST['mid']."'  and del_status = '0' order by vendor_name asc";
	 } 
	 else
	 {
	 	$sql="select id,vendor_name from in_vendor_details where del_status='0' order by vendor_name asc";
	 }
	 $res = imw_query($sql);
	 $nums = imw_num_rows($res); 
	 if($nums > 0)
	 {
	 	$sel="";
		while($rows = imw_fetch_array($res)) 
		{
			if($_REQUEST['vid']==$rows['id'])
			{
				$sel = 'selected';
			}
			else
			{
				$sel="";
			}
			
			if(!$return_json){
				echo "<option ".$sel." value='".$rows['id']."'>".$rows['vendor_name']."</option>";
			}
			else{
				$name = html_entity_decode($rows['vendor_name']);
				$reurn_data[$name] = $rows['id'];
			}
		}
	 }
	 
	 if($return_json){
		echo json_encode($reurn_data);
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_prac_code_name")
{
	if($_REQUEST['prac_code_id']!="" && $_REQUEST['prac_code_id']>0)
	{
		$qry = imw_query("select cpt_prac_code, cpt_desc from cpt_fee_tbl where cpt_fee_id='".$_REQUEST['prac_code_id']."' and delete_status = '0'");
		$res = imw_fetch_assoc($qry);
		echo $res['cpt_prac_code']."~~~~".$res['cpt_desc'];
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="prac_by_type")
{
	if($_REQUEST['type_id']!="" && $_REQUEST['type_id']>0)
	{
		$qry = imw_query("select cpt.cpt_prac_code, cpt.cpt_desc from cpt_fee_tbl as cpt inner join ".$_REQUEST['tb_name']." as lt on lt.id='".$_REQUEST['type_id']."' where FIND_IN_SET(cpt.cpt_fee_id, REPLACE(lt.prac_code, ';', ',')) and cpt.delete_status = '0'");
		if($_REQUEST['tb_name']=="in_lens_material"){
			$res = array('cpt_prac_code'=>'', 'cpt_desc'=>'');
			while($row=imw_fetch_assoc($qry)){
				$res['cpt_prac_code'] .= $row['cpt_prac_code'].";";
				$res['cpt_desc'] .= $row['cpt_desc'].";";
			}
			$res['cpt_prac_code'] = rtrim($res['cpt_prac_code'], ";");
			$res['cpt_desc'] = rtrim($res['cpt_desc'], ";");
		}
		else{
			$res = imw_fetch_assoc($qry);
		}
		echo $res['cpt_prac_code']."~~~~".$res['cpt_desc'];
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="prac_by_type_multi")
{
	if($_REQUEST['type_id']!="" && $_REQUEST['type_id']>0)
	{
		if($_REQUEST['tb_name']=="in_lens_ar"){
			$default_code_resp = imw_query("SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`=2 AND `sub_module`='coating' AND `del_status`=0 LIMIT 1");
			$default_code_resp = imw_fetch_assoc($default_code_resp);
			$default_code = $default_code_resp['prac_code'];
		}
		
		$rt_prac_codes = array();
		$qry = imw_query("select cpt.cpt_prac_code, cpt.cpt_desc from cpt_fee_tbl as cpt inner join ".$_REQUEST['tb_name']." as lt on lt.id IN(".$_REQUEST['type_id'].") where cpt.cpt_fee_id=lt.prac_code and cpt.delete_status = '0' ORDER BY FIELD(lt.id, ".$_REQUEST['type_id'].")");
		if(imw_num_rows($qry)>0){
			while($res = imw_fetch_assoc($qry)){
				array_push($rt_prac_codes,($res['cpt_prac_code']=="")?$default_code:$res['cpt_prac_code']);
			}
		}
		print implode(";",$rt_prac_codes);
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="prac_code_by_item")
{
	$qry = imw_query("select ".$_REQUEST['cl_name']." as prac from in_item_price_details where item_id = '".trim($_REQUEST['item_id'])."' and module_type_id='2'");
	$returnArr = array();
	while($row = imw_fetch_array($qry))
	{
		$item_prac_code = $row['prac'];
		if($item_prac_code > 0)
		{
			$qry = imw_query("select cpt_prac_code from cpt_fee_tbl where cpt_fee_id='".$item_prac_code."' and delete_status = '0'");
			$res = imw_fetch_assoc($qry);
			$returnArr = $res['cpt_prac_code'];	
		}
		else
		{
			$returnArr="";
		}
		
		echo $returnArr;
	}
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="prac_code_by_item_multi")
{
	$qry = "select ".$_REQUEST['cl_name']." as prac from in_item_price_details where item_id = '".trim($_REQUEST['item_id'])."' and module_type_id='2' ORDER BY id ASC LIMIT 1";
	$qry = imw_query($qry);
	$returnArr = array();
	while($row = imw_fetch_array($qry))
	{
		$item_prac_codes = explode(";", $row['prac']);
		$item_prac_codes = array_count_values($item_prac_codes);
		
		$item_prac_code = str_replace(";", "','", $row['prac']);
		$item_prac_code = "'".$item_prac_code."'";
		if($row['prac'] != "")
		{
			$qry = "select cpt_prac_code, cpt_fee_id from cpt_fee_tbl where cpt_fee_id IN(".$item_prac_code.") and delete_status = '0' ORDER BY FIELD(cpt_fee_id, ".$item_prac_code.")";
			$qry = imw_query($qry);
			while($res = imw_fetch_assoc($qry)){
				
				$temp = array();
				$temp = array_fill(0, $item_prac_codes[$res['cpt_fee_id']], $res['cpt_prac_code']);
				array_push($returnArr, implode(";", $temp));
			}
		}
		else
		{
			$returnArr="";
		}
	}
	print implode(';', $returnArr);
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="get_price_from_praccode")
{
	$str=$_REQUEST['prac_code'];
	$type= (isset($_REQUEST['type']) && $_REQUEST['type']=="cl_disc")?$_REQUEST['type']:false;
	$getCPTPriceQry = imw_query("SELECT b.cpt_desc,b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										".(($type)?"b.cpt_fee_id":"b.cpt_prac_code")."='$str'
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
	$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
	$cpt_fee = $getCPTPriceRow['cpt_fee'];
	$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
	$cpt_prac_code_desc=$getCPTPriceRow['cpt_desc'];
	if(imw_num_rows($getCPTPriceQry)==0){
		$getCPTPriceQry = imw_query("SELECT b.cpt_desc,b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										(".(($type)?"b.cpt_fee_id":"b.cpt4_code")."='$str' or b.cpt_desc='$str')
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		$cpt_fee = $getCPTPriceRow['cpt_fee'];
		$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
		$cpt_prac_code_desc=$getCPTPriceRow['cpt_desc'];
	}
	echo $cpt_prac_code."~~~".$cpt_fee."~~~".$cpt_prac_code_desc;
}
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="itemized_prac_into_lensindex" && $_REQUEST['item_id']>0)
{
	$qry = imw_query("select * from in_item_price_details where item_id = '".trim($_REQUEST['item_id'])."' LIMIT 1");
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
elseif($_REQUEST['action']!="" && $_REQUEST['action']=="getcitystate")
{
 $code_qry="Select * from zip_codes where zip_code='".stripslashes($_REQUEST['zipcode'])."'";

		$code_res=imw_query($code_qry) or die(imw_error());
		if(imw_num_rows($code_res)>0)	{
			$row=imw_fetch_array($code_res);
			$city_state=$row['city']."-".$row['state_abb'];
			echo $city_state;
		}	
		else
		{
			echo "false";	
		}
}
elseif(isset($_POST['action']) && $_POST['action']=="saveTax")
{
	/*Facility Id*/
	$facility = (int)$_POST['facility_id'];
	/*Tax string*/
	$loc_tax = implode("~~~", $_POST['location_tax']);
	
	$sql = "UPDATE `in_location` SET `tax`='".$loc_tax."' WHERE `id`='".$facility."'";
	if(imw_query($sql))echo '1';
	else echo '0';
}
/*Vision Web Drp downs for Inventory section - Lens*/
elseif(isset($_POST['action']) && $_POST['action']=="getVisionDDAdmin")
{
	
	$id = $_POST['value'];
	$element = $_POST['element'];
	$sql = false;
	$rq_prInterface = (isset($_REQUEST['ptInterface']))?true:false;
	
	if($element=="design"){
	
		$vw_codes = false;
		$sql0 = imw_query("SELECT 
		`vw_code` 
	FROM 
		`in_lens_type` 
	WHERE 
		`id` = '".$id."' 
		AND `del_status` = 0");
		
		if($sql0 && imw_num_rows($sql0)>0){
			$vw_code = imw_fetch_assoc($sql0);
			if(trim($vw_code['vw_code'])!=""){
				$vw_codes = $vw_code['vw_code'];
			}
		}
		
		if(!$vw_codes && $GLOBALS['connect_visionweb']!="" && $rq_prInterface){
			$sql0 = imw_query("SELECT 
									GROUP_CONCAT(DISTINCT(`vw_code`)) AS 'vw_code'
								FROM 
									`in_lens_type` 
								WHERE 
									`del_status` = 0 
									AND `vw_code` != ''");
			if($sql0 && imw_num_rows($sql0)>0){
				$vw_code = imw_fetch_assoc($sql0);
				if(trim($vw_code['vw_code'])!=""){
					$vw_codes = $vw_code['vw_code'];
				}
			}
		}
		
		if($vw_codes){
			$vw_codes = str_replace(",", "', '", $vw_codes);
			$vw_codes = "'".$vw_codes."'";
			
			$sql = "SELECT 
						`ld`.`id`, 
						`ld`.`design_name` AS 'name', 
						`ld`.`vw_code` AS 'vw_code', 
						`ld`.`lens_vw_code` AS 'vw_code_type'
					FROM 
						`in_lens_design` `ld` 
					WHERE
						`ld`.`lens_vw_code` IN(".$vw_codes.")
						AND `ld`.`del_status` = 0
					ORDER BY 
						`ld`.`design_name` ASC";
		}
	}
	else if($element=="material"){
		
		$where = "`lmd`.`design_id` = '".$id."' 
					AND `lm`.`del_status` = 0";
		
		if($GLOBALS['connect_visionweb']!="" && $rq_prInterface){
			$sql0 = imw_query("SELECT
									`material_id`
								FROM
									`in_lens_material_design`
								WHERE
									`design_id` = '".$id."'
								LIMIT
									1");
			
			if($sql0 && imw_num_rows($sql0)==0){
				$where = "`lm`.`del_status` = 0";
			}
		}
		
		$sql = "SELECT 
					`lm`.`id`, 
					`lm`.`material_name` AS 'name', 
					`lm`.`vw_code` AS 'vw_code'
				FROM 
					`in_lens_material_design` `lmd` 
					LEFT JOIN `in_lens_material` `lm` ON(
						`lmd`.`material_id` = `lm`.`id`
					) 
				WHERE 
					".$where."
				GROUP BY 
					`lm`.`id`
				ORDER BY 
					`lm`.`material_name` ASC";
	}
	else if($element=="coating"){
		
		$where = "`larm`.`material_id` = '".$id."' 
					AND `lar`.`del_status` = 0";
		
		if($GLOBALS['connect_visionweb']!="" && $rq_prInterface){
			
			$sql0 = imw_query("SELECT
									`ar_id`
								FROM
									`in_lens_ar_material`
								WHERE
									`material_id` = '".$id."'
								LIMIT
									1");
			
			if($sql0 && imw_num_rows($sql0)==0){
				$where = "`lar`.`del_status` = 0";
			}
		}
		
		$sql = "SELECT 
					`lar`.`id`, 
					`lar`.`ar_name` AS 'name', 
					`lar`.`vw_code` AS 'vw_code'
				FROM 
					`in_lens_ar_material` `larm` 
					LEFT JOIN `in_lens_ar` `lar` ON(
						`larm`.`ar_id` = `lar`.`id`
					) 
				WHERE 
					".$where."
				GROUP BY 
					`lar`.`id`
				ORDER BY 
					`lar`.`ar_name` ASC";
	}
	$response = array();
	if($sql){
		$resp = imw_query($sql);
		if($resp && imw_num_rows($resp)>0){
			while($row = imw_fetch_object($resp)){
				$data = array();
				$data['id'] = $row->id;
				$data['name'] = $row->name;
				$data['vw_code'] = $row->vw_code;
				$data['vw_code_type'] = '';
				array_push($response, $data);
			}
		}
	}
	print json_encode($response);
}
/*End Vision Web Dropdown for Lens*/
/*Sync Contact Lenses from iDoc*/
elseif(isset($_POST['action']) && $_POST['action']=='iDocSync'){
ini_set('max_execution_time', 0);

	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
		
	/*Manufacturers List from Optical*/
	$manufacturers = array();
	$manuf_resp = imw_query("SELECT `id`, `manufacturer_name` FROM `in_manufacturer_details` WHERE `cont_lenses_chk`=1 AND `del_status`=0");
	if($manuf_resp && imw_num_rows($manuf_resp)>0){
		while($row = imw_fetch_object($manuf_resp)){
			$manufacturers[$row->manufacturer_name] = $row->id;
		}
	}
	imw_free_result($manuf_resp);
	
	/*Styles List from Optical*/
	$styles = array();
	$style_resp = imw_query("SELECT `id`, `brand_name` FROM `in_contact_brand` WHERE `del_status`=0");
	if($style_resp && imw_num_rows($style_resp)>0){
		while($row = imw_fetch_object($style_resp)){
			$styles[$row->brand_name] = $row->id;
		}
	}
	imw_free_result($style_resp);
	
	/*Contact Lens Styles and Manufacturers from iDoc*/
	$cl_idoc_resp = imw_query("SELECT `manufacturer`, `style`, `cpt_fee_id`, `price`, `base_curve`, `diameter` FROM `contactlensemake` WHERE `manufacturer`!='' AND `del_status`=0");
	
	if($cl_idoc_resp && imw_num_rows($cl_idoc_resp)>0){
		
		while($row = imw_fetch_object($cl_idoc_resp)){
			$row->manufacturer = trim($row->manufacturer);
			$row->style = trim($row->style);
			$row->cpt_fee_id = trim($row->cpt_fee_id);
			$row->price = trim($row->price);
			$row->base_curve= trim($row->base_curve);
			$row->diameter= trim($row->diameter);
			
			$fee = $row->price;
			$fee_resp = imw_query("SELECT `cpt_fee` FROM `cpt_fee_table` WHERE `cpt_fee_id`='".$row->cpt_fee_id."' AND `fee_table_column_id`=1 LIMIT 1");
			if($fee_resp && imw_num_rows($fee_resp)>0){
				$fee = imw_fetch_assoc($fee_resp);
				$fee = $fee['cpt_fee'];
			}
			
			/*Find matching manufacturer in iDoc adn Insert if not exists*/
			if(!isset($manufacturers[$row->manufacturer])){
				
				$manuf_insert_qry = "INSERT INTO `in_manufacturer_details` SET `manufacturer_name`='".addslashes(trim($row->manufacturer))."', `cont_lenses_chk`=1, `entered_date`='".$date."', `entered_time`='".$time."', `entered_by`=".$opr_id;
				if(imw_query($manuf_insert_qry)){
					$manufacturers[$row->manufacturer] = imw_insert_id();
				}
			}
			
			/*Find matching style in iDoc adn Insert if not exists*/
			if(!isset($styles[$row->style])){
				
				$style_insert_qry = "INSERT INTO `in_contact_brand` SET `brand_name`='".addslashes($row->style)."', `prac_code`='".$row->cpt_fee_id."', `retail_price`='".$fee."', `source_idoc`=1, `entered_date`='".$date."', `entered_time`='".$time."', `entered_by`=".$opr_id;
				if(imw_query($style_insert_qry)){
					$styles[$row->style] = imw_insert_id();
					
					/*Associate added brand with it's manufacturer*/
					/*Check if already exists, in case of prevous data*/
					$assoc_resp = imw_query("SELECT `id` FROM `in_contact_brand_manufacture` WHERE `brand_id`='".$styles[$row->style]."' AND `manufacture_id`='".$manufacturers[$row->manufacturer]."'");
					if($assoc_resp && imw_num_rows($assoc_resp)==0){
						/*Insert Data*/
						$sql_inser = imw_query("INSERT INTO `in_contact_brand_manufacture` SET `brand_id`='".$styles[$row->style]."', `manufacture_id`='".$manufacturers[$row->manufacturer]."'");
					}
				}
			}
			else{
				imw_query("UPDATE `in_contact_brand` SET `source_idoc`=1 WHERE `id`='".$styles[$row->style]."'");	
			}
			
			/*Search for matching item in iDoc, if not found then create new record*/
			$resp_item = imw_query("SELECT `id` FROM `in_item` WHERE `name`='".addslashes($row->style)."' AND `bc`='".$row->base_curve."' AND `diameter`='".$row->diameter."' AND `module_type_id`=3 AND `del_status`=0");
			if($resp_item && imw_num_rows($resp_item)==0){
				
				/*$itemCount = "SELECT `id` FROM `in_item` WHERE `name` LIKE '".addslashes($row->style)."%' AND `manufacturer_id`='".$manufacturers[$row->manufacturer]."' AND `module_type_id`=3 AND `del_status`=0";
				$respCount = imw_query($itemCount);
				$itemCount = imw_num_rows($respCount);
				$style_bk	= $row->style;
				if( $itemCount > 0 )
					$row->style = $row->style.' - '.$itemCount;*/
				
				/*Obtain Unique upc code for new entry in optical items*/
				$sel_upc_num = imw_query("SELECT `id`, `upc_num` FROM `in_upc_no`");
				$fetch_upc_no = imw_fetch_assoc($sel_upc_num);
				$upc_num = $fetch_upc_no['upc_num'];
				$flag = false;
				
				do{
					$sel_frm_item = imw_query("SELECT `id` FROM `in_item` WHERE `upc_code`='".$upc_num."'");
					if(imw_num_rows($sel_frm_item)>0){
						$upc_num=$upc_num+1;
						$flag = true;
					}
					else{$flag = false;}
				}while($flag);
				
				$sql_item = "INSERT INTO `in_item` SET `upc_code`='".$upc_num."', `name`='".addslashes($row->style)."', `module_type_id`=3, `item_prac_code`='".$row->cpt_fee_id."', `manufacturer_id`='".$manufacturers[$row->manufacturer]."', `brand_id`='".$styles[$row->style]."', `bc`='".$row->base_curve."', `diameter`='".$row->diameter."', `retail_price`='".$fee."', `retail_price_flag`=1";
				imw_query($sql_item);
				
				$new_upc_no = $upc_num+1;
				imw_query("UPDATE `in_upc_no` SET `upc_num`='".$new_upc_no."' WHERE `id`='".$fetch_upc_no['id']."'");
			}
		}
	}
	imw_free_result($cl_idoc_resp);
}
/*End Contact Lens Sync from iDoc*/
/*Search Contact Lens Id by Name*/
elseif(isset($_POST['action']) && $_POST['action']=='find_contact_id')
{
	
	$sql_resp = imw_query("SELECT `id` FROM `in_item` WHERE `name`='".addslashes(trim($_POST['item_name']))."' and del_status=0 order by manufacturer_id desc LIMIT 1");
	if($sql_resp && imw_num_rows($sql_resp)>0){
		$data = imw_fetch_assoc($sql_resp);
		print $data['id'];
	}
}
/*End Search contact lend id by name*/
/*Search VisionWeb Lab HX*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='find_vw_lab_hx')
{
	$all_data="";
	$rowbg="";
	$sql_resp = imw_query("SELECT `vw_status`,vw_received_date FROM `in_vw_order_status_detail` WHERE `vw_order_id`='".addslashes(trim($_REQUEST['vw_order_id']))."' order by vw_received_date desc");
	if($sql_resp && imw_num_rows($sql_resp)>0){
		$all_data.='<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr class="listheading"><td style="font-weight:bold; font-size:15px;" width="35%">Date</td><td style="font-weight:bold; font-size:15px;">Status</td></tr>';
		while($data = imw_fetch_array($sql_resp)){
			if($rowbg=='even'){$rowbg="odd";}else{$rowbg="even";}
			$vw_received_at_stt=strtotime($data['vw_received_date']);
			$vw_received_at=date("m-d-Y h:i:s",$vw_received_at_stt);
			$all_data.='
			<tr class="'.$rowbg.'"><td>'.$vw_received_at.'</td><td>'.vw_lab_status($data['vw_status']).'</td></tr>';
		}
		$all_data.='</table>';
	}
	if($all_data==""){
		$all_data="No record found.";
	}
	print $all_data;
}
/*End Search VisionWeb Lab HX*/
/*Manufacturers Data Json*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='get_mod_manufacturer' && $_REQUEST['mod_id']!="")
{
	
	$data = array();
	
	/*Mod Type Fields*/
	$chk=array();
	$chk['1'] = "`frames_chk`=1";
	$chk['2'] = "`lenses_chk`=1";
	$chk['3'] = "`cont_lenses_chk`=1";
	$chk['5'] = "`supplies_chk`=1";
	$chk['6'] = "`medicine_chk`=1";
	$chk['7'] = "`accessories_chk`=1";
	
	$sql = "SELECT `id`, `manufacturer_name` AS 'name' FROM `in_manufacturer_details` WHERE ".$chk[$_REQUEST['mod_id']]."
			AND `del_status`=0 ORDER BY `manufacturer_name` ASC";
	
	$resp = imw_query($sql);
	
	if($resp && imw_num_rows($resp)){
		
		while($row = imw_fetch_object($resp)){
			$data[$row->name] = $row->id;
		}
	}
	print json_encode($data);
}
/*Vendors Data Json*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='get_manuf_vendors' && $_REQUEST['mod_id']!="")
{
	
	$data = array();
	
	$sql = 'SELECT `v`.`id`, `v`.`vendor_name` AS \'name\' FROM `in_vendor_manufacture` `vm`
			LEFT JOIN `in_vendor_details` `v` ON(`vm`.`vendor_id` = `v`.`id`) WHERE `vm`.`manufacture_id`='.$_REQUEST['mod_id'].'
			AND `v`.`del_status`=0 ORDER BY `v`.`vendor_name` ASC';
	
	$resp = imw_query($sql);
	
	if($resp && imw_num_rows($resp)){
		
		while($row = imw_fetch_object($resp)){
			$data[$row->name] = $row->id;
		}
	}
	print json_encode($data);
}
/*Brands Data Json*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']=='get_manuf_brands' && $_REQUEST['mod_id']!="" && $_REQUEST['module']!=""){
	
	$data = array();
	
	$moduleType = $_REQUEST['module'];
	
	$framesData = (isset($_REQUEST['framesData']) && $_REQUEST['framesData']==true) ? true : false;
	
	$sql = "";
	if($moduleType=="1"){
		$sql = 'SELECT `b`.`id`, `b`.`frame_source` AS \'name\'';
		
		if($framesData)
			$sql .= ', `b`.`del_status`';
		
		$sql .= ' FROM `in_brand_manufacture` `bm` LEFT JOIN `in_frame_sources` `b` ON(`bm`.`brand_id` = `b`.`id`) WHERE `bm`.`manufacture_id`='.$_REQUEST['mod_id'];
		
		if($framesData)
			$sql .= ' AND `b`.`del_status` !=2 AND `b`.`BrandFramesMasterID`!=\'\' ';
		else
			$sql .= ' AND `b`.`del_status`=0 ';
		
		$sql .= 'ORDER BY `b`.`frame_source` ASC';
	}
	elseif($moduleType=="3"){
		$sql = 'SELECT `b`.`id`, `b`.`brand_name` AS \'name\' FROM `in_contact_brand_manufacture` `bm`
				LEFT JOIN `in_contact_brand` `b` ON(`bm`.`brand_id` = `b`.`id`) WHERE `bm`.`manufacture_id`='.$_REQUEST['mod_id'].'
				AND `b`.`del_status`=0 ORDER BY `b`.`brand_name` ASC';
	}
	
	if($sql!=""){
		
		$resp = imw_query($sql);
		if($resp && imw_num_rows($resp)){
			
			while($row = imw_fetch_object($resp)){
				
				if($framesData){
					$data[$row->name]['id'] = $row->id;
					$data[$row->name]['del'] = $row->del_status;
				}
				else
					$data[$row->name] = $row->id;
			}
		}
	}
	print json_encode($data);
}
/*Frames Data Agree Terms & conditions*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']==='agreeFramesData'){
	
	include('../../library/data_api/framesData.php');
	require_once('../../library/data_api/framesDataDetails.php');	/*FramesData Credentials*/
	$obj = new framesData();
	$obj->setConfig($configs);	/*Create Authorization Tocker*/
	$obj->agreeTerms();
}
/*Get Retail Price Calculation formula*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']==='getFormula'){
	
	$formula = '';
	
	$module_type	= ( isset($_REQUEST['module']) && $_REQUEST['module']!='' ) ? (int)$_REQUEST['module'] : false;
	$manuf_id		= ( isset($_REQUEST['manuf']) && $_REQUEST['manuf']!='' ) ? (int)$_REQUEST['manuf'] : false;
	$brand_id		= ( isset($_REQUEST['brand']) && $_REQUEST['brand']!='' ) ? (int)$_REQUEST['brand'] : false;
	
	if( $module_type && ($manuf_id || $brand_id) ){
		
		$data = array();
		
		if($manuf_id)
			$data['manufacturer_id'] = $manuf_id;
		if($brand_id)
			$data['brand_id'] = $brand_id;
		
		$formula = get_retail_formula($module_type, $data);
	}
	echo $formula;
}
/*Get Retail Price Calculation formula*/
elseif(isset($_REQUEST['action']) && $_REQUEST['action']==='gtFrameColorId'){
	$colorName = trim($_REQUEST['colorName']);
	$sql = "SELECT `id` FROM `in_frame_color` WHERE `color_name`='".addslashes($colorName)."' LIMIT 1";
	$resp = imw_query($sql);
	$colorid = '';
	if($resp && imw_num_rows($resp)==1){
		$colorid = imw_fetch_assoc($resp);
		$colorid = $colorid['id'];
	}
	print $colorid;
}
?>