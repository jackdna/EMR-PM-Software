<?php 
/*
File: index.php
Coded in PHP7
Purpose: Add/Edit/Delete: Accessories
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php"); 

$stringAllUpc = get_upc_name_id('7');

$AllUpcArray=array();
$AllUpcIdArray=array();

foreach($stringAllUpc as $key=>$value)
{
	$AllUpcIdArrays[]=$key;
	$exp = explode('-:',$value);
	$AllUpcArray[]="'".$value."'";
	$AllNameArray[]="'".$exp[1]."'";
}
$AllUpcIdArray = implode(',',$AllUpcIdArrays);
$AllUpcArray = implode(',',$AllUpcArray);
$AllNameArray = implode(',',$AllNameArray);

/*$getname = imw_query("select id, upc_code from in_item where upc_code!='' and del_status='0'");
$getnameArr = array();
while($getnameRow=imw_fetch_array($getname))
{
	$getnameArr[] = "'".$getnameRow['id']."~~~".$getnameRow['upc_code']."'";
}

$proNameArr = implode(',',$getnameArr);*/

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	/*$proc_code_arr=array();
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

	$stringAllProcedures = substr($stringAllProcedures,0,-1);*/
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//

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

<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
$(document).ready(function(){

	/*selectProName = function(proname, id)
	{
		var chk_dup=0;
		$.each([<?php echo $proNameArr; ?>], function( index, value ) 
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

var custom_array_upc_id;
<?php if($AllUpcArray!=""){?>
	var custom_array_upc= new Array(<?php echo remLineBrk($AllUpcArray); ?>);
<?php } ?>
<?php if($AllNameArray!=""){?>
	var custom_array_name= new Array(<?php echo remLineBrk($AllNameArray); ?>);
<?php } ?>
<?php if($AllUpcIdArray!="" && count($AllUpcIdArrays)>1){?>
	custom_array_upc_id= new Array(<?php echo remLineBrk($AllUpcIdArray); ?>);
<?php } else{ ?>
	custom_array_upc_id= new Array('<?php echo $AllUpcIdArray; ?>');
<?php } ?>

<?php if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php } ?>

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
<script type="text/javascript">
function upc(upc_code,current_txt,upc_txt)
{
	
	//var ucode = $.trim(upc_code.value);
	
	//var dataString = 'action=managestock&upc='+ucode;
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = 'action=managestock&upc='+ucode;
	var arrayname=custom_array_name;
	var name_val=$("#name").val();
	if(($.inArray(name_val, arrayname)== -1)&& name_val && current_txt=='current_txt'){
		return false;
		}
	var upcname=custom_array_upc;
	var upc_val=$("#upc_name").val();
	if(($.inArray(upc_val, upcname)== -1) && upc_val && (current_txt=='upc_txt')){
		//return false;
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
					$("#edit_item_id").val(item.id);
					$("#upc_id").val(item.id);
					if(item.stock_image!="")
					{
						$("#item_image img").attr("src","../../../images/supplies_stock/"+item.stock_image);
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
					$("#brand").val(item.brand_id);					
					$("#type_desc").val(item.type_desc);
					$("#num_size").val(item.num_size);
					$("#measurement").val(item.measurment);
					$("#char_size").val(item.char_size);
					$("#bridge").val(item.bridge);
					$("#color_code").val(item.color_code);
					$("#color").val(item.color);
					$("#other").val(item.other);
					if(item.harcardous=="1")
					{
						$("#hazardous").prop('checked',true);
					}
					$("#retail_price").val(item.retail_price);
					//$("#wholesale_cost").val(item.wholesale_cost);
					//$("#purchase_price").val(item.purchase_price);	
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
					get_prac_code_name(item.item_prac_code);
					if(item.discount_till!="00-00-0000"){
					$("#datepicker").val(item.discount_till);
					}
				 });
			 }
			 else
			 {
				 
				// $("#stock_form")[0].reset();
			 }
		}
	}); 
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
	//alert(pages[type]);
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
	top.WindowDialog.closeAll();
	var win=top.WindowDialog.open('win','../stock_search.php?srch_id='+type+'&manuf_id='+manuf_id+'&vendor='+vendor,'location_popup','width=1237,height=500,left=180,scrollbars=no,top=150');
	win.focus();
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

	$savedId = accessories_stock($edit_item_id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$type_desc,$num_size,$measurement,$char_size,$other,$hazardous,$qty_on_hand,$amount,$retail_price,$discount,$disc_date);
	echo "<script>top.falert('Record saved successfully'); var loadItemId = ".((int)$savedId).";</script>";
	/*echo "<script>top.falert('Record saved successfully'); window.location.href='index.php'</script>";*/
	//header('Location: index.php');
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
</style>
<div class="listheading mt10">
	<div style="width:1045px; float:left;">Accessories</div>
	<div>
		<a href="javascript:void(0);"  style="vertical-align:text-top" class="text_purpule" onClick="javascript:product_history(document.getElementById('edit_item_id').value);">
           HX
        </a>
		<a style="margin-left:10px;" href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type').value);">
        <img style="width:22px; margin-top:1px;" src="../../../images/search.png" class="serch_icon_stock" title="Search stock"/>
        </a>
    </div>
</div>
<div style="height:<?php echo $_SESSION['wn_height']-450;?>px;">
    <form onSubmit="return validateForm()" action="" name="material_form" id="stock_form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="edit_item_id" id="edit_item_id" value="" />
	<input type="hidden" name="upc_id" id="upc_id" value="">
	<input type="hidden" name="module_type" id="module_type" value="7">
	<table class="table_collapse table_cell_padd5">
		<tr>
			<td style="width:40px;" class="module_label">
				<label for="upc_name">UPC</label>
			</td>
			<td style="width:163px;">
				<input type="text" name="upc_name" id="upc_name" onChange="javascript:return upc(document.getElementById('upc_id'),'upc_txt');" autocomplete="off" style="width:150px" />
			</td>
			<td style="width:110px;" class="module_label">
				<label for="manufacturer">Manufacturer</label>
			</td>
			<td style="width:229px;">
				<select style="width:165px;" name="manufacturer" id="manufacturer" onChange="get_vendor_manufacturer(this.value,'0');">
					<option value="">Please Select</option>
					<?php $rows="";
					$rows = data("select * from in_manufacturer_details where accessories_chk='1' and del_status='0' order by manufacturer_name asc");
					foreach($rows as $r)
					{ ?>
					<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['manufacturer_name']); ?></option>
					<?php }	?>
				</select>
			</td>
			<td class="module_label">
				<label for="name">Name</label>
			</td>
			<td style="width:150px;">
				<input type="text" onChange="javascript:return upc(document.getElementById('upc_id'),'current_txt');" name="name" id="name" autocomplete="off" style="width:100px;" />
			</td>
			<td style="width:50px;" class="module_label">
				<label for="vendor">Vendor</label>
			</td>
			<td style="width:167px;">
				<select style="width:150px;" name="vendor" id="vendor">
					<option value="">Please Select</option>
					<?php 
					$sql="select id,vendor_name from in_vendor_details where del_status = '0' order by vendor_name asc";
					$res = imw_query($sql);
					while($row = imw_fetch_array($res))
					{
					?>
					<option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['vendor_name']); ?></option>
					<?php 
					} ?>
				</select>
			</td>
			<td class="module_label" style="width:150px;">
				<label for="item_prac_code">Prac Code</label>
			</td>
			<td style="width:150px;">
				<input type="text" name="item_prac_code" id="item_prac_code"  value="" style="width:80px;" autocomplete="off" />
			</td>
		</tr>
	</table>
	
	<div class="module_border mt15">
		<table class="table_collapse table_cell_padd5">
			<tr>
				<td style="width:110px;">
					<label for="type_desc">Description</label>
				</td>
				<td coslpan="2" style="width:526px;">
					<input name="type_desc" id="type_desc" type="text" />
				</td>
				<td colspan="2">
					
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<label for="num_size" style="margin-right:20px;">Size</label>
					<select name="num_size" id="num_size" style="width:90px;margin-right:15px;">
						<option value="">Select</option>
						<?php   for($i = 1;$i<=1000;$i++)
						{
						echo "<option value='".$i."'>$i</option>";
						}	
						?>
					</select>
					<label for="measurement" style="margin-right:20px;">Measurement</label>
					<select name="measurement" id="measurement" style="width:90px;">
						<option value="">Select</option>
						<?php $sql="select id,measurment_name from in_supplies_measurment where del_status = '0'";
						$res = imw_query($sql);
						while($rowsm = imw_fetch_array($res)) {
						?>
						<option value="<?php echo $rowsm['id']; ?>"><?php echo ucfirst($rowsm['measurment_name']); ?> </option>
						<?php } ?>
					</select>
					<label for="hazardous" style="float:right">Hazardous</label>
				</td>
				<td colspan="2">
					<input name="hazardous" id="hazardous" type="checkbox" style="height: 15px; width: 15px; vertical-align: bottom; margin-right: 42px; margin-left: 0px;" />
					<input type="file" name="file" />
				</td>
			</tr>
			
			<tr>
				<td>
					<label for="char_size" style="margin-right:120px;display:none;">Size</label>
					<input name="char_size" id="char_size" type="hidden" value="" />
					<?php /*
					<select name="char_size" id="char_size" style="width:90px;margin-right:15px;">
						<option value="">Select</option>
						<?php $sql="select id,size_name from in_supplies_size where del_status = '0' order by size_name asc";
						$res = imw_query($sql);
						while($rowss = imw_fetch_array($res)) {
						?>
						<option value="<?php echo $rowss['id']; ?>"><?php echo ucfirst($rowss['size_name']); ?> </option>
						<?php } ?>
					</select>
					*/ ?>
					<label for="other" style="margin-right:72px;display:none;">Other</label>
					<input type="text" name="other" id="other" style="width:82px;display:none;" />
					<!-- label for="wholesale_cost">Wholesale Price</label -->
				</td>
				
				<td>
