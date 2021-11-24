<?php 
/*
File: contact_selection.php
Coded in PHP7
Purpose: Contact Lens Details
Access Type: Direct access
*/

require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php"); 

//print_r($_REQUEST);die;

if($_REQUEST['order_id']!="")
{
	$_SESSION['order_id']=$_REQUEST['order_id'];
}
$pageName = "contactLensSelection";
$_SESSION['order_id'] = (isset($_REQUEST['order_id']))?$_REQUEST['order_id']:$_SESSION['order_id'];
$patient_id=$_SESSION['patient_session_id'];
$order_id=$_SESSION['order_id'];
$action=$_REQUEST['frm_method'];
//$_SESSION['order_id']=100;

$icd10_sql_qry=imw_query("select id,icd10,icd10_desc from icd10_data where deleted='0'");
while($icd10_sql_row=imw_fetch_array($icd10_sql_qry)){
	$icd10_dx=str_replace('-','',$icd10_sql_row['icd10']);
	$icd10_desc_arr[$icd10_dx]=$icd10_sql_row['icd10_desc'];
	$dx_code_arr[$icd10_sql_row["id"]]=$icd10_sql_row["icd10"];
}

$order_id=$_SESSION['order_id'];
$sel_ord_qry=imw_query("select order_enc_id from in_order where id ='$order_id'");
$sel_ord_row=imw_fetch_array($sel_ord_qry);
$order_enc_id=$sel_ord_row['order_enc_id'];
$idoc_enc_id = (int)$order_enc_id;

if($action=="save" || $action =="order_post" || $action =="dispensed_post"){
	
	if($action =="dispensed_post"){
		foreach($_POST as $key=>$dt){
			if(strpos($key, "pos_")!== false && strpos($key, "pos_")=="0"){
				if($key=="pos_last_cont"){
				}else{
					$key1= str_replace("pos_", "", $key);
					$_POST[$key1] = $dt;
					unset($_POST[$key]);
				}
			}
		}
	}
	other_order_action($action,$_POST);
	
	if($action=="order_post" || $action =="dispensed_post"){
		$action="idoc_order_post";
	}else{
		$action="";
	}
	echo "<script type='text/javascript'>window.location.href='contact_selection.php?frm_method=".$action."'</script>";
}else if($action=="reorder")
{
	make_reorder($order_id);
	echo "<script type='text/javascript'>window.location.href='contact_selection.php'</script>";
}else if($action=="cancel"){
	//other_order_action($action,$_POST);
	echo "<script type='text/javascript'>window.location.href='./index.php'</script>";
}else if($action=="idoc_order_post"){
	echo "<script type='text/javascript'>top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('Add_new_popup','../../remoteConnect.php?encounter_id=$order_enc_id','opt_med');</script>";
}

/************ Start Getting Data for CPT Codes ************/
$proc_code_arr=array();
$proc_code_desc_arr=array();
$sql = "SELECT `cpt_fee_id`, `cpt_prac_code`, `cpt_desc` FROM `cpt_fee_tbl` 
		WHERE `status`='active' AND `delete_status`='0'
		ORDER BY `cpt_prac_code` ASC";
$sql = imw_query($sql);
if($sql && imw_num_rows($sql)>0){
	while($row = imw_fetch_assoc($sql)){
		$proc_code_arr[$row['cpt_fee_id']] = $row['cpt_prac_code'];
		$proc_code_desc_arr[$row['cpt_fee_id']] = $row['cpt_desc'];
	}
}
imw_free_result($sql);
/************ End Getting Data for CPT Codes ************/
/*Disinfectent List*/
$disinfectent = array();
$sql = "SELECT `id`, `name`, `prac_code`, `price` FROM `in_cl_disinfecting` WHERE `del_status`='0' order by `name` ASC";
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
  while($row = imw_fetch_assoc($resp)){
	  $id = $row['id'];
	  $disinfectent[$id]['name'] = $row['name'];
	  $disinfectent[$id]['prac_code'] = $row['prac_code'];
	  $disinfectent[$id]['price'] = $row['price'];
  }
}

/*Discount codes*/
$discCodes = "";
$discCodes1 = array();
$sel_rec=imw_query("select d_id,d_code,d_default from discount_code ORDER BY d_code ASC");
while($sel_write=imw_fetch_array($sel_rec)){
	$discCodes .='<option value="'.$sel_write['d_id'].'">'.$sel_write['d_code'].'</option>';
	$discCodes1[$sel_write['d_id']] = $sel_write['d_code'];
}

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

/*Default Prac Code for Contact Lens*/
$default_prac_code = "";
$d_prac_resp = imw_query("SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='3' AND `sub_module`='' LIMIT 1");
if($d_prac_resp && imw_num_rows($d_prac_resp)>0){
	$default_prac_code = imw_fetch_object($d_prac_resp);
	$default_prac_code = $default_prac_code->prac_code;
}
/*End default Prac code for Contact Lens*/
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
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script>
function get_lens_types(typesId){
	if(typeof(typesId)!="undefined" && typesId!=""){
		var selectedId = "";
		if($("#order_detail_id_1").val()){
			selectedId = $("#cl_type_1").val();
		}
		$.ajax({
			type: 'POST', 
			url: './ajax.php',
			data: 'action=get_lens_types&selectedId='+selectedId+'&ids='+typesId,
			success: function(data){
				data = '<option value="">Please Select</option>'+data;
				$("#cl_type_1").html(data);
			}
		});
	}
}
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/actb/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
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
<script type="text/javascript">


$(document).ready(function(e) {
    $("#dispUPCF").html($("#upc_name_1").val());
	$("#dispNameF").html($("#item_name_1").val());
	$("#dispPCodeF").val($("#item_prac_code_1").val());
	$("#dispPriceF").val($("#price_1").val());
	$("#dispDiscF").val($("#discount_1").val());
	$("#dispDiscCodeF").val($("#discount_code").val());
	$("#dispQtyF").val((parseInt($("#qty_1").val())+parseInt($("#qty_right_1").val())));
	$("#dispTotalF").val($("#total_amount_1").val());
	$("#dispCommentF").val($("#item_comment_1").val());
	
	//BUTTONS
	var mainBtnArr=[];
	top.btn_show("admin",mainBtnArr);		
});

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

