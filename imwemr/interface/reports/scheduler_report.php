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
	$selArrDispFields= array_combine($_REQUEST['display_fields'],$_REQUEST['display_fields']);
	$Dxcode10 = array_combine($Dxcode10, $Dxcode10);
	$strInsType= join(',',$_REQUEST['ins_type']);
	$strDxCodes= join(',',$_REQUEST['Dxcode']);
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
	}
}

//----- GET Dx CODES ----
$dxCOde_qry = "select DISTINCT dx_code,diagnosis_id,diag_description from diagnosis_code_tbl order by diag_description asc";
$dxCOde_rs = imw_query($dxCOde_qry);
$dx_opts_arr = array();
$dx_options = "";
while ($dxDetails = imw_fetch_array($dxCOde_rs)) {
    $dxId = $dxDetails['dx_code'];
    $dx_opts_arr[$dxId] = substr($dxDetails['diag_description'], 0, 50);
    $dx_options .= '<option value="' . $dxId . '" >' . $dxDetails['diag_description'] . '</option>';
}

//SET ICD10 CODE DROP DOWN
$arrICD10Code = $CLSReports->getICD10Codes();
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dx10Literals => $dx10code) {
	$dx10Literals = str_replace("'", "", $dx10Literals);
	$sel = ($Dxcode10[$dx10Literals]) ? 'selected' : '';
    $all_dx10_code_options.= "<option value='".$dx10Literals."' ".$sel.">" . $dx10code . "</option>";
}
$allDXCount10 = sizeof($arrICD10Code);

//--- GET ALL OPERATORS DETAILS ----
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');

//CANCEL REASONS
$cancel_reasons_opts ='';
$arrAllCancelReasons=array();
$qry = "SELECT reason_name FROM reason_list WHERE reason_name!='' ORDER BY reason_name";
$rs = imw_query($qry);
while($res=imw_fetch_assoc($rs)){
    $sel= ($res['reason_name']==$cancel_reason) ? 'SELECTED': '';
    $cancel_reasons_opts .= "<option value=\"".$res["reason_name"]."\" ".$sel." >".$res["reason_name"]."</option>";

    if($res['reason_name']!='Other' && $res['reason_name']!=''){
        $arrAllCancelReasons[$res['reason_name']]=$res['reason_name'];
    }
}

