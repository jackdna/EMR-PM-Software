<?php 
/*
File: index.php
Coded in PHP7
Purpose: Add/Edit/Delete: Lens
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php"); 

//get default prac code for uv400
$default_prac_code = array();
$q1=imw_query("SELECT `sub_module`, `prac_code` FROM `in_prac_codes` WHERE `module_id`='2' AND `del_status`=0");
while($d1=imw_fetch_object($q1)){
	$default_prac_code[$d1->sub_module] = $d1->prac_code;
}

/*$getname = imw_query("select id, upc_code from in_item where upc_code!='' and del_status='0'");
$getnameArr = array();
while($getnameRow=imw_fetch_array($getname))
{
	$getnameArr[] = "'".$getnameRow['id']."~~~".$getnameRow['upc_code']."'";
}
$proNameArr = implode(',',$getnameArr);*/

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
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
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
<?php 
echo "var uv400='';";
?>
$(document).ready(function(){

	/*$("#uv_check").click(function() {
		if($(this).is(':checked'))$("#uv_prac_code").val(uv400);
		else $("#uv_prac_code").val('');
	});*/
	
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
<script type="text/javascript">
 var type_codes = {in_lens_type:'type', in_lens_design:'design', in_lens_material:'material'};
 var default_prac_code = <?php print json_encode($default_prac_code); ?>;
 
$(function() {
	var cyear = new Date().getFullYear();		
	$( "#datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
});

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

function upc(upc_code,current_txt,upc_txt)
{
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = 'action=managestock&upc='+ucode;
	
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
					prac_code_by_item(item.id,'type_prac_code');
					prac_code_by_item_multi(item.id,'material_prac_code');
					prac_code_by_item_multi(item.id,'ar_prac_code');
					prac_code_by_item(item.id,'transition_prac_code');
					prac_code_by_item(item.id,'polarized_prac_code');
					prac_code_by_item(item.id,'tint_prac_code');
					prac_code_by_item(item.id,'uv_prac_code');
					prac_code_by_item(item.id,'pgx_prac_code');
					prac_code_by_item(item.id,'progressive_prac_code');
					prac_code_by_item(item.id,'design_prac_code');
					prac_code_by_item(item.id,'edge_prac_code');
					prac_code_by_item(item.id,'color_prac_code');
					
					$("#item_image img").attr("src","../../../images/lense_stock/"+item.stock_image);
					$("#manufacturer").val(item.manufacturer_id);
					$("#module_type").val(item.module_type_id);
					$("#upc_name").val(item.upc_code);
					$("#lens_type").val(item.type_id);
					fetch_vision_dd(item.type_id, 'design', item.design_id);
					
					show_progressive_dropdown($("#lens_type"));
					$("#lens_progresive").val(item.progressive_id);
					$("#lens_material").val(item.material_id);
					$("#lens_air").changeSelected(item.a_r_id);
					$("#lens_transition").val(item.transition_id);
					$("#polarized_name").val(item.polarized_id);
					$("#edge_name").val(item.edge_id);					
					$("#tint_type").val(item.tint_id);
					$("#color_name").val(item.color);
					$("#sphere_positive_min").val(item.sphere_positive);
					$("#sphere_negative_min").val(item.sphere_negative);
					$("#cylindep_positive_min").val(item.cylindep_positive);
					$("#cylindep_negative_min").val(item.cylindep_negative);
					$("#sphere_positive_max").val(item.sphere_positive_max);
					$("#sphere_negative_max").val(item.sphere_negative_max);
					$("#cylindep_positive_max").val(item.cylindep_positive_max);
					$("#cylindep_negative_max").val(item.cylindep_negative_max);
					$("#min_segment").val(item.minimum_segment+'~:~'+item.minimum_segment_id);
					if(!$("#min_segment").val() || $("#min_segment").val()==""){
						$("#min_segment").append("<option selected value='"+item.minimum_segment+'~:~'+item.minimum_segment_id+"'>"+item.minimum_segment+"</option>");
					}
					$("#diameter").val(item.diameter);
					$("#bc").val(item.bc);
					$("#th").val(item.th);

					$("#finish_type").val(item.finish_type);
					show_lab_dropdown(item.finish_type);
					
					$("#finish_type_other").val(item.finish_type_other);
					
					if(item.uv_check=="1")
					{
						$("#uv_check").prop('checked',true);
						prac_by_check('#uv_check','uv_prac_code');
					}					
					if(item.pgx_check=="1")
					{
						$("#pgx_check").prop('checked',true);
						prac_by_check('#pgx_check','pgx_prac_code');
					}					
					if(item.r_check=="1")
					{
						$("#r_check").prop('checked',true);
					}					
					if(item.l_check=="1")
					{
						$("#l_check").prop('checked',true);
					}					
					
					if(current_txt){$("#name").val(item.name);}
					$("#vendor").val(item.vendor_id);
					get_vendor_manufacturer(item.manufacturer_id,item.vendor_id);
					$("#brand").val(item.brand_id);					
					$("#type_desc").val(item.type_desc);
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
					$("#labs").val(item.lab_id);
					$("#amount").val(item.amount);
					$("#discount").val(item.discount);
					if(item.discount_till!="00-00-0000"){
					$("#datepicker").val(item.discount_till);
					}
				 });
			 }
			 else
			 {
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
	window.location.href = pages[type];
}
var addwin = '';
var pop_up ='';
var win ='';
function add_qty_fun(type){
	var item_id=document.getElementById('edit_item_id').value;
	top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('location_popup','location_lot_popup.php?item_add='+type+'&item_id='+item_id,'location_popup','width=820,height=500,left=600,scrollbars=no,top=150');
	addwin.focus();
}

function open_popup()
{
	var module_type_id = $('#module_type').val();
	var item_id=document.getElementById('edit_item_id').value;
	//var pracs = "&ftype="+$('#type_prac_code').val()+"&prgr="+$('#progressive_prac_code').val()+"&design="+$('#design_prac_code').val()+"&mat="+$('#material_prac_code').val()+"&ar="+$('#ar_prac_code').val()+"&tran="+$('#transition_prac_code').val()+"&pol="+$('#polarized_prac_code').val()+"&edge="+$('#edge_prac_code').val()+"&tint="+$('#tint_prac_code').val()+"&color="+$('#color_prac_code').val()+"&pgx="+$('#pgx_prac_code').val()+"&uv="+$('#uv_prac_code').val();
	
	var pracs = "&ftype="+$('#type_prac_code').val()+"&design="+$('#design_prac_code').val()+"&mat="+$('#material_prac_code').val()+"&ar="+$('#ar_prac_code').val();
	
	top.WindowDialog.closeAll();
	var itemized_popup=top.WindowDialog.open('itemized_popup','itemized_popup.php?module_type_id='+module_type_id+'&item_id='+item_id+pracs,'itemized_popup','width=410,height=430,left=300,scrollbars=yes,top=80');
	itemized_popup.focus();
}

function stock_search(type){
var manuf_id = document.getElementById('manufacturer').value;
var lens_type = document.getElementById('lens_type').value;
var lens_material = document.getElementById('lens_material').value;
var lens_air = document.getElementById('lens_air').value;
//var lens_transition = document.getElementById('lens_transition').value;
//var polarized_name = document.getElementById('polarized_name').value;
//var tint_type = document.getElementById('tint_type').value;
//var color_name = document.getElementById('color_name').value;


//var datastring = '&type='+lens_type+'&material='+lens_material+'&air='+lens_air+'&transition='+lens_transition+'&polarized='+polarized_name+'&tint='+tint_type+'&color='+color_name;
var datastring = '&type='+lens_type+'&material='+lens_material+'&air='+lens_air;
	top.WindowDialog.closeAll();
	var location_popup=top.WindowDialog.open('location_popup','../../patient_interface/lens_stock_search.php?srch_id='+type+'&manuf_id='+manuf_id+datastring,'location_popup','width=1050,height=500,left=180,scrollbars=no,top=150');
	location_popup.focus();
}

function hideFinishOth(){
	$("#finish_type_oth").hide();
	$("input#finish_type_other").val("");
	$("select#finish_type").val('').show();
}
function show_lab_dropdown(vall)
{
	if(vall=="4"){
		$("select#finish_type").hide();
		$("input#finish_type_other").val("");
		$("#finish_type_oth").show();
	}
	else if(vall=="3")
	{
		$("#labs").removeAttr('disabled');
	}
	else
	{
		$("#labs").val('0');
		$("#labs").prop('disabled','disabled');
	}
}

function prac_by_check(val,input_id)
{
	if($(val).is(':checked'))
	{
		$("#"+input_id).removeAttr('readonly');
	}
	else
	{
		$("#"+input_id).val('');
		$("#"+input_id).prop('readonly','readonly');
	}
}

function show_progressive_dropdown(vall)
{
	var type = $(vall).find("option:selected").text();
	var str = type.toLowerCase();
	if(str.match("progressive"))
	{
		$("#lens_progresive").removeAttr('disabled');
	}
	else
	{
		$("#lens_progresive").val('0');
		$("#progressive_prac_code").val('');
		$("#progressive_prac_code").attr('title','');
		$("#lens_progresive").prop('disabled','disabled');
	}
}

/*Fetch dropdown values - VisionWeb*/
function fetch_vision_dd(value, element, sel_id){
	
	if(typeof(value)!="undefined" && typeof(element)!="undefined" && value!="" || value!="0" && element!="" || element!="0"){
		
		if(typeof(sel_id)=="undefined")
			sel_id = 0;
		
		data = {};
		data.action = "getVisionDDAdmin";
		data.value = value;
		data.element = element;
		
		$.ajax({
			method: 'POST',
			data: data,
			url: top.WRP+'/interface/admin/ajax.php',
			success: function(dt){
				dt = $.parseJSON(dt);
				var opt = "";
				if(dt.length>0){
					if(element!="coating"){
						var opt = '<option value="0">Please Select</option>';
						var selected = "";
						$.each(dt, function(i, obj){
							selected = "";
							if(sel_id==obj.id)
								selected = 'selected="selected"';
							opt += '<option value="'+obj.id+'" vw_code="'+obj.vw_code+'" vw_code_type="'+obj.vw_code_type+'" '+selected+'>'+obj.name+'</option>';
						});
						
						if(element=="design"){
							$("#lens_design").html(opt);
						}
						else if(element=="material"){
							$("#lens_material").html(opt);
						}
					}
					else{
						var options = [];
						var sel = false;
						$.each(dt, function(i, obj){
							sel = false;
							if(sel_id==obj.id)
								sel = true;
							options.push({ text: obj.name, value: obj.id, selected: sel });
						});
						$("#lens_air").multiSelectOptionsUpdate(options);
						if(sel_id==0){$("#ar_prac_code").val('');}
					}
				}
			}
		});
	}
}
/*End fetch dropdown values - VisionWeb*/

<?php if($stringAllProcedures!=""){/*	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php */} ?>

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
.table_style tr{float:left;width:100%;}
.module_label1{width:165px !important;}
.rptDropDown>div.multiSelectOptions>label{font-family: Arial !important;font-size: 13.3px;clear:both;}
.rptDropDown>a>span{font-family: Arial !important;font-size: 13.3px;}
.rptDropDown>div.multiSelectOptions>label>input{margin:0px 2px 0px 0;vertical-align: top;float:none;}
</style>
</head>
<body>
<?php 
if(isset($_REQUEST['save']))
{
	/*Saving multiple values in the field*/
	$_POST['lens_air'] = is_array($_POST['lens_air'])?implode(',',$_POST['lens_air']):$_POST['lens_air'];
	extract($_POST);
	$savedId = lense_stock($edit_item_id,$manufacturer,$upc_name,$module_type,$name,$vendor,$lens_type,$lens_progresive,$lens_design,$lens_material,$lens_air,$lens_transition,$polarized_name,$edge_name,$tint_type,$color_name,$sphere_positive_min,$sphere_negative_min,$cylindep_positive_min,$cylindep_negative_min,$sphere_positive_max,$sphere_negative_max,$cylindep_positive_max,$cylindep_negative_max,$min_segment,$diameter,$th,$r_check,$l_check,$finish_type,$finish_type_other,$qty_on_hand,$amount,$uv_check,$pgx_check,$retail_price,$discount,$disc_date,$labs,$type_prac_code,$material_prac_code,$ar_prac_code,$transition_prac_code,$polarized_prac_code,$tint_prac_code,$uv_prac_code,$progressive_prac_code,$design_prac_code,$edge_prac_code,$color_prac_code,$pgx_prac_code,$bc);
	echo "<script>top.falert('Record saved successfully'); var loadItemId = ".((int)$savedId).";</script>";
/*echo "<script>top.falert('Record saved successfully'); window.location.href='index.php'</script>";
//header('Location: index.php');
*/
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
.rptDropDown>a.multiSelect>span{width:105px !important;}
</style>
<div class="listheading mt10">
  <div style="width:1045px; float:left;">Lenses</div>
  <div> <a style="vertical-align:text-top" href="javascript:void(0);" class="text_purpule" onClick="javascript:product_history(document.getElementById('edit_item_id').value);"> HX </a> <a style="margin-left:10px;" href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type').value);"> <img style="width:22px; margin-top:1px;" src="../../../images/search.png" class="serch_icon_stock" title="Search stock"/> </a> </div>
</div>
<form onSubmit="return validateForm();" id="stock_form" action="" name="stock_form" method="post" enctype="multipart/form-data">
  <div style="height:<?php echo $_SESSION['wn_height']-440;?>px width: 150px text-align: center;">
    <input type="hidden" name="edit_item_id" id="edit_item_id" value="">
    <input type="hidden" name="upc_id" id="upc_id" value="">
	<table class="table_cell_padd5">
		<tr>
			<td style="width:35px;" align="left" class="module_label">
				<label for="upc_name">UPC</label>
			</td>
			<td style="width:165px;">
				<input style="width:150px;" type="text" name="upc_name" id="upc_name" onChange="javascript:upc(document.getElementById('upc_id'),'upc_txt');" autocomplete="off" />
			</td>
			<td style="width:100px;" align="left" class="module_label">
				<label for="manufacturer">Manufacturer</label>
			</td>
			<td style="width:160px;">
				<select style="width:150px;" name="manufacturer" id="manufacturer" onChange="get_vendor_manufacturer(this.value,'0');">
					<option value="">Please Select</option>
					<?php 
					$rows = data("select * from in_manufacturer_details where lenses_chk='1' and del_status='0' order by manufacturer_name asc");
					foreach($rows as $r)
					{ 
					?>
					<option value="<?php echo $r['id']; ?>"><?php echo $r['manufacturer_name']; ?></option>
					<?php }	?>
				</select>
			</td>
			<td class="module_label" style="width:50px;">
				<label for="name">Name</label>
			</td>
			<td style="width:165px;">
				<input style="width:150px;" type="text" onChange="javascript:return upc(document.getElementById('upc_id'),'current_txt');" name="name" id="name" autocomplete="off" />
			</td>
			<td style="width:50px;" align="left" class="module_label">
				<label for="vendor">Vendor</label>
			</td>
			<td align="left">
				<select name="vendor" id="vendor" style="width:150px;">
					<option value="">Please Select</option>
					<?php  
					$rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
					foreach($rows as $r)
					{ ?>
					<option value="<?php echo $r['id']; ?>"><?php echo $r['vendor_name']; ?></option>
					<?php }	?>
				</select>
			</td>
		</tr>
		<tr style="display:none;">
			<td>
				<select style="width:133px;" name="module_type" id="module_type" onChange="page_change_acc_type();">
					<?php 
					$rows = data("select * from in_module_type where del_status='0' order by module_type_name asc");
					foreach($rows as $r)
					{ ?>
					<option value="<?php echo $r['id']; ?>" <?php if(strtolower($r['module_type_name'])=='lenses'){echo "selected";} ?>><?php echo $r['module_type_name']; ?></option>
					<?php }	?>
				</select>
			</td>
			<td class="module_label">Type</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
	
    <div class="module_border" style="margin-top:14px">
      <table class="table_collapse table_cell_padd5">
        <tr><td colspan="6"></td></tr>
        <tr>
          <td valign="top" width="50%">
		  <table width="100%" border="0" class="table_collapse lens_cell">
              <tr>
                <td style="width: 85px;">
					<label for="lens_type">Seg Type</label>
				</td>
				<td style="width:150px;">
					<select name="lens_type" id="lens_type" style="width: 130px;" onChange="javascript:fetch_vision_dd(this.value, 'design');javascript:prac_by_type(this.value,'in_lens_type','type_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
					  $rows="";
					  $default_vals = array('SV'=>'type_sv', 'PAL'=>'type_pr', 'BFF'=>'type_bf', 'TFF'=>'type_tf');
					  $rows = data("SELECT * FROM `in_lens_type` WHERE `del_status`='0' ORDER BY FIELD(`vw_code`, 'SV','PAL','BFF','TFF')");
					  foreach($rows as $r)
					  {  ?>
                    <option value="<?php echo $r['id']; ?>" default_val="<?php echo $default_vals[$r['vw_code']]; ?>"><?php echo $r['type_name']; ?></option>
                    <?php }	 ?>
                  </select>
				</td>
				<td style="width:85px;">
					<label for="type_prac_code">Prac Code</label>
				</td>
                <td>
                  <input type="text" name="type_prac_code" id="type_prac_code"  value="" style="width:80px;" readonly/>
                </td>
              </tr>

<?php /*
			  <tr>
			  	<td>
					<label for="lens_progresive">Progressive</label>
				</td>
                <td>
					<select disabled="disabled" name="lens_progresive" id="lens_progresive" style="width:130px;" onChange="javascript:prac_by_type(this.value,'in_lens_progressive','progressive_prac_code');">
                    <option value="0">Please Select</option>
                    <?php 
                                      $rows="";
                                      $rows = data("select * from in_lens_progressive where del_status='0' order by progressive_name asc");
                                      foreach($rows as $r)
                                      {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['progressive_name']; ?></option>
                    <?php }	 ?>
                  </select>
				</td>
				<td>
					<label for="progressive_prac_code">Prac Code</label>
				</td>
				<td>
                	<input type="text" name="progressive_prac_code" id="progressive_prac_code"  value="" style="width:80px;" readonly/>
				</td>
              </tr>
*/ ?>
			  <tr>
			  	<td>
					<label for="lens_design">Design</label>
				</td>
				<td>
					<select name="lens_design" id="lens_design" style="width: 130px;" onChange="javascript:fetch_vision_dd(this.value, 'material');javascript:prac_by_type(this.value,'in_lens_design','design_prac_code');">
						<option value="0">Please Select</option>
					</select>
				</td>
				<td>
					<label for="design_prac_code">Prac Code</label>
				</td>
				<td>
					<input type="text" name="design_prac_code" id="design_prac_code" value="" style="width:80px;" readonly />
				</td>
			  </tr>
              <tr>
			  	<td>
					<label for="lens_material">Material</label>
				</td>
                <td>
					<select name="lens_material" id="lens_material" style="width:130px;" onChange="javascript:fetch_vision_dd(this.value, 'coating');javascript:prac_by_type(this.value,'in_lens_material','material_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
                                      $rows="";
                                      $rows = data("select * from in_lens_material where del_status='0' order by material_name asc");
                                      foreach($rows as $r)
                                      { ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['material_name']; ?></option>
                    <?php }	?>
                  </select>
				</td>
				<td>
					<label for="material_prac_code">Prac Code</label>
				</td>
                <td> 
                  <input type="text" name="material_prac_code" id="material_prac_code"  value="" style="width:80px;" readonly/>
				</td>
              </tr>
			  
              <tr>
			  	<td>
					<label for="lens_air">Treatment</label>
				</td>
                <td class="rptDropDown">
					<select name="lens_air" id="lens_air" style="width:130px;" multiple="multiple">
                    <option value="">Please Select</option>
                    <?php 
                                      $rows="";
                                      $rows = data("select * from in_lens_ar where del_status='0' order by ar_name asc");
                                      foreach($rows as $r)
                                      {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['ar_name']; ?></option>
                    <?php }	 ?>
                  </select>
				</td>
				<td>
					<label for="ar_prac_code">Prac Code</label>
				</td>
                <td align="left" valign="top" class="module_label">
                  <input type="text" name="ar_prac_code" id="ar_prac_code"  value="" style="width: 80px;" readonly />
				</td>
              </tr>

<?php /*
              <tr>
			  	<td>
					<label for="lens_transition">Transition</label>
				</td>
                <td>
					<select name="lens_transition" id="lens_transition" style="width:130px;" onChange="javascript:prac_by_type(this.value,'in_lens_transition','transition_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
                                          $rows="";
                                          $rows = data("select * from in_lens_transition where del_status='0' order by transition_name asc");
                                          foreach($rows as $r)
                                          {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['transition_name']; ?></option>
                    <?php }	 ?>
                  </select>
				</td>
				<td>
					<label for="transition_prac_code">Prac Code</label>
				</td>
                <td>
                  <input type="text" name="transition_prac_code" id="transition_prac_code"  value="" style="width: 80px;" readonly />
				  </td>
              </tr>
			  
              <tr>
			  	<td>
					<label for="polarized_name">Polarized</label>
				</td>
                <td>
					<select name="polarized_name" id="polarized_name" style="width:130px;" onChange="javascript:prac_by_type(this.value,'in_lens_polarized','polarized_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
                                          $rows="";
                                          $rows = data("select * from in_lens_polarized where del_status='0' order by polarized_name asc");
                                          foreach($rows as $r)
                                          {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['polarized_name']; ?></option>
                    <?php }	 ?>
                  </select>
				</td>
				<td>
					<label for="polarized_prac_code">Prac Code</label>
				</td>
                <td>
                  <input type="text" name="polarized_prac_code" id="polarized_prac_code"  value="" style="width: 80px;" readonly/>
				</td>
              </tr>
			  
              <tr>
			  	<td>
					<label for="edge_name">Edge</label>
				</td>
                <td>
					<select name="edge_name" id="edge_name" style="width:130px;" onChange="javascript:prac_by_type(this.value,'in_lens_edge','edge_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
                                          $rows="";
                                          $rows = data("select * from in_lens_edge where del_status='0' order by edge_name asc");
                                          foreach($rows as $r)
                                          {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['edge_name']; ?></option>
                    <?php }	 ?>
                  </select>
				</td>
				<td>
					<label for="module_label">Prac Code</label>
				</td>
                <td>
                  <input type="text" name="edge_prac_code" id="edge_prac_code"  value="" style="width: 80px;" readonly/>
				</td>
              </tr>
			  
              <tr>
			  	<td>
					<label for="tint_type">Tint</label>
				</td>
                <td>
					<select name="tint_type" id="tint_type" style="width:130px;" onChange="javascript:prac_by_type(this.value,'in_lens_tint','tint_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
                                  $rows="";
                                  $rows = data("select * from in_lens_tint where del_status='0' order by tint_type asc");
                                  foreach($rows as $r)
                                  {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['tint_type']; ?></option>
                    <?php } ?>
                  </select>
				</td>
				<td>
					<label for="tint_prac_code">Prac Code</label>
				</td>
                <td>
                  <input type="text" name="tint_prac_code" id="tint_prac_code"  value="" style="width: 80px;" readonly />
				</td>
              </tr>
			  
              <tr>
			  	<td>
					<label for="color_name">Color</label>
				</td>
                <td>
					<select name="color_name" id="color_name" style="width:130px;" onChange="javascript:prac_by_type(this.value,'in_lens_color','color_prac_code');">
                    <option value="">Please Select</option>
                    <?php 
                                  $rows="";
                                  $rows = data("select * from in_lens_color where del_status='0' order by color_name asc");
                                  foreach($rows as $r)
                                  {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['color_name']; ?></option>
                    <?php } ?>
                  </select>
				</td>
				<td>
					<label for="color_prac_code">Prac Code</label>
				</td>
                <td> 
                  <input type="text" name="color_prac_code" id="color_prac_code"  value="" style="width: 80px;" readonly/>
				</td>
              </tr>

              <tr style="display:none;">
                <td>
					<label for="pgx_check">PGX</label>
				</td>
                <td>
					<input type="checkbox" name="pgx_check" id="pgx_check" style="height:15px;width:15px;margin:0;vertical-align:text-bottom;" onClick="prac_by_check(this,'pgx_prac_code');"/>
				</td>
                <td>
					<label for="pgx_prac_code">Prac Code</label>
				</td>
                <td>
                	<input type="text" name="pgx_prac_code" id="pgx_prac_code"  value="" style="width: 80px;" onChange="show_price_from_praccode(this,'','admin');" readonly/>
                </td>
              </tr>
              
			  <tr>
			  	<td>
					<label for="uv_check">UV400</label>
				</td>
                <td>
					<input name="uv_check" id="uv_check" type="checkbox" style="height:15px;width:15px;margin:0;vertical-align:text-bottom;" onClick="prac_by_check(this,'uv_prac_code');" />
				</td>
				<td>
					<label for="uv_prac_code">Prac Code</label>
				</td>
                <td> 
                  <input type="text" name="uv_prac_code" id="uv_prac_code"  value="" style="width: 80px;" onChange="show_price_from_praccode(this,'','admin');" readonly/>
				</td>
              </tr>
*/ ?>

            </table></td>
          <td  valign="top"><div class="module_border" style="margin-bottom:8px;">
              <table class="table_collapse table_style" border="0">
                <tr>
                  <td colspan="4" class="module_heading" style="text-align:left;width:310px;text-indent:20px;"> Lens Limits </td>
                  <td colspan="3" align="right" style="text-indent:-40px;"><input type="file" name="file" /></td>
                </tr>
                <tr>
                  <td style="width: 5px; text-align: center;">+</td>
                  <td style="width: 60px; text-align: center;" align="left">
				  	<input type="text" name="sphere_positive_min" id="sphere_positive_min" style="width:60px;"/>
					<label for="sphere_positive_min">Min.</label>
                  </td>
                  <td style="width: 60px; text-align: center;">
				  	<input type="text" name="sphere_positive_max" id="sphere_positive_max" style="width:60px;"/>
					<label for="sphere_positive_max">Max</label>
				  </td>
                  <td align="left" valign="top" style="text-align: center; width: 50px;">-</td>
                  <td style="text-align: center; width: 60px;">
				  	<input type="text" style="width:60px;" name="sphere_negative_min" id="sphere_negative_min"/>
					<label for="sphere_negative_min">Min.</label>
				  </td>
                  <td style="text-align: center; width: 60px;">
				  	<input type="text" style="width:60px;" name="sphere_negative_max" id="sphere_negative_max"/>
                    <label for="sphere_negative_max" >Max.</label>
				  </td>
                  <td align="left" class="module_label" valign="top">Sphere</td>
                </tr>
                <tr>
                  <td style="width: 5px; text-align: center;">+</td>
                  <td style="text-align: center; width: 60px;">
				  	<input type="text" name="cylindep_positive_min" id="cylindep_positive_min" style="width:60px;" />
					<label for="cylindep_positive_min">Min.</label>
                  </td>
                  <td style="text-align: center; width: 60px;">
				  	<input type="text" name="cylindep_positive_max" id="cylindep_positive_max" style="width:60px;" />
					<label for="cylindep_positive_max">Max.</label>
                  </td>
                  <td style="width: 50px; text-align: center;">-</td>
                  <td style="text-align: center; width: 60px;">
				  	<input type="text" name="cylindep_negative_min" id="cylindep_negative_min" style="width:60px;" />
					<label for="cylindep_negative_min">Min.</label>
                  </td>
                  <td style="text-align: center; width: 60px;">
				  	<input type="text" name="cylindep_negative_max" id="cylindep_negative_max" style="width:60px;" />
					<label for="cylindep_negative_max">Max.</label>
                  </td>
                  <td align="left" class="module_label" valign="top">Cylinder</td>
                </tr>
                <tr>
                  
                        <td align="left" class="module_label module_label1" colspan="3">
						   <label for="min_segment">Min.seg.ht.</label>
						   <select name="min_segment" id="min_segment" style="width:75px;">
                            <option value="">Select</option>
                            <?php 
                                                  $rows="";
                                                  $rows = data("select * from in_min_seg_ht where del_status='0' order by min_seg_name asc");
                                                  foreach($rows as $r)
                                                  {  ?>
                            <option value="<?php echo $r['min_seg_name'].'~:~'.$r['id']; ?>"><?php echo $r['min_seg_name']; ?></option>
                            <?php }	 ?>
                          </select>
                        </td>
                      <td  align="left" class="module_label module_label1" colspan="2">
                        <input type="hidden" name="diameter" id="diameter" style="width:60px;"/>
                         <!-- &nbsp;Diameter-->
						 <label for="bc">Base Curve</label>
                         <input type="text" name="bc" id="bc" style="width:60px;"/>
                      </td>
                      <td align="left" class="module_label module_label1"><input type="hidden" name="th" id="th" style="width:60px;"/>
                          <!--&nbsp;th --></td>
                      </tr>
                      <tr>
                        <td align="left" class="module_label" colspan="2" style="width:90px !important;">
							<label for="r_check">R <input name="r_check" id="r_check" type="checkbox" /></label>
                        	<label for="l_check">L <input name="l_check" id="l_check" type="checkbox" /></label>
						</td>
                      <td  align="left" class="module_label" colspan="2" style="width:210px;">
						<label for="finish_type">Finish Type</label>
						<select onChange="javascript:show_lab_dropdown(this.value);" style="width:117px;" name="finish_type" id="finish_type">
                            <option value="">Please Select</option>
                            <option value="1">Finish</option>
                            <option value="2">Semi-Finished</option>
                            <option value="3">Outside Lab</option>
							<option value="4">Other</option>
                          </select>
						<span id="finish_type_oth" style="display:none;">
						  <input type="text" name="finish_type_other" id="finish_type_other" value="" style="width:94.5px;height:17.5px;" />
						  <img src="<?php echo $GLOBALS['WEB_PATH']?>/images/icon_back.png" style="cursor:pointer;" onClick="hideFinishOth();" />
						</span>
                     </td>
                          
                   <td align="left" valign="top" class="module_label module_label1">
				   	  <label for="labs">Labs</label>
					  <select disabled="disabled" class="px100" name="labs" id="labs" style="width:120px;">
                    	<option value="0">Please Select</option>
                    <?php 
                                      $rows="";
                                      $rows = data("select * from in_lens_lab where del_status='0' order by lab_name asc");
                                      foreach($rows as $r)
                                      {  ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['lab_name']; ?></option>
                    <?php } ?>
                  </select>
                 </td>
               </tr>
                   
                </table>
            </div>
            <table class="table_collapse table_cell_padd10">
              <tr>
                <td align="left" valign="top" class="module_label" width="190px"><input name="qty_on_hand" id="qty_on_hand" type="hidden" style="width:40px;" readonly />
                  Quantity on Hand:&nbsp;&nbsp;<span id="qty_on_hand_td" style="font-weight:bold;">0</span></td>
                <td align="left" valign="top" class="module_label" width="150px">
					<label for="amount">Amount</label>
					<input name="amount" id="amount" type="text" readonly style="width:70px;" />
                </td>
                <td align="left" style="padding:0px; line-height:0.8;" rowspan="3" valign="top"><img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/add_btn.png"  onClick="add_qty_fun('yes');" style="cursor:pointer;"><br>
                  <img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/minus_btn.png"  onClick="add_qty_fun('no');" style="cursor:pointer;"></td>  <td align="left" valign="top" class="module_label"><div class="btn_cls" style="padding:0;">
                    <input type="button" name="submit2" value="Itemized" onClick="open_popup();" />
                  </div></td>
              </tr>
<?php /*              
              <tr>
                <td align="left" valign="top" colspan="4">
					<label for="wholesale_cost">Wholesale Price</label>
					<input style="width:62px;" type="text" name="wholesale_cost" id="wholesale_cost" readonly value="" class="currency" />
                </td>
              </tr>
              <tr>
                <td align="left" valign="top">
					<label for="purchase_price" style="margin-right: 8px;">Purchase Price</label>
					<input name="purchase_price" id="purchase_price" style="width:62px" type="text" onChange="parse_float(this);" class="currency" />
                </td>
 */ ?>
                <td align="left" valign="top" colspan="3">
					<label for="retail_price" style="margin-right: 20px;">Retail Price</label>
					<input style="width:92px;" type="text" name="retail_price" id="retail_price" readonly value="" class="currency" />
				</td>
              </tr>
              <tr>
                <td align="left" valign="top" class="module_label">
					<label for="discount" style="margin-right: 45px">Discount</label>
					<input style="width:70px;" type="text" name="discount" id="discount" value="" />
				</td>
                <td align="left" valign="top" class="module_label" colspan="3" style="width:250px;">
					<label for="datepicker">Discount Until</label>
					<input type="text" name="disc_date" id="datepicker" class="date-pick" style="background-size:20px 23px;padding:3px;width:100px;" value="" />
				</td>
              </tr>
            </table></td>
        </tr>
      </table>
    </div>
  </div>
  <div class="btn_cls mt10">
    <input type="hidden" name="hid_tex" value="0">
    <div style="display:none">
        <input type="submit" name="save" value="Save" id="saveBtn" />
        <input type="submit" name="del" id="delBtn" value="Delete" />                                    
    </div>
  </div>
</form>
<script type="text/javascript">

//var obj6 = new actb(document.getElementById('uv_prac_code'),customarrayProcedure);
//var obj8 = new actb(document.getElementById('pgx_prac_code'),customarrayProcedure);
	
	$("#uv_prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO'
	});
	
	$("#upc_name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'lensData',
		hidIDelem: document.getElementById('upc_id'),
		showAjaxVals: 'upc'
	});
	$("#name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'lensData',
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
	window.location.href= WEB_PATH+'/interface/admin/lens/index.php';
}
function closeWindow(mode){
	window.location.href= WEB_PATH+'/interface/admin/lens/index.php';
}

var dd_pro = new Array();
dd_pro["listHeight"] = 200;
dd_pro["noneSelected"] = "Select All";

$(document).ready(function() {	

validateForm = function(){

	check = document.stock_form;
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
		$("#name").focus();
		$("#hid_tex").val("1");
		return false;
	}
});	
	
	$("#lens_air").multiSelect(dd_pro, lens_callback);
function lens_callback(){
	var coatingVals = $("#lens_air").selectedValuesString();
	javascript:prac_by_type_multi(coatingVals,'in_lens_ar','ar_prac_code');
}
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