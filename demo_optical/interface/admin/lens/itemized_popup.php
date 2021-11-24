<?php 
/*
File: itemized_popup.php
Coded in PHP7
Purpose: Add/Edit: Lens Items Price
Access Type: Direct acess
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once("../../../library/classes/functions.php");

$item_id = $_REQUEST['item_id'];
$module_type_id = $_REQUEST['module_type_id'];
//--------- SAVE LENS ITEMS DATA ------//
if(isset($_POST['save']) && $_POST['save']=="Save")
{
	$opr_id = $_SESSION['authId'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	
	if($_POST['pr_item_id']<=0 || $_POST['pr_item_id']==""){
		$ins_item=imw_query("insert into in_item set entered_date='$entered_date', entered_time='$entered_time', entered_by='$opr_id'");
		$ins_item_id=imw_insert_id();
		$_POST['pr_item_id']=$ins_item_id;
	}
	$pr_item_id = trim($_POST['pr_item_id']);
	$pr_module_type_id = trim($_POST['pr_module_type_id']);
	$lens_wholesale = trim($_POST['lens_wholesale']);
	$lens_retail = trim($_POST['lens_retail']);
	$progressive_wholesale = trim($_POST['progressive_wholesale']);
	$progressive_retail = trim($_POST['progressive_retail']);
	$design_wholesale = trim($_POST['design_wholesale']);
	$design_retail = trim($_POST['design_retail']);
	$material_wholesale = trim($_POST['material_wholesale']);
	$material_retail = trim($_POST['material_retail']);
	$a_r_wholesale = trim($_POST['a_r_wholesale']);
	$a_r_retail = trim($_POST['a_r_retail']);
	$transition_wholesale = trim($_POST['transition_wholesale']);
	$transition_retail = trim($_POST['transition_retail']);
	$polarization_wholesale = trim($_POST['polarization_wholesale']);
	$polarization_retail = trim($_POST['polarization_retail']);
	$edge_wholesale = trim($_POST['edge_wholesale']);
	$edge_retail = trim($_POST['edge_retail']);
	$tint_wholesale = trim($_POST['tint_wholesale']);
	$tint_retail = trim($_POST['tint_retail']);
	$color_wholesale = trim($_POST['color_wholesale']);
	$color_retail = trim($_POST['color_retail']);
	$uv400_wholesale = trim($_POST['uv400_wholesale']);
	$uv400_retail = trim($_POST['uv400_retail']);
	$pgx_wholesale = trim($_POST['pgx_wholesale']);
	$pgx_retail = trim($_POST['pgx_retail']);
	$other_wholesale = trim($_POST['other_wholesale']);
	$other_retail = trim($_POST['other_retail']);
	$comment = trim($_POST['comment']);	
	$total_wholesale = trim($_POST['total_wholesale']);
	$total_retail = trim($_POST['total_retail']);
	$type_prac_code = back_prac_id(trim($_POST['type_prac_code']));
	$material_prac_code = back_prac_id(trim($_POST['material_prac_code']), true);
	$ar_prac_code = back_prac_id(trim($_POST['ar_prac_code']), true);
	$transition_prac_code = back_prac_id(trim($_POST['transition_prac_code']));
	$polarized_prac_code = back_prac_id(trim($_POST['polarized_prac_code']));
	$tint_prac_code = back_prac_id(trim($_POST['tint_prac_code']));
	$uv_prac_code = back_prac_id(trim($_POST['uv_prac_code']));
	$pgx_prac_code = back_prac_id(trim($_POST['pgx_prac_code']));
	$progressive_prac_code = back_prac_id(trim($_POST['progressive_prac_code']));
	$design_prac_code = back_prac_id(trim($_POST['design_prac_code']));
	$edge_prac_code = back_prac_id(trim($_POST['edge_prac_code']));
	$color_prac_code = back_prac_id(trim($_POST['color_prac_code']));
	$other_prac_code = back_prac_id(trim($_POST['other_prac_code']));
	
	$check_sql = "select id from in_item_price_details where item_id='".$pr_item_id."'";
	$check_res = imw_query($check_sql);
	$check_num_rows = imw_num_rows($check_res);
	if($check_num_rows > 0)
	{
		$get_row = imw_fetch_assoc($check_res);
		$act = "update";
		$whr = ", modified_date='$entered_date', modified_time='$entered_time', modified_by='$opr_id' where id='".$get_row['id']."'";
	}
	else
	{
		$act = "insert into";
		$whr = ", entered_date='$entered_date', entered_time='$entered_time', entered_by='$opr_id'";
	}
	
	 $query = "$act in_item_price_details set item_id='".imw_real_escape_string($pr_item_id)."', module_type_id='".imw_real_escape_string($pr_module_type_id)."', lens_wholesale='".imw_real_escape_string($lens_wholesale)."', lens_retail='".imw_real_escape_string($lens_retail)."', material_wholesale='".imw_real_escape_string($material_wholesale)."', material_retail='".imw_real_escape_string($material_retail)."', a_r_wholesale='".imw_real_escape_string($a_r_wholesale)."', a_r_retail='".imw_real_escape_string($a_r_retail)."', transition_wholesale='".imw_real_escape_string($transition_wholesale)."', transition_retail='".imw_real_escape_string($transition_retail)."', polarization_wholesale='".imw_real_escape_string($polarization_wholesale)."', polarization_retail='".imw_real_escape_string($polarization_retail)."', tint_wholesale='".imw_real_escape_string($tint_wholesale)."', tint_retail='".imw_real_escape_string($tint_retail)."', uv400_wholesale='".imw_real_escape_string($uv400_wholesale)."', uv400_retail='".imw_real_escape_string($uv400_retail)."', pgx_wholesale='".imw_real_escape_string($pgx_wholesale)."', pgx_retail='".imw_real_escape_string($pgx_retail)."', other_wholesale='".imw_real_escape_string($other_wholesale)."', other_retail='".imw_real_escape_string($other_retail)."', progressive_wholesale='".imw_real_escape_string($progressive_wholesale)."', progressive_retail='".imw_real_escape_string($progressive_retail)."', design_wholesale='".imw_real_escape_string($design_wholesale)."', design_retail='".imw_real_escape_string($design_retail)."', edge_wholesale='".imw_real_escape_string($edge_wholesale)."', edge_retail='".imw_real_escape_string($edge_retail)."', color_wholesale='".imw_real_escape_string($color_wholesale)."', color_retail='".imw_real_escape_string($color_retail)."', type_prac_code='".imw_real_escape_string($type_prac_code)."', material_prac_code='".imw_real_escape_string($material_prac_code)."', ar_prac_code='".imw_real_escape_string($ar_prac_code)."', transition_prac_code='".imw_real_escape_string($transition_prac_code)."', polarized_prac_code='".imw_real_escape_string($polarized_prac_code)."', tint_prac_code='".imw_real_escape_string($tint_prac_code)."', uv_prac_code='".imw_real_escape_string($uv_prac_code)."', progressive_prac_code='".imw_real_escape_string($progressive_prac_code)."', design_prac_code='".imw_real_escape_string($design_prac_code)."', edge_prac_code='".imw_real_escape_string($edge_prac_code)."', color_prac_code='".imw_real_escape_string($color_prac_code)."', other_prac_code='".imw_real_escape_string($other_prac_code)."', pgx_prac_code='".imw_real_escape_string($pgx_prac_code)."', comment='".imw_real_escape_string($comment)."' $whr ";
		
	$execute = imw_query($query);
	
	imw_query("update in_item set wholesale_cost='$total_wholesale',retail_price='$total_retail' where id='$pr_item_id'");
?>
 <script type='text/javascript'>
 window.opener = window.opener.main_iframe.admin_iframe;
	if(window.opener.document.getElementById('wholesale_cost')){
		var total_wholesale = '<?php echo $total_wholesale; ?>';
		var total_retail = '<?php echo $total_retail; ?>';
		window.opener.document.getElementById('wholesale_cost').value=total_wholesale;
		window.opener.document.getElementById('retail_price').value=total_retail;
	}
	
	if(window.opener.document.getElementById('qty_on_hand')){
		var retail_price =0;
		var qty_on_hand =0;
		var ins_item_id = '<?php echo $ins_item_id; ?>';
		var pr_item_id = '<?php echo $pr_item_id; ?>';
		if(window.opener.document.getElementById('retail_price').value>0){
			retail_price = window.opener.document.getElementById('retail_price').value;
		}
		if(window.opener.document.getElementById('qty_on_hand').value>0){
			qty_on_hand = window.opener.document.getElementById('qty_on_hand').value;
		}
		var total_price = parseFloat(retail_price)*parseInt(qty_on_hand);
		window.opener.document.getElementById('amount').value=total_price.toFixed(2);
		if(ins_item_id>0){
			window.opener.document.getElementById('edit_item_id').value=ins_item_id;
		}
	}
	window.close();
</script>
<?php	
}

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
				$proc_code_desc_arr[$rowCodes["cpt_prac_code"]]=$rowCodes["cpt_desc"];
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
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<style type="text/css">
#coating_row, #coating_row *, #material_row, #material_row *{background-color:#EBEBE4;cursor:pointer;}
#coating_div, #material_div{cursor:default;}
#hide_coating, #hide_material{
	position: absolute;
	top: 0;
	right: 8px;
	border-radius: 10px;
	background-color: #ddd;
	width: 21px;
	height: 21px;
	text-align: center;
	cursor:pointer;
}
#hide_coating img, #hide_material img{
	height: 15px;
    width: 15px;
    padding-top: 3px;
}
</style>
<script type="text/javascript">
CURRENCY_SYMBOL = '<?php currency_symbol(); ?>';
</script>
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script>
	function get_wholesale_total()
	{
		var wholesale_total = 0;
		$('.wholesale_num').not(".a_r_wholesale").each(function(index,value){
			wholesale_total += isNaN(parseFloat($(this).val(),10)) ? 0 : parseFloat($(this).val(),10);
		});
		
		/*Coating A/R values*/
		$(".a_r_wholesale_disp").each(function(index, obj){
			wholesale_total += isNaN(parseFloat($(this).val(),10)) ? 0 : parseFloat($(this).val(),10);
		});
		/*End Coating A/R values*/
		
		/*Material values*/
		$(".material_wholesale_disp").each(function(index, obj){
			wholesale_total += isNaN(parseFloat($(this).val(),10)) ? 0 : parseFloat($(this).val(),10);
		});
		/*Material values*/
		
		$('#total_wholesale').val(wholesale_total.toFixed(2));
	}
	
	function get_retail_total()
	{
		var retail_total = 0;
		$('.retail_num').not(".a_r_retail").each(function(index,value){
			retail_total += isNaN(parseFloat($(this).val(),10)) ? 0 : parseFloat($(this).val(),10);
		});
		
		/*Coating A/R values*/
		$(".a_r_retail_disp").each(function(index, obj){
			retail_total += isNaN(parseFloat($(this).val(),10)) ? 0 : parseFloat($(this).val(),10);
		});
		/*End Coating A/R values*/
		
		/*Material values*/
		$(".material_retail_disp").each(function(index, obj){
			retail_total += isNaN(parseFloat($(this).val(),10)) ? 0 : parseFloat($(this).val(),10);
		});
		/*End Material values*/
		$('#total_retail').val(retail_total.toFixed(2));
	}
	
	function display_total()
	{
		get_wholesale_total();
		get_retail_total();	
	}
	
