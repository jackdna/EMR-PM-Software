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
require_once('common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if ($_POST['form_submitted']) {
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$_REQUEST['from_date'] = $_REQUEST['to_date'] = date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$_REQUEST['from_date'] = $arrDateRange['WEEK_DATE'];
		$_REQUEST['to_date'] = date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$_REQUEST['from_date'] = $arrDateRange['MONTH_DATE'];
		$_REQUEST['to_date'] = date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$_REQUEST['from_date'] = $arrDateRange['QUARTER_DATE_START'];
		$_REQUEST['to_date'] = $arrDateRange['QUARTER_DATE_END'];
	}

	$strPhysician = implode(',', $provider);
	
	//---------------------
	$phpDateFormat 		= phpDateFormat();
	$curDate 			= date($phpDateFormat.' h:i A');
	$op_name_arr 		= preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy 			= ucfirst(trim($op_name_arr[1][0]));
	$createdBy 		   .= ucfirst(trim($op_name_arr[0][0]));

}
/*---SETTING COLUMNS dd---*/
$arr_showCols = array(
				'pd.id AS pid|~~|Pt.Id'=>'Pt. ID',
				'title|~~|Title'=>'Title',
				'fname|~~|First Name'=>'First Name',
				'lname|~~|Last Name'=>'Last Name',
				'mname|~~|Middle Name'=>'Middle Name',
				'CONCAT(lname,", ",fname) AS ptShortName|~~|Pt. Name'=>'Pt. Name (Lname, Fname)',
				'CONCAT(lname,", ",fname," ",mname) AS ptMediumName|~~|Pt. Name'=>'Pt. Name (Lname, Fname Mname)',
				'CONCAT(lname,", ",fname," ",mname," - ",pid) AS ptLargename|~~|Pt. Name'=>'Pt. Name - ID (Lname, Fname Mname - ID)',
				'DOB|~~|DOB'=>'Pt. DOB',
				'ss|~~|SS#'=>'Pt. SSN',
				'sex|~~|Gender'=>'Pt. Gender',
				'status|~~|Marital Status'=>'Pt. Marital Status',
				'CONCAT(street,", ",city," ",state,", ",postal_code) AS ptFullAddress|~~|Address'=>'Pt. Address (Street, City State, Zipcode)',
				'street|~~|Street Address'=>'Pt. Street',
				'city|~~|City'=>'Pt. City',
				'state|~~|State'=>'Pt. State',
				'postal_code|~~|Zip Code'=>'Pt. Zipcode',
				'phone_home|~~|Phone (Home)'=>'Pt. Phone Home',
				'phone_biz|~~|Phone (Office)'=>'Pt. Phone Office',
				'phone_cell|~~|Cell Number'=>'Pt. Cell Number',
				'preferr_contact|~~|Preferred Contact'=>'Preferred Contact',
				'email|~~|Pt. Email Id'=>'Pt. Email ID',
				'patientStatus|~~|Pt. Status'=>'Pt. Status',
				'primary-provider|~~|Primary Ins. Carrier'=>'Primary Ins. Carrier name',
				'primary-policy_number|~~|Primary Ins policy#'=>'Primary Ins policy#',
				'primary-copay|~~|Primary Ins CoPay'=>'Primary Ins CoPay',
				'secondary-provider|~~|Secondary Ins. Carrier'=>'Secondary Ins. Carrier name',
				'secondary-policy_number|~~|Secondary Ins policy#'=>'Secondary Ins policy#',
				'secondary-copay|~~|Secondary Ins CoPay'=>'Secondary Ins CoPay',
				'primary_care_phy_name|~~|Primary Care Physician'=>'Primary Care Physician',
				'primary_care|~~|Referring Phy.'=>'Referring Phy.'
				);
$ar_showCols = $arr_showCols;
$showColsNames = '';
$selectShow = $_REQUEST['showCols'];
foreach ($ar_showCols as $key => $val) {
	$select='';
	if(in_array($key,$selectShow))$select='SELECTED';
	$showColsNames .= "<option value='" . $key . "' ".$select.">" . $val . "</option>";
}

/*---SETTING COLUMNS dd---*/
$arr_appt_showCols = array(
				'sa.sa_facility_id|~~|Facility'=>'Facility',
				'sa.sa_doctor_id|~~|Physician'=>'Physician',
				'sa.sa_app_start_date|~~|Appointment Date'=>'Appointment Date',
				'sa.sa_app_starttime|~~|Appointment Time'=>'Appointment Time',
				'sa.procedureid|~~|Primary Procedure'=>'Primary Procedure',
				'sa.sec_procedureid|~~|Secondary Procedure'=>'Secondary Procedure',
				'sa.tertiary_procedureid|~~|Tertiary Procedure'=>'Tertiary Procedure',
				'sa.sa_comments|~~|Appointment Comment'=>'Appointment Comment',
				'prv.status_date|~~|Made Date'=>'Made Date',
				'prv.oldMadeBy|~~|Made By'=>'Made By',
				'prv.status AS appointmentStatus|~~|Status'=>'Status',
				'prv.status_time AS check_in_time|~~|Check In'=>'Check In',
				'prv.status_time AS check_out_time|~~|Check Out'=>'Check Out'
				);
