<?php
	/*
	File: frame_main.php
	Coded in PHP7
	Purpose: Add New Frame, Size, Measurements
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../config/config.php");
	require_once(dirname('__FILE__')."/../../library/classes/functions.php");
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	
	$heading = $_REQUEST['heading'];
	$type = $_REQUEST['type'];
	
	/*Lens Design List for Multiselect*/
	$designs = array();
	if($heading=="Lens_Material_Name"){
		$design_resp = imw_query("SELECT `id`, `design_name` FROM `in_lens_design` WHERE `del_status`=0 ORDER BY `design_name` ASC");
		if($design_resp && imw_num_rows($design_resp)>0){
			while($row = imw_fetch_object($design_resp)){
				$designs[$row->design_name] = $row->id;
			}
		}
		imw_free_result($design_resp);
	}
	
	/*Lens Material List for Multiselect*/
	$materials = array();
	if($heading=="Lens_Treatment_Name"){
		$material_resp = imw_query("SELECT `id`, `material_name` FROM `in_lens_material` WHERE `del_status`=0 ORDER BY `material_name` ASC");
		if($material_resp && imw_num_rows($material_resp)>0){
			while($row = imw_fetch_object($material_resp)){
				$materials[$row->material_name] = $row->id;
			}
		}
		imw_free_result($material_resp);
	}
	
	//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$stringAllProceduresId="";
	if($heading=="Contact_Lenses_Disinfecting"){
		$sql = "select * from cpt_category_tbl where cpt_category like '%contact lens%' order by cpt_category ASC";
	}
	elseif($heading=="Lens_Type_Name" || $heading=="Lens_Material_Name" || $heading=="Lens_Transition_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Polarized_Name" || $heading=="Lens_Tint_Name" || $heading=="Lens_Color" || $heading=="Lens_Edge_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Design_Name")
	{
		$sql = "select * from cpt_category_tbl where cpt_category like '%optical%' order by cpt_category ASC";
	}
	else{
		$sql = "select * from cpt_category_tbl order by cpt_category ASC";
	}
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
				$stringAllProceduresId.= "'".str_replace("'","",$rowCodes["cpt_fee_id"])."',";
				$stringAllProcedures.="'".str_replace("'","",$code)."',";	
				$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
				$proc_code_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_prac_code"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	$stringAllProceduresId = substr($stringAllProceduresId,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//
	
	if($heading=="Frame_Brand" || $heading=="Progressive_Name" || $heading=="Contact_Lenses_Brand"){
		$sel_manu = "select id, manufacturer_name from in_manufacturer_details where ".$type."_chk='1' and del_status='0' order by manufacturer_name asc";
		$sel_res = imw_query($sel_manu);
		$sel_num = imw_num_rows($sel_res);
		if($sel_num > 0)
			{
			while($sel_row = imw_fetch_array($sel_res))
			{
				$manu_opt .= '<option value="'.$sel_row['id'].'">'.htmlentities($sel_row['manufacturer_name'],ENT_QUOTES).'</option>'; 	 
			}
		}
	}
	
	elseif($heading=="Style_Name"){
		$sel_manu = "select id, frame_source from in_frame_sources where del_status='0' order by frame_source asc";
		$sel_res = imw_query($sel_manu);
		$sel_num = imw_num_rows($sel_res);
		if($sel_num > 0)
			{
			while($sel_row = imw_fetch_array($sel_res))
			{
				$manu_opt .= '<option value="'.$sel_row['id'].'">'.htmlentities($sel_row['frame_source'],ENT_QUOTES).'</option>'; 	 
			}
		}
	}
	
	if($_REQUEST['save']=="Save")
	{
		
		$input_val_arr=array();
		
		for($i=1; $i<=$_POST['totRows']; $i++)
		{
			if(trim($_POST['input_val'.$i])!='')
			{
				$module_name = $_REQUEST['module_name'];
				$module_id_n=$_REQUEST['m_id'];
				$col_name = $_REQUEST['col_name'];
				$rec_prac_code = trim($_POST['item_prac_code'.$i]);
				$procedureId = back_prac_id($rec_prac_code);
				
				$cl_lens_modules = array("lens_usage"=>1, "lens_type_common"=>2);
				$contact_modules = array("contact_lens_replenishment"=>4, "contact_lens_packaging"=>5);
				
				$sql="select id from in_".$module_name." where del_status != '2' and ".$col_name." = '".trim($_POST['input_val'.$i])."'";
				
				$opt_type = 0;
				if(in_array($module_name, array_keys($contact_modules))|| in_array($module_name, array_keys($cl_lens_modules))){
					
					$opt_type = (in_array($module_name, array_keys($contact_modules)))?$contact_modules[$module_name]:$cl_lens_modules[$module_name];
					
					$sql = "SELECT `id` FROM `in_options` WHERE `del_status`!='2' AND `opt_val`='".imw_real_escape_string(trim($_POST['input_val'.$i]))."' AND `opt_type`='".$contact_modules[$module_name]."' and module_id='".$module_id_n."'";
				}

				$res = imw_query($sql);
				$num = imw_num_rows($res);
				if($num==0)
				{
					if($module_name=="color" || $module_name=="frame_color")
					{
						if(trim($_POST['input_val'.$i])!='' || trim($_POST['code_val'.$i])!='')
						{
							$qry = "insert in_".$module_name." set color_name = '".imw_real_escape_string(trim($_POST['input_val'.$i]))."', color_code	= '".imw_real_escape_string(trim($_POST['code_val'.$i]))."', entered_date='$date', entered_time='$time', entered_by='$opr_id'";
							$color_tb = imw_query($qry);
							$last_insert = imw_insert_id(); 
						}
					}
					else if($module_name=="lens_color")
					{
						if(trim($_POST['input_val'.$i])!='' || trim($_POST['code_val'.$i])!='')
						{
							$qry = "insert in_".$module_name." set color_name = '".imw_real_escape_string(trim($_POST['input_val'.$i]))."', color_code	= '".imw_real_escape_string(trim($_POST['code_val'.$i]))."', prac_code='$procedureId', entered_date='$date', entered_time='$time', entered_by='$opr_id' ";
							
							$lens_color_tb = imw_query($qry);
							$last_insert = imw_insert_id(); 
						}
					}
					else if($module_name=="cl_disinfecting")
					{
						if(trim($_POST['input_val'.$i])!='')
						{
							
							$prac_code_id = $_POST['item_prac_code_id'.$i];
							$price	= (float)$_POST['item_price'.$i];
							$qry = "insert in_".$module_name." set name = '".imw_real_escape_string(trim($_POST['input_val'.$i]))."', prac_code='$prac_code_id', price='".$price."', entered_date='$date', entered_time='$time', entered_by='$opr_id' ";
							
							$lens_color_tb = imw_query($qry);
							$last_insert = imw_insert_id(); 
						}
					}
					else if(in_array($module_name, array_keys($contact_modules)) || in_array($module_name, array_keys($cl_lens_modules))){
						$sub_type = (isset($_POST['sub_type'.$i]))?$_POST['sub_type'.$i]:0;
						$qry = "INSERT INTO `in_options` SET `opt_val`='".imw_real_escape_string(trim($_POST['input_val'.$i]))."', `opt_type`='".$opt_type."', `opt_sub_type`='".$sub_type."', `module_id`='".$module_id_n."', `entered_date`='".$date."', `entered_time`='".$time."', `entered_by`='".$opr_id."'";
						$contact_option_tb = imw_query($qry);
						$last_insert = imw_insert_id();
					}
					else
					{
						if($module_name=="lens_type" || $module_name=="lens_material" || $module_name=="lens_transition" || $module_name=="lens_ar" || $module_name=="lens_polarized" || $module_name=="lens_tint" || $module_name=="lens_edge" || $module_name=="lens_progressive" || $module_name=="frame_styles" || $module_name=="return_reason" || $module_name=="lens_ar" || $module_name=="lens_design" || $module_name=="contact_brand")
						{
							$vw_code_qry = "";
							if($module_name=="return_reason"){$prac_qry = "prac_code='$rec_prac_code', prac_code_id='$procedureId'";}
							elseif($module_name=="lens_material"){
								$prac_sv = back_prac_id($_POST['item_prac_code_sv'.$i]);
								$prac_pr = back_prac_id($_POST['item_prac_code_pr'.$i]);
								$prac_bf = back_prac_id($_POST['item_prac_code_bf'.$i]);
								$prac_tf = back_prac_id($_POST['item_prac_code_tf'.$i]);
								$prac_qry = "prac_code_sv='$prac_sv', prac_code_pr='$prac_pr', prac_code_bf='$prac_bf', prac_code_tf='$prac_tf'";
							}
							else{$prac_qry = "prac_code='$procedureId'";}
							
							if( $module_name == 'return_reason'){
								$prac_qry .= ", price='".$_POST['item_price'.$i]."'";
							}
							
							if($module_name=="lens_material" || $module_name=="lens_ar" || $module_name=="lens_design" || $module_name=="contact_brand"){
								if($module_name!="contact_brand"){
									$vw_code = $_POST['vw_code'.$i];
									$vw_code_qry .=", vw_code='$vw_code'";
								}
								$wholesale_price = $_POST['item_wholesale_price'.$i];
								$purchase_price = $_POST['item_purchase_price'.$i];
								$retail_price = $_POST['item_retail_price'.$i];
								
								if($module_name=="lens_material"){
									$wholesale_price = array('sv'=>$wholesale_price, 'pr'=>$wholesale_price, 'bf'=>$wholesale_price, 'tf'=>$wholesale_price);
									$purchase_price = array('sv'=>$purchase_price, 'pr'=>$purchase_price, 'bf'=>$purchase_price, 'tf'=>$purchase_price);
									$retail_price = array('sv'=>$retail_price, 'pr'=>$retail_price, 'bf'=>$retail_price, 'tf'=>$retail_price);
									
									$wholesale_price = json_encode($wholesale_price);
									$purchase_price = json_encode($purchase_price);
									$retail_price = json_encode($retail_price);
									
									$vw_code_qry .=", wholesale_price='$wholesale_price'";
									$vw_code_qry .=", purchase_price='$purchase_price'";
									$vw_code_qry .=", retail_price='$retail_price'";
								}
								else{
									$vw_code_qry .=", wholesale_price='$wholesale_price'";
									$vw_code_qry .=", purchase_price='$purchase_price'";
									$vw_code_qry .=", retail_price='$retail_price'";
								}
							}
							
							if($module_name=="lens_design"){
								$vw_code_type = $_POST['vw_code_type'.$i];
								$vw_code_qry .= ", lens_vw_code='$vw_code_type'";
							}
							if($module_name=="lens_type"){
								$vw_code_type = $_POST['vw_code_type'.$i];
								$vw_code_qry .= ", vw_code='$vw_code_type'";
							}
							
							$qry = "insert in_".$module_name." set ".$col_name." = '".imw_real_escape_string(trim($_POST['input_val'.$i]))."', $prac_qry $vw_code_qry, entered_date='$date', entered_time='$time', entered_by='$opr_id'";
						}
						else
						{
							if($module_name=="frame_types"){
								$vw_code = $_POST['vw_code'.$i];
								$vw_code_qry =", vw_code='$vw_code'";
								
							}
						$qry = "insert in_".$module_name." set ".$col_name." = '".imw_real_escape_string(trim($_POST['input_val'.$i]))."', entered_date='$date', entered_time='$time', entered_by='$opr_id' $vw_code_qry ";
						}
					
					//echo $qry;
						$main_tb = imw_query($qry);
						$last_insert = imw_insert_id();
						if($module_name=="frame_sources")
						{
							if($_POST['sel_frm_manufact'.$i]!='')
							{								
								$brand_qry = "insert in_brand_manufacture set brand_id = '".$last_insert."', manufacture_id = '".imw_real_escape_string($_POST['sel_frm_manufact'.$i])."' ";
								$brand_tb = imw_query($brand_qry);
							}
						}
						elseif($module_name=="contact_brand"){
							if($_POST['sel_manufact'.$i]!='')
							{								
								$brand_qry = "insert into in_contact_brand_manufacture set brand_id = '".$last_insert."', manufacture_id = '".imw_real_escape_string($_POST['sel_manufact'.$i])."' ";
								$brand_tb = imw_query($brand_qry);
							}
						}
						elseif($module_name=="frame_styles")
						{
							if($_POST['sel_manufact'.$i]!='')
							{
								for($k=0;$k<count($_POST['sel_manufact'.$i]);$k++)
								{
									$brand_qry = "insert in_style_brand set style_id = '".$last_insert."', brand_id = '".imw_real_escape_string($_POST['sel_manufact'.$i][$k])."' ";
								
									$brand_tb = imw_query($brand_qry);
								}
							}
						}
						elseif($module_name=="lens_progressive")
						{
							if($_POST['sel_manufact'.$i]!='')
							{
								for($k=0;$k<count($_POST['sel_manufact'.$i]);$k++)
								{
									$brand_qry = "insert in_progressive_manufacture set progressive_id = '".$last_insert."', manufacture_id = '".imw_real_escape_string($_POST['sel_manufact'.$i][$k])."' ";
								
									$brand_tb = imw_query($brand_qry);
								}
							}
						}
						elseif($module_name=="lens_material"){
							$design_ids = $_POST['item_design'.$i];
							$material_designs = "";
							foreach($design_ids as $design){
								$material_designs .= "(".$last_insert.", ".$design."),";
							}
							$material_designs = rtrim($material_designs, ",");
							if($material_designs!=""){
								/*Associate Material with Designs*/
								$material_design_qry = "INSERT INTO `in_lens_material_design`(`material_id`, `design_id`) VALUES".$material_designs;
								imw_query($material_design_qry);
							}
						}
						elseif($module_name=="lens_ar"){
							$material_ids = $_POST['item_material'.$i];
							$treatment_materials = "";
							foreach($material_ids as $material){
								$treatment_materials .= "(".$last_insert.", ".$material."),";
							}
							$treatment_materials = rtrim($treatment_materials, ",");
							if($treatment_materials!=""){
								/*Associate Treatment with Materials*/
								$design_material_qry = "INSERT INTO `in_lens_ar_material`(`ar_id`, `material_id`) VALUES".$treatment_materials;
								imw_query($design_material_qry);
							}
						}
					}
				}
				else
				{ 
					$already=1;
				}
				
				if($last_insert)
				{
					echo "<script>window.opener.main_iframe.admin_iframe.location.reload(); window.close();</script>";
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
window.opener = window.opener.main_iframe.admin_iframe;
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" language="javascript">
var customarrayProcedure= new Array();
$(document).ready(function(){

duplicate = function(item_name)
{
		$(".input_val").each(function(index)
		{
			 if($.trim($(this).val())!="" && $.trim($(this).val()).toLowerCase() == $.trim($(item_name).val()).toLowerCase() && $(this).attr('id') != $(item_name).attr('id'))
			{
				top.falert($(item_name).val()+' Already Exist');
				$(item_name).val('');
				setTimeout(function(){$(item_name).focus(); },0);
			}	
		});
}

});
</script>

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<?php if($heading=="Contact_Lenses_Disinfecting" || $heading=="Lens_Material_Name"): ?>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<?php endif; ?>

<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>

<style type="text/css">
.rptDropDown>a.multiSelect>span{width:100px !important;font-size:13px;}
.rptDropDown>a.multiSelect{padding-bottom:0;}
<?php
if($heading=="Lens_Material_Name"){
echo '.listheading.pl5 span{float:left;}';
}
?>
</style>

<script>
<?php
if($heading=="Lens_Material_Name"){
	echo "var designs=".json_encode($designs).";";
}
elseif($heading=="Lens_Treatment_Name"){
	echo "var materials=".json_encode($materials).";";
}
?>

function createRows(startNum, NoOfRows)
{	
	var rowData_b='';
	var newNum = parseInt(startNum) + 1;
	var totalrows='';
	var heading = '<?php echo $heading; ?>';
	var manu_opt = '<?php echo $manu_opt; ?>';
	var module_name = '<?php echo $_REQUEST['module_name']; ?>';
	
	if(NoOfRows>0)
	{		
		oldId=startNum;
		
		totalrows = startNum+NoOfRows;
		
		for(i=newNum; i<=totalrows; i++)
		{	
			rowData_b='';
			
			rowData_b+='<tr id="tr_b_'+i+'">';
			if(heading=="Contact_Lenses_Color" || heading=="Frame_Color" || heading=="Lens_Colors") {
				rowData_b+='<td class="module_label"><input type="text" class="input_val" onChange="javascript:duplicate(this)" name="input_val'+i+'" id="input_val'+i+'" value="" style="width:150px;"/>&nbsp;<span>Color Name</span>';
				rowData_b+='</td>';
				rowData_b+='<td class="module_label"><input type="text" name="code_val'+i+'" id="code_val'+i+'" value="" style="width:150px;"/>&nbsp;<span>Color Code</span>';
				rowData_b+='</td>';
			}
			else 
			{
				if(heading=="Lens_Material_Name" || heading=="Lens_Treatment_Name" || heading=="Lens_Design_Name") { var m_wdth = '210'; } else { var m_wdth = '250'; }
				rowData_b+='<td><input autocomplete="off" type="text" class="input_val" onChange="javascript:duplicate(this)" name="input_val'+i+'" id="input_val'+i+'" value="" style="width:'+m_wdth+'px;"/>';
				rowData_b+='</td>';
				if(heading=="Contact_Lenses_Brand") {
					rowData_b+='<td class="rptDropDown"><select name="sel_manufact'+i+'" id="sel_manufact'+i+'" style="width:125px;"><option value="">Select</option>';
					rowData_b+=manu_opt;
					rowData_b+='</select></td>';
				}
				else if(heading=="Progressive_Name") {
					rowData_b+='<td class="rptDropDown"><select name="sel_manufact'+i+'" id="sel_manufact'+i+'" style="width:160px;" multiple="multiple"><option value="">Select</option>';
					rowData_b+=manu_opt;
					rowData_b+='</select></td>';
				}
				
				if(heading=="Style_Name" )
				{
					rowData_b+='<td class="rptDropDown"><select name="sel_manufact'+i+'" id="sel_manufact'+i+'" style="width:160px;" multiple="multiple"><option value="">Select</option>';
					rowData_b+=manu_opt;
					rowData_b+='</select></td>';
					rowData_b+='<td><input type="text" id="item_prac_code'+i+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code'+i+'" style=""/></td>';
				}
				
				if(heading=="Frame_Brand")
				{
					rowData_b+='<td class="rptDropDown"><select name="sel_frm_manufact'+i+'" id="sel_frm_manufact'+i+'" style="width:178px;"><option value="">Select</option>';
					rowData_b+=manu_opt;
					rowData_b+='</select></td>';
				}
				if(heading=="Lens_Type_Name"  || heading=="Lens_Design_Name"){
					rowData_b+='<td><select name="vw_code_type'+i+'" id="vw_code_type'+i+'" style="width:100px;"><option value="">Type</option><option value="BFF">BiFocal</option><option value="PAL">Progressive</option><option value="SV">Single Vision</option><option value="TFF">TriFocal</option></select></td>';
				}
				if(heading=="Lens_Material_Name" || heading=="Lens_Treatment_Name" || heading=="Lens_Design_Name" || heading=="Frame_Type"){
					rowData_b+='<td><input autocomplete="off" type="text" name="vw_code'+i+'" id="vw_code'+i+'" style="width:130px;"/></td>';
				}
				if(heading=="Lens_Type_Name" || heading=="Lens_Material_Name" || heading=="Lens_Transition_Name" || heading=="Lens_Treatment_Name" || heading=="Lens_Polarized_Name" || heading=="Lens_Tint_Name" || heading=="Lens_Color" || heading=="Lens_Edge_Name" || heading=="Progressive_Name" || heading=="Contact_Lenses_Disinfecting" || module_name=="return_reason" || heading=="Lens_Treatment_Name" || heading=="Lens_Design_Name" || heading=="Contact_Lenses_Brand") {
					
				if(heading=="Progressive_Name") { var wdth = '165'; } else { var wdth = '250'; }
				
				if(heading=="Lens_Material_Name"){
					rowData_b+= '<td>'
						rowData_b+= '<input autocomplete="off" type="text" id="item_prac_code_sv'+i+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_sv'+i+'" style="width:130px;" class="prac_lens_material" />';
					rowData_b+= '</td>';
					
					rowData_b+= '<td>';
						rowData_b+= '<input autocomplete="off" type="text" id="item_prac_code_pr'+i+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_pr'+i+'" style="width:130px;" class="prac_lens_material" />';
					rowData_b+= '</td>';
					
					rowData_b+= '<td>';
						rowData_b+= '<input autocomplete="off" type="text" id="item_prac_code_bf'+i+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_bf'+i+'" style="width:130px;" class="prac_lens_material" />';
					rowData_b+= '</td>';
					
					rowData_b+= '<td>';
						rowData_b+= '<input autocomplete="off" type="text" id="item_prac_code_tf'+i+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_tf'+i+'" style="width:130px;" class="prac_lens_material" />';
					rowData_b+= '</td>';
				}
				else{
					if(heading=="Lens_Type_Name" || heading=="Lens_Treatment_Name" || heading=="Lens_Design_Name" || heading=="Contact_Lenses_Brand") { var wdth = '130'; } else { var wdth = '250'; }
						rowData_b+='<td><input autocomplete="off" type="text" id="item_prac_code'+i+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code'+i+'" style="width:'+wdth+'px;"/>';
				}
					
					if(heading=="Contact_Lenses_Disinfecting"){
						rowData_b+='<input type="hidden" id="item_prac_code_id'+i+'" name="item_prac_code_id'+i+'" value="" />';
					}
					rowData_b+='</td>';
					
					if(heading=="Lens_Material_Name"){
						rowData_b+='<td class="rptDropDown">';
							rowData_b+='<select name="item_design'+i+'" id="item_design'+i+'">';
								rowData_b+='<option value="">Lens Design</option>';
								$.each(designs, function(key, value){
									rowData_b+='<option value="'+value+'">'+key+'</option>';
								});
							rowData_b+='</select>';
						rowData_b+='</td>';
					}
					
					if(heading=="Lens_Treatment_Name"){
						rowData_b+='<td class="rptDropDown">';
							rowData_b+='<select name="item_material'+i+'" id="item_material'+i+'">';
								rowData_b+='<option value="">Lens Material</option>';
								$.each(materials, function(key, value){
									rowData_b+='<option value="'+value+'">'+key+'</option>';
								});
							rowData_b+='</select>';
						rowData_b+='</td>';
					}
					
					if(heading=="Lens_Material_Name" || heading=="Lens_Design_Name" || heading=="Lens_Treatment_Name" || heading=="Contact_Lenses_Brand"){
						rowData_b+='<td>';
							rowData_b+='<input type="text" name="item_wholesale_price'+i+'" id="item_wholesale_price'+i+'" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />';
						rowData_b+='</td>';
						rowData_b+='<td>';
							rowData_b+='<input type="text" name="item_purchase_price'+i+'" id="item_purchase_price'+i+'" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />';
						rowData_b+='</td>';
						rowData_b+='<td>';
							rowData_b+='<input type="text" name="item_retail_price'+i+'" id="item_retail_price'+i+'" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />';
						rowData_b+='</td>';
					 }
				}
				if( module_name=="return_reason" || module_name=='cl_disinfecting' ){
					rowData_b+='<td>';
						rowData_b+='<input type="text" name="item_price'+i+'" id="item_price'+i+'" value="" class="input_val" style="width:200px;" onChange="convert_float(this);" />';
					rowData_b+='</td>';
				}
				if( (heading=='Lenses_Usage' && module_name=='lens_usage') || (heading=='Lenses_Type_Common' && module_name=='lens_type_common') ){
					rowData_b += '<td><select name="sub_type'+i+'" id="sub_type'+i+'" style="width:132px;"><option value="0">Both</option><option value="1">Contact Lens</option><option value="2">Lens</option></td>';
				 }
			}
			rowData_b+='</tr>';
			
			$("#tr_b_"+oldId).after(rowData_b);
			oldId=i;
			var dd_pro = new Array();
			dd_pro["listHeight"] = 100;
			dd_pro["noneSelected"] = "Select All";
<?php if($heading=="Lens_Material_Name"): ?>
	$("#item_design"+i).multiSelect(dd_pro);
<?php elseif($heading=="Lens_Treatment_Name"): ?>
	$("#item_material"+i).multiSelect(dd_pro);
<?php endif; ?>

<?php if($heading!="Contact_Lenses_Disinfecting"){ ?>
	if(heading!="Contact_Lenses_Brand"){
		$("#sel_manufact"+i).multiSelect(dd_pro);
	}
	var obj9 = new actb(document.getElementById('item_prac_code'+i),customarrayProcedure);
<?php }
	elseif($heading=="Lens_Material_Name"){ ?>
		$(".prac_lens_material").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'praCode',
			showAjaxVals: 'defaultCodeO',
			maxVals: 5
		});
<?php
	}
else{ ?>
	$("#item_prac_code"+i).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC',
		hidIDelem: document.getElementById("item_prac_code_id"+i),
		maxVals: 5
	});
<?php
} ?>
		}
		document.getElementById('totRows').value= i-1;
	}
}

