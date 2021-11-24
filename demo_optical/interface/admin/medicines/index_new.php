<?php 
/*
 * File: index.php
 * Coded in PHP7
 * Purpose: Add/Edit/Delete Medicines
 * Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");

/*Action*/
if(isset($_POST['save'])){
	medicine_stock();
	echo "<script>top.falert('Record saved successfully'); var loadItemId = '".((string)$_POST['name'])."';</script>";
	/*echo "<script>top.falert('Record saved successfully'); window.location.href='index_new.php'</script>";*/
}
else{
	echo "<script>var loadItemId = false;</script>";
}
/*End Action*/


/*Manufactuere List*/
$manufacturers = array();
$manuf_dt = imw_query("select `id`, TRIM(`manufacturer_name`) AS 'name' from in_manufacturer_details where medicine_chk='1' and del_status='0' order by manufacturer_name asc");
if($manuf_dt && imw_num_rows($manuf_dt)>0){
	while($row = imw_fetch_object($manuf_dt)){
		$manufacturers[$row->id] = $row->name;
	}
	natcasesort($manufacturers);
}
/*End Manufacturer List*/

/*Vendors List Form Medication*/
$vendors = array();
$vendor_dt = imw_query("SELECT 
	`V`.`id`, 
	`V`.`vendor_name` AS 'name' 
FROM 
	`in_manufacturer_details` `M` 
	LEFT JOIN `in_vendor_manufacture` `VM` ON(
		`M`.`medicine_chk` = '1' 
		AND `M`.`del_status` = '0' 
		AND `M`.`id` = `VM`.`manufacture_id`
	) 
	INNER JOIN `in_vendor_details` `V` ON(`VM`.`vendor_id` = `V`.`id`) 
WHERE 
	`V`.`del_status` = '0' order by vendor_name asc");
if($vendor_dt && imw_num_rows($vendor_dt)>0){
	while($row = imw_fetch_object($vendor_dt)){
		$vendors[$row->id] = $row->name;
	}
	natcasesort($vendors);
}
/*End Vendors List*/

/*Medicine Types*/
$med_types = array();
$med_dt = imw_query("SELECT 
	`id`, 
	`type_name` AS 'name' 
FROM 
	`in_medicines_types` 
WHERE 
	`del_status` = '0' 
ORDER BY 
	`type_name` ASC");
if($med_dt && imw_num_rows($med_dt)>0){
	while($row = imw_fetch_object($med_dt)){
		$med_types[$row->id] = $row->name;
	}
	natcasesort($med_types);
}
/*End Medicine Types*/
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Optical</title>
	<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
	<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
	<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/icd10dd.css?<?php echo constant("cache_version"); ?>" />
	
	<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.js?<?php echo constant("cache_version"); ?>"></script> 
	<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
	<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
	<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>
	
	<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script>
<!-- ICD 10 Files -->
	<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/icd10/jquery-ui-1.10.4.custom.js?<?php echo constant("cache_version"); ?>" ype="text/javascript"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/icd10/icd10_autocomplete.js?<?php echo constant("cache_version"); ?>"></script>
<!-- End ICD 10 Files -->
</head>
<body>
<style>
.left_padd5{padding-left:5px;}
.left_padd15{padding-left:15px;}
.right_padd5{padding-right:5px;}

.serch_icon_stock{
	cursor:pointer;
	text-decoration:none;
	border:0;
	vertical-align:text-bottom;
}
.med_details td{text-align:right;}
.module_label{
	padding:0px;
	text-align:center;
	font-weight: bold;
}
table.countrow>tbody>tr.bgColor{
	background-color:#E5DEDE;
}
.hideRow{display:none}
</style>
<div class="listheading mt10">
	<div style="width:1045px; float:left;">Medicines</div>
	<div>

		<a href="javascript:void(0);" class="text_purpule" style="vertical-align:text-top" onClick="javascript:product_history(document.getElementById('edit_item_id_1').value);">
            	HX
        </a>

		<a style="margin-left:10px;" href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type').value);">
        	<img style="width:22px; margin-top:1px;" src="../../../images/search.png" class="serch_icon_stock" title="Search stock"/>
        </a>
    </div>
</div>
<div style="height:<?php echo $_SESSION['wn_height']-450;?>px;">
    <form onSubmit="return validateForm()" action=""  name="stock_form" id="stock_form" method="post" enctype="multipart/form-data">
		<!-- Hidden fields -->
		<input type="hidden" name="module_type" id="module_type" value="6" />
	<div style="margin:10px 0 20px 0;">
		<table style="width:1118px">
			<tr>
				<td style="width: 120px;">
					<label for="manufacturer">Manufacturer</label>	
				</td>
				<td style="width: 252px;">
					<select style="width:186px;" name="manufacturer" id="manufacturer" onChange="get_vendor_manufacturer(this.value,'0');">
						<option value="">Please Select</option>
						<?php foreach($manufacturers as $key=>$data): ?>
						<option value="<?php echo $key; ?>"><?php echo $data; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				
				<td style="width: 100px;">
					<label for="vendor">Vendor</label>
				</td>
				<td style="width: 252px;">
					<select style="width:186px;" name="vendor" id="vendor">
						<option value="">Please Select</option>
						<?php foreach($vendors as $key=>$data): ?>
						<option value="<?php echo $key; ?>"><?php echo $data; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				
				<td style="width: 140px;">
					<label for="med_typ">Type of Medicines</label>
				</td>
				<td style="width: 252px;">
					<select  style="width:186px;" name="med_typ" id="med_typ">
						<option value="">Select</option>
						<?php foreach($med_types as $key=>$data): ?>
						<option value="<?php echo $key; ?>"><?php echo $data; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="name">Name</label>
				</td>
				<td>
					<input type="text" name="name" id="name" autocomplete="off" onChange="load_medicine(this.value);" style="width:186px;" />
					<input type="hidden" id="name_flag" autocomplete="off" />
				</td>
				<td>
					<label for="ndc">NDC</label>
				</td>
				<td>
					<input name="ndc" id="ndc" type="text" style="width:186px;" />
				</td>
				<td>
					<label for="type_desc">Description</label>
				</td>
				<td>
					<input type="text" name="type_desc" id="type_desc" style="width:186px;" />
				</td>
			</tr>
			<tr>
				<td style="display:none"">
					<label for="fee">Fee</label>
				</td>
				<td style="display:none;">
					<input type="text" name="fee" id="fee" style="width:186px;" />
				</td>
				<td>
					<label for="discount">Discount</label>
				</td>
				<td>
					<input type="text" name="discount" id="discount" style="width:186px;" />
				</td>
				<td>
					<label for="datepicker">Dis. Until</label>
				</td>
				<td>
					<input type="text" id="datepicker" name="disc_date" class="date-pick" style="width:186px;background-size:20px 23px;padding:3px;height:25px;" value="" />
				</td>
				<td>
					<label for="hazardous">Hazardous</label>
				</td>
				<td style="text-align:left;">
					<input type="checkbox" name="hazardous" id="hazardous" style="height:15px;width:15px;margin:0;vertical-align:bottom;" value="on"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="formula">Formula</label>
				</td>
				<td>
					<input type="text" name="formula" id="formula" style="width:186px;" onChange="formula_change(true);" />
					<input type="hidden" name="formula_save" id="formula_save" />
				</td>
			</tr>
			<tr style="display:none;">
				<td>
					<label for="pay_by">Pay By</label>
				</td>
				<td>
					<select style="width:186px;" name="pay_by" id="pay_by">
						<option value="0">Self Pay</option>
						<option value="1">Insurance</option>
					</select>
				</td>
				<td></td>
				<td></td>
			</tr>
		</table>
	</div>
		
	<div class="module_border mt5">
		<input type="hidden" name="totRows" id="totRows" value="1" />
		<!--this is dummy field to keep working previous js functionality-->
		<input type="hidden" name="qty_on_hand_td" id="qty_on_hand_td" value="0" />
		<div  style="height:390px; overflow-x:hidden; overflow-y:scroll">
			<table class="table_collapse countrow" style="width:100%">
				<thead>
					<tr class="listheading">
						<td style="width:9%;" class="module_label">UPC</td>
						<td style="width:8%;" class="module_label">Prac Code</td>
						<td style="width:9%;" class="module_label">DX Codes</td>
						<td style="width:6%;" class="module_label">Dosage</td>
						<td style="width:5%;" class="module_label">Units</td>
						<!--<td style="width:9%;" class="module_label">Exp. Date</td>-->
						<td style="width:8%;" class="module_label">Threshold</td>
						<!-- td style="width:8%;" class="module_label">Wh. Price</td -->
						<td style="width:8%;" class="module_label">Retail Price</td>
						<!-- td style="width:8%;" class="module_label">Pur. Price</td -->
						<td style="width:11%;" class="module_label">Qty. Hand</td>
						<td style="width:8%;" class="module_label">Amount</td>
						<td style="width:3%;" class="module_label">&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					<tr id="tr_b_1" class="bgColor">
						<td>
							<input type="text" name="upc_name_1" id="upc_name_1" style="width:90%;" autocomplete="off" onChange="" />
							<input type="hidden" name="upc_id_1" id="upc_id_1" value="">
							<input type="hidden" name="edit_item_id_1" id="edit_item_id_1" value="" />
							<input type="hidden" name="del_item_id_1" id="del_item_id_1" value="0" />
						</td>
						<td>
							<input type="text" class="item_prac_code" name="item_prac_code_1" id="item_prac_code_1" value="" style="width:90%;" autocomplete="off">
						</td>
						<td>
							<input type="text" name="dx_code_1" id="dx_code_1" style="width:91%;" class="rx_cls" value="" onChange="get_dxcode(this);" autocomplete="off">
						</td>
						<td>
							<input name="dosage_1" id="dosage_1" type="text" style="width:85.5%;" />
						</td>
						<td>
							<input name="units_1" id="units_1" type="text" style="width:85.5%;" autocomplete="off" />
						</td>
						<td>
							<input name="expiry_date_1" id="expiry_date_1" type="hidden" style="width:80px; height: 21px; background-size: 17px 24px;" autocomplete="off" class="expiryDate date-pick" />
							<input name="threshold_1" id="threshold_1" type="text" style="width:90%;" autocomplete="off" />
                                                </td>
<?php /*
                                                <td>
							<input class="currency wholesale_price" name="wholesale_cost_1" id="wholesale_cost_1" type="text" style="width:90%;" onChange="parse_float(this);"  autocomplete="off"/>
						</td>
 */ ?>
                                                <td>
							<input class="currency retail_price" name="retail_price_1" id="retail_price_1" type="text" style="width:90%;" onBlur="price_total_fun(1);" autocomplete="off" onChange="retailPriceChanged(1);" />
							<input type="hidden" class="price_flag" name="retail_price_flag_1" id="retail_price_flag_1" />
						</td>
<?php /*
                                                <td>
							<input class="currency purchase_price" name="purchase_price_1" id="purchase_price_1" type="text" style="width:90%;" onChange="parse_float(this);"  autocomplete="off"/>
						</td>

 */ ?>                                                <td>
							<div style="float:left; background:#CCC; margin:0; padding:1px">
								<input type="text" id="qty_on_hand_1" name="qty_on_hand_1"  style="width:66px; float:left; border:none" value="0" readonly>
								<a onClick="add_qty_fun('yes',1);" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer" title="Add Amt." alt="Add Amt.">+</a>
								<a onClick="add_qty_fun('no',1);" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer; margin-left:5px" title="Min Amt." alt="Min Amt.">-</a>
							</div>
						</td>
						<td>
							<input class="currency" name="amount_1" id="amount_1" type="text" style="width:90%;" readonly />
						</td>
						<td style="text-align:right;">
							<img style="cursor:pointer;" id="addbtn_1" class="addRow" onClick="addrow();" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" />
							<img style="cursor:pointer; display:none" class="removeRow" id="removebtn_1" onClick="removerow(1);" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/removerow.png" title="Remove Row" alt="Remove Row" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
      <div class="btn_cls mt10">
      		<input type="hidden" name="hed_tex" value="0">
            <div style="display:none">
                <input type="submit" name="save" value="Save" id="saveBtn" onClick=""/>
                <input type="submit" name="del" id="delBtn" value="Delete" />                                    
            </div>
            
     </div> 
    </form>
</div>

<script type="text/javascript">
var getDataFilePath = top.WRP+"/library/getICD10data.php"; /*ICD 10*/
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';

/*Mark price flag for the item*/
function retailPriceChanged(itemKey){
	
	var retailPrice = $('#retail_price_'+itemKey).val();
	retailPrice = parseFloat(retailPrice);
	retailPrice = (isNaN(retailPrice))?'0.00':retailPrice;
	
	if(retailPrice=='' || retailPrice=='0.00'){
		$('#retail_price_flag_'+itemKey).val(0);
		formula_change(false);
	}
	else{
		$('#retail_price_flag_'+itemKey).val(1);
	}
}

function addrow(){	
	var totalRows = $("#totRows").val();	/*total No. of Rows present in interface*/
	if($("#upc_name_"+totalRows).val()==""){
		//top.falert("Please enter UPC Code for blank row.");
		//return false;
	}
	
	y = parseInt(totalRows)+1;		/*count for New Row*/
	
	var bgClass = (y%2!==0)?'class="bgColor"':'';
	
	/*Structure of new row*/
	var rowData = '<tr id="tr_b_'+y+'" '+bgClass+'>';
			rowData += '<td>';
				rowData += '<input type="text" name="upc_name_'+y+'" id="upc_name_'+y+'" style="width:90%;" autocomplete="off" onChange="" />';
				rowData += '<input type="hidden" name="upc_id_'+y+'" id="upc_id_'+y+'" value="">';
				rowData += '<input type="hidden" name="edit_item_id_'+y+'" id="edit_item_id_'+y+'" value="" />';
			rowData += '</td>';
			rowData += '<td>';
				rowData += '<input type="text" class="item_prac_code" name="item_prac_code_'+y+'" id="item_prac_code_'+y+'" value="" style="width:90%;" autocomplete="off">';
			rowData += '</td>';
			rowData += '<td>';
				rowData += '<input type="text" name="dx_code_'+y+'" id="dx_code_'+y+'" style="width:91%;" class="rx_cls" value="" onChange="get_dxcode(this);" autocomplete="off">';
			rowData += '</td>';
			rowData += '<td>';
				rowData += '<input name="dosage_'+y+'" id="dosage_'+y+'" type="text" style="width:85.5%;" />';
			rowData += '</td>';
			rowData += '<td>';
				rowData += '<input name="units_'+y+'" id="units_'+y+'" type="text" style="width:85.5%;" autocomplete="off" />';
				rowData += '<input name="expiry_date_'+y+'" id="expiry_date_'+y+'" type="hidden" style="width:80px; height: 21px; background-size: 17px 24px;" autocomplete="off" class="expiryDate date-pick" />';
			rowData += '</td>';
			rowData += '<td>';
				rowData += '<input name="threshold_'+y+'" id="threshold_'+y+'" type="text" style="width:90%;" autocomplete="off" />';
			rowData += '</td>';
			/*rowData += '<td>';
				rowData += '<input class="currency wholesale_price" name="wholesale_cost_'+y+'" id="wholesale_cost_'+y+'" type="text" style="width:90%;" onChange="parse_float(this);"  autocomplete="off"/>';
                        rowData += '</td>';*/
			rowData += '<td>';
				rowData += '<input class="currency retail_price" name="retail_price_'+y+'" id="retail_price_'+y+'" type="text" style="width:90%;" onBlur="price_total_fun('+y+');" autocomplete="off" onChange="retailPriceChanged('+y+');" />';
				rowData += '<input type="hidden" class="price_flag" name="retail_price_flag_'+y+'" id="retail_price_flag_'+y+'" />';
			rowData += '</td>';
			/*rowData += '<td>';
				rowData += '<input class="currency purchase_price" name="purchase_price_'+y+'" id="purchase_price_'+y+'" type="text" style="width:90%;" onChange="parse_float(this);"  autocomplete="off"/>';
                        rowData += '</td>';*/
			rowData += '<td>';
				rowData += '<div style="float:left; background:#CCC; margin:0; padding:1px">';
					rowData += '<input type="text" id="qty_on_hand_'+y+'" name="qty_on_hand_'+y+'"  style="width:66px; float:left; border:none" value="0" readonly>';
					rowData += '<a onClick="add_qty_fun(\'yes\','+y+');" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer" title="Add Amt." alt="Add Amt.">+</a>';
					rowData += '<a onClick="add_qty_fun(\'no\','+y+');" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer; margin-left:5px" title="Min Amt." alt="Min Amt.">-</a>';
				rowData += '</div>';
			rowData += '</td>';
			rowData += '<td>';
				rowData += '<input class="currency" name="amount_'+y+'" id="amount_'+y+'" type="text" style="width:90%;" readonly />';
			rowData += '</td>';
			rowData += '<td style="text-align:right;">';
				rowData += '<img style="cursor:pointer;" id="addbtn_'+y+'" class="addRow" onClick="addrow();" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" />';
				rowData += '<img style="cursor:pointer; display:none" class="removeRow" id="removebtn_'+y+'" onClick="removerow('+y+');" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/removerow.png" title="Remove Row" alt="Remove Row" />';
			rowData += '</td>';
		rowData += '</tr>';
	
	/*Insert new row at last*/
	$(rowData).insertAfter($("#tr_b_"+totalRows));
	currencySymbols();
	
	/*Increase total row count.*/
	$("#totRows").val(y);
	
	$("table.countrow tr .addRow").hide();
	$("table.countrow tr .removeRow").show();
	
	$("table.countrow tr:last-child .addRow").show();
	$("table.countrow tr:last-child .removeRow").hide();
	
	/*Bind Functions*/
	/*Bind Daepicker*/
		$("#expiry_date_"+y).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'mm-dd-yy'
		});
	/*End Datepicker*/
	
	/*typeahead*/
		$(".item_prac_code").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'praCode',
			showAjaxVals: 'defaultCode'
		});
	
	/*ICD 10 typeahead*/
		bind_autocomp($("#dx_code_"+y), getDataFilePath);
	/*End ICD 10 typeahead*/
}
function removerow(id, conf){
	
	if(typeof(conf)==="undefined"){
		top.fconfirm("Are you sure want to delete this item from inventory?", removerow, id, true);
		return;
	}
	else{
		if(id==false)
			return;
		
		id = parseInt(conf);
	}
	
	$("#tr_b_"+id).addClass('hideRow');
	$("#tr_b_"+id).find('#del_item_id_'+id).val(1);
	
	var rows = $("table.countrow>tbody>tr:not(.hideRow)");
	$.each(rows, function(i, obj){
		if(i%2===0){
			$(obj).addClass('bgColor');
		}
		else{
			$(obj).removeClass('bgColor');
		}
	});
}

