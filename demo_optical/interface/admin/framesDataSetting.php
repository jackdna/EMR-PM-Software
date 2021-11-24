<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require_once("../../config/config.php");
require_once("../../library/classes/functions.php");

if( isset($_POST['action']) ){
	
	if( $_POST['action']=='svaeAction' ){
		
		$status			= (int)$_REQUEST['status'];
		$manufacturer	= (int)$_POST['manuf'];
		$brand			= (int)$_POST['brand'];
		$style			= (int)$_POST['style'];
		$actType		= (int)$_POST['type'];
		
		$operator_id = (int)$_SESSION['authId'];
		
		$statusFields = '';
		if( $status == 0 ){
			$statusFields = ' `del_status`='.$status.', `del_date`=\''.date('Y-m-d').'\',
							`del_time`=\''.date('h:i:s').'\', `del_by`=\''.$operator_id.'\'';
		}
		else{
			$statusFields = ' `del_status`='.$status.', `modified_date`=\''.date('Y-m-d').'\',
							`modified_time`=\''.date('h:i:s').'\', `modified_by`=\''.$operator_id.'\'';
		}
		
		$itemsUpdate = 'UPDATE `in_item` SET '.$statusFields.'
						WHERE `module_type_id` = 1 AND `manufacturer_id`='.$manufacturer.' AND `brand_id`='.$brand;
		if(	$style!=0 ){
			$itemsUpdate .= ' AND `frame_style`='.$style;
		}
		
		if( $actType == 0){
			$selItems = ( isset($_REQUEST['sel']) ) ? $_REQUEST['sel'] : array();
			if( count($selItems)>0 ){
				$itemsUpdate .= ' AND `id` IN('.implode(',', $selItems).')';
			}
			else{
				exit;
			}
		}
		
		$resp = imw_query($itemsUpdate);
		if($resp){
			
			print imw_affected_rows();
			
			if(	$style!=0 && $actType == 1){
				$sqlStyle = 'UPDATE `in_frame_styles` SET '.$statusFields.' WHERE `id`='.$style;
				imw_query($sqlStyle);
			}
			elseif(	$style==0 && $actType == 1){
				$sqlBrand = 'UPDATE `in_frame_sources` SET '.$statusFields.' WHERE `id`='.$brand;
				imw_query($sqlBrand);
			}
		}
	}
	elseif( $_POST['action']=='getFdItems' ){
		
		$manufacturer	= (int)$_POST['manuf'];
		$brand			= (int)$_POST['brand'];
		$style			= (int)$_POST['style'];
		$status			= $_POST['status'];
		
		if( $manufacturer!=0 && $brand!=0 ){
			
			$itemsCount = 
			
			$sqlItems = 'SELECT `i`.`id`, `i`.`upc_code` AS \'upc\', `i`.`name`, `i`.`wholesale_cost` AS \'cost\',
							IF(`i`.`qty_on_hand` = \'\', 0, `i`.`qty_on_hand`) AS \'qty\',
							IF(`m`.`manufacturer_name` IS NULL, \'\', `m`.`manufacturer_name`) AS \'manuf\',
							IF(`b`.`frame_source` IS NULL, \'\', `b`.`frame_source`) AS \'brand\',
							IF(`s`.`style_name` IS NULL, \'\', `s`.`style_name`) AS \'style\',
							IF(`c`.`color_name` IS NULL, \'\', `c`.`color_name`) AS \'color\'
						FROM
							`in_item` `i`
							LEFT JOIN `in_manufacturer_details` `m` ON(`i`.`manufacturer_id` = `m`.`id`) 
							LEFT JOIN `in_frame_sources` `b` ON(`i`.`brand_id` = `b`.`id`) 
							LEFT JOIN `in_frame_styles` `s` ON(`i`.`frame_style` = `s`.`id`) 
							LEFT JOIN `in_frame_color` `c` ON(`i`.`color` = `c`.`id`) 
						WHERE
							`i`.`manufacturer_id` = '.$manufacturer.' 
							AND `i`.`module_type_id` = 1
							AND `i`.`brand_id` = '.$brand;
			$where = '';
			if( $style!= 0 )
				$where .= ' AND `i`.`frame_style` = '.$style;
			
			if( $status=='in_use' )
				$where .= ' AND `i`.`del_status` = 0';
			else
				$where .= ' AND `i`.`del_status` = 1';
				
			$page = (isset($_POST['page'])&& $_POST['page']>0)?(int)$_POST['page']:1;
			$page1 = ($page-1)*100;
			
			$sqlItems = $sqlItems.$where.' LIMIT '.$page1.', 100';
			
			$total = 0;
			$itemsCountSql = 'SELECT COUNT(`i`.`id`) AS \'number\' FROM `in_item` `i`
									WHERE `i`.`manufacturer_id` = '.$manufacturer.' AND `i`.`module_type_id` = 1
									AND `i`.`brand_id` = '.$brand.$where;
			$itemsCountResp = imw_query($itemsCountSql);
			if($itemsCountResp){
				$total = imw_fetch_assoc($itemsCountResp);
				$total = $total['number'];
			}
				
			$itemsResp = imw_query($sqlItems);
			
			$items = array();
			if( $itemsResp && imw_num_rows($itemsResp) > 0 ){
				while( $row = imw_fetch_assoc($itemsResp) ){
					array_push($items, $row);
				}
			}
			imw_free_result($itemsResp);
			$items1 = array();
			$items1['items'] = $items;
			$items1['count'] = $total;
			unset($items);
			print json_encode($items1);
		}
		else{
			
		}
	}
	exit;
}


/*List FramesData Manufacturers*/
$manufacturers = array();
$manufDelStatus= array();
$sqlManuf = 'SELECT `id`, `manufacturer_name` AS \'name\', `del_status`
			FROM `in_manufacturer_details` WHERE `ManufacturerFramesMasterID` !=\'\' AND `del_status`!=2
			ORDER BY `manufacturer_name` ASC';
$manufResp = imw_query( $sqlManuf );
if( $manufResp && imw_num_rows($manufResp) > 0 ){
	while($row = imw_fetch_object($manufResp)){
		$name = html_entity_decode($row->name);
		$manufacturers[$name] = $row->id;
		$manufDelStatus[$row->id] = $row->del_status;
	}
}
imw_free_result($manufResp);

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Optical</title>
	<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
	<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
	<script src="../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<style type="text/css">
.disabled{color:#FD0000}
button.btn_cls{
	padding: 5px 15px 5px 15px;
	border: 1px solid #0077B3;
	cursor: pointer;
	color: #fff;
	border-radius: 5px;
	height: 31px;
	background: #0088cc;
}
#itemsBody{text-align:center}

#heading > tr > th:nth-child(1){width:57px}
#heading > tr > th:nth-child(2){width:28px;}
#heading > tr > th:nth-child(3){width:150px;}
#heading > tr > th:nth-child(4){width:150px;}
#heading > tr > th:nth-child(5){width:120px;}
#heading > tr > th:nth-child(6){width:120px;}
#heading > tr > th:nth-child(7){width:120px;}
#heading > tr > th:nth-child(8){width:80px;}
#heading > tr > th:nth-child(9){width:70px;}
#heading > tr > th:nth-child(10){width:70px;}


#itemsBody > tr > td:nth-child(1){width:50px}
#itemsBody > tr > td:nth-child(2){width:28px;}
#itemsBody > tr > td:nth-child(3){width:150px;}
#itemsBody > tr > td:nth-child(4){width:150px;}
#itemsBody > tr > td:nth-child(5){width:120px;}
#itemsBody > tr > td:nth-child(6){width:120px;}
#itemsBody > tr > td:nth-child(7){width:120px;}
#itemsBody > tr > td:nth-child(8){width:80px;}
#itemsBody > tr > td:nth-child(9){width:70px;}
#itemsBody > tr > td:nth-child(10){width:70px;}

table > thead > tr th:nth-child(2) input, table > tbody > tr td:nth-child(2) input{
	width: 15px;
	height: 15px;
}

#pagingContainer{text-align:center;padding-top:8px;}

</style>
</head>
<body>
<div class="tab_container" style="width:100%;margin:10px 0 0 0;">
	<h3 class="listheading" style="margin-top:0px;margin-bottom:5px;">Frames Data Not in Use</h3>
<div style="width:100%;overflow-y:scroll;">
	<table class="table_collapse">
		<thead class="listheading">
			<tr>
				<th style="width:300px;">Manufacturers</th>
				<th style="width:300px;">Brands</th>
				<th style="width:200px;">Style</th>
				<th style="width:200px;">Stauts</th>
				<th style="width:100px;"></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="text-align:center;">
					<select name="manufacturer" id="manufacturer" style="width:99%" onChange="listManufBrands(this.value);">
						<option value="0">Select Manufacturer</option>
<?php
	foreach( $manufacturers as $key=>$value ):
		$disabled = ($manufDelStatus[$value]==1) ? 'class="disabled"' : '';
?>
						<option value="<?php echo $value; ?>" <?php echo $disabled; ?>><?php echo $key; ?></option>
<? endforeach; ?>
					</select>
				</td>
				<td style="text-align:center;">
					<select name="brand" id="brand" style="width:99%" onChange="listBrandStyle(this.value);" disabled>
						<option value="0">Select Brand</option>
					</select>
				</td>
				<td style="text-align:center;">
					<select name="style" id="style" style="width:99%" disabled>
						<option value="0">Select Style</option>
					</select>
				</td>
				<td style="text-align:center;">
					<select name="status" id="status" style="width:99%">
						<option value="in_use">In Use</option>
						<option value="not_in_use">Not in Use</option>
					</select>
				</td>
				<td style="text-align:center;">
					<button class="btn_cls" onclick="searchItems();">Search</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:100%;overflow-y:scroll;margin-top:5px;">	
	<table class="table_collapse cellBorder table_cell_padd5">
		<thead class="listheading" id="heading">
			<tr>
				<th>Sr.No.</th>
				<th>
					<input type="checkbox" name="selectAll" id="selectAll" onChange="selectAll()">
				</th>
				<th>UPC</th>
				<th>Name</th>
				<th>Manufacturer</th>
				<th>Brand</th>
				<th>Style</th>
				<th>Color</th>
				<th>Cost</th>
				<th>Qty.</th>
			</tr>
		</thead>
	</table>
</div>
<div style="width:100%;height:<?php echo $_SESSION['wn_height']-541; ?>px;overflow-y:scroll;overflow-x:hidden;">	
	<table class="table_collapse cellBorder table_cell_padd5" style="width:99.94%;">
		<tbody id="itemsBody">
			<tr>
				<td colspan="10" style="text-align:center;">No Items to display</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="pagingContainer">

</div>
<div style="text-align:center">
	Set: <select name="actionItems" id="actionItems" disabled>
		<option value="0">Selected Items</option>
	</select>&nbsp;&nbsp;<strong id="actValue">Not In Use</strong>
</div>

</div>

<script type="text/javascript">
function listManufBrands(manufId){
	
	manufId = parseInt(manufId);
	var option = '';
	var brandsOpt = $('#brand');
	
	if(isNaN(manufId) || manufId==0){
		option = $('<option>').val(0).text('Select Brand');
		$( brandsOpt ).html( option ).attr('disabled', true);
		return;
	}
	params = {action:'get_manuf_brands', mod_id:manufId, module:1, framesData:true};
	
	$.ajax({
		type: 'POST',
		url: top.WRP+'/interface/admin/ajax.php',
		data: params,
		success: function(brands){
			brands = $.parseJSON(brands);
			brandsCount = Object.keys(brands).length;
			
			if( brandsCount > 0 ){
				
				option = $('<option>').val(0).text('Select Brand');
				$( brandsOpt ).html( option ).removeAttr('disabled');
				
				$.each(brands, function(value, id){
					
					option = $('<option>').val(id.id).html(value);
					if(id.del=='1')
						option.addClass('disabled');
					
					$( brandsOpt ).append(option);
				});
			}
		}
	});
}
function listBrandStyle(brandId){
	
	brandId = parseInt(brandId);
	var option = '';
	var styleOpt = $('#style');
	
	if(isNaN(brandId) || brandId==0){
		option = $('<option>').val(0).text('Select Style');
		$( styleOpt ).html( option ).attr('disabled', true);
		return;
	}
	params = {action:'get_style', bid:brandId, return_json:true, framesData:true};
	
	$.ajax({
		type: 'POST',
		url: top.WRP+'/interface/admin/ajax.php',
		data: params,
		success: function(styles){
			styles = $.parseJSON(styles);
			stylesCount = Object.keys(styles).length;
			
			if( stylesCount > 0 ){
				
				option = $('<option>').val(0).text('All Styles');
				$( styleOpt ).html( option ).removeAttr('disabled');
				
				$.each(styles, function(value, id){
					
					option = $('<option>').val(id.id).html(value);
					if(id.del=='1')
						option.addClass('disabled');
					
					$( styleOpt ).append(option);
				});
			}
		}
	});
}
function searchItems(page){
	
	page = (typeof(page)=="undefined")?0:page;
	page = parseInt(page);
	
	var manufId = $( '#manufacturer' ).val();
	var brandId = $( '#brand' ).val();
	var styleId= $( '#style' ).val();
	var status	= $( '#status' ).val();
	
	var msg = '';
	if( manufId == 0 )
		msg = '1. Manufacturer';
	if( brandId == 0 )
		msg += (msg!='') ? '<br />2. Brand' : '1. Brand';
	
	if(msg!=''){
		msg = 'Please select following:<br />'+msg;
		top.falert(msg);
		return;
	}
	
	var params = {action:'getFdItems', manuf:manufId, brand:brandId, style:styleId, status:status, page:page};
	
	
	$.ajax({
		type: 'POST',
		url: top.WRP+'/interface/admin/framesDataSetting.php',
		data: params,
		success: function(data){
			data = $.parseJSON(data);
			items = data.items;
			total = parseInt(data.count);
			
			itemsCount = Object.keys(items).length;
			var itemsContainer = $('#itemsBody');
			
			$(itemsContainer).empty();
			
			if( itemsCount > 0 ){
				
				var tr = '';
				var td = '';
				var elem = '';
				$.each(items, function(index, obj){
					
					tr = $('<tr>');
					
					/*Sr.No.*/
					td = $('<td>').text(index+1);
					$(td).appendTo(tr);
					
					/*Checkbox*/
					td = $('<td>');
					elem = $('<input>').addClass('itemSelectedChk').val(obj.id).attr({name:'itemSelected[]', type:'checkbox'});
					$(elem).appendTo(td);
					$(td).appendTo(tr);
					
					/*UPC*/
					td = $('<td>').text(obj.upc);
					$(td).appendTo(tr);
					
					/*Name*/
					td = $('<td>').text(obj.name);
					$(td).appendTo(tr);
					
					/*Manufacturer*/
					td = $('<td>').text(obj.manuf);
					$(td).appendTo(tr);
					
					/*Brand*/
					td = $('<td>').text(obj.brand);
					$(td).appendTo(tr);
					
					/*Style*/
					td = $('<td>').text(obj.style);
					$(td).appendTo(tr);
					
					/*Color*/
					td = $('<td>').text(obj.color);
					$(td).appendTo(tr);
					
					/*Cost*/
					td = $('<td>').text(obj.cost);
					$(td).appendTo(tr);
					
					/*Quantity*/
					td = $('<td>').text(obj.qty);
					$(td).appendTo(tr);
					
					$(itemsContainer).append(tr);
				});
				var tempOpts = '<option value="0">Selected Items</option><option value="1">All '+total+' Item(s)</optio>'
				$('#actionItems').html(tempOpts).removeAttr('disabled');
				$('#actValue').text((status=='in_use')?'Not In Use':'In Use');
			}
			else{
				var tempData = '<tr><td colspan="10" style="text-align:center;">No Items to display</td></tr>';
				$(itemsContainer).html(tempData);
				var tempOpts = '<option value="0">Selected Items</option>';
				$("#actionItems").html(tempOpts).attr('disabled', true);
				$('#actValue').text('Not In Use');
			}
			refreshPaging(total, page);
		}
	});
}

function newPage(event, obj){
	var page = $(obj).attr('href');
	page = page.split('&page=');
	page = page[1];
	searchItems(page);
	event.preventDefault();
}

function refreshPaging(total_pages, page){
	
	$.ajax({
		method: 'GET',
		type: 'POST',
		url: top.WRP+'/interface/admin/paging_new.php',
		data: 'limit=100&total_pages='+total_pages+'&stages=3&page='+page+'&getPagesAjax=true',
		success: function(data){
			$('#pagingContainer').html(data);
		}
	})
}

function changeItemStatus(){
	
	var base = top.main_iframe.admin_iframe;
	
	var actionType	= base.$( '#actionItems' ).val();
	var manufId		= base.$( '#manufacturer' ).val();
	var brandId		= base.$( '#brand' ).val();
	var styleId		= base.$( '#style' ).val();
	var status		= $( '#status' ).val();
	status			= (status=='in_use')?1:0; 
	
	var selectedItems = new Array();
	if( actionType == '0' ){
		
		var chkbx = $('.itemSelectedChk:checked');
		$.each(chkbx, function(index, obj){
			selectedItems.push($(obj).val());
		});
		
		if(selectedItems.length==0){
			top.falert('Please selct Item(s).');
			return;
		}
	}
	
	var params = {action:'svaeAction', manuf:manufId, brand:brandId, style:styleId, type:actionType, sel:selectedItems, status:status};
	
	$.ajax({
		type: 'POST',
		url: top.WRP+'/interface/admin/framesDataSetting.php',
		data: params,
		success: function(data){
			searchItems();
		}
	});
}

function selectAll(){
	var status = $('#selectAll').is(':checked');
	$('.itemSelectedChk').prop('checked', status);
}

$(document).ready(function(){
	
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Update","top.main_iframe.admin_iframe.changeItemStatus();");
	top.btn_show("admin",mainBtnArr);
});
</script>

</body>
</html>