<?php /*<input name="wholesale_cost" id="wholesale_cost" type="text" style="width:147px;" onChange="parse_float(this);" class="currency" /> */ ?>
					<span style="vertical-align:top;float:right;">
						<input name="qty_on_hand" id="qty_on_hand" type="hidden" style="width:50px;" readonly/>
						Quantity on Hand:
					</span>
				</td>
				<td valign="top" style="width:230px;">
					<span id="qty_on_hand_td" style="font-weight: bold; display: inline-block; margin-right: 46px;">0</span>
					<label for="amount">Amount</label> <input class="acc_amt currency" name="amount" id="amount" type="text" style="width:80px;margin:0 5px 0 0;position:inherit;" readonly/>
				</td>
				<td valign="top" rowspan="3">
					<span style="display:inline-block;"> <img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/add_btn.png"  onClick="add_qty_fun('yes');" style="cursor:pointer;"><br>
					<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/minus_btn.png"  onClick="add_qty_fun('no');" style="cursor:pointer;"> </span>
				</td>
			</tr>
<?php /*			
			<tr>
				<td>
					<label for="purchase_price">Purchase Price</label>
				</td>
				<td>
					<input name="purchase_price" id="purchase_price" style="width:147px" type="text" onChange="parse_float(this);" class="currency" />
				</td>
			</tr>
 */ ?>			
			<tr>
				<td>
					<label for="retail_price">Retail Price</label>
				</td>
				<td>
					<input name="retail_price" id="retail_price" type="text" style="width:147px;" onChange="price_total_fun();" class="currency" />
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<label>Discount <input name="discount" id="discount" type="text" style="width:82px;" /></label>
					&nbsp;&nbsp;
					<label>Dis. Until <input id="datepicker" type="text" name="disc_date" value="" class="date-pick" style="background-size:20px 23px;padding:3px;width:100px;" /></label>
				</td>
			</tr>
		</table>
	</div>
	
	<div class="btn_cls mt10">
		<input type="hidden" name="hid_tex" value="0"> 
		<div style="display:none">
			<input type="submit" name="save" value="Save" id="saveBtn"/>
			<input type="submit" name="del" id="delBtn" value="Delete" />                                    
		</div>
	</div>
     </form>
</div>
<script type="text/javascript">

	/*var obj6 = new actb(document.getElementById('item_prac_code'),customarrayProcedure);
	var obj7 = new actb(document.getElementById('upc_name'),custom_array_upc,"","",document.getElementById('upc_id'),custom_array_upc_id);
	var obj8 = new actb(document.getElementById('name'),custom_array_name,"","",document.getElementById('upc_id'),custom_array_upc_id);*/
	
	$("#item_prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCode'
	});
	
	$("#upc_name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'accessoriesData',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'upc'
	});
	$("#name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'accessoriesData',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'name'
	});
</script>


<script type="text/javascript">
function submitFrom(){
	$('#saveBtn').click();
}
function newForm(){
	window.location.href= WEB_PATH+'/interface/admin/accessories/index.php';
}
function closeWindow(mode){
	window.location.href= WEB_PATH+'/interface/admin/accessories/index.php';
}

$(document).ready(function() {	

validateForm = function(){

	check = document.material_form;

	/*if((check.upc_name.value.replace(/\s/g, "") == "")){
		top.falert("Please Enter UPC Code");		
		check.upc_name.value="";
		check.upc_name.focus();		
		return false;
	}*/
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