function submitFrom(){
	$('#saveBtn').click();
}
function newForm(){
	window.location.href= WEB_PATH+'/interface/admin/medicines/index_new.php';
}
function closeWindow(mode){
	window.location.href= WEB_PATH+'/interface/admin/medicines/index_new.php';
}

/*Calculate total amount*/
function price_total_fun(id){
	var retail_price =0;
	var qty_on_hand =0;
	if(document.getElementById('retail_price_'+id).value>0){
		retail_price = document.getElementById('retail_price_'+id).value;
	}
	if(document.getElementById('qty_on_hand_'+id).value>0){
		qty_on_hand = document.getElementById('qty_on_hand_'+id).value;
	}	
	var total_price = parseFloat(retail_price)*parseInt(qty_on_hand);
	document.getElementById('retail_price_'+id).value = parseFloat(retail_price).toFixed(2);
	document.getElementById('amount_'+id).value=total_price.toFixed(2);
}

/*Load Vendor's List for the selected manufacturer*/
function get_vendor_manufacturer(mid,vid){
	if(mid!=''){
		var string = 'action=get_vendor&mid='+mid+'&vid='+vid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response){
				var opt_data = "<option value='0'>Please Select</option>"+response;
				$('#vendor').html(opt_data);
			}
		});
	}
}