<?php if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
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
$(document).ready(function(){
	
	/*Show Coating Price Div onClick*/
	$("#coating_row").click(function(){
		var top = $(this).position().top;
		var left = $(this).position().left;
		$("#coating_divC").css({'top':(top-128)+'px', 'left':(left+56)+'px'});
		$("#coating_div").show();
	});
	$("#hide_coating").click(function(){
		$("#coating_div").hide();
	});
	
	/*Show Material Price Div onClick*/
	$("#material_row").click(function(){
		var top = $(this).position().top;
		var left = $(this).position().left;
		$("#material_divC").css({'top':(top-100)+'px', 'left':(left+56)+'px'});
		$("#material_div").show();
	});
	$("#hide_material").click(function(){
		$("#material_div").hide();
	});
});
</script>
</head>
<body onLoad="display_total();" onkeydown="return /*stopKey()*/">
	<div style="width:400px;margin:5px auto;">
<?php 
	 $sql = "select * from in_item_price_details where item_id='".$item_id."'";
	 $res = imw_query($sql);
	 $row = imw_fetch_array($res);
?>

<!-- Treatment PopUp -->
<div id="coating_div" style="position:absolute;width:100%;height:100%;top:0;left:0;display:none;background-color:rgba(255,255,255,0.5)">
	<div id="coating_divC" style="position:relative;background:white;border:2px solid #1289CC;border-radius:4px;width:286px;">
	<h6 style="margin:0;text-align:center;background-color:#1289CC;padding:2px 0;color:#fff;font-size:18px;line-height:20px;">Treatment</h6>
	<span id="hide_coating"><img src="<?php echo $GLOBALS['WEB_PATH']?>/images/del.png" /></span>
		<table>
			<tr>
				<th>Prac Code</th>
				<td>Wholesale</td>
				<td>Retail</td>
			</tr>
	<?php
	$ar_wholesale_total = array();
	$ar_retail_total = array();
	$ar_prac_codes_disp = explode(";",trim($_REQUEST['ar']));
	$ar_retail_disp = explode(";",trim($row['a_r_retail']));
	$ar_wholesale_disp = explode(";",trim($row['a_r_wholesale']));
	$ar_retail_disp = explode(";", $row['a_r_retail']);
	foreach($ar_prac_codes_disp as $ar_key=>$ar_value){
		$ar_wholesale_di = (isset($ar_wholesale_disp[$ar_key]) && $ar_wholesale_disp[$ar_key]!="")?$ar_wholesale_disp[$ar_key]:"0.00";
		$ar_retail_di = (isset($ar_retail_disp[$ar_key]) && $ar_retail_disp[$ar_key]!="")?$ar_retail_disp[$ar_key]:"0.00";
		array_push($ar_wholesale_total, $ar_wholesale_di);
		array_push($ar_retail_total, $ar_retail_di);
	?>
			<tr>
				<td><input type="text" class="a_r_prac_disp" id="a_r_prac_code_disp_<?php echo $ar_key; ?>" value="<?php echo $ar_value; ?>" style="width:80px;" disabled="disabled"/></td>
				<td><input type="text" class="a_r_wholesale_disp" id="a_r_wholesale_disp_<?php echo $ar_key; ?>" value="<?php echo $ar_wholesale_di; ?>" style="width:72px;" onChange="parse_float(this); update_coating_price();" /></td>
				<td><input type="text" class="a_r_retail_disp" id="a_r_retail_disp_<?php echo $ar_key; ?>" value="<?php echo $ar_retail_di; ?>" style="width:72px;" onChange="parse_float(this); update_coating_price();" /></td>
			</tr>
	<?php	
	}
	?>
		</table>
	</div>
