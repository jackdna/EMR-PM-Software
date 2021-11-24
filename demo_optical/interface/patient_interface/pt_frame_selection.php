<?php 
	/*
	File: pt_frame_selection.php
	Coded in PHP7
	Purpose: Patient Frame Information
	Access Type: Direct access
	*/
  require_once(dirname('__FILE__')."/../../config/config.php"); 
  require_once(dirname(__FILE__)."/../../library/classes/functions.php"); 	
  require_once(dirname(__FILE__)."/../../library/classes/common_functions.php"); 	
  require_once(dirname(__FILE__)."/../../library/classes/drop_down.php");
  $objDropDown = new dropDown();
  
$patient_id=$_SESSION['patient_session_id'];
$order_id=$_SESSION['order_id'];

function get_in_misc_val($fieldName)
{
	$sql = "SELECT $fieldName FROM in_alternative_settings WHERE id = '1'";
	$res = imw_query($sql);
	$row = imw_fetch_assoc($res);
	return $row[$fieldName];
}


function get_related_frames($upc_code){

	$arrRelItm = array();
	$sql = "SELECT * FROM in_item WHERE upc_code = '".$upc_code."'";
	$res = imw_query($sql);
	if(imw_num_rows($res) > 0){

		$row = imw_fetch_assoc($res);

		$sql = "SELECT id, stock_image, upc_code FROM in_item WHERE module_type_id='1'";
		
			if(get_in_misc_val("price_min_range") > 0 && get_in_misc_val("price_max_range") > 0)	{
			$sql .=" AND retail_price > '".($row['retail_price']-get_in_misc_val("price_min_range"))."' AND retail_price < '".(get_in_misc_val("price_max_range")+$row['retail_price'])."'";
			}
			if(get_in_misc_val("a_min_range") > 0 && get_in_misc_val("a_max_range") > 0)	{
			$sql .=" AND a > '".($row['a']-get_in_misc_val("a_min_range"))."' AND a < '".(get_in_misc_val("a_max_range")+$row['a'])."'";
			}
			if(get_in_misc_val("ed_min_range") > 0 && get_in_misc_val("ed_max_range") > 0)	{
			$sql .=" AND ed > '".($row['ed']-get_in_misc_val("ed_min_range"))."' AND ed < '".(get_in_misc_val("ed_max_range")+$row['ed'])."'";
			}
			if(get_in_misc_val("fpd_min_range") > 0 && get_in_misc_val("fpd_max_range") > 0)	{
			$sql .=" AND fpd > '".($row['fpd']-get_in_misc_val("fpd_min_range"))."' AND fpd < '".(get_in_misc_val("fpd_max_range")+$row['fpd'])."'";
			}
			if(get_in_misc_val("frame_shape") == "1")	{
			$sql .=" AND frame_shape = '".$row['frame_shape']."'";
			}
			if(get_in_misc_val("gender") == "1")	{
			$sql .=" AND gender = '".$row['gender']."'";
			}
			if(get_in_misc_val("frame_color") == "1")	{
			$sql .=" AND color = '".$row['color']."'";
			}
			if(get_in_misc_val("frame_style") == "1")	{
			$sql .=" AND frame_style = '".$row['frame_style']."'";
			}
			if(get_in_misc_val("frame_brand") == "1")	{
			$sql .=" AND brand_id = '".$row['brand_id']."'";
			}
			$sql .= " AND upc_code!='".$upc_code."' ";
			$sql .= "limit 0,7";
			//return $sql; die();
		$res = imw_query($sql);
		while($row = imw_fetch_assoc($res)){
			$arrRelItm[$row['id']]['id'] = $row['id'];
			$arrRelItm[$row['id']]['stock_image'] = $row['stock_image'];
			$arrRelItm[$row['id']]['upc_code'] = $row['upc_code'];
		}
	}
	return $arrRelItm;		
}
if($_REQUEST['mode'] == "get_related_frames"){
	
	$arrRelItm = get_related_frames($_REQUEST['upc_code']);
	if(count($arrRelItm)>0)
	echo json_encode($arrRelItm);
	else echo "";
	die();
}
?>
<?php
$action=$_REQUEST['frm_method'];
$sel_pic=$_REQUEST['sel_pic'];
$pt_wear_pic=$_REQUEST['pt_wear_pic_1'];
$order_detail_id=$_REQUEST['order_detail_id'];

