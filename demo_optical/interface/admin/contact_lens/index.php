<?php
/*
File: index.php
Coded in PHP7
Purpose: Add/Edit/Delete: Contact Lenses
Access Type: Direct access
*/ 
require_once(dirname('__FILE__')."/../../../config/config.php"); 
require_once(dirname('__FILE__')."/../../../library/classes/functions.php"); 

$getname = imw_query("select id, upc_code from in_item where upc_code!='' and del_status='0'");
$getnameArr = array();
while($getnameRow=imw_fetch_array($getname))
{
	$getnameArr[] = "'".$getnameRow['id']."~~~".$getnameRow['upc_code']."'";
}

$proNameArr = implode(',',$getnameArr);

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$sql = "select * from cpt_category_tbl where cpt_category like '%contact lens%' order by cpt_category ASC";
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

/*Default Prac Code for Contact Lens*/
$default_prac_code = "";
$default_retail_price = "";
$d_prac_resp = imw_query("SELECT `prac_code`, `retail_price` FROM `in_prac_codes` WHERE `module_id`='3' AND `sub_module`='' LIMIT 1");
if($d_prac_resp && imw_num_rows($d_prac_resp)>0){
	$default_prac_row = imw_fetch_object($d_prac_resp);
	$default_prac_code = $default_prac_row->prac_code;
	$default_retail_price = $default_prac_row->retail_price;
}
/*End default Prac code for Contact Lens*/
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<!--<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.widget.js?<?php echo constant("cache_version"); ?>"></script>-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<?php 
if(isset($_REQUEST['upc_name']) && $_REQUEST['upc_name'] !="")
{
	echo "<script>
		$(document).ready(function(){
			upc('".$_REQUEST['upc_name']."')
		});
		</script>";
}
?>
<script type="text/javascript">
var default_prac_code = "<?php echo $default_prac_code; ?>";
var default_retail_price = "<?php echo $default_retail_price; ?>";
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
var dd_pro = new Array();
dd_pro["listHeight"] = 200;
dd_pro["noneSelected"] = "Select All";
$(document).ready(function(){

	/*selectProName = function(proname, id)
	{
		var chk_dup=0;
		$.each([<?php /*echo $proNameArr;*/ ?>], function( index, value ) 
		{
			var val = value.split('~~~');
			if((val[1].toLowerCase() == proname.value.toLowerCase()) && (val[0]!=id.value))
			{
				top.falert(proname.value+' Already Exists');			
				proname.value='';
				chk_dup=1;
				setTimeout(function(){proname.focus()},10);
				return false;
			}
		});
		
		if(chk_dup==1)
		{
			return false;
		}
	}*/
});
</script>
<script>
$(function() {
	var cyear = new Date().getFullYear();		
	$( "#datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
});
</script>
<script type="text/javascript">
function formula_change(flag){
	
	flag = ( typeof(flag) == 'undefined' ) ? false : flag;
	
	var formula_changed = $('#formula').val();
	if(flag)
		$('#formula_save').val(formula_changed);
	
	var retail_price_changed = calculate_retail_price(formula_changed, 0, 0); 
	//var retail_price_changed = calculate_retail_price(formula_changed, $('#wholesale_cost').val(), $('#purchase_price').val());
	retail_price_changed = retail_price_changed.toFixed(2);
	$("#caclulated_price").val(retail_price_changed);
	
	var retailPriceFlag = $('#retailpriceFlag').val();
	if(retailPriceFlag=='0'){
		$('#retail_price').val(retail_price_changed);
		price_total_fun();
	}
}

/*Mark price flag for the item*/
function retailPriceChanged(){
	
	var retailPrice = $('#retail_price').val();
	retailPrice = parseFloat(retailPrice).toFixed(2);
	if(retailPrice=='' || retailPrice=='0.00'){
		$('#retailpriceFlag').val(0);
		formula_change(false);
	}
	else{
		$('#retailpriceFlag').val(1);
	}
}

/*Add New Item based on custom Item (Admin>Contact Lenses>Brand)*/
function select_custom(manuf_id, brand_id, retail_price, itemCounter){
	
	var form = $( '#stock_form' )[0];
	$(form).find( 'option:selected' ).removeAttr( 'selected' );
	$(form).find( 'select' ).val( '0' );
	reset_form();
	
	$( '#item_prac_code' ).val(default_prac_code);
	$( '#manufacturer' ).val(manuf_id);
	get_manufacture_brand_cl(manuf_id, brand_id);
	$( '#retail_price' ).val(retail_price);
	$('#retailpriceFlag').val(1);
	
	var dataString = {action:'getFormula', module: 3, manuf:manuf_id, brand:brand_id};
	
	/*Get the Items formula*/
	$.ajax({
		type: "POST",
		url: "../ajax.php",
		data: dataString,
		cache: false,
		success: function(response){
			$( '#formula' ).val(response);
		},
		complete: function(){
			formula_change();
		}
	});
}

function upc(upc_code,current_txt,upc_txt)
{
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = "";
	
	if(ucode==""){
		ucode = $("#idoc_cl_id").val();
		dataString = 'action=managestock_idoc_cl&id='+ucode;
		if(ucode==""){return;}
	}
	else{
		dataString = 'action=managestock&upc='+ucode;
	}
		
		$.ajax({
		type: "POST",
		url: "../ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = $.parseJSON(response);
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
					if(typeof(item.idcoCL)!=="undefined" && item.idcoCL){
						
						reset_form(current_txt);
						
						/*Set CL Data*/
							$("#name").val(item.style);
							/*Set Prac code*/
								if(item.cpt_fee_id==0 || item.cpt_fee_id==""){
									$("#item_prac_code").val(default_prac_code);
								}
								else{
									if(item.price==""){
										get_prac_code_name(item.cpt_fee_id, false, true);
									}
									else{
										get_prac_code_name(item.cpt_fee_id);
										$("#retail_price").val(item.price);
									}
								}
							/*Set manufacturer*/
								$("#manufacturer option").filter(function() {
									return $.trim($(this).text()) == item.manufacturer; 
								}).prop('selected', true);
								
							$("#bc").val(item.base_curve);
							$("#diameter").val(item.diameter);
					}
					else{
						$("#edit_item_id").val(item.id);
						$("#upc_id").val(item.id);
						if(item.stock_image!="")
						{
							$("#item_image img").attr("src","../../../images/contact_lens_stock/"+item.stock_image);
						}
						else
						{
							$("#item_image img").attr("src","../../../images/no_product_image.jpg");	
						}
						$("#manufacturer").val(item.manufacturer_id);
						$("#upc_name").val(item.upc_code);
						$("#module_type").val(item.module_type_id);
						if(current_txt){$("#name").val(item.name);}
						$("#vendor").val(item.vendor_id);
						get_vendor_manufacturer(item.manufacturer_id,item.vendor_id);
						get_manufacture_brand_cl(item.manufacturer_id,item.brand_id);
						$("#brand_name").val(item.brand_id);
						$("#type").changeSelected(item.type_id);
						$("#lens_type").changeSelected(item.cl_type);
						$("#type_desc").val(item.type_desc);
						$("#bc").val(item.bc);
						$("#diameter").val(item.diameter);
						$("#sphere_min").val(item.sphere_positive);
						$("#cyl_min").val(item.cylindep_positive);
						$("#axis_min").val(item.axis);
						$("#sphere_max").val(item.sphere_positive_max);
						$("#cyl_max").val(item.cylindep_positive_max);
						//$("#axis_max").val(item.axis_max);
						$("#cat_name").changeSelected(item.cl_wear_schedule);
						$("#replacement").changeSelected(item.cl_replacement);
						$("#lens_packaging").val(item.cl_packaging);
						$("#supply").changeSelected(item.supply_id);
											
						if(item.r_check=="1")
						{
							$("#r_check").attr('checked',true);
						}
						if(item.l_check=="1")
						{
							$("#l_check").prop('checked',true);
						}
						$("#color").val(item.color);
						if(item.dot=="1")
						{
							$("#dot").prop('checked',true);
						}
						if(item.trial_chk=="1")
						{
							$("#trial_check").prop('checked',true);
						}
						$("#style").val(item.style);
						//$("#wholesale_cost").val(item.wholesale_cost);
						//$("#purchase_price").val(item.purchase_price);	
						
						$("#formula").val(item.formula);
						$("#formula_save").val(item.formula_save);
						
						$('#retailpriceFlag').val(item.retail_price_flag);
						var retail_price = calculate_retail_price(item.formula, item.wholesale_cost, item.purchase_price);
						retail_price = retail_price.toFixed(2);
						$("#caclulated_price").val(retail_price);
						if(item.retail_price_flag=='1'){
							$("#retail_price").val(item.retail_price);
						}
						else{
							$("#retail_price").val(retail_price);
						}
						
						$("#qty_on_hand").val(item.qty_on_hand);
						if(item.qty_on_hand=="")
						{
							$("#qty_on_hand_td").html(0);
						}
						else
						{
							$("#qty_on_hand_td").html(item.qty_on_hand);
						}
						$("#amount").val(item.amount);
						$("#discount").val(item.discount);
						if(item.item_prac_code==0 || item.item_prac_code==""){
							$("#item_prac_code").val(default_prac_code);
						}
						else{
							get_prac_code_name(item.item_prac_code);
						}
						if(item.discount_till!="00-00-0000"){
							$("#datepicker").val(item.discount_till);
						}
					}
				 });
			 }
		}
	}); 
}

