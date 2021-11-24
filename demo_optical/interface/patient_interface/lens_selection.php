<?php 
/*
File: lens_selection.php
Coded in PHP7
Purpose: Show Lens information
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");
$saved='0';
$order_id=$_SESSION['order_id'];
$patient_id=$_SESSION['patient_session_id'];
function prismNumbers($selected=''){
	$optValues='';
	for($i=1; $i<=15;){
		$sel=($i==$selected)? 'selected': '';
		$optValues.='<option value="'.$i.'" '.$sel.'>'.$i.'</option>';

		if($i>=8){
			$i+=0.25;
		}else{
			$i+=0.5;
		}
	}
	return $optValues;
}

$action=$_REQUEST['frm_method'];
$order_detail_id=$_REQUEST['order_detail_id'];

if($action=="next"){
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='patient_pos.php'</script>";
}
elseif($action=="save"){
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='lens_selection.php'</script>";
}
else if($action=="previous"){
	echo "<script type='text/javascript'>window.location.href='pt_frame_selection.php'</script>";
}else if($action=="cancel"){
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='lens_selection.php'</script>";
}


$stringAllUpc = get_upc_name_id('2');

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
		$arrSubOptions = array();
		if(imw_num_rows($rezCodes) > 0){
			while($rowCodes=imw_fetch_array($rezCodes)){
				$arrSubOptions[] = array($rowCodes["d_prac_code"]."-".$rowCodes["diag_description"],$xyz, $rowCodes["d_prac_code"]);
				$arrCptCodesAndDesc[] = $rowCodes["diagnosis_id"];
				$arrCptCodesAndDesc[] = $rowCodes["d_prac_code"];
				$arrCptCodesAndDesc[] = $rowCodes["diag_description"];
				
				$code = str_replace(";","~~~",$rowCodes["d_prac_code"]);
				$cpt_desc = str_replace(";","~~~",$rowCodes["diag_description"]);
				$stringAllProcedures.="'".str_replace("'","",$code)."',";	
				$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
				$dx_code_arr[$rowCodes["diagnosis_id"]]=$rowCodes["d_prac_code"];
			}
		$arrCptCodes[] = array($row["category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Dx Code ------------------------//

//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
	$proc_code_desc_arr=array();
	$sql_pr = "select * from cpt_category_tbl order by cpt_category ASC";
	$rez_pr = imw_query($sql_pr);	
	while($row_pr=imw_fetch_array($rez_pr)){
		$cat_id_pr = $row_pr["cpt_cat_id"];		
		$sql_pr = "select * from cpt_fee_tbl WHERE cpt_cat_id='".$cat_id_pr."' AND status='active' AND delete_status = '0' order by cpt_prac_code ASC";
		$rezCodes_pr = imw_query($sql_pr);
		$arrSubOptions_pr = array();
		if(imw_num_rows($rezCodes_pr) > 0){
			while($rowCodes_pr=imw_fetch_array($rezCodes_pr)){
				$arrSubOptions_pr[] = array($rowCodes_pr["cpt_prac_code"]."-".$rowCodes_pr["cpt_desc"],$xyz, $rowCodes_pr["cpt_prac_code"]);
				$arrCptCodesAndDesc_pr[] = $rowCodes_pr["cpt_fee_id"];
				$arrCptCodesAndDesc_pr[] = $rowCodes_pr["cpt_prac_code"];
				$arrCptCodesAndDesc_pr[] = $rowCodes_pr["cpt_desc"];
				
				$code_pr = $rowCodes_pr["cpt_prac_code"];
				$cpt_desc_pr = $rowCodes_pr["cpt_desc"];
				$stringAllProcedures_pr.="'".str_replace("'","",$code_pr)."',";	
				$stringAllProcedures_pr.="'".str_replace("'","",$cpt_desc_pr)."',";
				$proc_code_arr[$rowCodes_pr["cpt_fee_id"]]=$rowCodes_pr["cpt_prac_code"];
				$proc_code_desc_arr[$rowCodes_pr["cpt_fee_id"]]=$rowCodes_pr["cpt_desc"];
			}
		$arrCptCodes_pr[] = array($row_pr["cpt_category"],$arrSubOptions_pr);
		}		
	}

	$stringAllProcedures_pr = substr($stringAllProcedures_pr,0,-1);
	
//---------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures ------------------------//
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
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.widget.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>

<script type="text/javascript">
$(function()
{
	$("#lens_last_exam_1").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
				
	last_exam_d=function()
	{
		if($("#isRXLoaded").val()==1)
		{
			$(".ui-datepicker").hide();
			$("#lens_last_exam_1").datepicker( "option", "readonly", "readonly");
		}
	}
});
</script>


<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<script type="text/javascript">
$(document).ready(function() 
{	

	itemdropdown = function(lens_sel_id)
	{
		var ov = "";
		ov = "&type="+$("#type_id_1").val()+"&progressive="+$("#progressive_id_1").val()+"&material="+$("#material_id_1").val()+"&transition="+$("#transition_id_1").val()+"&ar="+$("#a_r_id_1").val()+"&tint="+$("#tint_id_1").val()+"&polarized="+$("#polarized_id_1").val()+"&edge="+$("#edge_id_1").val()+"&color="+$("#color_id_1").val();

		var dataStringItems = 'action=lens_selection'+ov+'&lens_sel_id='+lens_sel_id;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: dataStringItems,
			cache: false,
			success: function(response)
			{
			    $("#item_lens_selections_1 option").remove();
			    $("#item_lens_selections_1").append(response);
			}
		});					
	}
	//itemdropdown();
});
</script>

<script type="text/javascript">
$(document).ready(function(e) {
	calculate_all();
	$( ".price_cls,.price_disc,.qty_cls" ).change(function() {
		calculate_all();
	});
});

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
		
		//disc_val = parseInt(disc_val);
		
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
		 $('.allowed_total').get(index).value = allowed_total.toFixed(2);
		}
		
		grand_price = grand_price + price_cls;
		grand_disc = parseFloat(parseFloat(grand_disc) + parseFloat(disc_val));
		grand_qty = parseFloat(parseFloat(grand_qty) + parseFloat(qty_cls));
		grand_total = parseFloat(grand_total) + parseFloat(price_total);
    });
	if(!isNaN(grand_price)){
		$('#item_lens_grand_price').val(grand_price.toFixed(2));
	}else{
		grand_price=0;
	}
	if(!isNaN(grand_disc)){
		$('#item_lens_grand_disc').val(grand_disc.toFixed(2));
	}else{
		grand_disc=0;
	}
	if(!isNaN(grand_qty)){
		$('#item_lens_grand_qty').val(grand_qty);
	}else{
		grand_qty=0;
	}
	//grand_total = (grand_price-grand_disc);
	$('#item_lens_grand_total').val(grand_total.toFixed(2));
}
function prescription_details(){
	var winTop='<?php echo $_SESSION['wn_height']-650;?>';
	top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/lens_prescriptions.php','lens_prescription_pop','width=800,height=340,left=600,scrollbars=no,left=10,top='+winTop);
	win1.focus();
}

<?php if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php } ?>

<?php if($stringAllProcedures_pr!=""){	?>
	var customarrayProcedure_pr= new Array(<?php echo remLineBrk($stringAllProcedures_pr); ?>);
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
</script>

<script>

var lens_frame_id='';
var type_id='';
var progressive_id='';
var material_id='';
var transition_id='';
var a_r_id='';
var tint_id='';
var polarized_id='';
var edge_id='';
var color_id='';
var item_lens_selections='';
var other='';

var dataString = "";
var itemID = "";

autofillupc = function()
{
	//alert($("#item_id_1").val());
	if($("#item_id_1").val()=="" && $("#color_id_1").val()>0){
		
		type_id = $("#type_id_1");
		progressive_id = $("#progressive_id_1");
		material_id = $("#material_id_1");
		transition_id = $("#transition_id_1");
		a_r_id = $("#a_r_id_1");
		tint_id = $("#tint_id_1");
		polarized_id = $("#polarized_id_1");
		edge_id = $("#edge_id_1");
		color_id = $("#color_id_1");
		item_lens_selections = $("#item_lens_selections_1");
		other = $("#other_1");
		
	
			dataString = 'action=findLenseUpc&type_id='+type_id.val()+'&progressive_id='+progressive_id.val()+'&material_id='+material_id.val()+'&transition_id='+transition_id.val()+'&a_r_id='+a_r_id.val()+'&tint_id='+tint_id.val()+'&polarized_id='+polarized_id.val()+'&edge_id='+edge_id.val()+'&color_id='+color_id.val();
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: dataString,
				cache: false,
				success: function(responseData)
				{
					if(responseData=="false")
					{
					}
					else
					{
						var dataArr = $.parseJSON(responseData);
						if(dataArr!="")
						{
							$.each(dataArr, function(index,value) 
							{
								
								if(dataArr.length==1){ get_details_by_upc(value);	}
								else{
								stock_search(document.getElementById('module_type_id_1').value,'lenses');
								}
							});
						}
					}
				}
			});
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
<script>
function submitForm(){
	document.lens_selection_form.submit();
}

function frm_sub_fun(action){
var isdis_code=0;
	if(action=="cancel")
	{
		var conf = confirm('Are you sure to cancel this Order ?');
		if(conf!=true)
		{
			return false;
		}
	}
	
	if(action=="save" || action=="next")
	{
		var dis=dis_code=alldis=0;
		$('.disc_code').each(function(index, element) {
			dis = $('.disc_code').get(index).value;
			dis_code = $('.discount_code').get(index).value;
			if(dis.slice(-1)=='%'){
				dis = dis.replace('%','');
			}
			if(dis[0]=="$")
			{
				dis = dis.replace(/^[$]+/,"");			
			}
			if(dis>0)
			{
				if(dis_code=="")
				{
					top.falert("Please Select Discount Code");
					isdis_code=1;
					return false;
				}
			}
		});
	}
	if(isdis_code==0)
	{
		$("#frm_method").val(action);
		document.lens_selection_form.submit();
	}
}

function old_order_detail(val){
	window.location.href='lens_selection.php?order_detail_id='+val;
}

function apply_dis_all(discnt)
{
	var dis=discount=0;
	var cnts=0;
	$('.disc_code').each(function(index, element) {
		dis = $('.disc_code').get(index).value;
		if(dis.slice(-1)=='%'){
			dis = dis.replace('%','');
		}
		if(dis[0]=="$")
		{
			dis = dis.replace(/^[$]+/,"");			
		}
		discount += parseFloat(dis);
	});
	if(discount>0)
	{
		var conf = confirm('This will overwrite all other discounts at Item(s)');
		if(conf==true)
		{
			cnts = 1;
		}
		else
		{
			$(discnt).val('');
		}
	}
	else
	{
		cnts = 1;
	}
	if(cnts==1)
	{
		$('.disc_code').each(function(index, element) {
			if($('.dis_class').get(index).style.display != 'none')
			{
				$('.disc_code').get(index).value = discnt.value;
			}
		});
		calculate_all();
	}
}

function stock_search(type,fromVal){
	var module_typePatval = document.getElementById('module_typePat').value;
	if(document.getElementById('order_detail_id_1').value>0){
		var order_detail_id_1 = document.getElementById('order_detail_id_1').value;
	}
	else{
		var order_detail_id_1 = 'new_form';
	}
	var lens_type = document.getElementById('type_id_1').value;
	var lens_material = document.getElementById('material_id_1').value;
	var lens_air = document.getElementById('a_r_id_1').value;
	var lens_transition = document.getElementById('transition_id_1').value;
	var polarized_name = document.getElementById('polarized_id_1').value;
	var tint_type = document.getElementById('tint_id_1').value;
	var color_name = document.getElementById('color_id_1').value;
	
	var datastring = '&type='+lens_type+'&material='+lens_material+'&air='+lens_air+'&transition='+lens_transition+'&polarized='+polarized_name+'&tint='+tint_type+'&color='+color_name;
	
	top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('Add_new_popup','lens_stock_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&from='+fromVal+datastring,'location_popup','width=1050,height=500,left=180,scrollbars=no,top=150');
	win1.focus();
}

function hx_prescription()
{
	top.WindowDialog.closeAll();
	var win=top.WindowDialog.open('Add_new_popup','hx_lens_prescription.php','location_popup','width=800,height=340,left=180,scrollbars=no,top=150');
	win.focus();
}

function show_progressive_dropdown(vall)
{
	var type = $(vall).find("option:selected").text();
	var str = type.toLowerCase();
	if(str.match("progressive"))
	{
		$("#progressive_id_1").removeAttr('disabled');
	}
	else
	{
		$("#progressive_id_1").val('0');
		$("#progressive_id_1").prop('disabled','disabled');
		itemized_row_display($("#progressive_id_1").val(),'progressive_display','in_lens_progressive');
	}
}

function change_pos()
{
	$("#tat_table").css("top","108px !important");
}

function frame_title(price,dis,qty,title_id)
{
	if(isNaN(price) || price==''){
		price = 0;
	}
	if(isNaN(dis) || dis==''){
		dis = 0;
	}
	var title_price = cal_discount(price,dis);
	$("#"+title_id).prop('title',title_price.toFixed(2)+' * '+qty);
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
if($_SESSION['order_id']>0 && $action!="new_form")
{
	if($action!="" && $action>0){
		$whr=" and id='$action'";
		$lens_pres_whr = " and det_order_id='$order_detail_id'";
		$price_whr = " and order_detail_id='$order_detail_id'";
	}else{
		if($order_detail_id>0){
			$whr=" and id='$order_detail_id'";
			$lens_pres_whr = " and det_order_id='$order_detail_id'";
			$price_whr = " and order_detail_id='$order_detail_id'";
		}
	}
	$sel_qry=imw_query("select * from in_order_details where order_id ='$order_id' $whr and patient_id='$patient_id' and module_type_id='2' and del_status='0' order by show_default desc");
	$sel_order=imw_fetch_array($sel_qry);
	$item_id=$sel_order['item_id'];
	$order_detail_id=$sel_order['id'];
	$lens_frame_id=$sel_order['lens_frame_id'];
	$lens_selection_id=$sel_order['lens_selection_id'];
	
	if($order_detail_id>0){
		$whr=" and id='$order_detail_id'";
		$lens_pres_whr = " and det_order_id='$order_detail_id'";
		$price_whr = " and order_detail_id='$order_detail_id'";
	}
		
			
	$lensRs=imw_query("Select in_optical_order_form.*, users.fname, users.lname FROM in_optical_order_form LEFT JOIN users ON users.id=in_optical_order_form.physician_id WHERE order_id='".$order_id."' $lens_pres_whr AND patient_id='$patient_id' AND del_status='0'");
	$lensRes=imw_fetch_array($lensRs);
	
	if($lensRes['physician_name']=="")
	{
		if(($lensRes['fname']!='' || $lensRes['lname']!='') && $lensRes['physician_id']>0)
		{
			$phyName=$lensRes['lname'].', '.$lensRes['fname'];
		}
	}
	else
	{
			$phyName=$lensRes['physician_name'];
	}
	
	 $frame_id=$_REQUEST['frame_id'];
	
	if($lens_frame_id>0)
	{
		$frame_whr = "and id='$lens_frame_id'";
	}
	elseif(isset($frame_id) && $frame_id>0)
	{
		$frame_whr = "and id='$frame_id'";
	}
	$sel_frame_qry=imw_query("select id as frame_order_id,item_name as frame_name,item_id as frame_item_id,price as frame_price, qty as frame_qty,discount as frame_discount,total_amount as frame_total_amount,item_prac_code as frame_item_prac_code from in_order_details where order_id ='$order_id' $frame_whr and patient_id='$patient_id' and module_type_id='1' and del_status='0'");
	$sel_frame_order=imw_fetch_array($sel_frame_qry);
	
	$sel_price_qry=imw_query("select * from in_order_item_price_details where order_id ='$order_id' $price_whr and patient_id='$patient_id' and del_status='0'");
	$sel_price_order=imw_fetch_array($sel_price_qry);
	
	$price_order_id=$sel_price_order['id'];
	$lens_price=$sel_price_order['lens_wholesale'];
	$material_price=$sel_price_order['material_wholesale'];
	$a_r_price=$sel_price_order['a_r_wholesale'];
	$transition_price=$sel_price_order['transition_wholesale'];
	$polarization_price=$sel_price_order['polarization_wholesale'];
	$tint_price=$sel_price_order['tint_wholesale'];
	$uv400_price=$sel_price_order['uv400_wholesale'];
	$other_price=$sel_price_order['other_wholesale'];
	
	$lens_discount=$sel_price_order['lens_discount'];
	$material_discount=$sel_price_order['material_discount'];
	$a_r_discount=$sel_price_order['a_r_discount'];
	$transition_discount=$sel_price_order['transition_discount'];
	$polarization_discount=$sel_price_order['polarization_discount'];
	$tint_discount=$sel_price_order['tint_discount'];
	$uv400_discount=$sel_price_order['uv400_discount'];
	$other_discount=$sel_price_order['other_discount'];	
	
	if($order_detail_id>0)
	{
		$sel_lens_price_qry=imw_query("select orlp.id as lens_price_id, orlp.* from in_order_lens_price_detail as orlp  where order_id ='$order_id' and order_detail_id='$order_detail_id' and patient_id='$patient_id' and del_status='0'");
	}
	else
	{
		$sel_lens_price_qry = imw_query("select id as itemized_id, lens_item_name as itemized_name from in_lens_items_detail");
	}
}
else
{
	$sel_lens_price_qry = imw_query("select id as itemized_id, lens_item_name as itemized_name from in_lens_items_detail");
}
$lens_item_count = imw_num_rows($sel_lens_price_qry);
while($sel_lens_price_data=imw_fetch_array($sel_lens_price_qry))
{
	$lens_price_data[] = $sel_lens_price_data;
}

if($order_detail_id>0 && $order_detail_id!="") { ?>
<style>
	#tat_table { top:164px !important; }
</style>
<?php } 
elseif($_SESSION['order_id']>0 && $action=="new_form")
{ ?>
<style>
	#tat_table { top:164px !important; }
</style>
<?php } else { ?>
<style>
	#tat_table { top:65px !important; }
</style>
<?php } ?>
<div class="listheading mt10">
	<div style="float:left;">
		<div style="width:<?php if($order_id!=""){ echo "480"; } else { echo "1050"; } ?>px; float:left;">Lens Selection</div>
		<?php if($order_id!="" || $order_id>0) { ?>
		<div style="width:565px; float:left;">Order #<?php echo $order_id; ?></div>
		<?php } ?>
		<div style="float:left;">
		<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type_id_1').value,'');">
        	<img style="margin-top:1px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock"/> 
		</a>
		<a style="margin-left:15px;" href="javascript:void(0);" onClick="javascript:hx_prescription();">
        	<img style="margin-top:1px; width:25px;" src="../../images/hx_icon.png" border="0" class="serch_icon_stock" title="Hx Lens Prescriptions"/>
		</a>
       </div>
	</div>
</div>
<form action="lens_selection.php" name="lens_selection_form" method="post" enctype="multipart/form-data">
<input type="hidden" name="frm_method" id="frm_method" value="">
<input type="hidden" name="order_detail_id_1" id="order_detail_id_1" value="<?php echo $sel_order['id']; ?>">
<input type="hidden" name="item_id_1" id="item_id_1" value="<?php echo $sel_order['item_id']; ?>">
<input type="hidden" name="lens_prescription_count[]" >
<input type="hidden" name="order_rx_lens_id_1" id="order_rx_lens_id_1" value="<?php echo $lensRes['id'];?>">
<input type="hidden" name="isRXLoaded" id="isRXLoaded" value="<?php echo $lensRes['id'];?>">
<input type="hidden" name="module_type_id_1" id="module_type_id_1" value="2">
<input type="hidden" name="module_typePat" id="module_typePat" value="patient_interPage">
<input type="hidden" name="page_name" value="lens_selection" />  
<input type="hidden" name="upc_id_1" id="upc_id_1" value="">
<input type="hidden" value="<?php if(isset($sel_order['qty'])){ echo $sel_order['qty']; } else { echo "1"; } ?>" name="qty_1">

<div style="height:<?php echo $_SESSION['wn_height']-450;?>px; overflow-y:auto; float:left;">
   <?php if($order_id>0){ 
	$other_orders_module="2";
	$img_path = "lense_stock/";	
	require_once("other_orders.php");
} ?>
      <div class="fr" style="width:730px; margin:7px;">
       	<table class="table_collapse table_cell_padd5 module_border" >
        	<tr>
              <td align="left" width="70"> 
                <a href="javascript:void(0);" class="text_purpule" onClick="javascript:prescription_details();">
                Rx
                </a>
              </td>
			  <td width="300" align="left"> 
				<input type="checkbox" name="lens_outside_rx" id="lens_outside_rx" value="1" <?php if($lensRes['outside_rx']=="1"){echo"checked";} ?>>
				<span>Outside Rx</span>
			  </td>
			  <td width="445" align="left">
				<input type="checkbox" name="lens_neutralize_rx" id="lens_neutralize_rx" value="1" <?php if($lensRes['neutralize_rx']=="1"){echo"checked";} ?> >
				<span>Neutralize</span>
			  </td>
            </tr>
            <tr>
              <td align="left">
                <span class="blueColor" style="font-weight:bold;">OD&nbsp;</span>SPH
              </td>
              <td colspan="2" align="left" >
                <input type="text" name="lens_sphere_od_1" id="lens_sphere_od_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['sphere_od'];?>">
                &nbsp;CYL&nbsp;&nbsp;&nbsp;<input type="text" name="lens_cylinder_od_1" id="lens_cylinder_od_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['cyl_od'];?>">
              	&nbsp;Axis&nbsp;&nbsp;&nbsp;<input type="text" name="lens_axis_od_1" id="lens_axis_od_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['axis_od'];?>">
              	&nbsp;Add&nbsp;&nbsp;&nbsp;<input type="text" name="lens_add_od_1" id="lens_add_od_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['add_od'];?>">
              </td>
             </tr>
            <tr>
              <td align="left">
                <span class="greenColor" style="font-weight:bold;">OS&nbsp;</span>SPH
              </td>
              <td colspan="2" align="left"> 
                <input type="text" name="lens_sphere_os_1" id="lens_sphere_os_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['sphere_os'];?>">
                &nbsp;CYL&nbsp;&nbsp;&nbsp;<input type="text" name="lens_cylinder_os_1" id="lens_cylinder_os_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['cyl_os'];?>">
              	&nbsp;Axis&nbsp;&nbsp;&nbsp;<input type="text" name="lens_axis_os_1" id="lens_axis_os_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['axis_os'];?>">
				&nbsp;Add&nbsp;&nbsp;&nbsp;<input type="text" name="lens_add_os_1" id="lens_add_os_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['add_os'];?>">
              </td>
              </tr>
            <tr>
              <td align="left"> 
                <span class="blueColor" style="font-weight:bold;">OD&nbsp;</span>Prism
                </td>
              <td colspan="2" align="left"><select name="lens_mr_od_p_1" id="lens_mr_od_p_1" style="width:80px" class="rx_cls" readOnly>
                <option value=""></option>
                <?php echo prismNumbers($lensRes['mr_od_p']); ?>
                </select>
                <select name="lens_mr_od_prism_1" id="lens_mr_od_prism_1" style="width:80px" class="rx_cls" readOnly>
                  <option value=""></option>
                  <option value="BD" <?php if($lensRes['mr_od_prism']=='BD')echo 'selected';?>>BD</option>
                  <option value="BU" <?php if($lensRes['mr_od_prism']=='BU')echo 'selected';?>>BU</option>
                </select>/                            
                <select name="lens_mr_od_splash_1" id="lens_mr_od_splash_1" style="width:80px" class="rx_cls" readOnly>
                  <option value=""></option>
                  <?php echo prismNumbers($lensRes['mr_od_splash']); ?>
                </select>
                <select name="lens_mr_od_sel_1" id="lens_mr_od_sel_1" style="width:80px" class="rx_cls" readOnly value="<?php echo $lensRes['mr_od_sel'];?>">
                  <option value=""></option>
                  <option value="BI" <?php if($lensRes['mr_od_sel']=='BI')echo 'selected';?>>BI</option>
                  <option value="BO" <?php if($lensRes['mr_od_sel']=='BO')echo 'selected';?>>BO</option>
                </select>
                &nbsp;Base&nbsp;&nbsp;&nbsp;<input type="text" name="lens_base_od_1" id="lens_base_od_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['base_od'];?>">
                &nbsp;Seg&nbsp;&nbsp;&nbsp;<input type="text" name="lens_seg_od_1" id="lens_seg_od_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['seg_od'];?>">
                </td>
              </tr>
            <tr>
              <td align="left"> 
                <span class="greenColor" style="font-weight:bold;">OS&nbsp;</span>Prism
                </td>
              <td colspan="2" align="left"><select name="lens_mr_os_p_1" id="lens_mr_os_p_1" style="width:80px" class="rx_cls" readOnly>
                <option value=""></option>
                <?php echo prismNumbers($lensRes['mr_os_p']); ?>
                </select>
                <select name="lens_mr_os_prism_1" id="lens_mr_os_prism_1" style="width:80px" class="rx_cls" readOnly>
                  <option value=""></option>
                  <option value="BD" <?php if($lensRes['mr_os_prism']=='BD')echo 'selected';?>>BD</option>
                  <option value="BU" <?php if($lensRes['mr_os_prism']=='BU')echo 'selected';?>>BU</option>
                </select>/                            
                <select name="lens_mr_os_splash_1" id="lens_mr_os_splash_1" style="width:80px" class="rx_cls" readOnly >
                  <option value=""></option>
                  <?php echo prismNumbers($lensRes['mr_os_splash']); ?>
                </select>
                <select name="lens_mr_os_sel_1" id="lens_mr_os_sel_1" style="width:80px" class="rx_cls" readOnly value="<?php echo $lensRes['axis_os'];?>">
                  <option value=""></option>
                  <option value="BI" <?php if($lensRes['mr_os_sel']=='BI')echo 'selected';?>>BI</option>
                  <option value="BO" <?php if($lensRes['mr_os_sel']=='BO')echo 'selected';?>>BO</option>
                </select>
				&nbsp;Base&nbsp;&nbsp;&nbsp;<input type="text" name="lens_base_os_1" id="lens_base_os_1" style="width:80px;" class="rx_cls" value="<?php echo $lensRes['base_os'];?>">
                &nbsp;Seg&nbsp;&nbsp;&nbsp;<input type="text" name="lens_seg_os_1" id="lens_seg_os_1" class="rx_cls" style="width:80px;" value="<?php echo $lensRes['seg_os'];?>" />
                </td>
              </tr>      
              <tr>
                <td align="left"> 
                   DPD
                  </td>
                <td colspan="2" align="left"> 
				<input type="text" name="lens_dpd_od_1" id="lens_dpd_od_1" style="width:70px;" class="rx_cls"  value="<?php echo $lensRes['dist_pd_od'];?>">&nbsp;/&nbsp;<input type="text" name="lens_dpd_os_1" id="lens_dpd_os_1" style="width:70px;" class="rx_cls" value="<?php echo $lensRes['dist_pd_os'];?>"> &nbsp;NPD&nbsp;&nbsp;&nbsp;<input type="text" name="lens_npd_od_1" id="lens_npd_od_1" style="width:70px;" class="rx_cls" value="<?php echo $lensRes['near_pd_od'];?>">&nbsp;/&nbsp;<input type="text" name="lens_npd_os_1" id="lens_npd_os_1" style="width:70px;" class="rx_cls" value="<?php echo $lensRes['near_pd_os'];?>">
				&nbsp;&nbsp;Last Exam&nbsp;&nbsp;&nbsp;<input type="text" name="lens_last_exam_1" id="lens_last_exam_1" style="width:83px;" class="rx_cls" value="<?php if($lensRes['last_exam']!="0000-00-00" && $lensRes['last_exam']!="") { echo getDateFormat($lensRes['last_exam']); }?>">
                  </td>
                </tr>
				<tr>
              <td align="left"> 
                Doctor
                </td>
              <td colspan="2" align="left"> 
                <input type="hidden" name="lens_physician_id" id="lens_physician_id" value="<?php echo $lensRes['physician_id'];?>">
                <input type="text" name="lens_physician_name" id="lens_physician_name" style="width:130px;" class="rx_cls" value="<?php echo $phyName;?>">
                &nbsp;Tel&nbsp;&nbsp;&nbsp;<input type="text" name="lens_telephone" id="lens_telephone" style="width:130px;" class="rx_cls" value="<?php echo  stripslashes(core_phone_format($lensRes['telephone']));?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');">
				&nbsp;DX Code&nbsp;&nbsp;&nbsp;
				<?php $all_dx_codes="";
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
			  <input type="text" name="dx_code_1" id="dx_code_1" style="width:130px;" class="rx_cls" value="<?php echo $all_dx_codes; ?>" onChange="get_dxcode(this);">
                </td>
              </tr>
          </table>
           <?php
			if($lensRes['outside_rx']=="1" || $lensRes['neutralize_rx']=="1")
			{
		 	?> 
			<!--<script type="text/javascript">activeDeactiveFields();</script>-->
		 <?php } ?>
    </div>
    <div class="fl" style="width:280px;">
    	<table width="100%" class="table_collapse table_cell_padd5">
        <tr>
          <td align="left">UPC</td>
          <td align="left">
            <input type="text" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_1'));"  name="upc_name_1" id="upc_name_1" style="width:130px;" value="<?php echo $sel_order['upc_code']; ?>" autocomplete="off"/>
       		</td>
      </tr>
	  <tr>
          <td align="left">Item Name</td>
          <td align="left">
		  	<input type="text" onKeyDown="change_pos();" name="item_name_1" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_1'));" id="item_name_1" value="<?php echo $sel_order['item_name']; ?>" style="width:130px;">
       		</td>
      </tr>
	  <tr>
          <td align="left">Frames</td>
          <td align="left">
		  <!-- onChange="get_frame_price_detail(this.value); lens_row_display(this.value,'frame_display','in_order_details');" -->
		  <select name="lens_frame_id_1" id="lens_frame_id_1" style="width:137px;" >
          	<option value="0">Please Select</option>
          	<?php  
			$see_qry=imw_query("select id,item_name,show_default,lens_frame_id from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='1' and del_status='0'");
            $nums_qry = imw_num_rows($see_qry);
            if($nums_qry > 0)
            {
       			 while($see_order=imw_fetch_array($see_qry)) { 
				   $sel_frm = "";
				 	if($order_detail_id>0){
						if($lens_frame_id==$see_order['id'])
						{
							$sel_frm = "selected='selected'";
						}
					}else{
						if($see_order['show_default']=="1" && !in_array($see_order['id'], $exist_frame_id_arr))
						{
							$sel_frm = "selected='selected'";
						}
						elseif(!in_array($see_order['id'], $exist_frame_id_arr) && $action=="new_form")
						{
							$sel_frm = "selected='selected'";?>
						<script>
							var frm_id = '<?php echo $see_order['id']; ?>';
							get_frame_price_detail(frm_id);
						</script>	
						<?php }
					}
				 ?>
                <option value="<?php echo $see_order['id']; ?>" <?php echo $sel_frm; ?>><?php echo $see_order['item_name']; ?></option>
                <?php } } ?>
		 </select>
          </td>
      </tr>
        <tr>
            <td align="left" width="140">
                Seg Type                                            
            </td>
            <td align="left" width="180">
                <select name="type_id_1" id="type_id_1" style="width:137px;" onChange="javascript:show_progressive_dropdown(this); itemdropdown(); itemized_row_display(this.value,'lens_display','in_lens_type');">
                <option value="0">Please Select</option>
				<?php  
                $qry="";
                $qry = imw_query("select * from in_lens_type where del_status='0' order by type_name asc");
                while($rows = imw_fetch_array($qry))
                { ?>
                <option <?php if($sel_order['type_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['type_name']; ?></option>
                <?php }	?> 
                </select>                                            
            </td> 
        </tr>
		<tr>
            <td align="left" width="80">
                Progressive                                            
            </td>
            <td align="left" width="180">
                <select onChange="itemdropdown(); itemized_row_display(this.value,'progressive_display','in_lens_progressive');" name="progressive_id_1" id="progressive_id_1" style="width:137px;">
                <option value="0">Please Select</option>
				<?php  
                $qry="";
                $qry = imw_query("select * from in_lens_progressive where del_status='0' order by progressive_name asc");
                while($rows = imw_fetch_array($qry))
                { ?>
                <option <?php if($sel_order['progressive_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['progressive_name']; ?></option>
                <?php }	?> 
                </select>                                            
            </td> 
        </tr>
        <tr>
            <td align="left">
                Material</td>
            <td align="left">
                <select name="material_id_1" onChange="itemdropdown(); itemized_row_display(this.value,'material_display','in_lens_material');" id="material_id_1" style="width:137px;">
                <option value="0">Please Select</option>
				<?php  
                $qry="";
                $qry = imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
						while($rows = imw_fetch_array($qry))
						  { ?>
							<option <?php if($sel_order['material_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['material_name']; ?></option>	
					<?php }	?>
                </select>                                            
            </td>  
           
        </tr>
        <tr>
            <td align="left">
                Transition</td>
            <td align="left">
                <select name="transition_id_1" onChange="itemdropdown(); itemized_row_display(this.value,'transition_display','in_lens_transition');" id="transition_id_1" style="width:137px;">
                <option value="0">Please Select</option>
					<?php  
                    $qry="";
                    $qry = imw_query("select * from in_lens_transition where del_status='0' order by transition_name asc");
                    while($rows = imw_fetch_array($qry))
                    { ?>
                    <option <?php if($sel_order['transition_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['transition_name']; ?></option>
                    
                    <?php }	?>
                </select>                                            
            </td>   
                              
        </tr>
        <tr>
            <td align="left">
                A/R</td>
            <td align="left">
                <select name="a_r_id_1" onChange="itemdropdown(); itemized_row_display(this.value,'a_r_display','in_lens_ar');" id="a_r_id_1" style="width:137px;">
                <option value="0">Please Select</option>
					<?php  
                    $qry="";
                    $qry = imw_query("select * from in_lens_ar where del_status='0' order by ar_name asc");
                    while($rows = imw_fetch_array($qry))
                    { ?>
                    <option <?php if($sel_order['a_r_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['ar_name']; ?></option>
                    <?php }	?>
                </select>                                            
            </td></tr>
        <tr>
            <td align="left">
                Tint</td>
            <td align="left">
                <select name="tint_id_1" onChange="itemdropdown(); itemized_row_display(this.value,'tint_display','in_lens_tint');" id="tint_id_1" style="width:137px;">
                <option value="0">Please Select</option>
					<?php  
                    $qry="";
                    $qry = imw_query("select * from in_lens_tint where del_status='0' order by tint_type asc");
                    while($rows = imw_fetch_array($qry))
                    { ?>
                    <option <?php if($sel_order['tint_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['tint_type']; ?></option>
                    <?php }	?>
                </select>                                            
            </td></tr>
        <tr>
            <td align="left">
                Polarized</td>
            <td align="left">

                <select name="polarized_id_1" onChange="itemdropdown(); itemized_row_display(this.value,'polarization_display','in_lens_polarized');" id="polarized_id_1" style="width:137px;">
                <option value="0">Please Select</option>
                <?php
                $qry="";
                $qry = imw_query("select * from in_lens_polarized where del_status='0' order by polarized_name asc");
                while($rows = imw_fetch_array($qry))
                { ?>
                <option <?php if($sel_order['polarized_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['polarized_name']; ?></option>
                <?php }	?>
                </select> 

            </td></tr>
        <tr>
            <td align="left">
                Edge</td>
            <td align="left">
                <select name="edge_id_1" id="edge_id_1" onChange="itemdropdown(); itemized_row_display(this.value,'edge_display','in_lens_edge');" style="width:137px;">
                <option value="0">Please Select</option>
                    <?php  
					$qry="";
					$qry = imw_query("select * from in_lens_edge where del_status='0' order by edge_name asc");
					while($rows = imw_fetch_array($qry))
					{ ?>
					<option <?php if($sel_order['edge_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['edge_name']; ?></option>
					<?php }	?>
                </select>                                            
            </td>
		</tr>
			
		<tr>
            <td align="left">
                Color</td>
            <td align="left">
                <select name="color_id_1" id="color_id_1" onChange="itemdropdown(); autofillupc(); itemized_row_display(this.value,'color_display','in_lens_color');" style="width:137px;">
                <option value="0">Please Select</option>
<?php  
$qry="";
$qry = imw_query("select * from in_lens_color where del_status='0' order by color_name asc");
while($rows = imw_fetch_array($qry))
{ ?>
<option <?php if($sel_order['color_id']==$rows['id']){ echo "selected='selected'"; } ?> value="<?php echo $rows['id']; ?>"><?php echo $rows['color_name']; ?></option>
<?php }	?> 
                </select>                                            
            </td>
		</tr>
		<tr>
          <td align="left">Related items</td>
          <td align="left"> 
          		<select name="item_lens_selections_1" id="item_lens_selections_1" style="width:137px;">
                	<option value="">Please Select</option>
                </select>   
        </td>
        </tr>  
        <tr>
            <td align="left">
                Other</td>
            <td align="left">
                <input type="text" name="other_1" id="other_1" onChange="itemized_other_display(this.value,'other_display','in_item_price_details');" style="width:130px;" value="<?php echo $sel_order['lens_other']; ?>">             
            </td>
		</tr>      
        <tr>
            <td align="left" valign="top">
<input type="checkbox" name="uv400_1" onClick="itemized_row_display(this.value,'uv400_display','in_item_price_details');" id="uv400_1" <?php if($sel_order['uv400']==1){echo"checked";} ?>>&nbsp;UV400
            </td>
			<td align="left" valign="top">
<input type="checkbox" name="pgx_1" onClick="itemized_row_display(this.value,'pgx_display','in_item_price_details');" id="pgx_1" <?php if($sel_order['pgx']==1){echo"checked";} ?>>&nbsp;PGX
            </td>
        </tr>
   </table>
    </div>
   <div class="module_border fr" style="width:730px; margin:7px;">
   <input type="hidden" name="frame_order_id" id="frame_order_id" value="<?php echo $sel_frame_order['frame_order_id']; ?>">
   <input type="hidden" name="dx_frame_order_id" id="dx_frame_order_id" value="<?php echo $sel_order['lens_frame_id']; ?>">
   <input type="hidden" name="price_order_id" id="price_order_id" value="<?php echo $price_order_id; ?>">
        <table class="table_collapse table_cell_padd5">
            <tr>
                 <td colspan="7" align="center" style="text-align:center;" class="module_heading">
                    Itemized
                 </td>                                       
            </tr>
            <tr style="border-bottom:1px solid #ccc; border-top:1px solid #ccc;">
                 <td align="left" class="module_heading" style="border-right:1px solid #ccc; width:100px;">
                    Item Name
                 </td>
				  <td align="left" class="module_heading" style="border-right:1px solid #ccc; width:100px;">
                    Prac Code
                 </td>
                 <td align="left" class="module_heading" style="border-right:1px solid #ccc; width:100px;">
                    Price
                 </td>
                 <td align="left" class="module_heading" style="border-right:1px solid #ccc; width:80px;">
                    Discount
                 </td>
				 <td align="left" class="module_heading" style="border-right:1px solid #ccc; width:130px;">
                    Discount Code
                 </td>
				 <td align="left" class="module_heading" style="border-right:1px solid #ccc; width:80px;">
                    Qty.
                 </td>
                 <td align="left" class="module_heading" style="width:100px;">
                    Total
                 </td>                                      
            </tr>
            <!--<tr id="frame_display" style="display:none;">
                 <td align="left" style="border-right:1px solid #ccc;" id="frame_name_td">
					<?php
						if($sel_frame_order['frame_name']!=""){
							echo $sel_frame_order['frame_name'];
						}else{
							echo "Frame";
						}
					?>
                 </td>
				 <td align="center" style="border-right:1px solid #ccc;" id="frame_item_prac_code">
					<?php echo "<script>get_prac_code_text('".$sel_frame_order['frame_item_prac_code']."','frame_item_prac_code');</script>"; ?>
                 </td>
                 <td align="right" style="border-right:1px solid #ccc;">
				 	<input type="text" name="frame_price" id="frame_price" style="width:50px;" class="price_cls" value="<?php echo $sel_frame_order['frame_price']; ?>" onChange="this.value = parseFloat(this.value).toFixed(2); frame_title(this.value,$('#frame_disc').val(),$('#frame_qty').val(),'frame_total');">
                 </td>
                 <td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="frame_disc" id="frame_disc" style="width:50px;" class="price_disc" value="<?php echo $sel_frame_order['frame_discount']; ?>" onChange="frame_title($('#frame_price').val(),this.value,$('#frame_qty').val(),'frame_total');">
                 </td>
				 <td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="frame_qty" class="qty_cls" id="frame_qty" style="width:50px;" value="<?php echo $sel_frame_order['frame_qty']; ?>" onChange="frame_title($('#frame_price').val(),$('#frame_disc').val(),this.value,'frame_total');">
                 </td>
                 <td align="right">
                    <input type="text" name="frame_total" id="frame_total" style="width:50px;" class="price_total" value="<?php echo $sel_frame_order['frame_total_amount']*$sel_frame_order['frame_qty']; ?>" title="<?php echo ($sel_frame_order['frame_price']-$sel_frame_order['frame_discount'])." * ".$sel_frame_order['frame_qty']; ?>" readonly>
                 </td>                                       
            </tr>-->
            <?php
				$final_price_arr=array($lens_price,$material_price,$a_r_price,$transition_price,$polarization_price,$tint_price,$uv400_price,$other_price);
				$final_discount_arr=array($lens_discount,$material_discount,$a_r_discount,$transition_discount,$polarization_discount,$tint_discount,$uv400_discount,$other_discount);
				
				
				foreach($lens_price_data as $len_row)
				{
			?>
             <tr id="<?php echo $len_row['itemized_name'].'_display'; ?>" style="display:none;" class="dis_class">
                 <td align="left" style="border-right:1px solid #ccc;">
				 	<input type="hidden" name="lens_price_detail_id_1_<?php echo $len_row['itemized_id']; ?>" id="lens_price_detail_id_<?php echo $len_row['itemized_id']; ?>" value="<?php echo $len_row['lens_price_id']; ?>">
					<input type="hidden" name="lens_item_detail_id_1_<?php echo $len_row['itemized_id']; ?>" id="lens_item_detail_id_<?php echo $len_row['itemized_id']; ?>" value="<?php echo $len_row['itemized_id']; ?>">
					<input type="hidden" name="lens_item_detail_name_1_<?php echo $len_row['itemized_id']; ?>" id="lens_item_detail_name_<?php echo $len_row['itemized_id']; ?>" value="<?php echo $len_row['itemized_name']; ?>"> 
					<input type="hidden" name="lens_item_allowed_1_<?php echo $len_row['itemized_id']; ?>" id="lens_item_allowed_<?php echo $len_row['itemized_id']; ?>" value="<?php echo $len_row['allowed']; ?>" class="allowed_total">
					<?php if($len_row['itemized_name']=="a_r") { echo "A/R"; } elseif($len_row['itemized_name']=="lens") { echo "Seg type"; } else { echo ucfirst($len_row['itemized_name']); } ?>
                 </td>
				 <td align="center" style="border-right:1px solid #ccc;" class="item_prac_code_text" id="item_prac_code_text_<?php echo $len_row['itemized_id']; ?>">
				 	<input style="width:80px;" type="text" class="pracodefield" name="item_prac_code_1_<?php echo $len_row['itemized_id']; ?>" id="item_prac_code_<?php echo $len_row['itemized_id']; ?>" value="<?php echo $proc_code_arr[$len_row['item_prac_code']]; ?>" title="<?php echo $proc_code_desc_arr[$len_row['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'lens_item_price_<?php echo $len_row['itemized_id']; ?>','frm');"/>
                 </td>
                 <td align="right" style="border-right:1px solid #ccc;">
					<input type="text" name="lens_item_price_1_<?php echo $len_row['itemized_id']; ?>" id="lens_item_price_<?php echo $len_row['itemized_id']; ?>" style="width:90px;" class="price_cls"  value="<?php echo $len_row['wholesale_price']; ?>" onChange="this.value = parseFloat(this.value).toFixed(2);">
                 </td>
                 <td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="lens_item_discount_1_<?php echo $len_row['itemized_id']; ?>" id="lens_item_discount_<?php echo $len_row['itemized_id']; ?>" style="width:70px;" class="price_disc disc_code" value="<?php echo $len_row['discount']; ?>">
                 </td>
				 <td align="left" style="border-right:1px solid #ccc;">
					<select name="discount_code_1_<?php echo $len_row['itemized_id']; ?>" id="discount_code" class="text_10 discount_code" style="width:120px;">
						<option value="">Please Select</option>
						<?php
						$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
						while($sel_write=imw_fetch_array($sel_rec)){
						?>
						<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$len_row['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
						</option>
						<?php } ?>
					</select>
				 </td>
				 <td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="lens_qty_1_<?php echo $len_row['itemized_id']; ?>" id="lens_qty_1_<?php echo $len_row['itemized_id']; ?>" class="qty_cls" style="width:70px;" value="<?php echo $len_row['qty']; ?>">
                 </td>
				 
                 <td align="right">
                    <input type="text" name="lens_item_total_1_<?php echo $len_row['itemized_id']; ?>" id="lens_item_total_<?php echo $len_row['itemized_id']; ?>" style="width:90px;" class="price_total" value="<?php echo $len_row['total_amt']; ?>" readonly>
                 </td>                                       
            </tr>
			<?php } ?>
			
			<tr>
				<td align="left" style="border-right:1px solid #ccc;">Overall Dis.</td>
				<td align="left" style="border-right:1px solid #ccc;">&nbsp;</td>
				<td align="left" style="border-right:1px solid #ccc;">&nbsp;</td>
				<td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="overall_lens_discount_1" style="width:70px;" value="<?php echo $sel_order['overall_discount']; ?>" onBlur="apply_dis_all(this);">
                </td>
				<td align="left" style="border-right:1px solid #ccc;">&nbsp;</td>
				<td style="border-right:1px solid #ccc;">&nbsp;</td>
			</tr>
			<input type="hidden" name="lens_item_count_1" id="lens_item_count" value="<?php echo $lens_item_count; ?>">
			<tr style="border-top:1px solid #ccc">
                 <td align="left" colspan="2" style="border-right:1px solid #ccc;" class="module_heading">
                    Grand Total
                 </td>
                 <td align="right" style="border-right:1px solid #ccc;">
					<input type="text" name="item_lens_grand_price" id="item_lens_grand_price" style="width:90px;"  value="<?php echo array_sum($final_price_arr);?>" readonly>
                 </td>
                 <td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="item_lens_grand_disc" id="item_lens_grand_disc" style="width:70px;"   value="<?php echo array_sum($final_discount_arr);?>" readonly>
                 </td>
				 <td align="left" style="border-right:1px solid #ccc;">&nbsp;
                    
                 </td>
				 <td align="left" style="border-right:1px solid #ccc;">
                    <input type="text" name="item_lens_grand_qty" id="item_lens_grand_qty" style="width:70px;"   value="" readonly>
                 </td>
                 <td align="right">
                    <input type="text" name="item_lens_grand_total" id="item_lens_grand_total" style="width:90px;"   value="<?php echo array_sum($final_total_price_arr);?>" readonly>
                 </td>                                     
            </tr>
        </table>
   </div>
</div>
	 <input type="hidden" name="last_cont" id="last_cont" value="1" />
    <div class="btn_cls mt10" style="width: 95%; float: left;">
        <input type="button" name="previous" value="Previous" onClick="frm_sub_fun('previous');"/>
		<input type="button" name="new_form" value="New" onClick="frm_sub_fun('new_form');"/>
		<input type="button" name="Cancel" value="Cancel" onClick="frm_sub_fun('cancel');"/>
		<input type="button" name="new2" value="On Hold"/>
		<input type="button" name="save" value="Save" onClick="frm_sub_fun('save');"/>
		<input type="button" name="next_btn" id="next_btn" value="Next" onClick="frm_sub_fun('next');"/>
    </div> 
</form>

<script type="text/javascript">
$(document).ready(function() 
{

	var lens_selid = '<?php echo $lens_selection_id; ?>';
	itemdropdown(lens_selid);
});
	show_progressive_dropdown($("#type_id_1"));
	var fram_id = document.getElementById('lens_frame_id_1').value;
	get_frame_price_detail(fram_id);
	var or_id = '<?php echo $order_detail_id; ?>';
	var ordr = "";
	if(or_id > 0)
	{
		ordr = "order";
	}
	//lens_row_display(fram_id,'frame_display','in_order_details');
	lens_row_display($("#type_id_1").val(),'lens_display','in_lens_type',ordr);
	lens_row_display($("#progressive_id_1").val(),'progressive_display','in_lens_progressive',ordr);
	lens_row_display($("#material_id_1").val(),'material_display','in_lens_material',ordr);
	lens_row_display($("#transition_id_1").val(),'transition_display','in_lens_transition',ordr);
	lens_row_display($("#a_r_id_1").val(),'a_r_display','in_lens_ar',ordr);
	lens_row_display($("#tint_id_1").val(),'tint_display','in_lens_tint',ordr);
	lens_row_display($("#polarized_id_1").val(),'polarization_display','in_lens_ar',ordr);
	lens_row_display($("#edge_id_1").val(),'edge_display','in_lens_edge',ordr);
	lens_row_display($("#color_id_1").val(),'color_display','in_lens_color',ordr);
	lens_row_display($("#uv400_1").val(),'uv400_display','in_item_price_details',ordr);
	lens_row_display($("#pgx_1").val(),'pgx_display','in_item_price_details',ordr);
	itemized_other_display($("#other_1").val(),'other_display','in_item_price_details',ordr);
	
	var obj7 = new actb(document.getElementById('upc_name_1'),custom_array_upc,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	
	var obj8 = new actb(document.getElementById('item_name_1'),custom_array_name,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	
	var obj6 = new actb(document.getElementById('dx_code_1'),customarrayProcedure);
	
	for(var t=1;t<=12;t++){
		var obj5 = new actb(document.getElementById('item_prac_code_'+t),customarrayProcedure_pr);
	}
</script>
</body>
</html>