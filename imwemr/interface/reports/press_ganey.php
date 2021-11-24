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
$curDate = date($phpDateFormat.' h:i A');

if (empty($_REQUEST['start_date']) == true && empty($_REQUEST['end_date']) == true) {
    $_REQUEST['start_date'] = $_REQUEST['end_date'] = date($phpDateFormat);
}


//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
$fac_query = "SELECT f.id, f.name FROM facility f 
		   LEFT JOIN server_location sl ON (f.server_location=sl.id)";
$fac_query_res = imw_query($fac_query);
$facilities_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $facilities_arr[$fac_id] = $fac_res['name'];
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
$allFacCount=sizeof($facilities_arr);

$dbtemp_name = "Press Ganey";
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
			<input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
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
                                                <label>Facility</label>
                                                <select name="facility_name[]" id="facility_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
                                                    <?php echo $facilityName; ?>
                                                </select>
                                            </div>
											<div class="col-sm-12">
												<label>Period</label>
												<div id="dateFieldControler">
													<select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
														<option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
														<option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
														<option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
														<option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
														<option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
													</select>
												</div>
												<div class="row" style="display:none" id="dateFields">
													<div class="col-sm-5">
														<div class="input-group">
															<input type="text" name="start_date" placeholder="From" style="font-size: 12px;" id="start_date" value="<?php echo $_REQUEST['start_date']; ?>" class="form-control date-pick">
															<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
														</div>
													</div>
													<div class="col-sm-5">
														<div class="input-group">
															<input type="text" name="end_date" placeholder="To" style="font-size: 12px;" id="end_date" value="<?php echo $_REQUEST['end_date']; ?>" class="form-control date-pick">
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
                            </div>
                            <div id="module_buttons" class="ad_modal_footer text-center">
								<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
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
											include('press_ganey_result.php');
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
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 
<script type="text/javascript">
	var hasData = '<?php echo $hasData; ?>';
	
	var mainBtnArr = new Array();
	var btncnt=0;
/*	if(hasData==1){
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
		btncnt++;
	}
	top.btn_show("PPR", mainBtnArr);
*/	
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.sch_report_form.submit();
	}
	
	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
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
		if(hasData==1){
			//$(".fltimg .toggle-sidebar").click();
			download_csv();
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