/*Reset the Item form*/
function reset_form(current_txt){
	current_txt = (typeof(current_txt)=='undefined')?'':current_txt;
	/*Reset the Data*/
	$("#edit_item_id").val('');
	$("#upc_id").val('');
	$("#item_image img").attr("src","../../../images/no_product_image.jpg");	
		$("#manufacturer").val(0);
	if(current_txt!="upc_txt"){
		$("#upc_name").val('');
	}
	$("#module_type").val(3);
		$("#name").val('');
	$("#vendor").val(0);
	$("#type").changeSelected('');
	$("#lens_type").changeSelected('');
	$("#type_desc").val('');
		$("#bc").val('');
		$("#diameter").val('');
	$("#sphere_min").val('');
	$("#cyl_min").val('');
	$("#axis_min").val('');
	$("#sphere_max").val('');
	$("#cyl_max").val('');
	$("#axis_max").val('');
	$("#cat_name").changeSelected('');
	$("#replacement").changeSelected('');
	$("#lens_packaging").val(0);
	$("#supply").changeSelected('');
		$("#r_check").attr('checked',false);
		$("#l_check").prop('checked',false);
		$("#retail_price").val('');
	$("#color").val('0');
	$("#dot").prop('checked',false);
	$("#trial_check").prop('checked',false);
	$("#style").val('');
	//$("#wholesale_cost").val('0.00');
	//$("#purchase_price").val('0.00');
	$("#qty_on_hand").val(0);
	$("#qty_on_hand_td").html(0);
	$("#amount").val(0.00);
	$("#discount").val(0);
	$("#datepicker").val('');
	$("#item_prac_code").val('');
	
	$('#idoc_cl_id').val('');
	$('#brand_name').val(0);
	$('#formula').val('');
	$('#caclulated_price').val('');
	$('#retailpriceFlag').val(0);
	$('#formula_save').val('');
	$('#hed_tex').val(0);
/*End Reset the Data*/
}

