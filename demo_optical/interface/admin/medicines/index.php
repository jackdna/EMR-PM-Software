<?php 
/*
File: index.php
Coded in PHP7
Purpose: Add/Edit/Delete Medicines
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php"); 

$stringAllUpc = get_upc_name_id('6');

$AllUpcArray=array();
$AllUpcIdArray=array();

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

$getname = imw_query("select id, upc_code from in_item where upc_code!='' and del_status='0'");
$getnameArr = array();
while($getnameRow=imw_fetch_array($getname))
{
	$getnameArr[] = "'".$getnameRow['id']."~~~".$getnameRow['upc_code']."'";
}

$proNameArr = implode(',',$getnameArr);

//--------------------START GETTING DATA OF MEDICINE TYPE------------------
    
$rows="";
$rows = data("select * from `in_medicines_types` where del_status='0' order by type_name asc");
	foreach($rows as $r)
  	{ 
		$med_type_opt.= '<option value='.$r['id'].'>'.ucfirst($r['type_name']).'</option>';	
		$newIdName.= $r['id'].'-'.ucfirst($r['type_name']).',';	
	}	



//------------------------	START GETTING DATA FOR MENUS TO Procedure CATEGORY-----------------------//
	$proc_code_arr=array();
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
			}
		$arrCptCodes[] = array($row["cpt_category"],$arrSubOptions);
		}		
	}

	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Procedures	------------------------//

//------------------------	START GETTING DATA FOR MENUS TO DX Code -----------------------//
	/*$dx_code_arr=array();
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
				$arrDXCodesAndDesc[] = $rowCodes["diagnosis_id"];
				$arrDXCodesAndDesc[] = $rowCodes["d_prac_code"];
				$arrDXCodesAndDesc[] = $rowCodes["diag_description"];
				
				$code = str_replace(";","~~~",$rowCodes["d_prac_code"]);
				$DX_desc = str_replace(";","~~~",$rowCodes["diag_description"]);
				$stringAllDX.="'".str_replace("'","",$code)."',";	
				$stringAllDX.="'".str_replace("'","",$DX_desc)."',";
				$dx_code_arr[$rowCodes["diagnosis_id"]]=$rowCodes["d_prac_code"];
			}
		$arrDXCodes[] = array($row["category"],$arrSubOptions);
		}		
	}*/

	$icd10_sql_qry=imw_query("select id,icd10,icd10_desc from icd10_data where deleted='0'");
	while($icd10_sql_row=imw_fetch_array($icd10_sql_qry)){
		$icd10_dx=str_replace('-','',$icd10_sql_row['icd10']);
		$icd10_desc_arr[$icd10_dx]=$icd10_sql_row['icd10_desc'];
		$dx_code_arr[$icd10_sql_row["id"]]=$icd10_sql_row["icd10"];
		
		$code = str_replace(";","~~~",$icd10_sql_row["icd10"]);
		$DX_desc = str_replace(";","~~~",$icd10_sql_row["icd10_desc"]);
		$stringAllDX.="'".str_replace("'","",$code)."',";	
		$stringAllDX.="'".str_replace("'","",$DX_desc)."',";
	}
	$stringAllDX = substr($stringAllDX,0,-1);
	