if($action=="next"){
	$frame_order_detail_id=$_REQUEST['order_detail_id_1'];
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='lens_selection.php?frame_id=".$frame_order_detail_id."'</script>";
}
elseif($action=="save"){
	$frame_order_detail_id=$_REQUEST['order_detail_id_1'];
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection.php'</script>";
}
else if($action=="previous"){
	echo "<script type='text/javascript'>window.location.href='pt_picture.php'</script>";
}
else if($action=="cancel"){
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection.php'</script>";
}
if($_SESSION['order_id']>0 && $action!="new_form"){
	if($action!="" && $action>0){
		$whr=" and id='$action'";
	}else{
		if($order_detail_id>0){
			$whr=" and id='$order_detail_id'";
		}
	}
	$sel_qry=imw_query("select * from in_order_details where order_id ='$order_id' $whr and patient_id='$patient_id' and module_type_id='1' and del_status='0' order by show_default desc");
	$sel_order=imw_fetch_array($sel_qry);
	if($sel_pic==""){
		$pt_wear_pic=$sel_order['pt_wear_pic'];
		
	}
	$order_detail_id=$sel_order['id'];
	$item_id=$sel_order['item_id'];
	$sel_qry2=imw_query("select qty_on_hand, stock_image, threshold from in_item where id ='$item_id'");
	$sel_item=imw_fetch_array($sel_qry2);
	$stock_image=$sel_item['stock_image'];
}

$pt_sql = "SELECT * from in_patient_pictures WHERE image ='$pt_wear_pic'";
$pt_res = imw_query($pt_sql);
$pt_row = imw_fetch_assoc($pt_res);
$pt_wear_pic_id=$pt_row['id'];


$stringAllUpc = get_upc_name_id('1');
$AllUpcArray=array();
$AllUpcIdArrays=array();
$AllNameArray = array();

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

