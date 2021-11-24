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
	$("#facility").multiSelect({noneSelected:'Select All'});
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
	var curFrmObj = document.stockForm;
	var postFrmObj = window.frames["frame_result"].medicationFormResult;
	
	postFrmObj.lot_number.value = $("#lot_number").val();
	postFrmObj.upc_code.value = $("#upc_code").val();
	postFrmObj.facility.value = $("#facility").selectedValuesString();
	postFrmObj.order_status.value = $("#order_status").val();
	postFrmObj.date_from.value = $("#date_from").val();
	postFrmObj.date_to.value = $("#date_to").val();

	if(curFrmObj.dayReport.value == 'Daily'){
		curFrmObj.date_from.value = '<?php echo $curDate;?>';
		curFrmObj.date_to.value = '<?php echo $curDate;?>';
	}
	else if(curFrmObj.dayReport.value == 'Weekly'){	
		curFrmObj.date_from.value = '<?php echo $arrSearchDates['WEEK_DATE'];?>';
		curFrmObj.date_to.value = '<?php echo $curDate;?>';
	}
	else if(curFrmObj.dayReport.value == 'Monthly'){
		curFrmObj.date_from.value = '<?php echo $arrSearchDates['MONTH_DATE'];?>';
		curFrmObj.date_to.value = '<?php echo $curDate;?>';
	}
	else if(curFrmObj.dayReport.value == 'Quarterly'){
		curFrmObj.date_from.value = '<?php echo $arrSearchDates['QUARTER_DATE_START'];?>';
		curFrmObj.date_to.value = '<?php echo $arrSearchDates['QUARTER_DATE_END'];?>';
	}
	postFrmObj.date_from.value = curFrmObj.date_from.value;
	postFrmObj.date_to.value = curFrmObj.date_to.value;	
	
/*	if ($("#show_report_sum").attr("checked") == true) {
		postFrmObj.show_report.value = "summary";
	}
	else if ($("#show_report_det").attr("checked") == true) {
		postFrmObj.show_report.value = "detail";
	}
*/
	postFrmObj.submit();
}

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

</script>

<script>
function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/medication_orders_result.php?print=true';
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
           <div class="listheading border_top_left border_top_left">Report - Medication Orders</div>
           <form name="stockForm" id="stockForm" method="post" action="" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr>
					<td style="width:190px">
                      <select name="facility" id="facility" style="width:170px;">
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
                      <div class="label">Facility(From order generated)</div>
					</td>
                <td style="width:190px">
                  <input type="text" name="lot_number" id="lot_number" style="width:163px">
                  <div class="label">Lot# (Comma Sep.)</div>
                </td>
                <td style="width:190px">
                  <input type="text" name="upc_code" id="upc_code" style="width:163px">
                  <div class="label">UPC Code (Comma Sep.)</div>
                </td>
                <td style="width:190px">
                  <select name="order_status" id="order_status" style="width:150px">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="ordered">Ordered</option>
                    <option value="received">Received</option>
					<option value="notified">Notified</option>
                    <option value="dispensed">Dispensed</option>
                  </select>
                  <div class="label">Status</div>
                </td>
				<td align="left">
                   	<div id="dateFieldControler">
                        <select class="input_text_10" name="dayReport" id="dayReport" onChange="DateOptions(this.value);" style="width:200px;">
                            <option value="Daily" <?php if($dayReport=='Daily') echo 'selected';?> >Daily</option>
                            <option value="Weekly" <?php if($dayReport=='Weekly') echo 'selected';?>>Weekly</option>
                            <option value="Monthly" <?php if($dayReport=='Monthly') echo 'selected';?>>Monthly</option>
                            <option value="Quarterly" <?php if($dayReport=='Quarterly') echo 'selected';?>>Quarterly</option>
                            <option value="Date" <?php if($dayReport=='Date') echo 'selected';?>>Date Range</option>
                        </select>
                        <div class="label">Date (On Order Created)</div>
                    </div>
                    <table cellpadding="0" cellspacing="0" border="0" id="dateFields" class="hide">
                        <tr>
                            <td width="auto" nowrap="nowrap">
                                <input type="text"  class="date-pick" name="date_from" id="date_from" value="<?php echo date('m-d-Y');?>" style="width:70px" />
                                <div class="label">From</div>
                            </td>
                            <td width="110" nowrap="nowrap">
                                <img src="../../images/icon_back.png" align="right" border="0" style="cursor:pointer; margin-top:5px;" onClick="DateOptions('x');" />
                                &nbsp;<input type="text" class="date-pick" name="date_to" id="date_to" value="<?php echo date('m-d-Y');?>" style="width:70px"/>
                                <div class="label">To</div>
                            </td>
                        </tr>
                     </table>
				</td>
				<td>
<!--		            <input type="radio" name="show_report" id="show_report_sum" value="summary" checked="checked">Summary<br>
                    <input type="radio" name="show_report" id="show_report_det" value="detail">Detail
-->                </td>                
				</tr>
            </table>
       </form>
        </div>
        <iframe src="medication_orders_result.php" name="frame_result" id="frame_result" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-440;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
</body>
</html>