function old_order_detail(val){ /*Truncate*/
	window.location.href='contact_selection.php?order_detail_id='+val+"&order=<?php echo $_SESSION['order_id'];?>";
}
function hx_prescription()
{
	var winTop=window.screen.availHeight;
	winTop = (winTop/2)-190;
	
	var winWidth = window.screen.availWidth;
	winWidth = (winWidth/2)-390;
	top.WindowDialog.closeAll();
	var win1=top.WindowDialog.open('Add_new_popup','hx_contact_lens_prescriptions.php','location_popup','width=800,height=340,left='+winWidth+',scrollbars=no,top='+winTop);
	win1.focus();
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
<style type="text/css">
.rightRx label {
	margin-right: 5px;
}
.rightRx input[type=text] {
	width: 70px;
}
.rightRx select {
	width: 70px;
}
.contact_pres input[type=text] {
	width: 50px !important;
}
.contact_pres label {
	margin-right: 5px;
}
.contact_pres td {
	float: left;
	padding-top: 1px;
}
.new_tab_con td {
	float: left;
}
.select2{
	width: 125px;
}
.new_tab_con select {
	width: 125px;
}
.mt11 {
	margin-top: 5px !important;
}
.od_os_span td, .od_os_span th{
	width: 55px;
	text-align: center;
}
.conflens .table_cell_padd5 td{
	padding: 0 5px 5px 0px;
}
.noPadTop td{
	padding-top: 0px;
}
.arrow_image {
	right: 0px !important;
    position: relative;
	transform: rotate(180deg) !important;
	transition: .5s !important;
}
.show_rx_div{
right:-70px !important;
}
</style>
</head>
<body>
<?php
/*error_reporting(E_ERROR);
ini_set('display_errors', 1);*/
if($_SESSION['order_id']>0 && $action!="new_form"){
	if($action!="" && $action>0){
		$whr=" and id='$action'";
	}else{
		if($order_detail_id>0){
			$whr=" and id='$order_detail_id'";
		}
	}
	
	$sel_qry=imw_query("select *, DATE_FORMAT(entered_date, '%m-%d-%Y') AS 'created_date' from in_order_details where order_id ='$order_id' $whr and patient_id='$patient_id' and module_type_id='3' order by id desc");// and del_status='0'
}
$LC = 0; /*Lens Count in saved Order*/
$sel_order=imw_fetch_array($sel_qry);
$main_order_status = $sel_order['order_status'];
$order_del_status=$sel_order['del_status'];
$created_date=$sel_order['created_date'];
?>
<form action="contact_selection.php" method="post" name="contact_frm">
<!-- Unique for all sections -->
    <div class="listheading mt10 mt11">
      <div style="float:left;width:99%;">
        <div style="float:left; width: 200px">Contact Lenses Selection</div>
        <?php if($order_id!="" || $order_id>0) { ?>
        <div style="float:left; width: 100px">Order #<?php echo $order_id; ?></div>
        <div style="float:left; width: 100px"><?php echo $created_date; ?></div>
        <?php }else{ ?>
        <div style="float:left; width: 100px"><input type="text"  class="date-pick" name="order_date" id="order_date" style="height: 21px; background-size:17px 21px;width: 95px;" value="<?php echo date("m-d-Y"); ?>" autocomplete="off" /></div>
        <?php }?>
        <div style="float:right;"><a href="javascript:addNContactLens();"><img src="../../images/add_btn.png" title="Add New Contact Lens" alt="Add New" style="margin:1px;width:22px;border:0px none;"></a> <a href="javascript:void(0);" onClick="javascript:hx_prescription();"> <img style="margin-top:0px; width:25px;" src="../../images/hx_icon.png" border="0" class="serch_icon_stock" title="Hx Contact Lens Prescriptions"/></a> </div>
      </div>
    </div>
<style type="text/css">
.multiSection:nth-child(even) {
	background: rgba(0, 0, 0, 0.03);
	margin-bottom: 8px;
}
.multiSection:last-child{
	margin-bottom: 0px;
}
#tat_table { top:66px !important; }
.rightRx td{padding-top:2px;padding-bottom:2px;}
</style>
<div style="height:<?php echo $_SESSION['wn_height']-387;?>px; overflow-y:scroll;width:100%;float:left;">
	<input type="hidden" name="frm_method" id="frm_method" value="" />
    <input type="hidden" name="module_typePat" id="module_typePat" value="patient_interPage" />
    <input type="hidden" name="page_name" id="page_name" value="contact_selection" />
<div id="contactLenses"> <!-- Multiple contact Lenses container -->
<!-- End Unique for all sections -->
<?php
do{	/*Multiple contact lenses in same order*/
	$LC++;
	$item_id=$sel_order['item_id'];
	$order_detail_id=$sel_order['id'];

	if($order_detail_id>0){
		$cl_whr=" and det_order_id='$order_detail_id'";
	}
	$sel_qry2=imw_query("select stock_image from in_item where id ='$item_id'");
	$sel_item=imw_fetch_array($sel_qry2);
	$stock_image=$sel_item['stock_image'];
	$lensRs = array();
	if($order_id!=""){
		$lensRs=imw_query("Select in_cl_prescriptions.*, DATE_FORMAT(in_cl_prescriptions.rx_dos, '%m-%d-%y') AS 'rx_dos_1', users.fname, users.lname FROM in_cl_prescriptions LEFT JOIN users ON users.id=in_cl_prescriptions.physician_id WHERE order_id='".$order_id."' $cl_whr AND patient_id='$patient_id' AND del_status='0'");
		$lensRes=imw_fetch_array($lensRs);
		if($lensRes['physician_name']==""){
			if(($lensRes['fname']!='' || $lensRes['lname']!='') && $lensRes['physician_id']>0){
				$phyName=$lensRes['lname'].', '.$lensRes['fname'];
			}
		}
		else{
				$phyName=$lensRes['physician_name'];
		}
	}
?>
<!-- Multiple Contact lenses Section -->
<div class="multiSection" id="contactlens_<?php echo $LC; ?>">
  <input type="hidden" name="order_detail_id_<?php echo $LC; ?>" id="order_detail_id_<?php echo $LC; ?>" value="<?php echo $sel_order['id']; ?>">
  <input type="hidden" name="order_id" value="<?php echo $_SESSION['order_id']?>">
  <input type="hidden" name="type_id_<?php echo $LC; ?>" id="type_id_<?php echo $LC; ?>" value="">
  <input type="hidden" name="contact_cat_id_<?php echo $LC; ?>" id="contact_cat_id_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_cat_id']; ?>" />
  <input type="hidden" name="cl_prescription_count[]">
  <input type="hidden" name="order_rx_cl_id_<?php echo $LC; ?>" id="order_rx_cl_id_<?php echo $LC; ?>" value="<?php echo $lensRes['id'];?>">
  <input type="hidden" name="isRXLoaded_<?php echo $LC; ?>" id="isRXLoaded_<?php echo $LC; ?>" value="<?php echo $lensRes['id'];?>">
  <input type="hidden" name="module_type_id_<?php echo $LC; ?>" id="module_type_id_<?php echo $LC; ?>" value="3">

  <input type="hidden" name="del_status_<?php echo $LC; ?>" id="del_status_<?php echo $LC; ?>" value="">
  <input type="hidden" name="trial_<?php echo $LC; ?>" id="trial_<?php echo $LC; ?>" value="<?php echo $sel_order['trial_chk']; ?>">
  <input type="hidden" name="cur_date" id="cur_date" value="<?php echo date('Y-m-d'); ?>">
  
  <div style="vertical-align:top;padding-right:2px;">
    <table class="table_collapse table_cell_padd5">
      <tr>
	  	<td class="blueColor" style="width:20px;font-weight:bold;">
			OD
		</td>
		<td>
			<label for="upc_name_<?php echo $LC; ?>" style="margin-right:5px;">UPC</label>
			
			<input type="hidden" name="item_id_<?php echo $LC; ?>" id="item_id_<?php echo $LC; ?>" value="<?php echo $sel_order['item_id']; ?>">
			<input type="hidden" name="item_id_<?php echo $LC; ?>_os" id="item_id_<?php echo $LC; ?>_os" value="<?php echo $sel_order['item_id_os']; ?>">
			
			<input type="hidden" name="upc_id_<?php echo $LC; ?>" id="upc_id_<?php echo $LC; ?>" value="">
          	<input type="text" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_<?php echo $LC; ?>'),<?php echo $LC; ?>);"  name="upc_name_<?php echo $LC; ?>" id="upc_name_<?php echo $LC; ?>" style="width:100px;" value="<?php echo $sel_order['upc_code']; ?>" autocomplete="off" />
			
			<input type="hidden" name="allowed_<?php echo $LC; ?>" id="allowed_<?php echo $LC; ?>" value="<?php echo $sel_order['allowed']; ?>">
			<input type="hidden" name="allowed_<?php echo $LC; ?>_os" id="allowed_<?php echo $LC; ?>_os" value="<?php echo $sel_order['allowed']; ?>">
		</td>
        <td>
        <?php if($sel_order['order_chld_id']>0){?>
            <span style="width:15px; padding-top:2px; margin-left:2px; margin-right:2px; display:inline-block; float:left;"><img src="../../images/flag_green.png" style="width:100%; display:inline-block;" title="Posted" /></span>
          <?php }?>
        <label for="manufacturer_id_<?php echo $LC; ?>" style="margin-right:5px;">Manufacturer</label>
          <select name="manufacturer_id_<?php echo $LC; ?>" style="width:108px;" id="manufacturer_id_<?php echo $LC; ?>" onChange="get_manufacture_brand(this.value,0,<?php echo $LC; ?>,3); get_vendor_list(this.value, <?php echo $LC; ?>);">
            <option value="0">Please Select</option>
            <?php  
            $qry="";
            $qry = imw_query("select `id`, `manufacturer_name` from in_manufacturer_details where del_status='0' and cont_lenses_chk='1' order by manufacturer_name asc");
            while($rows = imw_fetch_array($qry))
            { ?>
            <option value="<?php echo $rows['id']; ?>" <?php if($rows['id']==$sel_order['manufacturer_id']){echo"selected";} ?>><?php echo $rows['manufacturer_name']; ?></option>
            <?php }	?>
          </select></td>
        <td><label for="brand_id_<?php echo $LC; ?>">Brand&nbsp;</label>
          <select name="brand_id_<?php echo $LC; ?>" id="brand_id_<?php echo $LC; ?>" style="width:108px;" onChange="load_item_idoc(this.value, '<?php echo $LC; ?>');">
            <option value="0">Please Select</option>
            <?php 
                    $qry = "";
                    $qry = imw_query("select `id`, `brand_name`, `source_idoc` from `in_contact_brand` where del_status='0' order by brand_name asc");
                    while($rows = imw_fetch_assoc($qry)){	
                ?>
            <option <?php if($rows['id']==$sel_order['brand_id']){ echo "selected"; } ?>  value="<?php echo $rows['id'];?>" idoc_source="<?php echo $rows['source_idoc']; ?>"><?php echo $rows['brand_name'];?></option>
            <?php } ?>
          </select></td>
        <td>
			<label for="item_vendor_<?php echo $LC; ?>" style="margin-right:5px;">Vendor</label>
			<select name="item_vendor_<?php echo $LC; ?>" id="item_vendor_<?php echo $LC; ?>" style="width:108px">
				<option value="0">Please Select</option>
			<?php
				$vendort_qry = imw_query("SELECT 
												DISTINCT(`v`.`id`) AS 'id', 
												`v`.`vendor_name` 
											FROM 
												`in_manufacturer_details` `m` 
												INNER JOIN `in_vendor_manufacture` `vm` ON(
													`m`.`cont_lenses_chk` = 1 
													AND `m`.`id` = `vm`.`manufacture_id`
												) 
												INNER JOIN `in_vendor_details` `v` ON(`vm`.`vendor_id` = `v`.`id`) 
											WHERE 
												`v`.`del_status` = 0 
											ORDER BY 
												`v`.`vendor_name` ASC");
				if($vendort_qry && imw_num_rows($vendort_qry)>0){
					while($row = imw_fetch_object($vendort_qry)){
						echo '<option '.(($row->id==$sel_order['vendor_id'])?'selected':'').' value="'.$row->id.'">'.$row->vendor_name.'</option>';
					}
				}
			?>
			</select>
          	<input type="text" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_<?php echo $LC; ?>'),<?php echo $LC; ?>);" style="width:100px; display:none;" name="item_name_<?php echo $LC; ?>" id="item_name_<?php echo $LC; ?>" value="<?php echo $sel_order['item_name']; ?>" autocomplete="off" />
		</td>
        <td><label for="item_prac_code_<?php echo $LC; ?>" style="margin-right:5px;">Prac Code</label>
          <input type="text" name="item_prac_code_<?php echo $LC; ?>" id="item_prac_code_<?php echo $LC; ?>" value="<?php echo $proc_code_arr[$sel_order['item_prac_code']]; ?>" title="<?php echo $proc_code_desc_arr[$sel_order['item_prac_code']];?>" style="width:100px;" autocomplete="off" onChange="updatePracCode(this);" /></td>
        <td style="width:100px;"><label for="trial_chk_<?php echo $LC; ?>" style="margin-right:2px;">Trial</label>
        	<input type="checkbox" name="trial_chk_<?php echo $LC; ?>" id="trial_chk_<?php echo $LC; ?>" value="1" <?php if($sel_order['trial_chk']=="1"){echo "checked";}?> style="height:15px;width:15px;margin:0;vertical-align:text-bottom;" onChange="mark_trial('<?php echo $LC; ?>')">        	<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type_id_<?php echo $LC; ?>').value, <?php echo $LC; ?>);" style="text-decoration:none;float:right;"> <img style="margin-top:1px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock"/></a> </td>
      </tr>
	  
	  <tr>
	  	<td class="greenColor" style="width:20px; font-weight:bold;">
			OS
		</td>
		<td>
			<label for="upc_name_<?php echo $LC; ?>_os" style="margin-right:5px;">UPC</label>
			<input type="hidden" name="upc_id_<?php echo $LC; ?>_os" id="upc_id_<?php echo $LC; ?>_os" value="">
          	<input type="text" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_<?php echo $LC; ?>_os'),'<?php echo $LC; ?>_os', '', 'os');"  name="upc_name_<?php echo $LC; ?>_os" id="upc_name_<?php echo $LC; ?>_os" style="width:100px;" value="<?php echo ($sel_order['upc_code_os']!='0')?$sel_order['upc_code_os']:''; ?>" autocomplete="off" />
		</td>
        <td>
        <?php if($sel_order['order_chld_id_os']>0){?>
            <span style="width:15px; padding-top:2px; margin-left:2px; margin-right:2px; display:inline-block; float:left;"><img src="../../images/flag_green.png" style="width:100%; display:inline-block;" title="Posted" /></span>
          <?php }?>
        <label for="manufacturer_id_<?php echo $LC; ?>_os" style="margin-right:5px;">Manufacturer</label>
          <select name="manufacturer_id_<?php echo $LC; ?>_os" style="width:108px;" id="manufacturer_id_<?php echo $LC; ?>_os" onChange="get_manufacture_brand(this.value,0,'<?php echo $LC; ?>_os',3);get_vendor_list(this.value, '<?php echo $LC; ?>_os');">
            <option value="0">Please Select</option>
            <?php  
            $qry="";
            $qry = imw_query("select `id`, `manufacturer_name` from in_manufacturer_details where del_status='0' and cont_lenses_chk='1' order by manufacturer_name asc");
            while($rows = imw_fetch_array($qry))
            { ?>
            <option value="<?php echo $rows['id']; ?>" <?php if($rows['id']==$sel_order['manufacturer_id_os']){echo"selected";} ?>><?php echo $rows['manufacturer_name']; ?></option>
            <?php }	?>
          </select></td>
        <td><label for="brand_id_<?php echo $LC; ?>_os">Brand&nbsp;</label>
          <select name="brand_id_<?php echo $LC; ?>_os" id="brand_id_<?php echo $LC; ?>_os" style="width:108px;" onChange="load_item_idoc(this.value, '<?php echo $LC; ?>_os');">
            <option value="0">Please Select</option>
            <?php 
                    $qry = "";
                    $qry = imw_query("select `id`, `brand_name`, `source_idoc` from `in_contact_brand` where del_status='0' order by brand_name asc");
                    while($rows = imw_fetch_assoc($qry)){	
                ?>
            <option <?php if($rows['id']==$sel_order['brand_id_os']){ echo "selected"; } ?>  value="<?php echo $rows['id'];?>" idoc_source="<?php echo $rows['source_idoc']; ?>"><?php echo $rows['brand_name'];?></option>
            <?php } ?>
          </select></td>
        <td>
			<label for="item_vendor_<?php echo $LC; ?>_os" style="margin-right:5px;">Vendor</label>
			<select name="item_vendor_<?php echo $LC; ?>_os" id="item_vendor_<?php echo $LC; ?>_os" style="width:108px">
				<option value="0">Please Select</option>
			<?php
				$vendort_qry = imw_query("SELECT 
												DISTINCT(`v`.`id`) AS 'id', 
												`v`.`vendor_name` 
											FROM 
												`in_manufacturer_details` `m` 
												INNER JOIN `in_vendor_manufacture` `vm` ON(
													`m`.`cont_lenses_chk` = 1 
													AND `m`.`id` = `vm`.`manufacture_id`
												) 
												INNER JOIN `in_vendor_details` `v` ON(`vm`.`vendor_id` = `v`.`id`) 
											WHERE 
												`v`.`del_status` = 0 
											ORDER BY 
												`v`.`vendor_name` ASC");
				if($vendort_qry && imw_num_rows($vendort_qry)>0){
					while($row = imw_fetch_object($vendort_qry)){
						echo '<option '.(($row->id==$sel_order['vendor_id_os'])?'selected':'').' value="'.$row->id.'">'.$row->vendor_name.'</option>';
					}
				}
			?>
			</select>
          	<input type="text" onChange="javascript:get_details_by_upc(document.getElementById('upc_id_<?php echo $LC; ?>_os'),'<?php echo $LC; ?>_os', '', 'os');" style="width:100px; display:none;" name="item_name_<?php echo $LC; ?>_os" id="item_name_<?php echo $LC; ?>_os" value="<?php echo $sel_order['item_name_os']; ?>" autocomplete="off" />
		</td>
        <td><label for="item_prac_code_<?php echo $LC; ?>_os" style="margin-right:5px;">Prac Code</label>
          <input type="text" name="item_prac_code_<?php echo $LC; ?>_os" id="item_prac_code_<?php echo $LC; ?>_os" value="<?php echo $proc_code_arr[$sel_order['item_prac_code_os']]; ?>" title="<?php echo $proc_code_desc_arr[$sel_order['item_prac_code_os']];?>" style="width:100px;" autocomplete="off" onChange="updatePracCode(this);" /></td>
        <td style="width:100px;"><label for="trial_chk_<?php echo $LC; ?>_os" style="margin-right:2px;">Trial</label>
        	<input type="checkbox" name="trial_chk_<?php echo $LC; ?>_os" id="trial_chk_<?php echo $LC; ?>_os" value="1" <?php if($sel_order['trial_chk_os']=="1"){echo "checked";}?> style="height:15px;width:15px;margin:0;vertical-align:text-bottom;" onChange="mark_trial('<?php echo $LC; ?>_os')">
			<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type_id_<?php echo $LC; ?>').value, '<?php echo $LC; ?>_os');" style="text-decoration:none;float:right;"> <img style="margin-top:1px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock"/></a> </td>
      </tr>
    </table>
	<div class="module_border" style="margin:10px 0;">
    <div style="width:647px;margin:8px 0px 5px 5px;padding-top:5px;" class="fl conflens">
      <table class="table_collapse table_cell_padd5 contact_pres" border="0" >
        <tr>
          <td align="left"><span style="display:inline-block;"><span class="blueColor" style="font-weight:bold;">&nbsp;OD</span></span></td>
          <td align="left" style="width:162px;"><label for="cl_sphere_min_<?php echo $LC; ?>">SPH&nbsp;</label>
            <input type="text" name="cl_sphere_min_<?php echo $LC; ?>" id="cl_sphere_min_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_sphere_min_od'];?>" />
            <input type="text" name="cl_sphere_max_<?php echo $LC; ?>" id="cl_sphere_max_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_sphere_max_od']; ?>" /></td>
          <td align="left" style="width:156px;"><label for="cl_cyl_min_<?php echo $LC; ?>">CYL</label>
            <input type="text" name="cl_cyl_min_<?php echo $LC; ?>" id="cl_cyl_min_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_cylinder_min_od']; ?>" />
            <input type="text" name="cl_cyl_max_<?php echo $LC; ?>" id="cl_cyl_max_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_cylinder_max_od']; ?>" /></td>
          <td align="left" style="width:94px;"><label for="cl_axis_min_<?php echo $LC; ?>">Axis</label>
            <input type="text" name="cl_axis_min_<?php echo $LC; ?>" id="cl_axis_min_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_axis_min_od']; ?>" /></td>
          <td align="left" style="width:87px;"><label for="cl_bc_min_<?php echo $LC; ?>">BC</label>
            <input type="text" name="cl_bc_min_<?php echo $LC; ?>" id="cl_bc_min_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_bc_od']; ?>" /></td>
          <td align="left" style="width:90px;"><label for="cl_dia_min_<?php echo $LC; ?>">Dia</label>
            <input type="text" name="cl_dia_min_<?php echo $LC; ?>" id="cl_dia_min_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_diameter_od']; ?>" /></td>
        </tr>
        <tr>
          <td align="left"><span style="display:inline-block;width:28px;"><span class="greenColor" style="font-weight:bold;">&nbsp;OS</span></span></td>
          <td align="left" style="width:162px;"><label for="cl_sphere_min_os_<?php echo $LC; ?>">SPH&nbsp;</label>
            <input type="text" name="cl_sphere_min_os_<?php echo $LC; ?>" id="cl_sphere_min_os_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_sphere_min_os']; ?>" />
            <input type="text" name="cl_sphere_max_os_<?php echo $LC; ?>" id="cl_sphere_max_os_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_sphere_max_os']; ?>" /></td>
          <td align="left" style="width:156px;"><label for="cl_cyl_min_os_<?php echo $LC; ?>">CYL</label>
            <input type="text" name="cl_cyl_min_os_<?php echo $LC; ?>" id="cl_cyl_min_os_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_cylinder_min_os']; ?>" />
            <input type="text" name="cl_cyl_max_os_<?php echo $LC; ?>" id="cl_cyl_max_os_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_cylinder_max_os']; ?>" /></td>
          <td align="left" style="width:94px;"><label for="cl_axis_min_os_<?php echo $LC; ?>">Axis</label>
            <input type="text" name="cl_axis_min_os_<?php echo $LC; ?>" id="cl_axis_min_os_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_axis_min_os']; ?>" /></td>
          <td align="left" style="width:87px;"><label for="cl_bc_min_os_<?php echo $LC; ?>">BC</label>
            <input type="text" name="cl_bc_min_os_<?php echo $LC; ?>" id="cl_bc_min_os_<?php echo $LC; ?>"value="<?php echo $sel_order['contact_bc_os']; ?>" /></td>
          <td align="left" style="width:90px;"><label for="cl_dia_min_os_<?php echo $LC; ?>">Dia</label>
            <input type="text" name="cl_dia_min_os_<?php echo $LC; ?>" id="cl_dia_min_os_<?php echo $LC; ?>" value="<?php echo $sel_order['contact_diameter_os']; ?>" /></td>
        </tr>
      </table>
    </div>
	
    <div id="rx_div_<?php echo $LC; ?>" class="module_border fr rightRx od_os_span" style="width:420px; margin:5px 10px 5px 5px;">
        <!----------------------------------- RX Hidden Fields For OD----------------------------------->
        <input type="hidden" name="cl_sphere_od_<?php echo $LC; ?>" id="cl_sphere_od_<?php echo $LC; ?>" value="<?php echo $lensRes['sphere_od'];?>" />
        <input type="hidden" name="cl_cylinder_od_<?php echo $LC; ?>" id="cl_cylinder_od_<?php echo $LC; ?>" value="<?php echo $lensRes['cylinder_od'];?>" />
        <input type="hidden" name="cl_axis_od_<?php echo $LC; ?>" id="cl_axis_od_<?php echo $LC; ?>" value="<?php echo $lensRes['axis_od'];?>" />
        <input type="hidden" name="cl_base_od_<?php echo $LC; ?>" id="cl_base_od_<?php echo $LC; ?>" value="<?php echo $lensRes['base_od'];?>" />
        <input type="hidden" name="cl_diameter_od_<?php echo $LC; ?>" id="cl_diameter_od_<?php echo $LC; ?>" value="<?php echo $lensRes['diameter_od'];?>" />
        <input type="hidden" name="cl_add_od_<?php echo $LC; ?>" id="cl_add_od_<?php echo $LC; ?>" value="<?php echo $lensRes['add_od'];?>" />
        <input type="hidden" name="cl_for_od_<?php echo $LC; ?>" id="cl_for_od_<?php echo $LC; ?>" value="<?php echo $sel_order['cl_for_od']; ?>" />	<!--Select List-->
        
        <!----------------------------------- RX Hidden Fields For Os----------------------------------->
        
        <input type="hidden" name="cl_sphere_os_<?php echo $LC; ?>" id="cl_sphere_os_<?php echo $LC; ?>" value="<?php echo $lensRes['sphere_os'];?>" />
        <input type="hidden" name="cl_cylinder_os_<?php echo $LC; ?>" id="cl_cylinder_os_<?php echo $LC; ?>" value="<?php echo $lensRes['cylinder_os'];?>" />
        <input type="hidden" name="cl_axis_os_<?php echo $LC; ?>" id="cl_axis_os_<?php echo $LC; ?>" value="<?php echo $lensRes['axis_os'];?>" />
        <input type="hidden" name="cl_base_os_<?php echo $LC; ?>" id="cl_base_os_<?php echo $LC; ?>" value="<?php echo $lensRes['base_os'];?>" />
        <input type="hidden" name="cl_diameter_os_<?php echo $LC; ?>" id="cl_diameter_os_<?php echo $LC; ?>" value="<?php echo $lensRes['diameter_os'];?>" />
        <input type="hidden" name="cl_add_os_<?php echo $LC; ?>" id="cl_add_os_<?php echo $LC; ?>" value="<?php echo $lensRes['add_os'];?>" />
        <input type="hidden" name="cl_for_os_<?php echo $LC; ?>" id="cl_for_os_<?php echo $LC; ?>" value="<?php echo $sel_order['cl_for_os']; ?>" />	<!--Select List-->
    	<input type="hidden" name="rx_dos_<?php echo $LC; ?>" id="rx_dos_<?php echo $LC; ?>" value="<?php echo $lensRes['rx_dos']; ?>" />
		
		<input type="hidden" name="rx_make_od_<?php echo $LC; ?>" id="rx_make_od_<?php echo $LC; ?>" value="<?php echo $lensRes['rx_make_od']; ?>" />
		<input type="hidden" name="rx_make_os_<?php echo $LC; ?>" id="rx_make_os_<?php echo $LC; ?>" value="<?php echo $lensRes['rx_make_os']; ?>" />
<?php
	$make_od = $lensRes['rx_make_od'];
	$make_od1 = explode(" - ", $make_od);
	$make_od1 = array_shift($make_od1);
	if(strlen($make_od1)>6){
		$make_od1 = explode(" ", $make_od1);
		$make_od1 = array_shift($make_od1);
	}
	$make_od1 = $make_od1?substr($make_od1, 0, 6)."...":$make_od1;
	
	$make_os = $lensRes['rx_make_os'];
	$make_os1 = explode(" - ", $make_os);
	$make_os1 = array_shift($make_os1);
	if(strlen($make_os1)>6){
		$make_os1 = explode(" ", $make_os1);
		$make_os1 = array_shift($make_os1);
	}
	$make_os1 = (strlen($make_os1)>6)?substr($make_os1, 0, 6)."...":$make_os1;
?>
      <table style="padding-left:0px;border-collapse:collapse;">
        <tr>
          <th style="width:95px;text-align:left;"><a href="javascript:void(0);" class="text_purpule" onClick="javascript:prescription_details(<?php echo $LC; ?>);" style="padding-left:5px;">Rx</a><span id="disp_rx_dos_<?php echo $LC; ?>" style="font-weight:normal;float:right;"><?php echo ($lensRes['rx_dos_1']=="00-00-00" || trim($lensRes['rx_dos_1'])=="")?"":$lensRes['rx_dos_1'];?></span></th>
          <th>SPH</th>
          <th>CYL</th>
          <th>AXIS</th>
          <th>BC</th>
          <th>DIAM</th>
          <th>ADD</th>
        </tr>
        <tr>
          <td style="padding-left:5px;text-align:left;">
		  	<span class="blueColor" style="font-weight:bold">OD</span>
		  	<span id="rx_make_od_<?php echo $LC; ?>_disp" title="<?php echo $make_od; ?>"><?php echo $make_od1; ?></span>
		  </td>
          <td><span class="span_data" id="sph_text_od_<?php echo $LC; ?>"><?php echo $lensRes['sphere_od'];?></span></td>
          <td><span class="span_data" id="cyl_text_od_<?php echo $LC; ?>"><?php echo $lensRes['cylinder_od'];?></span></td>
          <td><span class="span_data" id="axis_text_od_<?php echo $LC; ?>"><?php echo $lensRes['axis_od'];?></span></td>
          <td><span class="span_data" id="base_text_od_<?php echo $LC; ?>"><?php echo $lensRes['base_od'];?></span></td>
          <td><span class="span_data" id="diam_text_od_<?php echo $LC; ?>"><?php echo $lensRes['diameter_od'];?></span></td>
          <td><span class="span_data" id="add_text_od_<?php echo $LC; ?>"><?php echo $lensRes['add_od'];?></span></td>
        </tr>
        <tr class="span_data1">
          <td style="padding-left:5px;text-align:left;">
		  	<span class="greenColor" style="font-weight:bold">OS</span>
			<span id="rx_make_os_<?php echo $LC; ?>_disp" title="<?php echo $make_os; ?>"><?php echo $make_os1; ?></span>
		  </td>
          <td><span class="span_data" id="sph_text_os_<?php echo $LC; ?>"><?php echo $lensRes['sphere_os'];?></span></td>
          <td><span class="span_data" id="cyl_text_os_<?php echo $LC; ?>"><?php echo $lensRes['cylinder_os'];?></span></td>
          <td><span class="span_data" id="axis_text_os_<?php echo $LC; ?>"><?php echo $lensRes['axis_os'];?></span></td>
          <td><span class="span_data" id="base_text_os_<?php echo $LC; ?>"><?php echo $lensRes['base_os'];?></span></td>
          <td><span class="span_data" id="diam_text_os_<?php echo $LC; ?>"><?php echo $lensRes['diameter_os'];?></span></td>
          <td><span class="span_data" id="add_text_os_<?php echo $LC; ?>"><?php echo $lensRes['add_os'];?></span></td>
        </tr>
      </table>
    </div>
<div style="clear:both;"></div>
</div>
<table>
<tr>
<td>
    <table class="table_cell_padd5 noPadTop">
        <tr>
            <td style="width:80px;">
                <label for="color_id_<?php echo $LC; ?>">Color</label>
            </td>
            <td style="width:130px;">
                <select name="color_id_<?php echo $LC; ?>" id="color_id_<?php echo $LC; ?>" class="select2">
                    <option value="">Please Select</option>
                    <?php
                    $qry="";
                    $qry = imw_query("select * from in_color where del_status='0' order by color_name asc");
                    $colorVals = array();
                    while($rows = imw_fetch_array($qry))
                    { 
                        $colorVals[$rows['id']]=$rows['color_name'];
                    ?>
                    <option value="<?php echo $rows['id']; ?>" <?php if($rows['id']==$sel_order['color_id']){echo"selected";} ?>><?php echo $rows['color_name']; ?></option>
                    <?php }	?>
                </select>
<script type="text/javascript">
	var colorOptions = <?php echo json_encode($colorVals); ?>;
</script> 
            </td>
            <td style="width:50px;">
                <label for="cl_type_<?php echo $LC; ?>">Type</label>
            </td>
            <td style="width:130px;">
                <select name="cl_type_<?php echo $LC; ?>" id="cl_type_<?php echo $LC; ?>" class="select2">
                    <option value="">Please Select</option>
                    <?php
                  $sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' and module_id='0' AND `opt_sub_type` IN(0,1) AND `del_status`='0' order by opt_val asc";
                  $resp = imw_query($sql);
                  if($resp && imw_num_rows($resp)>0){
                      while($row = imw_fetch_assoc($resp)){
                          $selected = "";
                          if($sel_order['contact_type']==$row['id']){$selected='selected="selected"';}
                    echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['opt_val'].'</option>';
                    }
                    }
                    ?>
                </select>
            </td>
            <td style="width:110px;">
                <label for="cl_replacement_<?php echo $LC; ?>">Replacement</label></td>
            <td style="width:130px;">
                <select name="cl_replacement_<?php echo $LC; ?>" id="cl_replacement_<?php echo $LC; ?>" class="select2">
                    <option value="" repAttr="0">Please Select</option>
                    <?php
                        $sql="Select * from in_options where opt_type='4' and module_id='3' and del_status='0' order by opt_val asc";
                        $resp=imw_query($sql);
                        $repVals=array();
                        if($resp && imw_num_rows($resp)>0)
                        {
                            while($row=imw_fetch_array($resp))
                            {
                                $selected="";
                                $repVals[$row['id']]=$row['opt_val'];
                                if($sel_order['cl_replacement_id']==$row['id']){$selected='selected="selected"';}
                                echo '<option value="'.$row['id'].'" '.$selected.' repAttr="'.$row['opt_val'].'">'.$row['opt_val'].'</option>';
                            }
                        }
                    ?>
               </select>
               <script type="text/javascript">
                var repOptions=<?php echo json_encode($repVals); ?>;
               </script>
            </td>
        </tr>
        <tr>
            <td>
                <label for="cl_packaging_<?php echo $LC; ?>">Packaging</label>
            </td>
            <td>
                <select name="cl_packaging_<?php echo $LC; ?>" id="cl_packaging_<?php echo $LC; ?>" class="select2">
                    <option value="1" packAttr="1">Please Select</option>
                    <?php
                        $sql="Select * from in_options where opt_type='5' and module_id='3' and del_status='0' order by opt_val asc" ;
                        $resp=imw_query($sql);
                        if($resp && imw_num_rows($resp)>0)
                        {
                            while($row=imw_fetch_array($resp))
                            {
                                $selected="";
                                if($sel_order['cl_packaging_id']==$row['id']){$selected='selected="selected"';}
                                $cAttr = $row['opt_val'];
                                /*if(preg_match('/\d+/', $row['opt_val']))
                                {
                                    if(preg_match('/\d+/', $row['opt_val'], $matches)){
                                        $cAttr = $matches[0];
                                        }
                                }*/
                                echo '<option value="'.$row['id'].'" '.$selected.' packAttr="'.$cAttr.'">'.$row['opt_val'].'</option>';
                            }
                        }
                    ?>
              </select>
           </td>
           <td>
                <label for="supply_id_<?php echo $LC; ?>">Supply</label>
           </td>
           <td>
                <select name="supply_id_<?php echo $LC; ?>" id="supply_id_<?php echo $LC; ?>" class="select2">
                <option value="" cAttr="0">Please Select</option>
                <?php
                    $sql="select * from in_supply where del_status='0' order by supply_name asc" ;
                    $resp=imw_query($sql);
                    $supplyVals=array();
                    if($resp && imw_num_rows($resp)>0)
                    {
                        while($row=imw_fetch_array($resp))
                        {
                            $supplyVals[$row['id']]=$row['supply_name'];
                            $selected="";
                            if($sel_order['supply_id']==$row['id']){$selected='selected="selected"';}
                            echo '<option value="'.$row['id'].'" '.$selected.' cAttr="'.$row['supply_name'].'">'.$row['supply_name'].'</option>';
                        }
                    }
                ?>
                </select>
              <script type="text/javascript">
                var supplyOptions=<?php echo json_encode($supplyVals); ?>;
              </script>
           </td>
           <td>
                <label for="cl_wear_sch_<?php echo $LC; ?>">Wear Schedule</label>
           </td>
           <td>
            <select name="cl_wear_sch_<?php echo $LC; ?>" id="cl_wear_sch_<?php echo $LC; ?>" class="select2">
                <option value="">Please Select</option>
                <?php
                    $sql="select * from in_contact_cat where del_status='0' order by cat_name asc" ;
                    $resp=imw_query($sql);
                    $wearVals=array();
                    if($resp && imw_num_rows($resp)>0)
                    {
                        while($row=imw_fetch_array($resp))
                        {
                            $wearVals[$row['id']]=$row['cat_name'];
                            $selected="";
                            if($sel_order['cl_wear_sch_id']==$row['id']){$selected='selected="selected"';}
                            echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['cat_name'].'</option>';
                        }
                    }
                ?>
            </select>
            <script type="text/javascript">
                var wearOptions=<?php echo json_encode($wearVals); ?>;
            </script>
          </td>
        </tr>
    </table>
    
    <table class="table_cell_padd5">
        <tr>
            <td colspan="2" style="width:215px;text-align:center;">Quantity</td>
            <td style="width:50px">
                <label for="cl_usage_<?php echo $LC; ?>">Usage</label>
            </td>
            <td style="width:130px;">
                <select name="cl_usage_<?php echo $LC; ?>" id="cl_usage_<?php echo $LC; ?>" class="select2">
                    <option value="">Please Select</option>
                    <?php
                      $sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='1' and module_id='0' AND `opt_sub_type` IN(0,1) AND `del_status`='0' order by opt_val";
                      $resp = imw_query($sql);
                      if($resp && imw_num_rows($resp)>0){
                          while($row = imw_fetch_assoc($resp)){
                              $selected = "";
                              if($sel_order['contact_usage']==$row['id']){$selected='selected="selected"';}
                        echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['opt_val'].'</option>';
                        }
                      }
                    ?>
                </select>
            </td>
            <td style="width:70px;">
                <label for="cl_physician_name_<?php echo $LC; ?>" style="margin-right:1px;">Doctor</label>
            </td>
            <td style="width:130px;">
                <input type="text" name="cl_physician_name_<?php echo $LC; ?>" id="cl_physician_name_<?php echo $LC; ?>" style="width:92%;" value="<?php echo $phyName;?>" autocomplete="off" />
                <input type="hidden" name="cl_physician_id_<?php echo $LC; ?>" id="cl_physician_id_<?php echo $LC; ?>" value="<?php echo $lensRes['physician_id'];?>" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label for="qty_right_<?php echo $LC; ?>">Right Eye</label>&nbsp;<input type="text" name="qty_right_<?php echo $LC; ?>" id="qty_right_<?php echo $LC; ?>" style="width:30px;" value="<?php echo (isset($sel_order['qty_right']))?$sel_order['qty_right']:0; ?>" onChange="chk_dis_fun('<?php echo $LC; ?>');" />&nbsp;&nbsp;&nbsp;<label for="qty_<?php echo $LC; ?>">Left Eye</label>&nbsp;<input type="text" name="qty_<?php echo $LC; ?>" id="qty_<?php echo $LC; ?>" style="width:30px;" value="<?php echo (isset($sel_order['qty']))?$sel_order['qty']:0; ?>" onChange="chk_dis_fun('<?php echo $LC; ?>_os');" />
            </td>
            <td>
                <label for="cl_disinfecting_<?php echo $LC; ?>">Disinfecting</label>
            </td>
            <td>
                <select name="cl_disinfecting_<?php echo $LC; ?>" id="cl_disinfecting_<?php echo $LC; ?>" class="select2" onChange="pos_row_disinfect(this.value, <?php echo $LC; ?>);">
                    <option value="0">Please Select</option>
                  <?php
                    foreach($disinfectent as $key=>$val){
						$selected = "";
						if((int)$sel_order['contact_disinfecting'] == (int)$key){$selected='selected="selected"';}
						echo '<option value="'.$key.'" '.$selected.' prac_code="'.$val['prac_code'].'" price="'.$val['price'].'">'.$val['name'].'</option>';
					}
                  ?>
                </select>
            </td>
            <td>
                <label for="cl_telephone_<?php echo $LC; ?>" style="width:39px;float:left;">Tel.</label>
            </td>
            <td>
                <input type="text" name="cl_telephone_<?php echo $LC; ?>" id="cl_telephone_<?php echo $LC; ?>" style="width:92%" value="<?php echo stripslashes(core_phone_format($lensRes['telephone']));?>" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'];?>');" />
            </td>
        </tr>
    </table>
    <table class="table_cell_padd5" style="width:100%;">
        <tr style="display:none;">
            <td style="width:65px;">
                <label for="price_<?php echo $LC; ?>">Price</label>
            </td>
            <td style="width:80px;">
                <input type="hidden" name="rtl_price_<?php echo $LC; ?>" id="rtl_price_<?php echo $LC; ?>" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun('<?php echo $LC; ?>');" value="<?php echo $sel_order['price_retail']; ?>" />
				<input type="hidden" name="rtl_price_<?php echo $LC; ?>_os" id="rtl_price_<?php echo $LC; ?>_os" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun('<?php echo $LC; ?>_os');" value="<?php echo $sel_order['price_retail_os']; ?>" />
				
                <input class="currency" type="text" name="price_<?php echo $LC; ?>" id="price_<?php echo $LC; ?>" value="<?php echo $sel_order['price']; ?>" style="width:64px;" onChange='$("#rtl_price_<?php echo $LC; ?>_od").val(this.value).trigger("change");' />
				<input class="currency" type="text" name="price_<?php echo $LC; ?>_os" id="price_<?php echo $LC; ?>_os" value="<?php echo $sel_order['price_os']; ?>" style="width:64px;" onChange='$("#rtl_price_<?php echo $LC; ?>_os").val(this.value).trigger("change");' />
            </td>
            <td style="width:70px">
                &nbsp;<label for="discount_<?php echo $LC; ?>">Discount</label>
            </td>
            <td style="width:60px;">
                <input type="text" name="discount_<?php echo $LC; ?>" id="discount_<?php echo $LC; ?>" value="<?php echo $sel_order['discount']; ?>" style="width:92%;" onChange="chk_dis_fun(<?php echo $LC; ?>);" />
				<input type="text" name="discount_<?php echo $LC; ?>_os" id="discount_<?php echo $LC; ?>_os" value="<?php echo $sel_order['discount_os']; ?>" style="width:92%;" onChange="chk_dis_fun(<?php echo $LC; ?>);" />
            </td>
            <td style="width:65px;">
                <label for="discount_code">Dis. Code</label>
            </td>
            <td style="width:130px;">
                <select name="discount_code_<?php echo $LC; ?>" id="discount_code" class="text_10 disc_code" style="width:100%;" onChange="discountChanged(this);">
                    <option value="">Please Select</option>
				<?php 
					foreach($discCodes1 as $dkey=>$dval){
						$dselected = ($sel_order['discount_code']==$dkey)?'selected="selected"':'';
						echo '<option value="'.$dkey.'" '.$dselected.'>'.$dval.'</option>';
					}
				?>
              </select>
            </td>
            <td style="width:40px;">
                <label for="total_amount_<?php echo $LC; ?>">Total</label>
            </td>
            <td style="width:70px">
                <input class="currency" type="text" name="total_amount_<?php echo $LC; ?>" id="total_amount_<?php echo $LC; ?>" value="" style="width:54px;" readonly />
				<input class="currency" type="text" name="total_amount_<?php echo $LC; ?>_os" id="total_amount_<?php echo $LC; ?>_os" value="" style="width:54px;" readonly />
            </td>
        </tr>
        <tr>
			<td style="width:114px;">
				<label for="use_on_hand_chk_<?php echo $LC; ?>">Use on hand</label>&nbsp;
				<input type="checkbox" name="use_on_hand_chk_<?php echo $LC; ?>" id="use_on_hand_chk_<?php echo $LC; ?>" value="1" <?php if($sel_order['use_on_hand_chk']==1){echo"checked";} ?> style="height:15px;width:15px;margin-right:0;vertical-align:middle;">
			</td>
			<td style="width:91px;text-align:right;padding-right:5px;">
				<label for="order_chk_<?php echo $LC; ?>">&nbsp;Order</label>&nbsp;
				<input type="checkbox" name="order_chk_<?php echo $LC; ?>" id="order_chk_<?php echo $LC; ?>" value="1" <?php if($sel_order['order_chk']==1){echo"checked";} ?> style="height:15px;width:15px;margin-right:3px;vertical-align:middle;">
			</td>
            <td style="width:78px;">
                <label for="dx_code_<?php echo $LC; ?>">DX Code</label>
            </td>
            <td style="width:132px;">
                <?php 
                    $all_dx_codes="";
                    if($sel_order['dx_code']!=""){
                        $dx_singl=array();
                        $get_dxs = explode(",",$sel_order['dx_code']);
                        /*for($fd=0;$fd<count($get_dxs);$fd++){
                            $dx_singl[] = $dx_code_arr[$get_dxs[$fd]];
                        }
                        $all_dx_codes = join('; ',$dx_singl);*/
						$all_dx_codes = join('; ',$get_dxs);
                    }
				?>
                <input type="text" name="dx_code_<?php echo $LC; ?>" id="dx_code_<?php echo $LC; ?>" style="width:116px;" value="<?php echo $all_dx_codes; ?>" onChange="get_dxcode(this);" autocomplete="off" />
            </td>
            <td style="width:100px;"><label for="cl_outside_rx_<?php echo $LC; ?>">Outside Rx</label>&nbsp;
            	<input type="checkbox" value="1" name="cl_outside_rx_<?php echo $LC; ?>" id="cl_outside_rx_<?php echo $LC; ?>" <?php if($lensRes['outside_rx']=="1"){echo"checked";} ?> style="height:15px;width:15px;margin:0;vertical-align:middle;" /></td>
            <td style="visibility:hidden;">
                <label style="display:none;" for="cl_neutralize_rx">Neutralize</label>&nbsp;
				<input type="checkbox" value="1" name="cl_neutralize_rx" id="cl_neutralize_rx" <?php if($lensRes['neutralize_rx']=="1"){echo"checked";} ?> style="height:15px;width:15px;margin:0;display:none;" />
            </td>
        </tr>
    </table>
</td>
<td style="width:400px;vertical-align:top;">
	<table class="table_cell_padd5 noPadTop">
		<tr>
			<td style="width:110px;padding:0;">
				<label for="dominant_eye_<?php echo $LC; ?>">Dominant Eye</label>
			</td>
			<td style="width:110px;padding: 0">
				<label for="fit_type_<?php echo $LC; ?>">Fit Type</label>
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td style="padding: 0;">
				<select name="dominant_eye_<?php echo $LC; ?>" id="dominant_eye_<?php echo $LC; ?>" class="<?php echo ($sel_order['dominant_eye']=='OD')?'blueColor':'greenColor'; ?>" onChange="changeDominantEye(this)">
					<option value="OD" class="blueColor" <?php echo ($sel_order['dominant_eye']=='OD')?'selected':''; ?>>OD</option>
					<option value="OS" class="greenColor" <?php echo ($sel_order['dominant_eye']=='OS')?'selected':''; ?>>OS</option>
				</select>
			</td>
			<td style="padding: 0;">
				<select name="fit_type_<?php echo $LC; ?>" id="fit_type_<?php echo $LC; ?>">
					<option value="1" <?php echo ($sel_order['fit_type']=='1')?'selected':''; ?>>Initial Fit</option>
					<option value="2" <?php echo ($sel_order['fit_type']=='2')?'selected':''; ?>>Refit</option>
				</select>
			</td>
		</tr>
		<td colspan="4" style="padding:  0;">
			<label for="item_comment_<?php echo $LC; ?>" style="margin:10px 0 3px 0;display:block;">Comments</label>
			<textarea name="item_comment_<?php echo $LC; ?>" id="item_comment_<?php echo $LC; ?>" style="width:378px;height:84px;resize:none;padding:5px;font-family:'Gill Sans', 'Gill Sans MT', 'Myriad Pro', 'DejaVu Sans Condensed', Helvetica, Arial, sans-serif"><?php echo $sel_order['item_comment']; ?></textarea>
		</td>
	</table>
</td>
</tr>
</table>
</div>
</div> <!-- End Multiple contact lenses Section -->
<?php
	}while($sel_order=imw_fetch_array($sel_qry)); /*End Multiple Contact Lenses*/
?>
</div> <!-- End container Contact Lenses -->
<?
//}
include("pt_pos.php");
?>
</div>
<input type="hidden" name="last_cont" id="last_cont" value="<?php echo $LC; ?>" />
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/ajaxTypeahead.js?<?php echo constant("cache_version"); ?>"></script> 
<script type="text/javascript">
reqFlag = true;
<?php if(defined('TAX_CHECKBOX_CHECKED') && constant('TAX_CHECKBOX_CHECKED')=='FALSE'){ echo'var tax_applied = false;';}else{ echo'var tax_applied = true;';}?>
function pos_row_disinfect(pracId, i){
	
	/*Discount code for New Row to be added*/
	var mainDiscountCode = $("#main_discount_code_1").val();
	
	var nRow = "";
	if(pracId!="" && pracId!="0"){
		var prac_code = $('#cl_disinfecting_'+i+' option[value="'+pracId+'"]').attr('prac_code');
		var dis_price = $('#cl_disinfecting_'+i+' option[value="'+pracId+'"]').attr('price');
		var disInfectent = $('#cl_disinfecting_'+i+' option[value="'+pracId+'"]').text();
		var response = getPriceFromPracCode(prac_code);
		var prac_resp = response.split('~~~');
		
		var row_count = $(".posTable tr[id^='3_"+i+"']");
		
		if(row_count.length>0){
			var rowId = "3_"+i+"_di";
			rCount = $("#"+rowId);
			if(rCount.length>0){
				$(rCount[0]).find("input#di_item_name_"+i).val(disInfectent);
				$(rCount[0]).find("input#di_item_prac_code_"+i).val(prac_resp[0]);
				$(rCount[0]).find("input#di_item_prac_code_"+i).attr('title',prac_resp[2]);
				$(rCount[0]).find("input#di_prac_code_id_"+i).val(prac_code);
				$(rCount[0]).find("input#di_price_"+i).val(dis_price);
				$(rCount[0]).find("input#di_item_id_"+i).val(pracId);
				$(rCount[0]).find("input#di_del_item_"+i).val(0);
				$(rCount[0]).removeClass('hideRow');
			}
			else{
				nRow += '<tr id="'+rowId+'">';
				nRow += '<td></td>';
				nRow += '<input type="hidden" name="di_order_detail_id_'+i+'" id="di_order_detail_id_'+i+'" value="">';
				nRow += '<input type="hidden" name="di_module_type_id_'+i+'" value="3">';
				nRow += '<input type="hidden" name="di_item_type_'+i+'" value="DI"><!--/td-->';
				
				nRow += '<td>';
				nRow += '<input readonly="" style="width:100%;" type="text" class="itemname" name="di_item_name_'+i+'" id="di_item_name_'+i+'" value="'+disInfectent+'">';
				nRow += '<input type="hidden" name="di_item_id_'+i+'" id="di_item_id_'+i+'" value="'+pracId+'">';
				nRow += '</td><td>';
				
				nRow += '<input style="width:100%;" type="text" class="pracodefield" name="di_item_prac_code_'+i+'" id="di_item_prac_code_'+i+'" value="'+prac_resp[0]+'" title="'+prac_resp[2]+'" autocomplete="off">';
				nRow += '<input type="hidden" name="di_prac_code_id_'+i+'" id="di_prac_code_id_'+i+'" value="'+prac_code+'">';
				nRow += '</td><td>';
				
				nRow += '<input style="width: 100%; text-align: right;" type="text" name="di_price_'+i+'" id="di_price_'+i+'" value="'+dis_price+'" class="price_cls currency" onchange="this.value=parseFloat(this.value).toFixed(2);calculate_all();">';
				nRow += '</td><td>';
				
				nRow += '<input type="text" style="width:100%; text-align:right;" class="qty_cls" id="di_qty_'+i+'" name="di_qty_'+i+'" value="1" onchange="calculate_all();" autocomplete="off" onKeyUp="validate_qty(this);" />';
				nRow += '<input type="hidden" class="rqty_cls" name="di_rqty" id="di_rqty" value="0" />';
				nRow += '</td><td>';
				
				nRow += '<input style="width: 100%; text-align: right;" type="text" name="di_allowed_'+i+'" id="di_allowed_'+i+'" value="" class="allowed_cls currency" onchange="calculate_all();">';
				nRow += '</td>';
				
					
				nRow += '<td style="display:none">';
				
				nRow += '<input readonly="" style="width: 100%; text-align: right;" type="text" name="di_total_amount_'+i+'" id="di_total_amount_'+i+'" value="0.00" class="price_total currency" onchange="calculate_all();">';
					/*Tax Calculations*/
					tax_applied = (tax_applied && facTax[3]>0);
				nRow +='<input type="hidden" name="di_tax_p_'+i+'" id="di_tax_p_'+i+'" class="tax_p" value="'+facTax[3]+'" />';
				nRow +='<input type="hidden" name="di_tax_v_'+i+'" id="di_tax_v_'+i+'" class="tax_v" value="0.00" />';
					/*End Tax Calculations*/
				nRow += '</td><td>';
				
				nRow += '<input style="width: 100%; text-align: right;" type="text" name="di_ins_amount_'+i+'" id="di_ins_amount_'+i+'" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency">';
				nRow += '</td>';
				
				nRow += '<td>';
					/*Line item's share in overall discount*/
					nRow +='<input type="hidden" name="di_overall_discount_'+i+'" id="di_overall_discount_'+i+'" value="0.00" class="item_overall_disc" />';
					nRow += '<input style="text-align:right;" type="hidden" name="di_discount_'+i+'" id="di_discount_'+i+'" value="0.00" onchange="calculate_all();" class="price_disc_per_proc">';
					nRow += '<input style="width: 100%; text-align: right;" type="text" name="di_read_discount_'+i+'" id="di_read_discount_'+i+'" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off">';
				nRow += '</td>';
				
				nRow += '<td><input style="width: 100%; text-align: right;" type="text" name="di_pt_paid_'+i+'" id="di_pt_paid_'+i+'" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency">';
				nRow += '</td><td>';
				
				nRow += '<input style="width: 100%; text-align: right;" type="text" name="di_pt_resp_'+i+'" id="di_pt_resp_'+i+'" value="0.00" class="resp_cls currency" readonly="">';
				nRow += '</td><td>';
				
				nRow += '<select name="di_discount_code_'+i+'" id="di_discount_code_'+i+'" class="text_10 disc_code dis_code_class" style="width:100%;"><option value="">Please Select</option>';
					defDisc = "";
					$.each(discCodes, function(di, dval){
						defDisc = (di==mainDiscountCode)?'selected="selected"':"";
						nRow += '<option value="'+di+'" '+defDisc+'>'+dval+'</option>';
					});
				nRow += '</select>';
				nRow += '</td><td>';
				
				nRow += '<select name="di_ins_case_id_'+i+'" id="di_ins_case_id_'+i+'" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp(\''+i+'_di\');"><option value="0">Self Pay</option>';
		$.each(insCases, function(insVal, insKey){
			insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
			nRow +='<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
		});
		nRow +='</select>';
				nRow += '<input type="hidden" name="di_del_item_'+i+'" id="di_del_item_'+i+'" value="0" />';
				nRow += '</td><td>';
				
				nRow +='<input type="checkbox" class="tax_applied" name="di_tax_applied_'+i+'" id="di_tax_applied_'+i+'" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" />';
				nRow += '</td><td>';
				
				nRow += '<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onclick="delPosRowDis(\'3\', \''+i+'\');">';
				nRow +='</td></tr>';
				
				$(nRow).insertAfter(row_count[row_count.length-1]);
			}
			currencySymbols();
			calculate_all();
		}
		else{
			top.falert("Please select contact lens first.");
			$("#cl_disinfecting_"+i).val(0);
		}
	}
	else if(pracId=="0"){
		delPosRowDis('3', i);
	}
	prac_code_typeahead();
}
function frm_sub_fun(action)
{
	var prc = "";
	var last_count="";
	var check="";
	last_count=$('#last_cont').val();
	for(i=1;i<=last_count;i++)
	{
		if($('#upc_name_'+i).val()=="" && $('#upc_name_'+i+'_os').val()=="")
		{
			check=1;
		}else{
			check=0;
			break;
		}
	}
		
	if(action=="cancel")
	{
		parent.parent.document.getElementById('main_iframe').src='interface/patient_interface/index.php';
		return;
		/*
			var conf = confirm('Are you sure to cancel this Order ?');
			if(conf!=true)
			{
				return false;
			}
		*/
	}
	
	var chk_sub=1;
	if( action=='order_post' ){
		
		var dis=0;
		var discCodemsg = $('<ol>').addClass('discCode_alert');
		
		$('.posTable>tbody tr[id]').each(function(index, element){
			
			dis = $(element).find('.price_disc').val();
			
			if(dis.slice(-1)=='%'){
				dis = dis.replace('%','');
			}
			if(dis[0]=="$"){
				dis = dis.replace(/^[$]+/,"");			
			}
			if(dis>0){
				var dis_code = $(element).find(".disc_code").val();
				if(dis_code=="" || dis_code=="0"){
					
					optLi = $('<li>');
					
					if( $(element).find('span.vis_type').length == 1){
						$(element).find('span.vis_type').clone().appendTo(optLi);
					}
					optText = $(element).find('.itemname').val();
					$(optLi).append(optText);
					$(optLi).appendTo(discCodemsg);
					
					chk_sub=0;
				}
			}
		});
		
		/*Check Overall Discount Code*/
		if(chk_sub==1){
			var oadVal = $('#overall_discount').val();
			
			if( oadVal.slice(-1) == '%' ){
				oadVal = oadVal.slice(0, oadVal.length - 1);
				oadVal = parseFloat(oadVal);
			}
			
			if(oadVal!=0 && $('#overall_discount_code').val() == 0){
				optLi = $('<li>').text('Overall Discount');
				$(optLi).appendTo(discCodemsg);
				chk_sub=0;
			}
		}
	}
	
	if(chk_sub==0){
		top.falert('Please Select Discount Code for the following:<br />'+$(discCodemsg)[0].outerHTML);
		return false;
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

		$(".pracodefield").each(function( index )
		{
			countz = index+1;
			di_name = $.trim($(this).attr('id'));
			di_name_check = di_name.substring(0, 3);
			di_counter = di_name.substring(di_name.length-1);
			
			if($.trim($(this).val())=="" && (di_name_check=="di_" && $('#di_del_item_'+di_counter).val()!="1"))
			{
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
		});
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
		document.contact_frm.submit();
	}
	if((action=="save") && prc=="" && chk_sub==1)
	{
		if(check==1)
		{
			top.falert("Please enter UPC code");
		}
		else
		{
			$("#frm_method").val(action);
			document.contact_frm.submit();
		}
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
						document.contact_frm.submit();
					}
				},
				'No'	: {
					'class'	: 'gray',
					'action': function(){
						$("#reduc_stock").val('no');
						$("#frm_method").val(action);
						document.contact_frm.submit();
					}
				}
			}
		});*/
		if(check==1)
		{
			top.falert("Please enter UPC code");
		}
		else
		{
			$("#reduc_stock").val('yes');
			$("#frm_method").val(action);
			document.contact_frm.submit();
		}
	}
	if(action=="reorder")
	{
		top.fconfirm('Reorder all the items in this order <br> Please confirm',frm_sub_fun_callBack);
	}
	if(action=='new_form')
	{
		$("#frm_method").val('reorder');
		document.contact_frm.submit();
	}
	
}

