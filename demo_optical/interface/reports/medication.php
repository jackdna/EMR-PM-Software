<?php
/*
File: medication.php
Coded in PHP7
Purpose: Medication Report Searching Criteria
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

$arrSearchDates = changeDateSelection();

$curDate=date('m-d-Y');

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<?php include 'report_includes.php';?>

<script type="text/javascript">
$(document).ready(function(){
	$("#manufacturer").multiSelect({noneSelected:'Select All'});
	$("#vendor").multiSelect({noneSelected:'Select All'});
	/*$("#med_type").multiSelect({noneSelected:'Select All'});*/
});

jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({
		changeMonth: true,changeYear: true,
		dateFormat: 'mm-dd-yy',
		onSelect: function() {
		$(this).change();
		}
	});
});


function submitForm(){
	top.main_iframe.loading('block');
	var postFrmObj = window.frames["frame_result"].medicationFormResult;
	postFrmObj.manufacturer.value = $("#manufacturer").selectedValuesString();
	postFrmObj.vendor.value = $("#vendor").selectedValuesString();
	postFrmObj.lot_number.value = $("#lot_number").val();
	postFrmObj.upc_code.value = $("#upc_code").val();
	/*postFrmObj.med_type.value = $("#med_type").selectedValuesString();*/
	postFrmObj.date_from.value = $("#date_from").val();
	postFrmObj.date_to.value = $("#date_to").val();

	if ($("#show_report_sum").attr("checked") == true) {
		postFrmObj.show_report.value = "summary";
	}
	else if ($("#show_report_det").attr("checked") == true) {
		postFrmObj.show_report.value = "detail";
	}
	
	postFrmObj.submit();
}
</script>

<script>
function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/medication_result.php?print=true';
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
    
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto;">
           <div class="listheading border_top_left border_top_left">Report - Medication</div>
           <form name="stockForm" id="stockForm" method="post" action="" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr>
					<td style="width:190px">
						<select style="width:163px;" name="manufacturer" id="manufacturer" onChange="get_vendor_manufacturer(this.value,'0');">
							<option value="">Please Select</option>
							<?php $rows="";
										$rows = data("select * from in_manufacturer_details where medicine_chk='1' and del_status='0' order by manufacturer_name asc");
										foreach($rows as $r)
										{ ?>
							<option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['manufacturer_name']); ?></option>
							<?php }	?>
						</select>
						<div class="label">Manufacturer</div>
					</td>
                <td style="width:190px">
					<select style="width:163px;" name="vendor" id="vendor">
						<option value="">Please Select</option>
						<?php 
							$sql="select id,vendor_name from in_vendor_details where del_status = '0' ORDER BY vendor_name";
							$res = imw_query($sql);
							while($row = imw_fetch_array($res))
							{
						?>
							<option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['vendor_name']); ?></option>
						<?php 
							} ?>
					</select>
					<div class="label">Vendor</div>
                </td>
                <td style="width:190px">
                  <input type="text" name="lot_number" id="lot_number" style="width:163px">
                  <div class="label">Lot# (Comma Sep.)</div>
                </td>
                <td style="width:190px">
                  <input type="text" name="upc_code" id="upc_code" style="width:163px">
                  <div class="label">UPC Code (Comma Sep.)</div>
                </td>
                  
          		<!--<td>
                    <select style="width:163px;" name="med_type" id="med_type">
						<option value="">Please Select</option>
						<?php 
							/*$sql="select `id`, `type_name` from `in_medicines_types` where del_status='0' order by type_name asc";
							$res = imw_query($sql);
							while($row = imw_fetch_array($res))
							{
						?>
							<option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['type_name']); ?></option>
						<?php 
							}*/ ?>
					</select>
					<div class="label">Type of Medicines</div>
                </td>-->
				<td align="left">
					<table cellpadding="0" cellspacing="0" border="0" id="dateFields" class="show">
						<tr>
							
							<td width="110" nowrap="nowrap">
                                <input type="text"  class="date-pick" name="date_from" id="date_from" style="height: 21px; background-size:17px 21px; width:80px" />
                                <div class="label">Expiry From</div>
                            </td>
                            <td width="120" nowrap="nowrap" style="padding-left: 10px;">
                                <input type="text" size="9" class="date-pick" name="date_to" id="date_to" style="height: 21px; background-size:17px 21px; width:80px" />
                                <div class="label">To</div>
                            </td>
							<!--<td width="240" nowrap="nowrap">
								<div name="dateHolder">
									<input type="text"  class="date-pick" size="9" name="date_from" id="date_from" placeholder="From" style="display: inline;" />
									<input type="text" size="9" class="date-pick" name="date_to" id="date_to" placeholder="To" style="display: inline; margin-left:10px;" />
								</div>
								<div style="text-align: center; padding-top: 2px;" class="label">Expiry Date</div>
							</td>-->
						</tr>
					</table>
				</td>
				<td>
		            <input type="radio" name="show_report" id="show_report_sum" value="summary" checked="checked">Summary<br>
                    <input type="radio" name="show_report" id="show_report_det" value="detail">Detail
                </td>                
				</tr>
            </table>
       </form>
        </div>
        <iframe src="medication_result.php" name="frame_result" id="frame_result" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-440;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
</body>
</html>