var rowData_c='';
var tr  = '';
	
function addrow()	
{
	var heading_ad = '<?php echo $heading; ?>';
	var manu_opt_ad = '<?php echo $manu_opt; ?>';
	var module_name = '<?php echo $_REQUEST['module_name']; ?>';
	var getRows = $(".countrow tr").size();
	y = getRows+1;
	
	rowData_c+='<tr id="tr_b_'+y+'">';
	if(heading_ad=="Contact_Lenses_Color" || heading_ad=="Frame_Color"  || heading_ad=="Lens_Colors") {
		rowData_c+='<td class="module_label"><input type="text" class="input_val" onChange="javascript:duplicate(this)" name="input_val'+y+'" id="input_val'+y+'" value="" style="width:150px;"/>&nbsp;<span>Color Name</span>';
		rowData_c+='</td>';
		rowData_c+='<td class="module_label"><input type="text" name="code_val'+y+'" id="code_val'+y+'" value="" style="width:150px;"/>&nbsp;<span>Color Code</span>';
		rowData_c+='</td>';
	}
	else 
	{
		if(heading_ad=="Lens_Material_Name" || heading_ad=="Lens_Treatment_Name" || heading_ad=="Lens_Design_Name") { var m_wdth = '210'; } else { var m_wdth = '250'; }
		rowData_c+='<td><input autocomplete="off" type="text" class="input_val" onChange="javascript:duplicate(this)" name="input_val'+y+'" id="input_val'+y+'" value="" style="width:'+m_wdth+'px;"/>';
		rowData_c+='</td>';
		if(heading_ad=="Contact_Lenses_Brand") {
			rowData_c+='<td class="rptDropDown"><select name="sel_manufact'+y+'" id="sel_manufact'+y+'" style="width:125px;"><option value="">Select</option>';
			rowData_c+=manu_opt_ad;
			rowData_c+='</select></td>';
		}
		else if(heading_ad=="Progressive_Name") {
			rowData_c+='<td class="rptDropDown"><select name="sel_manufact'+y+'" id="sel_manufact'+y+'" style="width:160px;" multiple="multiple"><option value="">Select</option>';
			rowData_c+=manu_opt_ad;
			rowData_c+='</select></td>';
		}
		
		if(heading_ad=="Style_Name" )
		{
			rowData_c+='<td class="rptDropDown"><select name="sel_manufact'+y+'" id="sel_manufact'+y+'" style="width:160px;" multiple="multiple"><option value="">Select</option>';
			rowData_c+=manu_opt_ad;
			rowData_c+='</select></td>';
			rowData_c+='<td><input type="text" id="item_prac_code'+y+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code'+y+'" style=""/></td>';
		}
		
		if(heading_ad=="Frame_Brand")
		{
			rowData_c+='<td class="rptDropDown"><select name="sel_frm_manufact'+y+'" id="sel_frm_manufact'+y+'" style="width:178px;"><option value="">Select</option>';
			rowData_c+=manu_opt_ad;
			rowData_c+='</select></td>';
		}
		if(heading_ad=="Lens_Type_Name" || heading_ad=="Lens_Design_Name"){
			rowData_c+='<td><select name="vw_code_type'+y+'" id="vw_code_type'+y+'" style="width:100px;"><option value="">Type</option><option value="BFF">BiFocal</option><option value="PAL">Progressive</option><option value="SV">Single Vision</option><option value="TFF">TriFocal</option></select></td>';
		}
		if(heading_ad=="Lens_Material_Name" || heading_ad=="Lens_Treatment_Name" || heading_ad=="Lens_Design_Name" || heading_ad=="Frame_Type"){
			rowData_c+='<td><input autocomplete="off" type="text" name="vw_code'+y+'" id="vw_code'+y+'" style="width:130px;"/></td>';
		}
		
	if(heading_ad=="Lens_Type_Name" || heading_ad=="Lens_Material_Name" || heading_ad=="Lens_Transition_Name" || heading_ad=="Lens_Treatment_Name" || heading_ad=="Lens_Polarized_Name" || heading_ad=="Lens_Tint_Name" || heading_ad=="Lens_Color" || heading_ad=="Lens_Edge_Name" || heading_ad=="Progressive_Name" || heading_ad=="Contact_Lenses_Disinfecting" || module_name=="return_reason" || heading_ad=="Lens_Treatment_Name" || heading_ad=="Lens_Design_Name" || heading_ad=="Contact_Lenses_Brand") {
		if(heading_ad=="Progressive_Name") { var wdth = '165'; } else { var wdth = '250'; }
		
		if(heading_ad=="Lens_Material_Name"){
			rowData_c+= '<td>'
				rowData_c+= '<input autocomplete="off" type="text" id="item_prac_code_sv'+y+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_sv'+y+'" style="width:130px;" class="prac_lens_material" />';
			rowData_c+= '</td>';
			
			rowData_c+= '<td>';
				rowData_c+= '<input autocomplete="off" type="text" id="item_prac_code_pr'+y+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_pr'+y+'" style="width:130px;" class="prac_lens_material" />';
			rowData_c+= '</td>';
			
			rowData_c+= '<td>';
				rowData_c+= '<input autocomplete="off" type="text" id="item_prac_code_bf'+y+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_bf'+y+'" style="width:130px;" class="prac_lens_material" />';
			rowData_c+= '</td>';
			
			rowData_c+= '<td>';
				rowData_c+= '<input autocomplete="off" type="text" id="item_prac_code_tf'+y+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code_tf'+y+'" style="width:130px;" class="prac_lens_material" />';
			rowData_c+= '</td>';
		}
		else{
			if(heading_ad=="Lens_Type_Name" || heading_ad=="Lens_Treatment_Name" || heading_ad=="Lens_Design_Name" || heading_ad=="Contact_Lenses_Brand") { var wdth = '130'; } else { var wdth = '250'; }
				rowData_c+='<td><input autocomplete="off" type="text" id="item_prac_code'+y+'" onChange="show_price_from_praccode(this,\'\',\'add_new\');" name="item_prac_code'+y+'" style="width:'+wdth+'px;"/>';
		}
			
			if(heading_ad=="Contact_Lenses_Disinfecting"){
				rowData_c+='<input type="hidden" id="item_prac_code_id'+y+'" name="item_prac_code_id'+y+'" value="" />';
			}
			rowData_c+='</td>';
			
			if(heading_ad=="Lens_Material_Name"){
				rowData_c+='<td class="rptDropDown">';
					rowData_c+='<select name="item_design'+y+'" id="item_design'+y+'">';
						rowData_c+='<option value="">Lens Design</option>';
						$.each(designs, function(key, value){
							rowData_c+='<option value="'+value+'">'+key+'</option>';
						});
					rowData_c+='</select>';
				rowData_c+='</td>';
			}
			
			if(heading_ad=="Lens_Treatment_Name"){
				rowData_c+='<td class="rptDropDown">';
					rowData_c+='<select name="item_material'+y+'" id="item_material'+y+'">';
						rowData_c+='<option value="">Lens Material</option>';
						$.each(materials, function(key, value){
							rowData_c+='<option value="'+value+'">'+key+'</option>';
						});
					rowData_c+='</select>';
				rowData_c+='</td>';
			}
					
			if(heading_ad=="Lens_Material_Name" || heading_ad=="Lens_Design_Name" || heading_ad=="Lens_Treatment_Name" || heading_ad=="Contact_Lenses_Brand"){
				rowData_c+='<td>';
					rowData_c+='<input type="text" name="item_wholesale_price'+y+'" id="item_wholesale_price'+y+'" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />';
				rowData_c+='</td>';
				rowData_c+='<td>';
					rowData_c+='<input type="text" name="item_purchase_price'+y+'" id="item_purchase_price'+y+'" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />';
				rowData_c+='</td>';
				rowData_c+='<td>';
					rowData_c+='<input type="text" name="item_retail_price'+y+'" id="item_retail_price'+y+'" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />';
				rowData_c+='</td>';
			 }
		}
		if( module_name=="return_reason" || module_name=='cl_disinfecting'){
			rowData_c+='<td>';
				rowData_c+='<input type="text" name="item_price'+y+'" id="item_price'+y+'" value="" class="input_val" style="width:200px;" onChange="convert_float(this);" />';
			rowData_c+='</td>';
		}
		if( (heading_ad=='Lenses_Usage' && module_name=='lens_usage') || (heading_ad=='Lenses_Type_Common' && module_name=='lens_type_common') ){
			rowData_c += '<td><select name="sub_type'+y+'" id="sub_type'+y+'" style="width:132px;"><option value="0">Both</option><option value="1">Contact Lens</option><option value="2">Lens</option></td>';
		 }
	}
	
	rowData_c+='</tr>';
	
	$("#tr_b_"+getRows).after(rowData_c); // ADD NEW ROW
	
	rowData_c='';
	var dd_pro = new Array();
	dd_pro["listHeight"] = 100;
	dd_pro["noneSelected"] = "Select All";
<?php if($heading=="Lens_Material_Name"): ?>
	$("#item_design"+y).multiSelect(dd_pro);
<?php elseif($heading=="Lens_Treatment_Name"): ?>
	$("#item_material"+y).multiSelect(dd_pro);
<?php endif; ?>

<?php if($heading!="Contact_Lenses_Disinfecting"){ ?>
	if(heading_ad!="Contact_Lenses_Brand"){
		$("#sel_manufact"+y).multiSelect(dd_pro);
	}
	var obj10 = new actb(document.getElementById('item_prac_code'+y),customarrayProcedure);
<?php }
	if($heading=="Lens_Material_Name"){ ?>
		$(".prac_lens_material").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'praCode',
			showAjaxVals: 'defaultCodeO',
			maxVals: 5
		});
