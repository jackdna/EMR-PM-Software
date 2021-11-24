<?php 
/*
File: patient_pos.php
Coded in PHP7
Purpose: Patient POS Final Page
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php"); 
require_once(dirname('__FILE__')."/../../library/classes/functions.php"); 
?>
<div align="center" id="div_loading_image" width="100%" style="display:block; top:150px; left:330px; z-index:1000; position:absolute;">
	<img src="../../images/loading_image.gif">
</div>
<?php
$patient_id=$_SESSION['patient_session_id'];
$action=$_REQUEST['frm_method'];
if(isset($_REQUEST['pt_pos']) && $_REQUEST['pt_pos']>0)
{
	$_SESSION['order_id']=$_REQUEST['pt_pos'];
}
if($action=="next" || $action =="order_post" || $action =="dispensed_post"){
	other_order_action($action,$_POST);
	if($action=="order_post" || $action =="dispensed_post"){
		$order_id=$_SESSION['order_id'];
		$sel_ord_qry=imw_query("select order_enc_id from in_order where id ='$order_id'");
		$sel_ord_row=imw_fetch_array($sel_ord_qry);
		$order_enc_id=$sel_ord_row['order_enc_id'];
		echo "<script type='text/javascript'>top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('Add_new_popup','../../remoteConnect.php?encounter_id=$order_enc_id','opt_med');</script>";
	}
	order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='patient_pos.php'</script>";
}else if($action=="cancel"){
	other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='patient_pos.php'</script>";
}

function prismNumbers($selected=''){
	$optValues='';
	for($i=1; $i<=10;){
		$sel=($i==$selected)? 'selected': '';
		$optValues.='<option  value="'.$i.'" '.$sel.'>'.$i.'</option>';
		$i+=0.5;
	}
	return $optValues;
}

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

$AllUpcIdArray = implode(',',$AllUpcIdArrays);
$AllUpcArray = implode(',',$AllUpcArray);
$AllNameArray = implode(',',$AllNameArray);
// COMMON FUNCTIONS
$arrManufac=array();
$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where frames_chk='1' and del_status='0'";
$manu_detail_res = imw_query($manu_detail_qry);
$manu_detail_nums = imw_num_rows($manu_detail_res);
if($manu_detail_nums > 0)
{	
	while($manu_detail_row = imw_fetch_array($manu_detail_res)) {
		$arrManufac[$manu_detail_row['id']] = $manu_detail_row['manufacturer_name'];
	}	
} 

$cl_arrManufac=array();

$cl_manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where cont_lenses_chk='1' and del_status='0'";

$cl_manu_detail_res = imw_query($cl_manu_detail_qry);
$cl_manu_detail_nums = imw_num_rows($cl_manu_detail_res);
if($cl_manu_detail_nums > 0)
{	
	while($cl_manu_detail_row = imw_fetch_array($cl_manu_detail_res)) {
		$cl_arrManufac[$cl_manu_detail_row['id']] = $cl_manu_detail_row['manufacturer_name'];
	}	
} 


$ins_opt="";
/*$getins_data = "SELECT insurance_data.provider,insurance_data.type,insurance_companies.in_house_code
				FROM insurance_data 
				join insurance_companies on insurance_companies.id= insurance_data.provider
				WHERE insurance_data.pid = '$patient_id' and insurance_data.actInsComp ='1'
				order by insurance_data.type";
$getins_data_qry = imw_query($getins_data);
while($getins_data_row = imw_fetch_array($getins_data_qry)){
	$ins_data_arr[$getins_data_row['provider']]=$getins_data_row['in_house_code'];
	$ins_opt.='<option value="'.$getins_data_row['provider'].'">'.$getins_data_row['in_house_code'].'</option>';
}*/

$getins_data="SELECT insct.case_name,insc.ins_caseid, insc.ins_case_type 
				FROM insurance_case_types insct 
				JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status ='Open') 
				JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0) 
				JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code !='n/a') 
				WHERE insc.patient_id='$patient_id'
				GROUP BY insc.ins_caseid 
				ORDER BY insc.ins_case_type";
$getins_data_qry = imw_query($getins_data);
while($getins_data_row = imw_fetch_array($getins_data_qry)){
	$insCasesArr = $getins_data_row['case_name'].'-'.$getins_data_row['ins_caseid'];
	$ins_case_arr[$getins_data_row['ins_caseid']]=$insCasesArr;
}
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
				
				$code_dx = $rowCodes_dx["d_prac_code"];
				$dx_desc = $rowCodes_dx["diag_description"];
				$stringAllProcedures_dx.="'".str_replace("'","",$code_dx)."',";	
				$stringAllProcedures_dx.="'".str_replace("'","",$dx_desc)."',";
				$dx_code_arr[$rowCodes_dx["diagnosis_id"]]=$rowCodes_dx["d_prac_code"];
			}
		$arrdxCodes[] = array($row["category"],$arrSubOptions_dx);
		}		
	}

	$stringAllProcedures_dx = substr($stringAllProcedures_dx,0,-1);
	
//-----------------  END GETTING DATA FOR MENUS TO CATEGORY OF DX Code	------------------------//


?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.confirm.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.widget.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/actb.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.confirm.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
$(function() 
{
	$( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true,dateFormat: 'mm-dd-yy'});
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	$("#otherback").click(function()
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
	});

	$( ".target" ).change(function() {
  		alert( "Handler for .change() called." );
	});
});
function sel_chk_date_fun(id){
	if($("#ordered_"+id).val()!=""){
		$("#ordered_chk_"+id).prop('checked',true);
	}
	if($("#received_"+id).val()!=""){
		$("#received_chk_"+id).prop('checked',true);
	}
	if($("#notified_"+id).val()!=""){
		$("#notified_chk_"+id).prop('checked',true);
	}
	if($("#dispensed_"+id).val()!=""){
		$("#dispensed_chk_"+id).prop('checked',true);
	}
}

function sel_location_chk(id,nums){
	$('#'+id+nums).prop('checked',true);
}

</script>

<script>
var rowData_c='';
var tr  = '';
function addrow()	
{		 
	var getRows = $("#last_cont").val();
	y = parseInt(getRows)+1;
	var ins_opt='<?php echo $ins_opt;?>';
	rowData_c+='<tr id="'+y+'">';
	rowData_c+='<td><input type="hidden" name="upc_id_'+y+'" id="upc_id_'+y+'" value=""><input style="width:90px;" type="text" name="upc_name_'+y+'" id="upc_name_'+y+'" value="" onchange="javascript:upc(document.getElementById(\'upc_id_'+y+'\'), '+y+');" /><input type="hidden" value="" id="order_chld_id_'+y+'" name="order_chld_id_'+y+'"></td>';
	rowData_c+='<td><input style="width:90px;" type="text" class="itemname" name="item_name_'+y+'" id="item_name_'+y+'" value="" onChange="javascript:upc(document.getElementById(\'upc_id_'+y+'\'), '+y+');"/><input type="hidden" value="" id="item_id_'+y+'" name="item_id_'+y+'"><input type="hidden" value="" id="module_type_id_'+y+'" name="module_type_id_'+y+'"><input type="hidden" name="qty_'+y+'" id="qty_'+y+'" value="1" /></td>';
	rowData_c+='<td><input style="width:90px;" class="pracodefield" type="text" name="item_prac_code_'+y+'" id="item_prac_code_'+y+'" value="" onChange="calculate_all();"/></td>';
	rowData_c+='<td><input style="width:90px; text-align:right;" type="text" name="price_'+y+'" id="price_'+y+'" value="" class="price_cls" onChange="calculate_all();"/></td>';
	rowData_c+='<td><input style="width:90px; text-align:right;" type="text" name="allowed_'+y+'" id="allowed_'+y+'" value="" class="allowed_cls" onChange="calculate_all();"/></td>';
	rowData_c+='<td><input style="width:70px; text-align:right;" type="hidden" name="discount_'+y+'" id="discount_'+y+'" value="" onChange="calculate_all();" class="price_disc_per_proc"/><input style="width:70px; text-align:right;" type="text" name="read_discount_'+y+'" id="read_discount_'+y+'" value="" class="price_disc" onChange="calculate_all();"/></td>';
	rowData_c+='<td><input style="width:90px; text-align:right;" type="text" name="total_amount_'+y+'" id="total_amount_'+y+'" value="" class="price_total" onChange="calculate_all();"/></td>';
	rowData_c+='<td><input style="width:90px; text-align:right;" type="text" name="ins_amount_'+y+'" id="ins_amount_'+y+'" value="" onChange="calculate_all();" class="ins_amt_cls"/></td>';
	rowData_c+='<td><input style="width:90px; text-align:right;" type="text" name="pt_paid_'+y+'" id="pt_paid_'+y+'" value="" onChange="calculate_all();" class="payed_cls"/></td>';
	rowData_c+='<td><input style="width:90px; text-align:right;" type="text" name="pt_resp_'+y+'" id="pt_resp_'+y+'" value="" onChange="calculate_all();" class="resp_cls"/></td>';
	rowData_c+='<td><select style="width:90px;" id="discount_code_'+y+'" name="discount_code_'+y+'"><option value=""></option></select></td><td><select style="width:90px;" id="ins_id_'+y+'" name="ins_id_'+y+'"><option value=""></option>'+ins_opt+'</select></td>';
	rowData_c+='</tr>';
	
	if(getRows==0){
		$("#item_tr_id").after(rowData_c);
	}else{
		$("#"+getRows).after(rowData_c);
	}
	
	rowData_c='';
	$("#last_cont").val(y);	
	
	//var obj6 = new actb(document.getElementById('upc_name_'+y),custom_array_upc,"","",document.getElementById('upc_id_'+y),custom_array_upc_id);
//	
//	var obj8 = new actb(document.getElementById('item_name_'+y),custom_array_name,"","",document.getElementById('upc_id_'+y),custom_array_upc_id);
//	
//	var obj7 = new actb(document.getElementById('item_prac_code_'+y),customarrayProcedure);				
}

function upc(upc_code, num)
{
	var ucode = $.trim(upc_code.value);
	var dataString = 'action=managestock&code='+ucode;
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = $.parseJSON(response);
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
				 	$("#upc_name_"+num).val(item.upc_code);
					$("#item_name_"+num).val(item.name);
					$("#price_"+num).val(item.retail_price);
					$("#discount_"+num).val(item.discount);
					$("#total_amount_"+num).val(item.amount);
					$("#item_id_"+num).val(item.id);
					$("#module_type_id_"+num).val(item.module_type_id);
				 });
			 }
			 else
			 {
				// $("#stock_form")[0].reset();
			 }
			 calculate_all();
		}
	});
	
	 var getRows = $("#last_cont").val();
	 if(getRows==num)
	 {
		// addrow();
	 }
}

function calculate_all()
{
	grand_price = grand_disc = grand_total = grand_allowed = 0;
	grand_payed = grand_resp = grand_ins_amt = 0;
	$('.price_cls').each(function(index, element) {
	price_cls = parseFloat($('.price_cls').get(index).value);
	allowed_cls = parseFloat($('.allowed_cls').get(index).value);
	qty_cls = $('.qty_cls').get(index).value;
	rqty_cls = $('.rqty_cls').get(index).value;
	payed_cls = parseFloat($('.payed_cls').get(index).value);
	ins_amt_cls = parseFloat($('.ins_amt_cls').get(index).value);
	disc_val = $('.price_disc_per_proc').get(index).value;
	tot_qty = parseInt(qty_cls)+parseInt(rqty_cls);
	if(disc_val.slice(-1)=='%'){
		disc_val = disc_val.replace('%','');
		disc_val = allowed_cls * (parseFloat(disc_val)/100);
	}else{
		disc_val = tot_qty * parseFloat(disc_val);
		if(disc_val>0){
			$('.price_disc').get(index).value = disc_val.toFixed(2);
		}
	}
	disc_val = parseFloat(disc_val);
	
	if(isNaN(price_cls)){
		price_cls = 0;
		$('.price_cls').get(index).value = price_cls.toFixed(2);
	}
	if(isNaN(allowed_cls)){
		allowed_cls = 0;
		$('.allowed_cls').get(index).value = allowed_cls.toFixed(2);
	}
	if(isNaN(payed_cls)){
		payed_cls = 0;
		$('.payed_cls').get(index).value = payed_cls.toFixed(2);
	}
	if(isNaN(ins_amt_cls)){
		ins_amt_cls = 0;
		$('.ins_amt_cls').get(index).value = ins_amt_cls.toFixed(2);
	}
	if(isNaN(disc_val)){
		disc_val = 0;
		$('.price_disc').get(index).value = disc_val.toFixed(2);
	}
	if(allowed_cls>0){
		price_total = (allowed_cls-disc_val);
	}else{
		price_total = (price_cls-disc_val)*tot_qty;
	}
	resp_cls = price_total-ins_amt_cls-payed_cls;
	
	if(!isNaN(price_total)){
		$('.price_total').get(index).value = price_total.toFixed(2);
	}
	if(!isNaN(resp_cls)){
		if(resp_cls>0){
			$('.resp_cls').get(index).value = resp_cls.toFixed(2);
		}else{
			$('.resp_cls').get(index).value = '0.00';
		}
	}
	grand_price = grand_price + price_cls;
	grand_allowed = grand_allowed + allowed_cls;
	grand_disc = grand_disc + disc_val;
	grand_total = grand_total + parseFloat(price_total);
	grand_payed = grand_payed + payed_cls;
	grand_resp = grand_resp + resp_cls;
	grand_ins_amt = grand_ins_amt + ins_amt_cls;
	});
	if(!isNaN(grand_price)){
		$('#pat_pos_grand_price').val(grand_price.toFixed(2));
	}else{
		grand_price=0;
	}
	if(!isNaN(grand_allowed)){
		$('#pat_pos_grand_allowed').val(grand_allowed.toFixed(2));
	}else{
		grand_allowed=0;
	}
	if(!isNaN(grand_payed)){
		$('#pat_pos_grand_payed').val(grand_payed.toFixed(2));
	}else{
		grand_payed=0;
	}
	if(!isNaN(grand_resp)){
		$('#pat_pos_grand_resp').val(grand_resp.toFixed(2));
	}else{
		grand_resp=0;
	}
	if(!isNaN(grand_ins_amt)){
		$('#pat_pos_grand_ins_amt').val(grand_ins_amt.toFixed(2));
	}else{
		grand_ins_amt=0;
	}
	if(!isNaN(grand_disc)){
		$('#pat_pos_grand_disc').val(grand_disc.toFixed(2));
	}else{
		grand_disc=0;
	}
	//grand_total = grand_price-grand_disc;
	$('#pat_pos_grand_total').val(grand_total.toFixed(2));
}
</script>

<script>
$(document).ready(function(e) {
	$('#lens_contact_lens').click(function(e) {
		$('#lens_contact_lens_hide').toggle();
	});
	
	$('#contact_lens_pres').click(function(e) {
		$('#contact_lens_pres_hide').toggle();
	});
	
	$('#frame_hide_show').click(function(e) {
		$('#frame_hide_show_id').toggle();
	});
	
	$('#lens_hide_show').click(function(e) {
		$('#lens_hide_show_id').toggle();
	});
	
	$('#cl_hide_show').click(function(e) {
		$('#cl_hide_show_id').toggle();
	});
	
	$('#other_hide_show').click(function(e) {
		$('#other_hide_show_id').toggle();
	});
});

