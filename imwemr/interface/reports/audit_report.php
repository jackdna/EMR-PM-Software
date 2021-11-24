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

$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');
$module=$_POST['module'];
if($module<>""){
	$exp_module=explode('/',$module);
	$main_module=$exp_module[0];
	if($exp_module[1]<>""){
		$sub_module=$exp_module[1];
		$mod_sub_whr="at.Category_Desc='$sub_module' and";
	}
	$mod_whr="at.Category='$main_module' and";
}

$temp_id = $_REQUEST['sch_temp_id'];
$dbtemp_name = "Audit Report"; 
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
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Filters</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria" >
                                        <div class="row">
											<div class="col-sm-6">
												<label>Start Date</label>
												<div class="input-group">
													<input type="text" name="dat_frm" placeholder="From" style="font-size: 12px;" id="dat_frm" value="<?php echo ($_POST['dat_frm']!="")?$_POST['dat_frm']:date(phpDateFormat()); ?>" class="form-control date-pick">
													<label class="input-group-addon" for="dat_frm"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>	
                                            <div class="col-sm-6">	
												<label>End Date</label>
                                                <div class="input-group">
													<input type="text" name="dat_to" placeholder="To" style="font-size: 12px;" id="dat_to" value="<?php echo ($_POST['dat_to']!="")?$_POST['dat_to']:date(phpDateFormat()); ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="dat_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>
											<div class="col-sm-12">
                                                <label>Module</label>
                                                <select name="module" id="module" class="form-control minimal">
						<option value="">All Module</option>
						<optgroup label="Clinical Summary"></optgroup>
                        <option value="chart_notes" <?php if($main_module=='chart_notes'){echo"selected";}?>> - All</option>
						<option value="chart_notes/chart_notes_without_provider_notes" <?php if($sub_module=='chart_notes_without_provider_notes'){echo"selected";}?>> - Without Provider Notes</option>
						<option value="chart_notes/chart_notes_with_provider_notes" <?php if($sub_module=='chart_notes_with_provider_notes'){echo"selected";}?>> - With Provider Notes</option>
						<option value="chart_notes-patient_information" <?php if($main_module=='chart_notes-patient_information'){echo"selected";}?>> - Patient Information - All Module</option>
						<option value="chart_notes-patient_information/all" <?php if($sub_module=='all'){echo"selected";}?>> - Patient Information - All</option>
						<option value="chart_notes-patient_information/face_sheet" <?php if($sub_module=='face_sheet'){echo"selected";}?>> - Patient Information - Face Sheet</option>
						<option value="chart_notes-patient_information/print_demographics" <?php if($sub_module=='print_demographics'){echo"selected";}?>> - Patient Information - Demographics</option>
						<option value="chart_notes-patient_information/printMedicalHistory" <?php if($sub_module=='printMedicalHistory'){echo"selected";}?>> - Patient Information - Medical History</option>
						<option value="chart_notes-patient_information/insurance_print" <?php if($sub_module=='insurance_print'){echo"selected";}?>> - Patient Information - Insurance</option>
						<option value="chart_notes-patient_information/print_legal" <?php if($sub_module=='print_legal'){echo"selected";}?>> - Patient Information - Legal Forms</option>
						<option value="chart_notes-patient_information/glaucoma" <?php if($sub_module=='glaucoma'){echo"selected";}?>> - Patient Information - Glaucoma Flow Sheet</option>
						<option value="chart_notes-patient_information/ascan" <?php if($sub_module=='ascan'){echo"selected";}?>> - Patient Information - A/Scan</option>
						<option value="chart_notes/pt_instructions" <?php if($sub_module=='pt_instructions'){echo"selected";}?>> - Pt Instructions</option>
						<option value="Restricted_Providers" <?php if($main_module=='Restricted_Providers'){echo"selected";}?>> - Restricted Providers</option>
                        <optgroup label="Patient Info"></optgroup>
						<option value="patient_info" <?php if($main_module=='patient_info'){echo"selected";}?>> - All</option>
						<option value="patient_info/demographics" <?php if($sub_module=='demographics'){echo"selected";}?>> - Demographics</option>
						<option value="patient_info/insurence" <?php if($sub_module=='insurence'){echo"selected";}?>> - Insurance</option>
						<option value="patient_info-medical_history" <?php if($main_module=='patient_info-medical_history'){echo"selected";}?>> - Med. HX - All</option>
						<option value="patient_info-medical_history/ocular" <?php if($sub_module=='ocular'){echo"selected";}?>> - Med. HX - Ocular</option>
						<option value="patient_info-medical_history/general_medicine" <?php if($sub_module=='general_medicine'){echo"selected";}?>> - Med. HX - General Health</option>
						<option value="patient_info-medical_history/medication" <?php if($sub_module=='medication'){echo"selected";}?>> - Med. HX - Medication</option>
						<option value="patient_info-medical_history/surgeries" <?php if($sub_module=='surgeries'){echo"selected";}?>> - Med. HX - Surgeries</option>
						<option value="patient_info-medical_history/allergies" <?php if($sub_module=='allergies'){echo"selected";}?>> - Med. HX - Allergies</option>
						<option value="patient_info-medical_history/immunizations" <?php if($sub_module=='immunizations'){echo"selected";}?>> - Med. HX - Immunizations</option>
                        <option value="patient_info-medical_history/CC_HX" <?php if($sub_module=='CC_HX'){echo"selected";}?>> - Med. HX - CC HX</option>
                        <option value="patient_info-medical_history/order_set" <?php if($sub_module=='order_set'){echo"selected";}?>> - Med. HX - Order Sets</option>
                        <option value="patient_info-medical_history/problems" <?php if($sub_module=='problems'){echo"selected";}?>> - Med. HX - Problem List</option>
						<!--<option value="patient_info-medical_history/social_history" <?php if($sub_module=='social_history'){echo"selected";}?>> - Med. HX - Social</option>-->
						<!--<option value="patient_info-medical_history/VS" <?php if($sub_module=='VS'){echo"selected";}?>> - Med. HX - VS</option>-->
                        <option value="patient_info-medical_history/Lab" <?php if($sub_module=='VS'){echo"selected";}?>> - Med. HX - Lab</option>
                        <option value="patient_info-medical_history/Radiology" <?php if($sub_module=='VS'){echo"selected";}?>> - Med. HX - Radiology</option>
						<option value="admin/providers" <?php if($main_module=='admin'){echo"selected";}?>>Providers</option>
						<!--<option value="signature" <?php if($main_module=='signature'){echo"selected";}?>>Signature - All</option>-->
						<option value="signature/consent_form_signature" <?php if($sub_module=='consent_form_signature'){echo"selected";}?>>Signature - Consent Form</option>
						<!--<option value="signature/surgery_consent_form_signature" <?php if($sub_module=='surgery_consent_form_signature'){echo"selected";}?>>Signature - Surgery Consent Form</option>-->
					</select>
                                            </div>
											<div class="col-sm-12">
                                                <label>Action</label>
													<select name="action" id="action" class="form-control minimal">
														<option value="">All Action</option>
														<option value="add" <?php if($_POST['action']=='add'){echo"selected";}?>>Add</option>
														<option value="user_login_f" <?php if($_POST['action']=='user_login_f'){echo"selected";}?>>Authentication Fail</option>
														<option value="user_locked" <?php if($_POST['action']=='user_locked'){echo"selected";}?>>A/c Locked</option>
														<option value="delete" <?php if($_POST['action']=='delete'){echo"selected";}?>>Delete</option>
														<option value="phi_export" <?php if($_POST['action']=='phi_export'){echo"selected";}?>>PHI Export</option>  
														<option value="user_login_s/user_logout_s" <?php if($_POST['action']=='user_login_s/user_logout_s'){echo"selected";}?>>Provider Login / Logout</option>								
														<option value="user_session_timeout_s" <?php if($_POST['action']=='user_session_timeout_s'){echo"selected";}?>>Session Timeout</option>
														<option value="app_start/app_stop" <?php if($_POST['action']=='app_start/app_stop'){echo"selected";}?>>Start / Stop Application</option>
														<option value="query_search" <?php if($_POST['action']=='query_search'){echo"selected";}?>>Search/Query</option>								
														<option value="sig_create" <?php if($_POST['action']=='sig_create'){echo"selected";}?>>Signature Created</option>
														<option value="update" <?php if($_POST['action']=='update'){echo"selected";}?>>Update</option>
														<option value="view" <?php if($_POST['action']=='view'){echo"selected";}?>>View</option>
													</select>
                                            </div>
											<div class="col-sm-12">
                                                <label>Operator</label>
                                                <select name="operater" id="operater" class="form-control minimal">
													<option value="" selected="selected">All Operator</option>
													<?php 
														$sel_opr=imw_query("select fname,lname,id from users where superuser!='yes' and delete_status = 0 order by lname,fname");
														while($row_opr=imw_fetch_array($sel_opr)){
														if($row_opr['lname']){
															$lname = $row_opr['lname'].', ';
														}
														$operater_name = $lname.$row_opr['fname'];
														$id = $row_opr['id'];
														?>
														<option value="<?php echo $id;?>" <?php if($_POST['operater']==$id){echo"selected";}?>><?php echo $operater_name;?></option>
													<?php } ?>
													</select>
                                            </div>
											<!-- Pt. Search -->
											<div class="col-sm-12"><label>Patient</label></div>
											<div class="col-sm-5">
												<input type="hidden" name="patientId" id="patientId" value="<?php echo trim($_REQUEST['patientId']); ?>">
												<div class="input-group">
													<input class="form-control" type="text" id="txt_patient_name" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control" onblur="searchPatient(this)" value="<?php echo trim($_POST['txt_patient_name']);?>">
												</div> 
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
											<div class="col-sm-3 text-center">
												<button class="btn btn-success btn-sm" type="button" onclick="searchPatient();">Search</button>
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
											include('audit_report_result.php');
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
	var pdfbtnCheck = '<?php echo $pdfbtnCheck; ?>';
	var printPdFBtn = '<?php echo $printPdFBtn; ?>';
	var csvbtnCheck = '<?php echo $csvbtnCheck; ?>';
	var csvBtn = '<?php echo $csvBtn; ?>';
	var HTMLCreated = '<?php echo $html_created; ?>';
	
	var mainBtnArr = new Array();
	if (csvBtn == '1' && csvbtnCheck == '1') {
		mainBtnArr[0] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
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
	function chk_temp(val) {
		document.getElementById("spanTemp1Id").style.display = 'inline-block';
		document.getElementById("spanTemp2Id").style.display = 'none';
		if (val == "Recall letter") {
			document.getElementById("recallTemplatesListId").disabled = false;
		} else if (val == "Package") {
			document.getElementById("spanTemp1Id").style.display = 'none';
			document.getElementById("spanTemp2Id").style.display = 'inline-block';
			document.getElementById("packageListId").value = '';
		} else {
			document.getElementById("recallTemplatesListId").value = '';
			document.getElementById("recallTemplatesListId").disabled = true;
		}

		if (val == 'Address Labels')
			$('#dymoOptions').slideDown();
		else
			$('#dymoOptions').slideUp();
		$('#sel_dymo').prop('checked', false);
		document.getElementById("dymoPrintersList").disabled = true;
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
		
		$('#txt_patient_name').on('change', function(){
			if($(this).val() == '') $('#patientId').val('');
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
		}
		else{
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
</script> 
</body>
</html>