function frm_sub_fun_callBack(result)
{
	if(result==true)
	{
		$("#frm_method").val('reorder');
		document.contact_frm.submit();	
	}
	else
	{
		return false;	
	}
}

function prescription_details(count){
	//var winTop='<?php echo $_SESSION['wn_height']-500;?>';
	var winTop=window.screen.availHeight;
	winTop = (winTop/2)-190;
	
	var winWidth = window.screen.availWidth;
	winWidth = (winWidth/2)-500;
	top.WindowDialog.closeAll();
	var win2=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/contact_lens_prescriptions.php?rxCount='+count,'contact_lens_prescription_pop','width=1000,height=340,left='+winWidth+',scrollbars=no,top='+winTop);
	win2.focus();
}

function stock_search(type, itemCounter){
	
	itemCounter = (typeof(itemCounter)=="undefined")?document.getElementById("last_cont").value:itemCounter;
	var module_typePatval = document.getElementById('module_typePat').value;
	/*var manuf_id = document.getElementById('manufacturer_id_1').value;
	var brand = document.getElementById('brand_id_1').value;
	var color = document.getElementById('color_id_1').value;*/
	if(document.getElementById('order_detail_id_1').value>0){
		var order_detail_id_1 = document.getElementById('order_detail_id_1').value;
	}
	else{
		var order_detail_id_1 = 'new_form';
	}
	/*var win = window.open('../admin/contact_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&manuf_id='+manuf_id+'&brand='+brand+'&color='+color,'location_popup','width=1000,height=500,left=180,scrollbars=no,top=150');*/
	
	var bc = ($("#cl_base_od_"+itemCounter).val()=="")?$("#cl_base_os_"+itemCounter).val():$("#cl_base_od_"+itemCounter).val();
	var dia = ($("#cl_diameter_od_"+itemCounter).val()=="")?$("#cl_diameter_os_"+itemCounter).val():$("#cl_diameter_od_"+itemCounter).val();
	
	var variables = "manuf_id="+$("#manufacturer_id_"+itemCounter).val()+"&brand="+$("#brand_id_"+itemCounter).val()+"&vendor="+$("#item_vendor_"+itemCounter).val()+"&source=ptInterface";
	//"&diameter="+dia+"&bc="+bc+
	
	top.WindowDialog.closeAll();
	var win3=top.WindowDialog.open('Add_new_popup','../admin/contact_search.php?frm_method='+order_detail_id_1+'&srch_id='+type+'&module_typePat='+module_typePatval+'&itemCounter='+itemCounter+'&'+variables,'location_popup','width=1050,height=500,left=180,scrollbars=no,top=150');
	
	reqFlag = true;
	win3.focus();
}