function show_hide_tbl(val){
	$('#'+val).show();
}

function auto_select_dis_code(dis_cd)
{
	$(".dis_code_class").val(dis_cd);
}

function auto_select_ins(sel_val)
{
	$(".ins_case_class").val(sel_val);
	var payed_clas=trans_amt=total_clas=0;
	$('.ins_case_class').each(function(index, element) {
		payed_clas = parseFloat($('.payed_cls').get(index).value);
		total_clas = parseFloat($('.price_total').get(index).value);
		trans_amt = total_clas - payed_clas;
		if($(this).val()=="")
		{
			$('.ins_amt_cls').get(index).value = "0.00";
			$('.resp_cls').get(index).value = trans_amt.toFixed(2);
		}
		else if($(this).val()!="" && $(this).val()>0)
		{
			$('.ins_amt_cls').get(index).value = trans_amt.toFixed(2);
		}
	});
	calculate_all();
}

function switch_pat_ins_resp(pr_cont)
{
	var ins_id=tot_amt=pt_paid=transfer_amt=0;
	ins_id = $("#ins_case_id_"+pr_cont).val();
	tot_amt = $("#total_amount_"+pr_cont).val();
	pt_paid = $("#pt_paid_"+pr_cont).val();
	transfer_amt = parseFloat(tot_amt)-parseFloat(pt_paid);
	if(ins_id=="")
	{
		$("#ins_amount_"+pr_cont).val("0.00");
		$("#pt_resp_"+pr_cont).val(transfer_amt.toFixed(2));
	}
	else if(ins_id!="" && ins_id>0)
	{
		$("#ins_amount_"+pr_cont).val(transfer_amt.toFixed(2));
	}
	calculate_all();
}

function pospage_title(pr_cnt)
{
	var oth_price = $("#price_"+pr_cnt).val();
	var oth_dis = $("#discount_"+pr_cnt).val();
	var oth_lqty = $("#qty_"+pr_cnt).val();
	var oth_rqty = $("#qty_right_"+pr_cnt).val();
	var oth_qty = parseInt(oth_lqty) + parseInt(oth_rqty);
	var title_price = cal_discount(oth_price,oth_dis);
	$("#total_amount_"+pr_cnt).prop('title',title_price.toFixed(2)+' * '+oth_qty);
}

</script>

<script>
var ptwin='';
function printpos()
{
	try 
	{
		top.WindowDialog.closeAll();
		var ptwin=top.WindowDialog.open('Add_new_popup','print_pos.php', "ptwindow","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=0");
		ptwin.focus();
	}
	catch(e) 
	{
		//location.target = "_self";
		//location.href = url;
	}

}

function patientReceipt()
{
	try 
	{
		top.WindowDialog.closeAll();
		var ptwin=top.WindowDialog.open('Add_new_popup','print_pos_patient.php', "ptwindow","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=0");
		ptwin.focus();
	}
	catch(e) 
	{
		//location.target = "_self";
		//location.href = url;
	}

}
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


<script type="text/javascript">
function frm_sub_fun_callBack(result)
{
	if(result==false)
	{
		return false;	
	}
}

