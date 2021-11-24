<?php
set_time_limit (0);
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

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET PHYSICIAN NAME ---
$user_query = "Select id, fname, mname, lname FROM users where user_type IN(1,3,13) ORDER BY lname, fname";
$user_query_res = imw_query($user_query);
$user_id_arr = array();
$userName = "";
while ($user_res = imw_fetch_array($user_query_res)) {
	$sel='';
	$sel = (in_array($user_res['id'], $_REQUEST['phy_name'])) ? 'selected' : '';
    $userName .= '<option value="'.$user_res['id'].'" '.$sel.'>'.$user_res['lname'].', '.$user_res['fname'].'</option>';
}
$allPhyCount = sizeof(explode('</option>', $userName))-1;

$dbtemp_name = 'Clinincal Productivity';
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
			<div class="row" id="row-main">
				<div class="col-md-3" id="sidebar">
					<div class="reportlft" style="height:100%;">
						<div class="practbox">
							<div class="anatreport"><h2>Practice Filter</h2></div>
							<div class="clearfix"></div>
							<div class="pd5" id="searchcriteria">
								
								<div class="row">
									<div class="col-sm-12">
										<label>Provider</label>
										<select name="phy_name[]" id="phy_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $userName; ?>
										</select>
									</div>
									<div class="col-sm-12">
										<label>Period</label>
										<div id="dateFieldControler">
											<select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
												<option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'selected="selected"'; ?>>Daily</option>
												<option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'selected="selected"'; ?>>Weekly</option>
												<option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'selected="selected"'; ?>>Monthly</option>
												<option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'selected="selected"'; ?>>Quarterly</option>
												<option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'selected="selected"'; ?>>Date Range</option>
											</select>
										</div>
										<div class="row" style="display:none" id="dateFields">
											<div class="col-sm-5">
												<div class="input-group">
													<input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
													<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>	
											<div class="col-sm-5">	
												<div class="input-group">
													<input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
													<label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>
											<div class="col-sm-2">
												<button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
											</div>
										</div>
									</div>
									<br />
									<div class="col-sm-12 clinical_options">
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" name="allmr" id="allmr" value="1" onClick="selectAll();" <?php if ($_POST['allmr'] == '1') echo 'CHECKED'; ?>>
												<label for="allmr">MR(All)</label>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" class="mr_vals" name="mr1" id="mr1" value="1"  onClick="checkCount();" <?php if ($_POST['mr1'] == '1') echo 'CHECKED'; ?>>
												<label for="mr1">MR 1</label>
											</div>
										</div>	
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" class="mr_vals" name="mr2" id="mr2" value="1" onClick="checkCount();" <?php if ($_POST['mr2'] == '1') echo 'CHECKED'; ?>>
												<label for="mr2">MR 2</label>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" class="mr_vals" name="mr3" id="mr3" value="1" onClick="checkCount();" <?php if ($_POST['mr3'] == '1') echo 'CHECKED'; ?>>
												<label for="mr3">MR 3</label>
											</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" name="iop" id="iop" value="1" <?php if ($_POST['iop'] == '1') echo 'CHECKED'; ?>>
												<label for="iop">IOP</label>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" name="intermediate" id="intermediate" value="1" <?php if ($_POST['intermediate'] == '1') echo 'CHECKED'; ?>>
												<label for="intermediate">Intermediate</label>
											</div>
										</div>	
										<div class="col-sm-3">
											<div class="checkbox checkbox-inline pointer">
												<input type="checkbox" name="comprehensive" id="comprehensive" value="1" <?php if ($_POST['comprehensive'] == '1') echo 'CHECKED'; ?>>
												<label for="comprehensive">Comprehensive</label>
											</div>
										</div>
									</div>
									<div class="col-sm-6"><br>
										<div class="radio radio-inline pointer">
											<input type="radio" name="report_view" id="summary" value="summary" <?php if ($_POST['report_view'] == 'summary' || !isset($_POST['report_view']) ) echo 'CHECKED'; ?>/>
											<label for="summary">Summary</label>
										</div>
										<div class="radio radio-inline pointer">
											<input type="radio" name="report_view" id="detail" value="detail" <?php if ($_POST['report_view'] == 'detail') echo 'CHECKED'; ?>/>
											<label for="detail">Detail</label>
										</div>
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
				<div class="col-md-9" id="content1">
					<div class="btn-group fltimg" role="group" aria-label="Controls">
						<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
					</div>
					<div class="pd5 report-content">
						<div class="rptbox">
							<div id="html_data_div" class="row">
								<?php
								if($_POST['form_submitted']) {
									include('clinical_productivity_result.php');
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
</form>
<?php $csvName = preg_replace('/\s+/', '_', $dbtemp_name); ?>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
	<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $csvName; ?>.csv" />
</form>
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	
	$(document).ready(function () {
		$(".fltimg").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
		
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		
		if($('.clinical_options input[type=checkbox]:checked').length <= 0){
			top.show_loading_image("hide");
			top.fAlert('Please select atleast one clinical options checkbox');
			return false;
		}
		
		document.frm_reports.submit();
	}
	
	function selectAll(){
	   if($('#allmr').prop("checked")) $('.mr_vals').prop('checked',true);
	   else $('.mr_vals').prop('checked', false);		
	}
	
	function checkCount(){
		count=$('input.mr_vals:checked').length;
		if(count<3){ $('#allmr').prop("checked", false) }
		if(count>2){ $('#allmr').prop("checked", true) }
	}

	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}

	$(document).ready(function (e) {
		DateOptions('<?php echo $_POST['dayReport'];?>');
		function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});	
	} 

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(printFile==1){
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
		set_header_title(page_heading);
	});

</script>
</body>
</html>