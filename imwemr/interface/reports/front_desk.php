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
$dateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET Groups SELECT BOX ----
$group_query = "Select gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if(in_array($group_id,$grp_id))$sel='SELECTED';
    $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$allGrpCount = sizeof(explode('</option>', $groupName)) - 1;

// POS Facility
$facility_name=array_combine($sc_name, $sc_name);
$facilityName = $CLSReports->getFacilityName($facility_name,'1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$user_query = "Select id, lname, fname, mname, delete_status FROM users WHERE Enable_Scheduler='1' OR user_type='1' ORDER BY delete_status, lname, fname";
$user_query_res = imw_query($user_query);
$user_id_arr = array();
$userName = "";
while ($user_res = imw_fetch_array($user_query_res)) {
	$sel='';
	$sel = (in_array($user_res['id'], $_REQUEST['Physician'])) ? 'selected' : '';
    $userName .= '<option value="'.$user_res['id'].'" '.$sel.'>'.$user_res['lname'].', '.$user_res['fname'].'</option>';
}
$allPhyCount = sizeof(explode('</option>', $userName))-1;

//--- ADMIN ACCESS CHECK ----
$adminAccess = false;
$reportManagerAccess = false;
if(core_check_privilege(array("priv_admin")) == true){
	$adminAccess = true;
}
if(core_check_privilege(array("priv_Reports_manager"))==true){
	$reportManagerAccess = true;
}
//--- GET LOGGED PROVIDER DETAILS ----
if($adminAccess == false && $reportManagerAccess == false){
	$provider_id = $_SESSION['authId'];
	$provider_name = $_SESSION['authProviderName'];
} else{ //--- GET ALL OPERATORS NAME ----
	$operatorOption = $CLSCommonFunction->dropDown_providers(implode(',',$operatorId), '', '','', '','', array('1'));
	$allOprCount = sizeof(explode('</option>', $operatorOption)) - 1;
}
//MAKE TIME OPTIONS
$timeHourFromOptions = '<option value="0">00 am</option>';
$timeHourToOptions = '<option value="0">00 am</option>';
for ($i = 1; $i <= 23; $i++) {
    $fromSel = $toSel = '';
    $ampm = 'am';
    $num = $i;
    if ($i > 11) {
        if ($i > 12)
            $num = $i - 12;
        $ampm = 'pm';
    }
    if ($num < 10)
        $num = '0' . $num;

    if ($_POST['hourFrom'] == $i)
        $fromSel = 'SELECTED';
    $timeHourFromOptions .= '<option value="' . $i . '" ' . $fromSel . '>' . $num . ' ' . $ampm . '</option>';

    if ($_POST['hourTo'] == $i)
        $toSel = 'SELECTED';
    $timeHourToOptions .= '<option value="' . $i . '" ' . $toSel . '>' . $num . ' ' . $ampm . '</option>';
}

$dbtemp_name = 'Front Desk';
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
			<div id="common_drop" style="position:absolute;bottom:0px;"></div>
			<div class="row" id="row-main">
				<div class="col-md-3" id="sidebar">
					<div class="reportlft" style="height:100%;">
						<div class="practbox">
							<div class="anatreport"><h2>Practice Filter</h2></div>
							<div class="clearfix"></div>
							<div class="pd5" id="searchcriteria">
								<div class="row">
									<div class="col-sm-6">
										<label for="grp_id">Group</label>
										<select name="grp_id[]" id="grp_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
										<?php echo $groupName; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label for="sc_name">Facility</label>
										<select name="sc_name[]" id="sc_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
										<?php echo $facilityName; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label for="Physician">Provider</label>
										<select name="Physician[]" id="Physician" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
										<?php echo $userName; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Operator</label> <!-- Check Privilege -->
										<?php if($adminAccess == false && $reportManagerAccess == false){ ?>
											<input type="text" class="form-control" name="operator" id="operator" value="<?php echo $provider_name; ?>">
											<input type="hidden" name="operatorId" id="operatorId" value="<?php echo $provider_id; ?>">
										<?php } else {?>
											<select name="operatorId[]" id="operatorId" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
												<?php echo $operatorOption; ?>
											</select>
										<?php } ?>
									</div>
									<div class="col-sm-6">
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
									<div class="col-sm-6">	
									  <div class="col-sm-6">
										<div class="form-group">
										  <label>Time From</label>
										  <select name="hourFrom" id="hourFrom" class="selectpicker" data-width="100%" data-size="10" data-actions-box="false" title="Select" data-container="#common_drop">
											<?php echo $timeHourFromOptions; ?>
										  </select>
										</div>
									  </div>
									  <div class="col-sm-6">
										<div class="form-group">
										  <label>Time To</label>
										  <select name="hourTo" id="hourTo" class="selectpicker" data-width="100%" data-size="10" data-actions-box="false" title="Select" data-container="#common_drop">
											<?php echo $timeHourToOptions; ?>
										  </select>
										</div>
									  </div>
									</div>
									<div class="col-sm-6">
										<div class="radio radio-inline pointer">
											<input type="radio" name="processReport" id="summary" value="Summary" <?php if ($_POST['processReport'] == 'Summary' || !isset($_POST['processReport']) ) echo 'CHECKED'; ?>/>
											<label for="summary">Summary</label>
										</div>
										<div class="radio radio-inline pointer">
											<input type="radio" name="processReport" id="detail" value="Detail" <?php if ($_POST['processReport'] == 'Detail') echo 'CHECKED'; ?>/>
											<label for="detail">Detail</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="appointflt">
						<div class="anatreport"><h2>Analytic Filter</h2></div>
						<div class="clearfix"></div>
						<div class="pd5" id="searchcriteria">
							<div class="row">
								<div class="col-sm-6">
									<label for="viewBy">View By</label>
									<select name="viewBy[]" id="viewBy" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<option value="trans" <?php if(in_array('trans', $viewBy)) echo "Selected"; ?>>Transactions</option>
										<option value="inout" <?php if(in_array('inout', $viewBy)) echo "Selected"; ?>>Check In/Out</option>
									</select>
								</div>
								<div class="col-sm-6">
									<label for="sortBy">Sort By</label>
									<select name="sortBy[]" id="sortBy" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<option value="patientName" <?php if(in_array('patientName', $sortBy)) echo "Selected"; ?>>Patient name</option>
										<option value="DOS" <?php if(in_array('DOS', $sortBy)) echo "Selected"; ?>>Date of Service</option>
										<option value="paymentDate" <?php if(in_array('paymentDate', $sortBy)) echo "Selected"; ?>>Transaction order</option>
										<option value="Charges" <?php if(in_array('Charges', $sortBy)) echo "Selected"; ?>>Charges</option>
									</select>
								</div>
							</div>
						</div>
						</div>
						<div class="grpara">
							<div class="anatreport"><h2>Group BY</h2></div>
							<div class="clearfix"></div>
							<div class="pd5" id="searchcriteria">
								<div class="row">
									<div class="col-sm-4">
										<div class="radio radio-inline pointer">
											<input type="radio" name="groupBy" id="grpby_physician" value="physician" <?php if (empty($_POST['groupBy']) || $_POST['groupBy'] == 'physician') echo 'CHECKED'; ?>/> 
											<label for="grpby_physician">Physician</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="radio radio-inline pointer">
											<input type="radio" name="groupBy" id="grpby_facility" value="facility" <?php if ($_POST['groupBy'] == 'facility') echo 'CHECKED'; ?>/> 
											<label for="grpby_facility">Facility</label>
										</div>
									</div>
									<div class="col-sm-4">	
										<div class="radio radio-inline pointer">
											<input type="radio" name="groupBy" id="grpby_department" value="department" <?php if ($_POST['groupBy'] == 'department') echo 'CHECKED'; ?>/> 
											<label for="grpby_department">Department</label>
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
						<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
					</div>
					<div class="pd5 report-content">
						<div class="rptbox">
							<div id="html_data_div" class="row">
								<?php
								if($_POST['form_submitted']) {
									include('front_desk_report.php');
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
		document.frm_reports.submit();
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