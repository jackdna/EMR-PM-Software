<?php 
/*
File: other_selection.php
Coded in PHP7
Purpose: Other Orders Selection Criteria
Access Type: Direct access
*/
// last updated : 8/1/2018 4:12PM G
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname(__FILE__)."/../../library/classes/functions.php"); 	
$patient_id=$_SESSION['patient_session_id'];
$page_name="other_selection";
$pageName="other_selection";
$order_id = '';
$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
while($sel_write=imw_fetch_array($sel_rec)){
	$discode .= "<option value=".$sel_write['d_id'].">".$sel_write['d_code']."</option>";
}

$action=$_REQUEST['frm_method'];
foreach($_POST as $key=>$dt){
	if(strpos($key, "pos_")!== false && strpos($key, "pos_")=="0"){
		$key1= str_replace("pos_", "", $key);
		$_POST[$key1] = $dt;
		unset($_POST[$key]);
	}

}

foreach($_POST as $key=>$dt){

	
	if(strpos($key, "_lensD")!= false){
		$key1= trim($key);
		$key1= str_replace("_lensD","",$key1);
		$_POST[$key1] = $dt;
		unset($_POST[$key]);
	}
}

$order_id=$_SESSION['order_id'];
$sel_ord_qry=imw_query("select order_enc_id from in_order where id ='$order_id'");
$sel_ord_row=imw_fetch_array($sel_ord_qry);
$order_enc_id=$sel_ord_row['order_enc_id'];
$idoc_enc_id = (int)$order_enc_id;

if($action=="order_post" || $action =="dispensed_post"){
	$_POST['other_page_name']="other_selection";
	other_order_action($action,$_POST);
	$action="idoc_order_post";
	
	echo "<script type='text/javascript'>window.location.href='other_selection.php?frm_method=".$action."'</script>";
}else if($action=="idoc_order_post"){
	
	echo "<script type='text/javascript'>top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('Add_new_popup','../../remoteConnect.php?encounter_id=$order_enc_id','opt_med');</script>";
}
elseif($action=="next"){
	
	echo "<script type='text/javascript'>window.location.href='patient_pos.php'</script>";
}
elseif($action=="save"){
	
	other_order_action($action,$_POST);
	$taxes = array();
	foreach($_POST as $key=>$dt){
		if(strpos($key, "tax_")!== false && strpos($key, "tax_")=="0"){
			$key1= str_replace("tax_", "", $key);
			$taxes[$key1] = $dt;
			unset($_POST[$key]);
		}
	}
	/*if(count($taxes)>0){
		$_POST = $taxes;
		other_order_action($action,$taxes);
	}*/
	echo "<script type='text/javascript'>window.location.href='other_selection.php'</script>";
}
else if($action=="cancel"){
	//other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='other_selection.php'</script>";
}

if($_SESSION['order_id']>0){
	$order_id=$_SESSION['order_id'];
}
if($_REQUEST['order_id']>0)
{
	$order_id=$_REQUEST['order_id'];
	$_SESSION['order_id']=$order_id;
}

/*
$stringAllUpc = get_upc_name_id();

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

$AllNameArray = array_slice($AllNameArray, 0, 20000);
$AllUpcIdArrays = array_slice($AllUpcIdArrays, 0, 20000);
$AllUpcArray = array_slice($AllUpcArray, 0, 20000);

$AllUpcIdArray = implode(',',$AllUpcIdArrays);
$AllUpcArray = implode(',',$AllUpcArray);
$AllNameArray = implode(',',$AllNameArray);
*/

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
/*
//------------------------	START GETTING DATA FOR MENUS TO DX Code -----------------------//
	$dx_code_arr=array();
	$sql_dx = "select * from diagnosis_category order by category ASC";
	$rez_dx = imw_query($sql_dx);	
	while($row_dx=imw_fetch_array($rez_dx)){
		$cat_id_dx = $row_dx["diag_cat_id"];		
		$sql_dx = "select * from diagnosis_code_tbl WHERE diag_cat_id='".$cat_id_dx."' AND delete_status = '0' order by d_prac_code ASC";
		$rezCodes_dx = imw_query($sql_dx);
		$arrSubOptions_dx = array();
		if(imw_num_rows($rezCodes_dx) > 0){
			while($rowCodes_dx=imw_fetch_array($rezCodes_dx)){
				$arrSubOptions_dx[] = array($rowCodes_dx["d_prac_code"]."-".$rowCodes_dx["diag_description"],$xyz, $rowCodes_dx["d_prac_code"]);
				$arrdxCodesAndDesc[] = $rowCodes_dx["diagnosis_id"];
				$arrdxCodesAndDesc[] = $rowCodes_dx["d_prac_code"];
				$arrdxCodesAndDesc[] = $rowCodes_dx["diag_description"];
				
				$code_dx = str_replace(";","~~~",$rowCodes_dx["d_prac_code"]);
				$dx_desc = str_replace(";","~~~",$rowCodes_dx["diag_description"]);
				$stringAllProcedures_dx.="'".str_replace("'","",$code_dx)."',";	
				$stringAllProcedures_dx.="'".str_replace("'","",$dx_desc)."',";
				$dx_code_arr[$rowCodes_dx["diagnosis_id"]]=$rowCodes_dx["d_prac_code"];
			}
		$arrdxCodes[] = array($row["category"],$arrSubOptions_dx);
		}		
	}

	$stringAllProcedures_dx = substr($stringAllProcedures_dx,0,-1);
	
//-----------------  END GETTING DATA FOR MENUS TO CATEGORY OF DX Code	------------------------//
*/

