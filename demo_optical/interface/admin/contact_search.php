<?php
/*
File: contact_search.php
Coded in PHP7
Purpose: View Contact Lens Search
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 

///manage width of page
if($_REQUEST['open']){
	$tabl_width='89.9';
	$td_width='140';
	$inputwidth='90';
	$heigh_div=$_SESSION['wn_height']-510;
}
else {
		$tabl_width='99.9';
		$td_width='162';
		$heigh_div='382';
	 }

$soruse_flag = (isset($_REQUEST['source']) && $_REQUEST['source']=="ptInterface")?true:false;
	 
$frm_method=$_REQUEST['frm_method'];
$manufacturer_Id_Srch=$_REQUEST['manuf_id'];
$opt_vendor_id=$_REQUEST['vendor'];
$opt_brand_id=$_REQUEST['brand'];
$cat_id_search=$_REQUEST['cat_name'];
$color_id_search=$_REQUEST['color'];
$material_id_search=$_REQUEST['type'];
$supply_id_search=$_REQUEST['supply'];
$bc_id_search=$_REQUEST['bc'];
$diameter_id_search=$_REQUEST['diameter'];
$price_frm=$_REQUEST['price_frm'];
$price_to=$_REQUEST['price_to'];
//get search result//
if($_POST['search_result'])
{
	$manufacturer_Id_Srch = $_POST['manufacturer_Id_Srch'];
	$opt_vendor_id= $_POST['opt_vendor_id'];
	$opt_brand_id = $_POST['opt_brand_id'];
	$upcval = $_POST['upc_name'];
	$name_txt = $_POST['name_txt'];
	$color_id_search=$_POST['color'];
	$supply_id_search=$_POST['supply'];
	$cat_id_search=$_POST['category'];
	$material_id_search=$_POST['material'];
	$bc_id_search=$_POST['bc'];
	$diameter_id_search=$_POST['diameter'];	
	$trial_id_search=$_POST['trial'];
	$price_frm_search=$_POST['price_frm'];
	$price_to_search=$_POST['price_to'];	
	
if($search_id<=0){
	$search_id=1;
}

$and="";
$tbNameJoin="";
if($opt_vendor_id!='' && $opt_vendor_id>0){
	$tb_field= ",VT.vendor_name";
	$and="And it.vendor_id='$opt_vendor_id'";
	$tbNameJoin= "LEFT join in_vendor_details as VT on VT.id = it.vendor_id";
}
if($opt_brand_id!='' && $opt_brand_id>0){
	$tb_field.= ",BT.brand_name";
	$and.=" And it.brand_id='$opt_brand_id'";
	$tbNameJoin.= " LEFT join in_contact_brand as BT on BT.id = it.brand_id";
}
if($upcval!=''){
	$and.="And it.upc_code like('$upcval%')";
}

if($name_txt!=''){
	$and.="And it.name like ('$name_txt%')";
}

if($manufacturer_Id_Srch!='' && $manufacturer_Id_Srch>0){
	$and.=" And it.manufacturer_id='$manufacturer_Id_Srch'";	
}

if($color_id_search!='' && $color_id_search>0){
	$and.=" And it.color='$color_id_search'";	
}
if($supply_id_search!='' && $supply_id_search>0){
	$and.=" And it.supply_id='$supply_id_search'";	
}
if($material_id_search!='' && $material_id_search>0){
	$and.=" And it.type_id='$material_id_search'";	
}
if($cat_id_search!='' && $cat_id_search>0){
	$and.=" And it.class_id='$cat_id_search'";	
}
if($bc_id_search!=''){
	$and.=" And it.bc='$bc_id_search'";	
}
if($diameter_id_search!=''){
	$and.=" And it.diameter='$diameter_id_search'";	
}
if($trial_id_search=="on"){
	$and.=" And it.trial_chk='1'";	
}
if($price_frm_search>0){
	$and.=" And it.retail_price>='$price_frm_search'";	
}
if($price_to_search>0){
	$and.=" And it.retail_price<='$price_to_search'";	
}
if(isset($_REQUEST['in_stock_chk']) && $_REQUEST['in_stock_chk']=='1'){
	$and.=" And it.qty_on_hand>0";
}

	$qry = "select it.*,
	FT.module_type_name,MT.manufacturer_name
	$tb_field
	from in_item  as it 
	LEFT join in_module_type as FT on FT.id = it.module_type_id
	LEFT join in_manufacturer_details as MT on MT.id = it.manufacturer_id
	$tbNameJoin
	where it.del_status='0' and it.module_type_id = '3'
	".$and." order by it.upc_code asc, it.name asc";

	$sql = imw_query($qry);
}
$show_heading='<div class="fl">Contact Lens Search </div>';

if($opt_brand_id || $manufacturer_Id_Srch)
{ 
	/*Custom Contact Lens List*/
	$sql_custom = 'SELECT `cb`.`id` AS \'brand_id\', `cb`.`brand_name`, `cb`.`retail_price`,
					IF(`manuf`.`id` IS NULL, 0, `manuf`.`id`) AS \'manuf_id\',
					IF(`manuf`.`manufacturer_name` IS NULL, \'\', `manuf`.`manufacturer_name`) AS \'manuf_name\'
				FROM `in_contact_brand` `cb`
				LEFT JOIN `in_contact_brand_manufacture` `bm` ON(`cb`.`id` = `bm`.`brand_id`)
				LEFT JOIN `in_manufacturer_details` `manuf` ON(`bm`.`manufacture_id` = `manuf`.`id` AND `manuf`.`del_status` = 0)
				WHERE `cb`.`del_status` = 0';

	if($opt_brand_id!='' && $opt_brand_id>0){
		$sql_custom .= ' AND `cb`.`id`='.((int)$opt_brand_id);
	}
	if($manufacturer_Id_Srch!='' && $manufacturer_Id_Srch>0){
		$sql_custom .= ' AND `manuf`.`id`='.((int)$manufacturer_Id_Srch);
	}
	$sql_custom = imw_query($sql_custom);
	/*Custom Contact Lens List*/
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript">
CURRENCY_SYMBOL = '<?php currency_symbol(); ?>';
window.opener = window.opener.main_iframe.admin_iframe;
</script>
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<style>
.btn_cls{
    background: url("../../images/btnbg.png") repeat-x scroll 0 0 transparent;
    border: medium none;
    border-radius: 5px 5px 5px 5px;
    color: #FFFFFF;
    cursor: pointer;
    height: 31px;
    padding: 5px 20px;
}
.txtcolr{
	color:#000;
	text-decoration:none;
}
td a:hover{
	color:#0080FF;
	text-decoration:none;
}
</style>
<script language="javascript"> 
var itemCounter = '<?php echo (isset($_REQUEST['itemCounter']) && $_REQUEST['itemCounter']!="")?$_REQUEST['itemCounter']:false; ?>';
function order_custom_lens(manuf_id, brand_id, retail_price){
	window.opener.select_custom(manuf_id, brand_id, retail_price, itemCounter);
	window.close();
}
function changeParent(upc_val) {
	if(typeof(window.opener)!="undefined"){
		var page_Other = "<?php echo $_REQUEST['module_typePat'];?>";
		var frm_method = "<?php echo $_REQUEST['frm_method'];?>";
		var search_id = "<?php echo $_REQUEST['srch_id'];?>";
		if(search_id=='3')
		{
			if(window.opener.document.getElementById('upc_id_'+itemCounter) && itemCounter){
				window.opener.document.getElementById('upc_id_'+itemCounter).value=upc_val;	
				window.opener.document.getElementById('upc_name_'+itemCounter).onchange();	
			}
			else if(window.opener.document.getElementById('upc_name')){
				window.opener.document.getElementById('upc_name').value=upc_val;	
				window.opener.upc(upc_val,'itm_id');
			}else{
				window.opener.location.href = window.opener.location+'?upc_name='+upc_val+'&frm_method='+frm_method;	
			}
		}
		else { 
			 window.opener.location.href = window.opener.location+'?upc_name='+upc_val+'&frm_method='+frm_method;
		}	 
	}
  	window.close();
} 

function page_change_acc_type()
{
	var type = "contact lenses";
	var pages = new Array();
	var page_action = "<?php echo $_REQUEST['module_typePat'];?>";
	
	if(page_action=='patient_interPage'){
		pages['frame'] = "../patient_interface/pt_frame_selection.php";
		pages['lenses'] = "../patient_interface/lens_selection.php";
		pages['contact lenses'] = "../patient_interface/contact_selection.php";
		pages['medicine'] = "../patient_interface/other_selection.php";
	}
	else{
		pages['frame'] = "frame/index.php";
		pages['lenses'] = "lens/index.php";
		pages['contact lenses'] = "contact_lens/index.php";
		pages['supplies'] = "supplies/index.php";
		pages['medicine'] = "medicines/index.php";
		pages['accessories'] = "accessories/index.php";
	}
	return pages[type];
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
function clean_val(id){
	if(document.getElementById(id).value=="Min"){
		document.getElementById(id).value="";
	}
	if(document.getElementById(id).value=="Max"){
		document.getElementById(id).value="";
	}
}
</script> 
</head>
<body>
	<div style="padding:0px; width:100%;">
        <div class="listheading"><?php echo $show_heading;?></div>
        <div>
        	<form method="post" action="" name="stock_srch_frm">
               <table style="width:<?php echo $tabl_width;?>%;" cellspacing="0">
                	<tr style="background: #D5Efff;">
						<td style="padding-left:5px;">
							<input type="text" name="upc_name" value="<?php echo $upcval;?>" style="width:90px;" />
							<div class="label">Upc Code</div>
						</td>
						<td>
							<select name="manufacturer_Id_Srch" id="manufacturer_Id_Srch" style="width:100px" onChange="javascript:get_vendorFromManufacturer(this.value,'0');">
								<option value="0">Select Manufacturer</option>
								<?php $rows="";
								$rows = data("select * from in_manufacturer_details where cont_lenses_chk='1' and del_status='0' order by manufacturer_name asc");
								foreach($rows as $r)
								{ 
									$selected = "";
									if($soruse_flag && $r['id']==$_POST['manuf_id']){
										$selected = 'selected="selected"';
									}
								?>
								  <option value="<?php echo $r['id']; ?>" <?php echo $selected; ?>><?php echo ucfirst($r['manufacturer_name']); ?></option>
						 		<?php }	?>
							</select>
							<div class="label">Manufacturer</div>
						</td>
						<td>
							<select name="opt_vendor_id" style="width:100px;" id="opt_vendor_id" onChange="javascript:get_brandFromVendor(this.value,'0',document.getElementById('type_optical_id').value);">
								<option value="0">Select Vendor</option>
								<?php $rows="";
									  $rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
									  foreach($rows as $r)
									  { ?>
										<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['vendor_name']); ?></option>	
								<?php }	?>
							</select>
							<div class="label">Vendor</div>
						</td>
						<td>
							<select name="opt_brand_id"  id="opt_brand_id" style="width:100px" >
								<option value="0">Select Brand</option>
								<?php $rows="";
									  $rows = data("select id,brand_name as frame_source from in_contact_brand where del_status='0' order by brand_name asc");
									  foreach($rows as $r)
									  { ?>
										<option <?php if($_REQUEST['brand']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['frame_source']); ?></option>	
								<?php }	?>
							</select>
							<div class="label">Brand</div>
						</td>
						<td>
							 <input type="checkbox" name="trial" style="width:40px;" <?php if($_REQUEST['trial']=="on"){echo"checked";} ?> />
							 <div class="label">Trial</div>
						</td>
						<?php /*<td>
							<select name="material"  id="material" style="width:100px;">
								<option value="0">Select Material</option>
								<?php 
								$rows="";
								$rows = data("select id, type_name from in_type where del_status='0' order by type_name asc");
								foreach($rows as $r)
								{ ?>
								<option <?php if($_REQUEST['material']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['type_name']); ?></option>	
								<?php }	?>
							</select>
							<div class="label">Material</div>
						</td>
						<td style="display:none;">
							<select name="category"  id="category" style="width:100px;">
								<option value="0">Select Wear Time</option>
								<?php 
								$rows="";
								$rows = data("select id, cat_name from in_contact_cat where del_status='0' order by cat_name asc");
								foreach($rows as $r)
								{ ?>
								<option <?php if($_REQUEST['category']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['cat_name']); ?></option>	
								<?php }	?>
							</select>
							<div class="label">Wear time</div>
						</td>
						<td>
							<select name="supply"  id="supply" style="width:100px;">
								<option value="0">Select Supply</option>
								<?php 
								$rows="";
								$rows = data("select id, supply_name from in_supply where del_status='0' order by supply_name asc");
								foreach($rows as $r)
								{ ?>
								<option <?php if($_REQUEST['supply']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['supply_name']); ?></option>	
								<?php }	?>
							</select>
							<div class="label">Supply</div>
						</td>
						<td>
							<select name="color"  id="color" style="width:100px;">
								<option value="0">Select Color</option>
								<?php 
								$rows="";
								$rows = data("select id, color_name from in_color where del_status='0' order by color_name asc");
								foreach($rows as $r)
								{ ?>
								<option <?php if($_REQUEST['color']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['color_name']); ?></option>	
								<?php }	?>
							</select>
							<div class="label">Color</div>
						</td>*/?>
						<?php
							if($price_frm==""){
								$price_frm="Min";
							}else{
								$price_frm=$_REQUEST['price_frm'];
							}
							if($price_to==""){
								$price_to="Max";
							}else{
								$price_to=$_REQUEST['price_to'];
							}
						?>
						<td>
							 <input type="text" name="price_frm" id="price_frm" value="<?php echo $price_frm;?>" style="width:65px;" onClick="clean_val('price_frm');" class="currency" /> - <input type="text" name="price_to" id="price_to" value="<?php echo $price_to;?>" style="width:65px;" onClick="clean_val('price_to');" class="currency" />
							 <div class="label" style="text-align:center;padding-left:0px;">Price Range</div>
						</td>
						<td>
							 <input type="text" name="bc" value="<?php echo $_REQUEST['bc'];?>" style="width:90px;" />
							 <div class="label">B.C</div>
						</td>
						<td>
							 <input type="text" name="diameter" value="<?php echo $_REQUEST['diameter'];?>" style="width:90px;" />
							 <div class="label">Diameter</div>
						</td>
						<td style="text-align:center;">
							 <input style="margin:0;height:18px;width:18px;vertical-align:middle;cursor:pointer;" type="checkbox" name="in_stock_chk" id="in_stock_chk" value="1" <?php echo (isset($_REQUEST['in_stock_chk']) && $_REQUEST['in_stock_chk']=='1')?'checked':''; ?>/>
							 <div style="padding-left:0;padding-right:5px;" class="label"><label for="in_stock_chk">In Stock</label></div>
						</td>
						<td colspan="4">
                			<input class="btn_cls" type="submit" name="search_result" value="Search" />
						</td>
					</tr>
					<tr style="background: #D5Efff; display:none;">
						
						<td style="display:none;">
							<input type="text" name="name_txt" value="<?php echo $name_txt;?>" style="width:90px;" />
							<div class="label">Item Name</div>
						</td>
						
						
						<td></td>
					</tr>
                </table>
                </form> 
        </div>
			<table class="table_collapse listheading table_cell_padd5" align="center" style="width:99.7%; margin-top:5px; display:<?php if(isset($_POST['search_result']) || $from=='style'){ echo "inline-table"; } else { echo "none"; } ?>;">
                <tr>
                    <td style="width:35px; text-align:center; font-size:14px;">Sr #</td>
                    <td style="width:100px; text-align:center; font-size:14px;">UPC</td>
                    <td style="width:90px; text-align:center; font-size:14px;">Name</td>
					<td style="width:110px; text-align:center; font-size:14px;">Prac Code</td>
                    <td style="width:90px; font-size: 14px; text-align:center;">Manufacturer</td>
                    <td style="width:100px; font-size: 14px; text-align:center;">Vendor</td>
                    <td style="width:90px; font-size: 14px; text-align:center">Brand</td>
					<td style="width:100px; font-size: 14px; text-align:center">Trial</td>
			        <td style="width: 47px; font-size: 14px; text-align:center;">Cost</td>
                    <td style="width: 45px; font-size: 14px; text-align:left;">T. Qty</td>
                    <td style="width: 65px; font-size: 14px; text-align:left;">Fac. Qty</td>
                </tr>
             </table>
            
                  <div style="overflow-y:scroll; height:<?php echo $heigh_div;?>px; width:99.7%; display:<?php if(isset($_POST['search_result'])){ echo "block"; } else { echo "none"; } ?>">
                   <table class="table_collapse cellBorder table_cell_padd5" align="center" style="width:99.7%" >   
						<?php 
						$sr_no=1;
						$noStock = true;
						if(imw_num_rows($sql)>0){
                        	$noStock = false;
						/*Get Default Formula for the item Type*/
							$default_formula = get_retail_formula(3, array('manufacturer_id'=>$manufacturer_Id_Srch, 'brand_id'=>$opt_brand_id));
						/*End Get Default Formula for the item Type*/
						
                          while($sql_result = imw_fetch_array($sql)){
							/*Retail Prices Markup - Caclulation*/
								if($sql_result['retail_price_flag']=='0' ){
									
									if( trim($sql_result['formula'])=='' ){
										$sql_result['formula'] = $default_formula;
									}
									/*Final Retail Price for the Item - based on formula calculation*/
									if( $sql_result['formula']!='' ){
										$sql_result['retail_price'] = calculate_markup_price($sql_result['formula'],  $sql_result['wholesale_cost'],  $sql_result['purchase_price']);
									}
									/*End Final Retail Price for the Item*/
								}
							/*End Retail Prices Markup - Caclulation*/
							  	$sql_result['brand_id'];
								$sql_result['vendor_id'];
															  
							 $get_vandor_name=imw_query('select vendor_name from in_vendor_details where id in('.$sql_result['vendor_id'].')'); 
							 $rowsvandor_name = imw_fetch_array($get_vandor_name);
							 $get_brand_name = imw_query('select brand_name as frame_source from in_contact_brand where id in('.$sql_result['brand_id'].')');
							
							 $rowsbrand_name = imw_fetch_array($get_brand_name);
							 $rowsstyle_name = imw_fetch_array($get_style_name);

							$fac_stock='';
							//GETTING FACILITY STOCK
							$qry="Select SUM(stock) as 'fac_stock' FROM in_item_loc_total WHERE loc_id='".$_SESSION['pro_fac_id']."' AND item_id='".$sql_result['id']."'";
							$rs=imw_query($qry);
							$res=imw_fetch_assoc($rs);
							$fac_stock=$res['fac_stock'];
							unset($rs);							 
                        ?>
                         <tr>
                            <td style="width:35px;  text-align:center;">
								<?php echo $sr_no;?>
								<input type="hidden" name="upc_hidden_val<?php echo $sr_no; ?>" id="upc_hidden_val<?php echo $sr_no; ?>" value="<?php echo $sql_result['id'];?>" />
							</td>
                            <td class="break_word" style="width:90px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><span class="gg_val"><?php echo $sql_result['upc_code'];?></span></a></td>
                            <td class="break_word" style="width:90px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $sql_result['name'];?></a></td>
							<td class="break_word" style="width:100px; text-align:center;">
								<?php $qry = imw_query("select cpt_prac_code, cpt_desc from cpt_fee_tbl where cpt_fee_id='".$sql_result['item_prac_code']."' and delete_status = '0'");
								  $res = imw_fetch_assoc($qry); ?>
								<a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)" title="<?php echo $res['cpt_desc'];?>">
								 <?php echo $res['cpt_prac_code'];?>
								</a>
							</td>
                            <td class="break_word" style="width:100px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $sql_result['manufacturer_name']?></a></td>
                            <td class="break_word" style="width:90px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo  $rowsvandor_name['vendor_name']; ?></a></td>
                            <td class="break_word res_brand_dis" style="width:90px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $rowsbrand_name['frame_source']?></a></td>
							<td class="break_word" style="width:90px; text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php if($sql_result['trial_chk']==1){ echo "Yes"; } else { echo "No"; } ?></a></td>
                 			<td class="break_word" style="width:50px;  text-align:right;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php currency_symbol(); ?><?php echo number_format($sql_result['retail_price'],2); ?></a></td>
                            <td class="break_word" style="width:50px;  text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $sql_result['qty_on_hand']; ?></a></td>
                            <td class="break_word" style="width:50px;  text-align:center;"><a class="txtcolr" href="javascript:void(0);" onClick="javascript:changeParent(document.getElementById('upc_hidden_val<?php echo $sr_no; ?>').value)"><?php echo $fac_stock; ?></a></td>
                        </tr>
                        <?php 
                        $sr_no++;
                        }
						}
						
						$custom_cl_flag = (isset($GLOBALS['CUSTOM_CONTACT_LENS']) && $GLOBALS['CUSTOM_CONTACT_LENS']['upc']!='')?true:false;
						if( imw_num_rows($sql_custom)>0 && $custom_cl_flag ){
							$noStock = false;
							$c_upc	= $GLOBALS['CUSTOM_CONTACT_LENS']['upc'];
							$c_name	= $GLOBALS['CUSTOM_CONTACT_LENS']['name'];
							while( $clrow = imw_fetch_object($sql_custom) ){
								$click_function = "order_custom_lens(".$clrow->manuf_id.", ".$clrow->brand_id.", '".$clrow->retail_price."')";
						?>
								<tr>
									<td style="width:35px;text-align:center;"><?php echo $sr_no; ?></td>
									<td class="break_word" style="width:90px; text-align:center;">
										<a class="txtcolr" href="javascript:void(0);" onClick="<?php echo $click_function; ?>"><?php echo $c_upc; ?></a>
									</td>
									<td class="break_word" style="width:90px; text-align:center;">
										<a class="txtcolr" href="javascript:void(0);" onClick="<?php echo $click_function; ?>"><?php echo $c_name; ?></a>
									</td>
									<td class="break_word" style="width:100px; text-align:center;"></td>
									<td class="break_word" style="width:100px; text-align:center;">
										<a class="txtcolr" href="javascript:void(0);" onClick="<?php echo $click_function; ?>"><?php echo $clrow->manuf_name; ?></a>
									</td>
									<td class="break_word" style="width:90px; text-align:center;"></td>
									<td class="break_word res_brand_dis" style="width:90px; text-align:center;">
										<a class="txtcolr" href="javascript:void(0);" onClick="<?php echo $click_function; ?>"><?php echo $clrow->brand_name; ?></a>
									</td>
									<td class="break_word" style="width:90px; text-align:center;">
										<a class="txtcolr" href="javascript:void(0);" onClick="<?php echo $click_function; ?>">No</a>
									</td>
									<td class="break_word" style="width:50px; text-align:center;">
										<a class="txtcolr" href="javascript:void(0);" onClick="<?php echo $click_function; ?>"><?php echo $clrow->retail_price; ?></a>
									</td>
									<td class="break_word" style="width:50px; text-align:center;"></td>
									<td class="break_word" style="width:50px; text-align:center;"></td>
								</tr>
						<?php
								$sr_no++;		
							}
						}
						
						if($noStock)
						{ ?>
                            <tr>
                            	<td colspan="11" align="center" height="50">No Record Found</td>
                            </tr>
				<?php 	}	?>
                    </table> 
                  </div>  
 			</div>
			<script>
				$(document).ready(function(e) { 
					var vendor_id ="<?php echo $opt_vendor_id; ?>";
					var brand_id ="<?php echo $opt_brand_id; ?>";
					var manufacture_id ="<?php echo $manufacturer_Id_Srch; ?>";
					get_type_manufacture('3',manufacture_id);
					get_vendorFromManufacturer(manufacture_id,vendor_id);
					get_brandFromVendor(vendor_id,brand_id,'3');
				});
            </script>  
    </body>
</html>