//------------------------	START GETTING DATA FOR MENUS TO DX Code -----------------------//
	$dx_code_arr=array();
	$sql = "select * from diagnosis_category order by category ASC";
	$rez = imw_query($sql);	
	while($row=imw_fetch_array($rez)){
		$cat_id = $row["diag_cat_id"];		
		$sql = "select * from diagnosis_code_tbl WHERE diag_cat_id='".$cat_id."' AND delete_status = '0' order by d_prac_code ASC";
		$rezCodes = imw_query($sql);
		$arrDXSubOptions = array();
		if(imw_num_rows($rezCodes) > 0){
			while($rowCodes=imw_fetch_array($rezCodes)){
				$arrDXSubOptions[] = array($rowCodes["d_prac_code"]."-".$rowCodes["diag_description"],$xyz, $rowCodes["d_prac_code"]);
				$arrDXCodesAndDesc[] = $rowCodes["diagnosis_id"];
				$arrDXCodesAndDesc[] = $rowCodes["d_prac_code"];
				$arrDXCodesAndDesc[] = $rowCodes["diag_description"];
				
				$code = str_replace(";","~~~",$rowCodes["d_prac_code"]);
				$DX_desc = str_replace(";","~~~",$rowCodes["diag_description"]);
				$stringAllDX.="'".str_replace("'","",$code)."',";	
				$stringAllDX.="'".str_replace("'","",$DX_desc)."',";
				$dx_code_arr[$rowCodes["diagnosis_id"]]=$rowCodes["d_prac_code"];
			}
		$arrDXCodes[] = array($row["category"],$arrDXSubOptions);
		}		
	}

	$stringAllDX = substr($stringAllDX,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Dx Code ------------------------//

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$proc_code_desc_arr=array();
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
				$proc_code_desc_arr[$rowCodes["cpt_fee_id"]]=$rowCodes["cpt_desc"];
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/js/themes/base/jquery.ui.all.css" />
<link rel="stylesheet" href="../../library/css/inv_css.css" />
<link href="tooltip.css" type="text/css" rel="stylesheet"/>

<script src="../../library/js/jquery-1.10.1.min.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js"></script>
<script src="tooltip.js"></script>

<script type="text/javascript">
function validateForm(){
	check = document.addframe;
	if((check.upc_name_1.value.replace(/\s/g, "") == "")){
		top.falert("Please Enter UPC Code");
		check.upc_name_1.value="";
		check.upc_name_1.focus();
		return false;
	}
}
</script>

<script>
$(document).ready(function(){

var fr_price=fr_qty=fr_dis=fr_amt=0;
$("#pof_manufacturer_id_1 , #pof_brand_id_1 , #pof_style_id_1 , #pof_shape_id_1 , #pof_color_id_1").hide();

$("#in_add_1").click(function() 
{
	if($("#in_add_1").is(':checked'))
	{	
		$("#a_1 , #b_1 , #ed_1 , #dbl_1 , #temple_1 , #bridge_1 , #fpd_1").removeAttr('readonly');
		$("#manufacturer_id_1 , #brand_id_1 , #style_id_1 , #shape_id_1 , #color_id_1").hide();
		$("#pof_manufacturer_id_1 , #pof_brand_id_1 , #pof_style_id_1 , #pof_shape_id_1 , #pof_color_id_1").show();
		fr_price = $("#price_1").val();
		fr_qty = $("#qty_1").val();
		fr_dis = $("#discount_1").val();
		fr_amt = $("#total_amount_1").val();
		
		$("#price_1 , #qty_1 , #discount_1 , #total_amount_1").val('0');
		$("#price_1 , #qty_1 , #discount_1 , #total_amount_1").attr('readonly','readonly');
	}
	else
	{
		$("#a_1 , #b_1 , #ed_1 , #dbl_1 , #temple_1 , #bridge_1 , #fpd_1").attr('readonly','readonly');
		$("#manufacturer_id_1 , #brand_id_1 , #style_id_1 , #shape_id_1 , #color_id_1").show();
		$("#pof_manufacturer_id_1 , #pof_brand_id_1 , #pof_style_id_1 , #pof_shape_id_1 , #pof_color_id_1").hide();
		if($("#price_hidden_1").val()!="")
		{
			$("#price_1").val($("#price_hidden_1").val());
			$("#qty_1").val($("#qty_hidden_1").val());
			$("#discount_1").val($("#discount_hidden_1").val());
			$("#total_amount_1").val($("#total_amount_hidden_1").val());
		}
		else
		{
			$("#price_1").val(fr_price);
			$("#qty_1").val(fr_qty);
			$("#discount_1").val(fr_dis);
			$("#total_amount_1").val(fr_amt);
		}
		$("#price_1 , #qty_1 , #discount_1 , #total_amount_1").removeAttr('readonly','readonly');
	}
});

if($("#in_add_1").is(':checked'))
{	
	$("#a_1 , #b_1 , #ed_1 ,#dbl_1 , #temple_1 , #bridge_1 , #fpd_1").removeAttr('readonly');		
	$("#manufacturer_id_1 , #brand_id_1 , #style_id_1 , #shape_id_1 , #color_id_1").hide();
	$("#pof_manufacturer_id_1 , #pof_brand_id_1 , #pof_style_id_1 , #pof_shape_id_1 , #pof_color_id_1").show();
	$("#price_1 , #qty_1 , #discount_1 , #total_amount_1").val('0');
	$("#price_1 , #qty_1 , #discount_1 , #total_amount_1").attr('readonly','readonly');
}
else
{
	$("#a_1 , #b_1 , #ed_1 ,#dbl_1 , #temple_1 , #bridge_1 , #fpd_1").attr('readonly','readonly');
	$("#manufacturer_id_1 , #brand_id_1 , #style_id_1 , #shape_id_1 , #color_id_1").show();
	$("#pof_manufacturer_id_1 , #pof_brand_id_1 , #pof_style_id_1 , #pof_shape_id_1 , #pof_color_id_1").hide();
	$("#price_1 , #qty_1 , #discount_1 , #total_amount_1").removeAttr('readonly','readonly');
}

});
</script>

<script>
/*function open_frame_frm(){
	if($("#in_add_1").is(':checked'))
	{	
upc_name = $("#upc_name_1").val();
window.open(top.WRP+"/interface/admin/frame/index.php?mode=fill_frm_sel&upc_name="+upc_name,"Add Frame",'width=800,height=500,resizable=1');
	}
	else
	{
		top.falert("Please select checkbox to add new frame");		
	}
}*/
function frm_sub_fun_callBack(result)
{
	if(result==false)
	{
		return false;	
	}
}
function frm_sub_fun(action){
	if(action=="cancel")
	{
		top.fconfirm('Are you sure to cancel this Order ?',frm_sub_fun_callBack);
	}
	
	if(action=="save" || action=="next")
	{
		var dis=0;
		dis = $("#discount_1").val();
		if(dis.slice(-1)=='%'){
			dis = dis.replace('%','');
		}
		if(dis[0]=="$")
		{
			dis = dis.replace(/^[$]+/,"");			
		}
		
		if(dis>0)
		{
			var dis_code = $("#discount_code").val();
			if(dis_code=="")
			{
				top.falert("Please Select Discount Code");
				return false;
			}
		}
	}
	
	$("#frm_method").val(action);
	document.addframe.submit();
}
function chk_dis_fun(){
	var amt =0;
	var dis =0;
	var quantity =0;
	
	if(document.getElementById('discount_1').value!="")
	{
		var dis = document.getElementById('discount_1').value;
	}
	if(document.getElementById('qty_1').value>0)
	{
		quantity = document.getElementById('qty_1').value;
	}
	if(document.getElementById('price_1').value!="")
	{
		var amt = document.getElementById('price_1').value;
	}
	
	var total = cal_discount(amt,dis);
	var final_price=parseFloat(total)*parseInt(quantity);
	document.getElementById('total_amount_1').value=final_price.toFixed(2);
	
	var final_allowed=parseFloat(amt)*parseInt(quantity);
	document.getElementById('allowed_1').value=final_allowed.toFixed(2);
}

function stock_search(type,fromVal){
	
	var win="";
	var pt_pic = "";
	var module_typePatval = document.getElementById('module_typePat').value;
	var manuf_id = document.getElementById('manufacturer_id_1').value;
	var brand = document.getElementById('brand_id_1').value;
	var color_id = document.getElementById('color_id_1').value;
	var shape_id = document.getElementById('shape_id_1').value;
	var style_id = document.getElementById('style_id_1').value;
	
	var pt_pic1 = '<?php echo $_REQUEST['sel_pic']; ?>';
	var pt_pic2 = '<?php echo $pt_wear_pic_id; ?>';
	if(pt_pic1!="")
	{
		pt_pic = pt_pic1;
	}
	else if(pt_pic2!="")
	{
		pt_pic = pt_pic2;
	}
	//alert(pt_pic);
	if(document.getElementById('order_detail_id_1').value>0){
		var order_detail_id_1 = document.getElementById('order_detail_id_1').value;
	}else{
		var order_detail_id_1 = 'new_form';
	}
	
	if(fromVal=='pt_int')
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup','../admin/stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from=style&frm_dw='+fromVal+'&manuf_id='+manuf_id+'&brand='+brand+'&color='+color_id+'&shape='+shape_id+'&style='+style_id+'&picture='+pt_pic,'location_popup','width=1250,height=500,left=120,scrollbars=no,top=150');
	}
	if(fromVal=='')
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup','../admin/stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from=style&manuf_id='+manuf_id+'&brand='+brand+'&picture='+pt_pic,'location_popup','width=1250,height=500,left=120,scrollbars=no,top=150');
	}
	ptwin.focus();
}