/*Order Edit operations*/
$order_edit_btn_status = true;
$order_post_btn_status = true;
$no_save = "'ordered', 'received', 'dispensed'"; /*Status for which save not allowed*/
if( $order_id != '' && $order_id > 0 ){
	$order_detail_status = imw_query('SELECT COUNT(\'id\') AS \'count\' FROM `in_order_details` WHERE `order_id`=\''.$order_id.'\' AND `order_status` IN('.$no_save.')');
	if($order_detail_status && imw_num_rows($order_detail_status)>0){
		$order_detail_status = imw_fetch_assoc($order_detail_status);
		$order_detail_status = (int)$order_detail_status['count'];
		if($order_detail_status > 0){
			$order_edit_btn_status = false;
		}
	}
	
	$no_post = "'received', 'dispensed'"; /*Status for Which Post not allowed*/
	$order_detail_status = imw_query('SELECT COUNT(\'id\') AS \'count\' FROM `in_order_details` WHERE `order_id`=\''.$order_id.'\' AND `order_status` IN('.$no_post.')');
	if($order_detail_status && imw_num_rows($order_detail_status)>0){
		
		$order_detail_status = imw_fetch_assoc($order_detail_status);
		$order_detail_status = (int)$order_detail_status['count'];
		if($order_detail_status > 0){
			
			$order_post_btn_status = false;
		}
	}
}
?>

<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.confirm.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.confirm.js?<?php echo constant("cache_version"); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 

<script type="text/javascript">
function prescription_details(){
	top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/lens_prescriptions.php','lens_history_pop','width=800,height=340,left=600,scrollbars=no,top=150');
	win1.focus();
}
pageName = "otherSelection"; /*Page name for deleting POS row*/
</script>

<script>
$(document).ready(function(e) {
	calculate_all();
	$( ".price_cls,.price_disc,.qty_cls" ).change(function() {
		calculate_all();
	});
	
	//BUTTONS
	var mainBtnArr=[];
	top.btn_show("admin",mainBtnArr);		
	
});

function get_upcbyid(upc_code)
{
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = 'action=managestock&code='+ucode;
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = (response.trim()!="")?$.parseJSON(response):{};
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
					$("#other_upc_name").val(item.upc_code);
					$('#other_name').val(item.name);
				 });
			 }
			 else
			 {
				// $("#stock_form")[0].reset();
			 }
		}
	}); 
}

var rowData_c='';
var details='';
var tr  = '';
function addrow()	
{
	var upc_code = $('#upc_id_1').val();
	var ucode = $.trim(upc_code);
	var discode = '<?php echo $discode; ?>';
	var cur_date = '<?php echo date('Y-m-d'); ?>';
	if(ucode!='' && ucode>0)
	{
		var dataString = 'action=managestock&code='+ucode;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: dataString,
			cache: false,
			success: function(response)
			{
				if(response=="")
				{
					top.falert("UPC Code Does not Exist");
				}
				else
				{
					var dataArr = $.parseJSON(response);
					if(dataArr!="")
					{
						$.each(dataArr, function(i, item) 
						{
							//var getRows = $(".countrow tr").size();
							var getRows = $("#last_cont").val();
							y = parseInt(getRows)+parseInt(1);
							try{
								addNewRow(item.module_type_id, item, y, 'other_selection');
							}
							catch(e){
								console.log(e.message);
							}
						 }); 
						 $("#last_cont").val(y);
						 $("#pos_last_cont").val(y);
						$('#other_upc_name').val('');
						$('#other_name').val('');
						$('#upc_id_1').val('');
					 }
					 else
					 {
						top.falert("UPC Code Does not Exist");
					 }
				}
			}
		}); 
	}
}