function page_change_acc_type()
{
	 var as = $( "#module_type option:selected" ).text();
	var type = as.toLowerCase();
	var pages = new Array();
	pages['frame'] = "../frame/index.php";
	pages['lenses'] = "../lens/index.php";
	pages['contact lenses'] = "../contact_lens/index.php";
	pages['supplies'] = "../supplies/index.php";
	pages['medicine'] = "../medicines/index.php";
	pages['accessories'] = "../accessories/index.php";
	window.location.href = pages[type];
}

var addwin = '';
var pop_up ='';
var win ='';
function add_qty_fun(type){
	var item_id=document.getElementById('edit_item_id').value;
	top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('addwin','../lens/location_lot_popup.php?item_add='+type+'&item_id='+item_id,'location_popup','width=820,height=500,left=600,scrollbars=no,top=150');
	addwin.focus();
}
function price_total_fun(){
	var retail_price =0;
	var qty_on_hand =0;
	if(document.getElementById('retail_price').value>0){
		retail_price = document.getElementById('retail_price').value;
	}
	if(document.getElementById('qty_on_hand').value>0){
		qty_on_hand = document.getElementById('qty_on_hand').value;
	}
	var total_price = parseFloat(retail_price)*parseInt(qty_on_hand);
	document.getElementById('retail_price').value = parseFloat(retail_price).toFixed(2);
	document.getElementById('amount').value=total_price.toFixed(2);
}