function chk_dis_fun(count, flagCalc){
	
	flagCalc = (typeof(flagCalc)=="undefined")?true:flagCalc;
	
	var amt =0;
	var dis = "0";
	var lquantity =0;
	var rquantity =0;
	var quantity =0;
	
	count1 = count;
	if(typeof(count)==='string' && count.slice(-2)=='os'){
		count1 = count1.replace(/_os/, '');
	}
	//console.log('qty_'+count1);
	var lquantity = parseInt(document.getElementById('qty_'+count1).value);
	//console.log(lquantity);
	if(isNaN(lquantity)){
		lquantity = 0;
		document.getElementById('qty_'+count1).value = lquantity;
	}
	
	var rquantity = parseInt(document.getElementById('qty_right_'+count1).value);
	if(isNaN(rquantity)){
		rquantity = 0;
		document.getElementById('qty_right_'+count1).value = rquantity;
	}
	
	var pos_discount = $("#pos_discount_"+count).val();
	pos_discount = (typeof(pos_discount) == 'undefined') ? '0' : pos_discount;
	if(document.getElementById('discount_'+count).value!=""){
		
		dis = document.getElementById('discount_'+count).value;
		dis = $.trim(dis);
		
		if(pos_discount!=''){
			dis = pos_discount;
		}
	}
	else{
		dis = pos_discount;
	}
	
	if(document.getElementById('qty_'+count1).value>0){
		lquantity = parseInt(document.getElementById('qty_'+count1).value);
	}
	if(document.getElementById('qty_right_'+count1).value>0){
		rquantity = parseInt(document.getElementById('qty_right_'+count1).value);
	}
	
	if(document.getElementById('rtl_price_'+count).value!=""){
		var amt = document.getElementById('rtl_price_'+count).value;
	}
	//console.log('rtl_price_'+count);
	quantity = lquantity + rquantity;
	//quantity = lquantity;
	//var total = cal_discount(amt,dis);
	var final_allowed=parseFloat(amt)*parseInt(quantity);
	
	$("#pos_discount_"+count).val(dis);
	if(dis.slice(-1)=='%'){
		dis = dis.replace('%','');
		dis = final_allowed * (parseFloat(dis)/100);
	}
	
	var final_price=final_allowed-dis;
	document.getElementById('total_amount_'+count).value=final_price.toFixed(2);
	document.getElementById('allowed_'+count).value=parseFloat(amt).toFixed(2);
	document.getElementById('price_'+count).value=parseFloat(amt).toFixed(2);
	
	if(document.getElementById('pos_allowed_'+count)){
		document.getElementById('pos_price_'+count).value=parseFloat(amt).toFixed(2);
		document.getElementById('pos_allowed_'+count).value=final_allowed.toFixed(2);
		document.getElementById('pos_read_discount_'+count).value=parseFloat(dis).toFixed(2);
		document.getElementById('pos_total_amount_'+count).value=(document.getElementById('pos_allowed_'+count).value)-(document.getElementById('pos_read_discount_'+count).value);
		
		if(typeof(count)==='string' && count.slice(-2)=='os'){
			document.getElementById('pos_qty_'+count).value=lquantity;
		}
		else{
			document.getElementById('pos_qty_'+count).value=rquantity;
		}
		
		document.getElementById('pos_qty_right_'+count).value='0';
		//console.log(lquantity+' -- '+rquantity);
		//document.getElementById('pos_discount_'+count).value=parseFloat(dis).toFixed(2);
	}
	if(flagCalc)
		calculate_all();
}