var remove_row='';
function removerow(row,order_detail_id)
{
	remove_row = $(".countrow tr").size();
	
	if(remove_row>0)
	{
		$("#tr_b_"+row).remove();
	}
	$("#last_cont").val(remove_row-1);
	$('#upc_id_1').val('');
	var calc = calculate_all();
	if(order_detail_id>0){
		del_rec_fun(order_detail_id);
	}
}

function calculate_all(){
	var disc_val=0;
	var price_cls=0;
	grand_price = grand_disc = grand_total = grand_qty = 0;
	    $('.price_cls').each(function(index, element) {
		price_cls 	= parseFloat($('.price_cls').get(index).value);
		qty_cls 	= $('.qty_cls').get(index).value;
		disc_val 	= $('.price_disc').get(index).value;
		if(disc_val.slice(-1)=='%'){
			$('.price_disc').get(index).value = disc_val;
			disc_val = disc_val.replace('%','');
			disc_val = price_cls * (parseFloat(disc_val)/100);
		}
		
		if(disc_val[0]=="$")
		{
			disc_val = disc_val.replace(/^[$]+/,"");			
		}
		
		if(isNaN(price_cls) || price_cls=='')
		{
			price_cls = 0;
			$('.price_cls').get(index).value = price_cls.toFixed(2);
		}

		if(isNaN(disc_val) || disc_val=='')
		{
			disc_val = 0;
			$('.price_disc').get(index).value = disc_val;
		}
		
		if(isNaN(qty_cls) || qty_cls=='')
		{
			qty_cls = 0;
			$('.qty_cls').get(index).value = qty_cls;
		}
		
		price_total	= (price_cls-disc_val)*qty_cls; 
		allowed_total	= (price_cls)*qty_cls; 
		if(!isNaN(price_total)){
       	 $('.price_total').get(index).value = price_total.toFixed(2);
		 $('.allowed_cls').get(index).value = allowed_total.toFixed(2);
		 /*allowed_total*/
		}
		
		grand_price = grand_price + price_cls;
		grand_disc = parseFloat(parseFloat(grand_disc) + parseFloat(disc_val));
		grand_qty = parseFloat(parseFloat(grand_qty) + parseFloat(qty_cls));
		grand_total = parseFloat(grand_total) + parseFloat(price_total);
    });
	if(!isNaN(grand_price)){
		$('#item_lens_grand_price_lensD').val(grand_price.toFixed(2));
	}else{
		grand_price=0;
	}
	if(!isNaN(grand_disc)){
		$('#item_lens_grand_disc_lensD').val(grand_disc.toFixed(2));
	}else{
		grand_disc=0;
	}
	if(!isNaN(grand_qty)){
		$('#item_lens_grand_qty_lensD').val(grand_qty);
	}else{
		grand_qty=0;
	}
	$('#item_lens_grand_total_lensD').val(grand_total.toFixed(2));
	GDTChange();
	calculate_all_Grand_POS();
}
function GDTChange(){
	var cost = (parseFloat($("#dispPriceF").val())+parseFloat($("#item_lens_grand_price_lensD").val()));
	if(!isNaN(cost)){
		$("#item_lens_grand_price_lensD").val(cost.toFixed(2));
	}
	
	var disc = (parseFloat($("#dispDiscF").val())+parseFloat($("#item_lens_grand_disc_lensD").val()));
	if(!isNaN(disc)){
		$("#item_lens_grand_disc_lensD").val(disc.toFixed(2));
	}
	
	var qty = (parseFloat($("#dispQtyF").val())+parseFloat($("#item_lens_grand_qty_lensD").val()));
	if(!isNaN(qty)){
		$("#item_lens_grand_qty_lensD").val(qty);
	}
	
	var gTotal = (parseFloat($("#dispTotalF").val())+parseFloat($("#item_lens_grand_total_lensD").val()));
	if(!isNaN(gTotal)){
		$("#item_lens_grand_total_lensD").val(gTotal.toFixed(2));
	}
}

