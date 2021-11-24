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
    $operator_id= array_combine($operator_id, $operator_id);
    $insuranceGrp = array_combine($insuranceGrp, $insuranceGrp);
    $ins_carriers = array_combine($ins_carriers, $ins_carriers);
    $cpt_code_id = array_combine($cpt, $cpt);
    $dx_code10 = array_combine($icd10_codes, $icd10_codes);
	$reason_code= array_combine($reason_code, $reason_code);
    $processReport = $_REQUEST['process'];
}


//--- GET ALL OPERATORS DETAILS ----
$selOperId= join(',',$_REQUEST['operator_id']);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');


//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive')
        $color = 'color:#CC0000!important';
		
		$sel = '';
		if (sizeof($cpt_code_id) > 0) {
			if ($cpt_code_id[$cpt_fee_id]) {
				$sel = 'selected';
			}
    }
    $cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
    $cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}


//SET ICD10 CODE DROP DOWN
$arrICD10Code = $CLSReports->getICD10Codes();
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dx10code) {
	$dx10code = str_replace("'", "", $dx10code);
	$sel = ($dx_code10[$dx10code]) ? 'selected' : '';
    $all_dx10_code_options .= "<option value='" . $dx10code . "' ".$sel.">" . $dx10code . "</option>";
}
$allDXCount10 = sizeof($arrICD10Code);

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
				if (sizeof($ins_carriers) > 0) {
					if ($ins_carriers[$ins_id]) {
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


//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
$ins_group_arr = array();
$ins_group_options = '';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

    $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "' ORDER BY id";
    $res = imw_query($qry);
    $tmp_grp_ins_arr = array();
    if (imw_num_rows($res) > 0) {
        while ($det_row = imw_fetch_array($res)) {
            $tmp_grp_ins_arr[] = $det_row['id'];
        }
        $selected = '';
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        $ins_group_arr[$grp_ins_ids] = $ins_grp_name;

        if ($insuranceGrp[$grp_ins_ids])
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}


//GET ALL OPERATORS DETAILS
$allOprCount = 0;
$operatorNameOptions = '';
$res = imw_query("Select id, lname, fname, delete_status from users WHERE lname!='' ORDER BY delete_status, lname, fname");
while ($row = imw_fetch_array($res)) {
    $select = '';
    $color = '';
    if ($row['delete_status'] == 1)
        $color = 'color:#CC0000!important';

    if (sizeof($operator_id) > 0) {
        if ($operator_id[$row['id']])
            $select = "SELECTED";
    }

    $operatorNameOptions .= "<option $select value='" . $row['id'] . "' style=\"" . $color . "\">" . $row['lname'] . ' ' . $row['fname'] . "</option>";
    $allOprCount++;
}

// REJECTION REASONS 
$qry=imw_query("Select * FROM cas_reason_code ORDER BY cas_code");
while($rs=imw_fetch_assoc($qry)){
	$tempArr[$rs['cas_code']] = $rs['cas_code'];
}
asort($tempArr);
foreach($tempArr as $cas_code){
	$select='';
	if($reason_code[$cas_code]){  $select = "SELECTED"; }
	$reason_code_options.="<option value='".$cas_code."' ".$select.">".$cas_code."</option>";
}
$select=($reason_code['0']=='0')? "SELECTED" : "";
$reason_code_options.="<option value='0' $select >No Reason Code</option>";



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
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

//SEARCH MODE DEFAULT SELECTION
$default_date_range_for='dos';
if(isset($filter_arr['dot'])){ $default_date_range_for='transaction_date';}
elseif(isset($filter_arr['dos'])){ $default_date_range_for='dos';}
elseif(isset($filter_arr['dor'])){ $default_date_range_for='date_of_payment';}
elseif(isset($filter_arr['doc'])){ $default_date_range_for='doc';}
?>

<style>
    .rptsearch1, .rptsearch2, .rptsearch3{ min-height:105px;}

    @media (min-width: 1400px) and (max-width: 2000px) {
        .rptsearch1 .col-sm-2 {
            width:14%;
        }}
    </style>

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
            .pd10.report-content {
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

        <form name="frm_reports" id="frm_reports" action="" method="post">
        	<input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            
            <div id="divLightBox" style="display:none">
                <span class="closeBtn"  onclick="toggle_lightbox()"></span>
                <div class="section_header">Save Search Criteria</div>
                <div id="content">Search Name: 
                    <input type="text" name="report_name" id="report_name"/>
                    <input type="button" name="save_search" id="save_search" value="Save" class="dff_button" onClick="$('#chkSaveSearch').attr('checked', false);submitForm()"/>
                </div>
            </div>
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="call_from_saved" id="call_from_saved" value="0">
            <div class=" container-fluid">
            	<div class="anatreport">
                <div id="sel_contianer" style="position:absolute;bottom:0px;"></div>
              	<div class="row" id="row-main">
                	<div class="col-md-3" id="sidebar">
                  	<div class="reportlft" style="height:100%;">
                    	<div class="practbox">
                      	<div class="anatreport"><h2>Filters</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                            
                            <div class="col-sm-6">
                              <label>Reason Code</label>
                              <select name="reason_code[]" id="reason_code" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $reason_code_options; ?>
                              </select>
                            </div>
                                           
                            <div class="col-sm-6">
                              <label>Operator</label>
                              <select name="operator_id[]" id="operator_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#sel_contianer">
                                  <?php echo $operatorOption; ?>
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
                            <br />
                            <div class="col-sm-6 pt10">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="process" id="summary" value="Summary" <?php if ($_POST['process'] == 'Summary' ) echo 'CHECKED'; ?> /> 
                                <label for="summary">Summary</label>
                            	</div>
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="process" id="detail" value="Detail" <?php if ($_POST['process'] == 'Detail' ||  empty($_POST['process'])) echo 'CHECKED'; ?> /> 
                                <label for="detail">Detail</label>
                            	</div>
                          	</div>
                            
                            <div class="col-sm-6 pt10">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dos" onclick="javascript:enable_disable_time(this.value);" value="date_of_service"  <?php if ($_POST['DateRangeFor'] == 'date_of_service' || $default_date_range_for=='dos') echo 'CHECKED'; ?>  />
                            		<label for="dos">DOS</label>
                            	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dot" onclick="javascript:enable_disable_time(this.value);" value="transaction_date" <?php if ($_POST['DateRangeFor'] == 'transaction_date' || $default_date_range_for=='dot') echo 'CHECKED'; ?>  /> 
                                <label for="dot">DOT</label>
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
                            	<label for="insuranceGrp">Insurance Group</label>
                              <select name="insuranceGrp[]" id="insuranceGrp" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                             		<?php echo $ins_group_options; ?>
                            	</select>
                          	</div>
                            
                            <div class="col-sm-6" >
                            	<label for="ins_carriers">Ins. Carriers</label>
                              <select name="ins_carriers[]" id="ins_carriers" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-container="#sel_contianer">
                              <?php echo $insComName_options; ?>
                              </select>
                           	</div>

                            <div class="col-sm-6" >
                            	<label for="cpt">CPT</label>
                              <select name="cpt[]" id="cpt" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                               <?php echo $cpt_code_options; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-6" >
                            	<label for="icd10_codes">ICD10 Codes</label>
                              <select name="icd10_codes[]" id="icd10_codes" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                               <?php echo $all_dx10_code_options; ?>
                              </select>
                           	</div>
                        	</div>
                       	</div>
                    	</div>
                      
                      <div class="grpara">
                      	<div class="anatreport"><h2>Format</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                          
                          	<div class="col-sm-4">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="output_option" id="output_actvity_summary" value="view" <?php if ($_POST['output_option'] == 'view' || $_POST['output_actvity_summary']=='') echo 'CHECKED'; ?>/> 
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
                      	</div>
                        
												<div class="clearfix">&nbsp;</div>
                        
                    	</div>
                    	
                  	</div>
                  	<div id="module_buttons" class="ad_modal_footer text-center">
												<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
											</div>

										
				  </div>
                  
                  <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg pointer" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>

                            <div class="pd10 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
                                        if ($_POST['form_submitted'])
										{
											include('top_rej_reasons_result.php');
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
		
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';
	
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
		
		if(output=='output_pdf'){
			generate_pdf(op);
			top.show_loading_image('hide');
		}
		if(output=='output_csv'){
			export_csv();
			top.show_loading_image('hide');
		}
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		if(dbtemp_name=='Referring Revenue'){
			var ctrlObj=$('#selectedRef');
			if(!ctrlObj.val()){
				top.show_loading_image('hide');
				alert('Please select referring physician groups.');
				return false;
			}
		}
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
		//$('#divLightBox').append('<div id="fade"></div>');
		//$('#fade').css({'filter':'alpha(opacity=50)'}).fadeIn();
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



	function enableOrNot(val) {
		/*		if(val=='Detail'){
		 $('#home_facility').attr('disabled', false);
		 }else{
		 $('#home_facility').attr('checked', false);
		 $('#home_facility').attr('disabled', true);
		 }*/
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
		
		//$('#call_from_saved').val('0');

		//DateOptions("<?php echo $_POST['dayReport']; ?>");
		//enableOrNot("<?php echo $_POST['processReport']; ?>");
		//addRemoveGroupBy("<?php echo $DateRangeFor; ?>");

		//$("#searchCriteria").msDropdown({roundedBorder: false});
		//oDropdown = $("#searchCriteria").msDropdown().data("dd");
		//oDropdown.visibleRows(10);
		
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
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
		
		
	});

</script>
    </body>
</html>