</div>

<!-- Material PopUp -->
<div id="material_div" style="position:absolute;width:100%;height:100%;top:0;left:0;display:none;background-color:rgba(255,255,255,0.5)">
	<div id="material_divC" style="position:relative;background:white;border:2px solid #1289CC;border-radius:4px;width:286px;">
	<h6 style="margin:0;text-align:center;background-color:#1289CC;padding:2px 0;color:#fff;font-size:18px;line-height:20px;">Material</h6>
	<span id="hide_material"><img src="<?php echo $GLOBALS['WEB_PATH']?>/images/del.png" /></span>
		<table>
			<tr>
				<th>Prac Code</th>
				<td>Wholesale</td>
				<td>Retail</td>
			</tr>
<?php
	$material_wholesale_total = array();
	$material_retail_total = array();
	$material_prac_codes_disp = explode(";",trim($_REQUEST['mat']));
	$material_retail_disp = explode(";",trim($row['material_retail']));
	$material_wholesale_disp = explode(";",trim($row['material_wholesale']));
	$material_retail_disp = explode(";", $row['material_retail']);
	foreach($material_prac_codes_disp as $material_key=>$material_value){
		$material_wholesale_di = (isset($material_wholesale_disp[$material_key]) && $material_wholesale_disp[$material_key]!="")?$material_wholesale_disp[$material_key]:"0.00";
		$material_retail_di = (isset($material_retail_disp[$material_key]) && $material_retail_disp[$material_key]!="")?$material_retail_disp[$material_key]:"0.00";
		array_push($material_wholesale_total, $material_wholesale_di);
		array_push($material_retail_total, $material_retail_di);
?>
			<tr>
				<td><input type="text" class="material_prac_disp" id="material_prac_code_disp_<?php echo $material_key; ?>" value="<?php echo $material_value; ?>" style="width:80px;" disabled="disabled"/></td>
				<td><input type="text" class="material_wholesale_disp" id="material_wholesale_disp_<?php echo $material_key; ?>" value="<?php echo $material_wholesale_di; ?>" style="width:72px;" onChange="parse_float(this); update_material_price();" /></td>
				<td><input type="text" class="material_retail_disp" id="material_retail_disp_<?php echo $material_key; ?>" value="<?php echo $material_retail_di; ?>" style="width:72px;" onChange="parse_float(this); update_material_price();" /></td>
			</tr>
<?php	
	}