var previous_value = "";
/*Load Medicine By Name*/
function load_medicine(name){
	if(typeof(name)=="undefined" || name==""){
		top.falert("Please Selecte Medicine.");
	}
	else{
		//if(previous_value==name || $("#name_flag").val()==""){return false;}
		if($("#name_flag").val()==""){return false;}
		previous_value = name;
		var parameters = {};
		parameters.action = "getMdDetails";
		parameters.item_name = name;
		
		$.ajax({
			url: WEB_PATH+'/interface/patient_interface/ajax.php',
			type: 'POST',
			data: parameters,
			success: function(data){
				data = $.parseJSON(data);
				if(Object.keys(data).length>0){
					
					/*Manufacturer and Vendor*/
					if(data.manufacturer_id != "0"){
						$("#manufacturer").val(data.manufacturer_id);
						get_vendor_manufacturer(data.manufacturer_id, data.vendor_id);
					}
					else if(data.vendor_id != "0"){
						$("#vendor").val(data.vendor_id);
					}
					
					/*Medicine type*/
					if(data.med_typ != "" || data.med_typ != "0"){
						$("#med_typ").val(data.med_typ);
					}
					
					/*Pay By*/
					if(data.pay_by != ""){
						$("#pay_by").val(data.pay_by);
					}
					
					/*Description*/
					$("#type_desc").val(data.type_desc);
					
					if(data.ndc!="")
						$("#ndc").val(data.ndc);
						
					if(data.fee!="")
						$("#fee").val(data.fee);
					
					if(data.discount!="")
						$("#discount").val(data.discount);
					
					if(data.discount_till != "0000-00-00")
						$("#datepicker").val(data.discount_till);
					
					/*Hazardous val here*/
					if(data.harcardous=="1")
						$("#hazardous").prop('checked', true);
						
					/*Price Markup Formula*/
					if(data.formula!='')
						$("#formula").val(data.formula);
					if(data.formula_save!='')
						$("#formula_save").prop(data.formula_save);
					var itemFormula = (data.formula_save!='')?data.formula_save:data.formula;
					
					var medRows = $('tr[id^="tr_b_"]');
					var ids = new Array();
					$.each(medRows, function(i, val){
						ids[i] = $(val).attr('id');
					});
					
					$.each(data.details, function(i, vals){
						delete ids[i];
						/*Add ned row for types*/
						key = i+1;
						if(key>1){addrow();}
						
						/*Hidden Item Id from Items Table*/
						$("#edit_item_id_"+key).val(vals.id);
						
						$("#upc_name_"+key).val(vals.upc);
						get_prac_code_name(vals.prac,'item_prac_code_'+key);
						$("#dx_code_"+key).val(vals.dx);
						$("#dosage_"+key).val(vals.dosage);
						$("#units_"+key).val(vals.units);
						$("#expiry_date_"+key).val(vals.expiry_date);
						$("#threshold_"+key).val(vals.threshold);
						//$("#wholesale_cost_"+key).val(vals.whole);
						
						$("#retail_price_flag_"+key).val(vals.retail_price_flag);
						
						if(vals.retail_price_flag=='0'){
							var item_retail_price = calculate_retail_price(itemFormula, vals.whole, vals.purch);
							$("#retail_price_"+key).val(item_retail_price.toFixed(2));
						}
						else{
							$("#retail_price_"+key).val(vals.retail);
						}
						
						//$("#purchase_price_"+key).val(vals.purch);
						$("#qty_on_hand_"+key).val(vals.qty);
						$("#amount_"+key).val(vals.amount);
					});
					
					$.each(ids, function(i, id){
						$("tr#"+id).remove();
					});
					$("table.countrow tr .addRow").hide();
					$("table.countrow tr .removeRow").show();
					
					$("table.countrow tr:last-child .addRow").show();
					$("table.countrow tr:last-child .removeRow").hide();
				}
			}
		});
	}
}
/*End Load Medicine By Name*/

