<?php 
/*
File: location_popup.php
Coded in PHP7
Purpose: Add and Subtract Quantity in Location
Access Type: Direct acess
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once("../../../library/classes/functions.php");

/*Dymo Label Printing*/
$printingData = "";	#Container to Hold Data for Dymo Printing
$printUpc = "";
$item_id = (isset($_REQUEST['item_id']))?$_REQUEST['item_id']:false;
$item_add=$_REQUEST['item_add'];
if($item_id && $item_add=="yes"){
	
	$module_id = false;
	$item_data = false;
	/*Fetch Item Data*/
	$module_resp = imw_query("SELECT * FROM `in_item` WHERE `id`='".$item_id."'");
	if($module_resp){
		$item_data = imw_fetch_object($module_resp);
		$module_id = $item_data->module_type_id;
	}
	$printFields = array();
	if($module_id){
		$allowed_vals = array();	
		$status_check=imw_query("select `option_chk` from in_print_option_stock where module_id=".$module_id." and status=1");
		if($status_check && imw_num_rows($status_check)>0){
			while($opt_row = imw_fetch_object($status_check)){
				$allowed_vals[] = $opt_row->option_chk;
			}
		}
		
		/*Fetch Item data only if an printing option is selected for the module type.*/
		if(count($allowed_vals)>0 && $item_data){
			
			$query1=imw_query("select * from in_vendor_details where id=".$item_data->vendor_id."");
			if($query1 && imw_num_rows($query1)>0){
				$vendor=imw_fetch_array($query1);
				$printFields[1]['ven_chk'] = htmlentities(html_entity_decode($vendor['vendor_name']));
			}
			$query2=imw_query("select  * from in_module_type where id=".$item_data->module_type_id."");
			if($query2 && imw_num_rows($query2)>0){
				$type=imw_fetch_array($query2);
				$printFields[1]['type_chk'] = htmlentities(html_entity_decode($type['module_type_name']));
			}
			$query3=imw_query("select * from in_manufacturer_details where id=".$item_data->manufacturer_id."");
			if($query3 && imw_num_rows($query3)>0){
				$manufac=imw_fetch_array($query3);
				$printFields[1]['mf_chk'] = htmlentities(html_entity_decode($manufac['manufacturer_name']));
				$printFields[1]['mf_chk'] = str_replace('&reg;', '�',$printFields[1]['mf_chk']);
			}
			$lense_color = $query_brand = $type_mat = "";
			switch($item_data->module_type_id)
			{
				case 1:
					$lense_color = "select * from in_frame_color where id in(".$item_data->color.")";
					$query_brand = "select * from in_frame_sources where id=".$item_data->brand_id."";
				break;
				case 2:
					$lense_color = "select * from in_lens_color where id in(".$item_data->color.")";
					$type_mat="select * from in_lens_material where id=".$item_data->material_id;
					$mat_type="select * from in_lens_type where id=".$item_data->type_id;
				break;
				case 3:
					$lense_color = "select * from in_color where id in(".$item_data->color.")";
					$query_brand = "select * from in_contact_brand where id=".$item_data->brand_id."";
				break;
			}
			if($lense_color!=""){
				$d="";
				$lense_color1=imw_query($lense_color);
				while($color=imw_fetch_array($lense_color1))
				{
					$d.=(htmlentities(html_entity_decode($color['color_name']))).",";
				}
				$printFields[1]['colr_chk'] = rtrim($d,",");
			}
			if($query_brand!=""){
				$query_brand=imw_query($query_brand);
				$brand=imw_fetch_array($query_brand);
				$printFields[1]['brnd_chk'] = (isset($brand['frame_source']))?$brand['frame_source']:$brand['brand_name'];
				if($printFields[1]['brnd_chk']!='')
					$printFields[1]['brnd_chk'] = htmlentities(html_entity_decode($printFields[1]['brnd_chk']));
			}
			$query5=imw_query("select * from in_frame_shapes where id=".$item_data->frame_shape."");
			if($query5 && imw_num_rows($query5)>0)
			{
				$frames_shape=imw_fetch_array($query5);
				$printFields[1]['shp_chk'] = htmlentities(html_entity_decode($frames_shape['shape_name']));
			}
			$query6=imw_query("select * from in_frame_styles where id=".$item_data->frame_style."");
			if($query6 && imw_num_rows($query6)>0)
			{
				$frames_style=imw_fetch_array($query6);
				$printFields[1]['styl_chk'] = htmlentities(html_entity_decode($frames_style['style_name']));
			}
			if($mat_type!="")
			{
				$query7=imw_query($mat_type);
				$lens_focl=imw_fetch_array($query7);
				$printFields[1]['lens_focl_chk'] = htmlentities(html_entity_decode($lens_focl['type_name']));
			}
			if($type_mat!="")
			{
				$q=imw_query($type_mat);
				$lens_mate=imw_fetch_array($q);
				$printFields[1]['lens_mate_chk'] = htmlentities(html_entity_decode($lens_mate['material_name']));
			}
			
			$query9=imw_query("select GROUP_CONCAT(ar_name) AS 'ar_name' from in_lens_ar where id IN(".$item_data->a_r_id.") ORDER BY ar_name ASC");
			if($query9 && imw_num_rows($query9)>0)
			{
				$lens_ar=imw_fetch_array($query9);
				$printFields[1]['lens_a_r_chk'] = htmlentities(html_entity_decode($lens_ar['ar_name']));
			}
			$query10=imw_query("select * from in_lens_transition where id=".$item_data->transition_id."");
			if($query10 && imw_num_rows($query10)>0)
			{
				$lens_tran=imw_fetch_array($query10);
				$printFields[1]['lens_tran_chk'] = $lens_tran['transition_name'];
			}
			$query11=imw_query("select * from in_lens_polarized where id=".$item_data->polarized_id."");
			if($query11 && imw_num_rows($query11)>0)
			{
				$lens_pol=imw_fetch_array($query11);
				$printFields[1]['lens_pol_chk'] = $lens_pol['polarized_name'];
			}
			$query12=imw_query("select * from in_lens_edge where id=".$item_data->edge_id."");
			if($query12 && imw_num_rows($query12)>0)
			{
				$lens_edge=imw_fetch_array($query12);
				$printFields[1]['lens_edge_chk'] = $lens_edge['edge_name'];
			}
			$query13=imw_query("select * from in_lens_tint where id=".$item_data->tint_id."");
			if($query13 && imw_num_rows($query13)>0)
			{
				$lens_tint=imw_fetch_array($query13);
				$printFields[1]['lens_tint_chk'] = $lens_tint['tint_type'];
			}
			$query14=imw_query("select * from in_contact_cat where id in (".$item_data->cl_wear_schedule.")");
			if($query14 && imw_num_rows($query14)>0)
			{
				$e="";
				while($con_lens_wear=imw_fetch_array($query14))
				{
					$e.=($con_lens_wear['cat_name']).",";
				}
				$printFields[1]['cnt_len_wer_chk'] = rtrim($e,",");
			}
			$query17=imw_query("select * from in_type where id in (".$item_data->type_id.")");
			if($query17 && imw_num_rows($query17)>0)
			{
				$c="";
				while($con_lens_mat=imw_fetch_array($query17))
				{
					$c.=($con_lens_mat['type_name']).",";
				}
				$printFields[1]['cnt_len_mat_chk'] = rtrim($c,",");
			}
			$query15=imw_query("select * from in_supply where id=".$item_data->supply_id."");
			if($query15 && imw_num_rows($query15)>0)
			{
				$con_lens_sup=imw_fetch_array($query15);
				$printFields[1]['cnt_len_sup_chk'] = $con_lens_sup['supply_name'];
			}
			$query16=imw_query("select * from in_supplies_measurment where id=".$item_data->measurment."");
			if($query16 && imw_num_rows($query16)>0)
			{
				$measurement=imw_fetch_array($query16);
				$printFields[1]['suply_mnt_chk'] = $measurement['measurment_name'];
			}
	 		$printFields[1]['upc_chk'] = $item_data->upc_code;
			$printFields[1]['gender_chk'] = $item_data->gender;
			$printFields[1]['wholesale_chk'] = $item_data->wholesale_cost;
			$printFields[1]['purchase_price'] = $item_data->purchase_price;
			$printFields[1]['retail_chk'] = $item_data->retail_price;
			$printFields[1]['med_exp_chk'] = $item_data->expiry_date;
			$printFields[1]['module_type'] = $item_data->module_type_id;
			$printFields[1]['manufacturer_id'] = $item_data->manufacturer_id;
			$printFields[1]['brand_id'] = $item_data->brand_id;
			$printFields[1]['frame_style'] = $item_data->frame_style;
			$printFields[1]['retail_price_flag'] = $item_data->retail_price_flag;
			$printFields[1]['formula'] = $item_data->formula;
			$printFields[1]['bridge'] = $item_data->bridge;
			$printFields[1]['temple'] = $item_data->temple;
		}
	}
	
	if(count($printFields)>0){
		foreach($printFields as $field){
			
		/*Dymo Label Printing Section*/
			# Common Fields
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
			
			// code for formula if 
			$formula = $field['formula'];
			$retail_price_markup_modules = array(1, 3, 5, 6);	/*List of module type id's for which retail price markup functionality is given*/
			/*Get Default Formula for the item Type*/
			if( in_array($field['module_type'], $retail_price_markup_modules) ){
				if( $field['module_type']=='1' ){
					$default_formula = get_retail_formula($field['module_type'], array('manufacturer_id'=>$field['manufacturer_id'], 'brand_id'=>$field['brand_id'], 'frame_style'=>$field['frame_style']));
				}
				else{
					$default_formula = get_retail_formula($field['module_type'], array('manufacturer_id'=>$field['manufacturer_id'], 'brand_id'=>$field['brand_id']));
				}
			}
			/*End Get Default Formula for the item Type*/
			/*Retail Prices Markup - Caclulation*/
			if( in_array($field['module_type'], $retail_price_markup_modules) && $field['retail_price_flag']=='0' ){

				if( trim($formula)=='' ){
					$formula = $default_formula;
				}
				/*Final Retail Price for the Item - based on formula calculation*/
				if( $formula!='' ){
					$field['retail_chk'] = calculate_markup_price($formula, $field['wholesale_chk'],$field['purchase_price']);
					$arrPrint[5][2]= currency_symbol(true)." ".$field['retail_chk'];
				}
				/*End Final Retail Price for the Item*/
			}
			/*End Retail Prices Markup - Caclulation*/

			foreach($arrPrint as $val){
				if(sizeof($val)>4){
					foreach($val as $subval){	
						$cntr++;
						$printingData.=$subval."-";
						if($cntr==4){
							if(substr($printingData,strlen($printingData)-1,1)=='-')
							$printingData=substr($printingData,0,strlen($printingData)-1);
							$printingData=wordwrap($printingData,60,"<br />");
							$cntr=0;
							$printingData.="<br />";	
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
			
			if($field['module_type']==1){
				$printingData .= '<br />  '.$field['a'].'  '.$field['bridge'].'  '.$field['temple'];
			}
		}
	}
}
/*End Dymo Label Printing*/

//print_r($printingData);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<style type="text/css">
#pritButton{
	float:right;
	width:40px;
	cursor: pointer;
}
.labelPrinting{width:100%;margin-top:10px;}
.label_div, .label_print_div{vertical-align:middle;display:inline-block;}
.label_div{width:40%;}
.label_div select{width:100%;}
.label_print_div{width:15%;}
</style>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/dymo/DYMO.Label.Framework.latest.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
	window.opener = window.opener.main_iframe.admin_iframe;
</script>
<script type="text/javascript">
var WRP = "<?php echo $GLOBALS['WEB_PATH']; ?>";
function loc_total_fun(){
	var sum_total = 0;
	$('.lot_qty_class').each(function()
	{
		if(!isNaN(parseInt($(this).val(),10))){
			sum_total += parseInt($(this).val(),10);
		}
	});
	$('#lot_total').val(sum_total);
}
function sub_form_fun(){
	if(document.getElementById('item_add').value!='yes' && document.getElementById('reason').value==""){
			alert("Please select reason");
			return false;
	}else{
		document.getElementById('save_stock').value=1;
		document.loc_form.submit();	
	}
}
function plus_qty_fun(id){
	if(document.getElementById('lot_qty_'+id)){
		var add_qty=0;
		if(document.getElementById('lot_qty_'+id).value!=""){
			add_qty=document.getElementById('lot_qty_'+id).value;
		}
		document.getElementById('lot_qty_'+id).value=parseInt(add_qty)+1;
	}
	loc_total_fun();
}
function minus_qty_fun(id){
	if(document.getElementById('lot_qty_'+id)){
		var add_qty=0;
		var old_price = 0;
		if(document.getElementById('lot_qty_'+id).value!=""){
			add_qty=document.getElementById('lot_qty_'+id).value;
		}
		if(document.getElementById('old_lot_qty_'+id).value!=""){
			old_price=document.getElementById('old_lot_qty_'+id).value;
		}
		if(parseInt(old_price)>parseInt(add_qty)){
			document.getElementById('lot_qty_'+id).value=parseInt(add_qty)+1;
		}
	}
	loc_total_fun();
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
function addNewRow(val,loc_id){
	var pre_cnt=document.getElementById('last_cnt').value;
	pre_cnt++;
	var loc_name = document.getElementById('loc_name_'+loc_id).value;
	if(val!=""){
		loc_name="";
	}
	var td_val = '<td style="text-align:left; padding-left:5px; font-weight:bold;" class="module_label">'+loc_name+'<input type="hidden" name="loc_id_'+pre_cnt+'" id="loc_id_'+pre_cnt+'" value="'+loc_id+'"><input type="hidden" name="loc_name_'+loc_id+'" id="loc_name_'+loc_id+'" value="'+loc_name+'"></td>';
		td_val  += '<td class="module_label" style="text-align:left; padding-left:5px;"><input type="hidden" name="lot_id_'+pre_cnt+'" id="lot_id_'+pre_cnt+'" value=""><input type="text" style="width:100px" name="lot_no_'+pre_cnt+'" id="lot_no_'+pre_cnt+'" value=""> <img style="cursor:pointer; vertical-align: middle;" id="addbtn_1" onClick="addNewRow('+pre_cnt+','+loc_id+');" src="../../../images/addrow.png" title="Add New Row" alt="Add New Row" /></td>';
		td_val  += '<td class="module_label" style="text-align:right; padding-left:5px;">0<input type="hidden" name="old_lot_qty_'+pre_cnt+'" id="old_lot_qty_'+pre_cnt+'" value=""></td>';
		td_val  += '<td style="text-align:right;"><input type="text" style="width:60px;" name="lot_qty_'+pre_cnt+'" id="lot_qty_'+pre_cnt+'" class="lot_qty_class"> <img src="../../../images/add_btn.png"  onClick="plus_qty_fun('+pre_cnt+');" style="cursor:pointer; margin:0px; vertical-align:middle;"></td>';
		td_val  += '<td style="text-align:right;">';
			td_val  += '<input type="text" style="width:60px" class="currency" name="wholesale_price_'+pre_cnt+'" id="wholesale_price_'+pre_cnt+'" onChange="convertFloat(this);" />';
		td_val  += '</td>';
		td_val  += '<td style="text-align:right;">';
			td_val  += '<input type="text" style="width:60px" class="currency" name="purchase_price_'+pre_cnt+'" id="purchase_price_'+pre_cnt+'" onChange="convertFloat(this);" />';
		td_val  += '</td>';
		/*td_val  += '<td style="text-align:right;">';
			td_val  += '<input type="text" style="width:60px" class="currency" name="retail_price_'+pre_cnt+'" id="retail_price_'+pre_cnt+'" onChange="convertFloat(this);" />';
                td_val  += '</td>';*/
	<?php if($_REQUEST['item_type']!="" && $_REQUEST['item_type']=="medicine"): ?>
		td_val  += '<td style="text-align:right;"><input type="text" style="width:80px;" name="expiry_date_'+pre_cnt+'" id="expiry_date_'+pre_cnt+'" class="expiryDate date-pick"> </td>';
	<?php endif; ?>
	var tr = '<tr id="'+pre_cnt+'">' + td_val + '</tr>';
	if(val==""){
		$(tr).insertAfter("#loc_div_"+loc_id);
	}else{
		$(tr).insertAfter('#'+val);
	}
	document.getElementById('last_cnt').value=pre_cnt;
	
	/*Bind Daepicker*/
	$("#expiry_date_"+pre_cnt).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
	/*End Datepicker*/
	currencySymbols();
}
</script>
</head>
<?php
	//Add and Subtract Quantity//
	$item_id=$_REQUEST['item_id'];
	$item_add=$_REQUEST['item_add'];
	$last_cnt=$_REQUEST['last_cnt'];
	$operator_id=$_SESSION['authId'];
	if($item_add=="yes"){
		$show_heading="Add Quantity in Location";
		$show_heading2="Add Qty";
	}else{
		$show_heading="Subtract Quantity in Location";
		$show_heading2="Subtract Qty";
	}
	if($_REQUEST['save_stock']!=""){
		$opr_id = $_SESSION['authId'];
		$entered_date=date('Y-m-d');
		$entered_time=date('H:i:s');
		if($_REQUEST['item_id']<=0 || $_REQUEST['item_id']==""){
			$ins_item=imw_query("insert into in_item set entered_date='$entered_date',entered_time='$entered_time',entered_by='$opr_id'");
			$ins_item_id=imw_insert_id();
			$item_id=$ins_item_id;
		}
		for($i=0;$i<=$last_cnt;$i++){
			$lot_id=$_REQUEST['lot_id_'.$i];
			$lot_no=$_REQUEST['lot_no_'.$i];
			$lot_qty=$_REQUEST['lot_qty_'.$i];
			$loc_id=$_REQUEST['loc_id_'.$i];
			$wholesale_price = $_REQUEST['wholesale_price_'.$i];
			$purchase_price = $_REQUEST['purchase_price_'.$i];
			//$retail_price = $_REQUEST['retail_price_'.$i];
			$expiry_date=saveDateFormat($_REQUEST['expiry_date_'.$i]);
			$reason=$_REQUEST['reason'];
			if($lot_qty>0 || $lot_id>0){
				if($lot_qty=="" || $lot_qty<0){
					$lot_qty=0;
				}
				$stock_qry=imw_query("Select * from in_item_lot_total where loc_id='$loc_id' and item_id='$item_id' and id='$lot_id'");
				if(imw_num_rows($stock_qry)>0){
					$update=$where='';
					/*if($lot_no)
					{
						$update=",lot_no='$lot_no'";
						$where=" and lot_no='$lot_no'";
					}*/
					if($item_add=="yes"){
						imw_query("update in_item_lot_total set expiry_date='$expiry_date' $update ,stock=stock+$lot_qty, wholesale_price='".$wholesale_price."',purchase_price='".$purchase_price."' where item_id='$item_id' and loc_id='$loc_id' and id='$lot_id'");
						imw_query("update in_item_loc_total set stock=stock+$lot_qty where item_id='$item_id' and loc_id='$loc_id' $where");
					}else{
						if($lot_qty>$_REQUEST['old_lot_qty_'.$i]){
							$lot_qty=$_REQUEST['old_lot_qty_'.$i];
						}
						imw_query("update in_item_lot_total set expiry_date='$expiry_date' $update, stock=stock-$lot_qty, wholesale_price='".$wholesale_price."',purchase_price='".$purchase_price."' where item_id='$item_id' and loc_id='$loc_id' and id='$lot_id'");
						imw_query("update in_item_loc_total set stock=stock-$lot_qty where item_id='$item_id' and loc_id='$loc_id' $where");
					}
				}else{
					if($item_add=="yes"){
						if(!$lot_no)$lot_no=date('Ymd').$_SESSION["authId"].$i;
						imw_query("insert into in_item_lot_total set expiry_date='$expiry_date',stock='$lot_qty',item_id='$item_id',loc_id='$loc_id',lot_no='$lot_no', wholesale_price='".$wholesale_price."',purchase_price='".$purchase_price."'");
						$stock_qry=imw_query("Select * from in_item_loc_total where loc_id='$loc_id' and item_id='$item_id'");
						if(imw_num_rows($stock_qry)>0){
							imw_query("update in_item_loc_total set stock=stock+$lot_qty where item_id='$item_id' and loc_id='$loc_id' ");
						}else{
							imw_query("insert into in_item_loc_total set stock='$lot_qty',item_id='$item_id',loc_id='$loc_id'");
						}
					}
				}
				if($item_add=="yes"){
					$trans_type="add";
				}else{
					$trans_type="minus";
				}
				$reason=imw_real_escape_string($reason);
				$stock_ins_qry="insert into in_stock_detail set item_id='$item_id',loc_id='$loc_id', lot_id='$lot_id',lot_no='$lot_no',stock='$lot_qty',
								trans_type='$trans_type',reason='$reason',operator_id='$operator_id',
								entered_date='$entered_date', entered_time='$entered_time', source='Inventory'";
				imw_query($stock_ins_qry);
			}
			//update wholesale and purchase price in item table
			$qLot=imw_query("select wholesale_price,purchase_price from in_item_lot_total where item_id='$item_id' order by id desc limit 0,1");
			if(imw_num_rows($qLot)>0)
			{
				$dLot=imw_fetch_assoc($qLot);
				$wholesale_price=$dLot[wholesale_price];
				$purchase_price=$dLot[purchase_price];
				imw_query("update in_item set wholesale_cost='$wholesale_price', purchase_price='$purchase_price' where id = '$item_id'");
			}
		}
		$sel_stock=imw_query("select sum(stock) as loc_stock from in_item_loc_total where item_id='$item_id'");
		$fet_stock=imw_fetch_array($sel_stock);
		$loc_stock=$fet_stock['loc_stock'];
		imw_query("update in_item set qty_on_hand='$loc_stock', amount=retail_price*$loc_stock, modified_date='$entered_date', modified_time='$entered_time', modified_by='$opr_id' where id='$item_id'");
		?>
        <script type='text/javascript'>
		var rowID='<?php echo $_REQUEST['id']; ?>';
			if(window.opener.document.getElementById('qty_on_hand'+rowID)){
				var loc_stock = 0;
				loc_stock = '<?php echo $loc_stock; ?>';
				var ins_item_id = '<?php echo $ins_item_id; ?>';
				window.opener.document.getElementById('qty_on_hand'+rowID).value=loc_stock;
				window.opener.document.getElementById('qty_on_hand_td').innerHTML = loc_stock;
				<?php if($wholesale_price){?>
				window.opener.document.getElementById('wholesale_cost').value = '<?php echo $wholesale_price;?>';
				<?php }if($purchase_price){?>
				window.opener.document.getElementById('purchase_price').value = '<?php echo $purchase_price;?>';
				<?php }
				if($wholesale_price || $purchase_price){?>
				window.opener.formula_change(true);
				<?php } ?>
				var retail_price =0;
				if(window.opener.document.getElementById('retail_price'+rowID).value>0){
					retail_price = window.opener.document.getElementById('retail_price'+rowID).value;
				}
				var total_price = parseFloat(retail_price)*parseInt(loc_stock);
				window.opener.document.getElementById('amount'+rowID).value=total_price.toFixed(2);
				if(ins_item_id>0){
					window.opener.document.getElementById('edit_item_id'+rowID).value=ins_item_id;
				}
			}
			window.close();
        </script>
        <?php
	}
?>
<body onclick="loc_total_fun();" >
<center>
	<div style="padding:5px; width:98%;">
        <div class="listheading"><?php echo $show_heading;?></div>
        <div>
            <form action="" name="loc_form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="item_add" id="item_add" value="<?php echo $_REQUEST['item_add'];?>">
            <input type="hidden" name="item_id" id="item_id" value="<?php echo $_REQUEST['item_id'];?>">
            <input type="hidden" name="save_stock" id="save_stock" value="">
			<input type="hidden" name="item_type" id="item_type" value="<?php echo $_REQUEST['item_type'];?>">
               <div style="overflow-y:scroll; height:275px;">
                   <table class="table_collapse cellBorder table_cell_padd5" style="border:1px solid #F00;">
                   		 <tr id="tr_heading">
                         	<td style="text-align:left; padding-left:5px;" class="text_b_w">
                               Location
                            </td>
                            <td style="text-align:left; padding-left:5px;" class="text_b_w">
                               Lot#
                            </td>
                            <td  style="text-align:left; padding-left:5px;" class="text_b_w">
                             Qty in hand
							</td>
                            <td style="text-align:center; padding-left:5px;" class="text_b_w">
                            	<?php echo $show_heading2; ?>
                            </td>
							<td  style="text-align:left; padding-left:5px;" class="text_b_w">
                             Wholesale Price
							</td>
							<td  style="text-align:left; padding-left:5px;" class="text_b_w">
							 Purchase Price
							</td>
<?php /*
							<td  style="text-align:left; padding-left:5px;" class="text_b_w">
                             Retail Price
							</td>
*/ ?>
							<?php if($_REQUEST['item_type']!="" && $_REQUEST['item_type']=="medicine"){?>
							<td style="text-align:center; padding-left:5px;" class="text_b_w">
                            	Exp. Date
                            </td>
							<?php } ?>
                        </tr>
                        <?php
							$pro_cont=0;
                            $old_total_stock_arr=array();
							$loc_qry=imw_query("select in_location.loc_name,in_location.id,in_item_loc_total.stock
                            from in_location left join in_item_loc_total on in_location.id=in_item_loc_total.loc_id 
                            and in_item_loc_total.item_id='$item_id' where in_location.del_status='0' GROUP BY in_location.id ORDER BY in_location.loc_name");
                            while($loc_row=imw_fetch_array($loc_qry))
							{
                                $old_total_stock_arr[]=$loc_row['stock'];
								$loc_name=$loc_row['loc_name'];
								if($item_add=="yes")
								{
                        ?>
									<tr id="loc_div_<?php echo $loc_row['id']; ?>">
										<td colspan="6">
											<input type="hidden" name="loc_name_<?php echo $loc_row['id']; ?>" id="loc_name_<?php echo $loc_row['id']; ?>" value="<?php echo $loc_row['loc_name']; ?>">
										</td>
									</tr>    
							<?php
								}
								$lot_qry=imw_query("select * from in_item_lot_total where item_id='$item_id' and loc_id='".$loc_row['id']."'");
								if(imw_num_rows($lot_qry)>0)
								{
									while($lot_row=imw_fetch_array($lot_qry))
									{
										$pro_cont++;
							?>
									<tr id="<?php echo $pro_cont; ?>">
										<td style="text-align:left; padding-left:5px; font-weight:bold;" class="module_label">
											<?php echo $loc_name; ?>
											<input type="hidden" name="loc_id_<?php echo $pro_cont; ?>" id="loc_id_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['loc_id']; ?>">
										</td>
										<td align="left" class="module_label" style="text-align:left; padding-left:5px;">
											<input type="hidden" name="lot_id_<?php echo $pro_cont; ?>" id="lot_id_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['id']; ?>">
											<input type="text" style="width:100px" name="lot_no_<?php echo $pro_cont; ?>" id="lot_no_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['lot_no']; ?>">
											<?php if($item_add=="yes"){ ?>
											<img style="cursor:pointer; vertical-align: middle;" id="addbtn_1" onClick="addNewRow('<?php echo $pro_cont; ?>','<?php echo $lot_row['loc_id'];?>');" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" />
											<?php } ?>
										</td>
										<td style="text-align:right; padding-right:5px;" class="module_label"><?php echo number_format($lot_row['stock']); ?>
											<input type="hidden" style="width:80px" name="old_lot_qty_<?php echo $pro_cont; ?>" id="old_lot_qty_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['stock']; ?>">
										</td>
										<td style="text-align:right;">
											<?php
											$lot_qty_read="";
											if($lot_row['stock']<=0 && $item_add!="yes"){
												$lot_qty_read="disabled";
											}
											?>
											<input type="text" <?php echo $lot_qty_read;?> style="width:60px" name="lot_qty_<?php echo $pro_cont; ?>" id="lot_qty_<?php echo $pro_cont; ?>" class="lot_qty_class numberOnly">
											<?php 
											if($item_add=="yes")
											{
											?>
												<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/add_btn.png"  onClick="plus_qty_fun('<?php echo $pro_cont; ?>');" style="cursor:pointer; margin:0px; vertical-align:middle;">
											<?php 
											}
											else
											{
											?>
												<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/minus_btn.png"  <?php if($lot_row['stock']>0){?> onClick="minus_qty_fun('<?php echo $pro_cont; ?>');" <?php } ?> style="cursor:pointer; margin:0px; vertical-align:middle;">
											<?php 
											}
											?>
										</td>
										<td style="text-align:right;">
											<input type="text" style="width:60px" class="currency" name="wholesale_price_<?php echo $pro_cont; ?>" id="wholesale_price_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['wholesale_price']; ?>" onChange="convertFloat(this);" />
										</td>
										<td style="text-align:right;">
											<input type="text" style="width:60px" class="currency" name="purchase_price_<?php echo $pro_cont; ?>" id="purchase_price_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['purchase_price']; ?>" onChange="convertFloat(this);" />
                                                                                </td>
<?php /*
										<td style="text-align:right;">
											<input type="text" style="width:60px" class="currency" name="retail_price_<?php echo $pro_cont; ?>" id="retail_price_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['retail_price']; ?>" onChange="convertFloat(this);" />
                                                                               </td>
 */ ?>
									<?php
										if($_REQUEST['item_type']!="" && $_REQUEST['item_type']=="medicine")
										{
									?>
										<td style="text-align:right;">
											<input name="expiry_date_<?php echo $pro_cont; ?>" id="expiry_date_<?php echo $pro_cont; ?>" type="text" style="width:80px; height: 21px; background-size: 17px 24px;" autocomplete="off" class="expiryDate date-pick" value="<?php echo getDateFormat($lot_row['expiry_date']); ?>" />
										</td>
									<?php
										}
									?>
									</tr>
							<?php
									$loc_name="";
									}
								}
								else
								{
									if($item_add=="yes")
									{
										$pro_cont++;
							?>
									<tr id="<?php echo $pro_cont; ?>">
										<td style="text-align:left; padding-left:5px; font-weight:bold;" class="module_label">
											<?php echo $loc_name; ?>
											<input type="hidden" name="loc_id_<?php echo $pro_cont; ?>" id="loc_id_<?php echo $pro_cont; ?>" value="<?php echo $loc_row['id']; ?>">
										</td>
										<td align="left" class="module_label" style="text-align:left; padding-left:5px;">
											<input type="hidden" name="lot_id_<?php echo $pro_cont; ?>" id="lot_id_<?php echo $pro_cont; ?>" value="">
											<input type="text" style="width:100px" name="lot_no_<?php echo $pro_cont; ?>" id="lot_no_<?php echo $pro_cont; ?>" value="">
											<img style="cursor:pointer; vertical-align: middle;" id="addbtn_1" onClick="addNewRow('<?php echo $pro_cont; ?>','<?php echo $loc_row['id'];?>');" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" />
										</td>
										<td style="text-align:right; padding-right:5px;" class="module_label">
											<input type="hidden" style="width:80px" name="old_lot_qty_<?php echo $pro_cont; ?>" id="old_lot_qty_<?php echo $pro_cont; ?>" value="">
										</td>
										<td style="text-align:right;">
											<input type="text" style="width:60px" name="lot_qty_<?php echo $pro_cont; ?>" id="lot_qty_<?php echo $pro_cont; ?>" class="lot_qty_class numberOnly">
										   <img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/add_btn.png"  onClick="plus_qty_fun('<?php echo $pro_cont; ?>');" style="cursor:pointer; margin:0px; vertical-align:middle;">
										</td>
										<td style="text-align:right;">
											<input type="text" style="width:60px" class="currency" name="wholesale_price_<?php echo $pro_cont; ?>" id="wholesale_price_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['wholesale_price']; ?>" onChange="convertFloat(this);" />
										</td>
										<td style="text-align:right;">
											<input type="text" style="width:60px" class="currency" name="purchase_price_<?php echo $pro_cont; ?>" id="purchase_price_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['purchase_price']; ?>" onChange="convertFloat(this);" />
                                                                                </td>
<?php /*

										<td style="text-align:right;">
											<input type="text" style="width:60px" class="currency" name="retail_price_<?php echo $pro_cont; ?>" id="retail_price_<?php echo $pro_cont; ?>" value="<?php echo $lot_row['retail_price']; ?>" onChange="convertFloat(this);" />
                                                                                        </td>
 */ ?>
									<?php 
										if($_REQUEST['item_type']!="" && $_REQUEST['item_type']=="medicine")
										{
									?>
										<td style="text-align:right;">
											<input name="expiry_date_<?php echo $pro_cont; ?>" id="expiry_date_<?php echo $pro_cont; ?>" type="text" style="width:80px; height: 21px; background-size: 17px 24px;" autocomplete="off" class="expiryDate date-pick" value="" />
										</td>
									<?php
										}
									?>
									</tr>
						<?php 
									}
								}
							}
						?>
                        <input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $pro_cont; ?>">
                     </table>
                 </div>
                 <table class="table_collapse table_cell_padd5">
                     <tr style="background-color:#CFC;">
                        <td width="225px" align="left" class="module_heading" style="text-align:right; padding-right:9px;">
                            Grand Total
                        </td>
                        <td width="140px" style="text-align:right;padding-right:10px;"><?php echo array_sum($old_total_stock_arr); ?></td>
                        <td style="text-align:right; width:auto;">
                            <input type="text" style="width:60px" name="lot_total" id="lot_total" readonly>
                        </td>
                        <td width="35px;"></td>
                    </tr>
                    <?php if($item_add!="yes"){?>
                    <tr>
                    	<td class="module_label" colspan="3">
                        	<strong>Reason :</strong> &nbsp;
                            <select name="reason" id="reason" style="width:290px;">
                            	<option value="">Please Select</option>
                                <?php
									$sel_res=imw_query("select * from in_reason where del_status='0' order by reason_name");
									while($sel_row=imw_fetch_array($sel_res)){
								?>
                                	<option value="<?php echo $sel_row['id'];?>"><?php echo $sel_row['reason_name'];?></option>
                                <?php		
									}
								?>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
               </table>
				<?php if($item_id && $item_add=="yes"): ?>
               <div class="labelPrinting">
					<div class="label_div">
			   			<label for="dymoPrinter">Select Printer</label>
						<select id="dymoPrinter"></select>
					</div>
					<div class="label_div">
						<label for="dymoPaper" style="margin-left:20px;">Paper Size</label>
						<select id="dymoPaper">
							<!--<option value="PriceTag.label">Price Tag 22mm x 24mm</option>-->
							<option value="PriceTag1.label">Price Tag 25.4mm x 76.2mm</option>
							<!--<option value="Address.label">Address 28mm x 89mm</option>
							<option value="ExtraSmall_2UP.label">Extra Small (2-Up) 13mm x 25mm</option-->>
						</select>
					</div>
					<div class="label_print_div">
			   			<img id="pritButton" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/print.png" onclick="printLabels();" />
					</div>
				</div>
				<?php endif; ?>
               <div class="btn_cls" style="width:50%">
               		<input type="button" name="save" value="Save" onClick="sub_form_fun();" />                        
                    <input type="button" name="new" value="Cancel" onClick="javascript:window.close();" />
               </div> 
            </form>
        </div>
	</div>
</center>
<?php if($item_id && $item_add=="yes"): ?>
<script type="text/javascript">
/*Dymo Lanel Printing*/
var item_upc_code = "<?php echo ($printUpc!="")?$printUpc:""; ?>";
var item_printing_data = "<?php echo ($printingData!="")?$printingData:""; ?>";
item_printing_data = $('<div>').html(item_printing_data);
item_printing_data = $(item_printing_data).html();
// stores loaded label info
	var label;
	
	// Printer's List
	var printersSelect = document.getElementById('dymoPrinter');
	// Label's List
	var labelSelected = document.getElementById('dymoPaper');
	// called when the document completly loaded
	// To load Dymo Printer
	function onload(){
		// loads all supported printers into a Select List 
		function loadPrinters(){
			var printers = dymo.label.framework.getLabelWriterPrinters();
			if (printers.length == 0){
				top.falert("No DYMO LabelWriter printers are installed.<br />Install DYMO LabelWriter printers.");
				//return;
			}
	
			for (var i = 0; i < printers.length; ++i){
				var printer = printers[i];
				var printerName = printer.name;
	
				var option = document.createElement('option');
				option.value = printerName;
				option.appendChild(document.createTextNode(printerName));
				printersSelect.appendChild(option);
			}
		}
		
		// load printers list on startup
		loadPrinters();
	};
	
	function loadLabelFromWeb(){
		
		// use jQuery API to load label
		$.ajax({
			url: top.WRP+"/library/dymo/"+labelSelected.value,
			async:false,
			success:function(data){
				label = dymo.label.framework.openLabelXml(data);
			}
		});
	}
	
	/*Print Labels for selected items*/
function printLabels(){
	
	try{
		// Load label Structure
		loadLabelFromWeb();
		
		if (!label){
			top.falert("Load label before printing");
			return;
		}
		if(printersSelect.value==""){
			top.falert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
			return;
		}
		
		printCount = $("#lot_total").val();
		if(printCount>0){
			// set data using LabelSet and text markup
			var labelSet = new dymo.label.framework.LabelSetBuilder();
			var record; 
			/*Getting Data from Tabele*/
		
		
			upc_data = item_upc_code;
			print_data = item_printing_data;
			print_data = print_data.replace(/<br>/g, "<br/>");
			printCount = printCount.replace(/[^\d]/g, "");
			
			if(printCount==""){printCount=0;}
			printCount = parseInt(printCount);
			for(i=printCount; i>0; i--){
				/*Add Data to Dymo LabelSet*/
				record = labelSet.addRecord();
				record.setText('BARCODE', upc_data);
				record.setTextMarkup('TEXT', print_data);
				/*End Add Data to Dymo LabelSet*/
			}
			
			label.print(printersSelect.value, null, labelSet.toString());
			delete labelSet;
		}
		else{
			top.falert("Plese add quantity in <strong>Add Qty.</strong>");
		}
		/*End Getting Data from Table*/
	}
	catch(e){
		top.falert(e.message || e);
	}
}
// register onload event
$(window).on('load', function(){
	onload();
});
/*End Dymo Lanel Printing*/

</script>
<?php endif; ?>
<script type="text/javascript">
/*Bind Daepicker*/
$("#datepicker, .expiryDate").datepicker({
	changeMonth: true,
	changeYear: true,
	dateFormat: 'mm-dd-yy'
});
/*End Datepicker*/
var CURRENCY_SYMBOL = '<?php currency_symbol(); ?>';
$(document).ready(function(){
	currencySymbols();
});
function convertFloat(element)
{
	var value = $(element).val();
	value = parseFloat(value).toFixed(2);
	$(element).val(value);
}
$(document).ready(function () {
	//called when key is pressed in textbox
	$(".numberOnly").keypress(function (e) {
	 //if the letter is not digit then display error and don't type anything
	 if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});
});
</script>
</body>
</html>