function stock_search(type){
var manuf_id = document.getElementById('manufacturer').value;
var vendor = document.getElementById('vendor').value;
var brand = document.getElementById('brand_name').value;
top.WindowDialog.closeAll();
var win=top.WindowDialog.open('win','../contact_search.php?srch_id='+type+'&manuf_id='+manuf_id+'&vendor='+vendor+'&brand='+brand+'&material='+$('#type').val()+'&category='+$('#cat_name').val()+'&supply='+$('#supply').val()+'&color='+$('#color').val(),'location_popup','width=1050,height=500,left=180,scrollbars=no,top=150');
	win.focus();
}

<?php if($stringAllProcedures!=""){	/*?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php */} ?>


function get_vendor_manufacturer(mid,vid)
{
	if(mid!='')
	{
		var string = 'action=get_vendor&mid='+mid+'&vid='+vid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Please Select</option>" + response;
				$('#vendor').html(opt_data);
			}
		});
	}
}
function get_manufacture_brand_cl(mid,bid)
{
	if(mid!='')
	{
		var string = 'action=get_brand_contact&mid='+mid+'&bid='+bid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Please Select</option>" + response;
				$('#brand_name').html(opt_data);
			}
		});
	}
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
</script>
</head>
<body>
<?php 
if(isset($_REQUEST['save']))
{
extract($_POST);
$axis_max = "";
$savedId = contact_lens_stock($edit_item_id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$bc,$diameter,$sphere_min,$cyl_min,$axis_min,$sphere_max,$cyl_max,$axis_max,$r_check,$l_check,$cat_name,$brand_name,$type,$replacement,$supply,$color,$dot,$style,$qty_on_hand,$amount,$retail_price,$discount,$disc_date,$trial_check,$lens_type,$lens_packaging,$formula_save,$retailpriceFlag);
echo "<script>top.falert('Record saved successfully'); var loadItemId = ".((int)$savedId).";</script>";
/*echo "<script>top.falert('Record saved successfully'); window.location.href='index.php'</script>";*/
}
else{
	echo "<script>var loadItemId = false;</script>";
}

if(isset($_REQUEST['del']))
{
	extract($_POST);
	delete_stock_item($edit_item_id);
	header('Location: index.php');
}
?>
<style>
.serch_icon_stock{
	cursor:pointer;
	text-decoration:none;
	border:0;
	vertical-align:text-bottom;}
#caclulated_price{
	background-color: rgb(235, 235, 228);
	border: 1px solid rgb(204, 204, 204);
}
#changeRetailPrice{height:18px; vertical-align:text-top; cursor:pointer;}
</style>
<div class="listheading mt10"  >
	<div  style="width:1045px; float:left;">Contact Lenses</div>
	<div> <a href="javascript:void(0);" style="vertical-align:text-top" class="text_purpule" onClick="javascript:product_history(document.getElementById('edit_item_id').value);"> HX </a> <a style="margin-left:10px;" href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type').value);"> <img style="width:22px; margin-top:1px;" src="../../../images/search.png" class="serch_icon_stock" title="Search stock"/> </a> </div>