?>
		</table>
	</div>
</div>

        <div class="module_border">
            <form action="" name="itemized_form" method="post" enctype="multipart/form-data">
            	<input type="hidden" name="pr_item_id" value="<?php echo $item_id; ?>" >
                <input type="hidden" name="pr_module_type_id" value="<?php echo $module_type_id; ?>" >
               <table class="table_collapse">
                	<tr>
                        <td  class="listheading" colspan="4" align="left">
                        	Lenses Itemized</td>
                    </tr>
                    <tbody class="table_cell_padd5">
                    <tr>
						<td width="80" align="left" class="module_heading" style="text-align:left !important;">
                        	Prac Code</td>
                        <td align="left" class="module_heading" style="text-align:left !important;width:90px;">Wholesale</td>
                        <td style="width:91px;">&nbsp;</td>
                        <td align="left" class="module_heading" style="text-align:left !important;width:91px;">Retail</td>
                    </tr>
                    <tr>
						<td><input type="text" name="type_prac_code" id="type_prac_code_1"  value="<?php echo $_REQUEST['ftype']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['ftype']]; ?>" readonly/></td>
                        <td>
                        	<input type="text" value="<?php echo $row['lens_wholesale']; ?>" name="lens_wholesale"  style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Seg Type                       </td>
                        <td >
                        	<input type="text" value="<?php echo $row['lens_retail']; ?>" name="lens_retail" id="lens_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