function del_rec_fun(order_detail_id){
	$("#frm_method").val('cancel');
	$("#cancel_order_detail_id").val(order_detail_id);
	document.getElementById("other_selection_form").submit();
}

function stock_search(type){
	var module_typePatval = document.getElementById('module_typePat').value;
	var otherTempPage = document.getElementById('otherTempPage').value;
	top.WindowDialog.closeAll();
	var win=top.WindowDialog.open('Add_new_popup','../admin/stock_search.php?srch_id='+type+'&module_typePat='+module_typePatval+'&otherTempPage='+otherTempPage,'location_popup','width=1237,height=500,left=180,scrollbars=no,top=150');
	win.focus();
}

function otherpage_title(pr_cnt)
{
	var oth_price = $("#price_"+pr_cnt).val();
	var oth_dis = $("#discount_"+pr_cnt).val();
	var oth_lqty = $("#qty_"+pr_cnt).val();
	var oth_rqty = $("#qty_right_"+pr_cnt).val();
	if(isNaN(oth_lqty) || oth_lqty==''){
		oth_lqty = 0;
	}
	if(isNaN(oth_rqty) || oth_rqty==''){
		oth_rqty = 0;
	}
	var oth_qty = parseInt(oth_lqty) + parseInt(oth_rqty);
	var title_price = cal_discount(oth_price,oth_dis);
	if(isNaN(title_price) || title_price==''){
		title_price = 0;
	}
	$("#total_amount_"+pr_cnt).prop('title',title_price.toFixed(2)+' * '+oth_qty);
}

<?php if($stringAllProcedures!=""){	?>
	//var customarrayProcedure= new Array(<?php //echo remLineBrk($stringAllProcedures); ?>);
<?php } ?>

<?php if($stringAllProcedures_dx!=""){	?>
	//var customarrayProcedure_dx= new Array(<?php //echo remLineBrk($stringAllProcedures_dx); ?>);
<?php } ?>

<?php if($AllUpcArray!=""){?>
	//var custom_array_upc= new Array(<?php //echo remLineBrk($AllUpcArray); ?>);
<?php } ?>

<?php if($AllNameArray!=""){?>
	//var custom_array_name= new Array(<?php //echo remLineBrk($AllNameArray); ?>);
<?php } ?>

