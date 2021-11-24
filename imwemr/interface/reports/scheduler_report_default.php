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
?>
<?php
/*
  FILE : scheduler_new_report.php
  PURPOSE : Search criteria for scheduler report
  ACCESS TYPE : Direct
 */

//Function Files
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

$selFacility='';
$selArrDispFields=array();
if($_POST['form_submitted']){
	$strSelPhysician= join(',',$_REQUEST['phyId']);
	$selArrAppStatus= array_combine($_REQUEST['ap_status'],$_REQUEST['ap_status']);
	$selArrProc= array_combine($_REQUEST['procedures'],$_REQUEST['procedures']);
	$selArrProcSec= array_combine($_REQUEST['proceduresSec'],$_REQUEST['proceduresSec']);
	$selArrProcTer= array_combine($_REQUEST['proceduresTer'],$_REQUEST['proceduresTer']);
	$selArrDispFields= array_combine($_REQUEST['display_fields'],$_REQUEST['display_fields']);
	$strInsType= join(',',$_REQUEST['ins_type']);
	$dx_code10 = array_combine($Dxcode10, $Dxcode10);
	$selArrInsId= array_combine($_REQUEST['insId'],$_REQUEST['insId']);
	$selOperId= join(',',$_REQUEST['operator_id']);
	
	$selArrFacility= array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
	
}else{
	$selArrDispFields['appt_details']= 'appt_details';
}

//--- GET FACILITY SELECT BOX ----
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$arrFacIds = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	$arrFacIds[$fac_id] = $fac_id;
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}

//--- GET ALL PHYSICIAN DETAILS ----
$physicianName = $CLSCommonFunction->drop_down_providers($strSelPhysician,'1','1');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;


//-----GETTING APPOINTMENT STATUS----
$appt_query = "SELECT id, status_name FROM schedule_status WHERE id NOT IN (1,5)";
$appt_query_res = imw_query($appt_query);
$appt_opts_arr = array();
$apptOption = "";
while ($apptDetails = imw_fetch_array($appt_query_res)) {
	$sel='';
    $apptId = $apptDetails['id'];
    $appt_opts_arr[$apptId] = $apptDetails['status_name'];
	if($selArrAppStatus[$apptId])$sel="SELECTED";

    $apptOption .= '<option value="' . $apptDetails['id'] . '" '.$sel.'>' . $apptDetails['status_name'] . '</option>';
}

//--- GET INSURANCE COMPANY DETAILS ----------
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
	$sel='';
	$ins_id = $insQryRes[$i]['attributes']['insCompId'];
	$ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
	$ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
	if ($ins_name == '') {
		$ins_name = $insQryRes[$i]['attributes']['insCompName'];
		if (strlen($ins_name) > 20) {
			$ins_name = substr($ins_name, 0, 20) . '....';
		}
	}
	
	if($selArrInsId[$ins_id])$sel='SELECTED';

	$ins_comp_arr[$ins_id] = $ins_name;
	if ($insQryRes[$i]['attributes']['insCompStatus'] == 0) {
		$sel_ins_comp_options .= "<option value='" . $ins_id . "' $sel>" . $ins_name . "</option>";
	} else {
		$sel_ins_comp_options .= "<option value='" . $ins_id . "' style='color:red' $sel>" . $ins_name . "</option>";
	}
}
//$insurance_cnt = sizeof($ins_comp_arr);

//--- GETTING SCHEDULER PROCEDURES---------------
$schPro_qry_res = imw_query("SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id =0 AND active_status = 'yes' AND user_group <> '' ORDER BY proc");
$procDetails = array();
while ($procRow = imw_fetch_assoc($schPro_qry_res)) {
	$procDetails[] = $procRow;
}
$proc_options_arr = array();
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
if(count($proc_options_arr) > 0){
	foreach($proc_options_arr as $procKey => $proVal){
		$sel = '';
		if($selArrProc[$procKey]) $sel='SELECTED';
		$proc_options .= '<option value="' . $procKey . '" '.$sel.' >' . $proVal . '</option>';
		
		$selSec = '';
		if($selArrProcSec[$procKey]) $selSec='SELECTED';
		$proc_optionsSec .= '<option value="' . $procKey . '" '.$selSec.' >' . $proVal . '</option>';
		
		$selTer = '';
		if($selArrProcTer[$procKey]) $selTer='SELECTED';
		$proc_optionsTer .= '<option value="' . $procKey . '" '.$selTer.' >' . $proVal . '</option>';
		
		
	}
}