<?php /*
					 <tr>
					 	<td>
							<input type="text" name="progressive_prac_code" id="type_prac_code_2"  value="<?php echo $_REQUEST['prgr']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['prgr']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['progressive_wholesale']; ?>" name="progressive_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Progressive                        </td>
                        <td>
                        	<input type="text" value="<?php echo $row['progressive_retail']; ?>" name="progressive_retail" id="progressive_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
*/ ?>
					<tr>
					 	<td>
							<input type="text" name="design_prac_code" id="type_prac_code_12"  value="<?php echo $_REQUEST['design']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['design']]; ?>" readonly/>
						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['design_wholesale']; ?>" name="design_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">
						</td>
                        <td class="module_label" align="left">
                        	Design
						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['design_retail']; ?>" name="design_retail" id="design_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">
						</td>
                    </tr>
					
                    <tr id="material_row" title="Click to Edit Material Price">
						<td>
							<input type="text" name="material_prac_code" id="type_prac_code_3"  value="<?php echo $_REQUEST['mat']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['mat']]; ?>" readonly />
						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['material_wholesale']; ?>" name="material_wholesale" id="material_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()" readonly />
						</td>
                        <td class="module_label" align="left">
                        	Material
						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['material_retail']; ?>" name="material_retail" id="material_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()" readonly />
						</td>
                    </tr>
					
                    <tr id="coating_row" title="Click to Edit Coating Price">
						<td>
							<input type="text" name="ar_prac_code" id="type_prac_code_4"  value="<?php echo trim($_REQUEST['ar']); ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['ar']]; ?>" readonly/>
						</td>
                        <td>
                        	<input type="text" value="<?php echo implode(";", $ar_wholesale_total); ?>" name="a_r_wholesale" id="a_r_wholesale" style="width:72px" class="a_r_wholesale wholesale_num currency" onChange="get_wholesale_total()" readonly>
						</td>
                        <td class="module_label coating_Link" align="left">
                        	Treatment</td>
                        <td>
                        	<input type="text" value="<?php echo implode(";", $ar_retail_total); ?>" name="a_r_retail" id="a_r_retail" style="width:72px" class="a_r_retail retail_num currency" onChange="get_retail_total()" readonly>
						</td>
                    </tr>

