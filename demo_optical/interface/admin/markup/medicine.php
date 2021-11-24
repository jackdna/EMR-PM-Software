<?php
require_once("../../../config/config.php");

/*Manufacturers List*/
$manufacturers = array();
$sql_manuf = 'SELECT `id`, `manufacturer_name` AS \'name\' FROM `in_manufacturer_details` WHERE `del_status`=0 AND `medicine_chk`=1 ORDER BY `manufacturer_name` ASC';
$manuf_resp= imw_query($sql_manuf);

if( $manuf_resp && imw_num_rows($manuf_resp) > 0 ){
	
	while( $row = imw_fetch_object($manuf_resp) ){
		
		$id		= $row->id;
		$name	= html_entity_decode($row->name);
		$manufacturers[$name] = $id;
	}
}
imw_free_result($manuf_resp);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Optical</title>
    <link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH']; ?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
	<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
	<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<style type="text/css">
.row{
	line-height:30px;
}
.listheading.thead > th:not(:first-child){
	text-align: left;
}
.even>td:first-child, .odd>td:first-child{
	text-align: center;
}
.even, .odd{
	font-family: "Arial","wf_SegoeUILight","Tahoma","Verdana","sans-serif";
}
.formula{
	width: 97%;
}
#data_table select{
	width: 94%;
}
#data_table td:first-child > input{
	vertical-align: middle;
}
#data_table td:last-child{
	text-align: center;
}
#data_table img{
	cursor: pointer;
	vertical-align: middle;
}
</style>
</head>
<body>
<div class="tab_container" style="float:left; width:100%;margin:10px 0 0 0;">
	<h3 class="listheading" style="margin-top:0;">Medicine Retail Price Markup</h3>
	<div class="row" style="overflow-y:scroll;">
		<table class="table_collapse">
			<thead>
				<tr class="listheading thead" style="background-size:30px;">
					<th style="width:35px;"></th>
					<th style="width:200px;">Manufacturer</th>
					<th style="width:200px;">Vendor</th>
					<th>Formula</th>
					<th style="width:32px;"></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="row" style="height:<?php echo $_SESSION['wn_height']-442; ?>px;overflow-y:scroll;">
	<form id="retail_markup">
		<table class="table_collapse">
			<tbody id="data_table">
				<tr>
					<td style="width:35px;"></td>
					<td style="width:200px;"></td>
					<td style="width:200px;"></td>
					<td></td>
					<td style="width:32px;"></td>
				</tr>
<?php
/*List Records for Frames*/
$sql = 'SELECT 
			`pm`.`id`, 
			`pm`.`module_type_id`, 
			`pm`.`manufacturer_id`, 
			`manuf`.`manufacturer_name`, 
			`pm`.`vendor_id`, 
			`vendor`.`vendor_name`, 
			`pm`.`formula` 
		FROM 
			`in_retail_price_markup` `pm` 
			LEFT JOIN `in_manufacturer_details` `manuf` ON(`pm`.`manufacturer_id` = `manuf`.`id`) 
			LEFT JOIN `in_vendor_details` `vendor` ON(`pm`.`vendor_id` = `vendor`.`id`)
		WHERE 
			`pm`.`module_type_id` = 6 
			AND `pm`.`del_status` = 0 
		ORDER BY 
			`id` ASC';
$resp = imw_query($sql);
$row_count = imw_num_rows($resp);

$sr_no = 1;
if( $row_count > 0 ):
	
	/*Display Records*/
	while( $row = imw_fetch_object($resp) ):
		$row_class = ( $sr_no%2 == 0 ) ? 'even' : 'odd';