var custom_array_upc_id;
<?php if($AllUpcIdArray!="" && count($AllUpcIdArrays)>1){?>
	//custom_array_upc_id= new Array(<?php //echo remLineBrk($AllUpcIdArray); ?>);
<?php } else{ ?>
	//custom_array_upc_id= new Array('<?php //echo $AllUpcIdArray; ?>');
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


function frm_sub_fun_callBack(result)
{
	if(result==false)
	{
		return false;	
	}
	else
	{
		return true;
	}
}



function reduce_qty_chk(act, alert_flag, action){
	
	var confirm_msg = "Do you want to reduce quantity from Inventory for:<br /><br />";
	var stop_alert="Unable to reduce qty for:<br /><br />";
	var use_on_hand = $('input[id^="pos_qty_on_hand_"]');
	var valid_order=true;
	var confirmFlag = false;
	 
	$.each(use_on_hand, function(index, obj){
		id_index = $.trim($(obj).attr('id'));		
		id_index = id_index.substr(id_index.lastIndexOf('_')+1);

		/*var fac_qty=($("#fqoh_"+id_index).text());
		var ordered_qty=($("#qty_"+id_index).val());*/
		
		qty_reduced = parseInt($("input#qty_reduced_"+id_index).val());
		
		item_id = $("input#pos_item_id_"+id_index).val();
		item_in_hand = $("input#pos_qty_on_hand_"+id_index).val();
		item_in_stock = $("input#pos_stock_"+id_index).val();
		
		flag = (item_id==="")?false:true;
		upc_code_frame = $("#pos_upc_name_"+id_index).val();
		item_name_frame = $("#pos_item_prac_code_"+id_index).val();
		
		if(item_in_hand!="" && flag && !alert_flag &&  qty_reduced!==1 && item_in_stock!=""){
			confirmFlag = true;
			upc_code_lens = item_name_lens = "";
			// if($("#upc_id_"+id_index+"_lensD").val()!=""){
			// 	upc_code_lens = $("#upc_name_"+id_index+"_lensD").val();
			// 	item_name_lens = $("#item_name_"+id_index+"_lensD").val();
			// }
			confirm_msg += '<label style="margin-bottom:5px;display:block;">';
			confirm_msg += '<input style="vertical-align:middle;margin:0;" type="checkbox" id="qty_reduce_confirm_'+id_index+'" value="'+id_index+'" />&nbsp;';
			confirm_msg += '<strong>'+upc_code_frame+'</strong> - '+item_name_frame;
			// if(upc_code_lens!="" || item_name_lens!="")
			// confirm_msg += '&nbsp;&nbsp;& <strong>'+upc_code_lens+'</strong> - '+item_name_lens;
			// confirm_msg += '</label>';
			
		}
	});
	
	if(typeof(act)!=="undefined"){		
		if(act===true){
			
			var modal_window = top.$('#modal-window');
			var item_sel_check = $(modal_window).find('input[id^="qty_reduce_confirm_"]');			
			$.each(item_sel_check, function(index, obj){
				var index = $(obj).val();				
				if($(obj).is(":checked")){
					$("#reduce_qty_"+index).val(true);
					var fac_qty=($("#pos_stock_"+index).text());
					var ordered_qty=($("#pos_qty_"+index).val());
					var upc_code_frame = $("#pos_upc_name_"+index).val();
					var item_name_frame = $("#pos_item_prac_code_"+index).val();
					if(typeof(ADJACENT_QTY_DEDUCTION)!=="undefined" && ADJACENT_QTY_DEDUCTION==false){
						if(fac_qty<ordered_qty)
						{
							valid_order=false;
							stop_alert+='<strong>'+upc_code_frame+'</strong> - '+item_name_frame+' due to insufficent qty at this facility';
							top.falert(stop_alert);
							return false;
						}
					}
				}
				else
				{
					$("#reduce_qty_"+index).val(false);
				}
			});
		}
		frm_sub_fun(action, true);
		return; 
	}
	if(confirmFlag){
		top.fconfirm(confirm_msg, reduce_qty_chk, true, action);
		return false;
	}else {
		return true;
	}
}




function frm_sub_fun(action,confirm_qty_reduce)
	{


	$.each($('input[class^="qty_cls"]'), function(index, obj){
		var id = $(obj).attr('id').replace("pos_qty_","");
		$('#qty_'+id).val($('#pos_qty_'+id).val());
		//console.log($('#qty_'+id).val());
	});	

	confirm_qty_reduce = (typeof(confirm_qty_reduce)==="undefined")?false:true;
	if(!confirm_qty_reduce && !reduce_qty_chk(undefined, false, action))
	{
		return false;	
	}

	
	var prc = "";
	if(action=="cancel")
	{
		parent.parent.document.getElementById('main_iframe').src='interface/patient_interface/index.php';
		return;
		/*var conf = confirm('Are you sure to cancel this Order ?');
			if(conf!=true)
			{
				return false;
			}*/
	}
	var chk_sub=1;
	if(action=="save" || action=="order_post" || action=="dispensed_post")
	{
		var dis=0;
		$('.price_disc').each(function(index, element) {
			dis = $('.price_disc').get(index).value;
			if(dis.slice(-1)=='%'){
				dis = dis.replace('%','');
			}
			if(dis[0]=="$")
			{
				dis = dis.replace(/^[$]+/,"");			
			}
			if(dis>0)
			{
				var dis_code = $(".disc_code").get(index).value;
				if(dis_code=="")
				{
					top.falert("Please Select Discount Code");
					chk_sub=0;
					return false;
				}
			}
		});
	}
	
	if((action=="save" || action=="order_post" || action=="dispensed_post")  && chk_sub==1)
	{
		var countz = "";
		var jsonObj=[];
		var pr_cd=0;

		$(".itemname").each(function( index )
		{	
			countz=index+1;
			$(this).addClass("itemnameClass"+countz);
		});
		countz="";

		/*$(".pracodefield").each(function( index )
		{
			if($.trim($(this).val())=="")
			{
				countz = index+1;
				var tax_alt = 0;
				
				if($(".itemnameClass"+countz).val()=="Taxes" && $(".price_cls").get(index).value==0)
				{
					tax_alt = 1;
				}
				if(tax_alt==0)
				{
					prc +=index;
					top.falert("Please Select Prac Code of '"+$(".itemnameClass"+countz).val()+"'" );
					countz='';
					$(this).focus();
					pr_cd=1;
					return false;
				}
			}
		});*/
	}
	if(action=="order_post" && pr_cd==0 && chk_sub==1)
	{
		var prac_alrt_msg="";
		var posRow = $("table.posTable tr:not(.hideRow1, .hideRow)");
		$.each(posRow, function(i, obj){
			var pracField = $(obj).find(".pracodefield").val();
			var itemName = $(obj).find(".itemname").val();
			if(typeof(itemName)!="undefined" && (typeof(pracField)=="undefined" || pracField=="")){
				prac_alrt_msg += 'Please select Prac Code for item "'+itemName+'"<br>';
			}
		});
		
		if(prac_alrt_msg!=""){
			top.falert(prac_alrt_msg);
			return false;
		}
		$("#frm_method").val(action);
		document.getElementById("other_selection_form").submit();
	}
	if((action=="save") && prc=="" && chk_sub==1)
	{
		//if($('#other_upc_name').val()=="")
//		{
//			alert("Please enter UPC Code");
//		}
//		else
//		{
			$("#frm_method").val(action);
			document.getElementById("other_selection_form").submit();
		//}
	}
	if(action=="dispensed_post" && pr_cd==0  && chk_sub==1)
	{
		/*$.confirm({
			'message'	: 'Do you want to reduce Qty from stock?',
			'buttons'	: {
				'Yes'	: {
					'class'	: 'blue',
					'action': function(){
						$("#reduc_stock").val('yes');
						$("#frm_method").val(action);
						document.other_selection_form.submit();
					}
				},
				'No'	: {
					'class'	: 'gray',
					'action': function(){
						$("#reduc_stock").val('no');
						$("#frm_method").val(action);
						document.other_selection_form.submit();
					}
				}
			}
		});*/
		if($('#other_upc_name').val()=="")
		{
			top.falert("Please enter UPC Code");
		}
		else
		{
			$("#reduc_stock").val('yes');
			$("#frm_method").val(action);
			document.getElementById("other_selection_form").submit();
		}
	}
	if(action=='new_form')
	{
		$("#frm_method").val(action);
		document.contact_frm.submit();
	}
	
}
</script>
<style>
	#tat_table { top:65px !important; }
	table td{ border-collapse:collapse;}
</style>
</head>
<body>
<?php
	$date_ord_qry=imw_query("select DATE_FORMAT(entered_date, '%m-%d-%Y') AS 'created_date' from in_order where id='".$order_id."' and id>0");
	$date_ord_res=imw_fetch_assoc($date_ord_qry);
?>
<form action="other_selection.php" name="other_selection_form" id="other_selection_form" method="post" enctype="multipart/form-data">
<div class="listheading mt10">
	<div style="width:150px; float:left;">Other Selection</div>
	<?php if($order_id!="" || $order_id>0) { ?>
	<div style="width:100px; float:left;">Order #<?php echo $order_id; ?></div>
	<div style="width:100px; float:left;"><?php echo $date_ord_res['created_date']; ?></div>
	<?php } else{?>
	<div style="float:left; width: 100px"><input type="text"  class="date-pick" name="order_date" id="order_date" style="height: 21px; background-size:17px 21px;width: 95px;" value="<?php echo date("m-d-Y"); ?>" autocomplete="off" /></div>
	<?php }?>
	<div style="float:right;">
	<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('medician_module_id').value);">
    <img style="margin-top:1px; margin-bottom:-5px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock" ></a>
    </div>
