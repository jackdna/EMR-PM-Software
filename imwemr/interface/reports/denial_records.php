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

$op='l';

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}


$arrSelGroups = array();
if (empty($selGroups) == false) {
    $arrSelGroups = explode(',', $selGroups);
    $arrSelGroups = array_combine($arrSelGroups, $arrSelGroups);
}

if ($_POST['form_submitted']) {
    $grp_id = array_combine($grp_id, $grp_id);
    $facility_id = array_combine($facility_id, $facility_id);
    $strPhysician = implode(',', $filing_provider);
	$pt_status = array_combine($pt_status, $pt_status);
    $processReport = $_REQUEST['processReport'];
}


//GET GROUPS NAME
$rs = imw_query("Select  gro_id,name,del_status from groups_new order by name");
$core_drop_groups = "<option value=''> All </option>";
while ($row = imw_fetch_array($rs)) {
    $sel = '';
    $color = '';
    if ($row['del_status'] == '1')
        $color = 'color:#CC0000!important';

    if ($grp_id[$row['gro_id']])
        $sel = 'SELECTED';

    $core_drop_groups .= '<option value="' . $row['gro_id'] . '" ' . $sel . ' style="' . $color . '" >' . $row['name'] . '</option>';
}
$allGrpCount = sizeof(explode('</option>', $core_drop_groups)) - 2;

//--- GET FACILITY NAME ----
$facilityName = $CLSReports->getFacilityName($facility_id, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;


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

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
foreach($insQryRes as $insDet){
	if($insDet['attributes']['insCompName']!=''){
		$insid=$insDet['attributes']['insCompId'];
		$tempInsCompdata[$insid]=$insDet['attributes'];
		$tempArrSort[$insid]=$insDet['attributes']['insCompName'];
	}
}
asort($tempArrSort);
foreach($tempArrSort as $insid =>$insname){
	$insDet=$tempInsCompdata[$insid];
    if ($insDet['insCompName']!= 'No Insurance') {
        $ins_id = $insDet['insCompId'];
        //$ins_name = $insDet['insCompINHouseCode'];
        $ins_name = $insDet['insCompName'];
        $ins_status = $insDet['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insDet['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
				
		$sel = '';
		if(sizeof($ins_carriers) > 0) {
			if(in_array($ins_id,$ins_carriers)) {
				$sel = 'selected';
			}
        }

        $ins_comp_arr[$ins_id] = $ins_name;
        if ($insDet['insCompStatus'] == 0)
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</option>";
        else
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</option>";

        
    }
}
unset($tempArrSort);
unset($tempInsCompdata);
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

        if (in_array($grp_ins_ids,$insuranceGrp))
		$selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//CSV NAME
$dbtemp_name1= str_replace("/", "_", $dbtemp_name);
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name1)).".csv";

