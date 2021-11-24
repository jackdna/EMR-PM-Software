<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require_once("../../config/config.php");
require_once("../../library/classes/functions.php");

if( isset($_POST['action']) ){
	
	if( $_POST['action']=='updatePrice' ){
		
		$items = $_POST['items'];
		if( count($items) > 0){
			$operator_id = (int)$_SESSION['authId'];
			
			foreach($items as $item){
				$itemId = (int)$item;
				$sqlUpdate = 'UPDATE `in_item`
							SET
								`wholesale_cost`=`fd_price_temp`,
								`fd_price_change_alert`=0,
								`fd_price_temp`=0.00
							WHERE `id`='.$itemId;
				imw_query($sqlUpdate);
			}
		}
	}
}
$limit = 100;
$stages = 3;

$page1 = (isset($_REQUEST['page']) && $_REQUEST['page']!='0')?(int)$_REQUEST['page']:1;
$limit_l = ($page1-1)*$limit;
$page = $page1;

$total_pages = 0;
$itemsCountSql = 'SELECT COUNT(`i`.`id`) AS \'number\' FROM `in_item` `i`
						WHERE `i`.`fd_price_change_alert` = 1 AND `i`.`module_type_id` = 1 AND `i`.`del_status` = 0';
$itemsCountResp = imw_query($itemsCountSql);
if($itemsCountResp){
	$total_pages = imw_fetch_assoc($itemsCountResp);
	$total_pages = $total_pages['number'];
}

$sqlItems = 'SELECT `i`.`id`, `i`.`upc_code` AS \'upc\', `i`.`name`, `i`.`wholesale_cost` AS \'cost\',
				IF(`i`.`qty_on_hand` = \'\', 0, `i`.`qty_on_hand`) AS \'qty\', `m`.`manufacturer_name` AS \'manuf\',
				`b`.`frame_source` AS \'brand\', `s`.`style_name` AS \'style\', `i`.`fd_price_temp` AS \'nCost\'
			FROM
				`in_item` `i`
				LEFT JOIN `in_manufacturer_details` `m` ON(`i`.`manufacturer_id` = `m`.`id`) 
				LEFT JOIN `in_frame_sources` `b` ON(`i`.`brand_id` = `b`.`id`) 
				LEFT JOIN `in_frame_styles` `s` ON(`i`.`frame_style` = `s`.`id`)
			WHERE
				`i`.`fd_price_change_alert` = 1
				AND `i`.`module_type_id` = 1
				AND `i`.`del_status` = 0
			LIMIT '.$limit_l.', 100';

$respItems = imw_query($sqlItems);
$items = array();
if( $respItems && imw_num_rows($respItems) > 0 ){
	while($row = imw_fetch_assoc($respItems)){
		array_push($items, $row);
	}
}
imw_query($respItems);
$itemsCount = count($items);
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
	<h3 class="listheading" style="margin-top:0px;margin-bottom:5px;">Frames Data Price Change</h3>


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
				<th>Cost</th>
				<th>N. Cost</th>
				<th>Qty.</th>
			</tr>
		</thead>
	</table>
</div>
<div style="width:100%;height:<?php echo $_SESSION['wn_height']-453; ?>px;overflow-y:scroll;overflow-x:hidden;">	
	<table class="table_collapse cellBorder table_cell_padd5" style="width:99.94%;">
		<tbody id="itemsBody">
<?php
	if($itemsCount>0): 
		foreach( $items as $key=>$tem ):
?>
			<tr>
				<td><?php echo $key+1; ?></td>
				<td>
					<input class="itemSelectedChk" name="itemSelected[]" type="checkbox" value="<?php echo $tem['id']; ?>">
				</td>
				<td><?php echo $tem['upc']; ?></td>
				<td><?php echo $tem['name']; ?></td>
				<td><?php echo $tem['manuf']; ?></td>
				<td><?php echo $tem['brand']; ?></td>
				<td><?php echo $tem['style']; ?></td>
				<td><?php echo $tem['cost']; ?></td>
				<td><?php echo $tem['nCost']; ?></td>
				<td><?php echo $tem['qty']; ?></td>
			</tr>
<?php 
		endforeach;
	else:
?>
			<tr>
				<td colspan="10" style="text-align:center;">No Items to display</td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>
</div>
<div style="text-align:center;padding-top:10px;">
<?php
	include('paging_new.php');
?>
</div>

</div>

<script type="text/javascript">

function updatePrice(){
	var base = top.main_iframe.admin_iframe;
	var checkbox = base.$('.itemSelectedChk:checked');
	var selected = new Array();
	
	$.each(checkbox, function(index, obj){
		selected.push($(obj).val());
	});
	
	if(selected.length==0){
		top.falert('Please select a record.');
		return;
	}
	
	var params = {action:'updatePrice', items:selected };
	
	$.ajax({
		type: 'POST',
		url: top.WRP+'/interface/admin/framesPricecChange.php',
		data: params,
		complete: function(){
			document.location.href='framesPricecChange.php';
		}
	});
}

function selectAll(){
	var status = $('#selectAll').is(':checked');
	$('.itemSelectedChk').prop('checked', status);
}

$(document).ready(function(){
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Update Cost","top.main_iframe.admin_iframe.updatePrice();");
	top.btn_show("admin", mainBtnArr);
});
</script>

</body>
</html>