$(document).ready(function(){
	var vison_list = ['', 'os'];
$.each(vison_list, function(visndex, visval){
<?php
for($i=1; $i<=$LC; $i++){
?>
	
	visval1 = '';
	if(visval!='')
		visval1 = '_'+visval;
		
	chk_dis_fun('<?php echo $i;?>'+visval1, false);
	
	$('#upc_name_<?php echo $i; ?>'+visval1).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensData',
		hidIDelem: document.getElementById('upc_id_<?php echo $i; ?>'+visval1),
		showAjaxVals: 'upc'
	});
	
	$('#item_name_<?php echo $i; ?>'+visval1).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensData',
		hidIDelem: document.getElementById('upc_id_<?php echo $i; ?>'+visval1),
		showAjaxVals: 'name'
	});
	
	$('#dx_code_<?php echo $i; ?>'+visval1).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'dxCodes',
		hidIDelem: document.getElementById('dx_code_<?php echo $i; ?>'+visval1),
		showAjaxVals: 'code'
	});
	
	$('#item_prac_code_<?php echo $i; ?>'+visval1).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC'
	});
	/*hidIDelem: document.getElementById('item_prac_code_<?php echo $i; ?>_'+visval),*/
	
	$('#cl_physician_name_<?php echo $i; ?>').ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'physicianData',
		hidIDelem: document.getElementById('cl_physician_id_<?php echo $i;?>'),
		showAjaxVals: 'name',
		minLength:1
	});
	