//SEARCH MODE DEFAULT SELECTION
$default_date_range_for='';
if(!$_POST['form_submitted']){
	$default_date_range_for='dos';
}

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 50;
$logicDiv = reportLogicInfo('denial_report', 'tpl', $logicWidth);
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
				<div id="select_drop" style="position:absolute;bottom:0px;"></div>
              	<div class="row" id="row-main">
                	<div class="col-md-3" id="sidebar">
                  	<div class="reportlft" style="height:100%;">
                    	<div class="practbox">
                      	<div class="anatreport"><h2>Practice Filter
							<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>						
						</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                          	<div class="col-sm-6">
                            	<label>Groups</label>
                              <select name="grp_id[]" id="grp_id" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['groups']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-container="#select_drop" data-actions-box="true" data-title="Select All">
                              <?php echo $core_drop_groups; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-6">
                                <label>Facility</label>
                                <select name="facility_id[]" id="facility_id" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['facility']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-container="#select_drop" data-actions-box="true" data-title="Select All">
                                    <?php echo $facilityName; ?>
                                </select>
                            </div>
                            
                            <div class="col-sm-4">
                              <label>Physician</label>
                              <select name="filing_provider[]" id="filing_provider" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['filing_provider']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-container="#select_drop" data-actions-box="true" data-title="Select All">
                                  <?php echo $physicianName; ?>
                              </select>
                            </div>
                            
                            <div class="col-sm-8">
                              <label>Denial Period</label>
                              <div id="dateFieldControler">
                              	<select name="dayReport" id="dayReport" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['date_range']))?'disabled':''; ?> data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
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
							<div class="col-sm-6">
											<label>Ins. Group</label>
											<select name="insuranceGrp[]" id="insuranceGrp" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $ins_group_options; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Ins. Carrier</label>
											<select name="ins_carriers[]" id="ins_carriers" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $insComName_options; ?>
											</select>
										</div>
							<div class="col-sm-2"><br />
								<div class="radio radio-inline pointer">
									<input type="radio" name="DateRangeFor" id="dor" value="dor" <?php if ($_POST['DateRangeFor'] == 'dor' || empty($_POST['DateRangeFor'])) echo 'CHECKED'; ?>  /> 
									<label for="dor">DOR</label>
								</div>
							</div>
							<div class="col-sm-2"><br />
								<div class="radio radio-inline pointer">
									<input type="radio" name="DateRangeFor" id="dot" value="dot" <?php if ($_POST['DateRangeFor'] == 'dot') echo 'CHECKED'; ?> />
									<label for="dot">DOT</label>
								</div>
							</div>
							</div>
                       	</div>
                    	</div>
						<div class="grpara">
							<div class="anatreport"><h2>Include</h2></div>
							<div class="clearfix"></div>
							<div class="pd5" id="searchcriteria">
								<div class="row">
									<div class="col-sm-12">
										<div class="checkbox checkbox-inline pointer">
											<input type="checkbox" name="inc_refraction" id="inc_refraction" value="1" <?php if ($_POST['inc_refraction'] == '1') echo 'CHECKED'; ?>/> 
											<label for="inc_refraction">No Refraction</label>
										</div>
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
                              	<input type="radio" name="output_option" id="output_actvity_summary" <?php echo ($temp_id && !isset($filter_arr['output_actvity_summary']))?'disabled':''; ?> value="view" <?php if($_POST['output_option']=='view' || $_POST['output_option']=="") echo 'CHECKED'; ?>/> 
                                <label for="output_actvity_summary">View</label>
                            	</div>
                           	</div>
                            
                            <div class="col-sm-4">
                              <div class="radio radio-inline pointer">
                                  <input type="radio" name="output_option" id="output_csv" <?php echo ($temp_id && !isset($filter_arr['output_csv']))?'disabled':''; ?> value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/> 
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
					<div class="btn-group fltimg pointer" role="group" aria-label="Controls">
						<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
					</div>

					<div class="pd10 report-content">
						<div class="rptbox">
							<div id="html_data_div" class="row" >
								<?php
								if ($_POST['form_submitted']) {
									include('denial_records_result.php');
								}else{
									echo '<div class="text-center alert alert-info">No Search Done.</div>';
								}
								?>

							</div>
						</div>
					</div>
					<?php echo $logicDiv;?>
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
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';

	$(function () { $('[data-toggle="tooltip"]').tooltip(); 
		$('input[name="processReport"]').on('change',function(){
			if($(this).val().toLowerCase() == 'summary'){
				$('#cpt_check').prop('checked', false);
				$('#cpt_check').prop('disabled', true);
			} else{
				$('#cpt_check').prop('disabled', false);
			}
		})
	});
	
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
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		//mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
		mainBtnArr[0] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	
	
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

	function disable_dates(){
		if($("#no_date").is(':checked')){
			$("#dayReport").prop('disabled','disabled');	
			$("#Start_date").prop('disabled','disabled');	
			$("#End_date").prop('disabled','disabled');	
		}else{
			$("#dayReport").removeAttr('disabled');	
			$("#Start_date").removeAttr('disabled');	
			$("#End_date").removeAttr('disabled');	
		}
	}

	$(document).ready(function (e) {
		
		DateOptions('<?php echo $_POST['dayReport'];?>');
		
		//$('#call_from_saved').val('0');

		//DateOptions("<?php echo $_POST['dayReport']; ?>");
		//enableOrNot("<?php echo $_POST['processReport']; ?>");
		//addRemoveGroupBy("<?php echo $DateRangeFor; ?>");

		//$("#searchCriteria").msDropdown({roundedBorder: false});
		//oDropdown = $("#searchCriteria").msDropdown().data("dd");
		//oDropdown.visibleRows(10);
		
	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	} 
	//if(HTMLCreated==1){
	
	
	$(window).load(function(){
		set_container_height();
		if(HTMLCreated==1){
			if(output=='output_pdf'){
				generate_pdf(op);
				top.show_loading_image('hide');
			}
			if(output=='output_csv'){
				export_csv();
				top.show_loading_image('hide');
			}
		}
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