</div>
<div style="height:<?php echo $_SESSION['wn_height']-460;?>px; float:left; width:100%;">
	<input type="hidden" name="frm_method" id="frm_method" value="">
	<input type="hidden" name="cancel_order_detail_id" id="cancel_order_detail_id" value="">
    <input type="hidden" name="module_typePat" id="module_typePat" value="patient_interPage">
    <input type="hidden" name="medician_module_id" id="medician_module_id" value="6">
    <input type="hidden" name="otherTempPage" id="otherTempPage" value="other_selPage">
	<input type="hidden" name="upc_id_1" id="upc_id_1" value="">
    <table width="100%" class="table_collapse table_cell_padd5">
        <tr>
            <td valign="top" align="left" style="vertical-align: top;width:40px;">
				<label for="other_upc_name">UPC</label>
			</td>
			<td valign="top" align="left" style="vertical-align: top;width:170px;">
				<input type="text" name="other_upc_name" id="other_upc_name" onChange="get_upcbyid(document.getElementById('upc_id_1'));"  value="<?php echo $_REQUEST['upc_name'];?>" autocomplete="off" />
			</td> 
			<td valign="top" align="left" style="vertical-align: top;width:85px">
				<label for="other_name">Item Name</label>
			</td>
            <td valign="top" align="left" style="vertical-align: top;width:795px;">
				<input type="text" name="other_name" id="other_name" onChange="get_upcbyid(document.getElementById('upc_id_1'));"  value="<?php echo $_REQUEST['item_name'];?>" autocomplete="off" />            	&nbsp;&nbsp;&nbsp;<img src="../../images/add_btn.png" alt="add" onClick="addrow();"  style="margin-bottom:-5px; cursor:pointer;" />
            </td>                          
        </tr>
	</table>
