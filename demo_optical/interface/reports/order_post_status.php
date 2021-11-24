<?php
/*
File: day_order_report_result.php
Coded in PHP7
Purpose: Day Order Report Searching Criteria
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
	$("#operators").multiSelect({noneSelected:'Select All'});
	$("#faclity").multiSelect({noneSelected:'Select All'});
	$("#product_type").multiSelect({noneSelected:'Select All'});
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
	var curFrmObj = document.searchForm;
	var postFrmObj = window.frames["frame_result"].searchFormResult;
	
	postFrmObj.operators.value = $('#operators').selectedValuesString();
	postFrmObj.faclity.value = $('#faclity').selectedValuesString();
	postFrmObj.product_type.value = $('#product_type').selectedValuesString();
	postFrmObj.order_status.value = curFrmObj.order_status.value;
	
	if ($("#show_report_sum").attr("checked") == true) {
		postFrmObj.show_report.value = "summary";
	}
	else if ($("#show_report_det").attr("checked") == true) {
		postFrmObj.show_report.value = "detail";
	}
	
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

	postFrmObj.submit();
}
</script>

<script>
function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/order_post_status_result.php?print=true';
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup',url, "day_order_report","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=1");
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
<style>
.ui-datepicker-month {width:70px !important;}
.ui-datepicker-year {width:80px !important;}
</style>
</head>
<body> 
    <div class="mt2" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
    
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto; height:75px;">
           <div class="listheading border_top_left border_top_left">Order Post Status Report</div>
           <form name="searchForm" id="searchForm" method="post" action="" style="margin:0px;">
            <table class="table_collapse m2 rptDropDown">
                <tr style="height:43px">
                  <td>
                      <select name="operators" id="operators" style="width:170px">
                        <option value="">- Select -</option>
						<?php 
						  $userOptions=''; 
						  $userRs = imw_query("select id,fname,lname from users where delete_status='0' order by lname,fname");
						  while($userRes=imw_fetch_array($userRs)){
							$userName='';
							if($userRes['fname']!='' || $userRes['lname']!=''){
								$userName=$userRes['lname'].', '.$userRes['fname'];
							}
							$userOptions.='<option value="'.$userRes['id'].'" >'.$userName.'</option>';
                          }
						  echo $userOptions;?>
                      </select>
                      <div class="label">Operator</div>
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
				  <td>
                      <select name="product_type" id="product_type" style="width:150px">
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
                      <select name="order_status" id="order_status" style="width:150px">
                        <option value="">All</option>
                        <option value="posted">Posted</option>
                        <option value="notposted">Not Posted</option>
                      </select>
                      <div class="label">Status</div>
                  </td>                  
				  <td>
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
		            <input type="radio" name="show_report" id="show_report_sum" value="summary" checked="checked">Summary<br>
                    <input type="radio" name="show_report" id="show_report_det" value="detail">Detail
                  </td>
			  
                  <td style="text-align:right">
                  </td>
                </tr>
            </table>
       </form>
        </div>
        <iframe src="order_post_status_result.php" name="frame_result" id="frame_result" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-440;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
</body>
</html>