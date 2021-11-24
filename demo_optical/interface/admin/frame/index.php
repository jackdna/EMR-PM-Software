<?php
/*
File: index.php
Coded in PHP7
Purpose: Add/Edit/Delete: Frame
Access Type: Direct access
*/ 
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");


/*$getname = imw_query("select id, upc_code from in_item where upc_code!='' and del_status='0'");
$getnameArr = array();
while($getnameRow=imw_fetch_array($getname))
{
	$getnameArr[] = "'".$getnameRow['id']."~~~".$getnameRow['upc_code']."'";
}
$proNameArr = implode(',',$getnameArr);*/

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$proc_code_desc_arr=array();
	$sql = "select * from cpt_category_tbl where cpt_category like '%optical%' order by cpt_category ASC";
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
				$proc_code_desc_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_desc"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//

/*Default Prac Code for Frame*/
$default_prac_code = "";
$default_retail_price = "";
$d_prac_resp = imw_query("SELECT `prac_code`, `retail_price` FROM `in_prac_codes` WHERE `module_id`='1' AND `sub_module`='' LIMIT 1");
if($d_prac_resp && imw_num_rows($d_prac_resp)>0){
	$default_prac_row = imw_fetch_object($d_prac_resp);
	$default_prac_code = $default_prac_row->prac_code;
	$default_retail_price = $default_prac_row->retail_price;
}

/*End default Prac code for Frame*/
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>">
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<!--<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.widget.js?<?php echo constant("cache_version"); ?>"></script>-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 

<script type="text/javascript">
var default_prac_code = "<?php echo $default_prac_code; ?>";
var default_retail_price = "<?php echo $default_retail_price; ?>";
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
$(function() 
{
	var cyear = new Date().getFullYear();		
	$( "#datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
});

<?php if($stringAllProcedures!=""){/*?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php */} ?>

</script>
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

<script>
$(document).ready(function(){
		/* $("#color").change(function(event){
				if($.trim($(this).val())!='')
				{
					var string = 'action=color_code&color_id='+$(this).val();
					$.ajax({
						type: "POST",
						url: "../ajax.php",
						data: string,
						cache: false,
						success: function(response)
						{
							$("#color_code").val(response);
						}
					});
				}
				else
				{
							$("#color_code").val("");	
				}
				
		}); */
		
		$("#otherframe , #otherback").hide();
		
		$("#otherback").click(function(){
			$("#otherframe").val('');
			$("#otherback , #otherframe").hide();
			$("#frame_style_name").show();	
			//$("#frame_style option[value='0']").prop('selected',true);	
			$("#frame_style_name").val('');
			$("#frame_style").val('');	
		});		
		
		$("#frame_style").change(function(){
			if($(this).val()=="other")
			{
				$(this).val('');
				$(this).hide();
				$("#otherframe , #otherback").show();	
			}
		});		

		
});
</script>

<script type="text/javascript">
function formula_change(flag){
	
	flag = ( typeof(flag) == 'undefined' ) ? false : flag;
	
	var formula_changed = $('#formula').val();
	if(flag)
		$('#formula_save').val(formula_changed);
	
	if(formula_changed){
		//var retail_price_changed = calculate_retail_price(formula_changed, 0, 0);
		var retail_price_changed = calculate_retail_price(formula_changed, $('#wholesale_cost').val(), $('#purchase_price').val());
		retail_price_changed = retail_price_changed.toFixed(2);
		$("#caclulated_price").val(retail_price_changed);

		var retailPriceFlag = $('#retailpriceFlag').val();
		if($('#retail_price').val()<=0){
			$('#retail_price').val(retail_price_changed);
			price_total_fun();
		}
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

function upc(upc_code,current_txt,upc_txt)
{
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = 'action=managestock&upc='+ucode+"&req="+Math.random();
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
					$("#edit_item_id").val(item.id);
					$("#upc_id").val(item.id);
					
					if(item.stock_image!=""){
						$('#item_image img').attr('src', top.WRP+'/images/frame_stock/'+item.stock_image);
					}
					else{
						$('#item_image img').attr('src', top.WRP+'/images/frame_stock/no_image_xl.jpg');
					}
					
					$("#manufacturer").val(item.manufacturer_id);
					get_manufacture_brand(item.manufacturer_id,item.brand_id);
					$("#upc_name").val(item.upc_code);
					$("#module_type").val(item.module_type_id);
					if(current_txt){$("#name").val($("<span/>").html(item.name).text());}
					$("#vendor").val(item.vendor_id);
					get_vendor_manufacturer(item.manufacturer_id,item.vendor_id);
					$("#brand").val(item.brand_id);
					$("#fpd").val(item.fpd);
					$("#frame_style").val(item.frame_style);
					$("#frame_style_name").val(item.frame_style_name);
					get_brand_style(item.brand_id,item.frame_style);
					$("#frame_shape").val(item.frame_shape);
					$("#a").val(item.a);
					$("#b").val(item.b);
					$("#ed").val(item.ed);
					$("#dbl").val(item.dbl);
					$("#temple").val(item.temple);					
					$("#bridge").val(item.bridge);
					$("#color_code").val(item.color_code);
					$("#color").val(item.color_name);
					$("#wholesale_cost").val(item.wholesale_cost);
					$("#purchase_price").val(item.purchase_price);
					
					$("#formula").val(item.formula);
					$("#formula_save").val(item.formula_save);
					
					$('#retailpriceFlag').val(item.retail_price_flag);
					var retail_price = calculate_retail_price(item.formula, item.wholesale_cost, item.purchase_price);
					retail_price = retail_price.toFixed(2);
					$("#caclulated_price").val(retail_price);
					if(item.retail_price>0){
						$("#retail_price").val(item.retail_price);
					}
					else{
						$("#retail_price").val(retail_price);
					}
									
					$("#qty_on_hand").val(item.qty_on_hand);
					$("#threshold").val(item.threshold);
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
					$("#disc_date").val(item.disc_date);
					$("#gender").val(item.gender);
					$("#type").val(item.type_id);
					if(item.item_prac_code==0 || item.item_prac_code==""){
						$("#item_prac_code").val(default_prac_code);
					}
					else{
						get_prac_code_name(item.item_prac_code);
					}
					if(item.discount_till!="00-00-0000")
					{	
						$("#datepicker").val(item.discount_till);			
					}
					
					if(item.style_other!="")
					{
						$("#otherback , #otherframe , #otherback").show();
						$("#frame_style").hide();	
						$("#otherframe").val(item.style_other);
					}					
				 });
			 }
			 else
			 {
					
			 }
		}
	}); 
}


