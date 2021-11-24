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

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET Groups SELECT BOX ----
$selArrGroups = array_combine($_REQUEST['groups'],$_REQUEST['groups']);
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
    $group_id = $group_res['gro_id'];
   	$sel=(in_array($group_id, $_REQUEST['grp_id'])== true)? 'SELECTED' : '';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$grp_cnt=sizeof($group_id_arr);

$dbtemp_name = 'Statement Payment';
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
		<input type="hidden" name="submitFRM" value="1">
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
									<div class="col-sm-6">
										<label>Groups</label>
										<select name="grp_id[]" id="grp_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $groupName; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Date Range For</label>
										<select name="DateRangeFor" id="DateRangeFor" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10">
											<option value="date_of_payment" <?php if ($_POST['DateRangeFor'] == 'date_of_payment') echo 'selected="selected"'; ?>>Payment Date</option>
											<option value="transaction_date" <?php if ($_POST['DateRangeFor'] == 'transaction_date') echo 'selected="selected"'; ?>>Transaction Date</option>
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
									<div class="col-sm-6"><br />
										<div class="radio radio-inline pointer">
											<input type="radio" name="Process" id="Summary" value="Summary" checked <?php if ($_POST['Process'] == 'Summary') echo 'CHECKED'; ?> /> 
											<label for="Summary">Summary</label>
										</div>
										<div class="radio radio-inline pointer">
											<input type="radio" name="Process" id="Detail" value="Detail" <?php if ($_POST['Process'] == 'Detail') echo 'CHECKED'; ?> /> 
											<label for="Detail">Detail</label>
										</div>
									</div>
									</div>
									</div>
								</div>
							</div>
						</div>
						<div id="module_buttons" class="ad_modal_footer text-center">
							<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
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
										include('statement_payment_result.php');
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
	<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
		<input type="hidden" name="csv_text" id="csv_text">	
		<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
	</form> 
<script type="text/javascript">
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
	
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.frm_reports.submit();
	}

	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}

	function set_container_height(){
		$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
		$('.reportlft').css({
			'height':$_hgt,
			'max-height':$_hgt,
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});	
	}

	$(document).ready(function (e) {
		set_container_height();
		var page_heading = "<?php echo $dbtemp_name; ?>";
		set_header_title(page_heading);
		DateOptions('<?php echo $_POST['dayReport'];?>');
	});
	
	$(window).load(function(){
		set_container_height();
		if(printFile==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
	});
</script>