//SET ICD10 CODE DROP DOWN
$arrICD10Code = $CLSReports->getICD10Codes();
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dx10Literals => $dx10code) {
	$dx10Literals = str_replace("'", "", $dx10Literals);
	$sel = ($dx_code10[$dx10Literals]) ? 'selected' : '';
    $all_dx10_code_options.= "<option value='".$dx10Literals."' ".$sel.">" . $dx10code . "</option>";
}
$allDXCount10 = sizeof($arrICD10Code);

//---- GET HEARED ABOUT US VALUES -------
$hrd_opn_qry = "SELECT DISTINCT heard_options,heard_id  FROM heard_about_us where status='0' ";
$hrd_opn_rs = imw_query($hrd_opn_qry);
$heard_about_opts = "";
$heard_opts_arr = array();
while ($heardDetails = imw_fetch_array($hrd_opn_rs)) {
	$sel='';
    $heardId = $heardDetails['heard_id'];
    $heard_opts_arr[$heardId] = substr($heardDetails['heard_options'], 0, 50);
	
	if(in_array($heardId,$heard))$sel='SELECTED';
    $heard_about_opts .= '<option value="' . $heardId . '" '.$sel.' >' . $heardDetails['heard_options'] . '</option>';
}

//--- GET ALL OPERATORS DETAILS ----
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');

