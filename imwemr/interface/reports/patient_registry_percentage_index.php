<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['from_date']) == true && empty($_REQUEST['to_date']) == true) {
    $_REQUEST['from_date'] = $_REQUEST['to_date'] = date($phpDateFormat);
}

if ($_POST['form_submitted']) {
	
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}
	//---------------------
	$phpDateFormat 		= phpDateFormat();
	$curDate 			= date($phpDateFormat.' h:i A');
	$op_name_arr 		= preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy 			= ucfirst(trim($op_name_arr[1][0]));
	$createdBy 		   .= ucfirst(trim($op_name_arr[0][0]));
}

$dbtemp_name = 'Patient Registry Fields';
//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>
<!-- Bootstrap -->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<style>
.pd5.report-content {
	position:relative;
	margin-left:40px;

	background-color: #EAEFF5;
}
.fltimg {
	position:absolute;
}
.fltimg span.glyphicon {
	position: absolute;
	top: 170px;
	left: 10px;
	color: #fff;
}
.reportlft .btn.btn-mkdef {
	padding-top: 6px;
	padding-bottom: 6px;
}
#content1{
	background-color:#EAEFF5;
}
.total-row {
	height: 1px;
	padding: 0px;
	background: #009933;
}	
</style>
</head>
<body>
<form name="frm_reports" id="frm_reports" action="" method="post">
<input type="hidden" name="form_submitted" id="form_submitted" value="1">
<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
<div class=" container-fluid">
	<div class="anatreport">
		 <div id="select_drop" style="position:absolute;bottom:0px;"></div>   
		<div class="row" id="row-main">
			<div class="col-md-3" id="sidebar">
				<div class="reportlft" style="height:100%;">
					<div class="practbox">
						<div class="anatreport"><h2>Practice Filter</h2></div>
						<div class="clearfix"></div>
						<div class="pd5" id="searchcriteria">
							<div class="row">
								<div class="col-sm-6">
									<label>Registration From</label>
									<div class="input-group">
										<input type="text" name="from_date" placeholder="From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date']; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="from_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>	
								<div class="col-sm-6">	
									<label>Registration To</label>
									<div class="input-group">
										<input type="text" name="to_date" placeholder="To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date']; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="to_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>
							</div>
							<div class="row"><br>
								<div class="col-sm-6">	
									<label>Practice Fields (Demographics)</label>
									<select name="field_type" id="field_type" class="selectpicker pull-right" data-width="100%">
										<option value="1" <?php echo ($field_type == '1' ? 'selected' : '');?>>Advisory</option>  
										<option value="2" <?php echo ($field_type == '2' ? 'selected' : '');?>>Mandatory</option>  
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="grpara">
						<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
							<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
						</div>
					</div>                                                                                        
				</div>
			</div>
			</form>
			<div class="col-md-9" id="content1">
				<div class="btn-group fltimg" role="group" aria-label="Controls">
					<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
				</div>
				<div class="pd5 report-content">
					<div class="rptbox">
						<div id="html_data_div" class="row">
							<?php
							if($_POST['form_submitted']) {
								 include('patient_registry_percentage_result.php');
							}else{
								echo '<div class="text-center alert alert-info">No Search Done.</div>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	
	var printFile = '<?php echo $printFile; ?>';
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.frm_reports.submit();
	}

	lightBoxFlag = 0;
	function toggle_lightbox(show_hide_flag) {
		show_hide_flag = show_hide_flag || '';
		if (show_hide_flag == 'hide')
			lightBoxFlag = 1;
		else if (show_hide_flag == 'show')
			lightBoxFlag = 0;

		var popupid = '#divLightBox';
		if (!lightBoxFlag) {
			$(popupid).fadeIn();
			lightBoxFlag = 1;
		} else {
			$(popupid).fadeOut();
			lightBoxFlag = 0;
		}
		$('#report_name').val('');
		var popuptopmargin = ($(popupid).height() + 10) / 2;
		var popupleftmargin = ($(popupid).width() + 10) / 2;
		$(popupid).css({
			'margin-top': -popuptopmargin,
			'margin-left': -popupleftmargin
		});
	}

	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	} 

	$(document).ready(function (e) {
		DateOptions('<?php echo $_POST['dayReport'];?>');
		var page_heading = "<?php echo $dbtemp_name; ?>";
		set_header_title(page_heading);
	});

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
		if(printFile==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
	
	<?php if(isset($_REQUEST['recallTemplatesListId']) && isset($_REQUEST['form_submitted']) && empty($_REQUEST['recallTemplatesListId']) == false){ ?>
		var mainBtnArr = new Array;
		mainBtnArr[0] = new Array("sendEmail","Send Email","top.fmain.send_email();");
		top.btn_show("O4A",mainBtnArr);
	<?php } ?>
</script>
</body>
</html>