</div>
<form  onSubmit="return validateForm()" action="" name="contact_lens_form" id="stock_form" method="post" enctype="multipart/form-data">
	<div style="height:<?php echo $_SESSION['wn_height']-412;?>px; overflow-y: auto;">
		<input type="hidden" name="edit_item_id" id="edit_item_id" />
		<input type="hidden" name="upc_id" id="upc_id" value="">
		<input type="hidden" id="idoc_cl_id" value="">
		<!-- Contact Lens Make ID in iDoc -->
		<table class="table_collapse table_cell_padd5">
			<tr>
				<td style="width: 32px;">
					<label for="upc_name">UPC</label>
				</td>
				<td style="width:150px;">
					<input type="text" name="upc_name" id="upc_name" onChange="javascript:upc(document.getElementById('upc_id'),'upc_txt');" autocomplete="off" style="width:132px;"/>
				</td>
				<td style="width: 95px;">
					<label for="manufacturer">Manufacturer</label>
				</td>
				<td style="width:150px;">
					<select style="width:140px;" name="manufacturer" id="manufacturer" onChange="get_manufacture_brand_cl(this.value,'0'); get_vendor_manufacturer(this.value,'0');">
						<option value="0">Please Select</option>
						<?php $rows="";
                    		$rows = data("select id, manufacturer_name from in_manufacturer_details where cont_lenses_chk='1' and del_status='0' order by manufacturer_name asc");
		                    foreach($rows as $r){
						?>
							<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['manufacturer_name']); ?></option>
						<?php }	?>
					</select>
				</td>
				<td style="width: 45px;">
					<label for="name">Name</label>
				</td>
				<td style="width: 120px;">
					<input type="text" onChange="javascript:return upc(document.getElementById('upc_id'),'current_txt');" name="name" id="name" autocomplete="off" style="width:102px;" />
				</td>
				<td style="width: 50px;">
					<label for="vendor">Vendor</label>
				</td>
				<td style="width: 120px;">
					<select name="vendor" id="vendor" style="width:110px;">
						<option value="0">Please Select</option>
						<?php  						  
                    $rows="";
                    $rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
                    foreach($rows as $r)
                    { ?>
						<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['vendor_name']); ?></option>
						<?php }	?>
					</select>
				</td>
				<td style="width: 72px;">
					<label for="item_prac_code">Prac Code</label>
				</td>
				<td style="width: 100px;">
					<input type="text" name="item_prac_code" id="item_prac_code"  value="<?php echo $default_prac_code; ?>" style="width:82px;" autocomplete="off" />
				</td>
				<td style="width: 30px;">
					<label for="trial_check">Trial</label>
				</td>
				<td>
					<input type="checkbox" name="trial_check" id="trial_check" style="height:15px;width:15px;margin:0;vertical-align:bottom;" />
				</td>
			</tr>
			<tr style="display:none;">
				<td>
					<label for="module_type">Type</label>
				</td>
				<td>
					<select style="width:163px;" name="module_type" id="module_type" onChange="page_change_acc_type();">
							<?php $rows="";
							$rows = data("select * from in_module_type where del_status='0' order by module_type_name asc");
							foreach($rows as $r){
						?>
							<option <?php if(strtolower($r['module_type_name'])=="contact lenses"){ echo "selected"; } ?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['module_type_name']); ?></option>
							<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		
		<div class="module_border mt15">
			<table class="table_collapse table_cell_padd5" border="0">
				<tr>
					<td colspan="10"></td>
				</tr>
				<tr>
					<td style="width: 105px;">
						<label for="brand_name">Brand</label>
					</td>
					<td>
						<select name="brand_name" id="brand_name" style="width: 155px;" onChange="load_item_idoc(this.value);">
							<option value="0">Please Select</option>
							<?php
                      $rows="";
                      $rows = data("select `id`, `brand_name`, `source_idoc` from `in_contact_brand` where del_status='0' order by brand_name asc");		
                      foreach($rows as $r){?>
							<option value="<?php echo $r['id']; ?>" idoc_source="<?php echo $r['source_idoc']; ?>"><?php echo ucfirst($r['brand_name']);?></option>
							<?php }  ?>
						</select>
					<td/>
					<td></td>
					<td width="534" rowspan="6" align="left" valign="top">
						<div class="module_border">
							<table class="table_collapse table_cell_padd5">
								<tr>
									<td align="right" colspan="9" class="module_label"><div style="float:left;" class="module_heading">Lens Rx</div></td>
								</tr>
								<tr>
									<td style="text-align: center">
										<label><input name="sphere_min" id="sphere_min" type="text" style="width:70px; height:15px;" />Min.</label>
									</td>
									<td style="text-align: center">
										<label><input name="sphere_max" id="sphere_max" type="text" style="width:70px; height:15px;" />Max.</label>
									</td>
									<td style="width:60px;text-align: left;vertical-align:top">Sphere</td>
									<td style="text-align: center">
										<label><input name="cyl_min" type="text" style="width:70px; height:15px;" id="cyl_min" />Min.</label>
									</td>
									<td style="text-align: center">
										<label><input name="cyl_max" type="text" style="width:70px; height:15px;" id="cyl_max" />Max.</label>
									</td>
									<td style="width: 60px; text-align: left; vertical-align: top;">CYL</td>
									<td colspan="2" style="text-align: center">
										<label><input name="axis_min" id="axis_min" type="text" style="width:158px; height:15px;" />Range</label>
									</td>
									<!--td align="left" class="module_label"><input name="axis_max" id="axis_max" type="text" style="width:70px; height:15px;"  />&nbsp;Max.</td-->
									<td style="width: 64px; text-align: center; vertical-align: top;"> Axis </td>
								</tr>
								<tr>
									<td>
										<input name="bc" id="bc" type="text" style="width:70px; height:15px;" />
									</td>
									<td style="text-align: left">
										<label for="bc">BC</label>
									</td>
									<td style="text-align: right">
										<label for="diameter">Diameter</label>
									</td>
									<td style="text-align: left">
										<input type="text" name="diameter" id="diameter" style="width:70px; height:15px;" />
									</td>
									<td style="width:64;"> </td>
									<td></td>
									<td colspan="2" style="text-align: left; vertical-align: top;">
										<label style="margin-right: 15px; vertical-align: top;">R&nbsp;&nbsp;<input type="checkbox" name="r_check" id="r_check" style="height:15px;width:15px;margin:0;vertical-align:text-bottom;" /></label>
										<label>L&nbsp;&nbsp;<input type="checkbox" name="l_check" id="l_check" style="height:15px;width:15px;margin:0;vertical-align:text-bottom;"/></label>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				
				<tr>
					<td>
						<label for="lens_packaging">Packaging</label>
					</td>
					<td>
						<select name="lens_packaging" id="lens_packaging" style="width: 155px;">
							<option value="0">Please Select</option>
							<?php
								$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='5' and module_id='3' AND `del_status`='0' ORDER BY `opt_val` ASC";
								$data = imw_query($sql);
								if($data && imw_num_rows($data)>0){
									while($row = imw_fetch_assoc($data)){
										echo '<option value="'.$row['id'].'">'.$row['opt_val'].'</option>';
									}
								}
							?>
						</select>
					</td>
					<td style="width:125px;">&nbsp;</td>
				</tr>
				
				<tr>
					<td>
						<label for="lens_type">Lens Type</label>
					</td>
					<td class="rptDropDown">
						<select name="lens_type" id="lens_type" style="width: 137px;" multiple="multiple">
							<option value="">Please Select</option>
							<?php
								$sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' and module_id='0' AND `opt_sub_type` IN(0,1) AND `del_status`='0' ORDER BY `opt_val` ASC";
								$data = imw_query($sql);
								if($data && imw_num_rows($data)>0){
									while($row = imw_fetch_assoc($data)){
										echo '<option value="'.$row['id'].'">'.$row['opt_val'].'</option>';
									}
								}
							?>
						</select>
					</td>
					<td style="width:125px;">&nbsp;</td>
				</tr>
				
				<tr>
					<td>
						<label for="type">Lens Material</label>
					</td>
					<td class="rptDropDown">
						<select name="type" id="type" style="width: 137px;" multiple="multiple">
							<option value="">Please Select</option>
							<?php $rows="";
								$rows = data("select * from in_type where del_status='0' order by type_name asc");
								foreach($rows as $r)
								{ ?>
									<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['type_name']); ?></option>
						 <?php } ?>
						</select>
					</td>
					<td style="width:125px;">&nbsp;</td>
				</tr>
				
				<tr>
					<td>
						<label for="cat_name">Wear Schedule</label>
					</td>
					<td class="rptDropDown">
						<select name="cat_name" id="cat_name" style="width: 137px;" multiple="multiple">
							<option value="">Please Select</option>
					<?php $rows="";
						$rows = data("select * from in_contact_cat where del_status='0' order by cat_name asc");
						foreach($rows as $r){ ?>
							<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['cat_name']); ?></option>
				    <?php }	?>
						</select>
					</td>
					<td style="width:125px;">&nbsp;</td>
				</tr>
				
				<tr>
					<td>
						<label for="replacement">Replacement</label>
					</td>
					<td class="rptDropDown">
						<select name="replacement" id="replacement" style="width: 137px;" multiple="multiple">
							<option value="">Please Select</option>
					<?php $rows="";
						$rows = data("select * from in_options where opt_type='4' and module_id='3' AND `del_status`='0' ORDER BY `opt_val` ASC");
						foreach($rows as $r){ ?>
							<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['opt_val']); ?></option>
				  <?php } ?>
						</select>
					</td>
					<td style="width:125px;">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<label for="supply">Supply</label>
					</td>
					<td class="rptDropDown">
						<select name="supply" id="supply" style="width: 137px;" multiple="multiple">
							<option value="">Please Select</option>
			  <?php $rows="";
					$rows = data("select * from in_supply where del_status='0' order by supply_name asc");
					foreach($rows as $r){ ?>
						<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['supply_name']); ?></option>
			  <?php } ?>
						</select>
					</td>
					<td style="width:125px;">&nbsp;</td>
					<td colspan="2">&nbsp;</td>
				</tr>
				
				<tr>
					<td>
						<label for="color">Color</label>
					</td>
					<td class="rptDropDown">
						<select name="color" id="color" style="width: 155px;">
							<option value="0">Please Select</option>
				<?php $rows="";
                      $rows = data("select `id`, `color_name` from in_color where del_status='0' order by color_name asc");
                      foreach($rows as $r){ ?>
						<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['color_name']); ?></option>
				<?php }	?>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
				
				<!--<tr>
          <td align="left" valign="top">
              <select name="style" id="style" style="width:158px;">
               <option value="">Please Select</option>
                  <?php $rows="";
                        $rows = data("select * from `in_contact_style` where del_status='0' order by style_name asc");
                        foreach($rows as $r)
                        { ?>
                          <option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['style_name']); ?></option>	
                  <?php }	?>
              </select>
          </td>
          <td width="133" align="left" valign="top" class="module_label">Style</td>
          <td>&nbsp;</td>
           <td colspan="2" align="center" class="module_label"><!--<input name="dot" id="dot" type="checkbox"  />
           DOT  </td>
      </tr>-->
			</table>
			
			<table class="table_collapse table_cell_padd5">
				<tr>
					<td style="width:550px;">