<?php
	}
else{ ?>
	$("#item_prac_code"+y).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC',
		hidIDelem: document.getElementById("item_prac_code_id"+y),
		maxVals: 5
	});
<?php } ?>
	
	if(getRows>=1)
	{
		$("#removebtn").show();
	}
	$("#totRows").val(y);
}

var remove_row='';
function removerow()
{
	remove_row = $(".countrow tr").size();
	
	if(remove_row>1)
	{
		if(remove_row==2)
		{
			$("#removebtn").hide();
		}
		
		$("#tr_b_"+remove_row).remove();
	}
	$("#totRows").val(remove_row-1);
}

<?php 
if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php }
if($stringAllProceduresId!=""){  ?>
	var customarrayProcedureId = new Array(<?php echo remLineBrk($stringAllProceduresId); ?>);
<?php } ?>

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
<body style="overflow:hidden;">
<?php 
if($heading=="Frame_Brand" || $heading=="Style_Name")
		{ $wid = "665"; }
		else if($heading=="Contact_Lenses_Color" || $heading=="Frame_Color"  || $heading=="Lens_Type_Name" || $heading=="Lens_Transition_Name" || $heading=="Lens_Polarized_Name" || $heading=="Lens_Tint_Name" || $heading=="Lens_Color" || $heading=="Lens_Edge_Name")
		{ $wid = "560"; }
		elseif($heading=="Progressive_Name" || $heading=="Lens_Design_Name" || $heading=="Lens_Treatment_Name")
		{
			$wid = "1040";
		}
		elseif($heading=="Lens_Material_Name"){
			$wid = "1479";
		}
		elseif($heading=="Contact_Lenses_Brand"){
			$wid = "927";
		}
		elseif($heading=="Frame_Type"){
			$wid = "480";
		}
		elseif( $_REQUEST['module_name']=="return_reason" || $heading=="Contact_Lenses_Disinfecting"){
			$wid = "780";
		}
		elseif( ($heading=='Lenses_Usage' && $_REQUEST['module_name']=='lens_usage') || ($heading=='Lenses_Type_Common' && $_REQUEST['module_name']=='lens_type_common') ){
			$wid = "440";
		}
		else { $wid = "290"; }