// Template field value form database
$temp_id = $_REQUEST['sch_temp_id'];
$dbtemp_name = "Scheduler Report"; 
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
            <div class=" container-fluid">
                <div class="anatreport">
					<div id="common_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria" >
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label>Provider</label>
                                                <select name="phyId[]" id="phyId" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['physician']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Facility</label>
                                                <select name="facility_name[]" id="facility_name" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['facility']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $facilityName; ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label>Operator</label>
                                                <select name="operator_id[]" id="operator_id" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['operators']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $operatorOption; ?>
                                                </select>
                                            </div>
											<div class="col-sm-8">
                                                <label>Period</label>
												<?php if($dbtemp_name == "Day Reports") {?>	
													<div class="input-group">
														<input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
														<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
													</div>
												<?php } else {?>	
													
                                                <div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker" <?php echo (!isset($filter_arr['date_range']))?'disabled':''; ?> data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
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
												<?php } ?>	
                                            </div>
											<div class="col-sm-4">
                                                <label>Day</label>
                                                <select name="day" id="day" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['day']))?'disabled':''; ?> data-width="100%" data-size="10"  >
                                                    <option value="morning" <?php if ($_POST['day'] == 'morning') echo 'SELECTED'; ?>>Morning</option>
                                                    <option value="afternoon" <?php if ($_POST['day'] == 'afternoon') echo 'SELECTED'; ?>>Afternoon</option>
                                                    <option value="full" <?php if ($_POST['day'] == 'full' || $_POST['day'] == '') echo 'SELECTED'; ?>>Full</option>
                                                </select>
                                            </div>                                                                                     
                                        </div>
                                    </div>
                                </div>
								<div class="appointflt">
                                    <div class="anatreport"><h2>Appointment Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
											<div class="col-sm-4">
                                                <label>Procedures</label>
                                                <select name="procedures[]" id="procedures" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['procedures']))?'disabled':''; ?> class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $proc_options; ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label>Appt. Status</label>
                                                <select name="ap_status[]" id="ap_status" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['appt_status']))?'disabled':''; ?> onchange="javascript: display_cancel_reasons();" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <option value="0">New Appointment</option>
                                                    <?php echo $apptOption; ?>
                                                </select>
                                            </div>
                                            
											<div class="col-sm-4">
                                                <label>ICD10</label>
                                                <select name="Dxcode10[]" id="Dxcode10" data-container="#common_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['icd10_codes']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $all_dx10_code_options; ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <label>Ins. Type</label>
                                                <select class="selectpicker" name="ins_type[]" id="ins_type" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['ins_types']))?'disabled':''; ?> data-width="100%"  multiple data-actions-box="true" data-title="Select All">
													<option value="primary" <?php if (in_array('primary', $_POST['ins_type'])) echo 'selected="selected"'; ?>>Primary</option>
													<option value="secondary" <?php if (in_array('secondary', $_POST['ins_type'])) echo 'selected="selected"'; ?>>Secondary</option>
													<option value="tertiary" <?php if (in_array('tertiary', $_POST['ins_type'])) echo 'selected="selected"'; ?>>Tertiary</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Ins. Carrier</label>
                                                <select name="insId[]" id="insId" class="selectpicker" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['ins_carriers']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $sel_ins_comp_options; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <label for="aging_from">Age </label>
                                                <input type="text" name="aging_from" id="aging_from" <?php echo ($temp_id && !isset($filter_arr['age_search']))?'disabled':''; ?> placeholder="From" value="<?php echo $_REQUEST['aging_from'];?>" class="form-control" />
                                            </div>
                                            <div class="col-sm-2">
                                                <label for="aging_to"></label>
                                                <input type="text" name="aging_to" id="aging_to" <?php echo ($temp_id && !isset($filter_arr['age_search']))?'disabled':''; ?> placeholder="To" value="<?php echo $_REQUEST['aging_to'];?>" class="form-control" />
                                            </div>
                                            
                                            <?php if($dbtemp_name != "Recalls" && $dbtemp_name != "Day Reports" && $dbtemp_name != "Day Appointments" && $dbtemp_name != "Appointments on a Certain Day"){ ?>
                                            <div id="div_cancel_reason" class="col-sm-12" style="display:none">
                                                <label>Cancellation Reason</label>
                                                <select name="cancel_reason" id="cancel_reason" class="selectpicker" data-width="100%" data-size="10"  >
                                                    <option value="">Select Reason</option>
                                                    <?php echo $cancel_reasons_opts;?>
                                                </select>
                                            </div>
                                            <?php } ?>

                                            <div class="clearfix"></div>
                                            
                                            <div class="col-sm-4">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input style="cursor:pointer; margin-left:2px" <?php echo ($temp_id && !isset($filter_arr['registered_fac']))?'disabled':''; ?> type="checkbox" name="registered_fac" id="registered_fac" value="1" <?php if ($_POST['registered_fac'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="registered_fac">Reg. Facility</label>
                                                </label>
                                            </div>	

                                            <div class="col-sm-3 revoption">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input style="cursor:pointer; margin-left:2px" <?php echo ($temp_id && !isset($filter_arr['date_made']))?'disabled':''; ?> type="checkbox" name="date_made" id="date_made" value="1" <?php if ($_POST['date_made'] == '1') echo 'CHECKED'; ?> onClick="selectApptInfo();"/>
                                                    <label for="date_made">Date Made</label>
                                                </label>
                                            </div>	

                                            <div class="col-sm-2">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input style="cursor:pointer; margin-left:2px" <?php echo ($temp_id && !isset($filter_arr['rte']))?'disabled':''; ?> type="checkbox" name="rte" id="rte" value="1" <?php if ($_POST['rte'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="rte">RTE&nbsp;</label>
                                                </label>
                                            </div>	
                                            <div class="col-sm-3 revoption">
                                                <label class="checkbox checkbox-inline pointer pull-right">
                                                    <input style="cursor:pointer; margin-left:2px" <?php echo ($temp_id && !isset($filter_arr['pre_auth']))?'disabled':''; ?> type="checkbox" name="pre_auth" id="pre_auth" value="1" <?php if ($_POST['pre_auth'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="pre_auth">Pre-Auth</label>
                                                </label>
                                            </div>
                                            <?php if($dbtemp_name=="Day Appointments"){?>	
                                            <div class="col-sm-12">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input style="cursor:pointer; margin-left:2px" type="checkbox" name="sort_patient_last_name" id="sort_patient_last_name" value="1" <?php if ($_POST['sort_patient_last_name'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="sort_patient_last_name">Sort by Last Name</label>
                                                </label>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

								<div class="grpara">
                                    <div class="anatreport"><h2>Group BY</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_groups" <?php echo ($temp_id && !isset($filter_arr['grpby_groups']))?'disabled':''; ?> value="grpby_groups" <?php if ($_POST['grpby_block'] == 'grpby_groups') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_groups">Groups</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_facility" <?php echo ($temp_id && !isset($filter_arr['grpby_facility']))?'disabled':''; ?> value="grpby_facility" <?php if ($_POST['grpby_block'] == 'grpby_facility') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_facility">Facility</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_physician" <?php echo ($temp_id && !isset($filter_arr['grpby_physician']))?'disabled':''; ?> value="grpby_physician" <?php if ($_POST['grpby_block'] == 'grpby_physician') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_physician">Physician</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-3">	
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_operators" <?php echo ($temp_id && !isset($filter_arr['grpby_operators']))?'disabled':''; ?> value="grpby_operators" <?php if ($_POST['grpby_block'] == 'grpby_operators') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_operators">Operators</label>
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
                                          	<div class="col-sm-4">
                                            	<div class="checkbox checkbox-inline pointer">
                                                	<input type="checkbox" name="inc_appt_detail" id="inc_appt_detail" <?php echo ($temp_id && !isset($filter_arr['inc_appt_detail']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt_detail'] == '1') echo 'CHECKED'; ?>/> 
                                                	<label for="inc_appt_detail">Appt Detail</label>
                                            	</div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_appt_status" id="inc_appt_status" <?php echo ($temp_id && !isset($filter_arr['inc_appt_status']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt_status'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_appt_status">Appt Status</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_demographics" id="inc_demographics" <?php echo ($temp_id && !isset($filter_arr['inc_demographics']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_demographics'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_demographics">Demographics</label>
                                                </div>
                                            </div>  

                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_insurance" id="inc_insurance" <?php echo ($temp_id && !isset($filter_arr['inc_insurance']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_insurance'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_insurance">Insurance</label>
                                                </div>
                                            </div>  

                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_portal_key" id="inc_portal_key" <?php echo ($temp_id && !isset($filter_arr['inc_portal_key']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_portal_key'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_portal_key">Portal Key</label>
                                                </div>
                                            </div>  
											<div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_recalls" id="inc_recalls" <?php echo ($temp_id && !isset($filter_arr['inc_recalls']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_recalls'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_recalls">Recalls</label>
                                                </div>
                                            </div>
											<?php if($dbtemp_name == "Day Appointments") {?>	
											<div class="col-sm-4">
												<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="inc_pt_dob" id="inc_pt_dob" value="1" <?php if ($_POST['inc_pt_dob'] == '1') echo 'CHECKED'; ?>/> 
													<label for="inc_pt_dob">Patient DOB</label>
												</div>
											</div>
											<?php } ?>		
											<div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_pt_documents" id="inc_pt_documents" <?php echo ($temp_id && !isset($filter_arr['inc_pt_documents']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_pt_documents'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_pt_documents">Pt. Documents</label>
                                                </div>
                                            </div>
											<div class="col-sm-8">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_appt_made" id="inc_appt_made" <?php echo ($temp_id && !isset($filter_arr['inc_appt_made']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt_made'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_appt_made">Appt. Made Info</label>
                                                </div>
                                            </div>
											<div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_pcp" id="inc_pcp" <?php echo ($temp_id && !isset($filter_arr['inc_pcp']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_pcp'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_pcp">PCP</label>
                                                </div>
                                            </div>
											<div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_ref_phy" id="inc_ref_phy" <?php echo ($temp_id && !isset($filter_arr['inc_ref_phy']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_ref_phy'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_ref_phy">Referral Dr.</label>
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
                                                    <input type="radio" name="output_option" id="output_view_only" <?php echo ($temp_id && !isset($filter_arr['output_view_only']))?'disabled':''; ?> value="output_view_only" <?php if ($_POST['output_option'] == 'output_view_only') echo 'CHECKED'; ?>/> 
                                                    <label for="output_view_only">View Only</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_pdf" <?php echo ($temp_id && !isset($filter_arr['output_pdf']))?'disabled':''; ?> value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/> 
                                                    <label for="output_pdf">PDF</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer" style="padding-left: 42%;">
                                                    <input type="radio" name="output_option" id="output_csv" <?php echo ($temp_id && !isset($filter_arr['output_csv']))?'disabled':''; ?> value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/> 
                                                    <label for="output_csv">CSV</label>
                                                </div>
                                            </div>  

                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_consult_letters" <?php echo ($temp_id && !isset($filter_arr['output_consult_letters']))?'disabled':''; ?> value="output_consult_letters" <?php if ($_POST['output_option'] == 'output_consult_letters') echo 'CHECKED'; ?>/> 
                                                    <label for="output_consult_letters">Consult Letters</label>
                                                </div>
                                            </div>  
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_sx_planning_sheet" <?php echo ($temp_id && !isset($filter_arr['output_sx_planning_sheet']))?'disabled':''; ?> value="output_sx_planning_sheet" <?php if ($_POST['output_option'] == 'output_sx_planning_sheet') echo 'CHECKED'; ?>/> 
                                                    <label for="output_sx_planning_sheet" class="text-nowrap">SX Planning Sheet</label>
                                                </div>
                                            </div>
											 <div class="col-sm-4">
                                                <div class="radio radio-inline pointer pull-right">
                                                    <input type="radio" name="output_option" id="output_face_sheet" <?php echo ($temp_id && !isset($filter_arr['output_face_sheet']))?'disabled':''; ?> value="output_face_sheet" <?php if ($_POST['output_option'] == 'output_face_sheet') echo 'CHECKED'; ?>/> 
                                                    <label for="output_face_sheet">Face Sheet</label>
                                                </div>
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
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span></img>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
										if($_POST['form_submitted'] && $dbtemp_name == "Recalls") {
											include('recall_report_result.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Day Reports"){
											include('day_report_print_pdf.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Day Appointments"){
											include('day_appointment_report_print.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Appointments on a Certain Day"){
											include('appointments_on_certain_day.php');											
										}elseif($_POST['form_submitted']){
                                            include('scheduler_new_report_result.php');
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
	$viewbtnCheck = '';
	if ($_POST['output_option'] == 'output_view_only'){
		$viewbtnCheck = 1;
	}
	$pdfbtnCheck = '';
	if ($_POST['output_option'] == 'output_pdf'){
		$pdfbtnCheck = 1;
	}
	$csvbtnCheck = '';
	if ($_POST['output_option'] == 'output_csv'){
		$csvbtnCheck = 1;
	}
	$clbtnCheck = '';
	if ($_POST['output_option'] == 'output_consult_letters'){
		$clbtnCheck = 1;
	}
	$sxbtnCheck = '';
	if ($_POST['output_option'] == 'output_sx_planning_sheet'){
		$sxbtnCheck = 1;
	}
	$facesheetbtnCheck = '';
	if ($_POST['output_option'] == 'output_face_sheet'){
		$facesheetbtnCheck = 1;
	}
	if($showbtn == 1){
		$pdfbtnCheck = 1;
		$csvbtnCheck = 1;
		$pdfbtnCheck = 1;
		$csvBtn = 1;
	}
?>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var pdfbtnCheck = '<?php echo $pdfbtnCheck; ?>';
	var printPdFBtn = '<?php echo $printPdFBtn; ?>';
	var csvbtnCheck = '<?php echo $csvbtnCheck; ?>';
	var csvBtn = '<?php echo $csvBtn; ?>';
	var date_made='<?php echo $filter_arr['date_made'] ;?>';
	var inc_appt_made='<?php echo $filter_arr['inc_appt_made'] ;?>';
	var inc_appt_made_posted='<?php echo $_POST['inc_appt_made'];?>';
	var reportName='<?php echo $dbtemp_name; ?>';
	
	var mainBtnArr = new Array();
	if (pdfbtnCheck == '1' && printPdFBtn == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
	}
	if (csvBtn == '1' && csvbtnCheck == '1') {
		if(reportName=='Day Appointments' || reportName=='Day Reports'){
			mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
		}else{
			mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
		}
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

	
	function selectApptInfo(){
	   if(inc_appt_made=='1'){
		   if($('#date_made').is(":checked")){
				$('#inc_appt_made').prop('checked', true);
				$('#inc_appt_made').attr('disabled', true);
		   }else{
			   $('#inc_appt_made').prop('checked', false);
			   $('#inc_appt_made').attr('disabled', false);
		   }
	   }
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

    function display_cancel_reasons(){
        var selectText = $( "#ap_status option:selected" ).text();
        
        if(selectText.indexOf('cancelled')>-1){
            $('#div_cancel_reason').css('display','block');
        }else{
            $('#div_cancel_reason').css('display','none');
            $('#cancel_reason').val('');
        }
    }

	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
		if(inc_appt_made_posted!='1'){
			if(inc_appt_made=='1' && date_made=='1'){
				selectApptInfo();
			}
		}

        display_cancel_reasons();

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