<?php
}
?>	
});
	calculate_all_Grand_POS();
	get_lens_types();
	calculate_all();
	prac_code_typeahead();
});
function calculate_all(){
	/*var disc_val=0;
	var price_cls=0;
	grand_price = grand_disc = grand_total = grand_qty = 0;
	    $('.price_cls').each(function(index, element) {
		price_cls 	= parseFloat($('.price_cls').get(index).value);
		resp_cls	= parseFloat($('.resp_cls').get(index).value);
		qty_cls 	= $('.qty_cls').get(index).value;
		disc_val 	= $('.price_disc').get(index).value;
		if(disc_val.slice(-1)=='%'){
			$('.price_disc').get(index).value = disc_val;
			disc_val = disc_val.replace('%','');
			//disc_val = price_cls * (parseFloat(disc_val)/100);
			disc_val = resp_cls * (parseFloat(disc_val)/100);
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
		 //$('.allowed_cls').get(index).value = allowed_total.toFixed(2);
		 /*allowed_total* /
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
	//GDTChange();*/
	calculate_all_Grand_POS();
}
function addNContactLens(){
	
	var discountCodes = <?php echo json_encode($discCodes1); ?>
	/*Discount code for Items to be added*/
	var mainDiscountCode = $("#main_discount_code_1").val();
	
	var LC = $("#last_cont").val();
	LC++;
	var newElem ='<div class="multiSection" id="contactlens_'+LC+'">';
	newElem +='<input type="hidden" name="order_detail_id_'+LC+'" id="order_detail_id_'+LC+'" value="">';
	newElem +='<input type="hidden" name="order_id" value="">';
	newElem +='<input type="hidden" name="type_id_'+LC+'" id="type_id_'+LC+'" value="">';
	newElem +='<input type="hidden" name="item_id_'+LC+'" id="item_id_'+LC+'" value="">';
	newElem +='<input type="hidden" name="contact_cat_id_'+LC+'" id="contact_cat_id_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_prescription_count[]">';
	newElem +='<input type="hidden" name="order_rx_cl_id_'+LC+'" id="order_rx_cl_id_'+LC+'" value="">';
	newElem +='<input type="hidden" name="isRXLoaded_'+LC+'" id="isRXLoaded_'+LC+'" value="">';
	newElem +='<input type="hidden" name="module_type_id_'+LC+'" id="module_type_id_'+LC+'" value="3">';
	
	newElem +='<input type="hidden" name="upc_id_'+LC+'" id="upc_id_'+LC+'" value="">';
	newElem +='<input type="hidden" name="trial_'+LC+'" id="trial_'+LC+'" value="">';
	newElem +='<input type="hidden" name="cur_date" id="cur_date" value="<?php echo date('Y-m-d'); ?>">';
	newElem +='<div style="vertical-align:top;padding-right:2px;">';
	newElem +='<table class="table_collapse table_cell_padd5">';
		newElem +='<tr>';
			newElem +='<td class="blueColor" style="width:20px;font-weight:bold;">';
				newElem +='OD';
			newElem +='</td>';
			newElem +='<td>';
				newElem +='<label for="upc_name_'+LC+'" style="margin-right:5px;">UPC</label>';
				newElem +='<input type="hidden" name="item_id_'+LC+'" id="item_id_'+LC+'" value="">';
				newElem +='<input type="hidden" name="item_id_'+LC+'_os" id="item_id_'+LC+'_os" value="">';
				newElem +='<input type="hidden" name="upc_id_'+LC+'" id="upc_id_'+LC+'" value="">';
          		newElem +='<input type="text" onChange="javascript:get_details_by_upc(document.getElementById(\'upc_id_'+LC+'\'),'+LC+');"  name="upc_name_'+LC+'" id="upc_name_'+LC+'" style="width:100px;" value="" autocomplete="off" />';
				newElem +='<input type="hidden" name="allowed_'+LC+'" id="allowed_'+LC+'" value="">';
				newElem +='<input type="hidden" name="allowed_'+LC+'_os" id="allowed_'+LC+'_os" value="">';
			newElem +='</td>';
			newElem +='<td>';
        		newElem +='<label for="manufacturer_id_'+LC+'" style="margin-right:5px;">Manufacturer</label>'
		        newElem +='<select name="manufacturer_id_'+LC+'" style="width:108px;" id="manufacturer_id_'+LC+'" onChange="get_manufacture_brand(this.value,0,'+LC+',3); get_vendor_list(this.value, '+LC+');">';
	            	newElem +='<option value="0">Please Select</option>'
            		<?php  
			            $qry="";
            			$qry = imw_query("select `id`, `manufacturer_name` from in_manufacturer_details where del_status='0' and cont_lenses_chk='1' order by manufacturer_name asc");
            			while($rows = imw_fetch_array($qry))
			            {
					?>
				            newElem +='<option value="<?php echo $rows['id']; ?>"><?php echo addslashes($rows['manufacturer_name']); ?></option>';
		            <?php }	?>
				newElem +='</select>';
			newElem +='</td>'
			newElem +='<td>';
				newElem +='<label for="brand_id_'+LC+'">Brand&nbsp;</label>';
					newElem +='<select name="brand_id_'+LC+'" id="brand_id_'+LC+'" style="width:108px;" onChange="load_item_idoc(this.value, \''+LC+'\');">';
						newElem +='<option value="0">Please Select</option>';
						<?php 
		                    $qry = "";
        		            $qry = imw_query("select `id`, `brand_name`, `source_idoc` from `in_contact_brand` where del_status='0' order by brand_name asc");
                		    while($rows = imw_fetch_assoc($qry)){	
               			 ?>
					            newElem +='<option value="<?php echo $rows['id'];?>" idoc_source="<?php echo $rows['source_idoc']; ?>"><?php echo addslashes($rows['brand_name']); ?></option>';
			            <?php } ?>
				newElem +='</select>';
			newElem +='</td>';
        	newElem +='<td>';
				newElem +='<label for="item_vendor_'+LC+'" style="margin-right:5px;">Vendor</label>';
				newElem +='<select name="item_vendor_'+LC+'" id="item_vendor_'+LC+'" style="width:108px">';
					newElem +='<option value="0">Please Select</option>';
					<?php
						$vendort_qry = imw_query("SELECT 
												DISTINCT(`v`.`id`), 
												`v`.`vendor_name` 
											FROM 
												`in_manufacturer_details` `m` 
												INNER JOIN `in_vendor_manufacture` `vm` ON(
													`m`.`cont_lenses_chk` = 1 
													AND `m`.`id` = `vm`.`manufacture_id`
												) 
												INNER JOIN `in_vendor_details` `v` ON(`vm`.`vendor_id` = `v`.`id`) 
											WHERE 
												`v`.`del_status` = 0 
											ORDER BY 
												`v`.`vendor_name` ASC");
					if($vendort_qry && imw_num_rows($vendort_qry)>0){
					while($row = imw_fetch_object($vendort_qry)){
						echo 'newElem +=\'<option value="'.$row->id.'">'.addslashes($row->vendor_name).'</option>\';';
					}
				}
			?>
			newElem +='</select>';
          	newElem +='<input type="text" onChange="javascript:get_details_by_upc(document.getElementById(\'upc_id_'+LC+'\'),'+LC+');" style="width:100px; display:none;" name="item_name_'+LC+'" id="item_name_'+LC+'" value="" autocomplete="off" />';
		newElem +='</td>';
		newElem +='<td>';
			newElem +='<label for="item_prac_code_'+LC+'" style="margin-right:5px;">Prac Code</label>';
			newElem +='<input type="text" name="item_prac_code_'+LC+'" id="item_prac_code_'+LC+'" value="" title="" style="width:100px;" autocomplete="off" onChange="updatePracCode(this);" /></td>';
		newElem +='<td style="width:100px;"><label for="trial_chk_'+LC+'" style="margin-right:2px;">Trial</label>';
        	newElem +='<input type="checkbox" name="trial_chk_'+LC+'" id="trial_chk_'+LC+'" value="1" style="height:15px; width:15px; margin:0; vertical-align:text-bottom;" onChange="mark_trial(\''+LC+'\')">';
			newElem +='<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById(\'module_type_id_'+LC+'\').value, '+LC+');" style="text-decoration:none;float:right;">';
				newElem +='<img style="margin-top:1px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock"/></a>';
		newElem +='</td>';
	newElem +='</tr>';
	newElem +='<tr>';
		newElem +='<td class="greenColor" style="width:20px; font-weight:bold;">';
			newElem +='OS';
		newElem +='</td>';
		newElem +='<td>';
			newElem +='<label for="upc_name_'+LC+'_os" style="margin-right:5px;">UPC</label>';
			newElem +='<input type="hidden" name="upc_id_'+LC+'_os" id="upc_id_'+LC+'_os" value="">';
          	newElem +='<input type="text" onChange="javascript:get_details_by_upc(document.getElementById(\'upc_id_'+LC+'_os\'),\''+LC+'_os\', \'\', \'os\');"  name="upc_name_'+LC+'_os" id="upc_name_'+LC+'_os" style="width:100px;" value="" autocomplete="off" />';
		newElem +='</td>';
        newElem +='<td>';
        newElem +='<label for="manufacturer_id_'+LC+'_os" style="margin-right:5px;">Manufacturer</label>';
         	newElem +='<select name="manufacturer_id_'+LC+'_os" style="width:108px;" id="manufacturer_id_'+LC+'_os" onChange="get_manufacture_brand(this.value,0,\''+LC+'_os\',3);get_vendor_list(this.value, \''+LC+'_os\');">';
            	newElem +='<option value="0">Select</option>';
				<?php  
		            $qry="";
        		    $qry = imw_query("select `id`, `manufacturer_name` from in_manufacturer_details where del_status='0' and cont_lenses_chk='1' order by manufacturer_name asc");
		            while($rows = imw_fetch_array($qry))
        		    {
				?>
			            newElem +='<option value="<?php echo $rows['id']; ?>"><?php echo addslashes($rows['manufacturer_name']); ?></option>';
            <?php }	?>
			newElem +='</select>';
		newElem +='</td>';
		newElem +='<td>';
			newElem +='<label for="brand_id_'+LC+'">Brand&nbsp;</label>';
			newElem +='<select name="brand_id_'+LC+'" id="brand_id_'+LC+'_os" style="width:108px;" onChange="load_item_idoc(this.value, \''+LC+'_os\');">';
            	newElem +='<option value="0">Select</option>';
			<?php
				$qry = "";
				$qry = imw_query("select `id`, `brand_name`, `source_idoc` from `in_contact_brand` where del_status='0' order by brand_name asc");
				while($rows = imw_fetch_assoc($qry)){	
			?>
				newElem +='<option value="<?php echo $rows['id'];?>" idoc_source="<?php echo $rows['source_idoc']; ?>"><?php echo addslashes($rows['brand_name']);?></option>';
            <?php } ?>
			newElem +='</select>';
		newElem +='</td>';
		newElem +='<td>';
			newElem +='<label for="item_vendor_'+LC+'_os" style="margin-right:5px;">Vendor</label>';
			newElem +='<select name="item_vendor_'+LC+'_os" id="item_vendor_'+LC+'_os" style="width:108px">';
				newElem +='<option value="0">Please Select</option>';
			<?php
				$vendort_qry = imw_query("SELECT 
												DISTINCT(`v`.`id`), 
												`v`.`vendor_name` 
											FROM 
												`in_manufacturer_details` `m` 
												INNER JOIN `in_vendor_manufacture` `vm` ON(
													`m`.`cont_lenses_chk` = 1 
													AND `m`.`id` = `vm`.`manufacture_id`
												) 
												INNER JOIN `in_vendor_details` `v` ON(`vm`.`vendor_id` = `v`.`id`) 
											WHERE 
												`v`.`del_status` = 0 
											ORDER BY 
												`v`.`vendor_name` ASC");
				if($vendort_qry && imw_num_rows($vendort_qry)>0){
					while($row = imw_fetch_object($vendort_qry)){
						echo 'newElem +=\'<option value="'.$row->id.'">'.addslashes($row->vendor_name).'</option>\';';
					}
				}
			?>
			newElem +='</select>';
			newElem +='<input type="text" onChange="javascript:get_details_by_upc(document.getElementById(\'upc_id_'+LC+'_os\'),\''+LC+'_os\', \'\', \'os\');" style="width:100px; display:none;" name="item_name_'+LC+'_os" id="item_name_'+LC+'_os" value="<?php echo $sel_order['item_name_os']; ?>" autocomplete="off" />';
		newElem +='</td>';
		newElem +='<td>';
			newElem +='<label for="item_prac_code_'+LC+'_os" style="margin-right:5px;">Prac Code</label>';
			newElem +='<input type="text" name="item_prac_code_'+LC+'_os" id="item_prac_code_'+LC+'_os" value="" title="" style="width:100px;" autocomplete="off" onChange="updatePracCode(this);" />';
		newElem +='</td>';
		newElem +='<td style="width:100px;"><label for="trial_chk_'+LC+'_os" style="margin-right:2px;">Trial</label>';
        	newElem +='<input type="checkbox" name="trial_chk_'+LC+'_os" id="trial_chk_'+LC+'_os" value="1" style="height:15px; width:15px; margin:0; vertical-align:text-bottom;" onChange="mark_trial(\''+LC+'_os\')">';
			newElem +='<a href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById(\'module_type_id_'+LC+'\').value, \''+LC+'_os\');" style="text-decoration:none;float:right;">';
				newElem +='<img style="margin-top:1px; width:22px;" src="../../images/search.png" border="0" class="serch_icon_stock" title="Search stock"/></a>';
		newElem +='</td>';
	newElem +='</tr>'
	
	newElem +='</table>';
	
	
	
	newElem +='<div class="module_border" style="margin:10px 0;">';
	newElem +='<div style="width:647px;margin:8px 0px 5px 5px;padding-top:5px;" class="fl conflens">';
	newElem +='<table class="table_collapse table_cell_padd5 contact_pres" border="0">';
	newElem +='<tr><td align="left"><span style="display:inline-block;"><span class="blueColor" style="font-weight:bold;">&nbsp;OD</span></span></td>';
	
	newElem +='<td align="left" style="width:162px;"><label for="cl_sphere_min_'+LC+'">SPH&nbsp;</label>';
	newElem +='<input type="text" name="cl_sphere_min_'+LC+'" id="cl_sphere_min_'+LC+'" value="" /> ';
	newElem +='<input type="text" name="cl_sphere_max_'+LC+'" id="cl_sphere_max_'+LC+'" value="" /></td>';
	
	newElem +='<td align="left" style="width:156px;"><label for="cl_cyl_min_'+LC+'">CYL</label>';
	newElem +='<input type="text" name="cl_cyl_min_'+LC+'" id="cl_cyl_min_'+LC+'" value="" /> ';
	newElem +='<input type="text" name="cl_cyl_max_'+LC+'" id="cl_cyl_max_'+LC+'" value="" /></td>';
	
	newElem +='<td align="left" style="width:94px;"><label for="cl_axis_min_'+LC+'">Axis</label>';
	newElem +='<input type="text" name="cl_axis_min_'+LC+'" id="cl_axis_min_'+LC+'" value="" /></td>';
	
	newElem +='<td align="left" style="width:87px;"><label for="cl_bc_min_'+LC+'">BC</label>';
	newElem +='<input type="text" name="cl_bc_min_'+LC+'" id="cl_bc_min_'+LC+'" value="" /></td>';
	
	newElem +='<td align="left" style="width:90px;"><label for="cl_dia_min_'+LC+'">Dia</label>';
	newElem +='<input type="text" name="cl_dia_min_'+LC+'" id="cl_dia_min_'+LC+'" value="" /></td>';
	newElem +='</tr>';
	
	newElem +='<tr><td align="left"><span style="display:inline-block;width:28px;"><span class="greenColor" style="font-weight:bold;">&nbsp;OS</span></span></td>';
	
	newElem +='<td align="left" style="width:162px;"><label for="cl_sphere_min_os_'+LC+'">SPH&nbsp;</label>';
	newElem +='<input type="text" name="cl_sphere_min_os_'+LC+'" id="cl_sphere_min_os_'+LC+'" value="" /> ';
	newElem +='<input type="text" name="cl_sphere_max_os_'+LC+'" id="cl_sphere_max_os_'+LC+'" value="" /></td>';
	
	newElem +='<td align="left" style="width:156px;"><label for="cl_cyl_min_os_'+LC+'">CYL</label>';
	newElem +='<input type="text" name="cl_cyl_min_os_'+LC+'" id="cl_cyl_min_os_'+LC+'" value="" /> ';
	newElem +='<input type="text" name="cl_cyl_max_os_'+LC+'" id="cl_cyl_max_os_'+LC+'" value="" /></td>';
	
	newElem +='<td align="left" style="width:94px;"><label for="cl_axis_min_os_'+LC+'">Axis</label>';
	newElem +='<input type="text" name="cl_axis_min_os_'+LC+'" id="cl_axis_min_os_'+LC+'" value="" /></td>';
	newElem +='<td align="left" style="width:87px;"><label for="cl_bc_min_os_'+LC+'">BC</label>';
	newElem +='<input type="text" name="cl_bc_min_os_'+LC+'" id="cl_bc_min_os_'+LC+'"value="" /></td>';
	
	newElem +='<td align="left" style="width:90px;"><label for="cl_dia_min_os_'+LC+'">Dia</label>';
	newElem +='<input type="text" name="cl_dia_min_os_'+LC+'" id="cl_dia_min_os_'+LC+'" value="" /></td>';
	
	newElem +='</tr></table></div>'
	
	newElem +='<div id="rx_div_'+LC+'" class="module_border fr rightRx od_os_span" style="width:420px; margin:5px 10px 5px 5px;">';
			/*<!----------------------------------- RX Hidden Fields For OD----------------------------------->*/
	newElem +='<input type="hidden" name="cl_sphere_od_'+LC+'" id="cl_sphere_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_cylinder_od_'+LC+'" id="cl_cylinder_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_axis_od_'+LC+'" id="cl_axis_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_base_od_'+LC+'" id="cl_base_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_diameter_od_'+LC+'" id="cl_diameter_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_add_od_'+LC+'" id="cl_add_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_for_od_'+LC+'" id="cl_for_od_'+LC+'" value="" />';	/*<!--Select List-->*/
			/*<!----------------------------------- RX Hidden Fields For Os----------------------------------->*/
	newElem +='<input type="hidden" name="cl_sphere_os_'+LC+'" id="cl_sphere_os_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_cylinder_os_'+LC+'" id="cl_cylinder_os_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_axis_os_'+LC+'" id="cl_axis_os_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_base_os_'+LC+'" id="cl_base_os_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_diameter_os_'+LC+'" id="cl_diameter_os_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_add_os_'+LC+'" id="cl_add_os_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="cl_for_os_'+LC+'" id="cl_for_os_'+LC+'" value="" />';	/*<!--Select List-->*/
	newElem +='<input type="hidden" name="rx_dos_'+LC+'" id="rx_dos_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="rx_make_od_'+LC+'" id="rx_make_od_'+LC+'" value="" />';
	newElem +='<input type="hidden" name="rx_make_os_'+LC+'" id="rx_make_os_'+LC+'" value="" />';
	
	newElem +='<table style="padding-left:0px;border-collapse:collapse;"><tr>';
	
	newElem +='<th style="width:95px;text-align:left;"><a href="javascript:void(0);" class="text_purpule" onClick="javascript:prescription_details('+LC+');" style="padding-left:5px;">Rx</a><span id="disp_rx_dos_'+LC+'" style="font-weight:normal;float:right;"></span></th>';
	newElem +='<th>SPH</th>';
	newElem +='<th>CYL</th>';
	newElem +='<th>AXIS</th>';
	newElem +='<th>BC</th>';
	newElem +='<th>DIAM</th>';
	newElem +='<th>ADD</th>';
	newElem +='</tr>';
	newElem +='<tr>';
	newElem +='<td style="padding-left:5px;text-align:left;">';
		newElem +='<span class="blueColor" style="font-weight:bold">OD</span>';
		newElem +='<span id="rx_make_od_'+LC+'_disp" title=""></span>';
	newElem +='</td>';
	newElem +='<td><span class="span_data" id="sph_text_od_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="cyl_text_od_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="axis_text_od_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="base_text_od_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="diam_text_od_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="add_text_od_'+LC+'"></span></td>';
	newElem +='</tr>';
	
	newElem +='<tr class="span_data1">';
	newElem +='<td style="padding-left:5px;text-align:left;">';
		newElem +='<span class="greenColor" style="font-weight:bold">OS</span>';
		newElem +='<span id="rx_make_os_'+LC+'_disp" title=""></span>';
	newElem +='</td>';
	newElem +='<td><span class="span_data" id="sph_text_os_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="cyl_text_os_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="axis_text_os_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="base_text_os_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="diam_text_os_'+LC+'"></span></td>';
	newElem +='<td><span class="span_data" id="add_text_os_'+LC+'"></span></td>';
	newElem +='</tr></table></div>';
	newElem +='<strong></strong><div style="clear:both;"></div></div>';
	
	newElem +='<table><tr><td><table class="table_cell_padd5 noPadTop"><tr>';
	newElem +='<td style="width:80px;"><label for="color_id_'+LC+'">Color</label></td>';
	
	newElem +='<td style="width:130px;"><select name="color_id_'+LC+'" id="color_id_'+LC+'" class="select2"><option value="">Please Select</option><?php $qry=""; $qry = imw_query("select * from in_color where del_status='0' order by color_name asc"); $colorVals = array(); while($rows = imw_fetch_array($qry)){$colorVals[$rows['id']]=$rows['color_name'];?><option value="<?php echo $rows['id']; ?>"><?php echo addslashes($rows['color_name']); ?></option>;<?php } ?></select></td>';
	
	newElem +='<td style="width:50px;"><label for="cl_type_'+LC+'">Type</label></td>';
	
	newElem +='<td style="width:130px;"><select name="cl_type_'+LC+'" id="cl_type_'+LC+'" class="select2"><option value="">Please Select</option><?php $sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='2' and module_id='0' AND `opt_sub_type` IN(0,1) AND `del_status`='0' order by opt_val"; $resp = imw_query($sql); if($resp && imw_num_rows($resp)>0){ while($row = imw_fetch_assoc($resp)){echo '<option value="'.$row['id'].'">'.addslashes($row['opt_val']).'</option>';}}?></select></td>';
	
	newElem +='<td style="width:110px;"><label for="cl_replacement_'+LC+'">Replacement</label></td>';
	newElem +='<td style="width:130px;"><select name="cl_replacement_'+LC+'" id="cl_replacement_'+LC+'" class="select2"><option value="" repAttr="0">Please Select</option><?php $sql="Select * from in_options where opt_type='4' and module_id='3' and del_status='0' order by opt_val"; $resp=imw_query($sql); $repVals=array(); if($resp && imw_num_rows($resp)>0){ while($row=imw_fetch_array($resp)){ $selected=""; $repVals[$row['id']]=$row['opt_val'];echo '<option value="'.$row['id'].'" repAttr="'.$row['opt_val'].'">'.addslashes($row['opt_val']).'</option>';}}?></select></td></tr>';
	
	newElem +='<tr><td><label for="cl_packaging_'+LC+'">Packaging</label></td><td><select name="cl_packaging_'+LC+'" id="cl_packaging_'+LC+'" class="select2"><option value="1" packAttr="1">Please Select</option><?php $sql="Select * from in_options where opt_type='5' and module_id='3' and del_status='0' order by opt_val"; $resp=imw_query($sql); if($resp && imw_num_rows($resp)>0){while($row=imw_fetch_array($resp)){$selected="";$cAttr = "";if(preg_match('/\d+/', $row['opt_val'])){if(preg_match('/\d+/', $row['opt_val'], $matches)){$cAttr = $matches[0];}}echo '<option value="'.$row['id'].'" packAttr="'.$cAttr.'">'.addslashes($row['opt_val']).'</option>';}}?></select></td>';
	
	newElem +='<td><label for="supply_id_'+LC+'">Supply</label></td>';
	newElem +='<td><select name="supply_id_'+LC+'" id="supply_id_'+LC+'" class="select2"><option value="" cAttr="0">Please Select</option><?php $sql="select * from in_supply where del_status='0' order by supply_name asc"; $resp=imw_query($sql); $supplyVals=array(); if($resp && imw_num_rows($resp)>0){while($row=imw_fetch_array($resp)){$supplyVals[$row['id']]=$row['supply_name'];echo '<option value="'.$row['id'].'" cAttr="'.$row['supply_name'].'">'.addslashes($row['supply_name']).'</option>';}}?></select></td>';
	
	newElem +='<td><label for="cl_wear_sch_'+LC+'">Wear Schedule</label></td>';
	newElem +='<td><select name="cl_wear_sch_'+LC+'" id="cl_wear_sch_'+LC+'" class="select2">';
	newElem +='<option value="">Please Select</option><?php $sql="select * from in_contact_cat where del_status='0' order by cat_name asc"; $resp=imw_query($sql);$wearVals=array(); if($resp && imw_num_rows($resp)>0){while($row=imw_fetch_array($resp)){$wearVals[$row['id']]=$row['cat_name']; echo '<option value="'.$row['id'].'">'.addslashes($row['cat_name']).'</option>';}}?></select></td></tr></table>';
	
	newElem +='<table class="table_cell_padd5"><tr><td colspan="2" style="width:215px;text-align:center;">Quantity</td>';
	newElem +='<td style="width:50px"><label for="cl_usage_'+LC+'">Usage</label></td>';
	
	newElem +='<td style="width:130px;"><select name="cl_usage_'+LC+'" id="cl_usage_'+LC+'" class="select2"><option value="">Please Select</option><?php $sql = "SELECT `id`, `opt_val` FROM `in_options` WHERE `opt_type`='1' and module_id='0' AND `opt_sub_type` IN(0,1) AND `del_status`='0' order by opt_val"; $resp = imw_query($sql); if($resp && imw_num_rows($resp)>0){ while($row = imw_fetch_assoc($resp)){echo '<option value="'.$row['id'].'">'.addslashes($row['opt_val']).'</option>';}}?></select></td>';
	
	newElem +='<td style="width:70px;"><label for="cl_physician_name_'+LC+'" style="margin-right:1px;">Doctor</label></td><td style="width:130px;"><input type="text" name="cl_physician_name_'+LC+'" id="cl_physician_name_'+LC+'" style="width:92%;" value="" autocomplete="off" /><input type="hidden" name="cl_physician_id_'+LC+'" id="cl_physician_id_'+LC+'" value="" /></td></tr>';
	
	newElem +='<tr><td colspan="2"><label for="qty_right_'+LC+'">Right Eye</label>&nbsp;<input type="text" name="qty_right_'+LC+'" id="qty_right_'+LC+'" style="width:30px;" value="0" onChange="chk_dis_fun(\''+LC+'\');" />&nbsp;&nbsp;&nbsp;<label for="qty_'+LC+'">Left Eye</label>&nbsp;<input type="text" name="qty_'+LC+'" id="qty_'+LC+'" style="width:30px;" value="0" onChange="chk_dis_fun(\''+LC+'_os\');" /></td>';
	
	newElem +='<td><label for="cl_disinfecting_'+LC+'">Disinfecting</label></td>';
	newElem +='<td><select name="cl_disinfecting_'+LC+'" id="cl_disinfecting_'+LC+'" class="select2" onChange="pos_row_disinfect(this.value, '+LC+');"><option value="0">Please Select</option><?php $sql = "SELECT `id`, `name`, `prac_code`, `price` FROM `in_cl_disinfecting` WHERE `del_status`='0' order by `name` ASC"; $resp = imw_query($sql); if($resp && imw_num_rows($resp)>0){ while($row = imw_fetch_assoc($resp)){echo '<option value="'.$row['id'].'" prac_code="'.$row['prac_code'].'" price="'.$row['price'].'">'.addslashes($row['name']).'</option>';}}?></select></td>';
	
	newElem +='<td><label for="cl_telephone_'+LC+'" style="width:39px;float:left;">Tel.</label></td>';
	newElem +='<td><input type="text" name="cl_telephone_'+LC+'" id="cl_telephone_'+LC+'" style="width:92%" value="" onChange="set_phone_format(this,\'<?php echo $GLOBALS['phone_format'];?>\');" /></td></tr></table>';
	
	newElem +='<table class="table_cell_padd5" style="width:100%;"><tr style="display:none;"><td style="width:65px;"><label for="price_'+LC+'">Price</label></td>';
	
	newElem +='<td style="width:80px;"><input type="hidden" name="rtl_price_'+LC+'" id="rtl_price_'+LC+'" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun('+LC+');" value="" /><input type="hidden" name="rtl_price_'+LC+'_os" id="rtl_price_'+LC+'_os" onChange="this.value = parseFloat(this.value).toFixed(2); chk_dis_fun(\''+LC+'_os\');" value="" /><input type="text" name="price_'+LC+'" id="price_'+LC+'" value="" style="width:64px;" class="currency" onChange=\'$("#rtl_price_'+LC+'").val(this.value).trigger("change");\' /><input type="text" name="price_'+LC+'_os" id="price_'+LC+'_os" value="" style="width:64px;" class="currency" onChange=\'$("#rtl_price_'+LC+'").val(this.value).trigger("change");\' /></td>';
	
	newElem +='<td style="width:70px">&nbsp;<label for="discount_'+LC+'">Discount</label></td>';
	newElem +='<td style="width:60px;">';
	newElem +='<input type="text" name="discount_'+LC+'" id="discount_'+LC+'" value="" style="width:92%;" onChange="chk_dis_fun('+LC+');" />';
	newElem +='<input type="text" name="discount_'+LC+'_os" id="discount_'+LC+'_os" value="" style="width:92%;" onChange="chk_dis_fun('+LC+');" />';
	newElem +='</td>';
	
	newElem +='<td style="width:65px;"><label for="discount_code">Dis. Code</label></td>';
	newElem +='<td style="width:130px;"><select name="discount_code_'+LC+'" id="discount_code" class="text_10 disc_code" style="width:100%;" onChange="discountChanged(this);"><option value="">Please Select</option>';
		var defSelected = "";
		$.each(discountCodes, function(di, dval){
			defSelected = (mainDiscountCode==di)?'selected="selected"':'';
			newElem +='<option value="'+di+'" '+defSelected+'>'+dval+'</option>';
		});
	newElem +='</select></td>';
	
	newElem +='<td style="width:40px;"><label for="total_amount_'+LC+'">Total</label></td>';
	newElem +='<td style="width:70px"><input type="text" name="total_amount_'+LC+'" id="total_amount_'+LC+'" value="" style="width:54px" readonly class="currency" /><input type="text" name="total_amount_'+LC+'_os" id="total_amount_'+LC+'_os" value="" style="width:54px" readonly class="currency" /></td></tr>';
	
	newElem +='<tr>';
		newElem +='<td style="width:114px;">';
			newElem +='<label for="use_on_hand_chk_'+LC+'">Use on hand</label>&nbsp;';
			newElem +='<input type="checkbox" name="use_on_hand_chk_'+LC+'" id="use_on_hand_chk_'+LC+'" value="1" style="height:15px;width:15px;margin:0;vertical-align:middle;">';
		newElem +='</td>';
		newElem +='<td style="width:91px;text-align:right;padding-right:5px;">';
			newElem +='<label for="order_chk_'+LC+'">&nbsp;Order</label>&nbsp;';
			newElem +='<input type="checkbox" name="order_chk_'+LC+'" id="order_chk_'+LC+'" value="1" style="height:15px;width:15px;margin:3px;vertical-align:middle;">';
		newElem +='</td>';
        newElem +='<td style="width:78px;">';
			newElem +='<label for="dx_code_'+LC+'">DX Code</label>';
		newElem +='</td>';
		newElem +='<td style="width:132px;">';
        	newElem +='<input type="text" name="dx_code_'+LC+'" id="dx_code_'+LC+'" style="width:116px;" value="" onChange="get_dxcode(this);" autocomplete="off" />';
        newElem +='</td>';
        newElem +='<td style="width:100px;"><label for="cl_outside_rx_'+LC+'">Outside Rx</label>&nbsp;\n';
			newElem +='<input type="checkbox" value="1" name="cl_outside_rx_'+LC+'" id="cl_outside_rx_'+LC+'" style="height:15px;width:15px;margin:0;vertical-align:middle;" /></td>';
        newElem +='<td style="visibility:hidden;">';
        	newElem +='<label style="display:none;" for="cl_neutralize_rx">Neutralize</label>&nbsp;';
			newElem +='<input type="checkbox" value="1" name="cl_neutralize_rx" id="cl_neutralize_rx" style="height:15px;width:15px;margin:0;display:none;" />';
		newElem +='</td>';
	newElem +='</tr></table></td>';
	
	newElem +='<td style="width:400px;vertical-align:top;">';
		
		newElem +='<table class="table_cell_padd5 noPadTop">';
			newElem += '<tr>';
				newElem += '<td style="width:110px;padding:0;">';
					newElem += '<label for="dominant_eye_'+LC+'">Dominant Eye</label>';
				newElem += '</td>';
				newElem += '<td style="width:110px;padding: 0">';
					newElem += '<label for="fit_type_'+LC+'">Fit Type</label>';
				newElem += '</td>';
				newElem += '<td></td>';
				newElem += '<td></td>';
			newElem += '</tr>';
			newElem += '<tr>';
				newElem += '<td style="padding: 0;">';
					newElem += '<select name="dominant_eye_'+LC+'" id="dominant_eye_'+LC+'" class="blueColor" onChange="changeDominantEye(this)">';
						newElem += '<option value="OD" class="blueColor">OD</option>';
						newElem += '<option value="OS" class="greenColor">OS</option>';
					newElem += '</select>';
				newElem += '</td>';
				newElem += '<td style="padding: 0;">';
					newElem += '<select name="fit_type_'+LC+'" id="fit_type_'+LC+'">';
						newElem += '<option value="1">Initial Fit</option>';
						newElem += '<option value="2">Refit</option>';
					newElem += '</select>';
				newElem += '</td>';
			newElem += '</tr>';
			newElem += '<td colspan="4" style="padding:  0;">';
				newElem += '<label for="item_comment_'+LC+'" style="margin:10px 0 3px 0;display:block;">Comments</label>';
				newElem += '<textarea name="item_comment_'+LC+'" id="item_comment_'+LC+'" style="width:378px;height:84px;resize:none;padding:5px;font-family:\'Gill Sans\', \'Gill Sans MT\', \'Myriad Pro\', \'DejaVu Sans Condensed\', Helvetica, Arial, sans-serif"></textarea>';
			newElem += '</td>';
		newElem += '</table>';
		newElem +='</td></tr></table></div></div>';
	
	$("#contactLenses").prepend(newElem);
	
	$("#upc_name_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensData',
		hidIDelem: document.getElementById('upc_id_'+LC),
		showAjaxVals: 'upc'
	});
	$("#upc_name_"+LC+"_os").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensData',
		hidIDelem: document.getElementById('upc_id_'+LC+'_os'),
		showAjaxVals: 'upc'
	});
	
	
	$("#item_name_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'contactLensData',
		hidIDelem: document.getElementById('upc_id_'+LC),
		showAjaxVals: 'name'
	});
	
	$("#dx_code_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'dxCodes',
		hidIDelem: document.getElementById('dx_code_'+LC),
		showAjaxVals: 'code'
	});
	
	$("#item_prac_code_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC'
	});
	/*hidIDelem: document.getElementById('item_prac_code_'+LC),*/
	
	$("#cl_physician_name_"+LC).ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'physicianData',
		hidIDelem: document.getElementById('cl_physician_id_'+LC),
		showAjaxVals: 'name',
		minLength:1
	});
	
	$("#last_cont").val(LC);
	currencySymbols();
}
function get_rx_dx_code(id){
	var v_icd_10 = 10;
	var s_os=document.getElementById('cl_sphere_os_'+id).value;
	var s_od=document.getElementById('cl_sphere_od_'+id).value;
	var ad_os=document.getElementById('cl_add_os_'+id).value;
	var ad_od=document.getElementById('cl_add_od_'+id).value;
	var t_dx_2="";
	var t_dx="";
	
	var t_dx_arr = new Array();
	if((ad_od!="" && ad_od!="+")||(ad_os!="" && ad_os!="+")){
		t_as_2="Presbyopia"; t_dx_2=(v_icd_10=="9")? "367.4" : "H52.4; ";
	}
	if(s_od!="" && s_od!="-" && s_od!="+"){
		if(s_od.indexOf("-")!=-1){t_as="Myopia"; t_dx_arr.push((v_icd_10=="9")? "367.1" : "H52.11");}
		else{t_as="Hyperopia"; t_dx_arr.push((v_icd_10=="9")? "367.0" : "H52.01");}
	}
	if(s_os!="" && s_os!="-" && s_os!="+"){
		if(s_os.indexOf("-")!=-1){t_as="Myopia"; t_dx_arr.push((v_icd_10=="9")? "367.1" : "H52.12");}
		else{t_as="Hyperopia"; t_dx_arr.push((v_icd_10=="9")? "367.0" : "H52.02");}
	}
	
	t_dx_arr = $.unique(t_dx_arr);
	t_dx_arr = t_dx_arr.join("; ");
	t_dx_arr = t_dx_arr.trim();
	t_dx_arr += "; ";
	
	var final_dx_code=t_dx_2+t_dx_arr;
	document.getElementById('dx_code_'+id).value=final_dx_code;
	
}

