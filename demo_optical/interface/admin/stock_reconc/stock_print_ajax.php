<?php 
ini_set('max_execution_time',300);
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");

if($_REQUEST['id']!=""){
	$printFields = array();
	$id=$_REQUEST['id'];
	$i=1;
	
	$col_names=array();
	$col_names['upc_chk']="upc_code";
	$col_names['mf_chk']="manufacturer_name";
	$col_names['type_chk']="module_type_name";
	$col_names['ven_chk']="vendor_name";
	$col_names['brnd_chk']['all']="frame_source";
	$col_names['brnd_chk']['cl']="brand_name";
	$col_names['colr_chk']="color_name";
	$col_names['qnt_chk']="qty_on_hand";
	$col_names['shp_chk']="shape_name";
	$col_names['styl_chk']="style_name";
	$col_names['lens_focl_chk']="type_name";
	$col_names['lens_mate_chk']="material_name";
	$col_names['lens_a_r_chk']="ar_name";
	$col_names['lens_tran_chk']="transition_name";
	$col_names['lens_pol_chk']="polarized_name";
	$col_names['lens_edge_chk']="edge_name";
	$col_names['lens_tint_chk']="tint_type";
	$col_names['cnt_len_mat_chk']="type_name";
	$col_names['cnt_len_wer_chk']="cat_name";
	$col_names['cnt_len_sup_chk']="supply_name";
	$col_names['suply_mnt_chk']="measurment_name";
	$col_names['med_exp_chk']="expiry_date";
	
	$allowed_vals = array();	
	$status_check=imw_query("select * from in_print_option_stock where module_id=".$id." and status=1");
	$data.='<tr class="listheading" style="background: rgb(218,237,251);
background: -moz-linear-gradient(top,  rgba(218,237,251,1) 0%, rgba(217,237,248,1) 8%, rgba(200,229,245,1) 67%, rgba(197,224,241,1) 79%, rgba(194,226,241,1) 88%, rgba(194,223,241,1) 100%);
background: -webkit-linear-gradient(top,  rgba(218,237,251,1) 0%,rgba(217,237,248,1) 8%,rgba(200,229,245,1) 67%,rgba(197,224,241,1) 79%,rgba(194,226,241,1) 88%,rgba(194,223,241,1) 100%);
background: linear-gradient(to bottom,  rgba(218,237,251,1) 0%,rgba(217,237,248,1) 8%,rgba(200,229,245,1) 67%,rgba(197,224,241,1) 79%,rgba(194,226,241,1) 88%,rgba(194,223,241,1) 100%);"><th><input type="checkbox" id="select_all" name="select_all" onClick="checlAll(this)"></th>';
			$included=0;
			$wholesale=0;
			$retail=0;
		while($row1=imw_fetch_array($status_check))
		{
			$allowed_vals[]=$row1['option_chk'];
			switch($row1['option_chk'])
			{
				case 'upc_chk':
				$data.="<th>Upc Code</th>";
				break;
				case 'mf_chk':
				$data.="<th>Manufacturer</th>";
				break;
				case 'type_chk':
				$data.="<th>Type</th>";
				break;
				case 'ven_chk':
				$data.="<th>Vendor</th>";
				break;
				case 'brnd_chk':
				$data.="<th>Brand</th>";
				break;
				case 'colr_chk':
				$data.="<th>Color</th>";
				break;
				case 'prc_chk':
				$data.="<th>Price</th>";
				break;
				case 'shp_chk':
				$data.="<th>Frame Shape</th>";
				break;
				case 'styl_chk':
				$data.="<th>Frame Style</th>";
				break;
				case 'lens_focl_chk':
				$data.="<th>Seg Type</th>";
				break;
				case 'lens_mate_chk':
				$data.="<th>Material</th>";
				break;
				case 'lens_a_r_chk':
				$data.="<th>A/R</th>";
				break;
				case 'lens_tran_chk':
				$data.="<th>Transition</th>";
				break;
				case 'lens_pol_chk':
				$data.="<th>Polarized</th>";
				break;
				case 'wholesale_chk':
				$wholesale=1;
				break;
				case 'retail_chk':
				$retail=1;
				break;
				case 'gender_chk':
				$data.="<th>Gender</th>";
				break;
				case 'lens_edge_chk':
				$data.="<th>Edge</th>";
				break;
				case 'lens_tint_chk':
				$data.="<th>Tint</th>";
				break;
				case 'cnt_len_mat_chk':
				$data.="<th>Material</th>";
				break;
				case 'cnt_len_wer_chk':
				$data.="<th>Wear Time</th>";
				break;
				case 'cnt_len_sup_chk':
				$data.="<th>Supply</th>";
				break;
				case 'suply_mnt_chk':
				$data.="<th>Measurement</th>";
				break;
				case 'med_exp_chk':
				$data.="<th>Exp. Date</th>";
				break;
			}
		}
		if($wholesale==1)
		$data.="<th>Wholesale Price</th>";
		if($retail==1)
		$data.="<th>Retail Price</th>";
		
		$data.="<th>Qty</th><th>Lbl.</th>";
		$data.="</tr>";
		echo $data;
		
		$search_id= $_POST['id'];
		$manufacturer_Id_Srch=$_REQUEST['manuf_id'];
		$opt_vendor_id=$_REQUEST['vendor'];
		$opt_brand_id=$_REQUEST['brand'];
		$upcval = $_POST['upc_name'];
		$name_txt = $_POST['name_txt'];
		$color_id_search=$_REQUEST['color'];
		$shape_id_search=$_REQUEST['shape'];
		$style_id_search=$_REQUEST['style'];
		$price_frm=$_REQUEST['price_frm'];
		$price_to=$_REQUEST['price_to'];
		$item_qty=$_REQUEST['item_qty'];

		if($search_id<=0){
			$search_id=1;
		}
		
		$and="";
		$tbNameJoin="";
		if($opt_vendor_id!='' && $opt_vendor_id>0){
			$tb_field= ",VT.vendor_name";
			$and="And it.vendor_id='$opt_vendor_id'";
			$tbNameJoin= "LEFT join in_vendor_details as VT on VT.id = it.vendor_id";
		}
		if($opt_brand_id!='' && $opt_brand_id>0){
			$tb_field.= ",BT.frame_source";
			$and.=" And it.brand_id='$opt_brand_id'";
			$tbNameJoin.= " LEFT join in_frame_sources as BT on BT.id = it.brand_id";
		}
		if($upcval!=''){
			$and.="And it.upc_code like('$upcval%')";
		}
		
		if($name_txt!=''){
			$and.="And it.name like ('$name_txt%')";
		}
		
		if($manufacturer_Id_Srch!='' && $manufacturer_Id_Srch>0){
			$and.=" And it.manufacturer_id='$manufacturer_Id_Srch'";	
		}
		
		if($color_id_search!='' && $color_id_search>0){
			$and.=" And it.color='$color_id_search'";	
		}
		if($shape_id_search!='' && $shape_id_search>0){
			$and.=" And it.frame_shape='$shape_id_search'";	
		}
		if($style_id_search!='' && $style_id_search>0){
			$and.=" And it.frame_style='$style_id_search'";	
		}
		if($price_frm_search>0){
			$and.=" And it.retail_price>='$price_frm_search'";	
		}
		if($price_to_search>0){
			$and.=" And it.retail_price<='$price_to_search'";	
		}
		if($item_qty>0){
			$and.=" And it.qty_on_hand>='$item_qty'";	
		}
		
			$qry = "select it.*,
			FT.module_type_name,MT.manufacturer_name
			$tb_field
			from in_item  as it 
			LEFT join in_module_type as FT on FT.id = it.module_type_id
			LEFT join in_manufacturer_details as MT on MT.id = it.manufacturer_id
			$tbNameJoin
			where it.del_status='0' and it.module_type_id = '$search_id'
			".$and." order by it.upc_code asc, it.name asc";
		
			$query = imw_query($qry);
		
		
	//$query=imw_query("select * from in_item where module_type_id=".$id." and del_status='0'");
	
		while($row=imw_fetch_array($query)){
			$query1=imw_query("select * from in_vendor_details where id=".$row['vendor_id']."");
			if($query1 && imw_num_rows($query1)>0){
				$vendor=imw_fetch_array($query1);
				$printFields[$i]['ven_chk'] = $vendor['vendor_name'];
			}
			$query2=imw_query("select  * from in_module_type where id=".$row['module_type_id']."");
			if($query2 && imw_num_rows($query2)>0){
				$type=imw_fetch_array($query2);
				$printFields[$i]['type_chk'] = $type['module_type_name'];
			}
			$query3=imw_query("select * from in_manufacturer_details where id=".$row['manufacturer_id']."");
			if($query3 && imw_num_rows($query3)>0){
				$manufac=imw_fetch_array($query3);
				$printFields[$i]['mf_chk'] = $manufac['manufacturer_name'];
			}
			$lense_color = $query_brand = $type_mat = "";
			switch($row['module_type_id'])
			{
				case 1:
					$lense_color = "select * from in_frame_color where id in(".$row['color'].")";
					$query_brand = "select * from in_frame_sources where id=".$row['brand_id']."";
				break;
				case 2:
					$lense_color = "select * from in_lens_color where id in(".$row['color'].")";
					$type_mat="select * from in_lens_material where id=".$row['material_id'];
					$mat_type="select * from in_lens_type where id=".$row['type_id'];
				break;
				case 3:
					$lense_color = "select * from in_color where id in(".$row['color'].")";
					$query_brand = "select * from in_contact_brand where id=".$row['brand_id']."";
				break;
			}
			if($lense_color!=""){
				$d="";
				$lense_color1=imw_query($lense_color);
				while($color=imw_fetch_array($lense_color1))
				{
					$d.=($color['color_name']).",";
				}
				$printFields[$i]['colr_chk'] = rtrim($d,",");
			}
			if($query_brand!=""){
				$query_brand=imw_query($query_brand);
				$brand=imw_fetch_array($query_brand);
				$printFields[$i]['brnd_chk'] = (isset($brand['frame_source']))?$brand['frame_source']:$brand['brand_name'];
			}
			$query5=imw_query("select * from in_frame_shapes where id=".$row['frame_shape']."");
			if($query5 && imw_num_rows($query5)>0)
			{
				$frames_shape=imw_fetch_array($query5);
				$printFields[$i]['shp_chk'] = $frames_shape['shape_name'];
			}
			$query6=imw_query("select * from in_frame_styles where id=".$row['frame_style']."");
			if($query6 && imw_num_rows($query6)>0)
			{
				$frames_style=imw_fetch_array($query6);
				$printFields[$i]['styl_chk'] = $frames_style['style_name'];
			}
			if($mat_type!="")
			{
				$query7=imw_query($mat_type);
				$lens_focl=imw_fetch_array($query7);
				$printFields[$i]['lens_focl_chk'] = $lens_focl['type_name'];
			}
			if($type_mat!="")
			{
				$q=imw_query($type_mat);
				$lens_mate=imw_fetch_array($q);
				$printFields[$i]['lens_mate_chk'] = $lens_mate['material_name'];
			}
			$query9=imw_query("select * from in_lens_ar where id=".$row['a_r_id']."");
			if($query9 && imw_num_rows($query9)>0)
			{
				$lens_ar=imw_fetch_array($query9);
				$printFields[$i]['lens_a_r_chk'] = $lens_ar['ar_name'];
			}
			$query10=imw_query("select * from in_lens_transition where id=".$row['transition_id']."");
			if($query10 && imw_num_rows($query10)>0)
			{
				$lens_tran=imw_fetch_array($query10);
				$printFields[$i]['lens_tran_chk'] = $lens_tran['transition_name'];
			}
			$query11=imw_query("select * from in_lens_polarized where id=".$row['polarized_id']."");
			if($query11 && imw_num_rows($query11)>0)
			{
				$lens_pol=imw_fetch_array($query11);
				$printFields[$i]['lens_pol_chk'] = $lens_pol['polarized_name'];
			}
			$query12=imw_query("select * from in_lens_edge where id=".$row['edge_id']."");
			if($query12 && imw_num_rows($query12)>0)
			{
				$lens_edge=imw_fetch_array($query12);
				$printFields[$i]['lens_edge_chk'] = $lens_edge['edge_name'];
			}
			$query13=imw_query("select * from in_lens_tint where id=".$row['tint_id']."");
			if($query13 && imw_num_rows($query13)>0)
			{
				$lens_tint=imw_fetch_array($query13);
				$printFields[$i]['lens_tint_chk'] = $lens_tint['tint_type'];
			}
			$query14=imw_query("select * from in_contact_cat where id in (".$row['cl_wear_schedule'].")");
			if($query14 && imw_num_rows($query14)>0)
			{
				$e="";
				while($con_lens_wear=imw_fetch_array($query14))
				{
					$e.=($con_lens_wear['cat_name']).",";
				}
				$printFields[$i]['cnt_len_wer_chk'] = rtrim($e,",");
			}
			$query17=imw_query("select * from in_type where id in (".$row['type_id'].")");
			if($query17 && imw_num_rows($query17)>0)
			{
				$c="";
				while($con_lens_mat=imw_fetch_array($query17))
				{
					$c.=($con_lens_mat['type_name']).",";
				}
				$printFields[$i]['cnt_len_mat_chk'] = rtrim($c,",");
			}
			$query15=imw_query("select * from in_supply where id=".$row['supply_id']."");
			if($query15 && imw_num_rows($query15)>0)
			{
				$con_lens_sup=imw_fetch_array($query15);
				$printFields[$i]['cnt_len_sup_chk'] = $con_lens_sup['supply_name'];
			}
			$query16=imw_query("select * from in_supplies_measurment where id=".$row['measurment']."");
			if($query16 && imw_num_rows($query16)>0)
			{
				$measurement=imw_fetch_array($query16);
				$printFields[$i]['suply_mnt_chk'] = $measurement['measurment_name'];
			}
	 		$printFields[$i]['upc_chk'] = $row['upc_code'];
			$printFields[$i]['item_id'] = $row['id'];
			$printFields[$i]['gender_chk'] = $row['gender'];
			$printFields[$i]['wholesale_chk'] = $row['wholesale_cost'];
			$printFields[$i]['purchase_chk'] = $row['purchase_price'];
			$printFields[$i]['retail_chk'] = $row['retail_price'];
			$printFields[$i]['qnt_chk'] = $row['qty_on_hand'];
			$printFields[$i]['module_type_id'] = $row['module_type_id'];
			$printFields[$i]['manufacturer_id'] = $row['manufacturer_id'];
			$printFields[$i]['brand_id'] = $row['brand_id'];
			$printFields[$i]['frame_style'] = $row['frame_style'];
			$printFields[$i]['retail_price_flag'] = $row['retail_price_flag']; 
			$printFields[$i]['formula'] = $row['formula']; 
			$qty=0;
			if($arr_qty[$row['upc_code']]>0)$qty=$arr_qty[$row['upc_code']];
			else $qty=0;//$row['qty_on_hand']
			$printFields[$i]['qnt_lbl'] = '<input type="text" name="label[]" value="'.$qty.'" style="width:20px" class="labelCount" onkeyup="validate_qty(this)" />';
			$printFields[$i]['med_exp_chk'] = $row['expiry_date'];
			$i++;
		}
	if(count($printFields)>0){
		foreach($printFields as $field){
			
		$retail_price_markup_modules = array(1, 3, 5, 6);	/*List of module type id's for which retail price markup functionality is given*/
		$default_formula = $module_id=$brand_id=$manufacturer_id=$formula=$wholesale_chk=$purchase_chk=$style_id_search='';
		$module_id=$field['module_type_id'];
		$brand_id=$field['brand_id'];
		$manufacturer_id=$field['manufacturer_id'];
		$formula=$field['formula'];
		$wholesale_chk=$field['wholesale_chk'];
		$purchase_chk=$field['purchase_chk'];
		$style_id_search=$field['frame_style'];
			
		/*Get Default Formula for the item Type*/
		if( in_array($field['module_type_id'], $retail_price_markup_modules) ){
			if( $field['module_type_id']=='1' ){
				$default_formula = get_retail_formula($module_id, array('manufacturer_id'=>$manufacturer_id, 'brand_id'=>$brand_id, 'frame_style'=>$style_id_search));
			}
			else{
				$default_formula = get_retail_formula($module_id, array('manufacturer_id'=>$manufacturer_id, 'brand_id'=>$brand_id));
			}
		}
		/*End Get Default Formula for the item Type*/
		/*Retail Prices Markup - Caclulation*/
		if( in_array($module_id, $retail_price_markup_modules) && $field['retail_price_flag']=='0' ){

			if( trim($formula)=='' ){
				$formula = $default_formula;
			}

			/*Final Retail Price for the Item - based on formula calculation*/
			if( $formula!='' ){
				$field['retail_chk'] = calculate_markup_price($formula,  $wholesale_chk,  $purchase_chk);
			}
			/*End Final Retail Price for the Item*/
		}
		/*End Retail Prices Markup - Caclulation*/
			
		/*Dymo Label Printing Section*/
			# Common Fields
			$printUpc = "";
			$arrPrint = array();	
			if($field['upc_chk'])
				$printUpc=$field['upc_chk'];		//upc Number
			
			if($field['type_chk'] && in_array('type_chk',$allowed_vals))
				$arrPrint[2][0]=$field['type_chk'];		//type
			//if($field['p_name'] && in_array('p_name',$allowed_vals))
			//$arrPrint[2][1]=$field['p_name'];// product name
			if($field['brnd_chk'] && in_array('brnd_chk',$allowed_vals))
				$arrPrint[2][2]=$field['brnd_chk'];		//Brand Name
				
			#Fields Specific to FRAMES
			if($field['colr_chk'] && in_array('colr_chk',$allowed_vals))
				$arrPrint[3][0]=$field['colr_chk'];		//Frme Color
			if($field['styl_chk'] && in_array('styl_chk',$allowed_vals))
				$arrPrint[3][1]=$field['styl_chk'];		//Frame Style
			if($field['shp_chk'] && in_array('shp_chk',$allowed_vals))
				$arrPrint[3][2]=$field['shp_chk'];		//Frame Shape
			
			#Fields Specific to LENSES
			if(trim($field['lens_focl_chk']) && in_array('lens_focl_chk',$allowed_vals))
				$arrPrint[3][3]=$field['lens_focl_chk'];	//Lens Focal Type
			if(trim($field['lens_mate_chk']) && in_array('lens_mate_chk',$allowed_vals))
				$arrPrint[3][4]=$field['lens_mate_chk'];	//Lens Material
			if(trim($field['lens_a_r_chk']) && in_array('lens_a_r_chk',$allowed_vals))
				$arrPrint[3][5]=$field['lens_a_r_chk'];		//Lens A/R
			if(trim($field['lens_tran_chk']) && in_array('lens_tran_chk',$allowed_vals))
				$arrPrint[3][6]=$field['lens_tran_chk'];	//Lens Transition
			if(trim($field['lens_pol_chk']) && in_array('lens_pol_chk',$allowed_vals))
				$arrPrint[3][7]=$field['lens_pol_chk'];		//Lens Polarized
			if(trim($field['lens_edge_chk']) && in_array('lens_edge_chk',$allowed_vals))
				$arrPrint[3][8]=$field['lens_edge_chk'];	//Lens Edge
			if(trim($field['lens_tint_chk']) && in_array('lens_tint_chk',$allowed_vals))
				$arrPrint[3][9]=$field['lens_tint_chk'];	//Lens Tint
			
			#Fields Specific to CONTACT LENSES
			if(trim($field['cnt_len_mat_chk']) && in_array('cnt_len_mat_chk',$allowed_vals))
				$arrPrint[3][10]=$field['cnt_len_mat_chk'];	//Contact Lens Material
			if(trim($field['cnt_len_wer_chk']) && in_array('cnt_len_wer_chk',$allowed_vals))
				$arrPrint[3][11]=$field['cnt_len_wer_chk'];	//Contact Lens Wear Time
			if(trim($field['cnt_len_sup_chk']) && in_array('cnt_len_sup_chk',$allowed_vals))
				$arrPrint[3][12]=$field['cnt_len_sup_chk'];	//Contact Lens Suply
			
			#Field Specific to MEDICINE
			if(trim($field['med_exp_chk']) && $field['med_exp_chk']!='0000-00-00' && in_array('med_exp_chk',$allowed_vals))
				$arrPrint[3][13]=$field['med_exp_chk'];	//Contact Lens  Exp. Date
			
			#Fields Specific to SUPPLIES/ASSESSORIES
			if(trim($field['suply_mnt_chk']) && in_array('suply_mnt_chk',$allowed_vals))
				$arrPrint[3][14]=$field['suply_mnt_chk'];	//Supplies Measurement
				
			#COMMON Fields
			if($field['mf_chk'] && in_array('mf_chk',$allowed_vals))
				$arrPrint[4][0]=$field['mf_chk'];	//Manufacturer
			if($field['ven_chk'] && in_array('ven_chk',$allowed_vals))
				$arrPrint[4][1]=$field['ven_chk'];	//Vender		
			if($field['gender_chk'] && in_array('gender_chk',$allowed_vals))
				$arrPrint[5][0]=$field['gender_chk'];	//Gender
			if($field['wholesale_chk'] && in_array('wholesale_chk',$allowed_vals))
				$arrPrint[5][1]= currency_symbol(true)." ".$field['wholesale_chk'];	//Wholesale Cost
			if($field['retail_chk'] && in_array('retail_chk',$allowed_vals))
				$arrPrint[5][2]= currency_symbol(true)." ".$field['retail_chk'];//Retail Price
			$printingData = "";	#Container to Hold Data for Dymo Printing
			foreach($arrPrint as $val){
				if(sizeof($val)>4){
					$data.="<div class='data_row'>";
					foreach($val as $subval){	
						$cntr++;
						$printingData.=$subval."-";
						if($cntr==4){
							if(substr($printingData,strlen($printingData)-1,1)=='-')
							$printingData=substr($printingData,0,strlen($printingData)-1);
							
							$cntr=0;
							$$printingData.="<br />";	
						}
					}
					if(substr($printingData,strlen($printingData)-1,1)=='-')
					$printingData=substr($printingData,0,strlen($printingData)-1);
					
					$printingData.="<br />";
				}
				else
					$printingData.=implode('-',$val)."<br />";
			}
			$printingData = preg_replace('/<br \/>$/', '', $printingData);
		/*End Dymo Label Printing Section*/
		
			$lblIncluded=0;
			$wholesale=0;
			$retail=0;
			echo "<tr>";
				echo "<input type='hidden' name='upc_code[]' value='".$field['upc_chk']."'>";
				echo '<td><input type="checkbox" name="selectedrecords[]" class="rowCheck" value="'.$field['item_id'].'"/>';
				echo '<span class="printing_upc">'.$printUpc."</span>";
				echo '<span class="printing_data">'.$printingData."</span>";
				echo '</td>';
				
			foreach($allowed_vals as $val){
				
				if($val=='wholesale_chk')
				$wholesale=1;
				elseif($val=='retail_chk')
				$retail=1;
				else
				echo "<td>".$field[$val]."</td>";
			}
			if($wholesale==1)
			echo "<td>".$field['wholesale_chk']."</td>";
			if($retail==1)
			echo "<td>".$field['retail_chk']."</td>";
			
			echo "<td>".$field['qnt_chk']."</td><td>".$field['qnt_lbl']."</td>";
			echo "</tr>";
		}
	}
}