?>
    <div style="width:<?php echo $wid; ?>px; margin:0 auto;">
       <div class="module_border">
        	<form name="addnew" id="firstform" action="" method="post">
             <input type="hidden" name="totRows" id="totRows" value="" />
             <input type="hidden" name="module_name" value="<?php echo $_REQUEST['module_name']; ?>" />
             <input type="hidden" name="col_name" value="<?php echo $_REQUEST['col_name']; ?>" />
        		<div class="listheading pl5">
					<?php
						if($heading=="Lens_Design_Name"){
							$lmargin = "230";
						}
						elseif($heading=="Lens_Material_Name" || $heading=="Lens_Treatment_Name"){
							$lmargin = "219";
						}
						else{
							$lmargin = "270";
						}
					?>
                	<span style="width:<?php echo $lmargin; ?>px; float:left;">
						Add <?php $name = str_replace(array('Lens_Type_Common', '_'), array('Lens Type', ' '),$heading); 
						echo $name;
						?>
					</span>
					<?php if($heading=="Frame_Brand" || $heading=="Progressive_Name" || $heading=="Contact_Lenses_Brand"){
						$width = ($heading=="Contact_Lenses_Brand")?132:188;
					?>
						<span style="width:<?php echo $width; ?>px; float:left;">Manufacturer</span>
					<?php } ?>
					<?php if($heading=="Style_Name"){ ?>
						<span style="width:187px;float:left">Brand</span>
					<?php } ?>
					<?php if($heading=="Contact_Lenses_Color" || $heading=="Frame_Color"){ ?>
						<span>Color Code</span>
					<?php } ?>
					<?php if($heading=="Lens_Type_Name" || $heading=="Lens_Design_Name"){?>
						<span style="padding-left:15px; margin-right:60px;">Type</span>
					<?php } ?>
                    <?php if($heading=="Lens_Material_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Design_Name" || $heading=="Frame_Type"){?>
						<span style="padding-left:15px; margin-right:30px;">VisionWeb Code</span>
					<?php } ?>
					<?php if($heading=="Lens_Type_Name" || $heading=="Lens_Material_Name" || $heading=="Lens_Transition_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Polarized_Name" || $heading=="Lens_Tint_Name" || $heading=="Lens_Color" || $heading=="Lens_Edge_Name" || $heading=="Progressive_Name" || $heading=="Style_Name" || $heading=="Contact_Lenses_Disinfecting" || $heading=="Lens_Design_Name" || $heading=="Contact_Lenses_Brand" || $_REQUEST['module_name']=="return_reason"){ 
						if($heading=="Lens_Design_Name"){ ?>
							<span style="padding-left:10px;margin-right:24px;">Billing Prac. Code</span>
					<?php
						}
						elseif($heading=="Lens_Material_Name"){ ?>
								<span style="padding-left:5px;width:130px;margin-right:10px;" title="Single Vision">Prac Code SV</span>
								<span style="padding-left:5px;width:130px;margin-right:10px;" title="Progressive">Prac Code Prog.</span>
								<span style="padding-left:5px;width:130px;margin-right:10px;" title="BiFocal">Prac Code BiFocal</span>
								<span style="padding-left:5px;width:135px;margin-right:10px;" title="TriFocal">Prac Code TriFocal</span>
					<?php
						}
						else{
					?>
							<span <?php echo($heading=="Lens_Treatment_Name" || $heading=="Contact_Lenses_Brand")?"style=\"padding-left:5px;margin-right:68px;\"":"" ;?>>Prac Code</span>
					<?php }
					}
					if($heading=="Lens_Material_Name"){ ?>
						<span style="padding-left:5px;margin-right:48px;">Lens Design</span>
					<?php }
					if($heading=="Lens_Treatment_Name"){ ?>
						<span style="padding-left:5px;margin-right:48px;">Lens Material</span>
					<?php }
					if($heading=="Lens_Material_Name" || $heading=="Lens_Design_Name" || $heading=="Lens_Treatment_Name" || $heading=="Contact_Lenses_Brand"){ ?>
						<span style="margin-right:14px;">Wholesale Price</span>
						<span style="padding-left:2px;margin-right:18px;">Purchase Price</span>
						<span style="padding-left:<?php echo ($heading=="Contact_Lenses_Brand")?0:15; ?>px;">Retail Price</span>
					<?php 
					}
					if($_REQUEST['module_name']=="return_reason" || $heading == 'Contact_Lenses_Disinfecting' ){ ?>
						<span style="margin-left:198px;">Price</span>
					<?php	
					}
					
					if( ($heading=='Lenses_Usage' && $_REQUEST['module_name']=='lens_usage') || ($heading=='Lenses_Type_Common' && $_REQUEST['module_name']=='lens_type_common') ){
						echo '<span style="margin-left:10px;">Availability</span>';
					}
					
					?>
				</div>
                <div style="height:250px; overflow-x:hidden; overflow-y:scroll">
                <table class="table_collapse countrow table_cell_padd5 module_border">
                	<tr id="tr_b_1">
                    	<?php if($heading=="Contact_Lenses_Color" || $heading=="Frame_Color"  || $heading=="Lens_Colors") { ?>
                         <td class="module_label">
                        	<input type="text" class="input_val" onChange="javascript:duplicate(this)" name="input_val1" id="input_val1" value="" style="width:150px;"/>
                            <span>Color Name</span>
                         </td>
                         <td class="module_label">
                         	<input type="text" name="code_val1" onChange="javascript:duplicate(this)" id="code_val1" value="" style="width:150px;"/>
                            <span>Color Code</span>
                         </td>
                         <?php } else {  ?>
                    	<td>
                        	<input autocomplete="off" type="text" class="input_val" name="input_val1" id="input_val1" value="" style="width:<?php if($heading=="Lens_Material_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Design_Name") { echo '210'; } else { echo '250'; } ?>px;"/>
                         </td>
                         
                         <?php if($heading=="Contact_Lenses_Brand"){ ?>
                         <td class="rptDropDown">
                         <select name="sel_manufact1" id="sel_manufact1" style="width:125px;">
                         <option value="">Select</option>
                         <?php echo $manu_opt; ?>
                         </select>
                         </td>
                         <?php }elseif($heading=="Progressive_Name"){ ?>
                         <td class="rptDropDown">
                         <select name="sel_manufact1" id="sel_manufact1" style="width:160px;" multiple="multiple">
                         <option value="">Select</option>
                         <?php echo $manu_opt; ?>
                         </select>
                         </td>
                         <?php } elseif($heading=="Frame_Brand"){ ?>
						 <td class="rptDropDown">
                         <select name="sel_frm_manufact1" id="sel_frm_manufact1" style="width:178px;">
                         <option value="">Select</option>
                         <?php echo $manu_opt; ?>
                         </select>
                         </td>
                         <?php }elseif($heading=="Style_Name"){
							 ?>
							   <td class="rptDropDown">
                         <select name="sel_manufact1" id="sel_manufact1" style="width:160px;" multiple="multiple">
                         <option value="">Select</option>
                         <?php echo $manu_opt; ?>
                         </select>
                         </td>
                         <td><input type="text" id="item_prac_code1" onChange="show_price_from_praccode(this,'','add_new');" name="item_prac_code1" style=""/></td>
						 <?php }
						 	if($heading=="Lens_Type_Name" || $heading=="Lens_Design_Name") { ?>
                         <td>
                         	 <select name="vw_code_type1" id="vw_code_type1" onChange="selectCurrentCheck('<?php echo $i; ?>');" style="width:100px;">
                                <option value="">Type</option>
                                <option value="BFF">BiFocal</option>
                                <option value="PAL">Progressive</option>
                                <option value="SV">Single Vision</option>
                                <option value="TFF">TriFocal</option>
                            </select>
                         </td>
                         <?php }
						 if($heading=="Lens_Material_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Design_Name" || $heading=="Frame_Type") { ?>
                         <td>
                         	<input autocomplete="off" type="text" id="vw_code1" name="vw_code1" style="width:130px;"/>
                         </td>
                         <?php } 
						 
						 if($heading=="Lens_Type_Name" || $heading=="Lens_Transition_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Polarized_Name" || $heading=="Lens_Tint_Name" || $heading=="Lens_Color" || $heading=="Lens_Edge_Name" || $heading=="Progressive_Name" || $heading=="Contact_Lenses_Disinfecting" || $_REQUEST['module_name']=="return_reason" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Design_Name" || $heading=="Contact_Lenses_Brand") { ?>
                         <td>
                         	<input autocomplete="off" type="text" id="item_prac_code1" onChange="show_price_from_praccode(this,'','add_new');" name="item_prac_code1" style="width:<?php if($heading=="Progressive_Name") { echo '165'; }else if($heading=="Lens_Type_Name" || $heading=="Lens_Treatment_Name" || $heading=="Lens_Design_Name" || $heading=="Contact_Lenses_Brand") { echo '130'; } else { echo '250'; } ?>px;"/>
							<?php if($heading=="Contact_Lenses_Disinfecting"): ?>
								<input type="hidden" id="item_prac_code_id1" name="item_prac_code_id1" value="" />
							<?php endif; ?>
                         </td>
                         <?php }
						 elseif($heading=="Lens_Material_Name"){ ?>
						 <td>
							 <input autocomplete="off" type="text" id="item_prac_code_sv1" onChange="show_price_from_praccode(this,'','add_new');" name="item_prac_code_sv1" style="width:130px;" class="prac_lens_material" />
						</td>
						<td>
							 <input autocomplete="off" type="text" id="item_prac_code_pr1" onChange="show_price_from_praccode(this,'','add_new');" name="item_prac_code_pr1" style="width:130px;" class="prac_lens_material" />
						</td>
						<td>
							 <input autocomplete="off" type="text" id="item_prac_code_bf1" onChange="show_price_from_praccode(this,'','add_new');" name="item_prac_code_bf1" style="width:130px;" class="prac_lens_material" />
						</td>
						<td>
							 <input autocomplete="off" type="text" id="item_prac_code_tf1" onChange="show_price_from_praccode(this,'','add_new');" name="item_prac_code_tf1" style="width:130px;" class="prac_lens_material" />
						</td>
					<?php }
						 if($heading=="Lens_Material_Name"){ ?>
							<td class="rptDropDown">
								<select name="item_design1" id="item_design1">	
									<option value="">Lens Design</option>
									<?php
									foreach($designs as $key=>$value){
										echo '<option value="'.$value.'">'.$key."</option>";
									}
									?>
								</select>
							</td>
						<?php }
						if($heading=="Lens_Treatment_Name"){ ?>
							<td class="rptDropDown">
								<select name="item_material1" id="item_material1">	
									<option value="">Lens Material</option>
									<?php
									foreach($materials as $key=>$value){
										echo '<option value="'.$value.'">'.$key."</option>";
									}
									?>
								</select>
							</td>
						<?php }
						 if($heading == "Lens_Design_Name" || $heading == "Lens_Material_Name" || $heading=="Lens_Treatment_Name" || $heading=="Contact_Lenses_Brand"){
							  ?>
						 	<td>
								<input type="text" name="item_wholesale_price1" id="item_wholesale_price1" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />
							</td>
							<td>
								<input type="text" name="item_purchase_price1" id="item_purchase_price1" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />
							</td>
							<td>
								<input type="text" name="item_retail_price1" id="item_retail_price1" value="" class="input_val" style="width:100px;" onChange="convert_float(this);" />
							</td>
						<?php
						 }
						 if($_REQUEST['module_name']=="return_reason" || $heading=="Contact_Lenses_Disinfecting" ){ ?>
							<td>
								<input type="text" name="item_price1" id="item_price1" value="" class="input_val" style="width:200px;" onChange="convert_float(this);" />
							</td>
						 <?php
						 }
						 if( ($heading=='Lenses_Usage' && $_REQUEST['module_name']=='lens_usage') || ($heading=='Lenses_Type_Common' && $_REQUEST['module_name']=='lens_type_common') ){
							echo '<td><select name="sub_type1" id="sub_type1" style="width:132px;"><option value="0">Both</option><option value="1">Contact Lens</option><option value="2">Lens</option></td>';
						 }
						 } ?>
                    </tr>                       
                </table>
                </div>
                <div style="float:right; padding-top:14px;">
                    <img onClick="addrow();" style="cursor:pointer;" src="../../images/addrow.png" /> <img style="cursor:pointer;" id="removebtn" onClick="removerow();" src="../../images/removerow.png" />
				</div>  
                <div class="btn_cls">
                    <input type="submit" name="save" value="Save" />                        
                </div>
           </form>
        </div>
    </div>