function scaleSize(maxW, maxH, currW, currH){

	var ratio = currH / currW;
	
	if(currW >= maxW && ratio <= 1){
	currW = maxW;
	currH = currW * ratio;
	} else if(currH >= maxH){
	currH = maxH;
	currW = currH / ratio;
	}
	
	return [currW, currH];
}

function get_manufacture_brand(mid,bid)
{
	if(mid!='')
	{
		var string = 'action=get_brand&mid='+mid+'&bid='+bid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Please Select</option>" + response;
				$('#brand').html(opt_data);
				
				if(bid>0)
				 {
					 $("#frame_style_name").attr('disabled',false);
				 }
				 else
				 {
					$("#frame_style_name").attr('disabled','disabled');
					$("#frame_style_name").val('');
					$("#frame_style").val('');
				 }
				
			}
		});
	}
}

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

function get_brand_style(bid,sid)
{
	/*if(bid!='')
	{
		var string = 'action=get_style&bid='+bid+'&sid='+sid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Please Select</option>" + response + "<option value='other'>Other</option>";				
				$('#frame_style').html(opt_data);
			}
		});
	}*/
	 if(bid>0)
	 {
		 $("#frame_style_name").attr('disabled',false);
	 }
	 else
	 {
		 $("#frame_style_name").attr('disabled','disabled');
	 }
	return true;
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
var searchWin ='';
function add_qty_fun(type){
	var item_id=document.getElementById('edit_item_id').value;
	top.WindowDialog.closeAll();
	var location_popup=top.WindowDialog.open('location_popup','../lens/location_lot_popup.php?item_add='+type+'&item_id='+item_id,'location_popup','width=820,height=500,left=600,scrollbars=no,top=150');
	location_popup.focus();
}
function price_total_fun(){
	var retail_price =0;
	var qty_on_hand =0;
	var total_price = 0;
	if(document.getElementById('retail_price').value>0){
		retail_price = document.getElementById('retail_price').value;
	}
	if(document.getElementById('qty_on_hand').value>0){
		qty_on_hand = document.getElementById('qty_on_hand').value;
	}	
	total_price = parseFloat(retail_price)*parseInt(qty_on_hand);
	document.getElementById('retail_price').value = parseFloat(retail_price).toFixed(2);
	document.getElementById('amount').value=total_price.toFixed(2);
}

function stock_search(type){
	var manuf_id = document.getElementById('manufacturer').value;
	var vendor = document.getElementById('vendor').value;
	var brand = document.getElementById('brand').value;
	var color_id = document.getElementById('color').value;
	var shape_id = document.getElementById('frame_shape').value;
	var style_id = document.getElementById('frame_style').value;
	top.WindowDialog.closeAll();
	var searchWin=top.WindowDialog.open('location_popup','../stock_search.php?srch_id='+type+'&manuf_id='+manuf_id+'&vendor='+vendor+'&from=style&brand='+brand+'&color='+color_id+'&shape='+shape_id+'&style='+style_id,'location_popup','width=1420,height=520,left=120,scrollbars=no,top=150');
	searchWin.focus();
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
</head>
<body>

<?php 
if(isset($_REQUEST['save']))
{
	extract($_POST);
        
        if( trim($color) != '' )
        {
	        /*Fix to enter custom color from Inventory Screen*/
			$sqlColor = "SELECT `id` FROM `in_frame_color` WHERE `color_name`='".addslashes($color)."' AND `del_status`!=2";
			$respColor = imw_query($sqlColor);
	        if($respColor && imw_num_rows($respColor)>0)
	        {
			$colorid = imw_fetch_assoc($respColor);
	                $color_id = $colorid['id'];
	                unset($colorid);
	        }
	        else
	        {
	            /*Add New Color*/
	            $sqlcolorAdd = "INSERT INTO `in_frame_color` SET `color_name`='".addslashes($color)."', `color_code`='".addslashes($color_code)."', `entered_date`='".date('Y-m-d')."', `entered_time`='".date('H:i:s')."', `entered_by`='".addslashes($_SESSION['authId'])."'";
	            imw_query($sqlcolorAdd);
	            $color_id = imw_insert_id();
	        }
        }

	$savedId = frame_stock($edit_item_id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$brand,$frame_shape,$frame_style,$style_other,$a,$b,$ed,$dbl,$temple,$fpd,$bridge,$color_code,$color_id,$wholesale_cost,$purchase_price,$retail_price,$threshold,$qty_on_hand,$amount,$discount,$disc_date,$gender,$type,$formula_save,$retailpriceFlag,$frame_style_name);
	echo "<script>top.falert('Record saved successfully'); var loadItemId = ".((int)$savedId).";</script>";
	/*echo "<script>top.falert('Record saved successfully'); window.location.href='index.php'</script>";
	//header('Location: index.php');
	if(isset($mode) && $mode == "fill_frm_sel"){
		echo "<script>opener.get_details_by_upc('".$upc_name."');window.close();</script>";
	}*/
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
        <div class="listheading mt10">
			<div style="width:1045px; float:left;">Frames</div> 
			
			<div>
			<a href="javascript:void(0);" class="text_purpule" style="vertical-align:text-top" onClick="javascript:product_history(document.getElementById('edit_item_id').value);">
            	HX
             </a>

			<a style="margin-left:10px;" href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type').value);">
          	  <img style="width:22px; margin-top:1px;" src="../../../images/search.png" class="serch_icon_stock" title="Search stock"/>
            </a>
            </div>
		</div>
<form action="" name="frame_form" id="stock_form" method="post" enctype="multipart/form-data" onSubmit="return validateForm();">        
  <div style="height:<?php echo $_SESSION['wn_height']-450;?>px;overflow-y:auto;">
		<input type="hidden" name="edit_item_id" id="edit_item_id" value=""/>
		<input type="hidden" name="mode" id="mode"  value="<?php echo isset($_REQUEST['mode'])?$_REQUEST['mode']:"";?>"/>
		<input type="hidden" name="upc_id" id="upc_id" value="">
		<table class="table_collapse table_cell_padd5">
			<tr>
				<td style="width:40px;" class="module_label">
					<label for="upc_name">UPC</label>
				</td>
				<td style="width:163px;">
					<input type="text" onChange="javascript:return upc(document.getElementById('upc_id'),'upc_txt');" name="upc_name" id="upc_name"  value="" style="width:150px" autocomplete="off" />
				</td>
				<td style="width:110px;" class="module_label">
					<label for="manufacturer">Manufacturer</label>
				</td>
				<td style="width:229px;">
					<select style="width:165px;" name="manufacturer" id="manufacturer" onChange="get_manufacture_brand(this.value,'0'); get_vendor_manufacturer(this.value,'0');">
						<option value="0">Please Select</option>
						<?php $rows="";
							$rows = data("select * from in_manufacturer_details where frames_chk='1' and del_status='0' order by manufacturer_name asc");
							foreach($rows as $r){
						?><option  value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['manufacturer_name']); ?></option><?php } ?>
					</select>
				</td>
				<td class="module_label">
					<label for="name">Name</label>
				</td>
				<td style="width:150px;">
					<input type="text" onChange="javascript:return upc(document.getElementById('upc_id'),'current_txt');" name="name" id="name" autocomplete="off" style="width:100px;" />
				</td>
				<td class="module_label" style="width:50px;">
					<label for="vendor">Vendor</label>
				</td>
				<td style="width:167px;">
					<select style="width:150px;" name="vendor" id="vendor">	
						<option value="0">Please Select</option>
						<?php 
							$rows="";
							$rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
							foreach($rows as $r)
							{ 
						?><option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['vendor_name']); ?></option><?php }	?>
					</select>
				</td>
				<td style="width:150px;" class="module_label">
					<label for="item_prac_code">Prac Code</label>
				</td>
				<td style="width:150px;">
					<input type="text" name="item_prac_code" id="item_prac_code" style="width:80px;" autocomplete="off" value="<?php echo $default_prac_code; ?>" />
				</td>
			</tr>
			<tr style="display:none;">
				<td class="module_label">
					<label for="module_type">Type</label>
				</td>
				<td>
					<select style="width:165px;" name="module_type" id="module_type" onChange="page_change_acc_type();">
					<?php $rows="";
						  $rows = data("select * from in_module_type where del_status='0' order by module_type_name asc");
						  foreach($rows as $r){
					?>
						<option <?php if(strtolower($r['module_type_name'])=="frame"){ echo "selected"; } ?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['module_type_name']); ?></option>	
					<?php }	?>
					</select>
				</td>
			</tr>
		</table>
		  
		  <div class="module_border mt15" style="width:98%;">
			<table class="table_collapse table_cell_padd5">
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td style="width:180px;"></td>
					<td></td>
					<td></td>
					<td colspan="2" rowspan="6"><div>
							<table class="table_collapse table_cell_padd5">
								<tr>
									<td align="center"><div id="item_image"> <img alt="" src="../../../images/no_product_image.jpg" width="220" class="module_border" style="padding:5px" /> </div></td>
								</tr>
							</table>
						</div></td>
				</tr>
				<tr>
					<td style="width:108px;" valign="top" class="module_label"><label for="brand">Brand</label></td>
					<td width="167" valign="top"><select style="width:159px;" name="brand" id="brand" onChange="javascript:get_brand_style(this.value,'0');">
							<option value='0'>Please Select</option>
							<?php /*commented to fix heavy page size due to framesData*//*$rows="";
				  $rows = data("select * from in_frame_sources where del_status='0' order by frame_source asc");
				  foreach($rows as $r)
				  { ?><option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['frame_source']); ?></option><?php }*/ ?>
						</select></td>
				</tr>
				<tr>
					<td valign="top" class="module_label"><label for="frame_shape">Frame Shape</label></td>
					<td valign="top"><select style="width:159px;" name="frame_shape" id="frame_shape">
							<option value="">Please Select</option>
							<?php  $rows="";
				$rows = data("select * from in_frame_shapes where del_status='0' order by shape_name asc");
				foreach($rows as $r)
				{ ?>
							<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['shape_name']); ?></option>
							<?php }	?>
						</select></td>
				</tr>
				<tr>
					<td valign="top" class="module_label"><label for="frame_style_name">Frame Style</label></td>
					<td valign="top">
						<input name="frame_style_name" id="frame_style_name" type="text" value="" style="width:151px; float:left;" disabled autocomplete="off" />
						<input name="style_other" id="otherframe" style="float:left; width:151px; display:none;" type="text" value="" />
						<input type="hidden" name="frame_style" id="frame_style" value="">
						<img id="otherback" style="float:left; cursor:pointer; position:relative; top:7px; right:7px;" src="../../../images/icon_back.png" /></td>
				</tr>
				<tr>
					<td valign="top" class="module_label" colspan="4">
						<label>A <input name="a" id="a"  style="width:50px;margin:0 15px 0 37px;" type="text" value="" /></label>
						<label>B <input name="b" id="b" style="width:50px;margin:0 15px 0 35px;" type="text" value="" /></label>
						<label>ED <input style="width:50px;margin:0 15px 0 8px;" type="text" name="ed" id="ed" value="" /></label>
						<label>DBL <input name="dbl" id="dbl" style="width:50px" type="text" value="" /></label>
					</td>
				</tr>
				<tr>
					<td valign="top" class="module_label" colspan="4">
						<label>Temple <input name="temple" id="temple" style="width:50px;margin:0 15px 0 0;" type="text" value="" /></label>
						<label>Bridge <input name="bridge" id="bridge" style="width:50px;margin:0 15px 0 0;" type="text" value="" /></label>
						<label>FPD <input name="fpd" id="fpd" style="width:50px" type="text" value="" /></label>
					</td>
					<td valign="top" class="module_label" >&nbsp;</td>
					<td valign="top" class="module_label" >&nbsp;</td>
					<td valign="top" class="module_label" >&nbsp;</td>
					<td colspan="2" valign="top" align="center"><input type="file" name="file" style="margin-left:25px;" /></td>
				</tr>
			</table>
			
			<table class="table_collapse table_cell_padd5" style="margin-top:15px;">
				<tr>
					<td style="width:108px;"><label for="color" style="margin-right:11px;">Color</label></td>
					<td colspan="5" valign="top">
						<input name="color" id="color" style="width:76px;" type="text" value="" autocomplete="off">
						<input type="hidden" name="color_id" id="color_id" value="">
						<label for="color_code" style="margin-left:60px;">Color Code</label>
						<input name="color_code" id="color_code" style="width:60px" type="text" />
					</td>
                                </tr>

				<tr>
					<td><label for="wholesale_cost">Wholesale Price</label></td>
					<td colspan="5" valign="top" class="module_label"><input name="wholesale_cost" id="wholesale_cost" style="width:70px" type="text" onChange="parse_float(this);formula_change(false);" class="currency" readonly /><span style="color: #969696; font-size: 12px">(from latest stock)</span></td>
				</tr>
 
               <tr>
					<td><label for="purchase_price">Purchase Price</label></td>
                    <td colspan="5" valign="top" class="module_label"><input name="purchase_price" id="purchase_price" style="width:70px" type="text" onChange="parse_float(this);formula_change(false);" class="currency" readonly /><span style="color: #969696; font-size: 12px">(from latest stock)</span></td>
					<td colspan="2" valign="top" align="right">&nbsp;</td>
               </tr>

				<tr>
					<td><label for="purchase_price">Formula</label></td>
					<td valign="top" class="module_label">
						<input name="formula" id="formula" style="width:70px" type="text" class="currency" onChange="formula_change(true);" />
						<span style="display:inline-block; vertical-align:top;">=</span>
						<input type="text" name="caclulated_price" id="caclulated_price" style="width:70px; display:inline-block; vertical-align:top;" readonly />
						<!-- 0=calculated, 1=modified -->
						<input type="hidden" name="retailpriceFlag" id="retailpriceFlag" value="0" />
						<input name="formula_save"  id="formula_save" type="hidden" />
					</td>
					<td>&nbsp;</td>
					<td colspan="5">&nbsp;</td>
				</tr>
				<tr>
				  <td><label for="retail_price">Retail Price</label></td>
				  <td valign="top" class="module_label">
					<input name="retail_price" id="retail_price" style="width:70px" type="text" onChange="price_total_fun();retailPriceChanged();" class="currency" value="<?php echo $default_retail_price; ?>" />
					<label for="threshold" style="float:right;margin-left:15px;">Threshold Qty.</label>
				  </td>
				   <td valign="top" class="module_label">
					<input type="text" name="threshold" id="threshold" style="width:30px;">&nbsp;
				   </td>
				   
				  <td width="220" align="left" valign="top" class="module_label" >
					<input name="qty_on_hand" id="qty_on_hand" style="width:40px;" type="hidden" readonly />
				   Quantity on hand:&nbsp;<span id="qty_on_hand_td" style="font-weight:bold;">0</span>
				  </td>
				   <td width="180" align="left" valign="top" class="module_label" >
					 <label for="amount">Amount</label>&nbsp;&nbsp;<input name="amount" id="amount" style="width:96px;" type="text" readonly class="currency" />
				  </td>
				  <td width="126" rowspan="2" align="left" valign="top" class="module_label" >
					  <img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/add_btn.png" onClick="add_qty_fun('yes');" style="cursor:pointer;" /><br />
					  <img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/minus_btn.png"  onClick="add_qty_fun('no');" style="cursor:pointer;" />
				  </td>
				</tr>
				 <tr>
				  <td><label for="discount">Discount</label>
				  <td valign="top" class="module_label" >
					<input name="discount" id="discount" style="width:78px;" type="text" />
					<label for="datepicker" style="float:right;">Dis. Until</label>
				  </td>
				  <td align="left" valign="top" class="module_label" >
					<input id="datepicker" class="date-pick" style="background-size:20px 23px;padding:3px;width:100px;" name="disc_date" value="" type="text" />
				  </td>
				   <td align="left" valign="top" class="module_label">
					<label for="gender">Gender</label>
					<select name="gender" id="gender" style="width:120px;">
					 <option value="">Please Select</option>
					  <option value="men">Men</option>
					  <option value="women">Women</option>
					  <option value="boy">Boy</option>
					  <option value="girl">Girl</option>
					  <option value="unisex">Unisex</option>
					</select>
				  </td>
				  <td align="left" valign="top" class="module_label" >
					<label for="type" style="margin-right:26px;">Type</label>
					<select name="type" id="type" style="width:111px;">
					  <option value="">Please Select</option>
					  <?php $rows="";
							$rows = data("select * from in_frame_types where del_status='0' order by type_name asc");
							foreach($rows as $r)
							{ ?>
							<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['type_name']); ?></option>	
							<?php }	?>   
					</select>
				  </td>
				</tr>
			</table>
	   </div>
    </div>
             <div class="btn_cls mt10">
             	<input type="hidden" id="hid_tex" value="0">
                <div style="display:none">
	                <input type="submit" name="save" value="Save" id="saveBtn" />
					<input type="submit" name="del" id="delBtn" value="Delete" />                                    
                </div>
            </div> 
            
		</form>
	