?>
				<tr class="<?php echo $row_class; ?>" id="markup_<?php echo $sr_no; ?>">
					<td><input type="checkbox" name="item_id[<?php echo $sr_no; ?>]" value="<?php echo $row->id; ?>" class="item_id" sr_no="<?php echo $sr_no; ?>" /></td>
					<td><?php echo ( $row->manufacturer_id == 0 ) ? 'ALL' : html_entity_decode($row->manufacturer_name); ?></td>
					<td><?php echo ( $row->vendor_id == 0 ) ? 'ALL' : html_entity_decode($row->vendor_name); ?></td>
					<td>
						<input type="text" name="formula[<?php echo $sr_no; ?>]" value="<?php echo html_entity_decode($row->formula); ?>" class="formula" sr_no="<?php echo $sr_no; ?>" />
					</td>
					<td>
<?php if($row->manufacturer_id != 0): ?>
						<img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delMarkupRow(<?php echo $sr_no; ?>)" />
<?php endif; ?>
					</td>
				</tr>
<?php
		$sr_no++;
	endwhile;
	imw_free_result($resp);
else:
?>
				<tr>
					<td colspan="5" style="text-align:center;">No records to display.</td>
				</tr>
<?php
endif;
?>
			</tbody>
		</table>
		<input type="hidden" name="action" value="saveData" />
	</form>
	</div>
</div>
<div id="loading" style="display:block;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
</body>
<script type="text/javascript">

var sr_no = parseInt(<?php echo $sr_no; ?>);
var manufacturers = <?php echo json_encode($manufacturers); ?>

function addNewRow(){
	
	var clas = ( sr_no%2 == 0 ) ? 'even' : 'odd';
	
	var tr = $('<tr/>').attr({class: clas, id: 'markup_'+sr_no});
	
	var srn = $('<td/>');
	var chkbx = $('<input/>').attr({type:'checkbox', name: 'item_id['+sr_no+']', class: 'item_id', sr_no: sr_no}).val('');
	$(chkbx).appendTo(srn);
	$(tr).append(srn);
	
/*Manufacturers List*/
	var manuf = $('<select>').attr({name: 'item_manuf['+sr_no+']'});
	/*Options*/
	$('<option/>').val(0).text('Please Select').appendTo(manuf);
	$.each(manufacturers, function(i, val){
		$('<option/>').val(val).text(i).appendTo(manuf);
	})
	$('<td/>').html(manuf).appendTo(tr);
	
/*Brands List*/
	var vendor = $('<select>').attr({name: 'item_vendor['+sr_no+']'}).prop('disabled', true);
	/*Options*/
	var vendor_opt = $('<option/>').val(0).text('ALL');
	$(vendor).html(vendor_opt);
	$('<td/>').html(vendor).appendTo(tr);
	
/*Formula*/
	var formula = $('<td>');
	var formula_field = $('<input/>').attr({type: 'text', name: 'formula['+sr_no+']', class: 'formula', sr_no: sr_no}).prop('disabled', true);
	$(formula_field).appendTo(formula);
	$(formula).appendTo(tr);
	
/*Delete icon*/
	var delIcon = $('<td>');
	$('<img/>').attr({src: top.WRP+'/images/del.png', onClick: 'delMarkupRow('+sr_no+')'}).appendTo(delIcon);
	$(delIcon).appendTo(tr);

/*Append Row to table Body*/
	$('#data_table').append(tr);
	
/*Increase counter*/
	sr_no++;

/*Bind Events to the dropDowns*/
	/*Manufacturers*/
	$(manuf).on('change', function(){
		
		var manuf_val = $(this).val();
		
		if( manuf_val == 0 ){
			$(vendor).html(vendor_opt).prop('disabled', true);
			$(formula_field).val('').prop('disabled', true);
			$(chkbx).prop('checked', false);
			/*Mark Chekcbox*/
		}
		else{
			var formData = {action: 'get_vendor', mid: manuf_val, return_json: true};
			$.ajax({
				method	: 'POST',
				url		: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/admin/ajax.php',
				data	: formData,
				beforeSend: function(obj){
					$('#loading').show();
				},
				success: function(resp){
					
					$(vendor).html(vendor_opt);
					
					resp = $.parseJSON(resp);
					$.each(resp, function(i, val){
						$('<option/>').val(val).text(i).appendTo(vendor);
					});
					$(vendor).prop('disabled', false);
					$(formula_field).val('').prop('disabled', false);
					$(chkbx).prop('checked', true);
				},
				complete: function(obj){
					$('#loading').hide();
				}
			});
		}
	});
	
	/*Validate Foumula*/
	$(formula_field).on('change', function(){
		validate_formula(this);
	});
}