function old_order_detail(val){
	window.location.href='pt_frame_selection.php?order_detail_id='+val;
}

<?php if($stringAllDX!=""){	?>
	var customarrayDX= new Array(<?php echo remLineBrk($stringAllDX); ?>);
<?php } ?>
<?php if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php } ?>

<?php if($AllUpcArray!=""){?>
	var custom_array_upc= new Array(<?php echo remLineBrk($AllUpcArray); ?>);
<?php } ?>

<?php if($AllNameArray!=""){?>
	var custom_array_name= new Array(<?php echo remLineBrk($AllNameArray); ?>);
<?php } ?>

var custom_array_upc_id;
<?php if($AllUpcIdArray!="" && count($AllUpcIdArrays)>1){?>
	custom_array_upc_id= new Array(<?php echo remLineBrk($AllUpcIdArray); ?>);
<?php } else{ ?>
	custom_array_upc_id= new Array('<?php echo $AllUpcIdArray; ?>');
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

<script>

var manuField = "";
var brandField = "";
var colorField = "";
var shapeField = "";
var styleField = "";
var dataString = "";
var itemID = "";

autofillupc = function()
{
	//alert($("#item_id_1").val());
	if($("#item_id_1").val()==""){
		
		 manuField = $("#manufacturer_id_1");
		brandField = $("#brand_id_1");
		colorField = $("#color_id_1");
		shapeField = $("#shape_id_1");
		styleField = $("#style_id_1");
		
		if(styleField.val()!='')
		{
			dataString = 'action=findupc&manufac='+manuField.val()+'&brand='+brandField.val()+'&color='+colorField.val()+'&shape='+shapeField.val()+'&style='+styleField.val();
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: dataString,
				cache: false,
				success: function(responseData)
				{
					//alert('response = '+ responsereturn);
					if(responseData=="false")
					{
						//$("#firstform").trigger('reset');
						//alert("No Result Found");
					}
					else
					{
						var dataArr = $.parseJSON(responseData);
						//alert(dataArr);
						if(dataArr!="")
						{
							$.each(dataArr, function(index,value) 
							{
								
								if(dataArr.length==1){ get_details_by_upc(value);	}
								else{
								stock_search(document.getElementById('module_type_id_1').value,'pt_int');
								}
							});
						}
						//alert(itemID);						
					}
				}
			});
		}
	}
}
</script>


