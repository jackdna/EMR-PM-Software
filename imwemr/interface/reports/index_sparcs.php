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
set_time_limit(0);
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

$dbtemp_name = 'SPARCS Report';
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
                                                <label>DOS Period</label>
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
                                                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                                        </div>
                                                    </div>	
                                                    <div class="col-sm-5">	
                                                        <div class="input-group">
                                                            <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
                                                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
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
											include('sparcs_report_result.php');
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
<iframe name="sparcs_result_frame" height="1" width="1" style="position:absolute; z-index:-1000;"></iframe>
<form name="sparcs_form" id="sparcs_form" action="sparcs_report_generate.php" method ="post" target="sparcs_result_frame"> 
	<input type="hidden" name="selected_charge_ids" id="selected_charge_ids" value="">
	<input type="hidden" name="sparcs_file_mode" id="sparcs_file_mode" value="T">
</form> 

<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="xml">
    <input type="hidden" name="file" id="file" value="<?php echo $xml_file_name;?>" />
</form> 
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	var HTMLCreated='<?php echo $HTMLCreated;?>';
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
	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		
		document.frm_reports.submit();
	}

	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {	
		mainBtnArr[0] = new Array("get_report", "Get Test File", "top.fmain.process_report('T');");
		mainBtnArr[1] = new Array("get_report", "Get Production File", "top.fmain.process_report('P');");
		top.btn_show("PPR", mainBtnArr);
	}
	//----------

	function process_report(fmode){
		var arrChrgId=new Array();
		var chgid=i=0;
		$('.chk_box_charges').each(function(){
			if($(this).is(':checked')){
				arrChrgId[i]=$(this).val();
				i++;
			}
		});

		if(arrChrgId.length<=0){
			alert('Please select alteast one record.');
			return false;
		}

		$('#selected_charge_ids').val(arrChrgId.join());
		$('#sparcs_file_mode').val(fmode);

	//	window.open('about:blank', 'sparcs_report', 'height=800, width=1000');
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.sparcs_form.submit();
		
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

	function selAllChkbox(ctrl){
		
		if($(ctrl).is(':checked')){
			$('.chk_box_charges').each(function(){
				$(this).prop('checked', true);
			});
		}else{
			$('.chk_box_charges').each(function(){
				$(this).prop('checked', false);
			});
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
		} 

		$(window).load(function(){
			set_container_height();
			$('#csvFileDataTable').height($('.reportlft').height()-70);
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