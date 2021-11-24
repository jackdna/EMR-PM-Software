<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$string_users='';
if($_POST){
	$string_users=implode(',', $users);
}
$operatorOption = $CLSCommonFunction->dropDown_providers($string_users, '', '');
$allUserCnt = sizeof(explode('</option>', $operatorOption)) - 1;


$temp_id = $_REQUEST['sch_temp_id'];
$dbtemp_name = "User Log Report"; 
if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}

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
		</style>
    </head>
    <body>
        <form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
            <input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
            <input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
			<input type="hidden" name="order_by" id="order_by" value="<?php echo $_REQUEST['order_by']; ?>">
			<input type="hidden" name="doSearch" id="doSearch" value="yes">
            <div class=" container-fluid">
                <div class="anatreport">
				<div id="common_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Filters</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria" >
                                        <div class="row">
											<div class="col-sm-6" style="display:none">
												<label>Start Date</label>
												<div class="input-group">
													<input type="text" name="dat_frm" placeholder="From" style="font-size: 12px;" id="dat_frm" value="<?php echo ($_POST['dat_frm']!="")?$_POST['dat_frm']:date(phpDateFormat()); ?>" class="form-control date-pick">
													<label class="input-group-addon" for="dat_frm"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>	
                                            <div class="col-sm-6" style="display:none">	
												<label>End Date</label>
                                                <div class="input-group">
													<input type="text" name="dat_to" placeholder="To" style="font-size: 12px;" id="dat_to" value="<?php echo ($_POST['dat_to']!="")?$_POST['dat_to']:date(phpDateFormat()); ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="dat_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>

											<div class="col-sm-12">
                                                <label>Users</label>
												<select name="users[]" id="users" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#common_drop">
													<?php echo $operatorOption;?>
												</select>
                                            </div>
											
										</div>
                                    </div>
                                </div>
								<div class="grpara">
                                    <div class="anatreport"><h2>Format</h2></div>
                                    <div class="clearfix"></div>
									<div class="pd5" id="searchcriteria">
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
											<input type="radio" name="output_option" id="output_actvity_summary" value="view" <?php if($_POST['output_option']=='view' || $_POST['output_option']=="") echo 'CHECKED'; ?>/> 
											<label for="output_actvity_summary">View</label>
											</div>
										</div>										
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
											<input type="radio" name="output_option" id="output_pdf" value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/> 
											<label for="output_pdf">PDF</label>
											</div>
										</div> 										
										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="output_option" id="output_csv" value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/> 
												<label for="output_csv">CSV</label>
											</div>
										</div>  
									</div>
									<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
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
											include('user_log_result.php');
                                        }else {
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
		
		<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden" name="zipName" id="zipName" value="">	
		<input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
		</form> 		
<?php
	$csvbtnCheck = '';
	if ($_POST['output_option'] == 'output_csv'){
		$csvbtnCheck = 1;
	}
	$csvBtn = "";
	if(@imw_num_rows($run_tab1)>0){ 
		$csvBtn = 1;
	}
?>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var printPdFBtn = '<?php echo $printPdFBtn; ?>';
	var csvbtnCheck = '<?php echo $csvbtnCheck; ?>';
	var csvBtn = '<?php echo $csvBtn; ?>';
	var HTMLCreated = '<?php echo $html_created; ?>';
	var output='<?php echo $_POST['output_option'];?>';
	
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
		mainBtnArr[1] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
	}
	
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'p');
			window.close();
		}
	}
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.sch_report_form.submit();
	}

	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");

		if(printFile==1){
			if(output=='output_pdf'){
				generate_pdf();
				top.show_loading_image('hide');
			}
			if(output=='output_csv'){
				download_csv();
				top.show_loading_image('hide');
			}
		}
		
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
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	}
	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
		if(HTMLCreated==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
</script> 
</body>
</html>