/*Addd Quantity seperated by facility*/
function add_qty_fun(type,id){
	var item_id=document.getElementById('edit_item_id_'+id).value;
	top.WindowDialog.closeAll();
	if(item_id && typeof(item_id)!=='undefined')
	var addwin=top.WindowDialog.open('location_lot_popup','../lens/location_lot_popup.php?item_add='+type+'&id=_'+id+'&item_id='+item_id+'&item_type=medicine','location_lot_popup','width=900,height=440,left=600,scrollbars=no,top=150');
	addwin.focus();
}

/*Search functionality*/
function stock_search(type){
	var manuf_id = document.getElementById('manufacturer').value;
	var vendor = document.getElementById('vendor').value;
	top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('location_lot_popup','../stock_search.php?srch_id='+type+'&manuf_id='+manuf_id+'&vendor='+vendor+'&source=medstock','location_popup','width=1237,height=500,left=180,scrollbars=no,top=150');
	addwin.focus();
}
/*End Search Functionality*/

/*Current Year*/
var cyear = new Date().getFullYear();

$(document).ready(function(){

/*Form Validation*/
validateForm = function(){
	check = document.stock_form;
	if(check.name.value.replace(/\s/g, "") == "" && check.upc_name_1.value.replace(/\s/g, "") == ""){
		top.falert("Please Enter Upc Code or Item Name");
		check.upc_name_1.value="";		
		check.upc_name_1.focus();
		return false;
	}
}

/*Bind Daepicker*/
	$("#datepicker, .expiryDate").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
/*End Datepicker*/

/*typeahead*/
	$(".item_prac_code").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCode'
	});
	
	$("#name").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'medNames',
		showAjaxVals: 'name',
		hidIDelem: document.getElementById('name_flag')
	});
	