//SET Recall Template CODE
	$qry = "SELECT recallLeter_id, recallTemplateName FROM `recalltemplate` order by recallTemplateName";
	$res = imw_query($qry);
	$recallArr = array();
	$recall_options = '';
	
	while ($row = imw_fetch_array($res)) {
		$sel = '';
		$r_id = $row['recallLeter_id'];
		$r_TName = $row['recallTemplateName'];
		$recallArr[$r_id] = $r_TName;
		if ($_REQUEST['recallTemplatesListId'] == $r_id) $sel = 'SELECTED';
		$recall_options .= "<option value='" . $r_id . "' " . $sel . ">" . $r_TName . "</option>";
	}
	
	$qry = "SELECT *  FROM consent_package WHERE delete_status!='yes' order by package_category_name";
	$res = imw_query($qry);
	$packArr = array();
	$pack_options = '';
	while ($row = imw_fetch_array($res)) {
		$sel = '';
		$package_category_id = $row['package_category_id'];
		$package_category_name = $row['package_category_name'];
		$packArr[$package_category_id] = $package_category_name;
		if ($_REQUEST['packageListId'] == $package_category_id) $sel = 'SELECTED';
		$pack_options .= "<option value='" . $package_category_id . "' " . $sel . ">" . $package_category_name . "</option>";
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
 
// Template field value form database
$dbtemp_name = "Scheduler Report"; 
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
<div class=" container-fluid">
	<div class="anatreport">
		<div id="common_drop" style="position:absolute;bottom:0px;"></div>
		<div class="row" id="row-main">
		<form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
			<input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
			<input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
			<input type="hidden" name="Submit" id="Submit" value="get reports">
			<input type="hidden" name="form_submitted" id="form_submitted" value="1">
			<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
			<div class="col-md-3" id="sidebar">
				<div class="reportlft" style="height:100%;">
					<div class="practbox">
						<div class="anatreport"><h2>Practice Filter</h2></div>
						<div class="clearfix"></div>
						<div class="pd5" id="searchcriteria" >
							<div class="row">
								<div class="col-sm-4">
									<label>Groups</label>
									<select name="groups[]" id="groups" class="selectpicker" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['groups']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $groupName; ?>
									</select>
								</div>
								<div class="col-sm-4">
								<label>Provider</label>
									<select name="phyId[]" id="phyId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $physicianName; ?>
									</select>
								</div>
								<div class="col-sm-4">
									<label>Facility</label>
									<select name="facility_name[]" id="facility_name" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $facilityName; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Operator</label>
									<select name="operator_id[]" id="operator_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $operatorOption; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label for="reff_physician"><span class="text_purple">Referring Physicians</span></label>
									<input class="form-control" id="reff_physician" name="reff_physician" value="<?php echo $_REQUEST['reff_physician']; ?>" />
									<input type="hidden" name="id_reff_physician" id="id_reff_physician" value="<?php echo $_REQUEST['id_reff_physician']; ?>" />
								</div>
								<div class="col-sm-6">
									<label>Procedures Primary</label>
									<select name="procedures[]" id="procedures" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $proc_options; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Procedures Secondary</label>
									<select name="proceduresSec[]" id="proceduresSec" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $proc_optionsSec; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Procedures Tertiary</label>
									<select name="proceduresTer[]" id="proceduresTer" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $proc_optionsTer; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Ins. Type</label>
									<select class="selectpicker" name="ins_type[]" id="ins_type" data-container="#common_drop" data-width="100%"  multiple data-actions-box="true" data-title="Select All">
										<option value="primary">Primary</option>
										<option value="secondary">Secondary</option>
										<option value="tertiary">Tertiary</option>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Ins. Carrier</label>
									<select name="insId[]" id="insId" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $sel_ins_comp_options; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Heard About Us</label>
									<select name="heard[]" id="heard" class="selectpicker" data-container="#common_drop" data-width="100%" 	data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $heard_about_opts; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Appt. Status</label>
									<select name="ap_status[]" id="ap_status" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<option value="0">New Appointment</option>
										<?php echo $apptOption; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<label for="aging_from">Age</label>
									<input type="text" name="aging_from" id="aging_from" placeholder="From" value="<?php echo $_REQUEST['aging_from'];?>" class="form-control" />
								</div>
								<div class="col-sm-3">
									<label for="aging_to"></label>
									<input type="text" name="aging_to" id="aging_to" placeholder="To" value="<?php echo $_REQUEST['aging_to'];?>" class="form-control" />
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
								<div class="clearfix"></div>
								<div class="col-sm-6">	
									<label>Report Type</label>
									<select name="report_type" id="report_type" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" onchange="chk_temp(this.value)" >
										<option value="">Select</option>
										<option <?php if ($_POST['report_type'] == 'Address Labels') echo 'SELECTED'; ?> value="Address Labels" >Address Labels</option>
										<option <?php if ($_POST['report_type'] == 'Recall letter') echo 'SELECTED'; ?> value="Recall letter">Recall letter</option>
										<option <?php if ($_POST['report_type'] == 'Post Card') echo 'SELECTED'; ?> value="Post Card">Post Card</option>
										<option <?php if ($_POST['report_type'] == 'Package') echo 'SELECTED'; ?> value="Package">Package</option>
									</select>
								</div>
								<div class="col-sm-6">	
									<label>Template</label>
									<span id="spanTemp1Id" style=" display:block;">
										<select name="recallTemplatesListId" id="recallTemplatesListId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10">
											<option value="">Select Template</option>
											<?php echo $recall_options; ?>
										</select>
									</span>
									<span id="spanTemp2Id" style=" display:none;">
										<select name="packageListId" id="packageListId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10">
											<option value="">Select Template</option>
											<?php echo $pack_options; ?>
										</select>
									</span>
								</div>
								<div class="col-sm-6">	
									<br />
									<div class="checkbox checkbox-inline pointer">
										<input type="checkbox" name="inc_ins_id" id="inc_ins_id" value="1" <?php if(trim($_REQUEST['Submit'])== '' || $_POST['inc_ins_id']=='1') echo 'CHECKED'; ?>/> 
										<label for="inc_ins_id">Include Insurance ID</label>
									</div>
								</div>
								<div class="col-sm-6">
									<label for="icd10_codes">ICD10 Codes</label>
										<select name="Dxcode10[]" id="Dxcode10" data-container="#common_drop" class="selectpicker dropup" data-dropup-auto="false" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $all_dx10_code_options; ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="grpara">
						<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
							<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
						</div>
					</div>                                                                                        
				</div>
			</div>
			</form>
			<div class="col-md-9" id="content1">
				<div class="btn-group fltimg" role="group" aria-label="Controls">
					<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
				</div>
				<div class="pd5 report-content">
					<div class="rptbox">
						<div id="html_data_div" class="row">
							<?php
							if($_POST['form_submitted']){
								include('scheduler_report_default_result.php');
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
<?php $csvName = preg_replace('/\s+/', '_', $dbtemp_name); ?>
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 
<?php if(trim($strHTML) != ""){	?>
	<form name="frmPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" target="_blank" >
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
	</form>	
<?php } ?>

<input type="hidden" name="PAGE_OP" id="PAGE_OP" value="l">
<input type="hidden" name="package_category_id" id="package_category_id" value="<?php echo $package_category_id; ?>">	
<input type="hidden" name="patient_id_comma" id="patient_id_comma" value="<?php echo $patient_id_comma; ?>">
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var showBtn = '<?php echo $showBtn; ?>';
	var report_type = '<?php echo $_POST['report_type']; ?>';
	var mainBtnArr = new Array();
	if (showBtn == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
	}
	if (report_type == 'Package') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_package_pdf();");
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
		
		if(document.getElementById('report_type').value=="Recall letter"){
			if(document.getElementById('recallTemplatesListId').value==""){
				top.show_loading_image("hide");	
				top.fAlert("Please select the Template");
				return false
			}
		}
		if(document.getElementById('report_type').value=="Package"){
			if(document.getElementById('packageListId')){
				if(document.getElementById('packageListId').value==""){
					top.show_loading_image("hide");	
					top.fAlert("Please select the Template");
					return false
				}
			}
		}
		document.sch_report_form.submit();
	}
	
	function generate_package_pdf(){
		var package_category_id = patient_id_comma = '';
		if(document.getElementById("package_category_id")) {
			package_category_id = document.getElementById("package_category_id").value;
			patient_id_comma = document.getElementById("patient_id_comma").value;
		}
		
		if(package_category_id){
			if(patient_id_comma==''){
				top.fAlert('Please select patient(s) to print package');	
			}else {
				window.open('<?php echo $GLOBALS['webroot']; ?>/interface/patient_info/consent_forms/print_package_schedule_report_new.php?op=p&onePage=false&package_category_id='+package_category_id+'&patient_id_comma='+patient_id_comma,'print_pdf','menubar=0,resizable=yes');
			}
		}else{
			window.open('<?php echo $GLOBALS['webroot']; ?>/library/html_to_pdf/createPdf.php?op=p&onePage=false&file_location='+file_name,'pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
		}
	}
	
	$(function() {
		$("#reff_physician").click(function(){window.open('refPhy.php?name_field=reff_physician&id_field=id_reff_physician','RefPhy','height=400, width=400, top=200, left=300, location=no, toolbar=0, menubar=0, status=0');});	
		$("#reff_physician").blur(function(){checkVal('reff_physician');});
	});
	
	function checkVal(ctrlId){
		if(document.getElementById(ctrlId).value==''){
			$('#id_reff_physician').val(''); 
		}
	}
	
	function chk_temp(val){
		document.getElementById("spanTemp1Id").style.display='block';
		document.getElementById("spanTemp2Id").style.display='none';
		if(val=="Recall letter") {
			$('#recallTemplatesListId').prop('disabled', false);
		}else if(val=="Package") {
			document.getElementById("spanTemp1Id").style.display='none';
			document.getElementById("spanTemp2Id").style.display='block';
			//document.getElementById("packageListId").value='';
		}else{
			document.getElementById("recallTemplatesListId").value='';
			$('#recallTemplatesListId').prop('disabled', true);
			document.getElementById("recallTemplatesListId").disabled=true;
		}
		$('.selectpicker').selectpicker('refresh');
	}
	
	function chkPtIdComma() {
		var boxFirstChk = chbxVal = '';
		var objText 	= document.getElementById("patient_id_comma");
		var obj 		= document.getElementsByName("chbxPackage[]");
		var objLength 	= document.getElementsByName("chbxPackage[]").length;
		for(i=0; i<objLength; i++){
			if(obj[i].checked == true){
				if(boxFirstChk!='yes') {
					chbxVal		= obj[i].value;
					boxFirstChk	= 'yes';
				}else {
					chbxVal    += ','+obj[i].value;	
				}
			}
		}
		if(objText) {
			objText.value = chbxVal;	
		}
					
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
		set_container_height();
		chk_temp($("#report_type").val());
		<?php if(trim($strHTML) != ""){	?>
			document.frmPDF.submit();
			top.show_loading_image('hide');
		<?php }	?>
	});

	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
	} 

	$(window).load(function(){
		set_container_height();
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(file_location!=''){
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