//------------------------	END GETTING DATA FOR MENUS TO CATEGORY OF Dx Code ------------------------//

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
<script>
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
$(function() {
	var cyear = new Date().getFullYear();		
	$( "#datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
	bindExpiryDate();
});

function bindExpiryDate(){
	$( ".expiryDate" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
}

</script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.js?<?php echo constant("cache_version"); ?>"></script> 
<script type="text/javascript">var jQ = jQuery.noConflict();</script>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.multiSelect.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript">
$(document).ready(function(){

	jQ("#med_typ").multiSelect({noneSelected:'Select All'});
	
	selectProName = function(proname, id)
	{
		var chk_dup=0;
		$.each([<?php echo $proNameArr; ?>], function( index, value ) 
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
	}
});
</script>
<script>

var custom_array_upc_id;
<?php if($AllUpcArray!=""){?>
	var custom_array_upc= new Array(<?php echo remLineBrk($AllUpcArray); ?>);
<?php } 
 if($AllNameArray!=""){?>
	var custom_array_name= new Array(<?php echo remLineBrk($AllNameArray); ?>);
<?php }
 if($AllUpcIdArray!="" && count($AllUpcIdArrays)>1){?>
	custom_array_upc_id= new Array(<?php echo remLineBrk($AllUpcIdArray); ?>);
<?php } else{ ?>
	custom_array_upc_id= new Array('<?php echo $AllUpcIdArray; ?>');
<?php } 
 if($stringAllProcedures!=""){	?>
	var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
<?php } 
 if($stringAllDX!=""){	?>
	var customarrayDX= new Array(<?php echo remLineBrk($stringAllDX); ?>);
<?php } ?>



</script>
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
var AllMedTyps='<?php echo $newIdName;?>';

function upc(upc_code,current_txt,id)
{
	var curID=(id)?'_'+id:'_1';
	//edit function stopped for now
	var ucode = (typeof(upc_code) == "object" )? $.trim(upc_code.value): upc_code;
	var dataString = 'action=managestock&upc='+ucode;
	var arrayname=custom_array_name;
	var name_val=$("#name").val();
	if(($.inArray(name_val, arrayname)== -1)&& name_val && current_txt=='current_txt'){
		return false;
		}
	var upcname=custom_array_upc;
	var upc_val=$("#upc_name"+curID).val();
	if(($.inArray(upc_val, upcname)== -1) && upc_val && (current_txt=='upc_txt')){
		//return false;
	}
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
					$("#edit_item_id"+curID).val(item.id);
					$("#upc_id"+curID).val(item.id);
					if(item.stock_image!="")
					{
						$("#item_image img").attr("src","../../../images/supplies_stock/"+item.stock_image);
					}
					else
					{
						$("#item_image img").attr("src","../../../images/no_product_image.jpg");	
					}
					if(!name_val)$("#name").val(item.name);
					$("#manufacturer").val(item.manufacturer_id);
					$("#upc_name"+curID).val(item.upc_code);
					$("#module_type").val(item.module_type_id);
					if(current_txt){$("#name").val(item.name);}
					$("#vendor").val(item.vendor_id);
					get_vendor_manufacturer(item.manufacturer_id,item.vendor_id);
					$("#brand").val(item.brand_id);					
					$("#type_desc").val(item.type_desc);
					
					$("#units"+curID).val(item.units);
					$("#threshold"+curID).val(item.threshold);
					$("#dosage"+curID).val(item.dosage);
					$("#ndc").val(item.ndc);
					$("#pay_by").val(item.pay_by);
					$("#med_typ select").html("");
					var medTypes = "";
					var medTyp = "";
					var medTypArr=item.med_typ.split(','); 
					var medTypArr2=AllMedTyps.split(',');
					
					for(i = 0; i<medTypArr2.length; i++){
						medTypes = medTypArr2[i];
						medTyp = medTypes.split('-');
						var preMedHtml=$("#med_typ").html();
						if($.inArray(medTyp[0], medTypArr)!=-1)
						{
							$("#med_typ").html(preMedHtml+"<option selected value="+medTyp[0]+">"+medTyp[1]+"</option>");
						}else
						{
							$("#med_typ").html(preMedHtml+"<option value="+medTyp[0]+">"+medTyp[1]+"</option>");
						}
					}
					$("#med_typ").css({"width":"138px","color":"#000","font-size":"14px","font-family":"Arial, Helvetica, sans-serif !important"});
					jQ("#med_typ").multiSelect({noneSelected:'Select All'});
				
					$("#fee").val(item.fee);
					
					if(item.harcardous=="1")
					{
						$("#hazardous").prop('checked',true);
					}
					
					$("#retail_price"+curID).val(item.retail_price);
					$("#wholesale_cost"+curID).val(item.wholesale_cost);
					$("#purchase_price"+curID).val(item.purchase_price);
					$("#qty_on_hand"+curID).val(item.qty_on_hand);
					$("#dx_code"+curID).val(item.dx_code);
					
					$("#amount"+curID).val(item.amount);
					$("#discount").val(item.discount);
					get_prac_code_name(item.item_prac_code,'item_prac_code'+curID);
					if(item.discount_till!="00-00-0000"){
					$("#datepicker").val(item.discount_till);
					}
					if(item.expiry_date!="00-00-0000") {
						$("#expiry_date"+curID).val(item.expiry_date);
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
function add_qty_fun(type,id){
	var item_id=document.getElementById('edit_item_id_'+id).value;
	top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('location_lot_popup','../lens/location_lot_popup.php?item_add='+type+'&id=_'+id+'&item_id='+item_id,'location_lot_popup','width=540,height=440,left=600,scrollbars=no,top=150');
	addwin.focus();
}
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

function stock_search(type){
var manuf_id = document.getElementById('manufacturer').value;
var vendor = document.getElementById('vendor').value;
	top.WindowDialog.closeAll();
	var addwin=top.WindowDialog.open('location_lot_popup','../stock_search.php?srch_id='+type+'&manuf_id='+manuf_id+'&vendor='+vendor,'location_popup','width=1137,height=500,left=180,scrollbars=no,top=150');
	addwin.focus();
}

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
if(isset($_REQUEST['save']))
{
	//extract($_POST);

	//medicine_stock($edit_item_id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$type_desc,$num_size,$measurement,$char_size,$other,$hazardous,$qty_on_hand,$amount,$wholesale_cost,$retail_price,$discount,$disc_date,$units,$dosage,$med_typ,$ndc,$pay_by,$fee);
	medicine_stock();
	echo "<script>top.falert('Record saved successfully'); window.location.href='index.php'</script>";
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
</style>
<div class="listheading mt10">
	<div style="width:1045px; float:left;">Medicines</div>
	<div>
		<!--<a href="javascript:void(0);" style="vertical-align:text-top" class="text_purpule" onClick="javascript:product_history(document.getElementById('edit_item_id').value);">
           HX
        </a>-->
		<a style="margin-left:10px;" href="javascript:void(0);" onClick="javascript:stock_search(document.getElementById('module_type').value);">
        <img style="width:22px; margin-top:1px;" src="../../../images/search.png" class="serch_icon_stock" title="Search stock"/>
        </a>
    </div>
</div>
<div style="height:<?php echo $_SESSION['wn_height']-450;?>px;">
    <form onSubmit="return validateForm()" action=""  name="material_form" id="stock_form" method="post" enctype="multipart/form-data">
    
	
    <table class="table_collapse table_cell_padd5" style="width:100%">
        <tr>
            <td><select style="width:163px;" name="module_type" id="module_type" onChange="page_change_acc_type();">
              <?php $rows="";
              $rows = data("select * from in_module_type where del_status='0' order by module_type_name asc");
              foreach($rows as $r)
              {
				  ?>
              <option <?php if(strtolower($r['module_type_name'])=="medicine"){ echo "selected"; } ?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['module_type_name']); ?></option>
              <?php }	?>
            </select>
            </td>
            <td class="module_label">Type</td>
            <td ><input type="text" onChange="javascript:return upc(document.getElementById('upc_id_1'),'current_txt');" name="name" id="name" autocomplete="off"/></td>
            <td  class="module_label">Name</td>
			  <td>&nbsp;</td>
              <td  class="module_label">&nbsp;</td>
        </tr>
        <tr>
            <td><select style="width:163px;" name="manufacturer" id="manufacturer" onChange="get_vendor_manufacturer(this.value,'0');">
              <option value="">Please Select</option>
              <?php $rows="";
                          $rows = data("select * from in_manufacturer_details where medicine_chk='1' and del_status='0' order by manufacturer_name asc");
                          foreach($rows as $r)
                          { ?>
              <option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['manufacturer_name']); ?></option>
              <?php }	?>
            </select></td>
            <td class="module_label">Manufacturer</td>
            <td><select style="width:163px;" name="vendor" id="vendor">
              <option value="">Please Select</option>
              <?php 
                            $sql="select id,vendor_name from in_vendor_details where del_status = '0'";
                            $res = imw_query($sql);
                            while($row = imw_fetch_array($res))
                            {
                            ?>
              <option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['vendor_name']); ?></option>
              <?php 
                            } ?>
            </select></td>
            <td class="module_label">Vendor</td>
            <td colspan="2">&nbsp;</td>
        </tr>
    
              <tr>
                <td width="199" style="font-family: Arial, Helvetica, sans-serif !important; font-size:14px; font-weight:none;">
                    <select  style="width:145px;" name="med_typ" id="med_typ">
                        <option value="">Select</option>
                         <?php  echo $med_type_opt; ?>
                    </select>
                  </td>
                  <td width="515" align="left">Type of Medicines</td>
                  <td width="186"><select style="width:163px;" name="pay_by" id="pay_by">
                    <option value="0">Self Pay</option>
                    <option value="1">Insurance</option>
                  </select></td>
                  <td width="532" class="module_label">Pay By</td>
                </tr>
                <tr>
                  <td><input name="ndc" id="ndc" type="text" style="width:155px;" /></td>
                  <td class="module_label" align="left">NDC</td>
                  <td><input name="type_desc" id="type_desc" type="text" /></td>
                  <td class="module_label">Description</td>
                </tr>
                <tr>
                  <td>
                    <input name="discount" id="discount" type="text" style="width:155px;" />
                  </td>
                  <td class="module_label" align="left">Discount</td>
                  <td><input name="hazardous" id="hazardous" type="checkbox" />
  &nbsp;Hazardous </td>
                  <td><input type="file" name="file" /></td>
                </tr>  
                <tr>
                    <td>
<input id="datepicker" type="text" name="disc_date" value="" style="width:155px;"/></td>
                    <td class="module_label">Dis. Until</td>
                    <td><input name="fee" id="fee" type="text" style="width:155px;" /></td>
                    <td class="module_label">Fee</td>
                </tr>
             </table>
       <br>

         <div class="module_border mt5">
          <input type="hidden" name="totRows" id="totRows" value="1" />
          <!--this is dummy field to keep working previous js functionality-->
          <input type="hidden" name="qty_on_hand_td" id="qty_on_hand_td" value="0" />
          <div  style="height:280px; overflow-x:hidden; overflow-y:scroll">
           <table class="table_collapse table_cell_padd5 countrow" style="width:100%">
              <tr>
                  <td width="9%" class="module_label">UPC</td>
                  <td style="width:8%" class="module_label">Prac Code</td>
                  <td width="9%" class="module_label">DX Codes</td>
                  <td width="6%" class="module_label">Dosage</td>
                  <td width="5%" class="module_label">Units</td>
				  <td width="9%" class="module_label">Exp. Date</td>
                  <td width="8%" class="module_label">Threshold</td>
                  <td width="8%" class="module_label">Wh. Price</td>
                  <td width="8%" class="module_label">Retail Price</td>
                  <td width="8%" class="module_label">Pur. Price</td>
                  <td width="14%" class="module_label">Qty.  Hand</td>
                  <td width="9%" class="module_label">Amount</td>
                  <td style="width:15px;" class="module_label">&nbsp;</td>
                </tr>
              <tbody>
                <tr id="tr_b_1">
                  <td><input type="text" name="upc_name_1" id="upc_name_1" style="width:85%;" onChange="javascript:upc(document.getElementById('upc_id_1'),'upc_txt',1);" autocomplete="off"/>
                  <input type="hidden" name="upc_id_1" id="upc_id_1" value="">
                  <input type="hidden" name="edit_item_id_1" id="edit_item_id_1" value="" />
                  </td>
                  <td><input type="text" name="item_prac_code_1" id="item_prac_code_1"  value="" style="width:85%;" onChange="show_price_from_praccode(this,'retail_price_1');"/ autocomplete="off"></td>
                  <td class="module_label"><input type="text" name="dx_code_1" id="dx_code_1" style="width:85%;" class="rx_cls" value="" onChange="get_dxcode(this);" autocomplete="off"></td>
                  <td class="module_label"><input name="dosage_1" id="dosage_1" type="text" style="width:80%;" /></td>
                <td><input name="units_1" id="units_1" type="text" style="width:75%;" autocomplete="off" /></td>
				<td><input name="expiry_date_1" id="expiry_date_1" type="text" style="width:85%; height: 23px; background-size: 17px 24px;" autocomplete="off" class="expiryDate date-pick" /></td>
                <td><input name="threshold_1" id="threshold_1" type="text" style="width:85%;" autocomplete="off" /></td>
                <td><span class="module_label">
                  <input class="currency" name="wholesale_cost_1" id="wholesale_cost_1" type="text" style="width:80%;" onChange="parse_float(this);"  autocomplete="off"/>
                </span></td>
                <td><span class="module_label">
                <input class="currency" name="retail_price_1" id="retail_price_1" type="text" style="width:80%;" onBlur="price_total_fun(1);" autocomplete="off" />
                  </span></td>
                
                <td><span class="module_label">
                  <input class="currency" name="purchase_price_1" id="purchase_price_1" type="text" style="width:80%;" onChange="parse_float(this);"  autocomplete="off"/>
                </span></td>
                <td>
                <div style="float:left; background:#CCC; margin:2px 0px 0px 0px; padding:1px">
                 
                 <input type="text" id="qty_on_hand_1" name="qty_on_hand_1"  style="width:85px; float:left; border:none" value="0" readonly><a onClick="add_qty_fun('yes',1);" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer" title="Add Amt." alt="Add Amt.">+</a>
                 
                 <a onClick="add_qty_fun('no',1);" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer; margin-left:5px" title="Min Amt." alt="Min Amt.">-</a></div>
                </td>
                <td><input class="currency" name="amount_1" id="amount_1" type="text" style="width:61px;" readonly /> </td>
                <td align="right"> <img style="cursor:pointer;" id="addbtn_1" onClick="addrow();" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" />
                   <img style="cursor:pointer; display:none" id="removebtn_1" onClick="removerow(1);" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/removerow.png" title="Remove Row" alt="Remove Row" /></td>
                </tr>
              </tbody>
            </table>
       	   </div>
         </div>              
      <div class="btn_cls mt10">
      		<input type="hidden" name="hed_tex" value="0">
            <div style="display:none">
                <input type="submit" name="save" value="Save" id="saveBtn" onClick="return selectProName(document.getElementById('upc_name_1'), document.getElementById('upc_id_1'));"/>
                <input type="submit" name="del" id="delBtn" value="Delete" />                                    
            </div>
            
     </div> 
     </form>
</div>
<script type="text/javascript">
	//var obj6 = new actb(document.getElementById('name'),custom_array_name,"","",document.getElementById('upc_id'),custom_array_upc_id);
	var obj6 = new actb(document.getElementById('name'),custom_array_name);
	var obj7 = new actb(document.getElementById('item_prac_code_1'),customarrayProcedure);
	//commenting editing functnality
	var obj8 = new actb(document.getElementById('upc_name_1'),custom_array_upc,"","",document.getElementById('upc_id_1'),custom_array_upc_id);
	var obj9 = new actb(document.getElementById('dx_code_1'),customarrayDX);
	
	
</script>


<script type="text/javascript">
function submitFrom(){
	$('#saveBtn').click();
}
function newForm(){
	window.location.href= WEB_PATH+'/interface/admin/medicines/index.php';
}
function closeWindow(mode){
	window.location.href= WEB_PATH+'/interface/admin/medicines/index.php';
}
$(document).ready(function() {	

validateForm = function(){

	check = document.material_form;
	if(check.name.value.replace(/\s/g, "") == "" && check.upc_name_1.value.replace(/\s/g, "") == ""){
		top.falert("Please Enter Upc Code or Item Name");
		check.upc_name_1.value="";		
		check.upc_name_1.focus();
		return false;
	}

}
	$("#upc_name_1").keypress(
	function (evt){
	if(evt.keyCode==13){
	$("#item_prac_code_1").focus();
	return false;
	}
});	

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	mainBtnArr[1] = new Array("frame","New","top.main_iframe.admin_iframe.newForm()");
	mainBtnArr[2] = new Array("frame","Make Copy","top.main_iframe.admin_iframe.copy_item_new()");
	mainBtnArr[3] = new Array("frame","Cancel","top.main_iframe.admin_iframe.closeWindow()");
	mainBtnArr[4] = new Array("frame","Delete","top.main_iframe.admin_iframe.delete_item()");
	top.btn_show("admin",mainBtnArr);	
});




var rowData_c='';
var tr  = '';
	
function addrow()	
{
	
	var getRows = $("#totRows").val();
	y = parseInt(getRows)+1;
	
	rowData_c+='<tr id="tr_b_'+y+'">';
	
	rowData_c+='<td><input type="text" name="upc_name_'+y+'" id="upc_name_'+y+'" style="width:85%;" onChange="javascript:upc(document.getElementById(\'upc_id_'+y+'\'),\'upc_txt\','+y+');" autocomplete="off"/>';
    rowData_c+='<input type="hidden" name="upc_id_'+y+'" id="upc_id_'+y+'" value=""><input type="hidden" name="edit_item_id_'+y+'" id="edit_item_id_'+y+'" value="" /></td>';
    rowData_c+='<td><input type="text" name="item_prac_code_'+y+'" id="item_prac_code_'+y+'"  value="" style="width:85%;" onBlur="show_price_from_praccode(this,\'retail_price_'+y+'\');"/ autocomplete="off"></td>';
    rowData_c+='<td class="module_label"><input type="text" name="dx_code_'+y+'" id="dx_code_'+y+'" style="width:85%;" class="rx_cls" value="" onChange="get_dxcode(this);" autocomplete="off"></td>';
    rowData_c+='<td class="module_label"><input name="dosage_'+y+'" id="dosage_'+y+'" type="text" style="width:80%;" /></td>';
    rowData_c+='<td><input name="units_'+y+'" id="units_'+y+'" type="text" style="width:75%;" autocomplete="off" /></td>';
	rowData_c+='<td><input name="expiry_date_'+y+'" id="expiry_date_'+y+'" type="text" style="width:85%;" autocomplete="off" class="expiryDate" /></td>';
	rowData_c+='<td><input name="threshold_'+y+'" id="threshold_'+y+'" type="text" style="width:85%;" autocomplete="off" /></td>';
    rowData_c+='<td class="module_label">';
    rowData_c+='<input name="retail_price_'+y+'" id="retail_price_'+y+'" type="text" style="width:80%;" onChange="price_total_fun('+y+');" autocomplete="off" class="currency" /></td>';
    rowData_c+='<td class="module_label"><input name="purchase_price_'+y+'" id="purchase_price_'+y+'" type="text" style="width:80%;" onChange="parse_float(this);" autocomplete="off" class="currency" /></td>';
    rowData_c+='<td class="module_label"><input name="wholesale_cost_'+y+'" id="wholesale_cost_'+y+'" type="text" style="width:80%;" onChange="parse_float(this);" autocomplete="off" class="currency" /></td>';
    rowData_c+='<td><div style="float:left; background:#CCC; margin:2px 0px 0px 0px; padding:1px">';
    rowData_c+='<input type="text" id="qty_on_hand_'+y+'" name="qty_on_hand_'+y+'" style="width:85px; float:left; border:none" value="0" readonly><a onClick="add_qty_fun(\'yes\','+y+');" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer" title="Add Amt." alt="Add Amt.">+</a>';
    rowData_c+='<a onClick="add_qty_fun(\'no\','+y+');" style="float:left; font-weight:bold; color:#FFF; text-decoration:none; padding:0 5px; cursor:pointer; margin-left:5px" title="Min Amt." alt="Min Amt.">-</a></div></td>';
    rowData_c+='<td><input name="amount_'+y+'" id="amount_'+y+'" type="text" style="width:61px;" readonly class="currency" /> </td>';
    rowData_c+='<td align="right"> <img style="cursor:pointer;" id="addbtn_'+y+'" onClick="addrow();" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" title="Add New Row" alt="Add New Row" /> ';
    rowData_c+='<img style="cursor:pointer; display:none" id="removebtn_'+y+'" onClick="removerow('+y+');" src="<?php echo $GLOBALS['WEB_PATH'];?>/images/removerow.png" title="Remove Row" alt="Remove Row" /></td>';
	
	rowData_c+='</tr>';
	
	$("#tr_b_"+getRows).after(rowData_c); // ADD NEW ROW
	currencySymbols();
	
	rowData_c='';
	
	var obj10 = new actb(document.getElementById('item_prac_code_'+y),customarrayProcedure);
	//commenting editing functinality
	var obj11 = new actb(document.getElementById('upc_name_'+y),custom_array_upc,"","",document.getElementById('upc_id_'+y),custom_array_upc_id);
	var obj12 = new actb(document.getElementById('dx_code_'+y),customarrayDX);
	
	//if(getRows>=1)
	//{
		//$("#removebtn_"+y).show();
	//}
	$("#totRows").val(y);
	
	for(i=0;i<y;i++)
	{
		$("#removebtn_"+i).show();
		$("#addbtn_"+i).hide();
	}
	
	var totalRows = $(".countrow tr").size();
	
	$("#removebtn_"+y).hide();
	$("#addbtn_"+y).show();
	
	bindExpiryDate();
}

var remove_row='';
function removerow(id)
{
	//remove_row = $(".countrow tr").size();
	
	//if(remove_row>1)
//	{
//		if(remove_row==2)
//		{
//			$("#removebtn").hide();
//		}
//		
//		$("#tr_b_"+remove_row).remove();
//	}
	//$("#totRows").val(remove_row-1);
	$("#tr_b_"+id).remove();
	
	
}

</script>

</body>
</html>