if($_REQUEST['upc_code']!="" && $_REQUEST['module']!="")
{
	$i=1;
	$printFields = array();
	$upc=rtrim($_REQUEST['upc_code'],","); 
	$upc_arr=explode(",",$upc); 
	$name="";
	$data="";
	$module=$_REQUEST['module'];
	$allowed_vals = array();
	$allowed_vals[] ='p_name';
	$status_check=imw_query("select * from in_print_option_stock where module_id=".$module." and status=1");
	while($row1=imw_fetch_array($status_check))
	{
		$allowed_vals[]=$row1['option_chk'];
	}
	foreach($upc_arr as $upc)
	{
		
	$query=imw_query("select * from in_item where module_type_id=".$module." and id='".$upc."' and del_status='0'");
	while($row=imw_fetch_array($query)){
			
	/***********************************bar code gen**********************************/		
			include_once('../../../library/bar_code/code128/code128.class.php');
			$barcode = new phpCode128("'".$row['upc_code']."'", 150, '', '');
			$barcode->setBorderWidth(0);
			$barcode->setBorderSpacing(0);
			$barcode->setPixelWidth(1);
			$barcode->setEanStyle(false);
			$barcode->setShowText(false);
			$barcode->setAutoAdjustFontSize(true);
			$barcode->setTextSpacing(10);
			
			if(!is_dir(dirname(__FILE__)."/../../../images/bar_codes")){
				mkdir(dirname(__FILE__)."/../../../images/bar_codes", 0777, true);
			}
			$name=dirname(__FILE__)."/../../../images/bar_codes/".$row['upc_code'].".png";
			$barcode->saveBarcode($name);
	/***********************************bar code gen**********************************/		
			
			$query1=imw_query("select * from in_vendor_details where id=".$row['vendor_id']."");
			if($query1 && imw_num_rows($query1)>0){
				$vendor=imw_fetch_array($query1);
				$printFields[$i]['ven_chk'] = $vendor['vendor_name'];
			}
			$query2=imw_query("select  * from in_module_type where id=".$row['module_type_id']."");
			if($query2 && imw_num_rows($query2)>0){
				$type=imw_fetch_array($query2);
				$printFields[$i]['type_chk'] = $type['module_type_name'];
			}
			$query3=imw_query("select * from in_manufacturer_details where id=".$row['manufacturer_id']."");
			if($query3 && imw_num_rows($query3)>0){
				$manufac=imw_fetch_array($query3);
				$printFields[$i]['mf_chk'] = $manufac['manufacturer_name'];
			}
			$lense_color = $query_brand = "";
			switch($row['module_type_id'])
			{
				case 1:
					$lense_color = "select * from in_frame_color where id in(".$row['color'].")";
					$query_brand = "select * from in_frame_sources where id=".$row['brand_id']."";
				break;
				case 2:
					$lense_color = "select * from in_lens_color where id in(".$row['color'].")";
					$type_mat="select * from in_lens_material where id=".$row['material_id'];

					$mat_type="select * from in_lens_type where id=".$row['type_id'];
				break;
				case 3:
					$lense_color = "select * from in_color where id in(".$row['color'].")";
					$query_brand = "select * from in_contact_brand where id=".$row['brand_id']."";
				break;
			}
			if($lense_color!=""){
				$d="";
				$lense_color1=imw_query($lense_color);
				while($color=imw_fetch_array($lense_color1))
				{
					$d.=($color['color_name']).",";
				}
				$printFields[$i]['colr_chk'] = rtrim($d,",");
			}
			if($query_brand!=""){
				$query_brand=imw_query($query_brand);
				$brand=imw_fetch_array($query_brand);
				$printFields[$i]['brnd_chk'] = (isset($brand['frame_source']))?$brand['frame_source']:$brand['brand_name'];
			}
			$query5=imw_query("select * from in_frame_shapes where id=".$row['frame_shape']."");
			if($query5 && imw_num_rows($query5)>0)
			{
				$frames_shape=imw_fetch_array($query5);
				$printFields[$i]['shp_chk'] = $frames_shape['shape_name'];
			}
			$query6=imw_query("select * from in_frame_styles where id=".$row['frame_style']."");
			if($query6 && imw_num_rows($query6)>0)
			{
				$frames_style=imw_fetch_array($query6);
				$printFields[$i]['styl_chk'] = $frames_style['style_name'];
			}
			if($mat_type!="")
			{
				$query7=imw_query($type_mat);
				$lens_focl=imw_fetch_array($query7);
				$printFields[$i]['lens_focl_chk'] = $lens_focl['type_name'];
			}
			if($type_mat!="")
			{
				$q=imw_query($type_mat);
				$lens_mate=imw_fetch_array($q);
				$printFields[$i]['lens_mate_chk'] = (isset($lens_mate['material_name']))?$lens_mate['material_name']:$lens_mate['type_name'];
			}
			$query9=imw_query("select * from in_lens_ar where id=".$row['a_r_id']."");
			if($query9 && imw_num_rows($query9)>0)
			{
				$lens_ar=imw_fetch_array($query9);
				$printFields[$i]['lens_a_r_chk'] = $lens_ar['ar_name'];
			}
			$query10=imw_query("select * from in_lens_transition where id=".$row['transition_id']."");
			if($query10 && imw_num_rows($query10)>0)
			{
				$lens_tran=imw_fetch_array($query10);
				$printFields[$i]['lens_tran_chk'] = $lens_tran['transition_name'];
			}
			$query11=imw_query("select * from in_lens_polarized where id=".$row['polarized_id']."");
			if($query11 && imw_num_rows($query11)>0)
			{
				$lens_pol=imw_fetch_array($query11);
				$printFields[$i]['lens_pol_chk'] = $lens_pol['polarized_name'];
			}
			$query12=imw_query("select * from in_lens_edge where id=".$row['edge_id']."");
			if($query12 && imw_num_rows($query12)>0)
			{
				$lens_edge=imw_fetch_array($query12);
				$printFields[$i]['lens_edge_chk'] = $lens_edge['edge_name'];
			}
			$query13=imw_query("select * from in_lens_tint where id=".$row['tint_id']."");
			if($query13 && imw_num_rows($query13)>0)
			{
				$lens_tint=imw_fetch_array($query13);
				$printFields[$i]['lens_tint_chk'] = $lens_tint['tint_type'];
			}
			$query14=imw_query("select * from in_contact_cat where id in (".$row['cl_wear_schedule'].")");
			if($query14 && imw_num_rows($query14)>0)
			{
				$e="";
				while($con_lens_wear=imw_fetch_array($query14))
				{
					$e.=($con_lens_wear['cat_name']).",";
				}
				$printFields[$i]['cnt_len_wer_chk'] = rtrim($e,",");
			}
			$query17=imw_query("select * from in_type where id in (".$row['type_id'].")");
			if($query17 && imw_num_rows($query17)>0)
			{
				$c="";
				while($con_lens_mat=imw_fetch_array($query17))
				{
					$c.=($con_lens_mat['type_name']).",";
				}
				$printFields[$i]['cnt_len_mat_chk'] = rtrim($c,",");
			}
			$query15=imw_query("select * from in_supply where id=".$row['supply_id']."");
			if($query15 && imw_num_rows($query15)>0)
			{
				$con_lens_sup=imw_fetch_array($query15);
				$printFields[$i]['cnt_len_sup_chk'] = $con_lens_sup['supply_name'];
			}
			$query16=imw_query("select * from in_supplies_measurment where id=".$row['measurment']."");
			if($query16 && imw_num_rows($query16)>0)
			{
				$measurement=imw_fetch_array($query16);
				$printFields[$i]['suply_mnt_chk'] = $measurement['measurment_name'];
			}
			$printFields[$i]['upc_chk'] = "<img src='../../images/bar_codes/".$row['upc_code'].".png' class='upc_img'><br>".$row['upc_code'];
			$printFields[$i]['gender_chk'] = $row['gender'];
			$printFields[$i]['wholesale_chk'] = $row['wholesale_cost'];
			$printFields[$i]['retail_chk'] = $row['retail_price'];
			$printFields[$i]['qnt_chk'] = $row['qty_on_hand'];
			$printFields[$i]['med_exp_chk'] = $row['expiry_date'];
			$printFields[$i]['p_name'] = $row['name'];
			$i++;
		}
	
	}
	
	if(count($printFields)>0){
		
		$data='';
		foreach($printFields as $field){
			//$data.='<div class="label_div"><div class="parent_main"><div class="p_name all_class1">Upc Code</div>';
			//$data.="<div class='p_named all_class'>".$field['upc_chk']."</div></div>";
			$data.='<page backtop="0mm" backbottom="0mm">';
			if($field['upc_chk'])
			$arrPrint[0][0]=$field['upc_chk'];//upc bar code
			if($field['upc_chk--'])
			$arrPrint[1][0]='';//upc number
			if($field['type_chk'] && in_array('type_chk',$allowed_vals))
			$arrPrint[2][0]=$field['type_chk'];//type
			//if($field['p_name'] && in_array('p_name',$allowed_vals))
			//$arrPrint[2][1]=$field['p_name'];// product name
			if($field['brnd_chk'] && in_array('brnd_chk',$allowed_vals))
			$arrPrint[2][2]=$field['brnd_chk'];//brand
			#FRAMES
			if($field['colr_chk'] && in_array('colr_chk',$allowed_vals))
			$arrPrint[3][0]=$field['colr_chk'];//color
			if($field['styl_chk'] && in_array('styl_chk',$allowed_vals))
			$arrPrint[3][1]=$field['styl_chk'];//frame style
			if($field['shp_chk'] && in_array('shp_chk',$allowed_vals))
			$arrPrint[3][2]=$field['shp_chk'];//frame shape
			#LENSES
			if(trim($field['lens_focl_chk']) && in_array('lens_focl_chk',$allowed_vals))
			$arrPrint[3][3]=$field['lens_focl_chk'];//focal type
			if(trim($field['lens_mate_chk']) && in_array('lens_mate_chk',$allowed_vals))
			$arrPrint[3][4]=$field['lens_mate_chk'];//material
			if(trim($field['lens_a_r_chk']) && in_array('lens_a_r_chk',$allowed_vals))
			$arrPrint[3][5]=$field['lens_a_r_chk'];//A/R
			if(trim($field['lens_tran_chk']) && in_array('lens_tran_chk',$allowed_vals))
			$arrPrint[3][6]=$field['lens_tran_chk'];//Transition
			if(trim($field['lens_pol_chk']) && in_array('lens_pol_chk',$allowed_vals))
			$arrPrint[3][7]=$field['lens_pol_chk'];//Polarized
			if(trim($field['lens_edge_chk']) && in_array('lens_edge_chk',$allowed_vals))
			$arrPrint[3][8]=$field['lens_edge_chk'];//Edge
			if(trim($field['lens_tint_chk']) && in_array('lens_tint_chk',$allowed_vals))
			$arrPrint[3][9]=$field['lens_tint_chk'];//Tint
			#CONTACT LENSES
			if(trim($field['cnt_len_mat_chk']) && in_array('cnt_len_mat_chk',$allowed_vals))
			$arrPrint[3][10]=$field['cnt_len_mat_chk'];//material
			if(trim($field['cnt_len_wer_chk']) && in_array('cnt_len_wer_chk',$allowed_vals))
			$arrPrint[3][11]=$field['cnt_len_wer_chk'];//wear time
			if(trim($field['cnt_len_sup_chk']) && in_array('cnt_len_sup_chk',$allowed_vals))
			$arrPrint[3][12]=$field['cnt_len_sup_chk'];//suply
			#MEDICINE
			if(trim($field['med_exp_chk']) && $field['med_exp_chk']!='0000-00-00' && in_array('med_exp_chk',$allowed_vals))
			$arrPrint[3][13]=$field['med_exp_chk'];//exp date
			#SUPPLIES/ASSESSORIES
			if(trim($field['suply_mnt_chk']) && in_array('suply_mnt_chk',$allowed_vals))
			$arrPrint[3][14]=$field['suply_mnt_chk'];//Measurement
			#COMMON
			if($field['mf_chk'] && in_array('mf_chk',$allowed_vals))
			$arrPrint[4][0]=$field['mf_chk'];//manufacturer
			if($field['ven_chk'] && in_array('ven_chk',$allowed_vals))
			$arrPrint[4][1]=$field['ven_chk'];//vender
			
			if($field['gender_chk'] && in_array('gender_chk',$allowed_vals))
			$arrPrint[5][0]=$field['gender_chk'];//gender
			if($field['wholesale_chk'] && in_array('wholesale_chk',$allowed_vals))
			$arrPrint[5][1]= currency_symbol(true)." ".$field['wholesale_chk'];//wholesale cost
			if($field['retail_chk'] && in_array('retail_chk',$allowed_vals))
			$arrPrint[5][2]= currency_symbol(true)." ".$field['retail_chk'];//retail price
			
			//qnt_chk(QTY),prc_chk(PRICE) not included
			$container++;
			$data.="<div class='container'>";
			foreach($arrPrint as $val){
				if(sizeof($val)>4)
				{
					$data.="<div class='data_row'>";
					foreach($val as $subval)
					{	
						$cntr++;
						$data.=$subval."-";
						if($cntr==4)
						{
							if(substr($data,strlen($data)-1,1)=='-')
							$data=substr($data,0,strlen($data)-1);
							
							$cntr=0;
							$data.="</div><div class='data_row'>";	
						}
					}
					if(substr($data,strlen($data)-1,1)=='-')
					$data=substr($data,0,strlen($data)-1);
					
					$data.="</div>";
				}else $data.="<div class='data_row'>".implode('-',$val)."</div>";
			}
			$data.="</div>";
			if($container%2==0){
				//$data.="</td></tr></table><table><tr><td>";
			}
			$data.="</page>";
		}
}
	//$header='<page backtop="10mm" backbottom="0mm">';
	
	if(file_put_contents('../../../library/new_html2pdf/print_data.html',($data.$css)))
	{
		echo 1;
	}
	else
	{
		echo 0;
	}
}