$ar_appt_showCols = $arr_appt_showCols;
$showApptColsNames = '';
$select_Show = $_REQUEST['showApptCols'];
foreach ($ar_appt_showCols as $key => $val) {
	$select='';
	if(in_array($key,$select_Show))$select='SELECTED';
	$showApptColsNames .= "<option value='" . $key . "' ".$select.">" . $val . "</option>";
}

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['comboFac'],$_REQUEST['comboFac']);
$fac_query = "select id,name,fac_prac_code from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_arr = $fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
	$fac_id = $fac_res['fac_prac_code']."_".$fac_res['id'];
	$facId = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
    $fac_arr[$facId] = $fac_res['name'];
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_id.'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
$fac_cnt=sizeof($fac_id_arr);

//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_sel= ($provider['0']=='0')? 'SELECTED' : '';
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

//GET ALL USERS
$rs=imw_query("Select id, fname, mname, lname, username FROM users");	
$providerNameArr[0] = 'No Provider';
while($res=imw_fetch_array($rs)){
	$id  = $res['id'];
	$pro_name_arr = array();
	$pro_name_arr["LAST_NAME"] = $res['lname'];
	$pro_name_arr["FIRST_NAME"] = $res['fname'];
	$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
	$pro_name = changeNameFormat($pro_name_arr);
	$providerNameArr[$id] = $pro_name;
	$arrAllUsersUName[$res['username']] = $id;
}

//-----GETTING APPOINTMENT STATUS----
$appt_query = "SELECT id, status_name FROM schedule_status WHERE id NOT IN (1,5)";
$appt_query_res = imw_query($appt_query);
$appt_opts_arr = array();
$apptOption = "";
while ($apptDetails = imw_fetch_array($appt_query_res)) {
	$apptId = $apptDetails['id'];
    $appt_opts_arr[$apptId] = $apptDetails['status_name'];
}

$schPro_qry_res = imw_query("SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id =0 AND active_status = 'yes' AND user_group <> '' ORDER BY proc");
$procDetails = array();
while ($procRow = imw_fetch_assoc($schPro_qry_res)) {
	$procDetails[] = $procRow;
}

$proc_options_arr = array();
$proc_options_arr[0] = 'No Procedure';
for($i=0;$i<count($procDetails);$i++){
	$proc_options_dr_arr = array();
	$procId_arr=array();
	$procId = $procDetails[$i]['id'];
	$procName = $procDetails[$i]['proc'];
	$subQry = imw_query("SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id != 0 AND active_status = 'yes' and procedureId='$procId' ORDER BY proc");
	$procDetails_dr = array();
	while ($subRow = imw_fetch_assoc($subQry)) {
		$procDetails_dr[] = $subRow;
	}
	$procId_arr[]=$procId;
	for($k=0;$k<count($procDetails_dr);$k++){
		$procId_arr[]=$procDetails_dr[$k]['id'];
	}
	$procId_imp=implode(',',$procId_arr);
	$proc_options_arr[$procId_imp] = $procName;
}


//---SETTING PATIENT STATUS---
$arr_status_options = core_pt_status_list();
$arr_status_id = array(); $arr_status_name = array();
$pt_status_all = '';
for($i=0; $i<count($arr_status_options); $i++){
	$arr_status_id[] = $arr_status_options[$i]['pt_status_id'];
	$arr_status_name[] = $arr_status_options[$i]['pt_status_name'];
	$pt_status_all .= $arr_status_options[$i]['pt_status_name']."','";
}
if($pt_status_all != ""){$pt_status_all = substr($pt_status_all,0,-3);}
$pt_status_all = $pt_status_all;
$pt_status_id  = $arr_status_id;
$pt_status_name = $arr_status_name;
$pt_stat_name = '';
foreach ($pt_status_name as $pt_status) {
	$sel= "";
	if(in_array($pt_status,$ptstatus))$sel='SELECTED';
	$pt_stat_name .= "<option value='" . $pt_status . "' ".$sel.">" . $pt_status . "</option>";
}

