<?php
/*
File: capture_report_result.php
Coded in PHP7
Purpose: Day Order Report Searching Criteria
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../config/config.php");
require_once(dirname('__FILE__')."/../../library/classes/functions.php");

$arrSearchDates = changeDateSelection();
$curDate=date('m-d-Y');

/*List Groups (iDoc)*/
$groups = array();
$sqlGroup = 'SELECT `gro_id` AS \'id\', `name` AS \'name\' FROM `groups_new` WHERE `del_status`=0 ORDER BY `name` ASC';
$respGroup = imw_query($sqlGroup);
if($respGroup && imw_num_rows($respGroup)>0)
{
	while( $row = imw_fetch_assoc($respGroup) )
		array_push($groups, $row);
}

/*List Facilities (iDoc)*/
$facilities_group = array();
$facilities = array();
$sqlFacilities = 'SELECT `id`, `name`, `default_group` FROM `facility` ORDER BY `name` ASC';
$respFacilities = imw_query($sqlFacilities);
if($respFacilities && imw_num_rows($respFacilities)>0)
{
	while( $row = imw_fetch_assoc($respFacilities) )
	{
		if( !isset($facilities_group[$row['default_group']]) )
			$facilities_group[$row['default_group']] = array();
		
		array_push($facilities_group[$row['default_group']], $row);
		unset($row['default_group']);
		array_push($facilities, $row);
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/ajaxTypeahead.css?<?php echo constant("cache_version"); ?>" />
<?php include 'report_includes.php';?>
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.multiSelect.js?<?php echo constant("cache_version"); ?>"></script>

<script type="text/javascript">
var frameId='<?php echo $frameId;?>';

var groupFacilities = <?php echo json_encode($facilities_group); ?>;
var facilities = <?php echo json_encode($facilities); ?>;

$(document).ready(function(){
	$("#iDocFacilities").multiSelect({noneSelected:'Select All'});
	$("#iDocGroups").multiSelect({noneSelected:'Select All'}, groupChanged);
});

function groupChanged()
{
	var facilityOptions = [];
	
	var selectedValues = $.trim($('#iDocGroups').selectedValuesString());
	
	if(selectedValues===''){
		
		$(facilities).each(function(key,data){
			facilityOptions.push({ text: data.name, value: data.name});
		});
		$('#iDocFacilities').multiSelectOptionsUpdate(facilityOptions);
		return true;
	}
	
	selectedValues = selectedValues.split(',');
	
	$(selectedValues).each(function(i,value){
		value = $.trim(value);
		
		if( typeof(groupFacilities[value]) === 'undefined')
			return true;
		
		$(groupFacilities[value]).each(function(key,data){
			facilityOptions.push({ text: data.name, value: data.id});
		});
	});
	
	$('#iDocFacilities').multiSelectOptionsUpdate(facilityOptions);
}

jQ(document).ready(function(){
	jQ( ".date-pick" ).datepicker({ changeMonth: true,changeYear: true, dateFormat: 'mm-dd-yy'});
	
	/*Typeahead fot Physician*/
	jQ("#physicianName").ajaxTypeahead({
		url: '<?php echo $GLOBALS['WEB_PATH']; ?>/interface/patient_interface/ajax.php',
		type: 'physicianName',
		hidIDelem: document.getElementById('physicianId'),
		showAjaxVals: 'phyName'
	});
	
	jQ('#physicianName').on('blur', function(){
		if($(this).val()=='')
			$('#physicianId').val('');
	});
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
	
	if ($("#show_report_sum").attr("checked") == true) {
		postFrmObj.show_report.value = "summary";
	}
	else if ($("#show_report_det").attr("checked") == true) {
		postFrmObj.show_report.value = "detail";
	}
	
	console.log(curFrmObj.date_from.value);
	postFrmObj.date_from.value = curFrmObj.date_from.value;
	postFrmObj.date_to.value = curFrmObj.date_to.value;
	
	postFrmObj.facility_ids.value = $('#iDocFacilities').selectedValuesString();
	postFrmObj.group_ids.value = $('#iDocGroups').selectedValuesString();
	postFrmObj.provider_id.value = $('#physicianId').val();
	postFrmObj.provider_name.value = $('#physicianName').val();

	postFrmObj.submit();
}

function printreport()
{
	var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=p&file_name=../../library/new_html2pdf/capture_report_result';
//window.location.href = url;

	//var url = '<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/capture_report_result.php?print=true';
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup',url, "capture_report","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=1");
	} catch(e) {
		location.target = "_blank";
		location.href = url;
	}
}
</script>
<style>
.ui-datepicker-month {width:70px !important;}
.ui-datepicker-year {width:80px !important;}
</style>
</head>
<body> 
    <div class="mt2" style="height:<?php echo $_SESSION['wn_height']-360;?>px;">
        <div id="searchPart" class="border_rounded" style="width:99.7%; height:auto;">
           <div class="listheading border_top_left border_top_left">Capture Report</div>
		   
			<form name="searchForm" id="searchForm" method="post" action="" style="margin:0px;">
				<table class="table_collapse m2 rptDropDown">
					<tr style="height:43px">
						<td style="width: 196px;">
							<input type="text" name="physicianName" id="physicianName" value="" style="width: 180px" />
							<input type="hidden" name="physicianId" id="physicianId" value="" />
							<div class="label">Provider Name (iDoc)</div>
						</td>
						<td style="width: 196px;">
							<select class="input_text_10" name="iDocGroups" id="iDocGroups" style="width:180px;">
								<option value="">Select Group</option>
								<?php foreach($groups as $group): ?>
									<option value="<?php echo $group['id'] ?>"><?php echo $group['name'] ?></option>
								<?php endforeach; ?>
							</select>
							<div class="label">Group (iDoc)</div>
						</td>
						<td style="width: 196px;">
							<select class="input_text_10" name="iDocFacilities" id="iDocFacilities" style="width:180px;">
								<option value="">Select Facility</option>
								<?php foreach($facilities as $facility): ?>
									<option value="<?php echo $facility['id'] ?>"><?php echo $facility['name'] ?></option>
								<?php endforeach; ?>
							</select>
							<div class="label">Facility (iDoc)</div>
						</td>
						<td style="width: 210px;">
							<div id="dateFieldControler">
								<select class="input_text_10" name="dayReport" id="dayReport" onChange="DateOptions(this.value);" style="width:200px;">
									<option value="Daily" <?php if($dayReport=='Daily') echo 'selected';?> >Daily</option>
									<option value="Weekly" <?php if($dayReport=='Weekly') echo 'selected';?>>Weekly</option>
									<option value="Monthly" <?php if($dayReport=='Monthly') echo 'selected';?>>Monthly</option>
									<option value="Quarterly" <?php if($dayReport=='Quarterly') echo 'selected';?>>Quarterly</option>
									<option value="Date" <?php if($dayReport=='Date') echo 'selected';?>>Date Range</option>
								</select>
								<div class="label">Date (By Rx. Given Date)</div>
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
							<label><input type="radio" name="show_report" id="show_report_sum" value="summary" checked="checked">Summary</label><br />
							<label><input type="radio" name="show_report" id="show_report_det" value="detail">Detail</label>
						</td>
					</tr>
				</table>
			</form>
        </div>
        <iframe src="capture_report_result.php" name="frame_result" id="frame_result" style="width:99.7%; height:<?php echo $_SESSION['wn_height']-454;?>px; margin:0px; margin-top:3px; display:block; overflow-x:hidden; overflow-y:scroll; border:1px solid #BCBCBC" frameborder="0" framespacing=0 scrolling="yes"></iframe>
    </div>
</body>
</html>