<?php /*
<label><span style="margin-right: 8px;">Wholesale Price</span><input type="text" name="wholesale_cost" id="wholesale_cost" onChange="parse_float(this);formula_change(false);" style="width:144px;" class="currency" /></label> */ ?>
					</td>
					<td style="width:200px;">&nbsp;</td>
					<td style="width:150px;">
						<label><span style="margin-right: 10px;">DOT</span><input name="dot" id="dot" type="checkbox" style="height:15px;width:15px;margin:0;vertical-align:bottom;" /></label>
					</td>
					<td style="width:150px;">&nbsp;</td>
				</tr>
<?php /*				
				<tr>
					<td>
						<label><span style="margin-right: 17px;">Purchase Price</span><input name="purchase_price" id="purchase_price" style="width:144px" type="text" onChange="parse_float(this);formula_change(false);" class="currency" /></label>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
 */ ?>                                
				<tr>
					<td>
						<label>
							<span style="margin-right: 54px;">Formula</span>
							<input name="formula" id="formula" style="width:144px" type="text" class="currency" onChange="formula_change(true);" />
							<span style="display:inline-block; vertical-align:top;">=</span>
							<input type="text" name="caclulated_price" id="caclulated_price" style="width:70px; display:inline-block; vertical-align:top;" readonly />
							<input type="hidden" name="retailpriceFlag" id="retailpriceFlag" value="0" />
							<input name="formula_save"  id="formula_save" type="hidden" />
						</label>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td rowspan="3" style="padding-top: 34px;">
						<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/add_btn.png"  onClick="add_qty_fun('yes');" style="cursor:pointer;"><br>
						<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/minus_btn.png"  onClick="add_qty_fun('no');" style="cursor:pointer;">
					</td>
				</tr>
				
				
				<tr>
					<td>
						<label><span style="margin-right: 41px;">Retail Price</span><input type="text" name="retail_price" id="retail_price" style="width:144px;" onChange="price_total_fun();retailPriceChanged();" class="currency" value="<?php echo $default_retail_price; ?>" /></label>
					</td>
					<td>
						<input name="qty_on_hand" id="qty_on_hand" type="hidden" readonly />
						Quantity on Hand:&nbsp;<span id="qty_on_hand_td" style="font-weight:bold;">0</span>
					</td>
					<td>
						<label><span style="margin-bottom: 0px; margin-right: 8px;">Amount</span><input name="amount" id="amount" style="width:72px;" type="text" readonly class="currency" /></label>
						</td>
				</tr>
				<tr>
					<td colspan="3">
						<label><span style="margin-bottom: 0px; margin-right: 62px;">Discount</span><input name="discount" id="discount" type="text" style="width: 144px;" /></label>
						<label><span style="margin-right: 20px; margin-left: 45px;">Dis. Until</span><input id="datepicker" name="disc_date" type="text" class="date-pick" style="background-size:20px 23px;padding:3px;width:100px;" value="" /></label>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="btn_cls mt10">
		<input type="hidden" name="hed_tex" id="hed_tex" value="0">
		<div style="display:none">
			<input type="submit" name="save" value="Save" id="saveBtn" />
			<input type="submit" name="del" id="delBtn" value="Delete" />
		</div>
	</div>
