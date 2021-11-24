<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}
//--- GET ALL PHYSICIAN DETAILS ----
$physicianName = $CLSCommonFunction->drop_down_providers(implode(',',$phyId),'1','1');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

//--- GET ALL OPERATORS DETAILS ----
$operatorOption = $CLSCommonFunction->dropDown_providers(implode(',',$oprId), '', '');
$opr_cnt = sizeof(explode('</option>', $operatorOption)) - 1;

$creditPhysicinName = $CLSCommonFunction->drop_down_providers(implode(',',$crediting_provider), '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;

//--- GET FACILITY NAME ----
$facility_id= array_combine($_REQUEST['facId'],$_REQUEST['facId']);
$posfacilityName = $CLSReports->getFacilityName($facility_id, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;

$dbtemp_name = "Modified Encounters";
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
        <form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
            <input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
            <input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
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
                                                <label>Billing Provider</label>
                                                <select name="phyId[]" id="phyId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>
											<div class="col-sm-6">
											  <label>Crediting Provider</label>
											  <select name="crediting_provider[]" id="crediting_provider" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												  <?php echo $creditPhysicinName; ?>
											  </select>
											</div>	
											<div class="col-sm-12">
												<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
													<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
													<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same.</label>
												</div>										  
											</div>																							
											<div class="col-sm-6">
                                                <label>Facility</label>
                                                <select name="facId[]" id="facId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $posfacilityName; ?>
                                                </select>
                                            </div>
											<div class="col-sm-6">
                                                <label>Operator</label>
                                                <select name="oprId[]" id="oprId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $operatorOption; ?>
												</select>
                                            </div>

											<div class="col-sm-12">
                                                <label>Period</label>
												<div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker pull-right" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
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
										</div>
                                    </div>
                                </div>
								<div class="clearfix">&nbsp;</div>
                            </div>
                            <div id="module_buttons" class="ad_modal_footer text-center">
                            	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
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
											include('modified_encounters_result.php');
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
	var file_location = '<?php echo $file_location; ?>';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	var dbtemp_name = "<?php echo $dbtemp_name; ?>";
	
	if(HTMLCreated==true){
		mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		btncnt++;
		mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");	
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}
	
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.sch_report_form.submit();
	}

	//--- Date check funtion
	function validDateCheck(StartDate,EndDate){
		var dateFrom = Date.parse(document.getElementById(StartDate).value)
		var dateTo = Date.parse(document.getElementById(EndDate).value)
		var return_val = false;
		if(dateFrom != '' && dateTo != '' && dateFrom > dateTo){
			return_val = true;		
		}
		return return_val;
	}
		
	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
		$(".toggle-sidebar").click(function () {
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

	$(window).load(function(){
		set_container_height();
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(HTMLCreated==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);  
</script>
</body>
</html>