<?php /*
                    <tr>
						<td>
							<input type="text" name="transition_prac_code" id="type_prac_code_5"  value="<?php echo $_REQUEST['tran']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['tran']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['transition_wholesale']; ?>" name="transition_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Transition</td>
                        <td>
                        	<input type="text" value="<?php echo $row['transition_retail']; ?>" name="transition_retail" id="transition_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
                    <tr>
						<td>
							<input type="text" name="polarized_prac_code" id="type_prac_code_6"  value="<?php echo $_REQUEST['pol']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['pol']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['polarization_wholesale']; ?>" name="polarization_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Polarization</td>
                        <td>
                        	<input type="text" value="<?php echo $row['polarization_retail']; ?>" name="polarization_retail" id="polarization_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
					<tr>
						<td>
							<input type="text" name="edge_prac_code" id="type_prac_code_7"  value="<?php echo $_REQUEST['edge']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['edge']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['edge_wholesale']; ?>" name="edge_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Edge                        </td>
                        <td>
                        	<input type="text" value="<?php echo $row['edge_retail']; ?>" name="edge_retail" id="edge_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
                    <tr>
						<td>
							<input type="text" name="tint_prac_code" id="type_prac_code_8"  value="<?php echo $_REQUEST['tint']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['tint']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['tint_wholesale']; ?>" name="tint_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()"> </td>
                        <td class="module_label" align="left">
                        	Tint</td>
                        <td>
                        	<input type="text" value="<?php echo $row['tint_retail']; ?>" name="tint_retail" id="tint_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
					<tr>
						<td>
							<input type="text" name="color_prac_code" id="type_prac_code_9"  value="<?php echo $_REQUEST['color']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['color']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['color_wholesale']; ?>" name="color_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Color                        </td>
                        <td>
                        	<input type="text" value="<?php echo $row['color_retail']; ?>" name="color_retail" id="color_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
                    <tr>
						<td>
							<input type="text" name="uv_prac_code" id="type_prac_code_10"  value="<?php echo $_REQUEST['uv']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['uv']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['uv400_wholesale']; ?>" name="uv400_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Uv400</td>
                        <td>
                        	<input type="text" value="<?php echo $row['uv400_retail']; ?>" name="uv400_retail" id="uv400_retail" style="width:72px" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
					<tr>
						<td>
							<input type="text" name="pgx_prac_code" id="type_prac_code_11"  value="<?php echo $_REQUEST['pgx']; ?>" style="width:80px;" title="<?php echo $proc_code_desc_arr[$_REQUEST['pgx']]; ?>" readonly/>						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['pgx_wholesale']; ?>" name="pgx_wholesale" style="width:72px" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	PGX</td>
                        <td>
                        	<input type="text" value="<?php echo $row['pgx_retail']; ?>" name="pgx_retail" style="width:72px;" id="pgx_retail" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
*/ ?>

                    <tr>
						<td>
							<input type="text" name="other_prac_code" id="other_prac_code"  value="<?php echo $proc_code_arr[$row['other_prac_code']]; ?>" style="width:80px;" onBlur="show_price_from_praccode(this,'other_retail','itemized');" title="<?php echo $proc_code_desc_arr[$proc_code_arr[$row['other_prac_code']]]; ?>" />	
						</td>
                        <td>
                        	<input type="text" value="<?php echo $row['other_wholesale']; ?>" name="other_wholesale" style="width:72px;" class="wholesale_num currency" onChange="get_wholesale_total()">                        </td>
                        <td class="module_label" align="left">
                        	Other</td>
                        <td>
                        	<input type="text" value="<?php echo $row['other_retail']; ?>" name="other_retail" id="other_retail" style="width:72px;" class="retail_num currency" onChange="get_retail_total()">                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="left">Comment&nbsp;&nbsp;<input type="text" style="width:305px;" name="comment" value="<?php echo $row['comment']; ?>"></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                        	<div class="module_border mt5">
                            	<table class="table_collapse">
                                	<tr>
                                    	<td width="100">&nbsp;                                        </td>
                                        <td width="80" class="module_label module_heading" style="text-align:center;">
                                        	Totals                                        </td>
                                        <td width="100">&nbsp;                                        </td>
                                    </tr>
                                    <tr>
                                    	<td align="right"><input type="txet" style="width:100px" id="total_wholesale" name="total_wholesale" readonly class="currency" /></td>
                                        <td>&nbsp;                                        </td>
                                        <td><input type="txet" style="width:100px" id="total_retail" name="total_retail" readonly class="currency" /></td>
                                    </tr>
                                </table>
                            </div>                         </td>
                    </tr> 
                    </tbody>       
               </table>
               
               <div class="btn_cls">
               		<input type="submit" name="save" value="Save" />                        
                    <input type="button" name="new" value="Cancel" onClick="window.close();" />
               </div> 
            </form>
        </div>
	</div>