</form>
<script type="text/javascript">

//var obj6 = new actb(document.getElementById('item_prac_code'),customarrayProcedure);

	$("#item_prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC'
	});
	
	$("#upc_name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensData',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'upc'
	});
	$("#name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensDataStock',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'name'
	});
</script>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect_edited.js?<?php echo constant("cache_version"); ?>"></script> 
<script type="text/javascript">
function submitFrom(){
	$('#saveBtn').click();
}
function newForm(){
	window.location.href= WEB_PATH+'/interface/admin/contact_lens/index.php';
}
function closeWindow(mode){
	window.location.href= WEB_PATH+'/interface/admin/contact_lens/index.php';
}

function load_item_idoc(value){
	
	if(value!="" || value!=0){
		option = $('#brand_name option[value="'+value+'"]');
		if($(option).attr('idoc_source')=="1"){
			
			var name = $(option).html();
			
			var params = {};
			params.action = "find_contact_id";
			params.item_name = name;
			
			$.ajax({
				method: 'POST',
				data: params,
				url: top.WRP+"/interface/admin/ajax.php",
				success: function(response){
					if(response!=""){
						$("#upc_id").val(response);
						//$("#name").val(name).trigger('change');
					}
				}
			});
		}
	}
}


$(document).ready(function() {		
	validateForm = function(){
		check = document.contact_lens_form;
		if(check.name.value.replace(/\s/g, "") == "" && check.upc_name.value.replace(/\s/g, "") == ""){
			top.falert("Please Enter Upc Code or Item Name");
			check.upc_name.value="";		
			check.upc_name.focus();
			return false;
		}
	}
	$("#upc_name").keypress(
		function (evt){
		if(evt.keyCode==13){
			$("#item_prac_code").focus();
			$("#hid_tex").val("1");
			return false;
		}
	});
	$("#lens_type").multiSelect(dd_pro);
	$("#cat_name").multiSelect(dd_pro);
	$("#replacement").multiSelect(dd_pro);
	$("#supply").multiSelect(dd_pro);
	$("#type").multiSelect(dd_pro);
	//$("#color").multiSelect(dd_pro);

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","New","top.main_iframe.admin_iframe.newForm()");
	mainBtnArr[2] = new Array("frame","Make Copy","top.main_iframe.admin_iframe.copy_item_new()");
	mainBtnArr[3] = new Array("frame","Cancel","top.main_iframe.admin_iframe.closeWindow()");
	mainBtnArr[4] = new Array("frame","Delete","top.main_iframe.admin_iframe.delete_item()");
	top.btn_show("admin",mainBtnArr);
	
	if(loadItemId && loadItemId!=''){
		upc(loadItemId, 'upc_txt');
	}
});
</script>
<style type="text/css">
a.multiSelect{
	font-size:14px;
	background:#FFF url("../../../library/css/images/scrollDown.gif") 98.7% center no-repeat;
}
</style>
</body>
</html>