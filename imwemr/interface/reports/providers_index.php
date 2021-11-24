<?php
set_time_limit (0);
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');
require_once('CLSSchedulerReports.php');
$CLSCommonFunction = new CLSCommonFunction;
//$CLSReports = new CLSReports;
list($year_list, $quarter_list, $month_list, $week_list) = sr_populate_yr_qr_mn_wk_lists();
//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$todate = get_date_format(date('Y-m-d'));

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
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

//--- GET FACILITY SELECT BOX ----
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$arrFacIds = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	$arrFacIds[$fac_id] = $fac_id;
	$sel = (in_array($fac_res['id'], $_REQUEST['facility_name'])) ? 'selected' : '';
    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

$refPhy_res = imw_query("select physician_Reffer_id, physician_phone from refferphysician");
$arr_priCarePhy = array();
while ($refPhy_row = imw_fetch_array($refPhy_res)){
	$ref_id = $refPhy_row['physician_Reffer_id'];
    $arr_priCarePhy[$ref_id] = $refPhy_row['physician_phone'];
}

$dbtemp_name = 'Providers Report';
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
												<label>Facility</label>
                                                <select name="facility_name[]" id="facility_name" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#select_drop">
                                                    <?php echo $facilityName; ?>
                                                </select>
											</div>
											<div class="col-sm-6">
                                                <label>Provider</label>
												<select name="phyId[]" id="phyId" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#select_drop">
												<?php echo $physicianName; ?>
												</select>
                                            </div>
											
											<div class="col-sm-6">
                                                <label>Report Type</label>
												<select name="rep_type" id="rep_type" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" onChange="displayOption(this);">
													<option <?php if ($_POST['rep_type'] == '1') echo 'SELECTED'; ?> value="1" data-value="<?php echo $_REQUEST['rep_date_dy'] ?>">Day</option>
													<option <?php if ($_POST['rep_type'] == '2') echo 'SELECTED'; ?> value="2" data-value="<?php echo $_REQUEST['rep_date_wk'] ?>">Weekly</option>
													<option <?php if ($_POST['rep_type'] == '3') echo 'SELECTED'; ?> value="3" data-value="<?php echo $_REQUEST['rep_date_mn'] ?>">Monthly</option>
													<option <?php if ($_POST['rep_type'] == '4') echo 'SELECTED'; ?> value="4" data-value="<?php echo $_REQUEST['rep_date_qa'] ?>">Quarterly</option>
													<option <?php if ($_POST['rep_type'] == '5') echo 'SELECTED'; ?> value="5" data-value="<?php echo $_REQUEST['rep_date_ya'] ?>">Yearly</option>	
												</select>
                                            </div>
											<div class="col-sm-6">
												<div id="dy" style="display:block">
													<label>Select Date</label>
													<input type="text" class="form-control date-pick" onblur="checkdate(this);" size="11" name="rep_date_dy" id="rep_date_dy" value="<?php echo $todate; ?>" />
												</div>
												<div id="wk" style="display:none">
													<label>Select Week</label>
													<select name="rep_date_wk" id="rep_date_wk" class="selectpicker" data-width="100%" data-size="10">
														<option value="">--Select Week--</option>
														<?php echo $week_list; ?>
													</select>
												</div>
												<div id="mn" style="display:none">
													<label>Select Month</label>
													<select name="rep_date_mn" id="rep_date_mn" class="selectpicker" data-width="100%" data-size="10">
														<option value="">--Select Month--</option>												
														<?php echo $month_list; ?>	
													</select>
												</div>	
												<div id="qa" style="display:none">
													<label>Select Quarter</label>
													<select name="rep_date_qa" id="rep_date_qa" class="selectpicker" data-width="100%" data-size="10">
														<option value="">--Select Quarter--</option>												
														<?php echo $quarter_list; ?>
													</select>
												</div>	
												<div id="ya" style="display:none">
													<label>Select Year</label>
													<select name="rep_date_ya" id="rep_date_ya" class="selectpicker" data-width="100%" data-size="10">
														<option value="">--Select Year--</option>												
														<?php echo $year_list; ?>	
													</select>
												</div>							
											</div>	
											<div class="col-sm-6">
												<br />
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="process" id="Summary" value="Summary" checked <?php if ($_POST['process'] == 'Summary') echo 'CHECKED'; ?> /> 
                                                    <label for="Summary">Summary</label>
                                                </div>
												<div class="radio radio-inline pointer">
                                                    <input type="radio" name="process" id="Detail" value="Detail" <?php if($_POST['process'] == 'Detail') echo 'CHECKED'; ?> /> 
                                                    <label for="Detail">Detail</label>
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
											include('provider_report_print.php');
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
		
		if($('#rep_type option:selected').length){
			displayOption($('#rep_type'));
		}
		
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
		
		var msg = '';
		if($("#rep_date_wk").val() =="" && $("#rep_type").val() =="2"){
			top.show_loading_image("hide", 250, "Please wait while processing....");
			msg = "Please select any Week.";
		}else if($("#rep_date_mn").val()=="" && $("#rep_type").val()=="3"){
			top.show_loading_image("hide", 250, "Please wait while processing....");
			msg = "Please select any Month.";
		}else if($("#rep_date_qa").val()=="" && $("#rep_type").val()=="4"){
			top.show_loading_image("hide", 250, "Please wait while processing....");
			msg = "Please select any Quarter.";
		}else if($("#rep_date_ya").val()=="" && $("#rep_type").val()=="5"){
			top.show_loading_image("hide", 250, "Please wait while processing....");
			msg = "Please select any Year.";
		}
		if(msg && msg.length){
			msg =decodeURIComponent(msg);
			top.fAlert(msg);
			return false;
		}
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
	
	
	/*     PROCEDURE REPORT JS START   */
	function displayOption(obj){	
	var dataObj = $(obj).find('option:selected').data('value');
	var dis=$(obj).val();
	$('#dy').hide();$('#wk').hide();$('#mn').hide();$('#qa').hide();$('#ya').hide();$('#date_range').hide();
	var selObj = '';
	switch(dis)
	{
		case "Day":
			$('#dy').show();
			if($('#dy').length) selObj = $('#dy');
			break;    
		case "Weekly":
			$('#wk').show();
			if($('#wk').length) selObj = $('#wk');
			break;
		case "Monthly":
			$('#mn').show();
			if($('#mn').length) selObj = $('#mn');
			break;	
		case "Quarterly":
			$('#qa').show();
			if($('#qa').length) selObj = $('#qa');
			break;	
		case "Yearly":
			$('#ya').show();
			if($('#ya').length) selObj = $('#ya');
			break;
		case "1":
			$('#dy').show();
			if($('#dy').length) selObj = $('#dy');
			break;    
		case "2":
			$('#wk').show();
			if($('#wk').length) selObj = $('#wk');
			break;
		case "3":
			$('#mn').show();
			if($('#mn').length) selObj = $('#mn');
			break;	
		case "4":
			$('#qa').show();
			if($('#qa').length) selObj = $('#qa');
			break;	
		case "5":
			$('#ya').show();
			if($('#ya').length) selObj = $('#ya');
			break;
		case "date_range":
			$('#date_range').show();
			break;
		default : break;			  
	}
	if(selObj.length && dataObj){
		var selectObj = selObj.find('select');
		selectObj.selectpicker('val', dataObj).selectpicker('refresh');
	}
	
}

</script>
</body>
</html>