if($_REQUEST['reconcile']==1)
{	
	$printFields = array();
	$i=1;
	
	$allowed_vals = array();	
	$status_check=imw_query("select * from in_print_option_stock where status=1 order by module_id asc");
	
	$headerStart='
<tr class="listheading" style="background: rgb(218,237,251);
background: -moz-linear-gradient(top,  rgba(218,237,251,1) 0%, rgba(217,237,248,1) 8%, rgba(200,229,245,1) 67%, rgba(197,224,241,1) 79%, rgba(194,226,241,1) 88%, rgba(194,223,241,1) 100%);
background: -webkit-linear-gradient(top,  rgba(218,237,251,1) 0%,rgba(217,237,248,1) 8%,rgba(200,229,245,1) 67%,rgba(197,224,241,1) 79%,rgba(194,226,241,1) 88%,rgba(194,223,241,1) 100%);
background: linear-gradient(to bottom,  rgba(218,237,251,1) 0%,rgba(217,237,248,1) 8%,rgba(200,229,245,1) 67%,rgba(197,224,241,1) 79%,rgba(194,226,241,1) 88%,rgba(194,223,241,1) 100%);">';
			$included=0;
			$wholesale=0;
			$retail=0;
		while($row1=imw_fetch_array($status_check))
		{
			$allowed_vals[$row1['module_id']][]=$row1['option_chk'];
			switch($row1['option_chk'])
			{
				case 'upc_chk':
				$header[$row1['module_id']][]="<th>Upc Code</th>";
				break;
				case 'mf_chk':
				$header[$row1['module_id']][]="<th>Manufacturer</th>";
				break;
				case 'type_chk':
				$header[$row1['module_id']][]="<th>Type</th>";
				break;
				case 'ven_chk':
				$header[$row1['module_id']][]="<th>Vendor</th>";
				break;
				case 'brnd_chk':
				$header[$row1['module_id']][]="<th>Brand</th>";
				break;
				case 'colr_chk':
				$header[$row1['module_id']][]="<th>Color</th>";
				break;
				case 'prc_chk':
				$header[$row1['module_id']][]="<th>Price</th>";
				break;
				case 'shp_chk':
				$header[$row1['module_id']][]="<th>Frame Shape</th>";
				break;
				case 'styl_chk':
				$header[$row1['module_id']][]="<th>Frame Style</th>";
				break;
				case 'lens_focl_chk':
				$header[$row1['module_id']][]="<th>Seg Type</th>";
				break;
				case 'lens_mate_chk':
				$header[$row1['module_id']][]="<th>Material</th>";
				break;
				case 'lens_a_r_chk':
				$header[$row1['module_id']][]="<th>A/R</th>";
				break;
				case 'lens_tran_chk':
				$header[$row1['module_id']][]="<th>Transition</th>";
				break;
				case 'lens_pol_chk':
				$header[$row1['module_id']][]="<th>Polarized</th>";
				break;
				case 'wholesale_chk':
				$wholesale=1;
				break;
				case 'retail_chk':
				$retail=1;
				break;
				case 'gender_chk':
				$header[$row1['module_id']][]="<th>Gender</th>";
				break;
				case 'lens_edge_chk':
				$header[$row1['module_id']][]="<th>Edge</th>";
				break;
				case 'lens_tint_chk':
				$header[$row1['module_id']][]="<th>Tint</th>";
				break;
				case 'cnt_len_mat_chk':
				$header[$row1['module_id']][]="<th>Material</th>";
				break;
				case 'cnt_len_wer_chk':
				$header[$row1['module_id']][]="<th>Wear Time</th>";
				break;
				case 'cnt_len_sup_chk':
				$header[$row1['module_id']][]="<th>Supply</th>";
				break;
				case 'suply_mnt_chk':
				$header[$row1['module_id']][]="<th>Measurement</th>";
				break;
				case 'med_exp_chk':
				$header[$row1['module_id']][]="<th>Exp. Date</th>";
				break;
			}
		
		}
		if($wholesale==1)
		$headerEnd.="<th>Wholesale Price</th>";
		if($retail==1)
		$headerEnd.="<th>Retail Price</th>";
		
		$headerEnd.="<th>Qty</th><th>Lbl.</th>";
		$headerEnd.="</tr>";
		
		//$headerArr[$row1['module_id']]= $headerStart.$headerEnd;
		
		$upcval = explode(',',$_POST['upc_name']);
		$upcStr="'".implode("','",$upcval)."'";

		$upc_qty = explode(',',$_POST['upc_qty']);
		for($i=0;$i<=sizeof($upcval);$i++)
		{
			$arr_qty[$upcval[$i]]=$upc_qty[$i];
		}
		if($search_id<=0){
			$search_id=1;
		}
		
		$where="";
		$tbNameJoin="";
		
		if($upcval!=''){
			$where.="And it.upc_code IN($upcStr)";
		}
		
			$qry = "select it.*,
			FT.module_type_name,MT.manufacturer_name
			from in_item  as it 
			LEFT join in_module_type as FT on FT.id = it.module_type_id
			LEFT join in_manufacturer_details as MT on MT.id = it.manufacturer_id
			where it.del_status='0' $where order by it.module_type_id, it.upc_code asc, it.name asc";
		
			$query = imw_query($qry);
		
		
	//$query=imw_query("select * from in_item where module_type_id=".$id." and del_status='0'");
	
		while($row=imw_fetch_array($query)){
			$query1=imw_query("select * from in_vendor_details where id=".$row['vendor_id']."");
			if($query1 && imw_num_rows($query1)>0){
				$vendor=imw_fetch_array($query1);
				$printFields[$i]['ven_chk'] = $vendor['vendor_name'];
			}
			$query2=imw_query("select  * from in_module_type where id=".$row['module_type_id']."");
			if($query2 && imw_num_rows($query2)>0){
				$type=imw_fetch_array($query2);
				$printFields[$i]['type_chk'] = $type['module_type_name'];
			}
			$query3=imw_query("select * from in_manufacturer_details where id=".$row['manufacturer_id']."");
			if($query3 && imw_num_rows($query3)>0){
				$manufac=imw_fetch_array($query3);
				$printFields[$i]['mf_chk'] = $manufac['manufacturer_name'];
			}
			$lense_color = $query_brand = $type_mat = "";
			switch($row['module_type_id'])
			{
				case 1:
					$lense_color = "select * from in_frame_color where id in(".$row['color'].")";
					$query_brand = "select * from in_frame_sources where id=".$row['brand_id']."";
				break;
				case 2:
					$lense_color = "select * from in_lens_color where id in(".$row['color'].")";
					$type_mat="select * from in_lens_material where id=".$row['material_id'];
					$mat_type="select * from in_lens_type where id=".$row['type_id'];
				break;
				case 3:
					$lense_color = "select * from in_color where id in(".$row['color'].")";
					$query_brand = "select * from in_contact_brand where id=".$row['brand_id']."";
				break;
			}
			if($lense_color!=""){
				$d="";
				$lense_color1=imw_query($lense_color);
				while($color=imw_fetch_array($lense_color1))
				{
					$d.=($color['color_name']).",";
				}
				$printFields[$i]['colr_chk'] = rtrim($d,",");
			}
			if($query_brand!=""){
				$query_brand=imw_query($query_brand);
				$brand=imw_fetch_array($query_brand);
				$printFields[$i]['brnd_chk'] = (isset($brand['frame_source']))?$brand['frame_source']:$brand['brand_name'];
			}
			$query5=imw_query("select * from in_frame_shapes where id=".$row['frame_shape']."");
			if($query5 && imw_num_rows($query5)>0)
			{
				$frames_shape=imw_fetch_array($query5);
				$printFields[$i]['shp_chk'] = $frames_shape['shape_name'];
			}
			$query6=imw_query("select * from in_frame_styles where id=".$row['frame_style']."");
			if($query6 && imw_num_rows($query6)>0)
			{
				$frames_style=imw_fetch_array($query6);
				$printFields[$i]['styl_chk'] = $frames_style['style_name'];
			}
			if($mat_type!="")
			{
				$query7=imw_query($mat_type);
				$lens_focl=imw_fetch_array($query7);
				$printFields[$i]['lens_focl_chk'] = $lens_focl['type_name'];
			}
			if($type_mat!="")
			{
				$q=imw_query($type_mat);
				$lens_mate=imw_fetch_array($q);
				$printFields[$i]['lens_mate_chk'] = $lens_mate['material_name'];
			}
			$query9=imw_query("select * from in_lens_ar where id=".$row['a_r_id']."");
			if($query9 && imw_num_rows($query9)>0)
			{
				$lens_ar=imw_fetch_array($query9);
				$printFields[$i]['lens_a_r_chk'] = $lens_ar['ar_name'];
			}
			$query10=imw_query("select * from in_lens_transition where id=".$row['transition_id']."");
			if($query10 && imw_num_rows($query10)>0)
			{
				$lens_tran=imw_fetch_array($query10);
				$printFields[$i]['lens_tran_chk'] = $lens_tran['transition_name'];
			}
			$query11=imw_query("select * from in_lens_polarized where id=".$row['polarized_id']."");
			if($query11 && imw_num_rows($query11)>0)
			{
				$lens_pol=imw_fetch_array($query11);
				$printFields[$i]['lens_pol_chk'] = $lens_pol['polarized_name'];
			}
			$query12=imw_query("select * from in_lens_edge where id=".$row['edge_id']."");
			if($query12 && imw_num_rows($query12)>0)
			{
				$lens_edge=imw_fetch_array($query12);
				$printFields[$i]['lens_edge_chk'] = $lens_edge['edge_name'];
			}
			$query13=imw_query("select * from in_lens_tint where id=".$row['tint_id']."");
			if($query13 && imw_num_rows($query13)>0)
			{
				$lens_tint=imw_fetch_array($query13);
				$printFields[$i]['lens_tint_chk'] = $lens_tint['tint_type'];
			}
			$query14=imw_query("select * from in_contact_cat where id in (".$row['cl_wear_schedule'].")");
			if($query14 && imw_num_rows($query14)>0)
			{
				$e="";
				while($con_lens_wear=imw_fetch_array($query14))
				{
					$e.=($con_lens_wear['cat_name']).",";
				}
				$printFields[$i]['cnt_len_wer_chk'] = rtrim($e,",");
			}
			$query17=imw_query("select * from in_type where id in (".$row['type_id'].")");
			if($query17 && imw_num_rows($query17)>0)
			{
				$c="";
				while($con_lens_mat=imw_fetch_array($query17))
				{
					$c.=($con_lens_mat['type_name']).",";
				}
				$printFields[$i]['cnt_len_mat_chk'] = rtrim($c,",");
			}
			$query15=imw_query("select * from in_supply where id=".$row['supply_id']."");
			if($query15 && imw_num_rows($query15)>0)
			{
				$con_lens_sup=imw_fetch_array($query15);
				$printFields[$i]['cnt_len_sup_chk'] = $con_lens_sup['supply_name'];
			}
			$query16=imw_query("select * from in_supplies_measurment where id=".$row['measurment']."");
			if($query16 && imw_num_rows($query16)>0)
			{
				$measurement=imw_fetch_array($query16);
				$printFields[$i]['suply_mnt_chk'] = $measurement['measurment_name'];
			}
	 		$printFields[$i]['module_type_id'] = $row['module_type_id'];
	 		$printFields[$i]['upc_chk'] = $row['upc_code'];
			$printFields[$i]['item_id'] = $row['id'];
			$printFields[$i]['gender_chk'] = $row['gender'];
			$printFields[$i]['wholesale_chk'] = $row['wholesale_cost'];
			$printFields[$i]['retail_chk'] = $row['retail_price'];
			$printFields[$i]['qnt_chk'] = $row['qty_on_hand'];
			$printFields[$i]['name_chk'] = $row['name'];
			$qty=0;
			if($arr_qty[$row['upc_code']]>0)$qty=$arr_qty[$row['upc_code']];
			else $qty=0;//$row['qty_on_hand']
			$printFields[$i]['qnt_lbl'] = '<input type="text" name="label[]" value="'.$qty.'" style="width:95%" class="labelCount" onkeyup="validate_qty(this)" />';
			$printFields[$i]['med_exp_chk'] = $row['expiry_date'];
			$i++;
		}
	if(count($printFields)>0){
		echo'
		<style>
		table {
			border-collapse: collapse;
			border: 1px solid #DCDCDC;
		}
		</style>
		<table class="print_table" border="1" cellpadding="2" cellspacing="0" width="100%" style="margin: 0px 0 0 0;"><tbody>
		<tr class="listheading" style="background: rgb(218,237,251);
		background: -moz-linear-gradient(top,  rgba(218,237,251,1) 0%, rgba(217,237,248,1) 8%, rgba(200,229,245,1) 67%, rgba(197,224,241,1) 79%, rgba(194,226,241,1) 88%, rgba(194,223,241,1) 100%);
		background: -webkit-linear-gradient(top,  rgba(218,237,251,1) 0%,rgba(217,237,248,1) 8%,rgba(200,229,245,1) 67%,rgba(197,224,241,1) 79%,rgba(194,226,241,1) 88%,rgba(194,223,241,1) 100%);
		background: linear-gradient(to bottom,  rgba(218,237,251,1) 0%,rgba(217,237,248,1) 8%,rgba(200,229,245,1) 67%,rgba(197,224,241,1) 79%,rgba(194,226,241,1) 88%,rgba(194,223,241,1) 100%);">
		<th>Upc Code</th>
		<th>Type</th>
		<th>Name</th>
		<th>Qty</th>
		<th style="width:100px">Lbl. Print</th></tr>';
		foreach($printFields as $field){
		/*Dymo Label Printing Section*/
			# Common Fields
			$printUpc = "";
			$arrPrint = array();
			$allowed_vals_sub=$allowed_vals[$field['module_type_id']];
			if($field['upc_chk'])
				$printUpc=$field['upc_chk'];		//upc Number
			
			if($field['type_chk'] && in_array('type_chk',$allowed_vals_sub))
				$arrPrint[2][0]=$field['type_chk'];		//type
			//if($field['p_name'] && in_array('p_name',$allowed_vals_sub))
			//$arrPrint[2][1]=$field['p_name'];// product name
			if($field['brnd_chk'] && in_array('brnd_chk',$allowed_vals_sub))
				$arrPrint[2][2]=$field['brnd_chk'];		//Brand Name
				
			#Fields Specific to FRAMES
			if($field['colr_chk'] && in_array('colr_chk',$allowed_vals_sub))
				$arrPrint[3][0]=$field['colr_chk'];		//Frme Color
			if($field['styl_chk'] && in_array('styl_chk',$allowed_vals_sub))
				$arrPrint[3][1]=$field['styl_chk'];		//Frame Style
			if($field['shp_chk'] && in_array('shp_chk',$allowed_vals_sub))
				$arrPrint[3][2]=$field['shp_chk'];		//Frame Shape
			
			#Fields Specific to LENSES
			if(trim($field['lens_focl_chk']) && in_array('lens_focl_chk',$allowed_vals_sub))
				$arrPrint[3][3]=$field['lens_focl_chk'];	//Lens Focal Type
			if(trim($field['lens_mate_chk']) && in_array('lens_mate_chk',$allowed_vals_sub))
				$arrPrint[3][4]=$field['lens_mate_chk'];	//Lens Material
			if(trim($field['lens_a_r_chk']) && in_array('lens_a_r_chk',$allowed_vals_sub))
				$arrPrint[3][5]=$field['lens_a_r_chk'];		//Lens A/R
			if(trim($field['lens_tran_chk']) && in_array('lens_tran_chk',$allowed_vals_sub))
				$arrPrint[3][6]=$field['lens_tran_chk'];	//Lens Transition
			if(trim($field['lens_pol_chk']) && in_array('lens_pol_chk',$allowed_vals_sub))
				$arrPrint[3][7]=$field['lens_pol_chk'];		//Lens Polarized
			if(trim($field['lens_edge_chk']) && in_array('lens_edge_chk',$allowed_vals_sub))
				$arrPrint[3][8]=$field['lens_edge_chk'];	//Lens Edge
			if(trim($field['lens_tint_chk']) && in_array('lens_tint_chk',$allowed_vals_sub))
				$arrPrint[3][9]=$field['lens_tint_chk'];	//Lens Tint
			
			#Fields Specific to CONTACT LENSES
			if(trim($field['cnt_len_mat_chk']) && in_array('cnt_len_mat_chk',$allowed_vals_sub))
				$arrPrint[3][10]=$field['cnt_len_mat_chk'];	//Contact Lens Material
			if(trim($field['cnt_len_wer_chk']) && in_array('cnt_len_wer_chk',$allowed_vals_sub))
				$arrPrint[3][11]=$field['cnt_len_wer_chk'];	//Contact Lens Wear Time
			if(trim($field['cnt_len_sup_chk']) && in_array('cnt_len_sup_chk',$allowed_vals_sub))
				$arrPrint[3][12]=$field['cnt_len_sup_chk'];	//Contact Lens Suply
			
			#Field Specific to MEDICINE
			if(trim($field['med_exp_chk']) && $field['med_exp_chk']!='0000-00-00' && in_array('med_exp_chk',$allowed_vals_sub))
				$arrPrint[3][13]=$field['med_exp_chk'];	//Contact Lens  Exp. Date
			
			#Fields Specific to SUPPLIES/ASSESSORIES
			if(trim($field['suply_mnt_chk']) && in_array('suply_mnt_chk',$allowed_vals_sub))
				$arrPrint[3][14]=$field['suply_mnt_chk'];	//Supplies Measurement
				
			#COMMON Fields
			if($field['mf_chk'] && in_array('mf_chk',$allowed_vals_sub))
				$arrPrint[4][0]=$field['mf_chk'];	//Manufacturer
			if($field['ven_chk'] && in_array('ven_chk',$allowed_vals_sub))
				$arrPrint[4][1]=$field['ven_chk'];	//Vender		
			if($field['gender_chk'] && in_array('gender_chk',$allowed_vals_sub))
				$arrPrint[5][0]=$field['gender_chk'];	//Gender
			if($field['wholesale_chk'] && in_array('wholesale_chk',$allowed_vals_sub))
				$arrPrint[5][1]= currency_symbol(true)." ".$field['wholesale_chk'];	//Wholesale Cost
			if($field['retail_chk'] && in_array('retail_chk',$allowed_vals_sub))
				$arrPrint[5][2]= currency_symbol(true)." ".$field['retail_chk'];//Retail Price
			$printingData = "";	#Container to Hold Data for Dymo Printing
			foreach($arrPrint as $val){
				if(sizeof($val)>4){
					$data.="<div class='data_row'>";
					foreach($val as $subval){	
						$cntr++;
						$printingData.=$subval."-";
						if($cntr==4){
							if(substr($printingData,strlen($printingData)-1,1)=='-')
							$printingData=substr($printingData,0,strlen($printingData)-1);
							
							$cntr=0;
							$$printingData.="<br />";	
						}
					}
					if(substr($printingData,strlen($printingData)-1,1)=='-')
					$printingData=substr($printingData,0,strlen($printingData)-1);
					
					$printingData.="<br />";
				}
				else
					$printingData.=implode('-',$val)."<br />";
			}
			$printingData = preg_replace('/<br \/>$/', '', $printingData);
		/*End Dymo Label Printing Section*/
			$lblIncluded=0;
			$wholesale=0;
			$retail=0;
			//start table here
			
			echo "<tr>";
			echo "<td>".$field['upc_chk']."</td>";
			echo "<td>".$field['type_chk']."</td>";
			echo "<td>".$field['name_chk']."</td>";
			
			echo "<td>".$field['qnt_chk']."</td><td>".$field['qnt_lbl'];
			echo "<input type='hidden' name='upc_code[]' value='".$field['upc_chk']."'>";
			echo '<input type="hidden" name="selectedrecords[]" class="rowCheck" value="'.$field['item_id'].'"/>';
			echo '<span class="printing_upc">'.$printUpc."</span>";
			echo '<span class="printing_data">'.$printingData."</span>";
			echo"</td>";
			echo "</tr>";
		}
		//end table here
		echo'</tbody></table>';
	}

}

?>