<script type="text/javascript">
	
	$("#item_prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO'
	});
	
	$("#upc_name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'framesData',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'upc'
	});
	
	$("#name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'framesData',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'name'
	});
	
	$("#color").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'frameColors',
		hidIDelem: document.getElementById('color_code'),
		showAjaxVals: 'name'
	});
	
	$("#frame_style_name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'framesStyle',
		hidIDelem: document.getElementById('frame_style'),
		minLength:3,
		maxVals: 10,
		showAjaxVals: 'name',
		extraElements:{ 1: document.getElementById('brand'), 2: document.getElementById('frame_shape')}
	});
	
</script>

<script type="text/javascript">
function submitFrom(){
	$('#saveBtn').click();
}
function newForm(){
	window.location.href= WEB_PATH+'/interface/admin/frame/index.php';
}
function closeWindow(mode){
	window.location.href= WEB_PATH+'/interface/admin/frame/index.php';
}
$(document).ready(function() {	

validateForm = function(){
	check = document.frame_form;
	if(check.name.value.replace(/\s/g, "") == "" && check.upc_name.value.replace(/\s/g, "") == ""){
		top.falert("Please Enter Upc Code or Item Name");
		check.upc_name.value="";
		check.upc_name.focus();
		return false;
	}
	
	/*Validate Frame Color Selected Value*/
	var color_val = $.trim($('#color').val());
	if(color_val==''){
		$('#color, #color_id').val('');
		return true;
	}
	/*End Validate Frame Color Selected Value*/
	
	/*Get Color Id* /
	var returnFlag = false;
	$.ajax({
		method:'POST',
		data: 'action=gtFrameColorId&colorName='+color_val,
		url: WEB_PATH+'/interface/admin/ajax.php',
		async: false,
		success: function(resp){
			resp = $.trim(resp);
			
			if(resp==''){
				top.falert('Please select valid color from typeahead list.');
				$('#color, #color_id').val('');
			}
			else{
				$('#color_id').val(resp);
				returnFlag = true;
			}
		}
	});
	return returnFlag;
        /*End Get Color Id*/
        return true;
	
}
	$("#upc_name").keypress(
	function (evt){
	if(evt.keyCode==13){
	$("#item_prac_code").focus();
	$("#hid_tex").val("1");
	return false;
	}
	});	
	
	
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
</body>
</html>