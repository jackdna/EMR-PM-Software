<?php
/*
File: stock_in_hand.php
Coded in PHP7
Purpose: Stock Report Searching Criteria
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
<?php include 'report_includes.php'; ?>

<script type="text/javascript">
var frameId='<?php echo $frameId;?>';

$(document).ready(function(){
	
	$("#manufac").multiSelect({noneSelected:'Select All'});
	$("#product_type").multiSelect({noneSelected:'Select All'});
	$("#vendor").multiSelect({noneSelected:'Select All'});
	$("#brand").multiSelect({noneSelected:'Select All'});
	$("#faclity").multiSelect({noneSelected:'Select All'});
	
});


function submitForm(){
	top.main_iframe.loading('block');
	
	var curFrmObj = document.stockForm;
	var postFrmObj = window.frames["frame_stock"].stockFormResult;	
	
	postFrmObj.upc_code.value = $('#upc_code').val();
	postFrmObj.manufac.value = $('#manufac').selectedValuesString();
	postFrmObj.product_type.value = $('#product_type').selectedValuesString();
	postFrmObj.vendor.value = $('#vendor').selectedValuesString();
	postFrmObj.brand.value = $('#brand').selectedValuesString();
	postFrmObj.facility.value = $('#faclity').selectedValuesString();
	postFrmObj.groupBy.value = curFrmObj.groupBy.value;
	postFrmObj.submit();
}

</script>


<script>
function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/stock_in_hand_result.php?print=true';
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('report_popup',url, "report_printing","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=1");
	} catch(e) {
		location.target = "_blank";
		location.href = url;
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
    
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto; height:75px;">
           <div class="listheading border_top_left border_top_left">Report - Valuation Report</div>
           <form name="stockForm" id="stockForm" action="" method="post" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr>
                  <td >
                      <select name="manufac" id="manufac" style="width:135px">
                        <option value="">- Select -</option>
						<?php 
						$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where del_status='0' ORDER BY manufacturer_name";
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
                      <select name="product_type" id="product_type" style="width:135px">
                        <option value="">- Select -</option>
                        <?php 
						  $typeRs = imw_query("select * from in_module_type order by module_type_name asc");
						  while($typeRes=imw_fetch_array($typeRs)){
							 $typeOptions.='<option value="'.$typeRes['id'].'">'.ucfirst($typeRes['module_type_name']).'</option>'; 
						  }
						  echo $typeOptions;?>
                      </select>
                      <div class="label">Product Type</div>
                  </td>
          		  <td>
                      <select name="vendor" id="vendor" style="width:135px">
                        <option value="">- Select -</option>
						<?php  
						  $vendorRs = imw_query("select * from in_vendor_details where del_status='0'  order by vendor_name asc");
						  while($vendorRes=imw_fetch_array($vendorRs)){
							$vendorOptions.='<option value="'.$vendorRes['id'].'">'.$vendorRes['vendor_name'].'</option>';
                          }
						  echo $vendorOptions;	?>
                      </select>
                      <div class="label">Vendor</div>
                  </td>
                  <td align="left">
                  	<div id="brandDiv"></div>
                      <select name="brand" id="brand" style="width:135px">
                        <option value="">- Select -</option>
						<?php  
						  $brandRs = imw_query("select * from in_frame_sources where del_status='0' order by frame_source asc");
						  while($brandRes=imw_fetch_array($brandRs)){
							$brandOptions.='<option value="'.$brandRes['id'].'" >'.$brandRes['frame_source'].'</option>';
                          }
						  echo $brandOptions;	?>
                      </select>
                      <div class="label">Brand</div>
                  </td>
				  <td>
				  	<select name="faclity" id="faclity" style="width:135px;">
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
                
                  <td>
                      <input type="text" name="upc_code" id="upc_code" style="width:145px">
                      <div class="label">UPC Code (Comma Sep.)</div>
                  </td>
                  <td>
                      <select name="groupBy" id="groupBy" style="width:135px">
                        <option value="manufac">Manufacturer</option>
                        <option value="type">Product Type</option>
                      </select>
                      <div class="label">Group By</div>
                  </td>
                  </tr>
            </table>
       </form>

        </div>
        
        <iframe src="stock_in_hand_result.php" name="frame_stock" id="frame_stock" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-440;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
        
        
    </div>
</body>
</html>