//CSV NAME
$dbtemp_name = 'Patients CSV Export';
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
												<select name="comboFac[]" id="comboFac" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $facilityName; ?>
												</select>
                                            </div>
											<div class="col-sm-6">
											  <label>Provider</label>
											  <select name="provider[]" id="provider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												  <?php echo $physicianName; ?>
											  </select>
											</div>											
											<div class="col-sm-6">
                                                <label>Report Columns</label>
												<select name="showCols[]" id="showCols" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $showColsNames; ?>
												</select>
                                            </div>
											<div class="col-sm-6">
                                                <label>Appointment Columns</label>
												<select name="showApptCols[]" id="showApptCols" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $showApptColsNames; ?>
												</select>
                                            </div>
											<div class="col-sm-6">
                                                <label>Pt. Status</label>
												<select name="ptstatus[]" id="ptstatus" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $pt_stat_name; ?>
												</select>
                                            </div>
											
											 <div class="clearfix"></div>
											<div class="col-sm-6">
												<label for="reg_from">Registration From</label>
												<div class="input-group">
													<input type="text" name="reg_from" placeholder="From" style="font-size: 12px;" id="reg_from" value="<?php echo $_REQUEST['reg_from']; ?>" class="form-control date-pick">
													<label class="input-group-addon" for="reg_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>	
											<div class="col-sm-6">	
												<label for="reg_to">To</label>
												<div class="input-group">
													<input type="text" name="reg_to" placeholder="To" style="font-size: 12px;" id="reg_to" value="<?php echo $_REQUEST['reg_to']; ?>" class="form-control date-pick">
													<label class="input-group-addon" for="reg_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
												</div>
											</div>
											
											<div class="col-sm-6">
												<label for="lname_from">Last Name From</label>
												<div>
													<input type="text" name="lname_from" placeholder="From" style="font-size: 12px;" id="lname_from" value="<?php echo $_REQUEST['lname_from']; ?>" class="form-control">
												</div>	
											</div>	
											<div class="col-sm-6">	
												<label for="lname_to">To</label>
												<div>
													<input type="text" name="lname_to" placeholder="To" style="font-size: 12px;" id="lname_to" value="<?php echo $_REQUEST['lname_to']; ?>" class="form-control">
												</div>
											</div>
											<div class="col-sm-12 mt5">
												<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="sch_date" id="sch_date" value="1" <?php if ($_POST['sch_date'] == '1') echo 'CHECKED'; ?>/> 
													<label for="sch_date">Schedule Date</label>
												</div>
											</div>																							
											<div class="col-sm-12 hide date_td">
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
                                                            <input type="text" name="from_date" placeholder="From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="from_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>	
                                                    <div class="col-sm-5">	
                                                        <div class="input-group">
                                                            <input type="text" name="to_date" placeholder="To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="to_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
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
											include('detail_patient_report_print.php');
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
		
		if($('#sch_date').is(':checked')){
			if($('.date_td').hasClass('hide') == true) $('.date_td').removeClass('hide');
			if($('.date_td').hasClass('show') == false) $('.date_td').addClass('show');
		}else{
			if($('.date_td').hasClass('hide') == false) $('.date_td').addClass('hide');
			if($('.date_td').hasClass('show') == true) $('.date_td').removeClass('show');
		}
				
		$('body').on('click', '#sch_date',function(){
			if($(this).is(':checked')){
				if($('.date_td').hasClass('hide') == true) $('.date_td').removeClass('hide');
				if($('.date_td').hasClass('show') == false) $('.date_td').addClass('show');
			}else{
				if($('.date_td').hasClass('hide') == false) $('.date_td').addClass('hide');
				if($('.date_td').hasClass('show') == true) $('.date_td').removeClass('show');
			}
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

	function addRemoveGroupBy(dateRangeFor) {
		if (dateRangeFor == 'date_of_service') {
			$("#viewBy").append('<option value="operator">Operator</option>');
			$('#without_deleted_amounts').attr('disabled', true);
		} else {
			$("#viewBy option[value='operator']").remove();
			$('#without_deleted_amounts').attr('disabled', false);
		}
	}

	// SAVED SEARCH FUNCTIONS
	var dChk = 0;
	function callAjaxFile(ddText, opIndex) {
		oDropdown.off("change");
		var returnVal = 0;
		dChk = 1;
		var dd = confirm('Are sure to delete the selected search?');
		if (dd) {
			$.ajax({
				url: "delete_search.php?sTxt=" + ddText,
				success: function (callSts) {
					if (callSts == '1') {
						oDropdown.close();
						oDropdown.remove(opIndex);
						oDropdown.set("selectedIndex", 0);
					}
				}
			});
		}
		return returnVal;
	}

	function callSavedSearch(srchVal, formId) {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		if (srchVal != '' && dChk != '1') {
			dChk = 0;

			$('#call_from_saved').val('yes');
			$('#' + formId).submit();
		}
		dChk = 0;
	}
	// END SAVED SEARCH	


	function enable_disable_time(ctrlVal){
		if(ctrlVal=='transaction_date'){
			$('#hourFrom').prop('disabled', false);
			$('#hourTo').prop('disabled', false);
		}else{
			$('#hourFrom').prop('disabled', true);
			$('#hourTo').prop('disabled', true);
		}
	}
	$(document).ready(function (e) {
		
		DateOptions('<?php echo $_POST['dayReport'];?>');
		enable_disable_time('<?php echo $_POST['DateRangeFor'];?>');
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