$(document).ready(function(e) {
/*PreSelect VisionPlan*/
<?php if($order_id==''): ?>
$('#main_ins_case_id_1').val(top.main_iframe.ptVisionPlanId);
<?php endif; ?>

	//BUTTONS
	var mainBtnArr=[];
	var btnCounter = 0;
<?php 
	if($order_del_status==0){
	if($order_edit_btn_status): ?>
	mainBtnArr[btnCounter] = new Array("frame","Save","top.main_iframe.admin_iframe.frm_sub_fun('save');");
	btnCounter++;
	mainBtnArr[btnCounter] = new Array("frame","Cancel","top.main_iframe.admin_iframe.frm_sub_fun('cancel')");
	btnCounter++;
<?php endif; ?>
	<?php if($_SESSION['order_id']!=""){?>
		mainBtnArr[btnCounter] = new Array("frame","Order Print","top.main_iframe.admin_iframe.printpos(<?php echo $_SESSION['order_id'];?>)");
		btnCounter++;
		mainBtnArr[btnCounter] = new Array("frame","Patient Receipt","top.main_iframe.admin_iframe.patientReceipt(<?php echo $_SESSION['order_id'];?>)");
		btnCounter++;
		<?php if($order_post_btn_status): ?>
			mainBtnArr[btnCounter] = new Array("frame","Post","top.main_iframe.admin_iframe.frm_sub_fun('order_post')");
			btnCounter++;
		<?php endif; ?>
		mainBtnArr[btnCounter] = new Array("frame","Reorder","top.main_iframe.admin_iframe.frm_sub_fun('reorder')");
		btnCounter++;
	<?php }
	elseif($order_post_btn_status){?>
		mainBtnArr[btnCounter] = new Array("frame","Post","top.main_iframe.admin_iframe.frm_sub_fun('order_post')");	
		btnCounter++;
	<?php }
	
	}else{
		?>
	mainBtnArr[btnCounter] = new Array("frame","Cancel","top.main_iframe.admin_iframe.frm_sub_fun('cancel')");
	btnCounter++;
	<?php
	}?>

	top.btn_show("admin",mainBtnArr);		
});