<?php 
if(isset($_REQUEST['upc_name']) && $_REQUEST['upc_name'] !="")
{
	echo "<script>
		$(document).ready(function(){
			get_details_by_upc('".$_REQUEST['upc_name']."')
		});
		</script>";
}
?>
<style>
.serch_icon_stock{
	cursor:pointer;
	text-decoration:none;
	vertical-align:text-bottom;}
</style>
</head>
    <body>
<div class="listheading mt10">
	<div style="float:left;">
		<div style="width:<?php if($order_id!=""){ echo "400"; } else { echo "1080"; } ?>px; float:left;">Frame Selection</div>
		<?php if($order_id!="" || $order_id>0) { ?>
		<div style="width:675px; float:left;">Order #<?php echo $order_id; ?></div>
		<?php } ?>
		<div style="float:left;">
		<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type_id_1').value,'');">
        	<img style="margin-top:1px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock" />
		</a>
       </div>
	</div>
</div>
	<form name="addframe" id="firstform" action="" method="post">
	<div style="height:<?php echo $_SESSION['wn_height']-430;?>px; overflow-y:auto; float:left; width:98%;">
	<input type="hidden" name="frm_method" id="frm_method" value="<?php echo $action; ?>">
	<input type="hidden" name="order_detail_id_1" id="order_detail_id_1" value="<?php echo $sel_order['id']; ?>">
	<input type="hidden" name="item_id_1" id="item_id_1" value="<?php echo $sel_order['item_id']; ?>">
	<input type="hidden" name="module_type_id_1" id="module_type_id_1" value="1">
    <input type="hidden" name="module_typePat" id="module_typePat" value="patient_interPage">
    <input type="hidden" name="page_name" value="pt_frame_selection">    
	<input type="hidden" name="upc_id_1" id="upc_id_1" value="">
	<input type="hidden" name="cur_date" id="cur_date" value="<?php echo date('Y-m-d'); ?>">
    <input type="hidden" name="allowed_1" id="allowed_1" value="<?php echo $sel_order['allowed']; ?>">
    
	<?php if($order_id>0){ 
		$other_orders_module="1";
		$img_path = "frame_stock/";	
		require_once("other_orders.php");
	 } 
	 	$all_dx_codes="";
		if($sel_order['dx_code']!="")
		{
			$dx_singl=array();
			$get_dxs = explode(",",$sel_order['dx_code']);
			for($fd=0;$fd<count($get_dxs);$fd++)
			{
				$dx_singl[] = $dx_code_arr[$get_dxs[$fd]];
			}
			$all_dx_codes = join('; ',$dx_singl);
		}
	 ?>
	   <table class="table_collapse table_cell_padd5">
	   	  <tr>                   
			<td align="left" class="module_label">
              <input name="upc_name_1" type="text" class="s_tbx" id="upc_name_1" value="<?php echo $sel_order['upc_code']; ?>" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_1'))" style="width:100px; margin: 5px 0px 0px 10px;" />&nbsp;&nbsp;UPC &nbsp;
			  <input type="text" name="item_name_1" id="item_name_1" value="<?php echo $sel_order['item_name']; ?>" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_1'))" style="width:100px; margin-left:20px;">&nbsp;&nbsp;Item Name&nbsp;
			  <input type="checkbox" name="in_add_1" value="1" <?php if($sel_order['pof_check']=="1"){ echo "checked='checked'"; } ?> id="in_add_1" class="span" style="margin-left:20px;">&nbsp;POF&nbsp;
			  <input type="text" name="item_prac_code_1" id="item_prac_code_1" value="<?php echo $proc_code_arr[$sel_order['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'price_1','frm');" title="<?php echo $proc_code_desc_arr[$sel_order['item_prac_code']];?>" style="width:80px; margin-left:20px;">&nbsp;&nbsp;Prac Code &nbsp;
			  <input type="text" name="dx_code_1" id="dx_code_1" value="<?php echo $all_dx_codes; ?>" onChange="get_dxcode(this);" style="width:80px; margin-left:20px;">&nbsp;&nbsp;DX Code
			  <!-- rel="tooltip" data-original-title="To add if not in inventory" data-placement="right"  -->
			 
			</td>
		  </tr>
		  <tr>
			<td width="80%">
			<table cellpadding="0" cellspacing="0" class="table_collapse">
			  <tr>
				<td width="70%" valign="top">
				<table width="80%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td align="left" >
					<div class="module_border" style="width:100%;">
                    
                    
					<?php				
                    $pofdetailqry = imw_query("select * from in_frame_pof where order_detail_id = '".$sel_order['id']."'");
                    $pofROW = imw_fetch_assoc($pofdetailqry);					
                    ?>   
					<table class="table_collapse" cellpadding="0" cellspacing="0" >
					  <tr>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" >
						  <tr>
							<td width="100" align="left" class="module_label">Manufacturer</td>
							<td align="left"> 
								<input type="text" name="pof_manufacturer_id_1" id="pof_manufacturer_id_1" style="width:142px; display:none;" value="<?php echo $pofROW['manufacturer']; ?>" />
								<?php echo $objDropDown->drop_down('manufacturer_id',$sel_order['manufacturer_id']);?></td>
							<td width="50">&nbsp;</td>
							<td class="module_label">A</td>
							<td><input type="text" name="a_1" id="a_1" style="width:80px" value="<?php echo $sel_order['a']; ?>"></td>
							</tr>
						  <tr>
							<td align="left" class="module_label">Brand</td>
							<td align="left">
								<input type="text" name="pof_brand_id_1" id="pof_brand_id_1" style="width:142px; display:none;" value="<?php echo $pofROW['brand']; ?>" />                            
								<?php echo $objDropDown->drop_down('brand_id',$sel_order['brand_id']);?>

						   </td>
							<td>&nbsp;</td>
							<td class="module_label">B</td>
							<td><input type="text" name="b_1" id="b_1" style="width:80px;" value="<?php echo $sel_order['b']; ?>"></td>
							</tr>
						
						  <tr>
						    <td align="left" class="module_label">Color</td>
						    <td align="left">
								<input type="text" name="pof_color_id_1" id="pof_color_id_1" style="width:142px; display:none;" value="<?php echo $pofROW['color']; ?>" />                            
								<?php echo $objDropDown->drop_down('color_id',$sel_order['color_id']);?></td>
						    <td>&nbsp;</td>
						    <td class="module_label">Temple</td>
						    <td><input type="text" name="temple_1" id="temple_1" style="width:80px;" value="<?php echo $sel_order['temple']; ?>"></td>
						    </tr>
							 <tr>
						    <td align="left" class="module_label">Shape</td>
						    <td align="left">
								<input type="text" name="pof_shape_id_1" id="pof_shape_id_1" style="width:142px; display:none;" value="<?php echo $pofROW['shape']; ?>" />
								<?php echo $objDropDown->drop_down('shape_id',$sel_order['shape_id']);?></td>
						    <td>&nbsp;</td>
						    <td class="module_label">DBL</td>
						    <td><input type="text" name="dbl_1" id="dbl_1" style="width:80px;" value="<?php echo $sel_order['dbl']; ?>"></td>
						    </tr>
							<tr>
							<td align="left" class="module_label">Style</td>
							<td align="left">
							<input type="text" name="pof_style_id_1" id="pof_style_id_1" style="width:142px; display:none;" value="<?php echo $pofROW['style']; ?>" />
							<select style="width:150px;" onChange="autofillupc()" name="style_id_1" id="style_id_1">
								<option value="">Select</option>
							<?php $sel_style = imw_query("select * from in_frame_styles where del_status='0' order by style_name asc");
								while($row_style = imw_fetch_array($sel_style))
								{
							 ?>
							 <option value="<?php echo $row_style['id']; ?>"><?php echo $row_style['style_name']; ?></option>
							 <?php } ?>
							</select>
                            
                                <!--<input type="text" name="other_style_1" id="other_style_1" style="width:142px; float:left; display:<?php if($sel_order['style_other']!=""){ ?>block<?php } else { ?>none<?php } ?>;" value="<?php echo $sel_order['style_other']; ?>" />
                                
                                <img id="otherback" style="float:left; display:<?php if($sel_order['style_other']!=""){ ?>block<?php } else { ?>none<?php } ?>; cursor:pointer; position:relative; top:7px; left:5px;" src="../../images/icon_back.png" />-->
                            
                            </td>
							<td>&nbsp;</td>
							<td class="module_label">ED</td>
							<td><input type="text" name="ed_1" id="ed_1" style="width:80px" value="<?php echo $sel_order['ed']; ?>"></td>
							</tr>
						  <tr>
						    <td colspan="2" align="left" class="module_label">Use on hand
						      <input type="checkbox" name="use_on_hand_chk_1" value="1" id="use_on_hand_chk_1" <?php if($sel_order['use_on_hand_chk']==1){echo"checked";} ?>>
						      Order
						      <input type="checkbox" name="order_chk_1" value="1" id="order_chk_1" <?php if($sel_order['order_chk']==1){echo"checked";} ?>></td>
						    <td>&nbsp;</td>
						   <td class="module_label">Bridge</td>
							<td><input type="text" name="bridge_1" id="bridge_1" style="width:80px" value="<?php echo $sel_order['bridge']; ?>"></td>
						    </tr>
                          <tr>
							<td align="left" class="module_label">&nbsp;</td>
							<td align="left">&nbsp;</td>
							<td>&nbsp;</td>
						    <td class="module_label">FPD</td>
						    <td><input type="text" name="fpd_1" id="fpd_1" style="width:80px;" value="<?php echo $sel_order['fpd']; ?>"></td>
						
                            </tr>
						  
						  </table>
					  
						  </td>
						</tr>
					  </table>
						  </div>
	
					  </td>
					</tr>
				</table></td>
				<td width="30%" rowspan="2" valign="top"><table width="100%" border="0" height="100%" cellpadding="0" cellspacing="0" >
                	
				  <tr>
					<td>
					<div class="module_border img_border" style="width:230px">
					<?php 
						if($sel_pic!=""){
						  $sql = "SELECT * from in_patient_pictures WHERE id ='$sel_pic'";
						  $res = imw_query($sql);
						  if(imw_num_rows($res) > 0){
							$row = imw_fetch_assoc($res);
							echo resizeImage($row['image'],230);
							$pt_wear_pic=$row['image'];
						  }
						}else if($pt_wear_pic!=""){
							echo resizeImage($pt_wear_pic,230);
						}else{
								echo '<img src="../../images/no_product_image.jpg" width="230px;" height="150px">';
						}
					?>
					 <input type="hidden" name="pt_wear_pic_1" id="pt_wear_pic_1" value="<?php echo $pt_wear_pic;?>">
				   </div></td>
					</tr>
                    
				  <tr>
					<td>
						<div style="width:230px;" id="frm_pic">
							<?php $filename = "../../images/frame_stock/".$stock_image;
							if($stock_image!="" && file_exists($filename)){?>
								<img src="../../images/frame_stock/<?php echo $stock_image; ?>" width="230px;" height="150px">
							<?php }else{?>
								<img src="../../images/no_product_image.jpg" width="230px;" height="150px" class="module_border img_border" style="padding:5px">
							<?php } ?>
						</div>
					</td>
					</tr>
				  </table></td>
			  </tr>
			  <tr>
				<td valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="25%" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
					  <tr>
						<td width="17%" class="module_label">Price</td>
						<td width="83%"><input name="price_1" id="price_1"  type="text" class="s_tbx" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun();" value="<?php echo $sel_order['price']; ?>" >
                        <input name="price_hidden_1" id="price_hidden_1"  type="hidden" value=""></td>
					  </tr>
					  <tr>
						<td class="module_label">Discount</td>
						<td><input name="discount_1" type="text" class="s_tbx" id="discount_1" onChange="chk_dis_fun();" value="<?php echo $sel_order['discount']; ?>">
                        <input name="discount_hidden_1" id="discount_hidden_1"  type="hidden" value="">
                        </td>
					  </tr>
					  <tr>
						<td class="module_label">Total</td>
						<td><input name="total_amount_1" type="text" class="s_tbx" id="total_amount_1" readonly value="">
                        <input name="total_amount_hidden_1" id="total_amount_hidden_1"  type="hidden" value="">
                        </td>
					  </tr>
					  
					</table></td>
					
					<td valign="top">
					  <table width="100%" border="0" cellpadding="0" cellspacing="0">
					  <tr>
						<td class="module_label"  valign="top">
							Qty
						</td>
						<td>
						<input type="text" name="qty_1" id="qty_1" style="width:140px;" onChange="chk_dis_fun();" value="<?php echo $sel_order['qty']; ?>">
                        <input type="hidden" name="qty_hidden_1" id="qty_hidden_1"  value="">
					<!--	</td> width="16%"
						<td width="84%">-->
                          <?php 
							if($sel_item['qty_on_hand']<$sel_item['threshold'])
							{
								$qty_color = "#FF0000";
							}
							else if($sel_item['qty_on_hand']==$sel_item['threshold'])
							{
								$qty_color = "#FF0000";
							}
							else
							{
								$qty_color = "#009900";
							}
							?>
							&nbsp;Quantity on hand: &nbsp;<span id="qoh" style="font-weight:bold; color:<?php echo $qty_color;?>"><?php if(isset($sel_item['qty_on_hand'])) { echo $sel_item['qty_on_hand'];} else { echo "0"; } ?></span>
						</td>
					  </tr>
					  <tr>
						<td class="module_label" width="16%">Dis. Code</td>
						<td>
							<select name="discount_code_1" id="discount_code" class="text_10" style="width:148px;">
								<option value="">Please Select</option>
								<?php
								$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
								while($sel_write=imw_fetch_array($sel_rec)){
								?>
								<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_order['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
								</option>
								<?php } ?>
							</select>
						</td>
					  </tr>
					  <tr>
						<td class="module_label" width="16%">Comments</td>
						<td><input type="text" name="item_comment_1" id="item_comment_1" style="width:140px;" value="<?php echo $sel_order['item_comment']; ?>"></td>
					  </tr>
					  </table>
					</td>  
				  </tr>
				</table></td>
			  </tr>
			  </table></td>
		  </tr>
		  <tr>
			<td valign="top">
			<div id="div_rel_itm" style="display:none; min-width:520px; border:1px solid #CCC">
			<div class="listheading">Alternatives</div>
		   <!-- <div class="left_arrow"></div>-->
			<div id="all_images"> </div>
		   <!--<div class="right_arrow"></div>-->
			</div>
			</td>
		  </tr>
		
		</table> 
		</div>
        <input type="hidden" name="last_cont" id="last_cont" value="1" />
		<div class="btn_cls">
		    <input type="button" name="previous" value="Previous" onClick="frm_sub_fun('previous');"/>
			<input type="button" name="new_form" value="New" onClick="frm_sub_fun('new_form');"/>
			<input type="button" name="Cancel" value="Cancel" onClick="frm_sub_fun('cancel'); "/>
			<input type="button" name="new2" value="On Hold"/>
            <input type="button" name="save" value="Save" onClick="frm_sub_fun('save'); "/>
			<input type="button" name="next_btn" id="next_btn" value="Next" onClick="frm_sub_fun('next');"/>
		</div> 
	</form>
<?php
if($sel_order['upc_code']!="")
{
	?>
	<script type="text/javascript">
		get_related_frames('<?php echo $sel_order['upc_code'];?>');
	</script>
	<?php
}
?>	

<script type="text/javascript">
$(document).ready(function(){
	var manu_id = '<?php echo $sel_order['manufacturer_id']; ?>';
	var brand_id = '<?php echo $sel_order['brand_id']; ?>';
	var style_id = '<?php echo $sel_order['style_id']; ?>';
	get_manufacture_brand(manu_id,brand_id);
	get_brand_style(brand_id,style_id);	
	
	/*$("#otherback").click(function()
	{
		$("#other_style_1").val('');		
		$("#other_style_1 , #otherback").hide();
		
		$("#style_id_1").show();
		$("#style_id_1 option[value='0']").prop('selected',true);
	});
	
	$("#style_id_1").change(function()
	{
		if($(this).val()=="other")
		{
			
			//$(this).val('');
			$(this).hide();
			$("#other_style_1 , #otherback").show();
		}
	});	*/		
	
	
});
	
	var obj7 = new actb(document.getElementById('upc_name_1'),custom_array_upc,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	
	var obj6 = new actb(document.getElementById('item_prac_code_1'),customarrayProcedure);
	
	var obj8 = new actb(document.getElementById('item_name_1'),custom_array_name,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	
	var obj9 = new actb(document.getElementById('dx_code_1'),customarrayDX);
	
	chk_dis_fun();
</script>
</body>
</html>