<script>
$(document).ready(function(){
	var heading = '<?php echo $heading; ?>';
	var dd_pro = new Array();
	dd_pro["listHeight"] = 100;
	dd_pro["noneSelected"] = "Select All";
<?php if($heading!="Contact_Lenses_Disinfecting" && $heading!="Contact_Lenses_Brand"): ?>
	$("#sel_manufact1").multiSelect(dd_pro);
<?php endif; ?>

<?php  if($heading=="Lens_Material_Name"): ?>
	$("#item_design1").multiSelect(dd_pro);
<?php elseif($heading=="Lens_Treatment_Name"): ?>
	$("#item_material1").multiSelect(dd_pro);
<?php endif; ?>

	createRows(1,6);
	document.getElementById('input_val1').focus();
	if(heading=="Contact_Lenses_Disinfecting"){
		$("#item_prac_code1").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'praCode',
			showAjaxVals: 'defaultCodeC',
			hidIDelem: document.getElementById("item_prac_code_id1"),
			maxVals: 5
		});
	}
	else if(heading=="Lens_Material_Name"){
		$(".prac_lens_material").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'praCode',
			showAjaxVals: 'defaultCodeO',
			maxVals: 5
		});
	}
	else{
		var obj8 = new actb(document.getElementById('item_prac_code1'),customarrayProcedure);	
	}

});
</script>

<?php 
	if($_POST['action']=="already")
	{
		$aQ = imw_query("select ".$_REQUEST['col_name']." from ".$_REQUEST['module_name']." where ".$_REQUEST['col_name']." = '".$_REQUEST['itemname']."'");	
		if(imw_num_rows($aQ)>0)
		{
			$istatus=$_REQUEST['itemname'];
			echo $istatus;
		}
		else
		{
			$istatus="";
			echo $istatus;
		}
		
	}	
?>
</body>
</html>