/*
 *Ajax Default Options - Loader
 */
$(document).ajaxSend(function(){
	$("#loading").show();
});
$(document).ajaxComplete(function(){
	$("#loading").hide();
});

/*Update PracCode in POS*/
function updatePracCode(obj){
	var id = $(obj).attr('id');
	var val = $(obj).val();
	
	$("#pos_"+id).val(val);
}

/*Delete Disinfectent POS Row*/
function delPosRowDis(module, counter){
	var row = $(".posTable tr#"+module+"_"+counter+"_di");
	$(row).find("#di_del_item_"+counter).val(1);
	$(row).addClass('hideRow');
	$("#cl_disinfecting_"+counter).val(0);
	calculate_all();
}

function prac_code_typeahead(){
	$(".posTable .pracodefield").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'praCode',
		showAjaxVals: 'defaultCodeC'
	});
	$(".posTable .pracodefield").attr('autocomplete', 'off');
	$('.posTable input[name^="di_item_prac_code_"]').attr('disabled', 'disabled');
	
	$('input[name^="pos_item_prac_code_"]').on('change', function(){
		nVal = $(this).val();
		pId = $(this).attr('id');
		pId = pId.replace(/^pos_/, "");
		$("#"+pId).val(nVal);
	});
}

/*Alter Vendor Options on change Manufacturer*/
function get_vendor_list(mid, key){
	
	if(typeof(mid)!="undefined" && parseInt(mid)!=0){
		params = {};
		params.action = "vendorListCL";
		params.manuf = mid;
		
		$.ajax({
			type: 'POST',
			data: params,
			url: top.WRP+"/interface/patient_interface/ajax.php",
			success: function(response){
				response = $.parseJSON(response);
				
				opts = '<option value="0">Please Select</option>';
				if(Object.keys(response).length>0){
					$.each(response, function(key, value){
						opts += '<option value="'+value+'">'+key+'</option>';
					});
				}
				$("#item_vendor_"+key).html(opts);
			}
		});
	}
}
/*End Alter Vendor options on change manufacturer*/

/*Change Brand from Rx popup*/
function change_brand_rx(manufacturer_id,brand_id,key){
	reqFlag = true;
	get_manufacture_brand(manufacturer_id,brand_id,key,3,true);
	//$("#"+key).trigger('change');
}

/*Load Item Details By Brand Name*/
function load_item_idoc(value, key){
	
	if(value!="" || value!=0 && reqFlag){
		option = $('#brand_id_'+key+' option[value="'+value+'"]');
		if($(option).attr('idoc_source')=="1"){
			
			var name = $(option).text();
			
			var params = {};
			params.action = "find_contact_id";
			params.item_name = name;
			
			$.ajax({
				method: 'POST',
				data: params,
				url: top.WRP+"/interface/admin/ajax.php",
				success: function(response){
					if(response!=""){
						$("#upc_id_"+key).val(response);
						$("#item_name_"+key).val(name).trigger('change');
					}
					reqFlag = false;
				}
			});
		}
	}
}
/*End load item name by brand name*/

/*Mark Item as Trial*/
function mark_trial(key){
	
	/*Upc Code Field*/
	var upc_val = $('#upc_name_'+key).val();
	var item_uid = $('#item_id_'+key).val();
	
	if(upc_val!='' || item_uid!=''){
		
		/*Get status of Trial Checkbox for the item being ordered*/
		trial_check = $('#trial_chk_'+key).is(':checked');
		if(trial_check){
			$('#discount_'+key).val("0");
			$('#rtl_price_'+key).val(0.00).trigger('change');
		}
		else{
			/*Get Price for the Item already loaded in the interface -  When trial is unchecked for the same*/
			var params = {action:'upcPrice', module:3, upc_code:upc_val, item_id:item_uid};
			$.ajax({
				method: 'POST',
				url: top.WRP+'/interface/patient_interface/ajax.php',
				data: params,
				success: function(response){
					response = $.parseJSON(response);
					if(Object.keys(response).length>0){
						$("#discount_"+key).val(response.discount);
						$("#rtl_price_"+key).val(parseFloat(response.price).toFixed(2)).trigger('change');
					}
				}
			});
		}
	}
}

function select_custom(manuf_id, brand_id, retail_price, key){
	var id_custom	= '<?php echo $GLOBALS['CUSTOM_CONTACT_LENS']['id']; ?>';
	var upc_custom	= '<?php echo $GLOBALS['CUSTOM_CONTACT_LENS']['upc']; ?>';
	var name_custom	= '<?php echo $GLOBALS['CUSTOM_CONTACT_LENS']['name']; ?>';
	
	$( '#upc_name_'+key ).val(upc_custom);
	$( '#upc_id_'+key ).val(upc_custom);
	$( '#item_name_'+key ).val(name_custom);
	
	$( '#manufacturer_id_'+key ).val(manuf_id);
	get_manufacture_brand(manuf_id,brand_id,key,3);
	
	$( '#rtl_price_'+key ).val(retail_price);
	$( '#allowed_'+key).val(retail_price);
	$( '#price_hidden_'+key).val(retail_price);
	
	total = 0;
	dissc = 0;
	total = cal_discount((retail_price*0), dissc);
	$( '#dispTotalF').val(total);
	$( '#trial_chk_'+key ).prop('checked', false);
	
	$( '#order_chk_'+key ).prop('checked', true);
	$( '#use_on_hand_chk_'+key ).prop('checked', false);
	
	$( '#dispQtyF' ).val( (parseInt($("#qty_"+key).val())+parseInt($("#qty_right_"+key).val())) );
	
	item = {};
	item.discount_till	= '0000-00-00';
	item.discount		= '';
	item.upc_code		= upc_custom;
	item.name			= name_custom;
	item.id				= id_custom;
	item.retail_price	= retail_price;
	item.item_prac_code	= '<?php echo $default_prac_code; ?>';
	item.module_type_id	= 3;
	
	get_prac_code_text('<?php echo $default_prac_code; ?>', 'item_prac_code_'+key, 'frm_cont', 3);
	addNewRow(item.module_type_id, item, key, '', '');
}

function changeDominantEye(obj){
	
	if( $(obj).val() == 'OD' ){
		$(obj).removeClass('greenColor').addClass('blueColor');
	}
	else{
		$(obj).removeClass('blueColor').addClass('greenColor');
	}
}
</script>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
<input type="hidden" id="delItemId" />
</body>
</html>