$('.formula').on('change', function(){
	var srNo = $(this).attr('sr_no');
	$('#markup_'+srNo).find('td:first-child > input.item_id').prop('checked', true);
});

var formData;

/*Save Data*/
function submitForm(){
	
	var checkedRows = $('input.item_id:checked');
	
	if(checkedRows.length == 0){
		top.falert('Plese Select records to save.');
		return;
	}
	
	formData = {exis:[], add:[]};
	var data = {};
	
	$.each(checkedRows, function(index, obj){
		
		var srNo	= $(obj).attr('sr_no');
		var uid		= $(obj).val();
		
		if( uid!='' ){
			
			data = {};
			data.id = uid;
			data.formula = $('.formula[sr_no="'+srNo+'"]').val();
			formData.exis.push(data);
		}
		else{
			
			data = {};
			data.manuf	= $('select[name="item_manuf['+srNo+']"]').val();
			data.vendor	= $('select[name="item_vendor['+srNo+']"]').val();
			data.formula= $('input[name="formula['+srNo+']"]').val();
			formData.add.push(data)
		}
		formData.moduleTypeId = 6;
	});
	
	formData.action = 'getCount';
	/*Get Count of Items to be updated*/
	$.ajax({
		method	: 'POST',
		url		: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/admin/markup/ajax.php',
		data	: formData,
		beforeSend: function(obj){
			$('#loading').show();
		},
		complete: function(obj, resp){
			if(obj.status==200){
				
				response = $.parseJSON(obj.responseText);
				
				if(response.count !='' && response.count>0){
					top.fconfirm('Are you sure want to update the modified Retail Price for the <strong>'+(response.count)+' items?', saveFormula);
				}
				else{
					saveFormula(true);
				}
			}
			$('#loading').hide();
		}
	});
}

function saveFormula(resp){
	formData.action='saveData';
	formData.updateItems=resp;
	$.ajax({
		method	: 'POST',
		url		: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/admin/markup/ajax.php',
		data	: formData,
		beforeSend: function(obj){
			$('#loading').show();
		},
		complete: function(obj){
			document.location.reload();
			$('#loading').hide();
			formData.updateItems = false;
		}
	});
}

/*Del MarkUp Row*/
function delMarkupRow(index){
	
	var row = $('tr#markup_'+index);
	var rowId = $(row).find('td:first-child > input.item_id').val();
	
	if( rowId == '' ){
		/*New Row*/
		$(row).remove();
	}
	else{
		
		var rowId = $('.item_id[sr_no="'+index+'"]').val();
		
		var formData = {action: 'delRow', rowId: rowId};
		
		/*Existing Row*/
		$.ajax({
			method	: 'POST',
			url		: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/admin/markup/ajax.php',
			data	: formData,
			beforeSend: function(obj){
				$('#loading').show();
			},
			complete: function(obj){
				document.location.reload();
				$('#loading').hide();
			}
		});
	}
}

/*Validate Markup Formula*/
$('.formula').on('change', function(){
	validate_formula(this);
});

	/*Action Buttons*/
	var mainBtnArr = new Array();
	var counter = 0;
<?php if( $row_count > 0 ): ?>
	mainBtnArr[counter] = new Array('frame', 'Save', 'top.main_iframe.admin_iframe.submitForm();');
	counter++;
<?php endif; ?>
	mainBtnArr[counter] = new Array('frame', 'Add New', 'top.main_iframe.admin_iframe.addNewRow();');
	top.btn_show( 'admin', mainBtnArr );

/*Action on page ready*/
$(document).ready(function(){
	$('#loading').hide();
});
</script>
</html>