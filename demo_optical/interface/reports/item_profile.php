<?php
/*
File: item_profile.php
Coded in PHP7
Purpose: Item Profile Report Searching Criteria
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

$arrSearchDates = changeDateSelection();

$curDate=date('m-d-Y');

$frameId='';
$typeRs = imw_query("select * from in_module_type where ORDER BY module_type_name asc");
while($typeRes=imw_fetch_array($typeRs)){
 if(strtolower($typeRes['module_type_name'])=='frame'){ $frameId=$typeRes['id']; }
 $typeOptions.='<option value="'.$typeRes['id'].'">'.$typeRes['module_type_name'].'</option>'; 
}


?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<?php include 'report_includes.php';?>

<script type="text/javascript">
var frameId='<?php echo $frameId;?>';
$(document).ready(function(){
	$("#manufac").multiSelect({noneSelected:'Select All'});
	$("#vendor").multiSelect({noneSelected:'Select All'});
	$("#measure").multiSelect({noneSelected:'Select All'});
	$("#faclity").multiSelect({noneSelected:'Select All'});
});

jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true, dateFormat: 'mm-dd-yy'});
});

function submitForm(){
	top.main_iframe.loading('block');
	var curFrmObj = document.stockForm;
	var postFrmObj = window.frames["frame_stock"].stockFormResult;
	postFrmObj.manufac.value = $('#manufac').selectedValuesString();
	postFrmObj.product_type.value = $('#product_type').val();
	postFrmObj.vendor.value = $('#vendor').selectedValuesString();
	postFrmObj.facility.value = $('#faclity').selectedValuesString();
	if($('#product_type').val()==1)
	{
		postFrmObj.brand.value = $('#fbrand').selectedValuesString();
	}
	if($('#product_type').val()==2)
	{
		postFrmObj.material.value = $('#materal').selectedValuesString();
	}
	if($('#product_type').val()==3)
	{
		postFrmObj.brand.value = $('#cbrand').selectedValuesString();
	}
	if($('#product_type').val()==5 || $('#product_type').val()==6 || $('#product_type').val()==7)
	{
		postFrmObj.measurement.value = $('#measure').selectedValuesString();
	}
	postFrmObj.upc_code.value = $('#upc_code').val();
	postFrmObj.item_name.value = $('#item_name').val();
	
	postFrmObj.date_from.value = curFrmObj.date_from.value;
	postFrmObj.date_to.value = curFrmObj.date_to.value;	
	
	postFrmObj.submit();
}
</script>

<script>
function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/item_profile_result.php?print=true';
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('report_popup',url, "report_printing","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=1");
	} catch(e) {
		location.target = "_blank";
		location.href = url;
	}
}

function change_brand(type_val)
{
	$("#cbrand").hide();
	$("#material").hide();
	$("#measurement").hide();
	$("#brands").hide();
	$("#fbrand").hide();
	if(type_val==1)
	{
		$("#brands").show();
		$("#fbrand").show();
		if($("#fbrand").attr("tagName").toLowerCase() == "select")
		{
			$("#fbrand").multiSelect({noneSelected:'Select All'});
		}
	}
	if(type_val==2)
	{
		$("#material").show();
		if($("#materal").attr("tagName").toLowerCase() == "select")
		{
			$("#materal").multiSelect({noneSelected:'Select All'});
		}		
	}
	if(type_val==3)
	{
		$("#brands").show();
		$("#cbrand").show();
		if($("#cbrand").attr("tagName").toLowerCase() == "select")
		{
			$("#cbrand").multiSelect({noneSelected:'Select All'});
		}
	}
	if(type_val==5 || type_val==6 || type_val==7)
	{
		$("#measurement").show();
		//$("#measure").multiSelect({noneSelected:'Select All'});
	}
}

jQ(document).unbind('keydown').bind('keydown', function (event) {
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
    <div class="mt2" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
    
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto; height:125px;">
           <div class="listheading border_top_left border_top_left">Report - Item Profile</div>
           <form name="stockForm" id="stockForm" method="post" action="" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr>
					<td>
                      <select name="product_type" id="product_type" style="width:190px" onChange="change_brand(this.value);">
                        <?php 
						  $typeRs = imw_query("select * from in_module_type where del_status='0' order by module_type_name asc");
						  while($typeRes=imw_fetch_array($typeRs)){
							 $typeOptions.='<option value="'.$typeRes['id'].'">'.ucfirst($typeRes['module_type_name']).'</option>';
						  }
						  echo $typeOptions;?>
                      </select>
                      <div class="label">Product Type</div>
                  </td>
                  <td>
                      <select name="manufac" id="manufac" style="width:170px">
                        <option value="">- Select -</option>
						<?php 
						$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where del_status='0' order by manufacturer_name asc";
						$manu_detail_rs = imw_query($manu_detail_qry);
						while($manu_detail_res=imw_fetch_array($manu_detail_rs)){
							$manuOptions.='<option value="'.$manu_detail_res['id'].'">'.$manu_detail_res['manufacturer_name'].'</option>';
						}
						echo $manuOptions;
						?>
                      </select>
                      <div class="label">Manufacturer</div>
                  </td>
                  
          		  <td>
                      <select name="vendor" id="vendor" style="width:160px">
                        <option value="">- Select -</option>
						<?php  
						  $vendorRs = imw_query("select * from in_vendor_details where del_status='0' order by vendor_name asc");
						  while($vendorRes=imw_fetch_array($vendorRs)){
							$vendorOptions.='<option value="'.$vendorRes['id'].'">'.$vendorRes['vendor_name'].'</option>';
                          }
						  echo $vendorOptions;	?>
                      </select>
                      <div class="label">Vendor</div>
                  </td>
                  <td align="left" id="brands" style="display:none;">
                      <select name="brand" id="fbrand" style="width:160px">
                        <option value="">- Select -</option>
						<?php  
						  $brandRs = imw_query("select * from in_frame_sources where del_status='0' order by frame_source asc");
						  while($brandRes=imw_fetch_array($brandRs)){
							$brandOptions.='<option value="'.$brandRes['id'].'" >'.$brandRes['frame_source'].'</option>';
                          }
						  echo $brandOptions; ?>
                      </select>
					  
					  <select name="brand" id="cbrand" style="width:160px; display:none;">
                        <option value="">- Select -</option>
						<?php  
						  $cbrandRs = imw_query("select * from in_contact_brand where del_status='0' order by brand_name asc");
						  while($cbrandRes=imw_fetch_array($cbrandRs)){
							$cbrandOptions.='<option value="'.$cbrandRes['id'].'" >'.$cbrandRes['brand_name'].'</option>';
                          }
						  echo $cbrandOptions; ?>
                      </select>
                      <div class="label">Brand</div>
                  </td>
				  <td id="material" style="display:none;">
                      <select name="materal" multiple="multiple" id="materal" style="width:160px">
                        <option value="">- Select -</option>
						<?php  
						  $materialRs = imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
						  while($materialRes=imw_fetch_array($materialRs)){
							$materialOptions.='<option value="'.$materialRes['id'].'">'.$materialRes['material_name'].'</option>';
                          }
						  echo $materialOptions;	?>
                      </select>
                      <div class="label">Material</div>
                  </td>
				  <td id="measurement">
                      <select name="measure" id="measure" style="width:160px">
                        <option value="">- Select -</option>
						<?php  
						  $measureRs = imw_query("select * from in_supplies_measurment where del_status='0' order by measurment_name asc");
						  while($measureRes=imw_fetch_array($measureRs)){
							$measureOptions.='<option value="'.$measureRes['id'].'">'.$measureRes['measurment_name'].'</option>';
                          }
						  echo $measureOptions;	?>
                      </select>
                      <div class="label">Measurement</div>
                  </td>
                
					<td>
				  	<select name="faclity" id="faclity" style="width:170px;">
						<option value="">Select Facility</option>
						<?php $fac_name_qry = imw_query("select id, loc_name from in_location where del_status='0' and loc_name!='' order by loc_name asc");
							  while($fac_name_row = imw_fetch_array($fac_name_qry)) {
								  $sel="";
								  if($fac_name_row['id']==$_SESSION['pro_fac_id'])
								  {
									$sel="selected";
								  } 
							  ?>
						<option value="<?php echo $fac_name_row['id']; ?>" <?php echo $sel; ?>><?php echo $fac_name_row['loc_name']; ?></option>
						<?php } ?>
					</select>
                    <div class="label">Facility</div>
                  </td> 
				</tr>
                <tr>
				  <td align="left">
				  	<input type="text" name="upc_code" id="upc_code" value="" style="width:180px;">
					<div class="label">Upc Code</div>
				  </td>
				  <td align="left">
				  	<input type="text" name="item_name" id="item_name" value="" style="width:182px;">
					<div class="label">Item Name</div>
				  </td>
                  <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="auto" nowrap="nowrap">
                                <input type="text"  class="date-pick" name="date_from" id="date_from" value="" style="width:70px" />
                                <div class="label">Ordered From</div>
                            </td>
                            <td width="110" nowrap="nowrap">
                                &nbsp;&nbsp;<input type="text" class="date-pick" name="date_to" id="date_to" value=""  style="width:70px"/>
                                <div class="label">To</div>
                            </td>
                        </tr>
                     </table>
                  </td>
                  <td style="text-align:right" colspan="2">
				  </td>
                </tr>
            </table>
       </form>
        </div>
        <iframe src="item_profile_result.php" name="frame_stock" id="frame_stock" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-485;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
</body>
</html>