<?php
/*
File: stock_ledger.php
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
<?php include 'report_includes.php';?>

<script type="text/javascript">
var frameId='<?php echo $frameId;?>';
$(document).ready(function(){
	$("#manufac").multiSelect({noneSelected:'Select All'});
	$("#product_type").multiSelect({noneSelected:'Select All'});
	$("#vendor").multiSelect({noneSelected:'Select All'});
	$("#brand").multiSelect({noneSelected:'Select All'});
	$("#operators").multiSelect({noneSelected:'Select All'});
	$("#faclity").multiSelect({noneSelected:'Select All'});
	
/*	$("#product_type").blur(function(){
		var types=$('#product_type').selectedValuesString();
		var arrTypes=types.split(',');
		var frameExist=0;
		for(i=0; i< arrTypes.length; i++){
			if(arrTypes[i]==frameId){
				frameExist=1;	
				break;
			}
		}
		if(frameExist=='1'){
			$('#brandDiv').html('Yes');
		}else{
			$('#brandDiv').html('No');
		}
	});*/
});

jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true, dateFormat: 'mm-dd-yy'});
});

function DateOptions(val){
	switch(val){
		case 'Date':
		{
			$('#dateFieldControler').hide();
			$('#dateFields').show();
			$('#monthly_vals').hide();
			break;
		}
		default:
		{
			$('#dateFields').hide();
			$('#monthly_vals').hide();
			$('#dateFieldControler').show();
			if(val=='x') $("#dayReport option[value='Daily']").attr('selected', 'selected');
			else if(val=='m') $("#dayReport option[value='Daily']").attr('selected', 'selected');
			break;
		}
	}
}

function submitForm(){
	top.main_iframe.loading('block');
	document.getElementById('frame_stock').src='dead_stock_result.php?details';
	top.main_iframe.loading('none');
}
</script>
</head>
<body> 
    <div class="mt2" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
    
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto;">
           <div class="listheading border_top_left border_top_left">Report - Dead Stock</div>
           <form name="stockForm" id="stockForm" method="post" action="" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr>
                  <td>
                      <select name="product_type" id="product_type" style="width:150px">
                        <option value="">- Select -</option>
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
                      <select name="manufac" id="manufac" style="width:190px">
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
                      <select name="vendor" id="vendor" style="width:140px">
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
                  <td align="left">
                  	<div id="brandDiv"></div>
                      <select name="brand" id="brand" style="width:140px">
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
				  	<select name="faclity" id="faclity" style="width:140px;">
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
                    <select name="groupBy" id="groupBy" style="width:165px">
						<option value="manufac">Manufacturer</option>
						<option value="type">Product Type</option>
						<option value="operator">Operator</option>
					</select>
                  <div class="label">Group By</div>
                  </td>
				</tr>
                <tr>
				  <td>
					<input type="text"  class="date-pick" name="date_from" id="date_from" value="<?php echo date('m-d-Y');?>" style="width:148px;height:22px;background-size: 18px 22px;" />
                    <div class="label">From</div>
                  </td>
                  <td>
                                      
                  </td>
                                   
                  <td style="text-align:right" colspan="4">
				  </td>
                </tr>
            </table>
       </form>
        </div>
        <iframe src="dead_stock_result.php" name="frame_stock" id="frame_stock" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-485;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
<script type="text/javascript">
$(document).ready(function(){
	var numr = '<?php echo $mainNumRs; ?>';		

	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Search","top.main_iframe.reports_iframe.submitForm();");
	if(numr>0){
		mainBtnArr[1] = new Array("frame","Print","top.main_iframe.reports_iframe.printreport()");
	}
	top.btn_show("admin",mainBtnArr);
	
	top.main_iframe.loading('none');
});
</script>
</body>
</html>