<script>
	var items_id = '<?php echo $item_id; ?>';
	var ftype = '<?php echo $_REQUEST['ftype']; ?>';
	var prgr = '<?php echo $_REQUEST['prgr']; ?>';
	var mat = '<?php echo $_REQUEST['mat']; ?>';
	var ar = '<?php echo $_REQUEST['ar']; ?>';
	var tran = '<?php echo $_REQUEST['tran']; ?>';
	var pol = '<?php echo $_REQUEST['pol']; ?>';
	var edge = '<?php echo $_REQUEST['edge']; ?>';
	var tint = '<?php echo $_REQUEST['tint']; ?>';
	var color = '<?php echo $_REQUEST['color']; ?>';
	var pgx = '<?php echo $_REQUEST['pgx']; ?>';
	var uv = '<?php echo $_REQUEST['uv']; ?>';
	
	var row_ftype = '<?php echo $proc_code_arr[$row["type_prac_code"]]; ?>';
	var row_prgr = '<?php echo $proc_code_arr[$row["progressive_prac_code"]]; ?>';
	var row_mat = '<?php echo $proc_code_arr[$row["material_prac_code"]]; ?>';
	var row_ar = '<?php echo $proc_code_arr[$row["ar_prac_code"]]; ?>';
	var row_tran = '<?php echo $proc_code_arr[$row["transition_prac_code"]]; ?>';
	var row_pol = '<?php echo $proc_code_arr[$row["polarized_prac_code"]]; ?>';
	var row_edge = '<?php echo $proc_code_arr[$row["edge_prac_code"]]; ?>';
	var row_tint = '<?php echo $proc_code_arr[$row["tint_prac_code"]]; ?>';
	var row_color = '<?php echo $proc_code_arr[$row["color_prac_code"]]; ?>';
	var row_pgx = '<?php echo $proc_code_arr[$row["pgx_prac_code"]]; ?>';
	var row_uv = '<?php echo $proc_code_arr[$row["uv_prac_code"]]; ?>';
	
	if(ftype!=row_ftype)
	{
		//show_price_from_praccode(ftype,'lens_retail','itemized');
	}
	
	if(prgr!=row_prgr)
	{
		//show_price_from_praccode(prgr,'progressive_retail','itemized');
	}
	
	if(mat!=row_mat)
	{
		//show_price_from_praccode(mat,'material_retail','itemized');
	}
	
	if(ar!=row_ar)
	{
		//show_price_from_praccode(ar,'a_r_retail','itemized');
	}
	
	if(tran!=row_tran)
	{
		//show_price_from_praccode(tran,'transition_retail','itemized');
	}
	
	if(pol!=row_pol)
	{
		//show_price_from_praccode(pol,'polarization_retail','itemized');
	}
	
	if(edge!=row_edge)
	{
		show_price_from_praccode(edge,'edge_retail','itemized');
	}
	
	if(tint!=row_tint)
	{
		//show_price_from_praccode(tint,'tint_retail','itemized');
	}
	
	if(color!=row_color)
	{
		//show_price_from_praccode(color,'color_retail','itemized');
	}
	
	if(pgx!=row_pgx)
	{
		//show_price_from_praccode(pgx,'pgx_retail','itemized');
	}
	
	if(uv!=row_uv)
	{
		//show_price_from_praccode(uv,'uv400_retail','itemized');
	}
	
	var obj8 = new actb(document.getElementById('other_prac_code'),customarrayProcedure);

/*Update Coating Price on Change in PopUp*/
function update_coating_price(){
	
	var wholesale = new Array;
	var retail = new Array;
	
	$(".a_r_wholesale_disp").each(function(index, obj){
		value = $(obj).val();
		wholesale.push(value);
	});
	
	$(".a_r_retail_disp").each(function(index, obj){
		value = $(obj).val();
		retail.push(value);
	});
	
	wholesale = wholesale.join(";");
	retail = retail.join(";");
	
	$("#a_r_wholesale").val(wholesale);
	$("#a_r_retail").val(retail);
	get_wholesale_total();
	get_retail_total();
}

/*Update Material Price on Change in PopUp*/
function update_material_price(){
	
	var wholesale = new Array;
	var retail = new Array;
	
	$(".material_wholesale_disp").each(function(index, obj){
		value = $(obj).val();
		wholesale.push(value);
	});
	
	$(".material_retail_disp").each(function(index, obj){
		value = $(obj).val();
		retail.push(value);
	});
	
	wholesale = wholesale.join(";");
	retail = retail.join(";");
	
	$("#material_wholesale").val(wholesale);
	$("#material_retail").val(retail);
	get_wholesale_total();
	get_retail_total();
}
</script>
</body>
</html>