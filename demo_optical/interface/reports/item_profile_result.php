<?php
/*
File: item_profile_result.php
Coded in PHP7
Purpose: Item Profile Report
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

function cal_discount($amt,$dis)
{
	$total = 0;
	if(preg_match('/%/', $dis, $matches))
	{
		$disc = str_replace('%','',$dis);
		$total = ($amt*$disc)/100;
	}
	elseif(preg_match('/$/', $dis, $matche))
	{
		$total = str_replace('$','',$dis);
	}
	else
	{
		$total = $dis;
	}
	return $total;
}

if($_POST['generateRpt']){
	
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	//MASTER TABLES
	if($_POST['groupBy']!='manufac'){
		$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details";
		$manu_detail_rs = imw_query($manu_detail_qry);
		while($manu_detail_res=imw_fetch_array($manu_detail_rs)){
			$arrManufac[$manu_detail_res['id']]=$manu_detail_res['manufacturer_name'];
		}
	}
	//TYPES
	if($_POST['groupBy']!='type'){
	   $typeRs = imw_query("select * from in_module_type");
	   while($typeRes=imw_fetch_array($typeRs)){
		   $arrTypes[$typeRes['id']]=$typeRes['module_type_name'];
	   }
	}
	//VENDORS
	$vendorRs = imw_query("select * from in_vendor_details");
	while($vendorRes=imw_fetch_array($vendorRs)){
		$arrVendors[$vendorRes['id']]=$vendorRes['vendor_name'];
	}
	//Frame BRANDS
	$brandRs = imw_query("select * from in_frame_sources");
    while($brandRes=imw_fetch_array($brandRs)){
		$arrBrands[$brandRes['id']]=$brandRes['frame_source'];
  	}
	
	//Lens Material
	$materialRs = imw_query("select * from in_lens_material");
    while($materialRes=imw_fetch_array($materialRs)){
		$arrMaterial[$materialRes['id']]=$materialRes['material_name'];
  	}
	
	//Measurement
	$measureRs = imw_query("select * from in_supplies_measurment");
    while($measureRes=imw_fetch_array($measureRs)){
		$arrMeasure[$measureRes['id']]=$measureRes['measurment_name'];
  	}
	
	//OPERATORS
   $usersRs = imw_query("select id, fname,lname from users");
   while($usersRes=imw_fetch_array($usersRs)){
	   if($usersRes['lname']!='' || $usersRes['fname']!=''){
			$arrUsers[$usersRes['id']]=$usersRes['lname'].', '.$usersRes['fname']; 
			//TWO CHARACTERS
			$opInit = substr($usersRes['lname'],0,1);
			$opInit .= substr($usersRes['fname'],0,1);
			$arrUsersTwoChar[$usersRes['id']] = strtoupper($opInit);
	   }
   }
   
   //Facility
   $facRs = imw_query("select * from in_location");
   while($facRes=imw_fetch_array($facRs)){
	   $arrfacility[$facRes['id']]=$facRes['loc_name'];
   }
   
	//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$sql = "select * from cpt_category_tbl order by cpt_category ASC";
	$rez = imw_query($sql);	
	while($row=imw_fetch_array($rez)){
		$cat_id = $row["cpt_cat_id"];		
		$sql = "select * from cpt_fee_tbl WHERE cpt_cat_id='".$cat_id."' AND status='active' AND delete_status = '0' order by cpt_prac_code ASC";
		$rezCodes = imw_query($sql);
		$arrSubOptions = array();
		if(imw_num_rows($rezCodes) > 0){
			while($rowCodes=imw_fetch_array($rezCodes)){
				$arrSubOptions[] = array($rowCodes["cpt_prac_code"]."-".$rowCodes["cpt_desc"],$xyz, $rowCodes["cpt_prac_code"]);
				$arrCptCodesAndDesc[] = $rowCodes["cpt_fee_id"];
				$arrCptCodesAndDesc[] = $rowCodes["cpt_prac_code"];
				$arrCptCodesAndDesc[] = $rowCodes["cpt_desc"];
				
				$code = $rowCodes["cpt_prac_code"];
				$cpt_desc = $rowCodes["cpt_desc"];
				$stringAllProcedures.="'".str_replace("'","",$code)."',";	
				$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
				$proc_code_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_prac_code"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//
	
	$dateFrom=saveDateFormat($_POST['date_from']);
	$dateTo=saveDateFormat($_POST['date_to']);
	
	$tb_field="";
	$tbNameJoin="";

	if($_POST['product_type']==1)
	{
		$tb_field.= ",BT.frame_source, St.style_name, Fs.shape_name, Fc.color_name, Ft.type_name";
		$tbNameJoin.= " LEFT join in_frame_sources as BT on BT.id = in_item.brand_id LEFT join in_frame_styles as St on St.id = in_item.frame_style LEFT join in_frame_shapes as Fs on Fs.id = in_item.frame_shape LEFT join in_frame_color as Fc on Fc.id = in_item.color LEFT join in_frame_types as Ft on Ft.id = in_item.type_id";
	}
	
	if($_POST['product_type']==3)
	{
		$tb_field.= ",BT.brand_name as frame_source, Cat.cat_name, Ct.type_name, Cs.supply_name, Ccl.color_name";
		$tbNameJoin.= " LEFT join in_contact_brand as BT on BT.id = in_item.brand_id LEFT join in_contact_cat as Cat on Cat.id = in_item.class_id LEFT join in_type as Ct on Ct.id = in_item.type_id LEFT join in_supply as Cs on Cs.id = in_item.supply_id LEFT join in_color as Ccl on Ccl.id = in_item.color";
	}
	
	if($_POST['product_type']==5 || $_POST['product_type']==7)
	{
		$tb_field.= ",Sm.measurment_name, Ss.size_name";
		$tbNameJoin.= "LEFT join in_supplies_measurment as Sm on Sm.id = in_item.measurment LEFT join in_supplies_size as Ss on Ss.id = in_item.char_size";
	}
	
	if($_POST['product_type']==2)
	{
		$tb_field.= ",Lt.type_name, Lpr.progressive_name, Lm.material_name, Lar.ar_name, Ltr.transition_name, Lp.polarized_name, Ltn.tint_type, Le.edge_name, Lc.color_name, Ll.lab_name";
		$tbNameJoin.= "LEFT join in_lens_type as Lt on Lt.id = in_item.type_id LEFT join in_lens_progressive as Lpr on Lpr.id = in_item.progressive_id LEFT join in_lens_material as Lm on Lm.id = in_item.material_id LEFT join in_lens_ar as Lar on Lar.id = in_item.a_r_id LEFT join in_lens_transition as Ltr on Ltr.id = in_item.transition_id LEFT join in_lens_polarized as Lp on Lp.id = in_item.polarized_id LEFT join in_lens_tint as Ltn on Ltn.id = in_item.tint_id LEFT join in_lens_edge as Le on Le.id = in_item.edge_id LEFT join in_lens_color as Lc on Lc.id = in_item.color LEFT join in_lens_lab as Ll on Ll.id = in_item.lab_id";
	}
	
	if($_POST['date_from']!="--" || $_POST['date_to']!="--"){
		$tbNameJoin.= " LEFT join in_order_details ON in_order_details.item_id = in_item.id"; 
	}
		
	$mainQry="Select in_item.*, DATE_FORMAT(in_item.entered_date, '%m-%d-%Y') as 'EnterDate', sum(loc_tot.stock) as qty_on_hand $tb_field FROM in_item 
	left join in_item_loc_total as loc_tot on loc_tot.item_id = in_item.id
	$tbNameJoin";
	
	
	
	if($_POST['product_type']=="all")
	{
		$mainQry.=" WHERE in_item.module_type_id>0";
	}
	else
	{
		$mainQry.=" WHERE in_item.module_type_id='".$_POST['product_type']."'";
	}
	if(empty($_POST['manufac'])==false){
		$mainQry.=' AND in_item.manufacturer_id IN('.$_POST['manufac'].')';
	}
	if(empty($_POST['vendor'])==false){
		$mainQry.=' AND in_item.vendor_id IN('.$_POST['vendor'].')';
	}
	if(empty($_POST['brand'])==false){
		$mainQry.=' AND in_item.brand_id IN('.$_POST['brand'].')';
	}
	if(empty($_POST['material'])==false){
		$mainQry.=' AND in_item.material_id IN('.$_POST['material'].')';
	}
	if(empty($_POST['measurement'])==false){
		$mainQry.=' AND in_item.measurment IN('.$_POST['measurement'].')';
	}
	if(empty($_POST['facility'])==false){
		$mainQry.=' AND loc_tot.loc_id IN('.$_POST['facility'].')';
	}
	if(empty($_POST['upc_code'])==false){
		$mainQry.=' And in_item.upc_code like("'.$_POST['upc_code'].'%")';
	}
	if(empty($_POST['item_name'])==false){
		$mainQry.=' And in_item.name like ("'.$_POST['item_name'].'%")';
	}
	
	$OrdDetWhr="";
	if($dateFrom!="--" && $dateTo!="--"){
		$OrdDetWhr=' AND (in_order_details.entered_date BETWEEN "'.$dateFrom.'" AND "'.$dateTo.'")';
	}else if($dateFrom!="--"){
		$OrdDetWhr=" AND in_order_details.entered_date>='".$dateFrom."'";
	}else if($dateTo!="--"){
		$OrdDetWhr=" AND in_order_details.entered_date<='".$dateTo."'";
	}
	
	$mainQry.=" $OrdDetWhr";
	
	if($_POST['product_type']=="all")
	{
		$mainQry.=" group by in_item.id order by in_item.module_type_id asc";
	}else{
		$mainQry.=" group by in_item.id order by in_item.entered_date asc";
	}
	//echo $mainQry;
	$mainRs=imw_query($mainQry);
	$mainNumRs=imw_num_rows($mainRs);
	while($mainRes=imw_fetch_array($mainRs)){
		$arrMainDetail[] = $mainRes;
		$showManufac=$arrManufac[$mainRes['manufacturer_id']];
		$showType=$arrTypes[$mainRes['module_type_id']];
		$showVendor=$arrVendors[$mainRes['vendor_id']];
		$showBrand=$mainRes['frame_source'];
		$showMaterial=$mainRes['material_name'];
		$showMeasure=$mainRes['measurment_name'];
	}
	//echo '<pre>'; print_r($arrMainDetail);
	
	// MAKE HTML
	if(count($arrMainDetail)>0){
		
		
$css = '<style type="text/css">
		.reportHeadBG{ font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#D9EDF8;}
		.reportHeadBG1{ font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; background-color:#67B9E8; color:#FFF;}
		.reportTitle { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; background-color:#7B7B7B; color:#FFF }
		.rptText13 { font-family: Arial, Helvetica, sans-serif; font-size:13px; }
		.rptText13b { font-family: Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; }
		.rptText12b { font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; }		
		.whiteBG{ background:#fff; } 
		.td_bdr { border-bottom:1px solid #ccc; padding:5px 0px; }
		.td_rbdr { border-right:1px solid #ccc; }
.alignMiddle{vertical-align:middle;} .alignTop{vertical-align:top;}  .alignBottom{vertical-align:bottom;}
.alignLeft{text-align:left;} .alignRight{text-align:right;} .alignCenter{text-align:center;} .alignJustify{text-align:justify;}	
		</style>';	

		$grandqtyTotal=$grandqtypending=$grandqtyordered=$grandqtyreceived=$grandqtydispensed=0;
		$grandpriceTotal=$grandpricepending=$grandpriceTotal=$grandpriceTotal=$grandpricedispensed=0;
		$subTotQty=$subQtypending=$subQtyordered=$subQtyreceived=$subQtydispensed=0;
		$subTotprice=$subPricepending=$subPriceorered=$subPricereceived=$subPricedispensed=0;
		
		for($i=0; $i<sizeof($arrMainDetail); $i++){
			$subTotQty+=$arrMainDetail[$i]['qty_on_hand'];
			$subTotprice+=$arrMainDetail[$i]['retail_price'] * $arrMainDetail[$i]['qty_on_hand'];
			
			$html.='<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="0" cellspacing="0">';
			$html.='<tr><td colspan="6" class="reportTitle" style="width:580px; padding:4px;">Item : '.$arrMainDetail[$i]['upc_code'].' - '.$arrMainDetail[$i]['name'].'</td>
				<td colspan="4" class="reportTitle" style="width:180px; text-align:right; padding:4px;">Entered Date : '.$arrMainDetail[$i]['EnterDate'].'</td>
			</tr>';
			
			$html.= '<tr>';
			if($arrMainDetail[$i]['item_prac_code']>0)
			{
				$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:77px;"><strong>Prac Code</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">'.$proc_code_arr[$arrMainDetail[$i]['item_prac_code']].'</td>';
			}
			else
			{
				$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:77px;"><strong>Prac Code</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">N/A</td>';
			}
			
			if($arrMainDetail[$i]['manufacturer_id']>0)
			{
				$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:95px;"><strong>Manufacturer</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:210px;">'.$arrManufac[$arrMainDetail[$i]['manufacturer_id']].'</td>';	
			}
			else
			{
				$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:95px;"><strong>Manufacturer</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:210px;">N/A</td>';
			}
			
			if($arrMainDetail[$i]['vendor_id']>0)
			{
				$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:56px;"><strong>Vendor</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:200px;">'.$arrVendors[$arrMainDetail[$i]['vendor_id']].'</td>';
			}
			else
			{
				$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:56px;"><strong>Vendor</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:200px;">N/A</td>';
			}
			
			if($arrMainDetail[$i]['qty_on_hand']!="")
			{
				$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:90px;"><strong>Qty on hand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:100px;">'.$arrMainDetail[$i]['qty_on_hand'].'</td>';
			}
			else
			{
				$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:90px;"><strong>Qty on hand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:100px;">0</td>';
			}
			
			$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Price</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:100px;">'.currency_symbol(true).$arrMainDetail[$i]['retail_price'].'</td>';
			
			$html.= '</tr></table>';
			
			if($arrMainDetail[$i]['module_type_id']==1)
			{
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
						
				if($arrMainDetail[$i]['shape_name']!="")
				{
					$html.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:50px;"><strong>Shape</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:171px;">'.$arrMainDetail[$i]['shape_name'].'</td>';
				}
				else
				{
					$html.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:50px;"><strong>Shape</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:171px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['frame_source']!="")
				{
					$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:50px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:265px;">'.$arrMainDetail[$i]['frame_source'].'</td>';
				}
				else
				{
					$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:50px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:265px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['style_name']!="")
				{
					$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Style</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:221px;">'.$arrMainDetail[$i]['style_name'].'</td>';
				}
				else
				{
					$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Style</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:221px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['color']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Color</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:299px;">'.$arrMainDetail[$i]['color_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Color</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:299px;">N/A</td>';
				}
				
				$html.= '</tr></table><table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['a']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>A</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">'.$arrMainDetail[$i]['a'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>A</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['b']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>B</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">'.$arrMainDetail[$i]['b'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>B</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['ed']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:27px;"><strong>ED</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">'.$arrMainDetail[$i]['ed'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:27px;"><strong>ED</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['dbl']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:36px;"><strong>DBL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">'.$arrMainDetail[$i]['dbl'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:36px;"><strong>DBL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['temple']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:56px;"><strong>Temple</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:95px;">'.$arrMainDetail[$i]['temple'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:56px;"><strong>Temple</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:95px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['bridge']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:51px;"><strong>Bridge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:75px;">'.$arrMainDetail[$i]['bridge'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:51px;"><strong>Bridge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:75px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['fpd']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>FPD</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:81px;">'.$arrMainDetail[$i]['fpd'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>FPD</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:81px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['gender']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:57px;"><strong>Gender</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:108px;">'.$arrMainDetail[$i]['gender'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:57px;"><strong>Gender</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:108px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['type_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Type</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:114px;">'.$arrMainDetail[$i]['type_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Type</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:114px;">N/A</td>';
				}
				
				
				$html.= '</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==3)
			{
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				if($arrMainDetail[$i]['type_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:80px;"><strong>Wear Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:149px;">'.$arrMainDetail[$i]['type_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:80px;"><strong>Wear Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:149px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['frame_source']!="")
				{
					$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:50px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:275px;">'.$arrMainDetail[$i]['frame_source'].'</td>';
				}
				else
				{
					$html.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:50px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:275px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['class_id']>0)
				{
					$html.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:68px;"><strong>Category</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:150px;">'.$arrMainDetail[$i]['cat_name'].'</td>';
				}
				else
				{
					$html.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:68px;"><strong>Category</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:150px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['supply_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:55px;"><strong>Supply</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:150px;">'.$arrMainDetail[$i]['supply_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:55px;"><strong>Supply</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:150px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['color']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:150px;">'.$arrMainDetail[$i]['color_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:150px;">N/A</td>';
				}
				
				$html.= '</tr></table>';
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['bc']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:32px;"><strong>BC</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">'.$arrMainDetail[$i]['bc'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:32px;"><strong>BC</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['diameter']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:67px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">'.$arrMainDetail[$i]['diameter'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:67px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['sphere_positive']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:55px;"><strong>Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">'.$arrMainDetail[$i]['sphere_positive'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:55px;"><strong>Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['cylindep_positive']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">'.$arrMainDetail[$i]['cylindep_positive'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:120px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['axis']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Axis</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:500px;">'.$arrMainDetail[$i]['axis'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Axis</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:500px;">N/A</td>';
				}
							
				$html.= '</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==5 || $arrMainDetail[$i]['module_type_id']==7)
			{
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				if($arrMainDetail[$i]['char_size']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Size</strong> :</td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:178px;"> '.$arrMainDetail[$i]['size_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Size</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:178px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['measurment']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:98px;"><strong>Measurement</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:210px;">'.$arrMainDetail[$i]['measurment_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:98px;"><strong>Measurement</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:210px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['other']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:48px;"><strong>Other</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:210px;">'.$arrMainDetail[$i]['other'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:48px;"><strong>Other</strong> :</td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:210px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['type_desc']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:83px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:255px;">'.$arrMainDetail[$i]['type_desc'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:83px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:255px;">N/A</td>';
				}
				$html .='</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==6)
			{
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['type_desc']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:83px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop  td_bdr td_rbdr" style="width:1015px;">'.$arrMainDetail[$i]['type_desc'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:83px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:1015px;">N/A</td>';
				}
				
				$html.= '</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==2)
			{
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				if($arrMainDetail[$i]['type_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:80px;"><strong>Seg Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:137px;">'.$arrMainDetail[$i]['type_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:80px;"><strong>Seg Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:137px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['progressive_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:85px;"><strong>Progressive</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:222px;">'.$arrMainDetail[$i]['progressive_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:85px;"><strong>Progressive</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:222px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['material_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:62px;"><strong>Material</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">'.$arrMainDetail[$i]['material_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:62px;"><strong>Material</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['a_r_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:34px;"><strong>A/R</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">'.$arrMainDetail[$i]['ar_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:34px;"><strong>A/R</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['transition_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:75px;"><strong>Transition</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">'.$arrMainDetail[$i]['transition_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:75px;"><strong>Transition</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">N/A</td>';
				}
				
				$html.= '</tr></table>';
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';				
				if($arrMainDetail[$i]['polarized_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:70px;"><strong>Polarized</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">'.$arrMainDetail[$i]['polarized_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:70px;;"><strong>Polarized</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['edge_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Edge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:254px;">'.$arrMainDetail[$i]['edge_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Edge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:254px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['tint_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Tint</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:160px;">'.$arrMainDetail[$i]['tint_type'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Tint</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:160px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['color']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:44px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:125px;">'.$arrMainDetail[$i]['color_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:44px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:125px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['lab_id']>0)
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:33px;"><strong>Lab</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:175px;">'.$arrMainDetail[$i]['lab_name'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:33px;"><strong>Lab</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:175px;">N/A</td>';
				}
				
				$html.= '</tr></table>';
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['sphere_positive']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:116px;"><strong>Positive Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:118px;">'.$arrMainDetail[$i]['sphere_positive'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:116px;"><strong>Positive Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:118px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['sphere_negative']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:121px;"><strong>Negative Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:212px;">'.$arrMainDetail[$i]['sphere_negative'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:121px;"><strong>Negative Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:212px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['cylindep_positive']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:96px;"><strong>Positive CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:110px;">'.$arrMainDetail[$i]['cylindep_positive'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:96px;"><strong>Positive CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:110px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['cylindep_negative']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:101px;"><strong>Negative CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:100px;">'.$arrMainDetail[$i]['cylindep_negative'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:101px;"><strong>Negative CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:100px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['diameter']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:68px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:163px;">'.$arrMainDetail[$i]['diameter'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:68px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:163px;">N/A</td>';
				}
				
				$html.= '</tr></table>';
				$html.= '<table cellspacing="0" cellpadding="0" style="width:100%; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['th']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>TH</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:70px;">'.$arrMainDetail[$i]['th'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>TH</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:70px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['minimum_segment']!="")
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:85px;"><strong>Min. Seg. ht.</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:890px;">'.$arrMainDetail[$i]['minimum_segment'].'</td>';
				}
				else
				{
					$html.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:85px;"><strong>Min. Seg. ht.</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:890px;">N/A</td>';
				}
				$html.= '</tr></table>';
			}
			
			//PDF Content
			$htmlPDF.='<table style="width:700px; border:none; background:#E3E3E3;" cellpadding="0" cellspacing="0">';
			$htmlPDF.='<tr><td colspan="6" class="reportTitle" style="width:450px; padding:4px;">Item : '.$arrMainDetail[$i]['upc_code'].' - '.$arrMainDetail[$i]['name'].'</td>
			<td colspan="4" class="reportTitle" style="width:200px; text-align:right; padding:4px;">Entered Date : '.$arrMainDetail[$i]['EnterDate'].'</td>
			</tr>';
			$htmlPDF.= '<tr>';
			
			if($arrMainDetail[$i]['item_prac_code']>0)
			{
				$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:62px;"><strong>Prac Code</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:40px; padding-left:0px;">'.$proc_code_arr[$arrMainDetail[$i]['item_prac_code']].'</td>';
			}
			else
			{
				$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:62px;"><strong>Prac Code</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:40px; padding-left:0px;">N/A</td>';
			}
			
			if($arrMainDetail[$i]['manufacturer_id']>0)
			{
				$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:76px;"><strong>Manufacturer</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:95px; padding-left:0px;">'.$arrManufac[$arrMainDetail[$i]['manufacturer_id']].'</td>';	
			}
			else
			{
				$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:76px;"><strong>Manufacturer</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:95px; padding-left:0px;">N/A</td>';
			}
			
			if($arrMainDetail[$i]['vendor_id']>0)
			{
				$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:39px;"><strong>Vendor</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:95px; padding-left:0px;">'.$arrVendors[$arrMainDetail[$i]['vendor_id']].'</td>';
			}
			else
			{
				$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:39px;"><strong>Vendor</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:95px; padding-left:0px;">N/A</td>';
			}
			
			if($arrMainDetail[$i]['qty_on_hand']!="")
			{
				$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:74px;"><strong>Qty on hand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['qty_on_hand'].'</td>';
			}
			else
			{
				$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:74px;"><strong>Qty on hand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">0</td>';
			}
			
			$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Price</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:40px; padding-left:0px;">'.currency_symbol(true).$arrMainDetail[$i]['retail_price'].'</td>';
			
			$htmlPDF.= '</tr></table>';
			
			if($arrMainDetail[$i]['module_type_id']==1)
			{
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
						
				if($arrMainDetail[$i]['shape_name']!="")
				{
					$htmlPDF.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:28px;"><strong>Shape</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:66px; padding-left:0px;">'.$arrMainDetail[$i]['shape_name'].'</td>';
				}
				else
				{
					$htmlPDF.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:28px;"><strong>Shape</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:66px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['frame_source']!="")
				{
					$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px; padding-left:0px;">'.$arrMainDetail[$i]['frame_source'].'</td>';
				}
				else
				{
					$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:140px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['style_name']!="")
				{
					$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:27px;"><strong>Style</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:108px; padding-left:0px;">'.$arrMainDetail[$i]['style_name'].'</td>';
				}
				else
				{
					$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:27px;"><strong>Style</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:108px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['color']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Color</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:180px; padding-left:0px;">'.$arrMainDetail[$i]['color_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Color</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:180px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table><table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['a']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:10px;"><strong>A</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['a'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:10px;"><strong>A</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['b']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:10px;"><strong>B</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['b'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:10px;"><strong>B</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['ed']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>ED</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['ed'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>ED</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['dbl']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>DBL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['dbl'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>DBL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['temple']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Temple</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['temple'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Temple</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['bridge']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Bridge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['bridge'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Bridge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['fpd']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>FPD</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">'.$arrMainDetail[$i]['fpd'].'</td>';				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>FPD</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:20px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['gender']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Gender</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['gender'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Gender</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['type_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Type</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:55px; padding-left:0px;">'.$arrMainDetail[$i]['type_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Type</strong> :  </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:55px; padding-left:0px;">N/A</td>';
				}
				
				
				$htmlPDF.= '</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==3)
			{
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				if($arrMainDetail[$i]['type_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:63px;"><strong>Wear Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:39px; padding-left:0px;">'.$arrMainDetail[$i]['type_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:63px;"><strong>Wear Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:39px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['frame_source']!="")
				{
					$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:139px; padding-left:0px;">'.$arrMainDetail[$i]['frame_source'].'</td>';
				}
				else
				{
					$htmlPDF.=	'<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:35px;"><strong>Brand</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:139px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['class_id']>0)
				{
					$htmlPDF.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>Category</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px; padding-left:0px;">'.$arrMainDetail[$i]['cat_name'].'</td>';
				}
				else
				{
					$htmlPDF.= '<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>Category</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['supply_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>Supply</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px; padding-left:0px;">'.$arrMainDetail[$i]['supply_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>Supply</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:60px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['color']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:71px; padding-left:0px;">'.$arrMainDetail[$i]['color_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:71px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table>';
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['bc']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>BC</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:45px; padding-left:0px;">'.$arrMainDetail[$i]['bc'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:15px;"><strong>BC</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:45px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['diameter']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:45px; padding-left:0px;">'.$arrMainDetail[$i]['diameter'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:45px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['sphere_positive']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:43px; padding-left:0px;">'.$arrMainDetail[$i]['sphere_positive'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:43px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['cylindep_positive']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:38px;"><strong>CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:70px; padding-left:0px;">'.$arrMainDetail[$i]['cylindep_positive'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:40px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['axis']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Axis</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:261px; padding-left:0px;">'.$arrMainDetail[$i]['axis'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Axis</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:261px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==5 || $arrMainDetail[$i]['module_type_id']==7)
			{
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				if($arrMainDetail[$i]['char_size']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Size</strong> :</td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:77px; padding-left:0px;"> '.$arrMainDetail[$i]['size_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Size</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:77px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['measurment']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:75px;"><strong>Measurement</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:93px; padding-left:0px;">'.$arrMainDetail[$i]['measurment_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:75px;"><strong>Measurement</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:93px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['other']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Other</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:105px; padding-left:0px;">'.$arrMainDetail[$i]['other'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:30px;"><strong>Other</strong> :</td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:105px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['type_desc']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:70px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:142px; padding-left:0px;">'.$arrMainDetail[$i]['type_desc'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:70px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:142px; padding-left:0px;">N/A</td>';
				}
				$htmlPDF .='</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==6)
			{
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['type_desc']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:60px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop  td_bdr td_rbdr" style="width:653px; padding-left:0px;">'.$arrMainDetail[$i]['type_desc'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:60px;"><strong>Description</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:653px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table>';
			}
			
			if($arrMainDetail[$i]['module_type_id']==2)
			{
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				if($arrMainDetail[$i]['type_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:67px;"><strong>Seg Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['type_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:67px;"><strong>Seg Type</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['progressive_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:55px;"><strong>Progressive</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:102px; padding-left:0px;">'.$arrMainDetail[$i]['progressive_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:55px;"><strong>Progressive</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:102px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['material_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Material</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:50px; padding-left:0px;">'.$arrMainDetail[$i]['material_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:42px;"><strong>Material</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:50px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['a_r_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:24px;"><strong>A/R</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:70px; padding-left:0px;">'.$arrMainDetail[$i]['ar_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:24px;"><strong>A/R</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:70px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['transition_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:60px;"><strong>Transition</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:66px; padding-left:0px;">'.$arrMainDetail[$i]['transition_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:60px;"><strong>Transition</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:66px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table>';
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';				
				if($arrMainDetail[$i]['polarized_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;"><strong>Polarized</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:45px; padding-left:0px;">'.$arrMainDetail[$i]['polarized_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:40px;;"><strong>Polarized</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:45px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['edge_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Edge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:144px; padding-left:0px;">'.$arrMainDetail[$i]['edge_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Edge</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:144px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['tint_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Tint</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:75px; padding-left:0px;">'.$arrMainDetail[$i]['tint_type'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Tint</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:75px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['color']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:63px; padding-left:0px;">'.$arrMainDetail[$i]['color_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:25px;"><strong>Color</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:63px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['lab_id']>0)
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Lab</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:105px; padding-left:0px;">'.$arrMainDetail[$i]['lab_name'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:20px;"><strong>Lab</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:105px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table>';
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['sphere_positive']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:95px;"><strong>Positive Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['sphere_positive'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:95px;"><strong>Positive Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['sphere_negative']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:100px;"><strong>Negative Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['sphere_negative'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:100px;"><strong>Negative Sphere</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['cylindep_positive']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:80px;"><strong>Positive CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['cylindep_positive'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:80px;"><strong>Positive CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['cylindep_negative']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:85px;"><strong>Negative CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['cylindep_negative'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:85px;"><strong>Negative CYL</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['diameter']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:39px; padding-left:0px;">'.$arrMainDetail[$i]['diameter'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:45px;"><strong>Diameter</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:39px; padding-left:0px;">N/A</td>';
				}
				
				$htmlPDF.= '</tr></table>';
				$htmlPDF.= '<table cellspacing="0" cellpadding="0" style="width:700px; border:none; background:#E3E3E3;"><tr>';
				
				if($arrMainDetail[$i]['th']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:12px;"><strong>TH</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">'.$arrMainDetail[$i]['th'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:12px;"><strong>TH</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:35px; padding-left:0px;">N/A</td>';
				}
				
				if($arrMainDetail[$i]['minimum_segment']!="")
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:75px;"><strong>Min. Seg. ht.</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:564px; padding-left:0px;">'.$arrMainDetail[$i]['minimum_segment'].'</td>';
				}
				else
				{
					$htmlPDF.='<td class="whiteBG rptText13 alignLeft alignTop td_bdr" style="width:75px;"><strong>Min. Seg. ht.</strong> : </td><td class="whiteBG rptText13 alignLeft alignTop td_bdr td_rbdr" style="width:564px; padding-left:0px;">N/A</td>';
				}
				$htmlPDF.= '</tr></table>';
			}
			
			$orders_detail = array();			
			$sel_order = imw_query("select in_order_details.order_id, in_order_details.patient_id,
			(in_order_details.qty+in_order_details.qty_right) as tqty, in_order_details.price, in_order_details.discount,
			in_order_details.total_amount, in_order_details.order_status, DATE_FORMAT(in_order_details.entered_date, '%m-%d-%Y') as enteredDate,
			in_order_details.operator_id, patient_data.fname, patient_data.lname 
			FROM in_order_details 
			LEFT JOIN patient_data ON patient_data.id = in_order_details.patient_id 
			WHERE item_id='".$arrMainDetail[$i]['id']."' and del_status='0' $OrdDetWhr");
			if(imw_num_rows($sel_order)>0)
			{
				while($get_ord_det = imw_fetch_array($sel_order))
				{
					$orders_detail[] = $get_ord_det;
				}
			}

			if(count($orders_detail) > 0)
			{
				$html.='<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr>
					<td colspan="9" class="reportHeadBG1 alignCenter" style="width:100%; padding:2px;">Order Detail</td>
				</tr>
				<tr>
					<td class="reportHeadBG1 alignCenter" style="width:60px;">Order #</td>
					<td class="reportHeadBG1 alignLeft" style="width:230px;">Patient Name - Id</td>
					<td class="reportHeadBG1 alignRight" style="width:60px;">Qty</td>
					<td class="reportHeadBG1 alignRight" style="width:80px;">Price</td>
					<td class="reportHeadBG1 alignRight" style="width:60px;">Disc.</td>
					<td class="reportHeadBG1 alignRight" style="width:80px;">Total Amt.</td>
					<td class="reportHeadBG1 alignCenter" style="width:80px;">Status</td>
					<td class="reportHeadBG1 alignCenter" style="width:90px;">Ordered On</td>
					<td class="reportHeadBG1 alignCenter" style="width:50px;">Opr.</td>
				</tr>';
				
				$htmlPDF.='<table style="width:700px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
				<tr>
					<td colspan="9" class="reportHeadBG1 alignCenter" style="width:700px; padding:2px;">Order Detail</td>
				</tr>
				<tr>
					<td class="reportHeadBG1 alignCenter" style="width:60px;">Order #</td>
					<td class="reportHeadBG1 alignLeft" style="width:200px;">Patient Name - Id</td>
					<td class="reportHeadBG1 alignRight" style="width:54px;">Qty</td>
					<td class="reportHeadBG1 alignRight" style="width:70px;">Price</td>
					<td class="reportHeadBG1 alignRight" style="width:54px;">Disc.</td>
					<td class="reportHeadBG1 alignRight" style="width:70px;">Total Amt.</td>
					<td class="reportHeadBG1 alignCenter" style="width:70px;">Status</td>
					<td class="reportHeadBG1 alignCenter" style="width:90px;">Ordered On</td>
					<td class="reportHeadBG1 alignCenter" style="width:40px;">Opr.</td>
				</tr>';
				$qtyTotal=$priceTotal=$amtTotal=$TotQty=$Totprice=$Totamt=$Totdisc=$discTotal=0;
				
				for($n=0;$n<sizeof($orders_detail);$n++)
				{
					$TotQty += $orders_detail[$n]['tqty'];
					$Totprice += $orders_detail[$n]['price'];
					$Totamt += $orders_detail[$n]['total_amount'];
					$ord_status = $orders_detail[$n]['order_status'];
					$disc = $orders_detail[$n]['discount'];
					if($orders_detail[$n]['order_status']=="")
					{
						$ord_status = "pending";
					}
					if($ord_status=="pending")
					{
						$subQtypending+=$orders_detail[$n]['tqty'];
						$subPricepending+=$orders_detail[$n]['total_amount'];
					}
					if($ord_status=="ordered")
					{
						$subQtyordered+=$orders_detail[$n]['tqty'];
						$subPriceordered+=$orders_detail[$n]['total_amount'];
					}
					if($ord_status=="received")
					{
						$subQtyreceived+=$orders_detail[$n]['tqty'];
						$subPricereceived+=$orders_detail[$n]['total_amount'];
					}
					if($ord_status=="dispensed")
					{
						$subQtydispensed+=$orders_detail[$n]['tqty'];
						$subPricedispensed+=$orders_detail[$n]['total_amount'];
					}
					if($orders_detail[$n]['discount']=="" || $orders_detail[$n]['discount']==0.00)
					{
						$disc = 0;
					}
					$discont = cal_discount($orders_detail[$n]['price'],$disc);
					$Totdisc += $discont;
					$html.='<tr>
						<td class="whiteBG rptText13 alignCenter" style="width:60px;">'.$orders_detail[$n]['order_id'].'</td>
						<td class="whiteBG rptText13 alignLeft" style="width:230px;">'.$orders_detail[$n]['lname'].' '.$orders_detail[$n]['fname'].' - '.$orders_detail[$n]['patient_id'].'</td>
						<td class="whiteBG rptText13 alignRight" style="width:60px;">'.$orders_detail[$n]['tqty'].'</td>
						<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).$orders_detail[$n]['price'].'</td>
						<td class="whiteBG rptText13 alignRight" style="width:60px;">'.currency_symbol(true).number_format($discont, 2, '.', '').'</td>
						<td class="whiteBG rptText13 alignRight" style="width:80px;">'.currency_symbol(true).$orders_detail[$n]['total_amount'].'</td>
						<td class="whiteBG rptText13 alignCenter" style="width:80px;">'.$ord_status.'</td>
						<td class="whiteBG rptText13 alignCenter" style="width:90px;">'.$orders_detail[$n]['enteredDate'].'</td>
						<td class="whiteBG rptText13 alignCenter" style="width:50px;">'.$arrUsersTwoChar[$orders_detail[$n]['operator_id']].'</td>
					</tr>';
					
					$htmlPDF.='<tr>
						<td class="whiteBG rptText13 alignCenter" style="width:60px;">'.$orders_detail[$n]['order_id'].'</td>
						<td class="whiteBG rptText13 alignLeft" style="width:200px;">'.$orders_detail[$n]['lname'].' '.$orders_detail[$n]['fname'].' - '.$orders_detail[$n]['patient_id'].'</td>
						<td class="whiteBG rptText13 alignRight" style="width:54px;">'.$orders_detail[$n]['tqty'].'</td>
						<td class="whiteBG rptText13 alignRight" style="width:70px;">'.currency_symbol(true).$orders_detail[$n]['price'].'</td>
						<td class="whiteBG rptText13 alignRight" style="width:54px;">'.currency_symbol(true).number_format($discont, 2, '.', '').'</td>
						<td class="whiteBG rptText13 alignRight" style="width:70px;">'.currency_symbol(true).$orders_detail[$n]['total_amount'].'</td>
						<td class="whiteBG rptText13 alignCenter" style="width:70px;">'.$ord_status.'</td>
						<td class="whiteBG rptText13 alignCenter" style="width:90px;">'.$orders_detail[$n]['enteredDate'].'</td>
						<td class="whiteBG rptText13 alignCenter" style="width:40px;">'.$arrUsersTwoChar[$orders_detail[$n]['operator_id']].'</td>
					</tr>';
				}
				
				//GRAND TOTAL
				$qtyTotal+=$TotQty;
				$priceTotal+=$Totprice;
				$amtTotal+=$Totamt;
				$discTotal+=$Totdisc;
				$html.='<tr>
					<td colspan="9" class="whiteBG pt2 pb2" width="100%"><div style="border-bottom:1px solid #0E87CA;"></div></td>
				</tr>
				<tr>
					<td class="reportHeadBG1 rptText13b alignRight" colspan="2">Total : </td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.$qtyTotal.'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.currency_symbol(true).number_format($priceTotal, 2, '.', '').'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.currency_symbol(true).number_format($discTotal, 2, '.', '').'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.currency_symbol(true).number_format($amtTotal, 2, '.', '').'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight" colspan="3">&nbsp;</td>
				</tr>';
				$html.= '</table>';
				
				$htmlPDF.='<tr>
					<td colspan="9" class="whiteBG pt2 pb2" width="700px"><div style="border-bottom:1px solid #0E87CA;"></div></td>
				</tr>
				<tr>
					<td class="reportHeadBG1 rptText13b alignRight" colspan="2">Total : </td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.$qtyTotal.'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.currency_symbol(true).number_format($priceTotal, 2, '.', '').'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.currency_symbol(true).number_format($discTotal, 2, '.', '').'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight"> '.currency_symbol(true).number_format($amtTotal, 2, '.', '').'&nbsp;</td>
					<td class="reportHeadBG1 rptText13b alignRight" colspan="3">&nbsp;</td>
				</tr>';
				$htmlPDF.= '</table>';
			}
			$html.='<table width="100%"><tr><td></td></tr></table>';
			$htmlPDF.='<table width="700px"><tr><td></td></tr></table>';
		}
		
		//GRAND TOTAL
		$grandqtyTotal+=$subTotQty;
		$grandqtypending+=$subQtypending;
		$grandqtyordered+=$subQtyordered;
		$grandqtyreceived+=$subQtyreceived;
		$grandqtydispensed+=$subQtydispensed;
		$grandpriceTotal+=$subTotprice;
		$grandpricepending+=$subPricepending;
		$grandpriceordered+=$subPriceordered;
		$grandpricereceived+=$subPricereceived;
		$grandpricedispensed+=$subPricedispensed;
		$html.='<table style="width:100%; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td colspan="3" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr>
			<td colspan="3" class="reportHeadBG rptText13b alignLeft" width="100%">Grand Total</td>
		</tr>
		<tr>
			<td class="reportHeadBG1 rptText13b alignLeft" width="250">Item Status</td>
			<td class="reportHeadBG1 rptText13b alignRight" width="250">Total Qty</td>
			<td class="reportHeadBG1 rptText13b alignRight" width="250">Total Price</td>
		</tr>
		<tr>
			<td class="whiteBG rptText13 alignLeft">Total Stock</td>
			<td class="whiteBG rptText13 alignRight">'.$grandqtyTotal.'</td>
			<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpriceTotal, 2, '.', '').'&nbsp;</td>
		</tr>';
		if($grandqtypending>0 || $grandpricepending>0)
		{
			$html.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Pending</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtypending.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpricepending, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		if($grandqtyordered>0 || $grandpriceordered>0)
		{
			$html.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Ordered</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtyordered.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpriceordered, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		if($grandqtyreceived>0 || $grandpricereceived>0)
		{
			$html.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Received</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtyreceived.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpricereceived, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		if($grandqtydispensed>0 || $grandpricedispensed>0)
		{
			$html.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Dispensed</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtydispensed.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpricedispensed, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		$html.='</table>';
		
		$htmlPDF.='<table style="width:700px; border:none; background:#E3E3E3;" cellpadding="1" cellspacing="1">
		<tr>
			<td colspan="3" class="whiteBG pt2 pb2"><div style="border-bottom:1px solid #0E87CA;"></div></td>
		</tr>
		<tr>
			<td colspan="3" class="reportHeadBG rptText13b alignLeft" width="700px">Grand Total</td>
		</tr>
		<tr>
			<td class="reportHeadBG1 rptText13b alignLeft" width="250">Item Status</td>
			<td class="reportHeadBG1 rptText13b alignRight" width="240">Total Qty</td>
			<td class="reportHeadBG1 rptText13b alignRight" width="248">Total Price</td>
		</tr>
		<tr>
			<td class="whiteBG rptText13 alignLeft">Total Stock</td>
			<td class="whiteBG rptText13 alignRight">'.$grandqtyTotal.'</td>
			<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpriceTotal, 2, '.', '').'&nbsp;</td>
		</tr>';
		if($grandqtypending>0 || $grandpricepending>0)
		{
			$htmlPDF.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Pending</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtypending.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpricepending, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		if($grandqtyordered>0 || $grandpriceordered>0)
		{
			$htmlPDF.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Ordered</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtyordered.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpriceordered, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		if($grandqtyreceived>0 || $grandpricereceived>0)
		{
			$htmlPDF.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Received</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtyreceived.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpricereceived, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		if($grandqtydispensed>0 || $grandpricedispensed>0)
		{
			$htmlPDF.='<tr>
				<td class="whiteBG rptText13 alignLeft">Total Order Dispensed</td>
				<td class="whiteBG rptText13 alignRight">'.$grandqtydispensed.'</td>
				<td class="whiteBG rptText13 alignRight">'.currency_symbol(true).number_format($grandpricedispensed, 2, '.', '').'&nbsp;</td>
			</tr>';
		}
		$htmlPDF.='</table>';
			
		if(empty($_POST['manufac'])==false){$arr_m=explode(',',$_POST['manufac']);}
		if(empty($_POST['product_type'])==false){$arr_t=explode(',',$_POST['product_type']);}
		if(empty($_POST['vendor'])==false){$arr_v=explode(',',$_POST['vendor']);}
		if(empty($_POST['brand'])==false){$arr_b=explode(',',$_POST['brand']);}
		if(empty($_POST['material'])==false){$arr_mt=explode(',',$_POST['material']);}
		if(empty($_POST['measurement'])==false){$arr_ms=explode(',',$_POST['measurement']);}
		if(empty($_POST['facility'])==false){ $arrfac=explode(',', $_POST['facility']); }
		$selManufac='All';
		$selType='All';
		$selVendor='All';
		$selBrand='All';
		$selMaterial='All';
		$selMeasure='All';
		$selUPC='All';
		$selItem='All';
		$selFac='All';
		
		$selManufac=(count($arr_m)>1)? 'Multi' : ((count($arr_m)=='1')? $showManufac : $selManufac);
		$selType=(count($arr_t)>1)? 'Multi' : ((count($arr_t)=='1')? $showType : $selType);
		$selVendor=(count($arr_v)>1)? 'Multi' : ((count($arr_v)=='1')? $showVendor : $selVendor);
		$selBrand=(count($arr_b)>1)? 'Multi' : ((count($arr_b)=='1')? $showBrand : $selBrand);
		$selMaterial=(count($arr_mt)>1)? 'Multi' : ((count($arr_mt)=='1')? $showMaterial : $selMaterial);
		$selMeasure=(count($arr_ms)>1)? 'Multi' : ((count($arr_ms)=='1')? $showMeasure : $selMeasure);
		$selUPC=(empty($_POST['upc_code'])==false)? ucfirst($_POST['upc_code']): $selUPC;
		$selItem=(empty($_POST['item_name'])==false)? ucfirst($_POST['item_name']): $selItem;
		$selFac=(count($arrfac)>1)? 'Multi' : ((count($arrfac)=='1')? ucfirst($arrfacility[$_POST['facility']]): $selFac);
		
		$reportHtml.='<table width="100%" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Item Profile Report</td>
			<td class="reportHeadBG" width="200">&nbsp;Type : '.$selType.'</td>
			<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i A').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG">&nbsp;Manufacturer : '.$selManufac.'</td>
			<td class="reportHeadBG">&nbsp;Vendor : '.$selVendor.'</td>';
		if($_POST['product_type']==1  || $_POST['product_type']==3 || $_POST['product_type']=="all")
		{
			$reportHtml.='<td class="reportHeadBG">&nbsp;Brand : '.$selBrand.'</td>';
		}
		if($_POST['product_type']==2)
		{
			$reportHtml.='<td class="reportHeadBG">&nbsp;Material : '.$selMaterial.'</td>';
		}
		if($_POST['product_type']==5 || $_POST['product_type']==6 || $_POST['product_type']==7)
		{
			$reportHtml.='<td class="reportHeadBG">&nbsp;Measurement : '.$selMeasure.'</td>';
		}
		
		$reportHtml.='</tr>
		<tr>
			<td class="reportHeadBG">&nbsp;UPC Code : '.$selUPC.'</td>
			<td class="reportHeadBG">&nbsp;Item Name : '.$selItem.'</td>
			<td class="reportHeadBG">&nbsp;Facility : '.$selFac.'</td>
		</tr>
		</table>';

		$reportHtmlPDF.='
		<page backtop="15mm" backbottom="5mm">
		<page_footer>
				<table style="width: 700px;">
					<tr>
						<td style="text-align: center;	width: 700px">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
		</page_footer>
		<page_header>
		<table width="700" cellpadding="1" cellspacing="1" border="0" style="background:#E9F8FF;">
		<tr style="height:20px;">
			<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Item Profile Report</td>
			<td class="reportHeadBG" width="240">&nbsp;Type : '.$selType.'</td>
			<td style="text-align:left;" class="reportHeadBG" width="250">&nbsp;Created by '.$opInitial.' on '.date('m-d-Y H:i A').'&nbsp;</td>
		</tr>
		<tr style="height:20px;">
			<td class="reportHeadBG">&nbsp;Manufacturer : '.$selManufac.'</td>
			<td class="reportHeadBG">&nbsp;Vendor : '.$selVendor.'</td>';
		if($_POST['product_type']==1  || $_POST['product_type']==3 || $_POST['product_type']=="all")
		{
			$reportHtmlPDF.='<td class="reportHeadBG">&nbsp;Brand : '.$selBrand.'</td>';
		}
		if($_POST['product_type']==2)
		{
			$reportHtmlPDF.='<td class="reportHeadBG">&nbsp;Material : '.$selMaterial.'</td>';
		}
		if($_POST['product_type']==5 || $_POST['product_type']==6 || $_POST['product_type']==7)
		{
			$reportHtmlPDF.='<td class="reportHeadBG">&nbsp;Measurement : '.$selMeasure.'</td>';
		}
		$reportHtmlPDF.='</tr>
		<tr>
			<td class="reportHeadBG">&nbsp;UPC Code : '.$selUPC.'</td>
			<td class="reportHeadBG">&nbsp;Item Name : '.$selItem.'</td>
			<td class="reportHeadBG">&nbsp;Facility : '.$selFac.'</td>
		</tr>
		</table>
		</page_header>';

	$reportHtml.= $html;
	
	$reportHtmlPDF.=$htmlPDF.'</page>';
}
	
  $pdfText = $css.$reportHtmlPDF;
  
  file_put_contents('../../library/new_html2pdf/item_profile_result.html',$pdfText);	
}
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script>
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
<?php
if(count($arrMainDetail)>0){
 echo $reportHtml;
}else{
	if($_REQUEST['print'])
	{
		$d="display:none;";
	}
	echo '<br><div style="text-align:center; '.$d.'"><strong>No Record Found.</strong></div>';
}
 ?>
<form name="stockFormResult" action="item_profile_result.php" method="post">
<input type="hidden" name="manufac" id="manufac" value="" />
<input type="hidden" name="product_type" id="product_type" value="" />
<input type="hidden" name="vendor" id="vendor" value="" />
<input type="hidden" name="brand" id="brand" value="" />
<input type="hidden" name="facility" id="facility" value="" />
<input type="hidden" name="material" id="material" value="" />
<input type="hidden" name="measurement" id="measurement" value="" />
<input type="hidden" name="item_name" id="item_name" value="" />
<input type="hidden" name="upc_code" id="upc_code" value="" />
<input type="hidden" name="date_from" id="date_from" value="" />
<input type="hidden" name="date_to" id="date_to" value="" />
<input type="hidden" name="generateRpt" id="generateRpt" value="yes" />
</form>

<?php if(isset($_REQUEST['print'])) { ?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/item_profile_result';
window.location.href = url;
</script>

<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $mainNumRs; ?>';		

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport()");
	}
	top.btn_show("admin",mainBtnArr);	
	
	top.main_iframe.loading('none');
});
</script>

</body>
</html>