/*ICD 10 typeahead*/
	bind_autocomp($("#dx_code_1"), getDataFilePath);
/*End ICD 10 typeahead*/

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","New","top.main_iframe.admin_iframe.newForm()");
	mainBtnArr[2] = new Array("frame","Make Copy","top.main_iframe.admin_iframe.copy_item_new()");
	mainBtnArr[3] = new Array("frame","Cancel","top.main_iframe.admin_iframe.closeWindow()");
	mainBtnArr[4] = new Array("frame","Delete","top.main_iframe.admin_iframe.delete_item()");
	top.btn_show("admin",mainBtnArr);	
	
	if(loadItemId && loadItemId!=''){
		$('#name_flag').val(1);
		$('#name').val(loadItemId);
		load_medicine(loadItemId);
	}
});

function formula_change(flag, rowId){
	
	if(typeof(rowId)=='undefined'){
		rowId = false;
	}
	
	flag = ( typeof(flag) == 'undefined' ) ? false : flag;
	
	var formula_changed = $('#formula').val();
	if(flag)
		$('#formula_save').val(formula_changed);
	
	if(rowId){
		retail_price_changed = calculate_retail_price(formula_changed, 0, 0); 
		//retail_price_changed = calculate_retail_price(formula_changed, $('#wholesale_cost_'+rowId).val(), $('#purchase_price_'+rowId).val());
	}
	else{
		var itemRows = $('table.table_collapse > tbody > tr[id^="tr_b_"]');
		
		$.each(itemRows, function(index, obj){
			var wholesale_price = 0; 
			//var wholesale_price = $(obj).find('.wholesale_price').val();
			var purchase_price	= 0; 
			//var purchase_price	= $(obj).find('.purchase_price').val();
			var price_flag		= $(obj).find('.price_flag').val();
			
			var retail_price_field	= $(obj).find('.retail_price');
			
			if(price_flag=='0'){
				retail_price_changed = calculate_retail_price(formula_changed, wholesale_price, purchase_price);
				$(retail_price_field).val(retail_price_changed.toFixed(2));
			}
		});
	}
}

</script>
</body>
</html>