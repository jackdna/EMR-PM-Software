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
	
	
    $insuranceName = array_combine($insuranceName, $insuranceName);
    $processReport = $_REQUEST['process'];
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
		if(in_array($group_id,$groups))$sel='SELECTED';

    $groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}

//--- GET ALL PHYSICIAN DETAILS ----
$physicianName = $CLSCommonFunction->drop_down_providers(implode(',',$Physician),'1','1');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

//--- GET FACILITY SELECT BOX ----
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
// POS Facility
$facility_name=array_combine($facility_name, $facility_name);
$facilityName = $CLSReports->getFacilityName($facility_name,'1');

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
		$sel = '';
		if (sizeof($insuranceName) > 0){
			if (in_array($ins_id,$insuranceName)){
				$sel = 'selected';
			}
        }
        $ins_comp_arr[$ins_id] = $ins_name;
        if ($insQryRes[$i]['attributes']['insCompStatus'] == 0)
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</option>";
        else
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</option>";        
    }
}
$insurance_cnt = sizeof($ins_comp_arr);

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
//CSV NAME
$dbtemp_name='EID Status';
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
                    <div id="sel_contianer" style="position:absolute;bottom:0px;"></div>
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
												<select name="groups[]" id="groups" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $groupName; ?>
												</select>
											</div>
											<div class="col-sm-6">
                                                <label>Provider</label>
												<select name="Physician[]" id="Physician" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#sel_contianer">
												<?php echo $physicianName; ?>
												</select>
                                            </div>
											<div class="col-sm-6">
												<label>Facility</label>
												<select name="facility_name[]" id="facility_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#sel_contianer" >
												<?php echo $facilityName; ?>
												</select>
											</div>
											<div class="col-sm-6">
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
											</div>

											<div class="col-sm-12">
                                                <div class="radio radio-inline pointer">
                                                	<input type="radio" name="DateRangeFor" id="dos" value="date_of_service"  <?php if ($_POST['DateRangeFor'] == 'date_of_service' || $_POST['DateRangeFor']=='') echo 'CHECKED'; ?> />
                                                    <label for="dos">DOS</label>
                                                </div>
                                                <div class="radio radio-inline pointer">
                                                	<input type="radio" name="DateRangeFor" id="doc" value="doc" <?php if ($_POST['DateRangeFor'] == 'doc' || $default_date_range_for=='doc') echo 'CHECKED'; ?> /> 
                                                	<label for="doc">DOC</label>
                                                </div>
											</div>	
                                            <div class="col-sm-12">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="having_balance" id="having_balance" value="1" <?php echo ($_POST['having_balance']=='1')?'checked':''; ?> />
                                                    <label for="having_balance">Only records having balance&nbsp;&nbsp;</label>
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
											<div class="col-sm-6" >
												<label for="insuranceName">Ins. Carriers</label>
												<select name="insuranceName[]" id="insuranceName" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $insComName_options; ?>
												</select>
											</div>
										</div>
									</div>
                                </div>
<!--								<div class="grpara">
                                    <div class="anatreport"><h2>Group BY</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
											<div class="col-sm-6">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="groupBy" id="grpby_physician" value="physician" <?php if ($_POST['groupBy'] == 'physician') echo 'CHECKED'; ?> checked /> 
                                                    <label for="grpby_physician">Physician</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="groupBy" id="grpby_facility" value="facility" <?php if ($_POST['groupBy'] == 'facility') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_facility">Facility</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>    
-->                              	<div class="grpara">
                                    <div class="anatreport"><h2>Format</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_csv" value="output_csv" CHECKED /> 
                                                    <label for="output_csv">CSV</label>
                                                </div>
                                            </div>  
										</div>
                                    </div>
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
											include('eid_status_result.php');
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

<!--<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $csv_file_name;?>" />
</form>-->

<form name="csvDownloadForm" id="csvDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
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
	var zip_file_name = '<?php echo $zip_file_name;?>';
	var printFile = '<?php echo $printFile; ?>';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';

	
	//BUTTONS
/*	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
*/	//-------	

	
	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		if ($('#searchCriteria').val() == '') {
			if ($('#chkSaveSearch').prop('checked') == true) {
				toggle_lightbox();
				//$('#divLightBox').toggle('slow');
				return false;
			}
			if (lightBoxFlag && $('#report_name').val() == '') {
				alert('Please enter search name');
				return false;
			} else if (lightBoxFlag && $('#report_name').val() != '') {
				for (i = 0; i < arrSearchName.length; i++) {
					if (arrSearchName[i] == $('#report_name').val()) {
						alert('Search name already exist.');
						return false;
					}
				}
			}
		}
		document.frm_reports.submit();
	}


	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
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

	function set_container_height(){
	$('.reportlft').css({
		'height':(window.innerHeight),
		'max-height':(window.innerHeight),
		'overflow-x':'hidden',
		'overflowY':'auto'
	});
	} 

	$(document).ready(function (e) {

		set_container_height();
		DateOptions('<?php echo $_POST['dayReport'];?>');
	
		$(window).resize(function(){
			set_container_height();
		});
		set_header_title(dbtemp_name);

		if(printFile==1){
			document.getElementById("csvDownloadForm").submit();		
			top.show_loading_image('hide');
		}		
	});

</script>
    </body>
</html>