<div style="height:<?php echo $_SESSION['wn_height']-425;?>px; overflow-x:hidden; overflow-y:scroll;">
	<?php 
    	require_once('pt_pos.php');
    ?>
</div>
   <input type="hidden" name="last_cont" id="last_cont" value="<?php echo $pro_cont; ?>" />
</div>
</form>
<script type="text/javascript">
	var last_cnt = document.getElementById("last_cont").value;
	//var obj7 = new actb(document.getElementById('other_upc_name'),custom_array_upc,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	
	$("#other_upc_name").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'mixedData',
			hidIDelem: document.getElementById('upc_id_1'),
			minLength:1,
			maxVals: 10,
			bgColor: "#888888",
			hoverBgColor: "#000000",
			textColor: "#FFFFFF",
			fontSize: "11px",
			showAjaxVals: 'upc'
		});
		
	for(var i=1;i<=last_cnt;i++)
	{
		//var obj8 = new actb(document.getElementById('item_prac_code_'+i),customarrayProcedure);
		//var obj9 = new actb(document.getElementById('dx_code_'+i),customarrayProcedure_dx);
	}
	//var obj6 = new actb(document.getElementById('other_name'),custom_array_name,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	$("#other_name").ajaxTypeahead({
			url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
			type: 'mixedData',
			hidIDelem: document.getElementById('upc_id_1'),
			minLength:1,
			maxVals: 10,
			bgColor: "#888888",
			hoverBgColor: "#000000",
			textColor: "#FFFFFF",
			fontSize: "11px",
			showAjaxVals: 'name'
		});
function prac_code_typeahead(){
	$(".posTable .pracodefield").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeO'
	});
	$(".posTable .pracodefield").attr('autocomplete', 'off');
}

$(document).ready(function(e) {
/*PreSelect VisionPlan*/
<?php if($order_id==''): ?>
$('#main_ins_case_id_1').val(top.main_iframe.ptVisionPlanId);
<?php endif; ?>

	//BUTTONS
	var mainBtnArr=[];
	btnCount = 0;
<?php if($order_edit_btn_status): ?>
	mainBtnArr[btnCount] = new Array("frame","Cancel","top.main_iframe.admin_iframe.frm_sub_fun('cancel')");
	btnCount++;
<?php endif; ?>
	mainBtnArr[btnCount] = new Array("frame","On Hold","");
	btnCount++;
<?php if($order_edit_btn_status): ?>
	mainBtnArr[btnCount] = new Array("frame","Save","top.main_iframe.admin_iframe.frm_sub_fun('save')");
	btnCount++;
<?php endif; ?>
<?php if($order_post_btn_status): ?>
	mainBtnArr[btnCount] = new Array("frame","Post","top.main_iframe.admin_iframe.frm_sub_fun('order_post')");
	btnCount++;
<?php endif; ?>
	mainBtnArr[btnCount] = new Array("frame","Print Order","top.main_iframe.admin_iframe.printpos('<?php echo $order_id;?>','Other Selection')");	
	btnCount++;
	mainBtnArr[btnCount] = new Array("frame","Patient Receipt","top.main_iframe.admin_iframe.patientReceipt('<?php echo $order_id; ?>','Other Selection')");
	btnCount++;
	top.btn_show("admin",mainBtnArr);		
});
	
</script>
</body>
</html>