function frm_sub_fun_callBack2(result)
{
	if(result==true)
	{
		$("#reduc_stock").val('yes');
		$("#frm_method").val('dispensed_post');
		document.patient_pos_frm.submit();
	}
	else
	{
		$("#reduc_stock").val('no');
		$("#frm_method").val('dispensed_post');
		document.patient_pos_frm.submit();	
	}
}
$(document).ready(function(){
	
frm_sub_fun = function(action)
{
	var prc = "";
	if(action=="cancel")
	{
		top.fconfirm('Are you sure to cancel this Order ?',frm_sub_fun_callBack);
	}
	var chk_sub=1;
	if(action=="next" || action=="order_post" || action=="dispensed_post")
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
	if((action=="order_post" || action=="dispensed_post")  && chk_sub==1)
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

		$(".pracodefield").each(function( index )
		{
		if($.trim($(this).val())=="")
			{
				prc +=index;				
				countz = index+1;
				var tax_alt = 0;
				if($(".itemnameClass"+countz).val()=="Taxes" && $(".price_cls").get(index).value==0)
				{
					tax_alt = 1;
				}
				if(tax_alt==0)
				{
					top.falert("Please Select Prac Code of '"+$(".itemnameClass"+countz).val()+"'" );
					countz='';
					$(this).focus();
					pr_cd=1;
					return false;
				}
			}
		});
	}
	if(action=="order_post" && pr_cd==0 && chk_sub==1)
	{
		$("#frm_method").val(action);
		document.patient_pos_frm.submit();
	}
	if((action=="next" || action=="cancel") && prc=="" && chk_sub==1)
	{
		$("#frm_method").val(action);
		document.patient_pos_frm.submit();
	}
	if(action=="dispensed_post" && pr_cd==0  && chk_sub==1)
	{
		top.fconfirm('Do you want to reduce Qty from stock?',frm_sub_fun_callBack2);
	}
}

});
function changeMode(){
	var thisVal = document.getElementById('paymentMode').value;
	if(thisVal == 'Cash'){
		document.getElementById('checkTd').style.display = 'none';
		document.getElementById('ccTd').style.display = 'none';
	}else if(thisVal == 'Check' || thisVal == 'EFT' || thisVal == 'Money Order'){
		document.getElementById('checkTd').style.display = 'block';
		document.getElementById('ccTd').style.display = 'none';
	}else if(thisVal == 'Credit Card'){
		document.getElementById('checkTd').style.display = 'none';
		document.getElementById('ccTd').style.display = 'block';
	}
}
function set_allowed_amt(id){
	document.getElementById('allowed_'+id).value = document.getElementById('price_'+id).value;
}
</script>
</head>
<body>
	
	<form action="" name="patient_pos_frm" method="post">
    <div class="module_border" style="height:<?php echo $_SESSION['wn_height']-465;?>px; overflow:hidden; overflow-y:auto;">
		<div class="listheading">Order #<?php echo $_SESSION['order_id']; ?></div>
        <div class="listheading" id="lens_contact_lens">
           <div style="width:100%; float:left">Lens Prescription</div>
        </div>
        
		<input type="hidden" name="frame_order_detail_id" id="frame_order_detail_id" value="<?php echo $frame_order_detail_id;?>">
		<input type="hidden" name="lens_order_detail_id" id="lens_order_detail_id" value="<?php echo $lens_order_detail_id;?>">
		<input type="hidden" name="cl_order_detail_id" id="cl_order_detail_id" value="<?php echo $cl_order_detail_id;?>">
		<input type="hidden" name="frame_module_type_id" id="frame_module_type_id" value="1">
		<input type="hidden" name="lens_module_type_id" id="lens_module_type_id" value="2">
		<input type="hidden" name="cl_module_type_id" id="cl_module_type_id" value="3">
		<input type="hidden" name="frm_method" id="frm_method" value="">
		<input type="hidden" name="page_name" id="page_name" value="pos">
		<input type="hidden" name="reduc_stock" id="reduc_stock" value="no">
		<?php
        if($_SESSION['order_id']>0){
		$order_id=$_SESSION['order_id'];
		
		$sel_ord_qry_ins=imw_query("select main_default_discount_code, main_default_ins_case, comment,payment_mode,checkNo,creditCardNo,creditCardCo,expirationDate from in_order where id ='$order_id'");
		$sel_ord_row_ins=imw_fetch_array($sel_ord_qry_ins);
		 //LENS PRESCRIPTION
		 
		$lensResArr=array();
		$clLensResArr=array();

		$lensRs=imw_query("Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND patient_id='".$_SESSION['patient_session_id']."' AND del_status='0'");
		
		$i=1;
		while($lensRes=imw_fetch_array($lensRs))
		{
			$lensResArr[]=$lensRes;
		?>
		
		<input type="hidden" name="lens_prescription_count[]" value="<?php echo $i; ?>">		
            
        <input type="hidden" name="order_rx_lens_id_<?php echo $i; ?>" id="order_rx_lens_id_<?php echo $i; ?>" value="<?php echo $lensRes['id'];?>">
        
			<?php 
			$i++;
			}
			
			//print_r($lensResArr);
			
			//CONTACT LENS PRESCRIPTION
			//echo "<br>";
			
			$clLensRs=imw_query("Select * FROM in_cl_prescriptions WHERE order_id='".$order_id."' AND patient_id='".$_SESSION['patient_session_id']."' AND del_status='0'");
			$j=1;
			while($clLensRes=imw_fetch_array($clLensRs))
			{	
				$clLensResArr[]=$clLensRes;	?>
		
				<input type="hidden" name="cl_prescription_count[]" value="<?php echo $j; ?>">
        			
				<input type="hidden" name="order_rx_cl_id_<?php echo $j; ?>" id="order_rx_cl_id_<?php echo $j; ?>" value="<?php echo $clLensRes['id'];?>">
			
			<?php
			$j++;
			}	
		}
        ?>

    	<table class="table_collapse table_cell_padd5" border="0" id="lens_contact_lens_hide" style="display:none;">
			 <?php if(count($lensResArr)>0) { ?>	 
            <tr>
              <td style="width:900px;" valign="top">              
              <?php 
			 	//echo count($lensResArr);
  
			  $lensResTotal=count($lensResArr);
			  
			  $y= 1;
			  for($z=0;$z<$lensResTotal;$z++){ 
			  $lensnameq = imw_query("select item_name from in_order_details where id = '".$lensResArr[$z]['det_order_id']."'");
			  $lensnamerow = imw_fetch_assoc($lensnameq);			  
			  ?>              
                <table class="table_collapse table_cell_padd5" border="0">
           		<tr>
                <td class="reportHeadBG1">Lens :- <?php echo $lensnamerow['item_name']; ?></td>
                </tr>
                    <tr class="even"> 
                        <td style="width:900px;">
                            <div style="width:75px; float:left;"><span class="blueColor" style="font-weight:bold;">OD&nbsp;&nbsp;</span>Sph</div>
                            <input readonly type="text" name="lens_sphere_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['sphere_od'];?>" /> <span class="mr_lr">CYL</span>
                            <input readonly type="text" name="lens_cylinder_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['cyl_od'];?>" /> <span class="mr_lr">Axis</span> 
                            <input readonly type="text" name="lens_axis_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['axis_od'];?>" /> <span class="mr_lr">Prism</span>
                            
							<input readonly type="text" style="width:100px;" name="lens_mr_od_p_<?php echo $y; ?>" value="<?php echo $lensResArr[$z]['mr_od_p']; ?>" />
                            &nbsp;
                            <!--<select name="lens_mr_od_p_<?php //echo $y; ?>" id="lens_mr_od_p_<?php //echo $y; ?>" style="width:50px" class="rx_cls">
                            <option  value=""></option>
                            <?php //echo prismNumbers($lensResArr[$z]['mr_od_p']); ?>
                            </select> -->
                            


                            
                            <input readonly type="text" name="lens_mr_od_prism_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['mr_od_prism']; ?>" />
                            
                            <!--<select name="lens_mr_od_prism_<?php echo $y; ?>" id="lens_mr_od_prism_<?php echo $y; ?>" style="width:50px" class="rx_cls" >
                              <option  value=""></option>
                              <option  value="BD" <?php if($lensResArr[$z]['mr_od_prism']=='BD')echo 'selected';?>>BD</option>
                              <option  value="BU" <?php if($lensResArr[$z]['mr_od_prism']=='BU')echo 'selected';?>>BU</option>
                            </select> -->/                            
                           
                            <input readonly type="text" name="lens_mr_od_splash_<?php echo $y; ?>"  style="width:100px;" value="<?php echo $lensResArr[$z]['mr_od_splash']; ?>" />
                            &nbsp;
                           <!-- <select name="lens_mr_od_splash_<?php echo $y; ?>" id="lens_mr_od_splash_<?php echo $y; ?>" style="width:50px" class="rx_cls">
                              <option  value=""></option>
                              <?php //echo prismNumbers($lensResArr[$z]['mr_od_splash']); ?>
                            </select> -->

                            <input readonly name="lens_mr_od_sel_<?php echo $y; ?>" type="text" style="width:100px;" value="<?php echo $lensResArr[$z]['mr_od_sel']; ?>" />
                            
                            <!--<select name="lens_mr_od_sel_<?php echo $y; ?>" id="lens_mr_od_sel_<?php echo $y; ?>" style="width:50px" class="rx_cls" value="<?php echo $lensResArr[$z]['mr_od_sel'];?>">
                            <option value=""></option>
                            <option value="BI" <?php if($lensResArr[$z]['mr_od_sel']=='BI')echo 'selected';?>>BI</option>
                            <option value="BO" <?php if($lensResArr[$z]['mr_od_sel']=='BO')echo 'selected';?>>BO</option>
                            </select>
                             -->                        
                       </td>
                    </tr>
                    <tr> 
                        <td>
                            <div style="width:75px; float:left;"><span class="greenColor" style="font-weight:bold;">OS&nbsp;&nbsp;</span>Sph</div> 
                            <input readonly type="text" name="lens_sphere_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['sphere_os'];?>" /> <span class="mr_lr">CYL</span>
                            <input readonly type="text" name="lens_cylinder_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['cyl_os'];?>" /> <span class="mr_lr">Axis</span>
                            <input readonly type="text" name="lens_axis_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['axis_os'];?>" /> <span class="mr_lr">Prism </span>
							<input readonly type="text" name="lens_mr_os_p_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['mr_os_p']; ?>" />
							&nbsp;
<!--                            <select name="lens_mr_os_p_<?php echo $y; ?>" id="lens_mr_os_p_<?php echo $y; ?>" style="width:50px" class="rx_cls" >
                            <option  value=""></option>
                            <?php echo prismNumbers($lensResArr[$z]['mr_os_p']); ?>
                            </select> -->
                            
							<input readonly type="text" name="lens_mr_os_prism_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['mr_os_prism']; ?>" />                            
<!--                            <select name="lens_mr_os_prism_<?php echo $y; ?>" id="lens_mr_os_prism_<?php echo $y; ?>" style="width:50px" class="rx_cls">
                              <option  value=""></option>
                              <option  value="BD" <?php if($lensResArr[$z]['mr_os_prism']=='BD')echo 'selected';?>>BD</option>
                              <option  value="BU" <?php if($lensResArr[$z]['mr_os_prism']=='BU')echo 'selected';?>>BU</option>
                            </select> -->/                            

							<input readonly type="text" name="lens_mr_os_splash_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['mr_os_splash']; ?>" />
							&nbsp;

							<!--  <select name="lens_mr_os_splash_<?php echo $y; ?>" id="lens_mr_os_splash_<?php echo $y; ?>" style="width:50px" class="rx_cls" >
														  <option  value=""></option>
														  <?php echo prismNumbers($lensResArr[$z]['mr_os_splash']); ?>
														</select>
							 -->
							 
							<input readonly type="text" name="lens_mr_os_sel_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['mr_os_sel']; ?>" />
 
<!--                             <select name="lens_mr_os_sel_<?php echo $y; ?>" id="lens_mr_os_sel_<?php echo $y; ?>" style="width:50px" class="rx_cls" value="<?php echo $lensResArr[$z]['axis_os'];?>">
                              <option  value=""></option>
                              <option  value="BI" <?php if($lensResArr[$z]['mr_os_sel']=='BI')echo 'selected';?>>BI</option>
                              <option  value="BO" <?php if($lensResArr[$z]['mr_os_sel']=='BO')echo 'selected';?>>BO</option>
                            </select>
 -->                            </td>
                    </tr>                                
                    <tr class="even"> 
						<td>
						<div style="float:left;">
							<div style="width:75px; float:left;">DPD</div> 
							<input readonly type="text" name="lens_dpd_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['dist_pd_od'];?>" /> / 
							<input readonly type="text" name="lens_dpd_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['dist_pd_os'];?>" />
                            <span class="mr_lr">NPD</span>
							<input readonly type="text" name="lens_npd_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['near_pd_od'];?>" /> /	<input readonly type="text" name="lens_npd_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['near_pd_os'];?>" />
							                             
					   </div>     			
					   <div style="float:left; margin:0 0 0 0px;">                
							<span class="mr_lr">Seg</span> 
							<input readonly type="text" name="lens_seg_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['seg_od'];?>" /> / 
							<input readonly type="text" name="lens_seg_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['seg_os'];?>" /> 
							<span class="mr_lr">Add</span>
							<input readonly type="text" name="lens_add_od_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['add_od'];?>" /> /
							<input readonly type="text" name="lens_add_os_<?php echo $y; ?>" style="width:100px;" value="<?php echo $lensResArr[$z]['add_os'];?>" />			                
					   </div>   
						</td>
                    </tr>
                    <tr> 
                        <td>
                        <input type="checkbox" <?php if($lensResArr[$z]['ship_home_chk']=="1") { echo "checked"; } ?> value="1" name="lens_ship_home_<?php echo $y; ?>" /> Ship Home &nbsp; 
                        <input type="checkbox"  value="1" name="lens_pu_loc_<?php echo $y; ?>" id="lens_pu_loc_<?php echo $y; ?>" /> P/U Location &nbsp;
                        <select name="lens_location_id_<?php echo $y; ?>" id="lens_location_id_<?php echo $y; ?>" style="width:120px;" onChange="javascript:sel_location_chk('lens_pu_loc_',<?php echo $y; ?>);">
                        <option value=""></option>
                        <?php 
                            $loc_qry = imw_query("select * from in_location where del_status='0' order by loc_name asc");								
                             while($loc_row = imw_fetch_assoc($loc_qry))                                  
                        { ?>
                        <option <?php if($lensResArr[$z]['location_id']==$loc_row['id']) { echo "selected"; } ?> value="<?php echo $loc_row['id']; ?>"><?php echo $loc_row['loc_name']; ?></option>	
                         <?php } ?>
                        </select>
                        </td>
                    </tr>
                </table>
                
              <?php $y++; }
			  
			  	echo "<script type='text/javascript'>show_hide_tbl('lens_contact_lens_hide');</script>";
			   ?>                    	
                  </td>
            </tr>
			<?php } else { ?>
			<tr><td align="center">No record</td></tr>
			<?php } ?>
      </table>
	  
	  <div class="listheading" id="contact_lens_pres">
           <div style="width:900px; float:left">Contact Lens Prescription</div>
       </div>
		
	  <table class="table_collapse table_cell_padd5" border="0" id="contact_lens_pres_hide" style="display:none;">
	  <?php if(count($clLensResArr)>0) { ?>
            <tr>
              <td style="width:900px;" valign="top">                  
                  
              <?php 
			  $clLensResTotal=count($clLensResArr);			  
			  $d=1;
			  for($v=0;$v<$clLensResTotal;$v++){ 

			  $lensnamepreq = imw_query("select item_name from in_order_details where id = '".$clLensResArr[$v]['det_order_id']."'");
			  $lensnamepreqrow = imw_fetch_assoc($lensnamepreq);
				?>
                 <table class="table_collapse table_cell_padd5" border="0">
           			<tr> 
                   	 	 <td class="reportHeadBG1">Contact Lens :- <?php echo $lensnamepreqrow['item_name']; ?></td>         
					</tr>
                        <tr class="even"> 
                            <td>
                            <div style="width:75px; float:left;"><span class="blueColor" style="font-weight:bold;">OD&nbsp;</span>Sph</div> 
                            <input readonly type="text" name="cl_sphere_od_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['sphere_od'];?>" /> <span class="mr_lr">CYL</span>
                            <input readonly type="text" name="cl_cylinder_od_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['cylinder_od'];?>" /> <span class="mr_lr">Axis</span> 
                            <input readonly type="text" name="cl_axis_od_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['axis_od'];?>" /> <span class="mr_lr">Add</span>
                            <input readonly type="text" name="cl_add_od_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['add_od'];?>" /><span class="mr_lr">Base</span>
                             <input readonly type="text" name="cl_base_od_<?php echo $d; ?>" style="width:100px;"  value="<?php echo $clLensResArr[$v]['base_od'];?>"/><span class="mr_lr">Diam</span>
                             <input readonly type="text" name="cl_diameter_od_<?php echo $d; ?>" style="width:100px;"  value="<?php echo $clLensResArr[$v]['diameter_od'];?>" />
                            </td>
                        </tr>
                        <tr> 
                            <td>
                            <div style="width:75px; float:left;"><span class="greenColor" style="font-weight:bold;">OS&nbsp;</span>Sph</div> 
                            <input readonly type="text" name="cl_sphere_os_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['sphere_os'];?>" /> <span class="mr_lr">CYL</span>
                            <input readonly type="text" name="cl_cylinder_os_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['cylinder_os'];?>" /> <span class="mr_lr">Axis</span> 
                            <input readonly type="text" name="cl_axis_os_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['axis_os'];?>" /> <span class="mr_lr">Add</span> 
                            <input readonly type="text" name="cl_add_os_<?php echo $d; ?>" style="width:100px;" value="<?php echo $clLensResArr[$v]['add_os'];?>" /><span class="mr_lr">Base</span>
							 <input readonly type="text" name="cl_base_os_<?php echo $d; ?>" style="width:100px;"  value="<?php echo $clLensResArr[$v]['base_os'];?>" /><span class="mr_lr">Diam</span>
		 					 <input readonly type="text" name="cl_diameter_os_<?php echo $d; ?>" style="width:100px;"  value="<?php echo $clLensResArr[$v]['diameter_os'];?>" />
                            </td>
                        </tr>                               
                        <tr class="even"> 
                            <td>
                           <input type="checkbox" <?php if($clLensResArr[$v]['ship_home_chk']=="1") { echo "checked"; } ?>  value="1" name="cl_ship_home_<?php echo $d; ?>" /> Ship Home &nbsp;
                        	<input type="checkbox"  value="1" name="cl_pu_loc_<?php echo $d; ?>" id="cl_pu_loc_<?php echo $d; ?>" /> P/U Location &nbsp;
                            
                            <select name="cl_location_id_<?php echo $d; ?>" id="cl_location_id_<?php echo $d; ?>"  style="width:120px;" onChange="javascript:sel_location_chk('cl_pu_loc_',<?php echo $d; ?>);">
                            <option value=""></option>
                            <?php 
								$loc_qry = imw_query("select * from in_location where del_status='0' order by loc_name asc");								 
								while($loc_row = imw_fetch_assoc($loc_qry))                                  
							{ ?><option <?php if($clLensResArr[$v]['location_id']==$loc_row['id']) { echo "selected"; } ?> value="<?php echo $loc_row['id']; ?>"><?php echo $loc_row['loc_name']; ?></option>	
                   		   <?php }	?>
                            </select>
                            </td>
                        </tr>
                    </table> 
               <?php $d++; } ?>                     	
                  </td>
            </tr>
			<?php 
				echo "<script type='text/javascript'>show_hide_tbl('contact_lens_pres_hide');</script>";
	 			} else { ?>
	  		<tr><td align="center">No record</td></tr>
	 		<?php } ?>
      </table>
      <?php
	    $sel_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and del_status='0' and module_type_id!='8' order by id asc");
		$top_cont=0;
		while($sel_order_row=imw_fetch_array($sel_qry))
		{
			$top_cont++;
			$sel_order_data[$top_cont]=$sel_order_row;
			$sel_order_module_data[$sel_order_row['module_type_id']][]=$sel_order_row;
		}
	  ?>
      		<div class="listheading mt2" id="frame_hide_show">Frames</div>
            <div id="frame_hide_show_id" style="display:none;">
                <table class="table_collapse table_cell_padd5" border="0">           
                    <?php
					   $top_cont=0;
                       if(count($sel_order_module_data[1])>0){
						   for($i=1;$i<=count($sel_order_data);$i++){
							   if($sel_order_data[$i]['module_type_id']==1){
								$top_cont=$i;
								$sel_order=$sel_order_data[$i];
								$sel_order_ord="";
								$sel_order_rec="";
								$sel_order_notif="";
								$sel_order_disp="";
								$frame_ordered_chk="";
								$frame_received_chk="";
								$frame_notified_chk="";
								$frame_dispensed_chk="";
						?>
                                <tr>
                                    <td colspan="2" class="reportHeadBG1">
                                    
                                                   
                    <?php 
					
					//print_r($sel_order);
					
					$pof_check_val = $sel_order['pof_check'];
					
					if($pof_check_val==1)
					{
					

				
					$pofdetailqry = imw_query("select * from in_frame_pof where order_detail_id = '".$sel_order['id']."'");
					$pofROW = imw_fetch_assoc($pofdetailqry);			
					}
					?>
                                    
                                <div style="float:left;">
								<?php echo "Frames :- ".ucfirst($sel_order['item_name']); ?>
                                </div>
                                <?php
								
								$vendornme = imw_query("select vendor_id from in_item where id = '".$sel_order['item_id']."'");
								$vendorrow = imw_fetch_assoc($vendornme);
								$vendorQry = imw_query("select vendor_name  from in_vendor_details where id = '".$vendorrow['vendor_id']."'");
								$vendorName = imw_fetch_assoc($vendorQry);
								?>
								<?php if($vendorName['vendor_name']!=""){ ?>
								<div style="float:left;position:relative; left:250px;">Vendor Name :- <?php echo $vendorName['vendor_name']; ?></div><?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                  <td width="57%">
                                    <table border="0" class="table_collapse table_cell_padd5">
                                        <tr> 
                                            <td colspan="2">
                                            
                                            <table class="table_collapse table_cell_padd5" border="0" cellpadding="0" cellspacing="0">
                                                <tr class="even">
                                                <td width="200">
                                                

                                                    <input type="hidden" name="in_add_<?php echo $top_cont; ?>" id="in_add_<?php echo $top_cont; ?>" value="<?php echo $pof_check_val; ?>" />
                                                    
                                                                                                    
                                                                                                    
                                                    <input type="text" readonly name="pof_manufacturer_id_<?php echo $top_cont; ?>" id="pof_manufacturer_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "block"; } else { echo "none"; } ?>; width:180px;" value="<?php echo $pofROW['manufacturer']; ?>" />
                                                                                                    
                                                    <?php
                                                    $manuFacOptions='';
                                                    $manufacNames = '';
                                                    foreach($arrManufac as $id => $manufacName)
                                                    {
                                                        if($id==$sel_order['manufacturer_id'])
                                                        {
                                                             $manufacNames = $manufacName; 
                                                        } 
                                                    } ?>
                                                    
                                                    <input type="text" readonly style="width:180px; display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>;" value="<?php echo $manufacNames; ?>" />
                                                                                        
                                                    <!--  <select name="manufacturer_id_<?php echo $top_cont; ?>" id="manufacturer_id__<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>; width:150px;" >
                                                    <option  value="">Select</option>
                                                    <?php
                                                    /*$manuFacOptions='';
                                                    foreach($arrManufac as $id => $manufacName)
                                                    {
                                                         $sel=($id==$sel_order['manufacturer_id'])? 'selected': '';
                                                         $manuFacOptions.='<option  value="'.$id.'" '.$sel.'>'.$manufacName.'</option>';
                                                    }
                                                    echo $manuFacOptions;*/
                                                    ?>
                                                </select>  -->
                                                
                                                </td>
                                                <td width="63"><input readonly type="text" style="width:40px;" name="a_<?php echo $top_cont; ?>" id="a_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['a']; } else { echo $sel_order['a']; } ?>" /></td>
                                                <td width="63"><input readonly type="text" style="width:40px;" name="b_<?php echo $top_cont; ?>" id="b_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['b']; } else { echo $sel_order['b']; } ?>" /></td>
                                                <td width="63"><input readonly type="text" style="width:40px;" name="ed_<?php echo $top_cont; ?>" id="ed_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['ed']; } else { echo $sel_order['ed']; } ?>" /></td>
                                                <td width="63"><input readonly type="text" style="width:40px;" name="dbl_<?php echo $top_cont; ?>" id="dbl_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['dbl']; } else { echo $sel_order['dbl']; } ?>" /></td>
                                                <td width="63"><input readonly type="text" style="width:40px;" name="temple_<?php echo $top_cont; ?>" id="temple_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['temple']; } else { echo $sel_order['temple']; } ?>" /></td>
                                                <td width="63"><input readonly type="text" style="width:40px;" name="bridge_<?php echo $top_cont; ?>" id="bridge_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['bridge']; } else { echo $sel_order['bridge']; } ?>" /></td>
                                                <td width="100"><input readonly type="text" style="width:40px;" name="fpd_<?php echo $top_cont; ?>" id="fpd_<?php echo $top_cont; ?>" value="<?php if($pof_check_val==1){ echo $pofROW['fpd']; } else { echo $sel_order['fpd']; } ?>" /></td>
                                                </tr>                        
                                                <tr class="pos_label"> 
                                                    <td width="130"><div style="width:120px;">Manufacturer</div>
                                                    </td>
                                                    <td width="63">A</td>
                                                    <td width="63">B</td>
                                                    <td width="63">ED</td>
                                                    <td width="63">DBL</td>
                                                    <td width="63">Temple</td>
                                                    <td width="63">Bridge</td>
                                                    <td width="319">FPD</td>
                                                </tr>
                                            </table>                                                                        
                                            </td>
                                        </tr>                                
                                        <tr class="even"> 
                                            <td width="50%" colspan="2">
                                                <div style="float:left;width:192px; margin-right:5px;">
                                                <input type="text" readonly name="pof_brand_id_<?php echo $top_cont; ?>" id="pof_brand_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "block"; } else { echo "none"; } ?>; width:183px;" value="<?php echo $pofROW['brand']; ?>" />                                                                            
                                                                           
                                                <?php
                                                
                                                
                                                $frame_source="";
                                                $frameBrandOpts='';                                    
                                                $sql = "SELECT frame_source,id FROM in_frame_sources WHERE del_status = 0 and id = '".$sel_order['brand_id']."' ORDER BY frame_source ASC";
                                                $res = imw_query($sql);
                                                while($row = imw_fetch_assoc($res))
                                                {
                                                    $frame_source = $row['frame_source'];
                                                }
                                                ?>
                                                <input type="text" readonly style="width:183px; display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>;" value="<?php echo $frame_source; ?>" />
                                                
                                                                     <!-- <select name="brand_id_<?php echo $top_cont; ?>" id="brand_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>; width:130px;" onChange="javascript:get_brand_style(this.value,'0');">
                                                <option  value="">Select</option>
                                                <?php
                                              /*  $frameBrandOpts='';                                    
                                                $sql = "SELECT frame_source,id FROM in_frame_sources WHERE del_status = 0 ORDER BY frame_source ASC";
                                                $res = imw_query($sql);
                                                while($row = imw_fetch_assoc($res)){
                                                    $sel=($row['id']==$sel_order['brand_id'])? 'selected': '';
                                                    $frameBrandOpts.='<option  value="'.$row['id'].'" '.$sel.'>'.$row['frame_source'].'</option>';
                                                }
                                                echo $frameBrandOpts;*/
                                                ?>
                                                </select> -->
                                                </div>

                                                <div style="float:left;width:200px; margin-right:5px;">
                                                <input type="text" readonly name="pof_style_id_<?php echo $top_cont; ?>" id="pof_style_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "block"; } else { echo "none"; } ?>; width:180px;" value="<?php echo $pofROW['style']; ?>" />
                                                 
                                                 
												<?php
                                                $style_name="";
                                                $rsFrame = imw_query("select * from in_frame_styles where del_status<='1' and id = '".$sel_order['style_id']."' order by style_name");
                                                while($resFrame=imw_fetch_array($rsFrame)){ 
                                                $style_name = ucfirst($resFrame['style_name']);
                                                 }	?>
                                                
                                                <input type="text" readonly style="width:180px; display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>;" value="<?php echo $style_name; ?>" />
                                                 
                                                <!--<select name="style_id_<?php echo $top_cont; ?>" id="style_id_<?php echo $top_cont; ?>" style="width:130px; display:<?php if($sel_order['style_other']!="" || $pof_check_val==1){ echo "none"; } else { echo "block"; }?>;">
                                                <option  value="">Select</option>
                                                <?php
                                                      $rsFrame = imw_query("select * from in_frame_styles where del_status<='1' order by style_name");
                                                      while($resFrame=imw_fetch_array($rsFrame)){?>
                                                        <option  <?php if(strtolower($resFrame['id'])==$sel_order['style_id']){ echo "selected"; } ?> value="<?php echo $resFrame['id']; ?>"><?php echo ucfirst($resFrame['style_name']); ?></option>	
                                                <?php }	?>
                                               
                                                </select>  -->
                                              
                                                <!--<input type="text" style="width:115px; float:left; display:<?php if($sel_order['style_other']!=""){ ?>block<?php } else { ?>none<?php } ?>;" name="other_style_<?php echo $top_cont; ?>" id="other_style_<?php echo $top_cont; ?>" value="<?php echo $sel_order['style_other']; ?>" />
                                               
                                                <img id="otherback" style="float:left; display:<?php if($sel_order['style_other']!=""){ ?>block<?php } else { ?>none<?php } ?>; cursor:pointer; position:relative; top:7px; left:5px;" src="../../images/icon_back.png" />-->
                                                </div>

                                                <div style="float:left;width:170px; margin-right:5px;">
													<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
													<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
													&nbsp;
													<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>

                                                </div>
                                            </td>
                                        </tr>      
                                        <tr class="pos_label"> 
                                            <td colspan="2" valign="top" class="module_label">
                                            <div style="float:left;width:192px; margin-right:5px;">Brand</div>
											<div style="float:left;width:200px; margin-right:5px;">Style</div>
                                            <div style="float:left;width:152px; margin-right:5px;">Received</div>
                                            </td>
                                        </tr>                                                          
                                        <tr class="even"> 
                                            <td>
                                            
                                            <input type="hidden" readonly name="pof_color_id_<?php echo $top_cont; ?>" id="pof_color_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "block"; } else { echo "none"; } ?>; width:142px;" value="<?php echo $pofROW['color']; ?>" />
                                                                    
                               <!--             <select name="color_id_<?php echo $top_cont; ?>" id="color_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>; width:150px;">
                                             <option value=""></option>
                                            <?php  $rows="";
                                            $rsColor = imw_query("select * from in_frame_color where del_status='0' order by color_name asc");
                                            
                                            while($resColor=imw_fetch_array($rsColor))
                                            { 
                                             $sel=($resColor['id']==$sel_order['color_id'])? 'selected': '';
                                            ?>
                                                <option value="<?php echo $resColor['id']; ?>" <?php echo $sel;?>><?php echo ucfirst($resColor['color_name']); ?></option>	
                                            <?php }	?>                        
                                            </select> -->
                                  
											<input type="text" style="width:565px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
                                            </td>
                                            <td>&nbsp;
												
                                            </td>
                                        </tr>
                                        <tr class="pos_label"> 
                                            <td width="50%" valign="top" class="module_label" colspan="2">
                                          		Notes
                                            </td>
                                        </tr>                                                          
                                        </table>
                                      </td>
                                      <td width="43%">
                                        <table class="table_collapse table_cell_padd5" border="0">                                
                                        <tr class="even"> 
                                            <?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
                                            <td width="7%"><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
                                            <td width="43%">
                                            <input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');" /></td>    
                                            
											<td width="7%">
												 <input type="checkbox" class="fl" name="lab_id_chk_<?php echo $top_cont; ?>" id="lab_id_chk_<?php echo $top_cont; ?>" <?php if($sel_order['lab_id']>0){echo "checked";} ?>/> 
											</td>
											<td width="43%">
                                            <select class="fl" name="lab_id_<?php echo $top_cont; ?>" id="lab_id_<?php echo $top_cont; ?>" style="width:120px;" onChange="javascript:sel_location_chk('lab_id_chk_',<?php echo $top_cont; ?>);">
                                            <option value=""></option>
                                                <?php 
                                                $lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
                                                while($lab_row = imw_fetch_assoc($lab_qry)){?>
                                                    <option <?php if($lab_row['id']==$sel_order['lab_id']){ echo "selected='selected'";  }?> value="<?php echo $lab_row['id']; ?>"><?php echo $lab_row['lab_name']; ?></option>	
                                                <?php }	?>
                                            </select>
                                            </td>   
                                        </tr>
                                        <tr class="pos_label"> 
                                            <td colspan="2">Ordered</td>
											<td colspan="2">Sent to Lab</td>
                                        </tr> 
                                        <tr  class="even"> 
                                            <?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
                                            <td><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
                                            <td>
                                            <input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
                                            <?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
                                            <td><input type="checkbox" name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
                                            <td>
                                            <input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     					
                                            </tr>
                                        <tr class="pos_label"> 
                                            <td colspan="2">Notified</td>
                                            <td colspan="2">Dispensed</td>
                                        </tr>                                                
                                        <tr class="even">
                                            <td colspan="4">
												<input type="text" readonly name="pof_shape_id_<?php echo $top_cont; ?>" id="pof_shape_id_<?php echo $top_cont; ?>" style="display:<?php if($pof_check_val==1){ echo "block"; } else { echo "none"; } ?>; width:180px;" value="<?php echo $pofROW['shape']; ?>" /> 
                                                
											  <?php
													$shape_id="";
													$shape_id=$sel_order['shape_id'];
													$rsShape = imw_query("select * from in_frame_shapes where del_status<='1' and id='$shape_id' order by shape_name");
													$resShape=imw_fetch_array($rsShape); 
												?>
                                                   <input type="text" readonly style="width:180px; display:<?php if($pof_check_val==1){ echo "none"; } else { echo "block"; } ?>;" value="<?php echo ucfirst($resShape['shape_name']); ?>" />
                                            </td>
                                        </tr>
                                        <tr class="pos_label">
                                            <td colspan="4">
												Shape
                                            </td>
                                        </tr>                                
                                     </table>  
                                </td>
                            </tr>
					<?php 
						} ?>
					<script>
					$(document).ready(function(e) {
						var manu_id = '<?php echo $sel_order['manufacturer_id']; ?>';
						var brand_id = '<?php echo $sel_order['brand_id']; ?>';
						var style_id = '<?php echo $sel_order['style_id']; ?>';
						var num = '<?php echo $top_cont; ?>';
						//get_manufacture_brand(manu_id,brand_id,num);
						//get_brand_style(brand_id,style_id,num);	
					});
					</script>
                       <?php $top_cont=0;
                       if(count($sel_order_module_data[2])>0){
						   for($k=1;$k<=count($sel_order_data);$k++){
							   if($sel_order_data[$k]['module_type_id']==2 && $sel_order_data[$k]['lens_frame_id']==$sel_order_data[$i]['id']){
								$top_cont=$k;
								$sel_order=$sel_order_data[$k];
								$sel_order_ord="";
								$sel_order_rec="";
								$sel_order_notif="";
								$sel_order_disp="";
								$frame_ordered_chk="";
								$frame_received_chk="";
								$frame_notified_chk="";
								$frame_dispensed_chk="";
						?>
						<tr>
							<td colspan="2" class="reportHeadBG1">
								<?php echo "Lenses :- ".ucfirst($sel_order['item_name']); ?>
							</td>
						</tr>
						<tr>
						<td>
							<table class="table_collapse table_cell_padd5" border="0">
								<tr  class="even"> 
									<td style="width:200px;">
<?php
$rows="";
$lensTypeRs = imw_query("select * from in_lens_type where del_status='0' order by type_name asc");
while($lensTypeRes=imw_fetch_array($lensTypeRs))
{  	
	if($lensTypeRes['id']==$sel_order['type_id'])
	{
		
		$type_name = $lensTypeRes['type_name'];
	}
}	 
?>

<input type="text" style="width:180px;" readonly name="readonly_<?php echo $top_cont; ?>" id="type_id_<?php echo $top_cont; ?>" value="<?php echo $type_name; ?>" />
									</td>
									<td style="width:200px;">

									<?php 
										  $rows="";
										  $lensMatRs= imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
										  while($lensMatRes=imw_fetch_array($lensMatRs))
										  { 
										   if($lensMatRes['id']==$sel_order['material_id']){ 
											   $lensMatiralRes = $lensMatRes['material_name'];
											   }
 }	?>
 
 <input type="text" style="width:180px;" readonly value="<?php echo $lensMatiralRes; ?>" />

                                    
<!--									 <select name="material_id_<?php echo $top_cont; ?>" id="material_id_<?php echo $top_cont; ?>" style="width:125px;">
									 <option value="">Please Select</option>
									<?php 
										  $rows="";
										  $lensMatRs= imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
										  while($lensMatRes=imw_fetch_array($lensMatRs))
										  { 
										   $sel=($lensMatRes['id']==$sel_order['material_id'])? 'selected': '';
									 ?>
											<option value="<?php echo $lensMatRes['id']; ?>" <?php echo $sel; ?>><?php echo $lensMatRes['material_name']; ?></option>	
									<?php }	?>
									</select>
 -->									
                                    </td>
									<td style="width:220px;">
									<div style="float:right; width:215px;">
                                  		
<?php $qr = imw_query("select name from in_item where id = '".$sel_order['lens_selection_id']."'");
if(imw_num_rows($qr)>0)
{
	$qrow = imw_fetch_array($qr);
	$names = $qrow['name'];
}
?>
                                    
<input type="text" style="width:180px;" readonly name="lens_item_name_<?php echo $top_cont; ?>" id="lens_item_name_<?php echo $top_cont; ?>" value="<?php echo ucfirst($sel_order['item_name']); ?>" />


                                    
<!--									 <select name="color_id_<?php echo $top_cont; ?>" id="color_id_<?php echo $top_cont; ?>" style="width:125px;">
									 <option value="">Please Select</option>
									<?php 
										  $rows="";
										  $lensColorRs= imw_query("select * from in_lens_color where del_status='0' order by color_name asc");
										  while($lensColorRes=imw_fetch_array($lensColorRs))
										  { 
										  $sel=($lensColorRes['id']==$sel_order['color_id'])? 'selected': '';
									?>
											<option value="<?php echo $lensColorRes['id']; ?>" <?php echo $sel;?>><?php echo $lensColorRes['color_name']; ?></option>	
									<?php }	?>
									</select> -->
                                    
                                    </div>
                                    
                                    <!--<input type="text" style="width:115px;" name="item_name_<?php echo $top_cont; ?>" id="item_name_<?php echo $top_cont; ?>" value="<?php echo $sel_order['item_name']; ?>" /> -->
                                    
                                    </td>
							  </tr>
								<tr class="pos_label"> 
									<td>Seg Type</td>
									<td>Material</td>
									<td>Name</td>
								</tr>                                
								<tr  class="even"> 
									<td colspan="2">
                               <?php 
$transition_name="";
$progressive_name="";
$ar_name="";
$tint_type="";
$polarized_name="";
$edge_name="";
$lens_other="";
$uv400="";
$pgx="";
$field="";
$ds="";
$qry="";
if($sel_order['transition_id']>0)
{
	$field .= " in_lens_transition.transition_name , ";
	$qry .= "inner join in_lens_transition on in_lens_transition.id = '".$sel_order['transition_id']."'";
	$ds .= " in_lens_transition.del_status = 0 and ";
}

if (preg_match("/progressive/i", strtolower($type_name))) {
	if($sel_order['progressive_id']>0)
	{
		$field .= " in_lens_progressive.progressive_name , ";
		$qry .= " inner join in_lens_progressive on in_lens_progressive.id = '".$sel_order['progressive_id']."'";
		$ds .= " in_lens_progressive.del_status = 0 and ";	
	}
}

if($sel_order['a_r_id']>0)
{
	$field .= " in_lens_ar.ar_name , ";
	$qry .= " inner join in_lens_ar on in_lens_ar.id = '".$sel_order['a_r_id']."'";
	$ds .= " in_lens_ar.del_status = 0 and ";	
}

if($sel_order['polarized_id']>0)
{
	$field .= " in_lens_polarized.polarized_name , ";
	$qry .= " inner join in_lens_polarized on in_lens_polarized.id = '".$sel_order['polarized_id']."'";
	$ds .= " in_lens_polarized.del_status = 0 and ";
}

if($sel_order['edge_id']>0)
{
	$field .= " in_lens_edge.edge_name , ";
	$qry .= " inner join in_lens_edge on in_lens_edge.id = '".$sel_order['edge_id']."'";
	$ds .= " in_lens_edge.del_status = 0 and ";
}

if($sel_order['tint_id']>0)
{
	$field .= " in_lens_tint.tint_type , ";
	$qry .= " inner join in_lens_tint on in_lens_tint.id = '".$sel_order['tint_id']."'";
	$ds .= " in_lens_tint.del_status = 0 and ";
}
//echo $ds;
	$field = substr($field,0,-2);
	$ds = substr($ds,0,-4);
	
$lens_opt_qry = "select in_order_details.id,$field from in_order_details $qry where in_order_details.id='".$sel_order['id']."' and $ds";


$lens_opt = imw_query($lens_opt_qry);	
while($lensOptionRow = imw_fetch_assoc($lens_opt))
{ 
	if($lensOptionRow['transition_name']!=""){ $transition_name = $lensOptionRow['transition_name']."; "; }
	if($lensOptionRow['ar_name']!=""){ $ar_name = $lensOptionRow['ar_name']."; "; }
	if($lensOptionRow['tint_type']!=""){ $tint_type = $lensOptionRow['tint_type']."; "; }
	if($lensOptionRow['progressive_name']!=""){ $progressive_name = $lensOptionRow['progressive_name']."; "; }
	if($lensOptionRow['polarized_name']!=""){ $polarized_name = $lensOptionRow['polarized_name']."; "; }
	if($lensOptionRow['edge_name']!=""){ $edge_name = $lensOptionRow['edge_name']."; "; }
} 
					if($sel_order['lens_other']!="")
						{
							$lens_other = $sel_order['lens_other']."; ";
						}
						
						if($sel_order['uv400']==1)
						{
							$uv400="uv400;";
						}
						
						if($sel_order['pgx']==1)
						{
							$pgx="pgx;";
						}
						
							?>
                            
                            <input type="text" readonly style="width:379px;" name="lens_item_option" value="<?php echo $transition_name.$progressive_name.$ar_name.$tint_type.$polarized_name.$edge_name.$lens_other.$uv400.$pgx; ?>" />
									</td>
									<td>
										<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
									<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
									&nbsp;
									<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
									</td>
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">
									List of lens options
									</td>
									<td>
										Received
									</td>
								</tr>                                                                
								<tr class="even">
									<td colspan="3">
										<input type="text" style="width:575px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
									</td>
								<!--	<td >
									<select class="fl" name="lab1_id_<?php echo $top_cont; ?>" id="lab1_id_<?php echo $top_cont; ?>" style="width:130px; margin-left:10px;">
									<option value=""></option>
                                
										<?php 
										$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
										while($lab_row = imw_fetch_assoc($lab_qry)){?>
											<option <?php if($lab_row['id']==$sel_order['lab_id']){ echo "selected='selected'";  }?> value="<?php echo $lab_row['id']; ?>"><?php echo $lab_row['lab_name']; ?></option>	
										<?php }	?>
									</select> 
									</td>      -->                              
								</tr>
								<tr class="pos_label"> 
									<td width="50%" valign="top" class="module_label" colspan="3">
									Notes
									</td>
								</tr>                                                          
						  </table>
						  </td>
						  <td valign="top">
							  <table class="table_collapse table_cell_padd5" border="0">                                
								<tr  class="even"> 
									<?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
									<td width="7%"><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td> 
									   
									<td width="7%">
										<input type="checkbox" style="float:left;" class="fl" name="lab_id_chk_<?php echo $top_cont; ?>" id="lab_id_chk_<?php echo $top_cont; ?>" <?php if($sel_order['lab_id']>0){echo "checked";} ?> />
									</td>
									<td width="43%">
										<select class="fl" name="lab_id_<?php echo $top_cont; ?>" id="lab_id_<?php echo $top_cont; ?>" style="width:125px; float:left;" onChange="javascript:sel_location_chk('lab_id_chk_',<?php echo $top_cont; ?>);">
										<option value=""></option>
										
										<?php 
										$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
										while($lab_row = imw_fetch_assoc($lab_qry)){?>
										<option <?php if($lab_row['id']==$sel_order['lab_id']){ echo "selected='selected'";  }?> value="<?php echo $lab_row['id']; ?>"><?php echo $lab_row['lab_name']; ?></option>	
										<?php }	?>
										</select>
									</td>     
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">Ordered</td>
									<td colspan="2">Sent to Lab</td>
								</tr> 
								<tr  class="even"> 
									<?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
									<td><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
									<?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
									<td><input type="checkbox" alue="1"  name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
									<td>
										<input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
									</td> 
									</tr>
								<tr class="pos_label"> 
									<td colspan="2">Notified</td>
									<td colspan="2">Dispensed</td>
								</tr>                               
							 </table>  
							</td>                
						 </tr> 
						<?php }}
						echo "<script type='text/javascript'>show_hide_tbl('lens_hide_show_id');</script>";
						} 
					}
						echo "<script type='text/javascript'>show_hide_tbl('frame_hide_show_id');</script>";
					}else{ 
					?> 
                     <tr>
                        <td colspan="2" style="text-align:center;">
                            No record
                        </td>
                    </tr>
                    <?php } ?>
                </table>
             </div>   
            <div class="listheading mt2" id="lens_hide_show">Other Lenses</div>
            <div id="lens_hide_show_id" style="display:none;"> 
                <table class="table_collapse table_cell_padd5" border="0">              
                    <?php
                       $top_cont=0;
                       if(count($sel_order_module_data[2])>0){
						   for($i=1;$i<=count($sel_order_data);$i++){
							   if($sel_order_data[$i]['module_type_id']==2 && $sel_order_data[$i]['lens_frame_id']==0){
								$top_cont=$i;
								$sel_order=$sel_order_data[$i];
								$sel_order_ord="";
								$sel_order_rec="";
								$sel_order_notif="";
								$sel_order_disp="";
								$frame_ordered_chk="";
								$frame_received_chk="";
								$frame_notified_chk="";
								$frame_dispensed_chk="";
						?>
						<tr>
							<td colspan="2" class="reportHeadBG1">
								<?php echo "Lenses :- ".ucfirst($sel_order['item_name']); ?>
							</td>
						</tr>
						<tr>
						<td width="57%">
							<table class="table_collapse table_cell_padd5" border="0">
								<tr class="even"> 
									<td style="width:200px;">
<?php
$rows="";
$lensTypeRs = imw_query("select * from in_lens_type where del_status='0' order by type_name asc");
while($lensTypeRes=imw_fetch_array($lensTypeRs))
{  	
	if($lensTypeRes['id']==$sel_order['type_id'])
	{
		$type_name = $lensTypeRes['type_name'];
	}
}	 
?>

<input type="text" style="width:180px;" readonly name="readonly_<?php echo $top_cont; ?>" id="type_id_<?php echo $top_cont; ?>" value="<?php echo $type_name; ?>" />

									</td>
									<td style="width:200px;">
									<?php 
										  $rows="";
										  $lensMatRs= imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
										  while($lensMatRes=imw_fetch_array($lensMatRs))
										  { 
										   if($lensMatRes['id']==$sel_order['material_id']){ 
											   $lensMatiralRes = $lensMatRes['material_name'];
											} }	?>
 
 <input type="text" style="width:180px;" readonly value="<?php echo $lensMatiralRes; ?>" />

<!--									 <select name="material_id_<?php echo $top_cont; ?>" id="material_id_<?php echo $top_cont; ?>" style="width:125px;">
									 <option value="">Please Select</option>
									<?php 
										  $rows="";
										  $lensMatRs= imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
										  while($lensMatRes=imw_fetch_array($lensMatRs))
										  { 
										   $sel=($lensMatRes['id']==$sel_order['material_id'])? 'selected': '';
									 ?>
											<option  value="<?php echo $lensMatRes['id']; ?>" <?php echo $sel; ?>><?php echo $lensMatRes['material_name']; ?></option>	
									<?php }	?>
									</select>-->
 									
                                    </td>
									<td style="width:220px;">
                                	<div style="float:right; width:215px;">
									<?php $qr = imw_query("select name from in_item where id = '".$sel_order['lens_selection_id']."'");
									if(imw_num_rows($qr)>0)
									{
										$qrow = imw_fetch_array($qr);
										$names = $qrow['name'];
									}
									?>
																		
									<input type="text" style="width:180px;" readonly name="lens_item_name_<?php echo $top_cont; ?>" id="lens_item_name_<?php echo $top_cont; ?>" value="<?php echo ucfirst($sel_order['item_name']); ?>" />
									
																<!-- <select name="color_id_<?php echo $top_cont; ?>" id="color_id_<?php echo $top_cont; ?>" style="width:125px;">
																		 <option value="">Please Select</option>
																		 <?php 
																			  $rows="";
																			  $lensColorRs= imw_query("select * from in_lens_color where del_status='0' order by color_name asc");
																			  while($lensColorRes=imw_fetch_array($lensColorRs))
																			  { 
																			  $sel=($lensColorRes['id']==$sel_order['color_id'])? 'selected': '';
																		?>
																				<option value="<?php echo $lensColorRes['id']; ?>" <?php echo $sel;?>><?php echo $lensColorRes['color_name']; ?></option>	
																		<?php }	?>
																		</select>
									-->	
                                    </div>
                                    <!--<input type="text" style="width:115px;" name="item_name_<?php echo $top_cont; ?>" id="item_name_<?php echo $top_cont; ?>" value="<?php echo $sel_order['item_name']; ?>" /> -->
									</td>
							  </tr>
								<tr class="pos_label"> 
									<td>Seg Type</td>
									<td>Material</td>
									<td>Name</td>
								</tr>                                
								<tr class="even"> 
									<td colspan="2">
<?php 
$transition_name="";
$progressive_name="";
$ar_name="";
$tint_type="";
$polarized_name="";
$edge_name="";
$lens_other="";
$uv400="";
$pgx="";
$field="";
$ds="";
$qry="";
if($sel_order['transition_id']>0)
{
	$field .= " in_lens_transition.transition_name , ";
	$qry .= "inner join in_lens_transition on in_lens_transition.id = '".$sel_order['transition_id']."'";
	$ds .= " in_lens_transition.del_status = 0 and ";
}
if(preg_match("/progressive/i", strtolower($type_name))) {
	if($sel_order['progressive_id']>0)
	{
		$field .= " in_lens_progressive.progressive_name , ";
		$qry .= " inner join in_lens_progressive on in_lens_progressive.id = '".$sel_order['progressive_id']."'";
		$ds .= " in_lens_progressive.del_status = 0 and ";	
	}
}
if($sel_order['a_r_id']>0)
{
	$field .= " in_lens_ar.ar_name , ";
	$qry .= " inner join in_lens_ar on in_lens_ar.id = '".$sel_order['a_r_id']."'";
	$ds .= " in_lens_ar.del_status = 0 and ";	
}

if($sel_order['polarized_id']>0)
{
	$field .= " in_lens_polarized.polarized_name , ";
	$qry .= " inner join in_lens_polarized on in_lens_polarized.id = '".$sel_order['polarized_id']."'";
	$ds .= " in_lens_polarized.del_status = 0 and ";
}

if($sel_order['edge_id']>0)
{
	$field .= " in_lens_edge.edge_name , ";
	$qry .= " inner join in_lens_edge on in_lens_edge.id = '".$sel_order['edge_id']."'";
	$ds .= " in_lens_edge.del_status = 0 and ";
}

if($sel_order['tint_id']>0)
{
	$field .= " in_lens_tint.tint_type , ";
	$qry .= " inner join in_lens_tint on in_lens_tint.id = '".$sel_order['tint_id']."'";
	$ds .= " in_lens_tint.del_status = 0 and ";
}
//echo $ds;
$field = substr($field,0,-2);
$ds = substr($ds,0,-4);

$lens_opt_qry = "select in_order_details.id,$field from in_order_details $qry where in_order_details.id='".$sel_order['id']."' and $ds";
$lens_opt = imw_query($lens_opt_qry);	
while($lensOptionRow = imw_fetch_assoc($lens_opt))
{ 
	if($lensOptionRow['transition_name']!=""){ $transition_name = $lensOptionRow['transition_name']."; "; }
	if($lensOptionRow['ar_name']!=""){ $ar_name = $lensOptionRow['ar_name']."; "; }
	if($lensOptionRow['progressive_name']!=""){ $progressive_name = $lensOptionRow['progressive_name']."; "; }
	if($lensOptionRow['tint_type']!=""){ $tint_type = $lensOptionRow['tint_type']."; "; }
	if($lensOptionRow['polarized_name']!=""){ $polarized_name = $lensOptionRow['polarized_name']."; "; }
	if($lensOptionRow['edge_name']!=""){ $edge_name = $lensOptionRow['edge_name']."; "; }
} 
	if($sel_order['lens_other']!="")
	{
		$lens_other = $sel_order['lens_other']."; ";
	}
	
	if($sel_order['uv400']==1)
	{
		$uv400="uv400;";
	}
	
	if($sel_order['pgx']==1)
	{
		$pgx="pgx;";
	}
?>
                            <input type="text" readonly style="width:379px;" name="lens_item_option" value="<?php echo $transition_name.$progressive_name.$ar_name.$tint_type.$polarized_name.$edge_name.$lens_other.$uv400.$pgx; ?>" />
                            
									</td>
									<td>
										<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
									<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
									&nbsp;
									<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
									</td>
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">
										List of lens options
									</td>
									<td>
										Received
									</td>
								</tr>                                                                
								<tr  class="even"> 
									<td colspan="3">
										<input type="text" style="width:575px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
                               		 </td>
                        <!--        <td colspan="2">
									
                       <select class="fl" name="lab1_id_<?php echo $top_cont; ?>" id="lab1_id_<?php echo $top_cont; ?>" style="width:130px; margin-left:10px;">
									<option value=""></option>
										<?php 
										$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
										while($lab_row = imw_fetch_assoc($lab_qry)){?>
											<option <?php if($lab_row['id']==$sel_order['lab_id']){ echo "selected='selected'";  }?> value="<?php echo $lab_row['id']; ?>"><?php echo $lab_row['lab_name']; ?></option>	
										<?php }	?>
									</select> 
									</td>      -->                              

								</tr>
								<tr class="pos_label"> 
									<td valign="top" class="module_label" colspan="3">
										Note
									</td>
								</tr>                                                         
						  </table>
						  </td>
						  <td valign="top"  width="43%">
							  <table class="table_collapse table_cell_padd5" border="0">
								<tr class="even"> 
									<?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
									<td width="7%"><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>   
									
									<td width="7%">
										<input type="checkbox" style="float:left;" class="fl" name="lab_id_chk_<?php echo $top_cont; ?>" id="lab_id_chk_<?php echo $top_cont; ?>" <?php if($sel_order['lab_id']>0){echo "checked";} ?> />
									</td>
									<td width="43%">
									   <select class="fl" name="lab_id_<?php echo $top_cont; ?>" id="lab_id_<?php echo $top_cont; ?>" style="width:125px; float:left;" onChange="javascript:sel_location_chk('lab_id_chk_',<?php echo $top_cont; ?>);">
										<option value=""></option>
											<?php 
											$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
											while($lab_row = imw_fetch_assoc($lab_qry)){?>
												<option <?php if($lab_row['id']==$sel_order['lab_id']){ echo "selected='selected'";  }?> value="<?php echo $lab_row['id']; ?>"><?php echo $lab_row['lab_name']; ?></option>	
											<?php }	?>
										</select>
									</td> 
									     
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">Ordered</td>
									<td colspan="2">Sent to Lab</td>
								</tr> 
								<tr class="even"> 
									<?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
									<td><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
									<?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
									<td><input type="checkbox" alue="1"  name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     					</tr>
								<tr class="pos_label"> 
									<td colspan="2">Notified</td>
									<td colspan="2">Dispensed</td>
								</tr>
								                               
							 </table>  
							</td>                
						 </tr> 
						<?php }}
						echo "<script type='text/javascript'>show_hide_tbl('lens_hide_show_id');</script>";
					}else{ 
					?> 
                     <tr>
                        <td colspan="2" style="text-align:center;">
                            No record
                        </td>
                    </tr>
                    <?php } ?>
                    </table>
                </div>    
                <div class="listheading mt2" id="cl_hide_show">Contact Lenses</div>
                <div id="cl_hide_show_id" style="display:none;">
                    <table class="table_collapse table_cell_padd5" border="0"> 
                        <?php
                        $top_cont=0;
                       if(count($sel_order_module_data[3])>0){
						   for($i=1;$i<=count($sel_order_data);$i++){
							   if($sel_order_data[$i]['module_type_id']==3){
								$top_cont=$i;
								$sel_order=$sel_order_data[$i];
								  $sel_order_ord="";
								  $sel_order_rec="";
								  $sel_order_notif="";
								  $sel_order_disp="";
								  $frame_ordered_chk="";
								  $frame_received_chk="";
								  $frame_notified_chk="";
								  $frame_dispensed_chk="";
							?>
							<tr>
								<td colspan="2" class="reportHeadBG1" style="text-align:left;">
									<?php echo "Contact Lenses :- ".ucfirst($sel_order['item_name']); ?>
								</td>
							</tr>
							<tr>
							  <td width="57%">
									<table class="table_collapse table_cell_padd5" border="0">
										<tr class="even"> 
											<td width="200">
											<?php
											$cl_manuFacOptions='';
											$cl_manufacNames='';
											foreach($cl_arrManufac as $id => $cl_manufacName){
											 if($id==$sel_order['manufacturer_id']){ 
											 	$cl_manufacNames = $cl_manufacName;
											  }
											  } ?>
											 <input readonly type="text" value="<?php echo $cl_manufacNames; ?>" style="width:180px;"/> 
											                                            
<!--											<select name="manufacturer_id_<?php echo $top_cont; ?>" id="manufacturer_id_<?php echo $top_cont; ?>" style="width:120px;">
											<option  value="">Select</option>
											<?php
											$cl_manuFacOptions='';
											foreach($cl_arrManufac as $id => $cl_manufacName){
											 $sel=($id==$sel_order['manufacturer_id'])? 'selected': '';
											 $cl_manuFacOptions.='<option  value="'.$id.'" '.$sel.'>'.$cl_manufacName.'</option>';
											}
											echo $cl_manuFacOptions;
											 ?>
											</select>
 -->											</td>
											<td width="200">
<?php 
$rows="";
$rows = data("select * from in_contact_cat where del_status='0' and id = '".$sel_order['contact_cat_id']."' order by cat_name asc");
foreach($rows as $r)
{ 
	$cat_name = ucfirst($r['cat_name']);
} 
?>
<input readonly type="text" style="width:180px;" value="<?php echo $cat_name; ?>" />


<!--											<select name="contact_cat_id_<?php echo $top_cont; ?>" id="contact_cat_id_<?php echo $top_cont; ?>" style="width:120px;">
											 <option  value="">Please Select</option>
											 <?php $rows="";
												  $rows = data("select * from in_contact_cat where del_status='0' order by cat_name asc");
												  foreach($rows as $r)
												  { 
												  $sel=($r['id']==$sel_order['contact_cat_id'])? 'selected': '';
											?>
													<option  value="<?php echo $r['id']; ?>" <?php echo $sel;?>><?php echo ucfirst($r['cat_name']); ?></option>	
											<?php }	?>
											</select>
 -->											</td>
											<td width="220">
<?php 

$rows="";
$type_name="";
$rows = data("select * from in_type where del_status='0' and id = '".$sel_order['type_id']."' order by type_name asc");
foreach($rows as $r)
{ 
	$type_name = ucfirst($r['type_name']);
}
?>
<input readonly type="text" value="<?php echo $type_name; ?>" style="width:165px;">


<!--<select name="type_id_<?php echo $top_cont; ?>" id="type_id_<?php echo $top_cont; ?>" style="width:120px;">
<option  value="">Please Select</option>
<?php $rows="";
$rows = data("select * from in_type where del_status='0' order by type_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_order['type_id'])? 'selected': '';
?>
<option  value="<?php echo $r['id']; ?>" <?php echo $sel;?>><?php echo ucfirst($r['type_name']); ?></option>	
<?php }	?>
</select>
 -->											</td>
										</tr>                                
										<tr class="pos_label"> 
											<td>Manufacturer</td>
											<td>Wear Time</td>
											<td>Material</td>
										</tr>                                   
										<tr  class="even"> 
											<td>
<?php  												$brand_name="";
													$qry="";
													$qry = imw_query("select * from `in_contact_brand` where del_status='0' and id = '".$sel_order['brand_id']."' order by brand_name asc");
													while($rows = imw_fetch_array($qry))
													{
														 $brand_name=$rows['brand_name'];
													}
													?>
                                                    <input readonly type="text" value="<?php echo $brand_name; ?>" style="width:180px;" />
<!--												<select name="brand_id_<?php echo $top_cont; ?>" id="brand_id_<?php echo $top_cont; ?>" style="width:120px;">
												<option  value="">Please Select</option>
												  <?php  
													$qry="";
													$qry = imw_query("select * from `in_contact_brand` where del_status='0' order by brand_name asc");
													while($rows = imw_fetch_array($qry))
													{
														$sel=($rows['id']==$sel_order['brand_id'])? 'selected': ''; 
													?>
													<option  value="<?php echo $rows['id']; ?>" <?php echo $sel;?>><?php echo $rows['brand_name']; ?></option>
													<?php }	?> 
												</select> -->
											</td>
											<td><input readonly type="text" style="width:180px;" name="cl_item_name_<?php echo $top_cont; ?>" value="<?php echo ucfirst($sel_order['item_name']); ?>" /></td>
											<td>
												<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
									<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
									&nbsp;
									<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
											</td>
										</tr>
										<tr class="pos_label"> 
											<td>Brand</td>
											<td>Name</td>
											<td>Received</td>
										</tr>                                                                
										<tr class="even"> 
											<td colspan="3">
												<input type="text" style="width:575px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
											</td>
											<!--<td>
<?php 
$style_name="";
$rows="";
$rows = data("select * from in_contact_style where del_status='0' and id = '".$sel_order['style_id']."' order by style_name asc");
foreach($rows as $r)
{ 
$style_name = ucfirst($r['style_name']);
}
?>
<input readonly type="text" value="<?php echo $style_name; ?>" style="width:120px;">
											</td>
											<td colspan="2"><!--<input type="text" style="width:80px;" name="cl_qty" value="<?php echo $sel_order['qty']; ?>" /></td> -->
										</tr>
										<tr class="pos_label"> 
											<td>Notes<!--Color --></td>
											<!--<td>Style</td>-->
											<td colspan="2"><!--Quantity--></td> 
										</tr>
									   
									</table>
							  </td>
							 <td valign="top" width="43%">
								<table class="table_collapse table_cell_padd5" border="0">                                
								<tr  class="even"> 
									<?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
									<td width="7%"><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>  
									
									<td colspan="2">
										<?php 
										$supply_name="";
										$rows="";
										$rows = data("select * from in_supply where del_status='0' and id = '".$sel_order['supply_id']."' order by supply_name asc");
										foreach($rows as $r)
										{ 
										$supply_name = ucfirst($r['supply_name']);
										}
										?>
										<input readonly type="text" value="<?php echo $supply_name; ?>" style="width:140px;">
										
										
										
										<!--                                            <select name="supply_id_<?php echo $top_cont; ?>" id="supply_id_<?php echo $top_cont; ?>" style="width:120px;">
																						 <option  value="">Please Select</option>
																							<?php $rows="";
																								  $rows = data("select * from in_supply where del_status='0' order by supply_name asc");
																								  foreach($rows as $r)
																								  { 
																								   $sel=($r['id']==$sel_order['supply_id'])? 'selected': ''; 
																							?>
																									<option  value="<?php echo $r['id']; ?>" <?php echo $sel; ?>><?php echo ucfirst($r['supply_name']); ?></option>	
																							<?php }	?>
																						</select>
										 -->												<!--<select name="color_id_<?php echo $top_cont; ?>" id="color_id_<?php echo $top_cont; ?>" style="width:120px;">
																						 <option  value="">Please Select</option>
																							<?php $rows="";
																								  $rows = data("select * from in_color where del_status='0' order by color_name asc");
																								  foreach($rows as $r)
																								  { 
																								  $sel=($r['id']==$sel_order['color_id'])? 'selected': ''; 
																							?>
																							<option  value="<?php echo $r['id']; ?>" <?php echo $sel; ?>><?php echo ucfirst($r['color_name']); ?></option>	
																							<?php }	?>
																						</select> -->
									</td>  
									
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">Ordered</td>
									<td colspan="2">Supply</td>
								</tr> 
								<tr  class="even"> 
									<?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
									<td><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
									<?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
									<td width="7%"><input type="checkbox" name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
									</td>     					
                                </tr>
								<tr class="pos_label"> 
									<td colspan="2">Notified</td>
									<td colspan="2">Dispensed</td>
								</tr>                               
							 </table>  
						</td> 
					  </tr> 
					 <?php }}
						echo "<script type='text/javascript'>show_hide_tbl('cl_hide_show_id');</script>";
					}else{ 
					?> 
                     <tr>
                        <td colspan="2" style="text-align:center;">
                            No record
                        </td>
                    </tr>
                    <?php } ?>
                  </table>
              </div> 
			  <div class="listheading mt2" id="other_hide_show">Other Selection</div>
                <div id="other_hide_show_id" style="display:none;">
                    <table class="table_collapse table_cell_padd5" border="0"> 
                        <?php
                        $top_cont=0;
                       if(count($sel_order_module_data[5])>0 || count($sel_order_module_data[6])>0 || count($sel_order_module_data[7])>0){
						   for($i=1;$i<=count($sel_order_data);$i++){
							   if($sel_order_data[$i]['module_type_id']==5){
								$top_cont=$i;
								$sel_order=$sel_order_data[$i];
								  $sel_order_ord="";
								  $sel_order_rec="";
								  $sel_order_notif="";
								  $sel_order_disp="";
								  $frame_ordered_chk="";
								  $frame_received_chk="";
								  $frame_notified_chk="";
								  $frame_dispensed_chk="";
								  $sel_data_item="";
								  $get_data_item="";
							$sel_data_item = imw_query("select itm.num_size, ss.size_name, sm.measurment_name , itm.other, itm.type_desc from in_item as itm left join in_supplies_measurment as sm on sm.id=itm.measurment left join in_supplies_size as ss on ss.id=itm.char_size where itm.id='".$sel_order['item_id']."' and itm.module_type_id='".$sel_order['module_type_id']."'");
							$get_data_item = imw_fetch_array($sel_data_item);
							?>
							<tr>
								<td colspan="2" class="reportHeadBG1" style="text-align:left;">
									<?php echo "Supplies :- ".ucfirst($sel_order['item_name']); ?>
								</td>
							</tr>
							<tr>
							  <td width="57%">
									<table class="table_collapse table_cell_padd5" border="0">
										<tr class="even"> 
											<td width="200">

											<?php
											$cl_manuFacOptions='';
											$cl_manufacNames='';
											foreach($cl_arrManufac as $id => $cl_manufacName){
											 if($id==$sel_order['manufacturer_id']){ 
											 	$cl_manufacNames = $cl_manufacName;
											  }
											  } ?>
											 <input readonly type="text" value="<?php echo $cl_manufacNames; ?>" style="width:180px;" /> 
											</td>
											<td width="200">
												<input readonly type="text" style="width:180px;" value="<?php echo $get_data_item['size_name']; ?>" />
											</td>
											<td width="220">
											<input readonly type="text" value="<?php echo ucfirst($get_data_item['measurment_name']); ?>" style="width:165px;">
										</td>
										</tr>                                
										<tr class="pos_label"> 
											<td>Manufacturer</td>
											<td>Size</td>
											<td>Measurment</td>
										</tr>                                   
										<tr class="even"> 
											<td>
                                                <input readonly type="text" value="<?php echo $get_data_item['num_size']; ?>" style="width:180px;" />											
											</td>
											<td>
												<input readonly type="text" style="width:180px;" value="<?php echo $get_data_item['other']; ?>" />
											</td>
											<td>
												<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
												<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
												&nbsp;
												<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
											</td>
										</tr>
										<tr class="pos_label"> 
											<td>Size</td>
											<td>Other</td>
											<td>Received</td>
										</tr>                                                                
										<tr  class="even"> 
											<td colspan="3">
												<input type="text" style="width:575px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
											</td>
										</tr>
										<tr class="pos_label"> 
											<td colspan="3">Notes</td>
										</tr>
									   
									</table>
							  </td>
							 <td width="43%">
								<table class="table_collapse table_cell_padd5" border="0">                                
								<tr  class="even"> 
									<?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
									<td width="7%"><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>    
									<td colspan="2"></td>
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">Ordered</td>
									<td colspan="2">&nbsp;</td>
								</tr> 
								<tr  class="even"> 
									<?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
									<td><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
									<?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
									<td width="7%"><input type="checkbox" name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     					
                                    </tr>
								<tr class="pos_label"> 
									<td colspan="2">Notified</td>
									<td colspan="2">Dispensed</td>
								</tr>                                                
								<tr class="even">
									<td colspan="4">
										<input readonly type="text" value="<?php echo $get_data_item['type_desc']; ?>" style="width:400px;">
									</td>
								</tr>
								<tr class="pos_label">
									<td colspan="4">
										Description	
									</td>
								</tr>                                
							 </table>  
						</td> 
					  </tr> 
					 <?php }
					 if($sel_order_data[$i]['module_type_id']==6){
								$top_cont=$i;
								$sel_order=$sel_order_data[$i];
								  $sel_order_ord="";
								  $sel_order_rec="";
								  $sel_order_notif="";
								  $sel_order_disp="";
								  $frame_ordered_chk="";
								  $frame_received_chk="";
								  $frame_notified_chk="";
								  $frame_dispensed_chk="";
								  $sel_data_item="";
								  $get_data_item="";
							$sel_data_item = imw_query("select harcardous, type_desc from in_item where id='".$sel_order['item_id']."' and module_type_id='".$sel_order['module_type_id']."'");
							$get_data_item = imw_fetch_array($sel_data_item);
							?>
							<tr>
								<td colspan="2" class="reportHeadBG1" style="text-align:left;">
									<?php echo "Medicines :- ".ucfirst($sel_order['item_name']); ?>
								</td>
							</tr>
							<tr>
							  <td width="57%">
									<table class="table_collapse table_cell_padd5" border="0">
										<tr class="even"> 
											<td width="400">

											<?php
											$cl_manuFacOptions='';
											$cl_manufacNames='';
											foreach($cl_arrManufac as $id => $cl_manufacName){
											 if($id==$sel_order['manufacturer_id']){ 
											 	$cl_manufacNames = $cl_manufacName;
											  }
											  } ?>
											 <input readonly type="text" value="<?php echo $cl_manufacNames; ?>" style="width:350px;" />
											</td>
											<td width="220">
												<input readonly type="text" value="<?php if($get_data_item['harcardous']=="1"){ echo "YES"; } else { echo "NO"; } ?>" style="width:165px;" />
												
											</td>
										</tr>                               
										<tr class="pos_label"> 
											<td>Manufacturer</td>
											<td>Hazcordous</td>
										</tr>                                   
										                                                      
										<tr class="even"> 
											<td>
												<input readonly type="text" value="<?php echo $get_data_item['type_desc']; ?>" style="width:350px;">
											</td>
											<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
											<td>
												<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
												&nbsp;
												<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
											</td>
										</tr>
										<tr class="pos_label"> 
											<td>Description</td>
											<td>Received</td>
										</tr>
									   <tr class="even" > 
											<td colspan="2">
												<input type="text" style="width:575px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
											</td>
										</tr>
										<tr class="pos_label"> 
											<td colspan="2">Notes</td>
										</tr>
									</table>
							  </td>
							 <td width="43%">
								<table class="table_collapse table_cell_padd5" border="0">                                
								<tr  class="even"> 
									<?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
									<td><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>    
									<td colspan="2"></td>
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">Ordered</td>
									<td colspan="2">&nbsp;</td>
								</tr> 
								<tr  class="even"> 
									<?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
									<td width="7%"><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
									<?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
									<td width="7%"><input type="checkbox" name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     					
                                    </tr>
								<tr class="pos_label"> 
									<td colspan="2">Notified</td>
									<td colspan="2">Dispensed</td>
								</tr>                                                
								<tr class="even">
									<td colspan="4">&nbsp;</td>
								</tr>
								<tr class="pos_label">
									<td colspan="4">&nbsp;
										
									</td>
								</tr>                                
							 </table>  
						</td> 
					  </tr> 
					 <?php }
					 
					 if($sel_order_data[$i]['module_type_id']==7){
								$top_cont=$i;
								$sel_order=$sel_order_data[$i];
								  $sel_order_ord="";
								  $sel_order_rec="";
								  $sel_order_notif="";
								  $sel_order_disp="";
								  $frame_ordered_chk="";
								  $frame_received_chk="";
								  $frame_notified_chk="";
								  $frame_dispensed_chk="";
								  $sel_data_item="";
								  $get_data_item="";
							$sel_data_item = imw_query("select itm.num_size, ss.size_name, sm.measurment_name , itm.other, itm.type_desc from in_item as itm left join in_supplies_measurment as sm on sm.id=itm.measurment left join in_supplies_size as ss on ss.id=itm.char_size where itm.id='".$sel_order['item_id']."' and itm.module_type_id='".$sel_order['module_type_id']."'");
							$get_data_item = imw_fetch_array($sel_data_item);
							?>
							<tr>
								<td colspan="2" class="reportHeadBG1" style="text-align:left;">
									<?php echo "Accessories :- ".ucfirst($sel_order['item_name']); ?>
								</td>
							</tr>
							<tr>
							  <td>
									<table class="table_collapse table_cell_padd5" border="0">
										<tr  class="even"> 
											<td width="200">

											<?php
											$cl_manuFacOptions='';
											$cl_manufacNames='';
											foreach($cl_arrManufac as $id => $cl_manufacName){
											 if($id==$sel_order['manufacturer_id']){ 
											 	$cl_manufacNames = $cl_manufacName;
											  }
											  } ?>
											 <input readonly type="text" value="<?php echo $cl_manufacNames; ?>"  style="width:180px;"/> 
											</td>
											<td width="200">
												<input readonly type="text" style="width:180px;" value="<?php echo $get_data_item['size_name']; ?>" />
											</td>
											<td width="220">
											<input readonly type="text" value="<?php echo ucfirst($get_data_item['measurment_name']); ?>" style="width:165px;">
										</td>
										</tr>                                
										<tr class="pos_label"> 
											<td>Manufacturer</td>
											<td>Size</td>
											<td>Measurment</td>
										</tr>                                   
										<tr  class="even"> 
											<td>
                                                <input readonly type="text" value="<?php echo $get_data_item['num_size']; ?>" style="width:180px;" />											
											</td>
											<td>
												<input readonly type="text" style="width:180px;" value="<?php echo $get_data_item['other']; ?>" />
											</td>
											<td>
												<?php if($sel_order['received']!="0000-00-00" && $sel_order['received']!="") { $frame_received_chk="checked"; $sel_order_rec = getDateFormat($sel_order['received']); } ?> 
												<input type="checkbox" name="received_chk_<?php echo $top_cont; ?>" value="1" id="received_chk_<?php echo $top_cont; ?>" <?php echo $frame_received_chk; ?> />
												&nbsp;
									<input type="text" style="width:120px;" value="<?php echo $sel_order_rec; ?>" name="received_<?php echo $top_cont; ?>" id="received_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/>
											</td>
										</tr>
										<tr class="pos_label"> 
											<td>Size</td>
											<td>Other</td>
											<td>Received</td>
										</tr>                                                                
										<tr class="even"> 
											<td colspan="3">
												<input type="text" style="width:575px;" name="item_comment_<?php echo $top_cont; ?>" id="item_comment_<?php echo $top_cont; ?>" onFocus="if (this.value=='Note') this.value = ''" onBlur="if (this.value=='') this.value = 'Note'" value="<?php if($sel_order['item_comment']!="") { echo $sel_order['item_comment']; } else { echo "Note"; } ?>" />
											</td>
										</tr>
										<tr class="pos_label"> 
											<td colspan="3">Notes</td>
										</tr>
									   
									</table>
							  </td>
							 <td>
								<table class="table_collapse table_cell_padd5" border="0">                                
								<tr  class="even"> 
									<?php if($sel_order['ordered']!="0000-00-00" && $sel_order['ordered']!="") { $frame_ordered_chk="checked"; $sel_order_ord = getDateFormat($sel_order['ordered']); } ?>
									<td><input type="checkbox" name="ordered_chk_<?php echo $top_cont; ?>" value="1" id="ordered_chk_<?php echo $top_cont; ?>" <?php echo $frame_ordered_chk; ?> /></td>
									<td>
									<input type="text" style="width:120px;" value="<?php echo $sel_order_ord; ?>" name="ordered_<?php echo $top_cont; ?>" id="ordered_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>  
									<td colspan="2">&nbsp;</td>  
									
								</tr>
								<tr class="pos_label"> 
									<td colspan="2">Ordered</td>
									<td colspan="2">&nbsp;</td>
								</tr> 
								<tr  class="even"> 
									<?php if($sel_order['notified']!="0000-00-00" && $sel_order['notified']!="") { $frame_notified_chk="checked"; $sel_order_notif = getDateFormat($sel_order['notified']); } ?>
									<td width="7%"><input type="checkbox" name="notified_chk_<?php echo $top_cont; ?>" value="1" id="notified_chk_<?php echo $top_cont; ?>" <?php echo $frame_notified_chk; ?>/></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_notif; ?>" name="notified_<?php echo $top_cont; ?>" id="notified_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     
									<?php if($sel_order['dispensed']!="0000-00-00" && $sel_order['dispensed']!="") { $frame_dispensed_chk="checked"; $sel_order_disp = getDateFormat($sel_order['dispensed']); } ?>
									<td width="7%"><input type="checkbox" name="dispensed_chk_<?php echo $top_cont; ?>" value="1" id="dispensed_chk_<?php echo $top_cont; ?>" <?php echo $frame_dispensed_chk; ?>/></td>
									<td width="43%">
									<input type="text" style="width:120px;" value="<?php echo $sel_order_disp; ?>" name="dispensed_<?php echo $top_cont; ?>" id="dispensed_<?php echo $top_cont; ?>" class="date-pick" onChange="sel_chk_date_fun('<?php echo $top_cont; ?>');"/></td>     					
                                    </tr>
								<tr class="pos_label"> 
									<td colspan="2">Notified</td>
									<td colspan="2">Dispensed</td>
								</tr>                                                
								<tr class="even">
									<td colspan="4">
										<input readonly type="text" value="<?php echo $get_data_item['type_desc']; ?>" style="width:400px;">
										
									</td>
								</tr>
								<tr class="pos_label">
									<td colspan="4">
										Description
									</td>
								</tr>                                
							 </table>  
						</td> 
					  </tr> 
					 <?php }
					 
					 }
						echo "<script type='text/javascript'>show_hide_tbl('other_hide_show_id');</script>";
					}else{ 
					?> 
                     <tr>
                        <td colspan="2" style="text-align:center;">
                            No record
                        </td>
                    </tr>
                    <?php } ?>
                  </table>
              </div>   
            <table class="table_collapse" border="0">
                <tr class="listheading" id="item_tr_id">
                    <td width="100">UPC</td> 
                    <td width="100">Item</td>
                    <td width="100">Prac Code</td>
                    <td width="100">Cost</td>
                    <td width="100">Allowed</td>
                    <td width="80">Discount</td>
                    <td width="100">Total</td>
                    <td width="100">Ins. Resp</td>
                    <td width="100">Pt Payed</td>
					<td width="100">Pt Resp</td>
                    <td width="100" style="padding:0;">
						<select name="main_discount_code_1" id="main_discount_code_1" style="width:90px;" onChange="auto_select_dis_code(this.value);">
							<option value="">Please Select</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_ord_row_ins['main_default_discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
							</option>
							<?php } ?>
						</select>
					</td>
                    <td style="padding:0;" width="100">
						<select name="main_ins_case_id_1" id="main_ins_case_id_1" style="width:90px;" onChange="auto_select_ins(this.value);">
                        	<option value="">Insurance</option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_ord_row_ins['main_default_ins_case']) { echo 'SELECTED'; } ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
					</td>
                </tr>   
                <?php
				$pro_cont=0;
				for($i=1;$i<=count($sel_order_data);$i++){
					$pro_cont=$i;
					$sel_records=$sel_order_data[$i];
					$all_dx_codes="";
					if($sel_records['dx_code']!="")
					{
						$dx_singl=array();
						$get_dxs = explode(",",$sel_records['dx_code']);
						for($fd=0;$fd<count($get_dxs);$fd++)
						{
							$dx_singl[] = $dx_code_arr[$get_dxs[$fd]];
						}
						$all_dx_codes = join('; ',$dx_singl);
					}
					
					if($sel_order_data[$i]['module_type_id']==2){
					
						$show_lens_value_arr = array('type_id','progressive_id','material_id','transition_id','a_r_id','tint_id','polarized_id','edge_id','color_id','uv400','lens_other','pgx');
					
						$show_itemized_name_arr = array('lens','progressive','material','transition','a_r','tint','polarization','edge','color','uv400','other','pgx');
						
						$pro=0;
						for($l=0;$l<count($show_lens_value_arr);$l++)
						{
							if(($sel_records[$show_lens_value_arr[$l]] > 0) || ($sel_records[$show_lens_value_arr[$l]]!="" && $show_itemized_name_arr[$l]=="other"))
							{
								$pro++;
								
							$sel_price_qry=imw_query("select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0'");	
							
							while($sel_lens_price_data=imw_fetch_array($sel_price_qry))
							{
								if($pro==1) { $clas = ""; } else { $clas = "even"; } ?>  
				<tr id="<?php echo $sel_lens_price_data['itemized_name']."_display"; ?>" class="<?php echo $clas; ?>">
                	<td>
						<input type="hidden" name="dx_code_<?php echo $pro_cont; ?>" value="<?php echo $all_dx_codes; ?>" />
                        <input type="hidden" name="order_chld_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['order_chld_id']; ?>" />
                     	<input type="hidden" name="order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['id']; ?>" />
						<input type="hidden" name="lens_item_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['itemized_id']; ?>" />
						<input type="hidden" name="lens_item_detail_name_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['itemized_name']; ?>" />
						<input type="hidden" name="lens_price_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['id']; ?>" />
                        <input type="hidden" class="qty_cls" name="lens_qty_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['qty']; ?>" />
                        <input type="hidden" class="rqty_cls" name="qty_right_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['qty_right']; ?>" />
                        <input type="hidden" name="module_type_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['module_type_id']; ?>" />
						<input type="hidden" name="upc_id_<?php echo $pro_cont; ?>" id="upc_id_<?php echo $pro_cont; ?>" value="">
                    	<?php if($pro==1) { ?>
						<input readonly style="width:90px;" type="text" name="upc_name_<?php echo $pro_cont; ?>" id="upc_name_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
						<?php } ?>
                    </td>
                    <td>
                    	<input readonly style="width:90px;" type="text" class="itemname" name="lens_item_name_<?php echo $pro_cont; ?>" id="lens_item_name_<?php echo $pro_cont; ?>" value="<?php if($sel_lens_price_data['itemized_name']=="a_r") { echo "a/r"; } elseif($sel_lens_price_data['itemized_name']=="lens") { echo "Seg type"; } else { echo $sel_lens_price_data['itemized_name']; } ?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
						<input readonly style="width:90px;" type="hidden" name="item_name_<?php echo $pro_cont; ?>" id="item_name_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_name'];?>"/>
						
                        <input type="hidden" name="item_id_<?php echo $pro_cont; ?>" id="item_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_id'];?>" />
                    </td>
                    <td>
                    	<input style="width:90px;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $proc_code_arr[$sel_lens_price_data['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_lens_price_data['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>','pos'); calculate_all();"/>
                    </td>
                    <td>
                    	<input readonly style="width:90px; text-align:right;" type="text" name="lens_item_price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['wholesale_price'];?>" class="price_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="lens_item_allowed_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="allowed_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['allowed'];?>" class="allowed_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input  style="width:70px; text-align:right;" type="hidden" name="lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_lens_price_data['discount'];?>" onChange="calculate_all();" class="price_disc_per_proc"/>
                    	<input readonly style="width:70px; text-align:right;" type="text" name="read_lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="read_discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_lens_price_data['discount'];?>" class="price_disc" onChange="calculate_all();"/>
                    </td> 
                    <td>
                    	<input readonly style="width:90px; text-align:right;" type="text" name="lens_item_total_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="total_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['total_amt'];?>" class="price_total"  onChange="calculate_all();"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls"/>
                    </td>                  
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['pt_resp'];?>" class="resp_cls" readonly/>
                    </td>
					<td>
						<select name="discount_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="discount_code_<?php echo $pro_cont; ?>" class="text_10 disc_code dis_code_class" style="width:90px;">
							<option value="">Please Select</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_lens_price_data['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
							</option>
							<?php } ?>
						</select>	
					</td>
                    <td>
                   		<!--<select name="ins_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="ins_id_<?php echo $pro_cont; ?>" style="width:110px;">
                        	<option value=""></option>
                            <?php
								foreach($ins_data_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>-->
                        <select name="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" class="ins_case_class" style="width:90px;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>_<?php echo $pro; ?>');">
                        	<option value=""></option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
                    </td>                                    
                </tr>
				
				<?php } } } ?>
				<input type="hidden" name="lens_item_count_<?php echo $pro_cont;?>" id="lens_item_count_<?php echo $pro_cont;?>" value="<?php echo $pro; ?>">
				<?php } elseif($sel_order_data[$i]['pof_check']==0 && $sel_order_data[$i]['module_type_id']!='8'){ ?>
				<tr id="<?php echo $pro_cont; ?>"> 
                	<td>
						<input type="hidden" name="dx_code_<?php echo $pro_cont; ?>" value="<?php echo $all_dx_codes; ?>" />
                        <input type="hidden" name="order_chld_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['order_chld_id']; ?>" />
                     	<input type="hidden" name="order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['id']; ?>" />
                        <input type="hidden" class="qty_cls" id="qty_<?php echo $pro_cont; ?>" name="qty_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['qty']; ?>" />
                        <input type="hidden" class="rqty_cls" id="qty_right_<?php echo $pro_cont; ?>" name="qty_right_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['qty_right']; ?>" />
                        <input type="hidden" name="module_type_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['module_type_id']; ?>" />
						<input type="hidden" name="upc_id_<?php echo $pro_cont; ?>" id="upc_id_<?php echo $pro_cont; ?>" value="">
                    	<input readonly style="width:90px;" type="text" name="upc_name_<?php echo $pro_cont; ?>" id="upc_name_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
                    </td>
                    <td>
                    	<input readonly style="width:90px;" type="text" class="itemname" name="item_name_<?php echo $pro_cont; ?>" id="item_name_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_name'];?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
                        <input type="hidden" name="item_id_<?php echo $pro_cont; ?>" id="item_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_id'];?>" />
                    </td>
                    <td>
                    	<input style="width:90px;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_1" value="<?php echo $proc_code_arr[$sel_records['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_records['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'price_<?php echo $pro_cont; ?>','pos','<?php echo $sel_records['trial_chk']; ?>'); pospage_title('<?php echo $pro_cont; ?>'); calculate_all();"/>
                    </td>
                    <td>
                    	<input readonly style="width:90px; text-align:right;" type="text" name="price_<?php echo $pro_cont; ?>" id="price_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['price'];?>" class="price_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="allowed_<?php echo $pro_cont; ?>" id="allowed_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['allowed'];?>" class="allowed_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input style="width:70px; text-align:right;" type="hidden" name="discount_<?php echo $pro_cont; ?>" id="discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['discount'];?>"  onChange="calculate_all();" class="price_disc_per_proc"/>
                    	<input readonly style="width:70px; text-align:right;" type="text" name="read_discount_<?php echo $pro_cont; ?>" id="read_discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['discount'];?>" class="price_disc" onChange="calculate_all();"/>
                    </td> 
                    <td>
                    	<input readonly style="width:90px; text-align:right;" type="text" name="total_amount_<?php echo $pro_cont; ?>" id="total_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['total_amount'];?>" class="price_total"  onChange="calculate_all();"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="ins_amount_<?php echo $pro_cont; ?>" id="ins_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls"/>
                    </td>                  
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_paid_<?php echo $pro_cont; ?>" id="pt_paid_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_resp_<?php echo $pro_cont; ?>" id="pt_resp_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['pt_resp'];?>"  class="resp_cls" readonly />
                    </td>
					<td>
						<select name="discount_code_<?php echo $pro_cont; ?>" id="discount_code" class="text_10 disc_code dis_code_class" style="width:90px;">
							<option value="">Please Select</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_records['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
							</option>
							<?php } ?>
						</select>
					</td>
                    <td>
                   		<!--<select name="ins_id_<?php echo $pro_cont; ?>" id="ins_id_<?php echo $pro_cont; ?>" style="width:110px;">
                        	<option value=""></option>
                            <?php
								foreach($ins_data_arr as $key => $insCoName){

								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_records['ins_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>-->
                        <select name="ins_case_id_<?php echo $pro_cont; ?>" id="ins_case_id_<?php echo $pro_cont; ?>" class="ins_case_class" style="width:90px;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>');">
                        	<option value=""></option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_records['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
                    </td>                                    
                </tr>
				<script>pospage_title('<?php echo $pro_cont; ?>');</script>
				<input type="hidden" name="lens_item_count_<?php echo $pro_cont;?>" id="lens_item_count_<?php echo $pro_cont;?>" value="1">
				<?php } }
				if(count($sel_order_data)>0)
				{
				$sel_rec=array();
				$sel_tax_ord = imw_query("select * from in_order_details where order_id='$order_id' and module_type_id='8' and del_status='0'");
				$pro_cont = $pro_cont+1;
				if(imw_num_rows($sel_tax_ord)>0)
				{
					$sel_rec = imw_fetch_array($sel_tax_ord);	
				}
				 ?>
				<tr>
					<td>
						<input type="hidden" name="lens_item_count_<?php echo $pro_cont;?>" id="lens_item_count_<?php echo $pro_cont;?>" value="1">
						<input type="hidden" name="module_type_id_<?php echo $pro_cont; ?>" value="8" />
						<input type="hidden" name="order_chld_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['order_chld_id']; ?>" />
						<input type="hidden" name="order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['id']; ?>" />
						<input type="hidden" class="qty_cls" name="qty_<?php echo $pro_cont;?>" value="1" />
                        <input type="hidden" class="rqty_cls" name="qty_right_<?php echo $pro_cont;?>" value="0" />
						<input type="text" value="Taxes" name="upc_name_<?php echo $pro_cont; ?>" style="width:90px;" readonly>
					</td>
					<td><input type="text" value="Taxes" name="item_name_<?php echo $pro_cont; ?>" class="itemname" style="width:90px;" readonly></td>
					<td>
						<input style="width:90px;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_1" value="<?php echo $proc_code_arr[$sel_rec['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_rec['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'','pos','tax');"/>
					</td>
					<td>
						<input type="text" name="price_<?php echo $pro_cont; ?>" id="price_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['price']; ?>" style="width:90px; text-align:right;" class="price_cls" onChange="set_allowed_amt('<?php echo $pro_cont; ?>');calculate_all();">
                    </td>
                    <td>
						<input type="text" name="allowed_<?php echo $pro_cont; ?>" id="allowed_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['allowed']; ?>" style="width:90px; text-align:right;" class="allowed_cls" onChange="calculate_all();">
                    </td>
					<td>
						<input type="hidden" name="discount_<?php echo $pro_cont; ?>" value="0" style="width:70px; text-align:right;"  onChange="calculate_all();" class="price_disc_per_proc">
						<input type="text" name="read_discount_<?php echo $pro_cont; ?>" value="0" style="width:70px; text-align:right;" class="price_disc" onChange="calculate_all();" readonly>
                    </td>
					<td>
						<input type="text" name="total_amount_<?php echo $pro_cont; ?>" id="total_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['total_amount']; ?>" style="width:90px; text-align:right;" class="price_total"  onChange="calculate_all();" readonly>
					</td>
					<td>
						<input type="text" name="ins_amount_<?php echo $pro_cont; ?>" id="ins_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['ins_amount']; ?>" style="width:90px; text-align:right;" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls">
					</td>
					<td>
						<input type="text" name="pt_paid_<?php echo $pro_cont; ?>" id="pt_paid_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['pt_paid']; ?>" style="width:90px; text-align:right;" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls">
					</td>
					<td>
					<input type="text" name="pt_resp_<?php echo $pro_cont; ?>" id="pt_resp_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['pt_resp']; ?>" style="width:90px; text-align:right;" class="resp_cls" readonly>
					</td>
					<td>&nbsp;</td>
					<td>
						<select name="ins_case_id_<?php echo $pro_cont; ?>" id="ins_case_id_<?php echo $pro_cont; ?>" class="ins_case_class" style="width:90px;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>');">
                        	<option value=""></option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_rec['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
					</td>
				</tr>
				<?php } ?>
               <input type="hidden" id="last_cont" name="last_cont" value="<?php echo $pro_cont;?>">
            </table>
    </div>
    <table class="table_collapse" border="0">
    	<tr>
            <td align="right" style="font-weight:bold; padding-left:170px;">Grand Total: </td>
            <td style="padding-left:20px;"><input readonly style="width:90px;" type="text" name="pat_pos_grand_price" id="pat_pos_grand_price" value="" /></td>
            <td><input readonly style="width:90px;" type="text" name="pat_pos_grand_allowed" id="pat_pos_grand_allowed" value="" /></td>
            <td><input readonly style="width:70px;" type="text" name="pat_pos_grand_disc" id="pat_pos_grand_disc" value="" /></td>
            <td><input readonly style="width:90px;" type="text" name="pat_pos_grand_total" id="pat_pos_grand_total" value="" /></td>
			<td><input readonly  style="width:90px;" type="text" name="pat_pos_grand_ins_amt" id="pat_pos_grand_ins_amt" value="" /></td>
            <td><input readonly style="width:90px;" type="text" name="pat_pos_grand_payed" id="pat_pos_grand_payed" value="" /></td>
            <td><input readonly style="width:90px;" type="text" name="pat_pos_grand_resp" id="pat_pos_grand_resp" value="" /></td>
            <td style="width:200px;">&nbsp;</td>
        </tr>
    </table>
    <table class="table_collapse" border="0">
    	<tr><td colspan="4" style="height:7px;"></td></tr>
    	<tr>
			<td style="font-weight:bold; text-align:left;width:520px;">
            	Comment:
            	<input type="text" name="charge_comment_1" value="<?php echo stripslashes($sel_ord_row_ins['comment']);?>" style="width:330px;"/>
            </td>
            <td style="text-align:left;font-weight:bold;">Method:
                <select name="paymentMode" id="paymentMode" class="input_text_10" style="width:78px;" onChange="return changeMode();">
                    <option value="Cash" <?php if($sel_ord_row_ins['payment_mode']=="Cash") echo 'SELECTED'; ?>>Cash</option>
                    <option value="Check" <?php if($sel_ord_row_ins['payment_mode']=="Check") echo 'SELECTED'; ?>>Check</option>
                    <option value="Credit Card" <?php if($sel_ord_row_ins['payment_mode']=="Credit Card") echo 'SELECTED'; ?>>Credit Card</option>
                    <option value="EFT" <?php if($sel_ord_row_ins['payment_mode']=="EFT") echo 'SELECTED'; ?>>EFT</option>
                    <option value="Money Order" <?php if($sel_ord_row_ins['payment_mode']=="Money Order") echo 'SELECTED'; ?>>Money Order</option>
                </select>
            </td>
            <td id="checkTd" style="font-weight:bold;border:none;text-align:left;display:<?php if($sel_ord_row_ins['payment_mode']=="Check" || $sel_ord_row_ins['payment_mode']=="EFT" || $sel_ord_row_ins['payment_mode']=="Money Order"){ echo 'block'; }else{ echo 'none'; } ?>;">
            	Check&nbsp;#:
                <input name="checkNo" id="checkNo" type="text" class="input_text_10" size="15" value="<?php echo $sel_ord_row_ins['checkNo']; ?>" />	
            </td>
            <td id="ccTd" style="text-align:left;border:none;display:<?php if($sel_ord_row_ins['payment_mode']=="Credit Card"){ echo 'block'; }else{ echo 'none'; } ?>;">
                <table class="table_collapse_autoW">
                    <tr>
                        <td style="text-align:left; border:none;font-weight:bold;" class="text_b_w">CC&nbsp;#:</td>
                        <td style="text-align:left; border:none;"><input name="cCNo" id="cCNo" type="text" class="input_text_10" size="12" value="<?php echo $sel_ord_row_ins['creditCardNo']; ?>" /></td>
                        <td style="width:6px;border:none;"></td>
                        <td class="text_b_w" style="text-align:left;border:none;font-weight:bold;">Type:</td>
                        <td style="text-align:left;border:none;" id="creditCardCoTd">
                            <select name="creditCardCo" id="creditCardCo" style="width:100px;" class="input_text_10">
                                <option value=""></option>
                                <option value="AX" <?php if($sel_ord_row_ins['creditCardCo'] == "AX") echo 'SELECTED'; ?>>American Express</option>
                                <option value="Dis" <?php if($sel_ord_row_ins['creditCardCo'] == "Dis") echo 'SELECTED'; ?>>Discover</option>
                                <option value="MC" <?php if($sel_ord_row_ins['creditCardCo'] == "MC") echo 'SELECTED'; ?>>Master Card</option>
                                <option value="Visa" <?php if($sel_ord_row_ins['creditCardCo'] == "Visa") echo 'SELECTED'; ?>>Visa</option>
                            </select>
                        </td>
                        <td style="width:5px;border:none;"></td>
                        <td style="text-align:right;border:none;font-weight:bold;" class="text_b_w">Exp.&nbsp;Date:</td>
                        <td style="text-align:left;border:none;">
                            <input type="text" name="expireDate" id="expireDate" value="<?php echo $sel_ord_row_ins['expirationDate']; ?>" size='4' maxlength="10" class="input_text_10" />
                        </td>
                    </tr>
                </table>
            </td>									
        </tr>
    </table>
    
    <div class="btn_cls mt10" style="width:95%; float:left;">
<!--    	<input type="button" name="payment" value="Payment"/>
        <input type="button" name="on_hold" value="On Hold"/> 
 -->        <?php if($_SESSION['order_id']!="")
			  { ?>
        	<input type="button" name="print" onClick="printpos()" value="Order Print"/> 
            
            <input type="button" name="patient_receipt" onClick="patientReceipt()" value="Patient Receipt"/> 
        <?php } else { ?>
      		<input type="button" class="dis" name="print" value="Order Print"/> 
            <input type="button" class="dis" name="patient_receipt" value="Patient Receipt"/> 
        <?php } ?>  
      
        <input type="button" name="save" value="Save" onClick="frm_sub_fun('next');"/> 
	<!--<input type="button" name="order" value="Order" onClick="frm_sub_fun('next');"/> -->
        <input type="button" name="new" value="Cancel" onClick="frm_sub_fun('cancel');"/>
        <!--<input type="button" name="post_btn" value="Post" onClick="frm_sub_fun('order_post');"/>-->
		<input type="button" name="post_btn" value="Post & Order" onClick="frm_sub_fun('order_post');"/>        
        <input type="button" name="post_btn" value="Post & Dispensed" onClick="frm_sub_fun('dispensed_post');"/>
    </div>
    </form> 

<script>

var last_cont=document.getElementById("last_cont").value;
$(document).ready(function(e) {
	calculate_all();
	if(last_cont==0)
	{
		addrow();
	}
	show_loading_image("hide");
});

for(var j=1;j<=last_cont;j++){
	//var obj6 = new actb(document.getElementById('upc_name_'+j),custom_array_upc,"","",document.getElementById('upc_id_'+j),custom_array_upc_id);
var lens_item_count=document.getElementById("lens_item_count_"+j).value;	
	for(var t=1;t<=lens_item_count;t++){
		var obj7 = new actb(document.getElementById('item_prac_code_'+j+'_'+t),customarrayProcedure);
	}
	//var obj8 = new actb(document.getElementById('item_name_'+j),custom_array_name,"","",document.getElementById('upc_id_'+j),custom_array_upc_id);
}
</script>
</body>
</html>