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

if (empty($_REQUEST['dateFrom']) == true && empty($_REQUEST['dateTo']) == true) {
    $_REQUEST['dateFrom'] = $_REQUEST['dateTo'] = date($phpDateFormat);
}

$dbtemp_name = 'TLF Proof';
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
											<div class="col-sm-12">
												<label>Patient</label>
											</div>
											<div class="col-sm-6">
												<input type="hidden" name="patientId" id="patientId" value="<?php echo trim($_REQUEST['patientId']); ?>">
												<input class="form-control" type="text" id="txt_patient_name" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" onblur="searchPatient(this)" value="<?php echo trim($_POST['txt_patient_name']);?>">
											</div> 
											<div class="col-sm-4">
												<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
													<option value="Active">Active</option>
													<option value="Inactive">Inactive</option>
													<option value="Deceased">Deceased</option> 
													<option value="Resp.LN">Resp.LN</option> 
													<option value="Ins.Policy">Ins.Policy</option>
												</select>
											</div> 
											<div class="col-sm-2 text-center">
												<button class="btn btn-success btn-sm" type="button" onclick="searchPatient();">Search</button>
											</div>
											<div class="col-sm-12">
												<div class="checkbox">
													<input type="checkbox" id="disableDate" name="disableDate" onClick="return disableEnableDates();" <?php if(isset($_REQUEST['disableDate'])) echo 'CHECKED'; ?>>
													<label for="disableDate">Date Range</label>
												</div>
											</div>
											<div class="col-sm-6">
												<label for="dateFrom">DOS From</label>
												<div class="input-group">
													<input type="text" name="dateFrom" placeholder="From" style="font-size: 12px;" id="dateFrom" value="<?php echo $_REQUEST['dateFrom']; ?>" class="form-control date-pick">
													<label class="input-group-addon" for="dateFrom"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>	
											<div class="col-sm-6">	
												<label for="dateTo">To</label>
												<div class="input-group">
													<input type="text" name="dateTo" placeholder="To" style="font-size: 12px;" id="dateTo" value="<?php echo $_REQUEST['dateTo']; ?>" class="form-control date-pick">
													<label class="input-group-addon" for="dateTo"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
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
											include('timelyFillProofDetails.php');
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
		
		$('#txt_patient_name').on('change', function(){
			if($(this).val() == '') $('#patientId').val('');
		});
	});

	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		var curFrmObj = document.frm_reports;
		
		if($('#disableDate').prop('checked') == true){
			curFrmObj.dateFrom.value;
			curFrmObj.dateTo.value;
		}else {
			curFrmObj.dateFrom.value = '';
			curFrmObj.dateTo.value = '';
			if(curFrmObj.txt_patient_name.value == ''){
				top.fAlert("Please select any creteria to search.");
				top.show_loading_image('hide');
				return false;
			}
		}
	
		if(curFrmObj.dateFrom.value != ''){
			if(curFrmObj.dateTo.value==''){
				top.fAlert("Please enter both Dates.");
				top.show_loading_image('hide');
				return false;
			}
		}
		if(curFrmObj.dateTo.value != ''){
			if(curFrmObj.dateFrom.value==''){
				top.fAlert("Please enter both Dates.");
				top.show_loading_image('hide');
				return false;
			}
		}
		if((curFrmObj.dateTo.value == '') && (curFrmObj.dateFrom.value=='') && (curFrmObj.txt_patient_name.value=='')){
			top.fAlert("Please select any creteria to search.");
			top.show_loading_image('hide');
			return false;
		}
		
		if(curFrmObj.txt_patient_name.value!=''){
			curFrmObj.patientId.value;
		}else{
			curFrmObj.patientId.value = '';
		}
		var returnVal = validDateCheck("dateFrom","dateTo");
		if(returnVal == true){
			top.fAlert('Start date Should be less than End date.');	
			document.getElementById("dateFrom").select();		
			top.show_loading_image('hide');
			return false;
		}
		document.frm_reports.submit();
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

	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}
	
	function searchPatient(){
		var name = document.getElementById("txt_patient_name").value;
		var findBy = document.getElementById("txt_findBy").value;
		var validate = true;
		if(name.indexOf('-') != -1){
			name = name.replace(' ','');
			name = name.split('-');
			name = name[0]
			validate = false;
		}
		if(validate){
			if(isNaN(name)){
				pt_win = window.open("../../interface/scheduler/search_patient_popup.php?sel_by="+findBy+"&txt_for="+name+"&btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
			} else {
				getPatientName(name);
			}
		}
		return false;
	}
	function getPatientName(id,obj){
		$.ajax({
			type: "POST",
			url: "<?php echo $GLOBALS['webroot']; ?>/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
			dataType:'JSON',
			success: function(r){
				if(r.id){
					if(obj){
						set_xml_modal_values(r.id,r.pt_name);
					}else{
						$("#txt_patient_name").val(r.pt_name);
						$("#patientId").val(r.id);
						load_ptcomm_ptinfo(r.id);
					}
				}else{
					fAlert("Patient not exists");
					$("#txt_patient_name").val('');
					return false;
				}	
			}
		});
	}
	
	function physician_console(id, name) {
		$("#txt_patient_name").val(name);
		$("#patientId").val(id);
	}
	function disableEnableDates(){
		var chkStatus = $('#disableDate').prop('checked');
		var curFrmObj = document.frm_reports;
		if(chkStatus == true){
			curFrmObj.dateTo.disabled = false;
			curFrmObj.dateFrom.disabled = false;
		}else{
			curFrmObj.dateTo.disabled = true;
			curFrmObj.dateFrom.disabled = true;
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
		disableEnableDates();
	});
	
	$(window).load(function(){
		set_container_height();
	});

	$(window).resize(function(){
		set_container_height();
	});
</script>
</body>
</html>