<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
if($_REQUEST['pass']==1)
{
set_time_limit(0);
$date = date("Y-m-d");
$time = date("h:i:s"); 
	function pre($d)
	{
		echo'<pre>';
		print_r($d);
		echo'</pre>';	
	}
$csvFileName="frame_import_huntington_eye.csv";
/*fiels_mrgd : a, dbl, */

$fileContents = fopen($csvFileName,"r");
$totPatImport = 0;

//create array of manufacturer 
$manu_query=imw_query("select manufacturer_name,id from in_manufacturer_details where frames_chk='1' and del_status='0' order by manufacturer_name asc")or die(imw_error().' _1');
while($manu_data=imw_fetch_object($manu_query))
{
	if($manu_data->manufacturer_name)$manufacturerArr[strtolower($manu_data->manufacturer_name)]=$manu_data->id;
}
//add new manufacturer
function FUNC_manufactured($manu_arr,$manu)
{
	imw_query("insert into in_manufacturer_details set frames_chk='1' ,del_status='0', manufacturer_name='$manu'")or die(imw_error().' _2');
	$manu_id=imw_insert_id();	
	$manu_arr[strtolower($manu)]=$manu_id;
	return $manu_arr;
}

//get list of vendors
$ven_query=imw_query("select vd.vendor_name,vd.id, vm.manufacture_id from in_vendor_manufacture as vm left join in_vendor_details as vd ON vm.vendor_id=vd.id where manufacture_id<>'' and  del_status=0 order by vm.manufacture_id, vd.vendor_name")or die(imw_error().' _3');
while($ven_data=imw_fetch_object($ven_query))
{
	$vender_arr[$ven_data->manufacture_id][strtolower($ven_data->vendor_name)]=$ven_data->id;	
}

//add new vendor
function FUNC_vendor($ven_arr, $ven, $manu_id)
{
	$vender_check=imw_query("select id from in_vendor_details where vendor_name = '$ven'")or die(imw_error().' _4');
	if(imw_num_rows($vender_check)==0)
	{
		//get new id
		imw_query("insert into in_vendor_details set vendor_name = '$ven', entered_date='$date', entered_time='$time'")or die(imw_error().' _5');
		$ven_id=imw_insert_id();	
	}
	else
	{
		//get existing id
		$vender_data=imw_fetch_object($vender_check);
		$ven_id=$vender_data->id;
	}
	
	imw_query("insert into in_vendor_manufacture set vendor_id = '$ven_id', manufacture_id = '$manu_id' ")or die(imw_error().' _6');
	$ven_arr[$manu_id][strtolower($ven)]=$ven_id;
	return $ven_arr;
}

//get list of brands
$brand_query=imw_query("select fs.frame_source,bm.brand_id, bm.manufacture_id from in_brand_manufacture as bm
	 left join in_frame_sources as fs on fs.id=bm.brand_id 
	 where fs.del_status = '0' order by fs.frame_source asc")or die(imw_error().' _7');
while($brand_data=imw_fetch_object($brand_query))
{
	$brand_arr[$brand_data->manufacture_id][strtolower($brand_data->frame_source)]=$brand_data->brand_id;	
}

	 
//add new brand
function FUNC_brand($brand_arr, $brand, $manu_id)
{
	imw_query("insert into in_frame_sources set frame_source = '$brand', entered_date='$date', entered_time='$time'")or die(imw_error().' _8');
	$brand_id=imw_insert_id();	
	imw_query("insert into in_brand_manufacture set brand_id = '$brand_id', manufacture_id = '$manu_id' ");
	$brand_arr[$manu_id][strtolower($brand)]=$brand_id;
	return $brand_arr;
}


//get list of styles
$style_query=imw_query("select fs.style_name,bs.style_id, bs.brand_id from in_style_brand as bs
	 inner join in_frame_styles as fs on fs.id=bs.style_id 
	 where fs.del_status = '0' order by fs.style_name asc")or die(imw_error().' _9');
while($style_data=imw_fetch_object($style_query))
{
	$style_arr[$style_data->brand_id][strtolower($style_data->style_name)]=$style_data->style_id;	
}

	 
//add new style
function FUNC_style($style_arr, $style_name, $brand_id)
{
	//check is that style exist
	$check_style=imw_query("select id from in_frame_styles where style_name = '$style_name'")or die(imw_error().' _10');
	if(imw_num_rows($check_style)==0)
	{
		//get new id
		imw_query("insert into in_frame_styles set style_name = '$style_name', entered_date='$date', entered_time='$time'")or die(imw_error().' _11');
		$style_id=imw_insert_id();	
	}
	else
	{
		//get existing id
		$style_data=imw_fetch_object($check_style);
		$style_id=$style_data->style_id;
	}
	
	imw_query( "insert into in_style_brand set style_id = '$style_id', brand_id = '$brand_id' ")or die(imw_error().' _12');
	$style_arr[$brand_id][strtolower($style_name)]=$style_id;
	return $style_arr;
}

//get list of colors
$color_query=imw_query("select * from in_frame_color where del_status='0' order by color_name asc")or die(imw_error().' _13');
while($color_data=imw_fetch_object($color_query))
{
	$color_arr[strtolower($color_data->color_name)]=$color_data->id;	
}

//add new style
function FUNC_color($color_arr, $color_name)
{
	//get new id
	imw_query("insert into in_frame_color set color_name = '$color_name', color_code = '$color_name', entered_date='$date', entered_time='$time'")or die(imw_error().' _14');
	$color_id=imw_insert_id();	
	$color_arr[strtolower($color_name)]=$color_id;
	return $color_arr;
}

if(file_exists($csvFileName)){
	$data = fgetcsv($fileContents,10000,',');//used to skip header row
	
	while(($data = fgetcsv($fileContents,10000,',')) !== FALSE)
	{
		if($data[0])
		{
			$manufacturer="";
			$manufacturer=trim(addslashes(ucwords(strtolower($data[0]))));
			//check is that manufacturer exist
			if($manufacturerArr[strtolower($manufacturer)])
			$manu_id=$manufacturerArr[strtolower($manufacturer)];
			else
			{
				$manufacturerArr=FUNC_manufactured($manufacturerArr,$manufacturer);
				$manu_id=$manufacturerArr[strtolower($manufacturer)];
			}
			
			$vendor="";
			$vendor=trim(addslashes(ucwords(strtolower($data[1]))));
			//check is that vendor exist
			if($vender_arr[$manu_id][strtolower($vendor)])
			$ven_id=$vender_arr[$manu_id][strtolower($vendor)];
			else
			{
				$vender_arr=FUNC_vendor($vender_arr, $vendor, $manu_id);
				$ven_id=$vender_arr[$manu_id][strtolower($vendor)];
			}
			
			$brand="";
			$brand=trim(addslashes(ucwords(strtolower($data[2]))));
			//check is that brand exist
			if($brand_arr[$manu_id][strtolower($brand)])
			$brand_id=$brand_arr[$manu_id][strtolower($brand)];
			else
			{
				$brand_arr=FUNC_brand($brand_arr, $brand, $manu_id);
				$brand_id=$brand_arr[$manu_id][strtolower($brand)];
			}
			
			$style="";
			$style=trim(addslashes(ucwords(strtolower($data[3]))));
			//check is that brand exist
			if($style_arr[$brand_id][strtolower($style)])
			$style_id=$style_arr[$brand_id][strtolower($style)];
			else
			{
				$style_arr=FUNC_style($style_arr, $style, $brand_id);
				$style_id=$style_arr[$brand_id][strtolower($style)];
			}
			
			$upc="";
			$upc=trim(addslashes(strtoupper($data[4])));
			//----
			$sp_fileds=explode(',',$data[5]);
			$a="";
			$a=$sp_fileds[0];
			$dbl="";
			$dbl=$sp_fileds[1];
			//----
			$color_code="";
			$color_code=trim(addslashes($data[6]));
			//check is that brand exist
			if($color_arr[strtolower($color_code)])
			$color_id=$color_arr[strtolower($color_code)];
			else
			{
				$color_arr=FUNC_color($color_arr, $color_code);
				$color_id=$color_arr[strtolower($color_code)];
			}
			$qty_on_hand="";
			$qty_on_hand=trim(addslashes($data[7]));
			$cost="";
			$cost=trim(addslashes($data[8]));
			//remove $ sign
			$cost=str_replace('$','',$cost);
			$amount=0;
			$amount=$cost*$qty_on_hand;
			
			//get location id
			$loc_qry=imw_query("select id from in_location where del_status='0' order by loc_name LIMIT 0,1")or die(imw_error().' _15');
			$loc_data=imw_fetch_object($loc_qry);
			$loc_id=$loc_data->id;
			//check is that item exist
			$checkExist=imw_query("select id from in_item where upc_code = '$upc' and color_code = '$color_code' and retail_price = '$cost' and frame_style = '$style_id'")or die(imw_error().' _16');
			$exist=0;
			$exist=imw_num_rows($checkExist);
			
			
			//proceed only if item doesn't exist
			if($exist==0 && $upc!='')
			{
				//get total records for same upc
				$checkExistupc=imw_query("select id from in_item where name = '$upc'")or die(imw_error().' _16_1');
				
				if(imw_num_rows($checkExistupc)>0){
					$item_name=$upc.(imw_num_rows($checkExistupc)+1);
				}else{
					$item_name=$upc;
				}
				
				//get item id
				$ins_item=imw_query("insert into in_item set entered_date='$entered_date',entered_time='$entered_time',entered_by='$opr_id'")or die(imw_error().' _17');
				$item_id=imw_insert_id();
				//update stock table with item id and location id
				imw_query("insert into in_item_loc_total set stock='$qty_on_hand',item_id='$item_id',loc_id='$loc_id'");
				//save item detail
				$qry = "update in_item set
				manufacturer_id = '$manu_id',
				upc_code = '$item_name',
				module_type_id	= '1',		
				name = '$upc',
				vendor_id = '$ven_id',
				brand_id = '$brand_id',
				frame_style = '$style_id',
				a = '$a',
				dbl = '$dbl',
				color = '$color_id',
				color_code = '$color_code',
				wholesale_cost = '$cost',
				retail_price = '$cost',
				qty_on_hand = '$qty_on_hand',
				amount = '$amount'
				where id = '$item_id'";
				
				imw_query($qry)or die(imw_error().' _18');
				
				$totPatImport++;
			}
		}
	}
	fclose($fileContents);
	echo "<div style=\"color:green\">Total number of Patient Import are ".$totPatImport."</div>";
	
}else{echo "<p style='color:red;font-size:14px;'>".$csvFileName." file not found</p>";}
}
else{echo "<p style='color:red;font-size:14px;'>Auth required to run update</p>";}
?>